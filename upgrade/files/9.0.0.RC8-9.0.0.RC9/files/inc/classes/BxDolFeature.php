<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaCore UNA Core
 * @{
 */

define('BX_DOL_FEATURED_USAGE_BLOCK', 'block');
define('BX_DOL_FEATURED_USAGE_INLINE', 'inline');
define('BX_DOL_FEATURED_USAGE_DEFAULT', BX_DOL_FEATURED_USAGE_BLOCK);

/**
 * Track any object feature automatically
 *
 * Add record to sys_object_feature table to track object feature,
 * to record feature just create this class instance with your object id,
 * for example:
 * @code
 *  BxDolFeature::getObjectInstance('my_system', 25); // 25 - is object id
 * @endcode
 *
 * Description of sys_object_feature table fields:
 * @code
 *  `name` - system name, it is better to use unique module prefix here, lowercase and all spaces are underscored
 *  `table_track` - table to track feature
 *  `is_on` - is the system activated
 *  `trigger_table` - table where you need to update feature field
 *  `trigger_field_id` - table field id to unique determine object
 *  `trigger_field_count` - table field where the featuring date is stored
 *  `class_name` - your custom class name, if you overrride default class
 *  `class_file` - your custom class path
 * @endcode
 *
 */
class BxDolFeature extends BxDolObject
{
    protected $_sBaseUrl;

    protected function __construct($sSystem, $iId, $iInit = true, $oTemplate = false)
    {
        parent::__construct($sSystem, $iId, $iInit, $oTemplate);
        if(empty($this->_sSystem))
            return;

        $this->_oQuery = new BxDolFeatureQuery($this);

        $this->_sBaseUrl = BxDolPermalinks::getInstance()->permalink($this->_aSystem['base_url']);
        if(get_mb_substr($this->_sBaseUrl, 0, 4) != 'http')
            $this->_sBaseUrl = BX_DOL_URL_ROOT . $this->_sBaseUrl;
    }

   /**
     * get feature object instanse
     * @param $sSys feature object name
     * @param $iId associated content id, where feature is available
     * @param $iInit perform initialization
     * @return null on error, or ready to use class instance
     */
    public static function getObjectInstance($sSys, $iId, $iInit = true)
    {
        if(isset($GLOBALS['bxDolClasses']['BxDolFeature!' . $sSys . $iId]))
            return $GLOBALS['bxDolClasses']['BxDolFeature!' . $sSys . $iId];

        $aSystems = self::getSystems();
        if(!isset($aSystems[$sSys]))
            return null;

        $sClassName = 'BxTemplFeature';
        if(!empty($aSystems[$sSys]['class_name'])) {
            $sClassName = $aSystems[$sSys]['class_name'];
            if(!empty($aSystems[$sSys]['class_file']))
                require_once(BX_DIRECTORY_PATH_ROOT . $aSystems[$sSys]['class_file']);
        }

        $o = new $sClassName($sSys, $iId, $iInit);
        return ($GLOBALS['bxDolClasses']['BxDolFeature!' . $sSys . $iId] = $o);
    }

    public static function &getSystems()
    {
        $sKey = 'bx_dol_cache_memory_feature_systems';

        if(!isset($GLOBALS[$sKey]))
            $GLOBALS[$sKey] = BxDolDb::getInstance()->fromCache('sys_objects_feature', 'getAllWithKey', '
                SELECT
                    `id` as `id`,
                    `name` AS `name`,
                    `is_on` AS `is_on`,
                    `is_undo` AS `is_undo`,
                    `base_url` AS `base_url`,
                    `trigger_table` AS `trigger_table`,
                    `trigger_field_id` AS `trigger_field_id`,
                    `trigger_field_author` AS `trigger_field_author`,
                    `trigger_field_flag` AS `trigger_field_flag`,
                    `class_name` AS `class_name`,
                    `class_file` AS `class_file`
                FROM `sys_objects_feature`', 'name');

        return $GLOBALS[$sKey];
    }

	/**
     * Actions functions
     */
    public function actionFeature()
    {
        return echoJson($this->_doFeature());
    }

    public function actionGetFeatureBy()
    {
        return '';
    }

    /**
     * Permissions functions
     */
    public function isAllowedFeature($isPerformAction = false)
    {
        if(isAdmin())
            return true;

        return $this->checkAction('feature', $isPerformAction);
    }

	public function msgErrAllowedFeature()
    {
        return $this->checkActionErrorMsg('feature');
    }

    /**
     * Auxiliary functions
     */
    public function isUndo()
    {
        return (int)$this->_aSystem['is_undo'] == 1;
    }

	/**
     * Internal functions
     */
	protected function _doFeature()
    {
        if (!$this->isEnabled())
           return array('code' => 1, 'message' => _t('_feature_err_not_enabled'));

        $iObjectId = $this->getId();
        $iObjectAuthorId = $this->_oQuery->getObjectAuthorId($iObjectId);
        $iAuthorId = $this->_getAuthorId();

        $bUndo = $this->isUndo();
        $bPerformed = $this->isPerformed($iObjectId, $iAuthorId);
        $bPerformUndo = $bPerformed && $bUndo ? true : false;

        if(!$bPerformUndo && !$this->isAllowedFeature())
            return array('code' => 2, 'message' => $this->msgErrAllowedFeature());

        if($bPerformed && !$bUndo)
        	return array('code' => 3, 'message' => _t('_feature_err_duplicate_feature'));

        if(!$this->_triggerValue($bPerformUndo ? 0 : time()))
            return array('code' => 4, 'message' => _t('_feature_err_cannot_perform_action'));
            
        if(!$bPerformUndo)
            $this->isAllowedFeature(true);

        bx_alert($this->_sSystem, ($bPerformUndo ? 'un' : '') . 'feature', $iObjectId, $iAuthorId, array('object_author_id' => $iObjectAuthorId));
        bx_alert('feature', ($bPerformUndo ? 'un' : '') . 'do', 0, $iAuthorId, array('object_system' => $this->_sSystem, 'object_id' => $iObjectId, 'object_author_id' => $iObjectAuthorId));

        return array(
        	'eval' => $this->getJsObjectName() . '.onFeature(oData, oElement)',
        	'code' => 0, 
        	'label_icon' => $this->_getIconDoFeature(!$bPerformed),
            'label_title' => _t($this->_getTitleDoFeature(!$bPerformed)),
            'disabled' => !$bPerformed && !$bUndo
        );
    }

    protected function _getIconDoFeature($bPerformed)
    {
    	return $bPerformed && $this->isUndo() ? 'far star' : 'star';
    }

    protected function _getTitleDoFeature($bPerformed)
    {
    	return $bPerformed && $this->isUndo() ? '_feature_do_unfeature' : '_feature_do_feature';
    }
}

/** @} */
