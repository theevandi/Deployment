
-- SETTINGS

SET @iTypeOrder = (SELECT MAX(`order`) FROM `sys_options_types` WHERE `group` = 'modules');
INSERT INTO `sys_options_types`(`group`, `name`, `caption`, `icon`, `order`) VALUES 
('modules', 'bx_spaces', '_bx_spaces', 'bx_spaces@modules/boonex/spaces/|std-icon.svg', IF(ISNULL(@iTypeOrder), 1, @iTypeOrder + 1));
SET @iTypeId = LAST_INSERT_ID();

INSERT INTO `sys_options_categories` (`type_id`, `name`, `caption`, `order`)
VALUES (@iTypeId, 'bx_spaces', '_bx_spaces', 1);
SET @iCategId = LAST_INSERT_ID();

INSERT INTO `sys_options` (`name`, `value`, `category_id`, `caption`, `type`, `extra`, `check`, `check_error`, `order`) VALUES
('bx_spaces_enable_multilevel_hierarchy', '', @iCategId, '_bx_spaces_option_enable_multilevel_hierarchy', 'checkbox', '', '', '', 0),
('bx_spaces_num_connections_quick', '6', @iCategId, '_bx_spaces_option_num_connections_quick', 'digit', '', '', '', 10),
('bx_spaces_per_page_browse', '24', @iCategId, '_bx_spaces_option_per_page_browse', 'digit', '', '', '', 11),
('bx_spaces_num_rss', '10', @iCategId, '_bx_spaces_option_num_rss', 'digit', '', '', '', 12),
('bx_spaces_per_page_browse_showcase', '32', @iCategId, '_sys_option_per_page_browse_showcase', 'digit', '', '', '', 15),
('bx_spaces_per_page_browse_recommended', '10', @iCategId, '_sys_option_per_page_browse_recommended', 'digit', '', '', '', 16),
('bx_spaces_searchable_fields', 'space_name,space_desc', @iCategId, '_bx_spaces_option_searchable_fields', 'list', 'a:2:{s:6:"module";s:9:"bx_spaces";s:6:"method";s:21:"get_searchable_fields";}', '', '', 30);


-- PAGES

-- PAGE: create profile

INSERT INTO `sys_objects_page`(`object`, `uri`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_spaces_create_profile', 'create-space-profile', '_bx_spaces_page_title_sys_create_profile', '_bx_spaces_page_title_create_profile', 'bx_spaces', 5, 2147483647, 1, 'page.php?i=create-space-profile', '', '', '', 0, 1, 0, '', '');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES 
('bx_spaces_create_profile', 1, 'bx_spaces', '_bx_spaces_page_block_title_create_profile', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:9:\"bx_spaces\";s:6:\"method\";s:13:\"entity_create\";}', 0, 1, 1);

-- PAGE: view profile

INSERT INTO `sys_objects_page`(`object`, `uri`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_spaces_view_profile', 'view-space-profile', '_bx_spaces_page_title_sys_view_profile', '_bx_spaces_page_title_view_profile', 'bx_spaces', 10, 2147483647, 1, 'page.php?i=view-space-profile', '', '', '', 0, 1, 0, 'BxSpacesPageEntry', 'modules/boonex/spaces/classes/BxSpacesPageEntry.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES
('bx_spaces_view_profile', 1, 'bx_spaces', '', '_bx_spaces_page_block_title_entry_social_sharing', 13, 2147483647, 'service', 'a:2:{s:6:\"module\";s:9:\"bx_spaces\";s:6:\"method\";s:21:\"entity_social_sharing\";}', 0, 0, 0, 0),
('bx_spaces_view_profile', 1, 'bx_spaces', '', '_bx_spaces_page_block_title_entry_all_actions', 13, 2147483647, 'service', 'a:2:{s:6:"module";s:9:"bx_spaces";s:6:"method";s:18:"entity_all_actions";}', 0, 0, 0, 0),
('bx_spaces_view_profile', 2, 'bx_spaces', '', '_bx_spaces_page_block_title_profile_info', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:9:\"bx_spaces\";s:6:\"method\";s:11:\"entity_info\";}', 0, 0, 1, 0),
('bx_spaces_view_profile', 2, 'bx_spaces', '', '_bx_spaces_page_block_title_parent', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:9:\"bx_spaces\";s:6:\"method\";s:13:\"entity_parent\";}', 0, 0, 1, 1),
('bx_spaces_view_profile', 2, 'bx_spaces', '', '_bx_spaces_page_block_title_childs', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:9:\"bx_spaces\";s:6:\"method\";s:13:\"entity_childs\";}', 0, 0, 1, 2),
('bx_spaces_view_profile', 3, 'bx_spaces', '', '_bx_spaces_page_block_title_profile_location', 13, 2147483647, 'service', 'a:2:{s:6:\"module\";s:9:\"bx_spaces\";s:6:\"method\";s:15:\"entity_location\";}', 0, 0, 0, 0),
('bx_spaces_view_profile', 3, 'bx_spaces', '', '_bx_spaces_page_block_title_fans', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:9:\"bx_spaces\";s:6:\"method\";s:4:\"fans\";}', 0, 0, 1, 0),
('bx_spaces_view_profile', 3, 'bx_spaces', '', '_bx_spaces_page_block_title_admins', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:9:\"bx_spaces\";s:6:\"method\";s:6:\"admins\";}', 0, 0, 1, 1),
('bx_spaces_view_profile', 3, 'bx_spaces', '', '_bx_spaces_page_block_title_profile_location', 3, 2147483647, 'service', 'a:4:{s:6:\"module\";s:6:\"system\";s:6:\"method\";s:13:\"locations_map\";s:6:\"params\";a:2:{i:0;s:9:\"bx_spaces\";i:1;s:12:\"{content_id}\";}s:5:\"class\";s:20:\"TemplServiceMetatags\";}', 0, 0, 1, 2),
('bx_spaces_view_profile', 4, 'bx_spaces', '', '_bx_spaces_page_block_title_profile_description', 13, 2147483647, 'service', 'a:2:{s:6:\"module\";s:9:\"bx_spaces\";s:6:\"method\";s:17:\"entity_text_block\";}', 0, 0, 1, 0),
('bx_spaces_view_profile', 4, 'bx_spaces', '', '_bx_spaces_page_block_title_profile_comments', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:9:\"bx_spaces\";s:6:\"method\";s:15:\"entity_comments\";}', 0, 0, 0, 0);

-- PAGE: view closed profile 

INSERT INTO `sys_objects_page`(`object`, `uri`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_spaces_view_profile_closed', 'view-space-profile-closed', '_bx_spaces_page_title_sys_view_profile_closed', '_bx_spaces_page_title_view_profile', 'bx_spaces', 10, 2147483647, 1, 'page.php?i=view-space-profile', '', '', '', 0, 1, 0, 'BxSpacesPageEntry', 'modules/boonex/spaces/classes/BxSpacesPageEntry.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES 
('bx_spaces_view_profile_closed', 2, 'bx_spaces', '', '_bx_spaces_page_block_title_profile_info', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:9:\"bx_spaces\";s:6:\"method\";s:11:\"entity_info\";}', 0, 0, 1, 0),
('bx_spaces_view_profile_closed', 3, 'bx_spaces', '', '_bx_spaces_page_block_title_fans', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:9:\"bx_spaces\";s:6:\"method\";s:4:\"fans\";}', 0, 0, 1, 0);

-- PAGE: edit profile

INSERT INTO `sys_objects_page`(`object`, `uri`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_spaces_edit_profile', 'edit-space-profile', '_bx_spaces_page_title_sys_edit_profile', '_bx_spaces_page_title_edit_profile', 'bx_spaces', 5, 2147483647, 1, 'page.php?i=edit-space-profile', '', '', '', 0, 1, 0, 'BxSpacesPageEntry', 'modules/boonex/spaces/classes/BxSpacesPageEntry.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES 
('bx_spaces_edit_profile', 1, 'bx_spaces', '_bx_spaces_page_block_title_edit_profile', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:9:\"bx_spaces\";s:6:\"method\";s:11:\"entity_edit\";}', 0, 0, 0);

-- PAGE: edit profile cover

INSERT INTO `sys_objects_page`(`object`, `uri`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_spaces_edit_profile_cover', 'edit-space-cover', '_bx_spaces_page_title_sys_edit_profile_cover', '_bx_spaces_page_title_edit_profile_cover', 'bx_spaces', 5, 2147483647, 1, 'page.php?i=edit-space-cover', '', '', '', 0, 1, 0, 'BxSpacesPageEntry', 'modules/boonex/spaces/classes/BxSpacesPageEntry.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES 
('bx_spaces_edit_profile_cover', 1, 'bx_spaces', '_bx_spaces_page_block_title_edit_profile_cover', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:9:\"bx_spaces\";s:6:\"method\";s:17:\"entity_edit_cover\";}', 0, 0, 0);

-- PAGE: invite members

INSERT INTO `sys_objects_page`(`object`, `uri`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_spaces_invite', 'invite-to-space', '_bx_spaces_page_title_sys_invite_to_space', '_bx_spaces_page_title_invite_to_space', 'bx_spaces', 5, 2147483647, 1, 'page.php?i=invite-to-space', '', '', '', 0, 1, 0, 'BxSpacesPageEntry', 'modules/boonex/spaces/classes/BxSpacesPageEntry.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES 
('bx_spaces_invite', 1, 'bx_spaces', '_bx_spaces_page_block_title_invite_to_space', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:9:\"bx_spaces\";s:6:\"method\";s:13:\"entity_invite\";}', 0, 0, 0);

-- PAGE: delete profile

INSERT INTO `sys_objects_page`(`object`, `uri`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_spaces_delete_profile', 'delete-space-profile', '_bx_spaces_page_title_sys_delete_profile', '_bx_spaces_page_title_delete_profile', 'bx_spaces', 5, 2147483647, 1, 'page.php?i=delete-space-profile', '', '', '', 0, 1, 0, 'BxSpacesPageEntry', 'modules/boonex/spaces/classes/BxSpacesPageEntry.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES 
('bx_spaces_delete_profile', 1, 'bx_spaces', '_bx_spaces_page_block_title_delete_profile', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:9:\"bx_spaces\";s:6:\"method\";s:13:\"entity_delete\";}', 0, 0, 0);

-- PAGE: profile info

INSERT INTO `sys_objects_page`(`object`, `uri`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_spaces_profile_info', 'space-profile-info', '_bx_spaces_page_title_sys_profile_info', '_bx_spaces_page_title_profile_info', 'bx_spaces', 5, 2147483647, 1, 'page.php?i=space-profile-info', '', '', '', 0, 1, 0, 'BxSpacesPageEntry', 'modules/boonex/spaces/classes/BxSpacesPageEntry.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES 
('bx_spaces_profile_info', 1, 'bx_spaces', '_bx_spaces_page_block_title_system_profile_info', '_bx_spaces_page_block_title_profile_info_link', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:9:\"bx_spaces\";s:6:\"method\";s:16:\"entity_info_full\";}', 0, 0, 1, 1),
('bx_spaces_profile_info', 1, 'bx_spaces', '', '_bx_spaces_page_block_title_profile_description', 13, 2147483647, 'service', 'a:2:{s:6:\"module\";s:9:\"bx_spaces\";s:6:\"method\";s:17:\"entity_text_block\";}', 0, 0, 1, 2);

-- PAGE: space fans

INSERT INTO `sys_objects_page`(`object`, `uri`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_spaces_fans', 'space-fans', '_bx_spaces_page_title_sys_space_fans', '_bx_spaces_page_title_space_fans', 'bx_spaces', 5, 2147483647, 1, 'page.php?i=space-fans', '', '', '', 0, 1, 0, 'BxSpacesPageEntry', 'modules/boonex/spaces/classes/BxSpacesPageEntry.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES 
('bx_spaces_fans', 1, 'bx_spaces', '_bx_spaces_page_block_title_system_fans', '_bx_spaces_page_block_title_fans_link', 11, 2147483647, 'service', 'a:2:{s:6:"module";s:9:"bx_spaces";s:6:"method";s:10:"fans_table";}', 0, 0, 1, 1);

-- PAGE: view entry comments
INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_spaces_profile_comments', '_bx_spaces_page_title_sys_profile_comments', '_bx_spaces_page_title_profile_comments', 'bx_spaces', 5, 2147483647, 1, 'space-profile-comments', '', '', '', '', 0, 1, 0, 'BxSpacesPageEntry', 'modules/boonex/spaces/classes/BxSpacesPageEntry.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES 
('bx_spaces_profile_comments', 1, 'bx_spaces', '_bx_spaces_page_block_title_profile_comments', '_bx_spaces_page_block_title_profile_comments_link', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:9:\"bx_spaces\";s:6:\"method\";s:15:\"entity_comments\";}', 0, 0, 0, 1);

-- PAGE: module home

INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_spaces_home', '_bx_spaces_page_title_sys_recent', '_bx_spaces_page_title_recent', 'bx_spaces', 5, 2147483647, 1, 'spaces-home', 'page.php?i=spaces-home', '', '', '', 0, 1, 0, 'BxSpacesPageBrowse', 'modules/boonex/spaces/classes/BxSpacesPageBrowse.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`, `active`) VALUES
('bx_spaces_home', 1, 'bx_spaces', '_bx_spaces_page_block_title_featured_profiles', 11, 2147483647, 'service', 'a:3:{s:6:"module";s:9:"bx_spaces";s:6:"method";s:15:"browse_featured";s:6:"params";a:1:{i:0;s:7:"gallery";}}', 0, 1, 0, 1),
('bx_spaces_home', 1, 'bx_spaces', '_bx_spaces_page_block_title_latest_profiles', 11, 2147483647, 'service', 'a:3:{s:6:\"module\";s:9:\"bx_spaces\";s:6:\"method\";s:22:\"browse_recent_profiles\";s:6:"params";a:1:{i:0;b:1;}}', 0, 1, 1, 1),
('bx_spaces_home', 1, 'bx_spaces', '_bx_spaces_page_block_title_top_level_spaces', 11, 2147483647, 'service', 'a:3:{s:6:\"module\";s:9:\"bx_spaces\";s:6:\"method\";s:16:\"browse_top_level\";s:6:"params";a:1:{i:0;b:1;}}', 0, 1, 2, 0);

-- PAGE: top profiles

INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_spaces_top', '_bx_spaces_page_title_sys_top', '_bx_spaces_page_title_top', 'bx_spaces', 5, 2147483647, 1, 'spaces-top', 'page.php?i=spaces-top', '', '', '', 0, 1, 0, 'BxSpacesPageBrowse', 'modules/boonex/spaces/classes/BxSpacesPageBrowse.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES 
('bx_spaces_top', 1, 'bx_spaces', '_bx_spaces_page_block_title_top_profiles', 11, 2147483647, 'service', 'a:3:{s:6:\"module\";s:9:\"bx_spaces\";s:6:\"method\";s:19:\"browse_top_profiles\";s:6:"params";a:1:{i:0;b:1;}}', 0, 1, 0);

-- PAGE: search for entries
INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_spaces_search', '_bx_spaces_page_title_sys_entries_search', '_bx_spaces_page_title_entries_search', 'bx_spaces', 5, 2147483647, 1, 'spaces-search', 'page.php?i=spaces-search', '', '', '', 0, 1, 0, 'BxSpacesPageBrowse', 'modules/boonex/spaces/classes/BxSpacesPageBrowse.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES 
('bx_spaces_search', 1, 'bx_spaces', '_bx_spaces_page_block_title_search_form', 11, 2147483647, 'service', 'a:4:{s:6:"module";s:6:"system";s:6:"method";s:8:"get_form";s:6:"params";a:1:{i:0;a:1:{s:6:"object";s:9:"bx_spaces";}}s:5:"class";s:27:"TemplSearchExtendedServices";}', 0, 1, 1, 1),
('bx_spaces_search', 1, 'bx_spaces', '_bx_spaces_page_block_title_search_results', 11, 2147483647, 'service', 'a:4:{s:6:"module";s:6:"system";s:6:"method";s:11:"get_results";s:6:"params";a:1:{i:0;a:2:{s:6:"object";s:9:"bx_spaces";s:10:"show_empty";b:1;}}s:5:"class";s:27:"TemplSearchExtendedServices";}', 0, 1, 1, 2),
('bx_spaces_search', 1, 'bx_spaces', '_bx_spaces_page_block_title_search_form_cmts', 11, 2147483647, 'service', 'a:4:{s:6:"module";s:6:"system";s:6:"method";s:8:"get_form";s:6:"params";a:1:{i:0;a:1:{s:6:"object";s:14:"bx_spaces_cmts";}}s:5:"class";s:27:"TemplSearchExtendedServices";}', 0, 1, 0, 3),
('bx_spaces_search', 1, 'bx_spaces', '_bx_spaces_page_block_title_search_results_cmts', 11, 2147483647, 'service', 'a:4:{s:6:"module";s:6:"system";s:6:"method";s:11:"get_results";s:6:"params";a:1:{i:0;a:2:{s:6:"object";s:14:"bx_spaces_cmts";s:10:"show_empty";b:1;}}s:5:"class";s:27:"TemplSearchExtendedServices";}', 0, 1, 0, 4);

-- PAGE: module manage own
INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_spaces_manage', '_bx_spaces_page_title_sys_manage', '_bx_spaces_page_title_manage', 'bx_spaces', 5, 2147483647, 1, 'spaces-manage', 'page.php?i=spaces-manage', '', '', '', 0, 1, 0, 'BxSpacesPageBrowse', 'modules/boonex/spaces/classes/BxSpacesPageBrowse.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES 
('bx_spaces_manage', 1, 'bx_spaces', '_bx_spaces_page_block_title_system_manage', '_bx_spaces_page_block_title_manage', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:9:\"bx_spaces\";s:6:\"method\";s:12:\"manage_tools\";}}', 0, 1, 0);

-- PAGE: module manage all
INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_spaces_administration', '_bx_spaces_page_title_sys_manage_administration', '_bx_spaces_page_title_manage', 'bx_spaces', 5, 192, 1, 'spaces-administration', 'page.php?i=spaces-administration', '', '', '', 0, 1, 0, 'BxSpacesPageBrowse', 'modules/boonex/spaces/classes/BxSpacesPageBrowse.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES 
('bx_spaces_administration', 1, 'bx_spaces', '_bx_spaces_page_block_title_system_manage_administration', '_bx_spaces_page_block_title_manage', 11, 192, 'service', 'a:3:{s:6:\"module\";s:9:\"bx_spaces\";s:6:\"method\";s:12:\"manage_tools\";s:6:\"params\";a:1:{i:0;s:14:\"administration\";}}', 0, 1, 0);

-- PAGE: user's spaces
INSERT INTO `sys_objects_page`(`object`, `uri`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_spaces_joined', 'joined-spaces', '_bx_spaces_page_title_sys_joined_entries', '_bx_spaces_page_title_joined_entries', 'bx_spaces', 5, 2147483647, 1, '', '', '', '', 0, 1, 0, 'BxSpacesPageJoinedEntries', 'modules/boonex/spaces/classes/BxSpacesPageJoinedEntries.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES
('bx_spaces_joined', 1, 'bx_spaces', '_bx_spaces_page_block_title_sys_entries_actions', '_bx_spaces_page_block_title_entries_actions', 13, 2147483647, 'service', 'a:2:{s:6:"module";s:9:"bx_spaces";s:6:"method";s:18:"my_entries_actions";}', 0, 0, 1, 1),
('bx_spaces_joined', 1, 'bx_spaces', '_bx_spaces_page_block_title_sys_entries_of_author', '_bx_spaces_page_block_title_entries_of_author', 11, 2147483647, 'service', 'a:2:{s:6:"module";s:9:"bx_spaces";s:6:"method";s:22:"browse_created_entries";}', 0, 0, 0, 2),
('bx_spaces_joined', 1, 'bx_spaces', '_bx_spaces_page_block_title_sys_favorites_of_author', '_bx_spaces_page_block_title_favorites_of_author', 11, 2147483647, 'service', 'a:3:{s:6:"module";s:9:"bx_spaces";s:6:"method";s:15:"browse_favorite";s:6:"params";a:1:{i:0;s:12:"{profile_id}";}}', 0, 1, 1, 3),
('bx_spaces_joined', 1, 'bx_spaces', '_bx_spaces_page_block_title_sys_joined_entries', '_bx_spaces_page_block_title_joined_entries', 11, 2147483647, 'service', 'a:3:{s:6:"module";s:9:"bx_spaces";s:6:"method";s:21:"browse_joined_entries";s:6:"params";a:2:{i:0;i:0;i:1;b:1;}}', 0, 0, 1, 4);

-- PAGE: add block to homepage
SET @iBlockOrder = (SELECT `order` FROM `sys_pages_blocks` WHERE `object` = 'sys_home' AND `cell_id` = 1 ORDER BY `order` DESC LIMIT 1);
INSERT INTO `sys_pages_blocks` (`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES
('sys_home', 1, 'bx_spaces', '', '_bx_spaces_page_block_title_latest_profiles', 11, 2147483647, 'service', 'a:2:{s:6:"module";s:9:"bx_spaces";s:6:"method";s:22:"browse_recent_profiles";}', 1, 0, 0, IFNULL(@iBlockOrder, 0) + 1),
('sys_home', 1, 'bx_spaces', '_bx_spaces_page_block_title_sys_recommended_entries_view_showcase', '_bx_spaces_page_block_title_recommended_entries_view_showcase', 11, 2147483647, 'service', 'a:2:{s:6:"module";s:9:"bx_spaces";s:6:"method";s:18:"browse_recommended";}', 1, 0, 0, IFNULL(@iBlockOrder, 0) + 2);
-- PAGE: service blocks
SET @iBlockOrder = (SELECT `order` FROM `sys_pages_blocks` WHERE `object` = '' AND `cell_id` = 0 ORDER BY `order` DESC LIMIT 1);
INSERT INTO `sys_pages_blocks` (`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES
('', 0, 'bx_spaces', '', '_bx_spaces_page_block_title_categories', 11, 2147483647, 'service', 'a:4:{s:6:"module";s:6:"system";s:6:"method";s:15:"categories_list";s:6:"params";a:2:{i:0;s:14:"bx_spaces_cats";i:1;a:2:{s:10:\"show_empty\";b:1;s:21:\"show_empty_categories\";b:1;}}s:5:"class";s:20:"TemplServiceCategory";}', 1, 1, 1, IFNULL(@iBlockOrder, 0) + 1),
('', 0, 'bx_spaces', '_bx_spaces_page_block_title_sys_featured_entries_view_showcase', '_bx_spaces_page_block_title_featured_entries_view_showcase', 11, 2147483647, 'service', 'a:3:{s:6:\"module\";s:9:\"bx_spaces\";s:6:\"method\";s:15:\"browse_featured\";s:6:\"params\";a:3:{s:9:\"unit_view\";s:8:\"showcase\";s:13:\"empty_message\";b:0;s:13:\"ajax_paginate\";b:0;}}', 0, 1, 1, IFNULL(@iBlockOrder, 0) + 2),
('', 0, 'bx_spaces', '_bx_spaces_page_block_title_sys_recommended_entries_view_showcase', '_bx_spaces_page_block_title_recommended_entries_view_showcase', 11, 2147483647, 'service', 'a:3:{s:6:\"module\";s:9:\"bx_spaces\";s:6:\"method\";s:18:\"browse_recommended\";s:6:\"params\";a:3:{s:9:\"unit_view\";s:8:\"showcase\";s:13:\"empty_message\";b:0;s:13:\"ajax_paginate\";b:0;}}', 0, 1, 1, IFNULL(@iBlockOrder, 0) + 3);

-- MENU

-- MENU: add to site menu

SET @iSiteMenuOrder = (SELECT `order` FROM `sys_menu_items` WHERE `set_name` = 'sys_site' AND `active` = 1 ORDER BY `order` DESC LIMIT 1);
INSERT INTO `sys_menu_items` (`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('sys_site', 'bx_spaces', 'spaces-home', '_bx_spaces_menu_item_title_system_entries_home', '_bx_spaces_menu_item_title_entries_home', 'page.php?i=spaces-home', '', '', 'object-group col-red2', 'bx_spaces_submenu', 2147483647, 1, 1, IFNULL(@iSiteMenuOrder, 0) + 1);

-- MENU: add to homepage menu

SET @iHomepageMenuOrder = (SELECT `order` FROM `sys_menu_items` WHERE `set_name` = 'sys_homepage' AND `active` = 1 ORDER BY `order` DESC LIMIT 1);
INSERT INTO `sys_menu_items` (`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('sys_homepage', 'bx_spaces', 'spaces-home', '_bx_spaces_menu_item_title_system_entries_home', '_bx_spaces_menu_item_title_entries_home', 'page.php?i=spaces-home', '', '', 'object-group col-red2', 'bx_spaces_submenu', 2147483647, 1, 1, IFNULL(@iHomepageMenuOrder, 0) + 1);

-- MENU: add to "add content" menu

SET @iAddMenuOrder = (SELECT `order` FROM `sys_menu_items` WHERE `set_name` = 'sys_add_content_links' AND `active` = 1 ORDER BY `order` DESC LIMIT 1);
INSERT INTO `sys_menu_items` (`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('sys_add_content_links', 'bx_spaces', 'create-space-profile', '_bx_spaces_menu_item_title_system_create_profile', '_bx_spaces_menu_item_title_create_profile', 'page.php?i=create-space-profile', '', '', 'object-group col-red2', '', 2147483647, 1, 1, IFNULL(@iAddMenuOrder, 0) + 1);

-- MENU: view actions

INSERT INTO `sys_objects_menu`(`object`, `title`, `set_name`, `module`, `template_id`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_spaces_view_actions', '_bx_spaces_menu_title_view_profile_actions', 'bx_spaces_view_actions', 'bx_spaces', 9, 0, 1, 'BxSpacesMenuView', 'modules/boonex/spaces/classes/BxSpacesMenuView.php');

INSERT INTO `sys_menu_sets`(`set_name`, `module`, `title`, `deletable`) VALUES 
('bx_spaces_view_actions', 'bx_spaces', '_bx_spaces_menu_set_title_view_profile_actions', 0);

INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `submenu_popup`, `visible_for_levels`, `active`, `copyable`, `editable`, `order`) VALUES 
('bx_spaces_view_actions', 'bx_spaces', 'profile-fan-add', '_bx_spaces_menu_item_title_system_become_fan', '{title_add_fan}', 'javascript:void(0)', 'bx_conn_action(this, \'bx_spaces_fans\', \'add\', \'{profile_id}\')', '', 'sign-in-alt', '', 0, 2147483647, 1, 0, 0, 5),
('bx_spaces_view_actions', 'bx_spaces', 'profile-subscribe-add', '_bx_spaces_menu_item_title_system_subscribe', '_bx_spaces_menu_item_title_subscribe', 'javascript:void(0)', 'bx_conn_action(this, \'sys_profiles_subscriptions\', \'add\', \'{profile_id}\')', '', 'check', '', 0, 2147483647, 1, 0, 1, 20),
('bx_spaces_view_actions', 'bx_spaces', 'profile-actions-more', '_bx_spaces_menu_item_title_system_more_actions', '_bx_spaces_menu_item_title_more_actions', 'javascript:void(0)', 'bx_menu_popup(''bx_spaces_view_actions_more'', this, {}, {profile_id:{profile_id}});', '', 'cog', 'bx_spaces_view_actions_more', 1, 2147483647, 1, 0, 1, 9999);

-- MENU: view actions more

INSERT INTO `sys_objects_menu`(`object`, `title`, `set_name`, `module`, `template_id`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_spaces_view_actions_more', '_bx_spaces_menu_title_view_profile_actions_more', 'bx_spaces_view_actions_more', 'bx_spaces', 6, 0, 1, 'BxSpacesMenuView', 'modules/boonex/spaces/classes/BxSpacesMenuView.php');

INSERT INTO `sys_menu_sets`(`set_name`, `module`, `title`, `deletable`) VALUES 
('bx_spaces_view_actions_more', 'bx_spaces', '_bx_spaces_menu_set_title_view_profile_actions_more', 0);

INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('bx_spaces_view_actions_more', 'bx_spaces', 'profile-fan-remove', '_bx_spaces_menu_item_title_system_leave_space', '{title_remove_fan}', 'javascript:void(0)', 'bx_conn_action(this, \'bx_spaces_fans\', \'remove\', \'{profile_id}\')', '', 'sign-out-alt', '', 2147483647, 1, 0, 10),
('bx_spaces_view_actions_more', 'bx_spaces', 'profile-subscribe-remove', '_bx_spaces_menu_item_title_system_unsubscribe', '_bx_spaces_menu_item_title_unsubscribe', 'javascript:void(0)', 'bx_conn_action(this, \'sys_profiles_subscriptions\', \'remove\', \'{profile_id}\')', '', 'check', '', 2147483647, 1, 0, 20),
('bx_spaces_view_actions_more', 'bx_spaces', 'edit-space-cover', '_bx_spaces_menu_item_title_system_edit_cover', '_bx_spaces_menu_item_title_edit_cover', 'page.php?i=edit-space-cover&id={content_id}', '', '', 'edit', '', 2147483647, 1, 0, 30),
('bx_spaces_view_actions_more', 'bx_spaces', 'edit-space-profile', '_bx_spaces_menu_item_title_system_edit_profile', '_bx_spaces_menu_item_title_edit_profile', 'page.php?i=edit-space-profile&id={content_id}', '', '', 'pencil-alt', '', 2147483647, 1, 0, 40),
('bx_spaces_view_actions_more', 'bx_spaces', 'invite-to-space', '_bx_spaces_menu_item_title_system_invite', '_bx_spaces_menu_item_title_invite', 'page.php?i=invite-to-space&id={content_id}', '', '', 'user-friends', '', 2147483647, 1, 0, 42),
('bx_spaces_view_actions_more', 'bx_spaces', 'delete-space-profile', '_bx_spaces_menu_item_title_system_delete_profile', '_bx_spaces_menu_item_title_delete_profile', 'page.php?i=delete-space-profile&id={content_id}', '', '', 'remove', '', 2147483647, 1, 0, 50);

-- MENU: all actions menu for view entry 

INSERT INTO `sys_objects_menu`(`object`, `title`, `set_name`, `module`, `template_id`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_spaces_view_actions_all', '_sys_menu_title_view_actions', 'bx_spaces_view_actions_all', 'bx_spaces', 15, 0, 1, 'BxSpacesMenuViewActionsAll', 'modules/boonex/spaces/classes/BxSpacesMenuViewActionsAll.php');

INSERT INTO `sys_menu_sets`(`set_name`, `module`, `title`, `deletable`) VALUES 
('bx_spaces_view_actions_all', 'bx_spaces', '_sys_menu_set_title_view_actions', 0);

INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `addon`, `submenu_object`, `submenu_popup`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('bx_spaces_view_actions_all', 'bx_spaces', 'profile-fan-add', '_bx_spaces_menu_item_title_system_become_fan', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 10),
('bx_spaces_view_actions_all', 'bx_spaces', 'profile-fan-remove', '_bx_spaces_menu_item_title_system_leave_group', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 20),
('bx_spaces_view_actions_all', 'bx_spaces', 'profile-subscribe-add', '_bx_spaces_menu_item_title_system_subscribe', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 30),
('bx_spaces_view_actions_all', 'bx_spaces', 'profile-subscribe-remove', '_bx_spaces_menu_item_title_system_unsubscribe', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 40),
('bx_spaces_view_actions_all', 'bx_spaces', 'comment', '_sys_menu_item_title_system_va_comment', '', '', '', '', '', '', '', 0, 2147483647, 0, 0, 200),
('bx_spaces_view_actions_all', 'bx_spaces', 'view', '_sys_menu_item_title_system_va_view', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 210),
('bx_spaces_view_actions_all', 'bx_spaces', 'vote', '_sys_menu_item_title_system_va_vote', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 220),
('bx_spaces_view_actions_all', 'bx_spaces', 'score', '_sys_menu_item_title_system_va_score', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 230),
('bx_spaces_view_actions_all', 'bx_spaces', 'favorite', '_sys_menu_item_title_system_va_favorite', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 240),
('bx_spaces_view_actions_all', 'bx_spaces', 'feature', '_sys_menu_item_title_system_va_feature', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 250),
('bx_spaces_view_actions_all', 'bx_spaces', 'repost', '_sys_menu_item_title_system_va_repost', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 260),
('bx_spaces_view_actions_all', 'bx_spaces', 'report', '_sys_menu_item_title_system_va_report', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 270),
('bx_spaces_view_actions_all', 'bx_spaces', 'social-sharing-facebook', '_sys_menu_item_title_system_social_sharing_facebook', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 300),
('bx_spaces_view_actions_all', 'bx_spaces', 'social-sharing-googleplus', '_sys_menu_item_title_system_social_sharing_googleplus', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 310),
('bx_spaces_view_actions_all', 'bx_spaces', 'social-sharing-twitter', '_sys_menu_item_title_system_social_sharing_twitter', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 320),
('bx_spaces_view_actions_all', 'bx_spaces', 'social-sharing-pinterest', '_sys_menu_item_title_system_social_sharing_pinterest', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 330),
('bx_spaces_view_actions_all', 'bx_spaces', 'edit-space-cover', '_bx_spaces_menu_item_title_system_edit_cover', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 400),
('bx_spaces_view_actions_all', 'bx_spaces', 'edit-space-profile', '_bx_spaces_menu_item_title_system_edit_profile', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 410),
('bx_spaces_view_actions_all', 'bx_spaces', 'invite-to-space', '_bx_spaces_menu_item_title_system_invite', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 420),
('bx_spaces_view_actions_all', 'bx_spaces', 'delete-space-profile', '_bx_spaces_menu_item_title_system_delete_profile', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 430),
('bx_spaces_view_actions_all', 'bx_spaces', 'more-auto', '_sys_menu_item_title_system_va_more_auto', '_sys_menu_item_title_va_more_auto', 'javascript:void(0)', '', '', 'ellipsis-v', '', '', 0, 2147483647, 1, 0, 9999);

-- MENU: actions menu for my entries

INSERT INTO `sys_objects_menu`(`object`, `title`, `set_name`, `module`, `template_id`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_spaces_my', '_bx_spaces_menu_title_entries_my', 'bx_spaces_my', 'bx_spaces', 9, 0, 1, 'BxSpacesMenu', 'modules/boonex/spaces/classes/BxSpacesMenu.php');

INSERT INTO `sys_menu_sets`(`set_name`, `module`, `title`, `deletable`) VALUES 
('bx_spaces_my', 'bx_spaces', '_bx_spaces_menu_set_title_entries_my', 0);

INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('bx_spaces_my', 'bx_spaces', 'create-space-profile', '_bx_spaces_menu_item_title_system_create_profile', '_bx_spaces_menu_item_title_create_profile', 'page.php?i=create-space-profile', '', '', 'plus', '', 2147483647, 1, 0, 0);

-- MENU: module sub-menu

INSERT INTO `sys_objects_menu`(`object`, `title`, `set_name`, `module`, `template_id`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_spaces_submenu', '_bx_spaces_menu_title_submenu', 'bx_spaces_submenu', 'bx_spaces', 8, 0, 1, '', '');

INSERT INTO `sys_menu_sets`(`set_name`, `module`, `title`, `deletable`) VALUES 
('bx_spaces_submenu', 'bx_spaces', '_bx_spaces_menu_set_title_submenu', 0);

INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('bx_spaces_submenu', 'bx_spaces', 'spaces-home', '_bx_spaces_menu_item_title_system_entries_recent', '_bx_spaces_menu_item_title_entries_recent', 'page.php?i=spaces-home', '', '', '', '', 2147483647, 1, 1, 1),
('bx_spaces_submenu', 'bx_spaces', 'spaces-top', '_bx_spaces_menu_item_title_system_entries_top', '_bx_spaces_menu_item_title_entries_top', 'page.php?i=spaces-top', '', '', '', '', 2147483647, 1, 1, 2),
('bx_spaces_submenu', 'bx_spaces', 'spaces-search', '_bx_spaces_menu_item_title_system_entries_search', '_bx_spaces_menu_item_title_entries_search', 'page.php?i=spaces-search', '', '', '', '', 2147483647, 1, 1, 3),
('bx_spaces_submenu', 'bx_spaces', 'spaces-manage', '_bx_spaces_menu_item_title_system_entries_manage', '_bx_spaces_menu_item_title_entries_manage', 'page.php?i=spaces-manage', '', '', '', '', 2147483646, 1, 1, 4);

-- MENU: view submenu

INSERT INTO `sys_objects_menu`(`object`, `title`, `set_name`, `module`, `template_id`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_spaces_view_submenu', '_bx_spaces_menu_title_view_profile_submenu', 'bx_spaces_view_submenu', 'bx_spaces', 8, 0, 1, 'BxSpacesMenuView', 'modules/boonex/spaces/classes/BxSpacesMenuView.php'),
('bx_spaces_view_submenu_cover', '_bx_spaces_menu_title_view_profile_submenu_cover', 'bx_spaces_view_submenu', 'bx_spaces', 7, 0, 1, 'BxSpacesMenuView', 'modules/boonex/spaces/classes/BxSpacesMenuView.php');

INSERT INTO `sys_menu_sets`(`set_name`, `module`, `title`, `deletable`) VALUES 
('bx_spaces_view_submenu', 'bx_spaces', '_bx_spaces_menu_set_title_view_profile_submenu', 0);

INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('bx_spaces_view_submenu', 'bx_spaces', 'view-space-profile', '_bx_spaces_menu_item_title_system_view_profile_view', '_bx_spaces_menu_item_title_view_profile_view', 'page.php?i=view-space-profile&id={content_id}', '', '', 'object-group col-red2', '', 2147483647, 1, 0, 1),
('bx_spaces_view_submenu', 'bx_spaces', 'space-profile-info', '_bx_spaces_menu_item_title_system_view_profile_info', '_bx_spaces_menu_item_title_view_profile_info', 'page.php?i=space-profile-info&id={content_id}', '', '', 'info-circle col-gray', '', 2147483647, 1, 0, 2),
('bx_spaces_view_submenu', 'bx_spaces', 'space-profile-comments', '_bx_spaces_menu_item_title_system_view_profile_comments', '_bx_spaces_menu_item_title_view_profile_comments', 'page.php?i=space-profile-comments&id={content_id}', '', '', '', '', 2147483647, 0, 0, 3),
('bx_spaces_view_submenu', 'bx_spaces', 'space-fans', '_bx_spaces_menu_item_title_system_view_fans', '_bx_spaces_menu_item_title_view_fans', 'page.php?i=space-fans&profile_id={profile_id}', '', '', 'object-group col-blue3', '', 2147483647, 1, 0, 4);

-- MENU: custom menu for snippet meta info
INSERT INTO `sys_objects_menu`(`object`, `title`, `set_name`, `module`, `template_id`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_spaces_snippet_meta', '_sys_menu_title_snippet_meta', 'bx_spaces_snippet_meta', 'bx_spaces', 15, 0, 1, 'BxSpacesMenuSnippetMeta', 'modules/boonex/spaces/classes/BxSpacesMenuSnippetMeta.php');

INSERT INTO `sys_menu_sets`(`set_name`, `module`, `title`, `deletable`) VALUES 
('bx_spaces_snippet_meta', 'bx_spaces', '_sys_menu_set_title_snippet_meta', 0);

INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `editable`, `order`) VALUES
('bx_spaces_snippet_meta', 'bx_spaces', 'join', '_sys_menu_item_title_system_sm_join', '_sys_menu_item_title_sm_join', '', '', '', '', '', 2147483647, 1, 0, 1, 1),
('bx_spaces_snippet_meta', 'bx_spaces', 'leave', '_sys_menu_item_title_system_sm_leave', '_sys_menu_item_title_sm_leave', '', '', '', '', '', 2147483647, 0, 0, 1, 2),
('bx_spaces_snippet_meta', 'bx_spaces', 'subscribe', '_sys_menu_item_title_system_sm_subscribe', '_sys_menu_item_title_sm_subscribe', '', '', '', '', '', 2147483647, 0, 0, 1, 3),
('bx_spaces_snippet_meta', 'bx_spaces', 'unsubscribe', '_sys_menu_item_title_system_sm_unsubscribe', '_sys_menu_item_title_sm_unsubscribe', '', '', '', '', '', 2147483647, 0, 0, 1, 4),
('bx_spaces_snippet_meta', 'bx_spaces', 'date', '_sys_menu_item_title_system_sm_date', '_sys_menu_item_title_sm_date', '', '', '', '', '', 2147483647, 0, 0, 1, 5),
('bx_spaces_snippet_meta', 'bx_spaces', 'tags', '_sys_menu_item_title_system_sm_tags', '_sys_menu_item_title_sm_tags', '', '', '', '', '', 2147483647, 0, 0, 1, 6),
('bx_spaces_snippet_meta', 'bx_spaces', 'views', '_sys_menu_item_title_system_sm_views', '_sys_menu_item_title_sm_views', '', '', '', '', '', 2147483647, 0, 0, 1, 7),
('bx_spaces_snippet_meta', 'bx_spaces', 'comments', '_sys_menu_item_title_system_sm_comments', '_sys_menu_item_title_sm_comments', '', '', '', '', '', 2147483647, 0, 0, 1, 8),
('bx_spaces_snippet_meta', 'bx_spaces', 'members', '_sys_menu_item_title_system_sm_members', '_sys_menu_item_title_sm_members', '', '', '', '', '', 2147483647, 1, 0, 1, 9),
('bx_spaces_snippet_meta', 'bx_spaces', 'subscribers', '_sys_menu_item_title_system_sm_subscribers', '_sys_menu_item_title_sm_subscribers', '', '', '', '', '', 2147483647, 0, 0, 1, 10);

-- MENU: profile stats
SET @iNotifMenuOrder = (SELECT IFNULL(MAX(`order`), 0) FROM `sys_menu_items` WHERE `set_name` = 'sys_profile_stats' AND `active` = 1 LIMIT 1);
INSERT INTO `sys_menu_items` (`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `addon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES
('sys_profile_stats', 'bx_spaces', 'profile-stats-manage-spaces', '_bx_spaces_menu_item_title_system_manage_my_spaces', '_bx_spaces_menu_item_title_manage_my_spaces', 'page.php?i=spaces-manage', '', '_self', 'object-group col-red2', 'a:2:{s:6:"module";s:9:"bx_spaces";s:6:"method";s:41:"get_menu_addon_manage_tools_profile_stats";}', '', 2147483646, 1, 0, @iNotifMenuOrder + 2);

-- MENU: manage tools submenu
INSERT INTO `sys_objects_menu`(`object`, `title`, `set_name`, `module`, `template_id`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_spaces_menu_manage_tools', '_bx_spaces_menu_title_manage_tools', 'bx_spaces_menu_manage_tools', 'bx_spaces', 6, 0, 1, 'BxSpacesMenuManageTools', 'modules/boonex/spaces/classes/BxSpacesMenuManageTools.php');

INSERT INTO `sys_menu_sets`(`set_name`, `module`, `title`, `deletable`) VALUES 
('bx_spaces_menu_manage_tools', 'bx_spaces', '_bx_spaces_menu_set_title_manage_tools', 0);

INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('bx_spaces_menu_manage_tools', 'bx_spaces', 'delete', '_bx_spaces_menu_item_title_system_delete', '_bx_spaces_menu_item_title_delete', 'javascript:void(0)', 'javascript:{js_object}.onClickDelete({content_id});', '_self', 'far trash-alt', '', 2147483647, 1, 0, 1),
('bx_spaces_menu_manage_tools', 'bx_spaces', 'delete-with-content', '_bx_spaces_menu_item_title_system_delete_with_content', '_bx_spaces_menu_item_title_delete_with_content', 'javascript:void(0)', 'javascript:{js_object}.onClickDeleteWithContent({content_id});', '_self', 'far trash-alt', '', 2147483647, 1, 0, 2);

-- MENU: dashboard manage tools
SET @iManageMenuOrder = (SELECT IFNULL(MAX(`order`), 0) FROM `sys_menu_items` WHERE `set_name`='sys_account_dashboard_manage_tools' LIMIT 1);
INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `addon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('sys_account_dashboard_manage_tools', 'bx_spaces', 'spaces-administration', '_bx_spaces_menu_item_title_system_admt_spaces', '_bx_spaces_menu_item_title_admt_spaces', 'page.php?i=spaces-administration', '', '_self', '', 'a:2:{s:6:"module";s:9:"bx_spaces";s:6:"method";s:27:"get_menu_addon_manage_tools";}', '', 192, 1, 0, @iManageMenuOrder + 1);

-- MENU: add menu item to profiles modules (trigger* menu sets are processed separately upon modules enable/disable)

INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('trigger_profile_view_submenu', 'bx_spaces', 'joined-spaces', '_bx_spaces_menu_item_title_system_view_joined_spaces', '_bx_spaces_menu_item_title_view_joined_spaces', 'page.php?i=joined-spaces&profile_id={profile_id}', '', '', 'object-group col-red2', '', 2147483647, 1, 0, 0);

-- ACL
INSERT INTO `sys_acl_actions` (`Module`, `Name`, `AdditionalParamName`, `Title`, `Desc`, `Countable`, `DisabledForLevels`) VALUES
('bx_spaces', 'create entry', NULL, '_bx_spaces_acl_action_create_profile', '', 1, 1);
SET @iIdActionProfileCreate = LAST_INSERT_ID();

INSERT INTO `sys_acl_actions` (`Module`, `Name`, `AdditionalParamName`, `Title`, `Desc`, `Countable`, `DisabledForLevels`) VALUES
('bx_spaces', 'delete entry', NULL, '_bx_spaces_acl_action_delete_profile', '', 1, 1);
SET @iIdActionProfileDelete = LAST_INSERT_ID();

INSERT INTO `sys_acl_actions` (`Module`, `Name`, `AdditionalParamName`, `Title`, `Desc`, `Countable`, `DisabledForLevels`) VALUES
('bx_spaces', 'view entry', NULL, '_bx_spaces_acl_action_view_profile', '', 1, 0);
SET @iIdActionProfileView = LAST_INSERT_ID();

INSERT INTO `sys_acl_actions` (`Module`, `Name`, `AdditionalParamName`, `Title`, `Desc`, `Countable`, `DisabledForLevels`) VALUES
('bx_spaces', 'edit any entry', NULL, '_bx_spaces_acl_action_edit_any_profile', '', 1, 3);
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
('bx_spaces', 'bx_spaces', 'bx_spaces_cmts', 1, 5000, 1000, 3, 5, 3, 'tail', 1, 'bottom', 1, 1, 1, -3, 1, 'cmt', 'page.php?i=view-space-profile&id={object_id}', '', 'bx_spaces_data', 'id', 'author', 'space_name', 'comments', '', '');

-- VIEWS
INSERT INTO `sys_objects_view` (`name`, `table_track`, `period`, `is_on`, `trigger_table`, `trigger_field_id`, `trigger_field_author`, `trigger_field_count`, `class_name`, `class_file`) VALUES 
('bx_spaces', 'bx_spaces_views_track', '86400', '1', 'bx_spaces_data', 'id', 'author', 'views', '', '');

-- VOTES
INSERT INTO `sys_objects_vote` (`Name`, `TableMain`, `TableTrack`, `PostTimeout`, `MinValue`, `MaxValue`, `IsUndo`, `IsOn`, `TriggerTable`, `TriggerFieldId`, `TriggerFieldAuthor`, `TriggerFieldRate`, `TriggerFieldRateCount`, `ClassName`, `ClassFile`) VALUES 
('bx_spaces', 'bx_spaces_votes', 'bx_spaces_votes_track', '604800', '1', '1', '0', '1', 'bx_spaces_data', 'id', 'author', 'rate', 'votes', '', '');

-- SCORES
INSERT INTO `sys_objects_score` (`name`, `module`, `table_main`, `table_track`, `post_timeout`, `is_on`, `trigger_table`, `trigger_field_id`, `trigger_field_author`, `trigger_field_score`, `trigger_field_cup`, `trigger_field_cdown`, `class_name`, `class_file`) VALUES 
('bx_spaces', 'bx_spaces', 'bx_spaces_scores', 'bx_spaces_scores_track', '604800', '0', 'bx_spaces_data', 'id', 'author', 'score', 'sc_up', 'sc_down', '', '');

-- REPORTS
INSERT INTO `sys_objects_report` (`name`, `table_main`, `table_track`, `is_on`, `base_url`, `trigger_table`, `trigger_field_id`, `trigger_field_author`, `trigger_field_count`, `class_name`, `class_file`) VALUES 
('bx_spaces', 'bx_spaces_reports', 'bx_spaces_reports_track', '1', 'page.php?i=view-space-profile&id={object_id}', 'bx_spaces_data', 'id', 'author', 'reports', '', '');

-- FAFORITES
INSERT INTO `sys_objects_favorite` (`name`, `table_track`, `is_on`, `is_undo`, `is_public`, `base_url`, `trigger_table`, `trigger_field_id`, `trigger_field_author`, `trigger_field_count`, `class_name`, `class_file`) VALUES 
('bx_spaces', 'bx_spaces_favorites_track', '1', '1', '1', 'page.php?i=view-space-profile&id={object_id}', 'bx_spaces_data', 'id', 'author', 'favorites', '', '');

-- FEATURED
INSERT INTO `sys_objects_feature` (`name`, `is_on`, `is_undo`, `base_url`, `trigger_table`, `trigger_field_id`, `trigger_field_author`, `trigger_field_flag`, `class_name`, `class_file`) VALUES 
('bx_spaces', '1', '1', 'page.php?i=view-space-profile&id={object_id}', 'bx_spaces_data', 'id', 'author', 'featured', '', '');

-- METATAGS
INSERT INTO `sys_objects_metatags` (`object`, `table_keywords`, `table_locations`, `table_mentions`, `override_class_name`, `override_class_file`) VALUES
('bx_spaces', 'bx_spaces_meta_keywords', 'bx_spaces_meta_locations', 'bx_spaces_meta_mentions', '', '');

-- CATEGORY
INSERT INTO `sys_objects_category` (`object`, `search_object`, `form_object`, `list_name`, `table`, `field`, `join`, `where`, `override_class_name`, `override_class_file`) VALUES
('bx_spaces_cats', 'bx_spaces', 'bx_space', 'bx_spaces_cats', 'bx_spaces_data', 'space_cat', 'INNER JOIN `sys_profiles` ON (`sys_profiles`.`content_id` = `bx_spaces_data`.`id` AND `sys_profiles`.`type` = ''bx_spaces'')', 'AND `sys_profiles`.`status` = ''active''', '', '');

-- SEARCH
SET @iSearchOrder = (SELECT IFNULL(MAX(`Order`), 0) FROM `sys_objects_search`);
INSERT INTO `sys_objects_search` (`ObjectName`, `Title`, `Order`, `ClassName`, `ClassPath`) VALUES
('bx_spaces', '_bx_spaces', @iSearchOrder + 1, 'BxSpacesSearchResult', 'modules/boonex/spaces/classes/BxSpacesSearchResult.php');

-- CONNECTIONS
INSERT INTO `sys_objects_connection` (`object`, `table`, `type`, `override_class_name`, `override_class_file`) VALUES
('bx_spaces_fans', 'bx_spaces_fans', 'mutual', '', '');

-- STATS
SET @iMaxOrderStats = (SELECT IFNULL(MAX(`order`), 0) FROM `sys_statistics`);
INSERT INTO `sys_statistics` (`module`, `name`, `title`, `link`, `icon`, `query`, `order`) VALUES 
('bx_spaces', 'bx_spaces', '_bx_spaces', 'page.php?i=spaces-home', 'object-group col-red2', 'SELECT COUNT(*) FROM `bx_spaces_data` AS `td` LEFT JOIN `sys_profiles` AS `tp` ON `td`.`id` = `tp`.`content_id` AND `tp`.`type`=''bx_spaces'' WHERE 1 AND `tp`.`status`=''active''', @iMaxOrderStats + 1);

-- CHARTS
SET @iMaxOrderCharts = (SELECT IFNULL(MAX(`order`), 0) FROM `sys_objects_chart`);
INSERT INTO `sys_objects_chart` (`object`, `title`, `table`, `field_date_ts`, `field_date_dt`, `field_status`, `query`, `active`, `order`, `class_name`, `class_file`) VALUES
('bx_spaces_growth', '_bx_spaces_chart_growth', 'bx_spaces_data', 'added', '', '', 'SELECT {field_date_formatted} AS `period`, COUNT(*) AS {object} FROM {table} LEFT JOIN `sys_profiles` AS `tp` ON {table}.`id` = `tp`.`content_id` AND `tp`.`type`=''bx_spaces'' WHERE 1 AND `tp`.`status`=''active'' {where_inteval} GROUP BY `period` ORDER BY {table}.{field_date} ASC', 1, @iMaxOrderCharts + 1, 'BxDolChartGrowth', ''),
('bx_spaces_growth_speed', '_bx_spaces_chart_growth_speed', 'bx_spaces_data', 'added', '', '', 'SELECT {field_date_formatted} AS `period`, COUNT(*) AS {object} FROM {table} LEFT JOIN `sys_profiles` AS `tp` ON {table}.`id` = `tp`.`content_id` AND `tp`.`type`=''bx_spaces'' WHERE 1 AND `tp`.`status`=''active'' {where_inteval} GROUP BY `period` ORDER BY {table}.{field_date} ASC', 1, @iMaxOrderCharts + 2, 'BxDolChartGrowthSpeed', '');

-- GRID: connections
INSERT INTO `sys_objects_grid` (`object`, `source_type`, `source`, `table`, `field_id`, `field_order`, `field_active`, `paginate_url`, `paginate_per_page`, `paginate_simple`, `paginate_get_start`, `paginate_get_per_page`, `filter_fields`, `filter_fields_translatable`, `filter_mode`, `sorting_fields`, `sorting_fields_translatable`, `visible_for_levels`, `override_class_name`, `override_class_file`) VALUES
('bx_spaces_fans', 'Sql', 'SELECT `p`.`id`, `c`.`added`, `c`.`mutual` FROM `sys_profiles` AS `p` INNER JOIN `sys_accounts` AS `a` ON (`a`.`id` = `p`.`account_id`) {join_connections}', 'sys_profiles', 'id', 'c`.`added', '', '', 10, NULL, 'start', '', 'name,email', '', 'auto', '', '', 2147483647, 'BxSpacesGridConnections', 'modules/boonex/spaces/classes/BxSpacesGridConnections.php');

INSERT INTO `sys_grid_fields` (`object`, `name`, `title`, `width`, `params`, `order`) VALUES
('bx_spaces_fans', 'name', '_sys_name', '50%', '', 10),
('bx_spaces_fans', 'actions', '', '50%', '', 20);

INSERT INTO `sys_grid_actions` (`object`, `type`, `name`, `title`, `icon`, `confirm`, `order`) VALUES
('bx_spaces_fans', 'single', 'accept', '_sys_accept', '', 0, 10),
('bx_spaces_fans', 'single', 'to_admins', '_bx_spaces_txt_to_admins', '', 0, 20),
('bx_spaces_fans', 'single', 'from_admins', '_bx_spaces_txt_from_admins', '', 0, 30),
('bx_spaces_fans', 'single', 'delete', '', 'remove', 1, 40);

-- GRIDS: administration

INSERT INTO `sys_objects_grid` (`object`, `source_type`, `source`, `table`, `field_id`, `field_order`, `field_active`, `paginate_url`, `paginate_per_page`, `paginate_simple`, `paginate_get_start`, `paginate_get_per_page`, `filter_fields`, `filter_fields_translatable`, `filter_mode`, `sorting_fields`, `sorting_fields_translatable`, `visible_for_levels`, `override_class_name`, `override_class_file`) VALUES
('bx_spaces_administration', 'Sql', 'SELECT `td`.*, `td`.`space_name` AS `name`, `ta`.`email` AS `account`, `td`.`added` AS `added_ts`, `tp`.`status` AS `status` FROM `bx_spaces_data` AS `td` LEFT JOIN `sys_profiles` AS `tp` ON `td`.`id`=`tp`.`content_id` AND `tp`.`type`=''bx_spaces'' LEFT JOIN `sys_accounts` AS `ta` ON `tp`.`account_id`=`ta`.`id` WHERE 1 ', 'bx_spaces_data', 'id', 'added', 'status', '', 20, NULL, 'start', '', 'space_name', '', 'like', 'reports', '', 192, 'BxSpacesGridAdministration', 'modules/boonex/spaces/classes/BxSpacesGridAdministration.php'),
('bx_spaces_common', 'Sql', 'SELECT `td`.*, `td`.`space_name` AS `name`, `ta`.`email` AS `account`, `td`.`added` AS `added_ts`, `tp`.`status` AS `status` FROM `bx_spaces_data` AS `td` LEFT JOIN `sys_profiles` AS `tp` ON `td`.`id`=`tp`.`content_id` AND `tp`.`type`=''bx_spaces'' LEFT JOIN `sys_accounts` AS `ta` ON `tp`.`account_id`=`ta`.`id` WHERE 1 ', 'bx_spaces_data', 'id', 'added', 'status', '', 20, NULL, 'start', '', 'space_name', '', 'like', '', '', 2147483647, 'BxSpacesGridCommon', 'modules/boonex/spaces/classes/BxSpacesGridCommon.php');

INSERT INTO `sys_grid_fields` (`object`, `name`, `title`, `width`, `translatable`, `chars_limit`, `params`, `order`) VALUES
('bx_spaces_administration', 'checkbox', '_sys_select', '2%', 0, '', '', 1),
('bx_spaces_administration', 'switcher', '_bx_spaces_grid_column_title_adm_active', '8%', 0, '', '', 2),
('bx_spaces_administration', 'reports', '_sys_txt_reports_title', '5%', 0, '', '', 3),
('bx_spaces_administration', 'name', '_bx_spaces_grid_column_title_adm_name', '25%', 0, '', '', 4),
('bx_spaces_administration', 'added_ts', '_bx_spaces_grid_column_title_adm_added', '20%', 1, '25', '', 5),
('bx_spaces_administration', 'account', '_bx_spaces_grid_column_title_adm_account', '20%', 0, '25', '', 6),
('bx_spaces_administration', 'actions', '', '20%', 0, '', '', 7),
('bx_spaces_common', 'checkbox', '_sys_select', '2%', 0, '', '', 1),
('bx_spaces_common', 'name', '_bx_spaces_grid_column_title_adm_name', '48%', 0, '', '', 2),
('bx_spaces_common', 'added_ts', '_bx_spaces_grid_column_title_adm_added', '30%', 1, '25', '', 3),
('bx_spaces_common', 'actions', '', '20%', 0, '', '', 4);

INSERT INTO `sys_grid_actions` (`object`, `type`, `name`, `title`, `icon`, `icon_only`, `confirm`, `order`) VALUES
('bx_spaces_administration', 'bulk', 'delete', '_bx_spaces_grid_action_title_adm_delete', '', 0, 1, 2),
('bx_spaces_administration', 'bulk', 'delete_with_content', '_bx_spaces_grid_action_title_adm_delete_with_content', '', 0, 1, 3),
('bx_spaces_administration', 'single', 'settings', '_bx_spaces_grid_action_title_adm_more_actions', 'cog', 1, 0, 2),
('bx_spaces_common', 'bulk', 'delete', '_bx_spaces_grid_action_title_adm_delete', '', 0, 1, 1),
('bx_spaces_common', 'bulk', 'delete_with_content', '_bx_spaces_grid_action_title_adm_delete_with_content', '', 0, 1, 2),
('bx_spaces_common', 'single', 'settings', '_bx_spaces_grid_action_title_adm_more_actions', 'cog', 1, 0, 1);


-- ALERTS

INSERT INTO `sys_alerts_handlers` (`name`, `class`, `file`, `service_call`) VALUES 
('bx_spaces', 'BxSpacesAlertsResponse', 'modules/boonex/spaces/classes/BxSpacesAlertsResponse.php', '');
SET @iHandler := LAST_INSERT_ID();

INSERT INTO `sys_alerts` (`unit`, `action`, `handler_id`) VALUES
('system', 'save_setting', @iHandler),
('bx_timeline', 'post_common', @iHandler),
('bx_spaces_pics', 'file_deleted', @iHandler),
('bx_spaces_fans', 'connection_added', @iHandler),
('bx_spaces_fans', 'connection_removed', @iHandler),
('profile', 'delete', @iHandler),
('bx_spaces', 'fan_added', @iHandler),
('bx_spaces', 'join_invitation', @iHandler),
('bx_spaces', 'join_request', @iHandler),
('bx_spaces', 'join_request_accepted', @iHandler),
('bx_spaces', 'timeline_view', @iHandler),
('bx_spaces', 'timeline_post', @iHandler),
('bx_spaces', 'timeline_delete', @iHandler),
('bx_spaces', 'timeline_comment', @iHandler),
('bx_spaces', 'timeline_vote', @iHandler),
('bx_spaces', 'timeline_report', @iHandler),
('bx_spaces', 'timeline_repost', @iHandler);

-- PRIVACY 

INSERT INTO `sys_objects_privacy` (`object`, `module`, `action`, `title`, `default_group`, `spaces`, `table`, `table_field_id`, `table_field_author`, `override_class_name`, `override_class_file`) VALUES
('bx_spaces_allow_view_to', 'bx_spaces', 'view', '_bx_spaces_form_profile_input_allow_view_to', '3', '', 'bx_spaces_data', 'id', 'author', 'BxSpacesPrivacy', 'modules/boonex/spaces/classes/BxSpacesPrivacy.php'),
('bx_spaces_allow_view_notification_to', 'bx_spaces', 'view_event', '_bx_spaces_form_profile_input_allow_view_notification_to', '3', '', 'bx_notifications_events', 'id', 'object_owner_id', 'BxSpacesPrivacyNotifications', 'modules/boonex/spaces/classes/BxSpacesPrivacyNotifications.php');

-- EMAIL TEMPLATES

INSERT INTO `sys_email_templates` (`Module`, `NameSystem`, `Name`, `Subject`, `Body`) VALUES
('bx_spaces', '_bx_spaces_email_join_request', 'bx_spaces_join_request', '_bx_spaces_email_join_request_subject', '_bx_spaces_email_join_request_body'),
('bx_spaces', '_bx_spaces_email_join_reject', 'bx_spaces_join_reject', '_bx_spaces_email_join_reject_subject', '_bx_spaces_email_join_reject_body'),
('bx_spaces', '_bx_spaces_email_join_confirm', 'bx_spaces_join_confirm', '_bx_spaces_email_join_confirm_subject', '_bx_spaces_email_join_confirm_body'),
('bx_spaces', '_bx_spaces_email_fan_remove', 'bx_spaces_fan_remove', '_bx_spaces_email_fan_remove_subject', '_bx_spaces_email_fan_remove_body'),
('bx_spaces', '_bx_spaces_email_fan_become_admin', 'bx_spaces_fan_become_admin', '_bx_spaces_email_fan_become_admin_subject', '_bx_spaces_email_fan_become_admin_body'),
('bx_spaces', '_bx_spaces_email_admin_become_fan', 'bx_spaces_admin_become_fan', '_bx_spaces_email_admin_become_fan_subject', '_bx_spaces_email_admin_become_fan_body'),
('bx_spaces', '_bx_spaces_email_invitation', 'bx_spaces_invitation', '_bx_spaces_email_invitation_subject', '_bx_spaces_email_invitation_body');

-- UPLOADERS

INSERT INTO `sys_objects_uploader` (`object`, `active`, `override_class_name`, `override_class_file`) VALUES
('bx_spaces_cover_crop', 1, 'BxSpacesUploaderCoverCrop', 'modules/boonex/spaces/classes/BxSpacesUploaderCoverCrop.php'),
('bx_spaces_picture_crop', 1, 'BxSpacesUploaderPictureCrop', 'modules/boonex/spaces/classes/BxSpacesUploaderPictureCrop.php');

