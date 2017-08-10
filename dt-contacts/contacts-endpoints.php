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
            ]
        );
        register_rest_route(
            $this->namespace, '/contact/(?P<id>\d+)', [
            "methods" => "POST",
            "callback" => [$this, 'update_contact'],
            ]
        );
        register_rest_route(
            $this->namespace, '/contact/(?P<id>\d+)/details', [
                "methods" => "POST",
                "callback" => [$this, 'add_contact_details'],
            ]
        );
        register_rest_route(
            $this->namespace, '/contact/(?P<id>\d+)/details_update', [
                "methods" => "POST",
                "callback" => [$this, 'update_contact_details'],
            ]
        );
        register_rest_route(
            $this->namespace, '/contact/(?P<id>\d+)/details', [
                "methods" => "DELETE",
                "callback" => [$this, 'delete_contact_details'],
            ]
        );
        register_rest_route(
            $this->namespace, '/user/(?P<user_id>\d+)/contacts', [
            "methods" => "GET",
            "callback" => [$this, 'get_user_contacts'],
            ]
        );
        register_rest_route(
            $this->namespace, '/contacts', [
            "methods" => "GET",
            "callback" => [$this, 'get_viewable_contacts'],
            ]
        );
        register_rest_route(
            $this->namespace, '/user/(?P<user_id>\d+)/team/contacts', [
            "methods" => "GET",
            "callback" => [$this, 'get_team_contacts'],
            ]
        );
        register_rest_route(
            $this->namespace, '/contact/(?P<id>\d+)/quick_action_button', [
            "methods" => "POST",
            "callback" => [$this, 'quick_action_button'],
            ]
        );
        register_rest_route(
            $this->namespace, '/contact/(?P<id>\d+)/comment', [
                "methods" => "POST",
                "callback" => [$this, 'post_comment']
            ]
        );
        register_rest_route(
            $this->namespace, '/contact/(?P<id>\d+)/comments', [
                "methods" => "GET",
                "callback" => [$this, 'get_comments']
            ]
        );
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
     * @return array|WP_Error The contact on success
     */
    public function get_contact( WP_REST_Request $request ){
        $params = $request->get_params();
        if (isset( $params['id'] )){
            $result = Disciple_Tools_Contacts::get_contact( $params['id'], true );
            return $result; // Could be permission WP_Error
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
     * @return WP_REST_Response|WP_Error Contact_id on success
     */
    public function update_contact( WP_REST_Request $request ){
        $params = $request->get_params();
        $body = $request->get_json_params();
        if (isset( $params['id'] )){
            $result = Disciple_Tools_Contacts::update_contact( $params['id'], $body, true );
            if ( is_wp_error( $result ) ){
                return $result;
            } else {
                return new WP_REST_Response( $result );
            }
        } else {
            return new WP_Error( "update_contact", "Missing a valid contact id", ['status' => 400] );
        }
    }

    private function add_related_info_to_contacts( WP_Query $contacts ): array {
        p2p_type( 'contacts_to_locations' )->each_connected( $contacts, array(), 'locations' );
        p2p_type( 'contacts_to_groups' )->each_connected( $contacts, array(), 'groups' );
        $rv = array();
        foreach ($contacts->posts as $contact) {
            $meta_fields = get_post_custom( $contact->ID );
            $contact_array = $contact->to_array();
            $contact_array['permalink'] = get_post_permalink( $contact->ID );
            $contact_array['overall_status'] = get_post_meta( $contact->ID, 'overall_status', true );
            $contact_array['locations'] = array();
            foreach ( $contact->locations as $location ) {
                $contact_array['locations'][] = $location->post_title;
            }
            $contact_array['groups'] = array();
            foreach ( $contact->groups as $group ) {
                $contact_array['groups'][] = $group->post_title;
            }
            $contact_array['phone_numbers'] = array();
            foreach ( $meta_fields as $meta_key => $meta_value ) {
                if ( strpos( $meta_key, "contact_phone" ) === 0 && strpos( $meta_key, "details" ) === false) {
                    $contact_array['phone_numbers'] = array_merge( $contact_array['phone_numbers'], $meta_value );
                } elseif ( strpos( $meta_key, "milestone_" ) === 0 ) {
                    $contact_array[$meta_key] = $meta_value[0] === "yes";
                } elseif ( $meta_key === "seeker_path" ) {
                    $contact_array[$meta_key] = $meta_value[0];
                } elseif ( $meta_key == "assigned_to" ) {
                    $type_and_id = explode( '-', $meta_value[0] );
                    $contact_array['assigned_to'] = array(
                        'type' => $type_and_id[0],
                        'id' => (int) $type_and_id[1],
                    );
                    if ($type_and_id[0] === 'user') {
                        $contact_array['assigned_to']['name'] = get_user_by( 'id', (int) $type_and_id[1] )->display_name;
                        $contact_array['assigned_to']['user_login'] = get_user_by( 'id', (int) $type_and_id[1] )->user_login;
                    }
                }
            }
            $rv[] = $contact_array;
        }
        return $rv;
    }


    public function add_contact_details( WP_REST_Request $request ){
        $params = $request->get_params();
        $body = $request->get_json_params();
        if (isset( $params['id'] )){
            reset( $body );
            $field = key( $body );
            $result = Disciple_Tools_Contacts::add_contact_detail( $params['id'], $field, $body[$field], true );
            if ( is_wp_error( $result ) ){
                return $result;
            } else {
                return new WP_REST_Response( $result );
            }
        } else {
            return new WP_Error( "add_contact_details", "Missing a valid contact id", ['status' => 400] );
        }
    }
    public function update_contact_details( WP_REST_Request $request ){
        $params = $request->get_params();
        $body = $request->get_json_params();
        if (isset( $params['id'] )){
            $field_key = $body["key"];
            $values = $body["values"];

            $result = Disciple_Tools_Contacts::update_contact_details( $params['id'], $field_key, $values, true );
            if ( is_wp_error( $result ) ){
                return $result;
            } else {
                return new WP_REST_Response( $result );
            }
        } else {
            return new WP_Error( "add_contact_details", "Missing a valid contact id", ['status' => 400] );
        }
    }
    public function delete_contact_details( WP_REST_Request $request ){
        $params = $request->get_params();
        $body = $request->get_json_params();
        if (isset( $params['id'] )){
            $field_key = $body["key"];
            $value = $body["value"];

            $result = Disciple_Tools_Contacts::delete_contact_details( $params['id'], $field_key, $value, true );
            if ( is_wp_error( $result ) ){
                return $result;
            } else {
                return new WP_REST_Response( $result );
            }
        } else {
            return new WP_Error( "add_contact_details", "Missing a valid contact id", ['status' => 400] );
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
            $contacts = Disciple_Tools_Contacts::get_user_contacts( (int) $params['user_id'], true );
            if (is_wp_error( $contacts )) {
                return $contacts;
            }
            return $this->add_related_info_to_contacts( $contacts );
        } else {
            return new WP_Error( "get_user_contacts", "Missing a valid user id", ['status' => 400] );
        }
    }

    /**
     * Get Contacts viewable by a user
     *
     * @param  WP_REST_Request $request
     * @access public
     * @since  0.1
     * @return array|WP_Error return the user's contacts
     */
    public function get_viewable_contacts( WP_REST_Request $request ) {
        $contacts = Disciple_Tools_Contacts::get_viewable_contacts( true );
        if (is_wp_error( $contacts )) {
            return $contacts;
        }
        return $this->add_related_info_to_contacts( $contacts );
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
            $result = Disciple_Tools_Contacts::get_team_contacts( $params['user_id'], true );
            return $result; // Could be permission WP_Error
        }  else {
            return new WP_Error( "get_team_contacts", "Missing a valid user id", ['status' => 400] );
        }
    }


    public function quick_action_button( WP_REST_Request $request ){
        $params = $request->get_params();
        $body = $request->get_json_params();
        if (isset( $params['id'] )){
            $result = Disciple_Tools_Contacts::quick_action_button( $params['id'], $body, true );
            if ( is_wp_error( $result ) ){
                return $result;
            } else {
                return new WP_REST_Response( ["seeker_path"=>$result] );
            }
        } else {
            return new WP_Error( "quick_action_button", "Missing a valid contact id", ['status' => 400] );
        }
    }

    public function post_comment( WP_REST_Request $request ){
        $params = $request->get_params();
        $body = $request->get_json_params();
        if (isset( $params['id'] )){
            $result = Disciple_Tools_Contacts::add_comment( $params['id'], $body["comment"], true );

            if ( is_wp_error( $result ) ){
                return $result;
            } else {
                return new WP_REST_Response( ["comment_id"=>$result] );
            }
        } else {
            return new WP_Error( "quick_action_button", "Missing a valid contact id", ['status' => 400] );
        }

    }
    public function get_comments( WP_REST_Request $request ){
        $params = $request->get_params();
        if (isset( $params['id'] )){
            $result = Disciple_Tools_Contacts::get_comments( $params['id'], true );

            if ( is_wp_error( $result ) ){
                return $result;
            } else {
                return new WP_REST_Response( $result );
            }
        } else {
            return new WP_Error( "quick_action_button", "Missing a valid contact id", ['status' => 400] );
        }

    }
}
