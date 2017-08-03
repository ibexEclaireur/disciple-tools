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

class Disciple_Tools_Contacts
{
    public static $contact_fields;

    public function __construct(){
        add_action(
            'init', function(){
                self::$contact_fields = Disciple_Tools_Contact_Post_Type::instance()->get_custom_fields_settings();
            }
        );

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
        //@todo search for duplicates
        //@todo set defaults

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
     * @access private
     * @since  0.1
     * @return array
     */
    private static function check_for_invalid_fields( $fields ){
        $bad_fields = [];
        $contact_model_fields = self::$contact_fields;
        //some fields are not in the model
        $contact_model_fields['title'] = "";
        foreach($fields as $field => $value){
            //	    	@todo check for invald values by type
            if (!isset( $contact_model_fields[$field] )){
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

        if ($check_permissions && ! current_user_can( "edit_contacts" )) {
            return new WP_Error( __FUNCTION__, __( "You do have permission for this" ), ['status' => 403] );
        }

        $post = get_post( $contact_id );
        if (isset( $fields['id'] )){
            unset( $fields['id'] );
        }

        if (!$post){
            return new WP_Error( __FUNCTION__, __( "Contact does not exist" ) );
        }
        $bad_fields = self::check_for_invalid_fields( $fields );
        if (!empty( $bad_fields )){
            return new WP_Error( __FUNCTION__, __( "These fields do not exist" ), ['bad_fields' => $bad_fields] );
        }

        if ($fields['title']){
            wp_update_post( ['ID'=>$contact_id, 'post_title'=>$fields['title']] );
        }

        foreach($fields as $field_id => $value){
            update_post_meta( $contact_id, $field_id, $value );
        }
        return $contact_id;
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

        //@todo restrict to only get contact's the user has access to

        if ($check_permissions && ! current_user_can( 'read_contact' )) {
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
            $fields[ "groups" ] = get_posts(
                [
                'connected_type' => 'contacts_to_groups',
                'connected_items' => $contact,
                'nopaging' => true,
                'suppress_filters' => false
                ]
            );
            $fields[ "baptized" ] = get_posts(
                [
                'connected_type' => 'contacts_to_baptized',
                'connected_items' => $contact,
                'nopaging' => true,
                'suppress_filters' => false
                ]
            );
            $fields[ "relationships" ] = get_posts(
                [
                'connected_type' => 'contacts_to_contacts',
                'connected_items' => $contact,
                'nopaging' => true,
                'suppress_filters' => false
                ]
            );


            $meta_fields = get_post_custom( $contact_id );
            foreach( $meta_fields as $key => $value) {
                if ( strpos( $key, "contact_phone" ) === 0 ){
                    $fields[ "phone_numbers" ][$key] = $value;
                } elseif ( strpos( $key, "contact_email" ) === 0){
                    $fields[ "emails" ][$key] = $value;
                } elseif ( strpos( $key, "address" ) === 0){
                    $fields[ "address" ][$key] = $value;
                } elseif ( isset( self::$contact_fields[$key] ) && self::$contact_fields[$key]["type"] == "key_select" ) {
                    $fields[$key] = [ "key"=>$value[0], "label"=>self::$contact_fields[$key]["default"][$value[0]] ];
                } else {
                    $fields[$key] = $value[0];
                }
            }
            $contact->fields = $fields;

            return $contact;
        } else {
            return new WP_Error( __FUNCTION__, __( "No contact found with ID" ), [ 'contact_id' => $contact_id ] );
        }
    }

    public static function merge_contacts( $base_contact, $duplicate_contact ){

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
        if ($check_permissions) {
            $current_user = wp_get_current_user();
            // TODO: the current permissions required don't make sense
            if (! current_user_can( 'edit_contacts' )
                || ($user_id != $current_user->ID && ! current_user_can( 'edit_team_contacts' )))
            {
                return new WP_Error( __FUNCTION__, __( "You do not have access to these contacts" ), ['status' => 403] );
            }
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
            if (! current_user_can( 'edit_contacts' )
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
            'SELECT DISTINCT %1$s.%3$s
          FROM %1$s
          INNER JOIN %2$s ON %1$s.%3$s=%2$s.%3$s
            WHERE object_id  = \'%4$d\'
            AND taxonomy = \'%5$s\'
            ',
            $wpdb->term_relationships,
            $wpdb->term_taxonomy,
            'term_taxonomy_id',
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
                'SELECT %1$s.object_id
          FROM %1$s
            WHERE term_taxonomy_id  = \'%2$d\'
            ',
                $wpdb->term_relationships,
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

    public static function quick_contact_update( int $contact_id, array $field, bool $check_permissions = true ){
        $updated = self::update_contact( $contact_id, $field, true );
        if ($updated != $contact_id){
            return $updated;
        } else {
            $update = [];
            if ( $field[ "contact_quick_button_no_answer" ] == 1 || $field[ "contact_quick_button_phone_off" ] == 1){
                $update["seeker_path"] = "1";
            } else if ($field[ "contact_quick_button_contact_established" ] == 1) {
                $update["seeker_path"] = "2";
            } else if ($field[ "contact_quick_button_meeting_scheduled" ] == 1) {
                $update["seeker_path"] = "4";
            } else if ($field[ "contact_quick_button_meeting_complete" ] == 1) {
                $update["seeker_path"] = "5";
            }
            if ( !empty( $update )){
                return self::update_contact( $contact_id, $update );
            }
        }
    }

}
