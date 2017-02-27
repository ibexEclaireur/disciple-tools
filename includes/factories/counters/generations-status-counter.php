<?php
/**
 * Count generations status
 *
 * @package   Disciple_Tools
 * @author 	  Chasm Solutions <chasm.crew@chasm.solutions>
 * @link      https://github.com/ChasmSolutions
 * @license   GPL-3.0
 * @version   0.1
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class drm_generations_status_counter  {
    /**
     * Constructor function.
     * @access  public
     * @since   0.1
     */
    public function __construct () {

    } // End __construct()

    /**
     * Counts the number of records at the supplied generation level
     * @access  public
     * @since   0.1
     * @param   number
     * @return  number
     */
    public function gen_level ($level, $type) {
        $i = 0;
        $list = $this->generation_status_list($type);

        foreach($list as $item) {
            if($item == $level) {
                $i++; // counts how many records at that generation level
            }
        }
        return $i;
    }

    /**
     * Counts generation status
     * Returns an array of all contacts in discipleship and their generation status.
     * @access  public
     * @since   0.1
     * @return array
     */
    public function generation_status_list ($type = 'contacts_to_contacts') {

        // Load variables
        global $wpdb;
        $gen_count = array();
        $full_p2p_array = array();

        // Get records from P2P table
        $p2p_array = $wpdb->get_results(" SELECT p2p_to, p2p_from FROM $wpdb->p2p WHERE p2p_type = '$type'", ARRAY_A);

        // Prepare arrays of all people involved in discipleship
        $p2p_array_from = array_column ( $p2p_array , 'p2p_from');
        $p2p_array_to = array_column ( $p2p_array , 'p2p_to');
        $full_p2p_array = array_unique( array_merge($full_p2p_array, $p2p_array_to, $p2p_array_from), SORT_REGULAR );

        // Run checks on every contact in discipleship
        foreach ($full_p2p_array as $contact) {
            // Check if contact is first generation. If true, create array item and move to next item in loop.
            if($this->zero_generation_check($contact, $p2p_array_from)) {
                $gen_count[$contact] = 0;
            }

            // If first generation is not true, then check for what generation the contact is.
            else  {

                // While loop checks for the first generation and increments the generation above the target until it gets to the first generation.
                $target_inc = $contact; // separates the target from the increment
                $gen_ids = array();
                $i = 1;

                while (true) {
                    if (! $this->zero_generation_check($target_inc, $p2p_array_from)) { // is initial condition true

                        // get the parent id & replace target with parent id
                        $parent_id = $this->get_parent_id($target_inc, $p2p_array) ;
                        $gen_ids[$i] = $parent_id;
                        $target_inc = $parent_id;
                        $i++;

                     }
                    else { // condition failed
                        break; // leave loop
                    }
                }
                // Count the number of records
                $gen_count[$contact] = count( $gen_ids );
            } // end else
        }
        return $gen_count;
    }


    /**
     * Helper: Checks if the parent is first generation
     * @access  public
     * @since   0.1
     * @param number
     * @param array
     * @return string
     */
    protected function get_parent_id( $target, $p2p_array) {
        $parent = '';

        foreach ($p2p_array as $row) {
            if ($row['p2p_from'] == $target) {
                $parent =  $row['p2p_to'];
            }
        }
        return $parent;
    }

    /**
     * Helper: Checks if record is zero generation
     * @access  public
     * @since   0.1
     * @parent  Single number taken from the wp_p2p.p2p_to column
     * @column  An array with the entire column of wp_p2p.p2p_from data
     */
    protected function zero_generation_check ($contact, $p2p_array_from) {
        foreach ($p2p_array_from as $value) {
            if ($value == $contact) {
                return FALSE;
            }
        }
        return TRUE;
    }

}