<?php
/*********
* Author: 
* Purpose:
*  Controller For "advertisements"
* 
* @package 
* @subpackage 
* 
* @link InfController.php 
* @link Base_Controller.php
* @link model/admin_groups_model.php
* @link views/##
*/

class Member_detail_utilities extends Admin_base_Controller
{
    

    // constructor definition...
    function __construct()
    {
        try
        {
            parent::__construct();
            parent::_check_admin_login(); 
                       
            # configuring paths...
            $this->upload_path = BASEPATH.'../uploads/user_profile_image/';
            # loading reqired model & helpers...
            $this->load->model("users_model");
            $this->load->model("education_model");
            $this->load->model("work_model");    
            $this->load->model("skill_model");    
            $this->load->helper('common_option_helper.php');
           
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    // function to show display form [for tabs] - AJAX CALL...
    public function display_tabbed_forms_AJAX($user_id, $tab=null) 
    {
        
        try
        {
            $ajax_data = $this->data;
            
            // fetching data
            $ajax_data['result'] = $this->users_model->fetch_this($user_id);
            $ajax_data['church_admin'] = $this->users_model->fetch_church_admin($user_id);   
            $ajax_data['church_member'] = $this->users_model->fetch_church_member($user_id); 
            $ajax_data['user_id'] = $user_id;
            // get concerned view file...
            $VIEW_FILE = $this->get_display_file($tab);
            
            # loading the view file...
            $this->load->view($VIEW_FILE, $ajax_data);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }

    
    # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    #           FORM EDIT & CANCEL [BEGIN]
    # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        
        // function to show EDIT PART [for tabs] - AJAX CALL...
        public function show_edit_part_AJAX($user_id, $tab_type=null) 
        {
            
            try
            {
                $ajax_data = $this->data;
                
                // fetching data
                $ajax_data['result'] = $this->users_model->fetch_this($user_id);
               	$where	= " i_country_id='".$ajax_data['result']["i_country_id"]."'";
				$ajax_data['state'] 	= makeOptionState($where, encrypt($ajax_data['result']["i_state_id"]));
				$where1	= " i_state_id='".$ajax_data['result']["i_state_id"]."'";
				$ajax_data['city'] 	= makeOptionCity($where1,encrypt($ajax_data['result']["i_city_id"]));
                // get concerned view file...
                $VIEW_FILE = $this->get_edit_part_file($tab_type);
                
                # loading the view file...
                $this->load->view($VIEW_FILE, $ajax_data);
            }
            catch(Exception $err_obj)
            {
                show_error($err_obj->getMessage());
            }
        }
        
        // function to cancel EDIT PART i.e. just for display-section [for tabs] - AJAX CALL...
        public function cancel_edit_part_AJAX($user_id, $tab_type=null) 
        {
            
            try
            {
                $ajax_data = $this->data;
                
                // fetching data
                $ajax_data['result'] = $this->users_model->fetch_this($user_id);
               
                // get concerned view file...
                $VIEW_FILE = $this->get_just_display_file($tab_type);
                
                # loading the view file...
                $this->load->view($VIEW_FILE, $ajax_data);
            }
            catch(Exception $err_obj)
            {
                show_error($err_obj->getMessage());
            }
        }
    # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    #           FORM EDIT & CANCEL [END]
    # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    
    
    
    
    
    
    
    
    # function to return correct view-file name corresponding to the concerned TAB...
    public function get_display_file($tab=null) 
    {
        
        try
        {
            $view_filename = '';
            switch($tab) {
                case 'personal': $view_filename = 'admin/members/display_ajax/display_personal_info_form_AJAX.phtml';
                                 break;
                case 'basic'   : $view_filename = 'admin/members/display_ajax/display_basic_info_form_AJAX.phtml';
                                 break;
                case 'edu'     : $view_filename = 'admin/members/display_ajax/display_edu_info_form_AJAX.phtml';
                                 break;
                case 'work'    : $view_filename = 'admin/members/display_ajax/display_work_info_form_AJAX.phtml';
                                 break;
                case 'skills'  : $view_filename = 'admin/members/display_ajax/display_skill_info_form_AJAX.phtml';
                                 break;
                case 'balance' : $view_filename = 'admin/members/display_ajax/display_balance_info_form_AJAX.phtml';
                                 break;
                case 'subscription'  : $view_filename = 'admin/members/display_ajax/display_subscription_info_form_AJAX.phtml';
                                 break;
               case 'church'  : $view_filename = 'admin/members/display_ajax/display_church_info_form_AJAX.phtml';
               break;
                default        : $view_filename = 'admin/members/display_ajax/display_personal_info_form_AJAX.phtml';
                                 break;
            }
            
            
            return $view_filename;
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }
	

    
    # function to return correct view-file name corresponding to the concerned TAB [for EDIT]...
    public function get_edit_part_file($tab=null) 
    {
        
        try
        {
            $view_filename = '';
            switch($tab) {
                case 'personal': $view_filename = 'admin/members/edit_part_ajax/edit_personal_info_form_AJAX.phtml';
                                 break;
                case 'basic'   : $view_filename = 'admin/members/edit_part_ajax/edit_basic_info_form_AJAX.phtml';
                                 break;
                case 'edu'     : $view_filename = 'admin/members/edit_part_ajax/edit_edu_info_form_AJAX.phtml';
                                 break;
                case 'work'    : $view_filename = 'admin/members/edit_part_ajax/edit_work_info_form_AJAX.phtml';
                                 break;
                case 'skills'  : $view_filename = 'admin/members/edit_part_ajax/edit_skill_info_form_AJAX.phtml';
                                 break;
                case 'balance' : $view_filename = 'admin/members/edit_part_ajax/edit_balance_info_form_AJAX.phtml';
                                 break;
                case 'subscription'  : $view_filename = 'admin/members/edit_part_ajax/edit_subscription_info_form_AJAX.phtml';
                                 break;
                default        : $view_filename = 'admin/members/edit_part_ajax/edit_personal_info_form_AJAX.phtml';
                                 break;
            }
            
            
            return $view_filename;
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }


    
    # function to return correct view-file name corresponding to the concerned TAB [for DISPLAY]...
    public function get_just_display_file($tab=null) 
    {
        
        try
        {
            $view_filename = '';
            switch($tab) {
                case 'personal': $view_filename = 'admin/members/view_ajax/view_personal_info_form_AJAX.phtml';
                                 break;
                case 'basic'   : $view_filename = 'admin/members/view_ajax/view_basic_info_form_AJAX.phtml';
                                 break;
                case 'edu'     : $view_filename = 'admin/members/view_ajax/view_edu_info_form_AJAX.phtml';
                                 break;
                case 'work'    : $view_filename = 'admin/members/view_ajax/view_work_info_form_AJAX.phtml';
                                 break;
                case 'skills'  : $view_filename = 'admin/members/view_ajax/view_skill_info_form_AJAX.phtml';
                                 break;
                case 'balance' : $view_filename = 'admin/members/view_ajax/view_balance_info_form_AJAX.phtml';
                                 break;
                case 'subscription'  : $view_filename = 'admin/members/view_ajax/view_subscription_info_form_AJAX.phtml';
                                 break;
                default        : $view_filename = 'admin/members/view_ajax/view_personal_info_form_AJAX.phtml';
                                 break;
            }
            
            
            return $view_filename;
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }
    
    
}   // end of controller
