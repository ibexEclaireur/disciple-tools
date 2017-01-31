<?php

/*
 * Mapping meta capabilities.
 *
 *
 *
 *
 */

add_filter( 'map_meta_cap', 'my_map_meta_cap', 10, 4 );

function my_map_meta_cap( $caps, $cap, $user_id, $args ) {
//    global $post_type;
//    global $post;

/* If editing, deleting, or reading a movie, get the post and post type object. */
if ( 'edit_contact' == $cap || 'delete_contact' == $cap || 'read_contact' == $cap ) {
$post = get_post( $args[0] );
$post_type = get_post_type_object( $post->post_type );

/* Set an empty array for the caps. */
$caps = array();
}

/* If editing a movie, assign the required capability. */
if ( 'edit_contact' == $cap ) {
if ( $user_id == $post->post_author )
$caps[] = $post_type->cap->edit_posts;
else
$caps[] = $post_type->cap->edit_others_posts;
}

/* If deleting a movie, assign the required capability. */
elseif ( 'delete_contact' == $cap ) {
if ( $user_id == $post->post_author )
$caps[] = $post_type->cap->delete_posts;
else
$caps[] = $post_type->cap->delete_others_posts;
}

/* If reading a private movie, assign the required capability. */
elseif ( 'read_contact' == $cap ) {

if ( 'private' != $post->post_status )
$caps[] = 'read';
elseif ( $user_id == $post->post_author )
$caps[] = 'read';
else
$caps[] = $post_type->cap->read_private_posts;
}

/* Return the capabilities required by the user. */
return $caps;
}