<?php

/**
 * KML File Update Class
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Disciple_Tools_Upload {

    /**
     * Constructor function.
     * @access  public
     * @since   0.1
     */
    public function __construct () {} // End __construct()

    /**
     * Uploads US Census Tract KML file to Locations Post Type
     * @return boolean
     */
    public static function upload_census_tract_kml_to_post_type ($file) {

        // test if file exists
        if(!file_exists($file))
            return false;

        // test if locations post type exists
        if(!post_type_exists( 'locations' ))
            return false;

        // parse xml information and build post
        $kml_object = simplexml_load_file( $file );

        foreach ($kml_object->Document->Folder->Placemark as $place) {
            $value = '';

            if($place->Polygon) {

                $value .= $place->Polygon->outerBoundaryIs->LinearRing->coordinates;

            } elseif ($place->MultiGeometry) {

                foreach($place->MultiGeometry->Polygon as $polygon) {

                    $value .= $polygon->outerBoundaryIs->LinearRing->coordinates;

                }
            }

            $value_array = substr(trim($value), 0, -2); // remove trailing ,0 so as not to create an empty array
            $value_array = explode(',0.0 ', $value_array); // create array from coordinates string

            /*************************************************************
             * Create JSON format coordinates. Display in Google Map
             */
            $coordinates = '';
            $last_lat = '';
            $last_lng = '';

            foreach ($value_array as $va) {
                if(!empty($va)) {
                    $coord = explode(',', $va);
                    $coordinates .= '{lat: '.$coord[1]. ', lng: ' . $coord[0] . '},';
                    $last_lng = $coord[0];
                    $last_lat = $coord[1];
                }
            }
            $coordinates = substr(trim($coordinates), 0, -1);

            $post = array(
                "post_title" => $place->ExtendedData->SchemaData->SimpleData[4],
                'post_type' => 'locations',
                "post_content" => $coordinates,
                "post_status" => "publish",
                "post_author" => get_current_user_id(),
                "meta_input"    => array(
                    "STATEFP"   => $place->ExtendedData->SchemaData->SimpleData[0].'',
                    "COUNTYFP"   => $place->ExtendedData->SchemaData->SimpleData[1].'',
                    "TRACTCE"   => $place->ExtendedData->SchemaData->SimpleData[2].'',
                    "AFFGEOID"   => $place->ExtendedData->SchemaData->SimpleData[3].'',
                    "GEOID"   => $place->ExtendedData->SchemaData->SimpleData[4].'',
                    "NAME"   => $place->ExtendedData->SchemaData->SimpleData[5].'',
                    "ALAND"   => $place->ExtendedData->SchemaData->SimpleData[7].'',
                    "last_lng"   => $last_lng,
                    "last_lat"   => $last_lat,
                    "coordinates"   => $coordinates,
                )
            );

            wp_insert_post($post);

        }

        return true;
    }


    /**
     * Uploads KML file to Custom Table
     * @return boolean
     */
    public static function upload_kml_to_custom ($file) {

        // test if file exists
        if(!file_exists($file))
            return false;

        // test if locations post type exists
        if(!post_type_exists( 'locations' ))
            return false;

        // parse xml information and build post
        $kml_object = simplexml_load_file( $file );

        foreach ($kml_object->Document->Folder->Placemark as $place) {
            $value = '';

            if($place->Polygon) {


                $value .= $place->Polygon->outerBoundaryIs->LinearRing->coordinates;

            } elseif ($place->MultiGeometry) {

                foreach($place->MultiGeometry->Polygon as $polygon) {

                    $value .= $polygon->outerBoundaryIs->LinearRing->coordinates;

                }
            }

            $value_array = substr(trim($value), 0, -2); // remove trailing ,0 so as not to create an empty array
            $value_array = explode(',0.0 ', $value_array); // create array from coordinates string

            /*************************************************************
             * Create JSON format coordinates. Display in Google Map
             */
            $coordinates = '';
            $last_lat = '';
            $last_lng = '';

            foreach ($value_array as $va) {
                if(!empty($va)) {
                    $coord = explode(',', $va);
                    $coordinates .= '{lat: '.$coord[1]. ', lng: ' . $coord[0] . '},';
                    $last_lng = $coord[0];
                    $last_lat = $coord[1];
                }
            }
            $coordinates = substr(trim($coordinates), 0, -1);

            $post = array(
                "post_title" => $place->ExtendedData->SchemaData->SimpleData[4],
                'post_type' => 'locations',
                "post_content" => $coordinates,
                "post_status" => "publish",
                "post_author" => get_current_user_id(),
                "meta_input"    => array(
                    "STATEFP"   => $place->ExtendedData->SchemaData->SimpleData[0].'',
                    "COUNTYFP"   => $place->ExtendedData->SchemaData->SimpleData[1].'',
                    "TRACTCE"   => $place->ExtendedData->SchemaData->SimpleData[2].'',
                    "AFFGEOID"   => $place->ExtendedData->SchemaData->SimpleData[3].'',
                    "GEOID"   => $place->ExtendedData->SchemaData->SimpleData[4].'',
                    "NAME"   => $place->ExtendedData->SchemaData->SimpleData[5].'',
                    "ALAND"   => $place->ExtendedData->SchemaData->SimpleData[7].'',
                    "last_lng"   => $last_lng,
                    "last_lat"   => $last_lat,
                    "coordinates"   => $coordinates,
                )
            );

            wp_insert_post($post);

        }

        return true;
    }

    /**
     * The box for deleting locations
     * @return string
     */
    public function delete_locations_box () {
        // check if $_POST to change option
        $status = '';

        if(!empty($_POST['delete_location']) && isset($_POST['delete_location']) && wp_verify_nonce( $_POST['delete_location'], 'delete_location_validate' )) {
            $status =  $this->delete_locations ();
        }

        // return form and dropdown
        $html = '';
        $html .= '<table class="widefat striped">
                    <thead><th>Delete All Locations</th></thead>
                    <tbody>
                        <tr>
                            <td><form action="" method="POST">' . wp_nonce_field( 'delete_location_validate', 'delete_location', true, false ) . '<button type="submit" class="button" value="submit">Delete All Locations Immediately</button>'.$status.'</form></td>
                        </tr>
                    </tbody>
                </table>
        ';
        return $html;
    }

    /**
     * Delete all locations in database
     * @return string
     */
    public function delete_locations () {
        global $wpdb;

        $args = array(
            'numberposts'   => -1,
            'post_type'   => 'locations',
        );

        $locations = get_posts( $args );
        foreach ($locations as $location) {
            $id = $location->ID;
            wp_delete_post( $id, true );
        }

        $wpdb->get_results("DELETE FROM $wpdb->postmeta WHERE NOT EXISTS (SELECT NULL FROM $wpdb->posts WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id)");
        return 'Locations deleted';
    }
}