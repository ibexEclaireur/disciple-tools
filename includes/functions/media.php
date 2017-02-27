<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Sanitize Image Name
 *
 * This restricts the admin panel view of contacts, groups, and media to the those owned by the logged in user.
 *
 * @author Chasm Solutions
 * @package Disciple_Tools
 */

/*
 * Action and Filters
 */
    add_filter('sanitize_file_name', 'make_filename_hash', 10);

/*
* Functions
*/

    /*
     * Sanitize image file name
     *
     * https://wordpress.org/plugins/wp-hash-filename/
     * */
    function make_filename_hash($filename) {
        $info = pathinfo($filename);
        $ext  = empty($info['extension']) ? '' : '.' . $info['extension'];
        $name = basename($filename, $ext);
        return md5($name) . $ext;
    }
    /* End Sanitize file name */


