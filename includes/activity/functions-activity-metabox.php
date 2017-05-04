<?php
/**
 * Creates the metaboxes and lists for contacts and groups activity.
 */


/**
 * Gets an array of activities for a contact record
 * @return array
 */
function dt_activity_list_for_id ($id, $order = 'DESC') {
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
 * @param $id
 */
function dt_activity_meta_box ($id)
{
    $list = dt_activity_list_for_id($id);

    $html = '<table class="widefat striped" width="100%">';
    $html .= '<tr><th>Name</th><th>Action</th><th>Note</th><th>Date</th></tr>';

    foreach ($list as $item) {
        $user = get_user_by('id', $item['user_id']);

        $html .= '</tr>';

        $html .= '<td>' . $user->display_name . '</td>';
        $html .= '<td>' . $item['action'] . '</td>';
        $html .= '<td>' . $item['object_note'] . '</td>';
        $html .= '<td>' . date('m/d/Y h:i:s', $item['hist_time']) . '</td>';

        $html .= '</tr>';
    }
    $html .= '</table>';
    echo $html;
}