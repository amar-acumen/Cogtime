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

class Dashboard extends Admin_base_Controller
{

	function __construct()
	 {
		try
		{
		    parent::__construct();
			parent::checkGlobalAdminLogin();
			//common-settings
		//	parent::check_SubAdmin_login();
            # loading reqired model & helpers...
            $this->load->model('users_model');
			$this->load->model('my_blog_model');
			$this->load->model('my_ring_model');
			$this->load->model('events_model');
           // $this->load->model('chat_rooms_model');
            $this->load->model('prayer_wall_model');
			$this->load->model('intercession_model');
			$this->load->model('projects_model');
			$this->load->model('bible_model');
			$this->load->model('church_model');
			$this->load->model('gospel_magazine_model');
			$this->load->model('landing_page_cms_model');
		  
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
            
            # adjusting header & footer sections [Start]...
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc("::: COGTIME Xtian network :::");
            parent::_set_meta_keywords("::: COGTIME Xtian network :::");
                
           // parent::_add_js_arr( array(''=>'header') );										
           parent::_add_css_arr( array('css/admin.css') );
		
			# adjusting header & footer sections [End]...
			/*******************************************/
           
           /****************************************************************/
			$WHERE = " WHERE i_isdeleted = 1 ";
			$data['total_user']  = $this->users_model->user_list_count($WHERE);
            $data['total_online_user']  = $this->users_model->online_user_list_count();
            $where_blogs = " WHERE i_isenabled = 1";
            $data['total_blogs'] = $this->my_blog_model->get_total_all_blogs($where_blogs);
			
			$where_ring = " WHERE i_isenabled = 1";
            $data['total_rings'] = $this->my_ring_model->get_list_count($where_ring);
			
			$where_events = " WHERE E.i_status = 1 ";
            $data['total_events'] = $this->events_model->get_list_count($where_events);
			
			//$data['total_chat_rooms'] =  $this->chat_rooms_model->get_list_count();
            $data['total_chat_rooms']='';
            //---------- holy place --------------------
            $data['total_prayer_req'] = $this->prayer_wall_model->get_count_list_prayers_request(" WHERE p.i_isenabled=1");
            $data['total_intercession'] = $this->intercession_model->get_total_intercession(" WHERE i.i_is_enable=1");
            
            $total_prayer_wall_testimonies = $this->prayer_wall_model->gettotal_testimony_list();
		    $total_intercession_testimonies = $this->intercession_model->get_total_intercession_testimony();
            $data['total_testimonies'] = $total_prayer_wall_testimonies + $total_intercession_testimonies;
            //---------- end holy place --------------------
			
			$fund_where = "WHERE  PF.i_order_status = 1";
			$data['fund_donor_list'] = $this->projects_model->get_total_fund_donated($fund_where);
			
			$p_where = "WHERE  P.i_isopened = 1";
			$data['total_project'] = $this->projects_model->get_list_count($p_where);
			//echo $this->db->last_query();
			
			$data['total_quiz'] = $this->bible_model->get_list_count();
			$data['total_church'] = $this->church_model->get_list_count();
			
			$data['total_gospel_mag']  = $this->gospel_magazine_model->get_count_magazines($wh);
			$data['total_ch_projects'] = $this->gospel_magazine_model->get_count_ch_project($wh);
			$data['total_ch_news'] = $this->landing_page_cms_model->get_total_news($where);
			$arr_videos = $this->landing_page_cms_model->get_all_videos();
			$data['total_mc_videos'] = count($arr_videos);
            
			# rendering the view file...
            $view_file = "admin/dashboard.phtml";
            parent::_render($data, $view_file);
		}
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
	} 
        function admin_logout(){
            $admin_id = $this->input->post('admin_id');
            $query = $this->db->get_where('cg_admin_user', array('id' => $admin_id));
            $result = $query->result();
            $status = $result[0]->i_status;
            if($status == 2){
                 $this->users_model->logout();
                 echo json_encode(array('result'=>'success'));
            }else{
                echo json_encode(array('result'=>'nologout'));
            }
               
        }
	
}   // end of controller...