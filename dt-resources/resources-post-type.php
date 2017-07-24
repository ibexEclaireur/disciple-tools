<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

/**
 * Class Disciple_Tools_Resource_Post_Type
 *
 * All functionality pertaining to post resources in Disciple_Tools.
 *
 * @package  Disciple_Tools
 * @category Plugin
 * @author   Chasm.Solutions & Kingdom.Training
 * @since    0.1
 */
class Disciple_Tools_Resources_Post_Type {
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
     * Main Disciple_Tools_Resource_Post_Type Instance
     *
     * Ensures only one instance of Disciple_Tools_Resource_Post_Type is loaded or can be loaded.
     *
     * @since  0.1
     * @static
     * @return Disciple_Tools_Resource_Post_Type
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
    public function __construct( $post_type = 'resources', $singular = '', $plural = '', $args = [], $taxonomies = [] ) {
        $this->post_type = 'resources';
        $this->singular = 'Resource';
        $this->plural = 'Resources';
        $this->args = [ 'menu_icon' => 'dashicons-book-alt' ];
        $this->taxonomies = $taxonomies = [];

        add_action( 'init', [ $this, 'register_post_type' ] );
        //		add_action( 'init', array( $this, 'register_taxonomy' ) );

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
            'name'                     => sprintf( _x( '%s', 'Resources', 'disciple_tools' ), $this->plural ),
            'singular_name'         => sprintf( _x( '%s', 'Resources', 'disciple_tools' ), $this->singular ),
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
            'insert_into_item'      => sprintf( __( 'Placed into %s', 'disciple_tools' ), $this->plural ),
            'uploaded_to_this_item' => sprintf( __( 'Uploaded to this %s', 'disciple_tools' ), $this->plural ),
            'items_list'            => sprintf( __( '%s list', 'disciple_tools' ), $this->plural ),
            'items_list_navigation' => sprintf( __( '%s list navigation', 'disciple_tools' ), $this->plural ),
            'filter_items_list'     => sprintf( __( 'Filter %s list', 'disciple_tools' ), $this->plural ),

        ];
        $rewrite = [
            'slug'                  => 'resource',
            'with_front'            => true,
            'pages'                 => true,
            'feeds'                 => false,
        ];
        $capabilities = [
            'edit_post'             => 'edit_resource',
            'read_post'             => 'read_resource',
            'delete_post'           => 'delete_resource',
            'delete_others_posts'   => 'delete_others_resources',
            'delete_posts'          => 'delete_resources',
            'edit_posts'            => 'edit_resources',
            'edit_others_posts'     => 'edit_others_resources',
            'publish_posts'         => 'publish_resources',
            'read_private_posts'    => 'read_private_resources',
        ];

        $defaults = [
            'label'                 => __( 'Resources', 'disciple_tools' ),
            'description'           => __( 'Resources generated by the media to movement effort', 'disciple_tools' ),
            'labels'                 => $labels,
            'public'                 => true,
            'publicly_queryable'     => true,
            'show_ui'                 => true,
            'show_in_menu'             => true,
            'query_var'             => true,
            'rewrite'                 => $rewrite,
            'capabilities'          => $capabilities,
            'capability_type'         => 'resource',
            'has_archive'             => true, //$archive_slug,
            'hierarchical'             => false,
            'supports'                 => [ 'title', 'editor', 'comments', 'author', 'revisions', 'thumbnail', 'post-formats'  ],
            'menu_position'         => 6,
            'menu_icon'             => 'dashicons-groups',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'exclude_from_search'   => false,
            'show_in_rest'          => true,
            'rest_base'             => 'resource',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        ];

        $args = wp_parse_args( $this->args, $defaults );

        register_post_type( $this->post_type, $args );
    } // End register_post_type()

    /**
     * Register the "projectupdates-category" taxonomy.
     *
     * @access public
     * @since  1.3.0
     * @return void
     */
    public function register_taxonomy () {
        $this->taxonomies['resource-type'] = new Disciple_Tools_Taxonomy( $post_type = 'resource', $token = 'resource-type', $singular = 'Type', $plural = 'Types', $args = [] ); // Leave arguments empty, to use the default arguments.
        $this->taxonomies['resource-type']->register();
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
            case 'phone':
                echo '';
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
     * @return mixed/void
     */
    public function register_custom_column_headings ( $defaults ) {

        $new_columns = []; //array( 'image' => __( 'Image', 'disciple_tools' ));

        $last_item = [];


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
            2 => __( 'Project Update updated.', 'disciple_tools' ),
            3 => __( 'Project Update deleted.', 'disciple_tools' ),
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
//        add_meta_box( $this->post_type . '_details', __( 'Audience', 'disciple_tools' ), array( $this, 'load_resource_info_meta_box' ), $this->post_type, 'normal', 'high' );
    } // End meta_box_setup()


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

        $html .= '<input type="hidden" name="dt_' . $this->post_type . '_noonce" id="dt_' . $this->post_type . '_noonce" value="' . wp_create_nonce( plugin_basename( dirname( Disciple_Tools()->plugin_path ) ) ) . '" />';


        if ( 0 < count( $field_data ) ) {
            $html .= '<table class="form-table">' . "\n";
            $html .= '<tbody>' . "\n";

            foreach ( $field_data as $k => $v ) {

                if ($v['section'] == $section) {

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
                            $html .= '<tr valign="top"><th scope="row"><label for="' . esc_attr( $k ) . '">' . $v['name'] . '</label></th>
                                <td><input name="' . esc_attr( $k ) . '" type="text" id="' . esc_attr( $k ) . '" class="regular-text" value="' . esc_attr( $data ) . '" />' . "\n";
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
                        case 'radio':
                            $html .= '<tr valign="top"><th scope="row">' . $v['name'] . '</th>
                                <td><fieldset>';
                            // Iterate the buttons
                            $increment_the_radio_button = 1;
                            foreach ($v['default'] as $vv) {
                                $html .= '<label for="'.esc_attr( "$k-$increment_the_radio_button" )."\">$vv</label>" .
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

        if ( isset( $_POST['dt_' . $this->post_type . '_noonce'] ) && ! wp_verify_nonce( $_POST['dt_' . $this->post_type . '_noonce'], plugin_basename( dirname( Disciple_Tools()->plugin_path ) ) ) ) {
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

        foreach ( $fields as $f ) {

            ${$f} = strip_tags( trim( $_POST[$f] ) );

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
     * Meta box for Status Information
     *
     * @access public
     * @since  0.1
     */
    public function load_resource_info_meta_box () {
        echo ''. $this->meta_box_content( 'info' );
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
            $title = __( 'Enter the title here', 'disciple_tools' );
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
        $fields = [];

        // Project Update Information Section
//        $fields['audience'] = [
//            'name' => __( 'Audience', 'disciple_tools' ),
//            'description' => 'resource Supporters are level 1; Project Supporters are level 2. Project supporters see all resource supporter posts, but resource supporters do not see project supporter posts.',
//            'type' => 'select',
//            'default' => ['Project Supporter', 'Project Supporter'],
//            'section' => 'info'
//        ];


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


} // End Class
