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

class My_profile extends Base_controller {

    // Error Messages for Required Fields...
    private $required_fields = array('txt_email', 'txt_gender',
        'day', 'month', 'year', 'txt_lname', 'txt_fname',
        'txt_city', 'txt_state', 'txt_country',
        'txt_website', 'txt_title', 'txt_profile_url'
    );
    private $required_basic_fields = array('txt_lang', 'txt_about',
        'txt_cname', 'txt_caddress', 'txt_ccity', 'txt_cstate', 'txt_ccountry',
        'txt_cpostcode', 'txt_cphone', 'txt_denomination'
    );
    private $required_edu_fields = array('txt_school_name[]', 'txt_school_city[]', 'txt_school_state[]',
        'txt_school_country[]', 'txt_school_year[]', 'txt_school_degree[]'
    );
    private $required_work_fields = array('txt_work_company', 'txt_work_city', 'txt_work_state', 'txt_work_country', 'txt_work_position');

    public function __construct() {
        try {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); //put this code on those pages which are not accessable by guest user
            $this->upload_path = BASEPATH . '../uploads/user_profile_image/';
            # loading reqired model & helpers...
            $this->load->model('users_model');
            $this->load->library('form_validation');
            $this->load->model('education_model');
            $this->load->model('work_model');
            $this->load->model('skill_model');
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    # index function definition...

    public function index() {
        try {
            $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
            $posted = array();
            $this->data["posted"] = $posted; /* don't change */
            $data = $this->data;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');


            parent::_add_js_arr(array(
                /*'js/ddsmoothmenu.js',
                'js/switch.js',
                'js/animate-collapse.js',
                'js/lightbox.js',
                //'js/jquery.dd.js',
                'js/jquery-ui-1.8.2.custom.min.js',
                'js/stepcarousel.js',
                'js/jquery.form.js',
                'js/jquery/JSON/json2.js',*/
                'js/production/manage_my_profile.js',
                'js/production/tweet_utilities.js'
            ));

            parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
                'css/dd.css'));
            # adjusting header & footer sections [End]...
            $data['page_view_type'] = 'myaccount';
            $arr_profile_info = $this->users_model->fetch_this($i_profile_id);

            $where = " i_country_id='" . $arr_profile_info["i_country_id"] . "'";
            $data['state'] = makeOptionState($where, encrypt($arr_profile_info["i_state_id"]));
            $where1 = " i_state_id='" . $arr_profile_info["i_state_id"] . "'";
            $data['city'] = makeOptionCity($where1, encrypt($arr_profile_info["i_city_id"]));

            $data['arr_profile_info'] = $arr_profile_info;


            $cwhere = " i_country_id='" . $arr_profile_info["i_church_country_id"] . "'";
            $data['cstate'] = makeOptionState($cwhere, encrypt($arr_profile_info["i_church_state_id"]));
            $cwhere1 = " i_state_id='" . $arr_profile_info["i_church_state_id"] . "'";
            $data['ccity'] = makeOptionCity($cwhere1, encrypt($arr_profile_info["i_church_city_id"]));

            //pr($data['arr_profile_info']);
            # view file...
            $VIEW_PG_FILE = "my_profile.phtml";
            $VIEW = "logged/{$VIEW_PG_FILE}";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    # modify member profile by ajax.

    public function modify_my_profile_personal_info_ajax() {
        # logged user-id...
        $logged_user_id = intval(decrypt($this->session->userdata('user_id')));
        // 1st, adjusting err-messages part accordingly...
        $arr_messages = array();
        $arr_values = $_POST;
        $err_flag = 0;

        // After form submission...
        if ($this->input->post('is_submitted', true) != "") {

            $posted["s_time"] = $this->input->post("time_zone");
            $posted["txt_title"] = ucfirst(trim($this->input->post("txt_title")));
            $posted["txt_lname"] = ucfirst(trim($this->input->post("txt_lname")));
            $posted["txt_fname"] = ucfirst(trim($this->input->post("txt_fname")));

            $posted["day"] = trim($this->input->post("day"));
            $posted["month"] = trim($this->input->post("month"));
            $posted["year"] = trim($this->input->post("year"));

            $posted["txt_gender"] = trim($this->input->post("txt_gender"));
            /*
              //// New "phone-number" field...
              $posted['txt_phone_number'] = $this->input->post('txt_phone_number', true); */

            $posted["txt_city"] = trim($this->input->post("txt_city"));
            $posted["txt_state"] = trim($this->input->post("txt_state"));
            $posted["txt_country"] = trim($this->input->post("txt_country"));


            $posted["txt_website"] = prep_url(trim($this->input->post("txt_website")));

            $posted["txt_profile_url"] = (trim($this->input->post("txt_profile_url")));
            $posted["txt_net_pal"] = (trim($this->input->post("txt_net_pal")));
            $posted["txt_prayer_ptnr"] = (trim($this->input->post("txt_prayer_ptnr")));
            /*             * ********************************** */


            /*             * ********************************** */

            #pr($posted);// exit;

            $this->form_validation->set_message('required', "* Required");
            $this->form_validation->set_message('valid_email', "must contain a valid email address.");
            $this->form_validation->set_message('matches', " %s " . "field does not match" . " %s");


            $this->form_validation->set_rules('txt_title', "Title", 'trim|required');

            $this->form_validation->set_rules('txt_lname', "Last Name", 'trim|required');
            $this->form_validation->set_rules('txt_fname', "First Name", 'trim|required');
            $this->form_validation->set_rules('txt_profile_url', "Profile Suffix", 'trim|required');
            $this->form_validation->set_rules('txt_website', "Website", 'trim|required|callback_valid_url');
            $this->form_validation->set_rules('txt_gender', "Gender", 'trim|required|callback_valid_gender');

            $this->form_validation->set_rules('txt_city', 'City', 'trim|required');
            $this->form_validation->set_rules('txt_country', 'Country', 'required');
            $this->form_validation->set_rules('txt_state', 'State', 'trim|required');

            $this->form_validation->set_rules('day', 'Birth date', 'trim|required');
            $this->form_validation->set_rules('month', 'Birth date', 'trim|required');
            $this->form_validation->set_rules('year', 'Birth date', 'trim|required');


            ##### profile pic upload #######

            /* if( $_FILES['txt_profile_pic']['name']=='' ) {
              $arr_messages['profile_pic'] = "* required";
              } */

            if (isset($_FILES['txt_profile_pic']['name']) && $_FILES['txt_profile_pic']['name'] != '') {
                preg_match('/(^.*)\.([^\.]*)$/', $_FILES['txt_profile_pic']['name'], $matches);
                $ext = "";
                if (count($matches) > 0) {
                    $ext = $matches[2];
                    $original_name = $matches[1];
                } else
                    $original_name = 'txt_profile_pic';


                if (!in_array($ext, $this->config->item('VALID_IMAGE_EXT'))) {
                    $arr_messages['txt_profile_pic'] = "supported extensions are " . implode(' , ', $this->config->item('VALID_IMAGE_EXT'));
                } else if ($_FILES['txt_profile_pic']['size'] > $this->config->item('MAX_UP_FILE_SIZE') * 1024 * 1024) {
                    $data["txt_profile_pic"] = "Maximum file upload size is " . $this->config->item('MAX_UP_FILE_SIZE') . " MB.";
                }
            }



            #### end #######################
            # traping error messages #
            $arr_messages = array();

            if ($posted["txt_title"] == '-1') {
                $arr_messages['title'] = "* Required Field.";
            }
            if ($posted["txt_fname"] == '') {
                $arr_messages['fname'] = "* Required Field.";
            }
            if ($posted["txt_lname"] == '') {
                $arr_messages['lname'] = "* Required Field.";
            }

            if ($posted["day"] == '' || $posted["month"] == '' || $posted["year"] == '') {
                $arr_messages['dob'] = "* Required Field.";
            }
            /* if($posted["month"] == ''){
              $arr_messages['month'] = "* Required Field.";
              }
              if($posted["year"] == ''){
              $arr_messages['year'] = "* Required Field.";
              } */
            if ($posted["txt_gender"] == '-1') {
                $arr_messages['gender'] = "* Required Field.";
            }


            //echo $this->valid_gender($posted["txt_gender"]);
            /* if($posted["txt_website"]==''){
              $arr_messages['website'] = "* Required Field.";
              } */ elseif ($this->valid_url($posted["txt_website"]) == FALSE) {
                $arr_messages['website'] = "* Not a valid URL.";
            }

            if ($posted["txt_city"] == '-1') {
                $arr_messages['city'] = "* Required Field.";
            }
            if ($posted["txt_state"] == '-1') {
                $arr_messages['state'] = "* Required Field.";
            }
            if ($posted["txt_country"] == '-1') {
                $arr_messages['country'] = "* Required Field.";
            }



            if ($posted["txt_profile_url"] == '') {
                $arr_messages['profile_url'] = "* Required Field.";
            }


//             if ($posted["s_time"] == -1) {
//                $arr_messages['s_time'] = "* Required Field.";
//            }



            /* if ($this->form_validation->run() == FALSE ) */
            if (count($arr_messages) > 0) {
                echo json_encode(array('result' => 'error',
                    'arr_messages' => $arr_messages));
                exit;
            } else {
                $info['s_time'] = get_formatted_string($posted["s_time"]);
                $info['e_title'] = get_formatted_string($posted['txt_title']);
                $info['s_first_name'] = get_formatted_string($posted['txt_fname']);
                $info['s_last_name'] = get_formatted_string($posted['txt_lname']);
                $info['e_gender'] = $posted["txt_gender"];

                $info['dt_dob'] = date('Y-m-d', mktime(0, 0, 0, $posted["month"], $posted["day"], $posted["year"]));

                $info['i_city_id'] = intval(decrypt(trim($posted['txt_city'])));
                $info['i_state_id'] = intval(decrypt(trim($posted['txt_state'])));
                $info['i_country_id'] = intval(decrypt(trim($posted['txt_country'])));
                $info['e_want_net_pal'] = ($posted["txt_net_pal"] == 'Y') ? 'Y' : 'N';
                $info['s_website'] = $posted["txt_website"];
                $info['e_want_prayer_partner'] = ($posted["txt_prayer_ptnr"] == 'Y') ? 'Y' : 'N';
                $info['s_profile_url_suffix'] = get_formatted_string($posted["txt_profile_url"]);
                /*                 * ************************** */
                $posted["txt_lang"] = trim($this->input->post("txt_lang"));
                $posted["txt_about"] = trim($this->input->post("txt_about"));
                $posted["txt_cname"] = (trim($this->input->post("txt_cname")));
                $posted["txt_caddress"] = (trim($this->input->post("txt_caddress")));
                $posted["txt_ccity"] = (trim($this->input->post("txt_ccity")));

                $posted["txt_cstate"] = trim($this->input->post("txt_cstate"));
                $posted["txt_ccountry"] = trim($this->input->post("txt_ccountry"));
                $posted["txt_cpostcode"] = trim($this->input->post("txt_cpostcode"));

                $posted["txt_cphone"] = trim($this->input->post("txt_cphone"));

                $posted["txt_denomination"] = intval(decrypt(($this->input->post("txt_denomination"))));


                $info['s_languages'] = get_formatted_string($posted['txt_lang']);
                $info['s_about_me'] = get_formatted_string($posted['txt_about']);
                $info['s_church_name'] = get_formatted_string($posted['txt_cname']);
                $info['s_church_address'] = get_formatted_string($posted["txt_caddress"]);


                $info['s_church_city'] = get_formatted_string($posted['txt_ccity']);
                $info['s_church_state'] = get_formatted_string($posted['txt_cstate']);
                $info['i_church_country_id'] = intval(decrypt(trim($posted['txt_ccountry'])));
                $info['i_church_state_id'] = intval(decrypt(trim($posted['txt_cstate'])));
                $info['i_church_city_id'] = intval(decrypt(trim($posted['txt_ccity'])));

                $info['s_church_phone'] = $posted["txt_cphone"];
                $info['s_church_postcode'] = get_formatted_string($posted["txt_cpostcode"]);
                $info['i_id_denomination'] = ($posted["txt_denomination"]);
                $info['dt_updated_on'] = get_db_datetime();
                /*                 * ************************** */
                if (isset($_FILES['txt_profile_pic']['name']) && $_FILES['txt_profile_pic']['name'] != '') {
                    $info['s_profile_photo'] = $this->_upload_profile_image();
                }

                $info['dt_updated_on'] = get_db_datetime();

                $info['s_age'] = get_age_from_dob($info['dt_dob']);

                //dump($info);
                # modify user-profile info...
                //$this->users_model->edit_info($info, $logged_user_id);
                // echo $this->db->last_query();
                $REDIRECT = "my-profile.html";
                $SUCCESS_MSG = "Account updated successfully";

                //echo "profile img : ".$info['s_profile_photo'];
                if ($info['s_profile_photo'])
                    $profile_img = get_profile_image_of_user('thumb',$info['s_profile_photo'],$info['e_gender']);
                $is_abusive_fnme = 0;
                $is_abusive_lnme = 0;
                $is_abusive_web = 0;
                $is_abusive_about = 0;
                $is_abusive_church_name = 0;
                $is_abusive_church_address = 0;
                if ($info['s_first_name']) {
                    $is_abusive_fnme = check_abusive_words($info['s_first_name']);
                }
                if ($info['s_last_name']) {
                    $is_abusive_lnme = check_abusive_words($info['s_last_name']);
                }
                if ($info['s_website']) {
                    $is_abusive_web = check_abusive_words($info['s_website']);
                }

                if ($posted["txt_about"]) {
                    $is_abusive_about = check_abusive_words($posted["txt_about"]);
                }
                if ($posted["txt_cname"]) {
                    $is_abusive_church_name = check_abusive_words($posted["txt_cname"]);
                }
                if ($posted["txt_caddress"]) {
                    $is_abusive_church_address = check_abusive_words($posted["txt_caddress"]);
                }

                if ($is_abusive_fnme > 0 || $is_abusive_lnme > 0 || $is_abusive_web > 0 || $is_abusive_about > 0 || $is_abusive_church_name > 0 || $is_abusive_church_address > 0) {
                    echo json_encode(array('result' => 'error',
                        'redirect' => $REDIRECT,
                        'msg' => "Abusive words are not allowed",
                        'profile_img' => $profile_img,
                        'is_wan_prayer_partner' => $info['e_want_prayer_partner']
                    ));
                } else if ($posted["s_time"] == -1) {
                    echo json_encode(array('result' => 'error',
                        'redirect' => $REDIRECT,
                        'msg' => "Please Select Timezone",
                        'profile_img' => $profile_img,
                        'is_wan_prayer_partner' => $info['e_want_prayer_partner']
                    ));
                } else {
                    // pr($info, 1);
                    $this->users_model->edit_info($info, $logged_user_id);
                    echo json_encode(array('result' => 'success',
                        'redirect' => $REDIRECT,
                        'msg' => $SUCCESS_MSG,
                        'profile_img' => $profile_img,
                        'is_wan_prayer_partner' => $info['e_want_prayer_partner']
                    ));
                    exit;
                }
            }
        }   // check if submitted...
    }

    public function modify_my_profile_basic_info_ajax() {
        # logged user-id...
        $logged_user_id = intval(decrypt($this->session->userdata('user_id')));
        // 1st, adjusting err-messages part accordingly...
        $arr_messages = array();
        $arr_values = $_POST;
        $err_flag = 0;

        // After form submission...
        if ($this->input->post('is_basic_submitted', true) != "") {


            $posted["txt_lang"] = trim($this->input->post("txt_lang"));
            $posted["txt_about"] = trim($this->input->post("txt_about"));
            $posted["txt_cname"] = (trim($this->input->post("txt_cname")));
            $posted["txt_caddress"] = (trim($this->input->post("txt_caddress")));
            $posted["txt_ccity"] = (trim($this->input->post("txt_ccity")));

            $posted["txt_cstate"] = trim($this->input->post("txt_cstate"));
            $posted["txt_ccountry"] = trim($this->input->post("txt_ccountry"));
            $posted["txt_cpostcode"] = trim($this->input->post("txt_cpostcode"));

            $posted["txt_cphone"] = trim($this->input->post("txt_cphone"));

            $posted["txt_denomination"] = intval(decrypt(($this->input->post("txt_denomination"))));


            $info['s_languages'] = get_formatted_string($posted['txt_lang']);
            $info['s_about_me'] = get_formatted_string($posted['txt_about']);
            $info['s_church_name'] = get_formatted_string($posted['txt_cname']);
            $info['s_church_address'] = get_formatted_string($posted["txt_caddress"]);


            $info['s_church_city'] = get_formatted_string($posted['txt_ccity']);
            $info['s_church_state'] = get_formatted_string($posted['txt_cstate']);
            $info['i_church_country_id'] = intval(decrypt(trim($posted['txt_ccountry'])));
            $info['i_church_state_id'] = intval(decrypt(trim($posted['txt_cstate'])));
            $info['i_church_city_id'] = intval(decrypt(trim($posted['txt_ccity'])));

            $info['s_church_phone'] = $posted["txt_cphone"];
            $info['s_church_postcode'] = get_formatted_string($posted["txt_cpostcode"]);
            $info['i_id_denomination'] = ($posted["txt_denomination"]);
            $info['dt_updated_on'] = get_db_datetime();

            # modify user-profile info...
            // $this->users_model->edit_info($info, $logged_user_id);
            //echo $this->db->last_query();
            $REDIRECT = "my-profile.html";
            $SUCCESS_MSG = "Account updated successfully";
            $is_abusive_about = 0;
            $is_abusive_church_name = 0;
            $is_abusive_church_address = 0;
            if ($posted["txt_about"]) {
                $is_abusive_about = check_abusive_words($posted["txt_about"]);
            }
            if ($posted["txt_cname"]) {
                $is_abusive_church_name = check_abusive_words($posted["txt_cname"]);
            }
            if ($posted["txt_caddress"]) {
                $is_abusive_church_address = check_abusive_words($posted["txt_caddress"]);
            }
            if ($is_abusive_about > 0 || $is_abusive_church_name > 0 || $is_abusive_church_address > 0) {
                echo json_encode(array('result' => 'error',
                    'redirect' => $REDIRECT,
                    'msg' => "Abusive words are not allowed"));
                exit;
            } else {
                $this->users_model->edit_info($info, $logged_user_id);
                echo json_encode(array('result' => 'success',
                    'redirect' => $REDIRECT,
                    'msg' => $SUCCESS_MSG));
                exit;
            }
        }   // check if submitted...
    }

    public function modify_my_profile_edu_info_ajax() {
        # logged user-id...
        $logged_user_id = intval(decrypt($this->session->userdata('user_id')));
        // 1st, adjusting err-messages part accordingly...
        $arr_messages = array();
        $arr_values = $_POST;
        $err_flag = 0;

        // After form submission...
        if ($this->input->post('is_basic_submitted', true) != "") {


            $DELETED_IDS_ARR = array();
            $ids_to_be_deleted = $this->input->post("deleted_edu_ids");

            if (!empty($ids_to_be_deleted))
                $DELETED_IDS_ARR = explode('#', $ids_to_be_deleted);

            $arr_institute_name = array();
            $arr_institute_city = array();
            $arr_institute_state = array();
            $arr_institute_country = array();
            $arr_institute_yrar = array();
            $arr_institute_degree = array();

            $arr_institute_name = $this->input->post("txt_school_name");
            $arr_institute_city = $this->input->post("txt_scity");
            $arr_institute_state = $this->input->post("txt_sstate");
            $arr_institute_country = $this->input->post("txt_school_country");
            $arr_institute_year = $this->input->post("txt_school_year");
            $arr_institute_degree = $this->input->post("txt_school_degree");
            $arr_db_id = $this->input->post("db_id");
            #pr($arr_institute_city);
            #pr($arr_institute_state);
            #pr($arr_db_id );
            /*
              $ar=array(count($arr_institute_name),
              count($arr_institute_city),
              count($arr_institute_state),
              count($arr_institute_country),
              count($arr_institute_year),
              count($arr_institute_degree)
              );
              $max_arr=max($ar);
             */
            //$total_divs=$this->input->post('total_edu_divs');
            $total_divs = count($arr_institute_name);
            $info = array();

//            for ($i = 0; $i < $total_divs; $i++) {
//
//                ## CHECKING BLANK ARRAY
//                if ($arr_institute_name[$i] != '' || $arr_institute_city[$i] != '-1' || $arr_institute_state[$i] != '-1' || $arr_institute_country[$i] != '-1' || $arr_institute_year[$i] != '' || $arr_institute_degree[$i] != '') {
//
//                    if (!empty($DELETED_IDS_ARR) && in_array($arr_db_id[$i], $DELETED_IDS_ARR)) {
//                        //echo "Delete : ". $arr_db_id[$i] ."\r\n";
//
//                        $this->education_model->delete_info_db($arr_db_id[$i]);
//                        #echo $this->db->last_query();
//                    } else {
//                        //echo "update..".$arr_db_id[$i];
//                        $info['s_school_name'] = get_formatted_string($arr_institute_name[$i]);
//                        $info['s_school_city'] = intval(decrypt(trim($arr_institute_city[$i])));
//                        $info['s_school_state'] = intval(decrypt(trim($arr_institute_state[$i])));
//                        $info['s_school_country'] = intval(decrypt(trim($arr_institute_country[$i])));
//                        $info['i_class_year'] = get_formatted_string($arr_institute_year[$i]);
//                        $info['s_degree'] = get_formatted_string($arr_institute_degree[$i]);
//                        $info['i_user_id'] = $logged_user_id;
//
//                        #pr($info);
//                        #echo "db id : ".$arr_db_id[$i];
//                        # modify user-profile info...                    
//                        $result = $this->education_model->edit_edu_info($info, $logged_user_id, $arr_db_id[$i]);
//                        #echo $this->db->last_query().' ==  ';
//                        //pr($result);
//                    }
//                }   // end of 'if any field in a div exists
//                else {
//                    $this->education_model->delete_info_db($arr_db_id[$i]);
//                }
//            }

            $REDIRECT = "my-profile.html";
            $SUCCESS_MSG = "Account updated successfully";
            $is_abusive_school = 0;
            $is_abusive_church_year = 0;
            $is_abusive_church_degree = 0;
            foreach ($arr_institute_name as $value) {
                if (check_abusive_words($value) > 0) {
                    $is_abusive_school++;
                }
            }
            foreach ($arr_institute_year as $value) {
                if (check_abusive_words($value) > 0) {
                    $is_abusive_church_year++;
                }
            }
            foreach ($arr_institute_degree as $value) {
                if (check_abusive_words($value) > 0) {
                    $is_abusive_church_degree++;
                }
            }
            if ($is_abusive_school > 0 || $is_abusive_church_year > 0 || $is_abusive_church_degree > 0) {
                echo json_encode(array('result' => 'error',
                    'redirect' => $REDIRECT,
                    'msg' => "Abusive words are not allowed"));
                exit;
            } else {
                for ($i = 0; $i < $total_divs; $i++) {

                    ## CHECKING BLANK ARRAY
                    if ($arr_institute_name[$i] != '' || $arr_institute_city[$i] != '-1' || $arr_institute_state[$i] != '-1' || $arr_institute_country[$i] != '-1' || $arr_institute_year[$i] != '' || $arr_institute_degree[$i] != '') {

                        if (!empty($DELETED_IDS_ARR) && in_array($arr_db_id[$i], $DELETED_IDS_ARR)) {
                            //echo "Delete : ". $arr_db_id[$i] ."\r\n";

                            $this->education_model->delete_info_db($arr_db_id[$i]);
                            #echo $this->db->last_query();
                        } else {
                            //echo "update..".$arr_db_id[$i];
                            $info['s_school_name'] = get_formatted_string($arr_institute_name[$i]);
                            $info['s_school_city'] = intval(decrypt(trim($arr_institute_city[$i])));
                            $info['s_school_state'] = intval(decrypt(trim($arr_institute_state[$i])));
                            $info['s_school_country'] = intval(decrypt(trim($arr_institute_country[$i])));
                            $info['i_class_year'] = get_formatted_string($arr_institute_year[$i]);
                            $info['s_degree'] = get_formatted_string($arr_institute_degree[$i]);
                            $info['i_user_id'] = $logged_user_id;

                            #pr($info);
                            #echo "db id : ".$arr_db_id[$i];
                            # modify user-profile info...                    
                            $result = $this->education_model->edit_edu_info($info, $logged_user_id, $arr_db_id[$i]);
                            #echo $this->db->last_query().' ==  ';
                            //pr($result);
                        }
                    }   // end of 'if any field in a div exists
                    else {
                        $this->education_model->delete_info_db($arr_db_id[$i]);
                    }
                }
                echo json_encode(array('result' => 'success',
                    'redirect' => $REDIRECT,
                    'msg' => $SUCCESS_MSG));
                exit;
            }
        }   // check if submitted...

        $REDIRECT = "my-profile.html";
        $SUCCESS_MSG = "Sorry! update not successful.";

        echo json_encode(array('result' => 'failure',
            'redirect' => $REDIRECT,
            'msg' => $SUCCESS_MSG));
        exit;
    }

    public function delete_edu_info() {

        $db_id = $this->input->post('db_id');
        if ($db_id)
            $result = $this->education_model->delete_info_db($db_id);
        else {
            echo json_encode(array(
                'result' => 'failure',
                'msg' => 'Removed.'
            ));
            exit;
        }
        if ($result)
            echo json_encode(array(
                'result' => 'success',
                'msg' => 'Successfully deleted.'
            ));
        else
            echo json_encode(array(
                'result' => 'failure',
                'msg' => 'Sorry. Can not be deleted.'
            ));
    }

    //=================================== work section ============================================
    public function modify_my_profile_work_info_ajax() {
        # logged user-id...
        $logged_user_id = intval(decrypt($this->session->userdata('user_id')));
        // 1st, adjusting err-messages part accordingly...
        $arr_messages = array();
        $arr_values = $_POST;
        $err_flag = 0;

        // After form submission...
        if ($this->input->post('is_work_submitted', true) != "") {

            //echo 11;

            $DELETED_IDS_ARR = array();
            $ids_to_be_deleted = $this->input->post("deleted_work_ids");

            if (!empty($ids_to_be_deleted))
                $DELETED_IDS_ARR = explode('#', $ids_to_be_deleted);


            $arr_db_id = array();

            $arr_work_company = array();
            $arr_work_city = array();
            $arr_work_state = array();
            $arr_work_country = array();
            $arr_work_position = array();

            $arr_work_month_from = array();
            $arr_work_year_from = array();
            $arr_work_month_to = array();
            $arr_work_year_to = array();

            //$arr_is_current_employer=   array();

            $arr_db_id = $this->input->post('db_id');

            $arr_work_company = $this->input->post("txt_work_company");
            $arr_work_city = $this->input->post("txt_work_city");
            $arr_work_state = $this->input->post("txt_work_state");
            $arr_work_country = $this->input->post("txt_work_country");
            $arr_work_position = $this->input->post("txt_work_position");

            $arr_work_month_from = $this->input->post("mnth_from");
            $arr_work_year_from = $this->input->post("year_from");
            $arr_work_month_to = $this->input->post("mnth_to");
            $arr_work_year_to = $this->input->post("year_to");


            if ($this->input->post('is_current_employer'))
                $current_employer_record_id = $this->input->post('is_current_employer');
            //echo "if radio checked : ".$current_employer_record_id ;
            // pr($arr_work_month_to);
            // pr($arr_work_month_from);
            $total_work_divs = count($arr_work_company);



            // echo $max_arr=max($ar);
            $info = array();
            $no_month_to = count($arr_work_month_to);
            for ($i = 0; $i < $total_work_divs; $i++) {
                #$info = array();
                ## CHECKING BLANK ARRAY
                if (trim($arr_work_company[$i]) != '' || trim($arr_work_city[$i]) != '' || trim($arr_work_state[$i]) != '' || trim($arr_work_country[$i]) != '-1') {

                    if (!empty($DELETED_IDS_ARR) && in_array($arr_db_id[$i], $DELETED_IDS_ARR)) {

                        $this->work_model->delete_info_db($arr_db_id[$i]);
                    } else {

                        $record_id = $arr_db_id[$i];



                        $info['s_employer_name'] = get_formatted_string($arr_work_company[$i]);
                        $info['s_employer_city'] = get_formatted_string($arr_work_city[$i]);
                        $info['s_employer_state'] = get_formatted_string($arr_work_state[$i]);
                        $info['s_employer_country'] = intval(decrypt(trim($arr_work_country[$i])));
                        $info['s_position'] = get_formatted_string($arr_work_position[$i]);
                        $info['e_is_current_employer'] = ($arr_db_id[$i] == $current_employer_record_id) ? 'yes' : 'no';

                        $info['s_experience_year_from'] = get_formatted_string($arr_work_year_from[$i]);
                        $info['i_experience_month_from'] = get_formatted_string($arr_work_month_from[$i]);

                        if ($info['e_is_current_employer'] == 'no') {
                            $info['s_experience_year_to'] = get_formatted_string($arr_work_year_to[$i]);
                            $info['i_experience_month_to'] = get_formatted_string($arr_work_month_to[$i]);
                        } else {
                            $info['s_experience_year_to'] = '-1';
                            $info['i_experience_month_to'] = '-1';
                        }
                        $info['i_user_id'] = $logged_user_id;

                        //pr($info,1);
                        # modify user-profile info... 
                        //echo "edit mode.."; exit;
                        $result = $this->work_model->edit_work_info($info, $logged_user_id, $arr_db_id[$i]);
                        //exit;
                        // echo '{$i} query: '.$this->db->last_query(); 
                    }
                } else {
                    //echo "delete mode..";    exit;
                    $result = $this->work_model->delete_info_db($arr_db_id[$i]);
                }
            }
            // echo 'here';

            $REDIRECT = "my-profile.html";
            $SUCCESS_MSG = "Account updated successfully";

            echo json_encode(array('result' => 'success',
                'redirect' => $REDIRECT,
                'msg' => $SUCCESS_MSG));
            exit;
        }   // check if submitted...
    }

// end of function modify_my_profile_work_info_ajax

    public function delete_work_info() {
        $db_id = $this->input->post('db_id');

        if ($db_id) {
            $result = $this->work_model->delete_info_db($db_id);
        } else {
            echo json_encode(array(
                'result' => 'failure',
                'msg' => 'Removed.'
            ));
            exit;
        }


        if ($result)
            echo json_encode(array(
                'result' => 'success',
                'msg' => 'Successfully deleted.'
            ));
        else
            echo json_encode(array(
                'result' => 'failure',
                'msg' => 'Sorry. Can not be deleted.'
            ));
    }

    //=================================== end of work section ============================================
    //=================================== skill section ============================================

    public function modify_my_profile_skill_info_ajax() {
        # logged user-id...
        $logged_user_id = intval(decrypt($this->session->userdata('user_id')));
        // 1st, adjusting err-messages part accordingly...
        $arr_messages = array();
        $arr_values = $_POST;
        $err_flag = 0;

        #echo "testing before completing ajax operation"; exit;
        // After form submission...
        if ($this->input->post('is_basic_submitted', true) != "") {

            $DELETED_IDS_ARR = array();
            $ids_to_be_deleted = $this->input->post("deleted_skill_ids");

            if (!empty($ids_to_be_deleted))
                $DELETED_IDS_ARR = explode('#', $ids_to_be_deleted);

            $arr_skill_name = array();
            $arr_db_id = array();

            $arr_skill_name = $this->input->post("txt_skill");

            $arr_db_id = $this->input->post("skill_db_id");

            # pr($arr_db_id  ,1);
            //$total_divs=$this->input->post('total_skill_divs');
            $total_divs = count($arr_skill_name);
            $info = array();
            #echo $total_divs;exit;
//            for ($i = 0; $i < $total_divs; $i++) {
//
//                ## CHECKING BLANK ARRAY
//                if (trim($arr_skill_name[$i]) != '') {
//                    if (!empty($DELETED_IDS_ARR) && in_array($arr_db_id[$i], $DELETED_IDS_ARR)) {
//
//                        $this->skill_model->delete_info_db($arr_db_id[$i]);
//                    } else {
//
//                        $info['s_name'] = get_formatted_string($arr_skill_name[$i]);
//                        $info['i_user_id'] = $logged_user_id;
//                        $info['id'] = $arr_db_id[$i];
//
//                        # modify user-profile info...                    
//                        $result = $this->skill_model->edit_skill_info($info, $logged_user_id, $arr_db_id[$i]);
//                        //echo $this->db->last_query().' ==  ';exit;
//                    }
//                }   // end of 'if any field in a div exists
//                else {
//                    $this->skill_model->delete_info_db($arr_db_id[$i]);
//                }
//            }
            //echo  $total_divs; exit;
            $REDIRECT = "my-profile.html";
            $SUCCESS_MSG = "Account updated successfully..";
            $is_abusive_skill = 0;
            foreach ($arr_skill_name as $value) {
                if (check_abusive_words($value) > 0) {
                    $is_abusive_skill++;
                }
            }
            if ($is_abusive_skill > 0) {
                echo json_encode(array('result' => 'error',
                    'redirect' => $REDIRECT,
                    'msg' => "Abusive Words are not allowed"));
                exit;
            } else {
                for ($i = 0; $i < $total_divs; $i++) {

                    ## CHECKING BLANK ARRAY
                    if (trim($arr_skill_name[$i]) != '') {
                        if (!empty($DELETED_IDS_ARR) && in_array($arr_db_id[$i], $DELETED_IDS_ARR)) {

                            $this->skill_model->delete_info_db($arr_db_id[$i]);
                        } else {

                            $info['s_name'] = get_formatted_string($arr_skill_name[$i]);
                            $info['i_user_id'] = $logged_user_id;
                            $info['id'] = $arr_db_id[$i];

                            # modify user-profile info...                    
                            $result = $this->skill_model->edit_skill_info($info, $logged_user_id, $arr_db_id[$i]);
                            //echo $this->db->last_query().' ==  ';exit;
                        }
                    }   // end of 'if any field in a div exists
                    else {
                        $this->skill_model->delete_info_db($arr_db_id[$i]);
                    }
                }
                echo json_encode(array('result' => 'success',
                    'redirect' => $REDIRECT,
                    'msg' => $SUCCESS_MSG));
                exit;
            }
        }   // check if submitted...

        $REDIRECT = "my-profile.html";
        $SUCCESS_MSG = "Sorry! update not successful.";

        echo json_encode(array('result' => 'failure',
            'redirect' => $REDIRECT,
            'msg' => $SUCCESS_MSG));
        exit;
    }

    public function delete_skill_info() {
        $db_id = $this->input->post('db_id');

        if ($db_id)
            $result = $this->skill_model->delete_info_db($db_id);
        else {
            echo json_encode(array(
                'result' => 'failure',
                'msg' => 'Removed.'
            ));
            exit;
        }


        if ($result)
            echo json_encode(array(
                'result' => 'success',
                'msg' => 'Successfully deleted.'
            ));
        else
            echo json_encode(array(
                'result' => 'failure',
                'msg' => 'Sorry. Can not be deleted.'
            ));
    }

    //=========================================== end of skill section ===============================================
    #### UPLOAD PROFILE PIC OF USER ######
    public function _upload_profile_image($prev_img = '', $filefieldname) {

        parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by non logged user and admin
        #pr($_FILES);
        $fileElementName = 'txt_profile_pic';
        if (!empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] != '') {
            preg_match('/(^.*)\.([^\.]*)$/', $_FILES[$fileElementName]['name'], $matches);
            $ext = "";
            if (count($matches) > 0) {
                $ext = strtolower($matches[2]);
                $original_name = $matches[1];
            } else
                $original_name = 'image';


            $imagename = createImageName($original_name);

            if (test_file($this->upload_path . $imagename . '-thumb.' . $ext)) {
                for ($i = 0; test_file($this->upload_path . $imagename . '-' . $i . '-thumb.' . $ext); $i++) {
                    
                }

                $new_imagename = $imagename . '-' . $i;
            } else {
                $new_imagename = $imagename;
            }

            $this->imagename = $new_imagename;

            $this->upload_image = $this->upload_path . $new_imagename . '.' . $ext;
            #echo $this->upload_path.' === ';  echo $this->upload_image ;

            @move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $this->upload_image);



            $config['source_image'] = $this->upload_image;
            $config['thumb_marker'] = '-thumb';
            $config['crop'] = false;
            $config['crop_from'] = 'middle';
            $config['width'] = 60;
            $config['height'] = 60;
            $config['small_image_resize'] = 'bigger';

            resize_exact($config);

            $config = array();
            $config['source_image'] = $this->upload_image;
            $config['thumb_marker'] = '-main';
            $config['crop'] = false;
            $config['width'] = 144;
            $config['height'] = 144;
            $config1['crop_from'] = 'middle';
            $config['small_image_resize'] = 'no_resize';
            resize_exact($config);
            unset($config);

            $this->s_picture_path = $new_imagename . '.' . $ext;

            @unlink($this->upload_image); //Unlink the original image........
            //@unlink($this->upload_path.getThumbName($prev_img,'thumb')); //Unlink the prevoius image........

            return $this->s_picture_path;
        } else {
            return $prev_img; // Unchanged previous image
        }
    }

    public function valid_url($url) {

        if (!isValidURL($url) && $url != '') {
            $this->form_validation->set_message('valid_url', "Not a valid url");
            return FALSE;
        }

        return TRUE;
    }

    public function valid_gender($gender) {

        if ((ucfirst($gender) != "Female") && (ucfirst($gender) != "Male") && $gender != '') {
            //$this->form_validation->set_message('valid_gender', "Please enter gender properly.");
            return FALSE;
        }
        return TRUE;
    }

    public function valid_denomination($txt_denomination) {

        if ($txt_denomination == 'TUNOaFkzVT0=') {
            $this->form_validation->set_message('valid_denomination', "Please select denomination.");
            return FALSE;
        }
        return TRUE;
    }

    public function update_status() {
        $status = ($this->input->post('status'));
        $info['s_status_message'] = get_formatted_string($this->input->post('txt_status_msg'));
        $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        $this->users_model->chng_user_online_status($i_profile_id, $status);
        $this->users_model->edit_info($info, $i_profile_id);

        if (($status == '1')) {
            $s_status = '<img src="' . base_url() . 'images/icons/online.png" alt="" /> Online';
        } else if ($status == '2') {
            $s_status = '<img src="' . base_url() . 'images/icons/invisible.png" alt="" /> Invisible';
        } else if ($status == '3') {
            $s_status = '<img src="' . base_url() . 'images/icons/away.png" alt="" /> Away';
        } else
            $s_status = '<img src="' . base_url() . 'images/icons/offline.png" alt="" /> Offline';
        #$s_status =($status == 1 )?'Online':($status == 2)?'Invisible':($status == 3)?'Away':'Offline';
        echo json_encode(array('success' => true, 'msg' => 'your status updated successfully', 'status' => $s_status));
    }

    //========================================= ajax for cancelling =======================================
    //-------------------------------------------- skill -----------------------------------------------
    function ajax_skill_cancel() {
        $logged_user_id = intval(decrypt($this->session->userdata('user_id')));
        $arr_profile_info = $this->users_model->fetch_this($logged_user_id);
        $data['arr_profile_info'] = $arr_profile_info;

        $this->load->view('logged/ajax_cancelling_my_profile/skill_cancel.phtml', $data);
    }

    //----------------------------------------- work ----------------------------------------------------
    function ajax_work_cancel() {
        $logged_user_id = intval(decrypt($this->session->userdata('user_id')));
        $arr_profile_info = $this->users_model->fetch_this($logged_user_id);
        $data['arr_profile_info'] = $arr_profile_info;

        $this->load->view('logged/ajax_cancelling_my_profile/work_cancel.phtml', $data);
    }

    //----------------------------------------- education -------------------------------------------

    function ajax_edu_cancel() {
        $logged_user_id = intval(decrypt($this->session->userdata('user_id')));
        $arr_profile_info = $this->users_model->fetch_this($logged_user_id);
        $data['arr_profile_info'] = $arr_profile_info;

        $this->load->view('logged/ajax_cancelling_my_profile/edu_cancel.phtml', $data);
    }

    //-------------------------------------- basic info ----------------------------------------------
    function ajax_basic_info_cancel() {
        $logged_user_id = intval(decrypt($this->session->userdata('user_id')));
        $arr_profile_info = $this->users_model->fetch_this($logged_user_id);
        $data['arr_profile_info'] = $arr_profile_info;

        $cwhere = " i_country_id='" . $arr_profile_info["i_church_country_id"] . "'";
        $data['cstate'] = makeOptionState($cwhere, encrypt($arr_profile_info["i_church_state_id"]));
        $cwhere1 = " i_state_id='" . $arr_profile_info["i_church_state_id"] . "'";
        $data['ccity'] = makeOptionCity($cwhere1, encrypt($arr_profile_info["i_church_city_id"]));

        $this->load->view('logged/ajax_cancelling_my_profile/basic_info_cancel.phtml', $data);
    }

    //-------------------------------------- personal info ----------------------------------------------
    function ajax_personal_info_cancel() {
        $logged_user_id = intval(decrypt($this->session->userdata('user_id')));
        $arr_profile_info = $this->users_model->fetch_this($logged_user_id);
        $where = " i_country_id='" . $arr_profile_info["i_country_id"] . "'";
        $data['state'] = makeOptionState($where, encrypt($arr_profile_info["i_state_id"]));
        $where1 = " i_state_id='" . $arr_profile_info["i_state_id"] . "'";
        $data['city'] = makeOptionCity($where1, encrypt($arr_profile_info["i_city_id"]));
        $data['arr_profile_info'] = $arr_profile_info;
        /*         * ************************************ */
        $cwhere = " i_country_id='" . $arr_profile_info["i_church_country_id"] . "'";
        $data['cstate'] = makeOptionState($cwhere, encrypt($arr_profile_info["i_church_state_id"]));
        $cwhere1 = " i_state_id='" . $arr_profile_info["i_church_state_id"] . "'";
        $data['ccity'] = makeOptionCity($cwhere1, encrypt($arr_profile_info["i_church_city_id"]));

        /*         * ********************************** */
        $this->load->view('logged/ajax_cancelling_my_profile/personal_info_cancel.phtml', $data);
    }

    //========================================= end of ajax for cancelling =======================================
    //========================================= ajax for submit =======================================
    //-------------------------------------- personal info ----------------------------------------------
    function ajax_personal_info_submit() {


        $logged_user_id = intval(decrypt($this->session->userdata('user_id')));
        $arr_profile_info = $this->users_model->fetch_this($logged_user_id);
        $where = " i_country_id='" . $arr_profile_info["i_country_id"] . "'";

        $data['state'] = makeOptionState($where, encrypt($arr_profile_info["i_state_id"]));
        $where1 = " i_state_id='" . $arr_profile_info["i_state_id"] . "'";
        $data['city'] = makeOptionCity($where1, encrypt($arr_profile_info["i_city_id"]));
        $data['arr_profile_info'] = $arr_profile_info;
        $cwhere = " i_country_id='" . $arr_profile_info["i_church_country_id"] . "'";
        $data['cstate'] = makeOptionState($cwhere, encrypt($arr_profile_info["i_church_state_id"]));
        $cwhere1 = " i_state_id='" . $arr_profile_info["i_church_state_id"] . "'";
        $data['ccity'] = makeOptionCity($cwhere1, encrypt($arr_profile_info["i_church_city_id"]));

        /*         * ************************************* */
        // $data['arr_profile_info'] = $arr_profile_info;

        /*         * ************************************ */
        //  pr($data);


        $this->load->view('logged/ajax_submit_my_profile/personal_info_submit_response.phtml', $data);
    }

    //-------------------------------------- personal info ----------------------------------------------
    function ajax_basic_info_submit() {
        $logged_user_id = intval(decrypt($this->session->userdata('user_id')));
        $arr_profile_info = $this->users_model->fetch_this($logged_user_id);
        $data['arr_profile_info'] = $arr_profile_info;
        $cwhere = " i_country_id='" . $arr_profile_info["i_church_country_id"] . "'";
        $data['cstate'] = makeOptionState($cwhere, encrypt($arr_profile_info["i_church_state_id"]));
        $cwhere1 = " i_state_id='" . $arr_profile_info["i_church_state_id"] . "'";
        $data['ccity'] = makeOptionCity($cwhere1, encrypt($arr_profile_info["i_church_city_id"]));

        $this->load->view('logged/ajax_submit_my_profile/basic_info_submit_response.phtml', $data);
    }

    //-------------------------------------- education ----------------------------------------------
    function ajax_edu_submit() {
        $logged_user_id = intval(decrypt($this->session->userdata('user_id')));
        $arr_profile_info = $this->users_model->fetch_this($logged_user_id);
        $data['arr_profile_info'] = $arr_profile_info;
        #pr($arr_profile_info);
        #pr($arr_profile_info["education_arr"]);
        /* $count=count($arr_profile_info["eduction_arr"]);
          if($count==0)
          {
          $count=1;
          }
          for($i=0;$i<$count;$i++)
          {
          $data['sstate'.$i] 	= makeOptionState($cwhere1, encrypt($arr_profile_info['education_arr'][$i]["s_school_state"]));
          $cwhere1	= " i_state_id='".$arr_profile_info["education_arr"][$i]["s_school_state"]."'";
          $data['scity'.$i] 	= makeOptionCity($cwhere1,encrypt($arr_profile_info["education_arr"][$i]["s_school_city"]));
          echo $data['scity'.$i];
          } */
        $this->load->view('logged/ajax_submit_my_profile/edu_submit_response.phtml', $data);
        //$this->load->view('logged/my_profile_edu_part.phtml',$data);
    }

    //-------------------------------------- work ----------------------------------------------
    function ajax_work_submit() {
        $logged_user_id = intval(decrypt($this->session->userdata('user_id')));
        $arr_profile_info = $this->users_model->fetch_this($logged_user_id);
        $data['arr_profile_info'] = $arr_profile_info;

        $this->load->view('logged/ajax_submit_my_profile/work_submit_response.phtml', $data);
    }

    //-------------------------------------- skill ----------------------------------------------
    function ajax_skill_submit() {
        $logged_user_id = intval(decrypt($this->session->userdata('user_id')));
        $arr_profile_info = $this->users_model->fetch_this($logged_user_id);
        $data['arr_profile_info'] = $arr_profile_info;

        $this->load->view('logged/ajax_submit_my_profile/skill_submit_response.phtml', $data);
    }

    //========================================= end of ajax for submit ======================================= 


    public function updateUserStatus() {
        $status = ($this->input->post('status'));
        $chk_frnd = ($this->input->post('chk_frnd') == '' ) ? '0' : $this->input->post('chk_frnd');
        $chk_netpal = ($this->input->post('chk_netpal') == '') ? '0' : $this->input->post('chk_netpal');
        $chk_pp = ($this->input->post('chk_pp') == 0) ? '0' : $this->input->post('chk_pp');


        $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        $this->users_model->updateUserOnlineStatus($i_profile_id, $status, $chk_frnd, $chk_netpal, $chk_pp);

        $list_types = '';
        $listarr = array();





        if (($status == '1')) {

            $s_status = '<img src="' . base_url() . 'images/icons/online.png" alt="" /> Online for';

            ### html online user types
            $user_status_arr = $this->users_model->getUserOnlineStatus($i_profile_id);
            if ($user_status_arr['i_isfriend'] == 1)
                array_push($listarr, '<li>Friends</li>');

            if ($user_status_arr['i_isnetpal'] == 1)
                array_push($listarr, '<li>Net Pals</li>');

            if ($user_status_arr['i_isprayerpartner'] == 1)
                array_push($listarr, '<li>Prayer Partners</li>');

            $list_types = implode('<li>I</li>', $listarr);

            $inst_html = '<a id="instantmsg" href="javascript:void(0);" onclick= "showOnlineUser(\'show\')" ><img src="images/Chat.png" alt=""/>Instant Message</a>';
        }
        else {
            $s_status = '<img src="' . base_url() . 'images/icons/offline.png" alt="" /> Offline';
            $inst_html = '<a id="instantmsg" href="javascript:void(0);" onclick="showUIMsg(\'You are offline to network!\');" ><img src="images/Chat.png" alt=""/>Instant Message</a>';
        }
        #$s_status =($status == 1 )?'Online':($status == 2)?'Invisible':($status == 3)?'Away':'Offline';
        echo json_encode(array('success' => true, 'msg' => 'your status updated successfully', 'status' => $s_status, 'list_types' => $list_types, 'inst_html' => $inst_html));
    }

    public function save_bio() {
        $s_bio = get_formatted_string($this->input->post('s_bio'));

        $id = intval(decrypt($this->session->userdata('user_id')));

        $SQL = "update cg_users set s_bio = '" . $s_bio . "' WHERE id='" . $id . "' ";
        $this->db->query($SQL);


        echo json_encode(array('success' => true, 'msg' => 'your mood has been updated successfully', 's_bio' => $s_bio));
    }

}

// end of controller...

