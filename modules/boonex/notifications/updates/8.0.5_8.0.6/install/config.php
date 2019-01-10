<?php
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

$aConfig = array(
    /**
     * Main Section.
     */
    'title' => 'Notifications',
    'version_from' => '8.0.5',
	'version_to' => '8.0.6',
    'vendor' => 'BoonEx',

	'compatible_with' => array(
        '8.0.0-RC'
    ),

    /**
     * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
     */
    'home_dir' => 'boonex/notifications/updates/update_8.0.5_8.0.6/',
	'home_uri' => 'notifications_update_805_806',

	'module_dir' => 'boonex/notifications/',
	'module_uri' => 'notifications',

    'db_prefix' => 'bx_notifications_',
    'class_prefix' => 'BxNtfs',

    /**
     * Installation/Uninstallation Section.
     */
    'install' => array(
		'execute_sql' => 1,
        'update_files' => 1,
        'update_languages' => 1,
		'clear_db_cache' => 1,
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
