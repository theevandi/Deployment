<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaCore UNA Core
 * @{
 */

bx_import('BxDolAcl');

class BxDolAccount extends BxDolFactory implements iBxDolSingleton
{
    protected $_iAccountID;
    protected $_aInfo;
    protected $_oQuery;

    /**
     * Constructor
     */
    protected function __construct ($iAccountId)
    {
        $iAccountId = (int)$iAccountId;
        $sClass = get_class($this) . '_' . $iAccountId;
        if (isset($GLOBALS['bxDolClasses'][$sClass]))
            trigger_error ('Multiple instances are not allowed for the class: ' . get_class($this), E_USER_ERROR);

        parent::__construct();

        $this->_iAccountID = $iAccountId; // since constructor is protected $iAccountId is always valid
        $this->_oQuery = BxDolAccountQuery::getInstance();
    }

    /**
     * Prevent cloning the instance
     */
    public function __clone()
    {
        $sClass = get_class($this) . '_' . $this->_iProfileID;
        if (isset($GLOBALS['bxDolClasses'][$sClass]))
            trigger_error('Clone is not allowed for the class: ' . get_class($this), E_USER_ERROR);
    }

    /**
     * Get singleton instance of the class
     */
    public static function getInstance($mixedAccountId = false)
    {
        if (!$mixedAccountId)
            $mixedAccountId = getLoggedId();

        $iAccountId = self::getID($mixedAccountId);
        if (!$iAccountId)
            return false;

        $sClass = __CLASS__ . '_' . $iAccountId;
        if(!isset($GLOBALS['bxDolClasses'][$sClass]))
            $GLOBALS['bxDolClasses'][$sClass] = new BxDolAccount($iAccountId);

        return $GLOBALS['bxDolClasses'][$sClass];
    }

    /**
     * Get studio operator account singleton instance on the class
     */
    public static function getInstanceStudioOperator()
    {
        $oQuery = BxDolAccountQuery::getInstance();
        if (!($iId = $oQuery->getStudioOperatorId()))
            return false;

        return self::getInstance($iId);
    }

    /**
     * Get account id
     */
    public function id()
    {
        $a = $this->getInfo($this->_iAccountID);
        return isset($a['id']) ? $a['id'] : false;
    }

    /**
     * Check if account is confirmed, it is checked by email confirmation
     */
    public function isConfirmed($iAccountId = false)
    {
        $sConfirmationType = getParam('sys_account_confirmation_type');
        if ($sConfirmationType == BX_ACCOUNT_CONFIRMATION_NONE) 
            return true;
        
        $a = $this->getInfo((int)$iAccountId);
        
        switch ($sConfirmationType) {
            case BX_ACCOUNT_CONFIRMATION_EMAIL:
                if ($a['email_confirmed'])
                    return true;
                break;
            case BX_ACCOUNT_CONFIRMATION_PHONE:
                if ($a['phone_confirmed'])
                    return true;
                break;
            case BX_ACCOUNT_CONFIRMATION_EMAIL_PHONE:
                if ($a['email_confirmed'] && $a['phone_confirmed'])
                    return true;
                break;
        }
        return false;
    }
    
    public function getCurrentConfirmationStatusValue($iAccountId = false)
    {
        $a = $this->getInfo((int)$iAccountId);
        $sTmp = $a['email_confirmed'] . $a['phone_confirmed'];
        switch ($sTmp) {
            case '01':
                return BX_ACCOUNT_CONFIRMATION_PHONE;
            case '10':
                return BX_ACCOUNT_CONFIRMATION_EMAIL;
            case '11':
                return BX_ACCOUNT_CONFIRMATION_EMAIL_PHONE;
        }
        return BX_ACCOUNT_CONFIRMATION_NONE;
    }
    
    public function isConfirmedEmail($iAccountId = false)
    {
        if (!self::isNeedConfirmEmail())
            return true;
        $a = $this->getInfo((int)$iAccountId);
        return $a['email_confirmed'] ? true : false;
    }
    
    public function isConfirmedPhone($iAccountId = false)
    {
        if (!self::isNeedConfirmPhone())
            return true;
        $a = $this->getInfo((int)$iAccountId);
        return $a['phone_confirmed'] ? true : false;
    }
    
    static public function isNeedConfirmEmail()
    {
        if (getParam('sys_account_confirmation_type') == BX_ACCOUNT_CONFIRMATION_EMAIL || getParam('sys_account_confirmation_type') == BX_ACCOUNT_CONFIRMATION_EMAIL_PHONE) 
            return true;
        return false;
    }
    
    static public function isNeedConfirmPhone()
    {
        if (getParam('sys_account_confirmation_type') == BX_ACCOUNT_CONFIRMATION_PHONE || getParam('sys_account_confirmation_type') == BX_ACCOUNT_CONFIRMATION_EMAIL_PHONE) 
            return true;
        return false;
    }
    
    public function isLocked($iAccountId = false)
    {
        $a = $this->getInfo((int)$iAccountId);
        return $a['locked'] ? true : false;
    }
    
    //

    /**
     * Set account email to confirmed or unconfirmed
     * @param  int  $isConfirmed - false: mark email as unconfirmed, true: as confirmed
     * @param  int  $iAccountId  - optional account id
     * @return true on success or false on error
     */
    public function updateEmailConfirmed($isConfirmed, $isAutoSendConfrmationEmail = true, $iAccountId = false)
    {
        $iId = (int)$iAccountId ? (int)$iAccountId : $this->_iAccountID;

        if (!$isConfirmed && $isAutoSendConfrmationEmail && self::isNeedConfirmEmail()) // if email_confirmation procedure is enabled - send email confirmation letter
            $this->sendConfirmationEmail($iId);

        if ($this->_oQuery->updateEmailConfirmed($isConfirmed, $iId)) {
            $this->_aInfo = false;
            bx_alert('account', $this->isConfirmed() ? 'confirm' : 'unconfirm', $iId);
            return true;
        }
        return false;
    }
    
    /**
     * Set account phone to confirmed or unconfirmed
     * @param  int  $isConfirmed - false: mark phone as unconfirmed, true: as confirmed
     * @param  int  $iAccountId  - optional account id
     * @return true on success or false on error
     */
    public function updatePhoneConfirmed($isConfirmed, $iAccountId = false)
    {
        $iId = (int)$iAccountId ? (int)$iAccountId : $this->_iAccountID;
        
        if ($this->_oQuery->updatePhoneConfirmed($isConfirmed, $iId)) {
            $this->_aInfo = false;
            bx_alert('account', $this->isConfirmed() ? 'confirm' : 'unconfirm', $iId);
            return true;
        }
        return false;
    }
    
    /**
     * Set account phone
     * @param  string  $sPhone - phone number
     * @param  int  $iAccountId  - optional account id
     * @return true on success or false on error
     */
    public function updatePhone($sPhone, $iAccountId = false)
    {
        $iId = (int)$iAccountId ? (int)$iAccountId : $this->_iAccountID;
        if ($this->_oQuery->updatePhone($sPhone, $iId)) {
            bx_alert('account', 'set_phone', $iId);
            return true;
        }
        return false;
    }
    /**
     * Switch context automatically to the first available profile
     * @param $iProfileIdFilterOut profile ID to exclude from the list of possible profiles
     * @param $iAccountId account ID to use istead of current account
     * @return true on success or false on error
     */ 
    public function updateProfileContextAuto($iProfileIdFilterOut = false, $iAccountId = false)
    {
        $oAccount = (!$iAccountId || $iAccountId == $this->_iAccountID ? $this : BxDolAccount::getInstance ($iAccountId));
        if (!$oAccount)
            return false;
        $aAccountInfo = $oAccount->getInfo();
        $aProfiles = $oAccount->getProfiles();
        $oProfileAccount = BxDolProfile::getInstanceAccountProfile($oAccount->id());

        // unset deleted profile and account profile
        if ($iProfileIdFilterOut)
            unset($aProfiles[$iProfileIdFilterOut]);
        unset($aProfiles[$oProfileAccount->id()]);

        if ($aProfiles) {
            // try to use another profile
            reset($aProfiles);
            $iProfileId = key($aProfiles);
        } 
        else {
            // if no profiles exist, use account profile
            $iProfileId = $oProfileAccount->id();
        }

        return $oAccount->updateProfileContext($iProfileId);
    }
    
    public function updateProfileContext($iSwitchToProfileId, $iAccountId = false)
    {
        $iId = (int)$iAccountId ? (int)$iAccountId : $this->_iAccountID;
        $aInfo = $this->getInfo((int)$iId);
        if (!$aInfo)
            return false;

        $ret = null;
        bx_alert('account', 'before_switch_context', $iId, $iSwitchToProfileId, array('profile_id_current' => $aInfo['profile_id'], 'override_result' => &$ret));
        if ($ret !== null)
            return $ret;

        if (!$this->_oQuery->updateCurrentProfile($iId, $iSwitchToProfileId))
            return false;

        $this->_aInfo = false;
            
        bx_alert('account', 'switch_context', $iId, $iSwitchToProfileId, array('profile_id_old' => $aInfo['profile_id']));

        return true;
    }

    /**
     * Send "confirmation" email
     */
    public function sendConfirmationEmail($iAccountId = false)
    {
    	$sName = $this->getDisplayName($iAccountId); 
        $sEmail = $this->getEmail($iAccountId);

        $oKey = BxDolKey::getInstance();
        $sConfirmationCode = $oKey->getNewKey(array('account_id' => $iAccountId));
        $sConfirmationLink = BX_DOL_URL_ROOT . BxDolPermalinks::getInstance()->permalink('page.php?i=confirm-email') . '&code=' . urlencode($sConfirmationCode);

        $aPlus = array(
        	'name' => $sName,
        	'email' => $sEmail,
        	'conf_code' => $sConfirmationCode,
        	'conf_link' => $sConfirmationLink,
        	'conf_form_link' => BX_DOL_URL_ROOT . BxDolPermalinks::getInstance()->permalink('page.php?i=confirm-email')
        );

        $aTemplate = BxDolEmailTemplates::getInstance()->parseTemplate('t_Confirmation', $aPlus);
        return $aTemplate && sendMail($sEmail, $aTemplate['Subject'], $aTemplate['Body'], 0, array(), BX_EMAIL_SYSTEM);
    }

    /**
     * Get account info
     */
    public function getInfo($iAccountId = false)
    {        
        if ($iAccountId && $iAccountId != $this->_iAccountID)
            return $this->_oQuery->getInfoById((int)$iAccountId ? (int)$iAccountId : $this->_iAccountID);

        if ($this->_aInfo)
            return $this->_aInfo;

        $this->_aInfo = $this->_oQuery->getInfoById($this->_iAccountID);
        return $this->_aInfo;
    }

    /**
     * Get account display name
     */
    public function getDisplayName($iAccountId = false)
    {
        $aInfo = $this->getInfo($iAccountId);
        return bx_process_output(!empty($aInfo['name']) ? $aInfo['name'] : _t('_sys_txt_user_n', $aInfo['id']));
    }

    /**
     * Get account url
     */
    public function getUrl($iAccountId = false)
    {
        return 'javascript:void(0);';
    }

    /**
     * Get account url
     */
    public function getUnit($iAccountId = false)
    {
        $sTitle = $this->getDisplayName($iAccountId);
        return BxDolTemplate::getInstance()->parseHtmlByName('unit_account.html', array(
            'color' => implode(', ', BxDolTemplate::getColorCode(($iAccountId ? $iAccountId : $this->_iAccountID), 1.0)),
			'letter' => mb_strtoupper(mb_substr($sTitle, 0, 1))
        ));
    }

    /**
     * Get picture url
     */
    public function getPicture($iAccountId = false)
    {
        return BxDolTemplate::getInstance()->getImageUrl('account-picture.png');
    }

    /**
     * Get avatar url
     */
    public function getAvatar($iAccountId = false)
    {
        return BxDolTemplate::getInstance()->getImageUrl('account-avatar.png');
    }

    /**
     * Get thumb picture url
     */
    public function getThumb($iAccountId = false)
    {
        return BxDolTemplate::getInstance()->getImageUrl('account-thumb.png');
    }

    /**
     * Get icon picture url
     */
    public function getIcon($iAccountId = false)
    {
        return BxDolTemplate::getInstance()->getImageUrl('account-icon.png');
    }

    /**
     * Get account email
     */
    public function getEmail($iAccountId = false)
    {
        $iAccountId = (int)$iAccountId ? (int)$iAccountId : $this->_iAccountID;
        $aAccountInfo = $this->getInfo($iAccountId);
        return $aAccountInfo['email'];
    }

    /**
     * Is account online
     */
	public function isOnline($iAccountId = false)
    {
        $iAccountId = (int)$iAccountId ? (int)$iAccountId : $this->_iAccountID;
        return $this->_oQuery->isOnline($iAccountId);
    }

    /**
     * Validate account.
     * @param $s - account identifier (id or email)
     * @return account id or false if account was not found
     */
    static public function getID($s)
    {
        $oQuery = BxDolAccountQuery::getInstance();

        bx_import('BxDolForm');
        if (BxDolFormCheckerHelper::checkEmail($s)) {
            $iId = (int)$oQuery->getIdByEmail($s);
            return $iId ? $iId : false;
        }

        $iId = $oQuery->getIdById((int)$s);
        return $iId ? $iId : false;
    }

    /**
     * Check if profiles limit reached
     */
    public function isProfilesLimitReached ()
    {
        $iProfilesLimit = (int)getParam('sys_account_limit_profiles_number');
        if ($iProfilesLimit && ($iProfilesNum = $this->getProfilesNumber()) && $iProfilesNum >= $iProfilesLimit)
            return true;

        return false;
    }

    /**
     * Get number of profiles associated with the account
     */
    public function getProfilesNumber ($isFilterNonSwitchableProfiles = true)
    {
        $a = $this->getProfilesIds($isFilterNonSwitchableProfiles);
        return count($a);
    }
    
    /**
     * Get all profile ids associated with the account
     */
    public function getProfilesIds ($isFilterNonSwitchableProfiles = true)
    {
        $a = $this->getProfiles ($isFilterNonSwitchableProfiles);
        return $a ? array_keys($a) : array();
    }

    /**
     * Get all profiles associated with the account
     */
    public function getProfiles ($isFilterNonSwitchableProfiles = true)
    {
        $oProfileQuery = BxDolProfileQuery::getInstance();
        $aProfiles = $oProfileQuery->getProfilesByAccount($this->_iAccountID);

        if ($isFilterNonSwitchableProfiles) {
            foreach ($aProfiles as $iProfileId => $aProfile)
                if (!BxDolService::call($aProfile['type'], 'act_as_profile'))
                    unset($aProfiles[$iProfileId]);
        }

        return $aProfiles;
    }

    /**
     * Delete profile.
     * @param $bDeleteWithContent - delete associated profiles with all its contents
     */
    function delete($bDeleteWithContent = false)
    {
        $aAccountInfo = $this->_oQuery->getInfoById($this->_iAccountID);
        if (!$aAccountInfo)
            return false;

        // create system event before deletion
        $isStopDeletion = false;
        bx_alert('account', 'before_delete', $this->_iAccountID, 0, array('delete_with_content' => $bDeleteWithContent, 'stop_deletion' => &$isStopDeletion));
        if ($isStopDeletion)
            return false;

        $oAccountQuery = BxDolAccountQuery::getInstance();

        $oProfileQuery = BxDolProfileQuery::getInstance();
        $aProfiles = $oProfileQuery->getProfilesByAccount($this->_iAccountID);
        foreach ($aProfiles as $iProfileId => $aRow) {
            $oProfile = BxDolProfile::getInstance($iProfileId);
            if (!$oProfile)
                continue;
            $oProfile->delete(false, $bDeleteWithContent, true);
        }

        // delete profile
        if (!$oAccountQuery->delete($this->_iAccountID))
            return false;

        // unset class instance to prevent creating the instance again
        $sClass = __CLASS__ . '_' . $this->_iAccountID;
        unset($GLOBALS['bxDolClasses'][$sClass]);

       // create system event
        bx_alert('account', 'delete', $this->_iAccountID, 0, array ('delete_with_content' => $bDeleteWithContent));
        
        return true;
    }

    /**
     * Add permament messages.
     */
    public function addInformerPermanentMessages ($oInformer)
    {
        if (!$this->isConfirmedEmail()) {
            $sUrl = BxDolPermalinks::getInstance()->permalink('page.php?i=confirm-email') . '&resend=1';
            $aAccountInfo = $this->getInfo();
            $oInformer->add('sys-account-unconfirmed-email', _t('_sys_txt_account_unconfirmed_email', $sUrl, $aAccountInfo['email']), BX_INFORMER_ALERT);
        }
		if (!$this->isConfirmedPhone()) {
            $sUrl = BxDolPermalinks::getInstance()->permalink('page.php?i=confirm-phone') . '';
            $aAccountInfo = $this->getInfo();
            $oInformer->add('sys-account-unconfirmed-phone', _t('_sys_txt_account_unconfirmed_phone', $sUrl), BX_INFORMER_ALERT);
        }
    }

    /**
     * Get unsubscribe link for the specified mesage type
     */
    public function getUnsubscribeLink($iEmailType, $iAccountId = false)
    {
        $iAccountId = (int)$iAccountId ? (int)$iAccountId : $this->_iAccountID;
        $sUrl = '';
        switch ($iEmailType) {
            case BX_EMAIL_NOTIFY:
                $sUrl = 'page.php?i=unsubscribe-notifications';
                break;
            case BX_EMAIL_MASS:
                $sUrl = 'page.php?i=unsubscribe-news';
                break;
            default:
                return '';
        }
        return BxDolPermalinks::getInstance()->permalink($sUrl) . '&id=' . $iAccountId . '&code=' . $this->getEmailHash();
    }

    public function getEmailHash($iAccountId = false)
    {
        $iAccountId = (int)$iAccountId ? (int)$iAccountId : $this->_iAccountID;
        $a = $this->getInfo();
        return md5($a['email'] . $a['salt'] . BX_DOL_SECRET);
    }

    /**
     * @return CHECK_ACTION_RESULT_ALLOWED if access is granted or error message if access is forbidden.
     */
    static public function isAllowedCreate ($iProfileId, $isPerformAction = false)
    {
        $aCheck = checkActionModule($iProfileId, 'create account', 'system', $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] !== CHECK_ACTION_RESULT_ALLOWED)
            return MsgBox($aCheck[CHECK_ACTION_MESSAGE]);
        return CHECK_ACTION_RESULT_ALLOWED;
    }

    /**
     * @return CHECK_ACTION_RESULT_ALLOWED if access is granted or error message if access is forbidden.
     */
    static public function isAllowedEdit ($iProfileId, $aContentInfo, $isPerformAction = false)
    {
        $oProfile = BxDolProfile::getInstance($iProfileId);
        if (!$oProfile)
            return _t('_sys_txt_access_denied');

        $aProfileInfo = $oProfile->getInfo();
        if (!$aProfileInfo || getLoggedId() != $aProfileInfo['account_id'])
            return _t('_sys_txt_access_denied');

        return CHECK_ACTION_RESULT_ALLOWED;
    }

    /**
     * @return CHECK_ACTION_RESULT_ALLOWED if access is granted or error message if access is forbidden.
     */
    static public function isAllowedDelete ($iProfileId, $aContentInfo, $isPerformAction = false)
    {
        $iAccountId = (int)BxDolProfile::getInstance($iProfileId)->getAccountId();
        if(isAdmin($iAccountId) && $iAccountId == (int)$aContentInfo['id'])
            return _t('_sys_txt_access_denied');

        $aCheck = checkActionModule($iProfileId, 'delete account', 'system', $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] !== CHECK_ACTION_RESULT_ALLOWED)
            return $aCheck[CHECK_ACTION_MESSAGE];

        return CHECK_ACTION_RESULT_ALLOWED;
    }

}

/** @} */
