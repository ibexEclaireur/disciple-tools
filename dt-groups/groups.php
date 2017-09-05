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

    public static function can_access_groups() {
        return current_user_can( "edit_group" ) && current_user_can( "read_group" ) && current_user_can( "edit_groups" );
    }

    public static function can_view_all_groups() {
        return current_user_can( "read_private_groups" );
    }

    public static function get_viewable_groups( bool $check_permissions = true ) {
        if ($check_permissions && ! self::can_access_groups()) {
            return new WP_Error( __FUNCTION__, __( "You do not have access to these groups" ), ['status' => 403] );
        }
        $current_user = wp_get_current_user();

        $query_args = array(
            'post_type' => 'groups',
            'nopaging' => true,
        );
        if (! self::can_view_all_groups()) {
            // TODO filter just by own groups
            return new WP_Error( __FUNCTION__, __( "Unimplemented" ) );
        }
        return new WP_Query( $query_args );
    }
}
