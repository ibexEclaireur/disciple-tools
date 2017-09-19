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

class Disciple_Tools_Groups_Endpoints {

    private static $_instance = null;

    public static function instance () {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private $version = 1;
    private $context = "dt-hooks";
    private $namespace;
    private $contacts_instance;
    private $api_keys_controller;

    public function __construct()
    {
        $this->namespace = $this->context . "/v" . intval( $this->version );
        add_action( 'rest_api_init', [$this,  'add_api_routes'] );

        require_once( 'groups.php' );
        $this->groups_instance = new Disciple_Tools_Groups;

        $this->api_keys_controller = Disciple_Tools_Api_Keys::instance();
    }

    public function add_api_routes() {
        register_rest_route(
            $this->namespace, '/groups', [
                "methods" => "GET",
                "callback" => [$this, 'get_viewable_groups'],
            ]
        );
        register_rest_route(
            $this->namespace, '/groups-compact', [
                'methods' => 'GET',
                'callback' => [$this, 'get_groups_compact']
            ]
        );
        register_rest_route(
            $this->namespace, '/group/(?P<id>\d+)', [
                'methods' => 'POST',
                'callback' => [$this, 'update_group']
            ]
        );
        register_rest_route(
            $this->namespace, '/group/(?P<id>\d+)', [
                'methods' => 'GET',
                'callback' => [$this, 'get_group']
            ]
        );
        register_rest_route(
            $this->namespace, '/group/(?P<id>\d+)/details', [
                "methods" => "POST",
                "callback" => [$this, 'add_item_to_field'],
            ]
        );
        register_rest_route(
            $this->namespace, '/group/(?P<id>\d+)/details', [
                "methods" => "DELETE",
                "callback" => [$this, 'remove_item_from_field'],
            ]
        );
        register_rest_route(
            $this->namespace, '/group/(?P<id>\d+)/comment', [
                "methods" => "POST",
                "callback" => [$this, 'post_comment']
            ]
        );
        register_rest_route(
            $this->namespace, '/group/(?P<id>\d+)/comments', [
                "methods" => "GET",
                "callback" => [$this, 'get_comments']
            ]
        );
        register_rest_route(
            $this->namespace, '/group/(?P<id>\d+)/activity', [
                "methods" => "GET",
                "callback" => [$this, 'get_activity']
            ]
        );
        register_rest_route(
            $this->namespace, '/group/(?P<id>\d+)/shared-with', [
                "methods" => "GET",
                "callback" => [$this, 'shared_with']
            ]
        );

        register_rest_route(
            $this->namespace, '/group/(?P<id>\d+)/remove-shared', [
                "methods" => "POST",
                "callback" => [$this, 'remove_shared']
            ]
        );

        register_rest_route(
            $this->namespace, '/group/(?P<id>\d+)/add-shared', [
                "methods" => "POST",
                "callback" => [$this, 'add_shared']
            ]
        );
    }

    public function get_viewable_groups( WP_REST_Request $request ) {
        $groups = Disciple_Tools_Groups::get_viewable_groups();
        if (is_wp_error( $groups )) {
            return $groups;
        }
        return $this->add_related_info_to_groups( $groups );
    }


    private function add_related_info_to_groups( array $groups ): array {
        p2p_type( 'groups_to_locations' )->each_connected( $groups, array(), 'locations' );
        p2p_type( 'contacts_to_groups' )->each_connected( $groups, array(), 'contacts' );
        $rv = array();
        foreach ($groups as $group) {
            $meta_fields = get_post_custom( $group->ID );
            $group_array = $group->to_array();
            unset( $group_array['contacts'] );
            $group_array['permalink'] = get_post_permalink( $group->ID );
            $group_array['locations'] = array();
            foreach ( $group->locations as $location ) {
                $group_array['locations'][] = $location->post_title;
            }
            $group_array['leaders'] = array();
            $group_array['member_count'] = 0;
            foreach ( $group->contacts as $contact ) {
                if ((int) p2p_get_meta( $contact->p2p_id, 'leader' )) {
                    $group_array['leaders'][] = array(
                        'post_title' => $contact->post_title,
                        'permalink' => get_permalink( $contact->ID ),
                    );
                }
                $group_array['member_count']++;
            }
            foreach ( $meta_fields as $meta_key => $meta_value ) {
                if ( $meta_key == 'group_status' ) {
                    $group_array[$meta_key] = $meta_value[0];
                } else if ( $meta_key == 'last_modified' ) {
                    $group_array[$meta_key] = (int) $meta_value[0];
                }
            }
            $rv[] = $group_array;
        }
        return $rv;
    }


    public function get_groups_compact( WP_REST_Request $request ){
        $params = $request->get_params();
        $search = "";
        if (isset( $params['s'] )){
            $search = $params['s'];
        }
        $groups = Disciple_Tools_Groups::get_groups_compact( $search );
        return $groups;
    }

    public function update_group( WP_REST_Request $request ){
        $params = $request->get_params();
        $body = $request->get_json_params();
        if (isset( $params['id'] )){
            $result = Disciple_Tools_Groups::update_group( $params['id'], $body, true );
            return $result;
        } else {
            return new WP_Error( "update_contact", "Missing a valid contact id", ['status' => 400] );
        }
    }

    /**
     * Get a single group by ID
     *
     * @param  WP_REST_Request $request
     * @access public
     * @since  0.1
     * @return array|WP_Error The group on success
     */
    public function get_group( WP_REST_Request $request ){
        $params = $request->get_params();
        if (isset( $params['id'] )){
            $result = Disciple_Tools_groups::get_group( $params['id'], true );
            return $result; // Could be permission WP_Error
        } else {
            return new WP_Error( "get_group_error", "Please provide a valid id", ['status' => 400] );
        }
    }

    public function add_item_to_field( WP_REST_Request $request ){
        $params = $request->get_params();
        $body = $request->get_json_params();
        if (isset( $params['id'] )){
            reset( $body );
            $field = key( $body );
            $result = Disciple_Tools_groups::add_item_to_field( $params['id'], $field, $body[$field], true );
            return $result;
        } else {
            return new WP_Error( "add_group_details", "Missing a valid group id", ['status' => 400] );
        }
    }

    public function remove_item_from_field( WP_REST_Request $request )
    {
        $params = $request->get_params();
        $body = $request->get_json_params();
        if ( isset( $params['id'] ) ) {
            $field_key = $body["key"];
            $value = $body["value"];

            $result = Disciple_Tools_groups::remove_item_from_field( $params['id'], $field_key, $value, true );
            if ( is_wp_error( $result ) ) {
                return $result;
            } else if ( $result == 0 ) {
                return new WP_Error( "delete_group_details", "Could not update group", [ 'status' => 400 ] );
            } else {
                return new WP_REST_Response( $result );
            }
        } else {
            return new WP_Error( "add_group_details", "Missing a valid group id", [ 'status' => 400 ] );
        }
    }

    public function post_comment( WP_REST_Request $request ){
        $params = $request->get_params();
        $body = $request->get_json_params();
        if (isset( $params['id'] )){
            $result = Disciple_Tools_Groups::add_comment( $params['id'], $body["comment"] );

            if ( is_wp_error( $result ) ){
                return $result;
            } else {
                $comment = get_comment( $result );
                return new WP_REST_Response( ["comment_id"=>$result, "comment"=>$comment] );
            }
        } else {
            return new WP_Error( "post_comment", "Missing a valid group id", ['status' => 400] );
        }
    }

    public function get_comments( WP_REST_Request $request ){
        $params = $request->get_params();
        if (isset( $params['id'] )){
            return Disciple_Tools_Groups::get_comments( $params['id'] );
        } else {
            return new WP_Error( "get_comments", "Missing a valid group id", ['status' => 400] );
        }
    }
    public function get_activity( WP_REST_Request $request ){
        $params = $request->get_params();
        if (isset( $params['id'] )){
            return Disciple_Tools_Groups::get_activity( $params['id'] );
        } else {
            return new WP_Error( "get_activity", "Missing a valid group id", ['status' => 400] );
        }
    }

    public function shared_with( WP_REST_Request $request ){
        $params = $request->get_params();
        if (isset( $params['id'] )){
            $result = Disciple_Tools_Groups::get_shared_with_on_group( $params['id'] );

            if ( is_wp_error( $result ) ){
                return $result;
            } else {
                return new WP_REST_Response( $result );
            }
        } else {
            return new WP_Error( 'shared_with', "Missing a valid group id", ['status' => 400] );
        }
    }

    public function remove_shared( WP_REST_Request $request ){
        $params = $request->get_params();
        if (isset( $params['id'] )){
            $result = Disciple_Tools_Groups::remove_shared_on_group( $params['id'], $params['user_id'] );

            if ( is_wp_error( $result ) ){
                return $result;
            } else {
                return new WP_REST_Response( $result );
            }
        } else {
            return new WP_Error( 'remove_shared', "Missing a valid group id", ['status' => 400] );
        }
    }

    public function add_shared( WP_REST_Request $request ){
        $params = $request->get_params();
        if ( isset( $params['id'] )){
            $result = Disciple_Tools_Groups::add_shared_on_group( $params['id'], $params['user_id'] );

            if ( is_wp_error( $result ) ){
                return $result;
            } else {
                return new WP_REST_Response( $result );
            }
        } else {
            return new WP_Error( 'add_shared', "Missing a valid group id", ['status' => 400] );
        }
    }

}
