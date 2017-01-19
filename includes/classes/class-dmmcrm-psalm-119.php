<?php
/**
 * Class that adds exerpts from Psalm 119 to the admin panel header.
 *
 *
 *
 * @author Chasm Solutions
 * @package
 *
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Psalm 119 added to the admin header
 *
 * @class Psalm_119
 * @version	1.0.0
 * @since 1.0.0
 * @package	DmmCrm_Plugin
 * @author Chasm.Solutions & Kingdom.Training
 */
final class Psalm_119 {
	/**
	 * Psalm_119 The single instance of Psalm_119.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;
	
	/**
	 * Main Psalm_119 Instance
	 *
	 * Ensures only one instance of Psalm_119 is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @return Main Psalm_119 instance
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
		add_action( 'admin_notices', array($this, 'get_psalm') );
		add_action( 'admin_head', array($this, 'psalm_css') );
	} // End __construct()
	
	
	protected function get_psalm_exerpt() {
		/** These are exerpts from Psalm 119 */
		$lyrics = "Open my eyes that I may see
	Blessed are those who keep His statutes
	I am a stranger on earth
	Your statutes are my delight
	I seek You with all my heart
	Do not let me stray from Your commands
	I have hidden Your word in my heart
	Preserve my life according to Your word
	My soul is weary with sorrow; strengthen me according to Your word
	Give me understanding, so that I may keep Your law
	Turn my eyes away from worthless things
	Preserve my life according to Your word
	May Your unfailing love come to me, Lord
	I will always obey Your law
	I will speak of Your statutes before kings
	Remember Your word to Your servant
	Your promise preserves my life
	You are my portion, Lord
	I have sought Your face with all my heart
	Be gracious to me according to Your promise
	I am a friend to all who fear You
	Teach me knowledge and good judgment
	You are good, and what You do is good
	Your hands made me and formed me
	May Your unfailing love be my comfort
	Praise be to You, Lord; teach me Your decrees";
	
		// Here we split it into lines
		$lyrics = explode( "\n", $lyrics );
	
		// And then randomly choose a line
		return wptexturize( $lyrics[ mt_rand( 0, count( $lyrics ) - 1 ) ] );
	}
	
	// This just echoes the chosen line, we'll position it later
	public function get_psalm() {
		$chosen = $this->get_psalm_exerpt();
		echo "<p id='psalm'>$chosen</p>";
	}
	
	// We need some CSS to position the paragraph
	public function psalm_css() {
		// This makes sure that the positioning is also good for right-to-left languages
		$x = is_rtl() ? 'left' : 'right';
	
		echo "
		<style type='text/css'>
		#psalm {
			float: $x;
			padding-$x: 15px;
			padding-top: 5px;		
			margin: 0;
			font-size: 11px;
		}
		</style>
		";
	}

}