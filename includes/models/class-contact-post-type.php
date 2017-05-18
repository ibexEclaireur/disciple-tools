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
	 * @access public
	 * @since  0.1
	 * @var    string
	 */
	public $post_type;

	/**
	 * The post type singular label.
	 * @access public
	 * @since  0.1
	 * @var    string
	 */
	public $singular;

	/**
	 * The post type plural label.
	 * @access public
	 * @since  0.1
	 * @var    string
	 */
	public $plural;

	/**
	 * The post type args.
	 * @access public
	 * @since  0.1
	 * @var    array
	 */
	public $args;

	/**
	 * The taxonomies for this post type.
	 * @access public
	 * @since  0.1
	 * @var    array
	 */
	public $taxonomies;

    /**
     * Disciple_Tools_Admin_Menus The single instance of Disciple_Tools_Admin_Menus.
     * @var 	object
     * @access  private
     * @since 	0.1
     */
    private static $_instance = null;

    /**
     * Main Disciple_Tools_Contact_Post_Type Instance
     *
     * Ensures only one instance of Disciple_Tools_Contact_Post_Type is loaded or can be loaded.
     *
     * @since 0.1
     * @static
     * @return Disciple_Tools_Contact_Post_Type instance
     */
    public static function instance () {
        if ( is_null( self::$_instance ) )
            self::$_instance = new self();
        return self::$_instance;
    } // End instance()

	/**
	 * Constructor function.
	 * @access public
	 * @since 0.1
	 */
	public function __construct( $post_type = 'contacts', $singular = '', $plural = '', $args = array(), $taxonomies = array() ) {
		$this->post_type = 'contacts';
		$this->singular = 'Contact';
		$this->plural = 'Contacts';
		$this->args = array( 'menu_icon' => 'dashicons-groups' );
		$this->taxonomies = $taxonomies = array();

		add_action( 'init', array( $this, 'register_post_type' ) );
//		add_action( 'init', array( $this, 'register_taxonomy' ) );

		if ( is_admin() ) {
			global $pagenow;

			add_action( 'save_post', array( $this, 'meta_box_save' ) );
//			add_action( 'save_post', array( $this, 'save_new_contacts' ) );
			add_filter( 'enter_title_here', array( $this, 'enter_title_here' ) );
			add_filter( 'post_updated_messages', array( $this, 'updated_messages' ) );

		}

	} // End __construct()

	/**
	 * Register the post type.
	 * @access public
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
			'insert_into_item'      => sprintf( __( 'Placed into %s', 'disciple_tools' ), $this->plural ),
			'uploaded_to_this_item' => sprintf( __( 'Uploaded to this %s', 'disciple_tools' ), $this->plural ),
			'items_list'            => sprintf( __( '%s list', 'disciple_tools' ), $this->plural ),
			'items_list_navigation' => sprintf( __( '%s list navigation', 'disciple_tools' ), $this->plural ),
			'filter_items_list'     => sprintf( __( 'Filter %s list', 'disciple_tools' ), $this->plural ),
		);
        $rewrite = array(
            'slug'                  => 'contacts',
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
		$defaults = array(
            'label'                 => __( 'Contact', 'disciple_tools' ),
            'description'           => __( 'Contacts generated by the media to movement effort', 'disciple_tools' ),
			'labels' 				=> $labels,
			'public' 				=> true,
			'publicly_queryable' 	=> true,
			'show_ui' 				=> true,
			'show_in_menu' 			=> true,
			'query_var' 			=> true,
			'rewrite' 				=> $rewrite,
            'capabilities'          => $capabilities,
			'capability_type' 		=> 'contact',
			'has_archive' 			=> true, //$archive_slug,
			'hierarchical' 			=> false,
			'supports' 				=> array( 'title', 'comments' ),
			'menu_position' 		=> 5,
			'menu_icon' 			=> 'dashicons-groups',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'exclude_from_search'   => false,
            'show_in_rest'          => true,
            'register_meta_box_cb'  => array($this, 'meta_box_setup'),
			'rest_base'             => 'contacts',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		);

		$args = wp_parse_args( $this->args, $defaults );

		register_post_type( $this->post_type, $args );
	} // End register_post_type()

	/**
	 * Register the "contacts-category" taxonomy.
	 * @access public
	 * @since  1.3.0
	 * @return void
	 */
	public function register_taxonomy () {
		$this->taxonomies['contacts-type'] = new Disciple_Tools_Taxonomy($post_type = 'contacts', $token = 'contacts-type', $singular = 'Type', $plural = 'Type', $args = array()); // Leave arguments empty, to use the default arguments.
		$this->taxonomies['contacts-type']->register();
	} // End register_taxonomy()

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
			2 => __( 'Contact updated.', 'disciple_tools' ),
			3 => __( 'Contact deleted.', 'disciple_tools' ),
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
	 * @access public
	 * @since  0.1
	 * @return void
	 */
	public function meta_box_setup () {
		add_meta_box( $this->post_type . '_details', __( 'Contact Details', 'disciple_tools' ), array( $this, 'load_contact_info_meta_box' ), $this->post_type, 'normal', 'high' );
		add_meta_box( $this->post_type . '_address', __( 'Address', 'disciple_tools' ), array( $this, 'load_address_info_meta_box' ), $this->post_type, 'normal', 'high' );
		add_meta_box( $this->post_type . '_activity', __( 'Activity', 'disciple_tools' ), array( $this, 'load_activity_meta_box' ), $this->post_type, 'normal', 'low' );
        add_meta_box( $this->post_type . '_path', __( 'Milestones', 'disciple_tools' ), array( $this, 'load_milestone_meta_box' ), $this->post_type, 'side', 'low' );
        add_meta_box( $this->post_type . '_misc', __( 'Misc', 'disciple_tools' ), array( $this, 'load_misc_meta_box' ), $this->post_type, 'side', 'low' );
        add_meta_box( $this->post_type . '_status', __( 'Status', 'disciple_tools' ), array( $this, 'load_status_info_meta_box' ), $this->post_type, 'side' );
		do_action("dt_contact_meta_boxes_setup", $this->post_type);
	} // End meta_box_setup()

    /**
     * The contents of meta box.
     * @access public
     * @since  0.1
     * @return mixed
     */
    public function meta_box_content ( $section = 'info') {
        global $post_id;
        $fields = get_post_custom( $post_id );
        $field_data = $this->get_custom_fields_settings();

        $html = '';

        $html .= '<input type="hidden" name="dt_' . $this->post_type . '_noonce" id="dt_' . $this->post_type . '_noonce" value="' . wp_create_nonce( 'update_dt_contacts' ) . '" />';


        if ( 0 < count( $field_data ) ) {
            $html .= '<table class="form-table">' . "\n";
            $html .= '<tbody>' . "\n";

            foreach ( $field_data as $k => $v ) {

                if ($v['section'] == $section || $section == 'all') {

                    $data = $v['default'];

                    if ( isset( $fields[$k] ) && isset( $fields[$k][0] ) ) {
                        $data = $fields[$k][0];
                    }

                    $type = $v['type'];

                    switch ( $type ) {

                        case 'text':
                            $html .= '<tr valign="top" id="'. esc_attr( $k )  .'"><th scope="row"><label for="' . esc_attr( $k ) . '">' . $v['name'] . '</label></th>
                                <td><input name="' . esc_attr( $k ) . '" type="text" id="' . esc_attr( $k ) . '" class="regular-text" value="' . esc_attr( $data ) . '" />' . "\n";
                            $html .= '<p class="description">' . $v['description'] . '</p>' . "\n";
                            $html .= '</td><tr/>' . "\n";
                            break;
                        case 'textarea':
                            $html .= '<tr valign="top"><th scope="row"><label for="' . esc_attr( $k ) . '">' . $v['name'] . '</label></th>
                                <td><textarea name="' . esc_attr( $k ) . '" type="text" id="' . esc_attr( $k ) . '" class="regular-text"  >' . esc_attr( $data ) . '</textarea>' . "\n";
                            $html .= '<p class="description">' . $v['description'] . '</p>' . "\n";
                            $html .= '</td><tr/>' . "\n";
                            break;
                        case 'select':
                            $html .= '<tr valign="top"><th scope="row">
                                <label for="' . esc_attr( $k ) . '">' . $v['name'] . '</label></th>
                                <td>
                                <select name="' . esc_attr( $k ) . '" id="' . esc_attr( $k ) . '" class="regular-text">';
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
                        case 'key_select':
                            $html .= '<tr valign="top"><th scope="row">
                                <label for="' . esc_attr( $k ) . '">' . $v['name'] . '</label></th>
                                <td>
                                <select name="' . esc_attr( $k ) . '" id="' . esc_attr( $k ) . '" class="regular-text">';
                            // Iterate the options
                            foreach ($v['default'] as $kk => $vv) {
                                $html .= '<option value="' . $kk . '" ';
                                if($kk == $data) { $html .= 'selected';}
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
                                $html .= ' <label for="'.esc_attr( $k ).'-'.$increment_the_radio_button.'">'.$vv.'</label> ' .
                                    '<input class="dt-radio" type="radio" name="'.esc_attr( $k ).'" id="'.$k.'-'.$increment_the_radio_button.'" value="'.$vv.'" ';
                                if($vv == $data) { $html .= 'checked';}
                                $html .= '>';
                                $increment_the_radio_button++;
                            }
                            $html .= '</fieldset>' . "\n";
                            $html .= '<p class="description">' . $v['description'] . '</p>' . "\n";
                            $html .= '</td><tr/>' . "\n";
                            break;
                        case 'checkbox':
                            $html .= '<tr valign="top"><th scope="row"><label for="' . esc_attr( $k ) . '" class="selectit">' . $v['name'] . '</label></th><td>
                                
                                <input name="' . esc_attr( $k ) . '" type="checkbox" id="' . esc_attr( $k ) . '" value="' ;

                            if($data) { $html .=  esc_attr( $data ) . '" checked="checked"/>';} else { $html .= '"/>'; }

                            $html .= '<p class="description">' . $v['description'] . '(' . $v   . ')</p>' . "\n";
                            $html .= '</td><tr/>' . "\n";
                            break;
                        case 'custom':
                            $html .= '<tr valign="top"><th scope="row"><label for="' . esc_attr( $k ) . '" class="selectit">' . $v['name'] . '</label></th><td>';
                            $html .= $v['default'];
                            $html .= '</td><tr/>' . "\n";
                            break;

                        default:
                            break;
                    }
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
     * @since  0.1
     * @param int $post_id
     * @return int $post_id
     */
    public function meta_box_save ( $post_id ) {
        global $post, $messages;

        // Verify
        if (  get_post_type() != $this->post_type  ) {
            return $post_id;
        }
        if ( isset($_POST['dt_' . $this->post_type . '_noonce']) && ! wp_verify_nonce( $_POST['dt_' . $this->post_type . '_noonce'], 'update_dt_contacts' ) ) {
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

        if ( isset($_GET['action']) ) {
            if ( $_GET['action'] == 'trash' || $_GET['action'] == 'untrash' || $_GET['action'] == 'delete' ) {
                return $post_id;
            }
        }

        $field_data = $this->get_custom_fields_settings();
        $fields = array_keys( $field_data );

        if ( (isset( $_POST['new-key-address']) && !empty($_POST['new-key-address']) ) && (isset( $_POST['new-value-address']) && !empty ($_POST['new-value-address']) ) ) { // catch and prepare new contact fields
            add_post_meta( $post_id, $_POST['new-key-address'], $_POST['new-value-address'], true );
        }

        if ( (isset( $_POST['new-key-contact']) && !empty($_POST['new-key-contact']) ) && (isset( $_POST['new-value-contact']) && !empty ($_POST['new-value-contact']) ) ) { // catch and prepare new contact fields
            add_post_meta( $post_id, $_POST['new-key-contact'], $_POST['new-value-contact'], true );
        }

        foreach ( $fields as $f ) {

            ${$f} = strip_tags(trim($_POST[$f]));

            if ( get_post_meta( $post_id,  $f ) == '' ) {
                add_post_meta( $post_id,  $f, ${$f}, true );
            } elseif ( ${$f} == '' ) {
                delete_post_meta( $post_id, $f, get_post_meta( $post_id,  $f, true ) );
            } elseif( ${$f} != get_post_meta( $post_id, $f, true ) ) {
                update_post_meta( $post_id, $f, ${$f} );
            }
        }

    } // End meta_box_save()

    /**
     * Load activity metabox
     */
    public function load_activity_meta_box () {
        dt_activity_metabox()->activity_meta_box(get_the_ID());
    }

    /**
     * Meta box for Status Information
     * @access public
     * @since  0.1
     */
    public function load_milestone_meta_box () {
        echo '' . $this->meta_box_content('milestone');
    }

    /**
     * Meta box for Status Information
     * @access public
     * @since  0.1
     */
    public function load_contact_info_meta_box () {
        global $post_id;
        echo ''. $this->meta_box_content('info');
        echo ''. $this->add_new_contact_field ();
//        print '<pre>'; print_r($this->get_custom_fields_settings()); print '</pre>';

    }

    /**
     * Meta box for Status Information
     * @access public
     * @since  0.1
     */
    public function load_address_info_meta_box () {
        echo ''. $this->meta_box_content('address');
        echo ''. $this->add_new_address_field();
    }

    /**
     * Meta box for Status Information
     * @access public
     * @since  0.1
     */
    public function load_status_info_meta_box () {
        echo ''. $this->meta_box_content('status');
    }

    /**
     * Meta box for Status Information
     * @access public
     * @since  0.1
     */
    public function load_misc_meta_box () {
        echo ''. $this->meta_box_content('misc');
    }

	/**
	 * Get the settings for the custom fields.
	 * @access public
	 * @since  0.1
	 * @return array
	 */
	public function get_custom_fields_settings () {
	    global $post;
		$fields = array();

        // Status Section
        $fields['assigned_to'] = array(
            'name' => __( 'Assigned To', 'disciple_tools' ),
            'description' => '',
            'type' => 'custom',
            'default' => $this->assigned_to_field(),
            'section' => 'status'
        );
        $fields['overall_status'] = array(
            'name' => __( 'Overall Status', 'disciple_tools' ),
            'description' => '',
            'type' => 'key_select',
            'default' => array('0' => __('Unassigned', 'disciple_tools' ), '1' => __('Accepted', 'disciple_tools' ), '2' => __('Paused', 'disciple_tools' ), '3' => __('Closed', 'disciple_tools' ), '4' => __('Unassignable', 'disciple_tools' ) ),
            'section' => 'status'
        );
        $fields['seeker_path'] = array(
            'name' => __( 'Seeker Path', 'disciple_tools' ),
            'description' => '',
            'type' => 'key_select',
            'default' => array('', '0' => __('Contact Attempted', 'disciple_tools' ), '1' => __('Contact Established', 'disciple_tools' ), '2' => __('Confirms Interest', 'disciple_tools' ), '3' => __('Meeting Scheduled', 'disciple_tools' ), '4' => __('First Meeting Complete', 'disciple_tools' ), '5' => __('Ongoing Meetings', 'disciple_tools' ), '6' => __('Being Coached', 'disciple_tools' )),
            'section' => 'status'
        );
        $fields['requires_update'] = array(
            'name' => __( 'Requires Update', 'disciple_tools' ),
            'description' => '',
            'type' => 'key_select',
            'default' => array('0' => __('No', 'disciple_tools' ), '1' =>  __('Yes', 'disciple_tools' )),
            'section' => 'status'
        );

        if(isset($post->ID) && $post->post_status != 'auto-draft') { // if being called for a specific record or new record.
            // Contact Channels Section
            $methods = $this->contact_fields();
            foreach ($methods as $k => $v) { // sets phone numbers as first
                $keys = explode('_', $k);
                if($keys[1] == __('Phone','disciple_tools') && $keys[2] == __('Primary','disciple_tools')) {
                    $fields[$k] = array(
                        'name' => $v['name'],
                        'description' => '',
                        'type' => 'text',
                        'default' => '',
                        'section' => 'info'
                    );
                }
            }
            foreach ($methods as $k => $v) { // sets phone numbers as first
                $keys = explode('_', $k);
                if($keys[1] == __('Phone','disciple_tools') && $keys[2] != __('Primary','disciple_tools')) {
                    $fields[$k] = array(
                        'name' => $v['name'],
                        'description' => '',
                        'type' => 'text',
                        'default' => '',
                        'section' => 'info'
                    );
                }
            }
            foreach ($methods as $k => $v) { // sets emails as second
                $keys = explode('_', $k);
                if($keys[1] == __('Email','disciple_tools') && $keys[2] == __('Primary','disciple_tools')) {
                    $fields[$k] = array(
                        'name' => $v['name'],
                        'description' => '',
                        'type' => 'text',
                        'default' => '',
                        'section' => 'info'
                    );
                }
            }
            foreach ($methods as $k => $v) { // sets emails as second
                $keys = explode('_', $k);
                if($keys[1] == __('Email','disciple_tools') && $keys[2] != __('Primary','disciple_tools')) {
                    $fields[$k] = array(
                        'name' => $v['name'],
                        'description' => '',
                        'type' => 'text',
                        'default' => '',
                        'section' => 'info'
                    );
                }
            }
            foreach ($methods as $k => $v) { // sets all others third
                $keys = explode('_', $k);
                if($keys[1] != __('Email','disciple_tools') && $keys[2] != __('Phone','disciple_tools') ) {
                    $fields[$k] = array(
                        'name' => $v['name'],
                        'description' => '',
                        'type' => 'text',
                        'default' => '',
                        'section' => 'info'
                    );
                }
            }



            // Address
            $addresses = $this->address_fields();
            foreach ($addresses as $k => $v) { // sets all others third
                $fields[$k] = array(
                    'name' => $v['name'],
                    'description' => '',
                    'type' => 'text',
                    'default' => '',
                    'section' => 'address'
                );
            }


        } else {
            $channels = $this->get_channels_list('contact');

            foreach ($channels as $channel) {
                $tag = null;

                $key =  'contact_' . $channel . '_111' ;
                $names = explode('_', $key);

                if($names[1] != $names[2]) { $tag = ' ('. $names[2] . ')'; }

                $fields[$key] = array(
                    'name' => $names[1] . $tag,
                    'description' => '',
                    'type' => 'text',
                    'default' => '',
                    'section' => 'info'
                );
            }

            $channels = $this->get_channels_list('address');

            foreach ($channels as $channel) {

                $key =  'address_' . $channel . '_111' ;;
                $names = explode('_', $key);


                $fields[$key] = array(
                    'name' => $names[1] ,
                    'description' => '',
                    'type' => 'text',
                    'default' => '',
                    'section' => 'address'
                );
            }
        }


        // Status information section
		$fields['milestone_belief'] = array(
		    'name' => __( 'States Belief', 'disciple_tools' ),
		    'description' => '',
		    'type' => 'key_select',
		    'default' => array('0' => __('No', 'disciple_tools' ), '1' => __('Yes', 'disciple_tools')),
		    'section' => 'milestone'
		);
        $fields['milestone_can_share'] = array(
            'name' => __( 'Can Share Gospel/Testimony', 'disciple_tools' ),
            'description' => '',
            'type' => 'key_select',
            'default' => array('0' => __('No', 'disciple_tools' ), '1' => __('Yes', 'disciple_tools')),
            'section' => 'milestone'
        );
        $fields['milestone_sharing'] = array(
            'name' => __( 'Sharing Gospel/Testimony', 'disciple_tools' ),
            'description' => '',
            'type' => 'key_select',
            'default' => array('0' => __('No', 'disciple_tools' ), '1' => __('Yes', 'disciple_tools')),
            'section' => 'milestone'
        );
        $fields['milestone_baptized'] = array(
            'name' => __( 'Baptized', 'disciple_tools' ),
            'description' => '',
            'type' => 'key_select',
            'default' => array('0' => __('No', 'disciple_tools' ), '1' => __('Yes', 'disciple_tools')),
            'section' => 'milestone'
        );
        $fields['milestone_baptizing'] = array(
            'name' => __( 'Baptizing', 'disciple_tools' ),
            'description' => '',
            'type' => 'key_select',
            'default' => array('0' => __('No', 'disciple_tools' ), '1' => __('Yes', 'disciple_tools')),
            'section' => 'milestone'
        );
        $fields['milestone_in_group'] = array(
            'name' => __( 'In Church/Group', 'disciple_tools' ),
            'description' => '',
            'type' => 'key_select',
            'default' => array('0' => __('No', 'disciple_tools' ), '1' => __('Yes', 'disciple_tools')),
            'section' => 'milestone'
        );
        $fields['milestone_planting'] = array(
            'name' => __( 'Starting Churches', 'disciple_tools' ),
            'description' => '',
            'type' => 'key_select',
            'default' => array('0' => __('No', 'disciple_tools' ), '1' => __('Yes', 'disciple_tools')),
            'section' => 'milestone'
        );


        // Misc Information fields
        $fields['bible'] = array(
            'name' => __( 'Bible', 'disciple_tools' ),
            'description' => '',
            'type' => 'select',
            'default' => array('', __('Yes - given by hand', 'disciple_tools' ), __('Yes - already had one', 'disciple_tools' ), __('Yes - receipt by mail confirmed', 'disciple_tools' ), __('Bible mailed', 'disciple_tools' ), __('Needs / Requests Bible', 'disciple_tools' )),
            'section' => 'misc'
        );
        $fields['gender'] = array(
            'name' => __( 'Gender', 'disciple_tools' ),
            'description' => '',
            'type' => 'select',
            'default' => array('', __('Male', 'disciple_tools' ), __('Female', 'disciple_tools' )),
            'section' => 'misc'
        );
        $fields['age'] = array(
            'name' => __( 'Age', 'disciple_tools' ),
            'description' => '',
            'type' => 'select',
            'default' => array('', __('Under 18 years old', 'disciple_tools' ), __('18-25 years old', 'disciple_tools' ), __('26-40 years old', 'disciple_tools' ), __('Over 40 years old', 'disciple_tools' )),
            'section' => 'misc'
        );
        $fields['comprehension'] = array(
            'name' => __( 'Gospel Comprehension', 'disciple_tools' ),
            'description' => '',
            'type' => 'select',
            'default' => array('', __('Very Strong', 'disciple_tools' ), __('Strong', 'disciple_tools' ), __('Unknown/Unclear', 'disciple_tools' ), __('Weak', 'disciple_tools' )),
            'section' => 'misc'
        );
        $fields['investigating_with_others'] = array(
            'name' => __( 'Investigating with others', 'disciple_tools' ),
            'description' => '',
            'type' => 'select',
            'default' => array('', __('Not exploring with others', 'disciple_tools' ), __('Only with a few people', 'disciple_tools' ), __('Openly sharing with many', 'disciple_tools' ), __('Studying in a group', 'disciple_tools' )),
            'section' => 'misc'
        );

        $fields['reason_closed'] = array(
            'name' => __( 'Reason Closed', 'disciple_tools' ),
            'description' => '',
            'type' => 'select',
            'default' => array('', __('Duplicate', 'disciple_tools' ), __('Hostile / Playing Games', 'disciple_tools' ), __('Insufficient Contact Info', 'disciple_tools' ), __('Already In Church/Connected with Others', 'disciple_tools' ), __('No Longer Interested', 'disciple_tools' ), __('Just wanted a book', 'disciple_tools' ), __('Unknown', 'disciple_tools' )),
            'section' => 'misc'
        );
		$fields['source_details'] = array(
			'name' => __( 'Source Details', 'disciple_tools'),
			'description' => '',
			'type' => 'text',
			'default' => '',
			'section' => 'misc'
		);
		return apply_filters( 'dt_custom_fields_settings', $fields );
	} // End get_custom_fields_settings()

    /**
     * Field: Contact Fields
     * @return array
     */
    public function contact_fields () {
	    global $wpdb, $post;

	    $fields = array();
	    $current_fields = array();

	    if (isset($post->ID)){
            $current_fields = $wpdb->get_results( "SELECT meta_key FROM wp_postmeta WHERE post_id = $post->ID AND meta_key LIKE 'contact_%' ORDER BY meta_key DESC", ARRAY_A );
	    }

        foreach ($current_fields as $value) {
            $names = explode('_', $value['meta_key']);
            $tag = null;

            if ($names[1] != $names[2]) { $tag = ' ('. $names[2] . ')'; }

            $fields[$value['meta_key']] = array(
                'name' => $names[1] . $tag,
                'tag' => $names[1],
            );
        }
        return $fields;
    }

    /**
     * Field: Contact Fields
     * @return array
     */
    public function address_fields () {
	    global $wpdb, $post;

	    $fields = array();
	    $current_fields = array();

	    if (isset($post->ID)){
            $current_fields = $wpdb->get_results( "SELECT meta_key FROM wp_postmeta WHERE post_id = $post->ID AND meta_key LIKE 'address_%' ORDER BY meta_key DESC", ARRAY_A );
	    }

        foreach ($current_fields as $value) {
            $names = explode('_', $value['meta_key']);

            $fields[$value['meta_key']] = array(
                'name' => $names[1] ,
            );
        }
        return $fields;
    }

    /**
     * Add Contact fields html for adding a new contact channel
     * @usage Added to the bottom of the Contact Details Metabox.
     */
    public function add_new_contact_field () {

        $html = '<p><a href="javascript:void(0);" onclick="jQuery(\'#new-fields\').toggle();"><strong>+ Contact Detail</strong></a></p>';
        $html .= '<table class="form-table" id="new-fields" style="display: none;"><tbody>' . "\n";

        $channels = $this->get_channels_list('contact');

        $html .= '<tr><th>
                <select name="new-key-contact" class="edit-input"><option value=""></option> ';
                        foreach ($channels as $channel) {

                            $key =  $this->create_channel_metakey($channel, 'contact'); // build key
                            $names = explode("_", $key); // separates primary name from type tag

                            $html .= '<option value="'.$key.'">'.$names[1];
                            if($names[1] != $names[2]) { $html .= '  (' . $names[2] . ')'; }
                            $html .= '</option>';
                        }
        $html .= '</select></th>';

        $html .= '<td><input type="text" name="new-value-contact" id="new-value" class="edit-input" /></td><td><button type="submit" class="button">Save</button></td></tr>';

        $html .= '</tbody></table>';
        return $html;

    }

    /**
     * Add Address fields html for adding a new contact channel
     * @usage Added to the bottom of the Contact Details Metabox.
     */
    public function add_new_address_field () {

        $html = '<p><a href="javascript:void(0);" onclick="jQuery(\'#new-address\').toggle();"><strong>+ Address Detail</strong></a></p>';
        $html .= '<table class="form-table" id="new-address" style="display: none;"><tbody>' . "\n";

        $channels = $this->get_channels_list('address');

        $html .= '<tr><th>
                <select name="new-key-address" class="edit-input"><option value=""></option> ';
        foreach ($channels as $channel) {

            $key =  $this->create_channel_metakey($channel, 'address'); // build key
            $names = explode("_", $key); // separates primary name from type tag

            $html .= '<option value="'.$key.'">'.$names[1] . '</option>';
        }
        $html .= '</select></th>';
        $html .= '<td><textarea type="text" name="new-value-address" id="new-address" class="edit-input" ></textarea></td><td><button type="submit" class="button">Save</button></td></tr>';

        $html .= '</tbody></table>';
        return $html;
    }

    /**
     * Helper function to create the unique metakey for contacts channels.
     * @param $channel
     * @return string
     */
    public function create_channel_metakey ($channel, $type) {
        return $type . '_' . $channel . '_' . $this->unique_hash(); // build key
    }

    public function unique_hash() {
        return substr(md5(rand(10000, 100000)), 0, 3); // create a unique 3 digit key
    }

    /**
     * Selectable values for different channels of contact information.
     * @return array
     */
    public function get_channels_list ($type = 'contact') {

        switch ($type) {
            case 'contact':
                $channels = array(
                    __('Phone', 'disciple_tools') . '_' . __('Primary', 'disciple_tools'),
                    __('Phone', 'disciple_tools') . '_' . __('Mobile', 'disciple_tools'),
                    __('Phone', 'disciple_tools') . '_' . __('Work', 'disciple_tools'),
                    __('Phone', 'disciple_tools') . '_' . __('Home', 'disciple_tools'),
                    __('Phone', 'disciple_tools') . '_' . __('Other', 'disciple_tools'),
                    __('Email', 'disciple_tools') . '_' . __('Primary', 'disciple_tools'),
                    __('Email', 'disciple_tools') . '_' . __('Work', 'disciple_tools'),
                    __('Email', 'disciple_tools') . '_' . __('Other', 'disciple_tools'),
                    __('Facebook', 'disciple_tools'). '_' . __('Facebook', 'disciple_tools'),
                    __('Twitter', 'disciple_tools'). '_' . __('Twitter', 'disciple_tools'),
                    __('Instagram', 'disciple_tools'). '_' . __('Instagram', 'disciple_tools'),
                    __('Skype', 'disciple_tools'). '_' . __('Skype', 'disciple_tools'),
                    __('Other', 'disciple_tools'). '_' . __('Other', 'disciple_tools'),
                );
                return $channels;
                break;
            case 'address':
                $addresses = array(
                    __('Home', 'disciple_tools'),
                    __('Work', 'disciple_tools'),
                    __('Other', 'disciple_tools'),
                );
                return $addresses;
                break;
            default:
                break;
        }

    }

    /**
     * Field: The 'Assigned To' dropdown controller
     * @return string
     */
    public function assigned_to_field () {
        global $post;

        $exclude_group = '';
        $exclude_user = '';
        $html = '';


        // Start drop down
        $html .= '<select name="assigned_to" id="assigned_to" class="edit-input">';

        // Set selected state
	    if (isset($post->ID)){
            $assigned_to = get_post_meta( $post->ID, 'assigned_to', true);
	    }

        if(empty( $assigned_to) || $assigned_to == 'dispatch' ) {
            // set default to dispatch
            $html .= '<option value="dispatch" selected>Dispatch</option>';
        }
        elseif ( !empty( $assigned_to ) ) { // If there is already a record
            $metadata = get_post_meta($post->ID, 'assigned_to', true);
            $meta_array = explode('-', $metadata); // Separate the type and id
            $type = $meta_array[0]; // Build variables
            $id = $meta_array[1];

            // Build option for current value
            if ( $type == 'user') {
                $value = get_user_by( 'id', $id);
                $html .= '<option value="user-'.$id.'" selected>'.$value->display_name.'</option>';

                // exclude the current id from the $results list
                $exclude_user = "'exclude' => $id";
            } else {
                $value = get_term( $id);
                $html .= '<option value="team-'.$value->term_id.'" selected>'.$value->name.'</option>';

                // exclude the current id from the $results list
                $exclude_group = "'exclude' => $id";
            }

            $html .= '<option value="" disabled> --- Dispatch</option><option value="dispatch">Dispatch</option>'; // add dispatch to top of list

        }

        // Visually categorize groups
        $html .= '<option value="" disabled> --- Teams</option>';

        // Get groups list excluding current selection
        $results = get_terms( array( 'taxonomy' => 'user-group', 'hide_empty' => true, 'exclude' => $exclude_group ) );

        // Loop list of groups list
        foreach ($results as $value) {
            $html .= '<option value="group-'.$value->term_id.'">'.$value->name.'</option>';
        }

        // Visually separate groups from users
        $html .= '<option value="" disabled> --- Users</option>';

        // Collect user list
        $args = array('role__not_in' => array('registered', 'prayer_supporter', 'project_supporter'), 'fields' => array('ID', 'display_name'), 'exclude' => $exclude_user );
        $results = get_users($args);

        // Loop user list
        foreach ($results as $value) {
            $html .= '<option value="user-'.$value->ID.'">'.$value->display_name.'</option>';
        }

        // End drop down
        $html .= '</select>  ';

        return $html;
    }

    /**
     * Customise the "Enter title here" text.
     * @access public
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
	 * Run on activation.
	 * @access public
	 * @since 0.1
	 */
	public function activation () {
		$this->flush_rewrite_rules();
	} // End activation()

	/**
	 * Flush the rewrite rules
	 * @access public
	 * @since 0.1
	 */
	private function flush_rewrite_rules () {
		$this->register_post_type();
		flush_rewrite_rules();
	} // End flush_rewrite_rules()


				
				
} // End Class