<?php

/**
 * Disciple_Tools_Tabs
 *
 * @class   Disciple_Tools_Tabs
 * @version 0.1
 * @since   0.1
 * @package Disciple_Tools_Tabs
 * @author  Chasm.Solutions
 */

if( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Class Disciple_Tools_Google_Geolocation
 */
class Disciple_Tools_Google_Geolocation
{

    /**
     * Constructor function.
     *
     * @access public
     * @since  0.1
     */
    public function __construct()
    {

    } // End __construct()

    /**
     * @param $address          string   Can be an address or a geolocation lat, lng
     * @param $type             string      Default is 'full_object', which returns full google response, 'coordinates only' returns array with coordinates_only
     *                          and 'core' returns an array of the core information elements of the google response.
     *
     * @return array|mixed|object|bool
     */
    public static function query_google_api( $address, $type = 'full_object' )
    {

        $address = str_replace( '   ', ' ', $address );
        $address = str_replace( '  ', ' ', $address );
        $address = urlencode( trim( $address ) );

        /*************************************************************
         * Chasm Google API Key AIzaSyBxUvKYE0LMTbz0VOtPxfRqHXWFyVqlF2I
         *
         * @see https://developers.google.com/maps/documentation/geocoding/start
         */
        $url_address = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $address . '&key=AIzaSyBxUvKYE0LMTbz0VOtPxfRqHXWFyVqlF2I';
        $details = json_decode( self::url_get_contents( $url_address ) );

        if( $details->status == 'ZERO_RESULTS' ) {
            return 'ZERO_RESULTS';
        }

        if( $type == 'coordinates_only' ) {

            $g_lat = $details->results[ 0 ]->geometry->location->lat;
            $g_lng = $details->results[ 0 ]->geometry->location->lng;

            return [ 'lng' => $g_lng, 'lat' => $g_lat ];
        } elseif( $type == 'core' ) {
            $g_lat = $details->results[ 0 ]->geometry->location->lat;
            $g_lng = $details->results[ 0 ]->geometry->location->lng;
            $g_formatted_address = $details->results[ 0 ]->formatted_address;

            return [ 'lng' => $g_lng, 'lat' => $g_lat, 'formatted_address' => $g_formatted_address ];
        } else {
            return $details; // full_object returned
        }
    }

    /**
     * @param $Url
     *
     * @return mixed
     */
    public static function url_get_contents( $url )
    {
        if( !function_exists( 'curl_init' ) ) {
            die( 'CURL is not installed!' );
        }
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $output = curl_exec( $ch );
        curl_close( $ch );

        return $output;
    }

}
