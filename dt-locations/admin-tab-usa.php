<?php

/**
 * Disciple_Tools_Locations_Tab_USA
 *
 * @class   Disciple_Tools_Locations_Tab_USA
 * @version 0.1
 * @since   0.1
 * @package Disciple_Tools_Locations_Tab_USA
 * @author  Chasm.Solutions
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

class Disciple_Tools_Locations_Tab_USA {
    
    
    public function install_us_state() {
        
        /*  Step 1
         *  This section controls the dropdown selection of the states */
        $html = '<form method="post" name="state_step1" id="state_step1">';
        $html .= wp_nonce_field( 'state_nonce_validate', 'state_nonce', true, false );
        $html .= '<h1>(Step 1) Select a State to Install:</h1><br>';
        $html .= $this->get_usa_states_dropdown_not_installed();
        $html .= ' <button type="submit" class="button">Install State</button>';
        $html .= '<br><br>';
        $html .= '</form>'; // end form
    
        /*  Step 2
         *  This section lists the available administrative units for each of the installed states */
        $html .= '<form method="post" name="state_step2" id="state_step2">';
        $html .= wp_nonce_field( 'state_levels_nonce_validate', 'state_levels_nonce', true, false );
        $option = get_option( '_dt_usa_installed_state' ); // this installer relies heavily on this options table row to store status
        if($option) {
            $html .= '<h1>(Step 2) Add Levels to Installed States:</h1><br>';
            foreach ( $option as $state ) {
                $html .= '<hr><h2>' . $state['Zone_Name'] . '</h2>';
                $html .= '<p>Add levels: ';
                foreach( $state['levels'] as $key => $value ) {
                    $html .= '<button type="submit" name="' . $key . '" value="'.$state['WorldID'].'" ';
                    ($value == 1) ? $html .= 'disabled' : null; //check if already installed
                    $html .= '>' . $key . '</button> ';
                }
                $html .= '<span style="float:right"><button type="submit" name="delete" value="'.$state['WorldID'].'">delete all</button></span></p>';
            }
        }
        $html .= '</form>';
        
        return $html;
    }
    
    /**
     */
    public function process_install_us_state() {
        // if state install
        if ( !empty( $_POST[ 'state_nonce' ] ) && isset( $_POST[ 'state_nonce' ] ) && wp_verify_nonce( $_POST[ 'state_nonce' ], 'state_nonce_validate' ) ) {
    
            $selected_state = $_POST[ 'states-dropdown' ];
            
            // TODO download state info
            
            
            // TODO install state info
            
            
            // update option record for state
            $state[ 'WorldID' ] = $selected_state;

            $dir_contents = $this->get_usa_states();
            foreach( $dir_contents->RECORDS as $value ) {
                if($value->WorldID == $state[ 'WorldID' ]) {
                    $state[ 'Zone_Name' ] = $value->Zone_Name;
                    break;
                }
            }

            $state[ 'levels' ]  = [
                "county" => false,
                "tract"  => true,
            ];

            $installed_states = [];

            if ( get_option( '_dt_usa_installed_state' ) ) {
                // Installed State List
                $installed_states = get_option( '_dt_usa_installed_state' );
            }

            array_push( $installed_states, $state );
            asort( $installed_states );

            update_option( '_dt_usa_installed_state', $installed_states, false );
        }
        elseif ( !empty( $_POST[ 'state_levels_nonce' ] ) && isset( $_POST[ 'state_levels_nonce' ] ) && wp_verify_nonce( $_POST[ 'state_levels_nonce' ], 'state_levels_nonce_validate' )  ) {
            
            $keys = array_keys( $_POST );
            
            switch($keys[2]) {
                
                case 'county':
                    
                    $state_worldid = $_POST['county'];
                    
                    // TODO download county info
                    
                    // TODO install county info
                    
                    // update option record for county
                    $options = get_option( '_dt_usa_installed_state' );
                    
                    foreach($options as $key => $value) {
                        
                        if($value['WorldID'] == $state_worldid) {
                            $options[$key]['levels']['county'] = true;
                            $options[$key]['levels']['tract'] = false;
                            break;
                        }
                    }
                    update_option( '_dt_usa_installed_state', $options, false );
                    
                    break;
                    
                case 'tract':
    
                    $state_worldid = $_POST['tract'];
    
                    // TODO download tract info
    
                    // TODO install tract info
    
                    // update option record for county
                    $options = get_option( '_dt_usa_installed_state' );
    
                    foreach($options as $key => $value) {
        
                        if($value['WorldID'] == $state_worldid) {
                            $options[$key]['levels']['tract'] = true;
                            break;
                        }
                    }
                    update_option( '_dt_usa_installed_state', $options, false );
                    
                    break;
                    
                case 'delete':
    
                    $state_worldid = $_POST['delete'];
                    
                    // TODO sql delete
                    
                    // update option record
                    $options = get_option( '_dt_usa_installed_state' );
    
                    foreach($options as $key => $value) {
        
                        if($value['WorldID'] == $state_worldid) {
                            unset( $options[$key] );
                            break;
                        }
                    }
                    update_option( '_dt_usa_installed_state', $options, false );
                    
                    break;
                    
                default:
                    break;
            }
        }
    }
    
    /**
     * Creates a dropdown of the states with the state key as the value.
     * @usage USA locations
     *
     * @return string
     */
    public function get_usa_states_dropdown_not_installed() {
        
        $dir_contents = $this->get_usa_states();
        
        $dropdown = '<select name="states-dropdown">';
        $option = get_option( '_dt_usa_installed_state' );
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
     * Get the master json file with USA states and counties names, ids, and file locations.
     * @usage USA locations
     *
     * @return array|mixed|object
     */
    public function get_usa_states() {
        return json_decode( file_get_contents( plugin_dir_path( __FILE__ ) . 'json/usa-states.json' ) );
    }
}
