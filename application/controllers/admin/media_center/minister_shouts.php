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

class Minister_shouts extends Admin_base_Controller
{
   
   
    private $pagination_per_page =  20 ;
   
    // constructor definition...
    function __construct()
    {
        try
        {
            parent::__construct();
            parent::_check_admin_login();
            
            # configuring paths...
           $this->logged_admin_id = $this->session->userdata('loggedin');
           $this->load->model("my_blog_post_model");
           $this->load->helper('common_option_helper.php');
           
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    // "index" function definition...
    public function index($page) 
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
                                        'js/jquery/JSON/json2.js',
										'js/backend/manage_shout.js'
                                        ) );
                                        
             parent::_add_css_arr( array('css/dd.css'
                                        ) );
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 5;
            $data['submenu'] = 7;
            
            
            $this->session->set_userdata('search_condition','');
            ### ajax call ###
            ob_start();
            $this->minister_shout_listing_ajax(0);
            $data['content'] = ob_get_contents();
            ob_end_clean();
            ### end ajax call ###
            
           
            # rendering the view file...
            $VIEW_FILE = "admin/media_center/ministers-shout.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }//end of index
    
   
    public function add_shouts() 
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
                                        'js/jquery/JSON/json2.js',
										// TINYMCE CALL...
										'tiny_mce/plugins/tinybrowser/tb_tinymce.js.php',
										'tiny_mce/tiny_mce.js',
									    'js/backend/minister-shouts/add_shouts.js'
                                        ) );
                                        
             parent::_add_css_arr( array('css/dd.css'
                                        ) );
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 5;
            $data['submenu'] = 7;
            
            
            $data['categories'] = $this->my_blog_post_model->get_all_news_cat();
            
            #$this->session->set_userdata('where','');
            #$this->session->set_userdata('order_by','');
            
            $current_page = ($this->session->userdata('current_page')!='')? $this->session->userdata('current_page'):$page;
            $data['current_page'] = $current_page;
            
            ### ajax call ###
            ob_start();
            $this->minister_shout_listing_ajax($current_page);
            $data['content'] = ob_get_contents();
            ob_end_clean();
            ### end ajax call ###
            
            $this->session->set_userdata('current_page','');
           
            # rendering the view file...
            $VIEW_FILE = "admin/media_center/add_edit_minister_shouts.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }//end of index
   
    
    public function minister_shout_listing_ajax($page=0)
    {
        try
        { 
			if($this->input->post('type') ==  'all'){
				$this->session->set_userdata('search_condition','');
			}
			
			## seacrh conditions : filter ############
		 	$WHERE_COND = '';
			 if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) :
				
				$WHERE_COND = " WHERE 1 AND U.is_minister  = 1 ";
				
				$txt_blog_title = get_formatted_string(trim($this->input->post('txt_blog_title')));
				$WHERE_COND .= ($txt_blog_title=='')?'':" AND ( BP.s_post_title LIKE '%".$txt_blog_title."%')";
			    
				$txt_username = get_formatted_string(trim($this->input->post('txt_name')));
				$WHERE_COND .= ($txt_username=='')?'':" AND (  CONCAT(U.s_first_name,' ',U.s_last_name) LIKE  '%".$txt_username."%')";
															
															
				$this->session->set_userdata('search_condition',$WHERE_COND);
			
	
			 endif;  
		   	
			if($this->session->userdata('search_condition') != '')
				$s_where = $this->session->userdata('search_condition');
			else
				$s_where =  " WHERE 1 AND U.is_minister  = 1 ";
				
			$order_by = "`id` DESC ";
			
		   	$result = $this->my_blog_post_model->get_minister_shouts_list($s_where,$page,$this->pagination_per_page,$order_by);
            $resultCount = count($result);
	
			$total_rows = $this->my_blog_post_model->get_minister_shouts_count($s_where);
			
			
			## end seacrh conditions : filter ############
			
			//pr($result,1);
			#Jquery Pagination Starts
           	$this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/media_center/minister_shouts/minister_shout_listing_ajax";
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
			$data['result_arr'] = $result;
			$data['no_of_result'] = $total_rows;
			$data['current_page'] = $page;
          
			# loading the view-part...
          echo  $this->load->view('admin/media_center/ministers_shout_ajax.phtml', $data,TRUE);
		 }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
    }
    
    
    
    
  
    
     public function change_status()
	 {
			
			$data = $this->data;
			$page  =  intval($this->input->post('cur_page'));
			$i_status = intval($this->input->post('i_status'));
			$cur_status = intval($this->input->post('cur_status'));
			$ID = $this->input->post('record_id');
			
			
			
			if($this->session->userdata('user_id') !="")
			{
				$this->my_blog_post_model->change_status($i_status,$ID);
				if($i_status==1)
				   {
					 
						$action_txt =
							 '<input name="" title="ENABLE" type="button" class="btn-06" onclick="javascript:changeStatus(\''.$ID.'\',\'2\',\''.$i_status.'\')"  value="DISABLE"/>';
					
				   }
				 else if($i_status==2)
				   {
						$action_txt =
							 '<input name="" title="DISABLE" type="button" class="btn-06" onclick="javascript:changeStatus(\''.$ID.'\',\'1\',\''.$i_status.'\')"  value="ENABLE"/>';
					
				   } 
			}
			else{
			    
				$SUCCESS_MSG = "An error has occured! please try again. ";
				echo json_encode(array('result'=>false,
                					   'u_id'=>$ID,
									   'action_txt' =>$action_txt,
									   'i_status' => $cur_status,
                					   'msg'=>$SUCCESS_MSG , 'redirect'=>true)); exit;
			}
			
			
			$SUCCESS_MSG = "Status changed successfully! ";
	    
			# view part...
			    ob_start();
                $content = '';
                ob_end_clean();
                
                echo json_encode(array('result'=>'success',
                					   'u_id'=>$ID,
									   'action_txt' =>$action_txt,
									   'i_status' => $cur_status,
                					   'msg'=>$SUCCESS_MSG ,'redirect'=>false));
	 }
    
   
     
    //// function to Delete donation
    public function delete_shout($id)
    {
		$i_ret=$this->my_blog_post_model->delete_by_id($id);
		echo json_encode(array('success'=>true));
		exit;
		
	} // end of Delete banner function...
    
       

    
    
    
    
}// end of controller
    