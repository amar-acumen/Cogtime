<?php
/*********
* Author: 
* Purpose:
*  Controller For "Manage Home page Bannner"
* 
* @package 
* @subpackage 
* 
* @link InfController.php 
* @link Base_Controller.php
* @link model/manage_hp_banner_model.php
* @link views/##
*/

class Hp_banners extends Admin_base_Controller
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
            $this->upload_thumb_path = BASEPATH.'../uploads/homepage_banner/thumb/';
			$this->upload_path = BASEPATH.'../uploads/homepage_banner/';
            
            # loading reqired model & helpers...
            // $this->load->helper('###');
            $this->load->model("manage_hp_banner_model" , "mod_banner");
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
            parent::_add_js_arr( array('js/backend/manage_site_settings.js'=>'header', 'js/lightbox.js'=>'header' ) );
                                        
            parent::_add_css_arr( array() );
            # adjusting header & footer sections [End]...
			$data['top_menu_selected'] = 1;
			$data['submenu'] = 1;
         
            
			// fetching data
			$page=0;
			$order_by = " `id` ASC ";
			$data['info_arr'] = $this->mod_banner->fetch_multi($s_where,$page,$this->pagination_per_page, $order_by);
			$total_rows = $this->mod_banner->gettotal_info($WHERE_COND);
				
			#Jquery Pagination starts
			$this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/site_settings/manage_hp_banner/ajax_pagination";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->pagination_per_page;
            $config['uri_segment'] = 3;
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
			$data['current_page'] = $page;
				#Jquery pagination ends
                
            # for selected language...
            $data['selected_language'] = $selected_lang;
			$data['heading']="Manage Homepage Banners";
    
            # rendering the view file...
            $VIEW_FILE = "admin/site_settings/hp_banner/manage_hp_banner.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }


    
    
    # function to Add new banner
    public function add_information()
    {
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
			$data['submenu'] = 1;
			$data['mode']="add";
           
			
			//Submitted Form
            if($_POST)
            {
				$posted=array();
                $posted["s_title"]= get_formatted_string(trim($this->input->post("txt_banner_title")));
				$posted["s_desc"]= get_formatted_string(trim($this->input->post("txta_banner_desc")));
				$posted["s_url"]= prep_url(trim($this->input->post("txt_banner_url")));
				
				//uploading banner Image
					$fileElementName = 'banner_image';	
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
                                        if( $uploaded_width>$this->config->item('hp_banner_width_limit') ||
                                            $uploaded_height>$this->config->item('hp_banner_height_limit') ) {
                                        
                                            $data["file_error_$fileElementName"] = "<div class=\"error_massage\" id=\"err_msg\">* Incorrect image dimension.</div>";
                                            
                                        }
                                    //// check for uploaded banner file's dimension [End]...    
							}else
							{
							
							  $data["file_error_$fileElementName"] = "<div class=\"error_massage\" id=\"err_msg\">Please select a file</div>";
							
							}
				
				//End uploading picture
				
				//validation starts here
					
				///$this->form_validation->set_message('required', "Please provide"." %s");
				$this->form_validation->set_message('required', '* Required Field');		
				$this->form_validation->set_rules('txt_banner_title', "* required " ,'trim|required');
				$this->form_validation->set_rules('txta_banner_desc', "* required", 'trim|required');
				$this->form_validation->set_rules('txt_banner_url',"* required", 'trim|required|callback_valid_url');
				
				//$this->form_validation->set_rules('banner_image',"banner_image", 'trim|required|callback_chck_image_size');
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
				$info["s_desc"]= get_formatted_string(trim($posted["s_desc"]));
				$info["s_url"]= get_formatted_string(trim($posted["s_url"]));
				$info["s_image"] = $this->_upload_profile_image(trim($posted['banner_image']));
				//$info["i_order"]= 1+$this->mod_banner->get_i_order();
                $i_newid = $this->mod_banner->add_info($info);
				$re_page = admin_base_url() ."site_settings/hp-banners.html";
				header("location:".$re_page);
				exit;
					
				}
				
			}
		 	// End Submitted Form
			 $VIEW_FILE = "admin/site_settings/hp_banner/add-edit_hp_banner.phtml";
            parent::_render($data, $VIEW_FILE);
			
		}
		
		catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
		
	}   // end of Add new banner function...
    
    
    
    
    //// function to Edit the Existing banner
    public function edit_information($id)
    {
		try
		{
			 # adjusting header & footer sections [Start]...
            $data = $this->data;
            parent::_set_title("::: COGTIME Xtian network :::");
            parent::_set_meta_desc("::: COGTIME Xtian network :::");
            parent::_set_meta_keywords("::: COGTIME Xtian network :::");
            parent::_add_js_arr( array() );
                                        
            parent::_add_css_arr( array() );
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 1;
			$data['submenu'] = 1;
			# rendering the view file...
			$data['mode']="edit";
		//Submitted Form
            if($_POST)
            {
				$posted=array();
                $posted["s_title"]= get_formatted_string(trim($this->input->post("txt_banner_title")));
				$posted["s_desc"]= get_formatted_string(trim($this->input->post("txta_banner_desc")));
				$posted["s_url"]= prep_url(trim($this->input->post("txt_banner_url")));
				
				
                //uploading banner Image
                    $fileElementName = 'banner_image';    
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
							//echo	$uploaded_width.' '.$uploaded_height    ; exit;
                            if( $uploaded_width > $this->config->item('hp_banner_width_limit') ||
                                $uploaded_height > $this->config->item('hp_banner_height_limit') || $uploaded_width < $this->config->item('hp_banner_width_limit') ||
                                $uploaded_height < $this->config->item('hp_banner_height_limit') ) {
                            
                                $data["file_error_$fileElementName"] = "<div class=\"error_massage\" id=\"err_msg\">* Incorrect image dimension.</div>";
                                
                            }
                        //// check for uploaded banner file's dimension [End]...    
										
					}
				//End uploading picture
				
				//validation starts here
					//pr($posted,1);
				//$this->form_validation->set_message('required', "Please provide"." %s");
				$this->form_validation->set_message('required', '* Required Field');	
				$this->form_validation->set_rules('txt_banner_title', "* Required" ,'trim|required');
				$this->form_validation->set_rules('txta_banner_desc', "* Required", 'trim|required');
				$this->form_validation->set_rules('txt_banner_url',"* Required", 'trim|required|callback_valid_url');
					
				//validation ends here
				if ($this->form_validation->run() == FALSE || $data["file_error_$fileElementName"] != '')
				{
				  
				 ////////Display the add form with posted values within it////
				 
				 	$info=$this->mod_banner->fetch_this($id);
				
					$posted["s_image"]= trim($info["s_image"]);
                    $data["posted"]=$posted;/*don't change*/ //pr($data["posted"]);
				}
				else
				{
				
				//adding to database
				$info=array();
					$info["s_title"]= trim($posted["s_title"]);
					$info["s_desc"]= trim($posted["s_desc"]);
					$info["s_url"]= trim($posted["s_url"]);
					
					//pr($info["s_desc"]);
					if( $_FILES['banner_image']['name'] != "")
					$info["s_image"] = $this->_upload_profile_image(trim($posted['banner_image']));				
                    $i_newid = $this->mod_banner->edit_info($info,$id);
					$re_page = admin_base_url() ."site_settings/hp-banners.html";
					header("location:".$re_page);
					exit;
					
				}
				
			}
			else
			{
				$info=$this->mod_banner->fetch_this($id);
				
                $posted=array();
				$posted["id"]= trim($info["id"]);
				$posted["s_title"]= trim($info["s_title"]);
				$posted["s_desc"]= trim($info["s_desc"]);
				$posted["s_url"]= get_unformatted_string(trim($info["s_url"]));
				$posted["s_image"]= trim($info["s_image"]);
				
			}
			
			 $data['posted']=$posted;
			 $VIEW_FILE = "admin/site_settings/hp_banner/add-edit_hp_banner.phtml";
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
		$i_ret=$this->mod_banner->delete_info($id);
		$re_page = admin_base_url() ."site_settings/hp-banners.html";
					header("location:".$re_page);
					exit;
		
		
		
		
	} // end of Delete banner function...
	
	 public function _upload_profile_image($prev_img = '')
    {
      
	   #pr($_FILES);
	    $fileElementName = 'banner_image';	 
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
					$config['width'] = 400;
					$config['height'] = 100;
					$config1['crop_from'] = 'middle';
                    #$config['within_rectangle'] = true;
                    $config['small_image_resize'] = 'no_resize';
					resize_exact($config);
                    
					unset($config);
					
					$config = array();

					$config['source_image'] = $this->upload_image;
					$config['thumb_marker'] = '-main';
					$config['crop'] = false;
					$config['width'] = 1900;
					$config['height'] = 521;
                    $config1['crop_from'] = 'middle';
                    #$config['within_rectangle'] = true;
                    $config['small_image_resize'] = 'inside';
					resize_exact($config);
					
					$config = array();

					$config['source_image'] = $this->upload_image;
					$config['thumb_marker'] = '-mini_thumb';
					$config['crop'] = false;
					$config['width'] = 110;
					$config['height'] = 60;
                    $config1['crop_from'] = 'middle';
                    //$config['within_rectangle'] = true;
                    $config['small_image_resize'] = 'no_resize';
					resize_exact($config);
					
					/* $config['source_image'] = $this->upload_image;
					  $config['thumb_marker'] = '-mini_thumb';
					  #$config['crop'] = true;
					  $config['crop'] = false;
					  $config['width'] = 110;
					  $config['height'] = 60;
					  $config1['crop_from'] = 'middle';
					  $config['within_rectangle'] =true;
					  $config['small_image_resize'] = 'no_resize';
					  resize_exact($config);
					  unset($config);*/
                    
                    /*$config['source_image'] = $this->upload_image;
                    $config['thumb_marker'] = '-thumb';
                    $config['crop'] = true;
                    $config['width'] = 400;
                    $config['height'] = 100;
                    $config['master_dim'] = getSmallerDimension($this->upload_image, 400, 100);
                    $config['small_image_resize'] = 'no_resize';
                    resize_exact($config);
                    unset($config);
                    
                    $config = array();

                    $config['source_image'] = $this->upload_image;
                    $config['thumb_marker'] = '-main';
                    $config['crop'] = true;
                    $config['width'] = 1002;
                    $config['height'] = 365;
                    $config['master_dim'] = getSmallerDimension($this->upload_image, 1002, 365);
                    $config['small_image_resize'] = 'no_resize';
                    resize_exact($config);*/
					
					$this->s_picture_path = $new_imagename.'.'.$ext;
					
					@unlink($this->upload_image); //Unlink the original image........
					//@unlink($this->upload_path.getThumbName($prev_img,'thumb')); //Unlink the prevoius image........
					
					return $this->s_picture_path;

				
			
					
				}
        else
        {
            return $prev_img; // Unchaged previous image
        }
        
        
    }
	
	//ordering in ajax
	 function maintain_displayorder_ajax($selected_language, $page=0)
    {
        //sleep(2);

        $actionID = $this->input->post('rid');
        $status = $this->input->post('status');
        
        
        # retrieving  info...
        $info_arr = $this->mod_banner->fetch_this($actionID);

        $this->load->model("utility_model");
		$tbl=$this->db->HOMEPAGE_BANNER;

      //  $WHERE_COND_BEGIN = " `i_parent_id` = {$PARENT_CATEGORY_ID} AND `i_is_active` = 1 ";
        $this->utility_model->Ranking($status, $actionID, $WHERE_COND_BEGIN,$tbl);
		
            $order_by = " `id` DESC ";
			$result = $this->mod_banner->fetch_multi($s_where,$page,$this->pagination_per_page, $order_by);
            $resultCount = count($result);
            //$total_rows = $this->mail_domains_model->gettotal_info($WHERE_COND);
			$total_rows = $this->mod_banner->gettotal_info($WHERE_COND);
			#Jquery Pagination Starts
           	$this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/site_settings/manage_hp_banner/ajax_pagination";
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
			$data['result_arr'] = $result;
			$data['no_of_result'] = $total_rows;
			$data['current_page'] = $page;
				#Jquery pagination ends
		
		# loading the view-part...
          echo  $this->load->view('admin/site_settings/manage_hp_banner_ajax.phtml', $data, TRUE);
		
	}
	
	  # function to load ajax-pagination [AJAX CALL]...
    public function ajax_pagination($page=0)
    {
         try
        {
			
		   	$order_by = " `i_order` ASC ";
		   	$result = $this->mod_banner->fetch_multi($s_where,$page,$this->pagination_per_page,$order_by);
            $resultCount = count($result);
			$total_rows = $this->mod_banner->gettotal_info($WHERE_COND);
			#Jquery Pagination Starts
           	$this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/site_settings/hp_banners/ajax_pagination";
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
			$data['result_arr'] = $result;
			$data['no_of_result'] = $total_rows;
			$data['current_page'] = $page;

    
            $data['current_page'] = $page;
    
          
			# loading the view-part...
          echo  $this->load->view('admin/site_settings/hp_banner/manage_hp_banner_ajax.phtml', $data,TRUE);
			


		 }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
    }
	public function valid_url($url)
    {
       
        if (!isValidURL($url) && $url!='')
        { 
            $this->form_validation->set_message('valid_url', "* Not a valid URL");
		    return FALSE;
        }

        return TRUE;
    } 
    
}   // end of controller...