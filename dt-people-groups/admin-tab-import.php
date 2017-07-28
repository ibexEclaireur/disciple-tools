<?php
/**
 * Disciple_Tools_People_Groups_Tab_Import
 *
 * @class   Disciple_Tools_People_Groups_Tab_Import
 * @version 0.1
 * @since   0.1
 * @package Disciple_Tools
 * @author  Chasm.Solutions
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly
class Disciple_Tools_People_Groups_Tab_Import {

    /**
     * Constructor function.
     *
     * @access public
     * @since  0.1
     */
    public function __construct () {
        // API Keys
        $this->jp_api_key = 'vinskxSNWQKH'; // Joshua Project API Key

        // File paths
        $this->jp_countries_path = plugin_dir_path( __FILE__ ) . 'json/jp_countries.json';

        // REST URLs
        $this->jp_query_countries_all = 'http://joshuaproject.net/api/v2/countries?fields=Ctry&api_key='.$this->jp_api_key.'&limit=300';

    } // End __construct()
    /**
     * Page content for the tab
     */
    public function page_contents() {
        $html = '';
        $html .= '<div class="wrap">'; // Block title
        $html .= '<div class="wrap"><div id="poststuff"><div id="post-body" class="metabox-holder columns-2">';
        $html .= '<div id="post-body-content">';
        $html .= $this->joshua_project_country_install_box();
//        print '<pre>'; print_r($this->get_joshua_project_country_names ()); print '</pre>';

        $html .= '<br>'; /* Add content to column */
        $html .= '</div><!-- end post-body-content --><div id="postbox-container-1" class="postbox-container">';
        $html .= '<br>'; /* Add content to column */
        $html .= '</div><!-- postbox-container 1 --><div id="postbox-container-2" class="postbox-container">';
        $html .= '';/* Add content to column */
        $html .= '</div><!-- postbox-container 2 --></div><!-- post-body meta box container --></div><!--poststuff end --></div><!-- wrap end -->';
        return $html;
    }

    public function joshua_project_country_install_box () {
        $html = '';
        $result = '';
        $result2 = '';
        $refresh = '';

        // check if $_POST to change option
        if(!empty( $_POST['jp_country_nonce'] ) && isset( $_POST['jp_country_nonce'] ) && wp_verify_nonce( $_POST['jp_country_nonce'], 'jp_country_nonce_validate' )) {

            if(!isset( $_POST['jp-country-dropdown'] )) { // check if file is correctly set
                return false;
            }

            $result = '';
            $result2 = '';

        } /* end if $_POST */

        if(!empty( $_POST['jp_country_refresh_nonce'] ) && isset( $_POST['jp_country_refresh_nonce'] ) && wp_verify_nonce( $_POST['jp_country_refresh_nonce'], 'jp_country_refresh_nonce_validate' )) {

            if(!isset( $_POST['jp-country-refresh'] )) { // check if file is correctly set
                return false;
            }

            $refresh = $this->jp_countries_json_refresh();

        } /* end if $_POST */

        $dropdown = $this->get_joshua_project_countries_dropdown_not_installed();

        // return form and dropdown

        $html .= '<table class="widefat ">
                    <thead><th>Joshua Project Country Install</th></thead>
                    <tbody>
                        <tr>
                            <td>
                                <form action="" method="POST">
                                    ' . wp_nonce_field( 'jp_country_nonce_validate', 'jp_country_nonce', true, false ) .  $dropdown .  '
                                    
                                    <button type="submit" class="button" value="submit">Import Country</button>
                                </form><br>
                                
                                <form action="" method="POST">
                                ' . wp_nonce_field( 'jp_country_refresh_nonce_validate', 'jp_country_refresh_nonce', true, false ) .  '
                                    <button type="submit" class="button" value="submit" name="jp-country-refresh">Refresh Country Data</button>
                                </form>
                                
                            </td>
                        </tr>
                    </tbody>
                </table>';


        if(!empty( $result ) || !empty( $result2 )) {
            $html .= '<table class="widefat striped">
                        <thead><th>Result</th></thead>
                        <tbody>
                            <tr>
                                <td>State Counties: '.$result.'<br>State Tracts: '.$result2.'</td>
                            </tr>
                        </tbody>
                    </table>';
        }
        if( !empty( $refresh ) ) {
            $html .= '<table class="widefat striped">
                        <thead><th>Result of Refresh</th></thead>
                        <tbody>
                            <tr>
                                <td>State Counties: '.$refresh.'</td>
                            </tr>
                        </tbody>
                    </table>';
        }

        return $html;
    }

    /**
     * Gets list of countries from Joshua Project API
     * Returns two letter country abbreviation and full country name.
     * @return mixed|array|boolean
     */
    public function get_joshua_project_country_names () {
        /* TODO: Currently using Chasm JP Key (vinskxSNWQKH). Should we have each project get their own key? */
        $jp_countries = json_decode( file_get_contents( 'http://joshuaproject.net/api/v2/countries?fields=Ctry&api_key=vinskxSNWQKH&limit=300&fields=ROG3|Ctry' ) );
        if( !$jp_countries ) {
            return false;
        }
        return $jp_countries->data;
    }

    /**
     * Builds dropdown menu
     * @return string
     */
    public function get_joshua_project_countries_dropdown_not_installed() {

        $jp_country_list = $this->get_joshua_project_country_names();

        if( $jp_country_list ) { // check if no error
            $dropdown = '<select name="states-dropdown">';

            foreach ( $jp_country_list as $value ) {
                $disabled = '';

                $dropdown .= '<option value="' . $value->ROG3 . '" ';
                if (get_option( '_installed_jp_country_'.$value->ROG3 )) {$dropdown .= ' disabled';
                    $disabled = ' (Installed)';}
                elseif (isset( $_POST['states-dropdown'] ) && $_POST['states-dropdown'] == $value->ROG3) {$dropdown .= ' selected';}
                $dropdown .= '>' . $value->Ctry . $disabled;
                $dropdown .= '</option>';
            }

            $dropdown .= '</select>';
        } else {
            $dropdown = 'Unable to retrive country list';
        }

        return $dropdown;
    }

    /**
     * @return bool
     */
    public function jp_countries_json_refresh () {
        $jp_countries = file_get_contents( $this->jp_query_countries_all );
        if( !$jp_countries ) {
            wp_die( 'Failed to get data from Joshua Project' );
        }
        $put_file = file_put_contents( $this->jp_countries_path, $jp_countries );
        if( !$put_file ) {
            wp_die( 'Failed to write to .json file' );
        }
        return true;
    }

}
