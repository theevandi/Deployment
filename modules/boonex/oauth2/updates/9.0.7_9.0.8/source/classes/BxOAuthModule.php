<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    OAuth2 OAuth2 server
 * @ingroup     UnaModules
 *
 * @{
 */

require_once (BX_DIRECTORY_PATH_PLUGINS . 'OAuth2/Autoloader.php');
OAuth2\Autoloader::register();

class BxOAuthUserCredentialsStorage extends OAuth2\Storage\Pdo implements OAuth2\Storage\UserCredentialsInterface
{
    public function checkUserCredentials($sLogin, $sPassword)
    {
        return ($sErrorMsg = bx_check_password($sLogin, $sPassword)) ? false : true;
    }

    public function getUser($sLogin)
    {
        return $this->getUserDetails($sLogin);
    }
    
    public function getUserDetails($sLogin)
    {
        if (!($oAccount = BxDolAccount::getInstance($sLogin)))
            return false;

        if (!($oProfile = BxDolProfile::getInstanceByAccount($oAccount->id())))
            return false;

        return array('user_id' => $oProfile->id());
    }

    public function checkRestrictedGrantType($iClientId, $sGrantType)
    {
        $aDetails = $this->getClientDetails($iClientId);
        if (isset($aDetails['grant_types'])) {
            $aGrantTypes = explode(',', $aDetails['grant_types']);

            return in_array($sGrantType, (array) $aGrantTypes);
        }

        // if grant_types are not defined, then none are restricted
        return true;
    }
}

class BxOAuthModule extends BxDolModule
{
    protected $_oStorage;
    protected $_oServer;
    protected $_oAPI;

    function __construct(&$aModule)
    {
        parent::__construct($aModule);        
    }

    public function initOAuth($sClientId)
    {
        if ($this->_oStorage)
            return;        

        $aClient = array();

        // check cross origin request
        
        if (isset($_SERVER['HTTP_ORIGIN']) && parse_url($_SERVER['HTTP_ORIGIN'], PHP_URL_HOST) != parse_url(BX_DOL_URL_ROOT, PHP_URL_HOST)) {

            $aClient = $this->_oDb->getClientByAllowedOriginUrl($_SERVER['HTTP_ORIGIN']);
            if (!$aClient) {
                header('HTTP/1.0 403 Forbidden');
                echo _t("_Access denied");
                exit;
            } 

            header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
            
            if ('OPTIONS' == $_SERVER['REQUEST_METHOD']) {
                header('Access-Control-Allow-Methods: POST, GET');
                header('Access-Control-Allow-Headers: Authorization, Content-Type, X-Custom-Header, X-Requested-With');                    
                exit(0);
            }            
        }

        // get the client data if it wasn't set before
        
        if (empty($aClient))
            $aClient = $sClientId ? $this->_oDb->getClientsBy(array('type' => 'client_id', 'client_id' => $sClientId)) : false;
        
        if (!$aClient)
            $aClient = array('grant_types' => 'authorization_code');
        
        if (!$aClient['grant_types'])
            $aClient['grant_types'] = 'authorization_code';
        
        $aGrantTypes = explode(',', $aClient['grant_types']);


        // configure OAuth storage and server
                
        $aConfig = array (
            'client_table' => 'bx_oauth_clients',
            'access_token_table' => 'bx_oauth_access_tokens',
            'refresh_token_table' => 'bx_oauth_refresh_tokens',
            'code_table' => 'bx_oauth_authorization_codes',
            'user_table' => 'sys_accounts',
            'jwt_table'  => '',
            'jti_table'  => '',
            'scope_table'  => 'bx_oauth_scopes',
            'public_key_table'  => '',
        );
        if (in_array('refresh_token', $aGrantTypes)) {
            $aConfig['refresh_token'] = '';
            $aConfig['always_issue_new_refresh_token'] = true;
            $aConfig['unset_refresh_token_after_use'] = false;
        }        
        
        $this->_oStorage = new BxOAuthUserCredentialsStorage(BxDolDb::getLink(), $aConfig);

        $this->_oServer = new OAuth2\Server($this->_oStorage, array(
            'require_exact_redirect_uri' => false,
            'refresh_token_lifetime' => 7776000, // set lifetime to 90 days
        ));

        // add grand types

        // Add the "Client Credentials" grant type (it is the simplest of the grant types)
        if (in_array('client_credentials', $aGrantTypes))
            $this->_oServer->addGrantType(new OAuth2\GrantType\ClientCredentials($this->_oStorage));

        // Add the "Authorization Code" grant type (this is where the oauth magic happens)
        if (in_array('authorization_code', $aGrantTypes))
            $this->_oServer->addGrantType(new OAuth2\GrantType\AuthorizationCode($this->_oStorage));

        // Add the "Password" grant type (generate client_id with empty client_secret)
        if (in_array('password', $aGrantTypes))
            $this->_oServer->addGrantType(new OAuth2\GrantType\UserCredentials($this->_oStorage));

        // Add the "Refresh Token" grant type
        if (in_array('refresh_token', $aGrantTypes))
            $this->_oServer->addGrantType(new OAuth2\GrantType\RefreshToken($this->_oStorage));
    }    

    /**
     * @page public_api API Public
     * @section public_api_token /m/oauth2/token
     * 
     * Get the token for the future communication with @ref private_api
     * 
     * **HTTP Method:** 
     * `POST`
     *
     * **Request params:**
     * - `grant_type` - for API it's better to use 'password' grant type
     * - `username` - login email
     * - `password` - login password
     * - `client_id` - client ID from bx_oauth_clients table
     *
     * **Response (success):**
     * @code
     * {  
     *    "access_token":"cdd7056d0adafa9ead87526ca22367c6b0df8273",
     *    "expires_in":3600,
     *    "token_type":"Bearer",
     *    "scope":"basic",
     *    "refresh_token":"c3d7f6f4b7cc640214ae0cba2b194872c3089f1c"
     * }
     * @endcode
     *
     * **Response (error):**
     * @code
     * {  
     *    "error":"short error description here",
     *    "error_description":"long error description here"
     * }
     * @endcode
     */     
    function actionToken ()
    {
        $this->initOAuth(bx_get('client_id'));
        
        // Handle a request for an OAuth2.0 Access Token and send the response to the client
        $this->_oServer->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();
    }

    function actionApi ($sAction)
    {
        $this->initOAuth($this->getClientIdFromAccessTokenHeader());
        
        // Handle a request to a resource and authenticate the access token
        if (!$this->_oServer->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $this->_oServer->getResponse()->send();
            return;
        }

        $aToken = $this->_oServer->getAccessTokenData(OAuth2\Request::createFromGlobals());

        if (!$this->_oAPI) {
            bx_import('API', $this->_aModule);
            $this->_oAPI = new BxOAuthAPI($this);
        }

        if (!$sAction || !method_exists($this->_oAPI, $sAction) || 0 === strcasecmp('errorOutput', $sAction) || 0 === strcasecmp('output', $sAction)) {
            $this->_oAPI->errorOutput(404, 'not_found', 'No such API endpoint available');
            return;
        }

        $aScope = explode(',', $this->_oAPI->aAction2Scope[$sAction]);
        $aScopeToken = explode(',', $aToken['scope']);
        if (!array_intersect($aScopeToken, $aScope)) {
            $this->_oAPI->errorOutput(403, 'insufficient_scope', 'The request requires higher privileges than provided by the access token');
            return;
        }

        $this->_oAPI->$sAction($aToken);
    }

    function actionAuth ()
    {
        $this->initOAuth(bx_get('client_id'));
        
        $oRequest = OAuth2\Request::createFromGlobals();
        $oResponse = new OAuth2\Response();

        // validate the authorize request
        if (!$this->_oServer->validateAuthorizeRequest($oRequest, $oResponse)) {
            require_once(BX_DIRECTORY_PATH_INC . 'design.inc.php');
            $o = json_decode($oResponse->getResponseBody());
            $this->_oTemplate->getPage(false, MsgBox($o->error_description));
        }

        if (!isLogged()) {
            require_once(BX_DIRECTORY_PATH_INC . 'design.inc.php');
            $sForceRelocate = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'auth/?client_id=' . bx_get('client_id') . '&response_type=' . bx_get('response_type') . '&scope=' . bx_get('scope') . '&state=' . bx_get('state') . '&redirect_uri=' . bx_get('redirect_uri');
            bx_login_form(false, false, $sForceRelocate);
            return;
        }

        $aProfiles = BxDolAccount::getInstance()->getProfiles();

        if (1 == count($aProfiles)) { // in case of one profile, don't display dialog with profiles choice
            $aProfile = array_pop($aProfiles);
            $_POST['profile_id'] = $aProfile['id'];
        }

        if (!($iProfileId = $this->_oDb->getSavedProfile(bx_get('client_id'), $aProfiles)) && empty($_POST)) {
            $oPage = BxDolPage::getObjectInstanceByURI('oauth-authorization');
            $this->_oTemplate->getPage(false, $oPage->getCode());
            return;
        } 

        if (!$iProfileId)
            $iProfileId = bx_get('profile_id');

        $this->_oServer->handleAuthorizeRequest($oRequest, $oResponse, (bool)$iProfileId, $iProfileId);

        $oResponse->send();
    }

    function serviceAuthorization ()
    {
        $sTitle = $this->_oDb->getClientTitle(bx_get('client_id'));
        $this->_oTemplate->addCss('main.css');
        return $this->_oTemplate->parseHtmlByName('page_auth.html', array(
            'text' => _t('_bx_oauth_authorize_app', htmlspecialchars_adv($sTitle)),
            'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'auth',
            'client_id' => bx_get('client_id'),
            'response_type' => bx_get('response_type'),
            'redirect_uri' => bx_get('redirect_uri'),
        	'scope' => bx_get('scope'),
            'state' => bx_get('state'),
            'profiles' => BxDolService::call('system', 'account_profile_switcher', array(getLoggedId(), false, "javascript: $('#bx-auth-profile-id').val('{profile_id}'); $('#bx-auth-form form').submit(); void(0);", true, _t('_bx_oauth_connect'), 'unit_wo_links'), 'TemplServiceProfiles')['content'],
        ));
    }

    function serviceGetClientsBy ($aParams = array())
    {
    	return $this->_oDb->getClientsBy($aParams);
    }

    function serviceAddClient ($aClient)
    {
        if (!isset($aClient['client_id'])) {
            bx_import('FormAdd', 'bx_oauth');
            for ($i = 0; $i < 99 ; ++$i) {
                $aClient['client_id'] = strtolower(genRndPwd(BxOAuthFormAdd::$LENGTH_ID, false));
                if (!$this->_oDb->getClientTitle($aClient['client_id'])) // check for uniq
                    break;
            }
        }

        if (!isset($aClient['client_secret'])) {
            bx_import('FormAdd', 'bx_oauth');
            $aClient['client_secret'] = strtolower(genRndPwd(BxOAuthFormAdd::$LENGTH_SECRET, false));
        }

        if (!isset($aClient['scope']))
            $aClient['scope'] = 'market';

        if (!isset($aClient['user_id']))
            $aClient['user_id'] = bx_get_logged_profile_id();

        if (!isset($aClient['title']) && isset($aClient['redirect_uri']) && ($sHost = parse_url($aClient['redirect_uri'], PHP_URL_HOST)))
            $aClient['title'] = $sHost;

    	return $this->_oDb->addClient($aClient);
    }

	function serviceUpdateClientsBy ($aParamsSet, $aParamsWhere)
    {
    	return $this->_oDb->updateClientsBy($aParamsSet, $aParamsWhere);
    }

	function serviceDeleteClientsBy ($aParams)
    {
    	return $this->_oDb->deleteClientsBy($aParams);
    }

    function studioSettings ()
    {
        if (!isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $oGrid = BxDolGrid::getObjectInstance('bx_oauth', BxDolStudioTemplate::getInstance());
        if ($oGrid)
            return $oGrid->getCode();

        return '';
    }

    function getClientIdFromAccessTokenHeader()
    {
        if (isset($_SERVER['HTTP_AUTHORIZATION']))
            $sAuthHeader = $_SERVER['HTTP_AUTHORIZATION'];
        elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']))
            $sAuthHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        if (empty($sAuthHeader))
            return false;
        
        if (false === stripos($sAuthHeader, 'Bearer'))
            return false;

        return $this->_oDb->getClientIdByAccessToken(trim(substr($sAuthHeader, 6)));
    }
}

/** @} */
