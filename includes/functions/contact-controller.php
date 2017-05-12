<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Contact_Controller
 *
 * Functions for creating, finding, updated or deleting contacts
 */

class Contact_Controller
{
    /**
     * @param array $fields
     * @return array
     */
    public static function create_contact($fields = array()){
        //@todo check for required fields
        //@todo search for duplicates
        //@todo set defaults


        if (!isset($fields["name"])){
            return array("success"=>false, "message"=>"contact needs a name");
        } else {
            $name = $fields["name"];
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