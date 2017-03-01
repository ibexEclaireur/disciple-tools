<?php

/**
 * Disciple_Tools_Dashboard Class
 *
 * @class Disciple_Tools_Dashboard
 * @version	0.1
 * @since 0.1
 * @package	Disciple_Tools
 * @author Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class Disciple_Tools_Dashboard {

	/**
	 * Disciple_Tools_Dashboard The single instance of Disciple_Tools_Dashboard.
	 * @var 	object
	 * @access  private
	 * @since 	0.1
	 */
	private static $_instance = null;

	/**
	 * Main Disciple_Tools_Dashboard Instance
	 * Ensures only one instance of Disciple_Tools_Dashboard is loaded or can be loaded.
	 *
	 * @since 0.1
	 * @static
	 * @return Disciple_Tools_Dashboard
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
		add_meta_box( 'system_stats_widget', 'System Stats', array( $this, 'system_stats_widget'), 'dashboard', 'side', 'low' );

		add_filter( 'dashboard_recent_posts_query_args', array( $this, 'add_page_to_dashboard_activity') );
	}

    function media_reports_menu() {
        add_dashboard_page('My Plugin Dashboard', 'My Plugin', 'read', 'my-unique-identifier', 'my_plugin_function');
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
        $html = Disciple_Tools()->reports_funnel->funnel_stats();
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