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

class Christian_project extends Admin_base_Controller
{
   
   
    private $pagination_per_page =  20 ;
   
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
            $data['submenu'] = 6;
            
         
            ### ajax call ###
            ob_start();
            $this->project_listing_ajax();//$this->project_listing_ajax($page);
            $data['content'] = ob_get_contents();
            ob_end_clean();
            ### end ajax call ###
			$this->session->set_userdata('where','');
            
            # rendering the view file...
            $VIEW_FILE = "admin/media_center/christian_project/christian_project.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }//end of index
    
    
    public function project_listing_ajax($page=0)
    {
         try
         {
             //echo "session : ".$this->session->userdata('current_page_magazine')."--";
           
            $data = $this->data; 
            $where    = "" ;
            $order_by = '';

            $this->session->set_userdata('where','');
            $this->session->set_userdata('order_by','');
            
            if($_POST && $_POST['hd_val'] == 'Y')
            {
                
                
                $title = $this->input->post('title');
             
                if($title!='')
                {
                    $where=  " AND (m.s_title) like '%{$title}%'";
                }
                
                $this->session->set_userdata('where',$where);
            }   // end post
            

			
            $s_where = $this->session->userdata('where');
            
            $result = $this->gospel_magazine_model->get_ch_project($s_where ,$page,$this->pagination_per_page,$order_by);
            $resultCount = count($result);
            $total_rows = $this->gospel_magazine_model->get_count_ch_project($s_where); 
			
            //$data['versepointer']	= $this->holy_place_model->get_array_of_verse_pointer();					
			$this->load->library('jquery_pagination');
			$config['base_url'] = base_url()."admin/media_center/christian_project/project_listing_ajax";
			$config['total_rows'] = $total_rows;
			$config['per_page'] = $this->pagination_per_page;
			$config['uri_segment'] = 5;
			$config['num_links'] = 9;
			$config['page_query_string'] = false;
			$config['prev_link'] = 'PREV';
			$config['next_link'] = 'NEXT';
			
			$config['cur_tag_open'] = '<li> <span><a href="javascript:void(0)" class="select">';
			$config['cur_tag_close'] = '</a></span></li>';
			
			$config['next_tag_open'] = '<li><a href="javascript:void(0)">';
			$config['next_tag_close'] = '</a></li>';
			
			$config['prev_tag_open'] = '<li class="previous">';
			$config['prev_tag_close'] = '</li>';
		
			$config['num_tag_open'] = '<li><a href="javascript:void(0)">';
			$config['num_tag_close'] = '</a></li>';
			
			$config['first_link'] = '';
			$config['last_link'] = '';
		
	
		
			$config['div'] = '#table_contents'; /* Here #content is the CSS selector for target DIV */
			$config['js_bind'] = "showBusyScreen();"; /* if you want to bind extra js code */
			$config['js_rebind'] = "hideBusyScreen();"; /* if you want to rebind extra js code */
			
			$this->jquery_pagination->initialize($config);
			$data['page_links'] = $this->jquery_pagination->create_links();
		
			// getting   listing...
			$data['result_arr'] = $result;
			$data['no_of_result'] = $total_rows;
			$data['current_page'] = $page;
			
			$data['pagination_per_page']   =    $this->pagination_per_page;
            
            # rendering the view file...
            $VIEW_FILE = "admin/media_center/christian_project/christian_project_ajax.phtml";
            echo $this->load->view($VIEW_FILE, $data,true);
            //return $html;
            
        } 
        catch(Exception $err_obj)
        {
            
        } 
    
    }
    
    
    public function add_new_project() 
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
                                        'js/jquery/JSON/json2.js',
                                        
                                        'tiny_mce/plugins/tinybrowser/tb_tinymce.js.php',
                                        'tiny_mce/tiny_mce.js',
                                        'js/backend/cms/christian_news_tiny_mce.js'
                                        ) );
                                        
             parent::_add_css_arr( array('css/dd.css'
                                        ) );
            # adjusting header & footer sections [End]...
           $data['top_menu_selected'] = 5;
            $data['submenu'] = 6;
            
            
            
            
            
            # rendering the view file...
            $VIEW_FILE = "admin/media_center/christian_project/add_new_project.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }
    function add_new()
    {
        if($_POST)
        {
            $error_msg = array();
            $info['dt_posted_on'] = get_db_datetime();
            
            $info['s_title'] = trim(get_formatted_string($this->input->post('txt_title')));
            $info['s_description'] = trim(get_formatted_string($this->input->post('elm1')));
            
            if($info['s_title']=='')
            {
                $error_msg['err_title'] = '* Required Field';
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
                $last_id = $this->gospel_magazine_model->add_new_project($info);
                
                if($last_id)
                {
                    $result = 'success';
                    $msg = "New Project successfully inserted.";
                    
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
	
	
	
	public function edit_project($page,$article_id) 
    {

        try
        {
            $this->session->set_userdata('current_page_magazine',$page);
            
            //echo "page : ".$page;
            # adjusting header & footer sections [Start]...
            $data = $this->data;
            parent::_set_title("::: COGTIME Xtian network :::");
            parent::_set_meta_desc("::: COGTIME Xtian network :::");
            parent::_set_meta_keywords("::: COGTIME Xtian network :::");
            parent::_add_js_arr( array( 'js/lightbox.js',
                                        'js/jquery.dd.js',
                                        'js/jquery.form.js',
                                        'js/jquery/JSON/json2.js',
                                        
                                        'tiny_mce/plugins/tinybrowser/tb_tinymce.js.php',
                                        'tiny_mce/tiny_mce.js',
                                        'js/backend/cms/christian_news_tiny_mce.js'
                                        ) );
                                        
             parent::_add_css_arr( array('css/dd.css'
                                        ) );
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 5;
            $data['submenu'] = 6;
            
            
            $data['current_page'] = $page;
			$wh	= " AND m.id='".$article_id."'";
            $data['article_info'] = $this->gospel_magazine_model->get_ch_project($wh,'','','','');
            
            # rendering the view file...
            $VIEW_FILE = "admin/media_center/christian_project/edit_project.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }//end of index
    
   
    
    function edit_post_article()
    {
        if($_POST)
        {
            $error_msg = array();
            $info['dt_updated_on'] = get_db_datetime();
            //$info['i_posted_by'] = $this->logged_admin_id;
            //$info['i_approve'] = 1;
            
            $info['s_title'] = trim(get_formatted_string($this->input->post('txt_title')));
            $info['s_description'] = trim(get_formatted_string($this->input->post('elm1')));
            
            $id = $this->input->post('txt_id');
            
            if($info['s_title']=='')
            {
                $error_msg['err_title'] = '* Required Field';
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
                $this->gospel_magazine_model->edit_project($info,$id);
                
                
                $result = 'success';
                $msg = "Article successfully updated.";
                
                echo json_encode(array('result'=>$result,'msg'=>$msg));
                exit;
            }
            
        }
    }
	
	
    //--------------------------------------- delete  ----------------------------------------------
    function delete_article($page=0)
    {
        $id = $this->input->post('id');
        #$current_page = $this->input->post('current_page');
      	$current_page = $page;
        $this->gospel_magazine_model->delete_article_by_article_id($id);
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
    public function fetch_people_liked_post()
    {
        try
          {
               $data = $this->data;  
               $news_id = $this->input->post('news_id');
               
               
               $data['result'] = $this->landing_page_cms_model->get_people_liked_by_news_id($news_id);
               
               
              
              $VIEW = "admin/media_center/like_comment/like_ajax.phtml";
              #parent::_render($data, $VIEW); 
              $html = $this->load->view($VIEW, $data, true);  
              echo json_encode( array('result'=>success,'html_data'=>$html) );
           } 
        catch(Exception $err_obj)
            {
              show_error($err_obj->getMessage());
            } 
            
    } 
    
     
    
    
    public function fetch_comment_on_news()
    {
        try
          {

             $data = $this->data;  
             $news_id = $this->input->post('news_id');
             
             $data['result'] = $this->landing_page_cms_model->get_comment_by_news_id($news_id);
             
              $VIEW = "admin/media_center/like_comment/comment_ajax.phtml";
              #parent::_render($data, $VIEW); 
              $html = $this->load->view($VIEW, $data, true);  
              echo json_encode( array('result'=>success,'html_data'=>$html) );
           } 
        catch(Exception $err_obj)
            {
              show_error($err_obj->getMessage());
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
    
    public function fetch_comment_on_christan_project($proj_id='')
	{
		try
		  {
			 $data = $this->data;  
			 
			 ob_start();
			 $this->project_comments_ajax_pagination($proj_id);
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
	
	public function project_comments_ajax_pagination($proj_id , $page=0) 
	  {
		 try
		 {
		    $data = $this->data;  
			$wh	= " AND i_proj_id='".$proj_id."'";
			$result = $this->gospel_magazine_model->get_project_cmnts($wh ,$page,$this->comments_pagination_per_page);
			
		    $resultCount = count($result);
			$total_rows = $this->gospel_magazine_model->get_count_project_cmnts($wh);
			#pr($result);exit; 		

			$this->load->library('jquery_pagination');
			$config['base_url'] = base_url()."logged/media_center/project_comments_ajax_pagination/{$proj_id}";
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
	
	
	
	public function fetch_likes_on_christan_project($proj_id='')
	{
		try
		  {
			 $data = $this->data;  
			 
			 ob_start();
			 $this->project_likes_ajax_pagination($proj_id);
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
	
	public function project_likes_ajax_pagination($proj_id , $page=0) 
	  {
		 try
		 {
		    $data = $this->data;  
			$wh	= " AND i_proj_id='".$proj_id."'";
			$result = $this->gospel_magazine_model->get_project_likes($wh ,$page,$this->comments_pagination_per_page);
			
		    $resultCount = count($result);
			$total_rows = $this->gospel_magazine_model->get_count_project_likes($wh);
			#pr($result);exit; 		

			$this->load->library('jquery_pagination');
			$config['base_url'] = base_url()."logged/media_center/project_likes_ajax_pagination/{$proj_id}";
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
			$VIEW_FILE = "logged/media_center/liked_by_lightbox_ajax.phtml";
			$this->load->view($VIEW_FILE, $data);
			//return $html;
			
		} 
        catch(Exception $err_obj)
        {
            
        } 
	
    }
    
    
}// end of controller
    