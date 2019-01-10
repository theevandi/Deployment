SET @sName = 'bx_stripe_connect';


-- SETTINGS
DELETE FROM `tot`, `toc`, `to` USING `sys_options_types` AS `tot` LEFT JOIN `sys_options_categories` AS `toc` ON `tot`.`id`=`toc`.`type_id` LEFT JOIN `sys_options` AS `to` ON `toc`.`id`=`to`.`category_id` WHERE `tot`.`name` = @sName;


-- PAGES
DELETE FROM `sys_objects_page` WHERE `module` = @sName;
DELETE FROM `sys_pages_blocks` WHERE `module` = @sName;


-- GRIDS
DELETE FROM `sys_objects_grid` WHERE `object` IN ('bx_stripe_connect_accounts');
DELETE FROM `sys_grid_fields` WHERE `object` IN ('bx_stripe_connect_accounts');
DELETE FROM `sys_grid_actions` WHERE `object` IN ('bx_stripe_connect_accounts');


-- ALERTS
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = @sName LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;