<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

class Disciple_Tools_Notifications_Hook_Comments extends Disciple_Tools_Notifications_Hook_Base {
    
    /**
     * Disciple_Tools_Notifications_Hook_Comments constructor.
     */
    public function __construct() {
        add_action( 'wp_insert_comment', [ &$this, 'filter_comment_for_notification' ], 10, 2 );
        add_action( 'edit_comment', [ &$this, 'filter_comment_for_notification' ] );
        add_action( 'trash_comment', [ &$this, 'filter_comment_for_notification' ] );
        add_action( 'untrash_comment', [ &$this, 'filter_comment_for_notification' ] );
        add_action( 'delete_comment', [ &$this, 'filter_comment_for_notification' ] );
        
        parent::__construct();
    }
    
    
    public function filter_comment_for_notification( $comment_id, $comment = null ) {
    
        if ( is_null( $comment ) ) {
            $comment = get_comment( $comment_id );
        }
        
        if($this->check_for_mention( $comment->comment_content ) == '0') { // fail if no mention found
            return;
        }
        
        $mentioned_user_ids = $this->match_mention( $comment->comment_content ); // fail if no match for mention found
        if(!$mentioned_user_ids){
            return;
        }
        
        foreach($mentioned_user_ids as $mentioned_user_id) {
            
            // build variables
            $post_id = $comment->comment_post_ID;
            $date_notified = $comment->comment_date;
            $author_name = $comment->comment_author;
            $author_url = $comment->comment_author_url;
            
            // call appropriate action
            switch ( current_filter() ) {
                case 'wp_insert_comment' :
                    $notification_action = 'created';
                    
                    $notification_note = '<strong>' . $author_name . '</strong> mentioned you on <a href="'.home_url( '/contacts/' ).$post_id.'">'
                    .get_the_title( $post_id ).'</a> saying, "'. $comment->comment_content . '" ' ;
                    
                    $this->add_mention_notification( $mentioned_user_id, $comment_id, $post_id, $notification_action, $notification_note, $date_notified );
                    break;
                
                case 'edit_comment' :
                    $notification_action = 'updated';
        
                    $notification_note = $author_name . ' ' . $notification_action . ' ' . $comment->comment_content; // TODO improve note grammar
        
                    $this->add_mention_notification( $mentioned_user_id, $comment_id, $post_id, $notification_action, $notification_note, $date_notified );
                    break;
                
                case 'untrash_comment' :
                    $notification_action = 'untrashed';
        
                    $notification_note = $author_name . ' ' . $notification_action . ' ' . $comment->comment_content; // TODO improve note grammar
        
                    $this->add_mention_notification( $mentioned_user_id, $comment_id, $post_id, $notification_action, $notification_note, $date_notified );
                    break;
                
                case 'delete_comment' :
                case 'trash_comment' :
                    $this->delete_mention_notification( $mentioned_user_id, $comment_id, $post_id, $date_notified );
                    break;
                
                default:
                    break;
            }
            
        }
    }
    
    /**
     * Checks for mention in text of comment.
     * If mention is found, returns true. If mention is not found, returns false.
     *
     * @param $comment_content
     *
     * @return bool
     */
    public function check_for_mention( $comment_content ) {
        return preg_match( '/(?<= |^)@([^@ ]+)/', $comment_content );
    }
    
    /**
     * Parse @mention to find user match
     * @param $comment_content
     *
     * @return bool|array
     */
    public function match_mention( $comment_content ) {
        preg_match_all( '/(?<= |^)@([^@ ]+)/', $comment_content, $matches );
        
        $user_ids = [];
        foreach($matches[1] as $match) {
            // get user_id by name match
            $user = get_user_by( 'login', $match );
            if($user) {
                $user_ids[] = $user->ID;
            }
        }
        
        return empty( $user_ids ) ? false : $user_ids;
    }
    
    /**
     * Create notification activity
     *
     * @param $mentioned_user_id
     * @param $comment_id
     * @param $post_id
     * @param $notification_action
     * @param $notification_note
     * @param $date_notified
     */
    protected function add_mention_notification( $mentioned_user_id, $comment_id, $post_id, $notification_action, $notification_note, $date_notified ) {
        
        dt_notification_insert(
            [
                'user_id'               => $mentioned_user_id,
                'item_id'               => $comment_id,
                'secondary_item_id'     => $post_id,
                'notification_name'     => 'mention',
                'notification_action'   => $notification_action,
                'notification_note'     => $notification_note,
                'date_notified'         => $date_notified,
                'is_new'                => 1,
            ]
        );
        
    }
    
    /**
     * Delete notification
     * @param $mentioned_user_id
     * @param $comment_id
     * @param $post_id
     * @param $date_notified
     */
    protected function delete_mention_notification( $mentioned_user_id, $comment_id, $post_id, $date_notified ) {
    
        dt_notification_delete(
            [
                'user_id'               => $mentioned_user_id,
                'item_id'               => $comment_id,
                'secondary_item_id'     => $post_id,
                'notification_name'     => 'mention',
                'date_notified'         => $date_notified,
            ]
        );
    
    }
}
