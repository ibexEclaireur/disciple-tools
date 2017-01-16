<?php
/**
 * Prayer Post Type
 *
 * This defines the Prayer custom post type. A majority of the prayer app data
 * will be stored under this custom post type. Taxonomy and heavy use of meta
 * are used as well to construct the different data functionalities that this
 * plugin provides.
 * 
 * @package   Prayer
 * @author 	  Kaleb Heitzman <kalebheitzman@gmail.com>
 * @link      https://github.com/kalebheitzman/prayer
 * @copyright 2016 Kaleb Heitzman
 * @license   GPL-3.0
 * @version   0.9.0
 */

class Dmmcrm_Prayer_Post_Type
{
	/**
	 * Class Construct
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'prayer_post_type' ), 0 );

		
	}

	/**
	 * Build the Prayer Post Type
	 *
	 * WP REST API functionality has been added to the post type to allow JSON
	 * feeds of prayers. This allows you easily feed your data to other 3rd party
	 * apps that use JSON for processing data.
	 * 
	 * @return hook
	 * @since  0.9.0
	 */
	function prayer_post_type() {

		$labels = array(
			'name'                => _x( 'Prayers', 'Post Type General Name', 'prayer' ),
			'singular_name'       => _x( 'Prayer', 'Post Type Singular Name', 'prayer' ),
			'menu_name'           => __( 'Prayers', 'prayer' ),
			'parent_item_colon'   => __( 'Parent Prayer:', 'prayer' ),
			'all_items'           => __( 'All Prayers', 'prayer' ),
			'view_item'           => __( 'View Prayer', 'prayer' ),
			'add_new_item'        => __( 'Add New Prayer', 'prayer' ),
			'add_new'             => __( 'Add New', 'prayer' ),
			'edit_item'           => __( 'Edit Prayer', 'prayer' ),
			'update_item'         => __( 'Update Prayer', 'prayer' ),
			'search_items'        => __( 'Search Prayer', 'prayer' ),
			'not_found'           => __( 'Not found', 'prayer' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'prayer' ),
		);
		$rewrite = array(
			'slug'                => 'prayers',
			'with_front'          => true,
			'pages'               => true,
			'feeds'               => true,
		);
		$args = array(
			'label'               => __( 'prayer', 'prayer' ),
			'description'         => __( 'Prayer Requests', 'prayer' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions', ),
			'taxonomies'          => array( 'prayer-category', 'prayer-tag', 'prayer_location' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 24,
			'menu_icon'           => 'dashicons-heart',
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'rewrite'             => $rewrite,
			'capability_type'     => 'post',
			//'register_meta_box_cb' =>'prayer_add_metabox',
			'show_in_rest'       => true,
			'rest_base'          => 'prayers',
	        'rest_controller_class' => 'WP_REST_Posts_Controller',
		);
		register_post_type( 'prayer', $args );

	}

	
}
