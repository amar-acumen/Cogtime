<?php
/*********
* Author: 
* Date  : 
* Modified By: 
* Modified Date:
* 
*/
include(APPPATH.'controllers/base_controller.php');


class My_friends extends Base_controller
{
    
    private $pagination_per_page= 10;
	private $friends_pagination_per_page= 10;
	
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
           	$this->data["MAIN_MENU_SELECTED"] = 1;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
		
			
            parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
										'js/switch.js','js/animate-collapse.js',
										'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
										'js/stepcarousel.js',
										'js/jquery.form.js',
                                		'js/jquery/JSON/json2.js',
										'js/frontend/logged/delete_friend.js'
										,'js/frontend/logged/my_friends.js',
										'js/frontend/logged/tweets/tweet_utilities.js',
										));
										
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
			  							'css/dd.css') );
            
			$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
			$data['page_view_type'] = 'myaccount';
			$this->load->model('users_model');
			$arr_profile_info = $this->users_model->fetch_this($i_profile_id);
			$data['arr_profile_info'] = $arr_profile_info;
			
			$this->session->set_userdata('search_condition','');
			ob_start();
            $this->friends_ajax_pagination();
            $data['friends_result_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
			
			
			### showing Friend request sent ###
			$data['page_name'] = 'my_friends';
			ob_start();
			$this->my_friend_request_recieved_ajax_pagination();
		    $data['friend_request_recived'] = ob_get_contents();
			ob_end_clean();	
			
			### showing Friend requset recived ##
			# view file...
            $VIEW = "logged/friends/my_friends.phtml"; 
            parent::_render($data, $VIEW);
		}
        catch(Exception $err_obj)
        {
           
        } 

    }   
	
	
	public function friends_ajax_pagination($page=0)
    {
        try
        {	  $add_where = '';
			  #$show = '-1';
			 /* $this->session->set_userdata('is_post_','');
			  $this->session->set_userdata('search_condition','');*/
			  $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
			  
			  ### search condition ###
			 if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) {
								 
				$s_name = get_formatted_string(trim($this->input->post('txt_name')));
				if($WHERE_COND != '')
					$WHERE_COND .= ($s_name=='')?'':" OR (u.s_first_name LIKE '".$s_name."%'  OR u.s_last_name LIKE '".$s_name."%'  )";
			    else
					$WHERE_COND .= ($s_name=='')?'':"  (u.s_first_name LIKE '".$s_name."%' OR u.s_last_name LIKE '".$s_name."%' )";
					
				
				$show = get_formatted_string(trim($this->input->post('show')));
				if($WHERE_COND != ''){
					$WHERE_COND .= ($show == '1')?" AND (uon.s_status = '".$show."' )":'';
					$WHERE_COND .= ($show == '4')?" AND (uon.s_status IS NULL)" :''; 
					$WHERE_COND .= ($show =='-1')? "":"";
				}
			    else
					{ $WHERE_COND .= ($show == '1')? "  (uon.s_status = '".$show."')":'';
					  $WHERE_COND .= ($show == '4')?" (uon.s_status IS NULL)":''; 
					  $WHERE_COND .= ($show =='-1')? "":"";
					}
					
				 
				$this->session->set_userdata('search_condition',$WHERE_COND);
				$this->session->set_userdata('is_post_','1');
			
			
			 }
		
			#### end search condition ###
			  
			  
			  
			  ### showing Friend request sent ###
			  $add_where = $this->session->userdata('search_condition');
			  if($add_where != ''){
				  $add_where = " AND (".$add_where.")";
			  }
		   
		
			  {  
				 $WHERE = " WHERE 
						1
						AND c.s_status = 'accepted' 
						AND u.i_status=1 
						AND
						((c.i_requester_id = '".$i_profile_id."' AND u.id=c.i_accepter_id ) 
						OR (c.i_accepter_id = '".$i_profile_id."' AND u.id=c.i_requester_id )) ".$add_where ." GROUP BY u.id "	;	
			  
			      $ORDER_BY = "u.id DESC";	
				  
				  $total_where =  " WHERE 
									1
									AND c.s_status = 'accepted' 
									AND u.i_status=1 
									AND
									((c.i_requester_id = '".$i_profile_id."' AND u.id=c.i_accepter_id ) 
									OR (c.i_accepter_id = '".$i_profile_id."' AND u.id=c.i_requester_id )) ".$add_where." GROUP BY u.id " ;
			 
				  $result = $this->contacts_model->fetch_multi_online_friends($WHERE, intval($page),
				  								   $this->friends_pagination_per_page,$ORDER_BY);
				  $resultCount = count($result);
				  
				  $total_rows = $this->contacts_model->gettotal_online_friends($total_where);
				  
					if( ( !is_array($result) || !count($result) ) && $total_rows ) {
						$page = $page - $this->friends_pagination_per_page;
						
						$result = $this->contacts_model->fetch_multi_online_friends($WHERE, intval($page),
														 $this->friends_pagination_per_page,$ORDER_BY);
					}
				
			  }
			 
			  ## end seacrh conditions : filter ############
			  
			  #pr($result);
			  #Jquery Pagination Starts
			  $this->load->library('jquery_pagination');
			  $config['base_url'] = base_url()."logged/my_friends/friends_ajax_pagination";
			  $config['total_rows'] = $total_rows;
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
  
			  $config['div'] = '#my_friends'; /* Here #content is the CSS selector for target DIV */
			  $config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
			  $config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */
  
			  $this->jquery_pagination->initialize($config);
			  $data['page_links'] = $this->jquery_pagination->create_links();
  
			  // getting   listing...
			  $data['result_arr'] = $result;
			  $data['no_of_result'] = $total_rows;
			  $data['current_page'] = $page;
			  $data['total_pages'] = ceil($total_rows/$this->friends_pagination_per_page);
			  
			
			   $p = ($page/$this->friends_pagination_per_page);
			   $data['current_loaded_page_no'] =  $p + 1;
               $data['is_post_'] = $this->session->userdata('is_post_') ;
				# loading the view-part...
         	 	echo  $this->load->view('logged/friends/my_friends_ajax.phtml', $data,TRUE);
		 }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
    }
	
	
	public function my_friend_request_recieved_ajax_pagination($page=0)
    {
        try
        { 
			$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
			### showing Friend request sent ###
			$WHERE = " WHERE 
						1
						AND i_deleted_by = 1
						AND c.s_status = 'pending' 
						AND u.i_status=1 
						AND
						((c.i_accepter_id = '".$i_profile_id."' AND u.id=c.i_requester_id ) ) GROUP BY u.id"	;	
			/*AND u.i_country_id=cn.id*/
			$ORDER_BY = "c.id DESC";			
		   
				$result = $this->contacts_model->fetch_multi($WHERE, null, null,$ORDER_BY);
				$resultCount = count($result);
				#echo $this->db->last_query(); 
				//pr($result);
				$total_rows = $this->contacts_model->gettotal_info($WHERE); 
				
			
            $data['result_arr_friend'] = $result;
			
			# loading the view-part...
          echo  $this->load->view('logged/friends/my_friend_request_received_ajax.phtml', $data,TRUE);
		 }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
    }
	
	
	public function friend_request()
	{
		$posted=array();
		$this->data["posted"]=$posted;/*don't change*/    
		$data = $this->data;      
		$this->data["MAIN_MENU_SELECTED"] = 1;
		parent::_set_title('::: COGTIME Xtian network :::');
		parent::_set_meta_desc('');
		parent::_set_meta_keywords('');
	
		
		parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
									'js/switch.js','js/animate-collapse.js',
									'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
									'js/stepcarousel.js',
									'js/frontend/logged/my_friends.js',
									'js/frontend/logged/tweets/tweet_utilities.js',
									));
									
		parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
									'css/dd.css') );
		
		$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
		$arr_profile_info = $this->users_model->fetch_this($i_profile_id);
		#pr($arr_profile_info);
		
		
		### showing Friend request sent ###
		
		ob_start();
		$this->friend_request_sent_ajax_pagination();
		$data['content1'] = ob_get_contents();
		ob_end_clean(); 
					
		#pr($friend_requset_sent_);
		
		### showing Friend request sent ###
		ob_start();
		$this->friend_request_recieved_ajax_pagination();
		$data['content2'] = ob_get_contents();
		ob_end_clean();	
		
		### showing Friend requset recived ##
		
		
		
		### end showing Friend requset recived ##
		#pr($friend_requset_recieved);
		
		# view file...
		$VIEW = "logged/friends/friend_request.phtml"; 
		parent::_render($data, $VIEW);
	}
	
	
	public function friend_request_sent_ajax_pagination($page=0)
    {
        try
        {
			$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
			### showing Friend request sent ###
			$WHERE = " WHERE 
							1
							AND i_deleted_by = 1
							AND c.s_status = 'pending' 
							AND u.i_status=1 
							AND
							((c.i_requester_id = '".$i_profile_id."' AND u.id=c.i_accepter_id ) ) GROUP BY u.id"	;	
			/*AND u.i_country_id=cn.id*/
			$ORDER_BY = "c.id DESC";		
		   
				 
				$result = $this->contacts_model->fetch_multi($WHERE, intval($page), $this->pagination_per_page,$ORDER_BY);
				#echo $resultCount = count($result);
				//echo $this->db->last_query(); 
				#($result);
				$total_rows = $this->contacts_model->gettotal_info($WHERE);
				
				if( ( !is_array($result) || !count($result) ) && $total_rows ) {
					$page = $page - $this->pagination_per_page;
					
					$result = $this->contacts_model->fetch_multi($WHERE, intval($page), $this->pagination_per_page,$ORDER_BY);
				}
				## end seacrh conditions : filter ############
				
				#pr($result,1);
				#Jquery Pagination Starts
				$this->load->library('jquery_pagination');
				$config['base_url'] = base_url()."logged/my_friends/friend_request_sent_ajax_pagination";
				$config['total_rows'] = $total_rows;
				$config['per_page'] = $this->pagination_per_page;
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
	
				$config['div'] = '#request_sent_content'; /* Here #content is the CSS selector for target DIV */
				$config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
				$config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */
	
				$this->jquery_pagination->initialize($config);
				$data['page_links'] = $this->jquery_pagination->create_links();
	
				// getting   listing...
				$data['result_arr'] = $result;
				$data['no_of_result'] = $total_rows;
				$data['current_page'] = $page;
				$data['total_pages'] = ceil($total_rows/$this->pagination_per_page);
				
				//echo $data['total_pages'].' ==total_pages==== '.$page;
				//echo $data['current_page'].' ==  ';
				 $p = ($page/$this->pagination_per_page);
				 $data['current_loaded_page_no'] =  $p + 1;
            
			# loading the view-part...
          echo  $this->load->view('logged/friends/friend_request_sent_ajax.phtml', $data,TRUE);
		 }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
    }
	
	
	public function friend_request_recieved_ajax_pagination($page=0)
    {
        try
        { 
			$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
			### showing Friend request sent ###
			$WHERE = " WHERE 
						1
						AND i_deleted_by = 1
						AND c.s_status = 'pending' 
						AND u.i_status=1 
						AND
						((c.i_accepter_id = '".$i_profile_id."' AND u.id=c.i_requester_id ) ) GROUP BY u.id"	;	
			/*AND u.i_country_id=cn.id*/
			$ORDER_BY = "c.id DESC";			
		   
				$result = $this->contacts_model->fetch_multi($WHERE, intval($page), $this->pagination_per_page,$ORDER_BY);
				$resultCount = count($result);
				#echo $this->db->last_query(); 
				#pr($result);
				$total_rows = $this->contacts_model->gettotal_info($WHERE); 
				
				if( ( !is_array($result) || !count($result) ) && $total_rows ) {
					$page = $page - $this->pagination_per_page;
					
					$result = $this->contacts_model->fetch_multi($WHERE, intval($page), $this->pagination_per_page,$ORDER_BY);
				}
				## end seacrh conditions : filter ############
				
				#pr($result,1);
				#Jquery Pagination Starts
				$this->load->library('jquery_pagination');
				$config['base_url'] = base_url()."logged/my_friends/friend_request_recieved_ajax_pagination";
				$config['total_rows'] = $total_rows;
				$config['per_page'] = $this->pagination_per_page;
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
	
				$config['div'] = '#request_recieved_content'; /* Here #content is the CSS selector for target DIV */
				$config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
				$config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */
	
				$this->jquery_pagination->initialize($config);
				$data['page_links'] = $this->jquery_pagination->create_links();
	
				// getting   listing...
				$data['result_arr'] = $result;
				$data['no_of_result'] = $total_rows;
				$data['current_page'] = $page;
				$data['total_pages'] = ceil($total_rows/$this->pagination_per_page);
				
				//echo $data['total_pages'].' ==total_pages==== '.$page;
				//echo $data['current_page'].' ==  ';
				 $p = ($page/$this->pagination_per_page);
				 $data['current_loaded_page_no'] =  $p + 1;
			
            
			# loading the view-part...
          echo  $this->load->view('logged/friends/friend_request_received_ajax.phtml', $data,TRUE);
		 }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
    }
	
	
	
	public function search_invite_friends()
	{
		$posted=array();
		$this->data["posted"]=$posted;/*don't change*/    
		$data = $this->data;      
		$this->data["MAIN_MENU_SELECTED"] = 1;
		parent::_set_title('::: COGTIME Xtian network :::');
		parent::_set_meta_desc('');
		parent::_set_meta_keywords('');
	
		
		parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
									'js/switch.js','js/animate-collapse.js',
									'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
									'js/stepcarousel.js',
									'js/frontend/logged/my_friends.js',
									'js/frontend/logged/tweets/tweet_utilities.js',
									'js/autocomplete/jquery.autocomplete.js'
									));
									
		parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
									'css/dd.css',
									'css/jquery.autocomplete.css') );
		
		$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        
        
        $this->session->set_userdata('is_preserve_search', false);
        $this->session->set_userdata('search_condition',''); 
        $this->session->set_userdata('is_post_','');
		#$data['search_'] = '';
		
		## seacrh conditions : filter ############
		
				 	
			
			
			
			ob_start();
            $this->ajax_pagination();
            $data['result_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
		
			
		   
		   ###########3
		
		# view file...
		$VIEW = "logged/friends/search_invite_friends.phtml"; 
		parent::_render($data, $VIEW);
	}
	
	public function ajax_pagination($page=0)
    {
        try
        {
			## seacrh conditions : filter ############
				
		    #echo $page .'- page, pagination - ' .$this->pagination_per_page;
			$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
            
            
            if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) {
            	
				if($_POST['select_search']==2)
				{
                
                $WHERE_COND = "";
				$this->session->unset_userdata('preserve_search_condition');
                $s_fname = get_formatted_string(trim($this->input->post('txt_fname')));
                if($WHERE_COND != ''){
                  $WHERE_COND .= ($s_fname=='')?'':" AND (u.s_first_name LIKE '".$s_fname."%' )";
                }else{
                     $WHERE_COND .= ($s_fname=='')?'':" (u.s_first_name LIKE '".$s_fname."%' )";
                }
                
                
                $s_lname = get_formatted_string(trim($this->input->post('txt_lname')));
                if($WHERE_COND != '')
                 $WHERE_COND .= ($s_lname=='')?'':" AND (u.s_last_name LIKE '".$s_lname."%' )";
                else
                  $WHERE_COND .= ($s_lname=='')?'':"  (u.s_last_name LIKE '".$s_lname."%' )";
				  
				/*$s_chat = get_formatted_string(trim($this->input->post('txt_chat')));
                if($WHERE_COND != '')
                 $WHERE_COND .= ($s_chat=='')?'':" AND (u.s_chat_display_name LIKE '".$s_chat."%' )";
                else
                  $WHERE_COND .= ($s_chat=='')?'':"  (u.s_chat_display_name LIKE '".$s_chat."%' )";*/
                  
                  
                
                $s_email = get_formatted_string(trim($this->input->post('txt_email')));
                if($WHERE_COND != '')
                    $WHERE_COND .= ($s_email=='')?'':" AND (binary u.s_email = '".$s_email."' )";
                else
                    $WHERE_COND .= ($s_email=='')?'':" (binary u.s_email = '".$s_email."' )";

                
                /*$s_country = intval(decrypt($this->input->post('txt_country')));
                if($WHERE_COND != '')
                    $WHERE_COND .= ($s_country=='')?'':" AND ( u.i_country_id= '".$s_country."' )";
                else
                    $WHERE_COND .= ($s_country=='')?'':" ( u.i_country_id = '".$s_country."' )";
                
                
                $s_state = get_formatted_string(trim($this->input->post('txt_state')));
                if($WHERE_COND != '')
                    $WHERE_COND .= ($s_state=='')?'':" AND ( u.`s_state`= '".$s_state."' )";
                else
                    $WHERE_COND .= ($s_state=='')?'':"  ( u.`s_state`= '".$s_state."' )";
                    
                
                $s_city = get_formatted_string(trim($this->input->post('txt_city')));
                if($WHERE_COND != '')
                    $WHERE_COND .= ($s_city=='')?'':" AND ( u.`s_city`= '".$s_city."' )";
                else
                    $WHERE_COND .= ($s_city=='')?'':" ( u.`s_city`= '".$s_city."' )";*/
            
                
                /*$s_school_name = get_formatted_string(trim($this->input->post('txt_school_name')));
                if($WHERE_COND != '')
                    $WHERE_COND .= ($s_school_name=='')?'':" AND (s_school_name LIKE '".$s_school_name."%' )";
                else
                    $WHERE_COND .= ($s_school_name=='')?'':"  (s_school_name LIKE '".$s_school_name."%' )";*/
                    
        
                
                $s_class = get_formatted_string(trim($this->input->post('txt_class')));
                if($WHERE_COND != '')
                    $WHERE_COND .= ($s_class=='')?'':" AND (i_class_year = '".$s_class."' )";
                else
                    $WHERE_COND .= ($s_class=='')?'':"  (i_class_year = '".$s_class."' )";
					
				
                    
                  
                    
                $s_emp_name = get_formatted_string(trim($this->input->post('txt_emp')));
                if($WHERE_COND != '')
                    $WHERE_COND .= ($s_emp_name=='')?'':" AND (s_employer_name LIKE '".$s_emp_name."%' )";
                else
                    $WHERE_COND .= ($s_emp_name=='')?'':"  (s_employer_name LIKE '".$s_emp_name."%' )";
                
                
                $s_year_from = get_formatted_string(trim($this->input->post('year_from')));
                if($WHERE_COND != '')
                    $WHERE_COND .= ($s_year_from=='')?'':" AND (s_experience_year_from = '".$s_year_from."' )";
                else
                    $WHERE_COND .= ($s_year_from=='')?'':"  (s_experience_year_from  =  '".$s_year_from."' )";
                    
                
                $s_year_to = get_formatted_string(trim($this->input->post('year_to')));
                if($WHERE_COND != '')
                    $WHERE_COND .= ($s_year_to=='')?'':" AND (s_experience_year_to = '".$s_year_to."' )";
                else
                    $WHERE_COND .= ($s_year_to=='')?'':"  (s_experience_year_to  =  '".$s_year_to."' )";
                
                  $e_gender = get_formatted_string(trim($this->input->post('gender')));
                 if($WHERE_COND != '')
                 {
                    
                    $WHERE_COND .= ($e_gender=='-1')?'':" AND ( u.`e_gender` = '".$e_gender."' )";
                 }
                else
                {
                    
                    $WHERE_COND .= ($e_gender=='-1')?'':"  ( u.`e_gender` = '".$e_gender."' )";
                }
                $s_age_from = get_formatted_string(trim($this->input->post('age_from')));
				$s_age_to = get_formatted_string(trim($this->input->post('age_to')));
                                  if($WHERE_COND != ''){
					//die('jj');
					if(($s_age_from == '-1' && $s_age_to == '-1')){
						$WHERE_COND .= '';
					}
					elseif($s_age_from != '-1' && $s_age_to != '-1'){
						  $WHERE_COND .= " AND ( u.`s_age` BETWEEN '".$s_age_from."' AND '".($s_age_to)."' )";
					}
					elseif($s_age_from == '-1' && $s_age_to != '-1'){
						 $WHERE_COND .= " AND ( u.`s_age` = '".($s_age_to)."' )";
					}
					elseif($s_age_from != '-1' && $s_age_to == '-1'){
						 $WHERE_COND .= " AND ( u.`s_age` = '".($s_age_from)."' )";
					}
                   
				}else{
                    if(($s_age_from == '-1' && $s_age_to == '-1')){
						$WHERE_COND .= '';
					}
					
				}
                
/*                $s_church_name = get_formatted_string(trim($this->input->post('txt_church')));
                if($WHERE_COND != '')
                    $WHERE_COND .= ($s_church_name=='')?'':" AND ( u.`s_church_name` LIKE '".$s_church_name."%' )";
                else
                    $WHERE_COND .= ($s_church_name=='')?'':"  ( u.`s_church_name` LIKE '".$s_church_name."%' )";
*/                    
                 #### new for smart search of country state and city
      
				  $location = get_formatted_string(trim($this->input->post('txt_location'))); 
				  if($location != '')
				  {
				   $location_arr = explode(', ',$location);
				  }
				  $total_locations = count($location_arr);
				  
					 if($total_locations)
					 {
						 for($i=0;$i<$total_locations;$i++)
						 {
							 
						  if($i== 0){
								if($WHERE_COND != '')
									$WHERE_COND .= "  AND (c.s_country like '".trim($location_arr[$i])."%' OR  S.s_state like '".$location_arr[$i]."%' OR city.s_city like '".$location_arr[$i]."%')";
									else
										$WHERE_COND .= "   (c.s_country like '".trim($location_arr[$i])."%' OR  S.s_state like '".$location_arr[$i]."%' OR city.s_city like '".$location_arr[$i]."%')";
						  }
						  else{
						   
						   $WHERE_COND .= "  AND (c.s_country like '{$location_arr[$i]}%' OR  S.s_state like '{$location_arr[$i]}%' OR city.s_city like '{$location_arr[$i]}%')";
						  }
					  
						   
					 }
					 }
					 $s_school_name = get_formatted_string(trim($this->input->post('txt_school_name')));
					  $s_location = get_formatted_string(trim($this->input->post('txt_s_location')));
					    if($s_location != '' )
				  {
				   $s_location_arr = explode(', ',$s_location);
				  }
				  $total_s_locations = count($s_location_arr);
					  if($s_location == '' && $s_school_name != '')
					  {
                			if($WHERE_COND != '')
                   			 $WHERE_COND .= ($s_school_name=='')?'':" AND (s_school_name LIKE '".$s_school_name."%' )";
              			  else
                    		$WHERE_COND .= ($s_school_name=='')?'':"  (s_school_name LIKE '".$s_school_name."%' )";
					  }
					
				  else if($s_location != '' && $s_school_name == '')
				 {
					 if($total_s_locations)
					 {
						 for($i=0;$i<$total_s_locations;$i++)
						 {
							 
						  if($i== 0){
								if($WHERE_COND != '')
								{
									
										$WHERE_COND .= "  AND (e.s_country like '".$s_location_arr[$i]."%' OR  e.s_state like '".$s_location_arr[$i]."%' OR e.s_city like '".$s_location_arr[$i]."%')";
									
								}
									else
										$WHERE_COND .= "   (e.s_country like '".trim($s_location_arr[$i])."%' OR  e.s_state like '".$s_location_arr[$i]."%' OR e.s_city like '".$s_location_arr[$i]."%')";
						  }
						  else{
						   
						   $WHERE_COND .= "  AND (e.s_country like '{$s_location_arr[$i]}%' OR  e.s_state like '{$s_location_arr[$i]}%' OR e.s_city like '{$s_location_arr[$i]}%')";
						  }
					  
						   
					 }
					} 
				 }
				 else if($s_location != '' && $s_school_name != '')
				 {
					 if($total_s_locations)
					 {
						 for($i=0;$i<$total_s_locations;$i++)
						 {
							 
						  if($i== 0){
								if($WHERE_COND != '')
								{
									
										$WHERE_COND .= "  AND ( s_school_name like '".$s_school_name."' AND (e.s_country like '".trim($s_location_arr[$i])."%' OR e.s_state like '".$s_location_arr[$i]."%' OR e.s_city like '".$s_location_arr[$i]."%'))";
									
								}
									else
										$WHERE_COND .= "   (s_school_name like '".$s_school_name."' AND (e.s_country like '".trim($s_location_arr[$i])."%' OR e.s_state like '".$s_location_arr[$i]."%' OR e.s_city like '".$s_location_arr[$i]."%'))";
						  }
						  else{
						   
						   $WHERE_COND .= "  AND ( s_school_name like '".$s_school_name."' AND (e.s_country like '{$s_location_arr[$i]}%' OR  e.s_state like '{$s_location_arr[$i]}%' OR e.s_city like '{$s_location_arr[$i]}%'))";
						  }
					  
						   
					 }
					} 
				 }
       else
	   {
		   $WHERE_COND.='';
	   }
			
            		$s_church_name = get_formatted_string(trim($this->input->post('txt_church')));
					  $c_location = get_formatted_string(trim($this->input->post('txt_c_location')));
					    if($c_location != '' )
				  {
				   $c_location_arr = explode(', ',$c_location);
				  }
				  $total_c_locations = count($c_location_arr);
					  if($c_location == '' && $s_church_name != '')
					  {
                			  if($WHERE_COND != '')
                    $WHERE_COND .= ($s_church_name=='')?'':" AND ( u.`s_church_name` LIKE '".$s_church_name."%' )";
                else
                    $WHERE_COND .= ($s_church_name=='')?'':"  ( u.`s_church_name` LIKE '".$s_church_name."%' )";
					  }
					
				  else if($c_location != '' && $s_church_name == '')
				 {
					 if($total_c_locations)
					 {
						 for($i=0;$i<$total_c_locations;$i++)
						 {
							 
						  if($i== 0){
								if($WHERE_COND != '')
								{
									
										$WHERE_COND .= "  AND (church.s_country like '".$c_location_arr[$i]."%' OR  church_state.s_state like '".$c_location_arr[$i]."%' OR church_city.s_city like '".$c_location_arr[$i]."%')";
									
								}
									else
										$WHERE_COND .= "   (church.s_country like '".trim($c_location_arr[$i])."%' OR  church_state.s_state like '".$c_location_arr[$i]."%' OR church_city.s_city like '".$c_location_arr[$i]."%')";
						  }
						  else{
						   
						   $WHERE_COND .= "  AND (church.s_country like '{$s_location_arr[$i]}%' OR  church_state.s_state like '{$s_location_arr[$i]}%' OR church_city.s_city like '{$s_location_arr[$i]}%')";
						  }
					  
						   
					 }
					} 
				 }
				 else if($c_location != '' && $s_church_name != '')
				 {
					 if($total_c_locations)
					 {
						 for($i=0;$i<$total_c_locations;$i++)
						 {
							 
						  if($i== 0){
								if($WHERE_COND != '')
								{
									
										$WHERE_COND .= "  AND ( s_church_name like '".$s_church_name."' AND (church.s_country like '".trim($c_location_arr[$i])."%' OR church_state.s_state like '".$c_location_arr[$i]."%' OR church_city.s_city like '".$c_location_arr[$i]."%'))";
									
								}
									else
										$WHERE_COND .= "   (s_church_name like '".$s_church_name."' AND (church.s_country like '".trim($c_location_arr[$i])."%' OR church_state.s_state like '".$c_location_arr[$i]."%' OR church_city.s_city like '".$c_location_arr[$i]."%'))";
						  }
						  else{
						   
						   $WHERE_COND .= "  AND ( s_church_name like '".$s_church_name."' AND (church.s_country like '{$c_location_arr[$i]}%' OR  church_state.s_state like '{$c_location_arr[$i]}%' OR church_city.s_city like '{$c_location_arr[$i]}%'))";
						  }
					  
						   
					 }
					} 
				 }
       else
	   {
		   $WHERE_COND.='';
	   }
                                
            
                
                $this->session->set_userdata('search_condition',$WHERE_COND);
                $this->session->set_userdata('is_post_','1');
				
				$this->session->set_userdata('preserve_search_condition',$WHERE_COND);
				
				## storing to preserve search on reload ##
				$this->session->set_userdata('s_fname' , $s_fname);
				$this->session->set_userdata('s_email' , $s_email);
				$this->session->set_userdata('s_lname' , $s_lname);
				/*$this->session->set_userdata('s_state' , $s_state);
				$this->session->set_userdata('s_city' , $s_city);
				$this->session->set_userdata('s_country' , $s_country);*/
                                $this->session->set_userdata('s_age_from' , $s_age_from);
                                 $this->session->set_userdata('s_age_to' , $s_age_to);
                                
                                $this->session->set_userdata('e_gender' , $e_gender);
				$this->session->set_userdata('location' , $location);
				$this->session->set_userdata('s_school_name' , $s_school_name);
				
				$this->session->set_userdata('s_church_name' , $s_church_name);
				$this->session->set_userdata('s_class' , $s_class);
				$this->session->set_userdata('s_emp_name' , $s_emp_name);
				$this->session->set_userdata('s_year_from' , $s_year_from);
				$this->session->set_userdata('s_year_to' , $s_year_to);
			
            
				}
				else
				{
					$this->session->unset_userdata('is_post_');
					$this->session->set_userdata('is_post_','1');
					$WHERE_COND='';
					$this->session->unset_userdata('s_fname' );
				$this->session->unset_userdata('s_email');
				$this->session->unset_userdata('s_lname');
				/*$this->session->set_userdata('s_state' , $s_state);
				$this->session->set_userdata('s_city' , $s_city);
				$this->session->set_userdata('s_country' , $s_country);*/
				$this->session->unset_userdata('location' );
				$this->session->unset_userdata('s_school_name');
				
				$this->session->unset_userdata('s_church_name');
				$this->session->unset_userdata('s_class' );
				$this->session->unset_userdata('s_emp_name' );
				$this->session->unset_userdata('s_year_from');
				$this->session->unset_userdata('s_year_to');
					$this->session->unset_userdata('preserve_search_condition');
					$s_chat = get_formatted_string(trim($this->input->post('txt_chat')));
                if($WHERE_COND != '')
                 $WHERE_COND .= ($s_chat=='')?'':" AND (u.s_chat_display_name LIKE '".$s_chat."%' )";
                else
                  $WHERE_COND .= ($s_chat=='')?'':"  (u.s_chat_display_name LIKE '".$s_chat."%' )";
				  		$this->session->set_userdata('search_condition',$WHERE_COND);
						$this->session->set_userdata('preserve_search_condition',$WHERE_COND);
				}
             }
		
            $exclude_id_csv = $i_profile_id;
		    $s_order_by = "`id` DESC ";
			if(isset($_POST['IS_PRESERVE_SEARCH']) && $_POST['IS_PRESERVE_SEARCH'] == 'Y'){
					$this->session->set_userdata('is_post_','1');
				    $this->session->set_userdata('is_preserve_search', true);
				    $s_where = $this->session->userdata('preserve_search_condition');
			}
			elseif($this->session->userdata('is_preserve_search')){
					$this->session->set_userdata('is_post_','1');
					$this->session->set_userdata('is_preserve_search', true);
					$s_where = $this->session->userdata('preserve_search_condition');	
			}
			else{
					$this->session->set_userdata('is_preserve_search', false);
					$s_where = $this->session->userdata('search_condition');
			}
			
			if($s_where != ''){
				
				 
				
				$result = $this->users_model->get_friends_suggestion($s_where, $exclude_id_csv ,'',$page,$this->pagination_per_page,$s_order_by);
				$resultCount = count($result);
				//echo $this->db->last_query(); 
				#pr($result);
				$total_rows = $this->users_model->get_friends_suggestion_total($s_where, $exclude_id_csv);
				
				if( ( !is_array($result) || !count($result) ) && $total_rows ) {
					$page = $page - $this->pagination_per_page;
					
					$result = $this->users_model->get_friends_suggestion($s_where,'', $page, $this->pagination_per_page,$s_order_by);
				}
				## end seacrh conditions : filter ############
				
				#pr($result,1);
				#Jquery Pagination Starts
				$this->load->library('jquery_pagination');
				$config['base_url'] = base_url()."logged/my_friends/ajax_pagination";
				$config['total_rows'] = $total_rows;
				$config['per_page'] = $this->pagination_per_page;
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
	
				$config['div'] = '#table_content'; /* Here #content is the CSS selector for target DIV */
				$config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
				$config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */
	
				$this->jquery_pagination->initialize($config);
				$data['page_links'] = $this->jquery_pagination->create_links();
	
				// getting   listing...
				$data['search_result_friends'] = $result;
				$data['no_of_result'] = $total_rows;
				$data['current_page'] = $page;
				$data['total_pages'] = ceil($total_rows/$this->pagination_per_page);
				$data['select']=$_POST['select_search'];
				
				 $data['post_val'] = ($total_rows >0 )? 'true':'false';
				 $p = ($page/$this->pagination_per_page);
				 $data['current_loaded_page_no'] =  $p + 1;
				
			}
            $data['is_post_'] = $this->session->userdata('is_post_') ;
			# loading the view-part...
          echo  $this->load->view('logged/friends/search_invite_friends_ajax.phtml', $data,TRUE);
		}
	
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
    }
	
	public function invite_friend() 
    {
        try
        {
            $user_id = intval(decrypt($this->input->post('frnd_id')));   //acceptor ID

            $this->load->model('contacts_model');
            
            $info['i_requester_id']    =    intval(decrypt($this->session->userdata('user_id'))); 
            $info['i_accepter_id']    =    $user_id ; 
            $info['s_status']    =    'pending' ; 
			
			$is_exists = $this->contacts_model->friend_request_already_sent($info['i_requester_id'], $info['i_accepter_id']);
			
			if($is_exists){
             	$_ret_id = 1;
			}else
			{
				$_ret_id = $this->contacts_model->add_info($info);
			}
			#echo $this->db->last_query();
				
            
            if($_ret_id > 0 )
            {
                    $message_id = parent::send_message($info['i_requester_id'], $info['i_accepter_id'], 'contact_request', '', $this->input->post('contact_message') );
        			
					## check if opted for this notification or not ##
					  $notificaion_opt = $this->user_alert_model->check_option_user_id($info['i_accepter_id']);	
					## insert noifications ####
					if($notificaion_opt['e_friend_request_received'] == 'Y'){
						$notification_arr['i_requester_id'] = decrypt($this->session->userdata('user_id'));
						$notification_arr['i_accepter_id'] =  $info['i_accepter_id'];
						$notification_arr['s_type'] = 'friend';
						$notification_arr['dt_created_on'] = get_db_datetime();
						
						
						$ret = $this->user_notifications_model->insert($notification_arr);	
					}
					### end  ###
                   $email_opt = $this->user_alert_model->check_option_email_user_id($info['i_accepter_id']);
						if($email_opt['e_friend_request_received'] == 'Y'){
						
						$mail_arr['i_requester_id'] =get_username_by_id( decrypt($this->session->userdata('user_id')));
						$mail_arr['i_accepter_id'] =  get_username_by_id($info['i_accepter_id']);
						$mail_arr['s_type'] = 'e_friend_request_received';
						$mail_id=get_useremail_by_id($info['i_accepter_id']);
						 $this->load->library('email');
						 $this->load->helper('html');
					$email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
					  $body=$this->load->view('logged/my-mail-template.phtml',$mail_arr,TRUE);
						$this->email->initialize($email_setting);
						$this->email->from('admin@cogtime.com', 'Team Cogtime');
					$this->email->to("$mail_id");
						//$this->email->bcc("$mailids");
				//$this->email->cc('arif.zisu@gmail.com');
					//$this->email->bcc('trisha.paul@hotmail.com');

					$this->email->subject($mail_arr["i_requester_id"].' wants to be your friend!!');
					$this->email->message("$body");

					$this->email->send();
					}	
                    echo json_encode( array('success'=>TRUE, 'msg'=>'Friend request sent successfully.' , 'html_txt'=>"Re-send Friend Request" , 'u_id' => $user_id) );
            }
            else
            {
                echo json_encode( array('success'=>FALSE, 'msg'=>'Error!' , 'html_txt'=>'') );
            }        
    
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }
	
	
	public function confirm_invitation_opt()
    {
		try
		{
		   $this->load->model('users_model');
		   if($this->input->post('type')=='accept')
		    { 
			
					$i_accepter_id =  intval(decrypt($this->session->userdata('user_id')));
					$i_requester_id = intval($this->input->post('i_requester_id'));
					
					/*****************************/
					
					
					
					
					
						$_ret = $this->contacts_model->update_by_requester_accepter( array('s_status'=>'accepted'), $i_requester_id, $i_accepter_id );
			
						if($_ret > 0)
						{
								
								
								$i_msg_id = $this->input->post('i_message_id');
								if(intval($i_msg_id)>0){
									$this->db->update('messages',  array('i_ended'=>'1'), array('id'=>$i_msg_id, 'i_sender_id'=>$i_requester_id));}
								else{
								    $this->db->update('messages',  array('i_ended'=>'1'), array('s_type'=>'contact_request', 'i_sender_id'=>$i_requester_id,'i_receiver_id'=>$i_accepter_id));}
								
								 parent::send_message($i_accepter_id, $i_requester_id, 'contact_accept');
								 
								 ## check if opted for this notification or not ##
								$notificaion_opt = $this->user_alert_model->check_option_user_id($i_requester_id);	
				
								## insert noifications ####
								if($notificaion_opt['e_friend_request_accepted'] == 'Y'){
								  $notification_arr['i_requester_id'] = $i_accepter_id;
								  $notification_arr['i_accepter_id'] =  $i_requester_id;
								  $notification_arr['s_type'] = 'friend_request_accepted';
								  $notification_arr['dt_created_on'] = get_db_datetime();
								  
								  $ret = $this->user_notifications_model->insert($notification_arr);	
								}
								### end  ###
							
							echo json_encode( array('success'=>TRUE, 'msg'=>'Contact accepted successfully!') );
					}
					else
					{
							echo json_encode( array('success'=>FALSE, 'msg'=>'Error!') );
					}
	
			  
			}
			else if($this->input->post('type')=='reject')
			{
				$i_accepter_id =  intval(decrypt($this->session->userdata('user_id')));
				$i_requester_id = intval($this->input->post('i_requester_id'));
					
				//$i_message_id = $this->input->post('i_message_id');
				$_ret = $this->contacts_model->update_by_requester_accepter( array('s_status'=>'rejected'), $i_requester_id, $i_accepter_id );
	
				if($_ret > 0)
				{
						
						 parent::send_message($i_accepter_id, $i_requester_id , 'contact_rejected');
						 
						## check if opted for this notification or not ##
						$notificaion_opt = $this->user_alert_model->check_option_user_id($i_requester_id);	
		
						## insert noifications ####
						if($notificaion_opt['e_friend_request_declined'] == 'Y'){
						  $notification_arr['i_requester_id'] = $i_accepter_id;
						  $notification_arr['i_accepter_id'] =  $i_requester_id ;
						  $notification_arr['s_type'] =  'friend_request_decline';
						  $notification_arr['dt_created_on'] = get_db_datetime();
						  
						  $ret = $this->user_notifications_model->insert($notification_arr);	
						}
						### end  ###
						 
						 
						echo json_encode( array('success'=>TRUE, 'msg'=>'Contact declined successfully!') );
				}
				else
				{
						echo json_encode( array('success'=>FALSE, 'msg'=>'Error!!!') );
				}
			}
		   
		   
		} 
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
	
	}
	
	
	public function cancel_friend_request() 
    {
        try
        {
            $user_id = intval($this->input->post('frnd_id'));   //acceptor ID
            $this->load->model('contacts_model');
            
            $info['i_requester_id']    =    intval(decrypt($this->session->userdata('user_id'))); 
            $info['i_accepter_id']    =    $user_id ; 
          //  $info['s_status']    =    'pending' ; 
            $_ret_id = $this->contacts_model->cancel_friend_request_sent($info);
			#echo $this->db->last_query();
				
            
            if($_ret_id > 0 )
            {
                   
				   $total_sent = $this->contacts_model->total_pending_friend_sent($info['i_requester_id']);
				  //$total_sent=0;
				    if($total_sent > 0){
					    echo json_encode( array('success'=>TRUE, 'msg'=>'Friend cancelled successfully' , 'html_txt'=>"" , 'u_id' => $user_id, 'last_record'=>'N') );
				   }else{
                   	 echo json_encode( array('success'=>TRUE, 'msg'=>'Friend cancelled successfully','html_txt'=>'<div class="shade_box_blue" style="padding-top:5px;"><div  class="shade_norecords" style="width:295px;"><p class="blue_bold12">No Friend Request Sent.</p></div></div>' , 'u_id' => $user_id, 'last_record'=>'Y') ); }
				   
				   
               
            }
            else
            {
                echo json_encode( array('success'=>FALSE, 'msg'=>'Error!' , 'html_txt'=>'') );
            }        
    
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }
	
	
	public function decline_friend_request() 
    {
        try
        {
            $user_id = intval($this->input->post('frnd_id'));   //acceptor ID
            $this->load->model('contacts_model');
            
            $info['i_requester_id']    =   $user_id ;   
            $info['i_accepter_id']    =  intval(decrypt($this->session->userdata('user_id')));  
            $info['s_status']    =    'pending' ; 
            $_ret_id = $this->contacts_model->decline_friend_request_recieved($info);
			$this->db->update('messages',  array('i_ended'=>'1'), array('s_type'=>'contact_request', 'i_sender_id'=>$info['i_requester_id'],'i_receiver_id'=>$info['i_accepter_id']));
			#echo $this->db->last_query();
				
            
            if($_ret_id > 0 )
            {
                   
				   parent::send_message($info['i_accepter_id'], $info['i_requester_id'] , 'contact_rejected');
				   
				    ## check if opted for this notification or not ##
					$notificaion_opt = $this->user_alert_model->check_option_user_id($info['i_requester_id']);	
	
					## insert noifications ####
					if($notificaion_opt['e_friend_request_declined'] == 'Y'){
					  $notification_arr['i_requester_id'] = decrypt($this->session->userdata('user_id'));
					  $notification_arr['i_accepter_id'] =  $info['i_requester_id'];
					  $notification_arr['s_type'] = 'friend_request_decline';
					  $notification_arr['dt_created_on'] = get_db_datetime();
					  
					  $ret = $this->user_notifications_model->insert($notification_arr);	
					}
					### end  ###
				    $email_opt = $this->user_alert_model->check_option_email_user_id($info['i_requester_id']);
						if($email_opt['e_friend_request_declined'] == 'Y'){
						
						$mail_arr['i_requester_id'] =get_username_by_id( decrypt($this->session->userdata('user_id')));
						$mail_arr['i_accepter_id'] =  get_username_by_id($info['i_requester_id']);
						$mail_arr['s_type'] = 'friend_request_decline';
						$mail_id=get_useremail_by_id( $info['i_requester_id']);
						 $this->load->library('email');
						 $this->load->helper('html');
					$email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
					  $body=$this->load->view('logged/my-mail-template.phtml',$mail_arr,TRUE);
						$this->email->initialize($email_setting);
						$this->email->from('admin@cogtime.com', 'Team Cogtime');
					$this->email->to("$mail_id");
						//$this->email->bcc("$mailids");
				//$this->email->cc('arif.zisu@gmail.com');
					//$this->email->bcc('trisha.paul@hotmail.com');

					$this->email->subject($mail_arr["i_requester_id"].' refused your friend request!!');
					$this->email->message("$body");

					$this->email->send();
					}	 
				     $total_sent = $this->contacts_model->total_pending_friend_recieved($info['i_accepter_id']);
				  //$total_sent=0;
				    if($total_sent > 0){
					    echo json_encode( array('success'=>TRUE, 'msg'=>'Friend request declined successfully.' , 'html_txt'=>"" , 'u_id' => $user_id, 'last_record'=>'N') );
				   }else{
                   	 echo json_encode( array('success'=>TRUE, 'msg'=>'Friend request declined successfully.','html_txt'=>'<div class="shade_box_blue" style="padding-top:5px;"><div  class="shade_norecords" style="width:295px;" ><p class="blue_bold12">No Friend Request Recieved.</p></div></div>' , 'u_id' => $user_id, 'last_record'=>'Y') ); }
				   
				   
              
            }
            else
            {
                echo json_encode( array('success'=>FALSE, 'msg'=>'Error!' , 'html_txt'=>'') );
            }        
    
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }
	
	
	
	public function confirm_invitation()
    {
		try
		{
		   $this->load->model('users_model');
			
					 $i_accepter_id =  intval(decrypt($this->session->userdata('user_id')));
					 $i_requester_id = intval($this->input->post('i_requester_id'));
					
					/*****************************/
						
						## checking already accepted ###
					
						$get_friend_status_me_him = $this->users_model->if_already_friend($i_requester_id,$i_accepter_id); 
						 if(count($get_friend_status_me_him) > 0  ) { 
							 $if_already_friend     ='true';
						 }else{
							 $if_already_friend    ='false';
						 }
					
						
						if( $if_already_friend  == 'true'){
						    $_ret = 1;
							 $_msg= 'Already Friend!';
					     }
						 else
						 {
						   $_ret = $this->contacts_model->update_by_requester_accepter( array('s_status'=>'accepted'), $i_requester_id, $i_accepter_id ); #echo $_ret;
						   $_msg = 'Friend request accepted successfully.';
						   $this->db->update('messages',  array('i_ended'=>'1'), array('s_type'=>'contact_request', 'i_sender_id'=>$i_requester_id,'i_receiver_id'=>$i_accepter_id));
						 }
			
						if($_ret > 0)
						{
								
								 $i_record_id = $this->input->post('i_record_id');
								 parent::send_message($i_accepter_id, $i_requester_id, 'contact_accept');
								 
								  ## check if opted for this notification or not ##
								$notificaion_opt = $this->user_alert_model->check_option_user_id($i_requester_id);	
				
								## insert noifications ####
								if($notificaion_opt['e_friend_request_accepted'] == 'Y'){
								  $notification_arr['i_requester_id'] = $i_accepter_id;
								  $notification_arr['i_accepter_id'] =  $i_requester_id;
								  $notification_arr['s_type'] = 'friend_request_accepted';
								  $notification_arr['dt_created_on'] = get_db_datetime();
								  
								  $ret = $this->user_notifications_model->insert($notification_arr);	
								}
								### end  ###
								 $email_opt = $this->user_alert_model->check_option_email_user_id($i_requester_id);
						if($email_opt['e_friend_request_accepted'] == 'Y'){
						
						$mail_arr['i_requester_id'] =get_username_by_id( $i_accepter_id);
						$mail_arr['i_accepter_id'] =  get_username_by_id($i_requester_id);
						$mail_arr['s_type'] = 'friend_request_accepted';
						$mail_id=get_useremail_by_id($i_requester_id);
						 $this->load->library('email');
						 $this->load->helper('html');
					$email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
					  $body=$this->load->view('logged/my-mail-template.phtml',$mail_arr,TRUE);
						$this->email->initialize($email_setting);
						$this->email->from('admin@cogtime.com', 'Team Cogtime');
					$this->email->to("$mail_id");
						//$this->email->bcc("$mailids");
				//$this->email->cc('arif.zisu@gmail.com');
					//$this->email->bcc('trisha.paul@hotmail.com');

					$this->email->subject($mail_arr["i_requester_id"].' is your friend now!!');
					$this->email->message("$body");

					$this->email->send();
					}	 
								
                            
                                $total_sent = $this->contacts_model->total_pending_friend_recieved($i_accepter_id);
                              //$total_sent=0;
                                if($total_sent > 0){
                                    echo json_encode( array('success'=>TRUE, 'msg'=>$_msg , 'html_txt'=>"" , 'u_id' => $i_requester_id, 'last_record'=>'N') );
                               }else{
                                    echo json_encode( array('success'=>TRUE, 'msg'=>$_msg,'html_txt'=>'<div class="shade_box_blue" style="padding-top:5px;"><div  class="shade_norecords" style="width:295px;"><p class="blue_bold12">No Friend Request Received.</p></div></div>' , 'u_id' => $i_requester_id, 'last_record'=>'Y') ); }
                              
							
					}
					else
					{
							echo json_encode( array('success'=>FALSE, 'msg'=>'Error!','u_id'=>'') );
					}
	
		   
		} 
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
	
	}
	
	
	public function delete_friend() 
    {
        try
        {
            $user_id = intval($this->input->post('frnd_id'));   //acceptor ID
            $this->load->model('contacts_model');
            
			$i_profile_id = intval(decrypt($this->session->userdata('user_id'))); 
            $info['i_requester_id']    =   $user_id ;   
            $info['i_accepter_id']    =  intval(decrypt($this->session->userdata('user_id')));  
            $info['s_status']    =    'accepted' ; 
			$_ret_id = $this->contacts_model->delete_friend($info);
			
			 $_ret_id  = 1;
            
            if($_ret_id > 0 )
            {
                   
				   ## search total frnds left ###
				   $total_where =  " WHERE 
										  1
										  AND c.s_status = 'accepted' 
										  AND u.i_status=1 
										  AND
										  ((c.i_requester_id = '".$i_profile_id."' AND u.id=c.i_accepter_id ) 
										  OR (c.i_accepter_id = '".$i_profile_id."' AND u.id=c.i_requester_id )) ".$add_where." GROUP BY u.id " ;
				  $total_frnd_left = $this->contacts_model->gettotal_online_friends($total_where);
				  
				   //$total_frnd_left =0;
				   parent::send_message($info['i_accepter_id'], $info['i_requester_id'] , 'contact_deleted');
				   if($total_frnd_left > 0){
					    echo json_encode( array('success'=>TRUE, 'msg'=>'Friend removed successfully.' , 'html_txt'=>"" , 'u_id' => $user_id, 'last_record'=>'N') );
				   }else{
                   	 echo json_encode( array('success'=>TRUE, 'msg'=>'Friend removed successfully.','html_txt'=>'<div class="shade_box_blue" style="padding-top:5px;"><div  class="shade_norecords" style="width: 275px;" ><p class="blue_bold12">No friends.</p></div></div>' , 'u_id' => $user_id, 'last_record'=>'Y') ); }
            }
            else
            {
                echo json_encode( array('success'=>FALSE, 'msg'=>'Error!' , 'html_txt'=>'') );
            }        
    
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }
	
	public function find_friends($contactimporter='Yahoo')
	{
		$posted=array();
		$this->data["posted"]=$posted;/*don't change*/    
		$data = $this->data;      
		$this->data["MAIN_MENU_SELECTED"] = 1;
		parent::_set_title('::: COGTIME Xtian network :::');
		parent::_set_meta_desc('');
		parent::_set_meta_keywords('');
	
		
		parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
									'js/switch.js','js/animate-collapse.js',
									'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
									'js/stepcarousel.js',
									'js/frontend/logged/my_friends.js',
									'js/frontend/logged/tweets/tweet_utilities.js',
									));
									
		parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
									'css/dd.css') );
		
		$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        
        
        $this->session->set_userdata('is_preserve_search', false);
        $this->session->set_userdata('search_condition',''); 
        $this->session->set_userdata('is_post_','');
		#$data['search_'] = '';
		
		############################# contact importer
		$_REQUEST['contacts_option']	= $contactimporter;
		$_GET['contacts_option']		= $contactimporter;
		include_once(APPPATH.'libraries/contactimporter/example/contacts.main.php');
		$data['handler'] = new ContactsHandler();
		############################# contact importer
		
		## seacrh conditions : filter ############
		
		
			ob_start();
            $this->ajax_pagination();
            $data['result_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
		   
		   ###########3
		$data['gauth']	= '';//$this->load->view('logged/friends/gAuth.phtml',true);
		# view file...
		$VIEW = "logged/friends/find_friends.phtml"; 
		parent::_render($data, $VIEW);
	}
	
	public function send_invitation()
	{
		$mailids		= $this->input->post('frnds_mailid');
                $arr_mailids	= explode(',',$mailids);
                /*---------------------------*/
                $logged_id=intval(decrypt($_SESSION['user_id']));
                                $newArr =  get_primary_user_info($logged_id);
                $this->load->library('email');
    $this->load->helper('html');
        $email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
$this->email->initialize($email_setting);
//$body = "<p>Dear User, </p><p>your church request is denied</p><p>Thanks</p><p>admin@cogtime.com</p> ";
  $logo="http://cogtime.com/images/logo.png";
    $body = '<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#e9f3f5" style="font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:19px;">
  <tr>
    <td align="left" style="background:#013D62; border-bottom:5px solid #62C3BC; padding:15px 0 15px 20px;"><img src="'.$logo.'" alt= ""></td>
  </tr>
  <tr style="border-top:1px solid #ffffff;">
    <td style="padding-top:10px; padding-bottom:10px;">&nbsp;</td>
  </tr>
  <tr>
  	<td style="background:#62C3BC; color:#fff; font-family:Arial, Helvetica, sans-serif; font-size:22px; font-weight:bold; width:300px; margin:0px auto; display:block; padding:15px; text-align:center;">
    '.$newArr["s_first_name"].' '.$newArr["s_last_name"].' invite you at Cogtime
    </td>
  </tr>
  <tr>
  	<td style="padding:15px;"><p> Dear Friend, I would like to invite you to join me at COGTime, the most exciting place for believers to hang out, visit the Prayer Wall, join a Prayer Group, find a Prayer Partner, create a Ring and much more! You have a unique gift and the Kingdom needs you now - than ever before!</p><p> I look forward to connecting with you in COGTime!  '.$newArr["s_first_name"].' '.$newArr["s_last_name"].'</p><p><a href="http://cogtime.com">http://cogtime.com</a></p>
	</td>
</tr>
  <tr>
    <td align="center" valign="middle" style="background:#A8A7A7; padding:15px;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle" style="color:#d3edfd; font-family:Arial, Helvetica, sans-serif; font-size:12px;"> <a href="http://acumencs.com/drandpt-arabic/contact-us/" style="color:#d3edfd; text-decoration:none;"></a></td>
        
        <td align="right" style="color:#013d62; font-family:Arial, Helvetica, sans-serif; font-size:12px; text-align="center" ">© All Rights Reserved<span style="color:#525252;"><strong> COGTIME 2014  </strong></span></td>
      </tr>
    </table></td>
  </tr>
</table>'; 
$this->email->from('admin@cogtime.com', 'Team Cogtime');
$this->email->to("admin@cogtime.com");
$this->email->bcc("$mailids");
//$this->email->cc('arif.zisu@gmail.com');
//$this->email->bcc('them@their-example.com');

$this->email->subject('New invitation from Cogtime');
$this->email->message("$body");

$this->email->send();	
//echo $this->email->print_debugger();
                /*----------------------------*/
		
//		$arr_mailids	= explode(',',$mailids);
//		#pr($arr_mailids,1);
//		$this->load->model('mail_contents_model');
//		$mail_info = $this->mail_contents_model->get_by_name("invite_friends");
//		$body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
//		
//		$body = nl2br(sprintf3( $body, array('usernaname'=>$receiver["s_profile_name"],
//									 'sender_name'=>$sender["s_profile_name"],
//									 "senderemail"=>$arr['s_email'],
//									 "senderphone"=>$arr['s_phone'],
//									 "s_url"=>$sname
//									 )));
//		
//									 
//		
//		foreach($arr_mailids as $v)
//		{
//			$arr['subject'] 	= htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
//			$arr['to']         	= trim($v);
//			//$arr['bcc']        	= 'sanhita.bubu@gmail.com';
//			$arr['from_email'] 	= $this->session->userdata('email');
//			$arr['from_name'] 	= $this->session->userdata('username');//$this->site_settings_model->get('s_mail_from_name');
//			$arr['message'] 	= $body;
//			#pr($arr); exit;
//			
//			send_mail($arr);
//		}
		echo json_encode(array("success"=>true,"msg"=>"Successfully send"));
		exit;
	}
	
	public function add_in_contacts()
	{
		$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
		#pr($_POST['contacts']);
		foreach($_POST['contacts'] as $value)
		{
			$arr_insert['i_user_id']			= $i_profile_id;
			$arr_insert['s_contact_email']		= $value;
			$this->db->insert($this->db->contacts,$arr_insert);
			#echo $this->db->last_query();
		}
		echo json_encode(array("success"=>true,"msg"=>"Successfully added"));
		exit;
	}
	
	public function my_contacts($contactimporter)
	{
		$posted=array();
		$this->data["posted"]=$posted;/*don't change*/    
		$data = $this->data;      
		$this->data["MAIN_MENU_SELECTED"] = 1;
		parent::_set_title('::: COGTIME Xtian network :::');
		parent::_set_meta_desc('');
		parent::_set_meta_keywords('');
	
		
		parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
									'js/switch.js','js/animate-collapse.js',
									'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
									'js/stepcarousel.js',
									'js/frontend/logged/my_friends.js',
									'js/frontend/logged/tweets/tweet_utilities.js',
									));
									
		parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
									'css/dd.css') );
		
		$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        
        
        #$this->session->set_userdata('is_preserve_search', false);
        #$this->session->set_userdata('search_condition',''); 
        #$this->session->set_userdata('is_post_','');
		#$data['search_'] = '';
		
		############################# contact importer
		#include_once(APPPATH.'libraries/contactimporter/example/contacts.main.php');
		
		############################# contact importer
		$_REQUEST['contacts_option']	= $contactimporter;
		$_GET['contacts_option']		= $contactimporter;
		$data['result'] = $this->contacts_model->fetch_contacts($i_profile_id);
		foreach($data['result'] as $emailval)
		{
			$_REQUEST['result'][]	= $emailval['s_contact_email'];
		}
		$_REQUEST['flag_for_page']	= 'mycontacts';
		include_once(APPPATH.'libraries/contactimporter/example/updatecontacts.main.php');
		$data['handler'] = new ContactsHandler();
		# view file...
		$VIEW = "logged/friends/my_contacts.phtml"; 
		parent::_render($data, $VIEW);
	}
	
	
	public function delete_contacts() 
	{
		foreach( explode(',',$this->input->post('csv_mail_ids')) as $id ) 
		{
			$SQL = "DELETE FROM {$this->db->contacts} WHERE id = {$id}";
			$this->db->query($SQL);
		}
		$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
		$result = $this->contacts_model->fetch_contacts($i_profile_id);
		$html = '';
		if(count($result)){
			foreach($result as $contact){	
				
				$html .=	'<li>
								 <input type="checkbox" name="contact_'.$contact['id'].'" />'.$contact['s_contact_email'].'
							 </li>';
			}
		}
		else{
			$html = '<h4 class="no-contacts">No Contacts Imported</h4>';
		}
		echo json_encode( array('sucess'=>TRUE, 'content' =>$html , 'msg'=>'Selected contacts successfully deleted.') );
   }
   
   ### search city state country for auto suggest #####
	
	public function send_cities()
	{
		$letter = $_GET['q']; 
		$this->load->model('location_model');
		
		$arr_category = $this->location_model->get_friend_edu_search_detail($letter); 
		
		//pr($arr_category);
		
		if(count($arr_category)>0)
			foreach($arr_category as $val)
			{
				if($val['result_type'] == 'country_res'){
					echo  $val['s_country']."\n";
				}
				else if($val['result_type'] == 'state_res'){
					echo  $val['s_state'].', '.$val['s_country']."\n";
				}
				else if($val['result_type'] == 'city_res'){
					echo  $val['s_city'].', '.$val['s_state'].', '.$val['s_country']."\n";
				}
				else {
					echo  $val['s_school_name'].', '.$val['s_city'].', '.$val['s_state'].', '.$val['s_country']."\n";
				}
				
			}
		else
			echo "No Result.";
		exit;
	}
   
}   // end of controller...

