<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    QuoteOfTheDay Quote of the Day
 * @ingroup     UnaModules
 *
 * @{
 */

define('BX_QOD_SOURCE_INTERNAL', 'internal');
define('BX_QOD_SOURCE_RSS', 'rss');
define('BX_QOD_SELECTION_MODE_BY_RANDOM', 'random');
define('BX_QOD_SELECTION_MODE_BY_ORDER', 'order');
define('BX_QOD_LIFETIME_IN_SECONDS', 86400);

class BxQuoteOfDayModule extends BxDolModule
{
    function __construct(&$aModule)
    {
        parent::__construct($aModule);
    }

    /**
     * Service methods
     */
    /**
     * @page service Service Calls
     * @section bx_quoteofday Quote of the Day
     * @subsection bx_quoteofday-other Other
     * @subsubsection bx_quoteofday-get_sources get_sources
     * 
     * @code bx_srv('bx_quoteofday', 'get_sources', [...]); @endcode
     * 
     * Get list of avaliable sources types for studio settings (rss feed or internal list)
     * 
     * @return an array with avaliable sources. 
     * 
     * @see BxQuoteOfDayModule::serviceGetSources
     */
    /** 
     * @ref bx_quoteofday-get_sources "get_sources"
     */
    public function serviceGetSources()
    {
        $aResult = array();
        $aChoices = array(BX_QOD_SOURCE_INTERNAL, BX_QOD_SOURCE_RSS);
        foreach($aChoices as $sChoice)
            $aResult[$sChoice] = _t('_bx_quoteofday_source_' . $sChoice);
             
        return $aResult;
    }
    
    /**
     * @page service Service Calls
     * @section bx_quoteofday Quote of the Day
     * @subsection bx_quoteofday-other Other
     * @subsubsection bx_quoteofday-get_selection_mode selection_mode
     * 
     * @code bx_srv('bx_quoteofday', 'get_selection_mode', [...]); @endcode
     * 
     * Get list of avaliable selection mode(randome mode or ordered mode) for studio settings
     * 
     * @return an array with selection modes.
     * 
     * @see BxQuoteOfDayModule::serviceGetSelectionMode
     */
    /** 
     * @ref bx_quoteofday-get_selection_mode "get_selection_mode"
     */
    public function serviceGetSelectionMode()
    {
        $aResult = array();
        $aChoices = array(BX_QOD_SELECTION_MODE_BY_RANDOM, BX_QOD_SELECTION_MODE_BY_ORDER);
        foreach($aChoices as $sChoice)
            $aResult[$sChoice] = _t('_bx_quoteofday_selection_mode_by_' . $sChoice);
        
        return $aResult;
    }
    
    /**
     * @page service Service Calls
     * @section bx_quoteofday Quote of the Day
     * @subsection bx_quoteofday-other Other
     * @subsubsection bx_quoteofday-get_menu_addon_manage_tools get_menu_addon_manage_tools
     * 
     * @code bx_srv('bx_quoteofday', 'get_menu_addon_manage_tools', [...]); @endcode
     * 
     * Get count of hidden items in internal data for dashbord 
     * 
     * @return digit - count of hidden item .
     * 
     * @see BxQuoteOfDayModule::serviceGetMenuAddonManageTools
     */
    /** 
     * @ref bx_quoteofday-get_menu_addon_manage_tools "get_menu_addon_manage_tools"
     */
    public function serviceGetMenuAddonManageTools()
    {
        return $this->_oDb->getHiddenItemsCount();
    }
    
    /**
     * @page service Service Calls
     * @section bx_quoteofday Quote of the Day
     * @subsection bx_quoteofday-other Other
     * @subsubsection bx_quoteofday-get_quote
     * 
     * @code bx_srv('bx_quoteofday', 'get_quote', [...]); @endcode
     * 
     * Get current Quote of the Day
     * 
     * @return string - current Quote of the Day from system cache .
     * 
     * @see BxQuoteOfDayModule::serviceGetQuote
     */
    /** 
     * @ref bx_quoteofday-get_quote "get_quote"
     */
    public function serviceGetQuote()
    {
        $sTextFromCache = $this->getQuoteFromCache();
        if ($sTextFromCache == null || $sTextFromCache == "")
        {
            $sTextFromCache = $this->defineAndSetQuote();
        }
        
        return  $sTextFromCache;
    }
    
    /**
     * @page service Service Calls
     * @section bx_quoteofday Quote of the Day
     * @subsection bx_quoteofday-other Other
     * @subsubsection bx_quoteofday-set_quotes_manage
     * 
     * @code bx_srv('bx_quoteofday', 'set_quotes_manage', [...]); @endcode
     * 
     * Return Grid from internal quotes management
     * 
     * @return string -  code of grid quotes management.
     * 
     * @see BxQuoteOfDayModule::serviceGetQuotesManage
     */
    /** 
     * @ref bx_quoteofday-set_quotes_manage "set_quotes_manage"
     */
    public function serviceGetQuotesManage()
    {
        $this->_oTemplate->addJs('jquery.form.min.js');
        $oGrid = BxDolGrid::getObjectInstance($this->_oConfig->CNF['OBJECT_GRID']);
        if(!$oGrid)
            return '';
        
        return $oGrid->getCode();
    }
    
    public function defineAndSetQuote()
    {
        $sRssUrl = getParam('bx_quoteofday_rss_url');
        $iRssMaxItems = intval(getParam('bx_quoteofday_rss_max_items'));
        $aSources = explode(',', getParam('bx_quoteofday_source'));
        $sSelectionType = getParam('bx_quoteofday_selection_mode');
        $aData = array();
        
        //##### Get data from internal set #####
        if (in_array(BX_QOD_SOURCE_INTERNAL, $aSources)) {
            $aData = array_merge($aData, $this->getInternalData());
        }
        
        //##### Get data from rss #####
        if (in_array(BX_QOD_SOURCE_RSS, $aSources) && $sRssUrl != "" && $iRssMaxItems > 0)
            $aData = array_merge($aData, $this->getRssData($sRssUrl, $iRssMaxItems));
        
        $sTextToChache = "";
		$iIndex = -1;
		if (count($aData) > 0){
			if ($sSelectionType == BX_QOD_SELECTION_MODE_BY_RANDOM) {
				$iIndex = rand(0, count($aData) - 1);
			}
			else {
				$iDayOfYear = date('z');
				$iIndex = $iDayOfYear % count($aData);
			}
			
			if ($iIndex > -1) {
				$sTextToChache = $aData[$iIndex];
				$this->putQuoteToCache($sTextToChache);
			}
		}
        
        return $sTextToChache;
    }
    
    public function putQuoteToCache($quoteText)
    {
        $oCachObject = $this->_oDb->getDbCacheObject();
        $oCachObject->setData($this->_oDb->genDbCacheKey($this->_oConfig->CNF['CACHEKEY']), $quoteText, BX_QOD_LIFETIME_IN_SECONDS);
    }
    
    public function getQuoteFromCache()
    {
        $oCachObject = $this->_oDb->getDbCacheObject();
        return $oCachObject->getData($this->_oDb->genDbCacheKey($this->_oConfig->CNF['CACHEKEY']), BX_QOD_LIFETIME_IN_SECONDS);
    }
    
    public function removeQuoteFromCache()
    {
        $oCachObject = $this->_oDb->getDbCacheObject();
        $oCachObject->delData($this->_oDb->genDbCacheKey($this->_oConfig->CNF['CACHEKEY']));
    }
    
    private function getInternalData()
    {
        return $this->_oDb->getData();
    }
    
    private function getRssData($sRssUrl, $iRssMaxItems)
    {
        $aTmpRv = array();
        $oXmlParser = BxDolXmlParser::getInstance();
        $sXmlContent = bx_file_get_contents($sRssUrl);
        $aTmp = $oXmlParser->getTags($sXmlContent, 'description');
        if (is_array($aTmp)) {
            $iC = 0;
            foreach ($aTmp as $aValue) {
                if ($iC == $iRssMaxItems) break;
                if (isset($aValue['value']) && $aValue['level'] == 4 && trim(strip_tags($aValue['value'])) != "") {
                    array_push($aTmpRv, $aValue['value']);
                    $iC++;
                }
            }
        }
        
        return $aTmpRv;
    }
}

/** @} */
