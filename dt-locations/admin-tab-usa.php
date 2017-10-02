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

if( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class Disciple_Tools_Locations_Tab_USA
{
    /**
     * @return string
     */
    public function install_us_state()
    {

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
        if( $option ) {
            $html .= '<h1>(Step 2) Add Levels to Installed States:</h1><br>';
            foreach( $option as $state ) {
                $html .= '<hr><h2>' . $state[ 'Zone_Name' ] . '</h2>';
                $html .= '<p>Add levels: ';
                foreach( $state[ 'levels' ] as $key => $value ) {
                    $html .= '<button type="submit" name="' . $key . '" value="' . $state[ 'WorldID' ] . '" ';
                    ( $value == 1 ) ? $html .= 'disabled' : null; //check if already installed
                    $html .= '>' . $key . '</button> ';
                }
                $html .= '<span style="float:right"><button type="submit" name="delete" value="' . $state[ 'WorldID' ] . '">delete all</button></span></p>';
            }
        }
        $html .= '</form>';

        return $html;
    }

    /**
     * @return bool|\WP_Error
     */
    public function process_install_us_state()
    {
        // if state install
        if( !empty( $_POST[ 'state_nonce' ] ) && isset( $_POST[ 'state_nonce' ] ) && wp_verify_nonce( $_POST[ 'state_nonce' ], 'state_nonce_validate' ) ) {

            $selected_state = $_POST[ 'states-dropdown' ];

            // download country info
            $geojson = $this->get_state_level( $selected_state, 'state' );
            if( empty( $geojson ) ) {
                return new WP_Error( "geojson_error", 'Failed to retrieve geojson info from API', [ 'status' => 400 ] );
            }

            // install country info
            $result = Disciple_Tools_Locations_Import::insert_geojson( $geojson );
            if( !$result ) {
                return new WP_Error( "insert_error", 'insert_geojson returned a false value and likely failed to insert all records', [ 'status' => 400 ] );
            }

            // update option record for state
            $state[ 'WorldID' ] = $selected_state;

            $dir_contents = $this->get_usa_states();
            foreach( $dir_contents->RECORDS as $value ) {
                if( $value->WorldID == $state[ 'WorldID' ] ) {
                    $state[ 'Zone_Name' ] = $value->Zone_Name;
                    break;
                }
            }

            $state[ 'levels' ] = [
                "county" => false,
                "tract"  => true,
            ];

            $installed_states = [];

            if( get_option( '_dt_usa_installed_state' ) ) {
                // Installed State List
                $installed_states = get_option( '_dt_usa_installed_state' );
            }

            array_push( $installed_states, $state );
            asort( $installed_states );

            update_option( '_dt_usa_installed_state', $installed_states, false );

            return true;
        } elseif( !empty( $_POST[ 'state_levels_nonce' ] ) && isset( $_POST[ 'state_levels_nonce' ] ) && wp_verify_nonce( $_POST[ 'state_levels_nonce' ], 'state_levels_nonce_validate' ) ) {

            $keys = array_keys( $_POST );

            switch( $keys[ 2 ] ) {

                case 'county':

                    $state_worldid = $_POST[ 'county' ];

                    // download country info
                    $geojson = $this->get_state_level( $state_worldid, 'county' );
                    if( !$geojson ) {
                        return new WP_Error( "geojson_error", 'Failed to retrieve geojson info from API', [ 'status' => 400 ] );
                    }

                    // install country info
                    $result = Disciple_Tools_Locations_Import::insert_geojson( $geojson );
                    if( !$result ) {
                        return new WP_Error( "insert_error", 'insert_geojson returned a false value and likely failed to insert all records', [ 'status' => 400 ] );
                    }

                    // update option record for county
                    $options = get_option( '_dt_usa_installed_state' );

                    foreach( $options as $key => $value ) {

                        if( $value[ 'WorldID' ] == $state_worldid ) {
                            $options[ $key ][ 'levels' ][ 'county' ] = true;
                            $options[ $key ][ 'levels' ][ 'tract' ] = false;
                            break;
                        }
                    }
                    update_option( '_dt_usa_installed_state', $options, false );

                    return true;
                    break;

                case 'tract':

                    $state_worldid = $_POST[ 'tract' ];

                    // download country info
                    $geojson = $this->get_state_level( $state_worldid, 'tract' );
                    if( !$geojson ) {
                        return new WP_Error( "geojson_error", 'Failed to retrieve geojson info from API', [ 'status' => 400 ] );
                    }

                    // install country info
                    $result = Disciple_Tools_Locations_Import::insert_geojson( $geojson );
                    if( !$result ) {
                        return new WP_Error( "insert_error", 'insert_geojson returned a false value and likely failed to insert all records', [ 'status' => 400 ] );
                    }

                    // update option record for county
                    $options = get_option( '_dt_usa_installed_state' );

                    foreach( $options as $key => $value ) {

                        if( $value[ 'WorldID' ] == $state_worldid ) {
                            $options[ $key ][ 'levels' ][ 'tract' ] = true;
                            break;
                        }
                    }
                    update_option( '_dt_usa_installed_state', $options, false );

                    return true;
                    break;

                case 'delete':

                    $state_worldid = $_POST[ 'delete' ];

                    $result = Disciple_Tools_Locations_Import::delete_location_data( $state_worldid );
                    if( !$result ) {
                        return new WP_Error( "delete_error", 'delete queries failed', [ 'status' => 400 ] );
                    }

                    // update option record
                    $options = get_option( '_dt_usa_installed_state' );

                    foreach( $options as $key => $value ) {

                        if( $value[ 'WorldID' ] == $state_worldid ) {
                            unset( $options[ $key ] );
                            break;
                        }
                    }
                    update_option( '_dt_usa_installed_state', $options, false );

                    return true;
                    break;

                default:
                    break;
            }
        }

        return false;
    }

    /**
     * Creates a dropdown of the states with the state key as the value.
     *
     * @usage USA locations
     * @return string
     */
    public function get_usa_states_dropdown_not_installed()
    {

        $dir_contents = $this->get_usa_states();

        $dropdown = '<select name="states-dropdown">';
        $option = get_option( '_dt_usa_installed_state' );
        foreach( $dir_contents->RECORDS as $value ) {
            $disabled = '';
            $dropdown .= '<option value="' . $value->WorldID . '" ';
            if( $option != false ) {
                foreach( $option as $installed ) {

                    if( $installed[ 'WorldID' ] == $value->WorldID ) {
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
     *
     * @usage USA locations
     * @return array|mixed|object
     */
    public function get_usa_states()
    {
        return json_decode( file_get_contents( plugin_dir_path( __FILE__ ) . 'json/usa-states.json' ) );
    }

    /**
     * API query for getting country summary info
     *
     * @param $cnty_id
     *
     * @return array|mixed|object
     */
    public function get_state_summary( $cnty_id )
    {
        $option = get_option( '_dt_locations_import_config' );

        if( empty( $option[ 'mm_hosts' ][ $option[ 'selected_mm_hosts' ] ] ) ) {
            $option = Disciple_Tools_Location_Tools_Menu::get_config_option();
        }

        return json_decode( file_get_contents( $option[ 'mm_hosts' ][ $option[ 'selected_mm_hosts' ] ] . 'get_summary?cnty_id=' . $cnty_id ), true );
    }

    /**
     * API query for country by admin level
     *
     * @param $state_id
     * @param $level_number
     *
     * @return array|bool|mixed|object
     */
    public function get_state_level( $state_id, $level_number )
    {
        if( empty( $state_id ) ) {
            return false;
        }

        $option = get_option( '_dt_locations_import_config' );

        if( empty( $option[ 'mm_hosts' ][ $option[ 'selected_mm_hosts' ] ] ) ) {
            $option = Disciple_Tools_Location_Tools_Menu::get_config_option();
        }

        return json_decode( file_get_contents( $option[ 'mm_hosts' ][ $option[ 'selected_mm_hosts' ] ] . 'getstate?state_id=' . $state_id . '&level=' . $level_number ), true );
    }
}
