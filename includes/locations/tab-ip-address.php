<?php

/**
 * Disciple_Tools_IP_Tab
 *
 * @class Disciple_Tools_IP_Tab
 * @version	0.1
 * @since 0.1
 * @package	Disciple_Tools_IP_Tab
 * @author Chasm.Solutions
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Disciple_Tools_IP_Tab {

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
        print'<div class="wrap"><h2>IP Query</h2>'; // Block title
        print '<div class="wrap"><div id="poststuff"><div id="post-body" class="metabox-holder columns-2">';
        print '<div id="post-body-content">';
        /* Add content to column */

        $this->ipinfo_page(); // call form

        print '</div><!-- end post-body-content --><div id="postbox-container-1" class="postbox-container">';
        /* Add content to column */

        print '</div><!-- postbox-container 1 --><div id="postbox-container-2" class="postbox-container">';
        /* Add content to column */

        print '</div><!-- postbox-container 2 --></div><!-- post-body meta box container --></div><!--poststuff end --></div><!-- wrap end -->';
    }

    /**
     * Example of IP Lookup
     */
    public function ipinfo_page () {
        // This is a sample lookup off an ipaddress using the ipinfo.io service
        $ip = $_SERVER['REMOTE_ADDR'];

        if($ip == '::1') { // work around for local development environment
            $ip = '67.177.209.111';
        }

        print 'IP Address is set to: ' . $ip . '<br><br>';

        $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
        $address = $details->loc;

        $google_result = Disciple_Tools_Google_Geolocation::query_google_api($address, $type = 'core'); // get google api info
        if ($google_result == 'ZERO_RESULTS') {
            wp_die('Yikes. Are you searching for an address on earth? Might be our bad. Can you try it again?');
        }

        print 'You IP gave us this address: <strong>' . $google_result['formatted_address'] . '</strong>';

        $census_result = Disciple_Tools_Census_Geolocation::query_census_api($google_result['lng'], $google_result['lat'], $type = 'core'); // get census api data
        if ($census_result == 'ZERO_RESULTS') {
            wp_die('Yikes. Google might know where this is, but the U.S. government doesn\'t? Can you try it again? Our U.S. Census info is not perfect.');
        }

        print ', so we found that <strong>' . $census_result['geoid'] . '</strong> is your tract number.<br><br>';

        $state = substr($census_result['geoid'], 0, 2);

        if (get_option('_db_option_type') == 'WPDB') {
            $coordinates = Disciple_Tools_Coordinates_DB::get_db_state( $state ); // return coordinates from database
        } else {
            $coordinates = Disciple_Tools_Coordinates_KML::get_tract_kml_coordinates ($census_result['geoid'], $state ); // return coordinates from KML files
        }

        Disciple_Tools_Map::get_map($census_result['zoom'], $google_result['lng'], $google_result['lat'], $coordinates); // return maps
    }

}

