<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
* Disciple_Tools modifications to WP Admin Area
*
* 
* @author Chasm Solutions
* @package Disciple_Tools
*/

    /*
     * Sanitize image file name
     *
     * https://wordpress.org/plugins/wp-hash-filename/
     * */
    function make_filename_hash($filename) {
        $info = pathinfo($filename);
        $ext  = empty($info['extension']) ? '' : '.' . $info['extension'];
        $name = basename($filename, $ext);
        return md5($name) . $ext;
    }
    add_filter('sanitize_file_name', 'make_filename_hash', 10);
    /* End Sanitize file name */


    /*
    * Set users to only see their posts and media.
    *   This is a key configuration section for partitioning the ability to view contacts.
    *
    * @source  http://phpbits.net/hide-wordpress-posts-and-media-uploaded-by-other-users/
    *
    * */
    function hide_posts_media_by_other($query) {
        global $pagenow;
        if( ( 'edit.php' != $pagenow && 'upload.php' != $pagenow && 'post.php' != $pagenow   ) || !$query->is_admin ){
            return $query;
        }
        if( !current_user_can( 'manage_contacts' ) ) {
            global $user_ID;
            $query->set('author', $user_ID );
        }
        return $query;
    }
    add_filter('pre_get_posts', 'hide_posts_media_by_other');

    /*
    * Hide Media Images
    */
    add_filter( 'posts_where', 'hide_attachments_wpquery_where' );
    function hide_attachments_wpquery_where( $where ){
        global $current_user;
        if( !current_user_can( 'manage_options' ) ) {
            if( is_user_logged_in() ){
                if( isset( $_POST['action'] ) ){
                    // library query
                    if( $_POST['action'] == 'query-attachments' ){
                        $where .= ' AND post_author='.$current_user->data->ID;
                    }
                }
            }
        }
        return $where;
    }

    /*
     * End set users.
     *
     * */





    /**
     * Sets the Admin color scheme.
     *
     * Sets the Disciple_Tools admin screen to "light" and take away the color scheme change feature in profile
     */
    add_filter('get_user_option_admin_color', 'change_admin_color');
    function change_admin_color($result) {
        return 'light';
    }
    remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
    /* end colored scheme configuration */


    /*
     * Table configuration experiment. Adds columns to the contacts list.
     * */

    add_filter('manage_contacts_posts_columns', 'contacts_table_head');
    function contacts_table_head( $defaults ) {
        $defaults['phone']  = 'Phone';
        $defaults['seeker_path']    = 'Seeker Path';
        $defaults['seeker_milestones']    = 'Seeker Milestone';
        return $defaults;
    }


    add_action( 'manage_contacts_posts_custom_column', 'contacts_table_content', 10, 2 );

    function contacts_table_content( $column_name, $post_id ) {
        if ($column_name == 'phone') {
            echo get_post_meta( $post_id, 'phone', true );
            ;
        }
        if ($column_name == 'seeker_path') {
            $status = get_post_meta( $post_id, 'seeker_path', true );
            echo $status;
        }

        if ($column_name == 'seeker_milestones') {
            echo get_post_meta( $post_id, 'seeker_milestones', true );
        }

    }



    /*
     * End table configuration experiment
     *
     * */


	/*
	* Modify Admin Bar
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
	 * Sets the default user role to 'registered'
	 *
	 *
	 * */
    add_filter('pre_option_default_role', function($default_role){
        // You can also add conditional tags here and return whatever
        return 'registered'; // This is changed
        return $default_role; // This allows default
    });

    /*
	* Remove Admin Footer and Version Number
	* 
	*/
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
	    wp_register_style( 'drm_admin_css', Disciple_Tools()->plugin_css . 'drm-admin-styles.css' );
	    wp_enqueue_style( 'drm_admin_css' );

		// Enqueue Jquery UI CSS
		wp_register_style( 'drm_ui_css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css' );
	    wp_enqueue_style( 'drm_ui_css' );

		// Enqueue Jquery UI
	    wp_enqueue_script("jquery-ui-core");
	    wp_enqueue_script( 'admin_scripts', Disciple_Tools()->plugin_js .'drm-admin.js', array('jquery', 'jquery-ui-core') );
	     // No need to enqueue jQuery as it's already included in the WordPress admin by default

	}
	add_action( 'admin_enqueue_scripts', 'my_enqueue_scripts' );
