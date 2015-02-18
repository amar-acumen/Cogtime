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


class Church_activity_management extends Base_controller
{
    
//    private $pagination_per_page =  10;
//    private $comments_pagination_per_page =  2 ;
//    private $people_liked_pagination_per_page =  4 ;
//   
    
    public function __construct()
     {
	 	
        try
        {
            parent::__construct();
            parent::_add_church_css_arr (array('css/church.css','css/wall-slider.css'));
			parent::_add_js_arr(array(
			'js/church-wall-slider.js'
		  ));
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers... 
//			$user_id = intval(decrypt($this->session->userdata('user_id')));
//                        parent::check_is_church_admin($user_id);
            $this->load->model('users_model');
			$this->load->model('user_notifications_model');
            $this->load->model('user_alert_model');
			$this->load->model('landing_page_cms_model');
            $this->load->model('church_new_model');
			$this->load->helper('my_utility_helper');
			$this->i_profile_id = intval(decrypt($this->session->userdata('user_id')));
			$this->upload_path = BASEPATH . '../uploads/church_logo_image/';
			$this->upload_cover_path = BASEPATH.'../uploads/church_cover_image/';
                        // $this->load->helper('Imagelibrary_helper');
			$this->pagination_per_page=6;
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }
	
    public function index($c_id) 
    {
       //echo $c_id;
       //die('comming soon.........');
        try
        {
			//$c_id = $_SESSION['logged_church_id'];
			
			$user_id = intval(decrypt($this->session->userdata('user_id')));
			parent::check_is_church_admin($user_id,$c_id);
			
			$posted=array();
			$this->data["posted"]=$posted;/*don't change*/    
			$data = $this->data;      
	//               $this->data["MAIN_MENU_SELECTED"] = 1;
			parent::_set_title('::: COGTIME Xtian network :::');
			parent::_set_meta_desc('');
			parent::_set_meta_keywords('');
			
			$data['church_activity_feed_comments'] = $this->church_new_model->get_church_activity_feed_comments($c_id);
			//$data['church_activity_ring_comments'] = $this->church_new_model->get_church_activity_comments($c_id);
			
			//pr($data['church_activity_feed_comments']);
			$VIEW = "logged/church/church_activity_management.phtml";

			parent::_render($data, $VIEW);
		}
        catch(Exception $err_obj)
        {
           
        } 

    } // end of index  

	public function save_comment() 
    {
       //die('comming soon.........');
        try
        {
		//echo 'SAVE COMMENT'.$this->input->post('wall_comment').$this->input->post('user_id');
		$s_wall_contents = $this->input->post('wall_comment');
		$s_ring_contents = $this->input->post('ring_comment');
		$id = $this->input->post('id');
		$post_type = $this->input->post('post_type');
		if ( $post_type == 1 ) {
			$data = array(
				   's_contents' => $s_wall_contents
				);
			$this->db->update('cg_church_newsfeed_comments', $data, array('id' => $id));
		} else {
			$data = array(
				   's_contents' => $s_ring_contents
				);
			$this->db->update('cg_church_ring_post_comments', $data, array('id' => $id));
		}
		/*if($this->db->insert_id() != '') {
			echo json_encode(array('success' => FALSE, 'msg' => 'Failed to update comment'));
		} else {
			echo json_encode(array('success' => TRUE, 'msg' => 'Comment updated!'));
		}*/
		 
		}
        catch(Exception $err_obj)
        {
           
        } 

    } 
	
	public function remove_comments() 
    {
	echo 'DELETE';
       //die('comming soon.........');
        try
        {
		$id = $this->input->post('id');
		$post_type = $this->input->post('post_type');
		if ( $post_type == 1 ) {
			$this->db->delete('cg_church_newsfeed_comments', array('id' => $id));
		} else {
			$this->db->delete('cg_church_ring_post_comments', array('id' => $id));
		}
		}
        catch(Exception $err_obj)
        {
           
        } 

    } 
	
}   // end of controller...

