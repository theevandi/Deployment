<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defdroup    Channels Channels
 * @indroup     UnaModules
 *
 * @{
 */

/**
 * Create/Edit Channel Form.
 */
class BxCnlFormEntry extends BxBaseModGroupsFormEntry
{
    public function __construct($aInfo, $oTemplate = false)
    {
        $this->MODULE = 'bx_channels';
        parent::__construct($aInfo, $oTemplate);
    }
}

/** @} */
