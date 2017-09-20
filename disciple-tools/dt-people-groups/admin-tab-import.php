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
        $this->jp_api_key = 'vinskxSNWQKH'; // Joshua Project API Key /* TODO: Currently using Chasm JP Key (vinskxSNWQKH). Should we have each project get their own key? */

        // File paths
        $this->jp_json_path = plugin_dir_path( __FILE__ ) . 'json/';
        $this->jp_countries_path = plugin_dir_path( __FILE__ ) . 'json/jp_countries.json';

        // REST URLs
        $this->jp_query_countries_all = 'https://joshuaproject.net/api/v2/countries?api_key='.$this->jp_api_key.'&limit=300';
        $this->jp_query_pg_by_country_all = 'https://joshuaproject.net/api/v2/people_groups?api_key='.$this->jp_api_key.'&limit=1000';

        $this->check_data_age( 'jp_countries' );

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
//        print '<pre>'; print_r($this->get_joshua_project_country_names()); print '</pre>';

        $html .= '<br>'; /* Add content to column */
        $html .= '</div><!-- end post-body-content --><div id="postbox-container-1" class="postbox-container">';
        $html .= '<br>'; /* Add content to column */
        $html .= '</div><!-- postbox-container 1 --><div id="postbox-container-2" class="postbox-container">';
        $html .= '';/* Add content to column */
        $html .= '</div><!-- postbox-container 2 --></div><!-- post-body meta box container --></div><!--poststuff end --></div><!-- wrap end -->';
        return $html;
    }

    /**
     * Joshua Project install metabox
     * @return bool|string
     */
    public function joshua_project_country_install_box () {
        $this->check_data_age( 'jp_countries' ); // Checks the age of the static json countries data

        $html = '';
        $jp_install_request = '';
        $refresh = '';

        // check if $_POST to change option
        if(!empty( $_POST['jp_country_nonce'] ) && isset( $_POST['jp_country_nonce'] ) && wp_verify_nonce( $_POST['jp_country_nonce'], 'jp_country_nonce_validate' )) {

            if(isset( $_POST['jp-countries-dropdown'] )) { // check if file is correctly set
                $jp_install_request = $_POST['jp-countries-dropdown'];

                $jp_install_result = $this->install_jp_country( $jp_install_request );
            }

            if(isset( $_POST['jp-countries-refresh'] )) { // check if file is correctly set
                $refresh = $this->json_refresh( 'jp_countries' );
            }

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
                                ' . wp_nonce_field( 'jp_country_nonce_validate', 'jp_country_nonce', true, false ) .  '
                                    <button type="submit" class="button" value="submit" name="jp-countries-refresh">Refresh JP Countries Data</button> (Countries file is from '.date( "m-d-Y", filemtime( $this->jp_countries_path ) ).' )
                                    
                                </form>
                                
                            </td>
                        </tr>
                    </tbody>
                </table>';

        // Displays success/fail message for the import selection.
        if(!empty( $jp_install_result ) ) {
            $html .= '<table class="widefat striped">
                        <tbody>
                            <tr>
                                <td>JP Install Request for '.$jp_install_request.': '.$jp_install_result.'</td>
                            </tr>
                        </tbody>
                    </table>';
        }
        // Displays success message for the refresh button
        if( !empty( $refresh ) ) {
            $html .= '<table class="widefat striped">
                        <tbody>
                            <tr>
                                <td>Result of refreshing Joshua Project data: '.$refresh.'</td>
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
    public function get_joshua_project_country_names() {

        $jp_countries = json_decode( file_get_contents( $this->jp_countries_path ) ); // load countries data

        if( !$jp_countries ) { // if countries file not available, refresh Joshua Project data to file

            $this->json_refresh( 'jp_countries' );
            $jp_countries = json_decode( file_get_contents( $this->jp_countries_path ) );

            if(!$jp_countries) {
                return false;
            }
        }
        return $jp_countries;
    }

    /**
     * Builds dropdown menu for Joshua Project countries
     * @return string
     */
    public function get_joshua_project_countries_dropdown_not_installed() {

        $jp_countries = $this->get_joshua_project_country_names();

        if( $jp_countries ) { // check if no error

            $dropdown = '<select name="jp-countries-dropdown">';

            foreach ( $jp_countries->data as $value ) {
                $disabled = '';
                $dropdown .= '<option value="' . $value->ROG3 . '" ';
                if (get_option( '_installed_jp_country_'.$value->ROG3 )) {$dropdown .= ' disabled';
                    $disabled = ' (Installed)';}
                elseif ( isset( $_POST['states-dropdown'] ) && $_POST['states-dropdown'] == $value->ROG3 ) {$dropdown .= ' selected';}
                $dropdown .= '>' . $value->Ctry . $disabled;
                $dropdown .= '</option>';
            }

            $dropdown .= '</select>';

        } else {

            $dropdown = 'Unable to retrieve country list';

        }

        return $dropdown;
    }

    /**
     * Queries the Joshua Project API for all country data and saves it to jp_countries.json.
     * @return bool
     */
    public function json_refresh( $file ) {

        switch( $file ) {
            case 'jp_countries':

                $jp_countries = file_get_contents( $this->jp_query_countries_all );

                if( !$jp_countries ) {
                    wp_die( 'Failed to get data from Joshua Project' );
                }

                $put_file = file_put_contents( $this->jp_countries_path, $jp_countries );

                if( !$put_file ) {
                    wp_die( 'Failed to write to .json file' );
                }

                return true;

                break;
            default:
                return false;
                break;
        }

    }

    /**
     * Check the age of the json file contents.
     * @param $file
     */
    public function check_data_age( $file ) {

        $one_month_ago = date( "Ymd", mktime( 0, 0, 0, date( "m" )-1, date( "d" ), date( "Y" ) ) );

        switch ( $file ) {
            case 'jp_countries':

                // Static country data should not be older than 30 days.
                if(file_exists( $this->jp_countries_path )) {
                    $jp_countries_json_age = date( "Ymd", filemtime( $this->jp_countries_path ) );

                    if( $jp_countries_json_age < $one_month_ago ) {
                        $this->json_refresh( 'jp_countries' );
                    }
                } else {
                    $this->json_refresh( 'jp_countries' );
                }

                break;
            default:
                break;
        }

    }

    public function install_jp_country( $jp_install_request ) {
        global $wpdb;

        // get people group data for the country
        $jp_pg_by_country_json = file_get_contents( $this->jp_query_pg_by_country_all . '&ROG3='. $jp_install_request );

        if(!$jp_pg_by_country_json) {
            return new WP_Error( 'failed_api_call', 'Failed to get API data from Joshua Project' );
        }

        $results = json_decode( $jp_pg_by_country_json );

        $json_file = $this->jp_json_path . 'jp_pg_country_' . $jp_install_request . '.json';
        file_put_contents( $json_file, $jp_pg_by_country_json );


        foreach($results->data as $people_group) {
            $post = [
                "post_title" => $people_group->PeopNameInCountry . ' (' . $people_group->CtryShort . ' | ' . $people_group->ROP3 . ') ',
                'post_type' => 'peoplegroups',
                "post_content" => '',
                "post_excerpt" => '',
                "post_name" => $people_group->ROP3,
                "post_status" => "publish",
                "post_author" => get_current_user_id(),
            ];

            $new_post_id = wp_insert_post( $post );

            foreach($people_group as $key => $value) {
                $wpdb->insert(
                    $wpdb->postmeta,
                    [
                        'post_id' => $new_post_id,
                        'meta_key' => 'jp_'.$key,
                        'meta_value' => $value,
                    ]
                );
            }
        } // end group loop

        return true;

    }

}
