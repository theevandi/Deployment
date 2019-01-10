<?php defined('BX_DOL') or defined('BX_DOL_INSTALL') or die('hack attempt');
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    TridentCore Trident Core
 * @{
 */

define('BX_DOL_LINK_CLASS', 'bx-link'); ///< class to add to every link in user content

define('BX_DATA_TEXT', 1); ///< regular text data type
define('BX_DATA_TEXT_MULTILINE', 2); ///< regular multiline text data type
define('BX_DATA_INT', 3); ///< integer data type
define('BX_DATA_FLOAT', 4); ///< float data type
define('BX_DATA_CHECKBOX', 5); ///< checkbox data type, 'on' or empty value
define('BX_DATA_HTML', 6); ///< HTML data type
define('BX_DATA_DATE', 7); ///< date data type stored as yyyy-mm-dd
define('BX_DATA_DATE_TS', 8); ///< date data type stored as unixtimestamp
define('BX_DATA_DATETIME_TS', 9); ///< date/time data type stored as unixtimestamp
define('BX_DATA_DATE_TS_UTC', 10); ///< date data type stored as unixtimestamp from UTC time
define('BX_DATA_DATETIME_TS_UTC', 11); ///< date/time data type stored as unixtimestamp from UTC time

define('BX_SLASHES_AUTO', 0);
define('BX_SLASHES_ADD', 1);
define('BX_SLASHES_STRIP', 2);
define('BX_SLASHES_NO_ACTION', 3);

define('BX_ESCAPE_STR_AUTO', 0); ///< turn apostropes and quote signs into html special chars, for use in @see bx_js_string and @see bx_html_attribute
define('BX_ESCAPE_STR_APOS', 1); ///< escape apostrophes only, for js strings enclosed in apostrophes, for use in @see bx_js_string and @see bx_html_attribute
define('BX_ESCAPE_STR_QUOTE', 2); ///< escape quotes only, for js strings enclosed in quotes, for use in @see bx_js_string and @see bx_html_attribute

define('BX_EMAIL_SYSTEM', 0); ///< system email without unsubscribe link, like forgot password or email verification
define('BX_EMAIL_NOTIFY', 1); ///< notification message, with unsubscribe link
define('BX_EMAIL_MASS', 2); ///< mass email, one mesage send to manu users, with unsubscribe link

define('BX_MAINTENANCE_FILE', '.bx_maintenance'); ///< file name to use as mantenance mode indicator

/**
 * The following two functions are needed to convert title to uri and back.
 * It usefull when titles are used in URLs, like in Categories and Tags.
 */
function title2uri($sValue)
{
    return str_replace(
        array('&', '/', '\\', '"', '+'),
        array('[and]', '[slash]', '[backslash]', '[quote]', '[plus]'),
        $sValue
    );
}
function uri2title($sValue)
{
    return str_replace(
        array('[and]', '[slash]', '[backslash]', '[quote]', '[plus]'),
        array('&', '/', '\\', '"', '+'),
        $sValue
    );
}

/*
 * functions for limiting maximal text length
 */
function strmaxtextlen($sInput, $iMaxLen = 60, $sEllipsisSign = '&#8230;')
{
    $sTail = '';
    $s = trim(strip_tags($sInput));
    if (mb_strlen($s) > $iMaxLen) {
        $s = mb_substr($s, 0, $iMaxLen);
        $sTail = $sEllipsisSign;
    }
    return htmlspecialchars_adv($s) . $sTail;
}

function html2txt($content, $tags = "")
{
    while($content != strip_tags($content, $tags)) {
        $content = strip_tags($content, $tags);
    }

    return $content;
}

/**
 * Functions to process user input.
 * DON'T use to process data before passing to SQL query - use db prepare instead @see BxDolDb::prepare.
 * It is ok to use bx_process_input and then db prepare.
 * @param $mixedData data to process
 * @param $iDataType how to handle data, possible valies:
 *          BX_DATA_INT - integer value
 *          BX_DATA_FLOAT - float values
 *          BX_DATA_CHECKBOX - 'on' or empty string
 *          BX_DATA_TEXT - text data, single line (default)
 *          BX_DATA_TEXT_MULTILINE - text data, multiple lines
 *          BX_DATA_HTML - HTML data
 *          BX_DATA_DATE - date data type stored as yyyy-mm-dd
 *          BX_DATA_DATE_TS' -  date data type stored as unixtimestamp
 *          BX_DATA_DATETIME_TS - date/time data type stored as unixtimestamp
 * @param $mixedParams optional parameters to pass for validation
 * @return the filtered data, or FALSE if the filter fails.
 */
function bx_process_input ($mixedData, $iDataType = BX_DATA_TEXT, $mixedParams = false, $isCheckMagicQuotes = true)
{
    if (is_array($mixedData)) {
        foreach ($mixedData as $k => $v)
            $mixedData[$k] = bx_process_input($v, $iDataType, $mixedParams);
        return $mixedData;
    }

    if (get_magic_quotes_gpc() && $isCheckMagicQuotes)
        $mixedData = stripslashes($mixedData);

    switch ($iDataType) {
    case BX_DATA_INT:
        return filter_var(trim($mixedData), FILTER_VALIDATE_INT);
    case BX_DATA_FLOAT:
        return filter_var(trim($mixedData), FILTER_VALIDATE_FLOAT);
    case BX_DATA_CHECKBOX:
        return 'on' == trim($mixedData) ? 'on' : '';

    case BX_DATA_DATE:
        // maybe consider using strtotime
        $mixedData = trim($mixedData);
        if (!preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', $mixedData))
            return false;
        list($iYear, $iMonth, $iDay) = explode('-', $mixedData); // 1985-10-28
        $iDay   = intval($iDay);
        $iMonth = intval($iMonth);
        $iYear  = intval($iYear);
        return sprintf("%04d-%02d-%02d", $iYear, $iMonth, $iDay);
    case BX_DATA_DATE_TS:
    case BX_DATA_DATE_TS_UTC:
        $mixedData = trim($mixedData);
        if (!preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', $mixedData))
            return false;
        list($iYear, $iMonth, $iDay) = explode('-', $mixedData);
        $iDay   = intval($iDay);
        $iMonth = intval($iMonth);
        $iYear  = intval($iYear);
        $sFunc = BX_DATA_DATE_TS_UTC == $iDataType ? 'gmmktime' : 'mktime';
        $iRet = $sFunc (0, 0, 0, $iMonth, $iDay, $iYear);
        return $iRet > 0 ? $iRet : false;
    case BX_DATA_DATETIME_TS:
    case BX_DATA_DATETIME_TS_UTC:
        if (!preg_match('#(\d+)\-(\d+)\-(\d+)[\sT]{1}(\d+):(\d+):(\d+)#', $mixedData, $m) && !preg_match('#(\d+)\-(\d+)\-(\d+)[\sT]{1}(\d+):(\d+)#', $mixedData, $m))
            return bx_process_input ($mixedData, BX_DATA_DATE_TS, $mixedParams, $isCheckMagicQuotes);
        $iDay   = $m[3];
        $iMonth = $m[2];
        $iYear  = $m[1];
        $iH = $m[4];
        $iM = $m[5];
        $iS = isset($m[6]) ? $m[6] : 0;
        $sFunc = BX_DATA_DATETIME_TS_UTC == $iDataType ? 'gmmktime' : 'mktime';
        $iRet = $sFunc ($iH, $iM, $iS, $iMonth, $iDay, $iYear);
        return $iRet > 0 ? $iRet : false;

    case BX_DATA_HTML:
        return clear_xss($mixedData);
    case BX_DATA_TEXT_MULTILINE:
        return nl2br(htmlspecialchars_adv($mixedData));
    case BX_DATA_TEXT:
    default:
        return $mixedData;
    }
}

/*
 * Functions to process user output.
 * Always use this function before output data which was entered by user before.
 * @param $mixedData string to process
 * @param $iDataType how to handle data, possible valies the same as in bx_process_input function, see bx_process_input.
 * @param $mixedParams optional parameters to pass for validation
 * @return the filtered data, or FALSE if the filter fails.
 */
function bx_process_output ($mixedData, $iDataType = BX_DATA_TEXT, $mixedParams = false)
{
    if (is_array($mixedData)) {
        foreach ($mixedData as $k => $v)
            $mixedData[$k] = bx_process_output($v, $iDataType, $mixedParams);
        return $mixedData;
    }

    switch ($iDataType) {
    case BX_DATA_INT:
        return filter_var($mixedData, FILTER_VALIDATE_INT);
    case BX_DATA_FLOAT:
        return filter_var($mixedData, FILTER_VALIDATE_FLOAT);
    case BX_DATA_CHECKBOX:
        return 'on' == trim($mixedData) ? 'on' : '';

    case BX_DATA_DATE:
        return $mixedData;
    case BX_DATA_DATE_TS:
        return empty($mixedData) ? '' : date("Y-m-d", (int)$mixedData);
    case BX_DATA_DATE_TS_UTC:
        return empty($mixedData) ? '' : gmdate("Y-m-d", (int)$mixedData);
    case BX_DATA_DATETIME_TS:
        return empty($mixedData) ? '' : date("Y-m-d H:i", (int)$mixedData);
    case BX_DATA_DATETIME_TS_UTC:
        return empty($mixedData) ? '' : gmdate("Y-m-d H:i", (int)$mixedData);

    case BX_DATA_HTML:
        return $mixedData;
    case BX_DATA_TEXT_MULTILINE:
        return $mixedData;
    case BX_DATA_TEXT:
    default:
        return htmlspecialchars_adv($mixedData);
    }
}

/*
 * This function apply bx_process_input and then bx_process_output.
 * Use this function to output data immediately after receiving, without saving to database.
 * Patams are the same as bx_process_input function - @see bx_process_input
 */
function bx_process_pass ($mixedData, $iDataType = BX_DATA_TEXT, $mixedParams = false, $isCheckMagicQuotes = true)
{
    return bx_process_output(bx_process_input ($mixedData, $iDataType, $mixedParams, $isCheckMagicQuotes), $iDataType, $mixedParams);
}

/**
 * DEPRECATED
 * use bx_process_input + bx_process_output instead
 * --------
 * function for processing pass data
 *
 * This function cleans the GET/POST/COOKIE data if magic_quotes_gpc() is on
 * for data which should be outputed immediately after submit
 */
/*
function process_pass_data( $text, $strip_tags = 0 )
{
    if ( $strip_tags )
        $text = strip_tags($text);

    if ( !get_magic_quotes_gpc() )
        return $text;
    else
        return stripslashes($text);
}
*/

/*
 * function for output data from database into html
 */
function htmlspecialchars_adv( $string )
{
    return htmlspecialchars($string, ENT_COMPAT, 'UTF-8', false);
}

/**
 * Send mail to user by parsing email template
 */
function sendMailTemplate($sTemplateName, $iAccountId = 0, $iProfileId = 0, $aReplaceVars = array(), $iEmailType = BX_EMAIL_NOTIFY)
{
    $oProfile = BxDolProfile::getInstance($iProfileId);

    $oAccount = $iAccountId ? BxDolAccount::getInstance($iAccountId) : ($oProfile ? $oProfile->getAccountObject() : null);

    $oEmailTemplates = BxDolEmailTemplates::getInstance();

    if (!$oAccount || !$oProfile || !$oEmailTemplates)
        return false;

    $aTemplate = $oEmailTemplates->parseTemplate($sTemplateName, $aReplaceVars, $iAccountId, (int)$iProfileId);
    if (!$aTemplate)
        return false;

    return sendMail($oAccount->getEmail(), $aTemplate['Subject'], $aTemplate['Body'], 0, array(), $iEmailType);
}

/**
 * Send system email 
 */
function sendMailTemplateSystem($sTemplateName, $aReplaceVars = array(), $iEmailType = BX_EMAIL_SYSTEM)
{
    $oEmailTemplates = BxDolEmailTemplates::getInstance();

    if (!$oEmailTemplates)
        return false;

    $aTemplate = $oEmailTemplates->parseTemplate($sTemplateName, $aReplaceVars);
    if (!$aTemplate)
        return false;

    return sendMail(getParam('site_email'), $aTemplate['Subject'], $aTemplate['Body'], 0, array(), $iEmailType);
}
/**
 * Send email function
 *
 * @param $sRecipientEmail - Email where email should be send
 * @param $sMailSubject - subject of the message
 * @param $sMailBody - Body of the message
 * @param $iRecipientID - ID of recipient profile
 * @param $aPlus - Array of additional information
 * @param $iEmailType - email message type: BX_EMAIL_SYSTEM, BX_EMAIL_NOTIFY or BX_EMAIL_MASS
 * @return true if message was send or false otherwise
 */
function sendMail($sRecipientEmail, $sMailSubject, $sMailBody, $iRecipientID = 0, $aPlus = array(), $iEmailType = BX_EMAIL_NOTIFY, $sEmailFlag = 'html', $isDisableAlert = false)
{
    // make sure that recipient's email is valid and message isn't empty
    if (!$sMailBody || !$sRecipientEmail || preg_match('/\(2\)$/', $sRecipientEmail))
        return false;

    // get recipient account
    $oAccount = !$isDisableAlert ? BxDolAccount::getInstance($sRecipientEmail) : null;
    $aAccountInfo = $oAccount ? $oAccount->getInfo() : false;

    // don't send bulk emails if user didn't subscribed to site news or email is unconfirmed
    if ($aAccountInfo && BX_EMAIL_MASS == $iEmailType && (!$aAccountInfo['email_confirmed'] || !$aAccountInfo['receive_news']))
        return false;

    // don't send email notifications if user didn't subscribed to notifications or email is unconfirmed
    if ($aAccountInfo && BX_EMAIL_NOTIFY == $iEmailType && (!$aAccountInfo['email_confirmed'] || !$aAccountInfo['receive_updates']))
        return false;

    // if profile id is provided - get profile's info
    $aRecipientInfo = false;
    if ($iRecipientID) {
        $oProfile = BxDolProfile::getInstance($iRecipientID);
        if ($oProfile)
            $aRecipientInfo = $oProfile->getInfo();
    }

    // get site vars
    $sEmailNotify = !$isDisableAlert ? getParam('site_email_notify') : $sRecipientEmail;
    $sSiteTitle = !$isDisableAlert ? getParam('site_title') : 'UNA ' . BX_DOL_VERSION;

    // add unsubscribe link
    if (!$isDisableAlert && empty($aPlus['unsubscribe'])) {
        $aPlus['unsubscribe'] = '';
        if ($oAccount && (BX_EMAIL_MASS == $iEmailType || BX_EMAIL_NOTIFY == $iEmailType))
            $aPlus['unsubscribe'] = ($sLink = $oAccount->getUnsubscribeLink($iEmailType)) ? '<a href="' . BX_DOL_URL_ROOT . $sLink . '">' . _t('_sys_et_txt_unsubscribe') . '</a>' : '';
    }

    // parse template
    if ($aPlus || $iRecipientID) {
        if(!is_array($aPlus))
            $aPlus = array();
        $oEmailTemplates = BxDolEmailTemplates::getInstance();
        $sMailSubject = $oEmailTemplates->parseContent($sMailSubject, $aPlus, $iRecipientID);
        $sMailBody = $oEmailTemplates->parseContent($sMailBody, $aPlus, $iRecipientID);
    }

    // email message headers
    $sMailHeader = "From: =?UTF-8?B?" . base64_encode( $sSiteTitle ) . "?= <{$sEmailNotify}>";
    $sMailParameters = "-f{$sEmailNotify}";
    $sMailSubject = '=?UTF-8?B?' . base64_encode( $sMailSubject ) . '?=';
    $sMailHeader = "MIME-Version: 1.0\r\n" . $sMailHeader;

    // build data for alert handler
    $bResult = null;
    $aAlert = array(
        'email' => $sRecipientEmail,
        'subject' => $sMailSubject,
        'body' => $sMailBody,
        'header' => $sMailHeader,
        'params' => $sMailParameters,
        'recipient' => $aRecipientInfo,
        'html' => 'html' == $sEmailFlag ? true : false,
        'override_result' => &$bResult,
    );

    // system alert
    if (!$isDisableAlert) {
        bx_alert('system', 'before_send_mail', (isset($aRecipientInfo['ID']) ? $aRecipientInfo['ID'] : 0), '', $aAlert);
        if ($bResult !== null)
            return $bResult;
        unset($aAlert['override_result']);
    }

    // send mail
    if( 'html' == $sEmailFlag) {
        $sMailHeader = "Content-type: text/html; charset=UTF-8\r\n" . $sMailHeader;
        $iSendingResult = mail( $sRecipientEmail, $sMailSubject, $sMailBody, $sMailHeader, $sMailParameters );
    } else {
        $sMailHeader = "Content-type: text/plain; charset=UTF-8\r\n" . $sMailHeader;
        $sMailBody = html2txt($sMailBody);
        $iSendingResult = mail( $sRecipientEmail, $sMailSubject, html2txt($sMailBody), $sMailHeader, $sMailParameters );
    }

    // system alert
    if (!$isDisableAlert)
        bx_alert('system', 'send_mail', (isset($aRecipientInfo['ID']) ? $aRecipientInfo['ID'] : 0), '', $aAlert);

    return $iSendingResult;
}

/*
 * Getting an array with Templates' Names
 */
function get_templates_array($bEnabledOnly = true, $bShortInfo = true)
{
    $oDb = BxDolDb::getInstance();

    $sWhereAddon = $bEnabledOnly ? " AND `enabled`='1'" : "";

    if($bShortInfo)
        return $oDb->getPairs("SELECT `uri`, `title` FROM `sys_modules` WHERE 1 AND `type`='" . BX_DOL_MODULE_TYPE_TEMPLATE . "'" . $sWhereAddon, "uri", "title");
    else
        return $oDb->getAllWithKey("SELECT * FROM `sys_modules` WHERE 1 AND `type`='" . BX_DOL_MODULE_TYPE_TEMPLATE . "'" . $sWhereAddon, "uri");
}

function extFileExists( $sFileSrc )
{
    return (file_exists( $sFileSrc ) && is_file( $sFileSrc )) ? true : false;
}

function getVisitorIP()
{
    $ip = "0.0.0.0";
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset( $_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = explode(".",$_SERVER['HTTP_CLIENT_IP']);
        $ip = $ip[3].".".$ip[2].".".$ip[1].".".$ip[0];
    } elseif (!isset( $_SERVER['HTTP_X_FORWARDED_FOR']) || empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        if (!isset( $_SERVER['HTTP_CLIENT_IP']) && empty($_SERVER['HTTP_CLIENT_IP']) && isset($_SERVER['REMOTE_ADDR']))
            $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function genFlag($sLang = '', $oTemplate = null)
{
    if (!$oTemplate)
        $oTemplate = BxDolTemplate::getInstance();
    $oTemplate->addCss(BX_DIRECTORY_PATH_PLUGINS_PUBLIC . 'flag-icon-css/css/|flag-icon.min.css');
    $sFlag = BxDolLanguages::getInstance()->getLangFlag($sLang);
    return '<span title="' . $sFlag . '" class="flag-icon flag-icon-' . $sFlag . '"></span>';
}

// print debug information ( e.g. arrays )
function echoDbg( $what, $desc = '' )
{
    if ( $desc )
        echo "<b>$desc:</b> ";
    echo "<pre>";
        print_r( $what );
    echo "</pre>\n";
}

function echoDbgLog($mWhat, $sDesc = '', $sFileName = 'debug.log')
{
    $sCont =
        '--- ' . date('r') . ' (' . BX_DOL_START . ") ---\n" .
        $sDesc . "\n" .
        print_r($mWhat, true) . "\n\n\n";

    $rFile = fopen(BX_DIRECTORY_PATH_LOGS . $sFileName, 'a');
    fwrite($rFile, $sCont);
    fclose($rFile);
}

function echoJson($a)
{
	header('Content-type: text/html; charset=utf-8');

	echo json_encode($a);
}

function clear_xss($val)
{
    // HTML Purifier plugin
    global $oHtmlPurifier;
    if (!isset($oHtmlPurifier) && !$GLOBALS['logged']['admin']) {

        require_once( BX_DIRECTORY_PATH_PLUGINS . 'htmlpurifier/HTMLPurifier.standalone.php' );

        HTMLPurifier_Bootstrap::registerAutoload();

        $oConfig = HTMLPurifier_Config::createDefault();

        $oConfig->set('Cache.SerializerPath', rtrim(BX_DIRECTORY_PATH_CACHE, '/'));
        $oConfig->set('Cache.SerializerPermissions', BX_DOL_DIR_RIGHTS);

        $oConfig->set('HTML.SafeObject', 'true');
        $oConfig->set('Output.FlashCompat', 'true');
        $oConfig->set('HTML.FlashAllowFullScreen', 'true');

        if (getParam('sys_add_nofollow')) {
            $sHost = parse_url(BX_DOL_URL_ROOT, PHP_URL_HOST);
            $oConfig->set('URI.Host', $sHost);
            $oConfig->set('HTML.Nofollow', 'true');
        }

        $oConfig->set('Filter.Custom', array (new HTMLPurifier_Filter_YouTube(), new HTMLPurifier_Filter_YoutubeIframe(), new HTMLPurifier_Filter_AddBxLinksClass()));

	    $oConfig->set('HTML.DefinitionID', 'html5-definitions');
		$oConfig->set('HTML.DefinitionRev', 1);
		if ($def = $oConfig->maybeGetRawHTMLDefinition()) {
		    $def->addElement('section', 'Block', 'Flow', 'Common');
		    $def->addElement('nav',     'Block', 'Flow', 'Common');
		    $def->addElement('article', 'Block', 'Flow', 'Common');
		    $def->addElement('aside',   'Block', 'Flow', 'Common');
		    $def->addElement('header',  'Block', 'Flow', 'Common');
		    $def->addElement('footer',  'Block', 'Flow', 'Common');
		    $def->addElement('video', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', array(
		        'src' => 'URI',
		        'type' => 'Text',
		        'width' => 'Length',
		        'height' => 'Length',
		        'poster' => 'URI',
		        'preload' => 'Enum#auto,metadata,none',
		        'controls' => 'Bool',
		    ));
		    $def->addElement('source', 'Block', 'Flow', 'Common', array(
		        'src' => 'URI',
		        'type' => 'Text',
		    ));
		}

        $oHtmlPurifier = new HTMLPurifier($oConfig);
    }

    if (!$GLOBALS['logged']['admin'])
        $val = $oHtmlPurifier->purify($val);

    bx_alert('system', 'clear_xss', 0, 0, array('oHtmlPurifier' => $oHtmlPurifier, 'return_data' => &$val));

    return $val;
}

//--------------------------------------- friendly permalinks --------------------------------------//
//------------------------------------------- main functions ---------------------------------------//
function uriGenerate ($s, $sTable, $sField, $sEmpty = '-')
{
    $s = uriFilter($s, $sEmpty);
    if(uriCheckUniq($s, $sTable, $sField))
        return $s;

    // cut off redundant part
    if(get_mb_len($s) > 240)
        $s = get_mb_substr($s, 0, 240);

    // try to add date
    $s .= '-' . date('Y-m-d');
    if(uriCheckUniq($s, $sTable, $sField))
        return $s;

    // try to add number
    for($i = 0 ; $i < 999 ; ++$i)
        if(uriCheckUniq($s . '-' . $i, $sTable, $sField))
            return ($s . '-' . $i);

    return rand(0, 999999999);
}

function uriFilter ($s, $sEmpty = '-')
{
    if (BxTemplConfig::getInstance()->bAllowUnicodeInPreg)
        $s = get_mb_replace ('/[^\pL^\pN]+/u', '-', $s); // unicode characters
    else
        $s = get_mb_replace ('/([^\d^\w]+)/u', '-', $s); // latin characters only

    $s = get_mb_replace ('/([-^]+)/', '-', $s);
    $s = get_mb_replace ('/([-]+)$/', '', $s); // remove trailing dash
    if (!$s) $s = $sEmpty;
    return $s;
}

function uriCheckUniq ($s, $sTable, $sField)
{
    $oDb = BxDolDb::getInstance();

    $sSql = $oDb->prepare("SELECT 1 FROM `$sTable` WHERE `$sField`=? LIMIT 1", $s);
    return !$oDb->query($sSql);
}

function get_mb_replace ($sPattern, $sReplace, $s)
{
    return preg_replace ($sPattern, $sReplace, $s);
}

function get_mb_len ($s)
{
    return (function_exists('mb_strlen')) ? mb_strlen($s) : strlen($s);
}

function get_mb_substr ($s, $iStart, $iLen)
{
    return (function_exists('mb_substr')) ? mb_substr ($s, $iStart, $iLen) : substr ($s, $iStart, $iLen);
}

function bx_mb_substr_replace($s, $sReplace, $iPosStart, $iLength)
{
    return mb_substr($s, 0, $iPosStart) . $sReplace . mb_substr($s, $iPosStart + $iLength);
}

function bx_mb_strpos ($s, $sReplacement, $iStart = 0)
{
    return mb_strpos($s, $sReplacement, $iStart);
}

/**
 * Import class file, it automatically detects class path by its prefix or module array/name
 *
 * @param $sClassName - full class name or class postfix(withoit prefix) in the case of module class
 * @param $mixedModule - module array or module name in the case of module class
 */
function bx_import($sClassName, $mixedModule = array())
{
    if (class_exists($sClassName, false))
        return;

    $aModule = false;
    if ($mixedModule) {
        if (is_array($mixedModule)) {
            $aModule = $mixedModule;
        } elseif (is_string($mixedModule)) {
            $o = BxDolModule::getInstance($mixedModule);
            $aModule = $o->_aModule;
        } elseif (is_bool($mixedModule) && true === $mixedModule) {
            $aModule = $GLOBALS['aModule'];
        }
    }

    if ($aModule) {
        if (class_exists($aModule['class_prefix'] . $sClassName, false))
            return;
        require_once (BX_DIRECTORY_PATH_MODULES . $aModule['path'] . 'classes/' . $aModule['class_prefix'] . $sClassName . '.php');
        return;
    }

    if (0 == strncmp($sClassName, 'BxDol', 5)) {
        if (0 == strncmp($sClassName, 'BxDolStudio', 11))
            require_once(BX_DOL_DIR_STUDIO_CLASSES . $sClassName . '.php');
        else
            require_once(BX_DIRECTORY_PATH_CLASSES . $sClassName . '.php');
        return;
    }

    if (0 == strncmp($sClassName, 'BxBase', 6)) {
        if (0 == strncmp($sClassName, 'BxBaseMod', 9)) {
            $aMatches = array();
            if (preg_match('/BxBaseMod([A-Z][a-z]+)/', $sClassName, $aMatches)) {
                require_once(BX_DIRECTORY_PATH_MODULES . 'base/' . strtolower($aMatches[1]) . '/classes/' . $sClassName . '.php');
                return;
            }
        } if (0 == strncmp($sClassName, 'BxBaseStudio', 12)) {
            require_once(BX_DOL_DIR_STUDIO_BASE . 'scripts/' . $sClassName . '.php');
            return;
        } else {
            require_once(BX_DIRECTORY_PATH_BASE . 'scripts/' . $sClassName . '.php');
            return;
        }
    }

    if (0 == strncmp($sClassName, 'BxTempl', 7)) {
        if(0 == strncmp($sClassName, 'BxTemplStudio', 13)) {
            $sPath = BX_DIRECTORY_PATH_MODULES . BxDolStudioTemplate::getInstance()->getPath() . 'data/template/studio/scripts/' . $sClassName . '.php';
        } else {
            $sPath = BX_DIRECTORY_PATH_MODULES . BxDolTemplate::getInstance()->getPath() . 'data/template/system/scripts/' . $sClassName . '.php';
        }

        if (file_exists($sPath)) {
            require_once($sPath);
            return;
        }

        trigger_error ("bx_import fatal error: class (" . $sClassName . ") wasn't found", E_USER_ERROR);
    }
}

/**
 * used in spl_autoload_register() function, so no need to call bx_import for system classes
 */
function bx_autoload($sClassName)
{
    if (0 == strncmp($sClassName, 'BxDol', 5) || 0 == strncmp($sClassName, 'BxBase', 6) || 0 == strncmp($sClassName, 'BxTempl', 7))
        bx_import($sClassName);
}

/**
 * Gets an instance of class pathing necessary parameters if it's necessary.
 *
 * @param string $sClassName class name.
 * @param array $aParams an array of parameters to be pathed to the constructor of the class.
 * @param array $aModule an array with module description. Is used when the requested class is located in some module.
 * @return unknown
 */
function bx_instance($sClassName, $aParams = array(), $mixedModule = array())
{
    if(isset($GLOBALS['bxDolClasses'][$sClassName]))
        return $GLOBALS['bxDolClasses'][$sClassName];

    if ($mixedModule) {
        if (!is_array($mixedModule)) {
            $o = BxDolModule::getInstance($mixedModule);
            $mixedModule = $o->_aModule;
        }
        $sClassName = bx_ltrim_str($sClassName, $mixedModule['class_prefix']);
        bx_import($sClassName, $mixedModule);
        $sClassName = $mixedModule['class_prefix'] . $sClassName;
    }

    $oClass = new ReflectionClass($sClassName);

    $GLOBALS['bxDolClasses'][$sClassName] = empty($aParams) ? $oClass->newInstance() : $oClass->newInstanceArgs($aParams);

    return $GLOBALS['bxDolClasses'][$sClassName];
}


/**
 * Escapes string/array ready to pass to js script with filtered symbols like ', " etc
 *
 * @param $mixedInput - string/array which should be filtered
 * @param $iQuoteType - string escaping method: BX_ESCAPE_STR_AUTO(default), BX_ESCAPE_STR_APOS or BX_ESCAPE_STR_QUOTE
 * @return converted string / array
 */
function bx_js_string ($mixedInput, $iQuoteType = BX_ESCAPE_STR_AUTO)
{
    $aUnits = array(
        "\n" => "\\n",
        "\r" => "",
    );
    if (BX_ESCAPE_STR_APOS == $iQuoteType) {
        $aUnits["'"] = "\\'";
        $aUnits['<script'] = "<scr' + 'ipt";
        $aUnits['</script>'] = "</scr' + 'ipt>";
    } elseif (BX_ESCAPE_STR_QUOTE == $iQuoteType) {
        $aUnits['"'] = '\\"';
        $aUnits['<script'] = '<scr" + "ipt';
        $aUnits['</script>'] = '</scr" + "ipt>';
    } else {
        $aUnits['"'] = '&quot;';
        $aUnits["'"] = '&apos;';
        $aUnits["<script>"] = '&lt;script&gt;';
        $aUnits["</script>"] = '&lt;/script&gt;';
    }
    return str_replace(array_keys($aUnits), array_values($aUnits), $mixedInput);
}

/**
 * Return input string/array ready to pass to html attribute with filtered symbols like ', " etc
 *
 * @param mixed $mixedInput - string/array which should be filtered
 * @return converted string / array
 */
function bx_html_attribute ($mixedInput, $iQuoteType = BX_ESCAPE_STR_AUTO)
{
    $aUnits = array ();
    if (BX_ESCAPE_STR_APOS == $iQuoteType)
        $aUnits["'"] = "\\'";
    elseif (BX_ESCAPE_STR_QUOTE == $iQuoteType)
        $aUnits['"'] = '&quot;';
    else
        $aUnits = array("\"" => "&quot;", "'" => "&apos;");

    return str_replace(array_keys($aUnits), array_values($aUnits), $mixedInput);
}

/**
 * Escapes string/array ready to pass to php script with filtered symbols like ', " etc
 *
 * @param mixed $mixedInput - string/array which should be filtered
 * @return converted string / array
 */
function bx_php_string_apos ($mixedInput)
{
    return str_replace("'", "\\'", $mixedInput);
}
function bx_php_string_quot ($mixedInput)
{
    return str_replace('"', '\\"', $mixedInput);
}

/**
 * Gets file contents by URL.
 *
 * @param string $sFileUrl - file URL to be read.
 * @param array $aParams - an array of parameters to be pathed with URL.
 * @param string $sMethod - post or get.
 * @param array $aHeaders - custom headers.
 * @return string the file's contents.
 */
function bx_file_get_contents($sFileUrl, $aParams = array(), $sMethod = 'get', $aHeaders = array(), &$sHttpCode = null, $aBasicAuth = array())
{
    $bChangeTimeout = false;

    if ('post' != $sMethod)
    	$sFileUrl = bx_append_url_params($sFileUrl, $aParams);

    $sResult = '';
    if(function_exists('curl_init')) {
        $rConnect = curl_init();

        curl_setopt($rConnect, CURLOPT_USERAGENT, 'UNA ' . BX_DOL_VERSION);
        curl_setopt($rConnect, CURLOPT_TIMEOUT, 10);
        curl_setopt($rConnect, CURLOPT_URL, $sFileUrl);
        curl_setopt($rConnect, CURLOPT_HEADER, NULL === $sHttpCode ? false : true);
        curl_setopt($rConnect, CURLOPT_RETURNTRANSFER, 1);

        if ($bChangeTimeout) {
            curl_setopt($rConnect, CURLOPT_CONNECTTIMEOUT, 3);
            curl_setopt($rConnect, CURLOPT_TIMEOUT, 3);
        }

        if (!ini_get('open_basedir'))
            curl_setopt($rConnect, CURLOPT_FOLLOWLOCATION, 1);

        if ($aHeaders)
            curl_setopt($rConnect, CURLOPT_HTTPHEADER, $aHeaders);

        if ($aBasicAuth)
            curl_setopt($rConnect, CURLOPT_USERPWD, $aBasicAuth['user'] . ':' . $aBasicAuth['password']);

        if ('post' == $sMethod) {
            curl_setopt($rConnect, CURLOPT_POST, true);
            curl_setopt($rConnect, CURLOPT_POSTFIELDS, http_build_query($aParams));
        }

        $sAllCookies = '';
        foreach($_COOKIE as $sKey=>$sValue){
            $sAllCookies .= $sKey . '=' . $sValue . ';';
        }
        curl_setopt($rConnect, CURLOPT_COOKIE, $sAllCookies);

        $sResult = curl_exec($rConnect);

        if (curl_errno($rConnect) == 60) { // CURLE_SSL_CACERT
            curl_setopt($rConnect, CURLOPT_CAINFO, BX_DIRECTORY_PATH_PLUGINS . 'curl/cacert.pem');
            $sResult = curl_exec($rConnect);
        }

        if (NULL !== $sHttpCode)
            $sHttpCode = curl_getinfo($rConnect, CURLINFO_HTTP_CODE);

        curl_close($rConnect);
    }
    else {

        $iSaveTimeout = false;
        if ($bChangeTimeout) {
            $iSaveTimeout = ini_get('default_socket_timeout');
            ini_set('default_socket_timeout', 3);
        }

        $sResult = @file_get_contents($sFileUrl);

        if ($bChangeTimeout && false !== $iSaveTimeout) {
            ini_set('default_socket_timeout', $iSaveTimeout);
        }
    }

    return $sResult;
}

function bx_get_site_info($sSourceUrl, $aProcessAdditionalTags = array())
{
    $aResult = array();
    $sContent = bx_file_get_contents($sSourceUrl);

    if ($sContent) {
        $sCharset = '';
        preg_match("/<meta.+charset=([A-Za-z0-9-]+).+>/i", $sContent, $aMatch);
        if (isset($aMatch[1]))
            $sCharset = $aMatch[1];

        if (preg_match("/<title[^>]*>(.*)<\/title>/i", $sContent, $aMatch))
            $aResult['title'] = $aMatch[1];
        else
            $aResult['title'] = parse_url($sSourceUrl, PHP_URL_HOST);

        $aResult['description'] = bx_parse_html_tag($sContent, 'meta', 'name', 'description', 'content', $sCharset);
        $aResult['keywords'] = bx_parse_html_tag($sContent, 'meta', 'name', 'keywords', 'content', $sCharset);

        if ($aProcessAdditionalTags) {

            foreach ($aProcessAdditionalTags as $k => $a) {
                $aResult[$k] = bx_parse_html_tag(
                    $sContent, 
                    isset($a['tag']) ? $a['tag'] : 'meta', 
                    isset($a['name_attr']) ? $a['name_attr'] : 'itemprop', 
                    isset($a['name']) ? $a['name'] : $k, 
                    isset($a['content_attr']) ? $a['content_attr'] : 'content', 
                    $sCharset); 
            }

        }
    }

    return $aResult;
}

function bx_parse_html_tag ($sContent, $sTag, $sAttrNameName, $sAttrNameValue, $sAttrContentName, $sCharset = false)
{
    if (!preg_match("/<{$sTag}\s+{$sAttrNameName}[='\" ]+{$sAttrNameValue}['\"]\s+{$sAttrContentName}[='\" ]+([^'>\"]*)['\"][^>]*>/i", $sContent, $aMatch) || !isset($aMatch[1]))
        preg_match("/<{$sTag}\s+{$sAttrContentName}[='\" ]+([^'>\"]*)['\"]\s+{$sAttrNameName}[='\" ]+{$sAttrNameValue}['\"][^>]*>/i", $sContent, $aMatch);

    $s = isset($aMatch[1]) ? $aMatch[1] : '';

    if ($s && $sCharset)
        $s = mb_convert_encoding($s, 'UTF-8', $sCharset);

    return $s;
}

// calculation ini_get('upload_max_filesize') in bytes as example
function return_bytes($val)
{
    $val = trim($val);
    $last = strtolower($val{strlen($val)-1});
    $val = (int)$val;
    switch($last) {
        // The 'G' modifier is available since PHP 5.1.0
        case 'k':
            $val *= 1024;
            break;
        case 'm':
            $val *= 1024 * 1024;
            break;
        case 'g':
            $val *= 1024 * 1024 * 1024;
            break;
    }
    return $val;
}

// Generate Random Password
function genRndPwd($iLength = 8, $bSpecialCharacters = true)
{
    $sPassword = '';
    $sChars = "abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789";

    if($bSpecialCharacters === true)
        $sChars .= "!?=/&+,.";

    srand((double)microtime()*1000000);
    for($i = 0; $i < $iLength; $i++) {
        $x = mt_rand(0, strlen($sChars) -1);
        $sPassword .= $sChars{$x};
    }

    return $sPassword;
}

// Generate Random Salt for Password encryption
function genRndSalt()
{
    return genRndPwd(8, true);
}

// Encrypt User Password
function encryptUserPwd($sPwd, $sSalt)
{
	$sAlgo = defined('BX_PWD_ALGO') ? BX_PWD_ALGO : '';

    switch ($sAlgo) {
    	case 'crypt':
            return crypt($sPwd, BX_PWD_ALGO_SALT);

        case 'sha1_crypt_salt':
            return sha1(crypt($sPwd, BX_PWD_ALGO_SALT) . $sSalt);

        case 'sha1_md5_salt':
        default:
            return sha1(md5($sPwd) . $sSalt);
    }
}

function bx_get ($sName, $sMethod = false)
{
    if (isset($_GET[$sName]) && ('get' == $sMethod || !$sMethod))
        return $_GET[$sName];
    elseif (isset($_POST[$sName]) && ('post' == $sMethod || !$sMethod))
        return $_POST[$sName];
    else
        return false;
}

function bx_encode_url_params ($a, $aExcludeKeys = array (), $aOnlyKeys = false)
{
    $s = '';
    foreach ($a as $sKey => $sVal) {
        if (in_array($sKey, $aExcludeKeys))
            continue;
        if (false !== $aOnlyKeys && !in_array($sKey, $aOnlyKeys))
            continue;
        if (is_array($sVal)) {
            foreach ($sVal as $sSubVal) {
                $s .= rawurlencode($sKey) . '[]=' . rawurlencode(is_array($sSubVal) ? 'array' : $sSubVal) . '&';
            }
        } else {
            $s .= rawurlencode($sKey) . '=' . rawurlencode($sVal) . '&';
        }
    }
    return $s;
}

function bx_append_url_params ($sUrl, $mixedParams)
{
    if (!$mixedParams)
        return $sUrl;

    $sParams = false == strpos($sUrl, '?') ? '?' : '&';

    if (is_array($mixedParams)) {
        foreach($mixedParams as $sKey => $sValue) {
            if (!is_array($sValue)) {
                $sParams .= $sKey . '=' . $sValue . '&';
            }
            else {
                foreach($sValue as $sSubValue)
                    $sParams .= $sKey . '[]=' . $sSubValue . '&';
            }
        }
        $sParams = substr($sParams, 0, -1);
    } else {
        $sParams .= $mixedParams;
    }
    return $sUrl . $sParams;
}

function bx_rrmdir($directory)
{
    if (substr($directory,-1) == "/")
        $directory = substr($directory,0,-1);

    if (!file_exists($directory) || !is_dir($directory))
        return false;
    elseif (!is_readable($directory))
        return false;

    if (!($directoryHandle = opendir($directory)))
        return false;

    while ($contents = readdir($directoryHandle)) {
        if ($contents != '.' && $contents != '..') {
            $path = $directory . "/" . $contents;

            if (is_dir($path))
                bx_rrmdir($path);
            else
                unlink($path);
        }
    }

    closedir($directoryHandle);

    if (!rmdir($directory))
        return false;

    return true;
}

function bx_clear_folder ($sPath, $aExts = array ())
{
    if (substr($$sPath,-1) == "/")
        $sPath = substr($sPath,0,-1);

    if (!file_exists($sPath) || !is_dir($sPath))
        return false;
    elseif (!is_readable($sPath))
        return false;

    if (!($h = opendir($sPath)))
        return false;

    while ($sFile = readdir($h)) {
        if ('.' == $sFile || '..' == $sFile)
            continue;

        $sFullPath = $sPath . '/' . $sFile;

        if (is_dir($sFullPath))
            continue;

        if (!$aExts || (($sExt = pathinfo($sFullPath, PATHINFO_EXTENSION)) && in_array($sExt, $aExts)))
            @unlink($sFullPath);
    }

    closedir($h);

    return true;
}

function bx_ltrim_str ($sString, $sPrefix, $sReplace = '')
{
    if ($sReplace && substr($sString, 0, strlen($sReplace)) == $sReplace)
        return $sString;
    if (substr($sString, 0, strlen($sPrefix)) == $sPrefix)
        return $sReplace . substr($sString, strlen($sPrefix));
    return $sString;
}

function bx_rtrim_str ($sString, $sPrefix, $sReplace = '')
{
    if ($sReplace && substr($sString, -strlen($sReplace)) == $sReplace)
        return $sString;
    if (substr($sString, -strlen($sPrefix)) == $sPrefix)
        return substr($sString, 0, -strlen($sPrefix)) . $sReplace;
    return $sString;
}

/**
 * Convert array to attributes string
 *
 * Example:
 * @code
 * $a = array('name' => 'test', 'value' => 5);
 * $s = bx_convert_array2attrs($a);
 * echo $s; // outputs: name="test" value="5"
 * @endcode
 *
 * @param $a - array of attributes
 * @param $sClasses - classes to merge with 'class' attribute
 * @param $sStyles - styles to merge with 'style' attribute
 * @return string
 */
function bx_convert_array2attrs ($a, $sClasses = false, $sStyles = false)
{
    $sRet = '';

    if (!$a || !is_array($a))
        $a = array();

    if ($sClasses) {
        $sClasses = trim($sClasses);
        $a['class'] = $sClasses . (!empty($a['class']) ? ' ' . $a['class'] : '');
    }

    if ($sStyles) {
        $sStyles = trim($sStyles);
        if (';' != $sStyles[strlen($sStyles)-1])
            $sStyles .= ';';
        $a['style'] = $sStyles . (!empty($a['style']) ? ' ' . $a['style'] : '');
    }

    foreach ($a as $sKey => $sValue) {
        if(is_null($sValue)) // pass NULL values
            continue;

        $sValueC = bx_html_attribute($sValue, BX_ESCAPE_STR_QUOTE);

        $sRet .= " $sKey=\"$sValueC\"";
    }

    return $sRet;
}

function bx_unicode_urldecode($s)
{
    preg_match_all('/%u([[:alnum:]]{4})/', $s, $a);

    foreach ($a[1] as $uniord) {
        $dec = hexdec($uniord);
        $utf = '';

        if ($dec < 128) {
            $utf = chr($dec);
        } else if ($dec < 2048) {
            $utf = chr(192 + (($dec - ($dec % 64)) / 64));
            $utf .= chr(128 + ($dec % 64));
        } else {
            $utf = chr(224 + (($dec - ($dec % 4096)) / 4096));
            $utf .= chr(128 + ((($dec % 4096) - ($dec % 64)) / 64));
            $utf .= chr(128 + ($dec % 64));
        }

        $s = str_replace('%u'.$uniord, $utf, $s);
    }

    return urldecode($s);
}

/**
 * Raise an alert
 * @param string $sType - system type
 * @param string $sAction - system action
 * @param int $iObjectId - object id
 * @param int $iSenderId - sender (action's author) profile id, if it is false - then currectly logged in profile id is used
 */
function bx_alert($sUnit, $sAction, $iObjectId, $iSender = false, $aExtras = array())
{
    $o = new BxDolAlerts($sUnit, $sAction, $iObjectId, $iSender, $aExtras);
    $o->alert();
}

function bx_replace_markers($mixed, $aMarkers)
{
    if (empty($aMarkers))
        return $mixed;

    if (is_array($mixed)) {
        foreach ($mixed as $sKey => $sValue)
            $mixed[$sKey] = bx_replace_markers ($sValue, $aMarkers);
    } else {
        foreach ($aMarkers as $sKey => $sValue)
            $mixed = str_replace('{' . $sKey . '}', $sValue, $mixed);
    }

    return $mixed;
}

function bx_site_hash($sSalt = '', $isSkipVersion = false)
{
    return md5($sSalt . ($isSkipVersion ? '' : bx_get_ver()) . BX_DOL_SECRET . BX_DOL_URL_ROOT);
}

/**
 * Transform string to method name string, for example it changes 'some_method' string to 'SomeMethod' string
 * @param array where words are separated with underscore
 * @return string where every word begins with capital letter
 */
function bx_gen_method_name ($s, $aWordsDelimiter = array('_'))
{
    return str_replace(' ', '', ucwords(str_replace($aWordsDelimiter, ' ', $s)));
}

/**
 * Trigger user error
 * @param $sMsg message to display
 * @param $iNumLevelsBack add additional debug backtracing N levels back
 */
function bx_trigger_error ($sMsg, $iNumLevelsBack = 0)
{
    $a = debug_backtrace();
    $sMsgAdd = "<br />\n related code in <b>{$a[$iNumLevelsBack]['file']}</b> on line <b>{$a[$iNumLevelsBack]['line']}</b> <br />\n";
    trigger_error ($sMsg . $sMsgAdd, E_USER_ERROR);
}

/**
 * Get system DB version, for files version @see BX_DOL_VERSION, these versions must match
 */
function bx_get_ver ($bInvalidateCache = false)
{
    $oDb = BxDolDb::getInstance();

    if ($bInvalidateCache)
        $oDb->cleanMemory('sys_version');
    
    $sQuery = $oDb->prepare("SELECT `version` FROM `sys_modules` WHERE `name` = 'system'");
    return $oDb->fromMemory('sys_version', 'getOne', $sQuery);
}

/**
 * Check if site maintetance mode is enabled.
 * Maintetance mode is enabled when '.bx_maintenance' file exists in the script root folder, 
 * please note that this is hidden file and some file managers don't show it.
 * @param $bShowHttpError show 503 HTTP error if site is in mainenance mode
 * @return true if site is in maintenance mode, or false otherwise
 */
function bx_check_maintenance_mode ($bShowHttpError = false)
{
    $bMaintetance = file_exists(BX_DIRECTORY_PATH_ROOT . BX_MAINTENANCE_FILE) && !defined('BX_DOL_UPGRADING');

    if ($bMaintetance && $bShowHttpError)
        bx_show_service_unavailable_error_and_exit ('Site is temporarily unavailable due to scheduled maintenance, please try again in a minute.', 600);

    return $bMaintetance;
}

/**
 * Check for minimal requirements.
 * if BX_DISABLE_REQUIREMENTS_CHECK is defined then this requirements checking is skipped.
 * @param $bShowHttpError show 503 HTTP error if site doesn't meet minimal requirements
 * @return false if requirements are met, or array of errors of requirements aren't met
 */
function bx_check_minimal_requirements ($bShowHttpError = false)
{
    if (defined('BX_DISABLE_REQUIREMENTS_CHECK'))
        return false;

    $aErrors = array();

    $aErrors[] = (ini_get('register_globals') == 0) ? '' : '<b>register_globals</b> is on (you need to disable it, or your site will be unsafe)';
    $aErrors[] = (ini_get('safe_mode') == 0) ? '' : '<b>safe_mode</b> is on (you need to disable it)';
    $aErrors[] = (version_compare(PHP_VERSION, '5.3.0', '<')) ? 'PHP version is too old (please update to <b>PHP 5.3.0</b> at least)' : '';
    $aErrors[] = (!extension_loaded( 'mbstring')) ? '<b>mbstring</b> extension not installed (the script cannot work without it)' : '';
    $aErrors[] = (ini_get('allow_url_include') == 0) ? '' : '<b>allow_url_include</b> is on (you need to disable it, or your site will be unsafe)';

    $aErrors = array_diff($aErrors, array('')); // delete empty

    $bFailedMinimalRequirements = !empty($aErrors);

    if ($bFailedMinimalRequirements && $bShowHttpError) {
        $sErrors = implode(" <br /> ", $aErrors);
        bx_show_service_unavailable_error_and_exit($sErrors);
    }

    return $bFailedMinimalRequirements ? $aErrors : false;
}

/**
 * Check if redirect to the correct hostname is required, for example redirect from site.com to www.site.com
 * @param $bProcessRedirect process redirect and exit if needed
 */
function bx_check_redirect_to_correct_hostname ($bProcessRedirect = false)
{
    $aUrl = parse_url(BX_DOL_URL_ROOT);
    $iPortDefault = 'https' == $aUrl['scheme'] ? '443' : '80';

    $bRedirectRequired = isset($_SERVER['HTTP_HOST']) && 0 != strcasecmp($_SERVER['HTTP_HOST'], $aUrl['host']) && 0 != strcasecmp($_SERVER['HTTP_HOST'], $aUrl['host'] . ':' . (!empty($aUrl['port']) ? $aUrl['port'] : $iPortDefault));

    if ($bRedirectRequired && $bProcessRedirect) {
        $sPort = empty($aUrl['port']) || 80 == $aUrl['port'] || 443 == $aUrl['port'] ? '' : ':' . $aUrl['port'];
        header("Location:{$aUrl['scheme']}://{$aUrl['host']}{$sPort}{$_SERVER['REQUEST_URI']}", true, 301);
        exit;
    }
    
    return $bRedirectRequired;
}

/**
 * Check if redirect to remove install folder.
 * If BX_SKIP_INSTALL_CHECK is defined then this redirect checking is skipped.
 * @param $bProcessRedirect process redirect and exit if needed
 */
function bx_check_redirect_to_remove_install_folder ($bProcessRedirect = false)
{
    $bRemoveInstallFolder = !defined ('BX_SKIP_INSTALL_CHECK') && file_exists(BX_DIRECTORY_PATH_ROOT . 'install');

    if ($bRemoveInstallFolder && $bProcessRedirect) {
        header('Location:' . BX_DOL_URL_ROOT . 'install/index.php?action=remove_install');
        exit;
    }

    return $bRemoveInstallFolder;
}

/**
 * Show HTTP 503 service unavailable error and exit
 */
function bx_show_service_unavailable_error_and_exit ($sMsg = false, $iRetryAfter = 86400)
{
    header('HTTP/1.0 503 Service Unavailable', true, 503);
    header('Retry-After: 600');
    echo $sMsg ? $sMsg : 'Service temporarily unavailable';
    exit;
}

/**
 * The function is sumilar to php readfile, but it send all required headers and can send file by chunks and suports file seek
 * @param $sPath path to file to output to the browser
 * @param $sFilename filename without path, ig file is saved from browser, then this name is used, not used(empty) by default
 * @param $sMimeType file mime type, by default 'application/octet-stream'
 * @param $iCacheAge file cache age, by default 0
 * @param $sCachePrivacy cache privacy 'public' (default value) or 'private'
 * @return true on success or false on error
 */
function bx_smart_readfile($sPath, $sFilename = '', $sMimeType = 'application/octet-stream', $iCacheAge = 0, $sCachePrivacy = 'public')
{
    if (!file_exists($sPath))
        return  false;

    $fp = @fopen($sPath, 'rb');

    $size   = filesize($sPath);
    $length = $size;
    $start  = 0;
    $end    = $size - 1;

    header('Content-type: ' . $sMimeType);
    header('Cache-Control: ' . $sCachePrivacy . ', must-revalidate, max-age=' . $iCacheAge);
    header("Accept-Ranges: 0-$length");
    if ($sFilename)
        header('Content-Disposition: inline; filename=' . $sFilename);

    if (isset($_SERVER['HTTP_RANGE'])) {

        $c_start = $start;
        $c_end   = $end;

        list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
        if (strpos($range, ',') !== false) {
            header('HTTP/1.1 416 Requested Range Not Satisfiable');
            header("Content-Range: bytes $start-$end/$size");
            return false;
        }
        if ($range == '-') {
            $c_start = $size - substr($range, 1);
        }else{
            $range  = explode('-', $range);
            $c_start = $range[0];
            $c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
        }
        $c_end = ($c_end > $end) ? $end : $c_end;
        if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
            header('HTTP/1.1 416 Requested Range Not Satisfiable');
            header("Content-Range: bytes $start-$end/$size");
            return false;
        }
        $start  = $c_start;
        $end    = $c_end;
        $length = $end - $start + 1;
        fseek($fp, $start);
        header('HTTP/1.1 206 Partial Content');
    }
    header("Content-Range: bytes $start-$end/$size");
    header("Content-Length: ".$length);


    $buffer = 1024 * 8;
    while(!feof($fp) && ($p = ftell($fp)) <= $end) {

        if ($p + $buffer > $end) {
            $buffer = $end - $p + 1;
        }
        set_time_limit(0);
        echo fread($fp, $buffer);
        flush();
    }

    fclose($fp);

    return true;
}

/**
 * Wrap in A tag links in TEXT string
 * @param $sHtmlOrig - text string without tags
 * @param $sAttrs - attributes string to add to the added A tag
 * @return string where all links are wrapped in A tag
 */
function bx_linkify($text, $sAttrs = '', $bHtmlSpecialChars = false)
{
    if ($bHtmlSpecialChars)
        $text = htmlspecialchars($text, ENT_NOQUOTES, 'UTF-8');

    $re = "@\b((https?://)|(www\.))(([0-9a-zA-Z_!~*'().&=+$%-]+:)?[0-9a-zA-Z_!~*'().&=+$%-]+\@)?(([0-9]{1,3}\.){3}[0-9]{1,3}|([0-9a-zA-Z_!~*'()-]+\.)*([0-9a-zA-Z][0-9a-zA-Z-]{0,61})?[0-9a-zA-Z]\.[a-zA-Z]{2,6})(:[0-9]{1,4})?((/[0-9a-zA-Z_!~*'().;?:\@&=+$,%#-]+)*/?)@";
    preg_match_all($re, $text, $matches, PREG_OFFSET_CAPTURE);

    $matches = $matches[0];

    if ($i = count($matches))
        $bAddNofollow = getParam('sys_add_nofollow') == 'on';

    while ($i--)
    {
        $url = $matches[$i][0];
        if (!preg_match('@^https?://@', $url))
            $url = 'http://'.$url;

        if (strncmp(BX_DOL_URL_ROOT, $url, strlen(BX_DOL_URL_ROOT)) != 0) {
            $sAttrs .= ' target="_blank" ';
            if ($bAddNofollow)
                $sAttrs .= ' rel="nofollow" ';
        }

        $text = substr_replace($text, '<a ' . $sAttrs . ' href="'.$url.'">'.$matches[$i][0].'</a>', $matches[$i][1], strlen($matches[$i][0]));
    }

    return $text;
}

/**
 * Wrap in A tag links in HTML string, which aren't wrapped in A tag yet
 * @param $sHtmlOrig - HTML string
 * @param $sAttrs - attributes string to add to the added A tag
 * @return modified HTML string, in case of errror original string is returned
 */
function bx_linkify_html($sHtmlOrig, $sAttrs = '') 
{
    if (!trim($sHtmlOrig))
        return $sHtmlOrig;

    $sId = 'bx-linkify-' . md5(microtime());
    $dom = new DOMDocument();
    @$dom->loadHTML('<?xml encoding="UTF-8"><div id="' . $sId . '">' . $sHtmlOrig . '</div>');
    $xpath = new DOMXpath($dom);

    foreach ($xpath->query('//text()') as $text) {
        $frag = $dom->createDocumentFragment();
        $frag->appendXML(bx_linkify($text->nodeValue, $sAttrs, true));
        $text->parentNode->replaceChild($frag, $text);
    }

    if (version_compare(PHP_VERSION, '5.3.6') >= 0)
        $s = $dom->saveHTML($dom->getElementById($sId));
    else
        $s = $dom->saveXML($dom->getElementById($sId), LIBXML_NOEMPTYTAG);

    if (false === $s) // in case of error return original string
        return $sHtmlOrig;

    if (false !== ($iPos = mb_strpos($s, '<html><body>')) && $iPos < mb_strpos($s, $sId))
        $s = mb_substr($s, $iPos + 12, -15); // strip <html><body> tags and everything before them

    return mb_substr($s, 54, -6); // strip added tags
}

/** @} */
