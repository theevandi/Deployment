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
    
    /**
     * @page service Service Calls
     * @section bx_invites Invitations
     * @subsection bx_invites-other Other
     * @subsubsection bx_invites-get_include get_include
     * 
     * @code bx_srv('bx_invites', 'get_include', [...]); @endcode
     * 
     * Get all necessary CSS and JS files to include in a page.
     *
     * @return string with all necessary CSS and JS files.
     * 
     * @see BxInvModule::serviceGetInclude
     */
    /** 
     * @ref bx_invites-get_include "get_include"
     */
    public function serviceGetInclude()
    {
        return $this->_oTemplate->getInclude();
    }

    /**
     * @page service Service Calls
     * @section bx_invites Invitations
     * @subsection bx_invites-page_blocks Page Blocks
     * @subsubsection bx_invites-get_block_invite get_block_invite
     * 
     * @code bx_srv('bx_invites', 'get_block_invite', [...]); @endcode
     * 
     * Get page block for member's Dashboard which displays invitations related info and action(s).
     *
     * @return an array describing a block to display on the site or empty string if something is wrong. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxInvModule::serviceGetBlockInvite
     */
    /** 
     * @ref bx_invites-get_block_invite "get_block_invite"
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

    /**
     * @page service Service Calls
     * @section bx_invites Invitations
     * @subsection bx_invites-page_blocks Page Blocks
     * @subsubsection bx_invites-get_block_form_invite get_block_form_invite
     * 
     * @code bx_srv('bx_invites', 'get_block_form_invite', [...]); @endcode
     * 
     * Get page block with invite form.
     *
     * @return an array describing a block to display on the site. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxInvModule::serviceGetBlockFormInvite
     */
    /** 
     * @ref bx_invites-get_block_form_invite "get_block_form_invite"
     */
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

    /**
     * @page service Service Calls
     * @section bx_invites Invitations
     * @subsection bx_invites-page_blocks Page Blocks
     * @subsubsection bx_invites-get_block_form_request get_block_form_request
     * 
     * @code bx_srv('bx_invites', 'get_block_form_request', [...]); @endcode
     * 
     * Get page block with request invitation form.
     *
     * @return an array describing a block to display on the site. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxInvModule::serviceGetBlockFormRequest
     */
    /** 
     * @ref bx_invites-get_block_form_request "get_block_form_request"
     */
    public function serviceGetBlockFormRequest()
    {
        $mixedResult = $this->_oTemplate->getBlockFormRequest();
        if(is_array($mixedResult)) {
            echoJson($mixedResult);
            exit;
        }

    	return array(
            'content' => $mixedResult
        );
    }

    /**
     * @page service Service Calls
     * @section bx_invites Invitations
     * @subsection bx_invites-page_blocks Page Blocks
     * @subsubsection bx_invites-get_block_manage_requests get_block_manage_requests
     * 
     * @code bx_srv('bx_invites', 'get_block_manage_requests', [...]); @endcode
     * 
     * Get page block with manage invitation requests table.
     *
     * @return HTML string with block content to display on the site or empty string if something is wrong. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxInvModule::serviceGetBlockManageRequests
     */
    /** 
     * @ref bx_invites-get_block_manage_requests "get_block_manage_requests"
     */
    public function serviceGetBlockManageRequests()
    {
        $oGrid = BxDolGrid::getObjectInstance($this->_oConfig->getObject('grid_requests'));
        if(!$oGrid)
            return '';

		$this->_oTemplate->addCss(array('main.css'));
		return $oGrid->getCode();
    }

    /**
     * @page service Service Calls
     * @section bx_invites Invitations
     * @subsection bx_invites-other Other
     * @subsubsection bx_invites-get_menu_addon_requests get_menu_addon_requests
     * 
     * @code bx_srv('bx_invites', 'get_menu_addon_requests', [...]); @endcode
     * 
     * Get number or invitation requests.
     *
     * @return integer value with number of invitation requests.
     * 
     * @see BxInvModule::serviceGetMenuAddonRequests
     */
    /** 
     * @ref bx_invites-get_menu_addon_requests "get_menu_addon_requests"
     */
	public function serviceGetMenuAddonRequests()
	{
        return $this->_oDb->getRequests(array('type' => 'count_all'));
	}

	/**
     * @page service Service Calls
     * @section bx_invites Invitations
     * @subsection bx_invites-other Other
     * @subsubsection bx_invites-account_add_form_check account_add_form_check
     * 
     * @code bx_srv('bx_invites', 'account_add_form_check', [...]); @endcode
     * 
     * Perform neccessary checking on join form.
     *
     * @return empty string - if join is allowed and should be processed as usual, non-empty string - if join form need to be replaced with this code.
     * 
     * @see BxInvModule::serviceAccountAddFormCheck
     */
    /** 
     * @ref bx_invites-account_add_form_check "account_add_form_check"
     */
	public function serviceAccountAddFormCheck()
	{
	    $sReturn = '';
    	if (!$this->_oConfig->isRegistrationByInvitation())
    		return $sReturn;

    	$oSession = BxDolSession::getInstance();
		$sKeyCode = $this->_oConfig->getKeyCode();

		$bKey = bx_get($sKeyCode) !== false;
		if($bKey) {
			$sKey = bx_process_input(bx_get($sKeyCode));

        	$oKeys = BxDolKey::getInstance();
        	if($oKeys && $oKeys->isKeyExists($sKey))
		    	$oSession->setValue($sKeyCode, $sKey);
		}

		$sKey = $oSession->getValue($sKeyCode);
		if($sKey !== false)
		    return $sReturn;

        if($bKey)
            $sReturn .= MsgBox(_t('_bx_invites_err_used'));
        $sReturn .= $this->_oTemplate->getBlockRequest();

        return $sReturn;
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
