<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Timeline Timeline
 * @ingroup     UnaModules
 *
 * @{
 */

bx_import('BxDolAcl');
bx_import('BxBaseModNotificationsModule');

define('BX_TIMELINE_TYPE_ITEM', 'view_item');
define('BX_TIMELINE_TYPE_OWNER_AND_CONNECTIONS', 'owner_and_connections');
define('BX_TIMELINE_TYPE_HOT', 'hot');
define('BX_TIMELINE_TYPE_DEFAULT', BX_BASE_MOD_NTFS_TYPE_OWNER);

define('BX_TIMELINE_VIEW_ITEM', 'item');
define('BX_TIMELINE_VIEW_TIMELINE', 'timeline');
define('BX_TIMELINE_VIEW_OUTLINE', 'outline');
define('BX_TIMELINE_VIEW_SEARCH', 'search');
define('BX_TIMELINE_VIEW_DEFAULT', BX_TIMELINE_VIEW_OUTLINE);

define('BX_TIMELINE_FILTER_ALL', 'all');
define('BX_TIMELINE_FILTER_OWNER', 'owner');
define('BX_TIMELINE_FILTER_OTHER', 'other');
define('BX_TIMELINE_FILTER_OTHER_VIEWER', 'other_viewer');

define('BX_TIMELINE_PARSE_TYPE_POST', 'post');
define('BX_TIMELINE_PARSE_TYPE_REPOST', 'repost');
define('BX_TIMELINE_PARSE_TYPE_DEFAULT', BX_TIMELINE_PARSE_TYPE_POST);

define('BX_TIMELINE_MEDIA_PHOTO', 'photo');
define('BX_TIMELINE_MEDIA_VIDEO', 'video');

//--- Video Auto Play 
define('BX_TIMELINE_VAP_OFF', 'off');
define('BX_TIMELINE_VAP_ON_MUTE', 'on_mute');
define('BX_TIMELINE_VAP_ON', 'on');

class BxTimelineModule extends BxBaseModNotificationsModule implements iBxDolContentInfoService
{
    protected $_sJsPostObject;
    protected $_sJsViewObject;
    protected $_aPostElements;
    protected $_sJsOutlineObject;

    protected $_sDividerTemplate;
    protected $_sBalloonTemplate;
    protected $_sCmtPostTemplate;
    protected $_sCmtViewTemplate;
    protected $_sCmtTemplate;

    /**
     * Constructor
     */
    function __construct($aModule)
    {
        parent::__construct($aModule);
    }

    /**
     * ACTION METHODS
     */
    public function actionVideo($iEventId, $iVideoId)
    {
        $aEvent = $this->_oDb->getEvents(array('browse' => 'id', 'value' => $iEventId));
        if(empty($aEvent) || !is_array($aEvent))
            return;

        $aData = $this->_oTemplate->getData($aEvent);
        if($aData === false || !isset($aData['content']['videos'][$iVideoId]))
            return;

        $oTemplate = $this->_oTemplate;
        $oTemplate->setPageNameIndex (BX_PAGE_CLEAR);
        $oTemplate->setPageContent ('page_main_code', $this->_oTemplate->getVideo($aEvent, $aData['content']['videos'][$iVideoId]));
        $oTemplate->getPageCode();
    }

    public function actionPost()
    {
        $this->_iOwnerId = bx_process_input(bx_get('owner_id'), BX_DATA_INT);

        $mixedAllowed = $this->isAllowedPost(true);
        if($mixedAllowed !== true)
            return echoJson(array('message' => strip_tags($mixedAllowed)));

        echoJson($this->getFormPost());
    }

    public function actionEdit($iId)
    {
        $this->_iOwnerId = bx_process_input(bx_get('owner_id'), BX_DATA_INT);

        $mixedAllowed = $this->isAllowedPost(true);
        if($mixedAllowed !== true)
            return echoJson(array('message' => strip_tags($mixedAllowed)));

        echoJson($this->getFormEdit($iId, array('dynamic_mode' => true)));
    }

	function actionPin()
    {
        $this->_iOwnerId = bx_process_input(bx_get('owner_id'), BX_DATA_INT);

        $iId = bx_process_input(bx_get('id'), BX_DATA_INT);
        $aEvent = $this->_oDb->getEvents(array('browse' => 'id', 'value' => $iId));

        $mixedAllowed = $this->{'isAllowed' . ((int)$aEvent['pinned'] == 0 ? 'Pin' : 'Unpin')}($aEvent, true);
        if($mixedAllowed !== true)
            return echoJson(array('code' => 1, 'message' => strip_tags($mixedAllowed)));

		$aEvent['pinned'] = (int)$aEvent['pinned'] == 0 ? time() : 0;
        if(!$this->_oDb->updateEvent(array('pinned' => $aEvent['pinned']), array('id' => $iId)))
        	return echoJson(array('code' => 2));

		echoJson(array(
			'code' => 0, 
			'id' => $iId, 
			'eval' => $this->_oConfig->getJsObject('view') . '.onPinPost(oData)'
		));
    }

    function actionStick()
    {
    	$this->_iOwnerId = bx_process_input(bx_get('owner_id'), BX_DATA_INT);

    	$iId = bx_process_input(bx_get('id'), BX_DATA_INT);
    	$aEvent = $this->_oDb->getEvents(array('browse' => 'id', 'value' => $iId));

    	$mixedAllowed = $this->{'isAllowed' . ((int)$aEvent['sticked'] == 0 ? 'Stick' : 'Unstick')}($aEvent, true);
    	if($mixedAllowed !== true)
    		return echoJson(array('code' => 1, 'message' => strip_tags($mixedAllowed)));

    	$aEvent['sticked'] = (int)$aEvent['sticked'] == 0 ? time() : 0;
    	if(!$this->_oDb->updateEvent(array('sticked' => $aEvent['sticked']), array('id' => $iId)))
    		return echoJson(array('code' => 2));

    	echoJson(array(
	    	'code' => 0,
	    	'id' => $iId,
	    	'eval' => $this->_oConfig->getJsObject('view') . '.onStickPost(oData)'
		));
    }

    function actionPromote()
    {
        $this->_iOwnerId = bx_process_input(bx_get('owner_id'), BX_DATA_INT);

        $iId = bx_process_input(bx_get('id'), BX_DATA_INT);
        $aEvent = $this->_oDb->getEvents(array('browse' => 'id', 'value' => $iId));

        $mixedAllowed = $this->{'isAllowed' . ((int)$aEvent['promoted'] == 0 ? 'Promote' : 'Unpromote')}($aEvent, true);
        if($mixedAllowed !== true)
            return echoJson(array('code' => 1, 'message' => strip_tags($mixedAllowed)));

		$aEvent['promoted'] = (int)$aEvent['promoted'] == 0 ? time() : 0;
        if(!$this->_oDb->updateEvent(array('promoted' => $aEvent['promoted']), array('id' => $iId)))
        	return echoJson(array('code' => 2));

		echoJson(array(
			'code' => 0, 
			'message' => _t('_bx_timeline_txt_msg_performed_action')
		));
    }

    function actionDelete()
    {
        $this->_iOwnerId = bx_process_input(bx_get('owner_id'), BX_DATA_INT);

        $aEvent = $this->_oDb->getEvents(array('browse' => 'id', 'value' => bx_process_input(bx_get('id'), BX_DATA_INT)));

        $mixedAllowed = $this->isAllowedDelete($aEvent, true);
        if($mixedAllowed !== true)
            return echoJson(array('code' => 1, 'message' => strip_tags($mixedAllowed)));

        if(!$this->deleteEvent($aEvent))
            return echoJson(array('code' => 2));

        echoJson(array(
        	'code' => 0, 
        	'id' => $aEvent['id'], 
        	'eval' => $this->_oConfig->getJsObject('view') . '.onDeletePost(oData)'
        ));
    }

    public function actionRepost()
    {
    	$iAuthorId = $this->getUserId();

        $iOwnerId = bx_process_input(bx_get('owner_id'), BX_DATA_INT);
        $aContent = array(
            'type' => bx_process_input(bx_get('type'), BX_DATA_TEXT),
            'action' => bx_process_input(bx_get('action'), BX_DATA_TEXT),
            'object_id' => bx_process_input(bx_get('object_id'), BX_DATA_INT),
        );

        $aReposted = $this->_oDb->getReposted($aContent['type'], $aContent['action'], $aContent['object_id']);
        if(empty($aReposted) || !is_array($aReposted)) {
            echoJson(array('code' => 1, 'message' => _t('_bx_timeline_txt_err_cannot_repost')));
            return;
        }

        $mixedAllowed = $this->isAllowedRepost($aReposted, true);
        if($mixedAllowed !== true) {
            echoJson(array('code' => 2, 'message' => strip_tags($mixedAllowed)));
            return;
        }

        $bReposted = $this->_oDb->isReposted($aReposted['id'], $iOwnerId, $iAuthorId);
		if($bReposted) {
        	echoJson(array('code' => 3, 'message' => _t('_bx_timeline_txt_err_already_reposted')));
            return;
        }

        $iId = $this->_oDb->insertEvent(array(
            'owner_id' => $iOwnerId,
            'type' => $this->_oConfig->getPrefix('common_post') . 'repost',
            'action' => '',
            'object_id' => $iAuthorId,
            'object_privacy_view' => $this->_oConfig->getPrivacyViewDefault('object'),
            'content' => serialize($aContent),
            'title' => '',
            'description' => ''
        ));

        if(empty($iId)) {
            echoJson(array('code' => 4, 'message' => _t('_bx_timeline_txt_err_cannot_repost')));        
            return;
        }

        $this->onRepost($iId, $aReposted);

        $aReposted = $this->_oDb->getReposted($aContent['type'], $aContent['action'], $aContent['object_id']);
        $sCounter = $this->_oTemplate->getRepostCounter($aReposted);

        echoJson(array(
            'code' => 0, 
            'message' => _t('_bx_timeline_txt_msg_success_repost'), 
            'count' => $aReposted['reposts'], 
            'countf' => (int)$aReposted['reposts'] > 0 ? $this->_oTemplate->getRepostCounterLabel($aReposted['reposts']) : '',
            'counter' => $sCounter,
            'disabled' => !$bReposted
        ));
    }

    function actionGetPost()
    {
        $this->_iOwnerId = bx_process_input(bx_get('owner_id'), BX_DATA_INT);

        $sJsObject = bx_process_input(bx_get('js_object'));
        if(empty($sJsObject))
            $sJsObject = $this->_oConfig->getJsObject('post');

        $sView = bx_process_input(bx_get('view'));
        $sType = bx_process_input(bx_get('type'));
        $iId = bx_process_input(bx_get('id'), BX_DATA_INT);

        $bAfpsLoading = (int)bx_get('afps_loading') === 1;

        $aEvent = $this->_oDb->getEvents(array('browse' => 'id', 'value' => $iId));
        if(empty($aEvent) || !is_array($aEvent))
            return echoJson(array());

        /**
         * Note. Disabled for now, because Own posts on Timelines of Following members 
         * became visible on posts' author Dashboard Timeline.
         */
        //if($bAfpsLoading && $this->_iOwnerId != $aEvent['owner_id'])
        //    return echoJson(array('message' => _t('_bx_timeline_txt_msg_posted')));
            
        echoJson(array(
            'id' => $aEvent['id'],
            'view' => $sView,
        	'item' => $this->_oTemplate->getPost($aEvent, array(
        		'view' => $sView, 
        		'type' => !empty($sType) ? $sType : BX_TIMELINE_TYPE_DEFAULT,
        		'owner_id' => $this->_iOwnerId, 
        		'dynamic_mode' => true
            )),
            'eval' => $sJsObject . "._onGetPost(oData)"
        ));
    }

    function actionGetPosts()
    {
        $aParams = $this->_prepareParamsGet();
        list($sItems, $sLoadMore, $sBack, $sEmpty) = $this->_oTemplate->getPosts($aParams);

        echoJson(array(
        	'view' => $aParams['view'],
        	'items' => $sItems, 
        	'load_more' => $sLoadMore, 
        	'back' => $sBack,
            'empty' => $sEmpty,
        	'eval' => $this->_oConfig->getJsObject('view') . "._onGetPosts(oData)"
        ));
    }

    public function actionGetPostForm()
    {
        $this->_iOwnerId = bx_process_input(bx_get('owner_id'), BX_DATA_INT);
        $sType = bx_process_input(bx_get('type'));

        echoJson($this->getFormPost(array(
            'type' => $sType,
            'form_display' => $this->_oConfig->getPostFormDisplay($sType)
        )));
    }

    public function actionGetEditForm($iId)
    {
        $this->_iOwnerId = bx_process_input(bx_get('owner_id'), BX_DATA_INT);

        echoJson($this->getFormEdit($iId, array('dynamic_mode' => true)));
    }

    public function actionGetComments()
    {
        $this->_iOwnerId = bx_process_input(bx_get('owner_id'), BX_DATA_INT);

        $sSystem = bx_process_input(bx_get('system'), BX_DATA_TEXT);
        $iId = bx_process_input(bx_get('id'), BX_DATA_INT);
        $sComments = $this->_oTemplate->getComments($sSystem, $iId, array('dynamic_mode' => true));

        echoJson(array('content' => $sComments));
    }

    public function actionAddAttachLink()
    {
        $sUrl = bx_process_input(bx_get('url'));
        if(empty($sUrl))
            return echoJson(array());

        echoJson($this->addAttachLink(array(
            'url' => $sUrl
        )));
    }

    public function actionDeleteAttachLink()
    {
    	$iUserId = $this->getUserId();
        $iLinkId = bx_process_input(bx_get('id'), BX_DATA_INT);
        if(empty($iLinkId)) {
            echoJson(array());
            return;
        }

        $aLink = $this->_oDb->getUnusedLinks($iUserId, $iLinkId);
    	if(empty($aLink) || !is_array($aLink)) {
            echoJson(array());
            return;
        }

		if(!empty($aLink['media_id']))
			BxDolStorage::getObjectInstance($this->_oConfig->getObject('storage_photos'))->deleteFile($aLink['media_id']);

        $aResult = array();
        if($this->_oDb->deleteUnusedLinks($iUserId, $iLinkId))
            $aResult = array('code' => 0);
        else
            $aResult = array('code' => 1, 'message' => _t('_bx_timeline_form_post_input_link_err_delete'));

        echoJson($aResult);
    }

    public function actionGetAttachLinkForm()
    {
        echo $this->_oTemplate->getAttachLinkForm();
    }

    public function actionSubmitAttachLinkForm()
    {
        echoJson($this->getFormAttachLink());
    }

    public function actionGetRepostedBy()
    {
        $iRepostedId = bx_process_input(bx_get('id'), BX_DATA_INT);

        echo $this->_oTemplate->getRepostedBy($iRepostedId);
    }

    public function actionGetItemBrief()
    {
        echo BxDolPage::getObjectInstance($this->_oConfig->getObject('page_item_brief'), $this->_oTemplate)->getCode();
    }

    public function actionResumeLiveUpdate($sType, $iOwnerId)
    {
    	$sKey = $this->_oConfig->getLiveUpdateKey($sType, $iOwnerId);

    	bx_import('BxDolSession');
    	BxDolSession::getInstance()->unsetValue($sKey, $iOwnerId);
    }

	public function actionPauseLiveUpdate($sType, $iOwnerId)
    {
    	$sKey = $this->_oConfig->getLiveUpdateKey($sType, $iOwnerId);

    	bx_import('BxDolSession');
    	BxDolSession::getInstance()->setValue($sKey, 1);
    }

    function actionRss()
    {
        $aArgs = func_get_args();

        $sType = array_shift($aArgs);
        $iOwnerId = 0;

        switch($sType) {
            case BX_BASE_MOD_NTFS_TYPE_OWNER:
                $iOwnerId = array_shift($aArgs);
                list($sUserName) = $this->getUserInfo($iOwnerId);

                $sRssCaption = _t('_bx_timeline_txt_rss_caption', $sUserName);
                $sRssLink = $this->_oConfig->getViewUrl($iOwnerId);
                break;

            case BX_BASE_MOD_NTFS_TYPE_PUBLIC:
                $sRssCaption = _t('_bx_timeline_page_title_view_home');
                $sRssLink = $this->_oConfig->getHomeViewUrl();
                break;
        }
        

        $aParams = $this->_prepareParams(BX_TIMELINE_VIEW_DEFAULT, $sType, $iOwnerId, 0, $this->_oConfig->getRssLength(), '', array(), 0);
        $aEvents = $this->_oDb->getEvents($aParams);

        $aRssData = array();
        foreach($aEvents as $aEvent) {
            if(empty($aEvent['title'])) continue;

            $aRssData[$aEvent['id']] = array(
               'UnitID' => $aEvent['id'],
               'UnitTitle' => $aEvent['title'],
               'UnitLink' => $this->_oConfig->getItemViewUrl($aEvent),
               'UnitDesc' => $aEvent['description'],
               'UnitDateTimeUTS' => $aEvent['date'],
            );
        }

        $oRss = new BxDolRssFactory();

        header('Content-Type: application/xml; charset=utf-8');
        echo $oRss->GenRssByData($aRssData, $sRssCaption, $sRssLink);
    }


    /**
     * SERVICE METHODS
     */

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-other Other
     * @subsubsection bx_timeline-get_create_post_form get_create_post_form
     * 
     * @code bx_srv('bx_timeline', 'get_create_post_form', [...]); @endcode
     * 
     * Get form code for add content. Is needed for United Post Form.
     * 
     * @param $aParams optional array with parameters(display name, etc)
     * @return form code or error
     * 
     * @see BxTimelineModule::serviceGetCreatePostForm
     */
    /** 
     * @ref bx_timeline-get_create_post_form "get_create_post_form"
     */
    public function serviceGetCreatePostForm($aParams = array())
    {
    	$aParams = array_merge($this->_aFormParams, $aParams);

        $this->_iOwnerId = $aParams['context_id'];

    	$oForm = $this->serviceGetObjectForm('add', $aParams);
    	if(!$oForm)
            return '';

        if(!empty($aParams['display']))
            $aParams['form_display'] = $aParams['display'];
        else if(isset($aParams['context_id'])) {
            if((int)$aParams['context_id'] == 0)
                $aParams['form_display'] = 'form_display_post_add_public';
            else
                $aParams['form_display'] = 'form_display_post_add_profile';
        }

    	$aResult = $this->getFormPost($aParams);
    	if(!empty($aResult['form'])) {
            $bDynamicMode = isset($aParams['dynamic_mode']) && $aParams['dynamic_mode'];

            $sCode = '';
            $sCode .= $this->_oTemplate->getCssJs($bDynamicMode);
            $sCode .= $this->_oTemplate->getJsCodePost($this->_iOwnerId);
            $sCode .= $aResult['form'];
            return $sCode;
        }

        if(!empty($aResult['message']))
                return $aResult['message'];

        return '';
    }

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-other Other
     * @subsubsection bx_timeline-get_object_form get_object_form
     * 
     * @code bx_srv('bx_timeline', 'get_object_form', [...]); @endcode
     * 
     * Get form object for add, edit, view or delete the content.
     * 
     * @param $sType 'add' is supported only 
     * @param $aParams optional array with parameters(display name, etc)
     * @return form object or false on error
     * 
     * @see BxTimelineModule::serviceGetObjectForm
     */
    /** 
     * @ref bx_timeline-get_object_form "get_object_form"
     */
    public function serviceGetObjectForm ($sType, $aParams = array())
    {
    	if (!in_array($sType, array('add')))
            return false;

        $CNF = &$this->_oConfig->CNF;

        if(!empty($aParams['display']))
            $aParams['form_display'] = $aParams['display'];
        else if(isset($aParams['context_id'])) {
            if((int)$aParams['context_id'] == 0)
                $aParams['form_display'] = 'form_display_post_add_public';
            else
                $aParams['form_display'] = 'form_display_post_add_profile';
        }

    	return $this->getFormPostObject($aParams);
    }
    
    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection get_timeline_post
     * @see BxTimelineModule::serviceGetTimelinePost
     *
     * Get Timeline post. It's needed for Timeline module.
     * 
     * @param $aEvent timeline event array from Timeline module
     * @return array in special format which is needed specifically for Timeline module to display the data.
     */
    public function serviceGetTimelinePost($aEvent, $aBrowseParams = array())
    {
        $CNF = &$this->_oConfig->CNF;

        /*
         * Note. For 'Direct Timeline Posts' FIELD_OBJECT_ID contains post's author profile ID.
         */
        $CNF['FIELD_AUTHOR'] = $CNF['FIELD_OBJECT_ID'];
        return parent::serviceGetTimelinePost($aEvent, $aBrowseParams);
    }

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-other Other
     * @subsubsection bx_timeline-get_author get_author
     * 
     * @code bx_srv('bx_timeline', 'get_author', [...]); @endcode
     * 
     * Get author ID from content info by content ID. Is used in "Content Info Objects" system.
     * 
     * @param $iContentId integer value with content ID.
     * @return integer value with author ID.
     * 
     * @see BxTimelineModule::serviceGetAuthor
     */
    /** 
     * @ref bx_timeline-get_author "get_author"
     */
    public function serviceGetAuthor ($iContentId)
    {
        $aEvent = $this->_oDb->getEvents(array('browse' => 'id', 'value' => $iContentId));
        if(empty($aEvent) || !is_array($aEvent))
            return 0;

        return $this->_oConfig->isSystem($aEvent['type'], $aEvent['action']) ? (int)$aEvent['owner_id'] : (int)$aEvent['object_id'];
    }

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-other Other
     * @subsubsection bx_timeline-get_date_changed get_date_changed
     * 
     * @code bx_srv('bx_timeline', 'get_date_changed', [...]); @endcode
     * 
     * Get date when the content was changed last time. Is used in "Content Info Objects" system.
     * Note. In case of Timeline event 0 is returned everytime.
     * 
     * @param $iContentId integer value with content ID.
     * @return integer value with changing date.
     * 
     * @see BxTimelineModule::serviceGetDateChanged
     */
    /** 
     * @ref bx_timeline-get_date_changed "get_date_changed"
     */
    public function serviceGetDateChanged ($iContentId)
    {
        return 0;
    }

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-other Other
     * @subsubsection bx_timeline-get_link get_link
     * 
     * @code bx_srv('bx_timeline', 'get_link', [...]); @endcode
     * 
     * Get content view page link. Is used in "Content Info Objects" system.
     * 
     * @param $iContentId integer value with content ID.
     * @return string value with view page link.
     * 
     * @see BxTimelineModule::serviceGetLink
     */
    /** 
     * @ref bx_timeline-get_link "get_link"
     */
    public function serviceGetLink ($iContentId)
    {
        $aEvent = $this->_oDb->getEvents(array('browse' => 'id', 'value' => $iContentId));
        if(empty($aEvent) || !is_array($aEvent))
            return '';

        return $this->_oConfig->getItemViewUrl($aEvent);
    }

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-other Other
     * @subsubsection bx_timeline-get_thumb get_thumb
     * 
     * @code bx_srv('bx_timeline', 'get_thumb', [...]); @endcode
     * 
     * Get content thumbnail link. Is used in "Content Info Objects" system.
     * Note. In case of Timeline event an empty string is returned everytime.
     * 
     * @param $iContentId integer value with content ID.
     * @param $sTranscoder (optional) string value with transcoder name which should be applied to thumbnail image.
     * @return string value with thumbnail link.
     * 
     * @see BxTimelineModule::serviceGetThumb
     */
    /** 
     * @ref bx_timeline-get_thumb "get_thumb"
     */
    public function serviceGetThumb ($iContentId, $sTranscoder = '') 
    {
        return '';
    }

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-other Other
     * @subsubsection bx_timeline-get_info get_info
     * 
     * @code bx_srv('bx_timeline', 'get_info', [...]); @endcode
     * 
     * Get content info by content ID. Is used in "Content Info Objects" system.
     * 
     * @param $iContentId integer value with content ID.
     * @param $bSearchableFieldsOnly (optional) boolean value determining all info or "searchable fields" only will be returned.
     * @return an array with content info. Empty array is returned if something is wrong.
     * 
     * @see BxTimelineModule::serviceGetInfo
     */
    /** 
     * @ref bx_timeline-get_info "get_info"
     */
    public function serviceGetInfo ($iContentId, $bSearchableFieldsOnly = true)
    {
        $aContentInfo = $this->_oDb->getEvents(array(
        	'browse' => 'id', 
        	'value' => $iContentId)
        );

        return BxDolContentInfo::formatFields($aContentInfo);
    }

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-other Other
     * @subsubsection bx_timeline-get_search_result_unit get_search_result_unit
     * 
     * @code bx_srv('bx_timeline', 'get_search_result_unit', [...]); @endcode
     * 
     * Get search result unit by content ID. Is used in "Content Info Objects" system.
     * 
     * @param $iContentId integer value with content ID.
     * @param $sUnitTemplate (optional) string value with template name.
     * @return HTML string with search result unit. Empty string is returned if something is wrong.
     * 
     * @see BxTimelineModule::serviceGetSearchResultUnit
     */
    /** 
     * @ref bx_timeline-get_search_result_unit "get_search_result_unit"
     */
    public function serviceGetSearchResultUnit ($iContentId, $sUnitTemplate = '')
    {
        $aEvent = $this->_oDb->getEvents(array('browse' => 'id', 'value' => $iContentId));
        if(empty($aEvent) || !is_array($aEvent))
            return '';

        if(empty($sUnitTemplate))
            $sUnitTemplate = 'unit.html';

        return $this->_oTemplate->unit($aEvent, true, $sUnitTemplate);
    }

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-page_blocks Page Blocks
     * @subsubsection bx_timeline-get_block_post get_block_post
     * 
     * @code bx_srv('bx_timeline', 'get_block_post', [...]); @endcode
     * 
     * Get Post block for a separate page.
     *
     * @param $iProfileId (optional) profile ID. If empty value is provided, an attempt to get it from GET/POST arrays will be performed.
     * @return an array describing a block to display on the site. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxTimelineModule::serviceGetBlockPost
     */
    /** 
     * @ref bx_timeline-get_block_post "get_block_post"
     */
    public function serviceGetBlockPost($iProfileId = 0)
    {
    	if(empty($iProfileId) && bx_get('profile_id') !== false)
			$iProfileId = bx_process_input(bx_get('profile_id'), BX_DATA_INT);

		if(empty($iProfileId) && isLogged())
			$iProfileId = bx_get_logged_profile_id();

        if(!$iProfileId)
            return array();

        $sType = BX_BASE_MOD_NTFS_TYPE_OWNER;
        return $this->_getBlockPost($iProfileId, array(
        	'type' => $sType,
        	'form_display' => $this->_oConfig->getPostFormDisplay($sType)
        ));
    }

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-page_blocks Page Blocks
     * @subsubsection bx_timeline-get_block_post_profile get_block_post_profile
     * 
     * @code bx_srv('bx_timeline', 'get_block_post_profile', [...]); @endcode
     * 
     * Get Post block for the Profile page.
     *
     * @param $sProfileModule (optional) string value with profile based module name. Persons module is used by default.
     * @param $iProfileContentId (optional) profile's content ID. If empty value is provided, an attempt to get it from GET/POST arrays will be performed.
     * @return an array describing a block to display on the site. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxTimelineModule::serviceGetBlockPostProfile
     */
    /** 
     * @ref bx_timeline-get_block_post_profile "get_block_post_profile"
     */
    public function serviceGetBlockPostProfile($sProfileModule = 'bx_persons', $iProfileContentId = 0)
    {
        if(empty($sProfileModule))
    		return array();

    	if(empty($iProfileContentId) && bx_get('id') !== false)
    		$iProfileContentId = bx_process_input(bx_get('id'), BX_DATA_INT);

		$oProfile = BxDolProfile::getInstanceByContentAndType($iProfileContentId, $sProfileModule);
		if(empty($oProfile))
			return array();

        $iProfileId = $oProfile->id();
        $sType = BX_BASE_MOD_NTFS_TYPE_OWNER;
		return $this->_getBlockPost($iProfileId, array(
		    'type' => $sType,
			'form_display' => $this->_oConfig->getPostFormDisplay($sType)
		));
    }

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-page_blocks Page Blocks
     * @subsubsection bx_timeline-get_block_post_home get_block_post_home
     * 
     * @code bx_srv('bx_timeline', 'get_block_post_home', [...]); @endcode
     * 
     * Get Post block for site's Home page.
     *
     * @return an array describing a block to display on the site. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxTimelineModule::serviceGetBlockPostHome
     */
    /** 
     * @ref bx_timeline-get_block_post_home "get_block_post_home"
     */
    public function serviceGetBlockPostHome()
    {
        $iProfileId = 0;
        $sType = BX_BASE_MOD_NTFS_TYPE_PUBLIC;
        return $this->_getBlockPost($iProfileId, array(
        	'type' => $sType,
            'form_display' => $this->_oConfig->getPostFormDisplay($sType)
        ));
    }

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-page_blocks Page Blocks
     * @subsubsection bx_timeline-get_block_post_account get_block_post_account
     * 
     * @code bx_srv('bx_timeline', 'get_block_post_account', [...]); @endcode
     * 
     * Get Post block for the Dashboard page.
     *
     * @return an array describing a block to display on the site. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxTimelineModule::serviceGetBlockPostAccount
     */
    /** 
     * @ref bx_timeline-get_block_post_account "get_block_post_account"
     */
    public function serviceGetBlockPostAccount()
    {
        if(!isLogged())
            return '';

        $iProfileId = $this->getProfileId();
        $sType = BX_TIMELINE_TYPE_OWNER_AND_CONNECTIONS;
		return $this->_getBlockPost($iProfileId, array(
		    'type' => $sType,
			'form_display' => $this->_oConfig->getPostFormDisplay($sType)
		));
    }

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-page_blocks Page Blocks
     * @subsubsection bx_timeline-get_block_view get_block_view
     * 
     * @code bx_srv('bx_timeline', 'get_block_view', [...]); @endcode
     * 
     * Get Timeline View block for a separate page.
     *
     * @param $iProfileId (optional) profile ID. If empty value is provided, an attempt to get it from GET/POST arrays will be performed.
     * @return an array describing a block to display on the site. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxTimelineModule::serviceGetBlockView
     */
    /** 
     * @ref bx_timeline-get_block_view "get_block_view"
     */
    public function serviceGetBlockView($iProfileId = 0)
    {
    	return $this->_serviceGetBlockView($iProfileId, BX_TIMELINE_VIEW_TIMELINE);
    }

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-page_blocks Page Blocks
     * @subsubsection bx_timeline-get_block_view_outline get_block_view_outline
     * 
     * @code bx_srv('bx_timeline', 'get_block_view_outline', [...]); @endcode
     * 
     * Get Outline View block for a separate page.
     * 
     * @param $iProfileId (optional) profile ID. If empty value is provided, an attempt to get it from GET/POST arrays will be performed.
     * @return an array describing a block to display on the site. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxTimelineModule::serviceGetBlockViewOutline
     */
    /** 
     * @ref bx_timeline-get_block_view_outline "get_block_view_outline"
     */
    public function serviceGetBlockViewOutline($iProfileId = 0)
    {
        return $this->_serviceGetBlockView($iProfileId, BX_TIMELINE_VIEW_OUTLINE);
    }

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-page_blocks Page Blocks
     * @subsubsection bx_timeline-get_block_view_profile get_block_view_profile
     * 
     * @code bx_srv('bx_timeline', 'get_block_view_profile', [...]); @endcode
     * 
     * Get Timeline View block for the Profile page.
     * 
     * @param $sProfileModule (optional) string value with profile based module name. Persons module is used by default.
     * @param $iProfileContentId (optional) profile's content ID. If empty value is provided, an attempt to get it from GET/POST arrays will be performed.
     * @param $iStart (optional) integer value with a page number. Is used in pagination.
     * @param $iPerPage (optional) integer value with a number of items per page. Is used in pagination. 
     * @param $sFilter (optional) string value with filter name.
     * @param $aModules (optional) an array of modules from which the events should be displayed. All available modules are used by default.
     * @param $iTimeline (optional) integer value determining whether the timeline should be displayed or not.
     * @return an array describing a block to display on the site. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxTimelineModule::serviceGetBlockViewProfile
     */
    /** 
     * @ref bx_timeline-get_block_view_profile "get_block_view_profile"
     */
    public function serviceGetBlockViewProfile($sProfileModule = 'bx_persons', $iProfileContentId = 0, $iStart = -1, $iPerPage = -1, $sFilter = '', $aModules = array(), $iTimeline = -1)
    {
        $sView = BX_TIMELINE_VIEW_TIMELINE;

        return $this->_serviceGetBlockViewProfile($sProfileModule, $iProfileContentId, $sView, $iStart, $iPerPage, $sFilter, $aModules, $iTimeline);
    }

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-page_blocks Page Blocks
     * @subsubsection bx_timeline-get_block_view_profile_outline get_block_view_profile_outline
     * 
     * @code bx_srv('bx_timeline', 'get_block_view_profile_outline', [...]); @endcode
     * 
     * Get Outline View block for the Profile page.
     * 
     * @param $sProfileModule (optional) string value with profile based module name. Persons module is used by default.
     * @param $iProfileContentId (optional) profile's content ID. If empty value is provided, an attempt to get it from GET/POST arrays will be performed.
     * @param $iStart (optional) integer value with a page number. Is used in pagination.
     * @param $iPerPage (optional) integer value with a number of items per page. Is used in pagination. 
     * @param $sFilter (optional) string value with filter name.
     * @param $aModules (optional) an array of modules from which the events should be displayed. All available modules are used by default.
     * @param $iTimeline (optional) integer value determining whether the timeline should be displayed or not.
     * @return an array describing a block to display on the site. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxTimelineModule::serviceGetBlockViewProfileOutline
     */
    /** 
     * @ref bx_timeline-get_block_view_profile_outline "get_block_view_profile_outline"
     */
	public function serviceGetBlockViewProfileOutline($sProfileModule = 'bx_persons', $iProfileContentId = 0, $iStart = -1, $iPerPage = -1, $sFilter = '', $aModules = array(), $iTimeline = -1)
    {
        $sView = BX_TIMELINE_VIEW_OUTLINE;

        return $this->_serviceGetBlockViewProfile($sProfileModule, $iProfileContentId, $sView, $iStart, $iPerPage, $sFilter, $aModules, $iTimeline);
    }

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-page_blocks Page Blocks
     * @subsubsection bx_timeline-get_block_view_home get_block_view_home
     * 
     * @code bx_srv('bx_timeline', 'get_block_view_home', [...]); @endcode
     * 
     * Get Timeline View block for site's Home page.
     * 
     * @param $iProfileId (optional) profile ID. 0 should be used here.
     * @param $iStart (optional) integer value with a page number. Is used in pagination.
     * @param $iPerPage (optional) integer value with a number of items per page. Is used in pagination.
     * @param $iTimeline (optional) integer value determining whether the timeline should be displayed or not. 
     * @param $sFilter (optional) string value with filter name.
     * @param $aModules (optional) an array of modules from which the events should be displayed. All available modules are used by default.
     * @return an array describing a block to display on the site. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxTimelineModule::serviceGetBlockViewHome
     */
    /** 
     * @ref bx_timeline-get_block_view_home "get_block_view_home"
     */
    public function serviceGetBlockViewHome($iProfileId = 0, $iStart = -1, $iPerPage = -1, $iTimeline = -1, $sFilter = '', $aModules = array())
    {
        return $this->_serviceGetBlockViewHome($iProfileId, BX_TIMELINE_VIEW_TIMELINE, $iStart, $iPerPage, $this->_oConfig->getPerPage('home'), $iTimeline, $sFilter, $aModules);
    }

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-page_blocks Page Blocks
     * @subsubsection bx_timeline-get_block_view_home_outline get_block_view_home_outline
     * 
     * @code bx_srv('bx_timeline', 'get_block_view_home_outline', [...]); @endcode
     * 
     * Get Outline View block for site's Home page.
     * 
     * @param $iProfileId (optional) profile ID. 0 should be used here.
     * @param $iStart (optional) integer value with a page number. Is used in pagination.
     * @param $iPerPage (optional) integer value with a number of items per page. Is used in pagination.
     * @param $iTimeline (optional) integer value determining whether the timeline should be displayed or not. 
     * @param $sFilter (optional) string value with filter name.
     * @param $aModules (optional) an array of modules from which the events should be displayed. All available modules are used by default.
     * @return an array describing a block to display on the site. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxTimelineModule::serviceGetBlockViewHomeOutline
     */
    /** 
     * @ref bx_timeline-get_block_view_home_outline "get_block_view_home_outline"
     */
	public function serviceGetBlockViewHomeOutline($iProfileId = 0, $iStart = -1, $iPerPage = -1, $iTimeline = -1, $sFilter = '', $aModules = array())
    {
        return $this->_serviceGetBlockViewHome($iProfileId, BX_TIMELINE_VIEW_OUTLINE, $iStart, $iPerPage, $this->_oConfig->getPerPage('home'), $iTimeline, $sFilter, $aModules);
    }

	/**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-page_blocks Page Blocks
     * @subsubsection bx_timeline-get_block_view_hot get_block_view_hot
     * 
     * @code bx_srv('bx_timeline', 'get_block_view_hot', [...]); @endcode
     * 
     * Get Timeline View block with Hot public events.
     * 
     * @param $iProfileId (optional) profile ID. 0 should be used here.
     * @param $iStart (optional) integer value with a page number. Is used in pagination.
     * @param $iPerPage (optional) integer value with a number of items per page. Is used in pagination.
     * @param $iTimeline (optional) integer value determining whether the timeline should be displayed or not. 
     * @param $sFilter (optional) string value with filter name.
     * @param $aModules (optional) an array of modules from which the events should be displayed. All available modules are used by default.
     * @return an array describing a block to display on the site. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxTimelineModule::serviceGetBlockViewHot
     */
    /** 
     * @ref bx_timeline-get_block_view_hot "get_block_view_hot"
     */
    public function serviceGetBlockViewHot($iProfileId = 0, $iStart = -1, $iPerPage = -1, $iTimeline = -1, $sFilter = '', $aModules = array())
    {
        return $this->_serviceGetBlockViewHot($iProfileId, BX_TIMELINE_VIEW_TIMELINE, $iStart, $iPerPage, $this->_oConfig->getPerPage('home'), $iTimeline, $sFilter, $aModules);
    }

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-page_blocks Page Blocks
     * @subsubsection bx_timeline-get_block_view_hot_outline get_block_view_hot_outline
     * 
     * @code bx_srv('bx_timeline', 'get_block_view_hot_outline', [...]); @endcode
     * 
     * Get Outline View block with Hot public events.
     * 
     * @param $iProfileId (optional) profile ID. 0 should be used here.
     * @param $iStart (optional) integer value with a page number. Is used in pagination.
     * @param $iPerPage (optional) integer value with a number of items per page. Is used in pagination.
     * @param $iTimeline (optional) integer value determining whether the timeline should be displayed or not. 
     * @param $sFilter (optional) string value with filter name.
     * @param $aModules (optional) an array of modules from which the events should be displayed. All available modules are used by default.
     * @return an array describing a block to display on the site. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxTimelineModule::serviceGetBlockViewHotOutline
     */
    /** 
     * @ref bx_timeline-get_block_view_hot_outline "get_block_view_hot_outline"
     */
	public function serviceGetBlockViewHotOutline($iProfileId = 0, $iStart = -1, $iPerPage = -1, $iTimeline = -1, $sFilter = '', $aModules = array())
    {
        return $this->_serviceGetBlockViewHot($iProfileId, BX_TIMELINE_VIEW_OUTLINE, $iStart, $iPerPage, $this->_oConfig->getPerPage('home'), $iTimeline, $sFilter, $aModules);
    }

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-page_blocks Page Blocks
     * @subsubsection bx_timeline-get_block_view_account get_block_view_account
     * 
     * @code bx_srv('bx_timeline', 'get_block_view_account', [...]); @endcode
     * 
     * Get Timeline View block for the Dashboard page.
     * 
     * @param $iProfileId (optional) profile ID. 0 should be used here.
     * @param $iStart (optional) integer value with a page number. Is used in pagination.
     * @param $iPerPage (optional) integer value with a number of items per page. Is used in pagination.
     * @param $iTimeline (optional) integer value determining whether the timeline should be displayed or not. 
     * @param $sFilter (optional) string value with filter name.
     * @param $aModules (optional) an array of modules from which the events should be displayed. All available modules are used by default.
     * @return an array describing a block to display on the site. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxTimelineModule::serviceGetBlockViewAccount
     */
    /** 
     * @ref bx_timeline-get_block_view_account "get_block_view_account"
     */
    public function serviceGetBlockViewAccount($iProfileId = 0, $iStart = -1, $iPerPage = -1, $iTimeline = -1, $sFilter = '', $aModules = array())
    {
        if(!isLogged())
            return '';

        return $this->_serviceGetBlockViewByType($iProfileId, BX_TIMELINE_VIEW_TIMELINE, BX_TIMELINE_TYPE_OWNER_AND_CONNECTIONS, $iStart, $iPerPage, $this->_oConfig->getPerPage('account'), $iTimeline, $sFilter, $aModules);
    }

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-page_blocks Page Blocks
     * @subsubsection bx_timeline-get_block_view_account_outline get_block_view_account_outline
     * 
     * @code bx_srv('bx_timeline', 'get_block_view_account_outline', [...]); @endcode
     * 
     * Get Outline View block for the Dashboard page.
     * 
     * @param $iProfileId (optional) profile ID. 0 should be used here.
     * @param $iStart (optional) integer value with a page number. Is used in pagination.
     * @param $iPerPage (optional) integer value with a number of items per page. Is used in pagination.
     * @param $iTimeline (optional) integer value determining whether the timeline should be displayed or not. 
     * @param $sFilter (optional) string value with filter name.
     * @param $aModules (optional) an array of modules from which the events should be displayed. All available modules are used by default.
     * @return an array describing a block to display on the site. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxTimelineModule::serviceGetBlockViewAccountOutline
     */
    /** 
     * @ref bx_timeline-get_block_view_account_outline "get_block_view_account_outline"
     */
    public function serviceGetBlockViewAccountOutline($iProfileId = 0, $iStart = -1, $iPerPage = -1, $iTimeline = -1, $sFilter = '', $aModules = array())
    {
        if(!isLogged())
            return '';

        return $this->_serviceGetBlockViewByType($iProfileId, BX_TIMELINE_VIEW_OUTLINE, BX_TIMELINE_TYPE_OWNER_AND_CONNECTIONS, $iStart, $iPerPage, $this->_oConfig->getPerPage('account'), $iTimeline, $sFilter, $aModules);
    }

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-page_blocks Page Blocks
     * @subsubsection bx_timeline-get_block_item get_block_item
     * 
     * @code bx_srv('bx_timeline', 'get_block_item', [...]); @endcode
     * 
     * Get View Item block.
     * 
     * @return an array describing a block to display on the site. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxTimelineModule::serviceGetBlockItem
     */
    /** 
     * @ref bx_timeline-get_block_item "get_block_item"
     */
    public function serviceGetBlockItem()
    {
        $iItemId = bx_process_input(bx_get('id'), BX_DATA_INT);
        if(!$iItemId)
            return array();

        return $this->_oTemplate->getItemBlock($iItemId);
    }

    public function serviceGetBlockItemContent()
    {
        $iItemId = bx_process_input(bx_get('id'), BX_DATA_INT);
        $sMode = bx_process_input(bx_get('mode'));
        if(!$iItemId || !$sMode)
            return '';
            
        return $this->_oTemplate->getItemBlockContent($iItemId, $sMode);
    }
    
    public function serviceGetBlockItemInfo()
    {
        $iItemId = bx_process_input(bx_get('id'), BX_DATA_INT);
        if(!$iItemId)
            return '';

        return $this->_oTemplate->getItemBlockInfo($iItemId);
    }

    public function serviceGetBlockItemComments()
    {
        $iItemId = bx_process_input(bx_get('id'), BX_DATA_INT);
        if(!$iItemId)
            return '';

        return $this->_oTemplate->getItemBlockComments($iItemId);
    }
    
    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-integration_notifications Integration with Notifications
     * @subsubsection bx_timeline-get_notifications_data get_notifications_data
     * 
     * @code bx_srv('bx_timeline', 'get_notifications_data', [...]); @endcode
     * 
     * Data for Notifications module.
     * 
     * @return an array with special format.
     * 
     * @see BxTimelineModule::serviceGetNotificationsData
     */
    /** 
     * @ref bx_timeline-get_notifications_data "get_notifications_data"
     */
    public function serviceGetNotificationsData()
    {
    	$sModule = $this->_aModule['name'];

        return array(
            'handlers' => array(
                array('group' => $sModule . '_object', 'type' => 'insert', 'alert_unit' => $sModule, 'alert_action' => 'post_common', 'module_name' => $sModule, 'module_method' => 'get_notifications_post', 'module_class' => 'Module'),
                array('group' => $sModule . '_object', 'type' => 'delete', 'alert_unit' => $sModule, 'alert_action' => 'delete'),

                array('group' => $sModule . '_repost', 'type' => 'insert', 'alert_unit' => $sModule, 'alert_action' => 'repost', 'module_name' => $sModule, 'module_method' => 'get_notifications_repost', 'module_class' => 'Module'),
                array('group' => $sModule . '_repost', 'type' => 'delete', 'alert_unit' => $sModule, 'alert_action' => 'delete_repost'),

                array('group' => $sModule . '_comment', 'type' => 'insert', 'alert_unit' => $sModule, 'alert_action' => 'commentPost', 'module_name' => $sModule, 'module_method' => 'get_notifications_comment', 'module_class' => 'Module'),
                array('group' => $sModule . '_comment', 'type' => 'delete', 'alert_unit' => $sModule, 'alert_action' => 'commentRemoved'),

                array('group' => $sModule . '_vote', 'type' => 'insert', 'alert_unit' => $sModule, 'alert_action' => 'doVote', 'module_name' => $sModule, 'module_method' => 'get_notifications_vote', 'module_class' => 'Module'),
                array('group' => $sModule . '_vote', 'type' => 'delete', 'alert_unit' => $sModule, 'alert_action' => 'undoVote'),
            ),
            'settings' => array(
                array('group' => 'content', 'unit' => $sModule, 'action' => 'post_common', 'types' => array('personal', 'follow_member', 'follow_context')),
                array('group' => 'content', 'unit' => $sModule, 'action' => 'repost', 'types' => array('follow_member', 'follow_context')),
                array('group' => 'comment', 'unit' => $sModule, 'action' => 'commentPost', 'types' => array('personal', 'follow_member', 'follow_context')),
                array('group' => 'vote', 'unit' => $sModule, 'action' => 'doVote', 'types' => array('personal', 'follow_member', 'follow_context'))
            ),
            'alerts' => array(
                array('unit' => $sModule, 'action' => 'post_common'),
                array('unit' => $sModule, 'action' => 'delete'),
                
                array('unit' => $sModule, 'action' => 'repost'),
                array('unit' => $sModule, 'action' => 'delete_repost'),
                
                array('unit' => $sModule, 'action' => 'commentPost'),
                array('unit' => $sModule, 'action' => 'commentRemoved'),
                
                array('unit' => $sModule, 'action' => 'doVote'),
                array('unit' => $sModule, 'action' => 'undoVote'),
            )
        );
    }

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-integration_notifications Integration with Notifications
     * @subsubsection bx_timeline-get_notifications_repost get_notifications_repost
     * 
     * @code bx_srv('bx_timeline', 'get_notifications_repost', [...]); @endcode
     * 
     * Get data for Repost event to display in Notifications module.
     * 
     * @param $aEvent an array with event description.
     * @return an array with special format.
     * 
     * @see BxTimelineModule::serviceGetNotificationsRepost
     */
    /** 
     * @ref bx_timeline-get_notifications_repost "get_notifications_repost"
     */
    public function serviceGetNotificationsRepost($aEvent)
    {
        $aResult = $this->serviceGetNotificationsPost($aEvent);
        $aResult['lang_key'] = '_bx_timeline_txt_object_reposted';

        return $aResult;
    }

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-integration_notifications Integration with Notifications
     * @subsubsection bx_timeline-get_notifications_post get_notifications_post
     * 
     * @code bx_srv('bx_timeline', 'get_notifications_post', [...]); @endcode
     * 
     * Get data for Post event to display in Notifications module.
     * 
     * @param $aEvent an array with event description.
     * @return an array with special format.
     * 
     * @see BxTimelineModule::serviceGetNotificationsPost
     */
    /** 
     * @ref bx_timeline-get_notifications_post "get_notifications_post"
     */
    public function serviceGetNotificationsPost($aEvent)
    {
    	$CNF = &$this->_oConfig->CNF;

    	$iContent = (int)$aEvent['object_id'];
		$aContent = $this->_oDb->getEvents(array('browse' => 'id', 'value' => $iContent));
        if(empty($aContent) || !is_array($aContent))
            return array();

        $sEntryCaption = !empty($aContent['title']) ? $aContent['title'] : $this->_oConfig->getTitle($aContent['description']);

		return array(
			'entry_sample' => $CNF['T']['txt_sample_single_ext'],
			'entry_url' => $this->_oConfig->getItemViewUrl($aContent),
			'entry_caption' => $sEntryCaption,
			'entry_author' => $aContent['owner_id'],
			'lang_key' => '_bx_timeline_ntfs_txt_object_added', //may be empty or not specified. In this case the default one from Notification module will be used.
		);
    }

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-integration_notifications Integration with Notifications
     * @subsubsection bx_timeline-get_notifications_comment get_notifications_comment
     * 
     * @code bx_srv('bx_timeline', 'get_notifications_comment', [...]); @endcode
     * 
     * Get data for Post Comment event to display in Notifications module.
     * 
     * @param $aEvent an array with event description.
     * @return an array with special format.
     * 
     * @see BxTimelineModule::serviceGetNotificationsComment
     */
    /** 
     * @ref bx_timeline-get_notifications_comment "get_notifications_comment"
     */
    public function serviceGetNotificationsComment($aEvent)
    {
    	$CNF = &$this->_oConfig->CNF;

    	$iContent = (int)$aEvent['object_id'];
    	$aContent = $this->_oDb->getEvents(array('browse' => 'id', 'value' => $iContent));
        if(empty($aContent) || !is_array($aContent))
            return array();

		$oComment = BxDolCmts::getObjectInstance($CNF['OBJECT_COMMENTS'], $iContent);
        if(!$oComment || !$oComment->isEnabled())
            return array();

        $sEntryCaption = !empty($aContent['title']) ? $aContent['title'] : $this->_oConfig->getTitle($aContent['description']);

		return array(
			'entry_sample' => $CNF['T']['txt_sample_single'],
			'entry_url' => $this->_oConfig->getItemViewUrl($aContent),
			'entry_caption' => $sEntryCaption,
			'entry_author' => $aContent['owner_id'],
			'subentry_sample' => $CNF['T']['txt_sample_comment_single'],
			'subentry_url' => $oComment->getViewUrl((int)$aEvent['subobject_id']),
			'lang_key' => '', //may be empty or not specified. In this case the default one from Notification module will be used.
		);
    }

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-integration_notifications Integration with Notifications
     * @subsubsection bx_timeline-get_notifications_vote get_notifications_vote
     * 
     * @code bx_srv('bx_timeline', 'get_notifications_vote', [...]); @endcode
     * 
     * Get data for Vote event to display in Notifications module.
     * 
     * @param $aEvent an array with event description.
     * @return an array with special format.
     * 
     * @see BxTimelineModule::serviceGetNotificationsVote
     */
    /** 
     * @ref bx_timeline-get_notifications_vote "get_notifications_vote"
     */
    public function serviceGetNotificationsVote($aEvent)
    {
    	$CNF = &$this->_oConfig->CNF;

    	$iContent = (int)$aEvent['object_id'];
    	$aContent = $this->_oDb->getEvents(array('browse' => 'id', 'value' => $iContent));
        if(empty($aContent) || !is_array($aContent))
            return array();

		$oVote = BxDolVote::getObjectInstance($CNF['OBJECT_VOTES'], $iContent);
        if(!$oVote || !$oVote->isEnabled())
            return array();

        $sEntryCaption = !empty($aContent['title']) ? $aContent['title'] : $this->_oConfig->getTitle($aContent['description']);

		return array(
			'entry_sample' => $CNF['T']['txt_sample_single'],
			'entry_url' => $this->_oConfig->getItemViewUrl($aContent),
			'entry_caption' => $sEntryCaption,
			'entry_author' => $aContent['owner_id'],
			'subentry_sample' => $CNF['T']['txt_sample_vote_single'],
			'lang_key' => '', //may be empty or not specified. In this case the default one from Notification module will be used.
		);
    }

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-repost Repost
     * @subsubsection bx_timeline-get_repost_element_block get_repost_element_block
     * 
     * @code bx_srv('bx_timeline', 'get_repost_element_block', [...]); @endcode
     * 
     * Get repost element for content based modules.
     * 
     * @param $iOwnerId integer value with owner profile ID.
     * @param $sType string value with type (module name). 
     * @param $sAction string value with action (module action). 
     * @param $iObjectId integer value with object ID to be reposted. 
     * @param $aParams (optional) an array with additional params.
     * @return HTML string with repost element to display on the site, all necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxTimelineModule::serviceGetRepostElementBlock
     */
    /** 
     * @ref bx_timeline-get_repost_element_block "get_repost_element_block"
     */
    public function serviceGetRepostElementBlock($iOwnerId, $sType, $sAction, $iObjectId, $aParams = array())
    {
    	if(!$this->isEnabled())
    		return '';

        $aParams = array_merge($this->_oConfig->getRepostDefaults(), $aParams);
        return $this->_oTemplate->getRepostElement($iOwnerId, $sType, $sAction, $iObjectId, $aParams);
    }

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-repost Repost
     * @subsubsection bx_timeline-get_repost_counter get_repost_counter
     * 
     * @code bx_srv('bx_timeline', 'get_repost_counter', [...]); @endcode
     * 
     * Get repost counter.
     * 
     * @param $sType string value with type (module name). 
     * @param $sAction string value with action (module action). 
     * @param $iObjectId integer value with object ID to be reposted. 
     * @return HTML string with repost counter to display on the site, all necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxTimelineModule::serviceGetRepostCounter
     */
    /** 
     * @ref bx_timeline-get_repost_counter "get_repost_counter"
     */
    public function serviceGetRepostCounter($sType, $sAction, $iObjectId)
    {
    	if(!$this->isEnabled())
    		return '';

		$aReposted = $this->_oDb->getReposted($sType, $sAction, $iObjectId);

        return $this->_oTemplate->getRepostCounter($aReposted);
    }

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-repost Repost
     * @subsubsection bx_timeline-get_repost_js_script get_repost_js_script
     * 
     * @code bx_srv('bx_timeline', 'get_repost_js_script', [...]); @endcode
     * 
     * Get repost JavaScript code.
     * 
     * @return HTML string with JavaScript code to display on the site, all necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxTimelineModule::serviceGetRepostJsScript
     */
    /** 
     * @ref bx_timeline-get_repost_js_script "get_repost_js_script"
     */
    public function serviceGetRepostJsScript()
    {
    	if(!$this->isEnabled())
    		return '';

        return $this->_oTemplate->getRepostJsScript();
    }

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-repost Repost
     * @subsubsection bx_timeline-get_repost_js_click get_repost_js_click
     * 
     * @code bx_srv('bx_timeline', 'get_repost_js_click', [...]); @endcode
     * 
     * Get repost JavaScript code for OnClick event.
     * 
     * @param $iOwnerId integer value with owner profile ID.
     * @param $sType string value with type (module name). 
     * @param $sAction string value with action (module action). 
     * @param $iObjectId integer value with object ID to be reposted. 
     * @return HTML string with JavaScript code to display in OnClick events of HTML elements.
     * 
     * @see BxTimelineModule::serviceGetRepostJsClick
     */
    /** 
     * @ref bx_timeline-get_repost_js_click "get_repost_js_click"
     */
    public function serviceGetRepostJsClick($iOwnerId, $sType, $sAction, $iObjectId)
    {
    	if(!$this->isEnabled())
    		return '';

        return $this->_oTemplate->getRepostJsClick($iOwnerId, $sType, $sAction, $iObjectId);
    }

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-other Other
     * @subsubsection bx_timeline-get_menu_item_addon_comment get_menu_item_addon_comment
     * 
     * @code bx_srv('bx_timeline', 'get_menu_item_addon_comment', [...]); @endcode
     * 
     * Get addon for Comment menu item.
     * 
     * @param $sSystem string value with comments engine system.
     * @param $iObjectId integer value with object ID. 
     * @return HTML string to display in menu item.
     * 
     * @see BxTimelineModule::serviceGetMenuItemAddonComment
     */
    /** 
     * @ref bx_timeline-get_menu_item_addon_comment "get_menu_item_addon_comment"
     */
    public function serviceGetMenuItemAddonComment($sSystem, $iObjectId)
    {
        if(empty($sSystem) || empty($iObjectId))
            return '';

        $oCmts = $this->getCmtsObject($sSystem, $iObjectId);
        if($oCmts === false)
            return '';

        $iCounter = (int)$oCmts->getCommentsCount();
        return  $this->_oTemplate->parseLink('javascript:void(0)', $iCounter > 0 ? $iCounter : '', array(
            'title' => _t('_bx_timeline_menu_item_title_item_comment'),
        	'onclick' => "javascript:" . $this->_oConfig->getJsObject('view') . ".commentItem(this, '" . $sSystem . "', " . $iObjectId . ")" 
        ));
    }

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-other Other
     * @subsubsection bx_timeline-get_settings_checker_helper get_settings_checker_helper
     * 
     * @code bx_srv('bx_timeline', 'get_settings_checker_helper', [...]); @endcode
     * 
     * Get Checker Helper class name for Forms engine.
     * 
     * @return string with Checker Helper class name.
     * 
     * @see BxTimelineModule::serviceGetSettingsCheckerHelper
     */
    /** 
     * @ref bx_timeline-get_settings_checker_helper "get_settings_checker_helper"
     */
    public function serviceGetSettingsCheckerHelper()
    {
        bx_import('FormCheckerHelper', $this->_aModule);
        return 'BxTimelineFormCheckerHelper';
    }

	/**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-other Other
     * @subsubsection bx_timeline-get_options_videos_autoplay get_options_videos_autoplay
     * 
     * @code bx_srv('bx_timeline', 'get_options_videos_autoplay', [...]); @endcode
     * 
     * Get an array with available options for 'Videos autoplay in Timeline' setting.
     *
     * @return an array with available options represented as key => value pairs.
     * 
     * @see BxTimelineModule::serviceGetOptionsVideosAutoplay
     */
    /** 
     * @ref bx_timeline-get_options_videos_autoplay "get_options_videos_autoplay"
     */
    public function serviceGetOptionsVideosAutoplay()
    {
        $aOptions = array(BX_TIMELINE_VAP_OFF, BX_TIMELINE_VAP_ON_MUTE, BX_TIMELINE_VAP_ON);

        $aResult = array();
        foreach($aOptions as $sOption)
            $aResult[] = array(
                'key' => $sOption,
                'value' => _t('_bx_timeline_option_videos_autoplay_' . $sOption)
            );

        return $aResult;
    }

    /**
     * @page service Service Calls
     * @section bx_timeline Timeline
     * @subsection bx_timeline-other Other
     * @subsubsection bx_timeline-get_live_updates get_live_updates
     * 
     * @code bx_srv('bx_timeline', 'get_live_updates', [...]); @endcode
     * 
     * Get an array with actual Live Update info.
     *
     * @return an array with Live Update info.
     * 
     * @see BxTimelineModule::serviceGetLiveUpdates
     */
    /** 
     * @ref bx_timeline-get_live_updates "get_live_updates"
     */
    public function serviceGetLiveUpdates($sType, $iOwnerId, $iProfileId, $iCount = 0, $iInit = 0)
    {
		$sKey = $this->_oConfig->getLiveUpdateKey($sType, $iOwnerId);

		bx_import('BxDolSession');
    	if((int)BxDolSession::getInstance()->getValue($sKey) == 1)
    		return false;

        $aParams = $this->_prepareParams(BX_TIMELINE_VIEW_DEFAULT, $sType, $iOwnerId, false, false, BX_TIMELINE_FILTER_OTHER_VIEWER);
        $aParams['count'] = true;

		$iCountNew = $this->_oDb->getEvents($aParams);
		if($iCountNew == $iCount)
			return false;

		if((int)$iInit != 0)
			return array('count' => $iCountNew);

    	return array(
			'count' => $iCountNew, // required (for initialization and visualization)
			'method' => $this->_oConfig->getJsObject('view') . '.showLiveUpdate(oData)', // required (for visualization)
			'data' => array(
				'code' => $this->_oTemplate->getLiveUpdateNotification($sType, $iOwnerId, $iProfileId, $iCount, $iCountNew)
			),  // optional, may have some additional data to be passed in JS method provided using 'method' param above.
		);
    }
    
    /**
     * @page service Service Calls
     * @section bx_timeline Accounts
     * @subsection bx_timeline-other Other
     * @subsubsection bx_timeline-get_menu_addon_manage_tools get_menu_addon_manage_tools
     * 
     * @code bx_srv('bx_timeline', 'get_menu_addon_manage_tools', [...]); @endcode
     * 
     * Get number of 'hidden' events for User End -> Dasboard page -> Manage block.
     *
     * @return integer number of 'hidden' events
     * 
     * @see BxTimelineModule::serviceGetMenuAddonManageTools
     */
    /** 
     * @ref bx_timeline-get_menu_addon_manage_tools "get_menu_addon_manage_tools"
     */
	public function serviceGetMenuAddonManageTools()
	{
		bx_import('SearchResult', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SearchResult';
        $o = new $sClass();
        $o->fillFilters(array(
			'active' => 0
        ));
        $o->unsetPaginate();

        return $o->getNum();
	}

    /*
     * COMMON METHODS
     */
    public function deleteEvent($aEvent)
    {
    	if(empty($aEvent) || !is_array($aEvent) || !$this->_oDb->deleteEvent(array('id' => (int)$aEvent['id'])))
            return false;

        $this->onDelete($aEvent);
        return true;
    }
    

    public function addAttachLink($aValues, $sDisplay = false)
    {
        $CNF = &$this->_oConfig->CNF;

        if(!$sDisplay)
            $sDisplay = $this->_oConfig->getObject('form_display_attach_link_add');

        $oForm = BxDolForm::getObjectInstance($this->_oConfig->getObject('form_attach_link'), $sDisplay, $this->_oTemplate);
        if(!$oForm)
            return array('message' => '_sys_txt_error_occured');

        $oForm->aFormAttrs['method'] = BX_DOL_FORM_METHOD_SPECIFIC;
        $oForm->aParams['csrf']['disable'] = true;
        if(!empty($oForm->aParams['db']['submit_name'])) {
            $sSubmitName = $oForm->aParams['db']['submit_name'];
            if(!isset($oForm->aInputs[$sSubmitName])) {
                if(isset($oForm->aInputs[$CNF['FIELD_CONTROLS']]))
                    foreach($oForm->aInputs[$CNF['FIELD_CONTROLS']] as $mixedIndex => $aInput) {
                        if(!is_numeric($mixedIndex) || empty($aInput['name']) || $aInput['name'] != $sSubmitName)
                            continue;
    
                        $aValues[$sSubmitName] = $aInput['value'];
                    }
            }
            else            
                $aValues[$sSubmitName] = $oForm->aInputs[$sSubmitName]['value'];
        }

        $oForm->aInputs['url']['checker']['params']['preg'] = $this->_oConfig->getPregPattern('url');

        $oForm->initChecker(array(), $aValues);
        if(!$oForm->isSubmittedAndValid())
            return array('message' => '_sys_txt_error_occured');

        return $this->_addLink($oForm);
    }

    public function getFormAttachLink()
    {
        $oForm = BxDolForm::getObjectInstance($this->_oConfig->getObject('form_attach_link'), $this->_oConfig->getObject('form_display_attach_link_add'), $this->_oTemplate);
        $oForm->aFormAttrs['action'] = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'submit_attach_link_form/';
        $oForm->aInputs['url']['checker']['params']['preg'] = $this->_oConfig->getPregPattern('url');

        $oForm->initChecker();
        if($oForm->isSubmittedAndValid())
            return $this->_addLink($oForm);

        return array('form' => $oForm->getCode(), 'form_id' => $oForm->id);
    }

    public function getFormPost($aParams = array())
    {
        $iUserId = $this->getUserId();

        $oForm = $this->getFormPostObject($aParams);
        $oForm->initChecker();

        $bAjaxMode = $oForm->isAjaxMode();
        $bDynamicMode = $bAjaxMode;

        if($oForm->isSubmittedAndValid()) {
            list($sUserName) = $this->getUserInfo($iUserId);

            $sType = $oForm->getCleanValue('type');
            $sType = $this->_oConfig->getPrefix('common_post') . $sType;
            BxDolForm::setSubmittedValue('type', $sType, $oForm->aFormAttrs['method']);

            $aContent = array();

            //--- Process Text ---//
            $sText = $oForm->getCleanValue('text');
            $sText = $this->_prepareTextForSave($sText);
            $bText = !empty($sText);

            if($bText)
            	$aContent['text'] = $sText;

            //--- Process Context and Privacy ---//
            $iOwnerId = (int)$oForm->getCleanValue('owner_id');
            $iObjectPrivacyView = (int)$oForm->getCleanValue('object_privacy_view');
            $iObjectPrivacyViewDefault = $this->_oConfig->getPrivacyViewDefault('object');
            if(empty($iObjectPrivacyView))
                $iObjectPrivacyView = $iObjectPrivacyViewDefault;
            else if($iObjectPrivacyView < 0)
                $iOwnerId = abs($iObjectPrivacyView);

            //--- Process Link ---//
            $aLinkIds = $oForm->getCleanValue('link');
            $bLinkIds = !empty($aLinkIds) && is_array($aLinkIds);

            //--- Process Media ---//
            $aPhotoIds = $oForm->getCleanValue(BX_TIMELINE_MEDIA_PHOTO);
            $bPhotoIds = !empty($aPhotoIds) && is_array($aPhotoIds);

            $aVideoIds = $oForm->getCleanValue(BX_TIMELINE_MEDIA_VIDEO);
            $bVideoIds = !empty($aVideoIds) && is_array($aVideoIds);

            if(!$bText && !$bLinkIds && !$bPhotoIds && !$bVideoIds) {
                $oForm->aInputs['text']['error'] =  _t('_bx_timeline_txt_err_empty_post');
                $oForm->setValid(false);

            	return $this->_prepareResponse(array('form' => $oForm->getCode($bDynamicMode), 'form_id' => $oForm->id), $bAjaxMode);
            }

            $sTitle = $bText ? $this->_oConfig->getTitle($sText) : $this->_oConfig->getTitleDefault($bLinkIds, $bPhotoIds, $bVideoIds);
            $sDescription = _t('_bx_timeline_txt_user_added_sample', '{profile_name}', _t('_bx_timeline_txt_sample_with_article'));

            //--- Process Date ---//
            $iDate = 0;
            if(isset($oForm->aInputs['date']))
                $iDate = $oForm->getCleanValue('date');
            if(empty($iDate))
                $iDate = time();

            /**
             * Unset 'text' input because its data was already processed 
             * and will be saved via additional values which were passed 
             * to BxDolForm::insert method.
             */
            unset($oForm->aInputs['text']);

            $iId = $oForm->insert(array(
                'owner_id' => $iOwnerId,
                'object_id' => $iUserId,
                'object_privacy_view' => $iObjectPrivacyView,
                'content' => serialize($aContent),
                'title' => $sTitle,
                'description' => $sDescription,
                'date' => $iDate
            ));

            if(!empty($iId)) {
            	$oMetatags = BxDolMetatags::getObjectInstance($this->_oConfig->getObject('metatags'));
            	if($bText)
                    $oMetatags->metaAdd($iId, $sText);
                $oMetatags->locationsAddFromForm($iId, $this->_oConfig->CNF['FIELD_LOCATION_PREFIX']);

                //--- Process Link ---//
                if($bLinkIds)
                    foreach($aLinkIds as $iLinkId)
                        $this->_oDb->saveLink($iId, $iLinkId);

				//--- Process Media ---//
				if($bPhotoIds) 
					$this->_saveMedia(BX_TIMELINE_MEDIA_PHOTO, $iId, $aPhotoIds);

				if($bVideoIds)
					$this->_saveMedia(BX_TIMELINE_MEDIA_VIDEO, $iId, $aVideoIds);

                $this->onPost($iId);

                return $this->_prepareResponse(array('id' => $iId), $bAjaxMode, array(
                	'redirect' => $this->_oConfig->getItemViewUrl(array('id' => $iId))
                ));
            }

            return $this->_prepareResponse(array('message' => _t('_bx_timeline_txt_err_cannot_perform_action')), $bAjaxMode);
        }

        return $this->_prepareResponse(array('form' => $oForm->getCode($bDynamicMode), 'form_id' => $oForm->id), $bAjaxMode && $oForm->isSubmitted());
    }

    public function getFormEdit($iId, $aParams = array())
    {
        $CNF = &$this->_oConfig->CNF;

        $aEvent = $this->_oDb->getEvents(array('browse' => 'id', 'value' => $iId));
        if(empty($aEvent) || !is_array($aEvent))
            return array();

        $aContent = unserialize($aEvent['content']);
        if(is_array($aContent) && !empty($aContent['text']))
            $aEvent['text'] = $aContent['text'];

		$bDynamicMode = isset($aParams['dynamic_mode']) ? (bool)$aParams['dynamic_mode'] : false;
        $sFormObject = !empty($aParams['form_object']) ? $aParams['form_object'] : 'form_post';
        $sFormDisplay = !empty($aParams['form_display']) ? $aParams['form_display'] : 'form_display_post_edit';
        $oForm = BxDolForm::getObjectInstance($this->_oConfig->getObject($sFormObject), $this->_oConfig->getObject($sFormDisplay), $this->_oTemplate);
        $oForm->setId($this->_oConfig->getHtmlIds('view', 'edit_form') . $iId);

        $oForm->aFormAttrs['action'] = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'edit/' . $iId ;
        foreach($oForm->aInputs[$CNF['FIELD_CONTROLS']] as $mixedIndex => $aInput) {
            if(!is_numeric($mixedIndex))
                continue;

            $oForm->aInputs[$CNF['FIELD_CONTROLS']][$mixedIndex]['attrs'] = bx_replace_markers($aInput['attrs'], array(
                'js_object_view' => $this->_oConfig->getJsObject('view'),
            	'content_id' => $iId
            ));
        }

        $oForm->initChecker($aEvent);
        if($oForm->isSubmittedAndValid()) {
            $aContent = array();

            //--- Process Text ---//
            $sText = $oForm->getCleanValue('text');
            $sText = $this->_prepareTextForSave($sText);
            $bText = !empty($sText);
            unset($oForm->aInputs['text']);

            if($bText)
            	$aContent['text'] = $sText;

            $aValsToAdd = array(
            	'content' => serialize($aContent)
            );

            //--- Process Privacy ---//
            $iObjectPrivacyView = (int)$oForm->getCleanValue('object_privacy_view');
            $iObjectPrivacyViewDefault = $this->_oConfig->getPrivacyViewDefault('object');
            if(empty($iObjectPrivacyView))
                $aValsToAdd = array_merge($aValsToAdd, array(
                    'object_privacy_view' => $iObjectPrivacyViewDefault
                ));
            else if($iObjectPrivacyView < 0) 
                $aValsToAdd = array_merge($aValsToAdd, array(
                    'owner_id' => abs($iObjectPrivacyView),
                    'object_privacy_view' => $iObjectPrivacyViewDefault
                ));

            //--- Process Date ---//
            if(isset($oForm->aInputs['date'])) {
                $iDate = $oForm->getCleanValue('date');
                if(empty($iDate))
                    $iDate = time();

                $aValsToAdd['date'] = $iDate;
            }

            if(!$oForm->update($iId, $aValsToAdd))
                return array('message' => _t('_bx_timeline_txt_err_cannot_perform_action'));

            $oMetatags = BxDolMetatags::getObjectInstance($this->_oConfig->getObject('metatags'));
        	if($bText)
				$oMetatags->metaAdd($iId, $sText);
			$oMetatags->locationsAddFromForm($iId, $this->_oConfig->CNF['FIELD_LOCATION_PREFIX']);

            return array(
                'id' => $iId,
            	'eval' => $this->_oConfig->getJsObject('view') . '.onEditPostSubmit(oData)'
            );
        }

        return array(
            'id' => $iId, 
        	'form' => $oForm->getCode($bDynamicMode), 
        	'form_id' => $oForm->id,
        	'eval' => $this->_oConfig->getJsObject('view') . '.onEditPost(oData)'
        );
    }

    public function getFormPostObject($aParams)
    {
    	$sFormObject = !empty($aParams['form_object']) ? $aParams['form_object'] : 'form_post';
        $sFormDisplay = !empty($aParams['form_display']) ? $aParams['form_display'] : 'form_display_post_add';

        $oForm = BxDolForm::getObjectInstance($this->_oConfig->getObject($sFormObject), $this->_oConfig->getObject($sFormDisplay), $this->_oTemplate);        

        $sParamsKey = 'ajax_mode';
        if(isset($aParams[$sParamsKey]) && is_bool($aParams[$sParamsKey]))
            $oForm->setAjaxMode((bool)$aParams[$sParamsKey]);

        $sParamsKey = 'visibility_autoselect';
        if(isset($aParams[$sParamsKey]) && is_bool($aParams[$sParamsKey]))
            $oForm->setVisibilityAutoselect((bool)$aParams[$sParamsKey]);

        $oForm->init();
        return $oForm;
    }

    public function getCmtsObject($sSystem, $iId)
    {
        if(empty($sSystem) || (int)$iId == 0)
            return false;

        $oCmts = BxDolCmts::getObjectInstance($sSystem, $iId, true, $this->_oTemplate);
        if(!$oCmts || !$oCmts->isEnabled())
            return false;

        return $oCmts;
    }

    public function getViewObject($sSystem, $iId)
    {
        if(empty($sSystem) || (int)$iId == 0)
            return false;

        $oView = BxDolView::getObjectInstance($sSystem, $iId, true, $this->_oTemplate);
        if(!$oView || !$oView->isEnabled())
            return false;

        return $oView;
    }

    public function getVoteObject($sSystem, $iId)
    {
        if(empty($sSystem) || (int)$iId == 0)
            return false;

        $oVote = BxDolVote::getObjectInstance($sSystem, $iId, true, $this->_oTemplate);
        if(!$oVote || !$oVote->isEnabled())
            return false;

        return $oVote;
    }

    public function getScoreObject($sSystem, $iId)
    {
        if(empty($sSystem) || (int)$iId == 0)
            return false;

        $oScore = BxDolScore::getObjectInstance($sSystem, $iId, true, $this->_oTemplate);
        if(!$oScore || !$oScore->isEnabled())
            return false;

        return $oScore;
    }

	public function getReportObject($sSystem, $iId)
    {
        if(empty($sSystem) || (int)$iId == 0)
            return false;

        $oReport = BxDolReport::getObjectInstance($sSystem, $iId, true, $this->_oTemplate);
        if(!$oReport || !$oReport->isEnabled())
            return false;

        return $oReport;
    }

    public function getAttachmentsMenuObject()
    {
        $oMenu = BxDolMenu::getObjectInstance($this->_oConfig->getObject('menu_post_attachments'), $this->_oTemplate);
        $oMenu->addMarkers(array(
            'js_object' => $this->_oConfig->getJsObject('post'),
        ));

        return $oMenu;
    }

    public function getManageMenuObject()
    {
        return BxDolMenu::getObjectInstance($this->_oConfig->getObject('menu_item_manage'), $this->_oTemplate);
    }

    //--- Check permissions methods ---//
    public function isModerator()
    {
        $sModule = $this->getName();
        $iUserId = (int)$this->getUserId();

        $aCheckResult = checkActionModule($iUserId, 'edit', $sModule);
        if($aCheckResult[CHECK_ACTION_RESULT] === CHECK_ACTION_RESULT_ALLOWED)
            return true;

        $aCheckResult = checkActionModule($iUserId, 'delete', $sModule);
        if($aCheckResult[CHECK_ACTION_RESULT] === CHECK_ACTION_RESULT_ALLOWED)
            return true;

        return false;
    }

    public function isAllowedPost($bPerform = false)
    {
        if(isAdmin())
            return true;

        $iUserId = $this->getUserId();
        $aCheckResult = checkActionModule($iUserId, 'post', $this->getName(), $bPerform);

        $oProfileOwner = BxDolProfile::getInstance($this->_iOwnerId);
        if($oProfileOwner !== false) {
            if($oProfileOwner->checkAllowedPostInProfile() !== CHECK_ACTION_RESULT_ALLOWED)
                return _t('_sys_txt_access_denied');

            bx_alert($oProfileOwner->getModule(), $this->_oConfig->getUri() . '_post', $oProfileOwner->id(), $iUserId, array('check_result' => &$aCheckResult));
        }

        return $aCheckResult[CHECK_ACTION_RESULT] !== CHECK_ACTION_RESULT_ALLOWED ? $aCheckResult[CHECK_ACTION_MESSAGE] : true;
    }

    public function isAllowedView($aEvent, $bPerform = false)
    {
        $CNF = $this->_oConfig->CNF;

        $mixedResult = BxDolProfile::getInstance($aEvent[$CNF['FIELD_OWNER_ID']])->checkAllowedProfileView();
        if($mixedResult !== CHECK_ACTION_RESULT_ALLOWED)
            return false;

		return true;
    }

    public function isAllowedEdit($aEvent, $bPerform = false)
    {
        if(!isLogged())
            return false;
            
        //--- System posts and Reposts cannot be edited at all.
        if(!$this->_oConfig->isCommon($aEvent['type'], $aEvent['action']) || $aEvent['type'] == $this->_oConfig->getPrefix('common_post') . 'repost')
            return false;

        if(isAdmin())
            return true;

        $iUserId = (int)$this->getUserId();
        $iOwnerId = (int)$aEvent['owner_id'];
        $iObjectId = abs((int)$aEvent['object_id']);
        if($iObjectId == $iUserId && $this->_oConfig->isAllowEdit())
           return true;

        $aCheckResult = checkActionModule($iUserId, 'edit', $this->getName(), $bPerform);

        $oProfileOwner = BxDolProfile::getInstance($iOwnerId);
        if($oProfileOwner !== false)
            bx_alert($oProfileOwner->getModule(), $this->_oConfig->getUri() . '_edit', $oProfileOwner->id(), $iUserId, array('check_result' => &$aCheckResult));

        return $aCheckResult[CHECK_ACTION_RESULT] !== CHECK_ACTION_RESULT_ALLOWED ? $aCheckResult[CHECK_ACTION_MESSAGE] : true;
    }

    public function isAllowedDelete($aEvent, $bPerform = false)
    {
        if(!isLogged())
            return false;

        if(isAdmin())
            return true;

        $iUserId = (int)$this->getUserId();
        $iOwnerId = (int)$aEvent['owner_id'];
        $iObjectId = abs((int)$aEvent['object_id']);
        if((($iOwnerId == $iUserId && $this->_oConfig->isAllowDelete()) || ($this->_oConfig->isCommon($aEvent['type'], $aEvent['action']) && $iObjectId == $iUserId)))
           return true;

        $aCheckResult = checkActionModule($iUserId, 'delete', $this->getName(), $bPerform);

        $oProfileOwner = BxDolProfile::getInstance($iOwnerId);
        if($oProfileOwner !== false)
            bx_alert($oProfileOwner->getModule(), $this->_oConfig->getUri() . '_delete', $oProfileOwner->id(), $iUserId, array('check_result' => &$aCheckResult));

        return $aCheckResult[CHECK_ACTION_RESULT] !== CHECK_ACTION_RESULT_ALLOWED ? $aCheckResult[CHECK_ACTION_MESSAGE] : true;
    }

    public function isAllowedComment($aEvent, $bPerform = false)
    {
        $mixedComments = $this->getCommentsData($aEvent['comments']);
        if($mixedComments === false)
            return false;

        list($sSystem, $iObjectId) = $mixedComments;
        $oCmts = $this->getCmtsObject($sSystem, $iObjectId);
        $oCmts->addCssJs();

        $bResult = true;

        $oProfileOwner = BxDolProfile::getInstance($aEvent['owner_id']);
        if($oProfileOwner !== false) {
            if($oProfileOwner->checkAllowedPostInProfile() !== CHECK_ACTION_RESULT_ALLOWED)
                return false;

            bx_alert($oProfileOwner->getModule(), $this->_oConfig->getUri() . '_comment', $oProfileOwner->id(), (int)$this->getUserId(), array('result' => &$bResult));
        }

        return $bResult;
    }

    public function isAllowedViewCounter($aEvent, $bPerform = false)
    {
        $mixedViews = $this->getViewsData($aEvent['views']);
        if($mixedViews === false)
            return false;

        list($sSystem, $iObjectId) = $mixedViews;
        $oView = $this->getViewObject($sSystem, $iObjectId);

        if(!$oView->isAllowedViewView($bPerform))
        	return false;

        $bResult = true;

        $oProfileOwner = BxDolProfile::getInstance($aEvent['owner_id']);
        if($oProfileOwner !== false)
            bx_alert($oProfileOwner->getModule(), $this->_oConfig->getUri() . '_view_counter', $oProfileOwner->id(), (int)$this->getUserId(), array('result' => &$bResult));

        return $bResult;
    }

    public function isAllowedVote($aEvent, $bPerform = false)
    {
        $mixedVotes = $this->getVotesData($aEvent['votes']);
        if($mixedVotes === false)
            return false;

        list($sSystem, $iObjectId) = $mixedVotes;
        $oVote = $this->getVoteObject($sSystem, $iObjectId);

        if(!$oVote->isAllowedVote($bPerform))
        	return false;

        $bResult = true;

        $oProfileOwner = BxDolProfile::getInstance($aEvent['owner_id']);
        if($oProfileOwner !== false)
            bx_alert($oProfileOwner->getModule(), $this->_oConfig->getUri() . '_vote', $oProfileOwner->id(), (int)$this->getUserId(), array('result' => &$bResult));

        return $bResult;
    }

    public function isAllowedScore($aEvent, $bPerform = false)
    {
        $mixedScores = $this->getScoresData($aEvent['scores']);
        if($mixedScores === false)
            return false;

        list($sSystem, $iObjectId) = $mixedScores;
        $oScore = $this->getScoreObject($sSystem, $iObjectId);

        if(!$oScore->isAllowedVote($bPerform))
        	return false;

        $bResult = true;

        $oProfileOwner = BxDolProfile::getInstance($aEvent['owner_id']);
        if($oProfileOwner !== false)
            bx_alert($oProfileOwner->getModule(), $this->_oConfig->getUri() . '_score', $oProfileOwner->id(), (int)$this->getUserId(), array('result' => &$bResult));

        return $bResult;
    }

    public function isAllowedReport($aEvent, $bPerform = false)
    {
        $mixedReports = $this->getReportsData($aEvent['reports']);
        if($mixedReports === false)
            return false;

        list($sSystem, $iObjectId) = $mixedReports;
        $oReport = $this->getReportObject($sSystem, $iObjectId);

        if(!$oReport->isAllowedReport($bPerform))
        	return false;

        $bResult = true;

        $oProfileOwner = BxDolProfile::getInstance($aEvent['owner_id']);
        if($oProfileOwner !== false)
            bx_alert($oProfileOwner->getModule(), $this->_oConfig->getUri() . '_report', $oProfileOwner->id(), (int)$this->getUserId(), array('result' => &$bResult));

        return $bResult;
    }

    public function isAllowedRepost($aEvent, $bPerform = false)
    {
        if(isAdmin())
            return true;

        $iUserId = (int)$this->getUserId();
        if($iUserId == 0)
            return false;

        $aCheckResult = checkActionModule($iUserId, 'repost', $this->getName(), $bPerform);

        $oProfileOwner = BxDolProfile::getInstance($aEvent['owner_id']);
        if($oProfileOwner !== false)
            bx_alert($oProfileOwner->getModule(), $this->_oConfig->getUri() . '_repost', $oProfileOwner->id(), $iUserId, array('check_result' => &$aCheckResult));

        return $aCheckResult[CHECK_ACTION_RESULT] !== CHECK_ACTION_RESULT_ALLOWED ? $aCheckResult[CHECK_ACTION_MESSAGE] : true;
    }

    public function isAllowedSend($aEvent, $bPerform = false)
    {
        if(!$this->_oDb->isEnabledByName('bx_convos'))
            return false;

        if(isAdmin())
            return true;

        $iUserId = (int)$this->getUserId();
        if($iUserId == 0)
            return false;

        $aCheckResult = checkActionModule($iUserId, 'send', $this->getName(), $bPerform);

        $oProfileOwner = BxDolProfile::getInstance($aEvent['owner_id']);
        if($oProfileOwner !== false)
            bx_alert($oProfileOwner->getModule(), $this->_oConfig->getUri() . '_send', $oProfileOwner->id(), $iUserId, array('check_result' => &$aCheckResult));

        return $aCheckResult[CHECK_ACTION_RESULT] !== CHECK_ACTION_RESULT_ALLOWED ? $aCheckResult[CHECK_ACTION_MESSAGE] : true;
    }

    /**
     * Pin - "Pin here" - pin the post on Profile Timeline for profile owner.
     * Can be done by profile owner for himself or by admin for profile owner to see.
     * @param type $aEvent
     * @param type $bPerform
     * @return boolean
     */
    public function isAllowedPin($aEvent, $bPerform = false)
    {
    	if((int)$aEvent['pinned'] != 0)
    		return false;

        return $this->_isAllowedPin($aEvent, $bPerform);
    }

    public function isAllowedUnpin($aEvent, $bPerform = false)
    {
    	if((int)$aEvent['pinned'] == 0)
    		return false;

        return $this->_isAllowedPin($aEvent, $bPerform);
    }

    /**
     * Stick - "Pin for All" - pin the post on Public Timeline for everybody to see.
     * Is available for Administrators/Moderators only.
     * @param type $aEvent
     * @param type $bPerform
     * @return boolean
     */
    public function isAllowedStick($aEvent, $bPerform = false)
    {
    	if((int)$aEvent['sticked'] != 0)
    		return false;
    
    	return $this->_isAllowedStick($aEvent, $bPerform);
    }
    
    public function isAllowedUnstick($aEvent, $bPerform = false)
    {
    	if((int)$aEvent['sticked'] == 0)
    		return false;
    
    	return $this->_isAllowedStick($aEvent, $bPerform);
    }
    
    public function isAllowedPromote($aEvent, $bPerform = false)
    {
    	if((int)$aEvent['promoted'] != 0)
    		return false;

        return $this->_isAllowedPromote($aEvent, $bPerform);
    }

	public function isAllowedUnpromote($aEvent, $bPerform = false)
    {
    	if((int)$aEvent['promoted'] == 0)
    		return false;

        return $this->_isAllowedPromote($aEvent, $bPerform);
    }

    public function isAllowedMore($aEvent, $bPerform = false)
    {
    	$oMoreMenu = $this->getManageMenuObject();
    	$oMoreMenu->setEventById($aEvent['id']);
    	return $oMoreMenu->isVisible();
    }

    public function checkAllowedView ($aContentInfo, $isPerformAction = false)
    {
        if(!$this->isAllowedView($aContentInfo, $isPerformAction))
            return _t('_sys_txt_access_denied');

        return CHECK_ACTION_RESULT_ALLOWED;
    }

    public function checkAllowedCommentsView ($aContentInfo, $isPerformAction = false)
    {
        $CNF = $this->_oConfig->CNF;

        $mixedResult = BxDolProfile::getInstance($aContentInfo[$CNF['FIELD_OWNER_ID']])->checkAllowedProfileView();
        if($mixedResult !== CHECK_ACTION_RESULT_ALLOWED)
            return $mixedResult;

        return CHECK_ACTION_RESULT_ALLOWED;
    }

    public function checkAllowedCommentsPost ($aContentInfo, $isPerformAction = false)
    {
        $sError = '_sys_txt_access_denied';

        $aContentInfo = $this->_oTemplate->getData($aContentInfo);
        if($aContentInfo === false)
            return _t($sError);

        if(!$this->isAllowedComment($aContentInfo, $isPerformAction))
            return _t($sError);

        return CHECK_ACTION_RESULT_ALLOWED;
    }

    public function onPost($iId)
    {
        $aEvent = $this->_oDb->getEvents(array('browse' => 'id', 'value' => $iId));

        if($this->_oConfig->isSystem($aEvent['type'], $aEvent['action'])) {
            //--- Request event's data from content module and update it in the Timeline DB.
            $this->_oTemplate->getData($aEvent);

            $sPostType = 'system';
            $iSenderId = $aEvent['owner_id'];
        } else {
            $sPostType = 'common';
            $iSenderId = $aEvent['object_id'];
        }

        //--- Event -> Post for Alerts Engine ---//
        $oAlert = new BxDolAlerts($this->_oConfig->getObject('alert'), 'post_' . $sPostType, $iId, $iSenderId, array(
        	'privacy_view' => $aEvent['object_privacy_view'],
        	'object_author_id' => $aEvent['owner_id'],
        ));
        $oAlert->alert();
        //--- Event -> Post for Alerts Engine ---//
    }

    public function onRepost($iId, $aReposted = array())
    {
        $aEvent = $this->_oDb->getEvents(array('browse' => 'id', 'value' => $iId));

        if(empty($aReposted)) {
            $aContent = unserialize($aEvent['content']);

            $aReposted = $this->_oDb->getReposted($aContent['type'], $aContent['action'], $aContent['object_id']);
            if(empty($aReposted) || !is_array($aReposted))
                return;
        }

        $iUserId = $this->getUserId();
        $this->_oDb->insertRepostTrack($aEvent['id'], $iUserId, $this->getUserIp(), $aReposted['id']);
        $this->_oDb->updateRepostCounter($aReposted['id'], $aReposted['reposts']);

        //--- Timeline -> Update for Alerts Engine ---//
        $oAlert = new BxDolAlerts($this->_oConfig->getObject('alert'), 'repost', $aReposted['id'], $iUserId, array(
        	'privacy_view' => $aEvent['object_privacy_view'],
        	'object_author_id' => $aReposted['owner_id'],
        	'repost_id' => $iId,
        ));
        $oAlert->alert();
        //--- Timeline -> Update for Alerts Engine ---//
    }

    public function onDelete($aEvent)
    {
        $iUserId = $this->getUserId();
    	$sCommonPostPrefix = $this->_oConfig->getPrefix('common_post');
    	$sCommonPostComment = $this->_oConfig->getObject('comment');
    	
    	//--- Delete comments for Common posts.
    	if($this->_oConfig->isCommon($aEvent['type'], $aEvent['action'])) {
            $oComments = $this->getCmtsObject($sCommonPostComment, $aEvent['id']);
            if($oComments !== false)
                $oComments->onObjectDelete($aEvent['id']);
    	}

    	//--- Delete attached photos, videos and links when common event was deleted.
    	if($aEvent['type'] == $sCommonPostPrefix . BX_TIMELINE_PARSE_TYPE_POST) {
    		$this->_deleteMedia(BX_TIMELINE_MEDIA_PHOTO, $aEvent['id']);
    		$this->_deleteMedia(BX_TIMELINE_MEDIA_VIDEO, $aEvent['id']);

	        $this->_deleteLinks($aEvent['id']);
    	}

    	//--- Update parent event when repost event was deleted.
    	$bRepost = $aEvent['type'] == $sCommonPostPrefix . BX_TIMELINE_PARSE_TYPE_REPOST;
        if($bRepost) {
            $this->_oDb->deleteRepostTrack($aEvent['id']);

            $aContent = unserialize($aEvent['content']);
            $aReposted = $this->_oDb->getReposted($aContent['type'], $aContent['action'], $aContent['object_id']);
            if(!empty($aReposted) && is_array($aReposted))
                $this->_oDb->updateRepostCounter($aReposted['id'], $aReposted['reposts'], -1);
        }

        //--- Find and delete repost events when parent event was deleted.
        $bSystem = $this->_oConfig->isSystem($aEvent['type'], $aEvent['action']);
	    $aRepostEvents = $this->_oDb->getEvents(array('browse' => 'reposted_by_descriptor', 'type' => $aEvent['type']));
		foreach($aRepostEvents as $aRepostEvent) {
			$aContent = unserialize($aRepostEvent['content']);
			if(isset($aContent['type']) && $aContent['type'] == $aEvent['type'] && isset($aContent['object_id']) && (($bSystem && (int)$aContent['object_id'] == (int)$aEvent['object_id']) || (!$bSystem  && (int)$aContent['object_id'] == (int)$aEvent['id'])) && (int)$this->_oDb->deleteEvent(array('id' => (int)$aRepostEvent['id'])) > 0) {
			    $oComments = $this->getCmtsObject($sCommonPostComment, $aRepostEvent['id']);
                if($oComments !== false)
                    $oComments->onObjectDelete($aRepostEvent['id']);

                bx_alert($this->_oConfig->getObject('alert'), 'delete_repost', $aEvent['id'], $iUserId, array(
                    'repost_id' => $aRepostEvent['id'],
                ));
            }
		}

		//--- Delete associated meta.
        $oMetatags = BxDolMetatags::getObjectInstance($this->_oConfig->getObject('metatags'));
        $oMetatags->onDeleteContent($aEvent['id']);

        //--- Event -> Delete for Alerts Engine ---//
        if($bRepost)
            bx_alert($this->_oConfig->getObject('alert'), 'delete_repost', $aReposted['id'], $iUserId, array(
                'repost_id' => $aEvent['id'],
            ));
        else
            bx_alert($this->_oConfig->getObject('alert'), 'delete', $aEvent['id'], $iUserId);
        //--- Event -> Delete for Alerts Engine ---//
    }

    public function getParams($sView = '', $sType = '', $iOwnerId = 0, $iStart = 0, $iPerPage = 0, $sFilter = BX_TIMELINE_FILTER_ALL, $aModules = array(), $iTimeline = 0)
    {
        return $this->_prepareParams($sView, $sType, $iOwnerId, $iStart, $iPerPage, $sFilter, $aModules, $iTimeline);
    }

    public function getViewsData(&$aViews)
    {
        if(empty($aViews) || !is_array($aViews))
            return false;

        $sSystem = isset($aViews['system']) ? $aViews['system'] : '';
        $iObjectId = isset($aViews['object_id']) ? (int)$aViews['object_id'] : 0;
        $iCount = isset($aViews['count']) ? (int)$aViews['count'] : 0;
        if($sSystem == '' || $iObjectId == 0)
            return false;

        return array($sSystem, $iObjectId, $iCount);
    }

    public function getVotesData(&$aVotes)
    {
        if(empty($aVotes) || !is_array($aVotes))
            return false;

        $sSystem = isset($aVotes['system']) ? $aVotes['system'] : '';
        $iObjectId = isset($aVotes['object_id']) ? (int)$aVotes['object_id'] : 0;
        $iCount = isset($aVotes['count']) ? (int)$aVotes['count'] : 0;
        if($sSystem == '' || $iObjectId == 0)
            return false;

        return array($sSystem, $iObjectId, $iCount);
    }

    public function getScoresData(&$aScores)
    {
        if(empty($aScores) || !is_array($aScores))
            return false;

        $sSystem = isset($aScores['system']) ? $aScores['system'] : '';
        $iObjectId = isset($aScores['object_id']) ? (int)$aScores['object_id'] : 0;
        $iScore = isset($aScores['score']) ? (int)$aScores['score'] : 0;
        if($sSystem == '' || $iObjectId == 0)
            return false;

        return array($sSystem, $iObjectId, $iScore);
    }

    public function getReportsData(&$aReports)
    {
        if(empty($aReports) || !is_array($aReports))
            return false;

        $sSystem = isset($aReports['system']) ? $aReports['system'] : '';
        $iObjectId = isset($aReports['object_id']) ? (int)$aReports['object_id'] : 0;
        $iCount = isset($aReports['count']) ? (int)$aReports['count'] : 0;
        if($sSystem == '' || $iObjectId == 0)
            return false;

        return array($sSystem, $iObjectId, $iCount);
    }

    public function getCommentsData(&$aComments)
    {
        if(empty($aComments) || !is_array($aComments))
            return false;

        $sSystem = isset($aComments['system']) ? $aComments['system'] : '';
        $iObjectId = isset($aComments['object_id']) ? (int)$aComments['object_id'] : 0;
        $iCount = isset($aComments['count']) ? (int)$aComments['count'] : 0;
        if($sSystem == '' || $iObjectId == 0 || ($iCount == 0 && !isLogged()))
            return false;

        return array($sSystem, $iObjectId, $iCount);
    }

    public function getUserInfo($iUserId = 0)
    {
        $iLoggedId = $this->getUserId();
        $iUserId = (int)$iUserId;
        return parent::getUserInfo($iUserId);
    }

    /**
     * Protected Methods 
     */
    protected function _serviceGetBlockView($iProfileId = 0, $sView = BX_TIMELINE_VIEW_DEFAULT)
    {
        if(empty($iProfileId) && bx_get('profile_id') !== false)
			$iProfileId = bx_process_input(bx_get('profile_id'), BX_DATA_INT);

		if(empty($iProfileId) && isLogged())
			$iProfileId = bx_get_logged_profile_id();

        $aBlock = $this->_getBlockView($iProfileId, $sView);
        if(!empty($aBlock))
            return $aBlock;

        return array('content' => MsgBox(_t('_bx_timeline_txt_msg_no_results')));
    }

    protected function _serviceGetBlockViewProfile($sProfileModule = 'bx_persons', $iProfileContentId = 0, $sView = BX_TIMELINE_VIEW_DEFAULT, $iStart = -1, $iPerPage = -1, $sFilter = '', $aModules = array(), $iTimeline = -1)
    {
    	if(empty($sProfileModule))
    		return array();

    	if(empty($iProfileContentId) && bx_get('id') !== false)
    		$iProfileContentId = bx_process_input(bx_get('id'), BX_DATA_INT);

		$oProfile = BxDolProfile::getInstanceByContentAndType($iProfileContentId, $sProfileModule);
		if(empty($oProfile))
			return array();

        return $this->_getBlockView($oProfile->id(), $sView, $iStart, $iPerPage, $sFilter, $aModules, $iTimeline);
    }

    protected function _serviceGetBlockViewHome($iProfileId = 0, $sView = BX_TIMELINE_VIEW_DEFAULT, $iStart = -1, $iPerPage = -1, $iPerPageDefault = -1,  $iTimeline = -1, $sFilter = '', $aModules = array())
    {
        $sRssUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'rss/' . BX_BASE_MOD_NTFS_TYPE_PUBLIC . '/';
        BxDolTemplate::getInstance()->addPageRssLink(_t('_bx_timeline_page_title_view_home'), $sRssUrl);

        return $this->_serviceGetBlockViewByType($iProfileId, $sView, BX_BASE_MOD_NTFS_TYPE_PUBLIC, $iStart, $iPerPage, $iPerPageDefault, $iTimeline, $sFilter, $aModules);
    }

    protected function _serviceGetBlockViewHot($iProfileId = 0, $sView = BX_TIMELINE_VIEW_DEFAULT, $iStart = -1, $iPerPage = -1, $iPerPageDefault = -1,  $iTimeline = -1, $sFilter = '', $aModules = array())
    {
        $sRssUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'rss/' . BX_TIMELINE_TYPE_HOT . '/';
        BxDolTemplate::getInstance()->addPageRssLink(_t('_bx_timeline_page_title_view_hot'), $sRssUrl);

        return $this->_serviceGetBlockViewByType($iProfileId, $sView, BX_TIMELINE_TYPE_HOT, $iStart, $iPerPage, $iPerPageDefault, $iTimeline, $sFilter, $aModules);
    }

    protected function _serviceGetBlockViewByType($iProfileId = 0, $sView = BX_TIMELINE_VIEW_DEFAULT, $sType = BX_TIMELINE_TYPE_DEFAULT, $iStart = -1, $iPerPage = -1, $iPerPageDefault = -1,  $iTimeline = -1, $sFilter = '', $aModules = array())
    {
        $aParams = $this->_prepareParams($sView, $sType, $iProfileId, $iStart, $iPerPage, $sFilter, $aModules, $iTimeline);

        $aParams['view'] = $sView;
        $aParams['per_page'] = (int)$iPerPage > 0 ? $iPerPage : ((int)$iPerPageDefault > 0 ? $iPerPageDefault : $this->_oConfig->getPerPage());

        $this->_iOwnerId = $aParams['owner_id'];

        $sContent = $this->_oTemplate->getViewBlock($aParams);
        return array('content' => $sContent);
    }

    protected function _getBlockPost($iProfileId, $aParams = array())
    {
        $this->_iOwnerId = $iProfileId;

        if($this->isAllowedPost() !== true)
            return array();

		$sContent = $this->_oTemplate->getPostBlock($this->_iOwnerId, $aParams);
        return array('content' => $sContent);
    }

    protected function _getBlockView($iProfileId, $sView = BX_TIMELINE_VIEW_DEFAULT, $iStart = -1, $iPerPage = -1, $sFilter = '', $aModules = array(), $iTimeline = -1)
    {
        if(!$iProfileId)
			return array();

        $aParams = $this->_prepareParams($sView, BX_BASE_MOD_NTFS_TYPE_OWNER, $iProfileId, $iStart, $iPerPage, $sFilter, $aModules, $iTimeline);
        $aParams['view'] = $sView;
        $aParams['per_page'] = (int)$iPerPage > 0 ? $iPerPage : $this->_oConfig->getPerPage('profile');

        $this->_iOwnerId = $aParams['owner_id'];
        $oProfileOwner = BxDolProfile::getInstance($this->_iOwnerId);

        $mixedResult = $oProfileOwner->checkAllowedProfileView();
        if($mixedResult !== CHECK_ACTION_RESULT_ALLOWED)
            return array('content' => MsgBox($mixedResult));

        list($sUserName, $sUserUrl) = $this->getUserInfo($aParams['owner_id']);

        $sRssUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'rss/' . BX_BASE_MOD_NTFS_TYPE_OWNER . '/' . $iProfileId . '/';
        $sJsObject = $this->_oConfig->getJsObject('view');
        $aMenu = array(
            array('id' => $sView . '-view-all', 'name' => $sView . '-view-all', 'class' => '', 'link' => 'javascript:void(0)', 'onclick' => 'javascript:' . $sJsObject . '.changeFilter(this)', 'target' => '_self', 'title' => _t('_bx_timeline_menu_item_view_all'), 'active' => 1),
            array('id' => $sView . '-view-owner', 'name' => $sView . '-view-owner', 'class' => '', 'link' => 'javascript:void(0)', 'onclick' => 'javascript:' . $sJsObject . '.changeFilter(this)', 'target' => '_self', 'title' => _t('_bx_timeline_menu_item_view_owner', $sUserName)),
            array('id' => $sView . '-view-other', 'name' => $sView . '-view-other', 'class' => '', 'link' => 'javascript:void(0)', 'onclick' => 'javascript:' . $sJsObject . '.changeFilter(this)', 'target' => '_self', 'title' => _t('_bx_timeline_menu_item_view_other')),
            array('id' => $sView . '-get-rss', 'name' => $sView . '-get-rss', 'class' => '', 'link' => $sRssUrl, 'target' => '_blank', 'title' => _t('_bx_timeline_menu_item_get_rss')),
        );

        $sContent = '';
        bx_alert($oProfileOwner->getModule(), $this->_oConfig->getUri() . '_view', $this->_iOwnerId, $this->getUserId(), array('override_content' => &$sContent, 'params' => &$aParams, 'menu' => &$aMenu));

        $oMenu = new BxTemplMenuInteractive(array('template' => 'menu_interactive_vertical.html', 'menu_id'=> $sView . '-view-all', 'menu_items' => $aMenu));
        $oMenu->setSelected('', $sView . '-view-all');

        if (!$sContent)
            $sContent = $this->_oTemplate->getViewBlock($aParams);

        BxDolTemplate::getInstance()->addPageRssLink(_t('_bx_timeline_page_title_view'), $sRssUrl);

        return array('content' => $sContent, 'menu' => $oMenu);
    }
    
    protected function _getContentForTimelinePost($aEvent, $aContentInfo, $aBrowseParams = array())
    {
    	$CNF = &$this->_oConfig->CNF;

    	$sUrl = BX_DOL_URL_ROOT . BxDolPermalinks::getInstance()->permalink('page.php?i=' . $CNF['URI_VIEW_ENTRY'] . '&id=' . $aContentInfo[$CNF['FIELD_ID']]);

        $sText = ''; 
        if(isset($aContentInfo['content'])){
            $aTmp = unserialize($aContentInfo['content']);
            if(isset($aTmp['text']))
                $sText = $aTmp['text'];
        }

        if(empty($sText) && !empty($aContentInfo[$CNF['FIELD_TITLE']]))
            $sText = $aContentInfo[$CNF['FIELD_TITLE']];

        if(!empty($CNF['OBJECT_METATAGS']) && !empty($sText)) {
            $oMetatags = BxDolMetatags::getObjectInstance($CNF['OBJECT_METATAGS']);
            $sText = $oMetatags->metaParse($aContentInfo[$CNF['FIELD_ID']], $sText);
        }

        return array(
            'sample' => isset($CNF['T']['txt_sample_single_with_article']) ? $CNF['T']['txt_sample_single_with_article'] : $CNF['T']['txt_sample_single'],
            'sample_wo_article' => $CNF['T']['txt_sample_single'],
            'sample_action' => isset($CNF['T']['txt_sample_action']) ? $CNF['T']['txt_sample_action'] : '',
            'url' => $sUrl,
            'title' => '',
            'text' => $sText,
            'images' => array(),
            'videos' => array()
        );
    }

	protected function _isAllowedPin($aEvent, $bPerform = false)
    {
    	$iUserId = (int)$this->getUserId();
    	if($iUserId == 0)
    		return false;

		if(isAdmin() || (int)$aEvent['owner_id'] == $iUserId || ((int)$aEvent['owner_id'] == 0 && $this->_oConfig->isCommon($aEvent['type'], $aEvent['action']) && (int)$aEvent['object_id'] == $iUserId))
           return true;

        $aCheckResult = checkActionModule($iUserId, 'pin', $this->getName(), $bPerform);

        $oProfileOwner = BxDolProfile::getInstance($aEvent['owner_id']);
        if($oProfileOwner !== false)
            bx_alert($oProfileOwner->getModule(), $this->_oConfig->getUri() . '_pin', $oProfileOwner->id(), $iUserId, array('check_result' => &$aCheckResult));

        return $aCheckResult[CHECK_ACTION_RESULT] !== CHECK_ACTION_RESULT_ALLOWED ? $aCheckResult[CHECK_ACTION_MESSAGE] : true;
    }

    protected function _isAllowedStick($aEvent, $bPerform = false)
    {
    	$iUserId = (int)$this->getUserId();
    	if($iUserId == 0)
    		return false;

    	if(isAdmin())
    		return true;

    	$aCheckResult = checkActionModule($iUserId, 'stick', $this->getName(), $bPerform);
    
    	$oProfileOwner = BxDolProfile::getInstance($aEvent['owner_id']);
    	if($oProfileOwner !== false)
    		bx_alert($oProfileOwner->getModule(), $this->_oConfig->getUri() . '_stick', $oProfileOwner->id(), $iUserId, array('check_result' => &$aCheckResult));
    
    	return $aCheckResult[CHECK_ACTION_RESULT] !== CHECK_ACTION_RESULT_ALLOWED ? $aCheckResult[CHECK_ACTION_MESSAGE] : true;
    }

    protected function _isAllowedPromote($aEvent, $bPerform = false)
    {
        if(isAdmin())
            return true;

        $iUserId = (int)$this->getUserId();
        if($iUserId == 0)
            return false;

        $aCheckResult = checkActionModule($iUserId, 'promote', $this->getName(), $bPerform);

        $oProfileOwner = BxDolProfile::getInstance($aEvent['owner_id']);
        if($oProfileOwner !== false)
            bx_alert($oProfileOwner->getModule(), $this->_oConfig->getUri() . '_promote', $oProfileOwner->id(), $iUserId, array('check_result' => &$aCheckResult));

        return $aCheckResult[CHECK_ACTION_RESULT] !== CHECK_ACTION_RESULT_ALLOWED ? $aCheckResult[CHECK_ACTION_MESSAGE] : true;
    }

    protected function _addLink(&$oForm)
    {
        $iUserId = $this->getUserId();

        $sLink = $oForm->getCleanValue('url');

        $aMatches = array();
        preg_match($this->_oConfig->getPregPattern('url'), $sLink, $aMatches);
        $sLink = (empty($aMatches[2]) ? 'http://' : '') . $aMatches[0];

        $aSiteInfo = bx_get_site_info($sLink, array(
            'thumbnailUrl' => array('tag' => 'link', 'content_attr' => 'href'),
            'OGImage' => array('name_attr' => 'property', 'name' => 'og:image'),
        ));

        $sTitle = !empty($aSiteInfo['title']) ? $aSiteInfo['title'] : _t('_Empty');
        $sDescription = !empty($aSiteInfo['description']) ? $aSiteInfo['description'] : _t('_Empty');

        $sMediaUrl = '';
        if(!empty($aSiteInfo['thumbnailUrl']))
        	$sMediaUrl = $aSiteInfo['thumbnailUrl'];
        else if(!empty($aSiteInfo['OGImage']))
        	$sMediaUrl = $aSiteInfo['OGImage'];

		$iMediaId = 0;
		$oStorage = null;
        if(!empty($sMediaUrl)) {
        	$oStorage = BxDolStorage::getObjectInstance($this->_oConfig->getObject('storage_' . BX_TIMELINE_MEDIA_PHOTO . 's'));

        	$iMediaId = $oStorage->storeFileFromUrl($sMediaUrl, true, $iUserId);
        }

        $iId = (int)$oForm->insert(array('profile_id' => $iUserId, 'media_id' => $iMediaId, 'url' => $sLink, 'title' => $sTitle, 'text' => $sDescription, 'added' => time()));
        if(!empty($iId)) {
        	if(!empty($oStorage) && !empty($iMediaId))
        		$oStorage->afterUploadCleanup($iMediaId, $iUserId);

            return array('id' => $iId, 'item' => $this->_oTemplate->getAttachLinkItem($iUserId, $iId));
        }

        return array('message' => _t('_bx_timeline_txt_err_cannot_perform_action'));
    }

	protected function _deleteLinks($iId)
    {
	    $aLinks = $this->_oDb->getLinks($iId);
	    if(empty($aLinks) || !is_array($aLinks))
	    	return;

		$oStorage = BxDolStorage::getObjectInstance($this->_oConfig->getObject('storage_photos'));
		foreach($aLinks as $aLink)
			if(!empty($aLink['media_id']))
				$oStorage->deleteFile($aLink['media_id']);

		$this->_oDb->deleteLinks($iId);
    }

    protected function _saveMedia($sType, $iId, $aItemIds)
    {
    	if(empty($aItemIds) || !is_array($aItemIds))
    		return; 

    	$iUserId = $this->getUserId();

		$oStorage = BxDolStorage::getObjectInstance($this->_oConfig->getObject('storage_' . strtolower($sType) . 's'));
		foreach($aItemIds as $iItemId)
        	if($this->_oDb->saveMedia($sType, $iId, $iItemId))
            	$oStorage->afterUploadCleanup($iItemId, $iUserId);
    }

    protected function _deleteMedia($sType, $iId)
    {
	    $aItems = $this->_oDb->getMedia($sType, $iId);
	    if(empty($aItems) || !is_array($aItems))
	    	return;

		$oStorage = BxDolStorage::getObjectInstance($this->_oConfig->getObject('storage_' . strtolower($sType) . 's'));
		foreach($aItems as $iItemId)
			$oStorage->deleteFile($iItemId);

		$this->_oDb->deleteMedia($sType, $iId);
    }

    protected function _prepareParams($sView, $sType, $iOwnerId, $iStart, $iPerPage, $sFilter = BX_TIMELINE_FILTER_ALL, $aModules = array(), $iTimeline = 0, $aBlink = array())
    {
         $aParams = array(
            'view' => !empty($sView) ? $sView : BX_TIMELINE_VIEW_DEFAULT,

            'browse' => 'list',
            'type' => !empty($sType) ? $sType : BX_TIMELINE_TYPE_DEFAULT,
            'owner_id' => (int)$iOwnerId != 0 ? $iOwnerId : $this->getUserId(),
            'filter' => !empty($sFilter) ? $sFilter : BX_TIMELINE_FILTER_ALL,
            'modules' => is_array($aModules) && !empty($aModules) ? $aModules : array(),
            'timeline' => (int)$iTimeline > 0 ? $iTimeline : 0,
         	'blink' => is_array($aBlink) && !empty($aBlink) ? $aBlink : array(),
            'active' => 1,
            'hidden' => 0
        );

        if($iStart !== false)
            $aParams['start'] = (int)$iStart > 0 ? $iStart : 0;

        if($iPerPage !== false)
            $aParams['per_page'] = (int)$iPerPage > 0 ? $iPerPage : $this->_oConfig->getPerPage();

        if($this->_oConfig->isHot())
            $aParams['hot'] = $this->_oDb->getHot();

        return $aParams;
    }

    protected function _prepareParamsGet()
    {
        $aParams = array(
            'browse' => 'list',
            'dynamic_mode' => true,
        );

        $aParams['view'] = bx_get('view');
        $aParams['view'] = $aParams['view'] !== false ? bx_process_input($aParams['view'], BX_DATA_TEXT) : BX_TIMELINE_VIEW_DEFAULT;

        $aParams['type'] = bx_get('type');
        $aParams['type'] = $aParams['type'] !== false ? bx_process_input($aParams['type'], BX_DATA_TEXT) : BX_TIMELINE_TYPE_DEFAULT;

        $aParams['owner_id'] = bx_get('owner_id');
        $aParams['owner_id'] = $aParams['owner_id'] !== false ? bx_process_input($aParams['owner_id'], BX_DATA_INT) : $this->getUserId();

        $aParams['start'] = bx_get('start');
        $aParams['start'] = $aParams['start'] !== false ? bx_process_input($aParams['start'], BX_DATA_INT) : 0;

        $aParams['per_page'] = bx_get('per_page');
        $aParams['per_page'] = $aParams['per_page'] !== false ? bx_process_input($aParams['per_page'], BX_DATA_INT) : $this->_oConfig->getPerPage();

        $aParams['filter'] = bx_get('filter');
        $aParams['filter'] = $aParams['filter'] !== false ? bx_process_input($aParams['filter'], BX_DATA_TEXT) : BX_TIMELINE_FILTER_ALL;

        $aParams['modules'] = bx_get('modules');
        $aParams['modules'] = $aParams['modules'] !== false ? bx_process_input($aParams['modules'], BX_DATA_TEXT) : array();

        $aParams['timeline'] = bx_get('timeline');
        $aParams['timeline'] = $aParams['timeline'] !== false ? bx_process_input($aParams['timeline'], BX_DATA_INT) : 0;

        $aParams['blink'] = bx_get('blink');
        $aParams['blink'] = $aParams['blink'] !== false ? explode(',', bx_process_input($aParams['blink'], BX_DATA_TEXT)) : array();

        $aParams['active'] = 1;
        $aParams['hidden'] = 0;

        if($this->_oConfig->isHot())
            $aParams['hot'] = $this->_oDb->getHot();

        return $aParams;
    }

    protected function _prepareTextForSave($s)
    {
        return bx_process_input($s, BX_DATA_HTML);
    }

    protected function _getFieldValue($sField, $iContentId)
    {
        $CNF = &$this->_oConfig->CNF;
        if(empty($CNF[$sField]))
            return false;

        $aEvent = $this->_oDb->getEvents(array('browse' => 'id', 'value' => $iContentId));
        if(empty($aEvent) || empty($aEvent[$CNF[$sField]]))
            return false;

        return $aEvent[$CNF[$sField]];
    }
}

/** @} */
