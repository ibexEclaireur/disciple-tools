<?php

/**
 * Disciple_Tools_Notifications_Endpoints
 *
 * @class Disciple_Tools_Notifications_Endpoints
 * @version    0.1
 * @since 0.1
 * @package    Disciple_Tools
 * @author Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly
}

class Disciple_Tools_Notifications_Endpoints {
    /**
     * Disciple_Tools_Notifications_Endpoints The single instance of Disciple_Tools_Notifications_Endpoints.
     * @var     object
     * @access  private
     * @since     0.1
     */
    private static $_instance = null;
    
    /**
     * Main Disciple_Tools_Notifications_Endpoints Instance
     *
     * Ensures only one instance of Disciple_Tools_Notifications_Endpoints is loaded or can be loaded.
     *
     * @since 0.1
     * @static
     * @return Disciple_Tools_Notifications_Endpoints instance
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
        add_action( 'rest_api_init', [$this,  'add_api_routes'] );
    } // End __construct()
    
    public function add_api_routes () {
        $version = '1';
        $namespace = 'dt/v' . $version;
        $base = 'notifications';
        
        register_rest_route(
            $namespace, '/' . $base . '/mark_notification_viewed', [
                [
                    'methods'         => WP_REST_Server::CREATABLE,
                    'callback'        => [ $this, 'mark_notification_viewed' ],
                ],
            ]
        );
    
        register_rest_route(
            $namespace, '/' . $base . '/get_notifications_for_user', [
                [
                    'methods'         => WP_REST_Server::CREATABLE,
                    'callback'        => [ $this, 'get_notifications_for_user' ],
                ],
            ]
        );
        
    }
    
    /**
     * Get tract from submitted address
     *
     * @param  WP_REST_Request $request
     * @access public
     * @since  0.1
     * @return string|WP_Error|array The contact on success
     */
    public function mark_notification_viewed( WP_REST_Request $request ){
        $params = $request->get_params();
        if (isset( $params['notification_ids'] )){
            $result = Disciple_Tools_Notifications::mark_notification_viewed( $params['notification_ids'] );
            if ($result["status"] == 'OK'){
                return $result['rows_affected'];
            } else {
                return new WP_Error( "notification_processing_error", $result["message"], ['status' => 400] );
            }
        } else {
            return new WP_Error( "notification_param_error", "Please provide a valid array", ['status' => 400] );
        }
    }
    
    /**
     * Get tract from submitted address
     *
     * @param  WP_REST_Request $request
     * @access public
     * @since  0.1
     * @return string|WP_Error|array The contact on success
     */
    public function get_notifications_for_user( WP_REST_Request $request ){
        $params = $request->get_params();
        if (isset( $params['user_id'] )){
            $result = Disciple_Tools_Notifications::get_notifications_for_user( $params );
            if ($result["status"]){
                return $result['result'];
            } else {
                return new WP_Error( "get_user_notification_processing_error", $result["message"], ['status' => 400] );
            }
        } else {
            return new WP_Error( "get_user_notification_param_error", "Please provide a valid array", ['status' => 400] );
        }
    }
    
}
