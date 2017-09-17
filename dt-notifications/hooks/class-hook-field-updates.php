<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

class Disciple_Tools_Notifications_Hook_Field_Updates extends Disciple_Tools_Notifications_Hook_Base {
    
    public function __construct() {
        add_action( "added_post_meta", [ &$this, 'hooks_added_post_meta'], 10, 4 );
        add_action( "updated_post_meta", [ &$this, 'hooks_updated_post_meta'], 10, 4 );
        
        parent::__construct();
    }
    
    public function hooks_added_post_meta ( $mid, $object_id, $meta_key, $meta_value ) {
        
        return $this->hooks_updated_post_meta( $mid, $object_id, $meta_key, $meta_value, true );
        
    }
    
    /**
     * Process specific meta changes and creates notifications for them
     *
     * @param      $meta_id
     * @param      $object_id
     * @param      $meta_key
     * @param      $meta_value
     * @param bool $new
     */
    public function hooks_updated_post_meta ( $meta_id, $object_id, $meta_key, $meta_value, $new = false ) {
    
//        if ($meta_key != 'assigned_to' || $meta_key != 'requires_update') { // ignore all but assigned to
        if ($meta_key != 'assigned_to' ) { // ignore all but assigned to
            return;
        }
        
//        if (empty( $meta_value )) {
//            return;
//        }
        
        switch($meta_key) {
            case 'assigned_to':
                $notification_name = 'assigned_to';
                
                // get user or team assigned_to
                $meta_array = explode( '-', $meta_value ); // Separate the type and id
                $type = $meta_array[0]; // parse type
                
                if($type == 'user') {
                    // check if accepted, return // TODO Are we creating an 'accepted' step to the assignment?
    
                    /**
                     * Delete all notifications with matching post_id and notification_name
                     * This prevents an assigned_to notification remaining in another persons inbox, that has since been
                     * assigned to someone else. The Activity log keeps the historical data, but this notifications table
                     * only should keep real status data.
                     */
                    $this->delete_by_post(
                        $object_id,
                        $notification_name
                    );
                    
                    $notification_note = 'You have been assigned <a href="'.home_url('/') . get_post_type($object_id) .'/' .$object_id. '">' . get_the_title($object_id) . '</a>';
                    
                    // build elements and submit notification
                    $this->add_notification(
                        $user_id = (int) $meta_array[1],
                        $post_id = (int) $object_id,
                        $secondary_item_id = (int) $meta_id,
                        $notification_name,
                        $notification_action = 'alert',
                        $notification_note,
                        $date_notified = current_time('mysql')
                    );
                    
                } else { // if team
                    return; // TODO Find out if we are supporting team assignments. C
                }
                
                
                break;
            case 'requires_update':
                // TODO
                break;
        }
        
        
    }
    
    /**
     * Create notification activity
     *
     * @param int    $user_id               This is the user that the notification is being assigned to
     * @param int    $post_id               This is contacts, groups, locations post type id.
     * @param int    $secondary_item_id
     * @param string $notification_name
     * @param string $notification_action
     * @param string $notification_note
     * @param        $date_notified
     */
    protected function add_notification( int $user_id, int $post_id, int $secondary_item_id, string $notification_name, string $notification_action, string $notification_note, $date_notified ) {
        
        dt_notification_insert(
            [
                'user_id'               => $user_id,
                'post_id'               => $post_id,
                'secondary_item_id'     => $secondary_item_id,
                'notification_name'     => $notification_name,
                'notification_action'   => $notification_action,
                'notification_note'     => $notification_note,
                'date_notified'         => $date_notified,
                'is_new'                => 1,
            ]
        );
        
    }
    
    /**
     * Delete single notification
     *
     * @param int    $user_id
     * @param int    $post_id
     * @param int    $secondary_item_id
     * @param string $notification_name
     * @param        $date_notified
     */
    protected function delete_single_notification( int $user_id, int $post_id, int $secondary_item_id, string $notification_name, $date_notified ) {
        
        dt_notification_delete(
            [
                'user_id'               => $user_id,
                'post_id'               => $post_id,
                'secondary_item_id'     => $secondary_item_id,
                'notification_name'     => $notification_name,
                'date_notified'         => $date_notified,
            ]
        );
        
    }
    
    /**
     * Delete all notifications by post and notification name (i.e. type)
     *
     * @param int $post_id
     * @param int $notification_name
     */
    protected function delete_by_post( int $post_id, string $notification_name ) {
    
        dt_notification_delete_by_post(
            [
                'post_id'               => $post_id,
                'notification_name'     => $notification_name,
            ]
        );
        
    }
    
}
