<?php
/**
 * Counts Misc Groups and Church numbers
 *
 * @package Disciple_Tools
 * @author  Chasm Solutions <chasm.crew@chasm.solutions>
 * @license GPL-3.0
 * @version 0.1.0
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

/**
 * Class Disciple_Tools_Counter_Groups
 */
class Disciple_Tools_Counter_Groups  {

    /**
     * Constructor function.
     *
     * @access public
     * @since  0.1.0
     */
    public function __construct() { } // End __construct()

    /**
     * Counts the number of active churches in the database
     */
    public function active_churches() {
        return true; // TODO actually data.
    }

}
