<?php
/**
 * Updates Post Type
 *
 * This defines the Updates custom post type. This is used for specific content
 * delivered to the project_supporter role of people.
 * 
 * @package   DmmCrm
 * @author 	  Chasm Solutions <chasm.crew@chasm.solutions>
 * @link      https://github.com/ChasmSolutions
 * @copyright 2017 Chasm Solutions
 * @license   GPL-3.0
 * @version   0.0.1
 */

class Dmmcrm_Updates_Post_Type
{
	/**
	 * Class Construct
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'update_post_type' ), 0 );

	}

	/**
	 * Build the Contact Post Type
	 *
	 * WP REST API functionality has been added to the post type to allow JSON
	 * feeds of prayers. This allows you easily feed your data to other 3rd party
	 * apps that use JSON for processing data.
	 * 
	 * @return hook
	 * @since  0.0.1
	 */
	
	function update_post_type() {

	$labels = array(
		'name'                  => _x( 'Updates', 'Post Type General Name', 'dmmcrm' ),
		'singular_name'         => _x( 'Update', 'Post Type Singular Name', 'dmmcrm' ),
		'menu_name'             => __( 'Updates', 'dmmcrm' ),
		'name_admin_bar'        => __( 'Update', 'dmmcrm' ),
		'archives'              => __( 'Update Archives', 'dmmcrm' ),
		'attributes'            => __( 'Update Attributes', 'dmmcrm' ),
		'parent_item_colon'     => __( 'Parent Update:', 'dmmcrm' ),
		'all_items'             => __( 'All Updates', 'dmmcrm' ),
		'add_new_item'          => __( 'Add New Update', 'dmmcrm' ),
		'add_new'               => __( 'Add New', 'dmmcrm' ),
		'new_item'              => __( 'New Update', 'dmmcrm' ),
		'edit_item'             => __( 'Edit Update', 'dmmcrm' ),
		'update_item'           => __( 'Update \"Update\"', 'dmmcrm' ),
		'view_item'             => __( 'View Update', 'dmmcrm' ),
		'view_items'            => __( 'View Updates', 'dmmcrm' ),
		'search_items'          => __( 'Search Update', 'dmmcrm' ),
		'not_found'             => __( 'Not found', 'dmmcrm' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'dmmcrm' ),
		'featured_image'        => __( 'Featured Image', 'dmmcrm' ),
		'set_featured_image'    => __( 'Set featured image', 'dmmcrm' ),
		'remove_featured_image' => __( 'Remove featured image', 'dmmcrm' ),
		'use_featured_image'    => __( 'Use as featured image', 'dmmcrm' ),
		'insert_into_item'      => __( 'Insert into update', 'dmmcrm' ),
		'uploaded_to_this_item' => __( 'Uploaded to this update', 'dmmcrm' ),
		'items_list'            => __( 'Updates list', 'dmmcrm' ),
		'items_list_navigation' => __( 'Updates list navigation', 'dmmcrm' ),
		'filter_items_list'     => __( 'Filter updates list', 'dmmcrm' ),
	);
	$args = array(
		'label'                 => __( 'Update', 'dmmcrm' ),
		'description'           => __( 'DMM project updates for project supporters', 'dmmcrm' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'post-formats', ),
		'taxonomies'            => array( 'category', 'post_tag' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-book-alt',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,		
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
		'show_in_rest'          => true,
		'rest_base'             => 'updates',
		'rest_controller_class' => 'WP_REST_Updates_Controller',
	);
	register_post_type( 'dmm_update', $args );

}
}
