<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Public_Hooks
 *
 * Expose some public rest api endpoints to outside sources
 */

class Disciple_Tools_Rest_Endpoints
{

    /**
     * @var object Public_Hooks instance variable
     */
    private static $_instance = null;

    /**
     * Public_Hooks. Ensures only one instance of Public_Hooks is loaded or can be loaded.
     * @return Disciple_Tools_Rest_Endpoints instance
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
    private $contact_controller;

    public function __construct()
    {
        $this->namespace = $this->context . "/v" . intval($this->version);
        add_action('rest_api_init', array($this,  'add_api_routes'));
        $this->contact_controller = new Contact_Controller;
    }

    /**
     * Add the api routes
     */
    public function add_api_routes(){
        register_rest_route($this->namespace, '/dt-public/create-contact', [
            'methods' => 'POST',
            'callback' => array($this, 'public_create_contact'),
        ]);
        register_rest_route($this->namespace, '/get-contact', [
        	"methods" => "GET",
	        "callback" => array($this, 'get_contact')
        ]);
    }


    /**
     * @param WP_REST_Request $request as application/json
     * @return array|WP_Error The new contact Id on success, an error on failure
     */
    public function public_create_contact(WP_REST_Request $request ){
        //@todo authentication/token

        $fields = $request->get_json_params();

        $result =  Contact_Controller::create_contact($fields);
        if ($result["success"] == true){
            return $result;
        } else {
            return new WP_Error("contact_creation_error", $result["message"], array('status', 400));
        }
    }


	/**
	 * Get a single contact by ID
	 * @param WP_REST_Request $request
	 *
	 * @return array|WP_Error
	 */
    public function get_contact(WP_REST_Request $request){
    	$queries =  $request->get_query_params();
	    if (isset($queries['id'])){
			$result = Contact_Controller::get_contact($queries['id']);
		    if ($result["success"] == true){
			    return $result["contact"];
		    } else {
			    return new WP_Error("get_contact_error", $result["message"], array('status', 400));
		    }
	    } else {
	    	return new WP_Error("get_contact_error", "Please provide a valid id", array('status', 400));
	    }
    }


}