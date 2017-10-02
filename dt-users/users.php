<?php
/**
 * Contains create, update and delete functions for users, wrapping access to
 * the database
 *
 * @package  Disciple_Tools
 * @category Plugin
 * @author   Chasm.Solutions & Kingdom.Training
 * @since    0.1
 */
if( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly.

/**
 * Class Disciple_Tools_Users
 * Functions for creating, finding, updating or deleting contacts
 */
class Disciple_Tools_Users
{
    /**
     * @param  $search_string
     *
     * @return array|\WP_Error
     */
    public static function get_assignable_users_compact( string $search_string = null )
    {
        //        @todo better permissions?
        //        @todo return only the users the user has the permission to assign to
        if( !current_user_can( "access_contacts" ) ) {
            return new WP_Error( __FUNCTION__, __( "No permissions to assign" ), [ 'status' => 403 ] );
        }

        $user_query = new WP_User_Query( [
            'search'         => '*' . esc_attr( $search_string ) . '*',
            'search_columns' => [
                'user_login',
                'user_nicename',
                'user_email',
                'user_url',
            ],
            'meta_query'     => [
                'relation' => 'OR',
                [
                    'key'     => 'first_name',
                    'value'   => $search_string,
                    'compare' => 'LIKE',
                ],
                [
                    'key'     => 'last_name',
                    'value'   => $search_string,
                    'compare' => 'LIKE',
                ],
            ],
        ] );
        $users = $user_query->get_results();
        $list = [];

        foreach( $users as $user ) {
            if( user_can( $user, "access_contacts" ) ) {
                $list[] = [
                    "name" => $user->display_name,
                    "ID"   => $user->ID,
                ];
            }
        }

        return $list;
    }

    /**
     * Switch user preference for notifications and availability meta fields.
     *
     * @param int    $user_id
     * @param string $preference_key
     *
     * @return array
     */
    public static function switch_preference( int $user_id, string $preference_key )
    {

        $value = get_user_meta( $user_id, $preference_key, true );

        if( empty( $value ) ) {
            $status = update_metadata( 'user', $user_id, $preference_key, true );
            if( $status ) {
                return [
                    'status'   => true,
                    'response' => $status,
                ];
            } else {
                return [
                    'status'  => false,
                    'message' => 'Unable to update_user_option ' . $preference_key . ' to true.',
                ];
            }
        } else {
            $status = update_metadata( 'user', $user_id, $preference_key, false );
            if( $status ) {
                return [
                    'status'   => true,
                    'response' => $status,
                ];
            } else {
                return [
                    'status'  => false,
                    'message' => 'Unable to update_user_option ' . $preference_key . ' to false.',
                ];
            }
        }
    }
}
