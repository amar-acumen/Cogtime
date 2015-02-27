<?php
/*********
* Author: 
* Purpose:
*  Controller For "advertisements"
* 
* @package 
* @subpackage 
* 
* @link InfController.php 
* @link Base_Controller.php
* @link model/abuse_model.php
* @link views/##
*/

class Reporting_abuse extends Admin_base_Controller
{
    private $pagination_per_page=10;

    

    // constructor definition...
    function __construct()
    {
        try
        {
            parent::__construct();
 			parent::_check_admin_login();            
            # configuring paths...
            
            # loading reqired model & helpers...
            // $this->load->helper('###');
           $this->load->model("abuse_model");
           $this->load->helper('common_option_helper.php');
           
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    // "index" function definition...
    public function index() 
    {

        try
        {
            # adjusting header & footer sections [Start]...
            $data = $this->data;
            parent::_set_title("::: COGTIME Xtian network :::");
            parent::_set_meta_desc("::: COGTIME Xtian network :::");
            parent::_set_meta_keywords("::: COGTIME Xtian network :::");
            parent::_add_js_arr( array( 'js/lightbox.js','js/jquery.form.js',
									       'js/jquery/JSON/json2.js') );
            
			// Sound-manager js, css
			parent::_add_js_arr( array('js/jwplayer/jwplayer.js') );
			// End Sound-manager js, css                          
            parent::_add_css_arr( array() );
            # adjusting header & footer sections [End]...
			$data['top_menu_selected'] = 1;
			$data['submenu'] = 7;
         
            
			// fetching data
			
			$this->session->set_userdata('search_condition','');
			$page=0;
			$order_by = " `id` DESC ";
			
			ob_start();
            $this->ajax_pagination();
            $data['photo_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
			
			
			/*ob_start();
            $this->ajax_pagination('audio');
            $data['audio_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
			
			
			ob_start();
            $this->ajax_pagination('video');
            $data['video_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
			
			
			ob_start();
            $this->ajax_pagination('user');
            $data['user_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
			
			
			ob_start();
            $this->ajax_pagination('event');
            $data['event_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();*/
			
                
            # rendering the view file...
            $VIEW_FILE = "admin/site_settings/reporting_abuse/reporting_abuse.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }

	# function to load ajax-pagination [AJAX CALL]...
    public function ajax_pagination($page=0)
    {
        try
        {
			## seacrh conditions : filter ############
		 	$s_where = " WHERE 1  ";
			$WHERE_COND ='';
			 if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) :
				if($this->input->post('txt_name') != ''):
				$s_word = get_formatted_string(trim($this->input->post('txt_name')));
				$WHERE_COND .= ($s_word=='')?'':" AND CONCAT(u.s_first_name,' ',u.s_last_name) LIKE '%".$s_word."%' ";
				endif;
				
				if($this->input->post('txt_abuser_name') != ''):
				$s_word = get_formatted_string(trim($this->input->post('txt_abuser_name')));
				$WHERE_COND .= ($s_word=='')?'':" AND r1.i_abuser_name LIKE '%".$s_word."%' ";
				endif;
				
				if($this->input->post('category') != '-1'):
				$s_word = $this->input->post('category');
				$WHERE_COND .= ($s_word=='')?'':" AND r1.e_type ='".$s_word."' ";
				endif;
				
				if($this->input->post('from_date') != ''):
				$s_word = get_db_dateformat($this->input->post('from_date'));
				$WHERE_COND .= ($s_word=='')?'':" AND Date(r1.dt_created_on) >='".$s_word."' ";
				endif;
				if($this->input->post('end_date') != ''):
				$s_word = get_db_dateformat($this->input->post('end_date'));
				$WHERE_COND .= ($s_word=='')?'':" AND Date(r1.dt_created_on) <='".$s_word."' ";
				endif;
				$this->session->set_userdata('search_condition',$WHERE_COND);
             endif;  
		   	
			$s_where .= $this->session->userdata('search_condition');
			$order_by = " `id` DESC ";
			
			//$s_where .= " AND e_type = '{$type}' ";
			
		   	$result = $this->abuse_model->get_list($s_where,$page,$this->pagination_per_page,$order_by);
            $resultCount = count($result);
			$total_rows = $this->abuse_model->get_list_count($s_where);
			
			
			//pr($result);
			#Jquery Pagination Starts
           	$this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/site_settings/reporting_abuse/ajax_pagination";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->pagination_per_page;
            $config['uri_segment'] = 5;
            $config['num_links'] = 9;
            $config['page_query_string'] = false;
            $config['prev_link'] = 'PREV';
            $config['next_link'] = 'NEXT';

            $config['cur_tag_open'] = '<li> <span><a href="javascript:void(0)" class="select">';
            $config['cur_tag_close'] = '</a></span></li>';

            $config['next_tag_open'] = '<li><a href="javascript:void(0)">';
            $config['next_tag_close'] = '</a></li>';

            $config['prev_tag_open'] = '<li><a href="javascript:void(0)">';
            $config['prev_tag_close'] = '</a></li>';

            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';

            $config['div'] = '#photo_content'; /* Here #content is the CSS selector for target DIV */
            $config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
            $config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */

            $this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();

            // getting   listing...
			$data['info_arr'] = $result;
			$data['no_of_result'] = $total_rows;
			$data['current_page'] = $page;
          
			# loading the view-part...
          echo  $this->load->view('admin/site_settings/reporting_abuse/reporting_abuse_ajax.phtml', $data,TRUE);
		 }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
    }
    
    
   
	
	
	 public function change_status()
	 {
			
			$data = $this->data;
			$page  =  intval($this->input->post('cur_page'));
			$i_status = intval($this->input->post('i_status'));
			$cur_status = intval($this->input->post('cur_status'));
			$ID = $this->input->post('record_id');
			
			
			$type = $this->input->post('type');
			$id = $this->input->post('ref_id');
			
			
			
			if($this->session->userdata('user_id') !="")
			{
				$this->abuse_model->change_status($i_status,$ID);
				
				### disable particular photo, video, audio, event or profile from BO
				###cg_events,cg_user_photos, cg_user_videos, cg_user_audio, cg_users
				
					if($type == 'user'){
						
					  $sql = " UPDATE   cg_users SET `i_status` = {$i_status} WHERE `id` = {$id}"; 
		  			  $this->db->query($sql);
					  $user_name = get_username_by_id($id);
					  $user_email = get_useremail_by_id($id);
					}
					else if($type == 'audio'){
					  $sql = " UPDATE   cg_user_audio SET `i_status` = {$i_status} WHERE `id` = {$id}"; 
		  			  $this->db->query($sql);
					  $user_name = get_audio_owner_name_by_id($id);
					  $user_email = get_useremail_by_id(get_audio_ownerID_by_id($id));
					}
					else if($type == 'video'){
					  $sql = " UPDATE   cg_user_videos SET `i_status` = {$i_status} WHERE `id` = {$id}"; 
		  			  $this->db->query($sql);
					  $user_name = get_video_owner_name_by_id($id);
					  $user_email = get_useremail_by_id(get_video_ownerID_by_id($id));
					}
					else if($type == 'photo'){
					    $sql = " UPDATE   cg_user_photos SET `i_status` = {$i_status} WHERE `id` = {$id}"; 
		  			   $this->db->query($sql);
					   $user_name = get_photo_owner_name_by_id($id);
					   $user_email = get_useremail_by_id(get_photo_ownerID_by_id($id));
					}
					else if($type == 'event'){
					  $sql = " UPDATE   cg_events SET `i_status` = {$i_status} WHERE `id` = {$id}"; 
		  			  $this->db->query($sql);
					  $event_info = get_event_detail_by_id($id);
					  $user_name = ($event_info['i_user_type'] == 2)?get_admin_nameby_id($event_info['i_host_id']):get_username_by_id($event_info['i_host_id']);
					  $user_email = ($event_info['i_user_type'] == 2)?get_admin_emailby_id($event_info['i_host_id']):get_useremail_by_id($event_info['i_host_id']);
					  
					
					}
					else if($type == 'wall'){
					  $sql = " UPDATE   cg_user_newsfeeds SET `i_status` = {$i_status} WHERE `id` = {$id}"; 
		  			  $this->db->query($sql);
					  $post_info = get_post_detail_by_id($id);
					  $user_name = get_username_by_id($post_info['i_abuser_name']);
					  $user_email =get_useremail_by_id($post_info['i_owner_id']); 
					  
					
					}
					else if($type == 'blog'){
					  $sql = " UPDATE   cg_user_blogs SET `i_isenabled` = {$i_status} WHERE `id` = {$id}"; 
		  			  $this->db->query($sql);
					  $blog_info = get_blog_detail_by_id($id);
					  $user_name = get_username_by_id($blog_info['i_user_id']);
					  $user_email =get_useremail_by_id($blog_info['i_user_id']); 
					  
					
					}
					
					else if($type == 'article'){
					  $sql = " UPDATE   cg_user_blog_posts SET `i_disable` = {$i_status} WHERE `id` = {$id}"; 
		  			  $this->db->query($sql);
					  $art_info = get_article_detail_by_id($id);
					  $user_name = get_username_by_id($art_info['i_user_id']);
					  $user_email =get_useremail_by_id($art_info['i_user_id']); 
					  
					
					}
					else if($type == 'ring'){
					  $sql = " UPDATE   cg_user_ring_post SET `i_disable` = {$i_status} WHERE `id` = {$id}"; 
		  			  $this->db->query($sql);
					  $art_info = get_ring_post_detail_by_id($id);
					  $user_name = get_username_by_id($art_info['i_user_id']);
					  $user_email =get_useremail_by_id($art_info['i_user_id']); 
					  
					}
					else if($type == 'tweet'){
					  $sql = " UPDATE   cg_tweets SET `i_isenabled` = {$i_status} WHERE `id` = {$id}"; 
		  			  $this->db->query($sql);
					  $art_info = get_tweet_detail_by_id($id);
					  $data_arr=json_decode($art_info['data']);
					  if($art_info['s_type'] == 'normal')
					  {
					  $user_name = get_username_by_id($art_info['i_owner_id']);
					  }
					  else if($art_info['s_type'] == 'retweeted')
					  {
					  $user_name = get_username_by_id($data_arr->tweet_owner_id);
					  }
					  if($art_info['s_type'] == 'normal')
					  {
					  $user_email =get_useremail_by_id($art_info['i_user_id']); 
					   }
					  else if($art_info['s_type'] == 'retweeted')
					  {
					  $user_email = get_useremail_by_id($data_arr->tweet_owner_id);
					  }
					}
				
				## send mail..
				if($i_status==2){
					
					
					  if($type == 'user'){
						$stype = 'profile'	;
						$type_name = get_username_by_id($id);
					  }
					  else if($type == 'audio'){
						$stype = 'audio'	;
						$type_name = get_audio_title($id);
					  }
					  else if($type == 'video'){
						$stype = 'video'	;
						$type_name = get_video_title($id);
					  }
					  else if($type == 'photo'){
						$stype = 'photo'	;
						$type_name = get_photo_title($id);
					  }
					  else if($type == 'event'){
						  
						$this->load->model('events_model'); 
						$stype = 'event'	;
						$type_name = $this->events_model->get_event_title_id($id);
					  }
					 else if($type == 'wall'){
						  
						//$this->load->model('events_model'); 
						$stype = 'wall'	;
						$type_name = 'Wall Post';
					  }
					  else if($type == 'blog'){
						  
						//$this->load->model('events_model'); 
						$stype = 'blog'	;
						$type_name = get_blog_name_by_id($id);
					  }
					  $this->load->helper('html');
					$this->load->library('email');
					 $email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
				$this->email->initialize($email_setting);
					$this->load->model('mail_contents_model');
					$mail_info = $this->mail_contents_model->get_by_name("admin_abuse_response_user");
					$body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
					
					$body = sprintf3( $body, array( 'user_name'=>$user_name,
													  'type'=>$stype,
													  'type_name' => $type_name));
							   
					
					$subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
					$subject =  sprintf3( $subject, array('user_name'=>$user_name,
													  'type' => $stype,
													  'type_name' => $type_name));
					$arr['subject']   =  $subject;
					$arr['to']         = $user_email;
					//$arr['bcc']    = 'aradhana.online19@gmail.com';
					
					$arr['from_email'] = 'admin@cogtime.com';
					$arr['from_name'] = 'Cogtime Admin';
					$arr['message'] = $body;
					$this->email->from( $arr['from_email'], $arr['from_name']);
                #dump($arr);
				$this->email->subject($arr['subject']);
						
				$this->email->to($arr['to']);
				$this->email->bcc($arr['bcc']);
				$this->email->message("$body");
                        //send_mail($arr);
				$this->email->send();
					//send_mail($arr);
					//var_dump($arr);
					### Send Mail ####
					
				}
				
				### disable particular photo, video, audio, event or profile from BO
				
				if($i_status==1)
				   {
					 
						$action_txt =
							 '<input name="" title="Enable" type="button" class="btn-03" onclick="javascript:changeStatus(\''.$ID.'\',\'2\',\''.$i_status.'\','.$id.',\''.$type.'\')"  value="Disable"/>';
					
				   }
				 else if($i_status==2)
				   {
						$action_txt =
							 '<input name="" title="Disable" type="button" class="btn-03" onclick="javascript:changeStatus(\''.$ID.'\',\'1\',\''.$i_status.'\','.$id.',\''.$type.'\')"  value="Enable"/>';
					
				   } 
			}
			else{
			    
				$SUCCESS_MSG = "An error has occured! please try again. ";
				echo json_encode(array('result'=>false,
                					   'u_id'=>$ID,
									   'action_txt' =>$action_txt,
									   'i_status' => $cur_status,
                					   'msg'=>$SUCCESS_MSG , 'redirect'=>true)); exit;
			}
			
			
			$SUCCESS_MSG = "Status changed successfully! ";
	    
			# view part...
			    ob_start();
                $content = '';
                ob_end_clean();
                
                echo json_encode(array('result'=>true,
                					   'u_id'=>$ID,
									   'action_txt' =>$action_txt,
									   'i_status' => $cur_status,
                					   'msg'=>$SUCCESS_MSG ,'redirect'=>false)); exit;
	 }
	
	
}   // end of controller...