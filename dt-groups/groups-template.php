<?php
/**
 * Presenter template for theme support
 *
 * @package  Disciple_Tools
 * @category Plugin
 * @author   Chasm.Solutions & Kingdom.Training
 * @since    0.1
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

/** Functions to output data for the theme. @see Buddypress bp-members-template.php or bp-groups-template.php for an example of the role of this page  */

/**
 *
 * @return void
 */
function dt_get_group_edit_form () {

    if(class_exists( 'Disciple_Tools' )) {

        // Create the title field
        $html = '<table class="form-table">' . "\n";
        $html .= '<tbody>' . "\n";
        $html .= '<input type="hidden" name="dt_contacts_noonce" id="dt_contacts_noonce" value="' . wp_create_nonce( 'update_dt_groups' ) . '" />';
        $html .= '<tr valign="top"><th scope="row"><label for="post_title">Title</label></th>
                                <td><input name="post_title" type="text" id="post_title" class="regular-text" value="'. get_the_title() .'" />' ;
        $html .= '</td><tr/></tbody></table>';
        echo $html;


        // Call the metadata fields
        $group = Disciple_Tools_Group_Post_Type::instance();

        echo ''.$group->load_type_meta_box();


    } // end if class exists

}

/**
 * Save contact
 */
function dt_save_group( $post ) {
    if(class_exists( 'Disciple_Tools' )) {

        if($post['post_title'] != get_the_title()) {
            $my_post = [
                'ID'           => get_the_ID(),
                'post_title'   => $post['post_title'],
            ];
            wp_update_post( $my_post );
        }

        $group = Disciple_Tools_Group_Post_Type::instance();
        $group->meta_box_save( get_the_ID() );

        wp_redirect( get_permalink() );
    }
}
