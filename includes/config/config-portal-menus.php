<?php
/**
 * DRM_Portal_Menu
 *
 * @class DRM_Portal_Menu
 * @version	0.1
 * @since 0.1
 * @package	DRM_Plugin
 * @author Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class DRM_Portal_Menu {

    /**
     * The single instance of DRM_Portal_Menu
     * @var 	object
     * @access  private
     * @since  0.1
     */
    private static $_instance = null;

    /**
     * Main DRM_Portal_Menu Instance
     *
     * Ensures only one instance of DRM_Portal_Menu is loaded or can be loaded.
     *
     * @since 0.1
     * @static
     * @return DRM_Portal_Menu instance
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


    } // End __construct()

    public function register_portal_menus() {
        register_nav_menu( 'prayer-supporter', 'Prayer Supporter' );
        register_nav_menu( 'project-supporter', 'Project Supporter' );
        register_nav_menu( 'multiplier', 'Multiplier' );
    }

    public function register_menu_items() {

        //then get the menu object by its name
        $menu = get_term_by( 'name', 'Multiplier', 'nav_menu' );

        //then add the actual link/ menu item and you do this for each item you want to add
        wp_update_nav_menu_item($menu->term_id, 0, array(
            'menu-item-title' =>  __('Prayer'),
            'menu-item-classes' => 'prayer',
            'menu-item-url' => home_url( '/' ),
            'menu-item-status' => 'publish'));
        wp_update_nav_menu_item($menu->term_id, 0, array(
            'menu-item-title' =>  __('Project Update'),
            'menu-item-classes' => 'project-update',
            'menu-item-url' => home_url( '/project-update' ),
            'menu-item-status' => 'publish'));
        wp_update_nav_menu_item($menu->term_id, 0, array(
            'menu-item-title' =>  __('Charts'),
            'menu-item-classes' => 'charts',
            'menu-item-url' => home_url( '/charts' ),
            'menu-item-status' => 'publish'));
        wp_update_nav_menu_item($menu->term_id, 0, array(
            'menu-item-title' =>  __('Maps'),
            'menu-item-classes' => 'maps',
            'menu-item-url' => home_url( '/maps' ),
            'menu-item-status' => 'publish'));
        wp_update_nav_menu_item($menu->term_id, 0, array(
            'menu-item-title' =>  __('Downloads'),
            'menu-item-classes' => 'downloads',
            'menu-item-url' => home_url( '/downloads' ),
            'menu-item-status' => 'publish'));

        //then you set the wanted theme  location
        $locations = get_theme_mod('nav_menu_locations');
        $locations['multiplier'] = $menu->term_id;
        set_theme_mod( 'nav_menu_locations', $locations );

    }

}

