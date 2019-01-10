UPDATE sys_modules SET help_url = 'http://feed.una.io/?section={module_name}' WHERE name = 'bx_facebook' LIMIT 1;


-- TABLE: bx_facebook_accounts

ALTER TABLE `bx_facebook_accounts` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;


REPAIR TABLE `bx_facebook_accounts`;
OPTIMIZE TABLE `bx_facebook_accounts`;
