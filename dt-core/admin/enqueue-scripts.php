<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly
/**
 * Enqueue Scripts for the site.
 *
 * @author  Chasm Solutions
 * @package Disciple_Tools
 */

/*
 * Action and Filters
 */

add_action( 'admin_enqueue_scripts', 'contact_page_scripts' );
add_action( 'admin_enqueue_scripts', 'group_page_scripts' );
add_action( 'admin_enqueue_scripts', 'location_page_scripts' );
add_action( 'admin_enqueue_scripts', 'asset_page_scripts' );
add_action( 'admin_enqueue_scripts', 'dismiss_notice_callback_script' );

/*
 * Functions
 */


/**
 * Loads scripts and styles for the contacts page.
 */
function contact_page_scripts() {
    global $pagenow, $post;

    if ( ('post.php' === $pagenow || 'post-new.php' === $pagenow) && 'contacts' === get_post_type( $post )) {

        wp_register_style( 'dt_admin_css', Disciple_Tools()->plugin_css . 'disciple-tools-admin-styles.css' );
        wp_enqueue_style( 'dt_admin_css' );

        wp_enqueue_script( 'dt_contact_scripts', Disciple_Tools()->plugin_js .'dt-contacts.js', ['jquery', 'jquery-ui-core'], '1.0.0', true );
        wp_enqueue_script( 'dt_shared_scripts', Disciple_Tools()->plugin_js .'dt-shared.js', [], '1.0.0', true );
    }
}

/**
 * Loads scripts and styles for the groups page.
 */
function group_page_scripts() {
    global $pagenow, $post;

    if ( ('post.php' === $pagenow || 'post-new.php' === $pagenow ) && 'groups' === get_post_type( $post ) ) {

        wp_register_style( 'dt_admin_css', Disciple_Tools()->plugin_css . 'disciple-tools-admin-styles.css' );
        wp_enqueue_style( 'dt_admin_css' );

        wp_enqueue_script( 'dt_group_scripts', Disciple_Tools()->plugin_js .'dt-groups.js', ['jquery', 'jquery-ui-core'], '1.0.0', true );
        wp_enqueue_script( 'dt_shared_scripts', Disciple_Tools()->plugin_js .'dt-shared.js', [], '1.0.0', true );
        
        
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-datepicker',[ 'jquery' ] );
        
        wp_register_style('jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');
        wp_enqueue_style( 'jquery-ui' );
    }
}

/**
 * Loads scripts and styles for the locations page.
 */
function location_page_scripts() {
    global $pagenow, $post;

    if ( ('post.php' === $pagenow || 'post-new.php' === $pagenow) && 'locations' === get_post_type( $post ) ) {

        wp_register_style( 'dt_admin_css', Disciple_Tools()->plugin_css . 'disciple-tools-admin-styles.css' );
        wp_enqueue_style( 'dt_admin_css' );

        wp_enqueue_script( 'dt_group_scripts', Disciple_Tools()->plugin_js .'dt-locations.js', ['jquery', 'jquery-ui-core'], '1.0.0', true );
        wp_enqueue_script( 'dt_shared_scripts', Disciple_Tools()->plugin_js .'dt-shared.js', [], '1.0.0', true );
    }
}

/**
 * Loads scripts and styles for the assets page.
 */
function asset_page_scripts() {
    global $pagenow, $post;

    if ( ('post.php' === $pagenow || 'post-new.php' === $pagenow) && 'assets' === get_post_type( $post ) ) {

        wp_register_style( 'dt_admin_css', Disciple_Tools()->plugin_css . 'disciple-tools-admin-styles.css' );
        wp_enqueue_style( 'dt_admin_css' );

        wp_enqueue_script( 'dt_group_scripts', Disciple_Tools()->plugin_js .'dt-assets.js', ['jquery', 'jquery-ui-core'], '1.0.0', true );
        wp_enqueue_script( 'dt_shared_scripts', Disciple_Tools()->plugin_js .'dt-shared.js', [], '1.0.0', true );
    }
}

/**
 *
 */
function dismiss_notice_callback_script(){
    global $pagenow;
    if (is_admin() && $pagenow === 'options-general.php'){
        wp_enqueue_script( 'disciple-tools-admin_script', Disciple_Tools()->plugin_js .'disciple-tools-admin.js',  ['jquery'], '1.0', true );
    }
}
    



