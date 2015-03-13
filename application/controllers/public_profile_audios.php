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

class Public_profile_audios extends Base_controller
{
    private $pagination_per_page = 5;
    private $album_pagination_per_page = 10;
    private $audio_pagination_per_page = 10;
    
    
    public function __construct()
     {
        try
        {
            parent::__construct();
            
            $this->upload_tmp_path = BASEPATH.'../uploads/wall_tmp/';
            $this->upload_path  = BASEPATH.'../uploads/wall_photos/';
               $this->upload_photo_path    = BASEPATH.'../uploads/wall_photos/';
            # loading reqired model & helpers...
            $this->load->helper('wall_helper');
            $this->load->model('users_model');
            //$this->load->model('netpals_model');
            //$this->load->model('my_prayer_partner_model');
            //$this->load->model('data_newsfeed_model');
            //$this->load->model('newsfeed_comments_model');
            $this->load->model('media_comments_model');
            //$this->load->model('user_notifications_model');
            //$this->load->model('user_alert_model');
            $this->load->model('audio_albums_model');
            $this->load->model('user_audios_model');
            
            
            
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }
    
    
    
    
        # index function definition...
    public function index($user_id='', $album_id ='') 
    {
        try
        { 
            
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/lightbox.js',
                                        'js/tab.js',
                                        'js/jquery.autofill.js',
                                        'js/jquery.lightbox.js',
										'js/stepcarousel.js',*/
                                        'js/production/my_friends.js',
                                        'js/production/my_net_pals.js',
                                        'js/production/my_prayer_partner.js',
                                        'js/production/my_message.js',
										'js/production/tweet_utilities.js',
                                        'js/jwplayer/jwplayer.js'
                                        ));
                                        
        //    parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css') );
                                        
                                        
            # adjusting header & footer sections [End]...
            $data['page_view_type'] = 'public_account';
            
            $i_profile_id = intval($user_id);
            
            $public_arr_profile_info = $this->users_model->fetch_this($i_profile_id);
            $data['public_arr_profile_info'] = $public_arr_profile_info;
            $data['album_id'] = $album_id;
            $data['profile_id'] = $i_profile_id;
          
          
            $data['audio_pagination_per_page'] = $this->audio_pagination_per_page;
            $data['album_detail'] =  $this->audio_albums_model->get_by_id($album_id);
			 
			 
			 
			 
            ## audios ##
            ob_start();
            $this->audio_pagination_show_more($i_profile_id , $album_id);
            $content = ob_get_contents();
            $content_obj = json_decode($content);
            $data['result_audio_content'] = $content_obj->html; 
            
            ob_end_clean();
            
            # view file...
            $VIEW_PG_FILE = "public_profile/public_profile_view_all_audio/albumdetails.phtml";
            $VIEW = "{$VIEW_PG_FILE}";
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           show_error($err_obj->getMessage());
        }
    }   //index
    
    
    
    public function audio_pagination_show_more($i_profile_id, $album_id , $page=0)  
    {
        
        $cur_page = $page + $this->audio_pagination_per_page;
        
        
        $data = $this->data;
        
        //$this->load->model('user_photos_model');
        
        $s_where = "AND i_id_audio_album = {$album_id}";
        $result = $this->user_audios_model->get_allaudios_with_comments_by_user_id_($i_profile_id,$s_where,$page , $this->audio_pagination_per_page);
        
        
        $total_rows = $this->user_audios_model->get_total_by_user_id($i_profile_id, $s_where);
        $data['audio_result_arr'] = $result;
        $data['audio_no_of_result'] = $total_rows;
        //$data['current_page_1'] = $page;
        $data['audio_current_page'] = $cur_page;
		$data['profile_id'] = $i_profile_id;
    
		//--- for check whether more videos are there or not
				$view_more = true;
				 $rest_counter = $total_rows-$page;
				 if($rest_counter<=$this->audio_pagination_per_page)
					$view_more = false;
		  //--------- end check
    
    
    
        
        $VIEW_FILE = "public_profile/public_profile_view_all_audio/all_audios_album_wise_ajax.phtml";
        
        if( is_array($result) && count($result) ) {
            $content = $this->load->view( $VIEW_FILE , $data, true);
        }
        else {
            #$content = '<div class="txt_content01"><p style="margin-left: 330px;">No Posts!</p></div>';
            $content = '';
        }
        
        //echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$content, 'current_page'=>$cur_page,'view_more'=>$view_more, 'audio_no_of_result'=>$data['audio_no_of_result']) );
    }
    
    
    
    
    
   public function audio_detail($user_id='', $audio_id ='') 
    {
        try
        { 
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/lightbox.js',
                                        'js/tab.js',
                                        'js/jquery.autofill.js',
                                        'js/jquery.lightbox.js',
										'js/stepcarousel.js',*/
                                        'js/production/my_friends.js',
                                        'js/production/my_net_pals.js',
                                        'js/production/my_prayer_partner.js',
                                        'js/production/my_message.js',
										'js/production/tweet_utilities.js',
										'js/production/my_audio.js',
										'js/production/audio_helper.js',
                                        'js/jwplayer/jwplayer.js'
                                        ));
                                        
          //  parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css') );
                                        
                                        
            # adjusting header & footer sections [End]...
            $data['page_view_type'] = 'public_account';
            
            $i_profile_id = intval($user_id);
            
            $public_arr_profile_info = $this->users_model->fetch_this($i_profile_id);
            $data['public_arr_profile_info'] = $public_arr_profile_info;
			
			$data['audio_detail'] =  $this->user_audios_model->get_by_id($audio_id);
            $data['album_id']     = $data['audio_detail']['i_id_audio_album'];
			$data['album_detail'] =  $this->audio_albums_model->get_by_id($data['album_id'] );
			
			$data['pagination_per_page'] = $this->pagination_per_page;
			$data['audio_id'] = $audio_id;
			
			### added for copy function
			$logged_user_id = intval(decrypt($this->session->userdata('user_id')));
			parent::_set_all_audio_album_data($logged_user_id);
			
			ob_start();
			$this->comments_ajax_pagination($audio_id);
			$content = ob_get_contents();
			$content_obj = json_decode($content); 
			$data['comments_list_html'] = $content_obj->html; 
			$data['no_of_result']  = $content_obj->no_of_result;
			$data['current_page_1'] = $content_obj->current_page;
			ob_end_clean();
            
            # view file...
            $VIEW_PG_FILE = "public_profile/public_profile_view_all_audio/track-details.phtml";
            $VIEW = "{$VIEW_PG_FILE}";
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           show_error($err_obj->getMessage());
        }
    }   //index
    
	
	
	public function comments_ajax_pagination($i_media_id, $page=0)
    {
		
		
		$cur_page = $page + $this->pagination_per_page;
		$data = $this->data;
		
		$result = $this->media_comments_model->get_by_newsfeed_id($i_media_id , 'audio',$page,
																$this->pagination_per_page);
		$total_rows = $this->media_comments_model->get_total_by_newsfeed_id($i_media_id, 'audio');
		
		$data['result_arr'] = $result;
		$data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
		$data['photo_id'] = $i_media_id;
		
		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->pagination_per_page)
			  $view_more = false;
         //--------- end check
		
		
		$VIEW_FILE = "public_profile/public_profile_view_all_audio/audio_comment_ajax_list.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
        echo json_encode( array('html'=>$content, 'current_page'=>$cur_page, 'no_of_result'=>$data['no_of_result'],'total'=>$total_rows,'view_more'=>$view_more  ,'cur_page'=>$data['current_page_1']) );
			
	}
	
	public function copyAudio($id,$current_album_id){
			$this->load->model('user_audios_model');
		    
			$MAX_AUDIO =  $this->data['site_settings_arr']['i_max_audio_upload'];
		    $TOTAL_AUDIO = $this->user_audios_model->get_total_by_user_id(intval(decrypt($this->session->userdata('user_id'))));
		   
		   if($current_album_id == '-1'){
			   echo json_encode( array('success'=>false , 'msg'=>'Please select a album to copy.') );
			   exit;
		   }
		   elseif(($TOTAL_AUDIO < $MAX_AUDIO || $MAX_AUDIO == 0 )){
			   
			   
				$user_id = intval(decrypt($this->session->userdata('user_id')));
				
				$src_path = BASEPATH.'../uploads/user_audio_files/';
				$upload_path = BASEPATH.'../uploads/user_audio_files/';
				
				
				$details_arr = $this->user_audios_model->get_by_id($id);
				$details = $details_arr;
				//pr($details ,1);
				
				if( !is_array($details) || !count($details) 
						|| @$details['s_audio_file_name']=='' ) {
						
					echo json_encode( array('success'=>false, 'msg'=>'Invalid request') );
					exit;
				}
				
			
			$ext_arr = get_ext($details['s_audio_file_name']);
			
			#$music_ext = $ext_arr['ext'];
            $music_ext = strtolower($ext_arr['ext']);
			$music_filename = $ext_arr['filename'];
			
			if( in_array($music_ext, $this->config->item('VALID_MUSIC_EXT')) ) {
				$music_filename = createImageName( $music_filename );
				
				//echo $this->upload_path_music_full.$music_filename.'.'.$music_ext; 

				if(test_file($upload_path.$music_filename.'.'.$music_ext)) {
					for( $i=0; test_file($upload_path.$music_filename.'-'.$i.'.'.$music_ext); $i++ ) {
					}

					$new_music_filename = $music_filename.'-'.$i;
				}
				else {
					$new_music_filename = $music_filename;
				}

				$this->music_filename = $new_music_filename;

				$upload_path = $upload_path.$new_music_filename.'.'.$music_ext;

				//move_uploaded_file($_FILES['track_music_file']['tmp_name'], $this->upload_music_filename);
				$copied = @copy($src_path.$details['s_audio_file_name'], $upload_path);
				
				if(!$copied) {
					echo json_encode( array('success'=>false, 'msg'=>'Some error occurred. Try after sometime.') );
					exit;
				}
				
				
			}
			
				
				 ### insert in user photo db.    
			   if( isset($details['s_audio_file_name']) && $details['s_audio_file_name']!='') {
				  $arr_data["s_audio_file_name"] = $new_music_filename.'.'.$music_ext;
			   }
			
			
					$arr_data['i_user_id'] = $user_id;
					$arr_data['i_id_audio_album'] = $current_album_id;
					$arr_data['s_artist'] = $details['s_artist']; 
					$arr_data['s_genre'] = $details['s_genre'];
					$arr_data['s_desc'] = $details['s_desc'];
					$arr_data['s_sound_track_album'] = $details['s_sound_track_album'];
					$arr_data['s_genre_id']=$details['s_genre_id'];
					$arr_data["i_order"] =  1+$this->audio_albums_model->get_i_order($current_album_id);
					
					$arr_data['dt_created_on'] =  get_db_datetime();
					
		
					$track_id = $this->user_audios_model->insert($arr_data);
				
				    echo json_encode( array('success'=>true , 'msg'=>'Audio copied successfully.') );
					exit;
		   }
		   
		   else if($TOTAL_AUDIO  == $MAX_AUDIO ){	
			 echo json_encode( array('success'=>false,'msg'=>'Audio cannot be uploaded as maximum audio upload limit is reached!'));
			 exit;
		   }
	    
	}
    
    
}
?>