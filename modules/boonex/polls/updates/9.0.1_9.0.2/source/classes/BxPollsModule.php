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

/**
 * Polls module
 */
class BxPollsModule extends BxBaseModTextModule
{
    function __construct(&$aModule)
    {
        parent::__construct($aModule);

        $CNF = &$this->_oConfig->CNF;
        $this->_aSearchableNamesExcept = array_merge($this->_aSearchableNamesExcept, array(
             $CNF['FIELD_ANONYMOUS'],
             $CNF['FIELD_HIDDEN_RESULTS']
        ));
    }

    /**
     * ACTION METHODS
     */
    public function actionGetBlock()
    {
        $iContentId = (int)bx_get('content_id');
        $sBlock = bx_process_input(bx_get('block'));

        $sMethod = 'serviceGetBlock' . bx_gen_method_name($sBlock);
        if(!method_exists($this, $sMethod))
            return echoJson(array());

        $aBlock = $this->$sMethod($iContentId, true);
        if(empty($aBlock) || !is_array($aBlock))
            return echoJson(array());

        return echoJson(array(
        	'content' => $aBlock['content']
        ));
    }


    /**
     * SERVICE METHODS
     */
    public function serviceGetResultsSearchExtended($aParams)
    {
        $this->_oTemplate->addJs(array('entry.js'));
        $this->_oTemplate->addCss(array('entry.css'));
        return $this->_oTemplate->getJsCode('entry') . BxDolService::call('system', 'get_results', array($aParams), 'TemplSearchExtendedServices');
    }

    public function serviceGetBlockSubentries($iContentId = 0, $bForceDisplay = false)
    {
        if (!$iContentId)
            $iContentId = bx_process_input(bx_get('id'), BX_DATA_INT);
        if (!$iContentId)
            return false;

        if(!$bForceDisplay && $this->_oDb->isPerformed($iContentId, bx_get_logged_profile_id()))
            return $this->serviceGetBlockResults($iContentId);

        return $this->_serviceTemplateFunc('entrySubentries', $iContentId);
    }

    public function serviceGetBlockResults($iContentId = 0)
    {
        return $this->_serviceTemplateFunc('entryResults', $iContentId);
    }

    /**
     * PERMISSION METHODS
     */
    public function isAllowedVote($isPerformAction = false)
    {
        $aCheck = checkActionModule($this->_iProfileId, 'vote entry', $this->getName(), false);
        if ($aCheck[CHECK_ACTION_RESULT] !== CHECK_ACTION_RESULT_ALLOWED)
            return $aCheck[CHECK_ACTION_MESSAGE];

        return CHECK_ACTION_RESULT_ALLOWED;
    }


	/**
     * INTERNAL METHODS
     */
    protected function _getImagesForTimelinePost($aEvent, $aContentInfo, $sUrl, $aBrowseParams = array())
    {
        return array();
    }

    protected function _getContentForTimelinePost($aEvent, $aContentInfo, $aBrowseParams = array())
    {
        $CNF = &$this->_oConfig->CNF;
        $bDynamic = isset($aBrowseParams['dynamic_mode']) && $aBrowseParams['dynamic_mode'] === true;

        $sInclude = '';
        $sInclude .= $this->_oTemplate->addJs(array('entry.js'), $bDynamic);
        $sInclude .= $this->_oTemplate->addCss(array('entry.css'), $bDynamic);

        $aBlock = $this->_oTemplate->{$this->_oDb->isPerformed($aContentInfo[$CNF['FIELD_ID']], bx_get_logged_profile_id()) ? 'entryResults' : 'entrySubentries'}($aContentInfo, $bDynamic);

        $aResult = parent::_getContentForTimelinePost($aEvent, $aContentInfo, $aBrowseParams);
        $aResult['title'] = $this->_oConfig->getTitle($aContentInfo);
        $aResult['text'] = '';
        $aResult['raw'] = ($bDynamic ? $sInclude : '') . $this->_oTemplate->getJsCode('entry') . $aBlock['content'];

        return $aResult;
    }
}

/** @} */
