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

class Public_profile_videos extends Base_controller
{
    private $pagination_per_page = 5;
    private $album_pagination_per_page = 10;
    private $video_pagination_per_page = 10;
    
    
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
            $this->load->model('netpals_model');
            $this->load->model('my_prayer_partner_model');
            $this->load->model('data_newsfeed_model');
            $this->load->model('media_comments_model');
            $this->load->model('user_photos_model');
            $this->load->model('my_videos_model');
            $this->load->model('user_notifications_model');
            $this->load->model('user_alert_model');
            $this->load->model('my_videos_model');
            
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
        
            
            parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
                                        'js/lightbox.js',
                                        'js/tab.js',
                                        'js/jquery.autofill.js',
                                        'js/jquery.bxSlider.js',
                                        'js/jquery.lightbox.js',
                                        'js/frontend/public_profile.js',
                                       
                                        'js/frontend/logged/my_friends.js',
                                        'js/frontend/logged/my_net_pals.js',
                                        'js/frontend/logged/my_prayer_partner.js',
                                        'js/frontend/logged/message_box/my_message.js',
										'js/frontend/logged/tweets/tweet_utilities.js',
                                        'js/jquery.fancybox.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                        'css/jquery.fancybox.css') );
                                        
                                        
            # adjusting header & footer sections [End]...
            $data['page_view_type'] = 'public_account';
            
            $i_profile_id = intval($user_id);
            
            $public_arr_profile_info = $this->users_model->fetch_this($i_profile_id);
            $data['public_arr_profile_info'] = $public_arr_profile_info;
            $data['album_id'] = $album_id;
            $data['profile_id'] = $i_profile_id;
			
            $data['video_pagination_per_page'] = $this->video_pagination_per_page;
            $data['album_detail'] =  $this->my_videos_model->get_album_info($album_id);
			
            ## videos ##
            ob_start();
            $this->video_pagination_show_more($i_profile_id , $album_id);
            $content = ob_get_contents();
            $content_obj = json_decode($content);
            $data['result_video_content'] = $content_obj->html; 
            ob_end_clean();
            
        
            # view file...
            $VIEW_PG_FILE = "public_profile/public_profile_view_all_video/video-albumdetails.phtml";
            $VIEW = "{$VIEW_PG_FILE}";
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           show_error($err_obj->getMessage());
        }
    }   //index
    
    
    public function video_pagination_show_more($i_profile_id, $album_id , $page=0)  
    {
        
        $cur_page = $page + $this->video_pagination_per_page;
        
        
        $data = $this->data;
        
        //$this->load->model('user_photos_model');
        
        $s_where = "AND i_video_album_id = {$album_id}";
        $result = $this->my_videos_model->get_allvideos_with_comments_by_user_id_($i_profile_id,$s_where,$page , $this->video_pagination_per_page);
        
        $this->db->last_query();
        $total_rows = $this->my_videos_model->get_total_no_of_videos_of_album_by_album_id($i_profile_id, $s_where);
        //--- for check whether more videos are there or not
            $view_more = true;
             $rest_counter = $total_rows-$page;
             if($rest_counter<=$this->video_pagination_per_page)
                $view_more = false;
        //--------- end check
        
        
        $data['video_result_arr'] = $result;
        $data['video_no_of_result'] = $total_rows;
        $data['video_current_page'] = $cur_page;
    	$data['profile_id'] = $i_profile_id;
        
        $VIEW_FILE = "public_profile/public_profile_view_all_video/all_videos_album_wise_ajax.phtml";
        
        if( is_array($result) && count($result) ) {
            $content = $this->load->view( $VIEW_FILE , $data, true);
        }
        else {
            $content = '';
        }
        
        echo json_encode( array('html'=>$content, 'current_page'=>$cur_page,'view_more'=>$view_more) );
    }
    
    
    
    
    
   
    
    ## SHOWING WALL VIDEOS ##
    
    public function get_video(){
        try
            {
            
                  $i_media_id = intval($this->input->post('media_id'));
                  $width = intval($this->input->post('width'))<=0?'329':intval($this->input->post('width'));
                  $height = intval($this->input->post('height'))<=0?'212':intval($this->input->post('height'));
                  $media_info = $this->data_newsfeed_model->get_by_id( $i_media_id);
            //pr(json_decode($media_info['data']));
            #echo utf8_accents_to_ascii($media_info['s_video_url']);
                  if( !is_array($media_info) || !count($media_info) ) {
                      echo json_encode( array('result'=>'error') );
                      exit;
                  }
                  $video_arr = json_decode($media_info['data']);
                  //$video_arr->video->image_name;
                  //pr($video_arr);
          /* ******************** Get video details ************************ */
          try {
                  $this->load->library('embed_video');
                  $config['zend_library_path'] = APPPATH ."libraries/Zend/";
                  $config['video_url'] =  ($video_arr->video->url);
                  
                  $this->embed_video->initialize($config);
                  $this->embed_video->prepare();
                  $image_source = $this->embed_video->get_player($width,$height);
              }
              catch(Exception $e) {
                  //$data['video_exists'] = false;
                  $image_source = 'This video has been deleted.';
              }
          $result_arr['result'] = 'success';
          $result_arr['s_image_source'] = $image_source;        
          $result_arr['i_media_id'] = $i_media_id;
          echo json_encode($result_arr );
              
        } 
        catch(Exception $err_obj)
            {
            } 
            
     }
    
    ##END  SHOWING WALL VIDEOS ##

   ########################################################################################################
    
    
    public function album_pagination_show_more($i_profile_id , $page=0)     // unused
    {
        
        $cur_page = $page + $this->album_pagination_per_page;
        $data = $this->data;
        $result = $this->my_videos_model->get_by_user_id($i_profile_id, $page, $this->album_pagination_per_page);
        $total_rows = $this->my_videos_model->get_total_by_user_id($i_profile_id);
        $data['album_result_arr'] = $result;
        $data['album_no_of_result'] = $total_rows;
        //$data['current_page_1'] = $page;
        $data['album_current_page'] = $cur_page;
    	$data['profile_id'] = $i_profile_id;
        
        $VIEW_FILE = "public_profile/public_profile_view_all_video/video_albums_ajax.phtml";
        
        if( is_array($result) && count($result) ) {
            $content = $this->load->view( $VIEW_FILE , $data, true);
        }
        else {
            $content = '';
        }
        
        echo json_encode( array('html'=>$content, 'current_page'=>$cur_page) );
    }
	
	public function video_detail($user_id='', $video_id ='') 
    {
        try
        { 
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
                                        'js/lightbox.js',
                                        'js/tab.js',
                                        'js/jquery.autofill.js',
                                        'js/jquery.lightbox.js',
										'js/stepcarousel.js',
                                        'js/frontend/logged/my_friends.js',
                                        'js/frontend/logged/my_net_pals.js',
                                        'js/frontend/logged/my_prayer_partner.js',
                                        'js/frontend/logged/message_box/my_message.js',
										'js/frontend/logged/tweets/tweet_utilities.js',
										'js/frontend/logged/video_helper.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css') );
                                        
                                        
            # adjusting header & footer sections [End]...
            $data['page_view_type'] = 'public_account';
            
            $i_profile_id = intval($user_id);
            
            $public_arr_profile_info = $this->users_model->fetch_this($i_profile_id);
            $data['public_arr_profile_info'] = $public_arr_profile_info;
			
			$data['video_detail'] =  $this->my_videos_model->get_video_info_by_id($video_id);
            $data['album_id']     = $data['video_detail'][0]['i_video_album_id'];
			$data['album_detail'] =  $this->my_videos_model->get_album_info($data['album_id'] );
			
			$data['pagination_per_page'] = $this->pagination_per_page;
			$data['video_id'] = $video_id;
			
			
			### new added to copy video 
			$logged_user_id = intval(decrypt($this->session->userdata('user_id')));
			parent::_get_user_all_video_albums($logged_user_id);
			
			
			ob_start();
			$this->comments_ajax_pagination($video_id);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['comments_list_html'] = $content_obj->html; 
			$data['no_of_result']  = $content_obj->no_of_result;
			$data['current_page_1'] = $content_obj->current_page;
			ob_end_clean();
            
			//pr($data['photo_detail']);
            # view file...
            $VIEW_PG_FILE = "public_profile/public_profile_view_all_video/video-track-details.phtml";
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
		
		$result = $this->media_comments_model->get_by_newsfeed_id($i_media_id , 'video',$page,
																$this->pagination_per_page);
		$total_rows = $this->media_comments_model->get_total_by_newsfeed_id($i_media_id, 'video');
		
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
		
		
		$VIEW_FILE = "public_profile/public_profile_view_all_video/video_comment_ajax_list.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
        echo json_encode( array('html'=>$content, 'current_page'=>$cur_page, 'no_of_result'=>$data['no_of_result'],'total'=>$total_rows,'view_more'=>$view_more  ,'cur_page'=>$data['current_page_1']) );
			
	}
	
	public function copyVideo($id,$current_album_id){
			$this->load->model('my_videos_model');
		    $MAX_VIDEO =  $this->data['site_settings_arr']['i_max_video_upload'];
	        $TOTAL_VIDEO = $this->my_videos_model->get_total_by_user_id(intval( decrypt($this->session->userdata('user_id')) ));
		   
		   
		   if($current_album_id == '-1'){
			   echo json_encode( array('success'=>false , 'msg'=>'Please select a album to copy.') );
			   exit;
		   }
		   elseif(($TOTAL_VIDEO < $MAX_VIDEO || $MAX_VIDEO == 0 )){
			   
			   
				$user_id = intval(decrypt($this->session->userdata('user_id')));
				
				$src_path = BASEPATH.'../uploads/user_videos/';
				$upload_path = BASEPATH.'../uploads/user_videos/';
				
				
				$details_arr = $this->my_videos_model->get_video_info_by_id($id);
				$details = $details_arr[0];
				//pr($details ,1);
				
				if( !is_array($details) || !count($details) 
						|| @$details['s_video_file_name']=='' ) {
						
					echo json_encode( array('success'=>false, 'msg'=>'Invalid request') );
					exit;
				}
				
				
				try{
				
					$this->load->library('embed_video');
					$config['zend_library_path'] = APPPATH ."libraries/Zend/";
					$config['video_url'] = trim($details['s_video_file_name']);
					
					$this->embed_video->initialize($config);
					$this->embed_video->prepare();
					
					$this->embed_video->save_thumb($upload_path, '-bigthumb', 122, 82);
					$this->video_img_name = $this->embed_video->get_resized_imagename();
				}
				catch(Exception $e) 
				{
					echo json_encode( array('success'=>false, 'msg'=>'* Not valid video URL.') );
					exit;
				}
				
				 ### insert in user photo db.    
			   
				$info['i_video_album_id'] = $current_album_id; 
				$info['s_video_file_name'] = $details['s_video_file_name']; 
				$info['s_video_image']     = $details['s_video_image'];
				$info['s_title'] = $details['s_title'];     
				$info['s_artist'] = $details['s_artist']; 
				$info['s_genre'] = $details['s_genre']; 

				$info['s_description'] = $details['s_description']; 
				$info["i_order"] =  1+$this->my_videos_model->get_i_order($current_album_id);
				$info['dt_created_on'] = get_db_datetime();
				$_ret = $this->my_videos_model->insert_new_video($info);
				
				echo json_encode( array('success'=>true , 'msg'=>'Video copied successfully.') );
		   }
		   
		   else if($TOTAL_VIDEO  == $MAX_VIDEO ){	

			  echo json_encode( array('success'=>false,'msg'=>'Video cannot be uploaded as maximum video upload limit is reached!'));
			  exit;
		   }
	    
	}
    
    
    
}
?>