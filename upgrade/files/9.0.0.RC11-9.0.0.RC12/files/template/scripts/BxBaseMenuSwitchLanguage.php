<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaBaseView UNA Base Representation Classes
 * @{
 */

/**
 * Site main menu representation.
 */
class BxBaseMenuSwitchLanguage extends BxTemplMenu
{
    protected $_sType;

    public function __construct ($aObject, $oTemplate)
    {
        parent::__construct ($aObject, $oTemplate);

        $this->_sType = str_replace('sys_switch_language_', '', $this->_sObject);
    }

    public function getMenuItems ()
    {
        $this->loadData();

        return parent::getMenuItems();
    }

    protected function loadData()
    {
        $aLanguages = BxDolLanguagesQuery::getInstance()->getLanguages(false, true);
        if(empty($aLanguages) || !is_array($aLanguages) || count($aLanguages) < 2)
            return;

        $sLanguage = BxDolLanguages::getInstance()->getCurrentLangName();
        $this->setSelected('', $sLanguage);

        list($sPageLink, $aPageParams) = $this->{'getBaseUrl' . bx_gen_method_name($this->_sType)}();

        $oPermalink = BxDolPermalinks::getInstance();
		$sMethod = 'getItemTitle' . bx_gen_method_name($this->_sType);

        $aItems = array();
        foreach( $aLanguages as $sName => $sLang ) {
            $aPageParams['lang'] = $sName;

            $aItems[] = array(
                'id' => $sName,
                'name' => $sName,
                'class' => '',
                'title' => $this->$sMethod($sName, $sLang),
                'target' => '_self',
                'icon' => '',
                'link' => bx_html_attribute($oPermalink->permalink(bx_append_url_params($sPageLink, $aPageParams))),
                'onclick' => ''
            );
        }

        $this->_aObject['menu_items'] = $aItems;
    }

    protected function getBaseUrlPopup()
    {
        return bx_get_base_url_popup();
    }

    protected function getBaseUrlInline()
    {
        return bx_get_base_url_inline();
    }

    protected function getItemTitlePopup($sName, $sTitle)
    {
    	return genFlag($sName) . ' ' . $sTitle;
    }

    protected function getItemTitleInline($sName, $sTitle)
    {
    	return genFlag($sName);
    }
}

/** @} */
