<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaStudio UNA Studio
 * @{
 */

class BxDolStudioDesignerQuery extends BxDolStudioPageQuery
{
    function __construct()
    {
        parent::__construct();
    }

    public function updateInjection($sName, $sValue)
    {
		return (int)$this->query("UPDATE `sys_injections` SET `data`=:data WHERE `name`=:name", array(
			'name' => $sName,
			'data' => $sValue
		)) > 0;
    }
    
}

/** @} */
