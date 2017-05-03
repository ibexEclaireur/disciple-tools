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

    p2p_register_connection_type( array(
        'name' => 'baptizer_to_baptized',
        'from' => 'contacts',
        'to' => 'contacts',
        'title' => array(
            'from' => __( 'Baptized by', 'disciple_tools' ),
            'to' => __( 'Baptized', 'disciple_tools' ),
        ),
        'from_labels' => array(
            'singular_name' => __( 'Contact', 'disciple_tools' ),
            'search_items' => __( 'Search contacts', 'disciple_tools' ),
            'not_found' => __( 'No contacts found.', 'disciple_tools' ),
            'create' => __( 'Add Baptism', 'disciple_tools' ),
        ),
        'to_labels' => array(
            'singular_name' => __( 'Contact', 'disciple_tools' ),
            'search_items' => __( 'Search contacts', 'disciple_tools' ),
            'not_found' => __( 'No contacts found.', 'disciple_tools' ),
            'create' => __( 'Add Baptizer', 'disciple_tools' ),
        ),
        'fields' => array(
            'month' => array(
                'title' => __( 'Month', 'disciple_tools' ),
                'type' => 'select',
                'values' => array( '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12' ),
                'default' => date('m')
            ),
            'day' => array(
                'title' => __( 'Day', 'disciple_tools' ),
                'type' => 'select',
                'values' => array( '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31'),
                'default' => date('d'),
            ),
            'year' => array(
                'title' => __( 'Year', 'disciple_tools' ),
                'type' => 'text',
                'default' => date('Y'),
            ),
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

    p2p_register_connection_type(
        array(
            'name' => 'contacts_to_locations',
            'from' => 'contacts',
            'to' => 'locations',
//            'cardinality' => 'many-to-one',
            'title' => array(
                'from' => __( 'Location', 'disciple_tools' ),
                'to' => __( 'Contacts', 'disciple_tools' )
            ),
            'to_labels' => array(
                'singular_name' => __( 'Locations', 'disciple_tools' ),
                'search_items' => __( 'Search locations', 'disciple_tools' ),
                'not_found' => __( 'No locations found.', 'disciple_tools' ),
                'create' => __( 'Connect Location', 'disciple_tools' ),
            ),
            'from_labels' => array(
                'singular_name' => __( 'Contacts', 'disciple_tools' ),
                'search_items' => __( 'Search contacts', 'disciple_tools' ),
                'not_found' => __( 'No contacts found.', 'disciple_tools' ),
                'create' => __( 'Create Contact', 'disciple_tools' ),
            ),
            'fields' => array(
                'primary' => array(
                    'title' => __( 'Primary', 'disciple_tools' ),
                    'type' => 'checkbox',
                ),
            ),
        )
    );


    p2p_register_connection_type(
        array(
            'name' => 'groups_to_locations',
            'from' => 'groups',
            'to' => 'locations',
            'cardinality' => 'many-to-one',
            'title' => array(
                'from' => __( 'Location', 'disciple_tools' ),
                'to' => __( 'Groups', 'disciple_tools' )
            ),
            'to_labels' => array(
                'singular_name' => __( 'Locations', 'disciple_tools' ),
                'search_items' => __( 'Search locations', 'disciple_tools' ),
                'not_found' => __( 'No locations found.', 'disciple_tools' ),
                'create' => __( 'Connect Location', 'disciple_tools' ),
            ),
            'from_labels' => array(
                'singular_name' => __( 'Groups', 'disciple_tools' ),
                'search_items' => __( 'Search groups', 'disciple_tools' ),
                'not_found' => __( 'No groups found.', 'disciple_tools' ),
                'create' => __( 'Create Group', 'disciple_tools' ),
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

    p2p_register_connection_type( array(
        'name' => 'assets_to_locations',
        'from' => 'assets',
        'to' => 'locations',
        'cardinality' => 'many-to-one',
        'title' => array(
            'from' => __( 'Location', 'disciple_tools' ),
            'to' => __( 'Asset', 'disciple_tools' ),
        ),
        'from_labels' => array(
            'singular_name' => __( 'Assets', 'disciple_tools' ),
            'search_items' => __( 'Search assets', 'disciple_tools' ),
            'not_found' => __( 'No assets found.', 'disciple_tools' ),
            'create' => __( 'Connect Assets', 'disciple_tools' ),
        ),
        'to_labels' => array(
            'singular_name' => __( 'Locations', 'disciple_tools' ),
            'search_items' => __( 'Search locations', 'disciple_tools' ),
            'not_found' => __( 'No locations found.', 'disciple_tools' ),
            'create' => __( 'Connect Location', 'disciple_tools' ),
        ),
    ) );

}
add_action( 'p2p_init', 'my_connection_types' );

function dt_years_dropdown () {

    $dates_array = array();
    $current_year = date('Y');
    $dates_array[] = $current_year;
    $years_count = 10;
    $i = 0;

    while($i < $years_count) {
        $dates_array[] = $current_year - 1;
    }


    return $dates_array;

}


/**
 * Sets the new connections to be published automatically.
 * @param $args
 * @return mixed
 */
function p2p_published_by_default( $args ) {
    $args['post_status'] = 'publish';

    return $args;
}
add_filter( 'p2p_new_post_args', 'p2p_published_by_default', 10, 1 );