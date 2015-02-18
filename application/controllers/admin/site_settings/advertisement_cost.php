<?php
/*********
* Author: 
* Date  : 
* Modified By: 
* Modified Date:
* 
* Purpose:
*  Controller For ## Management
* 
* @package 
* @subpackage 
* 
* @link InfController.php 
* @link Base_Controller.php
* @link model/##.php
* @link views/##
*/

class Advertisement_cost extends Admin_base_Controller
{

	function __construct()
	 {
		try
		{
		    parent::__construct();
			 parent::_check_admin_login();
			 
            # loading reqired model & helpers...
			$this->load->model('site_settings_model');
		}
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

	}

	function index() 
	{
            //die('ss');
		try
		 {
			$data = $this->data;
			$data['top_menu_selected'] = 1;
			$data['submenu'] =20;
            
            # adjusting header & footer sections [Start]...
                parent::_set_title('::: COGTIME Xtian network :::');
                parent::_set_meta_desc("::: COGTIME Xtian network :::");
                parent::_set_meta_keywords("::: COGTIME Xtian network :::");
                
                parent::_add_js_arr( array('js/jquery.form.js'=>'header',
											'js/jquery/JSON/json2.js'=>'header') );										
                parent::_add_css_arr( array('css/admin.css') );
		
			# adjusting header & footer sections [End]...
			
			# fetch and show hp cms content
			
			$data['posted'] = $this->site_settings_model->get_by_id(1);
             		$data['cost'] = $this->site_settings_model->get_advertisement_cost(1); 		
			# rendering the view file...
            $view_file = "admin/site_settings/advertisement_cost.phtml";
            parent::_render($data, $view_file);
		}
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
	}  
	
	public function edit_info($id=1)
	{
		try
		{
		  $arr_messages = array();
				
			if($_POST){
				  # error message trapping...
				  if( trim($this->input->post('home_page_cost'))=='') 
				  {
						  $arr_messages['home_page_cost'] = "* Required Field";
				  }
				  if( trim($this->input->post('socail_hub_chitter_tweets_page_cost'))=='') 
				  {
						  $arr_messages['socail_hub_chitter_tweets_page_cost'] = "* Required Field";
				  }
				  if( trim($this->input->post('socail_hub_blogs_page_cost'))=='') 
				  {
						  $arr_messages['socail_hub_blogs_page_cost'] = "* Required Field";
				  }
				 
				  if( trim($this->input->post('socail_hub_rings_page_cost'))=='') 
				  {
						  $arr_messages['socail_hub_rings_page_cost'] = "* Required Field";
				  }
				  
				  if( trim($this->input->post('socail_hub_events_page_cost'))=='') 
				  {
						  $arr_messages['socail_hub_events_page_cost'] = "* Required Field";
				  }
				  
				  if( trim($this->input->post('socail_hub_chat_rooms_page_cost'))=='') 
				  {
						  $arr_messages['socail_hub_chat_rooms_page_cost'] = "* Required Field";
				  }
				  
				  if( trim($this->input->post('build_kingdom_prayer_partners_zone_page_cost'))=='') 
				  {
						  $arr_messages['build_kingdom_prayer_partners_zone_page_cost'] = "* Required Field";
				  }
				  
				  if( trim($this->input->post('build_kingdom_bible_quiz_page_cost'))=='') 
				  {
						  $arr_messages['build_kingdom_bible_quiz_page_cost'] = "* Required Field";
				  }
				  
				   if( trim($this->input->post('build_kingdom_find_a_church_page_cost'))=='') 
				  {
						  $arr_messages['build_kingdom_find_a_church_page_cost'] = "* Required Field";
				  }
				  
				   if( trim($this->input->post('my_profile_page_cost'))=='') 
				  {
						  $arr_messages['my_profile_page_cost'] = "* Required Field";
				  }
				  
				  if( trim($this->input->post('my_friends_zone_page_cost')) =='') 
				  {
						  $arr_messages['my_friends_zone_page_cost'] = "* Required Field";
				  }
				  
				  if( trim($this->input->post('my_netpals_zone_page_cost')) =='') 
				  {
						  $arr_messages['my_netpals_zone_page_cost'] = "* Required Field";
				  }
				  
				  if( trim($this->input->post('my_photos_page_cost')) =='') 
				  {
						  $arr_messages['my_photos_page_cost'] = "* Required Field";
				  }
                                  
                                   if( trim($this->input->post('my_videos_page_cost')) =='') 
				  {
						  $arr_messages['my_videos_page_cost'] = "* Required Field";
				  }
                                   if( trim($this->input->post('my_audios_page_cost')) =='') 
				  {
						  $arr_messages['my_audios_page_cost'] = "* Required Field";
				  }
                                  
                                   if( trim($this->input->post('my_messages_page_cost')) =='') 
				  {
						  $arr_messages['my_messages_page_cost'] = "* Required Field";
				  }
                                  
                                   if( trim($this->input->post('my_privacy_settings_page_cost')) =='') 
				  {
						  $arr_messages['my_privacy_settings_page_cost'] = "* Required Field";
				  }
                                  
                                   if( trim($this->input->post('my_alert_settings_page_cost')) =='') 
				  {
						  $arr_messages['my_alert_settings_page_cost'] = "* Required Field";
				  }
                                  
                                   if( trim($this->input->post('my_email_notification_settings_page_cost')) =='') 
				  {
						  $arr_messages['my_email_notification_settings_page_cost'] = "* Required Field";
				  }
                                  
                                   if( trim($this->input->post('organizer_page_cost')) =='') 
				  {
						  $arr_messages['organizer_page_cost'] = "* Required Field";
				  }
                                  
                                  if( trim($this->input->post('change_password_page_cost')) =='') 
				  {
						  $arr_messages['change_password_page_cost'] = "* Required Field";
				  }
                                  
                                  if( trim($this->input->post('deactivate_account_page_cost')) =='') 
				  {
						  $arr_messages['deactivate_account_page_cost'] = "* Required Field";
				  }
                                  
                                  if( trim($this->input->post('media_center_banner_page_cost')) =='') 
				  {
						  $arr_messages['media_center_banner_page_cost'] = "* Required Field";
				  }
                                  
                                  if( trim($this->input->post('read_page_cost')) =='') 
				  {
						  $arr_messages['read_page_cost'] = "* Required Field";
				  }
                                  
                                  if( trim($this->input->post('watch_page_cost')) =='') 
				  {
						  $arr_messages['watch_page_cost'] = "* Required Field";
				  }
                                   if( trim($this->input->post('listen_page_cost')) =='') 
				  {
						  $arr_messages['listen_page_cost'] = "* Required Field";
				  }
                                   if( trim($this->input->post('media_center_category_page_cost')) =='') 
				  {
						  $arr_messages['media_center_category_page_cost'] = "* Required Field";
				  }
                                   if( trim($this->input->post('media_center_search_page_cost')) =='') 
				  {
						  $arr_messages['media_center_search_page_cost'] = "* Required Field";
				  }
                                   if( trim($this->input->post('public_profile_cost')) =='') 
				  {
						  $arr_messages['public_profile_cost'] = "* Required Field";
				  }
				  
				  if( count($arr_messages)==0 ) {
						  
					  $info = array();
					  
					  $info['home_page_cost'] =trim($this->input->post('home_page_cost'));	 
					  $info['socail_hub_chitter_tweets_page_cost'] = trim($this->input->post('socail_hub_chitter_tweets_page_cost'));	 

	  			      $info['socail_hub_blogs_page_cost'] = trim($this->input->post('socail_hub_blogs_page_cost'));
                                      
					  $info['socail_hub_rings_page_cost'] = trim($this->input->post('socail_hub_rings_page_cost'));	
                                          
					  $info['socail_hub_events_page_cost'] = $this->input->post('socail_hub_events_page_cost');
                                          
					  $info['socail_hub_chat_rooms_page_cost'] = trim($this->input->post('socail_hub_chat_rooms_page_cost'));	
					  $info['build_kingdom_prayer_partners_zone_page_cost'] = trim($this->input->post('build_kingdom_prayer_partners_zone_page_cost'));	
					  
					  $info['build_kingdom_bible_quiz_page_cost'] = trim($this->input->post('build_kingdom_bible_quiz_page_cost'));	
                                          
					  $info['build_kingdom_find_a_church_page_cost'] = trim($this->input->post('build_kingdom_find_a_church_page_cost'));
					  $info['my_profile_page_cost'] = trim($this->input->post('my_profile_page_cost'));
                                          
					  $info['my_friends_zone_page_cost'] = trim($this->input->post('my_friends_zone_page_cost'));
                                          
					  $info['my_netpals_zone_page_cost'] =trim($this->input->post('my_netpals_zone_page_cost'));
					  
					  $info['my_photos_page_cost'] = trim($this->input->post('my_photos_page_cost'));
                                          
                                          $info['my_audios_page_cost'] = trim($this->input->post('my_audios_page_cost'));
					  
					  $info['my_videos_page_cost'] = trim($this->input->post('my_videos_page_cost'));
                                          
                                           $info['my_messages_page_cost'] = trim($this->input->post('my_messages_page_cost'));
                                           
                                            $info['my_privacy_settings_page_cost'] = trim($this->input->post('my_privacy_settings_page_cost'));
                                            
                                             $info['my_alert_settings_page_cost'] = trim($this->input->post('my_alert_settings_page_cost'));
                                             
                                              $info['my_email_notification_settings_page_cost'] = trim($this->input->post('my_email_notification_settings_page_cost'));
                                              
                                              $info['organizer_page_cost'] = trim($this->input->post('organizer_page_cost'));
                                              
                                              $info['change_password_page_cost'] = trim($this->input->post('change_password_page_cost'));
                                              
                                              $info['deactivate_account_page_cost'] = trim($this->input->post('deactivate_account_page_cost'));
                                              
                                              $info['media_center_banner_page_cost'] = trim($this->input->post('media_center_banner_page_cost'));
                                              
                                              $info['read_page_cost'] = trim($this->input->post('read_page_cost'));
                                              
                                            $info['watch_page_cost'] = trim($this->input->post('watch_page_cost'));
                                            
                                            $info['read_page_cost'] = trim($this->input->post('read_page_cost'));
                                            
                                            $info['listen_page_cost'] = trim($this->input->post('listen_page_cost'));
                                            
                                             $info['media_center_category_page_cost'] = trim($this->input->post('media_center_category_page_cost'));
                                             
                                              $info['media_center_search_page_cost'] = trim($this->input->post('media_center_search_page_cost'));
                                              $info['public_profile_cost'] = trim($this->input->post('public_profile_cost'));
					  			
					  
					  
					 // $info['dt_created_on'] = get_db_datetime();
					  
					  $_ret = $this->site_settings_model->update_ad_cost($info ,1);
					  
										  
					  echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>' Advertisement cost settings updated Successfully.') );
				  }
				  else
				  {
					  echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'Error!') );
				  }
		
			}
			
		}
		catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
	}

}   // end of controller...