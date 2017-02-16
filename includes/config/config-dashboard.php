<?php

/**
 * DRM dashboard widget
 *
 * @class Drm_Dashboard
 * @version	0.1
 * @since 0.1
 * @package	drm
 * @author Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class DRM_Dashboard {

	/**
	 * Drm_Dashboard The single instance of Drm_Dashboard.
	 * @var 	object
	 * @access  private
	 * @since 	0.1
	 */
	private static $_instance = null;

	/**
	 * Main Drm_Dashboard Instance
	 * Ensures only one instance of Drm_Dashboard is loaded or can be loaded.
	 *
	 * @since 0.1
	 * @static
	 * @return Drm_Dashboard
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
		if ( is_admin() ) {
			/* Add dashboard widgets */
			add_action('wp_dashboard_setup', array( $this, 'add_dmm_widgets' ) );

			/* Remove Dashboard defaults */
			add_action( 'admin_init', array( $this, 'remove_dashboard_meta' ) );
			remove_action('welcome_panel', 'wp_welcome_panel' );
		}
	} // End __construct()

	/**
	 * Main action hooks
	 *
	 * @since 0.1
	 * @access public
	 */
	public function add_dmm_widgets() {

		/* Add custom dashboard widgets */
		wp_add_dashboard_widget('new_contacts_widget', 'New Contacts', array( $this, 'new_contacts_dashboard_widget' ) );
		wp_add_dashboard_widget('updates_needed_widget', 'Updates Needed', array( $this, 'update_needed_dashboard_widget' ) );

		add_meta_box( 'funnel_stats_widget', 'Funnel Stats', array( $this, 'funnel_stats_widget' ), 'dashboard', 'side', 'high' );
		add_meta_box( 'project_stats_widget', 'Project Stats', array( $this, 'project_stats_widget'), 'dashboard', 'side', 'high' );
		add_meta_box( 'generations_stats_widget', 'Generations Stats', array( $this, 'generations_stats_widget'), 'dashboard', 'side', 'low' );
		add_meta_box( 'system_stats_widget', 'System Stats', array( $this, 'system_stats_widget'), 'dashboard', 'side', 'low' );

		add_filter( 'dashboard_recent_posts_query_args', array( $this, 'add_page_to_dashboard_activity') );
	}

	/**
	 * New Contacts Dashboard Widget
	 *
	 * @since 0.1
	 * @access public
	*/
	public function new_contacts_dashboard_widget( ) {
		$html_content = '
			<table class="form-table striped ">
				<tbody>
					<tr>
						<td class="row-title"><a href="#">Ferran Sunnareh</a></td>
						<td>720-212-8535</td>
						<td>Assigned</td>
						<td>Aug. 26, 2016</td>
					</tr>
					<tr>
						<td class="row-title"><a href="#">Sherif A.</a></td>
						<td>720-212-8535</td>
						<td>Unassigned</td>
						<td>Aug. 26, 2016</td>
					</tr>
				</tbody>
			</table>
			';
		echo $html_content;
	}

	/**
	 * Updates Needed Dashboard Widget
	 *
	 * @since 0.1
	 * @access public
	 */
	public function update_needed_dashboard_widget( ) {
		$html_content = '
			<table class="form-table striped ">
				<tbody>
					<tr>
						<td class="row-title">Name</td>
						<td>Last Update</td>
						<td>Status</td>
					</tr>
					<tr>
						<td class="row-title"><a href="post.php?post=136&action=edit">Bari Waql</a></td>
						<td>Nov 23, 2016</td>
						<td><span style="background-color: #E36449; padding: 2px 6px;">Weak</span></td>
					</tr>
					<tr>
						<td class="row-title"><a href="post.php?post=128&action=edit">Sharif Zia</a></td>
						<td>Nov 28, 2016</td>
						<td><span style="background-color: #E36449; padding: 2px 6px;">Weak</span></td>
					</tr>
					<tr>
						<td class="row-title"><a href="post.php?post=102&action=edit">Maysa Azzam</a></td>
						<td>Dec 25, 2016</td>
						<td><span style="background-color: #E3BE49; padding: 2px 6px;">Fading</span></td>
					</tr>
					<tr>
						<td class="row-title"><a href="post.php?post=140&action=edit">Buthaynah</a></td>
						<td>Jan 1, 2016</td>
						<td><span style="background-color: #E3BE49; padding: 2px 6px;">Fading</span></td>
					</tr>
				</tbody>
			</table>
			';
		echo $html_content;
	}

	/**
	 * Movement funnel path dashboard widget
	 *
	 * @since 0.1
	 * @access public
	 */
	public function funnel_stats_widget( ) {

		// Build variables
		$prayer = 'x';
		$facebook = 'x';
		$websites = 'x';
		$new_contacts = DRM_Plugin()->counter->contacts_post_status('publish');
		$conacts_attempted = 'x';
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
								<td><a href="#">Prayers Network</a></td>
								<td>'.$prayer.'</td>
								
							</tr>
							<tr>
								<td><a href="#">Facebook Engagement</a></td>
								<td>'.$facebook.'</td>
								
							</tr>
							<tr>
								<td><a href="#">Website Visitors</a></td>
								<td>'.$websites.'</td>
								
							</tr>
							<tr>
								<td><a href="#">New Contacts</a></td>
								<td>'.$new_contacts.'</td>
							</tr>
							<tr>
								<td><a href="#">Contact Attempted</a></td>
								<td>'.$conacts_attempted.'</td>
							</tr>
							<tr>
								<td><a href="#">Contact Established</a></td>
								<td>'.$contacts_established.'</td>
							</tr>
							<tr>
								<td><a href="#">First Meeting Complete</a></td>
								<td>'.$first_meetings.'</td>
							</tr>
							<tr>
								<td><a href="#">Baptisms</a></td>
								<td>'.$baptisms.'</td>
							</tr>
							<tr>
								<td><a href="#">Baptizers</a></td>
								<td>'.$baptizers.'</td>
							</tr>
							<tr>
								<td><a href="#">Active Churches</a></td>
								<td>'.$active_churches.'</td>
							</tr>
							<tr>
								<td><a href="#">Church Planters</a></td>
								<td>'.$church_planters.'</td>
							</tr>
							
						</tbody>
					</table>
			';

		echo $html;
	}

	/**
	 * Project stats dashboard widget
	 *
	 * @since 0.1
	 * @access public
	 */
	public function project_stats_widget(  ) {

		// Build counters
		$contacts_count = DRM_Plugin()->counter->contacts_post_status();
		$unassigned = DRM_Plugin()->counter->contacts_overall_status('unassigned');
		$accepted = DRM_Plugin()->counter->contacts_overall_status('accepted');

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

	/**
	 * Generations stats dashboard widget
	 *
	 * @since 0.1
	 * @access public
	 */
	public function generations_stats_widget (  ) {
        print '<pre>'; print_r( DRM_Plugin()->counter->get_generation('first') ); print '</pre>';
		// Build counters
		$con_1gen = 'x';
		$con_2gen = 'x';
		$con_3gen = 'x';
		$con_4gen = 'x';
		$gr_1gen = 'x';
		$gr_2gen = 'x';
		$gr_3gen = 'x';
		$gr_4gen = 'x';

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
								<th><strong>CONTACTS</strong></th>
								<td></td>
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
								<th><strong>GROUPS</strong></td>
								<td></td>
							</tr>
							<tr>
								<td>1st Gen</td>
								<td>'. $gr_1gen .'</td>
							</tr>
							<tr>
								<td>1st Gen</td>
								<td>'. $gr_2gen .'</td>
							</tr>
							<tr>
								<td>1st Gen</td>
								<td>'. $gr_3gen .'</td>
							</tr>
							<tr>
								<td>1st Gen</td>
								<td>'. $gr_4gen .'</td>
							</tr>
						</tbody>
					</table>
			';
		echo $html;
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

	/**
	 * Remove default dashboard widgets
	 *
	 * @since 0.1
	 * @access public
	 */
	public function remove_dashboard_meta () {

		remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );

		//remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
		//remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');
	}

	/**
	 * Add custom post types to Activity feed on dashboard
	 *
	 * @source https://gist.github.com/Mte90/708e54b21b1f7372b48a
	 * @since 0.1
	 * @access public
	 */
	public function add_page_to_dashboard_activity ( $query_args ) {
		if ( is_array( $query_args[ 'post_type' ] ) ) {
			//Set your post type
			$query_args[ 'post_type' ][] = 'contacts';
		} else {
			$temp = array( $query_args[ 'post_type' ], 'contacts' );
			$query_args[ 'post_type' ] = $temp;
		}
		return $query_args;
	}

}