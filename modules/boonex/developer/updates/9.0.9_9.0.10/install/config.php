<?php
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 */

$aConfig = array(
    /**
     * Main Section.
     */
    'title' => 'Developer',
    'version_from' => '9.0.9',
	'version_to' => '9.0.10',
    'vendor' => 'BoonEx',

	'compatible_with' => array(
        '9.0.0-RC13'
    ),

    /**
     * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
     */
    'home_dir' => 'boonex/developer/updates/update_9.0.9_9.0.10/',
	'home_uri' => 'developer_update_909_9010',

	'module_dir' => 'boonex/developer/',
	'module_uri' => 'developer',

    'db_prefix' => 'bx_dev_',
    'class_prefix' => 'BxDev',

    /**
     * Installation/Uninstallation Section.
     */
    'install' => array(
		'execute_sql' => 1,
        'update_files' => 1,
        'update_languages' => 0,
		'clear_db_cache' => 1,
    ),

	/**
     * Category for language keys.
     */
    'language_category' => 'BoonEx Developer',

	/**
     * Files Section
     */
    'delete_files' => array(),
);
