<?php

/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     testing
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 *
 */
	define('ENVIRONMENT', 'development');
	define('BOOL_SEND_MAIL', true);
	define('SERVER', 'localhost');
/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but testing and live will hide them.
 */

if (defined('ENVIRONMENT'))
{
	switch (ENVIRONMENT)
	{
		case 'development':
			ini_set('display_errors', true);
			error_reporting(E_ALL);
			error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
			define('SQL_LOGGING', true);
			break;

		case 'testing':
			ini_set('display_errors', true);
			error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
			define('SQL_LOGGING', true);
			break;

		case 'production':
			ini_set('display_errors', false);
			error_reporting(0);
			define('SQL_LOGGING', false);
			break;

		default:
			exit('The application environment is not set correctly.');
	}
}


/* **************************************** Setting session timeout *************************************** */
// path for cookies
$cookie_path = "/";

// timeout value for the cookie
$cookie_timeout = 2629743; // 1 month in seconds

// timeout value for the garbage collector
//   we add 300 seconds, just in case the user's computer clock
//   was synchronized meanwhile; 600 secs (10 minutes) should be
//   enough - just to ensure there is session data until the
//   cookie expires
$garbage_timeout = $cookie_timeout + 600; // in seconds

// set the PHP session id (PHPSESSID) cookie to a custom value
// set a lower cookie timeout
// This line will save the session cookie even if browser is closed.
//session_set_cookie_params($cookie_timeout, $cookie_path);

// set the garbage collector - who will clean the session files -
//   to our custom timeout
ini_set('session.gc_maxlifetime', $garbage_timeout);

// we need a distinct directory for the session files,
//   otherwise another garbage collector with a lower gc_maxlifetime
//   will clean our files aswell - but in an own directory, we only
//   clean sessions with our "own" garbage collector (which has a
//   custom timeout/maxlifetime set each time one of our scripts is
//   executed)
$sep = "/";

//$sessdir = ini_get('session.save_path').$sep."cogtime_session";
//if (!is_dir($sessdir)) { mkdir($sessdir, 0777); }
//ini_set('session.save_path', $sessdir);



/* ********************************************************************************************************* */



/*
 *---------------------------------------------------------------
 * SYSTEM FOLDER NAME
 *---------------------------------------------------------------
 *
 * This variable must contain the name of your "system" folder.
 * Include the path if the folder is not in the same  directory
 * as this file.
 *
 */
	$system_path = 'system';

/*
 *---------------------------------------------------------------
 * APPLICATION FOLDER NAME
 *---------------------------------------------------------------
 *
 * If you want this front controller to use a different "application"
 * folder then the default one you can set its name here. The folder
 * can also be renamed or relocated anywhere on your server.  If
 * you do, use a full server path. For more info please see the user guide:
 * http://codeigniter.com/user_guide/general/managing_apps.html
 *
 * NO TRAILING SLASH!
 *
 */
	$application_folder = 'application';

/*
 * --------------------------------------------------------------------
 * DEFAULT CONTROLLER
 * --------------------------------------------------------------------
 *
 * Normally you will set your default controller in the routes.php file.
 * You can, however, force a custom routing by hard-coding a
 * specific controller class/function here.  For most applications, you
 * WILL NOT set your routing here, but it's an option for those
 * special instances where you might want to override the standard
 * routing in a specific front controller that shares a common CI installation.
 *
 * IMPORTANT:  If you set the routing here, NO OTHER controller will be
 * callable. In essence, this preference limits your application to ONE
 * specific controller.  Leave the function name blank if you need
 * to call functions dynamically via the URI.
 *
 * Un-comment the $routing array below to use this feature
 *
 */
	// The directory name, relative to the "controllers" folder.  Leave blank
	// if your controller is not in a sub-folder within the "controllers" folder
	// $routing['directory'] = '';

	// The controller class file name.  Example:  Mycontroller
	// $routing['controller'] = '';

	// The controller function you wish to be called.
	// $routing['function']	= '';


/*
 * -------------------------------------------------------------------
 *  CUSTOM CONFIG VALUES
 * -------------------------------------------------------------------
 *
 * The $assign_to_config array below will be passed dynamically to the
 * config class when initialized. This allows you to set custom config
 * items or override any default config values found in the config.php file.
 * This can be handy as it permits you to share one application between
 * multiple front controller files, with each file containing different
 * config values.
 *
 * Un-comment the $assign_to_config array below to use this feature
 *
 */
	// $assign_to_config['name_of_config_item'] = 'value of config item';



// --------------------------------------------------------------------
// END OF USER CONFIGURABLE SETTINGS.  DO NOT EDIT BELOW THIS LINE
// --------------------------------------------------------------------

/*
 * ---------------------------------------------------------------
 *  Resolve the system path for increased reliability
 * ---------------------------------------------------------------
 */

	// Set the current directory correctly for CLI requests
	if (defined('STDIN'))
	{
		chdir(dirname(__FILE__));
	}

	if (realpath($system_path) !== FALSE)
	{
		$system_path = realpath($system_path).'/';
	}

	// ensure there's a trailing slash
	$system_path = rtrim($system_path, '/').'/';

	// Is the system path correct?
	if ( ! is_dir($system_path))
	{
		exit("Your system folder path does not appear to be set correctly. Please open the following file and correct this: ".pathinfo(__FILE__, PATHINFO_BASENAME));
	}

/*
 * -------------------------------------------------------------------
 *  Now that we know the path, set the main path constants
 * -------------------------------------------------------------------
 */
	// The name of THIS file
	define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

	// The PHP file extension
	// this global constant is deprecated.
	define('EXT', '.php');

	// Path to the system folder
	define('BASEPATH', str_replace("\\", "/", $system_path));

	// Path to the front controller (this file)
	define('FCPATH', str_replace(SELF, '', __FILE__));

	// Name of the "system folder"
	define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));


	// The path to the "application" folder
	if (is_dir($application_folder))
	{
		define('APPPATH', $application_folder.'/');
	}
	else
	{
		if ( ! is_dir(BASEPATH.$application_folder.'/'))
		{
			exit("Your application folder path does not appear to be set correctly. Please open the following file and correct this: ".SELF);
		}

		define('APPPATH', BASEPATH.$application_folder.'/');
	}

/*******Interface Path Defined**********/
define('INFPATH', APPPATH.'interfaces/');

/*
 * --------------------------------------------------------------------
 * LOAD THE BOOTSTRAP FILE
 * --------------------------------------------------------------------
 *
 * And away we go...
 *
 */
require_once BASEPATH.'core/CodeIgniter.php';

/* End of file index.php */
/* Location: ./index.php */


/*******
* To Load the MY_Controller or MY_Model class autometically.
* Or any class if not found will searches and included using this
* function.
* 
* @param string strtolower($s_class_name), class name
*/
function __autoload($s_class_name)
{
    try
    {   
        
        if(file_exists(APPPATH."controllers/".strtolower($s_class_name) . '.php'))
        {
            require_once APPPATH."controllers/".strtolower($s_class_name) . '.php';
        }  
        elseif(file_exists(APPPATH."models/". strtolower($s_class_name) .".php"))
        {
            require_once APPPATH."models/". strtolower($s_class_name) .".php";     
        }
        elseif(file_exists(APPPATH."controllers/admin/".strtolower($s_class_name) . '.php'))
        {
            require_once APPPATH."controllers/admin/".strtolower($s_class_name) . '.php';
        }
        elseif(file_exists(APPPATH."controllers/fe/".strtolower($s_class_name) . '.php'))
        {
            require_once APPPATH."controllers/fe/".strtolower($s_class_name) . '.php';
        }        
        
    }
    catch(Exception $err_obj)
    {
      show_error($err_obj->getMessage());
    }         
    
}


///end __autoload


/* End of file index.php */
/* Location: ./index.php */