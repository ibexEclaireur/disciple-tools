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
 */
function activate_disciple_tools($network_wide) {
    require_once plugin_dir_path(__FILE__) . 'dt-core/admin/class-activator.php';
    Disciple_Tools_Activator::activate($network_wide);
}
register_activation_hook(__FILE__, 'activate_disciple_tools');

/**
 * Deactivation Hook
 */
function deactivate_disciple_tools($network_wide) {
    require_once plugin_dir_path(__FILE__) . 'dt-core/admin/class-deactivator.php';
    Disciple_Tools_Deactivator::deactivate($network_wide);
}
register_deactivation_hook(__FILE__, 'deactivate_disciple_tools');

/**
 * Multisite datatable maintenance
 */
function on_create_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
    require_once plugin_dir_path(__FILE__) . 'dt-core/admin/class-activator.php';
    Disciple_Tools_Activator::on_create_blog($blog_id, $user_id, $domain, $path, $site_id, $meta);
}
add_action( 'wpmu_new_blog', 'on_create_blog', 10, 6 );
function on_delete_blog( $tables ) {
    require_once plugin_dir_path(__FILE__) . 'dt-core/admin/class-activator.php';
    return Disciple_Tools_Activator::on_delete_blog( $tables );
}
add_filter( 'wpmu_drop_tables', 'on_delete_blog' );
/* End Multisite datatable maintenance */


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
		$this->plugin_img       = plugin_dir_url( __FILE__ ) . 'dt-core/img/';
		$this->plugin_js        = plugin_dir_url( __FILE__ ) . 'dt-core/js/';
		$this->plugin_css       = plugin_dir_url( __FILE__ ) . 'dt-core/css/';
        $this->includes         = plugin_dir_url( __FILE__ ) . 'dt-core/';
        $this->includes_path    = plugin_dir_path( __FILE__ ) . 'dt-core/';
        $this->factories        = plugin_dir_url( __FILE__ ) . 'dt-core/factories/';

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
            require_once('dt-core/admin/config-options-admin.php');
            $this->admin = Disciple_Tools_Admin::instance();

			// Disciple_Tools admin settings page configuration
			require_once('dt-core/admin/config-options-settings.php');
			$this->settings = Disciple_Tools_Settings::instance();

            // Load plugin library that "requires plugins" at activation
            require_once('dt-core/admin/config-required-plugins.php');

            // Load Disciple_Tools Dashboard
            require_once('dt-core/admin/config-dashboard.php');
			$this->config_dashboard = Disciple_Tools_Dashboard::instance();
            require_once('dt-core/admin/config-contacts.php');
            $this->config_contacts = Disciple_Tools_Config_Contacts::instance();
            require_once('dt-core/admin/config-groups.php');
            $this->config_groups = Disciple_Tools_Config_Groups::instance();

            // Load multiple column configuration library into screen options area.
            require_once('dt-core/admin/three-column-screen-layout.php');
            require_once('dt-core/admin/class-better-author-metabox.php');
            $this->better_metabox = Disciple_Tools_BetterAuthorMetabox::instance();

            // Load report pages
            require_once('dt-core/factories/class-page-factory.php'); // Factory class for page building
            require_once('dt-core/admin/reports-funnel.php');
            $this->reports_funnel = Disciple_Tools_Funnel_Reports::instance();
            require_once('dt-core/admin/reports-media.php');
            $this->reports_media = Disciple_Tools_Media_Reports::instance();
            require_once('dt-core/admin/reports-project.php');
            $this->reports_project = Disciple_Tools_Project_Reports::instance();

            // Load Functions
            require_once('dt-core/functions/hide-contacts.php');
            require_once('dt-core/functions/admin-design.php');
            require_once('dt-core/functions/hide-contacts.php');
            require_once('dt-core/functions/media.php');
            require_once('dt-core/functions/enqueue-scripts.php');
            require_once('dt-core/functions/structure-defaults.php');

            require_once('dt-locations/tab-tools-menu.php');
            $this->location_tools = Disciple_Tools_Location_Tools_Menu::instance();
            require_once('dt-locations/class-upload.php');

            // Profile page
            require_once('dt-core/admin/config-profile.php');
            $this->profile = Disciple_Tools_Profile::instance();

        }
        /* End Admin configuration section */


        /**
         * Data model
         *
         * @posttype Contacts       Post type for contact storage
         * @posttype Groups         Post type for groups storage
         * @posttype Locations      Post type for location information.
         * @posttype People Groups  (optional) Post type for people groups
         * @posttype Prayer         Post type for prayer movement updates.
         * @posttype Project        Post type for movement project updates. (These updates are intended to be for extended owners of the movement project, and different than the prayer guide published in the prayer post type.)
         * @taxonomies
         * @service   Post to Post connections
         * @service   User groups via taxonomies
         */
        // Register Post types
        require_once('dt-contacts/contacts-post-type.php');
        require_once('dt-groups/groups-post-type.php');
        require_once('dt-locations/class-location-post-type.php');
        require_once('dt-prayer/class-prayer-post-type.php');
        require_once('dt-progress/class-progress-post-type.php');
        require_once('dt-assets/class-asset-post-type.php');
        require_once('dt-core/models/class-taxonomy.php');
        $this->post_types['contacts'] = Disciple_Tools_Contact_Post_Type::instance();
        $this->post_types['groups'] = Disciple_Tools_Group_Post_Type::instance();
        $this->post_types['locations'] = Disciple_Tools_Location_Post_Type::instance();
        if(isset(get_option('disciple_tools-general', false)['add_people_groups'])) { /** @see config-p2p.php for the people groups connection registration */
            require_once('dt-people-groups/people-groups-post-type.php');
            $this->post_types['peoplegroups'] = Disciple_Tools_People_Groups_Post_Type::instance();
        }
        $this->post_types['assets'] = Disciple_Tools_Asset_Post_Type::instance();
        $this->post_types['prayer'] = new Disciple_Tools_Prayer_Post_Type( 'prayer', __( 'Prayer Guide', 'disciple_tools' ), __( 'Prayer Guide', 'disciple_tools' ), array( 'menu_icon' => 'dashicons-format-status' ) );
        $this->post_types['progress'] = new Disciple_Tools_Progress_Post_Type( 'progress', __( 'Progress Update', 'disciple_tools' ), __( 'Progress Update', 'disciple_tools' ), array( 'menu_icon' => 'dashicons-location' ) );


        // Creates the post to post relationship between the post type tables.
        // Based on the posts-to-posts project by scribu.
        require_once('dt-core/models/config-p2p.php');
        require_once('dt-core/plugins/posts-to-posts/posts-to-posts.php');


        // Creates User Groups out of Taxonomies
        require_once('dt-core/models/class-user-taxonomy.php');
        $this->user_tax = Disciple_Tools_User_Taxonomy::instance();
        require_once('dt-core/functions/user-groups-taxonomies.php');

        require_once('dt-core/admin/multi-role/multi-role.php');
        $this->multi = Disciple_Tools_Multi_Roles::instance();

        // Metaboxes
        require_once('dt-core/metaboxes/box-four-fields.php');
        require_once('dt-core/metaboxes/box-church-fields.php');
        require_once('dt-core/metaboxes/box-map.php');
        require_once('dt-core/metaboxes/box-activity.php');
        require_once('dt-core/metaboxes/box-address.php');
        require_once('dt-core/metaboxes/box-availability.php');
        /* End model configuration section */

		require_once('dt-core/admin/class-api-keys.php');
		Disciple_Tools_Api_Keys::instance();

        // Activity Logs
        require_once('dt-core/logging/class-activity-api.php');
        $this->activity_api = new Disciple_Tools_Activity_Log_API();
        require_once('dt-core/logging/class-activity-hooks.php'); // contacts and groups report building
        $this->activity_hooks = Disciple_Tools_Activity_Hooks::instance();
        if(is_admin()) {
            require_once('dt-core/logging/class-activity-admin-ui.php'); // contacts and groups report building
            require_once('dt-core/logging/class-activity-list-table.php'); // contacts and groups report building
            require_once('dt-core/logging/class-reports-list-table.php'); // contacts and groups report building
        }
        // Reports and Cron Jobs
        require_once('dt-core/logging/class-reports-api.php');
        $this->report_api = new Disciple_Tools_Reports_API();
        require_once('dt-core/logging/class-reports-cron.php'); // cron scheduling
        $this->report_cron = Disciple_Tools_Reports_Cron::instance();
        require_once('dt-core/logging/class-reports-dt.php'); // contacts and groups report building

        //integrations
        require_once('dt-contacts/contacts-controller.php');
        require_once('dt-groups/groups-controller.php');
        require_once('dt-core/integrations/class-integrations.php'); // data integration for cron scheduling
        if(! class_exists('Ga_Autoloader')) {
            require_once('dt-core/plugins/google-analytics/disciple-tools-analytics.php');
            require_once('dt-core/integrations/class-google-analytics-integration.php');
            $this->analytics_integration = Ga_Admin::instance();
        }
        require_once('dt-core/integrations/class-facebook-integration.php'); // integrations to facebook
        $this->facebook_integration = Disciple_Tools_Facebook_Integration::instance();

        // load rest api endpoints
        require_once('dt-core/functions/rest-api.php'); // sets authentication requirement for rest end points. Disables rest for pre-wp-4.7 sites.
        require_once('dt-contacts/contacts-endpoints.php');
        Disciple_Tools_Rest_Endpoints::instance();


        /*
         * Factories
         */
        require_once('dt-core/factories/class-counter-factory.php');
        $this->counter = Disciple_Tools_Counter_Factory::instance();

        /**
         * Load Functions
         */
        require_once('dt-core/functions/disable-xml-rpc-pingback.php');

        /**
         * Theme Support functions
         */
        require_once('dt-core/theme_support/user-functions-for-themes.php');
        require_once('dt-core/theme_support/group-functions-for-themes.php');
        require_once('dt-core/theme_support/contact-functions-for-themes.php');
        require_once('dt-core/theme_support/location-functions-for-themes.php');
        require_once('dt-core/theme_support/chart-functions-for-themes.php');

        /**
         * Locations Support
         */
        require_once('dt-locations/class-map.php'); // Helper
        require_once('dt-locations/locations-functions.php');
        require_once('dt-locations/class-census-geolocation-api.php');// APIs
        require_once('dt-locations/class-google-geolocation-api.php');
        require_once('dt-locations/class-coordinates-db.php');
        require_once('dt-locations/locations-rest-api.php'); // builds rest endpoints
        require_once('dt-locations/locations-rest-controller.php'); // serves the locations rest endpoints
        $this->location_api = Disciple_Tools_Locations_REST_API::instance();
        /** End Locations */

        /**
         * Multisite
         */
        if(is_multisite()) {
            /** Disciple Tools is intended to be multisite comapatible. Use the section below for if needed for compatibility files. Disciple Tools Multisite plugin is intended to expand features for multisite installations.  @see https://github.com/ChasmSolutions/disciple-tools-multisite  */
        }

        // Language
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

    } // End __construct()



	/**
	 * Load the localisation file.
	 * @access  public
	 * @since   0.1
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'disciple_tools', false, dirname( plugin_basename( __FILE__ ) ) . '/dt-core/languages/' );
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



