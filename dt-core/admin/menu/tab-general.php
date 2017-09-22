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

class Disciple_Tools_General_Tab {
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
//        print_r( $_POST );
//        print_r( get_option( 'dt_site_options' ) );
        print '</pre>';
        
        /* Box */
        $html .= '<table class="widefat striped">
                    <thead><th>Site Notifications</th></thead>
                    <tbody><tr><td>';
        
        $this->process_user_notifications();
        $html .= $this->user_notifications(); // content for the notifications box
        
        $html .= '</td></tr></tbody></table><br>';
        /* End Box */
        
        
        /* Box */
        $html .= '<table class="widefat striped">
                    <thead><th>Reports Settings</th></thead>
                    <tbody><tr><td>';
        
        $this->process_reports();
        $html .= $this->reports();
        
        $html .= '</td></tr></tbody></table><br>';
        /* End Box */
    
        /* Box */
        $html .= '<table class="widefat striped">
                    <thead><th>Extended Modules</th></thead>
                    <tbody><tr><td>';
    
        $this->process_extension_modules();
        $html .= $this->extension_modules();
    
        $html .= '</td></tr></tbody></table>';
        /* End Box */
        
        /* End Main Column */
        $html .= '</div><!-- end post-body-content --><div id="postbox-container-1" class="postbox-container">';
        /* Right Column */
        
        /* Box */
        $html .= '<table class="widefat striped">
                    <thead><th>Instructions</th></thead>
                    <tbody><tr><td>';
        
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
     *
     * @return string
     */
    public function user_notifications() {
        
        $site_options  = get_option( 'dt_site_options' );
        $notifications = $site_options[ 'notifications' ];
        
        $html = '<p>These are site overrides for individual preferences for notifications. Uncheck if you want, users to make their own decision on which notifications to recieve.</p>';
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
    
    /**
     * Process user notifications box
     */
    public function process_user_notifications() {
        
        if ( isset( $_POST[ 'notifications_nonce' ] ) && wp_verify_nonce( $_POST[ 'notifications_nonce' ], 'notifications' ) ) {
            
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
     *
     * @param $value
     *
     * @return string
     */
    public function is_checked( $value ) {
        return $value ? 'checked' : '';
    }
    
    /**
     * @return string
     */
    public function reports() {
        
        $site_options  = get_option( 'dt_site_options' );
        $daily_reports = $site_options[ 'daily_reports' ];
        
        $html = '<p>These are regular services that run to check and build reports on integrations and system status.</p>';
        $html .= '<form method="post" name="daily_reports_form">';
        $html .= '<input type="hidden" name="daily_reports_nonce" id="daily_reports_nonce" value="' . wp_create_nonce( 'daily_reports' ) . '" />';
        
        $html .= '<table class="widefat">';
        
        $html .= '<tr><td>Build Report for Contacts</td><td><input name="build_report_for_contacts" type="checkbox" ' . $this->is_checked( $daily_reports[ 'build_report_for_contacts' ] ) . ' /></td></tr>';
        $html .= '<tr><td>Build Report for Groups</td><td><input name="build_report_for_groups" type="checkbox" ' . $this->is_checked( $daily_reports[ 'build_report_for_groups' ] ) . ' /></td></tr>';
        $html .= '<tr><td>Build Report for Facebook</td><td><input name="build_report_for_facebook" type="checkbox" ' . $this->is_checked( $daily_reports[ 'build_report_for_facebook' ] ) . ' /></td></tr>';
        $html .= '<tr><td>Build Report for Twitter</td><td><input name="build_report_for_twitter" type="checkbox" ' . $this->is_checked( $daily_reports[ 'build_report_for_twitter' ] ) . ' /></td></tr>';
        $html .= '<tr><td>Build Report for Analytics</td><td><input name="build_report_for_analytics" type="checkbox" ' . $this->is_checked( $daily_reports[ 'build_report_for_analytics' ] ) . ' /></td></tr>';
        $html .= '<tr><td>Build Report for Adwords</td><td><input name="build_report_for_adwords" type="checkbox" ' . $this->is_checked( $daily_reports[ 'build_report_for_adwords' ] ) . ' /></td></tr>';
        $html .= '<tr><td>Build Report for Mailchimp</td><td><input name="build_report_for_mailchimp" type="checkbox" ' . $this->is_checked( $daily_reports[ 'build_report_for_mailchimp' ] ) . ' /></td></tr>';
        $html .= '<tr><td>Build Report for Youtube</td><td><input name="build_report_for_youtube" type="checkbox" ' . $this->is_checked( $daily_reports[ 'build_report_for_youtube' ] ) . ' /></td></tr>';
        
        $html .= '</table><br><span style="float:right;"><button type="submit" class="button float-right">Save</button></span>  </form>';
        
        return $html;
        
    }
    
    /**
     *
     */
    public function process_reports() {
        
        if ( isset( $_POST[ 'daily_reports_nonce' ] ) && wp_verify_nonce( $_POST[ 'daily_reports_nonce' ], 'daily_reports' ) ) {
        
            $site_options = get_option( 'dt_site_options' );
        
            foreach ( $site_options[ 'daily_reports' ] as $key => $value ) {
                if ( isset( $_POST[ $key ] ) ) {
                    $site_options[ 'daily_reports' ][ $key ] = true;
                } else {
                    $site_options[ 'daily_reports' ][ $key ] = false;
                }
            }
        
            update_option( 'dt_site_options', $site_options, true );
        
        }
        
    }
    
    public function extension_modules() {
    
        $site_options  = get_option( 'dt_site_options' );
        $extension_modules = $site_options[ 'extension_modules' ];
    
        $html = '<form method="post" name="extension_modules_form">';
        $html .= '<input type="hidden" name="extension_modules_nonce" id="extension_modules_nonce" value="' . wp_create_nonce( 'extension_modules' ) . '" />';
    
        $html .= '<table class="widefat">';
    
        $html .= '<tr><td>Add People Groups Module</td><td><input name="add_people_groups" type="checkbox" ' . $this->is_checked( $extension_modules[ 'add_people_groups' ] ) . ' /></td></tr>';
        $html .= '<tr><td>Add Asset Mapping</td><td><input name="add_assetmapping" type="checkbox" ' . $this->is_checked( $extension_modules[ 'add_assetmapping' ] ) . ' /></td></tr>';
        $html .= '<tr><td>Add Prayer </td><td><input name="add_prayer" type="checkbox" ' . $this->is_checked( $extension_modules[ 'add_prayer' ] ) . ' /></td></tr>';
    
        $html .= '</table><br><span style="float:right;"><button type="submit" class="button float-right">Save</button> </span></form>';
    
        return $html;
        
    }
    
    public function process_extension_modules() {
    
        if ( isset( $_POST[ 'extension_modules_nonce' ] ) && wp_verify_nonce( $_POST[ 'extension_modules_nonce' ], 'extension_modules' ) ) {
        
            $site_options = get_option( 'dt_site_options' );
        
            foreach ( $site_options[ 'extension_modules' ] as $key => $value ) {
                if ( isset( $_POST[ $key ] ) ) {
                    $site_options[ 'extension_modules' ][ $key ] = true;
                } else {
                    $site_options[ 'extension_modules' ][ $key ] = false;
                }
            }
        
            update_option( 'dt_site_options', $site_options, true );
        
        }
        
    }
    
}