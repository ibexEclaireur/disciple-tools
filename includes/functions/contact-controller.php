<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Contact_Controller
 *
 * Functions for creating, finding, updating or deleting contacts
 */

class Contact_Controller
{
	public $contact_fields;

	public function __construct(){
		add_action('init', function(){
			$this->contact_fields = Disciple_Tools_Contact_Post_Type::instance()->get_custom_fields_settings();
		});

	}

    /**
     * @param array $fields
     * @return array
     */
    public function create_contact($fields = array()){
	    //@todo search for duplicates
        //@todo set defaults

	    //required fields
        if (!isset($fields["title"])){
            return array("success"=>false, "message"=>"contact needs a title");
        } else {
            $name = $fields["title"];
        }

        //check to see if each field exists
        $bad_fields = array();
        foreach($fields as $field => $value){
        	if (!isset($this->contact_fields[$field])){
        		$bad_fields[] = $field;
	        }
        }
        if (!empty($bad_fields)){
        	return array("success"=>false, "message"=>array("these fields do not exist"=>$bad_fields));
        }


        $post = array(
            "post_title" => $name,
            'post_type' => "contacts",
            "post_status" => 'publish',
            "meta_input" => $fields
        );

        $post_id = wp_insert_post($post);
        return array("success"=>true, "contact_id"=>$post_id);

    }

    public static function update_contact($contact_id, $fields){

    }

    public static function find_contact(){

    }

    public static function merge_contacts($base_contact, $duplicate_contact){

    }
}