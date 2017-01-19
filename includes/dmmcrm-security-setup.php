<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * 
 *
 * @Login Page Modifications 
 *
 * @author Chasm Solutions
 * @package dmmcrm
 */


	/*
	 * Replace default login logo on login page
	 *
	 * TODO: Place this style sheet into the front end style sheet. 
	 */
	function my_login_logo() { 
		$html = '    
		    <style type="text/css">
		        #login h1 a, .login h1 a {
		            background-image: url(' . get_stylesheet_directory_uri() . '/img/dmm-crm-logo.png);
		        }
		    </style>';
		return $html;
	}
	add_action( 'login_enqueue_scripts', 'my_login_logo' );
	
	
	// Change homepage url
	function my_login_logo_url() {
	    return home_url();
	}
	add_filter( 'login_headerurl', 'my_login_logo_url' );
	
	// Change title
	function my_login_logo_url_title() {
	    return 'DMM CRM';
	}
	add_filter( 'login_headertitle', 'my_login_logo_url_title' );
