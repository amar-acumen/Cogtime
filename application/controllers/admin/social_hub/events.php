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
* @link model/Events_model.php
* @link views/##
*/

class Events extends Admin_base_Controller
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
			$this->upload_path = BASEPATH.'../uploads/events_photo/';
			            
            # loading reqired model & helpers...
            // $this->load->helper('###');
			
            $this->load->model("events_model");
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
									     'js/backend/manage_events.js') );
                                        
             parent::_add_css_arr( array('css/dd.css',
                                        ) );
            # adjusting header & footer sections [End]...
			$data['top_menu_selected'] = 3;
			$data['submenu'] = 6;
         
            
			// fetching data
			$WHERE_COND = " WHERE 1 ";
			$this->session->set_userdata('search_condition',$WHERE_COND);
			$page=0;
			$order_by = "`dt_start_time` DESC ";
			
			ob_start();
            $this->ajax_pagination();
            $data['result_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
			
                
            # rendering the view file...
            $VIEW_FILE = "admin/social_hub/events/manage_events.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }


    
    
    # function to Add new advertisement
    public function add_info()
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
									     'js/backend/manage_events.js') );
                                        
              parent::_add_css_arr( array('css/dd.css',
                                        ) );
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 3;
			$data['submenu'] = 6;
			$data['mode']="add";
            $data['error_country'] = '';
			
			//Submitted Form
            if($_POST)
            {//pr($_POST,1);
				$posted=array();
                $posted["s_title"]= get_formatted_string(trim($this->input->post("txt_title")));
				$posted["s_address"]= get_formatted_string(trim($this->input->post("txt_add")));
				
				$posted["s_city"]= get_formatted_string(trim($this->input->post("txt_city")));
				$posted["s_state"]= get_formatted_string(trim($this->input->post("txt_state")));
				$posted["s_postcode"]= get_formatted_string(trim($this->input->post("txt_postcode")));
				
				$posted["i_country_id"]= intval(decrypt(trim($this->input->post("country"))));
				
				//echo intval(trim($this->input->post("country")));
				$posted["start_date"]= trim($this->input->post("date_to1"));
				$posted["end_date"]=trim($this->input->post("date_end"));
				$posted["start_time"]= trim($this->input->post("todo_strt_frm"));
				$posted["s_desc"]= get_formatted_string(trim($this->input->post("txt_desc")));
				$posted["dt_start_date"]= $posted["start_date"];
				$posted["dt_end_date"]=$posted["end_date"];
				$posted["end_time"]= trim($this->input->post("todo_end_frm"));
				$posted["rem_time"]=trim($this->input->post("todo_rem_time"));
				//pr($posted);
				
				
				//validation starts here 
				//pr($posted);
				
				
				//uploading banner Image
				
				for($i=1; $i < 6 ; $i++)
				{
					$fileElementName = 'adv_image_'.$i;	
					  $data["file_error_$fileElementName_".$i] = ''; 
					  
					 # echo $_FILES[$fileElementName]['name'];
	  
					   if(!empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] != '') 
							  {
									  preg_match('/(^.*)\.([^\.]*)$/', $_FILES[$fileElementName]['name'], $matches);
									  $ext = "";
									  if(count($matches)>0) {
										  $ext = strtolower($matches[2]);
										  $original_name = $matches[1];
									  }
									  else
										  $original_name = 'image';
					  
									  if ( !in_array($ext , $this->config->item('VALID_IMAGE_EXT'))) 
									  {
										   $data["file_error_$fileElementName_".$i] ="<div class=\"error_massage\" id=\"err_msg\">supported extensions are ".implode(' , ',$this->config->item('VALID_IMAGE_EXT'))."</div>";
									  }
									  
									  
									   if($_FILES[$fileElementName]['size'] > $this->config->item('MAX_UP_FILE_SIZE')*1024*1024)
									   {
										  $data["file_error_$fileElementName_".$i] = "<div class=\"error_massage\" id=\"err_msg\">Maximum file upload size is ".$this->config->item('MAX_UP_FILE_SIZE')." MB.</div>";
									   }
								  
									  
									  //// check for uploaded banner file's dimension [End]...    
							  }
							  
							  if(empty($_FILES['adv_image_1']['tmp_name']) || $_FILES['adv_image_1']['tmp_name'] == '') 
							  {
							  
								$data["file_error_adv_image_1"] = "<div class=\"error_massage\" id=\"err_msg\">* Required Field</div>";
							  
							  }
				}
				//End uploading picture
				
				
					
				$this->form_validation->set_message('required', '* Required Field.');		
				$this->form_validation->set_rules('txt_title', "* required " ,'trim|required');
				$this->form_validation->set_rules('txt_add', "* required " ,'trim|required');
				$this->form_validation->set_rules('txt_postcode', "* required " ,'trim|required');
				$this->form_validation->set_rules('txt_city', "* required " ,'trim|required');
				$this->form_validation->set_rules('txt_state', "* required " ,'trim|required');
				$this->form_validation->set_rules('date_to1', "* required " ,'trim|required');
				$this->form_validation->set_rules('date_end', "* required " ,'trim|required');
				
				//$this->form_validation->set_rules('todo_strt_frm', "* Required Field.", 'trim|required');
				//$this->form_validation->set_rules('todo_end_frm', "* Required Field.", 'trim|required');
				/*$this->form_validation->set_rules('txt_desc',"* required", 'trim|required');*/
				
				if( trim($this->input->post('todo_strt_frm'))=='-1') 
				{
							$data['err_todo_strt_frm'] = "* Required Field.";
				}
				
				if( trim($this->input->post('todo_end_frm'))=='-1') 
				{
						$data['err_todo_end_frm'] = "* Required Field.";
				}
				
				/*if( trim($this->input->post('todo_rem_time'))=='-1') 
				{
						$data['err_todo_rem_time'] = "* Required Field.";
				}*/
			
				
				if(trim($this->input->post("country"))  == '-1'){
					$data['error_country'] = '* Required Field.';
					
				}
				
			
				//validation ends here
				if ($this->form_validation->run() == FALSE || $data['error_country'] != '' 
					|| $data["file_error_$fileElementName_1"] != ''  || $data["file_error_$fileElementName_2"] != '' || $data["file_error_$fileElementName_3"] != '' || $data["file_error_$fileElementName_4"] != '' || $data["file_error_$fileElementName_5"] != '')
				{
				  
				   ////////Display the add form with posted values within it////
                    $data["posted"]=$posted;/*don't change*/
					
					
					
				}
				else
				{
				
				//adding to database
				$info=array();
				$info["s_title"]= get_formatted_string(trim($posted["s_title"]));
				$info["i_host_id"] = intval(decrypt($this->session->userdata('user_id')));
				$info["i_user_type"] = intval(($this->session->userdata('user_type')));
				
				$info["s_desc"]= get_formatted_string(trim($posted["s_desc"]));
				
				$info["s_city"]= get_formatted_string(trim($posted["s_city"]));
				$info["s_state"]= get_formatted_string(trim($posted["s_state"]));
				$info["s_postcode"]= get_formatted_string(trim($posted["s_postcode"]));
				$info["s_address"]= get_formatted_string(trim($posted["s_address"]));
				$info["i_country_id"]= intval(trim($posted["i_country_id"]));
				$start_time  =  get_db_dateformat($posted["start_date"],'/').' '.$posted["start_time"] ; 
				$info["dt_start_time"] = $start_time;
				$end_time=		get_db_dateformat($posted["end_date"],'/').' '.$posted["end_time"] ;
				$info['dt_end_time']= $end_time;
				
				for($i=1; $i < 6 ; $i++){
					
					//if($posted['adv_image_'.$i] != ''){
					$info["s_image_".$i] = $this->_upload_profile_image(trim($_FILES['adv_image_'.$i]['name']) ,'adv_image_'.$i);
					//}
				}
				$info["t_start_time"] = trim($this->input->post('todo_strt_frm'));
				$info["t_end_time"] = trim($this->input->post('todo_end_frm'));
				$info["t_remind_time"] = trim($this->input->post('todo_rem_time'));
				$date_a = new DateTime($info["dt_start_time"]);
				$date_b = new DateTime(get_db_dateformat($this->input->post('date_to'),'/').' '.$this->input->post('todo_rem_time') );
				$interval=date_diff($date_a,$date_b);
				$info["t_remind_me_back"] = $interval->format('%h:%i:%s');
				$info['dt_created_on'] = get_db_datetime();
				//pr($info,1);
                $i_newid = $this->events_model->insert($info); //echo $this->db->last_query();
				$re_page = admin_base_url() ."social_hub/events.html";
				header("location:".$re_page);
				exit;
					
				}
				
			}
		 	// End Submitted Form
			 $VIEW_FILE = "admin/social_hub/events/add-edit_events.phtml";
            parent::_render($data, $VIEW_FILE);
			
		}
		
		catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
		
	}   // end of Add new banner function...
    
    
    
    
    //// function to Edit the Existing advertisement
    public function edit_info($id)
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
									     'js/backend/manage_events.js') );
                                        
            parent::_add_css_arr( array('css/dd.css',
                                        ) );
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 3;
			$data['submenu'] = 6;
			# rendering the view file...
			$data['mode']="edit";
		//Submitted Form
            if($_POST)
            {
				$posted=array();
                $posted["s_title"]= get_formatted_string(trim($this->input->post("txt_title")));
				$posted["s_address"]= get_formatted_string(trim($this->input->post("txt_add")));
				
				$posted["s_city"]= get_formatted_string(trim($this->input->post("txt_city")));
				$posted["s_state"]= get_formatted_string(trim($this->input->post("txt_state")));
				$posted["s_postcode"]= get_formatted_string(trim($this->input->post("txt_postcode")));
				
				$posted["i_country_id"]= intval(decrypt(trim($this->input->post("country"))));
				
				//echo intval(trim($this->input->post("country")));
				$posted["start_date"]= trim($this->input->post("date_to1"));
				$posted["start_time"]= trim($this->input->post("todo_strt_frm"));
				$posted["s_desc"]= get_formatted_string(trim($this->input->post("txt_desc")));
				$posted["dt_start_date"]= trim($this->input->post("date_to1"));
				$posted["end_date"]=trim($this->input->post("date_end"));
				$posted["dt_end_date"]=$posted["end_date"];
				$posted["end_time"]= trim($this->input->post("todo_end_frm"));
				$posted["rem_time"]=trim($this->input->post("todo_rem_time"));
				
				//validation starts here 
				//pr($posted,1);
				
				 //uploading banner Image
				 
				 
				for($i=1; $i < 6 ; $i++)
				{
					  $fileElementName = 'adv_image_'.$i;    
					  $data["file_error_$fileElementName_".$i] = ''; 
  
					  if(!empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] != '') 
					  {
							  preg_match('/(^.*)\.([^\.]*)$/', $_FILES[$fileElementName]['name'], $matches);
							  $ext = "";
							  if(count($matches)>0) {
								  $ext = strtolower($matches[2]);
								  $original_name = $matches[1];
							  }
							  else
								  $original_name = 'image';
			  
							  if ( !in_array($ext , $this->config->item('VALID_IMAGE_EXT'))) 
							  {
								  $data["file_error_$fileElementName_".$i] ="<div class=\"error_massage\" id=\"err_msg\">supported extensions are ".implode(' , ',$this->config->item('VALID_IMAGE_EXT'))."</div>";
							  }
							  
							  
							  if($_FILES[$fileElementName]['size'] > $this->config->item('MAX_UP_FILE_SIZE')*1024*1024)
							  {
								  $data["file_error_$fileElementName_".$i] = "<div class=\"error_massage\" id=\"err_msg\">Maximum file upload size is ".$this->config->item('MAX_UP_FILE_SIZE')." MB.</div>";
							  }
					  }
					  
				}
				//End uploading picture
				
					
				$this->form_validation->set_message('required', '* Required Field.');		
				$this->form_validation->set_rules('txt_title', "* required " ,'trim|required');
				$this->form_validation->set_rules('txt_add', "* required " ,'trim|required');
				$this->form_validation->set_rules('txt_postcode', "* required " ,'trim|required');
				$this->form_validation->set_rules('txt_city', "* required " ,'trim|required');
				$this->form_validation->set_rules('txt_state', "* required " ,'trim|required');
				$this->form_validation->set_rules('date_to1', "* required " ,'trim|required');
				
				$this->form_validation->set_rules('date_end', "* required " ,'trim|required');
				
				//$this->form_validation->set_rules('todo_strt_frm', "* Required Field.", 'trim|required|callback_check_time');
				//$this->form_validation->set_rules('todo_end_frm', "* Required Field.", 'trim|required|callback_check_time');
				//$this->form_validation->set_rules('txt_desc',"* required", 'trim|required');
				
				
				if(trim($this->input->post("country"))  == '-1'){
					$data['error_country'] = '* Required Field.';
					
				}
				if( trim($this->input->post('todo_end_frm'))=='-1') 
				{
						$data['err_todo_end_frm'] = "* Required Field.";
				}
				/*
				if( trim($this->input->post('todo_rem_time'))=='-1') 
				{
						$data['err_todo_rem_time'] = "* Required Field.";
				}*/
			
				
				if(trim($this->input->post("country"))  == '-1'){
					$data['error_country'] = '* Required Field.';
					
				}
				
			
				//validation ends here
				if ($this->form_validation->run() == FALSE || $data['error_country'] != '')
				{
				    $info=$this->events_model->get_by_id($id);
					$posted["s_image_1"]= trim($info["s_image_1"]);
					$posted["s_image_2"]= trim($info["s_image_2"]);
					$posted["s_image_3"]= trim($info["s_image_3"]);
					$posted["s_image_4"]= trim($info["s_image_4"]);
					$posted["s_image_5"]= trim($info["s_image_5"]);
				 ////////Display the add form with posted values within it////
                    $data["posted"]=$posted;/*don't change*/
				}
				else
				{
				
					//adding to database
					$info=array();
					$info["s_title"]= get_formatted_string(trim($posted["s_title"]));
					$info["i_host_id"] = intval(decrypt($this->session->userdata('user_id')));
					$info["i_user_type"] = intval(($this->session->userdata('user_type')));
					$info["s_desc"]= get_formatted_string(trim($posted["s_desc"]));
					
					$info["s_city"]= get_formatted_string(trim($posted["s_city"]));
					$info["s_state"]= get_formatted_string(trim($posted["s_state"]));
					$info["s_postcode"]= get_formatted_string(trim($posted["s_postcode"]));
					
					for($i=0; $i<6 ;$i++){
						if( $_FILES['adv_image_'.$i]['name'] != "")
						$info["s_image_".$i] = $this->_upload_profile_image(trim($posted['adv_image_'.$i]) , 'adv_image_'.$i);
					}
					$info["t_start_time"] = trim($this->input->post('todo_strt_frm'));
				$info["t_end_time"] = trim($this->input->post('todo_end_frm'));
				$info["t_remind_time"] = trim($this->input->post('todo_rem_time'));
				$date_a = new DateTime($info["dt_start_time"]);
				$date_b = new DateTime(get_db_dateformat($this->input->post('date_to'),'/').' '.$this->input->post('todo_rem_time') );
				$interval=date_diff($date_a,$date_b);
				$info["t_remind_me_back"] = $interval->format('%h:%i:%s');
					$info["s_address"]= get_formatted_string(trim($posted["s_address"]));
					$info["i_country_id"]= intval(trim($posted["i_country_id"]));
					$start_time  =  get_db_dateformat($posted["start_date"],'/').' '.$posted["start_time"] ; 
					$info["dt_start_time"] = $start_time;
					$end_time=		get_db_dateformat($posted["end_date"],'/').' '.$posted["end_time"] ;
					$info['dt_end_time']= $end_time;
					$info['dt_updated_on'] = get_db_datetime();
					
					//echo ' sys ='.date('Y-m-d H:i:s'); exit;
					
                    $i_newid = $this->events_model->update($info,$id);
					$re_page = admin_base_url() ."social_hub/events.html";
					header("location:".$re_page);
					exit;
					
				}
				
			}
			else
			{
				$info=$this->events_model->get_by_id($id);
				
                $posted=array();
				$posted["id"]= trim($info["id"]);
				$posted["s_title"]= trim($info["s_title"]);
				$posted["s_desc"]= trim($info["s_desc"]);
				
				$posted["i_host_id"]= trim($info["i_host_id"]);
				$posted["s_city"]= trim($info["s_city"]);
				$posted["s_state"]= trim($info["s_state"]);
				$posted["s_address"]= trim($info["s_address"]);
				$posted["s_postcode"]= trim($info["s_postcode"]);
				
				$posted["i_country_id"]= $info["i_country_id"];
				
				$posted["dt_start_date"]= getShortDate($info["dt_start_time"],3);
				$posted["dt_end_date"]= getShortDate($info["dt_end_time"],3);
				$posted["start_time"]= $info["t_start_time"];#getShortDateWithTime($info["dt_start_time"],8);
				$posted["end_time"]= $info["t_end_time"];#getShortDateWithTime($info["dt_end_time"],8);
				$posted["rem_time"]=getShortDateWithTime($info["t_remind_time"],8);
				$posted["s_image_1"]= trim($info["s_image_1"]);
				$posted["s_image_2"]= trim($info["s_image_2"]);
				$posted["s_image_3"]= trim($info["s_image_3"]);
				$posted["s_image_4"]= trim($info["s_image_4"]);
				$posted["s_image_5"]= trim($info["s_image_5"]);
				#pr($posted);
				
			}
			
			 $data['posted']=$posted;
			 $VIEW_FILE = "admin/social_hub/events/add-edit_events.phtml";
            parent::_render($data, $VIEW_FILE);
			
		}
		catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
		
		
	}	// end of edit banner function...
    
    
    
    
    
    
    //// function to Delete Banner
    public function delete_information ($id)
    {
		$i_ret=$this->events_model->delete_by_id($id);
		$re_page = admin_base_url() ."social_hub/events.html";
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
				
				$s_name = get_formatted_string(trim($this->input->post('txt_name')));
				$WHERE_COND .= ($s_name=='')?'':" AND (U.s_name LIKE '".$s_name."%' OR U.s_last_name  LIKE '".$s_name."%' )";
				
				$s_title = get_formatted_string(trim($this->input->post('txt_title')));
				$WHERE_COND .= ($s_title=='')?'':" AND (E.s_title LIKE '".$s_title."%')";
				
				$s_city = get_formatted_string(trim($this->input->post('txt_city')));
				$WHERE_COND .= ($s_city=='')?'':" AND (E.s_city LIKE '".$s_city."%')";
				
				$s_state = get_formatted_string(trim($this->input->post('txt_state')));
				$WHERE_COND .= ($s_state=='')?'':" AND (E.s_state LIKE '".$s_state."%')";
				
				$s_country = get_formatted_string(trim($this->input->post('txt_country')));
				$WHERE_COND .= ($s_country=='')?'':" AND (C.s_country_name LIKE '".$s_country."%')";
				
				$s_address = get_formatted_string(trim($this->input->post('txt_address')));
				$WHERE_COND .= ($s_address=='')?'':" AND (E.s_address LIKE '".$s_address."%')";
				
			
				if($this->input->post('date_to1') != ''){
					 $dt_start_date = get_db_dateformat($this->input->post('date_to1'));
					$WHERE_COND .= ($dt_start_date=='')?'':" AND (DATE(E.dt_start_time) >='".$dt_start_date."' )";
				}
				
				if($this->input->post('date_end1') != ''){
					 $dt_end_date = get_db_dateformat($this->input->post('date_end1'));
					$WHERE_COND .= ($dt_end_date=='')?'':" AND (DATE(E.dt_end_time) <='".$dt_end_date."' )";
				}
			
				$this->session->set_userdata('search_condition',$WHERE_COND);
			//echo $WHERE_COND;exit;
			
           endif;  
		   	
			$s_where = $this->session->userdata('search_condition');
			//$order_by = "`dt_start_time` ASC ";
			$order_by = "`id` DESC ";
		   	$result = $this->events_model->get_list($s_where,$page,$this->pagination_per_page,$order_by);
            $resultCount = count($result);
	//echo $this->db->last_query(); exit;
			$total_rows = $this->events_model->get_list_count($s_where);
			
			if( ( !is_array($result) || !count($result) ) && $total_rows ) {
                $page = $page - $this->pagination_per_page;
                
                $result = $this->events_model->get_list($s_where, $page, $this->pagination_per_page,$ORDER_BY);
            }
			## end seacrh conditions : filter ############
			
			//pr($result,1);
			#Jquery Pagination Starts
           	$this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/social_hub/events/ajax_pagination";
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
          echo  $this->load->view('admin/social_hub/events/manage_events_ajax.phtml', $data,TRUE);
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
				$this->events_model->change_status($i_status,$ID);
				if($i_status==1)
				   {
					 
						$action_txt =
							 '<input name="" title="Show" type="button" class="btn-01" onclick="javascript:changeStatus(\''.$ID.'\',\'2\',\''.$i_status.'\')"  value="HIDE"/>';
					
				   }
				 else if($i_status==2)
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
            $this->form_validation->set_message('valid_url', "* Not a valid url.");
		    return FALSE;
        }

        return TRUE;
    } 
	
	 function check_time($s_time)
	   {
		
		/*if(!preg_match("/^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])(:([0-5]?[0-9]))?$/", $s_time))*/
		if(!preg_match("/^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])?$/", $s_time))
		{
		  $this->form_validation->set_message('check_time', '* Required Field ');
		  return false;
		}
	   
		else
	   return true;
	   
	 }
	 
	 
	 
	  public function _upload_profile_image($prev_img = '', $fileElementName)
      {
      
	  // pr($_FILES);
	    //$fileElementName = 'adv_image';	 
        if(!empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] != '') 
		{
				preg_match('/(^.*)\.([^\.]*)$/', $_FILES[$fileElementName]['name'], $matches);
				$ext = "";
				if(count($matches)>0) {
					$ext = strtolower($matches[2]);
					$original_name = $matches[1];
				}
				else
					$original_name = 'image';

			
					 $imagename = createImageName( $original_name ); 

					if(test_file($this->upload_path.$imagename.'-thumb.'.$ext)) {
						for( $i=0; test_file($this->upload_path.$imagename.'-'.$i.'-thumb.'.$ext); $i++ ) {
						}

						$new_imagename = $imagename.'-'.$i;
					}
					else {
						$new_imagename = $imagename;
					}

					$this->imagename = $new_imagename;

					$this->upload_image = $this->upload_path.$new_imagename.'.'.$ext;
					//echo $this->upload_path; exit;
					
					@move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $this->upload_image);
					
					
					$config = array();

					$config['source_image'] = $this->upload_image;
					$config['thumb_marker'] = '-thumb';
					$config['crop'] = false;
					$config['width'] = 60;
					$config['height'] = 60;
					$config1['crop_from'] = 'middle';
                    #$config['within_rectangle'] = true;
                    $config['small_image_resize'] = 'no_resize';
					resize_exact($config);
                    
					unset($config);
					
					
					$config = array();

					$config['source_image'] = $this->upload_image;
					$config['thumb_marker'] = '-mid';
					$config['crop'] = false;
					$config['width'] = 119;
					$config['height'] = 131;
                    $config1['crop_from'] = 'middle';
                    #$config['within_rectangle'] = true;
                    $config['small_image_resize'] = 'inside';
					resize_exact($config);
					
					
					
					
					$config = array();

					$config['source_image'] = $this->upload_image;
					$config['thumb_marker'] = '-mid_FO';
					$config['crop'] = false;
					$config['width'] = 180;
					$config['height'] = 180;
                    $config1['crop_from'] = 'middle';
                    #$config['within_rectangle'] = true;
                    $config['small_image_resize'] = 'inside';
					resize_exact($config);
					
					
					$config = array();

					$config['source_image'] = $this->upload_image;
					$config['thumb_marker'] = '-main';
					$config['crop'] = false;
					$config['width'] = 200;
					$config['height'] = 200;
                    $config1['crop_from'] = 'middle';
                    #$config['within_rectangle'] = true;
                    $config['small_image_resize'] = 'inside';
					resize_exact($config);
					
					$config = array();
					
					$config['source_image'] = $this->upload_image;
					$config['thumb_marker'] = '-big';
					$config['crop'] = false;
					$config['width'] = 800;
					$config['height'] = 536;
                   // $config1['crop_from'] = 'middle';
                    $config['within_rectangle'] = true;
                    $config['small_image_resize'] = 'no_resize';
					resize_exact($config);

                
					
					$this->s_picture_path = $new_imagename.'.'.$ext;
					//echo $this->upload_image; 
					
					@unlink($this->upload_image); //Unlink the original image........
					//@unlink($this->upload_path.getThumbName($prev_img,'thumb')); //Unlink the prevoius image........
					//exit;
					return $this->s_picture_path;

				
			
					
				}
        else
        {
            return $prev_img; // Unchaged previous image
        }
        
        
    }
	
	
	//Generate endtime list
	public function generate_end_time_list($start_time)
	{
		try
		{
			
			if($start_time != -1){
				$start_hr_min = date('H:i',strtotime($start_time));
				$sel_html = '';
				
				$time = date('H:i', strtotime($start_hr_min) );	
					
				$sel_html .= makeOption_Endtime($time);				
			
				
				echo json_encode( array('success'=>true,'sel_html'=>$sel_html, 'start_time'=>$start_time));
			}else
			{
				echo json_encode( array('success'=>false,'sel_html'=>$sel_html, 'start_time'=>$start_time));
			}
		
		}
		catch(Exception $err_obj)
        {
            
        } 
	}	
    
}   // end of controller...