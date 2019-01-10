<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    BaseGroups Base classes for groups modules
 * @ingroup     UnaModules
 *
 * @{
 */

class BxBaseModGroupsMenuSnippetMeta extends BxBaseModProfileMenuSnippetMeta
{
    public function __construct($aObject, $oTemplate = false)
    {
        parent::__construct($aObject, $oTemplate);

        $CNF = &$this->_oModule->_oConfig->CNF;
        if (isset($CNF['OBJECT_CONNECTIONS'])){
            $this->_aConnectionToFunctionCheck[$CNF['OBJECT_CONNECTIONS']] = array(
			    'add' => 'checkAllowedFanAdd', 
			    'remove' => 'checkAllowedFanRemove'
            );

            $this->_aConnectionToFunctionTitle[$CNF['OBJECT_CONNECTIONS']] = '_getMenuItemConnectionsTitle';
        }
    }

    protected function _getMenuItemJoin($aItem)
    {
        if (isset($CNF['OBJECT_CONNECTIONS']))
            return $this->_getMenuItemConnection($this->_oModule->_oConfig->CNF['OBJECT_CONNECTIONS'], 'add', $aItem);
        return false;
    }

    protected function _getMenuItemLeave($aItem)
    {
        return $this->_getMenuItemConnection($this->_oModule->_oConfig->CNF['OBJECT_CONNECTIONS'], 'remove', $aItem);
    }

    protected function _getMenuItemMembers($aItem)
    {
        $CNF = &$this->_oModule->_oConfig->CNF;

        if(!$this->_bContentPublic || !$this->_oContentProfile || empty($CNF['OBJECT_CONNECTIONS']))
            return false;

        $oConnection = BxDolConnection::getObjectInstance($CNF['OBJECT_CONNECTIONS']);
        if(!$oConnection)
            return false;

        $iFriends = $oConnection->getConnectedInitiatorsCount($this->_oContentProfile->id(), true);
        if(!$iFriends)
            return false;

        return $this->_oTemplate->getUnitMetaItemText(_t('_sys_menu_item_title_sm_members', $iFriends));
    }

    protected function _getMenuItemConnectionsTitle($sAction, &$oConnection)
    {
        $iProfile = bx_get_logged_profile_id();
        $iContentProfile = $this->_oContentProfile->id();

        $aResult = array();
        if($oConnection->isConnectedNotMutual($iProfile, $iContentProfile))
            $aResult = array(
                'add' => '',
                'remove' => _t('_sys_menu_item_title_sm_leave_cancel'),
            );
        else if($oConnection->isConnectedNotMutual($iContentProfile, $iProfile))
            $aResult = array(
                'add' => _t('_sys_menu_item_title_sm_join_confirm'),
                'remove' => _t('_sys_menu_item_title_sm_leave_reject'),
            );
        else if($oConnection->isConnected($iProfile, $iContentProfile, true))
            $aResult = array(
                'add' => '',
                'remove' => _t('_sys_menu_item_title_sm_leave'),
            );
        else
            $aResult = array(
                'add' => _t('_sys_menu_item_title_sm_join'),
                'remove' => '',
            );

        return !empty($sAction) && isset($aResult[$sAction]) ? $aResult[$sAction] : $aResult;
    }

    protected function _getMenuItemCountry($aItem)
    {
        return $this->_getMenuItemLocation($aItem, true);
    }

    protected function _getMenuItemCountryCity($aItem)
    {
        return $this->_getMenuItemLocation($aItem, false);
    }

    protected function _getMenuItemLocation($aItem, $bCountryOnly = false)
    {
        $CNF = &$this->_oModule->_oConfig->CNF;

        if(!$this->_bContentPublic || !$this->_oContentProfile || empty($CNF['OBJECT_CONNECTIONS']))
            return false;

        $oMeta = BxDolMetatags::getObjectInstance($CNF['OBJECT_METATAGS']);
        if(!$oMeta)
            return false;

        if (!($sLocation = $oMeta->locationsString($this->_iContentId, false, $bCountryOnly ? array('country_only' => 1) : array('country_city_only' => 1))))
            return false;

        return $this->_oTemplate->getUnitMetaItemText($sLocation);
    }
}

/** @} */
