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
add_action( 'admin_enqueue_scripts', 'contact_groups_page_scripts' );
//add_action( 'admin_enqueue_scripts', 'group_page_scripts' );

/*
 * Functions
 */


// Loads scripts and styles for the admin contacts and groups pages.
function contact_groups_page_scripts() {
    // Global object containing current admin page
    global $pagenow, $post;

    // If current page is post.php and post isset than query for its post type
    // if the post type is 'event' do something
    if ( 'post.php' === $pagenow && 'contacts' === get_post_type( $post) || 'groups' === get_post_type( $post ) ) {

        wp_register_style( 'dt_admin_css', Disciple_Tools()->plugin_css . 'disciple-tools-admin-styles.css' );
        wp_enqueue_style( 'dt_admin_css' );

        wp_enqueue_script( 'dt_contact_scripts', Disciple_Tools()->plugin_js .'disciple-tools-admin.js', array(), '1.0.0', true  );
    }
}


