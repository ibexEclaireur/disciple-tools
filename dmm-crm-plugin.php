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
		
		// Locations Metabox
		if ( is_admin() ) {
			require_once( 'includes/classes/class-dmmcrm-location-meta.php' );
			$this->p2pmeta = DmmCrm_P2P_Metabox::instance();
		}
		// Locations Metabox End

        

        register_activation_hook( __FILE__, 'child_plugin_activate' );
        function child_plugin_activate(){

            // Require parent plugin
            if ( ! is_plugin_active( 'parent-plugin/parent-plugin.php' ) and current_user_can( 'activate_plugins' ) ) {
                // Stop activation redirect and show error
                wp_die('Sorry, but this plugin requires the Parent Plugin to be installed and active. <br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
            }
        }
		
		if ( is_admin() ) {
		/* 
		* Psalms 119 plugin
		*
		* Psalms 119 as a testing tool. 
		* 
		* Add the line require_once( 'psalm-119.php' ); to any function or class call that you want make sure is responding properly 
		* and if the directory is correct on psalm-119.php,thet it will add a verse to the top of the admin section.
		*
		* TODO: When development is done, add this call back to a permanent part of the system.
		* require_once( 'includes/classes/psalm-119.php' );
		*
		* Chris
		* 
		*/
		}
		
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

/**
 * Load columns plugin to changes view of contact to 2 equal columns
 */
require_once(DMMCRM_PLUGIN_DIR . 'includes/classes/three-column-screen-layout.php');

/* End Helper Pages */





/**
 * Include the TGM_Plugin_Activation class. This class makes other plugins required for the DMM CRM system.
 *
 * Refer to documentation here: https://github.com/TGMPA/TGM-Plugin-Activation
 *
 * TODO: Determine if we should use the DMM CRM Theme as the way to insure the installation of all the plugins. Chris
 *
 */
require_once dirname( __FILE__ ) . '/includes/classes/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'dmmcrm_register_required_plugins' );

/**
 * Register the required plugins for this theme.
 *
 * Example of array options:
 *
 * array(
            'name'               => 'REST API Console', // The plugin name.
            'slug'               => 'rest-api-console', // The plugin slug (typically the folder name).
            'source'             => dirname( __FILE__ ) . '/lib/plugins/rest-api-console.zip', // The plugin source.
            'required'           => true, // If false, the plugin is only 'recommended' instead of required.
            'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
            'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
            'external_url'       => '', // If set, overrides default API URL and points to an external URL.
            'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
        ),
 *
 */
function dmmcrm_register_required_plugins() {
    /*
     * Array of plugin arrays. Required keys are name and slug.
     * If the source is NOT from the .org repo, then source is also required.
     */
    $plugins = array(

        array(
            'name'              => 'rest-api',
            'slug'              => 'rest-api',
            'required'          => true,
            'version'            => '2.0-beta15',
            'force_activation'  => true,
            'force_deactivation' => true,
            'is_callable'       => 'WP_REST_Controller',
        ),
        array(
            'name'               => 'REST API Console',
            'slug'               => 'rest-api-console',
            'required'           => true,
            'version'            => '2.1',
            'force_activation'   => true,
            'force_deactivation' => true,
            'is_callable'        => 'WP_REST_Console',
        ),
        array(
            'name'               => 'WP oAuth Server',
            'slug'               => 'oauth2-provider',
            'required'           => true,
            'version'            => '3.2',
            'force_activation'   => true,
            'force_deactivation' => true,
            'is_callable'        => 'WO_Server',
        ),
        array(
            'name'               => 'DMM CRM Sample Data',
            'slug'               => 'dmm-crm-sample-data',
            'external_url'       => 'https://github.com/ChasmSolutions/dmm-crm-sample-data/archive/master.zip',
            'is_callable'       =>  'dmmcrm_sample_data',
        ),
    );

    /*
     * Array of configuration settings. Amend each line as needed.
     *
     * Only uncomment the strings in the config array if you want to customize the strings.
     */
    $config = array(
        'id'           => 'dmmcrm',                 // Unique ID for hashing notices for multiple instances of TGMPA.
        'default_path' => '/includes/plugins/',     // Default absolute path to bundled plugins.
        'menu'         => 'tgmpa-install-plugins', // Menu slug.
        'parent_slug'  => 'plugins.php',            // Parent menu slug.
        'capability'   => 'manage_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
        'has_notices'  => true,                    // Show admin notices or not.
        'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => 'For the DMM CRM system to work correction, these additional plugins must be installed.',                      // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => true,                   // Automatically activate plugins after installation or not.
        'message'      => '',                      // Message to output right before the plugins table.

        /*
        'strings'      => array(
            'page_title'                      => __( 'Install Required Plugins', 'dmmcrm' ),
            'menu_title'                      => __( 'Install Plugins', 'dmmcrm' ),
            /* translators: %s: plugin name. * /
            'installing'                      => __( 'Installing Plugin: %s', 'dmmcrm' ),
            /* translators: %s: plugin name. * /
            'updating'                        => __( 'Updating Plugin: %s', 'dmmcrm' ),
            'oops'                            => __( 'Something went wrong with the plugin API.', 'dmmcrm' ),
            'notice_can_install_required'     => _n_noop(
                /* translators: 1: plugin name(s). * /
                'This theme requires the following plugin: %1$s.',
                'This theme requires the following plugins: %1$s.',
                'dmmcrm'
            ),
            'notice_can_install_recommended'  => _n_noop(
                /* translators: 1: plugin name(s). * /
                'This theme recommends the following plugin: %1$s.',
                'This theme recommends the following plugins: %1$s.',
                'dmmcrm'
            ),
            'notice_ask_to_update'            => _n_noop(
                /* translators: 1: plugin name(s). * /
                'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.',
                'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.',
                'dmmcrm'
            ),
            'notice_ask_to_update_maybe'      => _n_noop(
                /* translators: 1: plugin name(s). * /
                'There is an update available for: %1$s.',
                'There are updates available for the following plugins: %1$s.',
                'dmmcrm'
            ),
            'notice_can_activate_required'    => _n_noop(
                /* translators: 1: plugin name(s). * /
                'The following required plugin is currently inactive: %1$s.',
                'The following required plugins are currently inactive: %1$s.',
                'dmmcrm'
            ),
            'notice_can_activate_recommended' => _n_noop(
                /* translators: 1: plugin name(s). * /
                'The following recommended plugin is currently inactive: %1$s.',
                'The following recommended plugins are currently inactive: %1$s.',
                'dmmcrm'
            ),
            'install_link'                    => _n_noop(
                'Begin installing plugin',
                'Begin installing plugins',
                'dmmcrm'
            ),
            'update_link' 					  => _n_noop(
                'Begin updating plugin',
                'Begin updating plugins',
                'dmmcrm'
            ),
            'activate_link'                   => _n_noop(
                'Begin activating plugin',
                'Begin activating plugins',
                'dmmcrm'
            ),
            'return'                          => __( 'Return to Required Plugins Installer', 'dmmcrm' ),
            'plugin_activated'                => __( 'Plugin activated successfully.', 'dmmcrm' ),
            'activated_successfully'          => __( 'The following plugin was activated successfully:', 'dmmcrm' ),
            /* translators: 1: plugin name. * /
            'plugin_already_active'           => __( 'No action taken. Plugin %1$s was already active.', 'dmmcrm' ),
            /* translators: 1: plugin name. * /
            'plugin_needs_higher_version'     => __( 'Plugin not activated. A higher version of %s is needed for this theme. Please update the plugin.', 'dmmcrm' ),
            /* translators: 1: dashboard link. * /
            'complete'                        => __( 'All plugins installed and activated successfully. %1$s', 'dmmcrm' ),
            'dismiss'                         => __( 'Dismiss this notice', 'dmmcrm' ),
            'notice_cannot_install_activate'  => __( 'There are one or more required or recommended plugins to install, update or activate.', 'dmmcrm' ),
            'contact_admin'                   => __( 'Please contact the administrator of this site for help.', 'dmmcrm' ),

            'nag_type'                        => '', // Determines admin notice type - can only be one of the typical WP notice classes, such as 'updated', 'update-nag', 'notice-warning', 'notice-info' or 'error'. Some of which may not work as expected in older WP versions.
        ),
        */
    );

    tgmpa( $plugins, $config );
}