<?php

/**
 * Disciple_Tools_Tabs
 *
 * @class Disciple_Tools_Tabs
 * @version	0.1
 * @since 0.1
 * @package	Disciple_Tools_Tabs
 * @author Chasm.Solutions
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Disciple_Tools_Maintenance {

    /**
     * Constructor function.
     * @access  public
     * @since   0.1
     */
    public function __construct () {
    } // End __construct()

    /**
     * Page content for the tab
     */
    public function page_contents() {

        $html = '';

        $html .= '<div class="wrap"><h2>Maintenance</h2>'; // Block title

        $html .= '<div class="wrap"><div id="poststuff"><div id="post-body" class="metabox-holder columns-2">';
        $html .= '<div id="post-body-content">';
        $html .= $this->select_census_data_dropdown() . '<br>';
        $html .= $this->select_census_data_to_custom_db() . '<br>'; /* Add content to column */

        $html .= '</div><!-- end post-body-content --><div id="postbox-container-1" class="postbox-container">';
        $html .= $this->db_option ()  . '<br>'; /* Add content to column */

        $html .= '</div><!-- postbox-container 1 --><div id="postbox-container-2" class="postbox-container">';
        $html .= '';/* Add content to column */

        $html .= '</div><!-- postbox-container 2 --></div><!-- post-body meta box container --></div><!--poststuff end --></div><!-- wrap end -->';

        return $html;

    }

    /**
     * Creates drop down for uploading state xml files
     * @return mixed
     */
    public function select_census_data_dropdown () {


        $dropdown = dt_get_states_key_dropdown_LL();

        // return form and dropdown
        $html = '';
        $html .= '<table class="widefat striped">
                    <thead><th>Import from KML to WPDB Post Type</th></thead>
                    <tbody>
                        <tr>
                            <td><form action="" method="POST">' . wp_nonce_field( 'state_nonce_validate', 'state_nonce', true, false ) . $dropdown . '<button type="submit" class="button" value="submit">Upload State</button></form></td>
                        </tr>
                    </tbody>
                </table>';

        // check if $_POST to change option
        if(!empty($_POST['state_nonce']) && isset($_POST['state_nonce']) && wp_verify_nonce( $_POST['state_nonce'], 'state_nonce_validate' )) {

            if(!isset($_POST['states-dropdown'])) { // check if file is correctly set
                return false;
            }

            $file = dt_get_file_path_by_key($_POST['states-dropdown']); // build url

            if(!file_exists($file)) { // check if file exists
               return false;
            }

            $result = Disciple_Tools_Upload::upload_census_tract_kml_to_post_type($file); // run insert process

            if($result) { $status = 'Success'; } else { $status = 'Fail. Sorry.'; } // if success, then post success box
            $html .= '<table class="widefat striped">
                        <thead><th>Result</th></thead>
                        <tbody>
                            <tr>
                                <td>'.$status.'</td>
                            </tr>
                        </tbody>
                    </table>';


        } /* end if $_POST */

        return $html;
    }



    /**
     * Creates drop down for uploading state xml files
     * @return mixed
     */
    public function select_census_data_to_custom_db () {

        $dropdown = dt_get_states_key_dropdown_LL();

        // return form and dropdown
        $html = '';
        $html .= '<table class="widefat striped">
                    <thead><th>Select State to Import to Custom MySql Table</th></thead>
                    <tbody>
                        <tr>
                            <td><form action="" method="POST">' . wp_nonce_field( 'custom_validate', 'custom_nonce', true, false ) . $dropdown . '<button type="submit" class="button" value="submit">Upload State to Custom DB</button></form></td>
                        </tr>
                    </tbody>
                </table>';

        // check if $_POST to change option
        if(!empty($_POST['custom_nonce']) && isset($_POST['custom_nonce']) && wp_verify_nonce( $_POST['custom_nonce'], 'custom_validate' )) {

            if(!isset($_POST['states-dropdown'])) { // check if file is correctly set
                return false;
            }

            $file = dt_get_file_path_by_key($_POST['states-dropdown']); // build url

            if(!file_exists($file)) { // check if file exists
                return false;
            }

            // call upload_kml()
            $result = Disciple_Tools_Upload::upload_kml_to_custom($file);

            // if true, then post success box
            if($result) { $status = 'Success'; } else { $status = 'Fail. Sorry.'; }

            $html .= '<table class="widefat striped">
                        <thead><th>Result</th></thead>
                        <tbody>
                            <tr>
                                <td>'.$status.'</td>
                            </tr>
                        </tbody>
                    </table>';


        } /* end if $_POST */

        return $html;
    }


    /**
     * Returns the option field and submit button
     * @return mixed
     */
    public function db_option () {

        // check if $_POST to change option
        if(!empty($_POST['db_option']) && isset($_POST['db_option']) && wp_verify_nonce( $_POST['db_option'], 'db_option_validate' )) {
            if($_POST['db_option_type'] != get_option('_db_option_type')) {
                update_option('_db_option_type', $_POST['db_option_type']);
            }
        }

        // get/create option value
        if(!empty(get_option('_db_option_type'))) {
            $option = get_option('_db_option_type');
        } else {
            add_option('_db_option_type', 'KML', '', 'no');
            $option = 'KML';
        }

        $list = array('KML', 'WPDB', 'Custom');

        // get directory & build dropdown
        $dropdown = '<select name="db_option_type">';
        foreach ($list as $value) {
            $dropdown .= '<option value="'.$value.'" ';
            if ($value == $option) { $dropdown .= 'selected'; }
            $dropdown .= '>' . $value;
            $dropdown .= '</option>';
        }
        $dropdown .= '</select>';

        // return form and dropdown
        $html = '';
        $html .= '<table class="widefat striped">
                    <thead><th>Select Data Source</th></thead>
                    <tbody>
                        <tr>
                            <td><form action="" method="POST">' . wp_nonce_field( 'db_option_validate', 'db_option', true, false ) . $dropdown . '<button type="submit" class="button" value="submit">Save</button></form></td>
                        </tr>
                    </tbody>
                </table>
        ';
        return $html;

    }

}