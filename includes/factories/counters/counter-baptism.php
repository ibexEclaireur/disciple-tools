<?php
/**
 * Counts Baptism statistics in database
 *
 * @package   Disciple_Tools
 * @author 	  Chasm Solutions <chasm.crew@chasm.solutions>
 * @link      https://github.com/ChasmSolutions
 * @license   GPL-3.0
 * @version   0.1
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Disciple_Tools_Baptism_Counter  {


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
    public function get_number_of_baptisms () {
        global $wpdb;

        // Build full query
        $sql = $wpdb->prepare(
            'SELECT count(%1$s) FROM %2$s
					WHERE `p2p_type` LIKE \'%3$s\'
				;',
            'p2p_id',
            $wpdb->p2p,
            'baptizer_to_baptized'
        );

        // query results
        $results = $wpdb->get_var( $sql );

        return $results;
    }

    /**
     * Counts the number of contacts with no disciples in database
     *
     * @access  public
     * @since   0.1
     */
    public function get_number_of_baptizers () {
        global $wpdb;

        // Build full query
        $sql = $wpdb->prepare(
            'SELECT COUNT(DISTINCT %1$s) FROM %2$s
					WHERE `p2p_type` LIKE \'%3$s\'
				;',
            'p2p_from',
            $wpdb->p2p,
            'baptizer_to_baptized'
        );

        // query results
        $results = $wpdb->get_var( $sql );

        return $results;
    }

}