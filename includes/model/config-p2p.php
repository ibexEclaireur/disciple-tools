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
            'from' => __( 'Coached by', 'disciple_tools' ),
            'to' => __( 'Coaching', 'disciple_tools' ),
        ),
        'from_labels' => array(
            'singular_name' => __( 'Contact', 'disciple_tools' ),
            'search_items' => __( 'Search contacts', 'disciple_tools' ),
            'not_found' => __( 'No contacts found.', 'disciple_tools' ),
            'create' => __( 'Connect Disciple ', 'disciple_tools' ),
        ),
        'to_labels' => array(
            'singular_name' => __( 'Contact', 'disciple_tools' ),
            'search_items' => __( 'Search contacts', 'disciple_tools' ),
            'not_found' => __( 'No contacts found.', 'disciple_tools' ),
            'create' => __( 'Connect Coach', 'disciple_tools' ),
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
            'from' => __( 'Assigned Multiplier', 'disciple_tools' ),
            'to' => __( 'Assigned Contact', 'disciple_tools' ),
        ),
        'to_labels' => array(
            'singular_name' => __( 'Multiplier', 'disciple_tools' ),
            'search_items' => __( 'Search multipliers', 'disciple_tools' ),
            'not_found' => __( 'No multiplier found.', 'disciple_tools' ),
            'create' => __( 'Connect Multiplier ', 'disciple_tools' ),
        ),
        'from_labels' => array(
            'singular_name' => __( 'Contact', 'disciple_tools' ),
            'search_items' => __( 'Search contacts', 'disciple_tools' ),
            'not_found' => __( 'No contacts found.', 'disciple_tools' ),
            'create' => __( 'Connect Contact', 'disciple_tools' ),
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
                'from' => __( 'Groups', 'disciple_tools' ),
                'to' => __( 'Members', 'disciple_tools' )
            ),
            'to_labels' => array(
                'singular_name' => __( 'Groups', 'disciple_tools' ),
                'search_items' => __( 'Search groups', 'disciple_tools' ),
                'not_found' => __( 'No groups found.', 'disciple_tools' ),
                'create' => __( 'Connect Group ', 'disciple_tools' ),
            ),
            'from_labels' => array(
                'singular_name' => __( 'Member', 'disciple_tools' ),
                'search_items' => __( 'Search members', 'disciple_tools' ),
                'not_found' => __( 'No members found.', 'disciple_tools' ),
                'create' => __( 'Connect Member', 'disciple_tools' ),
            ),
            'fields' => array(
                'role' => array(
                    'title' => __( 'Role', 'disciple_tools' ),
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
            'from' => __( 'Planted by', 'disciple_tools' ),
            'to' => __( 'Planting', 'disciple_tools' ),
        ),
        'from_labels' => array(
            'singular_name' => __( 'Group', 'disciple_tools' ),
            'search_items' => __( 'Search groups', 'disciple_tools' ),
            'not_found' => __( 'No groups found.', 'disciple_tools' ),
            'create' => __( 'Connect Child Group', 'disciple_tools' ),
        ),
        'to_labels' => array(
            'singular_name' => __( 'Group', 'disciple_tools' ),
            'search_items' => __( 'Search groups', 'disciple_tools' ),
            'not_found' => __( 'No groups found.', 'disciple_tools' ),
            'create' => __( 'Connect Parent Group', 'disciple_tools' ),
        ),
    ) );

}
add_action( 'p2p_init', 'my_connection_types' );