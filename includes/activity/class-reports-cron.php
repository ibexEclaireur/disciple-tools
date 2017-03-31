<?php

/**
 * Disciple Tools
 *
 * @class Disciple_Tools_
 * @version	0.1
 * @since 0.1
 * @package	Disciple_Tools
 * @author Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Disciple_Tools_Reports_Cron {

    /**
     * Disciple_Tools_Reports_Cron The single instance of Disciple_Tools_Reports_Cron.
     * @var 	object
     * @access  private
     * @since 	0.1
     */
    private static $_instance = null;

    /**
     * Main Disciple_Tools_Reports_Cron Instance
     *
     * Ensures only one instance of Disciple_Tools_Admin_Menus is loaded or can be loaded.
     *
     * @since 0.1
     * @static
     * @return Disciple_Tools_Reports_Cron instance
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

        // Registers the daily report cron jobs
        add_action( 'init', 'register_daily_report_events');

        // Adds action for Facebook report build
        add_action( 'build_facebook_reports', 'build_all_facebook_reports' );

    } // End __construct()

    /**
     * Main scheduler for daily report builds
     * @return void
     */
    public function register_daily_report_events() {
        // Make sure this event hasn't been scheduled
        if( !wp_next_scheduled( 'build_facebook_reports' ) ) {
            // Schedule the event
            wp_schedule_event( time(), 'daily', 'build_facebook_reports' );
        }
    }

    public function build_all_facebook_reports () {

    }


}