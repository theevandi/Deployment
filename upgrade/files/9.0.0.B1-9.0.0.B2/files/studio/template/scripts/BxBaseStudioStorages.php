<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    TridentView Trident Studio Representation classes
 * @ingroup     TridentStudio
 * @{
 */

class BxBaseStudioStorages extends BxDolStudioStorages
{
	protected $aStorages;
	protected $sSubpageUrl;

    function __construct($sPage = '')
    {
        parent::__construct($sPage);

        $this->aStorages = array(
        	BX_DOL_STUDIO_STRG_TYPE_FILES => array('icon' => 'file-o'),
        	BX_DOL_STUDIO_STRG_TYPE_IMAGES => array('icon' => 'file-image-o')
        );

        $this->sSubpageUrl = BX_DOL_URL_STUDIO . 'storages.php?page=';
    }

	function getPageCss()
    {
        return array_merge(parent::getPageCss(), array());
    }

    function getPageJs()
    {
        return array_merge(parent::getPageJs(), array());
    }

    function getPageJsObject()
    {
        return '';
    }

    function getPageMenu($aMenu = array(), $aMarkers = array())
    {
        $aMenu = array();
        foreach($this->aStorages as $sName => $aStorage)
            $aMenu[] = array(
                'name' => $sName,
                'icon' => $aStorage['icon'],
                'link' => $this->sSubpageUrl . $sName,
                'title' => _t('_adm_lmi_cpt_' . $sName),
                'selected' => $sName == $this->sPage
            );

        return parent::getPageMenu($aMenu);
    }

    function getPageCode($bHidden = false)
    {
        $sMethod = 'get' . ucfirst($this->sPage);
        if(!method_exists($this, $sMethod))
            return '';

        return $this->$sMethod();
    }

	protected function getFiles()
    {
        return $this->getGrid(BX_DOL_STUDIO_STRG_TYPE_FILES);
    }

    protected function getImages()
    {
        return $this->getGrid(BX_DOL_STUDIO_STRG_TYPE_IMAGES);
    }

	protected function getGrid($sName)
    {
        $oGrid = BxDolGrid::getObjectInstance('sys_studio_strg_' . $sName);
        if(!$oGrid)
            return '';

        return BxDolStudioTemplate::getInstance()->parseHtmlByName('storages.html', array(
            'js_object' => $this->getPageJsObject(),
            'content' => $this->getBlockCode(array(
				'items' => $oGrid->getCode()
			))
        ));
    }
}

/** @} */
