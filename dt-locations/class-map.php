<?php

/**
 * Disciple Tools
 *
 * @class   Disciple_Tools_
 * @version 1.0.0
 * @since   1.0.0
 * @package Disciple_Tools
 * @author  Chasm.Solutions & Kingdom.Training
 */

if( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Class Disciple_Tools_Map
 */
class Disciple_Tools_Map
{

    /**
     * Constructor function.
     *
     * @access public
     * @since  1.0.0
     */
    public function __construct()
    {
    } // End __construct()

    /**
     * Generate tract map
     *
     * @param  $zoom         int Numeric value of the zoom level 1-16
     * @param  $tract_lng    int     Longitute coordinates
     * @param  $tract_lat    int     Latitude coordinates
     * @param  $coordinates  array  array string of coordinates for the google map. ["lat" => $x, "lng" => $y],
     *
     * @return mixed
     */
    public static function get_map( $zoom, $tract_lng, $tract_lat, $coordinates )
    {

        ?>
        <style>
            /* Always set the map height explicitly to define the size of the div
        * element that contains the map. */
            #map {
                height: 600px;
                width: 100%;
            }

            /* Optional: Makes the sample page fill the window. */
            html, body {
                height: 100%;
                margin: 0;
                padding: 0;
            }
        </style>
        <div id="map"></div>

        <script>
            // This example creates a simple polygon representing the Bermuda Triangle.

            function initMap() {
                var map = new google.maps.Map(document.getElementById('map'), {
                    zoom: <?php print intval( $zoom ); ?>,
                    center: {lng: <?php print (float) $tract_lng; ?>, lat: <?php print (float) $tract_lat; ?>},
                    mapTypeId: 'terrain'
                });

                // Define the LatLng coordinates for the polygon's path.
                <?php

                print "var coords = [";
                print json_encode( $coordinates );
                print "];";
                ?>

                var tracts = [];

                for (i = 0; i < coords.length; i++) {
                    tracts.push(new google.maps.Polygon({
                        paths: coords[i],
                        strokeColor: '#FF0000',
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: '',
                        fillOpacity: 0.2
                    }));

                    tracts[i].setMap(map);
                }
            }
        </script>
        <script async defer
                src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCcddCscCo-Uyfa3HJQVe0JdBaMCORA9eY&callback=initMap">
        </script>
        <?php
    }

}
