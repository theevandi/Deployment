<?php
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

$aConfig = array(
    /**
     * Main Section.
     */
    'title' => 'Lucid',
    'version_from' => '9.0.0',
	'version_to' => '9.0.1',
    'vendor' => 'BoonEx',

    'compatible_with' => array(
        '9.0.0-RC10'
    ),

    /**
     * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
     */
    'home_dir' => 'boonex/lucid/updates/update_9.0.0_9.0.1/',
	'home_uri' => 'lucid_update_900_901',

	'module_dir' => 'boonex/lucid/',
	'module_uri' => 'lucid',

    'db_prefix' => 'bx_lucid_',
    'class_prefix' => 'BxLucid',

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
    'language_category' => 'Boonex Lucid Template',

	/**
     * Files Section
     */
    'delete_files' => array(
        'data/template/bx_events/cover.html',
        'data/template/bx_groups/cover.html',
        'data/template/bx_persons/cover.html'
    ),
);
