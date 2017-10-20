<?php

/**
 * Disciple Tools
 *
 * @class   Disciple_Tools_
 * @version 1.0.0
 * @since   1.0.0
 * @package Disciple_Tools
 *
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly
/**
 * @return \Disciple_Tools_Metabox_Map
 */
function dt_map_metabox()
{
    $object = new Disciple_Tools_Metabox_Map();

    return $object;
}

/**
 * Class Disciple_Tools_Metabox_Map
 */
class Disciple_Tools_Metabox_Map
{

    public $post_type;

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
     * Load map metabox
     */
    public function display_single_map()
    {
        global $wpdb, $post;

        // get coordinates for county
        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT
                meta_key, meta_value
            FROM
                `$wpdb->postmeta`
            WHERE
                post_id = %s
                AND (meta_key = 'coordinates' OR meta_key = 'Cen_x' OR meta_key = 'Cen_y')",
            $post->ID
        ) );

        $meta = [];
        foreach ( $results as $result ) {
            $meta[ $result->meta_key ] = $result->meta_value;
        }

        if ( !empty( $meta ) ) {
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
                        center: {lat: <?php echo esc_js( (float) $meta['Cen_y'] ); ?>, lng: <?php echo esc_js( (float) $meta['Cen_x'] ); ?>},
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
                src="https://maps.googleapis.com/maps/api/js?key=<?php echo esc_attr( dt_get_option( 'map_key' ) ); ?>">
            </script>

            <?php
        } else {
            echo '<p>No map info available</p>';
        } // end if no results

    }

    /**
     * Load map metabox
     */
    public function display_map()
    {
        global $wpdb, $post;

        // get coordinates for county
        $result = $wpdb->get_results( $wpdb->prepare(
            "SELECT
                meta_key, meta_value
            FROM
                `$wpdb->postmeta`
            WHERE
                post_id = %s
                AND meta_key LIKE %s",
            $post->ID,
            esc_like( "polygon_$post->post_content_filtered" ) . '%'
        ) );
        if ( count( $result ) > 0 ) {

            // build subsection
            ?>
            <p><select name="select_tract" id="select_tract">
            <option value="all">Select Subsection</option>
            <?php foreach ( $result as $value ): ?>
                <option value="<?php echo esc_attr( substr( $value->meta_key, 8 ) ); ?>"><?php echo esc_html( substr( $value->meta_key, 8 ) ); ?></option>
            <?php endforeach; ?>
            </select>
            <a href="javascript:location.reload();">show all</a>
            <span id="spinner"></span></p>
            <?php

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

                    var zoom = <?php echo intval( $meta['zoom'] ); ?>;

                    var map = new google.maps.Map(document.getElementById('map'), {
                        zoom: zoom,
                        center: {lat: <?php echo esc_js( $meta['center_lat'] ); ?>, lng: <?php echo esc_js( $meta['center_lng'] ); ?>},
                        mapTypeId: 'terrain'
                    });

                    // Define the LatLng coordinates for the polygon's path.
                    var coords = [ <?php
                        $rows = count( $result );
                        $i = 0;
                    foreach ( $result as $value ) {
                        echo esc_js( $value->meta_value );
                        if ( $rows > $i + 1 ) {
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
                        jQuery('#spinner').prepend('<img src="<?php echo esc_url( disciple_tools()->plugin_img_url ); ?>spinner.svg" style="height:30px;" />');

                        var tract = jQuery('#select_tract').val();
                        var restURL = '<?php echo esc_js( get_rest_url( null, '/dt/v1/locations/getmapbygeoid' ) ); ?>';
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
