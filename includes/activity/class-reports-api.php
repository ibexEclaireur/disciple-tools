<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * @since 1.0.0
 *
 * @see Disciple_Tools_Activity_Log_API::insert
 *
 * @param array $args
 * @return void
 */
function dt_report_insert( $args = array() ) {
    Disciple_Tools()->report_api->insert($args);
}

/**
 * Disciple_Tools_Reports_API
 * This handles the insert and other functions for the table _dt_reports and _dt_reportmeta tables
 *
 */
class Disciple_Tools_Reports_API {

    /**
     * @since 1.0.0
     *
     * @param array $args
     * @return void
     */
    public function insert( $args ) {
        global $wpdb;

        $args = wp_parse_args(
            $args,
            array(
                'report_date'       => date('Y-m-d h:m:s'),
                'report_source'     => '',
                'report_subsource'  => '',
                'group'             => '',
                'meta_input'          => array(),
            )
        );

        $user = get_user_by( 'id', get_current_user_id() );
        if ( $user ) {
            $args['user_caps'] = strtolower( key( $user->caps ) );
            if ( empty( $args['user_id'] ) )
                $args['user_id'] = $user->ID;
        } else {
            $args['user_caps'] = 'guest';
            if ( empty( $args['user_id'] ) )
                $args['user_id'] = 0;
        }

        // Make sure for non duplicate.
        $check_duplicate = $wpdb->get_row(
            $wpdb->prepare(
                'SELECT `id` FROM %1$s
					WHERE `user_caps` = \'%2$s\'
						AND `action` = \'%3$s\'
						AND `object_type` = \'%4$s\'
						AND `object_subtype` = \'%5$s\'
						AND `object_name` = \'%6$s\'
						AND `user_id` = \'%7$s\'
						AND `hist_ip` = \'%8$s\'
						AND `hist_time` = \'%9$s\'
				;',
                $wpdb->activity,
                $args['user_caps'],
                $args['action'],
                $args['object_type'],
                $args['object_subtype'],
                $args['object_name'],
                $args['user_id'],
                $args['hist_ip'],
                $args['hist_time']
            )
        );

        if ( $check_duplicate )
            return;

        $wpdb->insert(
            $wpdb->reports,
            array(
                'action'         => $args['action'],
                'object_type'    => $args['object_type'],
                'object_subtype' => $args['object_subtype'],
                'object_name'    => $args['object_name'],
                'object_id'      => $args['object_id'],
                'user_id'        => $args['user_id'],
                'user_caps'      => $args['user_caps'],
                'hist_ip'        => $args['hist_ip'],
                'hist_time'      => $args['hist_time'],
            ),
            array( '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%d' )
        );

        if ( ! empty( $postarr['meta_input'] ) ) {
            foreach ( $postarr['meta_input'] as $field => $value ) {
//                update_post_meta( $post_ID, $field, $value );
            }
        }

        // Final action on insert.
        do_action( 'dt_insert_report', $args );
    }
}