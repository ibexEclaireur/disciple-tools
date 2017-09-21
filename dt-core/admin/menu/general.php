<?php

/**
 * Disciple Tools
 *
 * @class Disciple_Tools_
 * @version    0.1
 * @since 0.1
 * @package    Disciple_Tools
 * @author Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly
}

class Disciple_Tools_General_Tab {
    
    /**
     * Constructor function.
     * @access  public
     * @since   0.1
     */
    public function __construct () {
    
    } // End __construct()
    
    public function general_options() {
        $html = '';
        $html .= '<div class="wrap"><div id="poststuff"><div id="post-body" class="metabox-holder columns-2">';
        $html .= '<div id="post-body-content">';
        /* Main Column */
        print_r($_POST);
        /* Box */
        $html .= '<table class="widefat striped">
                    <thead><th>Required Notifications</th></thead>
                    <tbody><tr><td>';
        $this->process_user_notifications();
        $html .= $this->user_notifications(); // content for the notifications box
        $html .= '</td></tr></tbody></table>';
        /* End Box */
        
        $html .= '<br>';
        
        /* Box */
        $html .= '<table class="widefat striped">
                    <thead><th>General Settings</th></thead>
                    <tbody><tr><td>';
        $html .= $this->options_box();
        $html .= '</td></tr><tr><td>';
        $html .= '</td></tr></tbody></table>';
        /* End Box */
        
        /* End Main Column */
        $html .= '</div><!-- end post-body-content --><div id="postbox-container-1" class="postbox-container">';
        /* Right Column */
    
        /* Box */
        $html .= '<table class="widefat striped">
                    <thead><th>General Settings</th></thead>
                    <tbody><tr><td>';
        $html .= $this->options_box();
        $html .= '</td></tr><tr><td>';
        $html .= '</td></tr></tbody></table>';
        /* End Box */
    
        /* End Right Column*/
        $html .= '</div><!-- postbox-container 1 --><div id="postbox-container-2" class="postbox-container">';
        $html .= '</div><!-- postbox-container 2 --></div><!-- post-body meta box container --></div><!--poststuff end --></div><!-- wrap end -->';
    
        return $html;
    }
    
    public function options_box() {
        $html = 'field';
        
        
        return $html;
    }
    
    public function user_notifications() {
        // check for default options
        if( !get_option( 'dt_site_notification_options' ) ) {
            $notifications_default = [
                'new' => [
                    'web' => true,
                    'email' => true,
                ],
                'mentions' => [
                    'web' => true,
                    'email' => true,
                ],
                'updates' => [
                    'web' => true,
                    'email' => true,
                ],
                'changes' => [
                    'web' => true,
                    'email' => true,
                ],
                'milestones' => [
                    'web' => true,
                    'email' => true,
                ]
            ];
            add_option('dt_site_notification_options', $notifications_default, '', true );
        }
    
        $site_options = get_option( 'dt_site_notification_options' );
        
        $html = '<p>These are site overrides for individual preferences for notifications. Uncheck if you want, users to make their own decision on which notifications to recieve.</p>';
        $html .= '<form method="post" name="notifications-form">';
        $html .= '<input type="hidden" name="notifications_nonce" id="notifications_nonce" value="' . wp_create_nonce( 'notifications' ) . '" />';
        
        $html .= '<table class="widefat">';
        
        $html .= '<tr><td>New Contacts</td><td>Web <input name="new-web" type="checkbox" '.$this->is_checked( $site_options['new']['web'] ).' /></td><td>Email <input name="new-email" type="checkbox" '.$this->is_checked( $site_options['new']['email'] ).' /></td></tr>';
        $html .= '<tr><td>@Mentions</td><td>Web <input name="mentions-web" type="checkbox" '.$this->is_checked( $site_options['mentions']['web'] ).' /></td><td>Email <input name="mentions-email" type="checkbox" '.$this->is_checked( $site_options['mentions']['email'] ).' /></td></tr>';
        $html .= '<tr><td>Updates Required</td><td>Web <input name="updates-web" type="checkbox" '.$this->is_checked( $site_options['updates']['web'] ).' /></td><td>Email <input name="updates-email" type="checkbox" '.$this->is_checked( $site_options['updates']['email'] ).' /></td></tr>';
        $html .= '<tr><td>Contact Info Changes</td><td>Web <input name="changes-web" type="checkbox" '.$this->is_checked( $site_options['changes']['web'] ).' /></td><td>Email <input name="changes-email" type="checkbox" '.$this->is_checked( $site_options['changes']['email'] ).' /></td></tr>';
        $html .= '<tr><td>Contact Milestones</td><td>Web <input name="milestones-web" type="checkbox" '.$this->is_checked( $site_options['milestones']['web'] ).' /></td><td>Email <input name="milestones-email" type="checkbox" '.$this->is_checked( $site_options['milestones']['email'] ).' /></td></tr>';
    
        $html .= '</table><br><button type="submit" class="button float-right">Save</button> </form>';
        return $html;
    }
    
    /**
     * Process
     */
    public function process_user_notifications() {
        
        if ( isset( $_POST['notifications_nonce'] ) && ! wp_verify_nonce( $_POST['notifications_nonce'], 'notifications' ) ) {
            $site_options = get_option( 'dt_site_notification_options' );
            //TODO check new post info and update site option
            
        }
        
    }
    
    /**
     * Helper function to translate boolean values into 'checked' value for checkbox inputs.
     * @param $value
     *
     * @return string
     */
    public function is_checked( $value ) {
        return $value ? 'checked' : '';
    }
    
    
    
    
}
