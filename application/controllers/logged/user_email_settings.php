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

class User_email_settings extends Base_controller
{
    
    
    public function __construct()
     {
        try
        {
            parent::__construct();
               parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...
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
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',*/
										'js/production/tweet_utilities.js',
										//'js/frontend/logged/notifcations/user_alerts.js'
										
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css') );
										  
            $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
         	$data['arr_email_arr'] = $this->user_alert_model->get_email_by_user_id($i_profile_id);
			
			//pr($data['arr_alert_arr']);
			
			
            # view file...
            $VIEW = "logged/user_notifications/email-notification-settings.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }  
	
	public function update_info()
	{
		try
		{
			parent::check_login(TRUE,'',array('1')); // put this code on those pages which are not accessable by non logged in user
			
			$i_logged_id = intval(decrypt($this->session->userdata('user_id')));
			
			$arr_messages = array();
						
						//pr($_POST,1);
		   //pr($arr_messages);
			if( count($arr_messages)==0 ) {
					
				$info = array();
				
				$info['i_user_id'] = intval(decrypt($this->session->userdata('user_id')));	 
				$info['e_friend_request_received'] = ($this->input->post('txt_frnd_req_rec') == '1')  ?'Y':'N'; 
				$info['e_friend_request_accepted'] = ($this->input->post('txt_frnd_req_acpt') == '1')  ?'Y':'N'; 
				$info['e_friend_request_declined'] = ($this->input->post('txt_frnd_req_dec') == '1')  ?'Y':'N'; 
				
				$info['e_net_pal_request_received'] = ($this->input->post('txt_netpal_req_rec')  == '1') ?'Y':'N'; 
				$info['e_net_pal_request_accepted'] = ($this->input->post('txt_netpal_req_acpt')  == '1') ?'Y':'N'; 
				$info['e_netpal_request_declined'] = ($this->input->post('txt_netpal_req_dec')  == '1') ?'Y':'N'; 
				
				$info['e_prayer_partner_request_received'] = ($this->input->post('txt_prayer_patner_req_rec')  == '1') ?'Y':'N'; 
				$info['e_prayer_partner_my_request_accepted'] = ($this->input->post('txt_prayer_patner_req_acpt')  == '1') ?'Y':'N'; 
				$info['e_prayer_partner_request_declined'] = ($this->input->post('txt_prayer_patner_req_dec')  == '1') ?'Y':'N' ; 
				
				$info['e_photo_comments_received'] = ($this->input->post('txt_photo_comments_rec')  == '1') ?'Y':'N'; 
				$info['e_video_comments_received'] = ($this->input->post('txt_video_comments_rec')  == '1') ?'Y':'N'; 
				$info['e_audio_comments_received'] = ($this->input->post('txt_audio_comments_rec')  == '1') ?'Y':'N' ; 
				
				$info['e_message_received'] = ($this->input->post('txt_message_rec') == '1')  ?'Y':'N'; 
				$info['e_ring_comments_received'] = ($this->input->post('txt_ring_comment_rec') == '1')  ?'Y':'N'; 
				
				$info['e_ring_posts_received'] = ($this->input->post('txt_ring_posts_rec') == '1')  ?'Y':'N'; 
				
				$info['e_blog_comments_received'] = ($this->input->post('txt_blog_comments_rec') == '1')  ?'Y':'N'; 
				$info['e_event_comments_received'] = ($this->input->post('txt_event_comments_rec') == '1')  ?'Y':'N'; 
				
				
				$info['e_tweet_comments_received'] = ($this->input->post('txt_tweet_comments_received') == '1')  ?'Y':'N'; 
				$info['e_retweet'] = ($this->input->post('txt_retweet') == '1')  ?'Y':'N'; 
				$info['e_prayer_commit_received'] = ($this->input->post('txt_prayer_commit_received') == '1')  ?'Y':'N'; 
				
				$info['e_prayer_grp_invitation'] = ($this->input->post('txt_prayer_grp_invitation') == '1')  ?'Y':'N'; 
				
				$info['e_prayer_grp_request_accepted'] = ($this->input->post('txt_prayer_grp_req_acpt') == '1')  ?'Y':'N';
				$info['e_prayer_grp_request_declined'] = ($this->input->post('txt_prayer_grp_req_dec') == '1')  ?'Y':'N'; 
				$info['e_prayer_grp_chat_invitation'] = ($this->input->post('txt_prayer_grp_chat_invitation') == '1')  ?'Y':'N'; 
				
				$info['e_prayer_grp_joining_req'] = ($this->input->post('txt_prayer_grp_joining_req') == '1')  ?'Y':'N'; 
				
				
			/*	$info['e_trade_request_recvd']     = ($this->input->post('e_trade_request_recvd') == '1')  ?'Y':'N';
				$info['e_trade_request_accpt']     = ($this->input->post('e_trade_request_accpt') == '1')  ?'Y':'N';
				$info['e_trade_request_shipped']   = ($this->input->post('e_trade_request_shipped') == '1')  ?'Y':'N';
				$info['e_trade_request_declined']  = ($this->input->post('e_trade_request_declined') == '1')  ?'Y':'N';
				
				$info['e_swap_request_recvd']    = ($this->input->post('e_swap_request_recvd') == '1')  ?'Y':'N';
				$info['e_swap_request_accpt']    = ($this->input->post('e_swap_request_accpt') == '1')  ?'Y':'N';
				$info['e_swap_request_declined'] = ($this->input->post('e_swap_request_declined') == '1')  ?'Y':'N';
				
				$info['e_freebie_request_declined'] = ($this->input->post('e_freebie_request_declined') == '1')  ?'Y':'N';
				$info['e_freebie_request_accpt']    = ($this->input->post('e_freebie_request_accpt') == '1')  ?'Y':'N';
				$info['e_freebie_request_recvd']    = ($this->input->post('e_freebie_request_recvd') == '1')  ?'Y':'N';*/
				
				$arr_email_arr = $this->user_alert_model->get_email_by_user_id($i_logged_id);
				
				if(is_array($arr_email_arr) && count($arr_email_arr)){
					
					$info['dt_updated_on'] = get_db_datetime();
					$_ret = $this->user_alert_model->update_email($info , $i_logged_id);
					
				}
				else{
					
					$info['dt_created_on'] = get_db_datetime();
					$_ret = $this->user_alert_model->insert_email($info);
				}
									
				echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'Email Notification settings updated Successfully.') );
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
    
    
}   // end of controller...

