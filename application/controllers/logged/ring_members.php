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

class Ring_members extends Base_controller
{
    private $ring_members_pagination_per_page= 10;
    
    public function __construct()
     {
        try
        {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...
            $this->load->helper('wall_helper');
            
            $this->load->model('users_model');
            $this->load->model('contacts_model');
            $this->load->model('ring_post_model');
            $this->load->model('my_ring_model');
            $this->load->model('netpals_model');

        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }
    
    
    public function index($i_ring_id) 
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
                                        'js/lightbox.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',
										'js/frontend/logged/tweets/tweet_utilities.js',
                                       
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css') );
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
            
             $this->session->set_userdata('search_condition','');
             
            $data['ring_id'] = $i_ring_id;
            $data['pagination_per_page'] = $this->ring_members_pagination_per_page;
            $data['profile_id'] = $i_user_id;
            
            ## fetching ring details #
            $where = " WHERE R.id  = {$i_ring_id} "; 
            $data['ring_detail_arr'] = $this->my_ring_model->get_list($where);
            ///echo $this->db->last_query();
            
            
            ob_start();
            $this->ring_members_ajax_pagination($i_ring_id);
            $content = ob_get_contents();
            $content_obj = json_decode($content);
            $data['ring_members'] = $content_obj->html; 
            $data['no_of_result'] = $content_obj->no_of_result;
            $data['view_more'] = $content_obj->view_more;
            $data['current_page'] = $content_obj->cur_page;
            ob_end_clean();
           
               
            # view file...
            $VIEW = "logged/ring/ring_members.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }   
    
    public function ring_members_ajax_pagination($i_ring_id, $page=0)
    {
        
        //echo $page;
        ## seacrh conditions : filter ############
          $WHERE_COND = '';
          
          
        
        $cur_page = $page + $this->ring_members_pagination_per_page;
        
        $data = $this->data;
        
       
       $result = $this->my_ring_model->get_all_ring_members_by_ring_id($i_ring_id, intval($page), $this->ring_members_pagination_per_page);

       $total_rows = $this->my_ring_model->get_total_all_ring_members_by_ring_id($i_ring_id);
       
        $data['arr_rings'] = $result;
        $data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
        $data['profile_id'] = $i_user_id;
        
         //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
           if($rest_counter <= $this->ring_members_pagination_per_page)
           {
              $view_more = false; 
           }
         //--------- end check
        
       
        $VIEW_FILE = "logged/ring/ajax_ring/ajax_ring_members_listing.phtml";
        
        if( is_array($result) && count($result) ) {
            $content = $this->load->view( $VIEW_FILE , $data, true);
        }
        else {
            $content = '<div class="view_more" style="text-align: center;"><p class="blue_bold12" style="font-size:14px;">No member.</p></div>';
        }
        
        echo json_encode( array('html'=>$content, 'no_of_result'=>$data['no_of_result'],'view_more'=>$view_more, 'cur_page'=>$data['current_page_1']) );
            
    }
    
    
    
    function delete_member()
    {
       $id= $this->input->post('table_id');
	  
	   $sql = "SELECT * FROM cg_ring_invited_user WHERE id = ".$id;
	   $query = $this->db->query($sql); #echo $this->db->last_query(); exit;
	   $result_arr = $query->result_array(); 
	   #pr( $result_arr,1);
       
	    $this->social_notifications_message(intval(decrypt($this->session->userdata('user_id'))), $result_arr[0]['i_invited_id'], 'ring_member_delete', $result_arr[0]['i_ring_id']) ; 
		$this->my_ring_model->delete_ring_member_by_id($id);
        echo json_encode(array("response"=>true, "msg"=>"The member successfully removed."));
        
    }
    
    
    
    
}   // end of controller