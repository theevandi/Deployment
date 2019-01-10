<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaBaseView UNA Base Representation Classes
 * @{
 */

class BxBaseFunctions extends BxDolFactory implements iBxDolSingleton
{
    protected $_oTemplate;
    protected $_sDesignBoxIcon = 'chevron';

    protected function __construct($oTemplate)
    {
        if (isset($GLOBALS['bxDolClasses'][get_class($this)]))
            trigger_error ('Multiple instances are not allowed for the class: ' . get_class($this), E_USER_ERROR);

        parent::__construct();

        $this->_oTemplate = $oTemplate ? $oTemplate : BxDolTemplate::getInstance();
    }

    /**
     * Prevent cloning the instance
     */
    public function __clone()
    {
        if (isset($GLOBALS['bxDolClasses'][get_class($this)]))
            trigger_error('Clone is not allowed for the class: ' . get_class($this), E_USER_ERROR);
    }

    public static function getInstanceWithTemplate($oTemplate)
    {
        if(!isset($GLOBALS['bxDolClasses']['BxTemplFunctions']))
            $GLOBALS['bxDolClasses']['BxTemplFunctions'] = new BxTemplFunctions($oTemplate);

        return $GLOBALS['bxDolClasses']['BxTemplFunctions'];
    }
    
    public static function getInstance()
    {
        return self::getInstanceWithTemplate(null);
    }

    function TemplPageAddComponent($sKey)
    {
        switch( $sKey ) {
            case 'something':
                return false; // return here additional components
            default:
                return false; // if you have not such component, return false!
        }
    }

    function msgBox($sText, $iTimer = 0, $sOnClose = "")
    {
        $iId = time() . mt_rand(1, 1000);

        if($iTimer > 0)
            BxDolTemplate::getInstance()->addJs(array('jquery.anim.js'));

        return $this->_oTemplate->parseHtmlByName('messageBox.html', array(
            'id' => $iId,
            'msgText' => $sText,
            'bx_if:timer' => array(
                'condition' => $iTimer > 0,
                'content' => array(
                    'id' => $iId,
                    'time' => 1000 * $iTimer,
                    'on_close' => $sOnClose,
                )
            )
        ));
    }

    /**
     * Get standard popup box with title.
     *
     * @param  string $sName    - unique name
     * @param  string $sTitle   - translated title
     * @param  string $sContent - content of the box
     * @return HTML   string
     */
    function popupBox($sName, $sTitle, $sContent, $isHiddenByDefault = false)
    {
        $iId = !empty($sName) ? $sName : time();

        return $this->_oTemplate->parseHtmlByName('popup_box.html', array(
            'id' => $iId,
            'wrapper_style' => $isHiddenByDefault ? 'display:none;' : '',
            'title' => $sTitle,
            'content' => $sContent
        ));
    }

    /**
     * Get popup box without title.
     *
     * @param  string $sName    - unique name
     * @param  string $sContent - content of the box
     * @return HTML   string
     */
    function transBox($sName, $sContent, $isHiddenByDefault = false, $isPlaceInCenter = false)
    {
    	return $this->simpleBox($sName, $sContent, $isHiddenByDefault, $isPlaceInCenter, 'popup_trans.html');
    }

    function slideBox($sName, $sContent, $isHiddenByDefault = false)
    {
    	return $this->simpleBox($sName, $sContent, $isHiddenByDefault, false, 'popup_slide.html');
    }

	function inlineBox($sName, $sContent, $isHiddenByDefault = false)
    {
        return $this->simpleBox($sName, $sContent, $isHiddenByDefault, false, 'popup_inline.html');
    }

    protected function simpleBox($sName, $sContent, $isHiddenByDefault, $isPlaceInCenter, $sTemplate) {
    	$iId = !empty($sName) ? $sName : time();

		$sContent = $this->_oTemplate->parseHtmlByName($sTemplate, array(
            'id' => $iId,
            'wrapper_style' => $isHiddenByDefault ? 'display:none;' : '',
            'content' => $sContent
        ));

        if($isPlaceInCenter)
        	$sContent = '<div class="login_ajax_wrap">' . $sContent . '</div>';

        return $sContent;
    }

    function getTemplateIcon($sName)
    {
        $sUrl = $this->_oTemplate->getIconUrl($sName);
        return !empty($sUrl) ? $sUrl : $this->_oTemplate->getIconUrl('spacer.gif');
    }

    function getTemplateImage($sName)
    {
        $sUrl = $this->_oTemplate->getImageUrl($sName);
        return !empty($sUrl) ? $sUrl : $this->_oTemplate->getImageUrl('spacer.gif');
    }

    function sysIcon ($sIcon, $sName, $sUrl = '', $iWidth = 0)
    {
        return '<div class="sys_icon">' . ($sUrl ? '<a title="'.$sName.'" href="'.$sUrl.'">' : '') . '<img alt="'.$sName.'" src="'.$sIcon.'" '.($iWidth ? 'width='.$iWidth : '').' />' . ($sUrl ? '</a>' : '') . '</div>';
    }

    /**
     * functions for limiting maximal string length
     */
    function getStringWithLimitedLength($sString, $iWidth = 45, $isPopupOnOverflow = false, $bReturnString = true)
    {
        if (empty($sString) || mb_strlen($sString, 'UTF-8') <= $iWidth)
            return $bReturnString ? $sString : array($sString);

        $sResult = '';
        $aWords = mb_split("[\s\r\n]", $sString);
        $iPosition = 0;
        $iWidthReal = $iWidth - 3;
        $iWidthMin = $iWidth - 15;
        foreach($aWords as $sWord) {
            $sWord = trim($sWord);
            $iWord = mb_strlen($sWord, 'UTF-8');
            if ($iPosition + $iWord > $iWidthReal)
                break;

            // add word and continue
            $sResult .= ' ' . $sWord;
            $iPosition += 1 + $iWord;
        }

        // last word is too long, cut it
        if(!$iPosition || $iPosition < $iWidthMin)
            $sResult .= ' ' . mb_substr($sWord, 0, $iWidthReal - $iPosition - $iWord, 'UTF-8');
        $sResult = trim($sResult);

        // add tripple dot
        if(!$isPopupOnOverflow) {
            $sResult .= '...';
            return $bReturnString ? $sResult : array($sResult);
        }

        // add button width popup
        $sId = 'bx-str-limit-' . rand(1, PHP_INT_MAX);
        $sPopup = '<img class="bx-str-limit" onclick="$(\'#' . $sId . '\').dolPopup({pointer:{el:$(this), offset:\'10 1\'}})" src="' . $this->getTemplateImage('str-limit.png') . '"/>';
        $sPopup .= '<div id="' . $sId . '" style="display:none;">' . BxTemplFunctions::getInstance()->transBox('', '<div class="bx-def-padding bx-def-color-bg-block">'.$sString.'</div>') . '</div>';

        return $bReturnString ? $sResult . $sPopup : array($sResult, $sPopup);
    }

    /**
     * Display design box with specified title, template, content and menu.
     * @param $sTitle - design box title, please note that some templates don't use title.
     * @param $sContent - design box content.
     * @param $iTemplateNum - number of design box template, use predefined contants only, default is BX_DB_DEF.
     * @param $mixedMenu - design box menu, it can be:
     *      - object: instance of BxTemplMenu class
     *      - string: menu object identifier
     *      - array: array of menu links to create menu from
     * @return string
     *
     * @see BX_DB_CONTENT_ONLY
     * @see BX_DB_DEF
     * @see BX_DB_EMPTY
     * @see BX_DB_NO_CAPTION
     * @see BX_DB_PADDING_CONTENT_ONLY
     * @see BX_DB_PADDING_DEF
     * @see BX_DB_PADDING_NO_CAPTION
     */
    function designBoxContent ($sTitle, $sContent, $iTemplateNum = BX_DB_DEF, $mixedMenu = false)
    {
        return $this->_oTemplate->parseHtmlByName('designbox_' . (int)$iTemplateNum . '.html', array(
            'title' => $sTitle,
            'designbox_content' => $sContent,
            'caption_item' => $this->designBoxMenu ($mixedMenu, array (array('menu' => 1))),
        ));
    }

    function designBoxMenu ($mixedMenu, $aButtons = array ())
    {
        $sCode = '';
        $aButtonMenu = false;
        if ($mixedMenu) {

            $sMenu = '';

            if (is_string($mixedMenu)) {

                $oMenu = BxTemplMenu::getObjectInstance($mixedMenu);
                $sMenu = $oMenu ? $oMenu->getCode () : $mixedMenu;

            } elseif (is_object($mixedMenu) && is_a($mixedMenu, 'BxTemplMenu')) {

                $sMenu = $mixedMenu->getCode();

            } elseif (is_array($mixedMenu)) {

                if (isset($mixedMenu['template']) && isset($mixedMenu['menu_items']))
                    $aMenu = $mixedMenu;
                else
                    $aMenu = array ('template' => 'menu_vertical.html', 'menu_items' => $mixedMenu);

                $oMenu = new BxTemplMenu($aMenu, $this->_oTemplate);
                $sMenu = $oMenu->getCode ();
            }

            if ($sMenu) {
                $bButtonMenu = !empty($aButtons['menu']) && is_array($aButtons['menu']);

                $sId = $bButtonMenu && !empty($aButtons['menu']['id']) ? $aButtons['menu']['id'] : 'bx-menu-db-' . time() . rand(0, PHP_INT_MAX);
                $sCode .= $this->slideBox($sId, $sMenu, true);

                $aButtonMenu = array('icon-a' => $this->_sDesignBoxIcon, 'onclick' => "bx_menu_slide_inline('#" . $sId . "', this)");
                if($bButtonMenu) {
                    $aButtonMenu = array_merge($aButtonMenu, $aButtons['menu']);
                    $aButtons[] = array('menu' => 1);

                    unset($aButtons['menu']);
                }
            }

        }

        if ($aButtons) {
            $sCode .= '<div class="bx-db-menu"><div class="bx-db-menu-tab bx-db-menu-tab-btn">';
            foreach ($aButtons as $aButton) {
                if (isset($aButton['menu']) && $aButton['menu']) {
                    if (!$aButtonMenu)
                        continue;

                    $aButton = $aButtonMenu;
                }

                $aAttrs = array ();
                if (!empty($aButton['onclick']))
                    $aAttrs['onclick'] = $aButton['onclick'];
                if (!empty($aButton['class']))
                    $aAttrs['class'] = $aButton['class'];
                $sAttrs = bx_convert_array2attrs ($aAttrs, 'bx-def-margin-sec-left');

                $sCode .= '<a href="javascript:void(0);" ' . $sAttrs . '>';
                $sCode .= !empty($aButton['icon']) ? '<i class="sys-icon ' . $aButton['icon'] . ' bx-def-font-h2"></i>' : '';
                $sCode .= !empty($aButton['icon-a']) ? '<i class="sys-icon-a" data-rotate="down" data-icon="' . $aButton['icon-a'] . '"></i>' : '';
                $sCode .= !empty($aButton['title']) ? $aButton['title'] : '';
                $sCode .= '</a>';
            }
            $sCode .= '</div></div>';
        }

        return $sCode;
    }

    /**
     * Get logo URL.
     * @return string
     */
    function getMainLogoUrl()
    {
		$oDesigns = BxDolDesigns::getInstance();

        $iFileId = (int)$oDesigns->getSiteLogo();
        if (!$iFileId) 
            return false;

        $oStorage = BxDolTranscoder::getObjectInstance('sys_custom_images');
        $sFileUrl = $oStorage->getFileUrl($iFileId);
        if (!$sFileUrl) 
            return false;

		$iLogoWidth = (int)$oDesigns->getSiteLogoWidth();
        if($iLogoWidth > 0)
            $sFileUrl = bx_append_url_params($sFileUrl, array('x' => $iLogoWidth));

		$iLogoHeight = (int)$oDesigns->getSiteLogoHeight();
        if($iLogoHeight > 0)
            $sFileUrl = bx_append_url_params($sFileUrl, array('y' => $iLogoHeight));

        return $sFileUrl;
    }

    /**
     * Get logo HTML.
     * @return string
     */
    function getMainLogo()
    {
		$oDesigns = BxDolDesigns::getInstance();

        $sAlt = $oDesigns->getSiteLogoAlt();
        if(empty($sAlt))
        	$sAlt = getParam('site_title');

        $sLogo = '<span>' . $sAlt . '</span>';

        $sFileUrl = $this->getMainLogoUrl();
        if (!empty($sFileUrl)) {
        	$iLogoWidth = (int)$oDesigns->getSiteLogoWidth();
            $sMaxWidth = $iLogoWidth > 0 ? 'max-width:' . round($iLogoWidth/16, 3) . 'rem;' : '';

            $iLogoHeight = (int)$oDesigns->getSiteLogoHeight();
            $sMaxHeight = $iLogoHeight > 0 ? 'max-height:' . round($iLogoHeight/16, 3) . 'rem;' : '';

            $sLogo = '<img style="' . $sMaxWidth . $sMaxHeight . '" src="' . $sFileUrl . '" id="bx-logo" alt="' . bx_html_attribute($sAlt, BX_ESCAPE_STR_QUOTE) . '" />';
        }

        return '<a class="bx-def-font-contrasted" href="' . BX_DOL_URL_ROOT . '" title="' . bx_html_attribute($sAlt, BX_ESCAPE_STR_QUOTE) . '">' . $sLogo . '</a>';
    }

    /**
     * Get HTML code for manifests.
     * @return HTML string to insert into HEAD section
     */
    function getManifests()
    {
        $sRet = '';
        $bLogged = isLogged();

        /*
         * OneSignal manifest must appear before any other <link rel="manifest" ...> in <head>
         */
        $sPushAppId = getParam('sys_push_app_id');
        if($bLogged && !empty($sPushAppId)) {
            $aUrlRoot = parse_url(BX_DOL_URL_ROOT);

            $sUrl = BX_DOL_URL_PLUGINS_PUBLIC .  'onesignal/manifest.json.php';
            $sUrl = bx_append_url_params($sUrl, array('bx_name' => $aUrlRoot['host']));

            $sRet .= '<link rel="manifest" href="' . $sUrl . '" />';
        }

        return $sRet;
    }

    /**
     * Get HTML code for meta icons.
     * @return HTML string to insert into HEAD section
     */
    function getMetaIcons()
    {
        $iId = (int)getParam('sys_site_icon');
        $sImageUrlFav = $sImageUrlFcb = $sImageUrlApl = '';

        if(!empty($iId)) {

            // favicon icon
            $oTranscoder = BxDolTranscoderImage::getObjectInstance(BX_DOL_TRANSCODER_OBJ_ICON_FAVICON);
            $sImageUrlFav = $oTranscoder->getFileUrl($iId);

            // facebook icon
            $oTranscoder = BxDolTranscoderImage::getObjectInstance(BX_DOL_TRANSCODER_OBJ_ICON_FACEBOOK);
            $sImageUrlFcb = $oTranscoder->getFileUrl($iId);

            // apple touch icon
            $oTranscoder = BxDolTranscoderImage::getObjectInstance(BX_DOL_TRANSCODER_OBJ_ICON_APPLE);
            $sImageUrlApl = $oTranscoder->getFileUrl($iId);
        }

        if(empty($sImageUrlFav))
            $sImageUrlFav = $this->_oTemplate->getIconUrl('favicon.png');

        if(empty($sImageUrlFcb))
            $sImageUrlFcb = $this->_oTemplate->getIconUrl('facebook-icon.png');

        if(empty($sImageUrlApl))
            $sImageUrlApl = $this->_oTemplate->getIconUrl('apple-touch-icon.png');

        $sRet = '';
        $sRet .= '<link rel="shortcut icon" type="image/x-icon" href="' . $sImageUrlFav . '" />';
        $sRet .= '<link rel="image_src" sizes="100x100" href="' . $sImageUrlFcb . '" />';
        $sRet .= '<link rel="apple-touch-icon" sizes="152x152" href="' . $sImageUrlApl . '" />';

        return $sRet;
    }

    function getInjectionHead() 
    {
        return $this->getInjection('getInjHead');
    }

    function getInjectionHeader() 
    {
        return $this->getInjection('getInjHeader');
    }

    function getInjectionFooter() 
    {
        return $this->getInjection('getInjFooter');
    }

    public function getPopupAlert()
    {
        return $this->transBox('bx-popup-alert', $this->_oTemplate->parseHtmlByName('popup_trans_alert_cnt.html', array()), true);
    }

    public function getPopupConfirm()
    {
        return $this->transBox('bx-popup-confirm', $this->_oTemplate->parseHtmlByName('popup_trans_confirm_cnt.html', array()), true);
    }

    public function getPopupPrompt()
    {
        $sInputText = 'bx-popup-prompt-value';
        $aInputText = array(
            'type' => 'text',
            'name' => $sInputText,
            'attrs' => array(
                'id' => $sInputText,
            ),
            'value' => '',
            'caption' => ''
        );

        $oForm = new BxTemplFormView(array(), $this->_oTemplate);
        return $this->transBox('bx-popup-prompt', $this->_oTemplate->parseHtmlByName('popup_trans_prompt_cnt.html', array(
            'input' => $oForm->genRow($aInputText)
        )), true);
    }

    /**
     * Output time wrapped in <time> tag in HTML.
     * Then time is autoformatted using JS upon page load, this is aumatically converted to user's timezone and
     * updated in realtime in case of short periods of 'from now' time format.
     *
     * Short version of this function:
     * @see bx_time_js
     *
     * @param $iUnixTimestamp time as unixtimestamp
     * @param $sFormatIdentifier output format identifier
     *     @see BX_FORMAT_DATE
     *     @see BX_FORMAT_TIME
     *     @see BX_FORMAT_DATE_TIME
     * @param $bForceFormat force provided format and don't use "from now" time autoformat.
     */
    function timeForJs ($iUnixTimestamp, $sFormatIdentifier = BX_FORMAT_DATE, $bForceFormat = false)
    {
        $sDateUTC = bx_time_utc ($iUnixTimestamp);
        return $this->timeForJsFullDate ($sDateUTC, $sFormatIdentifier, $bForceFormat);
    }

    /**
     * Same as @see timeForJs but instead of unxtimestamp full date format is used (ex: 2005-08-15T15:52:01) as passec date param
     */ 
    function timeForJsFullDate ($sDateUTC, $sFormatIdentifier = BX_FORMAT_DATE, $bForceFormat = false)
    {
        return '<time datetime="' . $sDateUTC . '" data-bx-format="' . getParam($sFormatIdentifier) . '" data-bx-autoformat="' . ($bForceFormat ? 0 : getParam('sys_format_timeago')) . '">' . $sDateUTC . '</time>';
    }
    
    function statusOnOff ($mixed, $isMsg = false)
    {
        if ((is_bool($mixed) && !$mixed) || (is_string($mixed) && 'fail' == $mixed))
            return '<i class="sys-icon circle col-red2"></i> ' . ($isMsg ? _t('_sys_off') : '');
        elseif (is_string($mixed) && 'warn' == $mixed)
            return '<i class="sys-icon circle col-red3"></i> ' . ($isMsg ? _t('_sys_warn') : '');
        elseif (is_string($mixed) && 'undef' == $mixed)
            return '<i class="sys-icon circle col-gray"></i> ' . ($isMsg ? _t('_undefined') : '');
        else
            return '<i class="sys-icon circle col-green1"></i> ' . ($isMsg ? _t('_sys_on') : '');
    }

    /**
     * Ouputs HTML5 video player.
     * @param $sUrlPoster video poster image
     * @param $sUrlMP4 .mp4 video
     * @param $sUrlWebM .webm video
     * @param $aAttrs custom attributes, defaults are: controls="" preload="none" autobuffer=""
     * @param $sStyles custom styles, defaults are: width:100%; height:auto;
     */
    function videoPlayer ($sUrlPoster, $sUrlMP4, $sUrlWebM = '', $aAttrs = false, $sStyles = 'width:100%; height:auto;')
    {
        $aAttrsDefaults = array(
            'controls' => '',
            'controlsList' => 'nodownload',
            'preload' => 'none',
            'autobuffer' => '', 
        );
        $aAttrs = array_merge($aAttrsDefaults, is_array($aAttrs) ? $aAttrs : array());
        $sAttrs = bx_convert_array2attrs($aAttrs, '', $sStyles);

        return '<video ' . $sAttrs . ' poster="' . $sUrlPoster . '">
                    ' . ($sUrlWebM ? '<source type="video/webm; codecs="vp8, vorbis" src="' . $sUrlWebM . '" />' : '') . '
                    ' . ($sUrlMP4  ? '<source type="video/mp4" src="' . $sUrlMP4 . '" />' : '') . '
                </video>';
    }

    protected function getInjection($sPrefix)
    {
        $sContent = '';

        $aMethods = get_class_methods($this);
        foreach($aMethods as $sMethod)
            if(preg_match("/^(" . $sPrefix . ")[A-Z].+$/", $sMethod))
                $sContent .= $this->$sMethod();

        return $sContent;
    }

    protected function getInjHeadLiveUpdates() 
    {
        return BxDolLiveUpdates::getInstance()->init();
    }
    
    protected function getInjHeaderPushNotifications() 
    {
        $iProfileId = bx_get_logged_profile_id();
        if(empty($iProfileId))
            return '';

        $sAppId = getParam('sys_push_app_id');
        if(empty($sAppId))
            return '';

        $sShortName = getParam('sys_push_short_name');
        $sSafariWebId = getParam('sys_push_safari_id');

        $sSubfolder = '/plugins_public/onesignal/';
        $aUrl = parse_url(BX_DOL_URL_ROOT);
        if(!empty($aUrl['path'])) {
            $sPath = trim($aUrl['path'], '/');
            if(!empty($sPath))
                $sSubfolder = '/' . $sPath . $sSubfolder;
        }

        $this->_oTemplate->addJs(array(
            'https://cdn.onesignal.com/sdks/OneSignalSDK.js',
            'BxDolPush.js',
        ));

        $sJsClass = 'BxDolPush';
        $sJsObject = 'oBxDolPush';

        $sContent = "var " . $sJsObject . " = new " . $sJsClass . "(" . json_encode(array(
            'sObjName' => $sJsObject,
            'sSiteName' => getParam('site_title'),
            'iProfileId' => $iProfileId,
            'sAppId' => $sAppId,
            'sShortName' => $sShortName,
            'sSafariWebId' => $sSafariWebId,
            'sSubfolder' => $sSubfolder,
            'sNotificationUrl' => BX_DOL_URL_ROOT,
            'sTxtNotificationRequest' => bx_js_string(_t('_sys_push_notification_request', getParam('site_title'))),
            'sTxtNotificationRequestYes' => bx_js_string(_t('_sys_push_notification_request_yes')),
            'sTxtNotificationRequestNo' => bx_js_string(_t('_sys_push_notification_request_no')),
        )) . ");";

        return $this->_oTemplate->_wrapInTagJsCode($sContent);
    }

    protected function getInjFooterMenuLoading() 
    {
        return $this->_oTemplate->parsePageByName('menu_loading.html', array());  
    }

    protected function getInjFooterPopupLoading() 
    {
        return $this->transBox('bx-popup-loading', $this->_oTemplate->parsePageByName('popup_loading.html', array()), true);  
    }

    protected function getInjFooterPopupMenus() 
    {
        $sContent = '';

        $oSearch = new BxTemplSearch();
        $oSearch->setLiveSearch(true);
        $sContent .= $this->_oTemplate->parsePageByName('search.html', array(
            'search_form' => $oSearch->getForm(BX_DB_CONTENT_ONLY),
            'results' => $oSearch->getResultsContainer(),
        ));

        $sContent .= $this->_oTemplate->getMenu ('sys_site');
        if(isLogged()) {
            $sContent .= $this->_oTemplate->getMenu ('sys_add_content');
            $sContent .= $this->_oTemplate->getMenu ('sys_account_popup');
        }

        return $sContent;
    }

    protected function getInjFooterPopups() 
    {
        $sContent = '';

        $sContent .= $this->getPopupAlert();
        $sContent .= $this->getPopupConfirm();
        $sContent .= $this->getPopupPrompt();

        return $sContent;
    }

    protected function getInjFooterEmbed() 
    {    
        // Load embed files
        $oEmbed = BxDolEmbed::getObjectInstance(false);
        if ($oEmbed) 
            return $oEmbed->addJsCss();

        return '';
    }
}

/** @} */
