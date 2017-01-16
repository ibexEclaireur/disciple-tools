<?php
/**
 * Groups Post Type
 *
 * This defines the Group custom post type. 
 * 
 * @package   DmmCrm
 * @author 	  Chasm Solutions <chasm.crew@chasm.solutions>
 * @link      https://github.com/ChasmSolutions
 * @copyright 2017 Chasm Solutions
 * @license   GPL-3.0
 * @version   0.0.1
 */

class Dmmcrm_Groups_Post_Type
{
	/**
	 * Class Construct
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'groups_post_type' ), 0 );

	}

	/**
	 * Build the Groups Post Type
	 *
	 * 
	 * 
	 * @return hook
	 * @since  0.0.1
	 */
	
	function groups_post_type() {

	$labels = array(
		'name'                  => _x( 'Groups', 'Post Type General Name', 'dmmcrm' ),
		'singular_name'         => _x( 'Group', 'Post Type Singular Name', 'dmmcrm' ),
		'menu_name'             => __( 'Groups', 'dmmcrm' ),
		'name_admin_bar'        => __( 'Group', 'dmmcrm' ),
		'archives'              => __( 'Group Archives', 'dmmcrm' ),
		'attributes'            => __( 'Group Attributes', 'dmmcrm' ),
		'parent_item_colon'     => __( 'Parent Group:', 'dmmcrm' ),
		'all_items'             => __( 'All Groups', 'dmmcrm' ),
		'add_new_item'          => __( 'Add New Group', 'dmmcrm' ),
		'add_new'               => __( 'Add Group', 'dmmcrm' ),
		'new_item'              => __( 'New Group', 'dmmcrm' ),
		'edit_item'             => __( 'Edit Group', 'dmmcrm' ),
		'update_item'           => __( 'Update Group', 'dmmcrm' ),
		'view_item'             => __( 'View Group', 'dmmcrm' ),
		'view_items'            => __( 'View Groups', 'dmmcrm' ),
		'search_items'          => __( 'Search Group', 'dmmcrm' ),
		'not_found'             => __( 'Not found', 'dmmcrm' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'dmmcrm' ),
		'featured_image'        => __( 'Featured Image', 'dmmcrm' ),
		'set_featured_image'    => __( 'Set featured image', 'dmmcrm' ),
		'remove_featured_image' => __( 'Remove featured image', 'dmmcrm' ),
		'use_featured_image'    => __( 'Use as featured image', 'dmmcrm' ),
		'insert_into_item'      => __( 'Insert into item', 'dmmcrm' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'dmmcrm' ),
		'items_list'            => __( 'Items list', 'dmmcrm' ),
		'items_list_navigation' => __( 'Items list navigation', 'dmmcrm' ),
		'filter_items_list'     => __( 'Filter items list', 'dmmcrm' ),
	);
	$rewrite = array(
			'slug'                  => 'contact',
			'with_front'            => true,
			'pages'                 => true,
			'feeds'                 => false,
		);
	$args = array(
		'label'                 => __( 'Group', 'dmmcrm' ),
		'description'           => __( 'These are 3/3 groups and simple churches', 'dmmcrm' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'excerpt', 'thumbnail', 'comments', ),
		'taxonomies'            => array( 'post_tag' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-admin-multisite',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => false,		
		'exclude_from_search'   => true,
		'publicly_queryable'    => false,
		'rewrite'               => $rewrite,
		'capability_type'       => 'post',
		'show_in_rest'          => true,
		'rest_base'             => 'groups',
		'rest_controller_class' => 'WP_REST_Posts_Controller',
	);
	register_post_type( 'dmm_groups', $args );

	}
}
