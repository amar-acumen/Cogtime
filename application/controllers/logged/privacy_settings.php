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

class Privacy_settings extends Base_controller
{
    
    
    public function __construct()
     {
        try
        {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...
            $this->load->model('user_alert_model');
			$this->load->model('contacts_model');
			$this->load->model('netpals_model');
			$this->load->model('my_prayer_partner_model');
			$this->load->model('my_ring_model');
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
										'js/frontend/logged/tweets/tweet_utilities.js',
										//'js/frontend/logged/notifcations/user_alerts.js'
										
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css') );
			
			$i_profile_id 		= intval(decrypt($this->session->userdata('user_id')));
			$WHERE_ring = " AND r.i_user_id='".$i_profile_id."'";	
			$data['arr_ring'] 		= $this->my_ring_model->get_list_of_ring_created_by_user($WHERE_ring);
			
			
			$data['arr_prayer_grp'] = $this->prayer_group_model->get_my_groups($i_profile_id,'','','','dt_created_on DESC', 'ownership'); 
			#pr($data['arr_prayer_grp']);
			
			
			
			$data['privacy_settings']	= $this->user_alert_model->get_privacy_settings($i_profile_id);
			#pr($data['privacy_settings']);
            # view file...
            $VIEW = "logged/user_notifications/privacy_settings.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }  
	
	public function update()
	{
		try
		{
			
			$i_logged_id = intval(decrypt($this->session->userdata('user_id')));
			
			$photoprivacy	= $this->input->post('photoprivacy');
			$photofrnd		= $this->input->post('photofrnd');
			$photonetpal	= $this->input->post('photonetpal');
			$photopp		= $this->input->post('photopp');
			$photoring		= $this->input->post('photoring');
			$photopg		= $this->input->post('photopg');
			
			$audioprivacy	= $this->input->post('audioprivacy');
			$audiofrnd		= $this->input->post('audiofrnd');
			$audionetpal	= $this->input->post('audionetpal');
			$audiopp		= $this->input->post('audiopp');
			$audioring		= $this->input->post('audioring');
			$audiopg		= $this->input->post('audiopg');
			
			$videoprivacy	= $this->input->post('videoprivacy');
			$videofrnd		= $this->input->post('videofrnd');
			$videonetpal	= $this->input->post('videonetpal');
			$videopp		= $this->input->post('videopp');
			$videoring		= $this->input->post('videoring');
			$videopg		= $this->input->post('videopg');
			
			$eventprivacy	= $this->input->post('eventprivacy');
			$eventfrnd		= $this->input->post('eventfrnd');
			$eventnetpal	= $this->input->post('eventnetpal');
			$eventpp		= $this->input->post('eventpp');
			$eventring		= $this->input->post('eventring');
			$eventpg		= $this->input->post('eventpg');
			
			$wallprivacy	= $this->input->post('wallprivacy');
			$wallfrnd		= $this->input->post('wallfrnd');
			$wallnetpal		= $this->input->post('wallnetpal');
			$wallpp			= $this->input->post('wallpp');
			$wallring		= $this->input->post('wallring');
			$wallpg			= $this->input->post('wallpg');
			
			
			
			$wallCommentLikeprivacy	= $this->input->post('wallCommentLikeprivacy');
			$wallCommentLikefrnd				= $this->input->post('wallCommentLikefrnd');
			$wallCommentLikenetpal				= $this->input->post('wallCommentLikenetpal');
			$wallCommentLikepp					= $this->input->post('wallCommentLikepp');
			$wallCommentLikering				= $this->input->post('wallCommentLikering');
			$wallCommentLikepg					= $this->input->post('wallCommentLikepg');
			
			$msgprivacy	= $this->input->post('msgprivacy');
			$msgfrnd		= $this->input->post('msgfrnd');
			$msgnetpal		= $this->input->post('msgnetpal');
			$msgpp			= $this->input->post('msgpp');
			$msgring		= $this->input->post('msgring');
			$msgpg			= $this->input->post('msgpg');
					
			
			
			
			$arr_messages 	= array();
			#echo $photoprivacy.'&&'.$photofrnd.'&&'.$photonetpal.'&&'.$photopp.'&&'.$photoring.'&&'.$photopg;	
			$error	= 0;
			if($photoprivacy==2 && $photofrnd=='' && $photonetpal=='' && $photopp=='' && $photoring=='' && $photopg=='')
			{
				$arr_messages['photoprivacy']	= 'Please check privacy settings';
				$error++;
			}
			if($audioprivacy==2 && ($audiofrnd=='' && $audionetpal=='' && $audiopp=='' && $audioring=='' && $audiopg==''))
			{
				$arr_messages['audioprivacy']	= 'Please check privacy settings';
				$error++;
			}
			if($videoprivacy==2 && ($videofrnd=='' && $videonetpal=='' && $videopp=='' && $videoring=='' && $videopg==''))
			{
				$arr_messages['videoprivacy']	= 'Please check privacy settings';
				$error++;
			}
			if($eventprivacy==2 && ($eventfrnd=='' && $eventnetpal=='' && $eventpp=='' && $eventring=='' && $eventpg==''))
			{
				$arr_messages['eventprivacy']	= 'Please check privacy settings';
				$error++;
			}
			if($wallprivacy==2 && ($wallfrnd=='' && $wallnetpal=='' && $wallpp=='' && $wallring=='' && $wallpg==''))
			{
				$arr_messages['wallprivacy']	= 'Please check privacy settings';
				$error++;
			}
			
			
			if($wallCommentLikeprivacy==2 && ($wallCommentLikefrnd=='' && $wallCommentLikenetpal=='' && $wallCommentLikepp=='' && $wallCommentLikering=='' && $wallCommentLikepg==''))
			{
				$arr_messages['wallCommentLikeprivacy']	= 'Please check privacy settings';
				$error++;
			}
			
			
			if($msgprivacy==2 && ($msgfrnd=='' && $msgnetpal=='' && $msgpp=='' && $msgring=='' && $msgpg==''))
			{
				$arr_messages['msgprivacy']	= 'Please check privacy settings';
				$error++;
			}
			
			
			if( count($arr_messages)==0 ) {
				$arr	= array();
				if($photoprivacy==2)
				{
					if($photofrnd==1)
						$arr['i_friend_privacy']	= $photofrnd;
					else
						$arr['i_friend_privacy']	= 0;	
					if($photonetpal==1)
						$arr['i_netpal_privacy']	= $photonetpal;
					else
						$arr['i_netpal_privacy']	= 0;	
							
					if($photopp==1)
						$arr['i_prayer_partner_privacy']	= $photopp;
					else
						$arr['i_prayer_partner_privacy']	= 0;
					if($photoring==1)
					{
						$arr['i_ring_privacy']	= 1;
						/*if(count($_POST['ring'])>0)
						{
							$strringval	= '';
							foreach($_POST['ring'] as $ring)
							{
								$strringval	.= $ring.', ';
							}
							$strringval	= substr($strringval,0,strlen($strringval)-2);
							$arr['i_ring_privacy']	= $strringval;
						}
						else
						{
							$arr['i_ring_privacy']	= 0;
						}*/
					}
					else
					$arr['i_ring_privacy']	= 0;
					
					if($photopg==1)
					{
						$arr['i_prayer_group_privacy']	= 1;
						/*if(count($_POST['prayer_group'])>0)
						{
							$strpgval	= '';
							foreach($_POST['prayer_group'] as $pg)
							{
								$strpgval	.= $pg.', ';
							}
							$strpgval	= substr($strpgval,0,strlen($strpgval)-2);
							$arr['i_prayer_group_privacy']	= $strpgval;
						}
						else
						{
							$arr['i_prayer_group_privacy']	= 0;
						}*/
					}
					else
						$arr['i_prayer_group_privacy']	= 0;
						
					$arr['s_section_name']	= 'photo';
					$arr['i_user_id']		= $i_logged_id;
					$this->user_alert_model->privacy_setting($arr,$i_logged_id,'photo');
					
				}
				else
				{
					$this->user_alert_model->public_privacy_settings($i_logged_id,'photo');
				}
				
				
				$arr	= array();
				if($audioprivacy==2)
				{
					if($audiofrnd==1)
						$arr['i_friend_privacy']	= $audiofrnd;
					else
					   $arr['i_friend_privacy']	= 0; 	
					   
					if($audionetpal==1)
						$arr['i_netpal_privacy']	= $audionetpal;
					else
						$arr['i_netpal_privacy']	= 0;		
					
					if($audiopp==1)
						$arr['i_prayer_partner_privacy']	= $audiopp;
					else
						$arr['i_prayer_partner_privacy']	= 0;
						
						
					if($audioring==1)
					{
						$arr['i_ring_privacy']	= 1;
					}
					else
						$arr['i_ring_privacy']	= 0;
					
					if($audiopg==1)
					{
						$arr['i_prayer_group_privacy']	= 1;
					}
					else
						$arr['i_prayer_group_privacy']	= 0;
						
					$arr['s_section_name']	= 'audio';
					$arr['i_user_id']		= $i_logged_id;
					$this->user_alert_model->privacy_setting($arr,$i_logged_id,'audio');
					
				}
				else
				{
					$this->user_alert_model->public_privacy_settings($i_logged_id,'audio');
				}
				$arr	= array();
				if($videoprivacy==2)
				{
					if($videofrnd==1)
						$arr['i_friend_privacy']	= $videofrnd;	
					else
						$arr['i_friend_privacy']	= 0;
					
					if($videonetpal==1)
						$arr['i_netpal_privacy']	= $videonetpal;	
					else
						$arr['i_netpal_privacy']	= 0;		
					
					if($videopp==1)
						$arr['i_prayer_partner_privacy']	= $videopp;
					else
						$arr['i_prayer_partner_privacy']	= 0;
						
					if($videoring==1)
					{
						$arr['i_ring_privacy']	= 1;
					}
					else
					   $arr['i_ring_privacy']	= 0;	
					
					if($videopg==1)
					{
						$arr['i_prayer_group_privacy']	= 1;
					}
					else
					   $arr['i_prayer_group_privacy']	= 0;
						
					$arr['s_section_name']	= 'video';
					$arr['i_user_id']		= $i_logged_id;
					$this->user_alert_model->privacy_setting($arr,$i_logged_id,'video');
					
				}	
				else
				{
					$this->user_alert_model->public_privacy_settings($i_logged_id,'video');
				}
				$arr	= array();
				if($eventprivacy==2)
				{
					if($eventfrnd==1)
						$arr['i_friend_privacy']	= $eventfrnd;
					else
						$arr['i_friend_privacy']	= 0;	
					
					if($eventnetpal==1)
						$arr['i_netpal_privacy']	= $eventnetpal;	
					else
						$arr['i_netpal_privacy']	= 0;		
						
					if($eventpp==1)
						$arr['i_prayer_partner_privacy']	= $eventpp;
					else
						$arr['i_prayer_partner_privacy']	= 0;
						
					if($eventring==1)
					{
						$arr['i_ring_privacy']	= 1;
					}
					else
						$arr['i_ring_privacy']	= 0;
						
					if($eventpg==1)
					{
						$arr['i_prayer_group_privacy']	= 1;
					}
					else
						$arr['i_prayer_group_privacy']	= 0;
						
					$arr['s_section_name']	= 'event';
					$arr['i_user_id']		= $i_logged_id;
					$this->user_alert_model->privacy_setting($arr,$i_logged_id,'event');
					
				}	
				else
				{
					$this->user_alert_model->public_privacy_settings($i_logged_id,'event');
				}
				
				$arr	= array();
				if($wallprivacy==2)
				{
					if($wallfrnd==1)
						$arr['i_friend_privacy']	= $wallfrnd;	
					else
						$arr['i_friend_privacy']	= 0;	
						
					if($wallnetpal==1)
						$arr['i_netpal_privacy']	= $wallnetpal;
					else
						$arr['i_netpal_privacy']	= 0;		
					 
					 
					 if($wallpp==1)
						$arr['i_prayer_partner_privacy']	= $wallpp;
					 else
					 	$arr['i_prayer_partner_privacy']	= 0;
						
					if($wallring==1)
					{
						$arr['i_ring_privacy']	= 1;
					}
					else
						$arr['i_ring_privacy']	= 0;
					
					if($wallpg==1)
					{
						
						$arr['i_prayer_group_privacy']	= 1;
					}
					else
					   $arr['i_prayer_group_privacy']	= 0;
						
						
					$arr['s_section_name']	= 'wall';
					$arr['i_user_id']		= $i_logged_id; //pr($arr,1);
					$this->user_alert_model->privacy_setting($arr,$i_logged_id,'wall');
					
				}	
				else
				{
					$this->user_alert_model->public_privacy_settings($i_logged_id,'wall');
				}
				
				$arr	= array();
				if($wallCommentLikeprivacy==2)
				{
					if($wallCommentLikefrnd==1)
						$arr['i_friend_privacy']	= $wallCommentLikefrnd;	
					else
						$arr['i_friend_privacy']	= 0;	
						
					if($wallCommentLikenetpal==1)
						$arr['i_netpal_privacy']	= $wallCommentLikenetpal;
					else
						$arr['i_netpal_privacy']	= 0;		
					 
					 
					 if($wallCommentLikepp==1)
						$arr['i_prayer_partner_privacy']	= $wallCommentLikepp;
					 else
					 	$arr['i_prayer_partner_privacy']	= 0;
						
					if($wallCommentLikering==1)
					{
						$arr['i_ring_privacy']	= 1;
					}
					else
						$arr['i_ring_privacy']	= 0;
					
					if($wallCommentLikepg==1)
					{
						
						$arr['i_prayer_group_privacy']	= 1;
					}
					else
					   $arr['i_prayer_group_privacy']	= 0;
						
						
					$arr['s_section_name']	= 'wallCommentLike';
					$arr['i_user_id']		= $i_logged_id; //pr($arr,1);
					$this->user_alert_model->privacy_setting($arr,$i_logged_id,'wallCommentLike');
					
				}	
				else
				{
					$this->user_alert_model->public_privacy_settings($i_logged_id,'wallCommentLike');
				}
				
				
				$arr	= array();
				if($msgprivacy==2)
				{
					if($msgfrnd==1)
						$arr['i_friend_privacy']	= $msgfrnd;	
					else
						$arr['i_friend_privacy']	= 0;	
						
					if($msgnetpal==1)
						$arr['i_netpal_privacy']	= $msgnetpal;
					else
						$arr['i_netpal_privacy']	= 0;		
					 
					 
					 if($msgpp==1)
						$arr['i_prayer_partner_privacy']	= $msgpp;
					 else
					 	$arr['i_prayer_partner_privacy']	= 0;
						
					if($msgring==1)
					{
						$arr['i_ring_privacy']	= 1;
					}
					else
						$arr['i_ring_privacy']	= 0;
					
					if($msgpg==1)
					{
						
						$arr['i_prayer_group_privacy']	= 1;
					}
					else
					   $arr['i_prayer_group_privacy']	= 0;
						
						
					$arr['s_section_name']	= 'msg';
					$arr['i_user_id']		= $i_logged_id; //pr($arr,1);
					$this->user_alert_model->privacy_setting($arr,$i_logged_id,'msg');
					
				}	
				else
				{
					$this->user_alert_model->public_privacy_settings($i_logged_id,'msg');
				}
				
				echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'Privacy settings updated Successfully.') );
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

