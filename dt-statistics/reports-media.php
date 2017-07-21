<?php

/**
 * Disciple_Tools_Media_Reports
 *
 * @class   Disciple_Tools_Media_Reports
 * @version 0.1
 * @since   0.1
 * @package Disciple_Tools
 * @author  Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

class Disciple_Tools_Media_Reports {

    //    private $page;

    /**
     * Disciple_Tools_Media_Reports The single instance of Disciple_Tools_Media_Reports.
     *
     * @var    object
     * @access private
     * @since  0.1
     */
    private static $_instance = null;

    /**
     * Main Disciple_Tools_Media_Reports Instance
     *
     * Ensures only one instance of Disciple_Tools_Media_Reports is loaded or can be loaded.
     *
     * @since  0.1
     * @static
     * @return Disciple_Tools_Media_Reports instance
     */
    public static function instance () {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    /**
     * Constructor function.
     *
     * @access public
     * @since  0.1
     */
    public function __construct () {
        // Load Admin menus
        $this->page = new Disciple_Tools_Page_Factory( 'index.php',__( 'Media Stats','disciple_tools' ),__( 'Media Stats','disciple_tools' ), 'read','media_report' );

        add_action( 'add_meta_boxes', [$this, 'page_metaboxes'] );
    } // End __construct()


    //Add some metaboxes to the page
    public function page_metaboxes(){

        add_meta_box( 'content_locations','Content Locations', [$this, 'content_locations_widget'],'dashboard_page_media_report','normal','high' );
        add_meta_box( 'page_notes','Notes', [$this, 'page_notes'],'dashboard_page_media_report','side','high' );
    }


    public function page_notes () {
        $html = '
            <p>The media stats report summarizes the web and social media properties being used by the project.</p>
            <hr>
            <p>Box 1...</p>
            <hr>
            <p>Box 2...</p>
            <hr>
            <p>Box 3...</p>
        ';
        echo $html;
    }

    /**
     * Movement funnel path dashboard widget
     *
     * @since  0.1
     * @access public
     */
    public function content_locations_widget () {



        // Build html
        $html = '
			<table class="widefat striped ">
						<thead>
							<tr>
								<th>Name</th>
								<th>Url</th>
								<th>Launch Date</th>

							</tr>
						</thead>
						<tbody>
							<tr>
								<th>Pray4Colorado</th>
								<td><a href="https://pray4colorado.org">https://www.pray4colorado.org</a></td>
								<td>Jan 1, 2017</td>
							</tr>
							<tr>
								<th>Pray4Colorado</th>
								<td><a href="https://pray4colorado.org">https://www.pray4colorado.org</a></td>
								<td>Jan 1, 2017</td>
							</tr>
							<tr>
								<th>Pray4Colorado</th>
								<td><a href="https://pray4colorado.org">https://www.pray4colorado.org</a></td>
								<td>Jan 1, 2017</td>
							</tr>
							<tr>
								<th>Pray4Colorado</th>
								<td><a href="https://pray4colorado.org">https://www.pray4colorado.org</a></td>
								<td>Jan 1, 2017</td>
							</tr>

						</tbody>
					</table>
			';

        echo $html;
    }

}
