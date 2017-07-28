<?php
/**
 * Disciple_Tools_People_Groups_Tab_Import
 *
 * @class   Disciple_Tools_People_Groups_Tab_Import
 * @version 0.1
 * @since   0.1
 * @package Disciple_Tools
 * @author  Chasm.Solutions
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly
class Disciple_Tools_People_Groups_Tab_Import {
    /**
     * Constructor function.
     *
     * @access public
     * @since  0.1
     */
    public function __construct () {
    } // End __construct()
    /**
     * Page content for the tab
     */
    public function page_contents() {
        $html = '';
        $html .= '<div class="wrap"><h2>Import</h2>'; // Block title
        $html .= '<div class="wrap"><div id="poststuff"><div id="post-body" class="metabox-holder columns-2">';
        $html .= '<div id="post-body-content">';
        $html .= '<br>'; /* Add content to column */
        $html .= '</div><!-- end post-body-content --><div id="postbox-container-1" class="postbox-container">';
        $html .= '<br>'; /* Add content to column */
        $html .= '</div><!-- postbox-container 1 --><div id="postbox-container-2" class="postbox-container">';
        $html .= '';/* Add content to column */
        $html .= '</div><!-- postbox-container 2 --></div><!-- post-body meta box container --></div><!--poststuff end --></div><!-- wrap end -->';
        return $html;
    }
}
