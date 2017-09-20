<?php
/**
 * Contains create, update and delete functions for locations, wrapping access to
 * the database
 *
 * @package  Disciple_Tools
 * @category Plugin
 * @author   Chasm.Solutions & Kingdom.Training
 * @since    0.1
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.


class Disciple_Tools_Locations {

    /**
     * Returns the tract geoid from an address
     *
     * @param  $address
     * @return array
     */
    public static function get_tract_by_address ( $address ) {

        $google_result = Disciple_Tools_Google_Geolocation::query_google_api( $address, $type = 'core' ); // get google api info
        if ($google_result == 'ZERO_RESULTS') {
            return [
                'status' => 'ZERO_RESULTS',
                'tract' => '',
            ];
        }

        $census_result = Disciple_Tools_Census_Geolocation::query_census_api( $google_result['lng'], $google_result['lat'], $type = 'core' ); // get census api data
        if ($census_result == 'ZERO_RESULTS') {
            return [
                'status' => 'ZERO_RESULTS',
                'tract' => '',
            ];
        }

        return [
          'status' => 'OK',
            'tract' => $census_result['geoid'],
        ];
    }

    /**
     * Returns the all the array elements needed for an address to tract map search
     *
     * @param  $address
     * @return array
     */
    public static function get_tract_map ( $address ) {

        // Google API
        $google_result = Disciple_Tools_Google_Geolocation::query_google_api( $address, $type = 'core' ); // get google api info
        if ($google_result == 'ZERO_RESULTS') {
            return [
                'status' => 'ZERO_RESULTS',
                'message' => 'Failed google geolocation lookup.',
            ];
        }
        $lng = $google_result['lng'];
        $lat = $google_result['lat'];
        $formatted_address = $google_result['formatted_address'];

        // Census API
        $census_result = Disciple_Tools_Census_Geolocation::query_census_api( $lng, $lat, $type = 'core' ); // get census api data
        if ($census_result == 'ZERO_RESULTS') {
            return [
                'status' => 'ZERO_RESULTS',
                'message' => 'Failed getting census data',
            ];
        }
        $geoid = $census_result['geoid'];
        $zoom = $census_result['zoom'];
        $state = $census_result['state'];
        $county = $census_result['county'];

        // Boundary data
        $coordinates = Disciple_Tools_Coordinates_DB::get_db_coordinates( $geoid ); // return coordinates from database

        return [
            'status' => 'OK',
            'zoom' => $zoom,
            'lng'   =>  $lng,
            'lat'   =>  $lat,
            'formatted_address' => $formatted_address,
            'geoid' => $geoid,
            'coordinates' => $coordinates,
            'state' =>  $state,
            'county'    => $county,
        ];
    }

    /**
     * Returns the all the array elements needed for an address to tract map search
     *
     * @param  $params
     * @return array
     */
    public static function get_map_by_geoid ( $params ) {

        $geoid = $params['geoid'];

        // Boundary data
        $coordinates = Disciple_Tools_Coordinates_DB::get_db_coordinates( $geoid ); // return coordinates from database
        $meta = dt_get_coordinates_meta( $geoid ); // returns an array of meta

        return [
            'status' => 'OK',
            'zoom' => $meta['zoom'],
            'lng'   =>  (float) $meta['center_lng'],
            'lat'   =>  (float) $meta['center_lat'],
            'geoid' => $geoid,
            'coordinates' => $coordinates,
            'state' =>  substr( $geoid, 0, 1 ),
        ];
    }

    public static function get_locations (){
//        @todo check permisions
        $query_args = array(
            'post_type' => 'locations',
            'orderby' => 'ID',
            'nopaging' => true,
        );
        $query = new WP_Query( $query_args );
        return $query->posts;
    }


    public static function get_locations_compact ( $search ){
//        @todo check permisions
        $query_args = array(
            'post_type' => 'locations',
            'orderby' => 'ID',
            's' => $search
        );
        $query = new WP_Query( $query_args );
        $list = [];
        foreach ($query->posts as $post){
            $list[] = ["ID" => $post->ID, "name" => $post->post_title];
        }
        return $list;
    }
    
    
}
