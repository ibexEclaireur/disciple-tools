<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Login page modifications
 *
 * @author Chasm Solutions
 * @package Disciple_Tools
 */

/*
 * Action and Filters
 */
    add_filter( 'login_headerurl', 'my_login_logo_url', 10 );
    add_filter( 'login_headertitle', 'my_login_logo_url_title', 10 );

/*
 * Functions
 */
    // Change homepage url
    function my_login_logo_url() {
        return home_url();
    }

    // Change title
    function my_login_logo_url_title() {
        return 'Disciple_Tools';
    }