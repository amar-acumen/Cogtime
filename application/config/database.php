<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/


$active_group = 'flashchat';
$active_record = TRUE;

$db['flashchat']['hostname'] = '212.227.138.184';
$db['flashchat']['username'] = 'cogtime_dbuser';
$db['flashchat']['password'] = 'qBzw29wH';
$db['flashchat']['database'] = 'flashchat';
$db['flashchat']['dbdriver'] = 'mysql';
$db['flashchat']['dbprefix'] = '';
$db['flashchat']['pconnect'] = FALSE;
$db['flashchat']['db_debug'] = TRUE;
$db['flashchat']['cache_on'] = FALSE;
$db['flashchat']['cachedir'] = '';
$db['flashchat']['char_set'] = 'utf8';
$db['flashchat']['dbcollat'] = 'utf8_general_ci';
$db['flashchat']['swap_pre'] = '';
$db['flashchat']['autoinit'] = TRUE;
$db['flashchat']['stricton'] = FALSE;
$dbprefix = $db['flashchat']['dbprefix'];

$active_group = 'default';
$active_record = TRUE;

$db['default']['hostname'] = 'localhost';
$db['default']['username'] = 'acumencs_siddhi';
$db['default']['password'] = 'siddhi@acu#1!';
$db['default']['database'] = 'acumencs_cogtime';
$db['default']['dbdriver'] = 'mysqli';
$db['default']['dbprefix'] = 'cg_';
$db['default']['pconnect'] = FALSE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;


/* End of file database.php */
/* Location: ./application/config/database.php */
$dbprefix = $db['default']['dbprefix'];

$db['default']['USERS'] = $dbprefix."users";
$db['default']['USER_EDUCATION'] = $dbprefix."user_education";
$db['default']['USER_WORKS'] = $dbprefix."user_work";
$db['default']['USER_SKILL'] = $dbprefix."user_skill";


$db['default']['LOGIN_LOGS'] = $dbprefix."login_logs";
$db['default']['USERS_ONLINE'] = $dbprefix."users_online";

$db['default']['DENOMINATION'] = $dbprefix."denomination";

### CMS AND PAGE CONTENT ####
$db['default']['ADVERTISEMENT'] = $dbprefix."advertisement";
$db['default']['CMS_PAGE'] = $dbprefix."cms_page";
$db['default']['HP_CMS'] = $dbprefix."hp_cms";
$db['default']['MAIL_CONTENTS'] = $dbprefix."mail_contents";
$db['default']['HP_BANNERS'] = $dbprefix."hp_banners";


$db['default']['MST_COUNTRY'] = $dbprefix."mst_country";	




### users part db ####
$db['default']['USER_AUDIO'] = $dbprefix."user_audio";
$db['default']['AUDIO_ALBUM'] = $dbprefix."audio_album";



$db['default']['USER_PHOTOS'] = $dbprefix."user_photos";
$db['default']['PHOTO_ALBUM'] = $dbprefix."photo_album";

$db['default']['VIDEO_ALBUM'] = $dbprefix."video_album";
$db['default']['USER_VIDEOS'] = $dbprefix."user_videos";

$db['default']['USER_MEDIA_COMMENTS'] = $dbprefix."user_media_comments";
$db['default']['USER_MEDIA_LIKE'] = $dbprefix."user_media_like";
$db['default']['USER_MEDIA_UNLIKE'] = $dbprefix."user_media_unlike";



### USER'S WALL POSTS AND COMMENTS ###
$db['default']['USER_NEWSFEEDS'] = $dbprefix."user_newsfeeds";
$db['default']['USER_NEWSFEED_COMMENTS'] = $dbprefix."user_newsfeed_comments";
$db['default']['USER_NEWSFEED_LIKE'] = $dbprefix."user_newsfeed_like";
$db['default']['USER_NEWSFEED_UNLIKE'] = $dbprefix."user_newsfeed_unlike";
### END OF USER'S WALL POSTS AND COMMENTS ###

## message #####
$db['default']['MESSAGES'] = $dbprefix."messages";
$db['default']['admin_messages'] = $dbprefix."admin_messages";
## end message #####

### USER FRIENDS AND CONTACT REQUEST ###
$db['default']['USER_CONTACTS'] = $dbprefix."user_contacts";
$db['default']['FRIENDS'] = $dbprefix."friends";
### END OF USER FRIENDS AND CONTACT REQUEST ###


### NET PALS REQUEST ###
$db['default']['NETPAL']=$dbprefix."users_net_pal_contacts";
### END OF NET PALS REQUEST ###


$db['default']['USER_PRAYER_PARTNER'] = $dbprefix."prayer_partner";
$db['default']['PRAYER_PARTNER_POINTS'] = $dbprefix."prayer_partners_prayer_points";


## EVENTS ###
$db['default']['EVENTS'] = $dbprefix."events";
$db['default']['EVENTS_EMAIL_INVITED'] = $dbprefix."event_email_invited";
$db['default']['EVENTS_FEEDBACK'] = $dbprefix."event_feedback";
$db['default']['EVENT_RSVP'] = $dbprefix."event_rsvp";
$db['default']['EVENTS_USER_INVITED'] = $dbprefix."event_user_invited";
$db['default']['EVENT_COMMENTS'] = $dbprefix."event_comments";
## END EVENTS ##

### NEWS TABLES ####

$db['default']['CHRISTIAN_HEADLINE'] = $dbprefix."christian_headline";
$db['default']['ORGANIZER_NOTE'] = $dbprefix."organizer_note";
$db['default']['ORGANIZER_TO_DO_LIST'] = $dbprefix."organizer_to_do_list";
$db['default']['ORGANIZER_EVENT'] = $dbprefix."organizer_event";

# notification table ###################
$db['default']['NOTIFICATIONS'] = $dbprefix."notifications";
$db['default']['USER_ALERTS'] = $dbprefix."user_alerts";
$db['default']['SYSTEM_REMINDER'] = $dbprefix."system_reminder";
$db['default']['USER_EMAIL_ALERTS'] = $dbprefix."user_email_alert";

####################################################################


## SOCIAL NETWORKS DB #####
$db['default']['USER_BLOGS'] = $dbprefix."user_blogs";
$db['default']['USER_BLOG_POST'] = $dbprefix."user_blog_post";
$db['default']['USER_BLOG_POST_COMMENTS'] = $dbprefix."user_blog_post_comments";
$db['default']['USER_TWEETS'] = $dbprefix."user_tweets";


## new tables for tweet
$db['default']['TWEETS'] = $dbprefix."tweets";
$db['default']['TWEETS_FOLLOWERS'] = $dbprefix."tweets_followers";
$db['default']['TWEETS_TRENDINGS'] = $dbprefix."tweets_trendings";
$db['default']['TWEETS_REPLYS'] = $dbprefix."tweets_replys";
$db['default']['TWEETS_FAV'] = $dbprefix."tweets_fav";



## SOCIAL NETWORKS DB #####


## RING TABLES ##
$db['default']['RING'] = $dbprefix."user_ring";
$db['default']['RING_CAT'] = $dbprefix."ring_category";
$db['default']['RING_INV_USER'] = $dbprefix."ring_invited_user";

$db['default']['USER_RING_POST'] = $dbprefix."user_ring_post";
$db['default']['USER_RING_POST_COMMENTS'] = $dbprefix."user_ring_post_comments";
$db['default']['USER_RING_POST_LIKE'] = $dbprefix."user_ring_post_like";
$db['default']['USER_RING_POST_UNLIKE'] = $dbprefix."user_ring_post_unlike";
$db['default']['ring_invitation']=$dbprefix."ring_invitation";
## RING TABLES ##



## chat tables ##

#$db['default']['CHAT_ROOM'] = $dbprefix."chat_room";
$db['default']['CHAT_ROOM_INVITATION'] = $dbprefix."prayer_grp_chat_room_invitation"; 

## chat tables ##




####### END OF NEWS TABLES ###3

### ADMIN TABLES #####
$db['default']['LANDING_PAGE_CONTENT'] = $dbprefix."landing_page_content";


$db['default']['ADMIN_USER'] = $dbprefix."admin_user";
$db['default']['ADMIN_USER_GRP'] = $dbprefix."admin_user_grp";
$db['default']['ADMIN_GRP_PRIVILEGE'] = $dbprefix."admin_grp_privilege";
$db['default']['ABUSE_DICTIONARY'] = $dbprefix."abuse_dictionary";
$db['default']['DENOMINATION'] = $dbprefix."denomination";
##language##
$db['default']['LANGUAGE'] = $dbprefix."language";

##### END ADMIN TABLES ####

### E-TRADE CENTER TABLES ###
$db['default']['TRADE_CAT'] = $dbprefix."trade_category";
$db['default']['ETRADE_PROD'] = $dbprefix."etrade_product";
$db['default']['ETRADE_REQUEST'] = $dbprefix."etrade_request";
$db['default']['ESWAP_PROD'] = $dbprefix."eswap_product";
$db['default']['ESWAP_WANTPROD'] = $dbprefix."eswap_want_product";
$db['default']['ESWAP_REQ'] = $dbprefix."eswap_request";
$db['default']['EFREE_PROD'] = $dbprefix."efreebie_product";
$db['default']['EFREE_REQ'] = $dbprefix."efreebie_request";

$db['default']['purchase_credit_history'] = $dbprefix."purchase_credit_history";
$db['default']['user_credits'] = $dbprefix."user_credits";


### END E-TRADE CENTER TABLES ###






######## HOLY PLACE #########
$db['default']['BIBLE_BOOKS'] = $dbprefix."bible_books";
$db['default']['BIBLE_PRAYER_REQUEST'] = $dbprefix."bible_prayer_request";
$db['default']['BIBLE_INTERCESSION'] = $dbprefix."bible_intercession";
$db['default']['BIBLE_INTERCESSION_WALL_POST_TESTIMONY'] = $dbprefix."bible_intercession_wall_post_testimony";
$db['default']['BIBLE_INTERCESSION_COMMITMENTS'] = $dbprefix."bible_intercession_commitments";

$db['default']['BIBLE_PRAYER_REQUEST_TESTIMONIES'] = $dbprefix."bible_prayer_request_testimonies";
$db['default']['BIBLE_PRAYER_COMMITMENTS'] = $dbprefix."bible_prayer_commitments";


$db['default']['BIBLE_BOOK'] = $dbprefix."bible_book";
$db['default']['BIBLE_CHAPTER'] = $dbprefix."bible_chapter";
$db['default']['BIBLE_VERSES'] = $dbprefix."bible_verses";
$db['default']['BIBLE_NOTE'] = $dbprefix."bible_note";
$db['default']['BIBLE_CAT'] = $dbprefix."bible_category";
$db['default']['BIBLE_HILITS'] = $dbprefix."bible_highlights";
$db['default']['BIBLE_HILITS_COLOR'] = $dbprefix."bible_highlights_color";
$db['default']['BIBLE_BOOKMARK'] = $dbprefix."bible_bookmark";
$db['default']['BIBLE_READING'] = $dbprefix."bible_reading";
$db['default']['BIBLE_READING_PLAN'] = $dbprefix."bible_reading_plan";

$db['default']['PRAYER_WALL_PHOTOS'] = $dbprefix."prayer_wall_photos";
$db['default']['PRAYER_GROUP'] = $dbprefix."prayer_group";
$db['default']['PRAYER_GROUP_POST'] = $dbprefix."prayer_group_post"; 
$db['default']['PRAYER_GROUP_MEMBERS'] = $dbprefix."prayer_group_members"; 
$db['default']['PRAYER_GROUP_NOTIFICATIONS'] = $dbprefix."prayer_group_notifications"; 

######## END HOLY PLACE #########

### bible fruit verse ####
$db['default']['BIBLE_FRUIT_VERSE'] = $dbprefix."bible_fruit_verse";
$db['default']['BIBLE_FRUIT'] = $dbprefix."bible_fruit";
$db['default']['FIVE_FRUITS_PER_USER'] = $dbprefix."five_fruits_per_user";
### bible fruit verse ####


######## MEDIA CENTER ########
$db['default']['WORD_FOR_TODAY'] = $dbprefix."word_for_today";
$db['default']['MC_VIDEO_CAT'] = $dbprefix."mc_video_cat";
$db['default']['MC_VIDEOS'] = $dbprefix."mc_videos";
$db['default']['mc_video_like'] = $dbprefix."mc_video_like";
$db['default']['mc_video_comments'] = $dbprefix."mc_video_comments";

$db['default']['CHRISTIAN_NEWS_CAT'] = $dbprefix."christian_news_cat";
$db['default']['CHRISTIAN_NEWS'] = $dbprefix."christian_news";
$db['default']['CHRISTIAN_NEWS_LIKE'] = $dbprefix."christian_news_like";
$db['default']['CHRISTIAN_NEWS_COMMENTS'] = $dbprefix."christian_news_comments";

$db['default']['MC_AUDIO_CAT'] = $dbprefix."mc_audio_cat";
$db['default']['MC_AUDIO'] = $dbprefix."mc_audio";
$db['default']['mc_audio_like'] = $dbprefix."mc_audio_like";
$db['default']['mc_audio_comments'] = $dbprefix."mc_audio_comments";

$db['default']['GOSPEL_MAGAZINE'] = $dbprefix."gospel_magazine";
$db['default']['GOSPEL_MAGAZINE_CMT'] = $dbprefix."gospel_magazine_comments";
$db['default']['GOSPEL_MAGAZINE_LIKE'] = $dbprefix."gospel_magazine_like";
$db['default']['CHRISTAN_PROJECT'] = $dbprefix."chirstan_project";
$db['default']['CHRISTAN_PROJECT_CMT'] = $dbprefix."christan_project_comments";
$db['default']['CHRISTAN_PROJECT_LIKE'] = $dbprefix."christan_project_like";



######## END MEDIA CENTER ########


#### new added country tables ####
$db['default']['COUNTRY'] = $dbprefix."country";
$db['default']['CITY'] = $dbprefix."city";
$db['default']['STATE'] = $dbprefix."state";
#### new added country tables ####



#### build kingdom tables #######
$db['default']['PROJECT'] = $dbprefix."project";
$db['default']['PROJECT_DONATION_HISTORY'] = $dbprefix."project_donation_history";

$db['default']['PROJECT_SKILL_REQUIRED'] = $dbprefix."project_skill_required";
$db['default']['PROJECT_SKILL_REQUEST'] = $dbprefix."project_skill_request";


$db['default']['SALVATION_PRAYER'] = $dbprefix."salvation_prayer";
$db['default']['SALVATION_PHOTO'] = $dbprefix."salvation_photo";
$db['default']['CHURCH'] = $dbprefix."church";
$db['default']['church_request'] = $dbprefix."church_request";
$db['default']['BIBLE_QUIZ'] = $dbprefix."bible_quiz";




### Donation basket #$##
$db['default']['COMMON_DONATION'] = $dbprefix."common_donation_basket_history";
### Donation basket #$##
#### build kingdom tables #######


####Country city state####
$db['default']['COUNTRY'] = $dbprefix."country";
$db['default']['STATE'] = $dbprefix."state";
$db['default']['CITY'] = $dbprefix."city";
####Country city state####

### IM chat
$db['default']['im_chat'] = $dbprefix."im_chat";



$db['flashchat']['CHAT_ROOM'] = "room";
### site settings
$db['default']['site_settings'] = $dbprefix."site_settings";
$db['default']['GRP_PRIVILEGE'] = $dbprefix."grp_privilege";

$db['default']['BO_MENU'] = $dbprefix."bo_menu";
### chat room categories:
$db['default']['room_cat'] = $dbprefix."room_cat";
$db['default']['chat_category'] = $dbprefix."chat_category";

### user chat room 
$db['default']['users_chat_room_invitation'] = $dbprefix."users_chat_room_invitation";
$db['default']['user_chat_rooms'] = $dbprefix."user_chat_rooms";


$db['default']['day_verse'] = $dbprefix."day_verse";
$db['default']['user_profile_rating'] = $dbprefix."user_profile_rating";
$db['default']['genre'] = $dbprefix."genre";
$db['default']['abuse_report'] = $dbprefix."abuse_report";

$db['default']['project_information'] = $dbprefix."project_information";

$db['default']['photoalbum_privacy'] = $dbprefix."photoalbum_privacy";
$db['default']['videolbum_privacy'] = $dbprefix."videolbum_privacy";
$db['default']['audioalbum_privacy'] = $dbprefix."audioalbum_privacy";
$db['default']['event_privacy'] = $dbprefix."event_privacy";
$db['default']['PRIVACY_SETTINGS'] = $dbprefix."privacy_settings";
$db['default']['photoalbum_privacy'] = $dbprefix."photoalbum_privacy";
$db['default']['videolbum_privacy'] = $dbprefix."videolbum_privacy";
$db['default']['audioalbum_privacy'] = $dbprefix."audioalbum_privacy";
$db['default']['event_privacy'] = $dbprefix."event_privacy";
$db['default']['event_invitation'] = $dbprefix."event_invitation";
$db['default']['contacts'] = $dbprefix."contacts";
$db['default']['prayer_group_invitation'] = $dbprefix."prayer_group_invitation";
$db['default']['ring_category_request'] = $dbprefix."ring_category_request";

$db['default']['skip_prayer_click'] = $dbprefix."skip_prayer_click";
$db['default']['user_word_like'] = $dbprefix."user_word_like";
$db['default']['user_word_unlike'] = $dbprefix."user_word_unlike";
$db['default']['user_word_comments'] = $dbprefix."user_word_comments";

$db['default']['wallpost_privacy'] = $dbprefix."wallpost_privacy";
$db['default']['wallcommentlike_privacy'] = $dbprefix."wallcommentlike_privacy";


## CHURCH RING TABLES ##
$db['default']['CHURCHRING'] = $dbprefix."church_ring";
$db['default']['CHURCHRING_INV_USER'] = $dbprefix."church_ring_invited_user";

/*$db['default']['USER_RING_POST'] = $dbprefix."user_ring_post";
$db['default']['USER_RING_POST_COMMENTS'] = $dbprefix."user_ring_post_comments";
$db['default']['USER_RING_POST_LIKE'] = $dbprefix."user_ring_post_like";
$db['default']['USER_RING_POST_UNLIKE'] = $dbprefix."user_ring_post_unlike";
$db['default']['ring_invitation']=$dbprefix."ring_invitation";*/
## CHURCH RING TABLES ##

/** church 10-02-2015 **/

$db['default']['CHURCH_PRAYER_GROUP_MEMBERS'] = $dbprefix."church_prayer_group_members";