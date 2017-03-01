<?php

/**
 * Disciple_Tools_Media_Reports
 *
 * @class Disciple_Tools_Media_Reports
 * @version	0.1
 * @since 0.1
 * @package	Disciple_Tools
 * @author Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Disciple_Tools_Media_Reports {

//    private $page;

    /**
     * Disciple_Tools_Media_Reports The single instance of Disciple_Tools_Media_Reports.
     * @var 	object
     * @access  private
     * @since 	0.1
     */
    private static $_instance = null;

    /**
     * Main Disciple_Tools_Media_Reports Instance
     *
     * Ensures only one instance of Disciple_Tools_Media_Reports is loaded or can be loaded.
     *
     * @since 0.1
     * @static
     * @return Disciple_Tools_Media_Reports instance
     */
    public static function instance () {
        if ( is_null( self::$_instance ) )
            self::$_instance = new self();
        return self::$_instance;
    } // End instance()

    /**
     * Constructor function.
     * @access  public
     * @since   0.1
     */
    public function __construct () {
        // Load Admin menus
        require_once('class-page-factory.php');
        $this->page = new Disciple_Tools_Page_Factory('index.php',__('Media Report','disciple_tools'),__('Media Report','disciple_tools'), 'manage_options','media_report' );

        add_action('add_meta_boxes', array($this, 'page_metaboxes') );
    } // End __construct()


    //Add some metaboxes to the page
    public function page_metaboxes(){

        add_meta_box('example1','Example 1', array($this, 'example_metabox'),'dashboard_page_media_report','normal','high');
        add_meta_box('example2','Example 2', array($this, 'example_metabox'),'dashboard_page_media_report','side','high');
        add_meta_box('example3','Example 3', array($this, 'example_metabox'),'dashboard_page_media_report','side','low');
    }

    //Define the insides of the metabox
    public function example_metabox(){
        ?>
        <p> An example of a metabox <p>
        <?php

    }

}