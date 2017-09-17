<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

class Disciple_Tools_Notifications_Hook_Field_Updates extends Disciple_Tools_Notifications_Hook_Base {
    
    // TODO configure this for field updates not comments
    
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
     * @param      $meta_id
     * @param      $object_id
     * @param      $meta_key
     * @param      $meta_value
     * @param bool $new
     */
    public function hooks_updated_post_meta ( $meta_id, $object_id, $meta_key, $meta_value, $new = false ) {
        global $wpdb;
        
        if ($meta_key != 'assigned_to' || $meta_key != 'requires_update') { // ignore all but assigned to
            return;
        }
        
        if (empty( $meta_value )) {
            return;
        }
        
        $parent_post = get_post( $object_id, ARRAY_A ); // get object info
        
        switch($meta_key) {
            case 'assigned_to':
                $notification_name = 'assigned_to';
                
                // get user or team assigned to
                $meta_array = explode( '-', $meta_value ); // Separate the type and id
                $type = $meta_array[0]; // Build variables
                $id = $meta_array[1];
                
                
                // check if accepted, return // TODO Are we creating an accepted step to the assignment?
                
                // search and delete all other new notifications for this post_id. This should clean other evidence of multiple assignment decisions.
                // Activity log will track long term the activity. But notification should just represent reality.
                
    
                // create notification for assignment
                
                
                // check if team assignment, then loop notifications for each person on the team
    
                // add notification
                $this->add_notification(
                    $user_id,
                    $item_id,
                    $secondary_item_id,
                    $notification_name,
                    $notification_action,
                    $notification_note,
                    $date_notified
                );
                
                break;
            case 'requires_update':
                $notification_name = 'requires_update';
    
                
                // add notification
                $this->add_notification(
                    $user_id,
                    $item_id,
                    $secondary_item_id,
                    $notification_name,
                    $notification_action,
                    $notification_note,
                    $date_notified
                );
                
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
                'item_id'               => $post_id,
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
     * Delete notification
     *
     * @param $user_id
     * @param $item_id
     * @param $secondary_item_id
     * @param $notification_name
     * @param $date_notified
     */
    protected function delete_notification( $user_id, $item_id, $secondary_item_id, $notification_name, $date_notified ) {
        
        dt_notification_delete(
            [
                'user_id'               => $user_id,
                'item_id'               => $item_id,
                'secondary_item_id'     => $secondary_item_id,
                'notification_name'     => $notification_name,
                'date_notified'         => $date_notified,
            ]
        );
        
    }
    
}
