<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    BaseProfile Base classes for profile modules
 * @ingroup     DolphinModules
 *
 * @{
 */

bx_import ('BxBaseModGeneralModule');
bx_import ('BxDolAcl');

/**
 * Base class for profile modules.
 */
class BxBaseModProfileModule extends BxBaseModGeneralModule implements iBxDolProfileService
{
    protected $_iAccountId;

    function __construct(&$aModule)
    {
        parent::__construct($aModule);
        $this->_iAccountId = getLoggedId();
    }

    // ====== SERVICE METHODS
	public function serviceGetMenuAddonManageTools()
	{
		bx_import('SearchResult', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SearchResult';
        $o = new $sClass();
        $o->fillFilters(array(
			'perofileStatus' => BX_PROFILE_STATUS_PENDING
        ));
        $o->unsetPaginate();

        return $o->getNum();
	}

	public function serviceGetMenuAddonManageToolsProfileStats()
	{
		bx_import('SearchResult', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SearchResult';
        $o = new $sClass();
        $o->fillFilters(array(
			'account_id' => getLoggedId(),
        	'perofileStatus' => ''
        ));
        $o->unsetPaginate();

        return $o->getNum();
	}

    public function serviceGetMenuSetNameForMenuTrigger ($sMenuTriggerName)
    {
        if ('trigger_profile_view_submenu' == $sMenuTriggerName)
            return $this->_oConfig->CNF['OBJECT_MENU_SUBMENU_VIEW_ENTRY'];
        elseif ('trigger_profile_view_actions' == $sMenuTriggerName)
            return $this->_oConfig->CNF['OBJECT_MENU_ACTIONS_VIEW_ENTRY'];
        return '';
    }

	public function serviceGetPageObjectForPageTrigger ($sPageTriggerName)
    {
        if(isset($this->_oConfig->CNF['TRIGGER_PAGE_VIEW_ENTRY']) && $this->_oConfig->CNF['TRIGGER_PAGE_VIEW_ENTRY'] == $sPageTriggerName)
        	return $this->_oConfig->CNF['OBJECT_PAGE_VIEW_ENTRY'];

        return '';
    }

    public function serviceProfilesSearch ($sTerm, $iLimit)
    {
        $aRet = array();
        $a = $this->_oDb->searchByTerm($sTerm, $iLimit);
        foreach ($a as $r)
            $aRet[] = array ('label' => $this->serviceProfileName($r['content_id']), 'value' => $r['profile_id']);
        return $aRet;
    }

    public function serviceProfileUnit ($iContentId)
    {
        return $this->_serviceTemplateFunc('unit', $iContentId);
    }

    public function serviceProfileAvatar ($iContentId)
    {
        return $this->_serviceTemplateFunc('urlAvatar', $iContentId);
    }

    public function serviceProfileEditUrl ($iContentId)
    {
        bx_import('BxDolPermalinks');
        return BX_DOL_URL_ROOT . BxDolPermalinks::getInstance()->permalink('page.php?i=' . $this->_oConfig->CNF['URI_EDIT_ENTRY'] . '&id=' . $iContentId);
    }

    public function serviceProfileThumb ($iContentId)
    {
        return $this->_serviceTemplateFunc('thumb', $iContentId);
    }

    public function serviceProfileIcon ($iContentId)
    {
        return $this->_serviceTemplateFunc('icon', $iContentId);
    }

    public function serviceProfileName ($iContentId)
    {
        if (!$iContentId)
            return false;
        $aContentInfo = $this->_oDb->getContentInfoById($iContentId);
        if (!$aContentInfo)
            return false;
        return $aContentInfo[$this->_oConfig->CNF['FIELD_NAME']];
    }

    public function serviceProfileUrl ($iContentId)
    {
        if (!$iContentId)
            return false;
        $aContentInfo = $this->_oDb->getContentInfoById($iContentId);
        if (!$aContentInfo)
            return false;
        $CNF = $this->_oConfig->CNF;
        bx_import('BxDolPermalinks');
        return BX_DOL_URL_ROOT . BxDolPermalinks::getInstance()->permalink('page.php?i=' . $CNF['URI_VIEW_ENTRY'] . '&id=' . $aContentInfo[$CNF['FIELD_ID']]);
    }

    public function serviceBrowseRecentProfiles ($bDisplayEmptyMsg = false)
    {
        return $this->_serviceBrowse ('recent', false, BX_DB_PADDING_DEF, $bDisplayEmptyMsg);
    }

    public function serviceBrowseConnections ($iProfileId, $sObjectConnections = 'sys_profiles_friends', $sConnectionsType = 'content', $iMutual = false, $iDesignBox = BX_DB_PADDING_DEF, $iProfileId2 = 0)
    {
        return $this->_serviceBrowse (
            'connections',
            array(
                'object' => $sObjectConnections,
                'type' => $sConnectionsType,
                'mutual' => $iMutual,
                'profile' => (int)$iProfileId,
                'profile2' => (int)$iProfileId2),
            $iDesignBox
        );
    }

    public function serviceBrowseConnectionsQuick ($iProfileId, $sObjectConnections = 'sys_profiles_friends', $sConnectionsType = 'content', $iMutual = false, $iProfileId2 = 0)
    {
        // get connections object
        bx_import('BxDolConnection');
        $oConnection = BxDolConnection::getObjectInstance($sObjectConnections);
        if (!$oConnection)
            return '';

        // set some vars
        $iLimit = empty($this->_oConfig->CNF['PARAM_NUM_CONNECTIONS_QUICK']) ? 4 : getParam($this->_oConfig->CNF['PARAM_NUM_CONNECTIONS_QUICK']);
        if (!$iLimit)
            $iLimit = 4;
        $iStart = (int)bx_get('start');

        // get connections array
        $a = $oConnection->getConnectionsAsArray ($sConnectionsType, $iProfileId, $iProfileId2, $iMutual, (int)bx_get('start'), $iLimit + 1, BX_CONNECTIONS_ORDER_ADDED_DESC);
        if (!$a)
            return '';

        // get paginate object
        bx_import('BxTemplPaginate');
        $oPaginate = new BxTemplPaginate(array(
            'on_change_page' => "return !loadDynamicBlockAutoPaginate(this, '{start}', '{per_page}');",
            'num' => count($a),
            'per_page' => $iLimit,
            'start' => $iStart,
        ));

        // remove last item from connection array, because we've got one more item for pagination calculations only
        if (count($a) > $iLimit)
            array_pop($a);

        // get profiles HTML
        bx_import('BxDolProfile');
        $s = '';
        foreach ($a as $iProfileId) {
            if (!($o = BxDolProfile::getInstance($iProfileId))) {
                continue;
            }
            $s .= $o->getUnit();
        }

        // return profiles + paginate
        return $s . (!$iStart && $oPaginate->getNum() <= $iLimit ?  '' : $oPaginate->getSimplePaginate());
    }

    public function serviceEntityEditCover ($iContentId = 0)
    {
        return $this->_serviceEntityForm ('editDataForm', $iContentId, $this->_oConfig->CNF['OBJECT_FORM_ENTRY_DISPLAY_EDIT_COVER']);
    }

    public function serviceProfileCover ($iContentId = 0)
    {
       if (!$iContentId)
            $iContentId = bx_process_input(bx_get('id'), BX_DATA_INT);
        if (!$iContentId)
            return false;

        $aContentInfo = $this->_oDb->getContentInfoById($iContentId);
        if (!$aContentInfo)
            return false;

        return $this->_oTemplate->cover($aContentInfo);
    }

    public function serviceProfileFriends ($iContentId = 0)
    {
        if (!$iContentId)
            $iContentId = bx_process_input(bx_get('id'), BX_DATA_INT);
        if (!$iContentId)
            return false;

        $aContentInfo = $this->_oDb->getContentInfoById($iContentId);
        if (!$aContentInfo)
            return false;

        bx_import('BxDolConnection');
        $s = $this->serviceBrowseConnectionsQuick ($aContentInfo['profile_id'], 'sys_profiles_friends', BX_CONNECTIONS_CONTENT_TYPE_CONTENT, true);
        if (!$s)
            return MsgBox(_t('_sys_txt_empty'));
        return $s;
    }

    // ====== PERMISSION METHODS

    /**
     * @return CHECK_ACTION_RESULT_ALLOWED if access is granted or error message if access is forbidden.
     */
    public function checkAllowedEdit ($aDataEntry, $isPerformAction = false)
    {
        // moderator always has access
        if ($this->_isModerator($isPerformAction))
            return CHECK_ACTION_RESULT_ALLOWED;

        // owner (checked by account! not as profile as ususal) always have access
        bx_import('BxDolProfile');
        $oProfile = BxDolProfile::getInstanceByContentAndType($aDataEntry[$this->_oConfig->CNF['FIELD_ID']], $this->_aModule['name']);
        if (!$oProfile)
            return _t('_sys_txt_error_occured');

        if ($oProfile->getAccountId() == $this->_iAccountId)
            return CHECK_ACTION_RESULT_ALLOWED;

        return _t('_sys_txt_access_denied');
    }

    /**
     * Check if user can change cover image
     */
    public function checkAllowedChangeCover ($aDataEntry, $isPerformAction = false)
    {
        // moderator always has access
        if ($this->_isModerator($isPerformAction))
            return CHECK_ACTION_RESULT_ALLOWED;

        // owner (checked by account! not as profile as ususal) always have access
        bx_import('BxDolProfile');
        $oProfile = BxDolProfile::getInstanceByContentAndType($aDataEntry[$this->_oConfig->CNF['FIELD_ID']], $this->_aModule['name']);
        if (!$oProfile)
            return _t('_sys_txt_error_occured');

        if ($oProfile->getAccountId() == $this->_iAccountId)
            return CHECK_ACTION_RESULT_ALLOWED;

        return _t('_sys_txt_access_denied');
    }

    /**
     * @return CHECK_ACTION_RESULT_ALLOWED if access is granted or error message if access is forbidden.
     */
    public function checkAllowedDelete (&$aDataEntry, $isPerformAction = false)
    {
        // moderator always has access
        if ($this->_isModerator($isPerformAction))
            return CHECK_ACTION_RESULT_ALLOWED;

        // check ACL and owner (checked by account! not as profile as ususal)
        $aCheck = checkActionModule($this->_iProfileId, 'delete entry', $this->getName(), $isPerformAction);

        bx_import('BxDolProfile');
        $oProfile = BxDolProfile::getInstanceByContentAndType($aDataEntry[$this->_oConfig->CNF['FIELD_ID']], $this->_aModule['name']);
        if (!$oProfile)
            return _t('_sys_txt_error_occured');

        if ($oProfile->getAccountId() == $this->_iAccountId && $aCheck[CHECK_ACTION_RESULT] === CHECK_ACTION_RESULT_ALLOWED)
            return CHECK_ACTION_RESULT_ALLOWED;

        return _t('_sys_txt_access_denied');
    }

    /**
     * @return CHECK_ACTION_RESULT_ALLOWED if access is granted or error message if access is forbidden.
     */
    public function checkAllowedViewMoreMenu (&$aDataEntry, $isPerformAction = false)
    {
        bx_import('BxTemplMenu');
        $oMenu = BxTemplMenu::getObjectInstance($this->_oConfig->CNF['OBJECT_MENU_ACTIONS_VIEW_ENTRY_MORE']);
        if (!$oMenu || !$oMenu->getCode())
            return _t('_sys_txt_access_denied');
        return CHECK_ACTION_RESULT_ALLOWED;
    }

    /**
     * @return CHECK_ACTION_RESULT_ALLOWED if access is granted or error message if access is forbidden.
     */
    public function checkAllowedFriendAdd (&$aDataEntry, $isPerformAction = false)
    {
        return $this->_checkAllowedConnect ($aDataEntry, $isPerformAction, 'sys_profiles_friends', true, false);
    }

    /**
     * @return CHECK_ACTION_RESULT_ALLOWED if access is granted or error message if access is forbidden.
     */
    public function checkAllowedFriendRemove (&$aDataEntry, $isPerformAction = false)
    {
        if (CHECK_ACTION_RESULT_ALLOWED === $this->_checkAllowedConnect ($aDataEntry, $isPerformAction, 'sys_profiles_friends', false, true, true))
            return CHECK_ACTION_RESULT_ALLOWED;
        return $this->_checkAllowedConnect ($aDataEntry, $isPerformAction, 'sys_profiles_friends', false, true, false);
    }

    /**
     * @return CHECK_ACTION_RESULT_ALLOWED if access is granted or error message if access is forbidden.
     */
    public function checkAllowedSubscribeAdd (&$aDataEntry, $isPerformAction = false)
    {
        return $this->_checkAllowedConnect ($aDataEntry, $isPerformAction, 'sys_profiles_subscriptions', false, false);
    }

    /**
     * @return CHECK_ACTION_RESULT_ALLOWED if access is granted or error message if access is forbidden.
     */
    public function checkAllowedSubscribeRemove (&$aDataEntry, $isPerformAction = false)
    {
        return $this->_checkAllowedConnect ($aDataEntry, $isPerformAction, 'sys_profiles_subscriptions', false, true);
    }

    // ====== PROTECTED METHODS

    protected function _checkAllowedConnect (&$aDataEntry, $isPerformAction, $sObjConnection, $isMutual, $isInvertResult, $isSwap = false)
    {
        if (!$this->_iProfileId)
            return _t('_sys_txt_access_denied');

        $CNF = &$this->_oConfig->CNF;

        $oProfileAuthor = BxDolProfile::getInstance($aDataEntry[$CNF['FIELD_AUTHOR']]);
        $oProfile = $oProfileAuthor ? BxDolProfile::getInstanceByContentTypeAccount($aDataEntry[$CNF['FIELD_ID']], $this->_aModule['name'], $oProfileAuthor->getAccountId()) : false;
        if (!$oProfile || $oProfile->id() == $this->_iProfileId)
            return _t('_sys_txt_access_denied');

        bx_import('BxDolConnection');
        $oConn = BxDolConnection::getObjectInstance($sObjConnection);
        if ($isSwap)
            $isConnected = $oConn->isConnected($oProfile->id(), $this->_iProfileId, $isMutual);
        else
            $isConnected = $oConn->isConnected($this->_iProfileId, $oProfile->id(), $isMutual);

        if ($isInvertResult)
            $isConnected = !$isConnected;

        return $isConnected ? _t('_sys_txt_access_denied') : CHECK_ACTION_RESULT_ALLOWED;
    }

    protected function _buildRssParams($sMode, $aArgs)
    {
        $aParams = array ();
        $sMode = bx_process_input($sMode);
        switch ($sMode) {
            case 'connections':
                $aParams = array(
                    'object' => isset($aArgs[0]) ? $aArgs[0] : '',
                    'type' => isset($aArgs[1]) ? $aArgs[1] : '',
                    'profile' => isset($aArgs[2]) ? (int)$aArgs[2] : 0,
                    'mutual' => isset($aArgs[3]) ? (int)$aArgs[3] : 0,
                    'profile2' => isset($aArgs[4]) ? (int)$aArgs[4] : 0,
                );
                break;
        }

        return $aParams;
    }
}

/** @} */
