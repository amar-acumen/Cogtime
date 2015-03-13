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
    private $pagination_per_page = 10;
	private $comments_per_page = 3;
	
	public function __construct()
     {
        try
        {
            parent::__construct();
           
			# loading reqired model & helpers...
            $this->load->model('events_model');
			
			//$this->load->model('events_user_invited_model');
			//$this->load->model('events_email_invited_model');
			$this->load->model('events_comments_model');
			//$this->load->model('events_feedback_model');
			
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
										'js/production/events_helper.js'
										));
										
    		 
			$where = "WHERE 1 and E.i_status =1 ";
			
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
            $VIEW = "events/events.phtml";
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
			$cur_page = $page + $this->pagination_per_page;
			$cur_time = date('Y-m-d');
			$s_where = " AND e.dt_end_time >= '".$cur_time."'";
			//$order_by = " `dt_start_time` ASC ";
		   	$result = $this->events_model->get_admin_events($s_where,$page,$this->pagination_per_page);

			$total_rows = $this->events_model->get_total_admin_events($s_where); 
			
			//pr($result,1);
			
            // getting   listing...
			$data['hp_events'] = $result;
			$data['no_of_result'] = $total_rows;
			$data['current_page_1'] = $cur_page;
		
			 //--- for check end of he page.
			   $view_more = true;
			   $rest_counter = $total_rows-$page;
			   if($rest_counter<=$this->pagination_per_page)
				  $view_more = false;
			 //--------- end check
			
			
			    $VIEW_FILE = "events/events_ajax.phtml";
			
				if( is_array($result) && count($result) ) {
					$content = $this->load->view( $VIEW_FILE , $data, true);
				}
				else {
					$content = '';
				}
		
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
           echo json_encode( array('html'=>$content, 'current_page'=>$cur_page, 'no_of_result'=>$data['no_of_result'],'total'=>$total_rows,'view_more'=>$view_more ,'cur_page'=>$data['current_page_1']) );
			
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
										'js/production/events_helper.js'));
			
			parent::_add_css_arr( array(/*'css/jquery-ui-1.8.2.custom.css',
                                          'css/thickbox.css'*/) );
																				
										
    		 
			$where = " AND e.id = {$id} ";
			$data['event_info']=$this->events_model->get_all_events($where);
			//echo ($data['event_info']['s_image_1']);
			
			 // ========= FOR GMAP [BEGIN] ==========         
              $map = new simpleGMapAPI();
              $geo = new simpleGMapGeocoder();
              
              $map->setWidth(300);
              $map->setHeight(300);
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
              $coords = $geo->getGeoCoords( $full_address );
              
              $map->addMarker($coords['lat'], $coords['lng'], 'Map Location', '', '', true);
              $this->data['mapshow'] = $map;
              $this->data['geodata'] = $coords;
              
              //print_r($this->data['geodata']);
         	// ========= FOR GMAP [END] ==========
			
			
			  $data['pagination_per_page'] = $this->comments_per_page;
			  $data['i_event_id'] = $id;
			  
			  ob_start();
			  $this->comments_ajax_pagination($id);
			  $content = ob_get_contents();
			  $content_obj = json_decode($content);
			  $data['comments_content'] = $content_obj->html; 
			  $data['no_comments'] = $content_obj->no_comments;
			  ob_end_clean();
			
			
			
			
            # view file...
            $VIEW = "events/event_detail.phtml";
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
			$VIEW_FILE = "events/event_comments_ajax.phtml";
			
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
	
	
	## post comments  ##
	
	public function post_comment_ajax($feed_id) 
	{
		

		$this->load->model('users_model');
		$user_id = intval(decrypt($this->session->userdata('user_id')));
		$user_details = $this->users_model->fetch_this($user_id);

		$message = nl2br( htmlspecialchars(trim($this->input->post('message')), ENT_QUOTES, 'utf-8') );
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
		
	

}   // end of controller...

