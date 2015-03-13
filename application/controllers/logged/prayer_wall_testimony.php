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


class Prayer_wall_testimony extends Base_controller
{
    
    private $testimony_pagination_per_page =  10 ;
    
    
    public function __construct()
     {
         
        try
        {
            parent::__construct();
               parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...

            //$this->load->model('users_model');
            //$this->load->model('holy_place_model');
            //$this->load->model('bible_fruits_model');
            //$this->load->model('prayer_wall_photos_model');
            $this->load->model('prayer_wall_model');
            //$this->load->model('prayer_commit_model');
            
            $this->i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    
    public function index() 
    {
        try
        {
                  
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
            $this->data["MAIN_MENU_SELECTED"] = 6;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery.dd.js',
                                        'js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',
                                        'js/tab.js',*/
                                        'js/production/prayer_wall.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css') );
          
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
            $data['testimony_pagination_per_page'] =  $this->testimony_pagination_per_page;
            
			
			$this->session->set_userdata('filter_prayer_req' ,'');
            
            
            ob_start();
            $this->prayer_request_testimony_ajax_pagination();
            $content = ob_get_contents();
            $content_obj = json_decode($content);
            $data['prayer_req_ajax_content'] = $content_obj->html; 
            $data['no_of_result'] = $content_obj->no_of_result;
            $data['view_more'] = $content_obj->view_more;
            ob_end_clean();
            
            
            # view file...
            
            $VIEW = "logged/holy_place/testimony/prayer_wall_testimony.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }
    
    
    public function prayer_request_testimony_ajax_pagination($page=0)
    {
        

        $cur_page = $page + $this->testimony_pagination_per_page;
        
        $data = $this->data;
        $where = "";
		$non_exact_where = "";
		$s_non_exact_where = "";
        
    
     
		  $WHERE = "";
	
		 if($this->input->post('if_posted')=='y')
		 {
			 
			
			 $key_word = $this->input->post('key_word');
			 if($key_word =='view_all')
			 {
				 $WHERE = "";
			 }
			 else if($key_word == 'my_testimony')
			 {
				 $WHERE = " AND p.i_user_id={$this->i_profile_id}";
			 }
			 else if($key_word == 'my_commitment')
			 {
				 $WHERE = " AND c.i_user_id={$this->i_profile_id}";
			 }
			
			 //echo $WHERE;
			 $this->session->set_userdata('filter_prayer_req',$WHERE);
			 
		 }
     
     
        if($this->session->userdata('search_fo_prayer_req')!='')
          $where = "AND ( ".$this->session->userdata('search_fo_prayer_req') ." )";
        if($this->session->userdata('search_non_eaxct_req')!='')
         $s_non_exact_where =  "AND ( ". $this->session->userdata('search_non_eaxct_req')." )"; //exit;
       
        $WHERE = $this->session->userdata('filter_prayer_req');
		$result = $this->prayer_wall_model->get_tesimony_list($WHERE, '',intval($page), $this->testimony_pagination_per_page);
		$total_rows = $this->prayer_wall_model->gettotal_testimony_list($WHERE);
       
        $data['arr_testimony'] = $result;
        $data['no_of_result'] = $total_rows;
        $data['current_page'] = $cur_page;
        $data['profile_id'] = $i_user_id;
        
         //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
           if($rest_counter<=$this->testimony_pagination_per_page)
              $view_more = false;
         //--------- end check
        
        
        $VIEW_FILE = "logged/holy_place/testimony/prayer_wall_testimony_ajax.phtml";
        
      
        $content = $this->load->view( $VIEW_FILE , $data, true);
        echo json_encode( array('html'=>$content, 'no_of_result'=>$data['no_of_result'],'view_more'=>$view_more, 'cur_page'=>$data['current_page']) );
            
    }
    
    
    
    public function edit_testimony()
    {
        $info['s_description'] = get_formatted_string(trim($this->input->post('txt_area_msg')));
        $id = $this->input->post('h_testimony_id');
        if($info['s_description']=='')
        {
            $result = 'blank';
            echo json_encode(array('result'=>$result,'id'=>$id));
            exit;
        }
        else
        {
            $this->prayer_wall_model->update_testimony($info,$id);
            $result = 'success';
            $msg = 'Testimony successfully updated.';
            echo json_encode(array('result'=>$result,'msg'=>$msg,'id'=>$id,'content'=>nl2br($info['s_description'])));
        }
    }
}   // end of controller
