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

bx_import('BxDolAcl');

/**
 * Entry create/edit pages
 */
class BxBaseModGeneralPageEntry extends BxTemplPage
{
    protected $MODULE;

    protected $_oModule;
    protected $_aContentInfo = false;

    protected $_sCoverClass;

    public function __construct($aObject, $oTemplate = false)
    {
        $this->_oModule = BxDolModule::getInstance($this->MODULE);

        parent::__construct($aObject, $oTemplate ? $oTemplate : $this->_oModule->_oTemplate);

        $this->_sCoverClass = $this->_oModule->getName() . '_cover';
    }

    public function isActive()
    {
        return true;
    }

    public function getCode ()
    {
        // check if content exists
        if (!$this->_aContentInfo || !$this->isActive()) { // if entry is not found - display standard "404 page not found" page
            $this->_oTemplate->displayPageNotFound();
            exit;
        }

        // permissions check 
        $this->_processPermissionsCheck ();

        // count views
        $CNF = &$this->_oModule->_oConfig->CNF;
        if (!empty($CNF['OBJECT_VIEWS'])) {
            BxDolView::getObjectInstance($CNF['OBJECT_VIEWS'], $this->_aContentInfo[$CNF['FIELD_ID']])->doView();
        }

        $oCover = BxDolCover::getInstance($this->_oModule->_oTemplate);
        $oCover->setCoverClass($this->_sCoverClass);

        // set cover image
        $mixedCover = method_exists($this, '_getImageForPageCover') ? $this->_getImageForPageCover() : $this->_getThumbForMetaObject();
        if($mixedCover !== false) {
            $aCover = array(
                'id' => $mixedCover['id']
            );

            if(!empty($mixedCover['transcoder']))
                $aCover['transcoder'] = $mixedCover['transcoder'];
            else if(!empty($CNF['OBJECT_IMAGES_TRANSCODER_COVER'])) 
                $aCover['transcoder'] = $CNF['OBJECT_IMAGES_TRANSCODER_COVER'];
            else if(!empty($mixedCover['object']))
                $aCover['object'] = $mixedCover['object'];

            $oCover->setCoverImageUrl($aCover);
        }

        // add content metatags
        $mixedThumb = $this->_getThumbForMetaObject();
        if (!empty($CNF['OBJECT_METATAGS']) && $mixedThumb) {
            $o = BxDolMetatags::getObjectInstance($CNF['OBJECT_METATAGS']);
            if ($o)
                $o->addPageMetaInfo($this->_aContentInfo[$CNF['FIELD_ID']], $mixedThumb);
        }

        $oMenuSubmenu = BxDolMenu::getObjectInstance('sys_site_submenu');

        // add actions menu to submenu
        if (isset($CNF['OBJECT_MENU_ACTIONS_VIEW_ENTRY']))
            $oMenuSubmenu->setObjectActionsMenu($CNF['OBJECT_MENU_ACTIONS_VIEW_ENTRY']);

        // add social sharing menu to submenu
        $oMenuSubmenu->setServiceSocialSharing(array(
            'module' => $this->MODULE,
            'method' => 'entity_social_sharing',
        ));

        return parent::getCode ();
    }

    protected function _processPermissionsCheck ()
    {
        if (CHECK_ACTION_RESULT_ALLOWED !== ($sMsg = $this->_oModule->checkAllowedView($this->_aContentInfo))) {
            $this->_oTemplate->displayAccessDenied($sMsg);
            exit;
        }
        $this->_oModule->checkAllowedView($this->_aContentInfo, true);
    }

    protected function _getThumbForMetaObject ()
    {
        return $this->_oModule->getEntryImageData($this->_aContentInfo);
    }

    protected function _getPageCacheParams ()
    {
        if (!$this->_aContentInfo)
            return '';
        return $this->_aContentInfo[$this->_oModule->_oConfig->CNF['FIELD_ID']]; // cache is different for every entry
    }
}

/** @} */
