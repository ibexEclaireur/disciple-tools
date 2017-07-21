<?php
/**
 * Count first generations in database using the Post-to-Post
 *
 * @package   Disciple_Tools
 * @author       Chasm Solutions <chasm.crew@chasm.solutions>
 * @link      https://github.com/ChasmSolutions
 * @license   GPL-3.0
 * @version   0.1
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Disciple_Tools_Counter_Connected  {


    /**
     * Constructor function.
     * @access  public
     * @since   0.1
     */
    public function __construct () { } // End __construct()

    /**
     * Counts the number of contacts with no disciples in database
     *
     * @access  public
     * @since   0.1
     */
    public function has_zero ($type) {
        global $wpdb;

        $post_type = 'contacts';
        if($type ==  'groups_to_groups') { $post_type = 'groups'; }

        // Get values
        $total_contacts = wp_count_posts( $post_type )->publish;
        $wpdb->get_var("SELECT DISTINCT p2p_to FROM $wpdb->p2p WHERE p2p_type = '$type'", ARRAY_A);
        $has_disciples = $wpdb->num_rows;

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
    public function has_at_least ($min_number, $type) {
        global $wpdb;
        $i = 0;

        $p2p_array_to = $wpdb->get_results(" SELECT p2p_to FROM $wpdb->p2p WHERE p2p_type = '$type'", ARRAY_A);
        $p2p_distinct = array_unique ($p2p_array_to, SORT_REGULAR);

        foreach ($p2p_distinct as $item) {
            if ($this->get_number_disciples($item, $p2p_array_to) >= $min_number) {
                $i++;
            };
        }
        return $i;
    }

    /**
     * Counts the number of disciples or groups connected to a single record.
     * Example: How many contacts have one disciple? How many have two disciples?
     * This helps identify general fruitfulness.
     *
     * @access  public
     * @since   0.1
     * @return  number
     */
    public function has_exactly ($exact_number, $type) {
        global $wpdb;
        $i = 0;

        $p2p_array_to = $wpdb->get_results(" SELECT p2p_to FROM $wpdb->p2p WHERE p2p_type = '$type'", ARRAY_A);
        $p2p_distinct = array_unique ($p2p_array_to, SORT_REGULAR);

        foreach ($p2p_distinct as $item) {
            if ($this->get_number_disciples($item, $p2p_array_to) == $exact_number) {
                $i++;
            };
        }
        return $i;
    }


    /**
     * Query: number of disciples of a given record
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
     * Has Meta Key
     *
     */
    public function has_meta_value ($type,  $meta_value)
    {
        global $wpdb;

        $sql = $wpdb->prepare( //Select count(DISTINCT p2p_to) as planters from wp_p2p INNER JOIN wp_p2pmeta ON wp_p2p.p2p_id=wp_p2pmeta.p2p_id  where meta_value = 'Planting'
            'SELECT count(DISTINCT %1$s) FROM %2$s
                    INNER JOIN %3$s ON %2$s.%4$s=%3$s.%4$s
					WHERE `%5$s` = \'%6$s\'
					AND `%7$s` = \'%8$s\'
				;',
            'p2p_to',
            $wpdb->p2p,
            $wpdb->p2pmeta,
            'p2p_id',
            'meta_value',
            $meta_value,
            'p2p_type',
            $type
        );

        // query results
        $results = $wpdb->get_var($sql);

        return $results;

    }
}