<?php
/**
 * Plugin Name: Disciple Tools
 * Plugin URI: https://github.com/ChasmSolutions/Disciple-Tools
 * Description: Disciple Tools is a disciple relationship management system for disciple making movements. The plugin is the core of the system. It is intended to work with the Disciple Tools Theme, and Disciple Tools extension plugins.
 * Version: 0.1
 * Author: Chasm.Solutions
 * Author URI: https://github.com/ChasmSolutions
 * Requires at least: 4.7.0
 * (Requires 4.7+ because of the integration of the REST API at 4.7 and the security requirements of this milestone version.)
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
 * Activation Hook
 * The code that runs during plugin activation.
 * This action is documented in includes/admin/class-activator.php
 */
function activate_disciple_tools() {
    require_once plugin_dir_path(__FILE__) . 'includes/admin/class-activator.php';
    Disciple_Tools_Activator::activate();
}

/**
 * Deactivation Hook
 * The code that runs during plugin deactivation.
 * This action is documented in includes/admin/class-deactivator.php
 */
function deactivate_disciple_tools() {
    require_once plugin_dir_path(__FILE__) . 'includes/admin/class-deactivator.php';
    Disciple_Tools_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_disciple_tools');
register_deactivation_hook(__FILE__, 'deactivate_disciple_tools');



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
    public $report_cron;

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
	 * The facebook_integration object.
	 * @var     object
	 * @access  public
	 * @since   0.1
	 */
	public $facebook_integration;

	/**
	 * The post types we're registering.
	 * @var     array
	 * @access  public
	 * @since   0.1
	 */
	public $post_types = array();

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
	 * Constructor function.
	 * @access  public
	 * @since   0.1
	 */
	public function __construct () {
	    global $wpdb;
		/**
		 * Prepare variables
		 *
		 */
	    $this->token 			= 'disciple_tools';
		$this->version 			= '0.1';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->plugin_img       = plugin_dir_url( __FILE__ ) . 'img/';
		$this->plugin_js        = plugin_dir_url( __FILE__ ) . 'js/';
		$this->plugin_css       = plugin_dir_url( __FILE__ ) . 'css/';
        $this->includes         = plugin_dir_url( __FILE__ ) . 'includes/';
        $this->includes_path    = plugin_dir_path( __FILE__ ) . 'includes/';
        $this->factories        = plugin_dir_url( __FILE__ ) . 'includes/factories/';

        $wpdb->activity = $wpdb->prefix . 'dt_activity_log'; // Prepare database table names
        $wpdb->reports = $wpdb->prefix . 'dt_reports';
        $wpdb->reportmeta = $wpdb->prefix . 'dt_reportmeta';


        /* End prep variables */


		/**
		 * Admin panel
         *
         * Contains all those features that only run if in the Admin panel
		 * or those things directly supporting Admin panel features.
		 */
		if ( is_admin() ) {
            // Disciple_Tools admin settings page configuration
            require_once('includes/admin/config-options-admin.php');
            $this->admin = Disciple_Tools_Admin::instance();

			// Disciple_Tools admin settings page configuration
			require_once('includes/admin/config-options-settings.php');
			$this->settings = Disciple_Tools_Settings::instance();

            // Load plugin library that "requires plugins" at activation
            require_once('includes/admin/config-required-plugins.php');

            // Load Disciple_Tools Dashboard configurations
            require_once('includes/admin/config-dashboard.php');
			$this->admin = Disciple_Tools_Dashboard::instance();

            // Load multiple column configuration library into screen options area.
            require_once ('includes/admin/three-column-screen-layout.php');
            require_once ('includes/admin/class-better-author-metabox.php');
            $this->better_metabox = Disciple_Tools_BetterAuthorMetabox::instance();

            // Load report pages
            require_once('includes/factories/class-page-factory.php'); // Factory class for page building
            require_once ('includes/admin/reports-funnel.php');
            $this->reports_funnel = Disciple_Tools_Funnel_Reports::instance();
            require_once ('includes/admin/reports-media.php');
            $this->reports_media = Disciple_Tools_Media_Reports::instance();
            require_once ('includes/admin/reports-project.php');
            $this->reports_project = Disciple_Tools_Project_Reports::instance();

            // Load Functions
            require_once ('includes/functions/hide-contacts.php');
            require_once ('includes/functions/admin-design.php');
            require_once ('includes/functions/profile.php');
            require_once ('includes/functions/hide-contacts.php');
            require_once ('includes/functions/media.php');
            require_once ('includes/functions/enqueue-scripts.php');

            // Profile page
            require_once ( 'includes/admin/config-profile.php');
            $this->profile = Disciple_Tools_Profile::instance();
        }
        /* End Admin configuration section */


        /**
         * Data model
         *
         * @posttype Contacts
         * @posttype Groups
         * @posttype Prayer
         * @posttype Reports
         * @posttype Locations
         * @taxonomies
         * @service   Post to Post connections
         * @service   User groups via taxonomies
         */
        // Register Post types
        require_once ( 'includes/models/class-contact-post-type.php' );
        require_once ( 'includes/models/class-group-post-type.php' );
        require_once('includes/models/class-prayer-post-type.php');
        require_once ( 'includes/models/class-location-post-type.php' );
        require_once ( 'includes/models/class-taxonomy.php' );
        $this->post_types['contacts'] = new Disciple_Tools_Contact_Post_Type( 'contacts', __( 'Contact', 'disciple_tools' ), __( 'Contacts', 'disciple_tools' ), array( 'menu_icon' => 'dashicons-groups' ) );
        $this->post_types['groups'] = new Disciple_Tools_Group_Post_Type( 'groups', __( 'Group', 'disciple_tools' ), __( 'Groups', 'disciple_tools' ), array( 'menu_icon' => 'dashicons-admin-multisite' ) );
        $this->post_types['prayer'] = new Disciple_Tools_Prayer_Post_Type( 'prayer', __( 'Prayer Guide', 'disciple_tools' ), __( 'Prayer Guide', 'disciple_tools' ), array( 'menu_icon' => 'dashicons-format-status' ) );
        $this->post_types['locations'] = new Disciple_Tools_Location_Post_Type( 'locations', __( 'Location', 'disciple_tools' ), __( 'Locations', 'disciple_tools' ), array( 'menu_icon' => 'dashicons-admin-site' ) );


        // Creates the post to post relationship between the post type tables.
        // Based on the posts-to-posts project by scribu.
        require_once ('includes/models/config-p2p.php');
        require_once ('includes/plugins/posts-to-posts/posts-to-posts.php');


        // Creates User Groups out of Taxonomies
        require_once ( 'includes/models/class-user-taxonomy.php' );
        require_once ( 'includes/functions/user-groups-admin.php' );
        require_once ( 'includes/functions/user-groups-common.php' );
        require_once ( 'includes/functions/user-groups-taxonomies.php' );
        require_once ( 'includes/functions/user-groups-hooks.php' );
        /* End model configuration section */


        // Activity Logs
        require_once ( 'includes/activity/class-activity-api.php' );
        $this->activity_api = new Disciple_Tools_Activity_Log_API();
        require_once ( 'includes/activity/class-activity-hooks.php' ); // contacts and groups report building
        $this->activity_hooks = Disciple_Tools_Activity_Hooks::instance();
        if(is_admin()) {
            require_once ( 'includes/activity/class-activity-admin-ui.php' ); // contacts and groups report building
            require_once ( 'includes/activity/class-activity-list-table.php' ); // contacts and groups report building
            require_once ( 'includes/activity/class-reports-list-table.php' ); // contacts and groups report building
        }
        // Reports and Cron Jobs
        require_once ( 'includes/activity/class-reports-api.php' );
        $this->report_api = new Disciple_Tools_Reports_API();
        require_once ( 'includes/activity/class-reports-cron.php' ); // cron scheduling
        $this->report_cron = Disciple_Tools_Reports_Cron::instance();
        require_once ( 'includes/activity/class-integrations.php' ); // data integration for cron scheduling
        require_once ( 'includes/activity/class-reports-dt.php' ); // contacts and groups report building
        require_once('includes/admin/class-facebook-integration.php'); // integrations to facebook
        $this->facebook_integration = Disciple_Tools_Facebook_Integration::instance();

        // load rest api endpoints
        require_once ('includes/functions/disable-json-api.php'); // sets authentication requirement for rest end points. Disables rest for pre-wp-4.7 sites.
        add_action('rest_api_init', array($this, "add_api_routes"));

        /*
         * Factories
         */
        require_once ('includes/factories/class-counter-factory.php');
        $this->counter = Disciple_Tools_Counter_Factory::instance();


		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );


    } // End __construct()


    /**
     * Setup the rest api routes for the plugin
     */
    public function add_api_routes(){
        // setup the facebook endpoints
        $this->facebook_integration->add_api_routes();
    }


	/**
	 * Load the localisation file.
	 * @access  public
	 * @since   0.1
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'disciple_tools', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	} // End load_plugin_textdomain()

    /**
     * Log the plugin version number.
     * @access  private
     * @since   0.1
     */
    public function _log_version_number () {
        // Log the version number.
        update_option( $this->token . '-version', $this->version );
    } // End _log_version_number()

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

} // End Class


