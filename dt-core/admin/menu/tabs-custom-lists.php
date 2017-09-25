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
        print_r( get_option( 'dt_site_custom_lists' ) );
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
        
        $site_custom_lists = get_option( 'dt_site_custom_lists' );
        if ( $site_custom_lists ) {
            dt_add_site_custom_lists();
        }
        $user_fields = $site_custom_lists[ 'user_fields' ];
        
        $html = '<p>You can extend and configure lists in the contacts module.</p>';
        $html .= '<form method="post" name="user_fields-form">';
        $html .= '<input type="hidden" name="user_fields_nonce" id="user_fields_nonce" value="' . wp_create_nonce( 'user_fields' ) . '" />';
        
        $html .= '<table class="widefat">';
        
        foreach ( $user_fields as $field ) {
            $html .= '<tr><td>' . $field[ 'label' ] . '</td><td>Enabled <input name="' . $field[ 'key' ] . '" type="checkbox" ' . $this->is_checked( $field[ 'enabled' ] ) . ' /></td><td>' . $field[ 'description' ] . ' </td></tr>';
        }
        
        $html .= '</table>';
        $html .= '<br><button type="button" onclick="add_input_fields();" class="button">Add</button> <button type="submit" style="float:right;" class="button">Save</button></form>';
        
        $html .= '<div id="add_input"></div>';
        $html .= '<script>function add_input_fields() {
                      "use strict";
                      jQuery(\'#add_input\').html(\'<form method="post"><input type="hidden" name="add_input_fields" /><table width="100%"><tr><td><hr><br><input type="text" name="label" placeholder="label" /><input type="text" name="description" placeholder="description" /><button type="submit">Add</button></td></tr></table></form>\');
                    }</script>';
        
        return $html;
        
    }
    
    public function process_user_profile_box() {
        
        if ( isset( $_POST[ 'user_fields_nonce' ] ) && wp_verify_nonce( $_POST[ 'user_fields_nonce' ], 'user_fields' ) ) {
            
            $site_options = get_option( 'dt_site_custom_lists' );
            
            foreach ( $site_options[ 'user_fields' ] as $key => $value ) {
                if ( isset( $_POST[ $key ] ) ) {
                    $site_options[ 'user_fields' ][ $key ][ 'enabled' ] = true;
                } else {
                    $site_options[ 'user_fields' ][ $key ][ 'enabled' ] = false;
                }
            }
            
            update_option( 'dt_site_custom_lists', $site_options, true );
            
        } elseif ( isset( $_POST[ 'add_input_fields' ] ) ) {
            
            // build record
            $label       = trim( strip_tags( $_POST[ 'label' ] ) );
            $description = trim( strip_tags( $_POST[ 'description' ] ) );
            $key         = strtolower( str_replace( ' ', '_', $label ) );
            $enabled     = true;
            
            // strip and make lowercase process
            $site_options                          = get_option( 'dt_site_custom_lists' );
            $site_options[ 'user_fields' ][ $key ] = [
                'label'       => $label,
                'key'         => $key,
                'description' => $description,
                'enabled'     => $enabled,
            ];
            
            update_option( 'dt_site_custom_lists', $site_options, true );
            
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
    
    
}
