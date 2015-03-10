<?php
/*********
* Author: 
* Date  : 
* Modified By: 
* Modified Date:
* 
* Purpose:
* Controller For WALL
* 
* 
*/
include(APPPATH.'controllers/base_controller.php');

class Organize_photo extends Base_controller
{
    private $pagination_per_page = 10;
    
    public function __construct()
     {
        try
        {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
			$this->upload_path = BASEPATH.'../uploads/user_photos/';
			$this->upload_tmp_path = BASEPATH.'../uploads/_tmp/';
		    # loading reqired model & helpers...
            $this->load->model('users_model');
			$this->load->model('user_photos_model');
			$this->load->model('photo_albums_model');
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    public function index($i_album_id) 
    {
        try
        {                   
            $posted=array();
            $data = $this->data;      
            $this->data["MAIN_MENU_SELECTED"] = 1;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( 
										/*'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',*/
                                        
										'js/thickbox.js',
//										'js/stepcarousel.js',
										'js/production/tweet_utilities.js',
										'js/production/organize_photo.js',
										
										'uploadify/swfobject.js',
                                        'uploadify/jquery.uploadify.js'
                                        ));
                                        
            parent::_add_css_arr( array(/*'css/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css',*/'css/thickbox.css','uploadify/uploadify.css') );
										  
			############################################################
			$i_user_id = intval(decrypt($this->session->userdata('user_id')));
			$data['page_view_type'] = 'myaccount'; 
			$arr_profile_info = $this->users_model->fetch_this($i_user_id);
			parent::_set_all_photo_album_data($i_user_id);
			
			#### FETCHING PHOTOS PER USER 
			if(is_array($arr_profile_info) && !empty($arr_profile_info)){
				$data['arr_profile_info'] = $arr_profile_info;
				
				$s_where = " AND `i_user_id` = {$i_user_id}";
				$data['arr_albums'] = $this->photo_albums_model->get_by_album_details_id($i_album_id, $s_where, 0,1);
				$data['arr_albums_photos'] = $this->photo_albums_model->get_photos_by_album_id($i_album_id, $i_user_id, 0 ,$this->pagination_per_page);
				
				
				###fetching all photo to show in slideshow
				
				###############################################################################
				$data['current_album_id'] = $i_album_id;
				$this->session->set_userdata('current_album_id',$i_album_id);
		
                $data['pagination_per_page'] = $this->pagination_per_page;
                
                ob_start();
                $this->album_wise_photo_ajax_pagination($i_album_id,$data['page_view_type']);
                $content = ob_get_contents();
                $content_obj = json_decode($content);
                $data['result_album_content'] = $content_obj->html; 
                $data['total'] = $content_obj->total;
                ob_end_clean();
				
			}
			
            # view file...
            $VIEW = "logged/photos/organize_photo.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }   
	
	
	public function album_wise_photo_ajax_pagination($i_album_id,$page_view_type='public',$page=0) 
	{

	   try
	   {
           $current_page = $page + $this->pagination_per_page; 
		   $i_user_id = intval(decrypt($this->session->userdata('user_id')));
		  parent::_set_all_photo_album_data($i_user_id);

		  $data = $this->data;    
		  
		  $data['page_view_type'] = $page_view_type;	
		     
		  $result		= $this->photo_albums_model->get_photos_by_album_id($i_album_id,
		  			 		$i_user_id, intval($page), $this->pagination_per_page);
							
		//pr($result);
		  $total_rows 	= $this->photo_albums_model->get_total_by_album_id($i_album_id);
		  
          //--- for check whether more videos are there or not
            $view_more = true;
             $rest_counter = $total_rows-$page;
             if($rest_counter<=$this->pagination_per_page)
                $view_more = false;
             
             
             //--------- end check
    
    
    
    	  $data['arr_albums_photos'] = $result;
		  $data['no_of_result'] = $total_rows;
		  $data['current_page'] = $current_page;
		  
		  $data['total_pages'] = ceil($total_rows/$this->pagination_per_page);
		  
		  $p = ($page/$this->pagination_per_page);
		  $data['current_loaded_page_no'] =  $p + 1;
		  $VIEW_FILE = "logged/photos/blocks/load_album_wise_photo_listing_ajax.phtml";
		  //$this->load->view( $VIEW_FILE , $data);
          
          if( is_array($result) && count($result) ) {
            $content = $this->load->view( $VIEW_FILE , $data, true);
            }
            else {
                #$content = '<div class="txt_content01"><p style="margin-left: 330px;">No Posts!</p></div>';
                $content = '';
            }
            echo json_encode( array('html'=>$content, 'current_page'=>$current_page , 'total'=>$total_rows,'view_more'=>$view_more) );
          
          
	  } 
	  catch(Exception $err_obj)
	  {
		  
	  } 
  
  } 
   ### function to zoom photo
   
	public function zoom_photo($id)
	{ 
		$data = $this->data;
		$data['photo_info'] = $this->user_photos_model->get_by_id($id);
		$this->load->view('logged/photos/blocks/zoom_photo_popup.phtml', $data);
	}
		
	//ordering in ajax
   function maintain_displayorder_ajax($page=0)
	{
		//sleep(2);
		$i_user_id = intval(decrypt($this->session->userdata('user_id')));
		parent::_set_all_photo_album_data($i_user_id);
		$data = $this->data;
		$actionID = $this->input->post('rid'); 
		$i_album_id = $this->input->post('i_album_id');
		$status = $this->input->post('status');
		$i_user_id = intval($i_user_id)<=0?intval(decrypt($this->session->userdata('user_id'))):intval($i_user_id);
 	  	$data['page_view_type'] = $page_view_type;
		
		# retrieving  info...
		//$arr_albums_photos = $this->photo_albums_model->get_photos_by_album_id($actionID,$i_user_id);
	
		$this->load->model("utility_model");
		$tbl=$this->db->USER_PHOTOS;
	
	 	
		$WHERE_COND_BEGIN = " AND i_photo_album_id = {$i_album_id}";	
		$this->utility_model->Ranking($status, $actionID, $WHERE_COND_BEGIN,$tbl);
		$order_by = " `i_order` DESC ";
		
		$result		= $this->photo_albums_model->get_photos_by_album_id($i_album_id,$i_user_id, 
																		intval($page), $this->pagination_per_page);
		//echo $this->db->last_query();
		//pr($result);
							
		$total_rows 	= $this->photo_albums_model->get_total_by_album_id($i_album_id);
		$resultCount = count($result);
		
		
		        ob_start();
                $this->album_wise_photo_ajax_pagination($i_album_id,'public');
                $content = ob_get_contents();
                $content_obj = json_decode($content);
                $html = $content_obj->html; 
                $total_rows = $content_obj->total;
				$view_more = $content_obj->view_more;
				$current_page = $content_obj->current_page;
                ob_end_clean();
		
	

	  # loading the view-part...
		echo json_encode( array('html'=>$html, 'current_page'=>$current_page , 'total'=>$total_rows,'view_more'=>$view_more) );
		//$this->load->view('logged/photos/blocks/load_album_wise_photo_listing_ajax.phtml', $data, TRUE);
			
	}
	
	function change_photo_album(){
		
		$current_page = $this->input->post('current_page');
		$current_album_id =  $this->input->post('curr_album_id');
		
		$info['i_photo_album_id'] = $this->input->post('album_id');
		# update i_order
		if($current_album_id != $info['i_photo_album_id']){
			$info["i_order"] =  1+$this->photo_albums_model->get_i_order($info['i_photo_album_id']);
		}
		$photo_id = $this->input->post('record_id');
		
		$this->user_photos_model->update($info, $photo_id);
		
		$i_user_id = intval(decrypt($this->session->userdata('user_id')));
		parent::_set_all_photo_album_data($i_user_id);
		/*ob_start();
		$this->album_wise_photo_ajax_pagination($current_album_id, $data['page_view_type'], $current_page);
		$result_album_content = ob_get_contents();
		ob_end_clean(); */
		
		ob_start();
		$this->album_wise_photo_ajax_pagination($current_album_id,'public');
		$content = ob_get_contents();
		$content_obj = json_decode($content);
		$html = $content_obj->html; 
		$total_rows = $content_obj->total;
		$view_more = $content_obj->view_more;
		$current_page = $content_obj->current_page;
		ob_end_clean();
		
		echo json_encode( array('sucess'=>TRUE, 'html'=>$html, 'current_page'=>$current_page , 'total'=>$total_rows,'view_more'=>$view_more, 'msg'=>'Photo successfully moved.') );
		
	}
	
	
	
	
	public function add_photo_ajax()
	{
		try
		{
			
			parent::check_login(TRUE, '', array('1'));
			$arr_messages = array();
			# error message trapping...
			if( trim($this->input->post('txt_title'))=='') 
			{
					$arr_messages['title'] = "* Required Field.";
			}
			
			if( $_FILES['s_photo']['name']=='' ) {
				 $arr_messages['photo'] = "* Required Field.";
			}
			
			if( trim($this->input->post('select_album1'))=='-1') 
			{
					$arr_messages['album1'] = "* Required Field.";
			}
			
			if(trim($this->input->post('select_album1')) == 'new_album' && trim($this->input->post('txt_album_name')) == ''){
				
				$arr_messages['album_name'] = "* Required Field.";
			}
			
			if( isset($_FILES['s_photo']['name']) && $_FILES['s_photo']['name']!='') {
				preg_match('/(^.*)\.([^\.]*)$/', $_FILES['s_photo']['name'], $matches);
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
				else if($_FILES['s_photo']['size'] > $this->config->item('MAX_UP_FILE_SIZE')*1024*1024)
				 {
					$arr_messages["photo"] = "Maximum file upload size is ".$this->config->item('MAX_UP_FILE_SIZE')." MB.";
				 }		
			}

		   //pr($arr_messages);
			if( count($arr_messages)==0 ) {
					
				$info = array();
				
				if($this->input->post('txt_album_name') != '' && $this->input->post('select_album1') == 'new_album' )
				{
					$album_info = array();
					$album_info['i_user_id'] = intval(decrypt($this->session->userdata('user_id')));	 
					$album_info['s_name'] = get_formatted_string($this->input->post('txt_album_name')); 
					$album_info['dt_created_on'] = get_db_datetime();
					$album_ret = $this->photo_albums_model->insert($album_info);
					
					## storing new album id
					$info['i_photo_album_id'] = $album_ret ;	 

				}
				else{
					
					$info['i_photo_album_id'] = intval($this->input->post('select_album1'));
				}
				
				$info['i_user_id'] = intval(decrypt($this->session->userdata('user_id')));
				$info['s_title'] = get_formatted_string($this->input->post('txt_title')); 
				$info['s_description'] = get_formatted_string($this->input->post('txt_desc')); 
				$info['s_photo'] 	= $this->_upload_photo();	
				$info["i_order"] =  1+$this->photo_albums_model->get_i_order($info['i_photo_album_id']);		
				$info['dt_created_on'] = get_db_datetime();
				$_ret = $this->user_photos_model->insert($info);
				$i_total = $this->user_photos_model->get_total_by_user_id($info['i_user_id']);
				
				
				$current_album_id = $this->session->userdata('current_album_id');
				ob_start();
                $this->album_wise_photo_ajax_pagination($current_album_id,$data['page_view_type']);
                $content = ob_get_contents();
                $content_obj = json_decode($content);
                $html = base64_encode($content_obj->html); 
                $data['total'] = $content_obj->total;
                ob_end_clean();
				
					
				echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'html'=>$html,'msg'=>'Photo Uploaded Successfully.'));
			}
			else
			{
				echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>t('Error!')) );
			}
		
		
		
		}
		catch(Exception $err_obj)
        {
            
        } 
	}
	
	
	
	public function add_multi_photo()
	{
		try
		{
			
			parent::check_login(TRUE,'',array('1')); // put this code on those pages which are not accessable by non logged in user
			$arr_messages = array();
			# error message trapping...
		    $flash_uploaded_pic = array();
			$flash_uploaded_pic = $this->input->post('flash_uploaded_file');
			
						
			if(!is_array($flash_uploaded_pic)) {
				 $arr_messages['mphoto'] = "* Required Field."; 
			}
			
			if( trim($this->input->post('select_malbum1'))=='-1') 
			{
					$arr_messages['malbum1'] = "* Required Field.";
			}
			
			if(trim($this->input->post('select_malbum1')) == 'new_album' && trim($this->input->post('txt_malbum_name')) == ''){
				
				$arr_messages['malbum_name'] = "* Required Field.";
			}
			
			if(count($flash_uploaded_pic) > 0) {
					  
				 foreach($flash_uploaded_pic as $val){	

				 		
					  $tmp_file_  = BASEPATH.'../uploads/_tmp/'.$val; // getting size of file.
					   $flash_uploaded_pic_size = round((filesize($tmp_file_)), 2);				 
				   
					  preg_match('/(^.*)\.([^\.]*)$/', $val, $matches);
					  $ext = "";
					  if(count($matches)>0) {
						  $ext = $matches[2];
						  $original_name = $matches[1];
					  }
					  else
						  $original_name = 'photo';
	  
				  
					  if ( !in_array($ext , $this->config->item('VALID_IMAGE_EXT'))) 
					  {
						   $arr_messages['mphoto'] = "supported extensions are "
						   							 .implode(' , ',$this->config->item('VALID_IMAGE_EXT'));
					  }
					  else if($flash_uploaded_pic_size > $this->config->item('MAX_UP_FILE_SIZE')*1024*1024)
					   {
						  $arr_messages["mphoto"] = "Maximum file upload size is "
						  							.$this->config->item('MAX_UP_FILE_SIZE')." MB.";
					   }	
					   
				 }
			}

		   //pr($arr_messages);
			if( count($arr_messages)==0 ) {
					
				$info = array();
				
				if(count($flash_uploaded_pic) > 0)
				{
					$arr_photo_data = $this->move_temp_images_AJAX($flash_uploaded_pic); 
					//pr($arr_photo_data); exit;
				}
				
				if($this->input->post('txt_malbum_name') != '' && $this->input->post('select_malbum1') == 'new_album' )
				{
					$album_info = array();
					$album_info['i_user_id'] = intval(decrypt($this->session->userdata('user_id')));	 
					$album_info['s_name'] = get_formatted_string($this->input->post('txt_malbum_name')); 
					$album_info['dt_created_on'] = get_db_datetime();
					$album_ret = $this->photo_albums_model->insert($album_info);
					
					## storing new album id
					$i_photo_album_id = $album_ret ;	 

				}
				else{
					
					$i_photo_album_id = intval($this->input->post('select_malbum1'));
				}
				
				if(count($flash_uploaded_pic) > 0){
					for($i=0 ; $i < count($arr_photo_data) ; $i++)
					{
						$info['i_user_id'] = intval(decrypt($this->session->userdata('user_id')));
						$title_arr = explode('.',$arr_photo_data[$i]); #pr($title_arr);
						$info['s_title'] = $title_arr[0]; /// assigning image name as title
						$info['s_description'] = ''; 
						$info['i_photo_album_id'] = $i_photo_album_id;
						$info['s_photo'] = $arr_photo_data[$i];
						$info["i_order"] =  1+$this->photo_albums_model->get_i_order($i_photo_album_id);	
						$info['dt_created_on'] = get_db_datetime();
						$_ret = $this->user_photos_model->insert($info); #echo $this->db->last_query();
						$i_total = $this->user_photos_model->get_total_by_user_id($info['i_user_id']);
				     }
				}
				$current_album_id = $this->session->userdata('current_album_id');
				ob_start();
                $this->album_wise_photo_ajax_pagination($current_album_id,$data['page_view_type']);
                $content = ob_get_contents();
                $content_obj = json_decode($content);
                $html = $content_obj->html; 
                $data['total'] = $content_obj->total;
                ob_end_clean();
				
					
				echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'html'=>$html,'msg'=>'Photo Uploaded Successfully.'));
			}
			else
			{
				echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>t('Error!'),) );
			}
		
		
		
		}
		catch(Exception $err_obj)
        {
            
        } 
	}
	
	
	
	
	
     public function _upload_photo($prev_img = '')
     {
      	parent::check_login(TRUE,'',array('1')); // put this code on those pages which are not accessable by non logged in user
	   #pr($_FILES);
	    $fileElementName = 's_photo';	 
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
					

					@move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $this->upload_image);
					                   
                    
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
						$config['thumb_marker'] = '-mini';
						$config['crop'] = false;
						$config['width'] = 73;
						$config['height'] = 73;
						$config1['crop_from'] = 'middle';
						$config['within_rectangle'] = true;
						$config['small_image_resize'] = 'no_resize';
						resize_exact($config);
						unset($config);
						
						$config = array();
						$config['source_image'] = $this->upload_image;
						$config['thumb_marker'] = '-mid';
						#$config['crop'] = false;
						$config['width'] = 472;
						$config['height'] = 378;
						$config['within_rectangle'] =true;
						$config['small_image_resize'] = 'no_resize';
						resize_exact($config);
						unset($config);
                        
                        $config = array();
                        $config['source_image'] = $this->upload_image;
                        $config['thumb_marker'] = '-main';
                        #$config['crop'] = false;
                        $config['width'] = 400;
                        $config['height'] = 327;
                        $config['within_rectangle'] =true;
                        $config['small_image_resize'] = 'no_resize';
                        resize_exact($config);
                        unset($config);
						
						$config = array();
						$config['source_image'] = $this->upload_image;
						$config['thumb_marker'] = '-big';
						#$config['crop'] = false;
						$config['width'] = 800;
						$config['height'] = 536;
						$config['within_rectangle'] =true;
						$config['small_image_resize'] = 'no_resize';
						resize_exact($config);
						unset($config);
						
                       # @@@@@@@@@@@@ NEW RESIZING PART [END] @@@@@@@@@@@
					
					$this->s_picture_path = $new_imagename.'.'.$ext;
					
					@unlink($this->upload_image); //Unlink the original image........
					//@unlink($this->upload_path.getThumbName($prev_img,'thumb')); //Unlink the prevoius image........
					
					return $this->s_picture_path;
					
				}
        else
        {
            return $prev_img; // Unchanged previous image
        }
        
        
    }
	
	   # function to move all those images from "temp" folder to user_photos and resizing accordingly...
        public function move_temp_images_AJAX($flash_uploaded_pic)
        {
            $arr_data = array();
            $i=0;
                $FLASH_UPLOADED_PHOTOS_ARR = $flash_uploaded_pic;
                            
                foreach($FLASH_UPLOADED_PHOTOS_ARR as $key=>$val)
                {
                   
                        $TMP_ORIGINAL_IMG = $val;
                        $ORIGINAL_VERSION = $TMP_ORIGINAL_IMG;
                        $arr_data[$i] = $this->resize_photos( $TMP_ORIGINAL_IMG);
                        // = $val;
                        $i++;
                     
                }
                
             
                return $arr_data;
        }
		
		
        public function resize_photos( $uploaded_tmp_file=null )
        { 
            
            preg_match('/(^.*)\.([^\.]*)$/', $uploaded_tmp_file, $matches);
            $ext = "";
            if(count($matches)>0) {
                $ext = strtolower($matches[2]);
                $original_name = $matches[1];
            }
            else
                $original_name = 'photo';

            
            $imagename = createImageName( $original_name );
            
            # fixing image path...
            $IMG_DESTINATION_PATH = $this->upload_path; 
            
            $chk_imagename = $imagename; //echo $IMG_DESTINATION_PATH.$chk_imagename.'-main.'.$ext ; exit;
        
			if(test_file($IMG_DESTINATION_PATH.$chk_imagename.'-thumb.'.$ext)) {
						for( $i=0; test_file($IMG_DESTINATION_PATH.$chk_imagename.'-'.$i.'-thumb.'.$ext); $i++ ) {
						}

						$new_imagename = $imagename.'-'.$i;
			 }
			else {
				$new_imagename = $imagename;
			}

            
            $this->photoname = $new_imagename;
            $this->upload_photo = $IMG_DESTINATION_PATH. $new_imagename.'.'.$ext;
            $UPLOADED_TEMP_IMG = $this->upload_tmp_path . $uploaded_tmp_file;

            @rename($UPLOADED_TEMP_IMG, $this->upload_photo);

            $config = array();
			$config['source_image'] = $this->upload_photo;
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
			$config['source_image'] = $this->upload_photo;
			$config['thumb_marker'] = '-mini';
			$config['crop'] = false;
			$config['width'] = 73;
			$config['height'] = 73;
			$config1['crop_from'] = 'middle';
			$config['within_rectangle'] = true;
			$config['small_image_resize'] = 'no_resize';
			resize_exact($config);
			unset($config);
			
			$config = array();
			$config['source_image'] = $this->upload_photo;
			$config['thumb_marker'] = '-mid';
			#$config['crop'] = false;
			$config['width'] = 366;
			$config['height'] = 220;
			$config['within_rectangle'] =true;
			$config['small_image_resize'] = 'no_resize';
			resize_exact($config);
			unset($config);
			
			$config = array();
			$config['source_image'] = $this->upload_photo;
			$config['thumb_marker'] = '-main';
			#$config['crop'] = false;
			$config['width'] = 400;
			$config['height'] = 327;
			$config['within_rectangle'] =true;
			$config['small_image_resize'] = 'no_resize';
			resize_exact($config);
			unset($config);
            
			$config = array();
			$config['source_image'] = $this->upload_photo;
			$config['thumb_marker'] = '-big';
			#$config['crop'] = false;
			$config['width'] = 800;
			$config['height'] = 536;
			$config['within_rectangle'] =true;
			$config['small_image_resize'] = 'no_resize';
			resize_exact($config);
			unset($config);
           
            @unlink($this->upload_photo);
            
            # also unlink the temp thumb version...
            $TMP_THUMB_IMG = getThumbName($uploaded_tmp_file, 'thumb');
            @unlink($this->upload_tmp_path . $TMP_THUMB_IMG);
                    
            return  $new_imagename.'.'.$ext;       
        }
		
		
	public function edit_photo_ajax($id)
	{
		try
		{
			parent::check_login(TRUE,'',array('1')); // put this code on those pages which are not accessable by non logged in user
			
			if($_POST){
				  $arr_messages = array();
				  
				  $id = intval($this->input->post('i_edit_id')); 
				//  $i_album_id  = intval($this->input->post('i_album_id'));  
				  # error message trapping...
				  if( trim($this->input->post('s_title'))=='') 
				  {
						  $arr_messages['edit_title'] = "* Required Field.";
				  }
				  
				 
				  if( trim($this->input->post('select_album1'))=='-1') 
				  {
						  $arr_messages['edit_album1'] = "* Required Field.";
				  }
				  
				  if(trim($this->input->post('select_album1')) == 'new_album' && trim($this->input->post('txt_album_name')) == ''){
					  
					  $arr_messages['album_name'] = "* Required Field.";
				  }
				  
				  if( isset($_FILES['s_photo']['name']) && $_FILES['s_photo']['name']!='') {
					  preg_match('/(^.*)\.([^\.]*)$/', $_FILES['s_photo']['name'], $matches);
					  $ext = "";
					  if(count($matches)>0) {
						  $ext = $matches[2];
						  $original_name = $matches[1];
					  }
					  else
						  $original_name = 'photo';
	  
				  
					  if ( !in_array($ext , $this->config->item('VALID_IMAGE_EXT'))) 
					  {
						   $arr_messages['edit_photo'] ="supported extensions are ".implode(' , ',$this->config->item('VALID_IMAGE_EXT'));
					  }
					  else if($_FILES['s_photo']['size'] > $this->config->item('MAX_UP_FILE_SIZE')*1024*1024)
					   {
						  $arr_messages["edit_photo"] = "Maximum file upload size is ".$this->config->item('MAX_UP_FILE_SIZE')." MB.";
					   }		
				  }
	  
				 //pr($arr_messages);
				  if( count($arr_messages)==0 ) {
						  
					  $info = array();
					  
					 
						  
					  $info['i_photo_album_id'] = intval($this->input->post('select_album1'));
					  
					  $info['i_user_id'] = intval(decrypt($this->session->userdata('user_id')));
					  $info['s_title'] = get_formatted_string($this->input->post('s_title')); 
					  $info['s_description'] = get_formatted_string($this->input->post('s_description')); 
					   if( isset($_FILES['s_photo']['name']) && $_FILES['s_photo']['name']!='') {
								$info['s_photo'] 	= $this->_upload_photo();	
					   }
					  //$info["i_order"] =  1+$this->photo_albums_model->get_i_order($info['i_photo_album_id']);		
					  $info['dt_updated_on'] = get_db_datetime();
					  $_ret = $this->user_photos_model->update($info, $id);
					  //$i_total = $this->user_photos_model->get_total_by_user_id($info['i_user_id']);
					  
					  
					  $current_album_id = $this->session->userdata('current_album_id');
                      
					  ob_start();
					  $this->album_wise_photo_ajax_pagination($current_album_id);
					  $content = ob_get_contents();
					  $content_obj = json_decode($content);
					  $html = base64_encode($content_obj->html); 
					  $data['total'] = $content_obj->total;
					  ob_end_clean();
					  
						  
					  echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'html'=>$html,'msg'=>'Photo updated successfully.'));
				  }
				  else
				  {
					  echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'Error!') );
				  }
			}
			else
				{
					  
					    //$photo_arr = $this->scrolling_headlines_model->get_by_id($id) ;
						$i_user_id = intval(decrypt($this->session->userdata('user_id')));
						$s_where = " AND id = {$id}";
			  			$photo_arr = $this->user_photos_model->get_by_user_id($i_user_id, $s_where , 0, 1);
						//pr($photo_arr);
						echo json_encode( array('success'=>true,'photo_arr'=>$photo_arr[0],'msg'=>'Photo updated successfully.'));

				  }
		
		
		}
		catch(Exception $err_obj)
        {
            
        } 
	}
	

}   // end of controller...

