<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaTemplate UNA Template Classes
 * @{
 */

/**
 * @see BxDolMenu
 */
class BxTemplMenuSite extends BxTemplMenu
{
    protected $_bSiteMenu;
    protected $_bSiteMenuSubmenu;

    public function __construct ($aObject, $oTemplate = false)
    {
        parent::__construct ($aObject, $oTemplate);

        $this->_bSiteMenu = $this->_sObject == 'sys_site';
        $this->_bSiteMenuSubmenu = false;
    }

    public function getCode ()
    {
        $sClass = 'bx-sliding-menu-main';
        $sStyle = 'display:none';
        if($this->_bSiteMenu) {
            $sClass = 'bx-sliding-smenu-main';
            $sStyle = '';
        }

        return '<div id="bx-sliding-menu-' . $this->_sObject . '" class="' . $sClass . ' bx-def-z-index-nav" style="' . $sStyle . '"><div class="bx-sliding-menu-main-cnt">' . parent::getCode() . '</div></div>';
    }

    protected function _getMenuItem ($a)
	{
	    $aResult = parent::_getMenuItem($a);
	    if(empty($aResult) || !is_array($aResult))
	        return $aResult;

        $aTmplVarsSubmenu = array();
        $bTmplVarsSubmenu = $this->_bSiteMenu && $this->_bSiteMenuSubmenu && !empty($aResult['submenu_object']) && (int)$aResult['submenu_popup'] == 1;
        if($bTmplVarsSubmenu) {
            $aResult['onclick'] = '';

            $aTmplVarsSubmenu['content'] = BxDolMenu::getObjectInstance($aResult['submenu_object'])->getCode();
        }

        $aResult['bx_if:show_arrow'] = array (
        	'condition' => false && $bTmplVarsSubmenu,
			'content' => array(),
        );

        $aResult['bx_if:show_line'] = array (
        	'condition' => true,
			'content' => array(),
        );

        $aResult['bx_if:show_submenu'] = array (
			'condition' => $bTmplVarsSubmenu,
			'content' => $aTmplVarsSubmenu,
		);

		return $aResult;
	}
}

/** @} */
