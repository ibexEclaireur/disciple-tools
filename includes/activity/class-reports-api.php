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

    /***********************************************************/
    /*            Create Section                               */
    /***********************************************************/

    /**
     * Insert Report into _reports and _reportmeta tables
     * @since 0.1
     * @param array     $args
     * @param date      'report_date'
     * @param string    'report_source'
     * @param string    'report_subsource'
     * @param array     'meta_input' this is an array of meta_key and meta_value
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

    /***********************************************************/
    /*            Read Section                               */
    /***********************************************************/

    /**
     * Gets a single report including metadata by the report id
     *
     * @param   $id     int     (required) This is the report id.
     * @return  array
     */
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
     * Get sum total of a meta key for a date range
     *
     * @param   $date       string      (required)
     * @param   $source     string      (required)
     * @param   $meta_key   string      (required)
     * @param   $type       string      (optional) Takes sum, max, min, average. Defaults to sum.
     * @returns int
     */
    public function get_meta_key_total ($date, $source, $meta_key, $type = 'sum') {
        global $wpdb;

        // Build full query
        $sql = $wpdb->prepare(
            'SELECT %6$s(meta_value) as %5$s
                FROM %1$s
                    RIGHT JOIN %2$s ON %1$s.id = %2$s.report_id
                WHERE %1$s.report_date LIKE \'%3$s\'
                    AND %1$s.report_source = \'%4$s\'
                    AND %2$s.meta_key = \'%5$s\'
                    ;',
            $wpdb->reports,
            $wpdb->reportmeta,
            $wpdb->esc_like($date) . '%',
            $source,
            $meta_key,
            $type
        );

        // Query results
        $results = $wpdb->get_results( $sql , ARRAY_A);

        $results_int = $results[0][$meta_key];

        return (int) $results_int;

    }

    /**
     * Gets report ids by date
     *
     * @param  $date string     This is the supplied date for the report date('Y-m-d') format
     * @param $source string    (optional) This argument limits the results to a certain source
     * @param $subsource string (optional) This argument further limits the results to a specific subsource of the source. Source is still required, in case of subsource naming conflicts.
     * @return array            Returns list of ids that match date and other arguments.
     */
    public function get_report_ids_by_date ($date, $source = null, $subsource = null) {
        global $wpdb;

        if(!empty($subsource) && !empty($source)) {
            // Build full query
            $sql = $wpdb->prepare(
                'SELECT id FROM %1$s
					WHERE `report_date` LIKE \'%2$s\'
						AND `report_source` = \'%3$s\'
						AND `report_subsource` = \'%4$s\'
				;',
                $wpdb->reports,
                $wpdb->esc_like($date) . '%',
                $source,
                $subsource
            );
        } elseif (!empty($source)) {
            // Build limited query
            $sql = $wpdb->prepare(
                'SELECT id FROM %1$s
					WHERE `report_date` LIKE \'%2$s\'
						AND `report_source` = \'%3$s\'
				;',
                $wpdb->reports,
                $wpdb->esc_like($date) . '%',
                $source
            );
        } else {
            // Build date query
            $sql = $wpdb->prepare(
                'SELECT id FROM %1$s
					WHERE `report_date` LIKE \'%2$s\'
				;',
                $wpdb->reports,
                $wpdb->esc_like($date) . '%'
            );
        }

        // Query results
        $results = $wpdb->get_results( $sql , ARRAY_A);

        return $results;

    }

    /**
     * Gets full reports with metadata for a single date, and can be filtered by source and subsource
     *
     * @param   $date   string      (required) This is a date formated '2017-03-22'
     * @param   $source string      (optional) This is the source
     * @param   $subsource  string  (optional) If this is supplied, the source must also be supplied.
     * @return          array
     */
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

    /**
     * Get the reports for a year, month, and day ranges based on source and optional subsource
     *
     * @param   $date       string  (required)  The month is a formated year and month. 2017-03
     * @param   $source     string  (required)  The source
     * @param   $range      string  (required)  This is one of three ranges. year, month, or day
     * @param   $subsource  string  (optional)  The subsource
     * @param   $id_only    boolean (optional)  By default this is true and will return the ids records, but if set to true it will return only IDs of reports in this date range.
     * @return  array
     */
    public function get_month_by_source($date, $source, $subsource = '', $id_only = true ) {

        global $wpdb;
        $results = array();

        // check required fields
        if(empty($date) || empty($source) ) {
            $results['error'] = 'required fields error';
            return $results;
        }

        // prepare id or all setting
        if ($id_only) {
            $columns = 'id';
        } else {
            $columns = '*';
        }

        // prepare sql
        if(!empty($subsource)) {
            // Build full query
            $sql = $wpdb->prepare(
                'SELECT %1$s FROM %2$s
					WHERE `report_date` LIKE \'%3$s\'
						AND `report_source` = \'%4$s\'
						AND `report_subsource` = \'%5$s\'
				;',
                $columns,
                $wpdb->reports,
                $wpdb->esc_like($date) . '%',
                $source,
                $subsource
            );
        } else {
            // Build full query
            $sql = $wpdb->prepare(
                'SELECT %1$s FROM %2$s
					WHERE `report_date` LIKE \'%3$s\'
						AND `report_source` = \'%4$s\'
				;',
                $columns,
                $wpdb->reports,
                $wpdb->esc_like($date) . '%',
                $source
            );
        }

        // query results
        $results = $wpdb->get_results( $sql , ARRAY_A);

        return $results;
    }

    /**
     * Gets full reports with metadata for a single date, and can be filtered by source and subsource
     *
     * @param   $date   string      (required) This is a date formated '2017-03-22'
     * @param   $source string      (optional) This is the source
     * @param   $subsource  string  (optional) If this is supplied, the source must also be supplied.
     * @return          array
     */
    public function get_month_by_source_full ($date, $source, $subsource) {
        $report = array();
        $i = 0;
        $results = $this->get_month_by_source($date, $source, $subsource, true );

        foreach ($results as $result) {
            $report[$i] = $this->get_report_by_id($result['id']);
            $i++;
        }
        return $report;
    }

}