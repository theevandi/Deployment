<?php
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Albums Albums
 * @ingroup     UnaModules
 *
 * @{
 */

$aConfig = array(

    /**
     * Main Section.
     */
    'type' => BX_DOL_MODULE_TYPE_MODULE,
    'name' => 'bx_albums',
    'title' => 'Albums',
    'note' => 'Photos & videos',
    'version' => '9.0.12',
    'vendor' => 'BoonEx',
	'help_url' => 'http://feed.una.io/?section={module_name}',

    'compatible_with' => array(
        '9.0.0-RC12'
    ),

    /**
     * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
     */
    'home_dir' => 'boonex/albums/',
    'home_uri' => 'albums',

    'db_prefix' => 'bx_albums_',
    'class_prefix' => 'BxAlbums',

    /**
     * Category for language keys.
     */
    'language_category' => 'Albums',

    /**
     * List of page triggers.
     */
    'page_triggers' => array (
    	'trigger_page_profile_view_entry',
    ),

    /**
     * Menu triggers.
     */
    'menu_triggers' => array(
    	'trigger_profile_view_submenu',
        'trigger_group_view_submenu',
    ),

	/**
     * Storage objects to automatically delete files from upon module uninstallation.
     * Note. Don't add storage objects used in transcoder objects.
     */
    'storages' => array(
    	'bx_albums_files'
    ),

    /**
     * Transcoders.
     */
    'transcoders' => array(
		'bx_albums_preview',
        'bx_albums_browse',
        'bx_albums_big',
        'bx_albums_video_poster_browse',
        'bx_albums_video_poster_preview',
        'bx_albums_video_poster_big',
        'bx_albums_video_mp4',
        'bx_albums_video_webm',
    ),

    /**
     * Extended Search Forms.
     */
    'esearches' => array(
        'bx_albums',
    	'bx_albums_media',
    	'bx_albums_cmts',
        'bx_albums_media_cmts'
    ),

    /**
     * Installation/Uninstallation Section.
     */
    'install' => array(
        'execute_sql' => 1,
        'update_languages' => 1,
        'clear_db_cache' => 1,
    ),
    'uninstall' => array (
    	'process_esearches' => 1,
        'execute_sql' => 1,
        'update_languages' => 1,
    	'update_relations' => 1,
        'clear_db_cache' => 1,
    ),
    'enable' => array(
        'execute_sql' => 1,
    	'update_relations' => 1,
        'clear_db_cache' => 1,
    ),
    'enable_success' => array(
        'process_menu_triggers' => 1,
        'process_page_triggers' => 1,
    	'process_esearches' => 1,
    	'register_transcoders' => 1,
        'clear_db_cache' => 1,
    ),
    'disable' => array (
        'execute_sql' => 1,
    	'update_relations' => 1,
    	'unregister_transcoders' => 1,
        'clear_db_cache' => 1,
    ),
    'disable_failed' => array (
    	'register_transcoders' => 1,
    	'clear_db_cache' => 1,
    ),

    /**
     * Dependencies Section
     */
    'dependencies' => array(),

    /**
     * Connections Section
     */
    'relations' => array(
    	'bx_timeline',
    	'bx_notifications'
    ),

);

/** @} */
