<?php
/**
* Custom endpoints file
*
* @package  Disciple_Tools
* @category Plugin
* @author   Chasm.Solutions & Kingdom.Training
* @since    0.1
*/
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

/**
 * Class Disciple_Tools_Groups_Endpoints
 *
 * Expose some public rest api endpoints to outside sources
 */

class Disciple_Tools_Groups_Endpoints {

    /**
     * @var object Public_Hooks instance variable
     */
    private static $_instance = null;

    /**
     * Public_Hooks. Ensures only one instance of Public_Hooks is loaded or can be loaded.
     *
     * @return Disciple_Tools_Groups_Endpoints instance
     */
    public static function instance () {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    private $version = 1;
    private $context = "dt";
    private $namespace;

    public function __construct()
    {
        $this->namespace = $this->context . "/v" . intval( $this->version );
        add_action( 'rest_api_init', [$this,  'add_api_routes'] );
    }

    public function add_api_routes(){
        register_rest_route(
            $this->namespace, '/groups', [
            'methods' => 'GET',
            'callback' => [$this, 'get_groups']
            ]
        );
    }

    public function get_groups( WP_REST_Request $request ){
        $params = $request->get_params();
//        @todo check permissions
        $groups = Disciple_Tools_Groups::get_groups();
        return $groups;
    }
}
