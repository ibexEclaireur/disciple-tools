<?php
/**
 * @package  Disciple_Tools
 * @category Plugin
 * @author   Chasm.Solutions & Kingdom.Training
 * @since    0.1
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

/**
 * Class Disciple_Tools_Contacts
 *
 * Functions for creating, finding, updating or deleting contacts
 */

class Disciple_Tools_Contacts extends Disciple_Tools_Posts
{
    public static $contact_fields;
    public static $channel_list;
    public static $address_types;

    public function __construct(){
        add_action(
            'init', function(){
                self::$contact_fields = Disciple_Tools_Contact_Post_Type::instance()->get_custom_fields_settings();
                self::$channel_list =  Disciple_Tools_Contact_Post_Type::instance()->get_channels_list();
                self::$address_types = dt_address_metabox()->get_address_type_list( "contacts" );
            }
        );
        parent::__construct();
    }

    /**
     * Helper method for creating a WP_Query with pagination and ordering
     * separated into a separate argument for validation.
     *
     * These two statements are equivalent in this example:
     *
     * $query = self::query_with_pagination( [ "post_type" => "contacts" ], [ "orderby" => "ID" ] );
     * // equivalent to:
     * $query = new WP_Query( [ "post_type" => "contacts", "orderby" => "ID" ] );
     *
     * The second argument, $query_pagination_args, may only contain keys
     * related to ordering and pagination, if it doesn't, this method will
     * return a WP_Error instance. This is useful in case you want to allow a
     * caller to modify pagination and ordering, but not anything else, in
     * order to keep permission checking valid. If $query_pagination_args is
     * specified with at least one value, then all pagination-related keys in
     * the first argument are ignored.
     *
     * @param array $query_args
     * @param array $query_pagination_args
     * @param access private
     * @return WP_Query | WP_Error
     */
    private static function query_with_pagination( array $query_args, array $query_pagination_args ) {
        $allowed_keys = array(
            'order', 'orderby', 'nopaging', 'posts_per_page', 'posts_per_archive_page', 'offset',
            'paged', 'page', 'ignore_sticky_posts',
        );
        $error = new WP_Error();
        foreach ($query_pagination_args as $key => $value) {
            if (! in_array( $key, $allowed_keys ) ) {
                $error->add( __FUNCTION__, __( "Key $key was an unexpected pagination key" ) );
            }
        }
        if ( count( $error->errors ) ) {
            return $error;
        }
        if ( count( $query_pagination_args ) ) {
            foreach ($allowed_keys as $pagination_key) {
                unset( $query_args[$pagination_key] );
            }
        }
        return new WP_Query( array_merge( $query_args, $query_pagination_args ) );
    }


    public static function get_contact_fields(){
        return self::$contact_fields;
    }
    public static function get_channel_list(){
        return self::$channel_list;
    }

    /**
     * Create a new Contact
     *
     * @param  array $fields, the new contact's data
     * @param  bool $check_permissions
     * @access private
     * @since  0.1
     * @return int | WP_Error
     */
    public static function create_contact( array $fields = [], bool $check_permissions = true ) {
        //@TODO search for duplicates
        //@TODO set defaults

        if ($check_permissions && ! current_user_can( 'publish_contacts' )) {
            return new WP_Error( __FUNCTION__, __( "You may not publish a contact" ), ['status' => 403] );
        }

        //required fields
        if (!isset( $fields["title"] )){
            return new WP_Error( __FUNCTION__, __( "Contact needs a title" ), ['fields' => $fields] );
        }
        $bad_fields = self::check_for_invalid_fields( $fields );
        if (!empty( $bad_fields )){
            return new WP_Error( __FUNCTION__, __( "These fields do not exist" ), ['bad_fields' => $bad_fields] );
        }

        $post = [
            "post_title" => $fields['title'],
            'post_type' => "contacts",
            "post_status" => 'publish',
            "meta_input" => $fields
        ];

        $post_id = wp_insert_post( $post );
        return $post_id;
    }

    /**
     * Make sure there are no extra or misspelled fields
     * Make sure the field values are the correct format
     *
     * @param  $fields, the contact meta fields
     * @param  $post_id, the id of the contact
     * @access private
     * @since  0.1
     * @return array
     */
    private static function check_for_invalid_fields( $fields, int $post_id = null ){
        $bad_fields = [];
        $contact_fields = Disciple_Tools_Contact_Post_Type::instance()->get_custom_fields_settings( isset( $post_id ), $post_id );
        $contact_fields['title'] = "";
        foreach($fields as $field => $value){
            if (!isset( $contact_fields[$field] )){
                $bad_fields[] = $field;
            }
        }
        return $bad_fields;
    }

    /**
     * Update an existing Contact
     *
     * @param  int $contact_id, the post id for the contact
     * @param  array $fields, the meta fields
     * @param  bool $check_permissions
     * @access public
     * @since  0.1
     * @return int | WP_Error of contact ID
     */
    public static function update_contact( int $contact_id, array $fields, bool $check_permissions = true ){

        if ($check_permissions && ! self::can_update( 'contacts', $contact_id )) {
            return new WP_Error( __FUNCTION__, __( "You do not have permission for this" ), ['status' => 403] );
        }

        $post = get_post( $contact_id );
        if (isset( $fields['id'] )){
            unset( $fields['id'] );
        }

        if (!$post){
            return new WP_Error( __FUNCTION__, __( "Contact does not exist" ) );
        }
        $bad_fields = self::check_for_invalid_fields( $fields, $contact_id );
        if (!empty( $bad_fields )){
            return new WP_Error( __FUNCTION__, __( "These fields do not exist" ), ['bad_fields' => $bad_fields] );
        }

        if ( isset( $fields['title'] ) ){
            wp_update_post( ['ID'=>$contact_id, 'post_title'=>$fields['title']] );
        }

        if (current_user_can( "assign_any_contact" )){
            if (isset( $fields["assigned_to"] )){
                $fields["overall_status"] = 'assigned';
            }
        }

        foreach($fields as $field_id => $value){
            update_post_meta( $contact_id, $field_id, $value );
        }
        return self::get_contact( $contact_id, true );
    }


    public static function add_location_to_contact( $contact_id, $location_id ){
        return p2p_type( 'contacts_to_locations' )->connect(
            $location_id, $contact_id,
            array('date' => current_time( 'mysql' ) )
        );
    }

    public static function add_group_to_contact( $contact_id, $group_id ){
        return p2p_type( 'contacts_to_groups' )->connect(
            $group_id, $contact_id,
            array('date' => current_time( 'mysql' ) )
        );
    }
    public static function add_baptized_by_to_contact( $contact_id, $baptized_by ){
        return p2p_type( 'baptizer_to_baptized' )->connect(
            $contact_id, $baptized_by,
            array('date' => current_time( 'mysql' ) )
        );
    }
    public static function add_baptized_to_contact( $contact_id, $baptized ){
        return p2p_type( 'baptizer_to_baptized' )->connect(
            $baptized, $contact_id,
            array('date' => current_time( 'mysql' ) )
        );
    }
    public static function add_coached_by_to_contact( $contact_id, $coached_by ){
        return p2p_type( 'contacts_to_contacts' )->connect(
            $contact_id, $coached_by,
            array('date' => current_time( 'mysql' ) )
        );
    }
    public static function add_coaching_to_contact( $contact_id, $coaching ){
        return p2p_type( 'contacts_to_contacts' )->connect(
            $coaching, $contact_id,
            array('date' => current_time( 'mysql' ) )
        );
    }

    public static function remove_location_from_contact( $contact_id, $location_id ){
        return p2p_type( 'contacts_to_locations' )->disconnect( $location_id, $contact_id );
    }
    public static function remove_group_from_contact( $contact_id, $group_id ){
        return p2p_type( 'contacts_to_groups' )->disconnect( $group_id, $contact_id );
    }
    public static function remove_baptized_by_from_contact( $contact_id, $baptized_by ){
        return p2p_type( 'baptizer_to_baptized' )->disconnect( $contact_id, $baptized_by );
    }
    public static function remove_baptized_from_contact( $contact_id, $baptized ){
        return p2p_type( 'baptizer_to_baptized' )->disconnect( $baptized, $contact_id );
    }
    public static function remove_coached_by_from_contact( $contact_id, $coached_by ){
        return p2p_type( 'contacts_to_contacts' )->disconnect( $contact_id, $coached_by );
    }
    public static function remove_coaching_from_contact( $contact_id, $coaching ){
        return p2p_type( 'contacts_to_contacts' )->disconnect( $coaching, $contact_id );
    }


    public static function add_contact_detail( int $contact_id, string $key, string $value, bool $check_permissions ){
        if ($check_permissions && ! self::can_update( 'contacts', $contact_id )) {
            return new WP_Error( __FUNCTION__, __( "You do not have permission for this" ), ['status' => 403] );
        }
        if (strpos( $key, "new-" ) === 0 ){
            $type = explode( '-', $key )[1];

            if ($key === "new-address") {
                $new_meta_key = dt_address_metabox()->create_channel_metakey( "address" );
            } else if (isset( self::$channel_list[$type] )){
                //check if this is a new field and is in the channel list
                $new_meta_key = Disciple_Tools_Contact_Post_Type::instance()->create_channel_metakey( $type, "contact" );
            }
            update_post_meta( $contact_id, $new_meta_key, $value );
            $details = ["verified"=>false];
            update_post_meta( $contact_id, $new_meta_key . "_details", $details );
            return $new_meta_key;
        }
        $connect = null;
        if ($key === "locations"){
            $connect = self::add_location_to_contact( $contact_id, $value );
        } else if ($key === "groups"){
            $connect =  self::add_group_to_contact( $contact_id, $value );
        } else if ($key === "baptized_by"){
            $connect = self::add_baptized_by_to_contact( $contact_id, $value );
        } else if ($key === "baptized"){
            $connect = self::add_baptized_to_contact( $contact_id, $value );
        } else if ($key === "coached_by"){
            $connect = self::add_coached_by_to_contact( $contact_id, $value );
        } else if ($key === "coaching"){
            $connect = self::add_coaching_to_contact( $contact_id, $value );
        }
        if (is_wp_error( $connect )){
            return $connect;
        }
        if ($connect){
            $connection = get_post( $value );
            $connection->permalink = get_permalink( $value );
            return $connection;
        }

        return new WP_Error( "add_contact_detail", "Field not recognized", ["status"=>400] );
    }



    public static function update_contact_details( int $contact_id, string $key, array $values, bool $check_permissions ){
        if ($check_permissions && ! self::can_update( 'contacts', $contact_id )) {
            return new WP_Error( __FUNCTION__, __( "You do not have permission for this" ), ['status' => 403] );
        }
        if ( ( strpos( $key, "contact_" ) === 0 || strpos( $key, "address_" ) === 0 ) &&
            strpos( $key, "_details" ) === false
        ){
            $details_key = $key . "_details";
            $details = get_post_meta( $contact_id, $details_key, true );
            $details = $details ?? [];
            foreach($values as $detail_key => $detail_value){
                $details[$detail_key] = $detail_value;
            }
            update_post_meta( $contact_id, $details_key, $details );
        }

        return $contact_id;
    }
    public static function delete_contact_details( int $contact_id, string $key, string $value, bool $check_permissions ){
        if ($check_permissions && ! self::can_update( 'contacts', $contact_id )) {
            return new WP_Error( __FUNCTION__, __( "You do not have permission for this" ), ['status' => 403] );
        }
        if ( $key === "locations" ){
            return self::remove_location_from_contact( $contact_id, $value );
        } else if( $key === "groups" )  {
            return self::remove_group_from_contact( $contact_id, $value );
        } else if ( $key === "baptized_by" ){
            return self::remove_baptized_by_from_contact( $contact_id, $value );
        } else if ( $key === "baptized" ){
            return self::remove_baptized_from_contact( $contact_id, $value );
        } else if ( $key === "coached_by" ){
            return self::remove_coached_by_from_contact( $contact_id, $value );
        } else if ( $key === "coaching" ){
            return self::remove_coaching_from_contact( $contact_id, $value );
        }

        return false;
    }

    /**
     * Get a single contact
     *
     * @param  int $contact_id , the contact post_id
     * @param  bool $check_permissions
     * @access public
     * @since  0.1
     * @return WP_Post| WP_Error, On success: the contact, else: the error message
     */
    public static function get_contact( int $contact_id, bool $check_permissions = true ){
        if ($check_permissions && ! self::can_view( 'contacts',  $contact_id )) {
            return new WP_Error( __FUNCTION__, __( "No permissions to read contact" ), ['status' => 403] );
        }

        $contact = get_post( $contact_id );
        if ($contact) {
            $fields = [];

            $locations = get_posts(
                [
                'connected_type' => 'contacts_to_locations',
                'connected_items' => $contact,
                'nopaging' => true,
                'suppress_filters' => false
                ]
            );
            foreach($locations as $l) {
                $l->permalink = get_permalink( $l->ID );
            }
            $fields[ "locations" ] = $locations;
            $groups = get_posts(
                [
                'connected_type' => 'contacts_to_groups',
                'connected_items' => $contact,
                'nopaging' => true,
                'suppress_filters' => false
                ]
            );
            foreach($groups as $g) {
                $g->permalink = get_permalink( $g->ID );
            }
            $fields[ "groups" ] = $groups;
            $baptized = get_posts(
                [
                'connected_type' => 'baptizer_to_baptized',
                'connected_direction' => 'to',
                'connected_items' => $contact,
                'nopaging' => true,
                'suppress_filters' => false
                ]
            );
            foreach($baptized as $b) {
                $b->fields = p2p_get_meta( $b->p2p_id );
                $b->permalink = get_permalink( $b->ID );
            }
            $fields[ "baptized" ] = $baptized;
            $baptized_by = get_posts(
                [
                'connected_type' => 'baptizer_to_baptized',
                'connected_direction' => 'from',
                'connected_items' => $contact,
                'nopaging' => true,
                'suppress_filters' => false
                ]
            );
            foreach($baptized_by as $b) {
                $b->fields = p2p_get_meta( $b->p2p_id );
                $b->permalink = get_permalink( $b->ID );
            }
            $fields[ "baptized_by" ] = $baptized_by;
            $coaching = get_posts(
                [
                'connected_type' => 'contacts_to_contacts',
                'connected_direction' => 'to',
                'connected_items' => $contact,
                'nopaging' => true,
                'suppress_filters' => false
                ]
            );
            foreach($coaching as $c) {
                $c->permalink = get_permalink( $c->ID );
            }
            $fields[ "coaching" ] = $coaching;
            $coached_by = get_posts(
                [
                'connected_type' => 'contacts_to_contacts',
                'connected_direction' => 'from',
                'connected_items' => $contact,
                'nopaging' => true,
                'suppress_filters' => false
                ]
            );
            foreach($coached_by as $c) {
                $c->permalink = get_permalink( $c->ID );
            }
            $fields[ "coached_by" ] = $coached_by;

            $meta_fields = get_post_custom( $contact_id );
            foreach( $meta_fields as $key => $value) {
                //if is contact details and is in a channel
                if ( strpos( $key, "contact_" ) === 0 && isset( self::$channel_list[explode( '_', $key )[1]] ) ) {
                    if ( strpos( $key, "details" ) === false ) {
                        $type = explode( '_', $key )[1];
                        $fields["contact_" . $type][] = self::format_contact_details( $meta_fields, $type, $key, $value[0] );
                    }
                } else if ( strpos( $key, "address" ) === 0){
                    if ( strpos( $key, "_details" ) === false ){

                        $details = [];
                        if ( isset( $meta_fields[$key.'_details'][0] )){
                            $details = unserialize( $meta_fields[$key.'_details'][0] );
                        }
                        $details["value"] = $value[0];
                        $details["key"] = $key;
                        if ( isset( $details["type"] )){
                            $details["type_label"] = self::$address_types[$details["type"]]["label"];
                        }
                        $fields[ "address" ][] = $details;
                    }
                } else if ( isset( self::$contact_fields[$key] ) && self::$contact_fields[$key]["type"] == "key_select" ) {
                    $label = self::$contact_fields[$key]["default"][$value[0]] ?? current( self::$contact_fields[$key]["default"] );
                    $fields[$key] = [ "key"=>$value[0], "label"=>$label ];
                } else if ($key === "assigned_to") {
                    if ($value){
                        $meta_array = explode( '-', $value[0] ); // Separate the type and id
                        $type = $meta_array[0]; // Build variables
                        if (isset( $meta_array[1] )){
                            $id = $meta_array[1];
                            if ( $type == 'user' ) {
                                $user = get_user_by( 'id', $id );
                                $fields[$key] = [ "id" => $id, "type" => $type, "display" => $user->display_name, "assigned-to" => $value[0] ];
                            }
                        }
                    }
                } else {
                    $fields[$key] = $value[0];
                }
            }

            $comments = get_comments( ['post_id'=>$contact_id] );
            $fields["comments"] = $comments;
            $contact->fields = $fields;

            return $contact;
        } else {
            return new WP_Error( __FUNCTION__, __( "No contact found with ID" ), [ 'contact_id' => $contact_id ] );
        }
    }

    public static function format_contact_details( $meta_fields, $type, $key, $value ){

        $details = [];
        if ( isset( $meta_fields[$key.'_details'][0] )){
            $details = unserialize( $meta_fields[$key.'_details'][0] );
        }
        $details["value"] = $value;
        $details["key"] = $key;
        if ( isset( $details["type"] )){
            $details["type_label"] = self::$channel_list[$type]["types"][$details["type"]]["label"];
        }
        return $details ;
    }

    public static function merge_contacts( $base_contact, $duplicate_contact ){

    }

    public static function get_activity( $contact_id ){
//        @todo permissions

        global $wpdb;

        $q = $wpdb->prepare(
            "SELECT
                 *
            FROM
                `$wpdb->dt_activity_log`
            WHERE
                `object_type` = 'contacts'
                AND `object_id` = %s",
            $contact_id
        );
        $activity = $wpdb->get_results( $q );
        foreach($activity as $a){
            if (isset( $a->user_id ) && $a->user_id > 0 ){
                $a->name = get_user_by( "id", $a->user_id )->display_name;
            }
        }
        return $activity;
    }

    /**
     * Get Contacts assigned to a user
     *
     * @param  $user_id
     * @param  $check_permissions
     * @param  $query_pagination_args Pass in pagination and ordering parameters if wanted.
     * @access public
     * @since  0.1
     * @return WP_Query | WP_Error
     */
    public static function get_user_contacts( int $user_id, bool $check_permissions = true, array $query_pagination_args = [] ) {
        if ($check_permissions && ! self::can_access( 'contacts' )) {
            return new WP_Error( __FUNCTION__, __( "You do not have access to these contacts" ), ['status' => 403] );
        }

        $query_args = array(
            'post_type' => 'contacts',
            'meta_key' => 'assigned_to',
            'meta_value' => "user-$user_id",
            'orderby' => 'ID',
            'nopaging' => true,
        );
        return self::query_with_pagination( $query_args, $query_pagination_args );
    }

    /**
     * Get Contacts viewable by a user
     *
     * @param  $check_permissions
     * @param  $query_pagination_args Pass in pagination and ordering parameters if wanted.
     * @access public
     * @since  0.1
     * @return array | WP_Error
     */
    public static function get_viewable_contacts( bool $check_permissions = true, array $query_pagination_args = [] ) {
        if ($check_permissions && !self::can_access( 'contacts' )) {
            return new WP_Error( __FUNCTION__, __( "You do not have access to these contacts" ), ['status' => 403] );
        }
        $current_user = wp_get_current_user();

        $query_args = array(
            'post_type' => 'contacts',
            'nopaging' => true,
        );
        $contacts_shared_with_user = [];
        if (!self::can_view_all( 'contacts' )){
            $contacts_shared_with_user = self::get_posts_shared_with_user( 'contacts', $current_user->ID );

            $query_args['meta_key'] = 'assigned_to';
            $query_args['meta_value'] = "user-". $current_user->ID;
        }
        $queried_contacts = self::query_with_pagination( $query_args, $query_pagination_args );
        if ( is_wp_error( $queried_contacts )){
            return $queried_contacts;
        }
        $contacts = $queried_contacts->posts;
        $contact_ids = array_map(
            function( $contact ){
                return $contact->ID;
            },
            $contacts
        );
        //add shared contacts to the list avoiding duplicates
        foreach ( $contacts_shared_with_user as $shared ){
            if(!in_array( $shared->ID, $contact_ids )){
                $contacts[] = $shared;
            }
        }
        return $contacts;
    }


    public static function get_viewable_contacts_compact( string $searchString ){
        return self::get_viewable_compact( 'contacts', $searchString );
    }


    /**
     * Get Contacts assigned to a user's team
     *
     * @param  int $user_id
     * @param  bool $check_permissions
     * @access public
     * @since  0.1
     * @return array | WP_Error
     */
    public static function get_team_contacts( int $user_id, bool $check_permissions = true ) {
        if ($check_permissions) {
            $current_user = wp_get_current_user();
            // TODO: the current permissions required don't make sense
            if (! self::can_access( 'contacts' )
                || ($user_id != $current_user->ID && ! current_user_can( 'edit_team_contacts' )))
            {
                return new WP_Error( __FUNCTION__, __( "You do not have permission" ), ['status' => 404] );
            }
        }
        global $wpdb;
        $user_connections = [];
        $user_connections['relation'] = 'OR';
        $members = [];

        // First Query
        // Build arrays for current groups connected to user
        $sql = $wpdb->prepare(
            "SELECT DISTINCT
                `$wpdb->term_relationships`.`term_taxonomy_id`
            FROM
                `$wpdb->term_relationships`
            INNER JOIN
                `$wpdb->term_taxonomy`
            ON
                `$wpdb->term_relationships`.`term_taxonomy_id` = `$wpdb->term_taxonomy`.`term_taxonomy_id`
            WHERE
                object_id  = %d
                AND taxonomy = %s
            ",
            $user_id,
            'user-group'
        );
        $results = $wpdb->get_results( $sql, ARRAY_A );


        // Loop
        foreach ($results as $result) {
            // create the meta query for the group
            $user_connections[] = ['key' => 'assigned_to', 'value' => 'group-' . $result['term_taxonomy_id']  ];

            // Second Query
            // query a member list for this group
            $sql = $wpdb->prepare(
                "SELECT
                    `$wpdb->term_relationships`.`object_id`
                FROM
                    `$wpdb->term_relationships`
                WHERE
                    `term_taxonomy_id` = %d
                ",
                $result['term_taxonomy_id']
            );

            // build list of member ids who are part of the team
            $results2 = $wpdb->get_results( $sql, ARRAY_A );

            // Inner Loop
            foreach ($results2 as $result2) {

                if($result2['object_id'] != $user_id) {
                    $members[] = $result2['object_id'];
                }
            }
        }

        $members = array_unique( $members );

        foreach($members as $member) {
            $user_connections[] = ['key' => 'assigned_to', 'value' => 'user-' . $member  ];
        }

        $args = [
            'post_type' => 'contacts',
            'nopaging' => true,
            'meta_query' => $user_connections,
        ];
        $query2 = new WP_Query( $args );
        return [
            "members" => $user_connections,
            "contacts" => $query2->posts,
        ];
    }


    public static function update_seeker_path( int $contact_id, string $path_option, bool $check_permissions = true ){
        $seeker_path_options = self::$contact_fields["seeker_path"]["default"];
        $option_keys = array_keys( $seeker_path_options );
        $current_seeker_path = get_post_meta( $contact_id, "seeker_path", true );
        $current_index = array_search( $current_seeker_path, $option_keys );
        $new_index = array_search( $path_option,  $option_keys );
        if ($new_index > $current_index){
            $current_index = $new_index;
            $update = self::update_contact( $contact_id, ["seeker_path"=> $path_option], $check_permissions );
            if ( is_wp_error( $update ) ){
                return $update;
            }
        }
        return [
            "current"=>  $seeker_path_options[$option_keys[$current_index]],
            "next" => isset( $option_keys[$current_index+1] ) ? $seeker_path_options[$option_keys[$current_index+1]] : ""
        ];

    }

    public static function quick_action_button( int $contact_id, array $field, bool $check_permissions = true ){
        $response = self::update_contact( $contact_id, $field, true );
        if ( !isset( $response->ID ) || $response->ID != $contact_id ){
            return $response;
        } else {
            $update = [];
            $key = key( $field );

            if ( $key == "quick_button_no_answer") {
                $update["seeker_path"] = "attempted";
            } else if ($key == "quick_button_phone_off"){
                $update["seeker_path"] = "attempted";
            } else if ($key == "quick_button_contact_established") {
                $update["seeker_path"] = "established";
            } else if ($key == "quick_button_meeting_scheduled"){
                $update["seeker_path"] = "scheduled";
            } else if ( $key == "quick_button_meeting_complete"){
                $update["seeker_path"] = "met";
            }

            if ( isset( $update["seeker_path"] )){
                return self::update_seeker_path( $contact_id, $update["seeker_path"], $check_permissions );
            } else {
                return $contact_id;
            }
        }
    }

    public static function add_comment( int $contact_id, string $comment, bool $check_permissions = true ){
        if ($check_permissions && ! self::can_update( 'contacts', $contact_id )) {
            return new WP_Error( __FUNCTION__, __( "You do not have permission for this" ), ['status' => 403] );
        }
        $user = wp_get_current_user();
        $user_id = get_current_user_id();
        $comment_data = [
            'comment_post_ID' => $contact_id,
            'comment_content' => $comment,
            'user_id' => $user_id,
            'comment_author' => $user->display_name,
            'comment_author_url' => $user->user_url,
            'comment_author_email' => $user->user_email,
            'comment_type' => 'comment'
        ];

        return wp_new_comment( $comment_data );
    }

    public static function get_comments ( int $contact_id, bool $check_permissions = true ){
        if ($check_permissions && ! self::can_view( 'contacts', $contact_id )) {
            return new WP_Error( __FUNCTION__, __( "No permissions to read contact" ), ['status' => 403] );
        }
        $comments = get_comments( ['post_id'=>$contact_id] );
        return $comments;
    }




    public static function accept_contact( int $contact_id, bool $accepted, bool $check_permissions ){
        if (!self::can_update( 'contacts', $contact_id )){
            return new WP_Error( __FUNCTION__, __( "You do not have permission for this" ), ['status' => 403] );
        }

        if ($accepted){
            update_post_meta( $contact_id, 'overall_status', 'active' );
            update_post_meta( $contact_id, 'accepted', 'Yes' );
            return ["overall_status"=> self::$contact_fields["overall_status"]["default"]['active']];
        } else {
            update_post_meta( $contact_id, 'assigned_to', $meta_value = 'dispatch' );
            update_post_meta( $contact_id, 'overall_status', $meta_value = 'unassigned' );
            return [
                "assigned_to" => 'dispatch'
            ];
        }
    }

    /**
     * Gets an array of users whom the contact is shared with.
     * @param $post_id
     * @return array|mixed
     */
    public static function get_shared_with_on_contact( int $post_id ) {
        return self::get_shared_with( 'contacts', $post_id );
    }

    /**
     * Removes share record
     * @param $post_id
     * @param $user_id
     *
     * @return false|int|WP_Error
     */
    public static function remove_shared_on_contact( int $post_id, int $user_id ) {
        return self::remove_shared( 'contacts', $post_id, $user_id );
    }

    /**
     * Adds a share record
     *
     * @param int   $post_id
     * @param int   $user_id
     * @param array $meta
     *
     * @return false|int|WP_Error
     */
    public static function add_shared_on_contact( int $post_id, int $user_id, $meta = null ) {
        return self::add_shared( 'contacts', $post_id, $user_id, $meta );
    }
}
