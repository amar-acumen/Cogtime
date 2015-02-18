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

class Media_video_category extends Admin_base_Controller
{
   
   
    private $news_pagination_per_page =  20 ;
   
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
            
            
           $this->load->model("landing_page_cms_model");
           $this->load->helper('common_option_helper.php');
           
           
           $this->upload_path = BASEPATH.'../uploads/media_center_videos/';
           
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    // "index" function definition...
    public function index($page = 0) 
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
            
            
            $data['categories'] = $this->landing_page_cms_model->get_all_video_cat();
            
            #$this->session->set_userdata('where','');
            #$this->session->set_userdata('order_by','');
            
            $current_page = ($this->session->userdata('current_page')!='')? $this->session->userdata('current_page'):$page;
            $data['current_page'] = $current_page;
            
            ### ajax call ###
            ob_start();
            $this->video_category_listing_ajax($current_page);
            $data['content'] = ob_get_contents();
            ob_end_clean();
            ### end ajax call ###
            
            $this->session->set_userdata('current_page','');
//echo "current page : ".$current_page.'--';
            $this->session->set_userdata('where','');
            
           
            
            
            # rendering the view file...
            $VIEW_FILE = "admin/media_center/media_video_category.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }//end of index
    
    
    public function video_category_listing_ajax($page=0)
    {
         try
         {
             //echo $page;
            $data = $this->data; 
            $where    = " WHERE 1" ;
            $order_by = '';
            
            $this->session->set_userdata('where','');
            $this->session->set_userdata('order_by','');
            
            if($_POST && $_POST['hd_val'] == 'Y')
            {
                
                
                $title = $this->input->post('title');
                $posted_by = $this->input->post('uploaded_by');
                
                $category = trim($this->input->post('category'));
               // $date_from = $this->input->post('date_from');
               // $date_to = $this->input->post('date_to');
                
                if($title!='')
                {
                    $where=  "WHERE MATCH (n.s_title) AGAINST ('{$title}')";
                }
                if($category !='-1' && $category!='' && $category!='t' && $category!='v')
                {

                    $where.=" AND n.i_category= '{$category}'";
                }
                if($category == 't')
                {
                    $where .=" AND n.i_is_top_story = '1'";
                }
				
                if($category == 'v')
                {
                    $order_by = " i_view_count DESC";
                    $this->session->set_userdata('order_by',$order_by);
                }
                else
                {
                    $this->session->set_userdata('order_by','');
                }
				
                if($posted_by!='')
                {
                    $where.= " AND n.i_posted_by= {$posted_by}";
                }
                
                
                if($this->input->post('date_from') != ''){
                     $dt_start_date = get_db_dateformat($this->input->post('date_from'));
                    $where .= " AND (DATE(n.dt_posted_on) >='".$dt_start_date."' )";
                }
                if($this->input->post('date_to') != ''){
                     $dt_end_date = get_db_dateformat($this->input->post('date_to'));
                    $where .= " AND (DATE(n.dt_posted_on) <='".$dt_end_date."' )";
                }
                
                
                $this->session->set_userdata('where',$where);
            }   // end post
            

			
            $s_where = $this->session->userdata('where');
            $order_by = $this->session->userdata('order_by');
            

            $result = $this->landing_page_cms_model->get_all_video_cat($s_where ,$page,$this->news_pagination_per_page,$order_by);
            // echo 'res :'.count($result);                                                   
            
            $resultCount = count($result);
            $total_rows = $this->landing_page_cms_model->get_total_video_cat($s_where); 
            if(count($result)==0 && $total_rows)
            {
                $page= $page - $this->news_pagination_per_page;
                $result = $this->landing_page_cms_model->get_all_video_cat($s_where ,$page,
                                                                $this->news_pagination_per_page);
                
            }
    
    //pr($result);         
    //echo "total : ".$total_rows;exit;

    
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/media_center/media_article_category/video_category_listing_ajax";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->news_pagination_per_page;
            $config['uri_segment'] =  ($this->session->userdata('current_page')!='')? 2: 5;
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
            $data['total_pages'] = ceil($total_rows/$this->news_pagination_per_page);
          
             $p = ($page/$this->news_pagination_per_page);
             $data['current_loaded_page_no'] =  $p + 1;
            
            # rendering the view file...
            $VIEW_FILE = "admin/media_center/media_video_category_ajax.phtml";
            echo $this->load->view($VIEW_FILE, $data,true);
            //return $html;
            
        } 
        catch(Exception $err_obj)
        {
            
        } 
    
    }
    
    
    
    
    //--------------------------------------- delete news ----------------------------------------------
    function delete_news($page=0)
    {
        $id = $this->input->post('id');
        #$current_page = $this->input->post('current_page');
        $current_page = $page;
        $this->landing_page_cms_model->delete_news_by_news_id($id);
        #$this->session->set_userdata('current_page',$current_page);
        ### ajax call ###
        ob_start();
        $this->christian_news_listing_ajax($current_page);
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
    
   /*************************************************************/
    function video_status_disable($id){
        $id = $this->input->post('id');
        $data = array(
               'is_hidden' => 1,
               
            );

$this->db->where('id', $id);
$this->db->update('cg_mc_video_cat', $data); 
    }
    
     function video_status_enable($id){
        $id = $this->input->post('id');
        $data = array(
               'is_hidden' => 0,
               
            );

$this->db->where('id', $id);
$this->db->update('cg_mc_video_cat', $data); 
    }
    
    
}// end of controller
    