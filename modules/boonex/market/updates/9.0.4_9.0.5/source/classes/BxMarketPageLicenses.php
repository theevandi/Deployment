<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Market Market
 * @ingroup     UnaModules
 *
 * @{
 */

/**
 * Profile's licenses page.
 */
class BxMarketPageLicenses extends BxBaseModTextPageAuthor
{
    protected $MODULE;

    public function __construct($aObject, $oTemplate = false)
    {
        $this->MODULE = 'bx_market';

        parent::__construct($aObject, $oTemplate);

        $oMenuSubmenu = BxDolMenu::getObjectInstance('sys_site_submenu');
        if($oMenuSubmenu) {
            $sMenuSubmenu = 'sys_account_dashboard';
            $oMenuSubmenu->setObjectSubmenu($sMenuSubmenu, array('title' => _t('_sys_menu_item_title_account_dashboard'), 'link' => '', 'icon' => ''));

            BxDolMenu::getObjectInstance($sMenuSubmenu)->setSelected($this->MODULE, 'dashboard-licenses');
        }
    }
}

/** @} */
