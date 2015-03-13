<?php
/*********
* Author: 
* Date  : 
* Modified By: 
* Modified Date:
* 
* Purpose:
* Controller For WALL
* 
* 
*/
include(APPPATH.'controllers/base_controller.php');

class prayer_partner_public_profile extends Base_controller
{
    private $pagination_per_page = 10;
	private $comments_pagination_per_page = 10;
	private $people_liked_pagination_per_page = 10;
    
    
    public function __construct()
     {
        try
        {
            parent::__construct();
			parent::check_login(TRUE, '', array('1'));//put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...
            $this->load->helper('wall_helper');
            $this->load->model('users_model');
			$this->load->model('netpals_model');
			$this->load->model('my_prayer_partner_model');
			//$this->load->model('data_newsfeed_model');
			$this->load->model('newsfeed_comments_model');
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }
	
	
	# index function definition...
    public function index($id='') 
    {
        try
        { 
			$posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array(/*'js/ddsmoothmenu.js',
										'js/switch.js','js/animate-collapse.js',
										'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
										'js/stepcarousel.js',*/
										'js/production/my_friends.js',
										'js/production/my_net_pals.js',
										'js/production/my_prayer_partner.js',
										'js/production/my_message.js',
										//'js/tab.js',
										
                                        ));
                                        
           // parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css') );
            # adjusting header & footer sections [End]...
			$data['page_view_type'] = 'public_account';
			
			$i_profile_id =  intval($id);
			$profile_info = $this->users_model->fetch_this($i_profile_id);
			$data['profile_info'] = $profile_info;
			
			## fetching logged user info to show in left bar ##
			$logged_user_id = intval(decrypt($this->session->userdata('user_id')));
			$arr_profile_info = $this->users_model->fetch_this($logged_user_id);
			$data['arr_profile_info'] = $arr_profile_info;
			
			## note: if its a prayer partner profile then he/she is not friend nor netpals  
			#so check only friend/ netpals request sent or not.##
			
			  $is_friend_req_alrdy_snt = $this->users_model->friend_request_already_sent($logged_user_id,$i_profile_id);
			  #pr($is_friend_req_alrdy_snt);
			  if($is_friend_req_alrdy_snt){
				  $data['display_becomefriend']     ='false';
				  $data['if_already_friend']     ='';
			  }else{
				  $data['if_already_friend']     ='false';
			  }
			
			  $is_netpals_req_alrdy_snt = $this->netpals_model->netpals_request_already_sent($logged_user_id,$i_profile_id);
			  #pr($is_friend_req_alrdy_snt);
			  if($is_netpals_req_alrdy_snt){
				  $data['display_becomenetpals']     ='false';
				  $data['if_already_netpals']     ='';
			  }else{
				  $data['if_already_netpals']     ='false';
			  }
			  
			  ### CHECK ALREADY PRAYER PARTNERS. ##
			  
			  $get_friend_req_sent_status_me_him = $this->my_prayer_partner_model->get_prayer_partner_status_me_him($logged_user_id , $i_profile_id);
		
				if(count($get_friend_req_sent_status_me_him) > 0  ) { 
					 $data['prayer_partner']['display_becomeprayer_partner']     ='false';
				 }
						
			   
			   $get_friend_status_me_him = $this->my_prayer_partner_model->get_prayer_partner_accepted_me_him(
													$logged_user_id , $i_profile_id);
				if(count($get_friend_status_me_him) > 0  ) { 
					 $data['prayer_partner']['display_alreadyprayer_partner']     ='true';
				 }
						
			   $total_PP_arr = $this->my_prayer_partner_model->get_prayerPartnerId_by_user_id($i_profile_id);
			   $total_PP = count($total_PP_arr);
			   $total_pending_PP_req = $this->my_prayer_partner_model->total_pending_prayer_partner_recieved($i_profile_id);
				## CHECKING TOTAL PRAYER PARTNERS ##
					if($total_PP == 3 || $total_pending_PP_req >= 5){
						 $data['prayer_partner']['is_available']     ='false';
						
					}else{
						$data['prayer_partner']['is_available']     ='true';					 
					}
			   ## CHECKING TOTAL PRAYER PARTNERS ##
			  
			  
			
            # view file...
			$VIEW_PG_FILE = "logged/prayer-partner-profile.phtml";
            $VIEW = "{$VIEW_PG_FILE}";
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           show_error($err_obj->getMessage());
        } 

    }
    
      
    
}   // end of controller...

