<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * DmmCRM Plugin Post Type Class
 *
 * All functionality pertaining to post types in DmmCrm.
 *
 * @package WordPress
 * @subpackage DmmCrm_Plugin
 * @category Plugin
 * @author Chasm.Solutions & Kingdom.Training
 * @since 1.0.0
 */
class DmmCrm_Plugin_Contact_Post_Type {
	/**
	 * The post type token.
	 * @access public
	 * @since  1.0.0
	 * @var    string
	 */
	public $post_type;

	/**
	 * The post type singular label.
	 * @access public
	 * @since  1.0.0
	 * @var    string
	 */
	public $singular;

	/**
	 * The post type plural label.
	 * @access public
	 * @since  1.0.0
	 * @var    string
	 */
	public $plural;

	/**
	 * The post type args.
	 * @access public
	 * @since  1.0.0
	 * @var    array
	 */
	public $args;

	/**
	 * The taxonomies for this post type.
	 * @access public
	 * @since  1.0.0
	 * @var    array
	 */
	public $taxonomies;

	/**
	 * Constructor function.
	 * @access public
	 * @since 1.0.0
	 */
	public function __construct( $post_type = 'contact', $singular = '', $plural = '', $args = array(), $taxonomies = array() ) {
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
			add_filter( 'enter_title_here', array( $this, 'enter_title_here' ) );
			add_filter( 'post_updated_messages', array( $this, 'updated_messages' ) );
			

			if ( $pagenow == 'edit.php' && isset( $_GET['post_type'] ) && esc_attr( $_GET['post_type'] ) == $this->post_type ) {
				add_filter( 'manage_edit-' . $this->post_type . '_columns', array( $this, 'register_custom_column_headings' ), 10, 1 );
				add_action( 'manage_posts_custom_column', array( $this, 'register_custom_columns' ), 10, 2 );
			}
		}

		add_action( 'after_setup_theme', array( $this, 'ensure_post_thumbnails_support' ) );
		add_action( 'after_theme_setup', array( $this, 'register_image_sizes' ) );
		
		// Additional Metabox Section
		
		
		add_action( 'add_meta_boxes', array( $this, 'additional_contact_meta' ) );
		add_action( 'save_post',  array( $this, 'additional_contact_meta_save' ) );
		add_action( 'admin_print_styles', array( $this, 'additional_contact_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'additional_contact_color_enqueue' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'additional_contact_image_enqueue' ) );
		
	} // End __construct()

	/**
	 * Register the post type.
	 * @access public
	 * @return void
	 */
	public function register_post_type () {
		$labels = array(
			'name' 					=> sprintf( _x( '%s', 'post type general name', 'dmmcrm' ), $this->plural ),
			'singular_name' 		=> sprintf( _x( '%s', 'post type singular name', 'dmmcrm' ), $this->singular ),
			'add_new' 				=> _x( 'Add New', $this->post_type, 'dmmcrm' ),
			'add_new_item' 			=> sprintf( __( 'Add New %s', 'dmmcrm' ), $this->singular ),
			'edit_item' 			=> sprintf( __( 'Edit %s', 'dmmcrm' ), $this->singular ),
			'update_item'           => sprintf( __( 'Update %s', 'dmmcrm' ), $this->singular ),
			'new_item' 				=> sprintf( __( 'New %s', 'dmmcrm' ), $this->singular ),
			'all_items' 			=> sprintf( __( 'All %s', 'dmmcrm' ), $this->plural ),
			'view_item' 			=> sprintf( __( 'View %s', 'dmmcrm' ), $this->singular ),
			'view_items'            => sprintf( __( 'View %s', 'dmmcrm' ), $this->plural ),
			'search_items' 			=> sprintf( __( 'Search %a', 'dmmcrm' ), $this->plural ),
			'not_found' 			=> sprintf( __( 'No %s Found', 'dmmcrm' ), $this->plural ),
			'not_found_in_trash' 	=> sprintf( __( 'No %s Found In Trash', 'dmmcrm' ), $this->plural ),
			'parent_item_colon' 	=> '',
			'menu_name' 			=> $this->plural,
			'featured_image'        => sprintf( __( 'Featured Image', 'dmmcrm' ), $this->plural ),
			'set_featured_image'    => sprintf( __( 'Set featured image', 'dmmcrm' ), $this->plural ),
			'remove_featured_image' => sprintf( __( 'Remove featured image', 'dmmcrm' ), $this->plural ),
			'use_featured_image'    => sprintf( __( 'Use as featured image', 'dmmcrm' ), $this->plural ),
			'insert_into_item'      => sprintf( __( 'Insert into %s', 'dmmcrm' ), $this->plural ),
			'uploaded_to_this_item' => sprintf( __( 'Uploaded to this %s', 'dmmcrm' ), $this->plural ),
			'items_list'            => sprintf( __( '%s list', 'dmmcrm' ), $this->plural ),
			'items_list_navigation' => sprintf( __( '%s list navigation', 'dmmcrm' ), $this->plural ),
			'filter_items_list'     => sprintf( __( 'Filter %s list', 'dmmcrm' ), $this->plural ),
			
		);

		$single_slug = apply_filters( 'dmmcrm_single_slug', _x( sanitize_title_with_dashes( $this->singular ), 'single post url slug', 'dmmcrm' ) );
		$archive_slug = apply_filters( 'dmmcrm_archive_slug', _x( sanitize_title_with_dashes( $this->plural ), 'post archive url slug', 'dmmcrm' ) );

		$defaults = array(
			'labels' 				=> $labels,
			'public' 				=> true,
			'publicly_queryable' 	=> true,
			'show_ui' 				=> true,
			'show_in_menu' 			=> true,
			'query_var' 			=> true,
			'rewrite' 				=> array( 'slug' => $single_slug ),
			'capability_type' 		=> 'post',
			'has_archive' 			=> $archive_slug,
			'hierarchical' 			=> false,
			'supports' 				=> array( 'title', 'excerpt', 'thumbnail' ),
			'menu_position' 		=> 5,
			'menu_icon' 			=> 'dashicons-groups',
			'show_in_rest'          => true,
			'rest_base'             => 'groups',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		);

		$args = wp_parse_args( $this->args, $defaults );

		register_post_type( $this->post_type, $args );
	} // End register_post_type()

	/**
	 * Register the "thing-category" taxonomy.
	 * @access public
	 * @since  1.3.0
	 * @return void
	 */
	public function register_taxonomy () {
		$this->taxonomies['contacts-source'] = new DmmCrm_Plugin_Taxonomy($post_type = 'contacts', $token = 'contacts-source', $singular = 'Source', $plural = 'Sources', $args = array()); // Leave arguments empty, to use the default arguments.
		$this->taxonomies['contacts-source']->register();
		$this->taxonomies['contacts-type'] = new DmmCrm_Plugin_Taxonomy($post_type = 'contacts', $token = 'contacts-type', $singular = 'Type', $plural = 'Type', $args = array()); // Leave arguments empty, to use the default arguments.
		$this->taxonomies['contacts-type']->register();
	} // End register_taxonomy()

	/**
	 * Add custom columns for the "manage" screen of this post type.
	 * @access public
	 * @param string $column_name
	 * @param int $id
	 * @since  1.0.0
	 * @return void
	 */
	public function register_custom_columns ( $column_name, $id ) {
		global $post;

		switch ( $column_name ) {
			case 'status':
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
	 * @access public
	 * @param array $defaults
	 * @since  1.0.0
	 * @return void
	 */
	public function register_custom_column_headings ( $defaults ) {
		$new_columns = array( 'status' => __( 'Status', 'dmmcrm' ), 'phone' => __( 'Phone', 'dmmcrm' ) );
		
		$last_item = array();

		if ( isset( $defaults['taxonomy-contacts-source'] ) ) { unset( $defaults['taxonomy-contacts-source'] ); }
		if ( isset( $defaults['taxonomy-contacts-type'] ) ) { unset( $defaults['taxonomy-contacts-type'] ); }

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
	 * @since  1.0.0
	 * @param  array $messages Array of messages for all post types.
	 * @return array           Modified array.
	 */
	public function updated_messages ( $messages ) {
		global $post, $post_ID;

		$messages[$this->post_type] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __( '%3$s updated. %sView %4$s%s', 'dmmcrm' ), '<a href="' . esc_url( get_permalink( $post_ID ) ) . '">', '</a>', $this->singular, strtolower( $this->singular ) ),
			2 => __( 'Custom field updated.', 'dmmcrm' ),
			3 => __( 'Custom field deleted.', 'dmmcrm' ),
			4 => sprintf( __( '%s updated.', 'dmmcrm' ), $this->singular ),
			/* translators: %s: date and time of the revision */
			5 => isset($_GET['revision']) ? sprintf( __( '%s restored to revision from %s', 'dmmcrm' ), $this->singular, wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __( '%1$s published. %3$sView %2$s%4$s', 'dmmcrm' ), $this->singular, strtolower( $this->singular ), '<a href="' . esc_url( get_permalink( $post_ID ) ) . '">', '</a>' ),
			7 => sprintf( __( '%s saved.', 'dmmcrm' ), $this->singular ),
			8 => sprintf( __( '%s submitted. %sPreview %s%s', 'dmmcrm' ), $this->singular, strtolower( $this->singular ), '<a target="_blank" href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) . '">', '</a>' ),
			9 => sprintf( __( '%s scheduled for: %1$s. %2$sPreview %s%3$s', 'dmmcrm' ), $this->singular, strtolower( $this->singular ),
			// translators: Publish box date format, see http://php.net/date
			'<strong>' . date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ) . '</strong>', '<a target="_blank" href="' . esc_url( get_permalink($post_ID) ) . '">', '</a>' ),
			10 => sprintf( __( '%s draft updated. %sPreview %s%s', 'dmmcrm' ), $this->singular, strtolower( $this->singular ), '<a target="_blank" href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) . '">', '</a>' ),
		);

		return $messages;
	} // End updated_messages()
	
	
	
	/**
	 * Setup the meta box.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function meta_box_setup () {
		add_meta_box( $this->post_type . '_details', __( 'Contact Details', 'dmmcrm' ), array( $this, 'meta_box_content' ), $this->post_type, 'normal', 'high' );
	} // End meta_box_setup()

	/**
	 * The contents of our meta box.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function meta_box_content () {
		global $post_id;
		$fields = get_post_custom( $post_id );
		$field_data = $this->get_custom_fields_settings();

		$html = '';

		$html .= '<input type="hidden" name="dmmcrm_' . $this->post_type . '_noonce" id="dmmcrm_' . $this->post_type . '_noonce" value="' . wp_create_nonce( plugin_basename( dirname( DmmCrm_Plugin()->plugin_path ) ) ) . '" />';
		

		if ( 0 < count( $field_data ) ) {
			$html .= '<table class="form-table">' . "\n";
			$html .= '<tbody>' . "\n";

			foreach ( $field_data as $k => $v ) {
				$data = $v['default'];
				if ( isset( $fields['_' . $k] ) && isset( $fields['_' . $k][0] ) ) {
					$data = $fields['_' . $k][0];
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
									
						            
						            foreach ($v['default'] as $vv) {
							            $html .= '<option value="' . $vv . '" '; 
							            if($vv == $data) { $html .= 'selected';}
							            $html .= '>' .$vv . '</option>';
						            }
						$html .= '</select>' . "\n";
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
	 * @access public
	 * @since  1.0.0
	 * @param int $post_id
	 * @return int $post_id
	 */
	public function meta_box_save ( $post_id ) {
		global $post, $messages;

		// Verify
		if ( ( get_post_type() != $this->post_type ) || ! wp_verify_nonce( $_POST['dmmcrm_' . $this->post_type . '_noonce'], plugin_basename( dirname( DmmCrm_Plugin()->plugin_path ) ) ) ) {
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

			if ( get_post_meta( $post_id, '_' . $f ) == '' ) {
				add_post_meta( $post_id, '_' . $f, ${$f}, true );
			} elseif( ${$f} != get_post_meta( $post_id, '_' . $f, true ) ) {
				update_post_meta( $post_id, '_' . $f, ${$f} );
			} elseif ( ${$f} == '' ) {
				delete_post_meta( $post_id, '_' . $f, get_post_meta( $post_id, '_' . $f, true ) );
			}
		}
	} // End meta_box_save()

	/**
	 * Customise the "Enter title here" text.
	 * @access public
	 * @since  1.0.0
	 * @param string $title
	 * @return void
	 */
	public function enter_title_here ( $title ) {
		if ( get_post_type() == $this->post_type ) {
			$title = __( 'Enter the contact name here', 'dmmcrm' );
		}
		return $title;
	} // End enter_title_here()

	/**
	 * Get the settings for the custom fields.
	 * @access public
	 * @since  1.0.0
	 * @return array
	 */
	public function get_custom_fields_settings () {
		$fields = array();
		
		$fields['phone'] = array(
		    'name' => __( 'Phone', 'dmmcrm' ),
		    'description' => __( '', 'dmmcrm' ),
		    'type' => 'text',
		    'default' => '',
		    'section' => 'info'
		);
		$fields['overall_status'] = array(
		    'name' => __( 'Overall Status', 'dmmcrm' ),
		    'description' => __( '', 'dmmcrm' ),
		    'type' => 'select',
		    'default' => array('Unassignable', 'Unassigned', 'Assigned', 'Accepted', 'On Pause', 'Closed'),
		    'section' => 'info'
		);
		

		return apply_filters( 'dmmcrm_custom_fields_settings', $fields );
	} // End get_custom_fields_settings()

	/**
	 * Get the image for the given ID.
	 * @param  int 				$id   Post ID.
	 * @param  mixed $size Image dimension. (default: "thing-thumbnail")
	 * @since  1.0.0
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
	 * @access public
	 * @since  1.0.0
	 */
	public function register_image_sizes () {
		if ( function_exists( 'add_image_size' ) ) {
			add_image_size( $this->post_type . '-thumbnail', 150, 9999 ); // 150 pixels wide (and unlimited height)
		}
	} // End register_image_sizes()

	/**
	 * Run on activation.
	 * @access public
	 * @since 1.0.0
	 */
	public function activation () {
		$this->flush_rewrite_rules();
	} // End activation()

	/**
	 * Flush the rewrite rules
	 * @access public
	 * @since 1.0.0
	 */
	private function flush_rewrite_rules () {
		$this->register_post_type();
		flush_rewrite_rules();
	} // End flush_rewrite_rules()

	/**
	 * Ensure that "post-thumbnails" support is available for those themes that don't register it.
	 * @access public
	 * @since  1.0.0
	 */
	public function ensure_post_thumbnails_support () {
		if ( ! current_theme_supports( 'post-thumbnails' ) ) { add_theme_support( 'post-thumbnails' ); }
	} // End ensure_post_thumbnails_support()
	
				
			
			/**
			 * Adds a meta box to the post editing screen
			 */
			public function additional_contact_meta() {
				add_meta_box( $this->post_type . '_add_meta', __( 'Additional Information', 'dmmcrm' ), array( $this, 'additional_contact_meta_callback' ), $this->post_type, 'normal', 'high'  );
			}
			
			public function additional_contact_meta_callback () {
				global $post_id;
				
				$fields = get_post_custom( $post_id );
				$field_data = $this->get_custom_fields_settings();
		
				$html = '';
				$html .= '<table class="form-table">' . "\n";
				$html .= '<tbody>' . "\n";
				
				$html .= '<tr><th>Example Text</th><td><input type="text" name="meta-text" id="meta-text" value="';
				//if ( isset ( $additional_contact_stored_meta['meta-text'] ) ) { $html .=  $additional_contact_stored_meta['meta-text'][0];}
				$html .= '" /></td></tr>';
							
				
				$html .= '</tbody>' . "\n";
				$html .= '</table>' . "\n";
		
				echo $html;
			} // End meta_box_content()
			
			/**
			 * Outputs the content of the meta box
			 */
			public function x_additional_contact_meta_callback( $post ) {
				
				wp_nonce_field( basename( __FILE__ ), 'additional_contact_nonce' );
				$additional_contact_stored_meta = get_post_meta( $post->ID );
				?>
			
				<p>
					<label for="meta-text" class="additional_contact-row-title"><?php _e( 'Example Text Input', 'additional_contact-textdomain' )?></label>
					<input type="text" name="meta-text" id="meta-text" value="<?php if ( isset ( $additional_contact_stored_meta['meta-text'] ) ) echo $additional_contact_stored_meta['meta-text'][0]; ?>" />
				</p>
			
				<p>
					<span class="additional_contact-row-title"><?php _e( 'Example Checkbox Input', 'additional_contact-textdomain' )?></span>
					<div class="additional_contact-row-content">
						<label for="meta-checkbox">
							<input type="checkbox" name="meta-checkbox" id="meta-checkbox" value="yes" <?php if ( isset ( $additional_contact_stored_meta['meta-checkbox'] ) ) checked( $additional_contact_stored_meta['meta-checkbox'][0], 'yes' ); ?> />
							<?php _e( 'Checkbox label', 'additional_contact-textdomain' )?>
						</label>
						<label for="meta-checkbox-two">
							<input type="checkbox" name="meta-checkbox-two" id="meta-checkbox-two" value="yes" <?php if ( isset ( $additional_contact_stored_meta['meta-checkbox-two'] ) ) checked( $additional_contact_stored_meta['meta-checkbox-two'][0], 'yes' ); ?> />
							<?php _e( 'Another checkbox', 'additional_contact-textdomain' )?>
						</label>
					</div>
				</p>
			
				<p>
					<span class="additional_contact-row-title"><?php _e( 'Example Radio Buttons', 'additional_contact-textdomain' )?></span>
					<div class="additional_contact-row-content">
						<label for="meta-radio-one">
							<input type="radio" name="meta-radio" id="meta-radio-one" value="radio-one" <?php if ( isset ( $additional_contact_stored_meta['meta-radio'] ) ) checked( $additional_contact_stored_meta['meta-radio'][0], 'radio-one' ); ?>>
							<?php _e( 'Radio Option #1', 'additional_contact-textdomain' )?>
						</label>
						<label for="meta-radio-two">
							<input type="radio" name="meta-radio" id="meta-radio-two" value="radio-two" <?php if ( isset ( $additional_contact_stored_meta['meta-radio'] ) ) checked( $additional_contact_stored_meta['meta-radio'][0], 'radio-two' ); ?>>
							<?php _e( 'Radio Option #2', 'additional_contact-textdomain' )?>
						</label>
					</div>
				</p>
			
				<p>
					<label for="meta-select" class="additional_contact-row-title"><?php _e( 'Example Select Input', 'additional_contact-textdomain' )?></label>
					<select name="meta-select" id="meta-select">
						<option value="select-one" <?php if ( isset ( $additional_contact_stored_meta['meta-select'] ) ) selected( $additional_contact_stored_meta['meta-select'][0], 'select-one' ); ?>><?php _e( 'One', 'additional_contact-textdomain' )?></option>';
						<option value="select-two" <?php if ( isset ( $additional_contact_stored_meta['meta-select'] ) ) selected( $additional_contact_stored_meta['meta-select'][0], 'select-two' ); ?>><?php _e( 'Two', 'additional_contact-textdomain' )?></option>';
					</select>
				</p>
			
				<p>
					<label for="meta-textarea" class="additional_contact-row-title"><?php _e( 'Example Textarea Input', 'additional_contact-textdomain' )?></label>
					<textarea name="meta-textarea" id="meta-textarea"><?php if ( isset ( $additional_contact_stored_meta['meta-textarea'] ) ) echo $additional_contact_stored_meta['meta-textarea'][0]; ?></textarea>
				</p>
			
				<p>
					<label for="meta-color" class="additional_contact-row-title"><?php _e( 'Color Picker', 'additional_contact-textdomain' )?></label>
					<input name="meta-color" type="text" value="<?php if ( isset ( $additional_contact_stored_meta['meta-color'] ) ) echo $additional_contact_stored_meta['meta-color'][0]; ?>" class="meta-color" />
				</p>
			
				<p>
					<label for="meta-image" class="additional_contact-row-title"><?php _e( 'Example File Upload', 'additional_contact-textdomain' )?></label>
					<input type="text" name="meta-image" id="meta-image" value="<?php if ( isset ( $additional_contact_stored_meta['meta-image'] ) ) echo $additional_contact_stored_meta['meta-image'][0]; ?>" />
					<input type="button" id="meta-image-button" class="button" value="<?php _e( 'Choose or Upload an Image', 'additional_contact-textdomain' )?>" />
				</p>
			 
			
				<?php
			}
			/**
			 * Saves the custom meta input
			 */
			public function additional_contact_meta_save( $post_id ) {
			 
				// Checks save status
				$is_autosave = wp_is_post_autosave( $post_id );
				$is_revision = wp_is_post_revision( $post_id );
				$is_valid_nonce = ( isset( $_POST[ 'additional_contact_nonce' ] ) && wp_verify_nonce( $_POST[ 'additional_contact_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
			 
				// Exits script depending on save status
				if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
					return;
				}
			 
				// Checks for input and sanitizes/saves if needed
				if( isset( $_POST[ 'meta-text' ] ) ) {
					update_post_meta( $post_id, 'meta-text', sanitize_text_field( $_POST[ 'meta-text' ] ) );
				}
				// Checks for input and saves
				if( isset( $_POST[ 'meta-checkbox' ] ) ) {
					update_post_meta( $post_id, 'meta-checkbox', 'yes' );
				} else {
					update_post_meta( $post_id, 'meta-checkbox', '' );
				}
				 
				// Checks for input and saves
				if( isset( $_POST[ 'meta-checkbox-two' ] ) ) {
					update_post_meta( $post_id, 'meta-checkbox-two', 'yes' );
				} else {
					update_post_meta( $post_id, 'meta-checkbox-two', '' );
				}
				// Checks for input and saves if needed
				if( isset( $_POST[ 'meta-radio' ] ) ) {
					update_post_meta( $post_id, 'meta-radio', $_POST[ 'meta-radio' ] );
				}
				// Checks for input and saves if needed
				if( isset( $_POST[ 'meta-select' ] ) ) {
					update_post_meta( $post_id, 'meta-select', $_POST[ 'meta-select' ] );
				}
				// Checks for input and saves if needed
				if( isset( $_POST[ 'meta-textarea' ] ) ) {
					update_post_meta( $post_id, 'meta-textarea', $_POST[ 'meta-textarea' ] );
				}
				// Checks for input and saves if needed
				if( isset( $_POST[ 'meta-color' ] ) ) {
					update_post_meta( $post_id, 'meta-color', $_POST[ 'meta-color' ] );
				}
				// Checks for input and saves if needed
				if( isset( $_POST[ 'meta-image' ] ) ) {
					update_post_meta( $post_id, 'meta-image', $_POST[ 'meta-image' ] );
				}
			}
			
			/**
			 * Adds the meta box stylesheet when appropriate
			 */
			public function additional_contact_admin_styles(){
				global $typenow;
				if( $typenow == 'post' ) {
					wp_enqueue_style( 'additional_contact_meta_box_styles', plugin_dir_url( __FILE__ ) . 'meta-box-styles.css' );
				}
			}
			
			/**
			 * Loads the color picker javascript
			 */
			public function additional_contact_color_enqueue() {
				global $typenow;
				if( $typenow == 'post' ) {
					wp_enqueue_style( 'wp-color-picker' );
					wp_enqueue_script( 'meta-box-color-js', plugin_dir_url( __FILE__ ) . 'meta-box-color.js', array( 'wp-color-picker' ) );
				}
			}
			
			/**
			 * Loads the image management javascript
			 */
			public function additional_contact_image_enqueue() {
				global $typenow;
				if( $typenow == 'post' ) {
					wp_enqueue_media();
			 
					// Registers and enqueues the required javascript.
					wp_register_script( 'meta-box-image', plugin_dir_url( __FILE__ ) . 'meta-box-image.js', array( 'jquery' ) );
					wp_localize_script( 'meta-box-image', 'meta_image',
						array(
							'title' => __( 'Choose or Upload an Image', 'additional_contact-textdomain' ),
							'button' => __( 'Use this image', 'additional_contact-textdomain' ),
						)
					);
					wp_enqueue_script( 'meta-box-image' );
				}
			}
			
} // End Class