<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Polls Polls
 * @ingroup     UnaModules
 *
 * @{
 */

/*
 * Module representation.
 */
class BxPollsTemplate extends BxBaseModTextTemplate
{
    /**
     * Constructor
     */
    function __construct(&$oConfig, &$oDb)
    {
        $this->MODULE = 'bx_polls';
        parent::__construct($oConfig, $oDb);
    }

    public function getJsCode($sType, $aParams = array(), $bWrap = true)
    {
        $aParams = array_merge(array(
            'aHtmlIds' => $this->_oConfig->getHtmlIds()
        ), $aParams);

        return parent::getJsCode($sType, $aParams, $bWrap);
    }

    public function entrySubentries ($aData, $bDynamic = false)
    {
        $sContent = $this->_getGetBlockContentSubentries($aData, $bDynamic);
        if(empty($sContent))
            return '';

    	return array(
            'content' => $sContent,
            'menu' => $this->_getGetBlockMenu($aData, 'subentries')
        );
    }

    public function entryResults($aData, $bDynamic = false)
    {
        $sContent = $this->_getGetBlockContentResults($aData, $bDynamic);
        if(empty($sContent))
            return '';

        return array(
            'content' => $sContent,
            'menu' => $this->_getGetBlockMenu($aData, 'results')
        );
    }

    public function entryTextAndSubentries($aData, $bForceDisplaySubentries = false)
    {
        $CNF = &$this->getModule()->_oConfig->CNF;

        $sMethod = '_getGetBlockContent';
        $sMenuItem = '';
        if(!$bForceDisplaySubentries && $this->_oDb->isPerformed($aData[$CNF['FIELD_ID']], bx_get_logged_profile_id())) {
            $sMethod .= 'Results';
            $sMenuItem = 'results';
        }
        else {
            $sMethod .= 'Subentries';
            $sMenuItem = 'subentries';
        };

        $sSubentries = $this->$sMethod($aData);
        $oMenu = $this->_getGetBlockMenu($aData, $sMenuItem);

        $sMenu = str_replace('_', '-', $this->_oConfig->getName()) . '-menu-db';
        $sMenuId = $sMenu . '-' . time() . rand(0, PHP_INT_MAX);        

        $aTmplVars = parent::getTmplVarsText($aData);
        $aTmplVars = array_merge($aTmplVars, array(
            'menu' => BxTemplFunctions::getInstance()->designBoxMenu($oMenu, array(
                'menu' => array(
                    'id' => $sMenuId, 
                    'class' => $sMenu, 
                    'onclick' => "bx_menu_slide_inline('#" . $sMenuId . "', this, $(this).parents('.bx-db-menu:first'))"
                )
            )),
            'bx_if:show_subentries' => array(
                'condition' => !empty($sSubentries),
                'content' => array(
                    'entry_subentries' => $sSubentries
                )
            )
        ));

        return array(
            'content' => $this->parseHtmlByName('entry-text-subentries.html', $aTmplVars),
            'menu' => $oMenu
        );
    }

    protected function getTitle($aData)
    {
        return $this->_oConfig->getTitle($aData);
    }

    protected function getSummary($aData, $sTitle = '', $sText = '', $sUrl = '')
    {
        $CNF = &$this->getModule()->_oConfig->CNF;

        $aBlock = $this->{$this->_oDb->isPerformed($aData[$CNF['FIELD_ID']], bx_get_logged_profile_id()) ? 'entryResults' : 'entrySubentries'}($aData);

        return $aBlock['content'];
    }

    protected function getUnitThumbAndGallery ($aData)
    {
        return array('', '');
    }

    protected function _getGetBlockMenu($aData, $sSelected = '')
    {
        $CNF = &$this->getModule()->_oConfig->CNF;

        $sPostfix = '-' . time() . rand(0, PHP_INT_MAX);
        $sJsObject = $this->_oConfig->getJsObject('entry');
        $iContentId = $aData[$CNF['FIELD_ID']];

        $aBlocks = array(
            'subentries' => true, 
            'results' => (int)$aData[$CNF['FIELD_HIDDEN_RESULTS']] == 0 || $this->_oDb->isPerformed($iContentId, bx_get_logged_profile_id())
        );

        $aMenu = array();
        foreach($aBlocks as $sBlock => $bActive) {
            if(!$bActive) 
                continue;

            $sId = $this->_oConfig->getHtmlIds('block_link_' . $sBlock) . $sPostfix;
            if(!empty($sSelected) && $sSelected == $sBlock)
                $sSelected = $sId;

            $aMenu[] = array('id' => $sId, 'name' => $sId, 'class' => '', 'link' => 'javascript:void(0)', 'onclick' => 'javascript:' . $sJsObject . '.changeBlock(this, \'' . $sBlock . '\', ' . $iContentId . ')', 'target' => '_self', 'title' => _t('_bx_polls_menu_item_view_' . $sBlock));
        }

        if(count($aMenu) <= 1)
            return '';

        $oMenu = new BxTemplMenuInteractive(array('template' => 'menu_interactive_vertical.html', 'menu_id' => $this->_oConfig->getHtmlIds('block_menu') . $sPostfix, 'menu_items' => $aMenu));
        if(!empty($sSelected))
            $oMenu->setSelected('', $sSelected);

        return $oMenu;
    }
    
    protected function _getGetBlockContentSubentries($aData, $bDynamic = false)
    {
        $CNF = &$this->getModule()->_oConfig->CNF;

        $aSubentries = $this->_oDb->getSubentries(array('type' => 'entry_id', 'entry_id' => $aData[$CNF['FIELD_ID']]));
        if(empty($aSubentries) || !is_array($aSubentries))
            return '';

        $aTmplVarsSubentries = array();
        foreach($aSubentries as $aSubentry) {
            $oVotes = BxDolVote::getObjectInstance($CNF['OBJECT_VOTES_SUBENTRIES'], $aSubentry['id']);

            $aTmplVarsSubentries[] = array(
                'subentry' => $oVotes->getElementBlock(array(
                    'dynamic_mode' => $bDynamic
                ))
            );
        }

    	return $this->parseHtmlByName('subentries.html', array(
            'html_id' => $this->_oConfig->getHtmlIds('content') . $aData[$CNF['FIELD_ID']],
            'bx_repeat:subentries' => $aTmplVarsSubentries,
            'bx_if:show_public' => array(
                'condition' => (int)$aData[$CNF['FIELD_ANONYMOUS_VOTING']] == 0,
                'content' => array()
            )
        ));
    }
    
    protected function _getGetBlockContentResults($aData, $bDynamic = false)
    {
        $CNF = &$this->getModule()->_oConfig->CNF;

        $bAnonymous = (int)$aData[$CNF['FIELD_ANONYMOUS_VOTING']] == 1;

        $aSubentries = $this->_oDb->getSubentries(array('type' => 'entry_id', 'entry_id' => $aData[$CNF['FIELD_ID']]));
        if(empty($aSubentries) || !is_array($aSubentries))
            return '';

        $iTotal = 0;
        foreach($aSubentries as $aSubentry)
            $iTotal += $aSubentry['votes'];

        $aTmplVarsSubentries = array();
        foreach($aSubentries as $aSubentry) {
            $oVotes = BxDolVote::getObjectInstance($CNF['OBJECT_VOTES_SUBENTRIES'], $aSubentry['id']);

            $fPercent = $iTotal > 0 ? 100 * (float)$aSubentry['votes']/$iTotal : 0;
            $aTmplVarsSubentries[] = array(
                'title' => bx_process_output($aSubentry['title']),
                'width' => (int)round($fPercent) . '%',
                'votes' => $oVotes->getCounter(array('show_counter_empty' => true, 'show_counter_in_brackets' => false)),
                'percent' => _t('_bx_polls_txt_subentry_vote_percent', $iTotal > 0 ? round($fPercent, 2) : 0),
                'js_code' => $oVotes->getJsScript($bDynamic)
            );
        }

        return $this->parseHtmlByName('subentries_results.html', array(
            'html_id' => $this->_oConfig->getHtmlIds('content') . $aData[$CNF['FIELD_ID']],
            'bx_repeat:subentries' => $aTmplVarsSubentries,
        ));
    }
}

/** @} */
