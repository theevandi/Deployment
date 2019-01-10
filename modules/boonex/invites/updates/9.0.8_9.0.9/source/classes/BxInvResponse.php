<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Invites Invites
 * @ingroup     UnaModules
 *
 * @{
 */

class BxInvResponse extends BxDolAlertsResponse
{
    protected $_sModule;
    protected $_oModule;

    public function __construct()
    {
        parent::__construct();

        $this->_sModule = 'bx_invites';
        $this->_oModule = BxDolModule::getInstance($this->_sModule);
    }

    /**
     * Overwtire the method of parent class.
     *
     * @param BxDolAlerts $oAlert an instance of alert.
     */
    public function response($oAlert)
    {
        $sMethod = '_process' . bx_gen_method_name($oAlert->sUnit . '_' . $oAlert->sAction);
        if(!method_exists($this, $sMethod))
            return;

        return $this->$sMethod($oAlert);
    }

    protected function _processAccountAddForm($oAlert)
    {
        $sCode = $this->_oModule->serviceAccountAddFormCheck();
        if($sCode)
            $oAlert->aExtras['form_code'] = $sCode;
    }

    protected function _processAccountAdded($oAlert)
    {
        if(!$this->_oModule->_oConfig->isRegistrationByInvitation())
            return;

        $sKeyCode = $this->_oModule->_oConfig->getKeyCode();
        $sKey = BxDolSession::getInstance()->getUnsetValue($sKeyCode);
        if($sKey === false)
            return;
        $this->_oModule->attachAccountIdToInvite($oAlert->iObject, $sKey);
        
        $sKeysToRemove = $this->_oModule->_oDb->getInvites(array('type' => 'invites_code_by_single', 'value' => $sKey));
        $aKeysToRemove = explode(',', $sKeysToRemove);
        $oKeys = BxDolKey::getInstance();
        if($oKeys){
            foreach($aKeysToRemove as $sKeyToRemove) {
                if($oKeys->isKeyExists($sKeyToRemove))
                    $oKeys->removeKey($sKeyToRemove);
            }
        }
        
        return;
    }

    protected function _processProfileDelete($oAlert)
    {
        $this->_oModule->_oDb->deleteInvites(array('profile_id' => $oAlert->iObject));
    }
    
    protected function _processAccountDelete($oAlert)
    {
        $this->_oModule->_oDb->deleteInvitesByAccount(array('joined_account_id' => $oAlert->iObject));
    }
}

/** @} */
