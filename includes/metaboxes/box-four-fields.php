<?php

/**
 * Disciple Tools
 *
 * @class Disciple_Tools_
 * @version	0.1
 * @since 0.1
 * @package	Disciple_Tools
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
        global $post, $wpdb;
        $html = '';

        $counts = $wpdb->get_results($wpdb->prepare('
                    SELECT meta_value, count(meta_value) as count 
                    FROM %1$s 
                      INNER JOIN %2$s ON %1$s.p2p_id = %2$s.p2p_id 
                    WHERE p2p_to = \'%3$d\' 
                    AND p2p_type = \'%4$s\' 
                    AND meta_key = \'%5$s\' 
                    GROUP BY meta_value;',
                    $wpdb->p2p,
                    $wpdb->p2pmeta,
                    $post->ID,
                    'contacts_to_groups',
                    'stage'
                    ), ARRAY_A);


        $stage = array();
        $stage['Unbelieving'] = 0;
        $stage['Believing'] = 0;
        $stage['Accountable'] = 0;
        $stage['Multiplying'] = 0;

        foreach ($counts as $count) {
            $stage[$count['meta_value']] = $count['count'];
        }

        $html .= '<table class="form-table"><tr><td>';

        $html .= '<h1>Unbelieving  : ' . $stage['Unbelieving'] . '<br>';
        $html .= 'Believing  : ' . $stage['Believing'] . '<br>';
        $html .= 'Accountable  : ' . $stage['Accountable'] . '<br>';
        $html .= 'Multiplying  : ' . $stage['Multiplying'] . '<br>';
        $html .= 'Is Church  : ' . get_post_meta($post->ID, 'type', true) . '<br></h1>';



        $html .= '</td><td>';
        $html .=  '<img src="'. Disciple_Tools()->plugin_img . '4fields.png" >';
        $html .= '</td></tr></table>';

        echo $html;
//        print'<pre>'; print_r($counts); print '</pre>';
    }


}