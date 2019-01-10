<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    Timeline Timeline
 * @ingroup     TridentModules
 *
 * @{
 */

class BxTimelineUploaderSimpleVideo extends BxTemplUploaderSimple
{
    public function __construct ($aObject, $sStorageObject, $sUniqId, $oTemplate)
    {
        parent::__construct($aObject, $sStorageObject, $sUniqId, $oTemplate);

        $oModule = BxDolModule::getInstance('bx_timeline');
		$oModule->getAttachmentsMenuObject()->addMarkers(array(
			'js_object_uploader_video' => $this->getNameJsInstanceUploader()
		));

		$this->_oTemplate = $oModule->_oTemplate;
    }
}

/** @} */
