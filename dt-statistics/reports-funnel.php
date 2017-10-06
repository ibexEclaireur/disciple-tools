<?php

/**
 * Disciple_Tools_Funnel_Reports
 *
 * @class   Disciple_Tools_Funnel_Reports
 * @version 0.1
 * @since   0.1
 * @package Disciple_Tools
 * @author  Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

class Disciple_Tools_Funnel_Reports {

//    private $page;

    /**
     * Disciple_Tools_Connections_Reports The single instance of Disciple_Tools_Connections_Reports.
     *
     * @var    object
     * @access private
     * @since  0.1
     */
    private static $_instance = null;

    /**
     * Main Disciple_Tools_Funnel_Reports Instance
     *
     * Ensures only one instance of Disciple_Tools_Funnel_Reports is loaded or can be loaded.
     *
     * @since  0.1
     * @static
     * @return Disciple_Tools_Funnel_Reports instance
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
        // Build page
        $this->page = new Disciple_Tools_Page_Factory( 'index.php',__( 'Funnel Stats','disciple_tools' ),__( 'Funnel Stats','disciple_tools' ), 'read','funnel_report' );
        // Build Boxes
        add_action( 'add_meta_boxes', [$this, 'page_metaboxes'] );
    } // End __construct()


    //Add some metaboxes to the page
    public function page_metaboxes(){

        add_meta_box( 'critical_path_stats','Critical Path', [$this, 'critical_path_stats'],'dashboard_page_funnel_report','normal','high' );
        add_meta_box( 'contact_stats','Contact Stats', [$this, 'contacts_stats_widget'],'dashboard_page_funnel_report','normal','low' );
        add_meta_box( 'groups_stats','Groups Stats', [$this, 'groups_stats_widget'],'dashboard_page_funnel_report','normal','low' );
        add_meta_box( 'baptism_stats','Baptism Stats', [$this, 'baptism_stats_widget'],'dashboard_page_funnel_report','normal','low' );
        add_meta_box( 'page_notes','Notes', [$this, 'page_notes'],'dashboard_page_funnel_report','side','high' );
    }

    /**
     * Movement funnel path dashboard widget
     *
     * @since  0.1
     * @access public
     */
    public function critical_path_stats () {
        global $wpdb;

        // Build variables
        $prayer = Disciple_Tools()->report_api->get_meta_key_total( '2017', 'Mailchimp', 'new_subscribers' );
        $mailchimp_subscribers = Disciple_Tools()->report_api->get_meta_key_total( '2017', 'Mailchimp', 'new_subscribers', 'max' );
        $facebook = Disciple_Tools()->report_api->get_meta_key_total( '2017', 'Facebook', 'page_likes_count' );
        $websites = Disciple_Tools()->report_api->get_meta_key_total( '2017', 'Analytics', 'unique_website_visitors' );

        $new_contacts = Disciple_Tools()->counter->contacts_post_status( 'publish' );
        $contacts_attempted = Disciple_Tools()->counter->contacts_meta_counter( 'seeker_path', 'attempted' );
        $contacts_established = Disciple_Tools()->counter->contacts_meta_counter( 'seeker_path', 'established' );
        $first_meetings = Disciple_Tools()->counter->contacts_meta_counter( 'seeker_path', 'met' );
        $baptisms = Disciple_Tools()->counter->get_baptisms( 'baptisms' );
        $baptizers = Disciple_Tools()->counter->get_baptisms( 'baptizers' );
        $active_churches = Disciple_Tools()->counter->groups_meta_counter( 'type', 'Church' );
        $church_planters = Disciple_Tools()->counter->connection_type_counter( 'participation', 'Planting' );



        ?>
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
                    <td><?php echo esc_html( $mailchimp_subscribers ); ?></td>

                </tr>
                <tr>
                    <td>Social Engagement</td>
                    <td><?php echo esc_html( $facebook ); ?></td>

                </tr>
                <tr>
                    <td>Website Visitors</td>
                    <td><?php echo esc_html( $websites ); ?></td>

                </tr>
                <tr>
                    <td>New Contacts</td>
                    <td><?php echo esc_html( $new_contacts ); ?></td>
                </tr>
                <tr>
                    <td>Contact Attempted</td>
                    <td><?php echo esc_html( $contacts_attempted ); ?></td>
                </tr>
                <tr>
                    <td>Contact Established</td>
                    <td><?php echo esc_html( $contacts_established ); ?></td>
                </tr>
                <tr>
                    <td>First Meeting Complete</td>
                    <td><?php echo esc_html( $first_meetings ); ?></td>
                </tr>
                <tr>
                    <td>Baptisms</td>
                    <td><?php echo esc_html( $baptisms ); ?></td>
                </tr>
                <tr>
                    <td>Baptizers</td>
                    <td><?php echo esc_html( $baptizers ); ?></td>
                </tr>
                <tr>
                    <td>Active Churches</td>
                    <td><?php echo esc_html( $active_churches ); ?></td>
                </tr>
                <tr>
                    <td>Church Planters</td>
                    <td><?php echo esc_html( $church_planters ); ?></td>
                </tr>

            </tbody>
        </table>
        <?php
    }

    /**
     * Contacts stats widget
     *
     * @since  0.1
     * @access public
     */
    public function contacts_stats_widget () {

//        print '<pre>'; print_r( Disciple_Tools()->counter->get_generation('generation_list') ); print '</pre>';

        // Build counters
        $has_at_least_1 = Disciple_Tools()->counter->get_generation( 'has_one_or_more' );
        $has_at_least_2 = Disciple_Tools()->counter->get_generation( 'has_two_or_more' );
        $has_more_than_2 = Disciple_Tools()->counter->get_generation( 'has_three_or_more' );

        $has_0 = Disciple_Tools()->counter->get_generation( 'has_0' );
        $has_1 = Disciple_Tools()->counter->get_generation( 'has_1' );
        $has_2 = Disciple_Tools()->counter->get_generation( 'has_2' );
        $has_3 = Disciple_Tools()->counter->get_generation( 'has_3' );

        $con_0gen = '';//Disciple_Tools()->counter->get_generation('at_zero');
        $con_1gen = '';//Disciple_Tools()->counter->get_generation('at_first');
        $con_2gen = '';//Disciple_Tools()->counter->get_generation('at_second');
        $con_3gen = '';//Disciple_Tools()->counter->get_generation('at_third');
        $con_4gen = '';//Disciple_Tools()->counter->get_generation('at_fourth');
        $con_5gen = '';//Disciple_Tools()->counter->get_generation('at_fifth');

        // Build counters
        $contacts_count = Disciple_Tools()->counter->contacts_post_status();
        $unassigned = Disciple_Tools()->counter->contacts_meta_counter( 'overall_status','unassigned' );

        $new_inquirers = Disciple_Tools()->counter->contacts_post_status();
        $assigned_inquirers = Disciple_Tools()->counter->contacts_meta_counter( 'overall_status','assigned' );
        $active_inquirers = Disciple_Tools()->counter->contacts_meta_counter( 'overall_status','active' );
        $contact_attempted = Disciple_Tools()->counter->contacts_meta_counter( 'seeker_path','Contact Attempted' );
        $contact_established = Disciple_Tools()->counter->contacts_meta_counter( 'seeker_path','Contact Established' );
        $meeting_scheduled = Disciple_Tools()->counter->contacts_meta_counter( 'seeker_path','Meeting Scheduled' );
        $first_meeting_complete = Disciple_Tools()->counter->contacts_meta_counter( 'seeker_path','First Meeting Complete' );
        $ongoing_meetings = Disciple_Tools()->counter->contacts_meta_counter( 'seeker_path','Ongoing Meetings' );



        ?>
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
                    <td><?php echo esc_html( $contacts_count->publish ); ?></td>
                    <td>Contact Established</td>
                    <td><?php echo esc_html( $contact_established ); ?></td>
                </tr>
                <tr>
                    <td>Unassigned</td>
                    <td><?php echo esc_html( $unassigned ); ?></td>
                    <td>Meeting Scheduled</td>
                    <td><?php echo esc_html( $meeting_scheduled ); ?></td>
                </tr>
                <tr>
                    <td>Assigned Inquirers</td>
                    <td><?php echo esc_html( $assigned_inquirers ); ?></td>
                    <td>First Meeting Complete</td>
                    <td><?php echo esc_html( $first_meeting_complete ); ?></td>
                </tr>
                <tr>
                    <td>Active</td>
                    <td><?php echo esc_html( $active_inquirers ); ?></td>
                    <td>Ongoing Meetings</td>
                    <td><?php echo esc_html( $ongoing_meetings ); ?></td>
                </tr>
                <tr>
                    <td>Contact Attempted</td>
                    <td><?php echo esc_html( $contact_attempted ); ?></td>
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
                    <td><?php echo esc_html( $has_at_least_1 ); ?></td>
                    <td>Zero Gen</td>
                    <td><?php echo esc_html( $con_0gen ); ?></td>
                </tr>
                <tr>
                    <td>Has at least 2 disciples</td>
                    <td><?php echo esc_html( $has_at_least_2 ); ?></td>
                    <td>1st Gen</td>
                    <td><?php echo esc_html( $con_1gen ); ?></td>
                </tr>
                <tr>
                    <td>Has more than 2 disciples</td>
                    <td><?php echo esc_html( $has_more_than_2 ); ?></td>
                    <td>2nd Gen</td>
                    <td><?php echo esc_html( $con_2gen ); ?></td>
                </tr>
                <tr>
                    <td><strong>HAS</strong></td>
                    <td></td>
                    <td>3rd Gen</td>
                    <td><?php echo esc_html( $con_3gen ); ?></td>
                </tr>
                <tr>
                    <td>Has No Disciples</td>
                    <td><?php echo esc_html( $has_0 ); ?></td>
                    <td>4th Gen</td>
                    <td><?php echo esc_html( $con_4gen ); ?></td>
                </tr>
                <tr>
                    <td>Has 1 Disciple</td>
                    <td><?php echo esc_html( $has_1 ); ?></td>
                    <td>5th Gen</td>
                    <td><?php echo esc_html( $con_5gen ); ?></td>
                </tr>
                <tr>
                    <td>Has 2 Disciples</td>
                    <td><?php echo esc_html( $has_2 ); ?></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Has 3 Disciples</td>
                    <td><?php echo esc_html( $has_3 ); ?></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <?php
    }

    /**
     * Groups stats widget
     *
     * @since  0.1
     * @access public
     */
    public function groups_stats_widget () {

//        print '<pre>'; print_r( Disciple_Tools()->counter->get_generation('generation_list') ); print '</pre>';

        // Build counters
        $has_at_least_1 = Disciple_Tools()->counter->get_generation( 'has_one_or_more', 'groups' );
        $has_at_least_2 = Disciple_Tools()->counter->get_generation( 'has_two_or_more', 'groups' );
        $has_more_than_2 = Disciple_Tools()->counter->get_generation( 'has_three_or_more', 'groups' );

        $has_0 = Disciple_Tools()->counter->get_generation( 'has_0', 'groups' );
        $has_1 = Disciple_Tools()->counter->get_generation( 'has_1', 'groups' );
        $has_2 = Disciple_Tools()->counter->get_generation( 'has_2', 'groups' );
        $has_3 = Disciple_Tools()->counter->get_generation( 'has_3', 'groups' );

        $gr_0gen = '';//Disciple_Tools()->counter->get_generation('at_zero', 'groups');
        $gr_1gen = '';//Disciple_Tools()->counter->get_generation('at_first', 'groups');
        $gr_2gen = '';//Disciple_Tools()->counter->get_generation('at_second', 'groups');
        $gr_3gen = '';//Disciple_Tools()->counter->get_generation('at_third', 'groups');
        $gr_4gen = '';//Disciple_Tools()->counter->get_generation('at_fourth', 'groups');

        $dbs = Disciple_Tools()->counter->groups_meta_counter( 'type', 'DBS' );
        $active_churches = Disciple_Tools()->counter->groups_meta_counter( 'type', 'Church' );


        ?>
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
                    <td><?php echo esc_html( $dbs ); ?></td>
                    <td>Zero Gen (has no record of being planted by another group)</td>
                    <td><?php echo esc_html( $gr_0gen ); ?></td>
                </tr>
                <tr>
                    <td>Active Churches</td>
                    <td><?php echo esc_html( $active_churches ); ?></td>
                    <td>1st Gen</td>
                    <td><?php echo esc_html( $gr_1gen ); ?></td>
                </tr>
                <tr>
                    <th><strong>HAS AT LEAST</strong></th>
                    <td></td>
                    <td>2nd Gen</td>
                    <td><?php echo esc_html( $gr_2gen ); ?></td>
                </tr>
                <tr>
                    <td>Has planted at least 1 group</td>
                    <td><?php echo esc_html( $has_at_least_1 ); ?></td>
                    <td>3rd Gen</td>
                    <td><?php echo esc_html( $gr_3gen ); ?></td>
                </tr>
                <tr>
                    <td>Has planted at least 2 groups</td>
                    <td><?php echo esc_html( $has_at_least_2 ); ?></td>
                    <td>4th Gen</td>
                    <td><?php echo esc_html( $gr_4gen ); ?></td>
                </tr>
                <tr>
                    <td>Has planted at least 3 groups</td>
                    <td><?php echo esc_html( $has_more_than_2 ); ?></td>
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
                    <td><?php echo esc_html( $has_0 ); ?></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Has Planted 1 Group</td>
                    <td><?php echo esc_html( $has_1 ); ?></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Has Planted 2 Groups</td>
                    <td><?php echo esc_html( $has_2 ); ?></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Has Planted 3 Groups</td>
                    <td><?php echo esc_html( $has_3 ); ?></td>
                    <td></td>
                    <td></td>
                </tr>

            </tbody>
        </table>
        <?php
    }

    /**
     * Baptism Generations stats dashboard widget
     *
     * @since  0.1
     * @access public
     */
    public function baptism_stats_widget () {

//        print '<pre>'; print_r( Disciple_Tools()->counter->get_generation('generation_list') ); print '</pre>';

        // Build counters
        $has_at_least_1 = Disciple_Tools()->counter->get_generation( 'has_one_or_more', 'baptisms' );
        $has_at_least_2 = Disciple_Tools()->counter->get_generation( 'has_two_or_more', 'baptisms' );
        $has_more_than_2 = Disciple_Tools()->counter->get_generation( 'has_three_or_more', 'baptisms' );

        $has_0 = Disciple_Tools()->counter->get_generation( 'has_0', 'baptisms' );
        $has_1 = Disciple_Tools()->counter->get_generation( 'has_1', 'baptisms' );
        $has_2 = Disciple_Tools()->counter->get_generation( 'has_2', 'baptisms' );
        $has_3 = Disciple_Tools()->counter->get_generation( 'has_3', 'baptisms' );

        $con_0gen = '';//Disciple_Tools()->counter->get_generation('at_zero', 'baptisms');
        $con_1gen = '';//Disciple_Tools()->counter->get_generation('at_first', 'baptisms');
        $con_2gen = '';//Disciple_Tools()->counter->get_generation('at_second', 'baptisms');
        $con_3gen = '';//Disciple_Tools()->counter->get_generation('at_third', 'baptisms');
        $con_4gen = '';//Disciple_Tools()->counter->get_generation('at_fourth', 'baptisms');
        $con_5gen = '';//Disciple_Tools()->counter->get_generation('at_fifth', 'baptisms');

        $baptisms = Disciple_Tools()->counter->get_baptisms( 'baptisms' );
        $baptizers = Disciple_Tools()->counter->get_baptisms( 'baptizers' );


        ?>
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
                    <td><?php echo esc_html( $baptisms ); ?></td>
                </tr>
                <tr>
                    <td>Baptizers</td>
                    <td><?php echo esc_html( $baptizers ); ?></td>
                </tr>
                <tr>
                    <th><strong>HAS AT LEAST</strong></th>
                    <td></td>
                </tr>
                <tr>
                    <td>Has baptized at least 1 disciple</td>
                    <td><?php echo esc_html( $has_at_least_1 ); ?></td>
                </tr>
                <tr>
                    <td>Has baptized at least 2 disciples</td>
                    <td><?php echo esc_html( $has_at_least_2 ); ?></td>
                </tr>
                <tr>
                    <td>Has baptized more than 2 disciples</td>
                    <td><?php echo esc_html( $has_more_than_2 ); ?></td>
                </tr>
                <tr>
                    <td><strong>HAS</strong></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Has not baptized anyone</td>
                    <td><?php echo esc_html( $has_0 ); ?></td>
                </tr>
                <tr>
                    <td>Has baptized 1</td>
                    <td><?php echo esc_html( $has_1 ); ?></td>
                </tr>
                <tr>
                    <td>Has baptized 2</td>
                    <td><?php echo esc_html( $has_2 ); ?></td>
                </tr>
                <tr>
                    <td>Has baptized 3</td>
                    <td><?php echo esc_html( $has_3 ); ?></td>
                </tr>
                <tr>
                    <th><strong>BAPTISM GENERATIONS</strong></th>
                    <td></td>
                </tr>
                <tr>
                    <td>Zero Gen</td>
                    <td><?php echo esc_html( $con_0gen ); ?></td>
                </tr>
                <tr>
                    <td>1st Gen</td>
                    <td><?php echo esc_html( $con_1gen ); ?></td>
                </tr>
                <tr>
                    <td>2nd Gen</td>
                    <td><?php echo esc_html( $con_2gen ); ?></td>
                </tr>
                <tr>
                    <td>3rd Gen</td>
                    <td><?php echo esc_html( $con_3gen ); ?></td>
                </tr>
                <tr>
                    <td>4th Gen</td>
                    <td><?php echo esc_html( $con_4gen ); ?></td>
                </tr>
                <tr>
                    <td>5th Gen</td>
                    <td><?php echo esc_html( $con_5gen ); ?></td>
                </tr>

            </tbody>
        </table>
        <?php
    }


    public function page_notes () {
        ?>
            <p>The funnel stats report summarizes the contacts and milestones within the disciple making movement project.</p>
            <hr>
            <p>Funnel stats box highlights the critical path of seekers through the system.</p>
            <hr>
            <p>Generations stats box highlights the generation status of contacts through the system.</p>
            <hr>
            <p>Contacts stats box highlights the current status of contacts.</p>
            <p><a href="/wp-admin/options-general.php?page=dtsample&tab=report">Sample Reports Page</a><hr></p>
        <?php
    }

}
