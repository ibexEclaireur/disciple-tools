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
        require_once('../admin/class-roles.php');
        $roles = Disciple_Tools_Roles::instance();
        $roles->reset_roles();
	}

}
