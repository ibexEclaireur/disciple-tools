<?php


class Disciple_Tools_Facebook_Integration {
	/**
	 * Disciple_Tools_Admin The single instance of Disciple_Tools_Admin.
	 * @var 	object
	 * @access  private
	 * @since  0.1
	 */
	private static $_instance = null;

	/**
	 * Main Disciple_Tools_Settings Instance
	 *
	 * Ensures only one instance of Disciple_Tools_Settings is loaded or can be loaded.
	 *
	 * @since 0.1
	 * @static
	 * @return Disciple_Tools_Settings instance
	 */
	public static function instance () {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()


    protected $context = "facebook";
    protected $version = 1;


	/**
	 * Constructor function.
	 * @access  public
	 * @since   0.1
	 */
	public function __construct () {

	    add_action('admin_menu', array($this, 'add_facebook_settings_menu') );
	} // End __construct()

    public function add_facebook_settings_menu () {
        $this->_hook = add_submenu_page( 'options-general.php', __( 'Facebook (DT)', 'disciple_tools' ),
            __( 'Facebook (DT)', 'disciple_tools' ), 'manage_options', $this->context, array( $this, 'facebook_settings_page' ) );
    } // End register_settings_screen()


    public function facebook_settings_page(){
        echo '<div class="dt_facebook_errors" style="background-color:white;">' . get_option( 'disciple_tools_facebook_error').'</div>';


        echo "<h1>Facebook Integration Settings</h1>";
        echo "<h3>Hook up Disciple tools to a Facebook app in order to get contacts or useful stats from your Facebook pages. </h3>";
        $html = '<div class="wrap">
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">

                        <form action="'.  $this->get_rest_url() . "/add-app" .'" method="post">
                        <input type="hidden" name="_wpnonce" id="_wpnonce" value="' . wp_create_nonce( 'wp_rest' ) . '" />';
        $html .=            '<table class="widefat striped">

                                <thead><th>Facebook App Settings</th><th></th></thead>
                                <p>For this integration to work, go to your <a href="https://developers.facebook.com/apps">Facebook app\'s settings page</a>.
                                 Under <strong>Add Platform</strong>, choose the website option, put: <strong>' . get_site_url() . '</strong> as the site URL and click save changes.<br>
                                From your Facebook App\'s settings page get the App ID and the App Secret and paste them bellow and click the "Save App Settings" button.<br>
                                If you have any Facebook pages, they should appear in the Facebook Pages Table bellow.<br>
                                You will need to re-authenticate (by clicking the "Save App Settings" button bellow) if:<br>
                                &nbsp;&nbsp;    •You change your Facebook account password<br>
                                &nbsp;&nbsp;    •You delete or de­authorize your Facebook App
                                <p/>
                                <p></p>
                                <tbody>

                                    <tr><td>Facebook App Id</td><td>
                                        <input type="text" class="regular-text" name="app_id" value="' . get_option("disciple_tools_facebook_app_id", "")  . '"/>
                                    </td></tr>
                                    <tr><td>Facebook App Secret</td><td>
                                    <input type="text" class="regular-text" name="app_secret" value="'. get_option("disciple_tools_facebook_app_secret") .'"/>
                                     </td></tr>
                                    <tr><td>Access Token</td><td>'
                                        .  (get_option("disciple_tools_facebook_access_token") ? "Access token is saved" : "No Access Token")  .
                                     '</td></tr>

                                    <tr><td>Save app</td><td><input type="submit" class="button" name="save_app" value="Save app Settings" /> </td></tr>
                                    ';
        $html .= '              </tbody>
                            </table>
                        </form>';

        $html .=  '<br>' .
            '                        <form action="" method="post">
                        <input type="hidden" name="_wpnonce" id="_wpnonce" value="' . wp_create_nonce( 'wp_rest' ) . '" />';
        $html .= $this->facebook_pages_function($_POST);
        $html .= '<table id="facebook_pages" class="widefat striped">
                    <thead><th>Facebook Pages </th></thead>
                    <tbody>';
        $facebook_pages = get_option("disciple_tools_facebook_pages", array());
        foreach($facebook_pages as $id => $facebook_page){
            $html .=  '<tr><td>' . $facebook_page->name . ' (' . $id .')'. '</td>
               <td>
                   <label for="'.$facebook_page->name.'-integrate" >Sync Contacts </label>
                   <input name="'.$facebook_page->name.'-integrate" type="checkbox" value="' . $facebook_page->name.'" ' .checked(1, isset($facebook_page->integrate) ? $facebook_page->integrate : false, false ).'/>
                   (requires page webhooks)
               </td>
               <td>
                   <label for="'.$facebook_page->name . '-report" >Include in Stats </label>
                   <input name="'.$facebook_page->name.'-report" type="checkbox" value="' . $facebook_page->name.'" ' .checked(1, isset($facebook_page->report) ? $facebook_page->report : false, false ).'/>
               </td>';
        }
        $html .= '</tbody>
                </table>
        <input type="submit" class="button" name="get_pages" value="Refresh Page List" />
        <input type="submit" class="button" name="save_pages" value="Save Pages Settings" />';


        $html .=       '</form>';
        $html .= '</div><!-- end post-body-content -->';

        $html .=   '</div><!-- post-body meta box container -->
            </div><!--poststuff end -->
        </div><!-- wrap end -->';

        echo $html;
    }

    private function display_error($err){
        $err = date("Y-m-d h:i:sa") . ' ' . $err;
        echo '<div class="dt_facebook_errors" style="background-color:white;">'.$err.'</div>';
        update_option( 'disciple_tools_facebook_error', $err);
    }
    public function facebook_pages_function($post){
        // Check noonce
        if ( isset($post['dt_app_form_noonce']) && ! wp_verify_nonce( $post['dt_app_form_noonce'], 'dt_app_form') ) {
            return 'Are you cheating? Where did this form come from?';
        }

        // get the pages the user has access to.
        if (isset($post["get_pages"])){
            $url = "https://graph.facebook.com/v2.8/me/accounts?access_token=" . get_option( 'disciple_tools_facebook_access_token');
            $request = wp_remote_get($url);

            if( is_wp_error( $request ) ) {
                $this->display_error($request);
            } else {
                $body = wp_remote_retrieve_body( $request );
                $data = json_decode( $body );
                if( ! empty( $data ) && isset($data->data) ) {
                    $pages = array();
                    foreach($data->data as $page){
                        $pages[$page->id] = $page;
                    }
                    update_option("disciple_tools_facebook_pages", $pages);
                }
                if (! empty( $data ) && isset($data->error)){
                    $this->display_error($data->error->message);
                }
            }
        }

        //save changes made to the pages in the page list
        if (isset($post["save_pages"])){
            $facebook_pages = get_option("disciple_tools_facebook_pages", array());
            foreach ($facebook_pages as $id => $facebook_page){
                $integrate = str_replace(' ', '_', $facebook_page->name . "-integrate");
                if (isset($post[$integrate])){
                    $facebook_page->integrate = 1;
                } else {
                    $facebook_page->integrate = 0;
                }
                $report = str_replace(' ', '_', $facebook_page->name . "-report");
                if (isset($post[$report])){
                    $facebook_page->report = 1;
                } else {
                    $facebook_page->report = 0;
                }
                //Add the page to the apps subscriptions (to allow webhooks)
                if ($facebook_page->integrate == 1 && (!isset($facebook_page->subscribed) || (isset($facebook_page->subscribed) && $facebook_page->subscribed != 1))){
                    $url = "https://graph.facebook.com/v2.8/" . $id . "/subscribed_apps?access_token=". $facebook_page->access_token;
                    $request = wp_remote_post($url);
                    if( is_wp_error( $request ) ) {
                        $this->display_error($request);
                    } else {
                        $body = wp_remote_retrieve_body( $request );
                        $data = json_decode( $body );
                        if (! empty( $data ) && isset($data->error)){
                            $this->display_error($data->error->message);
                        }
                        $facebook_page->subscribed = 1;
                    }
                }
                //enable and set up webhooks for getting page feed and conversations
                if (isset($facebook_page->subscribed) && $facebook_page->subscribed == 1 && !isset($facebook_page->webhooks)){
                    $url = "https://graph.facebook.com/v2.8/". $id . "/subscriptions?access_token=" . get_option("disciple_tools_facebook_app_id", "") ."|". get_option("disciple_tools_facebook_app_secret", "") ;
                    $request = wp_remote_post($url, array(
                        'body' => array(
                            'object' => 'page',
                            'callback_url' => $this->get_rest_url() . "/webhook",
                            'verify_token' => $this->tools->Authorize_secret(),
                            'fields' => array('conversations', 'feed')
                        )
                    ));
                    if( is_wp_error( $request ) ) {
                        $this->display_error($request);
                    } else {

                        $body = wp_remote_retrieve_body( $request );
                        $data = json_decode( $body );
                        if (! empty( $data ) && isset($data->error)){
                            $this->display_error($data->error->message);
                        }
                        if (! empty( $data ) && isset($data->success)){
                            $facebook_page->webhooks_set = 1;
                        }
                    }
                }
            }
            update_option("disciple_tools_facebook_pages", $facebook_pages);
        }
    }

    public function get_rest_url(){
        return get_site_url()."/wp-json/". $this->context . "/v" . intval($this->version);
    }
}