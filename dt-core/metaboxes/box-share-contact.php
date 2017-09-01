<?php

/**
 * Disciple_Tools_Metabox_Share_Contact
 *
 * @class   Disciple_Tools_Metabox_Share_Contact
 * @version 0.1
 * @since   0.1
 * @package Disciple_Tools
 * @author  Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

function dt_share_contact_metabox () {
    $object = new Disciple_Tools_Metabox_Share_Contact();
    return $object;
}

class Disciple_Tools_Metabox_Share_Contact {
    
    /**
     * Constructor function.
     *
     * @access public
     * @since  0.1
     */
    public function __construct () {
    } // End __construct()
    
    /**
     *
     */
    public function content_display ( $contact_id ) {
        
        $shared_with_list = Disciple_Tools_Contacts::get_shared_with( $contact_id );
       
//        print '<pre>'; print_r( $shared_with_list ); print '</pre>';
        
        $html = '<strong>Already sharing with</strong>';
        $html .= '<form method="post">';
        $html .= '<input type="hidden" name="dt_remove_shared_noonce" id="dt_remove_shared_noonce" value="' . wp_create_nonce( 'dt_remove_shared' ) . '" />';
        
        
        foreach($shared_with_list as $contact) {
            $html .= '<li><a href="'.admin_url().'user-edit.php?user_id='.$contact['user_id'].'">' . $contact['display_name'] . '</a>  <button type="button" name="remove" onclick="" value="'.$contact['id'].'">Remove</button> ';
        }
        $html .= '</ul></form>';
        
        $html .= '<p><strong>Share this contact with the following members:</strong></p>';
        $html .= '<form method="get"><input type="text" name="share_contact" /><button type="button">Add</button></form>';
        
        
        echo $html;
    
        
        $result = Disciple_Tools_Contacts::add_shared( $contact_id, 3 );
        if ( is_wp_error( $result ) ) {
            print_r( $result );
        } else {
            echo 'Removed ' . $result;
        }
    
        $result = Disciple_Tools_Contacts::remove_shared( $contact_id, 7 );
        if ( is_wp_error( $result ) ) {
            print_r( $result );
        } else {
            echo 'Removed ' . $result;
        }
        
    }
    
}
