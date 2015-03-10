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
    
//    private $pagination_per_page =  10;
//    private $comments_pagination_per_page =  2 ;
//    private $people_liked_pagination_per_page =  4 ;
//   
    
    public function __construct()
     {
	 	
        try
        {
            parent::__construct();
            parent::_add_church_css_arr (array('css/church.css'));
			 parent::_add_js_arr(array(
//                'js/ajaxupload.js',
                'js/frontend/wall/wall_photo_upload.js',
                'js/frontend/wall/wall_video_upload.js'));
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
            $_SESSION['logged_church_id'] = $c_id;
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
                if ($message != '' || !empty($imagevalue) || $this->input->post('txt_video_url') != '') {

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

                    ### uplaoding wall videos  ##

                    /* if($url_sent) {
                      if( trim($this->input->post('url')) != '' ) {

                      $new_imagename = md5(rand(1000, 9999).time());

                      $a = @copy($this->input->post('url'), $this->upload_path.$new_imagename);

                      if(!$a) {
                      echo json_encode( array('success'=>'false', 'msg'=>'Invalid photo url.') );
                      exit;
                      }

                      $ext = get_image_extension( $this->upload_path.$new_imagename );

                      if ( $ext=="jpg" || $ext=="jpeg" || $ext=="jpe" || $ext=="gif" || $ext=="bmp" || $ext=="png" || $ext=="tif" ) {

                      rename($this->upload_path.$new_imagename, $this->upload_path.$new_imagename.'.'.$ext);

                      $this->imagename = $new_imagename;

                      $this->upload_image_wall = $this->upload_path.$new_imagename.'.'.$ext;

                      $config = array();

                      $config['source_image'] = $this->upload_image_wall;
                      $config['thumb_marker'] = '-180';
                      $config['crop'] = false;
                      $config['width'] = 180;
                      $config['height'] = 140;
                      $config['within_rectangle'] = true;
                      $config['small_image_resize'] = 'no_resize';

                      resize_exact($config);

                      $config['thumb_marker'] = '-445';
                      $config['crop'] = true;
                      $config['crop_from'] = 'middle';
                      $config['width'] = 445;
                      $config['height'] = 266;

                      resize_exact($config);

                      $config['thumb_marker'] = '-big';
                      $config['crop'] = false;
                      $config['width'] = 800;
                      $config['height'] = 800;
                      $config['within_rectangle'] = true;
                      $config['small_image_resize'] = 'no_resize';

                      resize_exact($config);

                      @unlink($this->upload_image_wall);

                      $json_data['photo'] = $new_imagename.'.'.$ext;
                      }
                      else {
                      @unlink($this->upload_path.$new_imagename);
                      echo json_encode( array('success'=>'false', 'msg'=>'This image type is not supported.') );
                      exit;
                      }
                      }
                      else {
                      // This is a server-side checking for extra safety
                      // and normally javascript will not allow it.
                      echo json_encode( array('success'=>'false', 'msg'=>'URL not sent.') );
                      exit;
                      }
                      } */

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


        $result = $this->church_newsfeed_model->get_newsfeeds_by_church_id($i_church_id, $page, $this->pagination_per_page);
		//pr($result);exit;

        $total_rows = $this->church_newsfeed_model->get_total_newsfeeds_by_church_id($i_church_id);

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
            $width = intval($this->input->post('width')) <= 0 ? '329' : intval($this->input->post('width'));
            $height = intval($this->input->post('height')) <= 0 ? '212' : intval($this->input->post('height'));
            $media_info = $this->data_newsfeed_model->get_by_id($i_media_id);
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
}   // end of controller...

