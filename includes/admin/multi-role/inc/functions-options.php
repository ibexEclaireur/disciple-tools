<?php
/**
 * Functions for handling plugin options.
 *
 * @package    Members
 * @subpackage Includes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2009 - 2016, Justin Tadlock
 * @link       http://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Conditional check to see if the role manager is enabled.
 *
 * @since  1.0.0
 * @access public
 * @return bool
 */
function dt_multi_role_role_manager_enabled() {
	return apply_filters( 'dt_multi_role_role_manager_enabled', dt_multi_role_get_setting( 'role_manager' ) );
}

/**
 * Conditional check to see if denied capabilities should overrule granted capabilities when
 * a user has multiple roles with conflicting cap definitions.
 *
 * @since  1.0.0
 * @access public
 * @return bool
 */
function dt_multi_role_explicitly_deny_caps() {
	return apply_filters( 'dt_multi_role_explicitly_deny_caps', dt_multi_role_get_setting( 'explicit_denied_caps' ) );
}

/**
 * Conditional check to see if the role manager is enabled.
 *
 * @since  1.0.0
 * @access public
 * @return bool
 */
function dt_multi_role_multiple_user_roles_enabled() {
	return apply_filters( 'dt_multi_role_multiple_roles_enabled', true );
}

/**
 * Conditional check to see if content permissions are enabled.
 *
 * @since  1.0.0
 * @access public
 * @return bool
 */
function dt_multi_role_content_permissions_enabled() {
	return apply_filters( 'dt_multi_role_content_permissions_enabled', dt_multi_role_get_setting( 'content_permissions' ) );
}

/**
 * Conditional check to see if login widget is enabled.
 *
 * @since  1.0.0
 * @access public
 * @return bool
 */
function dt_multi_role_login_widget_enabled() {
	return apply_filters( 'dt_multi_role_login_widget_enabled', dt_multi_role_get_setting( 'login_form_widget' ) );
}

/**
 * Conditional check to see if users widget is enabled.
 *
 * @since  1.0.0
 * @access public
 * @return bool
 */
function dt_multi_role_users_widget_enabled() {
	return apply_filters( 'dt_multi_role_users_widget_enabled', dt_multi_role_get_setting( 'users_widget' ) );
}

/**
 * Gets a setting from from the plugin settings in the database.
 *
 * @since  0.2.0
 * @access public
 * @return mixed
 */
function dt_multi_role_get_setting( $option = '' ) {

	$defaults = dt_multi_role_get_default_settings();

	$settings = wp_parse_args( get_option( 'dt_multi_role_settings', $defaults ), $defaults );

	return isset( $settings[ $option ] ) ? $settings[ $option ] : false;
}

/**
 * Returns an array of the default plugin settings.
 *
 * @since  0.2.0
 * @access public
 * @return array
 */
function dt_multi_role_get_default_settings() {

	return array(

		// @since 0.1.0
		'role_manager'        => 1,
		'content_permissions' => 1,
		'private_blog'        => 0,

		// @since 0.2.0
		'private_feed'              => 0,
		'login_form_widget'         => 0,
		'users_widget'              => 0,
		'content_permissions_error' => esc_html__( 'Sorry, but you do not have permission to view this content.', 'members' ),
		'private_feed_error'        => esc_html__( 'You must be logged into the site to view this content.',      'members' ),

		// @since 1.0.0
		'explicit_denied_caps' => true,
		'multi_roles'          => true,
	);
}
