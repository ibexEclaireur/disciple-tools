<?php

/**
 * Disciple Tools
 *
 * @class   Disciple_Tools_
 * @version 0.1
 * @since   0.1
 * @package Disciple_Tools
 * @author  Chasm.Solutions & Kingdom.Training
 */

if( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly
/**
 * @return \Disciple_Tools_Metabox_Church_Fields
 */
function dt_church_fields_metabox()
{
    $object = new Disciple_Tools_Metabox_Church_Fields();

    return $object;
}

/**
 * Class Disciple_Tools_Metabox_Church_Fields
 */
class Disciple_Tools_Metabox_Church_Fields
{

    /**
     * Constructor function.
     *
     * @access public
     * @since  0.1
     */
    public function __construct()
    {

    } // End __construct()

    /**
     * @see https://github.com/scribu/wp-posts-to-posts/wiki/Connection-metadata#querying-connections-by-their-fields
     */
    public function content_display()
    {
        global $post;

        // Shows the church graphic
        //        if(get_post_meta( $post->ID, 'is_church', true ) == '1') {
        //            echo '<div class="center"><img src="' . Disciple_Tools()->plugin_img_url . 'church.png" style="text-align: center; margin: 0 auto;" ></div>';
        //        }

        // Prints javascript to hide dependent fields

    }

}
