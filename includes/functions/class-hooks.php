<?php

/**
 * Disciple_Tools_Functions
 *
 * @class Disciple_Tools_Functions
 * @version	0.1
 * @since 0.1
 * @package	Disciple_Tools
 * @author Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Disciple_Tools_Functions {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Disciple_Tools_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * Disciple_Tools_Functions The single instance of Disciple_Tools_Functions.
     * @var 	object
     * @access  private
     * @since 	0.1
     */
    private static $_instance = null;

    /**
     * Main Disciple_Tools_Functions Instance
     *
     * Ensures only one instance of Disciple_Tools_Functions is loaded or can be loaded.
     *
     * @since 0.1
     * @static
     * @return Disciple_Tools_Functions instance
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
        $this->path = plugin_dir_path(dirname(__FILE__)) . 'includes/functions';

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();

    } // End __construct()

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    0.1
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once 'class-loader.php';

        /**
         * Login configuration functions class
         */
        require_once 'login.php';


        $this->loader = new Disciple_Tools_Loader();
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        // Load Login Functions
        $login = new disciple_tools_login();
        $this->loader->add_filter( 'login_headerurl', $login, 'my_login_logo_url', 10 );
        $this->loader->add_filter( 'login_headertitle', $login, 'my_login_logo_url_title', 10 );

    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {



    }

}