<?php

/**
 * Disciple_Tools_Tabs
 *
 * @class Disciple_Tools_Tabs
 * @version	0.1
 * @since 0.1
 * @package	Disciple_Tools_Tabs
 * @author Chasm.Solutions
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Disciple_Tools_Options {

    public $path;

    /**
     * Disciple_Tools The single instance of Disciple_Tools.
     * @var 	object
     * @access  private
     * @since  0.1
     */
    private static $_instance = null;

    /**
     * Main Disciple_Tools_Tabs Instance
     *
     * Ensures only one instance of Disciple_Tools_Tabs is loaded or can be loaded.
     *
     * @since 0.1
     * @static
     * @see Disciple_Tools()
     * @return Disciple_Tools_Options instance
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
        $this->path  = plugin_dir_path(__DIR__);

        add_action( 'admin_menu', array( $this, 'load_admin_menu_item' ) );
    } // End __construct()

    /**
     * Load Admin menu into Settings
     */
    public function load_admin_menu_item () {
        add_submenu_page( 'options-general.php', __( 'Options (DT)', 'disciple_tools' ), __( 'Options (DT)', 'disciple_tools' ), 'manage_options', 'disciple_tools_options', array( $this, 'page_content' ) );
    }

    /**
     * Builds the tab bar
     * @since 0.1
     */
    public function page_content() {


        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }

        /**
         * Begin Header & Tab Bar
         */
        if (isset($_GET["tab"])) {$tab = $_GET["tab"];} else {$tab = 'general';}

        $tab_link_pre = '<a href="options-general.php?page=disciple_tools_options&tab=';
        $tab_link_post = '" class="nav-tab ';

        $html = '<div class="wrap">
            <h2>Import Locations</h2>
            <h2 class="nav-tab-wrapper">';

        $html .= $tab_link_pre . 'general' . $tab_link_post;
        if ($tab == 'general' || !isset($tab)) {$html .= 'nav-tab-active';}
        $html .= '">General</a>';

        $html .= $tab_link_pre . 'daily_reports' . $tab_link_post;
        if ($tab == 'daily_reports' ) {$html .= 'nav-tab-active';}
        $html .= '">Daily Reports</a>';

        $html .= '</h2>';

        echo $html; // Echo tabs

        $html = '';
        // End Tab Bar

        /**
         * Begin Page Content
         */
        switch ($tab) {

            case "daily_reports":
                $html .= $this->daily_reports_tab_page();
                break;
            default:
                $html .= $this->general_tab_page();
                break;
        }

        $html .= '</div>'; // end div class wrap

        echo $html; // Echo contents
    }


    public function general_tab_page () {
        $html = '';

        $html .= '<div class="wrap"><h2>General</h2>'; // Block title

        $html .= '<div class="wrap"><div id="poststuff"><div id="post-body" class="metabox-holder columns-2">';
        $html .= '<div id="post-body-content">';

        $html .= '</div><!-- end post-body-content --><div id="postbox-container-1" class="postbox-container">';
        $html .= '<br>'; /* Add content to column */

        $html .= '</div><!-- postbox-container 1 --><div id="postbox-container-2" class="postbox-container">';
        $html .= '';/* Add content to column */

        $html .= '</div><!-- postbox-container 2 --></div><!-- post-body meta box container --></div><!--poststuff end --></div><!-- wrap end -->';

        return $html;
    }

    public function daily_reports_tab_page () {
        $html = '';

        $html .= '<div class="wrap"><h2>Daily Reports</h2>'; // Block title

        $html .= '<div class="wrap"><div id="poststuff"><div id="post-body" class="metabox-holder columns-2">';
        $html .= '<div id="post-body-content">';

        $html .= '</div><!-- end post-body-content --><div id="postbox-container-1" class="postbox-container">';
        $html .= '<br>'; /* Add content to column */

        $html .= '</div><!-- postbox-container 1 --><div id="postbox-container-2" class="postbox-container">';
        $html .= '';/* Add content to column */

        $html .= '</div><!-- postbox-container 2 --></div><!-- post-body meta box container --></div><!--poststuff end --></div><!-- wrap end -->';

        return $html;
    }

    /**
     * Retrieve the settings fields details
     * @access  public
     * @param  string $section field section.
     * @since   0.1
     * @return  array        Settings fields.
     */
    public function get_settings_fields ( $section ) {
        $settings_fields = array();
        // Declare the default settings fields.

        switch ( $section ) {
            case 'general':

                $settings_fields['add_people_groups'] = array(
                    'name' => __( 'People Groups Addon', 'disciple_tools' ),
                    'type' => 'checkbox',
                    'default' => 'false',
                    'section' => 'general',
                    'description' => ''
                );
                $settings_fields['clear_data_on_deactivate'] = array(
                    'name' => __( 'Clear Data on Deactivate', 'disciple_tools' ),
                    'type' => 'checkbox',
                    'default' => 'false',
                    'section' => 'general',
                    'description' => ''
                );
                $settings_fields['location_cdn_url'] = array(
                    'name' => __( 'Location Files CDN URL', 'disciple_tools' ),
                    'type' => 'text',
                    'default' => 'https://s3.amazonaws.com/disciple-tools-locations/',
                    'section' => 'general',
                    'description' => 'temporary setting to designate the location of the file cdn source'
                );

//              $settings_fields['select'] = array(
//                    'name' => __( 'Select', 'disciple_tools' ),
//                    'type' => 'select',
//                    'default' => '',
//                    'section' => 'standard-fields',
//                    'options' => array(
//                        'one' => __( 'One', 'disciple_tools' ),
//                        'two' => __( 'Two', 'disciple_tools' ),
//                        'three' => __( 'Three', 'disciple_tools' )
//                    ),
//                    'description' => __( 'Place the field description text here.', 'disciple_tools' )
//                );
//			    $settings_fields['text'] = array(
//                    'name' => __( 'Example Text Input', 'disciple_tools' ),
//                    'type' => 'text',
//                    'default' => '',
//                    'section' => 'standard-fields',
//                    'description' => __( 'Place the field description text here.', 'disciple_tools' )
//                );
//				$settings_fields['textarea'] = array(
//                    'name' => __( 'Example Textarea', 'disciple_tools' ),
//                    'type' => 'textarea',
//                    'default' => '',
//                    'section' => 'standard-fields',
//                    'description' => __( 'Place the field description text here.', 'disciple_tools' )
//                );
//				$settings_fields['checkbox'] = array(
//                    'name' => __( 'Example Checkbox', 'disciple_tools' ),
//                    'type' => 'checkbox',
//                    'default' => '',
//                    'section' => 'standard-fields',
//                    'description' => __( 'Place the field description text here.', 'disciple_tools' )
//                );
//				$settings_fields['radio'] = array(
//                    'name' => __( 'Example Radio Buttons', 'disciple_tools' ),
//                    'type' => 'radio',
//                    'default' => '',
//                    'section' => 'standard-fields',
//                    'options' => array(
//                                        'one' => __( 'One', 'disciple_tools' ),
//                                        'two' => __( 'Two', 'disciple_tools' ),
//                                        'three' => __( 'Three', 'disciple_tools' )
//                                ),
//                    'description' => __( 'Place the field description text here.', 'disciple_tools' )
//                );


                break;


            case 'daily_reports':

                $settings_fields['build_report_for_contacts'] = array(
                    'name' => __( 'Disciple Tools Contacts', 'disciple_tools' ),
                    'type' => 'checkbox',
                    'default' => 'true',
                    'section' => 'daily_reports',
                    'description' => __( 'Default is true and enables the scheduling of daily report collection for Disciple Tools Contacts.', 'disciple_tools' )
                );
                $settings_fields['build_report_for_groups'] = array(
                    'name' => __( 'Disciple Tools Groups', 'disciple_tools' ),
                    'type' => 'checkbox',
                    'default' => 'true',
                    'section' => 'daily_reports',
                    'description' => __( 'Default is true and enables the scheduling of daily report collection for Disciple Tools Groups.', 'disciple_tools' )
                );
                $settings_fields['build_report_for_facebook'] = array(
                    'name' => __( 'Facebook', 'disciple_tools' ),
                    'type' => 'checkbox',
                    'default' => 'true',
                    'section' => 'daily_reports',
                    'description' => __( 'Default is true and enables the scheduling of daily report collection for Facebook.', 'disciple_tools' )
                );
                $settings_fields['build_report_for_twitter'] = array(
                    'name' => __( 'Twitter', 'disciple_tools' ),
                    'type' => 'checkbox',
                    'default' => 'true',
                    'section' => 'daily_reports',
                    'description' => __( 'Default is true and enables the scheduling of daily report collection for Twitter.', 'disciple_tools' )
                );
                $settings_fields['build_report_for_analytics'] = array(
                    'name' => __( 'Google Analytics', 'disciple_tools' ),
                    'type' => 'checkbox',
                    'default' => 'true',
                    'section' => 'daily_reports',
                    'description' => __( 'Default is true and enables the scheduling of daily report collection for Google Analytics.', 'disciple_tools' )
                );
                $settings_fields['build_report_for_adwords'] = array(
                    'name' => __( 'Adwords', 'disciple_tools' ),
                    'type' => 'checkbox',
                    'default' => 'true',
                    'section' => 'daily_reports',
                    'description' => __( 'Default is true and enables the scheduling of daily report collection for Google Adwords.', 'disciple_tools' )
                );
                $settings_fields['build_report_for_mailchimp'] = array(
                    'name' => __( 'Mailchimp', 'disciple_tools' ),
                    'type' => 'checkbox',
                    'default' => 'true',
                    'section' => 'daily_reports',
                    'description' => __( 'Default is true and enables the scheduling of daily report collection for Mailchimp.', 'disciple_tools' )
                );
                $settings_fields['build_report_for_youtube'] = array(
                    'name' => __( 'YouTube', 'disciple_tools' ),
                    'type' => 'checkbox',
                    'default' => 'true',
                    'section' => 'daily_reports',
                    'description' => __( 'Default is true and enables the scheduling of daily report collection for YouTube.', 'disciple_tools' )
                );


                break;
            default:
                # code...
                break;
        }

        return (array)apply_filters( 'disciple-tools-settings-fields', $settings_fields, $section);
    } // End get_settings_fields()


}