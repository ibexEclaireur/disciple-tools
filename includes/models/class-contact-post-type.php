<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * Disciple_Tools Plugin Contacts Post Type Class
 *
 * All functionality pertaining to contacts post types in Disciple_Tools.
 *
 * @package Disciple_Tools
 * @category Plugin
 * @author Chasm.Solutions & Kingdom.Training
 * @since 0.1
 */
class Disciple_Tools_Contact_Post_Type {
	/**
	 * The post type token.
	 * @access portal
	 * @since  0.1
	 * @var    string
	 */
	public $post_type;

	/**
	 * The post type singular label.
	 * @access portal
	 * @since  0.1
	 * @var    string
	 */
	public $singular;

	/**
	 * The post type plural label.
	 * @access portal
	 * @since  0.1
	 * @var    string
	 */
	public $plural;

	/**
	 * The post type args.
	 * @access portal
	 * @since  0.1
	 * @var    array
	 */
	public $args;

	/**
	 * The taxonomies for this post type.
	 * @access portal
	 * @since  0.1
	 * @var    array
	 */
	public $taxonomies;

	/**
	 * Constructor function.
	 * @access portal
	 * @since 0.1
	 */
	public function __construct( $post_type = 'contacts', $singular = '', $plural = '', $args = array(), $taxonomies = array() ) {
		$this->post_type = $post_type;
		$this->singular = $singular;
		$this->plural = $plural;
		$this->args = $args;
		$this->taxonomies = $taxonomies;

		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_taxonomy' ) );

		if ( is_admin() ) {
			global $pagenow;

			add_action( 'admin_menu', array( $this, 'meta_box_setup' ), 20 );
			add_action( 'save_post', array( $this, 'meta_box_save' ) );
            add_action( 'save_post', array( $this, 'save_assigned_meta_box' ) );
			add_filter( 'enter_title_here', array( $this, 'enter_title_here' ) );
			add_filter( 'post_updated_messages', array( $this, 'updated_messages' ) );
			

			if ( $pagenow == 'edit.php' && isset( $_GET['post_type'] ) && esc_attr( $_GET['post_type'] ) == $this->post_type ) {
				add_filter( 'manage_edit-' . $this->post_type . '_columns', array( $this, 'register_custom_column_headings' ), 10, 1 );
				add_action( 'manage_posts_custom_column', array( $this, 'register_custom_columns' ), 10, 2 );
			}
			
		}

		add_action( 'after_setup_theme', array( $this, 'ensure_post_thumbnails_support' ) );
		add_action( 'after_theme_setup', array( $this, 'register_image_sizes' ) );
		
		
		
	} // End __construct()

	/**
	 * Register the post type.
	 * @access portal
	 * @return void
	 */
	public function register_post_type () {
		$labels = array(
			'name' 					=> sprintf( _x( '%s', 'Contacts', 'disciple_tools' ), $this->plural ),
			'singular_name' 		=> sprintf( _x( '%s', 'Contact', 'disciple_tools' ), $this->singular ),
			'add_new' 				=> _x( 'Add New', $this->post_type, 'disciple_tools' ),
			'add_new_item' 			=> sprintf( __( 'Add New %s', 'disciple_tools' ), $this->singular ),
			'edit_item' 			=> sprintf( __( 'Edit %s', 'disciple_tools' ), $this->singular ),
			'update_item'           => sprintf( __( 'Update %s', 'disciple_tools' ), $this->singular ),
			'new_item' 				=> sprintf( __( 'New %s', 'disciple_tools' ), $this->singular ),
			'all_items' 			=> sprintf( __( 'All %s', 'disciple_tools' ), $this->plural ),
			'view_item' 			=> sprintf( __( 'View %s', 'disciple_tools' ), $this->singular ),
			'view_items'            => sprintf( __( 'View %s', 'disciple_tools' ), $this->plural ),
			'search_items' 			=> sprintf( __( 'Search %a', 'disciple_tools' ), $this->plural ),
			'not_found' 			=> sprintf( __( 'No %s Found', 'disciple_tools' ), $this->plural ),
			'not_found_in_trash' 	=> sprintf( __( 'No %s Found In Trash', 'disciple_tools' ), $this->plural ),
			'parent_item_colon' 	=> '',
			'menu_name' 			=> $this->plural,
			'featured_image'        => sprintf( __( 'Featured Image', 'disciple_tools' ), $this->plural ),
			'set_featured_image'    => sprintf( __( 'Set featured image', 'disciple_tools' ), $this->plural ),
			'remove_featured_image' => sprintf( __( 'Remove featured image', 'disciple_tools' ), $this->plural ),
			'use_featured_image'    => sprintf( __( 'Use as featured image', 'disciple_tools' ), $this->plural ),
			'insert_into_item'      => sprintf( __( 'Insert into %s', 'disciple_tools' ), $this->plural ),
			'uploaded_to_this_item' => sprintf( __( 'Uploaded to this %s', 'disciple_tools' ), $this->plural ),
			'items_list'            => sprintf( __( '%s list', 'disciple_tools' ), $this->plural ),
			'items_list_navigation' => sprintf( __( '%s list navigation', 'disciple_tools' ), $this->plural ),
			'filter_items_list'     => sprintf( __( 'Filter %s list', 'disciple_tools' ), $this->plural ),
			
		);
        $rewrite = array(
            'slug'                  => 'contact',
            'with_front'            => true,
            'pages'                 => true,
            'feeds'                 => false,
        );
        $capabilities = array(
            'edit_post'             => 'edit_contact',
            'read_post'             => 'read_contact',
            'delete_post'           => 'delete_contact',
            'delete_others_posts'   => 'delete_others_contacts',
            'delete_posts'          => 'delete_contacts',
            'edit_posts'            => 'edit_contacts',
            'edit_others_posts'     => 'edit_others_contacts',
            'publish_posts'         => 'publish_contacts',
            'read_private_posts'    => 'read_private_contacts',
        );
		$single_slug = apply_filters( 'dt_single_slug', _x( sanitize_title_with_dashes( $this->singular ), 'single post url slug', 'disciple_tools' ) );
		$archive_slug = apply_filters( 'dt_archive_slug', _x( sanitize_title_with_dashes( $this->plural ), 'post archive url slug', 'disciple_tools' ) );

		$defaults = array(
            'label'                 => __( 'Contact', 'disciple_tools' ),
            'description'           => __( 'Contacts generated by the media to movement effort', 'disciple_tools' ),
			'labels' 				=> $labels,
			'portal' 				=> true,
			'publicly_queryable' 	=> true,
			'show_ui' 				=> true,
			'show_in_menu' 			=> true,
			'query_var' 			=> true,
			'rewrite' 				=> $rewrite,
            'capabilities'          => $capabilities,
			'capability_type' 		=> 'contact',
			'has_archive' 			=> true, //$archive_slug,
			'hierarchical' 			=> false,
			'supports' 				=> array( 'title', 'comments', 'author', 'revisions' ),
			'menu_position' 		=> 5,
			'menu_icon' 			=> 'dashicons-groups',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'exclude_from_search'   => false,
            'show_in_rest'          => true,
			'rest_base'             => 'contacts',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		);

		$args = wp_parse_args( $this->args, $defaults );

		register_post_type( $this->post_type, $args );
	} // End register_post_type()

//	/**
//	 * Register the "thing-category" taxonomy.
//	 * @access portal
//	 * @since  1.3.0
//	 * @return void
//	 */
	public function register_taxonomy () {
//
//      TODO: removed until we decide whether we want classification and how we want to use them.
//
//		$this->taxonomies['contacts-source'] = new Disciple_Tools_Taxonomy($post_type = 'contacts', $token = 'contacts-source', $singular = 'Source', $plural = 'Sources', $args = array()); // Leave arguments empty, to use the default arguments.
//		$this->taxonomies['contacts-source']->register();
//		$this->taxonomies['contacts-type'] = new Disciple_Tools_Taxonomy($post_type = 'contacts', $token = 'contacts-type', $singular = 'Type', $plural = 'Type', $args = array()); // Leave arguments empty, to use the default arguments.
//		$this->taxonomies['contacts-type']->register();
	} // End register_taxonomy()

	/**
	 * Add custom columns for the "manage" screen of this post type.
	 * @access portal
	 * @param string $column_name
	 * @param int $id
	 * @since  0.1
	 * @return void
	 */
	public function register_custom_columns ( $column_name, $id ) {
		global $post;

		switch ( $column_name ) {
			case 'image':
				echo $this->get_image( $id, 40 );
			break;
			case 'phone':
				echo '';
			break;

			default:
			break;
		}
	} // End register_custom_columns()

	/**
	 * Add custom column headings for the "manage" screen of this post type.
	 * @access portal
	 * @param array $defaults
	 * @since  0.1
	 * @return void
	 */
	public function register_custom_column_headings ( $defaults ) {


	    $new_columns = array(); //array( 'image' => __( 'Image', 'disciple_tools' ));
		
		$last_item = array();

		if ( isset( $defaults['taxonomy-contacts-source'] ) ) { unset( $defaults['taxonomy-contacts-source'] ); } // Removes the automatic columns generated by taxonomies
		if ( isset( $defaults['taxonomy-contacts-type'] ) ) { unset( $defaults['taxonomy-contacts-type'] ); }  // Removes the automatic columns generated by taxonomies

		if ( count( $defaults ) > 2 ) {
			$last_item = array_slice( $defaults, -1 );

			array_pop( $defaults );
		}
		$defaults = array_merge( $defaults, $new_columns );

		if ( is_array( $last_item ) && 0 < count( $last_item ) ) {
			foreach ( $last_item as $k => $v ) {
				$defaults[$k] = $v;
				break;
			}
		}

		return $defaults;
	} // End register_custom_column_headings()

	/**
	 * Update messages for the post type admin.
	 * @since  0.1
	 * @param  array $messages Array of messages for all post types.
	 * @return array           Modified array.
	 */
	public function updated_messages ( $messages ) {
		global $post, $post_ID;

		$messages[$this->post_type] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __( '%3$s updated. %sView %4$s%s', 'disciple_tools' ), '<a href="' . esc_url( get_permalink( $post_ID ) ) . '">', '</a>', $this->singular, strtolower( $this->singular ) ),
			2 => __( 'Custom field updated.', 'disciple_tools' ),
			3 => __( 'Custom field deleted.', 'disciple_tools' ),
			4 => sprintf( __( '%s updated.', 'disciple_tools' ), $this->singular ),
			/* translators: %s: date and time of the revision */
			5 => isset($_GET['revision']) ? sprintf( __( '%s restored to revision from %s', 'disciple_tools' ), $this->singular, wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __( '%1$s published. %3$sView %2$s%4$s', 'disciple_tools' ), $this->singular, strtolower( $this->singular ), '<a href="' . esc_url( get_permalink( $post_ID ) ) . '">', '</a>' ),
			7 => sprintf( __( '%s saved.', 'disciple_tools' ), $this->singular ),
			8 => sprintf( __( '%s submitted. %sPreview %s%s', 'disciple_tools' ), $this->singular, strtolower( $this->singular ), '<a target="_blank" href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) . '">', '</a>' ),
			9 => sprintf( __( '%s scheduled for: %1$s. %2$sPreview %s%3$s', 'disciple_tools' ), $this->singular, strtolower( $this->singular ),
			// translators: Publish box date format, see http://php.net/date
			'<strong>' . date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ) . '</strong>', '<a target="_blank" href="' . esc_url( get_permalink($post_ID) ) . '">', '</a>' ),
			10 => sprintf( __( '%s draft updated. %sPreview %s%s', 'disciple_tools' ), $this->singular, strtolower( $this->singular ), '<a target="_blank" href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) . '">', '</a>' ),
		);

		return $messages;
	} // End updated_messages()
	

	/**
	 * Setup the meta box.
	 * @access portal
	 * @since  0.1
	 * @return void
	 */
	public function meta_box_setup () {
		add_meta_box( $this->post_type . '_details', __( 'Contact Details', 'disciple_tools' ), array( $this, 'meta_box_content' ), $this->post_type, 'normal', 'high' );
        add_meta_box( $this->post_type . '_assigned', __( 'Assigned to', 'disciple_tools' ), array( $this, 'load_assigned_meta_box' ), $this->post_type, 'side', 'low' );

	} // End meta_box_setup()

    /**
     * Setup "assigned" meta box.
     *
     */
    public function load_assigned_meta_box ( $post_id) {
        $exclude_group = '';
        $exclude_user = '';
        $type = '';
        $id = '';

        // Start drop down
        echo '<select name="assigned_to" id="assigned_to" class="regular-text">';

        if ( !empty( get_post_meta( $post_id->ID, 'assigned_to', true) ) ) { // If there is already a record
            $metadata = get_post_meta($post_id->ID, 'assigned_to', true);
            $meta_array = explode('-', $metadata); // Separate the type and id
            $type = $meta_array[0]; // Build variables
            $id = $meta_array[1];

            // Build option for current value
            if ( $type == 'group') {
                $term_id = $id;
                $value = get_term( $term_id);
                echo '<option value="group-'.$value->term_id.'" selected>'.$value->name.'</option>';

                // exclude the current id from the $results list
                $exclude_group = "'exclude' => $term_id";
            } else {
                $value = get_user_by( 'id', $id);
                echo '<option value="user-'.$id.'" selected>'.$value->display_name.'</option>';

                // exclude the current id from the $results list
                $exclude_user = "'exclude' => $id";
            }

        }

        // Separate groups from users
        echo '<option value="" disabled> --- Teams</option>';

        // Get groups list excluding current selection
        $results = get_terms( array( 'taxonomy' => 'user-group', 'hide_empty' => true, 'exclude' => $exclude_group ) );

        // Loop list of groups list
        foreach ($results as $value) {
            echo '<option value="group-'.$value->term_id.'">'.$value->name.'</option>';
        }

        // Separate groups from users
        echo '<option value="" disabled> --- Users</option>';

        // Collect user list
        $args = array('role__not_in' => array('registered', 'prayer_supporter', 'project_supporter'), 'fields' => array('ID', 'display_name'), 'exclude' => $exclude_user );
        $results = get_users($args);

        // Loop user list
        foreach ($results as $value) {
            echo '<option value="user-'.$value->ID.'">'.$value->display_name.'</option>';
        }

        // End drop down
        echo '</select><br> ';


//        $term_id = get_post_meta( $post_id->ID, 'assigned_to_teams', true);
//        print_r( get_term( '4')); //print_r(get_post_meta( $post_id->ID, 'assigned_to_teams', true) );
    }

    /**
     * Save the contents of the Assigned To Metabox
     *
     * @return mixed/void
     */
    public function save_assigned_meta_box ( $post_id ) {
        global $post, $messages;

        // Verify
        if ( ( get_post_type() != $this->post_type ) || ! wp_verify_nonce( $_POST['dt_' . $this->post_type . '_noonce'], plugin_basename( dirname( Disciple_Tools()->plugin_path ) ) ) ) {
            return $post_id;
        }

        if ( isset( $_POST['post_type'] ) && 'page' == esc_attr( $_POST['post_type'] ) ) {
            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return $post_id;
            }
        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }
        }

        $fields = array('assigned_to');

        foreach ( $fields as $f ) {

            ${$f} = strip_tags(trim($_POST[$f]));


            if ( get_post_meta( $post_id,  $f ) == '' ) {
                add_post_meta( $post_id,  $f, ${$f}, true );
            } elseif( ${$f} != get_post_meta( $post_id, $f, true ) ) {
                update_post_meta( $post_id, $f, ${$f} );
            } elseif ( ${$f} == '' ) {
                delete_post_meta( $post_id, $f, get_post_meta( $post_id,  $f, true ) );
            }
        }
    }
	
	/**
	 * The contents of our meta box.
	 * @access portal
	 * @since  0.1
	 * @return void
	 */
	public function meta_box_content () {
		global $post_id;
		$fields = get_post_custom( $post_id );
		$field_data = $this->get_custom_fields_settings();

		$html = '';

		$html .= '<input type="hidden" name="dt_' . $this->post_type . '_noonce" id="dt_' . $this->post_type . '_noonce" value="' . wp_create_nonce( plugin_basename( dirname( Disciple_Tools()->plugin_path ) ) ) . '" />';
		
		
		if ( 0 < count( $field_data ) ) {
			$html .= '<table class="form-table">' . "\n";
			$html .= '<tbody>' . "\n";

			foreach ( $field_data as $k => $v ) {
				$data = $v['default'];
				if ( isset( $fields[$k] ) && isset( $fields[$k][0] ) ) {
					$data = $fields[$k][0];
				}
				
				$type = $v['type'];
				
				switch ( $type ) {
					
					case 'url':
						$html .= '<tr valign="top"><th scope="row"><label for="' . esc_attr( $k ) . '">' . $v['name'] . '</label></th><td><input name="' . esc_attr( $k ) . '" type="text" id="' . esc_attr( $k ) . '" class="regular-text" value="' . esc_attr( $data ) . '" />' . "\n";
						$html .= '<p class="description">' . $v['description'] . '</p>' . "\n";
						$html .= '</td><tr/>' . "\n";
					break;
					case 'text':
						$html .= '<tr valign="top"><th scope="row"><label for="' . esc_attr( $k ) . '">' . $v['name'] . '</label></th><td><input name="' . esc_attr( $k ) . '" type="text" id="' . esc_attr( $k ) . '" class="regular-text" value="' . esc_attr( $data ) . '" />' . "\n";
						$html .= '<p class="description">' . $v['description'] . '</p>' . "\n";
						$html .= '</td><tr/>' . "\n";
					break;
					case 'select':
						$html .= '<tr valign="top"><th scope="row">
							<label for="' . esc_attr( $k ) . '">' . $v['name'] . '</label></th>
							<td><select name="' . esc_attr( $k ) . '" id="' . esc_attr( $k ) . '" class="regular-text">';
									// Iterate the options
						            foreach ($v['default'] as $vv) {
							            $html .= '<option value="' . $vv . '" '; 
							            if($vv == $data) { $html .= 'selected';}
							            $html .= '>' .$vv . '</option>';
						            }
						$html .= '</select>' . "\n";
						$html .= '<p class="description">' . $v['description'] . '</p>' . "\n";
						$html .= '</td><tr/>' . "\n";
					break;
					case 'radio':
						$html .= '<tr valign="top"><th scope="row">' . $v['name'] . '</th>
							<td><fieldset>';
								// Iterate the buttons
								$increment_the_radio_button = 1;
					            foreach ($v['default'] as $vv) {
						            $html .= '<label for="'.esc_attr( $k ).'-'.$increment_the_radio_button.'">'.$vv.'</label>' .
								    '<input class="drm-radio" type="radio" name="'.esc_attr( $k ).'" id="'.$k.'-'.$increment_the_radio_button.'" value="'.$vv.'" ';
								    if($vv == $data) { $html .= 'checked';}
								    $html .= '>';
								   $increment_the_radio_button++;
					            }
						$html .= '</fieldset>' . "\n";
						$html .= '<p class="description">' . $v['description'] . '</p>' . "\n";
						$html .= '</td><tr/>' . "\n";
					break;
		
					default:
					break;
				}
				
				
			}

			$html .= '</tbody>' . "\n";
			$html .= '</table>' . "\n";
		}

		echo $html;
	} // End meta_box_content()
	
	
	
	/**
	 * Save meta box fields.
	 * @access portal
	 * @since  0.1
	 * @param int $post_id
	 * @return int $post_id
	 */
	public function meta_box_save ( $post_id ) {
		global $post, $messages;

		// Verify
		if ( ( get_post_type() != $this->post_type ) || ! wp_verify_nonce( $_POST['dt_' . $this->post_type . '_noonce'], plugin_basename( dirname( Disciple_Tools()->plugin_path ) ) ) ) {
			return $post_id;
		}

		if ( isset( $_POST['post_type'] ) && 'page' == esc_attr( $_POST['post_type'] ) ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
		}

		$field_data = $this->get_custom_fields_settings();
		$fields = array_keys( $field_data );

		foreach ( $fields as $f ) {

			${$f} = strip_tags(trim($_POST[$f]));

			// Escape the URLs.
			if ( 'url' == $field_data[$f]['type'] ) {
				${$f} = esc_url( ${$f} );
			}

			if ( get_post_meta( $post_id,  $f ) == '' ) {
				add_post_meta( $post_id,  $f, ${$f}, true );
			} elseif( ${$f} != get_post_meta( $post_id, $f, true ) ) {
				update_post_meta( $post_id, $f, ${$f} );
			} elseif ( ${$f} == '' ) {
				delete_post_meta( $post_id, $f, get_post_meta( $post_id,  $f, true ) );
			}
		}
	} // End meta_box_save()
	

	
	/**
	 * Customise the "Enter title here" text.
	 * @access portal
	 * @since  0.1
	 * @param string $title
	 * @return string
	 */
	public function enter_title_here ( $title ) {
		if ( get_post_type() == $this->post_type ) {
			$title = __( 'Enter the contact name here', 'disciple_tools' );
		}
		return $title;
	} // End enter_title_here()

	/**
	 * Get the settings for the custom fields.
	 * @access portal
	 * @since  0.1
	 * @return array
	 */
	public function get_custom_fields_settings () {
		$fields = array();
		
		$fields['phone'] = array(
		    'name' => __( 'Phone', 'disciple_tools' ),
		    'description' => '',
		    'type' => 'text',
		    'default' => '',
		    'section' => 'info'
		);
		$fields['overall_status'] = array(
		    'name' => __( 'Overall Status', 'disciple_tools' ),
		    'description' => '',
		    'type' => 'select',
		    'default' => array('Unassigned', 'Unassignable', 'Assigned', 'Accepted', 'On Pause', 'Closed'),
		    'section' => 'info'
		);
		$fields['seeker_path'] = array(
		    'name' => __( 'Seeker Path', 'disciple_tools' ),
		    'description' => '',
		    'type' => 'select',
		    'default' => array('', 'Contact Attempted', 'Contact Established', 'Confirms Interest', 'Meeting Scheduled', 'First Meeting Complete', 'Ongoing Meetings', 'Being Coached'),
		    'section' => 'info'
		);
		$fields['seeker_milestones'] = array(
		    'name' => __( 'Seeker Milestones', 'disciple_tools' ),
		    'description' => '',
		    'type' => 'select',
		    'default' => array('', 'States Belief', 'Can Share Gospel/Testimony', 'Sharing Gospel/Testimony', 'Baptized', 'Baptizing', 'In Church/Group', 'Starting Churches'),
		    'section' => 'info'
		);
		$fields['preferred_contact_method'] = array(
		    'name' => __( 'Preferred Contact Method', 'disciple_tools' ),
		    'description' => '',
		    'type' => 'select',
		    'default' => array('', 'Phone', 'Skype', 'Facebook', 'Mail', 'Email', 'SMS'),
		    'section' => 'info'
		);
		$fields['bible'] = array(
		    'name' => __( 'Bible', 'disciple_tools' ),
		    'description' => '',
		    'type' => 'select',
		    'default' => array('', 'Yes - given by hand', 'Yes - already had one', 'Yes - receipt by mail confirmed', 'Bible mailed', 'Needs / Requests Bible'),
		    'section' => 'info'
		);
		$fields['email'] = array(
		    'name' => __( 'Email', 'disciple_tools' ),
		    'description' => '',
		    'type' => 'text',
		    'default' => '',
		    'section' => 'info'
		);
		$fields['skype'] = array(
		    'name' => __( 'Skype', 'disciple_tools' ),
		    'description' => '',
		    'type' => 'text',
		    'default' => '',
		    'section' => 'info'
		);
		$fields['facebook'] = array(
		    'name' => __( 'Facebook', 'disciple_tools' ),
		    'description' => '',
		    'type' => 'text',
		    'default' => '',
		    'section' => 'info'
		);
		$fields['last_actual_contact'] = array(
		    'name' => __( 'Last Actual Contact', 'disciple_tools' ),
		    'description' => '',
		    'type' => 'text',
		    'default' => '',
		    'section' => 'info'
		);
		$fields['comprehension'] = array(
		    'name' => __( 'Gospel Comprehension', 'disciple_tools' ),
		    'description' => '',
		    'type' => 'select',
		    'default' => array('', 'Very Strong', 'Strong', 'Unknown/Unclear', 'Weak'),
		    'section' => 'info'
		);
		$fields['investigating_with_others'] = array(
		    'name' => __( 'Investigating with others', 'disciple_tools' ),
		    'description' => '',
		    'type' => 'select',
		    'default' => array('', 'Not exploring with others', 'Only with a few people', 'Openly sharing with many', 'Studying in a group'),
		    'section' => 'info'
		);
		$fields['gender'] = array(
		    'name' => __( 'Gender', 'disciple_tools' ),
		    'description' => '',
		    'type' => 'select',
		    'default' => array('', 'Male', 'Female'),
		    'section' => 'info'
		);
		$fields['age'] = array(
		    'name' => __( 'Age', 'disciple_tools' ),
		    'description' => '',
		    'type' => 'select',
		    'default' => array('', 'Under 18 years old', '18-25 years old', '26-40 years old', 'Over 40 years old'),
		    'section' => 'info'
		);
		$fields['mailing_street'] = array(
		    'name' => __( 'Mailing Street', 'disciple_tools' ),
		    'description' => '',
		    'type' => 'text',
		    'default' => '',
		    'section' => 'info'
		);
		$fields['mailing_city'] = array(
		    'name' => __( 'Mailing City', 'disciple_tools' ),
		    'description' => '',
		    'type' => 'text',
		    'default' => '',
		    'section' => 'info'
		);
		$fields['mailing_zip'] = array(
		    'name' => __( 'Mailing Zip', 'disciple_tools' ),
		    'description' => '',
		    'type' => 'text',
		    'default' => '',
		    'section' => 'info'
		);
		$fields['mailing_state'] = array(
		    'name' => __( 'Mailing State', 'disciple_tools' ),
		    'description' => '',
		    'type' => 'text',
		    'default' => '',
		    'section' => 'info'
		);
		$fields['mailing_country'] = array(
		    'name' => __( 'Mailing Country', 'disciple_tools' ),
		    'description' => '',
		    'type' => 'text',
		    'default' => '',
		    'section' => 'info'
		);
		$fields['baptism_date'] = array(
		    'name' => __( 'Baptism Date', 'disciple_tools' ),
		    'description' => '',
		    'type' => 'text',
		    'default' => '',
		    'section' => 'info'
		);
		$fields['contact_generation'] = array(
		    'name' => __( 'Contact Generation', 'disciple_tools' ),
		    'description' => '',
		    'type' => 'select',
		    'default' => array('', '1st Generation (media)', '1st Generation (relationship)', '2nd Generation', '3rd Generation', '4th Generation', '5+ Generation'),
		    'section' => 'info'
		);
		$fields['preferred_language'] = array(
		    'name' => __( 'Preferred Language', 'disciple_tools' ),
		    'description' => '',
		    'type' => 'select',
		    'default' => array('', 'English', 'French', 'Arabic', 'Spanish'),
		    'section' => 'info'
		);
		$fields['reason_closed'] = array(
		    'name' => __( 'Reason Closed', 'disciple_tools' ),
		    'description' => '',
		    'type' => 'select',
		    'default' => array('', 'Duplicate', 'Hostile / Playing Games', 'Insufficient Contact Info', 'Already In Church/Connected with Others', 'No Longer Interested', 'Just wanted a book', 'Unknown'),
		    'section' => 'info'
		);
		
		
		

		return apply_filters( 'dt_custom_fields_settings', $fields );
	} // End get_custom_fields_settings()

	/**
	 * Get the image for the given ID.
	 * @param  int 				$id   Post ID.
	 * @param  mixed $size Image dimension. (default: "thing-thumbnail")
	 * @since  0.1
	 * @return string       	<img> tag.
	 */
	protected function get_image ( $id, $size = 'thing-thumbnail' ) {
		$response = '';

		if ( has_post_thumbnail( $id ) ) {
			// If not a string or an array, and not an integer, default to 150x9999.
			if ( ( is_int( $size ) || ( 0 < intval( $size ) ) ) && ! is_array( $size ) ) {
				$size = array( intval( $size ), intval( $size ) );
			} elseif ( ! is_string( $size ) && ! is_array( $size ) ) {
				$size = array( 150, 9999 );
			}
			$response = get_the_post_thumbnail( intval( $id ), $size );
		}

		return $response;
	} // End get_image()

	/**
	 * Register image sizes.
	 * @access portal
	 * @since  0.1
	 */
	public function register_image_sizes () {
		if ( function_exists( 'add_image_size' ) ) {
			add_image_size( $this->post_type . '-thumbnail', 150, 9999 ); // 150 pixels wide (and unlimited height)
		}
	} // End register_image_sizes()

	/**
	 * Run on activation.
	 * @access portal
	 * @since 0.1
	 */
	public function activation () {
		$this->flush_rewrite_rules();
	} // End activation()

	/**
	 * Flush the rewrite rules
	 * @access portal
	 * @since 0.1
	 */
	private function flush_rewrite_rules () {
		$this->register_post_type();
		flush_rewrite_rules();
	} // End flush_rewrite_rules()

	/**
	 * Ensure that "post-thumbnails" support is available for those themes that don't register it.
	 * @access portal
	 * @since  0.1
	 */
	public function ensure_post_thumbnails_support () {
		if ( ! current_theme_supports( 'post-thumbnails' ) ) { add_theme_support( 'post-thumbnails' ); }
	} // End ensure_post_thumbnails_support()
	
				
				
} // End Class