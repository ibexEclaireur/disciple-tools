<?php

// TODO: unfinished, not working.
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Disciple_Tools_Contacts_Activity extends AAL_Hook_Base {

    protected function _draft_or_post_title( $post = 0 ) {
        $title = get_the_title( $post );

        if ( empty( $title ) )
            $title = __( '(no title)', 'aryo-activity-log' );

        return $title;
    }

    public function hooks_update_metadata_status () {
        add_filter( 'update_user_metadata', 'hooks_transition_post_status', 10, 5 );
    }

    public function hooks_transition_post_status( $null, $object_id, $meta_key, $meta_value, $prev_value ) {
        global  $post;

//        if ( wp_is_post_revision( $post_ID ) )
//            return;

        $action = $meta_key;

        aal_insert_log(
            array(
                'action' => $action,
                'object_type' => 'Test',
                'object_subtype' => $post->post_type,
                'object_id' => $post->ID,
                'object_name' => $this->_draft_or_post_title( $post->ID ) . ' ' . $meta_value . ' ' . $prev_value,
            )
        );

//        if ( 'foo' == $meta_key && empty( $meta_value ) ) {
//            return true; // this means: stop saving the value into the database
//        }

        return null; // this means: go on with the normal execution in meta.php
    }

    public function hooks_delete_post( $post_id ) {
        if ( wp_is_post_revision( $post_id ) )
            return;

        $post = get_post( $post_id );

        if ( in_array( $post->post_status, array( 'auto-draft', 'inherit' ) ) )
            return;

        // Skip for menu items.
        if ( 'nav_menu_item' === get_post_type( $post->ID ) )
            return;

        aal_insert_log(
            array(
                'action' => 'deleted',
                'object_type' => 'Post',
                'object_subtype' => $post->post_type,
                'object_id' => $post->ID,
                'object_name' => $this->_draft_or_post_title( $post->ID ),
            )
        );
    }

    public function __construct() {
        add_action( 'init', array( $this, 'hooks_update_metadata_status' ), 10, 1 );
//        add_action( 'save_post', array( &$this, 'hooks_transition_post_status' ), 10, 1 );
        add_action( 'delete_post', array( &$this, 'hooks_delete_post' ) );

        parent::__construct();
    }
}
