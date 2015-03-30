<?php

function get_salt() {
    $ci = get_instance();
    return $ci->config->item('salt');
}

function get_salted_password($password) {
    $ci = get_instance();
    $salt = $ci->config->item('salt');

    return sha1($salt . $password);
}

function get_db_dateformat($date, $seperation = '/') {

    $date_arr = array();
    $date_arr = explode($seperation, $date);
    //pr($date_arr);
    $new_date = date('Y-m-d', mktime(0, 0, 0, $date_arr[1], $date_arr[0], $date_arr[2]));
    return $new_date;
}

function get_db_year($date) {
    $date_arr = array();
    $date_arr = explode(' ', $date);
    $date_split = $date_arr[0];
    $date_year = array();
    $date_year = explode('-', $date_split);
    $year = $date_year[0];
    return $year;
}

function get_db_month($date) {
    $date_arr = array();
    $date_arr = explode(' ', $date);
    $date_split = $date_arr[0];
    $date_month = array();
    $date_month = explode('-', $date_split);
    $month = $date_month[1];
    return $month;
}

/* function get_country_name_by_id($s_country_id = NULL) {
  try {
  $i_country_id = intval(($s_country_id));
  $o_ci = get_instance();

  $o_ci->db->select("s_country_name");
  $o_ci->db->where('id', $i_country_id);
  $arr_qry = $o_ci->db->get('mst_country');
  $ret_ = $arr_qry->result_array();
  return $ret_[0]["s_country_name"];
  } catch (Exception $err_obj) {
  show_error($err_obj->getMessage());
  }
  } */

function get_country_name_by_id($s_country_id = NULL) {
    try {
        $i_country_id = intval(($s_country_id));
        $o_ci = get_instance();

        $o_ci->db->select("s_country as s_country_name");
        $o_ci->db->where('id', $i_country_id);
        $arr_qry = $o_ci->db->get('country');
        $ret_ = $arr_qry->result_array();
        return $ret_[0]["s_country_name"];
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function get_city_name_by_id($s_city_id = NULL) {
    try {
        $i_country_id = intval(($s_city_id));
        $o_ci = get_instance();

        $o_ci->db->select("s_city as s_city_name");
        $o_ci->db->where('id', $i_country_id);
        $arr_qry = $o_ci->db->get('city');
        $ret_ = $arr_qry->result_array();
        return $ret_[0]["s_city_name"];
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function get_state_name_by_id($s_city_id = NULL) {
    try {
        $i_state_id = intval(($s_city_id));
        $o_ci = get_instance();

        $o_ci->db->select("s_state as s_state_name");
        $o_ci->db->where('id', $i_state_id);
        $arr_qry = $o_ci->db->get('state');
        $ret_ = $arr_qry->result_array();
        return $ret_[0]["s_state_name"];
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function get_city_id_by_name($s_country_name = NULL) {
    try {

        $o_ci = get_instance();

        $o_ci->db->select("id");
        $o_ci->db->like('s_country', $s_country_name, 'after');

        $arr_qry = $o_ci->db->get('country');
        $ret_ = $arr_qry->result_array();
        return $ret_[0]["id"];
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function get_unique_profile_name($username = "") {
    try {
        $ci = get_instance();
        $ci->load->model('users_model');
        $check_info = $ci->users_model->username_already_exists($username);
        if ($check_info > 0) {
            $var = $check_info; //getUniqueCode(1);
            return trim($username . '-' . $var);
        } else
            return $username;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

/////////Encryption and Decryption////////
/* * *
 * Encryption double ways.
 * 
 * @param string $s_var
 * @return string
 */
function encrypt($s_var) {
    try {
        $ret_ = $s_var . "#acu"; ///Hardcodded here for security reasons
        $ret_ = base64_encode(base64_encode($ret_));
        unset($s_var);
        return $ret_;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

/**
 * Decryption double ways.
 * 
 * @param string $s_var
 * @return string
 */
function decrypt($s_var) {
    try {
        $ret_ = base64_decode(base64_decode($s_var));
        $ret_ = str_replace("#acu", "", $ret_);
        unset($s_var);
        return $ret_;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

/////////end Encryption and Decryption////////

/**
 * Displays the pre-formatted array 
 * 
 * @param mix $mix_arr
 * @param int $i_then_exit
 * @return void
 */
function pr($mix_arr = array(), $i_then_exit = 0) {
    try {
        echo '<pre>';
        print_r($mix_arr);
        echo '</pre>';
        unset($mix_arr);
        if ($i_then_exit) {
            exit();
        }
        unset($mix_arr, $i_then_exit);
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

/**
 * Displays the pre-formatted array with array element type
 * 
 * @param mix $mix_arr
 * @param int $i_then_exit
 * @return void
 */
function vr($mix_arr = array(), $i_then_exit = 0) {
    try {
        echo '<pre>';
        var_dump($mix_arr);
        echo '</pre>';
        unset($mix_arr);
        if ($i_then_exit) {
            exit();
        }
        unset($mix_arr, $i_then_exit);
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

/**
 * For geting extension of a file
 * 
 * @param string $s_filename
 * @return string
 */
function getExtension($s_filename = '') {
    try {
        if (empty($s_filename))
            return FALSE;
        $mix_matches = array();
        preg_match('/\.([^\.]*)$/', $s_filename, $mix_matches);
        unset($s_filename);
        return strtolower($mix_matches[0]);
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

/* * **
 * Function to format input string
 *
 * *** */

function get_formatted_string($str) {
    try {
        //return addslashes(htmlspecialchars(trim($str),ENT_QUOTES));
        return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

/* * **
 * Function to reverse of get_formatted_string
 *
 * *** */

function get_unformatted_string($str) {
    try {
        // return stripslashes(trim($str));
        return trim($str);
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function get_unformatted_string_edit($str) {
    try {
        // return stripslashes(trim($str));
        return htmlspecialchars_decode(trim($str), ENT_QUOTES);
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

/*
  This function is used to display currency format
 */

function numeric_price_format($val) {
    $new_val = number_format($val, '2', '.', ',');
    return $new_val;
}

/*
 * Returns NULL value,specially for database dependency check 
 *
 * @value    public
 */

function convert_to_null($value) {
    try {
        $temp = intval($value);
        if ($temp == 0) {
            unset($temp);
            return NULL;
        } else {
            unset($temp);
            return $value;
        }
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

/**
 * Admin Base URL
 *
 * Returns the "admin_base_url" item from your config file
 *
 * @access    public
 * @return    string
 */
if (!function_exists('admin_base_url')) {

    function admin_base_url() {
        try {
            $CI = & get_instance();
            return $CI->config->slash_item('admin_base_url');
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

}

/**
 * Saves the error messages into session.
 * 
 * @param string $s_msg
 * @return void
 */
function set_error_msg($s_msg) {
    try {
        $ret_ = "";
        if (trim($s_msg) != "") {
            $o_ci = &get_instance();
            $ret_ = $o_ci->session->userdata('error_msg');
            $ret_.='<div id="err_msg" class="error_massage">' . $s_msg . '</div>';
            $o_ci->session->set_userdata('error_msg', $ret_);
        }
        unset($s_msg, $ret_);
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

/**
 * Saves the error messages into session.
 * 
 * @param string $s_msg
 * @return void
 */
function set_success_msg($s_msg) {
    try {
        $ret_ = "";
        if (trim($s_msg) != "") {
            $o_ci = &get_instance();
            $ret_ = $o_ci->session->userdata('success_msg');
            $ret_.='<div class="success_massage">' . $s_msg . '</div>';
            $o_ci->session->set_userdata('success_msg', $ret_);
            //echo $o_ci->session->userdata('success_msg');
        }
        unset($s_msg, $ret_);
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

/**
 * Displays the success or error or both messages.
 * And removes the messages from session
 * 
 * @param string $s_msgtype, "error","success","both" 
 * @return void
 */
function show_msg($s_msgtype = "both") {
    try {
        $o_ci = &get_instance();
        switch ($s_msgtype) {
            case "error":
                echo $o_ci->session->userdata('error_msg');
                $o_ci->session->unset_userdata('error_msg');
                break;
            case "success":
                echo $o_ci->session->userdata('success_msg');
                $o_ci->session->unset_userdata('success_msg');
                break;
            default:
                echo $o_ci->session->userdata('success_msg');
                echo $o_ci->session->userdata('error_msg');
                $array_items = array('success_msg' => '', 'error_msg' => '');
                $o_ci->session->unset_userdata($array_items);
                unset($array_items);
                break;
        }
        unset($s_msgtype);
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

######### / project related functions ################3

function showCurrency($price) {

    $price = " &euro;" . number_format($price, 2);

    return $price;
}

function pageSeoInfo($page_key = 'index', $replace_by = array()) {
    $cCI = &get_instance();
    $cCI->load->model('seo_model');
    $seo_info = array();
    $seo_info = $cCI->seo_model->fetch_this($page_key);

    if (empty($replace_array)) {
        $seo_info['s_title'] = str_replace('%%item_name%%', $replace_by['seo_title'], $seo_info['s_title']);
        $seo_info['s_meta_description'] = str_replace($replace_by['s_meta_description'], '%%item_name%%', $seo_info['s_meta_description']);
        $seo_info['s_keyword'] = str_replace($replace_by['s_keyword'], '%%item_name%%', $seo_info['s_keyword']);
    }

    return $seo_info;
}

########  FUNCTION TO GENERATE PHOTO ALBUM URL #####

function get_photo_album_url($album_id) {

    $album_id = intval($album_id);
    $url = base_url() . "photo-albums/" . $album_id . "/organize-photo." . "html";
    return $url;
}

function get_photo_album_detail_url($album_id, $album_name = "") {

    $album_id = intval($album_id);
    if ($album_name == '') {
        $ci = get_instance();
        $ci->load->model('photo_albums_model');
        $info = $ci->photo_albums_model->get_by_id($album_id);
        $album_name = $info['s_name'];
    }
    //$url = base_url()."photo-album-detail/".$album_id."/organize-photo."."html";
    $url = base_url() . "photo-album-detail/" . $album_id . "/" . my_url($album_name) . ".html";
    return $url;
}

function get_photo_detail_url($photo_id, $s_title = "") {

    $photo_id = intval($photo_id);
    if ($s_title == '') {
        $ci = get_instance();
        $ci->load->model('user_photos_model');
        $info = $ci->user_photos_model->get_by_id($photo_id);
        $s_title = $info['s_title'];
    }
    //$url = base_url()."photo-album-detail/".$album_id."/organize-photo."."html";
    $url = base_url() . "photo-detail/" . $photo_id . "/" . my_url($s_title) . ".html";
    return $url;
}

function get_photo_title($photo_id) {
    $ci = get_instance();
    $ci->load->model('user_photos_model');
    $info = $ci->user_photos_model->get_by_id($photo_id);
    return $s_title = $info['s_title'];
}

################################### audio url ########################

function get_audio_album_url($album_id) {

    $album_id = intval($album_id);
    $url = base_url() . "audio-albums/" . $album_id . "/organize-audio." . "html";
    return $url;
}

function get_audio_album_detail_url($album_id, $album_name = "") {

    $album_id = intval($album_id);
    if ($album_name == '') {
        $ci = get_instance();
        $ci->load->model('audio_albums_model');
        $info = $ci->audio_albums_model->get_by_id($album_id);
        $album_name = $info['s_name'];
    }
    //$url = base_url()."photo-album-detail/".$album_id."/organize-photo."."html";
    $url = base_url() . "audio-album-detail/" . $album_id . "/" . my_url($album_name) . ".html";
    return $url;
}

function get_audio_detail_url($photo_id, $s_title = "") {

    $photo_id = intval($photo_id);
    if ($s_title == '') {
        $ci = get_instance();
        $ci->load->model('user_audios_model');
        $info = $ci->user_audios_model->get_by_id($photo_id);
        $s_title = $info['s_title'];
    }
    //$url = base_url()."photo-album-detail/".$album_id."/organize-photo."."html";
    $url = base_url() . "audio-detail/" . $photo_id . "/" . my_url($s_title) . ".html";
    return $url;
}

function get_cms_url($id, $title = "") {
    //echo $title.' =='; exit;
    $id = intval($id);
    $title = explode(' ', $title);
    $new_title = replace_special_char(implode('-', $title));

    $url = base_url() . "cms/" . $id . "/" . $new_title . ".html";
    return $url;
}

function get_cms_view_name($title = "") {
    $title = explode(' ', $title);
    $new_title = implode('-', $title);

    $name = $new_title;
    return $name;
}

function get_events_detail_url($event_id, $event_name = '') {

    $event_id = intval($event_id);
    if ($event_name == '') {
        $ci = get_instance();
        $ci->load->model('events_model');
        $info = $ci->events_model->get_by_id($event_id);
        $event_name = $info['s_title'];
    }
    if (intval(decrypt($ci->session->userdata('user_id'))) != '' && $ci->session->userdata('is_admin') != 1)
        $url = base_url() . "event-detail/" . $event_id . "/" . my_url($event_name) . ".html";
    else
        $url = base_url() . "events/" . $event_id . "/" . my_url($event_name) . ".html";
    return $url;
}

################################################################## 

function getSortingImg($s_current_order_by = 'id', $url = '/logged/my_photo/change_photo_order/', $div_id = '') {
    $ci = get_instance();
    $sess_current_order_by = $ci->session->userdata('current_order_by');
    $sess_current_order = $ci->session->userdata('current_order');
    $url = base_url() . $url;

    //echo $div_id ."<br />";

    $DIV_ID_TXT = (!empty($div_id) ) ? ", '" . $div_id . "'" : "";

    if ($sess_current_order_by == $s_current_order_by) {
        if (strtoupper($sess_current_order) == 'DESC') {
            $order_img = '<a href="javascript:void(0);" 
                    onclick="javascript:changeOrderAjax(\'' . $s_current_order_by . '\',\'ASC\',\'' . $url . '\' ' . $DIV_ID_TXT . ');"><img src="images/down.png" alt="" /></a>';
        } else {
            $order_img = '<a href="javascript:void(0);" onclick="javascript:changeOrderAjax(\'' . $s_current_order_by . '\',\'DESC\',\'' . $url . '\' ' . $DIV_ID_TXT . ');"><img src="images/up.png" alt="" /></a>';
        }
    } else {
        $order_img = '<a href="javascript:void(0);" onclick="javascript:changeOrderAjax(\'' . $s_current_order_by . '\',\'DESC\',\'' . $url . '\' ' . $DIV_ID_TXT . ');"><img src="images/up.png" alt="" /></a>';
    }

    return $order_img;
}

# function to retrieve menu-index for different backoffice modules...

function retrive_admin_menu_index($selected_option, $options_arr) {
    $menu_index = array_search($selected_option, $options_arr);

    return $menu_index;
}

function get_admin_nameby_id($id = '') {
    try {
        $id = intval($id);
        $ci = get_instance();
        $ci->load->model('admins_user_model');
        $user_name = $ci->admins_user_model->get_username_by_id($id);
        return $user_name;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function get_admin_emailby_id($id = '') {
    try {
        $id = intval($id);
        $ci = get_instance();
        $ci->load->model('admins_user_model');
        $email = $ci->admins_user_model->get_email_by_id($id);
        return $email;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function get_profile_url($id, $user_name = "") {

    $id = intval($id);
    if (trim($user_name) == '') {
        $ci = get_instance();
        $ci->load->model('users_model');
        $info = $ci->users_model->fetch_this($id);
        $user_type = $info['i_user_type'];
        $user_name = $info['s_profile_name'];
    }


    $url = base_url() . "public-profile/" . $id . "/" . my_url($user_name) . ".html";
    return $url;
}

function get_profile_url_prayer_partner($id, $user_name = "") {

    $id = intval($id);
    if (trim($user_name) == '') {
        $ci = get_instance();
        $ci->load->model('users_model');
        $info = $ci->users_model->fetch_this($id);
        $user_type = $info['i_user_type'];
        $user_name = $info['s_profile_name'];
    }


    $url = base_url() . "prayer-partner-public-profile/" . $id . "/" . my_url($user_name) . ".html";
    return $url;
}

function get_public_profile_url($id, $user_name = "") {

    $id = intval($id);
    if (trim($user_name) == '') {
        $ci = get_instance();
        $ci->load->model('users_model');
        $info = $ci->users_model->fetch_this($id);
        $user_type = $info['i_user_type'];
        $user_name = $info['s_profile_name'];
    }
    $url = base_url() . "profile/" . $id . "/" . my_url($user_name) . ".html";
    return $url;
}

function get_profile_photo_url($id, $user_name = "") {

    $id = intval($id);
    if (trim($user_name) == '') {
        $ci = get_instance();
        $ci->load->model('users_model');
        $info = $ci->users_model->fetch_this($id);
        $user_name = $info['s_profile_name'];
    }


    $url = base_url() . "profile-photos/" . $id . "/" . my_url($user_name) . ".html";
    return $url;
}

function get_profile_image_of_user($size = "thumb", $db_image_name = "", $image_gender="") {

    switch ($size) {
        case 'thumb':
            if ($image_gender == 'M'):
                $no_img = 'uploads/no-image/noprofileimage-mini.gif';
            elseif ($image_gender == 'F'):
                $no_img = 'uploads/no-image/girl2.jpg';
            else:
                $no_img = 'uploads/no-image/noprofileimage-big.gif';
            endif;
            break;
        case 'main':
            if ($image_gender == 'M'):
                $no_img = 'uploads/no-image/noprofileimage-big.gif';
            elseif ($image_gender == 'F'):
                $no_img = 'uploads/no-image/girl3.png';
            else:
                $no_img = 'uploads/no-image/noprofileimage-big.gif';
            endif;
            //$no_img = 'uploads/no-image/noprofileimage-thumb.gif';
            break;
    }

    $nw_image_name = ($db_image_name == "") ? base_url() . $no_img : base_url() . 'uploads/user_profile_image/' . getThumbName($db_image_name, $size);
    #$url = base_url()."public-profile/".$id."/".my_url($user_name).".html";
    return $nw_image_name;
}

function get_profile_image($id, $size = "thumb", $image_name = "") {

    $id = intval($id);
    if (trim($image_name) == '') {
        $ci = get_instance();
        $ci->load->model('users_model');
        $info = $ci->users_model->fetch_this($id);
        $db_image_name = $info['s_profile_photo'];
        $image_gender = $info['e_gender'];
    } else {
        $db_image_name = $image_name;
    }
    switch ($size) {
        case 'thumb':
            if ($image_gender == 'M'):
                $no_img = 'uploads/no-image/noprofileimage-mini.gif';
            elseif ($image_gender == 'F'):
                $no_img = 'uploads/no-image/girl2.jpg';
            else:
                $no_img = 'uploads/no-image/noprofileimage-big.gif';
            endif;
            break;
        case 'main':
            if ($image_gender == 'M'):
                $no_img = 'uploads/no-image/noprofileimage-big.gif';
            elseif ($image_gender == 'F'):
                $no_img = 'uploads/no-image/girl3.png';
            else:
                $no_img = 'uploads/no-image/noprofileimage-big.gif';
            endif;
            //$no_img = 'uploads/no-image/noprofileimage-thumb.gif';
            break;
    }

    $nw_image_name = ($db_image_name == "") ? base_url() . $no_img : base_url() . 'uploads/user_profile_image/' . getThumbName($db_image_name, $size);
    #$url = base_url()."public-profile/".$id."/".my_url($user_name).".html";
    return $nw_image_name;
}

// generates the album page link for an user
function get_profile_album_url($user_id, $user_name = "") {

    $id = intval($user_id);
    if (trim($user_name) == '') {
        $ci = get_instance();
        $ci->load->model('users_model');
        $info = $ci->users_model->fetch_this($id);
        $user_name = $info['s_name'];
    }


    $url = base_url() . "profile-albums/" . $id . "/" . my_url($user_name) . ".html";
    return $url;
}

function check_user_online_hlpr($user_id, $scope = 'own', $relation = 'no', $ImgOnly = '') {
    $ci = get_instance();
    $ci->db->where('i_user_id', $user_id);
    $arr_qry = $ci->db->get('users_online');
    $ret_ = $arr_qry->row_array();

    $status_arr = array();
    if ($user_id != '') {
        $sql_check_user = "SELECT * from cg_users_status where i_user_id = {$user_id} order by last_seen_date desc LIMIT 0,1";
        $status_arr = $ci->db->query($sql_check_user)->row_array();
    }


    if ($relation == 'no' && $scope == 'own') {
        if (is_array($ret_) && count($ret_)) {
            if ($ret_['s_status'] == 1)
                return '<img src="' . base_url() . 'images/icons/online.png" width="12" height="12" alt="" />';
            else if ($ret_['s_status'] == 2)
                return '<img src="' . base_url() . 'images/icons/invisible.png" alt="" />';
            else if ($ret_['s_status'] == 3)
                return '<img src="' . base_url() . 'images/icons/away.png" alt="" />';
            else if ($ret_['s_status'] == 0 || $ret_['s_status'] == 4)
                return '<img src="' . base_url() . 'images/icons/offline.png" width="12" height="12" alt="" />';
        } else
            return '<img src="' . base_url() . 'images/icons/offline.png" width="12" height="12" alt="" /> ';
    }
    elseif ($ImgOnly == true) {

        //pr($ret_);

        if (is_array($ret_) && count($ret_) && is_array($status_arr) && count($status_arr)) {

            $show_f_online = 'N';
            $show_n_online = 'N';
            $show_p_online = 'N';

            ### checking online status for user grp	
            if ($status_arr['i_isfriend'] == 1) {
                $show_f_online = ($relation['i_isfriend'] == 'true' ) ? 'Y' : 'N';
            }
            if ($status_arr['i_isnetpal'] == 1) {
                $show_n_online = ($relation['i_isnetpal'] == 'true' ) ? 'Y' : 'N';
            }
            if ($status_arr['i_isprayerpartner'] == 1) {
                $show_p_online = ($relation['i_isprayerpartner'] == 'true' ) ? 'Y' : 'N';
            }

            ### checking online status for user grp	
            //echo $show_f_online .',,'.$show_n_online.',,'.$show_p_online ; exit;

            if ($ret_['s_status'] == 1 && ($show_f_online == 'Y' || $show_n_online == 'Y' || $show_p_online == 'Y'))
                return '<img src="' . base_url() . 'images/icons/online.png" width="12" height="12" alt="" />';
            else if ($show_f_online == 'N' || $show_n_online == 'N' || $show_p_online == 'N')
                return '<img src="' . base_url() . 'images/icons/offline.png" width="12" height="12" alt="" />';
        } else
            return '<img src="' . base_url() . 'images/icons/offline.png" width="12" height="12" alt="" />';
    }
    else {//pr($ret_);
        if (is_array($ret_) && count($ret_) && is_array($status_arr) && count($status_arr)) {


            $show_f_online = 'N';
            $show_n_online = 'N';
            $show_p_online = 'N';

            ### checking online status for user grp	
            if ($status_arr['i_isfriend'] == 1) {
                $show_f_online = ($relation['i_isfriend'] == 'true' ) ? 'Y' : 'N';
            }
            if ($status_arr['i_isnetpal'] == 1) {
                $show_n_online = ($relation['i_isnetpal'] == 'true' ) ? 'Y' : 'N';
            }
            if ($status_arr['i_isprayerpartner'] == 1) {
                $show_p_online = ($relation['i_isprayerpartner'] == 'true' ) ? 'Y' : 'N';
            }

            ### checking online status for user grp	
            //echo $show_f_online .',,'.$show_n_online.',,'.$show_p_online ; exit;

            if ($ret_['s_status'] == 1 && ($show_f_online == 'Y' || $show_n_online == 'Y' || $show_p_online == 'Y'))
                return '<img src="' . base_url() . 'images/icons/online.png" width="12" height="12" alt="" /> ';
            else if ($show_f_online == 'N' || $show_n_online == 'N' || $show_p_online == 'N')
                return '<img src="' . base_url() . 'images/icons/offline.png" width="12" height="12" alt="" />';
        } else
            return '<img src="' . base_url() . 'images/icons/offline.png" width="12" height="12" alt="" />';
    }
}

function check_user_online_image($user_id) {
    $ci = get_instance();
    $ci->db->where('i_user_id', $user_id);
    $arr_qry = $ci->db->get('users_online');
    $ret_ = $arr_qry->row_array(); #pr($ret_); exit;
    if (is_array($ret_) && count($ret_)) {
        if ($ret_['s_status'] == 1)
            return '<img src="' . base_url() . 'images/icons/online.png" width="12" height="12" alt="" />';
        else if ($ret_['s_status'] == 2)
            return '<img src="' . base_url() . 'images/icons/offline.png" width="12" height="12" alt="" />';
        else if ($ret_['s_status'] == 3)
            return '<img src="' . base_url() . 'images/icons/away.png" alt="" />';
        else if ($ret_['s_status'] == 4 || $ret_['s_status'] == 0)
            return '<img src="' . base_url() . 'images/icons/offline.png" width="12" height="12" alt="" />';
    } else
        return '<img src="' . base_url() . 'images/icons/offline.png" width="12" height="12" alt="" />';
}

function get_user_online_status_text($user_id) {
    $ci = get_instance();
    $ci->db->where('i_user_id', $user_id);
    $arr_qry = $ci->db->get('users_online');
    $ret_ = $arr_qry->row_array(); #pr($ret_); exit;
    if (is_array($ret_) && count($ret_)) {
        if ($ret_['s_status'] == 1)
            return 'Online';
        else if ($ret_['s_status'] == 2)
            return 'Offline';
        else if ($ret_['s_status'] == 3)
            return 'Away';
        else if ($ret_['s_status'] == 4)
            return 'Offline';
    } else
        return 'Offline';
}

# function to get Country-Name...

function getCountryName($countryID) {
    $CI = get_instance();
    $CI->load->model('country_model');
    $country_name = $CI->country_model->get_name_by_id($countryID);

    return $country_name;
}

# function to generate Unique random Strings...

function getUniqueCode($length = "8") {
    $code = md5(uniqid(rand(), true));

    if ($length != "") {
        $modified_str = substr($code, 0, $length);
        $code = strtoupper($modified_str);
    } else {
        $code = strtoupper($code);
    }

    return $code;
}

# function to generate unique Order-ID [format: "OR<user-id>_<tstamp>"]

function generate_unique_orderID($user_id = null) {
    $orderID = "OR{$user_id}_" . time();

    return $orderID;
}

// Fetching `country-name` while passing `country-id`...
# function to get country-id by name...
function get_country_name($country_name) {

    $ci = get_instance();

    $i_country_id = 0;
    $country_name = strtolower($country_name);

    $sql = sprintf("SELECT `id` FROM %s WHERE `s_country_name` = '%s' ", $ci->db->MST_COUNTRY, $country_name);
    $query = $ci->db->query($sql);

    if ($query->num_rows() > 0) {
        $result = $query->row();
        $i_country_id = $result->id;
    }


    return $i_country_id;
}

# function to get user-name by ID...

function get_username_by_id($i_user_id = NULL) {
    try {
        $o_ci = get_instance();
        $o_ci->db->select("s_first_name , s_last_name");
        $o_ci->db->where('id', $i_user_id);

        $arr_qry = $o_ci->db->get('users');
        $ret_ = $arr_qry->row_array();
        //echo $o_ci->db->last_query();

        return $ret_["s_first_name"] . ' ' . $ret_["s_last_name"];
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

/**
 * https Base URL
 *
 * Returns the "https_base_url" item from your config file
 *
 * @access    public
 * @return    string
 */
if (!function_exists('https_base_url')) {

    function https_base_url() {
        try {
            $CI = & get_instance();
            return $CI->config->slash_item('https_base_url');
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

}

function get_primary_user_info($id, $size = "mini") {

    $id = intval($id);
    $ci = get_instance();
    $ci->load->model('users_model');
    $info = $ci->users_model->fetch_this($id);
    $info['s_profile_url'] = get_profile_url($id);
    ;
    return $info;
}

# get comma seperated address of user ##

function get_user_address_info($id) {
    $id = intval($id);
    $ci = get_instance();
    $ci->load->model('users_model');
    $SQL = " SELECT con.s_country , s.s_state, c.s_city  FROM
						cg_users u LEFT JOIN cg_country con ON con.id = u.i_country_id
						LEFT JOIN cg_state s ON s.id = u.i_state_id
						LEFT JOIN cg_city c ON u.i_city_id = c.id WHERE u.id = {$id}";

    $query = $ci->db->query($SQL);
    $info = $query->result_array();

    if (!empty($info[0]['s_city']))
        $address['city'] = $info[0]['s_city'];
    if (!empty($info[0]['s_state']))
        $address['state'] = $info[0]['s_state'];
    if (!empty($info[0]['s_country']))
        $address['country'] = ($info[0]['s_country']);
    $address_str = implode(', ', $address);


    return $address_str;
}

# get comma seperated address of church ##

function get_church_address_info($id) {
    $id = intval($id);
    $ci = get_instance();
    $ci->load->model('users_model');
    $info = $ci->users_model->fetch_this($id);
    $address = array();

    if (!empty($info['s_church_address']))
        $address['s_church_address'] = $info['s_church_address'];

    if (!empty($info['s_church_postcode']))
        $address['s_church_postcode'] = $info['s_church_postcode'];

    if (!empty($info['s_church_city']))
        $address['s_church_city'] = get_city_name_by_id($info['i_church_city_id']);

    if (!empty($info['s_church_state']))
        $address['s_church_state'] = get_state_name_by_id($info['i_church_state_id']);

    if (!empty($info['i_church_country_id']))
        $address['country'] = get_country_name_by_id($info['i_church_country_id']);

    $address_str = implode(', ', $address);
    return $address_str;
}

### new added

function get_user_country_info($id) {
    $id = intval($id);
    $ci = get_instance();
    $ci->load->model('users_model');
    $info = $ci->users_model->fetch_this($id);
    $address_str = get_countryname($info['i_country_id']);
    return $address_str;
}

function get_video_album_url($id, $album_name = "") {

    $id = intval($id);
    if (trim($album_name) == '') {
        $ci = get_instance();
        $ci->load->model('my_videos_model');
        $info = $ci->my_videos_model->get_album_info($id);

        $album_name = $info['s_name'];
    }


    $url = base_url() . "video-album-detail/" . $id . "/" . my_url($album_name) . ".html";
    return $url;
}

function get_video_url($id, $album_name = "") {

    $id = intval($id);
    if (trim($album_name) == '') {
        $ci = get_instance();
        $ci->load->model('my_videos_model');
        $info = $ci->my_videos_model->get_video_by_id($id);

        $album_name = $info;
    }


    $url = base_url() . "video-detail/" . $id . "/" . my_url($album_name) . ".html";
    return $url;
}

function get_edit_video_album_url($id, $album_name = "") {

    $id = intval($id);
    if (trim($album_name) == '') {
        $ci = get_instance();
        $ci->load->model('my_videos_model');
        $info = $ci->my_videos_model->get_album_info($id);

        $album_name = $info['s_name'];
    }


    $url = base_url() . "edit-album/" . $id . "/" . my_url($album_name) . ".html";
    return $url;
}

function get_video_title($video_id) {
    $ci = get_instance();
    $ci->load->model('my_videos_model');
    $info = $ci->my_videos_model->get_video_title_by_id($video_id);
    return $info;
}

function get_audio_title($audio_id) {
    $ci = get_instance();
    $ci->load->model('user_audios_model');
    $info = $ci->user_audios_model->get_audio_title_by_id($audio_id);
    return $info;
}

function get_public_profile_photo_album_url($id) {
    $id = intval($id);

    $url = base_url() . "public-profile/" . $id . "/photo-album.html";
    return $url;
}

function get_public_profile_photos_url($user_id, $album_id) {
    $url = base_url() . "public-profile-photo/" . $user_id . "/album/" . $album_id . "/photos.html";
    return $url;
}

function get_public_profile_video_album_url($id) {
    $id = intval($id);

    $url = base_url() . "public-profile/" . $id . "/video-album.html";
    return $url;
}

function get_public_profile_videos_url($user_id, $album_id) {
    $url = base_url() . "public-profile-video/" . $user_id . "/album/" . $album_id . "/videos.html";
    return $url;
}

function get_public_profile_audio_album_url($id) {
    $id = intval($id);

    $url = base_url() . "public-profile/" . $id . "/audio-album.html";
    return $url;
}

function get_public_profile_audios_url($user_id, $album_id) {
    $url = base_url() . "public-profile-audio/" . $user_id . "/album/" . $album_id . "/audios.html";
    return $url;
}

######## methods for reminder section ##########

function get_reminder_todo_text($id) {
    $ci = get_instance();
    $ci->load->model('organizer_todo_model');
    $info = $ci->organizer_todo_model->get_reminder_todo_text($id);
    return $info;
}

function get_reminder_todo_start_time($id) {
    $ci = get_instance();
    $ci->load->model('organizer_todo_model');
    $info = $ci->organizer_todo_model->get_reminder_todo_start_time($id);
    return $info;
}

function get_reminder_todo_end_time($id) {
    $ci = get_instance();
    $ci->load->model('organizer_todo_model');
    $info = $ci->organizer_todo_model->get_reminder_todo_end_time($id);
    return $info;
}

function get_reminder_event_text($id) {
    $ci = get_instance();
    $ci->load->model('events_model');
    $info = $ci->events_model->get_reminder_todo_text($id);
    return $info;
}

function get_reminder_event_start_time($id) {
    $ci = get_instance();
    $ci->load->model('events_model');
    $info = $ci->events_model->get_reminder_todo_start_time($id);
    return $info;
}

function get_reminder_event_end_time($id) {
    $ci = get_instance();
    $ci->load->model('events_model');
    $info = $ci->events_model->get_reminder_todo_end_time($id);
    return $info;
}

############ EVENTS SECTION  ##############

function get_edit_event_url($id, $event_name) {

    $id = intval($id);
    if (trim($album_name) == '') {
        $ci = get_instance();
        $ci->load->model('events_model');
        $info = $ci->events_model->get_by_id($id);
        $event_name = $info['s_title'];
    }


    $url = base_url() . "edit-event/" . $id . "/" . my_url($event_name) . ".html";
    return $url;
}

### method o check friend and netpal status

function check_friend_netpal_status($result_arr) {

    $ci = get_instance();
    $ci->load->model('users_model');
    $ci->load->model('netpals_model');
    //pr($result_arr,1);
    if (is_array($result_arr) && count($result_arr)) {
        foreach ($result_arr as $key => $val) {

            $result_arr[$key]['post_owner_user_id'];
            $if_friend = $ci->users_model->if_already_friend(intval(decrypt($ci->session->userdata('user_id'))), $result_arr[$key]['post_owner_user_id']);
            // pr($if_friend);

            if (count($if_friend) > 0) {
                $result_arr[$key]['if_already_friend'] = 'true';
                //$result_arr = array_merge($result_arr);
            } else
                $result_arr[$key]['if_already_friend'] = 'false';
            //pr($result_arr,1);
            $arr_already_netpal = $ci->netpals_model->if_already_netpal(intval(decrypt($ci->session->userdata('user_id'))), $result_arr[$key]['post_owner_user_id']);
            if (count($arr_already_netpal) > 0 || (intval(decrypt($ci->session->userdata('user_id'))) == $result_arr[$key]['post_owner_user_id']))
                $result_arr[$key]['already_added_netpal'] = 'true';
            else
                $result_arr[$key]['already_added_netpal'] = 'false';

            #unset($get_friend_status_me_him);
        }
    }
    return $result_arr;
}

//-------------------------------- trade center section -----------------------------------
function get_product_category_detail_url($id, $name) {
    $url = base_url() . 'admin/trade-center/product-categories/' . $id . '/' . my_url($name) . ".html";

    return $url;
}

//-------------------------------- end of trade center section -----------------------------------
############################## TWITTER SECTION #############################################
function get_unique_twiter_profile_name($user_fname = "", $user_lname = "") {
    try {
        $ci = get_instance();
        $ci->load->model('users_model');
        $check_info = $ci->users_model->twitter_username_already_exists($user_fname, $user_lname);
        if ($check_info > 0) {
            $var = $check_info; //getUniqueCode(1);
            return trim('@' . $user_fname . '' . substr($user_lname, 0, 1) . '_' . sprintf("%02d", $var));
        } else
            return '@' . $user_fname . '' . substr($user_lname, 0, 1);
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

### get  twitter unique profile name 

function getTwitterUsernameById($i_user_id = NULL) {
    try {
        $o_ci = get_instance();
        $o_ci->db->select("s_tweet_id");
        $o_ci->db->where('id', $i_user_id);

        $arr_qry = $o_ci->db->get('users');
        $ret_ = $arr_qry->row_array();
        //echo $o_ci->db->last_query();

        return $ret_["s_tweet_id"];
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

### get twitter public profile by s_tweet_id

function getTwitterProfileLink($s_tweet_id = '') {
    try {

        $ci = get_instance();
        $ci->load->model('users_model');
        $info = $ci->users_model->getUserInfo_by_tweet_id($s_tweet_id);
        $user_type = $info['i_user_type'];
        $user_name = $info['s_first_name'] . ' ' . $info['s_last_name'];
        $count = strlen($s_tweet_id);

        $url = base_url() . "user-twitter-profile/" . $info['id'] . '/' . substr($s_tweet_id, 1, $count) . ".html";
        return $url;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function getTwitterProfileLink_text($s_tweet_id = '') {
    try {

        $ci = get_instance();
        $ci->load->model('users_model');
        $info = $ci->users_model->getUserInfo_by_tweet_id($s_tweet_id);
        $user_type = $info['i_user_type'];
        $user_name = $info['s_first_name'] . ' ' . $info['s_last_name'];
        $count = strlen($s_tweet_id);

        $url = '<a href="' . base_url() . "user-twitter-profile/" . $info['id'] . '/' . substr($s_tweet_id, 1, $count) . ".html" . '">' . $s_tweet_id . '</a>';
        return $url;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function get_twitter_profile_hash_link($string) {


    ### replacing hash
    ///echo $string;

    $mtchd_arr = array();
    preg_match_all('/(?!\b)(#\w+\b)/', $string, $mtchd_arr);
    $newstr = '';
    //pr($mtchd_arr,1);
    foreach ($mtchd_arr[1] as $key => $val) { //echo $val[$key].' ==';
        $ci = get_instance();
        $ci->load->model('my_tweet_model');
        $trend_info = $ci->my_tweet_model->get_trend_id_by_name($val);

        $link = base_url() . "search-trends/" . $trend_info['id'] . "/" . my_url($trend_info['s_tags']) . ".html";

        if ($val != "#039") {
            $string = str_replace($val, '<span style="color:#0084B4"><a href="' . $link . '" style="color:#0084B4">' . $val . '</a></span>', $string);
        }
    }
    ## end hash replaced
    ### replacing @

    $new_mtchd_arr = array();
    preg_match_all('/(?!\b)(@\w+\b)/', $string, $new_mtchd_arr);

    foreach ($new_mtchd_arr[1] as $key => $val) {

        $link = getTwitterProfileLink($val);

        $string = str_replace($val, '<span style="color:#0084B4"><a href="' . $link . '" style="color:#0084B4">' . $val . '</a></span>', $string);
    }
    ## end @ replaced

    return $string;
}

function get_tweet_follow_status_by_userid($i_user_id) {

    $ci = get_instance();
    $ci->load->model('my_tweet_model');
    $is_follow = $ci->my_tweet_model->isFollowing($i_user_id);
    return $is_follow;
}

##############################TWITTER SECTION ######################################################### 
############################## MEDIA SECTION ######################################################### 

function get_HMS_from_sec($sec) {
    $time = array();
    $min = 0;
    $hr = 0;
    if ($sec > 59) {
        $remind_sec = $sec % 60;
        $res_sec = $sec - $remind_sec;
        $min = $res_sec / 60;
    } else {
        $remind_sec = $sec;
    }
    if ($min > 59) {
        $remind_min = $min % 60;
        $res_min = $min - $remind_min;
        $hr = $res_min % 60;
    } else {
        $remind_min = $min;
    }

    if ($hr != 0) {
        $time['hr'] = $hr . ' hrs ';
    }
    if ($min != 0) {
        $time['min'] = $remind_min . ' mins ';
    }
    if ($sec != 0) {
        $time['sec'] = $remind_sec . ' secs ';
    }
    $time_duration = implode(' ', $time);
    return $time_duration;
}

function get_blog_name_by_id($id) {

    $ci = get_instance();
    $ci->load->model('my_blog_model');
    $info = $ci->my_blog_model->get_by_id($id);
    return $info['s_title'];
}

function get_ring_name_by_id($id) {

    $ci = get_instance();
    $ci->load->model('my_ring_model');
    $info = $ci->my_ring_model->get_by_id($id);
    return $info['s_ring_name'];
}

############################## END MEDIA SECTION ######################################################### 
########## ## bible reading plan formula ###########

function get_bible_reading_plan($total_days, $total_verse) {

    ### calculating verse reading plan per day ###
    $remainder_verse = 0;
    $left_verse = 0;
    $per_day_even_verse = 0;
    $distributed_verse = 0;
    $remaining_verse_after_distributing = 0;
    $days_left = 0;
    $left_days_no_of_verse = 0;

    ##uneven verse

    $remainder_verse = $total_verse % $total_days;

    ###Even verse 
    $left_verse = $total_verse - $remainder_verse;

    ##total verse per day
    $per_day_even_verse = $left_verse / $total_days;
    $PER_DAY_VERSE = $per_day_even_verse + 1;

    ##distributed verse (distribting even verse each day  ie. +1 uneven verse) 
    $distributed_verse = ($per_day_even_verse + 1) * $remainder_verse;

    ##remaining verse after distributing  
    $remaining_verse_after_distributing = ($total_verse - $distributed_verse);

    ##total days left after distrb
    $days_left = ($total_days - $remainder_verse);

    ##dist verse for left days :::
    $left_days_no_of_verse = ($remaining_verse_after_distributing / $days_left);

    $verse_array = array();
    $verse_array[0] = $PER_DAY_VERSE . '###' . $remainder_verse;
    $verse_array[1] = $left_days_no_of_verse . '###' . $days_left;
    ## returning  no. of verse for ### n days 
    return $verse_array;
}

########## ## bible reading plan formula ###########

function get_highlights_color($vid = '') {
    $ci = get_instance();
    $str = "";
    $sql = "SELECT * FROM 
                {$ci->db->BIBLE_HILITS_COLOR} WHERE 1 {$wh}";
    /* LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id */

    $res = $ci->db->query($sql)->result_array();

    $str = '<ul class="color-container">';
    foreach ($res as $val) {
        $str .= '<li title="' . $val['s_class'] . '" onclick="getColorCode(\'' . $val['id'] . '\',\'' . $vid . '\');"><a href="javascript:void(0);" class="' . $val['s_class'] . '"></a></li>';
    }
    $str .="</ul>";
    return $str;
}

function get_bible_progress_bar($plan_type) {

    $ci = get_instance();
    $ci->load->model('holy_place_model');

    ## get read history 
    //$read_history = $ci->holy_place_model->get_reading_plan_last_read_info();

    $total_read_day = $ci->holy_place_model->get_totaldays_read();

    if ($plan_type == '6 Months Plan') {

        $total_days = 184;
    } else if ($plan_type == '1 Year Plan') {

        $total_days = 366;
    } else if ($plan_type == 'Custom Plan') {
        $start_date = $ci->holy_place_model->get_reading_plan_start_date();
        $end_date = $ci->holy_place_model->get_reading_plan_end_date();

        $d1 = strtotime($start_date);
        $date_to = date('Y-m-d', mktime(0, 0, 0, date('m', $d1), date('d', $d1) - 1, date('y', $d1)));
        $d11 = strtotime($start_date);
        $d2 = strtotime($end_date);
        $all = round(($d2 - $d11) / 60);
        $d = floor($all / 1440);
        //echo $plan_type;
        $total_days = $d;
    } else if ($plan_type == 'Beginning To End') {
        $total_days = 365;
    }
    //echo '== '.$read_history['i_day'].' == ';
    $percent_val = ( $total_read_day / $total_days) * 100;

    //pr($read_history);
    return '<div class="progress-holder" style="width:100%;"><span class="progress-bar" style="width:' . $percent_val . '%;"></span></div>';
}

function get_countryname($id) {
    try {
        $CI = & get_instance();
        $res = $CI->db->query("select id,s_country as s_country FROM {$CI->db->COUNTRY} WHERE id='" . $id . "'");
        $mix_value = $res->result_array();
        return $mix_value[0]['s_country'];
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function get_statename($id) {
    try {
        $CI = & get_instance();
        $res = $CI->db->query("select id,s_state as s_state FROM {$CI->db->STATE} WHERE id='" . $id . "'");
        $mix_value = $res->result_array();
        return $mix_value[0]['s_state'];
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function get_cityname($id) {
    try {
        $CI = & get_instance();
        $res = $CI->db->query("select id,s_city as s_city FROM {$CI->db->CITY} WHERE id='" . $id . "'");
        $mix_value = $res->result_array();
        return $mix_value[0]['s_city'];
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

#### prayer group url 

function get_group_url($id, $group_name) {

    $id = intval($id);
    if (trim($group_name) == '') {
        $ci = get_instance();
        $ci->load->model('prayer_group_model');
        $info = $ci->prayer_group_model->get_group_detail_by_id($id);
        $group_name = $info['s_group_name'];
    }


    $url = base_url() . "prayer-group-details/" . $id . "/" . my_url($group_name) . ".html";
    return $url;
}

## prayer group chat room details by room_id

function get_chat_room_details($room_id) {

    $room_id = intval($room_id);
    if ($room_id != '') {
        $ci = get_instance();
        $ci->load->model('chat_rooms_model');
        $info = $ci->chat_rooms_model->get_by_id($room_id);
    }
    return $info;
}

### get prayer chat room start time and end time

function get_StartTime_EndTime($string) {
    $xml = simplexml_load_string($string);

    foreach ($xml->roomOpen->Time as $attr) {
        $ret_arr['end_time'] = $attr['e'];
        $ret_arr['strt_time'] = $attr['s'];
        $ret_arr['type'] = $attr['o'];
    }

    return $ret_arr;
}

### get group name by chat room id

function get_group_name_by_ChatRoom_id($room_id) {

    $room_id = intval($room_id);
    if ($room_id != '') {
        $ci = get_instance();
        $ci->load->model('chat_rooms_model');
        $grp_id = $ci->chat_rooms_model->get_grpid_by_Chatroom_id($room_id);

        $ci->load->model('prayer_group_model');
        $info = $ci->prayer_group_model->get_group_detail_by_id($grp_id);
        $group_name = $info['s_group_name'];
    }
    return $group_name;
}

##########################################
### PAYPAL PRO CARD PAYMENT 
##########################################

function PPHttpPost($methodName_, $nvpStr_) {

    global $environment;

    // Set up your API credentials, PayPal end point, and API version.
    $API_UserName = urlencode('sidneynazz_api1.gmail.com');
    $API_Password = urlencode('1364905946');
    $API_Signature = urlencode('AlpKjcufLj8sJqnFfSEJgHbFhTxOAtEpOr1DMAierdVTI0MaHS8OttfT');

    /* $API_UserName = urlencode('momenturellc_api1.gmail.com');
      $API_Password = urlencode('NYSLFYK7CGFD6Y44');
      $API_Signature = urlencode('ATDqhzrBhaQ2DVxH6HPAqw1Cl6JJA.hlM2rZxPzzGGOPJFB4IjentGVi'); */
    /* $API_Endpoint = "https://api-3t.paypal.com/nvp"; */

    //if("sandbox" === $environment || "beta-sandbox" === $environment) {
    $API_Endpoint = "https://api-3t.sandbox.paypal.com/nvp";
    //}


    $version = urlencode('51.0');

    // Set the curl parameters.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);

    // Turn off the server and peer verification (TrustManager Concept).
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);

    // Set the API operation, version, and API signature in the request.
    $nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";

    // Set the request as a POST FIELD for curl.
    curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

    // Get response from the server.
    $httpResponse = curl_exec($ch);

    if (!$httpResponse) {
        exit("$methodName_ failed: " . curl_error($ch) . '(' . curl_errno($ch) . ')');
    }

    // Extract the response details.
    $httpResponseAr = explode("&", $httpResponse);

    $httpParsedResponseAr = array();
    foreach ($httpResponseAr as $i => $value) {
        $tmpAr = explode("=", $value);
        if (sizeof($tmpAr) > 1) {
            $httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
        }
    }

    if ((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
        exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
    }

    return $httpParsedResponseAr;
}

### get shor day name #####

function getShortDay($dayname) {

    $shrt_name = '';

    if ($dayname == 'Sunday') {
        $shrt_name = 'Sun';
    } elseif ($dayname == 'Monday') {
        $shrt_name = 'Mon';
    } elseif ($dayname == 'Tuesday') {
        $shrt_name = 'Tue';
    } elseif ($dayname == 'Wednesday') {
        $shrt_name = 'Wed';
    } elseif ($dayname == 'Thursday') {
        $shrt_name = 'Thu';
    } elseif ($dayname == 'Friday') {
        $shrt_name = 'Fri';
    } elseif ($dayname == 'Saturday') {
        $shrt_name = 'Sat';
    }

    return $shrt_name;
}

######################################################
### calculate  total skill req on a charity project
######################################################

function get_total_skill_reqired($total_manpower, $total_days, $available_manpower, $available_day) {

    $skill_data = array();

    $skill_data[$total_days - $available_day] = $total_manpower;
    $skill_data[$available_day] = $total_manpower - $available_manpower;

    return $skill_data;
}

function checkSufficientSkillPerDay($skill_name, $project_id, $check_date) {

    global $CI;

    $CI->load->model('projects_model');
    $is_sufficient = $CI->projects_model->getSkillSufficency($skill_name, $project_id, $check_date);
    return $is_sufficient;
}

function setCvFilename($path, $filename, $file_ext) {
    #mt_srand();
    #$filename = md5(uniqid(mt_rand())).$file_ext;    
    $filename = $filename . '.' . $file_ext;
    if (!file_exists($path . $filename)) {
        return $filename;
    } else {
        $filename = str_replace($file_ext, '', $filename);
        $filename = str_replace('.', '', $filename);
        $new_filename = '';
        for ($i = 1; $i < 100; $i++) {
            if (!file_exists($path . $filename . '-' . $i . '.' . $file_ext)) {
                $new_filename = $filename . '-' . $i;
                break;
            }
        }
        $new_filename .= '.' . $file_ext;
        return $new_filename;
    }
}

### get denomination name

function getDenominationNameById($id) {

    $id = intval($id);
    if ($id != '') {
        $ci = get_instance();
        $ci->load->model('denomination_model');
        $info = $ci->denomination_model->fetch_this($id);
    }
    return $info[0]['s_name'];
}

### CheckUserRelation

function CheckUserRelation($to_user_id) {

    $CI = get_instance();
    $CI->load->model('users_model');
    $CI->load->model('my_prayer_partner_model');
    $CI->load->model('netpals_model');

    $logged_user = intval(decrypt($CI->session->userdata('user_id')));
    $result_arr = array();

    $pp_status = $CI->my_prayer_partner_model->get_prayer_partner_accepted_me_him($logged_user, $to_user_id);
    $netpal_status = $CI->netpals_model->if_already_netpal($logged_user, $to_user_id);
    $friend_status = $CI->users_model->if_already_friend($logged_user, $to_user_id);


    if (count($pp_status) > 0)
        $result_arr['i_isprayerpartner'] = 'true';

    if (count($friend_status) > 0)
        $result_arr['i_isfriend'] = 'true';

    if (count($netpal_status) > 0)
        $result_arr['i_isnetpal'] = 'true';

    return $result_arr;
}

# function to get user email by ID...

function get_useremail_by_id($i_user_id = NULL) {
    try {
        $o_ci = get_instance();
        $o_ci->db->select("s_email");
        $o_ci->db->where('id', $i_user_id);
        $arr_qry = $o_ci->db->get('users');
        $ret_ = $arr_qry->row_array();
        return $ret_["s_email"];
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function get_price($amt) {
    return '$' . number_format($amt);
}

function show_cateory_in_li($wh = '') {
    $CI = & get_instance();

    $res = $CI->db->query("SELECT * FROM {$CI->db->TRADE_CAT} WHERE 1 {$wh} ORDER BY s_category_name ASC");
    $mix_value = $res->result_array();

    //echo $CI->db->last_query().' @!#!@';

    $s_option = '';
    $CSS = '';
    if ($mix_value) {
        $s_select = ''; //defined here for unsetting this var 
        foreach ($mix_value as $k => $val) {

            if ($k == 0)
                $CSS = 'active';
            else
                $CSS = '';
            $s_option .= '<li><a href="javascript:void(0);" onclick="goToSubCategoryList( $(this), \'' . encrypt($val["id"]) . '\');" class="sublinks  ' . $CSS . '" >' . $val["s_category_name"] . '</a></li>'; //encrypt($val["id"])
        }
        unset($s_select, $val);
    }

    unset($cond, $res, $mix_value, $mix_where, $s_id);
    return $s_option;
}

function show_rating($rating) {
    $str = '';
    $rating = round($rating, 1);
    if ($rating == 0)
        $str = '<img src="' . base_url() . 'images/icons/0.0.png" alt="">';
    else if ($rating <= 0.5)
        $str = '<img src="' . base_url() . 'images/icons/0.5.png" alt="">';
    else if ($rating > 0.5 && $rating <= 1.0)
        $str = '<img src="' . base_url() . 'images/icons/1.0.png" alt="">';
    else if ($rating > 1.0 && $rating <= 1.5)
        $str = '<img src="' . base_url() . 'images/icons/1.5.png" alt="">';
    else if ($rating > 1.5 && $rating <= 2)
        $str = '<img src="' . base_url() . 'images/icons/2.0.png" alt="">';
    else if ($rating > 2 && $rating <= 2.5)
        $str = '<img src="' . base_url() . 'images/icons/2.5.png" alt="">';
    else if ($rating > 2.5 && $rating <= 3)
        $str = '<img src="' . base_url() . 'images/icons/3.0.png" alt="">';
    else if ($rating > 3 && $rating <= 3.5)
        $str = '<img src="' . base_url() . 'images/icons/3.5.png" alt="">';
    else if ($rating > 3.5 && $rating <= 4)
        $str = '<img src="' . base_url() . 'images/icons/4.0.png" alt="">';
    else if ($rating > 4 && $rating <= 4.5)
        $str = '<img src="' . base_url() . 'images/icons/4.5.png" alt="">';
    else if ($rating > 4.5 && $rating <= 5)
        $str = '<img src="' . base_url() . 'images/icons/5.png" alt="">';

    return $str;
}

function get_requests($where) {
    $CI = & get_instance();
    $sql = "SELECT p.id AS id, r.i_qty AS req_qty,r.s_email AS req_email,r.s_phone,r.dt_req_date,
						r.i_accept ,
						r.id AS reqid,
						r.i_user_id AS requid,
						r.f_rate_for_buyer AS f_rate_for_buyer,
						r.f_rate_for_seller_item AS f_rate_for_seller_item,
						r.f_rate_for_seller_communication AS f_rate_for_seller_communication,
						r.f_rate_for_seller_time AS f_rate_for_seller_time,
						
						
						u.s_email,u.s_first_name,u.s_last_name,u.s_moblie_no, r.is_shipped
				FROM {$CI->db->ETRADE_PROD} AS p, {$CI->db->ETRADE_REQUEST} as r ,
					 {$CI->db->USERS} AS u
				WHERE 1 AND p.id=r.i_etrade_prod_id AND r.i_user_id=u.id
				{$where} ";

    $query = $CI->db->query($sql); // echo $this->db->last_query();exit;
    $result_arr = $query->result_array(); //pr($result_arr,1);

    return $result_arr;
}

function get_main_category_name($cat = '') {
    $CI = & get_instance();

    $res = $CI->db->query("SELECT C2.s_category_name as cat_name FROM {$CI->db->TRADE_CAT} C1 , {$CI->db->TRADE_CAT} C2
							WHERE C1.i_parent_category = C2.id 
							AND C1.id = {$cat}
							");
    $mix_value = $res->result_array();
    return $mix_value[0]['cat_name'];
}

function get_trade_category_name($cat = '') {
    $CI = & get_instance();

    $res = $CI->db->query("SELECT C1.s_category_name as cat_name FROM {$CI->db->TRADE_CAT} C1 WHERE C1.id = {$cat}");
    $mix_value = $res->result_array();
    return $mix_value[0]['cat_name'];
}

function get_total_requests($where) {
    $CI = & get_instance();
    $sql = "SELECT COUNT(*) as total FROM ( SELECT r.id
				FROM {$CI->db->ETRADE_PROD} AS p, {$CI->db->ETRADE_REQUEST} as r ,
					 {$CI->db->USERS} AS u
				WHERE 1 AND p.id=r.i_etrade_prod_id AND r.i_user_id=u.id
				{$where} group by r.id) drvd_tvl ";

    $query = $CI->db->query($sql); #echo $CI->db->last_query();exit;
    $result_arr = $query->result_array(); //pr($result_arr,1);

    return $result_arr[0]['total'];
}

function get_userid_requests($rid) {
    $CI = & get_instance();
    $sql = "SELECT  i_req_user_id FROM {$CI->db->ESWAP_REQ} WHERE id = {$rid}";

    $query = $CI->db->query($sql); #echo $CI->db->last_query();exit;
    $result_arr = $query->result_array(); //pr($result_arr,1);

    return $result_arr[0]['i_req_user_id'];
}

function get_requested_userids_prodid($rid, $where = '') {
    $CI = & get_instance();
    $sql = "SELECT  i_req_user_id FROM {$CI->db->ESWAP_REQ} WHERE i_rcv_prod_id = {$rid} {$where}";

    $query = $CI->db->query($sql); #echo $CI->db->last_query();exit;
    $result_arr = $query->result_array(); //pr($result_arr,1);
    return $result_arr;
}

function get_efreebie_userid_requests($rid) {
    $CI = & get_instance();
    $sql = "SELECT i_user_id FROM {$CI->db->EFREE_REQ} WHERE id = {$rid}";

    $query = $CI->db->query($sql); #echo $CI->db->last_query();exit;
    $result_arr = $query->result_array(); //pr($result_arr,1);

    return $result_arr[0]['i_user_id'];
}

function get_efreebie_requested_userids_prodid($rid, $where = '') {
    $CI = & get_instance();
    $sql = "SELECT  i_user_id FROM {$CI->db->EFREE_REQ} WHERE i_freebie_prod_id = {$rid} {$where}";

    $query = $CI->db->query($sql); #echo $CI->db->last_query();exit;
    $result_arr = $query->result_array(); //pr($result_arr,1);
    return $result_arr;
}

function getTotalPrayerGroupCreated($i_user_id) {
    $CI = & get_instance();
    $CI->load->model('prayer_group_model');
    $total = $CI->prayer_group_model->get_total_my_groups($i_user_id, $s_where, 'ownership');
    return $total;
}

function getSelectedPageCSS($keyword, $i_main_menu_id) {

    $CI = & get_instance();
    $sql = "SELECT  count(*) as count FROM cg_bo_menu WHERE i_parent_menu_id = {$i_main_menu_id} 
			AND s_keyword = '{$keyword}'";

    $query = $CI->db->query($sql); //echo $CI->db->last_query();exit;
    $result_arr = $query->result_array(); //pr($result_arr,1);

    if ($result_arr[0][count] == 1) {
        $css = "select";
    }
    return $css;
}

#### get chat category

function getChatParentCatID($id) {
    $CI = & get_instance();
    $sql = "SELECT i_parent_category FROM {$CI->db->chat_category} WHERE id = " . $id;
    $query = $CI->db->query($sql); //echo $CI->db->last_query();exit;
    $result_arr = $query->result_array(); //pr($result_arr,1);

    return $result_arr[0]['i_parent_category'];
}

function getChatCatID($id) {
    $CI = & get_instance();
    $CI->load->model('chat_categories_model');
    $wh = " WHERE  cat.i_room_id =  " . $id;
    $s_category_name = $CI->chat_categories_model->get_category_name($wh);

    return $s_category_name;
}

function get_chat_category_detail_url($id, $name) {
    $url = base_url() . 'admin/social-hub/chat-categories/' . $id . '/' . my_url($name) . ".html";

    return $url;
}

# get comma seperated address of user ##

function get_user_location($id) {
    $id = intval($id);
    $ci = get_instance();
    $ci->load->model('users_model');
    $SQL = " SELECT con.s_country , s.s_state, c.s_city  FROM
						cg_users u LEFT JOIN cg_country con ON con.id = u.i_country_id
						LEFT JOIN cg_state s ON s.id = u.i_state_id
						LEFT JOIN cg_city c ON u.i_city_id = c.id WHERE u.id = {$id}";

    $query = $ci->db->query($SQL);
    $info = $query->result_array();

    if (!empty($info[0]['s_city']))
        $address['city'] = $info[0]['s_city'];

    if (!empty($info[0]['s_country']))
        $address['country'] = ($info[0]['s_country']);
    $address_str = implode(', ', $address);


    return $address_str;
}

function get_product_attr_ByCatID($pid) {
    $ci = get_instance();
    $sql = "SELECT p.id as attr_id,
							p.s_name as attr_name,
							p.i_type as attr_type
						    FROM {$ci->db->category_attribute} AS p 
								LEFT JOIN cg_category_attribute_values c ON p.id = c.i_attr_id 
								WHERE p.i_category_id =  {$pid}
								GROUP BY p.id
								ORDER BY p.s_name ASC";
    $res = $ci->db->query($sql);
    $result_arr = $res->result_array();

    if (count($result_arr)) {
        foreach ($result_arr as $k => $val) {
            $result_arr[$k]['attr_arr'] = get_attr_values_ByPID($val['attr_id']);
        }
    }

    $html = '';

    if (count($result_arr)) {
        foreach ($result_arr as $k => $val) {


            $name = $val['attr_name'];
            $attribute_value = $val['attr_arr'];

            $fldname = str_replace(' ', '_', strtolower($val['attr_name']));

            if ($val['attr_type'] == '2') {

                $html .= ' <li>
										<label class="attr-subproduct">' . $name . ' :</label>
										<br class="clr"/>
										<select class="subproduct-inpu attr-list-width sub_attr_sel" name="' . $fldname . '" multiple="multiple">
										';
            } else {
                $html .= '<li><label class="attr-subproduct">' . $name . ' :</label><br class="clr"/>';
            }

            if (count($attribute_value)) {
                foreach ($attribute_value as $k => $a_val) {

                    if ($val['attr_type'] == 1) {
                        $html .= '<input type="text" value="" class="subproduct-input attr-list-width " name="' . $fldname . '" />';
                    } else {
                        $html .= '<option value="' . $a_val['s_name'] . '">' . $a_val['s_name'] . '</option>';
                    }
                }
            }
            if ($val['attr_type'] == '2')
                $html .= '</select>
										</li>';
            else
                $html .= '</li>';
        }
    }
    else {

        $html = ' <li>
					  <label class="attr-subproduct">No attributes.</label>
				    </li>';
    }


    return $html;
}

function get_attr_values_ByPID($attrid) {
    $ci = get_instance();
    $sql = "SELECT  c.* FROM {$ci->db->category_attribute} AS p 
								LEFT JOIN cg_category_attribute_values c ON p.id = c.i_attr_id 
								WHERE c.i_attr_id =  {$attrid}
								{$cond} ORDER BY c.s_name ASC";
    $res = $ci->db->query($sql);
    $result_arr = $res->result_array();

    return $result_arr;
}

function check_Chat_link_online_hlpr($user_id, $scope = 'own', $relation = 'no', $ImgOnly = '') {
    $ci = get_instance();
    $ci->db->where('i_user_id', $user_id);
    $arr_qry = $ci->db->get('users_online');
    $ret_ = $arr_qry->row_array();
    #echo $ci->db->last_query();
    #pr($ret_);

    $sql_check_user = "SELECT * from cg_users_status where i_user_id = {$user_id} order by last_seen_date desc LIMIT 0,1";
    $status_arr = $ci->db->query($sql_check_user)->row_array();

    if ($relation == 'no' && $scope == 'own') {
        if (is_array($ret_) && count($ret_)) {

            if ($ret_['s_status'] == 1)
                return false;
        } else
            return false;
    }
    else {
        if (is_array($ret_) && count($ret_) && is_array($status_arr) && count($status_arr)) {

            $show_f_online = 'N';
            $show_n_online = 'N';
            $show_p_online = 'N';

            ### checking online status for user grp	
            if ($status_arr['i_isfriend'] == 1) {
                $show_f_online = ($relation['i_isfriend'] == 'true' ) ? 'Y' : 'N';
            }
            if ($status_arr['i_isnetpal'] == 1) {
                $show_n_online = ($relation['i_isnetpal'] == 'true' ) ? 'Y' : 'N';
            }
            if ($status_arr['i_isprayerpartner'] == 1) {
                $show_p_online = ($relation['i_isprayerpartner'] == 'true' ) ? 'Y' : 'N';
            }

            ### checking online status for user grp	
            //echo $show_f_online .',,'.$show_n_online.',,'.$show_p_online ; exit;

            if ($ret_['s_status'] == 1 && ($show_f_online == 'Y' || $show_n_online == 'Y' || $show_p_online == 'Y'))
                return true;
            else if ($show_f_online == 'N' || $show_n_online == 'N' || $show_p_online == 'N')
                return false;
        } else
            return false;
    }
}

function get_Chat_username_by_id($i_user_id = NULL) {
    try {
        $o_ci = get_instance();
        $o_ci->db->select("s_first_name");
        $o_ci->db->where('id', $i_user_id);

        $arr_qry = $o_ci->db->get('users');
        $ret_ = $arr_qry->row_array();
        //echo $o_ci->db->last_query();

        return $ret_["s_first_name"];
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function getCredentials($i_user_id = NULL) {
    try {
        $o_ci = get_instance();
        $o_ci->db->select("s_password");
        $o_ci->db->where('id', $i_user_id);

        $arr_qry = $o_ci->db->get('users');
        $ret_ = $arr_qry->row_array();
        //echo $o_ci->db->last_query();

        return $ret_["s_password"];
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function show_profile_rating($profile_id) {
    if (intval($profile_id) != 0) {
        $ci = get_instance();
        $SQL = "SELECT * FROM cg_user_profile_rating  
					WHERE  i_user_id = {$profile_id}";
        $query = $ci->db->query($SQL);
        $result_arr = $query->result_array();

        $html = '<ul class="small-ratings">';
        $avg_rate = 0;
        $total_rate = 0;
        if (count($result_arr)) {
            foreach ($result_arr as $k => $val) {
                $total_rate = $total_rate + $val['i_rate'];
            }
            $avg_rate = ceil($total_rate / count($result_arr));
        }

        ### check if user already rated this  profile #####
        $logged_id = intval(decrypt($ci->session->userdata('user_id')));
        $SQL = "SELECT COUNT(*) as count FROM cg_user_profile_rating  
					WHERE  i_user_id = {$profile_id} AND i_given_by_user_id  = {$logged_id} ";
        $query = $ci->db->query($SQL);
        $result_arr = $query->result_array();

        #### check if user already rated #####


        for ($i = 1; $i < 6; $i++) {
            $click_txt = ($result_arr[0]['count'] || $profile_id == $logged_id) ? '' : 'onclick="rate_user(' . $profile_id . ', ' . $i . ')"';
            if ($i <= $avg_rate)
                $html .= '<li><a class="rate_a active" href="javascript:void(0);" ' . $click_txt . '></a></li>';
            else
                $html .= '<li><a class="rate_a" href="javascript:void(0);" ' . $click_txt . '></a></li>';
        }

        $html .= '</ul>';

        return $html;
    }
}

function isRated($profile_id) {

    if (intval($profile_id) != 0) {
        $ci = get_instance();
        $logged_id = intval(decrypt($ci->session->userdata('user_id')));
        $SQL = "SELECT COUNT(*) as count FROM cg_user_profile_rating  
					  WHERE  i_user_id = {$profile_id} AND i_given_by_user_id  = {$logged_id} ";
        $query = $ci->db->query($SQL);
        $result_arr = $query->result_array();
    }
    return $result_arr[0]['count'];
}

#
# $1 = 100cent
#

function get_fruit($id) {
    try {
        $CI = & get_instance();
        $res = $CI->db->query("SELECT s_fruit_name,s_image_name as image FROM {$CI->db->BIBLE_FRUIT} WHERE id='" . $id . "'");
        $mix_value = $res->result_array();
        return $mix_value;
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function get_event_title_by_id($id) {

    if (intval($id) != 0) {
        $CI = & get_instance();
        $CI->load->model('events_model');
        $name = $CI->events_model->get_event_title_id($id);
    }

    return $name;
}

function get_photo_by_id($photo_id) {
    $ci = get_instance();
    $ci->load->model('user_photos_model');
    $info = $ci->user_photos_model->get_by_id($photo_id);

    $IMG = base_url() . "uploads/user_photos/" . getThumbName($info['s_photo'], 'thumb');
    return $IMG;
}

function get_video_snap_by_id($photo_id) {
    $ci = get_instance();
    $ci->load->model('my_videos_model');
    $info = $ci->my_videos_model->get_video_info_by_id($photo_id);

    $img = base_url() . 'uploads/user_videos/' . getThumbName($info[0]['s_video_image'], 'bigthumb');
    return $img;
}

function get_photo_owner_name_by_id($photo_id) {
    $ci = get_instance();
    $ci->load->model('user_photos_model');
    $info = $ci->user_photos_model->get_by_id($photo_id);
    $name = get_username_by_id($info['i_user_id']);
    return $name;
}

function get_video_owner_name_by_id($photo_id) {
    $ci = get_instance();
    $ci->load->model('my_videos_model');
    $info = $ci->my_videos_model->get_video_info_by_id($photo_id);
    $name = get_username_by_id($info[0]['i_user_id']);
    return $name;
}

function get_audio_owner_name_by_id($photo_id) {
    $ci = get_instance();
    $ci->load->model('user_audios_model');
    $info = $ci->user_audios_model->get_by_id($photo_id);
    $name = get_username_by_id($info['i_user_id']);
    return $name;
}

function get_audio_track_by_id($photo_id) {
    $ci = get_instance();
    $ci->load->model('user_audios_model');
    $info = $ci->user_audios_model->get_by_id($photo_id);
    $name = $info['s_audio_file_name'];
    return $name;
}

function get_event_detail_by_id($event_id) {
    $ci = get_instance();
    $ci->load->model('events_model');
    $info = $ci->events_model->get_by_id($event_id);
    return $info;
}

function get_photo_ownerID_by_id($photo_id) {
    $ci = get_instance();
    $ci->load->model('user_photos_model');
    $info = $ci->user_photos_model->get_by_id($photo_id);
    return $info['i_user_id'];
}

function get_video_ownerID_by_id($photo_id) {
    $ci = get_instance();
    $ci->load->model('my_videos_model');
    $info = $ci->my_videos_model->get_video_info_by_id($photo_id);
    return $info['0']['i_user_id'];
}

function get_audio_ownerID_by_id($photo_id) {
    $ci = get_instance();
    $ci->load->model('user_audios_model');
    $info = $ci->user_audios_model->get_by_id($photo_id);
    return $info['i_user_id'];
}

function get_privacy_setting($mode, $selecteduser = array(), $section_name = '') {
    $CI = & get_instance();

    $CI->load->model('contacts_model');
    $CI->load->model('netpals_model');
    $CI->load->model('my_prayer_partner_model');
    $CI->load->model('my_ring_model');
    $CI->load->model('prayer_group_model');
    $i_profile_id = intval(decrypt($CI->session->userdata('user_id')));
    $res = $CI->db->query("SELECT * FROM {$CI->db->PRIVACY_SETTINGS} WHERE i_user_id='" . $i_profile_id . "' AND s_section_name='" . $mode . "'"); #echo $CI->db->last_query();
    $rst_arr = $res->result_array();
    #pr($rst_arr,1);
    if ($rst_arr) {
        if ($rst_arr[0]['i_friend_privacy'] == 0 && $rst_arr[0]['i_netpal_privacy'] == 0 && $rst_arr[0]['i_prayer_partner_privacy'] == 0 && $rst_arr[0]['i_ring_privacy'] == 0 && $rst_arr[0]['i_prayer_group_privacy'] == 0) {
            return 'Public to all<input type="hidden" name="privacy" value="public">';
        } else {
            foreach ($rst_arr as $privacyval) {
                ############            FRIEND         #################
                if ($privacyval['i_friend_privacy'] == 1) {
                    $WHERE_frnd = " WHERE 
										1
										AND c.s_status = 'accepted' 
										AND u.i_status=1 
										AND
										((c.i_requester_id = '" . $i_profile_id . "' AND u.id=c.i_accepter_id ) 
										OR (c.i_accepter_id = '" . $i_profile_id . "' AND u.id=c.i_requester_id )) GROUP BY u.id ";
                    $arr_friends = $CI->contacts_model->fetch_multi_online_friends($WHERE_frnd);

                    $showfrnd = '';


                    $strfrnds = '<h4 class="privacy-ring-blue_bold12"><a href="javascript:void(0);">Friends</a></h4>';
                    $strfrnds .= '<select id="' . $section_name . 'friendSlct" multiple="multiple" style="width:370px" name="' . $section_name . 'frnds[]">';
                    #pr($selecteduser['Friend']);
                    foreach ($arr_friends as $val) {

                        if (in_array($val['user_id'], $selecteduser['Friend'])) {
                            $check_frnd = 'selected="selected"';
                        } else {
                            $check_frnd = '';
                        }
                        $strfrnds .= '<option ' . $check_frnd . ' value="' . $val['user_id'] . '" >' . $val['s_first_name'] . ' ' . $val['s_last_name'] . '</option>';
                    }
                }
                $strfrnds .= '</select><div class="clr"></div>';
                if ($privacyval['i_netpal_privacy'] == 1) {
                    $WHERE_netpal = " WHERE 
											1
											AND u.i_status=1 
											AND ((c.i_requester_id = " . $i_profile_id . " AND u.id=c.i_accepter_id) 
												OR 
											(c.i_accepter_id=" . $i_profile_id . " AND u.id=c.i_requester_id))
											AND c.s_status='accepted' " . $add_where . " GROUP BY u.id ";

                    $arr_netpal = $CI->netpals_model->fetch_multi_online_netpals($WHERE_netpal);

                    $shownetpal = '';
                    if (count($selecteduser['Netpal']) > 0)
                        $shownetpal = 'style="display:block"';

                    $strnetpal = '<h4 class="privacy-ring-blue_bold12" ><a href="javascript:void(0);">Netpal</a></h4>';
                    $strnetpal .= '<div class="clr"></div><select id="' . $section_name . 'netpalselect" multiple="multiple" style="width:370px" name="' . $section_name . 'netpal[]">';
                    foreach ($arr_netpal as $val) {
                        if (in_array($val['user_id'], $selecteduser['Netpal']))
                            $check_netpal = 'selected="selected"';
                        else
                            $check_netpal = '';
                        $strnetpal .= '<option ' . $check_netpal . '" value="' . $val['user_id'] . '" ' . $check_netpal . '>' . $val['s_first_name'] . ' ' . $val['s_last_name'] . '</option>';
                    }
                }
                $strnetpal .= '</select>';
                if ($privacyval['i_prayer_partner_privacy'] == 1) {
                    $WHERE_pp = " WHERE 
										1
										AND c.s_status = 'accepted' 
										AND u.i_status=1 
										AND
										((c.i_requester_id = '" . $i_profile_id . "' AND u.id=c.i_accepter_id ) 
										OR (c.i_accepter_id = '" . $i_profile_id . "' AND u.id=c.i_requester_id ))  GROUP BY u.id ";

                    $arr_pp = $CI->my_prayer_partner_model->fetch_multi_online_friends($WHERE_pp);

                    $strpp = '';

                    $showpp = '';
                    if (count($selecteduser['Prayer Partner']) > 0)
                        $showpp = 'style="display:block"';

                    $strpp = '<h4 class="privacy-ring-blue_bold12"><a href="javascript:void(0);">Prayer Partner</a></h4>';
                    $strpp .= '<div class="clr"></div><select id="' . $section_name . 'ppselect" multiple="multiple" style="width:370px" name="' . $section_name . 'pp[]">';
                    foreach ($arr_pp as $val) {
                        if (in_array($val['user_id'], $selecteduser['Prayer Partner']))
                            $check_pp = 'selected="selected"';
                        else
                            $check_pp = '';
                        $strpp .= '<option ' . $check_pp . ' value="' . $val['user_id'] . '" >' . $val['s_first_name'] . ' ' . $val['s_last_name'] . '</option>';
                    }
                }

                ############            RING         #################
                $strpp .= '</select><div class="clr"></div>';

                //###########variable to show Ring at the time of edit
                $show_ring = '';
                if (count($selecteduser['Ring User']) > 0)
                    $show_ring = 'style="display:block;"';

                if ($privacyval['i_ring_privacy'] == 1) {
                    $arr_ring = $CI->my_ring_model->get_all_ring_added_by_loggedin_user($i_profile_id);

                    $strring = '<h4 class="privacy-ring-blue_bold12" ><a href="javascript:void(0);">Ring</a></h4>';
                    $strring .= '<div class="clr"></div><select id="' . $section_name . 'ringselect" multiple="multiple" style="width:370px" name="' . $section_name . 'ringuser[]">';

                    //variable to complete the count of <ul>
                    $ringfirstcount = 0;
                    foreach ($arr_ring as $ringid) {
                        $arr_ring_user = $CI->my_ring_model->get_all_ring_members_in_table_by_ring_id($ringid, '', '', ' AND I.i_joined=1');
                        $tmpring = '';
                        #pr($arr_ring_user);
                        foreach ($arr_ring_user as $val) {
                            ###########   variable to show prayer group users at the time of edit
                            $show_ring_user = '';
                            if (count($selecteduser['Ring User'][$val['id']]) > 0)
                                $show_ring_user = 'style="display:block;"';

                            if ($tmpring != $val['s_ring_name']) {
                                if ($ringfirstcount > 0)
                                    $strring .= '</optgroup>';

                                //$strring	 .= '<div class="clr"></div>';
                                $tmpring = $val['s_ring_name'];
                                $strring .= '<optgroup label="' . $val['s_ring_name'] . '"  value="' . $val['id'] . '">';
                                //$strring	 .= '<input type="hidden" name="ringid[]" value="'.$val['id'].'">';
                            }
                            $ringfirstcount++;
                            if (in_array($val['i_invited_id'], $selecteduser['Ring User'][$val['id']]))
                                $check_ringuser = 'selected="selected"';
                            else
                                $check_ringuser = '';
                            /* $strring	.= '<option name="ringuser_'.$val['id'].'[]" value="'.$val['i_invited_id'].'" '.$check_ringuser.'>'.$val['profile_name'].'</option>'; */
                            $strring .= '<option  value="' . $val['i_invited_id'] . '_' . $val['id'] . '" ' . $check_ringuser . '>' . $val['profile_name'] . '</option>';
                        }
                    }
                }

                #exit;
                ############            PRAYER GROUP         #################
                $strring .= '</select><div class="clr"></div>';

                //###########variable to show prayer group at the time of edit
                $show_pg = '';
                if (count($selecteduser['Prayer Group']) > 0)
                    $show_pg = 'style="display:block;"';

                if ($privacyval['i_prayer_group_privacy'] != 0) {
                    $arr_pg = $CI->prayer_group_model->get_grpids_by_logged_in_user($i_profile_id); //explode(', ',$privacyval['i_prayer_group_privacy']);
                    $strpg .= '<h4 class="privacy-ring-blue_bold12"><a href="javascript:void(0);">Prayer Group</a></h4>';

                    $strpg .= '<div class="clr"></div><select id="' . $section_name . 'pgselect" multiple="multiple" style="width:370px" name="' . $section_name . 'pg[]">';

                    //variable to complete the count of <ul>
                    $pgfirstcount = 0;
                    foreach ($arr_pg as $prayer_group_id) {
                        $arr_pg_user = $CI->prayer_group_model->get_members_by_grpid($prayer_group_id);
                        #pr($arr_pg_user);
                        $tmp_pg = '';
                        foreach ($arr_pg_user as $val) {
                            ###########   variable to show prayer group users at the time of edit
                            $show_pg_user = '';
                            if (count($selecteduser['Prayer Group'][$val['i_prayer_group_id']]) > 0)
                                $show_pg_user = 'style="display:block;"';

                            if ($tmp_pg != $val['s_group_name']) {
                                if ($pgfirstcount > 0)
                                    $strpg .= '</optgroup>';

                                $strpg .= '<div class="clr"></div>';
                                $tmp_pg = $val['s_group_name'];
                                $strpg .= '<div class="clr"></div><optgroup label="' . $val['s_group_name'] . '" >';
                                //$strpg	 	.=  '<div class="clr"></div>';
                                //$strpg	 	.=  '<input type="hidden" name="pgid[]" value="'.$val['i_prayer_group_id'].'">';
                            }
                            $pgfirstcount++;
                            if (in_array($val['i_user_id'], $selecteduser['Prayer Group'][$val['i_prayer_group_id']]))
                                $check_pguser = 'selected="selected"';
                            else
                                $check_pguser = '';
                            $strpg .= '<option  value="' . $val['i_user_id'] . '_' . $val['i_prayer_group_id'] . '" ' . $check_pguser . '>' . $val['s_profile_name'] . '</option>';
                        }
                    }
                    $strpg .= '</select><div class="clr"></div>';
                }
                #$return_variable	= $strfrnds.$strnetpal.$strpp.$strring.$strpg;
                return '<input type="hidden" name="privacy" value="private">' . $strfrnds . $strnetpal . $strpp . $strring . $strpg;
            }
        }
    }
    else {
        return 'Public to all <input type="hidden" name="privacy" value="noprivacy">';
    }
}

function insert_privacy($itemid, $post, $table, $field, $post_alias = '') {
    $CI = & get_instance();
    $i = 0;
    if ($post['privacy'] == 'private') {
        foreach ($post[$post_alias . 'frnds'] as $val) {
            $arruser[$i] = $val;
            $arrsec[$i] = 'Friend';
            $i++;
        }

        foreach ($post[$post_alias . 'netpal'] as $val) {
            $arruser[$i] = $val;
            $arrsec[$i] = 'Netpal';
            $i++;
        }

        foreach ($post[$post_alias . 'pp'] as $val) {
            $arruser[$i] = $val;
            $arrsec[$i] = 'Prayer Partner';
            $i++;
        }
        #pr($post['ringuser']);
        /* foreach($post['ringid'] as $val)
          {
          foreach($post['ringuser_'.$val] as $valringuser)
          {
          $arruser[$i]		= $valringuser;
          $arrsec[$i]			= 'Ring User';
          $arrsecid[$i]		= $val;
          $i++;
          }
          } */
        foreach ($post[$post_alias . 'ringuser'] as $ring) {
            $rings = explode('_', $ring);
            #pr($rings);
            $arruser[$i] = $rings['0'];
            $arrsec[$i] = 'Ring User';
            $arrsecid[$i] = $rings['1'];
            $i++;
        }
        #pr($post['pgid']);
        /* foreach($post['pgid'] as $val)
          {
          foreach($post['pguser_'.$val] as $valpguser)
          {
          $arruser[$i]		= $valpguser;
          $arrsec[$i]			= 'Prayer Group';
          $arrsecid[$i]		= $val;
          $i++;
          }
          } */

        foreach ($post[$post_alias . 'pg'] as $pg) {
            $groups = explode('_', $pg);
            #pr($rings);
            $arruser[$i] = $groups['0'];
            $arrsec[$i] = 'Prayer Group';
            $arrsecid[$i] = $groups['1'];
            $i++;
        }
        #pr($arruser);
        #$arruser	= array_unique($arruser);
        $j = 0;
        foreach ($arruser as $val) {
            $arrinsert[$field] = $itemid;
            $arrinsert['i_user_id'] = $val;
            $arrinsert['s_section'] = $arrsec[$j];
            $arrinsert['i_section_id'] = $arrsecid[$j];
            $j++;

            $CI->db->insert($table, $arrinsert);
          
        }
		 // echo $CI->db->last_query();exit;
    }
}

function get_privacy($mode, $uid) {
    $CI = & get_instance();
    $i_profile_id = intval(decrypt($CI->session->userdata('user_id')));
    $res = $CI->db->query("SELECT * FROM {$CI->db->PRIVACY_SETTINGS} WHERE i_user_id='" . $uid . "' AND s_section_name='" . $mode . "'"); #echo $CI->db->last_query();
    $rst_arr = $res->result_array();
    #pr($rst_arr,1);
    if ($rst_arr) {
        if ($rst_arr[0]['i_friend_privacy'] == 0 && $rst_arr[0]['i_netpal_privacy'] == 0 && $rst_arr[0]['i_prayer_partner_privacy'] == 0 && $rst_arr[0]['i_ring_privacy'] == 0 && $rst_arr[0]['i_prayer_group_privacy'] == 0) {
            return 'Public';
        } else {
            return 'Private';
        }
    }
}

function get_photoid_with_privacy($uid, $profile_id = '') {
    $CI = & get_instance();

    ### get main privacy settings -> fetch accordingly 
    if ($profile_id != '') {
        $mp_sql = "  SELECT  if ((mp.i_friend_privacy = '0' &&  mp.i_netpal_privacy = '0' &&  mp.i_prayer_partner_privacy = '0' 
		 					&& mp.i_ring_privacy = '0' &&  mp.i_prayer_group_privacy = '0'
						    ) , '' ,
						       (concat(IF(mp.i_friend_privacy = '1', '\"Friend\", ', ''),''
		 							, IF(mp.i_netpal_privacy = '1', '\"Netpal\", ',''),'' 
									,IF(mp.i_prayer_partner_privacy = '1', '\"Prayer Partner\", ', '') , ''
									,IF(mp.i_ring_privacy = '1', '\"Ring User\", ', ''),''
									,IF(mp.i_prayer_group_privacy = '1', '\"Prayer Group\", ', '')))) as str
						FROM cg_privacy_settings mp 
						WHERE
						mp.i_user_id = '" . $profile_id . "' and mp.s_section_name = 'photo'";

        $res_data = $CI->db->query($mp_sql);
        $res_data_arr = $res_data->result_array();
        //pr($res_data_arr);
        //echo $res_data_arr[0]['str'].' #$#$'; 
        if ($res_data_arr[0]['str'] != '')
            $PRIVACY_STR = " AND pp.s_section IN  (" . substr($res_data_arr[0]['str'], 0, -2) . ")";
        else
            $PRIVACY_STR = '';



        $check_user_level_perm = "SELECT COUNT(pp.i_photo_album_id) as total FROM cg_photo_album AS pa 
										LEFT JOIN cg_user_photos AS up ON up.i_photo_album_id=pa.id 
										LEFT JOIN cg_photoalbum_privacy AS pp ON pp.i_photo_album_id=pa.id 
										WHERE pa.i_user_id= '" . $profile_id . "' ";

        $is_user_level = $CI->db->query($check_user_level_perm)->result_array();

        // echo 'total::: '.($is_user_level[0]['total']);
        ### if user level permission not given  i.e $is_user_level[0]['total'] =0  
        #then check for group level else check user level
        if ($is_user_level[0]['total'] == 0) {

            ## check user relationship if logged user has privacy permission of album 	
            $network_arr = CheckUserNetwork($profile_id);
            //pr($network_arr);

            $PRIVACY_ARR = explode(', ', substr($res_data_arr[0]['str'], 0, -2));
            //pr($PRIVACY_ARR);

            $privacy_result = array_intersect($network_arr, $PRIVACY_ARR);

            if (count($privacy_result)) {
                ### fetch all albums photos without user level permission i.e. only group level permission
                ### check 

                $s_qry = " SELECT pa.id, up.id AS photo_id 
							FROM cg_photo_album AS pa 
							LEFT JOIN cg_user_photos AS up ON up.i_photo_album_id=pa.id 
							WHERE pa.id not in (SELECT DISTINCT(pp.i_photo_album_id)
														FROM cg_photo_album AS pa1
														JOIN cg_photoalbum_privacy AS pp ON pp.i_photo_album_id = pa1.id  
														AND pa1.i_user_id = '" . $profile_id . "')
							AND pa.i_user_id = '" . $profile_id . "'
							AND (SELECT count(up1.id)
													FROM cg_user_photos AS up1
													WHERE up1.i_photo_album_id = pa.id
													) > 0 
						   ";

                // echo '## '.nl2br($s_qry);
                $rs = $CI->db->query($s_qry);  //pr($rs->result());

                foreach ($rs->result() as $row) {
                    $arr[] = $row->photo_id;
                }
            }
        } else { ### fetch all albums photos with user level permission	  
            $s_qry = "SELECT pa.id, up.id AS photo_id 
							FROM cg_photo_album AS pa 
							LEFT JOIN cg_user_photos AS up ON up.i_photo_album_id=pa.id 
							WHERE pa.id not in (SELECT DISTINCT(pp.i_photo_album_id)
														FROM cg_photo_album AS pa1
														JOIN cg_photoalbum_privacy AS pp ON pp.i_photo_album_id = pa1.id  
														AND pa1.i_user_id = '" . $profile_id . "')
							AND pa.i_user_id = '" . $profile_id . "' 
							AND (SELECT count(up1.id)
													FROM cg_user_photos AS up1
													WHERE up1.i_photo_album_id = pa.id
													) > 0 
							
							UNION 
							
							SELECT DISTINCT(pp.i_user_id) AS uid, up.id AS photo_id FROM 
							  {$CI->db->PHOTO_ALBUM} AS pa LEFT JOIN  
							  {$CI->db->USER_PHOTOS} AS up ON up.i_photo_album_id=pa.id 
							  LEFT JOIN " . $CI->db->photoalbum_privacy . " AS pp ON pp.i_photo_album_id=pa.id 
							  WHERE pp.i_user_id='" . $uid . "' AND pa.i_user_id = '" . $profile_id . "'
							  " . $PRIVACY_STR . "
							  AND (SELECT count(up1.id)
													FROM cg_user_photos AS up1
													WHERE up1.i_photo_album_id = pa.id
													) > 0 
							  
						  ";
            $rs = $CI->db->query($s_qry);  //pr($rs->result());

            foreach ($rs->result() as $row) {
                $arr[] = $row->photo_id;
            }
        }
    } //pr($arr);
    return $arr;
}

function get_videoid_with_privacy($uid, $profile_id = '') {

    $CI = & get_instance();

    ### get main privacy settings -> fetch accordingly 
    if ($profile_id != '') {
        $mp_sql = "  SELECT  if ((mp.i_friend_privacy = '0' &&  mp.i_netpal_privacy = '0' &&  mp.i_prayer_partner_privacy = '0' 
		 					&& mp.i_ring_privacy = '0' &&  mp.i_prayer_group_privacy = '0'
						    ) , '' ,
						       (concat(IF(mp.i_friend_privacy = '1', '\"Friend\", ', ''),''
		 							, IF(mp.i_netpal_privacy = '1', '\"Netpal\", ',''),'' 
									,IF(mp.i_prayer_partner_privacy = '1', '\"Prayer Partner\", ', '') , ''
									,IF(mp.i_ring_privacy = '1', '\"Ring User\", ', ''),''
									,IF(mp.i_prayer_group_privacy = '1', '\"Prayer Group\", ', '')))) as str
						FROM cg_privacy_settings mp 
						WHERE
						mp.i_user_id = '" . $profile_id . "' and mp.s_section_name = 'video'";

        $res_data = $CI->db->query($mp_sql);
        $res_data_arr = $res_data->result_array();
        #pr($res_data_arr);
        #echo $res_data_arr[0]['str'].' #$#$'; 
        if ($res_data_arr[0]['str'] != '')
            $PRIVACY_STR = " AND pp.s_section IN  (" . substr($res_data_arr[0]['str'], 0, -2) . ")";
        else
            $PRIVACY_STR = '';



        $check_user_level_perm = "SELECT COUNT(pp.i_video_album_id) as total FROM cg_video_album AS pa 
										LEFT JOIN {$CI->db->USER_VIDEOS} AS up ON up.i_video_album_id=pa.id 
										LEFT JOIN {$CI->db->videolbum_privacy} AS pp ON pp.i_video_album_id=pa.id 
										WHERE pa.i_user_id= '" . $profile_id . "' ";

        $is_user_level = $CI->db->query($check_user_level_perm)->result_array();

        #echo 'total::: '.($is_user_level[0]['total']);
        #### if public no restriction show all 
        if ($res_data_arr[0]['str'] == '') {

            $s_qry = " SELECT up.id as photo_id FROM {$CI->db->USER_VIDEOS} AS up
								  WHERE  up.i_user_id =" . $profile_id . "  ORDER BY id DESC 
			   ";
            $rs = $CI->db->query($s_qry);  //pr($rs->result());

            foreach ($rs->result() as $row) {
                $arr[] = $row->photo_id;
            }
        }
        ### if user level permission not given  i.e $is_user_level[0]['total'] =0  
        #then check for group level else check user level
        else if ($is_user_level[0]['total'] == 0) {

            ## check user relationship if logged user has privacy permission of album 	
            $network_arr = CheckUserNetwork($profile_id);
            //pr($network_arr);

            $PRIVACY_ARR = explode(', ', substr($res_data_arr[0]['str'], 0, -2));
            //pr($PRIVACY_ARR);

            $privacy_result = array_intersect($network_arr, $PRIVACY_ARR);

            if (count($privacy_result)) {
                ### fetch all albums photos without user level permission i.e. only group level permission
                ### check 

                $s_qry = " SELECT pa.id, up.id AS photo_id 
							FROM cg_video_album AS pa 
							LEFT JOIN {$CI->db->USER_VIDEOS} AS up ON up.i_video_album_id=pa.id 
							WHERE pa.id not in (SELECT DISTINCT(pp.i_video_album_id)
														FROM cg_video_album AS pa1
														JOIN {$CI->db->videolbum_privacy} AS pp ON pp.i_video_album_id = pa1.id  
														AND pa1.i_user_id = '" . $profile_id . "')
							AND pa.i_user_id = '" . $profile_id . "'
							AND (SELECT count(up1.id)
													FROM {$CI->db->USER_VIDEOS} AS up1
													WHERE up1.i_video_album_id = pa.id
													) > 0 
						   ";

                // echo '## '.nl2br($s_qry);
                $rs = $CI->db->query($s_qry);  //pr($rs->result());

                foreach ($rs->result() as $row) {
                    $arr[] = $row->photo_id;
                }
            }
        } else { ### fetch all albums photos with user level permission	  
            $s_qry = "SELECT pa.id, up.id AS photo_id 
							FROM cg_video_album AS pa 
							LEFT JOIN {$CI->db->USER_VIDEOS} AS up ON up.i_video_album_id=pa.id 
							WHERE pa.id not in (SELECT DISTINCT(pp.i_video_album_id)
														FROM cg_video_album AS pa1
														JOIN {$CI->db->videolbum_privacy} AS pp ON pp.i_video_album_id = pa1.id  
														AND pa1.i_user_id = '" . $profile_id . "')
							AND pa.i_user_id = '" . $profile_id . "' 
							AND (SELECT count(up1.id)
													FROM {$CI->db->USER_VIDEOS} AS up1
													WHERE up1.i_video_album_id = pa.id
													) > 0 
							
							UNION 
							
							SELECT DISTINCT(pp.i_user_id) AS uid, up.id AS photo_id FROM 
							   cg_video_album AS pa LEFT JOIN  
							  {$CI->db->USER_VIDEOS} AS up ON up.i_video_album_id=pa.id 
							  LEFT JOIN " . $CI->db->videolbum_privacy . " AS pp ON pp.i_video_album_id=pa.id 
							  WHERE pp.i_user_id='" . $uid . "'  AND pa.i_user_id = '" . $profile_id . "'
							  " . $PRIVACY_STR . "
							  AND (SELECT count(up1.id)
													FROM {$CI->db->USER_VIDEOS} AS up1
													WHERE up1.i_video_album_id = pa.id
													) > 0 
							  
						  ";

            # echo nl2br($s_qry); 
            $rs = $CI->db->query($s_qry);  //pr($rs->result());

            foreach ($rs->result() as $row) {
                $arr[] = $row->photo_id;
            }
        }
    } //pr($arr);
    return $arr;
}

/* function get_videoid_with_privacy($uid, $profile_id='')
  {
  $CI = & get_instance();

  ### get main privacy settings -> fetch accordingly
  if($profile_id != ''){
  $mp_sql = "  SELECT  if ((mp.i_friend_privacy = '0' &&  mp.i_netpal_privacy = '0' &&  mp.i_prayer_partner_privacy = '0' &&
  mp.i_ring_privacy = '0' &&  mp.i_prayer_group_privacy = '0'
  ) , '' ,
  (concat(IF(mp.i_friend_privacy = '1', '\"Friend\", ', ''),''
  , IF(mp.i_netpal_privacy = '1', '\"Netpal\", ',''),''
  ,IF(mp.i_prayer_partner_privacy = '1', '\"Prayer Partner\", ', '') , ''
  ,IF(mp.i_ring_privacy = '1', '\"Ring User\", ', ''),''
  ,IF(mp.i_prayer_group_privacy = '1', '\"Prayer Group\", ', '')))) as str
  FROM cg_privacy_settings mp
  WHERE
  mp.i_user_id = '".$profile_id."' and mp.s_section_name = 'video'" ;

  $res_data = $CI->db->query($mp_sql);
  $res_data_arr = $res_data->result_array();
  //pr($res_data_arr);

  if($res_data_arr[0]['str'] != '')
  $PRIVACY_STR = " AND pp.s_section IN  (".substr($res_data_arr[0]['str'], 0, -2).")";
  else
  $PRIVACY_STR = '';

  $s_qry =  "SELECT DISTINCT(pp.i_user_id) AS uid, up.id AS video_id FROM "
  .$CI->db->videolbum_privacy. " AS pp LEFT JOIN
  {$CI->db->VIDEO_ALBUM} AS pa ON pp.i_video_album_id=pa.id LEFT JOIN
  {$CI->db->USER_VIDEOS} AS up ON up.i_video_album_id=pa.id WHERE pp.i_user_id='".$uid."'
  ".$PRIVACY_STR."
  ";
  // echo $s_qry;
  $rs=$CI->db->query($s_qry);

  foreach($rs->result() as $row)
  {
  $arr[]	= $row->video_id;
  }
  }
  return $arr;
  }
  function get_audioid_with_privacy($uid, $profile_id='')
  {
  $CI = & get_instance();
  if($profile_id != ''){
  $mp_sql = "  SELECT  if ((mp.i_friend_privacy = '0' &&  mp.i_netpal_privacy = '0' &&  mp.i_prayer_partner_privacy = '0'
  && mp.i_ring_privacy = '0' &&  mp.i_prayer_group_privacy = '0'
  ) , '' ,
  (concat(IF(mp.i_friend_privacy = '1', '\"Friend\", ', ''),''
  , IF(mp.i_netpal_privacy = '1', '\"Netpal\", ',''),''
  ,IF(mp.i_prayer_partner_privacy = '1', '\"Prayer Partner\", ', '') , ''
  ,IF(mp.i_ring_privacy = '1', '\"Ring User\", ', ''),''
  ,IF(mp.i_prayer_group_privacy = '1', '\"Prayer Group\", ', '')))) as str
  FROM cg_privacy_settings mp
  WHERE
  mp.i_user_id = '".$profile_id."' and mp.s_section_name = 'audio'" ;

  $res_data = $CI->db->query($mp_sql);
  $res_data_arr = $res_data->result_array();
  //pr($res_data_arr);

  if($res_data_arr[0]['str'] != '')
  $PRIVACY_STR = " AND pp.s_section IN  (".substr($res_data_arr[0]['str'], 0, -2).")";
  else
  $PRIVACY_STR = '';

  $s_qry =  "SELECT DISTINCT(pp.i_user_id) AS uid, up.id AS audio_id FROM "
  .$CI->db->audioalbum_privacy. " AS pp LEFT JOIN
  {$CI->db->AUDIO_ALBUM} AS pa ON pp.i_audio_album_id=pa.id LEFT JOIN
  {$CI->db->USER_AUDIO} AS up ON up.i_id_audio_album=pa.id WHERE pp.i_user_id='".$uid."'
  ".$PRIVACY_STR."
  ";
  #echo $s_qry;
  $rs=$CI->db->query($s_qry);

  foreach($rs->result() as $row)
  {
  $arr[]	= $row->audio_id;
  }
  }
  return $arr;
  } */

function get_audioid_with_privacy($uid, $profile_id = '') {

    $CI = & get_instance();

    ### get main privacy settings -> fetch accordingly 
    if ($profile_id != '') {
        $mp_sql = "  SELECT  if ((mp.i_friend_privacy = '0' &&  mp.i_netpal_privacy = '0' &&  mp.i_prayer_partner_privacy = '0' 
		 					&& mp.i_ring_privacy = '0' &&  mp.i_prayer_group_privacy = '0'
						    ) , '' ,
						       (concat(IF(mp.i_friend_privacy = '1', '\"Friend\", ', ''),''
		 							, IF(mp.i_netpal_privacy = '1', '\"Netpal\", ',''),'' 
									,IF(mp.i_prayer_partner_privacy = '1', '\"Prayer Partner\", ', '') , ''
									,IF(mp.i_ring_privacy = '1', '\"Ring User\", ', ''),''
									,IF(mp.i_prayer_group_privacy = '1', '\"Prayer Group\", ', '')))) as str
						FROM cg_privacy_settings mp 
						WHERE
						mp.i_user_id = '" . $profile_id . "' and mp.s_section_name = 'audio'";

        $res_data = $CI->db->query($mp_sql);
        $res_data_arr = $res_data->result_array();
        #pr($res_data_arr);
        #echo $res_data_arr[0]['str'].' #$#$'; 
        if ($res_data_arr[0]['str'] != '')
            $PRIVACY_STR = " AND pp.s_section IN  (" . substr($res_data_arr[0]['str'], 0, -2) . ")";
        else
            $PRIVACY_STR = '';



        $check_user_level_perm = "SELECT COUNT(pp.i_audio_album_id) as total FROM cg_audio_album AS pa 
										LEFT JOIN {$CI->db->USER_AUDIO} AS up ON up.i_id_audio_album=pa.id 
										LEFT JOIN cg_audioalbum_privacy AS pp ON pp.i_audio_album_id=pa.id 
										WHERE pa.i_user_id= '" . $profile_id . "' ";

        $is_user_level = $CI->db->query($check_user_level_perm)->result_array();

        #echo 'total::: '.($is_user_level[0]['total']);
        #### if public no restriction show all 
        if ($res_data_arr[0]['str'] == '') {

            $s_qry = " SELECT up.id as photo_id FROM {$CI->db->USER_AUDIO} AS up
								  WHERE  up.i_user_id =" . $profile_id . "  ORDER BY id DESC 
			   ";
            $rs = $CI->db->query($s_qry);  //pr($rs->result());

            foreach ($rs->result() as $row) {
                $arr[] = $row->photo_id;
            }
        }
        ### if user level permission not given  i.e $is_user_level[0]['total'] =0  
        #then check for group level else check user level
        else if ($is_user_level[0]['total'] == 0) {

            ## check user relationship if logged user has privacy permission of album 	
            $network_arr = CheckUserNetwork($profile_id);
            //pr($network_arr);

            $PRIVACY_ARR = explode(', ', substr($res_data_arr[0]['str'], 0, -2));
            //pr($PRIVACY_ARR);

            $privacy_result = array_intersect($network_arr, $PRIVACY_ARR);

            if (count($privacy_result)) {
                ### fetch all albums photos without user level permission i.e. only group level permission
                ### check 

                $s_qry = " SELECT pa.id, up.id AS photo_id 
							FROM cg_audio_album AS pa 
							LEFT JOIN {$CI->db->USER_AUDIO} AS up ON up.i_id_audio_album=pa.id 
							WHERE pa.id not in (SELECT DISTINCT(pp.i_audio_album_id)
														FROM cg_audio_album AS pa1
														JOIN cg_audioalbum_privacy AS pp ON pp.i_audio_album_id = pa1.id  
														AND pa1.i_user_id = '" . $profile_id . "')
							AND pa.i_user_id = '" . $profile_id . "'
							AND (SELECT count(up1.id)
													FROM {$CI->db->USER_AUDIO} AS up1
													WHERE up1.i_id_audio_album = pa.id
													) > 0 
						   ";

                // echo '## '.nl2br($s_qry);
                $rs = $CI->db->query($s_qry);  //pr($rs->result());

                foreach ($rs->result() as $row) {
                    $arr[] = $row->photo_id;
                }
            }
        } else { ### fetch all albums photos with user level permission	  
            $s_qry = "SELECT pa.id, up.id AS photo_id 
							FROM cg_audio_album AS pa 
							LEFT JOIN {$CI->db->USER_AUDIO} AS up ON up.i_id_audio_album=pa.id 
							WHERE pa.id not in (SELECT DISTINCT(pp.i_audio_album_id)
														FROM cg_video_album AS pa1
														JOIN cg_audioalbum_privacy AS pp ON pp.i_audio_album_id = pa1.id  
														AND pa1.i_user_id = '" . $profile_id . "')
							AND pa.i_user_id = '" . $profile_id . "' 
							AND (SELECT count(up1.id)
													FROM {$CI->db->USER_AUDIO} AS up1
													WHERE up1.i_id_audio_album = pa.id
													) > 0 
							
							UNION 
							
							SELECT DISTINCT(pp.i_user_id) AS uid, up.id AS photo_id FROM 
							   cg_audio_album AS pa LEFT JOIN  
							  {$CI->db->USER_AUDIO} AS up ON up.i_id_audio_album=pa.id 
							  LEFT JOIN cg_audioalbum_privacy AS pp ON pp.i_audio_album_id=pa.id 
							  WHERE pp.i_user_id='" . $uid . "'  AND pa.i_user_id = '" . $profile_id . "'
							  " . $PRIVACY_STR . "
							  AND (SELECT count(up1.id)
													FROM {$CI->db->USER_AUDIO} AS up1
													WHERE up1.i_id_audio_album = pa.id
													) > 0 
							  
						  ";

            # echo nl2br($s_qry); 
            $rs = $CI->db->query($s_qry);  //pr($rs->result());

            foreach ($rs->result() as $row) {
                $arr[] = $row->photo_id;
            }
        }
    } //pr($arr);
    return $arr;
}

function getMembers_ring_bYID($ring_id) {
    $CI = & get_instance();
    $CI->load->model('my_ring_model');
    $arr = $CI->my_ring_model->get_all_ring_members_by_ring_id($ring_id);

    if (count($arr)) {
        $member_arr = array();
        foreach ($arr as $k => $val) {
            $member_arr[$k] = $val['i_invited_id'];
        }
    }
    return $member_arr;
}

function get_invitation($mode, $selecteduser = array()) {
    $CI = & get_instance();

    $CI->load->model('contacts_model');
    $CI->load->model('netpals_model');
    $CI->load->model('my_prayer_partner_model');
    $CI->load->model('prayer_group_model');
    $i_profile_id = intval(decrypt($CI->session->userdata('user_id')));
    $WHERE_frnd = " WHERE 
									1
									AND c.s_status = 'accepted' 
									AND u.i_status=1 
									AND
									((c.i_requester_id = '" . $i_profile_id . "' AND u.id=c.i_accepter_id ) 
									OR (c.i_accepter_id = '" . $i_profile_id . "' AND u.id=c.i_requester_id )) GROUP BY u.id ";
    $arr_friends = $CI->contacts_model->fetch_multi_online_friends($WHERE_frnd);
    #pr($arr_friends);
    $showfrnd = '';
    if (is_array($arr_friends)) {
        $showfrnd = 'style="display:none"';

        $strfrnds = '<h4 class="privacy-ring-blue_bold12" ><a href="javascript:void(0);">Friends</a>  </h4>';
        $strfrnds .= '<div class="clr"></div><select id="frndinv" multiple="multiple" style="width:370px" name="frndinv[]">';
        foreach ($arr_friends as $val) {


            if (!in_array($val['user_id'], $selecteduser['Friend'])) {
                $check_frnd = '';
                $strfrnds .= '<option value="' . $val['user_id'] . '" ' . $check_frnd . '>' . $val['s_first_name'] . ' ' . $val['s_last_name'] . '</option>';
            }
        }
    }
    $strfrnds .= '</select><div class="clr"></div>';
    $WHERE_netpal = " WHERE 
										1
										AND u.i_status=1 
										AND ((c.i_requester_id = " . $i_profile_id . " AND u.id=c.i_accepter_id) 
											OR 
										(c.i_accepter_id=" . $i_profile_id . " AND u.id=c.i_requester_id))
										AND c.s_status='accepted' " . $add_where . " GROUP BY u.id ";

    $arr_netpal = $CI->netpals_model->fetch_multi_online_netpals($WHERE_netpal);

    $shownetpal = '';
    if (count($arr_netpal) > 0) {
        $shownetpal = 'style="display:none"';

        $strnetpal = '<h4 class="privacy-ring-blue_bold12" ><a href="javascript:void(0);">Netpal</a>  </h4>';
        $strnetpal .= '<div class="clr"></div><select id="netpalinv" multiple="multiple" style="width:370px" name="netpalinv[]">';
        foreach ($arr_netpal as $val) {
            if (!in_array($val['user_id'], $selecteduser['Netpal'])) {
                $strnetpal .= '<option value="' . $val['user_id'] . '" ' . $check_netpal . '>' . $val['s_first_name'] . ' ' . $val['s_last_name'] . '</option>';
            }
        }
    }
    $strnetpal .= '</select><div class="clr"></div>';
    $WHERE_pp = " WHERE 
									1
									AND c.s_status = 'accepted' 
									AND u.i_status=1 
									AND
									((c.i_requester_id = '" . $i_profile_id . "' AND u.id=c.i_accepter_id ) 
									OR (c.i_accepter_id = '" . $i_profile_id . "' AND u.id=c.i_requester_id ))  GROUP BY u.id ";
    $arr_pp = $CI->my_prayer_partner_model->fetch_multi_online_friends($WHERE_pp);

    $strpp = '';

    $showpp = '';
    if (count($arr_pp) > 0) {
        $showpp = 'style="display:none"';

        $strpp = '<h4 class="privacy-ring-blue_bold12"><a href="javascript:void(0);">Prayer Partner</a>  </h4>';
        $strpp .= '<div class="clr"></div><select id="ppinv" multiple="multiple" style="width:370px" name="ppinv[]">';
        foreach ($arr_pp as $val) {
            if (!in_array($val['user_id'], $selecteduser['Prayer Partner'])) {
                $strpp .= '<option value="' . $val['user_id'] . '" ' . $check_pp . '>' . $val['s_first_name'] . ' ' . $val['s_last_name'] . '</option>';
            }
        }
    }
    $strpp .= '</select><div class="clr"></div>';

    if ($mode == 'ring') {

        $show_pg = '';
        if (count($selecteduser['Prayer Group']) > 0)
            $show_pg = 'style="display:none;"';


        $arr_pg = $CI->prayer_group_model->get_grpids_u_id($i_profile_id); //explode(', ',$privacyval['i_prayer_group_privacy']);

        if (count($arr_pg) == '0') {
            $strpg = '<h4 class="privacy-ring-blue_bold12"><a href="javascript:void(0);">Prayer Group</a></h4>';

            $strpg .= "You don't have any prayer group<div class='clr'></div>";
        } else {
            $strpg.='<h4 class="privacy-ring-blue_bold12"><a href="javascript:void(0);">Prayer Group</a></h4>';
            $strpg .= '<div class="clr"></div><select id="pginv" multiple="multiple" style="width:370px" name="pginv[]>';

            //variable to complete the count of <ul>
            $pgfirstcount = 0;
            foreach ($arr_pg as $prayer_group_id) {
                $arr_pg_user = $CI->prayer_group_model->get_members($prayer_group_id, $i_profile_id);

                $tmp_pg = '';

                foreach ($arr_pg_user as $val) {

                    ###########   variable to show prayer group users at the time of edit
                    $show_pg_user = '';
                    if (count($selecteduser['Prayer Group'][$val['i_prayer_group_id']]) > 0)
                        $show_pg_user = 'style="display:none;"';

                    if ($tmp_pg != $val['s_group_name']) {
                        if ($pgfirstcount > 0)
                            $strpg .= '</optgroup>';

                        //$strpg	 	.=  '<div class="clr"></div>';
                        $tmp_pg = $val['s_group_name'];
                        $strpg .= '<div class="clr"></div><optgroup label="' . $val['s_group_name'] . '">';
                        $strpg .= '<div class="clr"></div>';
                        //$strpg	 	.=  '<input type="hidden" name="pgid[]" id="pg" value="'.$val['i_prayer_group_id'].'">';
                    }
                    $pgfirstcount++;
                    if (in_array($val['i_user_id'], $selecteduser['Prayer Group'][$val['i_prayer_group_id']])) {
                        
                    }
                    /* $check_pguser	= 'checked="checked"'; */ else {
                        $strpg .= '<option  value="' . $val['i_user_id'] . '_' . $val['i_prayer_group_id'] . '">' . $val['s_profile_name'] . '</option>';
                    }
                }
            }

            $strpg .= '</select><div class="clr"></div>';
        }
    }
    return $strfrnds . $strnetpal . $strpp . $strpg;
}

function insert_invitation($itemid, $post, $table, $field, $mode, $group_id) {
    $CI = & get_instance();


    //echo $field;
    //echo $post['pp'];
    $i = 0;
    if ($mode == 'chat') {




        foreach ($post['frnds'] as $val) {
            $arruser[$i] = $val;
            $arrsec[$i] = 'Friend';
            $i++;
        }
        //$netpal=array_filter(explode(',',$post['netpalinv']));

        foreach ($post['netpals'] as $val) {
            $arruser[$i] = $val;
            $arrsec[$i] = 'Netpal';
            $i++;
        }
        //$pp = array_filter(explode(',',$post['ppinv']));
        foreach ($post['pp'] as $val) {
            $arruser[$i] = $val;
            $arrsec[$i] = 'Prayer Partner';
            $i++;
        }
    } else if ($mode == 'prayer_group') {
        $arr_frnd = array();
        $arr_netpal = array();
        $arr_pp = array();
        $arr_ringuser = array();
        $arr_frnd = $post['frnd_type1'];
        //$arr_frnd=array_filter(explode(',',$inv_frnds));
        $arr_netpal = $post['frnd_type2'];
        //$arr_netpal=array_filter(explode(',',$inv_netpal));
        $arr_pp = $post['frnd_type3'];
        //$arr_pp=array_filter(explode(',',$inv_frnds));
        $arr_ring = $post['frnd_type4'];
        //$arr_ring=array_filter(explode('_',$inv_ring));
        $invited_people = get_invitation_by_group_id($group_id);
        #pr($invited_people);
        foreach ($arr_frnd as $val) {
            if (!in_array($val['i_user_id'], $invited_people['Friend'])) {
                $arruser[$i] = $val;
                $arrsec[$i] = 'Friend';
                $i++;
            }
        }

        foreach ($arr_netpal as $val) {
            if (!in_array($val['i_user_id'], $invited_people['Netpal'])) {
                $arruser[$i] = $val;
                $arrsec[$i] = 'Netpal';
                $i++;
            }
        }

        foreach ($arr_pp as $val) {
            if (!in_array($val['i_user_id'], $invited_people['Prayer Partner'])) {
                $arruser[$i] = $val;
                $arrsec[$i] = 'Prayer Partner';
                $i++;
            }
        }
        foreach ($arr_ring as $val) {

            $ring = explode('_', $val);
            if (!in_array($ring['0'], $invited_people['Ring User'])) {
                $arruser[$i] = $ring['0'];
                $arrsec[$i] = 'Ring User';
                $arrsecid[$i] = $ring['1'];
                $i++;
            }
        }
    } else {
        #echo '11'.$table;
        foreach ($post['frndinv'] as $val) {
            $arruser[$i] = $val;
            $arrsec[$i] = 'Friend';
            $i++;
        }
        #pr($post['frndinv']);
        foreach ($post['netpalinv'] as $val) {
            $arruser[$i] = $val;
            $arrsec[$i] = 'Netpal';
            $i++;
        }

        foreach ($post['ppinv'] as $val) {
            $arruser[$i] = $val;
            $arrsec[$i] = 'Prayer Partner';
            $i++;
        }
        foreach ($post['pginv'] as $val) {
            $arr_p = explode('_', $val);
            $arruser[$i] = $arr_p['0'];
            $arrsec[$i] = 'Prayer Group';
            $arrsecid[$i] = $arr_p['1'];
            $i++;
        }
    }
    #pr($arruser);
    #$arruser	= array_unique($arruser);
    $j = 0;
    foreach ($arruser as $val) {
        $arrinsert[$field] = $itemid;
        $arrinsert['i_user_id'] = $val;
        $arrinsert['s_section'] = $arrsec[$j];
        $arrinsert['i_section_id'] = $arrsecid[$j];
        $j++;
        //echo $table;	
        $CI->db->insert($table, $arrinsert);

        $CI->db->last_query();
    }
}


function get_churchinvitation($mode, $selecteduser = array()) {
    $CI = & get_instance();

    $CI->load->model('church_new_model');
   
    $i_profile_id = intval(decrypt($CI->session->userdata('user_id')));
    $c_id = $_SESSION['logged_church_id'];
    $arr_friends = $CI->church_new_model->get_churchmembers($c_id,' AND cm.is_approved=1 ','',' cm.id DESC ','');
    #pr($arr_friends);
    $showfrnd = '';
    if (is_array($arr_friends)) {
        $showfrnd = 'style="display:none"';

       
        $strfrnds = '<div class="clr"></div><select id="frndinv" multiple="multiple" style="width:370px" name="frndinv[]">';
        foreach ($arr_friends as $val) {


            if (!in_array($val->mid, $selecteduser['Friend'])) {
                $check_frnd = '';
               $strfrnds .= '<option value="' . $val->mid . '" ' . $check_frnd . '>' . $val->s_first_name . ' ' . $val->s_last_name . '</option>';
               // $strfrnds .= '<option value="' . $i_profile_id . '" ' . $check_frnd . '>' . $val->s_first_name . ' ' . $val->s_last_name . '</option>';
            }
        }
    }
    $strfrnds .= '</select><div class="clr"></div>';
   
   
    return $strfrnds ;
}
# function to get user-name by ID...

function get_unique_username_by_id($i_user_id = NULL) {
    try {
        $o_ci = get_instance();
        $o_ci->db->select("s_profile_url_suffix");
        $o_ci->db->where('id', $i_user_id);

        $arr_qry = $o_ci->db->get('users');
        $ret_ = $arr_qry->row_array();
        //echo $o_ci->db->last_query();

        return $ret_["s_profile_url_suffix"];
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

# function to get user-name by ID...

function get_unique_chatname_by_id($i_user_id = NULL) {
    try {
        $o_ci = get_instance();
        $o_ci->db->select("s_chat_display_name");
        $o_ci->db->where('id', $i_user_id);

        $arr_qry = $o_ci->db->get('users');
        $ret_ = $arr_qry->row_array();
        //echo $o_ci->db->last_query();

        return $ret_["s_chat_display_name"];
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function get_ring_category_name_by_id($id) {

    $ci = get_instance();
    $res = $ci->db->query("SELECT s_category_name
								 FROM {$ci->db->RING_CAT} AS subcat WHERE  subcat.id = '{$id}'");
    $info = $res->result_array();
    return $info[0]['s_category_name'];
}

##prayer group invitation

function get_invitation_prayer_group($mode, $id, $selecteduser = array()) {
    $CI = & get_instance();

    $CI->load->model('contacts_model');
    $CI->load->model('netpals_model');
    $CI->load->model('my_prayer_partner_model');
    $CI->load->model('prayer_group_model');
    $CI->load->model('my_ring_model');
    $i_profile_id = intval(decrypt($CI->session->userdata('user_id')));
    $WHERE_frnd = " WHERE 
									1
									AND c.s_status = 'accepted' 
									AND u.i_status=1 
									AND
									((c.i_requester_id = '" . $i_profile_id . "' AND u.id=c.i_accepter_id ) 
									OR (c.i_accepter_id = '" . $i_profile_id . "' AND u.id=c.i_requester_id )) GROUP BY u.id ";
    $arr_friends = $CI->contacts_model->fetch_multi_online_friends($WHERE_frnd);
    #pr($arr_friends);
    $showfrnd = '';
    if (is_array($arr_friends)) {
        $showfrnd = 'style="display:none"';

        $strfrnds = '<h4 class="privacy-ring-blue_bold12"><a href="javascript:void(0);">Friends</a> </h4>';
        $strfrnds .= '<div class="clr"></div><select id="frndinv" multiple="multiple" style="width:370px" name="frndinv[]">';
        foreach ($arr_friends as $val) {
            $members = get_members($id, $val['user_id']);

            if ($members != 'accepted') {
                if (in_array($val['user_id'], $selecteduser['Friend'])) {
                    $check_frnd = '';
                    $strfrnds .= '<option value="' . $val['user_id'] . '" >' . $val['s_first_name'] . ' ' . $val['s_last_name'] . '<img src="images/mail-refresh.png" alt="Resend" height="14px" width="12px"></option> ';
                } else {
                    $strfrnds .= '<option value="' . $val['user_id'] . '" >' . $val['s_first_name'] . ' ' . $val['s_last_name'] . '</option>';
                }
            }
        }
    }
    $strfrnds .= '</select><div class="clr"></div>';
    $WHERE_netpal = " WHERE 
										1
										AND u.i_status=1 
										AND ((c.i_requester_id = " . $i_profile_id . " AND u.id=c.i_accepter_id) 
											OR 
										(c.i_accepter_id=" . $i_profile_id . " AND u.id=c.i_requester_id))
										AND c.s_status='accepted' " . $add_where . " GROUP BY u.id ";

    $arr_netpal = $CI->netpals_model->fetch_multi_online_netpals($WHERE_netpal);

    $shownetpal = '';
    if (count($arr_netpal) > 0) {
        $shownetpal = 'style="display:none"';

        $strnetpal = '<h4 class="privacy-ring-blue_bold12"><a href="javascript:void(0);">Netpal</a> </h4>';
        $strnetpal .= '<div class="clr"></div><select id="netpalinv" multiple="multiple" style="width:370px" name="netpalinv[]">';
        foreach ($arr_netpal as $val) {
            $members = get_members($id, $val['user_id']);

            if ($members != 'accepted') {
                if (in_array($val['user_id'], $selecteduser['Netpal'])) {
                    $strnetpal .= '<option value="' . $val['user_id'] . '" >' . $val['s_first_name'] . ' ' . $val['s_last_name'] . '<img src="images/mail-refresh.png" alt="Resend" height="14px" width="12px"></option>';
                } else {
                    $strnetpal .= '<option value="' . $val['user_id'] . '" >' . $val['s_first_name'] . ' ' . $val['s_last_name'] . '</option> ';
                }
            }
        }
    }
    $strnetpal .= '</select><div class="clr"></div>';
    $WHERE_pp = " WHERE 
									1
									AND c.s_status = 'accepted' 
									AND u.i_status=1 
									AND
									((c.i_requester_id = '" . $i_profile_id . "' AND u.id=c.i_accepter_id ) 
									OR (c.i_accepter_id = '" . $i_profile_id . "' AND u.id=c.i_requester_id ))  GROUP BY u.id ";
    $arr_pp = $CI->my_prayer_partner_model->fetch_multi_online_friends($WHERE_pp);

    $strpp = '';

    $showpp = '';
    if (count($arr_pp) > 0) {
        $showpp = 'style="display:none"';

        $strpp = '<h4 class="privacy-ring-blue_bold12"><a href="javascript:void(0);">Prayer Partner</a>  </h4>';
        $strpp .= '<div class="clr"></div><select id="ppinv" multiple="multiple" style="width:370px" name="ppinv[]">';
        foreach ($arr_pp as $val) {
            $members = get_members($id, $val['user_id']);

            if ($members != 'accepted') {
                if (in_array($val['user_id'], $selecteduser['Prayer Partner'])) {
                    $strpp .= '<option value="' . $val['user_id'] . '" >' . $val['s_first_name'] . ' ' . $val['s_last_name'] . '  <img src="images/mail-refresh.png" alt="Resend" height="14px" width="12px"> </option>';
                } else {
                    $strpp .= '<option value="' . $val['user_id'] . '" >' . $val['s_first_name'] . ' ' . $val['s_last_name'] . '</option>';
                }
            }
        }
    }
    $strpp .= '</select><div class="clr"></div>';
    ############            RING         #################
    //$strpp		.= '</select><div class="clr"></div>';
    //###########variable to show Ring at the time of edit
    $show_ring = '';
    if (count($selecteduser['Ring User']) > 0)
        $show_ring = 'style="display:block;"';


    $arr_ring = $CI->my_ring_model->get_all_ring_added_by_loggedin_user($i_profile_id);

    $strring = '<h4 class="privacy-ring-blue_bold12" ><a href="javascript:void(0);">Ring</a></h4>';
    $strring .= '<div class="clr"></div><select id="ringinv" multiple="multiple" style="width:370px" name="ringinv[]">';

    //variable to complete the count of <ul>
    $ringfirstcount = 0;
    foreach ($arr_ring as $ringid) {
        $arr_ring_user = $CI->my_ring_model->get_all_ring_members_in_table_by_ring_id($ringid, '', '', ' AND I.i_joined=1');
        $tmpring = '';
        #pr($arr_ring_user);
        foreach ($arr_ring_user as $val) {
            $members = get_members($id, $val['i_invited_id']);

            if ($members != 'accepted') {
                ###########   variable to show prayer group users at the time of edit
                $show_ring_user = '';
                if (count($selecteduser['Ring User'][$val['id']]) > 0)
                    $show_ring_user = 'style="display:block;"';

                if ($tmpring != $val['s_ring_name']) {
                    if ($ringfirstcount > 0)
                        $strring .= '</optgroup>';

                    //$strring	 .= '<div class="clr"></div>';
                    $tmpring = $val['s_ring_name'];
                    $strring .= '<optgroup label="' . $val['s_ring_name'] . '"  value="' . $val['id'] . '">';
                    //$strring	 .= '<input type="hidden" name="ringid[]" value="'.$val['id'].'">';
                }
                $ringfirstcount++;
                if (in_array($val['i_invited_id'], $selecteduser['Ring User'][$val['id']]))
                    $strring .= '<option  value="' . $val['i_invited_id'] . '_' . $val['id'] . '" ' . $check_ringuser . '>' . $val['profile_name'] . '</option><img src="images/mail-refresh.png" alt="Resend" height="14px" width="12px"> ';
                else

                /* $strring	.= '<option name="ringuser_'.$val['id'].'[]" value="'.$val['i_invited_id'].'" '.$check_ringuser.'>'.$val['profile_name'].'</option>'; */
                    $strring .= '<option  value="' . $val['i_invited_id'] . '_' . $val['id'] . '">' . $val['profile_name'] . '</option>';
            }
        }
    }

    #exit;
    ############            PRAYER GROUP         #################
    $strring .= '</select><div class="clr"></div>';
    return $strfrnds . $strnetpal . $strpp . $strring;
}

function get_invitation_by_group_id($i_group_id) {

    $ci = get_instance();
    $ci->load->model('prayer_group_model');

    $s_qry = "SELECT * FROM "
            . $ci->db->prayer_group_invitation . " WHERE i_prayer_group_id =" . $i_group_id;
    $rs = $ci->db->query($s_qry);
    //echo $ci->db->last_query();
    $result = $rs->result_array();
    #pr($result);
    foreach ($result as $row) {

        if ($row['s_section'] == 'Ring User' || $row['s_section'] == 'Prayer Group') {
            $returnarr[$row['s_section']][$row['i_section_id']][] = $row['i_user_id'];
        } else {

            $returnarr[$row['s_section']][] = $row['i_user_id'];
        }
    }
    #pr($returnarr);

    return $returnarr;
}

function get_invited($id, $table, $field) {
    $ci = get_instance();
    $ci->load->model('my_ring_model');
    $query = "SELECT i_user_id as user_id FROM  " . $table . " WHERE " . $field . "=" . $id . " GROUP BY i_user_id";
    #echo $query;
    $result = $ci->db->query($query);
    #pr($result);
    $result_arr = $result->result_array();
    #pr($result_arr);
    return $result_arr;
}

function get_members($group_id, $user_id) {
    $ci = get_instance();
    $query = "SELECT s_status from cg_prayer_group_members WHERE i_prayer_group_id=" . $group_id . " AND i_user_id=" . $user_id;
    $q1 = $ci->db->query($query);
    #echo $query;
    $q = $q1->result_array();
    #pr($q['0']['s_status']);
    return $q['0']['s_status'];
}

function CheckUserNetwork($to_user_id) {

    $CI = get_instance();
    $CI->load->model('users_model');
    $CI->load->model('my_prayer_partner_model');
    $CI->load->model('netpals_model');
    $CI->load->model('my_ring_model');
    $CI->load->model('prayer_group_model');


    $logged_user = intval(decrypt($CI->session->userdata('user_id')));
    $result_arr = array();

    $pp_status = $CI->my_prayer_partner_model->get_prayer_partner_accepted_me_him($logged_user, $to_user_id);
    $netpal_status = $CI->netpals_model->if_already_netpal($logged_user, $to_user_id);


    $friend_status = $CI->users_model->if_already_friend($logged_user, $to_user_id);


    ### prayer group

    $grp_ids_arr = $CI->prayer_group_model->get_grpids_by_logged_in_user($to_user_id);
    if (count($grp_ids_arr)) {
        $grp_ids_str = implode(',', $grp_ids_arr);
    }
    $arr_pg_user = $CI->prayer_group_model->get_members_ids_by_grpids($grp_ids_str);
    $pg_user_arr = array();
    if (count($arr_pg_user)) {
        foreach ($arr_pg_user as $val) {
            $pg_user_arr[] = $val['i_user_id'];
        }
    }
    if (in_array($logged_user, $pg_user_arr))
        $result_arr[] = '"Prayer Group"';
    ### prayer group
    ### ring group
    $ring_id_arr = $CI->my_ring_model->get_ringids_by_userID($to_user_id);
    if (count($ring_id_arr)) {
        $ring_ids_str = implode(',', $ring_id_arr);
    }
    $arr_ring_user = $CI->my_ring_model->get_members_ids_by_ringids($ring_ids_str);
    $ring_user_arr = array();
    if (count($arr_ring_user)) {
        foreach ($arr_ring_user as $val) {
            $ring_user_arr[] = $val['i_invited_id'];
        }
    }
    if (in_array($logged_user, $ring_user_arr))
        $result_arr[] = '"Ring User"';
    ### ring group	

    if (count($pp_status) > 0)
        $result_arr[] = '"Prayer Partner"';

    if (count($friend_status) > 0)
        $result_arr[] = '"Friend"';

    if (count($netpal_status) > 0)
        $result_arr[] = '"Netpal"';

    return $result_arr;
}

function get_genre_name_by_id($id) {
    $ci = get_instance();
    $query = "SELECT genre_name FROM cg_genre WHERE id=" . $id . " AND i_status=1";
    $q1 = $ci->db->query($query);
    $result = $q1->result_array();

    return $result['0']['genre_name'];
}

######### msg privacy per profile and logged user 

function getMsgPrivacy($i_profile_id, $logged_user_id, $friend_arr, $netpal_arr, $pp_arr) {

    $ci = get_instance();
    $ci->load->model('users_model');
    $arr_privacy_msg = $ci->users_model->get_privacy_for_msg($i_profile_id);

    #pr($arr_privacy_msg);
    $postwall_flag = false;

    if (intval($arr_privacy_msg['i_friend_privacy']) == 1) {
        foreach ($friend_arr as $frnd) {
            if ($frnd['user_id'] == $logged_user_id)
                $postwall_flag = true;
        }
    }

    if (intval($arr_privacy_msg['i_netpal_privacy']) == 1) {
        foreach ($netpal_arr as $netpal) {
            if ($netpal['user_id'] == $logged_user_id)
                $postwall_flag = true;
        }
    }


    if (intval($arr_privacy_msg['i_prayer_partner_privacy']) == 1) {
        foreach ($pp_arr as $pp) {
            if ($pp['user_id'] == $logged_user_id)
                $postwall_flag = true;
        }
    }


    if (intval($arr_privacy_msg['i_ring_privacy']) != 0) {
        $ci->load->model('my_ring_model');
        $ring_id_arr = $ci->my_ring_model->get_ringids_by_userID($i_profile_id);
        if (count($ring_id_arr)) {
            $ring_ids_str = implode(',', $ring_id_arr);
        }

        $arr_ring_user = $ci->my_ring_model->get_members_ids_by_ringids($ring_ids_str);
        $ring_user_arr = array();
        if (count($arr_ring_user)) {
            foreach ($arr_ring_user as $val) {
                $ring_user_arr[] = $val['i_invited_id'];
            }
        }
        if (in_array($logged_user_id, $ring_user_arr))
            $postwall_flag = true;
    }


    if (intval($arr_privacy_msg['i_prayer_group_privacy']) != 0) {
        $ci->load->model('prayer_group_model');
        ### get groups_ids string by profile id
        $grp_ids_arr = $ci->prayer_group_model->get_grpids_by_logged_in_user($i_profile_id);
        if (count($grp_ids_arr)) {
            $grp_ids_str = implode(',', $grp_ids_arr);
        }

        $arr_pg_user = $ci->prayer_group_model->get_members_ids_by_grpids($grp_ids_str);
        $pg_user_arr = array();
        if (count($arr_pg_user)) {
            foreach ($arr_pg_user as $val) {
                $pg_user_arr[] = $val['i_user_id'];
            }
        }
        if (in_array($logged_user_id, $pg_user_arr))
            $postwall_flag = true;
    }


    ### added if no restriction i.e visible to every one
    if ($arr_privacy_msg['i_friend_privacy'] == 0 && $arr_privacy_msg['i_prayer_group_privacy'] == 0 && $arr_privacy_msg['i_ring_privacy'] == 0 &&
            $arr_privacy_msg['i_prayer_partner_privacy'] == 0 && $arr_privacy_msg['i_netpal_privacy'] == 0) {
        $postwall_flag = true;
    }
    #echo $postwall_flag.' $$$$ ';

    return $postwall_flag;
}

function get_user_timezone_by_id($i_user_id = NULL) {
    try {
        $o_ci = get_instance();
        $o_ci->db->select("s_time");
        $o_ci->db->where('id', $i_user_id);

        $arr_qry = $o_ci->db->get('users');
        $ret_ = $arr_qry->row_array();
        return $ret_["s_time"];
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}

function getUserLocalTime($zone_val) {

    $timezone = new DateTimeZone($zone_val);
    $date = new DateTime();
    $date->setTimezone($timezone);
    return $date->format('F d, Y H:i:s');
}

/* * ********prayer partner check********* */

function check_prayer_partner($user_id) {
    //$flag=0;
    $ci = get_instance();
    $q = 'select * from cg_prayer_partner where (i_accepter_id="' . $user_id . '" or i_requester_id="' . $user_id . '") and s_status="accepted"';
    $qry = $ci->db->query($q);
    //echo $q;
    $count = $qry->num_rows();
    //echo 'pp'.$count;
    $max = $ci->db->query("select * from cg_site_settings");
    $max_pp = $max->result_array();
    //echo 'limit'.$max_pp['0']['i_max_prayer_partner'];exit;
    if ($count >= $max_pp['0']['i_max_prayer_partner']) {
        $flag = 1;
    } else {
        $flag = 0;
    }
    return $flag;
}

/* * fav tweet check * */

function check_fav($id) {
    $ci = get_instance();
    $query = $ci->db->query('select i_tweet_id as ids from cg_tweets_fav where i_tweet_id=' . $id);
    $q = $query->num_rows();
    return $q;
}

function get_mutual_friends($id) {
    $ci = get_instance();
    $ci->load->model('contacts_model');
    $ids = decrypt($id);
    $a = $ci->contacts_model->get_mutual_friends_by_user($ids);
//pr($a,1);

    return $a;
}

function get_user_name_by_id($id) {
    $ci = get_instance();
    $sql = $ci->db->query("select CONCAT(s_first_name,' ',s_last_name) as s_displayname from cg_users where id=" . $id);
    $a = $sql->result_array();
    return $a['0']['s_displayname'];
}

function get_mutual_count($ids) {
    $cms = array();
    $cms = explode(',', $ids);
    //pr($cms,1);
    return count($cms);
}

function get_friends_by_id($id) {
    $ci = get_instance();
    $ids = decrypt($id);
    $q = $ci->db->query("select count(*) count from cg_user_contacts where (i_requester_id =" . $ids . " or i_accepter_id=" . $ids . ") AND s_status='accepted'"); #echo $ci->db->last_query();
    $res = $q->result_array();
    return $res['0']['count'];
}

function get_friend_suggestion() {
    $ci = get_instance();
    $q = $ci->db->query("select distinct u.id, u.s_profile_photo, u.e_gender,CONCAT(u.s_first_name,' ',u.s_last_name) AS s_profile_name  
        from cg_users u,cg_user_contacts e where 1 
        AND e.i_accepter_id=u.id AND e.s_status='accepted' 
    AND u.i_status=1 and u.i_isdeleted=1 ORDER BY RAND() LIMIT 2"); #echo $ci->db->last_query();
    $res = $q->result_array();
    return $res;
}

function check_event_privacy($e_id, $u_id) {
    $ci = get_instance();
    $sql = "select count(*) count from cg_event_privacy where i_user_id=" . $u_id . " and i_event_id=" . $e_id;
//echo $sql;exit;
    $a = $ci->db->query($sql);
    $res = $a->result_array();
    return $res['0']['count'];
}

function get_privacy_setting_by_user_id($Uid, $mode) {
    $CI = & get_instance();
    //$i_profile_id 		= intval(decrypt($CI->session->userdata('user_id')));
    //$Uid='7';
    $res = $CI->db->query("SELECT * FROM {$CI->db->PRIVACY_SETTINGS} WHERE i_user_id='" . $Uid . "' AND s_section_name='" . $mode . "'"); #echo $CI->db->last_query();
    $rst_arr = $res->result_array();
    //pr ($rst_arr['0'],1);
    return $rst_arr['0'];
}

function get_user_details_by_id($id) {
    $CI = & get_instance();
    $query = $CI->db->query("select * from cg_users where id=" . $id);
    $q = $query->result_array();
    return $q['0'];
}

function get_total_ring_post_by_id($id) {
    $ci = & get_instance();
    $ci->load->model('ring_post_model');
    $total_rows = $ci->ring_post_model->get_total_all_ring_post_by_ring_id($id);
    return $total_rows;
}

function get_total_interested_people($id) {
    $ci = & get_instance();
    $ci->load->model('events_user_invited_model');
    $total_rows = $ci->events_user_invited_model->get_total_events_rsvps_recived($id, '');
    return $total_rows;
}

function get_comments_by_ring_id($id) {

    $ci = & get_instance();
    $ci->load->model('ring_post_comments_model');
    $where = " where A.i_ring_post_id=" . $id;
    $total_cmnts = $ci->ring_post_comments_model->gettotal_info($where);
    return $total_cmnts;
}

function get_post_detail_by_id($id) {
    $ci = & get_instance();
    $q = $ci->db->query("select id,data,i_owner_id from cg_user_newsfeeds where id=" . $id);
    $res = $q->result_array();
//pr($res,1);
    return $res[0];
}

function get_churchpost_detail_by_id($id) {
    $ci = & get_instance();
    $q = $ci->db->query("select id,data,i_owner_id from cg_church_newsfeed where id='" . $id."'");
    $res = $q->result_array();
//pr($res,1);
    return $res[0];
}

function get_blog_detail_by_id($id) {
    $ci = & get_instance();
    $q = $ci->db->query("select ub.* ,CONCAT(u.s_first_name,' ',u.s_last_name) AS s_profile_name  from cg_user_blogs AS ub , cg_users AS u where ub.id='".$id."' AND u.id = ub.i_user_id" );
    $res = $q->result_array();
//pr($res,1);
    return $res[0];
}

function get_article_deatil_by_id($id) {
    $ci = & get_instance();
    $q = $ci->db->query("select * from cg_user_blog_post where id=" . $id);
    $res = $q->result_array();
//pr($res,1);
    return $res[0];
}

function get_ring_post_deatil_by_id($id) {
    $ci = & get_instance();
    $q = $ci->db->query("select * from cg_user_ring_post where id=" . $id);
    $res = $q->result_array();
//pr($res,1);
    return $res[0];
}

function get_tweet_detail_by_id($id) {
    $ci = & get_instance();
    $q = $ci->db->query("select * from cg_tweets where id=" . $id);
    $res = $q->result_array();
//pr($res,1);
    return $res[0];
}

//join request

function get_pending_requests_count_by_ring_id($id = '') {
    $ci = & get_instance();
    $q = $ci->db->query("select count(*) as rows from cg_ring_invited_user where i_request=1 and i_ring_id=" . $id);
    $res = $q->result_array();
    $reqst_count = $res[0]['rows'];

    return $reqst_count;
}

function get_all_ring_details_by_id($id) {
    $ci = & get_instance();
    $q = $ci->db->query("select  inv.* , u.e_gender , u.s_profile_photo , CONCAT(u.s_first_name,' ',u.s_last_name) AS s_profile_name  from cg_ring_invited_user AS inv , cg_users AS u where inv.i_request=1 and inv.i_ring_id=$id AND u.id = inv.i_invited_id  GROUP BY inv.i_invited_id");
    $res = $q->result_array();
    //pr($res,1);
    return $res;
}

//abuse 

function check_abusive_words($msg = '') {
    $ci = & get_instance();
    $is_abusive = 0;
    $abusive_words = array();
    $q = $ci->db->query("select  s_word from cg_abuse_dictionary");
    $res = $q->result_array();
    foreach ($res as $value) {
        $word = $value['s_word'];
        array_push($abusive_words, $word);
    }
    foreach ($abusive_words as $value) {
        if (strpos($msg, $value) !== false) {
            $is_abusive++;
        }
    };
    //pr($abusive_words, 1);
    return $is_abusive;
}

function is_temp_password_user($user_id = '') {
    $ci = & get_instance();
    $q = $ci->db->query("select  is_temp_password from cg_users where id=" . $user_id);
    $res = $q->result_array();
    return $res[0]['is_temp_password'];
}

function is_temp_password_user_by_email($email = '') {
    $ci = & get_instance();
    $q = $ci->db->query("select  is_temp_password from cg_users where s_email='$email'");
    $res = $q->result_array();
    return $res[0]['is_temp_password'];
}

function change_temp_pwd_fld($user_id = '') {
    $ci = & get_instance();
    $data = array(
        'is_temp_password' => 2
    );
    $q = $ci->db->where('id', $user_id)->update('cg_users', $data);
}

function update_temp_pwd_fld($user_id = '') {
    $ci = & get_instance();
//    $q = $ci->db->query("select  is_temp_password from cg_users where id=" . $user_id);
//    $res = $q->result_array();
//    $istemp_pwd = $res[0]['is_temp_password'];
    $data = array(
        'is_temp_password' => ''
    );
    $q = $ci->db->where('id', $user_id)->update('cg_users', $data);
}

function update_account_status($user_id = '') {
    $ci = & get_instance();
    $i_ret_ = 0; ////Returns false
    $data = array(
        'account_status' => 1
    );
    $q = $ci->db->where('id', $user_id)->update('cg_users', $data);
    $i_ret_ = $ci->db->affected_rows();
    return $i_ret_;
}

function get_user_account_status($email = '') {
    $ci = & get_instance();
    $q = $ci->db->query("select  account_status from cg_users where s_email='$email'");
    $res = $q->result_array();
    return $res[0]['account_status'];
}

function update_account_status_active($user_id = '') {
    $ci = & get_instance();
    $i_ret_ = 0; ////Returns false
    $data = array(
        'account_status' => 0
    );
    $q = $ci->db->where('id', $user_id)->update('cg_users', $data);
    $i_ret_ = $ci->db->affected_rows();
    return $i_ret_;
}

function update_user_i_status_inactive($user_id = '') {
    $ci = & get_instance();
    $i_ret_ = 0; ////Returns false
    $data = array(
        'i_status ' => 2
    );
    $q = $ci->db->where('id', $user_id)->update('cg_users', $data);
    $i_ret_ = $ci->db->affected_rows();
    return $i_ret_;
}

function update_user_i_status_active($user_id = '') {
    $ci = & get_instance();
    $i_ret_ = 0; ////Returns false
    $data = array(
        'i_status ' => 1
    );
    $q = $ci->db->where('id', $user_id)->update('cg_users', $data);
    $i_ret_ = $ci->db->affected_rows();
    return $i_ret_;
}

//get prayer group name by id
function get_prayer_name_group_by_id($id) {
    $ci = & get_instance();
    $sql = $ci->db->query('select * from cg_prayer_group where id=' . $id);
    //echo $sql;
    $res = $sql->result_array();
    return $res['0']['s_group_name'];
}

// get ring owner id
function get_ring_owner_id($id) {
    $ci = & get_instance();
    $sql = $ci->db->query('select * from cg_user_ring where id=' . $id);
    //echo $sql;
    $res = $sql->result_array();
    return $res['0']['i_user_id'];
}

function check_member_by_ring_id($ring, $uid) {
    $ci = & get_instance();
    $sql = $ci->db->query('select count(*) as count from cg_ring_invited_user where i_ring_id=' . $ring . ' and i_invited_id=' . $uid . ' and i_joined = "1"');
    //echo $sql;
    $res = $sql->result_array();
    if ($res['0']['count'] > 0) {
        return true;
    } else
        return false;
}

function get_e_intercession_testimony_count($id = '') {
    $ci = & get_instance();
    $q = $ci->db->query("select  count(i_id_intercession_wall_post) as rows from cg_bible_intercession_wall_post_testimony where i_id_intercession_wall_post='$id'");
    $res = $q->result_array();
    return $res[0]['rows'];
}

function get_active_ring_members_count($ring_id = '') {
    $ci = & get_instance();
    $q = $ci->db->query("select  count(id) as rows from cg_ring_invited_user where  i_ring_id='$ring_id' and  	i_joined=1");
    $res = $q->result_array();
    return $res[0]['rows'];
}

function get_active_prayergrp_members_count($grp_id = '') {
    $ci = & get_instance();
    $q = $ci->db->query("select  count(id) as rows from cg_prayer_group_members where  i_prayer_group_id='$grp_id' and   s_status='accepted'");
    $res = $q->result_array();
    //pr($res);
    return $res[0]['rows'];
}

function get_verse_by_id($v_id) {
    $ci = & get_instance();
    $sql = "SELECT *,v.id AS verseid FROM 
					{$ci->db->BIBLE_BOOK} AS b, 
					{$ci->db->BIBLE_CHAPTER} AS c ,
					{$ci->db->BIBLE_VERSES} AS v 
					WHERE b.id=c.i_book_id AND c.id=v.i_chapter_id AND v.id=" . $v_id;
    $result = $ci->db->query($sql)->result_array();
    return $result['0'];
}

function get_event_accept_status($event_id, $userid) {
    $ci = & get_instance();
    $sql = $ci->db->query('select count(*) as count from cg_event_rsvp where i_event_id=' . $event_id . ' and i_user_id=' . $userid);
    $res = $sql->result_array();
    return $res['0']['count'];
}

function get_chat_room_owner($rid) {
    $ci = & get_instance();
    $sql = $ci->db->query('select i_user_id from cg_user_chat_rooms where i_room_id=' . $rid);
    $res = $sql->result_array();
    if ($res['0']['i_user_id'] != '')
        return get_username_by_id($res['0']['i_user_id']);
    else
        return '';
}

function get_user_data_by_id($user_id = '') {
    $ci = & get_instance();
    $q = $ci->db->query("select *  from cg_users where id='$user_id'");
    $res = $q->result_array();
    return $res;
}

function curPageURL() {
    $pageURL = 'http';
    if ($_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

function update_tweet_bg_img_by_id($arr, $id = '') {
    $ci = & get_instance();
    $q = $ci->db->update('cg_users', $arr, array('id' => $id));
    $i_ret_ = $ci->db->affected_rows();
    return $i_ret_;
}

function get_tweet_bg_img_by_id($id = '') {
    $ci = & get_instance();
    $q = $ci->db->query("select  s_tweet_bg_img from cg_users where id='$id'");
    $res = $q->result_array();
    return $res[0]['s_tweet_bg_img'];
}

function get_ring_photo_by_id($ringId) {
    $ci = get_instance();
    $sql = $ci->db->query("select s_logo from cg_user_ring where id=" . $ringId);
    $res = $sql->result_array();
    $IMG = base_url() . "uploads/user_ring_logos/" . getThumbName($res['0']['s_logo'], 'thumb');
    return $IMG;
}

function match_video($i_user_id, $i_album_id, $video_url, $id = '') {
    $ci = get_instance();
    if ($id == '') {
        $sql = $ci->db->query("select s_video_file_name from cg_user_videos where i_user_id=" . $i_user_id . " and i_video_album_id=" . $i_album_id);
    } else {
        $sql = $ci->db->query("select s_video_file_name from cg_user_videos where i_user_id=" . $i_user_id . " and i_video_album_id=" . $i_album_id . " and id !=" . $id);
    }
    $res = $sql->result_array();
    //pr($res,1);
    if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $val['s_video_file_name'], $fetch)) {
        $value = $fetch[1];
    } else if (preg_match('/youtube\.com\/embed\/([^\&\?\/]+)/', $val['s_video_file_name'], $fetch)) {
        $value = $fetch[1];
    } else if (preg_match('/youtube\.com\/v\/([^\&\?\/]+)/', $val['s_video_file_name'], $fetch)) {
        $value = $fetch[1];
    } else if (preg_match('/youtu\.be\/([^\&\?\/]+)/', $val['s_video_file_name'], $fetch)) {
        $value = $fetch[1];
    } else {
        $value = '0';
    }
    $ret_val = '';
    foreach ($res as $val) {
        if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $val['s_video_file_name'], $fetch)) {
            $values = $fetch[1];
        } else if (preg_match('/youtube\.com\/embed\/([^\&\?\/]+)/', $val['s_video_file_name'], $fetch)) {
            $values = $fetch[1];
        } else if (preg_match('/youtube\.com\/v\/([^\&\?\/]+)/', $val['s_video_file_name'], $fetch)) {
            $values = $fetch[1];
        } else if (preg_match('/youtu\.be\/([^\&\?\/]+)/', $val['s_video_file_name'], $fetch)) {
            $values = $fetch[1];
        } else {
            // not an youtube video
            $values = '0';
        }

        if ($values != 0 && $value != 0) {
            if ($values == $value) {
                $ret_val = "video already exists";
                //exit;
            }
        } else {
            if ($val['s_video_file_name'] == $video_url) {
                $ret_val = "video already exists";
                //exit;
            }
        }
    }
    //echo '11111'.$ret_val;exit;
    return $ret_val;
}

function get_chat_name_by_id($id) {
    $ci = & get_instance();
    $q = $ci->db->query("select s_chat_display_name from cg_users where id='$id'");
    $res = $q->result_array();
    return $res[0]['s_chat_display_name'];
}

function get_prayer_parameters_set_by_admin() {
    $ci = & get_instance();
    $q3 = $ci->db->query("select * from cg_prayer_partner_q_params");
    $res3 = $q3->result_array();
    return $res3[0];
}

function get_user_prayer_partner_qualifications_by_id($id = '') {
    $ci = & get_instance();

    $q1 = $ci->db->query("select count(*) as rows from cg_user_contacts where (i_requester_id='$id' or i_accepter_id='$id') and s_status='accepted' and i_deleted_by=1");
    $res1 = $q1->result_array();
    $frnds_nmbr = $res1[0]['rows'];

    $q3 = $ci->db->query("select count(*) as rows from cg_bible_prayer_commitments where i_user_id='$id'");
    $res3 = $q3->result_array();
    $commitmnts = $res3[0]['rows'];

    $q4 = $ci->db->query("select dt_created_on from cg_users where id='$id'");
    $res4 = $q4->result_array();
    $join_date = $res4[0]['dt_created_on'];
    $join_date_arr = explode(" ", $join_date);
    $join_date = strtotime($join_date_arr[0]);
    $admin_set_params = get_prayer_parameters_set_by_admin();
    $min_months = $admin_set_params['min_months'];
    $sixMonthsLater = strtotime("+$min_months months", $join_date);
//    echo $join_date = date("d/m/y", $join_date).'<br>';
//    echo $sixMonthsLater_date = date('d-m-Y', $sixMonthsLater);
    //$join_date = da
    $arr = array(
        'total_frnds' => $frnds_nmbr,
        'total_commitments' => $commitmnts,
        'sixMonthsLater' => $sixMonthsLater,
        'join_date' => $join_date
    );
    return $arr;
}

function get_is_pr_prtnr_q_mail_sent($user_id = '') {
    $ci = & get_instance();
    $q = $ci->db->query("select is_pr_partner_q_mail_sent from cg_users where id='$user_id'");
    $res = $q->result_array();
    return $res[0]['is_pr_partner_q_mail_sent'];
}

function update_prayer_partner_mail_sent_status($user_id = '') {
    $ci = & get_instance();
    $i_ret_ = 0; ////Returns false
    $data = array(
        'is_pr_partner_q_mail_sent ' => 1
    );
    $q = $ci->db->where('id', $user_id)->update('cg_users', $data);
    $i_ret_ = $ci->db->affected_rows();
    return $i_ret_;
}

function get_events_by_user($userid, $type) {
    $ci = & get_instance();
    $q = $ci->db->query("select * from cg_privacy_settings where i_user_id='$userid' and s_section_name='event'");
    $res = $q->result_array();
    // echo $ci->db->last_query();
    //pr($res);
    if ((($res['0']['i_friend_privacy'] == '0') && ($res['0']['i_netpal_privacy'] == '0') && ($res['0']['i_prayer_partner_privacy'] == '0') && ($res['0']['i_ring_privacy'] == '0') && ($res['0']['i_prayer_group_privacy'] == '0')) || $type == "my_wall") {
        $dt = new DateTime();
        $d = $dt->format('Y-m-d H:i:s');
        $q1 = $ci->db->query("select s_image_1,i_country_id,s_address,s_city,s_title,s_desc,dt_updated_on,dt_start_time,dt_end_time from cg_events where i_host_id='$userid' and i_status='1' and dt_end_time > '$d'");
        $res1 = $q1->result_array();
        // echo $ci->db->last_query();
        //pr($res1,1);
        return $res1;
    } else {
        return null;
    }
}

function get_netpal_parameters_set_by_admin() {
    $ci = & get_instance();
    $q3 = $ci->db->query("select * from cg_netpal_q_params");
    $res3 = $q3->result_array();
    return $res3[0];
}

function get_user_netpal_qualifications_by_id($id = '') {
    $ci = & get_instance();

    $q1 = $ci->db->query("select count(*) as rows from cg_user_contacts where (i_requester_id='$id' or i_accepter_id='$id') and s_status='accepted' and i_deleted_by=1");
    $res1 = $q1->result_array();
    $frnds_nmbr = $res1[0]['rows'];

    $q3 = $ci->db->query("select count(*) as rows from cg_bible_prayer_commitments where i_user_id='$id'");
    $res3 = $q3->result_array();
    $commitmnts = $res3[0]['rows'];

    $q4 = $ci->db->query("select dt_created_on from cg_users where id='$id'");
    $res4 = $q4->result_array();
    $join_date = $res4[0]['dt_created_on'];
    $join_date_arr = explode(" ", $join_date);
    $join_date = strtotime($join_date_arr[0]);
    $admin_set_params = get_netpal_parameters_set_by_admin();
    $min_months = $admin_set_params['min_months'];
    $sixMonthsLater = strtotime("+$min_months months", $join_date);
//    echo $join_date = date("d/m/y", $join_date).'<br>';
//    echo $sixMonthsLater_date = date('d-m-Y', $sixMonthsLater);
    //$join_date = da
    $arr = array(
        'total_frnds' => $frnds_nmbr,
        'total_commitments' => $commitmnts,
        'sixMonthsLater' => $sixMonthsLater,
        'join_date' => $join_date
    );
    return $arr;
}

function get_is_netpal_q_mail_sent($user_id = '') {
    $ci = & get_instance();
    $q = $ci->db->query("select is_netpal_q_mail_sent from cg_users where id='$user_id'");
    $res = $q->result_array();
    return $res[0]['is_netpal_q_mail_sent'];
}

function update_netpal_mail_sent_status($user_id = '') {
    $ci = & get_instance();
    $i_ret_ = 0; ////Returns false
    $data = array(
        'is_netpal_q_mail_sent' => 1
    );
    $q = $ci->db->query("UPDATE `cogtime`.`cg_users` SET `is_netpal_q_mail_sent` = '1' WHERE `cg_users`.`id` = '$user_id'");
    //$q = $ci->db->where('id', $user_id)->update('cg_users', $data);
    $i_ret_ = $ci->db->affected_rows();
    return $i_ret_;
}

function get_fruit_verse_by_id($id)
{
$ci = & get_instance();
  $q = $ci->db->query("select  * from cg_bible_fruit_verse where id='$id'");
  $res = $q->result_array();
  return $res['0'];
}

function get_admin_username_by_id($id)
{
$ci = & get_instance();
  $q = $ci->db->query("select concat(s_name,' ',s_last_name) as name  from cg_admin_user where id='$id'");
  $res = $q->result_array();
  return $res['0']['name'];
}

function getSlotStatus($dt, $time, $events_ids, $userid ,$exclude_upper_limit=false){
    
    $ci  = & get_instance();
    $sql = 'SELECT count(*)
                FROM 
                (SELECT id FROM cg_organizer_to_do_list 
                    where i_user_id = "'.$userid.'" 
                        AND d_date = "'.$dt.'"
                        AND HOUR(t_start_time) = "'.$time.'"
                    UNION 
                SELECT id FROM cg_organizer_note 
                    where i_user_id = "'.$userid.'" 
                        AND d_date = "'.$dt.'"
                        AND HOUR(t_time) = "'.$time.'"
                    UNION 
                SELECT id FROM cg_events 
                    where dt_start_time = "'.$dt.'"
                        AND HOUR(dt_start_time) = "'.$time.'" 
                        AND id in ('.$events_ids.')
                ) as drvd_tbl';
    $res = $ci->db->query($sql);
    $result = $res->result_array();

    if($result[0]['count'] > 0 )
        return false;
    else
        return true; 
}



function get_verse_link($book,$chapter,$verse_no)
{
$ci  = & get_instance();
$q = $ci->db->query("select (verse.id-17) as s_verse,(verse.id+8) as l_verse,chap.s_chapter,verse.s_text from cg_bible_verses verse join cg_bible_chapter chap on chap.id=verse.i_chapter_id join cg_bible_book book on chap.i_book_id=book.id where book.s_book_name='".$book."' and chap.s_chapter='".$chapter."' and verse.i_verses='".$verse_no."'");
  $res = $q->result_array();
 if($res['0']['s_verse']>17)
 {
 $link=base_url()."holy-place/read-bible/".$res['0']['s_verse']."/".$res['0']['l_verse'];
 }
 else
 {
	$s_verse=($res['0']['s_verse']-1);
	$l_verse=$s_verse+25;
	$link=base_url()."holy-place/read-bible/".$s_verse."/".$l_verse;
 }
 return $link;
	
}
function getChattypebyid($id) {
    $ci = & get_instance();
     $sql = "select s_type as type from cg_all_chat_room where i_chat_room_id = '".$id."'";
    $q = $ci->db->query($sql);
  
   
     $res = $q->result_array();
    // pr($res,1);
    return $res[0]['type'];
    //$CI->load->model('chat_categories_model');
    //$wh = " WHERE  cat.i_room_id =  " . $id;
   // $s_category_name = $CI->chat_categories_model->get_category_name($wh);

    //return $s_category_name;
}

function getChatStartDatebyId($id) {
    $ci = & get_instance();
     $sql = "select dt_start_time as start from cg_all_chat_room where i_chat_room_id = '".$id."'";
    $q = $ci->db->query($sql);
  
   
     $res = $q->result_array();
    // pr($res,1);
    return $res[0]['start'];
    //$CI->load->model('chat_categories_model');
    //$wh = " WHERE  cat.i_room_id =  " . $id;
   // $s_category_name = $CI->chat_categories_model->get_category_name($wh);

    //return $s_category_name;
}

function getChatEndDatebyId($id) {
    $ci = & get_instance();
     $sql = "select dt_end_time as end from cg_all_chat_room where i_chat_room_id = '".$id."'";
    $q = $ci->db->query($sql);
  
   
     $res = $q->result_array();
    // pr($res,1);
    return $res[0]['end'];
    //$CI->load->model('chat_categories_model');
    //$wh = " WHERE  cat.i_room_id =  " . $id;
   // $s_category_name = $CI->chat_categories_model->get_category_name($wh);

    //return $s_category_name;
}
/************user total firend , total commitment , join date , total ring ,total prayer room*****************/
function get_user_qualifications_by_id($id = '') {
    $ci = & get_instance();

    $q1 = $ci->db->query("select count(*) as rows from cg_user_contacts where (i_requester_id='$id' or i_accepter_id='$id') and s_status='accepted' and i_deleted_by=1");
    $res1 = $q1->result_array();
    $frnds_nmbr = $res1[0]['rows'];

    $q3 = $ci->db->query("select count(*) as rows from cg_bible_prayer_commitments where i_user_id='$id'");
    $res3 = $q3->result_array();
    $commitmnts = $res3[0]['rows'];

    $q4 = $ci->db->query("select dt_created_on from cg_users where id='$id'");
    $res4 = $q4->result_array();
    $join_date = $res4[0]['dt_created_on'];
    $join_date_arr = explode(" ", $join_date);
    $join_date = strtotime($join_date_arr[0]);
    $admin_set_params = get_netpal_parameters_set_by_admin();
    $min_months = $admin_set_params['min_months'];
    $sixMonthsLater = strtotime("+$min_months months", $join_date);
//    echo $join_date = date("d/m/y", $join_date).'<br>';
//    echo $sixMonthsLater_date = date('d-m-Y', $sixMonthsLater);
    //$join_date = da
    
     $ci->load->model('my_ring_model');
     $wh = " AND r.i_user_id='" . $id . "'";
                $wh1 = " AND inv.i_invited_id='" . $id . "'";
                $total_rings = $ci->my_ring_model->gettotal_ring_by_user($wh, $wh1);
                //$qualification_params['total_rings'] = $total_rings;prayer_group_model
                $ci->load->model('prayer_group_model');
                $grp_names = $ci->prayer_group_model->get_my_groups_names($id);
                $total_prayer_grps = count($grp_names);
                
                
    $arr = array(
        'total_frnds' => $frnds_nmbr,
        'total_commitments' => $commitmnts,
        'sixMonthsLater' => $sixMonthsLater,
        'join_date' => $join_date,
        'total_ring' => $total_rings,
        'total_prayer_grps' => $total_prayer_grps
    );
    return $arr;
}
 function get_friend_number_by_id($id){
$ci = & get_instance();
$sql = "SELECT count(c.id) as frndnmbr, c.i_requester_id, c.i_accepter_id, c.i_deleted_by, c.dt_created_on, c.dt_accepted_on, u.id user_id, u.s_email, u.s_last_name, u.s_first_name , u.s_profile_photo, u.e_gender, u.i_country_id, u.i_user_type, u.s_city, u.s_state, u.i_status, u.dt_created_on, uon.s_status FROM cg_user_contacts c ,cg_users u LEFT JOIN cg_users_online AS uon ON u.id = uon.i_user_id WHERE 1 AND c.s_status = 'accepted' AND u.i_status=1 AND ((c.i_requester_id = '$id' AND u.id=c.i_accepter_id ) OR (c.i_accepter_id = '$id' AND u.id=c.i_requester_id ))";
 $q = $ci->db->query($sql);
  $res = $q->result_array();
  return $res['0']['frndnmbr'];
}





function get_church_logo_image($id, $size = "thumb", $image_name = "") {
  $ci = & get_instance();
    $id = intval($id);
    if (trim($image_name) == '') {
        $query = $ci->db->get_where('cg_church', array('id' => $id));
        $result = $query->result();
      $db_image_name =  $result[0]->ch_logo;
    } else {
        $db_image_name = $image_name;
    }
    switch ($size) {
        case 'thumb':
           
                $no_img = 'uploads/no-image/no-logo-thumb.jpg';
           
            break;
        case 'main':
           
                $no_img = 'uploads/no-image/no-logo.png';
           
            //$no_img = 'uploads/no-image/noprofileimage-thumb.gif';
            break;
    }

    $nw_image_name = ($db_image_name == "") ? base_url() . $no_img : base_url() . 'uploads/church_logo_image/' . getThumbName($db_image_name, $size);
    #$url = base_url()."public-profile/".$id."/".my_url($user_name).".html";
    return $nw_image_name;
}
function get_church_cover_image($id, $size = "thumb", $image_name = "") {
$ci = & get_instance();
    $id = intval($id);
    if (trim($image_name) == '') {
        $query = $ci->db->get_where('cg_church', array('id' => $id));
        $result = $query->result();
      $db_image_name =  $result[0]->ch_cover;
    } else {
        $db_image_name = $image_name;
    }
    switch ($size) {
        case 'thumb':
           
                $no_img = 'uploads/no-image/no-image-cover-thumb.jpg';
           
            break;
        case 'main':
           
                $no_img = 'uploads/no-image/no-image-ch.jpg';
           
            //$no_img = 'uploads/no-image/noprofileimage-thumb.gif';
            break;
    }

    $nw_image_name = ($db_image_name == "") ? base_url() . $no_img : base_url() . 'uploads/church_cover_image/' . getThumbName($db_image_name, $size);
    #$url = base_url()."public-profile/".$id."/".my_url($user_name).".html";
    return $nw_image_name;
}


function get_church_profile_image($id, $size = "thumb", $image_name = "") {
$ci = & get_instance();
    $id = intval($id);
    if (trim($image_name) == '') {
        $query = $ci->db->get_where('cg_church', array('id' => $id));
        $result = $query->result();
      $db_image_name =  $result[0]->ch_profile_img;
    } else {
        $db_image_name = $image_name;
    }
           
		$no_img = 'uploads/no-image/noprofileimage-mini.gif';
        
    

    $nw_image_name = ($db_image_name == "") ? base_url() . $no_img : base_url() . 'uploads/church_logo_image/' . getThumbName($db_image_name, $size);
    #$url = base_url()."public-profile/".$id."/".my_url($user_name).".html";
    return $nw_image_name;
}


/*** get church id by admin*******/

function get_church_by_admin($uid)
{
	$ci=& get_instance();
	$query = $ci->db->get_where('cg_church', array('ch_admin_id' => $uid));
	$result=$query->result();
	return $result[0];
}

/*******************get landing page image*** 28-01-2015**************************/

function get_church_land_image1($id, $size = "thumb", $image_name = "") {
$ci = & get_instance();
    $id = intval($id);
    if (trim($image_name) == '') {
        $query = $ci->db->get_where('cg_church', array('id' => $id));
        $result = $query->result();
      $db_image_name =  $result[0]->img1;
    } else {
        $db_image_name = $image_name;
    }
    switch ($size) {
        case 'thumb':
           
                $no_img = 'uploads/no-image/church-no-image.jpg';
           
            break;
        case 'main':
           
                $no_img = 'uploads/no-image/church-no-image.jpg';
           
            //$no_img = 'uploads/no-image/noprofileimage-thumb.gif';
            break;
    }

    $nw_image_name = ($db_image_name == "") ? base_url() . $no_img : base_url() . 'uploads/church_landing_page_image1/' . getThumbName($db_image_name, $size);
    #$url = base_url()."public-profile/".$id."/".my_url($user_name).".html";
    return $nw_image_name;
}
function get_church_land_image2($id, $size = "thumb", $image_name = "") {
$ci = & get_instance();
    $id = intval($id);
    if (trim($image_name) == '') {
        $query = $ci->db->get_where('cg_church', array('id' => $id));
        $result = $query->result();
      $db_image_name =  $result[0]->img2;
    } else {
        $db_image_name = $image_name;
    }
    switch ($size) {
        case 'thumb':
           
                $no_img = 'uploads/no-image/church-no-image.jpg';
           
            break;
        case 'main':
           
                $no_img = 'uploads/no-image/church-no-image.jpg';
           
            //$no_img = 'uploads/no-image/noprofileimage-thumb.gif';
            break;
    }

    $nw_image_name = ($db_image_name == "") ? base_url() . $no_img : base_url() . 'uploads/church_landing_page_image2/' . getThumbName($db_image_name, $size);
    #$url = base_url()."public-profile/".$id."/".my_url($user_name).".html";
    return $nw_image_name;
}
function get_church_land_image3($id, $size = "thumb", $image_name = "") {
$ci = & get_instance();
    $id = intval($id);
    if (trim($image_name) == '') {
        $query = $ci->db->get_where('cg_church', array('id' => $id));
        $result = $query->result();
      $db_image_name =  $result[0]->img3;
    } else {
        $db_image_name = $image_name;
    }
    switch ($size) {
        case 'thumb':
           
                $no_img = 'uploads/no-image/church-no-image.jpg';
           
            break;
        case 'main':
           
                $no_img = 'uploads/no-image/church-no-image.jpg';
           
            //$no_img = 'uploads/no-image/noprofileimage-thumb.gif';
            break;
    }

    $nw_image_name = ($db_image_name == "") ? base_url() . $no_img : base_url() . 'uploads/church_landing_page_image3/' . getThumbName($db_image_name, $size);
    #$url = base_url()."public-profile/".$id."/".my_url($user_name).".html";
    return $nw_image_name;
}
function get_church_land_image4($id, $size = "thumb", $image_name = "") {
$ci = & get_instance();
    $id = intval($id);
    if (trim($image_name) == '') {
        $query = $ci->db->get_where('cg_church', array('id' => $id));
        $result = $query->result();
      $db_image_name =  $result[0]->img4;
    } else {
        $db_image_name = $image_name;
    }
    switch ($size) {
        case 'thumb':
           
                $no_img = 'uploads/no-image/church-no-image.jpg';
           
            break;
        case 'main':
           
                $no_img = 'uploads/no-image/church-no-image.jpg';
           
            //$no_img = 'uploads/no-image/noprofileimage-thumb.gif';
            break;
    }

    $nw_image_name = ($db_image_name == "") ? base_url() . $no_img : base_url() . 'uploads/church_landing_page_image4/' . getThumbName($db_image_name, $size);
    #$url = base_url()."public-profile/".$id."/".my_url($user_name).".html";
    return $nw_image_name;
}

/** get like**/

function like_count_display($window_id,$table,$column)
{
	$ci=& get_instance();
	$sql="select count(id) from ".$table." where ".$column."='".$window_id."'";
	$res=$ci->db->query($sql);
	$s=$res->result_array();
	
	return $s[0]['count(id)'];
}

/** get comment count **/
function count_feed_comment_link($window_id,$table,$column)
{
	$ci=& get_instance();
	$sql="select count(id) from ".$table." where ".$column."='".$window_id."'";
	$res=$ci->db->query($sql);
	$s=$res->result_array();
	
	return $s[0]['count(id)'];
}
/*********************************************/
/*******************************/


/********************church subadmin access************************/
function subadmin_access($access_type)
{
    $ci=& get_instance();
   
    $access_arr = explode(',', $_SESSION['subadmin_access']);
	//pr($access_arr);
    if($_SESSION['charch_super_admin']=='yes')
    {
        return true;
    }
    else
    {
        if( $_SESSION['subadmin_role']==2)
        {
            if(in_array($access_type, $access_arr)) {
				return true; }
			else {
				return false; }
        }
        else
        {
            return false;
        }
    }
    
    
}

/********************church subadmin access************************/
/** get comments list**/

function get_feed_comments($window_id,$table,$column)
{
	$ci=& get_instance();
	$sql="select * from ".$table." where ".$column."='".$window_id."'";
	$res=$ci->db->query($sql);
	$s=$res->result_array();
	
	return $s;
}
function get_active_church_ring_members_count($ring_id = '') {
    $ci = & get_instance();
    $q = $ci->db->query("select  count(id) as rows from cg_church_ring_invited_user where  i_ring_id='$ring_id' and  	i_joined=1");
    $res = $q->result_array();
    return $res[0]['rows'];
}
/**********************ring counter for perticular church****************************************************/
function get_church_ring_number_by_id($id){
    $ci=& get_instance();
	$sql="select count(*) as ring_number from  cg_church_ring  where church_id= '".$id."'";
	$res=$ci->db->query($sql);
	$s=$res->result_array();
	
	return $s[0]['ring_number'];
}

/*** church member invitation **/
function get_invitation_member_list($group_id,$church_id)
{
	$ci=& get_instance();
	$ci->load->model('church_new_model');
	$result=$ci->church_new_model->get_churchmembers($church_id,'AND cm.is_approved=1','',' cm.id DESC ','');
	//pr($result,1);
	foreach($result as $key=>$res)
	{
		$sql="select count(id) as ct from cg_church_prayer_group_members where i_prayer_group_id='".$group_id."' and i_user_id='".$res->member_id."' and s_status IN ('accepted','pending')";
		//echo $sql;
		$q=$ci->db->query($sql);
		$rest=$q->result();
		//echo $rest['0']->ct ;
		if($rest['0']->ct != 0)
		{
			unset($result[$key]);
		}
	}
	//pr($result,1);
	return $result;
}
function get_church_ring_logo_by_id($id, $size = "thumb", $image_name = "") {
$ci = & get_instance();
    $id = intval($id);
    if (trim($image_name) == '') {
        $query = $ci->db->get_where('cg_church_ring', array('id' => $id));
        $result = $query->result();
      $db_image_name =  $result[0]->s_logo;
    } else {
        $db_image_name = $image_name;
    }
    switch ($size) {
        case 'thumb':
           
                $no_img = 'uploads/no-image/ring-no-img.jpg';
           
            break;
        case 'main':
           
                $no_img = 'uploads/no-image/ring-no-img.jpg';
           
            //$no_img = 'uploads/no-image/noprofileimage-thumb.gif';
            break;
    }

    $nw_image_name = ($db_image_name == "") ? base_url() . $no_img : base_url() . 'uploads/church_ring_logo/' . getThumbName($db_image_name, $size);
    #$url = base_url()."public-profile/".$id."/".my_url($user_name).".html";
    return $nw_image_name;
}

/** Prayer groups by church id **/

function get_prayer_group_count_by_church($cid)
{
	$ci=& get_instance();
	$query=$ci->db->query("select count(id) as ct from cg_church_prayer_group where i_owner_id='".$cid."'");
	$result=$query->result_array();
	return $result['0']['ct'];
}

/** member count by church id **/

function get_member_count_by_church_id($cid)
{
	$ci=& get_instance();
	$query=$ci->db->query("select count(id) as ct from cg_church_member where church_id='".$cid."' and is_approved=1");
	$result=$query->result_array();
	return $result['0']['ct'];
}
function check_member_by_church_ring_id($ring, $uid) {
    $ci = & get_instance();
    $sql = $ci->db->query("select count(*) as count from cg_church_ring_invited_user where i_ring_id='" . $ring . "' and i_invited_id='" . $uid . "' and i_joined = '1'");
    //echo $sql;
    $res = $sql->result_array();
    if ($res['0']['count'] > 0) {
        return true;
    } else
        return false;
}

function get_church_dashboard_url_by_church_id($id) {
    $ci = & get_instance();
    $query = $ci->db->get_where('cg_church', array('id' => $id));
        $result = $query->result();
        if(!empty($result)){
            $url = $result[0]->ch_page_url;
        }else{
           $url='javascript:void(0)'; 
        }
        return $url;
   
}
function getMembers_church_ring_bYID($ring_id) {
    $CI = & get_instance();
    $CI->load->model('church_ring_model');
    $arr = $CI->church_ring_model->get_all_ring_members_by_ring_id($ring_id);

    if (count($arr)) {
        $member_arr = array();
        foreach ($arr as $k => $val) {
            $member_arr[$k] = $val['i_invited_id'];
        }
    }
    return $member_arr;
}


function get_post_count_by_ring_id($rid)
{
	$ci=& get_instance();
	$query=$ci->db->query('select count(id) as ct from cg_church_ring_post where i_ring_id='.$rid.' and i_disable=1');
	$result=$query->result_array();
	return $result['0']['ct'];
}
function get_member_count_by_ring_id($rid)
{
	$ci=& get_instance();
	$query=$ci->db->query('select count(id) as ct from cg_church_ring_invited_user where i_ring_id='.$rid.' and i_joined=1');
	$result=$query->result_array();
	return $result['0']['ct'];
}
function get_like_count_by_ring_id($rid)
{
	$ci=& get_instance();
	$query=$ci->db->query('select count(l.id) as ct from cg_church_ring_post_like as l , cg_church_ring_post as p where l.i_ring_post_id= p.id and p.i_ring_id = '.$rid.' AND p.i_disable=1');
	$result=$query->result_array();
	return $result['0']['ct'];
}
function get_comment_count_by_ring_id($rid)
{
	$ci=& get_instance();
	$query=$ci->db->query('select count(id) as ct from cg_church_ring_post_comments where i_ring_id='.$rid.'');
	$result=$query->result_array();
	return $result['0']['ct'];
}
function get_church_invited($id, $table, $field) {
    $ci = get_instance();
    $ci->load->model('church_ring_model');
    $query = "SELECT i_user_id as user_id FROM  " . $table . " WHERE " . $field . "=" . $id . " GROUP BY i_user_id";
    #echo $query;
    $result = $ci->db->query($query);
    #pr($result);
    $result_arr = $result->result_array();
    #pr($result_arr);
    return $result_arr;
}
function get_all_church_session($cid){
    $ci = get_instance();
    $user_id = intval(decrypt($ci->session->userdata('user_id')));
    $sql_churchmember = "SELECT *,ch.id AS chid FROM cg_church AS ch,cg_church_member AS chm 
    WHERE ch.id=chm.church_id AND chm.member_id='".$user_id."' AND ch.id='".$cid."'  AND chm.is_leave = 0 AND chm.is_blocked = 1 AND chm.is_approved = 1";

    $query_churchmember = $ci->db->query($sql_churchmember);
    $numrowmember = $query_churchmember->num_rows();   
    $result_churchmember = $query_churchmember->result();
    if($numrowmember>0)
    {
        
        if($result_churchmember[0]->role == 2)
            $_SESSION['subadmin_role'] = $result_churchmember[0]->role;
        $_SESSION['charch_super_admin'] = 'no';
        $_SESSION['logged_church_id'] = $result_churchmember[0]->chid;
        
    }
    else
    {
        $query = $ci->db->get_where('cg_church', array('ch_admin_id' => $user_id , 'id' =>$cid));
        $result = $query->result();
//pr($result,1);
         $numrow_superadmin = $query->num_rows();  
       
        if($numrowmember==0 && $numrow_superadmin > 0)
        {
           
            $_SESSION['charch_super_admin'] = 'yes';
            $_SESSION['logged_church_id'] = $result[0]->id;
            $_SESSION['subadmin_role'] = '';
           
        }else if($numrowmember == 0 && $numrow_superadmin == 0 ){
            header('Location: ' . base_url());
        }
    }
    
    
}

function get_prayer_group_member_count_by_church($cid)
{
	$ci=& get_instance();
	$query=$ci->db->query("select count(DISTINCT cpm.id) as pr_grp_mem_c from cg_church_prayer_group_members As cpm LEFT JOIN cg_church_prayer_group As cp ON cp.id = cpm.i_prayer_group_id where cpm.i_request = '1' AND cpm.s_status = 'accepted' AND cp.i_owner_id='".$cid."'");
	$result=$query->result_array();
	//pr($result);
	return $result['0']['pr_grp_mem_c'];
}

function get_prayer_group_post_count_by_church($cid)
{
	$ci=& get_instance();
	$query=$ci->db->query("select count(DISTINCT cpgrp.id) as pr_grp_post_c from cg_church_prayer_group_post As cpgrp LEFT JOIN cg_church_prayer_group As cp ON cp.id = cpgrp.i_prayer_group_id where cp.i_isenabled = '1' AND cp.i_owner_id='".$cid."'");
	$result=$query->result_array();
	//pr($result);
	return $result['0']['pr_grp_post_c'];
}
function get_church_ring_total_by_church_ID($cid){
    $ci=& get_instance();
    $query=$ci->db->query('select count(id) as total_ring from cg_church_ring where church_id = '.$cid.'');
	$result=$query->result_array();
        return $result['0']['total_ring'];
        
}
function get_church_ring_member_total_by_church_ID($cid){
     $ci=& get_instance();
    $query=$ci->db->query('select count(DISTINCT i.id) as total_ring_member from cg_church_ring_invited_user as i , cg_church_ring as r where r.id = i.i_ring_id AND r.church_id = '.$cid.' AND i.i_joined = 1');
	$result=$query->result_array();
        return $result['0']['total_ring_member']; 
}

function get_church_ring_post_total_by_church_ID($cid){
    $ci=& get_instance();
    $query=$ci->db->query('select count(DISTINCT p.id) as total_ring_post from cg_church_ring_post as p , cg_church_ring as r where r.id = p.i_ring_id AND r.church_id = '.$cid.'');
	$result=$query->result_array();
        return $result['0']['total_ring_post'];  
}
function get_church_prayer_group_name_by_grp_id($gid){
     $ci=& get_instance();
    $query=$ci->db->query('select *  from cg_church_prayer_group where id = "'.$gid.'"');
	$result=$query->result_array();
        return $result['0']['s_group_name'];
}

function check_prayer_councillor($memid) {
    $ci = & get_instance();
    $sql = $ci->db->query("select count(id) As councillor_ct from cg_users where id='" . $memid . "' and is_councillor='1'");
    //echo $sql;
    $res = $sql->result_array();
    //pr($res);exit;
    return $res['0']['councillor_ct'];
}

/** get all members by church id **/
function get_member_by_id($cid)
{
	$CI = & get_instance();
    $query=$CI->db->query('select *,u.id AS mid, CONCAT(u.s_first_name, " ", u.s_last_name) AS member_name,cm.id AS cmid from cg_church_member AS cm 
            LEFT JOIN cg_users AS u ON cm.member_id=u.id WHERE cm.church_id = "'.$cid.'" AND cm.is_deleted=0 AND cm.is_blocked=1 AND cm.is_approved = 1 ORDER BY member_name  ASC ');
	$members=$query->result();
	return $members;
}

/**get viewed by post id **/

function get_viewed($feed,$member)
{
	$ci=&get_instance();
	$query=$ci->db->query("select count(feed_id) as ct from cg_church_post_privacy_settings where feed_id='".$feed."' and viewed_by='".$member."'");
	$cts=$query->result();
	//pr($cts);
	if($cts[0]->ct > 0)
	{
		return 'selected';
	}
	else
	{
		return '';
	}
}
function get_church_ring_category_name_by_id($id) {

    $ci = get_instance();
    $res = $ci->db->query("SELECT s_category_name
								 FROM cg_church_ring_category AS subcat WHERE  subcat.id = {$id}");
    $info = $res->result_array();
    return $info[0]['s_category_name'];
}
function check_church_member_by_ring_id($ring, $uid) {
    $ci = & get_instance();
    $sql = $ci->db->query('select count(*) as count from cg_church_ring_invited_user where i_ring_id=' . $ring . ' and i_invited_id=' . $uid . ' and i_joined = "1"');
    //echo $sql;
    $res = $sql->result_array();
    if ($res['0']['count'] > 0) {
        return true;
    } else
        return false;
}
function get_prayer_group_member_count_by_grp_id($gid){
   $ci = & get_instance();
    $sql = $ci->db->query('select count(*) as count from cg_church_prayer_group_members where i_prayer_group_id="' . $gid . '" and s_status="accepted"');
    //echo $sql;
    $res = $sql->result_array();
    return $res['0']['count'];
//    if ($res['0']['count'] > 0) {
//        return true;
//    } else
//        return false;  
}
function get_prayer_group_post_count_by_grp_id($gid){
   $ci = & get_instance();
    $sql = $ci->db->query('select count(*) as count from cg_church_prayer_group_post where i_prayer_group_id="' . $gid . '" ');
    $res = $sql->result_array();
    return $res['0']['count'];
}

function get_video_from_url($url,$width,$height)
{
    $CI =& get_instance();
    $CI->load->library('embed_video');
    $config['zend_library_path'] = APPPATH . "libraries/Zend/";
    $config['video_url'] = ($url);
    $CI->embed_video->initialize($config);
    $CI->embed_video->prepare();
    $image_source = $CI->embed_video->get_player($width, $height);
    return $image_source;
}
function get_blog_info_by_id($id) {

    $ci = get_instance();
    $ci->load->model('my_blog_model');
    $info = $ci->my_blog_model->get_by_id($id);
    return $info;
}

function get_userinfo_for_newsfeed($i_user_id = NULL) {
    try {
        $ci = & get_instance();
        $sql = $ci->db->query("select CONCAT(u.s_first_name,' ',u.s_last_name) AS s_profile_name,
            u.s_profile_photo,u.e_gender from cg_users AS u where id='" . $i_user_id . "'");
        //echo $sql;
        $res = $sql->result_array();
        //pr($res);exit;
    return $res['0'];
    } catch (Exception $err_obj) {
        show_error($err_obj->getMessage());
    }
}
function check_prayer_partner_quality($user_id){
    try{
        $ci = & get_instance();
        
         $query = $ci->db->get_where('cg_church', array('ch_admin_id' => $user_id ));
        $result = $query->result();
//pr($result,1);
         $numrow_superadmin = $query->num_rows();  
         
         $sql_churchmember = "SELECT *,ch.id AS chid FROM cg_church AS ch,cg_church_member AS chm WHERE ch.id=chm.church_id 
              AND chm.is_approved=1 AND chm.member_id='".$user_id."' AND chm.is_leave = 0 AND chm.is_blocked = 1";

              $query_churchmember = $ci->db->query($sql_churchmember);
              $numrowmember = $query_churchmember->num_rows();
              if($numrow_superadmin > 0 || $numrowmember > 0 ){
                  $res = 1;
              }
        
   
        
              return $res;
    } catch (Exception $err_obj){
         show_error($err_obj->getMessage());
    }
}
function get_user_id_byemail($email){
          $ci = & get_instance();
        
         $query = $ci->db->get_where('cg_users', array('s_email' => $email ));
        $info = $query->result_array();
        return $info[0]['id'];
}