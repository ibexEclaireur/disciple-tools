<?php

/**
 * Disciple_Tools_Config_Menu class for the admin page
 *
 * @class Disciple_Tools_Config_Menu
 * @version    1.0.0
 * @since 1.0.0
 * @package    DRM_Plugin
 * @author Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly
}

final class Disciple_Tools_Config_Menu {

    /**
     * Disciple_Tools_Config_Menu The single instance of Disciple_Tools_Config_Menu.
     * @var     object
     * @access  private
     * @since     1.0.0
     */
    private static $_instance = null;

    /**
     * Disciple_Tools_Options_Menu Instance
     *
     * Ensures only one instance of Disciple_Tools_Config_Menu is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @return Disciple_Tools_Config_Menu instance
     */
    public static function instance () {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    /**
     * Constructor function.
     * @access  portal
     * @since   1.0.0
     */
    public function __construct () {
        
        add_action( "admin_menu", array($this, "add_dt_options_menu") );

    } // End __construct()

    /**
     * Loads the subnav page
     * @since 0.1
     */
    public function add_dt_options_menu () {
        add_menu_page( __( 'Config (DT)', 'disciple_tools' ), __( 'Config (DT)', 'disciple_tools' ), 'manage_dt', 'dt_options', [ $this, 'build_menu_page' ], dt_svg_icon(), 75 );
        add_submenu_page( 'dt_options', 'API Keys', 'API Keys', 'manage_dt', 'dt_api_keys', [ $this, 'build_api_key_page' ] );
        add_submenu_page( 'dt_options', 'Analytics', 'Analytics', 'manage_dt', 'dt_analytics', [ $this, 'build_analytics_page' ] );
        add_submenu_page( 'dt_options', 'Facebook', 'Facebook', 'manage_dt', 'dt_facebook', [ $this, 'build_facebook_page' ] );
        add_submenu_page( 'dt_options', 'Reports Log', 'Reports Log', 'manage_dt', 'dt_reports_log', [ $this, 'build_reports_log_page' ] );
        add_submenu_page( 'dt_options', 'Activity', 'Activity', 'manage_dt', 'dt_activity', [ $this, 'build_activity_page' ] );
        add_submenu_page( 'dt_options', 'Notifications', 'Notifications', 'manage_dt', 'dt_notifications', [ $this, 'build_notifications_page' ] );
        
    }
    
    public function build_api_key_page() {
        Disciple_Tools_Api_Keys::instance()->api_keys_page();
    }
    
    public function build_analytics_page() {
        Ga_Admin::options_page_googleanalytics();
    }
    
    public function build_facebook_page() {
        Disciple_Tools_Facebook_Integration::instance()->facebook_settings_page();
    }
    
    public function build_notifications_page() {
        dt_notifications_table ();
    }
    
    /**
     * Display the list table page
     *
     * @return Void
     */
    public function build_reports_log_page()
    {
        $ListTable = new Disciple_Tools_Reports_List_Table();
        $ListTable->prepare_items();
        ?>
        <div class="wrap">
            <div id="icon-users" class="icon32"></div>
            <h2>Disciple Tools Reports Log</h2>
            <p>This table displays the ongoing reports being recorded nightly from the different integration sources.</p>
            <?php $ListTable->display(); ?>
        </div>
        <?php
    }
    
    /**
     * Display the list table page
     *
     * @return Void
     */
    public function build_activity_page()
    {
        $ListTable = new Disciple_Tools_Activity_List_Table();
        $ListTable->prepare_items();
        ?>
        <div class="wrap">
            <div id="icon-users" class="icon32"></div>
            <h2>Disciple Tools Activity Report</h2>
            <?php $ListTable->display(); ?>
        </div>
        <?php
    }
    

    /**
     * Builds the tab bar
     * @since 0.1
     */
    public function build_menu_page() {


        if ( !current_user_can( 'manage_dt' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }

        /**
         * Begin Header & Tab Bar
         */
        if (isset( $_GET["tab"] )) {$tab = $_GET["tab"];
        } else {$tab = 'general';}

        $tab_link_pre = '<a href="admin.php?page=dt_options&tab=';
        $tab_link_post = '" class="nav-tab ';

        $html = '<div class="wrap">
            <h2>DISCIPLE TOOLS OPTIONS</h2>
            <h2 class="nav-tab-wrapper">';

        $html .= $tab_link_pre . 'general' . $tab_link_post;
        if ($tab == 'general' || !isset( $tab ) ) {$html .= 'nav-tab-active';}
        $html .= '">General</a>';

        $html .= $tab_link_pre . 'extensions' . $tab_link_post;
        if ($tab == 'extensions') {$html .= 'nav-tab-active';}
        $html .= '">Extensions</a>';
        
        $html .= $tab_link_pre . 'options' . $tab_link_post;
        if ($tab == 'options') {$html .= 'nav-tab-active';}
        $html .= '">Options</a>';

        $html .= $tab_link_pre . 'tutorials' . $tab_link_post;
        if ($tab == 'tutorials') {$html .= 'nav-tab-active';}
        $html .= '">Tutorials</a>';


        $html .= '</h2>';

        echo $html;

        $html = '';
        // End Tab Bar

        /**
         * Begin Page Content
         */
        switch ($tab) {

            case "general":
                require_once( 'general.php' );
                $content = new Disciple_Tools_General_Tab();
                $html .= $content->general_options();
                break;
            case "extensions":
                require_once( 'general.php' );
                $content = new Disciple_Tools_General_Tab();
                $html .= $content->general_options();
                break;
            case "options":
                
               
                break;
            default:
//                $html .= dt_demo_plugin()->add_records->dt_demo_add_records_content() ;
        }

        $html .= '</div>'; // end div class wrap

        echo $html;
    }
}
