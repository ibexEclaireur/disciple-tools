<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * DMM CRM Post to Post Metabox for Locations
 *
 * @class DmmCrm_Plugin_Settings
 * @version	1.0.0
 * @since 1.0.0
 * @package	DmmCrm_Plugin
 * @author Chasm.Solutions & Kingdom.Training
 */
final class DmmCrm_P2P_Metabox {
	
	/**
	 * DmmCrm_P2P_Metabox The single instance of DmmCrm_P2P_Metabox.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;
	
	


	/**
	 * Main DmmCrm_Plugin_Settings Instance
	 *
	 * Ensures only one instance of DmmCrm_Plugin_Settings is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @return Main DmmCrm_Plugin_Settings instance
	 */
	public static function instance () {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 */
	public function __construct () {
		if ( is_admin() ) {
			global $pagenow;
			
			
		}
	} // End __construct()
	
}