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


class Church_wall extends Base_controller
{
    
    private $pagination_per_page =  2;
//    private $comments_pagination_per_page =  2 ;
//    private $people_liked_pagination_per_page =  4 ;
//   
    
    public function __construct()
     {
	 	
        try
        {
            parent::__construct();
            parent::_add_church_css_arr (array('css/church.css','css/wall-slider.css','css/jquery.multiselect.css','css/jquery.multiselect.filter.css','css/jquery-ui-1.8.2.custom.css'));
			 parent::_add_js_arr(array(
                'js/lightbox.js',
//                'js/ajaxupload.js',
                'js/frontend/wall/wall_photo_upload.js',
                'js/frontend/wall/wall_video_upload.js',
				'js/church-wall-slider.js',
				'js/jquery.isotope.js',
				'js/jquery.multiselect.js',
				'js/jquery.multiselect.filter.js'));
               parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers... 
//			$user_id = intval(decrypt($this->session->userdata('user_id')));
//                        parent::check_is_church_admin($user_id);
            $this->load->model('users_model');
			$this->load->model('church_newsfeed_model');
			$this->load->model('user_notifications_model');
            $this->load->model('user_alert_model');
			$this->load->model('landing_page_cms_model');
            $this->load->model('church_new_model');
			$this->i_profile_id = intval(decrypt($this->session->userdata('user_id')));
			//$this->upload_path = BASEPATH . '../uploads/church_logo_image/';
			//$this->upload_cover_path = BASEPATH.'../uploads/church_cover_image/';
                        // $this->load->helper('Imagelibrary_helper');
			$this->pagination_per_page=6;
			$this->upload_path = BASEPATH.'../uploads/church_wall';
			$this->upload_photo_path = BASEPATH.'../uploads/church_wall';
			$this->upload_tmp_path = BASEPATH.'../uploads/wall_tmp/';
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    
    public function index($c_id) 
    {
       //echo $c_id;
        //die('comming soon.........');
        try
        {
          $user_id = intval(decrypt($this->session->userdata('user_id')));
          $posted=array();
          $this->data["posted"]=$posted;/*don't change*/    
          $data = $this->data;      
          parent::_set_title('::: COGTIME Xtian network :::');
          parent::_set_meta_desc('');
          parent::_set_meta_keywords('');
          get_all_church_session($c_id);
          $data['church_arr'] =$this->church_new_model->get_church_info($c_id);
          $data['church_admin'] = $this->church_new_model->get_church_admin_data($c_id);

          ## NEWSFEED ##
          ob_start();
          $this->newsfeed_pagination_show_more($c_id);
          $content = ob_get_contents();
          $content_obj = json_decode($content);
          $data['result_newsfeed_content'] = $content_obj->html;
          ob_end_clean();

          ## END NEWSFEED ##
          $VIEW = "logged/church/church_wall.phtml";
          parent::_render($data, $VIEW);
        }
        
        catch(Exception $err_obj)
        {
           
        } 

    } // end of index   
    
	// posting in wall
	
	public function post_his_church_wall($refresh_list = 0, $photo_sent = 0, $url_sent = 0) {
        try {
            parent::_add_js_arr(array(
//                'js/ajaxupload.js',
                'js/frontend/wall/wall_photo_upload.js',
                'js/frontend/wall/wall_video_upload.js'
            ));
			$this->load->helper('my_utility_helper');
            $data = $this->data;
            $video_url_messages = '';
            $i_session_user_id = intval(decrypt($this->session->userdata('user_id')));
            $user_id = intval(decrypt($this->input->post('public_wall_owner_id')));

            if (1) {
                $this->load->model('users_model');
                $this->load->model('church_newsfeed_model');
                $user_details = $this->users_model->fetch_this($i_session_user_id);

                $insert_date = get_db_datetime();

                $message = nl2br(htmlspecialchars(trim($this->input->post('church_post_message')), ENT_QUOTES, 'utf-8'));
				//echo $message;exit;
                if (trim($this->input->post('church_post_message')) == 'Max 500 Characters') {
                    $message = '';
                }


                //pr($this->input->post('photo'));
                $imagevalue = $this->move_images_from_temp();
                //pr($imagevalue);
                if ($message != '' || !empty($imagevalue) || $this->input->post('txt_video_url') != '' || $this->input->post('txt_audio_url') != '') {

                    ### uplaoding wall photos  ##
                    if (1) {
                        if (!empty($imagevalue)) {

                            foreach ($imagevalue as $key => $val) {
                                $json_data['photo'][$key] = $val['s_photo_name']; //$this->imagename.'.'.$ext;
                                $photo_uploaded = true;
                            }
                        }
                        /* else {
                          // This is a server-side checking for extra safety
                          // and normally javascript will not allow it.
                          echo json_encode( array('success'=>'false', 'msg'=>'Photo is blank.') );
                          exit;
                          } */
                    }
                    ### uplaoding wall photos  ##
                    ### uplaoding wall videos  ##

                    if ($this->input->post('txt_video_url') != '') {

                        try {

                            $this->load->library('embed_video');
                            $config['zend_library_path'] = APPPATH . "libraries/Zend/";
                            $config['video_url'] = trim($this->input->post('txt_video_url'));

                            $this->embed_video->initialize($config);
                            //echo 'video_url='.$this->embed_video->video_url;
                            $this->embed_video->prepare();

                            $this->embed_video->save_thumb($this->upload_video_path, '-bigthumb', 320, 190);
                            $this->video_img_name = $this->embed_video->get_resized_imagename();
                            $json_data['video']['image_name'] = $this->video_img_name;
                            $json_data['video']['url'] = trim($this->input->post('txt_video_url'));
                        } catch (Exception $e) {
                            $video_url_messages = "* Not valid video URL, Video cannot be uploaded!";
                        }
                    }
					if ($this->input->post('txt_audio_url') != '') {

                        try {
                            $json_data['audio']= trim($this->input->post('txt_audio_url'));
                        } catch (Exception $e) {
                            $audio_url_messages = "* Not valid video URL, Video cannot be uploaded!";
                        }
                    }
                    ### uplaoding wall videos  ##

                   

                    $json_data['user_id'] = $i_session_user_id;
                    //$json_data['pseudo']  = $user_details['s_fullname'];
                    //$json_data['user_photo'] = $user_details['s_picture_path'];
                    $json_data['message'] = $message;
                    //pr($json_data);exit;
					$church=get_church_by_admin($i_session_user_id);
                    $arr['i_owner_id'] =$church->ch_id ;
                    $arr['s_type'] = 'wall_post';
                    $wall_type = trim($this->input->post('wall_type'));
                    if ($wall_type == 'public_wall') {
                        $json_data['wall_owner_id'] = ($this->input->post('public_profile_id'));
                    }
                   
                     $ip = getenv("REMOTE_ADDR") ; 
                    $arr['data'] = json_encode($json_data);
                    $arr['dt_created_on'] = get_db_datetime();
                    $arr['u_ip'] = $ip;

                    $feed_id = $this->church_newsfeed_model->insert($arr);
					$meminv=$this->input->post('meminv');
					if($this->input->post('privacy') == 3)
					{
						foreach($meminv as $mem)
						{
							$p['feed_id']=$feed_id;
							$p['privacy']=$this->input->post('privacy');
							$p['posted_by']=$i_session_user_id;
							$p['viewed_by']=$mem;
							$pid=$this->church_newsfeed_model->insert_privacy($p);
						}
					}
					else
					{
						$p['feed_id']=$feed_id;
						$p['privacy']=$this->input->post('privacy');
						$p['posted_by']=$i_session_user_id;
						$pid=$this->church_newsfeed_model->insert_privacy($p);
					}
                    if ($refresh_list == 0) {
                       $data['feed'] = $arr;
                        $data['feed']['id'] = $feed_id;
                        $data['feed']['comments'] = array();
                        //$wall_type = trim($this->input->post('wall_type'));
                        if ($photo_uploaded) {
                            if ($wall_type == 'public_wall') {
                                //$feed = base64_encode($this->load->view('newsfeed/public_profile_single_feed.phtml', $data, true));
                            } else {
                                //$feed = base64_encode($this->load->view('newsfeed/my_profile_single_feed.phtml', $data, true));
                            }
                        } else {
                            if ($wall_type == 'public_wall') {
                              //  $feed = base64_encode($this->load->view('newsfeed/public_profile_single_feed.phtml', $data, true));
                            } else {
                                //$feed = base64_encode($this->load->view('newsfeed/my_profile_single_feed.phtml', $data, true));
                            }
                        }
                        $is_abusive = check_abusive_words($message);
                        if ($is_abusive > 0) {
                            echo json_encode(array('success' => FALSE, 'feed' => $feed, 'msg' => 'Abusive words are not allowed', 'vid_msg' => ''));
                        } else {
                            echo json_encode(array('success' => TRUE, 'feed' => $feed, 'msg' => 'Post Published!', 'vid_msg' => ''));
                        }

                        //echo json_encode( array('success'=>TRUE,'msg'=>'Post Published!' , 'vid_msg'=>''));
                        exit;
                    } else {
                      ob_start();
                      $this->pagination_ajax(0);

                      if($photo_uploaded)
                      {
                      $content = base64_encode(ob_get_contents());
                      }
                      else
                      {
                      $content = ob_get_contents();
                      }

                      ob_end_clean();
                      echo json_encode( array('success'=>TRUE, 'content'=>$content,'msg'=>t('Success!') ));
                      exit; 
                    }
					//echo $feed_id;
					echo json_encode( array('success'=>TRUE, 'content'=>$content,'msg'=>t('Success!') ));
                } else {
                    #echo json_encode( array('success'=>FALSE, 'msg'=>"Please enter some text!", 'vid_msg'=>$video_url_messages) );
                    echo json_encode(array('success' => FALSE, 'msg' => "Please enter some text!", 'vid_msg' => $video_url_messages));
                    exit;
                }
            } else {

                echo json_encode(array('success' => FALSE, 'msg' => 'Error!', 'vid_msg' => ''));
                exit;
            }
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }
    
   public function move_images_from_temp() {
        $arr_data = array();

        // ============================================================================
        //          DEALING WITH IMAGES [START]
        // ============================================================================

        $UPLOADED_PHOTOS_ARR = $this->input->post('photo');
		//pr($UPLOADED_PHOTOS_ARR,1);
        $SELECTED_MAIN_PIC = trim(str_replace("|@SEP@|", ".", $this->input->post('rdo_main_pic', true)));

        $this->upload_path = $this->upload_path . "/";
        $this->upload_photo_path = $this->upload_photo_path . "/";
		//echo  $this->upload_path.'  '.$this->upload_photo_path;exit;
        $i = 0;
        #foreach($UPLOADED_PHOTOS_ARR as $key=>$val)
        foreach ($UPLOADED_PHOTOS_ARR as $key => $val) {

            $IS_MAIN_PIC = ( $SELECTED_MAIN_PIC == $val ) ? 1 : 0;

            # 1st, moving all those images from "temp" folder to "webzine_images" folder...
            $MID_PIC = getThumbName($val, 'mid');
            //$MID_PIC = getThumbName($val, 'mid');
            $THUMB_PIC = getThumbName($val, 'thumb');
			$BIG_PIC = getThumbName($val, 'big');



            # deciding path if "main-image" or not...
            //$IMG_DESTINATION_PATH = ( $IS_MAIN_PIC )? $this->upload_path: $this->upload_photo_path;
            $IMG_DESTINATION_PATH = $this->upload_path;

            #echo $this->upload_tmp_path.$BIG_PIC ."<==>". $IMG_DESTINATION_PATH.$BIG_PIC ."<br />";

            @rename($this->upload_tmp_path . $MID_PIC, $IMG_DESTINATION_PATH . $MID_PIC);
            @rename($this->upload_tmp_path . $THUMB_PIC, $IMG_DESTINATION_PATH . $THUMB_PIC);
			@rename($this->upload_tmp_path . $BIG_PIC, $IMG_DESTINATION_PATH . $BIG_PIC);



            # forming the data array...
            if ($IS_MAIN_PIC) {
                $arr_data[$i]['s_photo_name'] = $val; //$arr_data[$i]['s_mag_thumb_img'] = $val;
                $arr_data[$i]['s_main_img'] = $val;
            } else {
                $arr_data[$i]['s_photo_name'] = $val;
            }

            $i++;
        }

        return $arr_data;
    }
	
	public function newsfeed_pagination_show_more($i_church_id, $page = 0) {

        $cur_page = $page + $this->pagination_per_page;
        $data = $this->data;

		//echo $cur_page;
		//echo $this->pagination_per_page;exit;
		$i_session_user_id = intval(decrypt($this->session->userdata('user_id')));
        $result = $this->church_newsfeed_model->get_newsfeeds_by_church_id($i_church_id,$i_session_user_id, $page, $this->pagination_per_page);
		//pr($result);

        $total_rows = $this->church_newsfeed_model->get_total_newsfeeds_by_church_id($i_church_id,$i_session_user_id);

        $data['result_arr'] = $result;
        $data['no_of_result'] = $total_rows;

        $data['current_page_1'] = $cur_page;
        $data['profile_id'] = $i_church_id;

        $VIEW_FILE = "logged/church/newsfeed/church_newsfeed.phtml";

        if (is_array($result) && count($result)) {
            $content = $this->load->view($VIEW_FILE, $data, true);
        } else {
            $content = '';
        }

        //echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode(array('html' => $content, 'current_page' => $cur_page));
    }

    ## SHOWING WALL VIDEOS ##

    public function get_video() {
        try {

            $i_media_id = intval($this->input->post('media_id'));
            $width = intval($this->input->post('width')) <= 0 ? '447' : intval($this->input->post('width'));
            $height = intval($this->input->post('height')) <= 0 ? '358' : intval($this->input->post('height'));
            $media_info = $this->church_newsfeed_model->get_by_id($i_media_id);
            if (!is_array($media_info) || !count($media_info)) {
                echo json_encode(array('result' => 'error'));
                exit;
            }
            $video_arr = json_decode($media_info['data']);
            /*             * ******************* Get photo details ************************ */
            try {
                $this->load->library('embed_video');
                $config['zend_library_path'] = APPPATH . "libraries/Zend/";
                $config['video_url'] = ($video_arr->video->url);

                $this->embed_video->initialize($config);
                $this->embed_video->prepare();
                $image_source = $this->embed_video->get_player($width, $height);
                //echo $image_source;exit;
            } catch (Exception $e) {
                //$data['video_exists'] = false;
                $image_source = 'This video has been deleted.';
            }
            $result_arr['result'] = 'success';
            $result_arr['s_image_source'] = $image_source;
            $result_arr['i_media_id'] = $i_media_id;
            echo json_encode($result_arr);
        } catch (Exception $err_obj) {
            
        }
    }
 # function to delete temporary webzine image files...

    public function delete_tmp_image_AJAX($s_filename, $s_fileExt, $s_xtraParam = '') {
        try {
            $img_filename = $s_filename . '.' . $s_fileExt;

            # retrieving all versions of images...
            $MID_PIC = getThumbName($img_filename, 'mid');
            $THUMB_PIC = getThumbName($img_filename, 'thumb');
			$BIG_PIC = getThumbName($img_filename, 'big');

            if ($s_xtraParam == 'orig') {

                if (test_file($this->upload_path . $MID_PIC)) {
                    @unlink($this->upload_path . $MID_PIC);
                    @unlink($this->upload_path . $THUMB_PIC);
					@unlink($this->upload_path . $BIG_PIC);

                    $this->shop_model->delete_shop_photo($img_filename);
                    echo 'ok';
                } else {
                    $this->shop_model->delete_shop_photo($img_filename);
                    echo 'ok';
                }
            } else {
                if (test_file($this->upload_tmp_path . $MID_PIC)) {
                    @unlink($this->upload_tmp_path . $MID_PIC);
                    @unlink($this->upload_tmp_path . $THUMB_PIC);


                    echo 'ok';
                } else {
                    echo 'err';
                }
            }
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }
    ##END  SHOWING WALL VIDEOS ##
	//-------------------------------like word post ------------------------------------------------//
	
	
	public function like_word()
	{
		$this->load->helper('wall_helper.php');
		$window_id=$this->input->post('window_id');
		$like_or_unlike=$this->input->post('like_val');
		$liked_user_id = intval(decrypt($this->session->userdata('user_id')));
		$log_time    = get_db_datetime();
		$ip_address  = $this->input->server('REMOTE_ADDR');
		if($like_or_unlike =="Like"){
               $like_unlike_information_array = array( "i_newsfeed_id"=>$window_id,
                                                        "i_liked_user_id"=>$liked_user_id,
                                                        "dt_liked_on"=>$log_time,
                                                       );
           }
              else if($like_or_unlike =="Unlike")
                {
                    $like_unlike_information_array = array( "i_newsfeed_id"=>$window_id,
															"i_liked_user_id"=>$liked_user_id,
															"dt_liked_on"=>$log_time,
														);
                 }

                 $status = 0;
                 $response = $this->church_newsfeed_model->postLikeUnlike($like_unlike_information_array,strtolower($like_or_unlike));
				 
				
				 $_html ='';
                 if($response['value'])
                  {

                $last_id = $response['last_inserted_id'];
                $response_message=  "<span class='success_message'>".$response['message']."</span>";
                $status =1;
                $like_val = like_count_display($window_id,'cg_church_newsfeed_like','i_newsfeed_id');
				//pr($like_val);
                /*$display_style = $like_val[1];
                $all_user_liked = $like_val[0];

                $dislike_val = dislike_display($window_id);
                $display_style_un = $dislike_val[1];
                $all_user_unliked = $dislike_val[0];*/

/*
$_html ='<div class="comment_box" style="display:'.$display_style.'" id="window_like_block'.$window_id.'">
&nbsp;<img align="absmiddle" src="images/wall_like.png">&nbsp;<span id="window_like_msg'.$window_id.'">'.$all_user_liked.'</span>
</div>
<div class="comment_box" style="display:'.$display_style_un.'" id="window_unlike_block'.$window_id.'">
&nbsp;<img align="absmiddle" src="images/wall_unlike.png">&nbsp;<span id="window_unlike_msg'.$window_id.'">'.$all_user_unliked.'</span>
</div>
';*/

$_html = ''."Liked by "." (".count_word_like_link($window_id).")";


                  }
                 else
                      $response_message =  "<span class='error_message'>".$response['message']."</span>";


		   $json_data = array ('status'=>$status,'response_message'=>$response_message,'response_html'=>$_html,'like'=>$like_val);
           echo json_encode($json_data);

	}
	
//-----------------------------------word comment
	public function post_comment($feed_id) {

        $this->load->model('users_model');
        $user_id = intval(decrypt($this->session->userdata('user_id')));
        $user_details = $this->users_model->fetch_this($user_id);

        $message = nl2br(htmlspecialchars(trim($this->input->post('message')), ENT_QUOTES, 'utf-8'));
        $_html = '';
        if ($message != '') {
            $ip = getenv("REMOTE_ADDR") ; 
            $arr['i_feed_id'] = $feed_id;
            $arr['i_user_id'] = $user_id;
            $arr['s_contents'] = $message;
            $arr['dt_created_on'] = get_db_datetime();
            $arr['u_ip'] = $ip;
			$arr['church_id'] = $_SESSION['logged_church_id'];

            $cid=$this->church_newsfeed_model->insert_comment($arr);
            $arr['pseudo'] = $user_details['s_profile_name'];
            $data['comment'] = $arr;
            //$data['total_comments'] = $this->newsfeed_comments_model->get_total_by_newsfeed_id($feed_id);
            //$comment = $this->load->view('newsfeed/my_profile_single_feed_comment.phtml', $data, true);
			$com_html='<div id="comment_content_div"'.$cid.'"><div class="txt_content01 comments-number-content"> 
					 			<a href="javascript:void(0);"><div class="pro_photo" style="background:url('.get_profile_image_of_user('thumb',$arr['s_profile_photo'],$arr['e_gender']).') no-repeat center;width:48px; height:48px;"></div></a>
									<div class="left-nw-wal">
										  <p class="blue_bold12"><a href="javascript:void(0);">'.get_username_by_id($arr['i_user_id']).'</a></p>
										  <p>'.$arr['s_contents'].'</p>
											 <p class="read-more">Updated on:  '.getShortDateWithTime($arr['dt_created_on'],6).'</p>
									</div>
									<div class="clr"></div>
							  </div></div>';
            $_html = count_feed_comment_link($feed_id,'cg_church_newsfeed_comments','i_feed_id');
            echo json_encode(array('success' => 'true', 'msg' => "Comment posted successfully.", 'html' => $_html,'com'=>$com_html));
        } else {
            echo json_encode(array('success' => 'false', 'msg' => "Please enter some text.", 'html' => $_html));
        }
    }


    public function delete_post($i_newsfeed_id = '') {
        try {
            $ret_ = $this->church_newsfeed_model->delete_by_id($i_newsfeed_id);

            echo json_encode(array('result' => 'success', 'msg' => 'Post has been successfully deleted.'));
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }


    public function update_his_church_wall($refresh_list = 0, $photo_sent = 0, $url_sent = 0) {
        try {
            parent::_add_js_arr(array(
//                'js/ajaxupload.js',
                'js/frontend/wall/wall_photo_upload.js',
                'js/frontend/wall/wall_video_upload.js'
            ));

            $flag='0';
            $data = $this->data;
            $video_url_messages = '';
            $i_session_user_id = intval(decrypt($this->session->userdata('user_id')));
            $user_id = intval(decrypt($this->input->post('public_wall_owner_id')));

            if(1)
            {
                $message = nl2br(htmlspecialchars(trim($this->input->post('wall_msg')), ENT_QUOTES, 'utf-8'));
                if (trim($this->input->post('wall_msg')) == 'Max 500 Characters') {
                    $message = '';
                }
                #pr($this->input->post('photo[]'));
                $post_id=$this->input->post('post_id');
                $del_img=$this->input->post('del_img'.$post_id);
                $imagevalue = $this->move_images_from_temp();
                //pr($imagevalue);
                if ($message != '' || !empty($imagevalue) || $this->input->post('txt_video_url') != ''|| $del_img != '' || $this->input->post('txt_audio_url') != '') {

                    ### uplaoding wall photos  ##
                    if (1) {
                        if (!empty($imagevalue)) {

                            foreach ($imagevalue as $key => $val) {
                                $json_data['photo'][$key] = $val['s_photo_name']; //$this->imagename.'.'.$ext;
                                $photo_uploaded = true;
                            }
                        }
                        /* else {
                          // This is a server-side checking for extra safety
                          // and normally javascript will not allow it.
                          echo json_encode( array('success'=>'false', 'msg'=>'Photo is blank.') );
                          exit;
                          } */


                    }
                    
                    if($del_img != '')
                    {
                        $delete_image=array();
                        $delete_image=explode(",",$del_img);
                        $photo=$this->get_feed_by_id($post_id);
                        $photo_final=$this->get_feed_by_id($post_id);
                        foreach($photo->photo as $key=>$val)
                        {
                            foreach($delete_image as $img)
                            {
                                if($val == $img)
                                {
                                    unset($photo_final->photo[$key]);
                                }
                            }
                        }
                        foreach($photo_final->photo as $val)
                        {
                            $json_data['photo'][]=$val;
                        }
                        //pr($json_data['photo'],1);
                        //pr($photo_final,1);
                        //pr($delete_image,1);
                    }
                    else
                    {
                        $photo=$this->get_feed_by_id($post_id);
                        foreach($photo->photo as $val)
                        {
                            $json_data['photo'][]=$val;
                        }
                    }
                    
                    ### uplaoding wall photos  ##
                    ### uplaoding wall videos  ##

                    if ($this->input->post('txt_video_url') != '') {

                        try {

                            $this->load->library('embed_video');
                            $config['zend_library_path'] = APPPATH . "libraries/Zend/";
                            $config['video_url'] = trim($this->input->post('txt_video_url'));

                            $this->embed_video->initialize($config);
                            //echo 'video_url='.$this->embed_video->video_url;
                            $this->embed_video->prepare();

                            $this->embed_video->save_thumb($this->upload_video_path, '-bigthumb', 320, 190);
                            $this->video_img_name = $this->embed_video->get_resized_imagename();
                            $json_data['video']['image_name'] = $this->video_img_name;
                            $json_data['video']['url'] = trim($this->input->post('txt_video_url'));
                        } catch (Exception $e) {
                            $video_url_messages = "* Not valid video URL, Video cannot be uploaded!";
                        }
                    }

                    if ($this->input->post('txt_audio_url') != '') {

                        try {
                            $json_data['audio']= trim($this->input->post('txt_audio_url'));
                        } catch (Exception $e) {
                            $audio_url_messages = "* Not valid video URL, Video cannot be uploaded!";
                        }
                    }



                    $json_data['message'] = $message;
                    $json_data['user_id'] = $i_session_user_id;

                    $arr['i_owner_id'] = $_SESSION['logged_church_id'];
                    $arr['s_type'] = 'wall_post';
                    $wall_type = trim($this->input->post('wall_type'));
                    
                    if ($user_id == $i_session_user_id) {
                        $arr['s_ownership'] = 'ownerpost';
                    } else {
                        $arr['s_ownership'] = 'otherpost';
                    }
                    $arr['data'] = json_encode($json_data);
                    $arr['i_referrence_id'] = 0;
                    $arr['dt_created_on'] = get_db_datetime();
                    if(count($json_data['photo'])!='0' || count($json_data['video'])!='0'|| count($json_data['audio'])!='0' || $json_data['message']!='')
                    {
                        $feed_id = $this->church_newsfeed_model->update_by_id($post_id,$arr);
                    }
                    else
                    {
                        $flag='1';
                    }
                    $this->church_newsfeed_model->del_privacy($post_id);
					$meminv=$this->input->post('meminv');
                    if($this->input->post('privacy') == 3)
					{
						foreach($meminv as $mem)
						{
							$p['feed_id']=$post_id;
							$p['privacy']=$this->input->post('privacy');
							$p['posted_by']=$i_session_user_id;
							$p['viewed_by']=$mem;
							$pid=$this->church_newsfeed_model->insert_privacy($p);
						}
					}
					else
					{
						$p['feed_id']=$post_id;
						$p['privacy']=$this->input->post('privacy');
						$p['posted_by']=$i_session_user_id;
						$pid=$this->church_newsfeed_model->insert_privacy($p);
					}
                    if ($refresh_list == 0) {
                        $data['feed'] = $arr;
                        $data['feed']['id'] = $feed_id;
                        $data['feed']['comments'] = array();
                        $wall_type = trim($this->input->post('wall_type'));
                        if ($photo_uploaded) {
                            $feed = base64_encode($this->load->view('logged/church/newsfeed/church_single_newsfeed.phtml', $data, true));
                        } else {
                            $feed = base64_encode($this->load->view('logged/church/newsfeed/church_single_newsfeed.phtml', $data, true));
                        }
                        $is_abusive = check_abusive_words($message);
                        if ($is_abusive > 0) {
                            echo json_encode(array('success' => FALSE, 'feed' => $feed, 'msg' => 'Abusive words are not allowed', 'vid_msg' => ''));
                        }
                                    else if($flag == '1'){
                                        echo json_encode(array('success' => false, 'feed' => $feed, 'msg' => 'Empty posts are not allowed!', 'vid_msg' => ''));
                                    }                       
                                    else {
                                        echo json_encode(array('success' => TRUE, 'feed' => $feed, 'msg' => 'Post Edited Successfully!', 'vid_msg' => ''));
                                    }

                                    //echo json_encode( array('success'=>TRUE,'msg'=>'Post Published!' , 'vid_msg'=>''));
                                    exit;
                                } 
                } 
                
                      else {
                          
                          echo json_encode(array('success' => FALSE, 'msg' => "Please enter some text!", 'vid_msg' => $video_url_messages));
                          exit;
                      }
                  } else {

                      echo json_encode(array('success' => FALSE, 'msg' => 'Error!', 'vid_msg' => ''));
                      exit;
                  }
              } catch (Exception $err_obj) {
                  show_error($err_obj->getMessage());
              }
    }

    public function get_feed_by_id($id)
    {
        $data_arr=get_churchpost_detail_by_id($id);
        $details=json_decode($data_arr['data']);
        return $details;
        //$html= $this->load->view("newsfeed/edit_newsfeed_post_.phtml",$data,TRUE);
        //exit;
    }

}   // end of controller...

