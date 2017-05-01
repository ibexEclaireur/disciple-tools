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

add_action( 'admin_bar_menu', 'disciple_tools_modify_admin_bar', 999 );

add_filter( 'admin_footer_text', '__empty_footer_string', 11 );
add_filter( 'update_footer',     '__empty_footer_string', 11 );

add_filter( 'get_user_option_admin_color', 'change_admin_color');
remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' ); // Remove options for admin area color scheme

add_filter('manage_contacts_posts_columns', 'contacts_table_head');
add_action( 'manage_contacts_posts_custom_column', 'contacts_table_content', 10, 2 );

if( is_admin() && !current_user_can( 'administrator' ) ) {
    add_action( 'admin_menu', 'disciple_tools_remove_posts_menu' );
}


/*********************************************************************************************
* Functions
*/

/**
 * Modify the admin bar
 */
function disciple_tools_modify_admin_bar( $wp_admin_bar ) {

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
 * Remove Admin Footer and Version Number
 */
function __empty_footer_string () {
    // Update the text area with an empty string. TODO: see if this is better to do with CSS display:none;
    return '';
}

/*
 * Set the admin area color scheme
 */
function change_admin_color($result) {
    return 'light';
}

/*
 * Adds columns to the all contacts screen
 * TODO: Consider moving to contacts object
 */
function contacts_table_head( $defaults ) {
    $defaults['phone']  = 'Phone';
    $defaults['seeker_path']    = 'Seeker Path';
    $defaults['seeker_milestones']    = 'Seeker Milestone';
    return $defaults;
}

function contacts_table_content( $column_name, $post_id ) {
    if ($column_name == 'phone') {
        echo get_post_meta( $post_id, 'phone', true );
        ;
    }
    if ($column_name == 'seeker_path') {
        $status = get_post_meta( $post_id, 'seeker_path', true );
        echo $status;
    }

    if ($column_name == 'seeker_milestones') {
        echo get_post_meta( $post_id, 'seeker_milestones', true );
    }

}

/**
 * Removes the Posts menu from all users but administrators
 */
if( is_admin() && !current_user_can( 'administrator' ) ) {

    function remove_menus(){
        remove_menu_page( 'edit.php' );
    }
    add_action( 'admin_menu', 'remove_menus' );
}

function disciple_tools_remove_posts_menu(){
    remove_menu_page( 'edit.php' ); // Posts
    remove_menu_page( 'edit.php?post_type=page' );    //Pages
}







