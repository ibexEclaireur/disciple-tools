<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Sanitize Image Name
 *
 * This restricts the admin panel view of contacts, groups, and media to the those owned by the logged in user.
 *
 * @author Chasm Solutions
 * @package Disciple_Tools
 */

/*
 * Action and Filters
 */
    add_filter('sanitize_file_name', 'dt_make_filename_hash', 10);


/*
* Functions
*/

    /*
     * Sanitize image file name
     *
     * https://wordpress.org/plugins/wp-hash-filename/
     * */
    function dt_make_filename_hash($filename) {
        $info = pathinfo($filename);
        $ext  = empty($info['extension']) ? '' : '.' . $info['extension'];
        $name = basename($filename, $ext);
        return md5($name) . $ext;
    }
    /* End Sanitize file name */

/**
 * Add Categories to Attachments
 *
 */
    function dt_add_categories_to_attachments() {
        register_taxonomy_for_object_type( 'category', 'attachment' );
    }
    add_action( 'init' , 'dt_add_categories_to_attachments' );

// register new taxonomy which applies to attachments
function dt_add_location_taxonomy() {
    $labels = array(
        'name'              => 'Types',
        'singular_name'     => 'Type',
        'search_items'      => 'Search Type',
        'all_items'         => 'All Types',
        'parent_item'       => 'Parent Type',
        'parent_item_colon' => 'Parent Type:',
        'edit_item'         => 'Edit Type',
        'update_item'       => 'Update Type',
        'add_new_item'      => 'Add New Type',
        'new_item_name'     => 'New Type Name',
        'menu_name'         => 'Type',
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'query_var' => 'true',
        'rewrite' => 'true',
        'show_admin_column' => 'true',
    );

    register_taxonomy( 'type', 'attachment', $args );
}
//add_action( 'init', 'dt_add_location_taxonomy' );




/**
 * @see https://code.tutsplus.com/articles/creating-custom-fields-for-attachments-in-wordpress--net-13076
 *
 */

//function dt_attachement_featured_media_edit( $form_fields, $post ) {
//    $featured = (bool) get_post_meta($post->ID, 'featured_media', true);
//
//    $form_fields['featured_media'] = array(
//        'label' => 'Featured Media',
//        'input' => 'html',
//        'html' => '<input type="checkbox" id="featured_media" name="featured_media" value="1" ' .($featured ? ' checked="checked"' : '') . ' /> ',
//        'value' => $featured,
//        'show_in_edit'  => true,
//        'show_in_modal' => true
//    );
//
//
//
//    return $form_fields;
//}
//
//add_filter( 'attachment_fields_to_edit', 'dt_attachement_featured_media_edit', 10, 2 );
//
///**
// * @param $attachment_id
// */
//function dt_attachment_featured_meta_save( $post, $attachment ) {
//    if( isset($attachment['featured_media']) ){
//        // update_post_meta(postID, meta_key, meta_value);
//        update_post_meta($post['ID'], 'featured_media', $attachment['featured_media']);
//
//    }
//    return $post;
//}
//
//add_filter( 'attachment_fields_to_save', 'dt_attachment_featured_meta_save', 10, 2 );




