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

class E_intercession extends Base_controller {

    private $pagination_per_page = 5;
    private $home_pagination_per_page = 6;
    private $commits_pagination_per_page = 5;
    private $all_commits_pagination_per_page = 10;

    public function __construct() {

        try {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...

            $this->load->model('users_model');
            $this->load->model('holy_place_model');
            $this->load->model('bible_fruits_model');
            $this->load->model('prayer_wall_photos_model');
            $this->load->model('intercession_model');
            $this->load->model('intercession_model');

            $this->i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function index() {
        try {

            $posted = array();
            $this->data["posted"] = $posted; /* don't change */
            $data = $this->data;
            $this->data["MAIN_MENU_SELECTED"] = 6;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');


            parent::_add_js_arr(array('js/ddsmoothmenu.js',
                'js/switch.js', 'js/animate-collapse.js',
                'js/lightbox.js', 'js/jquery.dd.js',
                'js/jquery-ui-1.8.2.custom.min.js',
                'js/stepcarousel.js',
                'js/jquery/ui/jquery.ui.core.js',
                'js/jquery.ui.datepicker.js',
                'js/jquery-ui-timepicker-addon.js',
                'js/jquery-ui-sliderAccess.js',
                'js/tab.js',
                'js/frontend/logged/holy_place/intercession.js',
                'js/autocomplete/jquery.autocomplete.js'
            ));

            parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css', 'css/jquery.autocomplete.css'));

            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
            $data['home_pagination_per_page'] = $this->home_pagination_per_page;
            $this->session->set_userdata('search_condition', '');
            ob_start();
            $this->all_request_ajax_pagination(0);
            $content = ob_get_contents();
            $content_obj = json_decode($content);
            $data['prayer_req_ajax_content'] = $content_obj->html;
            $data['no_of_result'] = $content_obj->no_of_result;
            ob_end_clean();

            # view file...
            $curr_url = curPageURL();

            $this->session->set_userdata('current_url', $curr_url);
            $VIEW = "logged/holy_place/e-intercession/view-search-eintercession.phtml";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

    public function all_request_ajax_pagination($page = 0) {

        //echo $page;
        ## seacrh conditions : filter ############
        $WHERE_COND = 'WHERE 1 AND i.i_is_enable  = 1 AND i.e_request_type = "On Going" ';

        if (isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y') {

            $txt_srch_country = intval(decrypt($this->input->post('txt_srch_country')));
            $WHERE_COND .= ($txt_srch_country == '' || $txt_srch_country == '-1') ? '' : " AND 
			  				(i.i_country_id =  '{$txt_srch_country}')";


            $txt_srch_state = get_formatted_string(trim($this->input->post('txt_srch_state')));
            $WHERE_COND .= ($txt_srch_state == '') ? '' : " AND (s.s_state  LIKE  '%" . $txt_srch_state . "%')";

            $txt_srch_city = get_formatted_string(trim($this->input->post('txt_srch_city')));
            $WHERE_COND .= ($txt_srch_city == '') ? '' : " AND (ct.s_city  LIKE  '%" . $txt_srch_city . "%')";


            $location = get_formatted_string(trim($this->input->post('txt_location')));
            if ($location != '') {
                $location_arr = explode(',', $location);
            }
            $total_locations = count($location_arr);

            if ($total_locations) {

                $WHERE_COND .= " AND (";
                for ($i = 0; $i < $total_locations; $i++) {

                    if ($i == 0) {
                        $WHERE_COND .= "  (mst_c.s_country like '{$location_arr[$i]}%' OR  s.s_state like '{$location_arr[$i]}%' OR ct.s_city like '{$location_arr[$i]}%')";
                    } else {
                        $WHERE_COND .= "  OR (mst_c.s_country like '{$location_arr[$i]}%' OR  s.s_state like '{$location_arr[$i]}%' OR ct.s_city like '{$location_arr[$i]}%')";
                    }
                }
                $WHERE_COND .= " )";
            }


            if ($this->input->post('srch_date_to') != '' && $this->input->post('srch_date_end') != '') {

                $dt_start_date = $this->input->post('srch_date_to');
                $dt_end_date = $this->input->post('srch_date_end');

                if ($WHERE_COND != '') {
                    $WHERE_COND .= ($dt_start_date == '' && $dt_end_date == '') ? '' : " AND  ( DATE_FORMAT(i.dt_start_date , '%Y-%m-%d %H:%i') BETWEEN
										'" . $dt_start_date . "' AND '" . $dt_end_date . "'
										AND 
										    DATE_FORMAT(i.dt_end_date , '%Y-%m-%d %H:%i') BETWEEN
										'" . $dt_start_date . "' AND '" . $dt_end_date . "' )";
                } else {
                    $WHERE_COND .= ($dt_start_date == '' && $dt_end_date == '') ? '' : " ( DATE_FORMAT(i.dt_start_date , '%Y-%m-%d %H:%i') BETWEEN
										'" . $dt_start_date . "' AND '" . $dt_end_date . "'
										AND 
										  DATE_FORMAT(i.dt_end_date , '%Y-%m-%d %H:%i') BETWEEN
										'" . $dt_start_date . "' AND '" . $dt_end_date . "' )";
                }

                if ($NON_EXACT_WHERE != '') {
                    $NON_EXACT_WHERE .= ($dt_start_date == '' && $dt_end_date == '') ? '' : " OR ( DATE_FORMAT(i.dt_start_date , '%Y-%m-%d %H:%i') BETWEEN
										'" . $dt_start_date . "' AND '" . $dt_end_date . "'
										OR 
										  DATE_FORMAT(i.dt_end_date , '%Y-%m-%d %H:%i') BETWEEN
										'" . $dt_start_date . "' AND '" . $dt_end_date . "' ) ";
                } else {
                    $NON_EXACT_WHERE .= ($dt_start_date == '' && $dt_end_date == '') ? '' : "  ( DATE_FORMAT(i.dt_start_date , '%Y-%m-%d %H:%i') BETWEEN
										'" . $dt_start_date . "' AND '" . $dt_end_date . "'
										OR 
										 DATE_FORMAT(i.dt_end_date , '%Y-%m-%d %H:%i') BETWEEN
										'" . $dt_start_date . "' AND '" . $dt_end_date . "' )";
                }
            } else if ($this->input->post('srch_date_to') != '') {
                $dt_start_date = ($this->input->post('srch_date_to'));
                $WHERE_COND .= ($dt_start_date == '') ? '' : " AND (DATE_FORMAT(i.dt_start_date , '%Y-%m-%d %H:%i') ='" . $dt_start_date . "' )";
            } else if ($this->input->post('srch_date_end') != '') {
                $dt_end_date = ($this->input->post('srch_date_end'));
                $WHERE_COND .= ($dt_end_date == '') ? '' : " AND (DATE_FORMAT(i.dt_end_date, '%Y-%m-%d %H:%i') ='" . $dt_end_date . "' )";
            }



            $this->session->set_userdata('search_condition', $WHERE_COND);
        }

        if ($this->session->userdata('search_condition') != '') {
            $s_where = $this->session->userdata('search_condition');
        } else {
            $s_where = 'WHERE 1 AND i.i_is_enable  = 1 AND i.e_request_type = "On Going" ';
        }
        //exit;
        $cur_page = $page + $this->pagination_per_page;

        $data = $this->data;

        $result = $this->intercession_model->get_all_intercession($s_where, intval($page), $this->pagination_per_page);
        //echo $this->db->last_query();
        $total_rows = $this->intercession_model->get_count_all_intercession($s_where);

        //pr($result,1);
        $data['arr_prayer_request'] = $result;
        $data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
        $data['profile_id'] = $i_user_id;

        //--- for check end of he page.
        $view_more = true;
        $rest_counter = $total_rows - $page;
        if ($rest_counter <= $this->pagination_per_page)
            $view_more = false;
        //--------- end check


        $VIEW_FILE = "logged/holy_place/e-intercession/list_intercession_ajax.phtml";

        if (is_array($result) && count($result)) {
            $content = $this->load->view($VIEW_FILE, $data, true);
        } else {
            $content = '';
        }

        echo json_encode(array('html' => $content, 'no_of_result' => $data['no_of_result'], 'view_more' => $view_more, 'cur_page' => $data['current_page_1']));
    }

    ### post commitments ###

    public function post_commitments($p_request_id) {
        $user_id = intval(decrypt($this->session->userdata('user_id')));
        $user_details = $this->users_model->fetch_this($user_id);
        $arr_messages = array();


        $message = nl2br(htmlspecialchars(trim($this->input->post('message')), ENT_QUOTES, 'utf-8'));
        if (trim($this->input->post('message')) == 'Max 140 Characters') {
            $arr_messages['message' . $p_request_id] = '* Required Field.';
        }



        if (trim($this->input->post('start_date')) == '') {
            $arr_messages['start_date' . $p_request_id] = "* Required Field.";
        }

        if (trim($this->input->post('end_date')) == '') {
            $arr_messages['end_date' . $p_request_id] = "* Required Field.";
        }
        $start_date = trim($this->input->post('start_date'));
        $end_date = trim($this->input->post('end_date'));
        if ($start_date > $end_date) {
            $arr_messages['end_date' . $p_request_id] = "Please enter a valid end date.";
        }
        if ($this->input->post('chk_day') == '') {
            $arr_messages['chk_day' . $p_request_id] = "* Required Field.";
        }

        if ($this->input->post('chk_time') == '') {
            $arr_messages['chk_time' . $p_request_id] = "* Required Field.";
        }


        $_html = '';
        if (count($arr_messages) == 0) {
            $arr = array();
            $arr['i_user_id'] = $user_id;
            $arr['s_contents'] = $message;
            $arr['dt_start_time'] = trim($this->input->post('start_date')) . ':00';
            $arr['dt_end_time'] = trim($this->input->post('end_date')) . ':00';
            $arr['dt_created_on'] = get_db_datetime();
            $arr['i_id_intercession_wall_post '] = $p_request_id;
            $arr['s_weekdays'] = substr($this->input->post('chk_day'), 0, -2);
            $arr['s_time'] = substr($this->input->post('chk_time'), 0, -2);
            $commit_id = $this->intercession_model->insert_commitments($arr);

            $total_commits = $this->intercession_model->get_total_by_request_id($p_request_id);

            ### add to do list
            $this->load->model('organizer_todo_model');

            ### getting days
            $prayer_arr = $this->intercession_model->get_info_by_intercession_id($p_request_id);
            $day_arr = GetDays(trim($this->input->post('start_date')), trim($this->input->post('end_date')));
            //pr($day_arr);

            $time_arr = array();
            $time_arr = explode(', ', substr($this->input->post('chk_time'), 0, -2));

            //pr($time_arr,1);

            if (count($day_arr)) {
                foreach ($day_arr as $val) {

                    ### time slots
                    if (count($time_arr)) {

                        foreach ($time_arr as $t_val) {

                            $t_arr = explode('-', $t_val);
                            $strt_time = $t_arr[0];

                            $info = array();
                            $info['i_user_id'] = intval(decrypt($this->session->userdata('user_id')));
                            $info['s_description'] = " " . get_formatted_string($prayer_arr['s_subject']) . ".";
                            $info["d_date"] = $val;
                            $info["t_start_time"] = $t_arr[0];
                            $info["t_end_time"] = $t_arr[1];
                            $info["t_remind_time"] = '00:15:00';
                            $info['i_request_id'] = 'inter#' . $p_request_id;
                            //pr($info,1);				  
                            $date_a = new DateTime($info["d_date"] . ' ' . $info["t_start_time"]);
                            $date_b = new DateTime($info["d_date"] . ' ' . trim($this->input->post('todo_rem_time')));

                            $interval = date_diff($date_a, $date_b);

                            $info["t_remind_me_back"] = $interval->format('%h:%i:%s');


                            $info['dt_created_on'] = get_db_datetime();
                            $_ret = $this->organizer_todo_model->insert($info);
                        }
                    }
                }
            }

            ### add to do list

            ob_start();
            $this->commitments_ajax_pagination($p_request_id);
            $content = ob_get_contents();
            $content_obj = json_decode($content);
            $_html = $content_obj->html;
            $no_of_result = $content_obj->no_of_result;
            ob_end_clean();

            echo json_encode(array('success' => 'true', 'msg' => "Commited successfully.", 'html' => $_html, 'view_more' => $view_more, 'cur_page' => $cur_page, 'total_commits' => $total_commits));
        } else {
            echo json_encode(array('success' => 'false', 'arr_messages' => $arr_messages, 'html' => $_html));
        }
    }

    public function viewCommits($type = 'long') {

        $i_prayer_id = $this->input->post('p_request_id');
        $commit_arr = $this->intercession_model->get_by_request_id($i_prayer_id);
        # pr($commit_arr,1);
        if (count($commit_arr)) {
            if ($type == 'short') {
                $VIEW_FILE = "logged/holy_place/e-intercession/short_single_commitment_ajax.phtml";
            } else {
                $VIEW_FILE = "logged/holy_place/e-intercession/single_commitment_ajax.phtml";
            }

            $data['commit_arr'] = $commit_arr;
            $content = $this->load->view($VIEW_FILE, $data, true);

            echo json_encode(array('des' => base64_encode($content)));
        } else
            echo json_encode(array('des' => base64_encode('')));
    }

    public function post_testimony($p_request_id) {
        $user_id = intval(decrypt($this->session->userdata('user_id')));
        $user_details = $this->users_model->fetch_this($user_id);
        $arr_messages = array();


        $message = nl2br(htmlspecialchars(trim($this->input->post('message')), ENT_QUOTES, 'utf-8'));
        if (trim($this->input->post('message')) == 'Max 500 Characters') {
            $message = '';
        }

        $_html = '';
        if ($message != '') {
            $arr = array();
            $arr['s_message'] = $message;
            $arr['i_id_intercession_wall_post '] = $p_request_id;
            $arr['dt_insert_date'] = get_db_datetime();


            $commit_id = $this->intercession_model->insert_testimony($arr);

            echo json_encode(array('success' => 'true', 'msg' => "Testimony added successfully.", 'html' => $_html, 'view_more' => $view_more, 'cur_page' => $cur_page));
        } else {
            echo json_encode(array('success' => 'false', 'msg' => "Please enter some text!", 'html' => $_html));
        }
    }

    public function viewTestimony() {

        $i_prayer_id = $this->input->post('p_request_id');
        $commit_arr = $this->intercession_model->get_testimony_by_intercession_id($i_prayer_id);
        //pr($commit_arr,1);
        $content = '';
        if (count($commit_arr)) {
            $profile_image_filename = get_profile_image($commit_arr['i_user_id'], 'thumb');
            $DESC = html_entity_decode(htmlspecialchars_decode($commit_arr['s_message']), ENT_QUOTES, 'utf-8');

            $content = '<div class="commitment-box"> 
                    
                    <div class="right-panel full">
                        <p class="quoted-text">
                          ' . $DESC . '
                            <span>Updated on: ' . get_time_elapsed($commit_arr['dt_created_on']) . '</span>
                        </p>
                        <h2 class="name">' . $commit_arr['s_profile_name'] . '</h2>
                        <p class="place">' . $commit_arr['s_country_name'] . '</p>
                        
						<p class="date-time">From: <span>' . getShortDateWithTime($val['dt_start_time'], 9) . '</span> | To: <span>' . getShortDateWithTime($val['dt_end_time'], 9) . '</span></p>
                    </div>
                    <div class="clr"></div>
              </div>';

            echo json_encode(array('des' => base64_encode($content)));
        } else
            echo json_encode(array('des' => base64_encode('')));
    }

    public function intercession_prayer_request_detail($id) {
        try {

            $posted = array();
            $this->data["posted"] = $posted; /* don't change */
            $data = $this->data;
            $this->data["MAIN_MENU_SELECTED"] = 6;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');


            parent::_add_js_arr(array('js/ddsmoothmenu.js',
                'js/switch.js', 'js/animate-collapse.js',
                'js/lightbox.js',
                'js/jquery-ui-1.8.2.custom.min.js',
                'js/jquery/ui/jquery.ui.core.js',
                'js/jquery.ui.datepicker.js',
                'js/jquery-ui-timepicker-addon.js',
                'js/stepcarousel.js',
                'js/jquery-ui.triggeredAutocomplete.js',
                'js/jquery-ui-sliderAccess.js',
                'js/tab.js',
                'js/frontend/logged/holy_place/intercession.js',
                'js/autocomplete/jquery.autocomplete.js'
            ));

            parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
                'css/jquery.autocomplete.css'));

            $i_user_id = intval(decrypt($this->session->userdata('user_id')));

            $s_where = " WHERE i.id = {$id}";
            $prayer_info = $this->intercession_model->get_all_intercession($s_where);
            $data['prayer_info'] = $prayer_info[0];
            $data['prayer_commits_info'] = $this->intercession_model->get_by_request_id($id);

            $data['pagination_per_page'] = $this->commits_pagination_per_page;

            ob_start();
            $this->commitments_ajax_pagination($id);
            $content = ob_get_contents();
            $content_obj = json_decode($content);
            $data['prayer_commit_content'] = $content_obj->html;
            $data['no_of_result'] = $content_obj->no_of_result;
            ob_end_clean();


            $VIEW = "logged/holy_place/e-intercession/intercession-wall-request-detail.phtml";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

    public function commitments_ajax_pagination($prayer_id, $page = 0) {

        $cur_page = $page + $this->commits_pagination_per_page;

        $data = $this->data;
        $result = $this->intercession_model->get_by_request_id($prayer_id, intval($page), $this->commits_pagination_per_page);
        //echo $this->db->last_query();
        $total_rows = $this->intercession_model->get_total_by_request_id($prayer_id);
        //pr($result,1);
        $data['prayer_commits_info'] = $result;
        $data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
        $data['profile_id'] = $i_user_id;

        //--- for check end of he page.
        $view_more = true;
        $rest_counter = $total_rows - $page;
        if ($rest_counter <= $this->commits_pagination_per_page)
            $view_more = false;
        //--------- end check


        $VIEW_FILE = "logged/holy_place/e-intercession/ajax_commits_list.phtml";

        if (is_array($result) && count($result)) {
            $content = $this->load->view($VIEW_FILE, $data, true);
        } else {
            $content = '';
        }

        //echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode(array('html' => $content, 'no_of_result' => $data['no_of_result'], 'view_more' => $view_more, 'cur_page' => $data['current_page_1']));
    }

    ### edit commitments
    #### NEW METHOD TO COMMIT PRAYER CLICK IE  only EMERGENCY  INTERCESSION

    public function commitPrayerClick($id) {
        $user_id = intval(decrypt($this->session->userdata('user_id')));
        $user_details = $this->users_model->fetch_this($user_id);

        $arr = array();
        $arr['i_user_id'] = $user_id;
        $arr['dt_created_on'] = get_db_datetime();
        $arr['i_id_intercession_wall_post '] = $id;

        $commit_id = $this->intercession_model->insert_commitments($arr);
        $total_commits = $this->intercession_model->get_total_by_request_id($id);

        echo json_encode(array('success' => 'true', 'msg' => "Commited successfully.", 'total_commits' => $total_commits));
    }

    public function getPrayerClickTotalCommit($id) {

        $total_commits = $this->intercession_model->get_total_by_request_id($id);
        echo json_encode(array('success' => 'true', 'total_commits' => $total_commits));
    }

    public function hide_prayerClick($id) {

        $arr = array();

        $arr['i_user_id'] = intval(decrypt($this->session->userdata('user_id')));
        $arr['i_request_id'] = $id;
        $arr['dt_created_on'] = get_db_datetime();
        $this->db->insert(cg_skip_prayer_click, $arr);

        echo json_encode(array('success' => 'true'));
    }

}

// end of controller...

