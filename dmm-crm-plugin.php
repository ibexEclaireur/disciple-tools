<?php
/**
 * Plugin Name: DMM CRM
 * Plugin URI: https://github.com/ChasmSolutions/DMM-CRM-Plugin
 * Description: DMM CRM is a contact relationship management system for disciple making movements. 
 * Version: 0.0.1
 * Author: Chasm.Solutions & Kingdom.Training
 * Author URI: https://github.com/ChasmSolutions
 * Requires at least: 4.0.0
 * Tested up to: 4.7.0
 *
 * @package   DmmCrm
 * @author 	  Chasm Solutions <chasm.crew@chasm.solutions>
 * @link      https://github.com/ChasmSolutions
 * @copyright 2017 Chasm Solutions
 * @license   GPL-3.0
 * @version   0.0.1
 * 
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

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
//$dmmcrm_post_type_contacts = new class_dmmcrm_psalm_119;





/**
 * Returns the main instance of DmmCrm_Plugin to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object DmmCrm_Plugin
 */
function DmmCrm_Plugin() {
	return DmmCrm_Plugin::instance();
} // End DmmCrm_Plugin()

add_action( 'plugins_loaded', 'DmmCrm_Plugin' );

/**
 * Main DmmCrm_Plugin Class
 *
 * @class DmmCrm_Plugin
 * @version	1.0.0
 * @since 1.0.0
 * @package	DmmCrm_Plugin
 * @author Chasm.Solutions & Kingdom.Training
 */
final class DmmCrm_Plugin {
	/**
	 * DmmCrm_Plugin The single instance of DmmCrm_Plugin.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	/**
	 * The plugin directory URL.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $plugin_url;

	/**
	 * The plugin directory path.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $plugin_path;

	// Admin - Start
	/**
	 * The admin object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin;

	/**
	 * The settings object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings;
	// Admin - End

	// Post Types - Start
	/**
	 * The post types we're registering.
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $post_types = array();
	// Post Types - End
	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 */
	public function __construct () {
		$this->token 			= 'dmmcrm';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '0.0.1';

		// Admin - Start
		require_once( 'includes/classes/class-dmmcrm-settings.php' );
			$this->settings = DmmCrm_Plugin_Settings::instance();

		if ( is_admin() ) {
			require_once( 'includes/classes/class-dmmcrm-admin.php' );
			$this->admin = DmmCrm_Plugin_Admin::instance();
		}
		// Admin - End
		
		// Add Psalm 119
		require_once( 'includes/classes/class-dmmcrm-psalm-119.php' );
			$this->psalm = Psalm_119::instance();

		// Post Types - Start
		require_once( 'includes/classes/class-dmmcrm-contact-post-type.php' );
		require_once( 'includes/classes/class-dmmcrm-group-post-type.php' );
		require_once( 'includes/classes/class-dmmcrm-location-post-type.php' );
		require_once( 'includes/classes/class-dmmcrm-taxonomy.php' );

		// Register an example post type. To register other post types, duplicate this line.
		$this->post_types['contacts'] = new DmmCrm_Plugin_Contact_Post_Type( 'contacts', __( 'Contact', 'dmmcrm' ), __( 'Contacts', 'dmmcrm' ), array( 'menu_icon' => 'dashicons-groups' ) );
		$this->post_types['groups'] = new DmmCrm_Plugin_Group_Post_Type( 'groups', __( 'Group', 'dmmcrm' ), __( 'Groups', 'dmmcrm' ), array( 'menu_icon' => 'dashicons-admin-multisite' ) );
		$this->post_types['locations'] = new DmmCrm_Plugin_Location_Post_Type( 'locations', __( 'Location', 'dmmcrm' ), __( 'Locations', 'dmmcrm' ), array( 'menu_icon' => 'dashicons-admin-site' ) );
		// Post Types - End
		
		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		
	} // End __construct()

	/**
	 * Main DmmCrm_Plugin Instance
	 *
	 * Ensures only one instance of DmmCrm_Plugin is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see DmmCrm_Plugin()
	 * @return Main DmmCrm_Plugin instance
	 */
	public static function instance () {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

	/**
	 * Load the localisation file.
	 * @access  public
	 * @since   1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'dmmcrm', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	} // End load_plugin_textdomain()

	/**
	 * Cloning is forbidden.
	 * @access public
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 * @access public
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	} // End __wakeup()

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 */
	public function install () {
		$this->_log_version_number();
	} // End install()

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   1.0.0
	 */
	private function _log_version_number () {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	} // End _log_version_number()
} // End Class


/**
 * Helper pages
 *
 * This defines the default dashboard widgets.
 * 
 * TODO: Convert to Class & Integrate it the class above.
 *
 * @since 0.0.1
 */
require_once( DMMCRM_PLUGIN_DIR . "includes/dmmcrm-dashboard.php" );

/**
 * Load roles.
 */
require_once (DMMCRM_PLUGIN_DIR . 'includes/dmmcrm-roles.php');


/**
 * Load security modifications to site.
 */
require_once (DMMCRM_PLUGIN_DIR . 'includes/dmmcrm-security-setup.php');

/**
 * Load security modifications to site.
 */
require_once (DMMCRM_PLUGIN_DIR . 'includes/dmmcrm-metaboxes.php');

/**
 * Load admin panel functions to control the experience of the admin panel.
 */
require_once (DMMCRM_PLUGIN_DIR . 'includes/dmmcrm-admin-setup.php');

