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
     * Packages and returns tab page
     * @return string
     */
    public function content() {
        $html = '';
        $html .= '<div class="wrap"><div id="poststuff"><div id="post-body" class="metabox-holder columns-2">';
        $html .= '<div id="post-body-content">';
        /* Main Column */
        print_r( $_POST );
        print '<pre>';
        print_r( get_option( 'dt_site_options' ) );
        print '</pre>';
        
        /* Box */
        $html .= '<table class="widefat striped">
                    <thead><th>Site Notifications</th></thead>
                    <tbody><tr><td>';
        $html .= $this->process_user_notifications().'';
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
    
    /**
     * Builds the user notifications box
     * @return string
     */
    public function user_notifications() {
    
        $site_options = get_option( 'dt_site_options' );
        $notifications = $site_options['notifications'];
        
        $html = '<p>These are site overrides for individual preferences for notifications. Uncheck if you want, users to make their own decision on which notifications to recieve.</p>';
        $html .= '<form method="post" name="notifications-form">';
        $html .= '<input type="hidden" name="notifications_nonce" id="notifications_nonce" value="' . wp_create_nonce( 'notifications' ) . '" />';
        
        $html .= '<table class="widefat">';
        
        $html .= '<tr><td>New Contacts</td><td>Web <input name="new_web" type="checkbox" '.$this->is_checked( $notifications['new_web'] ).' /></td><td>Email <input name="new_email" type="checkbox" '.$this->is_checked( $notifications['new_email'] ).' /></td></tr>';
        $html .= '<tr><td>@Mentions</td><td>Web <input name="mentions_web" type="checkbox" '.$this->is_checked( $notifications['mentions_web'] ).' /></td><td>Email <input name="mentions_email" type="checkbox" '.$this->is_checked( $notifications['mentions_email'] ).' /></td></tr>';
        $html .= '<tr><td>Updates Required</td><td>Web <input name="updates_web" type="checkbox" '.$this->is_checked( $notifications['updates_web'] ).' /></td><td>Email <input name="updates_email" type="checkbox" '.$this->is_checked( $notifications['updates_email'] ).' /></td></tr>';
        $html .= '<tr><td>Contact Info Changes</td><td>Web <input name="changes_web" type="checkbox" '.$this->is_checked( $notifications['changes_web'] ).' /></td><td>Email <input name="changes_email" type="checkbox" '.$this->is_checked( $notifications['changes_email'] ).' /></td></tr>';
        $html .= '<tr><td>Contact Milestones</td><td>Web <input name="milestones_web" type="checkbox" '.$this->is_checked( $notifications['milestones_web'] ).' /></td><td>Email <input name="milestones_email" type="checkbox" '.$this->is_checked( $notifications['milestones_email'] ).' /></td></tr>';
    
        $html .= '</table><br><button type="submit" class="button float-right">Save</button> </form>';
        return $html;
    }
    
    /**
     * Process user notifications box
     */
    public function process_user_notifications() {
        
        if ( isset( $_POST['notifications_nonce'] ) && wp_verify_nonce( $_POST['notifications_nonce'], 'notifications' ) ) {
            
            $site_options = get_option( 'dt_site_options' );
    
            foreach ( $site_options[ 'notifications' ] as $key => $value ) {
                if ( isset( $_POST[ $key ] ) ) {
                    $site_options[ 'notifications' ][ $key ] = true;
                } else {
                    $site_options[ 'notifications' ][ $key ] = false;
                }
            }
            
            update_option( 'dt_site_options', $site_options, true );
            
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
