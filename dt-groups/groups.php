<?php
/**
 * Contains create, update and delete functions for groups, wrapping access to
 * the database
 *
 *
 * @package  Disciple_Tools
 * @category Plugin
 * @author   Chasm.Solutions & Kingdom.Training
 * @since    0.1
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

class Disciple_Tools_Groups {

    public static function get_groups (){
        $query_args = array(
            'post_type' => 'groups',
            'orderby' => 'ID',
            'nopaging' => true,
        );
        $query = new WP_Query( $query_args );
        return $query->posts;
    }
}
