<?php

/**
 * Disciple Tools Portal Menus
 *
 * @class Disciple_Tools_Portal_Menus
 * @version	0.1
 * @since 0.1
 * @package	Disciple_Tools
 * @author Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Disciple_Tools_Portal_Nav {

    public $top_nav = 'Disciple Tools Top Nav';
    public $footer_nav = 'Disciple Tools Footer Nav';
    public $top_theme_location = 'main-nav'; // theme locations specific to Disciple Tools Theme
    public $footer_theme_location = 'footer-links'; // theme locations specific to Disciple Tools Theme

    /**
     * Disciple_Tools_Portal_Menus The single instance of Disciple_Tools_Portal_Menus.
     * @var 	object
     * @access  private
     * @since 	0.1
     */
    private static $_instance = null;

    /**
     * Main Disciple_Tools_Portal_Menus Instance
     *
     * Ensures only one instance of Disciple_Tools_Admin_Menus is loaded or can be loaded.
     *
     * @since 0.1
     * @static
     * @return Disciple_Tools_Portal_Nav instance
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

        // Forces nav menu installation
        if(!wp_get_nav_menu_object($this->top_nav)) {
            $this->add_top_nav();
            $this->set_top_menu_to_main ();
        }


    } // End __construct()

    /**
     * Installs Top Menu
     *
     */
    public function add_top_nav ()
    {

        $menuname = $this->top_nav;

        // Does the menu exist already?
        $menu_exists = wp_get_nav_menu_object($menuname);

        // If it doesn't exist, let's create it.
        if (!$menu_exists) {
            $menu_id = wp_create_nav_menu($menuname);

            // Set up default Disciple Tools links and add them to the menu.
            wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title' => __('Dashboard'),
                'menu-item-classes' => 'dashboard',
                'menu-item-url' => home_url('/'),
                'menu-item-status' => 'publish'));

            wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title' => __('Contacts'),
                'menu-item-classes' => 'my-contacts',
                'menu-item-url' => home_url('/contacts/'),
                'menu-item-status' => 'publish'));

            wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title' => __('Groups'),
                'menu-item-classes' => 'groups',
                'menu-item-url' => home_url('/groups/'),
                'menu-item-status' => 'publish'));

            wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title' => __('Reports'),
                'menu-item-classes' => 'reports',
                'menu-item-url' => home_url('/reports/'),
                'menu-item-status' => 'publish'));

            wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title' => __('Prayer Guide'),
                'menu-item-classes' => 'prayer-guide',
                'menu-item-url' => home_url('/prayer-guide/'),
                'menu-item-status' => 'publish'));

            wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title' => __('Profile'),
                'menu-item-classes' => 'profile',
                'menu-item-url' => home_url('/profile/'),
                'menu-item-status' => 'publish'));

        }
    }

    /**
     * Adds top menu to the top display location
     *
     */
    public function set_top_menu_to_main () {

        $menu_object = wp_get_nav_menu_object( $this->top_nav );
        $menu_id = $menu_object->term_id;
        $main_nav = $this->top_theme_location;

        // Grab the theme locations and assign our newly-created menu
        if (!has_nav_menu($main_nav)) {
            $locations = get_theme_mod('nav_menu_locations');
            $locations[$main_nav] = $menu_id;
            set_theme_mod('nav_menu_locations', $locations);
        }
    }


}