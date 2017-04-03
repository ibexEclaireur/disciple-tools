<?php
/**
 * Disciple Tools Activity Admin UI
 *
 * @class Disciple_Tools_Activity_Admin_Ui
 * @version	0.1
 * @since 0.1
 * @package	Disciple_Tools
 * @author Chasm.Solutions & Kingdom.Training
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(is_admin())
{
    new Disciple_Tools_Wp_List_Table();
}

/**
 * Paulund_Wp_List_Table class will create the page to load the table
 */
class Disciple_Tools_Wp_List_Table
{
    /**
     * Constructor will create the menu item
     */
    public function __construct()
    {
        add_action( 'admin_menu', array($this, 'add_menu_activity_list_table_page' ));
    }

    /**
     * Menu item will allow us to load the page to display the table
     */
    public function add_menu_activity_list_table_page()
    {
        add_menu_page( 'Activity', 'Activity', 'manage_options', 'activity-list-table', array($this, 'list_table_page') );
    }

    /**
     * Display the list table page
     *
     * @return Void
     */
    public function list_table_page()
    {
        $exampleListTable = new Disciple_Tools_Activity_List_Table();
        $exampleListTable->prepare_items();
        ?>
        <div class="wrap">
            <div id="icon-users" class="icon32"></div>
            <h2>Example List Table Page</h2>
            <?php $exampleListTable->display(); ?>
        </div>
        <?php
    }
}

