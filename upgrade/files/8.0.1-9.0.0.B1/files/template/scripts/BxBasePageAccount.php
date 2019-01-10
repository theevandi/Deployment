<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    TridentCore Trident Core
 * @{
 */

/**
 * Account page.
 */
class BxBasePageAccount extends BxTemplPage
{
    protected $_aMapStatus2LangKey = array (
        BX_PROFILE_STATUS_PENDING => '_sys_txt_account_pending',
        BX_PROFILE_STATUS_SUSPENDED => '_sys_txt_account_suspended',
    );

    public function __construct($aObject, $oTemplate)
    {
        parent::__construct($aObject, $oTemplate);

        $oProfile = BxDolProfile::getInstance();
        $aProfileInfo = $oProfile ? $oProfile->getInfo() : false;

        $this->addMarkers(array(
            'account_id' => $aProfileInfo ? $aProfileInfo['account_id'] : 0,
            'profile_id' => $aProfileInfo ? $aProfileInfo['id'] : 0,
            'profile_type' => $aProfileInfo ? $aProfileInfo['type'] : 0,
            'profile_content_id' => $aProfileInfo ? $aProfileInfo['content_id'] : 0,
        ));

        // set settings submenu
        $oMenuSubmenu = BxDolMenu::getObjectInstance('sys_site_submenu');
        if ($oMenuSubmenu) {
            $oMenuSubmenu->setObjectSubmenu('sys_account_settings_submenu', array (
                'title' => _t('_sys_menu_item_title_account_settings'),
                'link' => BX_DOL_URL_ROOT . 'member.php',
                'icon' => '',
            ));
        }

        // display message if profile isn't active
        if ($oProfile) {
            $sStatus = $oProfile->getStatus();
            if (isset($this->_aMapStatus2LangKey[$sStatus])) {
                $oInformer = BxDolInformer::getInstance($this->_oTemplate);
                if ($oInformer)
                    $oInformer->add('sys-account-status-not-active', _t($this->_aMapStatus2LangKey[$sStatus]), BX_INFORMER_ALERT);
            }
        }

        // switch profile context
        if ($iSwitchToProfileId = (int)bx_get('switch_to_profile')) {
            $oInformer = BxDolInformer::getInstance($this->_oTemplate);
            $oProfile = BxDolProfile::getInstance($iSwitchToProfileId);
            $aProfile = $oProfile->getInfo();
            $sInformerMsg = '';            

            if (BxDolService::call($aProfile['type'], 'act_as_profile')) {
                if ($oProfile && $oProfile->getAccountId() == getLoggedId()) {
                    $oAccount = BxDolAccount::getInstance();
                    if ($oAccount->updateProfileContext($iSwitchToProfileId))
                        $sInformerMsg = _t('_sys_txt_account_profile_context_changed_success', $oProfile->getDisplayName());
                }

                if ($oInformer)
                    $oInformer->add('sys-account-profile-context-change-result', $sInformerMsg ? $sInformerMsg : _t('_error occured'), $sInformerMsg ? BX_INFORMER_INFO : BX_INFORMER_ERROR);
            }
        }

    }

	protected function _addJsCss()
    {
    	parent::_addJsCss();

        $this->_oTemplate->addCss('account.css');
    }

    protected function _getPageCacheParams ()
    {
        return getLoggedId(); // cache is different for every account
    }
}

/** @} */
