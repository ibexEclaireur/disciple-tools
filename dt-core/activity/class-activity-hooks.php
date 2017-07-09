<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Disciple_Tools_Activity_Hooks {

    /**
     * Disciple_Tools_Admin_Menus The single instance of Disciple_Tools_Admin_Menus.
     * @var 	object
     * @access  private
     * @since 	0.1
     */
    private static $_instance = null;

    /**
     * Main Disciple_Tools_Admin_Menus Instance
     *
     * Ensures only one instance of Disciple_Tools_Admin_Menus is loaded or can be loaded.
     *
     * @since 0.1
     * @static
     * @return Disciple_Tools_Activity_Hooks instance
     */
    public static function instance () {
        if ( is_null( self::$_instance ) )
            self::$_instance = new self();
        return self::$_instance;
    } // End instance()
	
	public function __construct() {
		// Load abstract class.
		include('hooks/abstract-class-hook-base.php');
		
		// Load all our hooks.
		include('hooks/class-hook-user.php');
		include('hooks/class-hook-attachment.php');
		include('hooks/class-hook-posts.php');
		include('hooks/class-hook-taxonomy.php');
		include('hooks/class-hook-export.php');
		include('hooks/class-hook-comments.php');
		include('hooks/class-hook-theme.php');

		new Disciple_Tools_Hook_User();
		new Disciple_Tools_Hook_Attachment();
		new Disciple_Tools_Hook_Posts();
		new Disciple_Tools_Hook_Taxonomy();
		new Disciple_Tools_Hook_Export();
		new Disciple_Tools_Hook_Comments();
        new Disciple_Tools_Hook_Theme();
	}
}
