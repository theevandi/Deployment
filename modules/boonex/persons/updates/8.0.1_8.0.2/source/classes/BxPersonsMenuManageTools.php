<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    Persons Persons
 * @ingroup     DolphinModules
 *
 * @{
 */

bx_import('BxBaseModProfileMenuManageTools');

/**
 * 'Persons manage tools' menu.
 */
class BxPersonsMenuManageTools extends BxBaseModProfileMenuManageTools
{

    public function __construct($aObject, $oTemplate = false)
    {
        $this->MODULE = 'bx_persons';
        parent::__construct($aObject, $oTemplate);
    }
}

/** @} */
