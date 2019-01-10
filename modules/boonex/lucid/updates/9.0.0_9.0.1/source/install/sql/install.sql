SET @sName = 'bx_lucid';


-- SETTINGS
INSERT INTO `sys_options_types`(`group`, `name`, `caption`, `icon`, `order`) VALUES 
('templates', @sName, '_bx_lucid_stg_cpt_type', 'bx_lucid@modules/boonex/lucid/|std-icon.svg', 2);
SET @iTypeId = LAST_INSERT_ID();

-- SETTINGS: Lucid template System
INSERT INTO `sys_options_categories`(`type_id`, `name`, `caption`, `order`) VALUES 
(@iTypeId, CONCAT(@sName, '_system'), '_bx_lucid_stg_cpt_category_system', 10);
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options`(`category_id`, `name`, `caption`, `value`, `type`, `extra`, `check`, `check_error`, `order`) VALUES
(@iCategoryId, CONCAT(@sName, '_switcher_title'), '_bx_lucid_stg_cpt_option_switcher_name', 'Lucid', 'digit', '', '', '', 1);


-- SETTINGS: Lucid template Styles General
INSERT INTO `sys_options_categories`(`type_id`, `name`, `caption`, `order`) VALUES 
(@iTypeId, CONCAT(@sName, '_styles_general'), '_bx_lucid_stg_cpt_category_styles_general', 20);
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options`(`category_id`, `name`, `caption`, `value`, `type`, `extra`, `check`, `check_error`, `order`) VALUES
(@iCategoryId, CONCAT(@sName, '_general_item_bg_color_hover'), '_bx_lucid_stg_cpt_option_general_item_bg_color_hover', 'rgba(210, 230, 250, 0.2)', 'rgba', '', '', '', 1),
(@iCategoryId, CONCAT(@sName, '_general_item_bg_color_active'), '_bx_lucid_stg_cpt_option_general_item_bg_color_active', 'rgba(210, 230, 250, 0.4)', 'rgba', '', '', '', 2);


-- SETTINGS: Lucid template Styles Header
INSERT INTO `sys_options_categories`(`type_id`, `name`, `caption`, `order`) VALUES 
(@iTypeId, CONCAT(@sName, '_styles_header'), '_bx_lucid_stg_cpt_category_styles_header', 30);
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options`(`category_id`, `name`, `caption`, `value`, `type`, `extra`, `check`, `check_error`, `order`) VALUES
(@iCategoryId, CONCAT(@sName, '_header_height'), '_bx_lucid_stg_cpt_option_header_height', '3.5rem', 'digit', '', '', '', 1),
(@iCategoryId, CONCAT(@sName, '_header_content_padding'), '_bx_lucid_stg_cpt_option_header_content_padding', '0px', 'digit', '', '', '', 2),
(@iCategoryId, CONCAT(@sName, '_header_bg_color'), '_bx_lucid_stg_cpt_option_header_bg_color', 'rgba(76, 103, 159, 1.0)', 'rgba', '', '', '', 3),
(@iCategoryId, CONCAT(@sName, '_header_bg_image'), '_bx_lucid_stg_cpt_option_header_bg_image', '', 'image', '', '', '', 4),
(@iCategoryId, CONCAT(@sName, '_header_bg_image_repeat'), '_bx_lucid_stg_cpt_option_header_bg_image_repeat', 'no-repeat', 'select', 'no-repeat,repeat,repeat-x,repeat-y', '', '', 5),
(@iCategoryId, CONCAT(@sName, '_header_bg_image_size'), '_bx_lucid_stg_cpt_option_header_bg_image_size', 'cover', 'select', 'auto,cover,contain', '', '', 6),
(@iCategoryId, CONCAT(@sName, '_site_logo'), '_bx_lucid_stg_cpt_option_site_logo', '', 'image', '', '', '', 7),
(@iCategoryId, CONCAT(@sName, '_site_logo_alt'), '_bx_lucid_stg_cpt_option_site_logo_alt', '', 'text', '', '', '', 8),
(@iCategoryId, CONCAT(@sName, '_site_logo_width'), '_bx_lucid_stg_cpt_option_site_logo_width', '240', 'digit', '', '', '', 9),
(@iCategoryId, CONCAT(@sName, '_site_logo_height'), '_bx_lucid_stg_cpt_option_site_logo_height', '48', 'digit', '', '', '', 10),
(@iCategoryId, CONCAT(@sName, '_header_border_color'), '_bx_lucid_stg_cpt_option_header_border_color', 'rgba(255, 255, 255, 1.0)', 'rgba', '', '', '', 11),
(@iCategoryId, CONCAT(@sName, '_header_border_size'), '_bx_lucid_stg_cpt_option_header_border_size', '1px', 'digit', '', '', '', 12),
(@iCategoryId, CONCAT(@sName, '_header_shadow'), '_bx_lucid_stg_cpt_option_header_shadow', 'none', 'digit', '', '', '', 13),
(@iCategoryId, CONCAT(@sName, '_header_icon_color'), '_bx_lucid_stg_cpt_option_header_icon_color', 'rgba(255, 255, 255, 0.8)', 'rgba', '', '', '', 14),
(@iCategoryId, CONCAT(@sName, '_header_icon_color_hover'), '_bx_lucid_stg_cpt_option_header_icon_color_hover', 'rgba(255, 255, 255, 1)', 'rgba', '', '', '', 15),
(@iCategoryId, CONCAT(@sName, '_header_link_color'), '_bx_lucid_stg_cpt_option_header_link_color', 'rgba(215, 235, 255, 1)', 'rgba', '', '', '', 16),
(@iCategoryId, CONCAT(@sName, '_header_link_color_hover'), '_bx_lucid_stg_cpt_option_header_link_color_hover', 'rgba(255, 255, 255, 1)', 'rgba', '', '', '', 17);

-- SETTINGS: Lucid template Styles Footer
INSERT INTO `sys_options_categories`(`type_id`, `name`, `caption`, `order`) VALUES 
(@iTypeId, CONCAT(@sName, '_styles_footer'), '_bx_lucid_stg_cpt_category_styles_footer', 40);
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options`(`category_id`, `name`, `caption`, `value`, `type`, `extra`, `check`, `check_error`, `order`) VALUES
(@iCategoryId, CONCAT(@sName, '_footer_bg_color'), '_bx_lucid_stg_cpt_option_footer_bg_color', 'rgba(40, 60, 80, 0.0)', 'rgba', '', '', '', 1),
(@iCategoryId, CONCAT(@sName, '_footer_bg_image'), '_bx_lucid_stg_cpt_option_footer_bg_image', '', 'image', '', '', '', 2),
(@iCategoryId, CONCAT(@sName, '_footer_bg_image_repeat'), '_bx_lucid_stg_cpt_option_footer_bg_image_repeat', 'no-repeat', 'select', 'no-repeat,repeat,repeat-x,repeat-y', '', '', 3),
(@iCategoryId, CONCAT(@sName, '_footer_bg_image_size'), '_bx_lucid_stg_cpt_option_footer_bg_image_size', 'cover', 'select', 'auto,cover,contain', '', '', 4),
(@iCategoryId, CONCAT(@sName, '_footer_content_padding'), '_bx_lucid_stg_cpt_option_footer_content_padding', '1rem', 'digit', '', '', '', 5),
(@iCategoryId, CONCAT(@sName, '_footer_border_color'), '_bx_lucid_stg_cpt_option_footer_border_color', 'rgba(40, 60, 80, 0.1)', 'rgba', '', '', '', 6),
(@iCategoryId, CONCAT(@sName, '_footer_border_size'), '_bx_lucid_stg_cpt_option_footer_border_size', '1px', 'digit', '', '', '', 7),
(@iCategoryId, CONCAT(@sName, '_footer_shadow'), '_bx_lucid_stg_cpt_option_footer_shadow', 'none', 'digit', '', '', '', 8),
(@iCategoryId, CONCAT(@sName, '_footer_font_color'), '_bx_lucid_stg_cpt_option_footer_font_color', 'rgba(40, 60, 80, 1)', 'rgba', '', '', '', 9),
(@iCategoryId, CONCAT(@sName, '_footer_icon_color'), '_bx_lucid_stg_cpt_option_footer_icon_color', 'rgba(30, 140, 240, 1)', 'rgba', '', '', '', 10),
(@iCategoryId, CONCAT(@sName, '_footer_icon_color_hover'), '_bx_lucid_stg_cpt_option_footer_icon_color_hover', 'rgba(10, 120, 220, 1)', 'rgba', '', '', '', 11),
(@iCategoryId, CONCAT(@sName, '_footer_link_color'), '_bx_lucid_stg_cpt_option_footer_link_color', 'rgba(30, 140, 240, 1)', 'rgba', '', '', '', 12),
(@iCategoryId, CONCAT(@sName, '_footer_link_color_hover'), '_bx_lucid_stg_cpt_option_footer_link_color_hover', 'rgba(10, 120, 220, 1)', 'rgba', '', '', '', 13);

-- SETTINGS: Lucid template Styles Body
INSERT INTO `sys_options_categories`(`type_id`, `name`, `caption`, `order`) VALUES 
(@iTypeId, CONCAT(@sName, '_styles_body'), '_bx_lucid_stg_cpt_category_styles_body', 50);
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options`(`category_id`, `name`, `caption`, `value`, `type`, `extra`, `check`, `check_error`, `order`) VALUES
(@iCategoryId, CONCAT(@sName, '_body_bg_color'), '_bx_lucid_stg_cpt_option_body_bg_color', 'rgb(230, 240, 250)', 'rgb', '', '', '', 1),
(@iCategoryId, CONCAT(@sName, '_body_bg_image'), '_bx_lucid_stg_cpt_option_body_bg_image', '', 'image', '', '', '', 2),
(@iCategoryId, CONCAT(@sName, '_body_bg_image_repeat'), '_bx_lucid_stg_cpt_option_body_bg_image_repeat', 'no-repeat', 'select', 'no-repeat,repeat,repeat-x,repeat-y', '', '', 3),
(@iCategoryId, CONCAT(@sName, '_body_bg_image_size'), '_bx_lucid_stg_cpt_option_body_bg_image_size', 'cover', 'select', 'auto,cover,contain', '', '', 4),
(@iCategoryId, CONCAT(@sName, '_page_width'), '_bx_lucid_stg_cpt_option_page_width', '1024', 'digit', '', '', '', 5),
(@iCategoryId, CONCAT(@sName, '_body_icon_color'), '_bx_lucid_stg_cpt_option_body_icon_color', 'rgba(30, 150, 250, 0.9)', 'rgba', '', '', '', 6),
(@iCategoryId, CONCAT(@sName, '_body_icon_color_hover'), '_bx_lucid_stg_cpt_option_body_icon_color_hover', 'rgba(30, 150, 250, 1)', 'rgba', '', '', '', 7),
(@iCategoryId, CONCAT(@sName, '_body_link_color'), '_bx_lucid_stg_cpt_option_body_link_color', 'rgba(30, 150, 250, 0.9)', 'rgba', '', '', '', 8),
(@iCategoryId, CONCAT(@sName, '_body_link_color_hover'), '_bx_lucid_stg_cpt_option_body_link_color_hover', 'rgba(30, 150, 250, 1)', 'rgba', '', '', '', 9);


-- SETTINGS: Lucid template Styles Cover
INSERT INTO `sys_options_categories`(`type_id`, `name`, `caption`, `order`) VALUES 
(@iTypeId, CONCAT(@sName, '_styles_cover'), '_bx_lucid_stg_cpt_category_styles_cover', 55);
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options`(`category_id`, `name`, `caption`, `value`, `type`, `extra`, `check`, `check_error`, `order`) VALUES
(@iCategoryId, CONCAT(@sName, '_cover_height'), '_bx_lucid_stg_cpt_option_cover_height', '30vh', 'digit', '', '', '', 1),
(@iCategoryId, CONCAT(@sName, '_cover_bg_color'), '_bx_lucid_stg_cpt_option_cover_bg_color', 'rgba(255, 255, 255, 1)', 'rgba', '', '', '', 2),
(@iCategoryId, CONCAT(@sName, '_cover_content_padding'), '_bx_lucid_stg_cpt_option_cover_content_padding', '2rem 3rem 2rem 3rem', 'digit', '', '', '', 3),
(@iCategoryId, CONCAT(@sName, '_cover_border_color'), '_bx_lucid_stg_cpt_option_cover_border_color', 'rgba(208, 208, 208, 0)', 'rgba', '', '', '', 4),
(@iCategoryId, CONCAT(@sName, '_cover_border_size'), '_bx_lucid_stg_cpt_option_cover_border_size', '0px', 'digit', '', '', '', 5),
(@iCategoryId, CONCAT(@sName, '_cover_border_radius'), '_bx_lucid_stg_cpt_option_cover_border_radius', '0px', 'digit', '', '', '', 6),
(@iCategoryId, CONCAT(@sName, '_cover_shadow'), '_bx_lucid_stg_cpt_option_cover_shadow', 'none', 'digit', '', '', '', 7),
(@iCategoryId, CONCAT(@sName, '_cover_icon_border_color'), '_bx_lucid_stg_cpt_option_cover_icon_border_color', 'rgba(208, 208, 208, 1)', 'rgba', '', '', '', 8),
(@iCategoryId, CONCAT(@sName, '_cover_icon_border_size'), '_bx_lucid_stg_cpt_option_cover_icon_border_size', '1px', 'digit', '', '', '', 9),
(@iCategoryId, CONCAT(@sName, '_cover_icon_border_radius'), '_bx_lucid_stg_cpt_option_cover_icon_border_radius', '3px', 'digit', '', '', '', 10),
(@iCategoryId, CONCAT(@sName, '_cover_icon_shadow'), '_bx_lucid_stg_cpt_option_cover_icon_shadow', 'none', 'digit', '', '', '', 11),
(@iCategoryId, CONCAT(@sName, '_cover_text_align'), '_bx_lucid_stg_cpt_option_cover_text_align', 'center', 'select', 'left,center,right', '', '', 12),
(@iCategoryId, CONCAT(@sName, '_cover_text_shadow'), '_bx_lucid_stg_cpt_option_cover_text_shadow', '0px 1px 3px rgba(0, 0, 0, .3)', 'digit', '', '', '', 13),
(@iCategoryId, CONCAT(@sName, '_cover_font_family'), '_bx_lucid_stg_cpt_option_cover_font_family', 'Arial, sans-serif', 'digit', '', '', '', 14),
(@iCategoryId, CONCAT(@sName, '_cover_font_size'), '_bx_lucid_stg_cpt_option_cover_font_size', '2.0rem', 'digit', '', '', '', 15),
(@iCategoryId, CONCAT(@sName, '_cover_font_color'), '_bx_lucid_stg_cpt_option_cover_font_color', 'rgba(255, 255, 255, 1)', 'rgba', '', '', '', 16),
(@iCategoryId, CONCAT(@sName, '_cover_font_weight'), '_bx_lucid_stg_cpt_option_cover_font_weight', '700', 'digit', '', '', '', 17);


-- SETTINGS: Lucid template Styles Block
INSERT INTO `sys_options_categories`(`type_id`, `name`, `caption`, `order`) VALUES 
(@iTypeId, CONCAT(@sName, '_styles_block'), '_bx_lucid_stg_cpt_category_styles_block', 60);
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options`(`category_id`, `name`, `caption`, `value`, `type`, `extra`, `check`, `check_error`, `order`) VALUES
(@iCategoryId, CONCAT(@sName, '_block_bg_color'), '_bx_lucid_stg_cpt_option_block_bg_color', 'rgba(245, 250, 255, 0.9)', 'rgba', '', '', '', 1),
(@iCategoryId, CONCAT(@sName, '_block_bg_image'), '_bx_lucid_stg_cpt_option_block_bg_image', '', 'image', '', '', '', 2),
(@iCategoryId, CONCAT(@sName, '_block_bg_image_repeat'), '_bx_lucid_stg_cpt_option_block_bg_image_repeat', 'no-repeat', 'select', 'no-repeat,repeat,repeat-x,repeat-y', '', '', 3),
(@iCategoryId, CONCAT(@sName, '_block_bg_image_size'), '_bx_lucid_stg_cpt_option_block_bg_image_size', 'cover', 'select', 'auto,cover,contain', '', '', 4),
(@iCategoryId, CONCAT(@sName, '_block_content_padding'), '_bx_lucid_stg_cpt_option_block_content_padding', '1rem', 'digit', '', '', '', 5),
(@iCategoryId, CONCAT(@sName, '_block_border_color'), '_bx_lucid_stg_cpt_option_block_border_color', 'rgba(0, 0, 0, 0)', 'rgba', '', '', '', 6),
(@iCategoryId, CONCAT(@sName, '_block_border_size'), '_bx_lucid_stg_cpt_option_block_border_size', '0px', 'digit', '', '', '', 7),
(@iCategoryId, CONCAT(@sName, '_block_border_radius'), '_bx_lucid_stg_cpt_option_block_border_radius', '3px', 'digit', '', '', '', 8),
(@iCategoryId, CONCAT(@sName, '_block_shadow'), '_bx_lucid_stg_cpt_option_block_shadow', '0px 1px 2px 0px rgba(0, 0, 0, 0.05)', 'digit', '', '', '', 9),
(@iCategoryId, CONCAT(@sName, '_block_title_bg_color'), '_bx_lucid_stg_cpt_option_block_title_bg_color', 'rgba(255, 255, 255, 0)', 'rgba', '', '', '', 10),
(@iCategoryId, CONCAT(@sName, '_block_title_padding'), '_bx_lucid_stg_cpt_option_block_title_padding', '0.3rem 1rem 0rem 1rem', 'digit', '', '', '', 11),
(@iCategoryId, CONCAT(@sName, '_block_title_border_color'), '_bx_lucid_stg_cpt_option_block_title_border_color', 'rgba(0, 0, 0, 0)', 'rgba', '', '', '', 12),
(@iCategoryId, CONCAT(@sName, '_block_title_border_size'), '_bx_lucid_stg_cpt_option_block_title_border_size', '0px', 'digit', '', '', '', 13),
(@iCategoryId, CONCAT(@sName, '_block_title_border_radius'), '_bx_lucid_stg_cpt_option_block_title_border_radius', '0px', 'digit', '', '', '', 14),
(@iCategoryId, CONCAT(@sName, '_block_title_font_family'), '_bx_lucid_stg_cpt_option_block_title_font_family', 'Arial, sans-serif', 'digit', '', '', '', 15),
(@iCategoryId, CONCAT(@sName, '_block_title_font_size'), '_bx_lucid_stg_cpt_option_block_title_font_size', '1rem', 'digit', '', '', '', 16),
(@iCategoryId, CONCAT(@sName, '_block_title_font_color'), '_bx_lucid_stg_cpt_option_block_title_font_color', 'rgba(40, 50, 60, 0.8)', 'rgba', '', '', '', 17),
(@iCategoryId, CONCAT(@sName, '_block_title_font_weight'), '_bx_lucid_stg_cpt_option_block_title_font_weight', '700', 'digit', '', '', '', 18),
(@iCategoryId, CONCAT(@sName, '_block_title_div_height'), '_bx_lucid_stg_cpt_option_block_title_div_height', '0px', 'digit', '', '', '', 19),
(@iCategoryId, CONCAT(@sName, '_block_title_div_bg_color'), '_bx_lucid_stg_cpt_option_block_title_div_bg_color', 'rgba(40, 60, 80, 0)', 'rgba', '', '', '', 20);


-- SETTINGS: Lucid template Styles Card
INSERT INTO `sys_options_categories`(`type_id`, `name`, `caption`, `order`) VALUES 
(@iTypeId, CONCAT(@sName, '_styles_card'), '_bx_lucid_stg_cpt_category_styles_card', 70);
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options`(`category_id`, `name`, `caption`, `value`, `type`, `extra`, `check`, `check_error`, `order`) VALUES
(@iCategoryId, CONCAT(@sName, '_card_bg_color'), '_bx_lucid_stg_cpt_option_card_bg_color', 'rgba(255, 255, 255, 1)', 'rgba', '', '', '', 1),
(@iCategoryId, CONCAT(@sName, '_card_bg_color_hover'), '_bx_lucid_stg_cpt_option_card_bg_color_hover', 'rgba(255, 255, 255, 0.5)', 'rgba', '', '', '', 2),
(@iCategoryId, CONCAT(@sName, '_card_bg_image'), '_bx_lucid_stg_cpt_option_card_bg_image', '', 'image', '', '', '', 3),
(@iCategoryId, CONCAT(@sName, '_card_bg_image_repeat'), '_bx_lucid_stg_cpt_option_card_bg_image_repeat', 'no-repeat', 'select', 'no-repeat,repeat,repeat-x,repeat-y', '', '', 4),
(@iCategoryId, CONCAT(@sName, '_card_bg_image_attachment'), '_bx_lucid_stg_cpt_option_card_bg_image_attachment', 'scroll', 'select', 'fixed,scroll,local', '', '', 5),
(@iCategoryId, CONCAT(@sName, '_card_bg_image_size'), '_bx_lucid_stg_cpt_option_card_bg_image_size', 'cover', 'select', 'auto,cover,contain', '', '', 6),
(@iCategoryId, CONCAT(@sName, '_card_content_padding'), '_bx_lucid_stg_cpt_option_card_content_padding', '1rem', 'digit', '', '', '', 7),
(@iCategoryId, CONCAT(@sName, '_card_border_color'), '_bx_lucid_stg_cpt_option_card_border_color', 'rgba(0, 0, 0, 0.1)', 'rgba', '', '', '', 8),
(@iCategoryId, CONCAT(@sName, '_card_border_size'), '_bx_lucid_stg_cpt_option_card_border_size', '1px', 'digit', '', '', '', 9),
(@iCategoryId, CONCAT(@sName, '_card_border_radius'), '_bx_lucid_stg_cpt_option_card_border_radius', '3px', 'digit', '', '', '', 10),
(@iCategoryId, CONCAT(@sName, '_card_shadow'), '_bx_lucid_stg_cpt_option_card_shadow', '0px 1px 3px 0px rgba(0, 0, 0, 0.05)', 'digit', '', '', '', 11);


-- SETTINGS: Lucid template Styles Popups
INSERT INTO `sys_options_categories`(`type_id`, `name`, `caption`, `order`) VALUES 
(@iTypeId, CONCAT(@sName, '_styles_popup'), '_bx_lucid_stg_cpt_category_styles_popup', 80);
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options`(`category_id`, `name`, `caption`, `value`, `type`, `extra`, `check`, `check_error`, `order`) VALUES
(@iCategoryId, CONCAT(@sName, '_popup_bg_color'), '_bx_lucid_stg_cpt_option_popup_bg_color', 'rgba(255, 255, 255, 1)', 'rgba', '', '', '', 1),
(@iCategoryId, CONCAT(@sName, '_popup_bg_image'), '_bx_lucid_stg_cpt_option_popup_bg_image', '', 'image', '', '', '', 2),
(@iCategoryId, CONCAT(@sName, '_popup_bg_image_repeat'), '_bx_lucid_stg_cpt_option_popup_bg_image_repeat', 'no-repeat', 'select', 'no-repeat,repeat,repeat-x,repeat-y', '', '', 3),
(@iCategoryId, CONCAT(@sName, '_popup_bg_image_size'), '_bx_lucid_stg_cpt_option_popup_bg_image_size', 'cover', 'select', 'auto,cover,contain', '', '', 4),
(@iCategoryId, CONCAT(@sName, '_popup_content_padding'), '_bx_lucid_stg_cpt_option_popup_content_padding', '1.25rem', 'digit', '', '', '', 5),
(@iCategoryId, CONCAT(@sName, '_popup_border_color'), '_bx_lucid_stg_cpt_option_popup_border_color', 'rgba(0, 0, 0, 0.2)', 'rgba', '', '', '', 6),
(@iCategoryId, CONCAT(@sName, '_popup_border_size'), '_bx_lucid_stg_cpt_option_popup_border_size', '1px', 'digit', '', '', '', 7),
(@iCategoryId, CONCAT(@sName, '_popup_border_radius'), '_bx_lucid_stg_cpt_option_popup_border_radius', '3px', 'digit', '', '', '', 8),
(@iCategoryId, CONCAT(@sName, '_popup_shadow'), '_bx_lucid_stg_cpt_option_popup_shadow', '0px 1px 5px 0px rgba(0, 0, 0, 0.05)', 'digit', '', '', '', 9),
(@iCategoryId, CONCAT(@sName, '_popup_title_bg_color'), '_bx_lucid_stg_cpt_option_popup_title_bg_color', 'rgba(230, 240, 250, 0.9)', 'rgba', '', '', '', 10),
(@iCategoryId, CONCAT(@sName, '_popup_title_padding'), '_bx_lucid_stg_cpt_option_popup_title_padding', '1.25rem', 'digit', '', '', '', 11),
(@iCategoryId, CONCAT(@sName, '_popup_title_font_family'), '_bx_lucid_stg_cpt_option_popup_title_font_family', 'Arial, sans-serif', 'digit', '', '', '', 12),
(@iCategoryId, CONCAT(@sName, '_popup_title_font_size'), '_bx_lucid_stg_cpt_option_popup_title_font_size', '1rem', 'digit', '', '', '', 13),
(@iCategoryId, CONCAT(@sName, '_popup_title_font_color'), '_bx_lucid_stg_cpt_option_popup_title_font_color', 'rgba(40, 50, 60, 0.9)', 'rgba', '', '', '', 14);


-- SETTINGS: Lucid template Styles Main Menu
INSERT INTO `sys_options_categories`(`type_id`, `name`, `caption`, `order`) VALUES 
(@iTypeId, CONCAT(@sName, '_styles_menu_main'), '_bx_lucid_stg_cpt_category_styles_menu_main', 90);
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options`(`category_id`, `name`, `caption`, `value`, `type`, `extra`, `check`, `check_error`, `order`) VALUES
(@iCategoryId, CONCAT(@sName, '_menu_main_bg_color'), '_bx_lucid_stg_cpt_option_menu_main_bg_color', 'rgba(248, 249, 252, 1.0)', 'rgba', '', '', '', 1),
(@iCategoryId, CONCAT(@sName, '_menu_main_bg_image'), '_bx_lucid_stg_cpt_option_menu_main_bg_image', '', 'image', '', '', '', 2),
(@iCategoryId, CONCAT(@sName, '_menu_main_bg_image_repeat'), '_bx_lucid_stg_cpt_option_menu_main_bg_image_repeat', 'no-repeat', 'select', 'no-repeat,repeat,repeat-x,repeat-y', '', '', 3),
(@iCategoryId, CONCAT(@sName, '_menu_main_bg_image_size'), '_bx_lucid_stg_cpt_option_menu_main_bg_image_size', 'cover', 'select', 'auto,cover,contain', '', '', 4),
(@iCategoryId, CONCAT(@sName, '_menu_main_content_padding'), '_bx_lucid_stg_cpt_option_menu_main_content_padding', '0.625rem', 'digit', '', '', '', 5),
(@iCategoryId, CONCAT(@sName, '_menu_main_border_color'), '_bx_lucid_stg_cpt_option_menu_main_border_color', 'rgba(0, 0, 0, 0.1)', 'rgba', '', '', '', 6),
(@iCategoryId, CONCAT(@sName, '_menu_main_border_size'), '_bx_lucid_stg_cpt_option_menu_main_border_size', '0px', 'digit', '', '', '', 7),
(@iCategoryId, CONCAT(@sName, '_menu_main_shadow'), '_bx_lucid_stg_cpt_option_menu_main_shadow', 'none', 'digit', '', '', '', 8),
(@iCategoryId, CONCAT(@sName, '_menu_main_align_items'), '_bx_lucid_stg_cpt_option_menu_main_align_items', 'left', 'select', 'left,center,right', '', '', 9),
(@iCategoryId, CONCAT(@sName, '_menu_main_font_family'), '_bx_lucid_stg_cpt_option_menu_main_font_family', 'Arial, sans-serif', 'digit', '', '', '', 10),
(@iCategoryId, CONCAT(@sName, '_menu_main_font_size'), '_bx_lucid_stg_cpt_option_menu_main_font_size', '1rem', 'digit', '', '', '', 11),
(@iCategoryId, CONCAT(@sName, '_menu_main_font_color'), '_bx_lucid_stg_cpt_option_menu_main_font_color', 'rgba(255, 255, 255, 0.8)', 'rgba', '', '', '', 12),
(@iCategoryId, CONCAT(@sName, '_menu_main_font_color_hover'), '_bx_lucid_stg_cpt_option_menu_main_font_color_hover', 'rgba(255, 255, 255, 1.0)', 'rgba', '', '', '', 13),
(@iCategoryId, CONCAT(@sName, '_menu_main_font_color_active'), '_bx_lucid_stg_cpt_option_menu_main_font_color_active', 'rgba(255, 255, 255, 1.0)', 'rgba', '', '', '', 14),
(@iCategoryId, CONCAT(@sName, '_menu_main_font_shadow'), '_bx_lucid_stg_cpt_option_menu_main_font_shadow', 'none', 'digit', '', '', '', 15),
(@iCategoryId, CONCAT(@sName, '_menu_main_font_weight'), '_bx_lucid_stg_cpt_option_menu_main_font_weight', '400', 'digit', '', '', '', 16);


-- SETTINGS: Lucid template Styles Account Menu
INSERT INTO `sys_options_categories`(`type_id`, `name`, `caption`, `order`) VALUES 
(@iTypeId, CONCAT(@sName, '_styles_menu_account'), '_bx_lucid_stg_cpt_category_styles_menu_account', 95);
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options`(`category_id`, `name`, `caption`, `value`, `type`, `extra`, `check`, `check_error`, `order`) VALUES
(@iCategoryId, CONCAT(@sName, '_menu_account_bg_color'), '_bx_lucid_stg_cpt_option_menu_account_bg_color', 'rgba(248, 249, 252, 1.0)', 'rgba', '', '', '', 1),
(@iCategoryId, CONCAT(@sName, '_menu_account_bg_image'), '_bx_lucid_stg_cpt_option_menu_account_bg_image', '', 'image', '', '', '', 2),
(@iCategoryId, CONCAT(@sName, '_menu_account_bg_image_repeat'), '_bx_lucid_stg_cpt_option_menu_account_bg_image_repeat', 'no-repeat', 'select', 'no-repeat,repeat,repeat-x,repeat-y', '', '', 3),
(@iCategoryId, CONCAT(@sName, '_menu_account_bg_image_size'), '_bx_lucid_stg_cpt_option_menu_account_bg_image_size', 'cover', 'select', 'auto,cover,contain', '', '', 4),
(@iCategoryId, CONCAT(@sName, '_menu_account_content_padding'), '_bx_lucid_stg_cpt_option_menu_account_content_padding', '1.25rem', 'digit', '', '', '', 5),
(@iCategoryId, CONCAT(@sName, '_menu_account_border_color'), '_bx_lucid_stg_cpt_option_menu_account_border_color', 'rgba(0, 0, 0, 0.1)', 'rgba', '', '', '', 6),
(@iCategoryId, CONCAT(@sName, '_menu_account_border_size'), '_bx_lucid_stg_cpt_option_menu_account_border_size', '0px', 'digit', '', '', '', 7),
(@iCategoryId, CONCAT(@sName, '_menu_account_shadow'), '_bx_lucid_stg_cpt_option_menu_account_shadow', 'none', 'digit', '', '', '', 8),
(@iCategoryId, CONCAT(@sName, '_menu_account_font_family'), '_bx_lucid_stg_cpt_option_menu_account_font_family', 'Arial, sans-serif', 'digit', '', '', '', 9),
(@iCategoryId, CONCAT(@sName, '_menu_account_font_size'), '_bx_lucid_stg_cpt_option_menu_account_font_size', '1rem', 'digit', '', '', '', 10),
(@iCategoryId, CONCAT(@sName, '_menu_account_font_color'), '_bx_lucid_stg_cpt_option_menu_account_font_color', 'rgba(76, 103, 159, 0.8)', 'rgba', '', '', '', 11),
(@iCategoryId, CONCAT(@sName, '_menu_account_font_color_hover'), '_bx_lucid_stg_cpt_option_menu_account_font_color_hover', 'rgba(76, 103, 159, 1.0)', 'rgba', '', '', '', 12),
(@iCategoryId, CONCAT(@sName, '_menu_account_font_color_active'), '_bx_lucid_stg_cpt_option_menu_account_font_color_active', 'rgba(76, 103, 159, 1.0)', 'rgba', '', '', '', 13),
(@iCategoryId, CONCAT(@sName, '_menu_account_font_shadow'), '_bx_lucid_stg_cpt_option_menu_account_font_shadow', 'none', 'digit', '', '', '', 14),
(@iCategoryId, CONCAT(@sName, '_menu_account_font_weight'), '_bx_lucid_stg_cpt_option_menu_account_font_weight', '400', 'digit', '', '', '', 15);


-- SETTINGS: Lucid template Styles Add Menu
INSERT INTO `sys_options_categories`(`type_id`, `name`, `caption`, `order`) VALUES 
(@iTypeId, CONCAT(@sName, '_styles_menu_add'), '_bx_lucid_stg_cpt_category_styles_menu_add', 97);
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options`(`category_id`, `name`, `caption`, `value`, `type`, `extra`, `check`, `check_error`, `order`) VALUES
(@iCategoryId, CONCAT(@sName, '_menu_add_bg_color'), '_bx_lucid_stg_cpt_option_menu_add_bg_color', 'rgba(248, 249, 252, 1.0)', 'rgba', '', '', '', 1),
(@iCategoryId, CONCAT(@sName, '_menu_add_bg_image'), '_bx_lucid_stg_cpt_option_menu_add_bg_image', '', 'image', '', '', '', 2),
(@iCategoryId, CONCAT(@sName, '_menu_add_bg_image_repeat'), '_bx_lucid_stg_cpt_option_menu_add_bg_image_repeat', 'no-repeat', 'select', 'no-repeat,repeat,repeat-x,repeat-y', '', '', 3),
(@iCategoryId, CONCAT(@sName, '_menu_add_bg_image_size'), '_bx_lucid_stg_cpt_option_menu_add_bg_image_size', 'cover', 'select', 'auto,cover,contain', '', '', 4),
(@iCategoryId, CONCAT(@sName, '_menu_add_content_padding'), '_bx_lucid_stg_cpt_option_menu_add_content_padding', '1.25rem', 'digit', '', '', '', 5),
(@iCategoryId, CONCAT(@sName, '_menu_add_border_color'), '_bx_lucid_stg_cpt_option_menu_add_border_color', 'rgba(0, 0, 0, 0.1)', 'rgba', '', '', '', 6),
(@iCategoryId, CONCAT(@sName, '_menu_add_border_size'), '_bx_lucid_stg_cpt_option_menu_add_border_size', '0px', 'digit', '', '', '', 7),
(@iCategoryId, CONCAT(@sName, '_menu_add_shadow'), '_bx_lucid_stg_cpt_option_menu_add_shadow', 'none', 'digit', '', '', '', 8),
(@iCategoryId, CONCAT(@sName, '_menu_add_font_family'), '_bx_lucid_stg_cpt_option_menu_add_font_family', 'Arial, sans-serif', 'digit', '', '', '', 9),
(@iCategoryId, CONCAT(@sName, '_menu_add_font_size'), '_bx_lucid_stg_cpt_option_menu_add_font_size', '1rem', 'digit', '', '', '', 10),
(@iCategoryId, CONCAT(@sName, '_menu_add_font_color'), '_bx_lucid_stg_cpt_option_menu_add_font_color', 'rgba(76, 103, 159, 0.8)', 'rgba', '', '', '', 11),
(@iCategoryId, CONCAT(@sName, '_menu_add_font_color_hover'), '_bx_lucid_stg_cpt_option_menu_add_font_color_hover', 'rgba(76, 103, 159, 1.0)', 'rgba', '', '', '', 12),
(@iCategoryId, CONCAT(@sName, '_menu_add_font_color_active'), '_bx_lucid_stg_cpt_option_menu_add_font_color_active', 'rgba(76, 103, 159, 1.0)', 'rgba', '', '', '', 13),
(@iCategoryId, CONCAT(@sName, '_menu_add_font_shadow'), '_bx_lucid_stg_cpt_option_menu_add_font_shadow', 'none', 'digit', '', '', '', 14),
(@iCategoryId, CONCAT(@sName, '_menu_add_font_weight'), '_bx_lucid_stg_cpt_option_menu_add_font_weight', '400', 'digit', '', '', '', 15);


-- SETTINGS: Lucid template Styles Page Menu
INSERT INTO `sys_options_categories`(`type_id`, `name`, `caption`, `order`) VALUES 
(@iTypeId, CONCAT(@sName, '_styles_menu_page'), '_bx_lucid_stg_cpt_category_styles_menu_page', 100);
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options`(`category_id`, `name`, `caption`, `value`, `type`, `extra`, `check`, `check_error`, `order`) VALUES
(@iCategoryId, CONCAT(@sName, '_menu_page_bg_color'), '_bx_lucid_stg_cpt_option_menu_page_bg_color', 'rgba(255, 255, 255, 0.9)', 'rgba', '', '', '', 1),
(@iCategoryId, CONCAT(@sName, '_menu_page_bg_image'), '_bx_lucid_stg_cpt_option_menu_page_bg_image', '', 'image', '', '', '', 2),
(@iCategoryId, CONCAT(@sName, '_menu_page_bg_image_repeat'), '_bx_lucid_stg_cpt_option_menu_page_bg_image_repeat', 'no-repeat', 'select', 'no-repeat,repeat,repeat-x,repeat-y', '', '', 3),
(@iCategoryId, CONCAT(@sName, '_menu_page_bg_image_size'), '_bx_lucid_stg_cpt_option_menu_page_bg_image_size', 'cover', 'select', 'auto,cover,contain', '', '', 4),
(@iCategoryId, CONCAT(@sName, '_menu_page_content_padding'), '_bx_lucid_stg_cpt_option_menu_page_content_padding', '0.3rem', 'digit', '', '', '', 5),
(@iCategoryId, CONCAT(@sName, '_menu_page_border_color'), '_bx_lucid_stg_cpt_option_menu_page_border_color', 'rgba(0, 0, 0, 0.1)', 'rgba', '', '', '', 6),
(@iCategoryId, CONCAT(@sName, '_menu_page_border_size'), '_bx_lucid_stg_cpt_option_menu_page_border_size', '1px', 'digit', '', '', '', 7),
(@iCategoryId, CONCAT(@sName, '_menu_page_shadow'), '_bx_lucid_stg_cpt_option_menu_page_shadow', 'none', 'digit', '', '', '', 8),
(@iCategoryId, CONCAT(@sName, '_menu_page_font_family'), '_bx_lucid_stg_cpt_option_menu_page_font_family', 'Arial, sans-serif', 'digit', '', '', '', 9),
(@iCategoryId, CONCAT(@sName, '_menu_page_font_size'), '_bx_lucid_stg_cpt_option_menu_page_font_size', '1rem', 'digit', '', '', '', 10),
(@iCategoryId, CONCAT(@sName, '_menu_page_font_color'), '_bx_lucid_stg_cpt_option_menu_page_font_color', 'rgba(40, 50, 60, 0.8)', 'rgba', '', '', '', 11),
(@iCategoryId, CONCAT(@sName, '_menu_page_font_color_hover'), '_bx_lucid_stg_cpt_option_menu_page_font_color_hover', 'rgba(30, 150, 250, 1)', 'rgba', '', '', '', 12),
(@iCategoryId, CONCAT(@sName, '_menu_page_font_color_active'), '_bx_lucid_stg_cpt_option_menu_page_font_color_active', 'rgba(30, 150, 250, 1)', 'rgba', '', '', '', 13),
(@iCategoryId, CONCAT(@sName, '_menu_page_font_shadow'), '_bx_lucid_stg_cpt_option_menu_page_font_shadow', 'none', 'digit', '', '', '', 14),
(@iCategoryId, CONCAT(@sName, '_menu_page_font_weight'), '_bx_lucid_stg_cpt_option_menu_page_font_weight', '400', 'digit', '', '', '', 15);


-- SETTINGS: Lucid template Styles Slide Menus
INSERT INTO `sys_options_categories`(`type_id`, `name`, `caption`, `order`) VALUES 
(@iTypeId, CONCAT(@sName, '_styles_menu_slide'), '_bx_lucid_stg_cpt_category_styles_menu_slide', 110);
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options`(`category_id`, `name`, `caption`, `value`, `type`, `extra`, `check`, `check_error`, `order`) VALUES
(@iCategoryId, CONCAT(@sName, '_menu_slide_bg_color'), '_bx_lucid_stg_cpt_option_menu_slide_bg_color', 'rgba(255, 255, 255, 0.9)', 'rgba', '', '', '', 1),
(@iCategoryId, CONCAT(@sName, '_menu_slide_bg_image'), '_bx_lucid_stg_cpt_option_menu_slide_bg_image', '', 'image', '', '', '', 2),
(@iCategoryId, CONCAT(@sName, '_menu_slide_bg_image_repeat'), '_bx_lucid_stg_cpt_option_menu_slide_bg_image_repeat', 'no-repeat', 'select', 'no-repeat,repeat,repeat-x,repeat-y', '', '', 3),
(@iCategoryId, CONCAT(@sName, '_menu_slide_bg_image_size'), '_bx_lucid_stg_cpt_option_menu_slide_bg_image_size', 'cover', 'select', 'auto,cover,contain', '', '', 4),
(@iCategoryId, CONCAT(@sName, '_menu_slide_content_padding'), '_bx_lucid_stg_cpt_option_menu_slide_content_padding', '1.25rem', 'digit', '', '', '', 5),
(@iCategoryId, CONCAT(@sName, '_menu_slide_border_color'), '_bx_lucid_stg_cpt_option_menu_slide_border_color', 'rgba(0, 0, 0, 0.1)', 'rgba', '', '', '', 6),
(@iCategoryId, CONCAT(@sName, '_menu_slide_border_size'), '_bx_lucid_stg_cpt_option_menu_slide_border_size', '1px 0px 1px 0px', 'digit', '', '', '', 7),
(@iCategoryId, CONCAT(@sName, '_menu_slide_shadow'), '_bx_lucid_stg_cpt_option_menu_slide_shadow', 'none', 'digit', '', '', '', 8),
(@iCategoryId, CONCAT(@sName, '_menu_slide_font_family'), '_bx_lucid_stg_cpt_option_menu_slide_font_family', 'Arial, sans-serif', 'digit', '', '', '', 9),
(@iCategoryId, CONCAT(@sName, '_menu_slide_font_size'), '_bx_lucid_stg_cpt_option_menu_slide_font_size', '1.0rem', 'digit', '', '', '', 10),
(@iCategoryId, CONCAT(@sName, '_menu_slide_font_color'), '_bx_lucid_stg_cpt_option_menu_slide_font_color', 'rgba(40, 50, 60, 0.8)', 'rgba', '', '', '', 11),
(@iCategoryId, CONCAT(@sName, '_menu_slide_font_color_hover'), '_bx_lucid_stg_cpt_option_menu_slide_font_color_hover', 'rgba(40, 50, 60, 0.9)', 'rgba', '', '', '', 12),
(@iCategoryId, CONCAT(@sName, '_menu_slide_font_color_active'), '_bx_lucid_stg_cpt_option_menu_slide_font_color_active', 'rgba(20, 30, 40, 1)', 'rgba', '', '', '', 13),
(@iCategoryId, CONCAT(@sName, '_menu_slide_font_shadow'), '_bx_lucid_stg_cpt_option_menu_slide_font_shadow', 'none', 'digit', '', '', '', 14),
(@iCategoryId, CONCAT(@sName, '_menu_slide_font_weight'), '_bx_lucid_stg_cpt_option_menu_slide_font_weight', '400', 'digit', '', '', '', 15);


-- SETTINGS: Lucid template Styles Forms
INSERT INTO `sys_options_categories`(`type_id`, `name`, `caption`, `order`) VALUES 
(@iTypeId, CONCAT(@sName, '_styles_form'), '_bx_lucid_stg_cpt_category_styles_form', 120);
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options`(`category_id`, `name`, `caption`, `value`, `type`, `extra`, `check`, `check_error`, `order`) VALUES
(@iCategoryId, CONCAT(@sName, '_form_input_height'), '_bx_lucid_stg_cpt_option_form_input_height', '2rem', 'digit', '', '', '', 1),
(@iCategoryId, CONCAT(@sName, '_form_input_bg_color'), '_bx_lucid_stg_cpt_option_form_input_bg_color', 'rgba(255, 255, 255, 0.8)', 'rgba', '', '', '', 2),
(@iCategoryId, CONCAT(@sName, '_form_input_bg_color_active'), '_bx_lucid_stg_cpt_option_form_input_bg_color_active', 'rgba(255, 255, 255, 1)', 'rgba', '', '', '', 3),
(@iCategoryId, CONCAT(@sName, '_form_input_border_color'), '_bx_lucid_stg_cpt_option_form_input_border_color', 'rgba(40, 60, 80, 0.5)', 'rgba', '', '', '', 4),
(@iCategoryId, CONCAT(@sName, '_form_input_border_color_active'), '_bx_lucid_stg_cpt_option_form_input_border_color_active', 'rgba(30, 150, 250, 1)', 'rgba', '', '', '', 5),
(@iCategoryId, CONCAT(@sName, '_form_input_border_size'), '_bx_lucid_stg_cpt_option_form_input_border_size', '1px', 'digit', '', '', '', 6),
(@iCategoryId, CONCAT(@sName, '_form_input_border_radius'), '_bx_lucid_stg_cpt_option_form_input_border_radius', '3px', 'digit', '', '', '', 7),
(@iCategoryId, CONCAT(@sName, '_form_input_shadow'), '_bx_lucid_stg_cpt_option_form_input_shadow', 'inset 0px 0px 2px 1px rgba(0,0,0,0.15)', 'digit', '', '', '', 8),
(@iCategoryId, CONCAT(@sName, '_form_input_font_family'), '_bx_lucid_stg_cpt_option_form_input_font_family', 'Arial, sans-serif', 'digit', '', '', '', 9),
(@iCategoryId, CONCAT(@sName, '_form_input_font_size'), '_bx_lucid_stg_cpt_option_form_input_font_size', '1rem', 'digit', '', '', '', 10),
(@iCategoryId, CONCAT(@sName, '_form_input_font_color'), '_bx_lucid_stg_cpt_option_form_input_font_color', 'rgba(20, 30, 40, 1)', 'rgba', '', '', '', 11);


-- SETTINGS: Lucid template Styles Large Buttons
INSERT INTO `sys_options_categories`(`type_id`, `name`, `caption`, `order`) VALUES 
(@iTypeId, CONCAT(@sName, '_styles_large_button'), '_bx_lucid_stg_cpt_category_styles_large_button', 130);
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options`(`category_id`, `name`, `caption`, `value`, `type`, `extra`, `check`, `check_error`, `order`) VALUES
(@iCategoryId, CONCAT(@sName, '_button_lg_height'), '_bx_lucid_stg_cpt_option_button_height', '2rem', 'digit', '', '', '', 1),
(@iCategoryId, CONCAT(@sName, '_button_lg_bg_color'), '_bx_lucid_stg_cpt_option_button_bg_color', 'rgba(76, 103, 159, 1.0)', 'rgba', '', '', '', 2),
(@iCategoryId, CONCAT(@sName, '_button_lg_bg_color_hover'), '_bx_lucid_stg_cpt_option_button_bg_color_hover', 'rgba(76, 103, 159, 0.8)', 'rgba', '', '', '', 3),
(@iCategoryId, CONCAT(@sName, '_button_lg_border_color'), '_bx_lucid_stg_cpt_option_button_border_color', 'rgba(76, 103, 159, 1.0)', 'rgba', '', '', '', 4),
(@iCategoryId, CONCAT(@sName, '_button_lg_border_color_hover'), '_bx_lucid_stg_cpt_option_button_border_color_hover', 'rgba(76, 103, 159, 0.8)', 'rgba', '', '', '', 5),
(@iCategoryId, CONCAT(@sName, '_button_lg_border_size'), '_bx_lucid_stg_cpt_option_button_border_size', '0px', 'digit', '', '', '', 6),
(@iCategoryId, CONCAT(@sName, '_button_lg_border_radius'), '_bx_lucid_stg_cpt_option_button_border_radius', '3px', 'digit', '', '', '', 7),
(@iCategoryId, CONCAT(@sName, '_button_lg_shadow'), '_bx_lucid_stg_cpt_option_button_shadow', '0px 0px 0px 1px rgba(0,0,0,0.0)', 'digit', '', '', '', 8),
(@iCategoryId, CONCAT(@sName, '_button_lg_font_family'), '_bx_lucid_stg_cpt_option_button_font_family', 'Arial, sans-serif', 'digit', '', '', '', 9),
(@iCategoryId, CONCAT(@sName, '_button_lg_font_size'), '_bx_lucid_stg_cpt_option_button_font_size', '1rem', 'digit', '', '', '', 10),
(@iCategoryId, CONCAT(@sName, '_button_lg_font_color'), '_bx_lucid_stg_cpt_option_button_font_color', 'rgba(255, 255, 255, 1.0)', 'rgba', '', '', '', 11),
(@iCategoryId, CONCAT(@sName, '_button_lg_font_color_hover'), '_bx_lucid_stg_cpt_option_button_font_color_hover', 'rgba(255, 255, 255, 0.9)', 'rgba', '', '', '', 12),
(@iCategoryId, CONCAT(@sName, '_button_lg_font_shadow'), '_bx_lucid_stg_cpt_option_button_font_shadow', 'none', 'digit', '', '', '', 13),
(@iCategoryId, CONCAT(@sName, '_button_lg_font_weight'), '_bx_lucid_stg_cpt_option_button_font_weight', '700', 'digit', '', '', '', 14);


-- SETTINGS: Lucid template Styles Large Primary Buttons
INSERT INTO `sys_options_categories`(`type_id`, `name`, `caption`, `order`) VALUES 
(@iTypeId, CONCAT(@sName, '_styles_large_button_primary'), '_bx_lucid_stg_cpt_category_styles_large_button_primary', 131);
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options`(`category_id`, `name`, `caption`, `value`, `type`, `extra`, `check`, `check_error`, `order`) VALUES
(@iCategoryId, CONCAT(@sName, '_button_lgp_height'), '_bx_lucid_stg_cpt_option_button_height', '2rem', 'digit', '', '', '', 1),
(@iCategoryId, CONCAT(@sName, '_button_lgp_bg_color'), '_bx_lucid_stg_cpt_option_button_bg_color', 'rgba(76, 103, 159, 1.0)', 'rgba', '', '', '', 2),
(@iCategoryId, CONCAT(@sName, '_button_lgp_bg_color_hover'), '_bx_lucid_stg_cpt_option_button_bg_color_hover', 'rgba(76, 103, 159, 0.8)', 'rgba', '', '', '', 3),
(@iCategoryId, CONCAT(@sName, '_button_lgp_border_color'), '_bx_lucid_stg_cpt_option_button_border_color', 'rgba(76, 103, 159, 1.0)', 'rgba', '', '', '', 4),
(@iCategoryId, CONCAT(@sName, '_button_lgp_border_color_hover'), '_bx_lucid_stg_cpt_option_button_border_color_hover', 'rgba(76, 103, 159, 0.8)', 'rgba', '', '', '', 5),
(@iCategoryId, CONCAT(@sName, '_button_lgp_border_size'), '_bx_lucid_stg_cpt_option_button_border_size', '0px', 'digit', '', '', '', 6),
(@iCategoryId, CONCAT(@sName, '_button_lgp_border_radius'), '_bx_lucid_stg_cpt_option_button_border_radius', '3px', 'digit', '', '', '', 7),
(@iCategoryId, CONCAT(@sName, '_button_lgp_shadow'), '_bx_lucid_stg_cpt_option_button_shadow', '0px 0px 0px 1px rgba(0,0,0,0.0)', 'digit', '', '', '', 8),
(@iCategoryId, CONCAT(@sName, '_button_lgp_font_family'), '_bx_lucid_stg_cpt_option_button_font_family', 'Arial, sans-serif', 'digit', '', '', '', 9),
(@iCategoryId, CONCAT(@sName, '_button_lgp_font_size'), '_bx_lucid_stg_cpt_option_button_font_size', '1rem', 'digit', '', '', '', 10),
(@iCategoryId, CONCAT(@sName, '_button_lgp_font_color'), '_bx_lucid_stg_cpt_option_button_font_color', 'rgba(255, 255, 255, 1.0)', 'rgba', '', '', '', 11),
(@iCategoryId, CONCAT(@sName, '_button_lgp_font_color_hover'), '_bx_lucid_stg_cpt_option_button_font_color_hover', 'rgba(255, 255, 255, 0.9)', 'rgba', '', '', '', 12),
(@iCategoryId, CONCAT(@sName, '_button_lgp_font_shadow'), '_bx_lucid_stg_cpt_option_button_font_shadow', 'none', 'digit', '', '', '', 13),
(@iCategoryId, CONCAT(@sName, '_button_lgp_font_weight'), '_bx_lucid_stg_cpt_option_button_font_weight', '700', 'digit', '', '', '', 14);

-- SETTINGS: Lucid template Styles Normal Buttons
INSERT INTO `sys_options_categories`(`type_id`, `name`, `caption`, `order`) VALUES 
(@iTypeId, CONCAT(@sName, '_styles_button'), '_bx_lucid_stg_cpt_category_styles_button', 135);
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options`(`category_id`, `name`, `caption`, `value`, `type`, `extra`, `check`, `check_error`, `order`) VALUES
(@iCategoryId, CONCAT(@sName, '_button_nl_height'), '_bx_lucid_stg_cpt_option_button_height', '2rem', 'digit', '', '', '', 1),
(@iCategoryId, CONCAT(@sName, '_button_nl_bg_color'), '_bx_lucid_stg_cpt_option_button_bg_color', 'rgba(76, 103, 159, 1.0)', 'rgba', '', '', '', 2),
(@iCategoryId, CONCAT(@sName, '_button_nl_bg_color_hover'), '_bx_lucid_stg_cpt_option_button_bg_color_hover', 'rgba(76, 103, 159, 0.8)', 'rgba', '', '', '', 3),
(@iCategoryId, CONCAT(@sName, '_button_nl_border_color'), '_bx_lucid_stg_cpt_option_button_border_color', 'rgba(76, 103, 159, 1.0)', 'rgba', '', '', '', 4),
(@iCategoryId, CONCAT(@sName, '_button_nl_border_color_hover'), '_bx_lucid_stg_cpt_option_button_border_color_hover', 'rgba(76, 103, 159, 0.8)', 'rgba', '', '', '', 5),
(@iCategoryId, CONCAT(@sName, '_button_nl_border_size'), '_bx_lucid_stg_cpt_option_button_border_size', '0px', 'digit', '', '', '', 6),
(@iCategoryId, CONCAT(@sName, '_button_nl_border_radius'), '_bx_lucid_stg_cpt_option_button_border_radius', '3px', 'digit', '', '', '', 7),
(@iCategoryId, CONCAT(@sName, '_button_nl_shadow'), '_bx_lucid_stg_cpt_option_button_shadow', '0px 0px 0px 1px rgba(0,0,0,0.0)', 'digit', '', '', '', 8),
(@iCategoryId, CONCAT(@sName, '_button_nl_font_family'), '_bx_lucid_stg_cpt_option_button_font_family', 'Arial, sans-serif', 'digit', '', '', '', 9),
(@iCategoryId, CONCAT(@sName, '_button_nl_font_size'), '_bx_lucid_stg_cpt_option_button_font_size', '1rem', 'digit', '', '', '', 10),
(@iCategoryId, CONCAT(@sName, '_button_nl_font_color'), '_bx_lucid_stg_cpt_option_button_font_color', 'rgba(255, 255, 255, 1.0)', 'rgba', '', '', '', 11),
(@iCategoryId, CONCAT(@sName, '_button_nl_font_color_hover'), '_bx_lucid_stg_cpt_option_button_font_color_hover', 'rgba(255, 255, 255, 0.9)', 'rgba', '', '', '', 12),
(@iCategoryId, CONCAT(@sName, '_button_nl_font_shadow'), '_bx_lucid_stg_cpt_option_button_font_shadow', 'none', 'digit', '', '', '', 13),
(@iCategoryId, CONCAT(@sName, '_button_nl_font_weight'), '_bx_lucid_stg_cpt_option_button_font_weight', '700', 'digit', '', '', '', 14);


-- SETTINGS: Lucid template Styles Large Primary Buttons
INSERT INTO `sys_options_categories`(`type_id`, `name`, `caption`, `order`) VALUES 
(@iTypeId, CONCAT(@sName, '_styles_button_primary'), '_bx_lucid_stg_cpt_category_styles_button_primary', 136);
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options`(`category_id`, `name`, `caption`, `value`, `type`, `extra`, `check`, `check_error`, `order`) VALUES
(@iCategoryId, CONCAT(@sName, '_button_nlp_height'), '_bx_lucid_stg_cpt_option_button_height', '2rem', 'digit', '', '', '', 1),
(@iCategoryId, CONCAT(@sName, '_button_nlp_bg_color'), '_bx_lucid_stg_cpt_option_button_bg_color', 'rgba(76, 103, 159, 1.0)', 'rgba', '', '', '', 2),
(@iCategoryId, CONCAT(@sName, '_button_nlp_bg_color_hover'), '_bx_lucid_stg_cpt_option_button_bg_color_hover', 'rgba(76, 103, 159, 0.8)', 'rgba', '', '', '', 3),
(@iCategoryId, CONCAT(@sName, '_button_nlp_border_color'), '_bx_lucid_stg_cpt_option_button_border_color', 'rgba(76, 103, 159, 1.0)', 'rgba', '', '', '', 4),
(@iCategoryId, CONCAT(@sName, '_button_nlp_border_color_hover'), '_bx_lucid_stg_cpt_option_button_border_color_hover', 'rgba(76, 103, 159, 0.8)', 'rgba', '', '', '', 5),
(@iCategoryId, CONCAT(@sName, '_button_nlp_border_size'), '_bx_lucid_stg_cpt_option_button_border_size', '0px', 'digit', '', '', '', 6),
(@iCategoryId, CONCAT(@sName, '_button_nlp_border_radius'), '_bx_lucid_stg_cpt_option_button_border_radius', '3px', 'digit', '', '', '', 7),
(@iCategoryId, CONCAT(@sName, '_button_nlp_shadow'), '_bx_lucid_stg_cpt_option_button_shadow', '0px 0px 0px 1px rgba(0,0,0,0.0)', 'digit', '', '', '', 8),
(@iCategoryId, CONCAT(@sName, '_button_nlp_font_family'), '_bx_lucid_stg_cpt_option_button_font_family', 'Arial, sans-serif', 'digit', '', '', '', 9),
(@iCategoryId, CONCAT(@sName, '_button_nlp_font_size'), '_bx_lucid_stg_cpt_option_button_font_size', '1rem', 'digit', '', '', '', 10),
(@iCategoryId, CONCAT(@sName, '_button_nlp_font_color'), '_bx_lucid_stg_cpt_option_button_font_color', 'rgba(255, 255, 255, 1.0)', 'rgba', '', '', '', 11),
(@iCategoryId, CONCAT(@sName, '_button_nlp_font_color_hover'), '_bx_lucid_stg_cpt_option_button_font_color_hover', 'rgba(255, 255, 255, 0.9)', 'rgba', '', '', '', 12),
(@iCategoryId, CONCAT(@sName, '_button_nlp_font_shadow'), '_bx_lucid_stg_cpt_option_button_font_shadow', 'none', 'digit', '', '', '', 13),
(@iCategoryId, CONCAT(@sName, '_button_nlp_font_weight'), '_bx_lucid_stg_cpt_option_button_font_weight', '700', 'digit', '', '', '', 14);

-- SETTINGS: Lucid template Styles Small Buttons
INSERT INTO `sys_options_categories`(`type_id`, `name`, `caption`, `order`) VALUES 
(@iTypeId, CONCAT(@sName, '_styles_small_button'), '_bx_lucid_stg_cpt_category_styles_small_button', 140);
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options`(`category_id`, `name`, `caption`, `value`, `type`, `extra`, `check`, `check_error`, `order`) VALUES
(@iCategoryId, CONCAT(@sName, '_button_sm_height'), '_bx_lucid_stg_cpt_option_button_height', '1.5rem', 'digit', '', '', '', 1),
(@iCategoryId, CONCAT(@sName, '_button_sm_bg_color'), '_bx_lucid_stg_cpt_option_button_bg_color', 'rgba(76, 103, 159, 1.0)', 'rgba', '', '', '', 2),
(@iCategoryId, CONCAT(@sName, '_button_sm_bg_color_hover'), '_bx_lucid_stg_cpt_option_button_bg_color_hover', 'rgba(76, 103, 159, 0.8)', 'rgba', '', '', '', 3),
(@iCategoryId, CONCAT(@sName, '_button_sm_border_color'), '_bx_lucid_stg_cpt_option_button_border_color', 'rgba(76, 103, 159, 1.0)', 'rgba', '', '', '', 4),
(@iCategoryId, CONCAT(@sName, '_button_sm_border_color_hover'), '_bx_lucid_stg_cpt_option_button_border_color_hover', 'rgba(76, 103, 159, 0.8)', 'rgba', '', '', '', 5),
(@iCategoryId, CONCAT(@sName, '_button_sm_border_size'), '_bx_lucid_stg_cpt_option_button_border_size', '0px', 'digit', '', '', '', 6),
(@iCategoryId, CONCAT(@sName, '_button_sm_border_radius'), '_bx_lucid_stg_cpt_option_button_border_radius', '2px', 'digit', '', '', '', 7),
(@iCategoryId, CONCAT(@sName, '_button_sm_shadow'), '_bx_lucid_stg_cpt_option_button_shadow', '0px 0px 0px 1px rgba(0,0,0,0)', 'digit', '', '', '', 8),
(@iCategoryId, CONCAT(@sName, '_button_sm_font_family'), '_bx_lucid_stg_cpt_option_button_font_family', 'Arial, sans-serif', 'digit', '', '', '', 9),
(@iCategoryId, CONCAT(@sName, '_button_sm_font_size'), '_bx_lucid_stg_cpt_option_button_font_size', '0.75rem', 'digit', '', '', '', 10),
(@iCategoryId, CONCAT(@sName, '_button_sm_font_color'), '_bx_lucid_stg_cpt_option_button_font_color', 'rgba(255, 255, 255, 1.0)', 'rgba', '', '', '', 11),
(@iCategoryId, CONCAT(@sName, '_button_sm_font_color_hover'), '_bx_lucid_stg_cpt_option_button_font_color_hover', 'rgba(255, 255, 255, 0.9)', 'rgba', '', '', '', 12),
(@iCategoryId, CONCAT(@sName, '_button_sm_font_shadow'), '_bx_lucid_stg_cpt_option_button_font_shadow', 'none', 'digit', '', '', '', 13),
(@iCategoryId, CONCAT(@sName, '_button_sm_font_weight'), '_bx_lucid_stg_cpt_option_button_font_weight', '400', 'digit', '', '', '', 14);

-- SETTINGS: Lucid template Styles Small Primary Buttons
INSERT INTO `sys_options_categories`(`type_id`, `name`, `caption`, `order`) VALUES 
(@iTypeId, CONCAT(@sName, '_styles_small_button_primary'), '_bx_lucid_stg_cpt_category_styles_small_button_primary', 141);
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options`(`category_id`, `name`, `caption`, `value`, `type`, `extra`, `check`, `check_error`, `order`) VALUES
(@iCategoryId, CONCAT(@sName, '_button_smp_height'), '_bx_lucid_stg_cpt_option_button_height', '1.5rem', 'digit', '', '', '', 1),
(@iCategoryId, CONCAT(@sName, '_button_smp_bg_color'), '_bx_lucid_stg_cpt_option_button_bg_color', 'rgba(76, 103, 159, 1.0)', 'rgba', '', '', '', 2),
(@iCategoryId, CONCAT(@sName, '_button_smp_bg_color_hover'), '_bx_lucid_stg_cpt_option_button_bg_color_hover', 'rgba(76, 103, 159, 0.8)', 'rgba', '', '', '', 3),
(@iCategoryId, CONCAT(@sName, '_button_smp_border_color'), '_bx_lucid_stg_cpt_option_button_border_color', 'rgba(76, 103, 159, 1.0)', 'rgba', '', '', '', 4),
(@iCategoryId, CONCAT(@sName, '_button_smp_border_color_hover'), '_bx_lucid_stg_cpt_option_button_border_color_hover', 'rgba(76, 103, 159, 0.8)', 'rgba', '', '', '', 5),
(@iCategoryId, CONCAT(@sName, '_button_smp_border_size'), '_bx_lucid_stg_cpt_option_button_border_size', '0px', 'digit', '', '', '', 6),
(@iCategoryId, CONCAT(@sName, '_button_smp_border_radius'), '_bx_lucid_stg_cpt_option_button_border_radius', '2px', 'digit', '', '', '', 7),
(@iCategoryId, CONCAT(@sName, '_button_smp_shadow'), '_bx_lucid_stg_cpt_option_button_shadow', '0px 0px 0px 1px rgba(0,0,0,0)', 'digit', '', '', '', 8),
(@iCategoryId, CONCAT(@sName, '_button_smp_font_family'), '_bx_lucid_stg_cpt_option_button_font_family', 'Arial, sans-serif', 'digit', '', '', '', 9),
(@iCategoryId, CONCAT(@sName, '_button_smp_font_size'), '_bx_lucid_stg_cpt_option_button_font_size', '0.75rem', 'digit', '', '', '', 10),
(@iCategoryId, CONCAT(@sName, '_button_smp_font_color'), '_bx_lucid_stg_cpt_option_button_font_color', 'rgba(255, 255, 255, 1.0)', 'rgba', '', '', '', 11),
(@iCategoryId, CONCAT(@sName, '_button_smp_font_color_hover'), '_bx_lucid_stg_cpt_option_button_font_color_hover', 'rgba(255, 255, 255, 0.9)', 'rgba', '', '', '', 12),
(@iCategoryId, CONCAT(@sName, '_button_smp_font_shadow'), '_bx_lucid_stg_cpt_option_button_font_shadow', 'none', 'digit', '', '', '', 13),
(@iCategoryId, CONCAT(@sName, '_button_smp_font_weight'), '_bx_lucid_stg_cpt_option_button_font_weight', '400', 'digit', '', '', '', 14);

-- SETTINGS: Lucid template Styles Font
INSERT INTO `sys_options_categories`(`type_id`, `name`, `caption`, `order`) VALUES 
(@iTypeId, CONCAT(@sName, '_styles_font'), '_bx_lucid_stg_cpt_category_styles_font', 150);
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options`(`category_id`, `name`, `caption`, `value`, `type`, `extra`, `check`, `check_error`, `order`) VALUES
(@iCategoryId, CONCAT(@sName, '_font_family'), '_bx_lucid_stg_cpt_option_font_family', 'Arial, sans-serif', 'digit', '', '', '', 1),
(@iCategoryId, CONCAT(@sName, '_font_size_default'), '_bx_lucid_stg_cpt_option_font_size_default', '18px', 'digit', '', '', '', 2),
(@iCategoryId, CONCAT(@sName, '_font_color_default'), '_bx_lucid_stg_cpt_option_font_color_default', 'rgba(40, 50, 60, 0.9)', 'rgba', '', '', '', 3),
(@iCategoryId, CONCAT(@sName, '_font_color_grayed'), '_bx_lucid_stg_cpt_option_font_color_grayed', 'rgba(40, 50, 60, 0.5)', 'rgba', '', '', '', 4),
(@iCategoryId, CONCAT(@sName, '_font_color_contrasted'), '_bx_lucid_stg_cpt_option_font_color_contrasted', 'rgba(255, 255, 255, 1)', 'rgba', '', '', '', 5),
(@iCategoryId, CONCAT(@sName, '_font_size_small'), '_bx_lucid_stg_cpt_option_font_size_small', '12px', 'digit', '', '', '', 6),
(@iCategoryId, CONCAT(@sName, '_font_size_middle'), '_bx_lucid_stg_cpt_option_font_size_middle', '15px', 'digit', '', '', '', 7),
(@iCategoryId, CONCAT(@sName, '_font_size_large'), '_bx_lucid_stg_cpt_option_font_size_large', '18px', 'digit', '', '', '', 8),
(@iCategoryId, CONCAT(@sName, '_font_size_h1'), '_bx_lucid_stg_cpt_option_font_size_h1', '32px', 'digit', '', '', '', 9),
(@iCategoryId, CONCAT(@sName, '_font_weight_h1'), '_bx_lucid_stg_cpt_option_font_weight_h1', '700', 'digit', '', '', '', 10),
(@iCategoryId, CONCAT(@sName, '_font_color_default_h1'), '_bx_lucid_stg_cpt_option_font_color_default_h1', 'rgba(40, 50, 60, 1)', 'rgba', '', '', '', 11),
(@iCategoryId, CONCAT(@sName, '_font_color_grayed_h1'), '_bx_lucid_stg_cpt_option_font_color_grayed_h1', 'rgba(40, 50, 60, 0.5)', 'rgba', '', '', '', 12),
(@iCategoryId, CONCAT(@sName, '_font_color_contrasted_h1'), '_bx_lucid_stg_cpt_option_font_color_contrasted_h1', 'rgba(255, 255, 255, 0.9)', 'rgba', '', '', '', 13),
(@iCategoryId, CONCAT(@sName, '_font_color_link_h1'), '_bx_lucid_stg_cpt_option_font_color_link_h1', 'rgba(30, 150, 250, 0.9)', 'rgba', '', '', '', 14),
(@iCategoryId, CONCAT(@sName, '_font_color_link_h1_hover'), '_bx_lucid_stg_cpt_option_font_color_link_h1_hover', 'rgba(30, 150, 250, 1)', 'rgba', '', '', '', 15),
(@iCategoryId, CONCAT(@sName, '_font_size_h2'), '_bx_lucid_stg_cpt_option_font_size_h2', '24px', 'digit', '', '', '', 16),
(@iCategoryId, CONCAT(@sName, '_font_weight_h2'), '_bx_lucid_stg_cpt_option_font_weight_h2', '700', 'digit', '', '', '', 17),
(@iCategoryId, CONCAT(@sName, '_font_color_default_h2'), '_bx_lucid_stg_cpt_option_font_color_default_h2', 'rgba(40, 60, 80, 0.9)', 'rgba', '', '', '', 18),
(@iCategoryId, CONCAT(@sName, '_font_color_grayed_h2'), '_bx_lucid_stg_cpt_option_font_color_grayed_h2', 'rgba(40, 60, 80, 0.5)', 'rgba', '', '', '', 19),
(@iCategoryId, CONCAT(@sName, '_font_color_contrasted_h2'), '_bx_lucid_stg_cpt_option_font_color_contrasted_h2', 'rgba(255, 255, 255, 1)', 'rgba', '', '', '', 20),
(@iCategoryId, CONCAT(@sName, '_font_color_link_h2'), '_bx_lucid_stg_cpt_option_font_color_link_h2', 'rgba(30, 150, 250, 0.9)', 'rgba', '', '', '', 21),
(@iCategoryId, CONCAT(@sName, '_font_color_link_h2_hover'), '_bx_lucid_stg_cpt_option_font_color_link_h2_hover', 'rgba(30, 150, 250, 1)', 'rgba', '', '', '', 22),
(@iCategoryId, CONCAT(@sName, '_font_size_h3'), '_bx_lucid_stg_cpt_option_font_size_h3', '20px', 'digit', '', '', '', 23),
(@iCategoryId, CONCAT(@sName, '_font_weight_h3'), '_bx_lucid_stg_cpt_option_font_weight_h3', '500', 'digit', '', '', '', 24),
(@iCategoryId, CONCAT(@sName, '_font_color_default_h3'), '_bx_lucid_stg_cpt_option_font_color_default_h3', 'rgba(40, 60, 80, 0.9)', 'rgba', '', '', '', 25),
(@iCategoryId, CONCAT(@sName, '_font_color_grayed_h3'), '_bx_lucid_stg_cpt_option_font_color_grayed_h3', 'rgba(40, 60, 80, 0.5)', 'rgba', '', '', '', 26),
(@iCategoryId, CONCAT(@sName, '_font_color_contrasted_h3'), '_bx_lucid_stg_cpt_option_font_color_contrasted_h3', 'rgba(255, 255, 255, 1)', 'rgba', '', '', '', 27),
(@iCategoryId, CONCAT(@sName, '_font_color_link_h3'), '_bx_lucid_stg_cpt_option_font_color_link_h3', 'rgba(30, 150, 250, 0.9)', 'rgba', '', '', '', 28),
(@iCategoryId, CONCAT(@sName, '_font_color_link_h3_hover'), '_bx_lucid_stg_cpt_option_font_color_link_h3_hover', 'rgba(30, 150, 250, 1)', 'rgba', '', '', '', 29);

-- SETTINGS: Lucid template Custom Styles
INSERT INTO `sys_options_categories`(`type_id`, `name`, `caption`, `order`) VALUES 
(@iTypeId, CONCAT(@sName, '_styles_custom'), '_bx_lucid_stg_cpt_category_styles_custom', 160);
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options`(`category_id`, `name`, `caption`, `value`, `type`, `extra`, `check`, `check_error`, `order`) VALUES
(@iCategoryId, CONCAT(@sName, '_styles_custom'), '_bx_lucid_stg_cpt_option_styles_custom', 'div.bx-market-unit-cover div.bx-base-text-unit-no-thumb {\r\n border-width: 0px;\r\n}', 'text', '', '', '', 1);

-- SETTINGS: Lucid template Viewport Tablet
INSERT INTO `sys_options_categories`(`type_id`, `name`, `caption`, `order`) VALUES 
(@iTypeId, CONCAT(@sName, '_viewport_tablet'), '_bx_lucid_stg_cpt_category_viewport_tablet', 170);
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options`(`category_id`, `name`, `caption`, `value`, `type`, `extra`, `check`, `check_error`, `order`) VALUES
(@iCategoryId, CONCAT(@sName, '_vpt_font_size_scale'), '_bx_lucid_stg_cpt_option_vpt_font_size_scale', '100%', 'digit', '', '', '', 1);

-- SETTINGS: Lucid template Viewport Mobile
INSERT INTO `sys_options_categories`(`type_id`, `name`, `caption`, `order`) VALUES 
(@iTypeId, CONCAT(@sName, '_viewport_mobile'), '_bx_lucid_stg_cpt_category_viewport_mobile', 180);
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options`(`category_id`, `name`, `caption`, `value`, `type`, `extra`, `check`, `check_error`, `order`) VALUES
(@iCategoryId, CONCAT(@sName, '_vpm_font_size_scale'), '_bx_lucid_stg_cpt_option_vpm_font_size_scale', '100%', 'digit', '', '', '', 1);


-- MIXES
INSERT INTO `sys_options_mixes` (`type`, `category`, `name`, `title`, `active`, `editable`) VALUES
(@sName, '', 'Light-Mix', 'Light Mix', 1, 0);
SET @iMixId = LAST_INSERT_ID();

INSERT INTO `sys_options_mixes2options` (`option`, `mix_id`, `value`) VALUES
('bx_lucid_button_nlp_bg_color_hover', @iMixId, 'rgba(50, 100, 180, 1)'),
('bx_lucid_button_nlp_bg_color', @iMixId, 'rgba(50, 150, 250, 1)'),
('bx_lucid_button_nlp_height', @iMixId, '2rem'),
('bx_lucid_button_nl_font_weight', @iMixId, '600'),
('bx_lucid_button_nl_font_color', @iMixId, 'rgba(20, 80, 100, 1)'),
('bx_lucid_button_nl_font_family', @iMixId, 'Nunito, Lato, Arial, sans-serif'),
('bx_lucid_button_nl_font_size', @iMixId, '1rem'),
('bx_lucid_button_nl_font_shadow', @iMixId, 'none'),
('bx_lucid_button_nl_font_color_hover', @iMixId, 'rgba(20, 80, 100, 1)'),
('bx_lucid_button_nl_shadow', @iMixId, '0px 1px 3px 0px rgba(0, 0, 0, 0.05)'),
('bx_lucid_button_nl_border_size', @iMixId, '1px'),
('bx_lucid_button_nl_border_radius', @iMixId, '20px'),
('bx_lucid_button_nl_border_color', @iMixId, 'rgba(0, 0, 0, 0.05)'),
('bx_lucid_button_nl_border_color_hover', @iMixId, 'rgba(0, 0, 0, 0.1)'),
('bx_lucid_button_lgp_font_shadow', @iMixId, 'none'),
('bx_lucid_button_nl_bg_color', @iMixId, 'rgba(255, 255, 255, 1)'),
('bx_lucid_button_nl_height', @iMixId, '2rem'),
('bx_lucid_button_nl_bg_color_hover', @iMixId, 'rgba(230, 240, 250, 1)'),
('bx_lucid_button_lgp_font_color_hover', @iMixId, 'rgba(255, 255, 255, 1)'),
('bx_lucid_button_lgp_shadow', @iMixId, '0px 1px 3px 0px rgba(0, 0, 0, 0.05)'),
('bx_lucid_button_lgp_font_size', @iMixId, '1.5rem'),
('bx_lucid_button_lgp_font_color', @iMixId, 'rgba(255, 255, 255, 1)'),
('bx_lucid_button_lgp_font_weight', @iMixId, '600'),
('bx_lucid_button_lgp_font_family', @iMixId, 'Nunito, Lato, Arial, sans-serif'),
('bx_lucid_button_lgp_border_color_hover', @iMixId, 'rgba(0, 0, 0, 0.1)'),
('bx_lucid_button_lgp_height', @iMixId, '3rem'),
('bx_lucid_button_lgp_border_size', @iMixId, '1px'),
('bx_lucid_button_lgp_border_radius', @iMixId, '30px'),
('bx_lucid_button_lgp_bg_color', @iMixId, 'rgba(50, 150, 250, 1)'),
('bx_lucid_button_lgp_bg_color_hover', @iMixId, 'rgba(50, 100, 180, 1)'),
('bx_lucid_button_lgp_border_color', @iMixId, 'rgba(0, 0, 0, 0.05)'),
('bx_lucid_card_bg_image_attachment', @iMixId, 'scroll'),
('bx_lucid_card_bg_color_hover', @iMixId, 'rgba(255, 255, 255, 0.5)'),
('bx_lucid_font_color_contrasted_h3', @iMixId, 'rgba(255, 255, 255, 1)'),
('bx_lucid_font_color_grayed_h3', @iMixId, 'rgba(40, 60, 80, 0.5)'),
('bx_lucid_font_color_link_h3', @iMixId, 'rgba(20, 80, 100, 1)'),
('bx_lucid_font_size_h3', @iMixId, '20px'),
('bx_lucid_font_color_default_h3', @iMixId, 'rgba(20, 80, 100, 1)'),
('bx_lucid_font_color_link_h2_hover', @iMixId, 'rgba(50, 100, 180, 1)'),
('bx_lucid_font_weight_h3', @iMixId, '600'),
('bx_lucid_font_color_contrasted_h2', @iMixId, 'rgba(255, 255, 255, 1)'),
('bx_lucid_font_color_grayed_h2', @iMixId, 'rgba(40, 60, 80, 0.5)'),
('bx_lucid_font_color_link_h2', @iMixId, 'rgba(20, 80, 100, 1)'),
('bx_lucid_font_color_default_h2', @iMixId, 'rgba(20, 80, 100, 1)'),
('bx_lucid_font_color_contrasted_h1', @iMixId, 'rgba(255, 255, 255, 1)'),
('bx_lucid_font_weight_h2', @iMixId, '700'),
('bx_lucid_font_color_link_h1', @iMixId, 'rgba(20, 80, 100, 1)'),
('bx_lucid_font_size_h2', @iMixId, '24px'),
('bx_lucid_font_color_default_h1', @iMixId, 'rgba(20, 80, 100, 1)'),
('bx_lucid_font_color_grayed_h1', @iMixId, 'rgba(40, 50, 60, 0.5)'),
('bx_lucid_font_weight_h1', @iMixId, '800'),
('bx_lucid_font_size_small', @iMixId, '12px'),
('bx_lucid_font_size_h1', @iMixId, '32px'),
('bx_lucid_button_sm_font_color', @iMixId, 'rgba(20, 80, 100, 1)'),
('bx_lucid_font_size_middle', @iMixId, '14px'),
('bx_lucid_font_size_large', @iMixId, '18px'),
('bx_lucid_font_color_contrasted', @iMixId, 'rgba(255, 255, 255, 1)'),
('bx_lucid_font_color_default', @iMixId, 'rgba(20, 80, 100, 1)'),
('bx_lucid_font_color_grayed', @iMixId, 'rgba(20, 80, 100, 0.5)'),
('bx_lucid_button_sm_font_shadow', @iMixId, 'none'),
('bx_lucid_font_size_default', @iMixId, '16px'),
('bx_lucid_font_family', @iMixId, 'Nunito, Lato, Arial, sans-serif'),
('bx_lucid_button_sm_font_weight', @iMixId, '600'),
('bx_lucid_button_sm_font_size', @iMixId, '0.75rem'),
('bx_lucid_button_sm_border_radius', @iMixId, '15px'),
('bx_lucid_button_sm_font_family', @iMixId, 'Nunito, Lato, Arial, sans-serif'),
('bx_lucid_button_sm_font_color_hover', @iMixId, 'rgba(20, 80, 100, 1)'),
('bx_lucid_button_sm_border_color', @iMixId, 'rgba(0, 0, 0, 0.05)'),
('bx_lucid_button_sm_shadow', @iMixId, '0px 1px 3px 0px rgba(0, 0, 0, 0.05)'),
('bx_lucid_button_sm_border_color_hover', @iMixId, 'rgba(0, 0, 0, 0.1)'),
('bx_lucid_button_sm_border_size', @iMixId, '1px'),
('bx_lucid_button_sm_bg_color', @iMixId, 'rgba(255, 255, 255, 1)'),
('bx_lucid_button_sm_height', @iMixId, '1.5rem'),
('bx_lucid_button_lg_font_weight', @iMixId, '600'),
('bx_lucid_button_lg_font_family', @iMixId, 'Nunito, Lato, Arial, sans-serif'),
('bx_lucid_button_lg_font_size', @iMixId, '1.5rem'),
('bx_lucid_button_lg_font_color_hover', @iMixId, 'rgba(20, 80, 100, 1)'),
('bx_lucid_button_sm_bg_color_hover', @iMixId, 'rgba(230, 240, 250, 1)'),
('bx_lucid_button_lg_font_color', @iMixId, 'rgba(20, 80, 100, 1)'),
('bx_lucid_button_lg_border_size', @iMixId, '1px'),
('bx_lucid_button_lg_bg_color_hover', @iMixId, 'rgba(230, 240, 250, 1)'),
('bx_lucid_button_lg_border_radius', @iMixId, '30px'),
('bx_lucid_button_lg_shadow', @iMixId, '0px 1px 3px 0px rgba(0, 0, 0, 0.05)'),
('bx_lucid_button_lg_border_color_hover', @iMixId, 'rgba(0, 0, 0, 0.1)'),
('bx_lucid_button_lg_border_color', @iMixId, 'rgba(0, 0, 0, 0.05)'),
('bx_lucid_form_input_font_color', @iMixId, 'rgba(20, 80, 100, 1)'),
('bx_lucid_button_lg_height', @iMixId, '3rem'),
('bx_lucid_button_lg_bg_color', @iMixId, 'rgba(255, 255, 255, 1)'),
('bx_lucid_form_input_font_family', @iMixId, 'Nunito, Lato, Arial, sans-serif'),
('bx_lucid_form_input_border_color_active', @iMixId, 'rgba(50, 150, 250, 1)'),
('bx_lucid_form_input_shadow', @iMixId, '0px 1px 3px 0px rgba(0, 0, 0, 0)'),
('bx_lucid_form_input_font_size', @iMixId, '1rem'),
('bx_lucid_form_input_bg_color_active', @iMixId, 'rgba(255, 255, 255, 1)'),
('bx_lucid_form_input_border_size', @iMixId, '1px'),
('bx_lucid_menu_slide_font_color_active', @iMixId, 'rgba(20, 80, 100, 1)'),
('bx_lucid_menu_slide_font_color_hover', @iMixId, 'rgba(50, 100, 180, 1)'),
('bx_lucid_form_input_border_color', @iMixId, 'rgba(50, 100, 180, 0.2)'),
('bx_lucid_form_input_bg_color', @iMixId, 'rgba(255, 255, 255, 0.9)'),
('bx_lucid_form_input_height', @iMixId, '2.5rem'),
('bx_lucid_menu_slide_font_shadow', @iMixId, 'none'),
('bx_lucid_menu_slide_font_color', @iMixId, 'rgba(50, 150, 250, 1)'),
('bx_lucid_menu_slide_font_family', @iMixId, 'Nunito, Lato, Arial, sans-serif'),
('bx_lucid_menu_slide_font_weight', @iMixId, '600'),
('bx_lucid_menu_slide_font_size', @iMixId, '1rem'),
('bx_lucid_menu_slide_border_color', @iMixId, 'rgba(20, 80, 100, 0.2)'),
('bx_lucid_menu_slide_border_size', @iMixId, '1px'),
('bx_lucid_menu_slide_shadow', @iMixId, '0px 4px 12px 0px rgba(0, 0, 0, 0.1)'),
('bx_lucid_menu_slide_bg_color', @iMixId, 'rgba(255, 255, 255, 0.9)'),
('bx_lucid_menu_slide_content_padding', @iMixId, '1rem'),
('bx_lucid_menu_slide_bg_image_repeat', @iMixId, 'no-repeat'),
('bx_lucid_menu_slide_bg_image_size', @iMixId, 'cover'),
('bx_lucid_menu_page_font_color_hover', @iMixId, 'rgba(50, 100, 180, 1)'),
('bx_lucid_menu_page_font_weight', @iMixId, '600'),
('bx_lucid_menu_page_font_color', @iMixId, 'rgba(50, 150, 250, 1)'),
('bx_lucid_menu_page_font_shadow', @iMixId, 'none'),
('bx_lucid_menu_page_font_color_active', @iMixId, 'rgba(20, 80, 100, 1)'),
('bx_lucid_menu_page_font_size', @iMixId, '1rem'),
('bx_lucid_menu_page_shadow', @iMixId, 'none'),
('bx_lucid_menu_page_border_size', @iMixId, '0px'),
('bx_lucid_menu_page_font_family', @iMixId, 'Nunito, Lato, Arial, sans-serif'),
('bx_lucid_menu_page_border_color', @iMixId, 'rgba(0, 0, 0, 0.1)'),
('bx_lucid_menu_page_content_padding', @iMixId, '0px'),
('bx_lucid_menu_page_bg_image_size', @iMixId, 'cover'),
('bx_lucid_menu_page_bg_color', @iMixId, 'rgba(0, 0, 0, 0)'),
('bx_lucid_menu_page_bg_image_repeat', @iMixId, 'no-repeat'),
('bx_lucid_menu_add_font_color_active', @iMixId, 'rgba(20, 80, 100, 1)'),
('bx_lucid_menu_add_font_shadow', @iMixId, 'none'),
('bx_lucid_menu_add_font_weight', @iMixId, '600'),
('bx_lucid_menu_add_font_color_hover', @iMixId, 'rgba(50, 100, 180, 1)'),
('bx_lucid_menu_add_font_size', @iMixId, '1rem'),
('bx_lucid_menu_add_font_color', @iMixId, 'rgba(50, 150, 250, 1)'),
('bx_lucid_menu_add_font_family', @iMixId, 'Nunito, Lato, Arial, sans-serif'),
('bx_lucid_menu_add_shadow', @iMixId, '0px 4px 12px 0px rgba(0, 0, 0, 0.2)'),
('bx_lucid_menu_add_bg_image_repeat', @iMixId, 'no-repeat'),
('bx_lucid_menu_add_bg_image_size', @iMixId, 'cover'),
('bx_lucid_menu_add_border_size', @iMixId, '1px'),
('bx_lucid_menu_add_border_color', @iMixId, 'rgba(20, 80, 100, 0.05)'),
('bx_lucid_menu_add_content_padding', @iMixId, '1rem'),
('bx_lucid_menu_account_font_color_active', @iMixId, 'rgba(20, 80, 100, 1)'),
('bx_lucid_menu_add_bg_color', @iMixId, 'rgba(255, 255, 255, 1)'),
('bx_lucid_menu_account_font_family', @iMixId, 'Nunito, Lato, Arial, sans-serif'),
('bx_lucid_menu_account_font_weight', @iMixId, '600'),
('bx_lucid_menu_account_font_color_hover', @iMixId, 'rgba(50, 100, 180, 1)'),
('bx_lucid_menu_account_border_color', @iMixId, 'rgba(20, 80, 100, 0.2)'),
('bx_lucid_menu_account_font_shadow', @iMixId, 'none'),
('bx_lucid_menu_account_border_size', @iMixId, '1px'),
('bx_lucid_menu_account_font_color', @iMixId, 'rgba(50, 150, 250, 1)'),
('bx_lucid_menu_account_content_padding', @iMixId, '1rem'),
('bx_lucid_menu_account_font_size', @iMixId, '1rem'),
('bx_lucid_menu_account_shadow', @iMixId, '0px 4px 12px 0px rgba(0, 0, 0, 0.2)'),
('bx_lucid_menu_account_bg_image_repeat', @iMixId, 'no-repeat'),
('bx_lucid_menu_account_bg_image_size', @iMixId, 'cover'),
('bx_lucid_menu_main_font_weight', @iMixId, '600'),
('bx_lucid_menu_account_bg_color', @iMixId, 'rgba(255, 255, 255, 1)'),
('bx_lucid_menu_main_font_shadow', @iMixId, 'none'),
('bx_lucid_menu_main_font_color_active', @iMixId, 'rgba(255, 255, 255, 1)'),
('bx_lucid_menu_main_font_color_hover', @iMixId, 'rgba(255, 255, 255, 1)'),
('bx_lucid_menu_main_border_size', @iMixId, '0px'),
('bx_lucid_menu_main_font_color', @iMixId, 'rgba(255, 255, 255, 0.9)'),
('bx_lucid_menu_main_font_size', @iMixId, '1rem'),
('bx_lucid_menu_main_font_family', @iMixId, 'Nunito, Lato, Arial, sans-serif'),
('bx_lucid_menu_main_shadow', @iMixId, 'none'),
('bx_lucid_menu_main_border_color', @iMixId, 'rgba(0, 0, 0, 0)'),
('bx_lucid_menu_main_content_padding', @iMixId, '0.625rem'),
('bx_lucid_menu_main_bg_image_size', @iMixId, 'cover'),
('bx_lucid_menu_main_bg_image_repeat', @iMixId, 'no-repeat'),
('bx_lucid_menu_main_bg_color', @iMixId, 'rgba(0, 0, 0, 0)'),
('bx_lucid_popup_title_font_family', @iMixId, 'Nunito, Lato, Arial, sans-serif'),
('bx_lucid_popup_title_font_size', @iMixId, '1rem'),
('bx_lucid_popup_title_font_color', @iMixId, 'rgba(20, 80, 100, 1)'),
('bx_lucid_popup_shadow', @iMixId, '0px 4px 12px 0px rgba(0, 0, 0, 0.2)'),
('bx_lucid_popup_title_bg_color', @iMixId, 'rgba(230, 240, 250, 0.9)'),
('bx_lucid_popup_border_size', @iMixId, '1px'),
('bx_lucid_popup_border_color', @iMixId, 'rgba(20, 80, 100, 0.2)'),
('bx_lucid_popup_title_padding', @iMixId, '1rem'),
('bx_lucid_popup_content_padding', @iMixId, '1rem 1rem'),
('bx_lucid_popup_border_radius', @iMixId, '8px'),
('bx_lucid_popup_bg_image_size', @iMixId, 'cover'),
('bx_lucid_popup_bg_image_repeat', @iMixId, 'no-repeat'),
('bx_lucid_card_content_padding', @iMixId, '1rem'),
('bx_lucid_popup_bg_color', @iMixId, 'rgba(255, 255, 255, 1)'),
('bx_lucid_card_shadow', @iMixId, '0px 2px 6px 0px rgba(0, 0, 0, 0.05)'),
('bx_lucid_vpt_font_size_scale', @iMixId, '100%'),
('bx_lucid_card_border_radius', @iMixId, '8px'),
('bx_lucid_vpm_font_size_scale', @iMixId, '100%'),
('bx_lucid_card_border_size', @iMixId, '1px'),
('bx_lucid_card_border_color', @iMixId, 'rgba(20, 80, 100, 0.1)'),
('bx_lucid_styles_custom', @iMixId, 'div.bx-market-unit-cover div.bx-base-text-unit-no-thumb {\r\n border-width: 0px;\r\n}'),
('bx_lucid_font_color_link_h3_hover', @iMixId, 'rgba(50, 100, 180, 1)'),
('bx_lucid_block_title_div_bg_color', @iMixId, 'rgba(20, 80, 100, 0.1)'),
('bx_lucid_card_bg_color', @iMixId, 'rgba(255, 255, 255, 1)'),
('bx_lucid_card_bg_image_repeat', @iMixId, 'no-repeat'),
('bx_lucid_card_bg_image_size', @iMixId, 'cover'),
('bx_lucid_block_title_font_family', @iMixId, 'Nunito, Lato, Arial, sans-serif'),
('bx_lucid_block_title_div_height', @iMixId, '1px'),
('bx_lucid_block_title_border_radius', @iMixId, '0px'),
('bx_lucid_block_title_border_size', @iMixId, '0px'),
('bx_lucid_block_title_font_color', @iMixId, 'rgba(20, 80, 100, 1)'),
('bx_lucid_block_title_font_weight', @iMixId, '800'),
('bx_lucid_block_title_bg_color', @iMixId, 'rgba(255, 255, 255, 0)'),
('bx_lucid_block_shadow', @iMixId, '0px 2px 6px 0px rgba(0, 0, 0, 0.05)'),
('bx_lucid_block_border_size', @iMixId, '1px'),
('bx_lucid_block_title_padding', @iMixId, '0rem 1rem 0rem 1rem'),
('bx_lucid_block_border_radius', @iMixId, '8px'),
('bx_lucid_block_title_font_size', @iMixId, '1rem'),
('bx_lucid_block_title_border_color', @iMixId, 'rgba(0, 0, 0, 0)'),
('bx_lucid_block_border_color', @iMixId, 'rgba(20, 80, 100, 0.1)'),
('bx_lucid_cover_font_size', @iMixId, '2.0rem'),
('bx_lucid_block_content_padding', @iMixId, '1rem'),
('bx_lucid_cover_font_color', @iMixId, 'rgba(255, 255, 255, 1)'),
('bx_lucid_cover_font_family', @iMixId, 'Nunito, Lato, Arial, sans-serif'),
('bx_lucid_block_bg_image_size', @iMixId, 'cover'),
('bx_lucid_block_bg_color', @iMixId, 'rgba(255, 255, 255, 0.8)'),
('bx_lucid_cover_font_weight', @iMixId, '700'),
('bx_lucid_block_bg_image_repeat', @iMixId, 'no-repeat'),
('bx_lucid_cover_icon_shadow', @iMixId, 'none'),
('bx_lucid_cover_text_shadow', @iMixId, '0px 1px 3px rgba(0, 0, 0, .3)'),
('bx_lucid_cover_border_radius', @iMixId, '0px'),
('bx_lucid_cover_icon_border_color', @iMixId, 'rgba(208, 208, 208, 1)'),
('bx_lucid_cover_icon_border_size', @iMixId, '1px'),
('bx_lucid_cover_icon_border_radius', @iMixId, '3px'),
('bx_lucid_cover_text_align', @iMixId, 'center'),
('bx_lucid_cover_border_size', @iMixId, '0px'),
('bx_lucid_cover_border_color', @iMixId, 'rgba(208, 208, 208, 0)'),
('bx_lucid_cover_shadow', @iMixId, 'none'),
('bx_lucid_cover_content_padding', @iMixId, '2rem 3rem 2rem 3rem'),
('bx_lucid_cover_height', @iMixId, '8rem'),
('bx_lucid_body_icon_color_hover', @iMixId, 'rgba(20, 80, 160, 1)'),
('bx_lucid_body_link_color_hover', @iMixId, 'rgba(20, 80, 160, 1)'),
('bx_lucid_cover_bg_color', @iMixId, 'rgba(50, 150, 250, 1)'),
('bx_lucid_body_link_color', @iMixId, 'rgba(50, 150, 250, 1)'),
('bx_lucid_font_color_link_h1_hover', @iMixId, 'rgba(50, 100, 180, 1)'),
('bx_lucid_button_lg_font_shadow', @iMixId, 'none'),
('bx_lucid_page_width', @iMixId, '100%'),
('bx_lucid_body_icon_color', @iMixId, 'rgba(50, 150, 250, 1)'),
('bx_lucid_body_bg_image_size', @iMixId, 'cover'),
('bx_lucid_body_bg_color', @iMixId, 'rgb(220, 230, 240)'),
('bx_lucid_body_bg_image_repeat', @iMixId, 'no-repeat'),
('bx_lucid_footer_icon_color_hover', @iMixId, 'rgba(20, 80, 160, 1)'),
('bx_lucid_footer_link_color_hover', @iMixId, 'rgba(20, 80, 160, 1)'),
('bx_lucid_footer_link_color', @iMixId, 'rgba(50, 150, 250, 1)'),
('bx_lucid_footer_border_size', @iMixId, '1px'),
('bx_lucid_button_smp_font_family', @iMixId, 'Nunito, Lato, Arial, sans-serif'),
('bx_lucid_button_smp_font_size', @iMixId, '0.75rem'),
('bx_lucid_button_smp_font_color', @iMixId, 'rgba(255, 255, 255, 0.9)'),
('bx_lucid_button_smp_font_weight', @iMixId, '600'),
('bx_lucid_button_smp_font_shadow', @iMixId, 'none'),
('bx_lucid_button_smp_font_color_hover', @iMixId, 'rgba(255, 255, 255, 1)'),
('bx_lucid_button_smp_shadow', @iMixId, '0px 1px 3px 0px rgba(0, 0, 0, 0.05)'),
('bx_lucid_button_smp_border_radius', @iMixId, '15px'),
('bx_lucid_button_smp_border_color_hover', @iMixId, 'rgba(255, 255, 255, 0.5)'),
('bx_lucid_button_smp_border_size', @iMixId, '1px'),
('bx_lucid_button_smp_border_color', @iMixId, 'rgba(255, 255, 255, 0.2)'),
('bx_lucid_button_smp_bg_color_hover', @iMixId, 'rgba(50, 100, 180, 1)'),
('bx_lucid_button_smp_height', @iMixId, '1.5rem'),
('bx_lucid_button_smp_bg_color', @iMixId, 'rgba(50, 150, 250, 1)'),
('bx_lucid_button_nlp_font_weight', @iMixId, '600'),
('bx_lucid_button_nlp_font_shadow', @iMixId, 'none'),
('bx_lucid_button_nlp_font_size', @iMixId, '1rem'),
('bx_lucid_button_nlp_font_color_hover', @iMixId, 'rgba(255, 255, 255, 1)'),
('bx_lucid_button_nlp_font_color', @iMixId, 'rgba(255, 255, 255, 0.9)'),
('bx_lucid_button_nlp_shadow', @iMixId, '0px 1px 3px 0px rgba(0, 0, 0, 0.05)'),
('bx_lucid_button_nlp_font_family', @iMixId, 'Nunito, Lato, Arial, sans-serif'),
('bx_lucid_button_nlp_border_radius', @iMixId, '20px'),
('bx_lucid_button_nlp_border_size', @iMixId, '1px'),
('bx_lucid_button_nlp_border_color_hover', @iMixId, 'rgba(255, 255, 255, 0.5)'),
('bx_lucid_footer_content_padding', @iMixId, '1rem'),
('bx_lucid_footer_border_color', @iMixId, 'rgba(40, 60, 80, 0.1)'),
('bx_lucid_button_nlp_border_color', @iMixId, 'rgba(255, 255, 255, 0.2)'),
('bx_lucid_footer_font_color', @iMixId, 'rgba(20, 80, 100, 1)'),
('bx_lucid_footer_icon_color', @iMixId, 'rgba(50, 150, 250, 1)'),
('bx_lucid_footer_shadow', @iMixId, 'none'),
('bx_lucid_footer_bg_color', @iMixId, 'rgba(40, 60, 80, 0)'),
('bx_lucid_footer_bg_image_size', @iMixId, 'cover'),
('bx_lucid_footer_bg_image_repeat', @iMixId, 'no-repeat'),
('bx_lucid_header_shadow', @iMixId, '0px 2px 6px 0px rgba(0, 0, 0, 0.4)'),
('bx_lucid_header_icon_color', @iMixId, 'rgba(255, 255, 255, 1)'),
('bx_lucid_header_border_size', @iMixId, '1px'),
('bx_lucid_header_link_color', @iMixId, 'rgba(255, 255, 255, 1)'),
('bx_lucid_header_icon_color_hover', @iMixId, 'rgba(255, 255, 255, 0.8)'),
('bx_lucid_site_logo_alt', @iMixId, ''),
('bx_lucid_header_link_color_hover', @iMixId, 'rgba(255, 255, 255, 0.8)'),
('bx_lucid_site_logo_height', @iMixId, '30'),
('bx_lucid_site_logo_width', @iMixId, '0'),
('bx_lucid_header_border_color', @iMixId, 'rgba(25, 50, 90, 1)'),
('bx_lucid_header_bg_image_size', @iMixId, 'cover'),
('bx_lucid_header_bg_image_repeat', @iMixId, 'no-repeat'),
('bx_lucid_header_bg_color', @iMixId, 'rgba(50, 100, 180, 1)'),
('bx_lucid_general_item_bg_color_active', @iMixId, 'rgba(238, 241, 244, 1)'),
('bx_lucid_header_content_padding', @iMixId, ' 0 1rem'),
('bx_lucid_header_height', @iMixId, '4rem'),
('bx_lucid_form_input_border_radius', @iMixId, '8px'),
('bx_lucid_general_item_bg_color_hover', @iMixId, 'rgba(238, 241, 244, 1)'),
('bx_lucid_menu_main_align_items', @iMixId, 'left');


-- STUDIO PAGE & WIDGET
INSERT INTO `sys_std_pages`(`index`, `name`, `header`, `caption`, `icon`) VALUES
(3, @sName, '', '', 'bx_lucid@modules/boonex/lucid/|std-icon.svg');
SET @iPageId = LAST_INSERT_ID();

SET @iParentPageId = (SELECT `id` FROM `sys_std_pages` WHERE `name`='home');
SET @iParentPageOrder = (SELECT MAX(`order`) FROM `sys_std_pages_widgets` WHERE `page_id`=@iParentPageId);
INSERT INTO `sys_std_widgets`(`page_id`, `module`, `url`, `click`, `icon`, `caption`, `cnt_notices`, `cnt_actions`) VALUES
(@iPageId, @sName, CONCAT('{url_studio}design.php?name=', @sName), '', 'bx_lucid@modules/boonex/lucid/|std-icon.svg', '_bx_lucid_wgt_cpt', '', 'a:4:{s:6:"module";s:6:"system";s:6:"method";s:11:"get_actions";s:6:"params";a:0:{}s:5:"class";s:18:"TemplStudioDesigns";}');
INSERT INTO `sys_std_pages_widgets`(`page_id`, `widget_id`, `order`) VALUES
(@iParentPageId, LAST_INSERT_ID(), @iParentPageOrder + 1);