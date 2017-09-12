<?php

/**
 * Contains create, update and delete functions for notifications, wrapping access to
 * the database
 *
 * @class Disciple_Tools_Notifications
 * @version    0.1
 * @since 0.1
 * @package    Disciple_Tools
 * @author Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly
}

/**
 * @since 1.0.0
 *
 * @see Disciple_Tools_Activity_Log_API::insert
 *
 * @param array $args
 * @return void
 */
function dt_notification_insert( $args = [] ) {
    Disciple_Tools_Notifications::insert_notification( $args );
}

function dt_notification_delete( $args = [] ) {
    Disciple_Tools_Notifications::delete_notification( $args );
}

class Disciple_Tools_Notifications {
    
    /**
     * Disciple_Tools_Admin_Menus The single instance of Disciple_Tools_Admin_Menus.
     * @var     object
     * @access  private
     * @since     0.1
     */
    private static $_instance = null;
    
    /**
     * Main Disciple_Tools_Notifications Instance
     *
     * Ensures only one instance of Disciple_Tools_Notifications is loaded or can be loaded.
     *
     * @since 0.1
     * @static
     * @return Disciple_Tools_Notifications instance
     */
    public static function instance () {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()
    
    /**
     * Constructor function.
     * @access  public
     * @since   0.1
     */
    public function __construct () {
    
    } // End __construct()
    
    /**
     * Insert statement
     * @since 1.0.0
     *
     * @param array $args
     * @return void
     */
    public static function insert_notification( $args ) {
        global $wpdb;
        
        // Make sure for non duplicate.
        $check_duplicate = $wpdb->get_row(
            $wpdb->prepare(
                'SELECT `id`
                    FROM %1$s
					WHERE `user_id` = \'%2$s\'
						AND `item_id` = \'%3$s\'
						AND `secondary_item_id` = \'%4$s\'
						AND `notification_name` = \'%5$s\'
						AND `notification_action` = \'%6$s\'
						AND `notification_note` = \'%7$s\'
						AND `date_notified` = \'%8$s\'
						AND `is_new` = \'%9$s\'
				;',
                $wpdb->dt_notifications,
                $args['user_id'],
                $args['item_id'],
                $args['secondary_item_id'],
                $args['notification_name'],
                $args['notification_action'],
                $args['notification_note'],
                $args['date_notified'],
                $args['is_new']
            )
        );
        
        if ( $check_duplicate ) {
            return;
        }
        
        $wpdb->insert(
            $wpdb->dt_notifications,
            [
                'user_id'                   => $args['user_id'],
                'item_id'                   => $args['item_id'],
                'secondary_item_id'         => $args['secondary_item_id'],
                'notification_name'         => $args['notification_name'],
                'notification_action'       => $args['notification_action'],
                'notification_note'         => $args['notification_note'],
                'date_notified'             => $args['date_notified'],
                'is_new'                    => $args['is_new'],
            ],
            [ '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%d' ]
        );
        
        // TODO consider adding a meta data process here
        
        // Final action on insert.
        do_action( 'dt_insert_notification', $args );
    }
    
    /**
     * Insert statement
     * @since 1.0.0
     *
     * @param array $args
     * @return void
     */
    public static function delete_notification( $args ) {
        global $wpdb;
        
        $args = wp_parse_args(
            $args,
            [
                'user_id'               => '',
                'item_id'               => '',
                'secondary_item_id'     => '',
                'notification_name'     => 'mention',
                'date_notified'         => '',
            ]
        );
        
        $wpdb->delete(
            $wpdb->dt_notifications,
            [
                'user_id'               => $args['user_id'],
                'item_id'               => $args['item_id'],
                'secondary_item_id'     => $args['secondary_item_id'],
                'notification_name'     => $args['notification_name'],
                'date_notified'         => $args['date_notified'],
            ]
        );
        
        
        // Final action on insert.
        do_action( 'dt_delete_notification', $args );
    }
    
    /**
     * Mark the is_new field to 0 after user has viewed notification
     * {"notification_ids": [1,2,3 4]} requires valid json array
     *
     * @param $notification_ids array
     *
     * @return array
     */
    public static function mark_notification_viewed( $notification_ids ) {
        global $wpdb;
    
        if ( ! is_array( $notification_ids ) ) {
            return ['status' => 'Error', 'message' => 'Not an array' ];
        }
        
        $i = 0;
        foreach($notification_ids as $notification_id) {
            $wpdb->update(
                $wpdb->dt_notifications,
                [
                    'is_new' => 0,
                ],
                [
                    'id' => $notification_id
                ]
            );
            $i = $i + $wpdb->rows_affected;
        }
        
        return $wpdb->last_error ? ['status' => 'Error', 'message' => $wpdb->last_error] : ['status' => 'OK', 'rows_affected' => $i];
    }
    
    /**
     * Get user notifications
     *
     * @param     $params array     user_id (required)
     *                              limit (optional) default 25.
     *                              offset (optional) default 0.
     *
     * @return array
     */
    public static function get_notifications_for_user( $params ) {
        global $wpdb;
        $user_id = $params['user_id'];
        isset( $params['limit'] ) ? $limit = $params['limit'] : $limit = 25;
        isset( $params['offset'] ) ? $offset = $params['offset'] : $offset = 0;
        
        
        $result = $wpdb->get_results( "SELECT * FROM $wpdb->dt_notifications WHERE user_id = '$user_id' ORDER BY date_notified DESC LIMIT $limit OFFSET $offset", ARRAY_A );
        
        if($result) {
            return [
                'status' => 'OK',
                'notifications' => $result,
            ];
        } else {
            return [
              'status' => 'Fail',
              'message' => 'Fails to query user notifications. Query returned false.'
            ];
        }
    }
    
    /**
     * Get field update message
     * @param $activity_id
     *
     * @return null|string
     */
    public static function get_field_update_message( $activity_id ) {
        global $wpdb;
        
        $result = $wpdb->get_var( "SELECT object_note FROM $wpdb->dt_activity_log WHERE histid = '$activity_id'" );
        if(!$result) {
            return 'no activity record';
        }
        
        return $result;
    }
    
    /**
     * Get the @mention message content
     * @param $comment_id
     *
     * @return array|null|WP_Post
     */
    public static function get_at_mention_message( $comment_id ) {
        return get_post( $comment_id );
    }
    
    // TODO add the function to change a notification from new to viewed. 1 to 0 in the is_new table column
    
    // TODO get_new_notifications for a user_id. Filter by notification preferences.
    
    // TODO modify_notifications for a user. Store preferences in the usermeta data.
    
    
    
}
