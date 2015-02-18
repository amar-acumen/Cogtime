<?php
/*********
* Author: 
* Purpose:
*  Controller For "Media_center_advertisement"
* 
* @package 
* @subpackage 
* 
* @link InfController.php 
* @link Base_Controller.php
* @link model/manage_advertisement_model.php
* @link views/##
*/

class Media_center_advertisement extends Admin_base_Controller
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
			$this->upload_path = BASEPATH.'../uploads/media_center_advertisement/';
                         //$this->upload_path1 = BASEPATH.'../uploads/media_banner/';
            # loading reqired model & helpers...
            // $this->load->helper('###');
            $this->load->model("manage_advertisement_model" , "mod_banner");
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
         // die('d');
        try
        {
			
            # adjusting header & footer sections [Start]...
            $data = $this->data;
            parent::_set_title("::: COGTIME Xtian network :::");
            parent::_set_meta_desc("::: COGTIME Xtian network :::");
            parent::_set_meta_keywords("::: COGTIME Xtian network :::");
            parent::_add_js_arr( array( 'js/lightbox.js',
									     'js/backend/manage_advertisement.js') );
                                        
            parent::_add_css_arr( array() );
            # adjusting header & footer sections [End]...
			$data['top_menu_selected'] = 1;
			$data['submenu'] = 19;
         
            
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
            $VIEW_FILE = "admin/site_settings/media_center_advertisement/manage_advertisement.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }


    
    
    # function to Add new advertisement
    public function add_advertisement()
    {
        //die('k');
		try
		{
			 # adjusting header & footer sections [Start]...
            $data = $this->data;
            parent::_set_title("::: COGTIME Xtian network :::");
            parent::_set_meta_desc("::: COGTIME Xtian network :::");
            parent::_set_meta_keywords("::: COGTIME Xtian network :::");
            parent::_add_js_arr( array() );
                                       //'js/backend/manage_hp_banner.js'=>'header') );
                                        
            parent::_add_css_arr( array() );
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 1;
			$data['submenu'] = 19;
			$data['mode']="add";
           
			
			//Submitted Form
            if($_POST)
            {
				$posted=array();
                $posted["s_title"]= get_formatted_string(trim($this->input->post("txt_title")));
				$posted["dt_start_date"]= trim($this->input->post("date_to1"));
				$posted["dt_end_date"]= trim($this->input->post("date_to2"));
				$posted["s_url"]= prep_url(trim($this->input->post("txt_url")));
                                $posted['p_loc'] = $this->input->post("page-loc");
                               // $posted['p_sub_loc'] =
				
				//uploading banner Image
					$fileElementName = 'adv_image';	
					$data["file_error_$fileElementName"] = ''; 

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
										 $data["file_error_$fileElementName"] ="<div class=\"error_massage\" id=\"err_msg\">supported extensions are ".implode(' , ',$this->config->item('VALID_IMAGE_EXT'))."</div>";
									}
									
									
									if($_FILES[$fileElementName]['size'] > $this->config->item('MAX_UP_FILE_SIZE')*1024*1024)
									 {
									    $data["file_error_$fileElementName"] = "<div class=\"error_massage\" id=\"err_msg\">Maximum file upload size is ".$this->config->item('MAX_UP_FILE_SIZE')." MB.</div>";
									 }
								
									
                                    //// check for uploaded banner file's dimension [Start]...	
                                      /*  list($uploaded_width, $uploaded_height) = getimagesize( $_FILES[$fileElementName]['tmp_name'] );
                                        if( $uploaded_width>$this->config->item('hp_banner_width_limit') ||
                                            $uploaded_height>$this->config->item('hp_banner_height_limit') ) {
                                        
                                            $data["file_error_$fileElementName"] = "<div class=\"error_massage\" id=\"err_msg\">Plz upload banner of preferred size.</div>";
                                            
                                        }*/
                                    //// check for uploaded banner file's dimension [End]...    
							}else
							{
							
							  $data["file_error_$fileElementName"] = "<div class=\"error_massage\" id=\"err_msg\">* Required Field</div>";
							
							}
				
				//End uploading picture
				
				//validation starts here 
				#pr($posted,1);
					
				///$this->form_validation->set_message('required', "Please provide"." %s");
				$this->form_validation->set_message('required', '* Required Field');		
				$this->form_validation->set_rules('txt_title', "* required " ,'trim|required');
				$this->form_validation->set_rules('txt_url', "* required " ,'trim|required|callback_valid_url');
				$this->form_validation->set_rules('date_to2', "* required", 'trim|required');
				$this->form_validation->set_rules('date_to2',"* required", 'trim|required');
				$this->form_validation->set_rules('page-loc', "* required " ,'trim|required');
				//$this->form_validation->set_rules('adv_image',"adv_image", 'trim|required|callback_chck_image_size');
			#echo "file_error_$fileElementName" ;echo $data["file_error_$fileElementName"]; exit;
				//validation ends here
				if ($this->form_validation->run() == FALSE || $data["file_error_$fileElementName"] != '')
				{
				  
				 
				   ////////Display the add form with posted values within it////
                    $data["posted"]=$posted;/*don't change*/
					
					
					
				}
				else
				{
				
				//adding to database
				$info=array();
				$info["s_title"]= get_formatted_string(trim($posted["s_title"]));
				$info["i_user_id"] = intval(decrypt($this->session->userdata('user_id')));
				$info["s_url"]= get_formatted_string(trim($posted["s_url"]));
				$info["s_image"] = $this->_upload_profile_image(trim($posted['adv_image']));
				$info["dt_start_date"] = get_db_dateformat($posted["dt_start_date"],'/');
				$info["dt_end_date"]   =  get_db_dateformat($posted["dt_end_date"],'/');
				$info['dt_created_on'] = get_db_datetime();
				$info['p_loc'] = $this->input->post("page-loc");
				//pr($info,1);
                $i_newid = $this->mod_banner->add_media_center_info($info);
				$re_page = admin_base_url() ."site_settings/media_center_advertisement";
				header("location:".$re_page);
				exit;
					
				}
				
			}
		 	// End Submitted Form
			 $VIEW_FILE = "admin/site_settings/media_center_advertisement/add-edit_advertisement.phtml";
            parent::_render($data, $VIEW_FILE);
			
		}
		
		catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
		
	}   // end of Add new banner function...
    
    
    
    
    //// function to Edit the Existing advertisement
    public function edit_advertisement($id)
            
    {
	//die('j');
        try
		{
			 # adjusting header & footer sections [Start]...
            $data = $this->data;
            parent::_set_title("::: COGTIME Xtian network :::");
            parent::_set_meta_desc("::: COGTIME Xtian network :::");
            parent::_set_meta_keywords("::: COGTIME Xtian network :::");
            parent::_add_js_arr( array('js/jquery.form.js'=>'header',
                                       
                                       'js/thickbox.js'=>'header',
									   'js/jquery/JSON/json2.js'=>'header',
									   'js/jquery-ui-1.8.2.custom.min.js'=>'header',) );
                                        
            parent::_add_css_arr( array('css/thickbox.css',
                                        'css/jquery-ui-1.8.2.custom.css') );
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 1;
			$data['submenu'] = 19;
			# rendering the view file...
			$data['mode']="edit";
		//Submitted Form
            if($_POST)
            {
				$posted=array();
                $posted["s_title"]= get_formatted_string(trim($this->input->post("txt_title")));
				$posted["dt_start_date"]= trim($this->input->post("date_to1"));
				$posted["dt_end_date"]= trim($this->input->post("date_to2"));
				$posted["s_url"]= prep_url(trim($this->input->post("txt_url")));
				$posted['p_loc'] = $this->input->post("page-loc");
                //uploading banner Image
                    $fileElementName = 'adv_image';    
                    $data["file_error_$fileElementName"] = ''; 

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
                                $data["file_error_$fileElementName"] ="<div class=\"error_massage\" id=\"err_msg\">supported extensions are ".implode(' , ',$this->config->item('VALID_IMAGE_EXT'))."</div>";
							}
							
							
							if($_FILES[$fileElementName]['size'] > $this->config->item('MAX_UP_FILE_SIZE')*1024*1024)
							{
                                $data["file_error_$fileElementName"] = "<div class=\"error_massage\" id=\"err_msg\">Maximum file upload size is ".$this->config->item('MAX_UP_FILE_SIZE')." MB.</div>";
							}
                            
                                    
                        //// check for uploaded banner file's dimension [Start]...    
                            list($uploaded_width, $uploaded_height) = getimagesize( $_FILES[$fileElementName]['tmp_name'] );
                           /* if( $uploaded_width>$this->config->item('hp_banner_width_limit') ||
                                $uploaded_height>$this->config->item('hp_banner_height_limit') ) {
                            
                                $data["file_error_$fileElementName"] = "<div class=\"error_massage\" id=\"err_msg\">Plz upload banner of preferred size.</div>";
                                
                            }*/
                        //// check for uploaded banner file's dimension [End]...    
										
					}
				//End uploading picture
				
				//validation starts here
					//pr($posted,1);
				//$this->form_validation->set_message('required', "Please provide"." %s");
				$this->form_validation->set_message('required', '* Required Field');		
				$this->form_validation->set_rules('txt_title', "* required " ,'trim|required');
				$this->form_validation->set_rules('txt_url', "* required " ,'trim|required|callback_valid_url');
				$this->form_validation->set_rules('date_to2', "* required", 'trim|required');
				$this->form_validation->set_rules('date_to2',"* required", 'trim|required');
				$this->form_validation->set_rules('page-loc', "* required " ,'trim|required');	
				//validation ends here
				if ($this->form_validation->run() == FALSE || $data["file_error_$fileElementName"] != '')
				{
				  
				  $info=$this->mod_banner->fetch_this_media_center($id);
				
					$posted["s_image"]= trim($info["s_image"]);
				 ////////Display the add form with posted values within it////
                    $data["posted"]=$posted;/*don't change*/
				}
				else
				{
				
				//adding to database
				$info=array();
					
					$info["s_title"]= get_formatted_string(trim($posted["s_title"]));
					//$info["s_desc"]= get_formatted_string(trim($posted["s_desc"]));
					$info["s_url"]= get_formatted_string(trim($posted["s_url"]));
					$info["dt_start_date"] =   get_db_dateformat($posted["dt_start_date"],'/');;
					$info["dt_end_date"]   =   get_db_dateformat($posted["dt_end_date"],'/');
					$info['dt_updated_on'] = get_db_datetime();
                                        $info['p_loc'] = $this->input->post("page-loc");
					//pr($info["s_desc"]);
					if( $_FILES['adv_image']['name'] != "")
					$info["s_image"] = $this->_upload_profile_image(trim($posted['adv_image']));
					//pr($info,1);				
                    $i_newid = $this->mod_banner->edit_info_media_center($info,$id);
					$re_page = admin_base_url() ."site_settings/media_center_advertisement";
					header("location:".$re_page);
					exit;
					
				}
				
			}
			else
			{
				$info=$this->mod_banner->fetch_this_media_center($id);
				
                $posted=array();
				$posted["id"]= trim($info["id"]);
				$posted["s_title"]= trim($info["s_title"]);
				$posted["s_desc"]= trim($info["s_desc"]);
				$posted["s_url"]= get_unformatted_string(trim($info["s_url"]));
				$posted["s_image"]= trim($info["s_image"]);
				$posted["dt_start_date"]= getShortDate($info["dt_start_date"],'3');
				$posted["dt_end_date"]= getShortDate($info["dt_end_date"],'3');
                                $posted['p_loc'] = trim($info["p_loc"]);//$this->input->post("");
				
			}
			
			 $data['posted']=$posted;
                        //pr($posted,1);
			 $VIEW_FILE = "admin/site_settings/media_center_advertisement/add-edit_advertisement.phtml";
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
		$id = $this->input->post('id');
                $this->db->where('id', $id);
$this->db->delete('cg_media_center_advertisement');
		
	} // end of Delete banner function...
	
	 public function _upload_profile_image($prev_img = '')
    {
      
	  // pr($_FILES);
	    $fileElementName = 'adv_image';	 
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
					$config['thumb_marker'] = '-main';
					$config['crop'] = false;
					$config['width'] = 300;
					$config['height'] = 300;
                    $config1['crop_from'] = 'middle';
                    #$config['within_rectangle'] = true;
                    $config['small_image_resize'] = 'inside';
					resize_exact($config);
					
					$config = array();

                
					
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
	
	
	
	  # function to load ajax-pagination [AJAX CALL]...
    public function ajax_pagination($page=0)
    {
        try
        {
			## seacrh conditions : filter ############
		 	
			 if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) :
           
            
			
				$WHERE_COND = " WHERE 1  ";
				
				$s_name = get_formatted_string(trim($this->input->post('txt_username')));
				$WHERE_COND .= ($s_name=='')?'':" AND (U.s_name LIKE '".$s_name."%' OR U.s_last_name  LIKE '".$s_name."%' )";
				
				$s_title = get_formatted_string(trim($this->input->post('txt_title')));
				$WHERE_COND .= ($s_title=='')?'':" AND (A.s_title LIKE '".$s_title."%')";
				
				if($this->input->post('date_posting') != ''){
					$dt_created_on = get_db_dateformat($this->input->post('date_posting'));
					$WHERE_COND .= ($dt_created_on=='')?'':" AND (DATE(A.dt_created_on) ='".$dt_created_on."' )";
				}
				
				if($this->input->post('date_pub_start') != ''){
					 $dt_start_date = get_db_dateformat($this->input->post('date_pub_start'));
					$WHERE_COND .= ($dt_start_date=='')?'':" AND (A.dt_start_date ='".$dt_start_date."' )";
				}
				
				if($this->input->post('date_pub_end') != ''){
					$date_pub_end = get_db_dateformat($this->input->post('date_pub_end'));
					$WHERE_COND .= ($date_pub_end=='')?'':" AND (A.dt_end_date ='".$date_pub_end."' )";
				}
				
				$this->session->set_userdata('search_condition',$WHERE_COND);
			
			
           endif;  
		   	
			$s_where = $this->session->userdata('search_condition');
			$order_by = " `id` DESC ";
		   	$result = $this->mod_banner->fetch_multi_media_center($s_where,$page,$this->pagination_per_page,$order_by);
            $resultCount = count($result);
			#echo $this->db->last_query(); exit;
			$total_rows = $this->mod_banner->gettotal_info_media_center($s_where);
			
			if( ( !is_array($result) || !count($result) ) && $total_rows ) {
                $page = $page - $this->pagination_per_page;
                
                $result = $this->mod_banner->fetch_multi_media_center($s_where, $page, $this->pagination_per_page,$ORDER_BY);
            }
			## end seacrh conditions : filter ############
			
			//pr($result,1);
			#Jquery Pagination Starts
           	$this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/site_settings/media_center_advertisement/ajax_pagination";
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
          echo  $this->load->view('admin/site_settings/media_center_advertisement/manage_advertisement_ajax.phtml', $data,TRUE);
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
				$this->mod_banner->change_adv_status_media($i_status,$ID);
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
            $this->form_validation->set_message('valid_url', "* Not a valid url");
		    return FALSE;
        }

        return TRUE;
    } 
    
    public function add_mediabanner()
    {
        //die('k');
		try
		{
			 # adjusting header & footer sections [Start]...
            $data = $this->data;
            parent::_set_title("::: COGTIME Xtian network :::");
            parent::_set_meta_desc("::: COGTIME Xtian network :::");
            parent::_set_meta_keywords("::: COGTIME Xtian network :::");
            parent::_add_js_arr( array() );
                                       //'js/backend/manage_hp_banner.js'=>'header') );
                                        
            parent::_add_css_arr( array() );
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 1;
			$data['submenu'] = 19;
			$data['mode']="add";
           
			
			//Submitted Form
            if($_POST)
            {
				$posted=array();
                $posted["s_title"]= get_formatted_string(trim($this->input->post("txt_title")));
				$posted["dt_start_date"]= trim($this->input->post("date_to1"));
				$posted["dt_end_date"]= trim($this->input->post("date_to2"));
				$posted["s_url"]= prep_url(trim($this->input->post("txt_url")));
                                //$posted['p_loc'] = $this->input->post("page-loc");
                               // $posted['p_sub_loc'] =
				
				//uploading banner Image
					$fileElementName = 'adv_image';	
					$data["file_error_$fileElementName"] = ''; 

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
										 $data["file_error_$fileElementName"] ="<div class=\"error_massage\" id=\"err_msg\">supported extensions are ".implode(' , ',$this->config->item('VALID_IMAGE_EXT'))."</div>";
									}
									
									
									if($_FILES[$fileElementName]['size'] > $this->config->item('MAX_UP_FILE_SIZE')*1024*1024)
									 {
									    $data["file_error_$fileElementName"] = "<div class=\"error_massage\" id=\"err_msg\">Maximum file upload size is ".$this->config->item('MAX_UP_FILE_SIZE')." MB.</div>";
									 }
								
									
                                    //// check for uploaded banner file's dimension [Start]...	
                                      /*  list($uploaded_width, $uploaded_height) = getimagesize( $_FILES[$fileElementName]['tmp_name'] );
                                        if( $uploaded_width>$this->config->item('hp_banner_width_limit') ||
                                            $uploaded_height>$this->config->item('hp_banner_height_limit') ) {
                                        
                                            $data["file_error_$fileElementName"] = "<div class=\"error_massage\" id=\"err_msg\">Plz upload banner of preferred size.</div>";
                                            
                                        }*/
                                    //// check for uploaded banner file's dimension [End]...    
							}else
							{
							
							  $data["file_error_$fileElementName"] = "<div class=\"error_massage\" id=\"err_msg\">* Required Field</div>";
							
							}
				
				//End uploading picture
				
				//validation starts here 
				#pr($posted,1);
					
				///$this->form_validation->set_message('required', "Please provide"." %s");
				$this->form_validation->set_message('required', '* Required Field');		
				$this->form_validation->set_rules('txt_title', "* required " ,'trim|required');
				$this->form_validation->set_rules('txt_url', "* required " ,'trim|required|callback_valid_url');
				$this->form_validation->set_rules('date_to2', "* required", 'trim|required');
				$this->form_validation->set_rules('date_to2',"* required", 'trim|required');
				//$this->form_validation->set_rules('page-loc', "* required " ,'trim|required');
				//$this->form_validation->set_rules('adv_image',"adv_image", 'trim|required|callback_chck_image_size');
			#echo "file_error_$fileElementName" ;echo $data["file_error_$fileElementName"]; exit;
				//validation ends here
				if ($this->form_validation->run() == FALSE || $data["file_error_$fileElementName"] != '')
				{
				  
				 
				   ////////Display the add form with posted values within it////
                    $data["posted"]=$posted;/*don't change*/
					
					
					
				}
				else
				{
				
				//adding to database
				$info=array();
				$info["s_title"]= get_formatted_string(trim($posted["s_title"]));
				$info["i_user_id"] = intval(decrypt($this->session->userdata('user_id')));
				$info["s_url"]= get_formatted_string(trim($posted["s_url"]));
				$info["s_image"] = $this->_upload_profile_image_media_banner(trim($posted['adv_image']));
				$info["dt_start_date"] = get_db_dateformat($posted["dt_start_date"],'/');
				$info["dt_end_date"]   =  get_db_dateformat($posted["dt_end_date"],'/');
				$info['dt_created_on'] = get_db_datetime();
				$info['p_loc'] = 'media-banner';
				//pr($info,1);
                $i_newid = $this->mod_banner->add_media_center_info($info);
				$re_page = admin_base_url() ."site_settings/media_center_advertisement";
				header("location:".$re_page);
				exit;
					
				}
				
			}
		 	// End Submitted Form
			 $VIEW_FILE = "admin/site_settings/media_center_advertisement/add-edit_mediabanner.phtml";
            parent::_render($data, $VIEW_FILE);
			
		}
		
		catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
		
	}
         public function _upload_profile_image_media_banner($prev_img = '')
    {
      
	  // pr($_FILES);
	    $fileElementName = 'adv_image';	 
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
					$config['thumb_marker'] = '-main';
					$config['crop'] = false;
					$config['width'] = 1000;
					$config['height'] = 300;
                    $config1['crop_from'] = 'middle';
                    #$config['within_rectangle'] = true;
                    $config['small_image_resize'] = 'inside';
					resize_exact($config);
					
					$config = array();

                
					
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
    
}   // end of controller...