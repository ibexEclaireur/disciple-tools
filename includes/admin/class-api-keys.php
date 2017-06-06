<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Class Disciple_Tools_Api_Keys
 * Generate api keys for DT. The api key can be used by external sites or
 * applications where there is no authenticated user.
 */
class Disciple_Tools_Api_Keys {
	/**
	 * @var object instance. The class instance
	 * @access private
	 * @since 0.1
	 */
	private static $_instance = null;

	/**
	 * Main Disciple_Tools_Api_Keys Instance
	 * Ensures only one instance of Disciple_Tools_Api_Keys is loaded or can be loaded.
	 * @since 0.1
	 * @static
	 * @return Disciple_Tools_Api_Keys instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	} // End instance()

	/**
	 * Constructor function.
	 * @access  public
	 * @since   0.1
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_api_keys_menu' ) );
	}

	/**
	 * Create the menu item in the Tools section
	 * @access public
	 * @since 0.1
	 */
	public function add_api_keys_menu() {
		add_submenu_page( 'tools.php', __( 'API Keys (DT)', 'disciple_tools' ),
			__( 'API Keys (DT)', 'disciple_tools' ), 'manage_options', 'api-keys', array( $this, 'api_keys_page' ) );
	}

	/**
	 * Display an admin notice on the page
	 *
	 * @param $notice , the message to display
	 * @param $type , the type of message to display
	 *
	 * @access private
	 * @since 0.1
	 */
	private function admin_notice( $notice, $type ) {
		echo '<div class="notice notice-' . $type . ' is-dismissible"><p>';
		echo $notice;
		echo '</p></div>';
	}

	/**
	 * The API keys page html
	 * @access public
	 * @since 0.1
	 */
	public function api_keys_page() {
		$keys = get_option( "dt_api_keys", array() );
		if ( isset( $_POST["application"]) && !empty($_POST["application"])) {
			$client_id= wordwrap(strtolower($_POST["application"]), 1, '-', 0);
			$token = bin2hex( random_bytes( 32 ) );
			if (!isset($keys[$client_id])) {
				$keys[$client_id] = array( "client_id" => $client_id, "client_token" => $token );
				update_option( "dt_api_keys", $keys );
			} else {
				$this->admin_notice( "Application already exists", "error" );
			}
		} else if (isset($_POST["delete"])){
			if ($keys[$_POST["delete"]]){
				unset($keys[$_POST["delete"]]);
				update_option( "dt_api_keys", $keys );
			}
		}
		include 'views/api-keys-view.php';
	}


	/**
	 * Check to see if an api key and token exist
	 * @param $client_Id
	 * @param $client_token
	 *
	 * @return bool
	 */
	public function check_api_key($client_Id, $client_token){
		$keys = get_option( "dt_api_keys", array());
		return isset($keys[$client_Id]) && $keys[$client_Id]["client_token"] == $client_token;
	}

}