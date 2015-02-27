<?php
/*********
* Author
* 
* Purpose:
*  Controller For Login Page 
* 
* 
*/

include(APPPATH.'controllers/base_controller.php');

# including "gmap-API" class...
include_once APPPATH."libraries/gmapAPI/simpleGMapAPI.php";
include_once APPPATH."libraries/gmapAPI/simpleGMapGeocoder.php";

class Events extends Base_controller
{
    private $pagination_per_page = 3;
	private $comments_per_page = 3;
	private $feedbacks_per_page = 3;
	public function __construct()
     {
        try
        {
            parent::__construct();
           
			# loading reqired model & helpers...
            $this->load->model('events_model');
			
			$this->load->model('events_user_invited_model');
			$this->load->model('events_email_invited_model');
			$this->load->model('events_comments_model');
			$this->load->model('events_feedback_model');
			$this->load->model('user_notifications_model');
			
			
			$this->load->helper('wall_helper');
		
			
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
			     
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
			parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
										'js/ModalDialog.js',
										'js/lightbox.js',	
										'js/jquery.autofill.js',
										 'js/stepcarousel.js',*/
										'js/frontend/logged/events/events_helper.js'
										));
										
    		 
			$where = "WHERE 1 and e.i_status =1 ";
			
			//$data['hp_events']=$this->events_model->get_list($where);
			$data['pagination_per_page'] = $this->pagination_per_page;
			
			/*ob_start();
            $this->ajax_pagination();
            $data['result_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();*/
			
			ob_start();
			$this->ajax_pagination();
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['result_content'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
			
			//pr($data['info_arr']);
            # view file...
            $VIEW = "logged/my_events/events.phtml";
            parent::_render($data, $VIEW);
			
		 }
        catch(Exception $err_obj)
        {
           
        }
    }
	
			  # function to load ajax-pagination [AJAX CALL]...
    public function ajax_pagination($page=0)
    {
        try
        {
			$cur_page = intval($page) + intval($this->pagination_per_page);
			//$data = $this->data;
			//$order_by = " `dt_start_time` ASC ";
		   	$result = $this->events_model->get_all_events($s_where,$page,$this->pagination_per_page);
			//echo $s_where;exit;
			$total_rows = $this->events_model->get_total_all_events($s_where); 
			
            // getting   listing...
			$data['hp_events'] = $result;
			$data['no_of_result'] = $total_rows;
			$data['current_page_1'] = $cur_page;
			//$data['page'] = $page;
			//pr($data);
			 //--- for check end of he page.
				$view_more = true;
			    $rest_counter = $total_rows - $page;
			   //echo $rest_counter;exit;
			   if($rest_counter<=$this->pagination_per_page)
				  $view_more = false;
			 //--------- end check
			
			
			    $VIEW_FILE = "logged/my_events/events_ajax.phtml";
			
				if( is_array($result) && count($result) ) {
					$content = $this->load->view( $VIEW_FILE , $data, true);
				}
				else {
					$content = '';
				}
		
		//echo json_encode( array('html'=>$content, 'current_page'=>$cur_page) );
          echo json_encode( array('html'=>$content, 'current_page'=>$cur_page, 'no_of_result'=>$data['no_of_result'],'total'=>$total_rows,'view_more'=>$view_more,'cur_page'=>$data['current_page_1'] ) );
			
		 }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
    }
	

	
	
	
	
	public function event_detail($id) 
    {
        try
        {      
			     
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data; 
			     
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
			parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
										'js/ModalDialog.js',
										'js/lightbox.js',	
										'js/jquery.autofill.js',
										'js/thickbox.js','js/stepcarousel.js',*/
										'js/frontend/logged/events/events_helper.js'));
			
			parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                          'css/thickbox.css') );																	
																				
										
    		 
			$where = " AND e.id = {$id} ";
			$data['event_info']=$this->events_model->get_all_events($where);
			//echo ($data['event_info']['s_image_1']);
			
			 // ========= FOR GMAP [BEGIN] ==========         
              $map = new simpleGMapAPI();
              $geo = new simpleGMapGeocoder();
              
              $map->setWidth(475);
              $map->setHeight(180);
              $map->setZoomLevel(13); // not really needed because showMap is called in this demo with auto zoom
              /*$map->setBackgroundColor('#d0d0d0');
              $map->setMapDraggable(true);
              $map->setDoubleclickZoom(true);
              $map->setScrollwheelZoom(true);
              $map->showDefaultUI(false);
              $map->showMapTypeControl(true, 'DROPDOWN_MENU');
              $map->showNavigationControl(true, 'DEFAULT');
              $map->showScaleControl(true);
              $map->showStreetViewControl(true);
              $map->setZoomLevel(4); // not really needed because showMap is called in this demo with auto zoom
              $map->setInfoWindowBehaviour('SINGLE_CLOSE_ON_MAPCLICK');
              $map->setInfoWindowTrigger('MOUSEOVER');*/
              $full_address	= $data['event_info'][0]['s_address'].' '.$data['event_info'][0]['s_postcode'].' '.$data['event_info'][0]['s_city'].' '.get_country_name_by_id($data['event_info'][0]['i_country_id']);
				//echo $full_address;
              $coords = $geo->getGeoCoords( $full_address );
              
              $map->addMarker($coords['lat'], $coords['lng'], 'Map Location', '', '', true);
              $this->data['mapshow'] = $map;
              $this->data['geodata'] = $coords;
              
              //print_r($this->data['geodata']);
         	// ========= FOR GMAP [END] ==========
			
			
			  $data['pagination_per_page'] = $this->comments_per_page;
			  $data['i_event_id'] = $id;
			 // $data['page_name']="event_detail";
			  ob_start();
			  $this->comments_ajax_pagination($id);
			  $content = ob_get_contents();
			  $content_obj = json_decode($content);
			  $data['comments_content'] = $content_obj->html; 
			  $data['no_comments'] = $content_obj->no_comments;
			  ob_end_clean();
			
			 ob_start();
			  $this->feedback_ajax_pagination($id);
			  $content = ob_get_contents();
			  $content_obj = json_decode($content);
			  $data['feedbacks_content'] = $content_obj->html; 
			  $data['no_feedback'] = $content_obj->no_feedback;
			  ob_end_clean();
			
			
            # view file...
            $VIEW = "logged/my_events/event_detail.phtml";
            parent::_render($data, $VIEW);
			
		 }
        catch(Exception $err_obj)
        {
           
        }
    }
	
	
	 public function comments_ajax_pagination($i_media_id , $page=0) 
	  {
		 try
		 {
		    $data = $this->data;
			$cur_page = $page + $this->comments_per_page;  
			$result = $this->events_comments_model->get_by_event_id($i_media_id , $page,
																$this->comments_per_page);
		    $resultCount = count($result);
			$total_rows = $this->events_comments_model->get_total_by_event_id($i_media_id);
			#pr($result); 		
			
			$data['result_arr'] = $result;
			$data['no_comments'] = $total_rows;
			$data['current_page'] = $cur_page; //exit;
			
		
			# rendering the view file...
			$VIEW_FILE = "logged/my_events/event_comments_ajax.phtml";
			
		    //--- for check end of he page.
			 $view_more = true;
			 $rest_counter = $total_rows-$page;
			 if($rest_counter<=$this->comments_per_page)
				$view_more = false;
		    //--------- end check
			  if( is_array($result) && count($result) ) {
				  $content = $this->load->view( $VIEW_FILE , $data, true);
			  }
			  else {
				  $content = '';
			  }
		
           echo json_encode( array('html'=>$content, 'no_comments'=>$data['no_comments'],'total'=>$total_rows,'view_more'=>$view_more ,'cur_page'=>$data['current_page']) );
			
		} 
        catch(Exception $err_obj)
        {
            
        } 
	
    }
	
	
	public function feedback_ajax_pagination($i_media_id , $page=0)
	{
	try
		 {
		    $data = $this->data;
			$cur_page_feedback = $page + $this->feedbacks_per_page;  
			$result = $this->events_feedback_model->get_feed_list($i_media_id , $page,
																$this->feedbacks_per_page);
		    $resultCount = count($result);
			$total_rows = $this->events_feedback_model->get_total_by_event_id($i_media_id);
			//pr($result,1); 		
			
			$data['result_arr_feedback'] = $result;
			$data['no_feedback'] = $total_rows;
			$data['current_page_feedback'] = $cur_page_feedback; //exit;
			
		
			# rendering the view file...
			$VIEW_FILE = "logged/my_events/events_feedbacks_ajax.phtml";
			
		    //--- for check end of he page.
			 $view_more = true;
			 $rest_counter = $total_rows-$page;
			 if($rest_counter<=$this->feedbacks_per_page)
				$view_more = false;
		    //--------- end check
			  if( is_array($result) && count($result) ) {
				  $feedback_content = $this->load->view( $VIEW_FILE , $data, true);
			  }
			  else {
				  $feedback_content = '';
			  }
		
           echo json_encode( array('html'=>$feedback_content, 'no_feedback'=>$data['no_feedback'],'total'=>$total_rows,'view_more'=>$view_more ,'cur_fpage'=>$data['current_page_feedback']) );
			
		} 
        catch(Exception $err_obj)
        {
            
        } 
	}
	
	## post comments  ##
	
	public function post_comment_ajax($feed_id) 
	{
		

		$this->load->model('users_model');
		$user_id = intval(decrypt($this->session->userdata('user_id')));
		$user_details = $this->users_model->fetch_this($user_id);
		if(nl2br( htmlspecialchars(trim($this->input->post('message')), ENT_QUOTES, 'utf-8') )== 'Max 500 Char allowed')
		{
			$message='';
		}
		else
		{
			$message = nl2br( htmlspecialchars(trim($this->input->post('message')), ENT_QUOTES, 'utf-8') );
		}
        $_html='';
		if($message!='')
		    {
				$arr		= array();
				$arr['i_event_id'] = $feed_id;
				$arr['i_user_id'] = $user_id;
				$arr['s_contents'] = $message;
				$arr['dt_created_on'] = get_db_datetime();
				
				$this->events_comments_model->insert($arr); 
				$arr['pseudo'] = $user_details['s_profile_name'];
				$data['comment'] = $arr;
				
				## SENDING SYSTEM NOTIFICATION MESSAGE ###
				
				$event_owner_info = $this->events_model->get_owner_by_event_id($feed_id);
					//pr($media_owner_user_details);
				if($event_owner_info['i_user_id'] != $user_id){	
				
					$info['i_requester_id'] = $user_id;
					$info['i_accepter_id'] = $event_owner_info['i_user_id'];
					
					$message_id = parent::social_notifications_message($info['i_requester_id'], $info['i_accepter_id'], 'event_comment', $feed_id);
					## check if opted for this notification or not ##
					//$notificaion_opt = $this->user_alert_model->check_option_user_id($info['i_accepter_id']);	
				  
					## insert notifications ####
					//if($notificaion_opt['e_photo_comments_received'] == 'Y'){
						$notification_arr['i_requester_id'] = $info['i_requester_id'];
						$notification_arr['i_accepter_id'] = $info['i_accepter_id'];
						$notification_arr['s_type'] = 'event_comment';
						$notification_arr['dt_created_on'] = get_db_datetime();
						
						$ret = $this->user_notifications_model->insert($notification_arr);
					//}
				}
				### END ##########
				
				
				
				ob_start();
				$this->comments_ajax_pagination($feed_id);
				$content = ob_get_contents();
				$content_obj = json_decode($content);
				$comments_content = $content_obj->html; 
				$view_more = $content_obj->view_more;
				$cur_page = $content_obj->cur_page;
				ob_end_clean();
				
                
                //$_html = ''."Comments "." (".count_event_comment_link($feed_id).")";
				echo json_encode( array('success'=>'true', 'msg'=>"Comment posted successfully.",'html'=>$comments_content, 'view_more'=>$view_more,'cur_page'=>$cur_page ) );
			}
			 else
		   {
			  echo json_encode( array('success'=>'false', 'msg'=>"Please enter some text.", 'html'=>$comments_content, 'view_more'=>$view_more,'cur_page'=>$cur_page) );
		   }
		
	}
	
	## end post comments ##
	
	
	### function to zoom photo
   
	public function zoom_photo($image_num, $id)
	{ 
		$data = $this->data;
		$data['photo_info'] = $this->events_model->get_by_id($id);
		//echo $data['photo_info']['s_image_'.$image_num]; exit;
		
		$data['image_name'] = $data['photo_info']['s_image_'.$image_num];
		
		$this->load->view('layouts/blocks/event_image_zoom.phtml', $data);
	}
		
	 public function invite_member($event_id) 
    {
        try
         {
		// echo $event_id;exit;
                
            $data = $this->data;      
               parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',*/
                                        //'js/frontend/logged/my_friends.js'
                                        'js/frontend/logged/message_box/my_message.js'
                                        ));
                                        
            parent::_add_css_arr( array(/*'css/jquery-ui-1.8.2.custom.css',
                                        'css/dd.css'*/) );
            
            
            /////////////////////////////////////////////
             
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));    
            $i_profile_id = $i_user_id;
            
            $this->load->model('users_model');
            $arr_profile_info = $this->users_model->fetch_this($i_user_id);
            #dump( $arr_profile_info );
            $data['arr_profile_info'] = $arr_profile_info; 
            
            
            ## fetching ring details # for ring sub menu
            $where = " WHERE E.id  = {$event_id} "; 
            $data['event_info'] = $this->events_model->get_list($where);
            $data['profile_id'] = $i_user_id;
            
            $data['event_invited']=$this->events_model->get_invited_by_id($event_id);
           
          
    
            
            $data['event_id'] = $event_id;
            //////////////////////////////////////////
            
            $data['page_view_type'] = 'myaccount'; 
            $VIEW = "logged/my_events/invite_member.phtml";
            parent::_render($data, $VIEW);
         
         }        
        catch(Exception $err_obj)
         {
            
         }    
    }
	
	function send_event_invitation($event_id)
	{
	try{
		if($this->input->post('frndinv')=='0'&& $this->input->post('netpalinv') == '0' && $this->input->post('ppinv') == '0' )
		{
		echo json_encode(array('success'=>true,'msg'=>'please select some recipients.'));
		exit;
		}
		else{
	
		insert_invitation($event_id,$_POST,$this->db->event_invitation,'i_event_id');
				## insert guest id in  cg_event_user_invited #
				$arr_frnd=array();
				$arr_netpal=array();
				$arr_pp=array();
				if($this->input->post('frndinv')=='0')
				{
					$arr_frnd['0']='0';
					//echo '0';
				}
				else
				{
					$arr_frnd=$this->input->post('frndinv');
			//echo '1';
				}
				//pr($arr_frnd,1);
				if($this->input->post('netpalinv') == '0')
				{
					$arr_netpal['0']='0';
				}
				else
				{
					$arr_netpal=$this->input->post('netpalinv');
				}
				if($this->input->post('ppinv') == '0')
				{
					$arr_pp[]='0';
				}
				else
				{
					$arr_pp=$this->input->post('ppinv');
				}
				$complete_arr_frnd =  array();
				$contact_arr = array();
			
				$contact_arr = array_merge($arr_frnd,$arr_netpal);
				//echo $arr_frnd 
				$complete_arr_frnd =  array_merge($contact_arr,$arr_pp);
				$complete_arr_frnd = array_unique($complete_arr_frnd);
				$complete_arr_frnd = array_filter($complete_arr_frnd);
			
				if(count($complete_arr_frnd)){
					
					$guest_info = array();
					//$guest_arr = explode('##',$this->input->post('h_friend_id'));
					foreach($complete_arr_frnd as $val){
						 
						$notification_arr  = array(); 
						  
						$guest_info['i_user_id'] = $val;
						$guest_info['i_event_id'] = $event_id;	
						$guest_info['dt_created_on'] = get_db_datetime();	
						//pr($guest_info,1);
						$i_newid = $this->events_user_invited_model->insert_user_invited_from_contacts($guest_info);
						
						## send im and add notifications ##
						
						#$notificaion_opt = $this->user_alert_model->check_option_user_id($info['i_accepter_id']);	
						//if($notificaion_opt['e_photo_comments_received'] == 'Y'){
						$notification_arr['i_requester_id'] = decrypt($this->session->userdata('user_id'));
						$notification_arr['i_accepter_id'] = $guest_info['i_user_id'];
						$notification_arr['s_type'] = 'event_invitation';
						$notification_arr['dt_created_on'] = get_db_datetime();
						#pr($notification_arr);
						$ret = $this->user_notifications_model->insert($notification_arr);	
						//}
						$message_id = parent::social_notifications_message($notification_arr['i_requester_id'], $notification_arr['i_accepter_id'], 'event_invitations_request', $event_id);
						
						## send im and add notifications ##
						
					}
				}
				
				echo json_encode(array('success'=>true,'msg'=>'users invited successfully'));
				
				exit;
				}
	}
	catch(Exception $err_obj){
	}
	}
	
	
	function invite_sent($event_id)
	{
	
	 try
         {
		// echo $event_id;exit;
                
            $data = $this->data;      
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',*/
                                        //'js/frontend/logged/my_friends.js'
                                        'js/frontend/logged/message_box/my_message.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                        'css/dd.css') );
            
            
            /////////////////////////////////////////////
             
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));    
            $i_profile_id = $i_user_id;
			$where = " WHERE E.id  = {$event_id} "; 
            $data['event_info'] = $this->events_model->get_list($where);
            $data['profile_id'] = $i_user_id;
			//$data['inv_sent']=$this->events_model->get_event_inv($event_id);
			ob_start();
			$content = $this->event_home_pending_invitaion_listing_AJAX($event_id);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['inv_sent'] = $content_obj->html; 
			$data['no_of_result_for_inv'] = $content_obj->no_of_result;
			ob_end_clean();	
			//pr($data['inv_sent'],1);
            //  $data['event_id'] = $event_id;
            //////////////////////////////////////////
            $data['pagination_per_page']=$this->pagination_per_page;

            $data['page_view_type'] = 'myaccount'; 
            $VIEW = "logged/my_events/home-approve_request.phtml";
            parent::_render($data, $VIEW);
         
         }        
        catch(Exception $err_obj)
         {
            
         }    
	}

	public function event_home_pending_invitaion_listing_AJAX($event_id, $page=0)
    {
		$data['event_id']	= $event_id;
		$user_id = intval(decrypt($this->session->userdata('user_id')));
		//$wh	= " AND r.i_ring_id = '".$ring_id."'";
		
		$data['eventdata']	= $this->events_model->get_event_inv($event_id,$page,$this->pagination_per_page);
		$data['pagination_per_page'] = $this->pagination_per_page;
		#pr($data['ringdata'],1);
		$resultCount = count($data['eventdata']);
		$total_rows = $this->events_model->gettotal_event_inv($event_id);
		//echo $total_rows;exit;
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
        $AJAX_VIEW_FILE = 'logged/my_events/home_ajax_listing_inv.phtml';
        
		
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
}   // end of controller...

