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

if( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Class Disciple_Tools_Custom_Lists_Tab
 */
class Disciple_Tools_Custom_Lists_Tab
{
    
    /**
     * Packages and returns tab page
     *
     * @return string
     */
    public function content()
    {
        print '<pre>';
        //        print_r( $_POST );
//                print_r( get_option( 'dt_site_custom_lists' ) );
        print '</pre>';
        
        $html = '';
        $html .= '<div class="wrap"><div id="poststuff"><div id="post-body" class="metabox-holder columns-2">';
        $html .= '<div id="post-body-content">';
        /* Main Column */
        
        /* Box */
        $html .= '<table class="widefat striped">
                    <thead><th>User (Worker) Contact Profile</th></thead>
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
    
    /**
     * Build the contact settings box.
     *
     * @return string
     */
    public function user_profile_box()
    {
        $html = '';
        $html .= '<form method="post" name="user_fields_form">';
        $html .= '<button type="submit" class="button-like-link" name="user_fields_reset" value="1">reset</button>';
        $html .= '<p>You can add or remove types of contact fields for worker profiles.</p>';
        $html .= '<input type="hidden" name="user_fields_nonce" id="user_fields_nonce" value="' . wp_create_nonce( 'user_fields' ) . '" />';
        $html .= '<table class="widefat">';
        
        // custom list block
        $site_custom_lists = get_option( 'dt_site_custom_lists' );
        if( !$site_custom_lists ) {
            dt_add_site_custom_lists();
        }
        $user_fields = $site_custom_lists[ 'user_fields' ];
        foreach( $user_fields as $field ) {
            $html .= '<tr><td>' . esc_attr( $field[ 'label' ] ) . '</td><td>Enabled <input name="user_fields[' . $field[ 'key' ] . ']" type="checkbox" ' . $this->is_checked( $field[ 'enabled' ] ) . ' /></td><td>' . esc_attr( $field[ 'description' ] ) . ' </td><td><button type="submit" name="delete_field" value="' . $field[ 'key' ] . '" class="button small" >delete</button> </td></tr>';
        }
        // end list block
        
        $html .= '</table>';
        $html .= '<br><button type="button" onclick="jQuery(\'#add_input\').toggle();" class="button">Add</button>
                        <button type="submit" style="float:right;" class="button">Save</button>';
        $html .= '<div id="add_input" style="display:none;">';
        $html .= '<table width="100%"><tr><td><hr><br>
                    <input type="text" name="add_input_field[label]" placeholder="label" />
                    <input type="text" name="add_input_field[description]" placeholder="description" />
                    <button type="submit">Add</button>
                    </td></tr></table></div>';
        
        $html .= '</form>';
        
        return $html;
        
    }
    
    /**
     * Process user profile settings
     */
    public function process_user_profile_box()
    {
        
        if( isset( $_POST[ 'user_fields_nonce' ] ) ) {
            
            if( !wp_verify_nonce( sanitize_key( $_POST[ 'user_fields_nonce' ] ), 'user_fields' ) ) {
                return;
            }
            
            // Process current fields submitted
            $site_custom_lists = get_option( 'dt_site_custom_lists' );
            if( !$site_custom_lists ) {
                $site_custom_lists = dt_add_site_custom_lists();
            }
            
            foreach( $site_custom_lists[ 'user_fields' ] as $key => $value ) {
                if( isset( $_POST[ 'user_fields' ][ $key ] ) ) {
                    $site_custom_lists[ 'user_fields' ][ $key ][ 'enabled' ] = true;
                } else {
                    $site_custom_lists[ 'user_fields' ][ $key ][ 'enabled' ] = false;
                }
            }
            
            // Process new field submitted
            if( !empty( $_POST[ 'add_input_field' ][ 'label' ] ) ) {
                
                $label = sanitize_text_field( wp_unslash( $_POST[ 'add_input_field' ][ 'label' ] ) );
                if( empty( $label ) ) {
                    return;
                }
                
                if( !empty( $_POST[ 'add_input_field' ][ 'description' ] ) ) {
                    $description = sanitize_text_field( wp_unslash( $_POST[ 'add_input_field' ][ 'description' ] ) );
                } else {
                    $description = '';
                }
                
                $key = 'dt_user_' . sanitize_key( strtolower( str_replace( ' ', '_', $label ) ) );
                $enabled = true;
                
                // strip and make lowercase process
                $site_custom_lists[ 'user_fields' ][ $key ] = [
                    'label'       => $label,
                    'key'         => $key,
                    'description' => $description,
                    'enabled'     => $enabled,
                ];
                
            }
            
            // Process a field to delete.
            if( isset( $_POST[ 'delete_field' ] ) ) {
                
                $delete_key = sanitize_text_field( wp_unslash( $_POST[ 'delete_field' ] ) );
                
                unset( $site_custom_lists[ 'user_fields' ][ $delete_key ] );
                
                //TODO: Consider adding a database query to delete all instances of this key from usermeta
                
            }
            
            // Process reset request
            if( isset( $_POST[ 'user_fields_reset' ] ) ) {
                
                unset( $site_custom_lists[ 'user_fields' ] );
                
                $site_custom_lists[ 'user_fields' ] = dt_get_site_custom_lists( 'user_fields' );
            }
            
            // Update the site option
            update_option( 'dt_site_custom_lists', $site_custom_lists, true );
            
        }
    }
    
    /**
     * Helper function to translate boolean values into 'checked' value for checkbox inputs.
     *
     * @param $value
     *
     * @return string
     */
    public function is_checked( $value )
    {
        return $value ? 'checked' : '';
    }
    
    /**
     * Build the contact settings box.
     *
     * @return string
     */
    public function source_box()
    {
        $html = '';
        $html .= '<form method="post" name="user_fields-form">';
        $html .= '<button type="submit" class="button-like-link" name="user_fields_reset" value="1">reset</button>';
        $html .= '<p>You can add or remove types of contact fields for worker profiles.</p>';
        $html .= '<input type="hidden" name="user_fields_nonce" id="user_fields_nonce" value="' . wp_create_nonce( 'user_fields' ) . '" />';
        $html .= '<table class="widefat">';
        
        // custom list block
        $site_custom_lists = get_option( 'dt_site_custom_lists' );
        if( !$site_custom_lists ) {
            dt_add_site_custom_lists();
        }
        $user_fields = $site_custom_lists[ 'user_fields' ];
        foreach( $user_fields as $field ) {
            $html .= '<tr><td>' . esc_attr( $field[ 'label' ] ) . '</td><td>Enabled <input name="user_fields[' . $field[ 'key' ] . ']" type="checkbox" ' . $this->is_checked( $field[ 'enabled' ] ) . ' /></td><td>' . esc_attr( $field[ 'description' ] ) . ' </td><td><button type="submit" name="delete_field" value="' . $field[ 'key' ] . '" class="button small" >delete</button> </td></tr>';
        }
        // end list block
        
        $html .= '</table>';
        $html .= '<br><button type="button" onclick="jQuery(\'#add_input\').toggle();" class="button">Add</button>
                        <button type="submit" style="float:right;" class="button">Save</button>';
        $html .= '<div id="add_input" style="display:none;">';
        $html .= '<table width="100%"><tr><td><hr><br>
                    <input type="text" name="add_input_field[label]" placeholder="label" />
                    <input type="text" name="add_input_field[description]" placeholder="description" />
                    <button type="submit">Add</button>
                    </td></tr></table></div>';
        
        $html .= '</form>';
        
        return $html;
        
    }
    
    /**
     * Process user profile settings
     */
    public function process_source_box()
    {
        
        if( isset( $_POST[ 'user_fields_nonce' ] ) ) {
            
            if( !wp_verify_nonce( sanitize_key( $_POST[ 'user_fields_nonce' ] ), 'user_fields' ) ) {
                return;
            }
            
            // Process current fields submitted
            $site_custom_lists = get_option( 'dt_site_custom_lists' );
            if( !$site_custom_lists ) {
                $site_custom_lists = dt_add_site_custom_lists();
            }
            
            foreach( $site_custom_lists[ 'user_fields' ] as $key => $value ) {
                if( isset( $_POST[ 'user_fields' ][ $key ] ) ) {
                    $site_custom_lists[ 'user_fields' ][ $key ][ 'enabled' ] = true;
                } else {
                    $site_custom_lists[ 'user_fields' ][ $key ][ 'enabled' ] = false;
                }
            }
            
            // Process new field submitted
            if( !empty( $_POST[ 'add_input_field' ][ 'label' ] ) ) {
                
                $label = sanitize_text_field( wp_unslash( $_POST[ 'add_input_field' ][ 'label' ] ) );
                if( empty( $label ) ) {
                    return;
                }
                
                if( !empty( $_POST[ 'add_input_field' ][ 'description' ] ) ) {
                    $description = sanitize_text_field( wp_unslash( $_POST[ 'add_input_field' ][ 'description' ] ) );
                } else {
                    $description = '';
                }
                
                $key = 'dt_user_' . sanitize_key( strtolower( str_replace( ' ', '_', $label ) ) );
                $enabled = true;
                
                // strip and make lowercase process
                $site_custom_lists[ 'user_fields' ][ $key ] = [
                    'label'       => $label,
                    'key'         => $key,
                    'description' => $description,
                    'enabled'     => $enabled,
                ];
                
            }
            
            // Process a field to delete.
            if( isset( $_POST[ 'delete_field' ] ) ) {
                
                $delete_key = sanitize_text_field( wp_unslash( $_POST[ 'delete_field' ] ) );
                
                unset( $site_custom_lists[ 'user_fields' ][ $delete_key ] );
                
                //TODO: Consider adding a database query to delete all instances of this key from usermeta
                
            }
            
            // Process reset request
            if( isset( $_POST[ 'user_fields_reset' ] ) ) {
                
                unset( $site_custom_lists[ 'user_fields' ] );
                
                $site_custom_lists[ 'user_fields' ] = dt_get_site_custom_lists( 'user_fields' );
            }
            
            // Update the site option
            update_option( 'dt_site_custom_lists', $site_custom_lists, true );
            
        }
    }
    
}
