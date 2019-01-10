<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    BasePayment Base classes for Payment like modules
 * @ingroup     UnaModules
 *
 * @{
 */

class BxBaseModPaymentDb extends BxBaseModGeneralDb
{
    function __construct(&$oConfig)
    {
        parent::__construct($oConfig);
    }

	public function insertModule($aData)
    {
    	$sQuery = $this->prepare("INSERT IGNORE INTO `" . $this->_sPrefix . "modules`(`name`) VALUES(?)", $aData['name']);
		$this->query($sQuery);
    }

    public function deleteModule($aData)
    {
    	$sQuery = $this->prepare("DELETE FROM `" . $this->_sPrefix . "modules` WHERE `name`=? LIMIT 1", $aData['name']);
    	$this->query($sQuery);
    }

    public function getModules()
    {
        $sQuery = $this->prepare("SELECT
				`tsm`.`id` AS `id`,
				`tm`.`name` AS `name`,
				`tsm`.`title` AS `title`,
				`tsm`.`uri` AS `uri`
            FROM `" . $this->_sPrefix . "modules` AS `tm`
            LEFT JOIN `sys_modules` AS `tsm` ON `tm`.`name`=`tsm`.`name`
            ORDER BY `tsm`.`date`");

        return $this->getAll($sQuery);
    }
}

/** @} */
