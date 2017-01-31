<?php

/**
 * DMM CRM Activation Configurations
 *
 * @class DmmCrm_Config_Activation
 * @version	1.0.0
 * @since 1.0.0
 * @package	DmmCrm_Plugin
 * @author Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class DmmCrm_Classes {

    /**
     * DmmCrm_Config_Activation The single instance of DmmCrm_Config_Activation.
     * @var 	object
     * @access  private
     * @since 	1.0.0
     */
    private static $_instance = null;

    /**
     * Main DmmCrm_Config_Activation Instance
     *
     * Ensures only one instance of DmmCrm_P2P_Metabox is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @return DmmCrm_Classes instance
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



    } // End __construct()

}