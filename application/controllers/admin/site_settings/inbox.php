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
* @link model/admin_groups_model.php
* @link views/##
*/

class Inbox extends Admin_base_Controller
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
           //$this->load->model("users_model"); 
          // $this->load->model("education_model");
           $this->load->model("data_messages_model");
           //data_messages_model
           $this->load->helper('common_option_helper.php');
           
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    // "index" function definition...
    public function index($page_no=0) 
    {
        //echo "in members.php : page_no = ".$page_no;
        try
        {
            # adjusting header & footer sections [Start]...
            $data = $this->data;
            parent::_set_title("::: COGTIME Xtian network :::");
            parent::_set_meta_desc("::: COGTIME Xtian network :::");
            parent::_set_meta_keywords("::: COGTIME Xtian network :::");
            parent::_add_js_arr( array( 'js/lightbox.js',
                                        'js/ModalDialog.js',
                                         'js/jquery.dd.js',
                                         'js/backend/members/delete_user.js'
                                         ) );
                                        
            parent::_add_css_arr( array() );
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 1;
            $data['submenu'] = 15;
         
            
            // fetching data
            $WHERE_COND = " WHERE 1  AND i_is_deleted = 0 ";
            $this->session->set_userdata('search_condition',$WHERE_COND);
            $page=0;
            $order_by = " `id` DESC ";
            
            
            //---------------------- for pagination back ---------------------
            if($page_no!=0)
                $page=($page_no-1)*2;
            //---------------------- end pagination back ---------------------
            
            ob_start();
            $this->ajax_pagination($page);
            $data['result_content'] = ob_get_contents(); //pr($data['result_content'],1);
            ob_end_clean();
            
             #pr($data,1);
            # rendering the view file...
            $VIEW_FILE = "admin/site_settings/inbox/inbox.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }

   
   
    
      # function to load ajax-pagination [AJAX CALL]...
    public function ajax_pagination($page=0)
    {
        try
        {
            ## seacrh conditions : filter ############
             
             if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) :
                $WHERE_COND = " WHERE 1 AND i_is_deleted = 0 ";
				
               
		if($this->input->post('category')!='-1')
                {
                $s_name = get_formatted_string(trim($this->input->post('category')));
                $WHERE_COND .= ($s_name=='')?'':" AND (s_type LIKE '%".$s_name."%')AND i_is_deleted = 0";
                }
				
		if($this->input->post('from_date') != ''):
		$s_word = get_db_dateformat($this->input->post('from_date'));
		$WHERE_COND .= ($s_word=='')?'':" AND Date(dt_created_on) >='".$s_word."' AND i_is_deleted = 0 ";
		endif;
		if($this->input->post('end_date') != ''):
		$s_word = get_db_dateformat($this->input->post('end_date'));
		$WHERE_COND .= ($s_word=='')?'':" AND Date(dt_created_on) <='".$s_word."' AND i_is_deleted = 0";
		endif;				
                $this->session->set_userdata('search_condition',$WHERE_COND);
            
                
           endif;  
               
            $s_where = $this->session->userdata('search_condition');
            $order_by = " `id` DESC ";
            $result = $this->data_messages_model->message_list_admin($s_where,$page,$this->pagination_per_page,$order_by);
            $resultCount = count($result);
           // echo $this->db->last_query(); 
            //pr($result,1);
            $total_rows = $this->data_messages_model->message_list_admin_count($s_where);
            
            if( ( !is_array($result) || !count($result) ) && $total_rows ) {
                $page = $page - $this->pagination_per_page;
                
                $result = $this->users_model->user_list($s_where, $page, $this->pagination_per_page,$order_by);
            }
            ## end seacrh conditions : filter ############
            
            //pr($result,1);
            #Jquery Pagination Starts
               $this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/site_settings/inbox/ajax_pagination";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->pagination_per_page;
            $config['uri_segment'] = 5;
            $config['num_links'] = 9;
            $config['page_query_string'] = false;
            $config['prev_link'] = 'PREV';
            $config['next_link'] = 'NEXT';

            /*$config['cur_tag_open'] = '<li> <span><a href="javascript:void(0)" class="select">';
            $config['cur_tag_close'] = '</a></span></li>';

            $config['next_tag_open'] = '<li><a href="javascript:void(0)">';
            $config['next_tag_close'] = '</a></li>';

            $config['prev_tag_open'] = '<li><a href="javascript:void(0)">';
            $config['prev_tag_close'] = '</a></li>'; --commented@12.02.13 */
            
            $config['cur_tag_open'] = '<li>';
            $config['cur_tag_close'] = '</li>';

            $config['next_tag_open'] = '<li>';
            $config['next_tag_close'] = '</li>';

            $config['prev_tag_open'] = '<li>';
            $config['prev_tag_close'] = '</li>';

            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';

            $config['div'] = '#table_content'; /* Here #content is the CSS selector for target DIV */
            $config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
            $config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */

            $this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();

            // getting   listing...
            $data['info_arr'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $page;
            
            $data['pagination_per_page']   =    $this->pagination_per_page;
          
            # loading the view-part...
            echo $this->load->view('admin/site_settings/inbox/inbox_ajax.phtml', $data,TRUE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
        
    }



// ================================== delete member ==============================================
   
// ================================= change status ======================================
    function status($id){
        
          $this->data_messages_model->change_admin_message_status($id);  
           $SUCCESS_MSG = "status change succesfully!";
          
           echo json_encode(array('result'=>'success',
                					   'id'=>$id,
                					   'msg'=>$SUCCESS_MSG ));
    }
    function status_accept($id)
    {
        $id = end(explode('_',$id));
        $this->data_messages_model->change_admin_message_status_accept($id);  
           $SUCCESS_MSG = "status change succesfully!";
          
           echo json_encode(array('result'=>'success',
                					   'id'=>$id,
                					   'msg'=>$SUCCESS_MSG )); 
    }
    
    function admin_del($id)
    {
      $SUCCESS_MSG = "status change succesfully!";
           $this->data_messages_model->change_admin_message_status_admin_del($id);  
           echo json_encode(array('result'=>'success',
                					   'id'=>$id,
                					   'msg'=>$SUCCESS_MSG ));    
    }
    
// ================================= end of change status ======================================     
// ================================= change is minister ======================================
}