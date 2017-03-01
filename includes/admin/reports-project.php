<?php

/**
 * Disciple_Tools_Project_Reports
 *
 * @class Disciple_Tools_Project_Reports
 * @version	0.1
 * @since 0.1
 * @package	Disciple_Tools
 * @author Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Disciple_Tools_Project_Reports {

//    private $page;

    /**
     * Disciple_Tools_Project_Reports The single instance of Disciple_Tools_Project_Reports.
     * @var 	object
     * @access  private
     * @since 	0.1
     */
    private static $_instance = null;

    /**
     * Main Disciple_Tools_Project_Reports Instance
     *
     * Ensures only one instance of Disciple_Tools_Project_Reports is loaded or can be loaded.
     *
     * @since 0.1
     * @static
     * @return Disciple_Tools_Project_Reports instance
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
        $this->page = new Disciple_Tools_Page_Factory('index.php',__('Project Report','disciple_tools'),__('Project Report','disciple_tools'), 'manage_options','project_report' );

        add_action('add_meta_boxes', array($this, 'page_metaboxes') );
    } // End __construct()


    //Add some metaboxes to the page
    public function page_metaboxes(){

        add_meta_box('example1','Example 1', array($this, 'dt_example_metabox'),'dashboard_page_project_report','normal','high');
        add_meta_box('example2','Example 2', array($this, 'dt_example_metabox'),'dashboard_page_project_report','side','high');
        add_meta_box('example3','Example 3', array($this, 'dt_example_metabox'),'dashboard_page_project_report','side','low');
    }

    //Define the insides of the metabox
    public function dt_example_metabox(){
        ?>
        <p> An example of a metabox <p>
        <?php

    }

}