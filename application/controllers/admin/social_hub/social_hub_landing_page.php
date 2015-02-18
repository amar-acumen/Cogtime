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

class Social_hub_landing_page extends Admin_base_Controller
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
           $this->load->model("landing_page_cms_model");
           $this->load->helper('common_option_helper.php');
           
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
                                        'js/jquery/JSON/json2.js'
                                        ) );
                                        
             parent::_add_css_arr( array('css/dd.css'
                                        ) );
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 3;
            $data['submenu'] = 1;
            
            $where = " WHERE s_keyword like 'sh_%'";
            $data['res'] = $this->landing_page_cms_model->get_contents($where);
            
            
            # rendering the view file...
            $VIEW_FILE = "admin/social_hub/social_hub_landing_page.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }
    
    
    
    
    function post_data()
    {
        $error = 0;
        $info=array();
        $key=array();
        
        
        
            $info['s_title'] = $this->input->post('txt_sh_title');
            $info['s_desc'] = $this->input->post('txtarea_sh_desc');
            if($info['s_title']=='')
            {
                $arr_messages['sh_title']='* Required field';
                $error = 1;
            }
            else
            {
                $info=array();
                $key=array();
                $info['s_title'] = $this->input->post('txt_sh_title');
                $info['s_desc'] = $this->input->post('txtarea_sh_desc');
                $info['dt_updated_on'] = get_db_datetime();
                //$info['s_keyword'] = 'sh_main';
                $key = array('s_keyword'=>'sh_main');
                
                $this->landing_page_cms_model->update_content($info,$key);
                
            }
            
            
            
            
                $info=array();
                $key=array();
                $info['s_desc'] = $this->input->post('txtarea_sh_tweet');
                $info['dt_updated_on'] = get_db_datetime();
                #$info['s_keyword'] = 'sh_tweet';
                $key = array('s_keyword'=>'sh_tweet');
                $this->landing_page_cms_model->update_content($info,$key);
                
            
            

                $info=array();
                $key=array();
                $info['s_desc'] = $this->input->post('txtarea_sh_blog');
                $info['dt_updated_on'] = get_db_datetime();
                #$info['s_keyword'] = 'sh_blog';
                $key = array('s_keyword'=>'sh_blog');
                $this->landing_page_cms_model->update_content($info,$key);
            
            
            
                $info=array();
                $key=array();
                $info['s_desc'] = $this->input->post('txtarea_sh_rings');
                $info['dt_updated_on'] = get_db_datetime();
                #$info['s_keyword'] = 'sh_rings';
                $key = array('s_keyword'=>'sh_rings');
                $this->landing_page_cms_model->update_content($info,$key);
            
            
            
                $info=array();
                $key=array();
                $info['s_desc'] = $this->input->post('txtarea_sh_events');
                $info['dt_updated_on'] = get_db_datetime();
                #$info['s_keyword'] = 'sh_event';
                $key = array('s_keyword'=>'sh_events');
                $this->landing_page_cms_model->update_content($info,$key);
            
            
                $info=array();
                $key=array();
                $info['s_desc'] = $this->input->post('txtarea_sh_chatRoom');
                $info['dt_updated_on'] = get_db_datetime();
                #$info['s_keyword'] = 'sh_chatRoom';
                $key = array('s_keyword'=>'sh_chatRoom');
                $this->landing_page_cms_model->update_content($info,$key);
            
            
            
            
            
            if($error==1)
            {
                $result = 'failure';
                $msg    = 'Title field can not be blank!';
                echo json_encode(array('result'=>$result,'msg'=>$msg,'arr_messages'=>$arr_messages));
                exit;
            }            
            
            $result = 'success';
            $msg = 'Contents updated successfully.';

        echo json_encode(array('result'=>$result,'msg'=>$msg));
    }
    
    
    
}// end of controller