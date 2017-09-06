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
        // Step 1
        $html = '<form method="post" name="country_step1" id="country_step1">';
        $html .= wp_nonce_field( 'country_nonce_validate', 'country_nonce', true, false );
        $html .= '<h1>(Step 1) Select a Country to Install:</h1><br>';
        $html .= $this->get_country_dropdown_not_installed();
        $html .= ' <button type="submit" class="button">Install Country</button>';
        $html .= '<br><br>';
        $html .= '</form>'; // end form
        
        // Step 2
        $html .= '<form method="post" name="country_step2" id="country_step2">';
        $html .= wp_nonce_field( 'country_levels_nonce_validate', 'country_levels_nonce', true, false );
        $option = get_option( '_dt_installed_country' );
        if($option) {
            $html .= '<h1>(Step 2) Add Levels to Installed Countries:</h1><br>';
            foreach ( $option as $country ) {
                $html .= '<hr><h2>' . $country['Zone_Name'] . '</h2>';
                $html .= '<p>Add levels: ';
                foreach( $country['levels'] as $key => $value ) {
                    $html .= '<button type="submit" name="' . $key . '" value="'.$country['WorldID'].'" ';
                    ($value == 1) ? $html .= 'disabled' : null; //check if already installed
                    $html .= '>' . $key . '</button> ';
                }
                $html .= '<span style="float:right"><button type="submit" name="delete" value="'.$country['WorldID'].'">delete all of '.$country['Zone_Name'].'</button></span></p>';
            }
        }
        $html .= '</form>';
        
        return $html;
    }
    
    /**
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
            foreach( $dir_contents->RECORDS as $value ) {
                if($value->WorldID == $country[ 'WorldID' ]) {
                    $country[ 'Zone_Name' ] = $value->Zone_Name;
                    break;
                }
            }
            
            $country[ 'levels' ]  = [
                "county" => false,
                "tract"  => true,
            ];
            
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
                
                case 'county':
                    
                    $country_worldid = $_POST['county'];
                    
                    // TODO download county info
                    
                    // TODO install county info
                    
                    // update option record for county
                    $options = get_option( '_dt_installed_country' );
                    
                    foreach($options as $key => $value) {
                        
                        if($value['WorldID'] == $country_worldid) {
                            $options[$key]['levels']['county'] = true;
                            $options[$key]['levels']['tract'] = false;
                            break;
                        }
                    }
                    update_option( '_dt_installed_country', $options, false );
                    
                    break;
                
                case 'tract':
                    
                    $country_worldid = $_POST['tract'];
                    
                    // TODO download tract info
                    
                    // TODO install tract info
                    
                    // update option record for county
                    $options = get_option( '_dt_installed_country' );
                    
                    foreach($options as $key => $value) {
                        
                        if($value['WorldID'] == $country_worldid) {
                            $options[$key]['levels']['tract'] = true;
                            break;
                        }
                    }
                    update_option( '_dt_installed_country', $options, false );
                    
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
        foreach ($dir_contents->RECORDS as $value) {
            $disabled = '';
            $dropdown .= '<option value="' . $value->WorldID . '" ';
            if($option != false) {
                foreach($option as $installed) {
                    if( $installed['WorldID'] == $value->WorldID) {
                        $dropdown .= ' disabled';
                        $disabled = ' (Installed)';
                    }
                }
            }
            $dropdown .= '>' . $value->Zone_Name . $disabled;
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
        return json_decode( file_get_contents( plugin_dir_path( __FILE__ ) . 'json/countries.json' ) );
    }
}
