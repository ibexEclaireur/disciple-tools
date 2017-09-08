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
    Disciple_Tools()->notifications->insert( $args ); // TODO need to require this file from root .php file and create this variable
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
    public function insert( $args ) {
        global $wpdb;
        
        $args = wp_parse_args(
            $args,
            [
                'user_id'               => '',
                'item_id'               => '',
                'secondary_item_id'     => '',
                'component_name'        => '',
                'component_action'      => '',
                'date_notified'         => '',
                'is_new'                => 1,
            ]
        );
        
        // Make sure for non duplicate.
        $check_duplicate = $wpdb->get_row(
            $wpdb->prepare(
                'SELECT `id`
                    FROM %1$s
					WHERE `user_id` = \'%2$s\'
						AND `item_id` = \'%3$s\'
						AND `secondary_item_id` = \'%4$s\'
						AND `component_name` = \'%5$s\'
						AND `component_action` = \'%6$s\'
						AND `date_notified` = \'%7$s\'
						AND `is_new` = \'%8$s\'
				;',
                $wpdb->dt_notifications,
                $args['user_id'],
                $args['item_id'],
                $args['secondary_item_id'],
                $args['component_name'],
                $args['component_action'],
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
                'user_id'         => $args['user_id'],
                'item_id'    => $args['item_id'],
                'secondary_item_id' => $args['secondary_item_id'],
                'component_name'    => $args['component_name'],
                'component_action'      => $args['component_action'],
                'date_notified'        => $args['date_notified'],
                'is_new'      => $args['is_new'],
            ],
            [ '%d', '%d', '%d', '%s', '%s', '%s', '%d' ]
        );
        
        // TODO consider adding a meta data process here
        
        // Final action on insert.
        do_action( 'dt_insert_notification', $args );
    }
    
    // TODO add the function to change a notification from new to viewed. 1 to 0 in the is_new table column
    
    // TODO get_new_notifications for a user_id. Filter by notification preferences.
    
    // TODO modify_notifications for a user. Store preferences in the usermeta data.
    
    
    
}
