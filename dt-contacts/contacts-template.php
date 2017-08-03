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
 *
 * @return mixed
 */
function dt_get_contact_edit_form () {

    if(class_exists( 'Disciple_Tools' )) {

        // Create the title field
        $html = '<input type="hidden" name="dt_contacts_noonce" id="dt_contacts_noonce" value="' . wp_create_nonce( 'update_dt_contacts' ) . '" />';
        $html .= '<input name="post_title" type="text" id="post_title" class="regular-text" value="'. get_the_title() .'" />' ;
        echo $html;


        // Call the metadata fields
        $contact = Disciple_Tools_Contact_Post_Type::instance();

        echo $contact->meta_box_content( 'all' );


    } // end if class exists

}

/**
 * Save contact
 */
function dt_save_contact( $post ) {
    if(class_exists( 'Disciple_Tools' )) {

        if($post['post_title'] != get_the_title()) {
            $my_post = [
                'ID'           => get_the_ID(),
                'post_title'   => $post['post_title'],
            ];
            wp_update_post( $my_post );
        }

        $contact = Disciple_Tools_Contact_Post_Type::instance();
        $contact->meta_box_save( get_the_ID() );

        wp_redirect( get_permalink() );
    }
}

/**
 * Get Number of Contacts for a Location
 *
 * @return int
 */
function dt_get_contacts_at_location( $post_id, $user_id ) {
    return 0; //TODO
}

/**
 * Get an array of records that require an update
 */
function dt_get_requires_update ( $user_id ) {
    $assigned_to = 'user-' . $user_id;

    // Search for records assigned to user and have the meta_key requires_update and meta_value Yes
    // Build arrays for current groups connected to user
    $meta_query_args = [
        'relation' => 'AND', // Optional, defaults to "AND"
        [
            'key'     => 'assigned_to',
            'value'   => $assigned_to,
            'compare' => '='
        ],
        [
            'key'     => 'requires_update',
            'value'   => 'Yes',
            'compare' => '='
        ]
    ];

    $query = new WP_Query( $meta_query_args );

    return $query;
}


/**
 * Updates meta_data from form response
 */
//@todo move to contacts class
function dt_update_overall_status ( $post ) {

    if ($post['response'] == '1') {

        update_post_meta( $post_id = $post['post_id'], $meta_key = 'overall_status', $meta_value = '1' );

    } elseif ($post['response'] == 'decline') {

        update_post_meta( $post_id = $post['post_id'], $meta_key = 'assigned_to', $meta_value = 'Dispatch' );
        update_post_meta( $post_id = $post['post_id'], $meta_key = 'overall_status', $meta_value = '0' );

    }

}

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
            echo $rv;
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
