<?php
/**
 * Plugin Name: Disciple Tools
 * Plugin URI: https://github.com/ChasmSolutions/Disciple-Tools
 * Description: Disciple Tools is a disciple relationship management system for disciple making movements. The plugin is the core of the system. It is intended to work with the Disciple Tools Theme, and Disciple Tools extension plugins.
 * Version: 0.1
 * Author: Chasm.Solutions
 * Author URI: https://github.com/ChasmSolutions
 * Requires at least: 4.5.0
 * Tested up to: 4.7.2
 *
 * @package   Disciple_Tools
 * @author 	  Chasm Solutions <chasm.crew@chasm.solutions>
 * @link      https://github.com/ChasmSolutions
 * @license   GPL-3.0
 * @version   0.1
 * 
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Returns the main instance of Disciple_Tools to prevent the need to use globals.
 *
 * @since  0.1
 * @return object Disciple_Tools
 */

    // Adds the Disciple_Tools Plugin after plugins load
    add_action( 'plugins_loaded', 'Disciple_Tools' );

    // Creates the instance
    function Disciple_Tools() {
        return Disciple_Tools::instance();
    }


/**
 * Main Disciple_Tools Class
 *
 * @class Disciple_Tools
 * @since 0.1
 * @package	Disciple_Tools
 * @author Chasm.Solutions & Kingdom.Training
 */
class Disciple_Tools {
	/**
	 * Disciple_Tools The single instance of Disciple_Tools.
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
		 * Prepare variables
		 *
		 */
	    $this->token 			= 'disciple_tools';
		$this->version 			= '0.1';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->plugin_img       = plugin_dir_url( __FILE__ ) . '/img/';
		$this->plugin_js        = plugin_dir_url( __FILE__ ) . '/js/';
		$this->plugin_css       = plugin_dir_url( __FILE__ ) . '/css/';
        $this->factories        = plugin_dir_url( __FILE__ ) . 'includes/factories/';
        $this->includes         = plugin_dir_path( __FILE__ ) . 'includes/';
        /* End prep variables */


		/**
		 * Admin configuration section
         *
         * Contains all those features that only run if in the Admin panel
		 * or those things directly supporting Admin panel features.
		 *
		 */
		if ( is_admin() ) {
            // Disciple_Tools admin settings page configuration
            require_once ( 'includes/config/config-admin.php' );
            $this->admin = Disciple_Tools_Admin::instance();

			// Disciple_Tools admin settings page configuration
			require_once ( 'includes/config/config-settings.php' );
			$this->settings = Disciple_Tools_Settings::instance();

            // Load plugin library that "requires plugins" at activation
            require_once ( 'includes/config/config-required-plugins.php' );

            // Load Disciple_Tools Dashboard configurations
            require_once ( 'includes/config/config-dashboard.php' );
			$this->admin = Disciple_Tools_Dashboard::instance();

            // Load multiple column configuration library into screen options area.
            require_once('includes/config/three-column-screen-layout.php');

        }
		// Admin panel filters
        require_once('includes/config/drm-filters.php');

		// Counters
		require_once('includes/factories/counter-factory.php');
		$this->counter = Disciple_Tools_Counter_Factory::instance();

        /* End Admin configuration section */


        /**
         * Data model configuration section
         *
         * @posttype Contacts
         * @posttype Groups
         * @posttype Locations
         * @taxonomies
         * @postconnector   P2P connection
         *
         */
        // Contacts post types
        require_once( 'includes/classes/class-contact-post-type.php' );
        $this->post_types['contacts'] = new Disciple_Tools_Contact_Post_Type( 'contacts', __( 'Contact', 'disciple_tools' ), __( 'Contacts', 'disciple_tools' ), array( 'menu_icon' => 'dashicons-groups' ) );

        // Groups post types
        require_once( 'includes/classes/class-group-post-type.php' );
        $this->post_types['groups'] = new Disciple_Tools_Group_Post_Type( 'groups', __( 'Group', 'disciple_tools' ), __( 'Groups', 'disciple_tools' ), array( 'menu_icon' => 'dashicons-admin-multisite' ) );

        // Locations post types
        // require_once( 'includes/classes/class-location-post-type.php' ); //TODO: Reactivate when ready for development
        // $this->post_types['locations'] = new Disciple_Tools_Location_Post_Type( 'locations', __( 'Location', 'disciple_tools' ), __( 'Locations', 'disciple_tools' ), array( 'menu_icon' => 'dashicons-admin-site' ) ); //TODO: Reactivate when ready for development

		// Taxonomies
		require_once( 'includes/classes/class-taxonomy.php' );

        // Creates the post to post relationship between the post type tables.
        require_once ( 'includes/config/config-p2p.php' );
        require_once ( 'includes/plugins/posts-to-posts/posts-to-posts.php' );
        /* End model configuration section */


        /**
         * Overall site configuration section
         *
         */
		// Set the site to private.
        require_once( 'includes/config/config-private-site.php' );
		// Load security modifications to site.
        require_once ( 'includes/config/config-site.php');
		// Load shortcodes
        require_once('includes/classes/class-shortcodes.php');
        $this->shortcodes = Disciple_Tools_Function_Callback::instance();

        /* End overall site configuration section */

		/**
		 * Activation section
		 *
		 */
        require_once('includes/classes/class-runonce.php');
        $this->run_once = new run_once;


        if ($this->run_once->run('activation') ) {
            // Roles and capabilities
            require_once('includes/classes/class-roles.php');
            $this->roles = Disciple_Tools_Roles::instance();
            $this->roles->set_roles();

        }

		register_activation_hook( __FILE__, array( 'Disciple_Tools', 'install' ) );
        /* End Activation seciton */

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

    } // End __construct()


	/**
	 * Main Disciple_Tools Instance
	 *
	 * Ensures only one instance of Disciple_Tools is loaded or can be loaded.
	 *
	 * @since 0.1
	 * @static
	 * @see Disciple_Tools()
	 * @return Disciple_Tools instance
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
		load_plugin_textdomain( 'disciple_tools', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
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
	static function install () {
        Disciple_Tools::instance()->_log_version_number();
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


