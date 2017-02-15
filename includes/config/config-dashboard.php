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

	/* Action hooks */
	public function add_dmm_widgets() {

		/* Add custom dashboard widgets */
		wp_add_dashboard_widget('new_contacts_widget', 'New Contacts', array( $this, 'new_contacts_dashboard_widget' ) );
		wp_add_dashboard_widget('updates_needed_widget', 'Updates Needed', array( $this, 'update_needed_dashboard_widget' ) );
		add_meta_box( 'stats_widget', 'Stats', array( $this, 'stats_widget'), 'dashboard', 'side', 'high' );
		add_meta_box( 'new_stats_widget', 'Project Statistics', array( $this, 'prayers_network_dashboard_widget' ), 'dashboard', 'side', 'low' );
		add_filter( 'dashboard_recent_posts_query_args', array( $this, 'add_page_to_dashboard_activity') );
	}

	/*
	* New Contacts Dashboard Widget
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

	/*
	* Updates Needed Dashboard Widget
	*
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

	/*
	* New Contacts Dashboard Widget
	*
	*/
	public function new_comments_dashboard_widget( $post, $callback_args ) {
		$html_content = '
			<table class="form-table striped ">
				<tbody>
					<tr>
						<td class="row-title"><a href="#">Mohammed P.</a></td>
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


	/*
	 * Stats dashboard widget
	 *
	 * @since 0.1
	 * @access public
	 */
	public function stats_widget( $post, $callback_args ) {
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
								<td>132,811</td>
								
							</tr>
							<tr>
								<td><a href="#">Facebook Engagement</a></td>
								<td>447,239</td>
								
							</tr>
							<tr>
								<td><a href="#">Website Visitors</a></td>
								<td>182,994</td>
								
							</tr>
							<tr>
								<td><a href="#">New Inquirer</a></td>
								<td>2,243</td>
							</tr>
							<tr>
								<td><a href="#">Contact Attempted</a></td>
								<td>866</td>
							</tr>
							<tr>
								<td><a href="#">Contact Established</a></td>
								<td>725</td>
							</tr>
							<tr>
								<td><a href="#">First Meeting Complete</a></td>
								<td>458</td>
							</tr>
							<tr>
								<td><a href="#">Baptisms</a></td>
								<td>72</td>
							</tr>
							<tr>
								<td><a href="#">Baptizers</a></td>
								<td>37</td>
							</tr>
							<tr>
								<td><a href="#">Active Churches</a></td>
								<td>7</td>
							</tr>
							<tr>
								<td><a href="#">Church Planters</a></td>
								<td>23</td>
							</tr>
							
						</tbody>
					</table>
			';
		echo $html;
	}

	/*
	 * Critical path dashboard widget
	 *
	 * @since 0.1
	 * @access public
	 */
	public function prayers_network_dashboard_widget( ) {
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
								<td>132,811</td>
								
							</tr>
							<tr>
								<td><a href="#">Facebook Engagement</a></td>
								<td>447,239</td>
								
							</tr>
							<tr>
								<td><a href="#">Website Visitors</a></td>
								<td>182,994</td>
								
							</tr>
							<tr>
								<td><a href="#">New Inquirer</a></td>
								<td>2,243</td>
							</tr>
							<tr>
								<td><a href="#">Contact Attempted</a></td>
								<td>866</td>
							</tr>
							<tr>
								<td><a href="#">Contact Established</a></td>
								<td>725</td>
							</tr>
							<tr>
								<td><a href="#">First Meeting Complete</a></td>
								<td>458</td>
							</tr>
							<tr>
								<td><a href="#">Baptisms</a></td>
								<td>72</td>
							</tr>
							<tr>
								<td><a href="#">Baptizers</a></td>
								<td>37</td>
							</tr>
							<tr>
								<td><a href="#">Active Churches</a></td>
								<td>7</td>
							</tr>
							<tr>
								<td><a href="#">Church Planters</a></td>
								<td>23</td>
							</tr>
							
						</tbody>
					</table>
			';

		echo $html;
	}

	/*
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

	/*
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