<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaCore UNA Core
 * @{
 */

class BxDolContentInfoCmts extends BxDolContentInfo
{
    protected $_oCmts;

    protected function __construct($sSystem)
    {
        parent::__construct($sSystem);

        $this->_oCmts = BxDolCmts::getObjectInstance($this->_aSystem['alert_unit'], 0, false);
    }

    public function getContentAuthor ($iContentId)
    {
        return $this->_oCmts->serviceGetAuthor($iContentId);
    }

    public function getContentDateAdded ($iContentId)
    {
        return $this->_oCmts->serviceGetDateAdded($iContentId);
    }

    public function getContentDateChanged ($iContentId)
    {
        return $this->_oCmts->serviceGetDateChanged($iContentId);
    }

    public function getContentTitle ($iContentId)
    {
        return $this->_oCmts->serviceGetTitle($iContentId);
    }

    public function getContentThumb ($iContentId)
    {
        return $this->_oCmts->serviceGetThumb($iContentId);
    }

    public function getContentLink ($iContentId)
    {
        return $this->_oCmts->serviceGetLink($iContentId);
    }

    public function getContentText ($iContentId)
    {
        return $this->_oCmts->serviceGetText($iContentId);
    }

    public function getContentInfo ($iContentId, $bSearchableFieldsOnly = true)
    {
        return $this->_oCmts->serviceGetInfo($iContentId);
    }

    public function getContentSearchResultUnit ($iContentId, $sUnitTemplate = '')
    {
        $this->_oCmts->addCssJs();
        return '<ul class="cmts">' . $this->_oCmts->serviceGetSearchResultUnit($iContentId, $sUnitTemplate) . '</ul>';
    }

    public function getAll ($aParams = array())
    {
        return $this->_oCmts->serviceGetAll($aParams);
    }
    
    public function getSearchableFieldsExtended ()
    {
        return $this->_oCmts->serviceGetSearchableFieldsExtended();
    }

    public function getSearchResultExtended ($aParams, $iStart = 0, $iPerPage = 0)
    {
        return $this->_oCmts->serviceGetSearchResultExtended($aParams, $iStart, $iPerPage);
    }
}

/** @} */
