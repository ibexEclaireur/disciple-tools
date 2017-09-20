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

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly


function dt_activity_metabox () {
    $object = new Disciple_Tools_Metabox_Activity();
    return $object;
}

class Disciple_Tools_Metabox_Activity {

    /**
     * Constructor function.
     *
     * @access public
     * @since  0.1
     */
    public function __construct () {

    } // End __construct()


    /**
     * Gets an array of activities for a contact record
     *
     * @return array
     */
    public function activity_list_for_id ( $id, $order = 'DESC' ) {
        global $wpdb;

        // Query activity with the contact id
        $list = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT %1$s FROM %2$s
					WHERE `object_id` = \'%3$s\'
					ORDER BY hist_time %4$s
				;',
                '*',
                $wpdb->activity,
                $id,
                $order
            ), ARRAY_A
        );

        // Return activity array from contact id
        return $list;
    }

    /**
     * Echos the list contents of the activity metabox
     *
     * @param $id
     */
    public function activity_meta_box ( $id )
    {
        $list = $this->activity_list_for_id( $id );

        $html = '<table class="widefat striped" width="100%">';
        $html .= '<tr><th>Name</th><th>Action</th><th>Note</th><th>Date</th></tr>';

        foreach ($list as $item) {
            $user = get_user_by( 'id', $item['user_id'] );

            $html .= '</tr>';

            $html .= '<td>' . $user->display_name . '</td>';
            $html .= '<td>' . strip_tags( $item['action'] ) . '</td>';
            $html .= '<td>' . strip_tags( $item['object_note'] ) . '</td>';
            $html .= '<td>' . date( 'm/d/Y h:i:s', $item['hist_time'] ) . '</td>';

            $html .= '</tr>';
        }
        $html .= '</table>';
        echo $html;
    }

}
