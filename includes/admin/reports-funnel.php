<?php

/**
 * Disciple_Tools_Funnel_Reports
 *
 * @class Disciple_Tools_Funnel_Reports
 * @version	0.1
 * @since 0.1
 * @package	Disciple_Tools
 * @author Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Disciple_Tools_Funnel_Reports {

//    private $page;

    /**
     * Disciple_Tools_Connections_Reports The single instance of Disciple_Tools_Connections_Reports.
     * @var 	object
     * @access  private
     * @since 	0.1
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
        add_meta_box('generation_stats','Generation Stats', array($this, 'generations_stats_widget'),'dashboard_page_funnel_report','normal','high');
        add_meta_box('contact_stats','Contact Stats', array($this, 'contacts_stats_widget'),'dashboard_page_funnel_report','normal','low');
        add_meta_box('page_notes','Notes', array($this, 'page_notes'),'dashboard_page_funnel_report','side','high');
    }

    /**
     * Movement funnel path dashboard widget
     *
     * @since 0.1
     * @access public
     */
    public function critical_path_stats ( ) {

        // Build variables
        $prayer = Disciple_Tools()->report_api->get_meta_key_total('2017', 'Mailchimp', 'new_subscribers');
        $mailchimp_subscribers = Disciple_Tools()->report_api->get_meta_key_total('2017', 'Mailchimp', 'new_subscribers', 'max');
        $facebook = Disciple_Tools()->report_api->get_meta_key_total('2017', 'Facebook', 'page_likes_count');
        $websites = Disciple_Tools()->report_api->get_meta_key_total('2017', 'Analytics', 'unique_website_visitors');

        $new_contacts = Disciple_Tools()->counter->contacts_post_status('publish');
        $contacts_attempted = 'x';
        $contacts_established = 'x';
        $first_meetings = 'x';
        $baptisms = 'x';
        $baptizers = 'x';
        $active_churches = 'x';
        $church_planters = 'x';

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
								<td>Total 2017: '.$prayer.', Most Subscribers Per Day: '.$mailchimp_subscribers.'</td>
								
							</tr>
							<tr>
								<td>Facebook Engagement (2017, page_likes_count)</td>
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
     * Generations stats dashboard widget
     *
     * @since 0.1
     * @access public
     */
    public function generations_stats_widget (  ) {

//        print '<pre>'; print_r( Disciple_Tools()->counter->get_generation('generation_list') ); print '</pre>';

        // Build counters
        $has_at_least_1 = Disciple_Tools()->counter->get_generation('has_one_or_more');
        $has_at_least_2 = Disciple_Tools()->counter->get_generation('has_two_or_more');
        $has_more_than_2 = Disciple_Tools()->counter->get_generation('has_three_or_more');

        $has_0 = Disciple_Tools()->counter->get_generation('has_0');
        $has_1 = Disciple_Tools()->counter->get_generation('has_1');
        $has_2 = Disciple_Tools()->counter->get_generation('has_2');
        $has_3 = Disciple_Tools()->counter->get_generation('has_3');

        $con_0gen = Disciple_Tools()->counter->get_generation('at_zero');
        $con_1gen = Disciple_Tools()->counter->get_generation('at_first');
        $con_2gen = Disciple_Tools()->counter->get_generation('at_second');
        $con_3gen = Disciple_Tools()->counter->get_generation('at_third');
        $con_4gen = Disciple_Tools()->counter->get_generation('at_fourth');
        $con_5gen = Disciple_Tools()->counter->get_generation('at_fifth');

        $has_0_groups = Disciple_Tools()->counter->get_generation('has_0', 'groups');
        $gr_1gen = Disciple_Tools()->counter->get_generation('at_first', 'groups');
        $gr_2gen = Disciple_Tools()->counter->get_generation('at_second', 'groups');
        $gr_3gen = Disciple_Tools()->counter->get_generation('at_third', 'groups');
        $gr_4gen = Disciple_Tools()->counter->get_generation('at_fourth', 'groups');



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
								<th><strong>HAS AT LEAST</strong></th>
								<td></td>
							</tr>
							<tr>
								<td>Has at least 1 disciple</td>
								<td>'. $has_at_least_1 .'</td>
							</tr>
							<tr>
								<td>Has at least 2 disciples</td>
								<td>'. $has_at_least_2 .'</td>
							</tr>
							<tr>
								<td>Has more than 2 disciples</td>
								<td>'. $has_more_than_2 .'</td>
							</tr>
							<tr>
								<td><strong>HAS</strong></td>
								<td></td>
							</tr>
							<tr>
								<td>Has No Disciples</td>
								<td>'. $has_0 .'</td>
							</tr>
							<tr>
								<td>Has 1 Disciple</td>
								<td>'. $has_1 .'</td>
							</tr>
							<tr>
								<td>Has 2 Disciples</td>
								<td>'. $has_2 .'</td>
							</tr>
							<tr>
								<td>Has 3 Disciples</td>
								<td>'. $has_3 .'</td>
							</tr>
							<tr>
								<th><strong>CONTACTS</strong></th>
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
							<tr>
								<th><strong>GROUPS</strong></td>
								<td></td>
							</tr>
							<tr>
								<td>Has No Child Groups</td>
								<td>'. $has_0_groups .'</td>
							</tr>
							<tr>
								<td>1st Gen</td>
								<td>'. $gr_1gen .'</td>
							</tr>
							<tr>
								<td>2nd Gen</td>
								<td>'. $gr_2gen .'</td>
							</tr>
							<tr>
								<td>3rd Gen</td>
								<td>'. $gr_3gen .'</td>
							</tr>
							<tr>
								<td>4th Gen</td>
								<td>'. $gr_4gen .'</td>
							</tr>
						</tbody>
					</table>
			';
        echo $html;
    }

    /**
     * Contact stats dashboard widget
     *
     * @since 0.1
     * @access public
     */
    public function contacts_stats_widget () {

        // Build counters
        $contacts_count = Disciple_Tools()->counter->contacts_post_status();
        $unassigned = Disciple_Tools()->counter->contacts_overall_status('unassigned');
        $accepted = Disciple_Tools()->counter->contacts_overall_status('accepted');

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
								<td>Active Contacts</td>
								<td>'. $contacts_count->publish .'</td>
								
							</tr>
							<tr>
								<td>Draft Contacts</td>
								<td>'. $contacts_count->draft .'</td>
								
							</tr>
							<tr>
								<td>Unassigned</td>
								<td>'. $unassigned .'</td>
							</tr>
							<tr>
								<td>Accepted</td>
								<td>'. $accepted .'</td>
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
            <p><a href="/wp-admin/options-general.php?page=dtsample&tab=report">Sample Reports Page</a></p>
        ';
        echo $html;
    }

}