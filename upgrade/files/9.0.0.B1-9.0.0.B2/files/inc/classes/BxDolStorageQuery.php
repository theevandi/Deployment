<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    TridentCore Trident Core
 * @{
 */

/**
 * Database queries for File storage class.
 * @see BxDolStorage
 */
class BxDolStorageQuery extends BxDolDb
{
    protected $_aObject;
    protected $_sTableFiles;

    public function __construct($aObject)
    {
        parent::__construct();
        $this->_aObject = $aObject;
        $this->_sTableFiles = '`' . $aObject['table_files'] . '`';
    }

    static public function getStorageObject ($sObject)
    {
        $oDb = BxDolDb::getInstance();
        $sQuery = $oDb->prepare("SELECT * FROM `sys_objects_storage` WHERE `object` = ?", $sObject);
        return $oDb->getRow($sQuery);
    }

    static public function getStorageObjects ()
    {
        $oDb = BxDolDb::getInstance();
        $sQuery = "SELECT * FROM `sys_objects_storage`";
        return $oDb->getAll($sQuery);
    }

    public function getMimeTypeByExt($sExt)
    {
        $sQuery = $this->prepare("SELECT `mime_type` FROM `sys_storage_mime_types` WHERE `ext` = ?", $sExt);
        return $this->getOne($sQuery);
    }

    public function getIconByExt($sExt)
    {
        $sQuery = $this->prepare("SELECT `icon` FROM `sys_storage_mime_types` WHERE `ext` = ?", $sExt);
        return $this->getOne($sQuery);
    }

    public function clearAllMimeTypes()
    {
        return $this->query("TRUNCATE TABLE `sys_storage_mime_types`");
    }

    public function addMimeType($sMimeType, $sExt, $sIcon = '')
    {
        $sQuery = $this->prepare("INSERT IGNORE INTO `sys_storage_mime_types` SET `ext` = ?, `mime_type` = ?, `icon` = ?", $sExt, $sMimeType, $sIcon);
        return $this->query($sQuery);
    }

    public function getStorageObjectQuota()
    {
        return $this->_aObject;
    }

    public function updateStorageObjectQuota($iSize, $iNumber = 1)
    {
        $iTime = time();
        $sQuery = $this->prepare("
            UPDATE `sys_objects_storage`
            SET `current_size` = `current_size` + ?, `current_number` = `current_number` + (?), `ts` = ?
            WHERE `object` = ?",
            $iSize, $iNumber, $iTime, $this->_aObject['object']
        );
        if ($this->query($sQuery)) {
            $this->_aObject = $this->getStorageObject($this->_aObject['object']);
            return true;
        } else {
            return false;
        }
    }

    public function getUserQuota($iProfileId)
    {
        if (!$iProfileId) // for guests and storages without owner don't count per-user quota
            return array('current_size' => 0, 'current_number' => 0, 'quota_size' => 0, 'quota_number' => 0, 'max_file_size' => 0);

        $sQuery = $this->prepare("SELECT `current_size`, `current_number`, 0 as `quota_size`, 0 as `quota_number`, 0 as `max_file_size` FROM `sys_storage_user_quotas` WHERE `profile_id` = ?", $iProfileId);
        $a = $this->getRow($sQuery);
        if (!is_array($a) || !$a)
            $a = array ('current_size' => 0, 'current_number' => 0, 'quota_size' => 0, 'quota_number' => 0, 'max_file_size' => 0);

        // get quota_number and quota_size from user's acl/membership
        $aMembershipInfo = BxDolAcl::getInstance()->getMemberMembershipInfo($iProfileId);
        if ($aMembershipInfo) {
            if (isset($aMembershipInfo['quota_size']))
                $a['quota_size'] = $aMembershipInfo['quota_size'];
            if (isset($aMembershipInfo['quota_number']))
                $a['quota_number'] = $aMembershipInfo['quota_number'];
            if (isset($aMembershipInfo['quota_max_file_size']))
                $a['max_file_size'] = $aMembershipInfo['quota_max_file_size'];
        }

        return $a;
    }

    public function updateUserQuota($iProfileId, $iSize, $iNumber = 1)
    {
        if (!$iProfileId) // for guests and storages without owner don't update per-user quota
            return true;
            
        $iTime = time();
        $sQuery = $this->prepare("
            INSERT INTO `sys_storage_user_quotas`
            SET `profile_id` = ?, `current_size` = `current_size` + ?, `current_number` = `current_number` + ?, `ts` = ?
            ON DUPLICATE KEY UPDATE `current_size` = `current_size` + ?, `current_number` = `current_number` + ?, `ts` = ?",
            $iProfileId, $iSize, $iNumber, $iTime, $iSize, $iNumber, $iTime
        );
        if ($this->query($sQuery))
            return true;
        else
            return false;
    }

    public function addFile($iProfileId, $sLocalId, $sPath, $aFileName, $sMimeType, $sExt, $iSize, $iTime, $isPrivate)
    {
        $sQuery = $this->prepare("INSERT INTO " . $this->_sTableFiles . " SET
            `profile_id` = ?, `remote_id` = ?, `path` = ?, `file_name` = ?, `mime_type` = ?, `ext` = ?, `size` = ?, `added` = ?, `modified` = ?, `private` = ?",
            $iProfileId, $sLocalId, $sPath, $aFileName, $sMimeType, $sExt, $iSize, $iTime, $iTime, $isPrivate ? 1 : 0
        );
        return $this->query($sQuery);
    }

    public function modifyFilePrivate($iFileId, $isPrivate)
    {
        return $this->modifyCustomField($iFileId, 'private', $isPrivate ? 1 : 0);
    }

    public function modifyCustomField($iFileId, $sField, $sValue, $isUpdateModifiedField = true)
    {
    	$aBindings = array(
    		$sField => $sValue,
    		'id' => $iFileId
    	);
    	
        $sAdd = '';
        if ($isUpdateModifiedField) {
        	$aBindings['modified'] = time();

            $sAdd = ", `modified` = :modified";
        }

        $sQuery = "UPDATE " . $this->_sTableFiles . " SET `{$sField}` = :{$sField} {$sAdd} WHERE `id` = :id";
        return $this->query($sQuery, $aBindings);
    }

    public function deleteFile($iFileId)
    {
        // delete queued record for the file
        $sQuery = $this->prepare("DELETE FROM `sys_storage_deletions` WHERE `object` = ? AND `file_id` = ?", $this->_aObject['object'], $iFileId);
        $this->query($sQuery);

        // delete file record
        $sQuery = $this->prepare("DELETE FROM " . $this->_sTableFiles . " WHERE `id` = ?", $iFileId);
        if (!$this->query($sQuery))
            return false;

        // delete any file traces in ghosts table
        $sQuery = $this->prepare("DELETE FROM `sys_storage_ghosts` WHERE `object` = ? AND `id` = ?", $this->_aObject['object'], $iFileId);
        $this->query($sQuery);

        return true;
    }

    public function getFileByFileName($sValue)
    {
        return $this->_getFileBy('`file_name`', $sValue);
    }

    public function getFileById($sValue)
    {
        return $this->_getFileBy('`id`', $sValue);
    }

    public function getFileByRemoteId($sValue)
    {
        return $this->_getFileBy('`remote_id`', $sValue);
    }

    protected function _getFileBy($sField, $sValue)
    {
        $sQuery = $this->prepare("SELECT * FROM " . $this->_sTableFiles . " WHERE " . $sField . " = ?", $sValue);
        return $this->getRow($sQuery);
    }

    public function isTokenValid($iFileId, $sToken)
    {
        $iTime = time();
        $sQuery = $this->prepare("SELECT `created` FROM `sys_storage_tokens` WHERE `id` = ? AND `object` = ? AND `hash` = ? AND `created` > ?", $iFileId, $this->_aObject['object'], $sToken, $iTime - $this->_aObject['token_life']);
        return $this->getOne($sQuery) ? true : false;
    }

    public function genToken($iFileId)
    {
        $iTime = time();
        $sToken = md5($iTime . mt_rand() . BX_DOL_SECRET);
        $sQuery = $this->prepare("INSERT INTO `sys_storage_tokens` SET `id` = ?, `object` = ?, `hash` = ?, `created` = ?", $iFileId, $this->_aObject['object'], $sToken, $iTime);
        if ($this->query($sQuery))
            return $sToken;
        else
            return false;
    }

    public function insertGhosts($mixedFileIds, $iProfileId, $iContentId = 0)
    {
        $iTime = time();
        if (!is_array($mixedFileIds))
            $mixedFileIds = array($mixedFileIds);

        $iCount = 0;
        foreach ($mixedFileIds as $iFileId) {
            $sQuery = $this->prepare("INSERT INTO `sys_storage_ghosts`
                SET `id` = ?, `object` = ?, `profile_id` = ?, `content_id` = ?, `created` = ?
                ON DUPLICATE KEY UPDATE `profile_id` = ?, `content_id` = ?, `created` = ?", $iFileId, $this->_aObject['object'], $iProfileId, $iContentId, $iTime, $iProfileId, $iContentId, $iTime);
            $iCount += $this->query($sQuery);
        }
        return $iCount;
    }

    public function updateGhostsContentId($mixedFileIds, $iProfileId, $iContentId, $aProfiles = array())
    {
        $aBindings = array(
            'content' => $iContentId,
    		'profile' => $iProfileId,
    		'object' => $this->_aObject['object']
        );

        $sSetAddon = '';
        $sWhere = " AND `profile_id` = :profile ";
        if ($aProfiles || isAdmin()) {
            $sSetAddon = ', `profile_id` = :profile ';
            $sWhere = isAdmin() ? "" : " AND `profile_id` IN(" . $this->implode_escape($aProfiles) . ")";
        }
        
        $sQuery = $this->prepare("UPDATE `sys_storage_ghosts` SET `content_id` = :content $sSetAddon WHERE `object` = :object $sWhere AND `id` IN (" . $this->implode_escape($mixedFileIds) . ")");
        return $this->res($sQuery, $aBindings);
    }

    public function deleteGhosts($mixedFileIds, $iProfileId, $iContentId = false)
    {
    	$aBindings = array(
    		'profile_id' => $iProfileId,
    		'object' => $this->_aObject['object']
    	);
        $sQuery = "DELETE FROM `sys_storage_ghosts` WHERE `profile_id` = :profile_id AND `object` = :object AND `id` IN (" . $this->implode_escape($mixedFileIds) . ")";
        if (false !== $iContentId) {
        	$aBindings['content_id'] = $iContentId;

            $sQuery .= " AND `content_id` = :content_id";
        }
        $iCount = $this->query($sQuery, $aBindings);
        if ($iCount)
            $this->query("OPTIMIZE TABLE `sys_storage_ghosts`");
        return $iCount;
    }

    public function getGhosts($mixedProfileId, $iContentId = false)
    {
        return $this->getFiles($mixedProfileId, true, $iContentId);
    }

    public function getGhost($iFileId)
    {
        $sQuery = $this->prepare("SELECT * FROM `sys_storage_ghosts` WHERE `object` = ? AND `id` = ?", $this->_aObject['object'], $iFileId);
        return $this->getRow($sQuery);
    }

    public function getFiles($mixedProfileId, $isGhostsOnly = false, $iContentId = false)
    {
        $aBindings = array();        

        $sWhere = '';
        $sJoin = '';
        if ($isGhostsOnly) {
            $aBindings['object'] = $this->_aObject['object'];

            $sOnProfile = '';
            if (isAdmin() && $iContentId) {
                // don't check profile id for admins, so admin can edit any entry
            }
            elseif (is_array($mixedProfileId) && $mixedProfileId) {
                $sOnProfile = " AND `g`.`profile_id` IN(" . $this->implode_escape($mixedProfileId) . ") ";
            } 
            elseif (!is_array($mixedProfileId) && false !== $mixedProfileId) {
                $aBindings['profile_id'] = $mixedProfileId;
                
                $sOnProfile = " AND `g`.`profile_id` = :profile_id ";                
            }
            
            $sJoin .= " INNER JOIN `sys_storage_ghosts` AS `g` ON (`f`.`id` = `g`.`id` AND `g`.`object` = :object " . $sOnProfile;
            if (false !== $iContentId) {
                $aBindings['content_id'] = $iContentId;

                $sJoin .= " AND `g`.`content_id` = :content_id";
            }

            $sJoin .= ')';
        } 
        else {
            if (is_array($mixedProfileId) && $mixedProfileId) {
                $sWhere = " WHERE `f`.`profile_id` IN(" . $this->implode_escape($mixedProfileId) . ") ";
            }
            elseif (!is_array($mixedProfileId) && false !== $mixedProfileId) {
                $aBindings['profile_id'] = $mixedProfileId;

                $sWhere = " WHERE `f`.`profile_id` = :profile_id ";
            }
        }

        $sQuery = "SELECT `f`.* FROM " . $this->_sTableFiles . " AS `f` " . $sJoin . $sWhere;
        return $this->getAll($sQuery, $aBindings);
    }

    public function getFilesAll($iStart, $iPerPage)
    {
        $sQuery = $this->prepare("SELECT * FROM " . $this->_sTableFiles . " LIMIT ?, ?", (int)$iStart, (int)$iPerPage);
        return $this->getAll($sQuery);
    }

    public function prune()
    {
        $iTime = time();
        $sQuery = $this->prepare("DELETE FROM `sys_storage_tokens` WHERE `object` = ? AND `created` < ?", $this->_aObject['object'], $iTime - $this->_aObject['token_life']);
        $iCount = $this->query($sQuery);
        if ($iCount)
            $this->query("OPTIMIZE TABLE `sys_storage_tokens`");
        return $iCount;
    }

    public function queueFilesForDeletion ($a)
    {
        $iTime = time();
        $iAdded = 0;
        foreach ($a as $iFileId) {
            $sQuery = $this->prepare("INSERT IGNORE INTO `sys_storage_deletions` SET `object` = ?, `file_id` = ?, `requested` = ?", $this->_aObject['object'], (int)$iFileId, $iTime);
            $iAdded += ($this->query($sQuery) ? 1 : 0);
        }
        return $iAdded;
    }

    public static function getQueuedFilesForDeletion ($iLimit = 1000)
    {
        $oDb = BxDolDb::getInstance();
        $sQuery = $oDb->prepare("SELECT `object`, `file_id` FROM `sys_storage_deletions` ORDER BY `requested` ASC LIMIT ?", $iLimit);
        return $oDb->getAll($sQuery);
    }

    public static function isQueuedFilesForDeletion ($sPrefix)
    {
        $oDb = BxDolDb::getInstance();
        $sQuery = $oDb->prepare("SELECT COUNT(*) FROM `sys_storage_deletions` WHERE `object` LIKE ?", $sPrefix . '%');
        return $oDb->getOne($sQuery);
    }
}

/** @} */
