<?php
/**
 * Contains create, update and delete functions for locations, wrapping access to
 * the database
 *
 * @package  Disciple_Tools
 * @author   Chasm.Solutions & Kingdom.Training
 * @since    1.0.0
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly.

/**
 * Class Disciple_Tools_Locations
 */
class Disciple_Tools_Locations
{

    /**
     * Get all locations in database
     *
     * @return array|WP_Error
     */
    public static function get_locations()
    {
        if ( ! current_user_can( 'read_location' ) ) {
            return new WP_Error( __FUNCTION__, __( "No permissions to read locations" ), [ 'status' => 403 ] );
        }

        $query_args = [
            'post_type' => 'locations',
            'orderby'   => 'ID',
            'nopaging'  => true,
        ];
        $query = new WP_Query( $query_args );

        return $query->posts;
    }

    /**
     * @param $search
     *
     * @return array|WP_Error
     */
    public static function get_locations_compact( $search )
    {
        if ( !current_user_can( 'read_location' )){
            return new WP_Error( __FUNCTION__, __( "No permissions to read locations" ), [ 'status' => 403 ] );
        }
        $query_args = [
            'post_type' => 'locations',
            'orderby'   => 'ID',
            's'         => $search,
        ];
        $query = new WP_Query( $query_args );
        $list = [];
        foreach ( $query->posts as $post ) {
            $list[] = [ "ID" => $post->ID, "name" => $post->post_title ];
        }

        return $list;
    }

    /**
     * Gets a count for the different levels of 4K locations
     *
     * @param string $level
     *
     * @return int|null|string
     */
    public static function get_4k_location_count( $level = 'all' )
    {
        global $wpdb;

        switch ( $level ) {
            case 'all':
                $count = 0;
                $count = $count + $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE post_type = 'locations' AND post_name LIKE '___'" );
                $count = $count + $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE post_type = 'locations' AND post_name LIKE '___-___'" );
                $count = $count + $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE post_type = 'locations' AND post_name LIKE '___-___-___'" );
                $count = $count + $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE post_type = 'locations' AND post_name LIKE '___-___-___-___'" );
                $count = $count + $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE post_type = 'locations' AND post_name LIKE '___-___-___-___-___'" );
                return $count;
                break;
            case '0':
                return $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE post_type = 'locations' AND post_name LIKE '___'" );
                break;
            case '1':
                return $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE post_type = 'locations' AND post_name LIKE '___-___'" );
                break;
            case '2':
                return $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE post_type = 'locations' AND post_name LIKE '___-___-___'" );
                break;
            case '3':
                return $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE post_type = 'locations' AND post_name LIKE '___-___-___-___'" );
                break;
            case '4':
                return $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE post_type = 'locations' AND post_name LIKE '___-___-___-___-___'" );
                break;
            default:
                return 0;
                break;
        }
    }

    /**
     * Returns the tract geoid from an address
     * Zume Project USA
     *
     * @param  $address
     *
     * @return array
     */
    public static function geocode_address( $address, $type = 'full_object' )
    {

        $google_result = Disciple_Tools_Google_Geolocation::query_google_api( $address, $type ); // get google api info
        if ( $google_result == 'ZERO_RESULTS' ) {
            return [
                'status' => false,
                'message'  => 'Zero Results for Location',
            ];
        }

        return [
            'status' => true,
            'results'  => $google_result,
        ];
    }

    /**
     * Returns the tract geoid from an address
     * Zume Project USA
     *
     * @param  $address
     *
     * @return array
     */
    public static function get_tract_by_address( $address )
    {

        $google_result = Disciple_Tools_Google_Geolocation::query_google_api( $address, $type = 'core' ); // get google api info
        if ( $google_result == 'ZERO_RESULTS' ) {
            return [
                'status' => 'ZERO_RESULTS',
                'tract'  => '',
            ];
        }

        $census_result = Disciple_Tools_Census_Geolocation::query_census_api( $google_result['lng'], $google_result['lat'], $type = 'core' ); // get census api data
        if ( $census_result == 'ZERO_RESULTS' ) {
            return [
                'status' => 'ZERO_RESULTS',
                'tract'  => '',
            ];
        }

        return [
            'status' => 'OK',
            'tract'  => $census_result['geoid'],
        ];
    }

    /**
     * Returns the all the array elements needed for an address to tract map search
     * Zume Project USA
     *
     * @param  $address
     *
     * @return array
     */
    public static function get_tract_map( $address )
    {

        // Google API
        $google_result = Disciple_Tools_Google_Geolocation::query_google_api( $address, $type = 'core' ); // get google api info
        if ( $google_result == 'ZERO_RESULTS' ) {
            return [
                'status'  => 'ZERO_RESULTS',
                'message' => 'Failed google geolocation lookup.',
            ];
        }
        $lng = $google_result['lng'];
        $lat = $google_result['lat'];
        $formatted_address = $google_result['formatted_address'];

        // Census API
        $census_result = Disciple_Tools_Census_Geolocation::query_census_api( $lng, $lat, $type = 'core' ); // get census api data
        if ( $census_result == 'ZERO_RESULTS' ) {
            return [
                'status'  => 'ZERO_RESULTS',
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
            'status'            => 'OK',
            'zoom'              => $zoom,
            'lng'               => $lng,
            'lat'               => $lat,
            'formatted_address' => $formatted_address,
            'geoid'             => $geoid,
            'coordinates'       => $coordinates,
            'state'             => $state,
            'county'            => $county,
        ];
    }

    /**
     * Returns the all the array elements needed for an address to tract map search
     * Zume Project
     *
     * @param  $params
     *
     * @return array
     */
    public static function get_map_by_geoid( $params )
    {

        $geoid = $params['geoid'];

        // Boundary data
        $coordinates = Disciple_Tools_Coordinates_DB::get_db_coordinates( $geoid ); // return coordinates from database
        $meta = dt_get_coordinates_meta( $geoid ); // returns an array of meta

        return [
            'status'      => 'OK',
            'zoom'        => $meta['zoom'],
            'lng'         => (float) $meta['center_lng'],
            'lat'         => (float) $meta['center_lat'],
            'geoid'       => $geoid,
            'coordinates' => $coordinates,
            'state'       => substr( $geoid, 0, 1 ),
        ];
    }

}
