<?php
/**
 * Simple helper class to set something to run once.
 *
 * @package Disciple_Tools
 * @since   1.0.0
 */

if( !class_exists( 'Discple_Tools_Run_Once' ) ) {
    class Disciple_Tools_Run_Once
    {
        function run( $key )
        {
            $test_case = get_option( 'run_once' );
            if( isset( $test_case[ $key ] ) && $test_case[ $key ] ) {
                return false;
            } else {
                $test_case[ $key ] = true;
                update_option( 'run_once', $test_case );

                return true;
            }
        }

        function clear( $key )
        {
            $test_case = get_option( 'run_once' );
            if( isset( $test_case[ $key ] ) ) {
                unset( $test_case[ $key ] );
            }
            update_option( 'run_once', $test_case );
        }
    }
}

