<?php
/**
 * Template file for theme support
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.


/**
 * Prepares the keys of user connections for WP_Query
 * This function builds the array for the meta_query used in WP_Query to retrieve only records associated with
 * the user or the teams the user is connected to.
 *
 * Example return:
 * Array
 *   (
 *       [relation] => OR
[0] => Array
(
[key] => assigned_to
[value] => user-1
)

[1] => Array
(
[key] => assigned_to
[value] => group-1
)
)
 *
 * @return array
 */
function dt_get_user_associations () {

    // Set variables
    global $wpdb;
    $user_connections = array();

    // Set constructor
    $user_connections['relation'] = 'OR';

    // Get current user ID and build meta_key for current user
    $user_id = get_current_user_id();
    $user_key_value = 'user-' . $user_id;
    $user_connections[] = array('key' => 'assigned_to', 'value' => $user_key_value ) ;

    // Build arrays for current groups connected to user
    $sql = $wpdb->prepare(
        'SELECT %1$s.term_taxonomy_id 
          FROM %1$s
            WHERE object_id  = \'%2$d\'
            ',
        $wpdb->term_relationships,
        $user_id
    );
    $results = $wpdb->get_results( $sql, ARRAY_A );

    foreach ($results as $result) {
        $user_connections[] = array('key' => 'assigned_to', 'value' => 'group-' . $result['term_taxonomy_id']  );
    }

    // Return array to the meta_query
    return $user_connections;
}


/**
 * Gets team contacts for a specified user_id
 *
 * Example return:
 * Array
(
[relation] => OR
[0] => Array
(
[key] => assigned_to
[value] => user-1
)

[1] => Array
(
[key] => assigned_to
[value] => group-1
)
)
 * @return array
 */
function dt_get_team_contacts($user_id) {
    // get variables
    global $wpdb;
    $user_connections = array();
    $user_connections['relation'] = 'OR';
    $members = array();

    // First Query
    // Build arrays for current groups connected to user
    $sql = $wpdb->prepare(
        'SELECT DISTINCT %1$s.%3$s
          FROM %1$s
          INNER JOIN %2$s ON %1$s.%3$s=%2$s.%3$s
            WHERE object_id  = \'%4$d\'
            AND taxonomy = \'%5$s\'
            ',
        $wpdb->term_relationships,
        $wpdb->term_taxonomy,
        'term_taxonomy_id',
        $user_id,
        'user-group'
    );
    $results = $wpdb->get_results( $sql, ARRAY_A );


    // Loop
    foreach ($results as $result) {
        // create the meta query for the group
        $user_connections[] = array('key' => 'assigned_to', 'value' => 'group-' . $result['term_taxonomy_id']  );

        // Second Query
        // query a member list for this group
        $sql = $wpdb->prepare(
            'SELECT %1$s.object_id 
          FROM %1$s
            WHERE term_taxonomy_id  = \'%2$d\'
            ',
            $wpdb->term_relationships,
            $result['term_taxonomy_id']
        );

        // build list of member ids who are part of the team
        $results2 = $wpdb->get_results( $sql, ARRAY_A );

        // Inner Loop
        foreach ($results2 as $result2) {

            if($result2['object_id'] != $user_id) {
                $members[] = $result2['object_id'];
            }
        }
    }

    $members = array_unique($members);

    foreach($members as $member) {
        $user_connections[] = array('key' => 'assigned_to', 'value' => 'user-' . $member  );
    }

    // return
    return $user_connections;

}