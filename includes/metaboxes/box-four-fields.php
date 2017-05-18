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

function dt_four_fields_metabox () {
    $object = new Disciple_Tools_Metabox_Four_Fields();
    return $object;
}

class Disciple_Tools_Metabox_Four_Fields {

    /**
     * Constructor function.
     * @access  public
     * @since   0.1
     */
    public function __construct () {

    } // End __construct()

    public function content_display () {
        global $post;

        // get the current group


        // get all members of the group


        // count members at different stages.


        $html = '<img src="'. Disciple_Tools()->plugin_img . '4fields.png" >';

        return $html;
    }



}