<?php
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 */

$aConfig = array(
    /**
     * Main Section.
     */
    'title' => 'Invitations',
    'version_from' => '9.0.1',
	'version_to' => '9.0.2',
    'vendor' => 'BoonEx',

	'compatible_with' => array(
        '9.0.0-B3'
    ),

    /**
     * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
     */
    'home_dir' => 'boonex/invites/updates/update_9.0.1_9.0.2/',
	'home_uri' => 'invites_update_901_902',

	'module_dir' => 'boonex/invites/',
	'module_uri' => 'invites',

    'db_prefix' => 'bx_inv_',
    'class_prefix' => 'BxInv',

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
    'language_category' => 'Invitations',

	/**
     * Files Section
     */
    'delete_files' => array(
		'template/images/icons/std-mi.png',
		'template/images/icons/std-pi.png',
		'template/images/icons/std-si.png',
		'template/images/icons/std-wi.png'
	),
);
