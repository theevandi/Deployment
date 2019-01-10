
-- SETTINGS
SET @iTypeOrder = (SELECT MAX(`order`) FROM `sys_options_types` WHERE `group` = 'modules');
INSERT INTO `sys_options_types`(`group`, `name`, `caption`, `icon`, `order`) VALUES 
('modules', 'bx_snipcart', '_bx_snipcart', 'bx_snipcart@modules/boonex/snipcart/|std-icon.svg', IF(ISNULL(@iTypeOrder), 1, @iTypeOrder + 1));
SET @iTypeId = LAST_INSERT_ID();

INSERT INTO `sys_options_categories` (`type_id`, `name`, `caption`, `order`)
VALUES (@iTypeId, 'bx_snipcart', '_bx_snipcart', 1);
SET @iCategId = LAST_INSERT_ID();

INSERT INTO `sys_options` (`name`, `value`, `category_id`, `caption`, `type`, `check`, `check_error`, `extra`, `order`) VALUES
('bx_snipcart_summary_chars', '700', @iCategId, '_bx_snipcart_option_summary_chars', 'digit', '', '', '', 1),
('bx_snipcart_plain_summary_chars', '240', @iCategId, '_bx_snipcart_option_plain_summary_chars', 'digit', '', '', '', 2),
('bx_snipcart_per_page_browse', '12', @iCategId, '_bx_snipcart_option_per_page_browse', 'digit', '', '', '', 10),
('bx_snipcart_per_page_profile', '6', @iCategId, '_bx_snipcart_option_per_page_profile', 'digit', '', '', '', 12),
('bx_snipcart_per_page_browse_showcase', '32', @iCategId, '_sys_option_per_page_browse_showcase', 'digit', '', '', '', 15),
('bx_snipcart_rss_num', '10', @iCategId, '_bx_snipcart_option_rss_num', 'digit', '', '', '', 20),
('bx_snipcart_searchable_fields', 'title,text', @iCategId, '_bx_snipcart_option_searchable_fields', 'list', '', '', 'a:2:{s:6:"module";s:11:"bx_snipcart";s:6:"method";s:21:"get_searchable_fields";}', 30);

-- PAGE: create entry
INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_snipcart_create_entry', '_bx_snipcart_page_title_sys_create_entry', '_bx_snipcart_page_title_create_entry', 'bx_snipcart', 5, 2147483647, 1, 'create-snipcart-entry', 'page.php?i=create-snipcart-entry', '', '', '', 0, 1, 0, 'BxSnipcartPageBrowse', 'modules/boonex/snipcart/classes/BxSnipcartPageBrowse.php');

INSERT INTO `sys_pages_blocks` (`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES
('bx_snipcart_create_entry', 1, 'bx_snipcart', '_bx_snipcart_page_block_title_create_entry', 11, 2147483647, 'service', 'a:2:{s:6:"module";s:11:"bx_snipcart";s:6:"method";s:13:"entity_create";}', 0, 1, 1);

-- PAGE: edit entry
INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_snipcart_edit_entry', '_bx_snipcart_page_title_sys_edit_entry', '_bx_snipcart_page_title_edit_entry', 'bx_snipcart', 5, 2147483647, 1, 'edit-snipcart-entry', '', '', '', '', 0, 1, 0, 'BxSnipcartPageEntry', 'modules/boonex/snipcart/classes/BxSnipcartPageEntry.php');

INSERT INTO `sys_pages_blocks` (`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES
('bx_snipcart_edit_entry', 1, 'bx_snipcart', '_bx_snipcart_page_block_title_edit_entry', 11, 2147483647, 'service', 'a:2:{s:6:"module";s:11:"bx_snipcart";s:6:"method";s:11:"entity_edit";}', 0, 0, 0);

-- PAGE: delete entry
INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_snipcart_delete_entry', '_bx_snipcart_page_title_sys_delete_entry', '_bx_snipcart_page_title_delete_entry', 'bx_snipcart', 5, 2147483647, 1, 'delete-snipcart-entry', '', '', '', '', 0, 1, 0, 'BxSnipcartPageEntry', 'modules/boonex/snipcart/classes/BxSnipcartPageEntry.php');

INSERT INTO `sys_pages_blocks` (`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES
('bx_snipcart_delete_entry', 1, 'bx_snipcart', '_bx_snipcart_page_block_title_delete_entry', 11, 2147483647, 'service', 'a:2:{s:6:"module";s:11:"bx_snipcart";s:6:"method";s:13:"entity_delete";}', 0, 0, 0);

-- PAGE: view entry
INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_snipcart_view_entry', '_bx_snipcart_page_title_sys_view_entry', '_bx_snipcart_page_title_view_entry', 'bx_snipcart', 10, 2147483647, 1, 'view-snipcart-entry', '', '', '', '', 0, 1, 0, 'BxSnipcartPageEntry', 'modules/boonex/snipcart/classes/BxSnipcartPageEntry.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES 
('bx_snipcart_view_entry', 1, 'bx_snipcart', '', '_bx_snipcart_page_block_title_entry_text', 13, 2147483647, 'service', 'a:2:{s:6:"module";s:11:"bx_snipcart";s:6:"method";s:17:"entity_text_block";}', 0, 0, 1, 0),
('bx_snipcart_view_entry', 1, 'bx_snipcart', '', '_bx_snipcart_page_block_title_entry_attachments', 11, 2147483647, 'service', 'a:2:{s:6:"module";s:11:"bx_snipcart";s:6:"method";s:18:"entity_attachments";}', 0, 0, 1, 1),
('bx_snipcart_view_entry', 2, 'bx_snipcart', '', '_bx_snipcart_page_block_title_entry_author', 13, 2147483647, 'service', 'a:2:{s:6:"module";s:11:"bx_snipcart";s:6:"method";s:13:"entity_author";}', 0, 0, 1, 0),
('bx_snipcart_view_entry', 2, 'bx_snipcart', '_bx_snipcart_page_block_title_sys_entry_context', '_bx_snipcart_page_block_title_entry_context', 13, 2147483647, 'service', 'a:2:{s:6:\"module\";s:11:\"bx_snipcart\";s:6:\"method\";s:14:\"entity_context\";}', 0, 0, 1, 1),
('bx_snipcart_view_entry', 3, 'bx_snipcart', '', '_bx_snipcart_page_block_title_entry_info', 11, 2147483647, 'service', 'a:2:{s:6:"module";s:11:"bx_snipcart";s:6:"method";s:11:"entity_info";}', 0, 0, 1, 1),
('bx_snipcart_view_entry', 3, 'bx_snipcart', '', '_bx_snipcart_page_block_title_entry_location', 13, 2147483647, 'service', 'a:2:{s:6:"module";s:11:"bx_snipcart";s:6:"method";s:15:"entity_location";}', 0, 0, 0, 2),
('bx_snipcart_view_entry', 4, 'bx_snipcart', '', '_bx_snipcart_page_block_title_entry_all_actions', 13, 2147483647, 'service', 'a:2:{s:6:"module";s:11:"bx_snipcart";s:6:"method";s:18:"entity_all_actions";}', 0, 0, 1, 0),
('bx_snipcart_view_entry', 4, 'bx_snipcart', '', '_bx_snipcart_page_block_title_entry_actions', 13, 2147483647, 'service', 'a:2:{s:6:"module";s:11:"bx_snipcart";s:6:"method";s:14:"entity_actions";}', 0, 0, 0, 1),
('bx_snipcart_view_entry', 4, 'bx_snipcart', '', '_bx_snipcart_page_block_title_entry_social_sharing', 11, 2147483647, 'service', 'a:2:{s:6:"module";s:11:"bx_snipcart";s:6:"method";s:21:"entity_social_sharing";}', 0, 0, 0, 2),
('bx_snipcart_view_entry', 4, 'bx_snipcart', '', '_bx_snipcart_page_block_title_entry_comments', 11, 2147483647, 'service', 'a:2:{s:6:"module";s:11:"bx_snipcart";s:6:"method";s:15:"entity_comments";}', 0, 0, 1, 3),
('bx_snipcart_view_entry', 4, 'bx_snipcart', '', '_bx_snipcart_page_block_title_entry_location', 3, 2147483647, 'service', 'a:4:{s:6:"module";s:6:"system";s:6:"method";s:13:"locations_map";s:6:"params";a:2:{i:0;s:11:"bx_snipcart";i:1;s:4:"{id}";}s:5:"class";s:20:"TemplServiceMetatags";}', 0, 0, 1, 4);

-- PAGE: view entry comments
INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_snipcart_view_entry_comments', '_bx_snipcart_page_title_sys_view_entry_comments', '_bx_snipcart_page_title_view_entry_comments', 'bx_snipcart', 5, 2147483647, 1, 'view-snipcart-entry-comments', '', '', '', '', 0, 1, 0, 'BxSnipcartPageEntry', 'modules/boonex/snipcart/classes/BxSnipcartPageEntry.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES 
('bx_snipcart_view_entry_comments', 1, 'bx_snipcart', '_bx_snipcart_page_block_title_entry_comments', '_bx_snipcart_page_block_title_entry_comments_link', 11, 2147483647, 'service', 'a:2:{s:6:"module";s:11:"bx_snipcart";s:6:"method";s:15:"entity_comments";}', 0, 0, 1);

-- PAGE: popular entries
INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_snipcart_popular', '_bx_snipcart_page_title_sys_entries_popular', '_bx_snipcart_page_title_entries_popular', 'bx_snipcart', 5, 2147483647, 1, 'snipcart-popular', 'page.php?i=snipcart-popular', '', '', '', 0, 1, 0, 'BxSnipcartPageBrowse', 'modules/boonex/snipcart/classes/BxSnipcartPageBrowse.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES 
('bx_snipcart_popular', 1, 'bx_snipcart', '_bx_snipcart_page_block_title_popular_entries', 11, 2147483647, 'service', 'a:3:{s:6:"module";s:11:"bx_snipcart";s:6:"method";s:14:"browse_popular";s:6:"params";a:3:{s:9:"unit_view";s:7:"gallery";s:13:"empty_message";b:1;s:13:"ajax_paginate";b:0;}}', 0, 1, 1);

-- PAGE: recently updated entries
INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_snipcart_updated', '_bx_snipcart_page_title_sys_entries_updated', '_bx_snipcart_page_title_entries_updated', 'bx_snipcart', 5, 2147483647, 1, 'snipcart-updated', 'page.php?i=snipcart-updated', '', '', '', 0, 1, 0, 'BxSnipcartPageBrowse', 'modules/boonex/snipcart/classes/BxSnipcartPageBrowse.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES 
('bx_snipcart_updated', 1, 'bx_snipcart', '_bx_snipcart_page_block_title_updated_entries', 11, 2147483647, 'service', 'a:3:{s:6:"module";s:11:"bx_snipcart";s:6:"method";s:14:"browse_updated";s:6:"params";a:3:{s:9:"unit_view";s:7:"gallery";s:13:"empty_message";b:1;s:13:"ajax_paginate";b:0;}}', 0, 1, 1);

-- PAGE: entries of author
INSERT INTO `sys_objects_page`(`object`, `uri`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_snipcart_author', 'snipcart-author', '_bx_snipcart_page_title_sys_entries_of_author', '_bx_snipcart_page_title_entries_of_author', 'bx_snipcart', 5, 2147483647, 1, '', '', '', '', 0, 1, 0, 'BxSnipcartPageAuthor', 'modules/boonex/snipcart/classes/BxSnipcartPageAuthor.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES 
('bx_snipcart_author', 1, 'bx_snipcart', '', '_bx_snipcart_page_block_title_entries_actions', 13, 2147483647, 'service', 'a:2:{s:6:"module";s:11:"bx_snipcart";s:6:"method";s:18:"my_entries_actions";}', 0, 0, 1, 1),
('bx_snipcart_author', 1, 'bx_snipcart', '_bx_snipcart_page_block_title_sys_favorites_of_author', '_bx_snipcart_page_block_title_favorites_of_author', 11, 2147483647, 'service', 'a:3:{s:6:"module";s:11:"bx_snipcart";s:6:"method";s:15:"browse_favorite";s:6:"params";a:1:{i:0;s:12:"{profile_id}";}}', 0, 1, 1, 2),
('bx_snipcart_author', 1, 'bx_snipcart', '_bx_snipcart_page_block_title_sys_entries_of_author', '_bx_snipcart_page_block_title_entries_of_author', 11, 2147483647, 'service', 'a:2:{s:6:"module";s:11:"bx_snipcart";s:6:"method";s:13:"browse_author";}', 0, 0, 1, 3);

-- PAGE: entries in context
INSERT INTO `sys_objects_page`(`object`, `uri`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_snipcart_context', 'snipcart-context', '_bx_snipcart_page_title_sys_entries_in_context', '_bx_snipcart_page_title_entries_in_context', 'bx_snipcart', 5, 2147483647, 1, '', '', '', '', 0, 1, 0, 'BxSnipcartPageAuthor', 'modules/boonex/snipcart/classes/BxSnipcartPageAuthor.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES 
('bx_snipcart_context', 1, 'bx_snipcart', '_bx_snipcart_page_block_title_sys_entries_in_context', '_bx_snipcart_page_block_title_entries_in_context', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:11:\"bx_snipcart\";s:6:\"method\";s:14:\"browse_context\";}', 0, 0, 1, 1);

-- PAGE: module home
INSERT INTO `sys_objects_page`(`object`, `uri`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_snipcart_home', 'snipcart-home', '_bx_snipcart_page_title_sys_home', '_bx_snipcart_page_title_home', 'bx_snipcart', 2, 2147483647, 1, 'page.php?i=snipcart-home', '', '', '', 0, 1, 0, 'BxSnipcartPageBrowse', 'modules/boonex/snipcart/classes/BxSnipcartPageBrowse.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES 
('bx_snipcart_home', 1, 'bx_snipcart', '', '_bx_snipcart_page_block_title_featured_entries_view_extended', 11, 2147483647, 'service', 'a:3:{s:6:"module";s:11:"bx_snipcart";s:6:"method";s:15:"browse_featured";s:6:"params";a:1:{i:0;s:8:"extended";}}', 0, 1, 1, 0),
('bx_snipcart_home', 1, 'bx_snipcart', '', '_bx_snipcart_page_block_title_recent_entries_view_extended', 11, 2147483647, 'service', 'a:3:{s:6:"module";s:11:"bx_snipcart";s:6:"method";s:13:"browse_public";s:6:"params";a:1:{i:0;s:8:"extended";}}', 0, 1, 1, 1),
('bx_snipcart_home', 2, 'bx_snipcart', '', '_bx_snipcart_page_block_title_popular_keywords', 11, 2147483647, 'service', 'a:4:{s:6:"module";s:6:"system";s:6:"method";s:14:"keywords_cloud";s:6:"params";a:2:{i:0;s:11:"bx_snipcart";i:1;s:11:"bx_snipcart";}s:5:"class";s:20:"TemplServiceMetatags";}', 0, 1, 1, 0),
('bx_snipcart_home', 2, 'bx_snipcart', '', '_bx_snipcart_page_block_title_cats', 11, 2147483647, 'service', 'a:4:{s:6:"module";s:6:"system";s:6:"method";s:15:"categories_list";s:6:"params";a:2:{i:0;s:16:"bx_snipcart_cats";i:1;a:1:{s:10:"show_empty";b:1;}}s:5:"class";s:20:"TemplServiceCategory";}', 0, 1, 1, 1);

-- PAGE: search for entries
INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_snipcart_search', '_bx_snipcart_page_title_sys_entries_search', '_bx_snipcart_page_title_entries_search', 'bx_snipcart', 5, 2147483647, 1, 'snipcart-search', 'page.php?i=snipcart-search', '', '', '', 0, 1, 0, 'BxSnipcartPageBrowse', 'modules/boonex/snipcart/classes/BxSnipcartPageBrowse.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES 
('bx_snipcart_search', 1, 'bx_snipcart', '_bx_snipcart_page_block_title_search_form', 11, 2147483647, 'service', 'a:4:{s:6:"module";s:6:"system";s:6:"method";s:8:"get_form";s:6:"params";a:1:{i:0;a:1:{s:6:"object";s:11:"bx_snipcart";}}s:5:"class";s:27:"TemplSearchExtendedServices";}', 0, 1, 1, 1),
('bx_snipcart_search', 1, 'bx_snipcart', '_bx_snipcart_page_block_title_search_results', 11, 2147483647, 'service', 'a:4:{s:6:"module";s:6:"system";s:6:"method";s:11:"get_results";s:6:"params";a:1:{i:0;a:2:{s:6:"object";s:11:"bx_snipcart";s:10:"show_empty";b:1;}}s:5:"class";s:27:"TemplSearchExtendedServices";}', 0, 1, 1, 2),
('bx_snipcart_search', 1, 'bx_snipcart', '_bx_snipcart_page_block_title_search_form_cmts', 11, 2147483647, 'service', 'a:4:{s:6:"module";s:6:"system";s:6:"method";s:8:"get_form";s:6:"params";a:1:{i:0;a:1:{s:6:"object";s:16:"bx_snipcart_cmts";}}s:5:"class";s:27:"TemplSearchExtendedServices";}', 0, 1, 0, 3),
('bx_snipcart_search', 1, 'bx_snipcart', '_bx_snipcart_page_block_title_search_results_cmts', 11, 2147483647, 'service', 'a:4:{s:6:"module";s:6:"system";s:6:"method";s:11:"get_results";s:6:"params";a:1:{i:0;a:2:{s:6:"object";s:16:"bx_snipcart_cmts";s:10:"show_empty";b:1;}}s:5:"class";s:27:"TemplSearchExtendedServices";}', 0, 1, 0, 4);

-- PAGE: module manage own
INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_snipcart_manage', '_bx_snipcart_page_title_sys_manage', '_bx_snipcart_page_title_manage', 'bx_snipcart', 5, 2147483647, 1, 'snipcart-manage', 'page.php?i=snipcart-manage', '', '', '', 0, 1, 0, 'BxSnipcartPageBrowse', 'modules/boonex/snipcart/classes/BxSnipcartPageBrowse.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES 
('bx_snipcart_manage', 1, 'bx_snipcart', '_bx_snipcart_page_block_title_system_manage', '_bx_snipcart_page_block_title_manage', 11, 2147483647, 'service', 'a:2:{s:6:"module";s:11:"bx_snipcart";s:6:"method";s:12:"manage_tools";}}', 0, 1, 0);

-- PAGE: module manage all
INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_snipcart_administration', '_bx_snipcart_page_title_sys_manage_administration', '_bx_snipcart_page_title_manage', 'bx_snipcart', 5, 192, 1, 'snipcart-administration', 'page.php?i=snipcart-administration', '', '', '', 0, 1, 0, 'BxSnipcartPageBrowse', 'modules/boonex/snipcart/classes/BxSnipcartPageBrowse.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES 
('bx_snipcart_administration', 1, 'bx_snipcart', '_bx_snipcart_page_block_title_system_manage_administration', '_bx_snipcart_page_block_title_manage', 11, 192, 'service', 'a:3:{s:6:"module";s:11:"bx_snipcart";s:6:"method";s:12:"manage_tools";s:6:"params";a:1:{i:0;s:14:"administration";}}', 0, 1, 0);

-- PAGE: module settings
INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_snipcart_settings', '_bx_snipcart_page_title_sys_settings', '_bx_snipcart_page_title_settings', 'bx_snipcart', 5, 2147483647, 1, 'snipcart-settings', 'page.php?i=snipcart-settings', '', '', '', 0, 1, 0, 'BxSnipcartPageBrowse', 'modules/boonex/snipcart/classes/BxSnipcartPageBrowse.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES 
('bx_snipcart_settings', 1, 'bx_snipcart', '_bx_snipcart_page_block_title_system_settings', '_bx_snipcart_page_block_title_settings', 11, 2147483647, 'service', 'a:2:{s:6:"module";s:11:"bx_snipcart";s:6:"method";s:8:"settings";}}', 0, 1, 0);

-- PAGE: add block to homepage
SET @iBlockOrder = (SELECT `order` FROM `sys_pages_blocks` WHERE `object` = 'sys_home' AND `cell_id` = 1 ORDER BY `order` DESC LIMIT 1);
INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES 
('sys_home', 1, 'bx_snipcart', '_bx_snipcart_page_block_title_recent_entries', 11, 2147483647, 'service', 'a:3:{s:6:"module";s:11:"bx_snipcart";s:6:"method";s:13:"browse_public";s:6:"params";a:2:{i:0;b:0;i:1;b:0;}}', 1, 0, 0, IFNULL(@iBlockOrder, 0) + 1);

-- PAGES: add page block to profiles modules (trigger* page objects are processed separately upon modules enable/disable)
SET @iPBCellProfile = 3;
INSERT INTO `sys_pages_blocks` (`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES
('trigger_page_profile_view_entry', @iPBCellProfile, 'bx_snipcart', '_bx_snipcart_page_block_title_my_entries', 11, 2147483647, 'service', 'a:3:{s:6:"module";s:11:"bx_snipcart";s:6:"method";s:13:"browse_author";s:6:"params";a:2:{i:0;s:12:"{profile_id}";i:1;a:2:{s:8:"per_page";s:28:"bx_snipcart_per_page_profile";s:13:"empty_message";b:0;}}}', 0, 0, 0);

-- PAGE: service blocks
SET @iBlockOrder = (SELECT `order` FROM `sys_pages_blocks` WHERE `object` = '' AND `cell_id` = 0 ORDER BY `order` DESC LIMIT 1);
INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system` , `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES 
('', 0, 'bx_snipcart', '', '_bx_snipcart_page_block_title_recent_entries', 11, 2147483647, 'service', 'a:3:{s:6:"module";s:11:"bx_snipcart";s:6:"method";s:13:"browse_public";s:6:"params";a:3:{s:9:"unit_view";s:7:"gallery";s:13:"empty_message";b:1;s:13:"ajax_paginate";b:0;}}', 0, 1, IFNULL(@iBlockOrder, 0) + 1),

('', 0, 'bx_snipcart', '', '_bx_snipcart_page_block_title_recent_entries_view_full', 11, 2147483647, 'service', 'a:3:{s:6:"module";s:11:"bx_snipcart";s:6:"method";s:13:"browse_public";s:6:"params";a:1:{i:0;s:4:"full";}}', 0, 1, IFNULL(@iBlockOrder, 0) + 2),
('', 0, 'bx_snipcart', '', '_bx_snipcart_page_block_title_popular_entries_view_extended', 11, 2147483647, 'service', 'a:3:{s:6:"module";s:11:"bx_snipcart";s:6:"method";s:14:"browse_popular";s:6:"params";a:1:{i:0;s:8:"extended";}}', 0, 1, IFNULL(@iBlockOrder, 0) + 3),
('', 0, 'bx_snipcart', '', '_bx_snipcart_page_block_title_popular_entries_view_full', 11, 2147483647, 'service', 'a:3:{s:6:"module";s:11:"bx_snipcart";s:6:"method";s:14:"browse_popular";s:6:"params";a:1:{i:0;s:4:"full";}}', 0, 1, IFNULL(@iBlockOrder, 0) + 4),
('', 0, 'bx_snipcart', '_bx_snipcart_page_block_title_sys_recent_entries_view_showcase', '_bx_snipcart_page_block_title_recent_entries_view_showcase', 11, 2147483647, 'service', 'a:3:{s:6:\"module\";s:11:\"bx_snipcart\";s:6:\"method\";s:13:\"browse_public\";s:6:\"params\";a:3:{s:9:\"unit_view\";s:8:\"showcase\";s:13:\"empty_message\";b:0;s:13:\"ajax_paginate\";b:0;}}', 0, 1, IFNULL(@iBlockOrder, 0) + 5),
('', 0, 'bx_snipcart', '_bx_snipcart_page_block_title_sys_popular_entries_view_showcase',  '_bx_snipcart_page_block_title_popular_entries_view_showcase', 11, 2147483647, 'service', 'a:3:{s:6:\"module\";s:11:\"bx_snipcart\";s:6:\"method\";s:13:\"browse_popular\";s:6:\"params\";a:3:{s:9:\"unit_view\";s:8:\"showcase\";s:13:\"empty_message\";b:0;s:13:\"ajax_paginate\";b:0;}}', 0, 1, IFNULL(@iBlockOrder, 0) + 6),
('', 0, 'bx_snipcart', '_bx_snipcart_page_block_title_sys_featured_entries_view_showcase', '_bx_snipcart_page_block_title_featured_entries_view_showcase', 11, 2147483647, 'service', 'a:3:{s:6:\"module\";s:11:\"bx_snipcart\";s:6:\"method\";s:15:\"browse_featured\";s:6:\"params\";a:3:{s:9:\"unit_view\";s:8:\"showcase\";s:13:\"empty_message\";b:0;s:13:\"ajax_paginate\";b:0;}}', 0, 1, IFNULL(@iBlockOrder, 0) + 7);


-- MENU: add to site menu
SET @iSiteMenuOrder = (SELECT `order` FROM `sys_menu_items` WHERE `set_name` = 'sys_site' AND `active` = 1 ORDER BY `order` DESC LIMIT 1);
INSERT INTO `sys_menu_items` (`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('sys_site', 'bx_snipcart', 'snipcart-home', '_bx_snipcart_menu_item_title_system_entries_home', '_bx_snipcart_menu_item_title_entries_home', 'page.php?i=snipcart-home', '', '', 'shopping-cart col-green2', 'bx_snipcart_submenu', 2147483647, 1, 1, IFNULL(@iSiteMenuOrder, 0) + 1);

-- MENU: add to homepage menu
SET @iHomepageMenuOrder = (SELECT `order` FROM `sys_menu_items` WHERE `set_name` = 'sys_homepage' AND `active` = 1 ORDER BY `order` DESC LIMIT 1);
INSERT INTO `sys_menu_items` (`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('sys_homepage', 'bx_snipcart', 'snipcart-home', '_bx_snipcart_menu_item_title_system_entries_home', '_bx_snipcart_menu_item_title_entries_home', 'page.php?i=snipcart-home', '', '', 'shopping-cart col-green2', 'bx_snipcart_submenu', 2147483647, 1, 1, IFNULL(@iHomepageMenuOrder, 0) + 1);

-- MENU: add to "add content" menu
SET @iAddMenuOrder = (SELECT `order` FROM `sys_menu_items` WHERE `set_name` = 'sys_add_content_links' AND `active` = 1 ORDER BY `order` DESC LIMIT 1);
INSERT INTO `sys_menu_items` (`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('sys_add_content_links', 'bx_snipcart', 'create-snipcart-entry', '_bx_snipcart_menu_item_title_system_create_entry', '_bx_snipcart_menu_item_title_create_entry', 'page.php?i=create-snipcart-entry', '', '', 'shopping-cart col-green2', '', 2147483647, 1, 1, IFNULL(@iAddMenuOrder, 0) + 1);

-- MENU: actions menu for view entry 
INSERT INTO `sys_objects_menu`(`object`, `title`, `set_name`, `module`, `template_id`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_snipcart_view', '_bx_snipcart_menu_title_view_entry', 'bx_snipcart_view', 'bx_snipcart', 9, 0, 1, 'BxSnipcartMenuView', 'modules/boonex/snipcart/classes/BxSnipcartMenuView.php');

INSERT INTO `sys_menu_sets`(`set_name`, `module`, `title`, `deletable`) VALUES 
('bx_snipcart_view', 'bx_snipcart', '_bx_snipcart_menu_set_title_view_entry', 0);

INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('bx_snipcart_view', 'bx_snipcart', 'edit-snipcart-entry', '_bx_snipcart_menu_item_title_system_edit_entry', '_bx_snipcart_menu_item_title_edit_entry', 'page.php?i=edit-snipcart-entry&id={content_id}', '', '', 'pencil-alt', '', 2147483647, 1, 0, 1),
('bx_snipcart_view', 'bx_snipcart', 'delete-snipcart-entry', '_bx_snipcart_menu_item_title_system_delete_entry', '_bx_snipcart_menu_item_title_delete_entry', 'page.php?i=delete-snipcart-entry&id={content_id}', '', '', 'remove', '', 2147483647, 1, 0, 2);

-- MENU: actions menu for my entries
INSERT INTO `sys_objects_menu`(`object`, `title`, `set_name`, `module`, `template_id`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_snipcart_my', '_bx_snipcart_menu_title_entries_my', 'bx_snipcart_my', 'bx_snipcart', 9, 0, 1, 'BxSnipcartMenu', 'modules/boonex/snipcart/classes/BxSnipcartMenu.php');

INSERT INTO `sys_menu_sets`(`set_name`, `module`, `title`, `deletable`) VALUES 
('bx_snipcart_my', 'bx_snipcart', '_bx_snipcart_menu_set_title_entries_my', 0);

INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('bx_snipcart_my', 'bx_snipcart', 'create-snipcart-entry', '_bx_snipcart_menu_item_title_system_create_entry', '_bx_snipcart_menu_item_title_create_entry', 'page.php?i=create-snipcart-entry', '', '', 'plus', '', 2147483647, 1, 0, 0);

-- MENU: module sub-menu
INSERT INTO `sys_objects_menu`(`object`, `title`, `set_name`, `module`, `template_id`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_snipcart_submenu', '_bx_snipcart_menu_title_submenu', 'bx_snipcart_submenu', 'bx_snipcart', 8, 0, 1, '', '');

INSERT INTO `sys_menu_sets`(`set_name`, `module`, `title`, `deletable`) VALUES 
('bx_snipcart_submenu', 'bx_snipcart', '_bx_snipcart_menu_set_title_submenu', 0);

INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('bx_snipcart_submenu', 'bx_snipcart', 'snipcart-home', '_bx_snipcart_menu_item_title_system_entries_public', '_bx_snipcart_menu_item_title_entries_public', 'page.php?i=snipcart-home', '', '', '', '', 2147483647, 1, 1, 1),
('bx_snipcart_submenu', 'bx_snipcart', 'snipcart-popular', '_bx_snipcart_menu_item_title_system_entries_popular', '_bx_snipcart_menu_item_title_entries_popular', 'page.php?i=snipcart-popular', '', '', '', '', 2147483647, 1, 1, 2),
('bx_snipcart_submenu', 'bx_snipcart', 'snipcart-search', '_bx_snipcart_menu_item_title_system_entries_search', '_bx_snipcart_menu_item_title_entries_search', 'page.php?i=snipcart-search', '', '', '', '', 2147483647, 1, 1, 3),
('bx_snipcart_submenu', 'bx_snipcart', 'snipcart-manage', '_bx_snipcart_menu_item_title_system_entries_manage', '_bx_snipcart_menu_item_title_entries_manage', 'page.php?i=snipcart-manage', '', '', '', '', 2147483646, 1, 1, 4),
('bx_snipcart_submenu', 'bx_snipcart', 'snipcart-settings', '_bx_snipcart_menu_item_title_system_settings', '_bx_snipcart_menu_item_title_settings', 'page.php?i=snipcart-settings', '', '', '', '', 2147483646, 1, 1, 5),
('bx_snipcart_submenu', 'bx_snipcart', 'snipcart-dashboard', '_bx_snipcart_menu_item_title_system_dashboard', '_bx_snipcart_menu_item_title_dashboard', 'https://app.snipcart.com', '', '_blank', '', '', 2147483646, 1, 1, 6);

-- MENU: sub-menu for view entry
INSERT INTO `sys_objects_menu`(`object`, `title`, `set_name`, `module`, `template_id`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_snipcart_view_submenu', '_bx_snipcart_menu_title_view_entry_submenu', 'bx_snipcart_view_submenu', 'bx_snipcart', 8, 0, 0, 'BxSnipcartMenuView', 'modules/boonex/snipcart/classes/BxSnipcartMenuView.php');

INSERT INTO `sys_menu_sets`(`set_name`, `module`, `title`, `deletable`) VALUES 
('bx_snipcart_view_submenu', 'bx_snipcart', '_bx_snipcart_menu_set_title_view_entry_submenu', 0);

INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('bx_snipcart_view_submenu', 'bx_snipcart', 'view-snipcart-entry', '_bx_snipcart_menu_item_title_system_view_entry', '_bx_snipcart_menu_item_title_view_entry_submenu_entry', 'page.php?i=view-snipcart-entry&id={content_id}', '', '', '', '', 2147483647, 1, 0, 1),
('bx_snipcart_view_submenu', 'bx_snipcart', 'view-snipcart-entry-comments', '_bx_snipcart_menu_item_title_system_view_entry_comments', '_bx_snipcart_menu_item_title_view_entry_submenu_comments', 'page.php?i=view-snipcart-entry-comments&id={content_id}', '', '', '', '', 2147483647, 0, 0, 2);

-- MENU: custom menu for snippet meta info
INSERT INTO `sys_objects_menu`(`object`, `title`, `set_name`, `module`, `template_id`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_snipcart_snippet_meta', '_sys_menu_title_snippet_meta', 'bx_snipcart_snippet_meta', 'bx_snipcart', 15, 0, 1, 'BxSnipcartMenuSnippetMeta', 'modules/boonex/snipcart/classes/BxSnipcartMenuSnippetMeta.php');

INSERT INTO `sys_menu_sets`(`set_name`, `module`, `title`, `deletable`) VALUES 
('bx_snipcart_snippet_meta', 'bx_snipcart', '_sys_menu_set_title_snippet_meta', 0);

INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `editable`, `order`) VALUES 
('bx_snipcart_snippet_meta', 'bx_snipcart', 'date', '_sys_menu_item_title_system_sm_date', '_sys_menu_item_title_sm_date', '', '', '', '', '', 2147483647, 1, 0, 1, 1),
('bx_snipcart_snippet_meta', 'bx_snipcart', 'author', '_sys_menu_item_title_system_sm_author', '_sys_menu_item_title_sm_author', '', '', '', '', '', 2147483647, 1, 0, 1, 2),
('bx_snipcart_snippet_meta', 'bx_snipcart', 'category', '_sys_menu_item_title_system_sm_category', '_sys_menu_item_title_sm_category', '', '', '', '', '', 2147483647, 0, 0, 1, 3),
('bx_snipcart_snippet_meta', 'bx_snipcart', 'tags', '_sys_menu_item_title_system_sm_tags', '_sys_menu_item_title_sm_tags', '', '', '', '', '', 2147483647, 0, 0, 1, 4),
('bx_snipcart_snippet_meta', 'bx_snipcart', 'views', '_sys_menu_item_title_system_sm_views', '_sys_menu_item_title_sm_views', '', '', '', '', '', 2147483647, 0, 0, 1, 5),
('bx_snipcart_snippet_meta', 'bx_snipcart', 'comments', '_sys_menu_item_title_system_sm_comments', '_sys_menu_item_title_sm_comments', '', '', '', '', '', 2147483647, 0, 0, 1, 6);

-- MENU: profile stats
SET @iNotifMenuOrder = (SELECT IFNULL(MAX(`order`), 0) FROM `sys_menu_items` WHERE `set_name` = 'sys_profile_stats' AND `active` = 1 LIMIT 1);
INSERT INTO `sys_menu_items` (`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `addon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES
('sys_profile_stats', 'bx_snipcart', 'profile-stats-manage-snipcart', '_bx_snipcart_menu_item_title_system_manage_my_entries', '_bx_snipcart_menu_item_title_manage_my_entries', 'page.php?i=snipcart-manage', '', '_self', 'shopping-cart col-green2', 'a:2:{s:6:"module";s:11:"bx_snipcart";s:6:"method";s:41:"get_menu_addon_manage_tools_profile_stats";}', '', 2147483646, 1, 0, @iNotifMenuOrder + 1);

-- MENU: manage tools submenu
INSERT INTO `sys_objects_menu`(`object`, `title`, `set_name`, `module`, `template_id`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_snipcart_menu_manage_tools', '_bx_snipcart_menu_title_manage_tools', 'bx_snipcart_menu_manage_tools', 'bx_snipcart', 6, 0, 1, 'BxSnipcartMenuManageTools', 'modules/boonex/snipcart/classes/BxSnipcartMenuManageTools.php');

INSERT INTO `sys_menu_sets`(`set_name`, `module`, `title`, `deletable`) VALUES 
('bx_snipcart_menu_manage_tools', 'bx_snipcart', '_bx_snipcart_menu_set_title_manage_tools', 0);

--INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
--('bx_snipcart_menu_manage_tools', 'bx_snipcart', 'delete-with-content', '_bx_snipcart_menu_item_title_system_delete_with_content', '_bx_snipcart_menu_item_title_delete_with_content', 'javascript:void(0)', 'javascript:{js_object}.onClickDeleteWithContent({content_id});', '_self', 'far trash-alt', '', 128, 1, 0, 0);

-- MENU: dashboard manage tools
SET @iManageMenuOrder = (SELECT IFNULL(MAX(`order`), 0) FROM `sys_menu_items` WHERE `set_name`='sys_account_dashboard_manage_tools' LIMIT 1);
INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `addon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('sys_account_dashboard_manage_tools', 'bx_snipcart', 'snipcart-administration', '_bx_snipcart_menu_item_title_system_admt_entries', '_bx_snipcart_menu_item_title_admt_entries', 'page.php?i=snipcart-administration', '', '_self', '', 'a:2:{s:6:"module";s:11:"bx_snipcart";s:6:"method";s:27:"get_menu_addon_manage_tools";}', '', 192, 1, 0, @iManageMenuOrder + 1);

-- MENU: add menu item to profiles modules (trigger* menu sets are processed separately upon modules enable/disable)
INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('trigger_profile_view_submenu', 'bx_snipcart', 'snipcart-author', '_bx_snipcart_menu_item_title_system_view_entries_author', '_bx_snipcart_menu_item_title_view_entries_author', 'page.php?i=snipcart-author&profile_id={profile_id}', '', '', 'shopping-cart col-green2', '', 2147483647, 1, 0, 0),
('trigger_group_view_submenu', 'bx_snipcart', 'snipcart-context', '_bx_snipcart_menu_item_title_system_view_entries_in_context', '_bx_snipcart_menu_item_title_view_entries_in_context', 'page.php?i=snipcart-context&profile_id={profile_id}', '', '', 'shopping-cart col-green2', '', 2147483647, 1, 0, 0);
  
-- PRIVACY 
INSERT INTO `sys_objects_privacy` (`object`, `module`, `action`, `title`, `default_group`, `table`, `table_field_id`, `table_field_author`, `override_class_name`, `override_class_file`) VALUES
('bx_snipcart_allow_view_to', 'bx_snipcart', 'view', '_bx_snipcart_form_entry_input_allow_view_to', '3', 'bx_snipcart_entries', 'id', 'author', '', '');


-- ACL
INSERT INTO `sys_acl_actions` (`Module`, `Name`, `AdditionalParamName`, `Title`, `Desc`, `Countable`, `DisabledForLevels`) VALUES
('bx_snipcart', 'create entry', NULL, '_bx_snipcart_acl_action_create_entry', '', 1, 3);
SET @iIdActionEntryCreate = LAST_INSERT_ID();

INSERT INTO `sys_acl_actions` (`Module`, `Name`, `AdditionalParamName`, `Title`, `Desc`, `Countable`, `DisabledForLevels`) VALUES
('bx_snipcart', 'delete entry', NULL, '_bx_snipcart_acl_action_delete_entry', '', 1, 3);
SET @iIdActionEntryDelete = LAST_INSERT_ID();

INSERT INTO `sys_acl_actions` (`Module`, `Name`, `AdditionalParamName`, `Title`, `Desc`, `Countable`, `DisabledForLevels`) VALUES
('bx_snipcart', 'view entry', NULL, '_bx_snipcart_acl_action_view_entry', '', 1, 0);
SET @iIdActionEntryView = LAST_INSERT_ID();

INSERT INTO `sys_acl_actions` (`Module`, `Name`, `AdditionalParamName`, `Title`, `Desc`, `Countable`, `DisabledForLevels`) VALUES
('bx_snipcart', 'set thumb', NULL, '_bx_snipcart_acl_action_set_thumb', '', 1, 3);
SET @iIdActionSetThumb = LAST_INSERT_ID();

INSERT INTO `sys_acl_actions` (`Module`, `Name`, `AdditionalParamName`, `Title`, `Desc`, `Countable`, `DisabledForLevels`) VALUES
('bx_snipcart', 'edit any entry', NULL, '_bx_snipcart_acl_action_edit_any_entry', '', 1, 3);
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
(@iPremium, @iIdActionEntryCreate),

-- entry delete
(@iStandard, @iIdActionEntryDelete),
(@iModerator, @iIdActionEntryDelete),
(@iAdministrator, @iIdActionEntryDelete),
(@iPremium, @iIdActionEntryDelete),

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
(@iStandard, @iIdActionSetThumb),
(@iModerator, @iIdActionSetThumb),
(@iAdministrator, @iIdActionSetThumb),
(@iPremium, @iIdActionSetThumb),

-- edit any entry
(@iModerator, @iIdActionEntryEditAny),
(@iAdministrator, @iIdActionEntryEditAny);


-- SEARCH
SET @iSearchOrder = (SELECT IFNULL(MAX(`Order`), 0) FROM `sys_objects_search`);
INSERT INTO `sys_objects_search` (`ObjectName`, `Title`, `Order`, `ClassName`, `ClassPath`) VALUES
('bx_snipcart', '_bx_snipcart', @iSearchOrder + 1, 'BxSnipcartSearchResult', 'modules/boonex/snipcart/classes/BxSnipcartSearchResult.php'),
('bx_snipcart_cmts', '_bx_snipcart_cmts', @iSearchOrder + 2, 'BxSnipcartCmtsSearchResult', 'modules/boonex/snipcart/classes/BxSnipcartCmtsSearchResult.php');


-- METATAGS
INSERT INTO `sys_objects_metatags` (`object`, `table_keywords`, `table_locations`, `table_mentions`, `override_class_name`, `override_class_file`) VALUES
('bx_snipcart', 'bx_snipcart_meta_keywords', 'bx_snipcart_meta_locations', 'bx_snipcart_meta_mentions', '', '');


-- CATEGORY
INSERT INTO `sys_objects_category` (`object`, `search_object`, `form_object`, `list_name`, `table`, `field`, `join`, `where`, `override_class_name`, `override_class_file`) VALUES
('bx_snipcart_cats', 'bx_snipcart', 'bx_snipcart', 'bx_snipcart_cats', 'bx_snipcart_entries', 'cat', 'INNER JOIN `sys_profiles` ON (`sys_profiles`.`id` = `bx_snipcart_entries`.`author`)', 'AND `sys_profiles`.`status` = ''active''', '', '');


-- STATS
SET @iMaxOrderStats = (SELECT IFNULL(MAX(`order`), 0) FROM `sys_statistics`);
INSERT INTO `sys_statistics` (`module`, `name`, `title`, `link`, `icon`, `query`, `order`) VALUES 
('bx_snipcart', 'bx_snipcart', '_bx_snipcart', 'page.php?i=snipcart-home', 'shopping-cart col-green2', 'SELECT COUNT(*) FROM `bx_snipcart_entries` WHERE 1 AND `status` = ''active'' AND `status_admin` = ''active''', @iMaxOrderStats + 1);

-- CHARTS
SET @iMaxOrderCharts = (SELECT IFNULL(MAX(`order`), 0) FROM `sys_objects_chart`);
INSERT INTO `sys_objects_chart` (`object`, `title`, `table`, `field_date_ts`, `field_date_dt`, `field_status`, `query`, `active`, `order`, `class_name`, `class_file`) VALUES
('bx_snipcart_growth', '_bx_snipcart_chart_growth', 'bx_snipcart_entries', 'added', '', 'status,status_admin', '', 1, @iMaxOrderCharts + 1, 'BxDolChartGrowth', ''),
('bx_snipcart_growth_speed', '_bx_snipcart_chart_growth_speed', 'bx_snipcart_entries', 'added', '', 'status,status_admin', '', 1, @iMaxOrderCharts + 2, 'BxDolChartGrowthSpeed', '');

-- GRIDS: moderation tools
INSERT INTO `sys_objects_grid` (`object`, `source_type`, `source`, `table`, `field_id`, `field_order`, `field_active`, `paginate_url`, `paginate_per_page`, `paginate_simple`, `paginate_get_start`, `paginate_get_per_page`, `filter_fields`, `filter_fields_translatable`, `filter_mode`, `sorting_fields`, `sorting_fields_translatable`, `visible_for_levels`, `override_class_name`, `override_class_file`) VALUES
('bx_snipcart_administration', 'Sql', 'SELECT * FROM `bx_snipcart_entries` WHERE 1 ', 'bx_snipcart_entries', 'id', 'added', 'status_admin', '', 20, NULL, 'start', '', 'title,text', '', 'like', 'reports', '', 192, 'BxSnipcartGridAdministration', 'modules/boonex/snipcart/classes/BxSnipcartGridAdministration.php'),
('bx_snipcart_common', 'Sql', 'SELECT * FROM `bx_snipcart_entries` WHERE 1 ', 'bx_snipcart_entries', 'id', 'added', 'status', '', 20, NULL, 'start', '', 'title,text', '', 'like', '', '', 2147483647, 'BxSnipcartGridCommon', 'modules/boonex/snipcart/classes/BxSnipcartGridCommon.php');

INSERT INTO `sys_grid_fields` (`object`, `name`, `title`, `width`, `translatable`, `chars_limit`, `params`, `order`) VALUES
('bx_snipcart_administration', 'checkbox', '_sys_select', '2%', 0, '', '', 1),
('bx_snipcart_administration', 'switcher', '_bx_snipcart_grid_column_title_adm_active', '8%', 0, '', '', 2),
('bx_snipcart_administration', 'reports', '_sys_txt_reports_title', '5%', 0, '', '', 3),
('bx_snipcart_administration', 'title', '_bx_snipcart_grid_column_title_adm_title', '25%', 0, '25', '', 4),
('bx_snipcart_administration', 'added', '_bx_snipcart_grid_column_title_adm_added', '20%', 1, '25', '', 5),
('bx_snipcart_administration', 'author', '_bx_snipcart_grid_column_title_adm_author', '20%', 0, '25', '', 6),
('bx_snipcart_administration', 'actions', '', '20%', 0, '', '', 7),

('bx_snipcart_common', 'checkbox', '_sys_select', '2%', 0, '', '', 1),
('bx_snipcart_common', 'switcher', '_bx_snipcart_grid_column_title_adm_active', '8%', 0, '', '', 2),
('bx_snipcart_common', 'title', '_bx_snipcart_grid_column_title_adm_title', '40%', 0, '35', '', 3),
('bx_snipcart_common', 'added', '_bx_snipcart_grid_column_title_adm_added', '30%', 1, '25', '', 4),
('bx_snipcart_common', 'actions', '', '20%', 0, '', '', 5);

INSERT INTO `sys_grid_actions` (`object`, `type`, `name`, `title`, `icon`, `icon_only`, `confirm`, `order`) VALUES
('bx_snipcart_administration', 'bulk', 'delete', '_bx_snipcart_grid_action_title_adm_delete', '', 0, 1, 1),
('bx_snipcart_administration', 'single', 'edit', '_bx_snipcart_grid_action_title_adm_edit', 'pencil-alt', 1, 0, 1),
('bx_snipcart_administration', 'single', 'delete', '_bx_snipcart_grid_action_title_adm_delete', 'remove', 1, 1, 2),
('bx_snipcart_administration', 'single', 'settings', '_bx_snipcart_grid_action_title_adm_more_actions', 'cog', 1, 0, 3),

('bx_snipcart_common', 'bulk', 'delete', '_bx_snipcart_grid_action_title_adm_delete', '', 0, 1, 1),
('bx_snipcart_common', 'single', 'edit', '_bx_snipcart_grid_action_title_adm_edit', 'pencil-alt', 1, 0, 1),
('bx_snipcart_common', 'single', 'delete', '_bx_snipcart_grid_action_title_adm_delete', 'remove', 1, 1, 2),
('bx_snipcart_common', 'single', 'settings', '_bx_snipcart_grid_action_title_adm_more_actions', 'cog', 1, 0, 3);


-- ALERTS
INSERT INTO `sys_alerts_handlers` (`name`, `class`, `file`, `service_call`) VALUES 
('bx_snipcart', 'BxSnipcartAlertsResponse', 'modules/boonex/snipcart/classes/BxSnipcartAlertsResponse.php', '');
SET @iHandler := LAST_INSERT_ID();

INSERT INTO `sys_alerts` (`unit`, `action`, `handler_id`) VALUES
('system', 'save_setting', @iHandler),
('profile', 'delete', @iHandler);
