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

$active_group = 'default';
$active_record = TRUE;

$db['default']['hostname'] = 'localhost';
$db['default']['username'] = 'acumencs_roshni';
$db['default']['password'] = ';wMCAkGIZPRB';
$db['default']['database'] = 'acumencs_cogtime';
$db['default']['dbdriver'] = 'mysql';
$db['default']['dbprefix'] = 'cg_';
$db['default']['pconnect'] = TRUE;
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
$db['default']['USER_BLOGS'] = $dbprefix."user_blogs";
$db['default']['USER_PHOTOS'] = $dbprefix."user_photos";
$db['default']['PHOTO_ALBUM'] = $dbprefix."photo_album";
$db['default']['USER_VIDEOS'] = $dbprefix."user_videos";
$db['default']['VIDEO_ALBUM'] = $dbprefix."video_album";


### USER'S WALL POSTS AND COMMENTS ###
$db['default']['USER_NEWSFEEDS'] = $dbprefix."user_newsfeeds";
$db['default']['USER_NEWSFEED_COMMENTS'] = $dbprefix."user_newsfeed_comments";
$db['default']['USER_NEWSFEED_LIKE'] = $dbprefix."user_newsfeed_like";
$db['default']['USER_NEWSFEED_UNLIKE'] = $dbprefix."users_newsfeed_unlike";

### END OF USER'S WALL POSTS AND COMMENTS ###

## message #####
$db['default']['MESSAGES'] = $dbprefix."messages";
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
$db['default']['EVENTS_RSVP'] = $dbprefix."event_rsvp";
$db['default']['EVENTS_USER_INVITED'] = $dbprefix."event_user_invited";
## END EVENTS ##

### NEWS TABLES ####

$db['default']['CHRISTIAN_HEADLINE'] = $dbprefix."christian_headline";


# notification table
$db['default']['NOTIFICATIONS'] = $dbprefix."notifications";
$db['default']['USER_ALERTS'] = $dbprefix."user_alerts";



####### END OF NEWS TABLES ###3

### ADMIN TABLES #####

$db['default']['ADMIN_USER'] = $dbprefix."admin_user";
$db['default']['ADMIN_USER_GRP'] = $dbprefix."admin_user_grp";
$db['default']['ADMIN_GRP_PRIVILEGE'] = $dbprefix."admin_grp_privilege";
$db['default']['ABUSE_DICTIONARY'] = $dbprefix."abuse_dictionary";


##### END ADMIN TABLES ####



