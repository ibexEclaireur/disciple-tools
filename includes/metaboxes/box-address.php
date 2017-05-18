<?php

/**
 * Disciple_Tools_Metabox_Address
 *
 * @class Disciple_Tools_Metabox_Address
 * @version	0.1
 * @since 0.1
 * @package	Disciple_Tools
 * @author Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function dt_address_metabox () {
    $object = new Disciple_Tools_Metabox_Address();
    return $object;
}

class Disciple_Tools_Metabox_Address {

    /**
     * Constructor function.
     * @access  public
     * @since   0.1
     */
    public function __construct () {

    } // End __construct()

    public function content_display () {
        $html = 'Here is content';
        return $html;
    }

    /**
     * Add Address fields html for adding a new contact channel
     * @usage Added to the bottom of the Contact Details Metabox.
     */
    public function add_new_address_field () {
        global $post;

        $html = '<p><a href="javascript:void(0);" onclick="jQuery(\'#new-address\').toggle();"><strong>+ Address Detail</strong></a></p>';
        $html .= '<table class="form-table" id="new-address" style="display: none;"><tbody>' . "\n";

        $channels = $this->get_address_list($post->post_type);

        $html .= '<tr><th>
                <select name="new-key-address" class="edit-input"><option value=""></option> ';
        foreach ($channels as $channel) {

            $key =  $this->create_channel_metakey($channel, 'address'); // build key
            $names = explode("_", $key); // separates primary name from type tag

            $html .= '<option value="'.$key.'">'.$names[1] . '</option>';
        }
        $html .= '</select></th>';
        $html .= '<td><input type="text" name="new-value-address" id="new-address" class="edit-input" placeholder="i.e. 888 West Street, Los Angelos CO 90210" /></td><td><button type="submit" class="button">Save</button></td></tr>';

        $html .= '</tbody></table>';
        return $html;
    }

    /**
     * Helper function to create the unique metakey for contacts channels.
     * @param $channel
     * @return string
     */
    public function create_channel_metakey ($channel, $type) {
        return $type . '_' . $channel . '_' . $this->unique_hash(); // build key
    }

    /**
     * Creates 3 digit random hash
     * @return string
     */
    public function unique_hash() {
        return substr(md5(rand(10000, 100000)), 0, 3); // create a unique 3 digit key
    }

    /**
     * Selectable values for different channels of contact information.
     * @return array
     */
    public function get_address_list ($post_type) {

        switch ($post_type) {
            case 'contacts':
                $addresses = array(
                    __('Home', 'disciple_tools'),
                    __('Work', 'disciple_tools'),
                    __('Other', 'disciple_tools'),
                );
                return $addresses;
                break;
            case 'groups':
                $addresses = array(
                    __('Main', 'disciple_tools'),
                    __('Alternate', 'disciple_tools'),
                );
                return $addresses;
                break;
            default:
                break;
        }

    }

    /**
     * Field: Contact Fields
     * @return array
     */
    public function address_fields () {
        global $wpdb, $post;

        $fields = array();
        $current_fields = array();

        if (isset($post->ID)){
            $current_fields = $wpdb->get_results( "SELECT meta_key FROM wp_postmeta WHERE post_id = $post->ID AND meta_key LIKE 'address_%' ORDER BY meta_key DESC", ARRAY_A );
        }

        foreach ($current_fields as $value) {
            $names = explode('_', $value['meta_key']);

            $fields[$value['meta_key']] = array(
                'name' => $names[1] ,
            );
        }
        return $fields;
    }



}