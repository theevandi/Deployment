<?php
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

$aConfig = array(
    /**
     * Main Section.
     */
    'title' => 'Organizations',
    'version_from' => '8.0.2',
	'version_to' => '8.0.3',
    'vendor' => 'BoonEx',

    'compatible_with' => array(
        '8.0.0.A9'
    ),

    /**
     * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
     */
    'home_dir' => 'boonex/organizations/updates/update_8.0.2_8.0.3/',
	'home_uri' => 'orgs_update_802_803',

	'module_dir' => 'boonex/organizations/',
	'module_uri' => 'orgs',

    'db_prefix' => 'bx_organizations_',
    'class_prefix' => 'BxOrgs',

	/**
     * Page triggers.
     */
	'page_triggers' => array (
    	'trigger_page_organizations_view_entry',  
    ),

    /**
     * Installation/Uninstallation Section.
     */
    'install' => array(
		'execute_sql' => 1,
        'update_files' => 1,
        'update_languages' => 1,
		'process_page_triggers' => 1,
		'clear_db_cache' => 1,
    ),

	/**
     * Category for language keys.
     */
    'language_category' => 'Organizations',
);
