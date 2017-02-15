<?php
/**
 * Initialization of the Post to Post library
 *
 * https://github.com/scribu/wp-lib-posts-to-posts
 * https://github.com/scribu/wp-posts-to-posts
 *
 *
 * TODO: Figure out the contact to user. Look at the solution here for adding metabox to user profile listing assigned contacts, https://github.com/scribu/wp-posts-to-posts/issues/261
 *
 */

function my_connection_types() {


    p2p_register_connection_type( array(
        'name' => 'contacts_to_contacts',
        'from' => 'contacts',
        'to' => 'contacts',
        //'admin_column' => 'any',  // TODO: This created an invalid string error.
        'title' => array(
            'from' => __( 'Coached by', 'drm' ),
            'to' => __( 'Coaching', 'drm' ),
        ),
        'from_labels' => array(
            'singular_name' => __( 'Contact', 'drm' ),
            'search_items' => __( 'Search contacts', 'drm' ),
            'not_found' => __( 'No contacts found.', 'drm' ),
            'create' => __( 'Connect Disciple ', 'drm' ),
        ),
        'to_labels' => array(
            'singular_name' => __( 'Contact', 'drm' ),
            'search_items' => __( 'Search contacts', 'drm' ),
            'not_found' => __( 'No contacts found.', 'drm' ),
            'create' => __( 'Connect Coach', 'drm' ),
        ),

    ) );

/*  // TODO: This section connects a contact to a user, and generates a connection column.
	// The better way to do this is to create a one-to-one connection between the contact and user.

	p2p_register_connection_type( array(
        'name' => 'users_to_contacts',
        'from' => 'contacts',
        'to' => 'user',
        'admin_dropdown' => 'any',
        'title' => array(
            'from' => __( 'Assigned Multiplier', 'drm' ),
            'to' => __( 'Assigned Contact', 'drm' ),
        ),
        'to_labels' => array(
            'singular_name' => __( 'Multiplier', 'drm' ),
            'search_items' => __( 'Search multipliers', 'drm' ),
            'not_found' => __( 'No multiplier found.', 'drm' ),
            'create' => __( 'Connect Multiplier ', 'drm' ),
        ),
        'from_labels' => array(
            'singular_name' => __( 'Contact', 'drm' ),
            'search_items' => __( 'Search contacts', 'drm' ),
            'not_found' => __( 'No contacts found.', 'drm' ),
            'create' => __( 'Connect Contact', 'drm' ),
        ),
    ) );*/

    p2p_register_connection_type(
        array(
            'name' => 'contacts_to_groups',
            'from' => 'contacts',
            'to' => 'groups',
            'admin_column' => 'any',
            'admin_dropdown' => 'from',
            'title' => array(
                'from' => __( 'Groups', 'drm' ),
                'to' => __( 'Members', 'drm' )
            ),
            'to_labels' => array(
                'singular_name' => __( 'Groups', 'drm' ),
                'search_items' => __( 'Search groups', 'drm' ),
                'not_found' => __( 'No groups found.', 'drm' ),
                'create' => __( 'Connect Group ', 'drm' ),
            ),
            'from_labels' => array(
                'singular_name' => __( 'Member', 'drm' ),
                'search_items' => __( 'Search members', 'drm' ),
                'not_found' => __( 'No members found.', 'drm' ),
                'create' => __( 'Connect Member', 'drm' ),
            ),
            'fields' => array(
                'role' => array(
                    'title' => __( 'Role', 'drm' ),
                    'type' => 'select',
                    'values' => array( 'Attending', 'Planting', 'Coaching' ),
                ),
            ),
        )
    );

    p2p_register_connection_type( array(
        'name' => 'groups_to_groups',
        'from' => 'groups',
        'to' => 'groups',
        'admin_column' => 'any',
        'title' => array(
            'from' => __( 'Planted by', 'drm' ),
            'to' => __( 'Planting', 'drm' ),
        ),
        'from_labels' => array(
            'singular_name' => __( 'Group', 'drm' ),
            'search_items' => __( 'Search groups', 'drm' ),
            'not_found' => __( 'No groups found.', 'drm' ),
            'create' => __( 'Connect Child Group', 'drm' ),
        ),
        'to_labels' => array(
            'singular_name' => __( 'Group', 'drm' ),
            'search_items' => __( 'Search groups', 'drm' ),
            'not_found' => __( 'No groups found.', 'drm' ),
            'create' => __( 'Connect Parent Group', 'drm' ),
        ),
    ) );

}
add_action( 'p2p_init', 'my_connection_types' );