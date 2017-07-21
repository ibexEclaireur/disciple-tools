<?php

/**
 * Disciple Tools
 *
 * @class Disciple_Tools_
 * @version    0.1
 * @since 0.1
 * @package    Disciple_Tools
 * @author Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function dt_four_fields_metabox () {
    $object = new Disciple_Tools_Metabox_Four_Fields();
    return $object;
}

class Disciple_Tools_Metabox_Four_Fields {

    /**
     * Constructor function.
     * @access  public
     * @since   0.1
     */
    public function __construct () {

    } // End __construct()

    /**
     * @see https://github.com/scribu/wp-posts-to-posts/wiki/Connection-metadata#querying-connections-by-their-fields
     * @return void
     */
    public function content_display () {
        global $post;
        $html = '';

        $unknown = new WP_Query( array(
            'connected_type' => 'contacts_to_groups',
            'connected_items' => $post,
            'nopaging' => true,
            'connected_meta' => array( 'stage' => 'Unknown' )
        ) );
        $unbelieving = new WP_Query( array(
            'connected_type' => 'contacts_to_groups',
            'connected_items' => $post,
            'nopaging' => true,
            'connected_meta' => array( 'stage' => 'Unbelieving' )
        ) );
        $believing = new WP_Query( array(
            'connected_type' => 'contacts_to_groups',
            'connected_items' => $post,
            'nopaging' => true,
            'connected_meta' => array( 'stage' => 'Believing' )
        ) );
        $accountable = new WP_Query( array(
            'connected_type' => 'contacts_to_groups',
            'connected_items' => $post,
            'nopaging' => true,
            'connected_meta' => array( 'stage' => 'Accountable' )
        ) );
        $multiplying = new WP_Query( array(
            'connected_type' => 'contacts_to_groups',
            'connected_items' => $post,
            'nopaging' => true,
            'connected_meta' => array( 'stage' => 'Multiplying' )
        ) );


        $html .= '<table class="form-table"><tr><td>';

        $html .= '<h1>Unknown  : ' . $unknown->found_posts . '<br>';
        $html .= 'Unbelieving  : ' . $unbelieving->found_posts . '<br>';
        $html .= 'Believing  : ' . $believing->found_posts . '<br>';
        $html .= 'Accountable  : ' . $accountable->found_posts . '<br>';
        $html .= 'Multiplying  : ' . $multiplying->found_posts . '<br>';
        $html .= 'Is Church  : ' . get_post_meta($post->ID, 'is_church', true) . '<br></h1>';



        $html .= '</td><td>';
        $html .=  '<img src="'. Disciple_Tools()->plugin_img . '4fields.png" >';
        $html .= '</td></tr></table>';

        echo $html;
//        print'<pre>'; print_r($multiplying); print '</pre>';
    }


}
