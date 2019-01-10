SET @sName = 'bx_massmailer';


-- FORMS
DELETE FROM `sys_form_inputs` WHERE `object`=@sName AND `name`='is_one_per_account';
INSERT INTO `sys_form_inputs` (`object`, `module`, `name`, `value`, `values`, `checked`, `type`, `caption_system`, `caption`, `info`, `required`, `unique`, `collapsed`, `html`, `attrs`, `attrs_tr`, `attrs_wrapper`, `checker_func`, `checker_params`, `checker_error`, `db_pass`, `db_params`, `editable`, `deletable`) VALUES
(@sName, @sName, 'is_one_per_account', '1', '', 1, 'switcher', '_bx_massmailer_form_campaign_input_sys_is_one_per_account', '_bx_massmailer_form_campaign_input_is_one_per_account', '', 0, 0, 0, 0, '', '', '', '', '', '', 'Int', '', 1, 0);

DELETE FROM `sys_form_display_inputs` WHERE `display_name` IN ('bx_massmailer_campaign_add', 'bx_massmailer_campaign_edit') AND `input_name`='is_one_per_account';
INSERT INTO `sys_form_display_inputs` (`display_name`, `input_name`, `visible_for_levels`, `active`, `order`) VALUES
('bx_massmailer_campaign_add', 'is_one_per_account', 2147483647, 1, 7),
('bx_massmailer_campaign_edit', 'is_one_per_account', 2147483647, 1, 7);
