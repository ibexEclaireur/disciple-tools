<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

/**
 * Disciple Tools Post Type Class
 *
 * All functionality pertaining to post types in Disciple_Tools.
 *
 * @package    WordPress
 * @subpackage Disciple_Tools
 * @category   Plugin
 * @author     Chasm.Solutions & Kingdom.Training
 * @since      0.1
 */
class Disciple_Tools_People_Groups_Post_Type {
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
     * Disciple_Tools_People_Groups_Post_Type The single instance of Disciple_Tools_People_Groups_Post_Type.
     *
     * @var    object
     * @access private
     * @since  0.1
     */
    private static $_instance = null;

    /**
     * Main Disciple_Tools_People_Groups_Post_Type Instance
     *
     * Ensures only one instance of Disciple_Tools_People_Groups_Post_Type is loaded or can be loaded.
     *
     * @since  0.1
     * @static
     * @return Disciple_Tools_People_Groups_Post_Type instance
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
    public function __construct() {
        $this->post_type = 'peoplegroups';
        $this->singular = __( 'People Group', 'disciple_tools' );
        $this->plural = __( 'People Groups', 'disciple_tools' );
        $this->args = [ 'menu_icon' => 'dashicons-image-filter' ];
        $this->taxonomies = [];

        add_action( 'init', [ $this, 'register_post_type' ] );
        add_action( 'init', [ $this, 'register_taxonomy' ] );

        if ( is_admin() ) {
            global $pagenow;

            add_action( 'admin_menu', [ $this, 'meta_box_setup' ], 20 );
            add_action( 'save_post', [ $this, 'meta_box_save' ] );
            add_filter( 'enter_title_here', [ $this, 'enter_title_here' ] );
            add_filter( 'post_updated_messages', [ $this, 'updated_messages' ] );

            if ( $pagenow == 'edit.php' && isset( $_GET['post_type'] ) && esc_attr( $_GET['post_type'] ) == $this->post_type ) {
                add_filter( 'manage_edit-' . $this->post_type . '_columns', [ $this, 'register_custom_column_headings' ], 10, 1 );
                add_action( 'manage_posts_custom_column', [ $this, 'register_custom_columns' ], 10, 2 );
            }

            add_action( 'admin_init', [$this, 'remove_add_new_submenu'] );

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
            'name'                     => sprintf( _x( '%s', 'post type general name', 'disciple_tools' ), $this->plural ),
            'singular_name'         => sprintf( _x( '%s', 'post type singular name', 'disciple_tools' ), $this->singular ),
            'add_new'                 => _x( 'Add New', $this->post_type, 'disciple_tools' ),
            'add_new_item'             => sprintf( __( 'Add New %s', 'disciple_tools' ), $this->singular ),
            'edit_item'             => sprintf( __( 'Edit %s', 'disciple_tools' ), $this->singular ),
            'update_item'           => sprintf( __( 'Update %s', 'disciple_tools' ), $this->singular ),
            'new_item'                 => sprintf( __( 'New %s', 'disciple_tools' ), $this->singular ),
            'all_items'             => sprintf( __( 'All %s', 'disciple_tools' ), $this->plural ),
            'view_item'             => sprintf( __( 'View %s', 'disciple_tools' ), $this->singular ),
            'view_items'            => sprintf( __( 'View %s', 'disciple_tools' ), $this->plural ),
            'search_items'             => sprintf( __( 'Search %a', 'disciple_tools' ), $this->plural ),
            'not_found'             => sprintf( __( 'No %s Found', 'disciple_tools' ), $this->plural ),
            'not_found_in_trash'     => sprintf( __( 'No %s Found In Trash', 'disciple_tools' ), $this->plural ),
            'parent_item_colon'     => '',
            'menu_name'             => $this->plural,
            'featured_image'        => sprintf( __( 'Featured Image', 'disciple_tools' ), $this->plural ),
            'set_featured_image'    => sprintf( __( 'Set featured image', 'disciple_tools' ), $this->plural ),
            'remove_featured_image' => sprintf( __( 'Remove featured image', 'disciple_tools' ), $this->plural ),
            'use_featured_image'    => sprintf( __( 'Use as featured image', 'disciple_tools' ), $this->plural ),
            'insert_into_item'      => sprintf( __( 'Insert %s', 'disciple_tools' ), $this->plural ),
            'uploaded_to_this_item' => sprintf( __( 'Uploaded to this %s', 'disciple_tools' ), $this->plural ),
            'items_list'            => sprintf( __( '%s list', 'disciple_tools' ), $this->plural ),
            'items_list_navigation' => sprintf( __( '%s list navigation', 'disciple_tools' ), $this->plural ),
            'filter_items_list'     => sprintf( __( 'Filter %s list', 'disciple_tools' ), $this->plural ),
        ];

        $rewrite = [
            'slug'                  => 'peoplegroups',
            'with_front'            => true,
            'pages'                 => true,
            'feeds'                 => false,
        ];
        $capabilities = [
            'edit_post'             => 'edit_peoplegroup',
            'read_post'             => 'read_peoplegroup',
            'delete_post'           => 'delete_peoplegroup',
            'delete_others_posts'   => 'delete_others_peoplegroups',
            'delete_posts'          => 'delete_peoplegroups',
            'edit_posts'            => 'edit_peoplegroups',
            'edit_others_posts'     => 'edit_others_peoplegroups',
            'publish_posts'         => 'publish_peoplegroups',
            'read_private_posts'    => 'read_private_peoplegroups',
        ];
        $defaults = [
            'labels'                 => $labels,
            'public'                 => true,
            'publicly_queryable'     => true,
            'show_ui'                 => true,
            'show_in_menu'             => true,
            'query_var'             => true,
            'rewrite'                 => $rewrite,
            'capabilities'          => $capabilities,
            'has_archive'             => true,
            'hierarchical'             => false,
            'supports'                 => [ 'title', 'comments' ],
            'menu_position'         => 6,
            'menu_icon'             => 'dashicons-smiley',
            'show_in_rest'          => true,
            'rest_base'             => 'peoplegroups',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        ];

        $args = wp_parse_args( $this->args, $defaults );

        register_post_type( $this->post_type, $args );
    } // End register_post_type()

    /**
     * Register the "thing-category" taxonomy.
     *
     * @access public
     * @since  1.3.0
     * @return void
     */
    public function register_taxonomy () {
//		TODO: commented out taxonomies until we know how we want to use them. Chris
//
//      $this->taxonomies['peoplegroups-type'] = new Disciple_Tools_Taxonomy($post_type = 'peoplegroups', $token = 'peoplegroups-type', $singular = 'Type', $plural = 'Type', $args = array()); // Leave arguments empty, to use the default arguments.
//		$this->taxonomies['peoplegroups-type']->register();
    } // End register_taxonomy()

    /**
     * Add custom columns for the "manage" screen of this post type.
     *
     * @access public
     * @param  string $column_name
     * @param  int $id
     * @since  0.1
     * @return void
     */
    public function register_custom_columns ( $column_name, $id ) {
        global $post;

        switch ( $column_name ) {
            case 'image':
                break;

            default:
                break;
        }
    } // End register_custom_columns()

    /**
     * Add custom column headings for the "manage" screen of this post type.
     *
     * @access public
     * @param  array $defaults
     * @since  0.1
     * @return void
     */
    public function register_custom_column_headings ( $defaults ) {
//		$new_columns = array( 'image' => __( 'Image', 'disciple_tools' ) );
        $new_columns = []; // TODO: restore above column once we know what columns we need to show.

        $last_item = [];

        if ( isset( $defaults['date'] ) ) { unset( $defaults['date'] ); }

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
            2 => __( 'Custom field updated.', 'disciple_tools' ),
            3 => __( 'Custom field deleted.', 'disciple_tools' ),
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
//		add_meta_box( $this->post_type . '_data', __( 'peoplegroup Details', 'disciple_tools' ), array( $this, 'load_details_meta_box' ), $this->post_type, 'normal', 'high' );
        add_meta_box( $this->post_type . '_activity', __( 'Activity', 'disciple_tools' ), [ $this, 'load_activity_meta_box' ], $this->post_type, 'normal', 'low' );
    } // End meta_box_setup()

    /**
     * Load activity metabox
     */
    public function load_activity_meta_box () {
        dt_activity_metabox()->activity_meta_box( get_the_ID() );
    }

    /**
     * Load activity metabox
     */
    public function load_map_meta_box () {
        dt_map_metabox()->display_map();
    }

    /**
     * Load activity metabox
     */
    public function load_details_meta_box () {
        $this->meta_box_content( 'info' );
    }



    /**
     * The contents of our meta box.
     *
     * @access public
     * @since  0.1
     * @return void
     */
    public function meta_box_content ( $section = 'info' ) {
        global $post_id;
        $fields = get_post_custom( $post_id );
        $field_data = $this->get_custom_fields_settings();

        $html = '';

        $html .= '<input type="hidden" name="dt_' . $this->post_type . '_noonce" id="dt_' . $this->post_type . '_noonce" value="' . wp_create_nonce( 'update_peoplegroup_info' ) . '" />';


        if ( 0 < count( $field_data ) ) {
            $html .= '<table class="form-table">' . "\n";
            $html .= '<tbody>' . "\n";

            foreach ( $field_data as $k => $v ) {

                if ($v['section'] == $section || $section == 'all') {

                    $data = $v['default'];
                    if (isset( $fields[$k] ) && isset( $fields[$k][0] )) {
                        $data = $fields[$k][0];
                    }

                    $type = $v['type'];

                    switch ($type) {

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
                                if ($vv == $data) {
                                    $html .= 'selected';
                                }
                                $html .= '>' . $vv . '</option>';
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
                                $html .= '<label for="' . esc_attr( "$k-$increment_the_radio_button" ) . "\">$vv</label>" .
                                    '<input class="dt-radio" type="radio" name="' . esc_attr( $k ) . '" id="' . $k . '-' . $increment_the_radio_button . '" value="' . $vv . '" ';
                                if ($vv == $data) {
                                    $html .= 'checked';
                                }
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
        if ( ( get_post_type() != $this->post_type ) || ! wp_verify_nonce( $_POST['dt_' . $this->post_type . '_noonce'], 'update_peoplegroup_info' ) ) {
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

        if ( (isset( $_POST['new-key-address'] ) && !empty( $_POST['new-key-address'] ) ) && (isset( $_POST['new-value-address'] ) && !empty( $_POST['new-value-address'] ) ) ) { // catch and prepare new contact fields
            add_post_meta( $post_id, strtolower( $_POST['new-key-address'] ), $_POST['new-value-address'], true );
        }

        foreach ( $fields as $f ) {

            ${$f} = strip_tags( trim( $_POST[$f] ) );


            if ( get_post_meta( $post_id, $f ) == '' ) {
                add_post_meta( $post_id, $f, ${$f}, true );
            } elseif ( ${$f} == '' ) {
                delete_post_meta( $post_id, $f, get_post_meta( $post_id, $f, true ) );
            } elseif( ${$f} != get_post_meta( $post_id, $f, true ) ) {
                update_post_meta( $post_id, $f, ${$f} );
            }
        }
    } // End meta_box_save()

    /**
     * Customise the "Enter title here" text.
     *
     * @access public
     * @since  0.1
     * @param  string $title
     * @return void
     */
    public function enter_title_here ( $title ) {
        if ( get_post_type() == $this->post_type ) {
            $title = __( 'Enter the People Group title here', 'disciple_tools' );
        }
        return $title;
    } // End enter_title_here()

    /**
     * Get the settings for the custom fields.
     *
     * @access public
     * @since  0.1
     * @return array
     */
    public function get_custom_fields_settings () {
        global $post;
        $fields = [];

        $fields['overall_status'] = [
            'name' => __( 'Overall Status', 'disciple_tools' ),
            'description' => '',
            'type' => 'key_select',
            'default' => ['0' => __( 'Unassigned', 'disciple_tools' ), '1' => __( 'Accepted', 'disciple_tools' ), '2' => __( 'Paused', 'disciple_tools' ), '3' => __( 'Closed', 'disciple_tools' ), '4' => __( 'Unassignable', 'disciple_tools' ) ],
            'section' => 'status'
        ];

        return apply_filters( 'dt_custom_fields_settings', $fields );
    } // End get_custom_fields_settings()


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

    /**
     * Remove the add new submenu from the locaions menu
     */
    public function remove_add_new_submenu()
    {
        global $submenu;
        unset(
            $submenu['edit.php?post_type=peoplegroups'][10]
        );
    }


} // End Class
