<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    TridentCore Trident Core
 * @{
 */

bx_import('BxBaseFormView');

class BxTemplFormView extends BxBaseFormView
{
    function __construct($aInfo, $oTemplate = false)
    {
        parent::__construct($aInfo, $oTemplate);
    }
}

/** @} */
