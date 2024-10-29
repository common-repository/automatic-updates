<?php
/*
Plugin Name: Automatic Updates
Description: This plugin allows admin to update the code wordpress & plugins in background.
Version: 1.3.3
Author: Laxman Thapa
Author URI: http://lakshman.com.np
References: https://codex.wordpress.org/Function_Reference/get_plugins
            https://codex.wordpress.org/Configuring_Automatic_Background_Updates
*/

//enable core update
//if(!defined('WP_AUTO_UPDATE_CORE')) define( 'WP_AUTO_UPDATE_CORE', true );

require __DIR__.'/Laksh_Automatic_Updates_Plugin.php';
require __DIR__.'/Laksh_Automatic_Updates_Core.php';

add_filter( 'allow_major_auto_core_updates', '__return_true' );

add_action( 'wp_ajax_laksh_automatic_update_core', 'laksh_automatic_updates_core_handler' );
//add_action( 'wp_ajax_nopriv_laksh_automatic_update_core', 'laksh_automatic_updates_core_handler' );

add_action( 'wp_ajax_laksh_automatic_update_plugin', 'laksh_automatic_updates_plugin_handler' );
//add_action( 'wp_ajax_nopriv_laksh_automatic_update_plugin', 'laksh_automatic_updates_plugin_handler' );


add_action( 'wp_ajax_laksh_automatic_update_notification', 'laksh_automatic_updates_notification_handler' );
//add_action( 'wp_ajax_nopriv_laksh_automatic_update_notification', 'laksh_automatic_updates_notification_handler' );


add_action('admin_menu', function(){
    add_menu_page( 'Manage Updates',
        'Manage Updates',
        'manage_options',    //created custom capability for the artist role
        'manage-updates',
        'laksh_automatic_updates_manage_handler',
        'dashicons-admin-tools');
});


//set updates
function laksh_automatic_updates_init(){
    $coreUpdateType = (new Laksh_Automatic_Updates_Core())->getCoreUpdateOption();
    $pluginIgnoredList = (new Laksh_Automatic_Updates_Plugin())->getIgnoredPlugins();

    $isNotificationEnabled = (new Laksh_Automatic_Updates_Core())->isNotificationEnabled();

    if(!$isNotificationEnabled){
        //var_dump('disable email');
        add_filter( 'auto_core_update_send_email', '__return_false' );
    }
    //var_dump($coreUpdateType,$pluginIgnoredList,$isNotificationEnabled);

    switch ($coreUpdateType){
        case "true":
            //Development, minor, and major updates are all enabled
            add_filter( 'allow_dev_auto_core_updates', '__return_true' );           // Enable development updates
            add_filter( 'allow_minor_auto_core_updates', '__return_true' );         // Enable minor updates
            add_filter( 'allow_major_auto_core_updates', '__return_true' );         // Enable major updates
            break;
        case "minor":
            //Minor updates are enabled, development, and major updates are disabled
            add_filter( 'allow_dev_auto_core_updates', '__return_false' );           // Enable development updates
            add_filter( 'allow_minor_auto_core_updates', '__return_true' );         // Enable minor updates
            add_filter( 'allow_major_auto_core_updates', '__return_false' );         // Enable major updates
            break;
        default:
            //Development, minor, and major updates are all disabled
            add_filter( 'automatic_updater_disabled', '__return_true' );
            break;
    }


    if(empty($pluginIgnoredList)){
        add_filter( 'auto_update_plugin', '__return_true' );
        return;
    };


    add_filter( 'auto_update_plugin', 'laksh_automatic_updates_specific_plugin_handler', 10, 2 );
}

laksh_automatic_updates_init();

function laksh_automatic_updates_specific_plugin_handler ( $update, $item ) {
    $pluginIgnoredList = (new Laksh_Automatic_Updates_Plugin())->getIgnoredPlugins();
    if(in_array($item->slug, $pluginIgnoredList)){
        return false;
    }
    return $update;
}


function laksh_automatic_updates_core_handler(){
    $updateType = (isset($_GET['updateType']) && $_GET['updateType']!='') ? $_GET['updateType'] : false;
    if($updateType === false) die('unknown type');

    $autoUpdater = new Laksh_Automatic_Updates_Core();
    $autoUpdater->updateCoreOption($updateType);

    echo wp_send_json($autoUpdater->getCoreUpdateOption());
    die();
}

function laksh_automatic_updates_plugin_handler(){
    $pluginSlug = (isset($_GET['slug']) && $_GET['slug']!='') ? $_GET['slug'] : null;
    $pluginStatus = (isset($_GET['status']) && $_GET['status']!='') ? filter_var($_GET['status'], FILTER_VALIDATE_INT)  : false;
    if($pluginSlug === null || $pluginStatus === false) die('plugin or update type is empty');
    $plugins = get_plugins();
    if(!array_key_exists($pluginSlug, $plugins)){
        die('plugin does not exits');
    }

    $autoUpdater = new Laksh_Automatic_Updates_Plugin();
    $autoUpdater->updatePluginStatus($pluginSlug, $pluginStatus);
    $ignoredList = $autoUpdater->getIgnoredPlugins();

    echo wp_send_json($ignoredList);
    die();
}


function laksh_automatic_updates_notification_handler(){
    if(!isset($_GET['allow'])){
        die();
    }

    $isNotificationEnabled = (isset($_GET['allow']) && $_GET['allow']!='') ? $_GET['allow'] : false;
    (new Laksh_Automatic_Updates_Core())->updateNotifications($isNotificationEnabled);
    //echo wp_send_json($autoUpdater->getCoreUpdateOption());
    die();
}

function laksh_automatic_updates_manage_handler(){
    //$activePlugins = get_option( 'active_plugins', [] );


    $autoUpdater = new Laksh_Automatic_Updates_Plugin();
    $ignoredPlugins = $autoUpdater->getIgnoredPlugins();

    $coreAutoUpdater = new Laksh_Automatic_Updates_Core();

    $currentCoreUpdateType = $coreAutoUpdater->getCoreUpdateOption();
    $coreUpdateTypes = $coreAutoUpdater->getCoreUpdateTypes();

    $isNotificationEnabled = $coreAutoUpdater->isNotificationEnabled();

    //$ignoredPlugins = [];

    //var_dump($ignoredPlugins);
    $plugins = get_plugins();
    //var_dump($activePlugins, $allPlugins);
    include __DIR__.'/inc/form.php';
}

