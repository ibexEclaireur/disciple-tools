<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Profile functions
 *
 * @author Chasm Solutions
 * @package Disciple_Tools
 */

/*
 * Action and Filters
 */

    // Sets the default role to registered.
    add_filter('pre_option_default_role', function($default_role){
        // You can also add conditional tags here and return whatever
        return 'registered'; // This is changed
        return $default_role; // This allows default
    });

/*
 * Functions
 */







