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


function dt_map_metabox () {
    $object = new Disciple_Tools_Metabox_Map();
    return $object;
}

class Disciple_Tools_Metabox_Map {

    /**
     * Constructor function.
     * @access  public
     * @since   0.1
     */
    public function __construct () {

    } // End __construct()

    public function display_map () {
        global $post;



        // sanitize address
        $address = 'Africa';
        $address = str_replace(" ", "+", $address);
        $address = str_replace("'", "+", $address);

        $html = '<iframe
                  width="100%"
                  height="450"
                  frameborder="0" style="border:0"
                  src="https://www.google.com/maps/embed/v1/place?key=AIzaSyAjbfL34WHBjOfB0gjfWcBrvMrsRSS_IP0
                    &q='.$address.'" allowfullscreen>
                </iframe>';
        echo $html;
    }
}