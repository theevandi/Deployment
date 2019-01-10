<?php
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 */

$aConfig = array(
    /**
     * Main Section.
     */
    'title' => 'Notifications',
    'version_from' => '9.0.12',
	'version_to' => '9.0.13',
    'vendor' => 'BoonEx',

	'compatible_with' => array(
        '9.0.0-RC13'
    ),

    /**
     * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
     */
    'home_dir' => 'boonex/notifications/updates/update_9.0.12_9.0.13/',
	'home_uri' => 'notifications_update_9012_9013',

	'module_dir' => 'boonex/notifications/',
	'module_uri' => 'notifications',

    'db_prefix' => 'bx_notifications_',
    'class_prefix' => 'BxNtfs',

    /**
     * Installation/Uninstallation Section.
     */
    'install' => array(
        'execute_sql' => 0,
        'update_files' => 1,
        'update_languages' => 1,
        'clear_db_cache' => 0,
    ),

	/**
     * Category for language keys.
     */
    'language_category' => 'Notifications',

	/**
     * Files Section
     */
    'delete_files' => array(),
);
