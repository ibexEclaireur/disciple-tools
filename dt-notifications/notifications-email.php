<?php

/**
 * Disciple_Tools_Notifications_Email
 *
 * @see     https://github.com/A5hleyRich/wp-background-processing
 * @class   Disciple_Tools_Notifications_Email
 * @version 0.1
 * @since   0.1
 * @package Disciple_Tools
 * @author  Chasm.Solutions & Kingdom.Training
 */

if( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Class Disciple_Tools_Notifications_Email
 */
class Disciple_Tools_Notifications_Email extends Disciple_Tools_Async_Task
{

    protected $action = 'email_notification';

    /**
     * Prepare data for the asynchronous request
     *
     * @throws Exception If for any reason the request should not happen
     *
     * @param array $data An array of data sent to the hook
     *
     * @return array
     */
    protected function prepare_data( $data )
    {
        dt_write_log( '@prepare_data' );
        $user_id = $data[ 0 ][ 'user_id' ]; // TODO add processing on of data and throw any exceptions
        $user = get_userdata( $user_id );
        $data[ 0 ][ 'email' ] = $user->user_email;

        dt_write_log( $user_id );

        return $data;
    }

    /**
     * Run the async task action
     */
    protected function run_action()
    {

        // TODO: This section is not working properly. I'm going around it somehow with the init hook below.

        do_action( "wp_async_$this->action", '' );
        dt_write_log( '@run_action complete' );
    }

}

/**
 * Sends email after load.
 * TODO: This is not properly using the class to run action on a hook. It is instead watching for the wp_remote_post post and catching that and processing, while loading the function through an init hook.
 */
function dt_send_email()
{
    if( isset( $_POST[ 'action' ] ) && $_POST[ 'action' ] == 'wp_async_email_notification' ) {

        dt_write_log( '@dt_send_email to ' . $_POST[ 0 ][ 'email' ] . ' saying ' . $_POST[ 0 ][ 'message' ] );

        $sent = wp_mail( $_POST[ 0 ][ 'email' ], $_POST[ 0 ][ 'subject' ], $_POST[ 0 ][ 'message' ] );

        if( $sent ) {
            dt_write_log( 'Mail was sent!' );
        } else {
            dt_write_log( 'Mail failed to send. Boo.' );
        }
    }
}
add_action( 'init', 'dt_send_email' );


