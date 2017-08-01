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
     * Constructor function.
     *
     * @access public
     * @since  0.1
     */
    public function __construct()
    {
    } // End __construct()
    
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
        $html .= $this->select_country_data_dropdown() . '<br>';
        
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
                    <thead><th>US Census Data </th></thead>
                    <tbody>
                        <tr>
                            <td>
                                <form action="" method="POST">
                                    ' . wp_nonce_field( 'state_nonce_validate', 'state_nonce', true, false ) . $dropdown . '
                                    
                                    <button type="submit" class="button" value="submit">Upload State</button>
                                </form>
                            </td>
                        </tr>
                    </tbody>
                </table>';
        
        if ( !empty( $result ) || !empty( $result2 ) ) {
            $html .= '<table class="widefat striped">
                        <thead><th>Result</th></thead>
                        <tbody>
                            <tr>
                                <td>State Counties: ' . $result . '<br>State Tracts: ' . $result2 . '</td>
                            </tr>
                        </tbody>
                    </table>';
        }
        
        
        return $html;
    }
    
    
    /**
     * Creates drop down for uploading state xml files
     *
     * @return mixed
     */
    public function select_country_data_dropdown()
    {
        
        $country_dropdown = 'dropdown';
        
        // return form and dropdown
        $html = '';
        $html .= '<table class="widefat ">
                    <thead><th>Import Omega Zones</th></thead>
                    <tbody>
                        <tr>
                            <td>
                                <form action="" method="POST">
                                    ' . wp_nonce_field( 'country_nonce_validate', 'country_nonce', true, false ) . $country_dropdown . '
                                    
                                    <button type="submit" class="button" value="submit">Import Country</button>
                                </form>
                            </td>
                        </tr>
                    </tbody>
                </table>';
        return $html;
        
        // check if $_POST to change option
        //        if (!empty( $_POST['country_nonce'] ) && isset( $_POST['country_nonce'] ) && wp_verify_nonce( $_POST['country_nonce'], 'country_nonce_validate' )) {
        //
        //            if (!isset( $_POST['country-dropdown'] )) { // check if file is correctly set
        //                return false;
        //            }
        //
        ////            $file = dt_get_file_path_by_key( $_POST['country-dropdown'] ); // build url
        //
        //            if (!file_exists( $file )) { // check if file exists
        //                return false;
        //            }
        //
        ////            $result = Disciple_Tools_Upload::upload_country_kml_to_post_type( $file ); // run insert process
        //
        //            if ($result) {
        //                $status = 'Success';
        //            } else {
        //                $status = 'Fail. Sorry.';
        //            } // if success, then post success box
        //
        //
        //            $html .= '<table class="widefat striped">
        //                        <thead><th>Result</th></thead>
        //                        <tbody>
        //                            <tr>
        //                                <td>' . $status . '</td>
        //                            </tr>
        //                        </tbody>
        //                    </table>';
        //
        //
        //        } /* end if $_POST */
        //
        //
        //    }
        
    }
}
