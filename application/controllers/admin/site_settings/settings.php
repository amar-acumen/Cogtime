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

class Settings extends Admin_base_Controller
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
		try
		 {
			$data = $this->data;
			$data['top_menu_selected'] = 1;
			$data['submenu'] =2;
            
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
             				
			# rendering the view file...
            $view_file = "admin/site_settings/common_settings.phtml";
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
				  if( trim($this->input->post('i_etrade_listing_cost'))=='') 
				  {
						  $arr_messages['i_etrade_listing_cost'] = "* Required Field";
				  }
				  if( trim($this->input->post('i_eswap_listing_cost'))=='') 
				  {
						  $arr_messages['i_eswap_listing_cost'] = "* Required Field";
				  }
				  if( trim($this->input->post('i_max_efreebie'))=='') 
				  {
						  $arr_messages['i_max_efreebie'] = "* Required Field";
				  }
				 
				  if( trim($this->input->post('i_max_prayer_partner'))=='') 
				  {
						  $arr_messages['i_max_prayer_partner'] = "* Required Field";
				  }
				  
				  if( trim($this->input->post('i_max_prayer_partner_sugg'))=='') 
				  {
						  $arr_messages['i_max_prayer_partner_sugg'] = "* Required Field";
				  }
				  
				  if( trim($this->input->post('i_max_ring'))=='') 
				  {
						  $arr_messages['i_max_ring'] = "* Required Field";
				  }
				  
				  if( trim($this->input->post('i_max_commitement'))=='') 
				  {
						  $arr_messages['i_max_commitement'] = "* Required Field";
				  }
				  
				  if( trim($this->input->post('i_max_ring_member'))=='') 
				  {
						  $arr_messages['i_max_ring_member'] = "* Required Field";
				  }
				  
				   if( trim($this->input->post('i_max_video_upload'))=='') 
				  {
						  $arr_messages['i_max_video_upload'] = "* Required Field";
				  }
				  
				   if( trim($this->input->post('i_max_audio_upload'))=='') 
				  {
						  $arr_messages['i_max_audio_upload'] = "* Required Field";
				  }
				  
				  if( trim($this->input->post('i_prayer_wall_refresh_frequency')) =='-1') 
				  {
						  $arr_messages['i_prayer_wall_refresh_frequency'] = "* Required Field";
				  }
				  
				  if( trim($this->input->post('i_clear_prayer_request')) =='') 
				  {
						  $arr_messages['i_clear_prayer_request'] = "* Required Field";
				  }
				  
				  if( trim($this->input->post('i_max_prayer_grp')) =='') 
				  {
						  $arr_messages['i_max_prayer_grp'] = "* Required Field";
				  }
				  
				  if( count($arr_messages)==0 ) {
						  
					  $info = array();
					  
					  $info['i_etrade_listing_cost'] = doubleval(trim($this->input->post('i_etrade_listing_cost')));	 
					  $info['i_eswap_listing_cost'] = doubleval(trim($this->input->post('i_eswap_listing_cost')));	 

	  			      $info['i_max_efreebie'] = intval(trim($this->input->post('i_max_efreebie')));	
					  $info['i_max_commitement'] = intval(trim($this->input->post('i_max_commitement')));	
					  $info['i_max_ring_member'] = intval(trim($this->input->post('i_max_ring_member')));	
					  $info['i_max_video_upload'] = intval(trim($this->input->post('i_max_video_upload')));	
					  $info['i_max_audio_upload'] = intval(trim($this->input->post('i_max_audio_upload')));	
					  
					  $info['i_max_ring'] = intval(trim($this->input->post('i_max_ring')));	
					  $info['i_max_prayer_partner_sugg'] = intval(trim($this->input->post('i_max_prayer_partner_sugg')));
					  $info['i_max_prayer_partner'] = intval(trim($this->input->post('i_max_prayer_partner')));
					  $info['i_clear_prayer_request'] = intval(trim($this->input->post('i_clear_prayer_request')));
					  $info['i_max_prayer_grp'] = intval(trim($this->input->post('i_max_prayer_grp')));
					  
					  $info['i_prayer_wall_refresh_frequency'] = intval(trim($this->input->post('i_prayer_wall_refresh_frequency')));	
					  			
					  
					  
					  $info['dt_created_on'] = get_db_datetime();
					  
					  $_ret = $this->site_settings_model->update($info ,1);
					  
										  
					  echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'Site Settings updated Successfully.') );
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