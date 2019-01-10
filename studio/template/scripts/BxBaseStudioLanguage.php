<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaView UNA Studio Representation classes
 * @ingroup     UnaStudio
 * @{
 */

class BxBaseStudioLanguage extends BxDolStudioLanguage
{
    protected $aMenuItems = array(
        BX_DOL_STUDIO_LANG_TYPE_SETTINGS => array('caption' => '_adm_lmi_cpt_settings', 'icon' => 'cogs')
    );

    function __construct($sLanguage = "", $sPage = "")
    {
        parent::__construct($sLanguage, $sPage);
    }
    function getPageCss()
    {
        return array_merge(parent::getPageCss(), array());
    }
    function getPageJs()
    {
        return array_merge(parent::getPageJs(), array('settings.js', 'language.js'));
    }
    function getPageJsObject()
    {
        return 'oBxDolStudioLanguage';
    }
    function getPageCaption()
    {
        $oTemplate = BxDolStudioTemplate::getInstance();

        $aTmplVars = array(
            'js_object' => $this->getPageJsObject(),
            'content' => parent::getPageCaption(),
        );
        return $oTemplate->parseHtmlByName('lang_page_caption.html', $aTmplVars);
    }
    function getPageAttributes()
    {
        if((int)$this->aLanguage['enabled'] == 0)
            return 'style="display:none"';

        return parent::getPageAttributes();
    }

    function getPageMenu($aMenu = array(), $aMarkers = array())
    {
        $sJsObject = $this->getPageJsObject();

        $aMenu = array();
        foreach($this->aMenuItems as $sName => $aItem)
            $aMenu[] = array(
                'name' => $sName,
                'icon' => $aItem['icon'],
                'link' => bx_append_url_params($this->sManageUrl, array('page' => $sName)),
                'title' => _t($aItem['caption']),
                'selected' => $sName == $this->sPage
            );

        return parent::getPageMenu($aMenu);
    }

    function getPageCode($bHidden = false)
    {
        $sMethod = 'get' . ucfirst($this->sPage);
        if(!method_exists($this, $sMethod))
            return '';

        if((int)$this->aLanguage['enabled'] != 1)
            BxDolStudioTemplate::getInstance()->addInjection('injection_bg_style', 'text', ' bx-std-page-bg-empty');

        return $this->$sMethod();
    }

    protected function getSettings()
    {
        $oPage = new BxTemplStudioSettings($this->sLanguage);

        return BxDolStudioTemplate::getInstance()->parseHtmlByName('language.html', array(
            'content' => $oPage->getPageCode()
        ));
    }
}

/** @} */
