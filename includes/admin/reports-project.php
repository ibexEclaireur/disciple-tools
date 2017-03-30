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
        $this->page = new Disciple_Tools_Page_Factory('index.php',__('Project Stats','disciple_tools'),__('Project Stats','disciple_tools'), 'read','project_report' );

        add_action('add_meta_boxes', array($this, 'page_metaboxes') );
    } // End __construct()


    //Add some metaboxes to the page
    public function page_metaboxes(){
        add_meta_box('system_stats','System Stats', array($this, 'system_stats_widget'),'dashboard_page_project_report','normal','high');
        add_meta_box('page_notes','Notes', array($this, 'page_notes'),'dashboard_page_project_report','side','high');
    }

    /**
     * System stats dashboard widget
     *
     * @since 0.1
     * @access public
     */
    public function system_stats_widget (  ) {

        // Build counters
        $system_users = count_users();
        $dispatchers = $system_users['avail_roles']['dispatcher'];
        $marketers = $system_users['avail_roles']['marketer'];
        $multipliers = $system_users['avail_roles']['multiplier'];
        $multiplier_leader = $system_users['avail_roles']['multiplier_leader'];
        $prayer_supporters = $system_users['avail_roles']['prayer_supporter'];
        $project_supporters = $system_users['avail_roles']['project_supporter'];
        $registered = $system_users['avail_roles']['registered'];

        $monitored_websites = 'x';
        $monitored_facebook_pages = 'x';

        $comments = wp_count_comments();
        $comments = $comments->total_comments;

        $comments_for_dispatcher = 'x';

        // Build HTML of widget
        $html = '
			<table class="widefat striped ">
						<thead>
							<tr>
								<th>Name</th>
								<th>Progress</th>
								
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>System Users</td>
								<td>'. $system_users['total_users'] .'</td>
							</tr>
							<tr>
								<td>Dispatchers</td>
								<td>'. $dispatchers .'</td>
							</tr>
							<tr>
								<td>Marketers</td>
								<td>'. $marketers .'</td>
							</tr>
							<tr>
								<td>Multipliers</td>
								<td>'. $multipliers .'</td>
							</tr>
							<tr>
								<td>Multiplier Leaders</td>
								<td>'. $multiplier_leader .'</td>
							</tr>
							<tr>
								<td>Prayer Supporters</td>
								<td>'. $prayer_supporters .'</td>
							</tr>
							<tr>
								<td>Project Supporters</td>
								<td>'. $project_supporters .'</td>
							</tr>
							<tr>
								<td>Registered</td>
								<td>'. $registered .'</td>
							</tr>
							<tr>
								<td>Monitored Websites</td>
								<td>'. $monitored_websites .'</td>
							</tr>
							<tr>
								<td>Monitored Facebook</td>
								<td>'. $monitored_facebook_pages .'</td>
							</tr>
							<tr>
								<td>Comments</td>
								<td>'. $comments .'</td>
							</tr>
							<tr>
								<td>Comments for @dispatcher</td>
								<td>'. $comments_for_dispatcher .'</td>
							</tr>
						</tbody>
					</table>
			';
        echo $html;
    }

    public function page_notes () {
        $html = '
            <p>Project stats summarizes the people and activities within the disciple making movement project.</p>
            <ul>
                <li>
                    Project statistics covers...
                </li>
                <li>
                    System statistics covers counts of features inside the Disciple Tools system.
                </li>
            </ul>
        ';
        echo $html;
    }



}