<?php
/**
 * Initialization of the Post to Post library
 * This is the key configuration file for the post-to-post system in Disciple Tools.
 *
 * @see https://github.com/scribu/wp-posts-to-posts/wiki
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
                'stage' => array(
                    'title' => __( 'Stage', 'disciple_tools' ),
                    'type' => 'select',
                    'values' => array( __('Unknown', 'disciple_tools'), __('Unbelieving', 'disciple_tools'), __('Believing', 'disciple_tools'), __('Accountable', 'disciple_tools'), __('Multiplying', 'disciple_tools') ),
                    'default' => __('Unknown', 'disciple_tools'),
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

    p2p_register_connection_type( array(
        'name' => 'locations_to_locations',
        'from' => 'locations',
        'to' => 'locations',
        'reciprocal' => true,
        'title' => 'Nearby Locations',

    ) );

}
add_action( 'p2p_init', 'my_connection_types' );


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