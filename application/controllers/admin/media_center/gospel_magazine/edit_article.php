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

class Edit_article extends Admin_base_Controller
{
   
   
    
   
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
    public function index($page,$article_id) 
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
            $data['submenu'] = 5;
            
            
            $data['current_page'] = $page;
            $data['article_info'] = $this->gospel_magazine_model->fetch_article_info_by_article_id($article_id);
            
            # rendering the view file...
            $VIEW_FILE = "admin/media_center/gospel_magazine/edit_article.phtml";
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
            $info['s_publisher'] = trim(get_formatted_string($this->input->post('txt_publisher')));
            $info['s_description'] = trim(get_formatted_string($this->input->post('elm1')));
            
            $id = $this->input->post('txt_id');
            
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
                $this->gospel_magazine_model->edit_article($info,$id);
                
                
                $result = 'success';
                $msg = "Article successfully updated.";
                
                echo json_encode(array('result'=>$result,'msg'=>$msg));
                exit;
            }
            
        }
    }
    
   
  
    
   
    
    
    
}// end of controller
    