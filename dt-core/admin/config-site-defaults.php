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
