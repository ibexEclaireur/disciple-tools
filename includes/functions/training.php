<?php
/**
 * This functions file is to support access to Disciple Tools Training Plugin with access to Disciple Tools
 * classes and functions.
 *
 * @see https://github.com/ChasmSolutions/disciple-tools-training
 * @since 0.1
 * @version 0.1
 */

/**
 * Supports the "reset roles" button
 */
function dt_training_reset_system_roles () {
    // Create roles and capabilities
    require_once( plugin_dir_path(__DIR__). '/admin/class-roles.php');
    $roles = Disciple_Tools_Roles::instance();
    $roles->set_roles();
}

function dt_training_get_channel_key($channel, $type) {
    require_once( plugin_dir_path(__DIR__). '/models/class-contact-post-type.php');
    $contacts = Disciple_Tools_Contact_Post_Type::instance();
    return $contacts->create_channel_metakey($channel, $type);
}