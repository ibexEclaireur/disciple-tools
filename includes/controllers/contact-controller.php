<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Contact_Controller
 *
 * Functions for creating, finding, updating or deleting contacts
 */

class Contact_Controller
{
	public static $contact_fields;

	public function __construct(){
		add_action('init', function(){
			self::$contact_fields = Disciple_Tools_Contact_Post_Type::instance()->get_custom_fields_settings();
		});

	}

    /**
     * Create a new Contact
     * @param array $fields, the new contact's data
     * @access private
	 * @since 0.1
     * @return array
     */
    public static function create_contact($fields = array()){
	    //@todo search for duplicates
        //@todo set defaults

	    //required fields
        if (!isset($fields["title"])){
            return array("success"=>false, "message"=>"contact needs a title", "fields"=>$fields);
        }
	    $bad_fields = self::check_for_invalid_fields($fields);
	    if (!empty($bad_fields)){
		    return array("success"=>false, "message"=>array("these fields do not exist"=>$bad_fields));
	    }

        $post = array(
            "post_title" => $fields['title'],
            'post_type' => "contacts",
            "post_status" => 'publish',
            "meta_input" => $fields
        );

        $post_id = wp_insert_post($post);
        return array("success"=>true, "contact_id"=>$post_id);
    }

	/**
	 * Make sure there are no extra or misspelled fields
	 * Make sure the field values are the correct format
	 * @param $fields, the contact meta fields
	 * @access private
	 * @since 0.1
	 * @return array
	 */
    private static function check_for_invalid_fields($fields){
	    $bad_fields = array();
	    $contact_model_fields = self::$contact_fields;
	    //some fields are not in the model
	    $contact_model_fields['title'] = "";
	    foreach($fields as $field => $value){
//	    	@todo check for invald values by type
		    if (!isset($contact_model_fields[$field])){
			    $bad_fields[] = $field;
		    }
	    }
	    return $bad_fields;
	}

	/**
	 * Update an existing Contact
	 * @param $contact_id, the post id for the contact
	 * @param $fields, the meta fields
	 * @access public
	 * @since 0.1
	 * @return array success|error
	 */
    public static function update_contact($contact_id, $fields){
	    $post = get_post($contact_id);

	    if (!$post){
            return array("success"=>false, "message"=>"Contact does not exist");
	    }
        $bad_fields = self::check_for_invalid_fields($fields);
		if (!empty($bad_fields)){
			return array("success"=>false, "message"=>array("these fields do not exist"=>$bad_fields));
		}

		foreach($fields as $field_id => $value){
			update_post_meta($contact_id, $field_id, $value);
		}
        return array("success"=>true);
    }

	/**
	 * Get a single contact
	 *
	 * @param $contact_id , the contact post_id
	 *
	 * @return array, On success: the contact, else: the error message
	 */
    public static function get_contact($contact_id){

    	$contact = get_post($contact_id);

	    if ($contact){
    	    $contact->fields = get_post_custom($contact_id);

	    } else {
		    return array("success"=>false, "message"=>"No contact with found with id:" . $contact_id);
	    }
    	return array("success"=>true, "contact"=>$contact);
    }

    public static function find_contact(){

    }

    public static function merge_contacts($base_contact, $duplicate_contact){

    }


}