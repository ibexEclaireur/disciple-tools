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
    Disciple_Tools()->report_api->insert($args);
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
     */
    public function insert( $args ) {
        global $wpdb;

        $args = wp_parse_args(
            $args,
            array(
                'report_date'       => date('Y-m-d'),
                'report_source'     => '',
                'report_subsource'  => '',
                'meta_input'        => array(),
            )
        );

        $args['report_date'] = date_create($args['report_date']); // Format submitted date
        $args['report_date'] = date_format($args['report_date'],"Y-m-d");

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

        if ( $check_duplicate )
            return;

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

    /**
     * Gets report ids by data
     * @param  $date string     This is the supplied date for the report date('Y-m-d') format
     * @param $source string    (optional) This argument limits the results to a certain source
     * @param $subsource string (optional) This argument further limits the results to a specific subsource of the source. Source is still required, in case of subsource naming conflicts.
     * @return array            Returns list of ids that match date and other arguments.
     */
    public function get_report_ids_by_date ($date, $source = null, $subsource = null) {
        global $wpdb;

        // check date for proper format
        $date = date_create($date);
        $date = date_format($date,"Y-m-d");

        if(!empty($subsource) && !empty($source)) {
            // Build full query
            $sql = $wpdb->prepare(
                'SELECT id FROM %1$s
					WHERE `report_date` = \'%2$s\'
						AND `report_source` = \'%3$s\'
						AND `report_subsource` = \'%4$s\'
				;',
                $wpdb->reports,
                $date,
                $source,
                $subsource
            );
        } elseif (!empty($source)) {
            // Build limited query
            $sql = $wpdb->prepare(
                'SELECT id FROM %1$s
					WHERE `report_date` = \'%2$s\'
						AND `report_source` = \'%3$s\'
				;',
                $wpdb->reports,
                $date,
                $source
            );
        } else {
            // Build date query
            $sql = $wpdb->prepare(
                'SELECT id FROM %1$s
					WHERE `report_date` = \'%2$s\'
				;',
                $wpdb->reports,
                $date
            );
        }

        // Query results
        $results = $wpdb->get_results( $sql , ARRAY_A);

        return $results;

    }

    public function get_reports_by_date ($date, $source = null, $subsource = null) {
        $report = array();
        $i = 0;
        $results = $this->get_report_ids_by_date($date, $source, $subsource);

        foreach ($results as $result) {
            $report[$i] = $this->get_report_by_id($result['id']);
            $i++;
        }
        return $report;
    }

}