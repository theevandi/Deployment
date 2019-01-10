<?php
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    DolphinMigration  Dolphin Migration
 * @ingroup     UnaModules
 *
 * @{
 */

$aConfig = array(
    /**
     * Main Section.
     */
    'type' => BX_DOL_MODULE_TYPE_MODULE,
    'name' => 'bx_dolphin_migration',
    'title' => 'Dolphin Migration',
    'note' => 'Migration Tool',
    'version' => '9.0.5',
    'vendor' => 'Boonex',
	'help_url' => 'http://feed.una.io/?section={module_name}',

    'compatible_with' => array(
        '9.0.0-RC13'
    ),

    /**
     * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
     */
    'home_dir' => 'boonex/dolphin_migration/',
    'home_uri' => 'dolphin_migration',

    'db_prefix' => 'bx_dolphin_',
    'class_prefix' => 'BxDolM',

    /**
     * Category for language keys.
     */
    'language_category' => 'Boonex Dolphin Migration Tool',

    /**
     * Installation/Uninstallation Section.
     */
    'install' => array(
        'execute_sql' => 1,
        'update_languages' => 1,
    	'clear_db_cache' => 1
    ),
    'uninstall' => array (
        'execute_sql' => 1,
        'update_languages' => 1,
    	'clear_db_cache' => 1
    ),
    'enable' => array(
        'execute_sql' => 1,    	
        'clear_db_cache' => 1,
    ),
    'disable' => array(
        'execute_sql' => 1,    	
        'clear_db_cache' => 1,
    ),

    /**
     * Dependencies Section
     */
    'dependencies' => array(),
);

/** @} */
