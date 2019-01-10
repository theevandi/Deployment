<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaCore UNA Core
 * @{
 */

bx_import('BxDolLanguages');

define('BX_PRELOADER_TYPE_CSS', 'css_system');
define('BX_PRELOADER_TYPE_STUDIO_CSS', 'studio_css_system');
define('BX_PRELOADER_TYPE_JS', 'js_system');
define('BX_PRELOADER_TYPE_STUDIO_JS', 'studio_js_system');
define('BX_PRELOADER_TYPE_JS_OPTION', 'js_option');
define('BX_PRELOADER_TYPE_JS_TRANSLATION', 'js_translation');
define('BX_PRELOADER_TYPE_JS_IMAGE', 'js_image');

class BxDolPreloader extends BxDolFactory implements iBxDolSingleton
{
	protected $_oDb;

	protected $_aEntries;

	protected $_aTypes;
	protected $_aMarkers;

    protected function __construct()
    {
        parent::__construct();

        $this->_oDb = new BxDolPreloaderQuery();

        $this->_aEntries = $this->_oDb->getEntries();

        $this->_aTypes = array(
            BX_PRELOADER_TYPE_CSS => '',
            BX_PRELOADER_TYPE_STUDIO_CSS => '',  
            BX_PRELOADER_TYPE_JS => '', 
            BX_PRELOADER_TYPE_STUDIO_JS => '',
            BX_PRELOADER_TYPE_JS_OPTION => '',
            BX_PRELOADER_TYPE_JS_TRANSLATION => '',
            BX_PRELOADER_TYPE_JS_IMAGE => ''
        );
        foreach($this->_aTypes as $sType => $sValue)
            $this->_aTypes[$sType] = 'add' . bx_gen_method_name($sType);

        $this->_aMarkers = array(
            'dir_plugins_public' => BX_DIRECTORY_PATH_PLUGINS_PUBLIC
        );
    }

    public function __clone()
    {
        if (isset($GLOBALS['bxDolClasses'][get_class($this)]))
            trigger_error('Clone is not allowed for the class: ' . get_class($this), E_USER_ERROR);
    }

	public static function getInstance()
    {
        if(!isset($GLOBALS['bxDolClasses'][__CLASS__]))
        	$GLOBALS['bxDolClasses'][__CLASS__] = new BxDolPreloader();

		return $GLOBALS['bxDolClasses'][__CLASS__];
    }

    public function perform($oTemplateSystem)
    {
    	$bStudio = $oTemplateSystem instanceof BxDolStudioTemplate;
    	$aTypesAvail = array_keys($this->_aTypes);

        foreach($this->_aEntries as $aEntry) {
            $sType = $aEntry['type'];
            if(!in_array($sType, $aTypesAvail))
                continue;

            $sLocKey = '';
            $bLocRemove = false;
            $bLocRemoveJs = false;

            $bModule = $aEntry['module'] != 'system';
            $oModule = $bModule ? BxDolModule::getInstance($aEntry['module']) : null;
            if($bModule && !$oModule)
            	continue;

            $sMethod = $this->_aTypes[($bStudio && $bModule ? 'studio_' : '') . $sType];
            $sContent = bx_replace_markers($aEntry['content'], $this->_aMarkers);

            if($bModule)
            	$oModule->_oTemplate->$sMethod($sContent);
            else
            	$oTemplateSystem->$sMethod($sContent);
        }
    }
}

/** @} */
