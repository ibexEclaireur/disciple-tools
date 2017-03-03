<?php

/**
 * @reference https://codex.wordpress.org/Plugin_API/Action_Reference/wp_ajax_(action)#Usage
 */
add_action( 'wp_ajax_list_group_users', 'prefix_ajax_list_group_users' );

function prefix_ajax_list_group_users( $group_id ) {
    // Handle request then generate response using WP_Ajax_Response
    $result = '';
    // Don't forget to stop execution afterward.
    if ( function_exists('disciple_tools_get_users_of_group') ) {
        $args = array(
            'taxonomy' => 'user-group',
            'term'     => $group_id,
            'term_by'  => 'slug'
        );
        $result = disciple_tools_get_users_of_group($args);

    }
    $result = json_encode($result);
    if ( isset($result)) { wp_die('yes' . '$result'); }
//    return $result;


}
