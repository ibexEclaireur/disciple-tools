<?php

/**
 * Disciple_Tools_Options_Menu class for the admin page
 *
 * @class Disciple_Tools_Options_Menu
 * @version	1.0.0
 * @since 1.0.0
 * @package	DRM_Plugin
 * @author Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class Disciple_Tools_Options_Menu {

    /**
     * Disciple_Tools_Options_Menu The single instance of Disciple_Tools_Options_Menu.
     * @var 	object
     * @access  private
     * @since 	1.0.0
     */
    private static $_instance = null;

    /**
     * Disciple_Tools_Options_Menu Instance
     *
     * Ensures only one instance of Disciple_Tools_Options_Menu is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @return Disciple_Tools_Options_Menu instance
     */
    public static function instance () {
        if ( is_null( self::$_instance ) )
            self::$_instance = new self();
        return self::$_instance;
    } // End instance()

    /**
     * Constructor function.
     * @access  portal
     * @since   1.0.0
     */
    public function __construct () {

        add_action("admin_menu", array($this, "add_dt_options_menu") );

    } // End __construct()

    /**
     * Loads the subnav page
     * @since 0.1
     */
    public function add_dt_options_menu () {
        add_menu_page( __('Disciple Tools', 'disciple_tools'), __('Disciple Tools', 'disciple_tools'), 'manage_dt', 'dt_options', [ $this, 'build_menu_page' ], 'dashicons-admin-generic', 75 );
        
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
        if (isset($_GET["tab"])) {$tab = $_GET["tab"];} else {$tab = 'general';}

        $tab_link_pre = '<a href="admin.php?page=dt_options&tab=';
        $tab_link_post = '" class="nav-tab ';

        $html = '<div class="wrap">
            <h2>DISCIPLE TOOLS OPTIONS</h2>
            <h2 class="nav-tab-wrapper">';

        $html .= $tab_link_pre . 'general' . $tab_link_post;
        if ($tab == 'general' || !isset($tab) ) {$html .= 'nav-tab-active';}
        $html .= '">General</a>';

        $html .= $tab_link_pre . 'extensions' . $tab_link_post;
        if ($tab == 'extensions') {$html .= 'nav-tab-active';}
        $html .= '">Extensions</a>';

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
//                    $html .= dt_demo_plugin()->tutorials->dt_tabs_tutorial_content();
                break;
            case "extensions":
//                $html .= dt_demo_plugin()->add_report->add_report_page_form ();
                break;
            default:
//                $html .= dt_demo_plugin()->add_records->dt_demo_add_records_content() ;
        }

        $html .= '</div>'; // end div class wrap

        echo $html;
    }
}
