<?php
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 */

$aConfig = array(
    /**
     * Main Section.
     */
    'title' => 'SMTP Mailer',
    'version_from' => '9.0.1',
	'version_to' => '9.0.2',
    'vendor' => 'BoonEx',

	'compatible_with' => array(
        '9.0.0-RC1'
    ),

    /**
     * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
     */
    'home_dir' => 'boonex/smtpmailer/updates/update_9.0.1_9.0.2/',
	'home_uri' => 'smtpmailer_update_901_902',

	'module_dir' => 'boonex/smtpmailer/',
	'module_uri' => 'smtpmailer',

    'db_prefix' => 'bx_smtp_',
    'class_prefix' => 'BxSMTP',

    /**
     * Installation/Uninstallation Section.
     */
    'install' => array(
		'execute_sql' => 0,
        'update_files' => 1,
        'update_languages' => 0,
		'clear_db_cache' => 0,
    ),

	/**
     * Category for language keys.
     */
    'language_category' => 'SMTP Mailer',

	/**
     * Files Section
     */
    'delete_files' => array(),
);
