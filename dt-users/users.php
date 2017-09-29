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
     * @param int    $user_id
     * @param string $preference_key
     *
     * @return array
     */
    public static function change_notification_preference( int $user_id, string $preference_key )
    {

        $user_notifications = dt_get_user_notification_options( $user_id );
        if( is_wp_error( $user_notifications ) ) {
            return [
                'status'  => false,
                'message' => $user_notifications->get_error_message(),
            ];
        }

        foreach( $user_notifications as $key => $value ) {
            if( $key == $preference_key ) {
                $value === true ? $user_notifications[ $key ] = false : $user_notifications[ $key ] = true;
            }
        }

        $update = update_user_meta( $user_id, 'dt_notification_options', $user_notifications );

        if( $update ) {
            return [
                'status'   => true,
                'response' => 'success',
            ];
        } else {
            return [
                'status'  => false,
                'message' => 'Unable to update_user_option while updating user notification preferences.',
            ];
        }
    }

    /**
     * @param int $user_id
     *
     * @return array
     */
    public static function change_availability( int $user_id )
    {

        $user_availability = dt_get_user_option_availability( $user_id );
        if( is_wp_error( $user_availability ) ) {
            return [
                'status'  => false,
                'message' => 'Failed to get user availability option from db.',
            ];
        };

        $user_availability == true ? $user_availability = false : $user_availability = true;

        // @codingStandardsIgnoreLine  Note: VIP coding standards errors on the use of update_user_meta
        $update = update_user_meta( $user_id, 'dt_availability', $user_availability );

        if( $update ) {
            return [
                'status'   => true,
                'response' => 'success',
            ];
        } else {
            return [
                'status'  => false,
                'message' => 'Unable to update user dt_availability option.',
            ];
        }
    }

}
