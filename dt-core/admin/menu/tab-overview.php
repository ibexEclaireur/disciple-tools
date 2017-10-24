<?php

/**
 * Disciple_Tools_Landing_Tab
 *
 * @class      Disciple_Tools_Landing_Tab
 * @since      0.1.0
 * @package    Disciple_Tools
 * @author     Chasm.Solutions & Kingdom.Training
 */

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Class Disciple_Tools_Landing_Tab
 */
class Disciple_Tools_Overview_Tab
{
    /**
     * Packages and prints tab page
     */
    public function content()
    {
        echo '<div class="wrap"><div id="poststuff"><div id="post-body" class="metabox-holder columns-2">';
        echo '<div id="post-body-content">';
        /* Main Column */

        /* Box */
        echo '<table class="widefat striped">
                    <thead><th>Getting Started</th></thead>
                    <tbody><tr><td>';


        echo '</td></tr></tbody></table><br>';
        /* End Box */

        /* Box */
        echo '<table class="widefat striped">
                    <thead><th>Configurations</th></thead>
                    <tbody><tr><td>';


        echo '</td></tr></tbody></table><br>';
        /* End Box */

        /* Box */
        echo '<table class="widefat striped">
                    <thead><th>Help</th></thead>
                    <tbody><tr><td>';


        echo '</td></tr></tbody></table>';
        /* End Box */

        /* End Main Column */
        echo '</div><!-- end post-body-content --><div id="postbox-container-1" class="postbox-container">';
        /* Right Column */

        /* Box */
        echo '<table class="widefat striped">
                    <thead><th>Instructions</th></thead>
                    <tbody><tr><td>';

        echo '</td></tr></tbody></table>';
        /* End Box */

        /* End Right Column*/
        echo '</div><!-- postbox-container 1 --><div id="postbox-container-2" class="postbox-container">';
        echo '</div><!-- postbox-container 2 --></div><!-- post-body meta box container --></div><!--poststuff end --></div><!-- wrap end -->';
    }



}
