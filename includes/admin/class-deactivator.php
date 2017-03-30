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

        /**
         * Deactivate for Aryo Actity Log Plugin
         * found in /includes/plugins/aryo-activity-log
         * @since 0.1
         */
        require_once ( Disciple_Tools()->includes_path . 'plugins/aryo-activity-log/classes/class-aal-maintenance.php');
        AAL_Maintenance::uninstall(false);
        /* End Aryo Activity Log Plugin */


	}



}
