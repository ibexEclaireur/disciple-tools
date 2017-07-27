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
     * Create a new Contact
     *
     * @param  array $fields, the new contact's data
     * @access private
     * @since  0.1
     * @return array
     */
    public static function create_contact( $fields = [] ){
        //@todo search for duplicates
        //@todo set defaults

        //required fields
        if (!isset( $fields["title"] )){
            return ["success"=>false, "message"=>"contact needs a title", "fields"=>$fields];
        }
        $bad_fields = self::check_for_invalid_fields( $fields );
        if (!empty( $bad_fields )){
            return ["success"=>false, "message"=>["these fields do not exist"=>$bad_fields]];
        }

        $post = [
            "post_title" => $fields['title'],
            'post_type' => "contacts",
            "post_status" => 'publish',
            "meta_input" => $fields
        ];

        $post_id = wp_insert_post( $post );
        return ["success"=>true, "contact_id"=>$post_id];
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
     * @param  $contact_id, the post id for the contact
     * @param  $fields, the meta fields
     * @access public
     * @since  0.1
     * @return array success|error
     */
    public static function update_contact( $contact_id, $fields ){
        $post = get_post( $contact_id );
        if (isset( $fields['id'] )){
            unset( $fields['id'] );
        }

        if (!$post){
            return ["success"=>false, "message"=>"Contact does not exist"];
        }
        $bad_fields = self::check_for_invalid_fields( $fields );
        if (!empty( $bad_fields )){
            return ["success"=>false, "message"=>["these fields do not exist"=>$bad_fields]];
        }

        if ($fields['title']){
            wp_update_post( ['ID'=>$contact_id, 'post_title'=>$fields['title']] );
        }

        foreach($fields as $field_id => $value){
            update_post_meta( $contact_id, $field_id, $value );
        }
        return ["success"=>true, "contact_id"=>$contact_id];
    }

    /**
     * Get a single contact
     *
     * @param  $contact_id , the contact post_id
     * @access public
     * @since  0.1
     * @return array, On success: the contact, else: the error message
     */
    public static function get_contact( $contact_id ){

        $contact = get_post( $contact_id );

        if ($contact){
            $contact->fields = get_post_custom( $contact_id );

        } else {
            return ["success"=>false, "message"=>"No contact with found with id:" . $contact_id];
        }
        return ["success"=>true, "contact"=>$contact];
    }

    /**
     * Find Contacts with meta field value
     *
     * @param  $meta_field
     * @param  $value
     * @access public
     * @since  0.1
     * @return array
     */
    public static function find_contacts( $meta_field, $value ){
        $query = new WP_Query(
            [
            'post_type' => 'contacts',
            'meta_key' => $meta_field,
            'meta_value' => $value
             ]
        );
        return $query->posts;
    }

    public static function merge_contacts( $base_contact, $duplicate_contact ){

    }

    /**
     * Get Contacts assigned to a user
     *
     * @param  $user_id
     * @param  $check_permissions
     * @access public
     * @since  0.1
     * @return array or WP_Error
     */
    public static function get_user_contacts( int $user_id, bool $check_permissions = true ) {
        if ($check_permissions) {
            $current_user = wp_get_current_user();
            // TODO: the current permissions required don't make sense
            if (! current_user_can( 'edit_contacts' )
                || ($user_id != $current_user->ID && ! current_user_can( 'edit_team_contacts' )))
            {
                return new WP_Error( __FUNCTION__, __( "You do not have access to these contacts" ), ['status' => 403] );
            }
        }
        $contacts = self::find_contacts( 'assigned_to', "user-$user_id" );
        return $contacts;
    }

    /**
     * Get Contacts assigned to a user's team
     *
     * @param  $user_id
     * @access public
     * @since  0.1
     * @return array
     */
    public static function get_team_contacts( $user_id ){
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



        return ["success"=>true, "members"=>$user_connections, "contacts"=>$query2->posts];
    }

}
