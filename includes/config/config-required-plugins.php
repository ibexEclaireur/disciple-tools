<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Require plugins wit the TGM library.
 *
 * This defines the required and suggested plugins.
 *
 *
 *
 */


/**
 * Include the TGM_Plugin_Activation class. This class makes other plugins required for the DMM CRM system.
 * Refer to documentation here: https://github.com/TGMPA/TGM-Plugin-Activation
 *
 *
 */
require_once (DmmCrm_Plugin()->plugin_path . 'includes/plugins/class-tgm-plugin-activation.php');

/**
 * Register the required plugins for this theme.
 *
// Example of array options:
//
//        array(
//        'name'               => 'REST API Console', // The plugin name.
//        'slug'               => 'rest-api-console', // The plugin slug (typically the folder name).
//        'source'             => dirname( __FILE__ ) . '/lib/plugins/rest-api-console.zip', // The plugin source.
//        'required'           => true, // If false, the plugin is only 'recommended' instead of required.
//        'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
//        'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
//        'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
//        'external_url'       => '', // If set, overrides default API URL and points to an external URL.
//        'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
//        ),
//
 */
function dmmcrm_register_required_plugins() {
    /*
     * Array of plugin arrays. Required keys are name and slug.
     * If the source is NOT from the .org repo, then source is also required.
     */
    $plugins = array(

        array(
            'name'              => 'rest-api',
            'slug'              => 'rest-api',
            'required'          => true,
            'version'            => '2.0-beta15',
            'force_activation'  => true,
            'force_deactivation' => true,
            'is_callable'       => 'WP_REST_Controller',
        ),
//        array(
//            'name'               => 'REST API Console',
//            'slug'               => 'rest-api-console',
//            'required'           => true,
//            'version'            => '2.1',
//            'force_activation'   => true,
//            'force_deactivation' => true,
//            'is_callable'        => 'WP_REST_Console',
//        ),
//        array(
//            'name'               => 'Members',
//            'slug'               => 'members',
//            'required'           => true,
//            'version'            => '1.1.2',
//            'force_activation'   => true,
//            'force_deactivation' => true,
//            'is_callable'        => 'Members_Plugin',
//        ),
//        array(
//            'name'               => 'Simple Local Avatars',
//            'slug'               => 'simple-local-avatars',
//            'required'           => true,
//            'version'            => '2.0',
//            'force_activation'   => true,
//            'force_deactivation' => true,
//            'is_callable'        => 'Simple_Local_Avatars',
//        ),
//        array(
//            'name'               => 'WP oAuth Server',
//            'slug'               => 'oauth2-provider',
//            'required'           => true,
//            'version'            => '3.2',
//            'force_activation'   => true,
//            'force_deactivation' => true,
//            'is_callable'        => 'WO_Server',
//        ),
// Removed because it was giving an install error trying to pull from Github. Needs research.
//        array(
//            'name'               => 'DMM CRM Sample Data',
//            'slug'               => 'dmm-crm-sample-data',
//            'external_url'       => 'https://github.com/ChasmSolutions/dmm-crm-sample-data/archive/master.zip',
//            'is_callable'       =>  'dmmcrm_sample_data',
//        ),
    );

    /*
     * Array of configuration settings. Amend each line as needed.
     *
     * Only uncomment the strings in the config array if you want to customize the strings.
     */
    $config = array(
        'id'           => 'dmmcrm',                 // Unique ID for hashing notices for multiple instances of TGMPA.
        'default_path' => '/includes/plugins/',     // Default absolute path to bundled plugins.
        'menu'         => 'tgmpa-install-plugins', // Menu slug.
        'parent_slug'  => 'plugins.php',            // Parent menu slug.
        'capability'   => 'manage_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
        'has_notices'  => true,                    // Show admin notices or not.
        'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => 'For the DMM CRM system to work correction, these additional plugins must be installed.',                      // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => true,                   // Automatically activate plugins after installation or not.
        'message'      => '',                      // Message to output right before the plugins table.

        /*
        'strings'      => array(
            'page_title'                      => __( 'Install Required Plugins', 'dmmcrm' ),
            'menu_title'                      => __( 'Install Plugins', 'dmmcrm' ),
            /* translators: %s: plugin name. * /
            'installing'                      => __( 'Installing Plugin: %s', 'dmmcrm' ),
            /* translators: %s: plugin name. * /
            'updating'                        => __( 'Updating Plugin: %s', 'dmmcrm' ),
            'oops'                            => __( 'Something went wrong with the plugin API.', 'dmmcrm' ),
            'notice_can_install_required'     => _n_noop(
                /* translators: 1: plugin name(s). * /
                'This theme requires the following plugin: %1$s.',
                'This theme requires the following plugins: %1$s.',
                'dmmcrm'
            ),
            'notice_can_install_recommended'  => _n_noop(
                /* translators: 1: plugin name(s). * /
                'This theme recommends the following plugin: %1$s.',
                'This theme recommends the following plugins: %1$s.',
                'dmmcrm'
            ),
            'notice_ask_to_update'            => _n_noop(
                /* translators: 1: plugin name(s). * /
                'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.',
                'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.',
                'dmmcrm'
            ),
            'notice_ask_to_update_maybe'      => _n_noop(
                /* translators: 1: plugin name(s). * /
                'There is an update available for: %1$s.',
                'There are updates available for the following plugins: %1$s.',
                'dmmcrm'
            ),
            'notice_can_activate_required'    => _n_noop(
                /* translators: 1: plugin name(s). * /
                'The following required plugin is currently inactive: %1$s.',
                'The following required plugins are currently inactive: %1$s.',
                'dmmcrm'
            ),
            'notice_can_activate_recommended' => _n_noop(
                /* translators: 1: plugin name(s). * /
                'The following recommended plugin is currently inactive: %1$s.',
                'The following recommended plugins are currently inactive: %1$s.',
                'dmmcrm'
            ),
            'install_link'                    => _n_noop(
                'Begin installing plugin',
                'Begin installing plugins',
                'dmmcrm'
            ),
            'update_link' 					  => _n_noop(
                'Begin updating plugin',
                'Begin updating plugins',
                'dmmcrm'
            ),
            'activate_link'                   => _n_noop(
                'Begin activating plugin',
                'Begin activating plugins',
                'dmmcrm'
            ),
            'return'                          => __( 'Return to Required Plugins Installer', 'dmmcrm' ),
            'plugin_activated'                => __( 'Plugin activated successfully.', 'dmmcrm' ),
            'activated_successfully'          => __( 'The following plugin was activated successfully:', 'dmmcrm' ),
            /* translators: 1: plugin name. * /
            'plugin_already_active'           => __( 'No action taken. Plugin %1$s was already active.', 'dmmcrm' ),
            /* translators: 1: plugin name. * /
            'plugin_needs_higher_version'     => __( 'Plugin not activated. A higher version of %s is needed for this theme. Please update the plugin.', 'dmmcrm' ),
            /* translators: 1: dashboard link. * /
            'complete'                        => __( 'All plugins installed and activated successfully. %1$s', 'dmmcrm' ),
            'dismiss'                         => __( 'Dismiss this notice', 'dmmcrm' ),
            'notice_cannot_install_activate'  => __( 'There are one or more required or recommended plugins to install, update or activate.', 'dmmcrm' ),
            'contact_admin'                   => __( 'Please contact the administrator of this site for help.', 'dmmcrm' ),

            'nag_type'                        => '', // Determines admin notice type - can only be one of the typical WP notice classes, such as 'updated', 'update-nag', 'notice-warning', 'notice-info' or 'error'. Some of which may not work as expected in older WP versions.
        ),
        */
    );

    tgmpa( $plugins, $config );
}
add_action( 'tgmpa_register', 'dmmcrm_register_required_plugins' );