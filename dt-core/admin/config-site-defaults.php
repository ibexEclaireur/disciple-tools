<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly
/**
 * Default Structure
 *
 * This is for default structure settings.
 *
 * @author  Chasm Solutions
 * @package Disciple_Tools
 */

/*********************************************************************************************
 * Action and Filters
 */

add_action( 'init', 'set_permalink_structure' );
add_action( 'permalink_structure_changed', 'permalink_structure_changed_callback' );
//unconditionally allow duplicate comments
add_filter( 'duplicate_comment_id', '__return_false' );
//allow multiple comments in quick succession
add_filter( 'comment_flood_filter', '__return_false' );



/*********************************************************************************************
* Functions
*/

/**
 * Set default premalink structure
 * Needed for the rest api url structure (for wp-json to work)
 */
function set_permalink_structure(){
    global $wp_rewrite;
    $wp_rewrite->set_permalink_structure( '/%postname%/' );
    flush_rewrite_rules();
}

function warn_user_about_permalink_settings() {
    ?>
    <div class="error notices">
        <p><?php _e( 'You may only set your permalink settings to "Post name"' ); ?></p>
    </div>
    <?php
}

function permalink_structure_changed_callback( $permalink_structure ) {
    global $wp_rewrite;
    if ($permalink_structure !== '/%postname%/') {
        add_action( 'admin_notices', 'warn_user_about_permalink_settings' );
    }
}

/**
 * Admin panel svg icon for disciple tools.
 * @return string
 */
function dt_svg_icon() {
    return 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMS40IDIwLjMyIj48ZGVmcz48c3R5bGU+LmF7ZmlsbDojMmQyZDJkO308L3N0eWxlPjwvZGVmcz48dGl0bGU+ZGlzY2lwbGUtdG9vbHM8L3RpdGxlPjxwb2x5Z29uIGNsYXNzPSJhIiBwb2ludHM9IjIxLjQgMjAuMzIgOS4zIDAgMi44NiAxMC44MSA4LjUyIDIwLjMyIDIxLjQgMjAuMzIiLz48cG9seWdvbiBjbGFzcz0iYSIgcG9pbnRzPSIwLjAyIDE1LjU4IDAgMTUuNjEgMi44MyAyMC4zMiA1LjUxIDE1LjM0IDAuMDIgMTUuNTgiLz48L3N2Zz4=';
}

/**
 * Returns the default master array of site options
 *
 * @return array
 */
function dt_get_site_options_defaults () {
    $fields = [];
    
    $fields[ 'version' ] = '1.0';
    
    $fields[ 'notifications' ] = [
        'new_web'          => true,
        'new_email'        => true,
        'mentions_web'     => true,
        'mentions_email'   => true,
        'updates_web'      => true,
        'updates_email'    => true,
        'changes_web'      => true,
        'changes_email'    => true,
        'milestones_web'   => true,
        'milestones_email' => true,
    ];
    
    $fields[ 'add_people_groups' ]        = true;
    $fields[ 'clear_data_on_deactivate' ] = true;
    $fields[ 'daily_reports' ]            = [
        'build_report_for_contacts'  => true,
        'build_report_for_groups'    => true,
        'build_report_for_facebook'  => true,
        'build_report_for_twitter'   => true,
        'build_report_for_analytics' => true,
        'build_report_for_adwords'   => true,
        'build_report_for_mailchimp' => true,
        'build_report_for_youtube'   => true,
    ];
    
    return $fields;
}

function dt_update_site_options_to_current_version() {
    return true;
    // TODO save current settings
    // TODO check and update keys
    // TODO set new keys to default
    // TODO update site options meta and return true.
}
