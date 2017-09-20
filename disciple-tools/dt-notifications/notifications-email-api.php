<?php

/**
 * Disciple_Tools_Notifications_Email_API
 *
 * This contains all the send logic for email notifications
 *
 * @class Disciple_Tools_Notifications_Email_API
 * @version    0.1
 * @since 0.1
 * @package    Disciple_Tools
 * @author Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly
}

class Disciple_Tools_Notifications_Email_API {
    
    /**
     * Disciple_Tools_Notifications_Email_API The single instance of Disciple_Tools_Notifications_Email_API.
     * @var     object
     * @access  private
     * @since     0.1
     */
    private static $_instance = null;
    
    /**
     * Main Disciple_Tools_Notifications_Email_API Instance
     *
     * Ensures only one instance of Disciple_Tools_Notifications_Email_API is loaded or can be loaded.
     *
     * @since 0.1
     * @static
     * @return Disciple_Tools_Notifications_Email_API instance
     */
    public static function instance () {
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
    public function __construct () { } // End __construct()
    
    // TODO add logic for email notifications
    // TODO create a place to modify the appearance of emails in the editor
    // TODO add potential for integreting into third-party email services. Look at wp_mail_smtp.
    
}
