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

class Create_photo_album extends Base_controller
{
    private $pagination_per_page =  2 ;
	private $upload_path ;
    
    public function __construct()
     {
        try
        {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
           
		    $this->upload_path = BASEPATH.'../uploads/user_album_photos/';
            $this->load->model('users_model');
			$this->load->model('photo_albums_model');
		    # loading reqired model & helpers...
            $this->load->model('users_model');
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    
    public function index($id= '') 
    {
        try
        {
                  
            $posted=array();
            $data = $this->data;      
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',*/
										'js/production/tweet_utilities.js',
                                        //'js/stepcarousel.js'
                                        ));
                                        
          //  parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
           //                               'css/dd.css') );
										  
			############################################################
			 if(intval($id)<=0){
				$i_user_id = intval(decrypt($this->session->userdata('user_id')));
				$data['page_view_type'] = 'myaccount'; 
			}
			$arr_profile_info = $this->users_model->fetch_this($i_user_id);
			$data['arr_profile_info'] = $arr_profile_info;
            
            parent::_set_all_photo_album_data($i_user_id);
            
            # view file...
            $VIEW = "logged/photos/create_photo_album.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    } 
	
	public function add_photo_album()
	{
		try
		{
			parent::check_login(TRUE,'',array('1')); // put this code on those pages which are not accessable by non logged in user
			$arr_messages = array();
				
			# error message trapping...
			if( trim($this->input->post('txt_name'))=='') 
			{
					$arr_messages['name'] = "* Required Field.";
			}
			if( trim($this->input->post('txt_add_desc'))=='') 
			{
					$arr_messages['desc'] = "* Required Field.";
			}
			if( trim($this->input->post('privacy'))=='-1') 
			{
					$arr_messages['privacy'] = "* Required Field.";
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
					 $arr_messages['album_photo'] ="supported extensions are ".implode(' , ',$this->config->item('VALID_IMAGE_EXT'));
				}
				else if($_FILES['s_photo']['size'] > $this->config->item('MAX_UP_FILE_SIZE')*1024*1024)
				 {
					$arr_messages["album_photo"] = "Maximum file upload size is ".$this->config->item('MAX_UP_FILE_SIZE')." MB.";
				 }		
			}else
			{
				$arr_messages['album_photo'] = "* Required Field.";
			}
			$flag	= 0;
			
			/* new change to allow group level privacy when changed from general settings
			
			if(trim($this->input->post('privacy'))=='private')
			{
				foreach($_POST['ringid'] as $val)
				{
					if(count($_POST['ringuser_'.$val])==0)
						$flag++;
				}
				foreach($_POST['pgid'] as $val)
				{
					if(count($_POST['pguser_'.$val])==0)
						$flag++;
				}
				if(count($_POST['frnds'])==0 && count($_POST['netpal'])==0 && count($_POST['pp'])==0 )
					$flag++;
					
				if($flag==0)
					$arr_messages['privacy'] = "* Required Field.";
			}*/
			
		   //pr($arr_messages);
			if( count($arr_messages)==0 ) {
					
				$info = array();
				
				$info['i_user_id'] = intval(decrypt($this->session->userdata('user_id')));	 
				$info['s_name'] = get_formatted_string($this->input->post('txt_name')); 
				$info['s_desc'] = get_formatted_string($this->input->post('txt_add_desc')); 
				$info['e_privacy'] = get_formatted_string($this->input->post('privacy')); 
				$info['s_photo'] = $this->_upload_photo();
				$info['dt_created_on'] = get_db_datetime();
				
				$_ret = $this->photo_albums_model->insert($info);
				
				###########################Privacy settings###################################
				insert_privacy($_ret,$_POST,$this->db->photoalbum_privacy,'i_photo_album_id');
				###########################Privacy settings###################################
				echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'Photo Album created Successfully.') );
			}
			else
			{
				echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'Please provide values for all required fields!'));
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
					//echo $this->upload_path; exit;

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
	
	
	
	public function edit_photo_album($id)
	{
		try
		{   
		
			$posted=array();
            $data = $this->data;      
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css') );
										  
			############################################################
			$i_user_id = intval(decrypt($this->session->userdata('user_id')));
			$data['page_view_type'] = 'myaccount'; 
			
			$arr_profile_info = $this->users_model->fetch_this($i_user_id);
			$data['arr_profile_info'] = $arr_profile_info;
			
            
            parent::_set_all_photo_album_data($i_user_id);
			
			$s_where = " AND i_user_id = {$i_user_id}";
			$data['posted'] = $this->photo_albums_model->get_by_album_details_id($id, $s_where, null, null);
			$data['privacy_arr'] = $this->photo_albums_model->get_privacy_settings_by_album_id($id);
			#pr($data['privacy_arr'],1);
			
			## to prevent url hit ###
			if(is_array($data['posted'])){
				# view file...
            	$VIEW = "logged/photos/edit_photo_album.phtml"; 
            	parent::_render($data, $VIEW);	
			}else{
                $re_page = base_url()."manage-my-photo.html";
				
				header("location:".$re_page);
				exit;
				
			}
			
			
           }
		catch(Exception $err_obj)
        {
            
        } 
	}  
	
	public function edit_album($id , $user_id)
	{
		try
		{
			parent::check_login(TRUE,'',array('1')); // put this code on those pages which are not accessable by non logged in user
			
			$i_logged_id = intval(decrypt($this->session->userdata('user_id')));
			
			$arr_messages = array();
				
			# error message trapping...
			if( trim($this->input->post('txt_name'))=='') 
			{
					$arr_messages['name'] = "* Required Field.";
			}
			if( trim($this->input->post('txt_desc'))=='') 
			{
					$arr_messages['desc'] = "* Required Field.";
			}
			/*if( trim($this->input->post('privacy'))=='-1') 
			{
					$arr_messages['privacy'] = "* Required Field.";
			}*/
			
					
			
		   //pr($arr_messages);
			if( count($arr_messages)==0 ) {
					
				$info = array();
				
				$info['i_user_id'] = intval(decrypt($this->session->userdata('user_id')));	 
				$info['s_name'] = get_formatted_string($this->input->post('txt_name')); 
				$info['s_desc'] = get_formatted_string($this->input->post('txt_desc')); 
				$info['e_privacy'] = get_formatted_string($this->input->post('privacy')); 
				
				if($_FILES['s_photo']['name'] != ""){
					$info['s_photo'] = $this->_upload_photo();
				}
				$info['dt_created_on'] = get_db_datetime();
				
				$_ret = $this->photo_albums_model->update($info , $id);
				
				###########################Privacy settings###################################
				$this->db->query("DELETE FROM {$this->db->photoalbum_privacy} WHERE i_photo_album_id='".$id."'");
				insert_privacy($id,$_POST,$this->db->photoalbum_privacy,'i_photo_album_id');
				###########################Privacy settings###################################
									
				echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'Photo Album updated Successfully.') );
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
	
}   // end of controller...

