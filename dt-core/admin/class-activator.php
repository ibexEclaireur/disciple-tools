<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.1
 * @package    Disciple_Tools
 * @subpackage Disciple_Tools/includes/admin
 * @author
 */


class Disciple_Tools_Activator {


    /**
     * Activities to run during installation.
     *
     * Long Description.
     *
     * @since 0.1
     */
    public static function activate( $network_wide ) {
        global $wpdb;
        $Disciple_Tools = Disciple_Tools();

        /**
         * Log version number of Disciple_Tools
         */
        $Disciple_Tools->_log_version_number();

        /**
         * Create roles and capabilities
         */
        require_once( 'class-roles.php' );
        $roles = Disciple_Tools_Roles::instance();
        $roles->set_roles();


        /**
         * Set defaults for options page TODO: Need to rebuild a proper options page for DT plugin configurations.
         */
        //        $settings_sections = $Disciple_Tools->settings->get_settings_sections ( );
        //        foreach ($settings_sections as $key => $value) {
        //            $section = $Disciple_Tools->settings->get_settings_fields ( $key );
        //            $preset = array();
        //            foreach ($section as $field => $item) {
        //                if(!empty($item['default'])) {
        //                    $preset[$field] = $item['default'];
        //                }
        //            }
        //            add_option( $Disciple_Tools->token . '-' . $key, $preset, '', 'yes'  );
        //        }


        /**
         * Setup key for JWT authentication
         */
        if (!defined( 'JWT_AUTH_SECRET_KEY' ) ) {
            if (get_option( "my_jwt_key" )){
                define( 'JWT_AUTH_SECRET_KEY', get_option( "my_jwt_key" ) );
            } else {
                $iv = password_hash( random_bytes( 16 ), PASSWORD_DEFAULT );
                update_option( 'my_jwt_key', $iv );
                define( 'JWT_AUTH_SECRET_KEY', $iv );
            }
        }


        /**
         * Activate database creation for Disciple Tools Activity logs
         *
         * @since 0.1
         */
        if ( is_multisite() && $network_wide ) {
            // Get all blogs in the network and activate plugin on each one
            $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
            foreach ( $blog_ids as $blog_id ) {
                switch_to_blog( $blog_id );
                self::create_tables( $Disciple_Tools->version );
                restore_current_blog();
            }
        } else {
            self::create_tables( $Disciple_Tools->version );
        }
    }

    /**
     * Creating tables whenever a new blog is created
     *
     * @param $blog_id
     * @param $user_id
     * @param $domain
     * @param $path
     * @param $site_id
     * @param $meta
     */
    public static function on_create_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {

        if ( is_plugin_active_for_network( 'disciple-tools/disciple-tools.php' ) ) {
            switch_to_blog( $blog_id );
            self::create_tables( Disciple_Tools()->version );
            restore_current_blog();
        }
    }

    public static function on_delete_blog( $tables ) {
        global $wpdb;
        $tables[] = $wpdb->prefix . 'dt_activity_log';
        $tables[] = $wpdb->prefix . 'dt_reports';
        $tables[] = $wpdb->prefix . 'dt_reportmeta';
        return $tables;
    }

    /**
     * Creates the tables for the activity and report logs.
     *
     * @access protected
     */
    protected static function create_tables( $version ) {
        global $wpdb;

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        /* Activity Log */
        $table_name = $wpdb->prefix . 'dt_activity_log';
        if( $wpdb->get_var( "show tables like '{$table_name}'" ) != $table_name ) {
            $sql1 = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
					  `histid` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
					  `user_caps` varchar(70) NOT NULL DEFAULT 'guest',
					  `action` varchar(255) NOT NULL,
					  `object_type` varchar(255) NOT NULL,
					  `object_subtype` varchar(255) NOT NULL DEFAULT '',
					  `object_name` varchar(255) NOT NULL,
					  `object_id` int(11) NOT NULL DEFAULT '0',
					  `user_id` int(11) NOT NULL DEFAULT '0',
					  `hist_ip` varchar(55) NOT NULL DEFAULT '127.0.0.1',
					  `hist_time` int(11) NOT NULL DEFAULT '0',
					  `object_note` VARCHAR(255) NOT NULL DEFAULT '0',
					  `meta_id` BIGINT(20) NOT NULL DEFAULT '0',
					  `meta_key` VARCHAR(100) NOT NULL DEFAULT '0',
					  `meta_value` VARCHAR(255) NOT NULL DEFAULT '0',
					  `meta_parent` BIGINT(20) NOT NULL DEFAULT '0',
					  PRIMARY KEY (`histid`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";

            dbDelta( $sql1 );

            update_option( 'dt_activity_log_db_version', $version );
        }


        /* Report Log Table */
        $table_name = $wpdb->prefix . 'dt_reports';
        if( $wpdb->get_var( "show tables like '{$table_name}'" ) != $table_name ) {
            $sql2 = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
					  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
					  `report_date` DATE NOT NULL,
					  `report_source` VARCHAR(55) NOT NULL,
					  `report_subsource` VARCHAR(100) NOT NULL,
					  PRIMARY KEY (`id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
            dbDelta( $sql2 );
            update_option( 'dt_reports_db_version', $version );
        }


        /* Report Meta Log Table */
        $table_name = $wpdb->prefix . 'dt_reportmeta';
        if( $wpdb->get_var( "show tables like '{$table_name}'" ) != $table_name ) {
            $sql3 = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
					  `meta_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
					  `report_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
					  `meta_key` VARCHAR(255) NOT NULL,
					  `meta_value` LONGTEXT,
					  PRIMARY KEY (`meta_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
            dbDelta( $sql3 );
            update_option( 'dt_reportmeta_db_version', $version );
        }
    
        /* Report Meta Log Table */
        $table_name = $wpdb->prefix . 'dt_follow';
        if( $wpdb->get_var( "show tables like '{$table_name}'" ) != $table_name ) {
            $sql4 = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
					  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
					  `user_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
					  `contact_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
					  `meta` LONGTEXT,
					  PRIMARY KEY (`id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
            dbDelta( $sql4 );
            update_option( 'dt_follow_db_version', $version );
        }

    }

}
