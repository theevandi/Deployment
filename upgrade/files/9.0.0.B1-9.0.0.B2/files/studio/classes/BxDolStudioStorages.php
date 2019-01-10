<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    TridentStudio Trident Studio
 * @{
 */

define('BX_DOL_STUDIO_STRG_TYPE_FILES', 'files');
define('BX_DOL_STUDIO_STRG_TYPE_IMAGES', 'images');

define('BX_DOL_STUDIO_STRG_TYPE_DEFAULT', BX_DOL_STUDIO_STRG_TYPE_FILES);

class BxDolStudioStorages extends BxTemplStudioPage
{
protected $sPage;

    function __construct($sPage = "")
    {
        parent::__construct('storages');

        $this->oDb = new BxDolStudioStoragesQuery();

        $this->sPage = BX_DOL_STUDIO_STRG_TYPE_DEFAULT;
        if(is_string($sPage) && !empty($sPage))
            $this->sPage = $sPage;
    }

    public function init()
	{
        if(($sAction = bx_get('strg_action')) === false) 
        	return;

		$sAction = bx_process_input($sAction);

		$aResult = array('code' => 1, 'message' => _t('_adm_strg_err_cannot_process_action'));
		switch($sAction) {
			case 'get-page-by-type':
				$sValue = bx_process_input(bx_get('nav_value'));
				if(empty($sValue))
					break;

				$this->sPage = $sValue;
				$aResult = array('code' => 0, 'content' => $this->getPageCode());
				break;

			default:
				$sMethod = 'action' . $this->getClassName($sAction);
				if(method_exists($this, $sMethod))
					$aResult = $this->$sMethod();
		}

		echo json_encode($aResult);
		exit;
	}
}

/** @} */
