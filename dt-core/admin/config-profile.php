<?php

/**
 * Disciple Tools Profile
 *
 * @class Disciple_Tools_Profile
 * @version	0.1
 * @since 0.1
 * @package	Disciple_Tools
 * @author Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Disciple_Tools_Profile
{

    /**
     * Disciple_Tools_Admin_Menus The single instance of Disciple_Tools_Admin_Menus.
     * @var    object
     * @access  private
     * @since    0.1
     */
    private static $_instance = null;

    /**
     * Main Disciple_Tools_Profile Instance
     *
     * Ensures only one instance of Disciple_Tools_Profile is loaded or can be loaded.
     *
     * @since 0.1
     * @static
     * @return Disciple_Tools_Profile instance
     */
    public static function instance()
    {
        if (is_null(self::$_instance))
            self::$_instance = new self();
        return self::$_instance;
    } // End instance()

    /**
     * Constructor function.
     * @access  public
     * @since   0.1
     */
    public function __construct()
    {

        if (is_admin()) {
            // Add elements to the contact section of the profile.
            add_filter('user_contactmethods', array($this, 'modify_profile_fields'));
        }

    } // End __construct()

    /**
     *
     *
     */
    public function connect_user_to_contact_record($user_id, $meta_value)
    {
        // $meta_value is the id of the contact record

        add_user_meta($user_id, $meta_key = 'contact_id', $meta_value = '', $unique = true);
    }

    public function modify_profile_fields($profile_fields)
    {

        // Add new fields
        $profile_fields['twitter'] = 'Twitter Username';
        $profile_fields['facebook'] = 'Facebook URL';
        $profile_fields['gplus'] = 'Google+ URL';
        $profile_fields['contact_id'] = 'Contact Id';

        return $profile_fields;

    }

}