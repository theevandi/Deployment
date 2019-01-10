
-- Email template

INSERT INTO `sys_email_templates` (`Module`, `NameSystem`, `Name`, `Subject`, `Body`) VALUES
('bx_drupal', '_bx_drupal_et_password_generated', 'bx_drupal_password_generated', '_bx_drupal_et_password_generated_subject', '_bx_drupal_et_password_generated_body');

-- Auth objects

INSERT INTO `sys_objects_auths` (`Name`, `Title`, `Link`, `Icon`) VALUES
('bx_drupal', '_bx_drupal_auth_title', 'modules/?r=drupal/start', 'fab drupal');

-- Alerts

INSERT INTO `sys_alerts_handlers` SET `name` = 'bx_drupal', `class` = 'BxDrupalAlerts', `file` = 'modules/boonex/drupal_connect/classes/BxDrupalAlerts.php';

SET @iHandlerId := (SELECT `id` FROM `sys_alerts_handlers`  WHERE `name` = 'bx_drupal');

INSERT INTO `sys_alerts` (`unit`, `action`, `handler_id`) VALUES
('account', 'logout', @iHandlerId),
('profile', 'delete', @iHandlerId),
('profile', 'add', @iHandlerId),
('profile', 'show_login_form', @iHandlerId);

-- Options

SET @iTypeOrder = (SELECT MAX(`order`) FROM `sys_options_types` WHERE `group` = 'modules');
INSERT INTO `sys_options_types` (`group`, `name`, `caption`, `icon`, `order`) VALUES 
('modules', 'bx_drupal', '_bx_drupal_adm_stg_cpt_type', 'bx_drupal@modules/boonex/drupal_connect/|std-icon.svg', IF(NOT ISNULL(@iTypeOrder), @iTypeOrder + 1, 1));
SET @iTypeId = LAST_INSERT_ID();

INSERT INTO `sys_options_categories` (`type_id`, `name`, `caption`, `order` )  
VALUES (@iTypeId, 'bx_drupal_general', '_sys_connect_adm_stg_cpt_category_general', 1);
SET @iCategId = LAST_INSERT_ID();

INSERT INTO `sys_options` (`name`, `value`, `category_id`, `caption`, `type`, `check`, `check_error`, `order`, `extra`) VALUES
('bx_drupal_login_url', '', @iCategId, '_bx_drupal_option_login_url', 'digit', '', '', 10, ''),
('bx_drupal_redirect_page', 'index', @iCategId, '_sys_connect_option_redirect', 'select', '', '', 40, 'join,settings,dashboard,index'),
('bx_drupal_module', 'bx_persons', @iCategId, '_sys_connect_option_module', 'select', '', '', 50, 'a:2:{s:6:"module";s:9:"bx_drupal";s:6:"method";s:20:"get_profiles_modules";}'),
('bx_drupal_privacy', '3', @iCategId, '_sys_connect_option_privacy', 'select', '', '', 54, 'a:2:{s:6:"module";s:9:"bx_drupal";s:6:"method";s:18:"get_privacy_groups";}'),
('bx_drupal_confirm_email', 'on', @iCategId, '_sys_connect_option_confirm_email', 'checkbox', '', '', 70, ''),
('bx_drupal_approve', 'on', @iCategId, '_sys_connect_option_approve', 'checkbox', '', '', 80, '');

-- Pages

INSERT INTO `sys_objects_page`(`object`, `uri`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_drupal_error', 'drupal-error', '_bx_drupal_error', '_bx_drupal_error', 'bx_drupal', 5, 2147483647, 0, '', '', '', '', 0, 1, 0, 'BxDrupalPage', 'modules/boonex/drupal_connect/classes/BxDrupalPage.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES 
('bx_drupal_error', 1, 'bx_drupal', '_bx_drupal_error', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:9:\"bx_drupal\";s:6:\"method\";s:10:\"last_error\";}', 0, 0, 1, 1);

-- SET @iBlockOrder = (SELECT `order` FROM `sys_pages_blocks` WHERE `object` = 'sys_login' AND `cell_id` = 1 ORDER BY `order` DESC LIMIT 1);
-- INSERT INTO `sys_pages_blocks` (`object`, `cell_id`, `module`, `title_system`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES
-- ('sys_login', 1, 'bx_drupal', '_bx_drupal_block_system_title_login', '_bx_drupal_block_title_login', 11, 2147483647, 'service', 'a:2:{s:6:"module";s:9:"bx_drupal";s:6:"method";s:10:"login_form";}', 0, 1, 1, @iBlockOrder + 1);

