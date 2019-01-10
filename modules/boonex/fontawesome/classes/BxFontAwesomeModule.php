<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    FontAwesome Font Awesome Pro integration
 * @ingroup     UnaModules
 *
 * @{
 */

class BxFontAwesomeModule extends BxDolModule
{
    function __construct(&$aModule)
    {
        parent::__construct($aModule);
    }

    public function serviceSwitchLightFont($bEnable)
    {
        $this->_oDb->switchLightFont($bEnable);
        BxDolCacheUtilities::getInstance()->clear('css');
        BxDolCacheUtilities::getInstance()->clear('db');
    }
}

/** @} */
