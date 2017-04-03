<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Disciple_Tools_Activity_Hooks {
	
	public function __construct() {
		// Load abstract class.
		include( 'hooks/abstract-class-hook-base.php' );
		
		// Load all our hooks.
		include( 'hooks/class-hook-user.php' );
		include( 'hooks/class-hook-attachment.php' );
		include( 'hooks/class-hook-posts.php' );
		include( 'hooks/class-hook-taxonomy.php' );
		include( 'hooks/class-hook-core.php' );
		include( 'hooks/class-hook-export.php' );
		include( 'hooks/class-hook-comments.php' );
		
		new Disciple_Tools_Hook_User();
		new Disciple_Tools_Hook_Attachment();
		new Disciple_Tools_Hook_Posts();
		new Disciple_Tools_Hook_Taxonomy();
		new Disciple_Tools_Hook_Theme();
		new Disciple_Tools_Hook_Core();
		new Disciple_Tools_Hook_Export();
		new Disciple_Tools_Hook_Comments();
	}
}
