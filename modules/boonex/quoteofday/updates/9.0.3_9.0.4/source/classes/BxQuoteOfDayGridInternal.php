<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    QuoteOfTheDay Quote of the Day
 * @ingroup     UnaModules
 *
 * @{
 */

class BxQuoteOfDayGridInternal extends BxTemplGrid
{
    protected $MODULE;
    protected $_oModule;
    
    public function __construct ($aOptions, $oTemplate = false)
    {
        $this->MODULE = 'bx_quoteofday';
        $this->_oModule = BxDolModule::getInstance($this->MODULE);
        parent::__construct ($aOptions, $oTemplate);
    }
    
    protected function _getCellText($mixedValue, $sKey, $aField, $aRow)
    {
        $mixedValue = $this->_limitMaxLength(strip_tags(htmlspecialchars_decode($aRow['text'])), $sKey, $aField, $aRow, $this->_isDisplayPopupOnTextOverflow);
        return parent::_getCellDefault ($mixedValue, $sKey, $aField, $aRow);
    }
    
    public function performActionAdd()
    {
        $sAction = 'add';
        $oForm = BxDolForm::getObjectInstance('bx_quoteofday', 'bx_quoteofday_entry_add'); // get form instance for specified form object and display
        if (!$oForm)
            return '';
        $oForm->aFormAttrs['action'] = BX_DOL_URL_ROOT . 'grid.php?' . bx_encode_url_params($_GET, array('ids', '_r'));
        $oForm->initChecker();
        if($oForm->isSubmittedAndValid()) {
            $mixedResult = $oForm->insert(array('added' => time()));
            if(is_numeric($mixedResult))
                $aRes = array('grid' => $this->getCode(false), 'blink' => $mixedResult);
            else
                $aRes = array('msg' => $mixedResult);
            echoJson($aRes);
        }
        else {
            $sContent = BxTemplStudioFunctions::getInstance()->popupBox('bx_quoteofday_form_add', _t('_bx_quoteofday_form_add_title'), $this->_oModule->_oTemplate->parseHtmlByName('manage_item.html', array(
                'form_id' => $oForm->id,
                'form' => $oForm->getCode(true),
                'object' => $this->_sObject,
                'action' => $sAction
            )));
           echoJson(array('popup' => array('html' => $sContent, 'options' => array('closeOnOuterClick' => true))));
        }
    }
    
    public function performActionEdit()
    {
        $sAction = 'edit';
        $aIds = bx_get('ids');
        $iId = $aIds[0];
        $oForm = BxDolForm::getObjectInstance('bx_quoteofday', 'bx_quoteofday_entry_edit'); // get form instance for specified form object and display
        if (!$oForm)
            return '';
        $oForm->aFormAttrs['action'] = BX_DOL_URL_ROOT . 'grid.php?' . bx_encode_url_params($_GET, array('_r'));
        $aContentInfo = array();
        $aContentInfo = $this->_oModule->_oDb->getContentInfoById($iId);
        $oForm->initChecker($aContentInfo, array());
        if($oForm->isSubmittedAndValid()) {
            $mixedResult = $oForm->update($iId);
            if(is_numeric($mixedResult))
                $aRes = array('grid' => $this->getCode(false), 'blink' => $mixedResult);
            else
                $aRes = array('msg' => $mixedResult);
            echoJson($aRes);
        }
        else {
            
            $sContent = BxTemplStudioFunctions::getInstance()->popupBox('bx_quoteofday_form_edit', _t('_bx_quoteofday_form_edit_title'), $this->_oModule->_oTemplate->parseHtmlByName('manage_item.html', array(
                'form_id' => $oForm->id,
                'form' => $oForm->getCode(true),
                'object' => $this->_sObject,
                'action' => $sAction
            )));
           echoJson(array('popup' => array('html' => $sContent, 'options' => array('closeOnOuterClick' => false))));
        }
    }
    
    public function performActionPublish()
    {
        $aIds = bx_get('ids');
        $iId = $aIds[0];
        $aContentInfo = $this->_oModule->_oDb->getContentInfoById($iId);
        $this->_oModule->removeQuoteFromCache();
        $this->_oModule->putQuoteToCache($aContentInfo["text"]);
        $aRes = array('msg' => _t('_bx_quoteofday_grid_action_title_adm_publish_text'));
        echoJson($aRes);
    }
}

/** @} */
