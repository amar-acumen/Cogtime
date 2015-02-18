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

class activate_account extends Base_controller {

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

    public function index() {
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
                'js/frontend/logged/tweets/tweet_utilities.js',
                'js/stepcarousel.js',
                    //'js/frontend/logged/notifcations/user_alerts.js'
            ));

            parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
                'css/dd.css'));

            $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
            $data['arr_alert_arr'] = $this->users_model->fetch_this($i_profile_id);

            //pr($data['arr_alert_arr']);
            # view file...
            $VIEW = "activate_account.phtml";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

    public function activate_user_account() {
        try {
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by non logged in user

            $i_logged_id = intval(decrypt($this->session->userdata('user_id')));

            $arr_messages = array();

            if (trim($this->input->post('txt_pwd')) == '') {
                $arr_messages['pwd'] = '* Required Field.';
            }

            if (trim($this->input->post('txt_pwd')) != '') {

                $IS_CORRECT_PWD = $this->users_model->match_password($i_logged_id, trim($this->input->post('txt_pwd')));

                if (!$IS_CORRECT_PWD) {
                    $arr_messages['pwd'] = '* Incorrect Password.';
                }
            }
            if (count($arr_messages) == 0) {
                $info = array();
                $i_user_id = intval(decrypt($this->session->userdata('user_id')));
                $info['s_password'] = trim($this->input->post('txt_new_cpwd'));
                // $i_ret = $this->users_model->change_password($info, $i_user_id);
                $i_ret = update_account_status_active($i_user_id);
                $i_ret_stat = update_user_i_status_active($i_user_id);
                if ($i_ret && $i_ret_stat) {
                    echo json_encode(array('success' => true, 'arr_messages' => $arr_messages, 'msg' => 'Your account has been activated'));
                }
            } else {
                echo json_encode(array('success' => false, 'arr_messages' => $arr_messages, 'msg' => 'Error!'));
            }
        } catch (Exception $err_obj) {
            
        }
    }

}

// end of controller...

