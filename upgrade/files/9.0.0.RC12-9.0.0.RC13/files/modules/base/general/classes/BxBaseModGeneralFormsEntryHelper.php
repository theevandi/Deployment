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

/**
 * Entry forms helper functions
 */
class BxBaseModGeneralFormsEntryHelper extends BxDolProfileForms
{
    protected $_oModule;
    protected $_bAjaxMode;
    protected $_bDynamicMode;

    public function __construct($oModule)
    {
        parent::__construct();
        $this->_oModule = $oModule;

        $this->_bDynamicMode = false;

        $this->_bAjaxMode = false;
        $mixedAjaxMode = bx_get('ajax_mode');
        if($mixedAjaxMode !== false)
        	$this->setAjaxMode($mixedAjaxMode);
    }

	public function setAjaxMode($bAjaxMode)
    {
        $this->_bAjaxMode = (bool)$bAjaxMode;
        if($this->_bAjaxMode)
        	$this->setDynamicMode(true);
    }

    public function setDynamicMode($bDynamicMode)
    {
        $this->_bDynamicMode = (bool)$bDynamicMode;
    }

    public function getObjectStorage()
    {
        return BxDolStorage::getObjectInstance($this->_oModule->_oConfig->CNF['OBJECT_STORAGE']);
    }

    public function getObjectFormAdd ($sDisplay = false)
    {
    	$CNF = &$this->_oModule->_oConfig->CNF;

        if (false === $sDisplay)
            $sDisplay = $CNF['OBJECT_FORM_ENTRY_DISPLAY_ADD'];
        
        return BxDolForm::getObjectInstance($CNF['OBJECT_FORM_ENTRY'], $sDisplay, $this->_oModule->_oTemplate);
    }

    public function getObjectFormEdit ($sDisplay = false)
    {
    	$CNF = &$this->_oModule->_oConfig->CNF;

        if (false === $sDisplay)
            $sDisplay = $CNF['OBJECT_FORM_ENTRY_DISPLAY_EDIT'];

        return BxDolForm::getObjectInstance($CNF['OBJECT_FORM_ENTRY'], $sDisplay, $this->_oModule->_oTemplate);
    }

	public function getObjectFormView ($sDisplay = false)
    {
    	$CNF = &$this->_oModule->_oConfig->CNF;

        if (false === $sDisplay)
            $sDisplay = $CNF['OBJECT_FORM_ENTRY_DISPLAY_VIEW'];

        return BxDolForm::getObjectInstance($CNF['OBJECT_FORM_ENTRY'], $sDisplay, $this->_oModule->_oTemplate);
    }

    public function getObjectFormDelete ($sDisplay = false)
    {
        $CNF = &$this->_oModule->_oConfig->CNF;

        if (false === $sDisplay)
            $sDisplay = $CNF['OBJECT_FORM_ENTRY_DISPLAY_DELETE'];

        return BxDolForm::getObjectInstance($CNF['OBJECT_FORM_ENTRY'], $sDisplay, $this->_oModule->_oTemplate);
    }

    public function viewDataEntry ($iContentId)
    {
        // get content data and profile info
        list ($oProfile, $aContentInfo) = $this->_getProfileAndContentData($iContentId);
        if (!$aContentInfo)
            return MsgBox(_t('_sys_txt_error_entry_is_not_defined'));

        // check access
        if (CHECK_ACTION_RESULT_ALLOWED !== ($sMsg = $this->_oModule->checkAllowedView($aContentInfo)))
            return MsgBox($sMsg);

        return $this->_oModule->_oTemplate->entryText($aContentInfo);
    }

    public function addData ($iProfile, $aValues, $sDisplay = false)
    {
        $CNF = &$this->_oModule->_oConfig->CNF;

        // check and display form
        $oForm = $this->getObjectFormAdd($sDisplay);
        if (!$oForm)
            return array('code' => 1, 'message' => '_sys_txt_error_occured');

        $oForm->aFormAttrs['method'] = BX_DOL_FORM_METHOD_SPECIFIC;
        $oForm->aParams['csrf']['disable'] = true;
        if(!empty($oForm->aParams['db']['submit_name'])) {            
            $sSubmitName = false;
            if (is_array($oForm->aParams['db']['submit_name'])) {
                foreach ($oForm->aParams['db']['submit_name'] as $sVal) {
                    if (isset($oForm->aInputs[$sVal])) {
                        $sSubmitName = $sVal;
                        break;
                    }
                }
            } 
            else {
                $sSubmitName = $oForm->aParams['db']['submit_name'];
            }
            if ($sSubmitName && isset($oForm->aInputs[$sSubmitName]))
                $aValues[$sSubmitName] = $oForm->aInputs[$sSubmitName]['value'];
        }

        $oForm->initChecker(array(), $aValues);
        if (!$oForm->isSubmittedAndValid())
            return array('code' => 2, 'message' => '_sys_txt_error_occured');

        // insert data into database
        $aValsToAdd = array ();
        if(isset($CNF['FIELD_AUTHOR']))
            $aValsToAdd[$CNF['FIELD_AUTHOR']] = $iProfile;

        $iContentId = $oForm->insert($aValsToAdd);
        if (!$iContentId) {
            if (!$oForm->isValid())
                return array('code' => 2, 'message' => '_sys_txt_error_occured');
            else
                return array('code' => 3, 'message' => '_sys_txt_error_entry_creation');
        }

        $sResult = $this->onDataAddAfter(BxDolProfile::getInstance($iProfile)->getAccountId(), $iContentId);
        if($sResult)
            return array('code' => 4, 'message' => $sResult);

        // process uploaded files
        if (isset($CNF['FIELD_PHOTO']))
            $oForm->processFiles ($CNF['FIELD_PHOTO'], $iContentId, true);
        
        list ($oProfile, $aContentInfo) = $this->_getProfileAndContentData($iContentId);
        return array('code' => 0, 'message' => '', 'content' => $aContentInfo);
    }

    public function addDataForm ($sDisplay = false, $sCheckFunction = false)
    {
        if (!$sCheckFunction)
            $sCheckFunction = 'checkAllowedAdd';
        
        $CNF = &$this->_oModule->_oConfig->CNF;

        // check access
        if (CHECK_ACTION_RESULT_ALLOWED !== ($sMsg = $this->_oModule->$sCheckFunction())) {
            $oProfile = BxDolProfile::getInstance();
            if ($oProfile && ($aProfileInfo = $oProfile->getInfo()) && $aProfileInfo['type'] == 'system' && is_subclass_of($this->_oModule, 'BxBaseModProfileModule') && $this->_oModule->serviceActAsProfile()) // special check for system profile is needed, because of incorrect error message
                return $this->prepareResponse(MsgBox(_t('_sys_txt_access_denied')), $this->_bAjaxMode, 'msg');
            else
                return $this->prepareResponse(MsgBox($sMsg), $this->_bAjaxMode, 'msg');
        }

        // check and display form
        $oForm = $this->getObjectFormAdd($sDisplay);
        if (!$oForm)
            return $this->prepareResponse(MsgBox(_t('_sys_txt_error_occured')), $this->_bAjaxMode, 'msg');

        $oForm->initChecker();
        if (!$oForm->isSubmittedAndValid())
            return $this->prepareResponse($oForm->getCode($this->_bDynamicMode), $this->_bAjaxMode && $oForm->isSubmitted(), 'form', array(
            	'form_id' => $oForm->getId()
            ));

        // insert data into database
        $aValsToAdd = array ();
        $iContentId = $oForm->insert ($aValsToAdd);
        if (!$iContentId) {
            if (!$oForm->isValid())
                return $this->prepareResponse($oForm->getCode($this->_bDynamicMode), $this->_bAjaxMode, 'form', array(
                	'form_id' => $oForm->getId()
                ));
            else
                return $this->prepareResponse(MsgBox(_t('_sys_txt_error_entry_creation')), $this->_bAjaxMode, 'msg');
        }

        $sResult = $this->onDataAddAfter (getLoggedId(), $iContentId);
        if ($sResult)
            return $this->prepareResponse($sResult, $this->_bAjaxMode, 'msg');

        // process uploaded files
        if (isset($CNF['FIELD_PHOTO']))
            $oForm->processFiles ($CNF['FIELD_PHOTO'], $iContentId, true);

        // perform action
        $this->_oModule->checkAllowedAdd(true);

        // redirect
        list ($oProfile, $aContentInfo) = $this->_getProfileAndContentData($iContentId);
        $this->redirectAfterAdd($aContentInfo);
    }
    
    public function redirectAfterAdd($aContentInfo)
    {
    	$CNF = &$this->_oModule->_oConfig->CNF;

    	$sUrl = 'page.php?i=' . $CNF['URI_VIEW_ENTRY'] . '&id=' . $aContentInfo[$CNF['FIELD_ID']];
        if($this->_bAjaxMode)
        	$this->prepareResponse($sUrl, $this->_bAjaxMode, 'redirect');
		else
        	$this->_redirectAndExit($sUrl);
    }

    public function editDataForm ($iContentId, $sDisplay = false, $sCheckFunction = false, $bErrorMsg = true)
    {
        if (!$sCheckFunction)
            $sCheckFunction = 'checkAllowedEdit';

        $CNF = &$this->_oModule->_oConfig->CNF;

        // get content data and profile info
        list ($oProfile, $aContentInfo) = $this->_getProfileAndContentData($iContentId);
        if (!$aContentInfo)
            return $bErrorMsg ? MsgBox(_t('_sys_txt_error_entry_is_not_defined')) : '';

        // check access
        if (CHECK_ACTION_RESULT_ALLOWED !== ($sMsg = $this->_oModule->$sCheckFunction($aContentInfo)))
            return $bErrorMsg ? MsgBox($sMsg) : '';

        // check and display form
        $oForm = $this->getObjectFormEdit($sDisplay);
        if (!$oForm)
            return $bErrorMsg ? MsgBox(_t('_sys_txt_error_occured')) : '';

        $aSpecificValues = array();        
        if (!empty($CNF['OBJECT_METATAGS'])) {
            $oMetatags = BxDolMetatags::getObjectInstance($CNF['OBJECT_METATAGS']);
            if ($oMetatags->locationsIsEnabled())
                $aSpecificValues = $oMetatags->locationGet($iContentId, empty($CNF['FIELD_LOCATION_PREFIX']) ? '' : $CNF['FIELD_LOCATION_PREFIX']);
        }
        $oForm->initChecker($aContentInfo, $aSpecificValues);

        if (!$oForm->isSubmittedAndValid())
            return $oForm->getCode();

        // update data in the DB
        $aTrackTextFieldsChanges = null;

        $this->onDataEditBefore ($aContentInfo[$CNF['FIELD_ID']], $aContentInfo, $aTrackTextFieldsChanges);

        if (!$oForm->update ($aContentInfo[$CNF['FIELD_ID']], array(), $aTrackTextFieldsChanges)) {
            if (!$oForm->isValid())
                return $oForm->getCode();
            else
                return MsgBox(_t('_sys_txt_error_entry_update'));
        }

        $sResult = $this->onDataEditAfter ($aContentInfo[$CNF['FIELD_ID']], $aContentInfo, $aTrackTextFieldsChanges, $oProfile, $oForm);
        if ($sResult)
            return $sResult;

        // process uploaded files
        if (isset($CNF['FIELD_PHOTO']))
            $oForm->processFiles ($CNF['FIELD_PHOTO'], $iContentId, false);

        // perform action
        $this->_oModule->checkAllowedEdit($aContentInfo, true);
        
        // redirect
        $this->redirectAfterEdit($aContentInfo);
    }

    protected function redirectAfterEdit($aContentInfo)
    {
        $CNF = &$this->_oModule->_oConfig->CNF;
        $this->_redirectAndExit('page.php?i=' . $CNF['URI_VIEW_ENTRY'] . '&id=' . $aContentInfo[$CNF['FIELD_ID']]);
    }

    public function deleteDataForm ($iContentId, $sDisplay = false, $sCheckFunction = false)
    {
        if (!$sCheckFunction)
            $sCheckFunction = 'checkAllowedDelete';
        
        $CNF = &$this->_oModule->_oConfig->CNF;

        // get content data and profile info
        list ($oProfile, $aContentInfo) = $this->_getProfileAndContentData($iContentId);
        if (!$aContentInfo)
            return MsgBox(_t('_sys_txt_error_entry_is_not_defined'));

        // check access
        if (CHECK_ACTION_RESULT_ALLOWED !== ($sMsg = $this->_oModule->$sCheckFunction($aContentInfo)))
            return MsgBox($sMsg);

        // check and display form
        $oForm = $this->getObjectFormDelete($sDisplay);
        if (!$oForm)
            return MsgBox(_t('_sys_txt_error_occured'));

        $oForm->initChecker($aContentInfo);

        if (!$oForm->isSubmittedAndValid())
            return $oForm->getCode();

        if ($sError = $this->deleteData($aContentInfo[$CNF['FIELD_ID']], $aContentInfo, $oProfile, $oForm))
            return MsgBox($sError);

        // perform action
        $this->_oModule->checkAllowedDelete($aContentInfo, true);

        // redirect
        $this->redirectAfterDelete($aContentInfo);
    }

    protected function redirectAfterDelete($aContentInfo)
    {
        $CNF = &$this->_oModule->_oConfig->CNF;
        $this->_redirectAndExit($CNF['URL_HOME'], true, array(
            'account_id' => getLoggedId(),
            'profile_id' => bx_get_logged_profile_id(),
        ));
    }

    /**
     * Delete data entry
     * @param $iContentId entry id
     * @param $oForm optional content info array
     * @param $aContentInfo optional content info array
     * @param $oProfile optional content author profile
     * @return error string on error or empty string on success
     */
    public function deleteData ($iContentId, $aContentInfo = false, $oProfile = null, $oForm = null)
    {
        $CNF = &$this->_oModule->_oConfig->CNF;

        if (!$aContentInfo || !$oProfile)
            list ($oProfile, $aContentInfo) = $this->_getProfileAndContentData($iContentId);

        if (!$aContentInfo)
            return _t('_sys_txt_error_entry_is_not_defined');

        if (!$oForm)
            $oForm = $this->getObjectFormDelete();

        if (!$oForm->delete ($aContentInfo[$CNF['FIELD_ID']], $aContentInfo))
            return _t('_sys_txt_error_entry_delete');

        if ($sResult = $this->onDataDeleteAfter ($aContentInfo[$CNF['FIELD_ID']], $aContentInfo, $oProfile))
            return $sResult;

        // create an alert
        bx_alert($this->_oModule->getName(), 'deleted', $aContentInfo[$CNF['FIELD_ID']]);

        return '';
    }

    public function viewDataForm ($iContentId, $sDisplay = false)
    {
        $CNF = &$this->_oModule->_oConfig->CNF;

        // get content data and profile info
        list ($oProfile, $aContentInfo) = $this->_getProfileAndContentData($iContentId);
        if (!$aContentInfo)
            return MsgBox(_t('_sys_txt_error_entry_is_not_defined'));

        // check access
        if ($sMsg = $this->_processPermissionsCheckForViewDataForm ($aContentInfo, $oProfile))
            return MsgBox($sMsg);

        // get form
        $oForm = $this->getObjectFormView($sDisplay);
        if (!$oForm)
            return MsgBox(_t('_sys_txt_error_occured'));

        // process metatags
        if (!empty($CNF['OBJECT_METATAGS'])) {
            $oMetatags = BxDolMetatags::getObjectInstance($CNF['OBJECT_METATAGS']);
            if ($oMetatags->keywordsIsEnabled()) {
                $aFields = $oMetatags->metaFields($aContentInfo, $CNF, $CNF['OBJECT_FORM_ENTRY_DISPLAY_VIEW']);
                $oForm->setMetatagsKeywordsData($iContentId, $aFields, $oMetatags);
            }
        }        

        // display profile
        $oForm->initChecker($aContentInfo);
        return $oForm->getCode();
    }

    protected function _processPermissionsCheckForViewDataForm ($aContentInfo, $oProfile)
    {
        if (CHECK_ACTION_RESULT_ALLOWED !== ($sMsg = $this->_oModule->checkAllowedView($aContentInfo)))
            return $sMsg;

        return '';
    }

    public function onDataDeleteAfter ($iContentId, $aContentInfo, $oProfile)
    {
        return '';
    }

    public function onDataEditBefore ($iContentId, $aContentInfo, &$aTrackTextFieldsChanges)
    {
    }

    public function onDataEditAfter ($iContentId, $aContentInfo, $aTrackTextFieldsChanges, $oProfile, $oForm)
    {
        $CNF = &$this->_oModule->_oConfig->CNF;

        if (!empty($CNF['OBJECT_METATAGS'])) { // && isset($aTrackTextFieldsChanges['changed_fields'][$CNF['FIELD_TEXT']])) { // TODO: check if aTrackTextFieldsChanges works 
            list ($oProfile, $aContentInfo) = $this->_getProfileAndContentData($iContentId);
            $oMetatags = BxDolMetatags::getObjectInstance($CNF['OBJECT_METATAGS']);
            $oMetatags->metaAddAuto($aContentInfo[$CNF['FIELD_ID']], $aContentInfo, $CNF, $CNF['OBJECT_FORM_ENTRY_DISPLAY_EDIT']);
            if (isset($CNF['FIELD_LOCATION_PREFIX']) && isset($oForm->aInputs[$CNF['FIELD_LOCATION_PREFIX']]) && $oMetatags->locationsIsEnabled())
                $oMetatags->locationsAddFromForm($aContentInfo[$CNF['FIELD_ID']], empty($CNF['FIELD_LOCATION_PREFIX']) ? '' : $CNF['FIELD_LOCATION_PREFIX']);

            if (!empty($CNF['FIELD_LABELS']) && ($aLabels = bx_get($CNF['FIELD_LABELS'])) && $oMetatags->keywordsIsEnabled()) {
                foreach ($aLabels as $sLabel)
                    $oMetatags->keywordsAddOne($aContentInfo[$CNF['FIELD_ID']], $sLabel, false);
            }
        }

        return '';
    }

    public function onDataAddAfter ($iAccountId, $iContentId)
    {
        $this->_processMetas($iAccountId, $iContentId);

        return '';
    }

    protected function _processMetas($iAccountId, $iContentId)
    {
        $CNF = &$this->_oModule->_oConfig->CNF;

        if (!empty($CNF['OBJECT_METATAGS'])) {
            list ($oProfile, $aContentInfo) = $this->_getProfileAndContentData($iContentId);
            $oMetatags = BxDolMetatags::getObjectInstance($CNF['OBJECT_METATAGS']);
            if ($aContentInfo)
                $oMetatags->metaAddAuto($aContentInfo[$CNF['FIELD_ID']], $aContentInfo, $CNF, $CNF['OBJECT_FORM_ENTRY_DISPLAY_ADD']);
            if ($oMetatags->locationsIsEnabled() && $aContentInfo)
                $oMetatags->locationsAddFromForm($aContentInfo[$CNF['FIELD_ID']], $CNF['FIELD_LOCATION_PREFIX']);

            if ($aContentInfo && !empty($CNF['FIELD_LABELS']) && ($aLabels = bx_get($CNF['FIELD_LABELS'])) && $oMetatags->keywordsIsEnabled()) {
                foreach ($aLabels as $sLabel)
                    $oMetatags->keywordsAddOne($aContentInfo[$CNF['FIELD_ID']], $sLabel, false);
            }
        }
    }

    protected function prepareCustomRedirectUrl($s, $aContentInfo)
    {
        $CNF = &$this->_oModule->_oConfig->CNF;
        $oProfile = BxDolProfile::getInstanceByContentAndType($aContentInfo[$CNF['FIELD_ID']], $this->_oModule->getName());

        $aMarkers = array(
            '{profile_id}',
            '{content_id}',
            '{module}',
        );
        $aReplacements = array(
            $oProfile ? $oProfile->id() : 0,
            $aContentInfo[$CNF['FIELD_ID']],
            $this->_oModule->getName(),
        );
        $s = str_replace($aMarkers, $aReplacements, $s);

        $s = BxDolPermalinks::getInstance()->permalink($s);

        if (false === strpos($s, 'http://') && false === strpos($s, 'https://'))
            $s = BX_DOL_URL_ROOT . $s;
        
        return $s;
    }

    protected function prepareResponse($mixedResponse, $bAsJson = false, $sKey = 'msg', $aAdditional = array())
    {
    	if(!$bAsJson)
    		return $mixedResponse;

		$aResponse = array(
			$sKey => $mixedResponse
		);

		if(!empty($aAdditional) && is_array($aAdditional))
			$aResponse = array_merge($aResponse, $aAdditional);

		echoJson($aResponse);
		exit;
    }
}

/** @} */
