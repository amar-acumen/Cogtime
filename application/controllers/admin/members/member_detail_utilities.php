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
           $this->load->model("admins_user_model");
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
    
    function autologin_church(){
        /************admin logout*******************/
        session_destroy(); 
        /********************************/
        $user_id = $this->input->post('user_id');
        $church_id = $this->input->post('church_id');
        $type = $this->input->post('type');
        if($type == 'admin'){
            
            $info = $this->users_model->fetch_this($user_id);
        $USER_ID = $user_id;
         if ($info['i_status'] == 1) {
             
             
             get_all_church_session($churchid);
               $this->session->set_userdata('login_referrer', '');
                    $this->session->set_userdata('loggedin', true);
                    $this->session->set_userdata('user_id', encrypt($info["id"]));
                    $this->session->set_userdata('username', $info["s_first_name"]);
                    $this->session->set_userdata('user_type', $info["i_user_type"]);
                    $this->session->set_userdata('email', $info["s_email"]);
                    $this->session->set_userdata('user_lastname', $info["s_last_name"]);
                    $this->session->set_userdata('is_admin', $info["i_is_admin"]);
                    $this->session->set_userdata('upassword', $info["s_password"]);
                    $this->session->set_userdata('IMuserid', ($ret_["id"]));
                    $this->session->set_userdata('s_profile_photo', ($info['s_profile_photo']));
                    $this->session->set_userdata('e_gender', ($info['e_gender']));
					$this->session->set_userdata('s_time', ($info['s_time']));
					$this->session->set_userdata('s_bio', ($info['s_bio']));
                    $this->session->set_userdata('unique_username', $info["s_profile_url_suffix"]);
                    $this->session->set_userdata('display_username', $info["s_chat_display_name"]);
                    $this->session->set_userdata('s_tweet_bg_img', $info["s_tweet_bg_img"]);
                     $this->session->set_userdata('s_tweet_id', ($info['s_tweet_id']));
                      $this->session->set_userdata('s_profile_name', ($info['s_profile_name']));
					$this->session->set_userdata('s_chat_display_name', $info["s_chat_display_name"]);
					$this->session->set_userdata('e_want_net_pal', $info["e_want_net_pal"]);
					$this->session->set_userdata('e_want_prayer_partner', $info["e_want_prayer_partner"]);
					$this->session->set_userdata('is_pr_partner_q_mail_sent', $info["is_pr_partner_q_mail_sent"]);
					$this->session->set_userdata('is_netpal_q_mail_sent', $info["is_netpal_q_mail_sent"]);
					$this->session->set_userdata('s_timezone_text', $info["s_timezone_text"]);
                    //$_SESSION['username'] = 'jhon';
                    $this->session->set_userdata('is_first_login_checked', 'false');
                        //$this->mail_contents_model->get_by_name("acknowledgement");

                    $this->users_model->set_user_online($info["id"], $_SERVER['REMOTE_ADDR']);
                    $loc = base_url().get_church_dashboard_url_by_church_id($church_id).'?autologin=1';
            //header("location:" . $loc);
            echo json_encode(array('url' => $loc , 'result' => true));
             
         }else {
           
               $loc = base_url().'admin/members/member_details/index/'.$user_id.'/1';
                echo json_encode(array('url' => $loc , 'result' => false));  
          //  header("location:" . $loc);
            
         }
    
        
            
            
        }
        if($type == 'member'){
             $info = $this->users_model->fetch_this($user_id);
        $USER_ID = $user_id;
         if ($info['i_status'] == 1) {
             
             
             get_all_church_session($churchid);
               $this->session->set_userdata('login_referrer', '');
                    $this->session->set_userdata('loggedin', true);
                    $this->session->set_userdata('user_id', encrypt($info["id"]));
                    $this->session->set_userdata('username', $info["s_first_name"]);
                    $this->session->set_userdata('user_type', $info["i_user_type"]);
                    $this->session->set_userdata('email', $info["s_email"]);
                    $this->session->set_userdata('user_lastname', $info["s_last_name"]);
                    $this->session->set_userdata('is_admin', $info["i_is_admin"]);
                    $this->session->set_userdata('upassword', $info["s_password"]);
                    $this->session->set_userdata('IMuserid', ($ret_["id"]));
                    $this->session->set_userdata('s_profile_photo', ($info['s_profile_photo']));
                    $this->session->set_userdata('e_gender', ($info['e_gender']));
					$this->session->set_userdata('s_time', ($info['s_time']));
					$this->session->set_userdata('s_bio', ($info['s_bio']));
                    $this->session->set_userdata('unique_username', $info["s_profile_url_suffix"]);
                    $this->session->set_userdata('display_username', $info["s_chat_display_name"]);
                    $this->session->set_userdata('s_tweet_bg_img', $info["s_tweet_bg_img"]);
                     $this->session->set_userdata('s_tweet_id', ($info['s_tweet_id']));
                      $this->session->set_userdata('s_profile_name', ($info['s_profile_name']));
					$this->session->set_userdata('s_chat_display_name', $info["s_chat_display_name"]);
					$this->session->set_userdata('e_want_net_pal', $info["e_want_net_pal"]);
					$this->session->set_userdata('e_want_prayer_partner', $info["e_want_prayer_partner"]);
					$this->session->set_userdata('is_pr_partner_q_mail_sent', $info["is_pr_partner_q_mail_sent"]);
					$this->session->set_userdata('is_netpal_q_mail_sent', $info["is_netpal_q_mail_sent"]);
					$this->session->set_userdata('s_timezone_text', $info["s_timezone_text"]);
                    //$_SESSION['username'] = 'jhon';
                    $this->session->set_userdata('is_first_login_checked', 'false');
                        //$this->mail_contents_model->get_by_name("acknowledgement");

                    $this->users_model->set_user_online($info["id"], $_SERVER['REMOTE_ADDR']);
                    $loc = base_url().$church_id.'/church-wall?autologin=1';
            //header("location:" . $loc);
            echo json_encode(array('url' => $loc , 'result' => true));
             
         }else {
             $loc = base_url().'admin/members/member_details/index/'.$user_id.'/1';
            //header("location:" . $loc);
            echo json_encode(array('url' => $loc , 'result' => false));  
         }
     
        }
        
        
        
        
    }
}   // end of controller
