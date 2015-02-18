<?php

/* * *******
 * Author: 
 * Date  : 
 * Modified By: 
 * Modified Date:
 * 
 * Purpose:
 *  Controller For ## Management
 * 
 * @package 
 * @subpackage 
 * 
 * @link InfController.php 
 * @link Base_Controller.php
 * @link model/##.php
 * @link views/##
 */

class Prayer_partner_parameters extends Admin_base_Controller {

    private $upload_path;

    function __construct() {
        try {
            parent::__construct();
            parent::_check_admin_login();
            $this->upload_path = BASEPATH . '../uploads/prayer_partner_t_c_files/';
            # loading reqired model & helpers...
            $this->load->model('admins_user_model');
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    function index() {
        try {
            $data = $this->data;
            $data['top_menu_selected'] = 1;
            $data['submenu'] = 17;

            # adjusting header & footer sections [Start]...
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc("::: COGTIME Xtian network :::");
            parent::_set_meta_keywords("::: COGTIME Xtian network :::");

            parent::_add_js_arr(array());
            parent::_add_css_arr(array('css/admin.css'));


            # rendering the view file...
            $result_arr = array();
            $result_arr = $this->admins_user_model->get_prayer_partner_q_params(1);
            //pr($result_arr);
            $data['result_arr'] = $result_arr[0];
            $view_file = "admin/site_settings/prayer_partner_parameters.phtml";
            parent::_render($data, $view_file);
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    function update_prayer_partner_qualification_parameters() {
        $min_months = $this->input->post('min_months');
        $min_friends = $this->input->post('min_friends');
        $min_pryer_grps = $this->input->post('min_prayer_grps');
        $min_rings = $this->input->post('min_rings');
        $min_pr_cmtmnts = $this->input->post('min_pr_cmtmnts');
        $t_c_file = $this->input->post('c_file_name');

        //pr($_FILES);
        $file_name_arr = $_FILES['min_intros']['name'];
        if (!empty($file_name_arr)) {
            $file_name_arr = explode(".", $file_name_arr);
            // pr($file_name_arr);
            $file_name = $file_name_arr[0] . '_' . time();
            $file_ext = $file_name_arr[1];
            $file_name = $file_name . '.' . $file_ext;

            $this->upload_image = $this->upload_path . $file_name;
            @move_uploaded_file($_FILES['min_intros']['tmp_name'], $this->upload_image);
        }
        if (empty($file_name)) {
            $file_name = $t_c_file;
        }
        $arr = array(
            'min_months' => $min_months,
            'min_friends' => $min_friends,
            'min_prayer_grps' => $min_pryer_grps,
            'min_rings' => $min_rings,
            'min_pr_comttmnts' => $min_pr_cmtmnts,
            't_c_file_name' => $file_name
        );
        $update = $this->admins_user_model->update_prayer_partner_q_params($arr, 1);
        $location = admin_base_url() . 'site_settings/prayer_partner_parameters?update=yes';
        if (!empty($update)) {
            header("Location: $location");
        }
        //echo time();
    }

}

// end of controller...