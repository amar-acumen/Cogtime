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

class Testimonies extends Admin_base_Controller
{
    private $prayer_wall_pagination_per_page= 2;
   
    

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
            $this->load->model("testimonies_model");
           $this->load->helper('common_option_helper.php');
           
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    // "index" function definition...
    public function index($page=0) 
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
                                         'js/backend/manage_product_categories.js') );
                                        
             parent::_add_css_arr( array('css/dd.css'
                                        ) );
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 7;
            $data['submenu'] = 7;
         
            
            // fetching data
            //$WHERE_COND = " WHERE 1 ";
            
            //$this->session->set_userdata('search_condition',$WHERE_COND);
            $this->session->set_userdata('search_condition','');
            if($this->session->userdata('current_page_testimonies')!='')
                $page=$this->session->userdata('current_page_testimonies');
            if($page!=0)
                $this->session->set_userdata('current_page_testimonies',$page);
                
            $order_by = "`id` ASC ";
            
            
            ob_start();
            $this->testimonies_ajax_pagination($page);
            $data['result_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
            
            $this->session->set_userdata('current_page_testimonies','');
                
   //echo "session : ".$this->session->userdata('search_condition');exit;
            # rendering the view file...
            $VIEW_FILE = "admin/holy_place/testimonies/testimonies.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }


    # function to load ajax-pagination [AJAX CALL]...
    public function testimonies_ajax_pagination($page=0,$position=0)
    {
        try
        {
            ## seacrh conditions : filter ############
         $s_where = " WHERE 1 ";
         
         //$s_where = $this->session->userdata('search_condition');
         if($this->input->post('if_post')=='y')
         {
            
             if($this->input->post('date_to1')!='')
             {
                 $start_date=$this->input->post('date_to1');
                 $s_where.=" AND dt_start_date >= '".get_db_dateformat($start_date,'/')."'";
             }
             if($this->input->post('date_to2')!='')
             {
                 $end_date=$this->input->post('date_to2');
                 $s_where.=" AND  dt_end_date <= '".get_db_dateformat($end_date,'/')."'";
             }
             $this->session->set_userdata('search_condition',$s_where);
         }
           

           
            
            $s_where = $this->session->userdata('search_condition');
            
            $order_by = "";
               $result = $this->testimonies_model->get_all_testimonies($s_where,$page,$this->prayer_wall_pagination_per_page,$order_by);
            $resultCount = count($result);
            
            $total_rows = $this->testimonies_model->get_count_all_testimonies($s_where);
            
            if(!count($result) && $total_rows)
            {
                $page = $page - $this->prayer_wall_pagination_per_page;
                $result = $this->testimonies_model->get_all_testimonies($s_where,$page,$this->prayer_wall_pagination_per_page,$order_by);
                
            }
            //echo $total_rows;exit;
            //echo $this->db->last_query(); exit;
            //echo "total : ".$total_rows;exit;
            ## end seacrh conditions : filter ############
            
            //pr($result,1);
            #Jquery Pagination Starts
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/holy_place/testimonies/testimonies_ajax_pagination";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->prayer_wall_pagination_per_page;
            $config['uri_segment'] = ($position!=0)? $position:5;
            $config['num_links'] = 9;
            $config['page_query_string'] = false;
            $config['prev_link'] = 'PREV';
            $config['next_link'] = 'NEXT';

            $config['cur_tag_open'] = '<li> <span><a href="javascript:void(0)" class="select">';
            $config['cur_tag_close'] = '</a></span></li>';

            $config['next_tag_open'] = '<li><a href="javascript:void(0)">';
            $config['next_tag_close'] = '</a></li>';

            $config['prev_tag_open'] = '<li><a href="javascript:void(0)">';
            $config['prev_tag_close'] = '</a></li>';

            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';

            $config['div'] = '#table_content'; /* Here #content is the CSS selector for target DIV */
            $config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
            $config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */

            $this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();
            $this->jquery_pagination->create_links();

            // getting   listing...
            $data['info_arr'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $page;
          
            # loading the view-part...
          echo  $this->load->view('admin/site_settings/cms/testimonies_ajax.phtml', $data,TRUE);
         }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
        
    }
    
    function fetch_testimony_detail()
    {
        $id = $this->input->post('id');
        $res = $this->testimonies_model->get_testimony_detail_by_testimony_id($id);
        $desc = $res['s_description'];
        echo json_encode(array('desc'=>$desc));
        
    }
    
    
    function edit_testimony($current_page)
    {
        $info['s_description'] = get_formatted_string(trim($this->input->post('desc')));
        $id = $this->input->post('id');
        //$current_page = $this->input->post('current_page');
        
        if($info['s_description']=='')
        {
            $result = "failure";
            $msg = "* Required Field";
            $html = '';
        }
        else
        {
            $this->testimonies_model->edit_testimony($info,$id);
            
            $result = 'success';
            $msg = "Successfully updated";
            ob_start();
            $this->testimonies_ajax_pagination($current_page);
            $html = ob_get_contents();
            ob_clean();
            
        }
        
        echo json_encode(array(
                                'result'=>$result,
                                'msg'=>$msg,
                                'html'=>$html
                                )
                        );
    }
    
    function delete_testimony($current_page)
    {
        $id=$this->input->post('id');
        //$current_page = $this->input->post('current_page');
        
        /*if($this->session->userdata('current_page_intercession'))
            echo "set session";
        else
            echo "not set";
        */
        
        //$this->session->set_userdata('current_page_intercession',$current_page);
        
        $this->testimonies_model->delete_by_id($id);
        
        ob_start();
        $this->testimonies_ajax_pagination($current_page);
        $html = ob_get_contents();
        ob_end_clean();
        
        
        //$this->session->set_userdata('current_page_intercession','');
        
        $result='success';
        echo json_encode(array('result'=>$result,'response'=>$html));
    }
    
    function viwe_post()
    {
        $id = $this->input->post('id');
        $res = $this->testimonies_model->get_post_details_by_testimonial_id($id);
        
        echo json_encode(array('res'=>$res));
    }
    
    
    
    function change_status()
    {
        $id = $this->input->post('id');
        $current_status = $this->input->post('status');
        
        $data['i_is_enable'] = 1 - $current_status;
        $this->testimonies_model->change_status_testimonies($data,$id);
        
        echo json_encode(array('status'=>$data['i_is_enable']));
        
    }
        
    
    
    
    

    
    
    

    
    function check_time($s_time)
    {

        /*if(!preg_match("/^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])(:([0-5]?[0-9]))?$/", $s_time))*/
        if(!preg_match("/^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])?$/", $s_time))
        {
            $this->form_validation->set_message('check_time', '* Please input time in 24 hours format. ');
            return false;
        }

        else
        return true;

    }
    
    
     
    
}   // end of controller...