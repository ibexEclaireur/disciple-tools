<?php
/**
 * Contains create, update and delete functions for users, wrapping access to
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
 * Class Disciple_Tools_Users
 *
 * Functions for creating, finding, updating or deleting contacts
 */


class Disciple_Tools_Users {
    
  
    

    public static function get_assignable_users_compact( string $search_string = '' ){
//        @todo better permissions?
//        @todo return only the users the user has the permission to assign to
        if (!current_user_can( "access_contacts" )){
            return new WP_Error( __FUNCTION__, __( "No permissions to assign" ), ['status' => 403] );
        }

        $userQuery = new WP_User_Query( array(
            'search'         => '*'.esc_attr( $search_string ).'*',
            'search_columns' => array(
                'user_login',
                'user_nicename',
                'user_email',
                'user_url',
            ),
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key'     => 'first_name',
                    'value'   => $search_string,
                    'compare' => 'LIKE'
                ),
                array(
                    'key'     => 'last_name',
                    'value'   => $search_string,
                    'compare' => 'LIKE'
                )
            )
        ) );
        $users = $userQuery->get_results();
        $list = [];


        foreach($users as $user){
            if ( user_can( $user, "access_contacts" )){
                $list[] = [
                    "name" => $user->display_name,
                    "ID" => $user->ID
                ];
            }
        }
        return $list;
    }
    
    
    
    
}
