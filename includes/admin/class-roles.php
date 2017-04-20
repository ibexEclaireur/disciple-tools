<?php
/**
 * Disciple_Tools Post to Post Metabox for Locations
 *
 * @class Disciple_Tools_Roles
 * @version	1.0.0
 * @since 0.1
 * @package	Disciple_Tools
 * @author Chasm.Solutions & Kingdom.Training
 */

//if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Disciple_Tools_Roles {

    /**
     * The single instance of Disciple_Tools_Roles
     * @var 	object
     * @access  private
     * @since  0.1
     */
    private static $_instance = null;

    /**
     * Main Disciple_Tools_P2P_Metabox Instance
     *
     * Ensures only one instance of Disciple_Tools_P2P_Metabox is loaded or can be loaded.
     *
     * @since 0.1
     * @static
     * @return Disciple_Tools_Roles instance
     */
    public static function instance () {
        if ( is_null( self::$_instance ) )
            self::$_instance = new self();
        return self::$_instance;
    } // End instance()

    /**
     * Constructor function.
     * @access  public
     * @since   0.1
     */
    public function __construct () {

    } // End __construct()

    /*
     * Install DMM Roles
     * */
    public function set_roles () {
        if ( get_role( 'marketer' )) { remove_role( 'marketer' ); }
        add_role( 'marketer', 'Marketer',
            array(
                // Standard Capabilities
                'delete_others_posts' => true,
                'delete_pages' => true,
                'delete_posts' => true,
                'delete_private_pages' => true,
                'delete_private_posts' => true,
                'delete_published_pages' => true,
                'delete_published_posts' => true,
                'edit_others_pages' => true,
                'edit_others_posts' => true,
                'edit_pages' => true,
                'edit_posts' => true,
                'edit_private_pages' => true,
                'edit_private_posts' => true,
                'edit_published_pages' => true,
                'edit_published_posts' => true,
                'manage_categories' => true,
                'manage_links' => true,
                'moderate_comments' => true,
                'publish_pages' => true,
                'publish_posts' => true,
                'read' => true,
                'read_private_pages' => true,
                'read_private_posts' => true,
                'upload_files' => true,
                'level_0' => true,
                // see all contacts
                'manage_contacts' => true,
                // Add custom caps for contacts
                'edit_contact' => true,
                'read_contact' => true,
                'delete_contact' => true,
                'delete_others_contacts' => true,
                'delete_contacts' => true,
                'edit_contacts' => true,
                'edit_others_contacts' => true,
                'publish_contacts' => true,
                'read_private_contacts' => true,
                // Add custom caps for groups
                'edit_group' => true,
                'read_group' => true,
                'delete_group' => true,
                'delete_others_groups' => true,
                'delete_groups' => true,
                'edit_groups' => true,
                'edit_others_groups' => true,
                'publish_groups' => true,
                'read_private_groups' => true,
                // Add custom caps for prayer
                'read_prayer' => true,
                'edit_prayer' => true,
                'delete_prayer' => true,
                'delete_others_prayers' => true,
                'delete_prayers' => true,
                'edit_prayers' => true,
                'edit_others_prayers' => true,
                'publish_prayers' => true,
                'read_private_prayers' => true,
                // Add custom caps for locations
                'read_location' => true,
                'edit_location' => true,
                'delete_location' => true,
                'delete_others_locations' => true,
                'delete_locations' => true,
                'edit_locations' => true,
                'edit_others_locations' => true,
                'publish_locations' => true,
                'read_private_locations' => true,
                // Add custom caps for project updates
                'read_report' => true

            ) );


        if ( get_role( 'dispatcher' )) { remove_role( 'dispatcher' );}
        add_role( 'dispatcher', 'Dispatcher',
            array(
                // Standard Capabilities
                'delete_others_posts' => true,
                'delete_pages' => true,
                'delete_posts' => true,
                'delete_private_pages' => true,
                'delete_private_posts' => true,
                'delete_published_pages' => true,
                'delete_published_posts' => true,
                'edit_others_pages' => true,
                'edit_others_posts' => true,
                'edit_pages' => true,
                'edit_posts' => true,
                'edit_private_pages' => true,
                'edit_private_posts' => true,
                'edit_published_pages' => true,
                'edit_published_posts' => true,
                'manage_categories' => true,
                'manage_links' => true,
                'moderate_comments' => true,
                'publish_pages' => true,
                'publish_posts' => true,
                'read' => true,
                'read_private_pages' => true,
                'read_private_posts' => true,
                'upload_files' => true,
                'level_0' => true,
                // See all contacts
                'manage_contacts' => true,
                // Add custom caps for contacts
                'edit_contact' => true,
                'read_contact' => true,
                'delete_contact' => true,
                'delete_others_contacts' => true,
                'delete_contacts' => true,
                'edit_contacts' => true,
                'edit_others_contacts' => true,
                'publish_contacts' => true,
                'read_private_contacts' => true,
                // Add custom caps for groups
                'edit_group' => true,
                'read_group' => true,
                'delete_group' => true,
                'delete_others_groups' => true,
                'delete_groups' => true,
                'edit_groups' => true,
                'edit_others_groups' => true,
                'publish_groups' => true,
                'read_private_groups' => true,
                // Add custom caps for project updates
                'read_prayer' => true,
                'edit_prayer' => true,
                'delete_prayer' => true,
                'delete_others_prayers' => true,
                'delete_prayers' => true,
                'edit_prayers' => true,
                'edit_others_prayers' => true,
                'publish_prayers' => true,
                'read_private_prayers' => true,
                // Add custom caps for locations
                'read_location' => true,
                'edit_location' => true,
                'delete_location' => true,
                'delete_others_locations' => true,
                'delete_locations' => true,
                'edit_locations' => true,
                'edit_others_locations' => true,
                'publish_locations' => true,
                'read_private_locations' => true,
                // Add custom caps for project updates
                'read_report' => true
            ) );


        if ( get_role( 'multiplier' )) { remove_role( 'multiplier' );}
        add_role( 'multiplier', 'Multiplier',
            array(
                // Standard Capabilities
                'moderate_comments' => true,
                'read' => true,
                'upload_files' => true,
                // Add custom caps for contacts
                'edit_contact' => true,
                'read_contact' => true,
                'delete_contact' => true,
                'delete_contacts' => true,
                'edit_contacts' => true,
                'publish_contacts' => true,
                'read_private_contacts' => true,
                // Add custom caps for groups
                'edit_group' => true,
                'read_group' => true,
                'delete_group' => true,
                'delete_groups' => true,
                'edit_groups' => true,
                'publish_groups' => true,
                'read_private_groups' => true,
                // Add custom caps for project updates
                'read_prayer' => true,
                'edit_prayer' => true,
                'delete_prayer' => true,
                'delete_others_prayers' => true,
                'delete_prayers' => true,
                'edit_prayers' => true,
                'edit_others_prayers' => true,
                'publish_prayers' => true,
                'read_private_prayers' => true,
                // Add custom caps for locations
                'read_location' => true,
                'edit_location' => true,
                'edit_locations' => true,
                // Add custom caps for project updates
                'read_report' => true
            ) );


        if ( get_role( 'multiplier_leader' )) { remove_role( 'multiplier_leader' );}
        add_role( 'multiplier_leader', 'Multiplier Leader',
            array(
                // Standard Capabilities
                'read' => true,
                'upload_files' => true,
                // See all contacts
                'manage_contacts' => true,
                // Add custom caps for contacts
                'edit_contact' => true,
                'read_contact' => true,
                'delete_contact' => true,
                'delete_others_contacts' => true,
                'delete_contacts' => true,
                'edit_contacts' => true,
                'edit_others_contacts' => true,
                'publish_contacts' => true,
                'read_private_contacts' => true,
                // Add custom caps for groups
                'edit_group' => true,
                'read_group' => true,
                'delete_group' => true,
                'delete_others_groups' => true,
                'delete_groups' => true,
                'edit_groups' => true,
                'edit_others_groups' => true,
                'publish_groups' => true,
                'read_private_groups' => true,
                // Add custom caps for project updates
                'read_prayer' => true,
                'edit_prayer' => true,
                'delete_prayer' => true,
                'delete_others_prayers' => true,
                'delete_prayers' => true,
                'edit_prayers' => true,
                'edit_others_prayers' => true,
                'publish_prayers' => true,
                'read_private_prayers' => true,
                // Add custom caps for project updates
                'read_report' => true
            ) );



        if ( get_role( 'prayer_supporter' )) { remove_role( 'prayer_supporter' );}
        add_role( 'prayer_supporter', 'Prayer Supporter',
            array(
                'prayer_supporter' => true,
                'read_prayer' => true
            ) );

        if ( get_role( 'project_supporter' )) { remove_role( 'project_supporter' );}
        add_role( 'project_supporter', 'Project Supporter',
            array(
                'project_supporter' => true,
                'read_prayer' => true
            ) );

        if ( get_role( 'registered' )) { remove_role( 'registered' );}
        add_role( 'registered', 'Registered',
            array(
                // No capabilities to this role. Must be moved to another role for permission.
            ) );
        /*
         * Default user role set to registered in /includes/drm-filters.php
         * */

        remove_role( 'subscriber' );
        remove_role( 'contributor' );
        remove_role( 'editor' );
        remove_role( 'author' );


        // Get the administrator role.
        $role = get_role( 'administrator' );

        // If the administrator role exists, add required capabilities for the plugin.
        if ( ! empty( $role ) ) {

            $role->add_cap( 'manage_contacts' );
            $role->add_cap( 'edit_contact' );
            $role->add_cap( 'read_contact' );
            $role->add_cap( 'delete_contact' );
            $role->add_cap( 'delete_others_contacts' );
            $role->add_cap( 'delete_contacts' );
            $role->add_cap( 'edit_contacts' );
            $role->add_cap( 'edit_others_contacts' );
            $role->add_cap( 'publish_contacts' );
            $role->add_cap( 'read_private_contacts' );
            $role->add_cap( 'edit_group' );
            $role->add_cap( 'read_group' );
            $role->add_cap( 'delete_group' );
            $role->add_cap( 'delete_others_groups' );
            $role->add_cap( 'delete_groups' );
            $role->add_cap( 'edit_groups' );
            $role->add_cap( 'edit_others_groups' );
            $role->add_cap( 'publish_groups' );
            $role->add_cap( 'read_private_groups' );
            $role->add_cap( 'edit_prayer' );
            $role->add_cap( 'read_prayer' );
            $role->add_cap( 'delete_prayer' );
            $role->add_cap( 'delete_others_prayers' );
            $role->add_cap( 'delete_prayers' );
            $role->add_cap( 'edit_prayers' );
            $role->add_cap( 'edit_others_prayers' );
            $role->add_cap( 'publish_prayers' );
            $role->add_cap( 'read_private_prayers' );
            $role->add_cap( 'edit_report' );
            $role->add_cap( 'read_report' );
            $role->add_cap( 'delete_report' );
            $role->add_cap( 'delete_others_reports' );
            $role->add_cap( 'delete_reports' );
            $role->add_cap( 'edit_reports' );
            $role->add_cap( 'edit_others_reports' );
            $role->add_cap( 'publish_reports' );
            $role->add_cap( 'read_private_reports' );
            $role->add_cap( 'edit_location' );
            $role->add_cap( 'read_location' );
            $role->add_cap( 'delete_location' );
            $role->add_cap( 'delete_others_locations' );
            $role->add_cap( 'delete_locations' );
            $role->add_cap( 'edit_locations' );
            $role->add_cap( 'edit_others_locations' );
            $role->add_cap( 'publish_locations' );
            $role->add_cap( 'read_private_locations' );

        }

        return "complete";
}



    /*
    * Reset Roles on deactivation
    */
    public function reset_roles () {
        delete_option('run_once');

        remove_role( 'dispatcher' );
        remove_role( 'multiplier' );
        remove_role( 'multiplier_leader' );
        remove_role( 'marketer' );
        remove_role( 'prayer_supporter' );
        remove_role( 'project_supporter' );

        add_role( 'subscriber', 'Subscriber',
            array(
                'delete_others_posts' => true,
                'delete_pages' => true,
                'delete_posts' => true,
                'delete_private_pages' => true,
                'delete_private_posts' => true,
                'delete_published_pages' => true,
                'delete_published_posts' => true,
                'edit_others_pages' => true,
                'edit_others_posts' => true,
                'edit_pages' => true,
                'edit_posts' => true,
                'edit_private_pages' => true,
                'edit_private_posts' => true,
                'edit_published_pages' => true,
                'edit_published_posts' => true,
                'manage_categories' => true,
                'manage_links' => true,
                'moderate_comments' => true,
                'publish_pages' => true,
                'publish_posts' => true,
                'read' => true,
                'read_private_pages' => true,
                'read_private_posts' => true,
                'upload_files' => true
            ) );

        add_role( 'editor', 'Editor',
            array(
                'delete_others_posts' => true,
                'delete_pages' => true,
                'delete_posts' => true,
                'delete_private_pages' => true,
                'delete_private_posts' => true,
                'delete_published_pages' => true,
                'delete_published_posts' => true,
                'edit_others_pages' => true,
                'edit_others_posts' => true,
                'edit_pages' => true,
                'edit_posts' => true,
                'edit_private_pages' => true,
                'edit_private_posts' => true,
                'edit_published_pages' => true,
                'edit_published_posts' => true,
                'manage_categories' => true,
                'manage_links' => true,
                'moderate_comments' => true,
                'publish_pages' => true,
                'publish_posts' => true,
                'read' => true,
                'read_private_pages' => true,
                'read_private_posts' => true,
                'upload_files' => true,
                'level_0' => true
            ) );
        add_role( 'author', 'Author',
            array(
                'delete_posts' => true,
                'delete_published_posts' => true,
                'edit_posts' => true,
                'edit_published_posts' => true,
                'publish_posts' => true,
                'read' => true,
                'upload_files' => true
            ) );

        add_role( 'contributor', 'Contributor',
            array(
                'delete_posts' => true,
                'edit_posts' => true,
                'read' => true
            ) );

        add_filter('pre_option_default_role', function($default_role){
            return 'subscriber';
        });
    }

}

