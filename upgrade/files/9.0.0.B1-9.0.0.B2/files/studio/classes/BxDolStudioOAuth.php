<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    TridentStudio Trident Studio
 * @{
 */

bx_import('BxDolStudioInstallerUtils');

class BxDolStudioOAuth extends BxDol
{
    protected $oSession;

	protected $sErrorCode;
    protected $sErrorMessage;

	protected $sKey;
    protected $sSecret;
    protected $sDataRetrieveMethod;

    public function __construct()
    {
        parent::__construct ();

        $this->oSession = BxDolSession::getInstance();

        $this->sErrorCode = 'oauth_err_code';
        $this->sErrorMessage = 'oauth_err_message';
    }

    static function isAuthorizedClient()
    {
        return (int)BxDolSession::getInstance()->getValue('sys_oauth_authorized') == 1;
    }

    static function getAuthorizedClient()
    {
        return (int)BxDolSession::getInstance()->getValue('sys_oauth_authorized_user');
    }

    public function loadItems($aParams = array())
    {
        if(empty($this->sKey) || empty($this->sSecret))
            return _t('_adm_err_oauth_empty_key_secret');

        $mixedResult = $this->authorize();
        if($mixedResult !== true)
            return $mixedResult;

        $aItems = $this->fetch($aParams);
        if(is_null($aItems))
            return _t('_adm_err_oauth_cannot_read_answer');
        else if(empty($aItems))
            return _t('_Empty');

        return $aItems;
    }

    public function doAuthorize()
    {
    	if(empty($this->sKey) || empty($this->sSecret))
            return _t('_adm_err_oauth_empty_key_secret');

		$mixedResult = $this->authorize();
		if($mixedResult === true)
			BxDolStudioInstallerUtils::getInstance()->checkModules(true);

        return $mixedResult;
    }

    protected function isAuthorized()
    {
        return self::isAuthorizedClient();
    }

    protected function getAuthorizedUser()
    {
    	return self::getAuthorizedClient();
    }

	protected function unsetAuthorizedUser()
	{
		$this->oSession->unsetValue('sys_oauth_token');
        $this->oSession->unsetValue('sys_oauth_authorized');
		$this->oSession->unsetValue('sys_oauth_authorized_user');
	}

    protected function isServerError($aResult)
    {
        return isset($aResult[$this->sErrorCode]) && isset($aResult[$this->sErrorMessage]);
    }
}

/** @} */
