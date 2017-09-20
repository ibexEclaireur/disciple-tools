<?php
/**
 * Contains create, update and delete functions for people groups, wrapping access to
 * the database
 *
 * @package  Disciple_Tools
 * @category Plugin
 * @author   Chasm.Solutions & Kingdom.Training
 * @since    0.1
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

class Disciple_Tools_People_Groups {
    /**
     * Disciple_Tools_People_Groups The single instance of Disciple_Tools_People_Groups.
     * @var     object
     * @access  private
     * @since     0.1
     */
    private static $_instance = null;
    
    /**
     * Main Disciple_Tools_People_Groups Instance
     *
     * Ensures only one instance of Disciple_Tools_People_Groups is loaded or can be loaded.
     *
     * @since 0.1
     * @static
     * @return Disciple_Tools_People_Groups instance
     */
    public static function instance () {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()
    
    /**
     * Constructor function.
     * @access  public
     * @since   0.1
     */
    public function __construct () {
    
    } // End __construct()
}
