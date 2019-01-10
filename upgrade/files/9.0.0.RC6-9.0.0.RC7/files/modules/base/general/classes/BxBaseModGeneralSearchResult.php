<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    BaseGeneral Base classes for modules
 * @ingroup     UnaModules
 *
 * @{
 */

define('BX_SYS_PER_PAGE_BROWSE_SHOWCASE', 32);

class BxBaseModGeneralSearchResult extends BxTemplSearchResult
{
    protected $oModule;
    protected $bShowcaseView = false;
    protected $aUnitViews = array();
    protected $sUnitViewDefault = 'gallery';

    function __construct($sMode = '', $aParams = array())
    {
        parent::__construct();
    }

    function getMain()
    {
        return BxDolModule::getInstance($this->aCurrent['module_name']);
    }

    function getRssUnitLink (&$a)
    {
        $CNF = &$this->oModule->_oConfig->CNF;

        return BX_DOL_URL_ROOT . BxDolPermalinks::getInstance()->permalink('page.php?i=' . $CNF['URI_VIEW_ENTRY'] . '&id=' . $a[$CNF['FIELD_ID']]);
    }

    function getRssPageUrl ()
    {
        if (false === parent::getRssPageUrl())
            return false;

        $oPermalinks = BxDolPermalinks::getInstance();
        return BX_DOL_URL_ROOT . $oPermalinks->permalink($this->aCurrent['rss']['link']);
    }

    function rss ()
    {
        if (!isset($this->aCurrent['rss']))
            return '';

        $this->aCurrent['paginate']['perPage'] = empty($this->oModule->_oConfig->CNF['PARAM_NUM_RSS']) ? 10 : getParam($this->oModule->_oConfig->CNF['PARAM_NUM_RSS']);

        return parent::rss();
    }

    /**
     * Add conditions for private content
     */
    protected function addConditionsForPrivateContent($CNF, $oProfile, $aCustomGroup = array()) 
    {
        if(empty($CNF['OBJECT_PRIVACY_VIEW']))
            return;

        $oPrivacy = BxDolPrivacy::getObjectInstance($CNF['OBJECT_PRIVACY_VIEW']);
        if(!$oPrivacy)
            return;

        if (isset($this->aCurrent['restriction']['context'])) {
            $this->setProcessPrivateContent(true);
            return;
        }
        
        $aCondition = $oPrivacy->getContentPublicAsCondition($oProfile ? $oProfile->id() : 0, $aCustomGroup);
        if(empty($aCondition) || !is_array($aCondition))
            return;

        if(isset($aCondition['restriction']))
            $this->aCurrent['restriction'] = array_merge($this->aCurrent['restriction'], $aCondition['restriction']);
        if(isset($aCondition['join']))
            $this->aCurrent['join'] = array_merge($this->aCurrent['join'], $aCondition['join']);

        $this->setProcessPrivateContent(false);
    }
    
    function showPagination($bAdmin = false, $bChangePage = true, $bPageReload = true)
    {
        if ($this->bShowcaseView){
            return '';
        }
        else{
            $sTmp = parent::showPagination ($bAdmin, $bChangePage, $bPageReload);
            if ($sTmp != '')
                return '<div class="bx-def-margin-top">' . $sTmp . '</div>';
            else
                return '';
        }
    }
    
    protected function getItemPerPageInShowCase ()
    {
        $iPerPageInShowCase = BX_SYS_PER_PAGE_BROWSE_SHOWCASE;
        $CNF = &$this->oModule->_oConfig->CNF;
        if (isset($CNF['PARAM_PER_PAGE_BROWSE_SHOWCASE']))
            $iPerPageInShowCase = getParam($CNF['PARAM_PER_PAGE_BROWSE_SHOWCASE']);
        return $iPerPageInShowCase;
    }
    
    function displayResultBlock ()
    {
		if ($this->bShowcaseView){
            $this->addContainerClass(array('bx-def-margin-sec-lefttopright-neg', 'bx-base-unit-showcase-wrapper'));
			$this->aCurrent['paginate']['perPage'] = $this->getItemPerPageInShowCase();
			$this->oModule->_oTemplate->addCss(array(BX_DIRECTORY_PATH_PLUGINS_PUBLIC . 'flickity/|flickity.css'));
            $this->oModule->_oTemplate->addJs(array('flickity/flickity.pkgd.min.js','modules/base/general/js/|showcase.js'));
		}
		return parent::displayResultBlock ();
    }
}

/** @} */
