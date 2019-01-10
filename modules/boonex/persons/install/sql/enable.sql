
-- SETTINGS

SET @iTypeOrder = (SELECT MAX(`order`) FROM `sys_options_types` WHERE `group` = 'modules');
INSERT INTO `sys_options_types`(`group`, `name`, `caption`, `icon`, `order`) VALUES 
('modules', 'bx_persons', '_bx_persons', 'bx_persons@modules/boonex/persons/|std-icon.svg', IF(ISNULL(@iTypeOrder), 1, @iTypeOrder + 1));
SET @iTypeId = LAST_INSERT_ID();

INSERT INTO `sys_options_categories` (`type_id`, `name`, `caption`, `order`)
VALUES (@iTypeId, 'bx_persons', '_bx_persons', 1);
SET @iCategId = LAST_INSERT_ID();

INSERT INTO `sys_options` (`name`, `value`, `category_id`, `caption`, `type`, `extra`, `check`, `check_error`, `order`) VALUES
('bx_persons_autoapproval', 'on', @iCategId, '_bx_persons_option_autoapproval', 'checkbox', '', '', '', 1),
('bx_persons_default_acl_level', '3', @iCategId, '_bx_persons_option_default_acl_level', 'select', 'a:4:{s:6:"module";s:6:"system";s:6:"method";s:15:"get_memberships";s:6:"params";a:4:{s:11:"purchasable";b:0;s:6:"active";b:1;s:9:"translate";b:1;s:22:"filter_out_auto_levels";b:1;}s:5:"class";s:16:"TemplAclServices";}', '', '', 2),
('bx_persons_redirect_aadd', 'profile', @iCategId, '_bx_persons_option_redirect_aadd', 'select', 'a:2:{s:6:"module";s:10:"bx_persons";s:6:"method";s:30:"get_options_redirect_after_add";}', '', '', 3),
('bx_persons_redirect_aadd_custom_url', '', @iCategId, '_bx_persons_option_redirect_aadd_custom_url', 'digit', '', '', '', 4),
('bx_persons_num_connections_quick', '6', @iCategId, '_bx_persons_option_num_connections_quick', 'digit', '', '', '', 10),
('bx_persons_per_page_browse', '24', @iCategId, '_bx_persons_option_per_page_browse', 'digit', '', '', '', 11),
('bx_persons_num_rss', '10', @iCategId, '_bx_persons_option_num_rss', 'digit', '', '', '', 12),
('bx_persons_per_page_browse_showcase', '32', @iCategId, '_sys_option_per_page_browse_showcase', 'digit', '', '', '', 15),
('bx_persons_per_page_browse_recommended', '10', @iCategId, '_sys_option_per_page_browse_recommended', 'digit', '', '', '', 16),
('bx_persons_searchable_fields', 'fullname,description', @iCategId, '_bx_persons_option_searchable_fields', 'list', 'a:2:{s:6:"module";s:10:"bx_persons";s:6:"method";s:21:"get_searchable_fields";}', '', '', 20),
('bx_persons_public_subscriptions', '', @iCategId, '_bx_persons_option_public_subscriptions', 'checkbox', '', '', '', 30),
('bx_persons_public_subscribed_me', '', @iCategId, '_bx_persons_option_public_subscribed_me', 'checkbox', '', '', '', 31),
('bx_persons_enable_profile_activation_letter', 'on', @iCategId, '_bx_persons_option_enable_profile_activation_letter', 'checkbox', '', '', '', 32),
('bx_persons_labels', '', @iCategId, '_sys_option_labels', 'text', '', '', '', 40);

-- PAGES

-- PAGE: create profile

INSERT INTO `sys_objects_page`(`object`, `uri`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_persons_create_profile', 'create-persons-profile', '_bx_persons_page_title_sys_create_profile', '_bx_persons_page_title_create_profile', 'bx_persons', 5, 2147483647, 1, 'page.php?i=create-persons-profile', '', '', '', 0, 1, 0, '', '');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES 
('bx_persons_create_profile', 1, 'bx_persons', '_bx_persons_page_block_title_choose_type', 11, 2147483647, 'menu', 'sys_add_profile_vertical', 0, 1, 0, 0),
('bx_persons_create_profile', 1, 'bx_persons', '_bx_persons_page_block_title_create_profile', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:10:\"bx_persons\";s:6:\"method\";s:13:\"entity_create\";}', 0, 1, 1, 1);

-- PAGE: view profile
INSERT INTO `sys_objects_page`(`object`, `uri`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_persons_view_profile', 'view-persons-profile', '_bx_persons_page_title_sys_view_profile', '_bx_persons_page_title_view_profile', 'bx_persons', 7, 2147483647, 1, 'page.php?i=view-persons-profile', '', '', '', 0, 1, 0, 'BxPersonsPageEntry', 'modules/boonex/persons/classes/BxPersonsPageEntry.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES
('bx_persons_view_profile', 2, 'bx_persons', '', '_bx_persons_page_block_title_profile_subscriptions', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:10:\"bx_persons\";s:6:\"method\";s:21:\"profile_subscriptions\";}', 0, 1, 0, 0),
('bx_persons_view_profile', 2, 'bx_persons', '', '_bx_persons_page_block_title_profile_subscribed_me', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:10:\"bx_persons\";s:6:\"method\";s:21:\"profile_subscribed_me\";}', 0, 1, 0, 0),
('bx_persons_view_profile', 2, 'bx_persons', '', '_bx_persons_page_block_title_profile_comments', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:10:\"bx_persons\";s:6:\"method\";s:15:\"entity_comments\";}', 0, 0, 0, 0),
('bx_persons_view_profile', 2, 'bx_persons', '', '_bx_persons_page_block_title_profile_description', 13, 2147483647, 'service', 'a:2:{s:6:\"module\";s:10:\"bx_persons\";s:6:\"method\";s:17:\"entity_text_block\";}', 0, 0, 1, 1),
('bx_persons_view_profile', 2, 'bx_persons', '', '_bx_persons_page_block_title_profile_friends', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:10:\"bx_persons\";s:6:\"method\";s:15:\"profile_friends\";}', 0, 1, 1, 2),
('bx_persons_view_profile', 3, 'bx_persons', '', '_bx_persons_page_block_title_profile_location', 13, 2147483647, 'service', 'a:2:{s:6:\"module\";s:10:\"bx_persons\";s:6:\"method\";s:15:\"entity_location\";}', 0, 0, 0, 0),
('bx_persons_view_profile', 3, 'bx_persons', '', '_bx_persons_page_block_title_profile_info', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:10:\"bx_persons\";s:6:\"method\";s:11:\"entity_info\";}', 0, 0, 1, 1),
('bx_persons_view_profile', 3, 'bx_persons', '', '_bx_persons_page_block_title_profile_all_actions', 13, 2147483647, 'service', 'a:2:{s:6:"module";s:10:"bx_persons";s:6:"method";s:18:"entity_all_actions";}', 0, 0, 0, 0),
('bx_persons_view_profile', 3, 'bx_persons', '', '_bx_persons_page_block_title_profile_membership', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:10:\"bx_persons\";s:6:\"method\";s:18:\"profile_membership\";}', 0, 0, 1, 2),
('bx_persons_view_profile', 3, 'bx_persons', '', '_bx_persons_page_block_title_profile_location', 3, 2147483647, 'service', 'a:4:{s:6:\"module\";s:6:\"system\";s:6:\"method\";s:13:\"locations_map\";s:6:\"params\";a:2:{i:0;s:10:\"bx_persons\";i:1;s:12:\"{content_id}\";}s:5:\"class\";s:20:\"TemplServiceMetatags\";}', 0, 0, 1, 3);

-- PAGE: view closed profile 
INSERT INTO `sys_objects_page`(`object`, `uri`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_persons_view_profile_closed', 'view-person-profile-closed', '_bx_persons_page_title_sys_view_profile_closed', '_bx_persons_page_title_view_profile', 'bx_persons', 10, 2147483647, 1, 'page.php?i=view-person-profile', '', '', '', 0, 1, 0, 'BxPersonsPageEntry', 'modules/boonex/persons/classes/BxPersonsPageEntry.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES 
('bx_persons_view_profile_closed', 1, 'bx_persons', '', '_bx_persons_page_block_title_profile_private', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:10:\"bx_persons\";s:6:\"method\";s:19:\"private_profile_msg\";}', 0, 1, 1, 0);

-- PAGE: edit profile
INSERT INTO `sys_objects_page`(`object`, `uri`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_persons_edit_profile', 'edit-persons-profile', '_bx_persons_page_title_sys_edit_profile', '_bx_persons_page_title_edit_profile', 'bx_persons', 5, 2147483647, 1, 'page.php?i=edit-persons-profile', '', '', '', 0, 1, 0, 'BxPersonsPageEntry', 'modules/boonex/persons/classes/BxPersonsPageEntry.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES 
('bx_persons_edit_profile', 1, 'bx_persons', '_bx_persons_page_block_title_edit_profile', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:10:\"bx_persons\";s:6:\"method\";s:11:\"entity_edit\";}', 0, 0, 0);

-- PAGE: edit profile cover
INSERT INTO `sys_objects_page`(`object`, `uri`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_persons_edit_profile_cover', 'edit-persons-cover', '_bx_persons_page_title_sys_edit_profile_cover', '_bx_persons_page_title_edit_profile_cover', 'bx_persons', 5, 2147483647, 1, 'page.php?i=edit-persons-cover', '', '', '', 0, 1, 0, 'BxPersonsPageEntry', 'modules/boonex/persons/classes/BxPersonsPageEntry.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES 
('bx_persons_edit_profile_cover', 1, 'bx_persons', '_bx_persons_page_block_title_edit_profile_cover', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:10:\"bx_persons\";s:6:\"method\";s:17:\"entity_edit_cover\";}', 0, 0, 0);

-- PAGE: delete profile
INSERT INTO `sys_objects_page`(`object`, `uri`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_persons_delete_profile', 'delete-persons-profile', '_bx_persons_page_title_sys_delete_profile', '_bx_persons_page_title_delete_profile', 'bx_persons', 5, 2147483647, 1, 'page.php?i=delete-persons-profile', '', '', '', 0, 1, 0, 'BxPersonsPageEntry', 'modules/boonex/persons/classes/BxPersonsPageEntry.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES 
('bx_persons_delete_profile', 1, 'bx_persons', '_bx_persons_page_block_title_delete_profile', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:10:\"bx_persons\";s:6:\"method\";s:13:\"entity_delete\";}', 0, 0, 0);

-- PAGE: profile info
INSERT INTO `sys_objects_page`(`object`, `uri`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_persons_profile_info', 'persons-profile-info', '_bx_persons_page_title_sys_profile_info', '_bx_persons_page_title_profile_info', 'bx_persons', 5, 2147483647, 1, 'page.php?i=persons-profile-info', '', '', '', 0, 1, 0, 'BxPersonsPageEntry', 'modules/boonex/persons/classes/BxPersonsPageEntry.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES 
('bx_persons_profile_info', 1, 'bx_persons', '_bx_persons_page_block_title_system_profile_info', '_bx_persons_page_block_title_profile_info_link', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:10:\"bx_persons\";s:6:\"method\";s:16:\"entity_info_full\";}', 0, 0, 1, 1),
('bx_persons_profile_info', 1, 'bx_persons', '', '_bx_persons_page_block_title_profile_description', 13, 2147483647, 'service', 'a:2:{s:6:\"module\";s:10:\"bx_persons\";s:6:\"method\";s:17:\"entity_text_block\";}', 0, 0, 1, 2);

-- PAGE: profile friends
INSERT INTO `sys_objects_page`(`object`, `uri`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_persons_profile_friends', 'persons-profile-friends', '_bx_persons_page_title_sys_profile_friends', '_bx_persons_page_title_profile_friends', 'bx_persons', 5, 2147483647, 1, 'page.php?i=persons-profile-friends', '', '', '', 0, 1, 0, 'BxPersonsPageEntry', 'modules/boonex/persons/classes/BxPersonsPageEntry.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES 
('bx_persons_profile_friends', 1, 'bx_persons', '_bx_persons_page_block_title_system_profile_friends', '_bx_persons_page_block_title_profile_friends_link', 11, 2147483647, 'service', 'a:3:{s:6:\"module\";s:6:\"system\";s:6:\"method\";s:17:\"connections_table\";s:5:\"class\";s:23:\"TemplServiceConnections\";}', 0, 0, 1, 1);

-- PAGE: profile friend requests
INSERT INTO `sys_objects_page`(`object`, `uri`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_persons_friend_requests', 'persons-friend-requests', '_bx_persons_page_title_sys_friend_requests', '_bx_persons_page_title_friend_requests', 'bx_persons', 5, 2147483647, 1, 'page.php?i=persons-friend-requests', '', '', '', 0, 1, 0, 'BxPersonsPageEntry', 'modules/boonex/persons/classes/BxPersonsPageEntry.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES 
('bx_persons_friend_requests', 1, 'bx_persons', '_bx_persons_page_block_title_system_friend_requests', '_bx_persons_page_block_title_friend_requests_link', 11, 2147483647, 'service', 'a:3:{s:6:\"module\";s:6:\"system\";s:6:\"method\";s:25:\"connections_request_table\";s:5:\"class\";s:23:\"TemplServiceConnections\";}', 0, 0, 1, 1);

-- PAGE: profile favorites
INSERT INTO `sys_objects_page`(`object`, `uri`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_persons_profile_favorites', 'persons-profile-favorites', '_bx_persons_page_title_sys_profile_favorites', '_bx_persons_page_title_profile_favorites', 'bx_persons', 5, 2147483647, 1, 'page.php?i=persons-profile-favorites', '', '', '', 0, 1, 0, 'BxPersonsPageBrowse', 'modules/boonex/persons/classes/BxPersonsPageBrowse.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES 
('bx_persons_profile_favorites', 1, 'bx_persons', '_bx_persons_page_block_title_system_profile_favorites', '_bx_persons_page_block_title_profile_favorites', 11, 2147483647, 'service', 'a:2:{s:6:"module";s:10:"bx_persons";s:6:"method";s:15:"browse_favorite";}', 0, 1, 1, 1);

-- PAGE: profile subscriptions
INSERT INTO `sys_objects_page`(`object`, `uri`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_persons_profile_subscriptions', 'persons-profile-subscriptions', '_bx_persons_page_title_sys_profile_subscriptions', '_bx_persons_page_title_profile_subscriptions', 'bx_persons', 5, 2147483647, 1, 'page.php?i=persons-profile-subscriptions', '', '', '', 0, 1, 0, 'BxPersonsPageEntry', 'modules/boonex/persons/classes/BxPersonsPageEntry.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES 
('bx_persons_profile_subscriptions', 1, 'bx_persons', '_bx_persons_page_block_title_system_profile_subscriptions', '_bx_persons_page_block_title_profile_subscriptions', 11, 2147483647, 'service', 'a:3:{s:6:\"module\";s:6:\"system\";s:6:\"method\";s:19:\"subscriptions_table\";s:5:\"class\";s:23:\"TemplServiceConnections\";}', 0, 0, 1, 1),
('bx_persons_profile_subscriptions', 1, 'bx_persons', '_bx_persons_page_block_title_system_profile_subscribed_me', '_bx_persons_page_block_title_profile_subscribed_me', 11, 2147483647, 'service', 'a:3:{s:6:\"module\";s:6:\"system\";s:6:\"method\";s:19:\"subscribed_me_table\";s:5:\"class\";s:23:\"TemplServiceConnections\";}', 0, 0, 1, 2);

-- PAGE: view entry comments
INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_persons_profile_comments', '_bx_persons_page_title_sys_profile_comments', '_bx_persons_page_title_profile_comments', 'bx_persons', 5, 2147483647, 1, 'persons-profile-comments', '', '', '', '', 0, 1, 0, 'BxPersonsPageEntry', 'modules/boonex/persons/classes/BxPersonsPageEntry.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES 
('bx_persons_profile_comments', 1, 'bx_persons', '_bx_persons_page_block_title_profile_comments', '_bx_persons_page_block_title_profile_comments_link', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:10:\"bx_persons\";s:6:\"method\";s:15:\"entity_comments\";}', 0, 0, 0, 1);


-- PAGE: module home
INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_persons_home', '_bx_persons_page_title_sys_recent', '_bx_persons_page_title_recent', 'bx_persons', 5, 2147483647, 1, 'persons-home', 'page.php?i=persons-home', '', '', '', 0, 1, 0, 'BxPersonsPageBrowse', 'modules/boonex/persons/classes/BxPersonsPageBrowse.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES
('bx_persons_home', 1, 'bx_persons', '_bx_persons_page_block_title_featured_profiles', 11, 2147483647, 'service', 'a:3:{s:6:"module";s:10:"bx_persons";s:6:"method";s:15:"browse_featured";s:6:"params";a:1:{i:0;s:7:"gallery";}}', 0, 1, 0),
('bx_persons_home', 1, 'bx_persons', '_bx_persons_page_block_title_latest_profiles', 11, 2147483647, 'service', 'a:3:{s:6:\"module\";s:10:\"bx_persons\";s:6:\"method\";s:22:\"browse_recent_profiles\";s:6:"params";a:1:{i:0;b:1;}}', 0, 1, 1);

-- PAGE: active profiles

INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_persons_active', '_bx_persons_page_title_sys_active', '_bx_persons_page_title_active', 'bx_persons', 5, 2147483647, 1, 'persons-active', 'page.php?i=persons-active', '', '', '', 0, 1, 0, 'BxPersonsPageBrowse', 'modules/boonex/persons/classes/BxPersonsPageBrowse.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES 
('bx_persons_active', 1, 'bx_persons', '_bx_persons_page_block_title_active_profiles', 11, 2147483647, 'service', 'a:3:{s:6:"module";s:10:"bx_persons";s:6:"method";s:22:"browse_active_profiles";s:6:"params";a:2:{s:9:"unit_view";s:7:"gallery";s:13:"empty_message";b:1;}}', 0, 1, 0);

-- PAGE: online profiles
INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_persons_online', '_bx_persons_page_title_sys_online', '_bx_persons_page_title_online', 'bx_persons', 5, 2147483647, 1, 'persons-online', 'page.php?i=persons-online', '', '', '', 0, 1, 0, 'BxPersonsPageBrowse', 'modules/boonex/persons/classes/BxPersonsPageBrowse.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES 
('bx_persons_online', 1, 'bx_persons', '_bx_persons_page_block_title_online_profiles', 11, 2147483647, 'service', 'a:3:{s:6:\"module\";s:10:\"bx_persons\";s:6:\"method\";s:22:\"browse_online_profiles\";s:6:"params";a:1:{i:0;b:1;}}', 0, 1, 0);

-- PAGE: search for entries
INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_persons_search', '_bx_persons_page_title_sys_entries_search', '_bx_persons_page_title_entries_search', 'bx_persons', 5, 2147483647, 1, 'persons-search', 'page.php?i=persons-search', '', '', '', 0, 1, 0, 'BxPersonsPageBrowse', 'modules/boonex/persons/classes/BxPersonsPageBrowse.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES 
('bx_persons_search', 1, 'bx_persons', '_bx_persons_page_block_title_search_form', 11, 2147483647, 'service', 'a:4:{s:6:"module";s:6:"system";s:6:"method";s:8:"get_form";s:6:"params";a:1:{i:0;a:1:{s:6:"object";s:10:"bx_persons";}}s:5:"class";s:27:"TemplSearchExtendedServices";}', 0, 1, 1, 1),
('bx_persons_search', 1, 'bx_persons', '_bx_persons_page_block_title_search_results', 11, 2147483647, 'service', 'a:4:{s:6:"module";s:6:"system";s:6:"method";s:11:"get_results";s:6:"params";a:1:{i:0;a:2:{s:6:"object";s:10:"bx_persons";s:10:"show_empty";b:1;}}s:5:"class";s:27:"TemplSearchExtendedServices";}', 0, 1, 1, 2),
('bx_persons_search', 1, 'bx_persons', '_bx_persons_page_block_title_search_form_cmts', 11, 2147483647, 'service', 'a:4:{s:6:"module";s:6:"system";s:6:"method";s:8:"get_form";s:6:"params";a:1:{i:0;a:1:{s:6:"object";s:15:"bx_persons_cmts";}}s:5:"class";s:27:"TemplSearchExtendedServices";}', 0, 1, 0, 3),
('bx_persons_search', 1, 'bx_persons', '_bx_persons_page_block_title_search_results_cmts', 11, 2147483647, 'service', 'a:4:{s:6:"module";s:6:"system";s:6:"method";s:11:"get_results";s:6:"params";a:1:{i:0;a:2:{s:6:"object";s:15:"bx_persons_cmts";s:10:"show_empty";b:1;}}s:5:"class";s:27:"TemplSearchExtendedServices";}', 0, 1, 0, 4);

-- PAGE: module manage own
INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_persons_manage', '_bx_persons_page_title_sys_manage', '_bx_persons_page_title_manage', 'bx_persons', 5, 2147483647, 1, 'persons-manage', 'page.php?i=persons-manage', '', '', '', 0, 1, 0, 'BxPersonsPageBrowse', 'modules/boonex/persons/classes/BxPersonsPageBrowse.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES 
('bx_persons_manage', 1, 'bx_persons', '_bx_persons_page_block_title_system_manage', '_bx_persons_page_block_title_manage', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:10:\"bx_persons\";s:6:\"method\";s:12:\"manage_tools\";}}', 0, 1, 0);

-- PAGE: module manage all
INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_persons_administration', '_bx_persons_page_title_sys_manage_administration', '_bx_persons_page_title_manage', 'bx_persons', 5, 192, 1, 'persons-administration', 'page.php?i=persons-administration', '', '', '', 0, 1, 0, 'BxPersonsPageBrowse', 'modules/boonex/persons/classes/BxPersonsPageBrowse.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES 
('bx_persons_administration', 1, 'bx_persons', '_bx_persons_page_block_title_system_manage_administration', '_bx_persons_page_block_title_manage', 11, 192, 'service', 'a:3:{s:6:\"module\";s:10:\"bx_persons\";s:6:\"method\";s:12:\"manage_tools\";s:6:\"params\";a:1:{i:0;s:14:\"administration\";}}', 0, 1, 0);

-- PAGE: add block to homepage

SET @iBlockOrder = (SELECT `order` FROM `sys_pages_blocks` WHERE `object` = 'sys_home' AND `cell_id` = 1 ORDER BY `order` DESC LIMIT 1);
INSERT INTO `sys_pages_blocks` (`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES
('sys_home', 1, 'bx_persons', '' , '_bx_persons_page_block_title_latest_profiles', 11, 2147483647, 'service', 'a:2:{s:6:"module";s:10:"bx_persons";s:6:"method";s:22:"browse_recent_profiles";}', 1, 0, 0, IFNULL(@iBlockOrder, 0) + 1),
('sys_home', 1, 'bx_persons', '_bx_persons_page_block_title_sys_recommended_entries_view_showcase', '_bx_persons_page_block_title_recommended_entries_view_showcase', 11, 2147483647, 'service', 'a:3:{s:6:\"module\";s:10:\"bx_persons\";s:6:\"method\";s:18:\"browse_recommended\";s:6:\"params\";a:3:{s:9:\"unit_view\";s:8:\"showcase\";s:13:\"empty_message\";b:0;s:13:\"ajax_paginate\";b:0;}}', 1, 0, 0, IFNULL(@iBlockOrder, 0) + 2);

-- PAGE: service blocks

SET @iBlockOrder = (SELECT `order` FROM `sys_pages_blocks` WHERE `object` = '' AND `cell_id` = 0 ORDER BY `order` DESC LIMIT 1);
INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES 
('', 0, 'bx_persons', '_bx_persons_page_block_title_sys_featured_entries_view_showcase', '_bx_persons_page_block_title_featured_entries_view_showcase', 11, 2147483647, 'service', 'a:3:{s:6:\"module\";s:10:\"bx_persons\";s:6:\"method\";s:15:\"browse_featured\";s:6:\"params\";a:3:{s:9:\"unit_view\";s:8:\"showcase\";s:13:\"empty_message\";b:0;s:13:\"ajax_paginate\";b:0;}}', 0, 1, 1, IFNULL(@iBlockOrder, 0) + 1),
('', 0, 'bx_persons', '_bx_persons_page_block_title_sys_recommended_entries_view_showcase', '_bx_persons_page_block_title_recommended_entries_view_showcase', 11, 2147483647, 'service', 'a:3:{s:6:\"module\";s:10:\"bx_persons\";s:6:\"method\";s:18:\"browse_recommended\";s:6:\"params\";a:3:{s:9:\"unit_view\";s:8:\"showcase\";s:13:\"empty_message\";b:0;s:13:\"ajax_paginate\";b:0;}}', 0, 1, 1, IFNULL(@iBlockOrder, 0) + 2),
('', 0, 'bx_persons', '_bx_persons_page_block_title_sys_active_entries_view_showcase', '_bx_persons_page_block_title_active_entries_view_showcase', 11, 2147483647, 'service', 'a:3:{s:6:\"module\";s:10:\"bx_persons\";s:6:\"method\";s:22:\"browse_active_profiles\";s:6:\"params\";a:3:{s:9:\"unit_view\";s:16:\"showcase_wo_info\";s:13:\"empty_message\";b:0;s:13:\"ajax_paginate\";b:0;}}', 0, 1, 1, IFNULL(@iBlockOrder, 0) + 3);

-- MENU

-- MENU: add to site menu

SET @iSiteMenuOrder = (SELECT `order` FROM `sys_menu_items` WHERE `set_name` = 'sys_site' AND `active` = 1 ORDER BY `order` DESC LIMIT 1);
INSERT INTO `sys_menu_items` (`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('sys_site', 'bx_persons', 'persons-home', '_bx_persons_menu_item_title_system_entries_home', '_bx_persons_menu_item_title_entries_home', 'page.php?i=persons-home', '', '', 'user col-blue3', 'bx_persons_submenu', 2147483647, 1, 1, IFNULL(@iSiteMenuOrder, 0) + 1);

-- MENU: add to homepage menu

SET @iHomepageMenuOrder = (SELECT `order` FROM `sys_menu_items` WHERE `set_name` = 'sys_homepage' AND `active` = 1 ORDER BY `order` DESC LIMIT 1);
INSERT INTO `sys_menu_items` (`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('sys_homepage', 'bx_persons', 'persons-home', '_bx_persons_menu_item_title_system_entries_home', '_bx_persons_menu_item_title_entries_home', 'page.php?i=persons-home', '', '', 'user col-blue3', 'bx_persons_submenu', 2147483647, 1, 1, IFNULL(@iHomepageMenuOrder, 0) + 1);

-- SET @iCreateProfileMenuOrder = (SELECT `order` FROM `sys_menu_items` WHERE `set_name` = 'sys_profiles_create' AND `active` = 1 ORDER BY `order` DESC LIMIT 1);
-- INSERT INTO `sys_menu_items` (`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES
-- ('sys_profiles_create', 'bx_persons', 'create-person-profile', '_bx_persons_menu_item_title_system_profile_type', '_bx_persons_menu_item_title_profile_type', 'page.php?i=create-persons-profile', '', '', 'user', '', 2147483647, 1, 1, IFNULL(@iCreateProfileMenuOrder, 0) + 1);

SET @iAddMenuOrder = (SELECT `order` FROM `sys_menu_items` WHERE `set_name` = 'sys_add_profile_links' AND `active` = 1 ORDER BY `order` DESC LIMIT 1);
INSERT INTO `sys_menu_items` (`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('sys_add_profile_links', 'bx_persons', 'create-persons-profile', '_bx_persons_menu_item_title_system_create_profile', '_bx_persons_menu_item_title_create_profile', 'page.php?i=create-persons-profile', '', '', 'user col-blue3', '', 2147483647, 1, 1, IFNULL(@iAddMenuOrder, 0) + 1);

-- MENU: view actions

INSERT INTO `sys_objects_menu`(`object`, `title`, `set_name`, `module`, `template_id`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_persons_view_actions', '_bx_persons_menu_title_view_profile_actions', 'bx_persons_view_actions', 'bx_persons', 9, 0, 1, 'BxPersonsMenuView', 'modules/boonex/persons/classes/BxPersonsMenuView.php');

INSERT INTO `sys_menu_sets`(`set_name`, `module`, `title`, `deletable`) VALUES 
('bx_persons_view_actions', 'bx_persons', '_bx_persons_menu_set_title_view_profile_actions', 0);

INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `submenu_popup`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('bx_persons_view_actions', 'bx_persons', 'profile-friend-add', '_bx_persons_menu_item_title_system_befriend', '{title_add_friend}', 'javascript:void(0)', 'bx_conn_action(this, \'sys_profiles_friends\', \'add\', \'{profile_id}\')', '', 'user-plus', '', 0, 2147483647, 1, 0, 10),
('bx_persons_view_actions', 'bx_persons', 'profile-subscribe-add', '_bx_persons_menu_item_title_system_subscribe', '_bx_persons_menu_item_title_subscribe', 'javascript:void(0)', 'bx_conn_action(this, \'sys_profiles_subscriptions\', \'add\', \'{profile_id}\')', '', 'check', '', 0, 2147483647, 1, 0, 20),
('bx_persons_view_actions', 'bx_persons', 'profile-set-acl-level', '_sys_menu_item_title_system_set_acl_level', '_sys_menu_item_title_set_acl_level', 'javascript:void(0)', 'bx_menu_popup(''sys_set_acl_level'', window, {}, {profile_id: {profile_id}});', '', 'certificate', '', 0, 192, 1, 0, 30),
('bx_persons_view_actions', 'bx_persons', 'profile-actions-more', '_bx_persons_menu_item_title_system_more_actions', '_bx_persons_menu_item_title_more_actions', 'javascript:void(0)', 'bx_menu_popup(''bx_persons_view_actions_more'', this, {}, {profile_id:{profile_id}});', '', 'cog', 'bx_persons_view_actions_more', 1, 2147483647, 1, 0, 9999);

-- MENU: view actions more

INSERT INTO `sys_objects_menu`(`object`, `title`, `set_name`, `module`, `template_id`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_persons_view_actions_more', '_bx_persons_menu_title_view_profile_actions_more', 'bx_persons_view_actions_more', 'bx_persons', 6, 0, 1, 'BxPersonsMenuView', 'modules/boonex/persons/classes/BxPersonsMenuView.php');

INSERT INTO `sys_menu_sets`(`set_name`, `module`, `title`, `deletable`) VALUES 
('bx_persons_view_actions_more', 'bx_persons', '_bx_persons_menu_set_title_view_profile_actions_more', 0);

INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('bx_persons_view_actions_more', 'bx_persons', 'profile-friend-remove', '_bx_persons_menu_item_title_system_unfriend', '{title_remove_friend}', 'javascript:void(0)', 'bx_conn_action(this, \'sys_profiles_friends\', \'remove\', \'{profile_id}\')', '', 'user-times', '', 2147483647, 1, 0, 10),
('bx_persons_view_actions_more', 'bx_persons', 'profile-subscribe-remove', '_bx_persons_menu_item_title_system_unsubscribe', '_bx_persons_menu_item_title_unsubscribe', 'javascript:void(0)', 'bx_conn_action(this, \'sys_profiles_subscriptions\', \'remove\', \'{profile_id}\')', '', 'check', '', 2147483647, 1, 0, 20),
('bx_persons_view_actions_more', 'bx_persons', 'edit-persons-cover', '_bx_persons_menu_item_title_system_edit_cover', '_bx_persons_menu_item_title_edit_cover', 'page.php?i=edit-persons-cover&id={content_id}', '', '', 'edit', '', 2147483647, 1, 0, 25),
('bx_persons_view_actions_more', 'bx_persons', 'edit-persons-profile', '_bx_persons_menu_item_title_system_edit_profile', '_bx_persons_menu_item_title_edit_profile', 'page.php?i=edit-persons-profile&id={content_id}', '', '', 'pencil-alt', '', 2147483647, 1, 0, 30),
('bx_persons_view_actions_more', 'bx_persons', 'delete-persons-profile', '_bx_persons_menu_item_title_system_delete_profile', '_bx_persons_menu_item_title_delete_profile', 'page.php?i=delete-persons-profile&id={content_id}', '', '', 'remove', '', 2147483647, 1, 0, 40),
('bx_persons_view_actions_more', 'bx_persons', 'delete-persons-account', '_bx_persons_menu_item_title_system_delete_account', '_bx_persons_menu_item_title_delete_account', 'page.php?i=account-settings-delete&id={account_id}', '', '', 'user-times', '', 128, 1, 0, 50),
('bx_persons_view_actions_more', 'bx_persons', 'delete-persons-account-content', '_bx_persons_menu_item_title_system_delete_account_content', '_bx_persons_menu_item_title_delete_account_content', 'page.php?i=account-settings-delete&id={account_id}&content=1', '', '', 'trash', '', 128, 1, 0, 60);

-- MENU: all actions menu for view entry 

INSERT INTO `sys_objects_menu`(`object`, `title`, `set_name`, `module`, `template_id`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_persons_view_actions_all', '_sys_menu_title_view_actions', 'bx_persons_view_actions_all', 'bx_persons', 15, 0, 1, 'BxPersonsMenuViewActionsAll', 'modules/boonex/persons/classes/BxPersonsMenuViewActionsAll.php');

INSERT INTO `sys_menu_sets`(`set_name`, `module`, `title`, `deletable`) VALUES 
('bx_persons_view_actions_all', 'bx_persons', '_sys_menu_set_title_view_actions', 0);

INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `addon`, `submenu_object`, `submenu_popup`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('bx_persons_view_actions_all', 'bx_persons', 'profile-friend-add', '_bx_persons_menu_item_title_system_befriend', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 10),
('bx_persons_view_actions_all', 'bx_persons', 'profile-friend-remove', '_bx_persons_menu_item_title_system_unfriend', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 20),
('bx_persons_view_actions_all', 'bx_persons', 'profile-subscribe-add', '_bx_persons_menu_item_title_system_subscribe', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 30),
('bx_persons_view_actions_all', 'bx_persons', 'profile-subscribe-remove', '_bx_persons_menu_item_title_system_unsubscribe', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 40),
('bx_persons_view_actions_all', 'bx_persons', 'profile-set-acl-level', '_sys_menu_item_title_system_set_acl_level', '', '', '', '', '', '', '', 0, 192, 1, 0, 50),
('bx_persons_view_actions_all', 'bx_persons', 'comment', '_sys_menu_item_title_system_va_comment', '', '', '', '', '', '', '', 0, 2147483647, 0, 0, 200),
('bx_persons_view_actions_all', 'bx_persons', 'view', '_sys_menu_item_title_system_va_view', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 210),
('bx_persons_view_actions_all', 'bx_persons', 'vote', '_sys_menu_item_title_system_va_vote', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 215),
('bx_persons_view_actions_all', 'bx_persons', 'score', '_sys_menu_item_title_system_va_score', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 220),
('bx_persons_view_actions_all', 'bx_persons', 'favorite', '_sys_menu_item_title_system_va_favorite', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 230),
('bx_persons_view_actions_all', 'bx_persons', 'feature', '_sys_menu_item_title_system_va_feature', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 240),
('bx_persons_view_actions_all', 'bx_persons', 'report', '_sys_menu_item_title_system_va_report', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 250),
('bx_persons_view_actions_all', 'bx_persons', 'edit-persons-cover', '_bx_persons_menu_item_title_system_edit_cover', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 400),
('bx_persons_view_actions_all', 'bx_persons', 'edit-persons-profile', '_bx_persons_menu_item_title_system_edit_profile', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 410),
('bx_persons_view_actions_all', 'bx_persons', 'delete-persons-profile', '_bx_persons_menu_item_title_system_delete_profile', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 420),
('bx_persons_view_actions_all', 'bx_persons', 'delete-persons-account', '_bx_persons_menu_item_title_system_delete_account', '', '', '', '', '', '', '', 0, 128, 1, 0, 430),
('bx_persons_view_actions_all', 'bx_persons', 'delete-persons-account-content', '_bx_persons_menu_item_title_system_delete_account_content', '', '', '', '', '', '', '', 0, 128, 1, 0, 440),
('bx_persons_view_actions_all', 'bx_persons', 'more-auto', '_sys_menu_item_title_system_va_more_auto', '_sys_menu_item_title_va_more_auto', 'javascript:void(0)', '', '', 'ellipsis-v', '', '', 0, 2147483647, 1, 0, 9999);

-- MENU: module sub-menu

INSERT INTO `sys_objects_menu`(`object`, `title`, `set_name`, `module`, `template_id`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_persons_submenu', '_bx_persons_menu_title_submenu', 'bx_persons_submenu', 'bx_persons', 8, 0, 1, '', '');

INSERT INTO `sys_menu_sets`(`set_name`, `module`, `title`, `deletable`) VALUES 
('bx_persons_submenu', 'bx_persons', '_bx_persons_menu_set_title_submenu', 0);

INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('bx_persons_submenu', 'bx_persons', 'persons-home', '_bx_persons_menu_item_title_system_entries_recent', '_bx_persons_menu_item_title_entries_recent', 'page.php?i=persons-home', '', '', '', '', 2147483647, 1, 1, 1),
('bx_persons_submenu', 'bx_persons', 'persons-active', '_bx_persons_menu_item_title_system_entries_active', '_bx_persons_menu_item_title_entries_active', 'page.php?i=persons-active', '', '', '', '', 2147483647, 1, 1, 2),
('bx_persons_submenu', 'bx_persons', 'persons-online', '_bx_persons_menu_item_title_system_entries_online', '_bx_persons_menu_item_title_entries_online', 'page.php?i=persons-online', '', '', '', '', 2147483647, 1, 1, 3),
('bx_persons_submenu', 'bx_persons', 'persons-search', '_bx_persons_menu_item_title_system_entries_search', '_bx_persons_menu_item_title_entries_search', 'page.php?i=persons-search', '', '', '', '', 2147483647, 1, 1, 4),
('bx_persons_submenu', 'bx_persons', 'persons-manage', '_bx_persons_menu_item_title_system_entries_manage', '_bx_persons_menu_item_title_entries_manage', 'page.php?i=persons-manage', '', '', '', '', 2147483646, 1, 1, 5);

-- MENU: view submenu

INSERT INTO `sys_objects_menu`(`object`, `title`, `set_name`, `module`, `template_id`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_persons_view_submenu', '_bx_persons_menu_title_view_profile_submenu', 'bx_persons_view_submenu', 'bx_persons', 8, 0, 1, 'BxPersonsMenuView', 'modules/boonex/persons/classes/BxPersonsMenuView.php'),
('bx_persons_view_submenu_cover', '_bx_persons_menu_title_view_profile_submenu_cover', 'bx_persons_view_submenu', 'bx_persons', 7, 0, 1, 'BxPersonsMenuView', 'modules/boonex/persons/classes/BxPersonsMenuView.php');

INSERT INTO `sys_menu_sets`(`set_name`, `module`, `title`, `deletable`) VALUES 
('bx_persons_view_submenu', 'bx_persons', '_bx_persons_menu_set_title_view_profile_submenu', 0);

INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('bx_persons_view_submenu', 'bx_persons', 'view-persons-profile', '_bx_persons_menu_item_title_system_view_profile_view', '_bx_persons_menu_item_title_view_profile_view', 'page.php?i=view-persons-profile&id={content_id}', '', '', 'user col-blue3', '', 2147483647, 1, 0, 1),
('bx_persons_view_submenu', 'bx_persons', 'persons-profile-info', '_bx_persons_menu_item_title_system_view_profile_info', '_bx_persons_menu_item_title_view_profile_info', 'page.php?i=persons-profile-info&id={content_id}', '', '', 'info-circle col-gray', '', 2147483647, 1, 0, 2),
('bx_persons_view_submenu', 'bx_persons', 'persons-profile-friends', '_bx_persons_menu_item_title_system_view_profile_friends', '_bx_persons_menu_item_title_view_profile_friends', 'page.php?i=persons-profile-friends&profile_id={profile_id}', '', '', 'users col-blue3', '', 2147483647, 1, 0, 3),
('bx_persons_view_submenu', 'bx_persons', 'persons-profile-subscriptions', '_bx_persons_menu_item_title_system_view_profile_subscriptions', '_bx_persons_menu_item_title_view_profile_subscriptions', 'page.php?i=persons-profile-subscriptions&profile_id={profile_id}', '', '', 'check col-blue3', '', 2147483647, 1, 0, 4),
('bx_persons_view_submenu', 'bx_persons', 'persons-profile-comments', '_bx_persons_menu_item_title_system_view_profile_comments', '_bx_persons_menu_item_title_view_profile_comments', 'page.php?i=persons-profile-comments&id={content_id}', '', '', '', '', 2147483647, 0, 0, 5);

-- MENU: custom menu for snippet meta info
INSERT INTO `sys_objects_menu`(`object`, `title`, `set_name`, `module`, `template_id`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_persons_snippet_meta', '_sys_menu_title_snippet_meta', 'bx_persons_snippet_meta', 'bx_persons', 15, 0, 1, 'BxPersonsMenuSnippetMeta', 'modules/boonex/persons/classes/BxPersonsMenuSnippetMeta.php');

INSERT INTO `sys_menu_sets`(`set_name`, `module`, `title`, `deletable`) VALUES 
('bx_persons_snippet_meta', 'bx_persons', '_sys_menu_set_title_snippet_meta', 0);

INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `editable`, `order`) VALUES
('bx_persons_snippet_meta', 'bx_persons', 'befriend', '_sys_menu_item_title_system_sm_befriend', '_sys_menu_item_title_sm_befriend', '', '', '', '', '', 2147483647, 1, 0, 1, 1),
('bx_persons_snippet_meta', 'bx_persons', 'unfriend', '_sys_menu_item_title_system_sm_unfriend', '_sys_menu_item_title_sm_unfriend', '', '', '', '', '', 2147483647, 0, 0, 1, 2),
('bx_persons_snippet_meta', 'bx_persons', 'subscribe', '_sys_menu_item_title_system_sm_subscribe', '_sys_menu_item_title_sm_subscribe', '', '', '', '', '', 2147483647, 0, 0, 1, 3),
('bx_persons_snippet_meta', 'bx_persons', 'unsubscribe', '_sys_menu_item_title_system_sm_unsubscribe', '_sys_menu_item_title_sm_unsubscribe', '', '', '', '', '', 2147483647, 0, 0, 1, 4),
('bx_persons_snippet_meta', 'bx_persons', 'date', '_sys_menu_item_title_system_sm_date', '_sys_menu_item_title_sm_date', '', '', '', '', '', 2147483647, 0, 0, 1, 5),
('bx_persons_snippet_meta', 'bx_persons', 'tags', '_sys_menu_item_title_system_sm_tags', '_sys_menu_item_title_sm_tags', '', '', '', '', '', 2147483647, 0, 0, 1, 6),
('bx_persons_snippet_meta', 'bx_persons', 'views', '_sys_menu_item_title_system_sm_views', '_sys_menu_item_title_sm_views', '', '', '', '', '', 2147483647, 0, 0, 1, 7),
('bx_persons_snippet_meta', 'bx_persons', 'comments', '_sys_menu_item_title_system_sm_comments', '_sys_menu_item_title_sm_comments', '', '', '', '', '', 2147483647, 0, 0, 1, 8),
('bx_persons_snippet_meta', 'bx_persons', 'friends', '_sys_menu_item_title_system_sm_friends', '_sys_menu_item_title_sm_friends', '', '', '', '', '', 2147483647, 1, 0, 1, 9),
('bx_persons_snippet_meta', 'bx_persons', 'subscribers', '_sys_menu_item_title_system_sm_subscribers', '_sys_menu_item_title_sm_subscribers', '', '', '', '', '', 2147483647, 0, 0, 1, 10),
('bx_persons_snippet_meta', 'bx_persons', 'membership', '_sys_menu_item_title_system_sm_membership', '_sys_menu_item_title_sm_membership', '', '', '', '', '', 2147483647, 0, 0, 1, 11);

-- MENU: notifications menu in account popup
SET @iNotifMenuOrder = (SELECT `order` FROM `sys_menu_items` WHERE `set_name` = 'sys_account_notifications' AND `active` = 1 AND `order` < 9999 ORDER BY `order` DESC LIMIT 1);
INSERT INTO `sys_menu_items` (`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `addon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES
('sys_account_notifications', 'bx_persons', 'notifications-friend-requests', '_bx_persons_menu_item_title_system_friends', '_bx_persons_menu_item_title_friends', 'page.php?i=persons-profile-friends&profile_id={member_id}', '', '', 'users col-blue3', 'a:4:{s:6:"module";s:6:"system";s:6:"method";s:31:"get_unconfirmed_connections_num";s:6:"params";a:1:{i:0;s:20:"sys_profiles_friends";}s:5:"class";s:23:"TemplServiceConnections";}', '', 2147483646, 1, 0, IFNULL(@iNotifMenuOrder, 0) + 1);

-- MENU: profile stats
SET @iNotifMenuOrder = (SELECT IFNULL(MAX(`order`), 0) FROM `sys_menu_items` WHERE `set_name` = 'sys_profile_stats' AND `active` = 1 LIMIT 1);
INSERT INTO `sys_menu_items` (`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `addon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES
('sys_profile_stats', 'bx_persons', 'profile-stats-friend-requests', '_bx_persons_menu_item_title_system_friend_requests', '_bx_persons_menu_item_title_friend_requests', 'page.php?i=persons-profile-friends&profile_id={member_id}', '', '', 'users col-blue3', 'a:4:{s:6:"module";s:6:"system";s:6:"method";s:31:"get_unconfirmed_connections_num";s:6:"params";a:1:{i:0;s:20:"sys_profiles_friends";}s:5:"class";s:23:"TemplServiceConnections";}', '', 2147483646, 1, 0, @iNotifMenuOrder + 1),
('sys_profile_stats', 'bx_persons', 'profile-stats-manage-profiles', '_bx_persons_menu_item_title_system_manage_my_profiles', '_bx_persons_menu_item_title_manage_my_profiles', 'page.php?i=persons-manage', '', '_self', 'users col-blue3', 'a:2:{s:6:"module";s:10:"bx_persons";s:6:"method";s:41:"get_menu_addon_manage_tools_profile_stats";}', '', 2147483646, 1, 0, @iNotifMenuOrder + 2),
('sys_profile_stats', 'bx_persons', 'profile-stats-favorite-persons', '_bx_persons_menu_item_title_system_favorites', '_bx_persons_menu_item_title_favorites', 'page.php?i=persons-profile-favorites&profile_id={member_id}', '', '', 'star col-blue3', 'a:2:{s:6:"module";s:10:"bx_persons";s:6:"method";s:38:"get_menu_addon_favorites_profile_stats";}', '', 2147483646, 1, 0, @iNotifMenuOrder + 3),
('sys_profile_stats', 'bx_persons', 'profile-stats-subscriptions', '_bx_persons_menu_item_title_system_subscriptions', '_bx_persons_menu_item_title_subscriptions', 'page.php?i=persons-profile-subscriptions&profile_id={member_id}#subscriptions', '', '_self', 'rss col-blue3', 'a:4:{s:6:"module";s:6:"system";s:6:"method";s:25:"get_connected_content_num";s:6:"params";a:1:{i:0;s:26:"sys_profiles_subscriptions";}s:5:"class";s:23:"TemplServiceConnections";}', '', 2147483646, 1, 0, @iNotifMenuOrder + 4),
('sys_profile_stats', 'bx_persons', 'profile-stats-subscribed-me', '_bx_persons_menu_item_title_system_subscribed_me', '_bx_persons_menu_item_title_subscribed_me', 'page.php?i=persons-profile-subscriptions&profile_id={member_id}#subscribers', '', '_self', 'rss col-blue3', 'a:4:{s:6:"module";s:6:"system";s:6:"method";s:28:"get_connected_initiators_num";s:6:"params";a:1:{i:0;s:26:"sys_profiles_subscriptions";}s:5:"class";s:23:"TemplServiceConnections";}', '', 2147483646, 1, 0, @iNotifMenuOrder + 5);

-- MENU: manage tools submenu
INSERT INTO `sys_objects_menu`(`object`, `title`, `set_name`, `module`, `template_id`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_persons_menu_manage_tools', '_bx_persons_menu_title_manage_tools', 'bx_persons_menu_manage_tools', 'bx_persons', 6, 0, 1, 'BxPersonsMenuManageTools', 'modules/boonex/persons/classes/BxPersonsMenuManageTools.php');

INSERT INTO `sys_menu_sets`(`set_name`, `module`, `title`, `deletable`) VALUES 
('bx_persons_menu_manage_tools', 'bx_persons', '_bx_persons_menu_set_title_manage_tools', 0);

INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('bx_persons_menu_manage_tools', 'bx_persons', 'delete', '_bx_persons_menu_item_title_system_delete', '_bx_persons_menu_item_title_delete', 'javascript:void(0)', 'javascript:{js_object}.onClickDelete({content_id});', '_self', 'far trash-alt', '', 2147483647, 1, 0, 1),
('bx_persons_menu_manage_tools', 'bx_persons', 'delete-with-content', '_bx_persons_menu_item_title_system_delete_with_content', '_bx_persons_menu_item_title_delete_with_content', 'javascript:void(0)', 'javascript:{js_object}.onClickDeleteWithContent({content_id});', '_self', 'far trash-alt', '', 2147483647, 1, 0, 2);

-- MENU: dashboard manage tools
SET @iManageMenuOrder = (SELECT IFNULL(MAX(`order`), 0) FROM `sys_menu_items` WHERE `set_name`='sys_account_dashboard_manage_tools' LIMIT 1);
INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `addon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('sys_account_dashboard_manage_tools', 'bx_persons', 'persons-administration', '_bx_persons_menu_item_title_system_admt_persons', '_bx_persons_menu_item_title_admt_persons', 'page.php?i=persons-administration', '', '_self', '', 'a:2:{s:6:"module";s:10:"bx_persons";s:6:"method";s:27:"get_menu_addon_manage_tools";}', '', 192, 1, 0, @iManageMenuOrder + 1);


-- ACL
INSERT INTO `sys_acl_actions` (`Module`, `Name`, `AdditionalParamName`, `Title`, `Desc`, `Countable`, `DisabledForLevels`) VALUES
('bx_persons', 'create entry', NULL, '_bx_persons_acl_action_create_profile', '', 1, 1);
SET @iIdActionProfileCreate = LAST_INSERT_ID();

INSERT INTO `sys_acl_actions` (`Module`, `Name`, `AdditionalParamName`, `Title`, `Desc`, `Countable`, `DisabledForLevels`) VALUES
('bx_persons', 'delete entry', NULL, '_bx_persons_acl_action_delete_profile', '', 1, 1);
SET @iIdActionProfileDelete = LAST_INSERT_ID();

INSERT INTO `sys_acl_actions` (`Module`, `Name`, `AdditionalParamName`, `Title`, `Desc`, `Countable`, `DisabledForLevels`) VALUES
('bx_persons', 'view entry', NULL, '_bx_persons_acl_action_view_profile', '', 1, 0);
SET @iIdActionProfileView = LAST_INSERT_ID();

INSERT INTO `sys_acl_actions` (`Module`, `Name`, `AdditionalParamName`, `Title`, `Desc`, `Countable`, `DisabledForLevels`) VALUES
('bx_persons', 'edit any entry', NULL, '_bx_persons_acl_action_edit_any_profile', '', 1, 3);
SET @iIdActionProfileEditAny = LAST_INSERT_ID();


SET @iUnauthenticated = 1;
SET @iAccount = 2;
SET @iStandard = 3;
SET @iUnconfirmed = 4;
SET @iPending = 5;
SET @iSuspended = 6;
SET @iModerator = 7;
SET @iAdministrator = 8;
SET @iPremium = 9;


INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES

-- profile create
(@iAccount, @iIdActionProfileCreate),
(@iStandard, @iIdActionProfileCreate),
(@iUnconfirmed, @iIdActionProfileCreate),
(@iPending, @iIdActionProfileCreate),
(@iModerator, @iIdActionProfileCreate),
(@iAdministrator, @iIdActionProfileCreate),
(@iPremium, @iIdActionProfileCreate),

-- profile delete
(@iAccount, @iIdActionProfileDelete),
(@iStandard, @iIdActionProfileDelete),
(@iUnconfirmed, @iIdActionProfileDelete),
(@iPending, @iIdActionProfileDelete),
(@iModerator, @iIdActionProfileDelete),
(@iAdministrator, @iIdActionProfileDelete),
(@iPremium, @iIdActionProfileDelete),

-- profile view
(@iUnauthenticated, @iIdActionProfileView),
(@iAccount, @iIdActionProfileView),
(@iStandard, @iIdActionProfileView),
(@iUnconfirmed, @iIdActionProfileView),
(@iPending, @iIdActionProfileView),
(@iModerator, @iIdActionProfileView),
(@iAdministrator, @iIdActionProfileView),
(@iPremium, @iIdActionProfileView),

-- any profile edit
(@iModerator, @iIdActionProfileEditAny),
(@iAdministrator, @iIdActionProfileEditAny);


-- COMMENTS
INSERT INTO `sys_objects_cmts` (`Name`, `Module`, `Table`, `CharsPostMin`, `CharsPostMax`, `CharsDisplayMax`, `Html`, `PerView`, `PerViewReplies`, `BrowseType`, `IsBrowseSwitch`, `PostFormPosition`, `NumberOfLevels`, `IsDisplaySwitch`, `IsRatable`, `ViewingThreshold`, `IsOn`, `RootStylePrefix`, `BaseUrl`, `ObjectVote`, `TriggerTable`, `TriggerFieldId`, `TriggerFieldAuthor`, `TriggerFieldTitle`, `TriggerFieldComments`, `ClassName`, `ClassFile`) VALUES
('bx_persons', 'bx_persons', 'bx_persons_cmts', 1, 5000, 1000, 3, 5, 3, 'tail', 1, 'bottom', 1, 1, 1, -3, 1, 'cmt', 'page.php?i=view-persons-profile&id={object_id}', '', 'bx_persons_data', 'id', 'author', 'fullname', 'comments', '', '');

-- VIEWS
INSERT INTO `sys_objects_view` (`name`, `module`, `table_track`, `period`, `is_on`, `trigger_table`, `trigger_field_id`, `trigger_field_author`, `trigger_field_count`, `class_name`, `class_file`) VALUES 
('bx_persons', 'bx_persons', 'bx_persons_views_track', '86400', '1', 'bx_persons_data', 'id', 'author', 'views', '', '');

-- VOTES
INSERT INTO `sys_objects_vote` (`Name`, `TableMain`, `TableTrack`, `PostTimeout`, `MinValue`, `MaxValue`, `IsUndo`, `IsOn`, `TriggerTable`, `TriggerFieldId`, `TriggerFieldAuthor`, `TriggerFieldRate`, `TriggerFieldRateCount`, `ClassName`, `ClassFile`) VALUES 
('bx_persons', 'bx_persons_votes', 'bx_persons_votes_track', '604800', '1', '1', '0', '1', 'bx_persons_data', 'id', '', 'rate', 'votes', 'BxPersonsVote', 'modules/boonex/persons/classes/BxPersonsVote.php');

-- SCORES
INSERT INTO `sys_objects_score` (`name`, `module`, `table_main`, `table_track`, `post_timeout`, `is_on`, `trigger_table`, `trigger_field_id`, `trigger_field_author`, `trigger_field_score`, `trigger_field_cup`, `trigger_field_cdown`, `class_name`, `class_file`) VALUES 
('bx_persons', 'bx_persons', 'bx_persons_scores', 'bx_persons_scores_track', '604800', '0', 'bx_persons_data', 'id', 'author', 'score', 'sc_up', 'sc_down', '', '');

-- FAFORITES
INSERT INTO `sys_objects_favorite` (`name`, `table_track`, `is_on`, `is_undo`, `is_public`, `base_url`, `trigger_table`, `trigger_field_id`, `trigger_field_author`, `trigger_field_count`, `class_name`, `class_file`) VALUES 
('bx_persons', 'bx_persons_favorites_track', '1', '1', '0', 'page.php?i=view-persons-profile&id={object_id}', 'bx_persons_data', 'id', 'author', 'favorites', 'BxPersonsFavorite', 'modules/boonex/persons/classes/BxPersonsFavorite.php');

-- FEATURED
INSERT INTO `sys_objects_feature` (`name`, `is_on`, `is_undo`, `base_url`, `trigger_table`, `trigger_field_id`, `trigger_field_author`, `trigger_field_flag`, `class_name`, `class_file`) VALUES 
('bx_persons', '1', '1', 'page.php?i=view-persons-profile&id={object_id}', 'bx_persons_data', 'id', 'author', 'featured', '', '');

-- REPORTS
INSERT INTO `sys_objects_report` (`name`, `table_main`, `table_track`, `is_on`, `base_url`, `trigger_table`, `trigger_field_id`, `trigger_field_author`, `trigger_field_count`, `class_name`, `class_file`) VALUES 
('bx_persons', 'bx_persons_reports', 'bx_persons_reports_track', '1', 'page.php?i=view-persons-profile&id={object_id}', 'bx_persons_data', 'id', 'author', 'reports', 'BxPersonsReport', 'modules/boonex/persons/classes/BxPersonsReport.php');

-- METATAGS
INSERT INTO `sys_objects_metatags` (`object`, `table_keywords`, `table_locations`, `table_mentions`, `override_class_name`, `override_class_file`) VALUES
('bx_persons', 'bx_persons_meta_keywords', 'bx_persons_meta_locations', 'bx_persons_meta_mentions', '', '');

-- SEARCH
SET @iSearchOrder = (SELECT IFNULL(MAX(`Order`), 0) FROM `sys_objects_search`);
INSERT INTO `sys_objects_search` (`ObjectName`, `Title`, `Order`, `ClassName`, `ClassPath`) VALUES
('bx_persons', '_bx_persons', @iSearchOrder + 1, 'BxPersonsSearchResult', 'modules/boonex/persons/classes/BxPersonsSearchResult.php'),
('bx_persons_cmts', '_bx_persons_cmts', @iSearchOrder + 2, 'BxPersonsCmtsSearchResult', 'modules/boonex/persons/classes/BxPersonsCmtsSearchResult.php');

-- STATS
SET @iMaxOrderStats = (SELECT IFNULL(MAX(`order`), 0) FROM `sys_statistics`);
INSERT INTO `sys_statistics` (`module`, `name`, `title`, `link`, `icon`, `query`, `order`) VALUES 
('bx_persons', 'bx_persons', '_bx_persons', 'page.php?i=persons-home', 'user col-blue3', 'SELECT COUNT(*) FROM `bx_persons_data` AS `td` LEFT JOIN `sys_profiles` AS `tp` ON `td`.`id` = `tp`.`content_id` AND `tp`.`type`=''bx_persons'' WHERE 1 AND `tp`.`status`=''active''', @iMaxOrderStats + 1);

-- CHARTS
SET @iMaxOrderCharts = (SELECT IFNULL(MAX(`order`), 0) FROM `sys_objects_chart`);
INSERT INTO `sys_objects_chart` (`object`, `title`, `table`, `field_date_ts`, `field_date_dt`, `field_status`, `query`, `active`, `order`, `class_name`, `class_file`) VALUES
('bx_persons_growth', '_bx_persons_chart_growth', 'bx_persons_data', 'added', '', '', 'SELECT {field_date_formatted} AS `period`, COUNT(*) AS {object} FROM {table} LEFT JOIN `sys_profiles` AS `tp` ON {table}.`id` = `tp`.`content_id` AND `tp`.`type`=''bx_persons'' WHERE 1 AND `tp`.`status`=''active'' {where_inteval} GROUP BY `period` ORDER BY {table}.{field_date} ASC', 1, @iMaxOrderCharts + 1, 'BxDolChartGrowth', ''),
('bx_persons_growth_speed', '_bx_persons_chart_growth_speed', 'bx_persons_data', 'added', '', '', 'SELECT {field_date_formatted} AS `period`, COUNT(*) AS {object} FROM {table} LEFT JOIN `sys_profiles` AS `tp` ON {table}.`id` = `tp`.`content_id` AND `tp`.`type`=''bx_persons'' WHERE 1 AND `tp`.`status`=''active'' {where_inteval} GROUP BY `period` ORDER BY {table}.{field_date} ASC', 1, @iMaxOrderCharts + 2, 'BxDolChartGrowthSpeed', '');

-- GRIDS: administration
INSERT INTO `sys_objects_grid` (`object`, `source_type`, `source`, `table`, `field_id`, `field_order`, `field_active`, `paginate_url`, `paginate_per_page`, `paginate_simple`, `paginate_get_start`, `paginate_get_per_page`, `filter_fields`, `filter_fields_translatable`, `filter_mode`, `sorting_fields`, `sorting_fields_translatable`, `visible_for_levels`, `override_class_name`, `override_class_file`) VALUES
('bx_persons_administration', 'Sql', 'SELECT `td`.*, `ta`.`email` AS `account`, `ta`.`logged` AS `last_online`, `tp`.`status` AS `status` FROM `bx_persons_data` AS `td` LEFT JOIN `sys_profiles` AS `tp` ON `td`.`id`=`tp`.`content_id` AND `tp`.`type`=''bx_persons'' LEFT JOIN `sys_accounts` AS `ta` ON `tp`.`account_id`=`ta`.`id` WHERE 1 ', 'bx_persons_data', 'id', 'last_online', 'status', '', 20, NULL, 'start', '', 'fullname', '', 'like', 'reports', '', 192, 'BxPersonsGridAdministration', 'modules/boonex/persons/classes/BxPersonsGridAdministration.php'),
('bx_persons_common', 'Sql', 'SELECT `td`.*, `ta`.`email` AS `account`, `ta`.`logged` AS `last_online`, `tp`.`status` AS `status` FROM `bx_persons_data` AS `td` LEFT JOIN `sys_profiles` AS `tp` ON `td`.`id`=`tp`.`content_id` AND `tp`.`type`=''bx_persons'' LEFT JOIN `sys_accounts` AS `ta` ON `tp`.`account_id`=`ta`.`id` WHERE 1 ', 'bx_persons_data', 'id', 'last_online', 'status', '', 20, NULL, 'start', '', 'fullname', '', 'like', '', '', 2147483647, 'BxPersonsGridCommon', 'modules/boonex/persons/classes/BxPersonsGridCommon.php');

INSERT INTO `sys_grid_fields` (`object`, `name`, `title`, `width`, `translatable`, `chars_limit`, `params`, `order`) VALUES
('bx_persons_administration', 'checkbox', '_sys_select', '2%', 0, '', '', 1),
('bx_persons_administration', 'switcher', '_bx_persons_grid_column_title_adm_active', '8%', 0, '', '', 2),
('bx_persons_administration', 'reports', '_sys_txt_reports_title', '5%', 0, '', '', 3),
('bx_persons_administration', 'fullname', '_bx_persons_grid_column_title_adm_fullname', '25%', 0, '', '', 4),
('bx_persons_administration', 'last_online', '_bx_persons_grid_column_title_adm_last_online', '20%', 1, '25', '', 5),
('bx_persons_administration', 'account', '_bx_persons_grid_column_title_adm_account', '20%', 0, '25', '', 6),
('bx_persons_administration', 'actions', '', '20%', 0, '', '', 7),
('bx_persons_common', 'checkbox', '_sys_select', '2%', 0, '', '', 1),
('bx_persons_common', 'fullname', '_bx_persons_grid_column_title_adm_fullname', '48%', 0, '', '', 2),
('bx_persons_common', 'last_online', '_bx_persons_grid_column_title_adm_last_online', '30%', 1, '25', '', 3),
('bx_persons_common', 'actions', '', '20%', 0, '', '', 4);

INSERT INTO `sys_grid_actions` (`object`, `type`, `name`, `title`, `icon`, `icon_only`, `confirm`, `order`) VALUES
('bx_persons_administration', 'bulk', 'set_acl_level', '_bx_persons_grid_action_title_adm_set_acl_level', '', 0, 0, 1),
('bx_persons_administration', 'bulk', 'delete', '_bx_persons_grid_action_title_adm_delete', '', 0, 1, 2),
('bx_persons_administration', 'bulk', 'delete_with_content', '_bx_persons_grid_action_title_adm_delete_with_content', '', 0, 1, 3),
('bx_persons_administration', 'single', 'set_acl_level', '_bx_persons_grid_action_title_adm_set_acl_level', 'certificate', 1, 0, 1),
('bx_persons_administration', 'single', 'settings', '_bx_persons_grid_action_title_adm_more_actions', 'cog', 1, 0, 2),
('bx_persons_common', 'bulk', 'delete', '_bx_persons_grid_action_title_adm_delete', '', 0, 1, 1),
('bx_persons_common', 'bulk', 'delete_with_content', '_bx_persons_grid_action_title_adm_delete_with_content', '', 0, 1, 2),
('bx_persons_common', 'single', 'settings', '_bx_persons_grid_action_title_adm_more_actions', 'cog', 1, 0, 1);

-- LIVE UPDATES
INSERT INTO `sys_objects_live_updates`(`name`, `frequency`, `service_call`, `active`) VALUES
('bx_persons_friend_requests', 1, 'a:4:{s:6:"module";s:6:"system";s:6:"method";s:40:"get_live_updates_unconfirmed_connections";s:6:"params";a:5:{i:0;s:10:"bx_persons";i:1;s:20:"sys_profiles_friends";i:2;a:2:{s:11:"menu_object";s:18:"sys_toolbar_member";s:9:"menu_item";s:7:"account";}i:3;a:2:{s:11:"menu_object";s:25:"sys_account_notifications";s:9:"menu_item";s:29:"notifications-friend-requests";}i:4;s:7:"{count}";}s:5:"class";s:23:"TemplServiceConnections";}', 1);

-- ALERTS
INSERT INTO `sys_alerts_handlers` (`name`, `class`, `file`, `service_call`) VALUES 
('bx_persons', 'BxPersonsAlertsResponse', 'modules/boonex/persons/classes/BxPersonsAlertsResponse.php', '');
SET @iHandler := LAST_INSERT_ID();

INSERT INTO `sys_alerts` (`unit`, `action`, `handler_id`) VALUES
('system', 'save_setting', @iHandler),
('sys_profiles_friends', 'connection_added', @iHandler),
('bx_timeline', 'post_common', @iHandler),
('bx_persons_pictures', 'file_deleted', @iHandler),
('bx_persons', 'timeline_view', @iHandler),
('bx_persons', 'timeline_post', @iHandler),
('bx_persons', 'timeline_delete', @iHandler),
('bx_persons', 'timeline_comment', @iHandler),
('bx_persons', 'timeline_vote', @iHandler),
('bx_persons', 'timeline_report', @iHandler),
('bx_persons', 'timeline_repost', @iHandler);

-- PRIVACY 
INSERT INTO `sys_objects_privacy` (`object`, `module`, `action`, `title`, `default_group`, `spaces`, `table`, `table_field_id`, `table_field_author`, `override_class_name`, `override_class_file`) VALUES
('bx_persons_allow_view_to', 'bx_persons', 'view', '_bx_persons_form_profile_input_allow_view_to', '3', '', 'bx_persons_data', 'id', 'author', 'BxPersonsPrivacy', 'modules/boonex/persons/classes/BxPersonsPrivacy.php');

-- EMAIL TEMPLATES
INSERT INTO `sys_email_templates` (`Module`, `NameSystem`, `Name`, `Subject`, `Body`) VALUES
('bx_persons', '_bx_persons_email_friend_request', 'bx_persons_friend_request', '_bx_persons_email_friend_request_subject', '_bx_persons_email_friend_request_body');

-- UPLOADERS
INSERT INTO `sys_objects_uploader` (`object`, `active`, `override_class_name`, `override_class_file`) VALUES
('bx_persons_cover_crop', 1, 'BxPersonsUploaderCoverCrop', 'modules/boonex/persons/classes/BxPersonsUploaderCoverCrop.php'),
('bx_persons_picture_crop', 1, 'BxPersonsUploaderPictureCrop', 'modules/boonex/persons/classes/BxPersonsUploaderPictureCrop.php');
