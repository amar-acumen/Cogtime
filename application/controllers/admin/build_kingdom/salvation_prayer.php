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

class Salvation_prayer extends Admin_base_Controller
{
   

    

    // constructor definition...
    function __construct()
    {
        try
        {
            parent::__construct();
            parent::_check_admin_login();
            
            # configuring paths...
             $this->upload_path = BASEPATH.'../uploads/salvation_prayer_photos/';           
            # loading reqired model & helpers...
            // $this->load->helper('###');
           //$this->load->model("ring_categories_model");
           $this->load->model("salvation_model");
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
            parent::_add_js_arr( array( 'js/jquery.form.js',
                                        'js/jquery/JSON/json2.js',
										'tiny_mce/plugins/tinybrowser/tb_tinymce.js.php',
										 'tiny_mce/tiny_mce.js',
										 'js/backend/build_kingdom/salvation_prayer.js'
                                        ) );
                                        
             parent::_add_css_arr( array() );
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 6;
            $data['submenu'] = 4;
            
           
            $data['res'] = $this->salvation_model->get_by_id(1);
			
			$data['photo_arr'] = $this->salvation_model->get_photo_by_salvation_id(1);
            #pr($data['photo_arr']);
            
            # rendering the view file...
            $VIEW_FILE = "admin/build_kingdom/salvation_prayer.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }
    
   
	
	public function edit_info($id=1)
	{
		try
		{
		  $arr_messages = array();
				
			if($_POST){
				  # error message trapping...
				  
				   if( trim($this->input->post('ta_welcome_msg'))=='') 
				  {
						  $arr_messages['welcome_msg'] = "* Required Field";
				  }
				  
				  if( trim($this->input->post('ta_mean_of_member'))=='') 
				  {
						  $arr_messages['mean_of_member'] = "* Required Field";
				  }
				  
				  if( trim($this->input->post('ta_to_become_member'))=='') 
				  {
						  $arr_messages['to_become_member'] = "* Required Field";
				  }
				  
				  if( trim($this->input->post('ta_salvation_prayer'))=='') 
				  {
						  $arr_messages['salvation_prayer'] = "* Required Field";
				  }
				 
				  if( trim($this->input->post('ta_what_next'))=='') 
				  {
						  $arr_messages['what_next'] = "* Required Field";
				  }
				  
				
				  //uploading Image
				  ## get photo from databse.
				  
				  $photo_arr = $this->salvation_model->get_photo_by_salvation_id(1);
				  
				  if(count($photo_arr)){
					  
					  foreach($photo_arr as $p_val){
					  
						   for($i=0 ; $i < count($_FILES['s_edit_photo_'.$p_val['id']]['name']); $i++){
								 
								 if( isset($_FILES['s_edit_photo_'.$p_val['id']]['name']) && $_FILES['s_edit_photo_'.$p_val['id']]['name']!='') {
									 
									preg_match('/(^.*)\.([^\.]*)$/', $_FILES['s_edit_photo_'.$p_val['id']]['name'], $matches);
									$ext = "";
									if(count($matches)>0) {
										$ext = $matches[2];
										$original_name = $matches[1];
									}
									else
										$original_name = 'photo';
					
								
									if( !in_array($ext , $this->config->item('VALID_IMAGE_EXT'))) 
									{
										 $arr_messages['photo'] ="supported extensions are ".implode(' , ',$this->config->item('VALID_IMAGE_EXT'));
									}
									else if($_FILES['s_edit_photo_'.$p_val['id']]['size'] > $this->config->item('MAX_UP_FILE_SIZE')*1024*1024)
									 {
										$arr_messages["photo"] = "Maximum file upload size is ".$this->config->item('MAX_UP_FILE_SIZE')." MB.";
									 }		
								}
						   }
					  }
				  }
				  
				  
				  
				   for($i=0 ; $i < count($_FILES['s_photo']['name']); $i++){
						 if( isset($_FILES['s_photo']['name'][$i]) && $_FILES['s_photo']['name'][$i]!='') {
							preg_match('/(^.*)\.([^\.]*)$/', $_FILES['s_photo']['name'][$i], $matches);
							$ext = "";
							if(count($matches)>0) {
								$ext = $matches[2];
								$original_name = $matches[1];
							}
							else
								$original_name = 'photo';
			
						
							if ( !in_array($ext , $this->config->item('VALID_IMAGE_EXT'))) 
							{
								 $arr_messages['photo'] ="supported extensions are ".implode(' , ',$this->config->item('VALID_IMAGE_EXT'));
							}
							else if($_FILES['s_photo']['size'][$i] > $this->config->item('MAX_UP_FILE_SIZE')*1024*1024)
							 {
								$arr_messages["photo"] = "Maximum file upload size is ".$this->config->item('MAX_UP_FILE_SIZE')." MB.";
							 }		
						}
				   }
				//End uploading picture
				 
				  
				  if( count($arr_messages)==0 ) {
						  
					  $info = array();
					  				 
					  $info['s_welcome_msg'] = get_formatted_string($this->input->post('ta_welcome_msg'));  
					  $info['s_mean_of_member'] = get_formatted_string($this->input->post('ta_mean_of_member'));	 
					  $info['s_to_become_member'] = get_formatted_string($this->input->post('ta_to_become_member')); 
					  $info['s_salvation_prayer'] = get_formatted_string($this->input->post('ta_salvation_prayer')); 
					  $info['s_what_next'] = get_formatted_string($this->input->post('ta_what_next')); 
	  				  $info['dt_updated_on'] = get_db_datetime();
					  
					  $_ret = $this->salvation_model->update($info ,1);
					  
					  $db_photo_id = $this->input->post('db_photo_id');
					  ## uploading pics ###
					  //pr($db_photo_id);
					 // pr($_FILES['s_photo'],1);
					  ## for new pics
					  for($i=0 ; $i < count($_FILES['s_photo']['name']); $i++){
					  		
							$photo_arr = array();
							$photo_arr['i_salavation_id'] = 1;
							$photo_arr['s_photo_name'] = $this->_upload_photo($i);
							$photo_arr['dt_created_on'] = get_db_datetime();

							$this->salvation_model->insert_photo($photo_arr);
					  }
					  
					  ### for existing pics
					  if(count($photo_arr)){
					  
					 	foreach($photo_arr as $p_val){
					  		
							if($_FILES['s_edit_photo_'.$p_val['id']]['name'] != ''){
								
								$photo_arr = array();
								
								$photo_arr['i_salavation_id'] = 1;
								$photo_arr['s_photo_name'] = $this->_upload_photo(0, 's_edit_photo_'.$p_val['id']);
								$this->salvation_model->update_photo($photo_arr, $p_val['id']);
							}
						}
					  }
					  
					  // pr($_FILES['s_photo'],1);
					  ### uploading pics ###
					  
										  
					  echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'Content updated Successfully.') );
				  }
				  else
				  {
					  echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'Error!' ));
				  }
		
			}
			
		}
		catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
	}
    
	
	 public function _upload_photo($i, $fileElementName = 's_photo')
     {
      	if($fileElementName == 's_photo'){
			$uploaded_file_tmp_name = $_FILES[$fileElementName]['tmp_name'][$i];
			$uploaded_file_name = $_FILES[$fileElementName]['name'][$i];
		}else{
			$uploaded_file_tmp_name = $_FILES[$fileElementName]['tmp_name'];
			$uploaded_file_name = $_FILES[$fileElementName]['name'];
		}
		
        if(!empty($uploaded_file_tmp_name) || $uploaded_file_tmp_name != '') 
		{
				preg_match('/(^.*)\.([^\.]*)$/', $uploaded_file_name, $matches);
				$ext = "";
				if(count($matches)>0) {
					$ext = strtolower($matches[2]);
					$original_name = $matches[1]; 
				}
				else
					$original_name = 'image';

			
					$imagename = createImageName($original_name);

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
					
					@move_uploaded_file($uploaded_file_tmp_name, $this->upload_image);
					                   
                    
                    # @@@@@@@@@@@@ NEW RESIZING PART [BEGIN] @@@@@@@@@@@
                        $config = array();
                        $config['source_image'] = $this->upload_image;
                        $config['thumb_marker'] = '-thumb';
                        $config['crop'] = false;
                        $config['width'] = 122;
                        $config['height'] = 82;
                        $config1['crop_from'] = 'middle';
                        $config['within_rectangle'] = true;
                        $config['small_image_resize'] = 'no_resize';
                        resize_exact($config);
                        unset($config);
						
											
                        
                        $config = array();
                        $config['source_image'] = $this->upload_image;
                        $config['thumb_marker'] = '-main';
                        #$config['crop'] = false;
                        $config['width'] = 385;
                        $config['height'] = 173;
                        //$config['within_rectangle'] =true;
                        $config['small_image_resize'] = 'no_resize';
                        resize_exact($config);
                        unset($config);
						
						
                       # @@@@@@@@@@@@ NEW RESIZING PART [END] @@@@@@@@@@@
					
					$this->s_picture_path = $new_imagename.'.'.$ext;
					
					@unlink($this->upload_image); //Unlink the original image........
					
					return $this->s_picture_path;
					
				}
        else
        {
            return $prev_img; // Unchanged previous image
        }
        
        
    }
	
	public function delete_photo($photo_id){
		$this->salvation_model->delete_photo_by_id($photo_id);
		echo json_encode(array('success'=>true));
		exit;
	}
    
}// end of controller