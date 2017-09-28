<?php
/**
 * Presenter template for theme support
 *
 * @package  Disciple_Tools
 * @category Plugin
 * @author   Chasm.Solutions & Kingdom.Training
 * @since    0.1
 */
if( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly.

/** Functions to output data for the theme. @see Buddypress bp-members-template.php or bp-groups-template.php for an example of the role of this page */

/**
 * Prepares the keys of user connections for WP_Query
 * This function builds the array for the meta_query used in WP_Query to retrieve only records associated with
 * the user or the teams the user is connected to.
 * Example return:
 * Array
 *   (
 *       [relation] => OR
 * [0] => Array
 * (
 * [key] => assigned_to
 * [value] => user-1
 * )
 * [1] => Array
 * (
 * [key] => assigned_to
 * [value] => group-1
 * )
 * )
 *
 * @return array
 */
function dt_get_user_associations()
{
    
    // Set variables
    global $wpdb;
    $user_connections = [];
    
    // Set constructor
    $user_connections[ 'relation' ] = 'OR';
    
    // Get current user ID and build meta_key for current user
    $user_id = get_current_user_id();
    $user_key_value = 'user-' . $user_id;
    $user_connections[] = [ 'key' => 'assigned_to', 'value' => $user_key_value ];
    
    // Build arrays for current groups connected to user
    $results = $wpdb->get_results( $wpdb->prepare( "SELECT
            `$wpdb->term_relationships`.`term_taxonomy_id`
        FROM
            `$wpdb->term_relationships`
        WHERE
            object_id = %d", $user_id ), ARRAY_A );
    
    foreach( $results as $result ) {
        $user_connections[] = [ 'key' => 'assigned_to', 'value' => 'group-' . $result[ 'term_taxonomy_id' ] ];
    }
    
    // Return array to the meta_query
    return $user_connections;
}

/**
 * Gets team contacts for a specified user_id
 * Example return:
 * Array
 * (
 * [relation] => OR
 * [0] => Array
 * (
 * [key] => assigned_to
 * [value] => user-1
 * )
 * [1] => Array
 * (
 * [key] => assigned_to
 * [value] => group-1
 * )
 * )
 *
 * @return array
 */
function dt_get_team_contacts( $user_id )
{
    // get variables
    global $wpdb;
    $user_connections = [];
    $user_connections[ 'relation' ] = 'OR';
    $members = [];
    
    // First Query
    // Build arrays for current groups connected to user
    $results = $wpdb->get_results( $wpdb->prepare( "SELECT
            DISTINCT `$wpdb->term_relationships`.`term_taxonomy_id`
        FROM
            `$wpdb->term_relationships`
        INNER JOIN
            `$wpdb->term_taxonomy`
        ON
            `$wpdb->term_relationships`.`term_taxonomy_id` = `$wpdb->term_taxonomy`.`term_taxonomy_id`
        WHERE
            object_id  = %d
            AND taxonomy = 'user-group'", $user_id ), ARRAY_A );
    
    // Loop
    foreach( $results as $result ) {
        // create the meta query for the group
        $user_connections[] = [ 'key' => 'assigned_to', 'value' => 'group-' . $result[ 'term_taxonomy_id' ] ];
        
        // Second Query
        // query a member list for this group
        // build list of member ids who are part of the team
        $results2 = $wpdb->get_results( $wpdb->prepare( "SELECT
                `$wpdb->term_relationships`.object_id
            FROM
                `$wpdb->term_relationships`
            WHERE
                term_taxonomy_id = %d", $result[ 'term_taxonomy_id' ] ), ARRAY_A );
        
        // Inner Loop
        foreach( $results2 as $result2 ) {
            
            if( $result2[ 'object_id' ] != $user_id ) {
                $members[] = $result2[ 'object_id' ];
            }
        }
    }
    
    $members = array_unique( $members );
    
    foreach( $members as $member ) {
        $user_connections[] = [ 'key' => 'assigned_to', 'value' => 'user-' . $member ];
    }
    
    // return
    return $user_connections;
    
}

/**
 * Get user notification options
 *
 * @param int|null $user_id
 *
 * @return array|WP_Error
 */
function dt_get_user_notification_options( int $user_id = null )
{
    if( is_null( $user_id ) ) {
        $user_id = get_current_user_id();
    }
    
    $check = dt_user_notification_options_check( $user_id );
    if( is_wp_error( $check ) ) {
        return $check;
    }
    
    return get_user_meta( $user_id, 'dt_notification_options', true );
}

/**
 * Check for existence of user notification options
 *
 * @param int $user_id
 *
 * @return bool|WP_Error
 */
function dt_user_notification_options_check( int $user_id ): bool {
    
    // check existence of options for user
    if( !get_user_meta( $user_id, 'dt_notification_options' ) ) {
        
        // if they don't exist create them
        $site_options = dt_get_option( 'dt_site_options' );
        $notifications_default = $site_options[ 'user_notifications' ];
        $result = add_user_meta( $user_id, 'dt_notification_options', $notifications_default, true );
        if( !$result ) {
            return new WP_Error('user_option_check_fail', 'Failed to create options for user_id. Check id.' ); // return false if fail to create options for user
        }
        
        return true; // return true, options now exist
    }
    
    return true; // return true, options exist
}

/**
 * Gets the current site defaults defined in the notifications config section in wp-admin
 *
 * @return array
 */
function dt_get_site_notification_defaults()
{
    $site_options = dt_get_option( 'dt_site_options' );
    
    return $site_options[ 'user_notifications' ];
}

/**
 * Echos user display name
 *
 * @param $user_id
 */
function dt_user_display_name( $user_id )
{
    echo esc_html( dt_get_user_display_name( $user_id ) );
}

/**
 * Returns user display name
 *
 * @param $user_id
 *
 * @return string
 */
function dt_get_user_display_name( $user_id )
{
    $user = get_userdata( $user_id );
    
    return $user->display_name;
}

/**
 * @param $profile_fields
 *
 * @return mixed
 */
function dt_modify_profile_fields( $profile_fields )
{
    
    $site_custom_lists = dt_get_option( 'dt_site_custom_lists' );
    if( is_wp_error($site_custom_lists ) ) {
        return $profile_fields;
    }
    $user_fields = $site_custom_lists[ 'user_fields' ];
    
    foreach( $user_fields as $field ) {
        if( $field[ 'enabled' ] ) {
            $profile_fields[ $field[ 'key' ] ] = $field[ 'label' ];
        }
    }
    
    return $profile_fields;
    
}

if( is_admin() ) {
    // Add elements to the contact section of the profile.
    add_filter( 'user_contactmethods', 'dt_modify_profile_fields' );
}

/**
 * Compares the user_metadata array with the site user fields and returns a combined array limited to site_user_fields.
 * This is used in the theme template to display the user profile.
 *
 * @param array $usermeta
 *
 * @return array
 */
function dt_build_user_fields_display( array $usermeta ): array
{
    $fields = [];
    
    $site_custom_lists = dt_get_option( 'dt_site_custom_lists' );
    if( is_wp_error($site_custom_lists ) ) {
        print $site_custom_lists->get_error_message();
    }
    $site_user_fields = $site_custom_lists[ 'user_fields' ];
    
    foreach( $site_user_fields as $key => $value ) {
        foreach( $usermeta as $k => $v ) {
            if( $key == $k ) {
                $fields[] = array_merge( $value, [ 'value' => $v[ 0 ] ] );
            }
        }
    }
    
    return $fields;
}

/**
 * @param int $user_id
 *
 * @return array|bool
 */
function dt_get_user_locations_list( int $user_id ) {
    global $wpdb;
    
    // get connected location ids to user
    $location_ids = $wpdb->get_col(
        $wpdb->prepare(
        "SELECT p2p_from as location_id FROM  $wpdb->p2p WHERE p2p_to = '%d' AND p2p_type = 'team_member_locations';", $user_id )
    );
    
    // check if null return
    if( empty( $location_ids ) ) {
        return false;
    }
    
    // get location posts from connected array
    $location_posts = new WP_Query( [ 'post__in' => $location_ids, 'post_type' => 'locations'] );
    
    return $location_posts->posts;
    
}

/**
 * Gets an array of teams populated with an array of members for each team
 * array(
 *      team_id
 *      team_name
 *      team_members array(
 *              ID
 *              display_name
 *              user_email
 *              user_url
 *
 * @param int $user_id
 *
 * @return array|bool
 */
function dt_get_user_team_members_list( int $user_id ): array {
    
    $team_members_list = [];
    
    $teams = wp_get_object_terms( $user_id, 'user-group' );
    if( empty( $teams ) ) {
        return false;
    }
    
    foreach( $teams as $team ) {
    
        $team_id = $team->term_id;
        $team_name = $team->name;
    
        $members_list = [];
        $args = [
            'taxonomy' => 'user-group',
            'term'     => $team_id,
            'term_by'  => 'id',
        ];
        $results = disciple_tools_get_users_of_group( $args );
        if( !empty( $results ) ) {
            foreach( $results as $result ) {
                if( !( $user_id == $result->data->ID ) ) {
                    $members_list[] = [
                        'ID'           => $result->data->ID,
                        'display_name' => $result->data->display_name,
                        'user_email'   => $result->data->user_email,
                        'user_url'     => $result->data->user_url,
                    ];
                }
            }
        }
        
        $team_members_list[] = [
            'team_id'      => $team_id,
            'team_name'    => $team_name,
            'team_members' => $members_list,
        ];
        
    }
    
    return $team_members_list;
}

