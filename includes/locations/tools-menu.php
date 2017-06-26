<?php

/**
 * Disciple_Tools_Tabs
 *
 * @class Disciple_Tools_Tabs
 * @version	0.1
 * @since 0.1
 * @package	Disciple_Tools_Tabs
 * @author Chasm.Solutions
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Disciple_Tools_Location_Tools_Menu {

    public $path;

    /**
     * Disciple_Tools The single instance of Disciple_Tools.
     * @var 	object
     * @access  private
     * @since  0.1
     */
    private static $_instance = null;

    /**
     * Main Disciple_Tools_Tabs Instance
     *
     * Ensures only one instance of Disciple_Tools_Tabs is loaded or can be loaded.
     *
     * @since 0.1
     * @static
     * @see Disciple_Tools()
     * @return Disciple_Tools_Location_Tools_Menu instance
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
        $this->path  = plugin_dir_path(__DIR__);
        require_once ('tab-state.php');
        require_once ('tab-public-data.php');
        require_once ('tab-maintenance.php');
        require_once ('tab-js-tract-lookup.php');
        require_once ('tab-ip-address.php');

        add_action( 'admin_menu', array( $this, 'load_admin_menu_item' ) );
    } // End __construct()

    /**
     * Load Admin menu into Settings
     */
    public function load_admin_menu_item () {
        add_submenu_page( 'edit.php?post_type=locations', __( 'Import', 'disciple_tools' ), __( 'Import', 'disciple_tools' ), 'manage_options', 'disciple_tools_locations', array( $this, 'page_content' ) );
    }

    /**
     * Builds the tab bar
     * @since 0.1
     */
    public function page_content() {


        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }

        /**
         * Begin Header & Tab Bar
         */
        if (isset($_GET["tab"])) {$tab = $_GET["tab"];} else {$tab = 'address_tract';}

        $tab_link_pre = '<a href="edit.php?post_type=locations&page=disciple_tools_locations&tab=';
        $tab_link_post = '" class="nav-tab ';

        $html = '<div class="wrap">
            <h2>Import Locations</h2>
            <h2 class="nav-tab-wrapper">';

        $html .= $tab_link_pre . 'address_tract' . $tab_link_post;
        if ($tab == 'address_tract' || !isset($tab) ) {$html .= 'nav-tab-active';}
        $html .= '">Address to Tract</a>';

        $html .= $tab_link_pre . 'state' . $tab_link_post;
        if ($tab == 'state' || !isset($tab) ) {$html .= 'nav-tab-active';}
        $html .= '">State</a>';

        $html .= $tab_link_pre . 'ip' . $tab_link_post;
        if ($tab == 'ip') {$html .= 'nav-tab-active';}
        $html .= '">IP Address</a>';

        $html .= $tab_link_pre . 'public' . $tab_link_post;
        if ($tab == 'public') {$html .= 'nav-tab-active';}
        $html .= '">Public Data</a>';

        $html .= $tab_link_pre . 'maintenance' . $tab_link_post;
        if ($tab == 'maintenance') {$html .= 'nav-tab-active';}
        $html .= '">Maintenance</a>';

        $html .= '</h2>';

        echo $html;

        $html = '';
        // End Tab Bar

        /**
         * Begin Page Content
         */
        switch ($tab) {

            case "state":
                $class_object = new Disciple_Tools_State();
                $html .= ''. $class_object->page_contents();
                break;
            case "public":
                $class_object = new Disciple_Tools_Public_Data();
                $html .= ''. $class_object->page_contents();
                break;
            case "js_tract":
                $class_object = new Disciple_Tools_JS_Tract_Lookup();
                $html .= ''. $class_object->page_contents();
                break;
            case "ip":
                $class_object = new Disciple_Tools_IP_Tab();
                $html .= ''. $class_object->page_contents();
                break;
            case "maintenance":
                $class_object = new Disciple_Tools_Maintenance();
                $html .= ''. $class_object->page_contents();
                break;
            default:
                $class_object = new Disciple_Tools_JS_Tract_Lookup();
                $html .= ''. $class_object->page_contents();
                break;
        }

        $html .= '</div>'; // end div class wrap

        echo $html;
    }



}