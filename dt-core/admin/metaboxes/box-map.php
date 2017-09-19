<?php

/**
 * Disciple Tools
 *
 * @class   Disciple_Tools_
 * @version 0.1
 * @since   0.1
 * @package Disciple_Tools
 * @author  Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly


function dt_map_metabox () {
    $object = new Disciple_Tools_Metabox_Map();
    return $object;
}

class Disciple_Tools_Metabox_Map {


    public $post_type;

    /**
     * Constructor function.
     *
     * @access public
     * @since  0.1
     */
    public function __construct () {

    } // End __construct()
    
    
    /**
     * Load map metabox
     */
    public function display_single_map () {
        global $wpdb, $post;
        
        // get coordinates for county
        $results = $wpdb->get_results( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = '$post->ID' AND (meta_key = 'coordinates' OR meta_key = 'Cen_x' OR meta_key = 'Cen_y')" );
        
        $meta = [];
        foreach($results as $result) {
            $meta[$result->meta_key] = $result->meta_value;
        }
        
        
        if(!empty( $meta )) {
            ?>
            
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
            <div id="map"></div>
            <script type="text/javascript">
                
                jQuery(document).ready(function () {
                    
                    var zoom = 7;
                    
                    var map = new google.maps.Map(document.getElementById('map'), {
                        zoom: zoom,
                        center: {lat: <?php echo $meta['Cen_y']; ?>, lng: <?php echo $meta['Cen_x']; ?>},
                        mapTypeId: 'terrain'
                    });
    
//                    // TODO getting invalid geojson result from movement_mapping
//                    map.data.loadGeoJson(
//                        'http://en/wp-json/mm/v1/install/getcountrybylevel?cnty_id=ABW&level=0');
                    
                    // Define the LatLng coordinates for the polygon's path.
//                    var coords = <?php //echo $meta['coordinates'] ?>//;
//
//                    var tracts = [];
//
//                    for (i = 0; i < coords.length; i++) {
//                        tracts.push(new google.maps.Polygon({
//                            paths: coords[i],
//                            strokeColor: '#FF0000',
//                            strokeOpacity: 0.5,
//                            strokeWeight: 2,
//                            fillColor: '',
//                            fillOpacity: 0.2
//                        }));
//
//                        tracts[i].setMap(map);
//                    }
                
                });
            </script>
            <script
                src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCcddCscCo-Uyfa3HJQVe0JdBaMCORA9eY">
            </script>
            
            <?php
        } else {
            echo '<p>No map info available</p>';
        } // end if no results
        
        
    }
    
    /**
     * Load map metabox
     */
    public function display_map () {
        global $wpdb, $post;

        // get coordinates for county
        $result = $wpdb->get_results( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = '$post->ID' AND meta_key LIKE 'polygon_$post->post_content_filtered%'" );

        if(count( $result ) > 0) {

            // build subsection
            $html = '';
            $html .= '<p><select name="select_tract" id="select_tract">';
            $html .= '<option value="all">Select Subsection</option>';
            foreach ($result as $value) {
                $html .= '<option value="' . substr( $value->meta_key, 8 ) . '">' . substr( $value->meta_key, 8 ) . '</option>';
            }
            $html .= '</select>';
            $html .= ' <a href="javascript:location.reload();">show all</a>';
            $html .= ' <span id="spinner"></span></p>';

            echo $html;

            $meta = dt_get_coordinates_meta( $post->post_content_filtered );


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
            <div id="map"></div>
            <script type="text/javascript">

                jQuery(document).ready(function () {

                    var zoom = <?php echo $meta['zoom']; ?>;

                    var map = new google.maps.Map(document.getElementById('map'), {
                        zoom: zoom,
                        center: {lat: <?php echo $meta['center_lat']; ?>, lng: <?php echo $meta['center_lng']; ?>},
                        mapTypeId: 'terrain'
                    });

                    // Define the LatLng coordinates for the polygon's path.
                    var coords = [ <?php
                        $rows = count( $result );
                        $i = 0;
                    foreach ($result as $value) {
                        echo $value->meta_value;
                        if ($rows > $i + 1) {
                            echo ',';
                        }
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

                    jQuery('#select_tract').change(function () {
                        jQuery('#spinner').prepend('<img src="<?php echo Disciple_Tools()->plugin_img_url; ?>spinner.svg" style="height:30px;" />');

                        var tract = jQuery('#select_tract').val();
                        var restURL = '<?php echo get_rest_url( null, '/dt/v1/locations/getmapbygeoid' ); ?>';
                        jQuery.post(restURL, {geoid: tract})
                            .done(function (data) {
                                jQuery('#spinner').html('');

                                var map = new google.maps.Map(document.getElementById('map'), {
                                    zoom: data.zoom,
                                    center: {lng: data.lng, lat: data.lat},
                                    mapTypeId: 'terrain'
                                });

                                // Define the LatLng coordinates for the polygon's path.
                                var coords = [data.coordinates];

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
        } else {
            echo '<p>No map info available</p>';
        } // end if no results


    }
}
