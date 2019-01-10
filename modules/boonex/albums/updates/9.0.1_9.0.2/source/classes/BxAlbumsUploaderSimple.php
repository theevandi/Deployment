<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Albums Albums
 * @ingroup     UnaModules
 *
 * @{
 */

class BxAlbumsUploaderSimple extends BxTemplUploaderSimple
{
    protected $_oModule;

    public function __construct ($aObject, $sStorageObject, $sUniqId, $oTemplate)
    {
        parent::__construct($aObject, $sStorageObject, $sUniqId, $oTemplate);
        $this->_oModule = BxDolModule::getInstance('bx_albums');
    }

    protected function getGhostTemplateVars($aFile, $iProfileId, $iContentId, $oStorage, $oImagesTranscoder)
    {
        $sTitle = $this->_oModule->_oDb->getFileTitle($aFile['id']);
        $a = array(
            'file_title' => $sTitle ? $sTitle : $aFile['file_name']
        );
        $a['file_title_attr'] = bx_html_attribute($a['file_title']);
        return $a;
    }
}

/** @} */
