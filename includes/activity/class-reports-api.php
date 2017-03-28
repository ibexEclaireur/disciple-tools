<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * @since 1.0.0
 *
 * @see Disciple_Tools_Activity_Log_API::insert
 *
 * @param array $args
 * @return mixed
 */
function dt_report_insert( $args = array() ) {
    return Disciple_Tools()->report_api->insert($args);
}

/**
 * Disciple_Tools_Reports_API
 * This handles the insert and other functions for the table _dt_reports and _dt_reportmeta tables
 *
 */
class Disciple_Tools_Reports_API {

    /**
     * @since 0.1
     *
     * @param array $args
     * @return mixed
     */
    public function insert( $args ) {
        global $wpdb;

        $args = wp_parse_args(
            $args,
            array(
                'report_date'       => date('Y-m-d h:m:s'),
                'report_source'     => '',
                'report_subsource'  => '',
                'meta_input'        => array(),
            )
        );

        // Make sure for non duplicate.
        $check_duplicate = $wpdb->get_row(
            $wpdb->prepare(
                'SELECT `id` FROM %1$s
					WHERE `report_date` = \'%2$s\'
						AND `report_source` = \'%3$s\'
						AND `report_subsource` = \'%4$s\'
				;',
                $wpdb->reports,
                $args['report_date'],
                $args['report_source'],
                $args['report_subsource']
            )
        );

        if ( $check_duplicate ) {
            return false;
        }


        $wpdb->insert(
            $wpdb->reports,
            array(
                'report_date'       => $args['report_date'],
                'report_source'     => $args['report_source'],
                'report_subsource'  => $args['report_subsource'],
            ),
            array( '%s', '%s', '%s' )
        );

        $report_id = $wpdb->insert_id;

        if ( ! empty( $args['meta_input'] ) ) {
            foreach ( $args['meta_input'] as $field => $value ) {
                $this->add_report_meta ( $report_id, $field, $value );
            }
        }

        // Final action on insert.
        do_action( 'dt_insert_report', $args );

        return $report_id;
    }

    /**
     * Add Report Metadata
     * @since 0.1
     *
     * @param int $report_id
     * @param string $field
     * @param string $value
     * @return void
     */
    private function add_report_meta ($report_id, $field, $value) {
        global $wpdb;

        $wpdb->insert(
            $wpdb->reportmeta,
            array(
                'report_id'    => $report_id,
                'meta_key'     => $field,
                'meta_value'   => $value,
            ),
            array( '%d', '%s', '%s' )
        );
    }

    public function get_reports_by_source ($report_source) {
        global $wpdb;

        $results = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT * FROM %1$s
					WHERE `report_source` = \'%2$s\'
				;',
                $wpdb->reports,
                $report_source
            )
        );
        return $results;
    }

    public function get_report_by_id ($id) {
        global $wpdb;

        $results = $wpdb->get_row(
            $wpdb->prepare(
                'SELECT * FROM %1$s
					WHERE `id` = \'%2$s\'
				;',
                $wpdb->reports,
                $id
            ),
            ARRAY_A
        );
        $meta_input = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT * FROM %1$s
					WHERE `report_id` = \'%2$s\'
				;',
                $wpdb->reportmeta,
                $id
            ),
            ARRAY_A
        );
        $results['meta_input'] = $meta_input;
        return $results;
    }
}