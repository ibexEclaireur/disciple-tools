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
        $html = '';
        $html .= '<div class="wrap"><div id="poststuff"><div id="post-body" class="metabox-holder columns-2">';
        $html .= '<div id="post-body-content">';
        /* Main Column */

        /* Box */
        $html .= '<table class="widefat striped"><thead><th>User (Worker) Contact Profile</th></thead><tbody><tr><td>';
        $this->process_user_profile_box();
        $html .= $this->user_profile_box();
        $html .= '</td></tr></tbody></table><br>';
        /* End Box */

        /* Box */
        $html .= '<table class="widefat striped"><thead><th>Sources</th></thead><tbody><tr><td>';
        $this->process_sources_box();
        $html .= $this->sources_box();
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
        $html .= '<thead><tr><td>Label</td><td>Type</td><td>Description</td><td>Enabled</td><td>Delete</td></tr></thead><tbody>';

        // custom list block
        $site_custom_lists = dt_get_option( 'dt_site_custom_lists' );
        if( is_wp_error( $site_custom_lists ) ) {
            print esc_html( $site_custom_lists->get_error_message() );
        }
        $user_fields = $site_custom_lists[ 'user_fields' ];
        foreach( $user_fields as $field ) {
            $html .= '<tr>
                        <td>' . esc_attr( $field[ 'label' ] ) . '</td>
                        <td>' . esc_attr( $field[ 'type' ] ) . '</td>
                        <td>' . esc_attr( $field[ 'description' ] ) . ' </td>
                        <td><input name="user_fields[' . $field[ 'key' ] . ']" type="checkbox" ' . $this->is_checked( $field[ 'enabled' ] ) . ' /></td>
                        <td><button type="submit" name="delete_field" value="' . $field[ 'key' ] . '" class="button small" >delete</button> </td>
                      </tr>';
        }
        // end list block

        $html .= '</table>';
        $html .= '<br><button type="button" onclick="jQuery(\'#add_user\').toggle();" class="button">Add</button>
                        <button type="submit" style="float:right;" class="button">Save</button>';
        $html .= '<div id="add_user" style="display:none;">';
        $html .= '<table width="100%"><tr><td><hr><br>
                    <input type="text" name="add_input_field[label]" placeholder="label" />&nbsp;';
        $html .= '<select name="add_input_field[type]" id="add_input_field_type">';
        // Iterate the options
        $user_fields_types = $site_custom_lists[ 'user_fields_types' ];
        foreach( $user_fields_types as $value ) {
            $html .= '<option value="' . $value[ 'key' ] . '" >' . esc_attr( $value[ 'label' ] ) . '</option>';
        }
        $html .= '</select>' . "\n";

        $html .= '<input type="text" name="add_input_field[description]" placeholder="description" />&nbsp;
                    <button type="submit">Add</button>
                    </td></tr></table></div>';

        $html .= '</tbody></form>';

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
            $site_custom_lists = dt_get_option( 'dt_site_custom_lists' );
            if( is_wp_error( $site_custom_lists ) ) {
                print esc_html( $site_custom_lists->get_error_message() );
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

                if( !empty( $_POST[ 'add_input_field' ][ 'type' ] ) ) {
                    $type = sanitize_text_field( wp_unslash( $_POST[ 'add_input_field' ][ 'type' ] ) );
                } else {
                    $type = 'other';
                }

                $key = 'dt_user_' . sanitize_key( strtolower( str_replace( ' ', '_', $label ) ) );
                $enabled = true;

                // strip and make lowercase process
                $site_custom_lists[ 'user_fields' ][ $key ] = [
                    'label'       => $label,
                    'key'         => $key,
                    'type'        => $type,
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
     * Build the sources settings box.
     *
     * @return string
     */
    public function sources_box()
    {
        $html = '';
        $html .= '<form method="post" name="sources_form">';
        $html .= '<button type="submit" class="button-like-link" name="sources_reset" value="1">reset</button>';
        $html .= '<p>Add or remove sources for new contacts.</p>';
        $html .= '<input type="hidden" name="sources_nonce" id="sources_nonce" value="' . wp_create_nonce( 'sources' ) . '" />';
        $html .= '<table class="widefat">';
        $html .= '<thead><tr><td>Label</td><td>Enabled</td><td>Delete</td></tr></thead><tbody>';

        // custom list block
        $site_custom_lists = dt_get_option( 'dt_site_custom_lists' );
        if( is_wp_error( $site_custom_lists ) ) {
            print esc_html( $site_custom_lists->get_error_message() );
        }
        $sources = $site_custom_lists[ 'sources' ];
        foreach( $sources as $source ) {
            $html .= '<tr>
                        <td>' . esc_attr( $source[ 'label' ] ) . '</td>
                        <td><input name="sources[' . $source[ 'key' ] . ']" type="checkbox" ' . $this->is_checked( $source[ 'enabled' ] ) . ' /></td>
                        <td><button type="submit" name="delete_field" value="' . $source[ 'key' ] . '" class="button small" >delete</button> </td>
                      </tr>';
        }
        // end list block

        $html .= '</table>';
        $html .= '<br><button type="button" onclick="jQuery(\'#add_source\').toggle();" class="button">Add</button>
                        <button type="submit" style="float:right;" class="button">Save</button>';
        $html .= '<div id="add_source" style="display:none;">';
        $html .= '<table width="100%"><tr><td><hr><br>
                    <input type="text" name="add_input_field[label]" placeholder="label" />&nbsp;';
        $html .= '<button type="submit">Add</button>
                    </td></tr></table></div>';

        $html .= '</tbody></form>';

        return $html;
    }

    /**
     * Process contact sources settings
     */
    public function process_sources_box()
    {

        if( isset( $_POST[ 'sources_nonce' ] ) ) {

            if( !wp_verify_nonce( sanitize_key( $_POST[ 'sources_nonce' ] ), 'sources' ) ) {
                return;
            }

            // Process current fields submitted
            $site_custom_lists = dt_get_option( 'dt_site_custom_lists' );
            if( is_wp_error( $site_custom_lists ) ) {
                print esc_html( $site_custom_lists->get_error_message() );
            }

            foreach( $site_custom_lists[ 'sources' ] as $key => $value ) {
                if( isset( $_POST[ 'sources' ][ $key ] ) ) {
                    $site_custom_lists[ 'sources' ][ $key ][ 'enabled' ] = true;
                } else {
                    $site_custom_lists[ 'sources' ][ $key ][ 'enabled' ] = false;
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

                if( !empty( $_POST[ 'add_input_field' ][ 'type' ] ) ) {
                    $type = sanitize_text_field( wp_unslash( $_POST[ 'add_input_field' ][ 'type' ] ) );
                } else {
                    $type = 'other';
                }

                $key = sanitize_key( strtolower( str_replace( ' ', '_', $label ) ) );
                $enabled = true;

                // strip and make lowercase process
                $site_custom_lists[ 'sources' ][ $key ] = [
                    'label'       => $label,
                    'key'         => $key,
                    'type'        => $type,
                    'description' => $description,
                    'enabled'     => $enabled,
                ];
            }

            // Process a field to delete.
            if( isset( $_POST[ 'delete_field' ] ) ) {

                $delete_key = sanitize_text_field( wp_unslash( $_POST[ 'delete_field' ] ) );

                unset( $site_custom_lists[ 'sources' ][ $delete_key ] );
                //TODO: Consider adding a database query to delete all instances of this key from usermeta

            }

            // Process reset request
            if( isset( $_POST[ 'sources_reset' ] ) ) {

                unset( $site_custom_lists[ 'sources' ] );

                $site_custom_lists[ 'sources' ] = dt_get_site_custom_lists( 'sources' );
            }

            // Update the site option
            update_option( 'dt_site_custom_lists', $site_custom_lists, true );
        }
    }

}
