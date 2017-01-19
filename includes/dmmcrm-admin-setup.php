<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
* DMM CRM modifications to WP Admin Area
*
* 
* @author Chasm Solutions
* @package dmmcrm
*/


	/*
	* Modified Admin Bar
	* 
	*/
	function modify_admin_bar( $wp_admin_bar ) {
			
		// Remove Logo
		$wp_admin_bar->remove_node( 'wp-logo' );
		
		// Remove "Howday" and replace with "Welcome"
		$user_id = get_current_user_id();
		$current_user = wp_get_current_user();
		$profile_url = get_edit_profile_url( $user_id );
		
		if ( 0 != $user_id ) {
			/* Add the "My Account" menu */
			$avatar = get_avatar( $user_id, 28 );
			$howdy = sprintf( __('Welcome, %1$s'), $current_user->display_name );
			$class = empty( $avatar ) ? '' : 'with-avatar';
			
			$wp_admin_bar->add_menu( array(
				'id' => 'my-account',
				'parent' => 'top-secondary',
				'title' => $howdy . $avatar,
				'href' => $profile_url,
				'meta' => array(
					'class' => $class,
					),
				) 
			);
		} // end if
	}
	add_action( 'admin_bar_menu', 'modify_admin_bar', 999 );

    /*
	* Remove Admin Footer and Version Number
	* 
	*/
	// 
	function __empty_footer_string () {
		// Update the text area with an empty string. TODO: see if this is better to do with CSS display:none;
		return '';
	}
	add_filter( 'admin_footer_text', '__empty_footer_string', 11 );
	add_filter( 'update_footer',     '__empty_footer_string', 11 );

    /*
	* Enqueue Styles and Scripts to the Post Type pages
	* 
	*/
	function my_enqueue_scripts($hook) {
	    // Test if post type page
	    if( 'post.php' != $hook )
	          return;
		
		// Enqueue Custom DMMCRM admin styles page
	    wp_register_style( 'dmmcrm_admin_css', plugin_dir_url( __FILE__ ) . 'css/dmmcrm-admin-styles.css' );
	    wp_enqueue_style( 'dmmcrm_admin_css' );
		
		// Enqueue Jquery UI CSS
		wp_register_style( 'dmmcrm_ui_css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css' );
	    wp_enqueue_style( 'dmmcrm_ui_css' );
	    
		// Enqueue Jquery UI
	    wp_enqueue_script("jquery-ui-core");
	    wp_enqueue_script( 'admin_scripts', plugin_dir_url( __FILE__ ) .'js/dmmcrm-admin.js', array('jquery', 'jquery-ui-core') );
	     // No need to enqueue jQuery as it's already included in the WordPress admin by default
	    
	}
	add_action( 'admin_enqueue_scripts', 'my_enqueue_scripts' );

