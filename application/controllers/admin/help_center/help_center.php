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

class Help_center extends Admin_base_Controller
{
   
   
    private $news_pagination_per_page =  10 ;
   
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
           $this->load->helper('common_option_helper.php');
           
           
        //   $this->upload_path = BASEPATH.'../uploads/media_center_videos/';
           
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
            $data['submenu'] = 1;
            
            
            $data['categories'] = $this->help_center_model->get_help_center_category();
            
            #$this->session->set_userdata('where','');
            #$this->session->set_userdata('order_by','');
            
            $current_page = ($this->session->userdata('current_page')!='')? $this->session->userdata('current_page'):$page;
            $data['current_page'] = $current_page;
            
            ### ajax call ###
            ob_start();
            $this->help_center_listing_ajax($current_page);
            $data['content'] = ob_get_contents();
            ob_end_clean();
            ### end ajax call ###
            
            $this->session->set_userdata('current_page','');
//echo "current page : ".$current_page.'--';
            $this->session->set_userdata('where','');
            
           
            
            
            # rendering the view file...
            $VIEW_FILE = "admin/help_center/help_center.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }//end of index
    
    
    public function help_center_listing_ajax($page=0)
    {
         try
         {
             //echo $page;
            $data = $this->data; 
            $where    = " WHERE 1" ;
            $order_by = '';
            
            $this->session->set_userdata('where','');
            $this->session->set_userdata('order_by','');
            
            if($_POST)
            {
                
                
                $title = $this->input->post('title');
                $posted_by = $this->input->post('uploaded_by');
                
                $category = trim($this->input->post('category'));
               // $date_from = $this->input->post('date_from');
               // $date_to = $this->input->post('date_to');
                
                if($title!='')
                {
                    $where=  "WHERE c.h_title LIKE '%$title%'";
                }
                if($category !='-1' && $category!='' && $category!='t' && $category!='v')
                {

                    $where.=" AND c.h_cat= '{$category}'";
                }
               
               
                else
                {
                    $this->session->set_userdata('order_by','');
                }
				
                if($posted_by!='')
                {
                    $where.= " AND c.i_posted_by= {$posted_by}";
                }
                
                
                if($this->input->post('create_time') != ''){
                     $dt_start_date = get_db_dateformat($this->input->post('create_time'));
                    $where .= " AND (DATE(c.dt_posted_on) ='".$dt_start_date."' )";
                }
//                if($this->input->post('date_to') != ''){
//                     $dt_end_date = get_db_dateformat($this->input->post('date_to'));
//                    $where .= " AND (DATE(c.dt_posted_on) <='".$dt_end_date."' )";
//                }
//                
                
                $this->session->set_userdata('where',$where);
            }   // end post
            

			
           $s_where = $this->session->userdata('where');
            $order_by = $this->session->userdata('order_by');
            
   //echo 'dd';
            $result = $this->help_center_model->get_all_help($s_where ,$page,$this->news_pagination_per_page,$order_by);
            // echo 'res :'.count($result);                                                   
            
            $resultCount = count($result);
            $total_rows = $this->help_center_model->get_total_help($s_where); 
            if(count($result)==0 && $total_rows)
            {
                $page= $page - $this->news_pagination_per_page;
                $result = $this->help_center_model->get_all_help($s_where ,$page,
                                                                $this->news_pagination_per_page);
                
            }
    
    //pr($result);         
    //echo "total : ".$total_rows;exit;

    
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/help_center/help_center/help_center_listing_ajax";
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
            $VIEW_FILE = "admin/help_center/help_center_ajax.phtml";
            echo $this->load->view($VIEW_FILE, $data,true);
            //return $html;
            
        } 
        catch(Exception $err_obj)
        {
            
        } 
    
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
    function delete_help_article($id){
        //echo 'ggg';
        
         
        $id = $this->input->post('id');
        //echo $id;
       // die();
$this->db->where('id', $id);
$this->db->delete('cg_help_center'); 
       
    }
    
    //--------------------------------------- end of delete news ----------------------------------------------
    
    
    
    //------------------------------- fetch like n comment --------------------------------------------
   
    
    
    
    
}// end of controller
    