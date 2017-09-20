<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

/**
 * Disciple_Tools Plugin Contacts Post Type Class
 *
 * All functionality pertaining to contacts post types in Disciple_Tools.
 *
 * @package  Disciple_Tools
 * @category Plugin
 * @author   Chasm.Solutions & Kingdom.Training
 * @since    0.1
 */
class Disciple_Tools_Contact_Post_Type {
    /**
     * The post type token.
     *
     * @access public
     * @since  0.1
     * @var    string
     */
    public $post_type;

    /**
     * The post type singular label.
     *
     * @access public
     * @since  0.1
     * @var    string
     */
    public $singular;

    /**
     * The post type plural label.
     *
     * @access public
     * @since  0.1
     * @var    string
     */
    public $plural;

    /**
     * The post type args.
     *
     * @access public
     * @since  0.1
     * @var    array
     */
    public $args;

    /**
     * The taxonomies for this post type.
     *
     * @access public
     * @since  0.1
     * @var    array
     */
    public $taxonomies;


    /**
     * Disciple_Tools_Admin_Menus The single instance of Disciple_Tools_Admin_Menus.
     *
     * @var    object
     * @access private
     * @since  0.1
     */
    private static $_instance = null;

    /**
     * Main Disciple_Tools_Contact_Post_Type Instance
     *
     * Ensures only one instance of Disciple_Tools_Contact_Post_Type is loaded or can be loaded.
     *
     * @since  0.1
     * @static
     * @return Disciple_Tools_Contact_Post_Type instance
     */
    public static function instance () {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    /**
     * Constructor function.
     *
     * @access public
     * @since  0.1
     */
    public function __construct( $post_type = 'contacts', $singular = '', $plural = '', $args = [], $taxonomies = [] ) {
        $this->post_type = 'contacts';
        $this->singular = 'Contact';
        $this->plural = 'Contacts';
        $this->args = [ 'menu_icon' => dt_svg_icon() ];
        $this->taxonomies = $taxonomies = [];

        add_action( 'init', [ $this, 'register_post_type' ] );
        //		add_action( 'init', array( $this, 'register_taxonomy' ) );
        add_action( 'init', [$this, 'contacts_rewrites_init'] );
        add_filter( 'post_type_link', [$this,'contacts_permalink'], 1, 3 );

        if ( is_admin() ) {
            global $pagenow;

            add_action( 'save_post', [ $this, 'meta_box_save' ] );
            add_filter( 'enter_title_here', [ $this, 'enter_title_here' ] );
            add_filter( 'post_updated_messages', [ $this, 'updated_messages' ] );
        }


    } // End __construct()

    /**
     * Register the post type.
     *
     * @access public
     * @return void
     */
    public function register_post_type () {
        $labels = [
            'name'                  => sprintf( _x( '%s', 'Contacts', 'disciple_tools' ), $this->plural ),
            'singular_name'         => sprintf( _x( '%s', 'Contact', 'disciple_tools' ), $this->singular ),
            'add_new'               => _x( 'Add New', $this->post_type, 'disciple_tools' ),
            'add_new_item'          => sprintf( __( 'Add New %s', 'disciple_tools' ), $this->singular ),
            'edit_item'             => sprintf( __( 'Edit %s', 'disciple_tools' ), $this->singular ),
            'update_item'           => sprintf( __( 'Update %s', 'disciple_tools' ), $this->singular ),
            'new_item'              => sprintf( __( 'New %s', 'disciple_tools' ), $this->singular ),
            'all_items'             => sprintf( __( 'All %s', 'disciple_tools' ), $this->plural ),
            'view_item'             => sprintf( __( 'View %s', 'disciple_tools' ), $this->singular ),
            'view_items'            => sprintf( __( 'View %s', 'disciple_tools' ), $this->plural ),
            'search_items'          => sprintf( __( 'Search %a', 'disciple_tools' ), $this->plural ),
            'not_found'             => sprintf( __( 'No %s Found', 'disciple_tools' ), $this->plural ),
            'not_found_in_trash'    => sprintf( __( 'No %s Found In Trash', 'disciple_tools' ), $this->plural ),
            'parent_item_colon'     => '',
            'menu_name'             => $this->plural,
            'featured_image'        => sprintf( __( 'Featured Image', 'disciple_tools' ), $this->plural ),
            'set_featured_image'    => sprintf( __( 'Set featured image', 'disciple_tools' ), $this->plural ),
            'remove_featured_image' => sprintf( __( 'Remove featured image', 'disciple_tools' ), $this->plural ),
            'use_featured_image'    => sprintf( __( 'Use as featured image', 'disciple_tools' ), $this->plural ),
            'insert_into_item'      => sprintf( __( 'Placed into %s', 'disciple_tools' ), $this->plural ),
            'uploaded_to_this_item' => sprintf( __( 'Uploaded to this %s', 'disciple_tools' ), $this->plural ),
            'items_list'            => sprintf( __( '%s list', 'disciple_tools' ), $this->plural ),
            'items_list_navigation' => sprintf( __( '%s list navigation', 'disciple_tools' ), $this->plural ),
            'filter_items_list'     => sprintf( __( 'Filter %s list', 'disciple_tools' ), $this->plural ),
        ];
        $rewrite = [
            'slug'                  => 'contacts',
            'with_front'            => true,
            'pages'                 => true,
            'feeds'                 => false,
        ];
        $capabilities = [
            'edit_post'             => 'access_contacts',
            'read_post'             => 'access_contacts',
            'delete_post'           => 'delete_any_contact',
            'delete_others_posts'   => 'delete_any_contact',
            'delete_posts'          => 'delete_any_contact',
            'edit_posts'            => 'access_contacts',
            'edit_others_posts'     => 'update_any_contact',
            'publish_posts'         => 'create_contacts',
            'read_private_posts'    => 'view_any_contact',
        ];
        $defaults = [
            'label'                 => __( 'Contact', 'disciple_tools' ),
            'description'           => __( 'Contacts generated by the media to movement effort', 'disciple_tools' ),
            'labels'                => $labels,
            'public'                => true,
            'publicly_queryable'    => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'query_var'             => true,
            'rewrite'               => $rewrite,
            'capabilities'          => $capabilities,
            'capability_type'       => 'contact',
            'has_archive'           => true, //$archive_slug,
            'hierarchical'          => false,
            'supports'              => [ 'title', 'comments' ],
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-groups',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'exclude_from_search'   => false,
            'show_in_rest'          => true,
            'register_meta_box_cb'  => [$this, 'meta_box_setup'],
            'rest_base'             => 'contacts',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        ];

        $args = wp_parse_args( $this->args, $defaults );

        register_post_type( $this->post_type, $args );
    } // End register_post_type()

    /**
     * Register the "contacts-category" taxonomy.
     *
     * @access public
     * @since  1.3.0
     * @return void
     */
    public function register_taxonomy () {
        $this->taxonomies['contacts-type'] = new Disciple_Tools_Taxonomy( $post_type = 'contacts', $token = 'contacts-type', $singular = 'Type', $plural = 'Type', $args = [] ); // Leave arguments empty, to use the default arguments.
        $this->taxonomies['contacts-type']->register();
    } // End register_taxonomy()

    /**
     * Update messages for the post type admin.
     *
     * @since  0.1
     * @param  array $messages Array of messages for all post types.
     * @return array           Modified array.
     */
    public function updated_messages ( $messages ) {
        global $post, $post_ID;

        $messages[$this->post_type] = [
            0 => '', // Unused. Messages start at index 1.
            1 => sprintf( __( '%3$s updated. %sView %4$s%s', 'disciple_tools' ), '<a href="' . esc_url( get_permalink( $post_ID ) ) . '">', '</a>', $this->singular, strtolower( $this->singular ) ),
            2 => __( 'Contact updated.', 'disciple_tools' ),
            3 => __( 'Contact deleted.', 'disciple_tools' ),
            4 => sprintf( __( '%s updated.', 'disciple_tools' ), $this->singular ),
            /* translators: %s: date and time of the revision */
            5 => isset( $_GET['revision'] ) ? sprintf( __( '%s restored to revision from %s', 'disciple_tools' ), $this->singular, wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
            6 => sprintf( __( '%1$s published. %3$sView %2$s%4$s', 'disciple_tools' ), $this->singular, strtolower( $this->singular ), '<a href="' . esc_url( get_permalink( $post_ID ) ) . '">', '</a>' ),
            7 => sprintf( __( '%s saved.', 'disciple_tools' ), $this->singular ),
            8 => sprintf( __( '%s submitted. %sPreview %s%s', 'disciple_tools' ), $this->singular, strtolower( $this->singular ), '<a target="_blank" href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) . '">', '</a>' ),
            9 => sprintf(
                __( '%s scheduled for: %1$s. %2$sPreview %s%3$s', 'disciple_tools' ), $this->singular, strtolower( $this->singular ),
                // translators: Publish box date format, see http://php.net/date
                '<strong>' . date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ) . '</strong>', '<a target="_blank" href="' . esc_url( get_permalink( $post_ID ) ) . '">', '</a>'
            ),
            10 => sprintf( __( '%s draft updated. %sPreview %s%s', 'disciple_tools' ), $this->singular, strtolower( $this->singular ), '<a target="_blank" href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) . '">', '</a>' ),
        ];

        return $messages;
    } // End updated_messages()

    /**
     * Setup the meta box.
     *
     * @access public
     * @since  0.1
     * @return void
     */
    public function meta_box_setup () {
        add_meta_box( $this->post_type . '_details', __( 'Contact Details', 'disciple_tools' ), [ $this, 'load_contact_info_meta_box' ], $this->post_type, 'normal', 'high' );
        add_meta_box( $this->post_type . '_address', __( 'Address', 'disciple_tools' ), [ $this, 'load_address_info_meta_box' ], $this->post_type, 'normal', 'high' );
        add_meta_box( $this->post_type . '_activity', __( 'Activity', 'disciple_tools' ), [ $this, 'load_activity_meta_box' ], $this->post_type, 'normal', 'low' );
        add_meta_box( $this->post_type . '_path', __( 'Milestones', 'disciple_tools' ), [ $this, 'load_milestone_meta_box' ], $this->post_type, 'side', 'low' );
        add_meta_box( $this->post_type . '_misc', __( 'Misc', 'disciple_tools' ), [ $this, 'load_misc_meta_box' ], $this->post_type, 'side', 'low' );
        add_meta_box( $this->post_type . '_sharing', __( 'Sharing', 'disciple_tools' ), [ $this, 'load_shared_meta_box' ], $this->post_type, 'normal' );
        add_meta_box( $this->post_type . '_status', __( 'Status', 'disciple_tools' ), [ $this, 'load_status_info_meta_box' ], $this->post_type, 'side' );
        do_action( "dt_contact_meta_boxes_setup", $this->post_type );
    } // End meta_box_setup()

    /**
     * The contents of meta box.
     *
     * @access public
     * @since  0.1
     * @return mixed
     */
    public function meta_box_content ( $section = 'info' ) {
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
                            $html .= '<tr valign="top" id="'. esc_attr( $k )  .'"><th scope="row"><label for="' . esc_attr( $k ) . '">' . esc_attr( $v['name'] ) . '</label></th>
                                <td><input name="' . esc_attr( $k ) . '" type="text" id="' . esc_attr( $k ) . '" class="regular-text" value="' . esc_attr( $data ) . '" />' . "\n";
                            $html .= '<p class="description">' . esc_attr( $v['description'] ) . '</p>' . "\n";
                            $html .= '</td><tr/>' . "\n";
                            break;
                        case 'textarea':
                            $html .= '<tr valign="top"><th scope="row"><label for="' . esc_attr( $k ) . '">' . esc_attr( $v['name'] ) . '</label></th>
                                <td><textarea name="' . esc_attr( $k ) . '" type="text" id="' . esc_attr( $k ) . '" class="regular-text"  >' . esc_attr( $data ) . '</textarea>' . "\n";
                            $html .= '<p class="description">' . esc_attr( $v['description'] ) . '</p>' . "\n";
                            $html .= '</td><tr/>' . "\n";
                            break;
                        case 'select':
                            $html .= '<tr valign="top"><th scope="row">
                                <label for="' . esc_attr( $k ) . '">' . esc_attr( $v['name'] ) . '</label></th>
                                <td>
                                <select name="' . esc_attr( $k ) . '" id="' . esc_attr( $k ) . '" class="regular-text">';
                            // Iterate the options
                            foreach ($v['default'] as $vv) {
                                $html .= '<option value="' . $vv . '" ';
                                if($vv == $data) { $html .= 'selected';}
                                $html .= '>' . esc_attr( $vv ) . '</option>';
                            }
                            $html .= '</select>' . "\n";
                            $html .= '<p class="description">' . $v['description'] . '</p>' . "\n";
                            $html .= '</td><tr/>' . "\n";
                            break;
                        case 'key_select':
                            $html .= '<tr valign="top"><th scope="row">
                                <label for="' . esc_attr( $k ) . '">' . esc_attr( $v['name'] )  . '</label></th>
                                <td>
                                <select name="' . esc_attr( $k ) . '" id="' . esc_attr( $k ) . '" class="regular-text">';
                            // Iterate the options
                            foreach ($v['default'] as $kk => $vv) {
                                $html .= '<option value="' . $kk . '" ';
                                if($kk == $data) { $html .= 'selected';}
                                $html .= '>' . esc_attr( $vv ) . '</option>';
                            }
                            $html .= '</select>' . "\n";
                            $html .= '<p class="description">' . esc_attr( $v['description'] ) . '</p>' . "\n";
                            $html .= '</td><tr/>' . "\n";
                            break;
                        case 'radio':
                            $html .= '<tr valign="top"><th scope="row">' . esc_attr( $v['name'] ) . '</th>
                                <td><fieldset>';
                            // Iterate the buttons
                            $increment_the_radio_button = 1;
                            foreach ($v['default'] as $vv) {
                                $html .= '<label for="' . esc_attr( "$k-$increment_the_radio_button" ) . '">' . esc_attr( $vv ) . '</label>
                                    <input class="dt-radio" type="radio" name="'.esc_attr( $k ).'" id="'.$k.'-'.$increment_the_radio_button.'" value="'.$vv.'" ';
                                if($vv == $data) { $html .= 'checked';}
                                $html .= '>';
                                $increment_the_radio_button++;
                            }
                            $html .= '</fieldset>' . "\n";
                            $html .= '<p class="description">' . $v['description'] . '</p>' . "\n";
                            $html .= '</td><tr/>' . "\n";
                            break;
                        case 'checkbox':
                            $html .= '<tr valign="top"><th scope="row"><label for="' . esc_attr( $k ) . '" class="selectit">' . esc_attr( $v['name'] ) . '</label></th><td>

                                <input name="' . esc_attr( $k ) . '" type="checkbox" id="' . esc_attr( $k ) . '" value="' ;

                            if($data) { $html .=  esc_attr( $data ) . '" checked="checked"/>';
                            } else { $html .= '"/>'; }

                            $html .= '<p class="description">' . $v['description'] . '(' . $v   . ')</p>' . "\n";
                            $html .= '</td><tr/>' . "\n";
                            break;
                        case 'custom':
                            $html .= '<tr valign="top"><th scope="row"><label for="' . esc_attr( $k ) . '" class="selectit">' . esc_attr( $v['name'] ) . '</label></th><td>';
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
     *
     * @access public
     * @since  0.1
     * @param  int $post_id
     * @return int $post_id
     */
    public function meta_box_save ( $post_id ) {
        global $post, $messages;

        // Verify
        if (  get_post_type() != $this->post_type  ) {
            return $post_id;
        }
        if ( isset( $_POST['dt_' . $this->post_type . '_noonce'] ) && ! wp_verify_nonce( $_POST['dt_' . $this->post_type . '_noonce'], 'update_dt_contacts' ) ) {
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

        if ( isset( $_GET['action'] ) ) {
            if ( $_GET['action'] == 'trash' || $_GET['action'] == 'untrash' || $_GET['action'] == 'delete' ) {
                return $post_id;
            }
        }

        $field_data = $this->get_custom_fields_settings();
        $fields = array_keys( $field_data );

        if ( (isset( $_POST['new-key-address'] ) && !empty( $_POST['new-key-address'] ) ) && (isset( $_POST['new-value-address'] ) && !empty( $_POST['new-value-address'] ) ) ) { // catch and prepare new contact fields
            $k = explode( "_",  $_POST['new-key-address'] );
            $type = $k[1];
            $number_key = dt_address_metabox()->create_channel_metakey( "address" );
            $details_key = $number_key . "_details";
            $details = ['type'=>$type, 'verified'=>false];
            //save the field and the field details
            add_post_meta( $post_id, strtolower( $number_key ), $_POST['new-value-address'], true );
            add_post_meta( $post_id, strtolower( $details_key ), $details, true );
        }

        if ( (isset( $_POST['new-key-contact'] ) && !empty( $_POST['new-key-contact'] ) ) && (isset( $_POST['new-value-contact'] ) && !empty( $_POST['new-value-contact'] ) ) ) { // catch and prepare new contact fields
            $k = explode( "_",  $_POST['new-key-contact'] );
            $channel = $k[0];
            $type = $k[1];
            $number_key = $this->create_channel_metakey( $channel, "contact" );
            $details_key = $number_key . "_details";
            $details = ['type'=>$type, 'verified'=>false];
            //save the field and the field details
            add_post_meta( $post_id, strtolower( $number_key ), $_POST['new-value-contact'], true );
            add_post_meta( $post_id, strtolower( $details_key ), $details, true );
        }
        
        foreach ( $fields as $f ) {

            if ( isset( $_POST[$f] )){
                ${$f} = strip_tags( trim( $_POST[$f] ) );

                if ( get_post_meta( $post_id,  $f ) == '' ) {
                    add_post_meta( $post_id,  $f, ${$f}, true );
                } elseif ( ${$f} == '' ) {
                    delete_post_meta( $post_id, $f, get_post_meta( $post_id,  $f, true ) );
                } elseif( ${$f} != get_post_meta( $post_id, $f, true ) ) {
                    update_post_meta( $post_id, $f, ${$f} );
                }
            }
        }

    } // End meta_box_save()

    /**
     * Load activity metabox
     */
    public function load_activity_meta_box () {
        dt_activity_metabox()->activity_meta_box( get_the_ID() );
    }

    /**
     * Load activity metabox
     */
    public function load_shared_meta_box() {
        dt_share_contact_metabox()->content_display( get_the_ID() );
    }

    /**
     * Meta box for Status Information
     *
     * @access public
     * @since  0.1
     */
    public function load_milestone_meta_box () {
        echo '' . $this->meta_box_content( 'milestone' );
    }

    /**
     * Meta box for Status Information
     *
     * @access public
     * @since  0.1
     */
    public function load_contact_info_meta_box () {
        echo ''. $this->meta_box_content( 'info' );
        echo ''. $this->add_new_contact_field();
    }

    /**
     * Meta box for Status Information
     *
     * @access public
     * @since  0.1
     */
    public function load_address_info_meta_box () {
        echo ''. $this->meta_box_content( 'address' );
        echo ''. dt_address_metabox()->add_new_address_field();
    }

    /**
     * Meta box for Status Information
     *
     * @access public
     * @since  0.1
     */
    public function load_status_info_meta_box () {
        echo ''. $this->meta_box_content( 'status' );
    }

    /**
     * Meta box for Status Information
     *
     * @access public
     * @since  0.1
     */
    public function load_misc_meta_box () {
        echo ''. $this->meta_box_content( 'misc' );
    }

    /**
     * Get the settings for the custom fields.
     *
     * @access public
     * @since  0.1
     * @return array
     */
    public function get_custom_fields_settings( bool $include_current_post = true, int $post_id = null ) {
        global $post;
        $fields = [];

        // Status Section
        $fields['assigned_to'] = [
            'name' => __( 'Assigned To', 'disciple_tools' ),
            'description' => '',
            'type' => 'custom',
            'default' => $this->assigned_to_field(),
            'section' => 'status'
        ];
        $fields['overall_status'] = [
            'name' => __( 'Overall Status', 'disciple_tools' ),
            'description' => '',
            'type' => 'key_select',
            'default' => [
                'unassigned' => __( 'Unassigned', 'disciple_tools' ),
                'assigned' => __( "Assigned", 'disciple_tools' ),
                'active' => __( 'Active', 'disciple_tools' ),
                'paused' => __( 'Paused', 'disciple_tools' ),
                'closed' => __( 'Closed', 'disciple_tools' ),
                'unassignable' => __( 'Unassignable', 'disciple_tools' )
            ],
            'section' => 'status'
        ];

        //these fields must stay in order of importance
        $fields['seeker_path'] = [
            'name' => __( 'Seeker Path', 'disciple_tools' ),
            'description' => '',
            'type' => 'key_select',
            'default' => [
                'none' => __( 'No Action', 'disciple_tools' ),
                'attempted' => __( 'Contact Attempted', 'disciple_tools' ),
                'established' => __( 'Contact Established', 'disciple_tools' ),
                'scheduled' => __( 'Meeting Scheduled', 'disciple_tools' ),
                'met' => __( 'First Meeting Complete', 'disciple_tools' ),
                'ongoing' => __( 'Ongoing Meetings', 'disciple_tools' ),
                'coaching' => __( 'Being Coached', 'disciple_tools' )
            ],
            'section' => 'status'
        ];
        $fields['requires_update'] = [
            'name' => __( 'Requires Update', 'disciple_tools' ),
            'description' => '',
            'type' => 'key_select',
            'default' => ['no' => __( 'No', 'disciple_tools' ), 'yes' => __( 'Yes', 'disciple_tools' )],
            'section' => 'status'
        ];

        $id = $post->ID ?? $post_id;
        if ( $include_current_post && ( $id || ( isset( $post->ID ) && $post->post_status != 'auto-draft' ))) { // if being called for a specific record or new record.
            // Contact Channels Section
            $methods = $this->contact_fields( $id );
            foreach ($methods as $k => $v) { // sets phone numbers as first
                $keys = explode( '_', $k );
                if($keys[1] == 'phone') {
                    $fields[$k] = [
                        'name' => ucwords( $v['name'] ),
                        'description' => '',
                        'type' => 'text',
                        'default' => '',
                        'section' => 'info'
                    ];
                }
            }

            foreach ($methods as $k => $v) { // sets emails as second
                $keys = explode( '_', $k );
                if($keys[1] == 'email') {
                    $fields[$k] = [
                        'name' => ucwords( $v['name'] ),
                        'description' => '',
                        'type' => 'text',
                        'default' => '',
                        'section' => 'info'
                    ];
                }
            }
            foreach ($methods as $k => $v) { // sets all others third
                $keys = explode( '_', $k );
                if($keys[1] != 'email' && $keys[1] != 'phone' ) {
                    $fields[$k] = [
                        'name' => ucwords( $v['name'] ),
                        'description' => '',
                        'type' => 'text',
                        'default' => '',
                        'section' => 'info'
                    ];
                }
            }

            foreach ($methods as $k => $v) { // address
                $keys = explode( '_', $k );
                if($keys[0] == 'address_' && sizeof( $keys ) === 2 ) {
                    $fields[$k] = [
                        'name' => ucwords( $v['name'] ),
                        'description' => '',
                        'type' => 'text',
                        'default' => '',
                        'section' => 'info'
                    ];
                }
            }

            // Address
            $addresses = dt_address_metabox()->address_fields( $id );
            foreach ($addresses as $k => $v) { // sets all others third
                $fields[$k] = [
                    'name' => ucwords( $v['name'] ),
                    'description' => '',
                    'type' => 'text',
                    'default' => '',
                    'section' => 'address'
                ];
            }


        } else {
            // TODO: I don't understand why this is only run when we're not
            // getting info for the current post
//            I commented this code out because it appears it is not being used


//            $channels = $this->get_channels_list();

//            foreach ($channels as $channel_key => $channel) {
//                foreach($channel["types"] as $type_key => $type){
//
//                    $tag = null;
//
//                    $key =  strtolower( 'contact_' . $channel_key . '_' . $type_key . '_111' );
//
//                    if($channel["label"] != $type["label"]) { $tag = ' ('. ucwords( $type["label"] ) . ')'; }
//
//                    $fields[$key] = [
//                        'name' => ucwords( $channel["label"] ) . $tag,
//                        'description' => '',
//                        'type' => 'text',
//                        'default' => '',
//                        'section' => 'info'
//                    ];
//                }
//            }

//            $address_types = dt_address_metabox()->get_address_type_list( $this->post_type );

//            foreach ($address_types as $key => $type) {
//
//                $key =  strtolower( 'address_11' . $index );
//
//                $fields[$key] = [
//                    'name' => ucwords( $type ) ,
//                    'description' => '',
//                    'type' => 'text',
//                    'default' => '',
//                    'section' => 'address'
//                ];
//            }
        }


        // Status information section
        $fields['milestone_belief'] = [
            'name' => __( 'States Belief', 'disciple_tools' ),
            'description' => '',
            'type' => 'key_select',
            'default' => ['no' => __( 'No', 'disciple_tools' ), 'yes' => __( 'Yes', 'disciple_tools' )],
            'section' => 'milestone'
        ];
        $fields['milestone_can_share'] = [
            'name' => __( 'Can Share Gospel/Testimony', 'disciple_tools' ),
            'description' => '',
            'type' => 'key_select',
            'default' => ['no' => __( 'No', 'disciple_tools' ), 'yes' => __( 'Yes', 'disciple_tools' )],
            'section' => 'milestone'
        ];
        $fields['milestone_sharing'] = [
            'name' => __( 'Sharing Gospel/Testimony', 'disciple_tools' ),
            'description' => '',
            'type' => 'key_select',
            'default' => ['no' => __( 'No', 'disciple_tools' ), 'yes' => __( 'Yes', 'disciple_tools' )],
            'section' => 'milestone'
        ];
        $fields['milestone_baptized'] = [
            'name' => __( 'Baptized', 'disciple_tools' ),
            'description' => '',
            'type' => 'key_select',
            'default' => ['no' => __( 'No', 'disciple_tools' ), 'yes' => __( 'Yes', 'disciple_tools' )],
            'section' => 'milestone'
        ];
        $fields['milestone_baptizing'] = [
            'name' => __( 'Baptizing', 'disciple_tools' ),
            'description' => '',
            'type' => 'key_select',
            'default' => ['no' => __( 'No', 'disciple_tools' ), 'yes' => __( 'Yes', 'disciple_tools' )],
            'section' => 'milestone'
        ];
        $fields['milestone_in_group'] = [
            'name' => __( 'In Church/Group', 'disciple_tools' ),
            'description' => '',
            'type' => 'key_select',
            'default' => ['no' => __( 'No', 'disciple_tools' ), 'yes' => __( 'Yes', 'disciple_tools' )],
            'section' => 'milestone'
        ];
        $fields['milestone_planting'] = [
            'name' => __( 'Starting Churches', 'disciple_tools' ),
            'description' => '',
            'type' => 'key_select',
            'default' => ['no' => __( 'No', 'disciple_tools' ), 'yes' => __( 'Yes', 'disciple_tools' )],
            'section' => 'milestone'
        ];


        // Misc Information fields
        $fields['bible'] = [
            'name' => __( 'Bible', 'disciple_tools' ),
            'description' => '',
            'type' => 'key_select',
            'default' => [
                'not-set' => '',
                'gives' => __( 'Yes - given by hand', 'disciple_tools' ),
                'has' => __( 'Yes - already had one', 'disciple_tools' ),
                'received' => __( 'Yes - receipt by mail confirmed', 'disciple_tools' ),
                'mailed' => __( 'Bible mailed', 'disciple_tools' ),
                'requested' => __( 'Needs / Requests Bible', 'disciple_tools' )
            ],
            'section' => 'misc'
        ];
        $fields['gender'] = [
            'name' => __( 'Gender', 'disciple_tools' ),
            'description' => '',
            'type' => 'key_select',
            'default' => [
                'not-set' => '',
                'male' => __( 'Male', 'disciple_tools' ),
                'female' => __( 'Female', 'disciple_tools' )
            ],
            'section' => 'misc'
        ];
        $fields['age'] = [
            'name' => __( 'Age', 'disciple_tools' ),
            'description' => '',
            'type' => 'key_select',
            'default' => [
                'not-set' => '',
                '<19' => __( 'Under 18 years old', 'disciple_tools' ),
                '<26' => __( '18-25 years old', 'disciple_tools' ),
                '<41' => __( '26-40 years old', 'disciple_tools' ),
                '>41' => __( 'Over 40 years old', 'disciple_tools' )
            ],
            'section' => 'misc'
        ];

        $fields["reason_unassignable"] = [
            'name' => __( 'Reason Unassaginable' ),
            'description' => '',
            'type' => 'key_select',
            'default' => [
                'none' => '',
                'insufficient' => __( 'Insufficient Contact Information' ),
                'location' => __( 'Unknown Location' ),
                'media' => __( 'Only wants media' ),
                'outside_area' => __( 'Outside Area' ),
                'needs_review' => __( 'Needs Review' ),
            ],
            'section' => 'misc'
        ];

        $fields['reason_paused'] = [
            'name' => __( 'Reason Paused' ),
            'description' => '',
            'type' => 'key_select',
            'default' => [
                'none' => '',
                'vacation' => __( 'On Vacation', 'disciple_tools' ),
                'not-responding' => __( 'Not Responding', 'disciple_tools' )
            ],
            'section' => 'misc'
        ];

        $fields['reason_closed'] = [
            'name' => __( 'Reason Closed', 'disciple_tools' ),
            'description' => '',
            'type' => 'key_select',
            'default' => [
                'none' => '',
                'duplicate' => __( 'Duplicate', 'disciple_tools' ),
                'hostile' => __( 'Hostile', 'disciple_tools' ),
                'games' => __( 'Playing Games', 'disciple_tools' ),
                'insufficient' => __( 'Insufficient Contact Info', 'disciple_tools' ),
                'already-connected' => __( 'Already In Church/Connected with Others', 'disciple_tools' ),
                'no-longer-interested' => __( 'No Longer Interested', 'disciple_tools' ),
                'book-only' => __( 'Just wanted a book', 'disciple_tools' ),
                'unknown' => __( 'Unknown', 'disciple_tools' )],
            'section' => 'misc'
        ];

        $fields['accepted'] = [
            'name' => __( 'Accepted', 'disciple_tools' ),
            'description' => '',
            'type' => 'key_select',
            'default' => ['no' => __( 'No', 'disciple_tools' ), 'yes' => __( 'Yes', 'disciple_tools' )],
            'section' => 'status'
        ];

        $fields['source_details'] = [
            'name' => __( 'Source Details', 'disciple_tools' ),
            'description' => '',
            'type' => 'text',
            'default' => '',
            'section' => 'misc'
        ];

        // contact buttons
        $fields['quick_button_no_answer'] = [
            'name' => __( 'No Answer', 'disciple_tools' ),
            'description' => '',
            'type' => 'number',
            'default' => 0,
            'section' => 'quick_buttons',
            'icon' => "no-answer.svg"
        ];
        $fields['quick_button_phone_off'] = [
            'name' => __( 'Phone Off', 'disciple_tools' ),
            'description' => '',
            'type' => 'number',
            'default' => 0,
            'section' => 'quick_buttons',
            'icon' => "no-answer.svg"
        ];
        $fields['quick_button_contact_established'] = [
            'name' => __( 'Contact Established', 'disciple_tools' ),
            'description' => '',
            'type' => 'number',
            'default' => 0,
            'section' => 'quick_buttons',
            'icon' => "successful-conversation.svg"
        ];
        $fields['quick_button_meeting_scheduled'] = [
            'name' => __( 'Meeting Scheduled', 'disciple_tools' ),
            'description' => '',
            'type' => 'number',
            'default' => 0,
            'section' => 'quick_buttons',
            'icon' => "meeting-scheduled.svg"
        ];
        $fields['quick_button_meeting_complete'] = [
            'name' => __( 'Meeting Complete', 'disciple_tools' ),
            'description' => '',
            'type' => 'number',
            'default' => 0,
            'section' => 'quick_buttons',
            'icon' => "meeting-complete.svg"
        ];
        $fields['quick_button_no_show'] = [
            'name' => __( 'Meeting No-show', 'disciple_tools' ),
            'description' => '',
            'type' => 'number',
            'default' => 0,
            'section' => 'quick_buttons',
            'icon' => "no-show.svg"
        ];


        return apply_filters( 'dt_custom_fields_settings', $fields );
    } // End get_custom_fields_settings()

    /**
     * Field: Contact Fields
     *
     * @return array
     */
    public function contact_fields ( int $post_id ) {
        global $wpdb, $post;

        $fields = [];
        $current_fields = [];

        $id = $post->ID ?? $post_id;
        if (isset( $post->ID ) || isset( $post_id )){
            $current_fields = $wpdb->get_results(
                "SELECT meta_key
                FROM $wpdb->postmeta
                WHERE post_id = $id
                AND meta_key LIKE 'contact_%'
                ORDER BY meta_key DESC", ARRAY_A
            );
        }

        foreach ($current_fields as $value) {
            $names = explode( '_', $value['meta_key'] );
            $tag = null;

            if (strpos( $value["meta_key"], "_details" ) == false ){
                $details = get_post_meta( $id, $value['meta_key'] . "_details", true );
                if ($details && isset( $details["type"] )){
                    if ($names[1] != $details["type"]){
                        $tag =  ' ('. ucwords( $details["type"] ) . ')';
                    }
                }
                $fields[$value['meta_key']] = [
                    'name' => ucwords( $names[1] )  . $tag,
                    'tag' => $names[1],
                ];
            }
        }
        return $fields;
    }


    /**
     * Add Contact fields html for adding a new contact channel
     *
     * @usage Added to the bottom of the Contact Details Metabox.
     */
    public function add_new_contact_field () {

        $html = '<p><a href="javascript:void(0);" onclick="jQuery(\'#new-fields\').toggle();"><strong>+ Contact Detail</strong></a></p>';
        $html .= '<table class="form-table" id="new-fields" style="display: none;"><tbody>' . "\n";

        $channels = $this->get_channels_list();

        $html .= '<tr><th>
            <select name="new-key-contact" class="edit-input"><option value=""></option> ';
        foreach ($channels as $channel_key => $channel) {
            foreach ($channels[$channel_key]["types"] as $type_key => $type ) {
                $key = $channel_key . '_' . $type_key;

                $html .= '<option value="' . $key . '">' . $channel["label"];
                if ( $channel["label"] != $type["label"] ) {
                    $html .= '  (' . $type["label"] . ')';
                }
                $html .= '</option>';
            }
        }
        $html .= '</select></th>';

        $html .=
            '<td>
                <input type="text" name="new-value-contact" id="new-value" class="edit-input" />
            </td>
            <td>
                <button type="submit" class="button">Save</button>
            </td>
            </tr>';

        $html .= '</tbody></table>';
        return $html;

    }


    /**
     * Helper function to create the unique metakey for contacts channels.
     *
     * @param $channel_key
     * @param $field_type
     *
     * @return string
     */
    public function create_channel_metakey ( $channel_key, $field_type ) {
        return $field_type . '_' . $channel_key .'_' . $this->unique_hash(); // build key
    }

    public function unique_hash() {
        return substr( md5( rand( 10000, 100000 ) ), 0, 3 ); // create a unique 3 digit key
    }


    /**
     * Get a list of the contact channels and their types
     *
     * @access public
     * @since  0.1
     * @return mixed
     */
    public function get_channels_list(){
        $channelList = [
            "phone" => [
                "label" => __( 'Phone', 'disciple_tools' ),
                "types" => [
                    "primary" =>    ["label"=>__( 'Primary', 'disciple_tools' )],
                    "mobile" =>     ["label"=>__( 'Mobile', 'disciple_tools' )],
                    "work" =>       ["label"=>__( 'Work', 'disciple_tools' )],
                    "home" =>       ["label"=>__( 'Home', 'disciple_tools' )],
                    "other" =>      ["label"=>__( 'Other', 'disciple_tools' )],
                ]
            ],
            "email" => [
                "label" => __( 'Email', 'disciple_tools' ),
                "types" => [
                    "primary" =>    ["label"=>__( 'Primary', 'disciple_tools' )],
                    "work" =>       ["label"=>__( 'Work', 'disciple_tools' )],
                    "other" =>      ["label"=>__( 'Other', 'disciple_tools' )],
                ]
            ],
            "facebook" => [
                "label" => __( 'Facebook', 'disciple_tools' ),
                "types" => [
                    "facebook" => ["label"=>__( 'Facebook', 'disciple_tools' )]
                ]
            ],
            "twitter" => [
                "label" => __( 'Twitter', 'disciple_tools' ),
                "types" => [
                    "twitter" => ["label"=>__( 'Twitter', 'disciple_tools' )]
                ]
            ],
            "instagram" => [
                "label" => __( 'Instagram', 'disciple_tools' ),
                "types" => [
                    "instagram" => ["label"=>__( 'Instagram', 'disciple_tools' )]
                ]
            ],
            "skype" => [
                "label" => __( 'Skype', 'disciple_tools' ),
                "types" => [
                    "skype" => ["label"=> __( 'Skype', 'disciple_tools' )]
                ]
            ],
            "other" => [
                "label" => __( 'Other', 'disciple_tools' ),
                "types" => [
                    "other" => ["label"=> __( 'Other', 'disciple_tools' )]
                ]
            ],
        ];
        return apply_filters( 'dt_custom_channels', $channelList );
    }



    /**
     * Field: The 'Assigned To' dropdown controller
     *
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
        if (isset( $post->ID )){
            $assigned_to = get_post_meta( $post->ID, 'assigned_to', true );
        }

        if(empty( $assigned_to ) || $assigned_to == 'dispatch' ) {
            // set default to dispatch
//            @todo $assigned_to the dispatcher user
            $html .= '<option value="" selected></option>';
        }
        elseif ( !empty( $assigned_to ) ) { // If there is already a record
            $metadata = get_post_meta( $post->ID, 'assigned_to', true );
            $meta_array = explode( '-', $metadata ); // Separate the type and id
            $type = $meta_array[0]; // Build variables

            // Build option for current value
            if ( $type == 'user' && isset( $metadata[1] )) {
                $id = $meta_array[1];
                $value = get_user_by( 'id', $id );
                $html .= '<option value="user-'.$id.'" selected>'.$value->display_name.'</option>';

                // exclude the current id from the $results list
                $exclude_user = "'exclude' => $id";
            }
        }

        // Collect user list
        $args = ['role__not_in' => ['registered', 'prayer_supporter', 'project_supporter'], 'fields' => ['ID', 'display_name'], 'exclude' => $exclude_user ];
        $results = get_users( $args );

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
     *
     * @access public
     * @since  0.1
     * @param  string $title
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
     *
     * @access public
     * @since  0.1
     */
    public function activation () {
        $this->flush_rewrite_rules();
    } // End activation()

    /**
     * Flush the rewrite rules
     *
     * @access public
     * @since  0.1
     */
    private function flush_rewrite_rules () {
        $this->register_post_type();
        flush_rewrite_rules();
    } // End flush_rewrite_rules()

    public function contacts_permalink( $post_link, $post ) {
        if ($post->post_type === "contacts"){
            return home_url( "contacts/" . $post->ID .'/' );
        } else {
            return $post_link;
        }
    }
    function contacts_rewrites_init(){
        add_rewrite_rule( 'contacts/([0-9]+)?$', 'index.php?post_type=contacts&p=$matches[1]', 'top' );
    }


} // End Class
