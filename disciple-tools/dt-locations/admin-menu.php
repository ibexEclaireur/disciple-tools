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
    
//        $html .= $tab_link_pre . 'import' . $tab_link_post;
//        if ($tab == 'import' ) {$html .= 'nav-tab-active';}
//        $html .= '">Temp Import</a>';

        $html .= '</h2>';

        echo $html; // Echo tabs

        $html = '';
        // End Tab Bar

        /**
         * Begin Page Content
         */
        switch ($tab) {

            case "global":
                
                $html .= '<div class="wrap"><div id="poststuff"><div id="post-body" class="metabox-holder columns-2">';
                $html .= '<div id="post-body-content">';
    
                /* BOX */
                $html .= '<table class="widefat striped"><thead><th>Install</th></thead><tbody><tr><td>';
                
                /* Build content of box */
                require_once( 'admin-tab-global.php' );
                $object = new Disciple_Tools_Locations_Tab_Global();
                $object->process_install_country();
                $html .= $object->install_country();
                /* End build */
                
                $html .= '</td></tr></tbody></table>';
    
//                print '<pre>';
//
//                print_r( $_POST );
//                print '<br>';
//                print_r( get_option( '_dt_installed_country' ) );
//
//                print '</pre>';
    
                $html .= '</div><!-- end post-body-content --><div id="postbox-container-1" class="postbox-container">';
    
                /* BOX */
                $html .= '<table class="widefat striped"><thead><th>Source</th></thead><tbody><tr><td>';
                $html .= $this->get_import_config_dropdown( 'mm_hosts' );
                $html .= '</td></tr></tbody></table><br>';
                $html .= $this->locations_currently_installed();
                
                $html .= '</div><!-- postbox-container 1 --><div id="postbox-container-2" class="postbox-container">';
                $html .= '</div><!-- postbox-container 2 --></div><!-- post-body meta box container --></div><!--poststuff end --></div><!-- wrap end -->';
                break;
             
                
            case "usa":
                $html .= '<div class="wrap"><div id="poststuff"><div id="post-body" class="metabox-holder columns-2">';
                $html .= '<div id="post-body-content">';
                
                /* BOX */
                $html .= '<table class="widefat striped"><thead><th>Install by State</th></thead><tbody><tr><td>';
    
                require_once( 'admin-tab-usa.php' );
                $object = new Disciple_Tools_Locations_Tab_USA(); // create object
                $object->process_install_us_state();
                $html .= $object->install_us_state();
                
//                print '<pre>';
//
//                print_r( $_POST );
//                print '<br>';
//                print_r( get_option( '_dt_usa_installed_state' ) );
//
//                print '</pre>';
                
                $html .= '</td></tr></tbody></table><br>';
                $html .= '</div><!-- end post-body-content --><div id="postbox-container-1" class="postbox-container">';
    
                /* BOX */
                $html .= '<table class="widefat striped"><thead><th>Instructions</th></thead><tbody><tr><td>';
                
                
                $html .= '</td></tr></tbody></table><br>';
    
                $html .= $this->usa_states_currently_installed();
                
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
    
    /**
     *
     * @param $host string  Can be either 'kml_hosts' or 'mm_hosts'
     */
    public function get_import_config_dropdown( $host ) {
        // get vars
        $option = $this->get_config_option();
        
        // update from post
        if(isset( $_POST['change_host_source'] )) {
            if (isset( $_POST[$host] )) {
                $option['selected_'.$host] = $_POST[$host];
                update_option( '_dt_locations_import_config', $option, false );
            }
        }
        
        // create dropdown
        $html = '';
        $html .= '<form method="post"><select name="'.$host.'" >';
        foreach ($option[$host] as $key => $value) {
            $html .= '<option value="'.$key.'" ';
            if( $option['selected_'.$host] == $key ) { $html .= ' selected'; }
            $html .= '>' . $key . '</option>';
        }
        $html .= '</select> <button type="submit" name="change_host_source" value="true">Save</button></form>';

        return $html;
    }
    
    public static function get_config_option() {
        $option = get_option( '_dt_locations_import_config' );
        $config = json_decode( file_get_contents( plugin_dir_path( __FILE__ ). 'config.json' ), true );
        // check on option status
        if ( empty( $option ) || $option['version'] < $config['version'] ) { // check if option exists
            update_option( '_dt_locations_import_config', $config, false );
            $option = get_option( '_dt_locations_import_config' );
        }
        
        return $option;
    }
    
    public function locations_currently_installed () {
        global $wpdb;
        $count = [];
        $html = '';
        
        // Search for currently installed locations
        
        $html .= '<table class="widefat ">
                    <thead><th>Currently Installed</th></thead>
                    <tbody>
                        <tr>
                            <td>';
        // Total number of locations in database
        $html .= 'Total number of locations: <br>' . wp_count_posts( 'locations' )->publish . '<br>';
        
        // Total number of countries
        $count['countries'] = $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE post_type = 'locations' AND post_name LIKE '___'" );
        $html .= 'Total number of countries (admin0): <br>' . $count['countries'] . '<br>';
        
        // Total number of admin1
        $count['admin1'] = $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE post_type = 'locations' AND post_name LIKE '___-___'" );
        $html .= 'Total number of Admin1: <br>' . $count['admin1'] . '<br>';
        
        // Total number of admin2
        $count['admin2'] = $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE post_type = 'locations' AND post_name LIKE '___-___-___'" );
        $html .= 'Total number of Admin2: <br>' . $count['admin2'] . '<br>';
        
        // Total number of admin3
        $count['admin3'] = $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE post_type = 'locations' AND post_name LIKE '___-___-___-___'" );
        $html .= 'Total number of Admin3: <br>' . $count['admin3'] . '<br>';
        
        // Total number of admin4
        $count['admin4'] = $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE post_type = 'locations' AND post_name LIKE '___-___-___-___-___'" );
        $html .= 'Total number of Admin4: <br>' . $count['admin4'] . '<br>';
        
        
        $html .= '      </td>
                        </tr>';
        
        $html .= '</tbody>
                </table>';
        
        return $html;
    }
    
    public function usa_states_currently_installed () {
        global $wpdb;
        $count = [];
        $html = '';
        
        // Search for currently installed locations
        
        $html .= '<table class="widefat ">
                    <thead><th>Currently Installed</th></thead>
                    <tbody>
                        <tr>
                            <td>';
        // Total number of locations in database
        $html .= 'Total number of locations: <br>' . wp_count_posts( 'locations' )->publish . '<br>';
        
        // Total number of admin1
        $count['admin1'] = $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE post_type = 'locations' AND post_name LIKE 'USA-___'" );
        $html .= 'Total number of States: <br>' . $count['admin1'] . '<br>';
        
        // Total number of admin2
        $count['admin2'] = $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE post_type = 'locations' AND post_name LIKE 'USA-___-___'" );
        $html .= 'Total number of Counties: <br>' . $count['admin2'] . '<br>';
        
        // Total number of admin3
        $count['admin3'] = $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE post_type = 'locations' AND post_name LIKE 'USA-___-___-%'" );
        $html .= 'Total number of Tracts: <br>' . $count['admin3'] . '<br>';
        
        $html .= '      </td>
                        </tr>';
        
        $html .= '</tbody>
                </table>';
        
        return $html;
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
