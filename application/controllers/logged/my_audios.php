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
include(APPPATH . 'controllers/base_controller.php');

class My_audios extends Base_controller {

    private $pagination_per_page = 5;
    private $album_pagination_per_page = 3;
    private $all_album_pagination_per_page = 15;
    private $comments_pagination_per_page = 10;
    private $people_liked_pagination_per_page = 10;
    private $new_pagination_per_page = 5;
    private $upload_path;
    private $upload_tmp_path;

    public function __construct() {
        try {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # configuring required paths & folders...
            $this->upload_path_music_full = BASEPATH . '../uploads/user_audio_files/';

            $this->load->helper('wall_helper');
            $this->load->helper('common_option_helper');
            $this->load->model('users_model');
            $this->load->model('user_audios_model');
            $this->load->model('audio_albums_model');
            $this->load->model('media_comments_model');

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
                'js/frontend/logged/my_audio/my_audio.js',
                'js/frontend/logged/my_audio/audio_helper.js',
                'js/frontend/logged/tweets/tweet_utilities.js',
            ));

            // Sound-manager js, css
            parent::_add_js_arr(array('js/jwplayer/jwplayer.js'));

            // End Sound-manager js, css

            parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
                'css/dd.css'));

            ############################################################

            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
            $data['page_view_type'] = 'myaccount';

            $arr_profile_info = $this->users_model->fetch_this($i_user_id);
            parent::_set_all_audio_album_data($i_user_id);

            #### FETCHING PHOTOS PER USER 
            if (is_array($arr_profile_info) && !empty($arr_profile_info)) {
                $data['arr_profile_info'] = $arr_profile_info;
                $data['arr_photos'] = $this->user_audios_model->get_by_user_id($i_user_id, null, 0, $this->pagination_per_page);

                $data['total_albums'] = $this->audio_albums_model->get_total_by_user_id($i_user_id);
                $data['total_audios'] = $this->user_audios_model->get_total_by_user_id($i_user_id, '');



                ###fetching all photo to show in slideshow

                $this->session->set_userdata('search_condition', '');

                ob_start();
                $this->audio_ajax_pagination($i_user_id);
                $content = ob_get_contents();
                $content_obj = json_decode($content);
                $data['result_content'] = $content_obj->html;
                $data['no_of_result'] = $content_obj->no_of_result;
                ob_end_clean();


                ob_start();
                $this->albums_ajax_pagination($i_user_id);
                $data['album_result_content'] = ob_get_contents();
                ob_end_clean();
            }

            # view file...
            $VIEW = "logged/audios/my_audios.phtml";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

    public function albums_ajax_pagination($i_user_id, $page = 0) {

        try {
            $data = $this->data;

            $result = $this->audio_albums_model->get_by_user_id($i_user_id, $page, $this->album_pagination_per_page);
            $total_rows = $this->audio_albums_model->get_total_by_user_id($i_user_id);

            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url() . 'logged/my_audios/albums_ajax_pagination/' . $i_user_id;
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


            $VIEW_FILE = "logged/audios/load_my_albums_audios_listing_ajax.phtml";
            $this->load->view($VIEW_FILE, $data);
        } catch (Exception $err_obj) {
            
        }
    }

    public function audio_ajax_pagination($i_user_id = '', $page = 0) {
        //echo $page;
        ## seacrh conditions : filter ############
        $WHERE_COND = '';
        if (isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y') :

            $s_title = get_formatted_string(trim($this->input->post('txt_title')));
            $WHERE_COND .= ($s_title == '') ? '' : " AND s_title LIKE '%" . $s_title . "%' ";

            $s_artist = get_formatted_string(trim($this->input->post('txt_artist')));
            $WHERE_COND .= ($s_artist == '') ? '' : " AND s_artist LIKE '%" . $s_artist . "%' ";

            $s_genre_id = get_formatted_string(trim($this->input->post('txt_genre')));
            $WHERE_COND .= ($s_genre_id == '-1') ? '' : " AND s_genre_id LIKE '%" . $s_genre_id . "%' ";

            $s_sound_track_album = get_formatted_string(trim($this->input->post('txt_track_album')));
            $WHERE_COND .= ($s_sound_track_album == '') ? '' : " AND s_sound_track_album LIKE '%" . $s_sound_track_album . "%' ";


            $this->session->set_userdata('search_condition', $WHERE_COND);


        endif;
        $s_where = $this->session->userdata('search_condition');

        $cur_page = $page + $this->pagination_per_page;

        $data = $this->data;
        $result = $this->user_audios_model->get_by_user_id($i_user_id, $s_where, intval($page), $this->pagination_per_page);
        $total_rows = $this->user_audios_model->get_total_by_user_id($i_user_id, $s_where);

        //--- for check whether more  are there or not
        $view_more = true;
        $rest_counter = $total_rows - $page;
        if ($rest_counter <= $this->pagination_per_page)
            $view_more = false;


        //--------- end check
        #pr($result,1);
        $data['arr_photos'] = $result;
        $data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
        $data['profile_id'] = $i_user_id;

        $VIEW_FILE = "logged/audios/load_my_audio_listing_ajax.phtml";

        if (is_array($result) && count($result)) {
            $content = $this->load->view($VIEW_FILE, $data, true);
        } else {
            $content = '';
        }

        echo json_encode(array('html' => $content, 'current_page' => $cur_page, 'no_of_result' => $data['no_of_result'], 'total' => $total_rows, 'view_more' => $view_more));
    }

    public function view_all_audio_albums($id = '') {
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
                'js/frontend/logged/my_audio/my_audio.js',
                'js/frontend/logged/my_audio/audio_helper.js',
                'js/frontend/logged/tweets/tweet_utilities.js',
            ));

            // Sound-manager js, css
            parent::_add_js_arr(array(
                'js/jQuery.jPlayer.2.2.0/jquery.jplayer.min.js',
                'js/jquery.mb.miniAudioPlayer-1.6.5/inc/jquery.mb.miniPlayer.js',
                'js/jquery/external/jquery.metadata.js'
            ));
            parent::_add_css_arr(array('css/miniplayer.css'));
            // End Sound-manager js, css

            parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
                'css/dd.css'));

            ############################################################
            if (intval($id) <= 0) {
                $i_user_id = intval(decrypt($this->session->userdata('user_id')));
                $data['page_view_type'] = 'myaccount';
            }
            $arr_profile_info = $this->users_model->fetch_this($i_user_id);
            parent::_set_all_audio_album_data($i_user_id);

            #### FETCHING PHOTOS PER USER 
            if (is_array($arr_profile_info) && !empty($arr_profile_info)) {
                $data['arr_profile_info'] = $arr_profile_info;
                $data['arr_photos'] = $this->user_audios_model->get_by_user_id($i_user_id, null, 0, $this->pagination_per_page);
                ###fetching all photo to show in slideshow
                $this->session->set_userdata('search_condition', '');

                ob_start();
                $this->all_albums_ajax_pagination($i_user_id);
                $data['album_result_content'] = ob_get_contents();
                ob_end_clean();
            }

            # view file...
            $VIEW = "logged/audios/my_all_audio_albums.phtml";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

    public function all_albums_ajax_pagination($i_user_id, $page = 0) {

        try {
            //pr($this->uri->segments);
            $data = $this->data;

            $result = $this->audio_albums_model->get_by_user_id($i_user_id, $page, $this->all_album_pagination_per_page);
            //pr($result);
            $total_rows = $this->audio_albums_model->get_total_by_user_id($i_user_id);

            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url() . 'logged/my_audios/all_albums_ajax_pagination/' . $i_user_id;
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->all_album_pagination_per_page;
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

            $data['total_pages'] = ceil($total_rows / $this->all_album_pagination_per_page);

            $p = ($page / $this->all_album_pagination_per_page);
            $data['current_loaded_page_no'] = $p + 1;


            $VIEW_FILE = "logged/audios/all_audio_albums_ajax.phtml";
            $this->load->view($VIEW_FILE, $data);
        } catch (Exception $err_obj) {
            
        }
    }

    public function add_new_track() {
        $arr_messages = array();

        $user_id = intval(decrypt($this->session->userdata('user_id')));

        ## validation  part
        if (trim($this->input->post('txt_title') == '')) {
            $arr_messages['title'] = '* Required Field.';
        }

        if (trim($this->input->post('txt_artists') == '')) {
            $arr_messages['artists'] = '* Required Field.';
        }

        if (trim($this->input->post('txt_genre') == '-1')) {
            $arr_messages['genre'] = '* Required Field.';
        }


        if (trim($this->input->post('select_album1') == '-1')) {
            $arr_messages['album1'] = '* Required Field.';
        }


        # audio-file uploading part...
        if (isset($_FILES['track_music_file']['name']) && $_FILES['track_music_file']['name'] != '') {
            $ext_arr = get_ext($_FILES['track_music_file']['name']);

            #$music_ext = $ext_arr['ext'];
            $music_ext = strtolower($ext_arr['ext']);
            $music_filename = $ext_arr['filename'];

            if (!in_array($music_ext, $this->config->item('VALID_MUSIC_EXT'))) {
                $arr_messages['track_music_file'] = 'Allowed extension is mp3';
            }
        } else {
            $arr_messages['track_music_file'] = '* Required Field.';
        }

        $MAX_AUDIO = $this->data['site_settings_arr']['i_max_audio_upload'];
        $TOTAL_AUDIO = $this->user_audios_model->get_total_by_user_id($user_id);


        if (count($arr_messages) == 0 && ($TOTAL_AUDIO < $MAX_AUDIO || $MAX_AUDIO == 0 )) {
            //die(22);
            $arr_data["s_title"] = htmlspecialchars($this->input->post('txt_title'), ENT_QUOTES, 'utf-8');


            ##### uploading audio ######

            if (isset($_FILES['track_music_file']['name']) && $_FILES['track_music_file']['name'] != '') {
                $ext_arr = get_ext($_FILES['track_music_file']['name']);

                #$music_ext = $ext_arr['ext'];
                $music_ext = strtolower($ext_arr['ext']);
                $music_filename = $ext_arr['filename'];

                if (in_array($music_ext, $this->config->item('VALID_MUSIC_EXT'))) {
                    $music_filename = createImageName($music_filename);

                    //echo $this->upload_path_music_full.$music_filename.'.'.$music_ext; 

                    if (test_file($this->upload_path_music_full . $music_filename . '.' . $music_ext)) {
                        for ($i = 0; test_file($this->upload_path_music_full . $music_filename . '-' . $i . '.' . $music_ext); $i++) {
                            
                        }

                        $new_music_filename = $music_filename . '-' . $i;
                    } else {
                        $new_music_filename = $music_filename;
                    }

                    $this->music_filename = $new_music_filename;

                    $this->upload_music_filename = $this->upload_path_music_full . $new_music_filename . '.' . $music_ext;

                    move_uploaded_file($_FILES['track_music_file']['tmp_name'], $this->upload_music_filename);
                } else {
                    $arr_messages['track_music_file'] = 'Allowed extension is mp3';
                }
            }
            ##### uploading audio ######



            if (isset($_FILES['track_music_file']['name']) && $_FILES['track_music_file']['name'] != '') {
                $arr_data["s_audio_file_name"] = $new_music_filename . '.' . $music_ext;
            }


            $arr_data['i_user_id'] = $user_id;
            $arr_data['i_id_audio_album'] = $this->input->post('select_album1');
            $arr_data['s_artist'] = get_formatted_string($this->input->post('txt_artists'));
            $arr_data['s_genre_id'] = trim($this->input->post('txt_genre'));
            $arr_data['s_desc'] = get_formatted_string($this->input->post('ta_desc'));
            $arr_data['s_sound_track_album'] = get_formatted_string($this->input->post('txt_album_name'));


            $arr_data["i_order"] = 1 + $this->audio_albums_model->get_i_order($arr_data['i_id_audio_album']);
            $cur_date = get_db_datetime();
            $arr_data['dt_created_on'] = $cur_date;
            $arr_data['dt_updated_on'] = $cur_date;


            $track_id = $this->user_audios_model->insert($arr_data);
            //print_r($arr_data);
            //echo $this->db->last_query();exit;
            //echo json_encode(array('result'=>'success', 'album_id'=>$album_id));
            echo json_encode(array('success' => true, 'arr_messages' => $arr_messages, 'album_id' => $album_id, 'msg' => 'Audio Uploaded Successfully.'));
            exit;
        } else if (count($arr_messages) == 0 && $TOTAL_AUDIO == $MAX_AUDIO) {
            @unlink($this->upload_music_filename);
            echo json_encode(array('success' => false, 'msg' => 'Audio cannot be uploaded as maximum audio upload limit reached!', 'album_id' => $album_id, 'maxlimit' => true));
            exit;
        } else {

            @unlink($this->upload_music_filename);
            //echo json_encode(array('result'=>'error', 'arr_messages'=>$arr_messages, 'album_id'=>$album_id));
            echo json_encode(array('success' => false, 'arr_messages' => $arr_messages, 'msg' => 'Error!', 'album_id' => $album_id));
            exit;
        }
    }

    public function get_photo_album_info($i_album_id) {

        $data = $this->data;
        $data['photo_album_detail_arr'] = $this->audio_albums_model->get_by_id($i_album_id);
        //pr($data['photo_album_detail_arr'],1);
        $VIEWS = "logged/photos/load_photo_album_details.phtml";
        $html = $this->load->view($VIEWS, $data, true);
        //echo $html; exit;

        echo json_encode(array('result' => 'success', 'album_html' => $html));
        exit;
    }

    public function delete($rec_id, $album_id) {
        $data = $this->data;
        $audio_arr = $this->user_audios_model->get_by_id($rec_id);
        //pr($photo_arr);
        $ret = $this->user_audios_model->delete_by_id($rec_id);
        @unlink($this->upload_path_music_full . $audio_arr['s_audio_file_name']);
        //@unlink($this->upload_path_music_full . $photo_arr['s_audio_file_name']);


        $re_page = base_url() . "audio-albums/" . $album_id . "/organize-audio.html";
        header("location:" . $re_page);
        exit;
    }

    public function delete_sel_photos() {
        //$messages = $this->input->post('csv_mail_ids');

        $this->load->model('data_messages_model');

        $current_page = $this->input->post('current_page');

        foreach (explode(',', $this->input->post('csv_mail_ids')) as $photo_id) {
            $photo_arr = $this->user_audios_model->get_by_id($photo_id);
            $ret = $this->user_audios_model->delete_by_id($photo_id);
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
            $data['arr_allphotos'] = $this->user_audios_model->get_by_user_id($i_user_id);
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
$this->load->model('user_alert_model');

        $this->load->model('users_model');
        $user_id = intval(decrypt($this->session->userdata('user_id')));
        $user_details = $this->users_model->fetch_this($user_id);

        $message = nl2br(htmlspecialchars(trim($this->input->post('message')), ENT_QUOTES, 'utf-8'));
        $_html = '';
        if ($message != '') {
             $ip = getenv("REMOTE_ADDR") ; 
            $arr['i_media_id'] = $feed_id;
            $arr['s_media_type'] = 'audio';
            $arr['i_user_id'] = $user_id;
            $arr['s_contents'] = $message;
            $arr['dt_created_on'] = get_db_datetime();
            $arr['u_ip'] = $ip; 
			
//            $this->media_comments_model->insert($arr);
//            $arr['pseudo'] = $user_details['s_profile_name'];
//            $data['comment'] = $arr;
//            //$data['total_comments'] = $this->media_comments_model->get_total_by_newsfeed_id($feed_id);
//            //$comment = $this->load->view('newsfeed/my_profile_single_feed_comment.phtml', $data, true);
//
//            $_html = '' . "Comments " . " (" . count_photo_comment_link($feed_id, 'audio') . ")";
//
//            ob_start();
//            $this->new_comments_ajax_pagination($feed_id);
//            $content = ob_get_contents();
//            $content_obj = json_decode($content);
//            $comments_list_html = $content_obj->html;
//            $no_of_result = $content_obj->no_of_result;
//            $current_page_1 = $content_obj->current_page;
//            $view_more = $content_obj->view_more;
//
//            ob_end_clean();
            $is_abusive = 0;
            $is_abusive = check_abusive_words($message);
            if ($is_abusive > 0) {
                echo json_encode(array('success' => 'false', 'msg' => "Abusive Words are not allowed", 'html' => $_html,
                    'comments_list_html' => $comments_list_html,
                    'no_of_result' => $no_of_result));
                exit;
            } else {
                $this->media_comments_model->insert($arr);
				$owner_id=get_audio_ownerID_by_id($feed_id);
				if($owner_id != $user_id)
				{
			
				$email_opt = $this->user_alert_model->check_option_email_user_id($user_id);
						if($email_opt['e_audio_comments_received'] == 'Y'){
						
						$mail_arr['i_requester_id'] =get_username_by_id( $user_id);
						$mail_arr['i_accepter_id'] =  get_username_by_id($owner_id);
						$mail_arr['s_type'] = 'e_audio_comments_received';
						$mail_arr['s_title']=get_audio_title($feed_id);
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

					$this->email->subject($mail_arr["i_requester_id"].' commented on your audio.');
					$this->email->message("$body");

					$this->email->send();
					}
				}
                $arr['pseudo'] = $user_details['s_profile_name'];
                $data['comment'] = $arr;
                //$data['total_comments'] = $this->media_comments_model->get_total_by_newsfeed_id($feed_id);
                //$comment = $this->load->view('newsfeed/my_profile_single_feed_comment.phtml', $data, true);

                $_html = '' . "Comments " . " (" . count_photo_comment_link($feed_id, 'audio') . ")";

                ob_start();
                $this->new_comments_ajax_pagination($feed_id);
                $content = ob_get_contents();
                $content_obj = json_decode($content);
                $comments_list_html = $content_obj->html;
                $no_of_result = $content_obj->no_of_result;
                $current_page_1 = $content_obj->current_page;
                $view_more = $content_obj->view_more;

                ob_end_clean();
                echo json_encode(array('success' => 'true', 'msg' => "Comment posted successfully.", 'html' => $_html,
                    'comments_list_html' => $comments_list_html,
                    'no_of_result' => $no_of_result));
                exit;
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
                "s_media_type" => 'audio');
        } else if ($like_or_unlike == "Unlike") {
            $like_unlike_information_array = array("i_media_id" => $window_id,
                "i_unliked_user_id" => $liked_user_id,
                "dt_unliked_on" => $log_time,
                "s_media_type" => 'audio');
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

            $_html = '' . "Liked by " . " (" . count_photo_like_link($window_id, 'audio') . ")";
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
            $result = $this->media_comments_model->get_by_newsfeed_id($i_media_id, 'audio', $page, $this->comments_pagination_per_page);
            $resultCount = count($result);
            $total_rows = $this->media_comments_model->get_total_by_newsfeed_id($i_media_id, 'audio');
            //pr($result); 		

            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url() . "logged/my_audios/comments_ajax_pagination/{$i_media_id}";
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
            $VIEW_FILE = "logged/audios/comments/my_audios_view_comments_lightbox_ajax.phtml";
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

            $VIEW = "logged/audios/comments/liked_by_lightbox.phtml";
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
            $result = $this->media_comments_model->get_people_liked_by_newsfeed_id($i_media_id, 'audio', $page, $this->people_liked_pagination_per_page);
            $resultCount = count($result);
            $total_rows = $this->media_comments_model->get_total_people_liked_by_newsfeed_id($i_media_id, 'audio');
            //pr($result); 		

            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url() . "logged/my_audios/fetch_people_liked_post_ajax/{$i_media_id}";
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
            $VIEW_FILE = "logged/audios/comments/liked_by_lightbox_ajax.phtml";
            $this->load->view($VIEW_FILE, $data);
            //return $html;
        } catch (Exception $err_obj) {
            
        }
    }

    ########### NEW FETCH COMMENTS ON WALL ###########

    public function new_fetch_likes_on_audios($i_media_id = '') {
        try {
            $data = $this->data;
            $html = '';
            $result = $this->media_comments_model->get_people_liked_by_newsfeed_id($i_media_id, 'audio');

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

    public function NEW_fetch_comment_audios($i_media_id = '') {
        try {
            $data = $this->data;
            $html = '';
            $result = $this->media_comments_model->get_by_newsfeed_id($i_media_id, 'audio');

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

    public function new_comments_ajax_pagination($i_media_id, $page = 0) {


        $cur_page = $page + $this->new_pagination_per_page;
        $data = $this->data;

        $result = $this->media_comments_model->get_by_newsfeed_id($i_media_id, 'audio', $page, $this->new_pagination_per_page);
        $total_rows = $this->media_comments_model->get_total_by_newsfeed_id($i_media_id, 'audio');

        $data['result_arr'] = $result;
        $data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
        $data['photo_id'] = $i_media_id;

        //--- for check end of he page.
        $view_more = true;
        $rest_counter = $total_rows - $page;
        if ($rest_counter <= $this->new_pagination_per_page)
            $view_more = false;
        //--------- end check


        $VIEW_FILE = "public_profile/public_profile_view_all_audio/audio_comment_ajax_list.phtml";

        if (is_array($result) && count($result)) {
            $content = $this->load->view($VIEW_FILE, $data, true);
        } else {
            $content = '';
        }

        echo json_encode(array('html' => $content, 'current_page' => $cur_page, 'no_of_result' => $data['no_of_result'], 'total' => $total_rows, 'view_more' => $view_more, 'cur_page' => $data['current_page_1']));
    }

}

// end of controller...

