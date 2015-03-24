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


class Church_members extends Base_controller
{
    
    private $pagination_per_page =  20;
//    private $comments_pagination_per_page =  2 ;
//    private $people_liked_pagination_per_page =  4 ;
//   
    
    public function __construct()
     {
	 	
        try
        {
            parent::__construct();
            parent::_add_church_css_arr (array('css/church.css'));
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers... 

            $this->load->model('church_new_model');
			$this->load->helper('my_utility_helper');
			$this->i_profile_id = intval(decrypt($this->session->userdata('user_id')));
			$this->upload_path = BASEPATH . '../uploads/church_logo_image/';
			$this->upload_cover_path = BASEPATH.'../uploads/church_cover_image/';
                        // $this->load->helper('Imagelibrary_helper');
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
			$user_id = intval(decrypt($this->session->userdata('user_id')));
			$posted=array();
			$this->data["posted"]=$posted;/*don't change*/    
			$data = $this->data;      
	//               $this->data["MAIN_MENU_SELECTED"] = 1;
			parent::_set_title('::: COGTIME Xtian network :::');
			parent::_set_meta_desc('');
			parent::_set_meta_keywords('');
			
			/*$where = 'AND cm.is_approved=1';
			$data['member_arr'] =$this->church_new_model->get_churchmembers($c_id,$where,'',$order_by=' cm.id DESC','');
			$data['church_arr'] =$this->church_new_model->get_church_info($c_id);
			$data['church_admin'] = $this->church_new_model->get_church_admin_data($c_id);*/
			
			$this->session->set_userdata('where','');
			$this->session->set_userdata('serach_alphabet', '');
			$this->session->set_userdata('search_data', '');
			
            
            # view file...
			ob_start();
			$content = $this->generate_member_listing_AJAX();
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['listingContent'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
			//var_dump($data['listingContent']);
			$VIEW = "logged/church/members.phtml";
			parent::_render($data, $VIEW);
		}
        catch(Exception $err_obj)
        {
           
        } 

    } // end of index   
	
	public function members_search_result() 
    {
       //die('comming soon.........');
        try
        {
			$c_id = $_SESSION['logged_church_id'];
			$serach_alphabet = $this->input->post('serach_alphabet');
			if ($serach_alphabet != '') {
				$this->session->set_userdata('serach_alphabet', $this->input->post('serach_alphabet'));
			} else {
				$this->session->set_userdata('search_data', $this->input->post('search_data'));
			}
			 # view file...
			ob_start();
			$content = $this->generate_member_listing_AJAX();
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['listingContent'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
			
			echo $data['listingContent'];	
		}
        catch(Exception $err_obj)
        {
           
        } 

    }
	
	
	public function generate_member_listing_AJAX($page=0)
    {
		$c_id = $_SESSION['logged_church_id'];
		$s_where = " AND cm.is_approved=1 AND is_blocked = 1 AND is_leave = 0";
		 if (isset($_SESSION['search_data']) && $_SESSION['search_data'] != '') {
			$s_where .= ' AND (u.s_first_name LIKE "%'.$_SESSION['search_data'].'%" OR u.s_last_name LIKE "%'.$_SESSION['search_data'].'%" OR CONCAT(u.s_first_name, " ", u.s_last_name) LIKE "%'.$_SESSION['search_data'].'%")';
		//$data['ringdata']	= $this->church_new_model->get_churchmembers($c_id,$s_where, '', $order_by, '');
		 } else {
			$s_where .= ' AND u.s_first_name LIKE "'.$_SESSION['serach_alphabet'].'%"';
		 }
		//$wh1	= " AND inv.i_invited_id='".$this->i_profile_id."'";
		$order_by = 'cm.id DESC';
		$data['ringdata']	= $this->church_new_model->get_churchmembers($c_id,$s_where, $page, $order_by, $this->pagination_per_page);
		//$data['ringdata']	= check_friend_netpal_status($data['ringdata']);
		
		$data['pagination_per_page'] = $this->pagination_per_page;
		//pr($result);
		$resultCount = count($data['ringdata']);
		$total_rows = $this->church_new_model->get_churchmembers_count($c_id);
		$cur_page = $page + $this->pagination_per_page;
        
       
		// getting auction-category listing...
		$data['no_of_ring'] = $total_rows;
		$data['no_of_result'] = $total_rows;
		$data['current_page_1'] = $cur_page;

		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->pagination_per_page)
			  $view_more = false;
         //--------- end check
        # loading the view-part...
        $AJAX_VIEW_FILE = 'logged/church/ajax_church_members_list.phtml';
        
   //pr($data['ringdata']);
		
		if( $total_rows>0 ) {
        	$listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);
		}
		else {
			$listingContent = '';
		}
		//echo 1; exit;
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$listingContent, 
								'current_page'=>$cur_page, 
								'no_of_result'=>$data['no_of_result'],
								'total'=>$total_rows,
								'view_more'=>$view_more ,
								'cur_page'=>$data['current_page_1']) );
    }   
}   // end of controller...

