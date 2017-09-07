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
     * Process the posts
     */
    public function process_install_country() {
        // if country install
        if ( !empty( $_POST[ 'country_nonce' ] ) && isset( $_POST[ 'country_nonce' ] ) && wp_verify_nonce( $_POST[ 'country_nonce' ], 'country_nonce_validate' ) ) {
            
            $selected_country = $_POST[ 'countries-dropdown' ];
            
            // TODO download country info
            
            
            
            // TODO install country info
            
            
            // update option record for country
            $country[ 'WorldID' ] = $selected_country;
            
            $dir_contents = $this->get_countries_json();
            foreach( $dir_contents['RECORDS'] as $value ) {
                if($value['WorldID'] == $country[ 'WorldID' ]) {
                    $country[ 'Zone_Name' ] = $value['Zone_Name'];
                    break;
                }
            }
    
            $country[ 'levels' ] = $this->get_country_summary( $selected_country );
            
            $installed_countries = [];
            
            if ( get_option( '_dt_installed_country' ) ) {
                // Installed State List
                $installed_countries = get_option( '_dt_installed_country' );
            }
            
            array_push( $installed_countries, $country );
            asort( $installed_countries );
            
            update_option( '_dt_installed_country', $installed_countries, false );
        }
        elseif ( !empty( $_POST[ 'country_levels_nonce' ] ) && isset( $_POST[ 'country_levels_nonce' ] ) && wp_verify_nonce( $_POST[ 'country_levels_nonce' ], 'country_levels_nonce_validate' )  ) {
            
            $keys = array_keys( $_POST );
            
            switch($keys[2]) {
                
                case 'adm1_count':
                    
                    $country_worldid = $_POST['adm1_count'];
                    
                    // TODO download county info
                    
                    // TODO install county info
                    
                    // update option record for county
                    $options = get_option( '_dt_installed_country' );
                    
                    foreach($options as $key => $value) {
                        if($value['WorldID'] === $country_worldid) {
                            $options[$key]['levels']['adm1_count'] = (string) 'installed';
                            update_option( '_dt_installed_country', $options, false );
                        }
                    }
                    
                    
                    break;
                
                case 'adm2_count':
                    
                    $country_worldid = $_POST['adm2_count'];
                    
                    // TODO download tract info
                    
                    // TODO install tract info
                    
                    // update option record for county
                    $options = get_option( '_dt_installed_country' );
    
                    foreach($options as $key => $value) {
                        if($value['WorldID'] === $country_worldid) {
                            $options[$key]['levels']['adm2_count'] = (string) 'installed';
                            update_option( '_dt_installed_country', $options, false );
                        }
                    }
                    
                    break;
    
                case 'adm3_count':
        
                    $country_worldid = $_POST['adm3_count'];
        
                    // TODO download tract info
        
                    // TODO install tract info
        
                    // update option record for county
                    $options = get_option( '_dt_installed_country' );
    
                    foreach($options as $key => $value) {
                        if($value['WorldID'] === $country_worldid) {
                            $options[$key]['levels']['adm3_count'] = (string) 'installed';
                            update_option( '_dt_installed_country', $options, false );
                        }
                    }
        
                    break;
    
                case 'adm4_count':
        
                    $country_worldid = $_POST['adm4_count'];
        
                    // TODO download tract info
        
                    // TODO install tract info
        
                    // update option record for county
                    $options = get_option( '_dt_installed_country' );
    
                    foreach($options as $key => $value) {
                        if($value['WorldID'] === $country_worldid) {
                            $options[$key]['levels']['adm4_count'] = (string) 'installed';
                            update_option( '_dt_installed_country', $options, false );
                        }
                    }
        
                    break;
                    
                case 'delete':
                    
                    $country_worldid = $_POST['delete'];
                    
                    // TODO sql delete
                    
                    // update option record
                    $options = get_option( '_dt_installed_country' );
                    
                    foreach($options as $key => $value) {
                        
                        if($value['WorldID'] == $country_worldid) {
                            unset( $options[$key] );
                            break;
                        }
                    }
                    update_option( '_dt_installed_country', $options, false );
                    
                    break;
                
                default:
                    break;
            }
        }
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
     * Get the master json file with USA countries and counties names, ids, and file locations.
     * @usage USA locations
     *
     * @return array|mixed|object
     */
    public function get_countries_json() {
        return json_decode( file_get_contents( plugin_dir_path( __FILE__ ) . 'json/countries.json' ), true );
    }
    
    public function get_country_summary( $cnty_id ) {
        $option = get_option( '_dt_locations_import_config' );
        
        if(empty( $option['mm_hosts'][$option['selected_mm_hosts']] )) {
            $option = Disciple_Tools_Location_Tools_Menu::get_config_option();
        }
        
        return json_decode( file_get_contents( $option['mm_hosts'][$option['selected_mm_hosts']] . 'get_summary?cnty_id=' . $cnty_id ), true );
    }
    
    public function  find_key_index( $arrays, $field, $value ) {
        foreach ( $arrays as $key => $array ) {
            if ( $array[ $field ] === $value ) {
                return $key;
            }
        }
    
        return false;
    }
}
