<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaCore UNA Core
 * @{
 */

/**
 * @see BxDolObject
 */
class BxDolObjectQuery extends BxDolDb
{
    protected $_oModule;

    protected $_sTable;

    protected $_sTableTrack;
    protected $_sTableTrackFieldAuthor;

    protected $_sTriggerTable;
    protected $_sTriggerFieldId;
    protected $_sTriggerFieldAuthor;
    protected $_sTriggerFieldCount;

    protected $_sMethodGetEntry;

    public function __construct(&$oModule)
    {
        parent::__construct();

        $this->_oModule = $oModule;

        $aSystem = $this->_oModule->getSystemInfo();
        $this->_sTable = isset($aSystem['table_main']) ? $aSystem['table_main'] : '';

        $this->_sTableTrack = isset($aSystem['table_track']) ? $aSystem['table_track'] : '';
        $this->_sTableTrackFieldAuthor = 'author_id';
        
        $this->_sTriggerTable = isset($aSystem['trigger_table']) ? $aSystem['trigger_table'] : '';
        $this->_sTriggerFieldId = isset($aSystem['trigger_field_id']) ? $aSystem['trigger_field_id'] : '';
        $this->_sTriggerFieldAuthor = isset($aSystem['trigger_field_author']) ? $aSystem['trigger_field_author'] : '';
        $this->_sTriggerFieldCount = isset($aSystem['trigger_field_count']) ? $aSystem['trigger_field_count'] : '';
    }

    /**
     * Get SQL parts for main table. 
     */
    public function getSqlParts($sMainTable, $sMainField)
    {
        if(empty($this->_sTable) || empty($sMainTable) || empty($sMainField))
            return array();

        return array (
            'fields' => ", `{$this->_sTable}`.`count` as `count` ",
            'join' => " LEFT JOIN `{$this->_sTable}` ON (`{$this->_sTable}`.`object_id` = `{$sMainTable}`.`{$sMainField}`) ",
        );
    }

    /**
     * Get SQL parts for track table. 
     */
    public function getSqlPartsTrack($sMainTable, $sMainField, $iAuthorId = 0)
    {
        if(empty($this->_sTableTrack) || empty($sMainTable) || empty($sMainField))
            return array();

        return array (
            'fields' => ", `{$this->_sTableTrack}`.`author_id` as `favorer_id` ",
            'where' => $this->prepareAsString(" AND `{$this->_sTableTrack}`.`author_id` = ?", $iAuthorId),
            'join' => " LEFT JOIN `{$this->_sTableTrack}` ON (`{$this->_sTableTrack}`.`object_id` = `{$sMainTable}`.`{$sMainField}`) ",
        );
    }

	public function isPerformed($iObjectId, $iAuthorId)
    {
        $sQuery = $this->prepare("SELECT `object_id` FROM `{$this->_sTableTrack}` WHERE `object_id` = ? AND `author_id` = ? LIMIT 1", $iObjectId, $iAuthorId);
        return (int)$this->getOne($sQuery) != 0;
    }

    public function getPerformedBy($iObjectId)
    {
        $sQuery = $this->prepare("SELECT `author_id` FROM `{$this->_sTableTrack}` WHERE `object_id`=?", $iObjectId);
        return $this->getColumn($sQuery);
    }

    public function deleteObjectEntries($iObjectId)
    {
    	if(!empty($this->_sTable)) {
	        $sQuery = $this->prepare("DELETE FROM `{$this->_sTable}` WHERE `object_id` = ?", $iObjectId);
	        if($this->query($sQuery))
	        	$this->query("OPTIMIZE TABLE `{$this->_sTable}`");
    	}

    	if(!empty($this->_sTableTrack)) {
	        $sQuery = $this->prepare("DELETE FROM `{$this->_sTableTrack}` WHERE `object_id` = ?", $iObjectId);
	        if($this->query($sQuery))
	        	$this->query ("OPTIMIZE TABLE `{$this->_sTableTrack}`");
    	}
    }
    public function deleteAuthorEntries($iAuthorId)
    {
        if(empty($this->_sTableTrack))
            return;

        $bTable = !empty($this->_sTable);
        $bTableTrigger = !empty($this->_sTriggerTable) && !empty($this->_sTriggerFieldCount);

        if($bTable || $bTableTrigger) {
            $aTracks = $this->getAll("SELECT * FROM `{$this->_sTableTrack}` WHERE `{$this->_sTableTrackFieldAuthor}`=:author_id", array('author_id' => $iAuthorId));
            foreach($aTracks as $aTrack) {
                if($bTable)
                    $this->_deleteAuthorEntriesTableMain($aTrack);
    
                /**
                 * Note. It's essential that Trigger Table is updated at the end, 
                 * because it may require updated data from main ($this->_sTable) table. 
                 */
                if($bTableTrigger)
                    $this->_deleteAuthorEntriesTableTrigger($aTrack);
            }
        }

        $sQuery = $this->prepare("DELETE FROM `{$this->_sTableTrack}` WHERE `{$this->_sTableTrackFieldAuthor}`=?", $iAuthorId);
        if($this->query($sQuery))
        	$this->query("OPTIMIZE TABLE `{$this->_sTableTrack}`");
    }

	public function getObjectAuthorId($iId)
    {
        if(empty($this->_sTriggerFieldAuthor))
            return 0;

        $sQuery = $this->prepare("SELECT `{$this->_sTriggerFieldAuthor}` FROM `{$this->_sTriggerTable}` WHERE `{$this->_sTriggerFieldId}` = ? LIMIT 1", $iId);
        return (int)$this->getOne($sQuery);
    }

    public function getObjectCount($iId)
    {
        $sQuery = $this->prepare("SELECT `{$this->_sTriggerFieldCount}` FROM `{$this->_sTriggerTable}` WHERE `{$this->_sTriggerFieldId}` = ? LIMIT 1", $iId);
        return (int)$this->getOne($sQuery);
    }

    public function updateMainTableValue($iObjectId, $iValue)
    {
        $sQuery = $this->prepare("UPDATE `{$this->_sTable}` SET `count` = `count` + ? WHERE `object_id` = ?", (int)$iValue, $iObjectId);
        return (int)$this->query($sQuery) > 0;
    }

    public function updateTriggerTable($iObjectId)
    {
    	if(empty($this->_sMethodGetEntry))
    		return false;

        $aEntry = $this->{$this->_sMethodGetEntry}($iObjectId);
        if(empty($aEntry) || !is_array($aEntry))
            return false;

        return $this->_updateTriggerTable($iObjectId, $aEntry);
    }

    public function updateTriggerTableValue($iObjectId, $iValue)
    {
        $sQuery = $this->prepare("UPDATE `{$this->_sTriggerTable}` SET `{$this->_sTriggerFieldCount}` = `{$this->_sTriggerFieldCount}` + ? WHERE `{$this->_sTriggerFieldId}` = ?", (int)$iValue, $iObjectId);
        return (int)$this->query($sQuery) > 0;
    }

    protected function _updateTriggerTable($iObjectId, $aEntry)
    {
    	$sQuery = $this->prepare("UPDATE `{$this->_sTriggerTable}` SET `{$this->_sTriggerFieldCount}` = ? WHERE `{$this->_sTriggerFieldId}` = ?", $aEntry['count'], $iObjectId);
        return (int)$this->query($sQuery) > 0;
    }

    protected function _deleteAuthorEntriesTableMain($aTrack)
    {
        return $this->updateMainTableValue($aTrack['object_id'], -1);
    }

    protected function _deleteAuthorEntriesTableTrigger($aTrack)
    {
        return $this->updateTriggerTableValue($aTrack['object_id'], -1);
    }
}

/** @} */
