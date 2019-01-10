<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    TridentStudio Trident Studio
 * @{
 */

class BxDolStudioDashboardQuery extends BxDolStudioPageQuery
{
    function __construct()
    {
        parent::__construct();
    }

    function getModuleStorageSize($sModule)
    {
    	$sSql = "SELECT SUM(`current_size`) AS `size` FROM `sys_objects_storage` WHERE `object` LIKE " . $this->escape($sModule . '%') . " LIMIT 1";
    	return $this->getOne($sSql);
    }
}

/** @} */
