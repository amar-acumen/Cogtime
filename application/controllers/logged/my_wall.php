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

class My_wall extends Base_controller {

    private $pagination_per_page = 2;
    private $comments_pagination_per_page = 10;
    private $people_liked_pagination_per_page = 10;

    public function __construct() {
        try {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            $this->upload_tmp_path = BASEPATH . '../uploads/wall_tmp/';
            $this->upload_path = BASEPATH . '../uploads/wall_photos/';
            $this->upload_photo_path = BASEPATH . '../uploads/wall_photos/';


            # loading reqired model & helpers...
            $this->load->helper('wall_helper');
            $this->load->model('users_model');
            $this->load->model('data_newsfeed_model');
            $this->load->model('newsfeed_comments_model');
            $this->load->model('bible_fruits_model');
            $this->load->model('holy_place_model');
            $this->load->model('intercession_model');
            $this->load->model('prayer_wall_model');
            $this->load->model('projects_model');
            $this->load->model('prayer_commit_model');
            $this->load->model('prayer_group_model');
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function index($s_member_type = '') {
        try {

            $posted = array();
            $this->data["posted"] = $posted; /* don't change */
            $data = $this->data;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');


            parent::_add_js_arr(array(/*'js/ddsmoothmenu.js',
                'js/switch.js', 'js/animate-collapse.js', 'js/ModalDialog.js',
                'js/lightbox.js', 'js/jquery.dd.js', //'js/jquery-ui-1.8.2.custom.min.js',//comment out to reduce page load time-sanhita
                'js/stepcarousel.js',
                'js/tab.js',*/
                'js/ajaxupload.js',
                'js/frontend/wall/wall_photo_upload.js',
                'js/frontend/wall/wall_helper.js',
                //'js/frontend/logged/tweets/tweet_utilities.js',
                    /* 'chat/js/chat.js' */
            ));

            parent::_add_css_arr(array(//'css/jquery-ui-1.8.2.custom.css',
            ));


            $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
            $data['page_view_type'] = 'myaccount';
            $this->load->model('users_model');
            $arr_profile_info = $this->users_model->fetch_this($i_profile_id);
            $data['arr_profile_info'] = $arr_profile_info;

            #### FOR THE FIRST LOGIN ONLY after registration 
            $data['first_login'] = $this->session->userdata('first_login');
            $this->session->unset_userdata('first_login');
            #### FOR THE FIRST LOGIN ONLY after registration 

            $data['is_first_login_in_a_day'] = '';

            if ($this->session->userdata('is_first_login_checked') == 'false') {
                $data['is_first_login_in_a_day'] = $this->users_model->check_user_first_login_in_a_day($i_profile_id);
                //echo $data['is_first_login_in_a_day'];
            }


            if ($data['is_first_login_in_a_day'] == 'true') {
                $this->bible_fruits_model->generate_fruit_list_per_user_id_date();
                $data['five_fruits_arr'] = $this->bible_fruits_model->get_fruit_list($i_profile_id);
            } else {
                $data['five_fruits_arr'] = $this->bible_fruits_model->get_fruit_list($i_profile_id);
            }
            //pr($data['five_fruits_arr']);
            //$data['five_fruits_arr'] = $this->bible_fruits_model->get_fruit_list($i_profile_id);


            $s_inter_where = 'WHERE 1 AND i.i_is_enable  = 1 AND i.e_request_type = "On Going"';
            $data['latest_intercession'] = $this->intercession_model->get_all_intercession($s_inter_where, 0, 1);

            /* 	$rand_verse_id = rand(1, 31102);
              $s_verse_where = " AND v.id =  {$rand_verse_id}";
              $data['rand_bible_verse'] = $this->holy_place_model->get_bible($s_verse_where); */

            $data['rand_bible_verse'] = $this->holy_place_model->getDayverse();


            #### prayer click
            $curr_date = date('Y-m-d h:i:s');

            $emergncy_whr = 'WHERE 1 AND i.i_is_enable  = 1 AND i.e_request_type = "Emergency" AND DATE(i.dt_end_date) >= "' . $curr_date . '"';
            $data['latest_emergency_intercession'] = $this->intercession_model->get_all_intercession($emergncy_whr, 0, 1);

            if (count($data['latest_emergency_intercession'])) {
                $data['total_commits'] = $this->intercession_model->get_total_by_request_id($data['latest_emergency_intercession'][0]['id']);
                $data['isCommitExists'] = $this->intercession_model->CheckIfCommitexists($data['latest_emergency_intercession'][0]['id'], $i_profile_id);
            }


            $skippedPrayerClick = $this->intercession_model->getSkippedPrayerClick_IDs();
            // pr($skippedPrayerClick);

            if (in_array($data['latest_emergency_intercession'][0]['id'], $skippedPrayerClick))
                $data['show_PC'] = false;
            else
                $data['show_PC'] = true;
            #### prayer click
            ## NEWSFEED ##
            ob_start();
            $this->newsfeed_pagination_show_more($i_profile_id);
            $content = ob_get_contents();
            $content_obj = json_decode($content);
            $data['result_newsfeed_content'] = $content_obj->html;
            ob_end_clean();

            ## END NEWSFEED ##
            ## prayer request
            $data['pagination_per_page'] = $this->pagination_per_page;


            ob_start();
            $this->my_all_prayer_request_ajax_pagination($i_profile_id, 0);
            $content = ob_get_contents();
            $content_obj = json_decode($content);
            $data['prayer_req_ajax_content'] = $content_obj->html;
            $data['prayer_no_of_result'] = $content_obj->no_of_result;
            ob_end_clean();
            ## prayer request
            ##project section
            $id = intval(decrypt($this->session->userdata('user_id')));
            $data['user_id'] = $id;
            $data['my_projects'] = $this->projects_model->get_my_project($id);
            $data['my_project_count'] = $this->projects_model->get_my_project_count($id);

            
            //code for netpal qualification mail sent start
            $is_netpal_mail_sent = get_is_netpal_q_mail_sent($i_profile_id);
            if ($is_netpal_mail_sent == 0 || $is_netpal_mail_sent == '') {
                $qualification_params = array();
                $qualification_params = get_user_netpal_qualifications_by_id($i_profile_id);
                //pr($qualification_params);
                $wh = " AND r.i_user_id='" . $i_profile_id . "'";
                $wh1 = " AND inv.i_invited_id='" . $i_profile_id . "'";
                $total_rings = $this->my_ring_model->gettotal_ring_by_user($wh, $wh1);
                $qualification_params['total_rings'] = $total_rings;

                $grp_names = $this->prayer_group_model->get_my_groups_names($i_profile_id);
                $total_prayer_grps = count($grp_names);
                $qualification_params['total_prayer_grps'] = $total_prayer_grps;

                $total_frnds = $qualification_params['total_frnds'];
                $total_commitmnts = $qualification_params['total_commitments'];
                $total_rings = $qualification_params['total_rings'];
                $total_prayer_grps = $qualification_params['total_prayer_grps'];
                $six_months_later = $qualification_params['sixMonthsLater'];
                $join_date = $qualification_params['join_date'];

                $current_time = time();
                $admin_set_q_params_arr = get_netpal_parameters_set_by_admin();
                //pr($admin_set_q_params_arr);
                $min_frnds = $admin_set_q_params_arr['min_friends'];
                $min_months = $admin_set_q_params_arr['min_months'];
                $min_rings = $admin_set_q_params_arr['min_rings'];
                $min_prayer_grps = $admin_set_q_params_arr['min_prayer_grps'];
                $min_pr_commtmnts = $admin_set_q_params_arr['min_pr_comttmnts'];
                $attachmnt_file_name = $admin_set_q_params_arr['t_c_file_name'];
                $attachmnt_file_path = BASEPATH.'../uploads/netpal_t_c_files/' . $attachmnt_file_name;
                if (($total_frnds >= $min_frnds) && ($total_rings >= $min_rings) && ($total_prayer_grps >= $min_prayer_grps) && ($total_commitmnts >= $min_pr_commtmnts) && ($current_time >= $six_months_later)) {
                    $user_data = get_user_data_by_id($i_profile_id);
                    $user_f_name = $user_data[0]['s_first_name'];
                    $user_l_name = $user_data[0] ['s_last_name'];
                    $USERNAME = $user_f_name . ' ' . $user_l_name;
                    $EMAIL = $user_data[0]['s_email'];
                    $replaceArr = array(
                        'name' => $USERNAME,
                    );
					$this->load->helper('html');
					$this->load->library('email');
					 $email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
				$this->email->initialize($email_setting);
                    $this->load->model('mail_contents_model');
                    $mail_info = $this->mail_contents_model->get_by_name("netpal_qualification_mail_content");
                    $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                    $body = sprintf3($body, $replaceArr);
                    $arr['subject'] = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
                    $subject = $arr['subject'];
                    $arr['to'] = $EMAIL;
                    $arr['from_email'] = 'no-reply@cogtime.com';
                    $from = $arr['from_email'];
                    $arr['from_name'] = 'Team Cogtime';
                    $arr['message'] = $body;
                    $arr['attachment'] = $attachmnt_file_path;
                    //dump($arr);
					$this->email->from( $arr['from_email'], $arr['from_name']);
                #dump($arr);
				$this->email->subject($arr['subject']);
						
				$this->email->to($arr['to']);
				$this->email->bcc($arr['bcc']);
				$this->email->message("$body");
                        //send_mail($arr);
				$this->email->send();
                    //send_mail($arr);
                    update_netpal_mail_sent_status($i_profile_id);
                }
            }
            //code for netpal qualification mail sent end
            #pr($data['my_projects']);
            #pr($data['arr_profile_info'] );
            # view file...

            $VIEW = "logged/wall/my_wall.phtml";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

    public function newsfeed_pagination_show_more($i_profile_id, $page = 0) {

        $cur_page = $page + $this->pagination_per_page;
        $data = $this->data;


        $result = $this->data_newsfeed_model->get_newsfeeds_by_user_id($i_profile_id, $page, $this->pagination_per_page);

        $total_rows = $this->data_newsfeed_model->get_total_newsfeeds_by_user_id($i_profile_id);

        $data['result_arr'] = $result;
        $data['no_of_result'] = $total_rows;

        $data['current_page_1'] = $cur_page;
        $data['profile_id'] = $i_profile_id;

        $VIEW_FILE = "newsfeed/my_profile_feeds.phtml";

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

    ##END  SHOWING WALL VIDEOS ##

    public function fetch_comment_on_post($i_newsfeed_id = '') {
        try {
            $data = $this->data;

            ob_start();
            $this->comments_ajax_pagination($i_newsfeed_id);
            $data['comments_list'] = ob_get_contents();
            ob_end_clean();

            $VIEW = "logged/wall/my_wall_view_comments_lightbox.phtml";
            #parent::_render($data, $VIEW); 
            $html = $this->load->view($VIEW, $data, true);
            echo json_encode(array('result' => success, 'html_data' => $html));
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function comments_ajax_pagination($i_newsfeed_id, $page = 0) {
        try {
            $data = $this->data;
            $result = $this->newsfeed_comments_model->get_by_newsfeed_id($i_newsfeed_id, $page, $this->comments_pagination_per_page);
            $resultCount = count($result);
            $total_rows = $this->newsfeed_comments_model->get_total_by_newsfeed_id($i_newsfeed_id);
            //pr($result); 		

            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url() . "logged/my_wall/comments_ajax_pagination/{$i_newsfeed_id}";
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
            $VIEW_FILE = "logged/wall/my_wall_view_comments_lightbox_ajax.phtml";
            $this->load->view($VIEW_FILE, $data);
            //return $html;
        } catch (Exception $err_obj) {
            
        }
    }

    public function fetch_people_liked_post($i_newsfeed_id = '') {
        try {
            $data = $this->data;

            ob_start();
            $this->fetch_people_liked_post_ajax($i_newsfeed_id);
            $data['people_liked_list'] = ob_get_contents();
            ob_end_clean();

            $VIEW = "logged/wall/liked_by_lightbox.phtml";
            #parent::_render($data, $VIEW); 
            $html = $this->load->view($VIEW, $data, true);
            echo json_encode(array('result' => success, 'html_data' => $html));
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function fetch_people_liked_post_ajax($i_newsfeed_id, $page = 0) {
        try {
            $data = $this->data;
            $result = $this->newsfeed_comments_model->get_people_liked_by_newsfeed_id($i_newsfeed_id, $page, $this->people_liked_pagination_per_page);
            $resultCount = count($result);
            $total_rows = $this->newsfeed_comments_model->get_total_people_liked_by_newsfeed_id($i_newsfeed_id);
            //pr($result); 		

            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url() . "logged/my_wall/fetch_people_liked_post_ajax/{$i_newsfeed_id}";
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
            $VIEW_FILE = "logged/wall/liked_by_lightbox_ajax.phtml";
            $this->load->view($VIEW_FILE, $data);
            //return $html;
        } catch (Exception $err_obj) {
            
        }
    }

    public function delete_post($i_newsfeed_id = '') {
        try {
            $ret_ = $this->data_newsfeed_model->delete_by_id($i_newsfeed_id);

            echo json_encode(array('result' => 'success', 'msg' => 'Post has been successfully deleted.'));
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    ### prayer request

    public function my_all_prayer_request_ajax_pagination($i_user_id, $page = 0) {

        ## seacrh conditions : filter ############
        $WHERE_COND = "WHERE 1 AND p.i_isenabled  = 1 AND p.i_user_id = {$i_user_id}";

        $s_where = $WHERE_COND;
        //exit;
        $cur_page = $page + $this->pagination_per_page;

        $data = $this->data;
        $s_order_by = " p.dt_end_date ASC ";

        $result = $this->prayer_wall_model->get_list_prayers_request($s_where, intval($page), $this->pagination_per_page, $s_order_by);
        //echo $this->db->last_query(); exit;
        $total_rows = $this->prayer_wall_model->get_count_list_prayers_request($s_where);
        //pr($result,1);
        $data['arr_prayer_request'] = $result;
        $data['prayer_no_of_result'] = $total_rows;
        $data['prayer_current_page'] = $cur_page;
        $data['profile_id'] = $i_user_id;

        //--- for check end of he page.
        $view_more = true;
        $rest_counter = $total_rows - $page;
        if ($rest_counter <= $this->pagination_per_page)
            $view_more = false;
        //--------- end check


        $VIEW_FILE = "logged/wall/my_prayer_request_ajax.phtml";

        if (is_array($result) && count($result)) {
            $content = $this->load->view($VIEW_FILE, $data, true);
        } else {
            $content = '';
        }

        //echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode(array('html' => $content, 'no_of_result' => $data['prayer_no_of_result'], 'view_more' => $view_more, 'cur_page' => $data['prayer_current_page']));
    }

    public function fetch_commit_on_prayer($i_prayer_id = '') {
        try {
            $data = $this->data;

            ob_start();
            $this->commits_ajax_pagination($i_prayer_id);
            $data['comments_list'] = ob_get_contents();
            ob_end_clean();

            $VIEW = "logged/wall/my_wall_view_comments_lightbox.phtml";
            #parent::_render($data, $VIEW); 
            $html = $this->load->view($VIEW, $data, true);
            echo json_encode(array('result' => success, 'html_data' => $html));
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function commits_ajax_pagination($prayer_id, $page = 0) {
        try {
            $data = $this->data;

            $result = $this->prayer_commit_model->get_by_request_id($prayer_id, intval($page), $this->comments_pagination_per_page);
            //echo $this->db->last_query();
            $total_rows = $this->prayer_commit_model->get_total_by_request_id($prayer_id);
            //pr($result); 		

            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url() . "logged/my_wall/commits_ajax_pagination/{$prayer_id}";
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



            $config['div'] = '#view_prayer_commit'; /* Here #content is the CSS selector for target DIV */
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
            $VIEW_FILE = "logged/wall/my_wall_view_commitments_lightbox_ajax.phtml";
            $this->load->view($VIEW_FILE, $data);
            //return $html;
        } catch (Exception $err_obj) {
            
        }
    }

    ########### NEW FETCH COMMENTS ON WALL ###########

    public function new_fetch_likes_on_wallpost($i_newsfeed_id = '') {
        try {
            $data = $this->data;
            $html = '';
            $result = $this->newsfeed_comments_model->get_people_liked_by_newsfeed_id($i_newsfeed_id);

            //pr($result);

            if (count($result)) {
                foreach ($result as $key => $val) {

                    $name = $val['s_profile_name'];
                    $profile_image = get_profile_image_of_user('thumb',$val['s_profile_photo'],$val['e_gender']);

                    $profile_link = get_profile_url($val['post_owner_user_id'], $val['s_profile_name']);

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

    public function NEW_fetch_comment_wallpost($i_newsfeed_id = '') {
        try {
            $data = $this->data;
            $html = '';
            //$result = $this->gospel_magazine_model->get_project_cmnts($wh ,$page,$this->comments_pagination_per_page);
            $result = $this->newsfeed_comments_model->get_by_newsfeed_id($i_newsfeed_id);

            //pr($result);
            if (count($result)) {

                foreach ($result as $key => $val) {

                    $profile_image_filename = get_profile_image_of_user('thumb',$val['s_profile_photo'],$val['e_gender']);
                    $DESC = html_entity_decode(htmlspecialchars_decode($val['s_contents']), ENT_QUOTES, 'utf-8');
                    $profile_link = get_profile_url($val['post_owner_user_id'], $val['s_profile_name']);

                    $html .= '<div class="txt_content01 comments-number-content"> 
					 			<a href="' . $profile_link . '"><div style="background:url(' . $profile_image_filename . ') no-repeat center;width:60px; height:60px;" class="pro_photo" ></div></a>
									<div class="left-nw-wal">
										  <p class="blue_bold12"><a href="javascript:void(0);">' . $val['s_profile_name'] . '</a></p>
										  <p>' . nl2br($DESC) . '</p>
											 <p class="read-more">Updated on: ' . get_time_elapsed($val['dt_posted_date']) . '</p>
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

    ########### NEW FETCH COMMENTS ON WALL METHOD ###########

    public function NEW_fetch_commit_prayer_request($prayer_id = '') {
        try {
            $data = $this->data;
            $html = '';
            //$result = $this->gospel_magazine_model->get_project_cmnts($wh ,$page,$this->comments_pagination_per_page);
            $result = $this->prayer_commit_model->get_by_request_id($prayer_id);

            //pr($result);
            if (count($result)) {

                foreach ($result as $key => $val) {

                    $profile_image_filename = get_profile_image_of_user('thumb',$val['s_profile_photo'],$val['e_gender']);
                    $DESC = html_entity_decode(htmlspecialchars_decode($val['s_contents']), ENT_QUOTES, 'utf-8');
                    $profile_link = get_profile_url($val['post_owner_user_id'], $val['s_profile_name']);

                    $html .= '<div class="txt_content01 comments-number-content"> 
					 			<a href="' . $profile_link . '"><div style="background:url(' . $profile_image_filename . ') no-repeat center;width:60px; height:60px;" class="pro_photo" ></div></a>
									<div class="left-nw-wal">
										  <p class="blue_bold12"><a href="javascript:void(0);">' . $val['s_profile_name'] . '</a></p>
										  <p>' . nl2br($DESC) . '</p>
											 <p class="read-more">Updated on: ' . get_time_elapsed($val['dt_posted_date']) . '</p>
									</div>
									<div class="clr"></div>
							  </div>';
                }
            } else {
                $html .= '     <div class="txt_content01 comments-number-content" style="width:475px !important;"> 
										<div style="text-align:center;"><p>No Commitments.</p></div>
										</div>
										';
            }

            echo json_encode(array('result' => 'success', 'html_data' => $html));
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    ### user level privacy  settings  for wall

    public function wall_privacy_settings() {
        try {

            $posted = array();
            $data = $this->data;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');


            parent::_add_js_arr(array('js/ddsmoothmenu.js',
                'js/switch.js', 'js/animate-collapse.js',
                'js/lightbox.js', 'js/jquery.dd.js', 'js/jquery-ui-1.8.2.custom.min.js',
                'js/frontend/logged/tweets/tweet_utilities.js',
                'js/stepcarousel.js'
            ));

            parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
                'css/dd.css'));

            ############################################################
            if (intval($id) <= 0) {
                $i_user_id = intval(decrypt($this->session->userdata('user_id')));
                $data['page_view_type'] = 'myaccount';
            }
            $arr_profile_info = $this->users_model->fetch_this($i_user_id);
            $data['arr_profile_info'] = $arr_profile_info;

            $data['privacy_arr_wallCommentLike'] = $this->data_newsfeed_model->get_privacy_settings_likeComment_by_wall_owner_id($i_user_id);

            $data['privacy_arr_wall'] = $this->data_newsfeed_model->get_privacy_settings_wall_by_wall_owner_id($i_user_id);
            # view file...
            $VIEW = "logged/wall/wallpost_privacy_settings.phtml";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

    public function update_wall_privacy_settings() {
        try {
            //pr($_POST,1);
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by non logged in user
            $i_logged_id = intval(decrypt($this->session->userdata('user_id')));

            ###########################Privacy settings###################################
            $this->db->query("DELETE FROM {$this->db->wallpost_privacy} WHERE i_wall_owner_id='" . $i_logged_id . "'");
            $this->db->query("DELETE FROM {$this->db->wallcommentlike_privacy} WHERE i_wall_owner_id='" . $i_logged_id . "'");


            insert_privacy($i_logged_id, $_POST, $this->db->wallpost_privacy, 'i_wall_owner_id', 'wall_');
			//echo $this->db->last_query();exit;
            insert_privacy($i_logged_id, $_POST, $this->db->wallcommentlike_privacy, 'i_wall_owner_id', 'wallCommentLike_');
            ###########################Privacy settings###################################

            echo json_encode(array('success' => true, 'arr_messages' => $arr_messages, 'msg' => 'Wall privacy updated Successfully.'));
        } catch (Exception $err_obj) {
            
        }
    }

}

// end of controller...

