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

class Disciple_Tools_Locations_Stats {
    
    /**
     * Page content for the tab
     */
    public function page_contents() {
        $html = '';
    
        $html .= '<div class="wrap"><h2>Stats</h2>'; // Block title
    
        $html .= '<div class="wrap"><div id="poststuff"><div id="post-body" class="metabox-holder columns-2">';
        $html .= '<div id="post-body-content">';
        $html .= $this->sync_4k_data();
    
        //        $result =  json_decode( file_get_contents( plugin_dir_path( __FILE__ ) . 'json/usa-counties.json' ) );
        //        foreach ($result as $location) {
        //            echo $location->STATEAB . ',' . $location->STATE . ',' . $location->COUNTY . ',' . strtoupper(substr($location->COUNTY_NAME, 0, 3)) . ',' . strtoupper(substr($location->COUNTY_NAME, 0, 2)) . substr($location->COUNTY, -1) .  '<br>';
        //        }
    
    
        $html .= '</div><!-- end post-body-content --><div id="postbox-container-1" class="postbox-container">';
        $html .= '<br>'; /* Add content to column */
    
        $html .= '</div><!-- postbox-container 1 --><div id="postbox-container-2" class="postbox-container">';
        $html .= '';/* Add content to column */
    
        $html .= '</div><!-- postbox-container 2 --></div><!-- post-body meta box container --></div><!--poststuff end --></div><!-- wrap end -->';
    
        return $html;
        
    }
    
    public function sync_4k_data () {
        global $wpdb;
        $html = '';
    
        if ( !empty( $_POST[ 'oz_nonce' ] ) && isset( $_POST[ 'oz_nonce' ] ) && wp_verify_nonce( $_POST[ 'oz_nonce' ], 'oz_nonce_validate' ) ) {
    
            if ( !empty( $_POST[ 'sync-4k' ] ) ) {
        
                // https://services1.arcgis.com/DnZ5orhsUGGdUZ3h/ArcGIS/rest/services/OmegaZones082016/FeatureServer/query?layerDefs={"0":"CntyID="}&returnGeometry=true&f=pjson
                $result =  json_decode( file_get_contents( 'https://services1.arcgis.com/DnZ5orhsUGGdUZ3h/ArcGIS/rest/services/OmegaZones082016/FeatureServer/query?layerDefs={"0":"CntyID=\''.$_POST[ 'sync-4k' ].'\'"}&returnGeometry=true&f=pjson' ) );
                
//                print '<pre>'; print_r($result->layers[0]->features); print '</pre>';
                
                // build a parsing loop
                foreach($result->layers[0]->features as $item) {
//                    print $item->attributes->OBJECTID_1 . '<br>';
//                    print $item->attributes->OBJECTID . '<br>';
//                    print $item->attributes->WorldID . '<br>';
//                    print $item->attributes->Zone_Name . '<br>';
//                    print $item->attributes->World . '<br>';
//                    print $item->attributes->Adm4ID . '<br>';
//                    print $item->attributes->Adm3ID . '<br>';
//                    print $item->attributes->Adm2ID . '<br>';
//                    print $item->attributes->Adm1ID . '<br>';
//                    print $item->attributes->CntyID . '<br>';
//                    print $item->attributes->Adm4_Name . '<br>';
//                    print $item->attributes->Adm3_Name . '<br>';
//                    print $item->attributes->Adm2_Name . '<br>';
//                    print $item->attributes->Adm1_Name . '<br>';
//                    print $item->attributes->Cnty_Name . '<br>';
//                    print $item->attributes->Population . '<br>';
//                    print $item->attributes->Shape_Leng . '<br>';
//                    print $item->attributes->Cen_x . '<br>';
//                    print $item->attributes->Cen_y . '<br>';
//                    print $item->attributes->Region . '<br>';
//                    print $item->attributes->Field . '<br>';
//                    print '<pre>'; print_r($item->geometry->rings); print '</pre>';
//                    print json_encode($item->geometry->rings);
                    
                    
                    // insert/update megazone table
                    $wpdb->update(
                        'omegazone_v1',
                        array(
                            'OBJECTID_1' => $item->attributes->OBJECTID_1,
                            'OBJECTID' => $item->attributes->OBJECTID,
                            'WorldID' => $item->attributes->WorldID,
                            'Zone_Name' => $item->attributes->Zone_Name,
                            'World' => $item->attributes->World,
                            'Adm4ID' => $item->attributes->Adm4ID,
                            'Adm3ID' => $item->attributes->Adm3ID,
                            'Adm2ID' => $item->attributes->Adm2ID,
                            'Adm1ID' => $item->attributes->Adm1ID,
                            'CntyID' => $item->attributes->CntyID,
                            'Adm4_Name' => $item->attributes->Adm4_Name,
                            'Adm3_Name' => $item->attributes->Adm3_Name,
                            'Adm2_Name' => $item->attributes->Adm2_Name,
                            'Adm1_Name' => $item->attributes->Adm1_Name,
                            'Cnty_Name' => $item->attributes->Cnty_Name,
                            'Population' => $item->attributes->Population,
                            'Shape_Leng' => $item->attributes->Shape_Leng,
                            'Cen_x' => $item->attributes->Cen_x,
                            'Cen_y' => $item->attributes->Cen_y,
                            'Region' => $item->attributes->Region,
                            'Field' => $item->attributes->Field,
                            'geometry' => json_encode($item->geometry->rings),
                        ),
                        array( 'WorldID' => $item->attributes->WorldID ),
                        array(
                            '%d',
                            '%d',
                            '%s',
                            '%s',
                            '%s',
                            '%s',
                            '%s',
                            '%s',
                            '%s',
                            '%s',
                            '%s',
                            '%s',
                            '%s',
                            '%s',
                            '%s',
                            '%d',
                            '%f',
                            '%f',
                            '%f',
                            '%s',
                            '%s',
                            '%s',
                        )
                    );
                    
                    print '<br><br>Records updated: ' . $wpdb->rows_affected . ' | ' . $item->attributes->Cnty_Name;
                }
    
                
        
            }
        }
    
        $dir_contents =  dt_get_oz_country_list();
    
        $admin1 = '<select name="sync-4k" class="regular-text">';
        $admin1 .= '<option >- Choose</option>';
    
        foreach ( $dir_contents as $value ) {
            
                $admin1 .= '<option value="' . $value->CntyID . '" ';
                $admin1 .= '>' . $value->Cnty_Name;
                $admin1 .= '</option>';
            
        }
    
        $admin1 .= '</select>';
        /* End load dropdown */
    
        $html .= '<table class="widefat ">
                    <thead><th>Sync 4K Data</th></thead>
                    <tbody>
                        <tr>
                            <td>
                                <form action="" method="POST">
                                    ' . wp_nonce_field( 'oz_nonce_validate', 'oz_nonce', true, false ) . $admin1 . '
                                    
                                    <button type="submit" class="button" value="submit">Upload State</button>
                                </form>
                            </td>
                        </tr>';
        $html .= '</tbody>
                </table>';
    
        return $html;
    }
    
    
}
