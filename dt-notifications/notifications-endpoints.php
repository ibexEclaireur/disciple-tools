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
        $base = 'locations';
        
        register_rest_route(
            $namespace, '/' . $base . '/sample', [
                [
                    'methods'         => WP_REST_Server::CREATABLE,
                    'callback'        => [ $this, 'sample' ],
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
    public function sample( WP_REST_Request $request ){
        $params = $request->get_params();
        if (isset( $params['address'] )){
            $result = Disciple_Tools_Locations::get_tract_map( $params['address'] ); // todo replace with correct connection
            if ($result["status"] == 'OK'){
                return $result;
            } else {
                return new WP_Error( "map_status_error", $result["message"], ['status' => 400] );
            }
        } else {
            return new WP_Error( "map_param_error", "Please provide a valid address", ['status' => 400] );
        }
    }
    
}
