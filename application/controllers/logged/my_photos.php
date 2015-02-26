<?php

/* * *******
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
//error_reporting(E_ALL && ~E_NOTICE);
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');
ini_set('max_input_time', 300);
ini_set('max_execution_time', 300);
include(APPPATH . 'controllers/base_controller.php');

class My_photos extends Base_controller {

    private $pagination_per_page = 10;     // for photos listing in landung page
    private $album_pagination_per_page = 3;
    private $all_albums_pagination_per_page = 15;
    private $comments_pagination_per_page = 10;
    private $people_liked_pagination_per_page = 10;
    private $upload_path;
    private $upload_tmp_path;

    public function __construct() {
        try {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user

            $this->upload_path = BASEPATH . '../uploads/user_photos/';
            $this->upload_tmp_path = BASEPATH . '../uploads/_tmp/';

            $this->load->helper('wall_helper');
            $this->load->model('users_model');
            $this->load->model('user_photos_model');
            $this->load->model('photo_albums_model');
            $this->load->model('media_comments_model');
            $this->load->library('Upload');
            # loading reqired model & helpers...
            $this->load->model('users_model');
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function index($id = '') {
        try {

            $posted = array();
            $data = $this->data;
            $this->data["MAIN_MENU_SELECTED"] = 1;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');


            parent::_add_js_arr(array(
                'js/ddsmoothmenu.js',
                'js/switch.js', 'js/animate-collapse.js',
                'js/lightbox.js', 'js/jquery.dd.js', 'js/jquery-ui-1.8.2.custom.min.js',
                'js/stepcarousel.js',
                //'js/uploadify/jquery.uploadify.min.js'
                //'js/uploadify/jquery.uploadify.js'
                'uploadify/swfobject.js',
                'js/frontend/logged/tweets/tweet_utilities.js',
                'uploadify/jquery.uploadify.js',
                'js/frontend/logged/my_photo/my_photo.js',
                'js/frontend/logged/my_photo/photo_helper.js'
            ));

            parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
                'css/dd.css',
                'uploadify/uploadify.css'));

            ############################################################
            if (intval($id) <= 0) {
                $i_user_id = intval(decrypt($this->session->userdata('user_id')));
                $data['page_view_type'] = 'myaccount';
            }


            $arr_profile_info = $this->users_model->fetch_this($i_user_id);
            parent::_set_all_photo_album_data($i_user_id);

            #### FETCHING PHOTOS PER USER 
            if (is_array($arr_profile_info) && !empty($arr_profile_info)) {
                $data['arr_profile_info'] = $arr_profile_info;
                $data['arr_photos'] = $this->user_photos_model->get_by_user_id($i_user_id, null, 0, $this->pagination_per_page);

                $data['pagination_per_page'] = $this->pagination_per_page;
                //$data['arr_albums'] = $this->photo_albums_model->get_by_user_id($i_user_id, 0 ,6);
                ###fetching all photo to show in slideshow
                //$data['arr_allphotos'] = $this->user_photos_model->get_by_user_id($i_user_id);

                $data['total_albums'] = $this->photo_albums_model->get_total_by_user_id($i_user_id);
                $data['total_photos'] = $this->user_photos_model->get_total_by_user_id($i_user_id, '');


                $this->session->set_userdata('search_condition', '');

                ob_start();
                $this->photos_ajax_pagination($i_user_id);
                $content = ob_get_contents();
                $content_obj = json_decode($content);
                $data['result_content'] = $content_obj->html;
                ob_end_clean();


                ob_start();
                $this->albums_ajax_pagination($i_user_id);
                $data['album_result_content'] = ob_get_contents();
                ob_end_clean();

                /* 	ob_start();
                  $this->photos_ajax_pagination($i_user_id,$data['page_view_type']);
                  $data['result_content'] = ob_get_contents();
                  ob_end_clean(); */
            }
            #pr($data['arr_photos']);
            #pr($data['arr_albums']); exit;
            # view file...
            // php_info();
            //phpinfo();
//            if (!empty($_FILES)) {
//                pr($_FILES);
//            }
            $data['max_file_upload_size'] = $this->config->item('MAX_UP_FILE_SIZE') * 1024 * 1024;
            $VIEW = "logged/photos/my_photos.phtml";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

    public function albums_ajax_pagination($i_user_id, $page = 0) {

        try {
            //pr($this->uri->segments);
            $data = $this->data;

            $result = $this->photo_albums_model->get_by_user_id($i_user_id, $page, $this->album_pagination_per_page);
            //pr($result);
            $total_rows = $this->photo_albums_model->get_total_by_user_id($i_user_id);

            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url() . 'logged/my_photos/albums_ajax_pagination/' . $i_user_id;
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->album_pagination_per_page;
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

            $config['div'] = '#result_albums_section'; /* Here #content is the CSS selector for target DIV */
            $config['js_bind'] = "showLoading();"; /* if you want to bind extra js code  */
            $config['js_rebind'] = "hideLoading();";


            $this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();
            $data['arr_albums'] = $result;
            $data['total_no_of_albums'] = $total_rows;
            $data['current_page'] = $page;

            $data['total_pages'] = ceil($total_rows / $this->album_pagination_per_page);

            $p = ($page / $this->album_pagination_per_page);
            $data['current_loaded_page_no'] = $p + 1;


            $VIEW_FILE = "logged/photos/load_my_albums_listing_ajax.phtml";
            $this->load->view($VIEW_FILE, $data);
        } catch (Exception $err_obj) {
            
        }
    }

    public function photos_ajax_pagination($i_user_id = '', $page = 0) {
        //echo $page;
        ## seacrh conditions : filter ############
        $WHERE_COND = '';
        if (isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y') :
            $s_photo = get_formatted_string(trim($this->input->post('txt_title')));
            $WHERE_COND .= ($s_photo == '') ? '' : " AND s_title LIKE '%" . $s_photo . "%' ";

            $this->session->set_userdata('search_condition', $WHERE_COND);


        endif;
        $s_where = $this->session->userdata('search_condition');

        $cur_page = $page + $this->pagination_per_page;

        $data = $this->data;

        # echo $page ."--". $this->pagination_per_page ."<br />";
        $result = $this->user_photos_model->get_by_user_id($i_user_id, $s_where, intval($page), $this->pagination_per_page);
        //echo $this->db->last_query();
        $total_rows = $this->user_photos_model->get_total_by_user_id($i_user_id, $s_where);
        //pr($result);
        //--- for check whether more videos are there or not
        $view_more = true;
        $rest_counter = $total_rows - $page;
        if ($rest_counter <= $this->pagination_per_page)
            $view_more = false;


        //--------- end check



        $data['arr_photos'] = $result;
        $data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
        $data['profile_id'] = $i_user_id;

        $VIEW_FILE = "logged/photos/load_my_photos_listing_ajax.phtml";

        if (is_array($result) && count($result)) {
            $content = $this->load->view($VIEW_FILE, $data, true);
        } else {
            $content = '';
        }

        //echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode(array('html' => $content, 'current_page' => $cur_page, 'view_more' => $view_more, 'no_of_result' => $data['no_of_result']));
    }

    /* public function photos_ajax_pagination_1($i_user_id,$page_view_type='public',$page=0) 
      {

      try
      {
      $data = $this->data;
      ## seacrh conditions : filter ############
      $WHERE_COND = '';
      if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) :
      $s_photo = get_formatted_string(trim($this->input->post('txt_title')));
      $WHERE_COND .= ($s_photo=='')?'':" AND s_photo LIKE '%".$s_photo."%' ";

      $this->session->set_userdata('search_condition',$WHERE_COND);


      endif;
      $s_where = $this->session->userdata('search_condition');


      $i_user_id = intval($i_user_id)<=0?intval(decrypt($this->session->userdata('user_id'))):intval($i_user_id);
      $data['page_view_type'] = $page_view_type;


      $result = $this->user_photos_model->get_by_user_id($i_user_id, $s_where, intval($page), $this->pagination_per_page);
      $total_rows = $this->user_photos_model->get_total_by_user_id($i_user_id, $s_where);

      $this->load->library('jquery_pagination');
      $config['base_url'] = base_url().'logged/my_photos/photos_ajax_pagination/'.$i_user_id."/".$page_view_type."/";
      $config['total_rows'] = $total_rows;
      $config['per_page'] = $this->pagination_per_page;
      $config['uri_segment'] = 6;
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

      $config['div'] = '#result_div'; /* Here #content is the CSS selector for target DIV
      $config['js_bind'] = "showLoading();"; /* if you want to bind extra js code
      $config['js_rebind'] = "hideLoading();";


      $this->jquery_pagination->initialize($config);
      $data['page_links'] = $this->jquery_pagination->create_links();
      $data['arr_photos'] = $result;
      $data['no_of_result'] = $total_rows;
      $data['current_page'] = $page;

      $data['total_pages'] = ceil($total_rows/$this->pagination_per_page);

      $p = ($page/$this->pagination_per_page);
      $data['current_loaded_page_no'] =  $p + 1;


      $VIEW_FILE = "logged/photos/load_my_photos_listing_ajax.phtml";
      $this->load->view( $VIEW_FILE , $data);
      }
      catch(Exception $err_obj)
      {

      }

      }
     */

    //=====================================----- all photo album ----=================================================


    public function view_all_photo_albums() {
        try {
            $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
            $posted = array();
            $this->data["posted"] = $posted; /* don't change */
            $data = $this->data;
            $this->data["MAIN_MENU_SELECTED"] = 1;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');


            parent::_add_js_arr(array(
                'js/ddsmoothmenu.js',
                'js/switch.js', 'js/animate-collapse.js',
                'js/lightbox.js', 'js/jquery.dd.js', 'js/jquery-ui-1.8.2.custom.min.js',
                'js/stepcarousel.js',
                //'js/uploadify/jquery.uploadify.min.js'
                //'js/uploadify/jquery.uploadify.js'
                'uploadify/swfobject.js',
                'uploadify/jquery.uploadify.js',
                'js/frontend/logged/tweets/tweet_utilities.js',
                'js/frontend/logged/my_photo/my_photo.js',
                'js/frontend/logged/my_photo/photo_helper.js'
            ));

            parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
                'css/dd.css'));
            # adjusting header & footer sections [End]...

            $data['page_view_type'] = 'myaccount';
            $arr_profile_info = $this->users_model->fetch_this($i_profile_id);
            $data['arr_profile_info'] = $arr_profile_info;


            // setting blank session 
            $this->session->set_userdata('search_condition', '');


            ### for all albums  ###
            ob_start();
            $this->all_photo_album_ajax();
            $data['result_arr'] = ob_get_contents();
            ob_end_clean();
            ### end all albums  ###
            # view file...
            $VIEW = "logged/photos/my_all_photo_albums.phtml";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

    public function all_photo_album_ajax($page = 0) {

        try {

            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
            //pr($this->uri->segments);
            $data = $this->data;

            $result = $this->photo_albums_model->get_by_user_id($i_user_id, $page, $this->all_albums_pagination_per_page);
            //pr($result);
            $total_rows = $this->photo_albums_model->get_total_by_user_id($i_user_id);

            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url() . 'logged/my_photos/all_photo_album_ajax';
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->all_albums_pagination_per_page;
            $config['uri_segment'] = 4;
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

            $config['div'] = '#result_albums_section'; /* Here #content is the CSS selector for target DIV */
            $config['js_bind'] = "showLoading();"; /* if you want to bind extra js code  */
            $config['js_rebind'] = "hideLoading();";


            $this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();
            $data['arr_albums'] = $result;
            $data['total_no_of_albums'] = $total_rows;
            $data['current_page'] = $page;

            $data['total_pages'] = ceil($total_rows / $this->all_albums_pagination_per_page);

            $p = ($page / $this->all_albums_pagination_per_page);
            $data['current_loaded_page_no'] = $p + 1;


            $VIEW_FILE = "logged/photos/load_my_all_albums_listing_ajax.phtml";
            $this->load->view($VIEW_FILE, $data);
        } catch (Exception $err_obj) {
            
        }
    }

    //=====================================----- /all photo album -----=================================================


    public function add_photo() {
        try {

            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by non logged in user
            $arr_messages = array();
            # error message trapping...
            if (trim($this->input->post('txt_title')) == '') {
                $arr_messages['title'] = "* Required Field.";
            }

            if ($_FILES['s_photo']['name'] == '') {
                $arr_messages['photo'] = "* Required Field.";
            }

            if (trim($this->input->post('select_album1')) == '-1') {
                $arr_messages['album1'] = "* Required Field.";
            }

            if (trim($this->input->post('select_album1')) == 'new_album' && trim($this->input->post('txt_album_name')) == '') {

                $arr_messages['album_name'] = "* Required Field.";
            }

            if (isset($_FILES['s_photo']['name']) && $_FILES['s_photo']['name'] != '') {
                preg_match('/(^.*)\.([^\.]*)$/', $_FILES['s_photo']['name'], $matches);
                $ext = "";
                if (count($matches) > 0) {
                    $ext = $matches[2];
                    $original_name = $matches[1];
                } else
                    $original_name = 'photo';


                if (!in_array($ext, $this->config->item('VALID_IMAGE_EXT'))) {
                    $arr_messages['photo'] = "supported extensions are " . implode(' , ', $this->config->item('VALID_IMAGE_EXT'));
                }
//                else if ($_FILES['s_photo']['size'] > $this->config->item('MAX_UP_FILE_SIZE') * 1024 * 1024) {
//                    $arr_messages["photo"] = "Maximum file upload size is " . $this->config->item('MAX_UP_FILE_SIZE') . " MB.";
//                }
            }


            if (count($arr_messages) == 0) {

                $info = array();

                if ($this->input->post('txt_album_name') != '' && $this->input->post('select_album1') == 'new_album') {
                    $album_info = array();
                    $album_info['i_user_id'] = intval(decrypt($this->session->userdata('user_id')));
                    $album_info['s_name'] = get_formatted_string($this->input->post('txt_album_name'));
                    $album_info['dt_created_on'] = get_db_datetime();
                    $album_ret = $this->photo_albums_model->insert($album_info);

                    ## storing new album id
                    $info['i_photo_album_id'] = $album_ret;
                } else {

                    $info['i_photo_album_id'] = intval($this->input->post('select_album1'));
                }

                $info['i_user_id'] = intval(decrypt($this->session->userdata('user_id')));
                $info['s_title'] = get_formatted_string($this->input->post('txt_title'));
                $info['s_description'] = get_formatted_string($this->input->post('txt_add_desc'));
                $info['s_photo'] = $this->_upload_photo();
                $info["i_order"] = 1 + $this->photo_albums_model->get_i_order($info['i_photo_album_id']);
                $info['dt_created_on'] = get_db_datetime();
                $_ret = $this->user_photos_model->insert($info);
                $i_total = $this->user_photos_model->get_total_by_user_id($info['i_user_id']);



                echo json_encode(array('success' => true, 'arr_messages' => $arr_messages, 'msg' => 'Photo Uploaded Successfully.'));
            } else {
                echo json_encode(array('success' => false, 'arr_messages' => $arr_messages, 'msg' => t('Error!')));
            }
        } catch (Exception $err_obj) {
            
        }
    }

    function edit_twt_bg_img() {
        try {

            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by non logged in user
            $arr_messages = array();

            if ($_FILES['s_photo']['name'] == '') {
                $arr_messages['photo'] = "* Required Field.";
            }

            if (isset($_FILES['s_photo']['name']) && $_FILES['s_photo']['name'] != '') {
                preg_match('/(^.*)\.([^\.]*)$/', $_FILES['s_photo']['name'], $matches);
                $ext = "";
                if (count($matches) > 0) {
                    $ext = $matches[2];
                    $original_name = $matches[1];
                } else
                    $original_name = 'photo';


                if (!in_array($ext, $this->config->item('VALID_IMAGE_EXT'))) {
                    $arr_messages['photo'] = "supported extensions are " . implode(' , ', $this->config->item('VALID_IMAGE_EXT'));
                } else if ($_FILES['s_photo']['size'] > $this->config->item('MAX_UP_FILE_SIZE') * 1024 * 1024) {
                    $arr_messages["photo"] = "Maximum file upload size is " . $this->config->item('MAX_UP_FILE_SIZE') . " MB.";
                }
            }
            if (count($arr_messages) == 0) {
                $info = array();
                $user_id = intval(decrypt($this->session->userdata('user_id')));
                $photo_name = $this->_upload_tweet_photo();
                $info['s_tweet_bg_img'] = $photo_name;
                $i_ret = update_tweet_bg_img_by_id($info, $user_id);
                //echo json_encode(array('success' => true, 'arr_messages' => $arr_messages, 'msg' => 'Photo Uploaded Successfully.'));
                if ($i_ret) {
                    echo json_encode(array('success' => true, 'arr_messages' => $arr_messages, 'msg' => 'Image Upldated Successfully.'));
                } else {
                    echo json_encode(array('success' => false, 'arr_messages' => $arr_messages, 'msg' => 'Some error occurred'));
                }
            } else {
                echo json_encode(array('success' => false, 'arr_messages' => $arr_messages, 'msg' => t('Error!')));
            }
        } catch (Exception $err_obj) {
            
        }
    }

    public function _upload_tweet_photo($prev_img = '') {
        $fileElementName = 's_photo';
        if (!empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] != '') {
            preg_match('/(^.*)\.([^\.]*)$/', $_FILES[$fileElementName]['name'], $matches);
            $ext = "";
            if (count($matches) > 0) {
                $ext = strtolower($matches[2]);
                $original_name = $matches[1];
            } else
                $original_name = 'image';

            /* new code start */
            //pr($_FILES);

            $img_name = $_FILES['s_photo']['name'];
            $img_name_arr = explode(".", $img_name);
            //pr($img_name_arr);
            $ext = $img_name_arr[1];
            $db_img_name = $img_name_arr[0] . '_' . time() . '.' . $ext;
            $new_img_name = $img_name_arr[0] . '_' . time() . '-big.' . $ext;
            $new_thumb_name = $img_name_arr[0] . '_' . time() . '-mini_thumb.' . $ext;
            $medium_thumb_name = $img_name_arr[0] . '_' . time() . '-thumb.' . $ext;
            //exit();
            $file_array = array(
                'name' => $new_img_name,
                'type' => $_FILES['s_photo']['type'],
                'tmp_name' => $_FILES['s_photo']['tmp_name'],
                'error' => $_FILES['s_photo']['error'],
                'size' => $_FILES['s_photo']['size'],
            );
            $thumb_file_array = array(
                'name' => $new_thumb_name,
                'type' => $_FILES['s_photo']['type'],
                'tmp_name' => $_FILES['s_photo']['tmp_name'],
                'error' => $_FILES['s_photo']['error'],
                'size' => $_FILES['s_photo']['size'],
            );
            $med_thumb_arry = array(
                'name' => $medium_thumb_name,
                'type' => $_FILES['s_photo']['type'],
                'tmp_name' => $_FILES['s_photo']['tmp_name'],
                'error' => $_FILES['s_photo']['error'],
                'size' => $_FILES['s_photo']['size'],
            );
            $handle = new Upload($file_array);
            $handle->image_resize = true;
            $handle->image_x = 800;
            $handle->image_y = 536;
            $handle->Process($this->upload_path);


            $handle = new Upload($med_thumb_arry);
            $handle->image_resize = true;
            $handle->image_x = 400;
            $handle->image_y = 100;
            $handle->Process($this->upload_path);

            $handle = new Upload($thumb_file_array);
            $handle->image_resize = true;
            $handle->image_x = 110;
            $handle->image_y = 60;
            $handle->Process($this->upload_path);

            return $db_img_name;
            /* new code end */
        } else {
            return $prev_img; // Unchaged previous image
        }
    }

    public function add_multi_photo() {
        try {

            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by non logged in user
            $arr_messages = array();
            # error message trapping...
            $flash_uploaded_pic = array();
            $flash_uploaded_pic = $this->input->post('flash_uploaded_file');


            if (!is_array($flash_uploaded_pic)) {
                $arr_messages['mphoto'] = "* Required Field.";
            }

            if (trim($this->input->post('select_malbum1')) == '-1') {
                $arr_messages['malbum1'] = "* Required Field.";
            }

            if (trim($this->input->post('select_malbum1')) == 'new_album' && trim($this->input->post('txt_malbum_name')) == '') {

                $arr_messages['malbum_name'] = "* Required Field.";
            }

            if (count($flash_uploaded_pic) > 0) {

                foreach ($flash_uploaded_pic as $val) {

                    $tmp_file_ = BASEPATH . '../uploads/_tmp/' . $val; // getting size of file.
                    $flash_uploaded_pic_size = round((filesize($tmp_file_)), 2);

                    preg_match('/(^.*)\.([^\.]*)$/', $val, $matches);
                    $ext = "";
                    if (count($matches) > 0) {
                        $ext = $matches[2];
                        $original_name = $matches[1];
                    } else
                        $original_name = 'photo';


                    if (!in_array($ext, $this->config->item('VALID_IMAGE_EXT'))) {
                        $arr_messages['mphoto'] = "supported extensions are "
                                . implode(' , ', $this->config->item('VALID_IMAGE_EXT'));
                    } else if ($flash_uploaded_pic_size > $this->config->item('MAX_UP_FILE_SIZE') * 1024 * 1024) {
                        $arr_messages["mphoto"] = "Maximum file upload size is "
                                . $this->config->item('MAX_UP_FILE_SIZE') . " MB.";
                    }
                }
            }

            //pr($arr_messages);
            if (count($arr_messages) == 0) {

                $info = array();

                if (count($flash_uploaded_pic) > 0) {
                    $arr_photo_data = $this->move_temp_images_AJAX($flash_uploaded_pic);
                    //pr($arr_photo_data); exit;
                }

                if ($this->input->post('txt_malbum_name') != '' && $this->input->post('select_malbum1') == 'new_album') {
                    $album_info = array();
                    $album_info['i_user_id'] = intval(decrypt($this->session->userdata('user_id')));
                    $album_info['s_name'] = get_formatted_string($this->input->post('txt_malbum_name'));
                    $album_info['dt_created_on'] = get_db_datetime();
                    $album_ret = $this->photo_albums_model->insert($album_info);

                    ## storing new album id
                    $i_photo_album_id = $album_ret;
                } else {

                    $i_photo_album_id = intval($this->input->post('select_malbum1'));
                }

                if (count($flash_uploaded_pic) > 0) {
                    for ($i = 0; $i < count($arr_photo_data); $i++) {
                        $info['i_user_id'] = intval(decrypt($this->session->userdata('user_id')));
                        $title_arr = explode('.', $arr_photo_data[$i]); #pr($title_arr);
                        $info['s_title'] = $title_arr[0]; /// assigning image name as title
                        $info['s_description'] = '';
                        $info['i_photo_album_id'] = $i_photo_album_id;
                        $info['s_photo'] = $arr_photo_data[$i];
                        $info["i_order"] = 1 + $this->photo_albums_model->get_i_order($i_photo_album_id);
                        $info['dt_created_on'] = get_db_datetime();
                        $_ret = $this->user_photos_model->insert($info); #echo $this->db->last_query();
                        $i_total = $this->user_photos_model->get_total_by_user_id($info['i_user_id']);
                    }
                }



                echo json_encode(array('success' => true, 'arr_messages' => $arr_messages, 'msg' => 'Photo Uploaded Successfully.'));
            } else {
                echo json_encode(array('success' => false, 'arr_messages' => $arr_messages, 'msg' => t('Error!'),));
            }
        } catch (Exception $err_obj) {
            
        }
    }

    public function _upload_photo($prev_img = '') {
        parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by non logged in user
        #pr($_FILES);
        $fileElementName = 's_photo';
        if (!empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] != '') {
            preg_match('/(^.*)\.([^\.]*)$/', $_FILES[$fileElementName]['name'], $matches);
            $ext = "";
            if (count($matches) > 0) {
                $ext = strtolower($matches[2]);
                $original_name = $matches[1];
            } else
                $original_name = 'image';


            $imagename = createImageName($original_name);

            if (test_file($this->upload_path . $imagename . '-thumb.' . $ext)) {
                for ($i = 0; test_file($this->upload_path . $imagename . '-' . $i . '-thumb.' . $ext); $i++) {
                    
                }

                $new_imagename = $imagename . '-' . $i;
            } else {
                $new_imagename = $imagename;
            }

            $this->imagename = $new_imagename;

            $this->upload_image = $this->upload_path . $new_imagename . '.' . $ext;
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
            $config['within_rectangle'] = true;
            $config['small_image_resize'] = 'no_resize';
            resize_exact($config);
            unset($config);

            $config = array();
            $config['source_image'] = $this->upload_image;
            $config['thumb_marker'] = '-main';
            #$config['crop'] = false;
            $config['width'] = 400;
            $config['height'] = 327;
            $config['within_rectangle'] = true;
            $config['small_image_resize'] = 'no_resize';
            resize_exact($config);
            unset($config);

            $config = array();
            $config['source_image'] = $this->upload_image;
            $config['thumb_marker'] = '-big';
            #$config['crop'] = false;
            $config['width'] = 800;
            $config['height'] = 536;
            $config['within_rectangle'] = true;
            $config['small_image_resize'] = 'no_resize';
            resize_exact($config);
            unset($config);


            # @@@@@@@@@@@@ NEW RESIZING PART [END] @@@@@@@@@@@

            $this->s_picture_path = $new_imagename . '.' . $ext;

            @unlink($this->upload_image); //Unlink the original image........
            //@unlink($this->upload_path.getThumbName($prev_img,'thumb')); //Unlink the prevoius image........

            return $this->s_picture_path;
        } else {
            return $prev_img; // Unchanged previous image
        }
    }

    # function to move all those images from "temp" folder to user_photos and resizing accordingly...

    public function move_temp_images_AJAX($flash_uploaded_pic) {
        $arr_data = array();
        $i = 0;
        $FLASH_UPLOADED_PHOTOS_ARR = $flash_uploaded_pic;

        foreach ($FLASH_UPLOADED_PHOTOS_ARR as $key => $val) {

            $TMP_ORIGINAL_IMG = $val;
            $ORIGINAL_VERSION = $TMP_ORIGINAL_IMG;
            $arr_data[$i] = $this->resize_photos($TMP_ORIGINAL_IMG);
            // = $val;
            $i++;
        }


        return $arr_data;
    }

    public function resize_photos($uploaded_tmp_file = null) {

        preg_match('/(^.*)\.([^\.]*)$/', $uploaded_tmp_file, $matches);
        $ext = "";
        if (count($matches) > 0) {
            $ext = strtolower($matches[2]);
            $original_name = $matches[1];
        } else
            $original_name = 'photo';


        $imagename = createImageName($original_name);

        # fixing image path...
        $IMG_DESTINATION_PATH = $this->upload_path;

        $chk_imagename = $imagename; //echo $IMG_DESTINATION_PATH.$chk_imagename.'-main.'.$ext ; exit;

        if (test_file($IMG_DESTINATION_PATH . $chk_imagename . '-thumb.' . $ext)) {
            for ($i = 0; test_file($IMG_DESTINATION_PATH . $chk_imagename . '-' . $i . '-thumb.' . $ext); $i++) {
                
            }

            $new_imagename = $imagename . '-' . $i;
        } else {
            $new_imagename = $imagename;
        }


        $this->photoname = $new_imagename;
        $this->upload_photo = $IMG_DESTINATION_PATH . $new_imagename . '.' . $ext;
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
        $config['width'] = 472;
        $config['height'] = 378;
        $config['within_rectangle'] = true;
        $config['small_image_resize'] = 'no_resize';
        resize_exact($config);
        unset($config);

        $config = array();
        $config['source_image'] = $this->upload_photo;
        $config['thumb_marker'] = '-main';
        #$config['crop'] = false;
        $config['width'] = 400;
        $config['height'] = 327;
        $config['within_rectangle'] = true;
        $config['small_image_resize'] = 'no_resize';
        resize_exact($config);
        unset($config);

        $config = array();
        $config['source_image'] = $this->upload_photo;
        $config['thumb_marker'] = '-big';
        #$config['crop'] = false;
        $config['width'] = 800;
        $config['height'] = 536;
        $config['within_rectangle'] = true;
        $config['small_image_resize'] = 'no_resize';
        resize_exact($config);
        unset($config);

        @unlink($this->upload_photo);

        # also unlink the temp thumb version...
        $TMP_THUMB_IMG = getThumbName($uploaded_tmp_file, 'thumb');
        @unlink($this->upload_tmp_path . $TMP_THUMB_IMG);

        return $new_imagename . '.' . $ext;
    }

    public function get_photo_album_info($i_album_id) {

        $data = $this->data;
        $data['photo_album_detail_arr'] = $this->photo_albums_model->get_by_id($i_album_id);
        //pr($data['photo_album_detail_arr'],1);
        $VIEWS = "logged/photos/load_photo_album_details.phtml";
        $html = $this->load->view($VIEWS, $data, true);
        //echo $html; exit;

        echo json_encode(array('result' => 'success', 'album_html' => $html));
        exit;
    }

    public function delete($rec_id, $album_id) {
        $data = $this->data;
        $photo_arr = $this->user_photos_model->get_by_id($rec_id);
        //pr($photo_arr);
        $ret = $this->user_photos_model->delete_by_id($rec_id);

        $this->load->model('media_comments_model');
        $this->media_comments_model->delete_by_id($rec_id, 'photo');

        @unlink($this->upload_path . getThumbName($photo_arr['s_photo'], 'thumb'));
        @unlink($this->upload_path . getThumbName($photo_arr['s_photo'], 'main'));


        $re_page = base_url() . "photo-albums/" . $album_id . "/organize-photo.html";
        header("location:" . $re_page);
        exit;
    }

    public function delete_sel_photos() {
        //$messages = $this->input->post('csv_mail_ids');

        $this->load->model('data_messages_model');

        $current_page = $this->input->post('current_page');

        foreach (explode(',', $this->input->post('csv_mail_ids')) as $photo_id) {
            $photo_arr = $this->user_photos_model->get_by_id($photo_id);
            $ret = $this->user_photos_model->delete_by_id($photo_id);
            @unlink($this->upload_path . getThumbName($photo_arr['s_photo'], 'thumb'));
            @unlink($this->upload_path . getThumbName($photo_arr['s_photo'], 'main'));
        }

        $content = '';
        ob_start();
        $this->photos_ajax_pagination($current_page);
        $content = ob_get_contents();
        ob_end_clean();

        echo json_encode(array('sucess' => TRUE, 'content' => $content, 'msg' => 'Selected photo successfully deleted'));
    }

    public function fetch_all_photos() {
        try {
            $data = $this->data;
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
            $data['arr_allphotos'] = $this->user_photos_model->get_by_user_id($i_user_id);
            $VIEW = "logged/photos/blocks/photo_slide_show_pop_up.phtml";
            #parent::_render($data, $VIEW); 
            $html = $this->load->view($VIEW, $data, true);
            echo json_encode(array('result' => success, 'html_data' => $html));
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    ## post comments  ##

    public function post_comment($feed_id) {
        $this->load->model('media_comments_model');

        $this->load->model('users_model');
        $user_id = intval(decrypt($this->session->userdata('user_id')));
        $user_details = $this->users_model->fetch_this($user_id);

        $message = nl2br(htmlspecialchars(trim($this->input->post('message')), ENT_QUOTES, 'utf-8'));
        $_html = '';
        if ($message != '') {
            $ip = getenv("REMOTE_ADDR");
            $arr['i_media_id'] = $feed_id;
            $arr['s_media_type'] = 'photo';
            $arr['i_user_id'] = $user_id;
            $arr['s_contents'] = $message;
            $arr['dt_created_on'] = get_db_datetime();
            $arr['u_ip'] = $ip;

//            $this->media_comments_model->insert($arr);
//            $arr['pseudo'] = $user_details['s_profile_name'];
//            $data['comment'] = $arr;
            //$data['total_comments'] = $this->media_comments_model->get_total_by_newsfeed_id($feed_id);
            //$comment = $this->load->view('newsfeed/my_profile_single_feed_comment.phtml', $data, true);
            $comments = count_photo_comment_link($feed_id, 'photo')+1;
            $_html = '' . "Comments " . " (" . $comments . ")";
            $is_abusive = 0;
            $is_abusive = check_abusive_words($message);
            if ($is_abusive > 0) {
                echo json_encode(array('success' => 'false', 'msg' => "Abusive Words are not allowed", 'html' => $_html));
            } else {
                $this->media_comments_model->insert($arr);
                $arr['pseudo'] = $user_details['s_profile_name'];
                $data['comment'] = $arr;
                echo json_encode(array('success' => 'true', 'msg' => "Comment posted successfully.", 'html' => $_html));
            }
        } else {
            echo json_encode(array('success' => 'false', 'msg' => "Please enter some text.", 'html' => $_html));
        }
    }

    ## end post comments ##
    ## POST LIKE UNLIKE
    //POST LIKE UNLIKE

    public function like_unlike() {

        //$user_session_data =$this->user_session_data;  //GETTING STORED SESSION DATA WHILE LOGGED IN

        $liked_user_id = intval(decrypt($this->session->userdata('user_id')));
        $window_id = $this->input->post('window_id');
        $like_or_unlike = $this->input->post('like_val');
        $log_time = get_db_datetime();
        $ip_address = $this->input->server('REMOTE_ADDR');

        if ($like_or_unlike == "Like") {
            $like_unlike_information_array = array("i_media_id" => $window_id,
                "i_liked_user_id" => $liked_user_id,
                "dt_liked_on" => $log_time,
                "s_media_type" => 'photo');
        } else if ($like_or_unlike == "Unlike") {
            $like_unlike_information_array = array("i_media_id" => $window_id,
                "i_unliked_user_id" => $liked_user_id,
                "dt_unliked_on" => $log_time,
                "s_media_type" => 'photo');
        }

        $status = 0;
        $response = $this->media_comments_model->postLikeUnlike($like_unlike_information_array, strtolower($like_or_unlike));


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

            $_html = '' . "Liked by " . " (" . count_photo_like_link($window_id, 'photo') . ")";
        } else
            $response_message = "<span class='error_message'>" . $response['message'] . "</span>";


        $json_data = array('status' => $status, 'response_message' => $response_message, 'response_html' => $_html);
        echo json_encode($json_data);
    }

    public function fetch_comment_on_photo($i_media_id = '') {
        try {

            $data = $this->data;

            ob_start();
            $this->comments_ajax_pagination($i_media_id);
            $data['comments_list'] = ob_get_contents();
            ob_end_clean();

            $VIEW = "logged/photos/comments/my_photo_view_comments_lightbox.phtml";
            #parent::_render($data, $VIEW); 
            $html = $this->load->view($VIEW, $data, true);
            echo json_encode(array('result' => success, 'html_data' => $html));
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function comments_ajax_pagination($i_media_id, $page = 0) {
        try {
            $data = $this->data;
            $result = $this->media_comments_model->get_by_newsfeed_id($i_media_id, 'photo', $page, $this->comments_pagination_per_page);
            $resultCount = count($result);
            $total_rows = $this->media_comments_model->get_total_by_newsfeed_id($i_media_id, 'photo');
            //pr($result); 		

            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url() . "logged/my_photos/comments_ajax_pagination/{$i_media_id}";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->comments_pagination_per_page;
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



            $config['div'] = '#view_comments'; /* Here #content is the CSS selector for target DIV */
            #$config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
            #$config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */


            $this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();


            $data['result_arr'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $page;
            $data['total_pages'] = ceil($total_rows / $this->comments_pagination_per_page);

            $p = ($page / $this->comments_pagination_per_page);
            $data['current_loaded_page_no'] = $p + 1;

            # rendering the view file...
            $VIEW_FILE = "logged/photos/comments/my_photo_view_comments_lightbox_ajax.phtml";
            $this->load->view($VIEW_FILE, $data);
            //return $html;
        } catch (Exception $err_obj) {
            
        }
    }

    public function fetch_people_liked_post($i_media_id = '') {
        try {
            $data = $this->data;

            ob_start();
            $this->fetch_people_liked_post_ajax($i_media_id);
            $data['people_liked_list'] = ob_get_contents();
            ob_end_clean();

            $VIEW = "logged/photos/comments/liked_by_lightbox.phtml";
            #parent::_render($data, $VIEW); 
            $html = $this->load->view($VIEW, $data, true);
            echo json_encode(array('result' => success, 'html_data' => $html));
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function fetch_people_liked_post_ajax($i_media_id, $page = 0) {
        try {
            $data = $this->data;
            $result = $this->media_comments_model->get_people_liked_by_newsfeed_id($i_media_id, 'photo', $page, $this->people_liked_pagination_per_page);
            $resultCount = count($result);
            $total_rows = $this->media_comments_model->get_total_people_liked_by_newsfeed_id($i_media_id, 'photo');
            //pr($result); 		

            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url() . "logged/my_photos/fetch_people_liked_post_ajax/{$i_media_id}";
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
            $data['total_pages'] = ceil($total_rows / $this->people_liked_pagination_per_page);

            $p = ($page / $this->people_liked_pagination_per_page);
            $data['current_loaded_page_no'] = $p + 1;

            # rendering the view file...
            $VIEW_FILE = "logged/photos/comments/liked_by_lightbox_ajax.phtml";
            $this->load->view($VIEW_FILE, $data);
            //return $html;
        } catch (Exception $err_obj) {
            
        }
    }

    ########### NEW FETCH COMMENTS ON WALL ###########

    public function new_fetch_likes_on_photos($i_media_id = '') {
        try {
            $data = $this->data;
            $html = '';
            $result = $this->media_comments_model->get_people_liked_by_newsfeed_id($i_media_id, 'photo');

            //pr($result);

            if (count($result)) {
                foreach ($result as $key => $val) {

                    $name = $val['s_profile_name'];
                    $profile_image = get_profile_image_of_user('thumb',$val['s_profile_photo'],$val['e_gender']);

                    $profile_link = get_profile_url($val['i_user_id'], $val['s_profile_name']);

                    $html .= '     <div class="user_div dp-list-user"> 
											<a href="' . $profile_link . '">
											<div class="pro_photo3" style="background:url(' . $profile_image . ') no-repeat center;width:60px; height:60px;"></div></a> 
											<a href="javascript:void(0);" class="blue_link">' . $name . '</a> 
										</div>
										';
                }
                $html .= '<br class="clr" />';
            } else {
                $html .= '     <div class="user_div" style="width:475px !important;"> 
										<div style="text-align:center;"><p>No Likes.</p></div>
										</div>
										';
            }


            echo json_encode(array('result' => 'success', 'html_data' => $html));
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    ########### NEW FETCH COMMENTS ON WALL METHOD ###########

    public function NEW_fetch_comment_photos($i_media_id = '') {
        try {
            $data = $this->data;
            $html = '';
            $result = $this->media_comments_model->get_by_newsfeed_id($i_media_id, 'photo');

            //pr($result);
            if (count($result)) {

                foreach ($result as $key => $val) {

                    $profile_image_filename = get_profile_image_of_user('thumb',$val['s_profile_photo'],$val['e_gender']);
                    $DESC = html_entity_decode(htmlspecialchars_decode($val['s_contents']), ENT_QUOTES, 'utf-8');
                    $profile_link = get_profile_url($val['i_user_id'], $val['s_profile_name']);

                    $html .= '<div class="txt_content01 comments-number-content"> 
					 			<a href="' . $profile_link . '"><div style="background:url(' . $profile_image_filename . ') no-repeat center;width:60px; height:60px;" class="pro_photo" ></div></a>
									<div class="left-nw-wal">
										  <p class="blue_bold12"><a href="javascript:void(0);">' . $val['s_profile_name'] . '</a></p>
										  <p>' . nl2br($DESC) . '</p>
											 <p class="read-more">Updated on: ' . get_time_elapsed($val['dt_created_on']) . '</p>
									</div>
									<div class="clr"></div>
							  </div>';
                }
            } else {
                $html .= '     <div class="txt_content01 comments-number-content" style="width:475px !important;"> 
										<div style="text-align:center;"><p>No Comments.</p></div>
										</div>
										';
            }

            echo json_encode(array('result' => 'success', 'html_data' => $html));
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    function php_info() {
        phpinfo();
    }

}

// end of controller...

