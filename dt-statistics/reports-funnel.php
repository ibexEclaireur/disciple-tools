<?php

/**
 * Disciple_Tools_Funnel_Reports
 *
 * @class Disciple_Tools_Funnel_Reports
 * @version    0.1
 * @since 0.1
 * @package    Disciple_Tools
 * @author Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Disciple_Tools_Funnel_Reports {

//    private $page;

    /**
     * Disciple_Tools_Connections_Reports The single instance of Disciple_Tools_Connections_Reports.
     * @var     object
     * @access  private
     * @since     0.1
     */
    private static $_instance = null;

    /**
     * Main Disciple_Tools_Funnel_Reports Instance
     *
     * Ensures only one instance of Disciple_Tools_Funnel_Reports is loaded or can be loaded.
     *
     * @since 0.1
     * @static
     * @return Disciple_Tools_Funnel_Reports instance
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
        // Build page
        $this->page = new Disciple_Tools_Page_Factory('index.php',__('Funnel Stats','disciple_tools'),__('Funnel Stats','disciple_tools'), 'read','funnel_report' );
        // Build Boxes
        add_action('add_meta_boxes', array($this, 'page_metaboxes') );
    } // End __construct()


    //Add some metaboxes to the page
    public function page_metaboxes(){

        add_meta_box('critical_path_stats','Critical Path', array($this, 'critical_path_stats'),'dashboard_page_funnel_report','normal','high');
        add_meta_box('contact_stats','Contact Stats', array($this, 'contacts_stats_widget'),'dashboard_page_funnel_report','normal','low');
        add_meta_box('groups_stats','Groups Stats', array($this, 'groups_stats_widget'),'dashboard_page_funnel_report','normal','low');
        add_meta_box('baptism_stats','Baptism Stats', array($this, 'baptism_stats_widget'),'dashboard_page_funnel_report','normal','low');
        add_meta_box('page_notes','Notes', array($this, 'page_notes'),'dashboard_page_funnel_report','side','high');
    }

    /**
     * Movement funnel path dashboard widget
     *
     * @since 0.1
     * @access public
     */
    public function critical_path_stats ( ) {
        global $wpdb;

        // Build variables
        $prayer = Disciple_Tools()->report_api->get_meta_key_total('2017', 'Mailchimp', 'new_subscribers');
        $mailchimp_subscribers = Disciple_Tools()->report_api->get_meta_key_total('2017', 'Mailchimp', 'new_subscribers', 'max');
        $facebook = Disciple_Tools()->report_api->get_meta_key_total('2017', 'Facebook', 'page_likes_count');
        $websites = Disciple_Tools()->report_api->get_meta_key_total('2017', 'Analytics', 'unique_website_visitors');

        $new_contacts = Disciple_Tools()->counter->contacts_post_status('publish');
        $contacts_attempted = Disciple_Tools()->counter->contacts_meta_counter('seeker_path', 'Contact Attempted');
        $contacts_established = Disciple_Tools()->counter->contacts_meta_counter('seeker_path', 'Contact Established');
        $first_meetings = Disciple_Tools()->counter->contacts_meta_counter('seeker_path', 'First Meeting Complete');
        $baptisms = Disciple_Tools()->counter->get_baptisms('baptisms');
        $baptizers = Disciple_Tools()->counter->get_baptisms('baptizers');
        $active_churches = Disciple_Tools()->counter->groups_meta_counter('type', 'Church');
        $church_planters = Disciple_Tools()->counter->connection_type_counter('participation', 'Planting');



        // Build html
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
								<td>Prayers Network</td>
								<td>'.$mailchimp_subscribers.'</td>

							</tr>
							<tr>
								<td>Social Engagement</td>
								<td>'.$facebook.'</td>

							</tr>
							<tr>
								<td>Website Visitors</td>
								<td>'.$websites.'</td>

							</tr>
							<tr>
								<td>New Contacts</td>
								<td>'.$new_contacts.'</td>
							</tr>
							<tr>
								<td>Contact Attempted</td>
								<td>'.$contacts_attempted.'</td>
							</tr>
							<tr>
								<td>Contact Established</td>
								<td>'.$contacts_established.'</td>
							</tr>
							<tr>
								<td>First Meeting Complete</td>
								<td>'.$first_meetings.'</td>
							</tr>
							<tr>
								<td>Baptisms</td>
								<td>'.$baptisms.'</td>
							</tr>
							<tr>
								<td>Baptizers</td>
								<td>'.$baptizers.'</td>
							</tr>
							<tr>
								<td>Active Churches</td>
								<td>'.$active_churches.'</td>
							</tr>
							<tr>
								<td>Church Planters</td>
								<td>'.$church_planters.'</td>
							</tr>

						</tbody>
					</table>
			';

        echo $html;
    }

    /**
     * Contacts stats widget
     *
     * @since 0.1
     * @access public
     */
    public function contacts_stats_widget (  ) {

//        print '<pre>'; print_r( Disciple_Tools()->counter->get_generation('generation_list') ); print '</pre>';

        // Build counters
        $has_at_least_1 = Disciple_Tools()->counter->get_generation('has_one_or_more');
        $has_at_least_2 = Disciple_Tools()->counter->get_generation('has_two_or_more');
        $has_more_than_2 = Disciple_Tools()->counter->get_generation('has_three_or_more');

        $has_0 = Disciple_Tools()->counter->get_generation('has_0');
        $has_1 = Disciple_Tools()->counter->get_generation('has_1');
        $has_2 = Disciple_Tools()->counter->get_generation('has_2');
        $has_3 = Disciple_Tools()->counter->get_generation('has_3');

        $con_0gen = '';//Disciple_Tools()->counter->get_generation('at_zero');
        $con_1gen = '';//Disciple_Tools()->counter->get_generation('at_first');
        $con_2gen = '';//Disciple_Tools()->counter->get_generation('at_second');
        $con_3gen = '';//Disciple_Tools()->counter->get_generation('at_third');
        $con_4gen = '';//Disciple_Tools()->counter->get_generation('at_fourth');
        $con_5gen = '';//Disciple_Tools()->counter->get_generation('at_fifth');

        // Build counters
        $contacts_count = Disciple_Tools()->counter->contacts_post_status();
        $unassigned = Disciple_Tools()->counter->contacts_meta_counter('overall_status','unassigned');

        $new_inquirers = Disciple_Tools()->counter->contacts_post_status();
        $assigned_inquirers = Disciple_Tools()->counter->contacts_meta_counter('overall_status','assigned');
        $accepted_inquirers = Disciple_Tools()->counter->contacts_meta_counter('overall_status','accepted');
        $contact_attempted = Disciple_Tools()->counter->contacts_meta_counter('seeker_path','Contact Attempted');
        $contact_established = Disciple_Tools()->counter->contacts_meta_counter('seeker_path','Contact Established');
        $meeting_scheduled = Disciple_Tools()->counter->contacts_meta_counter('seeker_path','Meeting Scheduled');
        $first_meeting_complete = Disciple_Tools()->counter->contacts_meta_counter('seeker_path','First Meeting Complete');
        $ongoing_meetings = Disciple_Tools()->counter->contacts_meta_counter('seeker_path','Ongoing Meetings');



        // Build HTML of widget
        $html = '
			<table class="widefat striped ">
						<thead>
							<tr>
								<th>Name</th>
								<th>Count</th>
								<th>Name</th>
								<th>Count</th>
							</tr>
						</thead>
						<tbody>
						    <tr>
								<td><strong>SEEKER MILESTONES</strong></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td>Published Contacts / New Inquirers</td>
								<td>'. $contacts_count->publish .'</td>
								<td>Contact Established</td>
								<td>'. $contact_established .'</td>
							</tr>
							<tr>
							    <td>Unassigned</td>
								<td>'. $unassigned .'</td>
								<td>Meeting Scheduled</td>
								<td>'. $meeting_scheduled .'</td>
							</tr>
							<tr>
							    <td>Assigned Inquirers</td>
								<td>'. $assigned_inquirers .'</td>
								<td>First Meeting Complete</td>
								<td>'. $first_meeting_complete .'</td>
							</tr>
							<tr>
							    <td>Accepted</td>
								<td>'. $accepted_inquirers .'</td>
							    <td>Ongoing Meetings</td>
								<td>'. $ongoing_meetings .'</td>
							</tr>
							<tr>
							    <td>Contact Attempted</td>
								<td>'. $contact_attempted .'</td>
							    <td></td>
								<td></td>
							</tr>
						    <tr>
								<th><strong>HAS AT LEAST</strong></th>
								<td></td>
								<th><strong>GENERATIONS</strong></th>
								<td></td>
							</tr>
							<tr>
								<td>Has at least 1 disciple</td>
								<td>'. $has_at_least_1 .'</td>
								<td>Zero Gen</td>
								<td>'. $con_0gen .'</td>
							</tr>
							<tr>
								<td>Has at least 2 disciples</td>
								<td>'. $has_at_least_2 .'</td>
								<td>1st Gen</td>
								<td>'. $con_1gen .'</td>
							</tr>
							<tr>
							    <td>Has more than 2 disciples</td>
								<td>'. $has_more_than_2 .'</td>
								<td>2nd Gen</td>
								<td>'. $con_2gen .'</td>
							</tr>
							<tr>
							    <td><strong>HAS</strong></td>
								<td></td>
								<td>3rd Gen</td>
								<td>'. $con_3gen .'</td>
							</tr>
							<tr>
							    <td>Has No Disciples</td>
								<td>'. $has_0 .'</td>
								<td>4th Gen</td>
								<td>'. $con_4gen .'</td>
							</tr>
							<tr>
								<td>Has 1 Disciple</td>
								<td>'. $has_1 .'</td>
								<td>5th Gen</td>
								<td>'. $con_5gen .'</td>
							</tr>
							<tr>
								<td>Has 2 Disciples</td>
								<td>'. $has_2 .'</td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td>Has 3 Disciples</td>
								<td>'. $has_3 .'</td>
								<td></td>
								<td></td>
							</tr>
						</tbody>
					</table>
			';
        echo $html;
    }

    /**
     * Groups stats widget
     *
     * @since 0.1
     * @access public
     */
    public function groups_stats_widget (  ) {

//        print '<pre>'; print_r( Disciple_Tools()->counter->get_generation('generation_list') ); print '</pre>';

        // Build counters
        $has_at_least_1 = Disciple_Tools()->counter->get_generation('has_one_or_more', 'groups');
        $has_at_least_2 = Disciple_Tools()->counter->get_generation('has_two_or_more', 'groups');
        $has_more_than_2 = Disciple_Tools()->counter->get_generation('has_three_or_more', 'groups');

        $has_0 = Disciple_Tools()->counter->get_generation('has_0', 'groups');
        $has_1 = Disciple_Tools()->counter->get_generation('has_1', 'groups');
        $has_2 = Disciple_Tools()->counter->get_generation('has_2', 'groups');
        $has_3 = Disciple_Tools()->counter->get_generation('has_3', 'groups');

        $gr_0gen = '';//Disciple_Tools()->counter->get_generation('at_zero', 'groups');
        $gr_1gen = '';//Disciple_Tools()->counter->get_generation('at_first', 'groups');
        $gr_2gen = '';//Disciple_Tools()->counter->get_generation('at_second', 'groups');
        $gr_3gen = '';//Disciple_Tools()->counter->get_generation('at_third', 'groups');
        $gr_4gen = '';//Disciple_Tools()->counter->get_generation('at_fourth', 'groups');

        $dbs = Disciple_Tools()->counter->groups_meta_counter('type', 'DBS');
        $active_churches = Disciple_Tools()->counter->groups_meta_counter('type', 'Church');


        // Build HTML of widget
        $html = '
			<table class="widefat striped ">
						<thead>
							<tr>
								<th>Name</th>
								<th>Count</th>
								<th>Name</th>
								<th>Count</th>
							</tr>
						</thead>
						<tbody>
						    <tr>
								<th><strong>TOTALS</strong></th>
								<td></td>
								<td><strong>GENERATIONS</strong></td>
								<td></td>
							</tr>
							<tr>
								<td>2x2 or DBS Groups</td>
								<td>'. $dbs .'</td>
								<td>Zero Gen (has no record of being planted by another group)</td>
								<td>'.$gr_0gen.'</td>
							</tr>
							<tr>
								<td>Active Churches</td>
								<td>'. $active_churches .'</td>
								<td>1st Gen</td>
								<td>'.$gr_1gen.'</td>
							</tr>
						    <tr>
								<th><strong>HAS AT LEAST</strong></th>
								<td></td>
								<td>2nd Gen</td>
								<td>'.$gr_2gen.'</td>
							</tr>
							<tr>
								<td>Has planted at least 1 group</td>
								<td>'. $has_at_least_1 .'</td>
								<td>3rd Gen</td>
								<td>'. $gr_3gen .'</td>
							</tr>
							<tr>
								<td>Has planted at least 2 groups</td>
								<td>'. $has_at_least_2 .'</td>
								<td>4th Gen</td>
								<td>'. $gr_4gen .'</td>
							</tr>
							<tr>
								<td>Has planted at least 3 groups</td>
								<td>'. $has_more_than_2 .'</td>
								<td></td>
								<td></td>
							</tr>
						    <tr>
								<td><strong>HAS</strong></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td>Has Not Planted Another Group</td>
								<td>'. $has_0 .'</td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td>Has Planted 1 Group</td>
								<td>'. $has_1 .'</td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td>Has Planted 2 Groups</td>
								<td>'. $has_2 .'</td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td>Has Planted 3 Groups</td>
								<td>'. $has_3 .'</td>
								<td></td>
								<td></td>
							</tr>

						</tbody>
					</table>
			';
        echo $html;
    }

    /**
     * Baptism Generations stats dashboard widget
     *
     * @since 0.1
     * @access public
     */
    public function baptism_stats_widget (  ) {

//        print '<pre>'; print_r( Disciple_Tools()->counter->get_generation('generation_list') ); print '</pre>';

        // Build counters
        $has_at_least_1 = Disciple_Tools()->counter->get_generation('has_one_or_more', 'baptisms');
        $has_at_least_2 = Disciple_Tools()->counter->get_generation('has_two_or_more', 'baptisms');
        $has_more_than_2 = Disciple_Tools()->counter->get_generation('has_three_or_more', 'baptisms');

        $has_0 = Disciple_Tools()->counter->get_generation('has_0', 'baptisms');
        $has_1 = Disciple_Tools()->counter->get_generation('has_1', 'baptisms');
        $has_2 = Disciple_Tools()->counter->get_generation('has_2', 'baptisms');
        $has_3 = Disciple_Tools()->counter->get_generation('has_3', 'baptisms');

        $con_0gen = '';//Disciple_Tools()->counter->get_generation('at_zero', 'baptisms');
        $con_1gen = '';//Disciple_Tools()->counter->get_generation('at_first', 'baptisms');
        $con_2gen = '';//Disciple_Tools()->counter->get_generation('at_second', 'baptisms');
        $con_3gen = '';//Disciple_Tools()->counter->get_generation('at_third', 'baptisms');
        $con_4gen = '';//Disciple_Tools()->counter->get_generation('at_fourth', 'baptisms');
        $con_5gen = '';//Disciple_Tools()->counter->get_generation('at_fifth', 'baptisms');

        $baptisms = Disciple_Tools()->counter->get_baptisms('baptisms');
        $baptizers = Disciple_Tools()->counter->get_baptisms('baptizers');


        // Build HTML of widget
        $html = '
			<table class="widefat striped ">
						<thead>
							<tr>
								<th>Name</th>
								<th>Count</th>

							</tr>
						</thead>
						<tbody>
						    <tr>
								<th><strong>TOTALS</strong></th>
								<td></td>
							</tr>
							<tr>
								<td>Baptisms</td>
								<td>'. $baptisms .'</td>
							</tr>
							<tr>
								<td>Baptizers</td>
								<td>'. $baptizers .'</td>
							</tr>
							<tr>
								<th><strong>HAS AT LEAST</strong></th>
								<td></td>
							</tr>
							<tr>
								<td>Has baptized at least 1 disciple</td>
								<td>'. $has_at_least_1 .'</td>
							</tr>
							<tr>
								<td>Has baptized at least 2 disciples</td>
								<td>'. $has_at_least_2 .'</td>
							</tr>
							<tr>
								<td>Has baptized more than 2 disciples</td>
								<td>'. $has_more_than_2 .'</td>
							</tr>
							<tr>
								<td><strong>HAS</strong></td>
								<td></td>
							</tr>
							<tr>
								<td>Has not baptized anyone</td>
								<td>'. $has_0 .'</td>
							</tr>
							<tr>
								<td>Has baptized 1</td>
								<td>'. $has_1 .'</td>
							</tr>
							<tr>
								<td>Has baptized 2</td>
								<td>'. $has_2 .'</td>
							</tr>
							<tr>
								<td>Has baptized 3</td>
								<td>'. $has_3 .'</td>
							</tr>
							<tr>
								<th><strong>BAPTISM GENERATIONS</strong></th>
								<td></td>
							</tr>
							<tr>
								<td>Zero Gen</td>
								<td>'. $con_0gen .'</td>
							</tr>
							<tr>
								<td>1st Gen</td>
								<td>'. $con_1gen .'</td>
							</tr>
							<tr>
								<td>2nd Gen</td>
								<td>'. $con_2gen .'</td>
							</tr>
							<tr>
								<td>3rd Gen</td>
								<td>'. $con_3gen .'</td>
							</tr>
							<tr>
								<td>4th Gen</td>
								<td>'. $con_4gen .'</td>
							</tr>
							<tr>
								<td>5th Gen</td>
								<td>'. $con_5gen .'</td>
							</tr>

						</tbody>
					</table>
			';
        echo $html;
    }


    public function page_notes () {
        $html = '

            <p>The funnel stats report summarizes the contacts and milestones within the disciple making movement project.</p>
            <hr>
            <p>Funnel stats box highlights the critical path of seekers through the system.</p>
            <hr>
            <p>Generations stats box highlights the generation status of contacts through the system.</p>
            <hr>
            <p>Contacts stats box highlights the current status of contacts.</p>
            <p><a href="/wp-admin/options-general.php?page=dtsample&tab=report">Sample Reports Page</a><hr></p>

        ';
        echo $html;

    }

}
