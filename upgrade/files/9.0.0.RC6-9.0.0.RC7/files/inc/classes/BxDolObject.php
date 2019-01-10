<?php defined('BX_DOL') or defined('BX_DOL_INSTALL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaCore UNA Core
 * @{
 */

/**
 * Base class for all "Object" classes.
 * Child classes usually represents high level programming constructions to generate ready 'objects' functionality, like Comments, Votings, Forms.
 */
class BxDolObject extends BxDolFactory implements iBxDolReplaceable
{
    protected $_oTemplate = null;
	protected $_oQuery = null;

    protected $_iId = 0; ///< item id the action to be performed with
    protected $_sSystem = ''; ///< current system name
    protected $_aSystem = array(); ///< current system array

    protected $_aMarkers = array ();

    protected function __construct($sSystem, $iId, $iInit = true, $oTemplate = false)
    {
        parent::__construct();

        $aSystems = $this->getSystems();
        if(!isset($aSystems[$sSystem]))
            return;

        $this->_sSystem = $sSystem;
        $this->_aSystem = $aSystems[$sSystem];

        if ($oTemplate)
            $this->_oTemplate = $oTemplate;
        else
            $this->_oTemplate = BxDolTemplate::getInstance();

        if(!$this->isEnabled())
            return;

        if($iInit)
            $this->init($iId);
    }

    public function init($iId)
    {
        if(!$this->isEnabled())
            return false;

        if(empty($this->_iId) && $iId)
            $this->setId($iId);

		$this->addMarkers(array(
            'object_id' => $this->getId(),
            'user_id' => $this->_getAuthorId()
        ));

        return true;
    }

    public function getSystemId()
    {
        return $this->_aSystem['id'];
    }

    public function getSystemName()
    {
        return $this->_sSystem;
    }

    public function getSystemInfo()
    {
        return $this->_aSystem;
    }

    public function getId()
    {
        return $this->_iId;
    }

    public function setId($iId)
    {
        if($iId == $this->getId())
            return;

        $this->_iId = $iId;
    }

    public function isEnabled ()
    {
        return $this->_aSystem && (int)$this->_aSystem['is_on'] == 1;
    }

    public function isPerformed($iObjectId, $iAuthorId, $iAuthorIp = 0)
    {
        return $this->_oQuery->isPerformed($iObjectId, $iAuthorId);
    }

	/**
	 * Interface functions for outer usage
	 */
    public function getConditions($sMainTable, $sMainField)
    {
        if(!$this->isEnabled())
            return array();

        $sTable = isset($this->_aSystem['table_main']) ? $this->_aSystem['table_main'] : '';
        if(empty($sTable) || empty($sMainTable) || empty($sMainField))
            return array();

        return array(
            'join' => array (
                'objects_' . $this->_sSystem => array(
                    'type' => 'INNER',
                    'table' => $sTable,
                    'mainTable' => $sMainTable,
                    'mainField' => $sMainField,
                    'onField' => 'object_id',
                    'joinFields' => array('count'),
                ),
            ),
        );
    }

    public function getConditionsTrack($sMainTable, $sMainField, $iAuthorId = 0)
    {
        if(!$this->isEnabled())
            return array();

        $sTableTrack = isset($this->_aSystem['table_track']) ? $this->_aSystem['table_track'] : '';
        if(empty($sTableTrack) || empty($sMainTable) || empty($sMainField))
            return array();

        return array(
            'restriction' => array (
                'objects_' . $this->_sSystem . '_author' => array(
                    'value' => $iAuthorId,
                    'field' => 'author_id',
                    'operator' => '=',
                    'table' => $sTableTrack,
                ),
            ),
            'join' => array (
                'objects_' . $this->_sSystem => array(
                    'type' => 'INNER',
                    'table' => $sTableTrack,
                    'mainTable' => $sMainTable,
                    'mainField' => $sMainField,
                    'onField' => 'object_id',
                    'joinFields' => array('author_id'),
                ),
            ),

        );
    }
    
	public function getSqlParts($sMainTable, $sMainField)
    {
        if(!$this->isEnabled())
            return array();

        return $this->_oQuery->getSqlParts($sMainTable, $sMainField);
    }

    public function getSqlPartsTrack($sMainTable, $sMainField, $iAuthorId = 0)
    {
        if(!$this->isEnabled())
            return array();

        return $this->_oQuery->getSqlPartsTrack($sMainTable, $sMainField, $iAuthorId);
    }

    public function addMarkers($aMarkers)
    {
        if(empty($aMarkers) || !is_array($aMarkers))
			return false;

        $this->_aMarkers = array_merge($this->_aMarkers, $aMarkers);
        return true;
    }

    /**
     * Database functions
     */
    public function getQueryObject ()
    {
        return $this->_oQuery;
    }

	/**
     * Permissions functions
     */
	public function checkAction ($sAction, $isPerformAction = false)
    {
        $iId = $this->_getAuthorId();
        $a = checkActionModule($iId, $sAction, 'system', $isPerformAction);
        return $a[CHECK_ACTION_RESULT] === CHECK_ACTION_RESULT_ALLOWED;
    }

    public function checkActionErrorMsg ($sAction)
    {
        $iId = $this->_getAuthorId();
        $a = checkActionModule($iId, $sAction, 'system');
        return $a[CHECK_ACTION_RESULT] !== CHECK_ACTION_RESULT_ALLOWED ? $a[CHECK_ACTION_MESSAGE] : '';
    }

	/**
     * Actions' response functions
     */
	public function onObjectDelete($iObjectId = 0)
    {
        $this->_oQuery->deleteObjectEntries($iObjectId ? $iObjectId : $this->getId());
    }

	/**
     * Internal functions
     */
	protected function _getAuthorId ()
    {
        return isMember() ? bx_get_logged_profile_id() : 0;
    }

    protected function _getAuthorPassword ()
    {
        return isMember() ? $_COOKIE['memberPassword'] : "";
    }

    protected function _getAuthorIp ()
    {
        return getVisitorIP();
    }

    protected function _getAuthorInfo($iAuthorId = 0)
    {
        $oProfile = $this->_getAuthorObject($iAuthorId);

        return array(
            $oProfile->getDisplayName(),
            $oProfile->getUrl(),
            $oProfile->getThumb(),
            $oProfile->getUnit(),
            $oProfile->getUnit(0, array('template' => 'unit_wo_info'))
        );
    }

    protected function _getAuthorObject($iAuthorId = 0)
    {
    	if($iAuthorId == 0)
    		return BxDolProfileUndefined::getInstance();

        $oProfile = BxDolProfile::getInstance($iAuthorId);
        if(!$oProfile)
			$oProfile = BxDolProfileUndefined::getInstance();

        return $oProfile;
    }

    /**
     * Update Trigger table using data which is automatically gotten from object's internal table.
     */
	protected function _trigger()
    {
        if(!$this->_aSystem['trigger_table'])
            return false;

        $iId = $this->getId();
        if(!$iId)
            return false;

        return $this->_oQuery->updateTriggerTable($iId);
    }

    /**
     * Update (increment/decrement) Trigger table using provided value.
     */
    protected function _triggerValue($iValue)
    {
        if(!$this->_aSystem['trigger_table'])
            return false;

        $iId = $this->getId();
        if(!$iId)
            return false;

        return $this->_oQuery->updateTriggerTableValue($iId, $iValue);
    }

    /**
     * Replace provided markers in a string
     * @param $mixed string or array to replace markers in
     * @return string where all occured markers are replaced
     */
    protected function _replaceMarkers ($mixed)
    {
        return bx_replace_markers($mixed, $this->_aMarkers);
    }
}

/** @} */
