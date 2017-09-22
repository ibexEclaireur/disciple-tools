<?php
/**
 * Presenter template for theme support
 *
 * @package  Disciple_Tools
 * @category Plugin
 * @author   Chasm.Solutions & Kingdom.Training
 * @since    0.1
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

/** Functions to output data for the theme. @see Buddypress bp-members-template.php or bp-groups-template.php for an example of the role of this page  */



/**
 * Prints the name of the Group or User
 * Used in the loop to get a friendly name of the 'assigned_to' field of the contact
 *
 * If $return is true, then return the name instead of printing it. (Similar to
 * the $return argument in var_export.)
 *
 * @param  int  $contact_id
 * @param  bool $return
 * @return void | string
 */
function dt_get_assigned_name ( int $contact_id, bool $return = false ) {

    $metadata = get_post_meta( $contact_id, $key = 'assigned_to', true );

    if(!empty( $metadata )) {
        $meta_array = explode( '-', $metadata ); // Separate the type and id
        $type = $meta_array[0];
        $id = $meta_array[1];

        if($type == 'user') {
            $value = get_user_by( 'id', $id );
            $rv = $value->display_name;
        } else {
            $value = get_term( $id );
            $rv = $value->name;
        }
        if ($return) {
            return $rv;
        } else {
            echo esc_html( $rv );
        }
    }

}

/**
 * Updates meta_data from form response
 */
function dt_update_required_update ( $post_data ) {

    global  $current_user; //for this example only :)

    $commentdata = [
        'comment_post_ID' => $post_data['post_ID'], // to which post the comment will show up
        'comment_content' => $post_data['comment_content'], //fixed value - can be dynamic
        'user_id' => $current_user->ID, //passing current user ID or any predefined as per the demand
    ];

    //Insert new comment and get the comment ID
    $comment_id = wp_new_comment( $commentdata );

    update_post_meta( $post_id = $post_data['post_ID'], $meta_key = 'requires_update', $meta_value = 'No' );

}

function dt_get_users_shared_with( $contact_id ) {
    return Disciple_Tools_Contacts::get_shared_with_on_contact( $contact_id );
}
