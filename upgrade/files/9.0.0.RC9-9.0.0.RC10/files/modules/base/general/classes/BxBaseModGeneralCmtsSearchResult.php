<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    BaseGeneral Base classes for modules
 * @ingroup     UnaModules
 *
 * @{
 */

class BxBaseModGeneralCmtsSearchResult extends BxBaseModGeneralSearchResult
{
    protected $sModule;
    protected $sModuleObjectComments;
    protected $aCommentsAddons;

    function __construct($sMode = '', $aParams = array())
    {
        parent::__construct($sMode, $aParams);

        $this->oModule = $this->getMain();
        $this->sModuleObjectComments = $this->oModule->_oConfig->CNF['OBJECT_COMMENTS'];
        $this->aCommentsAddons = array();

        $this->aCurrent = array(
            'name' => $this->oModule->_oConfig->getName() . '_cmts',
            'module_name' => $this->oModule->_oConfig->getName(),
            'object_metatags' => 'sys_cmts',
            'title' => '',
            'table' => $this->oModule->_oConfig->getDbPrefix() . 'cmts',
            'ownFields' => array('cmt_id', 'cmt_object_id', 'cmt_author_id', 'cmt_text', 'cmt_time'),
            'searchFields' => array('cmt_text'),
            'restriction' => array(
                'author' => array('value' => '', 'field' => 'cmt_author_id', 'operator' => '='),
            ),
            'paginate' => array('start' => 0),
            'sorting' => 'last',
            'ident' => 'cmt_id'
        );

        $this->sBrowseUrl = BX_DOL_SEARCH_KEYWORD_PAGE;
    }

    function getMain()
    {
        return BxDolModule::getInstance($this->sModule);
    }

    function displaySearchUnit($aData)
    {
        $oCmts = BxDolCmts::getObjectInstance($this->sModuleObjectComments, $aData['object_id']);
        $oCmts->addCssJs();

        if($this->_bLiveSearch) {
            $CNF = &$this->oModule->_oConfig->CNF;

            return $oCmts->getCommentLiveSearch($aData['id'], array(
                'txt_sample_single' => isset($CNF['T']['txt_sample_comment_single']) ? $CNF['T']['txt_sample_comment_single'] : ''
            ));
        }
        else {
            if(!isset($this->aCommentsAddons[$aData['object_id']]))
                $this->aCommentsAddons[$aData['object_id']] = '';

            return $oCmts->getCommentSearch(isset($aData['cmt_id']) ? $aData['cmt_id'] : $aData['id'], $this->aCommentsAddons[$aData['object_id']]);
        }
    }

    function displayResultBlock ()
    {
        $sCode = '<ul class="cmts">' . parent::displayResultBlock() . '</ul>';

        if(!empty($this->aCommentsAddons) && is_array($this->aCommentsAddons))
            foreach($this->aCommentsAddons as $sCommentsAddon)
                $sCode .= $sCommentsAddon;

        return $sCode;
    }

    function getAlterOrder()
    {
        if($this->aCurrent['sorting'] == 'last')
            return array(
            	'order' => " ORDER BY `" . $this->aCurrent['table'] . "`.`cmt_time` DESC"
            );

        return array();
    }

    function _getPseud ()
    {
        return array(
            'id' => 'cmt_id',
        	'object_id' => 'cmt_object_id',
            'author' => 'cmt_author_id',
            'text' => 'cmt_text',
            'added' => 'cmt_time'
        );
    }
}

/** @} */
