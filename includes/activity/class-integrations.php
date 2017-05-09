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
     * @param $url, the facebook url to query for the next stats
     * @param $since, how far back to go to get stats
     * @param $page_id
     * @return array()
     */
    private static function get_facebook_insights_with_paging($url, $since, $page_id){
        $request = wp_remote_get($url);
        if( !is_wp_error( $request ) ) {
            $body = wp_remote_retrieve_body($request);
            $data = json_decode($body);
            if (!empty($data)) {
                if (isset($data->error)) {
                    return $data->error->message;
                } elseif (isset($data->data)) {
                    //create reports for each day in the month
//                    return $this->create_reports_for_each_day($data->data, $page_id);
                    $earliest = date('Y-m-d', strtotime($data->data[0]->values[0]->end_time));
                    if ($since <= $earliest && isset($data->paging->previous)){
                        $next_page = self::get_facebook_insights_with_paging($data->paging->previous, $since, $page_id);
                        return array_merge($data->data, $next_page);
                    } else {
                        return $data->data;
                    }
                }
            }
        }
        return array();
    }


    /**
     * Facebook report data
     * Returns a prepared array for the dt_report_insert()
     * @see     Disciple_Tools_Reports_API
     * @return  array
     */
    public static function facebook_prepared_data ($date_of_last_record) {
        $date_of_last_record = date('Y-m-d', strtotime($date_of_last_record));
        $since = date('Y-m-d', strtotime('-30 days'));
        if ($date_of_last_record > $since){
            $since = $date_of_last_record;
        }


        //get the facebook pages and access tokens from the settings
        $facebook_pages = get_option("disciple_tools_facebook_pages", array());

        $all_reports = array();
        foreach($facebook_pages as $page_id => $facebook_page){
            if(isset($facebook_page->report) && $facebook_page->report == 1){
                $access_token = $facebook_page->access_token;
                $url = "https://graph.facebook.com/v2.8/" . $page_id . "/insights?metric=";
                $url .= "page_fans";
                $url .= ",page_engaged_users";
                $url .= ",page_admin_num_posts";
                $url .= ",page_actions_post_reactions_total";
                $url .= ",page_positive_feedback_by_type";
                $url .= "&since=" . $since;
                $url .= "&until=" . date('Y-m-d', strtotime('tomorrow'));
                $url .= "&access_token=" . $access_token;

                $all_page_data = self::get_facebook_insights_with_paging($url,  $date_of_last_record, $page_id);

                $month_metrics = array();
                foreach($all_page_data as $metric){
                    if ($metric->name === "page_engaged_users" && $metric->period === "day"){
                        foreach($metric->values as $day){
                            $month_metrics[$day->end_time]['page_engagement'] = $day->value;
                        }
                    }
                    if ($metric->name === "page_fans"){
                        foreach($metric->values as $day){
                            $month_metrics[$day->end_time]['page_likes_count'] = isset($day->value) ? $day->value : 0;
                        }
                    }
                    if ($metric->name === "page_admin_num_posts" && $metric->period === "day"){
                        foreach($metric->values as $day){
                            $month_metrics[$day->end_time]['page_post_count'] = $day->value;
                        }
                    }
                    if ($metric->name === "page_positive_feedback_by_type" && $metric->period === "day"){
                        foreach($metric->values as $day){
                            $month_metrics[$day->end_time]['page_comments_count'] = $day->value->like;
                        }
                    }
                }
                foreach($month_metrics as $day => $value){
                    array_push($all_reports, array(
                            'report_date' =>  date('Y-m-d h:m:s', strtotime($day)),
                            'report_source' => "Facebook",
                            'report_subsource' => $page_id,
                            'meta_input' => $value,
                        )
                    );
                }

            }
        }
        return $all_reports;
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
    public static function analytics_prepared_data ($last_date_recorded) {
        $reports = array();

        $website_unique_visits = Ga_Admin::get_report_data($last_date_recorded);

        foreach($website_unique_visits as $website => $days){
            foreach ($days as $day) {
                //set report date to the day after the day of the data
                $report_date = strtotime('+1day', $day['date']);
                $reports[] = array(
                    'report_date' => date('Y-m-d h:m:s', $report_date),
                    'report_source' => 'Analytics',
                    'report_subsource' => $website,
                    'meta_input' => array(
                        'unique_website_visitors' => $day['value']
                    )
                );
            }
        }

        return $reports;
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