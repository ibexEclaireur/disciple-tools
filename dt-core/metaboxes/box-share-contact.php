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
    public function __construct () { } // End __construct()
    
    /**
     *
     */
    public function content_display ( $contact_id) {
        global $wpdb;
        $shared_with_list = Disciple_Tools_Contacts::get_shared_with( $contact_id ) ;
        $list_of_members = [];
        $shared_with_list = $wpdb->get_results( $wpdb->prepare(
            "SELECT user_id
            FROM %s 
            WHERE contact_id = '%d'
            ",
            $wpdb->dt_share,
            $contact_id
        ));
        $shared_with_list = $wpdb->get_results("SELECT * FROM $wpdb->dt_share WHERE contact_id = '$contact_id'", ARRAY_A);
        var_dump($shared_with_list);
        
        $html = '<strong>Already sharing with</strong>';
        $html .= '<ul>';
        foreach($shared_with_list as $contact) {
            $html .= '<li>- ' . $contact . ' <a href="">remove</a></li>';
        }
        $html .= '</ul>';
        
        $html .= '<p><strong>Share this contact with the following members:</strong></p>';
        $html .= '<form method="get"><input type="text" name="share_contact" /><button type="button">Add</button></form>';
        
        
        
        echo $html;
        
        
        
        
    }
    
    
}
