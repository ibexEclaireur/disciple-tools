<?php
if( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/*
 * Sanitize image file name
 * https://wordpress.org/plugins/wp-hash-filename/
 */
function dt_make_filename_hash( $filename )
{
    $info = pathinfo( $filename );
    $ext = empty( $info[ 'extension' ] ) ? '' : '.' . $info[ 'extension' ];
    $name = basename( $filename, $ext );

    return md5( $name ) . $ext;
}
//add_filter( 'sanitize_file_name', 'dt_make_filename_hash', 10 );

/**
 * Add Categories to Attachments
 */
function dt_add_categories_to_attachments()
{
    register_taxonomy_for_object_type( 'category', 'attachment' );
}
add_action( 'init', 'dt_add_categories_to_attachments' );






