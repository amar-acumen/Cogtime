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

class My_events extends Base_controller {

    private $pagination_per_page = 5;
    private $comments_pagination_per_page = 10;

    public function __construct() {
        try {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...
            $this->load->model('users_model');
            $this->load->model('contacts_model');
            $this->load->model('tweet_model');
            $this->load->model('netpals_model');

            $this->load->model('events_model');
            $this->load->model('events_user_invited_model');
            $this->load->model('events_email_invited_model');
            $this->load->model('events_comments_model');
            $this->load->model('events_feedback_model');
            $this->load->model("user_notifications_model");
            $this->load->model("user_alert_model");
            $this->load->helper('wall_helper');
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


            parent::_add_js_arr(array(/*'js/ddsmoothmenu.js',
                'js/switch.js', 'js/animate-collapse.js',
                'js/lightbox.js', 'js/jquery-ui-1.8.2.custom.min.js',
                'js/stepcarousel.js',*/
                'js/production/events_helper.js',
                'js/production/tweet_utilities.js',
            ));

//            parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
//                'css/dd.css'));
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));

            $this->session->set_userdata('search_condition', '');

            $data['pagination_per_page'] = $this->pagination_per_page;
            $data['profile_id'] = $i_user_id;

            ob_start();
            $this->all_events_ajax_pagination($i_user_id);
            $content = ob_get_contents();
            $content_obj = json_decode($content);
            $data['events_ajax_content'] = $content_obj->html;
			
            $data['no_of_result'] = $content_obj->no_of_result;
            ob_end_clean();


            # view file...
            $VIEW = "logged/my_events/my-events.phtml";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

    public function all_events_ajax_pagination($i_user_id, $page = 0) {

        //echo $page;
        $s_where = '';
        $cur_page = $page + $this->pagination_per_page;
        $data = $this->data;
        $cur_time = date('Y-m-d');
        $s_where = " AND e.dt_end_time >= '" . $cur_time . "'";
        $result = $this->events_model->get_my_events($i_user_id, $s_where, intval($page), $this->pagination_per_page);
        //echo $this->db->last_query();
        $total_rows = $this->events_model->get_total_my_events($i_user_id, $s_where);
        //pr($result,1);
        $data['arr_events'] = $result;
        $data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
        $data['profile_id'] = $i_user_id;

        //--- for check end of he page.
        $view_more = true;
        $rest_counter = $total_rows - $page;
        if ($rest_counter <= $this->pagination_per_page)
            $view_more = false;
        //--------- end check

		//pr($data,1);
        $VIEW_FILE = "logged/my_events/my_events_ajax_new.phtml";

        if (is_array($result) && count($result)) {
            $content = $this->load->view($VIEW_FILE, $data, true);
             parent::_render($data, $VIEW_FILE);
        } else {
            $content = '';
        }

        //echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode(array('html' => $content, 'current_page' => $cur_page, 'no_of_result' => $data['no_of_result'], 'total' => $total_rows, 'view_more' => $view_more, 'cur_page' => $data['current_page_1']));
    }

    public function events_invitations_recieved() {
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
                'js/lightbox.js', 'js/jquery-ui-1.8.2.custom.min.js',
                'js/stepcarousel.js',*/
                'js/production/events_helper.js','js/production/tweet_utilities.js'
            ));

            parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
                'css/dd.css'));
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
            $this->session->set_userdata('search_condition', '');
            $data['pagination_per_page'] = $this->pagination_per_page;
            $data['profile_id'] = $i_user_id;

            ob_start();
            $this->events_invitations_recieved_ajax_pagination($i_user_id);
            $content = ob_get_contents();
            $content_obj = json_decode($content);
            $data['events_ajax_content'] = $content_obj->html;
            $data['no_of_result'] = $content_obj->no_of_result;
            ob_end_clean();


            # view file...
            $VIEW = "logged/my_events/events_invitation_recieved.phtml";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

    public function events_invitations_recieved_ajax_pagination($i_user_id, $page = 0) {

        //echo $page;
        $s_where = '';
        $cur_page = $page + $this->pagination_per_page;
        $data = $this->data;

        $result = $this->events_user_invited_model->get_events_invitation_recived($i_user_id, $s_where, intval($page), $this->pagination_per_page);
        //echo $this->db->last_query();
        $total_rows = $this->events_user_invited_model->get_total_events_invitation_recived($i_user_id, $s_where);
        //pr($result,1);
        $data['arr_events'] = $result;
        $data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
        $data['profile_id'] = $i_user_id;

        //--- for check end of he page.
        $view_more = true;
        $rest_counter = $total_rows - $page;
        if ($rest_counter <= $this->pagination_per_page)
            $view_more = false;
        //--------- end check


        $VIEW_FILE = "logged/my_events/events_invitation_recieved_ajax.phtml";

        if (is_array($result) && count($result)) {
            $content = $this->load->view($VIEW_FILE, $data, true);
        } else {
            $content = '';
        }

        //echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode(array('html' => $content, 'current_page' => $cur_page, 'no_of_result' => $data['no_of_result'], 'total' => $total_rows, 'view_more' => $view_more, 'cur_page' => $data['current_page_1']));
    }

    ## post comments  ##

    public function post_comment($feed_id) {


        $this->load->model('users_model');
        $user_id = intval(decrypt($this->session->userdata('user_id')));
        $user_details = $this->users_model->fetch_this($user_id);

        $message = nl2br(htmlspecialchars(trim($this->input->post('message')), ENT_QUOTES, 'utf-8'));
        $_html = '';
        if ($message != '') {
            $ip = getenv("REMOTE_ADDR") ; 
            $arr['i_event_id'] = $feed_id;
            $arr['i_user_id'] = $user_id;
            $arr['s_contents'] = $message;
            $arr['dt_created_on'] = get_db_datetime();
              $arr['u_ip'] = $ip;
            $is_abusive_commnt = check_abusive_words($message);
            if ($is_abusive_commnt > 0) {
                echo json_encode(array('success' => 'false', 'msg' => "Abusive words are not allowed", 'html' => $_html));
            } else {
                $this->events_comments_model->insert($arr);
                $arr['pseudo'] = $user_details['s_profile_name'];
                $data['comment'] = $arr;
                ### RING POST COMMENTS  NOTIFICATIONS ####
                $event_members_arr = array();
                $event_members = $this->events_model->get_all_event_members_by_event_id($feed_id);
                if (count($event_members)) {
                    foreach ($event_members as $arr) {
                        $arr_invited_id[] = $arr['post_owner_user_id'];
                    }
                }

                #### FETCHING RING OWNER ID AND DETAILS

                $event_detail_arr = $this->events_model->get_by_id($feed_id);
                array_push($arr_invited_id, $event_detail_arr['i_host_id']);

                $event_members_arr = $arr_invited_id;
                //pr($event_members_arr); exit;
                //pr($event_detail_arr,1);
                ## check if opted for this notification or not ##
                if (count($event_members_arr)) {

                    foreach ($event_members_arr as $val) {

                        $notificaion_opt = $this->user_alert_model->check_option_user_id($val);
                        //pr($notificaion_opt);
                        $notification_arr = array();

                        ## insert noifications ####
                        if ($notificaion_opt['e_event_comments_received'] == 'Y' && $val != $user_id) {

                            $notification_arr['i_requester_id'] = $user_id;
                            $notification_arr['i_accepter_id'] = $val;
                            $notification_arr['s_type'] = 'event_comment';
                            $notification_arr['dt_created_on'] = get_db_datetime();


                            $ret = $this->user_notifications_model->insert($notification_arr);
                            $message_id = parent::social_notifications_message($notification_arr['i_requester_id'], $notification_arr['i_accepter_id'], 'event_comment', $feed_id);
                        }
					$email_opt = $this->user_alert_model->check_option_email_user_id($val);
						if($email_opt['e_event_comments_received'] == 'Y' && $val != $user_id){
						
						$mail_arr['i_requester_id'] =get_username_by_id( $user_id);
						$mail_arr['i_accepter_id'] =  get_username_by_id($val);
						$mail_arr['s_type'] = 'e_event_comments_received';
						$mail_arr['event_name']=get_event_title_by_id($feed_id);
						$mail_id=get_useremail_by_id($val);
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

					$this->email->subject($mail_arr["i_requester_id"].' has commented on a Event ');
					$this->email->message("$body");

					$this->email->send();
					}

                        ### end  ###
                    }
                }
                ### RING POST COMMENTS  NOTIFICATIONS ####
                $_html = '' . "Comments " . " (" . count_event_comment_link($feed_id) . ")";
                echo json_encode(array('success' => 'true', 'msg' => "Comment posted successfully.", 'html' => $_html));
            }
        } else {
            echo json_encode(array('success' => 'false', 'msg' => "Please enter some text.", 'html' => $_html));
        }
    }

    ## end post comments ##

    public function fetch_comment_on_events($i_media_id = '') {
        try {

            $data = $this->data;

            ob_start();
            $this->comments_ajax_pagination($i_media_id);
            $data['comments_list'] = ob_get_contents();
            ob_end_clean();

            $VIEW = "logged/my_events/comments/my_event_view_comments_lightbox.phtml";
            #parent::_render($data, $VIEW); 
            $html = $this->load->view($VIEW, $data, true);
            echo json_encode(array('result' => success, 'html_data' => $html));
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function comments_ajax_pagination($i_media_id, $page = 0) {
        try {
            $data = $this->data;
            $result = $this->events_comments_model->get_by_event_id($i_media_id, $page, $this->comments_pagination_per_page);
            $resultCount = count($result);
            $total_rows = $this->events_comments_model->get_total_by_event_id($i_media_id);
            //pr($result); 		

            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url() . "logged/my_events/comments_ajax_pagination/{$i_media_id}";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->comments_pagination_per_page;
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
            $data['total_pages'] = ceil($total_rows / $this->comments_pagination_per_page);

            $p = ($page / $this->comments_pagination_per_page);
            $data['current_loaded_page_no'] = $p + 1;

            # rendering the view file...
            $VIEW_FILE = "logged/my_events/comments/my_event_view_comments_lightbox_ajax.phtml";
            $this->load->view($VIEW_FILE, $data);
            //return $html;
        } catch (Exception $err_obj) {
            
        }
    }

    public function send_rsvp($i_event_id) {

        $i_user_id = intval(decrypt($this->session->userdata('user_id')));
        $this->session->set_userdata('search_condition', '');
        $data['pagination_per_page'] = $this->pagination_per_page;
        $data['profile_id'] = $i_user_id;

        ### insert  rsvp  and hide send rsvp option ###

        $info = array();
        $info['i_event_id'] = $i_event_id;
        $info['i_user_id'] = $i_user_id;
        $info['dt_created_on'] = get_db_datetime();

        $ret_id = $this->events_user_invited_model->insert_user_rsvp($info);

        ## update message table
        $event_owner_info = $this->events_model->get_owner_by_event_id($i_event_id);
        $i_requester_id = $i_user_id;
        $i_accepter_id = $event_owner_info['i_user_id'];

        $this->db->update('messages', array('i_ended' => '1'), array('s_type' => 'event_invitations_request', 'i_sender_id' => $i_accepter_id, 'i_receiver_id' => $i_requester_id));
        //echo $this->db->last_query();
        ### end                                      

		parent::social_notifications_message($i_user_id,$i_accepter_id,'event_rsvp_received',$i_event_id);

        echo json_encode(array('html' => $html, 'current_page' => $cur_page, 'no_of_result' => $no_of_result, 'total' => $total_rows, 'view_more' => $view_more, 'cur_page' => $data['current_page_1'], 'msg' => 'RSVP sent succesfully.'));
        exit;
    }

    public function send_feedback($i_event_id) {

        $i_user_id = intval(decrypt($this->session->userdata('user_id')));
        $data['profile_id'] = $i_user_id;

        ### insert  rsvp  and hide send rsvp option ###

        $info = array();
        $info['i_event_id'] = $i_event_id;
        $info['i_user_id'] = $i_user_id;
        $info['s_message'] = nl2br(htmlspecialchars(trim($this->input->post('message')), ENT_QUOTES, 'utf-8'));
        $info['dt_created_on'] = get_db_datetime();
        if (!empty($info['s_message'])) {
            $is_abusive_text = check_abusive_words($info['s_message']);
            if ($is_abusive_text > 0) {
                echo json_encode(array('result' => 'error', 'msg' => 'Abusive words are not allowed'));
            } else {
                $ret_id = $this->events_feedback_model->insert_feedback($info);
                echo json_encode(array('result' => 'success', 'msg' => 'Feedback sent succesfully.'));
            }
        } else {
            echo json_encode(array('result' => 'error', 'msg' => 'Please enter some text'));
        }
        exit;
    }

    public function delete_information($id) {
        $i_ret = $this->events_model->delete_by_id($id);
        $re_page = base_url() . "my-events.html";
        header("location:" . $re_page);
        exit;
    }

    ########### NEW FETCH COMMENTS ON WALL METHOD ###########

    public function NEW_fetch_comment_events($i_media_id = '') {
        try {
            $data = $this->data;
            $html = '';
            $result = $this->events_comments_model->get_by_event_id($i_media_id);
            #pr($result);
            if (count($result)) {

                foreach ($result as $key => $val) {

                    $profile_image_filename = get_profile_image_of_user('thumb',$val['s_profile_photo'],$val['e_gender']);
                    $DESC = html_entity_decode(htmlspecialchars_decode($val['s_contents']), ENT_QUOTES, 'utf-8');
                    $profile_link = get_profile_url($val['i_user_id'], $val['s_profile_name']);

                    $html .= '<div class="txt_content01 comments-number-content"> 
					 			<a href="' . $profile_link . '"><div style="background:url(' . $profile_image_filename . ') no-repeat center;width:60px; height:60px;" class="pro_photo" ></div></a>
									<div class="left-nw-wal">
										  <p class="blue_bold12"><a href="javascript:void(0);">' . $val['s_profile_name'] . '</a></p>
										  <p>' . nl2br($DESC) . '</p>
											 <p class="date-time">Updated on: ' . get_time_elapsed($val['dt_created_on']) . '</p>
									</div>
									<div class="clr"></div>
							  </div>';
                }
            } else {
                $html .= '<div class="txt_content01 comments-number-content" style="width:475px !important;"> 
										<div style="text-align:center;"><p>No Comments.</p></div>
										</div>
										';
            }

            echo json_encode(array('result' => 'success', 'html_data' => $html));
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function new_fetch_likes_on_events($i_media_id = '') {
        try {
            $data = $this->data;
            $html = '';
            $result = $this->ring_post_comments_model->get_people_liked_by_ring_post_id($i_media_id);

            //pr($result);

            if (count($result)) {
                foreach ($result as $key => $val) {

                    $name = $val['s_profile_name'];
                    $profile_image = get_profile_image_of_user('thumb',$val['s_profile_photo'],$val['e_gender']);

                    $profile_link = get_profile_url($val['i_user_id'], $val['s_profile_name']);

                    $html .= '     <div class="user_div dp-list-user"> 
											<a href="' . $profile_link . '">
											<div class="pro_photo3" style="background:url(' . $profile_image . ') no-repeat center;width:60px; height:60px;"></div></a> 
											<a href="javascript:void(0);" class="blue_link">' . $name . '</a> 
										</div>
										';
                }
                $html .= '<br class="clr" />';
            } else {
                $html .= '     <div class="user_div" style="width:475px !important;"> 
										<div style="text-align:center;"><p>No Likes.</p></div>
										</div>
										';
            }


            echo json_encode(array('result' => 'success', 'html_data' => $html));
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function archive_events() {
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
                'js/lightbox.js', 'js/jquery-ui-1.8.2.custom.min.js',
                'js/stepcarousel.js',*/
                'js/production/events_helper.js',
                'js/production/tweet_utilities.js',
            ));

//            parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
//                'css/dd.css'));
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));

            $this->session->set_userdata('search_condition', '');

            $data['pagination_per_page'] = $this->pagination_per_page;
            $data['profile_id'] = $i_user_id;

            ob_start();
            $this->archive_events_ajax_pagination($i_user_id);
            $content = ob_get_contents();
            $content_obj = json_decode($content);
            $data['events_ajax_content'] = $content_obj->html;
            $data['no_of_result'] = $content_obj->no_of_result;
            ob_end_clean();


            # view file...
            $VIEW = "logged/my_events/archive-events.phtml";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

    public function archive_events_ajax_pagination($i_user_id, $page = 0) {

        //echo $page;
        $s_where = '';
        $cur_page = $page + $this->pagination_per_page;
        $data = $this->data;
        $s_where = " AND e.dt_end_time < '" . date('Y-m-d') . "'";
        $result = $this->events_model->get_my_events($i_user_id, $s_where, intval($page), $this->pagination_per_page);
        //echo $this->db->last_query();
        $total_rows = $this->events_model->get_total_my_events($i_user_id, $s_where);
        //pr($result,1);
        $data['arr_events'] = $result;
        $data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
        $data['profile_id'] = $i_user_id;

        //--- for check end of he page.
        $view_more = true;
        $rest_counter = $total_rows - $page;
        if ($rest_counter <= $this->pagination_per_page)
            $view_more = false;
        //--------- end check


        $VIEW_FILE = "logged/my_events/my_events_ajax.phtml";

        if (is_array($result) && count($result)) {
            $content = $this->load->view($VIEW_FILE, $data, true);
        } else {
            $content = '';
        }

        //echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode(array('html' => $content, 'current_page' => $cur_page, 'no_of_result' => $data['no_of_result'], 'total' => $total_rows, 'view_more' => $view_more, 'cur_page' => $data['current_page_1']));
    }

    #### events rsvps

    public function events_rsvps_recieved() {
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
                'js/lightbox.js', 'js/jquery-ui-1.8.2.custom.min.js',
                'js/stepcarousel.js',*/
                'js/production/events_helper.js','js/production/tweet_utilities.js'
            ));

//            parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
//                'css/dd.css'));
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
            $this->session->set_userdata('search_condition', '');
            $data['pagination_per_page'] = $this->pagination_per_page;
            $data['profile_id'] = $i_user_id;

            ob_start();
            $this->events_rsvps_recieved_ajax_pagination($i_user_id);
            $content = ob_get_contents();
            $content_obj = json_decode($content);
            $data['events_ajax_content'] = $content_obj->html;
            $data['no_of_result'] = $content_obj->no_of_result;
            ob_end_clean();


            # view file...
            $VIEW = "logged/my_events/rsvp_recieved.phtml";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

    public function events_rsvps_recieved_ajax_pagination($i_user_id, $page = 0) {

        //echo $page;
        $s_where = '';
        $cur_page = $page + $this->pagination_per_page;
        $data = $this->data;

        $result = $this->events_user_invited_model->get_events_rsvps_recived($i_user_id, $s_where, intval($page), $this->pagination_per_page);
        //echo $this->db->last_query();
        $total_rows = $this->events_user_invited_model->get_total_events_rsvps_recived($i_user_id, $s_where);
        //pr($result,1);
        $data['arr_events'] = $result;
        $data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
        $data['profile_id'] = $i_user_id;

        //--- for check end of he page.
        $view_more = true;
        $rest_counter = $total_rows - $page;
        if ($rest_counter <= $this->pagination_per_page)
            $view_more = false;
        //--------- end check


        $VIEW_FILE = "logged/my_events/rsvp_recieved_ajax.phtml";

        if (is_array($result) && count($result)) {
            $content = $this->load->view($VIEW_FILE, $data, true);
        } else {
            $content = '';
        }

        //echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode(array('html' => $content, 'current_page' => $cur_page, 'no_of_result' => $data['no_of_result'], 'total' => $total_rows, 'view_more' => $view_more, 'cur_page' => $data['current_page_1']));
    }
function deny_event(){
    $event_id = $this->input->post('event_id');
    $i_user_id = intval(decrypt($this->session->userdata('user_id')));
    $this->db->delete('cg_event_user_invited', array('i_event_id' => $event_id , 'i_user_id' => $i_user_id)); 
echo json_encode(array('msg' => 'ok'));
}
}

// end of controller...
/*
ore = true;
        $rest_counter = $total_rows - $page;
        if ($rest_counter <= $this->pagination_per_page)
            $view_more = false;
        //--------- end check


        $VIEW_FILE = "logged/my_events/rsvp_recieved_ajax.phtml";

        if (is_array($result) && count($result)) {
            $content = $this->load->view($VIEW_FILE, $data, true);
        } else {
            $content = '';
        }

        //echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode(array('html' => $content, 'current_page' => $cur_page, 'no_of_result' => $data['no_of_result'], 'total' => $total_rows, 'view_more' => $view_more, 'cur_page' => $data['current_page_1']));
    }

}*/

// end of controller...

