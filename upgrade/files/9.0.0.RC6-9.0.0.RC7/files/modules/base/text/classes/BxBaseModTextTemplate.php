<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    BaseText Base classes for text modules
 * @ingroup     UnaModules
 *
 * @{
 */

/**
 * Module representation.
 */
class BxBaseModTextTemplate extends BxBaseModGeneralTemplate
{
    protected $_sUnitClassShowCase;
    
    function __construct(&$oConfig, &$oDb)
    {
        parent::__construct($oConfig, $oDb);
        
        $this->_sUnitClassShowCase = 'bx-base-unit-showcase bx-base-text-unit-showcase bx-def-margin-sec-bottom';
    }

    function unit ($aData, $isCheckPrivateContent = true, $sTemplateName = 'unit.html', $aParams = array())
    {
    	$sResult = $this->checkPrivacy ($aData, $isCheckPrivateContent, $this->getModule(), $sTemplateName);
    	if($sResult)
            return $sResult;
        $aParams['template_name'] = $sTemplateName;
		return $this->parseHtmlByName($sTemplateName, $this->getUnit($aData, $aParams));
    }

    function entryAuthor ($aData, $iProfileId = false, $sFuncAuthorDesc = 'getAuthorDesc', $sTemplateName = 'author.html', $sFuncAuthorAddon = 'getAuthorAddon')
    {
        $CNF = &$this->getModule()->_oConfig->CNF;

        if (!$iProfileId)
            $iProfileId = $aData[$CNF['FIELD_AUTHOR']];

        $oProfile = BxDolProfile::getInstance($iProfileId);
        if (!$oProfile) 
            $oProfile = BxDolProfileUndefined::getInstance();

        if (!$oProfile)
            return '';

        $sName = $oProfile->getDisplayName();
        $sAddon = $sFuncAuthorAddon ? $this->$sFuncAuthorAddon($aData, $oProfile) : '';

        $aVars = array (
            'author_url' => $oProfile->getUrl(),
            'author_thumb_url' => $oProfile->getThumb(),
            'author_unit' => $oProfile->getUnit(0, array('template' => 'unit_wo_info')),
            'author_title' => $sName,
            'author_title_attr' => bx_html_attribute($sName),
            'author_desc' => $sFuncAuthorDesc ? $this->$sFuncAuthorDesc($aData) : '',
            'bx_if:addon' => array (
                'condition' => (bool)$sAddon,
                'content' => array (
                    'content' => $sAddon,
                ),
            ),
        );
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }

    function entryContext ($aData, $iProfileId = false, $sFuncContextDesc = 'getContextDesc', $sTemplateName = 'context.html', $sFuncContextAddon = 'getContextAddon')
    {
        $CNF = &$this->getModule()->_oConfig->CNF;

        $iContextId = $aData[$CNF['FIELD_ALLOW_VIEW_TO']];
        if ($iContextId >= 0)
            return '';
        
        $iProfileId = - $iContextId;

        $oProfile = BxDolProfile::getInstance($iProfileId);
        if (!$oProfile) 
            $oProfile = BxDolProfileUndefined::getInstance();

        if (!$oProfile)
            return '';

        $sName = $oProfile->getDisplayName();
        $sAddon = $sFuncContextAddon ? $this->$sFuncContextAddon($aData, $oProfile) : '';

        $aVars = array (
            'author_url' => $oProfile->getUrl(),
            'author_thumb_url' => $oProfile->getThumb(),
            'author_unit' => $oProfile->getUnit(0, array('template' => 'unit_wo_info')),
            'author_title' => $sName,
            'author_title_attr' => bx_html_attribute($sName),
            'author_desc' => $sFuncContextDesc ? $this->$sFuncContextDesc($aData) : '',
            'bx_if:addon' => array (
                'condition' => (bool)$sAddon,
                'content' => array (
                    'content' => $sAddon,
                ),
            ),
        );
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }

    function getAuthorDesc ($aData)
    {
        return '';
    }
    
    function getContextDesc ($aData)
    {
        return '';
    }

    function getAuthorAddon ($aData, $oProfile)
    {
        $CNF = &$this->getModule()->_oConfig->CNF;
        $sUrl = 'page.php?i=' . $CNF['URI_AUTHOR_ENTRIES'] . '&profile_id=' . $oProfile->id();
        $sUrl = BxDolPermalinks::getInstance()->permalink($sUrl);
        return _t($CNF['T']['txt_all_entries_by'], $sUrl, $oProfile->getDisplayName(), $this->getModule()->_oDb->getEntriesNumByAuthor($oProfile->id()));
    }
    
    function getContextAddon ($aData, $oProfile)
    {
        $CNF = &$this->getModule()->_oConfig->CNF;
        $sUrl = 'page.php?i=' . $CNF['URI_ENTRIES_BY_CONTEXT'] . '&profile_id=' . $oProfile->id();
        $sUrl = BxDolPermalinks::getInstance()->permalink($sUrl);
        return _t($CNF['T']['txt_all_entries_in'], $sUrl, $oProfile->getDisplayName(), $this->getModule()->_oDb->getEntriesNumByContext($oProfile->id()));
    }

    public function entryBreadcrumb($aContentInfo, $aTmplVarsItems = array())
    {
    	$CNF = &BxDolModule::getInstance($this->MODULE)->_oConfig->CNF;

    	return $this->parseHtmlByName('breadcrumb.html', array(
    		'url_home' => BX_DOL_URL_ROOT . BxDolPermalinks::getInstance()->permalink($CNF['URL_HOME']),
    		'icon_home' => $CNF['ICON'],
    		'bx_repeat:items' => $aTmplVarsItems
    	));
    }

    protected function checkPrivacy ($aData, $isCheckPrivateContent, $oModule, $sTemplateName = '')
    {
        if ($isCheckPrivateContent && CHECK_ACTION_RESULT_ALLOWED !== ($sMsg = $oModule->checkAllowedView($aData))) {
            $aVars = array (
                'summary' => $sMsg,
            );
            return $this->parseHtmlByName($sTemplateName ? str_replace('.html', '_private.html', $sTemplateName) : 'unit_private.html', $aVars);
        }

        return '';
    }

    protected function getUnitThumbAndGallery ($aData)
    {
        $CNF = &BxDolModule::getInstance($this->MODULE)->_oConfig->CNF;

        $sPhotoThumb = '';
        $sPhotoGallery = '';
        if(!empty($CNF['FIELD_THUMB']) && !empty($aData[$CNF['FIELD_THUMB']])) {

            $oImagesTranscoder = BxDolTranscoderImage::getObjectInstance($CNF['OBJECT_IMAGES_TRANSCODER_PREVIEW']);
            if ($oImagesTranscoder)
                $sPhotoThumb = $oImagesTranscoder->getFileUrl($aData[$CNF['FIELD_THUMB']]);

            $oImagesTranscoder = BxDolTranscoderImage::getObjectInstance($CNF['OBJECT_IMAGES_TRANSCODER_GALLERY']);
            if ($oImagesTranscoder)
                $sPhotoGallery = $oImagesTranscoder->getFileUrl($aData[$CNF['FIELD_THUMB']]);
            else
                $sPhotoGallery = $sPhotoThumb;
        }

        return array($sPhotoThumb, $sPhotoGallery);
    }

	protected function getUnit ($aData, $aParams = array())
    {
        $CNF = &BxDolModule::getInstance($this->MODULE)->_oConfig->CNF;

        // get thumb url
        list($sPhotoThumb, $sPhotoGallery) = $this->getUnitThumbAndGallery($aData);

        // get entry url
        $sUrl = BX_DOL_URL_ROOT . BxDolPermalinks::getInstance()->permalink('page.php?i=' . $CNF['URI_VIEW_ENTRY'] . '&id=' . $aData[$CNF['FIELD_ID']]);

        $oProfile = BxDolProfile::getInstance($aData[$CNF['FIELD_AUTHOR']]);
        if (!$oProfile) 
            $oProfile = BxDolProfileUndefined::getInstance();

        $sTitle = $this->getTitle($aData);
        $sText = $this->getText($aData);
        $sSummary = $this->getSummary($aData, $sTitle, $sText, $sUrl);
        $sSummaryPlain = isset($CNF['PARAM_CHARS_SUMMARY_PLAIN']) && $CNF['PARAM_CHARS_SUMMARY_PLAIN'] ? BxTemplFunctions::getInstance()->getStringWithLimitedLength(strip_tags($sSummary), (int)getParam($CNF['PARAM_CHARS_SUMMARY_PLAIN'])) : '';

        if(!empty($CNF['OBJECT_METATAGS'])) {
            $oMetatags = BxDolMetatags::getObjectInstance($CNF['OBJECT_METATAGS']);
            $sText = $oMetatags->metaParse($aData[$CNF['FIELD_ID']], $sText);
        }

        $sTitle = bx_process_output($sTitle);

        $aTmplVarsMeta = array();
        if(!empty($CNF['OBJECT_MENU_SNIPPET_META'])) {
            $oMenuMeta = BxDolMenu::getObjectInstance($CNF['OBJECT_MENU_SNIPPET_META'], $this);
            if($oMenuMeta) {
                $oMenuMeta->setContentId($aData[$CNF['FIELD_ID']]);
                $aTmplVarsMeta = array(
                    'meta' => $oMenuMeta->getCode()
                );
            }
        }

        // generate html
        return array (
            'class' => $this->_getUnitClass($aData,(isset($aParams['template_name']) ? $aParams['template_name'] : '')),
            'id' => $aData[$CNF['FIELD_ID']],
            'content_url' => $sUrl,
            'title' => $sTitle,
            'title_attr' => bx_html_attribute($sTitle),
            'summary' => $sSummary,
            'text' => $sText,
            'author' => $oProfile->getDisplayName(),
            'author_url' => $oProfile->getUrl(),
            'author_icon' => $oProfile->getIcon(),
            'author_thumb' => $oProfile->getThumb(),
            'author_avatar' => $oProfile->getAvatar(),
            'entry_posting_date' => bx_time_js($aData[$CNF['FIELD_ADDED']], BX_FORMAT_DATE),
            'module_name' => _t($CNF['T']['txt_sample_single']),
            'ts' => $aData[$CNF['FIELD_ADDED']],
            'bx_if:meta' => array(
                'condition' => !empty($aTmplVarsMeta),
                'content' => $aTmplVarsMeta
            ),
            'bx_if:thumb' => array (
                'condition' => $sPhotoThumb,
                'content' => array (
                    'title' => $sTitle,
                    'summary_attr' => bx_html_attribute($sSummaryPlain),
                    'content_url' => $sUrl,
                    'thumb_url' => $sPhotoThumb ? $sPhotoThumb : '',
                    'gallery_url' => $sPhotoGallery ? $sPhotoGallery : '',
                    'strecher' => str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ', 40),
                ),
            ),
            'bx_if:no_thumb' => array (
                'condition' => !$sPhotoThumb,
                'content' => array (
                    'content_url' => $sUrl,
                    'summary_plain' => $sSummaryPlain,
                    'strecher' => mb_strlen($sSummaryPlain) > 240 ? '' : str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ', round((240 - mb_strlen($sSummaryPlain)) / 6)),
                ),
            ),
        );
    }

    protected function getTitle($aData)
    {
        $CNF = &$this->getModule()->_oConfig->CNF;

        return isset($aData[$CNF['FIELD_TITLE']]) ? $aData[$CNF['FIELD_TITLE']] : '';
    }

    protected function getText($aData)
    {
        $CNF = &$this->getModule()->_oConfig->CNF;

        return isset($aData[$CNF['FIELD_TEXT']]) ? $aData[$CNF['FIELD_TEXT']] : '';
    }

    protected function getSummary($aData, $sTitle = '', $sText = '', $sUrl = '')
    {
        $CNF = &$this->getModule()->_oConfig->CNF;
        if(empty($CNF['PARAM_CHARS_SUMMARY']))
            return '';

        // get summary
        $sLinkMore = ' <a title="' . bx_html_attribute(_t('_sys_read_more', $sTitle)) . '" href="' . $sUrl . '"><i class="sys-icon ellipsis-h"></i></a>';
        return  strmaxtextlen($sText, (int)getParam($CNF['PARAM_CHARS_SUMMARY']), $sLinkMore);
    }
    
    protected function _getUnitClass($aData, $sTemplateName = 'unit.html')
    {
        $sResult = '';

        switch($sTemplateName) {
            case 'unit_showcase.html':
                $sResult = $this->_sUnitClassShowCase;
                break;
        }

        return $sResult;
    }
}

/** @} */
