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

class Help_Center_category extends Admin_base_Controller
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
            
             $this->load->model("help_center_model");
          // $this->load->model("landing_page_cms_model");
           $this->load->helper('common_option_helper.php');
           
           
          // $this->upload_path = BASEPATH.'../uploads/media_center_videos/';
           
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
            $data['top_menu_selected'] = 9;
            $data['submenu'] = 2;
            
            
            //$data['categories'] = $this->landing_page_cms_model->get_all_news_cat();
           // $data['categories'] = $this->help_center_model->get_help_center_category();
           
            #$this->session->set_userdata('where','');
            #$this->session->set_userdata('order_by','');
            
            $current_page = ($this->session->userdata('current_page')!='')? $this->session->userdata('current_page'):$page;
           $data['current_page'] = $current_page;
            
//            ### ajax call ###
           ob_start();
           $this->help_center_category_listing_ajax($current_page);
           $data['content'] = ob_get_contents();
           ob_end_clean();
           ### end ajax call ###
            
            $this->session->set_userdata('current_page','');
////echo "current page : ".$current_page.'--';
           $this->session->set_userdata('where','');
            
           
            
            
            # rendering the view file...
            $VIEW_FILE = "admin/help_center/help_center_category.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }//end of index
    
    
   
    function  help_center_category_listing_ajax(){
        $data['categories'] = $this->help_center_model->get_help_center_category();
         # rendering the view file...
            $VIEW_FILE = "admin/help_center/help_center_category_ajax.phtml";
            echo $this->load->view($VIEW_FILE, $data,true);
    }






    //--------------------------------------- delete news ----------------------------------------------
//    function delete_news($page=0)
//    {
//        $id = $this->input->post('id');
//        #$current_page = $this->input->post('current_page');
//        $current_page = $page;
//        $this->landing_page_cms_model->delete_news_by_news_id($id);
//        #$this->session->set_userdata('current_page',$current_page);
//        ### ajax call ###
//        ob_start();
//        $this->christian_news_listing_ajax($current_page);
//        $html = ob_get_contents();
//        ob_end_clean();
//        ### end ajax call ###
//        
//        
//        echo json_encode(array('success'=>true,
//                                'msg'=>'The news deleted successfully.',
//                                'html'=>$html
//                                ));
//    }
//    
//    
//    //--------------------------------------- end of delete news ----------------------------------------------
//    
//    
//    
//    //------------------------------- fetch like n comment --------------------------------------------
//    public function fetch_people_liked_post()
//    {
//        try
//          {
//               $data = $this->data;  
//               $news_id = $this->input->post('news_id');
//               
//               
//               $data['result'] = $this->landing_page_cms_model->get_people_liked_by_news_id($news_id);
//               
//               
//              
//              $VIEW = "admin/media_center/like_comment/like_ajax.phtml";
//              #parent::_render($data, $VIEW); 
//              $html = $this->load->view($VIEW, $data, true);  
//              echo json_encode( array('result'=>success,'html_data'=>$html) );
//           } 
//        catch(Exception $err_obj)
//            {
//              show_error($err_obj->getMessage());
//            } 
//            
//    } 
//    
//     
//    
//    
//    public function fetch_comment_on_news()
//    {
//        try
//          {
//
//             $data = $this->data;  
//             $news_id = $this->input->post('news_id');
//             
//             $data['result'] = $this->landing_page_cms_model->get_comment_by_news_id($news_id);
//             
//              $VIEW = "admin/media_center/like_comment/comment_ajax.phtml";
//              #parent::_render($data, $VIEW); 
//              $html = $this->load->view($VIEW, $data, true);  
//              echo json_encode( array('result'=>success,'html_data'=>$html) );
//           } 
//        catch(Exception $err_obj)
//            {
//              show_error($err_obj->getMessage());
//            } 
//            
//    } 
//    //------------------------------- end fetch like n comment --------------------------------------------
//    
//    
//    function remove_like()
//    {
//        $record_id = $this->input->post('comment_record_id');
//        $news_id = $this->input->post('news_id');
//        
//        
//        
//        $this->landing_page_cms_model->remove_from_news_like_list($record_id);
//        
//        $likes = $this->landing_page_cms_model->get_total_people_liked_by_news_id($news_id);
//        
//        echo json_encode(array('total_likes'=>$likes));
//        
//    }
//    
//    function remove_comment()
//    {
//        $record_id = $this->input->post('comment_record_id');
//        $news_id = $this->input->post('news_id');
//        
//        $this->landing_page_cms_model->remove_from_news_comment_list($record_id);
//        
//        $comments = $this->landing_page_cms_model->get_total_comment_by_news_id($news_id);
//        
//        echo json_encode(array('total_comments'=>$comments));
//    }
//    
    
    
    
}// end of controller
    