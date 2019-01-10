<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaStudio UNA Studio
 * @{
 */

bx_import('BxDolStudioUtils');

define('BX_DOL_STUDIO_BP_SKELETONS', 'skeletons');
define('BX_DOL_STUDIO_BP_TYPE_DEFAULT', BX_DOL_STUDIO_MODULE_SYSTEM);

define('BX_DOL_STUDIO_BP_BLOCK_RAW', 'raw');
define('BX_DOL_STUDIO_BP_BLOCK_HTML', 'html');
define('BX_DOL_STUDIO_BP_BLOCK_RSS', 'rss');
define('BX_DOL_STUDIO_BP_BLOCK_IMAGE', 'image');
define('BX_DOL_STUDIO_BP_BLOCK_LANG', 'lang');
define('BX_DOL_STUDIO_BP_BLOCK_MENU', 'menu');
define('BX_DOL_STUDIO_BP_BLOCK_SERVICE', 'service');

class BxDolStudioBuilderPage extends BxTemplStudioPage
{
    protected $sType;
    protected $sPage;
    protected $sPageBaseUrl;
    protected $aPageRebuild;

    function __construct($sType = '', $sPage = '')
    {
        parent::__construct('builder_pages');

        $this->oDb = new BxDolStudioBuilderPageQuery();

        $this->sType = BX_DOL_STUDIO_BP_TYPE_DEFAULT;
        if(is_string($sType) && !empty($sType))
            $this->sType = $sType;

        $this->sPage = '';
        $this->sPageBaseUrl = 'page.php?i=';
        $this->aPageRebuild = array();
        if(is_string($sPage) && !empty($sPage)) {
            $this->sPage = $sPage;

            $this->aPageRebuild = array();
            $this->oDb->getPages(array('type' => 'by_object_full', 'value' => $this->sPage), $this->aPageRebuild, false);
            if(empty($this->aPageRebuild) || !is_array($this->aPageRebuild)) {
                $this->sPage = '';
                $this->aPageRebuild = array();
            }
        }
    }

    function init()
    {
        if(($sAction = bx_get('bp_action')) === false) 
        	return;

		$sAction = bx_process_input($sAction);

		$aResult = array('code' => 1, 'message' => _t('_adm_bp_err_cannot_process_action'));
		switch($sAction) {
			case 'reorder':
				if(empty($this->aPageRebuild) || !is_array($this->aPageRebuild))
					break;

				$bResult = false;
				for($i = 1; $i <= $this->aPageRebuild['layout_cells_number']; $i++) {
					$aItems = bx_get('bp_items_' . $i);
					$iItems = count($aItems);

					for($j = 0; $j < $iItems; $j++)
					$bResult |= $this->oDb->updateBlock((int)$aItems[$j], array(
						'cell_id' => $i,
						'order' => $j
					));
				}
				$aResult = $bResult ? array('code' => 0, 'message' => _t('_adm_bp_scs_save')) : array('code' => 1, 'message' => _t('_adm_bp_err_nothing_changed'));
				break;

			default:
				$sMethod = 'action' . $this->getClassName($sAction);
				if(method_exists($this, $sMethod))
			    	$aResult = $this->$sMethod();
		}

		echo json_encode($aResult);
		exit;
    }

    protected function onSaveBlock(&$oForm, &$aBlock)
    {
        $iDesignboxId = (int)str_replace($this->sSelectKeyPrefix, '', $oForm->getCleanValue('designbox_id'));
        BxDolForm::setSubmittedValue('designbox_id', $iDesignboxId, $oForm->aFormAttrs['method']);

        $iVisibleFor = BxDolStudioUtils::getVisibilityValue($oForm->getCleanValue('visible_for'), $oForm->getCleanValue('visible_for_levels'));
        BxDolForm::setSubmittedValue('visible_for_levels', $iVisibleFor, $oForm->aFormAttrs['method']);
        unset($oForm->aInputs['visible_for']);

        //--- Process Lang fields
        if($aBlock['type'] == BX_DOL_STUDIO_BP_BLOCK_LANG && isset($oForm->aInputs['content']))
            $this->onSaveBlockLang($oForm, $aBlock);

        //--- Process Image fields
        if($aBlock['type'] == BX_DOL_STUDIO_BP_BLOCK_IMAGE && isset($oForm->aInputs['image_file'], $oForm->aInputs['image_align']))
            $this->onSaveBlockImage($oForm, $aBlock);

        //--- Process RSS fields
        if($aBlock['type'] == BX_DOL_STUDIO_BP_BLOCK_RSS && isset($oForm->aInputs['rss_url'], $oForm->aInputs['rss_length']))
            $this->onSaveBlockRss($oForm, $aBlock);
    }

    protected function onSaveBlockLang(&$oForm, &$aBlock)
    {
        $oLanguage = BxDolStudioLanguagesUtils::getInstance();

        $sContentKey = '';
        $sContentValue = $oForm->getCleanValue('content');
        if($aBlock['content'] == '') {
            $sContentKey = '_sys_bpb_content_' . $aBlock['id'];
            $oLanguage->addLanguageString($sContentKey, $sContentValue);
        } else {
            $sContentKey = $aBlock['content'];
            $oLanguage->updateLanguageString($sContentKey, $sContentValue);
        }
        BxDolForm::setSubmittedValue('content', $sContentKey, $oForm->aFormAttrs['method']);
    }

    protected function onSaveBlockImage(&$oForm, &$aBlock)
    {
        $iImageId = 0;
        if($aBlock['content'] != '')
            list($iImageId) = explode($this->sParamsDivider, $aBlock['content']);

        $iImageId = $oForm->processImageUploaderSave('image_file', $iImageId);
        if(is_string($iImageId) && !is_numeric($iImageId))
            return array('msg' => $iImageId);

        $sImageAlign = $oForm->getCleanValue('image_align');

        unset($oForm->aInputs['image_file'], $oForm->aInputs['image_align']);
        BxDolForm::setSubmittedValue('content', implode($this->sParamsDivider, array($iImageId, $sImageAlign)), $oForm->aFormAttrs['method']);
    }
    
    protected function onSaveBlockRss(&$oForm, &$aBlock)
    {
        $aRss = array(
            trim($oForm->getCleanValue('rss_url')),
            $oForm->getCleanValue('rss_length')
        );

        unset($oForm->aInputs['rss_url'], $oForm->aInputs['rss_length']);
        BxDolForm::setSubmittedValue('content', implode($this->sParamsDivider, $aRss), $oForm->aFormAttrs['method']);
    }

    protected function addInArray($aInput, $sKey, $aValues)
    {
        return BxDolStudioUtils::addInArray($aInput, $sKey, $aValues);
    }

    protected function getModuleIcon($sName, $sType = 'menu', $bReturnAsUrl = true)
    {
    	$sResult = '';

    	switch($sName) {
    		case BX_DOL_STUDIO_MODULE_SYSTEM:
    			$sResult = 'cog';
    			break;

    		case BX_DOL_STUDIO_MODULE_CUSTOM:
    			$sResult = 'wrench';
    			break;

    		case BX_DOL_STUDIO_BP_SKELETONS:
    			$sResult = 'qrcode';
    			break;

    		default:
    			$sResult = parent::getModuleIcon($sName, $sType, $bReturnAsUrl);
    	}

        return $sResult;
    }
}

/** @} */
