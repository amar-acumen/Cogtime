<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
  | -------------------------------------------------------------------------
  | URI ROUTING
  | -------------------------------------------------------------------------
  | This file lets you re-map URI requests to specific controller functions.
  |
  | Typically there is a one-to-one relationship between a URL string
  | and its corresponding controller class/method. The segments in a
  | URL normally follow this pattern:
  |
  |	example.com/class/method/id/
  |
  | In some instances, however, you may want to remap this relationship
  | so that a different class/function is called than the one
  | corresponding to the URL.
  |
  | Please see the user guide for complete details:
  |
  |	http://codeigniter.com/user_guide/general/routing.html
  |
  | -------------------------------------------------------------------------
  | RESERVED ROUTES
  | -------------------------------------------------------------------------
  |
  | There area two reserved routes:
  |
  |	$route['default_controller'] = 'welcome';
  |
  | This route indicates which controller class should be loaded if the
  | URI contains no data. In the above example, the "welcome" class
  | would be loaded.
  |
  |	$route['404_override'] = 'errors/page_missing';
  |
  | This route will tell the Router what URI segments to use if those provided
  | in the URL cannot be matched to a valid route.
  |
 */

$route['default_controller'] = "index";
$route['404_override'] = '';
$route['logout'] = "session/logout";
$route['forgot-password'] = "login/forgot_password";


/******************Help Center*******************/
$route['help_center'] = "logged/help_center/index";
$route['(:num)/help_center_details'] = "logged/help_center/help_center_category_details/$1";
$route['(:num)/help_center_question_details'] = "logged/help_center/help_center_question_details/$1";
/*************************************/
$route['advertisement_request'] = "logged/advertisement_request/index";
$route['my-wall'] = "logged/my_wall/index";
$route['wall-privacy-settings'] = "logged/my_wall/wall_privacy_settings";



$route['my-profile'] = "logged/my_profile/index";
$route['change-password'] = "logged/change_password/index";


$route['my-friends'] = "logged/my_friends/index";
$route['mutual-friends'] = "logged/mutual_friends/index";
$route['friend-request'] = "logged/my_friends/friend_request";
$route['search-invite-friends'] = "logged/my_friends/search_invite_friends";
$route['find-friends'] = "logged/my_friends/find_friends";
$route['find-friends/(:any)'] = "logged/my_friends/find_friends/$1";
$route['my-contacts/(:any)'] = "logged/my_friends/my_contacts/$1";


$route['my-net-pals'] = "logged/my_net_pals/index";
$route['net-pal-request'] = "logged/my_net_pals/net_pals_request";
$route['search-invite-net-pals'] = "logged/my_net_pals/search_invite_net_pals";

$route['my-prayer-partners'] = "logged/my_prayer_partners/index";
$route['prayer-partner-request'] = "logged/my_prayer_partners/prayer_partner_request";
$route['search-invite-prayer-partner'] = "logged/my_prayer_partners/search_invite_prayer_partner";


$route['my-photos'] = "logged/my_photos/index";
$route['all-photo-albums'] = "logged/my_photos/view_all_photo_albums";
$route['manage-my-photo'] = "logged/manage_my_photo/index";
$route['create-photo-album'] = "logged/create_photo_album/index";
$route['photo-albums/(:num)/organize-photo'] = "logged/organize_photo/index/$1";
$route['photo-albums/(:num)/edit-photo-album'] = "logged/create_photo_album/edit_photo_album/$1";

$route['photo-album-detail/(:num)/(:any)'] = "logged/my_photo_album_details/index/$1/$2";
$route['photo-detail/(:num)/(:any)'] = "logged/my_photo_album_details/photo_detail/$1/$2";




### VIDEO SECTION ###
$route['my-videos'] = "logged/my-videos/index";
$route['manage-video-album'] = "logged/my_videos/manage_video_album";
$route['video-albums/(:num)/organize-video'] = "logged/organize_my_videos/index/$1";
$route['create-video-album'] = "logged/my_videos/create_video_album";
$route['video-album-detail/(:num)/(:any)'] = "logged/my_videos/my_video_album_details/$1/$2";
$route['edit-album/(:num)/(:any)'] = "logged/my_videos/manage_edit_album/$1/$2";
$route['video-detail/(:num)/(:any)'] = "logged/video_details/index/$1/$2";
$route['all-video-albums'] = "logged/my_videos/view_all_video_albums";


### END OF VIDEO SECTION ###



$route['my-audios'] = "logged/my-audios/index";
$route['all-audio-albums'] = "logged/my-audios/view_all_audio_albums";
$route['manage-my-audio'] = "logged/manage_my_audio/index";
$route['create-audio-album'] = "logged/create_audio_album/index";
$route['audio-albums/(:num)/organize-audio'] = "logged/organize_audio/index/$1";
$route['audio-albums/(:num)/edit-audio-album'] = "logged/create_audio_album/edit_audio_album/$1";

$route['audio-album-detail/(:num)/(:any)'] = "logged/my_audio_album_details/index/$1/$2";
$route['audio-detail/(:num)/(:any)'] = "logged/my_audio_album_details/audio_detail/$1/$2";



$route['tweets'] = "logged/tweets/index";
//$route['my-tweets']= "logged/tweets/my_tweets";
$route['my-friends-tweets'] = "logged/tweets/my_friends_tweets";

## new tweets
$route['tweets'] = "logged/tweet_home/index";
$route['my-tweets'] = "logged/tweet_home/my_tweets";
$route['search-people'] = "logged/tweet_home/search_people";
$route['my-favourite-tweets'] = "logged/tweet_home/my_fav_tweets";

$route['my-followings'] = "logged/tweet_home/my_followings";
$route['my-followers'] = "logged/tweet_home/my_followers";


$route['trends'] = "logged/tweet_home/trends";
$route['search-trends/(:num)/(:any)'] = "logged/tweet_home/search_trends/$1";


### tweet public profile
$route['user-twitter-profile/(:num)/followings'] = "logged/user_twitter_profile/my_followings/$1";
$route['user-twitter-profile/(:num)/followers'] = "logged/user_twitter_profile/my_followers/$1";
$route['user-twitter-profile/(:num)/(:any)'] = "logged/user_twitter_profile/index/$1";









$route['blogs'] = "logged/my-blog/all_blogs";
$route['my-blog'] = "logged/my-blog/index";
$route['create-my-blog'] = "logged/my-blog/create_my_blog";
$route['edit-my-blog'] = "logged/my-blog/edit_my_blog";

$route['search-blogs'] = "logged/my-blog/search_blogs";
$route['most-popular-blogs'] = "logged/my-blog/most_popular_blogs";
$route['blog/(:num)/detail'] = "logged/my-blog/blog_detail/$1";




$route['browse-chat-room'] = "logged/chat_rooms/index";

$route['my-msg-inbox'] = "logged/mymessages/index";
$route['my-msg-outbox'] = "logged/my_msg_outbox/index";
$route['my-msg-trashbox'] = "logged/my_msg_outbox/msg_trash";



$route['compose-msg'] = "logged/compose_msg/index";
$route['message/(:num)/(:num)/reply-msg'] = "logged/compose_msg/reply_message/$1/$2";


$route['organize-calender-view'] = "logged/organize_calender_view/index";
$route['organizer-day-view'] = "logged/organizer_day_view/index";
$route['organizer-month-view'] = "logged/organizer_day_view/organizer_month_view";
$route['organizer-week-view'] = "logged/organizer_day_view/organizer_week_view";


#calender ajax on page ()
$route['show_event_calendar/(:num)/(:num)/(:num)/(:num)'] = "base_controller/show_event_calendar/$1/$2/$3/$4";
$route['organizer/(:num)/(:num)/(:num)/organizer-day-view'] = "logged/organizer_day_view/index/$1/$2/$3";


$route['browse-chat-room'] = "logged/browse_chat_room/index";
$route['create-chat-room'] = 'logged/browse_chat_room/create_chat_room';
$route['my-chat-rooms'] = 'logged/browse_chat_room/my_chat_room';

$route['private-chat-room'] = "logged/browse_chat_room/my_private_room";
$route['prayer-chat-room'] = "logged/browse_chat_room/my_prayer_room";
$route['ring-chat-room'] = "logged/browse_chat_room/my_ring_room";

$route['room/(:num)/edit-chat-room'] = "logged/browse_chat_room/edit_chat_room/$1";

### public profile ###


$route['public-profile/(:num)/photo-album'] = "public_profile_view_all_photo/index/$1";
$route['public-profile-photo/(:num)/album/(:num)/photos'] = "public_profile_photos/index/$1/$2";
$route['public-profile-photo/(:num)/album/(:num)/photo-details'] = "public_profile_photos/photo_detail/$1/$2";

$route['public-profile/(:num)/video-album'] = "public_profile_view_all_video/index/$1";
$route['public-profile-video/(:num)/album/(:num)/videos'] = "public_profile_videos/index/$1/$2";
$route['public-profile-video/(:num)/album/(:num)/video-details'] = "public_profile_videos/video_detail/$1/$2";


$route['public-profile/(:num)/audio-album'] = "public_profile_view_all_audio/index/$1";
$route['public-profile-audio/(:num)/album/(:num)/audios'] = "public_profile_audios/index/$1/$2";
$route['public-profile-audio/(:num)/album/(:num)/audio-details'] = "public_profile_audios/audio_detail/$1/$2";


## all friends
$route['public-profile/(:num)/all-friends'] = "public_profile/all_friends/$1";
$route['public-profile/(:num)/all-netpals'] = "public_profile/all_netpals/$1";


$route['public-profile/(:num)/(:any)'] = "public_profile/index/$1/$2";
$route['profile/(:num)/(:any)'] = "prayer_partner_public_profile/index/$1/$2";

$route['prayer-partner-public-profile/(:num)/(:any)'] = "prayer_partner_public_profile/index/$1/$2";







### public profile ###


$route['modify-my-profile-personal-info-ajax'] = "logged/my_profile/modify_my_profile_personal_info_ajax";
$route['modify-my-profile-basic-info-ajax'] = "logged/my_profile/modify_my_profile_basic_info_ajax";
$route['modify-my-profile-edu-info-ajax'] = "logged/my_profile/modify_my_profile_edu_info_ajax";
$route['modify-my-profile-work-info-ajax'] = "logged/my_profile/modify_my_profile_work_info_ajax";
$route['modify-my-profile-skill-info-ajax'] = "logged/my_profile/modify_my_profile_skill_info_ajax";



$route['cms/(:num)/(:any)'] = "cms_pages/index/$1";
$route['social-hub-public'] = "banner_pages/social_hub_public";
$route['trade-center-public'] = "banner_pages/trade_center";
$route['media-center-public'] = "banner_pages/media_center";
$route['social-hub-public'] = "banner_pages/social_hub_public";
$route['holy-place-public'] = "banner_pages/holy_place";
$route['build-the-kingdom-public'] = "banner_pages/build_kingdom";


####### events ###############
$route['upcoming-events'] = "events/index";
$route['all-events'] = "logged/events/index";
$route['events/(:num)/(:any)'] = "events/event_detail/$1";
$route['event-detail/(:num)/(:any)'] = "logged/events/event_detail/$1";

$route['my-events'] = "logged/my_events/index";
$route['event-invitations-received'] = "logged/my_events/events_invitations_recieved";

$route['create-event'] = "logged/create_event/index";
$route['edit-event/(:num)/(:any)'] = "logged/create_event/edit_event/$1/$2";
$route['archived-events'] = "logged/my_events/archive_events";
$route['find-events'] = "logged/events/find_events";
$route['events-rsvp-recevied'] = "logged/my_events/events_rsvps_recieved";


#######
###########signup-confirm
$route['signup-confirm/(:num)/(:any)'] = "register/signup_confirm/$1/$2";
$route['successfully-registered'] = "register/registration_success";



#private pages

$route['social-hub'] = "banner_pages/social_hub_public";
$route['trade-center'] = "banner_pages/trade_center";

$route['media-center'] = "banner_pages/media_center";
$route['word-for-today'] = "banner_pages/media_center/word_for_today_private";


$route['social-hub'] = "banner_pages/social_hub_public";
$route['holy-place'] = "banner_pages/holy_place";
$route['build-the-kingdom'] = "banner_pages/build_kingdom";


# user notifications routings
$route['user-alert-settings'] = "logged/user_alert_settings/index";
$route['user-email-settings'] = "logged/user_email_settings/index";



################ user ring ################

$route['rings'] = "logged/all_rings/index";


$route['my-ring'] = "logged/my_ring/index";
$route['create-my-ring'] = "logged/my_ring/create_my_ring";
$route['rings/(:num)/ring-home'] = "logged/ring_home/index/$1";
$route['rings/(:num)/ring-members'] = "logged/ring_members/index/$1";
$route['search-ring'] = "logged/my_ring/search_ring";
$route['search-ring'] = "logged/my_ring/search_ring";


$route['rings/(:num)/approve-join-request'] = "logged/ring_home/approve_join_request/$1";
$route['rings/(:num)/ring-approve-join-request'] = "logged/ring_home/approve_join_request_home/$1";


$route['rings/(:num)/invite-member'] = "logged/ring_home/invite_member/$1";
$route['my-ring/(:num)/edit'] = "logged/my_ring/edit/$1";

$route['approve-join-request'] = "logged/ring_home/approve_join_request";

#$route['edit-my-blog']= "logged/my-blog/edit_my_blog";
################ user ring ################
################ holy place ################
$route['holy-place/read-bible'] = "logged/holy_place/read_bible/";
$route['holy-place/read-bible/(:num)/(:num)'] = "logged/holy_place/read_bible/$1/$2";
$route['holy-place/bible/all-books'] = "logged/holy_place/all_books";
$route['holy-place/bible/(:any)/chapters'] = "logged/holy_place/books_chapter/$1";
$route['holy-place/bible/verses/(:any)/(:any)'] = "logged/holy_place/verses/$1/$2";
$route['holy-place/library'] = "logged/holy_place/bible_library/";
$route['holy-place/bible-library'] = "logged/holy_place/bible_library/";

$route['five-a-day'] = "logged/holy_place/five_a_day";
$route['my-daily-bible-reading-plan'] = "logged/my_daily_bible_reading_plan/index";



### prayer wall
$route['prayer-wall-home'] = "logged/prayer_wall/index";
$route['view-all-prayer-request'] = "logged/prayer_wall/view_all_prayer_request";
$route['manage-my-prayer-request'] = "logged/prayer_wall/manage_my_prayer_request";
$route['manage-my-commitments'] = "logged/prayer_wall/manage_my_commitments";
$route['prayer-wall/(:num)/prayer-wall-request-detail'] = "logged/prayer_wall/prayer_wall_request_detail/$1";
$route['search-prayer-request'] = "logged/prayer_wall/search_prayer_request";


## e-intercession
$route['view-search-eintercession'] = "logged/e_intercession/index";
$route['e-intercession/(:num)/e-intercession-request-detail'] = "logged/e_intercession/intercession_prayer_request_detail/$1";


### TESTIMONY ###
$route['prayer-wall-testimony'] = "logged/prayer_wall_testimony";
$route['intercession-testimony'] = "logged/intercession_testimony";
### TESTIMONY ###
### prayer group
$route['prayer-group'] = "logged/prayer_group/index";
$route['prayer-group-details/(:num)/(:any)'] = "logged/prayer_group/prayer_group_detail/$1";
$route['prayer-group/(:num)/search-and-invite'] = "logged/prayer_group/search_invite_friends/$1";
$route['prayer-group/(:num)/create-prayer-room'] = "logged/prayer_group/create_prayer_room/$1";
$route['search-and-join-prayer-group'] = "logged/prayer_group/search_prayer_group";

$route['request'] = "logged/prayer_group/request";


################ holy place ################
####################### Build the Kingdom ################################

$route['giving'] = "logged/build_the_kingdom/index";
$route['(:any)/charity-project-home'] = "logged/build_the_kingdom/charity_project_home/$1";
$route['view-my-projects'] = "logged/build_the_kingdom/view_my_projects";
$route['tithe-prayer-time'] = "logged/build_the_kingdom/search_prayer_request_eintercession";
$route['find-church'] = "logged/build_the_kingdom/findChurch";
$route['register-your-church'] = "logged/build_the_kingdom/registerChurch";
$route['bible-quiz'] = "logged/build_the_kingdom/bible_quiz";





####################### Build the Kingdom ################################
####################################### FRONT END MEDIA CENTER ##################################
$route['christian-news/(:any)/(:any)'] = "logged/christian_news/index/$1/$2";
$route['christian-news/read']      = "logged/christian_news/index";
$route['christian_news/category/(:num)'] = "logged/christian_news/category/$1";
$route['(:num)/(:any)/christian-news-details/(:any)'] = "logged/christian_news/christian_news_details/$1/$2/$3";

$route['watch/category/(:num)'] = "logged/media_center/category/$1";
$route['(:num)/(:any)/watch-detail/(:any)'] = "logged/media_center/watch_detail/$1/$2/$3";


$route['gospel-magazine'] = "logged/media_center/gospel_magazine";
$route['gospel-magazine-detail/(:num)/(:any)'] = "logged/media_center/gospel_magazine_detail/$1";
$route['watch'] = "logged/media_center/christan_project";
$route['listen'] = "logged/listen/index";
$route['listen/category/(:num)'] = "logged/listen/category/$1";
$route['christan-project-detail/(:num)/(:any)'] = "logged/media_center/christan_project_detail/$1";
$route['listen-detail/(:num)/(:any)'] = "logged/listen/details/$1/$2";

$route['minister-shout'] = "logged/media_center/minister_shout";


####################################### END FRONT END MEDIA CENTER ##################################
### start E Trade  ###
$route['etrade-home/create_product'] = "logged/e_trade/add_etrade_product";
$route['etrade-home'] = "logged/e_trade/index";
$route['etrade/manage-my-product'] = "logged/e_trade/manage_my_product";
$route['etrade/manage-buy-request-received'] = "logged/e_trade/manage_buy_request_received";
$route['etrade/manage-sent-request'] = "logged/e_trade/manage_sent_request";
$route['etrade/(:num)/edit-product'] = "logged/e_trade/edit_product/$1";
$route['etrade/(:num)/detail'] = "logged/e_trade/detail/$1";

$route['eswap/add-product'] = "logged/e_swap/add_product";
$route['eswap-home'] = "logged/e_swap/index";
$route['eswap/manage-my-product'] = "logged/e_swap/manage_my_product";
$route['eswap/request-received'] = "logged/e_swap/manage_recieved_request";
$route['eswap/sent-request'] = "logged/e_swap/manage_sent_request";
$route['eswap/(:num)/edit-product'] = "logged/e_swap/edit_product/$1";
$route['eswap/(:num)/detail'] = "logged/e_swap/detail/$1";


$route['efreebie/add-product'] = "logged/e_freebie/add_product";
$route['efreebie-home'] = "logged/e_freebie/index";
$route['efreebie/manage-my-product'] = "logged/e_freebie/manage_my_product";
$route['efreebie/request-received'] = "logged/e_freebie/manage_recieved_request";
$route['efreebie/sent-request'] = "logged/e_freebie/manage_sent_request";
$route['efreebie/(:num)/edit-product'] = "logged/e_freebie/edit_product/$1";
$route['efreebie/(:num)/detail'] = "logged/e_freebie/detail/$1";

$route['trade-activities'] = "logged/trade_activities/index";
$route['buy-product-listing-credit'] = "logged/trade_activities/purchaseCredit";

$route['minister-shout/(:any)/minister-list'] = 'logged/media_center/show_minister_list/$1';
$route['minister-shout/(:num)/minister-blog-detail'] = 'logged/my_blog/minister_blog_detail/$1';

$route['privacy-settings'] = "logged/privacy_settings";



## end E Trade ###
#### admin routings ###########

$route['dashboard'] = "admin/dashboard";
$route['sign-out'] = "session/admin_logout";
$route['admin/site_settings/hp-cms'] = "admin/site_settings/hp_cms";
$route['admin/site_settings/hp-banners'] = "admin/site_settings/hp_banners";
$route['admin/site_settings/hp-banners/add-information'] = "admin/site_settings/hp_banners/add_information";
$route['admin/site_settings/reset-password'] = "admin/site_settings/reset_password";

$route['admin/site_settings/advertisement'] = "admin/site_settings/advertisement";
$route['admin/site_settings/add-advertisement'] = "admin/site_settings/add_advertisement";
$route['admin/site_settings/edit-advertisement'] = "admin/site_settings/edit_advertisement";

$route['admin/site_settings/abuse-dictionary'] = "admin/site_settings/abuse_dictionary";
$route['admin/site_settings/denomination'] = "admin/site_settings/denomination";

$route['admin/site_settings/cms-pages'] = "admin/site_settings/cms_pages";
$route['admin/site_settings/add-cms-pages'] = "admin/site_settings/cms_pages/add_cms";
$route['admin/site_settings/cms-pages/edit-cms-pages/(:num)'] = "admin/site_settings/cms_pages/edit_cms/$1";

$route['admin/site_settings/admin-groups'] = "admin/site_settings/admin_groups";

$route['admin/site-settings/languages'] = "admin/site_settings/languages";
$route['admin/site-settings/prayer_partner_parameters'] = "admin/site_settings/prayer_partner_parameters";
$route['admin/site-settings/netpal_parameters'] = "admin/site_settings/netpal_parameters";
$route['admin/site_settings/add-language'] = "admin/site_settings/languages/add_lang";
$route['admin/site_settings/edit-language/(:num)'] = "admin/site_settings/languages/edit_lang/$1";

$route['admin/members'] = "admin/members/members";
$route['admin/member-posts/(:num)/(:any)'] = "admin/members_post/index/$1/$2";
$route['admin/site-settings/inbox']  = "admin/site_settings/inbox/index";
$route['admin/site-settings/all_comments']  = "admin/site_settings/all_comments/index";
$route['admin/site-settings/media_center_advertisement']  = "admin/site_settings/media_center_advertisement/index";
$route['admin/site-settings/advertisement_cost']  = "admin/site_settings/advertisement_cost";
#$route['admin/members/member-details/(:num)']  = "admin/members/member_details/index/$1";
#$route['admin/members/index/(:num)']    =   "admin/members/members/index/$1";



$route['admin/social-hub-landing-page'] = "admin/social_hub/social_hub_landing_page";

$route['admin/events'] = "admin/social_hub/events";
$route['admin/tweets'] = "admin/social_hub/tweets";
$route['admin/blogs'] = "admin/social_hub/blogs";
$route['admin/blogs/(:num)/blog-detail'] = "admin/social_hub/blog_detail/index/$1";


$route['admin/ring-categories'] = "admin/social_hub/ring_categories/index";
$route['admin/rings'] = "admin/social_hub/rings/index";
$route['admin/rings/(:num)/ring-details'] = "admin/social_hub/rings/ring_details/$1";

$route['admin/chat-rooms'] = "admin/social_hub/chat_rooms/index";
$route['admin/chat-categories'] = "admin/social_hub/chat_categories";
$route['admin/social-hub/chat-categories/(:num)/(:any)'] = "admin/social_hub/chat_categories/category_detail/$1";



################################################ TRADE CENTER #################################################
$route['admin/trade-center-landing-page'] = "admin/trade_center/trade_center_landing_page";
$route['admin/trade-center/product-categories'] = "admin/trade_center/product_categories";
$route['admin/trade-center/product-categories/(:num)/(:any)'] = "admin/trade_center/product_categories/category_detail/$1";

$route['admin/etrade-products'] = "admin/trade_center/etrade";

$route['admin/eswap-products'] = "admin/trade_center/eswap";
$route['admin/efreebie-products'] = "admin/trade_center/efreebie";

$route['admin/credit-history'] = "admin/trade_center/credit_history";





################################################ MEDIA CENTER ADMIN #################################################
$route['admin/media-center-landing-page'] = "admin/media_center/media_center_landing_page";
$route['admin/word-for-today'] = "admin/media_center/word_for_today";

$route['admin/(:num)/media-center-landing-videos'] = "admin/media_center/media_center_landing_videos/index/$1";
$route['admin/add-new-videos'] = "admin/media_center/add_new_videos";
$route['admin/media-center-video/(:num)/(:num)/edit-video'] = "admin/media_center/edit_video/index/$1/$2";

$route['admin/(:num)/audio'] = "admin/media_center/audio/index/$1";

$route['admin/(:num)/christian-news'] = "admin/media_center/christian_news/index/$1";
$route['admin/add_media_article'] = "admin/media_center/add_media_article";
$route['admin/(:num)/(:num)/edit-christian-news'] = "admin/media_center/edit_christian_news/index/$1/$2";
$route['admin/(:num)/(:num)/edit-christian-news-category'] = "admin/media_center/edit_christian_news_category/index/$1/$2";
$route['admin/(:num)/(:num)/edit-video-category'] = "admin/media_center/edit_video_category/index/$1/$2";
$route['admin/(:num)/(:num)/edit-audio-category'] = "admin/media_center/edit_audio_category/index/$1/$2";

$route['admin/gospel-magazine'] = "admin/media_center/gospel_magazine/gospel_magazine/index";
$route['admin/(:num)/gospel-magazine'] = "admin/media_center/gospel_magazine/gospel_magazine/index/$1";

$route['admin/add-new-article'] = "admin/media_center/gospel_magazine/add_new_article";
$route['admin/(:num)/(:num)/edit-article'] = "admin/media_center/gospel_magazine/edit_article/index/$1/$2";

$route['admin/chirstan-project'] = "admin/media_center/christian_project";
$route['admin/(:num)/chirstan-project'] = "admin/media_center/christian_project/index/$1";
$route['admin/add-new-project'] = "admin/media_center/christian_project/add_new_project";
$route['admin/(:num)/(:num)/edit-project'] = "admin/media_center/christian_project/edit_project/$1/$2";

$route['admin/minister-shouts'] = "admin/media_center/minister_shouts/index";
$route['admin/add-your-shout'] = "admin/media_center/minister_shouts/add_shouts";


################################################ END MEDIA CENTER ADMIN ################################################# 
############################################### HOLY PLACE ################################################
$route['admin/holy-place-landing-page'] = "admin/holy_place/holy_place_landing_page";

$route['admin/holy-place/read-bible'] = "admin/holy_place/read_bible/books";
$route['admin/holy-place/prayer-wall'] = "admin/holy_place/prayer_wall/prayer_wall/index";
$route['admin/holy-place/(:num)/prayer-wall'] = "admin/holy_place/prayer_wall/prayer_wall/index/$1";
$route['admin/holy-place/(:num)/(:num)/edit-prayer-wall'] = "admin/holy_place/prayer_wall/edit_prayer_wall/index/$1/$2";


$route['admin/holy-place/(:num)/testimonies'] = "admin/holy_place/testimonies/index/$1";
#$route['admin/holy-place/(:num)/(:num)/edit-testimonies'] = "admin/holy_place/testimonies/edit_testimonies/$1/$2";

$route['admin/holy-place/intercession'] = "admin/holy_place/intercession/index";
$route['admin/holy-place/(:num)/intercession'] = "admin/holy_place/intercession/index/$1";
$route['admin/holy-place/add-intercession'] = "admin/holy_place/intercession/add_intercession";
$route['admin/holy-place/(:num)/(:num)/edit-intercession'] = "admin/holy_place/intercession/edit_intercession/$1/$2";


$route['admin/holy-place/prayer-wall-photos'] = "admin/holy_place/prayer_wall_photos/index";
$route['admin/holy-place/(:num)/prayer-wall-photos'] = "admin/holy_place/prayer_wall_photos/index/$1";
$route['admin/holy-place/add-prayer-wall-photos'] = "admin/holy_place/prayer_wall_photos/add_prayer_wall_photos";
$route['admin/holy-place/(:num)/(:num)/edit-prayer-wall-photos'] = "admin/holy_place/prayer_wall_photos/edit_prayer_wall_photos/$1/$2";
$route['admin/holy-place/add-concordence'] = "admin/holy_place/concordence/add_concordence";
$route['admin/holy-place/concordence/edit-concordence'] = "admin/holy_place/concordence/edit_concordence/$1";
############################################### END HOLY PLACE ################################################
##### build the kingdom admin #######

$route['admin/charity-projects'] = "admin/build_kingdom/charity_projects/index";
$route['admin/charity-projects/add-info'] = "admin/build_kingdom/charity_projects/add_info";
$route['admin/charity-projects/(:num)/edit-charity-project'] = "admin/build_kingdom/charity_projects/edit_info/$1";
$route['admin/salvation-prayer'] = "admin/build_kingdom/salvation_prayer/index";



$route['admin/charity-projects/(:num)/manage-skill-donations'] = "admin/build_kingdom/charity_projects/manage_skill_donations/$1";
$route['admin/salvation-prayer'] = "admin/build_kingdom/salvation_prayer/index";
$route['admin/donation'] = "admin/build_kingdom/donation/index";
$route['admin/bible-quiz'] = "admin/build_kingdom/bible_quiz/index";
$route['admin/churches'] = "admin/build_kingdom/churches/index";
$route['admin/build-the-kingdom-landing-page'] = "admin/build_kingdom/build_kingdom_landing_page/index";



$route['admin/common-settings'] = "admin/site_settings/settings";
$route['admin/admin-group/(:num)/view-members'] = "admin/site_settings/admin_user/index/$1";

###new for subadmin
$route['admin/admin-groups'] = "admin/site_settings/admin_groups";
$route['admin/product-categories'] = "admin/trade_center/product_categories";
$route['admin/product-categories/(:num)/(:any)'] = "admin/trade_center/product_categories/category_detail/$1";

$route['admin/advertisement'] = "admin/site_settings/advertisement";
$route['admin/abuse-dictionary'] = "admin/site_settings/abuse_dictionary";
$route['admin/denomination'] = "admin/site_settings/denomination";
$route['admin/cms-pages'] = "admin/site_settings/cms_pages";

#$route['admin/scrolling-headlines']  = "admin/site_settings/scrolling_headlines";
$route['admin/scrolling-headlines'] = "admin/site_settings/reset_password/headlines";
$route['admin/media-center-landing-videos'] = "admin/media_center/media_center_landing_videos/index/$1";

$route['admin/(:num)/media_article'] = "admin/media_center/media_article/index/$1";
$route['admin/(:num)/media_article_category'] = "admin/media_center/media_article_category/index/$1";
$route['admin/(:num)/media_video_category'] = "admin/media_center/media_video_category/index/$1";
$route['admin/(:num)/media_audio_category'] = "admin/media_center/media_audio_category/index/$1";
$route['admin/(:num)/api_settings'] = "admin/media_center/api_settings/index";
$route['admin/(:num)/mediacenter_landing_page_text'] = "admin/media_center/mediacenter_landing_page_text/index";
$route['admin/intercession'] = "admin/holy_place/intercession/index";

$route['admin/manage-product-category-attribute'] = "admin/trade_center/product_attributes/index";

$route['admin/social-hub/ring-categories/(:num)/(:any)'] = "admin/social_hub/ring_categories/category_detail/$1";
$route['admin/holy-place/concordence'] = "admin/holy_place/concordence/index";

$route['admin/charity-projects/(:num)/requested-informations'] = "admin/build_kingdom/charity_projects/requested_informations/$1";
$route['admin/site-settings/(:any)/messages'] = "admin/site_settings/messages/index/$1";

$route['admin/holy-place/daily-bible-verse'] = "admin/holy_place/daily_bible_verse/index";

$route['admin/holy-place/publish-bible-verse'] = "admin/holy_place/daily_bible_verse/add";

$route['admin/holy-place/(:num)/daily-bible-verse'] = "admin/holy_place/daily_bible_verse/index/$1";

/******************Help Center*********************************************/
$route['admin/help_center'] = "admin/help_center/help_center/index/$1";
$route['admin/add_help_center'] = "admin/help_center/add_help_center/index/";
$route['admin/help_center_category'] = "admin/help_center/help_center_category/index/$1";
$route['admin/(:num)/edit-help-center-category'] = "admin/help_center/edit-help-center-category/index/$1";
$route['admin/(:num)/edit_help_center'] = "admin/help_center/edit_help_center/index/$1";
/***************************************************************/

$route['admin/site-settings/manage-admin'] = "admin/site_settings/reset_password/manage_admin";
$route['deactivate-account-by-link/(:num)'] = "deactivate_account_by_link/index/$1";
$route['deactivate-account'] = "logged/deactivate_account/index";
$route['activate-account/(:num)'] = "activate_account/index/$1";

# !!!!!!!!!!!! NEW ROUTING DEFINITIONs [END] !!!!!!!!!!!!

$route['audio-detail'] = "logged/media_center/audio_detail";
$route['searchlist'] = "logged/media_center/christan_project_search";
$route['category-media'] = "logged/media_center/christan_project_cat";
$route['event-detail/invite-member/(:num)']= "logged/events/invite_member/$1";
$route['event-detail/invitation-sent/(:num)/event']= "logged/events/invite_sent/$1";
$route['archive-intercession'] = "logged/e_intercession/archive_index";
/* End of file routes.php */
/* Location: ./application/config/routes.php */


/************************for church*******************************************************/

$route['church/(:any)/(:num)'] = "logged/church/index/$1/$2";


$route['church_public/(:any)/(:num)'] = "church_public/index/$1/$2";
$route['church_registration_by_email/(:num)/(:num)'] = "church_public/church_registration_by_email/$1/$2";
$route['church_registration_by_email/(:num)/(:num)/(:num)'] = "church_public/church_registration_by_email/$1/$2/$3";
$route['already_user/(:num)/(:num)/(:num)/(:num)'] = "church_public/already_user/$1/$2/$3/$4"; 
$route['church_registration'] = "church_public/church_registration";
$route['church_registration/(:any)'] = "church_public/church_registration/$1";
$route['church-successfully-registered'] = "church_public/registration_success";
$route['church_member'] = "logged/church/church_member";
$route['church_invited_member'] = "logged/church/church_invited_member";
$route['church_setting'] = "logged/church/church_setting";
$route['(:num)/church-wall']="logged/church_wall/index/$1";
$route['(:num)/church_members'] = "logged/church_members/index/$1";
$route['(:num)/church_activity_management'] = "logged/church_activity_management/index/$1";
$route['(:num)/church_wall_about'] = "logged/church_wall_about/index/$1";
$route['church_plugin'] = "logged/church/church_plugin";

/**************************************************************************************/


/**********************************church ring****************************************************/
//$route['my-ring'] = "logged/my_ring/index";
$route['create-church-ring'] = "logged/church_ring/create_church_ring";
$route['church-ring-list'] = "logged/church_ring/index/$1";

$route['church-ring/(:num)/edit'] = "logged/church_ring/edit/$1";
$route['member_ring/(:num)'] = "logged/church_ring/member_ring/$1";
$route['search_ring'] = 'logged/church_ring/search_ring/';
$route['ring_request'] = 'logged/church_ring/approve_join_request/';
$route['create_ring_category'] = 'logged/church_ring/create_ring_category/';
/*$route['rings/(:num)/ring-home'] = "logged/ring_home/index/$1";*/
$route['church-rings-members/(:num)'] = "logged/church_ring_home/members/$1";
/*$route['search-ring'] = "logged/my_ring/search_ring";
$route['search-ring'] = "logged/my_ring/search_ring";*/
/**********************************church ring****************************************************/

/**********************************church subadmin****************************************************/
$route['church-subadmin'] = "logged/church/church_subadmin";
$route['church-addsubadmin'] = "logged/church/church_addsubadmin";
$route['church-editsubadmin/(:num)'] = "logged/church/church_editsubadmin/$1";

/**********************************church subadmin****************************************************/
$route['(:num)/church-prayer-group']="logged/church_prayer_group/index/$1";
$route['(:num)/create-prayer-group']="logged/church_prayer_group/create_prayer_group/$1";
$route['church-prayer-group/(:num)']="logged/church_prayer_group/prayer_group_detail/$1";
$route['church-prayer-group-member/(:num)']="logged/church_prayer_group/prayer_group_members/$1";
$route['church-prayer-group-invite/(:num)']="logged/church_prayer_group/search_invite_friends/$1";
$route['church-prayer-group-invite/(:num)']="logged/church_prayer_group/search_invite_friends/$1";
$route['(:num)/request']="logged/church_prayer_group/request/$1";
$route['(:num)/search-and-join-church-prayer-group'] = "logged/church_prayer_group/search_prayer_group/$1";

/************************************church ring details***************************************************/
$route['church_rings/(:num)/ring-home'] = "logged/church_ring_home/index/$1";


/**********************************create church space at logged in section****************************************************/
$route['create-church-space'] = 'logged/create_church_space/index';
$route['find-church-space'] = 'logged/search_church_space/index';

/** admin prayer councillor section **/
$route['admin/holy-place/prayer_councillor'] = "admin/holy_place/prayer_councillor/index";
$route['admin/holy-place/add-prayercouncillor'] = "admin/holy_place/prayer_councillor/add_prayer_councillor";



/************************pre login event **********************************/
$route['upcoming-events'] = 'index/event_details';
/*************for church space in admin panel***********************************************/
$route['admin/church_spaces'] = "admin/build_kingdom/churches/space_view";