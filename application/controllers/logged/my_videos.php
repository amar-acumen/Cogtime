<?php

/* * *******
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
include(APPPATH . 'controllers/base_controller.php');

class My_videos extends Base_controller {

    private $pagination_per_page = 10;
    private $pagination_per_page_all_videos = 10;
    private $pagination_per_page_album_videos = 10;   // album detail page
    private $view_all_albums_pagination_per_page = 15;
    private $manage_album_pagination_per_page = 10;
    private $people_liked_pagination_per_page = 5;
    private $comments_pagination_per_page = 5;

    public function __construct() {
        try {
            parent::__construct();
            parent::check_login(TRUE, '', array('1', '2', '3')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...
            $this->load->model('media_comments_model');
            $this->load->helper('wall_helper');
            $this->load->model('users_model');
            $this->load->model('my_videos_model');


            #defining path

            $this->upload_path = BASEPATH . '../uploads/user_videos/';
            $this->upload_path_album_photo = BASEPATH . '../uploads/user_videos_album/';
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function index() {
        try {
            $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
            $posted = array();
            $this->data["posted"] = $posted; /* don't change */
            $data = $this->data;
            $this->data["MAIN_MENU_SELECTED"] = 1;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');


            parent::_add_js_arr(array('js/ddsmoothmenu.js',
                'js/switch.js',
                'js/animate-collapse.js',
                'js/lightbox.js',
                'js/jquery.dd.js',
                'js/jquery-ui-1.8.2.custom.min.js',
                'js/stepcarousel.js',
                'js/jquery.form.js',
                'js/jquery/JSON/json2.js',
                'js/frontend/logged/my_videos.js',
                'js/frontend/logged/video_helper.js',
                'js/frontend/logged/tweets/tweet_utilities.js'
            ));

            parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
                'css/dd.css'));
            # adjusting header & footer sections [End]...

            $data['page_view_type'] = 'myaccount';
            $arr_profile_info = $this->users_model->fetch_this($i_profile_id);
            $data['arr_profile_info'] = $arr_profile_info;

            // to show all album names in select box while uploading a video
            $data['album_names'] = $this->upload_video_select_album_name();


            // setting blank session 
            $this->session->set_userdata('search_condition', '');

            $data['total_albums'] = $this->my_videos_model->get_total_no_of_albums($i_profile_id);
            $data['total_videos'] = $this->my_videos_model->search_get_total_no_of_videos($i_profile_id, '');

            ### for all albums  ###
            ob_start();
            $this->my_video_albums_ajax_pagination();
            $data['result_arr'] = ob_get_contents();
            ob_end_clean();
            ### end all albums  ###
            ### for all videos  ###
            ob_start();
            $this->my_videos_ajax_pagination();
            $content = ob_get_contents();
            $content_obj = json_decode($content);
            $data['result_arr1'] = $content_obj->html;
            $data['pagination_per_page_all_videos'] = $content_obj->pagination_per_page_all_videos;
            $data['view_more'] = $content_obj->view_more;
            ob_end_clean();
            ### end all albums  ###
            # view file...
            $VIEW = "logged/videos/my_videos.phtml";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

    public function my_video_albums_ajax_pagination($page = 0) {
        try {
            $add_where = '';

            $i_profile_id = intval(decrypt($this->session->userdata('user_id')));

            $result = $this->my_videos_model->get_all_video_albums_with_count($i_profile_id, $page, $this->pagination_per_page);

            $total_rows = $this->my_videos_model->get_total_no_of_albums($i_profile_id);

            //pr($result);
            #Jquery Pagination Starts
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url() . "logged/my_videos/my_video_albums_ajax_pagination";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->pagination_per_page;
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

            $config['div'] = '#videos_albums'; /* Here #content is the CSS selector for target DIV */
            $config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
            $config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */

            $this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();

            // getting   listing...
            $data['my_videos_album_content'] = $result;
            $data['no_of_result_album'] = $total_rows;
            $data['current_page'] = $page;
            $data['total_pages'] = ceil($total_rows / $this->pagination_per_page);

            //echo $data['total_pages'].' ==total_pages==== '.$page;
            //echo $data['current_page'].' ==  ';
            $p = ($page / $this->pagination_per_page);
            $data['current_loaded_page_no'] = $p + 1;
            $data['is_post_'] = $this->session->userdata('is_post_');
            # loading the view-part...
            echo $this->load->view('logged/videos/my_videos_ajax.phtml', $data, TRUE);
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function my_videos_ajax_pagination($page = 0) {
        try {
            //$this->session->set_userdata('search_condition','');
            $WHERE = '';
            if (isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y') {
                $video_name = get_formatted_string(trim($this->input->post('txt_title')));

                $WHERE = ($video_name == '') ? '' : " AND (s_title LIKE '%" . $video_name . "%')";

                $this->session->set_userdata('search_condition', $WHERE);
            }


            $where_cond = $this->session->userdata('search_condition');


            $cur_page = $page + $this->pagination_per_page_all_videos;
            $i_profile_id = intval(decrypt($this->session->userdata('user_id')));

            $result = $this->my_videos_model->search_get_all_videos($i_profile_id, $where_cond, $page, $this->pagination_per_page_all_videos);

            $total_rows = $this->my_videos_model->search_get_total_no_of_videos($i_profile_id, $where_cond);


            //--- for check whether more videos are there or not
            $view_more = true;
            $rest_counter = $total_rows - $page;
            if ($rest_counter <= $this->pagination_per_page_all_videos)
                $view_more = false;


            //--------- end check
            // echo $data['view_more'];
            //$rest_videos = $this->my_videos_model->rest_total_videos($i_profile_id , $cur_page,$total_rows);
            //pr($result);
            #Jquery Pagination Starts
            /*             $this->load->library('jquery_pagination');
              $config['base_url'] = base_url()."logged/my_videos/my_videos_ajax_pagination";
              $config['total_rows'] = $total_rows;
              $config['per_page'] = $this->pagination_per_page_all_videos;
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

              $config['div'] = '#all_videos'; /* Here #content is the CSS selector for target DIV */
#              $config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
#              $config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */
#  
#              $this->jquery_pagination->initialize($config);
#              $data['page_links'] = $this->jquery_pagination->create_links();
            // getting   listing...
            $data['my_videos_content'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['pagination_per_page_all_videos'] = $this->pagination_per_page_all_videos;
            $data['total_pages'] = ceil($total_rows / $this->pagination_per_page_all_videos);
            $data['current_page_1'] = $cur_page;

            //echo $data['total_pages'].' ==total_pages==== '.$page;
            //echo $data['current_page'].' ==  ';
#               $p = ($page/$this->pagination_per_page_all_videos);
            #        $data['current_loaded_page_no'] =  $p + 1;
            #        $data['is_post_'] = $this->session->userdata('is_post_') ;
            # loading the view-part...

            $VIEW_FILE = "logged/videos/my_all_videos_ajax.phtml";
            if (is_array($result) && count($result)) {
                $content = $this->load->view($VIEW_FILE, $data, true);
            } else {

                $content = '';
            }

            echo json_encode(array('html' => $content, 'current_page' => $cur_page, 'pagination_per_page_all_videos' => $data['pagination_per_page_all_videos'], 'view_more' => $view_more));
            //echo  $this->load->view('logged/videos/my_all_videos_ajax.phtml', $data,TRUE);
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function view_all_video_albums() {
        try {
            $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
            $posted = array();
            $this->data["posted"] = $posted; /* don't change */
            $data = $this->data;
            $this->data["MAIN_MENU_SELECTED"] = 1;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');


            parent::_add_js_arr(array('js/ddsmoothmenu.js',
                'js/switch.js',
                'js/animate-collapse.js',
                'js/lightbox.js',
                'js/jquery.dd.js',
                'js/jquery-ui-1.8.2.custom.min.js',
                'js/stepcarousel.js',
                'js/jquery.form.js',
                'js/jquery/JSON/json2.js',
                'js/frontend/logged/my_videos.js',
                'js/frontend/logged/video_helper.js',
                'js/frontend/logged/tweets/tweet_utilities.js',
            ));

            parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
                'css/dd.css'));
            # adjusting header & footer sections [End]...

            $data['page_view_type'] = 'myaccount';
            $arr_profile_info = $this->users_model->fetch_this($i_profile_id);
            $data['arr_profile_info'] = $arr_profile_info;

            // to show all album names in select box while uploading a video
            $data['album_names'] = $this->upload_video_select_album_name();


            // setting blank session 
            $this->session->set_userdata('search_condition', '');


            ### for all albums  ###
            ob_start();
            $this->view_all_video_albums_ajax_pagination();
            $data['result_arr'] = ob_get_contents();
            ob_end_clean();
            ### end all albums  ###
            # view file...
            $VIEW = "logged/videos/view_all_video_albums.phtml";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

    public function view_all_video_albums_ajax_pagination($page = 0) {
        try {
            $add_where = '';

            $i_profile_id = intval(decrypt($this->session->userdata('user_id')));

            $result = $this->my_videos_model->get_all_video_albums_with_count($i_profile_id, $page, $this->view_all_albums_pagination_per_page);

            $total_rows = $this->my_videos_model->get_total_no_of_albums($i_profile_id);

            //pr($result);
            #Jquery Pagination Starts
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url() . "logged/my_videos/view_all_video_albums_ajax_pagination";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->view_all_albums_pagination_per_page;
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

            $config['div'] = '#videos_albums'; /* Here #content is the CSS selector for target DIV */
            $config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
            $config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */

            $this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();

            // getting   listing...
            $data['my_videos_album_content'] = $result;
            $data['no_of_result_album'] = $total_rows;
            $data['current_page'] = $page;
            $data['total_pages'] = ceil($total_rows / $this->view_all_albums_pagination_per_page);

            //echo $data['total_pages'].' ==total_pages==== '.$page;
            //echo $data['current_page'].' ==  ';
            $p = ($page / $this->view_all_albums_pagination_per_page);
            $data['current_loaded_page_no'] = $p + 1;
            $data['is_post_'] = $this->session->userdata('is_post_');
            # loading the view-part...
            echo $this->load->view('logged/videos/view_all_video_albums_ajax.phtml', $data, TRUE);
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    //------------------------------------------------ manage page-----------------------------------------
    function delete_video_album() {
        $album_id = $this->input->post('album_id');
        $album_img_res = $this->my_videos_model->get_album_img_by_album_id($album_id);

        $album_img = BASEPATH . "../uploads/user_videos_album/" . getThumbName($album_img_res['s_video_album_image'], 'thumb');

        @unlink($album_img);

        $sql = sprintf("SELECT id FROM %s WHERE `i_video_album_id`=%s", $this->db->USER_VIDEOS, $album_id);
        $res = $this->db->query($sql)->result_array();
        foreach ($res as $r) {

            $v_img = $this->my_videos_model->get_video_img_by_id($r['id']);
            $video_img = BASEPATH . "../uploads/user_videos/" . getThumbName($v_img['s_video_image'], 'bigthumb');

            @unlink($video_img);
        }

        $res = $this->my_videos_model->delete_video_album_by_id($album_id);



        echo json_encode(array('response' => true, 'msg' => "Album deleted successfully."));
    }

    //------------------------------------------ /delete -----------------------------------------
    //--------------------------------------- manage my video album -------------------------------------------
    public function manage_video_album() {

        try {
            $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
            $posted = array();
            $this->data["posted"] = $posted; /* don't change */
            $data = $this->data;
            $this->data["MAIN_MENU_SELECTED"] = 1;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');


            parent::_add_js_arr(array('js/ddsmoothmenu.js',
                'js/switch.js',
                'js/animate-collapse.js',
                'js/lightbox.js',
                'js/jquery.dd.js',
                'js/jquery-ui-1.8.2.custom.min.js',
                'js/stepcarousel.js',
                'js/jquery.form.js',
                'js/jquery/JSON/json2.js',
                'js/frontend/logged/my_videos.js',
                'js/frontend/logged/tweets/tweet_utilities.js',
            ));

            parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
                'css/dd.css'));
            # adjusting header & footer sections [End]...


            $data['page_view_type'] = 'myaccount';
            $arr_profile_info = $this->users_model->fetch_this($i_profile_id);
            $data['arr_profile_info'] = $arr_profile_info;


            $data['album_names'] = $this->upload_video_select_album_name();

            $data['manage_album_pagination_per_page'] = $this->manage_album_pagination_per_page;


            ### for all albums  ###
            /* ob_start();
              $this->manage_my_video_albums_ajax_pagination();
              $data['result_arr'] = ob_get_contents();
              ob_end_clean();
             */


            ob_start();
            $this->manage_my_video_albums_ajax_pagination();
            $content = ob_get_contents();
            $content_obj = json_decode($content);
            $data['result_arr'] = $content_obj->html;
            $data['view_more'] = $content_obj->view_more;
            ob_end_clean();


            ### end all albums  ###   
            # view file...
            $VIEW = "logged/videos/manage_video_album.phtml";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

    public function manage_my_video_albums_ajax_pagination($page = 0) {
        try {
            $current_page = $page + $this->manage_album_pagination_per_page;
            $add_where = '';

            $i_profile_id = intval(decrypt($this->session->userdata('user_id')));

            $result = $this->my_videos_model->manage_video_albums_with_count($i_profile_id, $page, $this->manage_album_pagination_per_page);

            $total_rows = $this->my_videos_model->get_total_no_of_albums($i_profile_id);

            //pr($result);
            #Jquery Pagination Starts
            /*           $this->load->library('jquery_pagination');
              $config['base_url'] = base_url()."logged/my_videos/manage_my_video_albums_ajax_pagination";
              $config['total_rows'] = $total_rows;
              $config['per_page'] = $this->manage_album_pagination_per_page;
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

              $config['div'] = '#manage_video_albums'; /* Here #content is the CSS selector for target DIV
              $config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code
              $config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code

              $this->jquery_pagination->initialize($config);
              $data['page_links'] = $this->jquery_pagination->create_links();
             */

            $view_more = true;
            $rest_counter = $total_rows - $page;
            if ($rest_counter <= $this->manage_album_pagination_per_page)
                $view_more = false;


            // getting   listing...
            $data['manage_my_videos_album_content'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['manage_album_pagination_per_page'] = $manage_album_pagination_per_page;
            $data['current_page'] = $current_page;
            $data['total_pages'] = ceil($total_rows / $this->manage_album_pagination_per_page);

            //echo $data['total_pages'].' ==total_pages==== '.$page;
            //echo $data['current_page'].' ==  ';
            $p = ($page / $this->manage_album_pagination_per_page);
            $data['current_loaded_page_no'] = $p + 1;
            $data['is_post_'] = $this->session->userdata('is_post_');
            # loading the view-part...
            //  echo  $this->load->view('logged/videos/manage_video_albums_ajax.phtml', $data,TRUE);
            $VIEW_FILE = 'logged/videos/manage_video_albums_ajax.phtml';
            if (is_array($result) && count($result)) {
                $content = $this->load->view($VIEW_FILE, $data, true);
            } else {
                #$content = '<div class="txt_content01"><p style="margin-left: 330px;">No Posts!</p></div>';
                $content = '';
            }
            echo json_encode(array('html' => $content, 'current_page' => $current_page, 'view_more' => $view_more));
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function create_video_album() {

        try {
            $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
            $posted = array();
            $this->data["posted"] = $posted; /* don't change */
            $data = $this->data;
            $this->data["MAIN_MENU_SELECTED"] = 1;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');


            parent::_add_js_arr(array('js/ddsmoothmenu.js',
                'js/switch.js',
                'js/animate-collapse.js',
                'js/lightbox.js',
                'js/jquery.dd.js',
                'js/jquery-ui-1.8.2.custom.min.js',
                'js/stepcarousel.js',
                'js/jquery.form.js',
                'js/jquery/JSON/json2.js',
                'js/frontend/logged/my_videos.js',
                'js/frontend/logged/tweets/tweet_utilities.js',
            ));

            parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
                'css/dd.css'));
            # adjusting header & footer sections [End]...
            //$data['http_referer'] = $_SERVER['HTTP_REFERER'];
            $this->session->set_userdata('http_referer', $_SERVER['HTTP_REFERER']);

            $data['page_view_type'] = 'myaccount';
            $arr_profile_info = $this->users_model->fetch_this($i_profile_id);
            $data['arr_profile_info'] = $arr_profile_info;


            # view file...
            $VIEW = "logged/videos/create_video_album.phtml";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

    function create_video_album_ajax_submit() {

        if ($_POST) {
            $info['i_user_id'] = intval(decrypt($this->session->userdata('user_id')));
            $info['s_name'] = trim($this->input->post('txt_video_album_name'));
            $info['s_desc'] = trim($this->input->post('txtarea_desc'));

            $info['e_privacy'] = $this->input->post('video_album_privacy');




            // checking for extension
            if (isset($_FILES['file_cover_photo']['name']) && $_FILES['file_cover_photo']['name'] != '') {

                preg_match('/(^.*)\.([^\.]*)$/', $_FILES['file_cover_photo']['name'], $matches);
                $ext = "";
                if (count($matches) > 0) {
                    $ext = $matches[2];
                    $original_name = $matches[1];
                } else
                    $original_name = 'photo';


                if (!in_array($ext, $this->config->item('VALID_IMAGE_EXT'))) {
                    $arr_message['file_cover_photo'] = "supported extensions are " . implode(' , ', $this->config->item('VALID_IMAGE_EXT'));
                } else if ($_FILES['file_cover_photo']['size'] > $this->config->item('MAX_UP_FILE_SIZE') * 1024 * 1024) {
                    $arr_message["file_cover_photo"] = "Maximum file upload size is " . $this->config->item('MAX_UP_FILE_SIZE') . " MB.";
                }
            } else {
                $arr_message['file_cover_photo'] = "* Required Field.";
            }

            if ($info['s_name'] == '') {
                $arr_message['txt_video_album_name'] = "* Required Field.";
            }
            /* if($info['e_privacy'] == '-1')
              {
              $arr_message['video_album_privacy'] = "* Required Field.";
              } */
            if ($info['s_desc'] == '') {
                $arr_message['txtarea_desc'] = "* Required Field.";
            }




            //pr($arr_message);
            if (count($arr_message) == 0) {
                //echo "testing if insert section.";
                $info['i_user_id'] = intval(decrypt($this->session->userdata('user_id')));
                $info['s_name'] = trim($this->input->post('txt_video_album_name'));
                $info['s_video_album_image'] = $this->_upload_photo();
                $info['e_privacy'] = $this->input->post('video_album_privacy');
                $info['dt_created_on'] = get_db_datetime();
                //echo "testing before model";
                $res = $this->my_videos_model->create_video_album($info);
                //echo "testing after model";
            }
            if ($res) {
                ###########################Privacy settings###################################
                insert_privacy($res, $_POST, $this->db->videolbum_privacy, 'i_video_album_id');
                ###########################Privacy settings###################################
                $success = true;
                $msg = "Album created successfully.";
            } else {
                $success = false;
                $msg = 'Error!';
            }

            //pr($info);

            $http_referer = $this->session->userdata('http_referer');
            echo json_encode(array('result' => $success,
                'msg' => $msg,
                'arr_messages' => $arr_message,
                'http_referer' => $http_referer
                    )
            );
        }
    }

    //--------------------------------------------- edit album --------------------------------------------
    public function manage_edit_album($album_id) {

        try {

            $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
            $posted = array();
            $this->data["posted"] = $posted; /* don't change */
            $data = $this->data;
            $this->data["MAIN_MENU_SELECTED"] = 1;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');


            parent::_add_js_arr(array('js/ddsmoothmenu.js',
                'js/switch.js',
                'js/animate-collapse.js',
                'js/lightbox.js',
                'js/jquery.dd.js',
                'js/jquery-ui-1.8.2.custom.min.js',
                'js/stepcarousel.js',
                'js/jquery.form.js',
                'js/jquery/JSON/json2.js',
                'js/frontend/logged/my_videos.js',
                'js/frontend/logged/tweets/tweet_utilities.js',
            ));

            parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
                'css/dd.css'));
            # adjusting header & footer sections [End]...


            $data['page_view_type'] = 'myaccount';
            $arr_profile_info = $this->users_model->fetch_this($i_profile_id);
            $data['arr_profile_info'] = $arr_profile_info;

            $data['album_info'] = $this->my_videos_model->get_album_info($album_id);


            // to show all album names in select box while uploading a video
            $data['album_names'] = $this->upload_video_select_album_name();
            $data['privacy_arr'] = $this->my_videos_model->get_privacy_settings_by_video_album_id($album_id);


            # view file...
            $VIEW = "logged/videos/manage_video_albums_edit_album.phtml";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

    function edit_video_album_ajax_submit() {

        if ($_POST) {

            $info['i_user_id'] = intval(decrypt($this->session->userdata('user_id')));
            $info['s_name'] = trim($this->input->post('txt_video_album_name'));
            $info['s_desc'] = trim($this->input->post('txtarea_desc'));

            $info['e_privacy'] = $this->input->post('video_album_privacy');




            // checking for extension
            if (isset($_FILES['file_cover_photo']['name']) && $_FILES['file_cover_photo']['name'] != '') {

                preg_match('/(^.*)\.([^\.]*)$/', $_FILES['file_cover_photo']['name'], $matches);
                $ext = "";
                if (count($matches) > 0) {
                    $ext = $matches[2];
                    $original_name = $matches[1];
                } else
                    $original_name = 'photo';


                if (!in_array($ext, $this->config->item('VALID_IMAGE_EXT'))) {
                    $arr_message['file_cover_photo'] = "supported extensions are " . implode(' , ', $this->config->item('VALID_IMAGE_EXT'));
                } else if ($_FILES['file_cover_photo']['size'] > $this->config->item('MAX_UP_FILE_SIZE') * 1024 * 1024) {
                    $arr_message["file_cover_photo"] = "Maximum file upload size is " . $this->config->item('MAX_UP_FILE_SIZE') . " MB.";
                } else {   // if given photo is perfect
                    $prev_cover_img = BASEPATH . "../uploads/user_videos_album/" . $this->input->post('album_cover_img');
                    @unlink($prev_cover_img);
                    $info['s_video_album_image'] = $this->_upload_photo();
                }
            } else {
                //$arr_message['file_cover_photo'] = "* Required Field.";
            }

            if ($info['s_name'] == '') {
                $arr_message['txt_video_album_name'] = "* Required Field.";
            }
            /* if($info['e_privacy'] == '-1')
              {
              $arr_message['video_album_privacy'] = "* Required Field.";
              } */
            if ($info['s_desc'] == '') {
                $arr_message['txtarea_desc'] = "* Required Field.";
            }




            //pr($arr_message);
            if (count($arr_message) == 0) {
                //echo "testing if insert section.";
                $info['id'] = $this->input->post('album_id');
                $info['i_user_id'] = intval(decrypt($this->session->userdata('user_id')));
                $info['s_name'] = trim($this->input->post('txt_video_album_name'));
                //$info['s_video_album_image'] = $this->_upload_photo() ;
                $info['e_privacy'] = $this->input->post('video_album_privacy');
                $info['dt_updated_on '] = get_db_datetime();
                //echo "testing before model";
                $res = $this->my_videos_model->edit_video_album($info);
                //echo "testing after model";
            }
            if ($res) {
                $success = true;

                ###########################Privacy settings###################################
                $id = $this->input->post('album_id');
                $this->db->query("DELETE FROM {$this->db->videolbum_privacy} WHERE i_video_album_id='" . $id . "'");
                insert_privacy($id, $_POST, $this->db->videolbum_privacy, 'i_video_album_id');
                ###########################Privacy settings###################################

                $msg = "Album edited successfully.";
            } else {
                $success = false;
                $msg = 'Error!';
            }

            //pr($info);
            echo json_encode(array('result' => $success,
                'msg' => $msg,
                'arr_messages' => $arr_message
                    )
            );
        }
    }

    //--------------------------------------------- end edit album --------------------------------------------
    //--------------------------------------- end manage my video album -------------------------------------------
    // -------------------------------- resize and upload album cover photo ----------------------------------------
    public function _upload_photo($prev_img = '') {
        parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by non logged in user
        #pr($_FILES);
        $fileElementName = 'file_cover_photo';
        if (!empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] != '') {
            preg_match('/(^.*)\.([^\.]*)$/', $_FILES[$fileElementName]['name'], $matches);
            $ext = "";
            if (count($matches) > 0) {
                $ext = strtolower($matches[2]);
                $original_name = $matches[1];
            } else
                $original_name = 'image';


            $imagename = createImageName($original_name);

            if (test_file($this->upload_path_album_photo . $imagename . '-thumb.' . $ext)) {
                for ($i = 0; test_file($this->upload_path_album_photo . $imagename . '-' . $i . '-thumb.' . $ext); $i++) {
                    
                }

                $new_imagename = $imagename . '-' . $i;
            } else {
                $new_imagename = $imagename;
            }

            $this->imagename = $new_imagename;

            $this->upload_image = $this->upload_path_album_photo . $new_imagename . '.' . $ext;
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


            /*     $config = array();
              $config['source_image'] = $this->upload_image;
              $config['thumb_marker'] = '-main';
              #$config['crop'] = false;
              $config['width'] = 400;
              $config['height'] = 327;
              $config['within_rectangle'] =true;
              $config['small_image_resize'] = 'no_resize';
              resize_exact($config);
              unset($config);
             */
            # @@@@@@@@@@@@ NEW RESIZING PART [END] @@@@@@@@@@@

            $this->s_picture_path = $new_imagename . '.' . $ext;

            @unlink($this->upload_image); //Unlink the original image........
            //@unlink($this->upload_path.getThumbName($prev_img,'thumb')); //Unlink the prevoius image........

            return $this->s_picture_path;
        } else {
            return $prev_img; // Unchanged previous image
        }
    }

// end of resize and upload album cover photo
    //================================================ upload video ================================================
    //------------------------------------------------ fetch album name -----------------------------------------------
    function upload_video_select_album_name() {       //called from index
        $user_id = intval(decrypt($this->session->userdata('user_id')));
        $names = $this->my_videos_model->get_all_video_album_name($user_id);

        return $names;
    }

    //-------------------------------------------- upload individual video --------------------------------------------
    function upload_individual_video() {

        try {
            $arr_messages = array();

            if ($_POST) {
                # error message trapping...
                if (trim($this->input->post('txt_title')) == '') {
                    $arr_messages['txt_title'] = "* Required Field";
                }
                /*          if( trim($this->input->post('txt_artist'))=='') 
                  {
                  $arr_messages['txt_artist'] = "* Required Field";
                  }
                  if( trim($this->input->post('txt_genre'))=='')
                  {
                  $arr_messages['txt_genre'] = "* Required Field";
                  }
                 */
                if (trim($this->input->post('txt_video_track_album')) == '') {
                    $arr_messages['txt_video_track_album'] = "* Required Field";
                }
                if (trim($this->input->post('txtarea_desc_video')) == '') {
                    $arr_messages['txtarea_desc_video'] = "* Required Field";
                }

                if (trim($this->input->post('select_album1')) == '-1') {
                    $arr_messages['select_album1'] = "* Required Field";
                } elseif (trim($this->input->post('select_album1')) == '0') {
                    $new_album = false;
                    if (trim($this->input->post('txt_nw_album')) == '') {
                        $arr_messages['txt_nw_album'] = "* Required Field";
                    } else {
                        $info_nw_album['s_name'] = get_formatted_string($this->input->post('txt_nw_album'));
                        $info_nw_album['i_user_id'] = intval(decrypt($this->session->userdata('user_id')));
                        $info_nw_album['e_privacy'] = 'everyone';
                        $info_nw_album['dt_created_on'] = get_db_datetime();

                        $new_album = true;
                    }
                } else {
                    //$info['i_video_album_id'] = trim($this->input->post('select_album1'));
                }

				if(trim($this->input->post('txt_video_track_album')) != '' || trim($this->input->post('select_album1')) != '0' || trim($this->input->post('select_album1')) != '-1' )
					{
					$returns=match_video(intval(decrypt($this->session->userdata('user_id'))),trim($this->input->post('select_album1')),trim($this->input->post('txt_video_track_album')));
					
					if($returns != '')
					{
					$arr_messages['txt_video_track_album'] = "* Video already exists";
					}
					}



                if ($this->input->post('txt_video_track_album') != '') {

                    try {

                        $this->load->library('embed_video');
                        $config['zend_library_path'] = APPPATH . "libraries/Zend/";
                        $config['video_url'] = trim($this->input->post('txt_video_track_album'));

                        $this->embed_video->initialize($config);
                        //echo 'video_url='.$this->embed_video->video_url;
                        $this->embed_video->prepare();

                        $this->embed_video->save_thumb($this->upload_path, '-bigthumb', 122, 82);
                        $this->video_img_name = $this->embed_video->get_resized_imagename();
                    } catch (Exception $e) {
                        $arr_messages['txt_video_track_album'] = "* Not valid video URL";
                    }
                }

                $MAX_VIDEO = $this->data['site_settings_arr']['i_max_video_upload'];
                $TOTAL_VIDEO = $this->my_videos_model->get_total_by_user_id(intval(decrypt($this->session->userdata('user_id'))));

                if (count($arr_messages) == 0 && ($TOTAL_VIDEO < $MAX_VIDEO || $MAX_VIDEO == 0 )) {

                    $info = array();
                    $info['i_user_id'] = intval(decrypt($this->session->userdata('user_id')));

                    if ($new_album) {
                        $nw_album_id = $this->my_videos_model->create_video_album_at_video_upload($info_nw_album);
                        $info['i_video_album_id'] = $nw_album_id;   // setting new video album id
                    } else
                        $info['i_video_album_id'] = trim($this->input->post('select_album1'));

                    $info['s_video_file_name'] = trim($this->input->post('txt_video_track_album'));
                    //$this->video_img_name = $this->embed_video->get_resized_imagename();
                    $info['s_video_image'] = $this->video_img_name;
                    //$info['s_video_image']     = trim($this->input->post('txt_video_track_album'));

                    $info['s_title'] = get_formatted_string($this->input->post('txt_title'));
                    $info['s_artist'] = get_formatted_string($this->input->post('txt_artist'));
                    $info['s_genre'] = trim($this->input->post('txt_genre'));

                    $info['s_description'] = trim($this->input->post('txtarea_desc_video'));
                    $info["i_order"] = 1 + $this->my_videos_model->get_i_order($info['i_video_album_id']);


                    $info['dt_created_on'] = get_db_datetime();

                    $_ret = $this->my_videos_model->insert_new_video($info);


                    $http_referer = $_SERVER['HTTP_REFERER'];

                    echo json_encode(array('success' => true, 'arr_messages' => $arr_messages, 'msg' => 'Video File Uploaded Successfully.', 'http_referer' => $http_referer));
                }
                else if (count($arr_messages) == 0 && $TOTAL_VIDEO == $MAX_VIDEO) {

                    echo json_encode(array('success' => false, 'msg' => 'Video cannot be uploaded as maximum video upload limit is reached!', 'maxlimit' => true));
                    exit;
                } else {
                    echo json_encode(array('success' => false, 'arr_messages' => $arr_messages, 'msg' => t('Error!')));
                }
            }   //$_POST
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    //================================================ end of upload video ================================================
    //=============================================== play media file ===========================================
    public function get_media() {
        try {

            $i_media_id = intval($this->input->post('media_id'));
            $width = intval($this->input->post('width')) <= 0 ? '122' : intval($this->input->post('width'));
            $height = intval($this->input->post('height')) <= 0 ? '82' : intval($this->input->post('height'));


            $media_info = $this->my_videos_model->get_video_by_id($i_media_id);
            #echo utf8_accents_to_ascii($media_info['s_video_url']);

            if ($media_info == '') {
                echo json_encode(array('result' => 'error'));
                exit;
            }

            //$this->data['current_media_id'] = $i_media_id;

            /*             * ******************* Get photo details ************************ */



            try {
                $this->load->library('embed_video');
                $config['zend_library_path'] = APPPATH . "libraries/Zend/";
                $config['video_url'] = $media_info;

                $this->embed_video->initialize($config);
                $this->embed_video->prepare();


                $image_source = $this->embed_video->get_player($width, $height);
            } catch (Exception $e) {
                //$data['video_exists'] = false;
                $image_source = 'This video has been deleted.';
            }


            $result_arr['result'] = 'success';
            $result_arr['s_image_source'] = $image_source;

            $result_arr['i_media_id'] = $i_media_id;
            //pr($result_arr);

            echo json_encode($result_arr);
        } catch (Exception $err_obj) {
            
        }
    }

    //=============================================== end play media file ===========================================
//------------------------------------------- individual album detail --------------------------------------
    function my_video_album_details($album_id) {
        try {

            $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
            $posted = array();
            $this->data["posted"] = $posted; /* don't change */
            $data = $this->data;
            $this->data["MAIN_MENU_SELECTED"] = 1;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');


            parent::_add_js_arr(array('js/ddsmoothmenu.js',
                'js/switch.js',
                'js/animate-collapse.js',
                'js/lightbox.js',
                'js/jquery.dd.js',
                'js/jquery-ui-1.8.2.custom.min.js',
                'js/stepcarousel.js',
                'js/jquery.form.js',
                'js/jquery/JSON/json2.js',
                'js/frontend/logged/my_videos.js',
                'js/frontend/logged/video_helper.js',
                'js/frontend/logged/tweets/tweet_utilities.js'
            ));

            parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
                'css/dd.css'));
            # adjusting header & footer sections [End]...


            $data['page_view_type'] = 'myaccount';
            $arr_profile_info = $this->users_model->fetch_this($i_profile_id);
            $data['arr_profile_info'] = $arr_profile_info;

            ## album details ##
            $data['album_info'] = $this->my_videos_model->get_album_info($album_id);

            $data['this_album_id'] = $album_id;
            //$date['album_videos'] = $this->my_videos_model->get_all_videos_of_album($album_id,)
            // to show all album names in select box while uploading a video
            $data['album_names'] = $this->upload_video_select_album_name();


            $data['pagination_per_page_album_videos'] = $this->pagination_per_page_album_videos;

            $data['total_video'] = $this->my_videos_model->get_total_no_of_album_videos($album_id);


            ### for all videos  ###
            ob_start();
            $this->my_album_videos_ajax_pagination($album_id);
            $content = ob_get_contents();
            $content_obj = json_decode($content);
            $data['album_videos'] = $content_obj->html;
            ob_end_clean();
            ### end all albums  ###
            # view file...
            $VIEW = "logged/videos/my_individual_video_album.phtml";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

    public function my_album_videos_ajax_pagination($album_id = '', $page = 0) {
        try {
            $current_page = $page + $this->pagination_per_page_album_videos;
            //$this->session->set_userdata('search_condition','');
            $WHERE = '';
            $where_cond = '';
            if (isset($_POST['if_search_indv']) && $_POST['if_search_indv'] == 'y') {
                $video_name = get_formatted_string(trim($this->input->post('search_video_name_indv')));
                $album_id = $this->input->post('album_id_indv');

                $WHERE = ($video_name == '') ? '' : " AND (s_title LIKE '%" . $video_name . "%')";

                $this->session->set_userdata('search_condition', $WHERE);
            }
            $where_cond = $this->session->userdata('search_condition');


            $cur_page = $page + $this->pagination_per_page_album_videos;

            $i_profile_id = intval(decrypt($this->session->userdata('user_id')));

            $result = $this->my_videos_model->get_all_videos_of_album($album_id, $where_cond, $page, $this->pagination_per_page_album_videos);

            //$total_rows = $this->my_videos_model->get_total_no_of_album_videos($i_profile_id);
            $total_rows = $this->my_videos_model->get_total_no_of_album_videos($album_id);

            // pr($result);
            #Jquery Pagination Starts
            /*          $this->load->library('jquery_pagination');
              $config['base_url'] = base_url()."logged/my_videos/my_album_videos_ajax_pagination";
              $config['total_rows'] = $total_rows;
              $config['per_page'] = $this->pagination_per_page_album_videos;
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

              $config['div'] = '#album_all_videos'; /* Here #content is the CSS selector for target DIV */
            #             $config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
            #             $config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */
            #            $this->jquery_pagination->initialize($config);
            #            $data['page_links'] = $this->jquery_pagination->create_links();
            /* -- */



            //--- for check whether more videos are there or not
            $view_more = true;
            $rest_counter = $total_rows - $page;
            if ($rest_counter <= $this->pagination_per_page_album_videos)
                $view_more = false;

            //--------- end check
            // getting   listing...
            $data['my_album_videos_content'] = $result;
            $data['no_of_result'] = $total_rows;

            $data['total_pages'] = ceil($total_rows / $this->pagination_per_page_album_videos);
            $data['pagination_per_page_album_videos'] = $this->pagination_per_page_album_videos;
            $data['current_page'] = $current_page;
            //echo $data['total_pages'].' ==total_pages==== '.$page;
            //echo $data['current_page'].' ==  ';
            $p = ($page / $this->pagination_per_page_album_videos);
            $data['current_loaded_page_no'] = $p + 1;
            $data['is_post_'] = $this->session->userdata('is_post_');


            # loading the view-part...

            $VIEW_FILE = "logged/videos/my_album_all_videos_ajax.phtml";
            if (is_array($result) && count($result)) {
                $content = $this->load->view($VIEW_FILE, $data, true);
                //echo $this->load->view( $VIEW_FILE , $data, true);
            } else {
                #$content = '<div class="txt_content01"><p style="margin-left: 210px;">No Posts!</p></div>';;
                #$content = '<div class="section01" style="padding-top:5px;"><div  class="shade_norecords" style="width:260px;"><p class="blue_bold12">No Videos.</p></div></div>';
                $content = '';
            }

            echo json_encode(array('html' => $content, 'current_page' => $current_page, 'total_videos' => $total_rows, 'view_more' => $view_more));
            //echo  $this->load->view('logged/videos/my_all_videos_ajax.phtml', $data,TRUE);
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    //------------------------------------- edit --------------------------------------
    function edit_individual_video() {

        try {
            $arr_messages = array();

            if ($_POST) {
                $album_id = $this->input->post('album_id');
                # error message trapping...
                if (trim($this->input->post('txt_title_edit')) == '') {
                    $arr_messages['txt_title_edit'] = "* Required Field";
                }
                /*        if( trim($this->input->post('txt_artist_edit'))=='') 
                  {
                  $arr_messages['txt_artist_edit'] = "* Required Field";
                  }
                  if( trim($this->input->post('txt_genre_edit'))=='')
                  {
                  $arr_messages['txt_genre_edit'] = "* Required Field";
                  }
                 */
                if (trim($this->input->post('txt_video_track_album_edit')) == '') {
                    $arr_messages['txt_video_track_album_edit'] = "* Required Field";
                }
                if (trim($this->input->post('txtarea_desc_video_edit')) == '') {
                    $arr_messages['txtarea_desc_video_edit'] = "* Required Field";
                }

                if (trim($this->input->post('select_album1_edit')) == '-1') {
                    $arr_messages['select_album1_edit'] = "* Required Field";
                } elseif (trim($this->input->post('select_album1_edit')) == '0') {
                    $new_album = false;
                    if (trim($this->input->post('txt_nw_album_edit')) == '') {
                        $arr_messages['txt_nw_album_edit'] = "* Required Field";
                    } else {
                        $info_nw_album['s_name'] = get_formatted_string($this->input->post('txt_nw_album'));
                        $info_nw_album['i_user_id'] = intval(decrypt($this->session->userdata('user_id')));
                        $info_nw_album['e_privacy'] = 'everyone';
                        $info_nw_album['dt_created_on'] = get_db_datetime();

                        $new_album = true;
                    }
                } else {
                    //$info['i_video_album_id'] = trim($this->input->post('select_album1'));
                }

				if(trim($this->input->post('txt_video_track_album_edit')) != '' || trim($this->input->post('select_album1_edit')) != '0' || trim($this->input->post('select_album1_edit')) != '-1' )
					{
					$returns=match_video(intval(decrypt($this->session->userdata('user_id'))),trim($this->input->post('select_album1_edit')),trim($this->input->post('txt_video_track_album_edit')),$this->input->post('id'));
					
					if($returns != '')
					{
					$arr_messages['txt_video_track_album_edit'] = "* Video already exists";
					}
					}



                if ($this->input->post('txt_video_track_album_edit') != '') {

                    try {

                        $this->load->library('embed_video');
                        $config['zend_library_path'] = APPPATH . "libraries/Zend/";
                        $config['video_url'] = trim($this->input->post('txt_video_track_album_edit'));

                        $this->embed_video->initialize($config);
                        //echo 'video_url='.$this->embed_video->video_url;
                        $this->embed_video->prepare();

                        $this->embed_video->save_thumb($this->upload_path, '-bigthumb', 122, 82);
                        $this->video_img_name = $this->embed_video->get_resized_imagename();
                    } catch (Exception $e) {
                        $arr_messages['txt_video_track_album_edit'] = "* Not valid video URL";
                    }
                }



                if (count($arr_messages) == 0) {

                    $info = array();
                    $info['id'] = $this->input->post('id');
                    $img = $this->input->post('s_video_image');
                    $img_path = BASEPATH . "../uploads/user_videos/" . getThumbName($img, 'bigthumb');
                    @unlink($img_path);

                    $info['i_user_id'] = intval(decrypt($this->session->userdata('user_id')));

                    if ($new_album) {
                        $nw_album_id = $this->my_videos_model->create_video_album_at_video_upload($info_nw_album);
                        $info['i_video_album_id'] = $nw_album_id;   // setting new video album id
                    } else
                        $info['i_video_album_id'] = trim($this->input->post('select_album1_edit'));

                    $info['s_video_file_name'] = trim($this->input->post('txt_video_track_album_edit'));
                    //$this->video_img_name = $this->embed_video->get_resized_imagename();
                    $info['s_video_image'] = $this->video_img_name;
                    //$info['s_video_image']     = trim($this->input->post('txt_video_track_album'));

                    $info['s_title'] = get_formatted_string($this->input->post('txt_title_edit'));
                    $info['s_artist'] = get_formatted_string($this->input->post('txt_artist_edit'));
                    $info['s_genre'] = trim($this->input->post('txt_genre_edit'));

                    $info['s_description'] = trim($this->input->post('txtarea_desc_video_edit'));

                    $info["i_order"] = 1 + $this->my_videos_model->get_i_order($info['i_video_album_id']);


                    $info['dt_updated_on'] = get_db_datetime();


                    //pr($arr_messages);
                    //pr($info,1);

                    $_ret = $this->my_videos_model->update_video($info);

                    ### for all videos  ###
                    ob_start();
                    $this->my_album_videos_ajax_pagination($album_id);
                    $content = ob_get_contents();
                    $content_obj = json_decode($content);
                    $html = $content_obj->html;
                    ob_end_clean();
                    ### end all albums  ###




                    echo json_encode(array('success' => true, 'arr_messages' => $arr_messages, 'msg' => 'Video File Uploaded Successfully.', 'html' => $html));
                }
                else {
                    echo json_encode(array('success' => false, 'arr_messages' => $arr_messages, 'msg' => t('Error!')));
                }
            }   //$_POST
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    //------------------------------------ delete single data --------------------------------
    function delete_video_single() {
        $album_id = $this->input->post('i_album_id');
        $video_id = $this->input->post('video_id');
        $img_res = $this->my_videos_model->get_video_img_by_id($video_id);
        $img = BASEPATH . "../uploads/user_videos/" . getThumbName($img_res['s_video_image'], 'bigthumb');

        $res = $this->my_videos_model->delete_video($video_id);

        $this->load->model('media_comments_model');
        $this->media_comments_model->delete_by_id($video_id, 'video');

        if ($res) {
            @unlink($img);
            $msg = 'Video File Deleted Successfully.';
            $success = true;
        } else {
            $msg = 'Error.';
            $success = false;
        }
        ### for all videos  ###
        ob_start();
        $this->my_album_videos_ajax_pagination($album_id);
        $content = ob_get_contents();
        $content_obj = json_decode($content);
        $html = $content_obj->html;
        $total = $content_obj->total_videos;
        ob_end_clean();
        ### end all albums  ###




        echo json_encode(array('success' => $success, 'msg' => $msg, 'html' => $html, 'total' => $total));
    }

    //------------------------------------------ delete multiple video -----------------------------------------
    function delete_video() {
        $data = $this->data;
        $del_ids = $this->input->post('video_ids');
        $cur_page = $this->input->post('current_page');
        $del_id_arr = explode(',', $del_ids);
        $album_id = $this->input->post('album_id');

        for ($i = 0; $i < count($del_id_arr); $i++) {
            $img_res = $this->my_videos_model->get_video_img_by_id($del_id_arr[$i]);
            $img = BASEPATH . "../uploads/user_videos/" . getThumbName($img_res['s_video_image'], 'bigthumb');

            $res = $this->my_videos_model->delete_video($del_id_arr[$i]);



            @unlink($img);
            if ($rse) {
                
            }
        }

        ### for all videos  ###
        ob_start();
        $this->my_album_videos_ajax_pagination($album_id);
        $content = ob_get_contents();
        $content_obj = json_decode($content);
        $html = $content_obj->html;
        ob_end_clean();
        ### end all albums  ###

        echo json_encode(array('success' => true, 'msg' => "Selected videos deleted successfully.", 'html' => $html));
    }

    //------------------------------------------ /delete multiple video -----------------------------------------
    //------------------------------------------- /individual album detail --------------------------------------
    //=============================================== like & comments =================================================
    ## post comments  ##

    public function post_comment($feed_id) {

        $this->load->model('media_comments_model');

        $this->load->model('users_model');
        $user_id = intval(decrypt($this->session->userdata('user_id')));
        $user_details = $this->users_model->fetch_this($user_id);

        $message = nl2br(htmlspecialchars(trim($this->input->post('message')), ENT_QUOTES, 'utf-8'));
        $_html = '';
        if ($message != '') {
              $ip = getenv("REMOTE_ADDR") ; 
            $arr['i_media_id'] = $feed_id;
            $arr['s_media_type'] = 'video';
            $arr['i_user_id'] = $user_id;
            $arr['s_contents'] = $message;
            $arr['dt_created_on'] = get_db_datetime();
            $arr['u_ip'] = $ip;
			
//            $this->media_comments_model->insert($arr);
//            $arr['pseudo'] = $user_details['s_profile_name'];
//            $data['comment'] = $arr;
            //$data['total_comments'] = $this->media_comments_model->get_total_by_newsfeed_id($feed_id);
            //$comment = $this->load->view('newsfeed/my_profile_single_feed_comment.phtml', $data, true);
                   $count = count_photo_comment_link($feed_id, 'video');
                   $count_comment = $count+1;
            $_html = '' . "Comments " . " (" .$count_comment. ")";
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
                "s_media_type" => 'video');
        } else if ($like_or_unlike == "Unlike") {
            $like_unlike_information_array = array("i_media_id" => $window_id,
                "i_unliked_user_id" => $liked_user_id,
                "dt_unliked_on" => $log_time,
                "s_media_type" => 'video');
        }

        $status = 0;
        $response = $this->media_comments_model->postLikeUnlike($like_unlike_information_array, strtolower($like_or_unlike));

        //pr($response);

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

            $_html = '' . "Liked by " . " (" . count_photo_like_link($window_id, 'video') . ")";
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

            $VIEW = "logged/videos/comments/my_video_view_comments_lightbox.phtml";
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
            $result = $this->media_comments_model->get_by_newsfeed_id($i_media_id, 'video', $page, $this->comments_pagination_per_page);
            $resultCount = count($result);
            $total_rows = $this->media_comments_model->get_total_by_newsfeed_id($i_media_id, 'video');
            //pr($result);         

            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url() . "logged/my_videos/comments_ajax_pagination/{$i_media_id}";
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
            $VIEW_FILE = "logged/videos/comments/my_video_view_comments_lightbox_ajax.phtml";
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

            $VIEW = "logged/videos/comments/liked_by_lightbox.phtml";
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
            $result = $this->media_comments_model->get_people_liked_by_newsfeed_id($i_media_id, 'video', $page, $this->people_liked_pagination_per_page);
            $resultCount = count($result);
            $total_rows = $this->media_comments_model->get_total_people_liked_by_newsfeed_id($i_media_id, 'video');
            //pr($result);         

            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url() . "logged/my_videos/fetch_people_liked_post_ajax/{$i_media_id}";
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
            $VIEW_FILE = "logged/videos/comments/liked_by_lightbox_ajax.phtml";
            $this->load->view($VIEW_FILE, $data);
            //return $html;
        } catch (Exception $err_obj) {
            
        }
    }

    ########### NEW FETCH COMMENTS ON WALL ###########

    public function new_fetch_likes_on_videos($i_media_id = '') {
        try {
            $data = $this->data;
            $html = '';
            $result = $this->media_comments_model->get_people_liked_by_newsfeed_id($i_media_id, 'video');

            //pr($result);

            if (count($result)) {
                foreach ($result as $key => $val) {

                    $name = $val['s_profile_name'];
                    $profile_image = get_profile_image_of_user('thumb',$info['s_profile_photo'],$info['e_gender']);

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

    public function NEW_fetch_comment_videos($i_media_id = '') {
        try {
            $data = $this->data;
            $html = '';
            $result = $this->media_comments_model->get_by_newsfeed_id($i_media_id, 'video');

            //pr($result);
            if (count($result)) {

                foreach ($result as $key => $val) {

                    $profile_image_filename = get_profile_image_of_user('thumb',$info['s_profile_photo'],$info['e_gender']);
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

//============================================= end of like & comments ===========================================
}

// end of controller...