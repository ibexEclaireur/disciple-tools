<?php
/**
 * Custom endpoints file
 *
 * @package  Disciple_Tools
 * @category Plugin
 * @author   Chasm.Solutions & Kingdom.Training
 * @since    1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

/**
 * Class Disciple_Tools_Statistics_Endpoints
 */
class Disciple_Tools_Metrics_Endpoints {
    /**
     * Disciple_Tools_Metrics_Endpoints The single instance of Disciple_Tools_Metrics_Endpoints.
     *
     * @var     object
     * @access    private
     * @since     1.0.0
     */
    private static $_instance = null;

    /**
     * Main Disciple_Tools_Metrics_Endpoints Instance
     * Ensures only one instance of Disciple_Tools_Metrics_Endpoints is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @return Disciple_Tools_Metrics_Endpoints instance
     */
    public static function instance()
    {
        if( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    } // End instance()

    /**
     * Constructor function.
     *
     * @access  public
     * @since   1.0.0
     */
    public function __construct()
    {
        add_action( 'rest_api_init', [ $this, 'add_api_routes' ] );
    } // End __construct()

    public function add_api_routes()
    {
        $version = '1';
        $namespace = 'dt/v' . $version;

        register_rest_route(
            $namespace, '/metrics/critical_path_prayer', [
                [
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => [ $this, 'critical_path_prayer' ],
                ],
            ]
        );

        register_rest_route(
            $namespace, '/metrics/critical_path_media', [
                [
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => [ $this, 'critical_path_media' ],
                ],
            ]
        );

        register_rest_route(
            $namespace, '/metrics/critical_path_fup', [
                [
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => [ $this, 'critical_path_fup' ],
                ],
            ]
        );

        register_rest_route(
            $namespace, '/metrics/critical_path_multiplication', [
                [
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => [ $this, 'critical_path_multiplication' ],
                ],
            ]
        );

    }

    /**
     * Get tract from submitted address
     *
     * @access public
     * @since  1.0.0
     * @return string|WP_Error|array The contact on success
     */
    public function critical_path_prayer()
    {
        $result = Disciple_Tools_Metrics::critical_path_prayer( true );
        if( is_wp_error( $result ) ) {
            return $result;
        }
        elseif( $result[ "status" ] ) {
            return $result[ 'data' ];
        }
        else {
            return new WP_Error( "critical_path_processing_error", $result[ "message" ], [ 'status' => 400 ] );
        }
    }

    /**
     * Get tract from submitted address
     *
     * @access public
     * @since  1.0.0
     * @return string|WP_Error|array The contact on success
     */
    public function critical_path_media()
    {
        $result = Disciple_Tools_Metrics::critical_path_media( true );
        if( is_wp_error( $result ) ) {
            return $result;
        }
        elseif( $result[ "status" ] ) {
            return $result[ 'data' ];
        }
        else {
            return new WP_Error( "critical_path_processing_error", $result[ "message" ], [ 'status' => 400 ] );
        }
    }

    /**
     * Get tract from submitted address
     *
     * @access public
     * @since  1.0.0
     * @return string|WP_Error|array The contact on success
     */
    public function critical_path_fup()
    {
        $result = Disciple_Tools_Metrics::critical_path_fup( true );
        if( is_wp_error( $result ) ) {
            return $result;
        }
        elseif( $result[ "status" ] ) {
            return $result[ 'data' ];
        }
        else {
            return new WP_Error( "critical_path_processing_error", $result[ "message" ], [ 'status' => 400 ] );
        }
    }

    /**
     * Get tract from submitted address
     *
     * @access public
     * @since  1.0.0
     * @return string|WP_Error|array The contact on success
     */
    public function critical_path_multiplication()
    {
        $result = Disciple_Tools_Metrics::critical_path_multiplication( true );
        if( is_wp_error( $result ) ) {
            return $result;
        }
        elseif( $result[ "status" ] ) {
            return $result[ 'data' ];
        }
        else {
            return new WP_Error( "critical_path_processing_error", $result[ "message" ], [ 'status' => 400 ] );
        }
    }


}
