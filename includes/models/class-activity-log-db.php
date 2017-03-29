<?php
/**
 * Disciple Tools Activity Log Database
 *
 * This class handles the creation and destruction of the report and activity tables for Disciple Tools.
 *
 * @since 0.1
 * @class Disciple_Tools_Activity_Log_DB
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Disciple_Tools_Activity_Log_DB {

    /**
     * Create table preprocessor
     * @access static public
     */
    public static function activate(  ) {
        // add activation logic here
        self::_create_tables();
    }

    /**
     * Delete table preprocessor
     * @access static public
     */
    public static function uninstall(  ) {
        if (get_option('delete_activity_db')) {
            self::_remove_tables();
        }
    }

    /**
     * Creates the tables for the activity and report logs.
     * @access protected
     */
    protected static function _create_tables() {
        global $wpdb;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        /* Activity Log */
        $sql1 = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}dt_activity_log` (
					  `histid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
					  `user_caps` varchar(70) NOT NULL DEFAULT 'guest',
					  `action` varchar(255) NOT NULL,
					  `object_type` varchar(255) NOT NULL,
					  `object_subtype` varchar(255) NOT NULL DEFAULT '',
					  `object_name` varchar(255) NOT NULL,
					  `object_id` int(11) NOT NULL DEFAULT '0',
					  `user_id` int(11) NOT NULL DEFAULT '0',
					  `hist_ip` varchar(55) NOT NULL DEFAULT '127.0.0.1',
					  `hist_time` int(11) NOT NULL DEFAULT '0',
					  PRIMARY KEY (`histid`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";


        /* Report Log Table */
        $sql2 = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}dt_reports` (
					  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
					  `report_date` DATE NOT NULL,
					  `report_source` VARCHAR(55) NOT NULL,
					  `report_subsource` VARCHAR(100) NOT NULL,
					  PRIMARY KEY (`id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";


        /* Report Meta Log Table */
        $sql3 = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}dt_reportmeta` (
					  `meta_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
					  `report_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
					  `meta_key` VARCHAR(255) NOT NULL,
					  `meta_value` LONGTEXT,
					  PRIMARY KEY (`meta_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";

        dbDelta( $sql1 );
        dbDelta( $sql2 );
        dbDelta( $sql3 );

        update_option( 'dt_activity_log_db_version', '1.0' );
        update_option( 'dt_reports_db_version', '1.0' );
        update_option( 'dt_reportmeta_db_version', '1.0' );

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
