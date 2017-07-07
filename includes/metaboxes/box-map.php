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


        echo '<select name="select_tract" id="select_tract">';
        echo '<option value="all">All Tracts</option>';
        foreach($result as $value) {
            echo '<option value="'.$value->meta_key.'">Tract: ' . substr($value->meta_key,8) . '</option>';
        }
        echo '</select>';
        echo '<span id="spinner"></span>';

        print ' <br>state/county id : ' . $post->post_content_filtered ;

        /*********************************************/


        /* query */
        $county_coords = $wpdb->get_results("SELECT meta_value FROM $wpdb->postmeta WHERE post_id = '$post->ID' AND meta_key LIKE 'polygon_$post->post_content_filtered%'", ARRAY_A);

        /* build full json of coodinates*/
        $rows = count($county_coords);
        $string = '[';
        $i = 0;
        foreach($county_coords as $value) {
            $string .= $value['meta_value'];
            if($rows > $i + 1 ) {$string .= ','; }
            $i++;
        }
        $string .= ']';
        $coords_objects = json_decode($string);
//        print_r($coords_objects );

        /* set values */
        $high_lng_e = -9999999; //will hold max val
        $high_lat_n = -9999999; //will hold max val
        $low_lng_w = 9999999; //will hold max val
        $low_lat_s = 9999999; //will hold max val

        /* filter for high and lows*/
        foreach ($coords_objects as $coords) {
            foreach($coords as $k=>$v)
            {
                if($v->lng > $high_lng_e)
                {
                    $high_lng_e = $v->lng;
                }
                if($v->lng < $low_lng_w)
                {
                    $low_lng_w = $v->lng;
                }
                if($v->lat > $high_lat_n)
                {
                    $high_lat_n = $v->lat;
                }
                if($v->lat < $low_lat_s)
                {
                    $low_lat_s = $v->lat;
                }
            }
        }
        print ' | n : '. $high_lat_n;
        print ' | s : '. $low_lat_s;
        print ' | e : '. $high_lng_e;
        print ' | w : '. $low_lng_w;


        // calculate centers
        $lng_size = $high_lng_e - $low_lng_w;
        $half_lng_difference = $lng_size / 2;
        $center_lng = $high_lng_e - $half_lng_difference;
        print ' | lng size: '.$lng_size ;

        $lat_size = $high_lat_n - $low_lat_s;
        $half_lat_difference = $lat_size / 2;
        $center_lat = $high_lat_n - $half_lat_difference;
        print ' | lat size: '.$lat_size ;

        // get zoom level
        if($lat_size > 3 || $lng_size > 3) {
            $zoom = 6;
        } elseif ($lat_size > 2 || $lng_size > 2) {
            $zoom = 7;
        } elseif ($lat_size > 1 || $lng_size > 1) {
            $zoom = 8;
        } elseif ($lat_size > .4 || $lng_size > .4) {
            $zoom = 9;
        } elseif ($lat_size > .2 || $lng_size > .2) {
            $zoom = 10;
        } elseif ($lat_size > .1 || $lng_size > .1) {
            $zoom = 11;
        } elseif ($lat_size > .07 || $lng_size > .07) {
            $zoom = 12;
        } elseif ($lat_size > .01 || $lng_size > .01) {
            $zoom = 13;
        } else {
            $zoom = 14;
        }

        print ' | zoom: '.$zoom ;

        $meta = array("center_lng" => (float)$center_lng,"center_lat" => (float)$center_lat,"ne" => $high_lat_n.','.$high_lng_e,"sw" => $low_lat_s.','.$low_lng_w ,"zoom" => (float)$zoom);

        /*********************************************/



        ?>
        <div id="search-response"></div>
        <style>
            /* Always set the map height explicitly to define the size of the div
        * element that contains the map. */
            #map {
                height: 450px;;
                width: 100%;
                /*max-width:1000px;*/
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

                var zoom = <?php echo $meta['zoom']; ?>;

                var map = new google.maps.Map(document.getElementById('map'), {
                    zoom: zoom,
                    center: {lat: <?php echo $meta['center_lat']; ?>, lng: <?php echo $meta['center_lng']; ?>},
                    mapTypeId: 'terrain'
                });

                // Define the LatLng coordinates for the polygon's path.
                var coords = [ <?php
                                $rows = count($result);
                                $i = 0;
                                foreach($result as $value) {
                                   echo $value->meta_value;
                                   if($rows > $i + 1) {echo ','; }
                                   $i++;
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
                    var restURL = '<?php echo get_rest_url(null, '/dt/v1/locations/gettractmap'); ?>';
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