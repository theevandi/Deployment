<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaBaseView UNA Base Representation Classes
 * @{
 */

/**
 * @see BxDolFeature
 */
class BxBaseFeature extends BxDolFeature
{
    protected static $_sTmplContentElementBlock;
    protected static $_sTmplContentElementInline;
    protected static $_sTmplContentDoFeatureLabel;

    protected $_bCssJsAdded;

	protected $_sJsObjClass;
    protected $_sJsObjName;
    protected $_sStylePrefix;

    protected $_aHtmlIds;

    protected $_aElementDefaults;

    public function __construct($sSystem, $iId, $iInit = 1, $oTemplate = false)
    {
        parent::__construct($sSystem, $iId, $iInit, $oTemplate);

        $this->_bCssJsAdded = false;

        $this->_sJsObjClass = 'BxDolFeature';
        $this->_sJsObjName = 'oFeature' . bx_gen_method_name($sSystem, array('_' , '-')) . $iId;
        $this->_sStylePrefix = 'bx-feature';

        $sHtmlId = str_replace(array('_' , ' '), array('-', '-'), $sSystem) . '-' . $iId;
        $this->_aHtmlIds = array(
            'main' => 'bx-feature-' . $sHtmlId,
        	'do_link' => 'bx-feature-do-link-' . $sHtmlId
        );

        $this->_aElementDefaults = array(
			'show_do_feature_as_button' => false,
			'show_do_feature_as_button_small' => false,
			'show_do_feature_icon' => true,
			'show_do_feature_label' => false
        );

        if(empty(self::$_sTmplContentElementBlock))
            self::$_sTmplContentElementBlock = $this->_oTemplate->getHtml('feature_element_block.html');

        if(empty(self::$_sTmplContentElementInline))
            self::$_sTmplContentElementInline = $this->_oTemplate->getHtml('feature_element_inline.html');

        if(empty(self::$_sTmplContentDoFeatureLabel))
            self::$_sTmplContentDoFeatureLabel = $this->_oTemplate->getHtml('feature_do_feature_label.html');
    }

    public function getJsObjectName()
    {
        return $this->_sJsObjName;
    }

    public function getJsScript($bDynamicMode = false)
    {
        $aParams = array(
            'sObjName' => $this->_sJsObjName,
            'sSystem' => $this->getSystemName(),
            'iAuthorId' => $this->_getAuthorId(),
            'iObjId' => $this->getId(),
            'sRootUrl' => BX_DOL_URL_ROOT,
            'sStylePrefix' => $this->_sStylePrefix,
            'aHtmlIds' => $this->_aHtmlIds
        );
        $sCode = "var " . $this->_sJsObjName . " = new " . $this->_sJsObjClass . "(" . json_encode($aParams) . ");";

        return $this->_oTemplate->_wrapInTagJsCode($sCode);
    }

    public function getJsClick()
    {
        return $this->getJsObjectName() . '.feature(this)';
    }

    public function getElementBlock($aParams = array())
    {
        $aParams['usage'] = BX_DOL_FEATURED_USAGE_BLOCK;

        return $this->getElement($aParams);
    }

    public function getElementInline($aParams = array())
    {
        $aParams['usage'] = BX_DOL_FEATURED_USAGE_INLINE;

        return $this->getElement($aParams);
    }

    public function getElement($aParams = array())
    {
    	$aParams = array_merge($this->_aElementDefaults, $aParams);
    	$bDynamicMode = isset($aParams['dynamic_mode']) && $aParams['dynamic_mode'] === true;

        $bShowDoFeatureAsButtonSmall = isset($aParams['show_do_feature_as_button_small']) && $aParams['show_do_feature_as_button_small'] == true;
        $bShowDoFeatureAsButton = !$bShowDoFeatureAsButtonSmall && isset($aParams['show_do_feature_as_button']) && $aParams['show_do_feature_as_button'] == true;

		$iObjectId = $this->getId();
		$iAuthorId = $this->_getAuthorId();
        $aFeature = $this->_oQuery->getFeature($iObjectId);
        $bCount = (int)$aFeature['count'] != 0;

        $bAllowedFeature = $this->isAllowedFeature();
        if(!$bAllowedFeature)
            return '';

        $aParams['is_featured'] = $this->isPerformed($iObjectId, $iAuthorId) ? true : false;

        $sTmplName = self::${'_sTmplContentElement' . bx_gen_method_name(!empty($aParams['usage']) ? $aParams['usage'] : BX_DOL_FEATURED_USAGE_DEFAULT)};
        return $this->_oTemplate->parseHtmlByContent($sTmplName, array(
            'style_prefix' => $this->_sStylePrefix,
            'html_id' => $this->_aHtmlIds['main'],
            'class' => $this->_sStylePrefix . ($bShowDoFeatureAsButton ? '-button' : '-link') . ($bShowDoFeatureAsButtonSmall ? '-button-small' : ''),
            'count' => $aFeature['count'],
            'do_feature' => $this->_getDoFeature($aParams, $bAllowedFeature),
            'script' => $this->getJsScript($bDynamicMode)
        ));
    }

    protected function _getDoFeature($aParams = array(), $bAllowedFeature = true)
    {
    	$bFeatured = isset($aParams['is_featured']) && $aParams['is_featured'] === true;
        $bShowDoFeatureAsButtonSmall = isset($aParams['show_do_feature_as_button_small']) && $aParams['show_do_feature_as_button_small'] == true;
        $bShowDoFeatureAsButton = !$bShowDoFeatureAsButtonSmall && isset($aParams['show_do_feature_as_button']) && $aParams['show_do_feature_as_button'] == true;
		$bDisabled = !$bAllowedFeature || ($bFeatured  && !$this->isUndo());

        $sClass = '';
		if($bShowDoFeatureAsButton)
			$sClass = 'bx-btn';
		else if ($bShowDoFeatureAsButtonSmall)
			$sClass = 'bx-btn bx-btn-small';

		if($bDisabled)
			$sClass .= $bShowDoFeatureAsButton || $bShowDoFeatureAsButtonSmall ? ' bx-btn-disabled' : 'bx-feature-disabled';

        return $this->_oTemplate->parseLink('javascript:void(0)', $this->_getLabelDoFeature($aParams), array(
        	'id' => $this->_aHtmlIds['do_link'],
            'class' => $this->_sStylePrefix . '-do-feature ' . $sClass,
            'title' => _t($this->_getTitleDoFeature($bFeatured)),
        	'onclick' => !$bDisabled ? $this->getJsClick() : ''
        ));
    }

    protected function _getLabelDoFeature($aParams = array())
    {
    	$bFeatured = isset($aParams['is_featured']) && $aParams['is_featured'] === true;
        return $this->_oTemplate->parseHtmlByContent(self::$_sTmplContentDoFeatureLabel, array(
        	'bx_if:show_icon' => array(
        		'condition' => isset($aParams['show_do_feature_icon']) && $aParams['show_do_feature_icon'] == true,
        		'content' => array(
        			'name' => $this->_getIconDoFeature($bFeatured)
        		)
        	),
        	'bx_if:show_text' => array(
        		'condition' => isset($aParams['show_do_feature_label']) && $aParams['show_do_feature_label'] == true,
        		'content' => array(
        			'text' => _t($this->_getTitleDoFeature($bFeatured))
        		)
        	)
        ));
    }
}

/** @} */
