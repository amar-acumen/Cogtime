<?php
/*********
* Author: 
* Date  : 
* Modified By: 
* Modified Date:
* 
* Purpose:
* 
* 
* 
*/
include(APPPATH.'controllers/base_controller.php');

class My_audio_album_details extends Base_controller
{
    private $pagination_per_page =  10 ;
	private $album_pagination_per_page =  3 ;
	private $comments_pagination_per_page = 2;
	private $people_liked_pagination_per_page = 10;
	private $upload_path ;
	private $upload_tmp_path;
    
    public function __construct()
     {
        try
        {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
           
		    $this->upload_path = BASEPATH.'../uploads/user_audio_album_photos/';
			$this->upload_path_music_full = BASEPATH.'../uploads/user_audio_files/';
			
			
			$this->load->helper('wall_helper');
			//$this->load->model('user_audios_model');
			$this->load->model('audio_albums_model');
			$this->load->model('media_comments_model');
			
		    # loading reqired model & helpers...
            $this->load->model('users_model');
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    
    public function index($i_album_id= '') 
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
                                        'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',*/
										//'js/uploadify/jquery.uploadify.min.js'
										//'js/uploadify/jquery.uploadify.js'
                                       'uploadify/swfobject.js',
                                        'uploadify/jquery.uploadify.js',
										'js/production/my_audio.js',
										'js/production/audio_helper.js',
										'js/jwplayer/jwplayer.js',
										'js/production/tweet_utilities.js',
                                        ));
                                        
            parent::_add_css_arr( array(/*'css/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css',*/
										  'uploadify/uploadify.css') );
										  
	      
										  
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
				
				$data['current_album_id'] = $i_album_id;				
				
				
				$this->session->set_userdata('search_condition','');
				
				ob_start();
				//$this->photos_ajax_pagination($i_user_id, $i_album_id);
				$data['photo_content'] = ob_get_contents();
				ob_end_clean();
				
			
				ob_start();
				$this->audio_tracks_ajax_pagination($i_album_id);
				$content = ob_get_contents();
				$content_obj = json_decode($content);
				$data['audio_track_content'] = $content_obj->html; 
				ob_end_clean();
				
				ob_start();
				$this->albums_comments_ajax_pagination($i_album_id);
				$content = ob_get_contents();
				$content_obj = json_decode($content); //pr($content_obj);
				$data['comments_list_html'] = $content_obj->html; 
				ob_end_clean();
				
				
			} 
			#pr($data['arr_photos']);
          #pr($data['arr_albums']); exit;
            # view file...
            $VIEW = "logged/audios/my-audio-album-details.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }   
	
	 public function audio_tracks_ajax_pagination($i_media_id , $page=0) 
	  {
		 try
		 {
		    $cur_page = $page + $this->pagination_per_page;
			$i_user_id = intval(decrypt($this->session->userdata('user_id')));
			$data = $this->data;  
			$result = $this->audio_albums_model->get_audios_by_album_id($i_media_id ,$i_user_id,$page,
																$this->pagination_per_page);
			$total_rows = $this->audio_albums_model->get_total_audios_by_album_id($i_media_id);
			//pr($result); 		
					
			$data['result_arr'] = $result;
			$data['no_of_tracks'] = $total_rows;
			$data['tracks_current_page_1'] = $cur_page;
		    $data['i_media_id'] = $i_media_id;
			$VIEW_FILE = "logged/audios/audio-details/load_audio_tracks.phtml";
		
			if( is_array($result) && count($result) ) {
				$content = $this->load->view( $VIEW_FILE , $data, true);
			}
			else {
				$content = '';
			}
			
			//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
			echo json_encode( array('html'=>$content, 'no_of_result'=>$data['no_of_tracks'], 'current_page'=>$data['tracks_current_page_1']) );
			
		} 
        catch(Exception $err_obj)
        {
            
        } 
	
    }
	
	
	
	
	 public function albums_comments_ajax_pagination($i_media_id , $page=0) 
	  {
		 try
		 {
		    $cur_page = $page + $this->comments_pagination_per_page;
			$data = $this->data;  
			$result = $this->media_comments_model->get_by_newsfeed_id($i_media_id , 'audio_album',$page,
																$this->comments_pagination_per_page);
		    $resultCount = count($result);
			$total_rows = $this->media_comments_model->get_total_by_newsfeed_id($i_media_id, 'audio_album');
			//pr($result); 		
					
			$data['result_arr'] = $result;
			$data['no_of_albums_comments'] = $total_rows;
			$data['comment_current_page_1'] = $cur_page;
		    $data['i_media_id'] = $i_media_id;
			$VIEW_FILE = "logged/audios/audio-details/comments/audio_detail_comments_ajax.phtml";
		
			if( is_array($result) && count($result) ) {
				$content = $this->load->view( $VIEW_FILE , $data, true);
			}
			else {
				$content = '';
			}
			
			//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
			echo json_encode( array('html'=>$content, 'no_of_result'=>$data['no_of_albums_comments'], 'current_page'=>$data['comment_current_page_1']) );
			
		} 
        catch(Exception $err_obj)
        {
            
        } 
	
    }
		
	
	## post comments  ##
	
	public function post_comment_ajax($feed_id) 
	{
		$this->load->model('media_comments_model');
		$this->load->model('users_model');
		$user_id = intval(decrypt($this->session->userdata('user_id')));
		$user_details = $this->users_model->fetch_this($user_id);

		$message = nl2br( htmlspecialchars(trim($this->input->post('message')), ENT_QUOTES, 'utf-8') );
        $_html='';
		if($message!='')
		    {
				$arr['i_media_id'] = $feed_id;
				$arr['s_media_type'] = 'audio_album' ;
				$arr['i_user_id'] = $user_id;
				$arr['s_contents'] = $message;
				$arr['dt_created_on'] = get_db_datetime();
				
				$this->media_comments_model->insert($arr);
				$arr['pseudo'] = $user_details['s_profile_name'];
				$data['comment'] = $arr;
                
				$data['i_media_id'] = $feed_id;
				
				  ## feching comments
			  	ob_start();
				$this->albums_comments_ajax_pagination($feed_id);
				$content = ob_get_contents();
				$content_obj = json_decode($content); //pr($content_obj);
				$comments_list_html = $content_obj->html; 
				$no_of_result  = $content_obj->no_of_albums_comments;
				$cur_page = $content_obj->comment_current_page_1;
				ob_end_clean();
				
				
				echo json_encode( array('success'=>'true', 'msg'=>"Comment posted successfully.",'html'=>$comments_list_html , 'no_of_result'=> $no_of_result , 'cur_page'=>$cur_page,'i_media_id'=> $data['i_media_id']) );
			}
			 else
		   {
			  echo json_encode( array('success'=>'false', 'msg'=>"Please enter some text.", 'html'=>$comments_list_html) );
		   }
		
	}
	
	//POST LIKE UNLIKE
        public function like_unlike()
         {

              //$user_session_data =$this->user_session_data;  //GETTING STORED SESSION DATA WHILE LOGGED IN
             
			  $liked_user_id = intval(decrypt($this->session->userdata('user_id')));
              $window_id = $this->input->post('window_id');
              $like_or_unlike = $this->input->post('like_val');
              $log_time    = get_db_datetime();
              $ip_address  = $this->input->server('REMOTE_ADDR');

              if($like_or_unlike =="Like"){
               $like_unlike_information_array = array( "i_media_id"=>$window_id,
                                                            "i_liked_user_id"=>$liked_user_id,
                                                            "dt_liked_on"=>$log_time,
                                                            "s_media_type"=>'audio_album');
              }
              else if($like_or_unlike =="Unlike")
                {
                    $like_unlike_information_array = array( "i_media_id"=>$window_id,
                                                            "i_unliked_user_id"=>$liked_user_id,
                                                            "dt_unliked_on"=>$log_time,
                                                            "s_media_type"=>'audio_album');
                 }

                 $status = 0;
                 $response = $this->media_comments_model->postLikeUnlike($like_unlike_information_array,strtolower($like_or_unlike));
				 
				
				 $_html ='';
                 if($response['value'])
                  {

                $last_id = $response['last_inserted_id'];
                $response_message=  "<span class='success_message'>".$response['message']."</span>";
                $status =1;
                $like_val = like_display($window_id);
                $display_style = $like_val[1];
                $all_user_liked = $like_val[0];

                $dislike_val = dislike_display($window_id);
                $display_style_un = $dislike_val[1];
                $all_user_unliked = $dislike_val[0];



				$_html = ''."Liked by "." (".count_photo_like_link($window_id, 'audio_album').")";


                  }
                 else
                      $response_message =  "<span class='error_message'>".$response['message']."</span>";


		   $json_data = array ('status'=>$status,'response_message'=>$response_message,'response_html'=>$_html);
           echo json_encode($json_data);



         }
	
	## end post comments ##
	
	public function fetch_people_liked_post($i_media_id='')
	{
		try
		  {
			   $data = $this->data;  
			   
			   ob_start();
			   $this->fetch_people_liked_post_ajax($i_media_id);
			   $data['album_liked_list'] = ob_get_contents();
			   ob_end_clean();
			  
			  $VIEW = "logged/audios/comments/album_liked_by_lightbox.phtml";
			  #parent::_render($data, $VIEW); 
			  $html = $this->load->view($VIEW, $data, true);  
			  echo json_encode( array('result'=>success,'html_data'=>$html) );
		   } 
		catch(Exception $err_obj)
			{
			  show_error($err_obj->getMessage());
			} 
			
	} 
	
	  public function fetch_people_liked_post_ajax($i_media_id , $page=0) 
	  {
		 try
		 {
		    $data = $this->data;  
			$result = $this->media_comments_model->get_people_liked_by_newsfeed_id($i_media_id ,'audio_album', $page,
																$this->people_liked_pagination_per_page);
		    $resultCount = count($result);
			$total_rows = $this->media_comments_model->get_total_people_liked_by_newsfeed_id($i_media_id, 'audio_album');
			//pr($result); 		

			$this->load->library('jquery_pagination');
			$config['base_url'] = base_url()."logged/my_audio_album_details/fetch_people_liked_post_ajax/{$i_media_id}";
			$config['total_rows'] = $total_rows;
			$config['per_page'] = $this->people_liked_pagination_per_page;
			$config['uri_segment'] = 5;
			$config['num_links'] = 9;
			$config['page_query_string'] = false;
			
			$config['prev_link'] = '&laquo; Previous';
			$config['next_link'] = 'Next &raquo;';

			$config['cur_tag_open'] = '<li> <span><a href="javascript:void(0)" class="select">';
			$config['cur_tag_close'] = '</a></span></li>';

			$config['next_tag_open'] = '<li><a href="javascript:void(0)">';
			$config['next_tag_close'] = '</a></li>';

			$config['prev_tag_open'] = '<li><a href="javascript:void(0)">';
			$config['prev_tag_close'] = '</a></li>';

			$config['num_tag_open'] = '<li>';
			$config['num_tag_close'] = '</li>';
			
			

			$config['div'] = '#view_album_liked'; /* Here #content is the CSS selector for target DIV */
			#$config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
			#$config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */


			$this->jquery_pagination->initialize($config);
			$data['page_links'] = $this->jquery_pagination->create_links();

			
			$data['result_liked_arr'] = $result;
			$data['no_of_result'] = $total_rows;
			$data['current_page'] = $page;
			$data['total_pages'] = ceil($total_rows/$this->people_liked_pagination_per_page);
		  
			 $p = ($page/$this->people_liked_pagination_per_page);
			 $data['current_loaded_page_no'] =  $p + 1;
			
			# rendering the view file...
			$VIEW_FILE = "logged/audios/comments/album_liked_by_lightbox_ajax.phtml";
			$this->load->view($VIEW_FILE, $data);
			//return $html;
			
		} 
        catch(Exception $err_obj)
        {
            
        } 
	
    }
	
	########### NEW FETCH COMMENTS ON WALL ###########

	public function new_fetch_likes_on_audio_album($i_media_id='')
	{
		try
		  {
			  $data = $this->data; 
			  $html = ''; 
			  $result = $this->media_comments_model->get_people_liked_by_newsfeed_id($i_media_id , 'audio_album');
			  
			  //pr($result);
			  
			  if(count($result)){
				  foreach($result as $key=> $val){
					  
						 $name = $val['s_profile_name'];
						 $profile_image = get_profile_image_of_user('thumb',$val['s_profile_photo'],$val['e_gender']);
						 
						 $profile_link = get_profile_url($val['i_user_id'],$val['s_profile_name']);
						
						$html .= '     <div class="user_div dp-list-user"> 
											<a href="'.$profile_link.'">
											<div class="pro_photo3" style="background:url('.$profile_image.') no-repeat center;width:60px; height:60px;"></div></a> 
											<a href="javascript:void(0);" class="blue_link">'.$name.'</a> 
										</div>
										';
				  }
				  $html .= '<br class="clr" />';
			  }
			  else{
				  $html .= '     <div class="user_div" style="width:475px !important;"> 
										<div style="text-align:center;"><p>No Likes.</p></div>
										</div>
										';
			  }
			  
			 
			  echo json_encode( array('result'=>'success','html_data'=>$html) );
		   } 
			catch(Exception $err_obj)
			{
			  show_error($err_obj->getMessage());
			} 
			
	} 
	
	
}   // end of controller...

