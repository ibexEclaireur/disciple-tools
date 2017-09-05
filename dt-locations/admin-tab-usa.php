<?php

/**
 * Disciple_Tools_Locations_Tab_USA
 *
 * @class   Disciple_Tools_Locations_Tab_USA
 * @version 0.1
 * @since   0.1
 * @package Disciple_Tools_Locations_Tab_USA
 * @author  Chasm.Solutions
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

class Disciple_Tools_Locations_Tab_USA {
    
    public function install_us_county() {
        return '<form method="post"> <input type="text" name="county" class="text-small" /> <button type="submit" class="button">Install US County</button></form>'; // auto search field
    }
    
    public function process_install_us_county( $county_id ) {
        return $county_id;
    }
}
