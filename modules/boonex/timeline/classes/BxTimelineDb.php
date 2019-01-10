<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Timeline Timeline
 * @ingroup     UnaModules
 *
 * @{
 */

class BxTimelineDb extends BxBaseModNotificationsDb
{
    protected $_sTableEvent2User;
    protected $_sTablesRepostTrack;
    protected $_sTableHotTrack;

    protected $_aTablesMedia;
    protected $_aTablesMedia2Events;

    /*
     * Constructor.
     */
    function __construct(&$oConfig)
    {
        parent::__construct($oConfig);
        $this->_sTableEvent2User = $this->_sPrefix . 'events2users';
        $this->_sTableRepostsTrack = $this->_sPrefix . 'reposts_track';
        $this->_sTableHotTrack = $this->_sPrefix . 'hot_track';

        $this->_aTablesMedia = array(
        	BX_TIMELINE_MEDIA_PHOTO => $this->_sPrefix . 'photos',
        	BX_TIMELINE_MEDIA_VIDEO => $this->_sPrefix . 'videos' 
        );
        $this->_aTablesMedia2Events = array(
        	BX_TIMELINE_MEDIA_PHOTO => $this->_sPrefix . 'photos2events',
        	BX_TIMELINE_MEDIA_VIDEO => $this->_sPrefix . 'videos2events'
        );
    }

    public function deleteModuleEvents($aData)
    {
    	foreach($aData['handlers'] as $aHandler) {
    		//Delete system events.
            $this->deleteEvent(array('type' => $aHandler['alert_unit'], 'action' => $aHandler['alert_action']));

            //Delete reposted events.
    		$aEvents = $this->getEvents(array('browse' => 'reposted_by_descriptor', 'type' => $aHandler['alert_unit'], 'action' => $aHandler['alert_action']));
			foreach($aEvents as $aEvent) {
				$aContent = unserialize($aEvent['content']);
				if(isset($aContent['type']) && $aContent['type'] == $aHandler['alert_unit'] && isset($aContent['action']) && $aContent['action'] == $aHandler['alert_action'])
					$this->deleteEvent(array('id' => (int)$aEvent['id']));
			}
    	}
    }

	public function activateModuleEvents($aData, $bActivate = true)
    {
    	$iActivate = $bActivate ? 1 : 0;

    	foreach($aData['handlers'] as $aHandler) {
    		//Activate (deactivate) system events.
            $this->updateEvent(array('active' => $iActivate), array('type' => $aHandler['alert_unit'], 'action' => $aHandler['alert_action']));

			//Activate (deactivate) reposted events.
			$aEvents = $this->getEvents(array('browse' => 'reposted_by_descriptor', 'type' => $aHandler['alert_unit'], 'action' => $aHandler['alert_action']));
			foreach($aEvents as $aEvent) {
				$aContent = unserialize($aEvent['content']);
				if(isset($aContent['type']) && $aContent['type'] == $aHandler['alert_unit'] && isset($aContent['action']) && $aContent['action'] == $aHandler['alert_action'])
					$this->updateEvent(array('active' => $iActivate), array('id' => (int)$aEvent['id']));
			}
    	}
    }

    public function getMaxDuration($aParams)
    {
        $aParams['browse'] = 'last';
        if(isset($aParams['timeline']))
            unset($aParams['timeline']);

        $aEvent = $this->getEvents($aParams);
        if(empty($aEvent) || !is_array($aEvent))
            return 0;

        $iNowYear = date('Y', time());
        return (int)$aEvent['year'] < $iNowYear ? (int)$aEvent['year'] : 0;
    }

    //--- Repost related methods ---//
    public function insertRepostTrack($iEventId, $iAuthorId, $sAuthorIp, $iRepostedId)
    {
        $iNow = time();
        $iAuthorNip = ip2long($sAuthorIp);
        $sQuery = $this->prepare("INSERT INTO `{$this->_sTableRepostsTrack}` SET `event_id` = ?, `author_id` = ?, `author_nip` = ?, `reposted_id` = ?, `date` = ?", $iEventId, $iAuthorId, $iAuthorNip, $iRepostedId, $iNow);
        return (int)$this->query($sQuery) > 0;
    }

    public function deleteRepostTrack($iEventId)
    {
        $sQuery = $this->prepare("DELETE FROM `{$this->_sTableRepostsTrack}` WHERE `event_id` = ?", $iEventId);
        return (int)$this->query($sQuery) > 0;
    }

    public function updateRepostCounter($iId, $iCounter, $iIncrement = 1)
    {
        return (int)$this->updateEvent(array('reposts' => (int)$iCounter + $iIncrement), array('id' => $iId)) > 0;
    }

    public function getReposted($sType, $sAction, $iObjectId)
    {
    	$bSystem = $this->_oConfig->isSystem($sType, $sAction);

        if($bSystem)
            $aParams = array('browse' => 'descriptor', 'type' => $sType, 'action' => $sAction, 'object_id' => $iObjectId);
        else
            $aParams = array('browse' => 'id', 'value' => $iObjectId);

		$aReposted = $this->getEvents($aParams);
		if($bSystem && (empty($aReposted) || !is_array($aReposted))) {
			$iOwnerId = 0;
			$iDate = 0;
			$iHidden = 1;

			$mixedResult = $this->_oConfig->getSystemDataByDescriptor($sType, $sAction, $iObjectId);
			if(is_array($mixedResult)) {
                            $iOwnerId = !empty($mixedResult['owner_id']) ? (int)$mixedResult['owner_id'] : 0;
                            $iDate = !empty($mixedResult['date']) ? (int)$mixedResult['date'] : 0;
                            if($this->_oConfig->isUnhideRestored() && !empty($iOwnerId) && !empty($iDate))
                                $iHidden = 0;
			}

			$iId = $this->insertEvent(array(
                            'owner_id' => $iOwnerId,
                            'type' => $sType,
                            'action' => $sAction,
                            'object_id' => $iObjectId,
                            'object_privacy_view' => $this->_oConfig->getPrivacyViewDefault('object'),
                            'content' => '',
                            'title' => '',
                            'description' => '',
                            'date' => $iDate,
                            'hidden' => $iHidden
			));

			$aReposted = $this->getEvents(array('browse' => 'id', 'value' => $iId));
		}

        return $aReposted;
    }

    function getRepostedBy($iRepostedId)
    {
        $sQuery = $this->prepare("SELECT `author_id` FROM `{$this->_sTableRepostsTrack}` WHERE `reposted_id`=?", $iRepostedId);
        return $this->getColumn($sQuery);
    }

    function isReposted($iRepostedId, $iOwnerId, $iAuthorId)
    {
    	$sQuery = $this->prepare("SELECT 
    			`te`.`id`
    		FROM `{$this->_sTableRepostsTrack}` AS `tst` 
    		LEFT JOIN `{$this->_sTable}` AS `te` ON `tst`.`event_id`=`te`.`id` 
    		WHERE `tst`.`author_id`=? AND `tst`.`reposted_id`=? AND `te`.`owner_id`=?", $iAuthorId, $iRepostedId, $iOwnerId);

    	return (int)$this->getOne($sQuery) > 0;
    }

    //--- Photo uploader related methods ---//
    public function saveMedia($sType, $iEventId, $iItemId)
    {
    	$sTable = $this->_aTablesMedia2Events[$sType];

        $sQuery = $this->prepare("INSERT INTO `" . $sTable . "` SET `event_id`=?, `media_id`=?", $iEventId, $iItemId);
        return (int)$this->query($sQuery) > 0;
    }

    public function deleteMedia($sType, $iEventId)
    {
    	$sTable = $this->_aTablesMedia2Events[$sType];

        $sQuery = $this->prepare("DELETE FROM `" . $sTable . "` WHERE `event_id` = ?", $iEventId);
        return (int)$this->query($sQuery) > 0;
    }

    public function getMedia($sType, $iEventId, $iOffset = 0)
    {
    	$sTableMedia = $this->_aTablesMedia[$sType];
    	$sTableMedia2Events = $this->_aTablesMedia2Events[$sType];

        $sLimitAddon = '';
        if($iOffset != 0)
            $sLimitAddon = $this->prepareAsString(" OFFSET ?", $iOffset);

        $sQuery = $this->prepare("SELECT
                 `tme`.`media_id` AS `id`
            FROM `" . $sTableMedia2Events . "` AS `tme`
            LEFT JOIN `" . $sTableMedia . "` AS `tm` ON `tme`.`media_id`=`tm`.`id`
            WHERE `tme`.`event_id`=?" . $sLimitAddon, $iEventId);

        return $this->getColumn($sQuery);
    }

    //--- Link attach related methods ---//
    public function getUnusedLinks($iUserId, $iLinkId = 0)
    {
        $aMethod = array('name' => 'getAll', 'params' => array(0 => 'query'));
        $aMethod['params'][1] = array(
			'profile_id' => $iUserId
		);

        $sWhereAddon = '';
        if(!empty($iLinkId)) {
            $aMethod['name'] = 'getRow';
            $aMethod['params'][1]['id'] = $iLinkId;

            $sWhereAddon = " AND `tl`.`id`=:id";
        }

        $aMethod['params'][0] = "SELECT
                `tl`.`id` AS `id`,
                `tl`.`profile_id` AS `profile_id`,
                `tl`.`media_id` AS `media_id`,
                `tl`.`url` AS `url`,
                `tl`.`title` AS `title`,
                `tl`.`text` AS `text`,
                `tl`.`added` AS `added`
            FROM `" . $this->_sPrefix . "links` AS `tl`
            LEFT JOIN `" . $this->_sPrefix . "links2events` AS `tle` ON `tl`.`id`=`tle`.`link_id`
            WHERE `tl`.`profile_id`=:profile_id AND ISNULL(`tle`.`event_id`)" . $sWhereAddon . "
            ORDER BY `tl`.`added` DESC";

        return call_user_func_array(array($this, $aMethod['name']), $aMethod['params']);;
    }

    public function deleteUnusedLinks($iUserId, $iLinkId = 0)
    {
    	$aBindings = array(
    		'profile_id' => $iUserId
    	);

        $sWhereAddon = '';
        if(!empty($iLinkId)) {
        	$aBindings['id'] = $iLinkId;

            $sWhereAddon = " AND `id`=:id";
        }

        return $this->query("DELETE FROM `" . $this->_sPrefix . "links` WHERE `profile_id`=:profile_id" . $sWhereAddon, $aBindings);
    }

    public function saveLink($iEventId, $iLinkId)
    {
        $sQuery = $this->prepare("INSERT INTO `" . $this->_sPrefix . "links2events` SET `event_id`=?, `link_id`=?", $iEventId, $iLinkId);
        return (int)$this->query($sQuery) > 0;
    }

    public function deleteLinks($iEventId)
    {
        $sQuery = $this->prepare("DELETE FROM `tl`, `tle` USING `" . $this->_sPrefix . "links` AS `tl` LEFT JOIN `" . $this->_sPrefix . "links2events` AS `tle` ON `tl`.`id`=`tle`.`link_id` WHERE `tle`.`event_id` = ?", $iEventId);
        return (int)$this->query($sQuery) > 0;
    }

    public function getLinks($iEventId)
    {
        $sQuery = $this->prepare("SELECT
                `tl`.`id` AS `id`,
                `tl`.`profile_id` AS `profile_id`,
                `tl`.`media_id` AS `media_id`,
                `tl`.`url` AS `url`,
                `tl`.`title` AS `title`,
                `tl`.`text` AS `text`,
                `tl`.`added` AS `added`
            FROM `" . $this->_sPrefix . "links` AS `tl`
            LEFT JOIN `" . $this->_sPrefix . "links2events` AS `tle` ON `tl`.`id`=`tle`.`link_id`
            WHERE `tle`.`event_id`=?", $iEventId);

        return $this->getAll($sQuery);
    }

    public function getHot()
    {
        return $this->getColumn("SELECT `event_id` FROM `" . $this->_sTableHotTrack . "`");
    }

    public function clearHot()
    {
        return $this->query("TRUNCATE TABLE `" . $this->_sTableHotTrack . "`");
    }

    public function getHotTrackByDate($iInterval = 24)
    {
        $sQuery = "SELECT 
                `te`.`id` AS `event_id`,
    			`te`.`date` AS `value`
    		FROM `" . $this->_sTable . "` AS `te`
    		WHERE `te`.`date` > (UNIX_TIMESTAMP() - 3600 * :interval)";

        return $this->getPairs($sQuery, 'event_id', 'value', array('interval' => $iInterval));
    }

    public function getHotTrackByCommentsDate($sModule, $sTableTrack, $iInterval = 24)
    {
        $sQuery = "SELECT 
    			`te`.`id` as `event_id`,
    			MAX(`tt`.`cmt_time`) AS `value`
    		FROM `" . $this->_sTable . "` AS `te`
    		INNER JOIN `" . $sTableTrack . "` AS `tt` ON `te`.`id`=`tt`.`cmt_object_id` AND `te`.`type`=:module 
    		WHERE `tt`.`cmt_time` > (UNIX_TIMESTAMP() - 3600 * :interval) 
    		GROUP BY `te`.`id`";

        return $this->getPairs($sQuery, 'event_id', 'value', array('module' => $sModule, 'interval' => $iInterval));
    }

    public function getHotTrackByCommentsDateModule($sModule, $sTableTrack, $iInterval = 24)
    {
        $sQuery = "SELECT 
    			`te`.`id` as `event_id`,
    			MAX(`tt`.`cmt_time`) AS `value`
    		FROM `" . $this->_sTable . "` AS `te`
    		INNER JOIN `" . $sTableTrack . "` AS `tt` ON `te`.`object_id`=`tt`.`cmt_object_id` AND `te`.`type`=:module 
    		WHERE `tt`.`cmt_time` > (UNIX_TIMESTAMP() - 3600 * :interval) 
    		GROUP BY `te`.`object_id`";

        return $this->getPairs($sQuery, 'event_id', 'value', array('module' => $sModule, 'interval' => $iInterval));
    }

    public function getHotTrackByVotesDate($sModule, $sTableTrack, $iInterval = 24)
    {
        $sQuery = "SELECT 
    			`te`.`id` as `event_id`,
    			MAX(`tt`.`date`) AS `value`
    		FROM `" . $this->_sTable . "` AS `te`
    		INNER JOIN `" . $sTableTrack . "` AS `tt` ON `te`.`id`=`tt`.`object_id` AND `te`.`type`=:module 
    		WHERE `tt`.`date` > (UNIX_TIMESTAMP() - 3600 * :interval) 
    		GROUP BY `te`.`id`";

        return $this->getPairs($sQuery, 'event_id', 'value', array('module' => $sModule, 'interval' => $iInterval));
    }

    public function getHotTrackByVotesDateModule($sModule, $sTableTrack, $iInterval = 24)
    {
        $sQuery = "SELECT 
    			`te`.`id` as `event_id`,
    			MAX(`tt`.`date`) AS `value`
    		FROM `" . $this->_sTable . "` AS `te`
    		INNER JOIN `" . $sTableTrack . "` AS `tt` ON `te`.`object_id`=`tt`.`object_id` AND `te`.`type`=:module 
    		WHERE `tt`.`date` > (UNIX_TIMESTAMP() - 3600 * :interval) 
    		GROUP BY `te`.`object_id`";

        return $this->getPairs($sQuery, 'event_id', 'value', array('module' => $sModule, 'interval' => $iInterval));
    }

    /**
     * Hot Track by Sum of Votes during specified Period is currently disabled.
     */
    public function getHotTrackByVotesSum($sModule, $sTableTrack, $iInterval = 24)
    {
        $sQuery = "SELECT 
    			`te`.`id` as `event_id`,
    			SUM(`tt`.`value`) AS `value`
    		FROM `" . $this->_sTable . "` AS `te`
    		INNER JOIN `" . $sTableTrack . "` AS `tt` ON `te`.`id`=`tt`.`object_id` AND `te`.`type`=:module 
    		WHERE `tt`.`date` > (UNIX_TIMESTAMP() - 3600 * :interval) 
    		GROUP BY `te`.`id`";

        return $this->getAll($sQuery, array('module' => $sModule, 'interval' => $iInterval));
    }

    /**
     * Hot Track by Sum of Votes during specified Period is currently disabled.
     */
    public function getHotTrackByVotesSumModule($sModule, $sTableTrack, $iInterval = 24)
    {
        $sQuery = "SELECT 
    			`te`.`id` as `event_id`,
    			SUM(`tt`.`value`) AS `value`
    		FROM `" . $this->_sTable . "` AS `te`
    		INNER JOIN `" . $sTableTrack . "` AS `tt` ON `te`.`object_id`=`tt`.`object_id` AND `te`.`type`=:module 
    		WHERE `tt`.`date` > (UNIX_TIMESTAMP() - 3600 * :interval) 
    		GROUP BY `te`.`object_id`";

        return $this->getAll($sQuery, array('module' => $sModule, 'interval' => $iInterval));
    }

    public function updateHotTrack($aTrack)
    {
        return (int)$this->query("REPLACE INTO `" . $this->_sTableHotTrack . "` SET " . $this->arrayToSQL($aTrack)) > 0;
    }

    protected function _getFilterAddon($iOwnerId, $sFilter)
    {
        switch($sFilter) {
            /**
             * Direct posts in Timeline made by a timeline owner ($iOwnerId)
             */
            case BX_TIMELINE_FILTER_OWNER:
                $sFilterAddon = $this->prepareAsString(" AND `{$this->_sTable}`.`action`='' AND `{$this->_sTable}`.`object_id`=? ", $iOwnerId);
                break;

            /**
             * Direct posts in Timeline made by users except a timeline owner ($iOwnerId)
             */
            case BX_TIMELINE_FILTER_OTHER:
                $sFilterAddon = $this->prepareAsString(" AND `{$this->_sTable}`.`action`='' AND `{$this->_sTable}`.`object_id`<>? ", $iOwnerId);
                break;

			/**
             * All (Direct and System) posts in Timeline (owned by $iOwnerId) made by users except the viewer
             */
            case BX_TIMELINE_FILTER_OTHER_VIEWER:
                $sFilterAddon = $this->prepareAsString(" AND (`{$this->_sTable}`.`action`<>'' OR (`{$this->_sTable}`.`action`='' AND `{$this->_sTable}`.`object_id`<>?)) ", bx_get_logged_profile_id());
                break;
                

            case BX_TIMELINE_FILTER_ALL:
            default:
                $sFilterAddon = "";
        }
        return $sFilterAddon;
    }

    protected function _getSqlPartsEvents($aParams)
    {
    	$sMethod = 'getAll';
    	$sSelectClause = "`{$this->_sTable}`.*";
        $sJoinClause = $sWhereClause = $sOrderClause = $sLimitClause = "";

        switch($aParams['browse']) {
        	case 'owner_id':
        		$sWhereClause = $this->prepareAsString("AND `{$this->_sTable}`.`owner_id`=? ", $aParams['value']);
        		break;

        	case 'common_by_object':
        		$sCommonPostPrefix = $this->_oConfig->getPrefix('common_post');
        		$sWhereClause = $this->prepareAsString("AND SUBSTRING(`{$this->_sTable}`.`type`, 1, " . strlen($sCommonPostPrefix) . ")='" . $sCommonPostPrefix . "' AND `{$this->_sTable}`.`object_id`=? ", $aParams['value']);
        		break;

            case 'descriptor':
                $sMethod = 'getRow';
                $sWhereClause = "";

                if(isset($aParams['type']))
                    $sWhereClause .= $this->prepareAsString("AND `{$this->_sTable}`.`type`=? ", $aParams['type']);
                if(isset($aParams['action']))
                    $sWhereClause .= $this->prepareAsString("AND `{$this->_sTable}`.`action`=? ", $aParams['action']);
                if(isset($aParams['object_id']))
                    $sWhereClause .= $this->prepareAsString("AND `{$this->_sTable}`.`object_id`=? ", $aParams['object_id']);

                $sLimitClause = "LIMIT 1";
                break;

            case 'reposted_by_descriptor':
            	$sWhereClause = "";

            	if(isset($aParams['type']))
                    $sWhereClause .= "AND `{$this->_sTable}`.`content` LIKE " . $this->escape('%' . $aParams['type'] . '%');

                if(isset($aParams['action']))
                    $sWhereClause .= "AND `{$this->_sTable}`.`content` LIKE " . $this->escape('%' . $aParams['action'] . '%');
                break;

            case 'list':
                list($sMethod, $sSelectClause, $sJoinClause, $sWhereClause, $sOrderClause, $sLimitClause) = parent::_getSqlPartsEvents($aParams);
                if(in_array($aParams['type'], array(BX_BASE_MOD_NTFS_TYPE_CONNECTIONS, BX_TIMELINE_TYPE_OWNER_AND_CONNECTIONS)))
                    $sSelectClause  = "DISTINCT " . $sSelectClause;
                break;

            default:
            	list($sMethod, $sSelectClause, $sJoinClause, $sWhereClause, $sOrderClause, $sLimitClause) = parent::_getSqlPartsEvents($aParams);
        }

		$sSelectClause .= ", DAYOFYEAR(FROM_UNIXTIME(`{$this->_sTable}`.`date`)) AS `days`, DAYOFYEAR(NOW()) AS `today`, ROUND((UNIX_TIMESTAMP() - `{$this->_sTable}`.`date`)/86400) AS `ago_days`, YEAR(FROM_UNIXTIME(`{$this->_sTable}`.`date`)) AS `year`";
		if($aParams['browse'] == 'list') {
			$sOrderClause = "";

			switch($aParams['type']) {
				case BX_TIMELINE_TYPE_HOT:
                    $sOrderClause = "`{$this->_sTableHotTrack}`.`value` DESC, ";
                    break;

				case BX_BASE_MOD_NTFS_TYPE_PUBLIC:
				case BX_BASE_MOD_NTFS_TYPE_CONNECTIONS:
				case BX_TIMELINE_TYPE_OWNER_AND_CONNECTIONS:
					$sOrderClause = "`{$this->_sTable}`.`sticked` DESC, ";
					break;

				case BX_BASE_MOD_NTFS_TYPE_OWNER:
					$sOrderClause = "`{$this->_sTable}`.`pinned` DESC, ";
					break;
			}

            $sOrderClause = "ORDER BY " . $sOrderClause . "`{$this->_sTable}`.`date` DESC";
		}

        if(isset($aParams['count']) && $aParams['count'] === true) {
            $sMethod = 'getOne';
            $sSelectClause = "COUNT(`{$this->_sTable}`.`id`)";
        }

        return array($sMethod, $sSelectClause, $sJoinClause, $sWhereClause, $sOrderClause, $sLimitClause);
    }

    protected function _getSqlPartsEventsList($aParams)
    {
        $sCommonPostPrefix = $this->_oConfig->getPrefix('common_post');

    	$sJoinClause = "";
    	$sWhereClause = "AND `{$this->_sTable}`.`hidden`='0' ";

		if(isset($aParams['active']))
        	$sWhereClause .= $this->prepareAsString("AND `{$this->_sTable}`.`active`=? ", (int)$aParams['active']);

		//--- Apply filter
        if(isset($aParams['filter']))
        	$sWhereClause .= $this->_getFilterAddon($aParams['owner_id'], $aParams['filter']);

		//--- Apply timeline
        if(isset($aParams['timeline']) && !empty($aParams['timeline'])) {
        	$iYear = (int)$aParams['timeline'];
            $sWhereClause .= $this->prepareAsString("AND `date`<=? ", mktime(23, 59, 59, 12, 31, $iYear));
		}

		//--- Apply modules or handlers filter
        $sWhereModuleFilter = '';
        if(!empty($aParams['modules']) && is_array($aParams['modules']))
        	$sWhereModuleFilter = "AND `" . $this->_sTable . "`.`type` IN (" . $this->implode_escape($aParams['modules']) . ") ";

		if($sWhereModuleFilter == '') {
        	$aHidden = $this->_oConfig->getHandlersHidden();
			$sWhereModuleFilter = !empty($aHidden) && is_array($aHidden) ? "AND `" . $this->_sTableHandlers . "`.`id` NOT IN (" . $this->implode_escape($aHidden) . ") " : "";
		}

		if($sWhereModuleFilter != '')
			$sWhereClause .= $sWhereModuleFilter;

        //--- Apply unpublished (date in future) filter
        $sWhereClause .= $this->prepareAsString("AND IF(SUBSTRING(`{$this->_sTable}`.`type`, 1, " . strlen($sCommonPostPrefix) . ") = '" . $sCommonPostPrefix . "' AND `{$this->_sTable}`.`object_id` = ?, 1, `{$this->_sTable}`.`date` <= UNIX_TIMESTAMP()) ", bx_get_logged_profile_id());

		//--- Check type
		$sWhereSubclause = "";
		switch($aParams['type']) {
		    case BX_TIMELINE_TYPE_HOT: //--- Hot (Public) Feed.
		        $sJoinClause .= " INNER JOIN `{$this->_sTableHotTrack}` ON `{$this->_sTable}`.`id`=`{$this->_sTableHotTrack}`.`event_id`";
		        
		    case BX_BASE_MOD_NTFS_TYPE_PUBLIC: //--- Site (Public) Feed.
		        //--- Apply privacy filter
		        $aPrivacyGroups = array(BX_DOL_PG_ALL);
		        if(isLogged())
		            $aPrivacyGroups[] = BX_DOL_PG_MEMBERS;

        		$aQueryParts = BxDolPrivacy::getObjectInstance($this->_oConfig->getObject('privacy_view'))->getContentByGroupAsSQLPart($aPrivacyGroups);
        		$sWhereClause .= $aQueryParts['where'] . " ";

        		if($this->_oConfig->isShowAll())
        		    break;

        		//--- Select All System posts
        		$sWhereSubclause = "SUBSTRING(`{$this->_sTable}`.`type`, 1, " . strlen($sCommonPostPrefix) . ") <> '" . $sCommonPostPrefix . "'";

        		//--- Select Public (Direct) posts created on Home Page Timeline (Public Feed) 
        		$sWhereSubclause .= $this->prepareAsString(" OR `{$this->_sTable}`.`owner_id`=?", 0);

        		//--- Select Promoted posts.
		        $sWhereSubclause .= " OR `{$this->_sTable}`.`promoted` <> '0'";
		        break;

                    //--- Profile Feed
                    case BX_BASE_MOD_NTFS_TYPE_OWNER:
                        if(empty($aParams['owner_id']))
                            break;

                        //--- Select Own (System and Direct) posts from Profile's Timeline.
                        $sWhereSubclause = $this->prepareAsString("(`{$this->_sTable}`.`owner_id` = ?)", $aParams['owner_id']);

                        //--- Select Own Public (Direct) posts from Home Page Timeline (Public Feed).
                        $sWhereSubclause .= $this->prepareAsString(" OR (`{$this->_sTable}`.`owner_id` = '0' AND IF(SUBSTRING(`{$this->_sTable}`.`type`, 1, " . strlen($sCommonPostPrefix) . ") = '" . $sCommonPostPrefix . "', `{$this->_sTable}`.`object_id` = ?, 1))", $aParams['owner_id']);

                        bx_alert($this->_oConfig->getName(), 'get_list_by_type', 0, 0, array(
                            'type' => BX_BASE_MOD_NTFS_TYPE_OWNER,
                            'owner_id' => $aParams['owner_id'],
                            'table' => $this->_sTable,
                            'where_clause' => &$sWhereClause,
                            'where_subclause' => &$sWhereSubclause
                        ));
                        break;

                    //--- Profile Connections Feed
                    case BX_BASE_MOD_NTFS_TYPE_CONNECTIONS:
                        if(empty($aParams['owner_id']))
                            break;

                        $oConnection = BxDolConnection::getObjectInstance($this->_oConfig->getObject('conn_subscriptions'));

                        $aQueryParts = $oConnection->getConnectedContentAsSQLPartsExt($this->_sPrefix . 'events', 'owner_id', $aParams['owner_id']);
                        $aJoin1 = $aQueryParts['join'];

                        $aQueryParts = $oConnection->getConnectedContentAsSQLPartsExt($this->_sPrefix . 'events', 'object_id', $aParams['owner_id']);
                        $aJoin2 = $aQueryParts['join'];

                        //--- Join System and Direct posts made by following members.  
                        $sJoinClause .= " " . $aJoin1['type'] . " JOIN `" . $aJoin1['table'] . "` AS `" . $aJoin1['table_alias'] . "` ON ((" . $aJoin1['condition'] . ") OR (SUBSTRING(`" . $this->_sTable . "`.`type`, 1, " . strlen($sCommonPostPrefix) . ") = '" . $sCommonPostPrefix . "' AND " . $aJoin2['condition'] . "))";

                        //--- Exclude Own (Direct) posts on timelines of following members.
                        //--- Note. Disabled for now.
                        //$sWhereSubclause = $this->prepareAsString("IF(SUBSTRING(`{$this->_sTable}`.`type`, 1, " . strlen($sCommonPostPrefix) . ") = '" . $sCommonPostPrefix . "', `{$this->_sTable}`.`object_id` <> ?, 1)", $aParams['owner_id']);
                        $sWhereSubclause = "0";

                        //--- Select Promoted posts.
                        $sWhereSubclause .= " OR `{$this->_sTable}`.`promoted` <> '0'";
                        break;

                    //--- Profile + Profile Connections Feed
                    case BX_TIMELINE_TYPE_OWNER_AND_CONNECTIONS:
                        if(empty($aParams['owner_id']))
                            break;

                        $oConnection = BxDolConnection::getObjectInstance($this->_oConfig->getObject('conn_subscriptions'));

                        $aQueryParts = $oConnection->getConnectedContentAsSQLPartsExt($this->_sPrefix . 'events', 'owner_id', $aParams['owner_id']);
                        $aJoin1 = $aQueryParts['join'];

                        $aQueryParts = $oConnection->getConnectedContentAsSQLPartsExt($this->_sPrefix . 'events', 'object_id', $aParams['owner_id']);
                        $aJoin2 = $aQueryParts['join'];

                        //--- Join System and Direct posts made by following members. 'LEFT' join is essential to apply different conditions.
                        $sJoinClause .= " LEFT JOIN `" . $aJoin1['table'] . "` AS `" . $aJoin1['table_alias'] . "` ON ((" . $aJoin1['condition'] . ") OR (SUBSTRING(`" . $this->_sTable . "`.`type`, 1, " . strlen($sCommonPostPrefix) . ") = '" . $sCommonPostPrefix . "' AND " . $aJoin2['condition'] . "))";

                        //--- Select Own (System and Direct) posts from Profile's Timeline.
                        $sWhereSubclause = $this->prepareAsString("(`{$this->_sTable}`.`owner_id` = ?)", $aParams['owner_id']);

                        //--- Select Own Public (Direct) posts from Home Page Timeline (Public Feed).
                        $sWhereSubclause .= $this->prepareAsString(" OR (`{$this->_sTable}`.`owner_id` = '0' AND IF(SUBSTRING(`{$this->_sTable}`.`type`, 1, " . strlen($sCommonPostPrefix) . ") = '" . $sCommonPostPrefix . "', `{$this->_sTable}`.`object_id` = ?, 1))", $aParams['owner_id']);

                        //--- Exclude Own (Direct) posts on timelines of following members.
                        //--- Note. Disabled for now and next check is used instead. 
                        //$sWhereSubclause .= $this->prepareAsString(" OR (NOT ISNULL(`c`.`content`) AND IF(SUBSTRING(`{$this->_sTable}`.`type`, 1, " . strlen($sCommonPostPrefix) . ") = '" . $sCommonPostPrefix . "', `{$this->_sTable}`.`object_id` <> ?, 1))", $aParams['owner_id']);

                        //--- All posts on timelines of following members.
                        $sWhereSubclause .= " OR NOT ISNULL(`c`.`content`)";				

                        //--- Select Promoted posts.
                        $sWhereSubclause .= " OR `{$this->_sTable}`.`promoted` <> '0'";
                        break;
		}

		if(!empty($sWhereSubclause))
		    $sWhereClause .= "AND (" . $sWhereSubclause . ") ";

		return array($sJoinClause, $sWhereClause);
    }

    function updateSimilarObject($iId, &$oAlert, $sDuration = 'day')
    {
        $sType = $oAlert->sUnit;
        $sAction = $oAlert->sAction;

        //Check handler
        $aHandler = $this->_oConfig->getHandlers($sType . '_' . $sAction);
        if(empty($aHandler) || !is_array($aHandler) || (int)$aHandler['groupable'] != 1)
            return false;

        //Check content's extra values
        if(isset($aHandler['group_by']) && !empty($aHandler['group_by']) && (!isset($oAlert->aExtras[$aHandler['group_by']]) || empty($oAlert->aExtras[$aHandler['group_by']])))
            return false;

		$aBindings = array(
			'object_id' => $oAlert->iObject,
			'id' => $iId,
			'owner_id' => $oAlert->iSender,
			'type' => $sType,
			'action' => $sAction
		);

        $sWhereClause = "";
        switch($sDuration) {
            case 'day':
                $aBindings['day_start'] = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                $aBindings['day_end'] = mktime(23, 59, 59, date('m'), date('d'), date('Y'));

                $sWhereClause .= "AND `date`>:day_start AND `date`<:day_end ";
                break;
        }

        if(isset($aHandler['group_by'])) {
        	$aBindings['content'] = '%' . $oAlert->aExtras[$aHandler['group_by']] . '%';

            $sWhereClause .= "AND `content` LIKE :content ";
        }

        $sSql = "UPDATE `{$this->_sTable}`
            SET
                `object_id`=CONCAT(`object_id`, ',:object_id'),
                `title`='',
                `description`='',
                `date`=UNIX_TIMESTAMP()
            WHERE
                `id`<>:id AND
                `owner_id`=:owner_id AND
                `type`=:type AND
                `action`=:action " . $sWhereClause;
        $mixedResult = $this->query($sSql, $aBindings);

        if((int)$mixedResult > 0)
            $this->deleteEvent(array('id' => $iId));

        return $mixedResult;
    }
}

/** @} */
