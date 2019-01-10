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

/**
 * Group profile forms functions
 */
class BxBaseModGroupsFormsEntryHelper extends BxBaseModProfileFormsEntryHelper
{
    public function __construct($oModule)
    {
        parent::__construct($oModule);
    }

    protected function _processPermissionsCheckForViewDataForm ($aContentInfo, $oProfile)
    {
        $sMsg = parent::_processPermissionsCheckForViewDataForm ($aContentInfo, $oProfile);

        $oPrivacy = BxDolPrivacy::getObjectInstance($this->_oModule->_oConfig->CNF['OBJECT_PRIVACY_VIEW']);
        if ($sMsg && $oPrivacy->isPartiallyVisible($aContentInfo[$this->_oModule->_oConfig->CNF['FIELD_ALLOW_VIEW_TO']]))
            return '';

        return $sMsg;
    }

    public function onDataAddAfter ($iAccountId, $iContentId)
    {
        if ($s = parent::onDataAddAfter($iAccountId, $iContentId))
            return $s;

        if (!($oGroupProfile = BxDolProfile::getInstanceByContentAndType($iContentId, $this->_oModule->_oConfig->getName())))
            return '';

        $this->makeAuthorAdmin ($oGroupProfile, bx_get('initial_members'));

        $this->inviteMembers ($oGroupProfile, bx_get('initial_members'));
        
        return '';
    }

    public function onDataEditAfter ($iContentId, $aContentInfo, $aTrackTextFieldsChanges, $oProfile, $oForm)
    {
        if ($s = parent::onDataEditAfter($iContentId, $aContentInfo, $aTrackTextFieldsChanges, $oProfile, $oForm))
            return $s;

        if (!($oGroupProfile = BxDolProfile::getInstanceByContentAndType($iContentId, $this->_oModule->_oConfig->getName())))
            return ''; 

        $this->inviteMembers ($oGroupProfile, bx_get('initial_members'));

        return '';
    }
    
    public function onDataDeleteAfter ($iContentId, $aContentInfo, $oProfile)
    {
        $CNF = &$this->_oModule->_oConfig->CNF;
        $oGroupProfile = BxDolProfile::getInstanceByContentAndType($iContentId, $this->_oModule->_oConfig->getName());

        if ($oGroupProfile && isset($CNF['TABLE_ADMINS']))
            $this->_oModule->_oDb->deleteAdminsByGroupId($oGroupProfile->id());

        if (isset($CNF['OBJECT_CONNECTIONS']) && $oGroupProfile && ($oConnection = BxDolConnection::getObjectInstance($CNF['OBJECT_CONNECTIONS'])))
            $oConnection->onDeleteInitiatorAndContent($oGroupProfile->id());

        return '';
    }

    protected function inviteMembers ($oGroupProfile, $aInitialProfiles)
    {
        if (!$aInitialProfiles)
            return;

        // insert invited members, so they will join without confirmation
        foreach ($aInitialProfiles as $iProfileId) {
            if (!($oProfile = BxDolProfile::getInstance($iProfileId)))
                continue;
            $this->_oModule->serviceAddMutualConnection ($oGroupProfile->id(), $oProfile->id(), true);            
        }
    }

    /**
     * Make author admin if their is in initial invitations list
     * @param $oGroupProfile group id
     * @param $aInitialProfiles array of initial profile ids
     * @return nothing
     */ 
    protected function makeAuthorAdmin ($oGroupProfile, $aInitialProfiles)
    {
        $CNF = &$this->_oModule->_oConfig->CNF;
        $iAdminProfileId = bx_get_logged_profile_id();

        if (!isset($CNF['OBJECT_CONNECTIONS']) || !$CNF['OBJECT_CONNECTIONS'] || !is_array($aInitialProfiles) || !in_array($iAdminProfileId, $aInitialProfiles))
            return;

        if (!($oConnection = BxDolConnection::getObjectInstance($CNF['OBJECT_CONNECTIONS'])))
            return;

        if (!$oConnection->isConnected($oGroupProfile->id(), (int)$iAdminProfileId))
            $oConnection->addConnection($oGroupProfile->id(), (int)$iAdminProfileId);
        if (!$oConnection->isConnected((int)$iAdminProfileId, $oGroupProfile->id()))
            $oConnection->addConnection((int)$iAdminProfileId, $oGroupProfile->id());

        if (!$this->_oModule->_oDb->isAdmin ($oGroupProfile->id(), $iAdminProfileId))
            $this->_oModule->_oDb->toAdmins ($oGroupProfile->id(), $iAdminProfileId);
    }
}

/** @} */
