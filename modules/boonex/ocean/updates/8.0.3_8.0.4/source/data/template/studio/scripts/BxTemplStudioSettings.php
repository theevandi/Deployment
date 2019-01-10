<?php
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    TridentView Trident Studio Representation classes
 * @ingroup     TridentStudio
 * @{
 */
defined('BX_DOL') or die('hack attempt');

bx_import('BxBaseStudioSettings');

class BxTemplStudioSettings extends BxBaseStudioSettings
{
    function __construct($sType = '', $sCategory = '')
    {
        parent::__construct($sType, $sCategory);
    }
}
/** @} */
