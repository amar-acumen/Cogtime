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

class My_net_pals extends Base_controller {

    private $pagination_per_page = 10;
    private $netpals_pagination_per_page = 10;
    private $landing_netpals_pagination_per_page = 10;

    public function __construct() {
        try {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...
            $this->load->model('users_model');
            $this->load->model('contacts_model');
            $this->load->model('netpals_model');
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


            parent::_add_js_arr(array('js/ddsmoothmenu.js',
                'js/switch.js', 'js/animate-collapse.js',
                'js/lightbox.js', 'js/jquery.dd.js', 'js/jquery-ui-1.8.2.custom.min.js',
                'js/stepcarousel.js',
                'js/jquery.form.js',
                'js/jquery/JSON/json2.js',
                'js/frontend/logged/tweets/tweet_utilities.js',
                'js/frontend/logged/my_net_pals.js'
            ));

            parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
                'css/dd.css'));

            $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
            $data['page_view_type'] = 'myaccount';
            $arr_profile_info = $this->users_model->fetch_this($i_profile_id);
            $data['arr_profile_info'] = $arr_profile_info;

            $this->session->set_userdata('search_condition', '');
            if ($arr_profile_info['s_want_net_pal'] == 'No') {
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
                // pr($qualification_params);
                $data['qualification_params'] = $qualification_params;
                $view = "logged/net_pals/enlist_net_pal.phtml";
                parent::_render($data, $view);
            }
            //$arr_profile_info['s_want_net_pal'];
            else {
                ob_start();
                $this->netpals_ajax_pagination();
                $data['result_arr'] = ob_get_contents();
                ob_end_clean();

                /*   ### showing Prayer Partner request sent ###
                  ob_start();
                  $this->request_recieved_landing_page_ajax_pagination();
                  $data['content2'] = ob_get_contents();
                  ob_end_clean();

                  ### showing Prayer Partner requset recived ## */

                # view file...
                $VIEW = "logged/net_pals/my_net_pals.phtml";
                parent::_render($data, $VIEW);
            }
        } catch (Exception $err_obj) {
            
        }
    }

    public function netpals_ajax_pagination($page = 0) {
        try {
            $add_where = '';
            #$show = '-1';
            /* $this->session->set_userdata('is_post_','');
              $this->session->set_userdata('search_condition',''); */
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
            ### showing Friend request sent ###
            $add_where = $this->session->userdata('search_condition');
            //echo "where condition in session : ".$add_where."---";
            if ($add_where != '') {
                $add_where = " AND (" . $add_where . ")";
            } {
                $WHERE = " WHERE 
                        1
                        
                        AND u.i_status=1 
                        
                        AND ((c.i_requester_id = " . $i_profile_id . " AND u.id=c.i_accepter_id) 
                            OR 
                        (c.i_accepter_id=" . $i_profile_id . " AND u.id=c.i_requester_id))
                        AND c.s_status='accepted' " . $add_where . " GROUP BY u.id ";



                $ORDER_BY = "u.id DESC";

                $total_where = " WHERE 
                                    1
                                   
                                    AND u.i_status=1 
                                    
                                    AND ((c.i_requester_id = " . $i_profile_id . " AND u.id=c.i_accepter_id) 
                                        OR 
                                    (c.i_accepter_id=" . $i_profile_id . " AND u.id=c.i_requester_id))
                                    AND c.s_status='accepted' " . $add_where . " GROUP BY u.id ";

                $result = $this->netpals_model->fetch_multi_online_netpals($WHERE, intval($page), $this->netpals_pagination_per_page, $ORDER_BY);
                //echo $this->db->last_query();
                //pr($result);
                $resultCount = count($result);

                $total_rows = $this->netpals_model->gettotal_online_netpals($total_where);

                if ((!is_array($result) || !count($result) ) && $total_rows) {
                    $page = $page - $this->netpals_pagination_per_page;

                    $result = $this->netpals_model->fetch_multi_online_netpals($WHERE, intval($page), $this->netpals_pagination_per_page, $ORDER_BY);
                }
            }
            #echo $resultCount = count($result);
            //echo $this->db->last_query(); 
            #($result);
            ## end seacrh conditions : filter ############
            //pr($result);
            #Jquery Pagination Starts
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url() . "logged/my_net_pals/netpals_ajax_pagination";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->netpals_pagination_per_page;
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

            $config['div'] = '#net_pals'; /* Here #content is the CSS selector for target DIV */
            $config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
            $config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */

            $this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();

            // getting   listing...
            $data['netpals_result_content'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $page;
            $data['total_pages'] = ceil($total_rows / $this->netpals_pagination_per_page);

            //echo $data['total_pages'].' ==total_pages==== '.$page;
            //echo $data['current_page'].' ==  ';
            $p = ($page / $this->netpals_pagination_per_page);
            $data['current_loaded_page_no'] = $p + 1;
            $data['is_post_'] = $this->session->userdata('is_post_');
            # loading the view-part...
            echo $this->load->view('logged/net_pals/my_net_pals_ajax.phtml', $data, TRUE);
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function request_recieved_landing_page_ajax_pagination($page = 0) {
        try {
            $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
            ### showing Friend request sent ###
            $WHERE = " WHERE 
                        1
                        AND i_deleted_by = 1
                        AND n.s_status = 'pending' 
                        AND u.i_status=1 
                        AND
                        ((n.i_accepter_id = '" . $i_profile_id . "' AND u.id=n.i_requester_id ) ) GROUP BY u.id";
            /* AND u.i_country_id=cn.id */
            $ORDER_BY = "n.id DESC";

            $result = $this->netpals_model->fetch_multi($WHERE, intval($page), $this->landing_netpals_pagination_per_page, $ORDER_BY);
            $resultCount = count($result);
            
            $total_rows = $this->netpals_model->gettotal_info($WHERE);

            if ((!is_array($result) || !count($result) ) && $total_rows) {
                $page = $page - $this->pagination_per_page;

                $result = $this->netpals_model->fetch_multi($WHERE, intval($page), $this->landing_netpals_pagination_per_page, $ORDER_BY);
            }
            
            // getting   listing...
            $data['result_arr'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $page;
            $data['total_pages'] = ceil($total_rows / $this->landing_netpals_pagination_per_page);

            //echo $data['total_pages'].' ==total_pages==== '.$page;
            //echo $data['current_page'].' ==  ';
            $p = ($page / $this->landing_netpals_pagination_per_page);
            $data['current_loaded_page_no'] = $p + 1;


            # loading the view-part...
            echo $this->load->view('logged/net_pals/net_pal_request_receive_landing_page_ajax.phtml', $data, TRUE);
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    // --------------------------------------------- search section ------------------------------------------------
    public function search_invite_net_pals() {
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
            'js/frontend/logged/tweets/tweet_utilities.js',
            'js/frontend/logged/my_net_pals.js'
        ));

        parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
            'css/dd.css'));

        $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        //$total_user = $this->my_prayer_partner_model->get_prayerPartnerId_by_user_id($i_profile_id);
        #pr($total_user);
        #$data['search_'] = '';
        ## seacrh conditions : filter ############


        /*     if($_POST ) {

          $WHERE_COND = "";

          $gender = get_formatted_string(trim($this->input->post('gender')));
          if($WHERE_COND != ''){
          $WHERE_COND .= ($gender=='0')?'':" OR (u.e_gender = '".$gender."' )";
          }else{
          $WHERE_COND .= ($gender=='0')?'':" (u.e_gender = '".$gender."' )";
          }


          $language = get_formatted_string(trim($this->input->post('txt_language')));
          if($WHERE_COND != '')
          $WHERE_COND .= ($language=='')?'':" OR (u.s_languages LIKE '%".$language."%' )";
          else
          $WHERE_COND .= ($language=='')?'':"  (u.s_languages LIKE '%".$language."%' )";



          $age = get_formatted_string(trim($this->input->post('agerange')));

          if($age!=0)
          {
          if($age==20)
          {
          if($WHERE_COND != '')
          $WHERE_COND .= " OR (u.s_age <= '".$age."' )";
          else
          $WHERE_COND .=  " (u.s_age <= '".$age."' )";
          }
          elseif($age==51)
          {
          if($WHERE_COND != '')
          $WHERE_COND .= " OR (u.s_age >= '".$age."' )";
          else
          $WHERE_COND .=  " (u.s_age >= '".$age."' )";
          }
          else
          {
          $age_to=$age;
          $age_from=$age-4;
          if($WHERE_COND != '')
          $WHERE_COND .= " OR (u.s_age BETWEEN ".$age_from." AND ".$age_to.")";
          else
          $WHERE_COND .= " (u.s_age BETWEEN ".$age_from." AND ".$age_to.")";
          }
          }




          /*$s_church_address = get_formatted_string(trim($this->input->post('txt_church_loc')));
          if($WHERE_COND != '')
          $WHERE_COND .= ($s_church_address=='')?'':" OR ( u.`s_church_address` LIKE '".$s_church_address." %'
          OR u.`s_church_city` LIKE '".$s_church_address." %'
          OR u.`s_church_state` LIKE '".$s_church_address." %')";
          else
          $WHERE_COND .= ($s_church_address=='')?'':" (u.`s_church_address` LIKE '".$s_church_address." %'
          OR u.`s_church_city` LIKE '".$s_church_address." %'
          OR u.`s_church_state` LIKE '".$s_church_address." %')"; */
        #echo $WHERE_COND;exit;

        /*               $this->session->set_userdata('search_condition',$WHERE_COND);
          $this->session->set_userdata('is_post_','1');


          }else
          {
          $this->session->unset_userdata('is_post_');
          $this->session->unset_userdata('search_condition');
          }

          // echo $WHERE_COND;
          $s_order_by = "`id` DESC ";
          #$where = " WHERE 1 " ;
         */

        $this->session->set_userdata('search_condition', '');
        $this->session->set_userdata('is_post_', '');
        $this->session->set_userdata('is_preserve_search', false);

        ob_start();
        $this->ajax_pagination();
        $data['result_content'] = ob_get_contents(); //pr($data['result_content']);
        ob_end_clean();

        //$data['_friends'] = $this->users_model->get_friend_status_me_him(1,2);
        //pr($data['_friends']);
        ###########3
        # view file...
        $VIEW = "logged/net_pals/search_invite_net_pals.phtml";
        parent::_render($data, $VIEW);
    }

    public function ajax_pagination($page = 0) {

        try {
            ## seacrh conditions : filter ############
            #echo $page .'- page, pagination - ' .$this->pagination_per_page;
            $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
            $exclude_id_csv = $i_profile_id;
            //$exclude_id_arr = $this->netpals_model->get_netpalsId_by_user_id($i_profile_id);
            //pr($exclude_id_arr);
            /* if(count($exclude_id_arr)){

              $exclude_id_csv = implode(', ',$exclude_id_arr);
              $exclude_id_csv .= ', '.$i_profile_id;
              }else{
              $exclude_id_csv = $i_profile_id;
              }
             */
            if (((isset($_POST['search_basic']) && $_POST['search_basic'] == 'y'))) {


                $WHERE_COND = "";

                $gender = get_formatted_string(trim($this->input->post('gender')));
                if ($WHERE_COND != '') {
                    $WHERE_COND .= ($gender == '0') ? '' : " AND (u.e_gender = '" . $gender . "' )";
                } else {
                    $WHERE_COND .= ($gender == '0') ? '' : " (u.e_gender = '" . $gender . "' )";
                }


                $language = get_formatted_string(trim($this->input->post('txt_language')));
                if ($WHERE_COND != '')
                    $WHERE_COND .= ($language == '') ? '' : " AND (u.s_languages LIKE '%" . $language . "%' )";
                else
                    $WHERE_COND .= ($language == '') ? '' : "  (u.s_languages LIKE '%" . $language . "%' )";



                $age = get_formatted_string(trim($this->input->post('agerange')));

                if ($age != 0) {
                    if ($age == 20) {
                        if ($WHERE_COND != '')
                            $WHERE_COND .= " AND (u.s_age <= '" . $age . "' )";
                        else
                            $WHERE_COND .= " (u.s_age <= '" . $age . "' )";
                    }
                    elseif ($age == 51) {
                        if ($WHERE_COND != '')
                            $WHERE_COND .= " AND (u.s_age >= '" . $age . "' )";
                        else
                            $WHERE_COND .= " (u.s_age >= '" . $age . "' )";
                    }
                    else {
                        $age_to = $age;
                        $age_from = $age - 4;
                        if ($WHERE_COND != '')
                            $WHERE_COND .= " AND (u.s_age BETWEEN " . $age_from . " AND " . $age_to . ")";
                        else
                            $WHERE_COND .= " (u.s_age BETWEEN " . $age_from . " AND " . $age_to . ")";
                    }
                }




                /* $s_church_address = get_formatted_string(trim($this->input->post('txt_church_loc')));
                  if($WHERE_COND != '')
                  $WHERE_COND .= ($s_church_address=='')?'':" OR ( u.`s_church_address` LIKE '".$s_church_address." %'
                  OR u.`s_church_city` LIKE '".$s_church_address." %'
                  OR u.`s_church_state` LIKE '".$s_church_address." %')";
                  else
                  $WHERE_COND .= ($s_church_address=='')?'':" (u.`s_church_address` LIKE '".$s_church_address." %'
                  OR u.`s_church_city` LIKE '".$s_church_address." %'
                  OR u.`s_church_state` LIKE '".$s_church_address." %')"; */
                #echo $WHERE_COND;exit;

                $this->session->set_userdata('search_condition', $WHERE_COND);
                $this->session->set_userdata('is_post_', '1');

                $this->session->set_userdata('preserve_search_condition', $WHERE_COND);

                ## storing to preserve search on reload ##
                $this->session->set_userdata('s_age_from', $age_from);
                $this->session->set_userdata('s_age_to', $age_to);
                $this->session->set_userdata('age', $age);
                $this->session->set_userdata('e_gender', $gender);
                $this->session->set_userdata('language', $language);
                //echo 'asda';
            }


            //  echo 'SB ===> '.$_POST['search_basic'].'  PS ===> '.$_POST['IS_PRESERVE_SEARCH']; 
            //$s_order_by = "`id` DESC ";

            $s_order_by = "`user_id` DESC ";
            if (isset($_POST['IS_PRESERVE_SEARCH']) && $_POST['IS_PRESERVE_SEARCH'] == 'Y') {
                $this->session->set_userdata('is_post_', '1');
                $this->session->set_userdata('is_preserve_search', true);
                $s_where = $this->session->userdata('preserve_search_condition');
            } elseif ($this->session->userdata('is_preserve_search')) {

                $this->session->set_userdata('is_post_', '1');
                $this->session->set_userdata('is_preserve_search', true);
                $s_where = $this->session->userdata('preserve_search_condition');
            } else {

                $this->session->set_userdata('is_preserve_search', false);
                $s_where = $this->session->userdata('search_condition');
            }

            if ($s_where != '') {

                if ($s_where != '') {
                    $s_where = " AND (" . $s_where . ")";
                }
                $WHERE = " WHERE 1 
                         AND u.i_isdeleted = 1  
                        
                         AND u.i_status=1  
                       
                          AND u.e_want_net_pal = 'Y'
                          AND u.id NOT IN (" . $exclude_id_csv . ")
                           " . $s_where . "  GROUP BY u.id";


                $result = $this->netpals_model->get_netpal_suggestion($WHERE, $page, $this->pagination_per_page, $s_order_by);
                //pr($result);
                $resultCount = count($result);



                $total_rows = $this->netpals_model->gettotal_netpal_suggestion($WHERE);

                if ((!is_array($result) || !count($result) ) && $total_rows) {
                    $page = $page - $this->pagination_per_page;

                    $result = $this->netpals_model->get_netpal_suggestion($WHERE, $page, $this->pagination_per_page, $s_order_by);
                }
                ## end seacrh conditions : filter ############
                //pr($result);
                #Jquery Pagination Starts
                $this->load->library('jquery_pagination');
                $config['base_url'] = base_url() . "logged/my_net_pals/ajax_pagination";
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

                $config['div'] = '#net_pals_search_ajax_table'; /* Here #content is the CSS selector for target DIV */
                $config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
                $config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */

                $this->jquery_pagination->initialize($config);
                $data['page_links'] = $this->jquery_pagination->create_links();

                // getting   listing...
                $data['search_result_netpals'] = $result;
                $data['no_of_result'] = $total_rows;
                $data['current_page'] = $page;
                $data['total_pages'] = ceil($total_rows / $this->pagination_per_page);

                //echo $data['total_pages'].' ==total_pages==== '.$page;
                //echo $data['current_page'].' ==  ';
                $data['post_val'] = ($total_rows > 0 ) ? 'true' : 'false';
                $p = ($page / $this->pagination_per_page);
                $data['current_loaded_page_no'] = $p + 1;
            }
            $data['is_post_'] = $this->session->userdata('is_post_');
            # loading the view-part...
            echo $this->load->view('logged/net_pals/search_invite_net_pals_ajax.phtml', $data, TRUE);
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

//================================================ invite netpals ================================================    
    public function invite_netpals() {
        try {
            $user_id = intval(($this->input->post('frnd_id')));   //acceptor ID



            $info['i_requester_id'] = intval(decrypt($this->session->userdata('user_id')));
            $info['i_accepter_id'] = $user_id;
            $info['s_status'] = 'pending';

            $is_exists = $this->netpals_model->netpal_request_already_sent($info['i_requester_id'], $info['i_accepter_id']);

            if ($is_exists) {
                $_ret_id = 1;
            } else {
                $_ret_id = $this->netpals_model->add_info($info);
            }
            #echo $this->db->last_query();


            if ($_ret_id > 0) {
                $message_id = parent::send_message($info['i_requester_id'], $info['i_accepter_id'], 'net_pal_request', '', $this->input->post('contact_message'));

                ## check if opted for this notification or not ##
                $notificaion_opt = $this->user_alert_model->check_option_user_id($info['i_accepter_id']);



                ## insert noifications ####
                if ($notificaion_opt['e_net_pal_request_received'] == 'Y') {
                    $notification_arr['i_requester_id'] = decrypt($this->session->userdata('user_id'));
                    $notification_arr['i_accepter_id'] = $info['i_accepter_id'];
                    $notification_arr['s_type'] = 'netpal';
                    $notification_arr['dt_created_on'] = get_db_datetime();


                    $ret = $this->user_notifications_model->insert($notification_arr);
                }
                ### end  ###
			$email_opt = $this->user_alert_model->check_option_email_user_id($info['i_accepter_id']);
						if($email_opt['e_net_pal_request_received'] == 'Y'){
						
						$mail_arr['i_requester_id'] =get_username_by_id( decrypt($this->session->userdata('user_id')));
						$mail_arr['i_accepter_id'] =  get_username_by_id($info['i_accepter_id']);
						$mail_arr['s_type'] = 'netpal';
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

					$this->email->subject($mail_arr["i_requester_id"].' Wants to be your Netpal!!');
					$this->email->message("$body");

					$this->email->send();
					}
                echo json_encode(array('success' => TRUE, 'msg' => 'NetPal request sent successfully.', 'html_txt' => "Reinvite As NetPal", 'u_id' => $user_id));
            } else {
                echo json_encode(array('success' => FALSE, 'msg' => 'Error!', 'html_txt' => ''));
            }
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    //============================================= remove request ======================================   
    public function remove_netpal_request() {
        try {
            $removed_id = intval($this->input->post('frnd_id'));   //acceptor ID
            $removed_by_id = intval(decrypt($this->session->userdata('user_id')));


            $info['i_requester_id'] = $removed_by_id;
            $info['i_accepter_id'] = $removed_id;


            $info['s_status'] = 'accepted';
            $_ret_id = $this->netpals_model->remove_netpal_request_sent($info);
            #echo $this->db->last_query();
            #echo "affected row : ".$_ret_id ;

            if ($_ret_id > 0) {
                $message_id = parent::send_message($removed_by_id, $removed_id, 'net_pal_deleted');
                /* ## check if opted for this notification or not ##
                  $notificaion_opt = $this->user_alert_model->check_option_user_id($removed_id);

                  ## insert noifications ####
                  if($notificaion_opt['e_netpal_request_declined'] == 'Y'){
                  $notification_arr['i_requester_id'] = decrypt($this->session->userdata('user_id'));
                  $notification_arr['i_accepter_id'] =  $removed_id;
                  $notification_arr['s_type'] = 'net_pal_deleted';
                  $notification_arr['dt_created_on'] = get_db_datetime();

                  $ret = $this->user_notifications_model->insert($notification_arr);
                  }
                  ### end  ### */


                $total_where = " WHERE 
									  1
									 
									  AND u.i_status=1 
									  
									  AND ((c.i_requester_id = " . $removed_by_id . " AND u.id=c.i_accepter_id) 
										  OR 
									  (c.i_accepter_id=" . $removed_by_id . " AND u.id=c.i_requester_id))
									  AND c.s_status='accepted' " . $add_where . " GROUP BY u.id ";

                $total_rows = $this->netpals_model->gettotal_online_netpals($total_where);

                if ($total_rows > 0) {
                    echo json_encode(array('success' => TRUE, 'msg' => 'NetPal request successfully removed.', 'html_txt' => "", 'u_id' => $removed_id, 'has_records' => 'y'));
                } else {
                    echo json_encode(array('success' => TRUE, 'msg' => 'NetPal request successfully removed.', 'html_txt' => '<div class="shade_box_blue" style="padding-top:5px;"><div  class="shade_norecords" style="width:285px;"><p class="blue_bold12">No NetPals.</p></div></div>', 'u_id' => $removed_id, 'has_records' => 'n'));
                }
            } else {
                echo json_encode(array('success' => FALSE, 'msg' => 'Error!', 'html_txt' => ''));
            }
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

//============================================== NET PALS REQUEST ================================================== 
    public function net_pals_request() {
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
            'js/frontend/logged/tweets/tweet_utilities.js',
            'js/frontend/logged/my_net_pals.js'
        ));

        parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
            'css/dd.css'));

        $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        $arr_profile_info = $this->users_model->fetch_this($i_profile_id);
        #pr($arr_profile_info);
        ### showing Prayer Partner request sent ###

        ob_start();
        $this->request_sent_ajax_pagination();
        $data['content1'] = ob_get_contents();
        ob_end_clean();

        #pr($friend_requset_sent_);
        ### showing Prayer Partner request sent ###
        ob_start();
        $this->request_recieved_ajax_pagination();
        $data['content2'] = ob_get_contents();
        ob_end_clean();

        ### showing Prayer Partner requset recived ##
        ### end showing Friend requset recived ##
        #pr($friend_requset_recieved);
        # view file...
        $VIEW = "logged/net_pals/net_pal_request.phtml";
        parent::_render($data, $VIEW);
    }

    public function request_sent_ajax_pagination($page = 0) {
        try {
            $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
            ### showing Friend request sent ###
            $WHERE = " WHERE 
                            1
                            AND i_deleted_by = 1
                            AND n.s_status = 'pending' 
                            AND u.i_status=1 
                            AND
                            ((n.i_requester_id = '" . $i_profile_id . "' AND u.id=n.i_accepter_id ) ) GROUP BY u.id";
            /* AND u.i_country_id=cn.id */
            $ORDER_BY = "n.id DESC";


            $result = $this->netpals_model->fetch_multi($WHERE, intval($page), $this->pagination_per_page, $ORDER_BY);
            #echo $resultCount = count($result);
            //echo $this->db->last_query(); 
            #($result);
            $total_rows = $this->netpals_model->gettotal_info($WHERE);

            if ((!is_array($result) || !count($result) ) && $total_rows) {
                $page = $page - $this->pagination_per_page;

                $result = $this->netpals_model->fetch_multi($WHERE, intval($page), $this->pagination_per_page, $ORDER_BY);
            }
            ## end seacrh conditions : filter ############
            #pr($result,1);
            #Jquery Pagination Starts
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url() . "logged/my_net_pals/request_sent_ajax_pagination";
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

            $config['div'] = '#netpal_request_sent'; /* Here #content is the CSS selector for target DIV */
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
            echo $this->load->view('logged/net_pals/net_pal_request_sent_ajax.phtml', $data, TRUE);
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
                        AND n.s_status = 'pending' 
                        AND u.i_status=1 
                        AND
                        ((n.i_accepter_id = '" . $i_profile_id . "' AND u.id=n.i_requester_id ) ) GROUP BY u.id";
            /* AND u.i_country_id=cn.id */
            $ORDER_BY = "n.id DESC";

            $result = $this->netpals_model->fetch_multi($WHERE, intval($page), $this->pagination_per_page, $ORDER_BY);
            $resultCount = count($result);
            #echo $this->db->last_query(); 
            #pr($result);
            $total_rows = $this->netpals_model->gettotal_info($WHERE);

            if ((!is_array($result) || !count($result) ) && $total_rows) {
                $page = $page - $this->pagination_per_page;

                $result = $this->netpals_model->fetch_multi($WHERE, intval($page), $this->pagination_per_page, $ORDER_BY);
            }
            ## end seacrh conditions : filter ############
            #pr($result,1);
            #Jquery Pagination Starts
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url() . "logged/my_net_pals/request_recieved_ajax_pagination";
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

            $config['div'] = '#netpal_request_receive'; /* Here #content is the CSS selector for target DIV */
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
            echo $this->load->view('logged/net_pals/net_pal_request_receive_ajax.phtml', $data, TRUE);
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    //============================================ END OF NET PALS REQUEST ===============================================
    //======================================== cancel net pal request =====================================
    public function cancel_net_pal_request() {
        try {
            $user_id = intval($this->input->post('frnd_id'));   //acceptor ID


            $info['i_requester_id'] = intval(decrypt($this->session->userdata('user_id')));
            $info['i_accepter_id'] = $user_id;
            //$info['s_status']    =    'pending' ; 
            $_ret_id = $this->netpals_model->cancel_netpal_request_sent($info);
            //echo $this->db->last_query();
            //$_ret_id = 1 ;    

            if ($_ret_id > 0) {

                $total_sent = $this->netpals_model->total_pending_netpal_sent($info['i_requester_id']);
                //$total_sent=0;
                if ($total_sent > 0) {
                    echo json_encode(array('success' => TRUE, 'msg' => 'NetPals request cancelled successfully.', 'html_txt' => "", 'u_id' => $user_id, 'last_record' => 'N'));
                } else {
                    echo json_encode(array('success' => TRUE, 'msg' => 'NetPals request cancelled successfully.', 'html_txt' => '<div class="shade_box_blue" style="padding-top:5px;"><div  class="shade_norecords" style="width:320px;><p class="blue_bold12">No NetPals Request Sent.</p></div></div>', 'u_id' => $user_id, 'last_record' => 'Y'));
                }

                /* echo json_encode( array('success'=>TRUE, 'msg'=>'Prayer partner request cancelled successfully.' , 'html_txt'=>"" , 'u_id' => $user_id) ); */
            } else {
                echo json_encode(array('success' => FALSE, 'msg' => 'Error!', 'html_txt' => ''));
            }
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function confirm_invitation_opt() {
        try {
            $this->load->model('users_model');
            if ($this->input->post('type') == 'accept') {

                $i_accepter_id = intval(decrypt($this->session->userdata('user_id')));
                $i_requester_id = intval($this->input->post('i_requester_id'));

                /*                 * ************************** */
                $_ret = $this->netpals_model->update_by_requester_accepter(array('s_status' => 'accepted'), $i_requester_id, $i_accepter_id);

                if ($_ret > 0) {

                    //$this->load->model('data_messages_model');
                    //$this->data_messages_model->update_by_id( array('ended'=>'yes'), $message_id );
                    $i_msg_id = $this->input->post('i_message_id');
                    if (intval($i_msg_id) > 0)
                        $this->db->update('messages', array('i_ended' => '1'), array('id' => $i_msg_id, 'i_sender_id' => $i_requester_id));
                    else
                        $this->db->update('messages', array('i_ended' => '1'), array('s_type' => 'net_pal_request', 'i_sender_id' => $i_requester_id, 'i_receiver_id' => $i_accepter_id));

                    parent::send_message($i_accepter_id, $i_requester_id, 'net_pal_accept');


                    ## check if opted for this notification or not ##
                    $notificaion_opt = $this->user_alert_model->check_option_user_id($i_requester_id);

                    ## insert noifications ####
                    if ($notificaion_opt['e_net_pal_request_accepted'] == 'Y') {
                        $notification_arr['i_requester_id'] = $i_accepter_id;
                        $notification_arr['i_accepter_id'] = $i_requester_id;
                        $notification_arr['s_type'] = 'netpal_request_accepted';
                        $notification_arr['dt_created_on'] = get_db_datetime();

                        $ret = $this->user_notifications_model->insert($notification_arr);
                    }
                    ### end  ###


                    echo json_encode(array('success' => TRUE, 'msg' => 'NetPal accepted successfully!'));
                } else {
                    echo json_encode(array('success' => FALSE, 'msg' => 'Error!'));
                }
            } else if ($this->input->post('type') == 'reject') {
                $i_accepter_id = intval(decrypt($this->session->userdata('user_id')));
                $i_requester_id = intval($this->input->post('i_requester_id'));

                //$i_message_id = $this->input->post('i_message_id');
                $_ret = $this->netpals_model->update_by_requester_accepter(array('s_status' => 'rejected'), $i_requester_id, $i_accepter_id);

                if ($_ret > 0) {
                    //$this->load->model('data_messages_model');
                    //$this->data_messages_model->update_by_id( array('ended'=>'yes'), $message_id );
                    parent::send_message($i_accepter_id, $i_requester_id, 'net_pal_rejected');

                    ## check if opted for this notification or not ##
                    $notificaion_opt = $this->user_alert_model->check_option_user_id($i_requester_id);

                    ## insert noifications ####
                    if ($notificaion_opt['e_netpal_request_declined'] == 'Y') {
                        $notification_arr['i_requester_id'] = $i_accepter_id;
                        $notification_arr['i_accepter_id'] = $i_requester_id;
                        $notification_arr['s_type'] = 'netpal_request_decline';
                        $notification_arr['dt_created_on'] = get_db_datetime();

                        $ret = $this->user_notifications_model->insert($notification_arr);
                    }
                    ### end  ###



                    echo json_encode(array('success' => TRUE, 'msg' => 'NetPal declined successfully!'));
                } else {
                    echo json_encode(array('success' => FALSE, 'msg' => 'Error!!!'));
                }
            }
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    //=   
    public function decline_netpal_request() {
        try {
            $user_id = intval($this->input->post('frnd_id'));   //acceptor ID
            //$this->load->model('my_prayer_partner_model');


            $info['i_requester_id'] = $user_id;
            $info['i_accepter_id'] = intval(decrypt($this->session->userdata('user_id')));
            $info['s_status'] = 'pending';
            $_ret_id = $this->netpals_model->decline_netpal_request_recieved($info);
            //echo $this->db->last_query();


            if ($_ret_id > 0) {
                $this->db->update('messages', array('i_ended' => '1'), array('s_type' => 'net_pal_request', 'i_sender_id' => $info['i_requester_id'], 'i_receiver_id' => $info['i_accepter_id']));

                parent::send_message($info['i_accepter_id'], $info['i_requester_id'], 'net_pal_rejected');
                ## check if opted for this notification or not ##
                $notificaion_opt = $this->user_alert_model->check_option_user_id($info['i_requester_id']);

                ## insert noifications ####
                if ($notificaion_opt['e_netpal_request_declined'] == 'Y') {
                    $notification_arr['i_requester_id'] = decrypt($this->session->userdata('user_id'));
                    $notification_arr['i_accepter_id'] = $info['i_requester_id'];
                    $notification_arr['s_type'] = 'netpal_request_decline';
                    $notification_arr['dt_created_on'] = get_db_datetime();

                    $ret = $this->user_notifications_model->insert($notification_arr);
                }
				$email_opt = $this->user_alert_model->check_option_email_user_id($info['i_requester_id']);
						if($email_opt['e_netpal_request_declined'] == 'Y'){
						
						$mail_arr['i_requester_id'] =get_username_by_id( decrypt($this->session->userdata('user_id')));
						$mail_arr['i_accepter_id'] =  get_username_by_id($info['i_requester_id']);
						$mail_arr['s_type'] = 'netpal_request_decline';
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

					$this->email->subject($mail_arr["i_requester_id"].' has refused your Netpal request');
					$this->email->message("$body");

					$this->email->send();
					}
                ### end  ###


                $total_sent = $this->netpals_model->total_pending_netpal_received($info['i_accepter_id']);


                //$total_sent=0;
                if ($total_sent > 0) {
                    echo json_encode(array('success' => TRUE, 'msg' => 'NetPal request declined successfully.', 'html_txt' => "", 'u_id' => $user_id, 'last_record' => 'N'));
                } else {
                    echo json_encode(array('success' => TRUE, 'msg' => 'NetPal request declined successfully.', 'html_txt' => '<div class="shade_box_blue" style="padding-top:5px;"><div  class="shade_norecords" style="width:320px;"><p class="blue_bold12">No NetPals Request Recieved.</p></div></div>', 'u_id' => $user_id, 'last_record' => 'Y'));
                }


                //echo json_encode( array('success'=>TRUE, 'msg'=>'NetPal request refused successfully.' , 'html_txt'=>"" , 'u_id' => $user_id) );
            } else {
                echo json_encode(array('success' => FALSE, 'msg' => 'Error!', 'html_txt' => 'Sorry. Due to some error, request can not be removed.'));
            }
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    //========================================== requset confirm =========================================   
    public function confirm_invitation() {

        try {
            $this->load->model('users_model');

            $i_accepter_id = intval(decrypt($this->session->userdata('user_id')));
            $i_requester_id = intval($this->input->post('i_requester_id'));

            /*             * ************************** */

            //---------------------- if already netpal ----------------------
            $arr_already_netpal = $this->netpals_model->if_already_netpal($i_accepter_id, $i_requester_id);
            //echo count($arr_already_netpal);
            if (count($arr_already_netpal) > 0) {
                $already_added_netpal = 'true';
            } else {
                $already_added_netpal = 'false';
            }


            if ($already_added_netpal == 'true') {
                $netpal_accept_msg = "Already in NetPal";
                $_ret = 1;
            } else {
                $_ret = $this->netpals_model->update_by_requester_accepter(array('s_status' => 'accepted'), $i_requester_id, $i_accepter_id); #echo $_ret;

                $netpal_accept_msg = "NetPal request accepted successfully.";

                $this->db->update('messages', array('i_ended' => '1'), array('s_type' => 'net_pal_request', 'i_sender_id' => $i_requester_id, 'i_receiver_id' => $i_accepter_id));
            }





            //$_ret = $this->netpals_model->update_by_requester_accepter( array('s_status'=>'accepted'), $i_requester_id, $i_accepter_id ); #echo $_ret;

            if ($_ret > 0) {

                $i_record_id = $this->input->post('i_record_id');
                parent::send_message($i_accepter_id, $i_requester_id, 'net_pal_request');

                ## check if opted for this notification or not ##
                $notificaion_opt = $this->user_alert_model->check_option_user_id($i_requester_id);

                ## insert noifications ####
                if ($notificaion_opt['e_net_pal_request_accepted'] == 'Y') {
                    $notification_arr['i_requester_id'] = $i_accepter_id;
                    $notification_arr['i_accepter_id'] = $i_requester_id;
                    $notification_arr['s_type'] = 'netpal_request_accepted';
                    $notification_arr['dt_created_on'] = get_db_datetime();

                    $ret = $this->user_notifications_model->insert($notification_arr);
                }
                ### end  ###
				$email_opt = $this->user_alert_model->check_option_email_user_id($i_requester_id);
						if($email_opt['e_net_pal_request_accepted'] == 'Y'){
						
						$mail_arr['i_requester_id'] =get_username_by_id( $i_accepter_id);
						$mail_arr['i_accepter_id'] =  get_username_by_id($i_requester_id);
						$mail_arr['s_type'] = 'netpal_request_accepted';
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

					$this->email->subject($mail_arr["i_requester_id"].' is now your Netpal!!');
					$this->email->message("$body");

					$this->email->send();
					}

                /*                 * ************************** */
                $total_sent = $this->netpals_model->total_pending_netpal_received($i_accepter_id);
                //$total_sent=0;
                if ($total_sent > 0) {
                    echo json_encode(array('success' => TRUE, 'msg' => $netpal_accept_msg, 'html_txt' => "", 'u_id' => $i_requester_id, 'last_record' => 'N'));
                } else {
                    echo json_encode(array('success' => TRUE, 'msg' => $netpal_accept_msg, 'html_txt' => '<div class="shade_box_blue" style="padding-top:5px; "><div  class="shade_norecords" style="width:320px;"><p class="blue_bold12">No NetPals Request received.</p></div></div>', 'u_id' => $i_requester_id, 'last_record' => 'Y'));
                }


                #echo json_encode( array('success'=>TRUE, 'msg'=>'Net Pal request accepted successfully!','u_id'=> $i_requester_id) );
            } else {
                echo json_encode(array('success' => FALSE, 'msg' => 'Error!', 'u_id' => ''));
            }
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    //======================================== enlist as net pal =========================================
    function enlist_net_pal() {
        $self_id = intval(decrypt($this->session->userdata('user_id')));

        //$this->db->update()
        $_ret = $this->db->update('users', array('e_want_net_pal' => 'Y'), array('id' => $self_id));
        if ($_ret > 0) {
            echo json_encode(array('success' => true, 'msg' => 'You have successfully opted for NetPal.'));
        } else {
            echo json_encode(array('success' => false, 'msg' => 'Sorry! Some error occure. Please try again.'));
        }
    }

    ## OPT OUT FROM NETPAL ZONE

    function opt_out_net_pal() {
        $self_id = intval(decrypt($this->session->userdata('user_id')));

        //$this->db->update()
        $_ret = $this->db->update('users', array('e_want_net_pal' => 'N'), array('id' => $self_id));
        if ($_ret > 0) {
            echo json_encode(array('success' => true, 'msg' => 'You have successfully opted out from NetPal.'));
        } else {
            echo json_encode(array('success' => false, 'msg' => 'Sorry! Some error occure. Please try again.'));
        }
    }

}

// end of controller...

