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

class Credit_history extends Admin_base_Controller
{
    private $pagination_per_page= 20;
	private $product_sub_category_pagination_per_page= 20;

    

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
            $this->load->model("e_trade_model");
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
									     'js/backend/manage_credit.js') );
                                        
             parent::_add_css_arr( array('css/dd.css'
                                        ) );
            # adjusting header & footer sections [End]...
			$data['top_menu_selected'] = 4;
			$data['submenu'] = 6;
         
			// fetching data
            $this->session->set_userdata('search_condition',$WHERE_COND);
		
			
			ob_start();
            $this->product_ajax_pagination();
            $data['product_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
		
            # rendering the view file...
            $VIEW_FILE = "admin/trade_center/credit.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }


    # function to load ajax-pagination [AJAX CALL]...
    public function product_ajax_pagination($page=0)
    {
        try
        {
		    ## seacrh conditions : filter ############
			$s_where = $this->session->userdata('search_condition');
			## seacrh conditions : filter ############

			
		   	$result = $this->e_trade_model->getCreditPurchaselist($s_where,$page,$this->pagination_per_page,$order_by);
            $resultCount = count($result);
			$total_rows = $this->e_trade_model->getCreditPurchaseCount($s_where);
			
			## end seacrh conditions : filter ############

			#Jquery Pagination Starts
           	$this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/trade_center/credit_history/product_ajax_pagination";
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

            $config['prev_tag_open'] = '<li><a href="javascript:void(0)">';
            $config['prev_tag_close'] = '</a></li>';

            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';

            $config['div'] = '#PRODUCT_DIV'; /* Here #content is the CSS selector for target DIV */
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
          echo  $this->load->view('admin/trade_center/credit_history_ajax_listing.phtml', $data,TRUE);
		 }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
    }
	
	
    
    function delete()
    {
        $id = $this->input->post('id');
        $this->e_trade_model->deletePurchaseCredit($id);
		ob_start();
		$this->product_ajax_pagination();
		$product_content = ob_get_contents(); //pr($data['result_content']);
		ob_end_clean();
        
        echo json_encode(array('product_content'=>$product_content, "request_content"=>$request_content));
    }
    
  
    
}   // end of controller...