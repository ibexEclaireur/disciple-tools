<?php

/**
 * Counter factory for reporting
 *
 * @package Disciple_Tools
 * @author  Chasm Solutions <chasm.crew@chasm.solutions>
 * @license GPL-3.0
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

class Disciple_Tools_Counter_Factory {

    /**
     * Disciple_Tools_Counter_Factory The single instance of Disciple_Tools_Counter_Factory.
     *
     * @var    object
     * @access private
     * @since  1.0.0
     */
    private static $_instance = null;

    /**
     * Main Disciple_Tools_Counter_Factory Instance
     * Ensures only one instance of Disciple_Tools_Counter_Factory is loaded or can be loaded.
     *
     * @since  1.0.0
     * @static
     * @return Disciple_Tools_Counter_Factory
     */
    public static function instance () {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    /**
     * Constructor function
     *
     * @access public
     * @since  1.0.0
     */
    public function __construct () {

        // Load required files
        require_once( 'counters/counter-connected.php' );
        require_once( 'counters/counter-generations-status.php' );
        require_once( 'counters/counter-baptism.php' );
        require_once( 'counters/counter-groups.php' );
        require_once( 'counters/counter-contacts.php' );

    } // End __construct

    /**
     * Returns count of contacts publish status
     *
     * @access public
     * @since  1.0.0
     */
    public function contacts_post_status ( $status = '' ) {

        /**
         * @usage disciple_tools()->counter->contacts_post_status()
         * @returns array of status counts
         *
         * @usage disciple_tools()->counter->contacts_post_status('publish')
         * @returns number count
         */

        $status = strtolower( $status );

        switch ($status) {

            case 'publish':
                $count = wp_count_posts( 'contacts' );
                $count = $count->publish;
                return $count;
                break;

            case 'draft':
                $count = wp_count_posts( 'contacts' );
                $count = $count->draft;
                return $count;
                break;

            case 'pending':
                $count = wp_count_posts( 'contacts' );
                $count = $count->pending;
                return $count;
                break;

            case 'private':
                $count = wp_count_posts( 'contacts' );
                $count = $count->private;
                return $count;
                break;

            case 'trash':
                $count = wp_count_posts( 'contacts' );
                $count = $count->trash;
                return $count;
                break;

            default:
                return wp_count_posts( 'contacts' );
                break;

        }
    }

    /**
     * Counts the meta_data attached to a P2P connection
     *
     * @param  $type    string   Can be either contacts, groups, baptisms
     * @param  $meta_value     string
     * @param  $meta_key       string
     * @return int
     */
    public function connection_type_counter ( $type, $meta_value ) {
        $type = $this->set_connection_type( $type );
        $count = new Disciple_Tools_Counter_Connected();
        $result = $count->has_meta_value( $type, $meta_value );
        return $result;
    }


    /**
     * Counts Contacts with matching $meta_key and $meta_value provided.
     * Used to retrieve the number of contacts that match the meta_key and meta_value supplied.
     *
     * Example usage: How many contacts have the "unassigned" status? or How many contacts have a "Contact Attempted" status?
     *
     * @usage  disciple_tools()->counter->contacts_counter('overall_status','active');
     * @return int
     */
    public function contacts_meta_counter ( $meta_key, $meta_value ) {
        $query = new WP_Query( [ 'meta_key' => $meta_key, 'meta_value' => $meta_value, 'post_type' => 'contacts', ] );
        return $query->found_posts;
    }

    /**
     * Counts Contacts with matching $meta_key and $meta_value provided.
     * Used to retrieve the number of contacts that match the meta_key and meta_value supplied.
     *
     * Example usage: How many contacts have the "unassigned" status? or How many contacts have a "Contact Attempted" status?
     *
     * @usage  disciple_tools()->counter->contacts_counter('overall_status','active');
     * @return int
     */
    public function groups_meta_counter ( $meta_key, $meta_value ) {
        $query = new WP_Query( [ 'meta_key' => $meta_key, 'meta_value' => $meta_value, 'post_type' => 'groups', ] );
        return $query->found_posts;
    }


    /**
     * Counts baptisms
     */
    public function get_baptisms ( $type ) {
        switch ($type) {
            case 'baptisms':
                $count = new Disciple_Tools_Counter_Baptism();
                $result = $count->get_number_of_baptisms();
                break;
            case 'baptizers':
                $count = new Disciple_Tools_Counter_Baptism();
                $result = $count->get_number_of_baptizers();
                break;
            default:
                $result = '';
                break;
        }
        return $result;
    }

    /**
     * Contact generations counting factory
     *
     * @param  number = 1,2,3 etc for $generation number
     * @param  string = contacts or groups or baptisms
     * @return number
     */
    public function get_generation( $generation_number, $type = 'contacts' ) {

        // Set the P2P type for selecting group or contacts
        $type = $this->set_connection_type( $type );

        switch($generation_number) {

            case 'has_one_or_more':
                $gen_object = new Disciple_Tools_Counter_Connected();
                $count = $gen_object->has_at_least( 1, $type );
                break;

            case 'has_two_or_more':
                $gen_object = new Disciple_Tools_Counter_Connected();
                $count = $gen_object->has_at_least( 2, $type );
                break;

            case 'has_three_or_more':
                $gen_object = new Disciple_Tools_Counter_Connected();
                $count = $gen_object->has_at_least( 3, $type );
                break;

            case 'has_0':
                $gen_object = new Disciple_Tools_Counter_Connected();
                $count = $gen_object->has_zero( $type );
                break;

            case 'has_1':
                $gen_object = new Disciple_Tools_Counter_Connected();
                $count = $gen_object->has_exactly( 1, $type );
                break;

            case 'has_2':
                $gen_object = new Disciple_Tools_Counter_Connected();
                $count = $gen_object->has_exactly( 2, $type );
                break;

            case 'has_3':
                $gen_object = new Disciple_Tools_Counter_Connected();
                $count = $gen_object->has_exactly( 3, $type );
                break;

            case 'generation_list':
                $gen_object = new Disciple_Tools_Counter_Generations();
                $count = $gen_object->generation_status_list();
                break;

            case 'at_zero':
                $gen_object = new Disciple_Tools_Counter_Generations();
                $count = $gen_object->gen_level( 0, $type );
                break;

            case 'at_first':
                $gen_object = new Disciple_Tools_Counter_Generations();
                $count = $gen_object->gen_level( 1, $type );
                break;

            case 'at_second':
                $gen_object = new Disciple_Tools_Counter_Generations();
                $count = $gen_object->gen_level( 2, $type );
                break;

            case 'at_third':
                $gen_object = new Disciple_Tools_Counter_Generations();
                $count = $gen_object->gen_level( 3, $type );
                break;

            case 'at_fourth':
                $gen_object = new Disciple_Tools_Counter_Generations();
                $count = $gen_object->gen_level( 4, $type );
                break;

            case 'at_fifth':
                $gen_object = new Disciple_Tools_Counter_Generations();
                $count = $gen_object->gen_level( 5, $type );
                break;

            default:
                $count = null;
                break;
        }
        return $count;
    }

    /**
     * Sets the p2p_type for the where statement
     *
     * @param  string = 'contacts' or 'groups' or 'baptisms'
     * @return string
     */
    protected function set_connection_type ( $type ) {
        if ($type == 'contacts') {
            $type = 'contacts_to_contacts';
        } elseif ($type == 'groups') {
            $type = 'groups_to_groups';
        } elseif ($type == 'baptisms') {
            $type = 'baptizer_to_baptized';
        } elseif ($type == 'participation') {
            $type = 'contacts_to_groups';
        } else {
            $type = '';
        }
        return $type;
    }


}
