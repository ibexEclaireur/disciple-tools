<?php
/*
Description: A simple class based on a tutorial at WP.Tuts that creates an page with metaboxes.
Author: Stephen Harris
Author URI: http://www.stephenharris.info
*/
/*  Copyright 2011 Stephen Harris (contact@stephenharris.info)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

/**
 *
 * The class takes the following arguments
 * * $hook - the hook of the 'parent' (menu top-level page).
 * * $title - the browser window title of the page
 * * $title - the page title as it appears in the menu
 * * $permissions - the capability a user requires to see the page
 * * $slug - a slug identifier for this page
 * * $body_content_cb -(optional) a callback that prints to the page, above the metaboxes. See the tutorial for more details.
 *
 * Example use
 * $my_admin page = new Disciple_Tools_Page_Creator('my_hook','My Admin Page','My Admin Page', 'manage_options','my-admin-page')
 *
 * Full example below the class (which adds example metaboxes too).
 */

class Disciple_Tools_Page_Factory
{
    var $hook;
    var $title;
    var $menu;
    var $permissions;
    var $slug;
    var $page;

    /**
     * Constructor class for the Simple Admin Metabox
     *@param $hook - (string) parent page hook
     *@param $title - (string) the browser window title of the page
     *@param $menu - (string)  the page title as it appears in the menuk
     *@param $permissions - (string) the capability a user requires to see the page
     *@param $slug - (string) a slug identifier for this page
     *@param $body_content_cb - (callback)  (optional) a callback that prints to the page, above the metaboxes. See the tutorial for more details.
     */
    function __construct($hook, $title, $menu, $permissions, $slug, $body_content_cb = null){
        $this->hook = $hook;
        $this->title = $title;
        $this->menu = $menu;
        $this->permissions = $permissions;
        $this->slug = $slug;
        $this->body_content_cb = $body_content_cb;

        /* Add the page */
        add_action('admin_menu', array($this,'add_page'));
    }


    /**
     * Adds the custom page.
     * Adds callbacks to the load-* and admin_footer-* hooks
     */
    function add_page(){

        /* Add the page */
        $this->page = add_submenu_page($this->hook,$this->title, $this->menu, $this->permissions,$this->slug,  array($this,'render_page'));

        /* Add callbacks for this screen only */
        add_action('load-'.$this->page,  array($this,'page_actions'),9);
        add_action('admin_footer-'.$this->page,array($this,'footer_scripts'));
    }

    /**
     * Prints the jQuery script to initiliase the metaboxes
     * Called on admin_footer-*
     */
    function footer_scripts(){
        ?>
        <script> postboxes.add_postbox_toggles(pagenow);</script>
        <?php
    }



    /*
    * Actions to be taken prior to page loading. This is after headers have been set.
        * call on load-$hook
    * This calls the add_meta_boxes hooks, adds screen options and enqueues the postbox.js script.
    */
    function page_actions(){
        do_action('add_meta_boxes_'.$this->page, null);
        do_action('add_meta_boxes', $this->page, null);

        /* User can choose between 1 or 2 columns (default 2) */
        add_screen_option('layout_columns', array('max' => 2, 'default' => 2) );

        /* Enqueue WordPress' script for handling the metaboxes */
        wp_enqueue_script('postbox');
    }


    /**
     * Renders the page
     */
    function render_page(){
        ?>
        <div class="wrap">

            <?php screen_icon(); ?>

            <h2> <?php echo esc_html($this->title);?> </h2>

            <form name="my_form" method="post">
                <input type="hidden" name="action" value="some-action">
                <?php wp_nonce_field( 'some-action-nonce' );

                /* Used to save closed metaboxes and their order */
                wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
                wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>

                <div id="poststuff">

                    <div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">

                        <?php if ($this->body_content_cb != null) : ?>
                        <div id="post-body-content">
                            <?php call_user_func($this->body_content_cb); ?>
                        </div>
                        <?php endif; ?>

                        <div id="postbox-container-1" class="postbox-container">
                            <?php do_meta_boxes('','side',null); ?>
                        </div>

                        <div id="postbox-container-2" class="postbox-container">
                            <?php do_meta_boxes('','normal',null);  ?>
                            <?php do_meta_boxes('','advanced',null); ?>
                        </div>

                    </div> <!-- #post-body -->

                </div> <!-- #poststuff -->

            </form>

        </div><!-- .wrap -->
        <?php
    }

}