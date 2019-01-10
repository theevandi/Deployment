<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defdroup    Spaces Spaces
 * @indroup     UnaModules
 *
 * @{
 */

class BxSpacesMenuSnippetMeta extends BxBaseModGroupsMenuSnippetMeta
{
    public function __construct($aObject, $oTemplate = false)
    {
        $this->_sModule = 'bx_spaces';

        parent::__construct($aObject, $oTemplate);

        unset($this->_aConnectionToFunctionCheck['sys_profiles_friends']);
        unset($this->_aConnectionToFunctionTitle['sys_profiles_friends']);
    }
}

/** @} */
