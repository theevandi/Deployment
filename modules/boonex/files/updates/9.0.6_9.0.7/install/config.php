<?php
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 */

$aConfig = array(
    /**
     * Main Section.
     */
    'title' => 'Files',
    'version_from' => '9.0.6',
	'version_to' => '9.0.7',
    'vendor' => 'BoonEx',

    'compatible_with' => array(
        '9.0.0-RC10'
    ),

    /**
     * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
     */
    'home_dir' => 'boonex/files/updates/update_9.0.6_9.0.7/',
	'home_uri' => 'files_update_906_907',

	'module_dir' => 'boonex/files/',
	'module_uri' => 'files',

    'db_prefix' => 'bx_files_',
    'class_prefix' => 'BxFiles',

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
    'language_category' => 'Files',

	/**
     * Files Section
     */
    'delete_files' => array(),
);
