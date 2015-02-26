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

class Church_ring_home extends Base_controller
{
    private $pagination_per_page= 3;
    private $comments_pagination_per_page = 10;
	private $people_liked_pagination_per_page = 10;
    public function __construct()
     {
        try
        {
            parent::__construct();
             $this->css_files = array('css/church_admin.css','css/church.css');
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...
			$this->load->helper('wall_helper');
			
            $this->load->model('users_model');
			$this->load->model('contacts_model');
			$this->load->model('church_ring_post_model');
			$this->load->model('church_ring_model');
			$this->load->model('netpals_model');
			$this->load->model('ring_post_comments_model');
			$this->load->model('user_notifications_model');
			$this->load->model('user_alert_model');
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    
    public function index($i_ring_id) 
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
                                        'js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',
										'js/frontend/logged/tweets/tweet_utilities.js',
										'js/frontend/logged/rings/church_ring_helper.js'
                                        ));
                                        
//            parent::_add_css_arr( array('css/church.css') );
										  
			$ring_members	= $this->church_ring_model->get_all_ring_members_by_ring_id($i_ring_id,'','','');
			foreach($ring_members as $arr)
			{
				$arr_invited_id[] = $arr['i_invited_id'];
			}
            
            $data['ring_members'] = $arr_invited_id;							  
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			
			 $this->session->set_userdata('search_condition','');
			 
			$data['pagination_per_page'] = $this->pagination_per_page;
			$data['profile_id'] = $i_user_id;
						
			## fetching ring details #
			$where = " WHERE R.id  = {$i_ring_id} "; 
			$data['ring_detail_arr'] = $this->church_ring_model->get_list($where);
			
			
			$wh = " AND i_joined = 0 ";
			$data['pending_join_req_arr'] = $this->church_ring_model->get_pending_join_req_arr($wh);
			//pr($data['ring_detail_arr'],1);

           	ob_start();
			$this->all_ring_post_ajax_pagination($i_ring_id);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['ring_post_ajax_content'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
		   
		   	
            # view file...
            $VIEW = "logged/church/church-ring-home.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }   
    
    public function all_ring_post_ajax_pagination($i_ring_id, $page=0)
    {
		
		$s_where = $this->session->userdata('search_condition');
		
		$cur_page = $page + $this->pagination_per_page;
		
		$data = $this->data;
		
       
		$result = $this->church_ring_post_model->get_all_ring_post_by_ring_id($i_ring_id,$s_where, intval($page), $this->pagination_per_page);

		$total_rows = $this->church_ring_post_model->get_total_all_ring_post_by_ring_id($i_ring_id,$s_where);
		$data['arr_rings'] = $result;
		$data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
		$data['profile_id'] = $i_user_id;
		
		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->pagination_per_page)
			  $view_more = false;
         //--------- end check
		
		$ring_members_ajax	= $this->church_ring_model->get_all_ring_members_by_ring_id($i_ring_id,'','','');
		
		foreach($ring_members_ajax as $arr)
		{
			$arr_invited_id[] = $arr['i_invited_id'];
		}
		
		$data['ring_members_ajax'] = $arr_invited_id;
				
		$VIEW_FILE = "logged/church/ajax_ring/ajax_ring_post_listing.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
        echo json_encode( array('html'=>$content, 'no_of_result'=>$data['no_of_result'],'view_more'=>$view_more, 'cur_page'=>$data['current_page_1']) );
			
	}
	
	
		
	
	### POSTING in Ring
	
	
	## post in ring  ##
	
	public function post_on_ring($ring_id) 
	{
		$this->load->model('church_ring_post_model');
		$this->load->model('users_model');
		$this->load->model('user_alert_model');
		$user_id = intval(decrypt($this->session->userdata('user_id')));
		$user_details = $this->users_model->fetch_this($user_id);
		
		$arr_messages = array();
		
		 $title = trim($this->input->post('txt_title'));
		 $message = nl2br( htmlspecialchars(trim($this->input->post('message')), ENT_QUOTES, 'utf-8') );
		
		if($title == '' || $title == 'Type title here'){
			$arr_messages['title'] = '* Required Field.';
		}
		
		if($message == '' || $message == 'Max 500 Characters'){
			$arr_messages['post_message'] = '* Required Field.';
		}
		
        $_html='';
		//pr(count($arr_messages));
		
		if(count($arr_messages) == 0 )
		    {
                             $ip = getenv("REMOTE_ADDR") ; 
				$arr['i_ring_id'] = $ring_id;
				$arr['i_user_id'] = $user_id;
				$arr['s_post_title'] = $title;
				$arr['s_post_description'] = $message;
				$arr['dt_created_on'] = get_db_datetime();
                                $arr['u_ip'] = $ip;
				
				$i_ring_post_id = $this->church_ring_post_model->insert($arr);
				
				
				$total_posts = $this->church_ring_post_model->get_total_all_ring_post_by_ring_id($ring_id);
				
				ob_start();
				$this->all_ring_post_ajax_pagination($ring_id);
				$content = ob_get_contents();
				$content_obj = json_decode($content);
				$html = $content_obj->html; 
				$view_more = $content_obj->view_more;
				$cur_page = $content_obj->cur_page;
				ob_end_clean();
				
				
				
				### RING POST COMMENTS  NOTIFICATIONS ####
				$ring_members_arr = array();
				$ring_members	= $this->church_ring_model->get_all_ring_members_by_ring_id($ring_id,'','','');
				foreach($ring_members as $arr)
				{
					$arr_invited_id[] = $arr['i_invited_id'];
				}
				
			  	#### FETCHING RING OWNER ID AND DETAILS
					$where = " WHERE R.id  = {$ring_id} "; 
					$ring_detail_arr = $this->church_ring_model->get_list($where);			
					array_push($arr_invited_id,$ring_detail_arr[0]['i_user_id']);
					
					$ring_members_arr = $arr_invited_id;
				
				## check if opted for this notification or not ##
				if(count($ring_members_arr)){
					
				  foreach($ring_members_arr as $val){	
					   
					   $notificaion_opt = $this->user_alert_model->check_option_user_id($val);	
					  
					    $notification_arr = array();
					  
						## insert noifications ####
						if($notificaion_opt['e_ring_posts_received'] == 'Y' && $val != $user_id){
							
							$notification_arr['i_requester_id'] = $user_id;
							$notification_arr['i_accepter_id'] = $val;
							$notification_arr['s_type'] = 'ring_post';
							$notification_arr['dt_created_on'] = get_db_datetime();
							
							
							$ret = $this->user_notifications_model->insert($notification_arr);	
							
							$message_id = parent::social_notifications_message($notification_arr['i_requester_id'], $notification_arr['i_accepter_id'], 'ring_post_notification', $ring_id);
						}
						$email_opt = $this->user_alert_model->check_option_email_user_id($val);
						if($email_opt['e_ring_posts_received'] == 'Y' && $val != $user_id){
						
						$mail_arr['i_requester_id'] =get_username_by_id( $user_id);
						$mail_arr['i_accepter_id'] =  get_username_by_id($val);
						$mail_arr['s_type'] = 'e_ring_posts_received';
						$mail_arr['Ring_name']=get_ring_name_by_id($ring_id);
						$mail_id=get_useremail_by_id($val);
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

					$this->email->subject($mail_arr["i_requester_id"].' has Posted on ring!!');
					$this->email->message("$body");

					$this->email->send();
					}
					  ### end  ###
				  }
				
				}
				
				### RING POST COMMENTS  NOTIFICATIONS ####
				
				
			
				echo json_encode( array('success'=>'true', 'msg'=>"Post Published!" , 'html'=>$html, 'view_more'=>$view_more, 'cur_page'=>$cur_page,'total_posts'=>$total_posts) );
			}
			 else
		   {
			  echo json_encode( array('success'=>'false', 'arr_messages'=>$arr_messages) );
		   }
		
	}
	
	## end post in ring ## 
	
	## post comments  ##
	
	public function post_comment($feed_id) 
	{
		$this->load->model('ring_post_comments_model');

		$this->load->model('users_model');
		$user_id = intval(decrypt($this->session->userdata('user_id')));
		$user_details = $this->users_model->fetch_this($user_id);

		$message = nl2br( htmlspecialchars(trim($this->input->post('message')), ENT_QUOTES, 'utf-8') );
        $_html='';
		if($message!='')
		    {                
                                  $ip = getenv("REMOTE_ADDR") ; 
				$arr['i_ring_post_id'] = $feed_id;
				$arr['i_user_id'] = $user_id;
				$arr['s_contents'] = $message;
				$arr['dt_created_on'] = get_db_datetime();
				$arr['u_ip'] = $ip;
				$this->ring_post_comments_model->insert($arr);
				$arr['pseudo'] = $user_details['s_profile_name'];
				$data['comment'] = $arr;
				
				$i_ring_id = intval(trim($this->input->post('ring_id')));
				
				$total_comments = $this->ring_post_comments_model->get_total_comments_by_ring_id($i_ring_id);
                
                $_html = ''."Comments "." (".count_ring_comment_link($feed_id).")";
				
				
				### RING POST COMMENTS  NOTIFICATIONS ####
				$ring_members_arr = array();
				$ring_members	= $this->my_ring_model->get_all_ring_members_by_ring_id($i_ring_id,'','','');
				foreach($ring_members as $arr)
				{
					$arr_invited_id[] = $arr['i_invited_id'];
				}
				
			  	#### FETCHING RING OWNER ID AND DETAILS
					$where = " WHERE R.id  = {$i_ring_id} "; 
					$ring_detail_arr = $this->my_ring_model->get_list($where);			
					array_push($arr_invited_id,$ring_detail_arr[0]['i_user_id']);
					
					$ring_members_arr = $arr_invited_id;
				
				## check if opted for this notification or not ##
				if(count($ring_members_arr)){
					
				  foreach($ring_members_arr as $val){	
					   
					   $notificaion_opt = $this->user_alert_model->check_option_user_id($val);	
					   $notification_arr = array();
					  
						## insert noifications ####
						if($notificaion_opt['e_ring_comments_received'] == 'Y' && $val != $user_id){
							
							$notification_arr['i_requester_id'] = $user_id;
							$notification_arr['i_accepter_id'] = $val;
							$notification_arr['s_type'] = 'ring_comment';
							$notification_arr['dt_created_on'] = get_db_datetime();
							
							
							$ret = $this->user_notifications_model->insert($notification_arr);	
							$message_id = parent::social_notifications_message($notification_arr['i_requester_id'], $notification_arr['i_accepter_id'], 'ring_comments_notification', $feed_id);
						}
						$email_opt = $this->user_alert_model->check_option_email_user_id($val);
						if($email_opt['e_ring_comments_received'] == 'Y' && $val != $user_id){
						
						$mail_arr['i_requester_id'] =get_username_by_id( $user_id);
						$mail_arr['i_accepter_id'] =  get_username_by_id($val);
						$mail_arr['s_type'] = 'e_ring_comments_received';
						$mail_arr['Ring_name']=get_ring_name_by_id($i_ring_id);
						$mail_id=get_useremail_by_id($val);
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

					$this->email->subject($mail_arr["i_requester_id"].' has commented on a Post in ring ');
					$this->email->message("$body");

					$this->email->send();
					}
						
					  ### end  ###
				  }
				
				}
				
				### RING POST COMMENTS  NOTIFICATIONS ####
				
				
				
				echo json_encode( array('success'=>'true', 'msg'=>"Comment posted successfully.",'html'=>$_html, 'total_comments'=>$total_comments ) );
			}
			 else
		   {
			  echo json_encode( array('success'=>'false', 'msg'=>"Please enter some text.", 'html'=>$_html) );
		   }
		
	}
	
	## end post comments ##
	
	
	## POST LIKE UNLIKE
	
	//POST LIKE UNLIKE
        public function like_unlike()
         {

             
			  $liked_user_id = intval(decrypt($this->session->userdata('user_id')));
              $window_id = $this->input->post('window_id');
              $like_or_unlike = $this->input->post('like_val');
              $log_time    = get_db_datetime();
              $ip_address  = $this->input->server('REMOTE_ADDR');

              if($like_or_unlike =="Like"){
               $like_unlike_information_array = array( "i_ring_post_id"=>$window_id,
                                                            "i_liked_user_id"=>$liked_user_id,
                                                            "dt_liked_on"=>$log_time);
              }
              else if($like_or_unlike =="Unlike")
                {
                    $like_unlike_information_array = array( "i_ring_post_id"=>$window_id,
                                                            "i_unliked_user_id"=>$liked_user_id,
                                                            "dt_unliked_on"=>$log_time);
                 }

                 $status = 0;
                 $response = $this->ring_post_comments_model->postLikeUnlike($like_unlike_information_array,strtolower($like_or_unlike));
				 
				
				 $_html ='';
                 if($response['value'])
                  {

                $last_id = $response['last_inserted_id'];
                $response_message=  "<span class='success_message'>".$response['message']."</span>";
                $status =1;
                $like_val = like_display($window_id);
                $display_style = $like_val[1];
                $all_user_liked = $like_val[0];

                $dislike_val = dislike_display($window_id);
                $display_style_un = $dislike_val[1];
                $all_user_unliked = $dislike_val[0];



					$_html = ''."Liked by "." (".count_ring_post_like_link($window_id).")";


                  }
                 else
                      $response_message =  "<span class='error_message'>".$response['message']."</span>";


		   $json_data = array ('status'=>$status,'response_message'=>$response_message,'response_html'=>$_html);
           echo json_encode($json_data);



         }
	
	public function fetch_comment_on_ring_post($i_media_id='')
	{
		try
		  {

			 $data = $this->data;  
			 
				 ob_start();
				 $this->comments_ajax_pagination($i_media_id);
				 $data['comments_list'] = ob_get_contents();
				 ob_end_clean();
			 
			  $VIEW = "logged/ring/comments/my_ring_view_comments_lightbox.phtml";
			  $html = $this->load->view($VIEW, $data, true);  
			  echo json_encode( array('result'=>success,'html_data'=>$html) );
		   } 
		catch(Exception $err_obj)
			{
			  show_error($err_obj->getMessage());
			} 
			
	} 
	
	  public function comments_ajax_pagination($i_media_id , $page=0) 
	  {
		 try
		 {
		    $data = $this->data;  
			$result = $this->ring_post_comments_model->get_by_ring_post_id($i_media_id ,$page,
																$this->comments_pagination_per_page);
		    $resultCount = count($result);
			 $total_rows = $this->ring_post_comments_model->get_total_by_ring_post_id($i_media_id);

			$this->load->library('jquery_pagination');
			$config['base_url'] = base_url()."logged/ring_home/comments_ajax_pagination/{$i_media_id}";
			$config['total_rows'] = $total_rows;
			$config['per_page'] = $this->comments_pagination_per_page;
			$config['uri_segment'] = 5;
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
			
			

			$config['div'] = '#view_comments'; /* Here #content is the CSS selector for target DIV */
			#$config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
			#$config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */


			$this->jquery_pagination->initialize($config);
			$data['page_links'] = $this->jquery_pagination->create_links();

			
			$data['result_arr'] = $result;
			$data['no_of_result'] = $total_rows;
			$data['current_page'] = $page;
			$data['total_pages'] = ceil($total_rows/$this->comments_pagination_per_page);
		  
			 $p = ($page/$this->comments_pagination_per_page);
			 $data['current_loaded_page_no'] =  $p + 1;
			
			# rendering the view file...
			$VIEW_FILE = "logged/ring/comments/my_ring_view_comments_lightbox_ajax.phtml";
			$this->load->view($VIEW_FILE, $data);
			//return $html;
			
		} 
        catch(Exception $err_obj)
        {
            
        } 
	
    }
	
	public function fetch_people_liked_post($i_media_id='')
	{
		try
		  {
			   $data = $this->data;  
			   
			   ob_start();
			   $this->fetch_people_liked_post_ajax($i_media_id);
			   $data['people_liked_list'] = ob_get_contents();
			   ob_end_clean();
			  
			  $VIEW = "logged/ring/comments/liked_by_lightbox.phtml";
			  $html = $this->load->view($VIEW, $data, true);  
			  echo json_encode( array('result'=>success,'html_data'=>$html) );
		   } 
		catch(Exception $err_obj)
			{
			  show_error($err_obj->getMessage());
			} 
			
	} 
	
	  public function fetch_people_liked_post_ajax($i_media_id , $page=0) 
	  {
		 try
		 {
		    $data = $this->data;  
			$result = $this->ring_post_comments_model->get_people_liked_by_ring_post_id($i_media_id , $page,
																$this->people_liked_pagination_per_page);
		    $resultCount = count($result);
			$total_rows = $this->ring_post_comments_model->get_total_people_liked_by_ring_post_id($i_media_id);
			//pr($result); 		

			$this->load->library('jquery_pagination');
			$config['base_url'] = base_url()."logged/ring_home/fetch_people_liked_post_ajax/{$i_media_id}";
			$config['total_rows'] = $total_rows;
			$config['per_page'] = $this->people_liked_pagination_per_page;
			$config['uri_segment'] = 5;
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
			
			

			$config['div'] = '#view_people_liked'; /* Here #content is the CSS selector for target DIV */
			#$config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
			#$config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */


			$this->jquery_pagination->initialize($config);
			$data['page_links'] = $this->jquery_pagination->create_links();

			
			$data['result_arr'] = $result;
			$data['no_of_result'] = $total_rows;
			$data['current_page'] = $page;
			$data['total_pages'] = ceil($total_rows/$this->people_liked_pagination_per_page);
		  
			 $p = ($page/$this->people_liked_pagination_per_page);
			 $data['current_loaded_page_no'] =  $p + 1;
			
			# rendering the view file...
			$VIEW_FILE = "logged/ring/comments/liked_by_lightbox_ajax.phtml";
			$this->load->view($VIEW_FILE, $data);
			//return $html;
			
		} 
        catch(Exception $err_obj)
        {
            
        } 
	
    }
	
	
	
	public function approve_join_request()
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
									'js/stepcarousel.js'
									));
									
		parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css') );
		//$data['ring_detail_arr'][0]	= $this->my_ring_model->get_by_id($ring_id);
		$data['profile_id']	= intval(decrypt($this->session->userdata('user_id')));
		
		$data['pagination_per_page'] = $this->pagination_per_page;
		//pr($data['ring_detail_arr']);
		ob_start();
		$content = $this->generate_join_request_listing_AJAX();
		$content = ob_get_contents();
		$content_obj = json_decode($content);
		$data['listingContent'] = $content_obj->html; 
		$data['no_of_result'] = $content_obj->no_of_result;
		ob_end_clean();	
		
		ob_start();
		$content = $this->generate_pending_invitaion_listing_AJAX();
		$content = ob_get_contents();
		$content_obj = json_decode($content);
		$data['listingContentForPendingInvitaion'] = $content_obj->html; 
		$data['no_of_result_for_inv'] = $content_obj->no_of_result;
		ob_end_clean();	
		
		
		ob_start();
		$content = $this->generate_join_request_listing_AJAX(0,'sent');
		$content = ob_get_contents();
		$content_obj = json_decode($content);
		$data['listingContent_sent'] = $content_obj->html; 
		$data['no_of_result_sent'] = $content_obj->no_of_result;
		ob_end_clean();	
		
		ob_start();
		$content = $this->generate_pending_invitaion_listing_AJAX(0, 'recv');
		$content = ob_get_contents();
		$content_obj = json_decode($content);
		$data['listingContentForPendingInvitaion_recv'] = $content_obj->html; 
		$data['no_of_result_for_inv_recv'] = $content_obj->no_of_result;
		ob_end_clean();	
										  
		$VIEW = "logged/ring/approve_request.phtml"; 
        parent::_render($data, $VIEW);
	}
	
	
	public function generate_join_request_listing_AJAX($page=0 ,$type ='recv')
    {
		$data['ringid']	= $ring_id;
		
		$data['type'] = $type;
		$user_id = intval(decrypt($this->session->userdata('user_id')));
		
		if($type == 'sent'){
			$wh	= " AND r.i_invited_id = '".$user_id."'";
		}
		else{
			$wh	= " AND rg.i_user_id = '".$user_id."'";
		}
		$data['ringdata']	= $this->my_ring_model->new_get_ring_join_req_list($wh,$page,$this->pagination_per_page,'');
		$data['ringdata']	= check_friend_netpal_status($data['ringdata']);
		$data['pagination_per_page'] = $this->pagination_per_page;
		$data['arr_join_req']	= $this->my_ring_model->get_join_req_arr();
		
		
		$resultCount = count($data['ringdata']);
		$total_rows = $this->my_ring_model->new_gettotal_ring_join_req($wh);
		$cur_page = $page + $this->pagination_per_page;
        
        
		// getting auction-category listing...
		$data['no_of_ring'] = $total_rows;
		$data['no_of_result'] = $total_rows;
		$data['current_page_1'] = $cur_page;

		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->pagination_per_page)
			  $view_more = false;
         //--------- end check
        # loading the view-part...
        $AJAX_VIEW_FILE = 'logged/ring/ajax_ring/ajax_listing_join_req.phtml';
        
		
		if( $total_rows>0 ) {
        	$listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);
		}
		else {
			$listingContent = '';
		}
        echo json_encode( array('html'=>$listingContent, 
								'current_page'=>$cur_page, 
								'no_of_result'=>$data['no_of_result'],
								'total'=>$total_rows,
								'view_more'=>$view_more ,
								'cur_page'=>$data['current_page_1']) );
    }   
	
	
	
	public function generate_pending_invitaion_listing_AJAX($page=0, $type ='sent')
    {
		$data['ringid']	= $ring_id;
		$data['type'] =  $type;
		$user_id = intval(decrypt($this->session->userdata('user_id')));
		
		if($type =='sent')
			$wh	= " AND r.i_invited_id = '".$user_id."'";
		else
			$wh	= " AND rg.i_user_id = '".$user_id."'";
		
		$data['ringdata']	= $this->my_ring_model->get_ring_inv_list_nw($wh,$page,$this->pagination_per_page,'');
		$data['pagination_per_page'] = $this->pagination_per_page;
		#pr($data['ringdata'],1);
		$resultCount = count($data['ringdata']);
		$total_rows = $this->my_ring_model->gettotal_ring_inv_nw($wh);
		$cur_page = $page + $this->pagination_per_page;
        
        
		// getting auction-category listing...
		$data['no_of_ring'] = $total_rows;
		$data['no_of_result'] = $total_rows;
		$data['current_page_1'] = $cur_page;

		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->pagination_per_page)
			  $view_more = false;
         //--------- end check
        # loading the view-part...
        $AJAX_VIEW_FILE = 'logged/ring/ajax_ring/ajax_listing_inv.phtml';
        
		
		if( $total_rows>0 ) {
        	$listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);
		}
		else {
			$listingContent = '';
		}
        echo json_encode( array('html'=>$listingContent, 
								'current_page'=>$cur_page, 
								'no_of_result'=>$data['no_of_result'],
								'total'=>$total_rows,
								'view_more'=>$view_more ,
								'cur_page'=>$data['current_page_1']) );
    } 
	
	
	
	
	public function accept_req($ringid, $uid, $id, $is_msg=0)
	{
		$uid	= intval(decrypt($uid));
		$ringid	= decrypt($ringid);
		$where = array('i_ring_id' => $ringid,
    					'i_invited_id' => $uid
						);
						
		$id	= decrypt($id);
		$arr['i_joined']	= 1;
		$arr['dt_joined_date']	= get_db_datetime();
		$msgarr	= array();
		
		
		$receiver_id = intval(decrypt($this->session->userdata('user_id')));
		$msgarr	= array('s_type'=>'ring_join_request_from_normal_user', 'i_referred_media_id'=>$ringid,'i_receiver_id'=>$receiver_id);
		$res = $this->my_ring_model->accept_invitation($where,$arr,1,$msgarr);
		$this->social_notifications_message($receiver_id, $uid, 'ring_accept_join_request', $ringid) ;
		
		
		if($is_msg==0)
		{
			ob_start();
			$content = $this->generate_join_request_listing_AJAX($ringid);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			ob_end_clean();	
		
			
			echo json_encode(array('msg'=>'The ring joining request has been accepted successfully.','divid'=>$id,
								'html'=>$content_obj->html, 
								'current_page'=>$content_obj->current_page, 
								'no_of_result'=>$content_obj->no_of_result,
								'total'=>$content_obj->total,
								'view_more'=>$content_obj->view_more ,
								'cur_page'=>$content_obj->cur_page));
			exit;
		}
		else
		{
			echo json_encode(array('msg'=>'The ring joining request has been accepted successfully.','divid'=>$id));
			exit;
		}
	}
	
	public function decline_req($ringid, $uid, $id, $is_msg=0)
	{
		$uid	= intval(decrypt($uid));
		$ringid	= decrypt($ringid);
		$where = array('i_ring_id' => $ringid,
    					'i_invited_id' => $uid
						);
		$id	= decrypt($id);
		$msg_arr	= array();
		$msgarr	= array('id'=>$id);
		$res = $this->my_ring_model->decline_invitation($where,1,$msg_arr);
		$sender_id = intval(decrypt($this->session->userdata('user_id')));
		
		$this->social_notifications_message($sender_id, $uid, 'ring_deny_join_request', $ringid) ;
		
		if($is_msg==0)
		{
			
			
			ob_start();
			$content = $this->generate_join_request_listing_AJAX($ringid);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			ob_end_clean();
			
			echo json_encode(array('msg'=>'The ring joining request has been declined successfully.','divid'=>$id,
								'html'=>$content_obj->html, 
								'current_page'=>$content_obj->current_page, 
								'no_of_result'=>$content_obj->no_of_result,
								'total'=>$content_obj->total,
								'view_more'=>$content_obj->view_more ,
								'cur_page'=>$content_obj->cur_page));
			exit;
		}
		else
		{
			
			echo json_encode(array('msg'=>'The ring joining request has been declined successfully.','divid'=>$id));
			exit;
		}
	}
    
    
    
    
    
    //======================================== INVITE MEMBER ==============================================
    
    public function invite_member($ring_id) 
    {
        try
         {
                
            $data = $this->data;      
               parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',
                                        //'js/frontend/logged/my_friends.js'
                                        'js/frontend/logged/message_box/my_message.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                        'css/dd.css') );
            
            
            /////////////////////////////////////////////
             
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));    
            $i_profile_id = $i_user_id;
            
            $this->load->model('users_model');
            $arr_profile_info = $this->users_model->fetch_this($i_user_id);
            #dump( $arr_profile_info );
            $data['arr_profile_info'] = $arr_profile_info; 
            
            
            ## fetching ring details # for ring sub menu
            $where = " WHERE R.id  = {$ring_id} "; 
            $data['ring_detail_arr'] = $this->my_ring_model->get_list($where);
            $data['profile_id'] = $i_user_id;
            
            $data['ringinvted']=$this->my_ring_model->get_invitation_by_ring_id($ring_id);
            ## FETCHING FRIENDS ###
            
            $WHERE = " WHERE 
                        1
                        AND c.s_status = 'accepted' 
                        AND u.i_status=1 
                        AND
                        ((c.i_requester_id = '".$i_profile_id."' AND u.id=c.i_accepter_id ) 
                        OR (c.i_accepter_id = '".$i_profile_id."' AND u.id=c.i_requester_id ))  GROUP BY u.id "    ;    
              
            $ORDER_BY = "u.s_first_name ASC";
            $contacts = $this->contacts_model->fetch_multi_online_friends($WHERE, null, null,$ORDER_BY);    
            //pr($contacts); 
            
            #echo $this->db->last_query();
            $exclude_id_csv = '';
            $exclude_id_csv .= $i_profile_id.', ';
            $exclude_id_arr = $this->contacts_model->get_friendsId_by_user_id($i_profile_id);
            if(count($exclude_id_arr)){
                    
                $exclude_id_csv .= implode(', ',$exclude_id_arr);
            }
            
            ## FETCHING PRAYER PARTNERS ###
           
            
            //echo $exclude_id_csv;
            
            ## FETCHING NETPALS ###
            $total_where =  " WHERE 1
                                    AND u.i_status=1 
                                    AND ((c.i_requester_id = ".$i_profile_id." AND u.id=c.i_accepter_id) 
                                        OR 
                                    (c.i_accepter_id=".$i_profile_id." AND u.id=c.i_requester_id))
                                    AND c.s_status='accepted' 
                                    AND u.id NOT IN (".$exclude_id_csv.")
                                    GROUP BY u.id " ;
            
            $ORDER_BY = "u.s_first_name ASC";
            
             
            $netpals = $this->netpals_model->fetch_multi_online_netpals($total_where, null,null,$ORDER_BY);    
            //pr($netpals);
            #echo $this->db->last_query();
            
            $total_contact_arr = array();
            
            
            //$contact_arr = array_merge($contacts,$prayer_partners);
            $total_contact_arr =  array_merge($contacts,$netpals);
            array_sort_by_column($total_contact_arr, 's_displayname');
            #pr($total_contact_arr);

            $data['contacts'] = $total_contact_arr;//$contacts;
            #pr($data['contacts']);
            
            $ring_members = $this->my_ring_model->get_all_ring_members_in_table_by_ring_id($ring_id);
            #pr($ring_members );
            foreach($ring_members as $arr)
            {
                //echo "id : ".$arr['user_id'];
                $arr_invited_id[] = $arr['i_invited_id'];
            }
            
           $data['arr_invited_id'] = $arr_invited_id;
            
            
            
            $data['ring_id'] = $ring_id;
            //////////////////////////////////////////
            
            $data['page_view_type'] = 'myaccount'; 
            $VIEW = "logged/ring/invite_member.phtml";
            parent::_render($data, $VIEW);
         
         }        
        catch(Exception $err_obj)
         {
            
         }    
    }
    
    
    function send_ring_invitation()
    {
        $i_user_id = intval(decrypt($this->session->userdata('user_id')));
        
        $ring_id = $this->input->post('ring_id');
        //$ids = $this->input->post('recipients');
		$arr_frnd=$this->input->post('frndinv');
		$arr_netpal=$this->input->post('netpalinv');
		$arr_pp=$this->input->post('ppinv');
		if(empty($arr_frnd))
		{
			$arr_frnd=array(0=>'0');
			#pr($arr_frnd);
		}
		
		//$arr_frnd=explode(',',$inv_frnds);
		//$arr_netpal=$this->input->post('frnd_type2');
		if(empty($arr_netpal))
		{
			$arr_netpal=array(0=>'0');
			
		}
		//$arr_netpal=explode(',',$inv_netpal);
		//$arr_pp=$this->input->post('frnd_type3');
		if(empty($arr_pp))
		{
			$arr_pp=array(0=>'0');
			
		}
		$arr_pgid=$this->input->post('pginv');
		foreach($arr_pgid as $val)
		{
			$u_id=explode('_',$val);
			$arr_pg[]=$u_id['0'];
		}
		$contact_arr=array();
		$complete_arr_frnd=array();
		$contact_arr=array_merge($arr_frnd,$arr_netpal);
		$contact_arr=array_merge($arr_pp,$contact_arr);
		$complete_arr_frnd=array_merge($contact_arr,$complete_arr_frnd);
		$complete_arr_frnd=array_unique($complete_arr_frnd);
		$complete_arr_frnd=array_filter($complete_arr_frnd);
        $MAX_RING_MEMBER  =  $this->data['site_settings_arr']['i_max_ring_member'];
		if($ids != ''){
			$i_ids = explode(',',$ids);
		}
		//echo count($i_ids);
		
		$invited=get_invited($ring_id,$this->db->ring_invitation,'i_ring_id');
		
		foreach($invited as $val1)
		{
			$invited_user[]=$val1['user_id'];
			
		}
		#pr($complete_arr_frnd,1);
        $res = 0;
        if(count($complete_arr_frnd) < $MAX_RING_MEMBER || $MAX_RING_MEMBER == 0)
        {
            //$i_ids = explode(',',$ids);
            foreach($complete_arr_frnd as $i)
            {
				if(!in_array($i,$invited_user))
				{
					
					$invite_id = $i;
					$message_id=$this->social_notifications_message($i_user_id, $invite_id, 'ring_join_request', $ring_id) ;
					
					$info_arr['i_ring_id'] = $ring_id;
					$info_arr['i_invited_id'] = $invite_id;
					$res = $this->my_ring_model->add_invite_member($info_arr);
					#echo $message_id;
				}
                
            }
			#pr($complete_arr_frnd,1);
			insert_invitation($ring_id,$_POST,$this->db->ring_invitation,'i_ring_id','ring');
			echo json_encode(array('success'=>true,'msg'=>'Invitation sent successfully.'));
            exit;
			
        }
        
        else if(count($complete_arr_frnd)== 0)
        {
            $arr_message['send_recepients'] = '* You must select a recipient';
            echo json_encode(array('success'=>false,'arr_message'=>$arr_message));
            exit;
        }
        else
        {
            echo json_encode(array('success'=>false,'arr_message'=>$arr_message,'msg'=>'You can not add more than '.$MAX_RING_MEMBER.' member'));
            exit;
        }
    }
    
    
    //======================================== END OF INVITE MEMBER ==============================================
    
    
	########### NEW FETCH COMMENTS ON WALL METHOD ###########
	
	public function NEW_fetch_comment_ring($i_media_id='')
	{
		try
		  {
			  $data = $this->data; 
			  $html  = ''; 
			  $result = $this->ring_post_comments_model->get_by_ring_post_id($i_media_id);
			
			//pr($result);
			  if(count($result)){
				  
				  foreach($result as $key=> $val){
					  
					 $profile_image_filename = get_profile_image_of_user('thumb',$val['s_profile_photo'],$val['e_gender']);
			 		 $DESC = html_entity_decode(htmlspecialchars_decode($val['s_contents']),ENT_QUOTES,'utf-8');
					 $profile_link = get_profile_url($val['i_user_id'],$val['s_profile_name']);
					
					 $html .= '<div class="txt_content01 comments-number-content"> 
					 			<a href="'.$profile_link.'"><div style="background:url('.$profile_image_filename.') no-repeat center;width:60px; height:60px;" class="pro_photo" ></div></a>
									<div class="left-nw-wal">
										  <p class="blue_bold12"><a href="javascript:void(0);">'.$val['s_profile_name'].'</a></p>
										  <p>'.$DESC.'</p>
											 <p class="read-more">Updated on: '.get_time_elapsed($val['dt_created_on']).'</p>
									</div>
									<div class="clr"></div>
							  </div>'; 
							  
						
				  }
			  }
			  else{
				  $html .= '     <div class="txt_content01 comments-number-content" style="width:475px !important;"> 
										<div style="text-align:center;"><p>No Comments.</p></div>
										</div>
										';
			  }
			 
			  echo json_encode( array('result'=>'success','html_data'=>$html) );
		   } 
			catch(Exception $err_obj)
			{
			  show_error($err_obj->getMessage());
			} 
			
	} 
	
	public function new_fetch_likes_on_ring($i_media_id='')
	{
		try
		  {
			  $data = $this->data; 
			  $html = ''; 
			  $result = $this->ring_post_comments_model->get_people_liked_by_ring_post_id($i_media_id);
			  
			  //pr($result);
			  
			  if(count($result)){
				  foreach($result as $key=> $val){
					  
						 $name = $val['s_profile_name'];
						 $profile_image = get_profile_image_of_user('thumb',$val['s_profile_photo'],$val['e_gender']);
						 
						 $profile_link = get_profile_url($val['i_user_id'],$val['s_profile_name']);
						
						$html .= '     <div class="user_div dp-list-user"> 
											<a href="'.$profile_link.'">
											<div class="pro_photo3" style="background:url('.$profile_image.') no-repeat center;width:60px; height:60px;"></div></a> 
											<a href="javascript:void(0);" class="blue_link">'.$name.'</a> 
										</div>
										';
				  }
				  $html .= '<br class="clr" />';
			  }
			  else{
				  $html .= '     <div class="user_div" style="width:475px !important;"> 
										<div style="text-align:center;"><p>No Likes.</p></div>
										</div>
										';
			  }
			  
			 
			  echo json_encode( array('result'=>'success','html_data'=>$html) );
		   } 
			catch(Exception $err_obj)
			{
			  show_error($err_obj->getMessage());
			} 
			
	} 
	
	 public function decline_ring_invitation($uid,$ringid,$type='')
	 {
	 	$uid	= intval(decrypt($uid));
		$ringid	= decrypt($ringid);
		
        #$res = $this->my_ring_model->leave_ring($ringid);
		$sql = sprintf( 'DELETE FROM '.$this->db->RING_INV_USER.' WHERE i_ring_id=%s AND i_invited_id=%s', $ringid,$uid );
		$this->db->query($sql);
		
		$msgarr	= array('s_type'=>'ring_join_request', 'i_referred_media_id'=>$ringid,'i_receiver_id'=>$uid);
		
		$this->my_ring_model->accept_invitation($where,$arr,1,$msgarr);
		
		if($type == 'recv'){
			ob_start();
			$content = $this->generate_pending_invitaion_listing_AJAX(0, 'recv');
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$listingContentForPendingInvitaion = $content_obj->html; 
			$view_more = $content_obj->view_more; 
			$cur_page = $content_obj->cur_page; 
			$total_rows = $content_obj->total_rows; 
			$no_of_result = $content_obj->no_of_result; 
			ob_end_clean();	
		}
		else if($type="home"){
			
			ob_start();
			$content = $this->ring_home_pending_invitaion_listing_AJAX($ringid);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$listingContentForPendingInvitaion = $content_obj->html; 
			$view_more = $content_obj->view_more; 
			$cur_page = $content_obj->cur_page; 
			$total_rows = $content_obj->total_rows; 
			$no_of_result = $content_obj->no_of_result; 
			ob_end_clean();	
		}
		else{
			ob_start();
			$content = $this->generate_pending_invitaion_listing_AJAX();
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$listingContentForPendingInvitaion = $content_obj->html; 
			$view_more = $content_obj->view_more; 
			$cur_page = $content_obj->cur_page; 
			$total_rows = $content_obj->total_rows; 
			$no_of_result = $content_obj->no_of_result; 
			ob_end_clean();
		}
				
		echo json_encode(array('msg'=>"You have successfully declined",'listingContentForPendingInvitaion'=> $listingContentForPendingInvitaion, 'current_page'=>$cur_page, 
								'no_of_result'=>$data['no_of_result'],
								'total'=>$total_rows,
								'view_more'=>$view_more ,
								'cur_page'=>$data['current_page_1']));
		exit;
	 }  
	
    public function approve_join_request_home($ring_id)
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
									'js/stepcarousel.js'
									));
									
		parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css') );
		$data['ring_detail_arr'][0]	= $this->my_ring_model->get_by_id($ring_id);
		$data['profile_id']	= intval(decrypt($this->session->userdata('user_id')));
		$data['pagination_per_page'] = $this->pagination_per_page;
		//pr($data['ring_detail_arr']);
		ob_start();
		$content = $this->ring_home_join_request_listing_AJAX($ring_id);
		$content = ob_get_contents();
		$content_obj = json_decode($content);
		$data['listingContent'] = $content_obj->html; 
		$data['no_of_result'] = $content_obj->no_of_result;
		ob_end_clean();	
		
		ob_start();
		$content = $this->ring_home_pending_invitaion_listing_AJAX($ring_id);
		$content = ob_get_contents();
		$content_obj = json_decode($content);
		$data['listingContentForPendingInvitaion'] = $content_obj->html; 
		$data['no_of_result_for_inv'] = $content_obj->no_of_result;
		ob_end_clean();	
										  
		$VIEW = "logged/ring/home-approve_request.phtml"; 
        parent::_render($data, $VIEW);
	}
	
	public function ring_home_join_request_listing_AJAX($ring_id, $page=0)
    {
		$data['ringid']	= $ring_id;
		
		$user_id = intval(decrypt($this->session->userdata('user_id')));
		$wh	= " AND r.i_ring_id = '".$ring_id."'";
		$data['ringdata']	= $this->my_ring_model->get_ring_join_req_list($wh,$page,$this->pagination_per_page,'');
		$data['ringdata']	= check_friend_netpal_status($data['ringdata']);
		$data['pagination_per_page'] = $this->pagination_per_page;
		$data['arr_join_req']	= $this->my_ring_model->get_join_req_arr();
		
		
		$resultCount = count($data['ringdata']);
		$total_rows = $this->my_ring_model->gettotal_ring_join_req($wh);
		$cur_page = $page + $this->pagination_per_page;
        
        
		// getting auction-category listing...
		$data['no_of_ring'] = $total_rows;
		$data['no_of_result'] = $total_rows;
		$data['current_page_1'] = $cur_page;

		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->pagination_per_page)
			  $view_more = false;
         //--------- end check
        # loading the view-part...
        $AJAX_VIEW_FILE = 'logged/ring/ajax_ring/home_ajax_listing_join_req.phtml';
        
		
		if( $total_rows>0 ) {
        	$listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);
		}
		else {
			$listingContent = '';
		}
        echo json_encode( array('html'=>$listingContent, 
								'current_page'=>$cur_page, 
								'no_of_result'=>$data['no_of_result'],
								'total'=>$total_rows,
								'view_more'=>$view_more ,
								'cur_page'=>$data['current_page_1']) );
    }   
	
	
	
	public function ring_home_pending_invitaion_listing_AJAX($ring_id, $page=0)
    {
		$data['ringid']	= $ring_id;
		$user_id = intval(decrypt($this->session->userdata('user_id')));
		$wh	= " AND r.i_ring_id = '".$ring_id."'";
		
		$data['ringdata']	= $this->my_ring_model->get_ring_inv_list($wh,$page,$this->pagination_per_page,'');
		$data['pagination_per_page'] = $this->pagination_per_page;
		#pr($data['ringdata'],1);
		$resultCount = count($data['ringdata']);
		$total_rows = $this->my_ring_model->gettotal_ring_inv($wh);
		$cur_page = $page + $this->pagination_per_page;
        
        
		// getting auction-category listing...
		$data['no_of_ring'] = $total_rows;
		$data['no_of_result'] = $total_rows;
		$data['current_page_1'] = $cur_page;

		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->pagination_per_page)
			  $view_more = false;
         //--------- end check
        # loading the view-part...
        $AJAX_VIEW_FILE = 'logged/ring/ajax_ring/home_ajax_listing_inv.phtml';
        
		
		if( $total_rows>0 ) {
        	$listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);
		}
		else {
			$listingContent = '';
		}
        echo json_encode( array('html'=>$listingContent, 
								'current_page'=>$cur_page, 
								'no_of_result'=>$data['no_of_result'],
								'total'=>$total_rows,
								'view_more'=>$view_more ,
								'cur_page'=>$data['current_page_1']) );
    } 
    
	public function new_accept_req($ringid, $uid, $id, $listype, $is_msg=0)
	{
		$uid	= intval(decrypt($uid));
		$ringid	= decrypt($ringid);
		$where = array('i_ring_id' => $ringid,
    					'i_invited_id' => $uid
						);
						
		$id	= decrypt($id);
		$arr['i_joined']	= 1;
		$arr['dt_joined_date']	= get_db_datetime();
		$msgarr	= array();
		
		
		$receiver_id = intval(decrypt($this->session->userdata('user_id')));
		$msgarr	= array('s_type'=>'ring_join_request_from_normal_user', 'i_referred_media_id'=>$ringid,'i_receiver_id'=>$receiver_id);
		$res = $this->my_ring_model->accept_invitation($where,$arr,1,$msgarr);
		$this->social_notifications_message($receiver_id, $uid, 'ring_accept_join_request', $ringid) ;
		
		
		if($is_msg==0)
		{
			
			
			if($listype == 'sent'){
				ob_start();
				$content = $this->generate_join_request_listing_AJAX(0,'sent');
				$content = ob_get_contents();
				$content_obj = json_decode($content);
				ob_end_clean();	
			}
			else{
				
				ob_start();
				$content = $this->generate_join_request_listing_AJAX(0);
				$content = ob_get_contents();
				$content_obj = json_decode($content);
				ob_end_clean();	
			}
			
			echo json_encode(array('msg'=>'The ring joining request has been accepted successfully.','divid'=>$id,
								'html'=>$content_obj->html, 
								'current_page'=>$content_obj->current_page, 
								'no_of_result'=>$content_obj->no_of_result,
								'total'=>$content_obj->total,
								'view_more'=>$content_obj->view_more ,
								'cur_page'=>$content_obj->cur_page));
			exit;
		}
		else
		{
			echo json_encode(array('msg'=>'The ring joining request has been accepted successfully.','divid'=>$id));
			exit;
		}
	}
	
	public function new_decline_req($ringid, $uid, $id, $listype, $is_msg=0)
	{
		$uid	= intval(decrypt($uid));
		$ringid	= decrypt($ringid);
		$where = array('i_ring_id' => $ringid,
    					'i_invited_id' => $uid
						);
		$id	= decrypt($id);
		$msg_arr	= array();
		$msgarr	= array('id'=>$id);
		$res = $this->my_ring_model->decline_invitation($where,1,$msg_arr);
		$sender_id = intval(decrypt($this->session->userdata('user_id')));
		
		$this->social_notifications_message($sender_id, $uid, 'ring_deny_join_request', $ringid) ;
		
		if($is_msg==0)
		{
			
			
			if($listype == 'sent'){
				ob_start();
				$content = $this->generate_join_request_listing_AJAX(0,'sent');
				$content = ob_get_contents();
				$content_obj = json_decode($content);
				ob_end_clean();	
			}
			else{
				
				ob_start();
				$content = $this->generate_join_request_listing_AJAX(0);
				$content = ob_get_contents();
				$content_obj = json_decode($content);
				ob_end_clean();	
			}
			
			echo json_encode(array('msg'=>'The ring joining request has been declined successfully.','divid'=>$id,
								'html'=>$content_obj->html, 
								'current_page'=>$content_obj->current_page, 
								'no_of_result'=>$content_obj->no_of_result,
								'total'=>$content_obj->total,
								'view_more'=>$content_obj->view_more ,
								'cur_page'=>$content_obj->cur_page));
			exit;
		}
		else
		{
			
			echo json_encode(array('msg'=>'The ring joining request has been declined successfully.','divid'=>$id));
			exit;
		}
	}
}   // end of controller...

