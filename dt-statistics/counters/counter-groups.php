<?php
/**
 * Counts Misc Groups and Church numbers
 *
 * @package   Disciple_Tools
 * @author       Chasm Solutions <chasm.crew@chasm.solutions>
 * @link      https://github.com/ChasmSolutions
 * @license   GPL-3.0
 * @version   0.1
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Disciple_Tools_Counter_Groups  {


    /**
     * Constructor function.
     * @access  public
     * @since   0.1
     */
    public function __construct () { } // End __construct()

    /**
     * Counts the number of active churches in the database
     */
    public function active_churches () {
        return true;
    }

}