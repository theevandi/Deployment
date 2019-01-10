<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    MassMailer Mass Mailer
 * @ingroup     UnaModules
 *
 * @{
 */

/**
 * Entry create/edit pages
 */
class BxMassMailerPageEntry extends BxTemplPage
{
    public function __construct($aObject, $oTemplate = false)
    {
        $this->MODULE = 'bx_massmailer';
        $CNF = &$this->_oModule->_oConfig->CNF;
        $CNF['TABLE_ENTRIES'] = $CNF['TABLE_CAMPAIGNS'];
        parent::__construct($aObject, $oTemplate);
    }
}

/** @} */
