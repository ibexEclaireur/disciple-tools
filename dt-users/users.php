<?php
/**
 * Contains create, update and delete functions for users, wrapping access to
 * the database
 *
 *
 * @package  Disciple_Tools
 * @category Plugin
 * @author   Chasm.Solutions & Kingdom.Training
 * @since    0.1
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

/**
 * Class Disciple_Tools_Users
 *
 * Functions for creating, finding, updating or deleting contacts
 */


class Disciple_Tools_Users {


    public static function get_assignable_users(){
        // TODO Shouldn't this filter out prayer, subscriber, etc. roles. These users are not assignable. Right? -CW
        $users = get_users();
        $list = [];
        foreach($users as $user){
            $list[] = [
                "display_name" => $user->display_name,
                "ID" => $user->ID
            ];
        }
        return $list;
    }
}
