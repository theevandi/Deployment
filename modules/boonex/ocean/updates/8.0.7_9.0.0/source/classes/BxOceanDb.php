<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    Ocean Ocean
 * @ingroup     TridentModules
 *
 * @{
 */

bx_import('BxBaseModGeneralDb');

/*
 * Module database queries
 */
class BxOceanDb extends BxBaseModGeneralDb
{
    function __construct(&$oConfig)
    {
        parent::__construct($oConfig);
    }
}

/** @} */
