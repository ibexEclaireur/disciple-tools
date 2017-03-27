<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * Disciple_Tools Reports Post Type Class
 *
 * All functionality pertaining to reports post types in Disciple_Tools.
 *
 * @package Disciple_Tools
 * @category Plugin
 * @author Chasm.Solutions & Kingdom.Training
 * @since 0.1
 */
class Disciple_Tools_Reports_Post_Type {
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
    public function __construct( $post_type = 'reports', $singular = 'Report', $plural = 'Reports', $args = array(), $taxonomies = array() ) {
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

            if ( $pagenow == 'edit.php' && isset( $_GET['post_type'] ) && esc_attr( $_GET['post_type'] ) == $this->post_type ) {
                add_filter( 'manage_reports_posts_columns', array( $this, 'set_custom_edit_report_columns' ) );
                add_action( 'manage_reports_posts_custom_column' , array( $this, 'custom_report_column' ), 10, 2 );
            }
        }

    } // End __construct()

    /**
     * Register the post type.
     * @access portal
     * @return void
     */
    public function register_post_type () {
        $labels = array(
            'name' 					=> sprintf( _x( '%s', 'Reports', 'disciple_tools' ), $this->plural ),
            'singular_name' 		=> sprintf( _x( '%s', 'Report', 'disciple_tools' ), $this->singular ),
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
            'slug'                  => 'report',
            'with_front'            => true,
            'pages'                 => true,
            'feeds'                 => false,
        );
        $capabilities = array(
            'edit_post'             => 'edit_report',
            'read_post'             => 'read_report',
            'delete_post'           => 'delete_report',
            'delete_others_posts'   => 'delete_others_reports',
            'delete_posts'          => 'delete_reports',
            'edit_posts'            => 'edit_reports',
            'edit_others_posts'     => 'edit_others_reports',
            'publish_posts'         => 'publish_reports',
            'read_private_posts'    => 'read_private_reports',
        );
        $defaults = array(
            'label'                 => __( 'Report', 'disciple_tools' ),
            'description'           => __( 'Daily reports on statistics', 'disciple_tools' ),
            'labels' 				=> $labels,
            'public' 				=> false,
            'publicly_queryable' 	=> false,
            'show_ui' 				=> true,
            'show_in_menu' 			=> true,
            'query_var' 			=> true,
            'rewrite' 				=> $rewrite,
            'capabilities'          => $capabilities,
            'capability_type' 		=> 'report',
            'has_archive' 			=> false, //$archive_slug,
            'hierarchical' 			=> false,
            'supports' 				=> array( 'title', 'custom-fields'  ),
            'menu_position' 		=> 5,
            'menu_icon' 			=> 'dashicons-groups',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'exclude_from_search'   => false,
            'show_in_rest'          => true,
            'rest_base'             => 'reports',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        );

        $args = wp_parse_args( $this->args, $defaults );

        register_post_type( $this->post_type, $args );
    } // End register_post_type()

    /**
     * Register the "report-source" taxonomy.
     * @access portal
     * @since  1.3.0
     * @return void
     */
    public function register_taxonomy () {
        $this->taxonomies['report-source'] = new Disciple_Tools_Taxonomy($post_type = 'reports', $token = 'report-source', $singular = 'Source', $plural = 'Sources', $args = array()); // Leave arguments empty, to use the default arguments.
        $this->taxonomies['report-source']->register();
    } // End register_taxonomy()

    /**
     * Setup the meta box.
     * @access public
     * @since  0.1
     * @return void
     */
    public function meta_box_setup () {
        add_meta_box( $this->post_type . '_date', __( 'Date', 'disciple_tools' ), array( $this, 'meta_box_date_content' ), $this->post_type, 'normal', 'high' );
    } // End meta_box_setup()

    /**
     * Display Meta Records
     * @access public
     * @since  0.1
     * @return mixed
     */
    public function set_custom_edit_report_columns($columns) {
        unset( $columns['date'] );
        $columns['report_date'] = __( 'Report Date', 'disciple_tools' );

        return $columns;
    }

    /**
     * Display Meta Records
     * @access public
     * @since  0.1
     * @return mixed
     */
    public function custom_report_column( $column ) {

        switch ( $column ) {

            case 'report_date':

                $custom_fields = get_post_custom( );

                if(!empty($custom_fields['report_date'][0])) { echo $custom_fields['report_date'][0] ;}

                break;
        }
        return $column;
    }

    /**
     * The contents of our meta box.
     * @access public
     * @since  0.1
     * @return void
     */
    public function meta_box_date_content () {

        // Create variables
        global $post_id;
        $k = 'report_date';
        $data = get_metadata('post', $post_id, $k, true);
        print $data;
        $v =  array(
            'name' => __( 'Report Date', 'disciple_tools' ),
            'description' => 'This is the 24 hour period that this report reflects. This can be different than the publish or modified dates.',
            'type' => 'report_date',
            'section' => 'info'
        );

        // Decide if dropdown is needed
        if (empty($data)) {
            $show_dropdown = true;
        } else {
            if ($data < date('Y-m-d', strtotime('1 year ago'))) {
                $show_dropdown = false;
            } else {
                $show_dropdown = true;
            }
        }


        // Begin Table
        $html = '';
        $html .= '<input type="hidden" name="dt_' . $this->post_type . '_noonce" id="dt_' . $this->post_type . '_noonce" value="' . wp_create_nonce( plugin_basename( dirname( Disciple_Tools()->plugin_path ) ) ) . '" />';
        $html .= '<table class="form-table"><tbody>' . "\n";


        /*
         * Check for the type of field needed. If the date is older than a year, it uses a text box; if it is within a year it loads a dropdown.
         * TODO: Ideally, this input should be managed by a jquery selector
         */
        if($show_dropdown) {

            $html .= '<tr valign="top"><th scope="row">
			<label for="' . esc_attr( $k ) . '">'. $v['name'].'</label></th>
			<td><select name="' . esc_attr( $k ) . '" id="' . esc_attr( $k ) . '" class="regular-text">';
            // Iterate the options

            $new_date = $this->get_dates_for_last_year();
            foreach ($new_date as $vv) {
                $html .= '<option value="' . $vv . '" ';
                if($vv == $data) { $html .= 'selected';}
                $html .= '>' .$vv . '</option>';
            }
            $html .= '</select>' . "\n";
            $html .= '<p class="description">' . $v['description'] . '</p>' . "\n";
            $html .= '</td><tr/>' . "\n";



        } else {

            $html .= '<tr valign="top"><th scope="row"><label for="' . esc_attr( $k ) . '">' . $v['name'] . '</label></th><td><input name="' . esc_attr( $k ) . '" type="text" id="' . esc_attr( $k ) . '" class="regular-text" value="' . esc_attr( $data ) . '" />' . "\n";
            $html .= '<p class="description">' . $v['description'] . '</p>' . "\n";
            $html .= '</td><tr/>' . "\n";
        }

        // End Table
        $html .= '</tbody></table>' . "\n";

        echo $html;
    } // End meta_box_content()

    /**
     * The contents of our meta box.
     * @access public
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

                    case 'report_date':
                        $html .= '<tr valign="top"><th scope="row">
							<label for="' . esc_attr( $k ) . '">' . $v['name'] . '</label></th>
							<td><select name="' . esc_attr( $k ) . '" id="' . esc_attr( $k ) . '" class="regular-text">';
                        // Iterate the options

                        $report_date = '';

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
        if ( isset($_POST['dt_' . $this->post_type . '_noonce']) && ! wp_verify_nonce( $_POST['dt_' . $this->post_type . '_noonce'], plugin_basename( dirname( Disciple_Tools()->plugin_path ) ) ) ) {
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

        foreach ( $fields as $f ) {

            ${$f} = strip_tags(trim($_POST[$f]));

            // Escape the URLs.
            if ( 'url' == $field_data[$f]['type'] ) {
                ${$f} = esc_url( ${$f} );
            }

            if ( get_post_meta( $post_id, $f ) == '' ) {
                add_post_meta( $post_id, $f, ${$f}, true );
            } elseif( ${$f} != get_post_meta( $post_id, $f, true ) ) {
                update_post_meta( $post_id, $f, ${$f} );
            } elseif ( ${$f} == '' ) {
                delete_post_meta( $post_id, $f, get_post_meta( $post_id, $f, true ) );
            }
        }
    } // End meta_box_save()

    /**
     * Customise the "Enter title here" text.
     * @access public
     * @since  0.1
     * @param string $title
     * @return void
     */
    public function enter_title_here ( $title ) {
        if ( get_post_type() == $this->post_type ) {
            $title = __( 'Enter the group here', 'disciple_tools' );
        }
        return $title;
    } // End enter_title_here()

    /**
     * Get the settings for the custom fields.
     * @access public
     * @since  0.1
     * @return array
     */
    public function get_custom_fields_settings () {
        $fields = array();

        $fields['report_date'] = array(
            'name' => __( 'Report Date', 'disciple_tools' ),
            'description' => 'This is the 24 hour period that this report reflects. This can be different than the publish or modified dates.',
            'type' => 'report_date',
            'default' => $this->get_dates_for_last_year(),
            'section' => 'info'
        );

        return apply_filters( 'dt_custom_fields_settings', $fields );
    } // End get_custom_fields_settings()

    /**
     * Get an array of dates for the last year
     * @access public
     * @since  0.1
     * @return array
     */
    public function get_dates_for_last_year () {

        $number_of_days = 365;
        $today = date("Y-m-d");
        $dates = array();
        $i = 0;

        while ($i < $number_of_days) {

            $date = date_create($today);
            date_add($date, date_interval_create_from_date_string('-'. $i .' days'));
            $dates[$i] = date_format($date, 'Y-m-d');

            $i++;
        }

        $dates = array_values($dates);

        return $dates;

    }


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