<?php
/*********
* Author: 
* Date  : 
* Modified By: 
* Modified Date:
* 
* Purpose:
* 
* 
*/
include(APPPATH.'controllers/base_controller.php');

class Organizer_day_view extends Base_controller
{
    
    
    public function __construct()
     {
        try
        {
            parent::__construct();
            //parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...
            //$this->load->model('users_model');
			$this->load->model('organizer_note_model');
			$this->load->model('organizer_todo_model');
			$this->load->model('system_reminder_model');
			$this->load->model('events_model');
			
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
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery.dd.js',
                                        'js/stepcarousel.js',
										'js/frontend/logged/organizer/organizer.js',*/
										'js/production/tweet_utilities.js',
//										'js/tab.js',
										'js/jquery.jcarousel.min.js',
//										'js/jquery-ui-1.8.2.custom.min.js',
										'js/jquery.mCustomScrollbar.min.js',
										'js/production/organizer.js'
										
                                        ));
                                        
            parent::_add_css_arr( array('css/date/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css') );
										  
            $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
			$data['right_panel']['uid'] = $i_profile_id; 
			
			if($_POST){
				$sel_date = $_POST['goto_date'];
				$current_date_arr = explode('-',$sel_date);
				$data['selected_year'] = $year = $current_date_arr[0] ;
				$data['selected_month'] = $month = $current_date_arr[1];
				$data['selected_day'] = $day  = $current_date_arr[2];
				$data['selected_date'] = $year.'/'.$month.'/'.$day;
			}
			else{
				$data['selected_year'] = $year = date('Y');
				$data['selected_month'] = $month = date('m');
				$data['selected_day'] = $day = date('d');
				$data['selected_date'] = date('Y').'/'.date('m').'/'.date('d');
			}
			
			
			
			$this->session->set_userdata('day',$day );
			$this->session->set_userdata('year',$year);
			$this->session->set_userdata('month',$month);
			$this->session->set_userdata('selected_date',$data['selected_date']);
			
			/*ob_start();
			$this->ajax_list();
			$data['result_content'] = ob_get_contents();
			ob_end_clean(); */
			
			
			ob_start();
			$this->timeListingData('00:00' ,60,$day, $month, $year);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['result_content'] = $content_obj->html; 
			ob_end_clean();
			
            # view file...
            $VIEW = "logged/organize/new-organize-calender-view.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }   
    
   public function ajax_list(){
	   
		$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
		$year = $this->session->userdata('year');
		$month = 	$this->session->userdata('month');
		$day = 	$this->session->userdata('day');
		$data['selected_date'] =	$this->session->userdata('selected_date');
		$curr_date = trim($this->input->post('hd_date'));
		
		$s_where = 'AND i_active = 1 AND YEAR(`d_date`)= '. $year .' AND MONTH(`d_date`)= '. $month.' AND DAY(`d_date`) = '.$day;
		$data['note_list'] = $this->organizer_note_model->get_by_user_id($i_profile_id, $s_where);
	
		$s_where = 'AND i_active = 1 AND YEAR(`d_date`)= '. $year .' AND MONTH(`d_date`)= '. $month.' AND DAY(`d_date`) = '.$day;
		$data['todo_list'] = $this->organizer_todo_model->get_by_user_id($i_profile_id, $s_where);
		
		$s_where = 'AND i_status =  1 AND YEAR(`dt_start_time`)= '. $year .' AND MONTH(`dt_start_time`)= '. $month .' AND DAY(`dt_start_time`) = '.$day;
	    $data['events_list'] = $this->events_model->get_by_user_id($i_profile_id, $s_where);
		#echo $this->db->last_query();
		
		
		$VIEW_FILE = "logged/organize/ajax_day_view.phtml";
		$this->load->view( $VIEW_FILE , $data);
	   
   }
	
	
	public function add_note_ajax()
	{
		try
		{
			
			parent::check_login(TRUE,'',array('1')); // put this code on those pages which are not accessable by non logged in user
			$arr_messages = array();
			# error message trapping...
			
			if( trim($this->input->post('txt_desc'))=='') 
			{
					$arr_messages['desc'] = "* Required Field.";
			}
			/*if( trim($this->input->post('s_time'))=='-1') 
			{
					$arr_messages['time'] = "* Required Field.";
			}*/
			
		
			
		   //pr($arr_messages);
			if( count($arr_messages)==0 ) {
					
				$info = array();
				
								
				$info['i_user_id'] = intval(decrypt($this->session->userdata('user_id')));
				$info['s_title'] = get_formatted_string($this->input->post('txt_title')); 
				$info['s_description'] = get_formatted_string($this->input->post('txt_desc')); 
				$info["t_time"] = trim($this->input->post('s_time'));
				
		
				$info["d_date"] = trim($this->input->post('hd_date'));
				$info['dt_created_on'] = get_db_datetime();
				$_ret = $this->organizer_note_model->insert($info);
				
				$current_date_arr = explode('-',$info["d_date"]);
				
				
				
				if($this->input->post('hd_note_ajax_method') != '')
				{
					$date = date('Y-m-d',mktime(0, 0, 0, $current_date_arr[1] ,$current_date_arr[2], $current_date_arr[0]));
					$ts = strtotime($date);
					// find the year (ISO-8601 year number) and the current week
					$year = date('o', $ts);
					$week = date('W', $ts); 
					
					ob_start();
					$this->week_slider_ajax_list($current_date_arr[0],$current_date_arr[1],$current_date_arr[2], '', $week);
					$content = ob_get_contents();
					$content_obj = json_decode($content); #pr($content_obj,1);
					$cal_html = $content_obj->cal_html;
					$html = $content_obj->html;
					$selected_date = $info["d_date"];
					$weekNumber = $content_obj->weekNumber;
					$display_date = $content_obj->display_date;
					ob_end_clean();
					
					$decode_html = false;
				
				}else{			
				
					ob_start();
					$this->slider_ajax_list($current_date_arr[0],$current_date_arr[1],$current_date_arr[2]);
					$content = ob_get_contents();
					$content_obj = json_decode($content);
					$cal_html = base64_encode($content_obj->cal_html);
					$html = base64_encode($content_obj->html);
					$selected_date = $info["d_date"];
					$display_date  = date('F  d, Y', strtotime($info["d_date"]));
					ob_end_clean();
					$decode_html = true;
				}
				
				
									
					
				echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'html'=>$html,'msg'=>'Note Added Successfully.','html'=>$html, 'cal_html'=>$cal_html,'selected_date'=>$selected_date, 'display_date'=> $display_date ,  'weekNumber'=>$weekNumber, 'decode_html'=>$decode_html));
			}
			else
			{
				echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>t('Error!')) );
			}
		
		
		
		}
		catch(Exception $err_obj)
        {
            
        } 
	}
    
	
	public function edit_note_ajax($id)
	{
		try
		{
			parent::check_login(TRUE,'',array('1')); // put this code on those pages which are not accessable by non logged in user
			
			if($_POST){
				  $arr_messages = array();
				  
				  $id = intval($this->input->post('i_note_id')); 
				
				  # error message trapping...
				 
				  if( trim($this->input->post('txt_edit_desc'))=='') 
				  {
						  $arr_messages['edit_desc'] = "* Required Field.";
				  }
				  /*if( trim($this->input->post('s_edit_time'))=='-1') 
				  {
						  $arr_messages['edit_time'] = "* Required Field.";
				  }*/
				 
	  
				 //pr($arr_messages);
				  if( count($arr_messages)==0 ) {
						  
					  $info = array();
					  $info['i_user_id'] = intval(decrypt($this->session->userdata('user_id')));
					  $info['s_title'] = get_formatted_string($this->input->post('txt_edit_title')); 
					  $info['s_description'] = get_formatted_string($this->input->post('txt_edit_desc')); 
					  $info["t_time"] = trim($this->input->post('s_edit_time'));
					  $info["d_date"] = trim($this->input->post('hd_edit_date'));
					  $info['dt_updated_on'] = get_db_datetime();
					  
					 // pr($info,1);
					  $_ret = $this->organizer_note_model->update($info, $id);
					  
					 	
					  $current_date_arr = explode('-',$info["d_date"]);
					  ob_start();
					  $this->slider_ajax_list($current_date_arr[0],$current_date_arr[1],$current_date_arr[2]);
					  $content = ob_get_contents();
					  $content_obj = json_decode($content);
					  $cal_html = base64_encode($content_obj->cal_html);
					  $html = base64_encode($content_obj->html);
					  $selected_date = $info["d_date"];
					  $display_date  = date('F  d, Y', strtotime($info["d_date"]));
					  ob_end_clean();				  
						  
					  echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'html'=>$html,'msg'=>'Note updated successfully.','html'=>$html, 'cal_html'=>$cal_html,'selected_date'=>$selected_date));
				  }
				  else
				  {
					  echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'Error!') );
				  }
			}
			else
				{
					  
					   
						$i_user_id = intval(decrypt($this->session->userdata('user_id')));
						$s_where = " AND id = {$id}";
			  			$note_arr = $this->organizer_note_model->get_by_user_id($i_user_id, $s_where , 0, 1);
						$note_arr[0]['s_description'] = get_unformatted_string_edit($note_arr[0]['s_description']);
						echo json_encode( array('success'=>true,'note_arr'=>$note_arr[0],'msg'=>'Note updated successfully.'));

				  }
		
		
		}
		catch(Exception $err_obj)
        {
            
        } 
	}
	
	
	
	public function add_todo_ajax()
	{
           // die('ok');
		try
		{
			
			parent::check_login(TRUE,'',array('1')); // put this code on those pages which are not accessable by non logged in user
			$arr_messages = array();
			# error message trapping...
			/********************************************/
                        $user_id = intval(decrypt($this->session->userdata('user_id')));
			$query = $this->db->get_where('cg_organizer_to_do_list', array('i_user_id' => $user_id));
                        foreach ($query->result() as $row)
                            {
                              $date =  $row->d_date;
                              $time = $row->t_start_time;
                              $i_active = $row->i_active;
                              //$time2 = "01:00:00";
                              $post_time = $this->input->post('todo_strt_frm');
                              $time_stamp = strtotime($post_time);
                              $time_stamp = date('H', $time_stamp);
                              $time_stamp = $time_stamp.':00:00';
                               //$time_stamp =date("H:i:s", strtotime("$time + 1 hour"));
                               //$time_stamp = date("H:i:s",strtotime($time_stamp));
                              //echo $post_time;
                              //die();
                              if($this->input->post('hd_tododate') == $date &&  $time_stamp == $time && $i_active == 1 ){
                                $arr_messages['time_stamp'] = "* Time slot already booked";  
                              }
                            }
                        /**********************************************/
			if( trim($this->input->post('ta_todo_desc'))=='') 
			{
					$arr_messages['todo_desc'] = "* Required Field.";
			}
			if( trim($this->input->post('todo_strt_frm'))=='-1') 
			{
					$arr_messages['todo_strt_frm'] = "* Required Field.";
			}
			if( trim($this->input->post('todo_end_frm'))=='-1') 
			{
					$arr_messages['todo_end_frm'] = "* Required Field.";
			}
			
		/*	if( trim($this->input->post('todo_rem_time'))=='-1') 
			{
					$arr_messages['todo_rem_time'] = "* Required Field.";
			}*/
			
			
		   //pr($arr_messages);
			if( count($arr_messages)==0 ) {
					
				$info = array();
				
								
				$info['i_user_id'] = intval(decrypt($this->session->userdata('user_id')));
				$info['s_title'] = get_formatted_string($this->input->post('txt_todo_title')); 
				$info['s_description'] = get_formatted_string($this->input->post('ta_todo_desc')); 
				
				
				### ::::::added to show proper time on website:::::::
				$info["d_display_date"] = trim($this->input->post('hd_tododate'));
				$info["t_display_start_time"] = trim($this->input->post('todo_strt_frm'));
				$info["t_display_end_time"] = trim($this->input->post('todo_end_frm'));

  				$date_disp_a = new DateTime($info["d_display_date"].' '.$info["t_display_start_time"]);
				$date_disp_b = new DateTime($info["d_display_date"].' '.trim($this->input->post('todo_rem_time')));
				$interval_disp = date_diff($date_disp_a,$date_disp_b);

				$info["t_display_remind_me_back"] = $interval_disp->format('%h:%i:%s');
				$info["t_display_remind_time"] = trim($this->input->post('todo_rem_time'));
				### ::::::added to show proper time on website:::::::
				
				
				$info["t_remind_time"] = trim($this->input->post('todo_rem_time'));
				
				if(trim($this->input->post('todo_rem_time'))=='-1')
					  $info['i_reminder_status']  = 2;
				
				#### showing alert accor to user local time-zone  ####
				#$USER_TIME_ZONE = get_user_timezone_by_id($info['i_user_id']);
				
				
				
				$info["d_date"] = trim($this->input->post('hd_tododate')); #getUserDateTime(trim($this->input->post('hd_tododate')).' '.trim($this->input->post('todo_strt_frm')), $USER_TIME_ZONE, 'dateonly');
				
				$info["t_start_time"] = trim($this->input->post('todo_strt_frm')); #getUserDateTime(trim($this->input->post('hd_tododate')).' '.trim($this->input->post('todo_strt_frm')), $USER_TIME_ZONE ,'timeonly');
				$info["t_end_time"] = trim($this->input->post('todo_end_frm'));#getUserDateTime(trim($this->input->post('hd_tododate')).' '.trim($this->input->post('todo_end_frm')), $USER_TIME_ZONE,'timeonly');
				
				//
				$date_a = new DateTime($info["d_date"].' '.$info["t_start_time"]);
				$date_b = new DateTime($info["d_date"].' '.trim($this->input->post('todo_rem_time')));

#pr($date_a); pr($date_b,1);
				$interval = date_diff($date_a,$date_b);

				$info["t_remind_me_back"] = $interval->format('%h:%i:%s');
				
				
				//$info['dt_created_on'] = get_db_datetime();
                                $info['dt_created_on'] = trim($this->input->post('hd_tododate')).' '.trim($this->input->post('todo_strt_frm'));
				
				//pr($info,1);

				$_ret = $this->organizer_todo_model->insert($info);
				
				
				
				$current_date_arr = explode('-',$info["d_date"]);
				
				
				
				if($this->input->post('hd_list_ajax_method') != '')
				{
					$date = date('Y-m-d',mktime(0, 0, 0, $current_date_arr[1] ,$current_date_arr[2], $current_date_arr[0]));
					$ts = strtotime($date);
					// find the year (ISO-8601 year number) and the current week
					$year = date('o', $ts);
					$week = date('W', $ts); 
					
					ob_start();
					$this->week_slider_ajax_list($current_date_arr[0],$current_date_arr[1],$current_date_arr[2], '', $week);
					$content = ob_get_contents();
					$content_obj = json_decode($content); #pr($content_obj,1);
					$cal_html = $content_obj->cal_html;
					$html = $content_obj->html;
					$selected_date = $info["d_date"];
					$weekNumber = $content_obj->weekNumber;
					$display_date = $content_obj->display_date;
					ob_end_clean();
					
					$decode_html = false;
				
				}else{		
				
					ob_start();
					$this->slider_ajax_list($current_date_arr[0],$current_date_arr[1],$current_date_arr[2]);
					$content = ob_get_contents();
					$content_obj = json_decode($content);
					$cal_html = base64_encode($content_obj->cal_html);
					$html = base64_encode($content_obj->html);
					$selected_date = $info["d_date"];
					$display_date  = date('F  d, Y', strtotime($info["d_date"]));
					ob_end_clean();	
					$decode_html = true;
				}
					
				echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'html'=>$html,'msg'=>'To-Do-List Added Successfully.','html'=>$html, 'cal_html'=>$cal_html,'selected_date'=>$selected_date,'display_date'=> $display_date ,  'weekNumber'=>$weekNumber, 'decode_html'=>$decode_html));
                               // echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'html'=>$html,'msg'=>'To-Do-List Added Successfully.', 'cal_html'=>$cal_html,'selected_date'=>$selected_date,'display_date'=> $display_date ,  'weekNumber'=>$weekNumber, 'decode_html'=>$decode_html));
			}
			else
			{
				echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'Error!') );
			}
		
		
		
		}
		catch(Exception $err_obj)
        {
            
        } 
	}
	
	
	public function edit_todo_ajax($id)
	{
		try
		{
			parent::check_login(TRUE,'',array('1')); // put this code on those pages which are not accessable by non logged in user
			
			if($_POST){
				  $arr_messages = array();
				  
				  $id = intval($this->input->post('i_list_id')); 
			
				  # error message trapping...
				/******************************************/
                                  /********************************************/
                                  $hd_edit_display_time = trim($this->input->post('hd_edit_display_time'));
                       $user_id = intval(decrypt($this->session->userdata('user_id')));
                       //die();
                       $time = $this->input->post('edit_todo_strt_frm');
                        $date = trim($this->input->post('hd_edit_tododate'));
                      $sql = 'select * from `cg_organizer_to_do_list` where `i_user_id` = '.$user_id.' and `t_start_time` != "'.$hd_edit_display_time.'" and d_date = "'.$date.'" and i_active = 1';
			//die();
                      $query = $this->db->query($sql);
                        $time_stamp_arr = $query->result();
                       // pr($time_stamp_arr,1);
                        foreach ($time_stamp_arr as $row)
                            {
                              $date =  $row->d_date;
                             $time = $row->t_start_time;
                              $i_active = $row->i_active;
                              //$time2 = "01:00:00";
                              $post_time = $this->input->post('edit_todo_strt_frm');
                              $time_stamp = strtotime($post_time);
                              $time_stamp = date('H', $time_stamp);
                              $time_stamp = $time_stamp.':00:00';
                               //$time_stamp =date("H:i:s", strtotime("$time + 1 hour"));
                               //$time_stamp = date("H:i:s",strtotime($time_stamp));
                             // echo$post_time;
                             // die();
                              if($this->input->post('hd_edit_tododate') == $date &&  $time_stamp == $time && $i_active == 1  ){
                                $arr_messages['edit_todo_rem_time'] = "*Time slot already booked";  
                              }
                            }
                        /**********************************************/
                        /**********************************************/
				  if( trim($this->input->post('ta_edit_todo_desc'))=='') 
				  {
						  $arr_messages['edit_todo_desc'] = "* Required Field.";
				  }
				  if( trim($this->input->post('edit_todo_strt_frm'))=='-1') 
				  {
						  $arr_messages['edit_todo_strt_frm'] = "* Required Field.";
				  }
				  if( trim($this->input->post('edit_todo_end_frm'))=='-1') 
				  {
						  $arr_messages['edit_todo_end_frm'] = "* Required Field.";
				  }
				 /* if( trim($this->input->post('edit_todo_rem_time'))=='-1') 
				  {
						  $arr_messages['edit_todo_rem_time'] = "* Required Field.";
				  }*/
				
	  
				 //pr($arr_messages);
				  if( count($arr_messages)==0 ) {
						  
					  				
					  $info['i_user_id'] = intval(decrypt($this->session->userdata('user_id')));
					  $info['s_title'] = get_formatted_string($this->input->post('txt_edit_todo_title')); 
					  $info['s_description'] = get_formatted_string($this->input->post('ta_edit_todo_desc')); 
					  /*$info["t_remind_time"] = trim($this->input->post('edit_todo_rem_time'));
					 				  
					  $info["d_date"] = trim($this->input->post('hd_edit_tododate'));
				
				
					  $info["t_start_time"] = trim($this->input->post('edit_todo_strt_frm'));
					  $info["t_end_time"] = trim($this->input->post('edit_todo_end_frm'));
					  
					  
					  $date_a = new DateTime($info["d_date"].' '.$info["t_start_time"]);
					  $date_b = new DateTime($info["d_date"].' '.trim($this->input->post('edit_todo_rem_time')));
	  
					  $interval = date_diff($date_a,$date_b);
	  
					  $info["t_remind_me_back"] = $interval->format('%h:%i:%s');
					  
					  
					  
					  
					  */
					    ### ::::::added to show proper time on website:::::::
						$info["d_display_date"] = trim($this->input->post('hd_edit_tododate'));
						$info["t_display_start_time"] = trim($this->input->post('edit_todo_strt_frm'));
						$info["t_display_end_time"] = trim($this->input->post('edit_todo_end_frm'));
		
						$date_disp_a = new DateTime($info["d_display_date"].' '.$info["t_display_start_time"]);
						$date_disp_b = new DateTime($info["d_display_date"].' '.trim($this->input->post('todo_rem_time')));
						$interval_disp = date_diff($date_disp_a,$date_disp_b);
		
						$info["t_display_remind_me_back"] = $interval_disp->format('%h:%i:%s');
						$info["t_display_remind_time"] = trim($this->input->post('edit_todo_rem_time'));
						### ::::::added to show proper time on website:::::::
						
						$info["t_remind_time"] = trim($this->input->post('edit_todo_rem_time'));
						#### showing alert accor to user local time-zone  ####
						$USER_TIME_ZONE = get_user_timezone_by_id($info['i_user_id']);
						//$info["d_date"] = getUserDateTime(trim($this->input->post('hd_edit_tododate')).' '.trim($this->input->post('edit_todo_strt_frm')), $USER_TIME_ZONE, 'dateonly');
						$info["d_date"] = trim($this->input->post('hd_edit_tododate'));
						//$info["t_start_time"] = getUserDateTime(trim($this->input->post('hd_edit_tododate')).' '.trim($this->input->post('edit_todo_strt_frm')), $USER_TIME_ZONE ,'timeonly');
						//$info["t_end_time"] = getUserDateTime(trim($this->input->post('hd_edit_tododate')).' '.trim($this->input->post('edit_todo_end_frm')), $USER_TIME_ZONE,'timeonly');
						$info["t_start_time"] =trim($this->input->post('edit_todo_strt_frm'));
                                                $info["t_end_time"] = trim($this->input->post('edit_todo_end_frm'));
						//
						$date_a = new DateTime($info["d_date"].' '.$info["t_start_time"]);
						$date_b = new DateTime($info["d_date"].' '.trim($this->input->post('todo_rem_time')));
		
		#pr($date_a); pr($date_b,1);
						$interval = date_diff($date_a,$date_b);
		
						$info["t_remind_me_back"] = $interval->format('%h:%i:%s');
					  
					  
					  $info['dt_updated_on'] = get_db_datetime();
					  if(trim($this->input->post('edit_todo_rem_time'))=='-1')
					  	$info['i_reminder_status']  = 2;
					  else
					  	$info['i_reminder_status']  = 1;
					  $_ret = $this->organizer_todo_model->update($info, $id);
					  
					 
					  $current_date_arr = explode('-',$info["d_date"]);
					  ob_start();
					  $this->slider_ajax_list($current_date_arr[0],$current_date_arr[1],$current_date_arr[2]);
					  $content = ob_get_contents();
					  $content_obj = json_decode($content);
					  $cal_html = base64_encode($content_obj->cal_html);
					  $html = base64_encode($content_obj->html);
					  $selected_date = $info["d_date"];
					  $display_date  = date('F  d, Y', strtotime($info["d_date"]));
					  ob_end_clean();							  
						  
					  echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'html'=>$html,'msg'=>'To-Do-List updated successfully.','html'=>$html, 'cal_html'=>$cal_html,'selected_date'=>$selected_date));
				  }
				  else
				  {
					  echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'Error!') );
				  }
			}
			else
				{
					  
						$i_user_id = intval(decrypt($this->session->userdata('user_id')));
						$s_where = " AND id = {$id}";
			  			$note_arr = $this->organizer_todo_model->get_by_user_id($i_user_id, $s_where , 0, 1);
						$note_arr[0]['s_description'] = get_unformatted_string_edit($note_arr[0]['s_description']);
						//pr($note_arr);
						echo json_encode( array('success'=>true,'note_arr'=>$note_arr[0],'msg'=>'To-Do-List updated successfully.'));

				  }
		
		
		}
		catch(Exception $err_obj)
        {
            
        } 
	}
	
	public function remove_note_ajax($id)
	{
		try
		{
			parent::check_login(TRUE,'',array('1')); // put this code on those pages which are not accessable by non logged in user
			
			$info['i_active'] = 2;
			$info['dt_updated_on'] = get_db_datetime();
			$note_info = $this->organizer_note_model->get_by_id($id);
			$_ret = $this->organizer_note_model->update($info, $id);
			
			
			
			$current_date_arr = explode('-',$note_info["d_date"]);
			ob_start();
			$this->slider_ajax_list($current_date_arr[0],$current_date_arr[1],$current_date_arr[2]);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$cal_html = base64_encode($content_obj->cal_html);
			$html = base64_encode($content_obj->html);
			$selected_date = $note_info["d_date"];
			ob_end_clean();							  
			
							
			echo json_encode( array('success'=>true,'html'=>$html,'msg'=>'Note removed successfully.' ,'html'=>$html, 'cal_html'=>$cal_html,'selected_date'=>$selected_date));
		
		}
		catch(Exception $err_obj)
        {
            
        } 
	}
	
	
	public function remove_list_ajax($id)
	{
		try
		{
			parent::check_login(TRUE,'',array('1')); // put this code on those pages which are not accessable by non logged in user
			
			$info = array();
			$info['i_active'] = 2;
			$info['i_reminder_status'] = 2;
			$info['dt_updated_on'] = get_db_datetime();
			$list_info = $this->organizer_todo_model->get_by_id($id);
			$_ret = $this->organizer_todo_model->update($info, $id);
			
			
			$rem_info['i_reminder_status'] = 2;
			$rem_info['dt_updated_on'] = get_db_datetime();
			$this->system_reminder_model->update_by_reminder_id($rem_info, $id);
			
			
			$current_date_arr = explode('-',$list_info["d_date"]);
			ob_start();
			$this->slider_ajax_list($current_date_arr[0],$current_date_arr[1],$current_date_arr[2]);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$cal_html = base64_encode($content_obj->cal_html);
			$html = base64_encode($content_obj->html);
			$selected_date = $list_info["d_date"];
			ob_end_clean();		

			echo json_encode( array('success'=>true,'html'=>$html,'msg'=>'To-do-List removed successfully.','html'=>$html, 'cal_html'=>$cal_html,'selected_date'=>$selected_date));
		
		}
		catch(Exception $err_obj)
        {
            
        } 
	}
	
	
	public function set_reminder_off_ajax($id)
	{
		try
		{
			parent::check_login(TRUE,'',array('1')); // put this code on those pages which are not accessable by non logged in user
			
			$info = array();
			$hd_arr = array();
			$hd_arr = $this->input->post('hd_list_');
			
			//pr($hd_arr);
			if(count($hd_arr)){
			
				foreach($hd_arr as $key=> $val){
					
					$val_arr = explode('###', $val);// pr($val_arr);
					//echo $val; exit;
					$is_checked = (($this->input->post('list_'.$val_arr[0]) == '1'))?'yes':'no';
					
					if($is_checked == 'yes'){
					 // $info['i_active'] = 2;
					  $info['i_reminder_status'] = 2;
					  $info['dt_updated_on'] = get_db_datetime();
					  
					  if($val_arr[1] == 'to-do'){	
						  $_ret = $this->organizer_todo_model->update($info, $val_arr[0]);
					  }
					  else{
						  $_ret = $this->events_model->update($info, $val_arr[0]);
					  }
					  
					  $rem_info['i_reminder_status'] = 2;
					  $rem_info['dt_updated_on'] = get_db_datetime();
					  $this->system_reminder_model->update_by_reminder_id($rem_info, $val_arr[0]);
					}
				}
		    }
			/*ob_start();
			$this->ajax_list();
			$html = base64_encode(ob_get_contents());
			ob_end_clean(); */	

			echo json_encode( array('success'=>true,'html'=>$html,'msg'=>"Selected To-do-List's reminder hidden successfully."));
		
		}
		catch(Exception $err_obj)
        {
            
        } 
	}
	
	
	
	public function generate_end_time_list($start_time)
	{
		try
		{
			
			if($start_time != -1){
				$start_hr_min = date('H:i',strtotime($start_time));
				$sel_html = '';
				
				$time = date('H:i', strtotime($start_hr_min) );	
					
				$sel_html .= makeOption_Endtime($time);				
			
				
				echo json_encode( array('success'=>true,'sel_html'=>$sel_html, 'start_time'=>$start_time));
			}else
			{
				echo json_encode( array('success'=>false,'sel_html'=>$sel_html, 'start_time'=>$start_time));
			}
		
		}
		catch(Exception $err_obj)
        {
            
        } 
	}
	
	
	########## ajax load notes , to do list and events
	
	 public function slider_ajax_list($year, $month, $day , $day_type){
	   
		$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
		
		
		if($day_type == 'prev'){
			$data['selected_day'] = date('d',mktime(0, 0, 0, $month  ,$day-1, $year));
			$data['selected_year']= date('Y',mktime(0, 0, 0, $month  ,$day-1, $year));
			$data['selected_month'] = date('m',mktime(0, 0, 0, $month  ,$day-1, $year));
			$day = $day-1;
		}elseif($day_type == 'next'){
			
			$data['selected_day'] = date('d',mktime(0, 0, 0, $month  ,$day+1, $year));
		    $data['selected_year']= date('Y',mktime(0, 0, 0, $month  ,$day+1, $year));
			$data['selected_month'] = date('m',mktime(0, 0, 0, $month  ,$day+1, $year));
			$day = $day+1;
		}
		else{
			
			$data['selected_day'] = $day;
			$data['selected_year']= $year;
			$data['selected_month'] = $month;
		}
			
		//exit;
		$selected_date = getShortDate($data['selected_year'].'/'.$data['selected_month'].'/'.$data['selected_day'],8);
		
		$display_date = getShortDate($data['selected_year'].'/'.$data['selected_month'].'/'.$data['selected_day'], 9);
		
		$CAL_VIEW_FILE = "logged/organize/ajax_left_cal.phtml";
		$cal_html =  $this->load->view( $CAL_VIEW_FILE , $data, true); 
		
		ob_start();
		$this->timeListingData('00:00' ,60,$day, $month, $year);
		$content = ob_get_contents();
		$content_obj = json_decode($content);
		$html = $content_obj->html; 
		ob_end_clean();
	
			
		
		echo json_encode(array('html'=>$html,'cal_html'=>$cal_html, 'selected_date'=>$selected_date, 
		'display_date'=> $display_date));
	   
   }
	
	
 function timeListingData($start_time='00:00' ,$i_gap = 15, $c_day, $month, $year)
 {
	  $s_select = '';
	  $day = date('Y/m/d');

	  $startTime = date(strtotime($day.'00:00'));
	  $endTime = date(strtotime($day."23:45"));
	  
	  $timeDiff = round(($endTime - $startTime)/60/60);
	  
	  $startHour = date("G", $startTime);
	  $endHour = $startHour + $timeDiff; 
	  
	  $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
	  //$todo_list = $this->organizer_todo_model->get_by_user_id($i_profile_id, $s_where);
	  
	  for ($i=$startHour; $i < $endHour; $i++)
	  {
		   for ($j = 0; $j <= 45; $j+=$i_gap)
			  {
				  	  $s_data_content = '';
					  // $s_option ='';
				      $time = str_pad($i, 2, '0', STR_PAD_LEFT).":".str_pad($j, 2, '0', STR_PAD_LEFT);
				      $time_val = str_pad($i, 2, '0', STR_PAD_LEFT).":".str_pad($j, 2, '0', STR_PAD_LEFT).":00";
					  
					  
					  $s_where = 'AND i_active = 1 AND YEAR(`d_date`)= '.$year.' AND MONTH(`d_date`)= '.
					  			  $month.' AND DAY(`d_date`) = '.$c_day.' AND HOUR(`t_time`) = '.getShortDateWithTime($time_val,12);
					  			   			  
					  $s_b_where = 'AND YEAR(`dt_created_date`)= '.$year.' AND MONTH(`dt_created_date`)= '.
					  			  $month.' AND DAY(`dt_created_date`) = '.$c_day.' AND HOUR(`dt_created_date`) = '.getShortDateWithTime($time_val,12);
								  
	  				  $note_list = $this->organizer_note_model->get_allNotes_by_user_id($i_profile_id, $s_where, $s_b_where);
					  
	  				  $s_todo_where = 'AND i_active = 1 AND YEAR(`d_date`)= '.$year.' AND MONTH(`d_date`)= '.
					  			  	   $month.' AND DAY(`d_date`) = '.$c_day.' AND HOUR(`t_start_time`) = '.getShortDateWithTime($time_val,12);
					  $todo_list = $this->organizer_todo_model->get_by_user_id($i_profile_id, $s_todo_where);
						

					  #$s_where = 'AND i_active = 1 AND YEAR(`d_display_date`)= '.$year.' AND MONTH(`d_display_date`)= '.
					  #			  $month.' AND DAY(`d_display_date`) = '.$c_day.
					 #			  ' AND HOUR(`t_display_start_time`) = '.getShortDateWithTime($time_val,12);
					  
					 
					  $s_where = 'AND e.i_status =  1 AND YEAR(e.dt_start_time)= '.$year.
					  			 ' AND MONTH(e.dt_start_time)= '.$month.' AND DAY(e.dt_start_time) = '.$c_day
								 .' AND HOUR(e.dt_start_time) =' .getShortDateWithTime($time_val,12);
	  				  $events_list = $this->events_model->get_my_events($i_profile_id, $s_where);
					 // pr($events_list ,1);
					  
					  $s_option .= "<li class='ul-color'>
					  				 <div class='day-time-zone'>
									 ".getShortDateWithTime($time_val,8)."</div>
									 <div class='day-data-list'>";
									 
										if(count($note_list)){
											foreach($note_list as $val){
												
												if($val['s_type'] == 'bible'){
												$s_data_content .= " <span class='day-event' >
																		<img src='".base_url()."images/add_note_sml.jpg'/>
																		".my_substr(nl2br($val['s_description']),100)."
																		</span>";
												}
												else{
													$s_data_content .= " <span class='day-event' onclick='edit_note(".$val['id'].")'>
																		<img src='".base_url()."images/add_note_sml.jpg'/>
																		".my_substr(nl2br($val['s_description']),100)."
																		</span><span class='day-event' style='border-left:0;' ><img src='".base_url()."images/icons/delete_small.png' class='del_img' onclick='show_note_delete_popup(".$val['id'].")'/></span>";
												}
											}
										}
										
										if(count($todo_list)){
											foreach($todo_list as $val){
												$s_data_content .= " <span class='day-event color-change' onclick= 'edit_list(".$val['id'].")'>
																		<img src='".base_url()."images/add_list_sml.jpg'/>
																		".my_substr(nl2br($val['s_description']),100)."
																		</span><span class='day-event' style='border-left:0;'><img src='".base_url()."images/icons/delete_small.png' class='del_img' onclick='show_list_delete_popup(".$val['id'].")'/></span>";
											}
										}
										
										if(count($events_list)){
											foreach($events_list as $val){
												$s_data_content .= " <span class='day-event'>
																		<img src='".base_url()."images/add_event_sml.png'/>
																		
																		".nl2br($val['s_title'])."
																		</span>";
											}
										}
                                    
                           $s_option .=     $s_data_content .  "</div>
					  						<li>"; 
				 
			  }
	  
	  }
	 
	 //return $s_option;		
	 echo json_encode(array('html'=>$s_option));
 }
 
 
 
 
  public function organizer_week_view() 
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
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery.dd.js',
                                        'js/stepcarousel.js',
										'js/frontend/logged/organizer/organizer.js',*/
										'js/production/tweet_utilities.js',
										'js/frontend/logged/organizer/organizer.js',
//										'js/tab.js',
										'js/jquery.jcarousel.min.js',
										'js/jquery-ui-1.8.2.custom.min.js',
										'js/jquery.mCustomScrollbar.min.js',
//										'js/jquery.ui.datepicker.js',
										'js/production/organizer.js'
										
                                        ));
                                        
            parent::_add_css_arr( array('css/date/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css') );
										  
            $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
         //	$data['right_panel']['calendar_data'] = parent::show_event_calendar($i_profile_id, date('Y'), date('m'), '', true);
			$data['right_panel']['uid'] = $i_profile_id; 
			
			$data['selected_year'] = $year = date('Y');
			$data['selected_month'] = $month = date('m');
			$data['selected_day'] = $day = date('d');
			$data['selected_date'] = date('Y').'/'.date('m').'/'.date('d');
			$data['selected_week'] = date("Y-m-d D ",strtotime("+1 week"));
		//	pr($data['selected_week'],1);
			
			$this->session->set_userdata('day',$day );
			$this->session->set_userdata('year',$year);
			$this->session->set_userdata('month',$month);
			$this->session->set_userdata('selected_date',$data['selected_date']);
			
			
			
			ob_start();
			$this->weekListingData('00:00' ,60,$day, $month, $year);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['result_content'] = $content_obj->html; 
			$data['weekNumber'] = $content_obj->weekNumber; 
			
			ob_end_clean();
			
            # view file...
            $VIEW = "logged/organize/organizer-week-view.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }   
      
	function weekListingData($start_time='00:00' ,$i_gap = 15, $c_day, $month, $year, $weekNumber = 0)
	{
	  $s_select = '';
	  $day = date('Y-m-d');
	  
	  $startTime = date(strtotime($day.'00:00'));
	  $endTime = date(strtotime($day."23:45"));
	  
	  $timeDiff = round(($endTime - $startTime)/60/60);
	  
	  $startHour = date("G", $startTime);
	  $endHour = $startHour + $timeDiff; 
	  
	  $i_profile_id = intval(decrypt($this->session->userdata('user_id')));

	  $s_option .= '<div class="cal_heading">';
	                  
	  
	  
	  if($weekNumber == 0) {				
	   
		$date = date('Y-m-d',mktime(0, 0, 0, $month ,$c_day, $year));
		$ts = strtotime($date);
		// find the year (ISO-8601 year number) and the current week
		$year = date('o', $ts);
		$week = date('W', $ts);
	  }
	  else{
	  	$year;
		$week = $weekNumber;
	  }
	   		  
	  for($i = 0; $i <= 6; $i++) {
		
		
		// timestamp from ISO week date format
		$ts = strtotime($year.'W'.$week.$i);
		$week_date = date("d", $ts) . "\n";
		$goto_date =  "'".date('Y-m-d',$ts)."'";
		
		$s_option .= ' <div  onclick="gotoDay('.$goto_date.')" style="cursor:pointer;"><strong>'.date("d", $ts).'</strong><span>'.date("D", $ts).'</span></div>';
	  }
	  
	  $s_option .=  '   </div>
	                    <ul class="day-list">';					
						
	  for ($i=$startHour; $i < $endHour; $i++)
	  {
		   for ($j = 0; $j <= 45; $j+=$i_gap)
			  {
					  $s_data_content = '';
					  // $s_option ='';
					  $time = str_pad($i, 2, '0', STR_PAD_LEFT).":".str_pad($j, 2, '0', STR_PAD_LEFT);
					  $time_val = str_pad($i, 2, '0', STR_PAD_LEFT).":".str_pad($j, 2, '0', STR_PAD_LEFT).":00";
					  
					   # fetch week days 
						
						
						 
						 $s_option .= "<li>
										<div class='day-time-zone'>
										   ".getShortDateWithTime($time_val,8)."</div><div class='day-data-list'>
										<div class='cal_body'>
										";
						// print week for the current date
						for($k = 0; $k <= 6; $k++) {
							// timestamp from ISO week date format
							$ts = strtotime($year.'W'.$week.$k);
							//$week_date = date("d", $ts) . "\n";
							
							  $val_date =  '"'.date('Y-m-d',$ts).'"';
							  $caldate = '"'.date('d/m/Y', $ts).'"';
							  
							  
							  $goto_date =  "'".date('Y-m-d',$ts)."'";
							  
							 
							
							  $s_where = 'AND i_active = 1 AND YEAR(`d_date`)= '.date("Y", $ts).' AND MONTH(`d_date`)= '.
								  		 date("m", $ts).' AND DAY(`d_date`) = '.date("d", $ts).' AND HOUR(`t_time`) = '.
								         getShortDateWithTime($time_val,12);
								
							  $s_b_where = 'AND YEAR(`dt_created_date`)= '.date("Y", $ts).' AND MONTH(`dt_created_date`)= '.
				  			  date("m", $ts).' AND DAY(`dt_created_date`) = '.date("d", $ts).' AND HOUR(`dt_created_date`) = '.getShortDateWithTime($time_val,12);


							  $note_list = $this->organizer_note_model->get_allNotes_by_user_id($i_profile_id, $s_where, $s_b_where);
							 
							  #$s_where = 'AND i_active = 1 AND YEAR(`d_display_date`)= '.date("Y", $ts).' AND MONTH(`d_display_date`)= '.
								#		  date("m", $ts).' AND DAY(`d_display_date`) = '.date("d", $ts).
								#		  ' AND HOUR(`t_start_time`) = '.getShortDateWithTime($time_val,12);

							  $s_where = 'AND i_active = 1 AND YEAR(`d_date`)= '.date("Y", $ts).' AND MONTH(`d_date`)= '.
								  		 date("m", $ts).' AND DAY(`d_date`) = '.date("d", $ts).' AND HOUR(`t_start_time`) = '.
								         getShortDateWithTime($time_val,12);

							  $todo_list = $this->organizer_todo_model->get_by_user_id($i_profile_id, $s_where);
						//pr($note_list); pr($todo_list);
							 
							//pr($events_list);
							 $s_where = 'AND e.i_status =  1 AND YEAR(e.dt_start_time)= '.date("Y", $ts).
				  			 			' AND MONTH(e.dt_start_time)= '.date("m", $ts).' AND DAY(e.dt_start_time) = '.date("d", $ts)
							 			.' AND HOUR(e.dt_start_time) =' .getShortDateWithTime($time_val,12);
					  			 $events_list = $this->events_model->get_my_events($i_profile_id, $s_where);
							
							 $note_img = (count($note_list) != 0)?"<img src='images/add_note_sml.jpg' />":'';
							 $list_img = (count($todo_list) != 0)?"<img src='images/add_list_sml.jpg'/>":''	;	
							 $event_img = (count($events_list) != 0)?"<img src='images/add_event_sml.png'/>":'';						 
							 $s_data_content .= "<div class='nw-spot-edit'  >
							 						<span class='spot-edit-box'>
													<span class='bottom-arrow'></span>
													<span class='spot-edit-container'>
														<ul class='main-buttons'>
															<li>
																<a href='javascript:void(0);' onclick='showAddNote(".$val_date.",".$caldate.")'>Add Note</a>
															</li>
															<li>
																<a href='javascript:void(0);' onclick='showAddList(".$val_date.",".$caldate.")'>Add ToDo List</a>
															</li>
														</ul>
													</span>
													</span>
													
													<span class='date' onclick='gotoDay(".$val_date.")'>
													
													".$note_img.$list_img.$event_img."
							 						</span>
													 <a href='javascript:void(0);' class='add-note-icon'>
														<img src='images/calender-plus.png' />
													</a>
													<br class='clear'/>
													
													<script type='text/javascript'>
													$(function() {
														$('.add-note-icon').mouseenter(function(){
															 $(this).parent().find('.spot-edit-box').css('display','block');
														});
														$('.spot-edit-box').mouseenter(function(){
														   $(this).css('display','block');
														});
														$('.spot-edit-box').mouseleave(function(){
														   $(this).css('display','none');
														});
														$('.add-note-icon').mouseleave(function(){
															$(this).parent().find('.spot-edit-box').css('display','none');
														});
													});
												</script>
												 </div> ";
							 
							 
						}
						$s_option .=     $s_data_content .  " </div>
																</div>
																</li>"; 
				 
			  }
	}
	 $s_option .= ' </ul>
	 				';		
	 echo json_encode(array('html'=>$s_option,'weekNumber'=>$week));
	}
	
	
	 public function week_slider_ajax_list($year,$month,$day,$day_type,$weekNumber){
	   
		$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
		
		$weekNumber = ($weekNumber < 10 )?str_pad($weekNumber, 2, '0', STR_PAD_LEFT):$weekNumber;
		
		$day_num = 0;
		//$display_date = getShortDate($year.'/'.$month.'/'.$day, 9);
		$selected_date = date('Y-m-d',strtotime($year."W".$weekNumber.$day_num));
		$display_start_date = date('F  d, Y', strtotime($year."W".$weekNumber.$day_num));
		$display_end_date = date('F  d, Y', strtotime($year."W".$weekNumber.($day_num+6))); 
		
		//echo $display_start_date.' - '.$display_end_date.' ####'; exit;
		
		ob_start();
		$this->weekListingData('00:00' ,60,$day, $month, $year,$weekNumber);
		$content = ob_get_contents();
		$content_obj = json_decode($content);
		$html = $content_obj->html; 
		$weekNumber = $content_obj->weekNumber; 
		ob_end_clean();
		
		
		$data['selected_month'] = $month;
		$data['selected_day'] = $day;
		$data['selected_year'] = $year;
		$data['selected_weekNumber'] = $weekNumber;
		$CAL_VIEW_FILE = "logged/organize/ajax_week_cal.phtml";
		$cal_html =  $this->load->view( $CAL_VIEW_FILE , $data, true); 
			
		
		echo json_encode(array('html'=>$html,'cal_html'=>$cal_html, 'selected_date'=>$selected_date, 
		'display_date'=> $display_start_date.' - '.$display_end_date ,  'weekNumber'=>$weekNumber));
	   
   }
	
  	
	
	public function organizer_month_view() 
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
        
            
              parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery.dd.js',
                                        'js/stepcarousel.js',
										'js/frontend/logged/organizer/organizer.js',*/
										'js/production/tweet_utilities.js',
//										'js/tab.js',
										'js/jquery.jcarousel.min.js',
//										'js/jquery-ui-1.8.2.custom.min.js',
										'js/jquery.mCustomScrollbar.min.js',
										'js/production/organizer.js'
										
                                        ));
                                        
            parent::_add_css_arr( array('css/date/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css') );
										  
            $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
			$data['right_panel']['uid'] = $i_profile_id; 
			
			
			
			if($_POST){
				$sel_date = $_POST['goto_date'];
				$current_date_arr = explode('-',$sel_date);
				
				$data['selected_year'] = $year = $current_date_arr[0] ;
				$data['selected_month'] = $month = $current_date_arr[1];
				$data['selected_day'] = $day  = $current_date_arr[2];
				$data['selected_date'] = $year.'/'.$month.'/'.$day;
				
			}
			else{
				
				$data['selected_year'] = $year = date('Y');
				$data['selected_month'] = $month = date('m');
				$data['selected_day'] = $day = date('d');
				$data['selected_date'] = date('Y').'/'.date('m').'/'.date('d');
			}
			$data['right_panel']['calendar_data'] = parent::show_event_calendar($i_profile_id, $year, $month, '', true);
			$data['right_panel']['uid'] = $i_profile_id; 
				
			
			$this->session->set_userdata('day',$day );
			$this->session->set_userdata('year',$year);
			$this->session->set_userdata('month',$month);
			$this->session->set_userdata('selected_date',$data['selected_date']);
			
			/*ob_start();
			$this->ajax_list();
			$data['result_content'] = ob_get_contents();
			ob_end_clean(); */
			
            # view file...
            $VIEW = "logged/organize/organize-month-view.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

   }  
   
   
  	
	
	
   
   
	
}   // end of controller...

