<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Disciple_Tools_Hook_Contacts extends Disciple_Tools_Hook_Base {

    protected function _draft_or_post_title( $post = 0 ) {
        $title = get_the_title( $post );

        if ( empty( $title ) )
            $title = __( '(no title)', 'disciple-tools' );

        return $title;
    }

    public function hooks_transition_post_status( $new_status, $old_status, $post ) {
        if ( 'auto-draft' === $old_status && ( 'auto-draft' !== $new_status && 'inherit' !== $new_status ) ) {
            // page created
            $action = 'created';
        }
        elseif ( 'auto-draft' === $new_status || ( 'new' === $old_status && 'inherit' === $new_status ) ) {
            // nvm.. ignore it.
            return;
        }
        elseif ( 'trash' === $new_status ) {
            // page was deleted.
            $action = 'trashed';
        }
        elseif ( 'trash' === $old_status ) {
            $action = 'restored';
        }
        else {
            // page updated. I guess.
            $action = 'updated';
        }

        if ( wp_is_post_revision( $post->ID ) )
            return;

        // Skip for menu items.
        if ( 'nav_menu_item' === get_post_type( $post->ID ) )
            return;

        dt_activity_insert(
            array(
                'action' => $action,
                'object_type' => 'Post',
                'object_subtype' => $post->post_type,
                'object_id' => $post->ID,
                'object_name' => $this->_draft_or_post_title( $post->ID ),
            )
        );
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

        dt_activity_insert(
            array(
                'action' => 'deleted',
                'object_type' => 'Post',
                'object_subtype' => $post->post_type,
                'object_id' => $post->ID,
                'object_name' => $this->_draft_or_post_title( $post->ID ),
            )
        );
    }

    public function hooks_added_post_meta ($mid, $object_id, $meta_key, $meta_value) {

        // ignore edit lock
        if ($meta_key == '_edit_lock') {
            return;
        }

        // get object info
        $parent_post = get_post($object_id, ARRAY_A);

        dt_activity_insert(
            array(
                'action'            => 'field_update',
                'object_type'       => $parent_post['post_type'],
                'object_subtype'    => $meta_key,
                'object_id'         => $object_id,
                'object_name'       => $parent_post['post_title'],
                'meta_id'           => $mid,
                'meta_key'          => $meta_key,
                'meta_value'        => $meta_value,
                'object_note'       => $meta_key . ' was changed to ' . $meta_value,
            )
        );
    }

    public function hooks_updated_post_meta ($meta_id, $object_id, $meta_key, $meta_value) {

        // ignore edit lock
        if ($meta_key == '_edit_lock') {
            return;
        }

        // get object info
        $parent_post = get_post($object_id, ARRAY_A);

        dt_activity_insert(
            array(
                'action'            => 'field_update',
                'object_type'       => $parent_post['post_type'],
                'object_subtype'    => $meta_key,
                'object_id'         => $object_id,
                'object_name'       => $parent_post['post_title'],
                'meta_id'           => $meta_id,
                'meta_key'          => $meta_key,
                'meta_value'        => $meta_value,
                'object_note'       => $meta_key . ' was changed to ' . $meta_value,
            )
        );
    }

    public function hooks_p2p_created ($p2p_id) { // I need to create two records. One for each end of the connection.
//        wp_die('success');
//        return;

        global $wpdb;

        // Query p2p Record
//        $p2p_record = p2p_get_connection( $p2p_id ); // returns object
        $p2p_record = $wpdb->get_row(
            $wpdb->prepare(
                'SELECT * FROM %1$s
					WHERE `p2p_id` = \'%2$s\'
				;',
                $wpdb->p2p,
                $p2p_id
            ), ARRAY_A
        );

        $p2p_type = $p2p_record['p2p_type'];


        // Build variable sets
        $connections = array();

        $p2p_from = get_post($p2p_record['p2p_from'], ARRAY_A);
        $p2p_to = get_post($p2p_record['p2p_to'], ARRAY_A);

        $connections['p2p_from'] = array(
            'post_type'     => $p2p_from['post_type'],
            'post_id'       => $p2p_from['ID'],
            'post_title'    => $p2p_from['post_title'],
            'p2p_opposite'  => $p2p_to['ID'],
            'object_note'   => 'was connected to ' . $p2p_to['post_title'],
        );
        $connections['p2p_to'] = array(
            'post_type'     => $p2p_to['post_type'],
            'post_id'       => $p2p_to['ID'],
            'post_title'    => $p2p_to['post_title'],
            'p2p_opposite'  => $p2p_from['ID'],
            'object_note'   => 'was connected to ' . $p2p_from['post_title'],
        );

        // Loop insert of sets
        foreach ($connections as $connection ) {

            dt_activity_insert(
                array(
                    'action'            => 'created',
                    'object_type'       => $connection['post_type'],
                    'object_subtype'    => 'p2p',
                    'object_id'         => $connection['post_id'],
                    'object_name'       => $connection['post_title'],
                    'meta_id'           => $p2p_id,
                    'meta_key'          => $p2p_type,
                    'meta_value'        => $connection['post_opposite'], // i.e. the opposite record of the object in the p2p
                    'object_note'       => $connection['object_note'],
                )
            );
        }

    }

    public function __construct() {
        add_action( "added_post_meta", array( &$this, 'hooks_added_post_meta'), 10, 4 );
        add_action( "updated_postmeta", array( &$this, 'hooks_updated_post_meta'), 10, 4 );
        add_action( 'p2p_created_connection', array( &$this, 'hooks_p2p_created'), 10, 1) ;
        add_action( 'p2p_delete_connections', array( &$this, 'hooks_p2p_created'), 10, 1) ;

        parent::__construct();
    }
}


