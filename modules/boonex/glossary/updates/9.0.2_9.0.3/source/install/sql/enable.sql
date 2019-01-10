SET @iAdministratorModeratorAccess = 192;
-- SETTINGS

SET @iTypeOrder = (SELECT MAX(`order`) FROM `sys_options_types` WHERE `group` = 'modules');
INSERT INTO `sys_options_types`(`group`, `name`, `caption`, `icon`, `order`) VALUES 
('modules', 'bx_glossary', '_bx_glossary', 'bx_glossary@modules/boonex/glossary/|std-icon.svg', IF(ISNULL(@iTypeOrder), 1, @iTypeOrder + 1));
SET @iTypeId = LAST_INSERT_ID();

INSERT INTO `sys_options_categories` (`type_id`, `name`, `caption`, `order`)
VALUES (@iTypeId, 'bx_glossary', '_bx_glossary', 1);
SET @iCategId = LAST_INSERT_ID();

INSERT INTO `sys_options` (`name`, `value`, `category_id`, `caption`, `type`, `check`, `check_error`, `extra`, `order`) VALUES
('bx_glossary_summary_chars', '700', @iCategId, '_bx_glossary_option_summary_chars', 'digit', '', '', '', 1),
('bx_glossary_plain_summary_chars', '240', @iCategId, '_bx_glossary_option_plain_summary_chars', 'digit', '', '', '', 2),
('bx_glossary_per_page_browse', '12', @iCategId, '_bx_glossary_option_per_page_browse', 'digit', '', '', '', 10),
('bx_glossary_per_page_profile', '6', @iCategId, '_bx_glossary_option_per_page_profile', 'digit', '', '', '', 12),
('bx_glossary_per_page_browse_showcase', '32', @iCategId, '_sys_option_per_page_browse_showcase', 'digit', '', '', '', 15),
('bx_glossary_rss_num', '10', @iCategId, '_bx_glossary_option_rss_num', 'digit', '', '', '', 20),
('bx_glossary_searchable_fields', 'title,text', @iCategId, '_bx_glossary_option_searchable_fields', 'list', '', '', 'a:2:{s:6:"module";s:11:"bx_glossary";s:6:"method";s:21:"get_searchable_fields";}', 30),
('bx_glossary_activate_terms_after_creation', 'on', @iCategId, '_bx_glossary_option_activate_terms_after_creation', 'checkbox', '', '', '', 40);

-- PAGE: create entry

INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_glossary_create_entry', '_bx_glossary_page_title_sys_create_entry', '_bx_glossary_page_title_create_entry', 'bx_glossary', 5, 2147483647, 1, 'create-glossary', 'page.php?i=create-glossary', '', '', '', 0, 1, 0, 'BxGlsrPageBrowse', 'modules/boonex/glossary/classes/BxGlsrPageBrowse.php');

INSERT INTO `sys_pages_blocks` (`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES
('bx_glossary_create_entry', 1, 'bx_glossary', '_bx_glossary_page_block_title_create_entry', 11, 2147483647, 'service', 'a:2:{s:6:"module";s:11:"bx_glossary";s:6:"method";s:13:"entity_create";}', 0, 1, 1);


-- PAGE: edit entry

INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_glossary_edit_entry', '_bx_glossary_page_title_sys_edit_entry', '_bx_glossary_page_title_edit_entry', 'bx_glossary', 5, 2147483647, 1, 'edit-glossary', '', '', '', '', 0, 1, 0, 'BxGlsrPageEntry', 'modules/boonex/glossary/classes/BxGlsrPageEntry.php');

INSERT INTO `sys_pages_blocks` (`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES
('bx_glossary_edit_entry', 1, 'bx_glossary', '_bx_glossary_page_block_title_edit_entry', 11, 2147483647, 'service', 'a:2:{s:6:"module";s:11:"bx_glossary";s:6:"method";s:11:"entity_edit";}', 0, 0, 0);


-- PAGE: delete entry

INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_glossary_delete_entry', '_bx_glossary_page_title_sys_delete_entry', '_bx_glossary_page_title_delete_entry', 'bx_glossary', 5, 2147483647, 1, 'delete-glossary', '', '', '', '', 0, 1, 0, 'BxGlsrPageEntry', 'modules/boonex/glossary/classes/BxGlsrPageEntry.php');

INSERT INTO `sys_pages_blocks` (`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES
('bx_glossary_delete_entry', 1, 'bx_glossary', '_bx_glossary_page_block_title_delete_entry', 11, 2147483647, 'service', 'a:2:{s:6:"module";s:11:"bx_glossary";s:6:"method";s:13:"entity_delete";}', 0, 0, 0);


-- PAGE: view entry

INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_glossary_view_entry', '_bx_glossary_page_title_sys_view_entry', '_bx_glossary_page_title_view_entry', 'bx_glossary', 10, 2147483647, 1, 'view-glossary', '', '', '', '', 0, 1, 0, 'BxGlsrPageEntry', 'modules/boonex/glossary/classes/BxGlsrPageEntry.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES 
('bx_glossary_view_entry', 1, 'bx_glossary', '', '_bx_glossary_page_block_title_entry_text', 13, 2147483647, 'service', 'a:2:{s:6:\"module\";s:11:\"bx_glossary\";s:6:\"method\";s:17:\"entity_text_block\";}', 0, 0, 1, 0),
('bx_glossary_view_entry', 2, 'bx_glossary', '', '_bx_glossary_page_block_title_entry_author', 13, 2147483647, 'service', 'a:2:{s:6:\"module\";s:11:\"bx_glossary\";s:6:\"method\";s:13:\"entity_author\";}', 0, 0, 0, 0),
('bx_glossary_view_entry', 2, 'bx_glossary', '_bx_glossary_page_block_title_sys_entry_context', '_bx_glossary_page_block_title_entry_context', 13, 2147483647, 'service', 'a:2:{s:6:\"module\";s:11:\"bx_glossary\";s:6:\"method\";s:14:\"entity_context\";}', 0, 0, 1, 1),
('bx_glossary_view_entry', 3, 'bx_glossary', '', '_bx_glossary_page_block_title_entry_info', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:11:\"bx_glossary\";s:6:\"method\";s:11:\"entity_info\";}', 0, 0, 0, 1),
('bx_glossary_view_entry', 4, 'bx_glossary', '', '_bx_glossary_page_block_title_entry_all_actions', 13, 2147483647, 'service', 'a:2:{s:6:\"module\";s:11:\"bx_glossary\";s:6:\"method\";s:18:\"entity_all_actions\";}', 0, 0, 1, 0),
('bx_glossary_view_entry', 4, 'bx_glossary', '', '_bx_glossary_page_block_title_entry_actions', 13, 2147483647, 'service', 'a:2:{s:6:\"module\";s:11:\"bx_glossary\";s:6:\"method\";s:14:\"entity_actions\";}', 0, 0, 0, 1),
('bx_glossary_view_entry', 4, 'bx_glossary', '', '_bx_glossary_page_block_title_entry_social_sharing', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:11:\"bx_glossary\";s:6:\"method\";s:21:\"entity_social_sharing\";}', 0, 0, 0, 2),
('bx_glossary_view_entry', 4, 'bx_glossary', '', '_bx_glossary_page_block_title_entry_attachments', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:11:\"bx_glossary\";s:6:\"method\";s:18:\"entity_attachments\";}', 0, 0, 0, 3),
('bx_glossary_view_entry', 4, 'bx_glossary', '', '_bx_glossary_page_block_title_entry_comments', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:11:\"bx_glossary\";s:6:\"method\";s:15:\"entity_comments\";}', 0, 0, 1, 4);


-- PAGE: view entry comments

INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_glossary_view_entry_comments', '_bx_glossary_page_title_sys_view_entry_comments', '_bx_glossary_page_title_view_entry_comments', 'bx_glossary', 5, 2147483647, 1, 'view-glossary-comments', '', '', '', '', 0, 1, 0, 'BxGlsrPageEntry', 'modules/boonex/glossary/classes/BxGlsrPageEntry.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES 
('bx_glossary_view_entry_comments', 1, 'bx_glossary', '_bx_glossary_page_block_title_entry_comments', '_bx_glossary_page_block_title_entry_comments_link', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:11:\"bx_glossary\";s:6:\"method\";s:15:\"entity_comments\";}', 0, 0, 1);


-- PAGE: popular entries

INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_glossary_popular', '_bx_glossary_page_title_sys_entries_popular', '_bx_glossary_page_title_entries_popular', 'bx_glossary', 5, 2147483647, 1, 'glossary-popular', 'page.php?i=glossary-popular', '', '', '', 0, 1, 0, 'BxGlsrPageBrowse', 'modules/boonex/glossary/classes/BxGlsrPageBrowse.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES 
('bx_glossary_popular', 1, 'bx_glossary', '_bx_glossary_page_block_title_popular_entries', 11, 2147483647, 'service', 'a:3:{s:6:"module";s:11:"bx_glossary";s:6:"method";s:14:"browse_popular";s:6:"params";a:3:{s:9:"unit_view";s:7:"gallery";s:13:"empty_message";b:1;s:13:"ajax_paginate";b:0;}}', 0, 1, 1);


-- PAGE: recently updated entries

INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_glossary_updated', '_bx_glossary_page_title_sys_entries_updated', '_bx_glossary_page_title_entries_updated', 'bx_glossary', 5, 2147483647, 1, 'glossary-updated', 'page.php?i=glossary-updated', '', '', '', 0, 1, 0, 'BxGlsrPageBrowse', 'modules/boonex/glossary/classes/BxGlsrPageBrowse.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES 
('bx_glossary_updated', 1, 'bx_glossary', '_bx_glossary_page_block_title_updated_entries', 11, 2147483647, 'service', 'a:3:{s:6:"module";s:11:"bx_glossary";s:6:"method";s:14:"browse_updated";s:6:"params";a:3:{s:9:"unit_view";s:7:"gallery";s:13:"empty_message";b:1;s:13:"ajax_paginate";b:0;}}', 0, 1, 1);


-- PAGE: entries of author

INSERT INTO `sys_objects_page`(`object`, `uri`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_glossary_author', 'glossary-author', '_bx_glossary_page_title_sys_entries_of_author', '_bx_glossary_page_title_entries_of_author', 'bx_glossary', 5, 2147483647, 1, '', '', '', '', 0, 1, 0, 'BxGlsrPageAuthor', 'modules/boonex/glossary/classes/BxGlsrPageAuthor.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES 
('bx_glossary_author', 1, 'bx_glossary', '', '_bx_glossary_page_block_title_entries_actions', 13, 2147483647, 'service', 'a:2:{s:6:\"module\";s:11:\"bx_glossary\";s:6:\"method\";s:18:\"my_entries_actions\";}', 0, 0, 1, 1),
('bx_glossary_author', 1, 'bx_glossary', '_bx_glossary_page_block_title_sys_favorites_of_author', '_bx_glossary_page_block_title_favorites_of_author', 11, 2147483647, 'service', 'a:3:{s:6:\"module\";s:11:\"bx_glossary\";s:6:\"method\";s:15:\"browse_favorite\";s:6:"params";a:1:{i:0;s:12:"{profile_id}";}}', 0, 1, 1, 2),
('bx_glossary_author', 1, 'bx_glossary', '_bx_glossary_page_block_title_sys_entries_of_author', '_bx_glossary_page_block_title_entries_of_author', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:11:\"bx_glossary\";s:6:\"method\";s:13:\"browse_author\";}', 0, 0, 1, 3);

-- PAGE: entries in context

INSERT INTO `sys_objects_page`(`object`, `uri`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_glossary_context', 'glossary-context', '_bx_glossary_page_title_sys_entries_in_context', '_bx_glossary_page_title_entries_in_context', 'bx_glossary', 5, 2147483647, 1, '', '', '', '', 0, 1, 0, 'BxGlsrPageAuthor', 'modules/boonex/glossary/classes/BxGlsrPageAuthor.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES 
('bx_glossary_context', 1, 'bx_glossary', '_bx_glossary_page_block_title_sys_entries_in_context', '_bx_glossary_page_block_title_entries_in_context', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:11:\"bx_glossary\";s:6:\"method\";s:14:\"browse_context\";}', 0, 0, 1, 1);

-- PAGE: module home

INSERT INTO `sys_objects_page`(`object`, `uri`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_glossary_home', 'glossary-home', '_bx_glossary_page_title_sys_home', '_bx_glossary_page_title_home', 'bx_glossary', 5, 2147483647, 1, 'page.php?i=glossary-home', '', '', '', 0, 1, 0, 'BxGlsrPageBrowse', 'modules/boonex/glossary/classes/BxGlsrPageBrowse.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES 
('bx_glossary_home', 1, 'bx_glossary', '', '_bx_glossary_page_block_title_featured_entries_view_alphabetical', 11, 2147483647, 'service', 'a:3:{s:6:"module";s:11:"bx_glossary";s:6:"method";s:19:"browse_alphabetical";s:6:"params";a:1:{i:0;s:8:"extended";}}', 0, 1, 1, 0),
('bx_glossary_home', 1, 'bx_glossary', '', '_bx_glossary_page_block_title_cats', 11, 2147483647, 'service', 'a:4:{s:6:"module";s:6:"system";s:6:"method";s:15:"categories_list";s:6:"params";a:2:{i:0;s:16:"bx_glossary_cats";i:1;a:1:{s:10:"show_empty";b:1;}}s:5:"class";s:20:"TemplServiceCategory";}', 0, 1, 0, 1);

-- PAGE: search for entries

INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_glossary_search', '_bx_glossary_page_title_sys_entries_search', '_bx_glossary_page_title_entries_search', 'bx_glossary', 5, 2147483647, 1, 'glossary-search', 'page.php?i=glossary-search', '', '', '', 0, 1, 0, 'BxGlsrPageBrowse', 'modules/boonex/glossary/classes/BxGlsrPageBrowse.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES 
('bx_glossary_search', 1, 'bx_glossary', '_bx_glossary_page_block_title_search_form', 11, 2147483647, 'service', 'a:4:{s:6:"module";s:6:"system";s:6:"method";s:8:"get_form";s:6:"params";a:1:{i:0;a:1:{s:6:"object";s:11:"bx_glossary";}}s:5:"class";s:27:"TemplSearchExtendedServices";}', 0, 1, 1, 1),
('bx_glossary_search', 1, 'bx_glossary', '_bx_glossary_page_block_title_search_results', 11, 2147483647, 'service', 'a:4:{s:6:"module";s:6:"system";s:6:"method";s:11:"get_results";s:6:"params";a:1:{i:0;a:2:{s:6:"object";s:11:"bx_glossary";s:10:"show_empty";b:1;}}s:5:"class";s:27:"TemplSearchExtendedServices";}', 0, 1, 1, 2),
('bx_glossary_search', 1, 'bx_glossary', '_bx_glossary_page_block_title_search_form_cmts', 11, 2147483647, 'service', 'a:4:{s:6:"module";s:6:"system";s:6:"method";s:8:"get_form";s:6:"params";a:1:{i:0;a:1:{s:6:"object";s:16:"bx_glossary_cmts";}}s:5:"class";s:27:"TemplSearchExtendedServices";}', 0, 1, 0, 3),
('bx_glossary_search', 1, 'bx_glossary', '_bx_glossary_page_block_title_search_results_cmts', 11, 2147483647, 'service', 'a:4:{s:6:"module";s:6:"system";s:6:"method";s:11:"get_results";s:6:"params";a:1:{i:0;a:2:{s:6:"object";s:16:"bx_glossary_cmts";s:10:"show_empty";b:1;}}s:5:"class";s:27:"TemplSearchExtendedServices";}', 0, 1, 0, 4);


-- PAGE: module manage own
INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_glossary_manage', '_bx_glossary_page_title_sys_manage', '_bx_glossary_page_title_manage', 'bx_glossary', 5, 2147483647, 1, 'glossary-manage', 'page.php?i=glossary-manage', '', '', '', 0, 1, 0, 'BxGlsrPageBrowse', 'modules/boonex/glossary/classes/BxGlsrPageBrowse.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES 
('bx_glossary_manage', 1, 'bx_glossary', '_bx_glossary_page_block_title_system_manage', '_bx_glossary_page_block_title_manage', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:11:\"bx_glossary\";s:6:\"method\";s:12:\"manage_tools\";}}', 0, 1, 0);

-- PAGE: module manage all
INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_glossary_administration', '_bx_glossary_page_title_sys_manage_administration', '_bx_glossary_page_title_manage', 'bx_glossary', 5, @iAdministratorModeratorAccess, 1, 'glossary-administration', 'page.php?i=glossary-administration', '', '', '', 0, 1, 0, 'BxGlsrPageBrowse', 'modules/boonex/glossary/classes/BxGlsrPageBrowse.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES 
('bx_glossary_administration', 1, 'bx_glossary', '_bx_glossary_page_block_title_system_manage_administration', '_bx_glossary_page_block_title_manage', 11, @iAdministratorModeratorAccess, 'service', 'a:3:{s:6:\"module\";s:11:\"bx_glossary\";s:6:\"method\";s:12:\"manage_tools\";s:6:\"params\";a:1:{i:0;s:14:\"administration\";}}', 0, 1, 0);

-- PAGE: add block to homepage

SET @iBlockOrder = (SELECT `order` FROM `sys_pages_blocks` WHERE `object` = 'sys_home' AND `cell_id` = 1 ORDER BY `order` DESC LIMIT 1);
INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES 
('sys_home', 1, 'bx_glossary', '_bx_glossary_page_block_title_recent_entries', 11, 2147483647, 'service', 'a:3:{s:6:"module";s:11:"bx_glossary";s:6:"method";s:13:"browse_public";s:6:"params";a:2:{i:0;b:0;i:1;b:0;}}', 1, 0, 0, IFNULL(@iBlockOrder, 0) + 1);

-- PAGES: add page block to profiles modules (trigger* page objects are processed separately upon modules enable/disable)
SET @iPBCellProfile = 3;
INSERT INTO `sys_pages_blocks` (`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES
('trigger_page_profile_view_entry', @iPBCellProfile, 'bx_glossary', '_bx_glossary_page_block_title_my_entries', 11, 2147483647, 'service', 'a:3:{s:6:"module";s:11:"bx_glossary";s:6:"method";s:13:"browse_author";s:6:"params";a:2:{i:0;s:12:"{profile_id}";i:1;a:2:{s:8:"per_page";s:28:"bx_glossary_per_page_profile";s:13:"empty_message";b:0;}}}', 0, 0, 0);

-- PAGE: service blocks

SET @iBlockOrder = (SELECT `order` FROM `sys_pages_blocks` WHERE `object` = '' AND `cell_id` = 0 ORDER BY `order` DESC LIMIT 1);
INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system` , `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES 
('', 0, 'bx_glossary', '', '_bx_glossary_page_block_title_recent_entries', 11, 2147483647, 'service', 'a:3:{s:6:\"module\";s:11:\"bx_glossary\";s:6:\"method\";s:13:\"browse_public\";s:6:\"params\";a:3:{s:9:\"unit_view\";s:7:\"gallery\";s:13:\"empty_message\";b:1;s:13:\"ajax_paginate\";b:0;}}', 0, 1, IFNULL(@iBlockOrder, 0) + 1),

('', 0, 'bx_glossary', '', '_bx_glossary_page_block_title_recent_entries_view_full', 11, 2147483647, 'service', 'a:3:{s:6:"module";s:11:"bx_glossary";s:6:"method";s:13:"browse_public";s:6:"params";a:1:{i:0;s:4:"full";}}', 0, 1, IFNULL(@iBlockOrder, 0) + 2),
('', 0, 'bx_glossary', '', '_bx_glossary_page_block_title_popular_entries_view_extended', 11, 2147483647, 'service', 'a:3:{s:6:"module";s:11:"bx_glossary";s:6:"method";s:14:"browse_popular";s:6:"params";a:1:{i:0;s:8:"extended";}}', 0, 1, IFNULL(@iBlockOrder, 0) + 3),
('', 0, 'bx_glossary', '', '_bx_glossary_page_block_title_popular_entries_view_full', 11, 2147483647, 'service', 'a:3:{s:6:"module";s:11:"bx_glossary";s:6:"method";s:14:"browse_popular";s:6:"params";a:1:{i:0;s:4:"full";}}', 0, 1, IFNULL(@iBlockOrder, 0) + 4),
('', 0, 'bx_glossary', '_bx_glossary_page_block_title_sys_recent_entries_view_showcase', '_bx_glossary_page_block_title_recent_entries_view_showcase', 11, 2147483647, 'service', 'a:3:{s:6:\"module\";s:11:\"bx_glossary\";s:6:\"method\";s:13:\"browse_public\";s:6:\"params\";a:3:{s:9:\"unit_view\";s:8:\"showcase\";s:13:\"empty_message\";b:0;s:13:\"ajax_paginate\";b:0;}}', 0, 1, IFNULL(@iBlockOrder, 0) + 5),
('', 0, 'bx_glossary', '_bx_glossary_page_block_title_sys_popular_entries_view_showcase', '_bx_glossary_page_block_title_popular_entries_view_showcase', 11, 2147483647, 'service', 'a:3:{s:6:\"module\";s:11:\"bx_glossary\";s:6:\"method\";s:14:\"browse_popular\";s:6:\"params\";a:3:{s:9:\"unit_view\";s:8:\"showcase\";s:13:\"empty_message\";b:0;s:13:\"ajax_paginate\";b:0;}}', 0, 1, IFNULL(@iBlockOrder, 0) + 6),
('', 0, 'bx_glossary', '_bx_glossary_page_block_title_sys_featured_entries_view_showcase', '_bx_glossary_page_block_title_featured_entries_view_showcase', 11, 2147483647, 'service', 'a:3:{s:6:\"module\";s:11:\"bx_glossary\";s:6:\"method\";s:15:\"browse_featured\";s:6:\"params\";a:3:{s:9:\"unit_view\";s:8:\"showcase\";s:13:\"empty_message\";b:0;s:13:\"ajax_paginate\";b:0;}}', 0, 1, IFNULL(@iBlockOrder, 0) + 7),
('', 0, 'bx_glossary', '', '_bx_glossary_page_block_title_featured_entries_view_extended', 11, 2147483647, 'service', 'a:3:{s:6:"module";s:11:"bx_glossary";s:6:"method";s:15:"browse_featured";s:6:"params";a:1:{i:0;s:8:"extended";}}', 0, 1, IFNULL(@iBlockOrder, 0) + 8),
('', 0, 'bx_glossary', '', '_bx_glossary_page_block_title_recent_entries_view_extended', 11, 2147483647, 'service', 'a:3:{s:6:"module";s:11:"bx_glossary";s:6:"method";s:13:"browse_public";s:6:"params";a:1:{i:0;s:8:"extended";}}', 0, 1, IFNULL(@iBlockOrder, 0) + 9);


-- MENU: add to site menu

SET @iSiteMenuOrder = (SELECT `order` FROM `sys_menu_items` WHERE `set_name` = 'sys_site' AND `active` = 1 ORDER BY `order` DESC LIMIT 1);
INSERT INTO `sys_menu_items` (`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('sys_site', 'bx_glossary', 'glossary-home', '_bx_glossary_menu_item_title_system_entries_home', '_bx_glossary_menu_item_title_entries_home', 'page.php?i=glossary-home', '', '', 'fa-book col-red3', 'bx_glossary_submenu', 2147483647, 1, 1, IFNULL(@iSiteMenuOrder, 0) + 1);

-- MENU: add to homepage menu

SET @iHomepageMenuOrder = (SELECT `order` FROM `sys_menu_items` WHERE `set_name` = 'sys_homepage' AND `active` = 1 ORDER BY `order` DESC LIMIT 1);
INSERT INTO `sys_menu_items` (`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('sys_homepage', 'bx_glossary', 'glossary-home', '_bx_glossary_menu_item_title_system_entries_home', '_bx_glossary_menu_item_title_entries_home', 'page.php?i=glossary-home', '', '', 'fa-book col-red3', 'bx_glossary_submenu', 2147483647, 1, 1, IFNULL(@iHomepageMenuOrder, 0) + 1);

-- MENU: add to "add content" menu

SET @iAddMenuOrder = (SELECT `order` FROM `sys_menu_items` WHERE `set_name` = 'sys_add_content_links' AND `active` = 1 ORDER BY `order` DESC LIMIT 1);
INSERT INTO `sys_menu_items` (`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('sys_add_content_links', 'bx_glossary', 'create-glossary', '_bx_glossary_menu_item_title_system_create_entry', '_bx_glossary_menu_item_title_create_entry', 'page.php?i=create-glossary', '', '', 'fa-book col-red3', '', 2147483647, 1, 1, IFNULL(@iAddMenuOrder, 0) + 1);


-- MENU: actions menu for view entry 

INSERT INTO `sys_objects_menu`(`object`, `title`, `set_name`, `module`, `template_id`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_glossary_view', '_bx_glossary_menu_title_view_entry', 'bx_glossary_view', 'bx_glossary', 9, 0, 1, 'BxGlsrMenuView', 'modules/boonex/glossary/classes/BxGlsrMenuView.php');

INSERT INTO `sys_menu_sets`(`set_name`, `module`, `title`, `deletable`) VALUES 
('bx_glossary_view', 'bx_glossary', '_bx_glossary_menu_set_title_view_entry', 0);

INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('bx_glossary_view', 'bx_glossary', 'edit-glossary', '_bx_glossary_menu_item_title_system_edit_entry', '_bx_glossary_menu_item_title_edit_entry', 'page.php?i=edit-glossary&id={content_id}', '', '', 'pencil-alt', '', 2147483647, 1, 0, 1),
('bx_glossary_view', 'bx_glossary', 'delete-glossary', '_bx_glossary_menu_item_title_system_delete_entry', '_bx_glossary_menu_item_title_delete_entry', 'page.php?i=delete-glossary&id={content_id}', '', '', 'remove', '', 2147483647, 1, 0, 2);


-- MENU: all actions menu for view entry 

INSERT INTO `sys_objects_menu`(`object`, `title`, `set_name`, `module`, `template_id`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_glossary_view_actions', '_sys_menu_title_view_actions', 'bx_glossary_view_actions', 'bx_glossary', 15, 0, 1, 'BxGlsrMenuViewActions', 'modules/boonex/glossary/classes/BxGlsrMenuViewActions.php');

INSERT INTO `sys_menu_sets`(`set_name`, `module`, `title`, `deletable`) VALUES 
('bx_glossary_view_actions', 'bx_glossary', '_sys_menu_set_title_view_actions', 0);

INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `addon`, `submenu_object`, `submenu_popup`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('bx_glossary_view_actions', 'bx_glossary', 'edit-glossary', '_bx_glossary_menu_item_title_system_edit_entry', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 10),
('bx_glossary_view_actions', 'bx_glossary', 'delete-glossary', '_bx_glossary_menu_item_title_system_delete_entry', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 20),
('bx_glossary_view_actions', 'bx_glossary', 'comment', '_sys_menu_item_title_system_va_comment', '', '', '', '', '', '', '', 0, 2147483647, 0, 0, 200),
('bx_glossary_view_actions', 'bx_glossary', 'view', '_sys_menu_item_title_system_va_view', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 210),
('bx_glossary_view_actions', 'bx_glossary', 'vote', '_sys_menu_item_title_system_va_vote', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 220),
('bx_glossary_view_actions', 'bx_glossary', 'score', '_sys_menu_item_title_system_va_score', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 230),
('bx_glossary_view_actions', 'bx_glossary', 'favorite', '_sys_menu_item_title_system_va_favorite', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 240),
('bx_glossary_view_actions', 'bx_glossary', 'feature', '_sys_menu_item_title_system_va_feature', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 250),
('bx_glossary_view_actions', 'bx_glossary', 'repost', '_sys_menu_item_title_system_va_repost', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 260),
('bx_glossary_view_actions', 'bx_glossary', 'report', '_sys_menu_item_title_system_va_report', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 270),
('bx_glossary_view_actions', 'bx_glossary', 'social-sharing-facebook', '_sys_menu_item_title_system_social_sharing_facebook', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 300),
('bx_glossary_view_actions', 'bx_glossary', 'social-sharing-googleplus', '_sys_menu_item_title_system_social_sharing_googleplus', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 310),
('bx_glossary_view_actions', 'bx_glossary', 'social-sharing-twitter', '_sys_menu_item_title_system_social_sharing_twitter', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 320),
('bx_glossary_view_actions', 'bx_glossary', 'social-sharing-pinterest', '_sys_menu_item_title_system_social_sharing_pinterest', '', '', '', '', '', '', '', 0, 2147483647, 1, 0, 330),
('bx_glossary_view_actions', 'bx_glossary', 'more-auto', '_sys_menu_item_title_system_va_more_auto', '_sys_menu_item_title_va_more_auto', 'javascript:void(0)', '', '', 'ellipsis-v', '', '', 0, 2147483647, 1, 0, 9999);


-- MENU: actions menu for my entries

INSERT INTO `sys_objects_menu`(`object`, `title`, `set_name`, `module`, `template_id`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_glossary_my', '_bx_glossary_menu_title_entries_my', 'bx_glossary_my', 'bx_glossary', 9, 0, 1, 'BxGlsrMenu', 'modules/boonex/glossary/classes/BxGlsrMenu.php');

INSERT INTO `sys_menu_sets`(`set_name`, `module`, `title`, `deletable`) VALUES 
('bx_glossary_my', 'bx_glossary', '_bx_glossary_menu_set_title_entries_my', 0);

INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('bx_glossary_my', 'bx_glossary', 'create-glossary', '_bx_glossary_menu_item_title_system_create_entry', '_bx_glossary_menu_item_title_create_entry', 'page.php?i=create-glossary', '', '', 'plus', '', 2147483647, 1, 0, 0);


-- MENU: module sub-menu

INSERT INTO `sys_objects_menu`(`object`, `title`, `set_name`, `module`, `template_id`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_glossary_submenu', '_bx_glossary_menu_title_submenu', 'bx_glossary_submenu', 'bx_glossary', 8, 0, 1, '', '');

INSERT INTO `sys_menu_sets`(`set_name`, `module`, `title`, `deletable`) VALUES 
('bx_glossary_submenu', 'bx_glossary', '_bx_glossary_menu_set_title_submenu', 0);

INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('bx_glossary_submenu', 'bx_glossary', 'glossary-home', '_bx_glossary_menu_item_title_system_entries_public', '_bx_glossary_menu_item_title_entries_public', 'page.php?i=glossary-home', '', '', '', '', 2147483647, 1, 1, 1),
('bx_glossary_submenu', 'bx_glossary', 'glossary-popular', '_bx_glossary_menu_item_title_system_entries_popular', '_bx_glossary_menu_item_title_entries_popular', 'page.php?i=glossary-popular', '', '', '', '', 2147483647, 1, 1, 2),
('bx_glossary_submenu', 'bx_glossary', 'glossary-search', '_bx_glossary_menu_item_title_system_entries_search', '_bx_glossary_menu_item_title_entries_search', 'page.php?i=glossary-search', '', '', '', '', 2147483647, 1, 1, 3),
('bx_glossary_submenu', 'bx_glossary', 'glossary-manage', '_bx_glossary_menu_item_title_system_entries_manage', '_bx_glossary_menu_item_title_entries_manage', 'page.php?i=glossary-manage', '', '', '', '', 2147483646, 1, 1, 4);

-- MENU: sub-menu for view entry
INSERT INTO `sys_objects_menu`(`object`, `title`, `set_name`, `module`, `template_id`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_glossary_view_submenu', '_bx_glossary_menu_title_view_entry_submenu', 'bx_glossary_view_submenu', 'bx_glossary', 8, 0, 1, 'BxGlsrMenuView', 'modules/boonex/glossary/classes/BxGlsrMenuView.php');

INSERT INTO `sys_menu_sets`(`set_name`, `module`, `title`, `deletable`) VALUES 
('bx_glossary_view_submenu', 'bx_glossary', '_bx_glossary_menu_set_title_view_entry_submenu', 0);

INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('bx_glossary_view_submenu', 'bx_glossary', 'view-glossary', '_bx_glossary_menu_item_title_system_view_entry', '_bx_glossary_menu_item_title_view_entry_submenu_entry', 'page.php?i=view-term&id={content_id}', '', '', '', '', 2147483647, 0, 0, 1),
('bx_glossary_view_submenu', 'bx_glossary', 'view-glossary-comments', '_bx_glossary_menu_item_title_system_view_entry_comments', '_bx_glossary_menu_item_title_view_entry_submenu_comments', 'page.php?i=view-term-comments&id={content_id}', '', '', '', '', 2147483647, 0, 0, 2);

-- MENU: custom menu for snippet meta info
INSERT INTO `sys_objects_menu`(`object`, `title`, `set_name`, `module`, `template_id`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_glossary_snippet_meta', '_sys_menu_title_snippet_meta', 'bx_glossary_snippet_meta', 'bx_glossary', 15, 0, 1, 'BxGlsrMenuSnippetMeta', 'modules/boonex/glossary/classes/BxGlsrMenuSnippetMeta.php');

INSERT INTO `sys_menu_sets`(`set_name`, `module`, `title`, `deletable`) VALUES 
('bx_glossary_snippet_meta', 'bx_glossary', '_sys_menu_set_title_snippet_meta', 0);

INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `editable`, `order`) VALUES 
('bx_glossary_snippet_meta', 'bx_glossary', 'date', '_sys_menu_item_title_system_sm_date', '_sys_menu_item_title_sm_date', '', '', '', '', '', 2147483647, 1, 0, 1, 1),
('bx_glossary_snippet_meta', 'bx_glossary', 'author', '_sys_menu_item_title_system_sm_author', '_sys_menu_item_title_sm_author', '', '', '', '', '', 2147483647, 1, 0, 1, 2),
('bx_glossary_snippet_meta', 'bx_glossary', 'category', '_sys_menu_item_title_system_sm_category', '_sys_menu_item_title_sm_category', '', '', '', '', '', 2147483647, 0, 0, 1, 3),
('bx_glossary_snippet_meta', 'bx_glossary', 'tags', '_sys_menu_item_title_system_sm_tags', '_sys_menu_item_title_sm_tags', '', '', '', '', '', 2147483647, 0, 0, 1, 4),
('bx_glossary_snippet_meta', 'bx_glossary', 'views', '_sys_menu_item_title_system_sm_views', '_sys_menu_item_title_sm_views', '', '', '', '', '', 2147483647, 0, 0, 1, 5),
('bx_glossary_snippet_meta', 'bx_glossary', 'comments', '_sys_menu_item_title_system_sm_comments', '_sys_menu_item_title_sm_comments', '', '', '', '', '', 2147483647, 0, 0, 1, 6);

-- MENU: profile stats
SET @iNotifMenuOrder = (SELECT IFNULL(MAX(`order`), 0) FROM `sys_menu_items` WHERE `set_name` = 'sys_profile_stats' AND `active` = 1 LIMIT 1);
INSERT INTO `sys_menu_items` (`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `addon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES
('sys_profile_stats', 'bx_glossary', 'profile-stats-manage-glossary', '_bx_glossary_menu_item_title_system_manage_my_glossary', '_bx_glossary_menu_item_title_manage_my_glossary', 'page.php?i=glossary-manage', '', '_self', 'fa-book col-red3', 'a:2:{s:6:"module";s:11:"bx_glossary";s:6:"method";s:41:"get_menu_addon_manage_tools_profile_stats";}', '', @iAdministratorModeratorAccess, 1, 0, @iNotifMenuOrder + 1);

-- MENU: manage tools submenu
INSERT INTO `sys_objects_menu`(`object`, `title`, `set_name`, `module`, `template_id`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_glossary_menu_manage_tools', '_bx_glossary_menu_title_manage_tools', 'bx_glossary_menu_manage_tools', 'bx_glossary', 6, 0, 1, 'BxGlsrMenuManageTools', 'modules/boonex/glossary/classes/BxGlsrMenuManageTools.php');

INSERT INTO `sys_menu_sets`(`set_name`, `module`, `title`, `deletable`) VALUES 
('bx_glossary_menu_manage_tools', 'bx_glossary', '_bx_glossary_menu_set_title_manage_tools', 0);

--INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
--('bx_glossary_menu_manage_tools', 'bx_glossary', 'delete-with-content', '_bx_glossary_menu_item_title_system_delete_with_content', '_bx_glossary_menu_item_title_delete_with_content', 'javascript:void(0)', 'javascript:{js_object}.onClickDeleteWithContent({content_id});', '_self', 'far trash-alt', '', 128, 1, 0, 0);

-- MENU: dashboard manage tools
SET @iManageMenuOrder = (SELECT IFNULL(MAX(`order`), 0) FROM `sys_menu_items` WHERE `set_name`='sys_account_dashboard_manage_tools' LIMIT 1);
INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `addon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('sys_account_dashboard_manage_tools', 'bx_glossary', 'glossary-administration', '_bx_glossary_menu_item_title_system_admt_glossary', '_bx_glossary_menu_item_title_admt_glossary', 'page.php?i=glossary-administration', '', '_self', '', 'a:2:{s:6:"module";s:11:"bx_glossary";s:6:"method";s:27:"get_menu_addon_manage_tools";}', '', @iAdministratorModeratorAccess, 1, 0, @iManageMenuOrder + 1);

-- MENU: add menu item to profiles modules (trigger* menu sets are processed separately upon modules enable/disable)

INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('trigger_profile_view_submenu', 'bx_glossary', 'glossary-author', '_bx_glossary_menu_item_title_system_view_entries_author', '_bx_glossary_menu_item_title_view_entries_author', 'page.php?i=glossary-author&profile_id={profile_id}', '', '', 'fa-book col-red3', '', 2147483647, 1, 0, 0),
('trigger_group_view_submenu', 'bx_glossary', 'glossary-context', '_bx_glossary_menu_item_title_system_view_entries_in_context', '_bx_glossary_menu_item_title_view_entries_in_context', 'page.php?i=glossary-context&profile_id={profile_id}', '', '', 'fa-book col-red3', '', 2147483647, 1, 0, 0);

-- PRIVACY 

INSERT INTO `sys_objects_privacy` (`object`, `module`, `action`, `title`, `default_group`, `table`, `table_field_id`, `table_field_author`, `override_class_name`, `override_class_file`) VALUES
('bx_glossary_allow_view_to', 'bx_glossary', 'view', '_bx_glossary_form_entry_input_allow_view_to', '3', 'bx_glossary_terms', 'id', 'author', '', '');


-- ACL

INSERT INTO `sys_acl_actions` (`Module`, `Name`, `AdditionalParamName`, `Title`, `Desc`, `Countable`, `DisabledForLevels`) VALUES
('bx_glossary', 'create entry', NULL, '_bx_glossary_acl_action_create_entry', '', 1, 3);
SET @iIdActionEntryCreate = LAST_INSERT_ID();

INSERT INTO `sys_acl_actions` (`Module`, `Name`, `AdditionalParamName`, `Title`, `Desc`, `Countable`, `DisabledForLevels`) VALUES
('bx_glossary', 'delete entry', NULL, '_bx_glossary_acl_action_delete_entry', '', 1, 3);
SET @iIdActionEntryDelete = LAST_INSERT_ID();

INSERT INTO `sys_acl_actions` (`Module`, `Name`, `AdditionalParamName`, `Title`, `Desc`, `Countable`, `DisabledForLevels`) VALUES
('bx_glossary', 'view entry', NULL, '_bx_glossary_acl_action_view_entry', '', 1, 0);
SET @iIdActionEntryView = LAST_INSERT_ID();

INSERT INTO `sys_acl_actions` (`Module`, `Name`, `AdditionalParamName`, `Title`, `Desc`, `Countable`, `DisabledForLevels`) VALUES
('bx_glossary', 'set thumb', NULL, '_bx_glossary_acl_action_set_thumb', '', 1, 3);
SET @iIdActionSetThumb = LAST_INSERT_ID();

INSERT INTO `sys_acl_actions` (`Module`, `Name`, `AdditionalParamName`, `Title`, `Desc`, `Countable`, `DisabledForLevels`) VALUES
('bx_glossary', 'edit any entry', NULL, '_bx_glossary_acl_action_edit_any_entry', '', 1, 3);
SET @iIdActionEntryEditAny = LAST_INSERT_ID();

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

-- entry create
(@iStandard, @iIdActionEntryCreate),
(@iModerator, @iIdActionEntryCreate),
(@iAdministrator, @iIdActionEntryCreate),


-- entry delete
(@iModerator, @iIdActionEntryDelete),
(@iAdministrator, @iIdActionEntryDelete),

-- entry view
(@iUnauthenticated, @iIdActionEntryView),
(@iAccount, @iIdActionEntryView),
(@iStandard, @iIdActionEntryView),
(@iUnconfirmed, @iIdActionEntryView),
(@iPending, @iIdActionEntryView),
(@iModerator, @iIdActionEntryView),
(@iAdministrator, @iIdActionEntryView),
(@iPremium, @iIdActionEntryView),

-- set entry thumb
(@iModerator, @iIdActionSetThumb),
(@iAdministrator, @iIdActionSetThumb),

-- edit any entry
(@iModerator, @iIdActionEntryEditAny),
(@iAdministrator, @iIdActionEntryEditAny);


-- SEARCH
SET @iSearchOrder = (SELECT IFNULL(MAX(`Order`), 0) FROM `sys_objects_search`);
INSERT INTO `sys_objects_search` (`ObjectName`, `Title`, `Order`, `ClassName`, `ClassPath`) VALUES
('bx_glossary', '_bx_glossary', @iSearchOrder + 1, 'BxGlsrSearchResult', 'modules/boonex/glossary/classes/BxGlsrSearchResult.php'),
('bx_glossary_cmts', '_bx_glossary_cmts', @iSearchOrder + 2, 'BxGlsrCmtsSearchResult', 'modules/boonex/glossary/classes/BxGlsrCmtsSearchResult.php');

-- METATAGS
INSERT INTO `sys_objects_metatags` (`object`, `table_keywords`, `table_locations`, `table_mentions`, `override_class_name`, `override_class_file`) VALUES
('bx_glossary', 'bx_glossary_meta_keywords', '', 'bx_glossary_meta_mentions', '', '');

-- CATEGORY
INSERT INTO `sys_objects_category` (`object`, `search_object`, `form_object`, `list_name`, `table`, `field`, `join`, `where`, `override_class_name`, `override_class_file`) VALUES
('bx_glossary_cats', 'bx_glossary', 'bx_glossary', 'bx_glossary_cats', 'bx_glossary_terms', 'cat', 'INNER JOIN `sys_profiles` ON (`sys_profiles`.`id` = `bx_glossary_terms`.`author`)', 'AND `sys_profiles`.`status` = ''active''', '', '');

-- STATS
SET @iMaxOrderStats = (SELECT IFNULL(MAX(`order`), 0) FROM `sys_statistics`);
INSERT INTO `sys_statistics` (`module`, `name`, `title`, `link`, `icon`, `query`, `order`) VALUES 
('bx_glossary', 'bx_glossary', '_bx_glossary', 'page.php?i=glossary-home', 'fa-book col-red3', 'SELECT COUNT(*) FROM `bx_glossary_terms` WHERE 1 AND `status` = ''active'' AND `status_admin` = ''active''', @iMaxOrderStats + 1);

-- CHARTS
SET @iMaxOrderCharts = (SELECT IFNULL(MAX(`order`), 0) FROM `sys_objects_chart`);
INSERT INTO `sys_objects_chart` (`object`, `title`, `table`, `field_date_ts`, `field_date_dt`, `field_status`, `query`, `active`, `order`, `class_name`, `class_file`) VALUES
('bx_glossary_growth', '_bx_glossary_chart_growth', 'bx_glossary_terms', 'added', '', 'status,status_admin', '', 1, @iMaxOrderCharts + 1, 'BxDolChartGrowth', ''),
('bx_glossary_growth_speed', '_bx_glossary_chart_growth_speed', 'bx_glossary_terms', 'added', '', 'status,status_admin', '', 1, @iMaxOrderCharts + 2, 'BxDolChartGrowthSpeed', '');

-- GRIDS: moderation tools
INSERT INTO `sys_objects_grid` (`object`, `source_type`, `source`, `table`, `field_id`, `field_order`, `field_active`, `paginate_url`, `paginate_per_page`, `paginate_simple`, `paginate_get_start`, `paginate_get_per_page`, `filter_fields`, `filter_fields_translatable`, `filter_mode`, `sorting_fields`, `sorting_fields_translatable`, `visible_for_levels`, `override_class_name`, `override_class_file`) VALUES
('bx_glossary_administration', 'Sql', 'SELECT * FROM `bx_glossary_terms` WHERE 1 ', 'bx_glossary_terms', 'id', 'added', 'status_admin', '', 20, NULL, 'start', '', 'title,text', '', 'like', 'reports', '', @iAdministratorModeratorAccess, 'BxGlsrGridAdministration', 'modules/boonex/glossary/classes/BxGlsrGridAdministration.php'),
('bx_glossary_common', 'Sql', 'SELECT * FROM `bx_glossary_terms` WHERE 1 ', 'bx_glossary_terms', 'id', 'added', 'status', '', 20, NULL, 'start', '', 'title,text', '', 'like', '', '', 2147483647, 'BxGlsrGridCommon', 'modules/boonex/glossary/classes/BxGlsrGridCommon.php');


INSERT INTO `sys_grid_fields` (`object`, `name`, `title`, `width`, `translatable`, `chars_limit`, `params`, `order`) VALUES
('bx_glossary_administration', 'checkbox', '_sys_select', '2%', 0, '', '', 1),
('bx_glossary_administration', 'switcher', '_bx_glossary_grid_column_title_adm_active', '8%', 0, '', '', 2),
('bx_glossary_administration', 'reports', '_sys_txt_reports_title', '5%', 0, '', '', 3),
('bx_glossary_administration', 'title', '_bx_glossary_grid_column_title_adm_title', '25%', 0, '25', '', 4),
('bx_glossary_administration', 'added', '_bx_glossary_grid_column_title_adm_added', '20%', 1, '25', '', 5),
('bx_glossary_administration', 'author', '_bx_glossary_grid_column_title_adm_author', '20%', 0, '25', '', 6),
('bx_glossary_administration', 'actions', '', '20%', 0, '', '', 7),
('bx_glossary_common', 'checkbox', '_sys_select', '2%', 0, '', '', 1),
('bx_glossary_common', 'switcher', '_bx_glossary_grid_column_title_adm_active', '8%', 0, '', '', 2),
('bx_glossary_common', 'title', '_bx_glossary_grid_column_title_adm_title', '40%', 0, '35', '', 3),
('bx_glossary_common', 'added', '_bx_glossary_grid_column_title_adm_added', '30%', 1, '25', '', 4),
('bx_glossary_common', 'actions', '', '20%', 0, '', '', 5);

INSERT INTO `sys_grid_actions` (`object`, `type`, `name`, `title`, `icon`, `icon_only`, `confirm`, `order`) VALUES
('bx_glossary_administration', 'bulk', 'delete', '_bx_glossary_grid_action_title_adm_delete', '', 0, 1, 1),
('bx_glossary_administration', 'single', 'edit', '_bx_glossary_grid_action_title_adm_edit', 'pencil-alt', 1, 0, 1),
('bx_glossary_administration', 'single', 'delete', '_bx_glossary_grid_action_title_adm_delete', 'remove', 1, 1, 2),
('bx_glossary_administration', 'single', 'settings', '_bx_glossary_grid_action_title_adm_more_actions', 'cog', 1, 0, 3),
('bx_glossary_common', 'bulk', 'delete', '_bx_glossary_grid_action_title_adm_delete', '', 0, 1, 1),
('bx_glossary_common', 'single', 'edit', '_bx_glossary_grid_action_title_adm_edit', 'pencil-alt', 1, 0, 1),
('bx_glossary_common', 'single', 'delete', '_bx_glossary_grid_action_title_adm_delete', 'remove', 1, 1, 2),
('bx_glossary_common', 'single', 'settings', '_bx_glossary_grid_action_title_adm_more_actions', 'cog', 1, 0, 3);

-- UPLOADERS

INSERT INTO `sys_objects_uploader` (`object`, `active`, `override_class_name`, `override_class_file`) VALUES
('bx_glossary_simple', 1, 'BxGlsrUploaderSimple', 'modules/boonex/glossary/classes/BxGlsrUploaderSimple.php'),
('bx_glossary_html5', 1, 'BxGlsrUploaderHTML5', 'modules/boonex/glossary/classes/BxGlsrUploaderHTML5.php');

-- ALERTS

INSERT INTO `sys_alerts_handlers` (`name`, `class`, `file`, `service_call`) VALUES 
('bx_glossary', 'BxGlsrAlertsResponse', 'modules/boonex/glossary/classes/BxGlsrAlertsResponse.php', '');
SET @iHandler := LAST_INSERT_ID();

INSERT INTO `sys_alerts` (`unit`, `action`, `handler_id`) VALUES
('system', 'save_setting', @iHandler),
('profile', 'delete', @iHandler);

