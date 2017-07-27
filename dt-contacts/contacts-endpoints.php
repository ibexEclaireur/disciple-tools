<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

/**
 * Class Disciple_Tools_Contacts_Endpoints
 *
 * Expose some public rest api endpoints to outside sources
 */

class Disciple_Tools_Contacts_Endpoints
{

    /**
     * @var object Public_Hooks instance variable
     */
    private static $_instance = null;

    /**
     * Public_Hooks. Ensures only one instance of Public_Hooks is loaded or can be loaded.
     *
     * @return Disciple_Tools_Contacts_Endpoints instance
     */
    public static function instance () {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    /**
     * The Public_Hooks rest api variables
     */
    private $version = 1;
    private $context = "dt-hooks";
    private $namespace;
    private $contacts_instance;
    private $api_keys_controller;

    public function __construct()
    {
        $this->namespace = $this->context . "/v" . intval( $this->version );
        add_action( 'rest_api_init', [$this,  'add_api_routes'] );

        require_once( 'contacts.php' );
        $this->contacts_instance = new Disciple_Tools_Contacts;

        $this->api_keys_controller = Disciple_Tools_Api_Keys::instance();
    }

    /**
     * Add the api routes
     */
    public function add_api_routes(){
        register_rest_route(
            $this->namespace, '/dt-public/create-contact', [
            'methods' => 'POST',
            'callback' => [$this, 'public_create_contact']
            ]
        );
        register_rest_route(
            $this->namespace, '/contact/create', [
            "methods" => "POST",
            "callback" => [$this, 'create_contact'],
            ]
        );
        register_rest_route(
            $this->namespace, '/contact/(?P<id>\d+)', [
            "methods" => "GET",
            "callback" => [$this, 'get_contact'],
            "permission_callback" => function () {
                return current_user_can( 'read_contact' );
            }
            ]
        );
        register_rest_route(
            $this->namespace, '/contact/(?P<id>\d+)', [
            "methods" => "POST",
            "callback" => [$this, 'update_contact'],
            "permission_callback" => function(){
                return current_user_can( "edit_contacts" );
            }
            ]
        );
        register_rest_route(
            $this->namespace, '/user/(?P<user_id>\d+)/contacts', [
            "methods" => "GET",
            "callback" => [$this, 'get_user_contacts'],
            ]
        );
        register_rest_route(
            $this->namespace, '/user/(?P<user_id>\d+)/team/contacts', [
            "methods" => "GET",
            "callback" => [$this, 'get_team_contacts'],
            "permission_callback" => function () {
                return current_user_can( 'edit_contacts' );
            }
            ]
        );
    }


    /**
     * Check if the user id slug in the same as the currently logged in user
     *
     * @param  $user_id
     * @access public
     * @since  0.1
     * @return bool
     */
    public function is_id_of_user_logged_in( $user_id ){
        $current_user = wp_get_current_user();
        if(isset( $current_user->ID )){
            return $current_user->ID == $user_id;
        }
        return false;
    }


    /**
     * Check to see if the client_id and the client_token are set and see if they are valid
     *
     * @param  $query_params
       * @access private
     * @since  0.1
     * @return bool
     */
    private function check_api_token( $query_params ){
        if (isset( $query_params['client_id'] ) && isset( $query_params['client_token'] )){
            return $this->api_keys_controller->check_api_key( $query_params['client_id'], $query_params['client_token'] );
        }
    }


    /**
     * Create a contact from the PUBLIC api.
     *
     * @param  WP_REST_Request $request as application/json
     * @access public
     * @since  0.1
     * @return array|WP_Error The new contact Id on success, an error on failure
     */
    public function public_create_contact( WP_REST_Request $request ){
        $query_params = $request->get_query_params();
        if($this->check_api_token( $query_params )){
            $fields = $request->get_json_params();
            $result = Disciple_Tools_Contacts::create_contact( $fields, true );
            return $result; // Could be permission WP_Error
        } else {
            return new WP_Error(
                "contact_creation_error",
                "Invalid or missing client_id or client_token", ['status' => 401]
            );
        }
    }


    /**
     * Create a contact
     *
     * @param  WP_REST_Request $request
      * @access public
     * @since  0.1
     * @return string|WP_Error The contact on success
     */
    public function create_contact( WP_REST_Request $request ){
        $fields = $request->get_json_params();
        $result = Disciple_Tools_Contacts::create_contact( $fields, true );
        return $result; // Could be permission WP_Error
    }

    /**
     * Get a single contact by ID
     *
     * @param  WP_REST_Request $request
      * @access public
     * @since  0.1
     * @return string|WP_Error The contact on success
     */
    public function get_contact( WP_REST_Request $request ){
        $params = $request->get_params();
        if (isset( $params['id'] )){
            //@todo restrict to only get contact's the user has access to
            $result = Disciple_Tools_Contacts::get_contact( $params['id'] );
            if ($result["success"] == true){
                return $result["contact"];
            } else {
                return new WP_Error( "get_contact_error", $result["message"], ['status' => 400] );
            }
        } else {
            return new WP_Error( "get_contact_error", "Please provide a valid id", ['status' => 400] );
        }
    }

    /**
     * Update a single contact by ID
     *
     * @param  WP_REST_Request $request
      * @access public
     * @since  0.1
     * @return string|WP_Error Contact_id on success
     */
    public function update_contact( WP_REST_Request $request ){
        $params = $request->get_params();
        $body = $request->get_json_params();
        if (isset( $params['id'] )){
            $result = Disciple_Tools_Contacts::update_contact( $params['id'], $body );
            if ($result["success"] == true){
                return $result["contact_id"];
            } else {
                return new WP_Error( "update_contact", $result["message"], ['status' => 400] );
            }
        } else {
            return new WP_Error( "update_contact", "Missing a valid contact id", ['status' => 400] );
        }
    }


    /**
     * Get Contacts assigned to a user
     *
     * @param  WP_REST_Request $request
     * @access public
     * @since  0.1
     * @return array|WP_Error return the user's contacts
     */
    public function get_user_contacts( WP_REST_Request $request ){
        $params = $request->get_params();
        if (isset( $params['user_id'] )){
            $contacts = Disciple_Tools_Contacts::get_user_contacts( (int) $params['user_id'] );
            if (is_wp_error( $contacts )) {
                return $contacts;
            }
            $rv = array();
            foreach ($contacts as $contact) {
                $contact_array = $contact->to_array();
                $contact_array['permalink'] = get_post_permalink( $contact->ID );
                $rv[] = $contact_array;
            }
            return $rv;
        } else {
            return new WP_Error( "get_user_contacts", "Missing a valid user id", ['status' => 400] );
        }
    }

    /**
     * Get Contact assigned to a user's team
     *
     * @param  WP_REST_Request $request
     * @access public
     * @since  0.1
     * @return array|WP_Error return the user's team's contacts
     */
    public function get_team_contacts( WP_REST_Request $request ){
        $params = $request->get_params();
        if (isset( $params['user_id'] )){
            if (!$this->is_id_of_user_logged_in( $params["user_id"] )){
                if (!current_user_can( "edit_team_contacts" )){
                    return new WP_Error( "get_team_contacts_error", "You do nat have access to these contacts", ['status' => 401] );
                }
            }
            $result = Disciple_Tools_Contacts::get_team_contacts( $params['user_id'] );
            if ($result["success"] == true){
                return $result;
            } else {
                return new WP_Error( "get_team_contacts_error", $result["message"], ['status' => 400] );
            }
        }  else {
            return new WP_Error( "get_team_contacts", "Missing a valid user id", ['status' => 400] );
        }
    }
}
