<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Login page modifications
 *
 * @author Chasm Solutions
 * @package Disciple_Tools
 */

class disciple_tools_login {

    // Change homepage url
    public function my_login_logo_url() {
        return home_url();
    }

    // Change title
    public function my_login_logo_url_title() {
        return 'Disciple_Tools';
    }

}




