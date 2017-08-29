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
 * @package Disciple_Tools
 * @author  Chasm Solutions <chasm.crew@chasm.solutions>
 * @link    https://github.com/ChasmSolutions
 * @license GPL-3.0
 * @version 0.1
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

function admin_notice_required_php_version_dt() {
    ?>
    <div class="notice notice-error">
        <p><?php _e( "The Disciple Tools plug-in requires PHP 7.0 or greater before it will have any effect. Please upgrade your PHP version or uninstall this plugin." ); ?></p>
    </div>
    <?php
}

if (version_compare( phpversion(), '7.0', '<' )) {

    /* We only support PHP >= 7.0, however, we want to support allowing users
     * to install this plugin even on old versions of PHP, without showing a
     * horrible message, but instead a friendly notice.
     *
     * For this to work, this file must be compatible with old PHP versions.
     * Feel free to use PHP 7 features in other files, but not in this one.
     */

    add_action( 'admin_notices', 'admin_notice_required_php_version_dt' );
    error_log( 'Disciple Tools plugin requires PHP version 7.0 or greater, please upgrade PHP or uninstall this plugin' );
    return;
}

/**
 * Activation Hook
 */
function activate_disciple_tools( $network_wide ) {
    require_once plugin_dir_path( __FILE__ ) . 'dt-core/admin/class-activator.php';
    Disciple_Tools_Activator::activate( $network_wide );
}
register_activation_hook( __FILE__, 'activate_disciple_tools' );

/**
 * Deactivation Hook
 */
function deactivate_disciple_tools( $network_wide ) {
    require_once plugin_dir_path( __FILE__ ) . 'dt-core/admin/class-deactivator.php';
    Disciple_Tools_Deactivator::deactivate( $network_wide );
}
register_deactivation_hook( __FILE__, 'deactivate_disciple_tools' );

/**
 * Multisite datatable maintenance
 */
function on_create_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
    require_once plugin_dir_path( __FILE__ ) . 'dt-core/admin/class-activator.php';
    Disciple_Tools_Activator::on_create_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta );
}
add_action( 'wpmu_new_blog', 'on_create_blog', 10, 6 );
function on_delete_blog( $tables ) {
    require_once plugin_dir_path( __FILE__ ) . 'dt-core/admin/class-activator.php';
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
 * @class   Disciple_Tools
 * @since   0.1
 * @package Disciple_Tools
 * @author  Chasm.Solutions & Kingdom.Training
 */
class Disciple_Tools {
    /**
     * Disciple_Tools The single instance of Disciple_Tools.
     *
     * @var    object
     * @access private
     * @since  0.1
     */
    private static $_instance = null;

    /**
     * The token.
     *
     * @var    string
     * @access public
     * @since  0.1
     */
    public $token;


    /**
     * The version number.
     *
     * @var    string
     * @access public
     * @since  0.1
     */
    public $version;

    /**
     * The plugin directory URL.
     *
     * @var    string
     * @access public
     * @since  0.1
     */
    public $plugin_url;

    /**
     * The plugin directory path.
     *
     * @var    string
     * @access public
     * @since  0.1
     */
    public $plugin_path;

    /**
     * Activation of roles.
     *
     * @var    string
     * @access public
     * @since  0.1
     */
    private $roles;
    public $report_cron;

    /**
     * The admin object.
     *
     * @var    object
     * @access public
     * @since  0.1
     */
    public $admin;

    /**
     * The settings object.
     *
     * @var    object
     * @access public
     * @since  0.1
     */
    public $settings;

    /**
     * The facebook_integration object.
     *
     * @var    object
     * @access public
     * @since  0.1
     */
    public $facebook_integration;

    /**
     * The post types we're registering.
     *
     * @var    array
     * @access public
     * @since  0.1
     */
    public $post_types = [];

    /**
     * Main Disciple_Tools Instance
     *
     * Ensures only one instance of Disciple_Tools is loaded or can be loaded.
     *
     * @since  0.1
     * @static
     * @see    Disciple_Tools()
     * @return Disciple_Tools instance
     */
    public static function instance () {
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
    public function __construct () {
        global $wpdb;

        /**
         * Prepare variables
         */
        $this->token            = 'disciple_tools';
        $this->version          = '0.1';
        $this->plugin_url       = plugin_dir_url( __FILE__ );
        $this->plugin_path      = plugin_dir_path( __FILE__ );
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

            // Administration
            require_once( 'dt-core/admin/config-options-admin.php' ); // General admin settings page
            $this->admin = Disciple_Tools_Admin::instance();
            require_once( 'dt-core/admin/config-options-settings.php' ); // General admin settings page
            $this->settings = Disciple_Tools_Settings::instance();
            require_once( 'dt-core/admin/enqueue-scripts.php' ); // Load admin scripts
            require_once( 'dt-core/admin/admin-theme-design.php' ); // Configures elements of the admin enviornment
            require_once( 'dt-core/admin/restrict-record-access-in-admin.php' ); //
            require_once( 'dt-core/admin/three-column-screen-layout.php' ); // Adds multicolumn configuration to screen options
            require_once( 'dt-core/admin/class-better-author-metabox.php' ); // Allows multiple authors to be selected as post author
            $this->better_metabox = Disciple_Tools_BetterAuthorMetabox::instance();

            // Profile
            require_once( 'dt-core/admin/config-profile.php' );
            $this->profile = Disciple_Tools_Profile::instance();

            // Dashboard
            require_once( 'dt-core/admin/config-dashboard.php' );
            $this->config_dashboard = Disciple_Tools_Dashboard::instance();
            require_once( 'dt-statistics/class-page-factory.php' ); // Factory class for page building
            require_once( 'dt-statistics/reports-funnel.php' );
            $this->reports_funnel = Disciple_Tools_Funnel_Reports::instance();
            require_once( 'dt-statistics/reports-media.php' );
            $this->reports_media = Disciple_Tools_Media_Reports::instance();
            require_once( 'dt-statistics/reports-project.php' );
            $this->reports_project = Disciple_Tools_Project_Reports::instance();

            // Contacts
            require_once( 'dt-contacts/contacts-config.php' );
            $this->config_contacts = Disciple_Tools_Config_Contacts::instance();

            // Groups
            require_once( 'dt-groups/groups-config.php' );
            $this->config_groups = Disciple_Tools_Groups_Config::instance();

            // Locations
            require_once( 'dt-locations/admin-menu.php' );
            $this->location_tools = Disciple_Tools_Location_Tools_Menu::instance();
            require_once( 'dt-locations/class-import.php' ); // import class
            require_once( 'dt-locations/admin-tab-import.php' ); // import tab page
            require_once( 'dt-locations/admin-tab-usa-lookup.php' ); // testing page, not intended for production

            // People Groups
            require_once( 'dt-people-groups/admin-menu.php' );
            $this->people_groups_admin = Disciple_Tools_People_Groups_Admin_Menu::instance();


            // Assets


            // Progress


            // Messaging


            // Logging
            require_once( 'dt-core/logging/class-activity-admin-ui.php' ); // contacts and groups report building
            require_once( 'dt-core/logging/class-activity-list-table.php' ); // contacts and groups report building
            require_once( 'dt-core/logging/class-reports-list-table.php' ); // contacts and groups report building
            
            // Options Menu
            require_once( 'dt-core/admin/class-menu.php' );
            $this->page = Disciple_Tools_Options_Menu::instance();

        }
        /* End Admin configuration section */

        require_once( 'dt-core/admin/config-site-defaults.php' ); // Force required site configurations

        /**
         * Rest API Support
         */
        require_once( 'dt-core/integrations/class-api-keys.php' ); // API keys for remote access
        $this->api_keys = Disciple_Tools_Api_Keys::instance();
        require_once( 'dt-core/admin/restrict-rest-api.php' ); // sets authentication requirement for rest end points. Disables rest for pre-wp-4.7 sites.
        require_once( 'dt-core/admin/restrict-xml-rpc-pingback.php' ); // protect against DDOS attacks.

        /**
         * User Groups & Multi Roles
         */
        require_once( 'dt-core/admin/user-groups/class-user-taxonomy.php' );
        $this->user_tax = Disciple_Tools_User_Taxonomy::instance();
        require_once( 'dt-core/admin/user-groups/user-groups-taxonomies.php' );
        require_once( 'dt-core/admin/multi-role/multi-role.php' );
        $this->multi = Disciple_Tools_Multi_Roles::instance();


        /**
         * Data model
         *
         * @posttype   Contacts       Post type for contact storage
         * @posttype   Groups         Post type for groups storage
         * @posttype   Locations      Post type for location information.
         * @posttype   People Groups  (optional) Post type for people groups
         * @posttype   Prayer         Post type for prayer movement updates.
         * @posttype   Project        Post type for movement project updates. (These updates are intended to be for extended owners of the movement project, and different than the prayer guide published in the prayer post type.)
         * @taxonomies
         * @service    Post to Post connections
         * @service    User groups via taxonomies
         */
        require_once( 'dt-core/class-taxonomy.php' );

        /**
         * dt-contacts
         */
        require_once( 'dt-contacts/contacts-post-type.php' );
        $this->post_types['contacts'] = Disciple_Tools_Contact_Post_Type::instance();


        require_once( 'dt-contacts/contacts-endpoints.php' );
        Disciple_Tools_Contacts_Endpoints::instance();
        require_once( 'dt-contacts/contacts-template.php' ); // Functions to support theme


        /**
         * dt-groups
         */
        require_once( 'dt-groups/groups-post-type.php' );
        $this->post_types['groups'] = Disciple_Tools_Groups_Post_Type::instance();
        require_once( 'dt-groups/groups.php' );
        require_once( 'dt-groups/groups-endpoints.php' ); // builds rest endpoints
        require_once( 'dt-groups/groups-template.php' ); // Functions to support theme


        /**
         * dt-locations
         */
        require_once( 'dt-locations/locations-post-type.php' );
        $this->post_types['locations'] = Disciple_Tools_Location_Post_Type::instance();
        require_once( 'dt-locations/class-map.php' ); // Helper
        require_once( 'dt-locations/locations-template.php' );
        require_once( 'dt-locations/class-census-geolocation-api.php' );// APIs
        require_once( 'dt-locations/class-google-geolocation-api.php' );
        require_once( 'dt-locations/class-coordinates-db.php' );
        require_once( 'dt-locations/locations-endpoints.php' ); // builds rest endpoints
        require_once( 'dt-locations/locations.php' ); // serves the locations rest endpoints
        $this->location_api = Disciple_Tools_Locations_Endpoints::instance();


        /**
         * dt-people-groups
         */
        require_once( 'dt-people-groups/people-groups-post-type.php' );
        $this->post_types['peoplegroups'] = Disciple_Tools_People_Groups_Post_Type::instance();
        require_once( 'dt-people-groups/people-groups-template.php' );
        require_once( 'dt-people-groups/people-groups.php' );
        require_once( 'dt-people-groups/people-groups-endpoints.php' ); // builds rest endpoints


        /**
         * dt-resources
         */
        require_once( 'dt-resources/resources-post-type.php' );
        $this->post_types['resources'] = Disciple_Tools_Resources_Post_Type::instance();
        require_once( 'dt-resources/resources-template.php' );
        require_once( 'dt-resources/resources.php' );
        require_once( 'dt-resources/resources-endpoints.php' ); // builds rest endpoints


        /**
         * dt-prayer
         */
        require_once( 'dt-prayer/prayer-post-type.php' );
        $this->post_types['prayer'] = new Disciple_Tools_Prayer_Post_Type( 'prayer', __( 'Prayer Guide', 'disciple_tools' ), __( 'Prayer Guide', 'disciple_tools' ), [ 'menu_icon' => 'dashicons-format-status' ] );
        require_once( 'dt-prayer/prayer-template.php' );
        require_once( 'dt-prayer/prayer.php' );
        require_once( 'dt-prayer/prayer-endpoints.php' ); // builds rest endpoints


        /**
         * dt-progress
         */
        require_once( 'dt-progress/progress-post-type.php' );
        $this->post_types['progress'] = new Disciple_Tools_Progress_Post_Type( 'progress', __( 'Progress Update', 'disciple_tools' ), __( 'Progress Update', 'disciple_tools' ), [ 'menu_icon' => 'dashicons-location' ] );
        require_once( 'dt-asset-mapping/asset-mapping-endpoints.php' ); // builds rest endpoints

        /**
         * dt-assets
         */
        require_once( 'dt-asset-mapping/asset-mapping-post-type.php' );
        $this->post_types['assetmapping'] = Disciple_Tools_Asset_Mapping_Post_Type::instance();
        require_once( 'dt-asset-mapping/asset-mapping-endpoints.php' ); // builds rest endpoints
        require_once( 'dt-asset-mapping/asset-mapping.php' );
        require_once( 'dt-asset-mapping/asset-mapping-template.php' );

        /**
         * dt-statistics
         */
        require_once( 'dt-statistics/class-counter-factory.php' );
        $this->counter = Disciple_Tools_Counter_Factory::instance();
        require_once( 'dt-statistics/chart-template.php' );

        /**
         * dt-users
         */
        require_once( 'dt-users/users.php' );
        require_once( 'dt-users/users-endpoints.php' );
        require_once( 'dt-users/users-template.php' );


        /**
         * Metaboxes
         */
        require_once( 'dt-core/config-p2p.php' );// Creates the post to post relationship between the post type tables.
        require_once( 'dt-core/libraries/posts-to-posts/posts-to-posts.php' ); // P2P library/plugin
        require_once( 'dt-core/metaboxes/box-four-fields.php' );
        require_once( 'dt-core/metaboxes/box-church-fields.php' );
        require_once( 'dt-core/metaboxes/box-map.php' );
        require_once( 'dt-core/metaboxes/box-activity.php' );
        require_once( 'dt-core/metaboxes/box-address.php' );
        require_once( 'dt-core/metaboxes/box-availability.php' );


        /**
         * Logging
         */
        require_once( 'dt-core/logging/class-activity-api.php' );
        $this->activity_api = new Disciple_Tools_Activity_Log_API();
        require_once( 'dt-core/logging/class-activity-hooks.php' ); // contacts and groups report building
        $this->activity_hooks = Disciple_Tools_Activity_Hooks::instance();
        require_once( 'dt-core/logging/class-reports-api.php' );
        $this->report_api = new Disciple_Tools_Reports_API();
        require_once( 'dt-core/logging/class-reports-cron.php' ); // Cron scheduling for nightly builds of reports
        $this->report_cron = Disciple_Tools_Reports_Cron::instance();
        require_once( 'dt-core/logging/class-reports-dt.php' ); // contacts and groups report building


        /**
         * Integrations
         */
        require_once( 'dt-core/integrations/class-integrations.php' ); // data integration for cron scheduling
        if(! class_exists( 'Ga_Autoloader' )) {
            require_once( 'dt-core/libraries/google-analytics/disciple-tools-analytics.php' );
            require_once( 'dt-core/integrations/class-google-analytics-integration.php' );
            $this->analytics_integration = Ga_Admin::instance();
        }
        require_once( 'dt-core/integrations/class-facebook-integration.php' ); // integrations to facebook
        $this->facebook_integration = Disciple_Tools_Facebook_Integration::instance();


        /**
         * Multisite
         */
        if(is_multisite()) {
            /**
 * Disciple Tools is intended to be multisite comapatible. Use the section below for if needed for compatibility files. Disciple Tools Multisite plugin is intended to expand features for multisite installations.  @see https://github.com/ChasmSolutions/disciple-tools-multisite
*/
        }

        // Language
        add_action( 'init', [ $this, 'load_plugin_textdomain' ] );

    } // End __construct()



    /**
     * Load the localisation file.
     *
     * @access public
     * @since  0.1
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain( 'disciple_tools', false, dirname( plugin_basename( __FILE__ ) ) . '/dt-core/languages/' );
    } // End load_plugin_textdomain()

    /**
     * Log the plugin version number.
     *
     * @access private
     * @since  0.1
     */
    public function _log_version_number () {
        // Log the version number.
        update_option( $this->token . '-version', $this->version );
    } // End _log_version_number()

    /**
     * Cloning is forbidden.
     *
     * @access public
     * @since  0.1
     */
    public function __clone () {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
    } // End __clone()

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @access public
     * @since  0.1
     */
    public function __wakeup () {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
    } // End __wakeup()

} // End Class



