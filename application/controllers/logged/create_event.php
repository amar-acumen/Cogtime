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
 * s
 */
include(APPPATH . 'controllers/base_controller.php');

class Create_event extends Base_controller {

    private $upload_thumb_path;
    private $upload_thumb_image;
    private $thumb_imagename = '';
    private $upload_path;
    private $upload_image;
    private $imagename = '';
    private $pagination_per_page = 5;

    public function __construct() {
        try {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            $this->upload_path = BASEPATH . '../uploads/events_photo/';
            # loading reqired model & helpers...
            $this->load->model('users_model');
            $this->load->model('contacts_model');
            $this->load->model('netpals_model');
            $this->load->model("events_model");
            $this->load->model("events_email_invited_model");
            $this->load->model("events_user_invited_model");
            $this->load->model("user_notifications_model");
            $this->load->helper("my_utility_helper");
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
                'js/lightbox.js', 'js/jquery-ui-1.8.2.custom.min.js',
                'js/stepcarousel.js', 'js/jquery.dd.js',
                'js/frontend/logged/tweets/tweet_utilities.js',
                'js/frontend/logged/events/my_events.js',
				'js/jquery-ui-timepicker-addon.js'
            ));

            parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
                'css/dd.css'));

            $i_user_id = intval(decrypt($this->session->userdata('user_id')));

            ## FETCHING FRIENDS ###

            $WHERE = " WHERE 
						1
						AND c.s_status = 'accepted' 
						AND u.i_status=1 
						AND
						((c.i_requester_id = '" . $i_user_id . "' AND u.id=c.i_accepter_id ) 
						OR (c.i_accepter_id = '" . $i_user_id . "' AND u.id=c.i_requester_id ))  GROUP BY u.id ";

            $ORDER_BY = "u.s_first_name ASC";
            $contacts = $this->contacts_model->fetch_multi_online_friends($WHERE, null, null, $ORDER_BY);

            $exclude_id_csv = '';
            $exclude_id_csv .= $i_user_id . '';
            $exclude_id_arr = $this->contacts_model->get_friendsId_by_user_id($i_user_id);
            #pr($exclude_id_arr);

            if (count($exclude_id_arr)) {

                $exclude_id_csv .= implode(', ', $exclude_id_arr);
            }


            ## FETCHING NETPALS ###
            $total_where = " WHERE 1
                                    AND u.i_status=1 
                                    AND ((c.i_requester_id = " . $i_user_id . " AND u.id=c.i_accepter_id) 
                                        OR 
                                    (c.i_accepter_id=" . $i_user_id . " AND u.id=c.i_requester_id))
                                    AND c.s_status='accepted' 
									AND u.id NOT IN (" . $exclude_id_csv . ")
									GROUP BY u.id ";

            $ORDER_BY = "u.s_first_name ASC";


            $netpals = $this->netpals_model->fetch_multi_online_netpals($total_where, null, null, $ORDER_BY);
            //echo $this->db->last_query();
            $total_contact_arr = array();

            $contact_arr = array_merge($contacts, $netpals);
            array_sort_by_column($contact_arr, 's_displayname');

            $data['contact_arr'] = $contact_arr;
            //pr($contact_arr);   
            # view file...
            $VIEW = "logged/my_events/create_event.phtml";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

    public function add_event_ajax($id) {
        try {

            parent::check_login(TRUE, '', array('1'));
            $arr_messages = array();
            # error message trapping...
            if (trim($this->input->post('txt_title')) == '') {
                $arr_messages['title'] = "* Required Field.";
            }

            if (trim($this->input->post('txt_street')) == '') {
                $arr_messages['street'] = "* Required Field.";
            }


            if (trim($this->input->post('txt_state')) == '') {
                $arr_messages['state'] = "* Required Field.";
            }

            if (trim($this->input->post('txt_city')) == '') {
                $arr_messages['city'] = "* Required Field.";
            }

            if (trim($this->input->post('txt_postcode')) == '') {
                $arr_messages['postcode'] = "* Required Field.";
            }


            if (trim($this->input->post('country')) == '-1') {
                $arr_messages['country'] = "* Required Field.";
            }

            if (trim($this->input->post('date_to')) == '') {
                $arr_messages['date_to'] = "* Required Field.";
            }

            if (trim($this->input->post('date_end')) == '') {
                $arr_messages['date_end'] = "* Required Field.";
            }


            /* if( trim($this->input->post('txt_venue_time'))=='') 
              {
              $arr_messages['venue_time'] = "* Required Field.";
              }
              /*elseif(!preg_match("/^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])(:([0-5]?[0-9]))?$/", trim($this->input->post('txt_venue_time')))){
              elseif(!preg_match("/^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])?$/", trim($this->input->post('txt_venue_time')))){
              $arr_messages['venue_time'] = '* Please input time in 24 hours format.';
              } */

            if (trim($this->input->post('privacy')) == '-1') {
                $arr_messages['privacy'] = "* Required Field.";
            }

            /* if( trim($this->input->post('txt_event_desc')) =='') 
              {
              $arr_messages['event_desc'] = "* Required Field.";
              }
             */

            if (empty($_FILES['adv_image_1']['tmp_name']) || $_FILES['adv_image_1']['tmp_name'] == '') {
                $arr_messages['image_1'] = "* Required Field.";
            }



            if (trim($this->input->post('txt_frnd_email')) != '') {
                $frnd_email = explode(',', trim($this->input->post('txt_frnd_email')));
                //pr($frnd_email);

                foreach ($frnd_email as $val) {

                    if ((!preg_match("/^[_a-z0-9-\+]+(\.[_a-z0-9-\+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4},?)$/i"
                                    , trim($val)))) {
                        $arr_messages['frnd_email'] = 'Invalid Email ID.';
                    }
                }
            }

           /* if (trim($this->input->post('todo_strt_frm')) == '-1') {
                $arr_messages['todo_strt_frm'] = "* Required Field.";
            }

            if (trim($this->input->post('todo_end_frm')) == '-1') {
                $arr_messages['todo_end_frm'] = "* Required Field.";
            }*/

            /* if( trim($this->input->post('todo_rem_time'))=='-1') 
              {
              $arr_messages['todo_rem_time'] = "* Required Field.";
              } */


            ## uploading pic
            for ($i = 1; $i < 6; $i++) {
                $fileElementName = 'adv_image_' . $i;
                $data["file_error_$fileElementName_" . $i] = '';

                # echo $_FILES[$fileElementName]['name'];

                if (!empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] != '') {
                    preg_match('/(^.*)\.([^\.]*)$/', $_FILES[$fileElementName]['name'], $matches);
                    $ext = "";
                    if (count($matches) > 0) {
                        $ext = strtolower($matches[2]);
                        $original_name = $matches[1];
                    } else
                        $original_name = 'image';

                    if (!in_array($ext, $this->config->item('VALID_IMAGE_EXT'))) {
                        $data["file_error_$fileElementName_" . $i] = "<div class=\"error_massage\" id=\"err_msg\">supported extensions are " . implode(' , ', $this->config->item('VALID_IMAGE_EXT')) . "</div>";
                    }


                    if ($_FILES[$fileElementName]['size'] > $this->config->item('MAX_UP_FILE_SIZE') * 1024 * 1024) {
                        $data["file_error_$fileElementName_" . $i] = "<div class=\"error_massage\" id=\"err_msg\">Maximum file upload size is " . $this->config->item('MAX_UP_FILE_SIZE') . " MB.</div>";
                    }


                    //// check for uploaded banner file's dimension [End]...    
                }
            }
            ## end 
            //pr($arr_messages);
            if (count($arr_messages) == 0) {

                $info = array();
                 $ip = getenv("REMOTE_ADDR") ;
                 $info["u_ip"] = $ip;
                 $info["s_title"] = get_formatted_string($this->input->post('txt_title'));
                
                $info["i_host_id"] = intval(decrypt($this->session->userdata('user_id')));
                $info["i_user_type"] = intval(($this->session->userdata('user_type')));
                if (get_formatted_string($this->input->post('txt_event_desc')) == 'Max 500 Char allowed') {
                    $info["s_desc"] = '';
                } else {
                    $info["s_desc"] = get_formatted_string($this->input->post('txt_event_desc'));
                }

                $info["s_city"] = get_formatted_string($this->input->post('txt_city'));
                $info["s_state"] = get_formatted_string($this->input->post('txt_state'));
                $info["s_postcode"] = get_formatted_string($this->input->post('txt_postcode'));
                $info["s_address"] = get_formatted_string($this->input->post('txt_street'));


                $info["i_country_id"] = intval(decrypt($this->input->post('country')));

                $info["e_privacy"] = ($this->input->post('privacy') == 'friend') ? 'friend' : 'everyone';

                //$start_time = get_db_dateformat($this->input->post('date_to'), '/') . ' ' . $this->input->post('todo_strt_frm');
				$start_time = trim($this->input->post('date_to')).':'.'00' ;

                $info["dt_start_time"] = $start_time;
				

               // $end_time = get_db_dateformat($this->input->post('date_end'), '/') . ' ' . $this->input->post('todo_end_frm');
			    $end_time = trim($this->input->post('date_end')).':'.'00' ;
                $info["dt_end_time"] = $end_time;


                for ($i = 1; $i < 6; $i++) {

                    //if($posted['adv_image_'.$i] != ''){
                    $info["s_image_" . $i] = $this->_upload_profile_image(trim($_FILES['adv_image_' . $i]['name']), 'adv_image_' . $i);
                    //}
                }

               // $info["t_start_time"] = trim($this->input->post('todo_strt_frm'));
              //  $info["t_end_time"] = trim($this->input->post('todo_end_frm'));
                $info["t_remind_time"] = trim($this->input->post('todo_rem_time'));

                //$date_a = new DateTime($info["dt_start_time"].' '.$info["t_start_time"]);
                //$date_b = new DateTime($info["dt_end_time"].' '.trim($this->input->post('todo_rem_time')));

                $date_a = new DateTime($info["dt_start_time"]);
                $date_b = new DateTime(get_db_dateformat($this->input->post('date_to'), '/') . ' ' . $this->input->post('todo_rem_time'));

                #pr($date_a); pr($date_b);


                $interval = date_diff($date_a, $date_b);

                #pr($interval);

                $info["t_remind_me_back"] = $interval->format('%h:%i:%s');

                $info['dt_created_on'] = get_db_datetime(); //pr($info,1);
                $is_abusive_title = 0;
                $is_abusive_desc = 0;
                $is_abusive_title = check_abusive_words($info["s_title"]);
                $is_abusive_desc = check_abusive_words($info["s_desc"]);
                if ($is_abusive_title > 0 || $is_abusive_desc > 0) {
                    echo json_encode(array('success' => true, 'abusive'=> 1, 'arr_messages' => $arr_messages, 'msg' => 'Abusive words are not allowed!'));
                    exit;
                } else {
                    $i_event_id = $this->events_model->insert($info);
                    #echo $i_event_id;
                    #echo $this->db->event_invitation;
                    insert_invitation($i_event_id, $_POST, $this->db->event_invitation, 'i_event_id');
                    ## insert guest id in  cg_event_user_invited #
                    $arr_frnd = array();
                    $arr_netpal = array();
                    $arr_pp = array();
                    if ($this->input->post('frndinv') == '') {
                        $arr_frnd['0'] = '0';
                        //echo '0';
                    } else {
                        $arr_frnd = $this->input->post('frndinv');
                        //echo '1';
                    }
                    if ($this->input->post('netpalinv') == '') {
                        $arr_netpal['0'] = '0';
                    } else {
                        $arr_netpal = $this->input->post('netpalinv');
                    }
                    if ($this->input->post('ppinv') == '') {
                        $arr_pp[] = '0';
                    } else {
                        $arr_pp = $this->input->post('ppinv');
                    }
                    $complete_arr_frnd = array();
                    $contact_arr = array();

                    $contact_arr = array_merge($arr_frnd, $arr_netpal);
                    $complete_arr_frnd = array_merge($contact_arr, $arr_pp);
                    $complete_arr_frnd = array_unique($complete_arr_frnd);
                    $complete_arr_frnd = array_filter($complete_arr_frnd);
                    #pr($complete_arr_frnd);
                    if (count($complete_arr_frnd) != '0') {

                        $guest_info = array();
                        //$guest_arr = explode('##',$this->input->post('h_friend_id'));
                        foreach ($complete_arr_frnd as $val) {

                            $notification_arr = array();

                            $guest_info['i_user_id'] = $val;
                            $guest_info['i_event_id'] = $i_event_id;
                            $guest_info['dt_created_on'] = get_db_datetime();
                            //pr($guest_info);
                            $i_newid = $this->events_user_invited_model->insert_user_invited_from_contacts($guest_info);

                            ## send im and add notifications ##
                            #$notificaion_opt = $this->user_alert_model->check_option_user_id($info['i_accepter_id']);	
                            //if($notificaion_opt['e_photo_comments_received'] == 'Y'){
                            $notification_arr['i_requester_id'] = decrypt($this->session->userdata('user_id'));
                            $notification_arr['i_accepter_id'] = $guest_info['i_user_id'];
                            $notification_arr['s_type'] = 'event_invitation';
                            $notification_arr['dt_created_on'] = get_db_datetime();
                            #pr($notification_arr);
                            $ret = $this->user_notifications_model->insert($notification_arr);
                            //}
                            $message_id = parent::social_notifications_message($notification_arr['i_requester_id'], $notification_arr['i_accepter_id'], 'event_invitations_request', $i_event_id);

                            ## send im and add notifications ##
                        }
                    }
                    ### end guest id in  cg_event_user_invited #
                    ## insert guest id in  event_email_invited #


                    if (trim($this->input->post('txt_frnd_email')) != '') {

                        $guest_email_info = array();
                        $guest_email_arr = explode(',', $this->input->post('txt_frnd_email'));
                        foreach ($guest_email_arr as $val) {

                            $guest_email_info['s_email'] = $val;
                            $guest_email_info['i_event_id'] = $i_event_id;
                            $guest_email_info['dt_created_on'] = get_db_datetime();
                            //pr($guest_info);
                            $i_newid = $this->events_email_invited_model->insert_user_invited_emails($guest_email_info);

                            ### send mail  ###
				$this->load->helper('html');
					$this->load->library('email');
					 $email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
				$this->email->initialize($email_setting);
                            $this->load->model('mail_contents_model');
                            $mail_info = $this->mail_contents_model->get_by_name("event_email_invitation_msg");
                            $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);

                            $url = "<a href='" . get_events_detail_url($i_event_id) . "' target='_blank'>" . $info["s_title"] . "</a>";

                            $body = sprintf3($body, array('email' => $guest_email_info["s_email"],
                                'sender_name' => get_username_by_id(intval(decrypt($this->session->userdata('user_id')))),
                                'url' => $url,
                                'descritption' => my_substr($info["s_desc"], 300)
                            ));


                            //echo $body;

                            $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
                            $subject = sprintf3($subject, array('sender_name' => get_username_by_id(intval(decrypt($this->session->userdata('user_id'))))));


                            $arr['subject'] = $subject;
                            $arr['to'] = $guest_email_info["s_email"];
                            $arr['from_email'] = 'no-reply@cogtime.com';
                            $arr['from_name'] = 'admin@cogtime.com'; //$this->site_settings_model->get('s_mail_from_name');
                            $arr['message'] = $body;
                            //pr($arr); exit;
							$this->email->from( $arr['from_email'], $arr['from_name']);
                #dump($arr);
							$this->email->subject($arr['subject']);
									
							$this->email->to($arr['to']);
							$this->email->bcc($arr['bcc']);
							$this->email->message("$body");
									//send_mail($arr);
							$this->email->send();
                           // send_mail($arr);
                        }
                    }
                    ### end guest id in  event_email_invited #
                    ###########################Privacy settings###################################
                    insert_privacy($i_event_id, $_POST, $this->db->event_privacy, 'i_event_id');
                    ###########################Privacy settings###################################

                    echo json_encode(array('success' => true, 'arr_messages' => $arr_messages, 'msg' => 'Event added Successfully.'));
                }
            } else {
                echo json_encode(array('success' => false, 'arr_messages' => $arr_messages, 'msg' => ''));
            }
        } catch (Exception $err_obj) {
            
        }
    }

    public function _upload_profile_image($prev_img = '', $fileElementName) {

        // pr($_FILES);
        //$fileElementName = 'adv_image';	 
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
            //echo $this->upload_path; exit;

            @move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $this->upload_image);


            $config = array();

            $config['source_image'] = $this->upload_image;
            $config['thumb_marker'] = '-thumb';
            $config['crop'] = false;
            $config['width'] = 60;
            $config['height'] = 60;
            $config1['crop_from'] = 'middle';
            #$config['within_rectangle'] = true;
            $config['small_image_resize'] = 'no_resize';
            resize_exact($config);

            unset($config);


            $config = array();

            $config['source_image'] = $this->upload_image;
            $config['thumb_marker'] = '-mid';
            $config['crop'] = false;
            $config['width'] = 119;
            $config['height'] = 131;
            $config1['crop_from'] = 'middle';
            #$config['within_rectangle'] = true;
            $config['small_image_resize'] = 'inside';
            resize_exact($config);




            $config = array();

            $config['source_image'] = $this->upload_image;
            $config['thumb_marker'] = '-mid_FO';
            $config['crop'] = false;
            $config['width'] = 180;
            $config['height'] = 180;
            $config1['crop_from'] = 'middle';
            #$config['within_rectangle'] = true;
            $config['small_image_resize'] = 'inside';
            resize_exact($config);


            $config = array();

            $config['source_image'] = $this->upload_image;
            $config['thumb_marker'] = '-main';
            $config['crop'] = false;
            $config['width'] = 200;
            $config['height'] = 200;
            $config1['crop_from'] = 'middle';
            #$config['within_rectangle'] = true;
            $config['small_image_resize'] = 'inside';
            resize_exact($config);

            $config = array();

            $config['source_image'] = $this->upload_image;
            $config['thumb_marker'] = '-big';
            $config['crop'] = false;
            $config['width'] = 800;
            $config['height'] = 536;
            // $config1['crop_from'] = 'middle';
            $config['within_rectangle'] = true;
            $config['small_image_resize'] = 'no_resize';
            resize_exact($config);


            $this->s_picture_path = $new_imagename . '.' . $ext;
            //echo $this->upload_image; 

            @unlink($this->upload_image); //Unlink the original image........
            //@unlink($this->upload_path.getThumbName($prev_img,'thumb')); //Unlink the prevoius image........
            //exit;
            return $this->s_picture_path;
        } else {
            return $prev_img; // Unchaged previous image
        }
    }

    ### edit event ###

    public function edit_event($i_event_id) {
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
                'js/lightbox.js', 'js/jquery-ui-1.8.2.custom.min.js',
                'js/stepcarousel.js', 'js/jquery.dd.js',
                'js/frontend/logged/events/edit_events.js',
				'js/jquery-ui-timepicker-addon.js'
            ));

            parent::_add_css_arr(array('css/jquery-ui-1.8.2.custom.css',
                'css/dd.css'));

            $i_user_id = intval(decrypt($this->session->userdata('user_id')));

            $data['event_info'] = $this->events_model->get_by_id($i_event_id);
           $data['event_info']["dt_start_date"] = getShortDateWithTime($data['event_info']["dt_start_time"],14);
            $data['event_info']["dt_end_date"] = getShortDateWithTime($data['event_info']["dt_end_time"],14);

            $data['event_info']["start_time"] = getShortDateWithTime($data['event_info']["dt_start_time"], 8);

            $data['event_info']["remind_time"] = getShortDateWithTime($data['event_info']["t_remind_time"], 8);

            $data['privacy_arr'] = $this->events_model->get_privacy_settings_by_event_id($i_event_id);
            $data['invited'] = $this->events_model->get_invited_by_id($i_event_id);
            //pr($data['invited']);
            /* ## FETCHING FRIENDS ###
              $INVITED_MEM = $this->events_model->get_event_membersID_by_event_id($i_event_id);

              $exclude_id_csv = '';
              if(count($INVITED_MEM)){
              $INVITED_MEM_str = implode(',',$INVITED_MEM);
              $exclude_id_csv .= $INVITED_MEM_str;
              }

              //pr($INVITED_MEM,1);


              if($exclude_id_csv!='')
              {
              $WHERE = " WHERE
              1
              AND c.s_status = 'accepted'
              AND u.i_status=1
              AND
              ((c.i_requester_id = '".$i_user_id."' AND u.id=c.i_accepter_id )
              OR (c.i_accepter_id = '".$i_user_id."' AND u.id=c.i_requester_id ))
              AND u.id NOT IN (".$exclude_id_csv.")
              GROUP BY u.id "	;

              $ORDER_BY = "u.s_first_name ASC";
              $contacts = $this->contacts_model->fetch_multi_online_friends($WHERE, null, null,$ORDER_BY);
              }



              $exclude_id_csv .= $i_user_id.',';
              $exclude_id_arr = $this->contacts_model->get_friendsId_by_user_id($i_user_id);
              #pr($exclude_id_arr);

              if(count($exclude_id_arr)){

              $exclude_id_csv .= implode(', ',$exclude_id_arr);
              }


              ## FETCHING NETPALS ###
              $total_where =  " WHERE 1
              AND u.i_status=1
              AND ((c.i_requester_id = ".$i_user_id." AND u.id=c.i_accepter_id)
              OR
              (c.i_accepter_id=".$i_user_id." AND u.id=c.i_requester_id))
              AND c.s_status='accepted'
              AND u.id NOT IN (".$exclude_id_csv.")
              GROUP BY u.id " ;

              $ORDER_BY = "u.s_first_name ASC";


              $netpals = $this->netpals_model->fetch_multi_online_netpals($total_where, null,null,$ORDER_BY);
              //echo $this->db->last_query();
              $total_contact_arr = array();

              $contact_arr = array_merge($contacts,$netpals);
              array_sort_by_column($contact_arr, 's_displayname');

              $data['contact_arr'] = $contact_arr; */



            # view file...
            $VIEW = "logged/my_events/edit_event.phtml";
            parent::_render($data, $VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

    public function edit_event_ajax($id) {
        try {

            parent::check_login(TRUE, '', array('1'));
            $arr_messages = array();
            # error message trapping...
            if (trim($this->input->post('txt_title')) == '') {
                $arr_messages['title'] = "* Required Field.";
            }

            if (trim($this->input->post('txt_street')) == '') {
                $arr_messages['street'] = "* Required Field.";
            }


            if (trim($this->input->post('txt_state')) == '') {
                $arr_messages['state'] = "* Required Field.";
            }

            if (trim($this->input->post('txt_city')) == '') {
                $arr_messages['city'] = "* Required Field.";
            }

            if (trim($this->input->post('txt_postcode')) == '') {
                $arr_messages['postcode'] = "* Required Field.";
            }


            if (trim($this->input->post('country')) == '-1') {
                $arr_messages['country'] = "* Required Field.";
            }

            if (trim($this->input->post('date_to')) == '') {
                $arr_messages['date_to'] = "* Required Field.";
            }

            if (trim($this->input->post('date_end')) == '') {
                $arr_messages['date_end'] = "* Required Field.";
            }


            /* if( trim($this->input->post('txt_venue_time'))=='') 
              {
              $arr_messages['venue_time'] = "* Required Field.";
              }
              /*elseif(!preg_match("/^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])(:([0-5]?[0-9]))?$/", trim($this->input->post('txt_venue_time')))){
              elseif(!preg_match("/^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])?$/", trim($this->input->post('txt_venue_time')))){
              $arr_messages['venue_time'] = '* Please input time in 24 hours format.';
              } */

            if (trim($this->input->post('privacy')) == '-1') {
                $arr_messages['privacy'] = "* Required Field.";
            }

            /* if( trim($this->input->post('txt_event_desc')) =='') 
              {
              $arr_messages['event_desc'] = "* Required Field.";
              } */

         /*   if (trim($this->input->post('todo_strt_frm')) == '-1') {
                $arr_messages['todo_strt_frm'] = "* Required Field.";
            }

            if (trim($this->input->post('todo_end_frm')) == '-1') {
                $arr_messages['todo_end_frm'] = "* Required Field.";
            }*/

            /* if( trim($this->input->post('todo_rem_time'))=='-1') 
              {
              $arr_messages['todo_rem_time'] = "* Required Field.";
              } */


            ## uploading pic
            for ($i = 1; $i < 6; $i++) {
                $fileElementName = 'adv_image_' . $i;
                $data["file_error_$fileElementName_" . $i] = '';

                # echo $_FILES[$fileElementName]['name'];

                if (!empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] != '') {
                    preg_match('/(^.*)\.([^\.]*)$/', $_FILES[$fileElementName]['name'], $matches);
                    $ext = "";
                    if (count($matches) > 0) {
                        $ext = strtolower($matches[2]);
                        $original_name = $matches[1];
                    } else
                        $original_name = 'image';

                    if (!in_array($ext, $this->config->item('VALID_IMAGE_EXT'))) {
                        $data["file_error_$fileElementName_" . $i] = "<div class=\"error_massage\" id=\"err_msg\">supported extensions are " . implode(' , ', $this->config->item('VALID_IMAGE_EXT')) . "</div>";
                    }


                    if ($_FILES[$fileElementName]['size'] > $this->config->item('MAX_UP_FILE_SIZE') * 1024 * 1024) {
                        $data["file_error_$fileElementName_" . $i] = "<div class=\"error_massage\" id=\"err_msg\">Maximum file upload size is " . $this->config->item('MAX_UP_FILE_SIZE') . " MB.</div>";
                    }


                    //// check for uploaded banner file's dimension [End]...    
                }
            }


            if (trim($this->input->post('txt_frnd_email')) != '') {
                $frnd_email = explode(',', trim($this->input->post('txt_frnd_email')));
                //pr($frnd_email);

                foreach ($frnd_email as $val) {

                    if ((!preg_match("/^[_a-z0-9-\+]+(\.[_a-z0-9-\+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4},?)$/i"
                                    , trim($val)))) {
                        $arr_messages['frnd_email'] = 'Invalid Email ID.';
                    }
                }
            }
            ## end 

            $arr_frnd = array();
            $arr_netpal = array();
            $arr_pp = array();
            if ($this->input->post('frndinv') == '') {
                $arr_frnd['0'] = '0';
                //echo '0';
            } else {
                $arr_frnd = $this->input->post('frndinv');
                //echo '1';
            }
            if ($this->input->post('netpalinv') == '') {
                $arr_netpal['0'] = '0';
            } else {
                $arr_netpal = $this->input->post('netpalinv');
            }
            if ($this->input->post('ppinv') == '') {
                $arr_pp[] = '0';
            } else {
                $arr_pp = $this->input->post('ppinv');
            }
            $complete_arr_frnd = array();
            $contact_arr = array();

            $contact_arr = array_merge($arr_frnd, $arr_netpal);
            $complete_arr_frnd = array_merge($contact_arr, $arr_pp);
            $complete_arr_frnd = array_unique($complete_arr_frnd);
            $complete_arr_frnd = array_filter($complete_arr_frnd);
            #pr($complete_arr_frnd);
            //pr($arr_messages);
            if (count($arr_messages) == 0) {

                $info = array();

                $info["s_title"] = get_formatted_string($this->input->post('txt_title'));
                $info["i_host_id"] = intval(decrypt($this->session->userdata('user_id')));
                $info["i_user_type"] = intval(($this->session->userdata('user_type')));

                if (get_formatted_string($this->input->post('txt_event_desc')) == 'Max 500 Char allowed') {
                    $info["s_desc"] = '';
                } else {
                    $info["s_desc"] = get_formatted_string($this->input->post('txt_event_desc'));
                }

                $info["s_city"] = get_formatted_string($this->input->post('txt_city'));
                $info["s_state"] = get_formatted_string($this->input->post('txt_state'));
                $info["s_postcode"] = get_formatted_string($this->input->post('txt_postcode'));
                $info["s_address"] = get_formatted_string($this->input->post('txt_street'));


                $info["i_country_id"] = intval(decrypt($this->input->post('country')));

                $info["e_privacy"] = ($this->input->post('privacy') == 'friend') ? 'friend' : 'everyone';

                    //$start_time = get_db_dateformat($this->input->post('date_to'), '/') . ' ' . $this->input->post('todo_strt_frm');
				$start_time = trim($this->input->post('date_to')).':'.'00' ;

                $info["dt_start_time"] = $start_time;
				

               // $end_time = get_db_dateformat($this->input->post('date_end'), '/') . ' ' . $this->input->post('todo_end_frm');
			    $end_time = trim($this->input->post('date_end')).':'.'00' ;
                $info["dt_end_time"] = $end_time;

                for ($i = 1; $i < 6; $i++) {

                    if ($_FILES['adv_image_' . $i]['name'] != "") {
                        $info["s_image_" . $i] = $this->_upload_profile_image(trim($_FILES['adv_image_' . $i]['name']), 'adv_image_' . $i);
                    }
                }

              //  $info["t_start_time"] = trim($this->input->post('todo_strt_frm'));
               // $info["t_end_time"] = trim($this->input->post('todo_end_frm'));
                $info["t_remind_time"] = trim($this->input->post('todo_rem_time'));



                $date_a = new DateTime($info["dt_start_time"]);
                $date_b = new DateTime(get_db_dateformat($this->input->post('date_to'), '/') . ' ' . $this->input->post('todo_end_frm'));

                $interval = date_diff($date_a, $date_b);

                $info["t_remind_me_back"] = $interval->format('%h:%i:%s');

                $info['dt_updated_on'] = get_db_datetime();
                $is_abusive_title = 0;
                $is_abusive_desc = 0;
                $is_abusive_title = check_abusive_words($info["s_title"]);
                $is_abusive_desc = check_abusive_words($info["s_desc"]);
                if ($is_abusive_title > 0 || $is_abusive_desc > 0) {
                    echo json_encode(array('success' => true, 'abusive'=> 1, 'arr_messages' => $arr_messages, 'msg' => 'Abusive words are not allowed!'));
                    exit;
                   
                } else {
                    $i_ret_id = $this->events_model->update($info, $id);
//                echo $i_ret_id;

                    insert_invitation($id, $_POST, $this->db->event_invitation, 'i_event_id');
                    $invited_id = get_invited($id, $this->db->event_invitation, 'i_event_id');
                    $i = 0;
                    $invited = array();
                    foreach ($invited_id as $inv) {
                        $invited[$i] = $inv['user_id'];
                        $i++;
                    }
                    ## insert guest id in  cg_event_user_invited #
                    if (count($complete_arr_frnd) != '0') {

                        $guest_info = array();
                        //$guest_arr = explode('##',$this->input->post('h_friend_id'));
                        foreach ($complete_arr_frnd as $val) {



                            if (!in_array($val, $invited)) {
                                $notification_arr = array();
                                $guest_info['i_user_id'] = $val;
                                $guest_info['i_event_id'] = $id;
                                $guest_info['dt_created_on'] = get_db_datetime();
                                //pr($guest_info);
                                $i_newid = $this->events_user_invited_model->insert_user_invited_from_contacts($guest_info);

                                ## send im and add notifications ##
                                #$notificaion_opt = $this->user_alert_model->check_option_user_id($info['i_accepter_id']);	
                                //if($notificaion_opt['e_photo_comments_received'] == 'Y'){
                                $notification_arr['i_requester_id'] = decrypt($this->session->userdata('user_id'));
                                $notification_arr['i_accepter_id'] = $guest_info['i_user_id'];
                                $notification_arr['s_type'] = 'event_invitation';
                                $notification_arr['dt_created_on'] = get_db_datetime();

                                $ret = $this->user_notifications_model->insert($notification_arr);
                                //}
                                $message_id = parent::social_notifications_message($notification_arr['i_requester_id'], $notification_arr['i_accepter_id'], 'event_invitations_request', $id);

                                ## send im and add notifications ##
                            }
                        }
                    }
                    ### end guest id in  cg_event_user_invited #
                    ## insert guest id in  event_email_invited #


                    if (trim($this->input->post('txt_frnd_email')) != '') {

                        $guest_email_info = array();
                        $guest_email_arr = explode(',', $this->input->post('txt_frnd_email'));
                        foreach ($guest_email_arr as $val) {

                            $guest_email_info['s_email'] = $val;
                            $guest_email_info['i_event_id'] = $id;
                            $guest_email_info['dt_created_on'] = get_db_datetime();
                            //pr($guest_info);
                            $i_newid = $this->events_email_invited_model->insert_user_invited_emails($guest_email_info);

                            ### send mail  ###
							$this->load->helper('html');
					$this->load->library('email');
					 $email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
				$this->email->initialize($email_setting);
                            $this->load->model('mail_contents_model');
                            $mail_info = $this->mail_contents_model->get_by_name("event_email_invitation_msg");
                            $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);

                            $url = "<a href='" . get_events_detail_url($i_event_id) . "' target='_blank'>" . $info["s_title"] . "</a>";

                            $body = sprintf3($body, array('email' => $guest_email_info["s_email"],
                                'sender_name' => get_username_by_id(intval(decrypt($this->session->userdata('user_id')))),
                                'url' => $url,
                                'descritption' => my_substr($info["s_desc"], 300)
                            ));


                            //echo $body;

                            $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
                            $subject = sprintf3($subject, array('sender_name' => get_username_by_id(intval(decrypt($this->session->userdata('user_id'))))));


                            $arr['subject'] = $subject;
                            $arr['to'] = $guest_email_info["s_email"];
                            $arr['from_email'] = 'no-reply@cogtime.com';
                            $arr['from_name'] = 'admin@cogtime.com'; //$this->site_settings_model->get('s_mail_from_name');
                            $arr['message'] = $body;
                            //pr($arr); exit;
							$this->email->from( $arr['from_email'], $arr['from_name']);
                #dump($arr);
				$this->email->subject($arr['subject']);
						
				$this->email->to($arr['to']);
				$this->email->bcc($arr['bcc']);
				$this->email->message("$body");
                        //send_mail($arr);
				$this->email->send();
                            //send_mail($arr);
                        }
                    }
                    ### end guest id in  event_email_invited #
                    ###########################Privacy settings###################################
                    $this->db->query("DELETE FROM {$this->db->event_privacy} WHERE i_event_id='" . $id . "'");
                    insert_privacy($id, $_POST, $this->db->event_privacy, 'i_event_id');
                    ###########################Privacy settings###################################	

                    echo json_encode(array('success' => true, 'arr_messages' => $arr_messages, 'msg' => 'Event Updated Successfully.'));
					
                }
            } else {
                echo json_encode(array('success' => false, 'arr_messages' => $arr_messages, 'msg' => 'Error!'));
				
				
            }
        } catch (Exception $err_obj) {
            
        }
    }

}

// end of controller...

