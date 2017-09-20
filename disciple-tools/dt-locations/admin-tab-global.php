<?php

/**
 * Disciple_Tools_Locations_Tab_Global
 *
 * @class   Disciple_Tools_Locations_Tab_Global
 * @version 0.1
 * @since   0.1
 * @package Disciple_Tools_Locations_Tab_Global
 * @author  Chasm.Solutions
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

class Disciple_Tools_Locations_Tab_Global {
    
    /**
     * Presentation function for the content of the global country install tab
     * @return string
     */
    public function install_country() {
        
        /*  Step 1
         *  This section controls the dropdown selection of the countries */
        $html = '<form method="post" name="country_step1" id="country_step1">';
        $html .= wp_nonce_field( 'country_nonce_validate', 'country_nonce', true, false );
        $html .= '<h1>(Step 1) Select a Country to Install:</h1><br>';
        $html .= $this->get_country_dropdown_not_installed();
        $html .= ' <button type="submit" class="button">Install Country</button>';
        $html .= '<br><br>';
        $html .= '</form>'; // end form
        
        /*  Step 2
         *  This section lists the available administrative units for each of the installed countries */
        $html .= '<form method="post" name="country_step2" id="country_step2">';
        $html .= wp_nonce_field( 'country_levels_nonce_validate', 'country_levels_nonce', true, false );
        $option = get_option( '_dt_installed_country' ); // this installer relies heavily on this options table row to store status
        if(!empty( $option )) { // if options are empty hide section
            $html .= '<h1>(Step 2) Add Admin Levels to Installed Countries:</h1><br>';
            foreach ( $option as $country ) {
                $html .= '<hr><h2>' . $country['Zone_Name'] . '</h2>';
                $html .= '<p>Add Levels: ';
                $i = 0; // increment makes sure that only the highest level of install is available at a time. This controls the order of install.
                if(!empty( $country['levels'] )) { // if level is empty array, hide section
                    foreach( $country['levels'] as $key => $value ) {
                            $label = '';
                        switch($key) {
                            case 'adm1_count': $label = 'Admin1';
                                break;
                            case 'adm2_count': $label = 'Admin2';
                                break;
                            case 'adm3_count': $label = 'Admin3';
                                break;
                            case 'adm4_count': $label = 'Admin4';
                                break;
                        }
                        
                        if($value > 0 || $value == 'installed') { // hide admin areas with zero value, but still show admin areas that have been installed
                            $html .= '<button type="submit" name="' . $key . '" value="'.$country['WorldID'].'" ';
                            ($i > 0 || $value == 'installed') ? $html .= 'disabled' : $i++; //check if already installed or needs to be installed first
                            $html .= '>'. $label . ' (' .$value. ')</button> ';
                        }
                    }
                }
                $html .= '<span style="float:right"><button type="submit" name="delete" value="'.$country['WorldID'].'">delete all</button></span></p>';
            }
        }
        $html .= '</form>';
        
        return $html;
    }
    
    /**
     * Process the post information for country tab
     */
    public function process_install_country() {
        
        // if country install
        if ( !empty( $_POST[ 'country_nonce' ] ) && isset( $_POST[ 'country_nonce' ] ) && wp_verify_nonce( $_POST[ 'country_nonce' ], 'country_nonce_validate' ) ) {
    
            $cnty_id = $_POST[ 'countries-dropdown' ];
    
            // download country info
            $geojson = $this->get_country_level( $cnty_id, '0' );
            if(empty( $geojson )) {
                return new WP_Error( "geojson_error", 'Failed to retrieve geojson info from API', ['status' => 400] );
            }
    
            // install country info
            $result = Disciple_Tools_Locations_Import::insert_geojson( $geojson );
            if (!$result){
                return new WP_Error( "insert_error", 'insert_geojson returned a false value and likely failed to insert all records', ['status' => 400] );
            }
            
            /*  Build and Add options information on install
             *  This section established the country, calls the api for summary info on the country, and builds the option record */
            $country[ 'WorldID' ] = $cnty_id;
            $dir_contents = $this->get_countries_json(); // gets stored list of countries
            foreach( $dir_contents['RECORDS'] as $value ) { // filters for the country name
                if($value['WorldID'] == $country[ 'WorldID' ]) {
                    $country[ 'Zone_Name' ] = $value['Zone_Name'];
                    break;
                }
            }
            $country[ 'levels' ] = $this->get_country_summary( $cnty_id ); // retrieves the summary info from the hosted movement mapping api
            $installed_countries = [];
            if ( get_option( '_dt_installed_country' ) ) {
                // Installed State List
                $installed_countries = get_option( '_dt_installed_country' );
            }
            array_push( $installed_countries, $country );
            asort( $installed_countries );
            update_option( '_dt_installed_country', $installed_countries, false );
            
            return true;
        }
        elseif ( !empty( $_POST[ 'country_levels_nonce' ] ) && isset( $_POST[ 'country_levels_nonce' ] ) && wp_verify_nonce( $_POST[ 'country_levels_nonce' ], 'country_levels_nonce_validate' )  ) {
            
            $keys = array_keys( $_POST );
            
            switch($keys[2]) {
                
                case 'adm1_count':
                    
                    $cnty_id = $_POST['adm1_count'];
                    
                    // download country info
                    $geojson = $this->get_country_level( $cnty_id, '1' );
                    if(empty( $geojson )) {
                        return new WP_Error( "geojson_error", 'Failed to retrieve geojson info from API', ['status' => 400] );
                        break;
                    }
                    
                    // install country info
                    $result = Disciple_Tools_Locations_Import::insert_geojson( $geojson );
                    if (!$result){
                        return new WP_Error( "insert_error", 'insert_geojson returned a false value and likely failed to insert all records', ['status' => 400] );
                        break;
                    }
                    
                    // update option record for county
                    $options = get_option( '_dt_installed_country' );
                    foreach($options as $key => $value) {
                        if($value['WorldID'] === $cnty_id) {
                            $options[$key]['levels']['adm1_count'] = (string) 'installed';
                            update_option( '_dt_installed_country', $options, false );
                        }
                    }
                    
                    return true;
                    break;
                
                case 'adm2_count':
                    
                    $cnty_id = $_POST['adm2_count'];
    
                    // download country info
                    $geojson = $this->get_country_level( $cnty_id, '2' );
                    if(empty( $geojson )) {
                        return new WP_Error( "geojson_error", 'Failed to retrieve geojson info from API', ['status' => 400] );
                    }
    
                    // install country info
                    $result = Disciple_Tools_Locations_Import::insert_geojson( $geojson );
                    if (!$result){
                        return new WP_Error( "insert_error", 'insert_geojson returned a false value and likely failed to insert all records', ['status' => 400] );
                    }
                    
                    // update option record for county
                    $options = get_option( '_dt_installed_country' );
    
                    foreach($options as $key => $value) {
                        if($value['WorldID'] === $cnty_id) {
                            $options[$key]['levels']['adm2_count'] = (string) 'installed';
                            update_option( '_dt_installed_country', $options, false );
                        }
                    }
    
                    return true;
                    break;
    
                case 'adm3_count':
        
                    $cnty_id = $_POST['adm3_count'];
    
                    // download country info
                    $geojson = $this->get_country_level( $cnty_id, '3' );
                    if(empty( $geojson )) {
                        return new WP_Error( "geojson_error", 'Failed to retrieve geojson info from API', ['status' => 400] );
                    }
    
                    // install country info
                    $result = Disciple_Tools_Locations_Import::insert_geojson( $geojson );
                    if (!$result){
                        return new WP_Error( "insert_error", 'insert_geojson returned a false value and likely failed to insert all records', ['status' => 400] );
                    }
        
                    // update option record for county
                    $options = get_option( '_dt_installed_country' );
    
                    foreach($options as $key => $value) {
                        if($value['WorldID'] === $cnty_id) {
                            $options[$key]['levels']['adm3_count'] = (string) 'installed';
                            update_option( '_dt_installed_country', $options, false );
                        }
                    }
    
                    return true;
                    break;
    
                case 'adm4_count':
        
                    $cnty_id = $_POST['adm4_count'];
    
                    // download country info
                    $geojson = $this->get_country_level( $cnty_id, '4' );
                    if(empty( $geojson )) {
                        return new WP_Error( "geojson_error", 'Failed to retrieve geojson info from API', ['status' => 400] );
                    }
    
                    // install country info
                    $result = Disciple_Tools_Locations_Import::insert_geojson( $geojson );
                    if (!$result){
                        return new WP_Error( "insert_error", 'insert_geojson returned a false value and likely failed to insert all records', ['status' => 400] );
                    }
        
                    // update option record for county
                    $options = get_option( '_dt_installed_country' );
    
                    foreach($options as $key => $value) {
                        if($value['WorldID'] === $cnty_id) {
                            $options[$key]['levels']['adm4_count'] = (string) 'installed';
                            update_option( '_dt_installed_country', $options, false );
                        }
                    }
    
                    return true;
                    break;
                    
                case 'delete':
                    
                    $cnty_id = $_POST['delete'];
    
                    Disciple_Tools_Locations_Tab_Global::delete_location_data( $cnty_id );
                    
                    // update option record
                    $options = get_option( '_dt_installed_country' );
                    
                    foreach($options as $key => $value) {
                        
                        if($value['WorldID'] == $cnty_id) {
                            unset( $options[$key] );
                            break;
                        }
                    }
                    update_option( '_dt_installed_country', $options, false );
                    
                    return true;
                    break;
                
                default:
                    break;
            }
        }
    }
    
    public static function delete_location_data ( $cnty_id ) {
        global $wpdb;
        
        $results1 = $wpdb->query( "DELETE from $wpdb->posts WHERE post_type = 'locations' AND post_name LIKE '$cnty_id%';" );
        $results2 = $wpdb->query( "DELETE FROM $wpdb->postmeta WHERE NOT EXISTS (SELECT NULL FROM $wpdb->posts WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id);" );
        
        return ($results1 || $results2) ? true : false;
    }
    
    /**
     * Creates a dropdown of the countries with the country key as the value.
     * @usage USA locations
     *
     * @return string
     */
    public function get_country_dropdown_not_installed() {
        
        $dir_contents = $this->get_countries_json();
        
        $dropdown = '<select name="countries-dropdown">';
        $option = get_option( '_dt_installed_country' );
        foreach ($dir_contents['RECORDS'] as $value) {
            $disabled = '';
            $dropdown .= '<option value="' . $value['WorldID'] . '" ';
            if($option != false) {
                foreach($option as $installed) {
                    if( $installed['WorldID'] == $value['WorldID']) {
                        $dropdown .= ' disabled';
                        $disabled = ' (Installed)';
                    }
                }
            }
            $dropdown .= '>' . $value['Zone_Name'] . $disabled;
            $dropdown .= '</option>';
        }
        $dropdown .= '</select>';
        
        return $dropdown;
    }
    
    /**
     * Get the master local json file with USA countries and counties names, ids, and file locations.
     * @usage USA locations
     *
     * @return array|mixed|object
     */
    public function get_countries_json() {
        return json_decode( file_get_contents( plugin_dir_path( __FILE__ ) . 'json/countries.json' ), true );
    }
    
    /**
     * API query for getting country summary info
     * @param $cnty_id
     *
     * @return array|mixed|object
     */
    public function get_country_summary( $cnty_id ) {
        $option = get_option( '_dt_locations_import_config' );
        
        if(empty( $option['mm_hosts'][$option['selected_mm_hosts']] )) {
            $option = Disciple_Tools_Location_Tools_Menu::get_config_option();
        }
        
        return json_decode( file_get_contents( $option['mm_hosts'][$option['selected_mm_hosts']] . 'get_summary?cnty_id=' . $cnty_id ), true );
    }
    
    /**
     * API query for country by admin level
     * @param $cnty_id
     *
     * @return array|mixed|object
     */
    public function get_country_level( $cnty_id, $level_number ) {
        $option = get_option( '_dt_locations_import_config' );
        
        if(empty( $option['mm_hosts'][$option['selected_mm_hosts']] )) {
            $option = Disciple_Tools_Location_Tools_Menu::get_config_option();
        }
        
        return json_decode( file_get_contents( $option['mm_hosts'][$option['selected_mm_hosts']] . 'getcountrybylevel?cnty_id='.$cnty_id.'&level=' . $level_number ), true );
    }
    
    /**
     * Utility to find the index of an array
     * @param $arrays
     * @param $field
     * @param $value
     *
     * @return bool|int|string
     */
    public function find_key_index( $arrays, $field, $value ) {
        foreach ( $arrays as $key => $array ) {
            if ( $array[ $field ] === $value ) {
                return $key;
            }
        }
    
        return false;
    }
    
    
}
