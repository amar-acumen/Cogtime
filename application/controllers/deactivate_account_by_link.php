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

class deactivate_account_by_link extends Base_controller {

    public function __construct() {
        try {
            parent::__construct();
            //parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...
            $this->load->model('users_model');
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function index($user_id = '') {
        try {

            $posted = array();
            $this->data["posted"] = $posted; /* don't change */
            $data = $this->data;
            $this->data["MAIN_MENU_SELECTED"] = 1;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');


            parent::_add_js_arr(array(//'js/ddsmoothmenu.js',
//                'js/switch.js', 'js/animate-collapse.js',
//                'js/lightbox.js', 'js/jquery.dd.js', 'js/jquery-ui-1.8.2.custom.min.js',
                'js/production/tweet_utilities.js'
//                'js/stepcarousel.js',
//                    //'js/frontend/logged/notifcations/user_alerts.js'
            ));
//
//            parent::_add_css_arr(array(/*'css/jquery-ui-1.8.2.custom.css',
//                'css/dd.css'*/));

            //echo $user_id = $_REQUEST['ud'];
            //$pwd = str_rot13($_REQUEST['cvx']);
            //$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
            $i_profile_id = $user_id;
            // $user_data = get_user_data_by_id($user_id);
            //pr($user_data);
            $i_ret = update_account_status($user_id);
            $i_ret_stat = update_user_i_status_inactive($user_id);
            if ($i_ret && $i_ret_stat) {
                $data['success_msg'] = "Your account has been successfully deactivated";
            }
            $data['arr_alert_arr'] = $this->users_model->fetch_this($i_profile_id);
            $array_items = array('loggedin' => FALSE, 'user_id' => '');
            $this->session->unset_userdata($array_items);
            //pr($data['arr_alert_arr']);
            # view file...
            $VIEW = "deactivate_account_by_link.phtml";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

//    public function deactivate_user_account() {
//        try {
//            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by non logged in user
//
//            $i_logged_id = intval(decrypt($this->session->userdata('user_id')));
//
//            $arr_messages = array();
//
//            if (trim($this->input->post('txt_pwd')) == '') {
//                $arr_messages['pwd'] = '* Required Field.';
//            }
//
////            if (trim($this->input->post('txt_new_pwd')) == '') {
////                $arr_messages['new_pwd'] = '* Required Field.';
////            }
////
////            if (trim($this->input->post('txt_new_cpwd')) == '') {
////                $arr_messages['new_cpwd'] = '* Required Field.';
////            }
////
////            if (trim($this->input->post('txt_new_pwd')) != trim($this->input->post('txt_new_cpwd'))) {
////                $arr_messages['new_cpwd'] = '* Password confirmation failed.';
////            }
////            if (trim($this->input->post('txt_pwd')) != '') {
////
////                $IS_CORRECT_PWD = $this->users_model->match_password($i_logged_id, trim($this->input->post('txt_pwd')));
////
////                if (!$IS_CORRECT_PWD) {
////                    $arr_messages['pwd'] = '* Incorrect Password.';
////                }
////            }
//            if (count($arr_messages) == 0) {
//                $info = array();
//                $i_user_id = intval(decrypt($this->session->userdata('user_id')));
//                //$info['s_password'] = trim($this->input->post('txt_new_cpwd'));
//                $pwd = trim($this->input->post('txt_new_cpwd'));
//                $pwd = str_rot13($pwd);
//                $user_data = get_user_data_by_id($i_user_id);
//                //pr($user_data);
//                $user_f_name = $user_data[0]['s_first_name'];
//                $user_l_name = $user_data[0] ['s_last_name'];
//                $USERNAME = $user_f_name . ' ' . $user_l_name;
//                $EMAIL = $user_data[0]['s_email'];
//                
//                exit();
//                // $i_ret = $this->users_model->change_password($info, $i_user_id);
////                 $i_ret = update_account_status($i_user_id);
////                 $i_ret_stat = update_user_i_status_inactive($i_user_id);
//                $replaceArr = array(
//                    'name' => $USERNAME,
//                    'link' => '');
//                $this->load->model('mail_contents_model');
//                $mail_info = $this->mail_contents_model->get_by_name("account_deactivate_mail_content");
//                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
//                $body = sprintf3($body, $replaceArr);
//                $arr['subject'] = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
//                $subject = $arr['subject'];
//                $arr['to'] = $EMAIL;
//                $arr['bcc'] = 'aradhana.online19@gmail.com';
//                $arr['from_email'] = 'no-reply@cogtime.com';
//                $from = $arr['from_email'];
//                $arr['from_name'] = 'Team Cogtime';
//                $arr['message'] = $body;
//                //dump($arr);
//                send_mail($arr);
//                if ($i_ret && $i_ret_stat) {
//                    //  echo json_encode(array('success' => true, 'arr_messages' => $arr_messages, 'msg' => 'Your account has been deactivated'));
//                }
//            } else {
//                echo json_encode(array('success' => false, 'arr_messages' => $arr_messages, 'msg' => 'Error!'));
//            }
//        } catch (Exception $err_obj) {
//            
//        }
//    }
}

// end of controller...

