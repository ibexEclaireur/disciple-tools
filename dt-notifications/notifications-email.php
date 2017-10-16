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
     * @throws Exception If for any reason the request should not happen.
     *
     * @param array $data An array of data sent to the hook
     *
     * @return array
     */
    protected function prepare_data( $data )
    {
        return $data;
    }

    /**
     * Run the async task action
     * Used when loading long running process with add_action
     */
    protected function run_action()
    {
        $email = sanitize_email( $_POST[ 0 ][ 'email' ] );
        $subject = sanitize_text_field( $_POST[ 0 ][ 'subject' ] );
        $message = sanitize_text_field( $_POST[ 0 ][ 'message' ] );

        do_action( "dt_async_$this->action", $email, $subject, $message );

    }

    public function send_email()
    {
        // Nonce validation through custom nonce process inside Disciple_Tools_Async_Task to allow for asynchronous processing
        // @codingStandardsIgnoreLine
        if( ( isset( $_POST[ 'action' ] ) && sanitize_key( wp_unslash( $_POST[ 'action' ] ) ) == 'dt_async_email_notification' ) && ( isset( $_POST[ '_nonce' ] ) ) && $this->verify_async_nonce( sanitize_key( wp_unslash( $_POST[ '_nonce' ] ) ) ) ) {

            // @codingStandardsIgnoreLine
            $sent = wp_mail( sanitize_email( $_POST[ 0 ][ 'email' ] ), sanitize_text_field( $_POST[ 0 ][ 'subject' ] ), sanitize_text_field( $_POST[ 0 ][ 'message' ] ) );

        }
    }
}

/**
 *
 */
function dt_load_async_email()
{
    if( isset( $_POST[ '_wp_nonce' ] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST[ '_wp_nonce' ] ) ) ) && isset( $_POST[ 'action' ] ) && sanitize_key( wp_unslash( $_POST[ 'action' ] ) ) == 'dt_async_email_notification' ) {

        dt_write_log( '@dt_load_async_email' );
        $send_email = new Disciple_Tools_Notifications_Email();
        $send_email->send_email();
    }
}
add_action( 'init', 'dt_load_async_email' );

/**
 * Shared DT email function
 *
 * @param $email
 * @param $subject
 * @param $message
 */
function dt_send_email( $email, $subject, $message )
{
    // Check permission to send email
    // TODO

    // Sanitize
    $email = sanitize_email( $email );
    $subject = sanitize_text_field( $subject );
    $message = sanitize_text_field( $message );

    // Send email
    $send_email = new Disciple_Tools_Notifications_Email();
    $send_email->launch(
        [
            'email'   => $email,
            'subject' => $subject,
            'message' => $message,
        ]
    );
    dt_write_log( '@dt_send_email' );
}

