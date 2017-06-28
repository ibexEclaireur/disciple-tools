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

class Disciple_Tools_Coordinates_KML {

    /**
     * Get coordinates from KML file
     * @param $state
     * @param $geoid
     * @return string
     */
    public static function get_tract_kml_coordinates ($geoid, $state ) {

        $file = dt_get_file_path_by_key ($state);

        $kml_object = simplexml_load_file($file);

        $value = '';

        foreach ($kml_object->Document->Folder->Placemark as $mark) {
            $element_geoid = $mark->ExtendedData->SchemaData->SimpleData[4];

            if ($element_geoid == $geoid) { // FILTER RETURN TO TRACT NUMBER

                if ($mark->Polygon) {
                    $value .= $mark->Polygon->outerBoundaryIs->LinearRing->coordinates;
                } elseif ($mark->MultiGeometry) {
                    foreach ($mark->MultiGeometry->Polygon as $polygon) {
                        $value .= $polygon->outerBoundaryIs->LinearRing->coordinates;
                    }
                }
            }
        }

        $value_array = substr(trim($value), 0, -2); // remove trailing ,0 so as not to create an empty array
        $value_array = explode(',0.0 ', $value_array); // create array from coordinates string

        /*************************************************************
         * Create JSON format coordinates. Display in Google Map
         */
        $coordinates = array();
        foreach ($value_array as $va) {
            if (!empty($va)) {
                $coord = explode(',', $va);
                $coordinates[] = array('lat' => (float)$coord[1], 'lng' => (float)$coord[0]);
            }
        }
        return $coordinates;
    }

    /**
     * Get coordinates from entire state
     * @param $state
     * @return string
     */
    public static function get_tract_kml_state ($state) {

        $file = dt_get_file_path_by_key ($state);

        $kml_object = simplexml_load_file($file);

        $sections = array();

        foreach ($kml_object->Document->Folder->Placemark as $mark) {
            $value = '';

            if ($mark->Polygon) {
                $value .= $mark->Polygon->outerBoundaryIs->LinearRing->coordinates;
            } elseif ($mark->MultiGeometry) {
                foreach ($mark->MultiGeometry->Polygon as $polygon) {
                    $value .= $polygon->outerBoundaryIs->LinearRing->coordinates;
                }
            }

            $value_array = substr(trim($value), 0, -2); // remove trailing ,0 so as not to create an empty array
            $value_array = explode(',0.0 ', $value_array); // create array from coordinates string

            /*************************************************************
             * Create JSON format coordinates. Display in Google Map
             */
            $coordinates = array();
            foreach ($value_array as $va) {
                if (!empty($va)) {
                    $coord = explode(',', $va);
                    $coordinates[] = array('lat' => (float)$coord[1], 'lng' => (float)$coord[0]);
                }
            }
            $sections[] = $coordinates;
        }

        return $sections;
    }

}