<?php

/**
 * Disciple Tools Admin Menus
 *
 * @class Disciple_Tools_Admin_Menus
 * @version	0.1
 * @since 0.1
 * @package	Disciple_Tools
 * @author Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Disciple_Tools_Admin_Menus {
    protected $connections;
    protected $media;
    protected $project;
    protected $_hook = array();
    protected $page;
    /**
     * Disciple_Tools_Admin_Menus The single instance of Disciple_Tools_Admin_Menus.
     * @var 	object
     * @access  private
     * @since 	0.1
     */
    private static $_instance = null;

    /**
     * Main Disciple_Tools_Admin_Menus Instance
     *
     * Ensures only one instance of Disciple_Tools_Admin_Menus is loaded or can be loaded.
     *
     * @since 0.1
     * @static
     * @return Disciple_Tools_Admin_Menus instance
     */
    public static function instance () {
        if ( is_null( self::$_instance ) )
            self::$_instance = new self();
        return self::$_instance;
    } // End instance()

    /**
     * Constructor function.
     * @access  public
     * @since   0.1
     */
    public function __construct () {
        add_action('admin_menu', array( $this, 'menu_loader') );
    } // End __construct()

    public function menu_loader() {
        $this->_hook .= add_submenu_page( 'index.php', __( 'Connections', 'disciple_tools' ), __( 'Connections', 'disciple_tools' ), 'read', 'connections-report', array( $this, 'connections_report_loader' ) );
        $this->_hook .= add_submenu_page( 'index.php', __( 'Media', 'disciple_tools' ), __( 'Media', 'disciple_tools' ), 'read', 'media-report', array( $this, 'media_report_loader' ) );
        $this->_hook .= add_submenu_page( 'index.php', __( 'Project', 'disciple_tools' ), __( 'Project', 'disciple_tools' ), 'read', 'project-report', array( $this, 'project_report_loader' ) );
//        $this->_hook .= add_submenu_page( 'index.php', __( 'Test', 'disciple_tools' ), __( 'Test', 'disciple_tools' ), 'read', 'test-report', array( $this, 'test_report_loader' ) );
        /* Add the page */
        $this->page = add_submenu_page('index.php', __( 'NewPage', 'disciple_tools' ), __( 'NewPage', 'disciple_tools' ), 'read', 'test_report', array( $this, 'render_page' ));

        /* Add callbacks for this screen only */
        add_action('load-'.$this->page,  array($this,'page_actions'),9);
        add_action('admin_footer-'.$this->page,array($this,'footer_scripts'));

        add_action('add_meta_boxes', array($this, 'example_metaboxes') );
    }

    public function connections_report_loader() {
        require_once ( 'reports-connections.php' );
        $this->connections = Disciple_Tools_Connection_Reports::instance();
        echo $this->connections->run_reports();
    }

    public function project_report_loader() {
        require_once ( 'reports-project.php' );
        $this->project = Disciple_Tools_Project_Reports::instance();
        echo $this->project->run_reports();
    }

    public function media_report_loader() {
        require_once ( 'reports-media.php' );
        $this->media = Disciple_Tools_Media_Reports::instance();
        echo $this->media->run_reports();
    }




    /**
     * Actions to be taken prior to page loading. This is after headers have been set.
     * call on load-$hook
     * This calls the add_meta_boxes hooks, adds screen options and enqueues the postbox.js script.
     */
    public function page_actions(){
        do_action('add_meta_boxes_'.$this->page, null);
        do_action('add_meta_boxes', $this->page, null);

        /* User can choose between 1 or 2 columns (default 2) */
        add_screen_option('layout_columns', array('max' => 2, 'default' => 2) );

        /* Enqueue WordPress' script for handling the metaboxes */
        wp_enqueue_script('postbox');
    }

    public function example_metaboxes(){

        add_meta_box('example1','Example 1', array($this, 'sh_example_metabox'),'dashboard_page_test_report','normal','high');
        add_meta_box('example2','Example 2', array($this, 'sh_example_metabox'),'dashboard_page_test_report','side','high');
        add_meta_box('example3','Example 3', array($this, 'sh_example_metabox'),'dashboard_page_test_report','advanced','low');
    }

    public function sh_example_metabox(){
        $html = 'Example Content';
        echo $html;
    }

    /**
     * Renders the page
     */
    public function render_page(){
        ?>
        <div class="wrap">

            <h2> <?php echo esc_html('Sample Page');?> </h2>

            <form name="my_form" method="post">
                <input type="hidden" name="action" value="some-action">
                <?php wp_nonce_field( 'some-action-nonce' );

                /* Used to save closed metaboxes and their order */
                wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
                wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>

                <div id="poststuff">

                    <div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">

                        <!--<div id="post-body-content"></div>-->

                        <div id="postbox-container-1" class="postbox-container">
                            <?php do_meta_boxes('','side',null); ?>
                        </div>

                        <div id="postbox-container-2" class="postbox-container">
                            <?php do_meta_boxes('','normal',null);  ?>
                            <?php do_meta_boxes('','advanced',null); ?>
                        </div>

                    </div> <!-- #post-body -->

                </div> <!-- #poststuff -->

            </form>

        </div><!-- .wrap -->
        <?php
    }

    /**
     * Prints the jQuery script to initilize the metaboxes
     * Called on admin_footer-*
     */
    public function footer_scripts(){
        ?>
        <script> postboxes.add_postbox_toggles(pagenow);</script>
        <?php
    }

}