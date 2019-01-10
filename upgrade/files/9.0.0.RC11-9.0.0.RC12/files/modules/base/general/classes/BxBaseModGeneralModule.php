<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    BaseGeneral Base classes for modules
 * @ingroup     UnaModules
 *
 * @{
 */

bx_import('BxDolAcl');

/**
 * Base module class.
 */
class BxBaseModGeneralModule extends BxDolModule
{
    protected $_iProfileId;
    protected $_aSearchableNamesExcept;
    protected $_aFormParams;

    function __construct(&$aModule)
    {
        parent::__construct($aModule);

        $this->_iProfileId = bx_get_logged_profile_id();
        $this->_aSearchableNamesExcept = array(
            'allow_view_to'
        );

        $this->_aFormParams = array(
            'display' => false, 
            'dynamic_mode' => false, 
            'ajax_mode' => false, 
            'absolute_action_url' => false,
            'visibility_autoselect' => false,
            'context_id' => 0,
        );
    }

    // ====== ACTIONS METHODS

    public function actionRss ()
    {
        $aArgs = func_get_args();
        $this->_rss($aArgs);
    }
    
    public function actionGetCreatePostForm()
    {
    	$aParams = bx_process_input(array_intersect_key($_GET, $this->_aFormParams));
    	$aParams = array_merge($this->_aFormParams, $aParams);

    	$sForm = $this->serviceGetCreatePostForm($aParams);
    	if(empty($sForm))
    		return echoJson(array());

	   	return echoJson(array(
    		'module' => $this->_oConfig->getName(),
    		'content' => $sForm
    	));
    }

    // ====== SERVICE METHODS
    public function serviceGetCreatePostForm($aParams = array())
    {
    	$aParams = array_merge($this->_aFormParams, $aParams);

    	$oForm = $this->serviceGetObjectForm('add', $aParams);
    	if(!$oForm)
    		return ''; 	

    	return $this->serviceEntityCreate($aParams);
    }

    public function serviceGetAuthor ($iContentId)
    {
        $mixedResult = $this->_getFieldValue('FIELD_AUTHOR', $iContentId);
        return $mixedResult !== false ? (int)$mixedResult : 0; 
    }

    public function serviceGetDateAdded ($iContentId)
    {
        $mixedResult = $this->_getFieldValue('FIELD_ADDED', $iContentId);
        return $mixedResult !== false ? (int)$mixedResult : 0; 
    }

    public function serviceGetDateChanged ($iContentId)
    {
        $mixedResult = $this->_getFieldValue('FIELD_CHANGED', $iContentId);
        return $mixedResult !== false ? (int)$mixedResult : 0;
    }

    public function serviceGetLink ($iContentId)
    {
        $CNF = &$this->_oConfig->CNF;
        if(empty($CNF['URI_VIEW_ENTRY']))
            return '';

        $aContentInfo = $this->_oDb->getContentInfoById($iContentId);
        if(empty($aContentInfo))
            return '';

        return BX_DOL_URL_ROOT . BxDolPermalinks::getInstance()->permalink('page.php?i=' . $CNF['URI_VIEW_ENTRY'] . '&id=' . $aContentInfo[$CNF['FIELD_ID']]);
    }

    public function serviceGetTitle ($iContentId)
    {
        $mixedResult = $this->_getFieldValue('FIELD_TITLE', $iContentId);
        return $mixedResult !== false ? $mixedResult : '';
    }

    public function serviceGetText ($iContentId)
    {
        $mixedResult = $this->_getFieldValue('FIELD_TEXT', $iContentId);
        if (false === $mixedResult)
            return '';

        $CNF = &$this->_oConfig->CNF;
        if (!empty($CNF['OBJECT_METATAGS']) && is_string($mixedResult)) {
            $oMetatags = BxDolMetatags::getObjectInstance($CNF['OBJECT_METATAGS']);
            $mixedResult = $oMetatags->metaParse($iContentId, $mixedResult);
        }

        return $mixedResult;
    }

    public function serviceGetInfo ($iContentId, $bSearchableFieldsOnly = true)
    {
        $CNF = &$this->_oConfig->CNF;

        $aContentInfo = $this->_getFields($iContentId);
        if(empty($aContentInfo))
            return array();

        if(!$bSearchableFieldsOnly)
            return $aContentInfo;

        if(empty($CNF['PARAM_SEARCHABLE_FIELDS']))
            return array();

        $aFields = explode(',', getParam($CNF['PARAM_SEARCHABLE_FIELDS']));
        if(empty($aFields))
            return array();

        $aResult = array();
        foreach($aFields as $sField)
            if(isset($aContentInfo[$sField]))
                $aResult[$sField] = $aContentInfo[$sField];

        return $aResult;
    }

    public function serviceGetSearchResultUnit ($iContentId, $sUnitTemplate = '')
    {
        $aContentInfo = $this->_oDb->getContentInfoById($iContentId);
        if(empty($aContentInfo))
            return '';

        if(empty($sUnitTemplate))
            $sUnitTemplate = 'unit.html';

        return $this->_oTemplate->unit($aContentInfo, true, $sUnitTemplate);
    }

    public function serviceGetAll ($aParams = array())
    {
        if(empty($aParams) || !is_array($aParams))
            $aParams = array('type' => 'all');

        return $this->_oDb->getEntriesBy($aParams);
    }

    public function serviceGetAllByAuthor ($iProfileId)
    {
        return $this->_oDb->getEntriesByAuthor((int)$iProfileId);
    }

    public function serviceGetSearchableFieldsExtended()
    {
        $CNF = &$this->_oConfig->CNF;
        if(empty($CNF['OBJECT_FORM_ENTRY']))
            return array();

        $aResult = array();
        if(!empty($CNF['FIELD_AUTHOR']) && !in_array($CNF['FIELD_AUTHOR'], $this->_aSearchableNamesExcept))
            $aResult[$CNF['FIELD_AUTHOR']] = array(
                'type' => 'text_auto', 
                'caption' => $CNF['T']['form_field_author'],
                'info' => '',
            	'value' => '',
                'values' => '',
                'pass' => ''
            );

        $aInputs = array();
        if(!empty($CNF['OBJECT_FORM_ENTRY_DISPLAY_ADD'])) {
            $oForm = BxDolForm::getObjectInstance($CNF['OBJECT_FORM_ENTRY'], $CNF['OBJECT_FORM_ENTRY_DISPLAY_ADD'], $this->_oTemplate);

            $aInputs = array_merge($aInputs, $oForm->aInputs);
        }
        if(!empty($CNF['OBJECT_FORM_ENTRY_DISPLAY_EDIT'])) {
            $oForm = BxDolForm::getObjectInstance($CNF['OBJECT_FORM_ENTRY'], $CNF['OBJECT_FORM_ENTRY_DISPLAY_EDIT'], $this->_oTemplate);

            $aInputs = array_merge($aInputs, $oForm->aInputs);
        }

        foreach($aInputs as $aInput)
            if(in_array($aInput['type'], BxDolSearchExtended::$SEARCHABLE_TYPES) && !in_array($aInput['name'], $this->_aSearchableNamesExcept))
                $aResult[$aInput['name']] = array(
                    'type' => $aInput['type'], 
                    'caption_system' => $aInput['caption_system_src'],
                    'caption' => $aInput['caption_src'],
                    'info' => $aInput['info_src'],
                    'value' => !empty($aInput['value']) ? $aInput['value'] : '',
                    'values' => !empty($aInput['values_src']) ? $aInput['values_src'] : '',
                    'pass' => !empty($aInput['db']['pass']) ? $aInput['db']['pass'] : '',
                );

        return $aResult;
    }

    public function serviceGetSearchResultExtended($aParams, $iStart = 0, $iPerPage = 0)
    {
        if(empty($aParams) || !is_array($aParams))
            return array();

        return $this->_oDb->getEntriesBy(array('type' => 'search_ids', 'search_params' => $aParams, 'start' => $iStart, 'per_page' => $iPerPage));
    }

    public function serviceModuleIcon ()
    {
        return isset($this->_oConfig->CNF['ICON']) ? $this->_oConfig->CNF['ICON'] : '';
    }

    public function serviceGetSearchableFields ()
    {
        $CNF = $this->_oConfig->CNF;

        if (!isset($CNF['PARAM_SEARCHABLE_FIELDS']) || !isset($CNF['OBJECT_FORM_ENTRY']) || !isset($CNF['OBJECT_FORM_ENTRY_DISPLAY_ADD']))
            return array();

        $aTextTypes = array('text', 'textarea');
        $aTextFields = array();
        $oForm = BxDolForm::getObjectInstance($CNF['OBJECT_FORM_ENTRY'], $CNF['OBJECT_FORM_ENTRY_DISPLAY_ADD'], $this->_oTemplate);
        foreach ($oForm->aInputs as $r) {
            if (in_array($r['type'], $aTextTypes))
                $aTextFields[$r['name']] = $r['caption'];
        }
        return $aTextFields;
    }
    
	public function serviceManageTools($sType = 'common')
    {
        $oGrid = BxDolGrid::getObjectInstance($this->_oConfig->getGridObject($sType));
        if(!$oGrid)
            return '';

		$CNF = &$this->_oConfig->CNF;

		$sMenu = '';
		if(BxDolAcl::getInstance()->isMemberLevelInSet(192)) {
			$oPermalink = BxDolPermalinks::getInstance();

			$aMenuItems = array();
			if(!empty($CNF['OBJECT_GRID_COMMON']) && !empty($CNF['T']['menu_item_manage_my']))
				$aMenuItems[] = array('id' => 'manage-common', 'name' => 'manage-common', 'class' => '', 'link' => $oPermalink->permalink($CNF['URL_MANAGE_COMMON']), 'target' => '_self', 'title' => _t($CNF['T']['menu_item_manage_my']), 'active' => 1);
			if(!empty($CNF['OBJECT_GRID_ADMINISTRATION']) && !empty($CNF['T']['menu_item_manage_all']))
				$aMenuItems[] = array('id' => 'manage-administration', 'name' => 'manage-administration', 'class' => '', 'link' => $oPermalink->permalink($CNF['URL_MANAGE_ADMINISTRATION']), 'target' => '_self', 'title' => _t($CNF['T']['menu_item_manage_all']), 'active' => 1);

			if(count($aMenuItems) > 1) {
	            $oMenu = new BxTemplMenu(array(
	            	'template' => 'menu_vertical.html', 
	            	'menu_items' => $aMenuItems
	            ), $this->_oTemplate);
	            $oMenu->setSelected($this->_aModule['name'], 'manage-' . $sType);
	            $sMenu = $oMenu->getCode();
			}
		}

		if(!empty($CNF['OBJECT_MENU_SUBMENU']) && isset($CNF['URI_MANAGE_COMMON'])) {
			BxDolMenu::getObjectInstance($CNF['OBJECT_MENU_SUBMENU'])->setSelected($this->_aModule['name'], $CNF['URI_MANAGE_COMMON']);
		}

        $this->_oTemplate->addCss(array('manage_tools.css'));
        $this->_oTemplate->addJs(array('manage_tools.js'));
        $this->_oTemplate->addJsTranslation(array('_sys_grid_search'));
        return array(
        	'content' => $this->_oTemplate->getJsCode('manage_tools', array('sObjNameGrid' => $this->_oConfig->getGridObject($sType))) . $oGrid->getCode(),
        	'menu' => $sMenu
        );
    }

    public function serviceGetMenuAddonManageTools()
    {
    	return 0;
    }
    
    public function serviceGetMenuAddonManageToolsProfileStats()
    {
    	return 0;
    }

    /**
     * My entries actions block
     */
    public function serviceMyEntriesActions ($iProfileId = 0)
    {
        $CNF = &$this->_oConfig->CNF;
        if(empty($CNF['OBJECT_MENU_ACTIONS_MY_ENTRIES']))
            return false;

        if (!$iProfileId)
            $iProfileId = bx_process_input(bx_get('profile_id'), BX_DATA_INT);
        if (!$iProfileId || !($oProfile = BxDolProfile::getInstance($iProfileId)))
            return false;

        if ($iProfileId != $this->_iProfileId)
            return false;

        $oMenu = BxTemplMenu::getObjectInstance($CNF['OBJECT_MENU_ACTIONS_MY_ENTRIES']);
        return $oMenu ? $oMenu->getCode() : false;
    }

    /**
     * Universal browse method
     * @param $aParams custom browse params, possible params are the following:
     *  - mode - browse mode, such as 'recent', 'featured', etc
     *  - params - custom params to browse method, for example 'unit_view' can be passed here
     *  - design_box - design box style, @see BxBaseFunctions::DesignBoxContent 
     *  - empty_message - display or not "empty" message when there is no content
     *  - ajax_paginate - use AJAX paginate or not
     *  @return HTML string
     */
    public function serviceBrowse ($aParams = array())
    {
        $aDefaults = array (
            'mode' => 'recent',
            'params' => false,
            'design_box' => BX_DB_PADDING_DEF,
            'empty_message' => false,
            'ajax_paginate' => true,
        );
        $aParams = array_merge($aDefaults, $aParams);
        return $this->_serviceBrowse ($aParams['mode'], $aParams['params'], $aParams['design_box'], $aParams['empty_message'], $aParams['ajax_paginate']);
    }
    
	/**
     * Display featured entries
     * @return HTML string
     */
    public function serviceBrowseFeatured ($sUnitView = false, $bEmptyMessage = false, $bAjaxPaginate = true)
    {
        return $this->_serviceBrowse ('featured', $sUnitView ? array('unit_view' => $sUnitView) : false, BX_DB_PADDING_DEF, $bEmptyMessage, $bAjaxPaginate);
    }
	
	/**
     * Display entries favored by a member
     * @return HTML string
     */
    public function serviceBrowseFavorite ($iProfileId = 0, $aParams = array())
    {
        $oProfile = null;
        if((int)$iProfileId)
            $oProfile = BxDolProfile::getInstance($iProfileId);
        if(!$oProfile && bx_get('profile_id') !== false)
            $oProfile = BxDolProfile:: getInstance(bx_process_input(bx_get('profile_id'), BX_DATA_INT));
        if(!$oProfile)
            $oProfile = BxDolProfile::getInstance();
        if(!$oProfile)
            return '';

        $bEmptyMessage = false;
        if(isset($aParams['empty_message'])) {
            $bEmptyMessage = (bool)$aParams['empty_message'];
            unset($aParams['empty_message']);
        }

        return $this->_serviceBrowse ('favorite', array_merge(array('user' => $oProfile->id()), $aParams), BX_DB_PADDING_DEF, $bEmptyMessage);
    }

    public function serviceFormsHelper ()
    {
        bx_import('FormsEntryHelper', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormsEntryHelper';
        return new $sClass($this);
    }

	/**
     * Add entry using provided fields' values.
     * @return array with result: 'code' is 0 on success or non-zero on error, 'message' is error message in case of error, 'content' is content info array in case of success
     */
    public function serviceEntityAdd ($iProfile, $aValues)
    {
        bx_import('FormsEntryHelper', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormsEntryHelper';
        $oFormsHelper = new $sClass($this);
        return $oFormsHelper->addData($iProfile, $aValues);
    }

	/**
     * Perform redirect after content creation
     * @return nothing, rediret header is sent
     */    
    public function serviceRedirectAfterAdd($aContentInfo)
    {
        bx_import('FormsEntryHelper', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormsEntryHelper';
        $oFormsHelper = new $sClass($this);
        $oFormsHelper->redirectAfterAdd($aContentInfo);
    }

    /**
     * Get form object for add, edit, view or delete the content 
     * @param $sType 'add', 'edit', 'view' or 'delete'
     * @param $aParams optional array with parameters(display name, etc)
     * @return form object or false on error
     */
    public function serviceGetObjectForm ($sType, $aParams = array())
    {
        if (!in_array($sType, array('add', 'edit', 'view', 'delete')))
            return false;

		$CNF = &$this->_oConfig->CNF;

        bx_import('FormsEntryHelper', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormsEntryHelper';
        $oFormsHelper = new $sClass($this);

        $sDisplay = !empty($aParams['display']) ? $aParams['display'] : false;

        $sFunc = 'getObjectForm' . ucfirst($sType);
        $oForm = $oFormsHelper->$sFunc($sDisplay);

        $sParamsKey = 'absolute_action_url';
        if(isset($aParams[$sParamsKey]) && (bool)$aParams[$sParamsKey] === true) {
        	$sKeyUri = 'URI_' . strtoupper($sType) . '_ENTRY';
        	if(!empty($this->_oConfig->CNF[$sKeyUri]))
        		$oForm->aFormAttrs['action'] = BX_DOL_URL_ROOT . BxDolPermalinks::getInstance()->permalink('page.php?i=' . $this->_oConfig->CNF[$sKeyUri]);
        }

        $sParamsKey = 'ajax_mode';
        if(isset($aParams[$sParamsKey]) && is_bool($aParams[$sParamsKey]))
        	$oForm->setAjaxMode((bool)$aParams[$sParamsKey]);

		$sKey = 'FIELD_ALLOW_VIEW_TO';
		if(!empty($aParams['context_id']) && !empty($CNF[$sKey]) && !empty($oForm->aInputs[$CNF[$sKey]])) {
			foreach($oForm->aInputs[$CNF[$sKey]]['values'] as $aValue)
				if(isset($aValue['key']) && (int)$aValue['key'] == -(int)$aParams['context_id']) {
					$oForm->aInputs[$CNF[$sKey]]['value'] = -(int)$aParams['context_id'];
					$oForm->aInputs[$CNF[$sKey]]['type'] = 'hidden';
					break;
				}
		}

        return $oForm;
    }

    /**
     * Create entry form
     * @return HTML string
     */
    public function serviceEntityCreate ($sParams = false)
    {
        $bParamsArray = is_array($sParams);

        $sDisplay = is_string($sParams) ? $sParams : false;
        if($bParamsArray && !empty($sParams['display']))
            $sDisplay = $sParams['display'];

        bx_import('FormsEntryHelper', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormsEntryHelper';
        $oFormsHelper = new $sClass($this);
        if($bParamsArray && isset($sParams['dynamic_mode']))
            $oFormsHelper->setDynamicMode($sParams['dynamic_mode']);

        return $oFormsHelper->addDataForm($sDisplay);
    }

    public function serviceEntityEdit ($iContentId = 0, $sDisplay = false)
    {
        return $this->_serviceEntityForm ('editDataForm', $iContentId, $sDisplay);
    }

    public function serviceEntityDelete ($iContentId = 0)
    {
        return $this->_serviceEntityForm ('deleteDataForm', $iContentId);
    }

    public function serviceEntityTextBlock ($iContentId = 0)
    {
        return $this->_serviceEntityForm ('viewDataEntry', $iContentId);
    }

    public function serviceEntityInfo ($iContentId = 0, $sDisplay = false)
    {
        return $this->_serviceEntityForm ('viewDataForm', $iContentId, $sDisplay);
    }

	public function serviceEntityInfoFull ($iContentId = 0)
    {
    	$CNF = &$this->_oConfig->CNF;

    	$sDisplayName = 'OBJECT_FORM_ENTRY_DISPLAY_VIEW_FULL';
        return $this->_serviceEntityForm ('viewDataForm', $iContentId, !empty($CNF[$sDisplayName]) ? $CNF[$sDisplayName] : false);
    }

	public function serviceEntityInfoExtended ($iContentId = 0)
    {
        return $this->_serviceTemplateFunc ('entryInfo', $iContentId);
    }

    /**
     * Entry location info
     */
    public function serviceEntityLocation ($iContentId = 0)
    {
        $iContentId = $this->_getContent($iContentId, false);
        if($iContentId === false)
            return false;

        return $this->_oTemplate->entryLocation ($iContentId);
    }

    /**
     * Entry comments
     */
    public function serviceEntityComments ($iContentId = 0)
    {
        $CNF = &$this->_oConfig->CNF;
        if(empty($CNF['OBJECT_COMMENTS']))
            return '';

        return $this->_entityComments($CNF['OBJECT_COMMENTS'], $iContentId);
    }

    /**
     * Entry attachments block
     */
    public function serviceEntityAttachments ($iContentId = 0)
    {
        return $this->_serviceTemplateFunc ('entryAttachments', $iContentId);
    }
    
    /**
     * Delete content entry
     * @param $iContentId content id 
     * @return error message or empty string on success
     */
    public function serviceDeleteEntity ($iContentId, $sFuncDelete = 'deleteData')
    {
        bx_import('FormsEntryHelper', $this->_aModule);
        $sClass = $this->_oConfig->getClassPrefix() . 'FormsEntryHelper';
        $oFormsHelper = new $sClass($this);
        return $oFormsHelper->$sFuncDelete($iContentId);
    }

    /**
     * Entry actions and social sharing block
     */
    public function serviceEntityAllActions ($mixedContent = false, $aParams = array())
    {
        $CNF = &$this->_oConfig->CNF;

        if(!empty($mixedContent)) {
            if(!is_array($mixedContent))
                $mixedContent = array((int)$mixedContent, (method_exists($this->_oDb, 'getContentInfoById')) ? $this->_oDb->getContentInfoById((int)$mixedContent) : array());
        }
        else {
            $mixedContent = $this->_getContent();
            if($mixedContent === false)
                return false;
        }

        list($iContentId, $aContentInfo) = $mixedContent;

        $sObjectMenu = !empty($aParams['object_menu']) ? $aParams['object_menu'] : '';
        if(empty($sObjectMenu) && !empty($CNF['OBJECT_MENU_ACTIONS_VIEW_ENTRY_ALL']))
            $sObjectMenu = $CNF['OBJECT_MENU_ACTIONS_VIEW_ENTRY_ALL'];

        if(empty($sObjectMenu))
            return false;

        $sEntryTitle = !empty($aParams['entry_title']) ? $aParams['entry_title'] : '';
        if(empty($sEntryTitle) && !empty($CNF['FIELD_TITLE']) && !empty($aContentInfo[$CNF['FIELD_TITLE']]))
            $sEntryTitle = $aContentInfo[$CNF['FIELD_TITLE']];

        $sEntryUrl = !empty($aParams['entry_url']) ? $aParams['entry_url'] : '';
        if(empty($sEntryUrl) && !empty($CNF['URI_VIEW_ENTRY']))
            $sEntryUrl = BX_DOL_URL_ROOT . BxDolPermalinks::getInstance()->permalink('page.php?i=' . $CNF['URI_VIEW_ENTRY'] . '&id=' . $iContentId);

        $iEntryThumb = !empty($aParams['entry_thumb']) ? (int)$aParams['entry_thumb'] : 0;
        if(empty($iEntryThumb) && !empty($CNF['FIELD_THUMB']) && !empty($aContentInfo[$CNF['FIELD_THUMB']]))
            $iEntryThumb = (int)$aContentInfo[$CNF['FIELD_THUMB']];

        $sObjectStorage = !empty($aParams['object_storage']) ? $aParams['object_storage'] : false;
        if(empty($sObjectStorage) && !empty($CNF['OBJECT_STORAGE']))
            $sObjectStorage = $CNF['OBJECT_STORAGE'];

        $sObjectTranscoder = !empty($aParams['object_transcoder']) ? $aParams['object_transcoder'] : false;

        $aMarkers = array(
            'id' => $iContentId,
            'module' => $this->_oConfig->getName(),
            'title' => !empty($sEntryTitle) ? $sEntryTitle : '',
            'url' => !empty($sEntryUrl) ? $sEntryUrl : '',
            'img_url' => ''
        );

        if(!empty($iEntryThumb)) {
            if(!empty($sObjectTranscoder))
                $o = BxDolTranscoder::getObjectInstance($sObjectTranscoder);
            else if(!empty($sObjectStorage))
                $o = BxDolStorage::getObjectInstance($sObjectStorage);

            $sImageUrl = $o ? $o->getFileUrlById($iEntryThumb) : '';
            if(!empty($sImageUrl))
                $aMarkers['img_url'] = $sImageUrl;
        }

        $oActions = BxDolMenu::getObjectInstance($sObjectMenu);
        if(!$oActions)
            return false;

        $oActions->setContentId($iContentId);
        $oActions->addMarkers($aMarkers);
        return $this->_oTemplate->entryAllActions($oActions->getCode());
    }

    /**
     * Entry actions block
     */
    public function serviceEntityActions ($iContentId = 0)
    {
        $iContentId = $this->_getContent($iContentId, false);
        if($iContentId === false)
            return false;

        $oMenu = BxTemplMenu::getObjectInstance($this->_oConfig->CNF['OBJECT_MENU_ACTIONS_VIEW_ENTRY']);
        return $oMenu ? $oMenu->getCode() : false;
    }

    /**
     * Entry social sharing block.
     * @param type $mixedContent integer content ID or array with integer content ID and array with content info, or false.
     * @param type $aParams array with additional custom params which may overwrite some default values.
     * @return boolean|string string with block content or false if something went wrong.
     */
    public function serviceEntitySocialSharing($mixedContent = false, $aParams = array())
    {
        if(!empty($mixedContent)) {
            if(!is_array($mixedContent))
               $mixedContent = array((int)$mixedContent, array());
        }
        else {
            $mixedContent = $this->_getContent();
            if($mixedContent === false)
                return false;
        }

        list($iContentId, $aContentInfo) = $mixedContent;

        $CNF = &$this->_oConfig->CNF;

        $sUri = !empty($aParams['uri']) ? $aParams['uri'] : '';
        if(empty($sUri) && !empty($CNF['URI_VIEW_ENTRY']))
            $sUri = $CNF['URI_VIEW_ENTRY'];

        $sUrl = !empty($sUri) ? BxDolPermalinks::getInstance()->permalink('page.php?i=' . $sUri . '&id=' . $iContentId) : '';

        $sTitle = !empty($aParams['title']) ? $aParams['title'] : '';
        if(empty($sTitle) && !empty($aContentInfo[$CNF['FIELD_TITLE']]))
            $sTitle = $aContentInfo[$CNF['FIELD_TITLE']];

        $aMarkers = array(
            'id' => $iContentId,
            'module' => $this->_aModule['name'],
            'url' => BX_DOL_URL_ROOT . $sUrl,
            'title' => $sTitle,
        );

        $iIdThumb = !empty($aParams['id_thumb']) ? (int)$aParams['id_thumb'] : 0;
        if(empty($iIdThumb) && !empty($CNF['FIELD_THUMB']) && !empty($aContentInfo[$CNF['FIELD_THUMB']]))
            $iIdThumb = (int)$aContentInfo[$CNF['FIELD_THUMB']];

        if ($iIdThumb) {
            $sTranscoder = !empty($aParams['object_transcoder']) ? $aParams['object_transcoder'] : '';
            $sStorage = !empty($aParams['object_storage']) ? $aParams['object_storage'] : '';
            if(empty($sStorage) && !empty($CNF['OBJECT_STORAGE']))
                $sStorage = $CNF['OBJECT_STORAGE'];

            if(!empty($sTranscoder))
                $o = BxDolTranscoder::getObjectInstance($sTranscoder);
            else if(!empty($sStorage))
                $o = BxDolStorage::getObjectInstance($sStorage);

            $sImgUrl = $o ? $o->getFileUrlById($iIdThumb) : '';
            if($sImgUrl)
                $aMarkers['img_url'] = $sImgUrl;
        }

        $oMenu = BxDolMenu::getObjectInstance('sys_social_sharing');
        $oMenu->addMarkers($aMarkers);
        $sMenu = $oMenu->getCode();

        if(empty($sMenu))
            return '';

        return $this->_oTemplate->parseHtmlByName('entry-share.html', array(
            'menu' => $sMenu
        ));
    }

    /**
     * Data for Notifications module
     */
    public function serviceGetNotificationsData()
    {
    	$sModule = $this->_aModule['name'];

    	$sEventPrivacy = $sModule . '_allow_view_event_to';
		if(BxDolPrivacy::getObjectInstance($sEventPrivacy) === false)
			$sEventPrivacy = '';

        return array(
            'handlers' => array(
                array('group' => $sModule . '_object', 'type' => 'insert', 'alert_unit' => $sModule, 'alert_action' => 'added', 'module_name' => $sModule, 'module_method' => 'get_notifications_post', 'module_class' => 'Module', 'module_event_privacy' => $sEventPrivacy),
                array('group' => $sModule . '_object', 'type' => 'update', 'alert_unit' => $sModule, 'alert_action' => 'edited'),
                array('group' => $sModule . '_object', 'type' => 'delete', 'alert_unit' => $sModule, 'alert_action' => 'deleted'),

                array('group' => $sModule . '_comment', 'type' => 'insert', 'alert_unit' => $sModule, 'alert_action' => 'commentPost', 'module_name' => $sModule, 'module_method' => 'get_notifications_comment', 'module_class' => 'Module', 'module_event_privacy' => $sEventPrivacy),
                array('group' => $sModule . '_comment', 'type' => 'delete', 'alert_unit' => $sModule, 'alert_action' => 'commentRemoved'),

                array('group' => $sModule . '_reply', 'type' => 'insert', 'alert_unit' => $sModule, 'alert_action' => 'replyPost', 'module_name' => $sModule, 'module_method' => 'get_notifications_reply', 'module_class' => 'Module', 'module_event_privacy' => $sEventPrivacy),
                array('group' => $sModule . '_reply', 'type' => 'delete', 'alert_unit' => $sModule, 'alert_action' => 'replyRemoved'),

                array('group' => $sModule . '_vote', 'type' => 'insert', 'alert_unit' => $sModule, 'alert_action' => 'doVote', 'module_name' => $sModule, 'module_method' => 'get_notifications_vote', 'module_class' => 'Module', 'module_event_privacy' => $sEventPrivacy),
                array('group' => $sModule . '_vote', 'type' => 'delete', 'alert_unit' => $sModule, 'alert_action' => 'undoVote'),
            ),
            'settings' => array(
                array('group' => 'content', 'unit' => $sModule, 'action' => 'added', 'types' => array('follow_member', 'follow_context')),
                array('group' => 'comment', 'unit' => $sModule, 'action' => 'commentPost', 'types' => array('personal', 'follow_member', 'follow_context')),
                array('group' => 'reply', 'unit' => $sModule, 'action' => 'replyPost', 'types' => array('personal')),
                array('group' => 'vote', 'unit' => $sModule, 'action' => 'doVote', 'types' => array('personal', 'follow_member', 'follow_context'))
            ),
            'alerts' => array(
                array('unit' => $sModule, 'action' => 'added'),
                array('unit' => $sModule, 'action' => 'edited'),
                array('unit' => $sModule, 'action' => 'deleted'),

                array('unit' => $sModule, 'action' => 'commentPost'),
                array('unit' => $sModule, 'action' => 'commentRemoved'),

                array('unit' => $sModule, 'action' => 'replyPost'),
                array('unit' => $sModule, 'action' => 'replyRemoved'),

                array('unit' => $sModule, 'action' => 'doVote'),
                array('unit' => $sModule, 'action' => 'undoVote'),
            )
        );
    }

    /**
     * Entry post for Notifications module
     */
    public function serviceGetNotificationsPost($aEvent)
    {
		$CNF = &$this->_oConfig->CNF;

        $aContentInfo = $this->_oDb->getContentInfoById($aEvent['object_id']);
        if(empty($aContentInfo) || !is_array($aContentInfo))
            return array();

        $sEntryUrl = BX_DOL_URL_ROOT . BxDolPermalinks::getInstance()->permalink('page.php?i=' . $CNF['URI_VIEW_ENTRY'] . '&id=' . $aContentInfo[$CNF['FIELD_ID']]);
        $sEntryCaption = isset($aContentInfo[$CNF['FIELD_TITLE']]) ? $aContentInfo[$CNF['FIELD_TITLE']] : strmaxtextlen($aContentInfo[$CNF['FIELD_TEXT']], 20, '...');

		return array(
			'entry_sample' => $CNF['T']['txt_sample_single'],
			'entry_url' => $sEntryUrl,
			'entry_caption' => $sEntryCaption,
			'entry_author' => $aContentInfo[$CNF['FIELD_AUTHOR']],
			'entry_privacy' => '', //may be empty or not specified. In this case Public privacy will be used.
			'lang_key' => '', //may be empty or not specified. In this case the default one from Notification module will be used.
		);
    }

	/**
     * Entry post comment for Notifications module
     */
    public function serviceGetNotificationsComment($aEvent)
    {
    	$CNF = &$this->_oConfig->CNF;

    	$iContentId = (int)$aEvent['object_id'];
        $aContentInfo = $this->_oDb->getContentInfoById($iContentId);
        if(empty($aContentInfo) || !is_array($aContentInfo))
            return array();

		$oComment = BxDolCmts::getObjectInstance($CNF['OBJECT_COMMENTS'], $iContentId);
        if(!$oComment || !$oComment->isEnabled())
            return array();

        $sEntryUrl = BX_DOL_URL_ROOT . BxDolPermalinks::getInstance()->permalink('page.php?i=' . $CNF['URI_VIEW_ENTRY'] . '&id=' . $aContentInfo[$CNF['FIELD_ID']]);
        $sEntryCaption = isset($aContentInfo[$CNF['FIELD_TITLE']]) ? $aContentInfo[$CNF['FIELD_TITLE']] : strmaxtextlen($aContentInfo[$CNF['FIELD_TEXT']], 20, '...');

		return array(
			'entry_sample' => $CNF['T']['txt_sample_single'],
			'entry_url' => $sEntryUrl,
			'entry_caption' => $sEntryCaption,
			'entry_author' => $aContentInfo[$CNF['FIELD_AUTHOR']],
			'subentry_sample' => $CNF['T']['txt_sample_comment_single'],
			'subentry_url' => $oComment->getViewUrl((int)$aEvent['subobject_id']),
			'lang_key' => '', //may be empty or not specified. In this case the default one from Notification module will be used.
		);
    }

	/**
     * Entry post reply for Notifications module
     */
    public function serviceGetNotificationsReply($aEvent)
    {
    	$CNF = &$this->_oConfig->CNF;

    	$oComment = BxDolCmts::getObjectInstance($CNF['OBJECT_COMMENTS'], 0, false);
        if(!$oComment || !$oComment->isEnabled())
            return array();

    	$iParentId = (int)$aEvent['object_id'];
        $aParentInfo = $oComment->getQueryObject()->getCommentsBy(array('type' => 'id', 'id' => $iParentId));
        if(empty($aParentInfo) || !is_array($aParentInfo))
            return array();

        $iObjectId = (int)$aParentInfo['cmt_object_id'];
		$oComment->init($iObjectId);

		return array(
		    'object_id' => $iObjectId,
			'entry_sample' => '_cmt_txt_sample_comment_single',
			'entry_url' => $oComment->getViewUrl($iParentId),
			'entry_caption' => strmaxtextlen($aParentInfo['cmt_text'], 20, '...'),
			'entry_author' => (int)$aParentInfo['cmt_author_id'],
			'subentry_sample' => '_cmt_txt_sample_reply_to',
			'subentry_url' => $oComment->getViewUrl((int)$aEvent['subobject_id']),
			'lang_key' => '', //may be empty or not specified. In this case the default one from Notification module will be used.
		);
    }

	/**
     * Entry post vote for Notifications module
     */
    public function serviceGetNotificationsVote($aEvent)
    {
    	$CNF = &$this->_oConfig->CNF;

    	$iContentId = (int)$aEvent['object_id'];
        $aContentInfo = $this->_oDb->getContentInfoById($iContentId);
        if(empty($aContentInfo) || !is_array($aContentInfo))
            return array();

		$oVote = BxDolVote::getObjectInstance($CNF['OBJECT_VOTES'], $iContentId);
        if(!$oVote || !$oVote->isEnabled())
            return array();

        $sEntryUrl = BX_DOL_URL_ROOT . BxDolPermalinks::getInstance()->permalink('page.php?i=' . $CNF['URI_VIEW_ENTRY'] . '&id=' . $aContentInfo[$CNF['FIELD_ID']]);
        $sEntryCaption = isset($aContentInfo[$CNF['FIELD_TITLE']]) ? $aContentInfo[$CNF['FIELD_TITLE']] : strmaxtextlen($aContentInfo[$CNF['FIELD_TEXT']], 20, '...');

		return array(
			'entry_sample' => $CNF['T']['txt_sample_single'],
			'entry_url' => $sEntryUrl,
			'entry_caption' => $sEntryCaption,
			'entry_author' => $aContentInfo[$CNF['FIELD_AUTHOR']],
			'subentry_sample' => $CNF['T']['txt_sample_vote_single'],
			'lang_key' => '', //may be empty or not specified. In this case the default one from Notification module will be used.
		);
    }

    /**
     * Data for Timeline module
     */
    public function serviceGetTimelineData()
    {
    	$sModule = $this->_aModule['name'];

        return array(
            'handlers' => array(
                array('group' => $sModule . '_object', 'type' => 'insert', 'alert_unit' => $sModule, 'alert_action' => 'added', 'module_name' => $sModule, 'module_method' => 'get_timeline_post', 'module_class' => 'Module',  'groupable' => 0, 'group_by' => ''),
                array('group' => $sModule . '_object', 'type' => 'update', 'alert_unit' => $sModule, 'alert_action' => 'edited'),
                array('group' => $sModule . '_object', 'type' => 'delete', 'alert_unit' => $sModule, 'alert_action' => 'deleted')
            ),
            'alerts' => array(
                array('unit' => $sModule, 'action' => 'added'),
                array('unit' => $sModule, 'action' => 'edited'),
                array('unit' => $sModule, 'action' => 'deleted'),
            )
        );
    }

    /**
     * Entry post for Timeline module
     */
    public function serviceGetTimelinePost($aEvent, $aBrowseParams = array())
    {
        $aContentInfo = $this->_oDb->getContentInfoById($aEvent['object_id']);
        if(empty($aContentInfo) || !is_array($aContentInfo))
            return false;

        $CNF = &$this->_oConfig->CNF;

        $iUserId = $this->getUserId();
        $iAuthorId = (int)$aContentInfo[$CNF['FIELD_AUTHOR']];
        $iAuthorIdAbs = abs($iAuthorId);
        if($iAuthorId < 0 && $iAuthorIdAbs == (int)$aEvent['owner_id'] && $iAuthorIdAbs != $iUserId)
            return false;

        //--- Views
        $oViews = isset($CNF['OBJECT_VIEWS']) ? BxDolView::getObjectInstance($CNF['OBJECT_VIEWS'], $aEvent['object_id']) : null;

        $aViews = array();
        if ($oViews && $oViews->isEnabled())
            $aViews = array(
                'system' => $CNF['OBJECT_VIEWS'],
                'object_id' => $aContentInfo[$CNF['FIELD_ID']],
                'count' => $aContentInfo['views']
            );

        //--- Votes
        $oVotes = isset($CNF['OBJECT_VOTES']) ? BxDolVote::getObjectInstance($CNF['OBJECT_VOTES'], $aEvent['object_id']) : null;

        $aVotes = array();
        if ($oVotes && $oVotes->isEnabled())
            $aVotes = array(
                'system' => $CNF['OBJECT_VOTES'],
                'object_id' => $aContentInfo[$CNF['FIELD_ID']],
                'count' => $aContentInfo['votes']
            );

        //--- Scores
        $oScores = isset($CNF['OBJECT_SCORES']) ? BxDolScore::getObjectInstance($CNF['OBJECT_SCORES'], $aEvent['object_id']) : null;

        $aScores = array();
        if ($oScores && $oScores->isEnabled())
            $aScores = array(
                'system' => $CNF['OBJECT_SCORES'],
                'object_id' => $aContentInfo[$CNF['FIELD_ID']],
                'score' => $aContentInfo['score']
            );

		//--- Reports
        $oReports = isset($CNF['OBJECT_REPORTS']) ? BxDolReport::getObjectInstance($CNF['OBJECT_REPORTS'], $aEvent['object_id']) : null;

        $aReports = array();
        if ($oReports && $oReports->isEnabled())
            $aReports = array(
                'system' => $CNF['OBJECT_REPORTS'],
                'object_id' => $aContentInfo[$CNF['FIELD_ID']],
                'count' => $aContentInfo['reports']
            );

        //--- Comments
        $oCmts = isset($CNF['OBJECT_COMMENTS']) ? BxDolCmts::getObjectInstance($CNF['OBJECT_COMMENTS'], $aEvent['object_id']) : null;

        $aComments = array();
        if($oCmts && $oCmts->isEnabled())
            $aComments = array(
                'system' => $CNF['OBJECT_COMMENTS'],
                'object_id' => $aContentInfo[$CNF['FIELD_ID']],
                'count' => $aContentInfo['comments']
            );

        //--- Title & Description
        $sTitle = !empty($aContentInfo[$CNF['FIELD_TITLE']]) ? $aContentInfo[$CNF['FIELD_TITLE']] : '';
        if(empty($sTitle) && !empty($aContentInfo[$CNF['FIELD_TEXT']]))
            $sTitle = $aContentInfo[$CNF['FIELD_TEXT']];

        return array(
            'owner_id' => $iAuthorId,
            'icon' => !empty($CNF['ICON']) ? $CNF['ICON'] : '',
            'sample' => isset($CNF['T']['txt_sample_single_with_article']) ? $CNF['T']['txt_sample_single_with_article'] : $CNF['T']['txt_sample_single'],
            'sample_wo_article' => $CNF['T']['txt_sample_single'],
            'sample_action' => isset($CNF['T']['txt_sample_action']) ? $CNF['T']['txt_sample_action'] : '',
            'url' => BX_DOL_URL_ROOT . BxDolPermalinks::getInstance()->permalink('page.php?i=' . $CNF['URI_VIEW_ENTRY'] . '&id=' . $aContentInfo[$CNF['FIELD_ID']]),
            'content' => $this->_getContentForTimelinePost($aEvent, $aContentInfo, $aBrowseParams), //a string to display or array to parse default template before displaying.
            'allowed_view' => method_exists($this, 'checkAllowedView') ? $this->checkAllowedView($aContentInfo) : CHECK_ACTION_RESULT_ALLOWED,
            'date' => $aContentInfo[$CNF['FIELD_ADDED']],
            'views' => $aViews,
            'votes' => $aVotes,
            'scores' => $aScores,
            'reports' => $aReports,
            'comments' => $aComments,
            'title' => $sTitle, //may be empty.
            'description' => '' //may be empty.
        );
    }

    /**
     * Check particular action permission without content
     * @param $sAction action to check, for example: Browse, Add
     * @param $iContentId content ID
     * @return message on error, or CHECK_ACTION_RESULT_ALLOWED when allowed
     */ 
    public function serviceCheckAllowed($sAction, $isPerformAction = false)
    {
        $sMethod = 'checkAllowed' . bx_gen_method_name($sAction);
        if (!method_exists($this, $sMethod))
            return _t('_sys_request_method_not_found_cpt');

        return $this->$sMethod($isPerformAction);
    }
    
    /**
     * Check particular action permission with content
     * @param $sAction action to check, for example: View, Edit
     * @param $iContentId content ID
     * @return message on error, or CHECK_ACTION_RESULT_ALLOWED when allowed
     */ 
    public function serviceCheckAllowedWithContent($sAction, $iContentId, $isPerformAction = false)
    {
        if (!$iContentId || !($aContentInfo = $this->_oDb->getContentInfoById($iContentId)))
            return _t('_sys_request_page_not_found_cpt');

        $sMethod = 'checkAllowed' . bx_gen_method_name($sAction);
        if (!method_exists($this, $sMethod))
            return _t('_sys_request_method_not_found_cpt');

        return $this->$sMethod($aContentInfo, $isPerformAction);
    }
    
    public function serviceCheckAllowedCommentsView($iContentId, $sObjectComments) 
    {
        return $this->serviceCheckAllowedWithContent('comments_view', $iContentId);
    }
    
    public function serviceCheckAllowedCommentsPost($iContentId, $sObjectComments) 
    {
        return $this->serviceCheckAllowedWithContent('comments_post', $iContentId);
    }

    public function serviceGetContentOwnerProfileId ($iContentId)
    {
        $CNF = &$this->_oConfig->CNF;

        // file owner must be author of the content or profile itself in case of profile based module
        if ($iContentId) {
            if ($this instanceof iBxDolProfileService) {
                $oProfile = BxDolProfile::getInstanceByContentAndType($iContentId, $this->getName());
            }
            else {
                $aContentInfo = $this->_oDb->getContentInfoById($iContentId);
                $oProfile = $aContentInfo ? BxDolProfile::getInstance($aContentInfo[$CNF['FIELD_AUTHOR']]) : null;
            }
            $iProfileId = $oProfile ? $oProfile->id() : bx_get_logged_profile_id();
        }
        else {
            $iProfileId = bx_get_logged_profile_id();
        }

        return $iProfileId;
    }
    
    // ====== PERMISSION METHODS

    public function checkAllowedSetThumb ($iContentId = 0)
    {
        return CHECK_ACTION_RESULT_ALLOWED;
    }
    
    /**
     * @return CHECK_ACTION_RESULT_ALLOWED if access is granted or error message if access is forbidden. So make sure to make "true === " checking.
     */
    public function checkAllowedBrowse ()
    {
        // check alert to allow custom checks
        $mixedResult = null;
        bx_alert('system', 'check_allowed_browse', 0, 0, array('module' => $this->getName(), 'profile_id' => $this->_iProfileId, 'override_result' => &$mixedResult));
        if($mixedResult !== null)
            return $mixedResult;

        return CHECK_ACTION_RESULT_ALLOWED;
    }

    /**
     * @return CHECK_ACTION_RESULT_ALLOWED if access is granted or error message if access is forbidden. So make sure to make strict(===) checking.
     */
    public function checkAllowedView ($aDataEntry, $isPerformAction = false)
    {
        return $this->serviceCheckAllowedViewForProfile ($aDataEntry, $isPerformAction);
    }

    public function serviceCheckAllowedViewForProfile ($aDataEntry, $isPerformAction = false, $iProfileId = false)
    {
        if (!$iProfileId)
            $iProfileId = $this->_iProfileId;

        $CNF = &$this->_oConfig->CNF;

        // moderator and owner always have access
        if (!empty($iProfileId) && (abs($aDataEntry[$CNF['FIELD_AUTHOR']]) == (int)$iProfileId || $this->_isModerator($isPerformAction)))
            return CHECK_ACTION_RESULT_ALLOWED;

        // check ACL
        $aCheck = checkActionModule($iProfileId, 'view entry', $this->getName(), $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] !== CHECK_ACTION_RESULT_ALLOWED)
            return $aCheck[CHECK_ACTION_MESSAGE];

        // check privacy
        if (!empty($CNF['OBJECT_PRIVACY_VIEW'])) {
            $oPrivacy = BxDolPrivacy::getObjectInstance($CNF['OBJECT_PRIVACY_VIEW']);
            if ($oPrivacy && !$oPrivacy->check($aDataEntry[$CNF['FIELD_ID']], $iProfileId))
                return _t('_sys_access_denied_to_private_content');
        }

        // check alert to allow custom checks
        $mixedResult = null;
        bx_alert('system', 'check_allowed_view', 0, 0, array('module' => $this->getName(), 'content_info' => $aDataEntry, 'profile_id' => $iProfileId, 'override_result' => &$mixedResult));
        if($mixedResult !== null)
            return $mixedResult;

        return CHECK_ACTION_RESULT_ALLOWED;
    }

    /**
     * @return CHECK_ACTION_RESULT_ALLOWED if access is granted or error message if access is forbidden. So make sure to make strict(===) checking.
     */
    public function checkAllowedAdd ($isPerformAction = false)
    {
        // check ACL
        $aCheck = checkActionModule($this->_iProfileId, 'create entry', $this->getName(), $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] !== CHECK_ACTION_RESULT_ALLOWED)
            return $aCheck[CHECK_ACTION_MESSAGE];
        return CHECK_ACTION_RESULT_ALLOWED;
    }

    /**
     * @return CHECK_ACTION_RESULT_ALLOWED if access is granted or error message if access is forbidden. So make sure to make strict(===) checking.
     */
    public function checkAllowedEdit ($aDataEntry, $isPerformAction = false)
    {
        // moderator and owner always have access
        if ($aDataEntry[$this->_oConfig->CNF['FIELD_AUTHOR']] == $this->_iProfileId || -$aDataEntry[$this->_oConfig->CNF['FIELD_AUTHOR']] == $this->_iProfileId || $this->_isModerator($isPerformAction))
            return CHECK_ACTION_RESULT_ALLOWED;
        return _t('_sys_txt_access_denied');
    }

    /**
     * @return CHECK_ACTION_RESULT_ALLOWED if access is granted or error message if access is forbidden. So make sure to make strict(===) checking.
     */
    public function checkAllowedDelete (&$aDataEntry, $isPerformAction = false)
    {
        // moderator always has access
        if ($this->_isModerator($isPerformAction))
            return CHECK_ACTION_RESULT_ALLOWED;

        // check ACL
        $aCheck = checkActionModule($this->_iProfileId, 'delete entry', $this->getName(), $isPerformAction);
        if (($aDataEntry[$this->_oConfig->CNF['FIELD_AUTHOR']] == $this->_iProfileId || -$aDataEntry[$this->_oConfig->CNF['FIELD_AUTHOR']] == $this->_iProfileId) && $aCheck[CHECK_ACTION_RESULT] === CHECK_ACTION_RESULT_ALLOWED)
            return CHECK_ACTION_RESULT_ALLOWED;

        return _t('_sys_txt_access_denied');
    }

    /**
     * @return CHECK_ACTION_RESULT_ALLOWED if access is granted or error message if access is forbidden. So make sure to make strict(===) checking.
     */
    public function checkAllowedSetMembership (&$aDataEntry, $isPerformAction = false)
    {
        // admin always has access
        if (isAdmin())
            return CHECK_ACTION_RESULT_ALLOWED;

        // check ACL
        $aCheck = checkActionModule($this->_iProfileId, 'set acl level', 'system', $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] !== CHECK_ACTION_RESULT_ALLOWED)
            return $aCheck[CHECK_ACTION_MESSAGE];

        return CHECK_ACTION_RESULT_ALLOWED;
    }

    /**
     * @return CHECK_ACTION_RESULT_ALLOWED if access is granted or error message if access is forbidden. So make sure to make strict(===) checking.
     */
    public function checkAllowedEditAnyEntry ($isPerformAction = false)
    {
    	$aCheck = checkActionModule($this->_iProfileId, 'edit any entry', $this->getName(), $isPerformAction);
    	if($aCheck[CHECK_ACTION_RESULT] === CHECK_ACTION_RESULT_ALLOWED)
    		return CHECK_ACTION_RESULT_ALLOWED;

    	return _t('_sys_txt_access_denied');
    }

	/**
     * @return CHECK_ACTION_RESULT_ALLOWED if access is granted or error message if access is forbidden. So make sure to make "true === " checking.
     */
    public function checkAllowedCommentsView ($aContentInfo, $isPerformAction = false)
    {
        return $this->checkAllowedView ($aContentInfo, $isPerformAction);
    }

	/**
     * @return CHECK_ACTION_RESULT_ALLOWED if access is granted or error message if access is forbidden. So make sure to make "true === " checking.
     */
    public function checkAllowedCommentsPost ($aContentInfo, $isPerformAction = false)
    {
        return $this->checkAllowedView ($aContentInfo, $isPerformAction);
    }

    public function _serviceBrowse ($sMode, $aParams = false, $iDesignBox = BX_DB_PADDING_DEF, $bDisplayEmptyMsg = false, $bAjaxPaginate = true, $sClassSearchResult = 'SearchResult')
    {
        if (CHECK_ACTION_RESULT_ALLOWED !== ($sMsg = $this->checkAllowedBrowse()))
            return MsgBox($sMsg);

        bx_import($sClassSearchResult, $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . $sClassSearchResult;
        $o = new $sClass($sMode, $aParams);

        $o->setDesignBoxTemplateId($iDesignBox);
        $o->setDisplayEmptyMsg($bDisplayEmptyMsg);
        $o->setAjaxPaginate($bAjaxPaginate);
        $o->setUnitParams(array('context' => $sMode));

        if ($o->isError)
            return '';

        if ($s = $o->processing())
            return $s;
        else
            return '';
    }

	// ====== COMMON METHODS

    public function getEntryImageData($aContentInfo, $sField = 'FIELD_THUMB', $aTranscoders = array())
    {
        if(empty($aTranscoders))
            $aTranscoders = array('OBJECT_TRANSCODER_COVER', 'OBJECT_IMAGES_TRANSCODER_COVER', 'OBJECT_IMAGES_TRANSCODER_GALLERY');

        $CNF = &$this->_oConfig->CNF;
        if(empty($CNF[$sField]) || empty($aContentInfo[$CNF[$sField]]) || empty($CNF['OBJECT_STORAGE']))
            return false;

        $iId = (int)$aContentInfo[$CNF[$sField]];
        foreach($aTranscoders as $sTranscoder)
            if(!empty($CNF[$sTranscoder]))
                return array('id' => $iId, 'transcoder' => $CNF[$sTranscoder]);

        return array('id' => $iId, 'object' => $CNF['OBJECT_STORAGE']);
    }

    public function getProfileId()
    {
    	return bx_get_logged_profile_id();
    }

	public function getProfileInfo($iUserId = 0)
    {
        $oProfile = $this->getObjectUser($iUserId);

        $oAccount = null;
        if($oProfile && !($oProfile instanceof BxDolProfileUndefined) && !($oProfile instanceof BxDolProfileAnonymous))
            $oAccount = $oProfile->getAccountObject();
        $bAccount = !empty($oAccount);

        return array(
        	'id' => $oProfile->id(),
            'name' => $oProfile->getDisplayName(),
        	'email' => $bAccount ? $oAccount->getEmail() : '',
            'link' => $oProfile->getUrl(),
            'icon' => $oProfile->getIcon(),
        	'thumb' => $oProfile->getThumb(),
        	'avatar' => $oProfile->getAvatar(),
            'unit' => $oProfile->getUnit(),
        	'active' => $oProfile->isActive(),
        );
    }
    
    public function getObjectUser($iUserId = 0)
    {
    	bx_import('BxDolProfile');
        $oProfile = BxDolProfile::getInstanceMagic($iUserId);
        return $oProfile;
    }

    public function getObjectFavorite($sSystem = '', $iId = 0)
    {
        $CNF = &$this->_oConfig->CNF;
        if(empty($sSystem) && !empty($CNF['OBJECT_FAVORITES']))
            $sSystem = $CNF['OBJECT_FAVORITES'];

        if(empty($sSystem))
            return false;

        $oFavorite = BxDolFavorite::getObjectInstance($sSystem, $iId, true, $this->_oTemplate);
        if(!$oFavorite->isEnabled())
            return false;

        return $oFavorite;
    }

	public function getUserId()
    {
        return isLogged() ? bx_get_logged_profile_id() : 0;
    }

    public function getUserIp()
    {
        return getVisitorIP();
    }

    public function getUserInfo($iUserId = 0)
    {
        $oProfile = BxDolProfile::getInstanceMagic($iUserId);

        return array(
            $oProfile->getDisplayName(),
            $oProfile->getUrl(),
            $oProfile->getThumb(),
            $oProfile->getUnit(),
            $oProfile->getUnit(0, array('template' => 'unit_wo_info'))
        );
    }

    public function isMenuItemVisible($sObject, &$aItem, &$aContentInfo)
    {
        $CNF = &$this->_oConfig->CNF;

        // default visible settings
        if(!BxDolAcl::getInstance()->isMemberLevelInSet($aItem['visible_for_levels']))
            return false;

        if (!empty($aItem['visibility_custom'])) {
            $oMenu = BxDolMenu::getObjectInstance($sObject);
            if ($oMenu && !BxDolService::callSerialized($aItem['visibility_custom'], $oMenu->getMarkers()))
                return false;
        }
        
        // get custom function name to check menu item visibility
        $sFuncCheckAccess = false;
        if(isset($CNF['MENU_ITEM_TO_METHOD'][$sObject][$aItem['name']]))
            $sFuncCheckAccess = $CNF['MENU_ITEM_TO_METHOD'][$sObject][$aItem['name']];

        // check custom visibility settings defined in module config class
        if($sFuncCheckAccess && CHECK_ACTION_RESULT_ALLOWED !== call_user_func_array(array($this, $sFuncCheckAccess), isset($aContentInfo) ? array(&$aContentInfo) : array()))
            return false;

        return true;
    }

    public function _isModerator ($isPerformAction = false)
    {
        return CHECK_ACTION_RESULT_ALLOWED === $this->checkAllowedEditAnyEntry ($isPerformAction);
    }

    // ====== PROTECTED METHODS

    protected function _serviceEntityForm ($sFormMethod, $iContentId = 0, $sDisplay = false, $sCheckFunction = false, $bErrorMsg = true)
    {
        $iContentId = $this->_getContent($iContentId, false);
        if($iContentId === false)
            return false;

        bx_import('FormsEntryHelper', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormsEntryHelper';
        $oFormsHelper = new $sClass($this);
        return $oFormsHelper->$sFormMethod((int)$iContentId, $sDisplay, $sCheckFunction, $bErrorMsg);
    }

    protected function _serviceTemplateFunc ($sFunc, $iContentId, $sFuncGetContent = 'getContentInfoById')
    {
        $mixedContent = $this->_getContent($iContentId, $sFuncGetContent);
        if($mixedContent === false)
            return false;

        list($iContentId, $aContentInfo) = $mixedContent;

        return $this->_oTemplate->$sFunc($aContentInfo);
    }

    protected function _rss ($aArgs, $sClass = 'SearchResult')
    {
        $sMode = array_shift($aArgs);

        if (CHECK_ACTION_RESULT_ALLOWED !== ($sMsg = $this->checkAllowedBrowse())) {
            $this->_oTemplate->displayAccessDenied ($sMsg);
            exit;
        }

        $aParams = $this->_buildRssParams($sMode, $aArgs);

        bx_import ($sClass, $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . $sClass;
        $o = new $sClass($sMode, $aParams);

        if ($o->isError)
            $this->_oTemplate->displayPageNotFound ();
        else
            $o->outputRSS();

        exit;
    }

    protected function _getContent($iContentId = 0, $sFuncGetContent = 'getContentInfoById')
    {
        if(!$iContentId)
            $iContentId = bx_process_input(bx_get('id'), BX_DATA_INT);

        if(!$iContentId)
            return false;

        if(empty($sFuncGetContent) || !method_exists($this->_oDb, $sFuncGetContent))
            return $iContentId;

        $aContentInfo = $this->_oDb->$sFuncGetContent($iContentId);
        if(!$aContentInfo)
            return false;

        return array($iContentId, $aContentInfo);
    }

    protected function _getContentForTimelinePost($aEvent, $aContentInfo, $aBrowseParams = array())
    {
    	$CNF = &$this->_oConfig->CNF;

    	$sUrl = BX_DOL_URL_ROOT . BxDolPermalinks::getInstance()->permalink('page.php?i=' . $CNF['URI_VIEW_ENTRY'] . '&id=' . $aContentInfo[$CNF['FIELD_ID']]);

    	//--- Image(s)
        $aImages = $this->_getImagesForTimelinePost($aEvent, $aContentInfo, $sUrl, $aBrowseParams);

        //--- Video(s)
        $aVideos = $this->_getVideosForTimelinePost($aEvent, $aContentInfo, $sUrl, $aBrowseParams);

        //--- Text
        $sText = isset($CNF['FIELD_TEXT']) && isset($aContentInfo[$CNF['FIELD_TEXT']]) ? $aContentInfo[$CNF['FIELD_TEXT']] : '';
        if(!empty($CNF['OBJECT_METATAGS']) && is_string($sText)) {
        	$oMetatags = BxDolMetatags::getObjectInstance($CNF['OBJECT_METATAGS']);
        	$sText = $oMetatags->metaParse($aContentInfo[$CNF['FIELD_ID']], $sText);
        }

    	return array(
    		'sample' => isset($CNF['T']['txt_sample_single_with_article']) ? $CNF['T']['txt_sample_single_with_article'] : $CNF['T']['txt_sample_single'],
    		'sample_wo_article' => $CNF['T']['txt_sample_single'],
    	    'sample_action' => isset($CNF['T']['txt_sample_action']) ? $CNF['T']['txt_sample_action'] : '',
			'url' => $sUrl,
			'title' => isset($CNF['FIELD_TITLE']) && isset($aContentInfo[$CNF['FIELD_TITLE']]) ? $aContentInfo[$CNF['FIELD_TITLE']] : 
			(isset($CNF['FIELD_TEXT']) && isset($aContentInfo[$CNF['FIELD_TEXT']]) ? strmaxtextlen($aContentInfo[$CNF['FIELD_TEXT']], 20, '...') : ''),
			'text' => $sText,
			'images' => $aImages,
            'videos' => $aVideos
		);
    }

    protected function _getImagesForTimelinePost($aEvent, $aContentInfo, $sUrl, $aBrowseParams = array())
    {
        $CNF = &$this->_oConfig->CNF;

        $sImage = $sImageOrig = '';
        if(isset($CNF['FIELD_COVER']) && isset($aContentInfo[$CNF['FIELD_COVER']]) && $aContentInfo[$CNF['FIELD_COVER']]) {
            $sImage = $this->_oConfig->getImageUrl($aContentInfo[$CNF['FIELD_COVER']], array('OBJECT_IMAGES_TRANSCODER_GALLERY', 'OBJECT_IMAGES_TRANSCODER_THUMB'));
            $sImageOrig = $this->_oConfig->getImageUrl($aContentInfo[$CNF['FIELD_COVER']], array('OBJECT_IMAGES_TRANSCODER_COVER'));
        }

        $bImageThumb = isset($CNF['FIELD_THUMB']) && isset($aContentInfo[$CNF['FIELD_THUMB']]) && $aContentInfo[$CNF['FIELD_THUMB']];
        if($sImage == '' && $bImageThumb)
            $sImage = $this->_oConfig->getImageUrl($aContentInfo[$CNF['FIELD_THUMB']], array('OBJECT_IMAGES_TRANSCODER_GALLERY', 'OBJECT_IMAGES_TRANSCODER_THUMB'));

        if($sImageOrig == '' && $bImageThumb)
            $sImageOrig = $this->_oConfig->getImageUrl($aContentInfo[$CNF['FIELD_THUMB']], array('OBJECT_IMAGES_TRANSCODER_COVER'));

        if(empty($sImage))
            return array();

        if($sImageOrig == '')
            $sImageOrig = $sImage;

        return array(
		    array('url' => $sUrl, 'src' => $sImage, 'src_orig' => $sImageOrig),
		);
    }

    protected function _getVideosForTimelinePost($aEvent, $aContentInfo, $sUrl, $aBrowseParams = array())
    {
        return array();
    }

    protected function _entityComments ($sObject, $iId = 0)
    {
        if (!$iId)
            $iId = bx_process_input(bx_get('id'), BX_DATA_INT);

        if (!$iId)
            return false;

        $oCmts = BxDolCmts::getObjectInstance($sObject, $iId);
        if (!$oCmts || !$oCmts->isEnabled())
            return false;

        return $oCmts->getCommentsBlock(array(), array('in_designbox' => false, 'show_empty' => true));
    }

    protected function _getFields($iContentId)
    {
        $aContentInfo = $this->_oDb->getContentInfoById($iContentId);
        if(empty($aContentInfo))
            return array();

        return BxDolContentInfo::formatFields($aContentInfo);
    }

    protected function _getFieldValue($sField, $iContentId)
    {
        $CNF = &$this->_oConfig->CNF;
        if(empty($CNF[$sField]))
            return false;

        $aContentInfo = $this->_oDb->getContentInfoById($iContentId);
        if(empty($aContentInfo) || empty($aContentInfo[$CNF[$sField]]))
            return false;

        return $aContentInfo[$CNF[$sField]];
    }

    protected function _getFieldValueThumb($sField, $iContentId, $sTranscoder = '') 
    {
        $CNF = &$this->_oConfig->CNF;

        if(empty($sTranscoder) || empty($CNF[$sField]))
            return false;

        $aContentInfo = $this->_oDb->getContentInfoById($iContentId);
        if(empty($aContentInfo) || empty($aContentInfo[$CNF[$sField]]))
            return false;

        $oImagesTranscoder = BxDolTranscoderImage::getObjectInstance($sTranscoder);
        if(!$oImagesTranscoder)
            return false;

        return $oImagesTranscoder->getFileUrl($aContentInfo[$CNF[$sField]]);
    }

	protected function _prepareResponse($aResponse, $bAsJson = false, $aAdditional = array())
    {
    	if(!$bAsJson)
    		return $aResponse;

		if(!empty($aAdditional) && is_array($aAdditional))
			$aResponse = array_merge($aResponse, $aAdditional);

		echoJson($aResponse);
		exit;
    }
}

/** @} */
