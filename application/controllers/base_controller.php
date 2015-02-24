<?php
ini_set('memory_limit', '2048M');
//die('ook');
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/* * *******
 * Author: 
 * Date  : 
 * Modified By:
 * Modified Date: 
 * Purpose:
 * 
 * @include 
 */

class Base_controller extends CI_Controller {

    protected $language;
    protected $data = array();


    /* session variables */
    protected $ses_user = array();
    public $translation_container = null;

    public function __construct() {
        try {
            
          //  die('ok');
            parent::__construct();

            $this->_set_timezone();

            $this->load->helper('common_helper');
            //$this->load->helper('chat_helper'); 

            $this->load->model('projects_model');
            $this->load->model('site_settings_model');
            $this->load->model('events_model');
            $this->data['site_settings_arr'] = $this->site_settings_model->get_by_id(1);

            //pr($this->data['site_settings_arr']);
            /* values required for all controllers will be generated here */
            /*
              set session variables to member variables. so one can not need to call
              session->userdata() function whenever these common variables are needed
             */
            $this->_set_session_variables();
            $this->_set_cms_menu_footer();
            //echo date_difference_due_time(strtotime('2013-08-16 14:20'),strtotime('2013-08-19 15:20') );
            //$this->show_system_reminder_popup_at_remind_me_time();
            # if not logged in yet...
            $this->data['logged_in_state'] = 'N';

            if ($this->session->userdata('loggedin') != '' || $this->session->userdata('loggedin') != false) {
                $this->data['loggedin'] = true;

                # set the logged in status...
                $this->data['logged_in_state'] = 'Y';

                # get scrolling news #
                $this->_set_latest_news();


                # get total unread messages #
                $logged_user_id = intval(decrypt($this->session->userdata('user_id')));
                $this->_get_total_unread_msgs($logged_user_id);
                $this->_set_left_panel_data($logged_user_id);
                $this->get_user_all_tweets($logged_user_id);
                $this->salavtion_popup_content();


                /* get advertisement */
                $this->getLatestAdvertisement();
                 $this->getmediaLatestAdvertisement();
                //$this->config->item('max_prayer_group', 5);
                //echo $this->config->item('max_prayer_group');
                //$re=array();
                //$re=$this->projects_model->get_list('','0','2');
                //$this->data['latest_charity_project']=$this->projects_model->get_project();
                $this->data['latest_admin_events'] = $this->events_model->get_latest_admin_events();
                #pr($re);
            } else {
                $this->data['loggedin'] = false;
                # $this->_login_by_cookie();  /* auto login if Remeber me was checked */
            }


            /*             * ************ news ticker content ***************** */

            /*             * ****************************** Layout based codes *********************** */
            $this->js_files = array();
            $this->css_files = array();
            $this->header_html = array();
            $this->title = '';
            $this->meta_desc = '';
            $this->meta_keywords = '';  // new 4 meta keywords...
            $this->layout = 'layouts/layout.phtml';
            /*             * ************************************************************************* */

            $this->_set_translations();

            ////////////Managing Validators/////////
            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<div id="err_msg" class="error_massage">', '</div>');
            $this->form_validation->set_message('required', 'Please provide %s.');
            ////////////end Managing Validators/////////

            $this->_add_default_js_files();
            $this->_add_default_css_files();
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    /*     * **
     * put your comment there...
     * 
     * @param array $files
     */

    protected function _set_multilanguage_db() {

        try {
            header('Content-type: text/html; charset=utf-8');

            //******** This Two Lines for multilanguage *********//
            $this->db->query("SET CHARACTER SET utf8");
            $this->db->query("SET SESSION collation_connection ='utf8_general_ci'");
            //*******************************************//
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    /*     * **
     * put your comment there...
     * 
     * @param array $files
     */

    protected function _set_timezone() {
        try {
            //************* set timezone *********************//
            //$this->db->query("SET session time_zone = '+1:00'");
            //************************************************//
            date_default_timezone_set('Europe/London');
            //date_default_timezone_set('Europe/Paris');
            /* setlocale(LC_ALL, 'en_EN.utf8'); Exclusively for zend search lucene */
            /* date_default_timezone_set('Europe/Paris');
              setlocale(LC_ALL, 'fr_FR.utf8');  Exclusively for zend search lucene */
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    /*     * **
     * put your comment there...
     * 
     * @param array $files
     */

    protected function _set_session_variables() {
        try {
            $this->ses_user['login_referrer'];
            $this->ses_user['login_referrer'] = base64_encode($this->session->userdata('session_referrer'));
            $this->ses_user['loggedin'] = $this->session->userdata('loggedin');
            $this->ses_user['user_id'] = $this->session->userdata('user_id');
            $this->ses_user['user_type'] = $this->session->userdata('user_type');
            $this->ses_user['email'] = $this->session->userdata('email');
            $this->ses_user['username'] = $this->session->userdata('username');
            $this->ses_user['is_admin'] = $this->session->userdata('is_admin');
            $this->data['session_data'] = $this->ses_user;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    /*     * **
     * put your comment there...
     * 
     * @param array $files
     */

    protected function _set_translations() {
        try {
            $ci = get_instance();

            if (is_readable(FCPATH . $ci->config->item('multilanguage_object'))) {
                $this->translation_container = unserialize(FCPATH . $ci->config->item('multilanguage_object'));
            } else if (is_readable(FCPATH . $ci->config->item('multilanguage_xml'))) {
                $ci->load->library('multilanguage/TMXParser');
                $ci->tmxparser->setXML(FCPATH . $ci->config->item('multilanguage_xml'));
                $ci->tmxparser->setMasterLanguage($ci->config->item('master_language'));
                $tc = $ci->tmxparser->doParse();

                $this->translation_container = $tc;
            }
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    /*     * **
     * put your comment there...
     * 
     * @param array $files
     */

    public function get_translations() {
        try {
            return $this->translation_container;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    /*     * **
     * put your comment there...
     * 
     * @param array $files
     */

    protected function _set_language($lang_id) {
        try {
            if (in_array($lang_id, array('en', 'fr'))) {
                $this->session->set_userdata('current_language', $lang_id);
            }
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    /*     * **
     * put your comment there...
     * 
     * @param array $files
     */

    protected function _set_login_vars($wronglogin = "", $referrer = '') {

        try {
            if ($referrer == '') {
                $this->data['referrer'] = ( $this->session->userdata('session_referrer') != '') ? base64_encode($this->session->userdata('session_referrer')) : '';
            } else {
                $this->data['referrer'] = $referrer;
            }
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    /*     * ***************************** Layout based codes *********************** */

    /*     * **
     * put your comment there...
     * 
     * @param array $files
     */

    /* e.g. array('js/jquery.js'=>'header', 'js/stepcarousel.js'=>'header') 
      OR     array('js/jquery.js', 'js/stepcarousel.js')
     */

    protected function _add_js($js_file, $position = 'footer') {
        try {
            $this->js_files[$js_file] = $position;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    /*     * **
     * put your comment there...
     * 
     * @param array $files
     */

    protected function _add_js_arr(array $js_files_arr) {
        try {
            /* if associative array position is supplied */
            if (is_assoc($js_files_arr)) {
                $this->js_files = array_merge($this->js_files, $js_files_arr);
            }
            /* if associative array position is not supplied. default of footer is used */ else {
                //$this->js_files = array_merge($this->js_files, $js_files_arr);
                $arr = array();
                foreach ($js_files_arr as $js_file) {
                    $arr[$js_file] = 'header';
                }
                $this->js_files = array_merge($this->js_files, $arr);
            }
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    /*     * **
     * put your comment there...
     * 
     * @param array $files
     */
    /* e.g. 'css/style.css', array('MEDIA'=>'print')
     */

    protected function _add_css($css_file, array $attrs = array()) {
        try {
            if (is_array($attrs) && count($attrs)) {
                $css_files[$item_css] = $attrs;
            } else {
                $css_files[$item_css] = array();
            }
            $this->css_files = array_merge($this->css_files, $css_files);
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    /*     * **
     * put your comment there...
     * 
     * @param array $files
     */


    /* e.g. array('css/style.css'=>array('MEDIA'=>'aural'), 'css/thickbox.css'=>array('MEDIA'=>'screen, print', 'TITLE'=>'24-bit Color Style'))
     * default structure is <link href="'.$css_file.'" rel="stylesheet" type="text/css" />, do not add href, rel and type attributes.
     */

    protected function _add_css_arr(array $css_files_arr) {
        try {
            foreach ($css_files_arr as $key_css => $item_css) {
                if (is_numeric($key_css) && !is_array($item_css)) {
                    $css_files[$item_css] = array();
                } else {
                    $css_files[$key_css] = $item_css;
                }
                //$this->css_files = array_merge($this->css_files, $css_files_arr);
            }
            $this->css_files = array_merge($this->css_files, $css_files);
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    /*     * **
     * put your comment there...
     * 
     * @param array $files
     */

    protected function _add_header_html($html) {
        try {
            $this->header_html[] = $html;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    /*     * **
     * put your comment there...
     * 
     * @param array $files
     */

    protected function _set_title($title) {
        try {
            $this->title = $title;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    /*     * **
     * put your comment there...
     * 
     * @param array $files
     */

    protected function _set_meta_desc($desc) {
        try {
            $this->meta_desc = $desc;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    /*     * **
     * put your comment there...
     * 
     * @param array $files
     */

    protected function _set_meta_keywords($keywords) {
        try {
            $this->meta_keywords = $keywords;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    /*     * **
     * put your comment there...
     * 
     * @param array $files
     */

    protected function _set_layout($layout) {

        try {
            $this->layout = $layout;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    protected function _add_default_js_files() {
        ///////// NEW CODE [FOR DEFAULT JS FILES - BEGIN] /////////
        $default_js_arr = array(
            'js/jquery-1.7.2.js' => 'header',
            'js/jquery.js' => 'header', // causing conflict with block ui
            //'js/frontend/header_slider.js'=>'header',
            //'js/contentslider.js'=>'header',
            /*'js/jquery/ui/jquery.blockUI.js' => 'header',
            'js/jquery/ui/jquery.ui.core.js' => 'header',
            'js/frontend/utilities.js' => 'header',
            'js/jquery/ui/jquery-ui-1.8.4.custom.js' => 'header',
            'js/jquery.form.js' => 'header',
            'js/jquery/JSON/json2.js' => 'header',
            'js/ModalDialog.js' => 'header',
            'js/frontend/login/login.js' => 'header',
            'js/utilities.js' => 'header',
            #'js/backend/admin_utilities.js'=>'header',
            'js/login.js' => 'header',
            'js/notification.js' => 'header',
            'js/utility_js_for_admin_and_fe.js' => 'header'*/
            'js/production.js' => 'header'
        );


        $this->_add_js_arr($default_js_arr);

        if ($this->session->userdata('loggedin') != '' && $this->session->userdata('loggedin') != false && $this->session->userdata('is_admin') == '') {
            $logged_chat_js = array(# added for im
                'chat/js/chat.js' => 'header',
                'js/jquery.gemoticons.js' => 'header'
                );
            $this->_add_js_arr($logged_chat_js);
        }


        # fix for "js_files" array...
        /* if( is_array($this->js_files) && count($this->js_files) ) {
          $this->js_files = array_merge($default_js_arr, $this->js_files);
          } else {
          $this->js_files = $default_js_arr;
          } */
    }

    protected function _add_default_css_files() {

        $default_css_arr = array('css/style.css' => array('media' => 'screen'),
            'css/IMchat/chat.css' => array('media' => 'screen'),
            //'css/IMchat/screen.css' => array('media' => 'screen'),
            'css/gemoticons.css' => array('media' => 'screen')
        );

        # fix for "css_files" array...
        /* if( is_array($this->css_files) && count($this->css_files) ) {
          $this->css_files = array_merge($default_css_arr, $this->js_files);
          } else {
          $this->css_files = $default_css_arr;
          } */

        $this->_add_css_arr($default_css_arr);
    }

    /*     * **
     * put your comment there...
     * 
     * @param array $files
     */

    // When _render() is used it releases the output as there is flush() call in the layout fle views/layouts/layout.php
    // so to get a string return one has to change layout file using _set_layout(). 
    // flush() call makes js and css parallel download possible so page loads faster.
    protected function _render(array $data = array(), $view_script = '', $bool_string = false) {
        try {
            $data = array_merge($data, $this->data);
            if ($view_script == '') {
                if ($this->router->directory == '') {
                    $view_script = $this->router->class . '.phtml';
                } else {
                    $view_script = $this->router->directory . '/' . $this->router->class . '.phtml';
                }
            }

            $matches_view = array();
            preg_match('/^(.*?)(\.[^\.]+)?$/', $view_script, $matches_view);
            $js_script = base64_encode($matches_view[1] . '.js');
            $css_script = base64_encode($matches_view[1] . '.css');

            //$this->js_files = array_unique($this->js_files);
            //$this->css_files = array_unique($this->css_files);
            $this->header_html = array_unique($this->header_html);

            $data['header']['header_html'] = '';
            $data['header']['footer_html'] = '';

            $data['header']['header_html'] .= '<title>' . $this->title . '</title>' . "\n";

            // NEW CODE [ meta content-type and base href] - START
            $data['header']['header_html'] .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "\n";
            $data['header']['header_html'] .= "<base href=\"" . base_url() . "\" />\n";
            // NEW CODE [ meta content-type and base href] - END


            if ($this->meta_desc != '') {
                $data['header']['header_html'] .= '<meta name="description" content="' . $this->meta_desc . '" />' . "\n";
            }

            ////// "meta keywords" setting [Begin] //////
            if ($this->meta_keywords != '') {
                $data['header']['header_html'] .= '<meta name="keywords" content="' . $this->meta_keywords . '" />' . "\n";
            }
            ////// "meta keywords" setting [End] //////
            ///////// NEW CODE [FOR DEFAULT CSS FILES - BEGIN] /////////
            if (is_array($this->css_files) && count($this->css_files)) {
                foreach ($this->css_files as $key_css => $item_css) {
                    if (is_array($item_css) && count($item_css)) {
                        $attr = '';
                        foreach ($item_css as $key_attr => $attr_value) {
                            $attr .= ' ' . $key_attr . '="' . $attr_value . '"';
                        }
                        $data['header']['header_html'] .= '<link href="' . base_url() . $key_css . '" rel="stylesheet" type="text/css"' . $attr . ' />' . "\n";
                    } else {
                        $data['header']['header_html'] .= '<link href="' . base_url() . $key_css . '" rel="stylesheet" type="text/css" />' . "\n";
                    }
                }
            }


            // Inline css will be written in a file with the same name as view file 
            // and will be parsed by controller parse, action css.
            $data['header']['header_html'] .= '';//'<link href="' . base_url() . 'parse/css/' . $css_script . '" rel="stylesheet" type="text/css" />' . "\n";

            //dump($this->js_files);
            $bible_content_change_method = "var this_method = '';";
            if ($this->router->fetch_method() == 'all_books' || $this->router->fetch_method() == 'books_chapter' || $this->router->fetch_method() == 'verses') {
                $bible_content_change_method = "var this_method = '" . $this->router->fetch_method() . "';";
            }
            $data['header']['header_html'] .= "<script type=\"text/javascript\">
                                                   <!--
                                                    var base_url = '" . base_url() . "';
                                                    var is_login = '" . $this->session->userdata('loggedin') . "';
													" . $bible_content_change_method . "
                                                   //-->
                                                  </script>";



            ///////// NEW CODE [FOR DEFAULT JS FILES] /////////
            #$default_js_arr = array('js/jquery.hoverIntent.js'=>'header');
            $default_js_arr = array('js/jquery.hoverIntent.js' => 'header',
                'js/frontend/utils.js' => 'header');

            if (is_array($this->js_files) && count($this->js_files)) {

                $this->js_files = array_merge($this->js_files, $default_js_arr);

                foreach ($this->js_files as $js_file => $position) {
                    if ($position == 'footer') {
                        //echo 'footer';
                        $data['header']['footer_html'] .= '<script type="text/javascript" src="' . base_url() . $js_file . '"></script>' . "\n";
                    } else {
                        //echo 'header';
                        $data['header']['header_html'] .= '<script type="text/javascript" src="' . base_url() . $js_file . '"></script>' . "\n";
                    }
                }
            }

            // Inline js will be written in a file with the same name as view file 
            // and will be parsed by controller parse, action js
            $data['header']['header_html'] .= '';//'<script type="text/javascript" src="' . base_url() . 'parse/js/' . $js_script . '"></script>' . "\n";

            if (is_array($this->header_html) && count($this->header_html)) {
                foreach ($this->header_html as $header_html) {
                    $data['header']['header_html'] .= $header_html . "\n";
                }
            }


            $data['content'] = $this->load->view($view_script, $data, true);



            if (!$bool_string) {
                $this->load->view($this->layout, $data);
            } else {
                return $this->load->view($this->layout, $data, true);
            }
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    /*     * ************************************************************************ */

    public function _non_accessible_by_logged() {
        try {
            if ($this->session->userdata('loggedin') && $this->session->userdata('is_admin') != 1 && $this->session->userdata('is_admin') != 2) {
                header('location:' . base_url() . 'my-wall.html');
                exit;
            }
            /* else if( $this->session->userdata('loggedin')  && $this->session->userdata('is_admin') == 1){
              header('location:'.admin_base_url().'dashboard.html');exit;
              } */
        } catch (Exception $err_obj) {
            
        }
    }

    function _login_by_cookie() {

        //CHECK REMEMBER ME
        if (isset($_COOKIE['KB']['email']) && isset($_COOKIE['KB']['password'])) {

            $username = $_COOKIE['KB']['email'];
            // Select the username from the cookie 
            $pwd = $_COOKIE['KB']['password'];
            // Select the password from the cookie 

            $pattern = '/[\s]*\'[\s]*(.*)[\s]*\'[\s]*/';

            $login_data['s_email'] = preg_replace($pattern, "$1", $username);
            $login_data['s_password'] = preg_replace($pattern, "$1", $pwd);
            $this->load->model('users_model');
            $loggedin = $this->users_model->authenticate($login_data);
            if (!empty($loggedin)) {
                $default_redirect_url = base_url() . "index.html";
                header("location: $default_redirect_url");
                exit();
            }
        }
    }

    protected function send_message($i_sender_id, $i_receiver_id, $s_type = 'normal', $s_subject = '', $s_message = '', $allowhtml = 'noallowhtml', $data = '') {

        try {

            $this->load->model('data_messages_model');

            if ($s_type == 'normal') {
                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);

                if ($allowhtml == 'noallowhtml') {
                    $arr['s_subject'] = htmlspecialchars($s_subject, ENT_QUOTES, 'utf-8');
                    $arr['s_message'] = nl2br(htmlspecialchars($s_message, ENT_QUOTES, 'utf-8'));
                } else {
                    $arr['s_subject'] = $s_subject;
                    $arr['s_message'] = nl2br($s_message);
                }
            } else if ($s_type == 'contact_request') {

                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);
                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("contact_request");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
                $subject = nl2br(sprintf3($subject, array('sender_name' => $user_sender['s_profile_name'], 'receiver_name' => $user_receiver['s_profile_name'])));

                $body = nl2br(sprintf3($body, array('sender_name' => $user_sender['s_profile_name'], 'receiver_name' => $user_receiver['s_profile_name'], 'profile_url' => $profile_url, 'message' => nl2br($s_message))));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            } else if ($s_type == 'contact_accept') {
                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);

                # $profile_url = get_profile_url($i_sender_id,$user_sender['s_profile_name']);

                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("contact_accept");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
                $subject = nl2br(sprintf3($subject, array('sender_name' => $user_sender['s_profile_name'], 'receiver_name' => $user_receiver['s_profile_name'])));

                $body = nl2br(sprintf3($body, array('sender_name' => $user_sender['s_profile_name'], 'receiver_name' => $user_receiver['s_profile_name'], 'profile_url' => $profile_url, 'message' => nl2br($s_message))));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            } else if ($s_type == 'contact_rejected') {

                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);
                #$profile_url = get_profile_url($i_sender_id,$user_sender['s_displayname']);


                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("contact_rejected");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
                $subject = nl2br(sprintf3($subject, array('sender_name' => $user_sender['s_profile_name'], 'receiver_name' => $user_receiver['s_profile_name'])));

                $body = nl2br(sprintf3($body, array('sender_name' => $user_sender['s_profile_name'], 'receiver_name' => $user_receiver['s_profile_name'], 'profile_url' => $profile_url, 'message' => nl2br($s_message))));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            } else if ($s_type == 'contact_deleted') {

                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);
                #$profile_url = get_profile_url($i_sender_id,$user_sender['s_displayname']);


                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("contact_deleted");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
                $subject = nl2br(sprintf3($subject, array('sender_name' => $user_sender['s_profile_name'], 'receiver_name' => $user_receiver['s_profile_name'])));

                $body = nl2br(sprintf3($body, array('sender_name' => $user_sender['s_profile_name'], 'receiver_name' => $user_receiver['s_profile_name'], 'profile_url' => $profile_url, 'message' => nl2br($s_message))));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            }
            ### for prayer partner request ###
            else if ($s_type == 'prayer_partner_request') {

                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);
                #$profile_url = get_profile_url($i_sender_id,$user_sender['s_displayname']);


                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("prayer_partner_request");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
                $subject = nl2br(sprintf3($subject, array('sender_name' => $user_sender['s_profile_name'], 'receiver_name' => $user_receiver['s_profile_name'])));

                $body = nl2br(sprintf3($body, array('sender_name' => $user_sender['s_profile_name'], 'receiver_name' => $user_receiver['s_profile_name'], 'profile_url' => $profile_url, 'message' => nl2br($s_message))));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            } else if ($s_type == 'prayer_partner_accept') {

                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);

                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("prayer_partner_accept");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
                $subject = nl2br(sprintf3($subject, array('sender_name' => $user_sender['s_profile_name'], 'receiver_name' => $user_receiver['s_profile_name'])));

                $body = nl2br(sprintf3($body, array('sender_name' => $user_sender['s_profile_name'], 'receiver_name' => $user_receiver['s_profile_name'], 'profile_url' => $profile_url, 'message' => nl2br($s_message))));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            } else if ($s_type == 'prayer_partner_rejected') {

                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);
                #$profile_url = get_profile_url($i_sender_id,$user_sender['s_displayname']);


                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("prayer_partner_rejected");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
                $subject = nl2br(sprintf3($subject, array('sender_name' => $user_sender['s_profile_name'], 'receiver_name' => $user_receiver['s_profile_name'])));

                $body = nl2br(sprintf3($body, array('sender_name' => $user_sender['s_profile_name'], 'receiver_name' => $user_receiver['s_profile_name'], 'profile_url' => $profile_url, 'message' => nl2br($s_message))));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            } else if ($s_type == 'prayer_partner_deleted') {

                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);
                #$profile_url = get_profile_url($i_sender_id,$user_sender['s_displayname']);


                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("prayer_partner_deleted");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
                $subject = nl2br(sprintf3($subject, array('sender_name' => $user_sender['s_profile_name'], 'receiver_name' => $user_receiver['s_profile_name'])));

                $body = nl2br(sprintf3($body, array('sender_name' => $user_sender['s_profile_name'], 'receiver_name' => $user_receiver['s_profile_name'], 'profile_url' => $profile_url, 'message' => nl2br($s_message))));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            }
            #### for net pal ####
            else if ($s_type == 'net_pal_request') {

                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);
                #$profile_url = get_profile_url($i_sender_id,$user_sender['s_displayname']);


                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("net_pal_request");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
                $subject = nl2br(sprintf3($subject, array('sender_name' => $user_sender['s_profile_name'], 'receiver_name' => $user_receiver['s_profile_name'])));

                $body = nl2br(sprintf3($body, array('sender_name' => $user_sender['s_profile_name'], 'receiver_name' => $user_receiver['s_profile_name'], 'profile_url' => $profile_url, 'message' => nl2br($s_message))));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            } else if ($s_type == 'net_pal_accept') {

                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);
                #$profile_url = get_profile_url($i_sender_id,$user_sender['s_displayname']);


                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("net_pal_accept");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
                $subject = nl2br(sprintf3($subject, array('sender_name' => $user_sender['s_profile_name'], 'receiver_name' => $user_receiver['s_profile_name'])));

                $body = nl2br(sprintf3($body, array('sender_name' => $user_sender['s_profile_name'], 'receiver_name' => $user_receiver['s_profile_name'], 'profile_url' => $profile_url, 'message' => nl2br($s_message))));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            } else if ($s_type == 'net_pal_rejected') {

                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);
                #$profile_url = get_profile_url($i_sender_id,$user_sender['s_displayname']);


                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("net_pal_rejected");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
                $subject = nl2br(sprintf3($subject, array('sender_name' => $user_sender['s_profile_name'], 'receiver_name' => $user_receiver['s_profile_name'])));

                $body = nl2br(sprintf3($body, array('sender_name' => $user_sender['s_profile_name'], 'receiver_name' => $user_receiver['s_profile_name'], 'profile_url' => $profile_url, 'message' => nl2br($s_message))));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            } else if ($s_type == 'net_pal_deleted') {

                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);
                #$profile_url = get_profile_url($i_sender_id,$user_sender['s_displayname']);


                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("net_pal_deleted");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
                $subject = nl2br(sprintf3($subject, array('sender_name' => $user_sender['s_profile_name'], 'receiver_name' => $user_receiver['s_profile_name'])));

                $body = nl2br(sprintf3($body, array('sender_name' => $user_sender['s_profile_name'], 'receiver_name' => $user_receiver['s_profile_name'], 'profile_url' => $profile_url, 'message' => nl2br($s_message))));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            }

            ### for prayer partner request ###  
            //$this->get_new_notifications_('message_notifications');


            $arr['s_type'] = $s_type;

            return $this->data_messages_model->add_info($arr);
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    /** admin inbox * */
    protected function admin_send_message($i_sender_id, $s_type = 'ring', $s_subject = '', $s_message = '') {

        try {

            $this->load->model('data_messages_model');
            $user_id = intval(decrypt($_SESSION['user_id']));
            $newArr = get_primary_user_info($user_id);
            if ($s_type == 'report') {
                // $arr['i_sender_id']  =  $user_id;
                $arr['i_sender_id'] = $user_id;
                $arr['s_type'] = 'Report abuse Request';
                $arr['object_id'] = $s_subject;
                $arr['s_message'] = $s_message;
                $arr['dt_created_on'] = get_db_datetime();
                $arr['user_email'] = $newArr["s_email"];
                $arr['user_name'] = $newArr["s_first_name"] . ' ' . $newArr["s_last_name"];
            } else if ($s_type == 'project') {
                $arr['i_sender_id'] = $user_id;
                $arr['s_type'] = 'project Information Request';
                $arr['object_id'] = $s_subject;
                $this->load->database();
                $query = $this->db->get_where('cg_project_information', array('i_user_id' => $user_id));
                foreach ($query->result() as $row) {
                    // echo $row->title;
                    $arr['s_message'] = "<p> Hello admin@cogtime.com<p>,
                            <p>A member has requested for project Information.</p>
                             <br>
                             <p>Message :</p>
                             <p>$row->s_information</p>
                              <p>poject - $row->s_project_name</p>
                            <p>Thanks</p>
                               <p>" . $newArr["s_email"] . "</p>";
                    //$arr['s_message'] = $row->s_information.'<br>'.$row->s_project_name;
                }
                $arr['dt_created_on'] = get_db_datetime();
                $arr['user_email'] = $newArr["s_email"];
                $arr['user_name'] = $newArr["s_first_name"] . ' ' . $newArr["s_last_name"];
            } else if ($s_type == 'church') {
                $arr['i_sender_id'] = $user_id;
                $arr['s_type'] = 'church';
                $arr['object_id'] = $s_subject;
                $arr['s_message'] = "<p> Hello admin@cogtime.com<p>,
                            <p>A member has requested for church.</p>
                             <br>
                            <p>Thanks</p>
                               <p>" . $newArr["s_email"] . "</p>";

                $arr['dt_created_on'] = get_db_datetime();
                $arr['user_email'] = $newArr["s_email"];
                $arr['user_name'] = $newArr["s_first_name"] . '  ' . $newArr["s_last_name"];
                //$this->data_messages_model->church_req($i_sender_id, $s_type,$s_subject,$s_message);
            } else if ($s_type == 'ring') {
                $arr['i_sender_id'] = $user_id;
                $arr['s_type'] = 'Ring Category Request';
                $arr['object_id'] = $s_subject;
                ;
                $arr['s_message'] = $s_message;
                $arr['dt_created_on'] = get_db_datetime();
                $arr['user_email'] = $newArr["s_email"];
                $arr['user_name'] = $newArr["s_first_name"] . '  ' . $newArr["s_last_name"];
            }



            $this->data_messages_model->admin_add_info($arr);

            $arr['s_type'] = $s_type;

            // return $this->data_messages_model->add_admin_info($arr);
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    /*     * **
     * put your comment there...
     * 
     * @$user_type 3=>enterprise, 4=>freight
     */

    public function check_login($store_url_session = TRUE, $user_type, $arr_autorized_user_types = array()) {
        try {

            $this->session->unset_userdata('loginPopUp');
            if ($this->session->userdata('loggedin') == '' || $this->session->userdata('loggedin') == false || (trim($user_type) != "" && intval($user_type) != intval($this->session->userdata('user_type')) || (!empty($arr_autorized_user_types) && !in_array(intval($this->session->userdata('user_type')), $arr_autorized_user_types)) )
            ) {

                $url = my_current_url();

                if ($store_url_session == TRUE) {
                    // Not an ajax request
                    if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
                        $this->session->set_userdata('session_referrer', $url);
                    }
                }
                //echo $url;
                /* echo "<script>window.location='".base_url()."index.html'+window.location.hash</script>"; */
                if ($this->session->userdata('loggedin') == '' || $this->session->userdata('loggedin') == false)
                    echo "<script>window.location='" . base_url() . "'+window.location.hash</script>";
                else
                    header("location:" . base_url());

                exit;
            }
            else {

                $this->session->unset_userdata('session_referrer');
            }
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    protected function _set_user_left_panel_data($user_id) {
        $this->load->model('users_model');
        $this->data['left_panel']['user_detail'] = $this->users_model->get_by_id($user_id);
        //pr($this->data['left_panel']['user_detail']);
    }

    protected function _set_all_photo_album_data($user_id) {
        $this->load->model('photo_albums_model');
        $this->data['all_photo_albums'] = $this->photo_albums_model->get_by_user_id($user_id);
        //pr($this->data['all_photo_albums']);
    }

    protected function _set_all_audio_album_data($user_id) {
        $this->load->model('audio_albums_model');
        $this->data['all_audio_albums'] = $this->audio_albums_model->get_by_user_id($user_id);
    }

    ## shows footer menu.

    protected function _set_cms_menu_footer() {
        $this->load->model('cms_model');

        if ($this->session->userdata('loggedin') != '' || $this->session->userdata('loggedin') != false) {
            $where = " WHERE 1 AND i_status = 1 AND (e_show_in_logged_in_page = 'yes' OR e_both ='yes') ";
            $this->data['footer_cms_menu'] = $this->cms_model->cms_menu($where);
        } else {
            $where = " WHERE 1 AND i_status = 1 AND (e_show_in_non_logged_in_page = 'yes' OR e_both ='yes')";
            $this->data['footer_cms_menu'] = $this->cms_model->cms_menu($where);
        }

        //pr($this->data['footer_cms_menu']);
    }

    ## shows latest news.
    /* protected function _set_latest_news() {
      $this->load->model('scrolling_headlines_model');
      $where = "WHERE i_status = 1 ";

      $this->data['scrolling_news'] = $this->scrolling_headlines_model->get_scolling_news($where,0,6);
      } */

    protected function _set_latest_news() {
	try{
        $this->load->library('rssparser');
        $this->load->model('scrolling_headlines_model');
        // load library
       // $url = $this->scrolling_headlines_model->get_feed_url_by_id(1);
        //echo $url;
       $rss=array();
        $this->rssparser->set_feed_url($this->scrolling_headlines_model->get_feed_url_by_id(1));  // get feed
       $this->rssparser->set_cache_life(30);                       // Set cache life time in minutes
        $rss[] = $this->rssparser->getFeed(2);
		$this->rssparser->set_feed_url($this->scrolling_headlines_model->get_feed_url_by_id(2));  // get feed
       $this->rssparser->set_cache_life(30);                       // Set cache life time in minutes
        $rss[] = $this->rssparser->getFeed(2);
		$this->rssparser->set_feed_url($this->scrolling_headlines_model->get_feed_url_by_id(3));  // get feed
        $this->rssparser->set_cache_life(30);                       // Set cache life time in minutes
        $rss[] = $this->rssparser->getFeed(2);
		//pr($rss);
		$feeds=array();
		foreach($rss as $val)
		{
		$feeds[] = $val[0];
		$feeds[] = $val[1];
		//$feeds[] = $val;
		}
		$this->data['scrolling_news']=$feeds;
		}
		catch (Exception $err_obj) {
            
        }
    }

    protected function _set_user_prayer_patner_data($i_profile_id) {
        $this->load->model('my_prayer_partner_model');

        $WHERE = " WHERE 
						1
						AND c.s_status = 'accepted' 
						AND u.i_status=1 
						AND
						((c.i_requester_id = '" . $i_profile_id . "' AND u.id=c.i_accepter_id ) 
						OR (c.i_accepter_id = '" . $i_profile_id . "' AND u.id=c.i_requester_id ))  GROUP BY u.id ";

        $ORDER_BY = "u.s_first_name ASC";
        $result = $this->my_prayer_partner_model->fetch_multi_online_friends($WHERE, 0, 4, $ORDER_BY);

        $this->data['right_panel']['prayer_partner_arr'] = $result;
        //pr($this->data['right_panel']['prayer_partner_arr']);
    }

    protected function _set_user_friends_data($i_profile_id) {
        $this->load->model('contacts_model');

        $WHERE = " WHERE 
						1
						AND c.s_status = 'accepted' 
						AND u.i_status=1 
						AND
						((c.i_requester_id = '" . $i_profile_id . "' AND u.id=c.i_accepter_id ) 
						OR (c.i_accepter_id = '" . $i_profile_id . "' AND u.id=c.i_requester_id ))  GROUP BY u.id ";

        $ORDER_BY = "u.id DESC";

        $result = $this->contacts_model->fetch_multi_online_friends($WHERE, null, null, $ORDER_BY);
        $this->data['right_panel']['friends_arr'] = $result;
        //pr($this->data['right_panel']['friends_arr']);
    }

    protected function _set_user_netpals_data($i_profile_id) {
        $this->load->model('netpals_model');

        $WHERE = " WHERE 
                        1
                        
                        AND u.i_status=1 
                        
                        AND ((c.i_requester_id = " . $i_profile_id . " AND u.id=c.i_accepter_id) 
                            OR 
                        (c.i_accepter_id=" . $i_profile_id . " AND u.id=c.i_requester_id))
                        AND c.s_status='accepted'  GROUP BY u.id ";

        $ORDER_BY = "u.id DESC";

        $result = $this->netpals_model->fetch_multi_online_netpals($WHERE, null, null, $ORDER_BY);
        $this->data['right_panel']['netpals_arr'] = $result;
        //pr($this->data['right_panel']['netpals_arr']);
    }

    ## ------------------- photo section ----------------------##

    protected function _get_user_all_photos($i_profile_id, $s_where = '', $start_limit = '', $end_limit = '') {
        $this->load->model('user_photos_model');
        //$all_photo_arr = $this->user_photos_model->get_by_user_id($i_profile_id);
        $all_photo_arr = $this->user_photos_model->get_allphotos_with_comments_by_user_id_($i_profile_id, $s_where, $start_limit, $end_limit);

        /// pr($this->data['arr_photo_detail']);
        $this->data['public_profile']['photo_res'] = $all_photo_arr;
    }

    protected function _get_user_all_photo_albums($i_profile_id, $start_limit = '', $end_limit = '') {
        $this->load->model('photo_albums_model');
        $photo_album_res = $this->photo_albums_model->get_by_user_id($i_profile_id, $start_limit, $end_limit);



        $this->data['public_profile']['photo_album_res'] = $photo_album_res;
    }

    ## ------------------- /photo section ----------------------##
    ##--------------- video section ----------------------##

    protected function _get_user_all_videos($i_profile_id, $s_where = '', $start_limit = '', $end_limit = '') {
        $this->load->model('my_videos_model');
        $all_photo_arr = $this->my_videos_model->get_allvideos_with_comments_by_user_id_($i_profile_id, $s_where, $start_limit, $end_limit);

        $this->data['public_profile']['video_res'] = $all_photo_arr;
    }

    protected function _get_user_all_video_albums($i_profile_id, $start_limit = '', $end_limit = '') {
        $this->load->model('my_videos_model');
        $photo_album_res = $this->my_videos_model->get_all_video_albums_with_count($i_profile_id, $start_limit, $end_limit);
        $this->data['public_profile']['video_album_res'] = $photo_album_res;
    }

    ##--------------- /video section ----------------------##
    ##--------------- audio section ----------------------##

    protected function _get_user_all_audios($i_profile_id, $s_where = '', $start_limit = '', $end_limit = '') {
        $this->load->model('user_audios_model');
        $all_photo_arr = $this->user_audios_model->get_allaudios_with_comments_by_user_id_($i_profile_id, $s_where, $start_limit, $end_limit);
        $this->data['public_profile']['audio_res'] = $all_photo_arr;
    }

    protected function _get_user_all_audio_albums($i_profile_id, $start_limit = '', $end_limit = '') {
        $this->load->model('audio_albums_model');
        $photo_album_res = $this->audio_albums_model->get_by_user_id($i_profile_id, $start_limit, $end_limit);
        $this->data['public_profile']['audio_album_res'] = $photo_album_res;
    }

    ##--------------- /audio section ----------------------##

    protected function _get_total_unread_msgs($i_profile_id) {
        $this->load->model('data_messages_model');
        $s_where = " AND  m.i_is_unread = 1 ";
        $this->data['left_panel']['total_unread_messages'] = $this->data_messages_model->get_total_by_receiver($i_profile_id, $s_where);
    }

    protected function _set_left_panel_data($i_profile_id) {
        $this->load->model('users_model');
        $arr_profile_info = $this->users_model->fetch_this($i_profile_id);

        $this->load->model('my_prayer_partner_model');
        $this->load->model('netpals_model');
        $this->load->model('contacts_model');
        $this->load->model('prayer_group_model');
        $this->load->model('my_ring_model');

        $prayer_count = $this->my_prayer_partner_model->total_pending_prayer_partner_recieved($i_profile_id);
        $netpal_count = $this->netpals_model->total_pending_netpal_received($i_profile_id);
        $friend_count = $this->contacts_model->total_pending_friend_recieved($i_profile_id);
        $prayergrp_notification_count = $this->prayer_group_model->get_total_pending_groups_requests($i_profile_id);

        $wh_ring_inv_count = ' AND r.i_invited_id="' . $i_profile_id . '"';
        $wh = " AND rg.i_user_id = '" . $i_profile_id . "'";

        $ring_notification_count = $this->my_ring_model->new_gettotal_ring_join_req($wh) +
                $this->my_ring_model->gettotal_ring_inv_nw($wh_ring_inv_count);


        $prayer_room_notification_count = $this->prayer_group_model->getTotalPrayerRoom($i_profile_id);

        $arr_profile_info['prayer_count'] = $prayer_count;
        $arr_profile_info['netpal_count'] = $netpal_count;
        $arr_profile_info['friend_count'] = $friend_count;
        $arr_profile_info['prayergrp_notification_count'] = $prayergrp_notification_count;
        $arr_profile_info['ring_notification_count'] = $ring_notification_count;
        $arr_profile_info['prayer_room_notification_count'] = $prayer_room_notification_count;



        ##### get online status 
        $arr_profile_info['user_status'] = $this->users_model->getUserOnlineStatus($i_profile_id);

        ### total prayer partner 
        $arr_profile_info['total_prayer_partner'] = $this->my_prayer_partner_model->total_prayer_partner($i_profile_id);

        $this->data['arr_profile_info'] = $arr_profile_info;
        //pr($arr_profile_info);

        $this->data['user_local_time_to_display'] = getUserLocalTime($arr_profile_info['s_time']);
        
    }

    public function show_event_calendar($user_id = '', $year = '', $month = '', $temp = '', $displayslider = 0) {
        if (empty($year)) {
            $year = date('Y');
        }
        if (empty($month)) {
            $month = date('m');
        }

        $conf = array('show_next_prev' => true,
            'day_type' => 'short'
        );

        $conf['template'] = '
           {table_open}<div class="organize-calender">{/table_open}
           {heading_row_start}<tr>{/heading_row_start}
           {heading_previous_cell}<th></th>{/heading_previous_cell}
           {heading_title_cell}<th class="cal_month" colspan="{colspan}"></th>    {/heading_title_cell}
           {heading_next_cell}<th></th>{/heading_next_cell}
           {heading_row_end}</tr>{/heading_row_end}
           {week_row_start}<div class="month_cal_heading">{/week_row_start}
           {week_day_cell}<div>{week_day}</div>{/week_day_cell}
           {week_row_end}</div><ul class="day-list">{/week_row_end}

           {cal_row_start}<li><div class="month-data-list"><div class="month_cal_body">{/cal_row_start}
           {cal_cell_start}<div onclick="gotoDay(\'{content}\')">{/cal_cell_start}

          	
		   
		   {cal_cell_content}<span class="date">{day}</span>{event_type}<br class="clear">{/cal_cell_content}
           {cal_cell_content_today}<span class="date">{day}</span>{event_type}<br class="clear">{/cal_cell_content_today}
		   
		   

           {cal_cell_no_content}<a title="{content}" class="" href="{content}">{day}</a>{/cal_cell_no_content}
           {cal_cell_no_content_today}<div class="active alarm_div" onclick="window.location=\'{content}\'">{day}</div>{/cal_cell_no_content_today}
           {cal_cell_blank}&nbsp;{/cal_cell_blank}

           {cal_cell_end}</div>{/cal_cell_end}
           {cal_row_end}</div></div></li>{/cal_row_end}

           {table_close} </ul><ul class="legends">
                                <li><img src="images/icons/add_note.png" alt="note" width="16" height="16" />&raquo; Note</li>
                                <li><img src="images/icons/add_list.png" alt="todo" width="16" height="16" />&raquo; To-Do List</li>
                                <li><img src="images/icons/add_event.png" alt="event" width="13" height="17" />&raquo; Event</li>
                            </ul>
                            <div class="clr"></div>
                        </div>{/table_close}
        ';


        $this->lang->load('calendar', 'english');

        $this->load->library('parser');

        $this->load->library('calendar', $conf);

        $this->load->model('organizer_note_model');
        $this->load->model('organizer_todo_model');
        $this->load->model('organizer_events_model');
        $this->load->model('events_model');

        $s_where = 'AND i_active = 1 AND YEAR(`d_date`)= ' . $year . ' AND MONTH(`d_date`)= ' . $month;
        $s_b_where = 'AND YEAR(`dt_created_date`)= ' . $year . ' AND MONTH(`dt_created_date`)= ' . $month;


        $note_list = $this->organizer_note_model->get_allNotes_by_user_id($user_id, $s_where, $s_b_where);

        //pr($note_list);

        $s_where = 'AND i_active = 1 AND YEAR(`d_date`)= ' . $year . ' AND MONTH(`d_date`)= ' . $month;
        $todo_list = $this->organizer_todo_model->get_by_user_id($user_id, $s_where);

        $s_where = 'AND e.i_status =  1 AND YEAR(e.dt_start_time)= ' . $year . ' AND MONTH(e.dt_start_time)= ' . $month;
        $events_list = $this->events_model->get_my_events($user_id, $s_where);



        $events_array = array_merge($note_list, $todo_list);
        $total_events_array = array_merge($events_array, $events_list);
        #pr($total_events_array);
        ### get total days in a selected month year ####

        $num = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $event_html = '<img src="images/add_list_sml.jpg"/><img src="images/add_note_sml.jpg"/><img src="images/add_event_sml.png"/>';

        //pr($todo_list);
        $event_arr = array();
        for ($i = 1; $i <= $num; $i++) {

            $track_event_type = array();
            $date_string = $year . '-' . $month . '-' . $i;
            $cal_note[$i] = $date_string;
            foreach ($total_events_array as $key => $info) :

                //echo str_pad($i, 2, '0', STR_PAD_LEFT) .'== '.substr($info['d_date'],8,2).' <br/>'; 
                if (str_pad($i, 2, '0', STR_PAD_LEFT) == substr($info['d_date'], 8, 2)) {

                    if ($info['event_type'] == ' [ D ] ' && $info['s_type_to_do'] == 'personal_event') {
                        $event_arr[$i]['D'] = '<img src="images/add_list_sml.jpg"/>';
                    } else if ($info['event_type'] == ' [ D ] ') {
                        $event_arr[$i]['D'] = '<img src="images/add_list_sml.jpg"/>';
                    }


                    if ($info['event_type'] == ' [ E ] ') {
                        $event_arr[$i]['E'] = '<img src="images/add_event_sml.png"/>';
                    }
                } else if (str_pad($i, 2, '0', STR_PAD_LEFT) == substr($info['dt_start_time'], 8, 2)) {
                    if ($info['event_type'] == ' [ E ] ') {
                        $event_arr[$i]['E'] = '<img src="images/add_event_sml.png"/>';
                    }
                } else if (str_pad($i, 2, '0', STR_PAD_LEFT) == substr($info['dt_added'], 8, 2)) {

                    if ($info['event_type'] == ' [ N ] ') {
                        $event_arr[$i]['N'] = '<img src="images/add_note_sml.jpg"/>';
                    }
                }

                $counter++;
            endforeach;
        }

        # pr($event_arr);

        $cal = $this->calendar->generate($year, $month, $cal_note, $event_arr);
        $data['cal'] = $cal;
        $data['uid'] = $user_id;

        // pr($cal,1);
        //pr($user_id);
        if ($temp) {

            if ($displayslider == 1) {

                $PreviousMonth = date('m', mktime(0, 0, 0, $month - 1, 1, $year));
                $PreviousYear = date('Y', mktime(0, 0, 0, $month - 1, 1, $year));

                $CurrentMonth = date('m', mktime(0, 0, 0, $month, 1, $year));
                $CurrentYear = date('Y', mktime(0, 0, 0, $month, 1, $year));

                $NextMonth = date('m', mktime(0, 0, 0, $month + 1, 1, $year));
                $NextYear = date('Y', mktime(0, 0, 0, $month + 1, 1, $year));


                $display_date = date('F, Y', mktime(0, 0, 0, $CurrentMonth, 1, $CurrentYear));

                $sliderContent = '<img src="images/icons/calender-right.png" class="calender-left-arrow" alt="" onclick="show_cal_contents_by_month(' . $NextYear . ', ' . $NextMonth . ', \'next\')"/>
										<img src="images/icons/calender-left.png" class="calender-left-arrow" alt="" onclick="show_cal_contents_by_month(' . $PreviousYear . ',' . $PreviousMonth . ',\'prev\')"/>';

                $html = $this->load->view('logged/organize/right_event_calendar_ajax.phtml', $data, TRUE);
                echo json_encode(array('html' => $html, 'sliderContent' => $sliderContent, 'display_date' => $display_date));
                exit;
            } else {
                echo $this->load->view('logged/organize/right_event_calendar_ajax.phtml', $data, TRUE);
            }
        }

        return $cal;
    }

    public function get_new_request_() {

        $i_user_id = intval(decrypt($this->session->userdata('user_id')));

        $this->load->model('my_prayer_partner_model');
        $this->load->model('netpals_model');
        $this->load->model('contacts_model');
        $this->load->model('prayer_group_model');
        $this->load->model('my_ring_model');


        $prayer_count = $this->my_prayer_partner_model->total_pending_prayer_partner_recieved($i_user_id);
        $netpal_count = $this->netpals_model->total_pending_netpal_received($i_user_id);
        $friend_count = $this->contacts_model->total_pending_friend_recieved($i_user_id);
        $prayergrp_notification_count = $this->prayer_group_model->get_total_pending_groups_requests($i_user_id);

        $prayer_room_notification_count = $this->prayer_group_model->getTotalPrayerRoom($i_user_id);

        $wh_ring_inv_count = ' AND r.i_invited_id="' . $i_user_id . '"';
        $wh = " AND rg.i_user_id = '" . $i_user_id . "'";

        $ring_notification_count = $this->my_ring_model->new_gettotal_ring_join_req($wh) +
                $this->my_ring_model->gettotal_ring_inv_nw($wh_ring_inv_count);


        echo json_encode(array('result' => 'success', 'prayer_count' => $prayer_count, 'netpal_count' => $netpal_count, 'friend_count' => $friend_count, 'prayergrp_notification_count' => $prayergrp_notification_count, 'prayer_room_notification_count' => $prayer_room_notification_count, 'ring_notification_count' => $ring_notification_count));
        exit;
    }

    public function get_new_notifications() {

        $i_user_id = intval(decrypt($this->session->userdata('user_id')));

        $this->load->model('user_notifications_model');


        $s_where = "  AND i_notification_shown = 1 ";


        $notification_array = $this->user_notifications_model->get_by_user_id($i_user_id, $s_where);

        ## UPDATE NOTIFICAION ARRAY

        $arr['i_notification_shown'] = 2;
        $friend_arr = array();
        $friend_declined_arr = array();
        $friend_accepted_arr = array();

        $prayer_partner_arr = array();
        $prayer_partner_declined_arr = array();
        $prayer_partner_accepted_arr = array();

        $netpal_arr = array();
        $netpal_declined_arr = array();
        $netpal_accepted_arr = array();

        $prayer_points_arr = array();
        $message_arr = array();

        $photo_comment = array();
        $video_comment = array();
        $audio_comment = array();

        $event_comment = array();
        $event_invitation = array();

        $blog_comment = array();
        $ring_comment = array();
        $ring_post = array();


        $tweet_comment = array();
        $retweet = array();

        $prayer_r_commit = array();

        $prayer_group_invitation = array();
        $prayer_group_accept_join_request = array();
        $prayer_group_deny_join_request = array();

        $prayer_group_chat_room_invitation = array();
        $prayer_group_joining = array();

        $etrade_request_recvd = array();
        $etrade_request_accept = array();
        $etrade_request_declined = array();
        $etrade_request_canceled_buyer = array();

        $eswap_request_recvd = array();
        $eswap_request_accept = array();
        $eswap_request_declined = array();

        $efreebie_request_recvd = array();
        $efreebie_request_accept = array();
        $efreebie_request_declined = array();

        $user_chat_room_invitation = array();

        $emergency_prayer_click = array();


        if (is_array($notification_array) && count($notification_array)) {



            foreach ($notification_array as $key => $val) {

                if ($val['s_type'] == 'friend') {
                    array_push($friend_arr, $val);
                } else if ($val['s_type'] == 'friend_request_decline') {
                    array_push($friend_declined_arr, $val);
                } else if ($val['s_type'] == 'friend_request_accepted') {
                    array_push($friend_accepted_arr, $val);
                }
                ## NETPAL PART 
                else if ($val['s_type'] == 'netpal') {
                    array_push($netpal_arr, $val);
                } else if ($val['s_type'] == 'netpal_request_decline') {
                    array_push($netpal_declined_arr, $val);
                } else if ($val['s_type'] == 'netpal_request_accepted') {
                    array_push($netpal_accepted_arr, $val);
                }
                ## NETPAL PART
                ## PP PART ###
                else if ($val['s_type'] == 'prayer_p') {
                    array_push($prayer_partner_arr, $val);
                } else if ($val['s_type'] == 'prayer_p_request_decline') {
                    array_push($prayer_partner_declined_arr, $val);
                } else if ($val['s_type'] == 'prayer_p_request_accepted') {
                    array_push($prayer_partner_accepted_arr, $val);
                }
                ## PP PART ###	  
                else if ($val['s_type'] == 'prayer_points') {
                    array_push($prayer_points_arr, $val);
                } else if ($val['s_type'] == 'message') {
                    array_push($message_arr, $val);
                }
                ## photo comment ##	  
                else if ($val['s_type'] == 'photo_comment') {
                    array_push($photo_comment, $val);
                }
                ## video comment ##	  
                else if ($val['s_type'] == 'video_comment') {
                    array_push($video_comment, $val);
                }
                ## audio comment ##	  
                else if ($val['s_type'] == 'audio_comment') {
                    array_push($audio_comment, $val);
                }
                ## event comment
                else if ($val['s_type'] == 'event_comment') {
                    array_push($event_comment, $val);
                } else if ($val['s_type'] == 'event_invitation') {
                    array_push($event_invitation, $val);
                }
                ##  tweet comment
                else if ($val['s_type'] == 'tweet_comment') {
                    array_push($tweet_comment, $val);
                }
                ##  tweet comment
                else if ($val['s_type'] == 'retweet') {
                    array_push($retweet, $val);
                }
                ##  ring_comment comment
                else if ($val['s_type'] == 'ring_comment') {
                    array_push($ring_comment, $val);
                }
                ##ring_post
                else if ($val['s_type'] == 'ring_post') {
                    array_push($ring_post, $val);
                }
                ##  blog_comment comment
                else if ($val['s_type'] == 'blog_comment') {
                    array_push($blog_comment, $val);
                }

                ## prayer_r_commit
                else if ($val['s_type'] == 'prayer_r_commit') {
                    array_push($prayer_r_commit, $val);
                }

                ## prayer group invitaion
                else if ($val['s_type'] == 'prayer_group_invitation') {
                    array_push($prayer_group_invitation, $val);
                }
                ## prayer group invitaion accept
                else if ($val['s_type'] == 'prayer_group_accept_join_request') {
                    array_push($prayer_group_accept_join_request, $val);
                }
                ## prayer group invitaion reject
                else if ($val['s_type'] == 'prayer_group_deny_join_request') {
                    array_push($prayer_group_deny_join_request, $val);
                }
                ## $prayer_group_chat_room_invitation
                else if ($val['s_type'] == 'prayer_group_chat_room_invitation') {
                    array_push($prayer_group_chat_room_invitation, $val);
                }
                ## $prayer_group_joining
                else if ($val['s_type'] == 'prayer_group_joining') {
                    array_push($prayer_group_joining, $val);
                }

                ### etrade 
                else if ($val['s_type'] == 'etrade_request_recvd') {
                    array_push($etrade_request_recvd, $val);
                } else if ($val['s_type'] == 'etrade_request_accpt') {
                    array_push($etrade_request_accept, $val);
                } else if ($val['s_type'] == 'etrade_request_declined') {
                    array_push($etrade_request_declined, $val);
                } else if ($val['s_type'] == 'etrade_request_canceled_buyer') {
                    array_push($etrade_request_canceled_buyer, $val);
                }

                ### eswap
                else if ($val['s_type'] == 'eswap_request_recvd') {
                    array_push($eswap_request_recvd, $val);
                } else if ($val['s_type'] == 'eswap_request_accpt') {
                    array_push($eswap_request_accept, $val);
                } else if ($val['s_type'] == 'eswap_request_declined') {
                    array_push($eswap_request_declined, $val);
                }
                ### efreebie
                else if ($val['s_type'] == 'efreebie_request_recvd') {
                    array_push($efreebie_request_recvd, $val);
                } else if ($val['s_type'] == 'efreebie_request_accpt') {
                    array_push($efreebie_request_accept, $val);
                } else if ($val['s_type'] == 'efreebie_request_declined') {
                    array_push($efreebie_request_declined, $val);
                }

                ### user_chat_room_invitation
                else if ($val['s_type'] == 'user_chat_room_invitation') {
                    array_push($user_chat_room_invitation, $val);
                }

                ### emergency_prayer_click
                else if ($val['s_type'] == 'emergency_prayer_click') {
                    array_push($emergency_prayer_click, $val);
                }
            }
        }


        $notification_message = "";
        $total_friend = count($friend_arr);

        ########################### friend section ############################

        if ((is_array($friend_arr) && count($friend_arr))) {

            $notification_message .= '<span class="blueName">You have recieved ' . count($friend_arr) . ' new </span> Friend Requests.';
        }

        if ((is_array($friend_declined_arr) && count($friend_declined_arr))) {

            foreach ($friend_declined_arr as $key => $val) {
                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has refused your friend request.</span>';
                } else
                    $notification_message .= '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has refused your friend request.</span>';
            }
        }

        if ((is_array($friend_accepted_arr) && count($friend_accepted_arr))) {

            foreach ($friend_accepted_arr as $key => $val) {

                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has accepted your friend request.</span>';
                } else
                    $notification_message .= '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has accepted your friend request.</span>';
            }
        }


        ########################### friend section #############################
        //pr($friend_accepted_arr);
        ############################ netpal section ############################

        if ((is_array($netpal_arr) && count($netpal_arr))) {

            $notification_message .= '<span class="blueName">You have recieved ' . count($netpal_arr) . ' new </span> Netpal Requests.';
        }

        if ((is_array($netpal_declined_arr) && count($netpal_declined_arr))) {

            foreach ($netpal_declined_arr as $key => $val) {
                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has refused your netpals request. </span>';
                } else
                    $notification_message .= '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has refused your netpals request. </span>';
            }
        }

        if ((is_array($netpal_accepted_arr) && count($netpal_accepted_arr))) {

            foreach ($netpal_accepted_arr as $key => $val) {

                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has accepted your netpals request. </span>';
                } else
                    $notification_message .= '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has accepted your netpals request </span>.';
            }
        }


        ############################ netpal section ###################################
        ########################### prayer partner section ############################
        if ((is_array($prayer_partner_arr) && count($prayer_partner_arr))) {

            $notification_message .= '<span class="blueName">You have recieved ' . count($prayer_partner_arr) . ' new </span> <span class="blueName"> Prayer Partner Requests. </span>';
        }


        if ((is_array($prayer_partner_declined_arr) && count($prayer_partner_declined_arr))) {

            foreach ($prayer_partner_declined_arr as $key => $val) {
                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has refused your </span> <span class="blueName"> prayer partner request.</span>';
                } else
                    $notification_message .= '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has refused your </span> <span class="blueName"> prayer partner request.</span>';
            }
        }

        if ((is_array($prayer_partner_accepted_arr) && count($prayer_partner_accepted_arr))) {

            foreach ($prayer_partner_accepted_arr as $key => $val) {

                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has accepted your prayer partner request </span>.';
                } else
                    $notification_message .= '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has accepted your prayer partner request </span>.';
            }
        }



        ########################### END  prayer partner section ########################################################
        ############################# START PRAYER POINTS SECTION #############################################################	
        if ((is_array($prayer_points_arr) && count($prayer_points_arr))) {

            $notification_message .= '<span class="blueName">You have recieved ' . count($prayer_points_arr) . ' new </span> prayer points.';
        }

        if ((is_array($message_arr) && count($message_arr))) {

            $notification_message .= '<span class="blueName">You have recieved ' . count($message_arr) . ' new </span> messages.';
        }

        ############################# END PRAYER POINTS SECTION #############################################################
        ############################# START PHOTO  SECTION ################################################

        if ((is_array($photo_comment) && count($photo_comment))) {

            foreach ($photo_comment as $key => $val) {

                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has commented on your photo.</span>';
                } else
                    $notification_message .= '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has commented on your photo.</span>';
            }
        }

        ############################# END PHOTO SECTION ################################################
        ############################# START VIDEO SECTION ################################################

        if ((is_array($video_comment) && count($video_comment))) {

            foreach ($video_comment as $key => $val) {

                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has commented on your video.</span>';
                } else
                    $notification_message .= '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has commented on your video.</span>';
            }
        }

        ############################# END VIDEO SECTION ################################################
        ############################# START audio SECTION ################################################

        if ((is_array($audio_comment) && count($audio_comment))) {

            foreach ($audio_comment as $key => $val) {

                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has commented on your audio.</span>';
                } else
                    $notification_message .= '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has commented on your audio.</span>';
            }
        }

        ############################# END audio SECTION ################################################
        # SOCIAL HUB SECTION
        ############################# START EVENTS SECTION ################################################

        if ((is_array($event_comment) && count($event_comment))) {

            foreach ($event_comment as $key => $val) {

                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has commented on your event.</span>';
                } else
                    $notification_message .= '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has commented on your event.</span>';
            }
        }



        if ((is_array($event_invitation) && count($event_invitation))) {

            foreach ($event_invitation as $key => $val) {

                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has invited you on his/her event.</span>';
                } else
                    $notification_message .= '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has invited you on his/her event.</span>';
            }
        }

        ############################# END EVENTS SECTION ################################################
        ############################# START tweet SECTION ################################################

        if ((is_array($tweet_comment) && count($tweet_comment))) {

            foreach ($tweet_comment as $key => $val) {

                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has posted on Tweet.</span>';
                } else
                    $notification_message .= '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has posted on Tweet.</span>';
            }
        }


        if ((is_array($retweet) && count($retweet))) {

            foreach ($retweet as $key => $val) {

                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has re-tweeted your post.</span>';
                } else
                    $notification_message .= '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has re-tweeted your post.</span>';
            }
        }


        ############################# END tweet SECTION ################################################
        ############################# START blog_comment SECTION ################################################

        if ((is_array($blog_comment) && count($blog_comment))) {

            foreach ($blog_comment as $key => $val) {

                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has commented on an article in blog.</span>';
                } else
                    $notification_message .= '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has commented on an article in blog.</span>';
            }
        }


        ############################# END blog_comment SECTION ################################################
        ############################# START ring_comment SECTION ################################################

        if ((is_array($ring_comment) && count($ring_comment))) {

            foreach ($ring_comment as $key => $val) {

                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has commented on a post of ring you have joined/created.</span>';
                } else
                    $notification_message .= '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has commented on a post of ring you have joined/created.</span>';
            }
        }


        ############################# END ring_comment SECTION ################################################
        ############################# START ring_comment SECTION ################################################

        if ((is_array($ring_post) && count($ring_post))) {

            foreach ($ring_post as $key => $val) {

                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has posted in ring you have joined/created.</span>';
                } else
                    $notification_message .= '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has posted in ring you have joined/created.</span>';
            }
        }


        ############################# END ring_comment SECTION ################################################
        ############################# START ring_comment SECTION ################################################

        if ((is_array($prayer_r_commit) && count($prayer_r_commit))) {

            foreach ($prayer_r_commit as $key => $val) {

                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has added his/her commitments to the prayer request you have created.</span>';
                } else
                    $notification_message .= '<span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has added his/her commitments to the prayer request you have created.</span>';
            }
        }


        ############################# END ring_comment SECTION ################################################
        ############################# START prayer_group_invitation SECTION ################################################

        if ((is_array($prayer_group_invitation) && count($prayer_group_invitation))) {

            foreach ($prayer_group_invitation as $key => $val) {

                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<a href="' . base_url() . 'my-msg-inbox.html"><span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has invited you to his/her prayer group.</span></a>';
                } else
                    $notification_message .= '<a href="' . base_url() . 'my-msg-inbox.html"><span class="blueName">' . get_username_by_id($val['i_requester_id']) . 'has invited you to his/her prayer group.</span></a>';
            }
        }


        ############################# END prayer_group_invitation SECTION ################################################
        ############################# START prayer_group_invitation accepted  ################################################

        if ((is_array($prayer_group_accept_join_request) && count($prayer_group_accept_join_request))) {

            foreach ($prayer_group_accept_join_request as $key => $val) {

                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<a href="' . base_url() . 'my-msg-inbox.html"><span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has accepted your prayer group invitation</span>.</a>';
                } else
                    $notification_message .= '<a href="' . base_url() . 'my-msg-inbox.html"><span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has accepted your prayer  prayer group invitation</span>.</a>';
            }
        }
        ############################# END prayer_group_invitation accepted ################################################
        ############################# START prayer_group_invitation denied  ################################################

        if ((is_array($prayer_group_deny_join_request) && count($prayer_group_deny_join_request))) {

            foreach ($prayer_group_deny_join_request as $key => $val) {

                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<a href="' . base_url() . 'my-msg-inbox.html"><span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has declined your prayer group invitation</span>.</a>';
                } else
                    $notification_message .= '<a href="' . base_url() . 'my-msg-inbox.html"><span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has declined your prayer  prayer group invitation</span>.</a>';
            }
        }
        ############################# END prayer_group_invitation accepted ################################################
        ############################# START prayer_group_chat_room_invitation   ################################################

        if ((is_array($prayer_group_chat_room_invitation) && count($prayer_group_chat_room_invitation))) {

            foreach ($prayer_group_chat_room_invitation as $key => $val) {

                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<a href="' . base_url() . 'my-msg-inbox.html"><span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has invited you to his/her chat room of prayer group</span>.</a>';
                } else
                    $notification_message .= '<a href="' . base_url() . 'my-msg-inbox.html"><span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has invited you to his/her chat room of prayer group</span>.</a>';
            }
        }
        ############################# END prayer_group_chat_room_invitation  ################################################
        ############################# START prayer_group_joining   ################################################


        if ((is_array($prayer_group_joining) && count($prayer_group_joining))) {

            foreach ($prayer_group_joining as $key => $val) {

                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<a href="' . base_url() . 'my-msg-inbox.html"><span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has sent you request to join your prayer group</span>.</a>';
                } else
                    $notification_message .= '<a href="' . base_url() . 'my-msg-inbox.html"><span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has sent you request to join your prayer group</span>.</a>';
            }
        }
        //pr($prayer_group_joining,1);
        ############################# END prayer_group_chat_room_invitation  ################################################
        # SOCIAL HUB SECTION
        ### trade center 
        ########### etrade 

        if ((is_array($etrade_request_recvd) && count($etrade_request_recvd))) {

            foreach ($etrade_request_recvd as $key => $val) {

                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<a href="' . base_url() . 'my-msg-inbox.html"><span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has sent you an etrade request</span>.</a>';
                } else
                    $notification_message .= '<a href="' . base_url() . 'my-msg-inbox.html"><span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has sent you etrade request</span>.</a>';
            }
        }

        if ((is_array($etrade_request_accept) && count($etrade_request_accept))) {

            foreach ($etrade_request_accept as $key => $val) {

                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<a href="' . base_url() . 'my-msg-inbox.html"><span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has accepted your etrade request</span>.</a>';
                } else
                    $notification_message .= '<a href="' . base_url() . 'my-msg-inbox.html"><span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has accepted your etrade request</span>.</a>';
            }
        }

        if ((is_array($etrade_request_declined) && count($etrade_request_declined))) {

            foreach ($etrade_request_declined as $key => $val) {

                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<a href="' . base_url() . 'my-msg-inbox.html"><span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has declined your etrade request</span>.</a>';
                } else
                    $notification_message .= '<a href="' . base_url() . 'my-msg-inbox.html"><span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has declined your etrade request</span>.</a>';
            }
        }


        if ((is_array($etrade_request_canceled_buyer) && count($etrade_request_canceled_buyer))) {

            foreach ($etrade_request_canceled_buyer as $key => $val) {

                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<a href="' . base_url() . 'my-msg-inbox.html"><span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has cancelled his/her etrade request</span>.</a>';
                } else
                    $notification_message .= '<a href="' . base_url() . 'my-msg-inbox.html"><span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has cancelled his/her etrade request</span>.</a>';
            }
        }





        if ((is_array($eswap_request_recvd) && count($eswap_request_recvd))) {

            foreach ($eswap_request_recvd as $key => $val) {

                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<a href="' . base_url() . 'my-msg-inbox.html"><span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has sent you an eswap request</span>.</a>';
                } else
                    $notification_message .= '<a href="' . base_url() . 'my-msg-inbox.html"><span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has sent you eswap request</span>.</a>';
            }
        }

        if ((is_array($eswap_request_accept) && count($eswap_request_accept))) {

            foreach ($eswap_request_accept as $key => $val) {

                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<a href="' . base_url() . 'my-msg-inbox.html"><span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has accepted your eswap request</span>.</a>';
                } else
                    $notification_message .= '<a href="' . base_url() . 'my-msg-inbox.html"><span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has accepted your eswap request</span>.</a>';
            }
        }

        if ((is_array($eswap_request_declined) && count($eswap_request_declined))) {

            foreach ($eswap_request_declined as $key => $val) {

                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<a href="' . base_url() . 'my-msg-inbox.html"><span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has declined your eswap request</span>.</a>';
                } else
                    $notification_message .= '<a href="' . base_url() . 'my-msg-inbox.html"><span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has declined your eswap request</span>.</a>';
            }
        }



        if ((is_array($efreebie_request_recvd) && count($efreebie_request_recvd))) {

            foreach ($efreebie_request_recvd as $key => $val) {

                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<a href="' . base_url() . 'my-msg-inbox.html"><span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has sent you an efreebie request</span>.</a>';
                } else
                    $notification_message .= '<a href="' . base_url() . 'my-msg-inbox.html"><span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has sent you efreebie request</span>.</a>';
            }
        }

        if ((is_array($efreebie_request_accept) && count($efreebie_request_accept))) {

            foreach ($efreebie_request_accept as $key => $val) {

                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<a href="' . base_url() . 'my-msg-inbox.html"><span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has accepted your efreebie request</span>.</a>';
                } else
                    $notification_message .= '<a href="' . base_url() . 'my-msg-inbox.html"><span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has accepted your efreebie request</span>.</a>';
            }
        }

        if ((is_array($efreebie_request_declined) && count($efreebie_request_declined))) {

            foreach ($efreebie_request_declined as $key => $val) {

                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<a href="' . base_url() . 'my-msg-inbox.html"><span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has declined your efreebie request</span>.</a>';
                } else
                    $notification_message .= '<a href="' . base_url() . 'my-msg-inbox.html"><span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has declined your efreebie request</span>.</a>';
            }
        }
        ### etrade
        #### user_chat_room_invitation

        if ((is_array($user_chat_room_invitation) && count($user_chat_room_invitation))) {

            foreach ($user_chat_room_invitation as $key => $val) {

                if ($key >= 1) {
                    $notification_message .= '<br/>' . '<a href="' . base_url() . 'my-msg-inbox.html"><span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has invited you to his/her chat room.</span>.</a>';
                } else
                    $notification_message .= '<a href="' . base_url() . 'my-msg-inbox.html"><span class="blueName">' . get_username_by_id($val['i_requester_id']) . ' has invited you to his/her chat room.</span>.</a>';
            }
        }

        #### emergency_prayer_click

        if ((is_array($emergency_prayer_click) && count($emergency_prayer_click))) {

            foreach ($emergency_prayer_click as $key => $val) {

                if ($key >= 1) {
                    $notification_message .= '<br/>' . 'Administrator has added an emergency prayer request.';
                } else
                    $notification_message .= 'Administrator has added an emergency prayer request.';
            }
        }

        ## UPDATING NOTIFICATION TABLE ####


        if (is_array($notification_array) && count($notification_array)) {

            foreach ($notification_array as $key => $val) {
                $this->user_notifications_model->update($arr, $val['id']);
            }
        }
        //count($notification_array);
        ###################################################



        if (count($prayer_points_arr) != 0 || count($prayer_partner_arr) != 0 || count($netpal_arr) != 0 || count($friend_arr) != 0 || count($message_arr) != 0 || count($netpal_declined_arr) != 0 || count($netpal_accepted_arr) != 0 || count($friend_declined_arr) != 0 || count($friend_accepted_arr) != 0 || count($prayer_partner_declined_arr) != 0 || count($prayer_partner_accepted_arr) != 0 || count($photo_comment) != 0 || count($video_comment) != 0 || count($audio_comment) != 0 || count($event_comment) != 0 || count($event_invitation) != 0 || count($tweet_comment) != 0 || count($ring_comment) != 0 || count($ring_post) != 0 || count($blog_comment) != 0 || count($retweet) != 0 || count($prayer_r_commit) != 0 || count($prayer_group_invitation) != 0 || count($prayer_group_deny_join_request) != 0 || count($prayer_group_accept_join_request) != 0 || count($prayer_group_chat_room_invitation) != 0 || count($prayer_group_joining) != 0 || count($etrade_request_declined) != 0 || count($etrade_request_accept) != 0 || count($etrade_request_recvd) != 0 || count($eswap_request_recvd) != 0 || count($eswap_request_accept) != 0 || count($eswap_request_declined) != 0 || count($efreebie_request_recvd) != 0 || count($efreebie_request_declined) != 0 || count($efreebie_request_accept) != 0 || count($etrade_request_canceled_buyer) != 0 || count($user_chat_room_invitation) != 0 || count($emergency_prayer_click) != 0) {


            echo json_encode(array('result' => 'success',
                'notification_msg' => $notification_message));
            exit;
        } else {
            echo json_encode(array('result' => 'failure'));
            exit;
        }
    }

    ## --------------------------------------------- MEDIA NOTIFICATIONS ---------------------------------------------- ##

    protected function media_notifications_message($i_sender_id, $i_receiver_id, $s_type = 'normal', $media_id) {

        try {

            $this->load->model('data_messages_model');

            if ($s_type == 'video_comment') {

                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);

                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("video_comment");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);

                $url = get_video_title($media_id);


                $body = nl2br(sprintf3($body, array('sender_name' => $user_sender['s_profile_name'], 'url' => $url)));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            } else if ($s_type == 'photo_comment') {
                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);


                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("photo_comment");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
                $url = get_photo_title($media_id);

                $body = nl2br(sprintf3($body, array('sender_name' => $user_sender['s_profile_name'], 'url' => $url)));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            } else if ($s_type == 'audio_comment') {

                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);


                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("audio_comment");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
                $url = get_audio_title($media_id);


                $body = nl2br(sprintf3($body, array('sender_name' => $user_sender['s_profile_name'], 'url' => $url)));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            }

            ### adding reminder notifications too ###
            else if ($s_type == 'todo_reminder_im_mail') {

                $this->load->model('users_model');
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);

                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("todo_reminder_im_mail");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);

                $s_description = get_reminder_todo_text($media_id);
                $start_time = get_reminder_todo_start_time($media_id);
                $end_time = get_reminder_todo_end_time($media_id);


                $subject = sprintf3($subject, array('short_desc' => my_substr($s_description, 30)));

                $body = nl2br(sprintf3($body, array('user_name' => $user_receiver['s_profile_name'], 's_desc' => $s_description, 'start_time' => $start_time, 'end_time' => $end_time)));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            }



            $arr['s_type'] = $s_type;

            return $this->data_messages_model->add_info($arr);
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function show_system_reminder_popup($show_popup = 'false', $popup_arr) {

        $this->load->model('users_model');
        $this->load->model('organizer_note_model');
        $this->load->model('organizer_todo_model');


        if ($show_popup == 'true') {
            $data['reminder_content'] = $popup_arr;
            $view = "logged/organize/loadreminder_popup.phtml";
            $reminder_html = $this->load->view($view, $data, true);
            echo json_encode(array('result' => 'success', 'reminder_html' => $reminder_html));
        } else {

            echo json_encode(array('result' => 'failure'));
        }
    }

    public function show_system_reminder_popup_at_remind_me_time($show_popup = 'false', $popup_arr) {

        $this->load->model('users_model');
        $this->load->model('organizer_todo_model');
        $this->load->model('events_model');

        ## fetching array ###
        ### fetch from reminder table ###
        $i_profile_id = intval(decrypt($this->session->userdata('user_id')));

        $today = strtotime(date("Y-m-d"));
        $tommorow = strtotime('+2 days');

        //echo (( strtotime('11:00:00')- strtotime("00:15:00")));

        $where = 'AND i_status = 1 
				  AND i_reminder_status = 1 
				  AND i_reminder_shown = 1 
				  AND DATE_FORMAT(`dt_start_time`,"%Y-%m-%d") = "' . date('Y-m-d', $today) . '"
				  AND DATE_FORMAT(`t_remind_me_back`,"%H:%i") = "' . date('H:i', time()) . '"
				  ';

        $event_arr = $this->events_model->get_by_user_id($i_profile_id, $where);

        //pr($event_arr);

        $s_where_today_after2days = 'AND i_active = 1  
									 AND i_reminder_status = 1 
									 AND i_reminder_shown = 1 
									 AND `d_date` = "' . date('Y-m-d', $today) . '"
								     AND DATE_FORMAT(`t_remind_me_back`,"%H:%i") = "' . date('H:i', time()) . '"';

        $todo_list_between_today_after2days = $this->organizer_todo_model->get_by_user_id($i_profile_id, $s_where_today_after2days);
        //exit;
        $reminder_array = array();
        $reminder_array = array_merge($todo_list_between_today_after2days, $event_arr);
        //array_merge($reminder_array,$todo_list_between_today_after2days);
        //pr($reminder_array);
        //	pr($todo_list_between_today_after2days,1);
        $popup_array = array();
        if (count($reminder_array)) {

            foreach ($reminder_array as $val) {

                $pop_content_array = array();

                #echo $val['event_type'] .' @@@@' ;

                if ($val['event_type'] == ' [ D ] ') {
                    $pop_content_array['content'] = my_substr($val['s_description'], 200);
                    $pop_content_array['organizer_id'] = $val['id'];
                    $pop_content_array['t_start_time'] = $val['t_start_time'];
                    $pop_content_array['d_start_date'] = $val['d_date'];

                    $due_date = strtotime($val['d_date'] . ' ' . $val['t_start_time']);
                    $today_date = strtotime(date('Y-m-d H:i:s'));

                    $pop_content_array['due_time'] = date_difference_due_time($due_date, $today_date);
                    $pop_content_array['type'] = 'to-do';
                } else {
                    $pop_content_array['content'] = $val['s_title'];
                    $pop_content_array['organizer_id'] = $val['id'];
                    $pop_content_array['t_start_time'] = $val['t_start_time'];
                    $pop_content_array['d_start_date'] = $val['dt_start_time'];


                    $due_date = strtotime($val['dt_start_time'] . ' ' . $val['t_start_time']);
                    $today_date = strtotime(date('Y-m-d H:i:s'));

                    $pop_content_array['due_time'] = date_difference_due_time($due_date, $today_date);
                    $pop_content_array['type'] = 'event';
                }

                //pr($pop_content_array,1);
                array_push($popup_array, $pop_content_array);
            }
        }
        //pr($popup_array,1);

        if (count($reminder_array)) {

            foreach ($reminder_array as $val) {
                ## update  i_reminder_shown_at_remind_me_time in db ##
                if ($val['event_type'] == ' [ D ] ') {
                    $to_do_arr['i_reminder_shown'] = 2;
                    $this->organizer_todo_model->update($to_do_arr, $val['id']);
                } else {
                    $to_do_arr['i_reminder_shown'] = 2;
                    $this->events_model->update($to_do_arr, $val['id']);
                }
            }
        }

        if (count($popup_array)) {

            $data['reminder_content'] = $popup_array;
            $view = "logged/organize/loadreminder_popup.phtml";
            $reminder_html = $this->load->view($view, $data, true);

            echo json_encode(array('result' => 'success', 'reminder_html' => $reminder_html, 'current_time' => date('H:i', time())));
        } else {

            echo json_encode(array('result' => 'failure', 'current_time' => date('H:i', time()), 'is_count' => count($popup_array), 'query' => $reminder_array));
        }
    }

    ## SOCIAL HUB  METHODS: ########################

    protected function get_user_all_tweets($i_profile_id, $s_where = '', $start_limit = '', $end_limit = '') {
        $this->load->model('my_tweet_model');

        $tweet_arr = $this->my_tweet_model->get_all_tweets_by_user_id($i_profile_id, $s_where, 0, 3);
        $this->data['right_bar']['tweet_arr'] = $tweet_arr;
    }

    ## SOCIAL HUB MESSAGES ###

    protected function social_notifications_message($i_sender_id, $i_receiver_id, $s_type = 'normal', $media_id, $prefix = '') {

        try {

            $this->load->model('data_messages_model');
            $this->load->model('events_model');

            if ($s_type == 'event_invitations_request') {

                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);
                // $profile_url = get_profile_url($i_sender_id,$user_sender['s_profile_name']);

                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("event_invitations_request");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
                $subject = sprintf3($subject, array('sender_name' => $user_sender['s_profile_name']));

                $info = $this->events_model->get_by_id($media_id);
                $event_name = $info['s_title'];


                $url = "<a href='" . get_events_detail_url($media_id) . "' target='_blank'>" . $event_name . "</a>";

                $body = nl2br(sprintf3($body, array('sender_name' => $user_sender['s_profile_name'], 'url' => $url, 'receiver_name' => $user_receiver['s_profile_name'])));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            } else if ($s_type == 'event_comment') {



                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);
                // $profile_url = get_profile_url($i_sender_id,$user_sender['s_profile_name']);

                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("event_comment");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
                $info = $this->events_model->get_by_id($media_id);
                $event_name = $info['s_title'];

                $url = "<a href='" . get_events_detail_url($media_id) . "' target='_blank'>" . $event_name . "</a>";


                $body = nl2br(sprintf3($body, array('sender_name' => $user_sender['s_profile_name'], 'url' => $url)));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            } else if ($s_type == 'ring_join_request') {

                $this->load->model('users_model');
                $this->load->model('my_ring_model');
                #echo $i_sender_id.'####'.$i_receiver_id;
                $user_sender = $this->users_model->get_user_email_by_id($i_sender_id);
                $user_receiver = $this->users_model->get_user_email_by_id($i_receiver_id);
                #pr($user_sender);pr($user_receiver);
                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("ring_join_request");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
                $info = $this->my_ring_model->get_by_id($media_id);
                $ring_name = $info['s_ring_name'];

                $url = '<a href="' . base_url() . 'accept_invitation/' . encrypt($i_receiver_id) . '" target="_blank" style=" width:108px; height:28px; background-color:#62C3BC; color:#fff; display:block; text-align:center; text-decoration:none; padding-top:8px;">Join</a>';


                $subject = sprintf3($subject, array('sender_name' => $user_sender['s_profile_name']));
                $body = nl2br(sprintf3($body, array('receiver_name' => $user_receiver['s_profile_name'],
                    'sender_name' => $user_sender['s_profile_name'],
                    'ring_name' => $ring_name)));




                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            } else if ($s_type == 'ring_join_request_from_normal_user') {

                $this->load->model('users_model');
                $this->load->model('my_ring_model');
                #echo $i_sender_id.'####'.$i_receiver_id;
                $user_sender = $this->users_model->get_user_email_by_id($i_sender_id);
                $user_receiver = $this->users_model->get_user_email_by_id($i_receiver_id);
                #pr($user_sender);pr($user_receiver);
                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("ring_join_request_from_normal_user");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
                $info = $this->my_ring_model->get_by_id($media_id);
                $ring_name = $info['s_ring_name'];

                $url = '<a href="' . base_url() . 'accept_invitation/' . encrypt($i_receiver_id) . '" target="_blank" style=" width:108px; height:28px; background-color:#62C3BC; color:#fff; display:block; text-align:center; text-decoration:none; padding-top:8px;">Join</a>';


                $subject = sprintf3($subject, array('sender_name' => $user_sender['s_profile_name']));
                $body = nl2br(sprintf3($body, array('receiver_name' => $user_receiver['s_profile_name'],
                    'sender_name' => $user_sender['s_profile_name'],
                    'ring_name' => $ring_name)));




                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            } else if ($s_type == 'ring_leave') {

                $this->load->model('users_model');
                $this->load->model('my_ring_model');
                #echo $i_sender_id.'####'.$i_receiver_id;
                $user_sender = $this->users_model->get_user_email_by_id($i_sender_id);
                $user_receiver = $this->users_model->get_user_email_by_id($i_receiver_id);
                #pr($user_sender);pr($user_receiver);
                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("ring_leave");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
                $info = $this->my_ring_model->get_by_id($media_id);
                $ring_name = $info['s_ring_name'];

                $subject = sprintf3($subject, array('sender_name' => $user_sender['s_profile_name'],
                    'ring_name' => $ring_name));
                $body = nl2br(sprintf3($body, array('receiver_name' => $user_receiver['s_profile_name'],
                    'sender_name' => $user_sender['s_profile_name'],
                    'ring_name' => $ring_name)));




                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            } else if ($s_type == 'ring_accept_join_request') {

                $this->load->model('users_model');
                $this->load->model('my_ring_model');
                #echo $i_sender_id.'####'.$i_receiver_id;
                $user_sender = $this->users_model->get_user_email_by_id($i_sender_id);
                $user_receiver = $this->users_model->get_user_email_by_id($i_receiver_id);
                #pr($user_sender);pr($user_receiver);
                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("ring_accept_join_request");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
                $info = $this->my_ring_model->get_by_id($media_id);
                $ring_name = $info['s_ring_name'];

                $subject = sprintf3($subject, array('sender_name' => $user_sender['s_profile_name'],
                    'ring_name' => $ring_name));
                $body = nl2br(sprintf3($body, array('receiver_name' => $user_receiver['s_profile_name'],
                    'sender_name' => $user_sender['s_profile_name'],
                    'ring_name' => $ring_name)));




                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            } else if ($s_type == 'ring_deny_join_request') {

                $this->load->model('users_model');
                $this->load->model('my_ring_model');
                #echo $i_sender_id.'####'.$i_receiver_id;
                $user_sender = $this->users_model->get_user_email_by_id($i_sender_id);
                $user_receiver = $this->users_model->get_user_email_by_id($i_receiver_id);
                #pr($user_sender);pr($user_receiver);
                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("ring_deny_join_request");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
                $info = $this->my_ring_model->get_by_id($media_id);
                $ring_name = $info['s_ring_name'];

                $subject = sprintf3($subject, array('sender_name' => $user_sender['s_profile_name'],
                    'ring_name' => $ring_name));
                $body = nl2br(sprintf3($body, array('receiver_name' => $user_receiver['s_profile_name'],
                    'sender_name' => $user_sender['s_profile_name'],
                    'ring_name' => $ring_name)));




                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            } else if ($s_type == 'ring_comments_notification') {

                $this->load->model('users_model');
                $this->load->model('my_ring_model');
                #echo $i_sender_id.'####'.$i_receiver_id;
                $user_sender = $this->users_model->get_user_email_by_id($i_sender_id);
                $user_receiver = $this->users_model->get_user_email_by_id($i_receiver_id);
                #pr($user_sender);pr($user_receiver);
                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("ring_comments_notification");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);


                $ring_info_arr = $this->my_ring_model->get_ring_details_by_ring_post_id($media_id);

                //pr($ring_info_arr,1);
                //$info = $this->my_ring_model->get_by_id($media_id);
                $ring_name = $ring_info_arr['s_ring_name'];

                $url = "<a href='" . base_url() . 'rings/' . $ring_info_arr['id'] . '/ring-home.html' . "' target='_blank'>" . $ring_name . "</a>";


                $subject = sprintf3($subject, array('sender_name' => $user_sender['s_profile_name']));
                $body = nl2br(sprintf3($body, array('receiver_name' => $user_receiver['s_profile_name'],
                    'sender_name' => $user_sender['s_profile_name'],
                    'url' => $url,
                    'post_name' => $ring_info_arr['s_post_title'])));




                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            } else if ($s_type == 'ring_post_notification') {

                $this->load->model('users_model');
                $this->load->model('my_ring_model');
                #echo $i_sender_id.'####'.$i_receiver_id;
                $user_sender = $this->users_model->get_user_email_by_id($i_sender_id);
                $user_receiver = $this->users_model->get_user_email_by_id($i_receiver_id);
                #pr($user_sender);pr($user_receiver);
                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("ring_post_notification");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);


                $ring_info_arr = $this->my_ring_model->get_by_id($media_id);

                //pr($ring_info_arr,1);
                //$info = $this->my_ring_model->get_by_id($media_id);
                $ring_name = $ring_info_arr['s_ring_name'];

                $url = "<a href='" . base_url() . 'rings/' . $ring_info_arr['id'] . '/ring-home.html' . "' target='_blank'>" . $ring_name . "</a>";


                $subject = sprintf3($subject, array('sender_name' => $user_sender['s_profile_name']));
                $body = nl2br(sprintf3($body, array('receiver_name' => $user_receiver['s_profile_name'],
                    'sender_name' => $user_sender['s_profile_name'],
                    'url' => $url)));
                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            } else if ($s_type == 'ring_delete') {

                $this->load->model('users_model');
                $this->load->model('my_ring_model');
                #echo $i_sender_id.'####'.$i_receiver_id;
                $user_sender = $this->users_model->get_user_email_by_id($i_sender_id);
                $user_receiver = $this->users_model->get_user_email_by_id($i_receiver_id);
                #pr($user_sender);pr($user_receiver);
                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("ring_delete");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
                $info = $this->my_ring_model->get_by_id($media_id);
                $ring_name = $info['s_ring_name'];

                $subject = sprintf3($subject, array('sender_name' => $user_sender['s_profile_name'],
                    'ring_name' => $ring_name));
                $body = nl2br(sprintf3($body, array('receiver_name' => $user_receiver['s_profile_name'],
                    'sender_name' => $user_sender['s_profile_name'],
                    'ring_name' => $ring_name)));
                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            } else if ($s_type == 'prayer_group_delete') {
                $this->load->model('users_model');
                $user_sender = $this->users_model->get_user_email_by_id($i_sender_id);
                $user_receiver = $this->users_model->get_user_email_by_id($i_receiver_id);
                $body = ' ';
                $subject = ' ';
                $this->load->model('mail_contents_model');
                $mail_info = $this->mail_contents_model->get_by_name("prayer_group_delete");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
                //$info = $this->prayer_group_model->get_group_name_by_id($media_id);
                $group_name = $this->prayer_group_model->get_group_name_by_id($media_id);

                $subject = sprintf3($subject, array('sender_name' => $user_sender['s_profile_name'],
                    'prayer_grp_name' => $group_name));
                $body = nl2br(sprintf3($body, array('receiver_name' => $user_receiver['s_profile_name'],
                    'sender_name' => $user_sender['s_profile_name'],
                    'prayer_grp_name' => $group_name)));
                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            } else if ($s_type == 'ring_member_delete') {

                $this->load->model('users_model');
                $this->load->model('my_ring_model');
                #echo $i_sender_id.'####'.$i_receiver_id;
                $user_sender = $this->users_model->get_user_email_by_id($i_sender_id);
                $user_receiver = $this->users_model->get_user_email_by_id($i_receiver_id);
                #pr($user_sender);pr($user_receiver);
                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("ring_member_delete");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
                $info = $this->my_ring_model->get_by_id($media_id);
                $ring_name = $info['s_ring_name'];

                $subject = sprintf3($subject, array('sender_name' => $user_sender['s_profile_name'],
                    'ring_name' => $ring_name));
                $body = nl2br(sprintf3($body, array('receiver_name' => $user_receiver['s_profile_name'],
                    'sender_name' => $user_sender['s_profile_name'],
                    'ring_name' => $ring_name)));




                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            } else if ($s_type == 'blog_comment') {



                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);
                // $profile_url = get_profile_url($i_sender_id,$user_sender['s_profile_name']);

                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("blog_comment");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);


                $info = $this->my_blog_model->get_blog_details_by_article_id($media_id);
                $blog_name = $info['s_title'];
                $article_name = $info['s_post_title'];
                //pr($info,1);

                $url = "<a href='" . base_url() . 'blog/' . $info['id'] . '/detail.html' . "' target='_blank'>" . $blog_name . "</a>";

                $subject = sprintf3($subject, array('sender_name' => $user_sender['s_profile_name']));
                $body = nl2br(sprintf3($body, array('receiver_name' => $user_receiver['s_profile_name'],
                    'sender_name' => $user_sender['s_profile_name'],
                    'url' => $url,
                    'article_name' => $article_name)));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            }
            ## twit section 
            else if ($s_type == 'tweet_comment') {



                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);
                // $profile_url = get_profile_url($i_sender_id,$user_sender['s_profile_name']);

                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("tweet_comment");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);


                $subject = sprintf3($subject, array('sender_name' => $user_sender['s_profile_name']));
                $body = nl2br(sprintf3($body, array('receiver_name' => $user_receiver['s_profile_name'],
                    'sender_name' => $user_sender['s_profile_name']
                )));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            } else if ($s_type == 'retweet') {



                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);
                // $profile_url = get_profile_url($i_sender_id,$user_sender['s_profile_name']);

                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("retweet");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);


                $tweet_id = getTwitterUsernameById($i_sender_id);

                $url = '<a href = "' . getTwitterProfileLink($tweet_id) . '" target="_blank">' . $tweet_id . '</a>';

                $subject = sprintf3($subject, array('sender_name' => $user_sender['s_profile_name']));
                $body = nl2br(sprintf3($body, array('receiver_name' => $user_receiver['s_profile_name'],
                    'sender_name' => $user_sender['s_profile_name'],
                    'url' => $url)));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            }
            ## prayer commits im
            else if ($s_type == 'prayer_r_commit') {



                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);
                // $profile_url = get_profile_url($i_sender_id,$user_sender['s_profile_name']);

                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("prayer_r_commit");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);




                $subject = sprintf3($subject, array('sender_name' => $user_sender['s_profile_name']));
                $body = nl2br(sprintf3($body, array('receiver_name' => $user_receiver['s_profile_name'],
                    'sender_name' => $user_sender['s_profile_name'],
                    'url' => $url,
                    'article_name' => $article_name)));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            }
            /// prayer group inviation
            else if ($s_type == 'prayer_group_invitation') {



                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);

                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("prayer_group_invitation");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);

                $this->load->model('prayer_group_model');
                $info = $this->prayer_group_model->get_group_detail_by_id($media_id);
                $group_name = '<strong>' . $info['s_group_name'] . '</strong>';

                //$url = '<a href="'.get_group_url($media_id).'">'.$group_name.'</a>';
                $url = $group_name;

                $subject = sprintf3($subject, array('sender_name' => $user_sender['s_profile_name']));
                $body = nl2br(sprintf3($body, array('receiver_name' => $user_receiver['s_profile_name'],
                    'sender_name' => $user_sender['s_profile_name'],
                    'url' => $url)));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            }
            // prayer group invitation accept
            else if ($s_type == 'prayer_group_accept_join_request') {



                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);

                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("prayer_group_accept_join_request");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);


                $subject = sprintf3($subject, array('sender_name' => $user_sender['s_profile_name']));
                $body = nl2br(sprintf3($body, array('receiver_name' => $user_receiver['s_profile_name'],
                    'sender_name' => $user_sender['s_profile_name'])));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            }
            // prayer group invitation rejected
            else if ($s_type == 'prayer_group_deny_join_request') {



                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);

                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("prayer_group_deny_join_request");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);


                $subject = sprintf3($subject, array('sender_name' => $user_sender['s_profile_name']));
                $body = nl2br(sprintf3($body, array('receiver_name' => $user_receiver['s_profile_name'],
                    'sender_name' => $user_sender['s_profile_name'])));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            }
            // prayer group chat room invitation
            else if ($s_type == 'prayer_group_chat_room_invitation') {



                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);

                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("prayer_group_chat_room_invitation");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);

                $s_group_name = get_group_name_by_ChatRoom_id($media_id);
                $group_name = '<strong>' . $s_group_name . '</strong>';
                $url = $group_name;

                ## chat room details
                $chat_info = get_chat_room_details($media_id);

                $xml_string = '<?xml version="1.0" encoding="UTF-8"?>' . $chat_info['xml_data'];
                $time_info = array();
                $time_info = get_StartTime_EndTime($xml_string);


                $subject = sprintf3($subject, array('sender_name' => $user_sender['s_profile_name']));
                $body = nl2br(sprintf3($body, array('receiver_name' => $user_receiver['s_profile_name'],
                    'sender_name' => $user_sender['s_profile_name'],
                    'url' => $url,
                    'start_time' => getShortDateWithTime($time_info['strt_time'], 9),
                    'end_time' => getShortDateWithTime($time_info['end_time'], 9))));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            }
            ##prayer_post_modified
            else if ($s_type == 'prayer_post_modified') {



                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);

                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("prayer_post_modified");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);

                $this->load->model('prayer_group_model');
                $info = $this->prayer_group_model->get_group_detail_by_id($media_id);
                $group_name = '<strong>' . $info['s_group_name'] . '</strong>';

                //$url = '<a href="'.get_group_url($media_id).'">'.$group_name.'</a>';
                $url = $group_name;

                $subject = sprintf3($subject, array('sender_name' => $user_sender['s_profile_name']));
                $body = nl2br(sprintf3($body, array('receiver_name' => $user_receiver['s_profile_name'],
                    'sender_name' => $user_sender['s_profile_name'],
                    'url' => $group_name)));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            }
            #prayer_group_joining
            else if ($s_type == 'prayer_group_joining') {



                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);

                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("prayer_group_joining");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);

                $this->load->model('prayer_group_model');
                $info = $this->prayer_group_model->get_group_detail_by_id($media_id);
                $group_name = '<strong>' . $info['s_group_name'] . '</strong>';

                //$url = '<a href="'.get_group_url($media_id).'">'.$group_name.'</a>';
                $url = $group_name;

                $subject = sprintf3($subject, array('sender_name' => $user_sender['s_profile_name']));
                $body = nl2br(sprintf3($body, array('receiver_name' => $user_receiver['s_profile_name'],
                    'sender_name' => $user_sender['s_profile_name'],
                    'url' => $group_name)));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            }
            #
            else if ($s_type == 'prayer_group_join_request_accepted_by_owner') {



                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);

                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("prayer_group_join_request_accepted_by_owner");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);

                $this->load->model('prayer_group_model');
                $info = $this->prayer_group_model->get_group_detail_by_id($media_id);
                $group_name = '<strong>' . $info['s_group_name'] . '</strong>';

                //$url = '<a href="'.get_group_url($media_id).'">'.$group_name.'</a>';
                $url = $group_name;

                $subject = sprintf3($subject, array('sender_name' => $user_sender['s_profile_name']));
                $body = nl2br(sprintf3($body, array('receiver_name' => $user_receiver['s_profile_name'],
                    'sender_name' => $user_sender['s_profile_name'],
                    'url' => $group_name)));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            }

            ########### etrade messaging ###################
            else if ($s_type == $prefix . '_send_request') {

                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);

                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name($prefix . "_send_request");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);

                $model_name = 'e_' . substr($prefix, 1) . '_model';
                $this->load->model($model_name);
                $detail_arr = $this->$model_name->get_by_id($media_id);

                $s_url = base_url() . $prefix . '/' . $detail_arr['id'] . '/detail.html';

                $sname = '"<a href="' . $s_url . '">' . $detail_arr['s_name'] . '</a>"';

                $subject = sprintf3($subject, array('sender_name' => $user_sender['s_profile_name']));

                $body = nl2br(sprintf3($body, array('usernaname' => $user_receiver["s_profile_name"],
                    'sender_name' => $user_sender["s_profile_name"],
                    "senderemail" => $user_sender['s_email'],
                    "senderphone" => $user_sender['s_phone'],
                    "s_url" => $sname
                )));


                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            }
            ########### etrade messaging ###################
            else if ($s_type == $prefix . '_accept_join_request') {

                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);

                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name($prefix . "_accept_join_request");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);

                $model_name = 'e_' . substr($prefix, 1) . '_model';
                $this->load->model($model_name);
                $detail_arr = $this->$model_name->get_by_id($media_id);

                $s_url = base_url() . $prefix . '/' . $detail_arr['id'] . '/detail.html';

                $sname = '"<a href="' . $s_url . '">' . $detail_arr['s_name'] . '</a>"';

                $subject = sprintf3($subject, array('sender_name' => $user_sender['s_profile_name']));
                $body = nl2br(sprintf3($body, array('usernaname' => $user_receiver["s_profile_name"],
                    'sender_name' => $user_sender["s_profile_name"],
                    "senderemail" => $user_sender['s_email'],
                    "senderphone" => $user_sender['s_phone'],
                    "s_url" => $sname
                )));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            } else if ($s_type == $prefix . '_decline_join_request') {

                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);

                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name($prefix . "_decline_join_request");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);

                $model_name = 'e_' . substr($prefix, 1) . '_model';
                $this->load->model($model_name);
                $detail_arr = $this->$model_name->get_by_id($media_id);

                $s_url = base_url() . $prefix . '/' . $detail_arr['id'] . '/detail.html';

                $sname = '"<a href="' . $s_url . '">' . $detail_arr['s_name'] . '</a>"';


                $subject = sprintf3($subject, array('sender_name' => $user_sender['s_profile_name']));
                $body = nl2br(sprintf3($body, array('usernaname' => $user_receiver["s_profile_name"],
                    'sender_name' => $user_sender["s_profile_name"],
                    "senderemail" => $user_sender['s_email'],
                    "senderphone" => $user_sender['s_phone'],
                    "s_url" => $sname
                )));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            } else if ($s_type == 'etrade_request_shipped') {

                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);

                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("etrade_request_shipped");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);

                $model_name = 'e_trade_model';
                $this->load->model($model_name);
                $detail_arr = $this->$model_name->get_by_id($media_id);

                $s_url = base_url() . 'etrade/' . $detail_arr['id'] . '/detail.html';

                $sname = '"<a href="' . $s_url . '">' . $detail_arr['s_name'] . '</a>"';


                $subject = sprintf3($subject, array('sender_name' => $user_sender['s_profile_name']));
                $body = nl2br(sprintf3($body, array('usernaname' => $user_receiver["s_profile_name"],
                    'sender_name' => $user_sender["s_profile_name"],
                    "senderemail" => $user_sender['s_email'],
                    "senderphone" => $user_sender['s_phone'],
                    "s_url" => $sname
                )));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            } else if ($s_type == 'etrade_request_canceled_buyer') {

                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);

                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("etrade_request_canceled_buyer");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);

                $model_name = 'e_trade_model';
                $this->load->model($model_name);
                $detail_arr = $this->$model_name->get_by_id($media_id);

                $s_url = base_url() . 'etrade/' . $detail_arr['id'] . '/detail.html';

                $sname = '"<a href="' . $s_url . '">' . $detail_arr['s_name'] . '</a>"';


                $subject = sprintf3($subject, array('sender_name' => $user_sender['s_profile_name']));
                $body = nl2br(sprintf3($body, array('usernaname' => $user_receiver["s_profile_name"],
                    'sender_name' => $user_sender["s_profile_name"],
                    "senderemail" => $user_sender['s_email'],
                    "senderphone" => $user_sender['s_phone'],
                    "s_url" => $sname
                )));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            }
            ###user_chat_room_invitation
            else if ($s_type == 'user_chat_room_invitation') {

                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);

                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("user_chat_room_invitation");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);

                $s_url = "'" . base_url() . 'client/123flashchat.html?init_room=' . $media_id . '&init_user=' . get_Chat_username_by_id($i_receiver_id) . "&init_password=" . getCredentials($i_receiver_id) . "'";
                $prop = "'" . "bReplace= 0 resizable=0, location=1, width=630, height=450" . "'";
                $blnk = "'" . "'";

                $sname = '<a  href="javascript:void(0);" onclick="window.open(' . $s_url . ',' . $blnk . ', ' . $prop . ');" target="">Join this Room</a>';


                $subject = sprintf3($subject, array('sender_name' => $user_sender['s_profile_name']));
                $body = nl2br(sprintf3($body, array('receiver_name' => $user_receiver["s_profile_name"],
                    'sender_name' => $user_sender["s_profile_name"],
                    "url" => $sname
                )));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            }

else if ($s_type == 'event_rsvp_received') {



                $this->load->model('users_model');
                $user_sender = $this->users_model->fetch_this($i_sender_id);
                $user_receiver = $this->users_model->fetch_this($i_receiver_id);
                // $profile_url = get_profile_url($i_sender_id,$user_sender['s_profile_name']);

                $body = ' ';
                $subject = ' ';

                $this->load->model('mail_contents_model');

                $mail_info = $this->mail_contents_model->get_by_name("event_rsvp_received");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
                $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
                $info = $this->events_model->get_by_id($media_id);
                $event_name = $info['s_title'];

                $url = "<a href='" . get_events_detail_url($media_id) . "' target='_blank'>" . $event_name . "</a>";

				 $subject = nl2br(sprintf3($subject, array('sender_name' => $user_sender['s_profile_name'])));
                $body = nl2br(sprintf3($body, array('receiver_name'=>get_username_by_id($i_receiver_id),'sender_name' => $user_sender['s_profile_name'], 'event_name' => $url)));

                $arr['i_sender_id'] = intval($i_sender_id);
                $arr['i_receiver_id'] = intval($i_receiver_id);
                $arr['s_subject'] = $subject;
                $arr['s_message'] = $body;
                $arr['i_referred_media_id'] = $media_id;
            }
	
		
            $arr['s_type'] = $s_type;
			$this->load->model('user_alert_model');
			 $this->load->helper('my_utility_helper');
			$email_opt = $this->user_alert_model->check_option_email_user_id($arr['i_receiver_id']);
						if($email_opt['e_message_received'] == 'Y'){
						
						$mail_arr['i_requester_id'] =get_username_by_id( $arr['i_sender_id']);
						$mail_arr['i_accepter_id'] =  get_username_by_id($arr['i_receiver_id']);
						$mail_arr['s_type'] = 'e_message_received';
						$mail_id=get_useremail_by_id($arr['i_receiver_id']);
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

					$this->email->subject('You have received a new message!');
					$this->email->message("$body");

					$this->email->send();
					}
            return $this->data_messages_model->add_info($arr); //exit;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function salavtion_popup_content() {
        $this->load->model('salvation_model');
        $this->data['salvation_prayer']['result'] = $this->salvation_model->get_by_id(1);
        $this->data['salvation_prayer']['photo_arr'] = $this->salvation_model->get_photo_by_salvation_id(1);
        //pr($data['salvation_prayer']);
    }

    public function getImList_Ajax($page = 0, $type = 'all', $s_name = '') {

        $this->load->model('prayer_group_model');
        $this->load->model('contacts_model');
        $this->load->model('my_prayer_partner_model');

        $logged_user_id = intval(decrypt($this->session->userdata('user_id')));

        $this->db->where('i_user_id', $logged_user_id);
        $arr_qry = $this->db->get('users_online');
        $ret_ = $arr_qry->row_array();

        //pr($ret_);

        $query_type = '';
        $exclude_id_csv = '';
        $s_query_type = '';


        $exclude_id_csv .= $logged_user_id;

        if ($s_name != '') {
            $name = " AND CONCAT(u.s_first_name,' ',u.s_last_name) LIKE '%" . $s_name . "%' ";
        }

        //$rltn_arr = CheckUserRelation($logged_user_id);

        if ($type == 'all') {
            $query_type .= "(SELECT 
										  u.id post_owner_user_id, 
										  u.s_email,
										  u.i_country_id,
										  u.s_last_name,
										  u.s_first_name ,
										  u.s_profile_photo,
										  'Friend'  as  'relationship',
										  o.s_status,
										  o.i_isfriend,
										  o.i_isnetpal,
										  o.i_isprayerpartner
											
											  FROM {$this->db->USER_CONTACTS} c ,{$this->db->USERS} u 
											  LEFT JOIN cg_users_online o on o.i_user_id = u.id
											  WHERE u.i_status=1 AND c.s_status='accepted'
											  AND ((c.i_requester_id = " . $logged_user_id . " AND u.id=c.i_accepter_id) 
												  OR (c.i_accepter_id=" . $logged_user_id . " AND u.id=c.i_requester_id))
											  AND u.id NOT IN (" . $exclude_id_csv . ")
											  AND o.s_status = 1 AND o.i_isfriend =1
											  {$name}
											  GROUP BY u.id
											  ORDER BY u.s_first_name ASC)
								  ";
            $contacts = $this->contacts_model->fetch_multi_online_friends($WHERE, null, null, $ORDER_BY);

            $exclude_id_arr = $this->contacts_model->get_friendsId_by_user_id($logged_user_id);

            if (count($exclude_id_arr)) {
                $exclude_id_csv .= ', ';
                $exclude_id_csv .= implode(', ', $exclude_id_arr);
            }

            $query_type .= "UNION
										(SELECT   
										   
										   u.id post_owner_user_id, 
										   u.s_email,
										   u.i_country_id,
										   u.s_last_name,
										   u.s_first_name ,
										   u.s_profile_photo,
										  'Prayer Partner' as 'relationship',
										  o.s_status,
										  o.i_isfriend,
										  o.i_isnetpal,
										  o.i_isprayerpartner
										   
												   FROM {$this->db->USER_PRAYER_PARTNER} p ,{$this->db->USERS} u
												   LEFT JOIN cg_users_online o on o.i_user_id = u.id
												   WHERE u.i_status=1 AND p.s_status='accepted'
												   AND ((p.i_requester_id = " . $logged_user_id . " AND u.id=p.i_accepter_id) 
														OR (p.i_accepter_id=" . $logged_user_id . " AND u.id=p.i_requester_id))
												   AND u.id NOT IN (" . $exclude_id_csv . ")
												   AND o.s_status = 1
												   AND o.i_isprayerpartner =1
												   {$name}
												   GROUP BY u.id
												   ORDER BY u.id ASC)";

            $exclude_id_PP_arr = $this->my_prayer_partner_model->get_prayerPartnerId_by_user_id($logged_user_id);
            if (count($exclude_id_PP_arr)) {
                $exclude_id_csv .= ', ';
                $exclude_id_csv .= implode(', ', $exclude_id_PP_arr);
            }

            $query_type .= "UNION
										   (SELECT  
											 
											 u.id post_owner_user_id, 
											 u.s_email,
											 u.i_country_id,
											 u.s_last_name,
											 u.s_first_name ,
											 u.s_profile_photo,
											 'Netpal' as 'relationship',
											 o.s_status,
										  	 o.i_isfriend,
										     o.i_isnetpal,
										     o.i_isprayerpartner
										    
											 FROM {$this->db->NETPAL} n ,{$this->db->USERS} u 
											 LEFT JOIN cg_users_online o on o.i_user_id = u.id
											 WHERE u.i_status=1 AND n.s_status='accepted'
											 AND ((n.i_requester_id = " . $logged_user_id . " AND u.id=n.i_accepter_id) 
												  OR (n.i_accepter_id=" . $logged_user_id . " AND u.id=n.i_requester_id))
											 AND u.id NOT IN (" . $exclude_id_csv . ")
											 AND o.s_status = 1
											 AND o.i_isnetpal =1
											 {$name}
											 GROUP BY u.id
											 ORDER BY u.id ASC )";
        } else if ($type == 'friend') {

            $query_type = "(SELECT 
										
										u.id post_owner_user_id, 
										u.s_email,
										u.i_country_id,
										u.s_last_name,
										u.s_first_name ,
										u.s_profile_photo,
										'Friend'  as 'relationship',
										o.s_status,
										o.i_isfriend,
										o.i_isnetpal,
										o.i_isprayerpartner
										  
										FROM {$this->db->USER_CONTACTS} c ,{$this->db->USERS} u 
										LEFT JOIN cg_users_online o on o.i_user_id = u.id
										WHERE u.i_status=1 AND c.s_status='accepted'
										AND ((c.i_requester_id = " . $logged_user_id . " AND u.id=c.i_accepter_id) 
											OR (c.i_accepter_id=" . $logged_user_id . " AND u.id=c.i_requester_id))
										AND u.id NOT IN (" . $exclude_id_csv . ")
										AND o.s_status = 1
										AND o.i_isfriend =1
										{$name}
										GROUP BY u.id
										ORDER BY u.s_first_name ASC) ";
        } else if ($type == 'netpal') {

            $query_type = "(SELECT  
									   
									   u.id post_owner_user_id, 
									   u.i_country_id,
									   u.s_email,
									   u.s_last_name,
									   u.s_first_name ,
									   u.s_profile_photo,
									   'Netpal' as 'relationship' ,
									   o.s_status,
									   o.i_isfriend,
									   o.i_isnetpal,
									   o.i_isprayerpartner
										   
									   FROM {$this->db->NETPAL} n ,{$this->db->USERS} u 
									   LEFT JOIN cg_users_online o on o.i_user_id = u.id
									   WHERE u.i_status=1 AND n.s_status='accepted'
									   AND ((n.i_requester_id = " . $logged_user_id . " AND u.id=n.i_accepter_id) 
											OR (n.i_accepter_id=" . $logged_user_id . " AND u.id=n.i_requester_id))
									   AND u.id NOT IN (" . $exclude_id_csv . ")
									   AND o.s_status = 1
									    AND o.i_isnetpal =1
									   {$name}
									   GROUP BY u.id
									   ORDER BY u.id ASC )";
        } else if ($type == 'pp') {

            $query_type = "(SELECT   
										  
										   u.id post_owner_user_id, 
										   u.s_email,
										   u.i_country_id,
										   u.s_last_name,
										   u.s_first_name ,
										   u.s_profile_photo,
										   'Prayer Partner' as 'relationship' ,
										   o.s_status,
										   o.i_isfriend,
										   o.i_isnetpal,
										   o.i_isprayerpartner
										  
										   FROM {$this->db->USER_PRAYER_PARTNER} p ,{$this->db->USERS} u
										   LEFT JOIN cg_users_online o on o.i_user_id = u.id
										   WHERE u.i_status=1 AND p.s_status='accepted'
										   AND ((p.i_requester_id = " . $logged_user_id . " AND u.id=p.i_accepter_id) 
												OR (p.i_accepter_id=" . $logged_user_id . " AND u.id=p.i_requester_id))
										   AND u.id NOT IN (" . $exclude_id_csv . ")
										   AND o.s_status = 1
										   AND o.i_isprayerpartner = 1
										   {$name}
										   GROUP BY u.id
										   ORDER BY u.id ASC)";
        }


        $data['im_arr'] = $this->prayer_group_model->get_frnd_list($query_type, '', '', '');
        //
        array_sort_by_column($data['im_arr'], 's_first_name');
        //pr($data['im_arr'],1);
        if (count($data['im_arr'])) {
            $VIEW = "logged/wall/im_list_ajax.phtml";
            $html = $this->load->view($VIEW, $data, true);
        } else {
            $html = '<li style="color:#000000;">No results.</li>';
        }

        echo json_encode(array('success' => true, 'im_html' => $html));
    }

   function getLatestAdvertisement() {
       
          $type = $this->uri->segment(1);
         
          if($type =="my-tweets" || $type == 'my-followings' || $type == 'my-followers' || $type == 'my-favourite-tweets' || $type =="trends" || $type=='tweets' ){  $type = 'tweets';}
          if($type == "blogs" || $type =="my-blog" || $type=='blog' || $type == 'edit-my-blog' || $type == 'search-blogs' || $type == 'most-popular-blogs'){ $type = 'blogs';}
          if($type == "my-ring" || $type == 'rings' || $type == 'search-ring' || $type == 'create-my-ring' || $type =='approve-join-request' ){ $type ='ring';}
          if($type == "all-events" || $type == 'event-detail' || $type == 'find-events' || $type == 'my-events' || $type == 'create-event' || $type == 'event-invitations-received' || $type =='archived-events'){ $type = 'events';}
          
          if($type == 'my-chat-rooms' || $type == 'browse-chat-room' || $type == 'private-chat-room' || $type == 'room' || $type == 'prayer-chat-room' || $type == 'ring-chat-room' || $type == 'create-chat-room')
              {
               $type = 'chat-room';
              
              }
           if($type == 'my-prayer-partners' || $type == 'prayer-partner-request' || $type == 'search-invite-prayer-partner')
           {
             $type = 'prayer-partner';  
           }
          if($type == 'bible-quiz'){
              $type = 'quiz';
          }
          if($type == 'find-church' || $type == 'register-your-church'){ $type = 'Church';}
          if( $type == 'my-profile'){ $type = 'my-profile';}
          if($type == 'my-friends' || $type == 'friend-request' || $type == 'search-invite-friends' || $type == 'find-friends'){ $type = 'my-friends';}
          if($type == 'my-net-pals' || $type == 'net-pal-request' || $type == 'search-invite-net-pals'){ $type = 'my-net-pals';}
          
if( $type == 'my-photos' || $type == 'manage-my-photo' || $type == 'create-photo-album' || $type == 'photo-detail' || $type == 'photo-album-detail'){ $type = 'my-photos';}
 if( $type == 'my-audios' || $type == 'manage-my-audio' || $type == 'create-audio-album' || $type == 'audio-album-detail'){ $type = 'my-audios';  }
 if($type == 'my-videos' || $type == 'manage-video-album' || $type == 'create-video-album' || $type == 'video-album-detail' || $type == 'video-detail'){ $type = 'my-videos';}
 if( $type == 'my-msg-inbox' || $type == 'my-msg-outbox'  || $type == 'my-msg-trashbox'){ $type = 'my-msg-inbox';}
 
 if($type == 'privacy-settings'){ $type = 'privacy-settings';}
 if($type == 'user-alert-settings'){ $type = 'user-alert-settings';}
 if($type == 'user-email-settings'){ $type = 'user-email-settings';}
if($type == 'organizer-day-view' || $type == 'organizer-week-view' || $type == 'organizer-month-view'){ $type = 'organizer-day-view';}
 if($type == 'change-password'){ $type = 'change-password';}
 if($type == 'deactivate-account'){ $type = 'deactivate-account';}
 
 
        $this->load->model("manage_advertisement_model");
        $s_where = " WHERE A.i_status  = 1 and A.p_loc ='$type'";
        $order_by = " `id` DESC ";
        $this->data['advertisement_detail'] = $this->manage_advertisement_model->fetch_multi($s_where, 0, 1, $order_by);

        #pr($this->data['advertisement_detail']);
    }

    public function new_getImList_Ajax($page = 0, $type = 'all', $s_name = '') {

        $this->load->model('prayer_group_model');
        $this->load->model('contacts_model');
        $this->load->model('my_prayer_partner_model');

        $logged_user_id = intval(decrypt($this->session->userdata('user_id')));

        $this->db->where('i_user_id', $logged_user_id);
        $arr_qry = $this->db->get('users_online');
        $ret_ = $arr_qry->row_array();

        $sql_check_user = "SELECT * from cg_users_status where i_user_id = {$logged_user_id} order by last_seen_date desc LIMIT 0,1";
        $status_arr = $this->db->query($sql_check_user)->row_array();
        //pr($ret_);
        #### new exclude arr ###

        $exclude = array();
        if ($status_arr['i_isfriend'] == '0') {
            array_push($exclude, 'friend');
        }
        if ($status_arr['i_isnetpal'] == '0') {
            array_push($exclude, 'netpal');
        }
        if ($status_arr['i_isprayerpartner'] == '0') {
            array_push($exclude, 'pp');
        }

        //pr($exclude);
        #### exclude arr ####
        //pr($ret_);

        $query_type = '';
        $exclude_id_csv = '';
        $s_query_type = '';


        $exclude_id_csv .= $logged_user_id;

        if ($s_name != '') {
            $name = " AND CONCAT(u.s_first_name,' ',u.s_last_name) LIKE '%" . $s_name . "%' ";
        }

        //$rltn_arr = CheckUserRelation($logged_user_id);
        #f & n
        #f & p
        #p & n
        $netpals_arr = array();
        $pp_arr = array();
        $frnd_arr = array();
        $total_contact_arr = array();
        $contact_arr = array();
        $DROPDOWN_HTML = '';

        if (count($exclude)) {

            if (in_array('pp', $exclude) && in_array('netpal', $exclude) && in_array('friend', $exclude)) {
                $contact_arr = array();
            } else if (in_array('pp', $exclude) && in_array('netpal', $exclude)) {
                $query_type = "(SELECT 
										
										u.id post_owner_user_id, 
										u.s_email,
										u.i_country_id,
										u.s_last_name,
										u.s_first_name ,
										u.s_profile_photo,
										'Friend'  as 'relationship',
										o.s_status,
										o.i_isfriend,
										o.i_isnetpal,
										o.i_isprayerpartner
										  
										FROM {$this->db->USER_CONTACTS} c ,{$this->db->USERS} u 
										LEFT JOIN cg_users_online o on o.i_user_id = u.id
										WHERE u.i_status=1 AND c.s_status='accepted'
										AND ((c.i_requester_id = " . $logged_user_id . " AND u.id=c.i_accepter_id) 
											OR (c.i_accepter_id=" . $logged_user_id . " AND u.id=c.i_requester_id))
										AND u.id NOT IN (" . $exclude_id_csv . ")
										AND o.s_status = 1
										AND o.i_isfriend =1
										{$name}
										GROUP BY u.id
										ORDER BY u.s_first_name ASC) ";
                $contact_arr = $this->prayer_group_model->get_frnd_list($query_type, '', '', '');


                $DROPDOWN_HTML = ' <input type="text" value="" onblur="getIMList()" id="txt_name"/>
										';
            } else if (in_array('pp', $exclude) && in_array('friend', $exclude)) {
                $query_type = "(SELECT  
									   
									   u.id post_owner_user_id, 
									   u.i_country_id,
									   u.s_email,
									   u.s_last_name,
									   u.s_first_name ,
									   u.s_profile_photo,
									   'Netpal' as 'relationship' ,
									   o.s_status,
									   o.i_isfriend,
									   o.i_isnetpal,
									   o.i_isprayerpartner
										   
									   FROM {$this->db->NETPAL} n ,{$this->db->USERS} u 
									   LEFT JOIN cg_users_online o on o.i_user_id = u.id
									   WHERE u.i_status=1 AND n.s_status='accepted'
									   AND ((n.i_requester_id = " . $logged_user_id . " AND u.id=n.i_accepter_id) 
											OR (n.i_accepter_id=" . $logged_user_id . " AND u.id=n.i_requester_id))
									   AND u.id NOT IN (" . $exclude_id_csv . ")
									   AND o.s_status = 1
									    AND o.i_isnetpal =1
									   {$name}
									   GROUP BY u.id
									   ORDER BY u.id ASC )";
                $contact_arr = $this->prayer_group_model->get_frnd_list($query_type, '', '', '');


                $DROPDOWN_HTML = ' <input type="text" value="" onblur="getIMList()" id="txt_name"/>
										';
            } else if (in_array('friend', $exclude) && in_array('netpal', $exclude)) {
                $query_type = "(SELECT   
										  
										   u.id post_owner_user_id, 
										   u.s_email,
										   u.i_country_id,
										   u.s_last_name,
										   u.s_first_name ,
										   u.s_profile_photo,
										   'Prayer Partner' as 'relationship' ,
										   o.s_status,
										   o.i_isfriend,
										   o.i_isnetpal,
										   o.i_isprayerpartner
										  
										   FROM {$this->db->USER_PRAYER_PARTNER} p ,{$this->db->USERS} u
										   LEFT JOIN cg_users_online o on o.i_user_id = u.id
										   WHERE u.i_status=1 AND p.s_status='accepted'
										   AND ((p.i_requester_id = " . $logged_user_id . " AND u.id=p.i_accepter_id) 
												OR (p.i_accepter_id=" . $logged_user_id . " AND u.id=p.i_requester_id))
										   AND u.id NOT IN (" . $exclude_id_csv . ")
										   AND o.s_status = 1
										   AND o.i_isprayerpartner = 1
										   {$name}
										   GROUP BY u.id
										   ORDER BY u.id ASC)";
                $contact_arr = $this->prayer_group_model->get_frnd_list($query_type, '', '', '');


                $DROPDOWN_HTML = ' <input type="text" value="" onblur="getIMList()" id="txt_name"/>
										';
            } else if (in_array('friend', $exclude)) {

                $query_type .= "(SELECT   
										   
										   u.id post_owner_user_id, 
										   u.s_email,
										   u.i_country_id,
										   u.s_last_name,
										   u.s_first_name ,
										   u.s_profile_photo,
										  'Prayer Partner' as 'relationship',
										  o.s_status,
										  o.i_isfriend,
										  o.i_isnetpal,
										  o.i_isprayerpartner
										   
												   FROM {$this->db->USER_PRAYER_PARTNER} p ,{$this->db->USERS} u
												   LEFT JOIN cg_users_online o on o.i_user_id = u.id
												   WHERE u.i_status=1 AND p.s_status='accepted'
												   AND ((p.i_requester_id = " . $logged_user_id . " AND u.id=p.i_accepter_id) 
														OR (p.i_accepter_id=" . $logged_user_id . " AND u.id=p.i_requester_id))
												   AND u.id NOT IN (" . $exclude_id_csv . ")
												   AND o.s_status = 1
												   AND o.i_isprayerpartner =1
												   {$name}
												   GROUP BY u.id
												   ORDER BY u.id ASC)";

                $exclude_id_PP_arr = $this->my_prayer_partner_model->get_prayerPartnerId_by_user_id($logged_user_id);
                if (count($exclude_id_PP_arr)) {
                    $exclude_id_csv .= ', ';
                    $exclude_id_csv .= implode(', ', $exclude_id_PP_arr);
                }

                $query_type .= "UNION
										   (SELECT  
											 
											 u.id post_owner_user_id, 
											 u.s_email,
											 u.i_country_id,
											 u.s_last_name,
											 u.s_first_name ,
											 u.s_profile_photo,
											 'Netpal' as 'relationship',
											 o.s_status,
										  	 o.i_isfriend,
										     o.i_isnetpal,
										     o.i_isprayerpartner
										    
											 FROM {$this->db->NETPAL} n ,{$this->db->USERS} u 
											 LEFT JOIN cg_users_online o on o.i_user_id = u.id
											 WHERE u.i_status=1 AND n.s_status='accepted'
											 AND ((n.i_requester_id = " . $logged_user_id . " AND u.id=n.i_accepter_id) 
												  OR (n.i_accepter_id=" . $logged_user_id . " AND u.id=n.i_requester_id))
											 AND u.id NOT IN (" . $exclude_id_csv . ")
											 AND o.s_status = 1
											 AND o.i_isnetpal =1
											 {$name}
											 GROUP BY u.id
											 ORDER BY u.id ASC )";

                $contact_arr = $this->prayer_group_model->get_frnd_list($query_type, '', '', '');


                $DROPDOWN_HTML = ' <input type="text" value="" onblur="getIMList()" id="txt_name"/>
										<select onchange="getIMList()" id="filter_list">
											<option value="all">All</option>
											<option value="netpal">Netpal</option>
											<option value="pp">Prayer Partner</option>
										</select>';
            } else if (in_array('netpal', $exclude)) {
                $query_type .= "(SELECT 
										  u.id post_owner_user_id, 
										  u.s_email,
										  u.i_country_id,
										  u.s_last_name,
										  u.s_first_name ,
										  u.s_profile_photo,
										  'Friend'  as  'relationship',
										  o.s_status,
										  o.i_isfriend,
										  o.i_isnetpal,
										  o.i_isprayerpartner
											
											  FROM {$this->db->USER_CONTACTS} c ,{$this->db->USERS} u 
											  LEFT JOIN cg_users_online o on o.i_user_id = u.id
											  WHERE u.i_status=1 AND c.s_status='accepted'
											  AND ((c.i_requester_id = " . $logged_user_id . " AND u.id=c.i_accepter_id) 
												  OR (c.i_accepter_id=" . $logged_user_id . " AND u.id=c.i_requester_id))
											  AND u.id NOT IN (" . $exclude_id_csv . ")
											  AND o.s_status = 1 AND o.i_isfriend =1
											  {$name}
											  GROUP BY u.id
											  ORDER BY u.s_first_name ASC)
								  ";
                $contacts = $this->contacts_model->fetch_multi_online_friends($WHERE, null, null, $ORDER_BY);

                $exclude_id_arr = $this->contacts_model->get_friendsId_by_user_id($logged_user_id);

                if (count($exclude_id_arr)) {
                    $exclude_id_csv .= ', ';
                    $exclude_id_csv .= implode(', ', $exclude_id_arr);
                }

                $query_type .= "UNION
										(SELECT   
										   
										   u.id post_owner_user_id, 
										   u.s_email,
										   u.i_country_id,
										   u.s_last_name,
										   u.s_first_name ,
										   u.s_profile_photo,
										  'Prayer Partner' as 'relationship',
										  o.s_status,
										  o.i_isfriend,
										  o.i_isnetpal,
										  o.i_isprayerpartner
										   
												   FROM {$this->db->USER_PRAYER_PARTNER} p ,{$this->db->USERS} u
												   LEFT JOIN cg_users_online o on o.i_user_id = u.id
												   WHERE u.i_status=1 AND p.s_status='accepted'
												   AND ((p.i_requester_id = " . $logged_user_id . " AND u.id=p.i_accepter_id) 
														OR (p.i_accepter_id=" . $logged_user_id . " AND u.id=p.i_requester_id))
												   AND u.id NOT IN (" . $exclude_id_csv . ")
												   AND o.s_status = 1
												   AND o.i_isprayerpartner =1
												   {$name}
												   GROUP BY u.id
												   ORDER BY u.id ASC)";

                $exclude_id_PP_arr = $this->my_prayer_partner_model->get_prayerPartnerId_by_user_id($logged_user_id);
                if (count($exclude_id_PP_arr)) {
                    $exclude_id_csv .= ', ';
                    $exclude_id_csv .= implode(', ', $exclude_id_PP_arr);
                }

                $contact_arr = $this->prayer_group_model->get_frnd_list($query_type, '', '', '');

                $DROPDOWN_HTML = ' <input type="text" value="" onblur="getIMList()" id="txt_name"/>
										<select onchange="getIMList()" id="filter_list">
											<option value="all">All</option>
											<option value="friend">Friend</option>
											<option value="pp">Prayer Partner</option>
										</select>';
            } else if (in_array('pp', $exclude)) {

                $query_type .= "(SELECT 
										  u.id post_owner_user_id, 
										  u.s_email,
										  u.i_country_id,
										  u.s_last_name,
										  u.s_first_name ,
										  u.s_profile_photo,
										  'Friend'  as  'relationship',
										  o.s_status,
										  o.i_isfriend,
										  o.i_isnetpal,
										  o.i_isprayerpartner
											
											  FROM {$this->db->USER_CONTACTS} c ,{$this->db->USERS} u 
											  LEFT JOIN cg_users_online o on o.i_user_id = u.id
											  WHERE u.i_status=1 AND c.s_status='accepted'
											  AND ((c.i_requester_id = " . $logged_user_id . " AND u.id=c.i_accepter_id) 
												  OR (c.i_accepter_id=" . $logged_user_id . " AND u.id=c.i_requester_id))
											  AND u.id NOT IN (" . $exclude_id_csv . ")
											  AND o.s_status = 1 AND o.i_isfriend =1
											  {$name}
											  GROUP BY u.id
											  ORDER BY u.s_first_name ASC)
								  ";
                $contacts = $this->contacts_model->fetch_multi_online_friends($WHERE, null, null, $ORDER_BY);

                $exclude_id_arr = $this->contacts_model->get_friendsId_by_user_id($logged_user_id);

                if (count($exclude_id_arr)) {
                    $exclude_id_csv .= ', ';
                    $exclude_id_csv .= implode(', ', $exclude_id_arr);
                }

                $query_type .= "UNION
										   (SELECT  
											 
											 u.id post_owner_user_id, 
											 u.s_email,
											 u.i_country_id,
											 u.s_last_name,
											 u.s_first_name ,
											 u.s_profile_photo,
											 'Netpal' as 'relationship',
											 o.s_status,
										  	 o.i_isfriend,
										     o.i_isnetpal,
										     o.i_isprayerpartner
										    
											 FROM {$this->db->NETPAL} n ,{$this->db->USERS} u 
											 LEFT JOIN cg_users_online o on o.i_user_id = u.id
											 WHERE u.i_status=1 AND n.s_status='accepted'
											 AND ((n.i_requester_id = " . $logged_user_id . " AND u.id=n.i_accepter_id) 
												  OR (n.i_accepter_id=" . $logged_user_id . " AND u.id=n.i_requester_id))
											 AND u.id NOT IN (" . $exclude_id_csv . ")
											 AND o.s_status = 1
											 AND o.i_isnetpal =1
											 {$name}
											 GROUP BY u.id
											 ORDER BY u.id ASC )";



                $contact_arr = $this->prayer_group_model->get_frnd_list($query_type, '', '', '');


                $DROPDOWN_HTML = ' <input type="text" value="" onblur="getIMList()" id="txt_name"/>
										<select onchange="getIMList()" id="filter_list">
											<option value="all">All</option>
											<option value="friend">Friend</option>
											<option value="netpal">Netpal</option>
										</select>';
            }



            //pr($contact_arr,1);				
        } else {


            if ($type == 'all') {
                $query_type .= "(SELECT 
										  u.id post_owner_user_id, 
										  u.s_email,
										  u.i_country_id,
										  u.s_last_name,
										  u.s_first_name ,
										  u.s_profile_photo,
										  'Friend'  as  'relationship',
										  o.s_status,
										  o.i_isfriend,
										  o.i_isnetpal,
										  o.i_isprayerpartner
											
											  FROM {$this->db->USER_CONTACTS} c ,{$this->db->USERS} u 
											  LEFT JOIN cg_users_online o on o.i_user_id = u.id
											  WHERE u.i_status=1 AND c.s_status='accepted'
											  AND ((c.i_requester_id = " . $logged_user_id . " AND u.id=c.i_accepter_id) 
												  OR (c.i_accepter_id=" . $logged_user_id . " AND u.id=c.i_requester_id))
											  AND u.id NOT IN (" . $exclude_id_csv . ")
											  AND o.s_status = 1 AND o.i_isfriend =1
											  {$name}
											  GROUP BY u.id
											  ORDER BY u.s_first_name ASC)
								  ";
                $contacts = $this->contacts_model->fetch_multi_online_friends($WHERE, null, null, $ORDER_BY);

                $exclude_id_arr = $this->contacts_model->get_friendsId_by_user_id($logged_user_id);

                if (count($exclude_id_arr)) {
                    $exclude_id_csv .= ', ';
                    $exclude_id_csv .= implode(', ', $exclude_id_arr);
                }

                $query_type .= "UNION
										(SELECT   
										   
										   u.id post_owner_user_id, 
										   u.s_email,
										   u.i_country_id,
										   u.s_last_name,
										   u.s_first_name ,
										   u.s_profile_photo,
										  'Prayer Partner' as 'relationship',
										  o.s_status,
										  o.i_isfriend,
										  o.i_isnetpal,
										  o.i_isprayerpartner
										   
												   FROM {$this->db->USER_PRAYER_PARTNER} p ,{$this->db->USERS} u
												   LEFT JOIN cg_users_online o on o.i_user_id = u.id
												   WHERE u.i_status=1 AND p.s_status='accepted'
												   AND ((p.i_requester_id = " . $logged_user_id . " AND u.id=p.i_accepter_id) 
														OR (p.i_accepter_id=" . $logged_user_id . " AND u.id=p.i_requester_id))
												   AND u.id NOT IN (" . $exclude_id_csv . ")
												   AND o.s_status = 1
												   AND o.i_isprayerpartner =1
												   {$name}
												   GROUP BY u.id
												   ORDER BY u.id ASC)";

                $exclude_id_PP_arr = $this->my_prayer_partner_model->get_prayerPartnerId_by_user_id($logged_user_id);
                if (count($exclude_id_PP_arr)) {
                    $exclude_id_csv .= ', ';
                    $exclude_id_csv .= implode(', ', $exclude_id_PP_arr);
                }

                $query_type .= "UNION
										   (SELECT  
											 
											 u.id post_owner_user_id, 
											 u.s_email,
											 u.i_country_id,
											 u.s_last_name,
											 u.s_first_name ,
											 u.s_profile_photo,
											 'Netpal' as 'relationship',
											 o.s_status,
										  	 o.i_isfriend,
										     o.i_isnetpal,
										     o.i_isprayerpartner
										    
											 FROM {$this->db->NETPAL} n ,{$this->db->USERS} u 
											 LEFT JOIN cg_users_online o on o.i_user_id = u.id
											 WHERE u.i_status=1 AND n.s_status='accepted'
											 AND ((n.i_requester_id = " . $logged_user_id . " AND u.id=n.i_accepter_id) 
												  OR (n.i_accepter_id=" . $logged_user_id . " AND u.id=n.i_requester_id))
											 AND u.id NOT IN (" . $exclude_id_csv . ")
											 AND o.s_status = 1
											 AND o.i_isnetpal =1
											 {$name}
											 GROUP BY u.id
											 ORDER BY u.id ASC )";
            } else if ($type == 'friend') {

                $query_type = "(SELECT 
											
											u.id post_owner_user_id, 
											u.s_email,
											u.i_country_id,
											u.s_last_name,
											u.s_first_name ,
											u.s_profile_photo,
											'Friend'  as 'relationship',
											o.s_status,
											o.i_isfriend,
											o.i_isnetpal,
											o.i_isprayerpartner
											  
											FROM {$this->db->USER_CONTACTS} c ,{$this->db->USERS} u 
											LEFT JOIN cg_users_online o on o.i_user_id = u.id
											WHERE u.i_status=1 AND c.s_status='accepted'
											AND ((c.i_requester_id = " . $logged_user_id . " AND u.id=c.i_accepter_id) 
												OR (c.i_accepter_id=" . $logged_user_id . " AND u.id=c.i_requester_id))
											AND u.id NOT IN (" . $exclude_id_csv . ")
											AND o.s_status = 1
											AND o.i_isfriend =1
											{$name}
											GROUP BY u.id
											ORDER BY u.s_first_name ASC) ";
            } else if ($type == 'netpal') {

                $query_type = "(SELECT  
										   
										   u.id post_owner_user_id, 
										   u.i_country_id,
										   u.s_email,
										   u.s_last_name,
										   u.s_first_name ,
										   u.s_profile_photo,
										   'Netpal' as 'relationship' ,
										   o.s_status,
										   o.i_isfriend,
										   o.i_isnetpal,
										   o.i_isprayerpartner
											   
										   FROM {$this->db->NETPAL} n ,{$this->db->USERS} u 
										   LEFT JOIN cg_users_online o on o.i_user_id = u.id
										   WHERE u.i_status=1 AND n.s_status='accepted'
										   AND ((n.i_requester_id = " . $logged_user_id . " AND u.id=n.i_accepter_id) 
												OR (n.i_accepter_id=" . $logged_user_id . " AND u.id=n.i_requester_id))
										   AND u.id NOT IN (" . $exclude_id_csv . ")
										   AND o.s_status = 1
											AND o.i_isnetpal =1
										   {$name}
										   GROUP BY u.id
										   ORDER BY u.id ASC )";
            } else if ($type == 'pp') {

                $query_type = "(SELECT   
											  
											   u.id post_owner_user_id, 
											   u.s_email,
											   u.i_country_id,
											   u.s_last_name,
											   u.s_first_name ,
											   u.s_profile_photo,
											   'Prayer Partner' as 'relationship' ,
											   o.s_status,
											   o.i_isfriend,
											   o.i_isnetpal,
											   o.i_isprayerpartner
											  
											   FROM {$this->db->USER_PRAYER_PARTNER} p ,{$this->db->USERS} u
											   LEFT JOIN cg_users_online o on o.i_user_id = u.id
											   WHERE u.i_status=1 AND p.s_status='accepted'
											   AND ((p.i_requester_id = " . $logged_user_id . " AND u.id=p.i_accepter_id) 
													OR (p.i_accepter_id=" . $logged_user_id . " AND u.id=p.i_requester_id))
											   AND u.id NOT IN (" . $exclude_id_csv . ")
											   AND o.s_status = 1
											   AND o.i_isprayerpartner = 1
											   {$name}
											   GROUP BY u.id
											   ORDER BY u.id ASC)";
            }


            $DROPDOWN_HTML = '<input type="text" value="" onblur="getIMList()" id="txt_name"/>
									<select onchange="getIMList()" id="filter_list">
										<option value="all">All</option>
										<option value="friend">Friend</option>
										<option value="netpal">Netpal</option>
										<option value="pp">Prayer Partner</option>
									</select>';
            $contact_arr = $this->prayer_group_model->get_frnd_list($query_type, '', '', '');
        }

        $data['im_arr'] = ($contact_arr);


        //
        array_sort_by_column($data['im_arr'], 's_first_name');
        //pr($data['im_arr'],1);
        if (count($data['im_arr'])) {
            $VIEW = "logged/wall/im_list_ajax.phtml";
            $html = $this->load->view($VIEW, $data, true);
        } else {
            $html = '<li style="color:#000000;">No results.</li>';
        }

        echo json_encode(array('success' => true, 'im_html' => $html, 'DROPDOWN_HTML' => $DROPDOWN_HTML));
    }

    function getUserClock() {

        $zone_val = $this->input->post('zone_val');
        $time = getUserLocalTime($zone_val);
        $time_html = 'Time: ' . $time;
        echo json_encode(array('result' => 'success', 'time' => $time_html));
        exit;
    }
    
     /*****************************/
     public  function getmediaLatestAdvertisement(){
        //die('dd');
        $i_limit = 1;
        $type = $this->uri->segment(2);
        $s_type =  $this->uri->segment(1);
        $i_type = $this->uri->segment(3);
        /***********Banner*****************/   
        if($s_type == 'media-center'){
            
            $i_limit = 1; 
         $this->load->model("manage_advertisement_model");
        $s_where = " WHERE A.i_status  = 1 and A.p_loc ='media-banner'";
        $order_by = " `id` DESC ";
        $this->data['advertisementmedia_detail'] = $this->manage_advertisement_model->fetch_multi_media_center($s_where, 0, $i_limit, $order_by);
        }
            /***********Banner*****************/
        if(empty($type)){
            $s_type =  $this->uri->segment(1);
            /******************listen***********************************/ //$s_type == 'listen-detail'
              if(!empty($s_type) && $s_type == 'listen' ){
                  //die('d');
                  $i_limit = 4; 
         $this->load->model("manage_advertisement_model");
        $s_where = " WHERE A.i_status  = 1 and A.p_loc ='$s_type'";
        $order_by = " `id` DESC ";
        $this->data['advertisementmedia_detail'] = $this->manage_advertisement_model->fetch_multi_media_center($s_where, 0, $i_limit, $order_by);
        //pr($this->data['advertisementmedia_detail']);     
        }
         
        
        /****************************watch*********************************************/
        if(!empty($s_type) && $s_type == 'watch'){
             $i_limit = 3;
              $this->load->model("manage_advertisement_model");
        $s_where = " WHERE A.i_status  = 1 and A.p_loc ='$s_type'";
        $order_by = " `id` DESC ";
        $this->data['advertisementmedia_detail'] = $this->manage_advertisement_model->fetch_multi_media_center($s_where, 0, $i_limit, $order_by);
             
        }
        
        
        }
        /****************************watch details*********************************************/
        if(!empty($s_type) && !empty($type) &&  $i_type == 'watch-detail' ){
            //die();
             $i_limit = 3;
              $this->load->model("manage_advertisement_model");
        $s_where = " WHERE A.i_status  = 1 and A.p_loc ='watch'";
        $order_by = " `id` DESC ";
        $this->data['advertisementmedia_detail'] = $this->manage_advertisement_model->fetch_multi_media_center($s_where, 0, $i_limit, $order_by);
        }
       /******************listen detail***********************************/ 
        if(!empty($s_type) && $s_type == 'listen-detail' ){
                  //die('d');
                  $i_limit = 4; 
         $this->load->model("manage_advertisement_model");
        $s_where = " WHERE A.i_status  = 1 and A.p_loc = 'listen'";
        $order_by = " `id` DESC ";
        $this->data['advertisementmedia_detail'] = $this->manage_advertisement_model->fetch_multi_media_center($s_where, 0, $i_limit, $order_by);
        //pr($this->data['advertisementmedia_detail']);     
        }
        /******************listen detail end***********************************/
        /***********************read******************************************/
       if($type == 'category')
        {
            
            //die('dd');
            $i_limit = 3;
              $this->load->model("manage_advertisement_model");
        $s_where = " WHERE A.i_status  = 1 and A.p_loc ='category-page'";
        $order_by = " `id` DESC ";
        $this->data['advertisementmedia_detail'] = $this->manage_advertisement_model->fetch_multi_media_center($s_where, 0, $i_limit, $order_by);
            
        
        }
        if(!empty($s_type) && !empty($type)&& $i_type == 'christian-news-details' ){
            
            //die('dd');
             $i_limit = 3;
              $this->load->model("manage_advertisement_model");
        $s_where = " WHERE A.i_status  = 1 and A.p_loc ='read'";
        $order_by = " `id` DESC ";
        $this->data['advertisementmedia_detail'] = $this->manage_advertisement_model->fetch_multi_media_center($s_where, 0, $i_limit, $order_by);
        }
        
        if(!empty($type) && $type == 'read')
        {
         $i_limit = 1; 
         $this->load->model("manage_advertisement_model");
        $s_where = " WHERE A.i_status  = 1 and A.p_loc ='$type'";
        $order_by = " `id` DESC ";
        $this->data['advertisementmedia_detail'] = $this->manage_advertisement_model->fetch_multi_media_center($s_where, 0, $i_limit, $order_by);
        }
        /**************seach page*****************************/
        if($i_type == 'search_news'){
             $i_limit = 4; 
         $this->load->model("manage_advertisement_model");
        $s_where = " WHERE A.i_status  = 1 and A.p_loc ='Search-page'";
        $order_by = " `id` DESC ";
        $this->data['advertisementmedia_detail'] = $this->manage_advertisement_model->fetch_multi_media_center($s_where, 0, $i_limit, $order_by);
        }
       // pr($this->data['advertisementmedia_detail']);
    }
    /*****************************/
    
     function session_des(){
            
             //$this->users_model->logout();
            $type = $this->input->post('type');
           // $this->load->library('session');
            if($type == 'logout'){ 
               // session_unset();
                 $this->users_model->logout();

           


            }
        }
        function send_contact_mail(){
            $cap = $this->input->post('cap');
            $name = $this->input->post('nam');
            $email = $this->input->post('eml');
            $message = $this->input->post('msg');
            $phone_number = $this->input->post('phn');
            $query = $this->db->get_where('cg_admin_user', array('id' => 1));
            foreach ($query->result() as $row)
                {
                   $admin_mail = $row->s_email;
                }
            if($cap == $_SESSION['cap_code'])
                 {
                
                    echo 'ok';
                    
                    /*****************************/
                    // $logged_id=intval(decrypt($_SESSION['user_id']));
                               // $newArr =  get_primary_user_info($logged_id);
                $this->load->library('email');
    $this->load->helper('html');
        $email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
$this->email->initialize($email_setting);
//$body = "<p>Dear User, </p><p>your church request is denied</p><p>Thanks</p><p>admin@cogtime.com</p> ";
  $logo="http://cogtime.com/images/logo.png";
    $body = '<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#e9f3f5" style="font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:19px;">
  <tr>
    <td align="left" style="background:#013D62; border-bottom:5px solid #62C3BC; padding:15px 0 15px 20px;"><img src="'.$logo.'" alt= ""></td>
  </tr>
  <tr style="border-top:1px solid #ffffff;">
    <td style="padding-top:10px; padding-bottom:10px;">&nbsp;</td>
  </tr>
  <tr>
  	
  </tr>
  <tr>
  	<td style="padding:15px;"><p> Dear Admin,</p><p>You get a mail from '.$name.' </p><p>Contact person Email id : '.$email.' </p><p>Contact person contact number : '.$phone_number.' </p>
            <p>Contact person message : '.$message.' </p>
	</td>
</tr>
  <tr>
    <td align="center" valign="middle" style="background:#A8A7A7; padding:15px;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle" style="color:#d3edfd; font-family:Arial, Helvetica, sans-serif; font-size:12px;"> <a href="http://acumencs.com/drandpt-arabic/contact-us/" style="color:#d3edfd; text-decoration:none;"></a></td>
        
        <td align="right" style="color:#013d62; font-family:Arial, Helvetica, sans-serif; font-size:12px; text-align="center" ">� All Rights Reserved<span style="color:#525252;"><strong> COGTIME 2014  </strong></span></td>
      </tr>
    </table></td>
  </tr>
</table>'; 
$this->email->from($email, 'From '.$name.'');
$this->email->to($admin_mail);
$this->email->bcc("$mailids");
//$this->email->cc('arif.zisu@gmail.com');
//$this->email->bcc('them@their-example.com');

$this->email->subject('Contact us mail from Cogtime');
$this->email->message("$body");

$this->email->send();	
                    /***************************/

                  }
                else {
                    echo 'error';
                }
        }
        
        /*******************delete expire prayer chat room*********************/
        
        /****************************************add ip blocker on 18-12-2014**********************/
          function ip_block(){
            try {

            # killing all session-data...
            $this->users_model->logout();

            # redirecting to the home-page...
            header('Location: ' . base_url().'?blockStatus=1');
            exit;
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        } 
            
        }
        
        
        
        /**********************************************************************/
        /****************for church section 14-01-2015***************************************/
         /******************today*********************/   
        
        
        function find_church_by_email(){
            $ch_email = $this->input->post('email_id');
            
            $query = $this->db->get_where('cg_church', array('ch_email' => $ch_email ,'i_disabled'=>1 ));
            $result = $query->result();
            $data['result_arr'] = $result;
            $VIEW_FILE = 'church_ajax.phtml';
            echo $this->load->view($VIEW_FILE, $data,true);
            //pr($result);
            //die($ch_email);
        }
        
        
        function send_verification_code(){
            
            $ch_id = $this->input->post('id');
            $ch_email = $this->input->post('email');
            $v_code = time();
            
            
            /*****************mail send************************************************/
            $query = $this->db->get_where('cg_admin_user', array('id' => 1));
            foreach ($query->result() as $row)
                {
                   $admin_mail = $row->s_email;
                }
            
            
            
             $this->load->library('email');
    $this->load->helper('html');
        $email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
$this->email->initialize($email_setting);
//$body = "<p>Dear User, </p><p>your church request is denied</p><p>Thanks</p><p>admin@cogtime.com</p> ";
  $logo="http://cogtime.com/images/logo.png";
    $body = '<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#e9f3f5" style="font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:19px;">
  <tr>
    <td align="left" style="background:#013D62; border-bottom:5px solid #62C3BC; padding:15px 0 15px 20px;"><img src="'.$logo.'" alt= ""></td>
  </tr>
  <tr style="border-top:1px solid #ffffff;">
    <td style="padding-top:10px; padding-bottom:10px;">&nbsp;</td>
  </tr>
  <tr>
  	
  </tr>
  <tr>
  	<td style="padding:15px;"><p> Dear Church admin,</p><p>You get a verification code : '.$v_code.' </p>
            <p>Now you can create church space , this code is valid within 24 hours</p>
            
            <p>Thanks</p>
<p>admin@cogtime.com</p>
            
	</td>
</tr>
  <tr>
    <td align="center" valign="middle" style="background:#A8A7A7; padding:15px;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle" style="color:#d3edfd; font-family:Arial, Helvetica, sans-serif; font-size:12px;"> <a href="http://acumencs.com/drandpt-arabic/contact-us/" style="color:#d3edfd; text-decoration:none;"></a></td>
        
        <td align="right" style="color:#013d62; font-family:Arial, Helvetica, sans-serif; font-size:12px; text-align="center" ">� All Rights Reserved<span style="color:#525252;"><strong> COGTIME 2014  </strong></span></td>
      </tr>
    </table></td>
  </tr>
</table>'; 
$this->email->from($admin_mail, 'From Cogtime Church');
$this->email->to($ch_email);
//->email->bcc("$mailids");
//$this->email->cc('arif.zisu@gmail.com');
//$this->email->bcc('them@their-example.com');

$this->email->subject('Verification code of Cogtime church');
$this->email->message("$body");

 $this->email->send();	
//var_dump($res);

//echo $body;
//die();

                    /***************************/
            
            /************************************************************************/
            
            
           // die($ch_email);
            
            
            $v_created_time = get_db_datetime();
            $data = array(
               'ch_verification_code' => $v_code,
               'ch_code_start_time' => $v_created_time,
               
            );

$this->db->where('id', $ch_id);
$this->db->update('cg_church', $data);

            //die($ch_id);
        }
        
        function add_new_church(){
            
            $txt_email_new = get_formatted_string($this->input->post('txt_email_new'));
            $txt_name = get_formatted_string($this->input->post('txt_name'));
            $txt_address = get_formatted_string($this->input->post('txt_address'));
            $txt_postcode = trim($this->input->post('txt_postcode'));
            $txt_phone_new =trim($this->input->post('txt_phone'));
            $sel_country = intval(decrypt($this->input->post('sel_country')));
            $txt_state = intval(decrypt($this->input->post('txt_state')));
            $txt_city = intval(decrypt($this->input->post('txt_city')));
            $query = $this->db->get_where('cg_church', array('ch_email' => $txt_email_new));
            $result = $query->result();
            $v_code = time();
            
        if(!empty($result)){
                            echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'Error!') );
                        }
                        else if(empty($result)){
                            
                            /**********************send mail********************************************/
                            
            /*****************mail send************************************************/
            $query = $this->db->get_where('cg_admin_user', array('id' => 1));
            foreach ($query->result() as $row)
                {
                   $admin_mail = $row->s_email;
                }
            
            
            
             $this->load->library('email');
    $this->load->helper('html');
        $email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
$this->email->initialize($email_setting);
//$body = "<p>Dear User, </p><p>your church request is denied</p><p>Thanks</p><p>admin@cogtime.com</p> ";
  $logo="http://cogtime.com/images/logo.png";
    $body = '<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#e9f3f5" style="font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:19px;">
  <tr>
    <td align="left" style="background:#013D62; border-bottom:5px solid #62C3BC; padding:15px 0 15px 20px;"><img src="'.$logo.'" alt= ""></td>
  </tr>
  <tr style="border-top:1px solid #ffffff;">
    <td style="padding-top:10px; padding-bottom:10px;">&nbsp;</td>
  </tr>
  <tr>
  	
  </tr>
  <tr>
  	<td style="padding:15px;"><p> Dear Church admin,</p>
<p>Your church request has been forwarded to admin,</p><p> After confirmation you can create church space </p>
<p>Thanks</p>
<p>admin@cogtime.com</p>

            
	</td>
</tr>
  <tr>
    <td align="center" valign="middle" style="background:#A8A7A7; padding:15px;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle" style="color:#d3edfd; font-family:Arial, Helvetica, sans-serif; font-size:12px;"> <a href="http://acumencs.com/drandpt-arabic/contact-us/" style="color:#d3edfd; text-decoration:none;"></a></td>
        
        <td align="right" style="color:#013d62; font-family:Arial, Helvetica, sans-serif; font-size:12px; text-align="center" ">� All Rights Reserved<span style="color:#525252;"><strong> COGTIME 2014  </strong></span></td>
      </tr>
    </table></td>
  </tr>
</table>'; 
$this->email->from($admin_mail, 'From Cogtime Church');
$this->email->to($txt_email_new);
//->email->bcc("$mailids");
//$this->email->cc('arif.zisu@gmail.com');
//$this->email->bcc('them@their-example.com');

$this->email->subject('Cogtime church registration');
$this->email->message("$body");

 $this->email->send();	
//var_dump($res);

//echo $body;
//die();

                    /***************************/
                            /**********************************************************************/
                            
                            
                            
                                $data = array(
                            's_name' => $txt_name ,
                            's_address' => $txt_address ,
                            'i_city_id' =>  $txt_city,
                            'i_state_id' => $txt_state,
                            'i_country_id' => $sel_country,
                             's_postcode' => $txt_postcode,
                             's_phone' => $txt_phone_new,       
                             'dt_created_on' => get_db_datetime(),
                              'i_disabled' => 2,
                               'ch_email' => $txt_email_new,
                                'ch_verification_code'=> '',
                                 'ch_code_start_time' => get_db_datetime()
                                  
                                    
                                    
                             
                         );

              $this->db->insert('cg_church', $data);
              echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'ok'));

                        }
			
			
            
        }
       function verify_church_code(){
            $vcode = $this->input->post('vcode');
            $query = $this->db->get_where('cg_church', array('ch_verification_code' => $vcode, 'i_disabled'=>1));
            $result = $query->result();
            
            
            if(empty($result)){
              echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'Error!') );
            }
            else if(!empty($result)){
                
                /****************************expire time************************/
                 $recent_time = get_db_datetime();
             $post_time = $result[0]->ch_code_start_time;
            // echo  $diff = date_diff($recent_time,$post_time);
             /**************************/
                        $date1 = $post_time;
                        $date2 = $recent_time;
                        $diff = abs(strtotime($date2) - strtotime($date1));
                        $time_diff = floor($diff/3600);
                /*******************************************************/
                
                
                
                
                if($result[0]->ch_page_url != ''){
                    echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'err')); 
                }
                 else if($time_diff >= 24){
                    echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'err1'));
                }
                else{
              $query1 = $this->db->get_where('cg_church', array('ch_verification_code' => $vcode, 'i_disabled'=>1));
                     $result1 = $query->result();
                     
                     
                     
                     $ch_admin_id = intval(decrypt($this->session->userdata('user_id')));
                     $ch_id = $result1[0]->id;
                     $v_code = $result1[0]->ch_verification_code;
                     $dt_join_on = get_db_datetime();
                     $ch_name = $result1[0]->s_name;
                     $newstr = preg_replace('/[^a-zA-Z0-9\']/', '_',$ch_name);
                     $newstr = str_replace("'", '', $newstr);
                     $ch_name_final = $newstr;
                     $ch_sp_url = 'church/'.$ch_name_final.'/'.$ch_id;
                      $ch_public_url ='church_public/'.$ch_name_final.'/'.$ch_id;
                     $data = array(
                        'ch_admin_id' => $ch_admin_id ,
                        'ch_id' => $ch_id ,
                        'v_code' => $v_code,
                         'dt_join_on'=> $dt_join_on,
                         'ch_sp_url'=>$ch_sp_url,
                         'ch_name' => $ch_name,
                         'ch_public_url'=>$ch_public_url
                     );

              $this->db->insert('cg_church_admin', $data); 
              
              
              $data = array(
               'ch_page_url' => $ch_sp_url,
               'ch_public_url' =>$ch_public_url   
               
            );

$this->db->where('id', $ch_id);
$this->db->update('cg_church', $data); 

              
                     
                     
                    
                echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'ok')); 
                }
                
                
                
            }
            
        }
         public function check_is_church_admin($id='',$c_id){
            $query = $this->db->get_where('cg_church_admin', array('ch_admin_id' => $id,'ch_id'=>$c_id));
            $result = $query->result();

            if(empty($result)){
                 header("location:" . base_url());
                 exit;
            }
        }
        /** church_css**/
		protected function _add_church_css_arr(array $css_files_arr) {
        try {
            foreach ($css_files_arr as $key_css => $item_css) {
                if (is_numeric($key_css) && !is_array($item_css)) {
                    $css_files[$item_css] = array();
                } else {
                    $css_files[$key_css] = $item_css;
                }
                //$this->css_files = array_merge($this->css_files, $css_files_arr);
            }
			$this->css_files =array();
            $this->css_files = array_merge($this->css_files, $css_files);
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }
        /**********************************************************************/
        /********************************************/
     public function check_church_id_empty($store_url_session = TRUE, $id , $arr_autorized_user_types = array()) {
        try {

            //$this->session->unset_userdata('loginPopUp');
           
                //echo $url;
                /* echo "<script>window.location='".base_url()."index.html'+window.location.hash</script>"; */
                if ($id == ''){
                    header("location:" . base_url());
              
                }

                
            
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }
     
    
    
     function find_member_church_by_email(){
            $ch_email = $this->input->post('email_id');
            
           // $query = $this->db->get_where('cg_church', array('ch_email' => $ch_email ,'i_disabled'=>1 ));
           $query =  $this->db->query('Select * from cg_church where  ch_email LIKE "'.$ch_email.'" AND i_disabled = 1 AND ch_public_url IS NOT NULL ');
            $result = $query->result();
            $data['result_arr'] = $result;
            $VIEW_FILE = 'church_list_ajax.phtml';
            echo $this->load->view($VIEW_FILE, $data,true);
            //pr($result);
            //die($ch_email);
        }
        function send_member_inv(){
            
            $church_id = $this->input->post('church_id');
            $member_id = $this->input->post('member_id');
            
            $query = $this->db->get_where('cg_church_member', array('church_id' => $church_id , 'member_id' => $member_id ));
           $result =  $query->result(); 
            
            if(!empty($result)){
                  echo json_encode( array('success'=>true,'msg'=>'error')); 
            }else if(empty ($result)){
            
            $data = array(
   'church_id' => $church_id ,
   'member_id' => $member_id ,
   'is_approved' => 0,
    'created_date'=>  get_db_datetime(),
    'is_deleted'=> 0            
);

$res = $this->db->insert('cg_church_member', $data);
if($res){
     echo json_encode( array('success'=>true,'msg'=>'ok')); 
}
            
        }
        
        }

}

/*function getLatestProject(){
	 
	$this->load->model("projects_model"); 
	
	$this->data['project_detail']  = $this->projects_model->get_project();
	
	#pr($this->data['project_detail']);
 }
*/


