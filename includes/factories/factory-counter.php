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

class drm_counter_factory {

	/**
	 * Constructor function
	 *
	 * @access  public
	 * @since   0.1
	 */
	public function __construct ( ) { } // End __construct

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

    


}