<?php

/**
 * Disciple Tools
 *
 * @class      Disciple_Tools_
 * @version    0.1
 * @since      0.1
 * @package    Disciple_Tools
 * @author     Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Disciple_Tools_Custom_Lists_Tab {
    
    
    
    /**
     * Packages and returns tab page
     *
     * @return string
     */
    public function content() {
        $html = '';
        $html .= '<div class="wrap"><div id="poststuff"><div id="post-body" class="metabox-holder columns-2">';
        $html .= '<div id="post-body-content">';
        /* Main Column */
        
        print '<pre>';
                print_r( $_POST );
                print_r( dt_get_site_custom_lists( $list_title = NULL ) );
        print '</pre>';
    
        /* Box */
        $html .= '<table class="widefat striped">
                    <thead><th>User Profile</th></thead>
                    <tbody><tr><td>';
        
        $this->process_user_profile_box();
        $html .= $this->user_profile_box();
    
        $html .= '</td></tr></tbody></table><br>';
        /* End Box */
        
        /* Box */
        $html .= '<table class="widefat striped">
                    <thead><th>Contacts</th></thead>
                    <tbody><tr><td>';
        
        
        $html .= '</td></tr></tbody></table><br>';
        /* End Box */
        
        
        /* Box */
        $html .= '<table class="widefat striped">
                    <thead><th>Groups</th></thead>
                    <tbody><tr><td>';
        
        
        $html .= '</td></tr></tbody></table><br>';
        /* End Box */
        
        
        /* End Main Column */
        $html .= '</div><!-- end post-body-content --><div id="postbox-container-1" class="postbox-container">';
        /* Right Column */
        
        /* Box */
        $html .= '<table class="widefat striped">
                    <thead><th>Instructions</th></thead>
                    <tbody><tr><td>';
        
        $html .= '</td></tr></tbody></table><br>';
        /* End Box */
        
        /* End Right Column*/
        $html .= '</div><!-- postbox-container 1 --><div id="postbox-container-2" class="postbox-container">';
        $html .= '</div><!-- postbox-container 2 --></div><!-- post-body meta box container --></div><!--poststuff end --></div><!-- wrap end -->';
        
        return $html;
    }
    
    public function user_profile_box() {
        
        $site_options  = get_option( 'dt_site_options' );
        $notifications = $site_options[ 'notifications' ];
    
        $html = '<p>You can extend and configure lists in the contacts module.</p>';
        $html .= '<form method="post" name="notifications-form">';
        $html .= '<input type="hidden" name="notifications_nonce" id="notifications_nonce" value="' . wp_create_nonce( 'notifications' ) . '" />';
    
        $html .= '<table class="widefat">';
    
        $html .= '<tr><td>New Contacts</td><td>Web <input name="new_web" type="checkbox" ' . $this->is_checked( $notifications[ 'new_web' ] ) . ' /></td><td>Email <input name="new_email" type="checkbox" ' . $this->is_checked( $notifications[ 'new_email' ] ) . ' /></td></tr>';
        $html .= '<tr><td>@Mentions</td><td>Web <input name="mentions_web" type="checkbox" ' . $this->is_checked( $notifications[ 'mentions_web' ] ) . ' /></td><td>Email <input name="mentions_email" type="checkbox" ' . $this->is_checked( $notifications[ 'mentions_email' ] ) . ' /></td></tr>';
        $html .= '<tr><td>Updates Required</td><td>Web <input name="updates_web" type="checkbox" ' . $this->is_checked( $notifications[ 'updates_web' ] ) . ' /></td><td>Email <input name="updates_email" type="checkbox" ' . $this->is_checked( $notifications[ 'updates_email' ] ) . ' /></td></tr>';
        $html .= '<tr><td>Contact Info Changes</td><td>Web <input name="changes_web" type="checkbox" ' . $this->is_checked( $notifications[ 'changes_web' ] ) . ' /></td><td>Email <input name="changes_email" type="checkbox" ' . $this->is_checked( $notifications[ 'changes_email' ] ) . ' /></td></tr>';
        $html .= '<tr><td>Contact Milestones</td><td>Web <input name="milestones_web" type="checkbox" ' . $this->is_checked( $notifications[ 'milestones_web' ] ) . ' /></td><td>Email <input name="milestones_email" type="checkbox" ' . $this->is_checked( $notifications[ 'milestones_email' ] ) . ' /></td></tr>';
    
        $html .= '</table><br><span style="float:right;"><button type="submit" class="button float-right">Save</button> </span></form>';
    
        return $html;
        
    }
    
    public function process_user_profile_box() {
        
        $list = [
            'sample' => 'sample value'
        ];
        
        return ;
    }
    
    /**
     * Helper function to translate boolean values into 'checked' value for checkbox inputs.
     *
     * @param $value
     *
     * @return string
     */
    public function is_checked( $value ) {
        return $value ? 'checked' : '';
    }
    
    
    
    
}
