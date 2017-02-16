<?php

/**
 * Counter factory for reporting
 *
 * @package   DRM
 * @author 	  Chasm Solutions <chasm.crew@chasm.solutions>
 * @link      https://github.com/ChasmSolutions
 * @license   GPL-3.0
 * @version   0.1
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class drm_counter_factory {

	/**
	 * drm_counter_factory The single instance of drm_counter_factory.
	 * @var 	object
	 * @access  private
	 * @since 	0.1
	 */
	private static $_instance = null;

	/**
	 * Main drm_counter_factory Instance
	 *
	 * Ensures only one instance of DmmCrm_P2P_Metabox is loaded or can be loaded.
	 *
	 * @since 0.1
	 * @static
	 * @return drm_counter_factory instance
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
	public function __construct ( ) {



		/*include_once ('counters/counter-parent-generations.php');
		$this->drm_counter($target, $type, $question, $count, $return);*/


	} // End __construct

	public function contacts_count () {
		$the_query = get_posts('post_type=contacts');
	}

	public function drm_counter( $target, $type, $question, $count, $return ) {

		/**
		 * @var "target"    This is the id number of the contact or group
		 * @var "type"      This is either "group" or "contact"
		 * @var "question"  This is name of the counter we are calling for information.
		 * @var "count"     This is number of items to return
		 * @var "return"    This is the type of answer returned
		 */

		/*switch( $question ) {

			case 'read-write':
				$user = new Admin();
				break;

			case 'help':
				$user = new Volunteer();
				break;

			case 'read':
				$user = new Reader();
				break;

			default:
				$user = null;
				break;

		}

		return $user;*/

	}
}