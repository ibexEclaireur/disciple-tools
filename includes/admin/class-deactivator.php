<?php

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      0.1
 * @package    Disciple_Tools
 * @subpackage Disciple_Tools/includes/admin
 * @author     
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Disciple_Tools_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    0.1
	 */
	public static function deactivate() {

        // Reset roles and capabilities
        require_once('class-roles.php');
        $roles = Disciple_Tools_Roles::instance();
        $roles->reset_roles();

        /* Determines if on deactivate you have checked to remove database content */
        if (get_option('delete_activity_db')) {
            self::_remove_tables();
        }
        self::_remove_tables();

	}

    /**
     * Removes the tables for the activity and report logs.
     * @access protected
     */
    protected static function _remove_tables() {
        global $wpdb;

        $wpdb->query( "DROP TABLE IF EXISTS `{$wpdb->prefix}dt_activity_log`;" );
        $wpdb->query( "DROP TABLE IF EXISTS `{$wpdb->prefix}dt_reports`;" );
        $wpdb->query( "DROP TABLE IF EXISTS `{$wpdb->prefix}dt_reportmeta`;" );

        delete_option( 'dt_activity_log_db_version' );
        delete_option( 'dt_reports_db_version' );
        delete_option( 'dt_reportmeta_db_version' );
    }



}
