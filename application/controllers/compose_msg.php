<?php
/*********
* Author: 
* Date  : 
* Modified By: 
* Modified Date:
* 
*/
include(APPPATH.'controllers/base_controller.php');

class Compose_msg extends Base_controller 
{
    
	private $pagination_per_page =  20 ;
	
	public function __construct()
     {
       try
         {
         
            parent::__construct();
			
			parent::check_login(TRUE,'',array('1')); // put this code on those pages which are not accessable by non logged in user

            $this->load->model('users_model');
			$this->load->model('data_messages_model');
			$this->load->model('contacts_model');
			$this->load->model('my_prayer_partner_model');
			$this->load->model('netpals_model');
			$this->load->model('user_notifications_model');
			$this->load->model('user_alert_model');

         }        
        catch(Exception $err_obj)
         {
            
         }        
    }
    
    public function index() 
    {
    	try
         {
		        
			$data = $this->data;      
           	parent::_set_title('::: COGTIME Xtian network :::');
			parent::_set_meta_desc('');
			parent::_set_meta_keywords('');
		
			
			parent::_add_js_arr( array( //'js/ddsmoothmenu.js',
										//'js/switch.js','js/animate-collapse.js',
										//'js/lightbox.js',
                                       // 'js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
										//'js/stepcarousel.js',
										//'js/frontend/logged/my_friends.js'
										'js/production/tweet_utilities.js',
										'js/frontend/logged/message_box/my_message.js'
										));
										
			parent::_add_css_arr( array(
                //'css/jquery-ui-1.8.2.custom.css',
				//						'css/dd.css'
            ) );
			
            
            /////////////////////////////////////////////
             
			$i_user_id = intval(decrypt($this->session->userdata('user_id')));	
			$i_profile_id = $i_user_id;
			
			$this->load->model('users_model');
			$arr_profile_info = $this->users_model->fetch_this($i_user_id);
            #dump( $arr_profile_info );
			$data['arr_profile_info'] = $arr_profile_info; 
			
			
			## FETCHING FRIENDS ###
			
			$WHERE = " WHERE 
						1
						AND c.s_status = 'accepted' 
						AND u.i_status=1 
						AND
						((c.i_requester_id = '".$i_profile_id."' AND u.id=c.i_accepter_id ) 
						OR (c.i_accepter_id = '".$i_profile_id."' AND u.id=c.i_requester_id ))  GROUP BY u.id "	;	
			  
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
			$PRAYERPARTNER_WHERE = " WHERE 
										1
										AND c.s_status = 'accepted' 
										AND u.i_status=1 
										AND
										((c.i_requester_id = '".$i_profile_id."' AND u.id=c.i_accepter_id ) 
										OR (c.i_accepter_id = '".$i_profile_id."' AND u.id=c.i_requester_id ))
										AND u.id NOT IN (".$exclude_id_csv.") 
										GROUP BY u.id "	;	
			  
			$ORDER_BY = "u.s_first_name ASC";	
				  
			$prayer_partners = $this->my_prayer_partner_model->fetch_multi_online_friends($PRAYERPARTNER_WHERE,null,null,$ORDER_BY);
			//pr($prayer_partners);
			#echo $this->db->last_query();
			
			$exclude_id_PP_arr = $this->my_prayer_partner_model->get_prayerPartnerId_by_user_id($i_profile_id);
			if(count($exclude_id_PP_arr)){
					$exclude_id_csv .= ', ';
					$exclude_id_csv .= implode(', ',$exclude_id_PP_arr);
			}
			
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
			
			$total_contact_arr = array();
			
			
			$contact_arr = array_merge($contacts,$prayer_partners);
			$total_contact_arr =  array_merge($contact_arr,$netpals);
			array_sort_by_column($total_contact_arr, 's_displayname');

			$data['contacts'] = $total_contact_arr;//$contacts;
			//pr($contact_arr);
            
			//////////////////////////////////////////
			
			$data['page_view_type'] = 'myaccount'; 
			$VIEW = "logged/message_box/compose-msg.phtml";
			parent::_render($data, $VIEW);
		 
		 }        
        catch(Exception $err_obj)
         {
            
         }    
    }


	public function send_message() 
	{

		try
        {
			
	        $arr_messages = array();
					
					# error message trapping...
					if( trim($this->input->post('recipients'))=='') 
					{
							$arr_messages['send_recepients'] = "* Required Field.";
					}
										
					if( trim($this->input->post('txt_send_message'))=='') 
					{
							$arr_messages['send_message'] = "* Required Field.";
					}
					
					
		
				   //pr($arr_messages);
					if( count($arr_messages)==0 ) 
						{
		 					
								$this->load->model('users_model');
								
						
								$recipients_ids =  $this->input->post('recipients');
								
								foreach( explode(',',$recipients_ids) as $recipient_id ) 
								 {
									//$recipient_id = ($recipient_id);
									parent::send_message(decrypt($this->session->userdata('user_id')), $recipient_id, 'normal', $this->input->post('txt_send_subject'), $this->input->post('txt_send_message'));
									
									
									## check if opted for this notification or not ##
									$notificaion_opt = $this->user_alert_model->check_option_user_id($recipient_id);	
									
									//pr($notificaion_opt);
									
									## insert noifications ####
									if($notificaion_opt['e_message_received'] == 'Y'){
										$notification_arr['i_requester_id'] = decrypt($this->session->userdata('user_id'));
										$notification_arr['i_accepter_id'] = $recipient_id;
										$notification_arr['s_type'] = 'message';
										$notification_arr['dt_created_on'] = get_db_datetime();
										
										$ret = $this->user_notifications_model->insert($notification_arr);	
									}
									### end  ###
									
									
								}
								
								echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'Message sent') );
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
	
   public function reply_message($friend_user_id, $message_id) 
    {
    	try
         {
		        
			$data = $this->data;      
           	parent::_set_title('::: COGTIME Xtian network :::');
			parent::_set_meta_desc('');
			parent::_set_meta_keywords('');
		
			
			parent::_add_js_arr( array(
//                                'js/ddsmoothmenu.js',
//										'js/switch.js','js/animate-collapse.js',
//										'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
//										'js/stepcarousel.js',
//										//'js/frontend/logged/my_friends.js'
										'js/production/tweet_utilities.js',
										'js/frontend/logged/message_box/my_message.js'
										));
//
//			parent::_add_css_arr( array(/*'css/jquery-ui-1.8.2.custom.css',
//										'css/dd.css'*/) );
			
            
            /////////////////////////////////////////////
             
			$i_user_id = intval(decrypt($this->session->userdata('user_id')));	
			$i_profile_id = $i_user_id;
			
			$this->load->model('users_model');
			$arr_profile_info = $this->users_model->fetch_this($i_user_id);
			$data['arr_profile_info'] = $arr_profile_info; 
			
			$contact_arr = $this->users_model->fetch_this($friend_user_id);
			$data['contacts'] = $contact_arr;//$contacts;
			
			$data['message_detail'] = $this->data_messages_model->get_message_by_id($message_id);
		   //pr($message_detail);
            
			//////////////////////////////////////////
			
			$data['page_view_type'] = 'myaccount'; 
			$VIEW = "logged/message_box/reply-msg.phtml";
			parent::_render($data, $VIEW);
		 
		 }        
        catch(Exception $err_obj)
         {
            
         }    
    }
	
	 public function suggest_friends() 
     {
    	    $letter = $_REQUEST['q'];
             
			$i_user_id = intval(decrypt($this->session->userdata('user_id')));	
			$i_profile_id = $i_user_id;
			
			$this->load->model('users_model');
			$arr_profile_info = $this->users_model->fetch_this($i_user_id);
            #dump( $arr_profile_info );
			$data['arr_profile_info'] = $arr_profile_info; 
			
			
			## FETCHING FRIENDS ###
			
			$WHERE = " WHERE 
						1
						AND c.s_status = 'accepted' 
						AND u.i_status=1 
						AND
						((c.i_requester_id = '".$i_profile_id."' AND u.id=c.i_accepter_id ) 
						OR (c.i_accepter_id = '".$i_profile_id."' AND u.id=c.i_requester_id ))
						AND CONCAT(u.s_first_name,' ',u.s_last_name) LIKE '%".$letter."%'
						GROUP BY u.id "	;	
			  
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
			$PRAYERPARTNER_WHERE = " WHERE 
										1
										AND c.s_status = 'accepted' 
										AND u.i_status=1 
										AND
										((c.i_requester_id = '".$i_profile_id."' AND u.id=c.i_accepter_id ) 
										OR (c.i_accepter_id = '".$i_profile_id."' AND u.id=c.i_requester_id ))
										AND u.id NOT IN (".$exclude_id_csv.") 
										AND CONCAT(u.s_first_name,' ',u.s_last_name) LIKE '%".$letter."%'
										GROUP BY u.id "	;	
			  
			$ORDER_BY = "u.s_first_name ASC";	
				  
			$prayer_partners = $this->my_prayer_partner_model->fetch_multi_online_friends($PRAYERPARTNER_WHERE,null,null,$ORDER_BY);
			//pr($prayer_partners);
			#echo $this->db->last_query();
			
			$exclude_id_PP_arr = $this->my_prayer_partner_model->get_prayerPartnerId_by_user_id($i_profile_id);
			if(count($exclude_id_PP_arr)){
					$exclude_id_csv .= ', ';
					$exclude_id_csv .= implode(', ',$exclude_id_PP_arr);
			}
			
			//echo $exclude_id_csv;
			
			## FETCHING NETPALS ###
			$total_where =  " WHERE 1
                                    AND u.i_status=1 
                                    AND ((c.i_requester_id = ".$i_profile_id." AND u.id=c.i_accepter_id) 
                                        OR 
                                    (c.i_accepter_id=".$i_profile_id." AND u.id=c.i_requester_id))
                                    AND c.s_status='accepted' 
									AND u.id NOT IN (".$exclude_id_csv.")
									AND CONCAT(u.s_first_name,' ',u.s_last_name) LIKE '%".$letter."%'
									GROUP BY u.id " ;
			
			$ORDER_BY = "u.s_first_name ASC";
			
             
            $netpals = $this->netpals_model->fetch_multi_online_netpals($total_where, null,null,$ORDER_BY);	
			
			$total_contact_arr = array();
			
			
			$contact_arr = array_merge($contacts,$prayer_partners);
			$total_contact_arr =  array_merge($contact_arr,$netpals);
			array_sort_by_column($total_contact_arr, 's_displayname');

			$data['contacts'] = $total_contact_arr;
			$sugg = array();
			if(count($total_contact_arr)>0){
						
				foreach($total_contact_arr as $k=> $val)
				{	
						$thumb_html ='';
						$thumb  = get_profile_image_of_user('thumb',$val['s_profile_photo'],$val['e_gender']);	
						
						$thumb_html =  '<div class="tweeter-thumb" style="background:url('.$thumb.') no-repeat center;width:36px; height:36px; float:left;" ></div><span style="float:right;width:345px;">'.$val['s_displayname'].'</span>'."\n";
						
						$sugg[$k]['id'] = $val['user_id'];
						$sugg[$k]['label'] = $thumb_html;
						$sugg[$k]['value'] = $val['s_displayname'];
				}
				echo json_encode($sugg);
			}
			else
			{	
				$sugg[0]['id'] = '';
				$sugg[0]['label'] = 'No Result.';
				$sugg[0]['value'] = '';
				echo json_encode($sugg);
			}
				//echo "No Result.";
			exit;
			
    }
	
	
	public function suggest_frwd_friends() 
     {
    	    $letter = $_REQUEST['q'];
             
			$i_user_id = intval(decrypt($this->session->userdata('user_id')));	
			$i_profile_id = $i_user_id;
			
			$this->load->model('users_model');
			$arr_profile_info = $this->users_model->fetch_this($i_user_id);
            #dump( $arr_profile_info );
			$data['arr_profile_info'] = $arr_profile_info; 
			
			
			## FETCHING FRIENDS ###
			
			$WHERE = " WHERE 
						1
						AND c.s_status = 'accepted' 
						AND u.i_status=1 
						AND
						((c.i_requester_id = '".$i_profile_id."' AND u.id=c.i_accepter_id ) 
						OR (c.i_accepter_id = '".$i_profile_id."' AND u.id=c.i_requester_id ))
						AND CONCAT(u.s_first_name,' ',u.s_last_name) LIKE '%".$letter."%'
						GROUP BY u.id "	;	
			  
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
			$PRAYERPARTNER_WHERE = " WHERE 
										1
										AND c.s_status = 'accepted' 
										AND u.i_status=1 
										AND
										((c.i_requester_id = '".$i_profile_id."' AND u.id=c.i_accepter_id ) 
										OR (c.i_accepter_id = '".$i_profile_id."' AND u.id=c.i_requester_id ))
										AND u.id NOT IN (".$exclude_id_csv.") 
										AND CONCAT(u.s_first_name,' ',u.s_last_name) LIKE '%".$letter."%'
										GROUP BY u.id "	;	
			  
			$ORDER_BY = "u.s_first_name ASC";	
				  
			$prayer_partners = $this->my_prayer_partner_model->fetch_multi_online_friends($PRAYERPARTNER_WHERE,null,null,$ORDER_BY);
			//pr($prayer_partners);
			#echo $this->db->last_query();
			
			$exclude_id_PP_arr = $this->my_prayer_partner_model->get_prayerPartnerId_by_user_id($i_profile_id);
			if(count($exclude_id_PP_arr)){
					$exclude_id_csv .= ', ';
					$exclude_id_csv .= implode(', ',$exclude_id_PP_arr);
			}
			
			//echo $exclude_id_csv;
			
			## FETCHING NETPALS ###
			$total_where =  " WHERE 1
                                    AND u.i_status=1 
                                    AND ((c.i_requester_id = ".$i_profile_id." AND u.id=c.i_accepter_id) 
                                        OR 
                                    (c.i_accepter_id=".$i_profile_id." AND u.id=c.i_requester_id))
                                    AND c.s_status='accepted' 
									AND u.id NOT IN (".$exclude_id_csv.")
									AND CONCAT(u.s_first_name,' ',u.s_last_name) LIKE '%".$letter."%'
									GROUP BY u.id " ;
			
			$ORDER_BY = "u.s_first_name ASC";
			
             
            $netpals = $this->netpals_model->fetch_multi_online_netpals($total_where, null,null,$ORDER_BY);	
			
			$total_contact_arr = array();
			
			
			$contact_arr = array_merge($contacts,$prayer_partners);
			$total_contact_arr =  array_merge($contact_arr,$netpals);
			array_sort_by_column($total_contact_arr, 's_displayname');

			$data['contacts'] = $total_contact_arr;
			$sugg = array();
			if(count($total_contact_arr)>0){
						
				foreach($total_contact_arr as $k=> $val)
				{	
						$thumb_html ='';
						$thumb  = get_profile_image_of_user('thumb',$val['s_profile_photo'],$val['e_gender']);	
						
						$thumb_html =  '<div class="tweeter-thumb" style="background:url('.$thumb.') no-repeat center;width:36px; height:36px; float:left;" ></div><span style="float:right;width:265px;">'.$val['s_displayname'].'</span>'."\n";
						
						$sugg[$k]['id'] = $val['user_id'];
						$sugg[$k]['label'] = $thumb_html;
						$sugg[$k]['value'] = $val['s_displayname'];
				}
				echo json_encode($sugg);
			}
			else
			{	
				$sugg[0]['id'] = '';
				$sugg[0]['label'] = 'No Result.';
				$sugg[0]['value'] = '';
				echo json_encode($sugg);
			}
				//echo "No Result.";
			exit;
			
    }
	
	
	public function forward_message() 
	{

		try
        {
			
	        $arr_messages = array();
					
					# error message trapping...
					if( trim($this->input->post('recipients'))=='') 
					{
							$arr_messages['frwd_recepients'] = "* Required Field.";
					}
										
					if( trim($this->input->post('txt_send_message'))=='') 
					{
							$arr_messages['send_message'] = "* Required Field.";
					}
					
					
		
				   //pr($arr_messages);
					if( count($arr_messages)==0 ) 
						{
		 					
								$this->load->model('users_model');
								
						
								$recipients_ids =  $this->input->post('recipients');
								
								foreach( explode(',',$recipients_ids) as $recipient_id ) 
								 {
									//$recipient_id = ($recipient_id);
									parent::send_message(decrypt($this->session->userdata('user_id')), $recipient_id, 'normal', $this->input->post('txt_send_subject'), $this->input->post('txt_send_message'));
									
									
									## check if opted for this notification or not ##
									$notificaion_opt = $this->user_alert_model->check_option_user_id($recipient_id);	
									
									//pr($notificaion_opt);
									
									## insert noifications ####
									if($notificaion_opt['e_message_received'] == 'Y'){
										$notification_arr['i_requester_id'] = decrypt($this->session->userdata('user_id'));
										$notification_arr['i_accepter_id'] = $recipient_id;
										$notification_arr['s_type'] = 'message';
										$notification_arr['dt_created_on'] = get_db_datetime();
										
										$ret = $this->user_notifications_model->insert($notification_arr);	
									}
									### end  ###
									
									
								}
								
								echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'Message sent') );
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
}

