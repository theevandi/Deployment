<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Posts Posts
 * @ingroup     UnaModules
 *
 * @{
 */

require_once(BX_DIRECTORY_PATH_INC . "design.inc.php");

class BxPostsCronPublishing extends BxDolCron
{
	protected $_sModule;
	protected $_oModule;

	public function __construct()
    {
        parent::__construct();

    	$this->_sModule = 'bx_posts';
    	$this->_oModule = BxDolModule::getInstance($this->_sModule);
    }

    function processing()
    {
        $mixedIds = $this->_oModule->_oDb->publish();
        if($mixedIds === false)
        	return;

		foreach($mixedIds as $iId)
        	$this->_oModule->onPublished($iId);
    }
}

/** @} */
