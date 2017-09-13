<?php
/**
 * Contains create, update and delete functions for posts, wrapping access to
 * the database
 *
 *
 * @package  Disciple_Tools
 * @category Plugin
 * @author   Chasm.Solutions & Kingdom.Training
 * @since    0.1
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

/**
 * Class Disciple_Tools_Contacts
 *
 * Functions for creating, finding, updating or deleting contacts
 */


class Disciple_Tools_Posts {

    public function __construct(){}


    /**
     * Permissions for interaction with contacts Custom Post Types
     * Example. Role permissions available on contacts:
     *
     *  access_contacts
     *  create_contacts
     *  view_any_contacts
     *  assign_any_contacts  //assign contacts to others
     *  update_any_contacts  //update any contact
     *  delete_any_contacts  //delete any contact
     *
     */

    public static function can_access( string $post_type )
    {
        return current_user_can( "access_" . $post_type );
    }

    public static function can_view_all( string $post_type )
    {
        return current_user_can( "view_any_" . $post_type );
    }

    public static function can_create( string $post_type ) {
        return current_user_can( 'create_' . $post_type );
    }

    public static function can_delete( string $post_type ){
        return current_user_can( 'delete_any_' . $post_type );
    }


    /**
     * A user can view the record if they have the global permission or
     * if the post if assigned or shared with them
     * @return bool
     */
    public static function can_view( string $post_type, int $post_id ){
        global $wpdb;
        if ( current_user_can( 'view_any_' . $post_type )){
            return true;
        } else {
            $user = wp_get_current_user();
            $assigned_to = get_post_meta( $post_id, "assigned_to", true );
            if ( $assigned_to && $assigned_to === "user-".$user->ID ){
                return true;
            } else {
                $shares = $wpdb->get_results(
                    "SELECT * FROM $wpdb->dt_share WHERE post_id = '$post_id'",
                    ARRAY_A
                );
                foreach ($shares as $share) {
                    if ( $share->ID === $user->ID ) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * A user can update the record if they have the global permission or
     * if the post if assigned or shared with them
     * @return bool
     */
    public static function can_update( string $post_type, int $post_id )
    {
        global $wpdb;
        if ( current_user_can( 'update_any_' . $post_type ) ) {
            return true;
        } else {
            $user = wp_get_current_user();
            $assigned_to = get_post_meta( $post_id, "assigned_to", true );
            if ( isset( $assigned_to ) && $assigned_to === "user-" . $user->ID ) {
                return true;
            } else {
                $shares = $wpdb->get_results(
                    "SELECT * FROM $wpdb->dt_share WHERE post_id = '$post_id'",
                    ARRAY_A
                );
                foreach ($shares as $share) {
                    if ( $share->ID === $user->ID ) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}





