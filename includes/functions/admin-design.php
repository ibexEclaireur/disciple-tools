<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Admin design
 *
 * This restricts the admin panel view of contacts, groups, and media to the those owned by the logged in user.
 *
 * @author Chasm Solutions
 * @package Disciple_Tools
 */

/*********************************************************************************************
 * Action and Filters
 */
if (is_admin()) {
    add_action( 'admin_bar_menu', 'dt_modify_admin_bar', 999 );

    add_filter( 'admin_footer_text', '__empty_footer_string', 11 );
    add_filter( 'update_footer',     '__empty_footer_string', 11 );

    add_action( 'admin_menu', 'dt_remove_post_admin_menus' );

    add_filter( 'get_user_option_admin_color', 'dt_change_admin_color'); // sets the theme to "light"
    remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' ); // Remove options for admin area color scheme
}


/*********************************************************************************************
* Functions
*/

/**
 * Modify the admin bar
 */
function dt_modify_admin_bar( $wp_admin_bar ) {

    // Remove Logo
    $wp_admin_bar->remove_node( 'wp-logo' );

    // Remove "Howday" and replace with "Welcome"
    $user_id = get_current_user_id();
    $current_user = wp_get_current_user();
    $profile_url = get_edit_profile_url( $user_id );

    if ( 0 != $user_id ) {
        /* Add the "My Account" menu */
        $avatar = get_avatar( $user_id, 28 );
        $howdy = sprintf( __('Welcome, %1$s'), $current_user->display_name );
        $class = empty( $avatar ) ? '' : 'with-avatar';

        $wp_admin_bar->add_menu( array(
                'id' => 'my-account',
                'parent' => 'top-secondary',
                'title' => $howdy . $avatar,
                'href' => $profile_url,
                'meta' => array(
                    'class' => $class,
                ),
            )
        );
    } // end if
}

/**
 * Remove menu items
 * @see https://codex.wordpress.org/Function_Reference/remove_menu_page
 */
function dt_remove_post_admin_menus(){
    remove_menu_page( 'edit.php' ); //Posts (Not using posts as a content channel for Disciple Tools, so that no data is automatically exposed by switching themes or plugin.
}

/**
 * Remove Admin Footer and Version Number
 */
function __empty_footer_string () {
    // Update the text area with an empty string.
    return '';
}

/*
 * Set the admin area color scheme
 */
function dt_change_admin_color($result) {
    return 'light';
}


// Removes the tools menu for the marketer
if ( current_user_can('marketer')) {
    add_action( 'admin_menu', 'dt_marketer_remove_tools', 99 );
    function dt_marketer_remove_tools()
    {
        remove_menu_page( 'tools.php' );
    }
}







