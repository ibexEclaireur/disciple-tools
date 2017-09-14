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


    public static function get_posts_shared_with_user( string $post_type, int $user_id ){
        global $wpdb;
        $shares = $wpdb->get_results(
            "SELECT * FROM $wpdb->dt_share WHERE user_id = '$user_id'",
            ARRAY_A
        );
        $list = [];
        foreach($shares as $share){
//          get the shares with a specific post_typo @todo add to shares table
            $post = get_post( $share[ "user_id" ] );
            if (isset( $post->post_type ) && $post->post_type === $post_type){
                $list[] = $post;
            }
        }
        return $list;
    }


    public static function add_post_comment( string $post_type, int $group_id, string $comment ){
        if (! self::can_update( $post_type,  $group_id )) {
            return new WP_Error( __FUNCTION__, __( "You do not have permission for this" ), ['status' => 403] );
        }
        $user = wp_get_current_user();
        $user_id = get_current_user_id();
        $comment_data = [
            'comment_post_ID' => $group_id,
            'comment_content' => $comment,
            'user_id' => $user_id,
            'comment_author' => $user->display_name,
            'comment_author_url' => $user->user_url,
            'comment_author_email' => $user->user_email,
            'comment_type' => 'comment'
        ];

        return wp_new_comment( $comment_data );
    }

    public static function get_post_activity( string $post_type, int $post_id ){
        global $wpdb;
        if (! self::can_view( $post_type, $post_id )){
            return new WP_Error( __FUNCTION__, __( "No permissions to read group" ), ['status' => 403] );
        }
        $q = $wpdb->prepare(
            'SELECT * from %1$s
            WHERE `object_type` = "%3$s"
            AND `object_id` = "%2$s"
            ;',
            $wpdb->activity,
            $post_id,
            $post_type
        );
        $activity = $wpdb->get_results( $q );
        foreach($activity as $a){
            if (isset( $a->user_id ) && $a->user_id > 0 ){
                $a->name = get_user_by( "id", $a->user_id )->display_name;
            }
        }
        return $activity;
    }


    public static function get_post_comments ( string $post_type, int $post_id ){
        if (! self::can_view( $post_type, $post_id )) {
            return new WP_Error( __FUNCTION__, __( "No permissions to read group" ), ['status' => 403] );
        }
        $comments = get_comments( ['post_id'=>$post_id] );
        return $comments;
    }
}





