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

class Organize_audio extends Base_controller
{
    private $pagination_per_page = 10;
    
    public function __construct()
     {
        try
        {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            $this->upload_path = BASEPATH.'../uploads/user_audio_album_photos/';
			$this->upload_path_music_full = BASEPATH.'../uploads/user_audio_files/';
		    # loading reqired model & helpers...
            $this->load->model('users_model');
			$this->load->model('user_audios_model');
			$this->load->model('audio_albums_model');
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
										'js/ddsmoothmenu.js',
                                        'js/switch.js',
										//'js/jquery.dd.js',

										//'js/jquery.js',
										'js/thickbox.js',
										'js/animate-collapse.js',
										
										
                                        'js/lightbox.js',
										'js/jquery-ui-1.8.2.custom.min.js',
										
                                        'js/stepcarousel.js',
										'js/frontend/logged/my_audio/organize_audio.js',
										'js/frontend/logged/tweets/tweet_utilities.js'
										
                                        ));
			
				  parent::_add_js_arr( array(
											  'js/jwplayer/jwplayer.js',
										 
										  ) );
			    parent::_add_css_arr( array('css/miniplayer.css'));
                                        
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                         'css/thickbox.css') );
										  
			############################################################
			$i_user_id = intval(decrypt($this->session->userdata('user_id')));
			$data['page_view_type'] = 'myaccount'; 
			$arr_profile_info = $this->users_model->fetch_this($i_user_id);
			parent::_set_all_audio_album_data($i_user_id);
			
			#### FETCHING PHOTOS PER USER 
			if(is_array($arr_profile_info) && !empty($arr_profile_info)){
				$data['arr_profile_info'] = $arr_profile_info;
				
				$s_where = " AND `i_user_id` = {$i_user_id}";
				$data['arr_albums'] = $this->audio_albums_model->get_by_album_details_id($i_album_id, $s_where, 0,1);
				$data['arr_albums_photos'] = $this->audio_albums_model->get_audios_by_album_id($i_album_id, $i_user_id, 0 ,$this->pagination_per_page);
				
				
				###fetching all photo to show in slideshow
				$this->session->set_userdata('current_album_id', $i_album_id);
				//$data['arr_allphotos'] = $this->user_audios_model->get_by_user_id($i_user_id);
				###############################################################################
				$data['current_album_id'] = $i_album_id;
				$data['pagination_per_page'] = $this->pagination_per_page;
               
                ob_start();
                $this->album_wise_audio_ajax_pagination($i_album_id);
                $content = ob_get_contents();
                $content_obj = json_decode($content);
                $data['result_album_content'] = $content_obj->html; 
				$data['total'] = $content_obj->total;
               
                ob_end_clean(); 
				
			}
			#pr($data['photo_info']);
           #pr($data['arr_albums_photos']);
            # view file...
            $VIEW = "logged/audios/organize_audio.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }   
	
	
	public function new_audio_ajax_pagination($i_album_id , $page=0)  {
		
		$cur_page = $page + $this->pagination_per_page;
		$i_user_id = intval(decrypt($this->session->userdata('user_id')));
		$data = $this->data;
		
		$result		= $this->audio_albums_model->get_audios_by_album_id($i_album_id,
						  $i_user_id, intval($page), $this->pagination_per_page);
						  
		$total_rows 	= $this->audio_albums_model->get_total_audios_albm_id_($i_album_id,$i_user_id); 
		$data['arr_albums_audios'] = $result;
		$data['no_audio_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
		$data['current_album_id'] = $i_album_id;
		
		$VIEW_FILE = "logged/audios/blocks/load_album_wise_audios_listing_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
        echo json_encode( array('html'=>$content, 'current_page'=>$cur_page, 'no_audio_of_result'=>$data['no_audio_of_result']) );
	}
	
	
	
	public function album_wise_audio_ajax_pagination($i_album_id,$page=0) 
	{

	   try
	   {
		  $current_page = $page + $this->pagination_per_page;
		  
		  $data = $this->data;    
		  
		 
		  
		  $i_user_id = intval($i_user_id)<=0?intval(decrypt($this->session->userdata('user_id'))):intval($i_user_id);
		     
		  $result		= $this->audio_albums_model->get_audios_by_album_id($i_album_id,
		  			 		$i_user_id, intval($page), $this->pagination_per_page);
							
		//pr($result);
		  $total_rows 	= $this->audio_albums_model->get_total_by_album_id($i_album_id,$i_user_id);
		  
	 	    //--- for check whether more  are there or not
            $view_more = true;
             $rest_counter = $total_rows-$page;
             if($rest_counter<=$this->pagination_per_page)
                $view_more = false;
             
             
             //--------- end check
		  
		  
		  $data['arr_albums_audios'] = $result;
		  $data['no_of_result'] = $total_rows;
		  $data['current_page'] = $current_page;
		  
		  $data['total_pages'] = ceil($total_rows/$this->pagination_per_page);
		  
		  $p = ($page/$this->pagination_per_page);
		  $data['current_loaded_page_no'] =  $p + 1;
		  
		  $VIEW_FILE = "logged/audios/blocks/load_album_wise_audios_listing_ajax.phtml";
		  //$this->load->view( $VIEW_FILE , $data);
          
          if( is_array($result) && count($result) ) {
            $content = $this->load->view( $VIEW_FILE , $data, true);
            }
            else {
                $content = '';
            }
            echo json_encode( array('html'=>$content, 'current_page'=>$current_page ,'total'=>$total_rows,'view_more'=>$view_more) );
	  } 
	  catch(Exception $err_obj)
	  {
		  
	  } 
  
  } 
 
	//ordering in ajax
   function maintain_displayorder_ajax($page=0)
	{
		//sleep(2);
		$data = $this->data;
		$actionID = $this->input->post('rid'); 
		$i_album_id = $this->input->post('i_album_id');
		$status = $this->input->post('status');
		$i_user_id = intval($i_user_id)<=0?intval(decrypt($this->session->userdata('user_id'))):intval($i_user_id);
 	  	$data['page_view_type'] = $page_view_type;
				
		
		# retrieving  info...
		//$arr_albums_photos = $this->audio_albums_model->get_audios_by_album_id($actionID,$i_user_id);
	
		$this->load->model("utility_model");
		$tbl=$this->db->USER_AUDIO;
	
	 	
		$WHERE_COND_BEGIN = " AND  	i_id_audio_album = {$i_album_id}";	
		$this->utility_model->Ranking($status, $actionID, $WHERE_COND_BEGIN,$tbl);
		$order_by = " `i_order` DESC ";
		
		$result		= $this->audio_albums_model->get_audios_by_album_id($i_album_id,$i_user_id, 
																		intval($page), $this->pagination_per_page);
		
		//pr($result);
							
		$total_rows 	= $this->audio_albums_model->get_total_by_album_id($i_album_id,$i_user_id);
		$resultCount = count($result);
		
	
	  # loading the view-part...
		//echo  $this->load->view('logged/audios/blocks/load_album_wise_audios_listing_ajax.phtml', $data, TRUE);
		  ob_start();
		  $this->album_wise_audio_ajax_pagination($i_album_id);
		  $content = ob_get_contents();
		  $content_obj = json_decode($content);
		  $html = $content_obj->html; 
		  $total_rows = $content_obj->total;
		  $view_more = $content_obj->view_more;
		  $current_page = $content_obj->current_page;
		  ob_end_clean(); 
		  
		  echo json_encode( array('html'=>$html, 'current_page'=>$current_page , 'total'=>$total_rows,'view_more'=>$view_more) );
			
	}
	
	function change_audio_album(){
		
		$current_page = $this->input->post('current_page');
		$current_album_id =  $this->input->post('curr_album_id');
		
		$info['i_id_audio_album'] = $this->input->post('album_id');
		
		# update i_order
		if($current_album_id != $info['i_id_audio_album']){
			$info["i_order"] =  1+$this->audio_albums_model->get_i_order($info['i_id_audio_album']);
		}
		$photo_id = $this->input->post('record_id');
		
		$this->user_audios_model->update($info, $photo_id);
		
		$i_user_id = intval(decrypt($this->session->userdata('user_id')));
		parent::_set_all_audio_album_data($i_user_id);
		/*ob_start();
		$this->album_wise_audio_ajax_pagination($current_album_id, $data['page_view_type'], $current_page);
		$result_album_content = (ob_get_contents());
		$data['total'] = $content_obj->total;
		ob_end_clean(); 
		
		echo json_encode( array( 'content'=>$result_album_content, ) );*/
		
		  ob_start();
		  $this->album_wise_audio_ajax_pagination($current_album_id);
		  $content = ob_get_contents();
		  $content_obj = json_decode($content);
		  $html = $content_obj->html; 
		  $total_rows = $content_obj->total;
		  $view_more = $content_obj->view_more;
		  $current_page = $content_obj->current_page;
		  ob_end_clean(); 
		  
		  echo json_encode( array('sucess'=>TRUE,'html'=>$html, 'current_page'=>$current_page , 'total'=>$total_rows,'view_more'=>$view_more,		  'msg'=>'Audio successfully moved.') );
		
	}
	
	
	public function ajax_add_new_track() {
		  parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
		  
		$arr_messages = array();

		$user_id = intval(decrypt($this->session->userdata('user_id')));
		
		## validation  part
		if(trim($this->input->post('txt_title')=='')){
			$arr_messages['title'] = '* Required Field.';
		}
		
		if(trim($this->input->post('txt_artists')=='')){
			$arr_messages['artists'] = '* Required Field.';
		}
		
		if(trim($this->input->post('txt_genre')=='')){
			$arr_messages['genre'] = '* Required Field.';
		}
		
			
		if(trim($this->input->post('select_album1')=='-1')){
			$arr_messages['album1'] = '* Required Field.';
		}
	
				
		# audio-file uploading part...
		if( isset($_FILES['track_music_file']['name']) && $_FILES['track_music_file']['name']!='') {
			
			$ext_arr = get_ext($_FILES['track_music_file']['name']);
			
			#$music_ext = $ext_arr['ext'];
            $music_ext = strtolower($ext_arr['ext']);
			$music_filename = $ext_arr['filename'];
			
			if(!in_array($music_ext, $this->config->item('VALID_MUSIC_EXT')) ) 
			 {
				$arr_messages['track_music_file'] = 'Allowed extension is mp3';
			}
		}
		else {
			$arr_messages['track_music_file'] = '* Required Field.';
		}
		
		//echo 1; exit;
		
		$MAX_AUDIO =  $this->data['site_settings_arr']['i_max_audio_upload'];
		$TOTAL_AUDIO = $this->user_audios_model->get_total_by_user_id($user_id);

		if( count($arr_messages)==0 && ($TOTAL_AUDIO < $MAX_AUDIO || $MAX_AUDIO == 0 )) {
		//die(22);
			$arr_data["s_title"] = htmlspecialchars($this->input->post('txt_title'), ENT_QUOTES, 'utf-8');
			
			
			###### uploading audio
			
			if( isset($_FILES['track_music_file']['name']) && $_FILES['track_music_file']['name']!='') {
			
			$ext_arr = get_ext($_FILES['track_music_file']['name']);
			
			#$music_ext = $ext_arr['ext'];
            $music_ext = strtolower($ext_arr['ext']);
			$music_filename = $ext_arr['filename'];
			
			if( in_array($music_ext, $this->config->item('VALID_MUSIC_EXT')) ) {
				$music_filename = createImageName( $music_filename );
				
				//echo $this->upload_path_music_full.$music_filename.'.'.$music_ext; 

				if(test_file($this->upload_path_music_full.$music_filename.'.'.$music_ext)) {
					for( $i=0; test_file($this->upload_path_music_full.$music_filename.'-'.$i.'.'.$music_ext); $i++ ) {
					}

					$new_music_filename = $music_filename.'-'.$i;
				}
				else {
					$new_music_filename = $music_filename;
				}

				$this->music_filename = $new_music_filename;

				$this->upload_music_filename = $this->upload_path_music_full.$new_music_filename.'.'.$music_ext;

				move_uploaded_file($_FILES['track_music_file']['tmp_name'], $this->upload_music_filename);
				
				
			}
			else {
				$arr_messages['track_music_file'] = 'Allowed extension is mp3';
			}
		} 
			
			##### uploading audio 
			
			if( isset($_FILES['track_music_file']['name']) && $_FILES['track_music_file']['name']!='') {
				$arr_data["s_audio_file_name"] = $new_music_filename.'.'.$music_ext;
			}
			
			
			$arr_data['i_user_id'] = $user_id;
			$arr_data['i_id_audio_album'] = $this->input->post('select_album1');
			$arr_data['s_artist'] = get_formatted_string($this->input->post('txt_artists')); 
			$arr_data['s_genre'] = get_formatted_string($this->input->post('txt_genre')); 
			$arr_data['s_desc'] = get_formatted_string($this->input->post('ta_desc')); 
			$arr_data['s_sound_track_album'] = get_formatted_string($this->input->post('txt_album_name')); 
			
			
			$arr_data["i_order"] =  1+$this->audio_albums_model->get_i_order($arr_data['i_id_audio_album']);
			$cur_date = get_db_datetime();
			$arr_data['dt_created_on'] = $cur_date;
			

			$track_id = $this->user_audios_model->insert($arr_data);
			$current_album_id = $this->session->userdata('current_album_id');
           
			
			ob_start();
			$this->album_wise_audio_ajax_pagination($current_album_id);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$html = base64_encode($content_obj->html);
			$data['total'] = $content_obj->total; 
			ob_end_clean(); 
			
			
			echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages, 'html'=>$html,'msg'=>'Audio Uploaded Successfully.'));
			exit;
		}
		else if(count($arr_messages)==0 && $TOTAL_AUDIO  == $MAX_AUDIO ){	
			@unlink($this->upload_music_filename);
			echo json_encode( array('success'=>false,'msg'=>'Audio cannot be uploaded as maximum audio upload limit reached!','album_id'=>$album_id,'maxlimit'=>true) );
			exit;
		}
		else {
			@unlink($this->upload_music_filename);
			echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'Error!','album_id'=>$album_id) );
			exit;
		}
	}
	
	public function edit_audio_ajax($id)
	{
		try
		{
			parent::check_login(TRUE,'',array('1')); // put this code on those pages which are not accessable by non logged in user
			$user_id = intval(decrypt($this->session->userdata('user_id')));
			 if($_POST){
				  
				  $arr_messages = array();
				  
				  $id = intval($this->input->post('i_edit_id')); 
			
				  # error message trapping...
				   ## validation  part
					if(trim($this->input->post('s_title')=='')){
						$arr_messages['title'] = '* Required Field.';
					}
					
					if(trim($this->input->post('s_artist')=='')){
						$arr_messages['artist'] = '* Required Field.';
					}
					
					if(trim($this->input->post('s_genre')=='')){
						$arr_messages['genre'] = '* Required Field.';
					}
					
					
					
					if(trim($this->input->post('select_album1')=='-1')){
						$arr_messages['album1'] = '* Required Field.';
					}
					
					# audio-file uploading part...
					if( isset($_FILES['track_music_file']['name']) && $_FILES['track_music_file']['name']!='') {
						$ext_arr = get_ext($_FILES['track_music_file']['name']);
						
						#$music_ext = $ext_arr['ext'];
						$music_ext = strtolower($ext_arr['ext']);
						$music_filename = $ext_arr['filename'];
						
						if( in_array($music_ext, $this->config->item('VALID_MUSIC_EXT')) ) {
							$music_filename = createImageName( $music_filename );
							
							//echo $this->upload_path_music_full.$music_filename.'.'.$music_ext; 
			
							if(test_file($this->upload_path_music_full.$music_filename.'.'.$music_ext)) {
								for( $i=0; test_file($this->upload_path_music_full.$music_filename.'-'.$i.'.'.$music_ext); $i++ ) {
								}
			
								$new_music_filename = $music_filename.'-'.$i;
							}
							else {
								$new_music_filename = $music_filename;
							}
			
							$this->music_filename = $new_music_filename;
			
							$this->upload_music_filename = $this->upload_path_music_full.$new_music_filename.'.'.$music_ext;
			
							move_uploaded_file($_FILES['track_music_file']['tmp_name'], $this->upload_music_filename);
						}
						else {
							$arr_messages['track_music_file'] = 'Allowed extension is mp3';
						}
					}
					
	  
				 //pr($arr_messages);
				  if( count($arr_messages)==0 ) {
						
					  $arr_data["s_title"] = htmlspecialchars($this->input->post('s_title'), ENT_QUOTES, 'utf-8');
					  
					  
					  if( isset($_FILES['track_music_file']['name']) && $_FILES['track_music_file']['name']!='') {
						  $arr_data["s_audio_file_name"] = $new_music_filename.'.'.$music_ext;
						  
						  @unlink($this->upload_path_music_full.$this->input->post('s_audio_file_name'));
						  /*@unlink($this->upload_path.getThumbName($album_details['s_photo'], 'big'));*/
					  }
					  
					  
					  $arr_data['i_user_id'] = $user_id;
					  $arr_data['i_id_audio_album'] = $this->input->post('select_album1');
					  $arr_data['s_artist'] = get_formatted_string($this->input->post('s_artist')); 
					  $arr_data['s_genre'] = get_formatted_string($this->input->post('s_genre')); 
					  $arr_data['s_desc'] = get_formatted_string($this->input->post('s_desc')); 
					  $arr_data['s_sound_track_album'] = get_formatted_string($this->input->post('s_sound_track_album')); 
					  
					  
					
					  $cur_date = get_db_datetime();
					  ///$arr_data['dt_created_on'] = $cur_date;
					  $arr_data['dt_updated_on'] = $cur_date;
					  
		  
					  $track_id = $this->user_audios_model->update($arr_data , $id);
					  
					  $current_album_id = $this->session->userdata('current_album_id');
					
					  
					  
					  ob_start();
					  $this->album_wise_audio_ajax_pagination($current_album_id);
					  $content = ob_get_contents();
					  $content_obj = json_decode($content);
					  $html = base64_encode($content_obj->html); 
					  $data['total'] = $content_obj->total;
					 
					  ob_end_clean();  
					  
					  
					  echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages, 'html'=>$html,'msg'=>'Audio Updated Successfully.'));
					  exit;
		  
				  }
				  else
				  {
					  @unlink($this->upload_music_filename);
					  echo json_encode( array('success'=>false,
					  						  'arr_messages'=>$arr_messages,'msg'=>'Error!') );
					  exit;
				  }
			}
			else
				{
					  
						$i_user_id = intval(decrypt($this->session->userdata('user_id')));
						$s_where = " AND audio.id = {$id}";
			  			$audio_arr = $this->user_audios_model->get_by_user_id($i_user_id, $s_where , 0, 1);
						//pr($photo_arr);
						echo json_encode( array('success'=>true,'audio_arr'=>$audio_arr[0]));

				  }
		
		
		}
		catch(Exception $err_obj)
        {
            
        } 
	}
	
}   // end of controller...

