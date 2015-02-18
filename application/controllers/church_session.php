<?php

/* * *******
 * Author: 

 * @link InfController.php 
 * @link Base_Controller.php
 * @link model/##.php 
 * @link views/##
 */
error_reporting(E_ALL && ~E_NOTICE);
include_once(BASEPATH . 'application/controllers/base_controller.php');

class Church_session extends Base_controller {

    public function __construct() {

        try {

            parent::__construct();

            $this->load->model('users_model');
            $this->load->model('admins_user_model');
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    //// function to validate user instant login...
    public function authenticate($email, $pwd) {

        try {
get_all_church_session();
            $arr_messages = array();


            # error message trapping...
            if (trim($this->input->post('email')) == '') {
                $arr_messages['email'] = "* Required";
            } else if (!preg_match("/^[_a-z0-9-\+]+(\.[_a-z0-9-\+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", trim($this->input->post('email')))) {
                $arr_messages['email'] = 'Invalid Email ID';
            }
            if (trim($this->input->post('password')) == '') {
                $arr_messages['password'] = "* Required";
            }

            $chkRem = ($this->input->post('chkRem') == '1' || $this->input->post('chkRem') == 'true') ? 'checked' : '';

            if (count($arr_messages) == 0) {



                $pattern = '/[\s]*\'[\s]*(.*)[\s]*\'[\s]*/';

                $login_data['s_email'] = preg_replace($pattern, "$1", $this->db->escape($this->input->post('email')));
                if (empty($login_data['s_email'])) {
                    $login_data['s_email'] = $email;
                }

                $login_data['s_password'] = preg_replace($pattern, "$1", $this->db->escape($this->input->post('password')));
                if (empty($login_data['s_password'])) {
                    $login_data['s_password'] = $pwd;
                }

                $is_temp_pwd_fld = is_temp_password_user_by_email($login_data['s_email']);
                if ($is_temp_pwd_fld == 2) {
                    echo json_encode(array('success' => 'false', 'msg' => t('Error in connection!'), 'arr_messages' => $arr_messages));
                    exit;
                } else {
                    $user_account_status = get_user_account_status($login_data['s_email']);
                    $loggedin = $this->users_model->authenticate($login_data, $user_account_status);
                    if (!empty($loggedin)) {

                        /* start of Remeber me */
                        $user_account_status = get_user_account_status($login_data['s_email']);

                        if ($user_account_status == 1) {
                            $red_url = base_url() . 'activate_account/index';
                            echo json_encode(array('success' => 'activate', 'redirect_url' => $red_url));
                            exit;
                        } else {
                            if ($this->input->post('chkRem') == '1' || $this->input->post('chkRem') == 'true' || $this->input->post('chkRem') == 'checked') {
                                setcookie("CG_FO[email]", $this->input->post('email', true), time() + 2592000, "/");
                                setcookie("CG_FO[password]", $this->input->post('password', true), time() + 2592000, "/");
                                setcookie("CG_FO[remember]", 'checked', time() + 2592000, "/");
                                //var_dump($_COOKIE);exit;
                            } else {
                                setcookie("CG_FO[username]", "", time() - 3600, "/");
                                setcookie("CG_FO[password]", "", time() - 3600, "/");
                                setcookie("CG_FO[remember]", "", time() - 3600, "/");
                            }

                            /* end of Remenber me */




                            $hash = '';
                            if ($this->input->post('hash') != '') {
                                $hash = $this->input->post('hash');
                            }


                            $default_redirect_url = base_url() . "index.html";

                            if ($this->input->post('referrer') != '' && url_exists(base64_decode($this->input->post('referrer')))) {

                                $redirect_url = base64_decode($this->input->post('referrer')) . $hash;
                                echo json_encode(array('success' => 'true', 'msg' => 'Connection Rejected!', 'arr_messages' => $arr_messages, 'redirect_url' => $redirect_url));
                                exit;
                            } else if ($this->input->post('current_referrer') != '' && url_exists(base64_decode($this->input->post('current_referrer')))) {

                                $redirect_url = base64_decode($this->input->post('current_referrer')) . $hash;
                                echo json_encode(array('success' => 'true', 'msg' => 'Connection Rejected!', 'arr_messages' => $arr_messages, 'redirect_url' => $redirect_url));
                                exit;
                            } else if ($loggedin['i_is_admin'] == 1) {

                                echo json_encode(array('success' => 'true', 'redirect_url' => base_url() . 'admin/'));
                                exit;
                            } else {
                                
                                /*********************ip catch add on 18-12-2014***************************************/
                                 /**************ip catch*********************/
                                $eml = $login_data['s_email'];
                                $query = $this->db->get_where('cg_users', array('s_email' => $eml));
                                foreach ($query->result() as $row)
                                    {
                                        $id = $row->id;
                                    }
                                     $ip = getenv("REMOTE_ADDR") ; 
                                     //die();
                                    $data = array(
                                    'u_id' => $id ,
                                    'u_ip' => $ip ,
                                    'is_status' => 0
                                 );

                             $this->db->insert('cg_user_ip', $data); 
                                    //var_dump($res);
                                    //die();
                                /**********************************/
                                /*****************************************************************/
                                
                                $query = $this->db->get_where('cg_church_admin', array('ch_admin_id' => $id));
                                 $result = $query->result();
                                 if(!empty($result)){
                                      $redirect_url = get_unformatted_string_edit($result[0]->ch_sp_url);
                                 }else if(empty($result)){
                                     //die();
                                $redirect_url = base_url() . "my-wall.html";     
                                 }
                                  
                                
                                /******************************************/
                                

                                
                                echo json_encode(array('success' => 'true', 'msg' => 'Connection Rejected!', 'arr_messages' => $arr_messages, 'redirect_url' => $redirect_url));
                                exit;
                            }
                        }
                    } else {
                        echo json_encode(array('success' => 'false', 'msg' => 'Error in connection!', 'arr_messages' => $arr_messages));

                        exit;
                    }
                }
            } else {
                echo json_encode(array('success' => 'false', 'msg' => t('Error in connection!'), 'arr_messages' => $arr_messages));
                exit;
            }
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function logout() {
        try {

            # killing all session-data...
            $this->users_model->logout();

            # redirecting to the home-page...
            header('Location: ' . base_url());
            exit;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function admin_logout() {
        try {

            # killing all session-data...
            $this->admins_user_model->logout();
            /*  setcookie("CG[username]","",time() - 3600, "/");
              setcookie("CG[password]","",time() - 3600, "/");
              setcookie("CG[remember]","",time() - 3600, "/");
             */
            # redirecting to the admin home-page...
            redirect(admin_base_url() . 'index.html');
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    # forgot password part...

    public function forgot_password_ajax() {
        try {
            //sleep(2);
            $arr_messages = array();
            $language = get_current_language();
            $arr_values = $_POST;

            $MAIL_ID = trim($this->input->post('txt_forgot_email'));

            if ($MAIL_ID == '') {
                $err_msg = "* champ requis";
            } else if (!preg_match("/^[_a-z0-9-\+]+(\.[_a-z0-9-\+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", $MAIL_ID)) {
                $err_msg = '* E-mail incorrecte id';
            } else {
                # check for email-id existence...
                $Query = sprintf("SELECT * FROM %susers WHERE binary `s_email` = '%s' ", $this->db->dbprefix, $MAIL_ID);
                $query = $this->db->query($Query);

                if ($query->num_rows()) {
                    $row = $query->result_array();
                    #pr($row);
                    # retrieving replacable values...
                    $USER_ID = $row[0]['id'];
                    if ($row[0]['i_user_type'] == 2):
                        $USERNAME = $row[0]['s_profile_name'];
                    else:
                        $USERNAME = $row[0]['s_name'];
                    endif;
                    $EMAIL = $row[0]['s_email'];

                    # new random passowrd...
                    $this->load->model('users_model');
                    $RANDOM_PASS = $this->users_model->generatePassword();

                    $NEW_PASSWD = $RANDOM_PASS;

                    $replaceArr = array('email' => $EMAIL,
                        'name' => $USERNAME,
                        'password' => $NEW_PASSWD);
                }
                else {
                    $err_msg = "* L'adresse courriel est invalide";
                }
            }

            if (empty($err_msg)) {
                # sending mail to the user with the reset password [Start]...
				$this->load->helper('html');
					$this->load->library('email');
					 $email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
				$this->email->initialize($email_setting);
                $this->load->model('mail_contents_model');
                $mail_info = $this->mail_contents_model->get_by_name("forgot_password_" . $language);
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);

                $body = sprintf3($body, $replaceArr);

                $arr['subject'] = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
                $arr['to'] = $MAIL_ID;
                #$arr['to'] = 'suman5690@gmail.com';
                #$arr['bcc'] = 'suman.d@acumensoft.info';
                $arr['from_email'] = $this->site_settings_model->get('s_no_reply_email');
                $arr['from_name'] = $this->site_settings_model->get('s_mail_from_name');
                $arr['message'] = $body;

                #dump($arr);
				$this->email->from( $arr['from_email'], $arr['from_name']);
                #dump($arr);
				$this->email->subject($arr['subject']);
						
				$this->email->to($arr['to']);
				$this->email->bcc($arr['bcc']);
				$this->email->message("$body");
                        //send_mail($arr);
				$this->email->send();
               // send_mail($arr);


                # sending mail to the user with the reset password [End]...
                # and finally, updating user-info...
                $pass_arr['s_password'] = get_salted_password($NEW_PASSWD);
                $this->load->model('users_model');
                $this->users_model->update($pass_arr, $USER_ID);

                $success_msg = "Mot de passe a été envoyé à votre courrier";
                echo json_encode(array('result' => 'success', 'msg' => $success_msg));
                exit;
            } else {
                echo json_encode(array('result' => 'failure', 'msg' => $err_msg));
                exit;
            }
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

//// end of forgot-password function
    /// the same authentication function for admin login-authentication...
    public function admin_authenticate() {

        try {

            $arr_messages = array();

            # error message trapping...
            if (trim($this->input->post('email')) == '') {
                $arr_messages['email'] = "* Required Field";
            } else if (!preg_match("/^[_a-z0-9-\+]+(\.[_a-z0-9-\+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", trim($this->input->post('email')))) {
                $arr_messages['email'] = 'Invalid Email ID';
            }

            if (trim($this->input->post('password')) == '') {
                $arr_messages['password'] = "* Required Field";
            }


            if (count($arr_messages) == 0) {

                $hash = '';
                if ($this->input->post('hash') != '') {
                    $hash = $this->input->post('hash');
                }

                $default_redirect_url = base_url() . "dashboard.html";

                if ($this->input->post('referrer') != '' && url_exists(base64_decode($this->input->post('referrer')))) {
                    $redirect_url = base64_decode($this->input->post('referrer')) . $hash;
                }
                /* else 
                  {


                  if( $this->input->post('current_referrer')!='' && url_exists(base64_decode($this->input->post('current_referrer'))) )
                  {
                  $redirect_url =  base64_decode($this->input->post('current_referrer')).$hash ;
                  }
                  else
                  {
                  $redirect_url = $default_redirect_url;
                  }


                  } */



                $pattern = '/[\s]*\'[\s]*(.*)[\s]*\'[\s]*/';




                $login_data['s_email'] = preg_replace($pattern, "$1", $this->db->escape($this->input->post('email')));
                $login_data['s_password'] = preg_replace($pattern, "$1", $this->db->escape($this->input->post('password')));

                $loggedin = $this->admins_user_model->authenticate($login_data);




                if (!empty($loggedin)) {


                    /* start of Remeber me */


                    if ($this->input->post('chkRem') == 'true') {
                        setcookie("CG[username]", $this->input->post('username', true), time() + 2592000, "/");
                        setcookie("CG[password]", $this->input->post('passwd', true), time() + 2592000, "/");
                        setcookie("CG[remember]", $this->input->post('chkRem'), time() + 2592000, "/");
                    } else {
                        setcookie("CG[username]", "", time() - 3600, "/");
                        setcookie("CG[password]", "", time() - 3600, "/");
                        setcookie("CG[remember]", "", time() - 3600, "/");
                    }

                    /* end of Remenber me */

                    $redirect_url = base_url() . "dashboard.html";


                    echo json_encode(array('success' => 'true',
                        'msg' => t('successful connection!'),
                        'arr_messages' => $arr_messages,
                        'redirect_url' => $redirect_url));
                    exit;
                } else {
                    echo json_encode(array('success' => 'false', 'msg' => 'Invalid sername / password!', 'arr_messages' => $arr_messages));

                    exit;
                }
            } else {
                echo json_encode(array('success' => 'false', 'msg' => t('Login failed!'), 'arr_messages' => $arr_messages));
                exit;
            }
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    # function to set as profile pic...  

    public function set_as_profile_pic($id) {
        $user_id = intval(decrypt($this->session->userdata('user_id')));

        $src_path = BASEPATH . '../uploads/user_photos/';
        $upload_path = BASEPATH . '../uploads/user_profile_image/';

        $this->load->model('user_photos_model');
        $photo_details = $this->user_photos_model->get_by_id($id);
        #pr($photo_details ,1);

        if (!is_array($photo_details) || !count($photo_details) || @$photo_details['s_photo'] == '' || $photo_details['i_user_id'] != $user_id) {

            echo json_encode(array('success' => false, 'msg' => 'Invalid request'));
            exit;
        }

        $ext_arr = get_ext($photo_details['s_photo']);
        $ext = $ext_arr['ext'];
        $filename = $ext_arr['filename'];

        if (test_file($upload_path . $filename . '-big.' . $ext)) {
            for ($i = 0; test_file($upload_path . $filename . '-' . $i . '-big.' . $ext); $i++) {
                
            }

            $new_imagename = $filename . '-' . $i;
        } else {
            $new_imagename = $filename;
        }

        $this->imagename = $new_imagename;

        $this->upload_image = $upload_path . $new_imagename . '.' . $ext;
        //echo $upload_path; exit;

        $copied = @copy($src_path . getThumbName($photo_details['s_photo'], 'main'), $this->upload_image);

        //echo $src_path.$photo_details['s_photo'].'###'.$this->upload_image;

        if (!$copied) {
            echo json_encode(array('success' => false, 'msg' => 'Some error occurred. Try after sometime.'));
            exit;
        }

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

        @unlink($this->upload_image);

        $this->load->model('users_model');
        $user_photo_details = $this->users_model->fetch_this($user_id);
        $arr['s_profile_photo'] = $new_imagename . '.' . $ext;

        $updated = $this->users_model->update($arr, $user_id);

        @unlink($upload_path . getThumbName($s_profile_photo['s_profile_photo'], 'thumb'));

        echo json_encode(array('success' => true, 'is_profile_pic' => 'true'));
    }

    ### new ajax login ####

    public function ajax_authenticate() {

        try {

            $arr_messages = array();


            # error message trapping...
            if (trim($this->input->post('email')) == '') {
                $arr_messages['email'] = "* Required";
            } else if (!preg_match("/^[_a-z0-9-\+]+(\.[_a-z0-9-\+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", trim($this->input->post('email')))) {
                $arr_messages['email'] = 'Invalid Email ID';
            }
            if (trim($this->input->post('password')) == '') {
                $arr_messages['password'] = "* Required";
            }

            $chkRem = ($this->input->post('chkRem') == '1' || $this->input->post('chkRem') == 'true') ? 'checked' : '';



            if (count($arr_messages) == 0) {



                $pattern = '/[\s]*\'[\s]*(.*)[\s]*\'[\s]*/';

                $login_data['s_email'] = preg_replace($pattern, "$1", $this->db->escape($this->input->post('email')));
                $login_data['s_password'] = preg_replace($pattern, "$1", $this->db->escape($this->input->post('password')));



                $loggedin = $this->users_model->authenticate($login_data);




                if (!empty($loggedin)) {

                    /* start of Remeber me */


                    if ($this->input->post('chkRem') == '1' || $this->input->post('chkRem') == 'true' || $this->input->post('chkRem') == 'checked') {
                        setcookie("CG_FO[email]", $this->input->post('email', true), time() + 2592000, "/");
                        setcookie("CG_FO[password]", $this->input->post('password', true), time() + 2592000, "/");
                        setcookie("CG_FO[remember]", 'checked', time() + 2592000, "/");
                        //var_dump($_COOKIE);exit;
                    } else {
                        setcookie("CG_FO[username]", "", time() - 3600, "/");
                        setcookie("CG_FO[password]", "", time() - 3600, "/");
                        setcookie("CG_FO[remember]", "", time() - 3600, "/");
                    }

                    /* end of Remenber me */



                    $hash = '';
                    if ($this->input->post('hash') != '') {
                        $hash = $this->input->post('hash');
                    }


                    //$default_redirect_url = base_url()."index.html";

                    if ($this->input->post('referrer') != '' && url_exists(base64_decode($this->input->post('referrer')))) {

                        $redirect_url = base64_decode($this->input->post('referrer')) . $hash;
                        echo json_encode(array('success' => 'true', 'msg' => 'Connection Rejected!', 'arr_messages' => $arr_messages, 'redirect_url' => $redirect_url));
                        exit;
                    } else if ($this->input->post('current_referrer') != '' && url_exists(base64_decode($this->input->post('current_referrer')))) {

                        $redirect_url = base64_decode($this->input->post('current_referrer')) . $hash;
                        echo json_encode(array('success' => 'true', 'msg' => 'Connection Rejected!', 'arr_messages' => $arr_messages, 'redirect_url' => $redirect_url));
                        exit;
                    } else {

                        $redirect_url = base_url() . "my-wall.html";
                        echo json_encode(array('success' => 'true', 'msg' => 'Connection Rejected!', 'arr_messages' => $arr_messages, 'redirect_url' => $redirect_url));
                        exit;
                    }
                } else {
                    echo json_encode(array('success' => 'false', 'msg' => 'Error in connection!', 'arr_messages' => $arr_messages));

                    exit;
                }
            } else {
                echo json_encode(array('success' => 'false', 'msg' => 'Error in connection!', 'arr_messages' => $arr_messages));
                exit;
            }
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

}
