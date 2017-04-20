<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Profile functions
 *
 * @author Chasm Solutions
 * @package Disciple_Tools
 */


/* Begin remove */
/**
 * Development function. Since roles are only placed at activation, if changes are made this function can be called
 * to refresh the roles found in the the class-roles.php
 * TODO: This function can be removed on release. It is used by the sample data plugin and the add/reset roles button.
 * @access public
 * @since 0.1
 */
function dt_reset_system_roles () {
    // Create roles and capabilities
    require_once( plugin_dir_path(__DIR__). '/admin/class-roles.php');
    $roles = Disciple_Tools_Roles::instance();
    $roles->set_roles();
}
/* End remove */






