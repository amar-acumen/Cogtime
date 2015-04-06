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
    private $comments_pagination_per_page =  2 ;
//    private $people_liked_pagination_per_page =  4 ;
//   
    
    public function __construct()
     {
	 	
        try
        {
            parent::__construct();
            parent::_add_church_css_arr (array('css/church.css','css/wall-slider.css','css/jquery-ui-1.8.2.custom.css','css/jquery-ui.css'));
			parent::_add_js_arr(array(
			'js/church-wall-slider.js'
		  ));
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers... 


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
			$this->session->set_userdata('search_condition', '');
			//$data['church_activity_feed_comments'] = $this->church_new_model->get_church_activity_feed_comments($c_id);
			//$data['church_activity_ring_comments'] = $this->church_new_model->get_church_activity_comments($c_id);
			
			//pr($data['church_activity_feed_comments']);
			## comments ##
		  $data['church_admin'] = $this->church_new_model->get_church_admin_data($c_id);
          ob_start();
          $this->comments_pagination_show_more($c_id);
          $content = ob_get_contents();
          $content_obj = json_decode($content);
          $data['church_activity_feed_comments'] = $content_obj->html;
		  $data['no_of_result']=$content_obj->no_of_result;
		  $data['current_page_1']=$content_obj->current_page;
          ob_end_clean();

          ## END Comments ##
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
	
	public function comments_pagination_show_more($c_id,$page=0)
	{
		//$comments=$this->church_new_model->get_church_activity_feed_comments($c_id);
		$cur_page = $page + $this->comments_pagination_per_page;
        $data = $this->data;

		//echo $cur_page;
		//echo $this->pagination_per_page;exit;
		$i_session_user_id = intval(decrypt($this->session->userdata('user_id')));
		if (isset($_SESSION['search_condition']) && $_SESSION['search_condition'] != ''){
        $result = $this->church_new_model->get_church_activity_feed_comments($c_id,$_SESSION['search_condition'], $page, $this->comments_pagination_per_page);
        $total_rows = $this->church_new_model->get_church_activity_feed_comments_count($c_id,$_SESSION['search_condition']);
		}
		else
		{
			$result = $this->church_new_model->get_church_activity_feed_comments($c_id,'', $page, $this->comments_pagination_per_page);
			$total_rows = $this->church_new_model->get_church_activity_feed_comments_count($c_id,'');
		}
		//pr($result);
        $data['result_arr'] = $result;
        $data['no_of_result'] = $total_rows;

        $data['current_page_1'] = $cur_page;
        $data['profile_id'] = $c_id;

        $VIEW_FILE = "logged/church/ajax_activity/church_activity_management_ajax.phtml";

        if (is_array($result) && count($result)) {
            $content = $this->load->view($VIEW_FILE, $data, true);
        } else {
            $content = '';
        }

        //echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode(array('html' => $content, 'current_page' => $cur_page,'no_of_result'=>$data['no_of_result']));
	}
	
	public function comment_search_result() 
    {
       //die('comming soon.........');
        try
        {
			$c_id = $_SESSION['logged_church_id'];
			$this->session->set_userdata('search_condition', '');
			$s_where='';
			if($this->input->post('member') != '-1')
			{
				$s_where.='AND cncm.i_user_id="'.$this->input->post('member').'"';
			}
			if($this->input->post('date') != '')
			{
				
					$s_where.='AND DATE(cncm.dt_created_on)="'.$this->input->post('date').'"';
				
					
				
			}
			if($this->input->post('content') != '')
			{
				
					$s_where.='AND cncm.s_contents LIKE "%'.$this->input->post('content').'%"';
				
					
				
			}
			
			$this->session->set_userdata('search_condition', $s_where);
			 # view file...
			ob_start();
			$content = $this->comments_pagination_show_more($c_id);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['result_arr'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			$data['current_page_1'] = $content_obj->current_page;
			ob_end_clean();
			//pr($data['result_arr'],1);
			//echo $data['result_arr'];	
			echo json_encode(array('html'=>$data['result_arr'],'total'=>$data['no_of_result'],'cur_page'=>$data['current_page_1']));
		}
        catch(Exception $err_obj)
        {
           
        } 

    }
    function auto_complete(){
        $term = $_GET['term'];
       
        $sql = "SELECT u.id,u.s_first_name,u.s_last_name FROM cg_church_member chm , cg_users u  WHERE  u.id = chm.member_id AND chm.church_id ='".$_SESSION['logged_church_id']."' AND chm.is_blocked = 1 AND chm.is_leave = 0 AND  u.s_first_name LIKE '%$term%'ORDER BY u.s_first_name " ;
        $query = $this->db->query($sql);
        $res = $query->result();
       // pr($res);
        $row = array();
       foreach ($res as $row_user){
    
   //$row_user->s_first_name = trim($row_user['s_first_name']);
   // $row['id'] = $row_user->id;       
    $row_set[] = $row_user->s_first_name.' '.$row_user->s_last_name;
    // $row_set[] = $row[];
    
}
           
     echo json_encode($row_set);    
    }
}   // end of controller...

