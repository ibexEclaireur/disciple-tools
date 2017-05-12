<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Public_Hooks
 *
 * Expose some public rest api endpoints to outside sources
 */

class Public_Hooks
{
    //setup endpoints

    /**
     * @var object Public_Hooks instance variable
     */
    private static $_instance = null;

    /**
     * Public_Hooks. Ensures only one instance of Public_Hooks is loaded or can be loaded.
     * @return Public_Hooks instance
     */
    public static function instance () {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

    /**
     * The Public_Hooks rest api variables
     */
    private $version = 1.0;
    private $context = "dt-hooks";
    private $namespace;

    public function __construct()
    {
        $this->namespace = $this->context . "/v" . intval($this->version);
        add_action('rest_api_init', array($this,  'add_api_routes'));
    }

    /**
     * Add the api routes
     */
    public function add_api_routes(){
        register_rest_route($this->namespace, 'create-contact', [
            'methods' => 'POST',
            'callback' => array($this, 'create_contact'),
        ]);
    }


    /**
     * @param WP_REST_Request $request
     * @return array|WP_Error
     */
    public function create_contact(WP_REST_Request $request ){
        //@todo authentication/token?
        $fields = $request->get_body_params();

        $result =  Contact_Controller::create_contact($fields);
        if ($result["success"] == true){
            return $result;
        } else {
            return new WP_Error("contact_creation_error", $result["message"], array('status', 400));
        }
    }

}