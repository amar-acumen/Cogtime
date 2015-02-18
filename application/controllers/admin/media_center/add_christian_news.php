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

class Add_christian_news extends Admin_base_Controller
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
    public function index() 
    {

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
            $data['top_menu_selected'] = 5;
            $data['submenu'] = 4;
            
            
            $data['categories'] = $this->landing_page_cms_model->get_all_news_cat();
            
            
            $this->session->set_userdata('current_page','');
           
            
            
            # rendering the view file...
            $VIEW_FILE = "admin/media_center/add_christian_news.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }//end of index
    
    
    //------------------------------------ add new news ---------------------------------------------
    function post_add_data()
    {
        $where = " WHERE 1";
        $error = 0;
        $sys_error=0;
        $info=array();
        $key=array();
        $data['i_posted_by'] = $this->logged_admin_id;
        $info['dt_posted_on'] = get_db_datetime();
        
            $info['i_posted_by'] = $this->logged_admin_id;
            
            $info['s_title'] = get_formatted_string($this->input->post('txt_title'));
            $info['s_desc'] = get_formatted_string($this->input->post('txtarea_desc'));
            
            $info['i_category'] = intval($this->input->post('category'));

            
            $info['i_is_top_story'] = $this->input->post('top_story');

            

            
            
            
            
            if($info['s_title']=='')
            {
                $arr_messages['title']='* Required field';
                
                $error = 1;
            }
            
            if($info['s_desc']=='')
            {
                $arr_messages['desc'] = '* Required field';
                $error=1;
            }
           

            
            
           
            
           
            if($error!=1)  
            {
//pr($info,1);
                $res = $this->landing_page_cms_model->add_new_news($info);
                if(!$res)
                {
                    $sys_error=1;
                }
            }
            
            else
            {
                $result = 'failure';
                $msg    = 'Field(s) can not be blank!';
                echo json_encode(array('result'=>$result,'msg'=>$msg,'arr_messages'=>$arr_messages));
                exit;
            }        
            if($sys_error==1)
            {
                $result = 'failure';
                $msg    = 'Error occured! Try again.';
                echo json_encode(array('result'=>$result,'msg'=>$msg));
                exit;
            }    
            
            
            $result = 'success';
            $msg = 'News added successfully.';
            

            //$html = $this->video_listing_AJAX();

        echo json_encode(array('result'=>$result,'msg'=>$msg));
    }
    
    
    
    
    
    
    
    
}// end of controller
    