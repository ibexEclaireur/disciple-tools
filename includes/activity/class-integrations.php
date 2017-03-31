<?php

/**
 * Disciple Tools
 *
 * @class Disciple_Tools_Reports_Integrations
 * @version	0.1
 * @since 0.1
 * @package	Disciple_Tools
 * @author Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Disciple_Tools_Reports_Integrations {

    /**
     * Constructor function.
     * @access  public
     * @since   0.1
     */
    public function __construct () {} // End __construct()

    /**
     * Facebook report data
     * Returns a prepared array for the dt_report_insert()
     * @see     Disciple_Tools_Reports_API
     * @return  array
     */
    public static function facebook_prepared_data ($date) {
        $report = array();
        
        $report[0] = array(
            'report_date' => $date,
            'report_source' => 'Facebook',
            'report_subsource' => 'Page1',
            'meta_input' => array(
                'page_likes_count' => rand ( 0 , 100 ),
                'page_engagement' => rand ( 0 , 100 ),
                'page_conversations_count' => rand ( 0 , 100 ),
                'page_messages_in_conversation_count' => rand ( 0 , 100 ),
                'page_post_count' => rand ( 2 , 6 ),
                'page_post_likes_and_reactions' => rand ( 0 , 100 ),
                'page_comments_count' => rand ( 0 , 100 ),
            )
        );
        $report[1] = array(
            'report_date' => $date,
            'report_source' => 'Facebook',
            'report_subsource' => 'Page2',
            'meta_input' => array(
                'page_likes_count' => rand ( 0 , 100 ),
                'page_engagement' => rand ( 0 , 100 ),
                'page_conversations_count' => rand ( 0 , 100 ),
                'page_messages_in_conversation_count' => rand ( 0 , 100 ),
                'page_post_count' => rand ( 2 , 6 ),
                'page_post_likes_and_reactions' => rand ( 0 , 100 ),
                'page_comments_count' => rand ( 0 , 100 ),
            )
        );
        $report[2] = array(
            'report_date' => $date,
            'report_source' => 'Facebook',
            'report_subsource' => 'Page3',
            'meta_input' => array(
                'page_likes_count' => rand ( 0 , 100 ),
                'page_engagement' => rand ( 0 , 100 ),
                'page_conversations_count' => rand ( 0 , 100 ),
                'page_messages_in_conversation_count' => rand ( 0 , 100 ),
                'page_post_count' => rand ( 2 , 6 ),
                'page_post_likes_and_reactions' => rand ( 0 , 100 ),
                'page_comments_count' => rand ( 0 , 100 ),
            )
        );
        return $report;
    }

    /**
     * Twitter report data
     * Returns a prepared array for the dt_report_insert()
     * @see     Disciple_Tools_Reports_API
     * @return  array
     */
    public static function twitter_prepared_data ($date) {
        $report = array();
        
        $report[0] = array(
            'report_date' => $date,
            'report_source' => 'Twitter',
            'report_subsource' => 'Channel1',
            'meta_input' => array(
                'unique_website_visitors' => rand(0, 100),
                'platforms' => rand(0, 100),
                'browsers' => rand(0, 100),
                'average_time' => rand(0, 100),
                'page_visits' => rand(0, 100),
            )
        );
        $report[1] = array(
            'report_date' => $date,
            'report_source' => 'Twitter',
            'report_subsource' => 'Channel2',
            'meta_input' => array(
                'unique_website_visitors' => rand(0, 100),
                'platforms' => rand(0, 100),
                'browsers' => rand(0, 100),
                'average_time' => rand(0, 100),
                'page_visits' => rand(0, 100),
            )
        );
        return $report;

    }

    /**
     * Analytics report data
     * Returns a prepared array for the dt_report_insert()
     * @see     Disciple_Tools_Reports_API
     * @return  array
     */
    public static function analytics_prepared_data ($date) {
        $report = array();
        
        $report[0] = array(
            'report_date' => $date,
            'report_source' => 'Analytics',
            'report_subsource' => 'Site1', // individual web property
            'meta_input' => array(
                'unique_website_visitors' => rand(0, 100),
                'platforms' => rand(0, 100),
                'browsers' => rand(0, 100),
                'average_time' => rand(0, 100),
                'page_visits' => rand(0, 100),
            )
        );
        $report[1] = array(
            'report_date' => $date,
            'report_source' => 'Analytics',
            'report_subsource' => 'Site2', // individual web property
            'meta_input' => array(
                'unique_website_visitors' => rand(0, 100),
                'platforms' => rand(0, 100),
                'browsers' => rand(0, 100),
                'average_time' => rand(0, 100),
                'page_visits' => rand(0, 100),
            )
        );
        $report[2] = array(
            'report_date' => $date,
            'report_source' => 'Analytics',
            'report_subsource' => 'Site3', // individual web property
            'meta_input' => array(
                'unique_website_visitors' => rand(0, 100),
                'platforms' => rand(0, 100),
                'browsers' => rand(0, 100),
                'average_time' => rand(0, 100),
                'page_visits' => rand(0, 100),
            )
        );
        return $report;
    }

    /**
     * Adwords report data
     * Returns a prepared array for the dt_report_insert()
     * @see     Disciple_Tools_Reports_API
     * @return  array
     */
    public static function adwords_prepared_data ($date) {
        $report = array();
        
        $report[0] = array(
            'report_date' => $date,
            'report_source' => 'Adwords',
            'report_subsource' => 'Campaign1',
            'meta_input' => array(
                'money_spent' => rand(0, 100),
                'conversions' => rand(0, 100),
                'total_clicks' => rand(0, 100),
                'ads_served' => rand(0, 100),
                'average_position' => rand(0, 100),
            )
        );
        $report[1] = array(
            'report_date' => $date,
            'report_source' => 'Adwords',
            'report_subsource' => 'Campaign2',
            'meta_input' => array(
                'money_spent' => rand(0, 100),
                'conversions' => rand(0, 100),
                'total_clicks' => rand(0, 100),
                'ads_served' => rand(0, 100),
                'average_position' => rand(0, 100),
            )
        );

        return $report;
    }


    /**
     * Mailchimp report data
     * Returns a prepared array for the dt_report_insert()
     * @see     Disciple_Tools_Reports_API
     * @return  array
     */
    public static function mailchimp_prepared_data ($date) {
        $report = array();
        
        $report[0] = array(
            'report_date' => $date,
            'report_source' => 'Mailchimp',
            'report_subsource' => 'List1',
            'meta_input' => array(
                'new_subscribers' => rand(0, 100),
                'campaigns_sent' => rand(0, 3),
                'list_opens' => rand(0, 5000),
                'campaign_opens' => rand(0, 100),
                'subscriber_count' => rand(5000, 6000),
                'opt_ins' => rand(0, 50),
                'opt_outs' => rand(0, 10),
            )
        );
        $report[1] = array(
            'report_date' => $date,
            'report_source' => 'Mailchimp',
            'report_subsource' => 'List2',
            'meta_input' => array(
                'new_subscribers' => rand(0, 100),
                'campaigns_sent' => rand(0, 3),
                'list_opens' => rand(0, 5000),
                'campaign_opens' => rand(0, 100),
                'subscriber_count' => rand(5000, 6000),
                'opt_ins' => rand(0, 50),
                'opt_outs' => rand(0, 10),
            )
        );

        return $report;
    }

    /**
     * Youtube report data
     * Returns a prepared array for the dt_report_insert()
     * @see     Disciple_Tools_Reports_API
     * @return  array
     */
    public static function youtube_prepared_data ($date) {
        $report = array();
        
        $report[0] = array(
            'report_date' => $date,
            'report_source' => 'Youtube',
            'report_subsource' => 'Channel1',
            'meta_input' => array(
                'total_views' => rand(100, 500),
                'total_likes' => rand(0, 100),
                'total_shares' => rand(0, 50),
                'number_of_videos_posted' => rand(0, 3),
            )
        );
        return $report;
    }

}