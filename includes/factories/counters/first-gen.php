<?php
/**
 * Count first generations in database
 *
 * @package   DRM
 * @author 	  Chasm Solutions <chasm.crew@chasm.solutions>
 * @link      https://github.com/ChasmSolutions
 * @license   GPL-3.0
 * @version   0.1
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class drm_first_generation_counter  {

    protected $p2p;
    protected $p2pmeta;
    protected $groups_filter = 'groups_to_groups';
    protected $contacts_filter = 'contacts_to_contacts';

    /**
     * Constructor function.
     * @access  public
     * @since   0.1
     */
    public function __construct () {
        // Build database tables for p2p
        global $wpdb;
        $this->p2p = $wpdb->prefix . 'p2p';
        $this->p2pmeta = $wpdb->prefix . 'p2pmeta';


    } // End __construct()

    public function count_first_generation () {
        /*// Get array of all active contacts
        $args = array(
            'post_type' => 'contacts'
        );
        $contacts = query_posts($args);


        // Check if each of those are first generation
        foreach ($contacts as $record) {
            $record['ID'];
        }*/

        global $wpdb;
        $i = 0;

        // Get array of all unique ids in p2p
        $p2p_array = $wpdb->get_results( "SELECT DISTINCT p2p_to FROM $this->p2p WHERE p2p_type = 'contacts_to_contacts'" );
        $p2p_array = json_decode(json_encode($p2p_array), True); // convert from object array to php array.
        $p2p_array_from = array_column ( $p2p_array , 'p2p_from');

        /*if($this->p2p_first_generation_check($gen, $p2p_array_from)) { //TRUE
            $i = 0;
        }*/

        // Return number of contacts who are first generation
        return $i;
    }

    /*
     * Get Current Generation
     * retrieves a number representing the generation of the supplied record number.
     *
     * @param   post_id number from the contact.
     * @returns number
     * @access public
     * @since 0.1
     */
    public function get_current_gen ($gen, $type) {
        global $wpdb;
        $i = 0;

        // Check whether to search contacts or groups generations
        if ($type == 'groups') { $filter = $this->groups_filter; }
        elseif ($type == 'contacts') { $filter = $this->contacts_filter; }
        else { wp_die(); }


        // Build array from db
        $p2p_array = $wpdb->get_results( "SELECT p2p_to, p2p_from FROM $this->p2p WHERE p2p_type = '$filter'" );
        $p2p_array = json_decode(json_encode($p2p_array), True); // convert from object array to php array.

        // Create array columns
        $p2p_array_from = array_column ( $p2p_array , 'p2p_from');

        // Check if this is source generation
        if($this->p2p_first_generation_check($gen, $p2p_array_from)) { //TRUE
            $i = 0;
        }
        else
        { // FALSE, then what generation is it?
            while (true) {
                if (! $this->p2p_first_generation_check($gen, $p2p_array_from)) { // checks if the target is found in the
                    $gen = $this->p2p_get_single_parent_id($gen, $p2p_array) ;
                    $i++;
                } else { // condition failed
                    break; // leave loop
                }
            }
        }
        return $i;
    }


    /*
     * Checks if record is first generation
     *
     * @parent  Single number taken from the wp_p2p.p2p_to column
     * @column  An array with the entire column of wp_p2p.p2p_from data
     *
     * */
    protected function p2p_first_generation_check ($parent, $column) {
        foreach ($column as $value) {
            if ($value == $parent) {
                return FALSE;
            }
        }
        return TRUE;
    }

}