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

class Reset_password extends Admin_base_Controller
{

	function __construct()
	 {
		try
		{
		    parent::__construct();
			 parent::_check_admin_login();
			
            # loading reqired model & helpers...
			$this->load->model('admins_user_model');
			
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
			$data['submenu'] =10;
            
            # adjusting header & footer sections [Start]...
                parent::_set_title('::: COGTIME Xtian network :::');
                parent::_set_meta_desc("::: COGTIME Xtian network :::");
                parent::_set_meta_keywords("::: COGTIME Xtian network :::");
                
                parent::_add_js_arr( array() );										
                parent::_add_css_arr( array('css/admin.css') );
		
			# adjusting header & footer sections [End]...
			if($_POST){
				
				$posted["txt_old_pwd"]=trim($this->input->post("txt_old_pwd"));
				$posted["txt_new_pwd"]=trim($this->input->post("txt_new_pwd"));
				$posted["txt_cnew_pwd"]=trim($this->input->post("txt_cnew_pwd"));
				//($posted);
				
				$this->form_validation->set_message('valid_email', "Invalid Email ID.");
				$this->form_validation->set_message('matches', "* Password confirmation failed.");
				$this->form_validation->set_message('required', '* Required Field.');	// added *required message
				
				$this->form_validation->set_rules('txt_old_pwd', "Old Password", 'trim|required|callback_match_password');
				$this->form_validation->set_rules('txt_new_pwd', "New Password", 'trim|required');
				if($posted["txt_new_pwd"] != ''){
				  $this->form_validation->set_rules('txt_cnew_pwd', "Confirm password", 'trim|required|matches[txt_new_pwd]');
				}
				
				
				if ($this->form_validation->run() == FALSE)
				{
				    ////////Display the add form with posted values within it////
                    $this->data["posted"]=$posted;/*don't change*/
					//$data["posted"]=$posted;/*don't change*/
					//($this->data["posted"]);
					
				}
				else{
					
				  $info = array();
				  $info['s_current_password'] = $posted['txt_old_pwd']; 
				  $info['s_password'] = $posted['txt_new_pwd']; 
                  $info['dt_updated_on'] = get_db_datetime();
				  $i_user_id = intval(decrypt($this->session->userdata('user_id')));
				  
				  $USER_ID = $this->admins_user_model->change_password($info , $i_user_id);
				  set_success_msg("Your password has been changed successfully");
				  $SUCCESS_PG = admin_base_url().'site_settings/reset-password.html';
							   header("location:".$SUCCESS_PG);
							   exit;
				  
				}
				
				
			}
			
			
			
			# rendering the view file...
            $view_file = "admin/site_settings/reset_password.phtml";
            parent::_render($data, $view_file);
		}
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
	}  
	
	public function match_password($txt_old_pwd)
	{ 
        $i_user_id = intval(decrypt($this->session->userdata('user_id')));
		$ret = $this->admins_user_model->match_password($i_user_id , $txt_old_pwd);
        if($ret)
        {
           return TRUE;
        }
        else
        {
            $this->form_validation->set_message('match_password', "* Incorrect password.");
            return FALSE; 
			
        }
	}
	
	function manage_admin() 
	{
		try
		 { 
			$data = $this->data;
			$data['top_menu_selected'] = 1;
			$data['submenu'] =13;
            
            # adjusting header & footer sections [Start]...
			parent::_set_title('::: COGTIME Xtian network :::');
			parent::_set_meta_desc("::: COGTIME Xtian network :::");
			parent::_set_meta_keywords("::: COGTIME Xtian network :::");
			
			parent::_add_js_arr( array() );										
			parent::_add_css_arr( array('css/admin.css') );
		
			# adjusting header & footer sections [End]...
			if($_POST){
				
				$txt_email =trim($this->input->post("txt_email"));
				
				$this->form_validation->set_message('valid_email', "Invalid Email ID.");
				$this->form_validation->set_message('required', '* Required Field.');	// added *required message
				
				$this->form_validation->set_rules('txt_email', "Email ", 'trim|required|valid_email');
				  
				if ($this->form_validation->run() == FALSE)
				{
				    ////////Display the add form with posted values within it////
                    $data["txt_email"]=$txt_email;/*don't change*/
				}
				else{
					
				  $info = array();
				  $info['s_email'] = $txt_email; 
                  $info['dt_updated_on'] = get_db_datetime();
				  $i_user_id = intval(decrypt($this->session->userdata('user_id')));
				  
				  $USER_ID = $this->admins_user_model->update_user($info , $i_user_id);
				  set_success_msg("Your Email has been updated successfully");
				  $SUCCESS_PG = admin_base_url().'site-settings/manage-admin.html';
							   header("location:".$SUCCESS_PG);
							   exit;
				}
				
				
			}
		   else{
				
				$i_user_id = intval(decrypt($this->session->userdata('user_id')));
				$data['txt_email'] = $this->admins_user_model->get_email_by_id($i_user_id);
		   }
			
			
			# rendering the view file...
            $view_file = "admin/site_settings/manage-admin.phtml";
            parent::_render($data, $view_file);
		}
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
	}  
	
	function headlines() 
	{
		try
		 { 
			$data = $this->data;
			$data['top_menu_selected'] = 1;
			$data['submenu'] =5;
            $this->load->model('scrolling_headlines_model');
            # adjusting header & footer sections [Start]...
			parent::_set_title('::: COGTIME Xtian network :::');
			parent::_set_meta_desc("::: COGTIME Xtian network :::");
			parent::_set_meta_keywords("::: COGTIME Xtian network :::");
			
			parent::_add_js_arr( array() );										
			parent::_add_css_arr( array('css/admin.css') );
		
			# adjusting header & footer sections [End]...
			if($_POST){
				
				$txt_feed1=trim($this->input->post("txt_feed1"));
				$txt_feed2 =trim($this->input->post("txt_feed2"));
				$txt_feed3 =trim($this->input->post("txt_feed3"));
				//$this->form_validation->set_message('valid_url_format', "Invalid Url.");
				$this->form_validation->set_message('required', '* Required Field.');	// added *required message
				
				$this->form_validation->set_rules('txt_feed1', "Feed ", 'trim|required');
				$this->form_validation->set_rules('txt_feed2', "Feed ", 'trim|required');
				$this->form_validation->set_rules('txt_feed3', "Feed ", 'trim|required');
				  
				
				if ($this->form_validation->run() == FALSE)
				{
				    ////////Display the add form with posted values within it////
                    $data["txt_feed1"]=$txt_feed1;
					$data["txt_feed2"]=$txt_feed2;
					 $data["txt_feed3"]=$txt_feed3;/*don't change*/
				}
				else{
					
				  $info = array();
				  $info['s_url'] = $txt_feed1; 
                  $info['dt_updated_on'] = get_db_datetime();
				  //$i_user_id = intval(decrypt($this->session->userdata('user_id')));
				  $feed1=$this->scrolling_headlines_model->update($info,1);
				  $info = array();
				  $info['s_url'] = $txt_feed2; 
                  $info['dt_updated_on'] = get_db_datetime();
				  //$i_user_id = intval(decrypt($this->session->userdata('user_id')));
				  $feed2=$this->scrolling_headlines_model->update($info,2);
				  //$feed2=$this->scrolling_headlines_model->insert($info);
				  $info = array();
				  $info['s_url'] = $txt_feed3; 
                  $info['dt_updated_on'] = get_db_datetime();
				  //$i_user_id = intval(decrypt($this->session->userdata('user_id')));
				  $feed3=$this->scrolling_headlines_model->update($info,3);
				  //$feed3=$this->scrolling_headlines_model->insert($info);
				  //$USER_ID = $this->admins_user_model->update_user($info , $i_user_id);
				  set_success_msg("Feed url has been updated successfully");
				  $SUCCESS_PG = admin_base_url().'scrolling-headlines.html';
							   header("location:".$SUCCESS_PG);
							   exit;
				}
				
				
			}
		   else{
				
				//$i_user_id = intval(decrypt($this->session->userdata('user_id')));
				$data['txt_feed1'] = $this->scrolling_headlines_model->get_feed_url_by_id(1);
				$data['txt_feed2'] = $this->scrolling_headlines_model->get_feed_url_by_id(2);
				$data['txt_feed3'] = $this->scrolling_headlines_model->get_feed_url_by_id(3);
		   }
			
			
			# rendering the view file...
            $view_file = "admin/site_settings/scrolling_headlines/scrolling_headlines.phtml";
            parent::_render($data, $view_file);
		}
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
	}  
}   // end of controller...