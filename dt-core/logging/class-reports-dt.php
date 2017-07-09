<?php

/**
 * Disciple Tools Reports for Contacts and Groups
 *
 * @class Disciple_Tools_Reports_Contacts_Groups
 * @version	0.1
 * @since 0.1
 * @package	Disciple_Tools
 * @author Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Disciple_Tools_Reports_Contacts_Groups
{

    /**
     * Constructor function.
     * @access  public
     * @since   0.1
     */
    public function __construct()
    {
    } // End __construct()

    /**
     * Contacts report data
     * Returns a prepared array for the dt_report_insert()
     * @see     Disciple_Tools_Reports_API
     * @return  array
     */
    public static function contacts_prepared_data($date) {
        $report = array();

        $report[0] = array(
            'report_date' => $date,
            'report_source' => 'Contacts',
            'report_subsource' => 'Project1',
            'meta_input' => array(
                'contacts_added' => rand ( 0 , 100 ),
                'assignable_contacts' => rand ( 0 , 100 ),
                'contact_attempted' => rand ( 0 , 100 ),
                'contact_established' => rand ( 0 , 100 ),
                'first_meeting_complete' => rand ( 0 , 100 ),
                'baptisms_count' => rand ( 0 , 100 ),
                'baptism_gen_1' => rand ( 0 , 100 ), // should create an array and store as serialized
                'baptism_gen_2' => rand ( 0 , 100 ),
                'baptism_gen_3' => rand ( 0 , 100 ),
                'baptism_gen_4' => rand ( 0 , 100 ),
                'baptizers' => rand ( 0 , 100 ),
            )
        );

        return $report;
    }

    /**
     * Groups report data
     * Returns a prepared array for the dt_report_insert()
     * @see     Disciple_Tools_Reports_API
     * @return  array
     */
    public static function groups_prepared_data($date) {
        $report = array();

        $report[0] = array(
            'report_date' => $date,
            'report_source' => 'Groups',
            'report_subsource' => 'Project1',
            'meta_input' => array(
                'total_groups' => rand ( 0 , 100 ),
                '2x2' => rand ( 0 , 100 ),
                '3x3' => rand ( 0 , 100 ),
                'total_active_churches' => rand ( 0 , 100 ),
                'church_gen_1' => rand ( 0 , 30 ),
                'church_gen_2' => rand ( 0 , 30 ),
                'church_gen_3' => rand ( 0 , 30 ),
                'church_gen_4' => rand ( 0 , 30 ),
                'church_planters' => rand ( 0 , 100 ),
            )
        );

        return $report;
    }
}