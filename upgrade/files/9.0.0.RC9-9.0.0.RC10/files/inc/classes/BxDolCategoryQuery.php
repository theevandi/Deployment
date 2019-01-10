<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaCore UNA Core
 * @{
 */

/**
 * Database queries for category objects.
 * @see BxDolCategory
 */
class BxDolCategoryQuery extends BxDolDb
{
    protected $_aObject;

    public function __construct($aObject)
    {
        parent::__construct();
        $this->_aObject = $aObject;
    }

    static public function getCategoryObject ($sObject)
    {
        $oDb = BxDolDb::getInstance();
        $sQuery = $oDb->prepare("SELECT * FROM `sys_objects_category` WHERE `object` = ?", $sObject);
        $aObject = $oDb->getRow($sQuery);
        if (!$aObject || !is_array($aObject))
            return false;

        return $aObject;
    }

    static public function getCategoryObjectByFormAndList ($sObjectForm, $sListName)
    {
        $oDb = BxDolDb::getInstance();
        $sQuery = $oDb->prepare("SELECT * FROM `sys_objects_category` WHERE `form_object` = ? AND `list_name` = ?", $sObjectForm, $sListName);
        $aObject = $oDb->getRow($sQuery);
        if (!$aObject || !is_array($aObject))
            return false;

        return $aObject;
    }

    static public function getItemsNumInCategory ($aObject, $sCategoryValue, $bPublicOnly = true)
    {
        $oDb = BxDolDb::getInstance();
        $sWhere = '';
        // TODO: in the future add 'module' field to categories object
        if ($bPublicOnly && ($oModule = BxDolModule::getInstance($aObject['search_object'])) && isset($oModule->_oConfig->CNF['FIELD_ALLOW_VIEW_TO'])) {
            bx_import('BxDolPrivacy');
            $a = isLogged() ? array(BX_DOL_PG_ALL, BX_DOL_PG_MEMBERS) : array(BX_DOL_PG_ALL);
            $sWhere = ' AND `' . $aObject['table'] . '`.`' . $oModule->_oConfig->CNF['FIELD_ALLOW_VIEW_TO'] . '` IN(' . $oDb->implode_escape($a) . ') ';
        }
        return $oDb->getOne("SELECT COUNT(*) FROM `" . $aObject['table'] . "` " . $aObject['join'] . " WHERE `" . $aObject['field'] . "` = :cat " . $aObject['where'] . $sWhere, array('cat' => $sCategoryValue));
    }
}

/** @} */
