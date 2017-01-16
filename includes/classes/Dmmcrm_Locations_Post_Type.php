<?php
/**
 * Locations Post Type
 *
 * This defines the Location Data custom post type. 
 * 
 * @package   DmmCrm
 * @author 	  Chasm Solutions <chasm.crew@chasm.solutions>
 * @link      https://github.com/ChasmSolutions
 * @copyright 2017 Chasm Solutions
 * @license   GPL-3.0
 * @version   0.0.1
 */

class Dmmcrm_Locations_Post_Type
{
	/**
	 * Class Construct
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'locations_post_type' ), 0 );

	}

	/**
	 * Build the Locations Post Type
	 *
	 * This post type stores all the location and mapping data.
	 * 
	 * @return hook
	 * @since  0.0.1
	 */
	
	function locations_post_type() {

	$labels = array(
		'name'                  => _x( 'Locations', 'Post Type General Name', 'dmmcrm' ),
		'singular_name'         => _x( 'Location', 'Post Type Singular Name', 'dmmcrm' ),
		'menu_name'             => __( 'Locations', 'dmmcrm' ),
		'name_admin_bar'        => __( 'Location', 'dmmcrm' ),
		'archives'              => __( 'Location Archives', 'dmmcrm' ),
		'attributes'            => __( 'Location Attributes', 'dmmcrm' ),
		'parent_item_colon'     => __( 'Parent Location:', 'dmmcrm' ),
		'all_items'             => __( 'All Locations', 'dmmcrm' ),
		'add_new_item'          => __( 'Add New Location', 'dmmcrm' ),
		'add_new'               => __( 'Add New', 'dmmcrm' ),
		'new_item'              => __( 'New Location', 'dmmcrm' ),
		'edit_item'             => __( 'Edit Location', 'dmmcrm' ),
		'update_item'           => __( 'Update Location', 'dmmcrm' ),
		'view_item'             => __( 'View Location', 'dmmcrm' ),
		'view_items'            => __( 'View Locations', 'dmmcrm' ),
		'search_items'          => __( 'Search Location', 'dmmcrm' ),
		'not_found'             => __( 'Not found', 'dmmcrm' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'dmmcrm' ),
		'featured_image'        => __( 'Featured Image', 'dmmcrm' ),
		'set_featured_image'    => __( 'Set featured image', 'dmmcrm' ),
		'remove_featured_image' => __( 'Remove featured image', 'dmmcrm' ),
		'use_featured_image'    => __( 'Use as featured image', 'dmmcrm' ),
		'insert_into_item'      => __( 'Insert into Location', 'dmmcrm' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Location', 'dmmcrm' ),
		'items_list'            => __( 'Locations list', 'dmmcrm' ),
		'items_list_navigation' => __( 'Locations list navigation', 'dmmcrm' ),
		'filter_items_list'     => __( 'Filter Locations list', 'dmmcrm' ),
	);
	$args = array(
		'label'                 => __( 'Location', 'dmmcrm' ),
		'description'           => __( 'All Locations posts include location data', 'dmmcrm' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'comments', ),
		'taxonomies'            => array( 'category', 'post_tag' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-location',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,		
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
		'show_in_rest'          => true,
		'rest_base'             => 'Locations',
		'rest_controller_class' => 'WP_REST_Locations_Controller',
	);
	register_post_type( 'locations_post_type', $args );

	}
}
