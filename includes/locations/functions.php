<?php
/**
 * Misc support functions
 */

/**
 * Creates a dropdown of the states with the state key as the value.
 * @return string
 */
function dt_get_states_key_dropdown_LL () {

    $dir_contents = json_decode(file_get_contents(plugin_dir_path(__DIR__) . '/json/data-file-directory.json')); // get directory & build dropdown

    $dropdown = '<select name="states-dropdown">';

    foreach ($dir_contents->USA_tracts as $value) {
        $dropdown .= '<option value="' . $value->key . '" ';
        if (isset($_POST['states-dropdown']) && $_POST['states-dropdown'] == $value->key) {$dropdown .= 'selected';}
        $dropdown .= '>' . $value->name;
        $dropdown .= '</option>';
    }
    $dropdown .= '</select>';

    return $dropdown;
}


/**
 * Returns directory in an array
 *
 * @usage           $directory = dt_get_data_file_directory_LL ();
                    print_r($directory->USA_tracts->{'08'}->name);
 *
 * @return array|mixed|object
 */
function dt_get_data_file_directory_LL () {
    return json_decode(file_get_contents(plugin_dir_path(__DIR__) . '/json/data-file-directory.json'));
}

/**
 * Returns the full file path for KML file using the state key
 * @param $key
 * @return string
 */
function dt_get_file_path_by_key_LL ($state) {
    $directory = dt_get_data_file_directory_LL (); // call directory
    return plugin_dir_path(__DIR__) . 'data/' . $directory->USA_tracts->{$state}->file; // build url
}

/**
 * Gets zoom size for chart
 * @param int   Number supplied from the AREALAND attribute of the census data. Based on this number we can calculate approximate zoom level.
 * @return int
 */
function dt_get_zoom_size_LL ($tract_size) {
    if($tract_size > 1000000000) {
        return 8;
    } elseif ($tract_size > 100000000) {
        return 10;
    } elseif ($tract_size > 50000000) {
        return 12;
    } elseif ($tract_size > 10000000) {
        return 13;
    } else {
        return 14;
    }
}
