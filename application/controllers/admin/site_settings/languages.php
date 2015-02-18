<?php
/*********
* Author: 
* Purpose:
*  Controller For "language"
* 
* @package 
* @subpackage 
* 
* @link InfController.php 
* @link Base_Controller.php
* @link model/manage_cms_model.php
* @link views/##
*/

class Languages extends Admin_base_Controller
{
    private $upload_thumb_path;
    private $upload_thumb_image;
    private $thumb_imagename = '';
    
    private $upload_path;
    private $upload_image;
    private $imagename = '';
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
            $this->load->model("language_model");
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

        try
        {
			
            # adjusting header & footer sections [Start]...
            $data = $this->data;
            parent::_set_title("::: COGTIME Xtian network :::");
            parent::_set_meta_desc("::: COGTIME Xtian network :::");
            parent::_set_meta_keywords("::: COGTIME Xtian network :::");
            	parent::_add_js_arr( array( 
										'js/jquery.dd.js',
										'js/lightbox.js',
										//'editor/tiny_mce.js',
										//'editor/tiny_mce_editor.js',
									    //'js/backend/cms/manage_cms.js'
										) );
                                        
            parent::_add_css_arr( array('css/dd.css') );
            # adjusting header & footer sections [End]...
			$data['top_menu_selected'] = 1;
			$data['submenu'] = 14;
         
            
			// fetching data
			$WHERE_COND = " WHERE 1 ";
			$page=0;
			$order_by = " `id` DESC ";//" `id` ASC ";
			//---------------------- for pagination back ---------------------
            if($page_no!=0)
                $page=($page_no-1)*2;
            //---------------------- end pagination back ---------------------
			
			ob_start();
            $this->ajax_pagination();
            $data['result_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
			
                
            # rendering the view file...
            $VIEW_FILE = "admin/site_settings/language/manage_language.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }


    
    
    # function to Add new cms
    public function add_lang()
    {
		try
		{
			
			# adjusting header & footer sections [Start]...
            $data = $this->data;
            parent::_set_title("::: COGTIME Xtian network :::");
            parent::_set_meta_desc("::: COGTIME Xtian network :::");
            parent::_set_meta_keywords("::: COGTIME Xtian network :::");
           	parent::_add_js_arr( array( 
										'js/jquery.dd.js',
										'js/lightbox.js',
										'js/jquery.form.js',
										 'js/jquery/JSON/json2.js',
										//editor/tiny_mce.js',
										///'editor/tiny_mce_editor.js',
										// TINYMCE CALL...
										 // 'tiny_mce/plugins/tinybrowser/tb_tinymce.js.php',
										 // 'tiny_mce/tiny_mce.js',
										
								) );
                                        
            parent::_add_css_arr( array('css/dd.css') );
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 1;
			$data['submenu'] = 14;
			$data['mode']="add";
           
			
			//Submitted Form
            if($_POST)
            {
				$posted=array();
                $posted["lang"]= get_formatted_string($this->input->post("txt_title"));
				//$posted["s_page_content"]= get_formatted_string($this->input->post("elm1"));
				//$category = get_formatted_string($this->input->post("category"));
				
				
				
				
				//validation starts here 
				//pr($posted,1);
				
				$arr_messages = array();
			  
				if(trim($this->input->post('txt_title'))=='') 
				{		
						$arr_messages['title'] = "* Required Field";
				}	
				
				/*if($posted["s_page_content"]=='') 
				{		
						$arr_messages['elm1'] = "* Required Field";
				}
				if(intval($this->input->post('category'))=='-1') 
				{		
						$arr_messages['category'] = "* Required Field";
				}
			  		*/
				
					
			if( count($arr_messages)==0 ) 
			 {
					
					//adding to database
					$info=array();
					$info["s_language"]= trim($posted["lang"]);
					/*$info["s_page_content"]= trim($posted["s_page_content"]);
					$info["e_show_in_logged_in_page"]= trim($posted["e_show_in_logged_in_page"]);
					$info["e_show_in_non_logged_in_page"]= trim($posted["e_show_in_non_logged_in_page"]);
					$info["e_both"]= trim($posted["e_both"]);
					$info['dt_created_on'] = get_db_datetime();*/
					
					//pr($info,1);
					$i_newid = $this->language_model->insert($info);
					#echo $this->db->last_query(); exit;
					$re_page = admin_base_url() ."site-settings/languages.html";
					echo json_encode( array('success'=>true,'redirect'=>$re_page,'arr_messages'=>$arr_messages,'msg'=>'New Language has been added successfully.') );
					//exit;
			 }
			  else
			  {
				  echo json_encode( array('success'=>false, 'arr_messages'=>$arr_messages,'msg'=>'Error!') );
			  }
				exit;
			}
		 else{
		 	// End Submitted Form
			  $VIEW_FILE = "admin/site_settings/language/add-edit_lang.phtml";
			  //$this->load->view($VIEW_FILE, $data);
              parent::_render($data, $VIEW_FILE);
			}
			
		}
		
		catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
		
	}   // end of Add new banner function...
    
    
    
    
    //// function to Edit the Existing cms
    public function edit_lang($id)
    {//echo 'http://'.$_SERVER['HTTP_HOST'].'/cogtime/tiny_uploads/files/';
		try
		{
		
			 # adjusting header & footer sections [Start]...
            $data = $this->data;
            parent::_set_title("::: COGTIME Xtian network :::");
            parent::_set_meta_desc("::: COGTIME Xtian network :::");
            parent::_set_meta_keywords("::: COGTIME Xtian network :::");
            parent::_add_js_arr( array( 'js/jquery.form.js',
										'js/jquery.dd.js',
										'js/lightbox.js',
										//'editor/tiny_mce.js',
                                        'js/jquery/JSON/json2.js', 
										//'editor/tiny_mce_editor.js',
										 //'tiny_mce/plugins/tinybrowser/tb_tinymce.js.php',
										 //'tiny_mce/tiny_mce.js',
									    //'js/backend/cms/edit_cms.js'
										) );
                                        
            parent::_add_css_arr( array('css/dd.css') );
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 1;
			$data['submenu'] = 14;
			# rendering the view file...
			$data['mode']="edit";
			
			
			//Submitted Form
            if($_POST)
            {
				$posted=array();
                $posted["s_language"]= get_formatted_string($this->input->post("txt_title"));
				/*$posted["s_page_content"]= get_formatted_string($this->input->post("elm1"));
				$category = get_formatted_string($this->input->post("category"));
				
				if($category == 'both'){
					$posted["e_both"]= 'yes';
					$posted["e_show_in_non_logged_in_page"]= 'no';
					$posted["e_show_in_logged_in_page"]= 'no';
				}
				else if($category == 'non_logged'){
					$posted["e_both"]= 'no';
					$posted["e_show_in_non_logged_in_page"]= 'yes';
					$posted["e_show_in_logged_in_page"]= 'no';
				}
				else{
					$posted["e_both"]= 'no';
					$posted["e_show_in_non_logged_in_page"]= 'no';
					$posted["e_show_in_logged_in_page"]= 'yes';
				}*/
				
				
				//validation starts here 
				//pr($posted,1);
				
				$arr_messages = array();
			  
				if(trim($this->input->post('txt_title'))=='') 
				{		
						$arr_messages['title'] = "* Required Field";
				}	
				
				/*if($posted["s_page_content"]=='') 
				{		
						$arr_messages['elm1'] = "* Required Field";
				}
				if(intval($this->input->post('category'))=='-1') 
				{		
						$arr_messages['category'] = "* Required Field";
				}
			  		
				*/
					
				if( count($arr_messages)==0 ) 
				 {
						
						//adding to database
						$info=array();
						$info["s_language"]= trim($posted["s_language"]);
						/*$info["s_page_content"]= trim($posted["s_page_content"]);
						$info["e_show_in_logged_in_page"]= trim($posted["e_show_in_logged_in_page"]);
						$info["e_show_in_non_logged_in_page"]= trim($posted["e_show_in_non_logged_in_page"]);
						$info["e_both"]= trim($posted["e_both"]);
						$info['dt_created_on'] = get_db_datetime();*/
						
						//pr($info,1);
						$i_newid = $this->language_model->update($info, $id);
						//echo $this->db->last_query(); exit;
						$re_page = admin_base_url() ."site-settings/languages.html";
						echo json_encode( array('success'=>true,'redirect'=>$re_page,'arr_messages'=>$arr_messages,'msg'=>'This language has been updated successfully.') );
					exit;
				 }
				  else
				  {
					  echo json_encode( array('success'=>false, 'arr_messages'=>$arr_messages,'msg'=>'Error!') );
				  }
				exit;
			}// End Submitted Form
		 else{
		 	    $info=$this->language_model->get_by_id($id);
				
                $posted=array();
				$posted["id"]= trim($info["id"]);
				$posted["s_language"]= trim($info["s_language"]);
				/*$posted["s_page_content"]= trim($info["s_page_content"]);
				$posted["e_both"]= trim($info["e_both"]);
				$posted["e_show_in_non_logged_in_page"]= trim($info["e_show_in_non_logged_in_page"]);
				$posted["e_show_in_logged_in_page"]= trim($info["e_show_in_logged_in_page"]);*/
			    $data['posted']=$posted;
				#pr($posted,1);
			 	$VIEW_FILE = "admin/site_settings/language/add-edit_lang.phtml";
           		parent::_render($data, $VIEW_FILE);
			}
			
			
		}
		catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
		
		
	}	// end of edit banner function...
    
    
    
    
    
    
    //// function to Delete Banner
    public function delete_information ($id)
    {
		$i_ret=$this->language_model->delete_by_id($id);
		$re_page = admin_base_url() ."site-settings/languages.html";
					header("location:".$re_page);
					exit;
		
	} // end of Delete banner function...

	  # function to load ajax-pagination [AJAX CALL]...
    public function ajax_pagination($page=0)
    {
        try
        {
			  
		   	
			$order_by = " `id` DESC ";//" `id` ASC ";
		   	$result = $this->language_model->lang_list($s_where,$page,$this->pagination_per_page,$order_by);
            $resultCount = count($result);
			#echo $this->db->last_query(); exit;
			$total_rows = $this->language_model->lang_list_count($s_where);
			
			if( ( !is_array($result) || !count($result) ) && $total_rows ) {
                $page = $page - $this->pagination_per_page;
                
                $result = $this->language_model->lang_list($s_where, $page, $this->pagination_per_page,$ORDER_BY);
            }
			## end seacrh conditions : filter ############
			
			#pr($result);
			#Jquery Pagination Starts
           	$this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/site_settings/languages/ajax_pagination";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->pagination_per_page;
            $config['uri_segment'] = 5;
            $config['num_links'] = 9;
            $config['page_query_string'] = false;
            $config['prev_link'] = 'PREV';
            $config['next_link'] = 'NEXT';

          /*  $config['cur_tag_open'] = '<li> <span><a href="javascript:void(0)" class="select">';
            $config['cur_tag_close'] = '</a></span></li>';

            $config['next_tag_open'] = '<li><a href="javascript:void(0)">';
            $config['next_tag_close'] = '</a></li>';

            $config['prev_tag_open'] = '<li><a href="javascript:void(0)">';
            $config['prev_tag_close'] = '</a></li>';*/
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
          
			# loading the view-part...
          echo  $this->load->view('admin/site_settings/language/manage_lang_ajax.phtml', $data,TRUE);
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
				$this->language_model->change_status($i_status,$ID);
				if($i_status==1)
				   {
					 
						$action_txt =
							 '<input name="" title="Show" type="button" class="btn-01" onclick="javascript:changeStatus(\''.$ID.'\',\'0\',\''.$i_status.'\')"  value="HIDE"/>';
					
				   }
				 else if($i_status==0)
				   {
						$action_txt =
							 '<input name="" title="Hide" type="button" class="btn-01" onclick="javascript:changeStatus(\''.$ID.'\',\'1\',\''.$i_status.'\')"  value="SHOW"/>';
					
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
	
	
	
	public function valid_url($url)
    {
       
        if (!isValidURL($url) && $url!='')
        {
            $this->form_validation->set_message('valid_url', "* Not a valid url");
		    return FALSE;
        }

        return TRUE;
    } 
	
	
	
		//ordering in ajax
	function maintain_displayorder_ajax($selected_language, $page=0)
    {
        //sleep(2);
	   
			$actionID = $this->input->post('rid');
			$status = $this->input->post('status');
			
			
			# retrieving  info...
			$info_arr = $this->cms_model->get_by_id($actionID);
	
			$this->load->model("utility_model");
			$tbl=$this->db->CMS_PAGE;
	
		  //  $WHERE_COND_BEGIN = " `i_parent_id` = {$PARENT_CATEGORY_ID} AND `i_is_active` = 1 ";
			$this->utility_model->Ranking($status, $actionID, $WHERE_COND_BEGIN,$tbl);
		
            $order_by = " `i_order` DESC ";
			$result = $this->cms_model->cms_list($s_where,$page,$this->pagination_per_page, $order_by);
            $resultCount = count($result);
            //$total_rows = $this->mail_domains_model->gettotal_info($WHERE_COND);
			$total_rows = $this->cms_model->cms_list_count($WHERE_COND);
			
		/*	$order_by = " `id` ASC ";
		   	$result = $this->cms_model->cms_list($s_where,$page,$this->pagination_per_page,$order_by);
            $resultCount = count($result);
			#echo $this->db->last_query(); exit;
			$total_rows = $this->cms_model->cms_list_count($s_where);*/
			
			if( ( !is_array($result) || !count($result) ) && $total_rows ) {
                $page = $page - $this->pagination_per_page;
                
                $result = $this->cms_model->cms_list($s_where, $page, $this->pagination_per_page,$ORDER_BY);
            }
			
			#Jquery Pagination Starts
           	$this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/site_settings/cms_pages/ajax_pagination";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->pagination_per_page;
            $config['uri_segment'] = 6;
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
          echo  $this->load->view('admin/site_settings/cms/manage_cms_ajax.phtml', $data,TRUE);
		
	}
	
    
}   // end of controller...