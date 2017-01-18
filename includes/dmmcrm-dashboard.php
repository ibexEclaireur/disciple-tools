<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



/*
*
* ADD DASHBOARD WIDGETS
*/

// Add widget action
add_action('wp_dashboard_setup', 'add_dmm_widgets' );

// Function used in the action hook
function add_dmm_widgets() {
	wp_add_dashboard_widget('new_contacts_widget', 'New Contacts', 'new_contacts_dashboard_widget');
	wp_add_dashboard_widget('updates_needed_widget', 'Updates Needed', 'update_needed_dashboard_widget');
	add_meta_box( 'new_stats_widget', 'Project Statistics', 'prayers_network_dashboard_widget', 'dashboard', 'side', 'high' );
	add_meta_box( 'stats_widget', 'Stats', 'stats_widget', 'dashboard', 'side', 'low' );
	
}


// New Contacts Dashboard Widget
function new_contacts_dashboard_widget( $post, $callback_args ) {
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

// New Contacts Dashboard Widget
function update_needed_dashboard_widget( $post, $callback_args ) {
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


// New Contacts Dashboard Widget
function new_comments_dashboard_widget( $post, $callback_args ) {
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

// New Contacts Dashboard Widget
function stats_widget( $post, $callback_args ) {
	$html_content = '
		<iframe src="/wp-content/plugins/dmm-crm/includes/charts/pie-chart.html" width="100%" height="300px" border="0"></iframe><br>
		<iframe src="/wp-content/plugins/dmm-crm/includes/charts/prayer-chart.html" width="100%" height="220px" border="0"></iframe><br>
		';
		
	echo $html_content;
	
}




// Stats Dashboard Widget
function prayers_network_dashboard_widget( $post, $callback_args ) {
	$html_content = '
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
		
	echo $html_content;
}


/*
*
* REMOVE ALL DEFAULT DASHBOARD WIDGETS
*/

function remove_dashboard_meta() {
        remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
        //remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
        //remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');//since 3.8
}
add_action( 'admin_init', 'remove_dashboard_meta' );

remove_action('welcome_panel', 'wp_welcome_panel');


// Add custom post types to the Activity feed. https://gist.github.com/Mte90/708e54b21b1f7372b48a 
if ( is_admin() ) {
	add_filter( 'dashboard_recent_posts_query_args', 'add_page_to_dashboard_activity' );
	function add_page_to_dashboard_activity( $query_args ) {
		if ( is_array( $query_args[ 'post_type' ] ) ) {
			//Set yout post type
			$query_args[ 'post_type' ][] = 'dmm_contacts';
		} else {
			$temp = array( $query_args[ 'post_type' ], 'dmm_contacts' );
			$query_args[ 'post_type' ] = $temp;
		}
		return $query_args;
	}
}
?>