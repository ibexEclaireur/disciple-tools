<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Hide Contacts and Media
 *
 * This restricts the admin panel view of contacts, groups, and media to the those owned by the logged in user.
 *
 * @author Chasm Solutions
 * @package Disciple_Tools
 */

/*
 * Action and Filters
 */
//    add_filter('pre_get_posts', 'hide_posts_media_by_other');  // TODO: Not sure if it is necessary to exclude admin users from seeing the media in the media area.
//    add_filter( 'posts_where', 'hide_attachments_wpquery_where' );

/*
* Functions
*/

    /*
    * Set users to only see their posts and media.
    *   This is a key configuration section for partitioning the ability to view contacts.
    *
    * @source  http://phpbits.net/hide-wordpress-posts-and-media-uploaded-by-other-users/
    *
    * */
    function hide_posts_media_by_other($query) {
        global $pagenow;
        if( ( 'edit.php' != $pagenow && 'upload.php' != $pagenow && 'post.php' != $pagenow   ) || !$query->is_admin ){
            return $query;
        }
        if( !current_user_can( 'manage_contacts' ) ) {
            global $user_ID;
            $query->set('author', $user_ID );
        }
        return $query;
    }

    function hide_attachments_wpquery_where( $where ){
        global $current_user;
        if( !current_user_can( 'manage_options' ) ) {
            if( is_user_logged_in() ){
                if( isset( $_POST['action'] ) ){
                    // library query
                    if( $_POST['action'] == 'query-attachments' ){
                        $where .= ' AND post_author='.$current_user->data->ID;
                    }
                }
            }
        }
        return $where;
    }


