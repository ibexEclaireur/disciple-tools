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
	        "permission_callback" => function () {
		        return current_user_can( 'publish_contacts' );
	        }
        ]);
        register_rest_route($this->namespace, '/contact/(?P<id>\d+)', [
        	"methods" => "GET",
	        "callback" => array($this, 'get_contact'),
	        "permission_callback" => function () {
		        return current_user_can( 'read_contact' );
	        }
        ]);


        register_rest_route($this->namespace, '/user/(?P<user>[a-zA-Z0-9-]+)/contacts', [
        	"methods" => "GET",
	        "callback" => array($this, 'get_user_contacts'),
	        "permission_callback" => function () {
	            return current_user_can( 'read_contact' );
	        }
        ]);
        register_rest_route($this->namespace, '/user/(?P<user>[a-zA-Z0-9-]+)/team/contacts', [
        	"methods" => "GET",
	        "callback" => array($this, 'get_team_contacts'),
	        "permission_callback" => function () {
		      return current_user_can( 'edit_others_contacts' );
		    }
        ]);
    }

    public function is_id_of_user_logged_in($user){
    	$meta_array = explode('-', $user); // Separate the type and id
        $type = $meta_array[0];
        $id = $meta_array[1];
        $current_user = wp_get_current_user();
        if($type == "user" && isset($current_user->ID)){
			return $current_user->ID == $id;
        }
        return false;
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
    	$params = $request->get_params();
	    if (isset($params['id'])){
			$result = Contact_Controller::get_contact($params['id']);
		    if ($result["success"] == true){
			    return $result["contact"];
		    } else {
			    return new WP_Error("get_contact_error", $result["message"], array('status', 400));
		    }
	    } else {
	    	return new WP_Error("get_contact_error", "Please provide a valid id", array('status', 400));
	    }
    }

	/**
	 * Get Contacts assigned to a user
	 * @param WP_REST_Request $request
	 * @access public
	 * @since 0.1
	 * @return array|WP_Error return the user's contacts
	 */
    public function get_user_contacts(WP_REST_Request $request){
	    $params = $request->get_params();
	    if (isset($params['user'])){
	    	$result = Contact_Controller::get_user_contacts($params['user']);
	    	if ($result["success"] == true){
			    return $result["contacts"];
		    } else {
			    return new WP_Error("get_user_contact_error", $result["message"], array('status', 400));
		    }
	    }
    }

	/**
	 * Get Contact assigned to a user's team
	 * @param WP_REST_Request $request
	 * @access public
	 * @since 0.1
	 * @return array|WP_Error return the user's team's contacts
	 */
    public function get_team_contacts(WP_REST_Request $request){
    	$params = $request->get_params();
	    $current_user_id = wp_get_current_user()->ID;
    	if (isset($params['user'])){
	        if ($params['user'] != $current_user_id){
//	        	@todo check capabilities
	        }
    		$result = Contact_Controller::get_team_contacts($params['user']);
		    if ($result["success"] == true){
			    return $result;
		    } else {
			    return new WP_Error("get_team_contacts_error", $result["message"], array('status', 400));
		    }
	    }
    }
}
