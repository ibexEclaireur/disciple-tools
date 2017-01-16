<?php
/**
 * Plugin Name: DMM CRM
 * Plugin URI: https://github.com/ChasmSolutions/DMM-CRM-Plugin
 * Description: DMM CRM is a contact relationship management system for disciple making movements. 
 * Version: 0.0.1
 * Author: Chasm.Solutions & Kingdom.Training
 * Author URI: https://github.com/ChasmSolutions
 *
 * @package   DmmCrm
 * @author 	  Chasm Solutions <chasm.crew@chasm.solutions>
 * @link      https://github.com/ChasmSolutions
 * @copyright 2017 Chasm Solutions
 * @license   GPL-3.0
 * @version   0.0.1
 *
 * TODO: 
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Define plugin directory constant
define( 'DMMCRM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Autoloader
 *
 * Automagically loads classes from the echo/includes. Instantiates them in the
 * plugin file using the i.e. $dmmcrm = new DmmCrm; format.
 */
spl_autoload_register(function ( $class ) {
	if ( is_readable( DMMCRM_PLUGIN_DIR . "includes/classes/{$class}.php" ) )
		require DMMCRM_PLUGIN_DIR . "includes/classes/{$class}.php";
});

/**
 * Contacts Post Type
 *
 * This defines the Contacts custom post type. A majority of the contacts app data
 * will be stored under this custom post type. Taxonomy and heavy use of meta
 * are used as well to construct the different data functionalities that this
 * plugin provides.
 *
 * @since 0.0.1
 */
$dmmcrm_post_type_contacts = new Dmmcrm_Contacts_Post_Type;

/**
 * Locations Post Type
 *
 * This defines the Locations custom post type. A majority of the map locations
 * will be stored under this custom post type.
 * 
 * @since 0.0.1
 */
$dmmcrm_post_type_locations = new Dmmcrm_Locations_Post_Type;

/**
 * Groups Post Type
 *
 * This defines the Groups custom post type. A majority of the discovery groups
 * and simple church app data will be stored under this custom post type.
 * Taxonomy and heavy use of meta are used as well to construct the different 
 * data functionalities that this plugin provides.
 *
 * @since 0.0.1
 */
$dmmcrm_post_type_groups = new Dmmcrm_Groups_Post_Type;

/**
 * Dashboard Widgets
 *
 * This defines the default dashboard widgets.
 * 
 * TODO: Convert to Class & Convert placeholder content.
 *
 * @since 0.0.1
 */
require_once( DMMCRM_PLUGIN_DIR . "includes/dmmcrm-dashboard.php" );





