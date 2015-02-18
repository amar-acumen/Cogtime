<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Exceptions extends CI_Exceptions{
    
	function __construct(){
		parent::__construct();
	}
	
	function show_404($page='', $log_error = true){
		# Your code here...
		
		# end with redirect to the custom 404 handler
		# add on the end the request-uri (might only work with apache)
		
		//print_r($GLOBALS);
				
		if( ENVIRONMENT == 'production' || ENVIRONMENT == 'testing' || ENVIRONMENT == 'development' ) {
			$this->config =& get_config();
			//print_r($this->config);
			$base_url = @$this->config['base_url'];
			$admin_base_url=@$this->config['admin_base_url'];
			
			if( isset($_SESSION['error_title']) && $_SESSION['error_title'] != '' ) {
				$error_title = $_SESSION['error_title'];
				unset($_SESSION['error_title']);
			}
			else {
				$error_title = 'Error 404';
			}
			
			if( isset($_SESSION['error_message']) && $_SESSION['error_message'] != '' ) {
				$error_message = $_SESSION['error_message'];
				unset($_SESSION['error_message']);
			}
			else {
				$error_message = '';
			}
	
			//$error_view = file_get_contents(APPPATH.'views/error/error.phtml');
			//echo $error_view;
	// 		include(BASEPATH.'libraries/Loader.php');
	// 		CI_Loader::helper('common_helper');

			//$TOP_MENU_SELECTED = 0;
			//$not_display_loggedin = true;
			$error_page = true;

			if (!function_exists('t')) {
				function t($str) {
					return $str;
				}
			}

			if (!function_exists('base_url')) {
				function base_url() {
					$config =& get_config();
					//print_r($this->config);
					$base_url = $config['base_url'];
					return $base_url;
				}
			}
			
			if (!function_exists('admin_base_url')) {
				function admin_base_url() {
					$config =& get_config();
					//print_r($this->config);
					$admin_base_url = $config['admin_base_url'];
					return $admin_base_url;
				}
			}
			
			
			if (!function_exists('my_current_url')) {
				function my_current_url() {
					$protocol = ( isset($_ENVIRONMENT['HTTPS']) && $_ENVIRONMENT['HTTPS'] == 'on') ? 'https' : 'http';
					$url = $protocol.'://'.$_ENVIRONMENT['HTTP_HOST'].$_ENVIRONMENT['REQUEST_URI'];
					return $url;
				}
			}
			
			ob_start();
			$error_view = include (APPPATH.'views/error/error.phtml');
			$error_view = ob_get_contents();
			ob_end_clean();
	
// 			$body = $this->sprintf3( $error_view, array('error_title'=>$error_title,
// 							'error_message'=>$error_message,
// 							'base_url'=>$base_url) );
			header("HTTP/1.1 404 Not Found");
			echo $error_view;
			//echo 'aaaaaaaaaaaaaaaaaa';
			
	// 		header("location: ".$base_url.'error.html');
			exit;
		}
		else {
			parent::show_404($page);
		}
	}

	/**
	 * Native PHP error handler
	 *
	 * @access	private
	 * @param	string	the error severity
	 * @param	string	the error string
	 * @param	string	the error filepath
	 * @param	string	the error line number
	 * @return	string
	 */
	function show_error($heading, $message, $template = 'error_general', $status_code = 500)
	{	
		if( ENVIRONMENT =='production' ) {
			$this->config =& get_config();
			$base_url = $this->config['base_url'];
			
			$error_title = 'Server Error';
			$error_message = 'A server error  is produced on site cogtime.com , Sorry for the inconvenience.';
	
			$error_view = file_get_contents(APPPATH.'views/error/error.phtml');
			//echo $error_view;
	// 		include(BASEPATH.'libraries/Loader.php');
	// 		CI_Loader::helper('common_helper');
	
			$body = $this->sprintf3( $error_view, array('error_title'=>$error_title,
							'error_message'=>$error_message,
							'base_url'=>$base_url) );
			header("HTTP/1.1 404 Not Found");
			echo $body;
			//echo 'aaaaaaaaaaaaaaaaaa';
			
	// 		header("location: ".$base_url.'error.html');
			exit;
		}
		else {
			//echo 'aa'.$status_code;
			echo parent::show_error($heading, $message, $template, $status_code);
		}
	}

	function sprintf3($str, $vars, $char = '%%') {
		$tmp = array();
		foreach($vars as $k => $v) {
			$tmp[$char . $k . $char] = $v;
		}
		return str_replace(array_keys($tmp), array_values($tmp), $str);
	}

// 	function show_php_error($severity, $message, $filepath, $line) {
// 		$this->config =& get_config();
// 		$base_url = $this->config['base_url'];
// 		header("location: ".$base_url.'logued/error.html');
// 		exit;
// 		echo '<br>php error';
// 	}
} 