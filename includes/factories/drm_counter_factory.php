<?php

/**
 * Counter factory for reporting
 *
 * @package   DRM
 * @author 	  Chasm Solutions <chasm.crew@chasm.solutions>
 * @link      https://github.com/ChasmSolutions
 * @license   GPL-3.0
 * @version   0.1
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Drm_Counter_Factory {

    /**
     * Drm_Counter_Factory The single instance of Drm_Counter_Factory.
     * @var 	object
     * @access  private
     * @since 	0.1
     */
    private static $_instance = null;

    /**
     * Main Drm_Counter_Factory Instance
     * Ensures only one instance of Drm_Counter_Factory is loaded or can be loaded.
     *
     * @since 0.1
     * @static
     * @return Drm_Counter_Factory
     */
    public static function instance () {
        if ( is_null( self::$_instance ) )
            self::$_instance = new self();
        return self::$_instance;
    } // End instance()

	/**
	 * Constructor function
	 *
	 * @access  public
	 * @since   0.1
	 */
	public function __construct ( ) {

	    // Load required files
	    require_once('counters/generations-counter.php');
        require_once('counters/generations-status-counter.php');

    } // End __construct

	/**
	 * Returns count of contacts publish status
	 *
	 * @access  public
	 * @since   0.1
	 */
	public function contacts_post_status ($status = '') {

		/**
		 * @usage DRM_Plugin()->counter->contacts_status()
		 * @returns array of status counts
		 *
		 * @usage DRM_Plugin()->counter->contacts_status('publish')
		 * @returns number count
		 */

		$status = strtolower($status);

		switch ($status) {

			case 'publish':
				$count = wp_count_posts('contacts');
				$count = $count->publish;
				return $count;
				break;

			case 'draft':
				$count = wp_count_posts('contacts');
				$count = $count->draft;
				return $count;
				break;

			case 'pending':
				$count = wp_count_posts('contacts');
				$count = $count->pending;
				return $count;
				break;

			case 'private':
				$count = wp_count_posts('contacts');
				$count = $count->private;
				return $count;
				break;

			case 'trash':
				$count = wp_count_posts('contacts');
				$count = $count->trash;
				return $count;
				break;

			default:
				return wp_count_posts('contacts');
				break;

		}
	}

	/**
	 * Get Count from Meta Data in Contacts
	 *
	 * @returns number
	 * @access  public
	 * @since   0.1
	 */
	public function contacts_overall_status ($status = 'unassigned') {

		$status = strtolower($status);

		switch ($status) {

			case 'unassignable':
				$query = new WP_Query( array( 'meta_key' => 'overall_status', 'meta_value' => 'Unassignable', 'post_type' => 'contacts', ) );
				return $query->found_posts;
				break;

			case 'unassigned':
				$query = new WP_Query( array( 'meta_key' => 'overall_status', 'meta_value' => 'Unassigned', 'post_type' => 'contacts', ) );
				return $query->found_posts;
				break;

			case 'assigned':
				$query = new WP_Query( array( 'meta_key' => 'overall_status', 'meta_value' => 'Assigned', 'post_type' => 'contacts', ) );
				return $query->found_posts;
				break;

			case 'accepted':
				$query = new WP_Query( array( 'meta_key' => 'overall_status', 'meta_value' => 'Accepted', 'post_type' => 'contacts', ) );
				return $query->found_posts;
				break;

			case 'onpause':
				$query = new WP_Query( array( 'meta_key' => 'overall_status', 'meta_value' => 'On Pause', 'post_type' => 'contacts', ) );
				return $query->found_posts;
				break;

			case 'closed':
				$query = new WP_Query( array( 'meta_key' => 'overall_status', 'meta_value' => 'Closed', 'post_type' => 'contacts', ) );
				return $query->found_posts;
				break;

			default:
				$query = new WP_Query( array( 'meta_key' => 'overall_status', 'meta_value' => 'Unassigned', 'post_type' => 'contacts', ) );
				return $query->found_posts;
				break;
		}
	}

	/**
	 * Get count of generations
	 *
	 *
	 */
    public function get_generation( $generation_number ) {


	    switch($generation_number) {



	        case 'has_zero':
                $gen_object = new drm_generations_counter();
                $count = $gen_object->contact_has_zero();
                break;

            case 'has_one_or_more':
                $gen_object = new drm_generations_counter();
                $count = $gen_object->contact_has_at_least(1);
                break;

            case 'has_two_or_more':
                $gen_object = new drm_generations_counter();
                $count = $gen_object->contact_has_at_least(2);
                break;

            case 'has_three_or_more':
                $gen_object = new drm_generations_counter();
                $count = $gen_object->contact_has_at_least(3);
                break;

            case 'has_0':
                $gen_object = new drm_generations_counter();
                $count = $gen_object->contact_has_zero();
                break;

            case 'has_1':
                $gen_object = new drm_generations_counter();
                $count = $gen_object->contact_has(1);
                break;

            case 'has_2':
                $gen_object = new drm_generations_counter();
                $count = $gen_object->contact_has(2);
                break;

            case 'has_3':
                $gen_object = new drm_generations_counter();
                $count = $gen_object->contact_has(3);
                break;

            case 'generation_list':
                $gen_object = new drm_generations_status_counter();
                $count = $gen_object->generation_status_list();
                break;

            case 'at_zero':
                $gen_object = new drm_generations_status_counter();
                $count = $gen_object->contact_gen_level(0);
                break;

            case 'at_first':
                $gen_object = new drm_generations_status_counter();
                $count = $gen_object->contact_gen_level(1);
                break;

            case 'at_second':
                $gen_object = new drm_generations_status_counter();
                $count = $gen_object->contact_gen_level(2);
                break;

            case 'at_third':
                $gen_object = new drm_generations_status_counter();
                $count = $gen_object->contact_gen_level(3);
                break;

            case 'at_fourth':
                $gen_object = new drm_generations_status_counter();
                $count = $gen_object->contact_gen_level(4);
                break;

            case 'at_fifth':
                $gen_object = new drm_generations_status_counter();
                $count = $gen_object->contact_gen_level(5);
                break;

            default:
                $count = null;
                break;
        }
        return $count;
    }


}