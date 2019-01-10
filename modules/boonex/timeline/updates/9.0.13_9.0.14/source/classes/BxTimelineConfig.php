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

bx_import('BxDolPrivacy');

class BxTimelineConfig extends BxBaseModNotificationsConfig
{
    protected $_aTypeToFormDisplay;

    protected $_bAllowEdit;
    protected $_bAllowDelete;
    protected $_bShowAll;

    protected $_iRssLength;
    protected $_iLiveUpdateLength;
    protected $_iCharsDisplayMaxTitle;

    protected $_sVideosAutoplay;
    protected $_iPreloadComments;
    protected $_iPreloadCommentsMax;

    protected $_bHot;
    protected $_iHotInterval;

    protected $_bEditorToolbar;
    protected $_bUnhideRestored;

    protected $_sStorageObject;
    protected $_sTranscoderObjectPreview;
    protected $_sTranscoderObjectView;
    protected $_aPhotoUploaders;
    protected $_aVideoUploaders;

    protected $_sMenuItemManage;
    protected $_sMenuItemActions;

    protected $_aRepostDefaults;

    protected $_iTimelineVisibilityThreshold;
    protected $_aPregPatterns;

    /**
     * Constructor
     */
    public function __construct($aModule)
    {
        parent::__construct($aModule);

        $this->CNF = array (
            'ICON' => 'far clock col-green1',

            // database tables
            'TABLE_ENTRIES' => $aModule['db_prefix'] . 'events',

            // database fields
            'FIELD_ID' => 'id',
            'FIELD_OWNER_ID' => 'owner_id',
            'FIELD_OBJECT_ID' => 'object_id', //Note. For 'Direct Timeline Posts' this field contains post's author profile ID.
            'FIELD_OBJECT_PRIVACY_VIEW' => 'object_privacy_view',
            'FIELD_ADDED' => 'date',
            'FIELD_TITLE' => 'title',
            'FIELD_TEXT' => 'description',
            'FIELD_ANONYMOUS' => 'anonymous',
            'FIELD_CONTROLS' => 'controls',
            'FIELD_LOCATION_PREFIX' => 'location',

            // page URIs
            'URI_VIEW_LIST' => 'timeline-view',
            'URI_VIEW_ENTRY' => 'item',

            'URL_HOME' => 'page.php?i=timeline-view-home',

            // objects
            'OBJECT_STORAGE' => $this->_sName . '_photos',
            'OBJECT_GRID_ADMINISTRATION' => $this->_sName . '_administration',
            'OBJECT_IMAGES_TRANSCODER_PREVIEW' => $this->_sName . '_photos_preview',
            'OBJECT_METATAGS' => $this->_sName,
            'OBJECT_COMMENTS' => $this->_sName,
            'OBJECT_VIEWS' => $this->_sName,
            'OBJECT_VOTES' => $this->_sName,
            'OBJECT_SCORES' => $this->_sName,
            'OBJECT_REPORTS' => $this->_sName,

            // some language keys
            'T' => array (
                'txt_sample_single' => '_bx_timeline_txt_sample',
                'txt_sample_single_ext' => '_bx_timeline_txt_sample_ext',
            	'txt_sample_comment_single' => '_bx_timeline_txt_sample_comment_single',
                'txt_sample_vote_single' => '_bx_timeline_txt_sample_vote_single',
                'txt_sample_with_link' => '_bx_timeline_txt_sample_with_link',
                'txt_sample_with_image' => '_bx_timeline_txt_sample_with_image',
                'txt_sample_with_video' => '_bx_timeline_txt_sample_with_video',
                'txt_sample_with_media' => '_bx_timeline_txt_sample_with_media',
                'grid_action_err_delete' => '_bx_timeline_grid_action_err_delete', 
                'grid_txt_account_manager' => '_bx_timeline_grid_txt_account_manager',
            ),
        );

        $this->_aTypeToFormDisplay = array(
            BX_BASE_MOD_NTFS_TYPE_OWNER => 'form_display_post_add_profile',
            BX_BASE_MOD_NTFS_TYPE_PUBLIC => 'form_display_post_add_public',
            BX_TIMELINE_TYPE_OWNER_AND_CONNECTIONS => 'form_display_post_add'
        );

        $this->_aPrefixes = array(
            'style' => 'bx-tl',
            'language' => '_bx_timeline',
            'option' => 'bx_timeline_',
            'common_post' => 'timeline_common_'
        );

        $this->_aObjects = array_merge($this->_aObjects, array(
            'comment' => $this->CNF['OBJECT_COMMENTS'],
            'view' => $this->CNF['OBJECT_VIEWS'],
            'vote' => $this->CNF['OBJECT_VOTES'],
            'score' => $this->CNF['OBJECT_SCORES'],
            'report' => $this->CNF['OBJECT_REPORTS'],
            'metatags' => $this->_sName,

            'storage_photos' => $this->CNF['OBJECT_STORAGE'],
            'storage_videos' => $this->_sName . '_videos',
            'transcoder_photos_preview' => $this->CNF['OBJECT_IMAGES_TRANSCODER_PREVIEW'],
            'transcoder_photos_view' => $this->_sName . '_photos_view',
            'transcoder_photos_medium' => $this->_sName . '_photos_medium',
            'transcoder_photos_big' => $this->_sName . '_photos_big',
            'transcoder_videos_poster' => $this->_sName . '_videos_poster',
            'transcoder_videos_mp4' => $this->_sName . '_videos_mp4',
            'transcoder_videos_webm' => $this->_sName . '_videos_webm',

            'page_item_brief' => $this->_sName . '_item_brief',

            'menu_item_manage' => $this->_sName . '_menu_item_manage',
            'menu_item_actions' => $this->_sName . '_menu_item_actions',
            'menu_item_meta' => $this->_sName . '_menu_item_meta',
            'menu_post_attachments' => $this->_sName . '_menu_post_attachments',

            'form_post' => $this->_sName . '_post',
            'form_attach_link' => $this->_sName . '_attach_link',
            'form_display_post_add' => $this->_sName . '_post_add',
            'form_display_post_add_public' => $this->_sName . '_post_add_public',
            'form_display_post_add_profile' => $this->_sName . '_post_add_profile',
            'form_display_post_edit' => $this->_sName . '_post_edit',
            'form_display_attach_link_add' => $this->_sName . '_attach_link_add'
        ));

        $this->_aHandlerDescriptor = array('module_name' => '', 'module_method' => '', 'module_class' => '', 'groupable' => '', 'group_by' => '');
        $this->_sHandlersMethod = 'get_timeline_data';

        $this->_aPhotoUploaders = array($this->_sName . '_simple_photo');
        $this->_aVideoUploaders = array($this->_sName . '_simple_video');

        $this->_bJsMode = false;
        $this->_aJsClasses = array(
            'main' => 'BxTimelineMain',
            'view' => 'BxTimelineView',
            'post' => 'BxTimelinePost',
            'repost' => 'BxTimelineRepost',
            'manage_tools' => 'BxTimelineManageTools'
        );
        $this->_aJsObjects = array(
            'view' => 'oTimelineView',
            'post' => 'oTimelinePost',
            'repost' => 'oTimelineRepost',
            'manage_tools' => 'oBxTimelineManageTools'
        );

        $this->_aGridObjects = array(
            'administration' => $this->CNF['OBJECT_GRID_ADMINISTRATION']
        );

        $sHp = str_replace('_', '-', $this->_sName);
        $sHpI = BX_TIMELINE_VIEW_ITEM;
        $sHpT = BX_TIMELINE_VIEW_TIMELINE;
        $sHpO = BX_TIMELINE_VIEW_OUTLINE;
        $sHpS = BX_TIMELINE_VIEW_SEARCH;
        $this->_aHtmlIds = array(
            'view' => array(
                'edit_form' => $sHp . '-edit-',

                'main_' . $sHpI => $sHp . '-' . $sHpI,
                'item_' . $sHpI => $sHp . '-item-' . $sHpI . '-',
                'item_popup_' . $sHpI => $sHp . '-item-popup-' . $sHpI,
                'photo_popup_' . $sHpI => $sHp . '-photo-popup-' . $sHpI,

                'main_' . $sHpT => $sHp . '-' . $sHpT,
                'item_' . $sHpT => $sHp . '-item-' . $sHpT . '-',
                'item_popup_' . $sHpT => $sHp . '-item-popup-' . $sHpT,
                'photo_popup_' . $sHpT => $sHp . '-photo-popup-' . $sHpT,

                'main_' . $sHpO => $sHp . '-' . $sHpO,
                'item_' . $sHpO => $sHp . '-item-' . $sHpO . '-',
                'item_popup_' . $sHpO => $sHp . '-item-popup-' . $sHpO,
                'photo_popup_' . $sHpO => $sHp . '-photo-popup-' . $sHpO,

                'main_' . $sHpS => $sHp . '-' . $sHpS,
                'photo_popup_' . $sHpS => $sHp . '-photo-popup-' . $sHpS,

                'main_item' => $sHp . '-item',
                'menu_popup' => $sHp . '-menu-popup-',

                'video_iframe' => $sHp . '-video-iframe-',
                'video' => $sHp . '-video-',

                'live_update_popup' => $sHp . '-live-update-popup-',
            ),
            'post' => array(
                'attach_link_popup' =>  $sHp . '-attach-link-popup',
                'attach_link_form_field' => $sHp . '-attach-link-form_field',
                'attach_link_item' => $sHp . '-attach-link-item-',
                'textarea' => $sHp . '-textarea-',

                'main_' . $sHpT => $sHp . '-' . $sHpT,
                'main_' . $sHpO => $sHp . '-' . $sHpO,
            ),
            'repost' => array(
                'main' => $sHp . '-repost-',
                'counter' => $sHp . '-repost-counter-',
                'by_popup' => $sHp . '-repost-by-',
            )
        );

        $this->_aRepostDefaults = array(
            'show_do_repost_as_button' => false,
            'show_do_repost_as_button_small' => false,
            'show_do_repost_image' => false,
            'show_do_repost_icon' => true,
            'show_do_repost_text' => false,
            'show_counter' => true,

            //--- Images
            'image_do_repost' => '',

            //--- Icons
            'icon_do_repost' => 'sync-alt',

            //--- Texts
            'text_do_repost' => '_bx_timeline_txt_do_repost',

            //--- Templates
            'template_do_repost_label' => '',
            'template_do_repost_label_name' => 'repost_do_repost_label.html',
        );

        $this->_iTimelineVisibilityThreshold = 0;

        $this->_aPregPatterns = array(
            "meta_title" => "/<title>(.*)<\/title>/",
            "meta_description" => "/<meta[\s]+[^>]*?name[\s]?=[\s\"\']+description[\s\"\']+content[\s]?=[\s\"\']+(.*?)[\"\']+.*?>/",
        	"url" => "/(([A-Za-z]{3,9}:(?:\/\/)?)(?:[\-;:&=\+\$,\w]+@)?[A-Za-z0-9\.\-]+|(?:www\.|[\-;:&=\+\$,\w]+@)[A-Za-z0-9\.\-]+)((?:\/[\+~%\/\.\w\-_]*)?\??(?:[\-\+=&;%@\.\w_]*)#?(?:[\.\!\/\\\w]*))?/"
        );
    }

    public function init(&$oDb)
    {
    	parent::init($oDb);

    	$sOptionPrefix = $this->getPrefix('option');
    	$this->_bAllowEdit = getParam($sOptionPrefix . 'enable_edit') == 'on';
        $this->_bAllowDelete = getParam($sOptionPrefix . 'enable_delete') == 'on';
        $this->_bShowAll = getParam($sOptionPrefix . 'enable_show_all') == 'on';

        $this->_aPerPage = array(
            'default' => (int)getParam($sOptionPrefix . 'events_per_page'),
            'profile' => (int)getParam($sOptionPrefix . 'events_per_page_profile'),
            'account' => (int)getParam($sOptionPrefix . 'events_per_page_account'),
            'home' => (int)getParam($sOptionPrefix . 'events_per_page_home')
    	);

        $this->_iRssLength = (int)getParam($sOptionPrefix . 'rss_length');
        $this->_iLiveUpdateLength = 10;
        $this->_iCharsDisplayMaxTitle = 20;

        $this->_sVideosAutoplay = getParam($sOptionPrefix . 'videos_autoplay');

        $this->_iPreloadCommentsMax = 7;
        $this->_iPreloadComments = (int)getParam($sOptionPrefix . 'preload_comments');
        if($this->_iPreloadComments > $this->_iPreloadCommentsMax)
            $this->_iPreloadComments = $this->_iPreloadCommentsMax;

        $this->_bHot = getParam($sOptionPrefix . 'enable_hot') == 'on';
        $this->_iHotInterval = (int)getParam($sOptionPrefix . 'hot_interval');

        $this->_bEditorToolbar = getParam($sOptionPrefix . 'enable_editor_toolbar') == 'on';

        $this->_bUnhideRestored = false;
    }

    public function isAllowEdit()
    {
        return $this->_bAllowEdit;
    }

    public function isAllowDelete()
    {
        return $this->_bAllowDelete;
    }

    public function isShowAll()
    {
        return $this->_bShowAll;
    }

    public function isHot()
    {
        return $this->_bHot;
    }

    public function isEditorToolbar()
    {
    	return $this->_bEditorToolbar;
    }

    public function isUnhideRestored()
    {
        return $this->_bUnhideRestored;
    }

    public function getPostFormDisplay($sType)
    {
        if(empty($sType) || !array_key_exists($sType, $this->_aTypeToFormDisplay))
            $sType = BX_TIMELINE_TYPE_DEFAULT;

        return $this->_aTypeToFormDisplay[$sType];
    }

    public function getUploaders($sType)
    {
        $aResult = array();

        switch($sType) {
            case 'photo':
                $aResult = $this->_aPhotoUploaders;
                break;
			case 'video':
                $aResult = $this->_aVideoUploaders;
                break;
        }

        return $aResult;
    }

    public function getRssLength()
    {
        return $this->_iRssLength;
    }

    public function getLiveUpdateLength()
    {
        return $this->_iLiveUpdateLength;
    }

    public function getCharsDisplayMaxTitle()
    {
        return $this->_iCharsDisplayMaxTitle;
    }

    public function getVideosAutoplay()
    {
        return $this->_sVideosAutoplay;
    }

    public function getPreloadComments()
    {
        return $this->_iPreloadComments;
    }

    public function getHotInterval()
    {
        return $this->_iHotInterval;
    }

    public function getRepostDefaults()
    {
        return $this->_aRepostDefaults;
    }

    public function getPregPattern($sType)
    {
        return $this->_aPregPatterns[$sType];
    }

    /**
     * Ancillary functions
     */
    public function getTitle($s, $mixedProfile = false)
    {
        if($mixedProfile !== false) {
            if(is_numeric($mixedProfile))
                $mixedProfile = BxDolProfile::getInstanceMagic((int)$mixedProfile);

            if($mixedProfile instanceof BxDolProfile)
                $s = bx_replace_markers($s, array(
                    'profile_name' => $mixedProfile->getDisplayName()
                ));
        }

        return strmaxtextlen($s, $this->getCharsDisplayMaxTitle(), '...');
    }

    public function getTitleDefault($bL, $bP, $bV)
    {
        $sResult = '';

        if($bL && !$bP && !$bV)
            $sResult = 'link';
        else if(!$bL && $bP && !$bV)
            $sResult = 'image';
        else if(!$bL && !$bP && $bV)
            $sResult = 'video';
        else 
            $sResult = 'media';

        return $this->CNF['T']['txt_sample_with_' . $sResult];
    }

    public function getViewUrl($iOwnerId)
    {
        return BX_DOL_URL_ROOT . BxDolPermalinks::getInstance()->permalink('page.php', array(
        	'i' => $this->CNF['URI_VIEW_LIST'], 
        	'id' => $iOwnerId
        ));
    }

    public function getHomeViewUrl()
    {
        return BX_DOL_URL_ROOT . BxDolPermalinks::getInstance()->permalink($this->CNF['URL_HOME']);
    }

    public function getItemViewUrl($aEvent)
    {
        return BX_DOL_URL_ROOT . BxDolPermalinks::getInstance()->permalink('page.php', array(
        	'i' => $this->CNF['URI_VIEW_ENTRY'], 
        	'id' => $aEvent['id']
        ));
    }

    public function getLiveUpdateKey($sType, $iOwnerId)
    {
        return $this->getName() . '_live_update_' . $sType . '_' . $iOwnerId;
    }

    public function isCommon($sType, $sAction)
    {
        return !$this->isSystem($sType, $sAction);
    }

    public function isSystem($sType, $sAction)
    {
        $sPrefix = $this->getPrefix('common_post');
        return strpos($sType, $sPrefix) === false && !empty($sAction);
    }

    public function getSystemData(&$aEvent, $aBrowseParams = array())
    {
        $sHandler = $aEvent['type'] . '_' . $aEvent['action'];
        if(!$this->isHandler($sHandler))
            return false;

        $aHandler = $this->getHandlers($sHandler);
        if(empty($aHandler['module_name']) || empty($aHandler['module_class']) || empty($aHandler['module_method']))
            return false; 

        return BxDolService::call($aHandler['module_name'], $aHandler['module_method'], array($aEvent, $aBrowseParams), $aHandler['module_class']);
    }

    public function getSystemDataByDescriptor($sType, $sAction, $iObjectId)
    {
    	$aDescriptor = array(
            'type' => $sType, 
            'action' => $sAction,
            'object_id' => $iObjectId
    	);
    	return $this->getSystemData($aDescriptor);
    }

    public function isEqualUrls($sUrl1, $sUrl2)
    {
        $sUrl1 = trim($sUrl1, "/");
        $sUrl2 = trim($sUrl2, "/");

        return strncmp($sUrl1, $sUrl2, strlen($sUrl1)) === 0;
    }
}

/** @} */
