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
* @link model/Abuse_dictionary_model.php
* @link views/##
*/

class Abuse_dictionary extends Admin_base_Controller
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
           $this->load->model("Abuse_dictionary_model");
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
            parent::_add_js_arr( array( 'js/lightbox.js','js/jquery.form.js',
									       'js/jquery/JSON/json2.js',
									     'js/backend/manage_abuse_dictionary.js') );
                                        
            parent::_add_css_arr( array() );
            # adjusting header & footer sections [End]...
			$data['top_menu_selected'] = 1;
			$data['submenu'] = 8;
         
            
			// fetching data
			$WHERE_COND = " WHERE 1 ";
			$this->session->set_userdata('search_condition',$WHERE_COND);
			$page=0;
			$order_by = " `id` DESC ";
			
			ob_start();
            $this->ajax_pagination();
            $data['result_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
			
                
            # rendering the view file...
            $VIEW_FILE = "admin/site_settings/abuse_dictionary/abuse_dictionary.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }


    
    
    
   
    //// function to Delete Banner
    public function delete_information ($id)
    {
		$i_ret=$this->Abuse_dictionary_model->delete_by_id($id);
		$re_page = admin_base_url() ."site_settings/abuse_dictionary.html";
					header("location:".$re_page);
					exit;
		
	} // end of Delete banner function...
	
	
	  # function to load ajax-pagination [AJAX CALL]...
    public function ajax_pagination($page=0)
    {
        try
        {
			## seacrh conditions : filter ############
		 	
			 if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) :
				$WHERE_COND = " WHERE 1  ";
				$s_word = get_formatted_string(trim($this->input->post('search_word')));
				$WHERE_COND .= ($s_word=='')?'':" AND (s_word LIKE '%".$s_word."%')";
				$this->session->set_userdata('search_condition',$WHERE_COND);
			
			
           endif;  
		   	
			$s_where = $this->session->userdata('search_condition');
			$order_by = " `id` DESC ";
		   	$result = $this->Abuse_dictionary_model->abuse_word_list($s_where,$page,$this->pagination_per_page,$order_by);
            $resultCount = count($result);
			//pr($result);
		   #echo $this->db->last_query(); exit;
			$total_rows = $this->Abuse_dictionary_model->abuse_word_list_count($s_where); 
			
			if( ( !is_array($result) || !count($result) ) && $total_rows ) {
                $page = $page - $this->pagination_per_page;
                
                $result = $this->Abuse_dictionary_model->abuse_word_list($s_where, $page, $this->pagination_per_page,$ORDER_BY);
            }
			## end seacrh conditions : filter ############
			
			//pr($result,1);
			#Jquery Pagination Starts
           	$this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/site_settings/abuse_dictionary/ajax_pagination";
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

            // getting   listing...
			$data['info_arr'] = $result;
			$data['no_of_result'] = $total_rows;
			$data['current_page'] = $page;
          
			# loading the view-part...
          echo  $this->load->view('admin/site_settings/abuse_dictionary/abuse_dictionary_ajax.phtml', $data,TRUE);
		 }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
    }
	
	
	   public function add_abuse()
	   {
		  try
			{
			  $arr_messages = array();
					
				if($_POST){
					  # error message trapping...
					  $err_count = 0;
					  for($i=1 ; $i < 6 ; $i++){
						  
						  if(trim($this->input->post('txt_word_'.$i))=='') 
						  {		
								  $err_count = $err_count + 1;
						  }						
					  }
					  if($err_count == 5){
							  $arr_messages['word_1'] = "* Required Field";

						  }
					  if( count($arr_messages)==0 ) {
						  
						  for($i=1 ; $i < 6 ; $i++){	
							  $info = array();
							   if($this->input->post('txt_word_'.$i) != ''){	
								   $info['s_word'] = get_formatted_string($this->input->post('txt_word_'.$i));	 
								   $info['dt_created_on'] = get_db_datetime();
								   $_ret = $this->Abuse_dictionary_model->insert($info);
								  
								  
							   }
						  }
						   echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'New Abuse word added successfully.') );
					  }
					  else
					  {
						  echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>t('Error!'),) );
					  }
			
				}
				
			}
			  catch(Exception $err_obj)
			  {
				  show_error($err_obj->getMessage());
			  }
		}
		
		
	   public function edit_abuse($id)
	   {
			  try
			  {
				$arr_messages = array();
				if($_POST){
					  
					    $id= intval($this->input->post('i_edit_id'));
						
						
						# error message trapping...
						if( trim($this->input->post('txt_word'))=='') 
						{
								$arr_messages['word'] = "* Required Field";
						}
											
						if( count($arr_messages)==0 ) {
								
							$info = array();
							
							$info['s_word'] = get_formatted_string($this->input->post('txt_word'));	 
							$info['dt_created_on'] = get_db_datetime();
							$_ret = $this->Abuse_dictionary_model->update($info ,$id);
							
							echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'Abuse word updated successfully.') );
						}
						else
						{
							echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>t('Error!'),) );
						}
			  
				  }else
				  {
					  
					    $data['posted'] = $this->Abuse_dictionary_model->get_by_id($id) ;
						echo json_encode( array('result'=>success,'s_word'=>$data['posted']['s_word']) );	  

				  }
				  
			  }
			  catch(Exception $err_obj)
			  {
				  show_error($err_obj->getMessage());
			  }
		}
    
}   // end of controller...