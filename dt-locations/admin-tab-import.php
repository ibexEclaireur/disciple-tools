<?php

/**
 * Disciple_Tools_Tabs
 *
 * @class   Disciple_Tools_Tabs
 * @version 1.0.0
 * @since   1.0.0
 * @package Disciple_Tools_Tabs
 * @author  Chasm.Solutions
 */

if( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Class Disciple_Tools_Locations_Tab_Import
 */
class Disciple_Tools_Locations_Tab_Import
{

    /**
     * Page content for the tab
     */
    public function page_contents()
    {

        echo '<div class="wrap"><h2>Import</h2>'; // Block title

        echo '<div class="wrap"><div id="poststuff"><div id="post-body" class="metabox-holder columns-2">';
        echo '<div id="post-body-content">';
        $this->select_oz_data_dropdown(); // prints
        echo '<br>';
        $this->select_us_census_data_dropdown(); // prints
        echo '<br>';
        $this->us_census_data_dropdown(); // prints
        echo '<br>';

        echo '</div><!-- end post-body-content --><div id="postbox-container-1" class="postbox-container">';
        $this->locations_currently_installed(); // prints /* Add content to column */

        echo '</div><!-- postbox-container 1 --><div id="postbox-container-2" class="postbox-container">';
        echo '';/* Add content to column */

        echo '</div><!-- postbox-container 2 --></div><!-- post-body meta box container --></div><!--poststuff end --></div><!-- wrap end -->';
    }

    /**
     * Creates drop down for uploading state xml files
     *
     * @return mixed
     */
    public function select_us_census_data_dropdown()
    {
        $result = '';
        $result2 = '';

        // check if $_POST to change option
        if ( !empty( $_POST[ 'state_nonce' ] ) && isset( $_POST[ 'state_nonce' ] ) && wp_verify_nonce( sanitize_key( $_POST[ 'state_nonce' ] ), 'state_nonce_validate' ) ) {

            if( !isset( $_POST[ 'states-dropdown' ] ) ) { // check if file is correctly set
                return false;
            }

            $result = Disciple_Tools_Locations_Import::upload_census_tract_kml_to_post_type( sanitize_text_field( wp_unslash( $_POST[ 'states-dropdown' ] ) ) ); // run insert process TODO make this a javascript call with a spinner.
            $result2 = Disciple_Tools_Locations_Import::upload_us_state_tracts( sanitize_text_field( wp_unslash( $_POST[ 'states-dropdown' ] ) ) );
        } /* end if $_POST */


        // return form and dropdown

        echo '<table class="widefat ">
                    <thead><th>Zume Project - USA Census Data </th></thead>
                    <tbody>
                        <tr>
                            <td>
                                <form action="" method="POST">
                                    ';
                                    wp_nonce_field( 'state_nonce_validate', 'state_nonce', true );
                                    // TODO: make this function print instead of returning HTML
                                    // @codingStandardsIgnoreLine
                                    echo dt_get_states_key_dropdown_not_installed();

                                    echo '<button type="submit" class="button" value="submit">Upload State</button>
                                </form>
                            </td>
                        </tr>';

        if( !empty( $result ) || !empty( $result2 ) ) {
            echo '<tr>
                            <td>State Counties: ' . esc_html( $result ) . '<br>State Tracts: ' . esc_html( $result2 ) . '</td>
                      </tr>';
        }

        echo '</tbody>
                </table>';
    }

    /**
     * Creates drop down for uploading state xml files and prints
     *
     * @version 2
     */
    public function us_census_data_dropdown()
    {
        $result = '';
        $result2 = '';

        // check if $_POST to change option
        if( !empty( $_POST[ 'state_nonce' ] ) && isset( $_POST[ 'state_nonce' ] ) && wp_verify_nonce( sanitize_key( $_POST[ 'state_nonce' ] ), 'state_nonce_validate' ) ) {

            if( !isset( $_POST[ 'states-dropdown' ] ) ) { // check if file is correctly set
                return false;
            }

            $result = Disciple_Tools_Locations_Import::census_tract_kml_to_post_type( sanitize_text_field( wp_unslash( $_POST[ 'states-dropdown' ] ) ) ); // run insert process TODO make this a javascript call with a spinner.
            $result2 = Disciple_Tools_Locations_Import::upload_us_state_tracts_coordinates( sanitize_text_field( wp_unslash( $_POST[ 'states-dropdown' ] ) ) );
        } /* end if $_POST */


        // return form and dropdown

        echo '<table class="widefat ">
                    <thead><th>Zume Project - USA Census Data </th></thead>
                    <tbody>
                        <tr>
                            <td>
                                <form action="" method="POST">
                                    ';
                                    wp_nonce_field( 'state_nonce_validate', 'state_nonce', true );
                                    // TODO: make this print HTML instead of returning it
                                    // @codingStandardsIgnoreLine
                                    echo dt_get_states_key_dropdown_not_installed();

                                    echo '<button type="submit" class="button" value="submit">Upload State</button>
                                </form>
                            </td>
                        </tr>';

        if( !empty( $result ) || !empty( $result2 ) ) {
            echo '<tr>
                            <td>State Counties: ' . esc_html( $result ) . '<br>State Tracts: ' . esc_html( $result2 ) . '</td>
                      </tr>';
        }

        echo '</tbody>
                </table>';
    }

    /**
     * Creates drop down meta box for loading Omega Zone files and prints
     */
    public function select_oz_data_dropdown()
    {

        /*********************************/
        /* POST */
        /*********************************/
        if( !empty( $_POST[ 'oz_nonce' ] ) && isset( $_POST[ 'oz_nonce' ] ) && wp_verify_nonce( sanitize_key( $_POST[ 'oz_nonce' ] ), 'oz_nonce_validate' ) ) {

            if( !empty( $_POST[ 'load-oz-admin1' ] ) ) {

                // insert records
                $import = new Disciple_Tools_Locations_Import();
                $import->insert_location_oz( sanitize_text_field( wp_unslash( $_POST[ 'load-oz-admin1' ] ) ), 'admin1' );

                // Update option.
                $option = get_option( '_dt_oz_installed' );
                $option[ 'Adm1ID' ][] = sanitize_text_field( wp_unslash( $_POST[ 'load-oz-admin1' ] ) );
                update_option( '_dt_oz_installed', $option );
            }

            if( !empty( $_POST[ 'load-oz-admin2' ] ) ) {

                // insert records
                $import = new Disciple_Tools_Locations_Import();
                $import->insert_location_oz( sanitize_text_field( wp_unslash( $_POST[ 'load-oz-admin2' ] ) ), 'admin2' );

                // Update option.
                $option = get_option( '_dt_oz_installed' );
                $option[ 'Adm2ID' ][] = sanitize_text_field( wp_unslash( $_POST[ 'load-oz-admin2' ] ) );
                update_option( '_dt_oz_installed', $option );
            }

            if( !empty( $_POST[ 'load-oz-admin3' ] ) ) {

                // insert records
                $import = new Disciple_Tools_Locations_Import();
                $import->insert_location_oz( sanitize_text_field( wp_unslash( $_POST[ 'load-oz-admin3' ] ) ), 'admin3' );

                // Update option.
                $option = get_option( '_dt_oz_installed' );
                $option[ 'Adm3ID' ][] = sanitize_text_field( wp_unslash( $_POST[ 'load-oz-admin3' ] ) );
                update_option( '_dt_oz_installed', $option );
            }

            if( !empty( $_POST[ 'load-oz-admin4' ] ) ) {

                // insert records
                $import = new Disciple_Tools_Locations_Import();
                $import->insert_location_oz( sanitize_text_field( wp_unslash( $_POST[ 'load-oz-admin4' ] ) ), 'admin4' );

                // Update option.
                $option = get_option( '_dt_oz_installed' );
                $option[ 'Adm4ID' ][] = sanitize_text_field( wp_unslash( $_POST[ 'load-oz-admin4' ] ) );
                update_option( '_dt_oz_installed', $option );
            }
        }
        /* End POST */

        /*********************************/
        /* Load or Create Options        */
        /*********************************/
        if( get_option( '_dt_oz_installed' ) ) {
            $currently_installed = get_option( '_dt_oz_installed' );
        } else {
            $currently_installed = [
                'Adm1ID' => [],
                'Adm2ID' => [],
                'Adm3ID' => [],
                'Adm4ID' => [],
            ];
            add_option( '_dt_oz_installed', $currently_installed, '', false );
        }

        /*********************************/
        /* Begin Admin 1 Create Dropdown */
        /*********************************/

        $dir_contents = dt_get_oz_country_list();

        $admin1 = '<select name="load-oz-admin1" class="regular-text">';
        $admin1 .= '<option >- Choose</option>';

        foreach( $dir_contents as $value ) {
            // @codingStandardsIgnoreLine
            $country_id = $value->CntyID;
            $test = array_search( $country_id, $currently_installed[ 'Adm1ID' ] );
            if( !( $test ) && !( $test === 0 ) ) {
                $admin1 .= '<option value="' . esc_attry( $country_id ) . '" ';
                // @codingStandardsIgnoreLine
                $admin1 .= '>' . esc_html( $value->Cnty_Name );
                $admin1 .= '</option>';
            }
        }

        $admin1 .= '</select>';
        /* End load dropdown */

        /*********************************/
        /* Begin Admin 2 Create Dropdown */
        /*********************************/
        $admin2 = '<select name="load-oz-admin2" class="regular-text">';

        if( !empty( $currently_installed[ 'Adm1ID' ] ) && isset( $currently_installed[ 'Adm1ID' ] ) ) {

            $admin2 .= '<option>- Choose</option>';

            foreach( $currently_installed[ 'Adm1ID' ] as $value ) {
                $test = array_search( $value, $currently_installed[ 'Adm2ID' ] );
                if( !( $test ) && !( $test === 0 ) ) {
                    $admin2 .= '<option value="' . $value . '" ';
                    $admin2 .= '>' . dt_locations_match_country_to_key( $value );
                    $admin2 .= '</option>';
                }
            }
        } else {
            $admin2 .= '<option selected>- Unavailable</option>';
        }

        $admin2 .= '</select>';
        /* End Admin 1 Create Dropdown */

        /*********************************/
        /* Begin Admin 3 Create Dropdown */
        /*********************************/
        $admin3 = '<select name="load-oz-admin3" class="regular-text">';

        if( !empty( $currently_installed[ 'Adm2ID' ] ) && isset( $currently_installed[ 'Adm2ID' ] ) ) {

            $admin3 .= '<option>- Choose</option>';

            foreach( $currently_installed[ 'Adm2ID' ] as $value ) {
                $test = array_search( $value, $currently_installed[ 'Adm3ID' ] );
                if( !( $test ) && !( $test === 0 ) ) {
                    $admin3 .= '<option value="' . $value . '" ';
                    $admin3 .= '>' . dt_locations_match_country_to_key( $value );
                    $admin3 .= '</option>';
                }
            }
        } else {
            $admin3 .= '<option selected>- Unavailable</option>';
        }

        $admin3 .= '</select>';
        /* End Admin 2 Create Dropdown */

        /*********************************/
        /* Begin Admin 4 Create Dropdown */
        /*********************************/
        $admin4 = '<select name="load-oz-admin4" class="regular-text">';

        if( !empty( $currently_installed[ 'Adm3ID' ] ) && isset( $currently_installed[ 'Adm3ID' ] ) ) {

            $admin4 .= '<option>- Choose</option>';

            foreach( $currently_installed[ 'Adm3ID' ] as $value ) {
                $test = array_search( $value, $currently_installed[ 'Adm4ID' ] );
                if( !( $test ) && !( $test === 0 ) ) {
                    $admin4 .= '<option value="' . $value . '" ';
                    $admin4 .= '>' . dt_locations_match_country_to_key( $value );
                    $admin4 .= '</option>';
                } else {
                    $admin4 .= '<option value="' . $value . '" disabled';
                    $admin4 .= '>' . dt_locations_match_country_to_key( $value ) . ' (Installed)';
                    $admin4 .= '</option>';
                }
            }
        } else {
            $admin4 .= '<option selected>- Unavailable</option>';
        }

        $admin4 .= '</select>';
        /* End Admin 3 Create Dropdown */

        /*********************************/
        /* Build form box                */
        /*********************************/
        $html = '';
        echo '<table class="widefat ">
                    <thead><th>Import Omega Zones/2414/Zume International</th></thead>
                    <tbody>
                        <tr>
                            <td>
                                <form action="" method="POST">
                                    ';
                                    wp_nonce_field( 'oz_nonce_validate', 'oz_nonce', true );
                                    // TODO: refactor to avoid building a variable containing HTML
                                    // @codingStandardsIgnoreLine
                                    echo $admin1;

                                    echo '<button type="submit" class="button" value="submit">Load Admin Level 1</button>
                                </form>
                            </td>
                        </tr>';
        echo '<tr>
                            <td>
                                <form action="" method="POST">
                                    ';
                                    wp_nonce_field( 'oz_nonce_validate', 'oz_nonce', true );
                                    // TODO: refactor to avoid building a variable containing HTML
                                    // @codingStandardsIgnoreLine
                                    echo $admin2;

                                    echo '<button type="submit" class="button" value="submit">Load Admin Level 2</button>
                                </form>
                            </td>
                        </tr>';
        echo '<tr>
                            <td>
                                <form action="" method="POST">
                                    ';
                                    wp_nonce_field( 'oz_nonce_validate', 'oz_nonce', true );
                                    // TODO: refactor to avoid building a variable containing HTML
                                    // @codingStandardsIgnoreLine
                                    echo $admin3;

                                    echo '<button type="submit" class="button" value="submit">Load Admin Level 3</button>
                                </form>
                            </td>
                        </tr>';
        echo '<tr>
                            <td>
                                <form action="" method="POST">
                                    ';
                                    wp_nonce_field( 'oz_nonce_validate', 'oz_nonce', true );
                                    // TODO: refactor to avoid building a variable containing HTML
                                    // @codingStandardsIgnoreLine
                                    echo $admin4;

                                    echo '<button type="submit" class="button" value="submit">Load Admin Level 4</button>
                                </form>
                            </td>
                        </tr>';

        echo '</tbody>
                </table>';

        /* End Build Form Box */
    }

    /**
     * prints
     */
    public function locations_currently_installed()
    {
        global $wpdb;
        $count = [];

        // Search for currently installed locations

        echo '<table class="widefat ">
                    <thead><th>Currently Installed</th></thead>
                    <tbody>
                        <tr>
                            <td>';
        // Total number of locations in database
        echo 'Total number of location posts: <br>' . esc_html( wp_count_posts( 'locations' )->publish ) . '<br>';

        // Total number of countries
        $count[ 'countries' ] = $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE post_type = 'locations' AND post_name LIKE '___'" );
        echo 'Total number of countries (admin0): <br>' . intval( $count[ 'countries' ] ) . '<br>';

        // Total number of admin1
        $count[ 'admin1' ] = $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE post_type = 'locations' AND post_name LIKE '___-___'" );
        echo 'Total number of Admin1: <br>' . intval( $count[ 'admin1' ] ) . '<br>';

        // Total number of admin2
        $count[ 'admin2' ] = $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE post_type = 'locations' AND post_name LIKE '___-___-___'" );
        echo 'Total number of Admin2: <br>' . intval( $count[ 'admin2' ] ) . '<br>';

        // Total number of admin3
        $count[ 'admin3' ] = $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE post_type = 'locations' AND post_name LIKE '___-___-___-___'" );
        echo 'Total number of Admin3: <br>' . intval( $count[ 'admin3' ] ) . '<br>';

        // Total number of admin4
        $count[ 'admin4' ] = $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE post_type = 'locations' AND post_name LIKE '___-___-___-___-___'" );
        echo 'Total number of Admin4: <br>' . intval( $count[ 'admin4' ] ) . '<br>';

        echo '      </td>
                        </tr>';

        echo '</tbody>
                </table>';
    }

}
