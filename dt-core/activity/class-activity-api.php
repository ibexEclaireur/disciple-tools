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
function dt_activity_insert( $args = array() ) {
    Disciple_Tools()->activity_api->insert($args);
}

/**
 * Disciple_Tools_Activity_Log_API
 * This handles the insert and other functions for the table _dt_activity_log
 *
 */
class Disciple_Tools_Activity_Log_API {


    /**
     * Get real address
     *
     * @since 2.1.4
     *
     * @return string real address IP
     */
    protected function _get_ip_address() {
        $server_ip_keys = array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        );

        foreach ( $server_ip_keys as $key ) {
            if ( isset( $_SERVER[ $key ] ) && filter_var( $_SERVER[ $key ], FILTER_VALIDATE_IP ) ) {
                return $_SERVER[ $key ];
            }
        }

        // Fallback local ip.
        return '127.0.0.1';
    }

    /**
     * @since 2.0.0
     * @return void
     */
    public function erase_all_items() {
        global $wpdb;

        $wpdb->query(
            $wpdb->prepare(
                'TRUNCATE %1$s',
                $wpdb->activity
            )
        );
    }

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
                'action'         => '',
                'object_type'    => '',
                'object_subtype' => '',
                'object_name'    => '',
                'object_id'      => '',
                'hist_ip'        => $this->_get_ip_address(),
                'hist_time'      => current_time( 'timestamp' ),
                'object_note'    => '',
                'meta_id'        => '',
                'meta_key'       => '',
                'meta_value'     => '',
                'meta_parent'     => '',
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
                'SELECT `histid` FROM %1$s
					WHERE `user_caps` = \'%2$s\'
						AND `action` = \'%3$s\'
						AND `object_type` = \'%4$s\'
						AND `object_subtype` = \'%5$s\'
						AND `object_name` = \'%6$s\'
						AND `user_id` = \'%7$s\'
						AND `hist_ip` = \'%8$s\'
						AND `hist_time` = \'%9$s\'
						AND `object_note` = \'%10$s\'
						AND `meta_id` = \'%11$s\'
						AND `meta_key` = \'%12$s\'
						AND `meta_value` = \'%13$s\'
						AND `meta_parent` = \'%13$s\'
				;',
                $wpdb->activity,
                $args['user_caps'],
                $args['action'],
                $args['object_type'],
                $args['object_subtype'],
                $args['object_name'],
                $args['user_id'],
                $args['hist_ip'],
                $args['hist_time'],
                $args['object_note'],
                $args['meta_id'],
                $args['meta_key'],
                $args['meta_value'],
                $args['meta_parent']
            )
        );

        if ( $check_duplicate )
            return;

        $wpdb->insert(
            $wpdb->activity,
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
                'object_note'    => $args['object_note'],
                'meta_id'        => $args['meta_id'],
                'meta_key'       => $args['meta_key'],
                'meta_value'       => $args['meta_value'],
                'meta_parent'       => $args['meta_parent'],
            ),
            array( '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%d', '%s', '%d', '%s', '%s', '%d' )
        );

        // Final action on insert.
        do_action( 'dt_insert_activity', $args );
    }
}


