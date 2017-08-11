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
