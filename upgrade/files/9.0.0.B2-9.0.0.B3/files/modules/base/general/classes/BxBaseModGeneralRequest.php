<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    BaseGeneral Base classes for modules
 * @ingroup     UnaModules
 *
 * @{
 */

bx_import('BxDolLanguages');

class BxBaseModGeneralRequest extends BxDolRequest
{
    function __construct()
    {
        parent::__construct();
    }
}

/** @} */
