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
     * Contents for the Sharing Metabox
     */
    public function content_display ( $post_id ) {
        
        $shared_with_list = Disciple_Tools_Contacts::get_shared_with( 'contacts', $post_id  );
        if (!empty( $shared_with_list )) {
            
            $html = '<strong>Sharing with:</strong>';
            $html .= '<form method="post">';
            $html .= '<input type="hidden" name="dt_remove_shared_noonce" id="dt_remove_shared_noonce" value="' . wp_create_nonce( 'dt_remove_shared' ) . '" />';
    
    
            foreach($shared_with_list as $contact) {
                $html .= '<li><a href="'.admin_url().'user-edit.php?user_id='.$contact['user_id'].'">' . $contact['display_name'] . '</a>  ';
            }
            $html .= '</ul></form>';
    
            echo $html;
            
        } else {
            
            echo 'Not shared with any other user';
            
        }
    }
    
}
