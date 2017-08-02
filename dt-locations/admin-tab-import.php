<?php

/**
 * Disciple_Tools_Tabs
 *
 * @class   Disciple_Tools_Tabs
 * @version 0.1
 * @since   0.1
 * @package Disciple_Tools_Tabs
 * @author  Chasm.Solutions
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

class Disciple_Tools_Locations_Tab_Import
{
    
    /**
     * Page content for the tab
     */
    public function page_contents()
    {
        
        $html = '';
        
        $html .= '<div class="wrap"><h2>Import</h2>'; // Block title
        
        $html .= '<div class="wrap"><div id="poststuff"><div id="post-body" class="metabox-holder columns-2">';
        $html .= '<div id="post-body-content">';
        $html .= $this->select_us_census_data_dropdown() . '<br>';
        $html .= $this->select_oz_data_dropdown() . '<br>';
        
        $html .= '</div><!-- end post-body-content --><div id="postbox-container-1" class="postbox-container">';
        $html .= '<br>'; /* Add content to column */
        
        $html .= '</div><!-- postbox-container 1 --><div id="postbox-container-2" class="postbox-container">';
        $html .= '';/* Add content to column */
        
        $html .= '</div><!-- postbox-container 2 --></div><!-- post-body meta box container --></div><!--poststuff end --></div><!-- wrap end -->';
        
        return $html;
        
    }
    
    
    /**
     * Creates drop down for uploading state xml files
     *
     * @return mixed
     */
    public function select_us_census_data_dropdown()
    {
        $html = '';
        $result = '';
        $result2 = '';
        
        // check if $_POST to change option
        if ( !empty( $_POST[ 'state_nonce' ] ) && isset( $_POST[ 'state_nonce' ] ) && wp_verify_nonce( $_POST[ 'state_nonce' ], 'state_nonce_validate' ) ) {
            
            if ( !isset( $_POST[ 'states-dropdown' ] ) ) { // check if file is correctly set
                return false;
            }
            
            $result = Disciple_Tools_Upload::upload_census_tract_kml_to_post_type( $_POST[ 'states-dropdown' ] ); // run insert process TODO make this a javascript call with a spinner.
            $result2 = Disciple_Tools_Upload::upload_us_state_tracts( $_POST[ 'states-dropdown' ] );
            
        } /* end if $_POST */
        
        $dropdown = dt_get_states_key_dropdown_not_installed();
        
        // return form and dropdown
        
        $html .= '<table class="widefat ">
                    <thead><th>Zume Project - USA Census Data </th></thead>
                    <tbody>
                        <tr>
                            <td>
                                <form action="" method="POST">
                                    ' . wp_nonce_field( 'state_nonce_validate', 'state_nonce', true, false ) . $dropdown . '
                                    
                                    <button type="submit" class="button" value="submit">Upload State</button>
                                </form>
                            </td>
                        </tr>';
                    
        
        if ( !empty( $result ) || !empty( $result2 ) ) {
            $html .= '<tr>
                            <td>State Counties: ' . $result . '<br>State Tracts: ' . $result2 . '</td>
                      </tr>';
        }
        
        $html .= '</tbody>
                </table>';
        
        return $html;
    }
    
    
    /**
     * Creates drop down meta box for loading Omega Zone files
     *
     * @return mixed
     */
    public function select_oz_data_dropdown()
    {
        /*********************************/
        /* Create load dropdown */
        /*********************************/
        $load = '<select name="load-oz-countries">';
        
        $dir_contents =  dt_get_oz_country_list();
        foreach ( $dir_contents as $value ) {
            $installed = '';
    
            $load .= '<option value="' . $value->CntyID . '" ';
            if ( file_exists( plugin_dir_path( __FILE__ ) . 'json/oz/' . $value->CntyID . '.json' ) ) {
                $load .= ' disabled';
                $installed = ' (Installed)';
            }
            elseif ( isset( $_POST['load-oz-countries'] ) && $_POST['load-oz-countries'] == $value->CntyID ) {
                $load .= ' selected';
            }
            $load .= '>' . $value->Cnty_Name . $installed;
            $load .= '</option>';
        }
        $load .= '</select>';
        /* End load dropdown */
    
        /*********************************/
        /* Begin Admin 1 Create Dropdown */
        /*********************************/
        $admin1 = '<select name="oz-import-admin1-dropdown">';
    
        $dir_contents =  dt_get_oz_country_list();
        foreach ( $dir_contents as $value ) {
            $disabled = ''; // if get option exists
            
            if ( file_exists( plugin_dir_path( __FILE__ ) . 'json/oz/' . $value->CntyID . '.json' ) ) {
                
                $admin1 .= '<option value="' . $value->CntyID . '" ';
            
                $admin1 .= '>' . $value->Cnty_Name . $disabled;
                $admin1 .= '</option>';
            }
        }
        $admin1 .= '</select>';
        /* End Admin 1 Create Dropdown */
    
        /*********************************/
        /* Begin Admin 2 Create Dropdown */
        /*********************************/
        $admin2 = '<select name="oz-import-admin2-dropdown">';
    
        $dir_contents =  dt_get_oz_country_list();
        foreach ( $dir_contents as $value ) {
            $disabled = '';
    
            $admin2 .= '<option value="' . $value->CntyID . '" ';
            if ( file_exists( plugin_dir_path( __FILE__ ) . 'json/oz/' . $value->CntyID . '.json' ) ) {
                $admin2 .= ' disabled';
                $disabled = ' (Installed)';
            }
            elseif ( isset( $_POST['oz-countries-dropdown'] ) && $_POST['oz-countries-dropdown'] == $value->CntyID ) {
                $admin2 .= ' selected';
            }
            $admin2 .= '>' . $value->Cnty_Name . $disabled;
            $admin2 .= '</option>';
        }
        $admin2 .= '</select>';
        /* End Admin 2 Create Dropdown */
    
        /*********************************/
        /* Begin Admin 3 Create Dropdown */
        /*********************************/
        $admin3 = '<select name="oz-import-admin2-dropdown">';
    
        $dir_contents =  dt_get_oz_country_list();
        foreach ( $dir_contents as $value ) {
            $disabled = '';
        
            $admin3 .= '<option value="' . $value->CntyID . '" ';
            if ( file_exists( plugin_dir_path( __FILE__ ) . 'json/oz/' . $value->CntyID . '.json' ) ) {
                $admin3 .= ' disabled';
                $disabled = ' (Installed)';
            }
            elseif ( isset( $_POST['oz-countries-dropdown'] ) && $_POST['oz-countries-dropdown'] == $value->CntyID ) {
                $admin3 .= ' selected';
            }
            $admin3 .= '>' . $value->Cnty_Name . $disabled;
            $admin3 .= '</option>';
        }
        $admin3 .= '</select>';
        /* End Admin 3 Create Dropdown */
    
        /*********************************/
        /* Build form box                */
        /*********************************/
        $html = '';
        $html .= '<table class="widefat ">
                    <thead><th>Import Omega Zones</th></thead>
                    <tbody>
                        <tr>
                            <td>
                                <form action="" method="POST">
                                    ' . wp_nonce_field( 'load_oz_nonce_validate', 'load_oz_nonce', true, false ) . $load . '
                                    
                                    <button type="submit" class="button" value="submit">Load Country</button>
                                </form>
                            </td>
                        </tr>';
        $html .=        '<tr>
                            <td>
                                <form action="" method="POST">
                                    ' . wp_nonce_field( 'install_1_oz_nonce_validate', 'install_1_oz_nonce', true, false ) . $admin1 . '
                                    
                                    <button type="submit" class="button" value="submit">Install Country (Admin 1)</button>
                                </form>
                            </td>
                        </tr>';
        $html .=        '<tr>
                            <td>
                                <form action="" method="POST">
                                    ' . wp_nonce_field( 'install_2_oz_nonce_validate', 'install_2_oz_nonce', true, false ) . $admin2 . '
                                    
                                    <button type="submit" class="button" value="submit">Install Country (Admin 2)</button>
                                </form>
                            </td>
                        </tr>';
        $html .=        '<tr>
                            <td>
                                <form action="" method="POST">
                                    ' . wp_nonce_field( 'install_3_oz_nonce_validate', 'install_3_oz_nonce', true, false ) . $admin3 . '
                                    
                                    <button type="submit" class="button" value="submit">Install Country (Admin 3)</button>
                                </form>
                            </td>
                        </tr>';
        
        $html .= '</tbody>
                </table>';
        /* End Build Form Box */
        
        return $html;
    }
    
    
}
