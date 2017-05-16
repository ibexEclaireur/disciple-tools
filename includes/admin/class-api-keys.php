<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Disciple_Tools_Api_Keys
 * Generate api keys for DT. The api key can be used by external sites or
 * applications where there is no authenticated user.
 */
class Disciple_Tools_Api_Keys
{
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
  public function __construct (){
    add_action('admin_menu', array($this, 'add_api_keys_menu'));
  }

	/**
	 * Create the menu item in the Tools section
   * @access public
   * @since 0.1
	 */
  public function add_api_keys_menu(){
    add_submenu_page('tools.php', __('API Keys (DT)', 'disciple_tools'),
      __('API Keys (DT)', 'disciple_tools'), 'manage_options', 'api-keys', array($this, 'api_keys_page'));
  }

  /**
   * The API keys page html
   * @access public
   * @since 0.1
   */
  public function api_keys_page(){
    ?>
    <h1>API Keys</h1>
    <?php
  }

}