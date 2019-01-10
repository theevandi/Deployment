<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Payment Payment
 * @ingroup     UnaModules
 * 
 * @{
 */


class BxPaymentGridProviders extends BxBaseModPaymentGridProviders
{
    public function __construct ($aOptions, $oTemplate = false)
    {
    	$this->_sModule = 'bx_payment';

        parent::__construct ($aOptions, $oTemplate);
    }
}

/** @} */
