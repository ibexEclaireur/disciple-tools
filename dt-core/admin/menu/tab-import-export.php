<?php

/**
 * Disciple Tools
 *
 * @class      Disciple_Tools_
 * @version    0.1
 * @since      0.1
 * @package    Disciple_Tools
 * @author     Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Disciple_Tools_Import_Export_Tab {
    /**
     * Packages and returns tab page
     *
     * @return string
     */
    public function content() {
        $html = '';
        $html .= '<div class="wrap"><div id="poststuff"><div id="post-body" class="metabox-holder columns-2">';
        $html .= '<div id="post-body-content">';
        /* Main Column */
        
        print '<pre>';
        //        print_r( $_POST );
        //        print_r( dt_get_option( 'dt_site_options' ) );
        print '</pre>';
        
        /* Box */
        $html .= '<table class="widefat striped">
                    <thead><th>Import Disciple Tools Data</th></thead>
                    <tbody><tr><td>';
        
        
        $html .= '</td></tr></tbody></table><br>';
        /* End Box */
        
        
        /* Box */
        $html .= '<table class="widefat striped">
                    <thead><th>Export Disciple Tools Data</th></thead>
                    <tbody><tr><td>';
        
        
        $html .= '</td></tr></tbody></table><br>';
        /* End Box */
        
        /* Box */
        $html .= '<table class="widefat striped">
                    <thead><th>Delete Disciple Tools Data</th></thead>
                    <tbody><tr><td>';
        
        
        $html .= '</td></tr></tbody></table><br>';
        /* End Box */
        
        /* End Main Column */
        $html .= '</div><!-- end post-body-content --><div id="postbox-container-1" class="postbox-container">';
        /* Right Column */
        
        /* Box */
        $html .= '<table class="widefat striped">
                    <thead><th>Instructions</th></thead>
                    <tbody><tr><td>';
        
        
        $html .= '</td></tr></tbody></table><br>';
        /* End Box */
        
        /* End Right Column*/
        $html .= '</div><!-- postbox-container 1 --><div id="postbox-container-2" class="postbox-container">';
        $html .= '</div><!-- postbox-container 2 --></div><!-- post-body meta box container --></div><!--poststuff end --></div><!-- wrap end -->';
        
        return $html;
    }
    
    
}
