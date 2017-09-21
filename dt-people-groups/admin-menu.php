<?php
/**
 * Disciple_Tools_People_Groups_Admin_Menu
 *
 * @class   Disciple_Tools_People_Groups_Admin_Menu
 * @version 0.1
 * @since   0.1
 * @package Disciple_Tools_People_Groups_Admin_Menu
 * @author  Chasm.Solutions & Kingdom.Training
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class Disciple_Tools_People_Groups_Admin_Menu {
    
    public $path;
    /**
     * Disciple_Tools The single instance of Disciple_Tools.
     *
     * @var    object
     * @access private
     * @since  0.1
     */
    private static $_instance = null;
    
    /**
     * Main Disciple_Tools_People_Groups_Admin_Menu Instance
     *
     * Ensures only one instance of Disciple_Tools_People_Groups_Admin_Menu is loaded or can be loaded.
     *
     * @since  0.1
     * @static
     * @see    Disciple_Tools()
     * @return Disciple_Tools_People_Groups_Admin_Menu instance
     */
    public static function instance() {
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
        $this->path = plugin_dir_path( __DIR__ );
        
        require_once( 'admin-tab-import.php' );
        
        add_action( 'admin_menu', [ $this, 'load_admin_menu_item' ] );
        
    } // End __construct()
    
    /**
     * Load Admin menu into Settings
     */
    public function load_admin_menu_item() {
        add_submenu_page( 'edit.php?post_type=peoplegroups', __( 'Add New', 'disciple_tools' ), __( 'Add New', 'disciple_tools' ), 'manage_options', 'disciple_tools_people_groups', [ $this, 'page_content' ] );
    }
    
    /**
     * Builds the tab bar
     *
     * @since 0.1
     */
    public function page_content() {
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
        
        /**
         * Begin Header & Tab Bar
         */
        if ( isset( $_GET[ "tab" ] ) ) {
            $tab = $_GET[ "tab" ];
        } else {
            $tab = 'import';
        }
        
        $tab_link_pre = '<a href="edit.php?post_type=peoplegroups&page=disciple_tools_people_groups&tab=';
        
        $tab_link_post = '" class="nav-tab ';
        
        $html = '<div class="wrap">

            <h2>People Group Settings</h2>
            <h2 class="nav-tab-wrapper">';
        
        $html .= $tab_link_pre . 'import' . $tab_link_post;
        if ( $tab == 'import' || ! isset( $tab ) ) {
            $html .= 'nav-tab-active';
        }
        $html .= '">Import</a>';
        
        //        $html .= $tab_link_pre . 'address_tract' . $tab_link_post;
        //        if ($tab == 'address_tract' ) {$html .= 'nav-tab-active';}
        //        $html .= '">Address to Tract</a>';
        
        $html .= '</h2>';
        echo $html; // Echo tabs
        $html = '';
        
        // End Tab Bar
        
        
        /**
         * Begin Page Content
         */
        
        switch ( $tab ) {
            
            default:
                $class_object = new Disciple_Tools_People_Groups_Tab_Import();
                $html         .= '' . $class_object->page_contents();
                break;
        }
        $html .= '</div>'; // end div class wrap
        echo $html; // Echo contents
    }
}
