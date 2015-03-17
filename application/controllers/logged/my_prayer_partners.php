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
 */
include(APPPATH . 'controllers/base_controller.php');

class My_prayer_partners extends Base_controller {

    private $pagination_per_page = 50;
    private $friends_pagination_per_page = 3;

    public function __construct() {
        try {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...
            $this->load->model('users_model');
            $this->load->model('my_prayer_partner_model');
            $this->load->model('user_notifications_model');
            $this->load->model('user_alert_model');
            $this->load->model('my_ring_model');
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
            $this->data["MAIN_MENU_SELECTED"] = 1;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');


            parent::_add_js_arr(array(/*'js/ddsmoothmenu.js',
                'js/switch.js', 'js/animate-collapse.js',
                'js/lightbox.js', 'js/jquery.dd.js', 'js/jquery-ui-1.8.2.custom.min.js',
                'js/stepcarousel.js',
                'js/jquery.form.js',
                'js/jquery/JSON/json2.js',*/
                'js/production/tweet_utilities.js',
                'js/production/my_prayer_partner.js',
               // 'js/tab.js'
            ));

            parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
                'css/dd.css'));

            $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
            $data['page_view_type'] = 'myaccount';
            $arr_profile_info = $this->users_model->fetch_this($i_profile_id);
            $data['arr_profile_info'] = $arr_profile_info;
            $data['is_want_prayer_partner'] = $arr_profile_info['e_want_prayer_partner'];



            ob_start();
            $this->friends_ajax_pagination();
            $data['friends_result_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();

            ### showing Prayer Partner request sent ###
            /* ob_start();
              $this->my_request_recieved_ajax_pagination();
              $data['content2'] = ob_get_contents();
              ob_end_clean(); */

            ### showing Prayer Partner requset recived ##
            $qualification_params = array();
            $qualification_params = get_user_prayer_partner_qualifications_by_id($i_profile_id);
            //pr($qualification_params);
            $wh = " AND r.i_user_id='" . $i_profile_id . "'";
            $wh1 = " AND inv.i_invited_id='" . $i_profile_id . "'";
            $total_rings = $this->my_ring_model->gettotal_ring_by_user($wh, $wh1);
            $qualification_params['total_rings'] = $total_rings;

            $grp_names = $this->prayer_group_model->get_my_groups_names($i_profile_id);
            $total_prayer_grps = count($grp_names);
            $qualification_params['total_prayer_grps'] = $total_prayer_grps;
            // pr($qualification_params);
            $data['qualification_params'] = $qualification_params;
            # view file...
            
            $VIEW = "logged/my_prayer_partners/my_prayer_partners.phtml";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

    public function friends_ajax_pagination($page = 0) {
        try {
            $add_where = '';
            #$show = '-1';
            $this->session->set_userdata('is_post_', '');
            $this->session->set_userdata('search_condition', '');
            $i_profile_id = intval(decrypt($this->session->userdata('user_id')));


            ### search condition ###
            if (isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y') {

                $s_name = get_formatted_string(trim($this->input->post('txt_name')));
                if ($WHERE_COND != '')
                    $WHERE_COND .= ($s_name == '') ? '' : " OR (u.s_first_name LIKE '" . $s_name . "%'  OR u.s_last_name LIKE '" . $s_name . "%'  )";
                else
                    $WHERE_COND .= ($s_name == '') ? '' : "  (u.s_first_name LIKE '" . $s_name . "%' OR u.s_last_name LIKE '" . $s_name . "%' )";


                $show = get_formatted_string(trim($this->input->post('show')));
                if ($WHERE_COND != '') {
                    $WHERE_COND .= ($show == '1') ? " AND (uon.s_status = '" . $show . "' )" : '';
                    $WHERE_COND .= ($show == '4') ? " AND (uon.s_status IS NULL)" : '';
                    $WHERE_COND .= ($show == '-1') ? "" : "";
                } else {
                    $WHERE_COND .= ($show == '1') ? "  (uon.s_status = '" . $show . "')" : '';
                    $WHERE_COND .= ($show == '4') ? " (uon.s_status IS NULL)" : '';
                    $WHERE_COND .= ($show == '-1') ? "" : "";
                }


                $this->session->set_userdata('search_condition', $WHERE_COND);
                $this->session->set_userdata('is_post_', '1');
            }

            #### end search condition ###
            $add_where = $this->session->userdata('search_condition');
            if ($add_where != '') {
                $add_where = " AND (" . $add_where . ")";
            } {
                $WHERE = " WHERE 
						1
						AND c.s_status = 'accepted' 
						AND u.i_status=1 
						AND
						((c.i_requester_id = '" . $i_profile_id . "' AND u.id=c.i_accepter_id ) 
						OR (c.i_accepter_id = '" . $i_profile_id . "' AND u.id=c.i_requester_id )) " . $add_where . " GROUP BY u.id ";

                $ORDER_BY = "u.s_first_name ASC";

                $total_where = " WHERE 
									1
									AND c.s_status = 'accepted' 
									AND u.i_status=1 
									AND
									((c.i_requester_id = '" . $i_profile_id . "' AND u.id=c.i_accepter_id ) 
									OR (c.i_accepter_id = '" . $i_profile_id . "' AND u.id=c.i_requester_id )) " . $add_where . " GROUP BY u.id ";

                $result = $this->my_prayer_partner_model->fetch_multi_online_friends($WHERE, intval($page), $this->friends_pagination_per_page, $ORDER_BY);
                $resultCount = count($result);

                $total_rows = $this->my_prayer_partner_model->gettotal_online_friends($total_where);

                if ((!is_array($result) || !count($result) ) && $total_rows) {
                    $page = $page - $this->friends_pagination_per_page;

                    $result = $this->my_prayer_partner_model->fetch_multi_online_friends($WHERE, intval($page), $this->friends_pagination_per_page, $ORDER_BY);
                }
            }
            #echo $resultCount = count($result);
            //echo $this->db->last_query(); 
            #($result);
            ## end seacrh conditions : filter ############
            #pr($result);
            #Jquery Pagination Starts
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url() . "logged/my_prayer_partners/friends_ajax_pagination";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->friends_pagination_per_page;
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

            $config['div'] = '#my_friends'; /* Here #content is the CSS selector for target DIV */
            $config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
            $config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */

            $this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();

            // getting   listing...
            $data['result_arr'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $page;
            $data['total_pages'] = ceil($total_rows / $this->friends_pagination_per_page);

            //echo $data['total_pages'].' ==total_pages==== '.$page;
            //echo $data['current_page'].' ==  ';
            $p = ($page / $this->friends_pagination_per_page);
            $data['current_loaded_page_no'] = $p + 1;
            $data['is_post_'] = $this->session->userdata('is_post_');
            # loading the view-part...
            echo $this->load->view('logged/my_prayer_partners/my_prayer_partners_ajax.phtml', $data, TRUE);
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function my_request_recieved_ajax_pagination($page = 0) {
        try {
            $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
            ### showing Friend request sent ###
            $WHERE = " WHERE 
						1
						AND i_deleted_by = 1
						AND c.s_status = 'pending' 
						AND u.i_status=1 
						AND
						((c.i_accepter_id = '" . $i_profile_id . "' AND u.id=c.i_requester_id ) ) GROUP BY u.id";
            /* AND u.i_country_id=cn.id */
            $ORDER_BY = "c.id DESC";

            $result = $this->my_prayer_partner_model->fetch_multi($WHERE, null, null, $ORDER_BY);
            $resultCount = count($result);
            #echo $this->db->last_query(); 
            #pr($result);
            $total_rows = $this->my_prayer_partner_model->gettotal_info($WHERE);



            // getting   listing...*/
            $data['result_arr_pp'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $page;
            $data['total_pages'] = ceil($total_rows / $this->pagination_per_page);

            //echo $data['total_pages'].' ==total_pages==== '.$page;
            //echo $data['current_page'].' ==  ';
            $p = ($page / $this->pagination_per_page);
            $data['current_loaded_page_no'] = $p + 1;


            # loading the view-part...
            echo $this->load->view('logged/my_prayer_partners/my_prayer_partner_request_recieved_ajax.phtml', $data, TRUE);
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function prayer_partner_request() {
        $posted = array();
        $this->data["posted"] = $posted; /* don't change */
        $data = $this->data;
        $this->data["MAIN_MENU_SELECTED"] = 1;
        parent::_set_title('::: COGTIME Xtian network :::');
        parent::_set_meta_desc('');
        parent::_set_meta_keywords('');


        parent::_add_js_arr(array(/*'js/ddsmoothmenu.js',
            'js/switch.js', 'js/animate-collapse.js',
            'js/lightbox.js', 'js/jquery.dd.js', 'js/jquery-ui-1.8.2.custom.min.js',
            'js/stepcarousel.js',*/
            'js/production/tweet_utilities.js',
            'js/production/my_prayer_partner.js'
        ));

//        parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
//            'css/dd.css'));

        $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        $arr_profile_info = $this->users_model->fetch_this($i_profile_id);
        #pr($arr_profile_info);
        ### showing Prayer Partner request sent ###

        ob_start();
        $this->request_sent_ajax_pagination();
        $data['content1'] = ob_get_contents();
        ob_end_clean();

        ob_start();
        $this->my_request_recieved_ajax_pagination();
        $data['content2'] = ob_get_contents();
        ob_end_clean();


        # view file...
        $VIEW = "logged/my_prayer_partners/prayer_partner_request.phtml";
        parent::_render($data, $VIEW);
    }

    public function request_sent_ajax_pagination($page = 0) {
        try {
            $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
            ### showing Friend request sent ###
            $WHERE = " WHERE 
							1
							AND i_deleted_by = 1
							AND c.s_status = 'pending' 
							AND u.i_status=1 
							AND
							((c.i_requester_id = '" . $i_profile_id . "' AND u.id=c.i_accepter_id ) ) GROUP BY u.id";
            /* AND u.i_country_id=cn.id */
            $ORDER_BY = "c.id DESC";


            $result = $this->my_prayer_partner_model->fetch_multi($WHERE, intval($page), $this->pagination_per_page, $ORDER_BY);
            #($result);
            $total_rows = $this->my_prayer_partner_model->gettotal_info($WHERE);

            if ((!is_array($result) || !count($result) ) && $total_rows) {
                $page = $page - $this->pagination_per_page;

                $result = $this->my_prayer_partner_model->fetch_multi($WHERE, intval($page), $this->pagination_per_page, $ORDER_BY);
            }
            ## end seacrh conditions : filter ############
            #pr($result,1);
            #Jquery Pagination Starts
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url() . "logged/my_friends/friend_request_sent_ajax_pagination";
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

            $config['div'] = '#request_sent_content'; /* Here #content is the CSS selector for target DIV */
            $config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
            $config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */

            $this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();

            // getting   listing...
            $data['result_arr'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $page;
            $data['total_pages'] = ceil($total_rows / $this->pagination_per_page);

            $p = ($page / $this->pagination_per_page);
            $data['current_loaded_page_no'] = $p + 1;

            # loading the view-part...
            echo $this->load->view('logged/my_prayer_partners/prayer_partner_request_sent_ajax.phtml', $data, TRUE);
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function request_recieved_ajax_pagination($page = 0) {
        try {
            $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
            ### showing Friend request sent ###
            $WHERE = " WHERE 
						1
						AND i_deleted_by = 1
						AND c.s_status = 'pending' 
						AND u.i_status=1 
						AND
						((c.i_accepter_id = '" . $i_profile_id . "' AND u.id=c.i_requester_id ) ) GROUP BY u.id";
            /* AND u.i_country_id=cn.id */
            $ORDER_BY = "c.id DESC";

            $result = $this->my_prayer_partner_model->fetch_multi($WHERE, intval($page), $this->pagination_per_page, $ORDER_BY);
            $resultCount = count($result);
            #echo $this->db->last_query(); 
            #pr($result);
            $total_rows = $this->my_prayer_partner_model->gettotal_info($WHERE);

            if ((!is_array($result) || !count($result) ) && $total_rows) {
                $page = $page - $this->pagination_per_page;

                $result = $this->my_prayer_partner_model->fetch_multi($WHERE, intval($page), $this->pagination_per_page, $ORDER_BY);
            }
            ## end seacrh conditions : filter ############
            #pr($result,1);
            #Jquery Pagination Starts
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url() . "logged/my_friends/friend_request_recieved_ajax_pagination";
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

            $config['div'] = '#request_recieved_content'; /* Here #content is the CSS selector for target DIV */
            $config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
            $config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */

            $this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();

            // getting   listing...
            $data['result_arr'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $page;
            $data['total_pages'] = ceil($total_rows / $this->pagination_per_page);

            //echo $data['total_pages'].' ==total_pages==== '.$page;
            //echo $data['current_page'].' ==  ';
            $p = ($page / $this->pagination_per_page);
            $data['current_loaded_page_no'] = $p + 1;


            # loading the view-part...
            echo $this->load->view('logged/my_prayer_partners/prayer_partner_request_recieved_ajax.phtml', $data, TRUE);
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function search_invite_prayer_partner() {
        $posted = array();
        $this->data["posted"] = $posted; /* don't change */
        $data = $this->data;
        $this->data["MAIN_MENU_SELECTED"] = 1;
        parent::_set_title('::: COGTIME Xtian network :::');
        parent::_set_meta_desc('');
        parent::_set_meta_keywords('');


        parent::_add_js_arr(array(/*'js/ddsmoothmenu.js',
            'js/switch.js', 'js/animate-collapse.js',
            'js/lightbox.js', 'js/jquery.dd.js', 'js/jquery-ui-1.8.2.custom.min.js',
            'js/stepcarousel.js',*/
            'js/production/tweet_utilities.js',
            'js/production/my_prayer_partner.js',
            'js/autocomplete/jquery.autocomplete.js'
        ));

        parent::_add_css_arr(array(/*'css/jquery-ui-1.8.2.custom.css',
            'css/dd.css',*/
            'css/jquery.autocomplete.css'));

        $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        $total_user = $this->my_prayer_partner_model->get_prayerPartnerId_by_user_id($i_profile_id);
        #pr($total_user);
        #$data['search_'] = '';
        ## seacrh conditions : filter ############


        $this->session->set_userdata('search_condition', '');
        $this->session->set_userdata('search_condition_nonexact', '');
        $this->session->set_userdata('is_post_', '');
        $this->session->set_userdata('is_preserve_search', false);
        ob_start();
        $this->ajax_pagination();
        $data['result_content'] = ob_get_contents(); //pr($data['result_content']);
        ob_end_clean();



        ###########3
        # view file...
        $VIEW = "logged/my_prayer_partners/search_invite_prayer_partner.phtml";
        parent::_render($data, $VIEW);
    }

    public function ajax_pagination($page = 0) {
        try {
            
            pr($_POST,1);
            ## seting session to detect the source of hit of public profile (if already netpals/ friends)##
            ## seacrh conditions : filter ############
            $i_profile_id = intval(decrypt($this->session->userdata('user_id')));

            if (isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y') {

                $WHERE_COND = "";
                $WHERE_COND_NOTEXACT = "";

                $WHERE_COMP_COND = "";
                $WHERE_COMP_COND_NOTEXACT = "";

                $WH = "";
                $WH_NOEXACT = "";


                #### new for smart search of country state and city

                $location = get_formatted_string(trim($this->input->post('txt_location')));
                if ($location != '') {
                    $location_arr = explode(', ', $location);
                }
                $total_locations = count($location_arr);

                if ($total_locations) {
                    $WHERE_COMP_COND_NOTEXACT .= " ( ";
                    for ($i = 0; $i < $total_locations; $i++) {

                        if ($i == 0) {
                            $WHERE_COMP_COND .= "  (con.s_country like '" . trim($location_arr[$i]) . "%' OR  s.s_state like '" . $location_arr[$i] . "%' OR c.s_city like '" . $location_arr[$i] . "%')";

                            $WHERE_COMP_COND_NOTEXACT .= " (con.s_country like '" . trim($location_arr[$i]) . "%' OR s.s_state like '" . trim($location_arr[$i]) . "%' OR c.s_city like '" . trim($location_arr[$i]) . "%')";
                        } else {

                            $WHERE_COMP_COND .= "  AND (con.s_country like '{$location_arr[$i]}%' OR  s.s_state like '{$location_arr[$i]}%' OR c.s_city like '{$location_arr[$i]}%')";

                            $WHERE_COMP_COND_NOTEXACT .= " OR (con.s_country like '" . trim($location_arr[$i]) . "%' OR s.s_state like '" . trim($location_arr[$i]) . "%' OR c.s_city like '" . trim($location_arr[$i]) . "%')";
                        }
                    }

                    $WHERE_COMP_COND_NOTEXACT .= " ) ";
                }


                $e_gender = get_formatted_string(trim($this->input->post('gender')));

                if ($WHERE_COMP_COND != '')
                    $WHERE_COMP_COND .= ($e_gender == '-1') ? '' : " AND ( u.`e_gender` = '" . $e_gender . "' )";
                else
                    $WHERE_COMP_COND .= ($e_gender == '-1') ? '' : "  ( u.`e_gender` = '" . $e_gender . "' )";


                if ($WHERE_COMP_COND_NOTEXACT != '')
                    $WHERE_COMP_COND_NOTEXACT .= ($e_gender == '-1') ? '' : " AND ( u.`e_gender` = '" . $e_gender . "' )";
                else
                    $WHERE_COMP_COND_NOTEXACT .= ($e_gender == '-1') ? '' : "  ( u.`e_gender` = '" . $e_gender . "' )";
                #### new for smart search of country state and city



                $s_fname = get_formatted_string(trim($this->input->post('txt_fname')));

                if ($WHERE_COND != '') {
                    $WHERE_COND .= ($s_fname == '') ? '' : " AND (u.s_first_name LIKE '" . $s_fname . "%' )";
                } else {
                    $WHERE_COND .= ($s_fname == '') ? '' : " (u.s_first_name LIKE '" . $s_fname . "%' )";
                }

                if ($WHERE_COND_NOTEXACT != '') {
                    $WHERE_COND_NOTEXACT .= ($s_fname == '') ? '' : " OR (u.s_first_name LIKE '" . $s_fname . "%' )";
                } else {
                    $WHERE_COND_NOTEXACT .= ($s_fname == '') ? '' : " (u.s_first_name LIKE '" . $s_fname . "%' )";
                }




                $s_lname = get_formatted_string(trim($this->input->post('txt_lname')));

                if ($WHERE_COND != '')
                    $WHERE_COND .= ($s_lname == '') ? '' : " AND (u.s_last_name LIKE '" . $s_lname . "%' )";
                else
                    $WHERE_COND .= ($s_lname == '') ? '' : "  (u.s_last_name LIKE '" . $s_lname . "%' )";


                if ($WHERE_COND_NOTEXACT != '')
                    $WHERE_COND_NOTEXACT .= ($s_lname == '') ? '' : " OR (u.s_last_name LIKE '" . $s_lname . "%' )";
                else
                    $WHERE_COND_NOTEXACT .= ($s_lname == '') ? '' : "  (u.s_last_name LIKE '" . $s_lname . "%' )";



                $s_email = get_formatted_string(trim($this->input->post('txt_email')));

                if ($WHERE_COND != '')
                    $WHERE_COND .= ($s_email == '') ? '' : " AND (binary u.s_email = '" . $s_email . "' )";
                else
                    $WHERE_COND .= ($s_email == '') ? '' : " (binary u.s_email = '" . $s_email . "' )";

                if ($WHERE_COND_NOTEXACT != '')
                    $WHERE_COND_NOTEXACT .= ($s_email == '') ? '' : " OR (binary u.s_email = '" . $s_email . "' )";
                else
                    $WHERE_COND_NOTEXACT .= ($s_email == '') ? '' : " (binary u.s_email = '" . $s_email . "' )";


                $s_church_name = get_formatted_string(trim($this->input->post('txt_church_name')));

                if ($WHERE_COND != '')
                    $WHERE_COND .= ($s_church_name == '') ? '' : " AND ( u.`s_church_name` LIKE '" . $s_church_name . "%' )";
                else
                    $WHERE_COND .= ($s_church_name == '') ? '' : "  ( u.`s_church_name` LIKE '" . $s_church_name . "%' )";

                if ($WHERE_COND_NOTEXACT != '')
                    $WHERE_COND_NOTEXACT .= ($s_church_name == '') ? '' : " OR ( u.`s_church_name` LIKE '" . $s_church_name . "%' )";
                else
                    $WHERE_COND_NOTEXACT .= ($s_church_name == '') ? '' : "  ( u.`s_church_name` LIKE '" . $s_church_name . "%' )";






                $s_denomination = intval(decrypt($this->input->post('denomination')));

                if ($WHERE_COND != '')
                    $WHERE_COND .= ($s_denomination == '-1' || $s_denomination == '0') ? '' : " AND ( u.`i_id_denomination` = '" . $s_denomination . "' )";
                else
                    $WHERE_COND .= ($s_denomination == '-1' || $s_denomination == '0') ? '' : "  ( u.`i_id_denomination` = '" . $s_denomination . "' )";



                if ($WHERE_COND_NOTEXACT != '')
                    $WHERE_COND_NOTEXACT .= ($s_denomination == '-1' || $s_denomination == '0') ? '' : " OR ( u.`i_id_denomination` = '" . $s_denomination . "' )";
                else
                    $WHERE_COND_NOTEXACT .= ($s_denomination == '-1' || $s_denomination == '0') ? '' : "  ( u.`i_id_denomination` = '" . $s_denomination . "' )";


                $s_age_from = get_formatted_string(trim($this->input->post('age_from')));
                $s_age_to = get_formatted_string(trim($this->input->post('age_to')));


                if ($WHERE_COND != '') {

                    if (($s_age_from == '-1' && $s_age_to == '-1')) {
                        $WHERE_COND .= '';
                    } elseif ($s_age_from != '-1' && $s_age_to != '-1') {
                        $WHERE_COND .= " AND ( u.`s_age` BETWEEN '" . $s_age_from . "' AND '" . ($s_age_to) . "' )";
                    } elseif ($s_age_from == '-1' && $s_age_to != '-1') {
                        $WHERE_COND .= " AND ( u.`s_age` = '" . ($s_age_to) . "' )";
                    } elseif ($s_age_from != '-1' && $s_age_to == '-1') {
                        $WHERE_COND .= " AND ( u.`s_age` = '" . ($s_age_from) . "' )";
                    }
                } else {
                    if (($s_age_from == '-1' && $s_age_to == '-1')) {
                        $WHERE_COND .= '';
                    } elseif ($s_age_from != '-1' && $s_age_to != '-1') {
                        $WHERE_COND .= "  ( u.`s_age` BETWEEN '" . $s_age_from . "' AND '" . ($s_age_to) . "' )";
                    } elseif ($s_age_from == '-1' && $s_age_to != '-1') {
                        $WHERE_COND .= "  ( u.`s_age` = '" . ($s_age_to) . "' )";
                    } elseif ($s_age_from != '-1' && $s_age_to == '-1') {
                        $WHERE_COND .= "  ( u.`s_age` = '" . ($s_age_from) . "' )";
                    }
                }

                if ($WHERE_COND_NOTEXACT != '') {

                    if (($s_age_from == '-1' && $s_age_to == '-1')) {
                        $WHERE_COND_NOTEXACT .= '';
                    } elseif ($s_age_from != '-1' && $s_age_to != '-1') {
                        $WHERE_COND_NOTEXACT .= " OR ( u.`s_age` BETWEEN '" . $s_age_from . "' AND '" . ($s_age_to) . "' )";
                    } elseif ($s_age_from == '-1' && $s_age_to != '-1') {
                        $WHERE_COND_NOTEXACT .= " OR ( u.`s_age` = '" . ($s_age_to) . "' )";
                    } elseif ($s_age_from != '-1' && $s_age_to == '-1') {
                        $WHERE_COND_NOTEXACT .= " OR ( u.`s_age` = '" . ($s_age_from) . "' )";
                    }
                } else {
                    if (($s_age_from == '-1' && $s_age_to == '-1')) {
                        $WHERE_COND_NOTEXACT .= '';
                    } elseif ($s_age_from != '-1' && $s_age_to != '-1') {
                        $WHERE_COND_NOTEXACT .= "  ( u.`s_age` BETWEEN '" . $s_age_from . "' AND '" . ($s_age_to) . "' )";
                    } elseif ($s_age_from == '-1' && $s_age_to != '-1') {
                        $WHERE_COND_NOTEXACT .= "  ( u.`s_age` = '" . ($s_age_to) . "' )";
                    } elseif ($s_age_from != '-1' && $s_age_to == '-1') {
                        $WHERE_COND_NOTEXACT .= "  ( u.`s_age` = '" . ($s_age_from) . "' )";
                    }
                }

                $language = (trim($this->input->post('language')));

                if ($WHERE_COND != '')
                    $WHERE_COND .= ($language == '-1') ? '' : " AND ( u.`s_languages` LIKE '" . $language . "%' )";
                else
                    $WHERE_COND .= ($language == '-1') ? '' : "  ( u.`s_languages` LIKE '" . $language . "%' )";

                if ($WHERE_COND_NOTEXACT != '')
                    $WHERE_COND_NOTEXACT .= ($language == '-1') ? '' : " OR ( u.`s_languages` LIKE '" . $language . "%' )";
                else
                    $WHERE_COND_NOTEXACT .= ($language == '-1') ? '' : "  ( u.`s_languages` LIKE '" . $language . "%' )";



                if ($WHERE_COND != '' && $WHERE_COMP_COND != '') {
                    $WH = $WHERE_COND . " AND (" . $WHERE_COMP_COND . ")";
                } else if ($WHERE_COND != '') {
                    $WH = $WHERE_COND;
                } else {
                    $WH = $WHERE_COMP_COND;
                }


                if ($WHERE_COND_NOTEXACT != '' && $WHERE_COMP_COND_NOTEXACT != '') {
                    $WH_NOEXACT = "( " . $WHERE_COND_NOTEXACT . " ) AND (" . $WHERE_COMP_COND_NOTEXACT . ")";
                } else if ($WHERE_COND_NOTEXACT != '') {
                    $WH_NOEXACT = $WHERE_COND_NOTEXACT;
                } else {
                    $WH_NOEXACT = $WHERE_COMP_COND_NOTEXACT;
                }

                /* 	echo '1--- '.$WH;
                  echo '2----'.$WH_NOEXACT; exit;
                 */
                $this->session->set_userdata('search_condition', $WH);
                $this->session->set_userdata('search_condition_nonexact', $WH_NOEXACT);



                $this->session->set_userdata('is_post_', '1');


                $this->session->set_userdata('preserve_search_condition', $WH);
                $this->session->set_userdata('preserve_search_condition_nonexact', $WH_NOEXACT);

                ## storing to preserve search on reload ##
                $this->session->set_userdata('s_fname', $s_fname);
                $this->session->set_userdata('s_email', $s_email);
                $this->session->set_userdata('s_lname', $s_lname);
                //$this->session->set_userdata('s_state' , $s_state);
                //$this->session->set_userdata('s_city' , $s_city);
                $this->session->set_userdata('s_church_name', $s_church_name);
                $this->session->set_userdata('e_gender', $e_gender);
                $this->session->set_userdata('s_denomination', $s_denomination);
                $this->session->set_userdata('s_age_from', $s_age_from);
                $this->session->set_userdata('s_age_to', $s_age_to);
                //$this->session->set_userdata('s_country' , $s_country);
                $this->session->set_userdata('language', $language);

                $this->session->set_userdata('location', $location);
            }




            $exclude_id_csv = $i_profile_id;
            $s_order_by = "`user_id` DESC ";
            #### PRESERVE SEARCH AFTER BACK ####
            if (isset($_POST['IS_PRESERVE_SEARCH']) && $_POST['IS_PRESERVE_SEARCH'] == 'Y') {

                $this->session->set_userdata('is_preserve_search', true);
                $s_where = $this->session->userdata('preserve_search_condition');
                $s_like_where = $this->session->userdata('preserve_search_condition_nonexact');
            } elseif ($this->session->userdata('is_preserve_search')) {
                $this->session->set_userdata('is_preserve_search', true);
                $s_where = $this->session->userdata('preserve_search_condition');
                $s_like_where = $this->session->userdata('preserve_search_condition_nonexact');
            } else {
                $this->session->set_userdata('is_preserve_search', false);
                $s_where = $this->session->userdata('search_condition');
                $s_like_where = $this->session->userdata('search_condition_nonexact');
            }


            if ($s_where != '') {
                $s_where = " AND (" . $s_where . ")";
            }

            if ($s_like_where != '') {
                $s_like_where = " AND (" . $s_like_where . ")";
            }

            if ((isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y'
                    ) || (isset($_POST['IS_PRESERVE_SEARCH']) && $_POST['IS_PRESERVE_SEARCH'] == 'Y')) {
				$check_councillor = check_prayer_councillor($i_profile_id);
				if($check_councillor == 0) {
					$is_councillor = " AND u.is_councillor = 1";
				}
				else {
					$is_councillor = "";
				}
                $EXACT_WHERE = " 
								  AND u.i_isdeleted = 1  
								  AND u.i_status=1  
								  AND u.e_want_prayer_partner = 'Y'
								  AND u.id NOT IN (" . $exclude_id_csv . ")
								  " . $s_where . "". $is_councillor ."  GROUP BY u.id";

                $LIKE_WHERE = " 
								  AND u.i_isdeleted = 1  
								  AND u.i_status=1  
								  AND u.e_want_prayer_partner = 'Y'
								  AND u.id NOT IN (" . $exclude_id_csv . ")
								  " . $s_like_where . "". $is_councillor ."  GROUP BY u.id";

                //$result = $this->my_prayer_partner_model->get_prayer_partner_suggestion($WHERE,$page,$this->pagination_per_page,$s_order_by);
                //echo 'EXACT_WHERE::: '.$EXACT_WHERE;
                //echo 'LIKE_WHERE::: '.$LIKE_WHERE; exit;

                $result = $this->my_prayer_partner_model->get_prayer_partner_sugg($EXACT_WHERE, $LIKE_WHERE, $s_order_by, $page, $this->pagination_per_page);
                $resultCount = count($result);
                #echo $this->db->last_query(); 
                //pr($result);
                $total_rows = $this->my_prayer_partner_model->get_prayer_partner_sug_total($EXACT_WHERE, $LIKE_WHERE);
            }
            ## end seacrh conditions : filter ############
            #pr($result);
            #Jquery Pagination Starts
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url() . "logged/my_prayer_partners/ajax_pagination";
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

            $config['div'] = '#srch_prayer_partner'; /* Here #content is the CSS selector for target DIV */
            $config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
            $config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */

            $this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();

            // getting   listing...
            $data['search_result_friends'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $page;
            $data['total_pages'] = ceil($total_rows / $this->pagination_per_page);

            //echo $data['total_pages'].' ==total_pages==== '.$page;
            //echo $data['current_page'].' ==  ';
            $data['post_val'] = ($total_rows > 0 ) ? 'true' : 'false';
            $p = ($page / $this->pagination_per_page);
            $data['current_loaded_page_no'] = $p + 1;



            $data['is_post_'] = $this->session->userdata('is_post_');
            # loading the view-part...
            echo $this->load->view('logged/my_prayer_partners/search_invite_prayer_partner_ajax.phtml', $data, TRUE);
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function invite_prayer_partner() {
        try {
            $send_request = false;
            $user_id = intval(decrypt($this->input->post('frnd_id')));   //acceptor ID
            $display_becomeprayer_partner = $this->input->post('display_becomeprayer_partner');

            $this->load->model('my_prayer_partner_model');

            $info['i_requester_id'] = intval(decrypt($this->session->userdata('user_id')));
            $info['i_accepter_id'] = $user_id;
            $info['s_status'] = 'pending';


            ## CHECKING TOTAL PRAYER PARTNERS ##
            $total_prayer_partners_arr = $this->my_prayer_partner_model->get_prayerPartnerId_by_user_id($info['i_requester_id'], $info['i_accepter_id']);
            $total_prayer_partners = count($total_prayer_partners_arr);

            $LIMIT_PP = $this->data['site_settings_arr']['i_max_prayer_partner'];
            $LIMIT_PP_SUGG = $this->data['site_settings_arr']['i_max_prayer_partner_sugg'];

            if (($total_prayer_partners < $LIMIT_PP && $total_prayer_partners != $LIMIT_PP) || $LIMIT_PP == 0) {
                ## checking total pending friend request  , should be < = 5##

                $total_pending_request = $this->my_prayer_partner_model->total_pending_prayer_partner_recieved($info['i_requester_id'], $info['i_accepter_id']);

                if (($total_pending_request < $LIMIT_PP_SUGG && $total_pending_request != $LIMIT_PP_SUGG) || $LIMIT_PP_SUGG == 0) {
                    $send_request = true;
                } else {
                    $prayer_p_flag = 'You already have ' . $LIMIT_PP_SUGG . ' outstanding prayer partner requests.';
                    if ($display_becomeprayer_partner == 'false'):
                        $html_txt = 'Re-send Partner Request';
                        $prayer_p_flag = 'You cannot have more than ' . $LIMIT_PP . 'prayer partners.';
                    else:
                        $html_txt = 'Invite As Partner';
                    endif;
                }
            }else {
                $prayer_p_flag = 'You cannot have more than ' . $LIMIT_PP . ' prayer partners.';
                if ($display_becomeprayer_partner == 'false'):
                    $html_txt = 'Re-send Partner Request';
                    $prayer_p_flag = 'You cannot have more than ' . $LIMIT_PP . 'prayer partners.';
                else:
                    $html_txt = 'Invite As Partner';
                endif;
            }
            ## CHECKING TOTAL PRAYER PARTNERS ##

            if ($send_request == true) {
                $is_exists = $this->my_prayer_partner_model->friend_request_already_sent($info['i_requester_id'], $info['i_accepter_id']);

                if ($is_exists) {
                    $_ret_id = 1;
                } else {
                    $_ret_id = $this->my_prayer_partner_model->add_info($info);
                }
                #echo $this->db->last_query();


                if ($_ret_id > 0) {
                    $message_id = parent::send_message($info['i_requester_id'], $info['i_accepter_id'], 'prayer_partner_request', '', $this->input->post('contact_message'));

                    ## check if opted for this notification or not ##
                    $notificaion_opt = $this->user_alert_model->check_option_user_id($info['i_accepter_id']);

                    ## insert noifications ####
                    if ($notificaion_opt['e_prayer_partner_request_received'] == 'Y') {
                        $notification_arr['i_requester_id'] = decrypt($this->session->userdata('user_id'));
                        $notification_arr['i_accepter_id'] = $info['i_accepter_id'];
                        $notification_arr['s_type'] = 'prayer_p';
                        $notification_arr['dt_created_on'] = get_db_datetime();

                        $ret = $this->user_notifications_model->insert($notification_arr);
                    }
                    //echo $this->db->last_query();	
					
					$email_opt = $this->user_alert_model->check_option_email_user_id($info['i_accepter_id']);
						if($email_opt['e_prayer_partner_request_received'] == 'Y'){
						
						$mail_arr['i_requester_id'] =get_username_by_id( decrypt($this->session->userdata('user_id')));
						$mail_arr['i_accepter_id'] =  get_username_by_id($info['i_accepter_id']);
						$mail_arr['s_type'] = 'prayer_p';
						$mail_id=get_useremail_by_id( $info['i_accepter_id']);
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

					$this->email->subject($mail_arr["i_requester_id"].' Wants to be your Prayer Partner!!');
					$this->email->message("$body");

					$this->email->send();
					}
                    ### end  ###

                    echo json_encode(array('success' => TRUE, 'msg' => 'Prayer Partner request sent successfully.', 'html_txt' => "Re-send Partner Request", 'u_id' => $user_id));
                } else {
                    echo json_encode(array('success' => FALSE, 'msg' => 'Error!', 'html_txt' => ''));
                }
            } else {

                echo json_encode(array('success' => TRUE, 'msg' => $prayer_p_flag, 'html_txt' => $html_txt, 'u_id' => $user_id));
            }
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function confirm_invitation_opt() {
        try {
            $send_msg = false;
            $this->load->model('users_model');
            if ($this->input->post('type') == 'accept') {

                $i_accepter_id = intval(decrypt($this->session->userdata('user_id')));
                $i_requester_id = intval($this->input->post('i_requester_id'));
                ## checking already accepted ###

                $get_friend_status_me_him = $this->my_prayer_partner_model->get_prayer_partner_accepted_me_him($i_requester_id, $i_accepter_id);
                if (count($get_friend_status_me_him) > 0) {
                    $data['display_alreadyprayer_partner'] = 'true';
                } else {
                    $data['display_alreadyprayer_partner'] = 'false';
                }

                #echo count($get_friend_status_me_him) ;
                ## CHECKING TOTAL PRAYER PARTNERS ##
                $total_prayer_partners_arr = $this->my_prayer_partner_model->get_prayerPartnerId_by_user_id($i_accepter_id, $i_requester_id);
                $total_prayer_partners = count($total_prayer_partners_arr);

                ## added new 
                $LIMIT_PP = $this->data['site_settings_arr']['i_max_prayer_partner'];
                $LIMIT_PP_SUGG = $this->data['site_settings_arr']['i_max_prayer_partner_sugg'];

                if (($total_prayer_partners < $LIMIT_PP && $total_prayer_partners != $LIMIT_PP) || $LIMIT_PP == 0) {

                    if ($data['display_alreadyprayer_partner'] == 'false') {
                        $_ret = $this->my_prayer_partner_model->update_by_requester_accepter(array('s_status' => 'accepted'), $i_requester_id, $i_accepter_id);

                        $prayer_p_msg = 'Prayer Partner accepted successfully!';
                    } else {
                        $_ret = 1;
                        $prayer_p_msg = 'Already Prayer Partner!';
                    }

                    $updated_flag = 'Y';
                    $send_msg = 'true';
                } else {
                    $prayer_p_msg = 'You cannot have more than three prayer partners!';
                    $_ret = 1;
                    $send_msg = 'false';
                    $updated_flag = 'N';
                }
                ## CHECKING TOTAL PRAYER PARTNERS ##




                if ($_ret > 0) {

                    //$this->load->model('data_messages_model');
                    //$this->data_messages_model->update_by_id( array('ended'=>'yes'), $message_id );
                    $i_msg_id = $this->input->post('i_message_id');
                    if ($send_msg == 'true') {
                        if (intval($i_msg_id) > 0)
                            $this->db->update('messages', array('i_ended' => '1'), array('id' => $i_msg_id, 'i_sender_id' => $i_requester_id));
                        else
                            $this->db->update('messages', array('i_ended' => '1'), array('s_type' => 'prayer_partner_request', 'i_sender_id' => $i_requester_id, 'i_receiver_id' => $i_accepter_id));

                        parent::send_message($i_accepter_id, $i_requester_id, 'prayer_partner_accept');

                        ## check if opted for this notification or not ##
                        $notificaion_opt = $this->user_alert_model->check_option_user_id($i_requester_id);

                        ## insert noifications ####
                        if ($notificaion_opt['e_prayer_partner_my_request_accepted'] == 'Y') {
                            $notification_arr['i_requester_id'] = $i_accepter_id;
                            $notification_arr['i_accepter_id'] = $i_requester_id;
                            $notification_arr['s_type'] = 'prayer_p_request_accepted';
                            $notification_arr['dt_created_on'] = get_db_datetime();

                            $ret = $this->user_notifications_model->insert($notification_arr);
                        }
                        ### end  ###
                    }


                    echo json_encode(array('success' => TRUE, 'msg' => $prayer_p_msg, 'updated_flag' => $updated_flag));
                } else {
                    echo json_encode(array('success' => FALSE, 'msg' => 'Error!'));
                }
            } else if ($this->input->post('type') == 'reject') {
                $i_accepter_id = intval(decrypt($this->session->userdata('user_id')));
                $i_requester_id = intval($this->input->post('i_requester_id'));

                //$i_message_id = $this->input->post('i_message_id');
                $_ret = $this->my_prayer_partner_model->update_by_requester_accepter(array('s_status' => 'rejected'), $i_requester_id, $i_accepter_id);

                if ($_ret > 0) {
                    //$this->load->model('data_messages_model');
                    //$this->data_messages_model->update_by_id( array('ended'=>'yes'), $message_id );
                    parent::send_message($i_accepter_id, $i_requester_id, 'prayer_partner_rejected');

                    ## check if opted for this notification or not ##
                    $notificaion_opt = $this->user_alert_model->check_option_user_id($i_requester_id);

                    ## insert noifications ####
                    if ($notificaion_opt['e_prayer_partner_request_declined'] == 'Y') {
                        $notification_arr['i_requester_id'] = $i_accepter_id;
                        $notification_arr['i_accepter_id'] = $i_requester_id;
                        $notification_arr['s_type'] = 'prayer_p_request_decline';
                        $notification_arr['dt_created_on'] = get_db_datetime();

                        $ret = $this->user_notifications_model->insert($notification_arr);
                    }
                    ### end  ###


                    echo json_encode(array('success' => TRUE, 'msg' => 'Prayer partner declined successfully!'));
                } else {
                    echo json_encode(array('success' => FALSE, 'msg' => 'Error!!!'));
                }
            }
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function cancel_prayer_partner_request() {
        try {
            $user_id = intval($this->input->post('frnd_id'));   //acceptor ID
            $this->load->model('my_prayer_partner_model');

            $info['i_requester_id'] = intval(decrypt($this->session->userdata('user_id')));
            $info['i_accepter_id'] = $user_id;
            //$info['s_status']    =    'pending' ; 
            $_ret_id = $this->my_prayer_partner_model->cancel_friend_request_sent($info);
            #echo $this->db->last_query();
            //$_ret_id = 1 ;	

            if ($_ret_id > 0) {

                $total_sent = $this->my_prayer_partner_model->total_pending_prayer_partner_sent($info['i_requester_id']);
                //$total_sent=0;
                if ($total_sent > 0) {
                    echo json_encode(array('success' => TRUE, 'msg' => 'Prayer partner request cancelled successfully.', 'html_txt' => "", 'u_id' => $user_id, 'last_record' => 'N'));
                } else {
                    echo json_encode(array('success' => TRUE, 'msg' => 'Prayer partner request cancelled successfully.', 'html_txt' => '<div class="shade_box_blue" style="padding-top:5px;"><div  class="shade_norecords" style="width:325px"><p class="blue_bold12">No Prayer Partner Request Sent.</p></div></div>', 'u_id' => $user_id, 'last_record' => 'Y'));
                }

                /* echo json_encode( array('success'=>TRUE, 'msg'=>'Prayer partner request cancelled successfully.' , 'html_txt'=>"" , 'u_id' => $user_id) ); */
            } else {
                echo json_encode(array('success' => FALSE, 'msg' => 'Error!', 'html_txt' => ''));
            }
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function decline_prayer_partner_request() {
        try {
            $user_id = intval($this->input->post('frnd_id'));   //acceptor ID
            $this->load->model('my_prayer_partner_model');

            $info['i_requester_id'] = $user_id;
            $info['i_accepter_id'] = intval(decrypt($this->session->userdata('user_id')));
            $info['s_status'] = 'pending';
            $_ret_id = $this->my_prayer_partner_model->decline_friend_request_recieved($info);
            #echo $this->db->last_query();
            //$_ret_id = 1;

            if ($_ret_id > 0) {

                parent::send_message($info['i_accepter_id'], $info['i_requester_id'], 'prayer_partner_rejected');

                ## check if opted for this notification or not ##
                $notificaion_opt = $this->user_alert_model->check_option_user_id($info['i_requester_id']);

                ## insert noifications ####
                if ($notificaion_opt['e_prayer_partner_request_declined'] == 'Y') {
                    $notification_arr['i_requester_id'] = decrypt($this->session->userdata('user_id'));
                    $notification_arr['i_accepter_id'] = $info['i_requester_id'];
                    $notification_arr['s_type'] = 'prayer_p_request_decline';
                    $notification_arr['dt_created_on'] = get_db_datetime();

                    $ret = $this->user_notifications_model->insert($notification_arr);
                }
				$email_opt = $this->user_alert_model->check_option_email_user_id($info['i_requester_id']);
						if($email_opt['e_prayer_partner_request_declined'] == 'Y'){
						
						$mail_arr['i_requester_id'] =get_username_by_id( decrypt($this->session->userdata('user_id')));
						$mail_arr['i_accepter_id'] =  get_username_by_id($info['i_requester_id']);
						$mail_arr['s_type'] = 'prayer_p_request_decline';
						$mail_id=get_useremail_by_id($info['i_requester_id']);
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

					$this->email->subject($mail_arr["i_requester_id"].' has refused your Prayer Partner request');
					$this->email->message("$body");

					$this->email->send();
					}
                ### end  ###




                $total_sent = $this->my_prayer_partner_model->total_pending_prayer_partner_recieved($info['i_accepter_id']);
                //$total_sent=0;
                if ($total_sent > 0) {
                    echo json_encode(array('success' => TRUE, 'msg' => 'Prayer partner request declined successfully.', 'html_txt' => "", 'u_id' => $user_id, 'last_record' => 'N'));
                } else {
                    echo json_encode(array('success' => TRUE, 'msg' => 'Prayer partner request removed successfully.', 'html_txt' => '<div class="shade_box_blue" style="padding-top:5px;"><div  class="shade_norecords" style="width:325px;"><p class="blue_bold12">No Prayer Partner Request Recieved.</p></div></div>', 'u_id' => $user_id, 'last_record' => 'Y'));
                }
            } else {
                echo json_encode(array('success' => FALSE, 'msg' => 'Error!', 'html_txt' => ''));
            }
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function confirm_invitation() {
        try {
            $this->load->model('users_model');
            $limit_exceed = '';

            $i_accepter_id = intval(decrypt($this->session->userdata('user_id')));
            $i_requester_id = intval($this->input->post('i_requester_id'));

            ## checking already accepted ###

            $get_friend_status_me_him = $this->my_prayer_partner_model->get_prayer_partner_accepted_me_him($i_requester_id, $i_accepter_id);
            if (count($get_friend_status_me_him) > 0) {
                $data['display_alreadyprayer_partner'] = 'true';
            } else {
                $data['display_alreadyprayer_partner'] = 'false';
            }


            ## CHECKING TOTAL PRAYER PARTNERS ##
            $total_prayer_partners_arr = $this->my_prayer_partner_model->get_prayerPartnerId_by_user_id($i_accepter_id, $i_requester_id);
            $total_prayer_partners = count($total_prayer_partners_arr);

            ## added new 
            $LIMIT_PP = $this->data['site_settings_arr']['i_max_prayer_partner'];
            $LIMIT_PP_SUGG = $this->data['site_settings_arr']['i_max_prayer_partner_sugg'];

            if ($data['display_alreadyprayer_partner'] == 'true') {
                $_ret = 1;
                $updated_flag = 'Y';
                $prayer_p_msg = 'Already Prayer Partner!';
            } else if (($total_prayer_partners < $LIMIT_PP && $total_prayer_partners != $LIMIT_PP) || $LIMIT_PP == 0) {

                if ($data['display_alreadyprayer_partner'] == 'false') {
                    $_ret = $this->my_prayer_partner_model->update_by_requester_accepter(array('s_status' => 'accepted'), $i_requester_id, $i_accepter_id);
                    $prayer_p_msg = 'Prayer Partner accepted successfully!';
                    $updated_flag = 'Y';
                    $this->db->update('messages', array('i_ended' => '1'), array('s_type' => 'prayer_partner_request', 'i_sender_id' => $i_requester_id, 'i_receiver_id' => $i_accepter_id));
                } else {
                    $_ret = 1;
                    $updated_flag = 'Y';
                    $prayer_p_msg = 'Already Prayer Partner!';
                }

                /* $updated_flag = 'Y';
                  $send_msg = 'true';


                  $_ret = $this->my_prayer_partner_model->update_by_requester_accepter( array('s_status'=>'accepted'), $i_requester_id, $i_accepter_id );
                  $prayer_p_msg= 'Prayer Partner accepted successfully!';
                  $updated_flag = 'Y'; */

                ## update req msg by i_ended = 1 ##
            } else {
                $prayer_p_msg = 'You cannot have more than three prayer partners!';
                $_ret = 1;
                $updated_flag = 'N';
                $limit_exceed = 'yes';
            }
            ## CHECKING TOTAL PRAYER PARTNERS ##
            /*             * ************************** */

            // $_ret = $this->my_prayer_partner_model->update_by_requester_accepter( array('s_status'=>'accepted'), $i_requester_id, $i_accepter_id ); #echo $_ret;

            if ($_ret > 0) {

                $i_record_id = $this->input->post('i_record_id');
                parent::send_message($i_accepter_id, $i_requester_id, 'prayer_partner_request');

                ## check if opted for this notification or not ##
                $notificaion_opt = $this->user_alert_model->check_option_user_id($i_requester_id);

                ## insert noifications ####
                if ($notificaion_opt['e_prayer_partner_request_received'] == 'Y') {
                    $notification_arr['i_requester_id'] = $i_accepter_id;
                    $notification_arr['i_accepter_id'] = $i_requester_id;
                    $notification_arr['s_type'] = 'prayer_p_request_accepted';
                    $notification_arr['dt_created_on'] = get_db_datetime();

                    $ret = $this->user_notifications_model->insert($notification_arr);
                }
				
				$email_opt = $this->user_alert_model->check_option_email_user_id($i_requester_id);
						if($email_opt['e_prayer_partner_request_received'] == 'Y'){
						
						$mail_arr['i_requester_id'] =get_username_by_id( $i_accepter_id);
						$mail_arr['i_accepter_id'] =  get_username_by_id($i_requester_id);
						$mail_arr['s_type'] = 'prayer_p_request_accepted';
						$mail_id=get_useremail_by_id($i_requester_id);
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

					$this->email->subject($mail_arr["i_requester_id"].' is now your Prayer Partner!!');
					$this->email->message("$body");

					$this->email->send();
					}
                ### end  ###
                //echo $limit_exceed;
                $total_sent = $this->my_prayer_partner_model->total_pending_prayer_partner_recieved($i_accepter_id);                               //$total_sent=0;
                if ($total_sent > 0) {
                    echo json_encode(array('success' => TRUE, 'msg' => $prayer_p_msg, 'html_txt' => "", 'u_id' => $i_requester_id, 'updated_flag' => $updated_flag, 'limit_exceed' => $limit_exceed));
                } else {
                    echo json_encode(array('success' => TRUE, 'msg' => $prayer_p_msg, 'html_txt' => '<div class="shade_box_blue" style="padding-top:5px;"><div  class="shade_norecords" style="width:325px;"><p class="blue_bold12">No Prayer Partner Request Received.</p></div></div>', 'u_id' => $i_requester_id, 'updated_flag' => $updated_flag, 'limit_exceed' => $limit_exceed));
                }


                //ho json_encode( array('success'=>TRUE, 'msg'=>$prayer_p_msg,'u_id'=> $i_requester_id,'updated_flag'=>$updated_flag) );
            } else {
                echo json_encode(array('success' => FALSE, 'msg' => 'Error!', 'u_id' => ''));
            }
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function delete_prayer_partners() {
        try {
            $user_id = intval($this->input->post('frnd_id'));   //acceptor ID
            $this->load->model('my_prayer_partner_model');

            $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
            $info['i_requester_id'] = $user_id;
            $info['i_accepter_id'] = intval(decrypt($this->session->userdata('user_id')));
            $info['s_status'] = 'accepted';
            $_ret_id = $this->my_prayer_partner_model->delete_friend($info);
            #echo $this->db->last_query();
            #$_ret_id =1;

            if ($_ret_id > 0) {

                $total_where = " WHERE 
										  1
										  AND c.s_status = 'accepted' 
										  AND u.i_status=1 
										  AND
										  ((c.i_requester_id = '" . $i_profile_id . "' AND u.id=c.i_accepter_id ) 
										  OR (c.i_accepter_id = '" . $i_profile_id . "' AND u.id=c.i_requester_id )) " . $add_where . " GROUP BY u.id ";
                $total_frnd_left = $this->my_prayer_partner_model->gettotal_online_friends($total_where);
                #$total_frnd_left=0;
                if ($total_frnd_left > 0) {

                    parent::send_message($info['i_accepter_id'], $info['i_requester_id'], 'prayer_partner_deleted');
                    echo json_encode(array('success' => TRUE, 'msg' => 'Prayer partner successfully.', 'html_txt' => "", 'u_id' => $user_id, 'last_record' => 'N'));
                } else {

                    parent::send_message($info['i_accepter_id'], $info['i_requester_id'], 'prayer_partner_deleted');
                    echo json_encode(array('success' => TRUE, 'msg' => 'Prayer partner removed successfully.', 'html_txt' => '<div class="shade_box_blue" style="padding-top:5px;"><div  class="shade_norecords" style="width:275px;"><p class="blue_bold12">No Prayer Patners.</p></div></div>', 'u_id' => $user_id, 'last_record' => 'Y'));
                }

                #parent::send_message($info['i_accepter_id'], $info['i_requester_id'] , 'contact_rejected');
            } else {
                echo json_encode(array('success' => FALSE, 'msg' => 'Error!', 'html_txt' => ''));
            }
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    ## ENLISTING PRAYER PARTNER

    function enlist_prayer_partner() {
        $self_id = intval(decrypt($this->session->userdata('user_id')));

        //$this->db->update()
        $_ret = $this->db->update('users', array('e_want_prayer_partner' => 'Y'), array('id' => $self_id));
        if ($_ret > 0) {
            echo json_encode(array('success' => true, 'msg' => 'You have successfully enlisted for Prayer Partner.'));
        } else {
            echo json_encode(array('success' => false, 'msg' => 'Sorry! Some error occure. Please try again.'));
        }
    }

    ### opt out from prayer partner
    ## ENLISTING PRAYER PARTNER

    function optout() {
        $self_id = intval(decrypt($this->session->userdata('user_id')));

        //$this->db->update()
        $_ret = $this->db->update('users', array('e_want_prayer_partner' => 'N'), array('id' => $self_id));
        if ($_ret > 0) {
            echo json_encode(array('success' => true, 'msg' => 'You have successfully opted out from Prayer Partner.'));
        } else {
            echo json_encode(array('success' => false, 'msg' => 'Sorry! Some error occure. Please try again.'));
        }
    }

    ### ---------------------------------------WRITE PRAYER PARTNER---------------------------------------- ###

    function write_prayer_partner() {
        $info['i_giving_user_id'] = intval(decrypt($this->session->userdata('user_id')));
        $info['i_rec_user_id'] = $this->input->post('frnd_id');


        //$if_exists = $this->my_prayer_partner_model->fetch_prayer_partner($info);

        $info['s_desc'] = get_formatted_string(trim($this->input->post('des')));
        if ($info['s_desc'] != '') {
            /* if($if_exists)
              {
              $info['dt_upadted_on'] = get_db_datetime();

              $res = $this->my_prayer_partner_model->update_prayer_partner_point($info);
              }
              else
              { */
            $info['dt_created_on'] = get_db_datetime();
            $res = $this->my_prayer_partner_model->insert_prayer_partner_point($info);
            //}
            ## insert noifications ####
            $notification_arr['i_requester_id'] = decrypt($this->session->userdata('user_id'));
            $notification_arr['i_accepter_id'] = $info['i_rec_user_id'];
            $notification_arr['s_type'] = 'prayer_points';
            $notification_arr['dt_created_on'] = get_db_datetime();

            $ret = $this->user_notifications_model->insert($notification_arr);
        } else {
            echo json_encode(array('success' => 'false', 'msg' => 'Please enter some text.'));
            exit;
        }



        if ($res) {
            echo json_encode(array('success' => 'true', 'msg' => 'Your prayer has been successfully posted.'));
        } else
            echo json_encode(array('success' => 'false', 'msg' => 'Error. Sorry. Try again.'));

        //$this->my_prayer_partner_model->fetch_prayer_partner($info);
        //$qry = $this->db->insert('prayer_partners_prayer_points',$info);
    }

    function edit_prayer_partner() {
        $info['i_giving_user_id'] = intval(decrypt($this->session->userdata('user_id')));
        $info['i_rec_user_id'] = $this->input->post('frnd_id');

        $row_id = $this->input->post('row_id');

        $info['s_desc'] = get_formatted_string(trim($this->input->post('des')));
        $info['prev_desc'] = get_formatted_string(trim($this->input->post('prev_desc')));
        if ($info['s_desc'] != '') {
            $info['dt_upadted_on'] = get_db_datetime();
            $res = $this->my_prayer_partner_model->edit_prayer_partner_point($info, $row_id);
            if ($res) {
                echo json_encode(array('success' => 'true', 'msg' => 'Your prayer has been successfully updated.'));
            } else {
                echo json_encode(array('success' => 'false', 'msg' => 'Error. Sorry. Try again.'));
            }
        } else {
            echo json_encode(array('success' => 'false', 'msg' => 'Please enter some text.'));
            exit;
        }
    }

    function read_prayer_partner() {
        $info['i_giving_user_id'] = intval(decrypt($this->session->userdata('user_id')));
        $info['i_rec_user_id'] = $this->input->post('frnd_id');
        $frnd_id = $info['i_rec_user_id'];
        $if_exists = $this->my_prayer_partner_model->fetch_prayer_partner_points($info);


        //pr($if_exists);
        if (count($if_exists) > 0) {
            $des = "<ul>";
            $counter = 0;
            $i = 0;
            $j = 1;
            $count = count($if_exists);
            foreach ($if_exists as $r) {

                $counter++;
                if ($counter == $count) {
                    $i++;
                    # CHECKING GIVING USERID IS SAME AS LOGGED USER ID  TO ENABLE DELETE OPTION
                    if ($r['i_giving_user_id'] == $info['i_giving_user_id']) {
                        $desc = nl2br($r['s_desc']);
                        $prev_desc = nl2br($r['prev_desc']);
                        if ($prev_desc == '') {
                            $des.='<li id="pp_' . $r['id'] . '">'
                                    . '<div class="parent_div">'
                                    . '<input type="hidden" class="row_id_' . $i . '" value="' . $r['id'] . '">'
                                    . '<div class="content" style="word-wrap: break-word;">'
                                    . '<div><strong>' . $r['s_first_name'] . ' '
                                    // . '<a style="margin-left:10px;" href="javascript:void(0);" class="right" onclick="delete_(' . $r['id'] . ')">'
                                    //   . '<img src="' . base_url() . 'images/icons/delete_small.png"/></a>'
                                    . '<a style="margin-left:7px; float:right;cursor:pointer;"  href="javascript:void(0);" class="edit">edit</a>'
                                    . '</strong></div>'
                                    . '<div class="clr"></div> <div>' . nl2br($r['s_desc']) . '</div><br>'
                                    . '<div class="date-time">Created On: ' . getShortDateWithTime($r['dt_created_on'], 9) . '</div><br>'
                                    . '<input type="hidden" class="prev_desc_' . $i . '"  value="' . nl2br($r['s_desc']) . '">'
                                    . '<div style="clear:both;display:none" class="edit-prayer-partner">'
                                    . ' <p style="padding-top:8px; clear:both;" class="blue12">Edit Prayer Points</p>
                                    <p><textarea class="text_area_prayer_point" id="text_area_edit_prayer_point_' . $i . '" style="width:428px; height:100px;">' . nl2br($r['s_desc']) . '</textarea></p>'
                                    . '<p class="blue12" style="padding-bottom:8px;"></p>'
                                    . '<p><input name="update_btn"  type="button" value="Update" class="btn update_btn" onclick="edit_prayer_partner_(' . $frnd_id . ',' . $i . ')" /></p>'
                                    . '</div>'
                                    . '</div>'
                                    . '</li>';
                        } else {
                            $des.='<li id="pp_' . $r['id'] . '">'
                                    . '<div class="parent_div">'
                                    . '<input type="hidden" class="row_id_' . $i . '" value="' . $r['id'] . '">'
                                    . '<div class="content" style="word-wrap: break-word;">'
                                    . '<div><strong>' . $r['s_first_name'] . ' '
                                    //  . '<a style="margin-left:10px;" href="javascript:void(0);" class="right" onclick="delete_(' . $r['id'] . ')">'
                                    //    . '<img src="' . base_url() . 'images/icons/delete_small.png"/></a>'
                                    . '<a style="margin-left:7px; float:right;cursor:pointer;"  href="javascript:void(0);" class="edit">edit</a>'
                                    . '</strong></div>'
                                    . '<div style="color:#494949; padding-bottom:7px;"> ' . nl2br($r['prev_desc']) . '</div>'
                                    . '<p class="date-time" style="padding-bottom: 10px;">Created On: ' . getShortDateWithTime($r['dt_created_on'], 9) . '</p> <br>'
                                    . '<div class="clr"></div><div style="padding-bottom:7px;"> ' . nl2br($r['s_desc']) . '</div>'
                                    . '<div class="date-time">Updated On: ' . getShortDateWithTime($r['dt_upadted_on'], 9) . '</div><br>'
                                    . '<input type="hidden" class="prev_desc_' . $i . '"  value="' . nl2br($r['s_desc']) . '">'
                                    . '<div style="clear:both;display:none" class="edit-prayer-partner">'
                                    . ' <p style="padding-top:8px; clear:both;" class="blue12">Edit Prayer Points</p>
                                    <p><textarea class="text_area_prayer_point" id="text_area_edit_prayer_point_' . $i . '" style="width:428px; height:100px;">' . nl2br($r['s_desc']) . '</textarea></p>'
                                    . '<p class="blue12" style="padding-bottom:8px;"></p>'
                                    . '<p><input name="update_btn"  type="button" value="Update" class="btn update_btn" onclick="edit_prayer_partner_(' . $frnd_id . ',' . $i . ')" /></p>'
                                    . '</div>'
                                    . '</div>'
                                    . '</li>';
                        }
                    } else {
                        $des.='<li class="no-brdr" id="pp_' . $r['id'] . '"><div class="content" style="word-wrap: break-word;"><strong>' . $r['s_first_name'] . '<p class="date-time" style="margin-top:2px;float:right;">' . getShortDateWithTime($r['dt_created_on'], 9) . '</p></strong><div class="clr"></div> ' . nl2br($r['s_desc']) . '</li>';
                    }
                } else {

                    if ($r['i_giving_user_id'] == $info['i_giving_user_id']) {
                        $j++;
                        $desc = nl2br($r['s_desc']);
                        $prev_desc = nl2br($r['prev_desc']);
                        if ($prev_desc == '') {
                            $des.='<li id="pp_' . $r['id'] . '">'
                                    . '<div class="parent_div">'
                                    . '<input type="hidden" class="row_id_' . $j . '" value="' . $r['id'] . '">'
                                    . '<div class="content" style="word-wrap: break-word;">'
                                    . '<div><strong>' . $r['s_first_name'] . ' '
                                    // . '<a style="margin-left:10px;" href="javascript:void(0);" class="right" onclick="delete_(' . $r['id'] . ')">'
                                    //  . '<img src="' . base_url() . 'images/icons/delete_small.png"/></a>'
                                    . '<a style="margin-left:7px; float:right;cursor:pointer;"  href="javascript:void(0);" class="edit">edit</a>'
                                    . '</strong></div>'
                                    . '<div class="clr"></div> <div>' . nl2br($r['s_desc']) . '</div><br>'
                                    . '<div class="date-time">Created On: ' . getShortDateWithTime($r['dt_created_on'], 9) . '</div><br>'
                                    . '<input type="hidden" class="prev_desc_' . $j . '"  value="' . nl2br($r['s_desc']) . '">'
                                    . '<div style="clear:both;display:none" class="edit-prayer-partner">'
                                    . ' <p style="padding-top:8px; clear:both;" class="blue12">Edit Prayer Points</p>
                                    <p><textarea class="text_area_prayer_point" id="text_area_edit_prayer_point_' . $j . '" style="width:428px; height:100px;">' . nl2br($r['s_desc']) . '</textarea></p>'
                                    . '<p class="blue12" style="padding-bottom:8px;"></p>'
                                    . '<p><input name="update_btn"  type="button" value="Update" class="btn update_btn" onclick="edit_prayer_partner_(' . $frnd_id . ',' . $j . ')" /></p>'
                                    . '</div>'
                                    . '</div>'
                                    . '</li>';
                        } else {
                            $des.='<li id="pp_' . $r['id'] . '">'
                                    . '<div class="parent_div">'
                                    . '<input type="hidden" class="row_id_' . $j . '" value="' . $r['id'] . '">'
                                    . '<div class="content" style="word-wrap: break-word;">'
                                    . '<div><strong>' . $r['s_first_name'] . ' '
                                    // . '<a style="margin-left:10px;" href="javascript:void(0);" class="right" onclick="delete_(' . $r['id'] . ')">'
                                    //  . '<img src="' . base_url() . 'images/icons/delete_small.png"/></a>'
                                    . '<a style="margin-left:7px; float:right;cursor:pointer;"  href="javascript:void(0);" class="edit">edit</a>'
                                    . '</strong></div>'
                                    . '<div style="color:#494949; padding-bottom:7px;"> ' . nl2br($r['prev_desc']) . '</div>'
                                    . '<p class="date-time" style="padding-bottom: 10px;">Created On: ' . getShortDateWithTime($r['dt_created_on'], 9) . '</p> <br>'
                                    . '<div class="clr"></div><div style="padding-bottom:7px;"> ' . nl2br($r['s_desc']) . '</div>'
                                    . '<div class="date-time">Updated On: ' . getShortDateWithTime($r['dt_upadted_on'], 9) . '</div><br>'
                                    . '<input type="hidden" class="prev_desc_' . $j . '"  value="' . nl2br($r['s_desc']) . '">'
                                    . '<div style="clear:both;display:none" class="edit-prayer-partner">'
                                    . ' <p style="padding-top:8px; clear:both;" class="blue12">Edit Prayer Points</p>
                                    <p><textarea class="text_area_prayer_point" id="text_area_edit_prayer_point_' . $j . '" style="width:428px; height:100px;">' . nl2br($r['s_desc']) . '</textarea></p>'
                                    . '<p class="blue12" style="padding-bottom:8px;"></p>'
                                    . '<p><input name="update_btn"  type="button" value="Update" class="btn update_btn" onclick="edit_prayer_partner_(' . $frnd_id . ',' . $j . ')" /></p>'
                                    . '</div>'
                                    . '</div>'
                                    . '</li>';
                        }
                    } else {
                        $des.='<li id="pp_' . $r['id'] . '"><div class="content" style="word-wrap: break-word;"><strong>' . $r['s_first_name'] . ' <p class="date-time" style="margin-top:2px;float:right;">' . getShortDateWithTime($r['dt_created_on'], 9) . '</p></strong><div class="clr"></div> ' . nl2br($r['s_desc']) . '</li>';
                    }
                }
            }
            $des.='</ul>';
            echo json_encode(array('des' => base64_encode($des)));
        } else
            echo json_encode(array('des' => base64_encode('<ul><li class="no-brdr"><div class="content" style="margin-left: 180px;">No prayer points.</div></li></ul>')));
    }

    public function delete_prayer_points() {
        try {
            $record_id = $this->input->post('rec_id');
            $_ret_id = $this->my_prayer_partner_model->delete_prayer_points($record_id);
            //echo $this->db->last_query();
            if ($_ret_id > 0) {

                echo json_encode(array('success' => TRUE, 'msg' => 'Prayer points removed successfully.', 'rec_id' => $record_id));
            } else {
                echo json_encode(array('success' => FALSE, 'msg' => 'Error!'));
            }
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

}

// end of controller...

