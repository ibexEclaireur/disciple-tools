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

class drm_generations_counter  {

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

    /**
     * Counts the number of zero generation contacts in database
     * @access  public
     * @since   0.1
     */
    public function count_zero_generation () {

        // Load variables
        global $wpdb;
        $i = 0;
        $gen_ids = array();

        // Get records from P2P table
        $p2p_array = $wpdb->get_results(" SELECT p2p_to, p2p_from FROM $wpdb->p2p WHERE p2p_type = 'contacts_to_contacts'", ARRAY_A);
        $p2p_array_from = array_column ( $p2p_array , 'p2p_from');

        // Filter the number of zero generation records
        foreach ($p2p_array as $value) {
            if($this->p2p_zero_generation_check($value['p2p_to'], $p2p_array_from)) { //TRUE
                $gen_ids[$i] = $value['p2p_to'];
                $i++;
            }
        }

        // Count the number of records
        $gen_count = count(array_unique($gen_ids));

        // Return number of contacts who are zero generation
        return $gen_count;
    }



    /**
     * Counts the number of zero generation contacts in database
     * @access  public
     * @since   0.1
     */
    public function generation_status_list () {
        // Load variables
        global $wpdb;
        $gen_ids = array();
        $gen_count = array();

        // Get records from P2P table
        $p2p_array = $wpdb->get_results(" SELECT p2p_to, p2p_from FROM $wpdb->p2p WHERE p2p_type = 'contacts_to_contacts'", ARRAY_A);
        $p2p_array_from = array_column ( $p2p_array , 'p2p_from');
        $p2p_array_to = array_column ( $p2p_array , 'p2p_to');

        // Find all generation numbers for contacts, except last generation
        foreach ($p2p_array as $value) {

            $target = $value['p2p_to'];
            $from = $value['p2p_from'];

            // While loop checks for the first generation and increments the generation above the target until it gets to the first generation.
            $target_inc = $target; // separates the target from the increment
            $target_from = $from;
            $i = 1; // sets the increment value

            while (true) {
                if (! $this->p2p_zero_generation_check($target_inc, $p2p_array_from)) { // is initial condition true

                    // get the parent id & replace target with parent id
                    $parent_id = $this->p2p_get_single_parent_id($target_inc, $p2p_array) ;
                    $gen_ids[$i] = $parent_id;
                    $target_inc = $parent_id;
                    $i++;

                }
                else { // condition failed
                    break; // leave loop
                }
            }

            

            // Count the number of records
            $gen_count[$target] = count( array_unique($gen_ids) );
        }

        /*// Find generation number for all last generation
        $distinct_from = array_unique( $p2p_array_from, SORT_REGULAR);

        foreach($distinct_from as $value) {
            if( $this->last_gen_check ($value, $p2p_array_to)) {
                // While loop checks for the first generation and increments the generation above the target until it gets to the first generation.
                $target_inc = $value; // separates the target from the increment
                $i = 1; // sets the increment value

                while (true) {
                    if ($this->last_gen_check($target_inc, $p2p_array_from)) { // is initial condition true

                        // get the parent id & replace target with parent id
                        $parent_id = $this->p2p_get_single_parent_id($target_inc, $p2p_array) ;
                        $gen_ids[$i] = $parent_id;
                        $target_inc = $parent_id;
                        $i++;

                    }
                    else { // condition failed
                        break; // leave loop
                    }
                }

                // Count the number of records
                $gen_count[$value] = count( array_unique($gen_ids) );
            }
        }*/
        // then add that number and its generation to the gen_count


        // Return number of contacts who are zero generation
        return $gen_count;
    }

    public function contact_gen_level ($level) {
        $i = 0;
        $list = $this->generation_status_list();

        foreach($list as $item) {
            if($item == $level) {
                $i++;
            }
        }
        return $i;
    }

    /**
     * Counts the number of contacts with no disciples in database
     *
     * @access  public
     * @since   0.1
     */
    public function contact_has_zero () {
       // Get values
        $total_contacts = wp_count_posts( 'contacts' )->publish;
       $has_disciples = $this->get_contacts_with_disciples();

       // Subtract total contacts from contacts with disciples
       $gen_count = $total_contacts - $has_disciples;

       return $gen_count;
    }

    /**
     * Counts the number of contacts with at least two disciples
     *
     * @access  public
     * @since   0.1
     * @returns number
     */
    public function contact_has_at_least ($min_number) {
        global $wpdb;
        $i = 0;

        $p2p_array_to = $wpdb->get_results(" SELECT p2p_to FROM $wpdb->p2p WHERE p2p_type = 'contacts_to_contacts'", ARRAY_A);
        $p2p_distinct = array_unique ($p2p_array_to, SORT_REGULAR);

        foreach ($p2p_distinct as $item) {
            if ($this->get_number_disciples($item, $p2p_array_to) >= $min_number) {
                $i++;
            };
        }
        return $i;
    }

    /**
     * Counts the number of contacts with at least two disciples
     *
     * @access  public
     * @since   0.1
     * @returns number
     */
    public function contact_has ($min_number) {
        global $wpdb;
        $i = 0;

        $p2p_array_to = $wpdb->get_results(" SELECT p2p_to FROM $wpdb->p2p WHERE p2p_type = 'contacts_to_contacts'", ARRAY_A);
        $p2p_distinct = array_unique ($p2p_array_to, SORT_REGULAR);

        foreach ($p2p_distinct as $item) {
            if ($this->get_number_disciples($item, $p2p_array_to) == $min_number) {
                $i++;
            };
        }
        return $i;
    }

    /**
     * Helper: Checks if the parent is first generation
     * @param number
     * @param array
     */
    protected function p2p_get_single_parent_id( $target, $list) {
        $parent = '';

        foreach ($list as $row) {
            if ($row['p2p_from'] == $target) {
                $parent =  $row['p2p_to'];
            }
        }
        return $parent;
    }

    /**
     * Helper: Checks if record is zero generation
     * @parent  Single number taken from the wp_p2p.p2p_to column
     * @column  An array with the entire column of wp_p2p.p2p_from data
     */
    protected function p2p_zero_generation_check ($parent, $column) {
        foreach ($column as $value) {
            if ($value == $parent) {
                return FALSE;
            }
        }
        return TRUE;
    }

    /**
     * Helper: Checks if record is zero generation
     * @parent  Single number taken from the wp_p2p.p2p_to column
     * @column  An array with the entire column of wp_p2p.p2p_from data
     */
    protected function last_gen_check ($target, $column) {
        foreach ($column as $value) {
            if ($value == $target) {
                return FALSE;
            }
        }
        return TRUE;
    }

    /**
     * Query number of disciples of a given record
     *
     * @returns number
     */
    protected function get_number_disciples ($contact, $column) {
        $i = 0;

        foreach($column as $item) {
            if($item == $contact) {
                $i++;
            }
        }
        return $i;
    }

    /**
     * Query Total Number of Distinct Contacts Who Have a Disciple Relationship
     *
     * @returns number
     */
    protected function get_contacts_with_disciples () {
        global $wpdb;
        $wpdb->get_var("SELECT DISTINCT p2p_to FROM $wpdb->p2p WHERE p2p_type = 'contacts_to_contacts'", ARRAY_A);
        return $wpdb->num_rows;
    }

}