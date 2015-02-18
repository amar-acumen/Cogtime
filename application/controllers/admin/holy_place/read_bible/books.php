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

class Books extends Admin_base_Controller
{
    private $books_pagination_per_page= 20;


    

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
           
            $this->load->model("read_bible_model");
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
                                        'js/jquery/JSON/json2.js',
                                         'js/backend/manage_product_categories.js') );
                                        
             parent::_add_css_arr( array('css/dd.css'
                                        ) );
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 7;
            $data['submenu'] = 3;
         
            
            // fetching data
            $WHERE_COND = " WHERE 1 ";
            
            $this->session->set_userdata('search_condition',$WHERE_COND);
            $page=0;
            $order_by = "`id` ASC ";
            
            ob_start();
            $this->books_ajax_pagination();
            $data['result_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
            
                
            # rendering the view file...
            $VIEW_FILE = "admin/holy_place/read_bible/books.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }
    
    
    # function to load ajax-pagination [AJAX CALL]...
    public function books_ajax_pagination($page=0)
    {
        try
        {
            ## seacrh conditions : filter ############
         
            $s_where = $this->session->userdata('search_condition');
            $s_where.='';
            
            $order_by = "";
            
            $result = $this->read_bible_model->get_all_books($s_where,$page,$this->books_pagination_per_page,$order_by);
            $resultCount = count($result);
            #echo $this->db->last_query(); exit;
            $total_rows = $this->read_bible_model->get_count_all_books();
            
            
            pr($result);
            pr($total_rows,1);
            
            if( ( !is_array($result) || !count($result) ) && $total_rows ) {
                $page = $page - $this->product_category_pagination_per_page;
                
                $result = $this->product_categories_model->get_list($s_where, $page, $this->books_pagination_per_page,$ORDER_BY);
            }
            ## end seacrh conditions : filter ############
            
            //pr($result,1);
            #Jquery Pagination Starts
               $this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/holy_place/read_bible/books/books_ajax_pagination";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->books_pagination_per_page;
            $config['uri_segment'] = 5;
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
          echo  $this->load->view('admin/holy_place/read_bible/books_ajax.phtml', $data,TRUE);
         }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
        
    }
    
    
}// end of controller