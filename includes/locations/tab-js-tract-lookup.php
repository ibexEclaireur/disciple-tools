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

class Disciple_Tools_JS_Tract_Lookup {
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
        print'<div class="wrap"><h2>Address to Tract Lookup</h2>'; // Block title
        print '<div class="wrap"><div id="poststuff"><div id="post-body" class="metabox-holder columns-2">';
        print '<div id="post-body-content">';
        /* Add content to column */

        $this->address_to_tract_search ();

        print '</div><!-- end post-body-content --><div id="postbox-container-1" class="postbox-container">';
        /* Add content to column */

        print '</div><!-- postbox-container 1 --><div id="postbox-container-2" class="postbox-container">';
        /* Add content to column */

        print '</div><!-- postbox-container 2 --></div><!-- post-body meta box container --></div><!--poststuff end --></div><!-- wrap end -->';

    }

    /**
     * Core form for address to tract search
     */
    public function address_to_tract_search ()
        /** TODO: Create a search that is global and adds a mark instead of a polygon */
    {
        ?>
        <div id="map-form">
            <input type="hidden" name="google_lookup" value="true" />
            <table class="widefat striped">
                <tbody>
                <tr>
                    <td>Address</td>
                    <td><input type="text" name="address" id="address" value="" /> </td>
                </tr>
                <tr>
                    <td></td>
                    <td><button class="button" type="button" value="submit">Lookup</button> </td>
                </tr>
                </tbody>
            </table>
        </div>

        <br><br>
        <div id="search-response"></div>
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

        <script type="text/javascript">
            jQuery(document).ready(function() {
                jQuery('button').click( function () {
                    var address = jQuery('#address').val();
                    jQuery.post( "<?php echo get_site_url(); ?>/wp-json/lookup/v1/tract/gettractmap", { address: address })
                        .done(function( data ) {
                            jQuery('#search-response').html('We found that your tract is ' + data.geoid );

                            var map = new google.maps.Map(document.getElementById('map'), {
                                zoom: data.zoom,
                                center: {lng: data.lng, lat: data.lat},
                                mapTypeId: 'terrain'
                            });

                            // Define the LatLng coordinates for the polygon's path.
                            var coords = [ data.coordinates ];

                            var tracts = [];

                            for (i = 0; i < coords.length; i++) {
                                tracts.push(new google.maps.Polygon({
                                    paths: coords[i],
                                    strokeColor: '#FF0000',
                                    strokeOpacity: 0.5,
                                    strokeWeight: 2,
                                    fillColor: '',
                                    fillOpacity: 0.2
                                }));

                                tracts[i].setMap(map);
                            }

                        });
                });
            });
        </script>
        <script async defer
                src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCcddCscCo-Uyfa3HJQVe0JdBaMCORA9eY">
        </script>

        <?php
    }



}