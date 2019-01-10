<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    Notifications Notifications
 * @ingroup     TridentModules
 *
 * @{
 */

class BxNtfsTemplate extends BxBaseModNotificationsTemplate
{
    function __construct(&$oConfig, &$oDb)
    {
        parent::__construct($oConfig, $oDb);
    }

    public function getViewBlock($aParams)
    {
        return $this->parseHtmlByName('block_view.html', array(
        	'html_id_view_block' => $this->_oConfig->getHtmlIds('view', 'block'),
        	'html_id_events' => $this->_oConfig->getHtmlIds('view', 'events'),
            'style_prefix' => $this->_oConfig->getPrefix('style'),
            'content' => $this->getPosts($aParams),
            'js_content' => $this->getJsCode('view', array(
        		'oRequestParams' => array(
	                'type' => $aParams['type'],
	                'owner_id' => $aParams['owner_id'],
	                'start' => $aParams['start'],
	                'per_page' => $aParams['per_page'],
	                'modules' => $aParams['modules']
       			)
            ))
        ));
    }

    public function getPosts($aParams)
    {
    	$sJsObject = $this->_oConfig->getJsObject('view');

    	$aParamsDb = $aParams;
    	$aParamsDb['per_page'] = $aParamsDb['per_page'] + 1;

        $aEvents = $this->_oDb->getEvents($aParamsDb);
        if(empty($aEvents))
        	return $this->getEmpty();

        $oPaginate = new BxTemplPaginate(array(
        	'start' => $aParams['start'],
            'per_page' => $aParams['per_page'],
        	'page_url' => $this->_oConfig->getViewUrl(),
        	'on_change_page' => $sJsObject . ".changePage(this, {start}, {per_page})"
        ));
        $oPaginate->setNumFromDataArray($aEvents);

        $sEvents = '';
        foreach($aEvents as $aEvent) {
            $sEvent = $this->getPost($aEvent, $aParams);
            if(empty($sEvent))
                continue;

            $sEvents .= $sEvent;
        }

        return $this->parseHtmlByName('events.html', array(
        	'style_prefix' => $this->_oConfig->getPrefix('style'),
        	'events' => $sEvents,
        	'paginate' => $oPaginate->getSimplePaginate()
        ));
    }

    public function getPost(&$aEvent, $aBrowseParams = array())
    {
    	$oModule = $this->getModule();

    	if((int)$aEvent['processed'] == 0) {
    		$aContent = $this->_getContent($aEvent);
    		if(!empty($aContent) && is_array($aContent)) {
    			$aEvent['content'] = serialize($aContent);

    			$this->_oDb->updateEvent(array('content' => $aEvent['content'], 'processed' => 1), array('id' => $aEvent['id']));
    		}
    	}

        list($sOwnerName, $sOwnerUrl, $sOwnerIcon) = $oModule->getUserInfo($aEvent['owner_id']);
        $bAuthorIcon = !empty($sOwnerIcon);

        $aEvent['content'] = unserialize($aEvent['content']);
        $aEvent['content']['owner_name'] = $sOwnerName;
        $aEvent['content']['owner_link'] = $sOwnerUrl;

    	$sContent = _t(!empty($aEvent['content']['lang_key']) ? $aEvent['content']['lang_key'] : $this->_getLangKey($aEvent));
    	$sContent = $this->parseHtmlByContent($sContent, $aEvent['content'], array('{', '}'));

        return $this->parseHtmlByName('event.html', array (
        	'html_id' => $this->_oConfig->getHtmlIds('view', 'event') . $aEvent['id'],
            'style_prefix' => $this->_oConfig->getPrefix('style'),
            'js_object' => $this->_oConfig->getJsObject('view'),
            'id' => $aEvent['id'],
            'bx_if:show_icon' => array(
                'condition' => $bAuthorIcon,
                'content' => array(
                    'author_icon' => $sOwnerIcon
                )
            ),
            'bx_if:show_icon_empty' => array(
                'condition' => !$bAuthorIcon,
                'content' => array()
            ),
            'content' => $sContent,
            'date' => bx_time_js($aEvent['date']),
        ));
    }

    public function getEmpty($bVisible = true)
    {
        return $this->parseHtmlByName('empty.html', array(
            'style_prefix' => $this->_oConfig->getPrefix('style'),
            'visible' => $bVisible ? 'block' : 'none',
            'content' => MsgBox(_t('_bx_ntfs_txt_msg_no_results'))
        ));
    }

    protected function _getContent(&$aEvent)
    {
        $sHandler = $aEvent['type'] . '_' . $aEvent['action'];
        if(!$this->_oConfig->isHandler($sHandler))
            return array();

        $aHandler = $this->_oConfig->getHandlers($sHandler);
        if(!empty($aHandler['module_name']) && !empty($aHandler['module_class']) && !empty($aHandler['module_method']))
        	return BxDolService::call($aHandler['module_name'], $aHandler['module_method'], array($aEvent), $aHandler['module_class']);
        

        $sMethod = 'display' . bx_gen_method_name($aHandler['alert_unit'] . '_' . $aHandler['alert_action']);
		if(!method_exists($this, $sMethod))
        	return array();

		return $this->$sMethod($aEvent);
    }

    protected function _getLangKey(&$aEvent)
    {
    	$sResult = '_bx_ntfs_txt_object_added';

    	if(!empty($aEvent['subobject_id']))
    		$sResult = '_bx_ntfs_txt_subobject_added_' . (!empty($aEvent['content']['subentry_url']) ? 'link' : 'text');

    	return $sResult;
    }
}

/** @} */
