<?php

/**
 * Disciple Tools - Availability Meta Box
 *
 * @class   Disciple_Tools_Metabox_Availability
 * @version 0.1
 * @since   0.1
 * @package Disciple_Tools
 * @author  Chasm.Solutions & Kingdom.Training
 */

if( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly
/**
 * @return \Disciple_Tools_Metabox_Availability
 */
function dt_availability_metabox()
{
    $object = new Disciple_Tools_Metabox_Availability();

    return $object;
}

/**
 * Class Disciple_Tools_Metabox_Availability
 */
class Disciple_Tools_Metabox_Availability
{

    /**
     * Constructor function.
     *
     * @access public
     * @since  0.1
     */
    public function __construct()
    {

    } // End __construct()

    /**
     * @return mixed
     */
    public function display_availability_box()
    {
        $this->availability_style(); // prints
        $this->availability_grid(); // prints
    }

    /**
     * @return void
     */
    public function availability_style()
    {
        ?>
        <style>
            #feedback {
                font-size: 1.2em;
            }

            .selectable .ui-selecting {
                background: #FECA40;
            }

            .selectable_header .ui-selecting {
                background: #FECA40;
            }

            .selectable .ui-selected {
                background: #F39814;
                color: white;
            }

            .selectable_header .ui-selected {
                background: #F39814;
                color: white;
            }

            .selectable {
                list-style-type: none;
                margin: 0;
                padding: 0;
                width: 100%;
            }

            .selectable_header {
                list-style-type: none;
                margin: 0;
                padding: 0;
                width: 100%;
            }

            .selectable li {
                margin: 3px;
                padding: 1px;
                float: left;
                width: 13%;
                height: 100px;
                font-size: 4em;
                text-align: center;
            }

            .selectable_header li {
                margin: 3px;
                padding: 1px;
                float: left;
                width: 13%;
                height: 100px;
                font-size: 4em;
                text-align: center;
            }
        </style>
        <?php
    }

    /**
     * @return string
     */
    public function availability_grid()
    {
        ?>
            <div class="row">
                <div class="small-12 column">
                    <h2>Availability</h2>

                    <ol class="selectable_header">
                      <li class="ui-state-default">S</li>
                      <li class="ui-state-default">M</li>
                      <li class="ui-state-default">T</li>
                      <li class="ui-state-default">W</li>
                      <li class="ui-state-default">T</li>
                      <li class="ui-state-default">F</li>
                      <li class="ui-state-default">S</li>
                    </ol>
                    <ol class="selectable">
                      <li class="ui-state-default" id="1-morning">M</li>
                      <li class="ui-state-default">M</li>
                      <li class="ui-state-default">M</li>
                      <li class="ui-state-default">M</li>
                      <li class="ui-state-default">M</li>
                      <li class="ui-state-default">M</li>
                      <li class="ui-state-default">M</li>

                      <li class="ui-state-default">N</li>
                      <li class="ui-state-default">N</li>
                      <li class="ui-state-default">N</li>
                      <li class="ui-state-default">N</li>
                      <li class="ui-state-default">N</li>
                      <li class="ui-state-default">N</li>
                      <li class="ui-state-default">N</li>

                      <li class="ui-state-default">E</li>
                      <li class="ui-state-default">E</li>
                      <li class="ui-state-default">E</li>
                      <li class="ui-state-default">E</li>
                      <li class="ui-state-default">E</li>
                      <li class="ui-state-default">E</li>
                      <li class="ui-state-default">E</li>

                      <li class="ui-state-default">N</li>
                      <li class="ui-state-default">N</li>
                      <li class="ui-state-default">N</li>
                      <li class="ui-state-default">N</li>
                      <li class="ui-state-default">N</li>
                      <li class="ui-state-default">N</li>
                      <li class="ui-state-default">N</li>
                    </ol>

                </div>
            </div>
            <span id="select-result">none</span>

            <script>
            jQuery(document).ready(function($) {
                jQuery( ".selectable" ).selectable({
                    stop: function() {
                        var result = $( "#select-result" ).empty();
                        $( ".ui-selected", this ).each(function() {
                          var index = $( ".selectable li" ).index( this );
                          result.append( " #" + ( index + 1 ) );
                        });
                      }
                });
            });

            </script>

        <?php
    }

    // end class
}
