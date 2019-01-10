<?php
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

$aConfig = array(
    /**
     * Main Section.
     */
    'title' => 'Mass mailer',
    'version_from' => '9.0.4',
	'version_to' => '9.0.5',
    'vendor' => 'BoonEx',

    'compatible_with' => array(
        '9.0.0-RC13'
    ),

    /**
     * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
     */
    'home_dir' => 'boonex/massmailer/updates/update_9.0.4_9.0.5/',
	'home_uri' => 'massmailer_update_904_905',

	'module_dir' => 'boonex/massmailer/',
	'module_uri' => 'massmailer',

    'db_prefix' => 'bx_massmailer_',
    'class_prefix' => 'BxMassMailer',

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
    'language_category' => 'MassMailer',

	/**
     * Files Section
     */
    'delete_files' => array(),
);
