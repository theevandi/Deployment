<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    TridentCore Trident Core
 * @{
 */

/**
 * Site main menu representation.
 */
class BxBaseMenuSite extends BxTemplMenu
{
    public function __construct ($aObject, $oTemplate)
    {
        parent::__construct ($aObject, $oTemplate);
    }

    public function getCode ()
    {
        $s = parent::getCode ();
        return '<div id="bx-sliding-menu-' . $this->_sObject . '" class="bx-sliding-menu-main bx-def-z-index-nav bx-def-border-bottom" style="display:none;"><div class="bx-sliding-menu-main-cnt">' . $s . '</div></div>';
    }
}

/** @} */
