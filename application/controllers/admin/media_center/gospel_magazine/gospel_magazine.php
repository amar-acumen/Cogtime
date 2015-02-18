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
* @link model/ring_categories_model.php
* @link views/##
*/

class Gospel_magazine extends Admin_base_Controller
{
   
   
    private $magazine_pagination_per_page =  20 ;
   
    // constructor definition...
    function __construct()
    {
        try
        {
            parent::__construct();
            parent::_check_admin_login();
            
            # configuring paths...
                        
            # loading reqired model & helpers...
            // $this->load->helper('###');
           //$this->load->model("ring_categories_model");
           
           $this->logged_admin_id = $this->session->userdata('loggedin');
            
            
           //$this->load->model("landing_page_cms_model");
           $this->load->model("gospel_magazine_model");
           $this->load->helper('common_option_helper.php');
           
           
           $this->upload_path = BASEPATH.'../uploads/media_center_videos/';
           
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    // "index" function definition...
    public function index($page) 
    {

        try
        {
            //echo "page : ".$page;
            # adjusting header & footer sections [Start]...
            $data = $this->data;
            parent::_set_title("::: COGTIME Xtian network :::");
            parent::_set_meta_desc("::: COGTIME Xtian network :::");
            parent::_set_meta_keywords("::: COGTIME Xtian network :::");
            parent::_add_js_arr( array( 'js/lightbox.js',
                                        'js/jquery.dd.js',
                                        'js/jquery.form.js',
                                        'js/jquery/JSON/json2.js'
                                        ) );
                                        
             parent::_add_css_arr( array('css/dd.css'
                                        ) );
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 5;
            $data['submenu'] = 5;
            
            
            #$this->session->set_userdata('where','');
            #$this->session->set_userdata('order_by','');
             //// Page
            $URI_SEGMENT = $this->uri->segment(2);
            $page = ( is_numeric($URI_SEGMENT) )? $URI_SEGMENT: 0;
            

            if( !is_numeric($URI_SEGMENT) ) {
                /*$this->session->unset_userdata('current_page_intercession');
                $this->session->unset_userdata('search_condition');
                $this->session->unset_userdata('search_key_from');
                $this->session->unset_userdata('search_key_to');
                */
                $this->session->unset_userdata('current_page_magazine');
                
                $this->session->unset_userdata('where_magazine');
            }
           
            //// Page
            
            #$current_page = ($this->session->userdata('current_page_magazine')!='')? $this->session->userdata('current_page_magazine'):$page;
            $data['current_page'] = $page;
           
           
            ### ajax call ###
            ob_start();
            $this->magazine_listing_ajax($page);
            $data['content'] = ob_get_contents();
            ob_end_clean();
            ### end ajax call ###

            # rendering the view file...
            $VIEW_FILE = "admin/media_center/gospel_magazine/gospel_magazine.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }//end of index
    
    
    public function magazine_listing_ajax($page=0)
    {
         try
         {
             //echo "session : ".$this->session->userdata('current_page_magazine')."--";
             //echo $page;
            $data = $this->data; 
            $where    = "" ;
            $order_by = '';
            
            
            //$this->session->set_userdata('order_by','');
            
            if($_POST && $_POST['hd_val'] == 'Y')
            {
                $this->session->set_userdata('where_magazine','');
                
                $title = $this->input->post('title');
                $published_by = $this->input->post('published_by');
                $date_from = $this->input->post('date_from');
                $date_to = $this->input->post('date_to');
                
                if($title!='')
                {
                    $where=  " AND m.s_title LIKE '%".$title."%' ";
                }
                
               $where = ($where=='')? " AND m.i_approve = 1" : $where." AND m.i_approve = 1";
                
                if($published_by!='')
                {
                    $where.= " AND m.s_publisher LIKE '{$published_by}%'";
                }
                
                
                if($this->input->post('date_from') != ''){
                     $dt_start_date = get_db_dateformat($this->input->post('date_from'));
                    $where .= " AND (DATE(m.dt_posted_on) >='".$dt_start_date."' )";
                }
                if($this->input->post('date_to') != ''){
                     $dt_end_date = get_db_dateformat($this->input->post('date_to'));
                    $where .= " AND (DATE(m.dt_posted_on) <='".$dt_end_date."' )";
                }
                
                
                $this->session->set_userdata('where_magazine',$where);
            }   // end post
            

            
            $s_where = $this->session->userdata('where_magazine');
            $order_by = $this->session->userdata('order_by');
            
            
            $result = $this->gospel_magazine_model->get_magazines($s_where ,$page,$this->magazine_pagination_per_page,$order_by);
            // echo 'res :'.count($result);                                                   
            
            $resultCount = count($result);
            $total_rows = $this->gospel_magazine_model->get_count_magazines($s_where); 
            if(count($result)==0 && $total_rows)
            {
                $page= $page - $this->magazine_pagination_per_page;
                $result = $this->gospel_magazine_model->get_magazines($s_where ,$page,
                                                                $this->magazine_pagination_per_page);
                
            }
    
    //pr($result);         
    //echo "total : ".$total_rows;exit;

    
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/media_center/gospel_magazine/gospel_magazine/magazine_listing_ajax";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->magazine_pagination_per_page;
            $config['uri_segment'] =  ($this->session->userdata('current_page_magazine')!='')? 2: 6;
            $config['num_links'] = 9;
            $config['page_query_string'] = false;
            
            $config['prev_link'] = '&laquo; Previous';
            $config['next_link'] = 'Next &raquo;';

            $config['cur_tag_open'] = '<li> <span><a href="javascript:void(0)" class="select">';
            $config['cur_tag_close'] = '</a></span></li>';

            $config['next_tag_open'] = '<li><a href="javascript:void(0)">';
            $config['next_tag_close'] = '</a></li>';

            $config['prev_tag_open'] = '<li><a href="javascript:void(0)">';
            $config['prev_tag_close'] = '</a></li>';

            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';
            
            

            $config['div'] = '#table_contents'; /* Here #content is the CSS selector for target DIV */
            #$config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
            #$config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */


            $this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();

            
            $data['result_arr'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $page;
            $data['total_pages'] = ceil($total_rows/$this->magazine_pagination_per_page);
          
             $p = ($page/$this->magazine_pagination_per_page);
             $data['current_loaded_page_no'] =  $p + 1;
            
            # rendering the view file...
            $VIEW_FILE = "admin/media_center/gospel_magazine/gospel_magazine_online_articles_ajax.phtml";
            echo $this->load->view($VIEW_FILE, $data,true);
            //return $html;
            
        } 
        catch(Exception $err_obj)
        {
            
        } 
    
    }
    
    
    
    
    //--------------------------------------- delete news ----------------------------------------------
    function delete_article($page=0)
    {
        $id = $this->input->post('id');
        #$current_page = $this->input->post('current_page');
        $current_page = $page;
        $this->gospel_magazine_model->delete_article_by_article_id($id);
		$this->gospel_magazine_model->delete_likes_by_project_id($id);
		$this->gospel_magazine_model->delete_comments_by_project_id($id);
        #$this->session->set_userdata('current_page',$current_page);
        ### ajax call ###
        ob_start();
        $this->magazine_listing_ajax($current_page);
        $html = ob_get_contents();
        ob_end_clean();
        ### end ajax call ###
        
        
        echo json_encode(array('success'=>true,
                                'msg'=>'The news deleted successfully.',
                                'html'=>$html
                                ));
    }
    
    
    //--------------------------------------- end of delete news ----------------------------------------------
    
    
    
    //------------------------------- fetch like n comment --------------------------------------------
    public function fetch_likes_on_magazine($magazine_id='')
	{
		try
		  {
			 $data = $this->data;  
			 
			 ob_start();
			 $this->magazine_likes_ajax_pagination($magazine_id);
			 $data['people_liked_list'] = ob_get_contents();
			 ob_end_clean();
			 
			  $VIEW = "logged/media_center/liked_by_lightbox.phtml";
              #parent::_render($data, $VIEW); 
			  $html = $this->load->view($VIEW, $data, true);  
			  echo json_encode( array('result'=>'success','html_data'=>$html) );
		   } 
			catch(Exception $err_obj)
			{
			  show_error($err_obj->getMessage());
			} 
			
	} 
	
	public function magazine_likes_ajax_pagination($magazine_id , $page=0) 
	{
		 try
		 {
		    $data = $this->data;  
			$wh	= " AND i_magazine_id='".$magazine_id."'";
			$result = $this->gospel_magazine_model->get_gospel_likes($wh ,$page,$this->comments_pagination_per_page);
			
		    $resultCount = count($result);
			$total_rows = $this->gospel_magazine_model->get_count_gospel_likes($wh);
			#pr($result);exit; 		

			$this->load->library('jquery_pagination');
			$config['base_url'] = base_url()."logged/media_center/project_likes_ajax_pagination/{$magazine_id}";
			$config['total_rows'] = $total_rows;
			$config['per_page'] = $this->comment_pagination_per_page;
			$config['uri_segment'] = 5;
			$config['num_links'] = 9;
			$config['page_query_string'] = false;
			
			$config['prev_link'] = '&laquo; Previous';
			$config['next_link'] = 'Next &raquo;';

			$config['cur_tag_open'] = '<li> <span><a href="javascript:void(0)" class="select">';
			$config['cur_tag_close'] = '</a></span></li>';

			$config['next_tag_open'] = '<li><a href="javascript:void(0)">';
			$config['next_tag_close'] = '</a></li>';

			$config['prev_tag_open'] = '<li><a href="javascript:void(0)">';
			$config['prev_tag_close'] = '</a></li>';

			$config['num_tag_open'] = '<li>';
			$config['num_tag_close'] = '</li>';
			
			

			$config['div'] = '#view_comments'; /* Here #content is the CSS selector for target DIV */
			#$config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
			#$config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */


			$this->jquery_pagination->initialize($config);
			$data['page_links'] = $this->jquery_pagination->create_links();

			
			$data['result_arr'] = $result;//check_friend_netpal_status($result);
			
			$data['no_of_result'] = $total_rows;
			$data['current_page'] = $page;
			$data['total_pages'] = ceil($total_rows/$this->comment_pagination_per_page);
		  
			$p = ($page/$this->comment_pagination_per_page);
			$data['current_loaded_page_no'] =  $p + 1;
			
			# rendering the view file...
			$VIEW_FILE = "logged/media_center/liked_by_lightbox_ajax.phtml";
			$this->load->view($VIEW_FILE, $data);
			//return $html;
			
		} 
        catch(Exception $err_obj)
        {
            
        } 
	
    }
    
     
    
    
    public function fetch_comment_on_gospel_magazine($magazine_id='')
	{
		try
		  {
			 $data = $this->data;  
			 
			 ob_start();
			 $this->magazine_comments_ajax_pagination($magazine_id);
			 $data['comments_list'] = ob_get_contents();
			 ob_end_clean();
			 
			  $VIEW = "logged/media_center/my_christan_project_comments_lightbox.phtml";
              #parent::_render($data, $VIEW); 
			  $html = $this->load->view($VIEW, $data, true);  
			  echo json_encode( array('result'=>'success','html_data'=>$html) );
		   } 
			catch(Exception $err_obj)
			{
			  show_error($err_obj->getMessage());
			} 
			
	} 
	
	public function magazine_comments_ajax_pagination($magazine_id , $page=0) 
	  {
		 try
		 {
		    $data = $this->data;  
			$wh	= " AND i_magazine_id='".$magazine_id."'";
			$result = $this->gospel_magazine_model->get_gospel_cmnts($wh ,$page,$this->comments_pagination_per_page);
			
		    $resultCount = count($result);
			$total_rows = $this->gospel_magazine_model->get_count_gospel_cmnts($wh);
			//pr($result); 		

			$this->load->library('jquery_pagination');
			$config['base_url'] = base_url()."logged/media_center/magazine_comments_ajax_pagination/{$magazine_id}";
			$config['total_rows'] = $total_rows;
			$config['per_page'] = $this->comment_pagination_per_page;
			$config['uri_segment'] = 5;
			$config['num_links'] = 9;
			$config['page_query_string'] = false;
			
			$config['prev_link'] = '&laquo; Previous';
			$config['next_link'] = 'Next &raquo;';

			$config['cur_tag_open'] = '<li> <span><a href="javascript:void(0)" class="select">';
			$config['cur_tag_close'] = '</a></span></li>';

			$config['next_tag_open'] = '<li><a href="javascript:void(0)">';
			$config['next_tag_close'] = '</a></li>';

			$config['prev_tag_open'] = '<li><a href="javascript:void(0)">';
			$config['prev_tag_close'] = '</a></li>';

			$config['num_tag_open'] = '<li>';
			$config['num_tag_close'] = '</li>';
			
			

			$config['div'] = '#view_comments'; /* Here #content is the CSS selector for target DIV */
			#$config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
			#$config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */


			$this->jquery_pagination->initialize($config);
			$data['page_links'] = $this->jquery_pagination->create_links();

			
			$data['result_arr'] = check_friend_netpal_status($result);
			$data['no_of_result'] = $total_rows;
			$data['current_page'] = $page;
			$data['total_pages'] = ceil($total_rows/$this->comment_pagination_per_page);
		  
			 $p = ($page/$this->comment_pagination_per_page);
			 $data['current_loaded_page_no'] =  $p + 1;
			
			# rendering the view file...
			$VIEW_FILE = "logged/media_center/my_christan_project_comments_lightbox_ajax.phtml";
			$this->load->view($VIEW_FILE, $data);
			//return $html;
			
		} 
        catch(Exception $err_obj)
        {
            
        } 
	
    }
    //------------------------------- end fetch like n comment --------------------------------------------
    
    
    function remove_like()
    {
        $record_id = $this->input->post('comment_record_id');
        $news_id = $this->input->post('news_id');
        
        
        
        $this->landing_page_cms_model->remove_from_news_like_list($record_id);
        
        $likes = $this->landing_page_cms_model->get_total_people_liked_by_news_id($news_id);
        
        echo json_encode(array('total_likes'=>$likes));
        
    }
    
    function remove_comment()
    {
        $record_id = $this->input->post('comment_record_id');
        $news_id = $this->input->post('news_id');
        
        $this->landing_page_cms_model->remove_from_news_comment_list($record_id);
        
        $comments = $this->landing_page_cms_model->get_total_comment_by_news_id($news_id);
        
        echo json_encode(array('total_comments'=>$comments));
    }
    
    
    function add_new()
    {
        if($_POST)
        {
            $error_msg = array();
            $info['dt_posted_on'] = get_db_datetime();
            $info['i_posted_by'] = $this->logged_admin_id;
            $info['i_approve'] = 1;
            
            $info['s_title'] = trim(get_formatted_string($this->input->post('txt_title')));
            $info['s_publisher'] = trim(get_formatted_string($this->input->post('txt_publisher')));
            $info['s_description'] = trim(get_formatted_string($this->input->post('elm1')));
            
            if($info['s_title']=='')
            {
                $error_msg['err_title'] = '* Required Field';
            }
            if($info['s_publisher']=='')
            {
                $error_msg['err_publisher'] = '* Required Field';
            }
            if($info['s_description']=='')
            {
                $error_msg['err_desc'] = '* Required Field';
            }
            
            if(count($error_msg))
            {
                $result = 'failure';
                echo json_encode(array(
                                        'result' => $result,
                                        'error' => $error_msg
                                        )
                                );
                exit;
            }
            else
            {
                $last_id = $this->gospel_magazine_model->add_new_article($info);
                
                if($last_id)
                {
                    $result = 'success';
                    $msg = "New Article successfully inserted.";
                    
                }
                else
                {
                    $result = "error";
                    $msg = 'Sorry. Error occured. Please try again.';
                }
                echo json_encode(array('result'=>$result,'msg'=>$msg));
                exit;
            }
            
        }
    }
    
}// end of controller
    