<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Enqueue Scripts for the site.
 *
 * @author Chasm Solutions
 * @package Disciple_Tools
 */

/*
 * Action and Filters
 */
//add_action( 'admin_enqueue_scripts', 'post_page_scripts' );

/*
 * Functions
 */


/*function post_page_scripts($hook) {  //TODO: Verify this is necissary. Originally designed to give jquery buttons to contacts page.
    // Test if post type page
    if( 'post.php' != $hook )
        return;

    // Enqueue Custom DMMCRM admin styles page
    wp_register_style( 'drm_admin_css', Disciple_Tools()->plugin_css . 'disciple-tools-admin-styles.css' );
    wp_enqueue_style( 'drm_admin_css' );

    // Enqueue Jquery UI CSS
    wp_register_style( 'drm_ui_css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css' );
    wp_enqueue_style( 'drm_ui_css' );

    // Enqueue Jquery UI
    wp_enqueue_script("jquery-ui-core");
    wp_enqueue_script( 'admin_scripts', Disciple_Tools()->plugin_js .'disciple-tools-admin.js', array('jquery', 'jquery-ui-core') );
    // No need to enqueue jQuery as it's already included in the WordPress admin by default

}*/
