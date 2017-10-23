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
     * @param $post
     * @param $location_address
     *
     * @return mixed
     */
    public function install_google_coordinates( $post, $location_address ) {

        global $post;

        $query_object = Disciple_Tools_Google_Geolocation::query_google_api( $location_address );

        $location = [];
        $location['lat'] = $query_object->results[0]->geometry->location->lat;
        $location['lng'] = $query_object->results[0]->geometry->location->lng;
        $location['northeast_lat'] = $query_object->results[0]->geometry->bounds->northeast->lat;
        $location['northeast_lng'] = $query_object->results[0]->geometry->bounds->northeast->lng;
        $location['southwest_lat'] = $query_object->results[0]->geometry->bounds->southwest->lat;
        $location['southwest_lng'] = $query_object->results[0]->geometry->bounds->southwest->lng;

        update_post_meta( $post->ID, 'location', $location );
        update_post_meta( $post->ID, 'lat', $location['lat'] );
        update_post_meta( $post->ID, 'lng', $location['lng'] );
        update_post_meta( $post->ID, 'northeast_lat', $location['northeast_lat'] );
        update_post_meta( $post->ID, 'northeast_lng', $location['northeast_lng'] );
        update_post_meta( $post->ID, 'southwest_lat', $location['southwest_lat'] );
        update_post_meta( $post->ID, 'southwest_lng', $location['southwest_lng'] );
        update_post_meta( $post->ID, 'google_coordinates_installed', true );

        return get_post_meta( $post->ID );
    }

    /**
     * Load map metabox
     */
    public function display_location_map()
    {
        global  $post;
        $post_meta = get_post_meta( $post->ID );

        echo '<input type="hidden" name="dt_locations_noonce" id="dt_locations_noonce" value="' . esc_attr( wp_create_nonce( 'update_location_info' ) ) . '" />';
        ?>

        <input type="text" name="location_address" value="<?php isset( $post_meta['location_address'][0] ) ? print esc_attr( $post_meta['location_address'][0] ) : print esc_attr( $post->post_title );  ?>" />
        <button type="submit">Update</button>
        <hr>

        <?php
        $key = 'dt_locations_noonce';
        if ( ( get_post_type() == 'locations' ) || isset( $_POST[ $key ] ) || wp_verify_nonce( sanitize_key( $_POST[ $key ] ), 'update_location_info' ) ) {
            $location_address = isset( $post_meta['location_address'][0] ) ? $post_meta['location_address'][0] . ', ' . $post_meta['Cnty_Name'][0] : $post->post_title . ', ' . $post_meta['Cnty_Name'][0];
            $post_meta = $this->install_google_coordinates( $post, $location_address );
        }

        if ( ! ( isset( $post_meta['google_coordinates_installed'] ) && $post_meta['google_coordinates_installed'] == true ) ) {

            $location_address = isset( $post_meta['location_address'][0] ) ? $post_meta['location_address'][0] : $post->post_title . ', ' . $post_meta['Cnty_Name'][0];
            $post_meta = $this->install_google_coordinates( $post, $location_address );

        }

        $lat = (float) $post_meta['lat'][0];
        $lng = (float) $post_meta['lng'][0];
        $northeast_lat = (float) $post_meta['northeast_lat'][0];
        $northeast_lng = (float) $post_meta['northeast_lng'][0];
        $southwest_lat = (float) $post_meta['southwest_lat'][0];
        $southwest_lng = (float) $post_meta['southwest_lng'][0];

            ?>

            <style>
                /* Always set the map height explicitly to define the size of the div
            * element that contains the map. */
                #map {
                    height: 550px;
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

                    let $mapDiv = jQuery('#map');

                    let centerLat = <?php echo esc_attr( $lat ); ?>;
                    let centerLng = <?php echo esc_attr( $lng ); ?>;
                    let center = new google.maps.LatLng(centerLat, centerLng);

                    let sw = new google.maps.LatLng(<?php echo esc_attr( $southwest_lat ); ?>, <?php echo esc_attr( $southwest_lng ); ?>);
                    let ne = new google.maps.LatLng(<?php echo esc_attr( $northeast_lat ); ?>, <?php echo esc_attr( $northeast_lng ); ?>);
                    let bounds = new google.maps.LatLngBounds(sw, ne);

                    let mapDim = {height: $mapDiv.height(), width: $mapDiv.width()};

                    let zoom = getBoundsZoomLevel(bounds, mapDim);

                    let map = new google.maps.Map(document.getElementById('map'), {
                        zoom: zoom - 3,
                        center: center,
                        mapTypeId: 'terrain'
                    });


                    let marker = new google.maps.Marker({
                        position: center,
                        map: map,
                    });

                    /**
                     * @see https://stackoverflow.com/questions/6048975/google-maps-v3-how-to-calculate-the-zoom-level-for-a-given-bounds
                     * @param bounds
                     * @param mapDim
                     * @returns {number}
                     */
                    function getBoundsZoomLevel(bounds, mapDim) {
                        let WORLD_DIM = { height: 256, width: 256 };
                        let ZOOM_MAX = 21;

                        function latRad(lat) {
                            let sin = Math.sin(lat * Math.PI / 180);
                            let radX2 = Math.log((1 + sin) / (1 - sin)) / 2;
                            return Math.max(Math.min(radX2, Math.PI), -Math.PI) / 2;
                        }

                        function zoom(mapPx, worldPx, fraction) {
                            return Math.floor(Math.log(mapPx / worldPx / fraction) / Math.LN2);
                        }

                        let ne = bounds.getNorthEast();
                        let sw = bounds.getSouthWest();

                        let latFraction = (latRad(ne.lat()) - latRad(sw.lat())) / Math.PI;

                        let lngDiff = ne.lng() - sw.lng();
                        let lngFraction = ((lngDiff < 0) ? (lngDiff + 360) : lngDiff) / 360;

                        let latZoom = zoom(mapDim.height, WORLD_DIM.height, latFraction);
                        let lngZoom = zoom(mapDim.width, WORLD_DIM.width, lngFraction);

                        return Math.min(latZoom, lngZoom, ZOOM_MAX);
                    }
                });

            </script>
            <script
                src="https://maps.googleapis.com/maps/api/js?key=<?php echo esc_attr( dt_get_option( 'map_key' ) ); ?>">
            </script>

            <?php

    }

}
