<?php
/**
 * Custom endpoints file
 */


class Disciple_Tools_Users_Endpoints
{

    private $version = 1;
    private $context = "dt";
    private $namespace;

    public function __construct()
    {
        $this->namespace = $this->context . "/v" . intval( $this->version );
        add_action( 'rest_api_init', [ $this, 'add_api_routes' ] );
    }

    public function add_api_routes(){
        register_rest_route(
            $this->namespace, '/users', [
            'methods' => 'GET',
            'callback' => [$this, 'get_users']
            ]
        );
    }

    public function get_users( WP_REST_Request $request ){
        $params = $request->get_params();
    //        @todo check permissions
        $users = Disciple_Tools_Users::get_assignable_users();
        return $users;
    }

}
