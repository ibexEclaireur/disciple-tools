<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Disciple_Tools_Hook_Posts extends Disciple_Tools_Hook_Base {
	
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
        elseif ( 'draft' === $old_status && 'published' == $new_status ) {
            $action = 'published';
        }
		else {
			return;
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
                'meta_id'           => '',
                'meta_key'          => '',
                'meta_value'        => '',
                'object_note'       => '',
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
        if ($meta_key == '_edit_lock' || $meta_key == '_edit_last') {
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
        if ($meta_key == '_edit_lock' || $meta_key == '_edit_last') {
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

    public function hooks_p2p_created ($p2p_id, $action = 'connected') { // I need to create two records. One for each end of the connection.
        // Get p2p record
        $p2p_record = p2p_get_connection( $p2p_id ); // returns object
        $p2p_from = get_post($p2p_record->p2p_from, ARRAY_A);
        $p2p_to = get_post($p2p_record->p2p_to, ARRAY_A);

        // Build variables
        $p2p_type = $p2p_record->p2p_type;
        if ($action == 'disconnected') {
            $object_note_from = $p2p_from['post_title'] . ' was disconnected from ' . $p2p_to['post_title'];
            $object_note_to = $p2p_to['post_title'] . ' was disconnected from ' . $p2p_from['post_title'];
        } else { // if 'connected'
            $object_note_from = $p2p_from['post_title'] . ' was connected to ' . $p2p_to['post_title'];
            $object_note_to = $p2p_to['post_title'] . ' was connected to ' . $p2p_from['post_title'];
        }

        // Log for both records
        dt_activity_insert(
            array(
                'action'            => $action,
                'object_type'       => $p2p_from['post_type'],
                'object_subtype'    => 'p2p',
                'object_id'         => $p2p_from['ID'],
                'object_name'       => $p2p_from['post_title'],
                'meta_id'           => $p2p_id,
                'meta_key'          => $p2p_type,
                'meta_value'        => $p2p_to['ID'], // i.e. the opposite record of the object in the p2p
                'object_note'       => $object_note_from,
            )
        );

        dt_activity_insert(
            array(
                'action'            => $action,
                'object_type'       => $p2p_to['post_type'],
                'object_subtype'    => 'p2p',
                'object_id'         => $p2p_to['ID'],
                'object_name'       => $p2p_to['post_title'],
                'meta_id'           => $p2p_id,
                'meta_key'          => $p2p_type,
                'meta_value'        => $p2p_from['ID'], // i.e. the opposite record of the object in the p2p
                'object_note'       => $object_note_to,
            )
        );

    }

    public function hooks_p2p_deleted ($p2p_id) {
        $this->hooks_p2p_created ($p2p_id, $action = 'disconnected');
    }
	
	public function __construct() {
		add_action( 'transition_post_status', array( &$this, 'hooks_transition_post_status' ), 10, 3 );
		add_action( 'delete_post', array( &$this, 'hooks_delete_post' ) );
        add_action( "added_post_meta", array( &$this, 'hooks_added_post_meta'), 10, 4 );
        add_action( "updated_postmeta", array( &$this, 'hooks_updated_post_meta'), 10, 4 );
        add_action( 'p2p_created_connection', array( &$this, 'hooks_p2p_created'), 10, 1) ;
        add_action( 'p2p_delete_connections', array( &$this, 'hooks_p2p_deleted'), 10, 1) ;
		
		parent::__construct();
	}
}
