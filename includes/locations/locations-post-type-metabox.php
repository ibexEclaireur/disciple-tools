<?php

/**
 * Locations Post Type Metabox
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Disciple_Tools_Metabox {

    public $post_type;



    /**
     * Constructor function.
     * @access  public
     * @since   0.1
     */
    public function __construct () {
        if(is_admin() ) {
            add_action( 'add_meta_boxes', array( $this, 'meta_box_setup' ), 20 );
            $this->post_type = 'locations';

            add_action( 'admin_init', array($this, 'remove_add_new_submenu') );
        }
    } // End __construct()

    /**
     * Setup the meta box.
     * @access public
     * @since  0.1
     * @return void
     */
    public function meta_box_setup () {
        add_meta_box( $this->post_type . '_data', __( 'Map', 'disciple_tools' ), array( $this, 'load_details_meta_box' ), $this->post_type, 'normal', 'high' );
    } // End meta_box_setup()


    /**
     * Load activity metabox
     */
    public function load_details_meta_box () {
        global $post;
        if(!empty(get_post_meta($post->ID, 'coordinates'))) {
            $coordinates = get_post_meta($post->ID, 'coordinates', true);
            $lat = get_post_meta($post->ID, 'last_lat', true);
            $lng = get_post_meta($post->ID, 'last_lng', true);
            $tract_size = get_post_meta($post->ID, 'ALAND', true);

            $zoom = dt_get_zoom_size_LL ($tract_size); // get zoom level

            ?>
            <h3>Tract : <?php the_title(); ?></h3>
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
                        zoom: <?php print $zoom ?>,
                        center: {lat: <?php print $lat; ?>, lng: <?php print $lng; ?>},
                        mapTypeId: 'terrain'
                    });

                    // Define the LatLng coordinates for the polygon's path.
                    <?php
                    print "var coords = [[";
                    print $coordinates;
                    print "]];";
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

    /**
     * Remove the add new submenu from the locaions menu
     *
     */
    public function remove_add_new_submenu()
    {
        global $submenu;
        unset(
            $submenu['edit.php?post_type=locations'][10]
        );
    }

}