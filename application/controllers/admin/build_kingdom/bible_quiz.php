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

class Bible_quiz extends Admin_base_Controller
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
           $this->load->model("bible_model");
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
										'js/jquery.form.js',
									    'js/jquery/JSON/json2.js') );
                                        
             parent::_add_css_arr( array('css/dd.css',
                                        ) );
            # adjusting header & footer sections [End]...
			$data['top_menu_selected'] = 6;
			$data['submenu'] = 5;
       
			$this->session->set_userdata('search_condition','');
			
			ob_start();
            $this->ajax_pagination();
            $data['result_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
			
                
            # rendering the view file...
            $VIEW_FILE = "admin/build_kingdom/bible_quiz.phtml";
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
			//echo $_POST['search_basic']; exit;
			## seacrh conditions : filter ############
		 	
			 if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) :
           
				$WHERE_COND = " WHERE 1  ";
				
				$s_question = trim($this->input->post('txt_question'));
				$WHERE_COND .= ($s_question=='0')?'':" AND ( C.s_question  LIKE  '%".$s_question."%')";
				
				$this->session->set_userdata('search_condition',$WHERE_COND);
			
            endif;  
		   	
			$s_where = $this->session->userdata('search_condition'); 

		   	$result = $this->bible_model->get_list($s_where,$page,$this->pagination_per_page,$order_by);
            $resultCount = count($result);
			$total_rows = $this->bible_model->get_list_count($s_where);
			
			
			
			//pr($result,1);
			#Jquery Pagination Starts
           	$this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/build_kingdom/bible_quiz/ajax_pagination";
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
          echo  $this->load->view('admin/build_kingdom/bible_quiz_ajax.phtml', $data,TRUE);
		 }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
    }
	
	
	
	//// function to Delete donation
    public function delete_quiz($id)
    {
		$i_ret=$this->bible_model->delete_by_id($id);
		echo json_encode(array('success'=>true));
		exit;
		
	} // end of Delete banner function...
	 
	 
	 public function add_quiz()
	 {
		try
		{
			
			$arr_messages = array();
			# error message trapping...
			if( trim($this->input->post('txt_question'))=='') 
			{
					$arr_messages['question'] = "* Required Field.";
			}
			
			if( trim($this->input->post('txt_answer'))=='') 
			{
					$arr_messages['answer'] = "* Required Field.";
			}
			
			

		   //pr($arr_messages);
			if( count($arr_messages)==0 ) {
					
				$info = array();
				
				$info['s_question'] = get_formatted_string($this->input->post('txt_question')); 
				$info['s_answer'] = get_formatted_string($this->input->post('txt_answer')); 
				$info['dt_created_on'] = get_db_datetime();
				
				$_ret = $this->bible_model->insert($info);
				
				ob_start();
				$this->ajax_pagination();
				$result_content = ob_get_contents(); //pr($data['result_content']);
				ob_end_clean();
					
				echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'Quiz added Successfully.',
						'html'=>$result_content));
			}
			else
			{
				echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'Error!') );
			}
		
		
		
		}
		catch(Exception $err_obj)
        {
            
        } 
	}
	
	
	public function edit_quiz($id)
	 {
		try
		{
			
			$arr_messages = array();
			
			if($_POST){
				
				  $id= $this->input->post('hd_edit_id'); 
				  # error message trapping...
				  if( trim($this->input->post('txt_edit_question'))=='') 
				  {
						  $arr_messages['edit_question'] = "* Required Field.";
				  }
				  
				  if( trim($this->input->post('txt_edit_ans'))=='') 
				  {
						  $arr_messages['edit_answer'] = "* Required Field.";
				  }
	  
				 //pr($arr_messages);
				  if( count($arr_messages)==0 ) {
						  
					  $info = array();
					  
					  $info['s_question'] = get_formatted_string($this->input->post('txt_edit_question')); 
					  $info['s_answer'] = get_formatted_string($this->input->post('txt_edit_ans')); 
					  $info['dt_updated_on'] = get_db_datetime();
					  $_ret = $this->bible_model->update($info, $id);
					  
					  ob_start();
					  $this->ajax_pagination();
					  $result_content = ob_get_contents(); //pr($data['result_content']);
					  ob_end_clean();
						  
					  echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'Quiz updated Successfully.', 'html'=>$result_content));
				  }
				  else
				  {
					  echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'Error') );
				  }
		
			}
			else
			{
				$info  = $this->bible_model->get_by_id($id);
				$info['s_answer'] = get_unformatted_string_edit($info['s_answer']);
				$info['s_question'] = get_unformatted_string_edit($info['s_question']);

				echo json_encode( array('success'=>true,'info'=>$info));

			}
		
		}
		catch(Exception $err_obj)
        {
            
        } 
	}
	
	public function view_quiz($id)
	 {
		try
		{
			
			  $info  = $this->bible_model->get_by_id($id);
			  
			  $info['s_answer'] = nl2br($info['s_answer']);
			  echo json_encode( array('success'=>true,'info'=>$info));
		
		}
		catch(Exception $err_obj)
        {
            
        } 
	}
	 
}   // end of controller...