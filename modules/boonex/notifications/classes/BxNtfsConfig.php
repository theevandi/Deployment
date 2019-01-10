<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Notifications Notifications
 * @ingroup     UnaModules
 *
 * @{
 */

bx_import('BxDolPrivacy');

class BxNtfsConfig extends BxBaseModNotificationsConfig
{
    protected $_iOwnerNameMaxLen;
    protected $_iContentMaxLen;
    protected $_iPushMaxLen;

    protected $_aHandlersHiddenEmail;
    protected $_aHandlersHiddenPush;

    /**
     * Group settings by action. Enabled by default.
     */
    protected $_bSettingsGrouped;
    protected $_aSettingsTypes;

    protected $_aModulesProfiles;
    protected $_aModulesContexts;

    /**
     * Constructor
     */
    public function __construct($aModule)
    {
        parent::__construct($aModule);

        $this->CNF = array (
            'URL_HOME' => 'page.php?i=notifications-view',

            'OBJECT_MENU_SUBMENU' => 'bx_notifications_submenu', // main module submenu
            'OBJECT_MENU_SETTINGS' => 'bx_notifications_settings', // settings submenu
            'OBJECT_GRID_SETTINGS_ADMINISTRATION' => 'bx_notifications_settings_administration',
            'OBJECT_GRID_SETTINGS_COMMON' => 'bx_notifications_settings_common',

            'T' => array(
                'setting_personal' => '_bx_ntfs_setting_type_personal',
                'setting_follow_member' => '_bx_ntfs_setting_type_follow_member',
                'setting_follow_context' => '_bx_ntfs_setting_type_follow_context',
                'setting_other' => '_bx_ntfs_setting_type_other',
            )
        );

        $this->_aPrefixes = array(
            'style' => 'bx-ntfs',
            'language' => '_bx_ntfs',
            'option' => 'bx_notifications_'
        );

        $this->_iOwnerNameMaxLen = 21;
        $this->_iContentMaxLen = 21;
        $this->_iPushMaxLen = 190;

        $this->_aHandlerDescriptor = array('module_name' => '', 'module_method' => '', 'module_class' => '');
        $this->_sHandlersMethod = 'get_notifications_data';
        $this->_aHandlersHiddenEmail = array();
        $this->_aHandlersHiddenPush = array();

        $this->_bSettingsGrouped = true;
        $this->_aSettingsTypes = array(
            BX_NTFS_STYPE_PERSONAL,
            BX_NTFS_STYPE_FOLLOW_MEMBER,
            BX_NTFS_STYPE_FOLLOW_CONTEXT,
            //BX_NTFS_STYPE_OTHER           //TODO: May be it can be removed, because there is no events(alerts) for this type.
        );

        $this->_aModulesProfiles = false;
        $this->_aModulesContexts = false;

        $this->_aJsClasses = array(
            'main' => 'BxNtfsMain',
            'view' => 'BxNtfsView',
        );
        $this->_aJsObjects = array(
            'main' => 'oBxNtfsMain',
            'view' => 'oBxNtfsView',
        );

        $sHtmlPrefix = str_replace('_', '-', $this->_sName);
        $this->_aHtmlIds = array(
            'view' => array(
                'block' => $sHtmlPrefix,
                'events' => $sHtmlPrefix . '-events',
                'event' => $sHtmlPrefix . '-event-'
            )
        );
    }

    public function init(&$oDb)
    {
    	parent::init($oDb);

    	$sOptionPrefix = $this->getPrefix('option');
    	$this->_aPerPage = array(
            'default' => (int)getParam($sOptionPrefix . 'events_per_page'),
    	    'preview' => (int)getParam($sOptionPrefix . 'events_per_preview')
    	);

        $this->_bSettingsGrouped = getParam($sOptionPrefix . 'enable_group_settings') == 'on';

    	$aSettings = array(
    	    'site' => '',
    	    'email' => 'Email',
    	    'push' => 'Push'
    	);
    	foreach($aSettings as $sSetting => $sVariable) {
    	    $sHideTimeline = getParam($sOptionPrefix . 'events_hide_' . $sSetting);
            if(!empty($sHideTimeline))
                $this->{'_aHandlersHidden' . $sVariable} = explode(',', $sHideTimeline);
    	}
    }

    public function getOwnerNameMaxLen()
    {
        return $this->_iOwnerNameMaxLen;
    }

    public function getContentMaxLen()
    {
        return $this->_iContentMaxLen;
    }

    public function getPushMaxLen()
    {
        return $this->_iPushMaxLen;
    }

    public function getHandlersHidden($sType = '')
    {
        if(!in_array($sType, array('', 'email', 'push')))
            return array();

        return $this->{'_aHandlersHidden' . ucfirst($sType)};
    }

    public function isSettingsGrouped()
    {
        return $this->_bSettingsGrouped;
    }

    public function getSettingsTypes()
    {
        return $this->_aSettingsTypes;
    }

    /**
     * Ancillary functions
     */
    public function getViewUrl()
    {
        return BX_DOL_URL_ROOT . BxDolPermalinks::getInstance()->permalink('page.php?i=notifications-view');
    }

    public function getProfileBasedModules() 
    {
        if ($this->_aModulesProfiles !== false && $this->_aModulesContexts !== false)
            return array($this->_aModulesProfiles, $this->_aModulesContexts);

        if (getParam('sys_db_cache_enable')) {
            $oDb = BxDolDb::getInstance();
            $oCache = $oDb->getDbCacheObject();
            $sCacheKey = $oDb->genDbCacheKey('bx_ntfs_profile_based_modules');
            $aData = $oCache->getData($sCacheKey);
            if (null === $aData) {
                $aData = $this->_getProfileBasedModulesData();
                $oCache->setData ($sCacheKey, $aData);
            }
        } 
        else {
            $aData = $this->_getProfileBasedModulesData();
        }

        return $aData;
    }

    protected function _getProfileBasedModulesData() 
    {
        $this->_aModulesProfiles = array();
        $this->_aModulesContexts = array();

        $aModules = BxDolModuleQuery::getInstance()->getModulesBy(array('type' => 'modules'));
        foreach($aModules as $aModule) {
            if(!BxDolRequest::serviceExists($aModule['name'], 'act_as_profile'))
                continue;

            if(BxDolService::call($aModule['name'], 'act_as_profile'))
                $this->_aModulesProfiles[] = $aModule['name'];
            else
                $this->_aModulesContexts[] = $aModule['name'];
        }

        return array($this->_aModulesProfiles, $this->_aModulesContexts);        
    }
}

/** @} */
