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

class Disciple_Tools_General_Tab {
    
    /**
     * Constructor function.
     * @access  public
     * @since   0.1
     */
    public function __construct () {
        
    } // End __construct()
    
    public function general_options() {
        $html = '';
        $html .= '<div class="wrap"><div id="poststuff"><div id="post-body" class="metabox-holder columns-2">';
        $html .= '<div id="post-body-content">';
        /* Main Column */
    
        /* Box */
        $html .= '<table class="widefat striped">
                    <thead><th>Daily Reports</th></thead>
                    <tbody><tr><td>';
        $html .= $this->options_box();
        $html .= '</td></tr><tr><td>';
        $html .= '</td></tr></tbody></table>';
        /* End Box */
        
        $html .= '<br>';
        
        /* Box */
        $html .= '<table class="widefat striped">
                    <thead><th>General Settings</th></thead>
                    <tbody><tr><td>';
        $html .= $this->options_box();
        $html .= '</td></tr><tr><td>';
        $html .= '</td></tr></tbody></table>';
        /* End Box */
        
        /* End Main Column */
        $html .= '</div><!-- end post-body-content --><div id="postbox-container-1" class="postbox-container">';
        /* Right Column */
    
        /* Box */
        $html .= '<table class="widefat striped">
                    <thead><th>General Settings</th></thead>
                    <tbody><tr><td>';
        $html .= $this->options_box();
        $html .= '</td></tr><tr><td>';
        $html .= '</td></tr></tbody></table>';
        /* End Box */
    
        /* End Right Column*/
        $html .= '</div><!-- postbox-container 1 --><div id="postbox-container-2" class="postbox-container">';
        $html .= '</div><!-- postbox-container 2 --></div><!-- post-body meta box container --></div><!--poststuff end --></div><!-- wrap end -->';
    
        return $html;
    }
    
    public function options_box() {
        $html = 'field';
        
        
        return $html;
    }
    
    
}
