<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Activity Log Configuration
 *
 * This is the configuration file for the bundled plugin 'Aryo-Actity-Log' located in /includes/plugins/aryo-activity-log
 *
 * @author Chasm Solutions
 * @package Disciple_Tools
 */

if ( class_exists('AAL_Main')) {

    class Disciple_Tools_Activity_Log_Config {

        public $slug = 'activity-log-settings';

        public function __construct() {
            add_action( 'admin_menu', array( &$this, 'remove_default_submenu_page' ), 99 );
        }

        /**
         * Removes the default Activity Log submenu found in class-aal-settings.php
         *
         * @since 0.1
         */
        public function remove_default_submenu_page() {
            remove_submenu_page( 'activity_log_page', 'activity-log-settings' );

        }

    }
    new Disciple_Tools_Activity_Log_Config();

}