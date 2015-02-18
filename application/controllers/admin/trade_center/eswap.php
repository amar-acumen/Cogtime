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

class Eswap extends Admin_base_Controller
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
            $this->load->model("e_swap_model");
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
									     'js/backend/manage_eswap.js') );
                                        
             parent::_add_css_arr( array('css/dd.css'
                                        ) );
            # adjusting header & footer sections [End]...
			$data['top_menu_selected'] = 4;
			$data['submenu'] = 4;
         
            
			// fetching data
            $this->session->set_userdata('search_condition',$WHERE_COND);
		
			
			ob_start();
            $this->product_ajax_pagination();
            $data['product_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
			
			
			ob_start();
            $this->request_ajax_pagination();
            $data['request_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
			
                
            # rendering the view file...
            $VIEW_FILE = "admin/trade_center/eswap.phtml";
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
		  if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) :
           
            
			
				$WHERE_COND = " AND 1 ";
				
				$s_title = get_formatted_string(trim($this->input->post('txt_title')));
				$WHERE_COND .= ($s_title=='')?'':" AND (esp.s_name LIKE '%".$s_title."%')";
				
				$txt_brand = get_formatted_string(trim($this->input->post('txt_brand')));
				$WHERE_COND .= ($txt_brand=='')?'':" AND (esp.s_brand LIKE '%".$txt_brand."%')";
				
				$product_code = get_formatted_string(trim($this->input->post('product_code')));
				$WHERE_COND .= ($product_code=='')?'':" AND (esp.product_id = '".$product_code."')";
				
				$category = decrypt(trim($this->input->post('category')));
				$WHERE_COND .= ($category =='')?'':" AND (c.i_parent_category = '".$category."')";
				
				$sub_category = decrypt(trim($this->input->post('sub_category')));
				$WHERE_COND .= ($sub_category =='')?'':" AND (c.id = '".$sub_category."')";
						
			    
			
				/*$txt_user_name = get_formatted_string(trim($this->input->post('txt_user_name')));
				$WHERE_COND .= ($txt_user_name=='')?'':" AND (P.txt_user_name LIKE '".$txt_user_name."%')";*/
				$this->session->set_userdata('search_condition',$WHERE_COND);
			
			
           endif;  
		   	
			$s_where = $this->session->userdata('search_condition');
			## seacrh conditions : filter ############

			
			//$order_by = "p.s_category_name ASC ";
		   	$result = $this->e_swap_model->get_all_eswap_product_list($s_where,$page,$this->pagination_per_page,$order_by);
            $resultCount = count($result);
			#echo $this->db->last_query(); //exit;
			$total_rows = $this->e_swap_model->get_all_eswap_product_list_count($s_where);
			
			if( ( !is_array($result) || !count($result) ) && $total_rows ) {
                $page = $page - $this->pagination_per_page;
                
                $result = $this->e_swap_model->get_all_eswap_product_list($s_where, $page, $this->pagination_per_page,$ORDER_BY);
            }
			## end seacrh conditions : filter ############
			
			//pr($result,1);
//						pr($this->uri->total_segments());

			#Jquery Pagination Starts
           	$this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/trade_center/eswap/product_ajax_pagination";
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
          echo  $this->load->view('admin/trade_center/swap_product_ajax_listing.phtml', $data,TRUE);
		 }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
    }
	
	  # function to load ajax-pagination [AJAX CALL]...
    public function request_ajax_pagination($page=0)
    {
        try
        {
			## seacrh conditions : filter ############
		     if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) :
           
            
			
				$WHERE_COND = " AND 1 ";
				
				$s_title = get_formatted_string(trim($this->input->post('txt_title')));
				$WHERE_COND .= ($s_title=='')?'':" AND (p.s_name LIKE '%".$s_title."%')";
				
				$txt_brand = get_formatted_string(trim($this->input->post('txt_brand')));
				$WHERE_COND .= ($txt_brand=='')?'':" AND (p.s_brand LIKE '%".$txt_brand."%')";
				
				$product_code = get_formatted_string(trim($this->input->post('product_code')));
				$WHERE_COND .= ($product_code=='')?'':" AND (p.product_id = '".$product_code."')";
				
				
				$category = decrypt(trim($this->input->post('category')));
				$WHERE_COND .= ($category =='')?'':" AND (c.i_parent_category = '".$category."')";
				
				$sub_category = decrypt(trim($this->input->post('sub_category')));
				$WHERE_COND .= ($sub_category =='')?'':" AND (c.id = '".$sub_category."')";
			
				
				/*$txt_user_name = get_formatted_string(trim($this->input->post('txt_user_name')));
				$WHERE_COND .= ($txt_user_name=='')?'':" AND (P.txt_user_name LIKE '".$txt_user_name."%')";*/
				$this->session->set_userdata('search_condition',$WHERE_COND);
			
			
           endif;  
		   	
			$s_where = $this->session->userdata('search_condition');
			## seacrh conditions : filter ############
		   	$result = $this->e_swap_model->get_all_eswap_request_product_list($s_where,$page,$this->pagination_per_page,$order_by);
            $resultCount = count($result);
			#echo $this->db->last_query(); exit;
			$total_rows = $this->e_swap_model->get_all_eswap_request_product_list_count($s_where);
			
			if( ( !is_array($result) || !count($result) ) && $total_rows ) {
                $page = $page - $this->pagination_per_page;
                
                $result = $this->e_swap_model->get_all_eswap_request_product_list($s_where, $page, $this->pagination_per_page,$ORDER_BY);
            }
			## end seacrh conditions : filter ############
			
			//pr($result,1);
			#Jquery Pagination Starts
           	$this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/trade_center/eswap/request_ajax_pagination";
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

            $config['div'] = '#REQUEST_DIV'; /* Here #content is the CSS selector for target DIV */
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
          echo  $this->load->view('admin/trade_center/swap_request_ajax_listing.phtml', $data,TRUE);
		 }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
    }
	
	function change_status()
    {
        $id = $this->input->post('id');
        $current_status = $this->input->post('status');
        $data['i_isenabled'] = 3 - $current_status;
		$whr_arr = array('id'=>$id);
        $this->e_swap_model->update_eswap_product($data, $whr_arr);
        
		$status = ( $data['i_isenabled'] == 2)?'Enable':'Disable';
		$action_html =  '<input id="prod_status_'.$id.'" name="" title="'.$status.'" type="button" class="btn-06"  value="'.$status.'" status="'.$data['i_isenabled'].'" onclick="return change_status('.$id.','.$data['i_isenabled'].')"/>';
        echo json_encode(array('status'=>$data['i_isenabled'], 'action_html'=>$action_html));
        
    }
    
    function delete()
    {
        $id = $this->input->post('id');
        $this->e_swap_model->delete_products_and_requests($id);
       
		ob_start();
		$this->product_ajax_pagination();
		$product_content = ob_get_contents(); //pr($data['result_content']);
		ob_end_clean();
		
		
		ob_start();
		$this->request_ajax_pagination();
		$request_content = ob_get_contents(); //pr($data['result_content']);
		ob_end_clean();
        
        echo json_encode(array('product_content'=>$product_content, "request_content"=>$request_content));
    }
    
    function change_request_status()
    {
        $id = $this->input->post('id');
        $current_status = $this->input->post('status');
        $data['i_isenabled'] = 3 - $current_status;
		$whr_arr = array('id'=>$id);
        $this->e_swap_model->update_eswap_request($data, $whr_arr);
        
		$status = ( $data['i_isenabled'] == 2)?'Enable':'Disable';
		$action_html =  '<input id="req_status_'.$id.'" name="" title="'.$status.'" type="button" class="btn-06"  value="'.$status.'" status="'.$data['i_isenabled'].'" onclick="return change_req_status('.$id.','.$data['i_isenabled'].')"/>';
        echo json_encode(array('status'=>$data['i_isenabled'], 'action_html'=>$action_html));
        
    }
    
    function delete_request()
    {
        $id = $this->input->post('id');
        $this->e_swap_model->delete_requests($id);
       
		ob_start();
		$this->product_ajax_pagination();
		$product_content = ob_get_contents(); //pr($data['result_content']);
		ob_end_clean();
		
		
		ob_start();
		$this->request_ajax_pagination();
		$request_content = ob_get_contents(); //pr($data['result_content']);
		ob_end_clean();
        
        echo json_encode(array('product_content'=>$product_content, "request_content"=>$request_content));
    }
    
    
 function generate_sub_categroies(){
	
	$id = ($this->input->post('id') != -1)?decrypt($this->input->post('id')):-1;	
	$res = $this->db->query("SELECT * FROM {$this->db->TRADE_CAT} WHERE 1
							 AND i_parent_category= {$id} ORDER BY s_category_name ASC ");
	$mix_value = $res->result_array();
	//pr($mix_value);
	
	$s_option = '';
        if($mix_value)
        {
            $s_select = '';//defined here for unsetting this var 
			$s_option = "<option value='-1'>Select</option>";
            foreach ($mix_value as $val)
            {
                $s_select = '';
                if(encrypt($val["id"]) == $s_id)
                    $s_select = " selected ";
				if($val["pcat_name"]=='')
                	$s_option .= "<option $s_select value='".encrypt($val["id"])."'>".$val["s_category_name"]."</option>";
				else
					$s_option .= "<option $s_select value='".encrypt($val["id"])."'>".$val["pcat_name"]." > ".$val["s_category_name"]."</option>";
            }
            unset($s_select,$val);
        }
        
        unset($cond,$res,$mix_value,$mix_where, $s_id);
	echo json_encode(array('s_option'=>$s_option));
	 
 }
  
   function get_details(){
	
	$id = $this->input->post('id');	
	
	$data['info']	= $this->e_swap_model->get_by_id($id);		
	$html = $this->load->view('admin/trade_center/eswap_detail.phtml',$data,true);
	echo json_encode(array('html'=>$html));
	 
 }
  
    
}   // end of controller...