<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaBaseView UNA Base Representation Classes
 * @{
 */

class BxBaseCmtsForm extends BxTemplFormView
{
    protected static $_sAttributeMaskId;
    protected static $_sAttributeMaskName;
    
    public function __construct($aInfo, $oTemplate)
    {
        parent::__construct($aInfo, $oTemplate);

        if(empty(self::$_sAttributeMaskId))
            self::$_sAttributeMaskId = $this->aFormAttrs['id'];

        if(empty(self::$_sAttributeMaskName))
            self::$_sAttributeMaskName = $this->aFormAttrs['name'];

    	if(isset($this->aInputs['cmt_image'])) {
            $aFormNested = array(
                'params' =>array(
                    'nested_form_template' => 'comments_uploader_nfw.html'
                ),
                'inputs' => array(),
            );

            $oFormNested = new BxDolFormNested('cmt_image', $aFormNested, 'cmt_submit');

            $this->aInputs['cmt_image']['storage_object'] = 'sys_cmts_images';
            $this->aInputs['cmt_image']['images_transcoder'] = 'sys_cmts_images_preview';
            $this->aInputs['cmt_image']['uploaders'] = !empty($this->aInputs['cmt_image']['value']) ? unserialize($this->aInputs['cmt_image']['value']) : array('sys_cmts_simple');
            $this->aInputs['cmt_image']['upload_buttons_titles'] = array('Simple' => 'camera');
            $this->aInputs['cmt_image']['multiple'] = true;
            $this->aInputs['cmt_image']['ghost_template'] = $oFormNested;
        }
    }

    public function getAttributeMask($sAttribute)
    {
        $sName = '_sAttributeMask' . bx_gen_method_name($sAttribute);
        return isset(self::$$sName) ? self::$$sName : '';
    }

	public function getStorageObjectName()
    {
        return isset($this->aInputs['cmt_image']['storage_object']) ? $this->aInputs['cmt_image']['storage_object'] : '';
    }

    public function getTranscoderPreviewName()
    {
    	return isset($this->aInputs['cmt_image']['images_transcoder']) ? $this->aInputs['cmt_image']['images_transcoder'] : '';
    }
    
    function initChecker ($aValues = array (), $aSpecificValues = array())
    {
        if (isset($this->aInputs['cmt_anonymous']) && isset($aValues['cmt_author_id']))
            $this->aInputs['cmt_anonymous']['checked'] = $aValues['cmt_author_id'] < 0;
        
        parent::initChecker ($aValues, $aSpecificValues);
    }
    
    public function insert ($aValsToAdd = array(), $isIgnore = false)
    {
        $aValsToAdd['cmt_author_id'] *= isset($this->aInputs['cmt_anonymous']) && $this->getCleanValue('cmt_anonymous') ? -1 : 1;

        return parent::insert ($aValsToAdd, $isIgnore);
    }

    function update ($iCmtId, $aValsToAdd = array(), &$aTrackTextFieldsChanges = null)
    {
        if (isset($this->aInputs['cmt_anonymous'])) {
            $aCmt = BxDolCmts::getObjectInstance($this->getCleanValue('sys'), $this->getCleanValue('id'))->getCommentRow($iCmtId);

            $aValsToAdd['cmt_author_id'] = ($this->getCleanValue('cmt_anonymous') ? -1 : 1) * abs($aCmt['cmt_author_id']);
        }

        return parent::update ($iCmtId, $aValsToAdd, $aTrackTextFieldsChanges);
    }
}

/** @} */
