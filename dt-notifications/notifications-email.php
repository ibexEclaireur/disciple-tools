<?php

/**
 * Disciple_Tools_Notifications_Email
 *
 * @see https://github.com/A5hleyRich/wp-background-processing
 *
 * @class Disciple_Tools_Notifications_Email
 * @version 0.1
 * @since 0.1
 * @package Disciple_Tools
 * @author Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly
}

/**
 * Class Disciple_Tools_Notifications_Email
 */
class Disciple_Tools_Notifications_Email extends Disciple_Tools_Async_Request  {

    /**
     * @var string
     */
    protected $action = 'email_mention';

    /**
     * Handle
     *
     * Override this method to perform any actions required
     * during the async request.
     */
    protected function handle() {
        dt_write_log( 'CUSTOM LOG: This is an email notification' );
    }

}
