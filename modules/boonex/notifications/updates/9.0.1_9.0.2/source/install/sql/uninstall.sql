SET @sName = 'bx_notifications';


DROP TABLE IF EXISTS `bx_notifications_events`;
DROP TABLE IF EXISTS `bx_notifications_events2users`;
DROP TABLE IF EXISTS `bx_notifications_handlers`;


-- STUDIO PAGE & WIDGET
DELETE FROM `tp`, `tw`, `tpw`
USING `sys_std_pages` AS `tp`, `sys_std_widgets` AS `tw`, `sys_std_pages_widgets` AS `tpw`
WHERE `tp`.`id` = `tw`.`page_id` AND `tw`.`id` = `tpw`.`widget_id` AND `tp`.`name` = @sName;
