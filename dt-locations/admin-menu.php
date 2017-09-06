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

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

class Disciple_Tools_Location_Tools_Menu {

    public $path;

    /**
     * Disciple_Tools The single instance of Disciple_Tools.
     *
     * @var    object
     * @access private
     * @since  0.1
     */
    private static $_instance = null;

    /**
     * Main Disciple_Tools_Tabs Instance
     *
     * Ensures only one instance of Disciple_Tools_Tabs is loaded or can be loaded.
     *
     * @since  0.1
     * @static
     * @see    Disciple_Tools()
     * @return Disciple_Tools_Location_Tools_Menu instance
     */
    public static function instance () {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    /**
     * Constructor function.
     *
     * @access public
     * @since  0.1
     */
    public function __construct () {
        $this->path  = plugin_dir_path( __DIR__ );
        add_action( 'admin_menu', [ $this, 'load_admin_menu_item' ] );
    } // End __construct()

    /**
     * Load Admin menu into Settings
     */
    public function load_admin_menu_item () {
        add_submenu_page( 'edit.php?post_type=locations', __( 'Import', 'disciple_tools' ), __( 'Import', 'disciple_tools' ), 'manage_options', 'disciple_tools_locations', [ $this, 'page_content' ] );
    }

    /**
     * Builds the tab bar
     *
     * @since 0.1
     */
    public function page_content() {


        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }

        /**
         * Begin Header & Tab Bar
         */
        if (isset( $_GET["tab"] )) {$tab = $_GET["tab"];
        } else {$tab = 'global';}

        $tab_link_pre = '<a href="edit.php?post_type=locations&page=disciple_tools_locations&tab=';
        $tab_link_post = '" class="nav-tab ';

        $html = '<div class="wrap">
            <h2>Import Locations</h2>
            <h2 class="nav-tab-wrapper">';

        $html .= $tab_link_pre . 'global' . $tab_link_post;
        if ($tab == 'global' ) {$html .= 'nav-tab-active';}
        $html .= '">Global</a>';
        
        $html .= $tab_link_pre . 'usa' . $tab_link_post;
        if ($tab == 'usa' ) {$html .= 'nav-tab-active';}
        $html .= '">USA</a>';
    
        $html .= $tab_link_pre . 'import' . $tab_link_post;
        if ($tab == 'import' ) {$html .= 'nav-tab-active';}
        $html .= '">Temp Import</a>';

        $html .= '</h2>';

        echo $html; // Echo tabs

        $html = '';
        // End Tab Bar

        /**
         * Begin Page Content
         */
        switch ($tab) {

            case "global":
                require_once( 'admin-tab-global.php' );
                $object = new Disciple_Tools_Locations_Tab_Global();
                
                $html .= '<div class="wrap"><div id="poststuff"><div id="post-body" class="metabox-holder columns-2">';
                $html .= '<div id="post-body-content">';
    
                /* BOX */
                $html .= '<table class="widefat striped"><thead><th>Global</th></thead><tbody><tr><td>';
                $object->process_install_country();
                $html .= $object->install_country();
                
                $html .= '</td></tr></tbody></table>';
                print_r($_POST);
                print '<br>'; print_r(get_option( '_dt_installed_country' ));
                
                $html .= '</div><!-- end post-body-content --><div id="postbox-container-1" class="postbox-container">';
    
                /* BOX */
                $html .= '<table class="widefat striped"><thead><th>Instructions</th></thead><tbody><tr><td>';
                /*first column*/
                $html .= '</td></tr><tr><td>';
                /*second column*/
                $html .= '</td></tr></tbody></table>';
                
                $html .= '</div><!-- postbox-container 1 --><div id="postbox-container-2" class="postbox-container">';
                $html .= '</div><!-- postbox-container 2 --></div><!-- post-body meta box container --></div><!--poststuff end --></div><!-- wrap end -->';
                break;
             
                
            case "usa":
                require_once( 'admin-tab-usa.php' );
                $object = new Disciple_Tools_Locations_Tab_USA(); // create object
                
                $html .= '<div class="wrap"><div id="poststuff"><div id="post-body" class="metabox-holder columns-2">';
                $html .= '<div id="post-body-content">';
                
                
    
                /* BOX */
                $html .= '<table class="widefat striped"><thead><th>Install by State</th></thead><tbody><tr><td>';
    
                $object->process_install_us_state();
                $html .= $object->install_us_state();
                
//                print_r($_POST);
//                print '<br>'; print_r(get_option( '_dt_usa_installed_state' ));
                
                
                $html .= '</td></tr></tbody></table><br>';
    
                $html .= '</div><!-- end post-body-content --><div id="postbox-container-1" class="postbox-container">';
    
                /* BOX */
                $html .= '<table class="widefat striped"><thead><th>Instructions</th></thead><tbody><tr><td>';
                /*first column*/
                $html .= '</td></tr><tr><td>';
                /*second column*/
                $html .= '</td></tr></tbody></table>';
                
                $html .= '</div><!-- postbox-container 1 --><div id="postbox-container-2" class="postbox-container">';
                $html .= '</div><!-- postbox-container 2 --></div><!-- post-body meta box container --></div><!--poststuff end --></div><!-- wrap end -->';
                break;
            
                
            case 'import':
                require_once( 'admin-tab-import.php' );
                $content = new Disciple_Tools_Locations_Tab_Import();
                $html .= $content->page_contents();
                break;
            default:
                break;
        }

        $html .= '</div>'; // end div class wrap

        echo $html; // Echo contents
    }
}



/**
 * Creates a dropdown of the states with the state key as the value.
 * @usage USA locations
 *
 * @return string
 */
function dt_get_states_key_dropdown_not_installed () {
    
    $dir_contents = dt_get_usa_meta();
    
    $dropdown = '<select name="states-dropdown">';
    
    foreach ($dir_contents->USA_states as $value) {
        $disabled = '';
        
        $dropdown .= '<option value="' . $value->key . '" ';
        if (get_option( '_installed_us_county_'.$value->key )) {$dropdown .= ' disabled';
            $disabled = ' (Installed)';}
        elseif (isset( $_POST['states-dropdown'] ) && $_POST['states-dropdown'] == $value->key) {$dropdown .= ' selected';}
        $dropdown .= '>' . $value->name . $disabled;
        $dropdown .= '</option>';
    }
    $dropdown .= '</select>';
    
    return $dropdown;
}



/**
 * Creates a dropdown of the states with the state key as the value.
 * @usage USA locations
 *
 * @return string
 */
function dt_get_states_key_dropdown_installed () {
    
    $dir_contents = dt_get_usa_meta(); // get directory & build dropdown
    
    $dropdown = '<select name="states-dropdown">';
    
    foreach ($dir_contents->USA_states as $value) {
        $disabled = '';
        
        $dropdown .= '<option value="' . $value->key . '" ';
        if (!get_option( '_installed_us_county_'.$value->key )) {$dropdown .= ' disabled';
            $disabled = ' (Not Installed)';}
        elseif (isset( $_POST['states-dropdown'] ) && $_POST['states-dropdown'] == $value->key) {$dropdown .= ' selected';}
        $dropdown .= '>' . $value->name . $disabled;
        $dropdown .= '</option>';
    }
    $dropdown .= '</select>';
    
    return $dropdown;
}


/**
 * Get the master json file with USA states and counties names, ids, and file locations.
 * @usage USA locations
 *
 * @return array|mixed|object
 */
function dt_get_usa_meta() {
    return json_decode( file_get_contents( plugin_dir_path( __FILE__ ) . 'json/usa-meta.json' ) );
}



/**
 * Get the master list of countries for omega zones including country abbreviation, country name, and zone.
 * @return array|mixed|object
 */
function dt_get_oz_country_list( $admin = 'cnty' ) {
    
    switch ( $admin ) {
        case 'cnty':
            $result =  json_decode( file_get_contents( plugin_dir_path( __FILE__ ) . 'json/oz/oz_cnty.json' ) );
            return $result->RECORDS;
            break;
        case 'admin1':
            $result =  json_decode( file_get_contents( plugin_dir_path( __FILE__ ) . 'json/oz/oz_admin1.json' ) );
            return $result->RECORDS;
            break;
        case 'admin2':
            $result =  json_decode( file_get_contents( plugin_dir_path( __FILE__ ) . 'json/oz/oz_admin2.json' ) );
            return $result->RECORDS;
            break;
        case 'admin3':
            $result =  json_decode( file_get_contents( plugin_dir_path( __FILE__ ) . 'json/oz/oz_admin3.json' ) );
            return $result->RECORDS;
            break;
        case 'admin4':
            $result =  json_decode( file_get_contents( plugin_dir_path( __FILE__ ) . 'json/oz/oz_admin4.json' ) );
            return $result->RECORDS;
            break;
        default:
            break;
    }
    
    return false;
}


/**
 * Gets the meta information for a polygon or array of polygons
 * @usage USA locations
 *
 * @param  $geoid        (int) Can be full 9 digit geoid or 5 digit state/county code
 * @return array
 */
function dt_get_coordinates_meta ( $geoid ) {
    global $wpdb;
    
    //* query */
    $county_coords = $wpdb->get_results( "SELECT meta_value FROM $wpdb->postmeta WHERE meta_key LIKE 'polygon_$geoid%'", ARRAY_A );
    
    /* build full json of coodinates*/
    $rows = count( $county_coords );
    $string = '[';
    $i = 0;
    foreach($county_coords as $value) {
        $string .= $value['meta_value'];
        if($rows > $i + 1 ) {$string .= ','; }
        $i++;
    }
    $string .= ']';
    
    $coords_objects = json_decode( $string );
    
    /* set values */
    $high_lng_e = -9999999; //will hold max val
    $high_lat_n = -9999999; //will hold max val
    $low_lng_w = 9999999; //will hold max val
    $low_lat_s = 9999999; //will hold max val
    
    /* filter for high and lows*/
    foreach ($coords_objects as $coords) {
        foreach($coords as $k=>$v)
        {
            if($v->lng > $high_lng_e)
            {
                $high_lng_e = $v->lng;
            }
            if($v->lng < $low_lng_w)
            {
                $low_lng_w = $v->lng;
            }
            if($v->lat > $high_lat_n)
            {
                $high_lat_n = $v->lat;
            }
            if($v->lat < $low_lat_s)
            {
                $low_lat_s = $v->lat;
            }
        }
    }
    //    print ' | n : '. $high_lat_n;
    //    print ' | s : '. $low_lat_s;
    //    print ' | e : '. $high_lng_e;
    //    print ' | w : '. $low_lng_w;
    
    
    // calculate centers
    $lng_size = $high_lng_e - $low_lng_w;
    $half_lng_difference = $lng_size / 2;
    $center_lng = $high_lng_e - $half_lng_difference;
    //    print ' | lng size: '.$lng_size ;
    
    $lat_size = $high_lat_n - $low_lat_s;
    $half_lat_difference = $lat_size / 2;
    $center_lat = $high_lat_n - $half_lat_difference;
    //    print ' | lat size: '.$lat_size ;
    
    // get zoom level
    if($lat_size > 3 || $lng_size > 3) {
        $zoom = 6;
    } elseif ($lat_size > 2 || $lng_size > 2) {
        $zoom = 7;
    } elseif ($lat_size > 1 || $lng_size > 1) {
        $zoom = 8;
    } elseif ($lat_size > .4 || $lng_size > .4) {
        $zoom = 9;
    } elseif ($lat_size > .2 || $lng_size > .2) {
        $zoom = 10;
    } elseif ($lat_size > .1 || $lng_size > .1) {
        $zoom = 11;
    } elseif ($lat_size > .07 || $lng_size > .07) {
        $zoom = 12;
    } elseif ($lat_size > .01 || $lng_size > .01) {
        $zoom = 13;
    } else {
        $zoom = 14;
    }
    
    //    print ' | zoom: '.$zoom ;
    
    $meta = ["center_lng" => (float) $center_lng,"center_lat" => (float) $center_lat,"ne" => $high_lat_n.','.$high_lng_e,"sw" => $low_lat_s.','.$low_lng_w ,"zoom" => (float) $zoom];
    
    return $meta;
}

/**
 * Get the full country name from key
 * @param $key
 * @return mixed
 */
function dt_locations_match_country_to_key( $key ) {
    
    $countries = dt_get_oz_country_list();
    
    foreach($countries as $country) {
        if($country->CntyID == $key) {
            return $country->Cnty_Name;
        }
    }
    
    return false;
}
