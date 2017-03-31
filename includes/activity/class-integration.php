<?php

/**
 * Disciple Tools
 *
 * @class Disciple_Tools_Integration_Facebook
 * @version	0.1
 * @since 0.1
 * @package	Disciple_Tools
 * @author Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Disciple_Tools_Integration_Facebook {

    /**
     * Constructor function.
     * @access  public
     * @since   0.1
     */
    public function __construct () {

    } // End __construct()

    public static function facebook_prepared_data ($date, $source, $subsource) {
        // Create Unique Meta Inputs Depending on Source
        switch ($source) {
            case 'Facebook':
                $meta_input = array(
                    'page_likes_count' => rand ( 0 , 100 ),
                    'page_engagement' => rand ( 0 , 100 ),
                    'page_conversations_count' => rand ( 0 , 100 ),
                    'page_messages_in_conversation_count' => rand ( 0 , 100 ),
                    'page_post_count' => rand ( 2 , 6 ),
                    'page_post_likes_and_reactions' => rand ( 0 , 100 ),
                    'page_comments_count' => rand ( 0 , 100 ),
                );
                break;
            case 'Twitter':
                $meta_input = array(
                    'unique_website_visitors' => rand ( 0 , 100 ),
                    'platforms' => rand ( 0 , 100 ),
                    'browsers' => rand ( 0 , 100 ),
                    'average_time' => rand ( 0 , 100 ),
                    'page_visits' => rand ( 0 , 100 ),
                );
                break;
            case 'Analytics':
                $meta_input = array(
                    'unique_website_visitors' => rand ( 0 , 100 ),
                    'platforms' => rand ( 0 , 100 ),
                    'browsers' => rand ( 0 , 100 ),
                    'average_time' => rand ( 0 , 100 ),
                    'page_visits' => rand ( 0 , 100 ),
                );
                break;
            case 'Adwords':
                $meta_input = array(
                    'money_spent' => rand ( 0 , 100 ),
                    'conversions' => rand ( 0 , 100 ),
                    'total_clicks' => rand ( 0 , 100 ),
                    'ads_served' => rand ( 0 , 100 ),
                    'average_position' => rand ( 0 , 100 ),
                );
                break;
            case 'Mailchimp':
                $meta_input = array(
                    'new_subscribers' => rand ( 0 , 100 ),
                    'campaigns_sent' => rand ( 0 , 3 ),
                    'list_opens' => rand ( 0 , 5000 ),
                    'campaign_opens' => rand ( 0 , 100 ),
                    'subscriber_count' => rand ( 5000 , 6000 ),
                    'opt_ins' => rand ( 0 , 50 ),
                    'opt_outs' => rand ( 0 , 10 ),
                );
                break;
            case 'YouTube':
                $meta_input = array(
                    'total_views' => rand ( 100 , 500 ),
                    'total_likes' => rand ( 0 , 100 ),
                    'total_shares' => rand ( 0 , 50 ),
                    'number_of_videos_posted' => rand ( 0 , 3 ),
                );
                break;
            case 'Vimeo':
                $meta_input = array(
                    'total_views' => rand ( 100 , 500 ),
                    'total_likes' => rand ( 0 , 100 ),
                    'total_shares' => rand ( 0 , 50 ),
                    'number_of_videos_posted' => rand ( 0 , 3 ),
                );
                break;
            case 'Bitly':
                $meta_input = array(
                    'clicks' => rand ( 0 , 100 ),
                    'clicks_per_tag' => rand ( 0 , 100 ),
                );
                break;
            case 'Bibles':
                $meta_input = array(
                    'given_by_hand' => rand ( 0 , 100 ),
                    'given_online' => rand ( 0 , 100 ),
                    'downloaded_from_website' => rand ( 0 , 100 ),
                );
                break;
            case 'Contacts':
                $meta_input = array(
                    'contacts_added' => rand ( 0 , 100 ),
                    'assignable_contacts' => rand ( 0 , 100 ),
                    'contact_attempted' => rand ( 0 , 100 ),
                    'contact_established' => rand ( 0 , 100 ),
                    'first_meeting_complete' => rand ( 0 , 100 ),
                    'baptisms_count' => rand ( 0 , 100 ),
                    '1_gen_baptisms' => rand ( 0 , 100 ),
                    '2_gen_baptisms' => rand ( 0 , 100 ),
                    '3_gen_baptisms' => rand ( 0 , 100 ),
                    '4_gen_baptisms' => rand ( 0 , 100 ),
                    'baptizers' => rand ( 0 , 100 ),
                );
                break;
            case 'Groups':
                $meta_input = array(
                    'total_groups' => rand ( 0 , 100 ),
                    '2x2' => rand ( 0 , 100 ),
                    '3x3' => rand ( 0 , 100 ),
                    'total_active_churches' => rand ( 0 , 100 ),
                    '1_gen_churches' => rand ( 0 , 100 ),
                    '2_gen_churches' => rand ( 0 , 100 ),
                    '3_gen_churches' => rand ( 0 , 100 ),
                    '4_gen_churches' => rand ( 0 , 100 ),
                    'church_planters' => rand ( 0 , 100 ),
                );
                break;
            default:
                $meta_input = array();
                break;

        }
    }

}