<?php

/* * *******
 * Author: 
 * Date  : 
 * Modified By: 
 * Modified Date:
 * 
 * Purpose:
 *  Controller For ## Management
 * 
 * @package 
 * @subpackage 
 * 
 * @link InfController.php 
 * @link Base_Controller.php
 * @link model/##.php
 * @link views/##
 */

include(APPPATH . 'controllers/base_controller.php');

class Newsfeed extends Base_controller {

    private $pagination_per_page = 5;

    public function __construct() {
        try {
            parent::__construct();
            #parent::check_login(FALSE);
            $this->upload_tmp_path = BASEPATH . '../uploads/wall_tmp/';
            $this->upload_path = BASEPATH . '../uploads/wall_photos/';
            $this->upload_photo_path = BASEPATH . '../uploads/wall_photos/';
            $this->upload_video_path = BASEPATH . '../uploads/wall_videos/';
            $this->load->model('data_newsfeed_model');
            $this->load->model('newsfeed_comments_model');
            $this->load->helper('wall_helper');
        } catch (Exception $err_obj) {
            
        }
    }

    public function post_own_wall($refresh_list = 0, $photo_sent = 0, $url_sent = 0) {
        try {

            $i_session_user_id = intval(decrypt($this->session->userdata('user_id')));
            $user_id = intval(decrypt($this->input->post('wall_owner_id')));

            if ($user_id == $i_session_user_id && $user_id != '') {


                $this->load->model('users_model');
                $this->load->model('church_newsfeed_model');
                $user_details = $this->users_model->fetch_this($i_session_user_id);

                $insert_date = get_db_datetime();

                $message = nl2br(htmlspecialchars(trim($this->input->post('wall_msg')), ENT_QUOTES, 'utf-8'));

                if ($message != '') {
                    $photo_uploaded = false;
                    /* if($photo_sent) {
                      if( $_FILES['upload_wall_photo_file']['name'] != '' ) {

                      preg_match('/(^.*)\.([^\.]*)$/', $_FILES['upload_wall_photo_file']['name'], $matches);
                      $ext = "";
                      if(count($matches)>0) {
                      $ext = strtolower($matches[2]);
                      $original_name = $matches[1];
                      }
                      else
                      $original_name = 'image';

                      if ( $ext=="jpg" || $ext=="jpeg" || $ext=="jpe" || $ext=="gif" || $ext=="bmp" || $ext=="png" || $ext=="tif" ) {
                      $imagename = createImageName( $original_name );
                      //$imagename = $original_name;

                      if(test_file($imagename.'-big.'.$ext)) {
                      for( $i=0; test_file($imagename.'-'.$i.'-big.'.$ext); $i++ ) {
                      }

                      $new_imagename = $imagename.'-'.$i;
                      }
                      else {
                      $new_imagename = $imagename;
                      }

                      $this->imagename = $new_imagename;

                      $this->upload_image_wall = $this->upload_path.$new_imagename.'.'.$ext;

                      move_uploaded_file($_FILES['upload_wall_photo_file']['tmp_name'], $this->upload_image_wall);

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

                      @unlink($_FILES['upload_wall_photo_file']);

                      $json_data['photo'] = $this->imagename.'.'.$ext;

                      $photo_uploaded = true;
                      }
                      else {
                      @unlink($_FILES['upload_wall_photo_file']);
                      echo json_encode( array('success'=>'false', 'msg'=>'Sont pris en charge les extensions jpg, jpeg, jpe, gif, bmp, png, tif') );
                      exit;
                      }
                      }
                      else {
                      // This is a server-side checking for extra safety
                      // and normally javascript will not allow it.
                      echo json_encode( array('success'=>'false', 'msg'=>'Photo is blank.') );
                      exit;
                      }
                      } */


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


                    $json_data['user_id'] = $user_id;
                    $json_data['message'] = $message;
                    //echo $message; exit;

                    $arr['i_owner_id'] = $user_id;
                    $arr['s_type'] = 'wall_post';
                    $arr['s_ownership'] = 'ownerpost';
                    $arr['data'] = json_encode($json_data);
                    $arr['i_referrence_id'] = 0;
                    $arr['dt_created_on'] = get_db_datetime();
                    $feed_id = $this->data_newsfeed_model->insert($arr);

                    #----Update status in user table s_status_text -----

                    /* $info['s_status_text'] = $message; 
                      $info['dt_status_updated_on'] = get_db_datetime();
                      $i_profile_id = $user_id;
                      $this->users_model->update($info,$i_profile_id); */

                    #-----End-------------------------------------------

                    $arr['s_profile_photo'] = $user_details['s_profile_photo'];
                    $arr['s_profile_name'] = $user_details['s_profile_name'];
                    $arr['i_user_type'] = $user_details['i_user_type'];
                    $arr['s_gender'] = $user_details['s_gender'];


                    if ($refresh_list == 0) {
                        $data['feed'] = $arr;
                        $data['feed']['id'] = $feed_id;
                        $data['feed']['comments'] = array();

                        if ($photo_uploaded) {
                            $feed = base64_encode($this->load->view('newsfeed/my_profile_single_feed.phtml', $data, true));
                        } else {
                            $feed = $this->load->view('newsfeed/my_profile_single_feed.phtml', $data, true);
                        }

                        echo json_encode(array('success' => TRUE, 'feed' => $feed, 'msg' => 'Success!'));
                        exit;
                    } else {
                        ob_start();
                        $this->pagination_ajax(0);

                        if ($photo_uploaded) {
                            $content = base64_encode(ob_get_contents());
                        } else {
                            $content = ob_get_contents();
                        }

                        ob_end_clean();
                        echo json_encode(array('success' => TRUE, 'content' => $content, 'msg' => 'Success!'));
                        exit;
                    }
                } else {
                    echo json_encode(array('success' => FALSE, 'msg' => 'Please enter some text!'));
                }
            } else {

                echo json_encode(array('success' => FALSE, 'msg' => 'Error!'));
            }
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function post_his_wall($refresh_list = 0, $photo_sent = 0, $url_sent = 0) {
        try {
            parent::_add_js_arr(array(
//                'js/ajaxupload.js',
                'js/frontend/wall/wall_photo_upload.js',
                'js/frontend/wall/wall_video_upload.js'
            ));


            $data = $this->data;
            $video_url_messages = '';
            $i_session_user_id = intval(decrypt($this->session->userdata('user_id')));
            $user_id = intval(decrypt($this->input->post('public_wall_owner_id')));

            if (1) {
                $this->load->model('users_model');
                $this->load->model('data_newsfeed_model');
                $user_details = $this->users_model->fetch_this($i_session_user_id);

                $insert_date = get_db_datetime();

                $message = nl2br(htmlspecialchars(trim($this->input->post('wall_msg')), ENT_QUOTES, 'utf-8'));
                if (trim($this->input->post('wall_msg')) == 'Max 500 Characters') {
                    $message = '';
                }


                #pr($this->input->post('photo[]'));
                $imagevalue = $this->move_images_from_temp();
                //pr($imagevalue);
                $is_abusive = check_abusive_words($message);
               // echo $is_abusive;
                if (($message != ''&& $is_abusive == 0) || !empty($imagevalue) || $this->input->post('txt_video_url') != '' ) {

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

                    $arr['i_owner_id'] = $user_id;
                    $arr['s_type'] = 'wall_post';
                    $wall_type = trim($this->input->post('wall_type'));
                    if ($wall_type == 'public_wall') {
                        $json_data['wall_owner_id'] = ($this->input->post('public_profile_id'));
                    }
                    if ($user_id == $i_session_user_id) {
                        $arr['s_ownership'] = 'ownerpost';
                    } else {
                        $arr['s_ownership'] = 'otherpost';
                    }
                     $ip = getenv("REMOTE_ADDR") ; 
                    $arr['data'] = json_encode($json_data);
                    $arr['i_referrence_id'] = 0;
                    $arr['dt_created_on'] = get_db_datetime();
                    $arr['u_ip'] = $ip;

                    $feed_id = $this->data_newsfeed_model->insert($arr);

                    if ($refresh_list == 0) {
                        $data['feed'] = $arr;
                        $data['feed']['id'] = $feed_id;
                        $data['feed']['comments'] = array();
                        $wall_type = trim($this->input->post('wall_type'));
                        if ($photo_uploaded) {
                            if ($wall_type == 'public_wall') {
                                $feed = base64_encode($this->load->view('newsfeed/public_profile_single_feed.phtml', $data, true));
                            } else {
                                $feed = base64_encode($this->load->view('newsfeed/my_profile_single_feed.phtml', $data, true));
                            }
                        } else {
                            if ($wall_type == 'public_wall') {
                                $feed = base64_encode($this->load->view('newsfeed/public_profile_single_feed.phtml', $data, true));
                            } else {
                                $feed = base64_encode($this->load->view('newsfeed/my_profile_single_feed.phtml', $data, true));
                            }
                        }
                        //$is_abusive = check_abusive_words($message);
                        echo json_encode(array('success' => TRUE, 'feed' => $feed, 'msg' => 'Post Published!', 'vid_msg' => ''));

                        //echo json_encode( array('success'=>TRUE,'msg'=>'Post Published!' , 'vid_msg'=>''));
                        exit;
                    } else {/*
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
                      exit; */
                    }
                } else {
                    //echo $message;
                      $is_abusive = check_abusive_words($message);
                    //echo $is_abusive;die();
                    //die($is_abusive);
                    if ($is_abusive > 0) {
                            echo json_encode(array('success' => FALSE, 'feed' => $feed, 'msg' => 'Abusive words are not allowed', 'vid_msg' => ''));
                        }else if($message == '' && $is_abusive  == 0 ) {
                    #echo json_encode( array('success'=>FALSE, 'msg'=>"Please enter some text!", 'vid_msg'=>$video_url_messages) );
                    echo json_encode(array('success' => FALSE, 'msg' => "Please enter some text!", 'vid_msg' => $video_url_messages));
                    exit;
                        }
                }
            } else {

                echo json_encode(array('success' => FALSE, 'msg' => 'Error!', 'vid_msg' => ''));
                exit;
            }
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    ######### added for ajax photo uploader on wall ###############

    function upload_multiple_img_AJAX() {

        preg_match('/(^.*)\.([^\.]*)$/', $_FILES['uploadfile']['name'], $matches);
        $ext = "";
        if (count($matches) > 0) {
            $ext = strtolower($matches[2]);
            $original_name = $matches[1];
        } else
            $original_name = 'webzine_img';


        $imagename = createImageName($original_name);
        $imagename = $imagename . md5(rand(10, 99) * time());

        $chk_imagename = $imagename;
        if (test_file($this->upload_tmp_path . $chk_imagename . '.' . $ext)) {
            for ($i = 0; test_file($this->upload_tmp_path . $imagename . '-' . $i . '.' . $ext); $i++) {
                
            }

            $new_imagename = $imagename . '-' . $i;
        } else {
            $new_imagename = $imagename;
        }


        $this->img_name = $new_imagename;
        $this->upload_img = $this->upload_tmp_path . $new_imagename . '.' . $ext;

        move_uploaded_file($_FILES['uploadfile']['tmp_name'], $this->upload_img);

        //$this->setMemoryForImage($this->upload_img);

        $config = array();


        $config['source_image'] = $this->upload_img;
        $config['thumb_marker'] = '-thumb';
        $config['crop'] = false;
        $config['width'] = 60; //$this->config->item('shop_thumb_img_width');
        $config['height'] = 60; //$this->config->item('shop_thumb_img_height');
        $config['within_rectangle'] = true;

        $config['small_image_resize'] = 'no_resize';
        //print_r($config);exit;
        resize_exact($config);
        unset($config);


        $config['source_image'] = $this->upload_img;
        $config['thumb_marker'] = '-mid';
        $config['crop'] = false;
        $config['width'] = 320;  //$this->config->item('shop_mid_img_width');
        $config['height'] = 212; //$this->config->item('shop_mid_img_height');
        #$config['master_dim'] = getSmallerDimension($this->upload_image, 710, 434);
        $config['within_rectangle'] = true;
        $config['small_image_resize'] = 'bigger';

        resize_exact($config);

        unset($config);

		$config['source_image'] = $this->upload_img;
        $config['thumb_marker'] = '-big';
        $config['crop'] = false;
        $config['width'] = 477;  //$this->config->item('shop_mid_img_width');
        $config['height'] = 244; //$this->config->item('shop_mid_img_height');
        #$config['master_dim'] = getSmallerDimension($this->upload_image, 710, 434);
        $config['within_rectangle'] = true;
        $config['small_image_resize'] = 'bigger';

        resize_exact($config);

        unset($config);

        @unlink($this->upload_img);

        $ORIGINAL_FILENAME = $this->img_name . '.' . $ext;
        $FILENAME = getThumbName($this->img_name . '.' . $ext, 'thumb');

        $response = 'ok|@sep@|' . $FILENAME . '|@sep@|' . $ORIGINAL_FILENAME;

        #return $response;
        echo $response;
    }

    # function to delete temporary webzine image files...

    public function delete_tmp_image_AJAX($s_filename, $s_fileExt, $s_xtraParam = '') {
        try {
            $img_filename = $s_filename . '.' . $s_fileExt;

            # retrieving all versions of images...
            $MID_PIC = getThumbName($img_filename, 'mid');
            $THUMB_PIC = getThumbName($img_filename, 'thumb');

            if ($s_xtraParam == 'orig') {

                if (test_file($this->upload_path . $MID_PIC)) {
                    @unlink($this->upload_path . $MID_PIC);
                    @unlink($this->upload_path . $THUMB_PIC);

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

    public function move_images_from_temp() {
        $arr_data = array();

        // ============================================================================
        //          DEALING WITH IMAGES [START]
        // ============================================================================

        $UPLOADED_PHOTOS_ARR = $this->input->post('photo');

        $SELECTED_MAIN_PIC = trim(str_replace("|@SEP@|", ".", $this->input->post('rdo_main_pic', true)));

        $this->upload_path = $this->upload_path . "/";
        $this->upload_photo_path = $this->upload_photo_path . "/";

        $i = 0;
        #foreach($UPLOADED_PHOTOS_ARR as $key=>$val)
        foreach ($UPLOADED_PHOTOS_ARR as $key => $val) {

            $IS_MAIN_PIC = ( $SELECTED_MAIN_PIC == $val ) ? 1 : 0;

            # 1st, moving all those images from "temp" folder to "webzine_images" folder...
            $MID_PIC = getThumbName($val, 'mid');
            //$MID_PIC = getThumbName($val, 'mid');
            $THUMB_PIC = getThumbName($val, 'thumb');



            # deciding path if "main-image" or not...
            //$IMG_DESTINATION_PATH = ( $IS_MAIN_PIC )? $this->upload_path: $this->upload_photo_path;
            $IMG_DESTINATION_PATH = $this->upload_path;

            #echo $this->upload_tmp_path.$BIG_PIC ."<==>". $IMG_DESTINATION_PATH.$BIG_PIC ."<br />";

            @rename($this->upload_tmp_path . $MID_PIC, $IMG_DESTINATION_PATH . $MID_PIC);
            @rename($this->upload_tmp_path . $THUMB_PIC, $IMG_DESTINATION_PATH . $THUMB_PIC);



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

    public function post_comment($feed_id) {
        $this->load->model('newsfeed_comments_model');

        $this->load->model('users_model');
        $user_id = intval(decrypt($this->session->userdata('user_id')));
        $user_details = $this->users_model->fetch_this($user_id);

        $message = nl2br(htmlspecialchars(trim($this->input->post('message')), ENT_QUOTES, 'utf-8'));
        $_html = '';
        if ($message != '') {
            $ip = getenv("REMOTE_ADDR") ; 
            $arr['i_newsfeed_id'] = $feed_id;
            $arr['i_user_id'] = $user_id;
            $arr['s_contents'] = $message;
            $arr['dt_created_on'] = get_db_datetime();
            $arr['u_ip'] = $ip;

            $this->newsfeed_comments_model->insert($arr);
            $arr['pseudo'] = $user_details['s_profile_name'];
            $data['comment'] = $arr;
            //$data['total_comments'] = $this->newsfeed_comments_model->get_total_by_newsfeed_id($feed_id);
            //$comment = $this->load->view('newsfeed/my_profile_single_feed_comment.phtml', $data, true);

            $_html = '' . "Comments " . " (" . count_comment_link($feed_id) . ")";
            echo json_encode(array('success' => 'true', 'msg' => "Comment posted successfully.", 'html' => $_html));
        } else {
            echo json_encode(array('success' => 'false', 'msg' => "Please enter some text.", 'html' => $_html));
        }
    }

    //POST LIKE UNLIKE
    public function like_unlike() {

        //$user_session_data =$this->user_session_data;  //GETTING STORED SESSION DATA WHILE LOGGED IN

        $liked_user_id = intval(decrypt($this->session->userdata('user_id')));
        $window_id = $this->input->post('window_id');
        $like_or_unlike = $this->input->post('like_val');
        $log_time = get_db_datetime();
        $ip_address = $this->input->server('REMOTE_ADDR');

        if ($like_or_unlike == "Like") {
            $like_unlike_information_array = array("i_newsfeed_id" => $window_id,
                "i_liked_user_id" => $liked_user_id,
                "dt_liked_on" => $log_time);
        } else if ($like_or_unlike == "Unlike") {
            $like_unlike_information_array = array("i_newsfeed_id" => $window_id,
                "i_unliked_user_id" => $liked_user_id,
                "dt_unliked_on" => $log_time);
        }

        $status = 0;
        $response = $this->newsfeed_comments_model->postLikeUnlike($like_unlike_information_array, strtolower($like_or_unlike));


        $_html = '';
        if ($response['value']) {

            $last_id = $response['last_inserted_id'];
            $response_message = "<span class='success_message'>" . $response['message'] . "</span>";
            $status = 1;
            $like_val = like_display($window_id);
            $display_style = $like_val[1];
            $all_user_liked = $like_val[0];

            $dislike_val = dislike_display($window_id);
            $display_style_un = $dislike_val[1];
            $all_user_unliked = $dislike_val[0];

            /*
              $_html ='<div class="comment_box" style="display:'.$display_style.'" id="window_like_block'.$window_id.'">
              &nbsp;<img align="absmiddle" src="images/wall_like.png">&nbsp;<span id="window_like_msg'.$window_id.'">'.$all_user_liked.'</span>
              </div>
              <div class="comment_box" style="display:'.$display_style_un.'" id="window_unlike_block'.$window_id.'">
              &nbsp;<img align="absmiddle" src="images/wall_unlike.png">&nbsp;<span id="window_unlike_msg'.$window_id.'">'.$all_user_unliked.'</span>
              </div>
              '; */

            $_html = '' . "Liked by " . " (" . count_like_link($window_id) . ")";
        } else
            $response_message = "<span class='error_message'>" . $response['message'] . "</span>";


        $json_data = array('status' => $status, 'response_message' => $response_message, 'response_html' => $_html);
        echo json_encode($json_data);
    }

    public function show_share_window($comment_id) {

        if ($this->session->userdata('loggedin') == '') {
            echo "<script>window.location='" . base_url() . "'+window.location.hash</script>";
            exit;
        }

        $data = $this->data;

        $data['title'] = "mes contacts";
        $data['width'] = "650px";
        $data['height'] = "550px";
        $this->load->model('contacts_model');
        $i_logged_used_id = intval(decrypt($this->ses_user['user_id']));
        $contacts = $this->contacts_model->get_by_anyuser($i_logged_used_id);
        $data['arr_contacts_list'] = $contacts;
        $data['i_comment_id'] = intval($comment_id);

        $this->load->view('logue/show_share_window.phtml', $data);
    }

    public function show_share_window_v2($comment_id) {

        if ($this->session->userdata('loggedin') == '') {
            echo "<script>window.location='" . base_url() . "'+window.location.hash</script>";
            exit;
        }

        $data = $this->data;

        $data['title'] = "Partager message sur mon mur";
        $data['width'] = "650px";
        $data['height'] = "550px";

        $i_logged_used_id = intval(decrypt($this->ses_user['user_id']));


        $data['i_comment_id'] = intval($comment_id);

        $this->load->view('logue/show_share_window_v2.phtml', $data);
    }

    public function share_on_wall() {
        try {
            $i_comment_id = intval(new_decrypt($this->input->post('s_comment_id')));
            $this->load->model('data_newsfeed_model');
            $feed = $this->data_newsfeed_model->get_by_id($i_comment_id);
            $data_arr = json_decode($feed['data']);


            $message = nl2br(htmlspecialchars(trim($this->input->post('my_added_comments')), ENT_QUOTES, 'utf-8'));
            //$data_arr->message

            $i_session_user_id = intval(decrypt($this->session->userdata('user_id')));

            $arr_selected_ids = explode(',', $this->input->post('selected_ids'));
            #pr($arr_selected_ids);
            $MSG = 'Error!';
            foreach ($arr_selected_ids as $k => $v) {


                $user_id = intval($v);

                if ($user_id > 0) {
                    $MSG = 'Success!';
                    $this->load->model('users_model');
                    $this->load->model('data_newsfeed_model');
                    $user_details = $this->users_model->fetch_this($i_session_user_id);

                    switch ($feed['type']) {
                        case 'wall_post': {

                                //$message = $data_arr->message;
                                $json_data['user_id'] = $i_session_user_id;
                                $json_data['pseudo'] = $user_details['s_fullname'];
                                $json_data['user_photo'] = $user_details['s_picture_path'];
                                $json_data['message'] = $message;


                                $arr['owner'] = $user_id;
                                $arr['type'] = 'wall_post';
                                if ($user_id == $i_session_user_id) {
                                    $arr['ownership'] = 'ownerpost';
                                } else {
                                    $arr['ownership'] = 'otherpost';
                                }
                                $arr['data'] = json_encode($json_data);
                                $arr['reference_id'] = $i_comment_id;
                                $arr['insert_date'] = get_db_datetime();
                                $feed_id = $this->data_newsfeed_model->insert($arr);
                                unset($arr);
                                break;
                            }
                        case 'friend_with': {

                                $arr['owner'] = $user_id;
                                $arr['type'] = 'friend_with';
                                $arr['ownership'] = 'otherpost';
                                $arr['data'] = $feed['data'];
                                $arr['reference_id'] = 0;
                                $arr['insert_date'] = get_db_datetime();
                                $this->data_newsfeed_model->insert($arr);
                                unset($arr);
                            }
                    }

                    $i_wallpost_count = $user_details['i_wallpost_count'] + 1;
                    $info['i_wallpost_count'] = $i_wallpost_count;
                    $this->users_model->update($info, $i_session_user_id);
                }
            }

            echo json_encode(array('success' => TRUE, 'msg' => $MSG));
            exit;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function delete_post($post_id) {

        $i_post_id = intval($this->input->post('post_id'));
        $current_page = intval($this->input->post('current_page'));

        $this->load->model('data_newsfeed_model');
        $this->data_newsfeed_model->delete_comment_subcomment($i_post_id);

        ob_start();
        $this->post_pagination_ajax($current_page);
        $result_content = ob_get_contents();
        ob_end_clean();



        /* $this->load->model('users_model');
          $user_id = intval(decrypt($this->session->userdata('user_id')));
          $user_details = $this->users_model->fetch_this($user_id);
          ## decrease wallpost counter
          $i_wallpost_count = $user_details['i_wallpost_count'] - 1;
          $info['i_wallpost_count']	=	$i_wallpost_count;
          $this->users_model->update($info,$user_id); */

        echo json_encode(array('success' => 'true', 'content' => $result_content));
    }
	
	 public function update_post_his_wall($refresh_list = 0, $photo_sent = 0, $url_sent = 0) {
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

            if (1) {
                $this->load->model('users_model');
                $this->load->model('data_newsfeed_model');
                $user_details = $this->users_model->fetch_this($i_session_user_id);

                $insert_date = get_db_datetime();

                $message = nl2br(htmlspecialchars(trim($this->input->post('wall_msg')), ENT_QUOTES, 'utf-8'));
                if (trim($this->input->post('wall_msg')) == 'Max 500 Characters') {
                    $message = '';
                }


                #pr($this->input->post('photo[]'));
				$post_id=$this->input->post('post_id');
				$del_img=$this->input->post('del_img'.$post_id);
                $imagevalue = $this->move_images_from_temp();
                //pr($imagevalue);
                if ($message != '' || !empty($imagevalue) || $this->input->post('txt_video_url') != ''|| $del_img != '') {

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

                    $arr['i_owner_id'] = $user_id;
                    $arr['s_type'] = 'wall_post';
                    $wall_type = trim($this->input->post('wall_type'));
                    if ($wall_type == 'public_wall') {
                        $json_data['wall_owner_id'] = ($this->input->post('public_profile_id'));
                    }
                    if ($user_id == $i_session_user_id) {
                        $arr['s_ownership'] = 'ownerpost';
                    } else {
                        $arr['s_ownership'] = 'otherpost';
                    }
                    $arr['data'] = json_encode($json_data);
                    $arr['i_referrence_id'] = 0;
                    $arr['dt_created_on'] = get_db_datetime();
					if(count($json_data['photo'])!='0' || count($json_data['video'])!='0' || $json_data['message']!='')
					{
					
					
                    $feed_id = $this->data_newsfeed_model->update_by_id($post_id,$arr);
					}
					else
					{
					$flag='1';
					}
					
                    if ($refresh_list == 0) {
                        $data['feed'] = $arr;
                        $data['feed']['id'] = $feed_id;
                        $data['feed']['comments'] = array();
                        $wall_type = trim($this->input->post('wall_type'));
                        if ($photo_uploaded) {
                            if ($wall_type == 'public_wall') {
                                $feed = base64_encode($this->load->view('newsfeed/public_profile_single_feed.phtml', $data, true));
                            } else {
                                $feed = base64_encode($this->load->view('newsfeed/my_profile_single_feed.phtml', $data, true));
                            }
                        } else {
                            if ($wall_type == 'public_wall') {
                                $feed = base64_encode($this->load->view('newsfeed/public_profile_single_feed.phtml', $data, true));
                            } else {
                                $feed = base64_encode($this->load->view('newsfeed/my_profile_single_feed.phtml', $data, true));
                            }
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
                    } else {/*
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
                      exit; */
                    }
                } 
				
				else {
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

	public function get_feed_by_id($id)
	{
	$data_arr=get_post_detail_by_id($id);
	$details=json_decode($data_arr['data']);
	return $details;
	//$html= $this->load->view("newsfeed/edit_newsfeed_post_.phtml",$data,TRUE);
	//exit;
	}
}
