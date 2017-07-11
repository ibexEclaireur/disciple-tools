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
     * @return int/bool
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
            return false;

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

        // Get all report detals
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

        // Get all metadata values for the report
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

        // Add meta_input to the report array and return
        $results['meta_input'] = $meta_input;
        return $results;
    }

    /**
     * Get meta_value using $id and $key
     * @return  string
     */
    public function get_meta_value ($id, $key) {
        global $wpdb;

        // Get all metadata values for the report
        $meta_value = $wpdb->get_row(
            $wpdb->prepare(
                'SELECT meta_value FROM %1$s
					WHERE `report_id` = \'%2$s\'
					AND `meta_key` = \'%3$s\'
				;',
                $wpdb->reportmeta,
                $id,
                $key
            ),
            ARRAY_A
        );
        return $meta_value['meta_value'];
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
            'SELECT %1$s(meta_value) as %2$s
                FROM %3$s
                    RIGHT JOIN %4$s ON %3$s.id = %4$s.report_id
                WHERE %3$s.report_date LIKE \'%5$s\'
                    AND %3$s.report_source = \'%6$s\'
                    AND %4$s.meta_key = \'%2$s\'
                    ;',
            $type,
            $meta_key,
            $wpdb->reports,
            $wpdb->reportmeta,
            $wpdb->esc_like($date) . '%',
            $source


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

        // get the ids
        $results = $this->get_report_ids_by_date($date, $source, $subsource);

        // build full record by the id
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
     * @return  mixed
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

    public function get_last_value ($source, $meta_key, $subsource = '') {

        global $wpdb;
        $today = date('Y-m-d');


        if(empty($source) || empty($meta_key))
            return false;

        // check for recent date
        if(!empty($subsource)) {
            // loop date to find match with source and subsource

            // select meta value
            $count = 0;

        } else {
            // loop date to find all matches with source

            // select meta values and add
            $count = 0;
        }

        return $count;

    }

    public static function get_last_record_of_source ($source) {
        global $wpdb;
        if(empty($source))
            return false;

        $results = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT * FROM %1$s
					WHERE `report_source` = \'%2$s\'
					AND
					report_date = (select max(report_date) from %1$s where `report_source` = \'%2$s\')
				;',
                $wpdb->reports,
                $source
            )
        );

        if (sizeof($results) > 0){
            return $results[0];
        } else {
            return false;
        }
    }

    public static function get_last_record_of_source_and_subsource($source, $subsource){
        global $wpdb;
        if(empty($source))
            return false;

        $results = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT * FROM %1$s
					WHERE `report_source` = \'%2$s\'
					AND
					`report_subsource` = \'%3$s\'
					AND
					report_date = (select max(report_date) from %1$s where `report_source` = \'%2$s\')
				;',
                $wpdb->reports,
                $source,
                $subsource
            )
        );

        if (sizeof($results) > 0){
            return $results[0];
        } else {
            return false;
        }
    }

}