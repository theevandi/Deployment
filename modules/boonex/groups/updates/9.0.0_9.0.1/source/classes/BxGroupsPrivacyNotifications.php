<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    Groups Groups
 * @ingroup     TridentModules
 *
 * @{
 */

class BxGroupsPrivacyNotifications extends BxBaseModGroupsPrivacyNotifications
{
    function __construct($aOptions, $oTemplate = false)
    {
    	$this->MODULE = 'bx_groups';
        parent::__construct($aOptions, $oTemplate);
    }
}

/** @} */
