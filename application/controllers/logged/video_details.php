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

class Video_details extends Base_controller
{

   
    private $comments_pagination_per_page= 5;
    private $people_liked_pagination_per_page = 5;

    
    public function __construct()
     {
        try
        {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...
            $this->load->model('media_comments_model');
            $this->load->helper('wall_helper');
            $this->load->model('users_model');
            $this->load->model('my_videos_model');
            
            
            #defining path
            
            $this->upload_path = BASEPATH.'../uploads/user_videos/';
            $this->upload_path_album_photo = BASEPATH.'../uploads/user_videos_album/';
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }

    } // end of constructor
    
    
    
    
    
    public function index($video_id) 
    {
        try
        {
            
            $i_profile_id = intval( decrypt($this->session->userdata('user_id')) );
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
            $this->data["MAIN_MENU_SELECTED"] = 1;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
                                        'js/switch.js',
                                        'js/animate-collapse.js',
                                        'js/lightbox.js',
                                        'js/jquery.dd.js',
                                        'js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',
                                        'js/jquery.form.js',
                                        'js/jquery/JSON/json2.js',
                                        'js/frontend/logged/my_videos.js',
                                        'js/frontend/logged/organize_my_videos.js',
										'js/frontend/logged/tweets/tweet_utilities.js',
                                        'js/frontend/logged/video_helper.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                        'css/dd.css') );
            # adjusting header & footer sections [End]...

            $data['page_view_type'] = 'myaccount';
            $arr_profile_info = $this->users_model->fetch_this($i_profile_id);
            $data['arr_profile_info'] = $arr_profile_info;
            
               
            // get full video details
            $data['video_info'] = $this->my_videos_model->get_video_info_by_id($video_id);
            
            // to show all album names in select box while uploading a video
            $data['album_names'] = $this->upload_video_select_album_name();   
                    
            $data['i_media_id'] = $video_id;
            ### for all comments  ###
            ob_start();
            $this->comments_ajax_pagination($video_id);
            $content = ob_get_contents();
            $content_obj = json_decode($content);
           $data['comments_result_arr'] = $content_obj->html; 
            
            ob_end_clean();           
            ### end all comments  ###
            
            # view file...
            $VIEW = "logged/videos/video_detail/video_detail.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    } // end of index
    
    
       //------------------------------------------------ fetch album name -----------------------------------------------
    function upload_video_select_album_name()       //called from index
    {
        $user_id = intval( decrypt($this->session->userdata('user_id')) );
        $names = $this->my_videos_model->get_all_video_album_name($user_id);
       
        return $names;
        
    }
    
    
    
    //=============================================== play media file ===========================================
    public function get_media()
       {
          try
              {
              
                    $i_media_id = intval($this->input->post('media_id'));
                    $width = intval($this->input->post('width'))<=0?'472':intval($this->input->post('width'));
                    $height = intval($this->input->post('height'))<=0?'378':intval($this->input->post('height'));
                    
                    
                    $media_info = $this->my_videos_model->get_video_by_id($i_media_id);
    
                    if($media_info == '') {
                        echo json_encode( array('result'=>'error') );
                        exit;
                    }
            
    
            /* ******************** Get photo details ************************ */
            
        
            
            try {
                    $this->load->library('embed_video');
                    $config['zend_library_path'] = APPPATH ."libraries/Zend/";
                    $config['video_url'] =  $media_info;
                    
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
    
            echo json_encode($result_arr);
                
                
                
              } 
          catch(Exception $err_obj)
              {
                
              } 
        
       }
    //=============================================== end play media file ===========================================
    
    
    
    
    
    
    
    
    //=============================================== like & comments =================================================
     
    ## post comments  ##
    
    public function post_comment($feed_id) 
    {
        //echo $feed_id; exit;
        $this->load->model('media_comments_model');
		$this->load->model('user_alert_model');
        $this->load->model('users_model');
        $user_id = intval(decrypt($this->session->userdata('user_id')));
        $user_details = $this->users_model->fetch_this($user_id);

        $message = nl2br( htmlspecialchars(trim($this->input->post('message')), ENT_QUOTES, 'utf-8') );

        $_html='';
        if($message!='')
            {
                $arr['i_media_id'] = $feed_id;
                $arr['s_media_type'] = 'video' ;
                $arr['i_user_id'] = $user_id;
                $arr['s_contents'] = $message;
                $arr['dt_created_on'] = get_db_datetime();
                
                $this->media_comments_model->insert($arr);
                $arr['pseudo'] = $user_details['s_profile_name'];
                $data['comment'] = $arr;
               $owner_id=get_video_ownerID_by_id($feed_id);
				if($owner_id != $user_id)
				{
				$email_opt = $this->user_alert_model->check_option_email_user_id($user_id);
						if($email_opt['e_video_comments_received'] == 'Y'){
						
						$mail_arr['i_requester_id'] =get_username_by_id( $user_id);
						$mail_arr['i_accepter_id'] =  get_username_by_id($owner_id);
						$mail_arr['s_type'] = 'e_video_comments_received';
						$mail_arr['s_title']=get_video_title($feed_id);
						//$mail_arr['s_url']=get_photo_detail_url($feed_id);
						$mail_id=get_useremail_by_id($owner_id);
						 $this->load->library('email');
						 $this->load->helper('html');
					$email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
					  $body=$this->load->view('logged/my-mail-template.phtml',$mail_arr,TRUE);
						$this->email->initialize($email_setting);
						$this->email->from('admin@cogtime.com', 'Team Cogtime');
					$this->email->to("$mail_id");
						//$this->email->bcc("$mailids");
				//$this->email->cc('arif.zisu@gmail.com');
					//$this->email->bcc('trisha.paul@hotmail.com');

					$this->email->subject($mail_arr["i_requester_id"].' commented on your video.');
					$this->email->message("$body");

					$this->email->send();
					}
				}
                
                ob_start();
                $this->comments_ajax_pagination($feed_id);
                $content = ob_get_contents();
                $content_obj = json_decode($content);
                $_html = $content_obj->html;
                $current_page = $content_obj->current_page;
                $comments_pagination_per_page = $this->comments_pagination_per_page;
                $no_of_result = $content_obj->no_of_result;
                ob_end_clean();    
                echo json_encode( array('success'=>'true', 'msg'=>"Comment posted successfully.",'html'=>$_html, 'current_page'=>$current_page,'comments_pagination_per_page'=>$comments_pagination_per_page, 'no_of_result'=>$no_of_result) );
            }
             else
           {
              echo json_encode( array('success'=>'false', 'msg'=>"Please enter some text.", 'html'=>$_html) );
           }
        
    }
    
    ## end post comments ##
    
    
    ## POST LIKE UNLIKE
    
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
                                                            "s_media_type"=>'video');
              }
              else if($like_or_unlike =="Unlike")
                {
                    $like_unlike_information_array = array( "i_media_id"=>$window_id,
                                                            "i_unliked_user_id"=>$liked_user_id,
                                                            "dt_unliked_on"=>$log_time,
                                                            "s_media_type"=>'video');
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


				$_html = ''."Liked by "." (".count_photo_like_link($window_id,'video').")";


                  }
                 else
                      $response_message =  "<span class='error_message'>".$response['message']."</span>";


           $json_data = array ('status'=>$status,'response_message'=>$response_message,'response_html'=>$_html);
           echo json_encode($json_data);



         }
    
    public function fetch_comment_on_photo($i_media_id='')
    {
        try
          {
             $data = $this->data;  
             
                 ob_start();
                 $this->comments_ajax_pagination($i_media_id);
                 $data['comments_list'] = ob_get_contents();
                 ob_end_clean();
             
              $VIEW = "logged/videos/comments/my_video_view_comments_lightbox.phtml";
              #parent::_render($data, $VIEW); 
              $html = $this->load->view($VIEW, $data, true);  
              echo json_encode( array('result'=>success,'html_data'=>$html) );
           } 
        catch(Exception $err_obj)
            {
              show_error($err_obj->getMessage());
            } 
    
    }
    
     public function comments_ajax_pagination($i_media_id , $page=0) 
      {
         try
         {
			$cur_page = $page +  $this->comments_pagination_per_page;

            $data = $this->data;  
            $result = $this->media_comments_model->get_by_newsfeed_id($i_media_id ,'video', $page,
                                                                $this->comments_pagination_per_page);
            $resultCount = count($result);
            $total_rows = $this->media_comments_model->get_total_by_newsfeed_id($i_media_id,'video');
           

            $data['result_arr'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $cur_page;
            $data['comments_pagination_per_page'] = $this->comments_pagination_per_page;
			$data['i_media_id']= $i_media_id;
           
            # rendering the view file...
            $VIEW_FILE = "logged/videos/video_detail/video_detail_comment_ajax.phtml";
            
              if( is_array($result) && count($result) ) {
                    $content = $this->load->view( $VIEW_FILE , $data, true); 
                }
                else {
                    $content = '';
                }
                
                echo json_encode( array('html'=>$content, 'current_page'=>$cur_page , 'comments_pagination_per_page'=>$data['comments_pagination_per_page'] , 'no_of_result'=>$data['no_of_result']) );
          
            
        } 
        catch(Exception $err_obj)
        {
            
        } 
    
    }
    
    public function fetch_people_liked_post($i_media_id='')
    {
        try
          {
               $data = $this->data;  
               
               ob_start();
               $this->fetch_people_liked_post_ajax($i_media_id);
               $data['people_liked_list'] = ob_get_contents();
               ob_end_clean();
              
              $VIEW = "logged/videos/comments/liked_by_lightbox.phtml";
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
            $result = $this->media_comments_model->get_people_liked_by_newsfeed_id($i_media_id , $page,
                                                                $this->people_liked_pagination_per_page);
            $resultCount = count($result);
            $total_rows = $this->media_comments_model->get_total_people_liked_by_newsfeed_id($i_media_id);
            //pr($result);         

            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."logged/video_details/fetch_people_liked_post_ajax/{$i_media_id}";
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
            
            

            $config['div'] = '#view_people_liked'; /* Here #content is the CSS selector for target DIV */
            #$config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
            #$config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */


            $this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();

            
            $data['result_arr'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $page;
            $data['total_pages'] = ceil($total_rows/$this->people_liked_pagination_per_page);
          
             $p = ($page/$this->people_liked_pagination_per_page);
             $data['current_loaded_page_no'] =  $p + 1;
            
            # rendering the view file...
            $VIEW_FILE = "logged/videos/comments/liked_by_lightbox_ajax.phtml";
            $this->load->view($VIEW_FILE, $data);
            //return $html;
            
        } 
        catch(Exception $err_obj)
        {
            
        } 
    
    }
//============================================= end of like & comments ===========================================
    
}   // end of controller