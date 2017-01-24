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
        'name' => 'coach_to_disciple',
        'from' => 'contacts',
        'to' => 'contacts',
        //'admin_column' => 'any',  // TODO: This created an invalid string error.
        'title' => array(
            'from' => __( 'Coached by', 'dmmcrm' ),
            'to' => __( 'Coaching', 'dmmcrm' ),
        ),
        'from_labels' => array(
            'singular_name' => __( 'Contact', 'dmmcrm' ),
            'search_items' => __( 'Search contacts', 'dmmcrm' ),
            'not_found' => __( 'No contacts found.', 'dmmcrm' ),
            'create' => __( 'Connect Disciple ', 'dmmcrm' ),
        ),
        'to_labels' => array(
            'singular_name' => __( 'Contact', 'dmmcrm' ),
            'search_items' => __( 'Search contacts', 'dmmcrm' ),
            'not_found' => __( 'No contacts found.', 'dmmcrm' ),
            'create' => __( 'Connect Coach', 'dmmcrm' ),
        ),

    ) );

    p2p_register_connection_type( array(
        'name' => 'users_to_contacts',
        'from' => 'contacts',
        'to' => 'user',
        'admin_dropdown' => 'any',
        'title' => array(
            'from' => __( 'Assigned Multiplier', 'dmmcrm' ),
            'to' => __( 'Assigned Contact', 'dmmcrm' ),
        ),
        'to_labels' => array(
            'singular_name' => __( 'Multiplier', 'dmmcrm' ),
            'search_items' => __( 'Search multipliers', 'dmmcrm' ),
            'not_found' => __( 'No multiplier found.', 'dmmcrm' ),
            'create' => __( 'Connect Multiplier ', 'dmmcrm' ),
        ),
        'from_labels' => array(
            'singular_name' => __( 'Contact', 'dmmcrm' ),
            'search_items' => __( 'Search contacts', 'dmmcrm' ),
            'not_found' => __( 'No contacts found.', 'dmmcrm' ),
            'create' => __( 'Connect Contact', 'dmmcrm' ),
        ),
    ) );

    p2p_register_connection_type(
        array(
            'name' => 'contacts_to_groups',
            'from' => 'contacts',
            'to' => 'groups',
            'admin_column' => 'any',
            'admin_dropdown' => 'from',
            'title' => array(
                'from' => __( 'Groups', 'dmmcrm' ),
                'to' => __( 'Members', 'dmmcrm' )
            ),
            'to_labels' => array(
                'singular_name' => __( 'Groups', 'dmmcrm' ),
                'search_items' => __( 'Search groups', 'dmmcrm' ),
                'not_found' => __( 'No groups found.', 'dmmcrm' ),
                'create' => __( 'Connect Group ', 'dmmcrm' ),
            ),
            'from_labels' => array(
                'singular_name' => __( 'Member', 'dmmcrm' ),
                'search_items' => __( 'Search members', 'dmmcrm' ),
                'not_found' => __( 'No members found.', 'dmmcrm' ),
                'create' => __( 'Connect Member', 'dmmcrm' ),
            ),
            'fields' => array(
                'role' => array(
                    'title' => __( 'Role', 'dmmcrm' ),
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
            'from' => __( 'Planted by', 'dmmcrm' ),
            'to' => __( 'Planting', 'dmmcrm' ),
        ),
        'from_labels' => array(
            'singular_name' => __( 'Group', 'dmmcrm' ),
            'search_items' => __( 'Search groups', 'dmmcrm' ),
            'not_found' => __( 'No groups found.', 'dmmcrm' ),
            'create' => __( 'Connect Child Group', 'dmmcrm' ),
        ),
        'to_labels' => array(
            'singular_name' => __( 'Group', 'dmmcrm' ),
            'search_items' => __( 'Search groups', 'dmmcrm' ),
            'not_found' => __( 'No groups found.', 'dmmcrm' ),
            'create' => __( 'Connect Parent Group', 'dmmcrm' ),
        ),
    ) );

}
add_action( 'p2p_init', 'my_connection_types' );