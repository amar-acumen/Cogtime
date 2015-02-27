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
* @link model/projects_model.php
* @link views/##
*/

class Messages extends Admin_base_Controller
{
	
	private $pagination_per_page=10;

    

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
            
           $this->load->model("projects_model");
	 $this->load->model("my_ring_model");
        
           $this->load->helper('common_option_helper.php');
            $this->load->model("church_model");
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }
	 // "index" function definition...
    public function index($type) 
    {

        try
        {
			
            # adjusting header & footer sections [Start]...
            $data = $this->data;
            parent::_set_title("::: COGTIME Xtian network :::");
            parent::_set_meta_desc("::: COGTIME Xtian network :::");
            parent::_set_meta_keywords("::: COGTIME Xtian network :::");
            parent::_add_js_arr( array( 'js/lightbox.js',
										'js/jquery.form.js',
									       'js/jquery/JSON/json2.js',
									     'js/backend/build_kingdom/charity_project.js') );
                                        
             parent::_add_css_arr( array('css/dd.css',
                                        ) );
            # adjusting header & footer sections [End]...
			$data['top_menu_selected'] = 6;
			$data['submenu'] = 12;
			// fetching data
			 $this->session->set_userdata('search_condition','WHERE 1  ');
			
			if($type  == 'ring')
                            
			 	$data['info_type'] = 'Ring Category Requests';
           else if($type  == 'church')
                            
			 	$data['info_type'] = 'church Category Requests';
				
			else if($type  == 'project')
				$data['info_type'] = 'Project Information Requests';
			
                        
			ob_start();
            $this->info_ajax_pagination('project');
            $data['result_content1'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
			
			ob_start();
            $this->info_ajax_pagination('ring');
            $data['result_content2'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
			
			ob_start();
            $this->info_ajax_pagination('church');
            $data['result_content3'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();  
            # rendering the view file...
            $VIEW_FILE = "admin/site_settings/msg/info.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }
	
	
	# function to load ajax-pagination [AJAX CALL]...
    public function info_ajax_pagination($type, $page=0)
    {
        try
        {
			## seacrh conditions : filter ############
		 	
			/* if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) :
				$WHERE_COND = "WHERE 1   ";
				
				$s_title = get_formatted_string(trim($this->input->post('txt_title')));
				$WHERE_COND .= ($s_title=='')?'':" AND (CONCAT(U.s_first_name, U.s_last_name )LIKE '%".$s_title."%')";
				$this->session->set_userdata('search_condition',$WHERE_COND);
            endif;  
		   	
			$s_where = $this->session->userdata('search_condition');
			$s_where .= " AND PR.i_project_id = {$project_id}";*/
			
			
                        
			if($type == 'project'){
                           
				$result = $this->projects_model->get_info_list($s_where,$page,$this->pagination_per_page,$order_by);
				$resultCount = count($result);
				$total_rows = $this->projects_model->get_info_list_count($s_where);
			}
			else if($type == 'ring')
			{
				$result = $this->my_ring_model->get_info_list($s_where,$page,$this->pagination_per_page,$order_by);
				$resultCount = count($result);
				$total_rows = $this->my_ring_model->get_info_list_count($s_where);
			}
                        else
                        {
                          $result = $this->church_model->get_info_list($s_where,$page,$this->pagination_per_page,$order_by); 
                          $resultCount = count($result);
                          $total_rows = $this->church_model->get_info_list_count($s_where);
                        }
                        
			
			## end seacrh conditions : filter ############
			
			
			
			//pr($result,1);
			#Jquery Pagination Starts
           	$this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/site_settings/messages/info_ajax_pagination/".$type;
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
          echo  $this->load->view('admin/site_settings/msg/info_ajax.phtml', $data,TRUE);
		 }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
    }
}   // end of controller...