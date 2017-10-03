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
 * @return \Disciple_Tools_Metabox_Four_Fields
 */
function dt_four_fields_metabox()
{
    $object = new Disciple_Tools_Metabox_Four_Fields();

    return $object;
}

/**
 * Class Disciple_Tools_Metabox_Four_Fields
 */
class Disciple_Tools_Metabox_Four_Fields
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
     * @return void
     */
    public function content_display()
    {
        global $post;
        $html = '';

        $unknown = new WP_Query(
            [
                'connected_type'  => 'contacts_to_groups',
                'connected_items' => $post,
                'nopaging'        => true,
                'connected_meta'  => [ 'stage' => 'Unknown' ],
            ]
        );
        $unbelieving = new WP_Query(
            [
                'connected_type'  => 'contacts_to_groups',
                'connected_items' => $post,
                'nopaging'        => true,
                'connected_meta'  => [ 'stage' => 'Unbelieving' ],
            ]
        );
        $believing = new WP_Query(
            [
                'connected_type'  => 'contacts_to_groups',
                'connected_items' => $post,
                'nopaging'        => true,
                'connected_meta'  => [ 'stage' => 'Believing' ],
            ]
        );
        $accountable = new WP_Query(
            [
                'connected_type'  => 'contacts_to_groups',
                'connected_items' => $post,
                'nopaging'        => true,
                'connected_meta'  => [ 'stage' => 'Accountable' ],
            ]
        );
        $multiplying = new WP_Query(
            [
                'connected_type'  => 'contacts_to_groups',
                'connected_items' => $post,
                'nopaging'        => true,
                'connected_meta'  => [ 'stage' => 'Multiplying' ],
            ]
        );

        ?>
        <table class="form-table"><tr><td>

        <h1>Unknown  : <?php echo esc_html( $unknown->found_posts ); ?><br>
        Unbelieving  : <?php echo esc_html( $unbelieving->found_posts ); ?><br>
        Believing  : <?php echo esc_html( $believing->found_posts ); ?><br>
        Accountable  : <?php echo esc_html( $accountable->found_posts ); ?><br>
        Multiplying  : <?php echo esc_html( $multiplying->found_posts ); ?><br>
        Is Church  : <?php echo esc_html( get_post_meta( $post->ID, 'is_church', true ) ); ?><br></h1>

        </td><td>
        <?php //        $html .=  '<img src="'. Disciple_Tools()->plugin_img_url . '4fields.png" >'; ?>
        </td></tr></table>

        <?php
        //        print'<pre>'; print_r($multiplying); print '</pre>';
    }

}
