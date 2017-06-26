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

class Disciple_Tools_State {

    /**
     * Constructor function.
     * @access  public
     * @since   0.1
     */
    public function __construct () {} // End __construct()

    /**
     * Page content for the tab
     */
    public function page_contents() {
        print'<div class="wrap"><h2>State Lookup</h2>'; // Block title
        print '<div class="wrap"><div id="poststuff"><div id="post-body" class="metabox-holder columns-2">';
        print '<div id="post-body-content">';
        /* Add content to column */

        $this->state_lookup_page (); // call form

        print '</div><!-- end post-body-content --><div id="postbox-container-1" class="postbox-container">';
        /* Add content to column */

        print '</div><!-- postbox-container 1 --><div id="postbox-container-2" class="postbox-container">';
        /* Add content to column */

        print '</div><!-- postbox-container 2 --></div><!-- post-body meta box container --></div><!--poststuff end --></div><!-- wrap end -->';

    }

    /**
     * Core form for address to tract search
     */
    public function state_lookup_page ()
    {

        $dropdown = dt_get_states_key_dropdown_LL();

        print '<form action="" method="POST">
                        <table class="widefat striped">
                        
                        <input type="hidden" name="state_lookup" value="true" />
                        
                         <tbody>
                            <tr>
                                <td>State</td>
                                <td>'.$dropdown.'</td>
                            </tr>
                           
                            <tr>
                                <td></td>
                                <td><button class="button" type="submit" value="submit">Lookup</button> </td>
                            </tr>
                        </tbody></table></form><br><br>';


        if (!empty($_POST['state_lookup'])) {

            $state = $_POST['states-dropdown'];
            $directory = dt_get_data_file_directory_LL ();
            $address = $directory->USA_tracts->{$_POST['states-dropdown']}->name;

            $google_result = Disciple_Tools_Google_Geolocation::query_google_api($address, $type = 'coordinates_only'); // get google api coordinates

            if (get_option('_db_option_type') == 'WPDB') {
                $coordinates = Disciple_Tools_Coordinates_DB::get_db_state( $state ); // return coordinates from database
            } else {
                $coordinates = Disciple_Tools_Coordinates_KML::get_tract_kml_state( $state ); // return coordinates from KML files
            }

            Disciple_Tools_Map::get_map('6', $google_result['lng'], $google_result['lat'], $coordinates); // return maps

        }
    }
}