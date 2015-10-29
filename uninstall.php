<?php

//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();

$settings = get_option( 'livesite_plugin_settings' );

if ( $settings ){

    $modules = $settings['modules'];

    if ( isset( $modules ) ){

        // Run over all modules and remove pages
        foreach ( $modules as $module ){

        	$page_id = $module['custom_page_id'];

        	// If custom page has been created delete page permanantely and skip the trash
        	if ( $page_id )
            	wp_delete_post( $page_id, true );

        }

    }

    // Remove plugin settings from db
    delete_option( 'livesite_plugin_settings' );

}

?>
