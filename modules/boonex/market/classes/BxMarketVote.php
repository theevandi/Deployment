<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Market Market
 * @ingroup     UnaModules
 *
 * @{
 */

class BxMarketVote extends BxTemplVote
{
	protected $MODULE;
	protected $_oModule;

    function __construct($sSystem, $iId, $iInit = 1)
    {
    	$this->MODULE = 'bx_market';
    	$this->_oModule = BxDolModule::getInstance($this->MODULE);

        parent::__construct($sSystem, $iId, $iInit);        
    }

    public function isAllowedVote($isPerformAction = false)
    {
    	$CNF = &$this->_oModule->_oConfig->CNF;

    	if($this->getObjectAuthorId() == $this->_getAuthorId())
    		return false;

    	$oPrivacy = BxDolPrivacy::getObjectInstance($CNF['OBJECT_PRIVACY_VOTE']);
		if($oPrivacy && !$oPrivacy->check($this->_iId))
			return false;

    	return parent::isAllowedVote($isPerformAction);
    }

	protected function _getLabelCounter($iCount)
    {
        return _t('_bx_market_vote_counter', $iCount);
    }
}

/** @} */
