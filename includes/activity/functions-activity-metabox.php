<?php
/**
 * Creates the metaboxes and lists for contacts and groups activity.
 */


/**
 * Gets an array of activities for a contact record
 * @return array
 */
function dt_activity_list_for_contact ($id, $order = 'DESC') {
    global $wpdb;

    // Query activity with the contact id
    $list = $wpdb->get_results(
        $wpdb->prepare(
            'SELECT %1$s FROM %2$s
					WHERE `object_id` = \'%3$s\'
					ORDER BY hist_time %4$s
				;',
            '*',
            $wpdb->activity,
            $id,
            $order
        ), ARRAY_A
    );

    // Return activity array from contact id
    return $list;
}

function hooks_p2p_created ($p2p_id) { // I need to create two records. One for each end of the connection.

    global $wpdb;

    $p2p_record = p2p_get_connection( $p2p_id ); // returns object
    // Query p2p Record
    $p2p_record = $wpdb->get_row(
        $wpdb->prepare(
            'SELECT * FROM %1$s
					WHERE `p2p_id` = \'%2$s\'
				;',
            $wpdb->p2p,
            $p2p_id
        ), ARRAY_A
    );

    $p2p_type = $p2p_record['p2p_type'];


    // Build variable sets
    $connections = array();

    $p2p_from = get_post($p2p_record['p2p_from'], ARRAY_A);
    $p2p_to = get_post($p2p_record['p2p_to'], ARRAY_A);

//    $connections['p2p_from'] = array(
//        'post_type'     => $p2p_from['post_type'],
//        'post_id'       => $p2p_from['ID'],
//        'post_title'    => $p2p_from['post_title'],
//        'p2p_opposite'  => $p2p_to['ID'],
//        'object_note'   => 'was connected to ' . $p2p_to['post_title'],
//    );
//    dt_activity_insert(
//        array(
//            'action'            => 'created',
//            'object_type'       => $p2p_from['post_type'],
//            'object_subtype'    => 'p2p',
//            'object_id'         => $p2p_from['ID'],
//            'object_name'       => $connection['post_title'],
//            'meta_id'           => $p2p_id,
//            'meta_key'          => $p2p_type,
//            'meta_value'        => $connection['post_opposite'], // i.e. the opposite record of the object in the p2p
//            'object_note'       => $connection['object_note'],
//        )
//    );

//    $connections['p2p_to'] = array(
//        'post_type'     => $p2p_to['post_type'],
//        'post_id'       => $p2p_to['ID'],
//        'post_title'    => $p2p_to['post_title'],
//        'p2p_opposite'  => $p2p_from['ID'],
//        'object_note'   => 'was connected to ' . $p2p_from['post_title'],
//    );
    dt_activity_insert(
        array(
            'action'            => 'created',
            'object_type'       => $p2p_to['post_type'],
            'object_subtype'    => 'p2p',
            'object_id'         => $p2p_to['ID'],
            'object_name'       => $p2p_to['post_title'],
            'meta_id'           => $p2p_id,
            'meta_key'          => $p2p_type,
            'meta_value'        => $p2p_from['ID'], // i.e. the opposite record of the object in the p2p
            'object_note'       => 'was connected to ' . $p2p_from['post_title'],
        )
    );
    wp_mail('chris@chasm.solutions', 'Testing email from website', 'This email is coming from the p2p action');




}
//https://github.com/scribu/wp-posts-to-posts/wiki/Actions-and-filters#p2p_created_connection
add_action( 'p2p_created_connection', 'hooks_p2p_created', 10, 1) ;
add_action( 'p2p_delete_connections', 'hooks_p2p_created', 10, 1) ;