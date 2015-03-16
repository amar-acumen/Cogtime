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

class My_blog extends Base_controller {

    private $pagination_per_page = 10;
    private $search_pagination_per_page = 5;
    private $popular_pagination_per_page = 10;
    private $all_pagination_per_page = 10;

    public function __construct() {

        try {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...

            //$this->load->model('users_model');
            $this->load->model('my_blog_model');
            $this->load->model('my_blog_post_model');
            $this->load->model('user_notifications_model');
            $this->load->model('user_alert_model');
            $this->i_profile_id = intval(decrypt($this->session->userdata('user_id')));
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
                'js/lightbox.js', 'js/jquery.dd.js', 'js/jquery-ui-1.8.2.custom.min.js',*/
                'js/production/tweet_utilities.js',
//                'js/stepcarousel.js','js/frontend/logged/tweets/tweet_utilities.js'
            ));

//            parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
//                'css/dd.css'));


            $wh = " WHERE c.i_user_id='" . $this->i_profile_id . "'  AND c.i_user_id=u.id ";
            $data['blogdata'] = $this->my_blog_model->fetch_multi($wh, '', '', '');

            $data['pagination_per_page'] = $this->pagination_per_page;
            $data['blog_id'] = $data['blogdata'][0]['blogid'];
            # pr($data['blogdata']);

            $this->session->set_userdata('where', '');

            # view file...
            ob_start();
            $content = $this->generate_blog_post_listing_AJAX($data['blogdata'][0]['blogid']);
            ob_end_clean();
            $logged_user_id = intval(decrypt($this->session->userdata('user_id')));
            $data['logged_user_id'] = $logged_user_id;
            $data['listingContent'] = $content;
            $VIEW = "logged/blogs/my_blog.phtml";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

    function create_my_blog() {

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
                'js/stepcarousel.js',*/'js/production/tweet_utilities.js'
            ));

//            parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
//                'css/dd.css'));





            $VIEW = "logged/blogs/create_my_blog.phtml";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

    function edit_my_blog() {

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
                'js/stepcarousel.js',*/'production/tweet_utilities.js'
            ));

            parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
                'css/dd.css'));



            $wh = " WHERE c.i_user_id='" . $this->i_profile_id . "'  AND c.i_user_id=u.id ";
            $data['blogdata'] = $this->my_blog_model->fetch_multi($wh, '', '', '');


            $data['blog_id'] = $data['blogdata'][0]['blogid'];

            $VIEW = "logged/blogs/edit_my_blog.phtml";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

    public function add_blog() {

        $blog_title = $this->input->post("blog_title", true);
        $blog_desc = $this->input->post("blog_desc", true);
         $ip = getenv("REMOTE_ADDR") ;
        if (trim($blog_title) == 'Type title here' || trim($blog_title) == '') {
            $return_arr['blog_title'] = '* Required field.';
        }
        if (trim($blog_desc) == 'Max 500 Characters' || trim($blog_desc) == '') {
            $return_arr['blog_desc'] = '* Required field.';
        }

        if (count($return_arr) == 0) {
            $arr = array("s_title" => $blog_title, "s_description" => $blog_desc, "i_user_id" => $this->i_profile_id , "u_ip" => $ip);

            $wh = " WHERE c.i_user_id='" . $this->i_profile_id . "'";
            $data['blogdata'] = $this->my_blog_model->fetch_multi($wh, '', '', '');
//pr($data['blogdata'],1);
//echo "title : ".$data['blogdata'][0]['s_title'];exit;
            if (count($data['blogdata'])) {

                $arr['dt_updated_on'] = get_db_datetime();
                $res = $this->my_blog_model->edit_blog($arr, $data['blogdata'][0]['blogid']);
                $msg = "Blog successfully updated";
                echo json_encode(array('msg' => $msg, 'result' => true, 'blog_title' => $blog_title));
                exit;
            } else {
                $arr['dt_created_on'] = get_db_datetime();
                //if($this->my_blog_model->add_bog($arr,$data['blogdata'][0]['s_title']))

                if ($this->my_blog_model->add_blog($arr)) {
                    //$return_arr    = array("result"=>true,"blog_title"=>$blog_title,"msg"=>"Blog successfully added","blog_id"=>$data['blogdata'][0]['blogid']);
                    $msg = "Blog successfully added";
                    echo json_encode(array('msg' => $msg, 'result' => true));
                    exit;
                } else {
                    //$return_arr    = array("msg"=>"Some Error Occured");
                    $msg = "Some Error Occured";
                    echo json_encode(array('arr_messages' => $return_arr, 'result' => false));
                    exit;
                }
            }
        } else {
            echo json_encode(array('arr_messages' => $return_arr, 'result' => false));
            exit;
        }
    }

    function search_blog_by_title() { //-------------------- U N U S E D 
        $blog_id = $this->input->post('blog_id');
        $title = $this->input->post('title');

        $res = $this->my_blog_model->fetch_search_result_by_title($blog_id, $title);
    }

    public function add_blog_post() {
        $blogposttitle = $this->input->post("blogposttitle", true);
        $blogpostdesc = $this->input->post("blogpostdesc", true);
        $blogid = $this->input->post("blogid", true);

        $posted_by_user_id = $this->i_profile_id;



        $create_date = get_db_datetime();
          $ip = getenv("REMOTE_ADDR") ; 
        $arr = array("i_blog_id" => $blogid,
            "s_post_title" => $blogposttitle,
            "s_post_description" => $blogpostdesc,
            "dt_created_date" => $create_date,
            "i_user_id" => $posted_by_user_id,
            "u_ip" => $ip
            );
        //pr($arr,1);
        $err = array();
        if ($blogposttitle == '' || $blogposttitle == 'Type title here')
            $err['err_add_blogposttitle'] = "* Required Field.";
        if ($blogpostdesc == '' || $blogpostdesc == 'Max 500 Characters')
            $err['err_add_blogpostdesc'] = "* Required Field.";

        if (count($err) > 0) {
            $return_arr = array('result' => 'error', "msg" => $err);
            echo json_encode($return_arr);
            exit;
        } else {
            $is_abusive_article = 0;
            $is_abusive_desc = 0;
            $is_abusive_article = check_abusive_words($blogposttitle);
            $is_abusive_desc = check_abusive_words($blogpostdesc);
            if ($is_abusive_desc > 0 || $is_abusive_article > 0) {
                $return_arr = array('result' => 'abusive_error', "msg" => "Abusive words are not allowed");
                echo json_encode($return_arr);
                exit;
            } else {
                if ($this->my_blog_post_model->add_info($arr)) {
                    ob_start();
                    $this->generate_blog_post_listing_AJAX($blogid, 1);
                    $content = ob_get_contents();
                    $content_obj = json_decode($content);
                    $html = $content_obj->html;
                    $view_more = $content_obj->view_more;
                    $current_page = $content_obj->current_page;
                    ob_end_clean();


                    # success message...
                    $SUCCESS_MSG = "Article post added successfully";

                    echo json_encode(array('result' => 'success',
                        'content' => $html,
                        'msg' => $SUCCESS_MSG,
                        'view_more' => $view_more,
                        'current_page' => $current_page));
                    exit;
                } else {
                    $return_arr = array('result' => 'error', "msg" => "Some Error Occured");
                }
                echo json_encode($return_arr);
                exit;
            }
        }
    }

    public function edit_blog_post() {
        $blogposttitle = $this->input->post("edit_blogpost_title", true);
        $blogpostdesc = $this->input->post("edit_blogpost_desc", true);
        $post_id = $this->input->post("post_id", true);
        $blogid = $this->input->post("blogid", true);

        $err = array();
        if ($blogposttitle == '')
            $err['err_blogposttitle' . $post_id] = "* Required Field.";
        if ($blogpostdesc == '')
            $err['err_blogpostdesc' . $post_id] = "* Required Field.";

        if (count($err) > 0) {
            $return_arr = array('result' => 'error', "msg" => $err);
            echo json_encode($return_arr);
            exit;
        } else {
            $update_date = get_db_datetime();
            $arr = array("s_post_title" => $blogposttitle,
                "s_post_description" => $blogpostdesc,
                "dt_updated_date" => $update_date);
            //pr($arr);exit;
            $is_abusive_article = 0;
            $is_abusive_desc = 0;
            $is_abusive_article = check_abusive_words($blogposttitle);
            $is_abusive_desc = check_abusive_words($blogpostdesc);
            if ($is_abusive_desc > 0 || $is_abusive_article > 0) {
                $return_arr = array('result' => 'abusive_error', "msg" => "Abusive words are not allowed");
                echo json_encode($return_arr);
                exit;
            } else {
                if ($this->my_blog_post_model->edit_info($arr, $post_id)) {
                    ob_start();
                    $content = $this->generate_blog_post_listing_AJAX($blogid);
                    ob_end_clean();

                    # success message...
                    $SUCCESS_MSG = "Article post updated successfully";

                    echo json_encode(array('result' => 'success',
                        'content' => $content,
                        'msg' => $SUCCESS_MSG));
                    exit;
                } else {
                    $return_arr = array('result' => 'error', "msg" => "Some Error Occured");
                }
                echo json_encode($return_arr);
                exit;
            }
        }
    }

    public function generate_blog_post_listing_AJAX($blogid, $paging = 0, $page = 0) {


        $where = '';
        if ($this->input->post('if_posted')) {
            $blog_id = $this->input->post('blog_id');
            $title = $this->input->post('title');
            if ($title != '') {

                $where = "AND s_post_title LIKE '%{$title}%'";
            }

            $WHERE_COND = " WHERE  p.i_blog_id='" . $blog_id . "' ";
            $this->session->set_userdata('where', $where);
        } else {
            $WHERE_COND = " WHERE  p.i_blog_id='" . $blogid . "' ";
        }

        $WHERE_COND.=$this->session->userdata('where', $where);
        $result = $this->my_blog_post_model->fetch_multi($WHERE_COND, $page, $this->pagination_per_page, '');
        //pr($result);
        $resultCount = count($result);
        $total_rows = $this->my_blog_post_model->gettotal_info($WHERE_COND);
        $cur_page = $page + $this->pagination_per_page;



        //--- for check whether more videos are there or not
        $view_more = true;
        $rest_counter = $total_rows - $page;
        if ($rest_counter <= $this->pagination_per_page)
            $view_more = false;


        //--------- end check
        // getting auction-category listing...
        $data['blog_post_arr'] = $result;
        $data['no_of_blogpost'] = $total_rows;
        $data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;

        //dump($data['agenda_category_arr']);
        # loading the view-part...
        $AJAX_VIEW_FILE = 'logged/blogs/ajax_blog/ajax_listing_blog_post.phtml';
        $listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);


        //pr($result);
        if ($paging == 1) {
            if (is_array($result) && count($result))
                $Content = $listingContent;
            else
                $Content = "";
            echo json_encode(array('html' => $Content, 'current_page' => $cur_page, 'view_more' => $view_more));
        } else
            return $listingContent;
    }

    /*     * **************************CMMENTS******************************** */
    /*     * ***************************************************************** */
    /*     * ***************************************************************** */

    public function fetch_comment_on_post($i_blog_post_id = '') {
        try {
            $data = $this->data;

            ob_start();
            $this->comments_ajax_pagination($i_blog_post_id);
            $data['comments_list'] = ob_get_contents();
            ob_end_clean();

            $VIEW = "logged/blogs/ajax_blog/comments/my_post_view_comments_lightbox.phtml";
            #parent::_render($data, $VIEW); 
            $html = $this->load->view($VIEW, $data, true);
            echo json_encode(array('result' => success, 'html_data' => $html));
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function comments_ajax_pagination($i_blog_post_id, $page = 0) {
        try {
            $data = $this->data;
            $wh = " WHERE c.i_blog_post_id='" . $i_blog_post_id . "'";
            $result = $this->my_blog_post_model->show_all_comments($wh, $page, $this->pagination_per_page);
            $resultCount = count($result);
            $total_rows = $this->my_blog_post_model->gettotal_comments($wh);
            //pr($result); 		

            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url() . "logged/my_blog/comments_ajax_pagination/{$i_blog_post_id}";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->pagination_per_page;
            $config['uri_segment'] = 5;
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



            $config['div'] = '#view_comments'; /* Here #content is the CSS selector for target DIV */
            #$config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
            #$config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */


            $this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();


            $data['result_arr'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $page;
            $data['total_pages'] = ceil($total_rows / $this->pagination_per_page);

            $p = ($page / $this->pagination_per_page);
            $data['current_loaded_page_no'] = $p + 1;

            # rendering the view file...
            $VIEW_FILE = "logged/blogs/ajax_blog/comments/my_post_view_comments_lightbox_ajax.phtml";
            $this->load->view($VIEW_FILE, $data);
            //return $html;
        } catch (Exception $err_obj) {
            
        }
    }

    public function add_blog_post_cmnts() {
        $postcmnts_postid = $this->input->post("postcmnts_postid", true);
        $postcmnts_txt = $this->input->post("postcmnts_txt", true);
        $blog_id = $this->input->post('comment_blog_id', true);

        $err = array();
        if ($postcmnts_txt == '')
            $err['err_postcmnts_txt'] = "* Required Field.";

        if (count($err) > 0) {
            $return_arr = array('result' => 'error', "msg" => "Please enter some text");
            echo json_encode($return_arr);
            exit;
        } else {
            $cr_date = get_db_datetime();
              $ip = getenv("REMOTE_ADDR") ;
            $arr = array("i_blog_post_id" => $postcmnts_postid,
                "i_blog_id" => $blog_id,
                "i_user_id" => intval(decrypt($this->session->userdata('user_id'))),
                "s_comments" => $postcmnts_txt,
                "dt_posted_date" => $cr_date,
                "u_ip" => $ip
                );

            //pr($arr);exit;
            $is_abusive_comment = 0;
            $is_abusive_comment = check_abusive_words($postcmnts_txt);
            if ($is_abusive_comment > 0) {
                $return_arr = array('result' => 'abusive_error', "msg" => "Abusive Words are not allowed");
                echo json_encode($return_arr);
                exit;
            } else {
                $i_blog_post_comment_id = $this->my_blog_post_model->add_post_cmnts($arr);
                if ($i_blog_post_comment_id) {
                    $wh = " WHERE c.i_blog_post_id='" . $postcmnts_postid . "'";
                    $no_of_cmnts = $this->my_blog_post_model->gettotal_comments($wh);

                    ### adding notification. ####
                    #fetching blog ownwer id ####
                    $blog_detail_arr = array();

                    $blog_detail_arr = $this->my_blog_model->get_by_id($blog_id);
                    #fetching blog ownwer id ####
                    //pr($blog_detail_arr);


                    $notificaion_opt = $this->user_alert_model->check_option_user_id($this->i_profile_id);
                    //pr($notificaion_opt);
                    $notification_arr = array();
                    ## insert noifications ####
                    if (($notificaion_opt['e_blog_comments_received'] == 'Y') && ($blog_detail_arr['i_user_id'] != $this->i_profile_id)) {

                        $notification_arr['i_requester_id'] = $this->i_profile_id;
                        $notification_arr['i_accepter_id'] = $blog_detail_arr['i_user_id'];
                        $notification_arr['s_type'] = 'blog_comment';
                        $notification_arr['dt_created_on'] = get_db_datetime();


                        $ret = $this->user_notifications_model->insert($notification_arr);
                        $message_id = parent::social_notifications_message($notification_arr['i_requester_id'], $notification_arr['i_accepter_id'], 'blog_comment', $postcmnts_postid);
                    }
					$this->load->model('user_alert_model');
					if($blog_detail_arr['i_user_id'] != $this->i_profile_id)
				{
				$email_opt = $this->user_alert_model->check_option_email_user_id($this->i_profile_id);
						if($email_opt['e_blog_comments_received'] == 'Y'){
						
						$mail_arr['i_requester_id'] =get_username_by_id( $this->i_profile_id);
						$mail_arr['i_accepter_id'] =  get_username_by_id($blog_detail_arr['i_user_id']);
						$mail_arr['s_type'] = 'e_blog_comments_received';
					
						$mail_id=get_useremail_by_id($blog_detail_arr['i_user_id']);
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

					$this->email->subject($mail_arr["i_requester_id"].' has commented on your Blog');
					$this->email->message("$body");

					$this->email->send();
					}
				}
                    //$wh	= " WHERE c.i_blog_id='".$i_blog_id."'" ;
                    $total_rows = $this->my_blog_model->get_total_comments_by_blog_id($blog_id);
                    //pr($total_rows,1);
                    ### adding notification. ####
                    # success message...
                    $SUCCESS_MSG = "Comment post successfully";

                    echo json_encode(array('result' => 'success', 'msg' => $SUCCESS_MSG, 'html' => $no_of_cmnts, 'blog' => $postcmnts_postid, 'comm' => $total_rows));
                    exit;
                } else {
                    $return_arr = array('result' => 'error', "msg" => "Some Error Occurred");
                }
                echo json_encode($return_arr);
                exit;
            }
        }
    }

    /*     * **************************END OF CMMENTS************************* */
    /*     * ***************************************************************** */
    /*     * ***************************************************************** */

    //================================= delete article ===============================
    function delete_article() {
        $article_id = $this->input->post('article_id');
        $res = $this->my_blog_post_model->delete_by_id($article_id);
        $wh = " WHERE c.i_user_id='" . $this->i_profile_id . "'  AND c.i_user_id=u.id ";
        $data['blogdata'] = $this->my_blog_model->fetch_multi($wh, '', '', '');
        $blogid = $data['blogdata'][0]['blogid'];
        ob_start();
        $this->generate_blog_post_listing_AJAX($blogid, 1);
        $content = ob_get_contents();
        $content_obj = json_decode($content);
        $html = $content_obj->html;
        $view_more = $content_obj->view_more;
        $current_page = $content_obj->current_page;
        ob_end_clean();

        # success message...
        $SUCCESS_MSG = "Article deleted successfully";

        echo json_encode(array('result' => 'success',
            'html' => $html,
            'msg' => $SUCCESS_MSG,
            'view_more' => $view_more,
            'current_page' => $current_page));
    }

    function delete_blog() {
        $blog_id = $this->input->post('blog_id');

        $res = $this->my_blog_post_model->delete_blog_data_by_id($blog_id);
        $SUCCESS_MSG = "Blog deleted successfully";
        echo json_encode(array('result' => 'success',
            'msg' => $SUCCESS_MSG
        ));
    }

    //================================= end of delete article ===============================



    /*     * **************************Search blogs*************************** */
    /*     * ***************************************************************** */
    /*     * ***************************************************************** */

    function search_blogs() {
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
                'js/stepcarousel.js',*/'js/production/tweet_utilities.js'
            ));

//            parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
//                'css/dd.css'));



            $data['search_pagination_per_page'] = $this->search_pagination_per_page;

            $this->session->set_userdata('where', '');
            $this->session->set_userdata('is_post', '');
            $this->session->set_userdata('WHERE_ARTICLE_COND', '');
            $this->session->set_userdata('s_query_type', '');
            //print_r($data['blogdata']);
            # view file...
            ob_start();
            $content = $this->generate_search_blog_listing_AJAX();
            ob_end_clean();
            $data['listingContent'] = $content;


            $VIEW = "logged/blogs/search_blog.phtml";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

    public function generate_search_blog_listing_AJAX($paging = 0, $page = 0) {
        $WHERE_COND = ""; //" WHERE  p.i_blog_id='".$blogid."' ";
        $WHERE_ARTICLE_COND = "";
        $is_post = 0;
        if ($_POST) {

            $blog_title = trim($this->input->post('title'));
            $rd_type = trim($this->input->post('rd_type'));
            $blog_posted_by = trim($this->input->post('posted_by'));

            if ($blog_title != '') {
                if (strlen($blog_title) < 3)
                    $arr_messages['blog_title'] = 'Please provide alteast three letters keyword.';
            }

            if (count($arr_messages) == 0) {
                /* if($blog_title!='')
                  {
                  $WHERE_COND.=" AND `s_title` LIKE '%{$blog_title}%'";
                  }
                  if($blog_desc!='')
                  {
                  $WHERE_COND.=" AND `s_description` LIKE '{$blog_desc}%'";
                  } */

                #### search criteria  ####
                if ($blog_posted_by != '') {
                    $WHERE_COND.=" AND CONCAT(`s_first_name`,' ',`s_last_name`) LIKE '%{$blog_posted_by}%'";
                }

                if ($rd_type == 1) {
                    $s_query_type = 'both';

                    if ($blog_posted_by != '') {
                        $WHERE_COND.=" AND CONCAT(`s_first_name`,' ',`s_last_name`) LIKE '%{$blog_posted_by}%'";
                    }

                    if ($blog_title != '') {
                        $WHERE_COND.=" AND (B.`s_title` LIKE '%{$blog_title}%' 
												OR B.`s_description` LIKE '%{$blog_title}%'
												) ";
                    }

                    if ($blog_posted_by != '') {
                        $WHERE_COND.=" AND CONCAT(`s_first_name`,' ',`s_last_name`) LIKE '%{$blog_posted_by}%'";
                    }


                    #### articles 

                    if ($blog_posted_by != '') {
                        $WHERE_ARTICLE_COND.=" AND CONCAT(`s_first_name`,' ',`s_last_name`) LIKE '%{$blog_posted_by}%'";
                    }

                    if ($blog_title != '') {
                        $WHERE_ARTICLE_COND.=" AND (
												 BP.`s_post_title` LIKE '%{$blog_title}%'
												OR BP.`s_post_description` LIKE '%{$blog_title}%') ";
                    }

                    if ($blog_posted_by != '') {
                        $WHERE_ARTICLE_COND.=" AND CONCAT(`s_first_name`,' ',`s_last_name`) LIKE '%{$blog_posted_by}%'";
                    }
                } else if ($rd_type == 2) {
                    $s_query_type = 'article';
                    if ($blog_title != '') {
                        $WHERE_COND.=" AND (  BP.`s_post_title` LIKE '%{$blog_title}%'
												  OR BP.`s_post_description` LIKE '%{$blog_title}%') ";
                    }
                } else if ($rd_type == 3) {
                    $s_query_type = 'blog';
                    if ($blog_title != '') {
                        $WHERE_COND.=" AND (B.`s_title` LIKE '%{$blog_title}%' 
												OR B.`s_description` LIKE '%{$blog_title}%') ";
                    }
                }

                #### search criteria  ####


                if ($blog_posted_by != '') {
                    $WHERE_COND.=" AND CONCAT(`s_first_name`,' ',`s_last_name`) LIKE '%{$blog_posted_by}%'";
                }



                $this->session->set_userdata('where', $WHERE_COND);
                $this->session->set_userdata('WHERE_ARTICLE_COND', $WHERE_ARTICLE_COND);
                $this->session->set_userdata('s_query_type', $s_query_type);
                $is_post = 1;
                $this->session->set_userdata('is_post', $is_post);
            } else {
                echo json_encode(array('status' => 'failure', 'html' => '', 'blog_title' => $arr_messages['blog_title']));
                exit;
            }
        }   // end if post

        $WHERE_COND = $this->session->userdata('where');
        $WHERE_ARTICLE_COND = $this->session->userdata('WHERE_ARTICLE_COND');

        $s_query_type = $this->session->userdata('s_query_type');

        $is_post = $this->session->userdata('is_post');
        if ($WHERE_COND != '') {




            //$result = $this->my_blog_post_model->fetch_multi($WHERE_COND, $page, $this->search_pagination_per_page,'');
            $result = $this->my_blog_model->get_blog_search_result($WHERE_COND, $page, $this->search_pagination_per_page, $s_query_type, $WHERE_ARTICLE_COND);
            //pr($result,1);
            $resultCount = count($result);
            $total_rows = $this->my_blog_model->get_total_blog_search_result($WHERE_COND, $s_query_type, $WHERE_ARTICLE_COND);
            $cur_page = $page + $this->search_pagination_per_page;
            // getting auction-category listing...
            $data['blog_arr'] = $result;
            $data['no_of_blogpost'] = $total_rows;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $cur_page;
            $data['is_post'] = $is_post;

            //dump($data['agenda_category_arr']);



            $view_more = true;
            $rest_counter = $total_rows - $page;
            if ($rest_counter <= $this->search_pagination_per_page)
                $view_more = false;
            //--------- end check
            # loading the view-part...
            $AJAX_VIEW_FILE = 'logged/blogs/ajax_blog/ajax_listing_blog_search.phtml';
            $listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);
            // pr($result,1);
        }   // endif where blank


        if (is_array($result) && count($result))
            $Content = $listingContent;
        else
            $Content = "";

        echo json_encode(array('status' => 'success', 'html' => $Content, 'current_page' => $cur_page, 'view_more' => $view_more));
    }

    /*     * **************************End Search blogs*********************** */
    /*     * ***************************************************************** */
    /*     * ***************************************************************** */


    /*     * **************************Most popular blogs*********************** */
    /*     * ***************************************************************** */
    /*     * ***************************************************************** */

    function most_popular_blogs() {
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
                'js/stepcarousel.js',*/'js/production/tweet_utilities.js'
            ));

//            parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
//                'css/dd.css'));


            //print_r($data['blogdata']);


            $data['popular_blogs'] = $this->my_blog_model->get_search_list_popular_blog($WHERE_COND, $page, 10, '');
            /* # view file...
              ob_start();
              $content = $this->generate_popular_blog_listing_AJAX();
              ob_end_clean();
              $data['popularListingContent'] = $content;
             */


            $VIEW = "logged/blogs/most_popular_blogs.phtml";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

    /*     * **************************End most popular blogs*********************** */
    /*     * ***************************************************************** */
    /*     * ***************************************************************** */

    /*     * ************************** blog detail*********************** */
    /*     * ***************************************************************** */
    /*     * ***************************************************************** */

    function blog_detail($blog_id) {
        //echo "blog id : ".$blog_id;

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
                'js/stepcarousel.js',*/'js/production/tweet_utilities.js'
            ));

//            parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
//                'css/dd.css'));


            $wh = " WHERE c.id='" . $blog_id . "'  AND c.i_user_id=u.id ";
            $data['blogdata'] = $this->my_blog_model->fetch_multi($wh, '', '', '');

            //----------------- update view count -------------------
            $this->my_blog_model->update_view_count_by_blog_id($blog_id);

            $data['pagination_per_page'] = $this->pagination_per_page;
            $data['blog_id'] = $data['blogdata'][0]['blogid'];
            //pr($data['blogdata']);

            $this->session->set_userdata('where', '');

            # view file...
            ob_start();
            $data['listingContent'] = $this->blog_detail_AJAX($data['blogdata'][0]['blogid']);
            ob_end_clean();
            // $data['listingContent'] = $content;
            $VIEW = "logged/blogs/blog_detail.phtml";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

    function blog_detail_AJAX($blogid, $paging = 0, $page = 0) {


        $where = '';
        if ($this->input->post('if_posted')) {
            $blog_id = $this->input->post('blog_id');
            $title = $this->input->post('title');
            if ($title != '') {

                $where = "AND s_post_title LIKE '%{$title}%'";
            }

            $WHERE_COND = " WHERE  p.i_blog_id='" . $blog_id . "' ";
            $this->session->set_userdata('where', $where);
        } else {
            $WHERE_COND = " WHERE  p.i_blog_id='" . $blogid . "' ";
        }

        $WHERE_COND.=$this->session->userdata('where', $where);
        $result = $this->my_blog_post_model->fetch_multi($WHERE_COND, $page, $this->pagination_per_page, '');
        // pr($result); 
        $resultCount = count($result);
        $total_rows = $this->my_blog_post_model->gettotal_info($WHERE_COND);
        $cur_page = $page + $this->pagination_per_page;



        //--- for check whether more or not
        $view_more = true;
        $rest_counter = $total_rows - $page;
        if ($rest_counter <= $this->pagination_per_page)
            $view_more = false;


        //--------- end check
        // getting auction-category listing...
        $data['blog_post_arr'] = $result;
        $data['no_of_blogpost'] = $total_rows;
        $data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;

        //dump($data['agenda_category_arr']);
        # loading the view-part...
        $AJAX_VIEW_FILE = 'logged/blogs/ajax_blog/blog_detail_ajax.phtml';
        $listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);


        //pr($result);
        if ($paging == 1) {
            if (is_array($result) && count($result))
                $Content = $listingContent;
            else
                $Content = "";
            echo json_encode(array('html' => $Content, 'current_page' => $cur_page, 'view_more' => $view_more));
        } else
            return $listingContent;
    }

    public function add_blog_post_detail() {
        $blogposttitle = $this->input->post("blogposttitle", true);
        $blogpostdesc = $this->input->post("blogpostdesc", true);
        $blogid = $this->input->post("blogid", true);

        $posted_by_user_id = $this->i_profile_id;



        $create_date = get_db_datetime();

        $arr = array("i_blog_id" => $blogid,
            "s_post_title" => $blogposttitle,
            "s_post_description" => $blogpostdesc,
            "dt_created_date" => $create_date,
            "i_user_id" => $posted_by_user_id);
        //pr($arr,1);
        $err = array();
        if ($blogposttitle == '' || $blogposttitle == 'Type title here')
            $err['err_add_blogposttitle'] = "* Required Field.";
        if ($blogpostdesc == '' || $blogpostdesc == 'Max 500 Characters')
            $err['err_add_blogpostdesc'] = "* Required Field.";

        if (count($err) > 0) {
            $return_arr = array('result' => 'error', "msg" => $err);
            echo json_encode($return_arr);
            exit;
        } else {
            if ($this->my_blog_post_model->add_info($arr)) {
                ob_start();
                $this->blog_detail_AJAX($blogid, 1);
                $content = ob_get_contents();
                $content_obj = json_decode($content);
                $html = $content_obj->html;
                $view_more = $content_obj->view_more;
                $current_page = $content_obj->current_page;
                ob_end_clean();

                # success message...
                $SUCCESS_MSG = "Article post added successfully";

                echo json_encode(array('result' => 'success',
                    'content' => $html,
                    'msg' => $SUCCESS_MSG,
                    'view_more' => $view_more,
                    'current_page' => $current_page));
                exit;
            } else {
                $return_arr = array('result' => 'error', "msg" => "Some Error Occured");
            }
            echo json_encode($return_arr);
            exit;
        }
    }

    public function edit_blog_post_detail() {
        $blogposttitle = $this->input->post("edit_blogpost_title", true);
        $blogpostdesc = $this->input->post("edit_blogpost_desc", true);
        $post_id = $this->input->post("post_id", true);
        $blogid = $this->input->post("blogid", true);

        $err = array();
        if ($blogposttitle == '')
            $err['err_blogposttitle'] = "* Required Field.";
        if ($blogpostdesc == '')
            $err['err_blogpostdesc'] = "* Required Field.";

        if (count($err) > 0) {
            $return_arr = array('result' => 'error', "msg" => $err);
            echo json_encode($return_arr);
            exit;
        } else {


            $update_date = get_db_datetime();

            $arr = array("s_post_title" => $blogposttitle,
                "s_post_description" => $blogpostdesc,
                "dt_updated_date" => $update_date);

            //pr($arr);exit;
            if ($this->my_blog_post_model->edit_info($arr, $post_id)) {
                ob_start();
                $content = $this->blog_detail_AJAX($blogid);
                ob_end_clean();

                # success message...
                $SUCCESS_MSG = "Article post updated successfully";

                echo json_encode(array('result' => 'success',
                    'content' => $content,
                    'msg' => $SUCCESS_MSG));
                exit;
            } else {
                $return_arr = array('result' => 'error', "msg" => "Some Error Occured");
            }
            echo json_encode($return_arr);
            exit;
        }
    }

    /*     * **************************End blog detail*********************** */
    /*     * ***************************************************************** */
    /*     * ***************************************************************** */

    //====================================== all blogs =======================================

    function all_blogs() {
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
                'js/stepcarousel.js',*/'js/production/tweet_utilities.js'
            ));

//            parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
//                'css/dd.css'));



            # view file...
            ob_start();
            //$content = $this->generate_all_blog_listing_AJAX();

            $this->generate_all_blog_listing_AJAX();
            $content = ob_get_contents();
            $content_obj = json_decode($content);
            $data['all_blogs'] = $content_obj->html;
            $data['view_more'] = $content_obj->view_more;
            ob_end_clean();
            //$data['all_blogs'] = $content;



            $VIEW = "logged/blogs/all_blogs.phtml";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

    public function generate_all_blog_listing_AJAX($paging = 0, $page = 0) {
        $WHERE_COND = ""; //" WHERE  p.i_blog_id='".$blogid."' ";


        $cur_page = $page + $this->all_pagination_per_page;
        #echo $this->all_pagination_per_page;
        $result = $this->my_blog_model->get_search_list_all_blogs($WHERE_COND, $page, $this->all_pagination_per_page, '');
        //pr($result,1);
        #exit;
        $resultCount = count($result);
        $where_blogs = " WHERE i_isenabled = 1";
        $total_rows = $this->my_blog_model->get_total_all_blogs($where_blogs);

        //$cur_page = $page + $this->popular_pagination_per_page;
        //--- for check whether more are there or not
        $view_more = true;
        $rest_counter = $total_rows - $page;
        if ($rest_counter <= $this->all_pagination_per_page)
            $view_more = false;

        //--------- end check
        // getting auction-category listing...
        $data['all_blogs_arr'] = $result;
        $data['no_of_blogpost'] = $total_rows;
        $data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;

        //dump($data['agenda_category_arr']);
        # loading the view-part...
        $AJAX_VIEW_FILE = 'logged/blogs/ajax_blog/ajax_listing_all_blogs.phtml';
        //$listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);


        if (is_array($result) && count($result))
            $Content = $this->load->view($AJAX_VIEW_FILE, $data, true);
        else
            $Content = '<div class="section01" style="padding-top:5px;"><div class="shade_norecords" style="width:260px;">
<p class="blue_bold12">No Blogs.</p></div></div>';

        echo json_encode(array('html' => $Content, 'current_page' => $cur_page, 'view_more' => $view_more));
    }

    //===================================== \ all blogs =======================================
    ########### NEW FETCH COMMENTS ON WALL METHOD ###########

    public function NEW_fetch_comment_blogPost($i_blog_post_id = '') {
        try {
            $data = $this->data;
            $html = '';
            $wh = " WHERE c.i_blog_post_id='" . $i_blog_post_id . "'";
            $result = $this->my_blog_post_model->show_all_comments($wh);


            if (count($result)) {

                foreach ($result as $key => $val) {

                    $profile_image_filename = get_profile_image_of_user('thumb',$val->s_profile_photo,$val->e_gender);
                    $DESC = html_entity_decode(htmlspecialchars_decode($val->s_comments), ENT_QUOTES, 'utf-8');
                    $profile_link = get_profile_url($val->i_user_id, $val->s_profile_name);

                    $html .= '<div class="txt_content01 comments-number-content"> 
					 			<a href="' . $profile_link . '"><div style="background:url(' . $profile_image_filename . ') no-repeat center;width:60px; height:60px;" class="pro_photo" ></div></a>
									<div class="left-nw-wal">
										  <p class="blue_bold12"><a href="javascript:void(0);">' . $val->s_profile_name . '</a></p>
										  <p>' . nl2br($DESC) . '</p>
											 <p class="read-more">Updated on: ' . get_time_elapsed($val->dt_posted_date) . '</p>
									</div>
									<div class="clr"></div>
							  </div>';
                }
            } else {
                $html .= '     <div class="txt_content01 comments-number-content" style="width:475px !important;"> 
										<div style="text-align:center;"><p>No Comments.</p></div>
										</div>
										';
            }

            echo json_encode(array('result' => 'success', 'html_data' => $html));
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    function minister_blog_detail($blog_id) {
        //echo "blog id : ".$blog_id;

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
                'js/stepcarousel.js',*/'js/production/tweet_utilities.js'
            ));

//            parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
//                'css/dd.css'));


            $wh = " WHERE c.id='" . $blog_id . "'  AND c.i_user_id=u.id ";
            $data['blogdata'] = $this->my_blog_model->fetch_multi($wh, '', '', '');

            //----------------- update view count -------------------
            $this->my_blog_model->update_view_count_by_blog_id($blog_id);

            $data['pagination_per_page'] = $this->pagination_per_page;
            $data['blog_id'] = $data['blogdata'][0]['blogid'];
            //pr($data['blogdata']);

            $this->session->set_userdata('where', '');

            # view file...
            ob_start();
            $data['listingContent'] = $this->minister_blog_detail_AJAX($data['blogdata'][0]['blogid']);
            ob_end_clean();
            // $data['listingContent'] = $content;
            $VIEW = "logged/blogs/minister_blog_detail.phtml";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

    function minister_blog_detail_AJAX($blogid, $paging = 0, $page = 0) {


        $where = '';
        if ($this->input->post('if_posted')) {
            $blog_id = $this->input->post('blog_id');
            $title = $this->input->post('title');
            if ($title != '') {

                $where = "AND s_post_title LIKE '%{$title}%'";
            }

            $WHERE_COND = " WHERE  p.i_blog_id='" . $blog_id . "' ";
            $this->session->set_userdata('where', $where);
        } else {
            $WHERE_COND = " WHERE  p.i_blog_id='" . $blogid . "' ";
        }

        $WHERE_COND.=$this->session->userdata('where', $where);
        $result = $this->my_blog_post_model->fetch_multi($WHERE_COND, $page, $this->pagination_per_page, '');
        // pr($result); 
        $resultCount = count($result);
        $total_rows = $this->my_blog_post_model->gettotal_info($WHERE_COND);
        $cur_page = $page + $this->pagination_per_page;



        //--- for check whether more or not
        $view_more = true;
        $rest_counter = $total_rows - $page;
        if ($rest_counter <= $this->pagination_per_page)
            $view_more = false;


        //--------- end check
        // getting auction-category listing...
        $data['blog_post_arr'] = $result;
        $data['no_of_blogpost'] = $total_rows;
        $data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;

        //dump($data['agenda_category_arr']);
        # loading the view-part...
        $AJAX_VIEW_FILE = 'logged/blogs/ajax_blog/blog_detail_ajax.phtml';
        $listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);


        //pr($result);
        if ($paging == 1) {
            if (is_array($result) && count($result))
                $Content = $listingContent;
            else
                $Content = "";
            echo json_encode(array('html' => $Content, 'current_page' => $cur_page, 'view_more' => $view_more));
        } else
            return $listingContent;
    }

}

// end of controller...

