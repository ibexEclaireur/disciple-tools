<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

class Disciple_Tools_Notifications_Hook_Field_Updates extends Disciple_Tools_Notifications_Hook_Base {
    
    // TODO configure this for field updates not comments
    
    public function __construct() {
        add_action( 'wp_insert_comment', [ &$this, 'handle_comment_log' ], 10, 2 );
        add_action( 'edit_comment', [ &$this, 'handle_comment_log' ] );
        add_action( 'trash_comment', [ &$this, 'handle_comment_log' ] );
        add_action( 'untrash_comment', [ &$this, 'handle_comment_log' ] );
        add_action( 'spam_comment', [ &$this, 'handle_comment_log' ] );
        add_action( 'unspam_comment', [ &$this, 'handle_comment_log' ] );
        add_action( 'delete_comment', [ &$this, 'handle_comment_log' ] );
        add_action( 'transition_comment_status', [ &$this, 'hooks_transition_comment_status' ], 10, 3 );
        
        parent::__construct();
    }
    
    public function hooks_transition_comment_status( $new_status, $old_status, $comment ) {
        $this->_add_comment_log( $comment->comment_ID, $new_status, $comment );
    }
    
    public function handle_comment_log( $comment_ID, $comment = null ) {
        if ( is_null( $comment ) ) {
            $comment = get_comment( $comment_ID );
        }
        
        $action = 'created';
        switch ( current_filter() ) {
            case 'wp_insert_comment' :
                $action = 1 === (int) $comment->comment_approved ? 'approved' : 'pending';
                break;
            
            case 'edit_comment' :
                $action = 'updated';
                break;
            
            case 'delete_comment' :
                $action = 'deleted';
                break;
            
            case 'trash_comment' :
                $action = 'trashed';
                break;
            
            case 'untrash_comment' :
                $action = 'untrashed';
                break;
            
            case 'spam_comment' :
                $action = 'spammed';
                break;
            
            case 'unspam_comment' :
                $action = 'unspammed';
                break;
        }
        
        $this->_add_comment_log( $comment_ID, $action, $comment );
    }
    
    protected function _add_comment_log( $id, $action, $comment = null ) {
        if ( is_null( $comment ) ) {
            $comment = get_comment( $id );
        }
    
        dt_notification_insert(
            [
                'user_id'               => '',
                'item_id'               => '',
                'secondary_item_id'     => '',
                'component_name'        => '',
                'component_action'      => '',
                'date_notified'         => '',
                'is_new'                => 1,
            ]
        );
    }
    
}
