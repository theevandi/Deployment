<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    TridentStudio Trident Studio
 * @{
 */

bx_import('BxTemplRss');

class BxDolStudioRssPageHelp extends BxTemplRss
{
    function __construct($aObject)
    {
        parent::__construct($aObject);
    }

    public function getUrl($mixedId)
    {
    	bx_import('BxDolStudioPage');
    	$oPage = new BxDolStudioPage($mixedId);
		return $oPage->getRssHelpUrl();
    }
}

/** @} */
