<?php
include(APPPATH.'controllers/base_controller.php');

class Parse extends Base_controller 
 {
	function __construct() 
	{
		
		
		parent::__construct();

		$this->load->helper('common_helper');
	}

	function index() {
		echo '';
	}

	function js($js_file='') {
	
	 
		if($js_file == '') {
			echo '';
			return;
		}
		$js_file = base64_decode($js_file);
		
		header('Content-type: application/javascript');
			
		if( file_exists(APPPATH.'views/js/'.$js_file) ) 
		{
            /* this is added to get some cache benefits */
			$mtime = filemtime(APPPATH.'views/js/'.$js_file);
			//header("last-modified: " . gmdate("D, d M Y H:i:s", $mtime) . " GMT");
			header("last-modified: " . gmdate("D, d M Y H:i:s", $mtime) . " GMT");
			$this->load->view( "js/".$js_file );
		}
		else {
			header("last-modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			echo '';
		}
	}

	function css($css_file='') {
		if($css_file == '') {
			echo '';
			return;
		}
		$css_file = base64_decode($css_file);
		
		header('Content-type: text/css; charset=utf-8');

		if( file_exists(BASEPATH.'application/views/css/'.$css_file) ) {

			/* this is added to get some cache benefits */
			$mtime = filemtime(BASEPATH.'application/views/css/'.$css_file);
			header("last-modified: " . gmdate("D, d M Y H:i:s", $mtime) . " GMT");
			
			$this->load->view( "css/".$css_file );
		}
		else {
			header("last-modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			echo '';
		}
	}
}


