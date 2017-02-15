<?php
/**
 * Plugin Name: DRM
 * Plugin URI: https://github.com/ChasmSolutions/DMM-CRM-Plugin
 * Description: DRM is a contact relationship management system for disciple making movements.
 * Version: 0.1
 * Author: Chasm.Solutions & Kingdom.Training
 * Author URI: https://github.com/ChasmSolutions
 * Requires at least: 4.0.0
 * Tested up to: 4.7.2
 *
 * @package   DRM
 * @author 	  Chasm Solutions <chasm.crew@chasm.solutions>
 * @link      https://github.com/ChasmSolutions
 * @license   GPL-3.0
 * @version   0.1
 * 
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



/**
 * Returns the main instance of DRM_Plugin to prevent the need to use globals.
 *
 * @since  0.1
 * @return object DRM_Plugin
 */

    // Adds the DRM Plugin after plugins load
    add_action( 'plugins_loaded', 'DRM_Plugin' );

    // Creates the instance
    function DRM_Plugin() {
        return DRM_Plugin::instance();
    }


/**
 * Main DRM_Plugin Class
 *
 * @class DRM_Plugin
 * @since 0.1
 * @package	DRM_Plugin
 * @author Chasm.Solutions & Kingdom.Training
 */
class DRM_Plugin {
	/**
	 * DRM_Plugin The single instance of DRM_Plugin.
	 * @var 	object
	 * @access  private
	 * @since  0.1
	 */
	private static $_instance = null;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   0.1
	 */
	public $token;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   0.1
	 */
	public $version;

	/**
	 * The plugin directory URL.
	 * @var     string
	 * @access  public
	 * @since   0.1
	 */
	public $plugin_url;

	/**
	 * The plugin directory path.
	 * @var     string
	 * @access  public
	 * @since   0.1
	 */
	public $plugin_path;

    /**
     * Activation of roles.
     * @var     string
     * @access  public
     * @since   0.1
     */
    private $roles;

	/**
	 * The admin object.
	 * @var     object
	 * @access  public
	 * @since   0.1
	 */
	public $admin;

	/**
	 * The settings object.
	 * @var     object
	 * @access  public
	 * @since   0.1
	 */
	public $settings;

	/**
	 * The post types we're registering.
	 * @var     array
	 * @access  public
	 * @since   0.1
	 */
	public $post_types = array();

	/**
	 * Constructor function.
	 * @access  public
	 * @since   0.1
	 */
	public function __construct () {
		/**
		 * Prepares variables
		 *
		 */
	    $this->token 			= 'drm';
		$this->version 			= '0.1';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->plugin_img       = plugin_dir_url( __FILE__ ) . 'includes/img/';
		$this->plugin_js        = plugin_dir_url( __FILE__ ) . 'includes/js/';
		$this->plugin_css        = plugin_dir_url( __FILE__ ) . 'includes/css/';
        /* End prep of variables */


		/**
		 * Admin configuration section
         *
         * Contains all those features that only run if in the Admin panel
		 * or those things directly supporting Admin panel features.
		 *
		 */
		if ( is_admin() ) {
            // DRM admin settings page configuration
            require_once ( 'includes/config/config-admin.php' );
            $this->admin = DRM_Plugin_Admin::instance();

			// DRM admin settings page configuration
			require_once ( 'includes/config/config-settings.php' );
			$this->settings = DRM_Plugin_Settings::instance();

            // Load plugin library that "requires plugins" at activation
            require_once ( 'includes/config/config-required-plugins.php' );

            // Load DRM Dashboard configurations
            require_once ( 'includes/config/config-dashboard.php' );
			$this->admin = DRM_Dashboard::instance();

            // Load multiple column configuration library into screen options area.
            require_once ( 'includes/plugins/three-column-screen-layout.php' );

        }


            // Admin panel filters
            require_once('includes/config/drm-filters.php');

        /* End Administrative panel section */


        /**
         * Data model configuration section
         *
         * @posttype Contacts
         * @posttype Groups
         * @posttype Locations
         * @postconnector   P2P connection
         *
         */
        // Contacts post types
        require_once( 'includes/classes/class-contact-post-type.php' );
        $this->post_types['contacts'] = new DRM_Plugin_Contact_Post_Type( 'contacts', __( 'Contact', 'drm' ), __( 'Contacts', 'drm' ), array( 'menu_icon' => 'dashicons-groups' ) );

        // Groups post types
        require_once( 'includes/classes/class-group-post-type.php' );
        $this->post_types['groups'] = new DRM_Plugin_Group_Post_Type( 'groups', __( 'Group', 'drm' ), __( 'Groups', 'drm' ), array( 'menu_icon' => 'dashicons-admin-multisite' ) );

        // Taxonomies
        require_once( 'includes/classes/class-taxonomy.php' );

        // Locations post types
        // require_once( 'includes/classes/class-location-post-type.php' ); //TODO: Reactivate when ready for development
        // $this->post_types['locations'] = new DRM_Plugin_Location_Post_Type( 'locations', __( 'Location', 'drm' ), __( 'Locations', 'drm' ), array( 'menu_icon' => 'dashicons-admin-site' ) ); //TODO: Reactivate when ready for development

        // Creates the post to post relationship between the post type tables.
        require_once ( 'includes/config/config-p2p.php' );
        require_once ( 'includes/plugins/posts-to-posts/posts-to-posts.php' );
        /* End post type configuration section */


        /**
         * Overall site configuration section
         *
         */


        // Sets the site to private.
        require_once( 'includes/config/config-private-site.php' );



        /**
         * Load security modifications to site.
         */
        require_once ( 'includes/config/config-site.php');





        // Activation section
        require_once( 'includes/services/service-runonce.php' );
        $this->run_once = new run_once;


        if ($this->run_once->run('activation') ) {
            // Roles and capabilities
            require_once ('includes/config/config-roles.php');
            $this->roles = DRM_Roles::instance();
            $this->roles->set_roles();
        }

		




		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

    } // End __construct()


	/**
	 * Main DRM_Plugin Instance
	 *
	 * Ensures only one instance of DRM_Plugin is loaded or can be loaded.
	 *
	 * @since 0.1
	 * @static
	 * @see DRM_Plugin()
	 * @return DRM_Plugin instance
	 */
	public static function instance () {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

	/**
	 * Load the localisation file.
	 * @access  public
	 * @since   0.1
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'drm', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	} // End load_plugin_textdomain()

	/**
	 * Cloning is forbidden.
	 * @access public
	 * @since 0.1
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 * @access public
	 * @since 0.1
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	} // End __wakeup()

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   0.1
	 */
	public function install () {
        $this->_log_version_number();
    } // End install()

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   0.1
	 */
	private function _log_version_number () {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	} // End _log_version_number()
} // End Class


