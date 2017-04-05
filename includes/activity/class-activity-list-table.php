<?php
// WP_List_Table is not loaded automatically so we need to load it in our application
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * Create a new table class that will extend the WP_List_Table
 */
class Disciple_Tools_Activity_List_Table extends WP_List_Table
{
    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $data = $this->get_activity_data();
        usort($data, array(&$this, 'sort_data'));

        $perPage = 15;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);

        $this->set_pagination_args(array(
            'total_items' => $totalItems,
            'per_page' => $perPage
        ));

        $data = array_slice($data, (($currentPage - 1) * $perPage), $perPage);

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns()
    {
        $columns = array(
            'date'        => __( 'Date', 'disciple-tools' ),
            'author'      => __( 'Author', 'disciple-tools' ),
            'ip'          => __( 'IP', 'disciple-tools' ),
            'type'        => __( 'Type', 'disciple-tools' ),
            'label'       => __( 'Label', 'disciple-tools' ),
            'action'      => __( 'Action', 'disciple-tools' ),
            'description' => __( 'Description', 'disciple-tools' ),
        );

        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns()
    {
        return array();
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns()
    {
        return array('type' => array('type', false), 'date' => array('date', false));
    }

    /**
     * Get the table data
     *
     * @return Array
     */
    private function get_activity_data()
    {
        global $wpdb;

        $data = array();

        // Get all report detals
        $results = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT * FROM %1$s
					ORDER BY %2$s
				;',
                $wpdb->activity,
                'hist_time desc'
            ),
            ARRAY_A
        );

        foreach ($results as $result) {
            $mapped_array = array(
                'date' => $result['hist_time'],
                'author' => $result['user_id'],
                'ip' => $result['hist_ip'],
                'type' => $result['object_type'],
                'label' => $result['object_subtype'],
                'action' => $result['action'],
                'description' => $result['object_name']
            );

            $data[] = $mapped_array;
        }

        return $data;
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'date':
            case 'author':
            case 'ip':
            case 'type':
            case 'label':
            case 'action':
            case 'description':
                return $item[$column_name];

            default:
                return print_r($item, true);
        }
    }

    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    private function sort_data($a, $b)
    {
        // Set defaults
        $orderby = 'date';
        $order = 'desc';

        // If orderby is set, use this as the sort column
        if (!empty($_GET['orderby'])) {
            $orderby = $_GET['orderby'];
        }

        // If order is set use this as the order
        if (!empty($_GET['order'])) {
            $order = $_GET['order'];
        }


        $result = strcmp($a[$orderby], $b[$orderby]);

        if ($order === 'asc') {
            return $result;
        }

        return -$result;
    }
}