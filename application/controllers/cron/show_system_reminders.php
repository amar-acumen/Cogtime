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

class Show_system_reminders extends Base_controller {

	public function __construct() {
		try
		{
			parent::__construct();
			$this->load->model('users_model');
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


    public function index() {
        
		##### fetch the todolist of expire after two days ########
									   
		//mail('aradhana.online19@gmail.com','Re: Cron Job Test Script',phpversion());		
		# todays date +2  and is active.
		# send mail , im , show popup method in base controller.
		# check if reminder expired.
		$popup_array = array();
		$user_array= array();
		
		$s_user_where = "WHERE i_status = 1 
						 AND i_isdeleted = 1";
		$user_array = $this->users_model->user_list();
		echo date('H:i:s',time());
		echo '<br/>.'. date('Y-m-d');
		
		$today    =  strtotime(date("Y-m-d"));
		$tommorow =  strtotime('+2 days');
		
		if(count($user_array)){
			
			foreach($user_array as $key=> $user_val){
				  $i_profile_id = $user_val['id'];
				  
				  ## fetch user details ###
				  $user_profile_info = $this->users_model->fetch_this($i_profile_id);
				  //pr($user_profile_info);
				  
				  $today    =  strtotime(date("Y-m-d"));
				  $tommorow =  strtotime('+2 days');
				  
				  $s_where_today_after2days = 'AND i_active = 1  
											   AND i_reminder_status = 1 
											   AND `d_date` BETWEEN "'.date('Y-m-d', $today).'" AND "'.date('Y-m-d', $tommorow).'"
											   AND `i_notification_added` = 1 ';
											   
				  $todo_list_between_today_after2days = $this->organizer_todo_model->get_by_user_id($i_profile_id, 
				  
																								  $s_where_today_after2days);
				 //echo $this->db->last_query();
				  
				  # added for event
					$where = 'AND i_status = 1 
							  AND i_reminder_status = 1 
							  AND i_reminder_shown = 1 
							  AND DATE_FORMAT(`dt_start_time`,"%Y-%m-%d")  BETWEEN "'.date('Y-m-d', $today).'" AND "'.date('Y-m-d', $tommorow).'" AND `i_notification_added` = 1 ';
							  
					$event_arr  = $this->events_model->get_by_user_id($i_profile_id, $where);
					//echo $this->db->last_query();
						
					#pr($event_arr);			
					#pr($todo_list_between_today_after2days);															  
				$reminder_array = array();	
				$reminder_array  = array_merge($todo_list_between_today_after2days,$event_arr);		
				 // echo $this->db->last_query();
				  //pr($reminder_array,1);
				  ## send mail for $todo_list_between_today_after2days ##
				  
				  if(count($reminder_array)){
					  
					  foreach($reminder_array as $key=> $list_val){
						  
						  ### insert into reminder table ONLY NEW REMINDERS ###
						  $reminder_info = array();
						  
						  if($list_val['event_type'] == ' [ D ] '){
							  $reminder_info['i_user_id'] = $i_profile_id;
							  $reminder_info['i_reminder_id'] = $list_val['id'];
							  $reminder_info['s_reminder_type'] = 'to-do-list';
							  $reminder_info['d_start_date'] = $list_val['d_date'];
							  $reminder_info['t_start_time'] = $list_val['t_start_time'];
							  $reminder_info['t_end_time'] = $list_val['t_end_time'];
							  $reminder_info['t_time_last_mail_sent'] = '00:00:00';
							  $reminder_info['dt_created_on'] = get_db_datetime();
						  }
						  else{
							  $reminder_info['i_user_id'] = $i_profile_id;
							  $reminder_info['i_reminder_id'] = $list_val['id'];
							  $reminder_info['s_reminder_type'] = 'event';
							  $reminder_info['d_start_date'] = $list_val['dt_start_time'];
							  $reminder_info['t_start_time'] = $list_val['t_start_time'];
							  $reminder_info['t_end_time'] = $list_val['t_end_time'];
							  $reminder_info['t_time_last_mail_sent'] = '00:00:00';
							  $reminder_info['dt_created_on'] = get_db_datetime();
						  }
						  
							  $ret_id 	=	$this->system_reminder_model->insert($reminder_info);
							
						  ### insert into reminder table ONLY NEW REMINDERS###
						  
						  ### UPDATE TO DO LIST  TABLE #######
						  if($list_val['event_type']== ' [ D ] '){
							$reminder_type = 'to-do-list';
							$info['i_notification_added'] = 2 ;
							$this->organizer_todo_model->update($info,$list_val['id']);
						  }
						  ### UPDATE TO DO LIST  TABLE #######
						  
						  ### UPDATE event  TABLE #######
						  if($list_val['event_type']== ' [ E ] '){
							$reminder_type = 'event';
							$info['i_notification_added'] = 2 ;
							$this->events_model->update($info,$list_val['id']);
						  }
						  ### UPDATE event  TABLE #######
					  }
				  }
				  
				  
				  ### fetch from reminder table ###
				  $now_time =  date('H:i:s',time());
				  $now_date =  date('Y-m-d');
				  $s_where_reminder = 'AND i_sent_mail_count < 3  
									   AND i_reminder_status = 1
									   AND d_start_date >= "'.$now_date.'"';
											   
				  $reminder_to_be_shown_arr = $this->system_reminder_model->get_by_user_id($i_profile_id, $s_where_reminder);
				  
			 //pr($reminder_to_be_shown_arr);
				  $this->load->helper('html');
					$this->load->library('email');
					 $email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
				$this->email->initialize($email_setting);
				  $this->load->model('mail_contents_model');
				  
				  
				  
				  //pr($reminder_to_be_shown_arr);
				  if(count($reminder_to_be_shown_arr)){
					  
					  foreach($reminder_to_be_shown_arr as $key=> $val){
						  
						  ## put checking if its first mail  or after six hours ####
		  
						  $now_time =  date('H:i:s',time());
						  $time_after_six_hours     = date('H:i:s', strtotime( $val['t_time_last_mail_sent']. " +6 hours"));
						  
						  if(($now_time == $time_after_six_hours || $val['t_time_last_mail_sent'] == '00:00:00') 
													  && $val['i_sent_mail_count'] == 0){
								  
								  
								  ## sending mails  ### 				
								  $mail_info = $this->mail_contents_model->get_by_name("todo_reminder_mail");
								  $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
								  
								  ### checking remider type
								  if($val['s_reminder_type'] == 'to-do-list'){
								  	$s_description = get_reminder_todo_text($val['i_reminder_id']);
								  	$start_time = get_reminder_todo_start_time($val['i_reminder_id']);
								  	$end_time = get_reminder_todo_end_time($val['i_reminder_id']);
								  }
								  else{
									$s_description = get_reminder_event_text($val['i_reminder_id']);
								  	$start_time = get_reminder_event_start_time($val['i_reminder_id']);
								  	$end_time = get_reminder_event_end_time($val['i_reminder_id']);
								  }
								  
								  
								  $n_due_date = strtotime($val['d_start_date'].' '.$val['t_start_time']);
								  $n_today_date = strtotime(date('Y-m-d H:i:s'));
								  $due_time = date_difference_due_time($n_due_date  ,$n_today_date);
								  
								  $subject = htmlspecialchars_decode( htmlspecialchars($mail_info['subject'], ENT_QUOTES, 'utf-8'), ENT_QUOTES);
								  //$subject = htmlspecialchars($mail_info['subject'], ENT_QUOTES, 'utf-8');
								  $subject = sprintf3( $subject, array('short_desc'=>substrws(get_unformatted_string_edit($s_description),30))); 
								  
								  $body = sprintf3( $body, array('email'=>$user_profile_info["s_email"],
																 'member_name'=>$user_profile_info["s_profile_name"],
																 'short_desc'=>my_substr($s_description,25),
																 's_desc'=>$s_description , 
																 'due_time'=>$due_time,
																 'start_date'=>$val['d_start_date'],
																 'start_time'=>$start_time, 
																 'end_time'=>$end_time
																 ));
								 
								  //echo $body;
																 
								  $arr['subject'] 	= $subject;
								  $arr['to']         = $user_profile_info["s_email"];
								 // $arr['bcc']        = 'aradhana.online19@gmail.com';
								  $arr['from_email'] = 'no-reply@cogtime.com';
								  $arr['from_name'] = 'admin@cogtime.com';
								  $arr['message'] = $body;
								 //pr($arr); exit;
								  $this->email->from( $arr['from_email'], $arr['from_name']);
									#dump($arr);
									$this->email->subject($arr['subject']);
											
									$this->email->to($arr['to']);
									$this->email->bcc($arr['bcc']);
									$this->email->message("$body");
											//send_mail($arr);
									$this->email->send();
								  //send_mail($arr);
								  
								$message_id = parent::media_notifications_message($i_profile_id, $i_profile_id, 'todo_reminder_im_mail', $val['i_reminder_id']);
								  ### show popup ###
								  $pop_content_array = array();
								  
								  $pop_content_array['content'] = my_substr($s_description,200);
								  $pop_content_array['organizer_id'] = $val['i_reminder_id'];
								  $pop_content_array['t_start_time'] = $val['t_start_time'];
								  $pop_content_array['d_start_date'] = $val['d_start_date'];
								  
								  #echo '<br/> due- date : '.($val['d_start_date'].' '.$val['t_start_time']);
								  #echo ' <br/> today_date : '.(date('Y-m-d H:i:s'));
								  
								  $due_date = strtotime($val['d_start_date'].' '.$val['t_start_time']);
								  $today_date = strtotime(date('Y-m-d H:i:s'));
								  
								  $pop_content_array['due_time'] = date_difference_due_time($due_date  ,$today_date);
								  array_push($popup_array,  $pop_content_array);
								  
								  
								  ### end show popup ###
								  
								  # UPDATE REMINDER TABLE  #
								  $reminder_update_info = array();
								  
								  $reminder_update_info['i_sent_mail_count'] = $this->system_reminder_model->get_last_count($val['id'])+1;
								  if( $val['t_time_last_mail_sent'] == '00:00:00' ){
									  $time_after_six_hours     = date('H:i:s',time(). " +6 hours");
								      $reminder_update_info['t_time_last_mail_sent'] = $time_after_six_hours;
								  }
								  else{
									  
									  $last_time =  $this->system_reminder_model->get_last_mail_sent_time($val['id']);
									  $new_time_after_six_hours     = date('H:i:s',strtotime($last_time). " +6 hours");
								      $reminder_update_info['t_time_last_mail_sent'] = $new_time_after_six_hours;
								  }
								  
								  $reminder_update_info['dt_updated_on'] = get_db_datetime();
								  $this->system_reminder_model->update($reminder_update_info, $val['id']);
									  
								  # UPDATE REMINDER TABLE  #
								  
								  
								  ### end sending mails
						  }## end if
						  // parent::show_system_reminder_popup('true',$popup_array);
					  }
					  ## showing popup
					             /* $anrr['subject'] 	= 'Hello world';
								  $anrr['to']         = 'aradhana.online19@gmail.com';
								  $anrr['from_email'] = 'no-reply@cogtime.com';
								  $anrr['from_name'] = 'admin@cogtime.com';
								  $anrr['message'] = 'hiiii'.count($pop_content_array);
								  send_mail($anrr);*/
					 
					  ## showing popup
				  }
			  }

		}
		
		//pr($popup_array);	
    }
    
  
}

