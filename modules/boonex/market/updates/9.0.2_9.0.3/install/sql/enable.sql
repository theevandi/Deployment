-- SETTINGS
UPDATE `sys_options_types` SET `icon`='bx_market@modules/boonex/market/|std-icon.svg' WHERE `name`='bx_market';


-- PAGES
UPDATE `sys_pages_blocks` SET `cell_id`='3', `designbox_id`='13', `active`='1' WHERE `object`='bx_market_view_entry' AND `title` IN ('_bx_market_page_block_title_entry_social_sharing');

DELETE FROM `sys_pages_blocks` WHERE `object`='bx_market_author' AND `title` IN ('_bx_market_page_block_title_favorites_of_author');
INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES 
('bx_market_author', 1, 'bx_market', '_bx_market_page_block_title_sys_favorites_of_author', '_bx_market_page_block_title_favorites_of_author', 11, 2147483647, 'service', 'a:3:{s:6:\"module\";s:9:\"bx_market\";s:6:\"method\";s:15:\"browse_favorite\";s:6:"params";a:1:{i:0;s:12:"{profile_id}";}}', 0, 1, 1, 2);