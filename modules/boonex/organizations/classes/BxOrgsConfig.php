<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Organizations Organizations
 * @ingroup     UnaModules
 *
 * @{
 */

class BxOrgsConfig extends BxBaseModProfileConfig
{
    function __construct($aModule)
    {
        parent::__construct($aModule);

        $this->_aMenuItems2MethodsSubmenu = array_merge($this->_aMenuItems2MethodsSubmenu, array(
            'organization-profile-subscriptions' => 'checkAllowedSubscriptionsView'
        ));

        $this->_aMenuItems2MethodsActions = array_merge($this->_aMenuItems2MethodsActions, array(
            'view-organization-profile' => 'checkAllowedView',
            'edit-organization-profile' => 'checkAllowedEdit',
            'edit-organization-cover' => 'checkAllowedChangeCover',
            'invite-to-organization' => 'checkAllowedInvite',
            'delete-organization-profile' => 'checkAllowedDelete',
            'profile-fan-add' => 'checkAllowedFanAdd',
            'profile-fan-remove' => 'checkAllowedFanRemove',
        ));

        $this->CNF = array (

            // module icon
            'ICON' => 'briefcase col-red2',

            // database tables
            'TABLE_ENTRIES' => $aModule['db_prefix'] . 'data',
            'TABLE_ENTRIES_FULLTEXT' => 'search_fields',
            'TABLE_ADMINS' => $aModule['db_prefix'] . 'admins',

            // database fields
            'FIELD_ID' => 'id',
            'FIELD_AUTHOR' => 'author',
            'FIELD_ADDED' => 'added',
            'FIELD_CHANGED' => 'changed',
            'FIELD_NAME' => 'org_name',
            'FIELD_TITLE' => 'org_name',
            'FIELD_TEXT' => 'org_desc',
            'FIELD_PICTURE' => 'picture',
            'FIELD_COVER' => 'cover',
            'FIELD_JOIN_CONFIRMATION' => 'join_confirmation',
            'FIELD_ALLOW_VIEW_TO' => 'allow_view_to',
            'FIELD_VIEWS' => 'views',
            'FIELD_COMMENTS' => 'comments',
            'FIELDS_QUICK_SEARCH' => array('org_name'),
            'FIELD_LOCATION_PREFIX' => 'location',
            'FIELD_LABELS' => 'labels',
            'FIELDS_WITH_KEYWORDS' => 'auto', // can be 'auto', array of fields or comma separated string of field names, works only when OBJECT_METATAGS is specified

            // page URIs
            'URI_VIEW_ENTRY' => 'view-organization-profile',
            'URI_VIEW_FRIENDS' => 'organization-profile-friends',
            'URI_VIEW_FRIEND_REQUESTS' => 'organization-friend-requests',
            'URI_VIEW_FAVORITES' => 'organization-profile-favorites',
            'URI_EDIT_ENTRY' => 'edit-organization-profile',
            'URI_EDIT_COVER' => 'edit-organization-cover',
            'URI_JOINED_ENTRIES' => 'joined-organizations',
            'URI_MANAGE_COMMON' => 'organizations-manage',

            'URL_HOME' => 'page.php?i=organizations-home',
            'URL_CREATE' => 'page.php?i=create-organization-profile',
            'URL_ENTRY_FANS' => 'page.php?i=organization-profile-fans',
            'URL_MANAGE_COMMON' => 'page.php?i=organizations-manage',
            'URL_MANAGE_ADMINISTRATION' => 'page.php?i=organizations-administration',

            // some params
            'PARAM_AUTOAPPROVAL' => 'bx_organizations_autoapproval',
            'PARAM_ENABLE_ACTIVATION_LETTER' => 'bx_organizations_enable_profile_activation_letter',
            'PARAM_DEFAULT_ACL_LEVEL' => 'bx_organizations_default_acl_level',
            'PARAM_NUM_RSS' => 'bx_organizations_num_rss',
            'PARAM_NUM_CONNECTIONS_QUICK' => 'bx_organizations_num_connections_quick',
            'PARAM_SEARCHABLE_FIELDS' => 'bx_organizations_searchable_fields',
            'PARAM_PER_PAGE_BROWSE_SHOWCASE' => 'bx_organizations_per_page_browse_showcase',
            'PARAM_PER_PAGE_BROWSE_RECOMMENDED' => 'bx_organizations_per_page_browse_recommended',
            'PARAM_PUBLIC_SBSN' => 'bx_organizations_public_subscriptions',
            'PARAM_PUBLIC_SBSD' => 'bx_organizations_public_subscribed_me',
            'PARAM_REDIRECT_AADD' => 'bx_organizations_redirect_aadd',
            'PARAM_REDIRECT_AADD_CUSTOM_URL' => 'bx_organizations_redirect_aadd_custom_url',
            'PARAM_LABELS' => 'bx_organizations_labels',

            // objects
            'OBJECT_STORAGE' => 'bx_organizations_pics',
            'OBJECT_STORAGE_COVER' => 'bx_organizations_pics',
            'OBJECT_IMAGES_TRANSCODER_THUMB' => 'bx_organizations_thumb',
            'OBJECT_IMAGES_TRANSCODER_ICON' => 'bx_organizations_icon',
            'OBJECT_IMAGES_TRANSCODER_AVATAR' => 'bx_organizations_avatar',
            'OBJECT_IMAGES_TRANSCODER_PICTURE' => 'bx_organizations_picture',
            'OBJECT_IMAGES_TRANSCODER_COVER' => 'bx_organizations_cover',
            'OBJECT_IMAGES_TRANSCODER_COVER_THUMB' => 'bx_organizations_cover_thumb',
            'OBJECT_IMAGES_TRANSCODER_GALLERY' => 'bx_organizations_gallery',
            'OBJECT_VIEWS' => 'bx_organizations',
            'OBJECT_VOTES' => 'bx_organizations',
            'OBJECT_SCORES' => 'bx_organizations',
            'OBJECT_FAVORITES' => 'bx_organizations',
            'OBJECT_FEATURED' => 'bx_organizations',
            'OBJECT_COMMENTS' => 'bx_organizations',
            'OBJECT_REPORTS' => 'bx_organizations',
            'OBJECT_METATAGS' => 'bx_organizations',
            'OBJECT_FORM_ENTRY' => 'bx_organization',
            'OBJECT_FORM_ENTRY_DISPLAY_VIEW' => 'bx_organization_view',
            'OBJECT_FORM_ENTRY_DISPLAY_VIEW_FULL' => 'bx_organization_view_full', // for "info" tab on view profile page
            'OBJECT_FORM_ENTRY_DISPLAY_ADD' => 'bx_organization_add',
            'OBJECT_FORM_ENTRY_DISPLAY_EDIT' => 'bx_organization_edit',
            'OBJECT_FORM_ENTRY_DISPLAY_EDIT_COVER' => 'bx_organization_edit_cover',
            'OBJECT_FORM_ENTRY_DISPLAY_DELETE' => 'bx_organization_delete',
            'OBJECT_FORM_ENTRY_DISPLAY_INVITE' => 'bx_organization_invite',
            'OBJECT_MENU_ACTIONS_VIEW_ENTRY' => 'bx_organizations_view_actions', // actions menu on view entry page
            'OBJECT_MENU_ACTIONS_VIEW_ENTRY_MORE' => 'bx_organizations_view_actions_more', // actions menu on view entry page for "more" popup
            'OBJECT_MENU_ACTIONS_VIEW_ENTRY_ALL' => 'bx_organizations_view_actions_all', // all actions menu on view entry page
            'OBJECT_MENU_ACTIONS_MY_ENTRIES' => 'bx_organizations_my', // actions menu on profile entries page
            'OBJECT_MENU_SUBMENU' => 'bx_organizations_submenu', // main module submenu
            'OBJECT_MENU_SUBMENU_VIEW_ENTRY' => 'bx_organizations_view_submenu',  // view entry submenu
            'OBJECT_MENU_SUBMENU_VIEW_ENTRY_COVER' => 'bx_organizations_view_submenu_cover',  // view entry submenu displayed in cover
            'OBJECT_MENU_SUBMENU_VIEW_ENTRY_MAIN_SELECTION' => 'organizations-home', // first item in view entry submenu from main module submenu
            'OBJECT_MENU_SNIPPET_META' => 'bx_organizations_snippet_meta', // menu for snippet meta info
            'OBJECT_MENU_MANAGE_TOOLS' => 'bx_organizations_menu_manage_tools', //manage menu in content administration tools
            'OBJECT_PAGE_VIEW_ENTRY' => 'bx_organizations_view_profile',
            'OBJECT_PAGE_VIEW_ENTRY_CLOSED' => 'bx_organizations_view_profile_closed',
            'OBJECT_PRIVACY_VIEW' => 'bx_organizations_allow_view_to',
            'OBJECT_PRIVACY_VIEW_NOTIFICATION_EVENT' => 'bx_organizations_allow_view_notification_to',
            'OBJECT_GRID_ADMINISTRATION' => 'bx_organizations_administration',
            'OBJECT_GRID_COMMON' => 'bx_organizations_common',
            'OBJECT_GRID_CONNECTIONS' => 'bx_organizations_fans',
            'OBJECT_CONNECTIONS' => 'bx_organizations_fans',
            'OBJECT_UPLOADERS_COVER' => array('bx_organizations_cover_crop'),
            'OBJECT_UPLOADERS_PICTURE' => array('bx_organizations_picture_crop'),

            'EMAIL_FRIEND_REQUEST' => 'bx_organizations_friend_request',
            'EMAIL_INVITATION' => 'bx_organizations_invitation',
            'EMAIL_JOIN_REQUEST' => 'bx_organizations_join_request',
            'EMAIL_JOIN_CONFIRM' => 'bx_organizations_join_confirm',
            'EMAIL_FAN_BECOME_ADMIN' => 'bx_organizations_fan_become_admin',
            'EMAIL_ADMIN_BECOME_FAN' => 'bx_organizations_admin_become_fan',
            'EMAIL_FAN_REMOVE' => 'bx_organizations_fan_remove',
            'EMAIL_JOIN_REJECT' => 'bx_organizations_join_reject',

            'TRIGGER_MENU_PROFILE_VIEW_SUBMENU' => 'trigger_profile_view_submenu',
            'TRIGGER_MENU_PROFILE_SNIPPET_META' => 'trigger_profile_snippet_meta',
            'TRIGGER_MENU_PROFILE_VIEW_ACTIONS' => 'trigger_profile_view_actions',
            'TRIGGER_PAGE_VIEW_ENTRY' => 'trigger_page_profile_view_entry',

            // menu items which visibility depends on custom visibility checking
            'MENU_ITEM_TO_METHOD' => array (
                'bx_organizations_view_submenu' => $this->_aMenuItems2MethodsSubmenu,
                'bx_organizations_view_actions' => $this->_aMenuItems2MethodsActions,
                'bx_organizations_view_actions_more' => $this->_aMenuItems2MethodsActions,
                'bx_organizations_view_actions_all' => $this->_aMenuItems2MethodsActions,
            ),

            // informer messages
            'INFORMERS' => array (
                'status' => array (
                    'name' => 'bx-organizations-status-not-active',
                    'map' => array (
                        BX_PROFILE_STATUS_PENDING => '_bx_orgs_txt_account_pending',
                        BX_PROFILE_STATUS_SUSPENDED => '_bx_orgs_txt_account_suspended',
                    ),
                ),
                'status_moderation' => array (
                    'name' => 'bx-organizations-status-not-active-moderation',
                    'map' => array (
                        BX_PROFILE_STATUS_PENDING => '_bx_orgs_txt_account_pending_moderation',
                        BX_PROFILE_STATUS_SUSPENDED => '_bx_orgs_txt_account_suspended_moderation',
                    ),
                ),
            ),

            // some language keys
            'T' => array (
                'txt_sample_single' => '_bx_orgs_txt_sample_single',
            	'txt_sample_comment_single' => '_bx_orgs_txt_sample_comment_single',
                'txt_sample_vote_single' => '_bx_orgs_txt_sample_vote_single',
            	'txt_sample_pp_single' => '_bx_orgs_txt_sample_pp_single',
            	'txt_sample_pp_single_with_article' => '_bx_orgs_txt_sample_pp_single_with_article',
                'txt_sample_pc_single' => '_bx_orgs_txt_sample_pc_single',
            	'txt_sample_pc_single_with_article' => '_bx_orgs_txt_sample_pc_single_with_article',
            	'txt_sample_pi_action' => '_bx_orgs_txt_sample_pi_action',
            	'txt_sample_pi_action_user' => '_bx_orgs_txt_sample_pi_action_user',
            	'txt_private_group' => '_bx_orgs_txt_private_organization',
                'txt_N_fans' => '_bx_orgs_txt_N_friends',
            	'txt_ntfs_join_request' => '_bx_orgs_txt_ntfs_join_request',
                'txt_ntfs_fan_added' => '_bx_orgs_txt_ntfs_fan_added',
            	'txt_ntfs_timeline_post_common' => '_bx_orgs_txt_ntfs_timeline_post_common',
            	'txt_all_entries_by_author' => '_bx_orgs_page_title_browse_by_author',
            	'form_field_picture' => '_bx_orgs_form_profile_input_picture_search',
                'form_field_online' => '_bx_orgs_form_profile_input_online_search',
                'menu_item_title_befriend_sent' => '_bx_orgs_menu_item_title_befriend_sent',
                'menu_item_title_unfriend_cancel_request' => '_bx_orgs_menu_item_title_unfriend_cancel_request',
                'menu_item_title_befriend_confirm' => '_bx_orgs_menu_item_title_befriend_confirm',
                'menu_item_title_unfriend_reject_request' => '_bx_orgs_menu_item_title_unfriend_reject_request',
                'menu_item_title_befriend' => '_bx_orgs_menu_item_title_befriend',
                'menu_item_title_unfriend' => '_bx_orgs_menu_item_title_unfriend',
            	'menu_item_title_become_fan_sent' => '_bx_orgs_menu_item_title_become_fan_sent',
                'menu_item_title_leave_group_cancel_request' => '_bx_orgs_menu_item_title_leave_organization_cancel_request',
                'menu_item_title_become_fan' => '_bx_orgs_menu_item_title_become_fan',
                'menu_item_title_leave_group' => '_bx_orgs_menu_item_title_leave_organization',
            	'menu_item_manage_my' => '_bx_orgs_menu_item_title_manage_my',
            	'menu_item_manage_all' => '_bx_orgs_menu_item_title_manage_all',
            	'grid_action_err_delete' => '_bx_orgs_grid_action_err_delete',
            	'grid_txt_account_manager' => '_bx_orgs_grid_txt_account_manager',
				'filter_item_active' => '_bx_orgs_grid_filter_item_title_adm_active',
            	'filter_item_pending' => '_bx_orgs_grid_filter_item_title_adm_pending',
            	'filter_item_suspended' => '_bx_orgs_grid_filter_item_title_adm_suspended',
                'filter_item_unconfirmed' => '_bx_orgs_grid_filter_item_title_adm_unconfirmed',
            	'filter_item_select_one_filter1' => '_bx_orgs_grid_filter_item_title_adm_select_one_filter1',
				'filter_item_select_one_filter2' => '_bx_orgs_grid_filter_item_title_adm_select_one_filter2',
            	'txt_browse_favorites' => '_bx_orgs_page_title_browse_favorites',
            	'option_redirect_aadd_profile' => '_bx_orgs_option_redirect_aadd_profile',
            	'option_redirect_aadd_last' => '_bx_orgs_option_redirect_aadd_last',
            	'option_redirect_aadd_custom' => '_bx_orgs_option_redirect_aadd_custom'
            ),
        );

        $this->_aJsClasses = array(
        	'manage_tools' => 'BxOrgsManageTools'
        );

        $this->_aJsObjects = array(
        	'manage_tools' => 'oBxOrgsManageTools'
        );

        $this->_aGridObjects = array(
        	'common' => $this->CNF['OBJECT_GRID_COMMON'],
        	'administration' => $this->CNF['OBJECT_GRID_ADMINISTRATION'],
        	
        );
    }

}

/** @} */
