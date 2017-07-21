<?php

/**
 * Disciple_Tools_Config_Groups
 * This class serves as master configuration and modification class to the groups post type within the admin screens.
 *
 * @class Disciple_Tools_Config_Contacts
 * @version    0.1
 * @since 0.1
 * @package    Disciple_Tools
 * @author Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Disciple_Tools_Config_Groups {

    /**
     * Disciple_Tools_Config_Groups The single instance of Disciple_Tools_Config_Groups.
     * @var     object
     * @access  private
     * @since     0.1
     */
    private static $_instance = null;

    /**
     * Main Disciple_Tools_Config_Groups Instance
     *
     * Ensures only one instance of Disciple_Tools_Config_Groups is loaded or can be loaded.
     *
     * @since 0.1
     * @static
     * @return Disciple_Tools_Config_Groups instance
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
        add_action( 'admin_menu', array($this, 'remove_default_meta_boxes' ) );
    } // End __construct()

    /**
     * Removes default metaboxes
     * @see https://codex.wordpress.org/Function_Reference/remove_meta_box
     */
    public function remove_default_meta_boxes() {

        remove_meta_box( 'linktargetdiv', 'link', 'normal' );
        remove_meta_box( 'linkxfndiv', 'link', 'normal' );
        remove_meta_box( 'linkadvanceddiv', 'link', 'normal' );
        remove_meta_box( 'postexcerpt', 'groups', 'normal' );
        remove_meta_box( 'trackbacksdiv', 'groups', 'normal' );
        remove_meta_box( 'postcustom', 'groups', 'normal' );
        remove_meta_box( 'commentstatusdiv', 'groups', 'normal' );
        remove_meta_box( 'revisionsdiv', 'groups', 'normal' );
        remove_meta_box( 'slugdiv', 'groups', 'normal' );
        remove_meta_box( 'authordiv', 'groups', 'normal' );
        remove_meta_box( 'sqpt-meta-tags', 'groups', 'normal' );
    }

}