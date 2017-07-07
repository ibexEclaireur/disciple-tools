<?php

/**
 * Disciple Tools
 *
 * @class Disciple_Tools_
 * @version	0.1
 * @since 0.1
 * @package	Disciple_Tools
 * @author Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


function dt_map_metabox () {
    $object = new Disciple_Tools_Metabox_Map();
    return $object;
}

class Disciple_Tools_Metabox_Map {


    public $post_type;

    /**
     * Constructor function.
     * @access  public
     * @since   0.1
     */
    public function __construct () {

    } // End __construct()


    /**
     * Load activity metabox
     */
    public function display_map () {
        global $wpdb, $post;

        $result = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE post_id = '$post->ID' AND meta_key LIKE 'polygon_$post->post_content_filtered%'");

        echo 'State and County ID: ' . $post->post_content_filtered . '<br>';

        echo '<select name="select_tract" id="select_tract">';
        echo '<option value="all">All Tracts</option>';
        foreach($result as $value) {
            echo '<option value="'.$value->meta_key.'">Tract: ' . substr($value->meta_key,8) . '</option>';
        }
        echo '</select>';


        $key = substr($value->meta_key,8);

        /* Get center coordinates */
        $center_coords = $wpdb->get_var("SELECT meta_value FROM $wpdb->postmeta WHERE post_id = '$post->ID' AND meta_key = 'polygon_$key'");
        $coords = json_decode($center_coords);

        $high_lng = -9999999; //will hold max val
        $high_lat = -9999999; //will hold max val
        $low_lng = 9999999; //will hold max val
        $low_lat = 9999999; //will hold max val
        $found_item = null; //will hold item with max val;

        foreach($coords as $k=>$v)
        {
            if($v->lng > $high_lng)
            {
                $high_lng = $v->lng;
            }
            if($v->lng < $low_lng)
            {
                $low_lng = $v->lng;
            }
            if($v->lat > $high_lat)
            {
                $high_lat = $v->lat;
            }
            if($v->lat < $low_lat)
            {
                $low_lat = $v->lat;
            }
        }

        $half_lng_difference = ($high_lng - $low_lng) / 2;
        $center_lng = $high_lng - $half_lng_difference;

        $half_lat_difference = ($high_lat - $low_lat) / 2;
        $center_lat = $high_lat - $half_lat_difference;
        /* End get center coordinates */

        

        ?>
        <div id="search-response"></div>
        <style>
            /* Always set the map height explicitly to define the size of the div
        * element that contains the map. */
            #map {
                height: 450px;;
                width: 100%;
                max-width:1000px;
            }
            /* Optional: Makes the sample page fill the window. */
            html, body {
                height: 100%;
                margin: 0;
                padding: 0;
            }

        </style>
        <div id="map" ></div>
        <script type="text/javascript">

            jQuery(document).ready(function() {

                var zoom = 8;

                var map = new google.maps.Map(document.getElementById('map'), {
                    zoom: zoom,
                    center: {lng: <?php echo $center_lng; ?>, lat: <?php echo $center_lat; ?>},
                    mapTypeId: 'terrain'
                });

                // Define the LatLng coordinates for the polygon's path.
                var coords = [ <?php
                                $rows = count($result);
                                $i = 0;
                                foreach($result as $value) {
                                   echo $value->meta_value;
                                   $i++;
                                   if($rows > $i + 1) {echo ','; }
                                } ?> ];

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

                jQuery('#select_tract').change( function () {
                    jQuery('#spinner').prepend('<img src="spinner.svg" style="height:30px;" />');

                    var tract = jQuery('#select_tract').val();
                    var restURL = '<?php echo get_rest_url(null, '/lookup/v1/tract/gettractmap'); ?>';
                    jQuery.post( restURL, { address: address })
                        .done(function( data ) {
                            jQuery('#spinner').html('');
                            jQuery('#search-button').html('Search Again?');
                            jQuery('#search-response').html('<p>Looks like you searched for <strong>' + data.formatted_address + '</strong>? <br>Therefore, <strong>' + data.geoid + '</strong> is most likely your census tract represented in the map below. </p>' );

                            jQuery('#map').css('height', '475px');

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
        <script
                src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCcddCscCo-Uyfa3HJQVe0JdBaMCORA9eY">
        </script>

            <?php



    }
}