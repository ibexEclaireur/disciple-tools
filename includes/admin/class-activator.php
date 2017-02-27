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
	 * @since    0.1
	 */
	public static function activate() {

	    // Log version number of Disciple_Tools
	    Disciple_Tools::instance()->_log_version_number();

        // Create roles and capabilities
        require_once('../admin/class-roles.php');
        $roles = Disciple_Tools_Roles::instance();
        $roles->set_roles();
	}

}
