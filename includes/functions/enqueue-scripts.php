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
add_action( 'admin_enqueue_scripts', 'dismiss_notice_callback_script' );

/*
 * Functions
 */


// Loads scripts and styles for the admin contacts and groups pages.
function contact_groups_page_scripts() {
    // Global object containing current admin page
    global $pagenow, $post;

    // If current page is post.php and post isset than query for its post type
    // if the post type is 'event' do something
    if ( 'post.php' === $pagenow || 'post-new.php' === $pagenow && 'contacts' === get_post_type( $post) || 'groups' === get_post_type( $post ) ) {

        wp_register_style( 'dt_admin_css', Disciple_Tools()->plugin_css . 'disciple-tools-admin-styles.css' );
        wp_enqueue_style( 'dt_admin_css' );

        wp_enqueue_script( 'dt_contact_scripts', Disciple_Tools()->plugin_js .'disciple-tools-admin.js', array(), '1.0.0', true  );

        if (! user_can(get_current_user_id(), 'publish_locations')) {
            wp_register_style( 'dt_marketer_css', Disciple_Tools()->plugin_css . 'marketer-styles.css' );
            wp_enqueue_style( 'dt_marketer_css' );
        }
    }


}

function dismiss_notice_callback_script(){
    global $pagenow;
    if (is_admin() && $pagenow === 'options-general.php'){
        wp_enqueue_script('disciple-tools-admin_script', Disciple_Tools()->plugin_js .'disciple-tools-admin.js',  array('jquery'), '1.0', true);
    }
}



