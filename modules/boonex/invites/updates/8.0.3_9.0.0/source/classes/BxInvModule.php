<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    Invites Invites
 * @ingroup     TridentModules
 *
 * @{
 */

bx_import('BxDolAcl');

define('BX_INV_TYPE_FROM_MEMBER', 'from_member');
define('BX_INV_TYPE_FROM_SYSTEM', 'from_system');

class BxInvModule extends BxDolModule
{
    /**
     * Constructor
     */
    function __construct($aModule)
    {
        parent::__construct($aModule);

        $this->_oConfig->init($this->_oDb);
    }

	/**
     * ACTION METHODS
     */
	function actionGetLink()
    {
    	$iProfileId = $this->getProfileId();
		$iAccountId = $this->getAccountId($iProfileId);

        $mixedAllowed = $this->isAllowedInvite($iProfileId);
        if($mixedAllowed !== true)
        	return echoJson(array('message' => $mixedAllowed));

	    if(!isAdmin($iAccountId)) {
			$iInvited = (int)$this->_oDb->getInvites(array('type' => 'count_by_account', 'value' => $iAccountId));
			if(($this->_oConfig->getCountPerUser() - $iInvited) <= 0)
				return echoJson(array('message' => _t('_bx_invites_err_limit_reached')));
		}

    	$oKeys = BxDolKey::getInstance();
    	if(!$oKeys)
    		return  echoJson(array('message' => _t('_bx_invites_err_not_available')));  		

    	$sKey = $oKeys->getNewKey(false, $this->_oConfig->getKeyLifetime());

    	$oForm = $this->getFormObjectInvite();
    	$oForm->insert(array(
			'account_id' => $iAccountId,
			'profile_id' => $iProfileId,
			'key' => $sKey,
			'email' => '',
			'date' => time()
		));

        echoJson(array('popup' => $this->_oTemplate->getLinkPopup(
        	$this->getJoinLink($sKey)
        )));
    }

    /**
     * SERVICE METHODS
     */
    public function serviceGetBlockInvite()
    {
		$iProfileId = $this->getProfileId();
		$iAccountId = $this->getAccountId($iProfileId);

		$mixedAllowed = $this->isAllowedInvite($iProfileId);
        if($mixedAllowed !== true)
            return '';

		$iInvited = (int)$this->_oDb->getInvites(array('type' => 'count_by_account', 'value' => $iAccountId));
		if(!isAdmin($iAccountId) && $iInvited >= $this->_oConfig->getCountPerUser())
			return '';

    	return array(
    		'content' => $this->_oTemplate->getBlockInvite($iAccountId, $iProfileId)
    	);
    }

    public function serviceGetBlockFormInvite()
    {
    	$iProfileId = $this->getProfileId();
		$iAccountId = $this->getAccountId($iProfileId);

        $mixedAllowed = $this->isAllowedInvite($iProfileId);
        if($mixedAllowed !== true)
            return array(
                'content' => MsgBox($mixedAllowed)
            );

		$mixedInvites = false;
		if(!isAdmin($iAccountId)) {
			$iInvited = (int)$this->_oDb->getInvites(array('type' => 'count_by_account', 'value' => $iAccountId));
			$mixedInvites = $this->_oConfig->getCountPerUser() - $iInvited;
			if($mixedInvites <= 0)
				return array(
					'content' => MsgBox(_t('_bx_invites_err_limit_reached'))
				);
		}

        $oForm = $this->getFormObjectInvite();
        $oForm->aInputs['text']['value'] = _t('_bx_invites_msg_invitation');

        $sResult = '';
        $oForm->initChecker();
        if($oForm->isSubmittedAndValid()) {
        	$sEmails = bx_process_input($oForm->getCleanValue('emails'));
        	$sText = bx_process_pass($oForm->getCleanValue('text'));

        	$mixedResult = $this->invite(BX_INV_TYPE_FROM_MEMBER, $sEmails, $sText, $mixedInvites, $oForm);
        	if($mixedResult !== false)
        		$sResult = _t('_bx_invites_msg_invitation_sent', (int)$mixedResult);
        	else
				$sResult = _t('_bx_invites_err_not_available');

        	$sResult = MsgBox($sResult);
        }

        return array(
            'content' => $sResult . $oForm->getCode()
        );
    }

    public function serviceGetBlockFormRequest()
    {
    	if(!$this->_oConfig->isRequestInvite())
    		return array(
                'content' => MsgBox(_t('_bx_invites_err_not_available'))
            );

    	$mixedAllowed = $this->isAllowedRequest(0);
        if($mixedAllowed !== true)
            return array(
                'content' => MsgBox($mixedAllowed)
            );

    	$sResult = '';

        $oForm = BxDolForm::getObjectInstance($this->_oConfig->getObject('form_request'), $this->_oConfig->getObject('form_display_request_send'));

        $oForm->initChecker();
        if($oForm->isSubmittedAndValid()) {
        	$sIp = getVisitorIP();

        	$iId = (int)$oForm->insert(array(
        		'nip' => ip2long($sIp),
				'date' => time()
			));

			if($iId !== false) {
				$sRequestsEmail = $this->_oConfig->getRequestsEmail();
				if(!empty($sRequestsEmail)) {
					$sManageUrl = BX_DOL_URL_ROOT . BxDolPermalinks::getInstance()->permalink('page.php?i=invites-requests');

					$aMessage = BxDolEmailTemplates::getInstance()->parseTemplate('bx_invites_request_form_message', array(
						'sender_name' => bx_process_output($oForm->getCleanValue('name')),
						'sender_email' => bx_process_output($oForm->getCleanValue('email')),
						'sender_ip' => $sIp,
						'manage_url' => $sManageUrl
					));

					sendMail($sRequestsEmail, $aMessage['Subject'], $aMessage['Body'], 0, array(), BX_EMAIL_SYSTEM);
				}

				$sResult = MsgBox(_t('_bx_invites_msg_request_sent'));
			}
        }

        return array(
            'content' => $sResult . $oForm->getCode()
        );
    }

    public function serviceGetBlockManageRequests()
    {
        $oGrid = BxDolGrid::getObjectInstance($this->_oConfig->getObject('grid_requests'));
        if(!$oGrid)
            return '';

		$this->_oTemplate->addCss(array('main.css'));
		return $oGrid->getCode();
    }

	public function serviceGetMenuAddonRequests()
	{
        return $this->_oDb->getRequests(array('type' => 'count_all'));
	}

    /**
     * Perform neccessary checking on join form
     * @return empty string - if join is allowed and shoulb be processed as usual, non-empty string - if join form need to be replaced with this code
     */
	public function serviceAccountAddFormCheck()
	{
    	if (!$this->_oConfig->isRegistrationByInvitation())
    		return '';

    	$oSession = BxDolSession::getInstance();

		$sKeyCode = $this->_oConfig->getKeyCode();
		if (bx_get($sKeyCode) !== false) {
			$sKey = bx_process_input(bx_get($sKeyCode));

        	$oKeys = BxDolKey::getInstance();
        	if($oKeys && $oKeys->isKeyExists($sKey))
		    	$oSession->setValue($sKeyCode, $sKey);
		}

		$sKey = $oSession->getValue($sKeyCode);
		if ($sKey === false)
			return $this->_oTemplate->getBlockRequest();

		return '';
	}

	public function invite($sType, $sEmails, $sText, $mixedLimit = false, $oForm = null)
	{
		$iProfileId = $this->getProfileId();
		$iAccountId = $this->getAccountId($iProfileId);

		$oKeys = BxDolKey::getInstance();
		if(!$oKeys || !in_array($sType, array(BX_INV_TYPE_FROM_MEMBER, BX_INV_TYPE_FROM_SYSTEM)))
			return false;

		$iKeyLifetime = $this->_oConfig->getKeyLifetime();

		$sEmailTemplate = '';
		switch($sType) {
			case BX_INV_TYPE_FROM_MEMBER:
				$sEmailTemplate = 'bx_invites_invite_form_message';
				break;

			case BX_INV_TYPE_FROM_SYSTEM:
				$sEmailTemplate = 'bx_invites_invite_by_request_message';
				break;
		}

		if(empty($oForm))
			$oForm = $this->getFormObjectInvite();

		$aMessage = BxDolEmailTemplates::getInstance()->parseTemplate($sEmailTemplate, array(
			'text' => $sText
		), $iAccountId, $iProfileId);

		$iSent = 0;
		$iDate = time();
		$aEmails = preg_split("/[\s\n,;]+/", $sEmails);
		if(is_array($aEmails) && !empty($aEmails))
			foreach($aEmails as $sEmail) {
				if($mixedLimit !== false && (int)$mixedLimit <= 0)
					break;

				$sEmail = trim($sEmail);
				if(empty($sEmail))
					continue;

				$sKey = $oKeys->getNewKey(false, $iKeyLifetime);
				if(sendMail($sEmail, $aMessage['Subject'], $aMessage['Body'], 0, array('join_url' => $this->getJoinLink($sKey)), BX_EMAIL_SYSTEM)) {
					$oForm->insert(array(
						'account_id' => $iAccountId,
						'profile_id' => $iProfileId,
						'key' => $sKey,
						'email' => $sEmail,
						'date' => $iDate
					));

					$this->onInvite($iAccountId, $iProfileId);

					$iSent += 1;
					if($mixedLimit !== false) 
						$mixedLimit -= 1;					
				}
			}

		return $iSent;
	}

	public function isAllowedInvite($iProfileId, $bPerform = false)
    {
        $aCheckResult = checkActionModule($iProfileId, 'invite', $this->getName(), $bPerform);
        return $aCheckResult[CHECK_ACTION_RESULT] !== CHECK_ACTION_RESULT_ALLOWED ? $aCheckResult[CHECK_ACTION_MESSAGE] : true;
    }

	public function isAllowedRequest($iProfileId, $bPerform = false)
    {
        $aCheckResult = checkActionModule($iProfileId, 'request', $this->getName(), $bPerform);
        return $aCheckResult[CHECK_ACTION_RESULT] !== CHECK_ACTION_RESULT_ALLOWED ? $aCheckResult[CHECK_ACTION_MESSAGE] : true;
    }

	public function isAllowedDeleteRequest($iProfileId, $bPerform = false)
    {
        $aCheckResult = checkActionModule($iProfileId, 'delete request', $this->getName(), $bPerform);
        return $aCheckResult[CHECK_ACTION_RESULT] !== CHECK_ACTION_RESULT_ALLOWED ? $aCheckResult[CHECK_ACTION_MESSAGE] : true;
    }

    public function getProfileId()
    {
        return isLogged() ? bx_get_logged_profile_id() : 0;
    }

    public function getProfileObject($iProfileId = 0)
    {
        $oProfile = BxDolProfile::getInstance($iProfileId);
        if (!$oProfile)
            $oProfile = BxDolProfileUndefined::getInstance();

        return $oProfile;
    }

	public function getAccountId($iProfileId)
    {
    	$oProfile = $this->getProfileObject($iProfileId);
    	if($oProfile->id() == 0)
    		return 0;

        return $oProfile->getAccountId();
    }

    protected function onInvite($iAccountId, $iProfileId)
    {
        $this->isAllowedInvite($iProfileId, true);

        //--- Event -> Invite for Alerts Engine ---//
        $oAlert = new BxDolAlerts($this->_oConfig->getObject('alert'), 'invite', 0, $iProfileId);
        $oAlert->alert();
        //--- Event -> Invite for Alerts Engine ---//
    }

	protected function onRequest()
    {
        //--- Event -> Request for Alerts Engine ---//
        $oAlert = new BxDolAlerts($this->_oConfig->getObject('alert'), 'request');
        $oAlert->alert();
        //--- Event -> Request for Alerts Engine ---//
    }

    protected function getFormObjectInvite($sDisplay = '')
    {
    	if(empty($sDisplay))
    		$sDisplay = $this->_oConfig->getObject('form_display_invite_send');

        bx_import('FormCheckerHelper', $this->_aModule);
        return BxDolForm::getObjectInstance($this->_oConfig->getObject('form_invite'), $sDisplay);
    }

	protected function getJoinLink($sKey)
	{
		$sKeyCode = $this->_oConfig->getKeyCode();

		$sJoinUrl = BX_DOL_URL_ROOT . BxDolPermalinks::getInstance()->permalink('page.php?i=create-account');
		return bx_append_url_params($sJoinUrl, array($sKeyCode => $sKey));
	}
}

/** @} */
