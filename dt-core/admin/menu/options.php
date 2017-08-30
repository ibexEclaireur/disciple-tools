<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly


/**
 * Disciple_Tools_Admin Class
 *
 * @class   Disciple_Tools_Admin
 * @version 1.0.0
 * @since   0.1
 * @package Disciple_Tools
 * @author  Chasm.Solutions & Kingdom.Training
 */
final class Disciple_Tools_Admin {
    /**
     * The single instance of Disciple_Tools_Admin.
     *
     * @var    object
     * @access private
     * @since  0.1
     */
    private static $_instance = null;
    
    /**
     * The string containing the dynamically generated hook token.
     *
     * @var    string
     * @access private
     * @since  0.1
     */
    private $_hook;
    
    /**
     * Constructor function.
     *
     * @access public
     * @since  0.1
     */
    public function __construct () {
        // Register the settings with WordPress.
        add_action( 'admin_init', [ $this, 'register_settings' ] );
        
        // Register the settings screen within WordPress.
        add_action( 'admin_menu', [ $this, 'register_settings_screen' ] );
    } // End __construct()
    
    
    /**
     * Main Disciple_Tools_Admin Instance
     *
     * Ensures only one instance of Disciple_Tools_Admin is loaded or can be loaded.
     *
     * @since  0.1
     * @static
     * @return Disciple_Tools_Admin instance
     */
    public static function instance () {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()
    
    /**
     * Register the admin screen.
     *
     * @access public
     * @since  0.1
     * @return void
     */
    public function register_settings_screen () {
        $this->_hook = add_submenu_page( 'options-general.php', __( 'Options (DT)', 'disciple_tools' ), __( 'Options (DT)', 'disciple_tools' ), 'manage_options', 'disciple_tools_options', [ $this, 'settings_screen' ] );
    } // End register_settings_screen()
    
    /**
     * Output the markup for the settings screen.
     *
     * @access public
     * @since  0.1
     * @return void
     */
    public function settings_screen () {
        global $title;
        $sections = Disciple_Tools()->settings->get_settings_sections();
        $tab = $this->_get_current_tab( $sections );
        ?>
        <div class="wrap dt-wrap">
            <?php
            echo $this->get_admin_header_html( $sections, $title );
            ?>
            <form action="options.php" method="post">
                <?php
                settings_fields( 'dt-settings-' . $tab );
                do_settings_sections( Disciple_Tools()->token . '-' . $tab );
                submit_button( __( 'Save Changes', 'disciple_tools' ) );
                ?>
            </form>
        </div><!--/.wrap-->
        <?php
    } // End settings_screen()
    
    /**
     * Register the settings within the Settings API.
     *
     * @access public
     * @since  0.1
     * @return void
     */
    public function register_settings () {
        $sections = Disciple_Tools()->settings->get_settings_sections();
        if ( 0 < count( $sections ) ) {
            foreach ( $sections as $k => $v ) {
                register_setting( 'dt-settings-' . sanitize_title_with_dashes( $k ), Disciple_Tools()->token . '-' . $k, [ $this, 'validate_settings' ] );
                add_settings_section( sanitize_title_with_dashes( $k ), $v, [ $this, 'render_settings' ], Disciple_Tools()->token . '-' . $k );
            }
        }
    } // End register_settings()
    
    /**
     * Render the settings.
     *
     * @access public
     * @param  array $args arguments.
     * @since  0.1
     * @return void
     */
    public function render_settings ( $args ) {
        $token = $args['id'];
        $fields = Disciple_Tools()->settings->get_settings_fields( $token );
        
        if ( 0 < count( $fields ) ) {
            foreach ( $fields as $k => $v ) {
                $args         = $v;
                $args['id'] = $k;
                
                add_settings_field( $k, $v['name'], [ Disciple_Tools()->settings, 'render_field' ], Disciple_Tools()->token . '-' . $token , $v['section'], $args );
            }
        }
    } // End render_settings()
    
    /**
     * Validate the settings.
     *
     * @access public
     * @since  0.1
     * @param  array $input Inputted data.
     * @return array        Validated data.
     */
    public function validate_settings ( $input ) {
        $sections = Disciple_Tools()->settings->get_settings_sections();
        $tab = $this->_get_current_tab( $sections );
        return Disciple_Tools()->settings->validate_settings( $input, $tab );
    } // End validate_settings()
    
    /**
     * Return marked up HTML for the header tag on the settings screen.
     *
     * @access public
     * @since  0.1
     * @param  array  $sections Sections to scan through.
     * @param  string $title    Title to use, if only one section is present.
     * @return string              The current tab key.
     */
    public function get_admin_header_html ( $sections, $title ) {
        $defaults = [
            'tag' => 'h2',
            'atts' => [ 'class' => 'dt-wrapper' ],
            'content' => $title
        ];
        
        $args = $this->_get_admin_header_data( $sections, $title );
        
        $args = wp_parse_args( $args, $defaults );
        
        $atts = '';
        if ( 0 < count( $args['atts'] ) ) {
            foreach ( $args['atts'] as $k => $v ) {
                $atts .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
            }
        }
        
        $response = '<' . esc_attr( $args['tag'] ) . $atts . '>' . $args['content'] . '</' . esc_attr( $args['tag'] ) . '>' . "\n";
        
        return $response;
    } // End get_admin_header_html()
    
    /**
     * Return the current tab key.
     *
     * @access private
     * @since  0.1
     * @param  array  $sections Sections to scan through for a section key.
     * @return string              The current tab key.
     */
    private function _get_current_tab ( $sections = [] ) {
        if ( isset( $_GET['tab'] ) ) {
            $response = sanitize_title_with_dashes( $_GET['tab'] );
        } else {
            if ( is_array( $sections ) && ! empty( $sections ) ) {
                list( $first_section ) = array_keys( $sections );
                $response = $first_section;
            } else {
                $response = '';
            }
        }
        
        return $response;
    } // End _get_current_tab()
    
    /**
     * Return an array of data, used to construct the header tag.
     *
     * @access private
     * @since  0.1
     * @param  array  $sections Sections to scan through.
     * @param  string $title    Title to use, if only one section is present.
     * @return array              An array of data with which to mark up the header HTML.
     */
    private function _get_admin_header_data ( $sections, $title ) {
        $response = [ 'tag' => 'h2', 'atts' => [ 'class' => 'dt-wrapper' ], 'content' => $title ];
        
        if ( is_array( $sections ) && 1 < count( $sections ) ) {
            $response['content'] = '';
            $response['atts']['class'] = 'nav-tab-wrapper';
            
            $tab = $this->_get_current_tab( $sections );
            
            foreach ( $sections as $key => $value ) {
                $class = 'nav-tab';
                if ( $tab == $key ) {
                    $class .= ' nav-tab-active';
                }
                
                $response['content'] .= '<a href="' . admin_url( 'options-general.php?page=disciple_tools_options&tab=' . sanitize_title_with_dashes( $key ) ) . '" class="' . esc_attr( $class ) . '">' . esc_html( $value ) . '</a>';
            }
        }
        
        return (array) apply_filters( 'dt-get-admin-header-data', $response );
    } // End _get_admin_header_data()
    
    
} // End Class


/**
 * Disciple_Tools_Settings Class
 *
 * @class   Disciple_Tools_Settings
 * @version 1.0.0
 * @since   0.1
 * @package Disciple_Tools
 * @author  Chasm.Solutions & Kingdom.Training
 */
final class Disciple_Tools_Settings {
    /**
     * Disciple_Tools_Admin The single instance of Disciple_Tools_Admin.
     *
     * @var    object
     * @access private
     * @since  0.1
     */
    private static $_instance = null;

    /**
     * Whether or not a 'select' field is present.
     *
     * @var    boolean
     * @access private
     * @since  0.1
     */
    private $_has_select;

    /**
     * Main Disciple_Tools_Settings Instance
     *
     * Ensures only one instance of Disciple_Tools_Settings is loaded or can be loaded.
     *
     * @since  0.1
     * @static
     * @return Disciple_Tools_Settings instance
     */
    public static function instance () {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    /**
     * Constructor function.
     *
     * @access public
     * @since  0.1
     */
    public function __construct () {
    } // End __construct()
    
    /**
     * Output the markup for the settings screen.
     *
     * @access public
     * @since  0.1
     * @return void
     */
    public function settings_screen () {
        global $title;
        $sections = $this->get_settings_sections();
        $tab = $this->_get_current_tab( $sections );
        ?>
        <div class="wrap dt-wrap">
            <?php
            echo $this->get_admin_header_html( $sections, $title );
            ?>
            <form action="options.php" method="post">
                <?php
                settings_fields( 'dt-settings-' . $tab );
                do_settings_sections( Disciple_Tools()->token . '-' . $tab );
                submit_button( __( 'Save Changes', 'disciple_tools' ) );
                ?>
            </form>
        </div><!--/.wrap-->
        <?php
    } // End settings_screen()
    
    /**
     * Register the settings within the Settings API.
     *
     * @access public
     * @since  0.1
     * @return void
     */
    public function register_settings () {
        $sections = Disciple_Tools()->settings->get_settings_sections();
        if ( 0 < count( $sections ) ) {
            foreach ( $sections as $k => $v ) {
                register_setting( 'dt-settings-' . sanitize_title_with_dashes( $k ), Disciple_Tools()->token . '-' . $k, [ $this, 'validate_settings' ] );
                add_settings_section( sanitize_title_with_dashes( $k ), $v, [ $this, 'render_settings' ], Disciple_Tools()->token . '-' . $k );
            }
        }
    } // End register_settings()
    
    /**
     * Render the settings.
     *
     * @access public
     * @param  array $args arguments.
     * @since  0.1
     * @return void
     */
    public function render_settings ( $args ) {
        $token = $args['id'];
        $fields = Disciple_Tools()->settings->get_settings_fields( $token );
        
        if ( 0 < count( $fields ) ) {
            foreach ( $fields as $k => $v ) {
                $args         = $v;
                $args['id'] = $k;
                
                add_settings_field( $k, $v['name'], [ Disciple_Tools()->settings, 'render_field' ], Disciple_Tools()->token . '-' . $token , $v['section'], $args );
            }
        }
    } // End render_settings()
    
    /**
     * Validate the settings.
     *
     * @access public
     * @since  0.1
     * @param  array $input Inputted data.
     * @return array        Validated data.
     */
    public function validate_settings ( $input ) {
        $sections = Disciple_Tools()->settings->get_settings_sections();
        $tab = $this->_get_current_tab( $sections );
        return Disciple_Tools()->settings->validate_settings( $input, $tab );
    } // End validate_settings()
    
    /**
     * Return marked up HTML for the header tag on the settings screen.
     *
     * @access public
     * @since  0.1
     * @param  array  $sections Sections to scan through.
     * @param  string $title    Title to use, if only one section is present.
     * @return string              The current tab key.
     */
    public function get_admin_header_html ( $sections, $title ) {
        $defaults = [
            'tag' => 'h2',
            'atts' => [ 'class' => 'dt-wrapper' ],
            'content' => $title
        ];
        
        $args = $this->_get_admin_header_data( $sections, $title );
        
        $args = wp_parse_args( $args, $defaults );
        
        $atts = '';
        if ( 0 < count( $args['atts'] ) ) {
            foreach ( $args['atts'] as $k => $v ) {
                $atts .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
            }
        }
        
        $response = '<' . esc_attr( $args['tag'] ) . $atts . '>' . $args['content'] . '</' . esc_attr( $args['tag'] ) . '>' . "\n";
        
        return $response;
    } // End get_admin_header_html()
    
    /**
     * Return the current tab key.
     *
     * @access private
     * @since  0.1
     * @param  array  $sections Sections to scan through for a section key.
     * @return string              The current tab key.
     */
    private function _get_current_tab ( $sections = [] ) {
        if ( isset( $_GET['tab'] ) ) {
            $response = sanitize_title_with_dashes( $_GET['tab'] );
        } else {
            if ( is_array( $sections ) && ! empty( $sections ) ) {
                list( $first_section ) = array_keys( $sections );
                $response = $first_section;
            } else {
                $response = '';
            }
        }
        
        return $response;
    } // End _get_current_tab()
    
    /**
     * Return an array of data, used to construct the header tag.
     *
     * @access private
     * @since  0.1
     * @param  array  $sections Sections to scan through.
     * @param  string $title    Title to use, if only one section is present.
     * @return array              An array of data with which to mark up the header HTML.
     */
    private function _get_admin_header_data ( $sections, $title ) {
        $response = [ 'tag' => 'h2', 'atts' => [ 'class' => 'dt-wrapper' ], 'content' => $title ];
        
        if ( is_array( $sections ) && 1 < count( $sections ) ) {
            $response['content'] = '';
            $response['atts']['class'] = 'nav-tab-wrapper';
            
            $tab = $this->_get_current_tab( $sections );
            
            foreach ( $sections as $key => $value ) {
                $class = 'nav-tab';
                if ( $tab == $key ) {
                    $class .= ' nav-tab-active';
                }
                
                $response['content'] .= '<a href="' . admin_url( 'options-general.php?page=disciple_tools_options&tab=' . sanitize_title_with_dashes( $key ) ) . '" class="' . esc_attr( $class ) . '">' . esc_html( $value ) . '</a>';
            }
        }
        
        return (array) apply_filters( 'dt-get-admin-header-data', $response );
    } // End _get_admin_header_data()

    /**
     * Validate the settings.
     *
     * @access public
     * @since  0.1
     * @param  array $input Inputted data.
     * @param  string $section field section.
     * @return array        Validated data.
     */
//    public function validate_settings ( $input, $section ) {
//        if ( is_array( $input ) && 0 < count( $input ) ) {
//            $fields = $this->get_settings_fields( $section );
//
//            foreach ( $input as $k => $v ) {
//                if ( ! isset( $fields[$k] ) ) {
//                    continue;
//                }
//
//                // Determine if a method is available for validating this field.
//                $method = 'validate_field_' . $fields[$k]['type'];
//
//                if ( ! method_exists( $this, $method ) ) {
//                    if ( true === (bool) apply_filters( 'dt-validate-field-' . $fields[$k]['type'] . '_use_default', true ) ) {
//                        $method = 'validate_field_text';
//                    } else {
//                        $method = '';
//                    }
//                }
//
//                // If we have an internal method for validation, filter and apply it.
//                if ( '' != $method ) {
//                    add_filter( 'dt-validate-field-' . $fields[$k]['type'], [ $this, $method ] );
//                }
//
//                $method_output = apply_filters( 'dt-validate-field-' . $fields[$k]['type'], $v, $fields[$k] );
//
//                if ( ! is_wp_error( $method_output ) ) {
//                    $input[$k] = $method_output;
//                }
//            }
//        }
//        return $input;
//    } // End validate_settings()

    /**
     * Validate the given data, assuming it is from a text input field.
     *
     * @access public
     * @since  6.0.0
     * @return void
     */
    public function validate_field_text ( $v ) {
        return (string) wp_kses_post( $v );
    } // End validate_field_text()

    /**
     * Validate the given data, assuming it is from a textarea field.
     *
     * @access public
     * @since  6.0.0
     * @return void
     */
    public function validate_field_textarea ( $v ) {
        // Allow iframe, object and embed tags in textarea fields.
        $allowed             = wp_kses_allowed_html( 'post' );
        $allowed['iframe']     = [
                                'src'         => true,
                                'width'     => true,
                                'height'     => true,
                                'id'         => true,
                                'class'     => true,
                                'name'         => true
                                ];
        $allowed['object']     = [
                                'src'         => true,
                                'width'     => true,
                                'height'     => true,
                                'id'         => true,
                                'class'     => true,
                                'name'         => true
                                ];
        $allowed['embed']     = [
                                'src'         => true,
                                'width'     => true,
                                'height'     => true,
                                'id'         => true,
                                'class'     => true,
                                'name'         => true
                                ];

        return wp_kses( $v, $allowed );
    } // End validate_field_textarea()

    /**
     * Validate the given data, assuming it is from a checkbox input field.
     *
     * @access public
     * @since  6.0.0
     * @param  string $v
     * @return string
     */
    public function validate_field_checkbox ( $v ) {
        if ( 'true' != $v ) {
            return 'false';
        } else {
            return 'true';
        }
    } // End validate_field_checkbox()

    /**
     * Validate the given data, assuming it is from a URL field.
     *
     * @access public
     * @since  6.0.0
     * @param  string $v
     * @return string
     */
    public function validate_field_url ( $v ) {
        return trim( esc_url( $v ) );
    } // End validate_field_url()
    

    /**
     * Render a field of a given type.
     *
     * @access public
     * @since  0.1
     * @param  array $args The field parameters.
     * @return void
     */
    public function render_field ( $args ) {
        $html = '';
        if ( ! in_array( $args['type'], $this->get_supported_fields() ) ) { return ''; // Supported field type sanity check.
        }

        // Make sure we have some kind of default, if the key isn't set.
        if ( ! isset( $args['default'] ) ) {
            $args['default'] = '';
        }

        $method = 'render_field_' . $args['type'];

        if ( ! method_exists( $this, $method ) ) {
            $method = 'render_field_text';
        }

        // Construct the key.
        $key                 = Disciple_Tools()->token . '-' . $args['section'] . '[' . $args['id'] . ']';
        $method_output         = $this->$method( $key, $args );

        if ( ! is_wp_error( $method_output ) ) {
            $html .= $method_output;
        }

        // Output the description, if the current field allows it.
        if ( isset( $args['type'] ) && ! in_array( $args['type'], (array) apply_filters( 'dt-no-description-fields', [ 'checkbox' ] ) ) ) {
            if ( isset( $args['description'] ) ) {
                $description = '<p class="description">' . wp_kses_post( $args['description'] ) . '</p>' . "\n";
                if ( in_array( $args['type'], (array) apply_filters( 'dt-new-line-description-fields', [ 'textarea', 'select' ] ) ) ) {
                    $description = wpautop( $description );
                }
                $html .= $description;
            }
        }

        echo $html;
    } // End render_field()

    /**
     * Retrieve the settings fields details
     *
     * @access public
     * @since  0.1
     * @return array        Settings fields.
     */
    public function get_settings_sections () {
        $settings_sections = [];

        $settings_sections['general'] = __( 'General', 'disciple_tools' );
        $settings_sections['daily_reports'] = __( 'Daily Reports', 'disciple_tools' );
        // Add your new sections below here.
        // Admin tabs will be created for each section.
        // Don't forget to add fields for the section in the get_settings_fields() function below

        return (array) apply_filters( 'disciple-tools-settings-sections', $settings_sections );
    } // End get_settings_sections()

    /**
     * Retrieve the settings fields details
     *
     * @access public
     * @param  string $section field section.
     * @since  0.1
     * @return array        Settings fields.
     */
    public function get_settings_fields ( $section ) {
        $settings_fields = [];
        // Declare the default settings fields.

        switch ( $section ) {
            case 'general':

                $settings_fields['add_people_groups'] = [
                    'name' => __( 'People Groups Addon', 'disciple_tools' ),
                    'type' => 'checkbox',
                    'default' => 'false',
                    'section' => 'general',
                    'description' => ''
                ];
                $settings_fields['clear_data_on_deactivate'] = [
                    'name' => __( 'Clear Data on Deactivate', 'disciple_tools' ),
                    'type' => 'checkbox',
                    'default' => 'false',
                    'section' => 'general',
                    'description' => ''
                ];


//                $settings_fields['select'] = array(
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

                $settings_fields['build_report_for_contacts'] = [
                    'name' => __( 'Disciple Tools Contacts', 'disciple_tools' ),
                    'type' => 'checkbox',
                    'default' => 'true',
                    'section' => 'daily_reports',
                    'description' => __( 'Default is true and enables the scheduling of daily report collection for Disciple Tools Contacts.', 'disciple_tools' )
                ];
                $settings_fields['build_report_for_groups'] = [
                    'name' => __( 'Disciple Tools Groups', 'disciple_tools' ),
                    'type' => 'checkbox',
                    'default' => 'true',
                    'section' => 'daily_reports',
                    'description' => __( 'Default is true and enables the scheduling of daily report collection for Disciple Tools Groups.', 'disciple_tools' )
                ];
                $settings_fields['build_report_for_facebook'] = [
                    'name' => __( 'Facebook', 'disciple_tools' ),
                    'type' => 'checkbox',
                    'default' => 'true',
                    'section' => 'daily_reports',
                    'description' => __( 'Default is true and enables the scheduling of daily report collection for Facebook.', 'disciple_tools' )
                ];
                $settings_fields['build_report_for_twitter'] = [
                    'name' => __( 'Twitter', 'disciple_tools' ),
                    'type' => 'checkbox',
                    'default' => 'true',
                    'section' => 'daily_reports',
                    'description' => __( 'Default is true and enables the scheduling of daily report collection for Twitter.', 'disciple_tools' )
                ];
                $settings_fields['build_report_for_analytics'] = [
                    'name' => __( 'Google Analytics', 'disciple_tools' ),
                    'type' => 'checkbox',
                    'default' => 'true',
                    'section' => 'daily_reports',
                    'description' => __( 'Default is true and enables the scheduling of daily report collection for Google Analytics.', 'disciple_tools' )
                ];
                $settings_fields['build_report_for_adwords'] = [
                    'name' => __( 'Adwords', 'disciple_tools' ),
                    'type' => 'checkbox',
                    'default' => 'true',
                    'section' => 'daily_reports',
                    'description' => __( 'Default is true and enables the scheduling of daily report collection for Google Adwords.', 'disciple_tools' )
                ];
                $settings_fields['build_report_for_mailchimp'] = [
                    'name' => __( 'Mailchimp', 'disciple_tools' ),
                    'type' => 'checkbox',
                    'default' => 'true',
                    'section' => 'daily_reports',
                    'description' => __( 'Default is true and enables the scheduling of daily report collection for Mailchimp.', 'disciple_tools' )
                ];
                $settings_fields['build_report_for_youtube'] = [
                    'name' => __( 'YouTube', 'disciple_tools' ),
                    'type' => 'checkbox',
                    'default' => 'true',
                    'section' => 'daily_reports',
                    'description' => __( 'Default is true and enables the scheduling of daily report collection for YouTube.', 'disciple_tools' )
                ];


                break;
            default:
                # code...
                break;
        }

        return (array) apply_filters( 'disciple-tools-settings-fields', $settings_fields, $section );
    } // End get_settings_fields()

    /**
     * Render HTML markup for the "text" field type.
     *
     * @access protected
     * @since  6.0.0
     * @param  string $key  The unique ID of this field.
     * @param  array $args  Arguments used to construct this field.
     * @return string       HTML markup for the field.
     */
    protected function render_field_text ( $key, $args ) {
        $html = '<input id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" size="40" type="text" value="' . esc_attr( $this->get_value( $args['id'], $args['default'], $args['section'] ) ) . '" />' . "\n";
        return $html;
    } // End render_field_text()

    /**
     * Render HTML markup for the "radio" field type.
     *
     * @access protected
     * @since  6.0.0
     * @param  string $key  The unique ID of this field.
     * @param  array $args  Arguments used to construct this field.
     * @return string       HTML markup for the field.
     */
    protected function render_field_radio ( $key, $args ) {
        $html = '';
        if ( isset( $args['options'] ) && ( 0 < count( (array) $args['options'] ) ) ) {
            $html = '';
            foreach ( $args['options'] as $k => $v ) {
                $html .= '<input type="radio" name="' . esc_attr( $key ) . '" value="' . esc_attr( $k ) . '"' . checked( esc_attr( $this->get_value( $args['id'], $args['default'], $args['section'] ) ), $k, false ) . ' /> ' . esc_html( $v ) . '<br />' . "\n";
            }
        }
        return $html;
    } // End render_field_radio()

    /**
     * Render HTML markup for the "textarea" field type.
     *
     * @access protected
     * @since  6.0.0
     * @param  string $key  The unique ID of this field.
     * @param  array $args  Arguments used to construct this field.
     * @return string       HTML markup for the field.
     */
    protected function render_field_textarea ( $key, $args ) {
        // Explore how best to escape this data, as esc_textarea() strips HTML tags, it seems.
        $html = '<textarea id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" cols="42" rows="5">' . $this->get_value( $args['id'], $args['default'], $args['section'] ) . '</textarea>' . "\n";
        return $html;
    } // End render_field_textarea()rist

    /**
     * Render HTML markup for the "checkbox" field type.
     *
     * @access protected
     * @since  6.0.0
     * @param  string $key  The unique ID of this field.
     * @param  array $args  Arguments used to construct this field.
     * @return string       HTML markup for the field.
     */
    protected function render_field_checkbox ( $key, $args ) {
        $has_description = false;
        $html = '';
        if ( isset( $args['description'] ) ) {
            $has_description = true;
            $html .= '<label for="' . esc_attr( $key ) . '">' . "\n";
        }
        $html .= '<input id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" type="checkbox" value="true"' . checked( esc_attr( $this->get_value( $args['id'], $args['default'], $args['section'] ) ), 'true', false ) . ' />' . "\n";
        if ( $has_description ) {
            $html .= wp_kses_post( $args['description'] ) . '</label>' . "\n";
        }
        return $html;
    } // End render_field_checkbox()

    /**
     * Render HTML markup for the "select2" field type.
     *
     * @access protected
     * @since  6.0.0
     * @param  string $key  The unique ID of this field.
     * @param  array $args  Arguments used to construct this field.
     * @return string       HTML markup for the field.
     */
    protected function render_field_select ( $key, $args ) {
        $this->_has_select = true;

        $html = '';
        if ( isset( $args['options'] ) && ( 0 < count( (array) $args['options'] ) ) ) {
            $html .= '<select id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '">' . "\n";
            foreach ( $args['options'] as $k => $v ) {
                $html .= '<option value="' . esc_attr( $k ) . '"' . selected( esc_attr( $this->get_value( $args['id'], $args['default'], $args['section'] ) ), $k, false ) . '>' . esc_html( $v ) . '</option>' . "\n";
            }
            $html .= '</select>' . "\n";
        }
        return $html;
    } // End render_field_select()

    /**
     * Render HTML markup for the "select_taxonomy" field type.
     *
     * @access protected
     * @since  6.0.0
     * @param  string $key  The unique ID of this field.
     * @param  array $args  Arguments used to construct this field.
     * @return string       HTML markup for the field.
     */
    protected function render_field_select_taxonomy ( $key, $args ) {
        $this->_has_select = true;

        $defaults = [
            'show_option_all'    => '',
            'show_option_none'   => '',
            'orderby'            => 'ID',
            'order'              => 'ASC',
            'show_count'         => 0,
            'hide_empty'         => 1,
            'child_of'           => 0,
            'exclude'            => '',
            'selected'           => $this->get_value( $args['id'], $args['default'], $args['section'] ),
            'hierarchical'       => 1,
            'class'              => 'postform',
            'depth'              => 0,
            'tab_index'          => 0,
            'taxonomy'           => 'category',
            'hide_if_empty'      => false,
            'walker'             => ''
        ];

        if ( ! isset( $args['options'] ) ) {
            $args['options'] = [];
        }

        $args['options']             = wp_parse_args( $args['options'], $defaults );
        $args['options']['echo']     = false;
        $args['options']['name']     = esc_attr( $key );
        $args['options']['id']         = esc_attr( $key );

        $html = '';
        $html .= wp_dropdown_categories( $args['options'] );

        return $html;
    } // End render_field_select_taxonomy()

    /**
     * Return an array of field types expecting an array value returned.
     *
     * @access public
     * @since  0.1
     * @return array
     */
    public function get_array_field_types () {
        return [];
    } // End get_array_field_types()

    /**
     * Return an array of field types where no label/header is to be displayed.
     *
     * @access protected
     * @since  0.1
     * @return array
     */
    protected function get_no_label_field_types () {
        return [ 'info' ];
    } // End get_no_label_field_types()

    /**
     * Return a filtered array of supported field types.
     *
     * @access public
     * @since  0.1
     * @return array Supported field type keys.
     */
    public function get_supported_fields () {
        return (array) apply_filters( 'dt-supported-fields', [ 'text', 'checkbox', 'radio', 'textarea', 'select', 'select_taxonomy' ] );
    } // End get_supported_fields()

    /**
     * Return a value, using a desired retrieval method.
     *
     * @access public
     * @param  string $key option key.
     * @param  string $default default value.
     * @param  string $section field section.
     * @since  0.1
     * @return mixed Returned value.
     */
    public function get_value ( $key, $default, $section ) {
        $values = get_option( Disciple_Tools()->token . '-' . $section, [] );
        if ( is_array( $values ) && isset( $values[$key] ) ) {
            $response = $values[$key];
        } else {
            $response = $default;
        }

        return $response;
    } // End get_value()

    /**
     * Return all settings keys.
     *
     * @access public
     * @param  string $section field section.
     * @since  0.1
     * @return mixed Returned value.
     */
    public function get_settings ( $section = '' ) {
        $response = false;

        $sections = array_keys( (array) $this->get_settings_sections() );

        if ( in_array( $section, $sections ) ) {
            $sections = [ $section ];
        }

        if ( 0 < count( $sections ) ) {
            foreach ( $sections as $k => $v ) {
                $fields = $this->get_settings_fields( $k );
                $values = get_option( Disciple_Tools()->token . '-' . $k, [] );

                if ( is_array( $fields ) && 0 < count( $fields ) ) {
                    foreach ( $fields as $i => $j ) {
                        // If we have a value stored, use it.
                        if ( isset( $values[$i] ) ) {
                            $response[$i] = $values[$i];
                        } else {
                            // Otherwise, check for a default value. If we have one, use it. Otherwise, return an empty string.
                            if ( isset( $fields[$i]['default'] ) ) {
                                $response[$i] = $fields[$i]['default'];
                            } else {
                                $response[$i] = '';
                            }
                        }
                    }
                }
            }
        }

        return $response;
    } // End get_settings()
} // End Class
    
class Disciple_Tools_Menu_Options
{
    /**
     * The single instance of Disciple_Tools_Menu_Options.
     *
     * @var    object
     * @access private
     * @since  0.1
     */
    private static $_instance = null;
    
    /**
     * Main Disciple_Tools_Menu_Options Instance
     *
     * Ensures only one instance of Disciple_Tools_Menu_Options is loaded or can be loaded.
     *
     * @since  0.1
     * @static
     * @return Disciple_Tools_Menu_Options instance
     */
    public static function instance () {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()
    
    /**
     * The contents of our meta box.
     *
     * @access public
     * @since  0.1
     * @return void
     */
    public function meta_box_content ( $section = 'dt_' ) {
        $fields = get_option( $section );
        $field_data = $this->get_custom_fields_settings();
        
        $html = '';
        
        $html .= '<input type="hidden" name="dt_' . $this->post_type . '_noonce" id="dt_' . $this->post_type . '_noonce" value="' . wp_create_nonce( 'update_location_info' ) . '" />';
        
        
        if ( 0 < count( $field_data ) ) {
            $html .= '<table class="form-table">' . "\n";
            $html .= '<tbody>' . "\n";
            
            foreach ( $field_data as $k => $v ) {
                
                if ($v['section'] == $section || $section == 'all') {
                    
                    $data = $v['default'];
                    if (isset( $fields[$k] ) && isset( $fields[$k][0] )) {
                        $data = $fields[$k][0];
                    }
                    
                    $type = $v['type'];
                    
                    switch ($type) {
                        
                        case 'text':
                            $html .= '<tr valign="top"><th scope="row"><label for="' . esc_attr( $k ) . '">' . $v['name'] . '</label></th><td><input name="' . esc_attr( $k ) . '" type="text" id="' . esc_attr( $k ) . '" class="regular-text" value="' . esc_attr( $data ) . '" />' . "\n";
                            $html .= '<p class="description">' . $v['description'] . '</p>' . "\n";
                            $html .= '</td><tr/>' . "\n";
                            break;
                        case 'select':
                            $html .= '<tr valign="top"><th scope="row">
							<label for="' . esc_attr( $k ) . '">' . $v['name'] . '</label></th>
							<td><select name="' . esc_attr( $k ) . '" id="' . esc_attr( $k ) . '" class="regular-text">';
                            // Iterate the options
                            foreach ($v['default'] as $vv) {
                                $html .= '<option value="' . $vv . '" ';
                                if ($vv == $data) {
                                    $html .= 'selected';
                                }
                                $html .= '>' . $vv . '</option>';
                            }
                            $html .= '</select>' . "\n";
                            $html .= '<p class="description">' . $v['description'] . '</p>' . "\n";
                            $html .= '</td><tr/>' . "\n";
                            break;
                        case 'key_select':
                            $html .= '<tr valign="top"><th scope="row">
                                <label for="' . esc_attr( $k ) . '">' . $v['name'] . '</label></th>
                                <td>
                                <select name="' . esc_attr( $k ) . '" id="' . esc_attr( $k ) . '" class="regular-text">';
                            // Iterate the options
                            foreach ($v['default'] as $kk => $vv) {
                                $html .= '<option value="' . $kk . '" ';
                                if($kk == $data) { $html .= 'selected';}
                                $html .= '>' .$vv . '</option>';
                            }
                            $html .= '</select>' . "\n";
                            $html .= '<p class="description">' . $v['description'] . '</p>' . "\n";
                            $html .= '</td><tr/>' . "\n";
                            break;
                        case 'radio':
                            $html .= '<tr valign="top"><th scope="row">' . $v['name'] . '</th>
							<td><fieldset>';
                            // Iterate the buttons
                            $increment_the_radio_button = 1;
                            foreach ($v['default'] as $vv) {
                                $html .= '<label for="' . esc_attr( "$k-$increment_the_radio_button" ) . "\">$vv</label>" .
                                         '<input class="dt-radio" type="radio" name="' . esc_attr( $k ) . '" id="' . $k . '-' . $increment_the_radio_button . '" value="' . $vv . '" ';
                                if ($vv == $data) {
                                    $html .= 'checked';
                                }
                                $html .= '>';
                                $increment_the_radio_button++;
                            }
                            $html .= '</fieldset>' . "\n";
                            $html .= '<p class="description">' . $v['description'] . '</p>' . "\n";
                            $html .= '</td><tr/>' . "\n";
                            break;
                        
                        
                        default:
                            break;
                    }
                    
                }
                
                
            }
            
            $html .= '</tbody>' . "\n";
            $html .= '</table>' . "\n";
        }
        
        echo $html;
    } // End meta_box_content()
    
    /**
     * Save meta box fields.
     *
     * @access public
     * @since  0.1
     * @param  int $post_id
     * @return int $post_id
     */
    public function meta_box_save ( $post_id ) {
        global $post, $messages;
        
        // Verify
        if ( ( get_post_type() != $this->post_type ) || ! wp_verify_nonce( $_POST['dt_' . $this->post_type . '_noonce'], 'update_location_info' ) ) {
            return $post_id;
        }
        
        if ( isset( $_POST['post_type'] ) && 'page' == esc_attr( $_POST['post_type'] ) ) {
            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return $post_id;
            }
        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }
        }
        
        $field_data = $this->get_custom_fields_settings();
        $fields = array_keys( $field_data );
        
        if ( (isset( $_POST['new-key-address'] ) && !empty( $_POST['new-key-address'] ) ) && (isset( $_POST['new-value-address'] ) && !empty( $_POST['new-value-address'] ) ) ) { // catch and prepare new contact fields
            $k = explode( "_",  $_POST['new-key-address'] );
            $type = $k[1];
            $number_key = dt_address_metabox()->create_channel_metakey( "address" );
            $details_key = $number_key . "_details";
            $details = ['type'=>$type, 'verified'=>false];
            //save the field and the field details
            add_post_meta( $post_id, strtolower( $number_key ), $_POST['new-value-address'], true );
            add_post_meta( $post_id, strtolower( $details_key ), $details, true );
        }
        
        foreach ( $fields as $f ) {
            
            ${$f} = strip_tags( trim( $_POST[$f] ) );
            
            
            if ( get_post_meta( $post_id, $f ) == '' ) {
                add_post_meta( $post_id, $f, ${$f}, true );
            } elseif ( ${$f} == '' ) {
                delete_post_meta( $post_id, $f, get_post_meta( $post_id, $f, true ) );
            } elseif( ${$f} != get_post_meta( $post_id, $f, true ) ) {
                update_post_meta( $post_id, $f, ${$f} );
            }
        }
    } // End meta_box_save()
    
    /**
     * Get the settings for the custom fields.
     *
     * @access public
     * @since  0.1
     * @return array
     */
    public function get_custom_fields_settings () {
        global $post;
        $fields = [];
    
        $fields['share'] = [
            'name' => 'share',
            'description' => '',
            'type' => 'text',
            'default' => '',
            'section' => 'address'
        ];
        
        
        return apply_filters( 'dt_custom_fields_settings', $fields );
    } // End get_custom_fields_settings()
    
    
           
}
