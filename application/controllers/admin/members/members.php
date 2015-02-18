<?php

/* * *******
 * Author: 
 * Purpose:
 *  Controller For "advertisements"
 * 
 * @package 
 * @subpackage 
 * 
 * @link InfController.php 
 * @link Base_Controller.php
 * @link model/admin_groups_model.php
 * @link views/##
 */

class Members extends Admin_base_Controller {

    private $pagination_per_page = 25;

    // constructor definition...
    function __construct() {
        try {
            parent::__construct();
            parent::_check_admin_login();
            # configuring paths...
            # loading reqired model & helpers...
            // $this->load->helper('###');
            $this->load->model("users_model");
            $this->load->model("education_model");
            $this->load->helper('common_option_helper.php');
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    // "index" function definition...
    public function index($page_no = 0) {
        //echo "in members.php : page_no = ".$page_no;
        try {
            # adjusting header & footer sections [Start]...
            $data = $this->data;
            parent::_set_title("::: COGTIME Xtian network :::");
            parent::_set_meta_desc("::: COGTIME Xtian network :::");
            parent::_set_meta_keywords("::: COGTIME Xtian network :::");
            parent::_add_js_arr(array('js/lightbox.js',
                'js/ModalDialog.js',
                'js/jquery.dd.js',
                'js/backend/members/delete_user.js'
            ));

            parent::_add_css_arr(array());
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 2;
            $data['submenu'] = 1;


            // fetching data
            $WHERE_COND = " WHERE 1 AND i_isdeleted=1";
            $this->session->set_userdata('search_condition', $WHERE_COND);
            $page = 0;
            $order_by = " `id` DESC ";


            //---------------------- for pagination back ---------------------
            if ($page_no != 0)
                $page = ($page_no - 1) * 2;
            //---------------------- end pagination back ---------------------

            ob_start();
            $this->ajax_pagination($page);
            $data['result_content'] = ob_get_contents(); //pr($data['result_content'],1);
            ob_end_clean();

            #pr($data,1);
            # rendering the view file...
            $VIEW_FILE = "admin/members/members.phtml";
            parent::_render($data, $VIEW_FILE);
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function get_member_list($page) {

        ob_start();
        $this->ajax_pagination($page);
        $data['result_content'] = ob_get_contents();
        ob_end_clean();
        //echo json_encode( array('success'=>1, 'msg'=>$success_msg) );

        $VIEW_FILE = "admin/members/members.phtml";
        parent::_render($data, $VIEW_FILE);
    }

    # function to load ajax-pagination [AJAX CALL]...

    public function ajax_pagination($page = 0) {
        try {
            ## seacrh conditions : filter ############

            if (isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y') :
                $WHERE_COND = " WHERE 1 and i_isdeleted = 1 ";

                $s_email = get_formatted_string(trim($this->input->post('txt_email')));
                $WHERE_COND .= ($s_email == '') ? '' : " AND (s_email = '" . $s_email . "')";

                $s_name = get_formatted_string(trim($this->input->post('txt_name')));
                $WHERE_COND .= ($s_name == '') ? '' : " AND (s_first_name LIKE '%" . $s_name . "%' OR s_last_name LIKE '%" . $s_name . "%')";

                $s_country = get_formatted_string(trim($this->input->post('txt_country')));
                $WHERE_COND .= ($s_country == '') ? '' : " AND (s_country = '" . $s_country . "')";

                $s_state = get_formatted_string(trim($this->input->post('txt_state')));
                $WHERE_COND .= ($s_state == '') ? '' : " AND (s_state = '" . $s_state . "')";

                $s_city = get_formatted_string(trim($this->input->post('txt_city')));
                $WHERE_COND .= ($s_city == '') ? '' : " AND (s_city = '" . $s_city . "')";

                $this->session->set_userdata('search_condition', $WHERE_COND);


            endif;

            $s_where = $this->session->userdata('search_condition');
            $order_by = " `id` DESC ";
            $result = $this->users_model->user_list($s_where, $page, $this->pagination_per_page, $order_by);
            $resultCount = count($result);
            // echo $this->db->last_query(); 
            #pr($result,1);
            $total_rows = $this->users_model->user_list_count($s_where);

            if ((!is_array($result) || !count($result) ) && $total_rows) {
                $page = $page - $this->pagination_per_page;

                $result = $this->users_model->user_list($s_where, $page, $this->pagination_per_page, $order_by);
            }
            ## end seacrh conditions : filter ############
            //pr($result,1);
            #Jquery Pagination Starts
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url() . "admin/members/members/ajax_pagination";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->pagination_per_page;
            $config['uri_segment'] = 5;
            $config['num_links'] = 9;
            $config['page_query_string'] = false;
            $config['prev_link'] = 'PREV';
            $config['next_link'] = 'NEXT';

            /* $config['cur_tag_open'] = '<li> <span><a href="javascript:void(0)" class="select">';
              $config['cur_tag_close'] = '</a></span></li>';

              $config['next_tag_open'] = '<li><a href="javascript:void(0)">';
              $config['next_tag_close'] = '</a></li>';

              $config['prev_tag_open'] = '<li><a href="javascript:void(0)">';
              $config['prev_tag_close'] = '</a></li>'; --commented@12.02.13 */

            $config['cur_tag_open'] = '<li>';
            $config['cur_tag_close'] = '</li>';

            $config['next_tag_open'] = '<li>';
            $config['next_tag_close'] = '</li>';

            $config['prev_tag_open'] = '<li>';
            $config['prev_tag_close'] = '</li>';

            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';

            $config['div'] = '#table_content'; /* Here #content is the CSS selector for target DIV */
            $config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
            $config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */

            $this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();

            // getting   listing...
            $data['info_arr'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $page;

            $data['pagination_per_page'] = $this->pagination_per_page;

            # loading the view-part...
            echo $this->load->view('admin/members/admin_members_ajax.phtml', $data, TRUE);
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

// ================================== delete member ==============================================
    public function delete_member($id, $user_type) {
        $info['i_isdeleted'] = 2;
        $_ret = $this->users_model->edit_info($info, $id);
        $re_page = admin_base_url() . "members/members.html";
        header("location:" . $re_page);
    }

// ================================= change status ======================================
    public function change_status($status, $id) {

        $info['i_status'] = $status;

        $_ret = $this->users_model->edit_info($info, $id);

        if ($_ret) {
            echo json_encode(array('result' => 'success'
            ));
        } else {
            echo json_encode(array('result' => 'failure'
            ));
        }
    }

// ================================= end of change status ======================================     
// ================================= change is minister ======================================
    public function change_status_is_minister($status, $id) {

        $info['is_minister'] = $status;

        $_ret = $this->users_model->edit_info($info, $id);

        if ($_ret) {
            echo json_encode(array('result' => 'success'
            ));
        } else {
            echo json_encode(array('result' => 'failure'
            ));
        }
    }

// ================================= end of change is minister======================================     


    public function reset_password() {

        $data = $this->data;
        $ID = $this->input->post('record_id');
        //$info['s_password'] =   $this->users_model->generatePassword();
        //$RANDOM_PASS = $this->users_model->generatePassword();
        $RANDOM_PASS = $this->input->post('newpwd');
        $NEW_PASSWD = $RANDOM_PASS;

        ## fetchin users detail for mail
        $this->load->model('users_model');
        $user_info = $this->users_model->fetch_this($ID);
        #pr($user_info ,1);

        $USERNAME = $user_info['s_first_name'] . ' ' . $user_info['s_last_name'];
        $EMAIL = $user_info['s_email'];


        $replaceArr = array('email' => $EMAIL,
            'name' => $USERNAME,
            'password' => $NEW_PASSWD);

        ## end
        ## fetch admin details for mail
//        $this->load->model('admins_user_model');
//        $admin_id = intval(decrypt($this->session->userdata('user_id')));
//        $admin_name = trim(($this->session->userdata('username')));
//
//        $replaceArr_admin = array('username' => $USERNAME, 'email' => $EMAIL,
//            'name' => $admin_name,
//            'password' => $NEW_PASSWD);
        $pwd_length = strlen($NEW_PASSWD);
        if ($NEW_PASSWD == '') {
            $SUCCESS_MSG = "Please Enter Password";
            echo json_encode(array('result' => false,
                'u_id' => $ID,
                'msg' => $SUCCESS_MSG, 'redirect' => true));
            exit;
        } else if ($pwd_length < 4) {
            $SUCCESS_MSG = "Password must contain atleast 4 characters";
            echo json_encode(array('result' => false,
                'u_id' => $ID,
                'msg' => $SUCCESS_MSG, 'redirect' => true));
            exit;
        } else {
            if ($this->session->userdata('user_id') != "") {
                //echo $NEW_PASSWD;
                $pass_arr['s_password'] = get_salted_password($NEW_PASSWD);
                $pass_arr['is_temp_password'] = 1;
                $this->users_model->update($pass_arr, $ID);
                //exit;
                ## sending mail to the user and super-admin .. key individual_password_reset_user
                ##user ##
				$this->load->helper('html');
					$this->load->library('email');
					 $email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
				$this->email->initialize($email_setting);
                $this->load->model('mail_contents_model');
                $mail_info = $this->mail_contents_model->get_by_name("individual_password_reset_user");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);

                $body = sprintf3($body, $replaceArr);

                $arr['subject'] = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
                $subject = $arr['subject'];
                $arr['to'] = $EMAIL;
                $arr['bcc'] = 'aradhana.online19@gmail.com';
                $arr['from_email'] = 'no-reply@cogtime.com';
                $from = $arr['from_email'];
                $arr['from_name'] = 'Team Cogtime';
                $arr['message'] = $body;
                $this->email->from( $arr['from_email'], $arr['from_name']);
                #dump($arr);
				$this->email->subject($arr['subject']);
						
				$this->email->to($arr['to']);
				$this->email->bcc($arr['bcc']);
				$this->email->message("$body");
                        //send_mail($arr);
				$this->email->send();
                //send_mail($arr);
//            $to = $EMAIL;
//            $subject = "This is subject";
//            $message = "This is simple text message.";
//            $header = "From:no-reply@cogtime.com \r\n";
//            $retval = mail($to, $subject, $message, $header);
                ## admin mail ##
//            $mail_info = $this->mail_contents_model->get_by_name("individual_password_reset_admin");
//            $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
//
//            $body = sprintf3($body, $replaceArr_admin);
//
//            $arr_admin['subject'] = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
//            $arr_admin['to'] = $MAIL_ID;
//            $arr_admin['bcc'] = 'aradhana.online19@gmail.com';
//            $arr_admin['from_email'] = 'no-reply@cogtime.com';
//            $arr_admin['from_name'] = 'Team Cogtime';
//            $arr_admin['message'] = $body;
//            //dump($arr_admin); exit;
//            send_mail($arr_admin);
                $SUCCESS_MSG = "Password changed Successfully!";
                echo json_encode(array('result' => 'success',
                    'u_id' => $ID,
                    'msg' => $SUCCESS_MSG, 'redirect' => false));
            } else {

                $SUCCESS_MSG = "An error has occured! please try again. ";
                echo json_encode(array('result' => false,
                    'u_id' => $ID,
                    'msg' => $SUCCESS_MSG, 'redirect' => true));
                exit;
            }
        }


//        $SUCCESS_MSG = "Password changed Successfully!";
//        echo json_encode(array('result' => 'success',
//            'u_id' => $ID,
//            'msg' => $SUCCESS_MSG, 'redirect' => false));
    }

}

// end of controller