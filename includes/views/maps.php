<?php
/**
 * Dtools Maps
 *
 * @uses DTools_Function_Callback
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Dtools_Maps
{
    public static function first_map($param)
    {
        return 'This is a map from a class::staticfunction ' . $param;
    }

    public static function portal_maps_page()
    {
        return 'This is a chart from a class::staticfunction by portal_maps_page ';
    }
}