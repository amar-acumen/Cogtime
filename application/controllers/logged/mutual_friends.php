<?php
/*********
* Author: 
* Date  : 
* Modified By: 
* Modified Date:
* 
*/
include(APPPATH.'controllers/base_controller.php');


class Mutual_friends extends Base_controller
{
    
   // private $pagination_per_page= 10;
	//private $friends_pagination_per_page= 10;
	
    public function __construct()
     {
        try
        {
            parent::__construct();
           	parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
			# loading reqired model & helpers...
            $this->load->model('users_model');
			$this->load->model('contacts_model');
			$this->load->model('user_notifications_model');
			$this->load->model('user_alert_model');
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    
    public function index() 
    {
        try
        {
			
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
           	//$this->data["MAIN_MENU_SELECTED"] = 1;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
		
			
            parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
										'js/switch.js','js/animate-collapse.js',
										'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
										'js/stepcarousel.js',
										'js/jquery.form.js',
                                		'js/jquery/JSON/json2.js'
										//'js/frontend/logged/delete_friend.js'
										//'js/frontend/logged/my_friends.js',
										//'js/frontend/logged/tweets/tweet_utilities.js',
										));
										
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
			  							'css/dd.css') );
           // $data['mutual']=$this->contacts_model->get_mutual_friends_by_user(intval(decrypt($this->session->userdata('user_id'))));
			$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
			$data['page_view_type'] = 'myaccount';
			$this->load->model('users_model');
			$this->load->model('contacts_model');
			//$arr_profile_info = $this->users_model->fetch_this($i_profile_id);
			//$data['arr_profile_info'] = $arr_profile_info;
			
			//$this->session->set_userdata('search_condition','');
			ob_start();
            $this->friends_ajax_pagination1();
            $data['friends_result_content'] = ob_get_contents();  //pr($data['friends_result_content'],1);
            ob_end_clean();
			
			
			### showing Friend requset recived ##
			# view file...
            $VIEW = "logged/friends/mutual_friends.phtml"; 
            parent::_render($data, $VIEW);
		}
        catch(Exception $err_obj)
        {
           
        } 

    }   
	
	
	public function friends_ajax_pagination1($page=0)
    {
        try
        {	 
			  $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
			  
			
			 
				  $result = $this->contacts_model->get_mutual_friends_by_user($i_profile_id);
				  //pr($result,1);
				  $resultCount = count($result);
				  
			  $this->load->library('jquery_pagination');
			  $config['base_url'] = base_url()."logged/mutual_friends/friends_ajax_pagination";
			 // $config['total_rows'] = $total_rows;
			  $config['per_page'] = $this->friends_pagination_per_page;
			  $config['uri_segment'] = 4;
			  $config['num_links'] = 9;
			  $config['page_query_string'] = false;
			  $config['prev_link'] = '&laquo; Previous';
			  $config['next_link'] = 'Next &raquo;';
  
			  $config['cur_tag_open'] = '<li> <span><a href="javascript:void(0)" class="select">';
			  $config['cur_tag_close'] = '</a></span></li>';
  
			  $config['next_tag_open'] = '<li><a href="javascript:void(0)">';
			  $config['next_tag_close'] = '</a></li>';
  
			  $config['prev_tag_open'] = '<li><a href="javascript:void(0)">';
			  $config['prev_tag_close'] = '</a></li>';
  
			  $config['num_tag_open'] = '<li>';
			  $config['num_tag_close'] = '</li>';
  
			  $config['div'] = '#my_mutual_friends'; /* Here #content is the CSS selector for target DIV */
			  $config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
			  $config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */
  
			  $this->jquery_pagination->initialize($config);
			  $data['page_links'] = $this->jquery_pagination->create_links();
  
			  // getting   listing...
			  $data['result_arr'] = $result;
			  //pr( $data['result_arr'],1);
			 // $data['no_of_result'] = $total_rows;
			  $data['current_page'] = $page;
			  //$data['total_pages'] = ceil($total_rows/$this->friends_pagination_per_page);		
			  // $p = ($page/$this->friends_pagination_per_page);
			  // $data['current_loaded_page_no'] =  $p + 1;
              // $data['is_post_'] = $this->session->userdata('is_post_') ;
				# loading the view-part...
         	 	echo  $this->load->view('logged/friends/mutual_friends_ajax.phtml', $data,TRUE);
		 }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
    }
	
	
	
}   // end of controller...

