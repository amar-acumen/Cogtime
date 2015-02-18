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

class Edit_help_center_category extends Admin_base_Controller
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
           $this->upload_path = BASEPATH.'../uploads/media_center_article/'; 
           $this->upload_path_featured_home = BASEPATH.'../uploads/media_center_article_featured_home/'; 
		    
           $this->load->model("help_center_model");
           $this->load->helper('common_option_helper.php');
           
           
         //  $this->upload_path = BASEPATH.'../uploads/media_center_article/';
		   
		}
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    // "index" function definition...
    public function index($category_id) 
    {
//die($category_id);
        try
        {
            
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
            $data['top_menu_selected'] = 9;
            $data['submenu'] = 2;
            
            
            
            $data['current_page'] = $current_page;
            $this->session->set_userdata('current_page','');
            $this->session->set_userdata('current_page',$current_page);
            
            
            
            $data['cat_info'] = $this->help_center_model->fetch_help_cat_by_id($category_id);
           
            
            
            # rendering the view file...
            $VIEW_FILE = "admin/help_center/edit_help_center_category.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }//end of index
    
    
    //------------------------------------ edit news ---------------------------------------------
   function post_edit_data(){
       //die();
       
       $cat_name = $this->input->post('cat_name');
       $id = $this->input->post('id');
       if($cat_name == ''){
           $result = 'failure';
           $error_msg = 'Enter Category name';
        echo json_encode(array(
                                        'result' => $result,
                                        'msg' => $error_msg
                                        )
                                );
                exit;
       }
       else{
       $data = array(
               'cat_name' => $cat_name,
              
            );

$this->db->where('id', $id);
 $this->db->update('cg_help_center_category', $data); 
//return $res;
       //die('dd');
       $result = 'success';
           $suceess_msg = 'Category name successfuly updated';
        echo json_encode(array(
                                        'result' => $result,
                                        'msg' => $suceess_msg
                                        )
                                );
   }
   }
    
    
    
}// end of controller
    