<?php use nspace\func;
defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Notifications Notifications
 * @ingroup     UnaModules
 *
 * @{
 */

bx_import('BxBaseModNotificationsModule');

define('BX_NTFS_TYPE_OBJECT_OWNER_AND_CONNECTIONS', 'obj_own_and_con');
define('BX_NTFS_TYPE_DEFAULT', BX_NTFS_TYPE_OBJECT_OWNER_AND_CONNECTIONS);

/**
 * STYPE - Setting Type:
 * 1. personal - related to you;
 * 2. follow_member - related to members you follow;
 * 3. follow_context - related to contexts you follow;
 * 4. other - others.
 */
define('BX_NTFS_STYPE_PERSONAL', 'personal');
define('BX_NTFS_STYPE_FOLLOW_MEMBER', 'follow_member');
define('BX_NTFS_STYPE_FOLLOW_CONTEXT', 'follow_context');
define('BX_NTFS_STYPE_OTHER', 'other');

class BxNtfsModule extends BxBaseModNotificationsModule
{
    /**
     * Constructor
     */
    function __construct($aModule)
    {
        parent::__construct($aModule);

        $this->_iOwnerId = $this->getUserId();
    }

    /**
     * ACTION METHODS
     */
    function actionGetPosts()
    {
        $aParams = $this->_prepareParamsGet();
        $sEvents = $this->_oTemplate->getPosts($aParams);

        echoJson(array('events' => $sEvents));
    }

    /**
     * SERVICE METHODS
     */

    /**
     * @page service Service Calls
     * @section bx_notifications Invitations
     * @subsection bx_notifications-other Other
     * @subsubsection bx_notifications-get_include get_include
     * 
     * @code bx_srv('bx_notifications', 'get_include', [...]); @endcode
     * 
     * Get all necessary CSS and JS files to include in a page.
     *
     * @return string with all necessary CSS and JS files.
     * 
     * @see BxInvModule::serviceGetInclude
     */
    /** 
     * @ref bx_notifications-get_include "get_include"
     */
    public function serviceGetInclude($bIncludeCss = true, $mixedIncludeJs = false)
    {
        return $this->_oTemplate->getInclude($bIncludeCss, $mixedIncludeJs);
    }

    /**
     * @page service Service Calls
     * @section bx_notifications Notifications
     * @subsection bx_notifications-page_blocks Page Blocks
     * @subsubsection bx_notifications-get_block_settings get_block_settings
     * 
     * @code bx_srv('bx_notifications', 'get_block_settings', [...]); @endcode
     * 
     * Get Settings block for a separate page.
     *
     * @return a string with block content to display on the site. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxNtfsModule::serviceGetBlockSettings
     */
    /** 
     * @ref bx_notifications-get_block_settings "get_block_settings"
     */
    public function serviceGetBlockSettings($sDeliveryType = '', $aParams = array())
    {
        if(!isLogged())
            return '';

        if(empty($sDeliveryType) && bx_get('delivery') !== false)
            $sDeliveryType = bx_process_input(bx_get('delivery'));

        if(empty($sDeliveryType))
            $sDeliveryType = BX_BASE_MOD_NTFS_DTYPE_SITE;

        $iUserId = bx_get_logged_profile_id();
        if(!empty($aParams['user_id']))
            $iUserId = (int)$aParams['user_id'];
        else
            $aParams['user_id'] = $iUserId;

        $this->_oDb->initSettingUser($iUserId);

        return array(
            'content' => $this->getBlockSettings($sDeliveryType, $aParams),
        );
    }

    /**
     * @page service Service Calls
     * @section bx_notifications Notifications
     * @subsection bx_notifications-page_blocks Page Blocks
     * @subsubsection bx_notifications-get_block_view get_block_view
     * 
     * @code bx_srv('bx_notifications', 'get_block_view', [...]); @endcode
     * 
     * Get View block for a separate page. Will return a block with "Empty" message if nothing found.
     *
     * @return an array describing a block to display on the site. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxNtfsModule::serviceGetBlockView
     */
    /** 
     * @ref bx_notifications-get_block_view "get_block_view"
     */
    public function serviceGetBlockView($sType = '', $iStart = -1, $iPerPage = -1, $aModules = array())
    {
    	$aBrowseTypes = array(BX_NTFS_TYPE_OBJECT_OWNER_AND_CONNECTIONS, BX_BASE_MOD_NTFS_TYPE_CONNECTIONS, BX_BASE_MOD_NTFS_TYPE_OBJECT_OWNER);

    	if(empty($sType)) {
    		$mixedType = bx_get('type');
    		if($mixedType !== false && in_array($mixedType, $aBrowseTypes))
    			$sType = $mixedType;

	    	if(empty($sType))
    			$sType = BX_NTFS_TYPE_DEFAULT;
    	}

    	$iOwnerId = $this->getUserId();
        if(!$iOwnerId)
            return array('content' => MsgBox(_t('_bx_ntfs_txt_msg_no_results')));

		$aParams = $this->_prepareParams($sType, $iOwnerId, $iStart, $iPerPage, $aModules);
		$sContent = $this->_oTemplate->getViewBlock($aParams);

		$aParams['browse'] = 'first';
    	$aEvent = $this->_oDb->getEvents($aParams);
    	if(!empty($aEvent))
			$this->_oDb->markAsRead($iOwnerId, $aEvent['id']);

		$sModule = $this->_oConfig->getName();
		$sJsObject = $this->_oConfig->getJsObject('view');

        return array('content' => $sContent); 
    }

    /**
     * @page service Service Calls
     * @section bx_notifications Notifications
     * @subsection bx_notifications-browsing Browsing
     * @subsubsection bx_notifications-get_event_by_id get_event_by_id
     * 
     * @code bx_srv('bx_notifications', 'get_event_by_id', [...]); @endcode
     * 
     * Get event's data by event ID.
     *
     * @return an array with event's data.
     * 
     * @see BxNtfsModule::serviceGetEventById
     */
    /** 
     * @ref bx_notifications-get_event_by_id "get_event_by_id"
     */
    public function serviceGetEventById($iId)
    {
        $aParams = array(
            'browse' => 'id',
            'value' => $iId,
        );
    	return $this->_oDb->getEvents($aParams);
    }

    /**
     * @page service Service Calls
     * @section bx_notifications Notifications
     * @subsection bx_notifications-other Other
     * @subsubsection bx_notifications-get_notifications get_notifications
     * 
     * @code bx_srv('bx_notifications', 'get_notifications', [...]); @endcode
     * 
     * Get notifications by params.
     *
     * @return array with notifications.
     * 
     * @see BxNtfsModule::serviceGetNotifications
     */
    /** 
     * @ref bx_notifications-get_notifications "get_notifications"
     */
    public function serviceGetNotifications($iOwnerId = 0, $aBrowseParams = array())
    {
    	if(!$iOwnerId)
			$iOwnerId = $this->getUserId();

		if(!$iOwnerId)
			return 0;

		$aParams = $this->_prepareParams(BX_NTFS_TYPE_DEFAULT, $iOwnerId);
		if(!empty($aBrowseParams) && is_array($aBrowseParams))
		    $aParams = array_merge($aParams, $aBrowseParams);

		list($aEvent) = $this->_oDb->getEvents($aParams, true);
		return $aEvent;
    }

    /**
     * @page service Service Calls
     * @section bx_notifications Notifications
     * @subsection bx_notifications-other Other
     * @subsubsection bx_notifications-get_unread_notifications get_unread_notifications
     * 
     * @code bx_srv('bx_notifications', 'get_unread_notifications', [...]); @endcode
     * 
     * Get unread notifications.
     *
     * @return array with unread notifications.
     * 
     * @see BxNtfsModule::serviceGetUnreadNotifications
     */
    /** 
     * @ref bx_notifications-get_unread_notifications "get_unread_notifications"
     */
    public function serviceGetUnreadNotifications($iOwnerId = 0)
    {
		return $this->serviceGetNotifications($iOwnerId, array('new' => 1));
    }

    /**
     * @page service Service Calls
     * @section bx_notifications Notifications
     * @subsection bx_notifications-other Other
     * @subsubsection bx_notifications-get_unread_notifications_num get_unread_notifications_num
     * 
     * @code bx_srv('bx_notifications', 'get_unread_notifications_num', [...]); @endcode
     * 
     * Get number of unread notifications.
     *
     * @return integer value with number of unread notifications.
     * 
     * @see BxNtfsModule::serviceGetUnreadNotificationsNum
     */
    /** 
     * @ref bx_notifications-get_unread_notifications_num "get_unread_notifications_num"
     */
    public function serviceGetUnreadNotificationsNum($iOwnerId = 0)
    {
        if(!$iOwnerId)
            $iOwnerId = $this->getUserId();

        if(!$iOwnerId)
            return 0;

        $aParams = $this->_prepareParams(BX_NTFS_TYPE_DEFAULT, $iOwnerId);
        $aParams = array_merge($aParams, array(
            'new' => 1,
            'count_only' => 1
        ));

        return (int)$this->_oDb->getEvents($aParams);
    }

    /**
     * @page service Service Calls
     * @section bx_notifications Notifications
     * @subsection bx_notifications-other Other
     * @subsubsection bx_notifications-get_live_updates get_live_updates
     * 
     * @code bx_srv('bx_notifications', 'get_live_updates', [...]); @endcode
     * 
     * Get data for Live Updates system.
     *
     * @return an array with special format.
     * 
     * @see BxNtfsModule::serviceGetLiveUpdates
     */
    /** 
     * @ref bx_notifications-get_live_updates "get_live_updates"
     */
    public function serviceGetLiveUpdates($aMenuItemParent, $aMenuItemChild, $iCount = 0)
    {
        $iOwnerId = $this->getUserId();
        $iCountNew = $this->serviceGetUnreadNotificationsNum($iOwnerId);
        if($iCountNew == $iCount)
			return false;

        return array(
    		'count' => $iCountNew, // required
    		'method' => 'bx_menu_show_live_update(oData)', // required
    		'data' => array(
    			'code' => BxDolTemplate::getInstance()->parseHtmlByTemplateName('menu_item_addon', array(
    				'content' => '{count}'
                )),
                'mi_parent' => $aMenuItemParent,
                'mi_child' => $aMenuItemChild
    		),  // optional, may have some additional data to be passed in JS method provided using 'method' param above.
    	);
    }

    /*
     * COMMON METHODS
     */
    public function getBlockSettings($sDeliveryType, $aParams = array())
    {
        return $this->_oTemplate->getSettingsBlock($sDeliveryType, $aParams);
    }

    public function setSubmenu($sSelected)
    {
    	$oSubmenuSystem = BxDolMenu::getObjectInstance('sys_site_submenu');
        if(!$oSubmenuSystem)
            return;

        $CNF = &$this->_oConfig->CNF;

        $oSubmenuSystem->setObjectSubmenu($CNF['OBJECT_MENU_SUBMENU'], array (
            'title' => _t('_bx_ntfs'),
            'link' => BX_DOL_URL_ROOT . BxDolPermalinks::getInstance()->permalink($CNF['URL_HOME']),
            'icon' => '',
        ));

        $oSubmenuModule = BxDolMenu::getObjectInstance($CNF['OBJECT_MENU_SUBMENU']);
        if($oSubmenuModule)
            $oSubmenuModule->setSelected($this->_oConfig->getName(), $sSelected);
    }

    public function onPost($iId)
    {
    	//--- Event -> Post for Alerts Engine ---//
        $oAlert = new BxDolAlerts($this->_oConfig->getObject('alert'), 'post', $iId);
        $oAlert->alert();
        //--- Event -> Post for Alerts Engine ---//
    }

    public function enableSettingsLike($iId, $bValue, $bAdministration = false)
    {
        $aSetting = $this->_oDb->getSetting(array(
            'by' => $bAdministration ? 'id' : 'tsu_id', 
            'id' => (int)$iId
        ));
        if(empty($aSetting) || !is_array($aSetting))
            return false;

        $aSettingsIds = $this->_oDb->getSetting(array(
            'by' => 'group_type_delivery', 
            'group' => $aSetting['group'], 
            'delivery' => $aSetting['delivery'], 
            'type' => $aSetting['type'], 
            'active' => !$bAdministration
        ));
        if(empty($aSettingsIds) || !is_array($aSettingsIds))
            return false;

        $iUserId = bx_get_logged_profile_id();

        $mixedResult = false;
        if($bAdministration)
            $mixedResult =  $this->_oDb->activateSettingById($bValue, $aSettingsIds);
        else
            $mixedResult = $this->_oDb->activateSettingByIdUser($bValue, $iUserId, $aSettingsIds);

        return $mixedResult;
    }

    protected function _prepareParams($sType = '', $iOwnerId = 0, $iStart = -1, $iPerPage = -1, $aModules = array())
    {
        $aParams = array();
        $aParams['browse'] = 'list';
        $aParams['type'] = !empty($sType) ? $sType : BX_NTFS_TYPE_DEFAULT;
        $aParams['owner_id'] = (int)$iOwnerId != 0 ? $iOwnerId : $this->getUserId();
        $aParams['start'] = (int)$iStart > 0 ? $iStart : 0;
        $aParams['per_page'] = (int)$iPerPage > 0 ? $iPerPage : $this->_oConfig->getPerPage();
        $aParams['modules'] = is_array($aModules) && !empty($aModules) ? $aModules : array();
        $aParams['last_read'] = $this->_oDb->getLastRead($aParams['owner_id']);
        $aParams['active'] = 1;

        return $aParams;
    }

	protected function _prepareParamsGet()
    {
        $aParams = array();
        $aParams['browse'] = 'list';

        $sType = bx_get('type');
        $aParams['type'] = $sType !== false ? bx_process_input($sType, BX_DATA_TEXT) : BX_NTFS_TYPE_DEFAULT;

        $aParams['owner_id'] = $sType !== false ? bx_process_input(bx_get('owner_id'), BX_DATA_INT) : $this->getUserId();

        $iStart = bx_get('start');
        $aParams['start'] = $iStart !== false ? bx_process_input($iStart, BX_DATA_INT) : 0;

        $iPerPage = bx_get('per_page');
        $aParams['per_page'] = $iPerPage !== false ? bx_process_input($iPerPage, BX_DATA_INT) : $this->_oConfig->getPerPage();

        $aModules = bx_get('modules');
        $aParams['modules'] = $aModules !== false ? bx_process_input($aModules, BX_DATA_TEXT) : array();

        $aParams['active'] = 1;

        return $aParams;
    }
}

/** @} */
