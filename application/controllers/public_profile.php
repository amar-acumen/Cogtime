<?php
/*********
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
include(APPPATH.'controllers/base_controller.php');

class Public_profile extends Base_controller
{
    private $pagination_per_page = 10;
	private $comments_pagination_per_page = 3;
	private $people_liked_pagination_per_page = 10;
	private $friend_pagination_per_page =6;
    
    
    public function __construct()
     {
        try
        {
            parent::__construct();
            $this->upload_tmp_path = BASEPATH.'../uploads/wall_tmp/';
        	$this->upload_path  = BASEPATH.'../uploads/wall_photos/';
       		$this->upload_photo_path    = BASEPATH.'../uploads/wall_photos/';
			$this->upload_path_music_full = BASEPATH.'../uploads/user_audio_files/';
            # loading reqired model & helpers...
            $this->load->helper('wall_helper');
			$this->load->helper('my_utility_helper');
            $this->load->model('users_model');
			$this->load->model('netpals_model');
			$this->load->model('my_prayer_partner_model');
			$this->load->model('data_newsfeed_model');
			$this->load->model('newsfeed_comments_model');
			$this->load->model('user_photos_model');
			$this->load->model('user_audios_model');
			$this->load->model('user_notifications_model');
			$this->load->model('user_alert_model');
			$this->load->model('my_videos_model');
			$this->load->model('abuse_model');
			$this->load->model('my_ring_model');
			$this->load->model('prayer_group_model');
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }
	
	
	# index function definition...
    public function index($id='') 
    { 
        try
        { 
			
			$posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/lightbox.js',
										'js/tab.js',
										'js/jquery.autofill.js',
										'js/jquery.bxSlider.js',
										'js/jquery.lightbox.js',*/
										//'js/frontend/public_profile.js',
										'js/frontend/wall/wall_helper.js',
										'js/frontend/logged/my_friends.js',
										'js/frontend/logged/my_net_pals.js',
										'js/frontend/logged/my_prayer_partner.js',
										'js/frontend/logged/message_box/my_message.js',
										'js/production/tweet_utilities.js',
                                       // 'js/jquery.fancybox.js',
									//	'js/stepcarousel.js'
                                        ));
                                        
            parent::_add_css_arr( array(//'css/jquery-ui-1.8.2.custom.css',
                                        //'css/jquery.fancybox.css',
										'css/skin.css') );
										
		 
			parent::_add_js_arr( array(  'js/jquery.ui.rcarousel.js',
										 'js/jquery.jcarousel.min.js',
										 'js/jwplayer/jwplayer.js',
										  ) );
		  // End Sound-manager js, css
                                        
                                        
            # adjusting header & footer sections [End]...
			$data['page_view_type'] = 'public_account';
			
			$i_profile_id = intval($id);
			
			$public_arr_profile_info = $this->users_model->fetch_this($i_profile_id);
			$data['public_arr_profile_info'] = $public_arr_profile_info;
			$data['user_education_arr'] = $public_arr_profile_info['education_arr'];
			$data['user_work_arr'] = $public_arr_profile_info['work_arr'];
			$data['user_skill_arr'] = $public_arr_profile_info['skill_arr'];
			
		    //pr($data['arr_profile_info']);
            
            
            $data['i_profile_id'] = $i_profile_id;
			
			## setting right bar prayer partner ##
			parent::_set_user_prayer_patner_data($i_profile_id);
			## setting friends ##
			parent::_set_user_friends_data($i_profile_id);
			## setting netpals ##
			parent::_set_user_netpals_data($i_profile_id);
			
            
            
            ## get user's all photos ##
            parent::_get_user_all_photos($i_profile_id);
            
            
            
            ## get user's all videos ##
            parent::_get_user_all_videos($i_profile_id,'',0,6);
            
            
            ## get user's all audios ##
            parent::_get_user_all_audios($i_profile_id,'',0,4);
            
            
            
            ## checking friend relationship with logged user ##
			
			$logged_user_id = intval(decrypt($this->session->userdata('user_id')));
			
			if($logged_user_id != $i_profile_id){
				
				# CHECKING IS FRIEND ALREADY #
				$is_friend_arr =$this->users_model->if_already_friend($i_profile_id,$logged_user_id);
				if(count($is_friend_arr) >0 ){
					$data['if_already_friend'] = 'true';
				}
				# checking friend request already sent
				else{
					$is_friend_req_alrdy_snt = $this->users_model->friend_request_already_sent($logged_user_id,$i_profile_id);
					#pr($is_friend_req_alrdy_snt);
					if($is_friend_req_alrdy_snt){
						$data['display_becomefriend']     ='false';
						$data['if_already_friend']     ='';
					}else{
					    $data['if_already_friend']     ='false';
					}
				}
				
				# CHECKING IS Netpals ALREADY #
				$is_netpal_arr =$this->netpals_model->if_already_netpal($i_profile_id,$logged_user_id);
				if(count($is_netpal_arr) >0 ){
					$data['if_already_netpals'] = 'true';
				}
				# checking friend request already sent
				else{
					$is_netpal_req_alrdy_snt = $this->netpals_model->netpals_request_already_sent($logged_user_id,$i_profile_id);
					#pr($is_friend_req_alrdy_snt);
					if($is_netpal_req_alrdy_snt){
						$data['display_becomenetpals']     ='false';
						$data['if_already_netpals']     ='';
					}else{
					    $data['if_already_netpals']     ='false';
					}
				}
				$reffer_name =  basename($_SERVER['HTTP_REFERER']);
				
				$is_hit_frm_pp = strpos($reffer_name ,'prayer-partner');
				
			    ## IF HIT FROM PP PARNER SECTION THEN SHOW PRAYER PARTNER INVITATION SCOPE ##
				if($is_hit_frm_pp){
					$data['is_hit_frm_pp'] = $is_hit_frm_pp;
				}
				
				if($is_hit_frm_pp){
					### CHECK ALREADY PRAYER PARTNERS. ##
			  
					$get_friend_req_sent_status_me_him = $this->my_prayer_partner_model->get_prayer_partner_status_me_him(
																						$logged_user_id , $i_profile_id);
			  
					if(count($get_friend_req_sent_status_me_him) > 0  ) { 
						 $data['prayer_partner']['display_becomeprayer_partner']     ='false';
					 }
							  
					 
					$get_friend_status_me_him = $this->my_prayer_partner_model->get_prayer_partner_accepted_me_him(
														  $logged_user_id , $i_profile_id);
					if(count($get_friend_status_me_him) > 0  ) { 
						   $data['prayer_partner']['display_alreadyprayer_partner']     ='true';
					 }
							  
					$total_PP_arr = $this->my_prayer_partner_model->get_prayerPartnerId_by_user_id($i_profile_id);
					$total_PP = count($total_PP_arr);
					$total_pending_PP_req = $this->my_prayer_partner_model->total_pending_prayer_partner_recieved($i_profile_id);
					## CHECKING TOTAL PRAYER PARTNERS ##
					if($total_PP == 3 || $total_pending_PP_req >= 5){
						   $data['prayer_partner']['is_available']     ='false';		  
					}
					else{
							  $data['prayer_partner']['is_available']     ='true';					 
					 }
			  		 ## CHECKING TOTAL PRAYER PARTNERS ##
				}
				
				## END IF HIT FROM PP PARNER SECTION THEN SHOW PRAYER PARTNER INVITATION SCOPE ##
				
				
				
				$data['own_profile']     ='false';
			}else{
				$data['own_profile']     ='true';
			}
			
			/*****************************SET UP FLAG FOR POST IN WALL***********************************/
			$data['postwall_flag']	= false;	
			$arr_privacy_for_wall	= $this->users_model->get_privacy_for_wall_post($i_profile_id);
			
			$arr_user_level_privacy_for_wall_post = $this->data_newsfeed_model->get_privacy_settings_wall_by_wall_owner_id($i_profile_id);
			
			$arr_user_level_privacy_for_wall_like_comment = $this->data_newsfeed_model->get_privacy_settings_likeComment_by_wall_owner_id($i_profile_id);
			
			if(intval($arr_privacy_for_wall['i_friend_privacy'])==1 && count($arr_user_level_privacy_for_wall_post['Friend']) ==0 )
			{ 
				 foreach($this->data['right_panel']['friends_arr'] as $frnd)
				 {
					 if($frnd['user_id']==$logged_user_id)
						$data['postwall_flag']	= true;	
				 }
			}
			else if(intval($arr_privacy_for_wall['i_friend_privacy'])==1 && count($arr_user_level_privacy_for_wall_post['Friend'])  ){ 
				
					 if(in_array($logged_user_id, $arr_user_level_privacy_for_wall_post['Friend']))
						$data['postwall_flag']	= true;	
				
			}
			
			
			
			
			if(intval($arr_privacy_for_wall['i_netpal_privacy'])==1 && count($arr_user_level_privacy_for_wall_post['Netpal']) == 0)
			{
				 foreach($this->data['right_panel']['netpals_arr'] as $netpal)
				 {
					 if($netpal['user_id']==$logged_user_id)
						$data['postwall_flag']	= true;	
				 }
			}
			else if(intval($arr_privacy_for_wall['i_netpal_privacy'])==1 && count($arr_user_level_privacy_for_wall_post['Netpal']) ){ 
				
					 if(in_array($logged_user_id, $arr_user_level_privacy_for_wall_post['Netpal']))
						$data['postwall_flag']	= true;	
				
			}
			
			
			
			if(intval($arr_privacy_for_wall['i_prayer_partner_privacy'])==1 && count($arr_user_level_privacy_for_wall_post['Prayer Partner'] == 0 ))
			{ 
				 foreach($this->data['right_panel']['prayer_partner_arr'] as $pp)
				 {
					 if($pp['user_id']==$logged_user_id)
						$data['postwall_flag']	= true;	
				 }
			}
			else if(intval($arr_privacy_for_wall['i_prayer_partner_privacy'])==1 && count($arr_user_level_privacy_for_wall_post['Prayer Partner']) ){ 
				
					 if(in_array($logged_user_id, $arr_user_level_privacy_for_wall_post['Prayer Partner']))
						$data['postwall_flag']	= true;	
				
			}
			
			
			
			
			if(intval($arr_privacy_for_wall['i_ring_privacy'])!= 0 && count($arr_user_level_privacy_for_wall_post['Ring User']) == 0)
			{
				 $ring_id_arr = $this->my_ring_model->get_ringids_by_userID($i_profile_id);
				 if(count($ring_id_arr)){
					 $ring_ids_str =  implode(',', $ring_id_arr); 
				 }
				 
				 
				 $arr_ring_user	= $this->my_ring_model->get_members_ids_by_ringids($ring_ids_str);
				 $ring_user_arr = array();
				 if(count($arr_ring_user)){
					  
					  foreach($arr_ring_user as $val){
						 $ring_user_arr[] = $val['i_invited_id'];  
					  }
				  }
				 
				
				 //pr($arr_ring_user_frmtd);
				 if(in_array($logged_user_id,$ring_user_arr))
					$data['postwall_flag']	= true;	
			}
			else if(intval($arr_privacy_for_wall['i_ring_privacy'])==1 && count($arr_user_level_privacy_for_wall_post['Ring User']) == 0 ){ 
				
					 if(in_array($logged_user_id, $arr_user_level_privacy_for_wall_post['Ring User']))
						$data['postwall_flag']	= true;	
				
			}
			
			if(intval($arr_privacy_for_wall['i_prayer_group_privacy']) != 0 && count($arr_user_level_privacy_for_wall_post['Prayer Group']) == 0)
			{ 
				//echo  $arr_privacy_for_wall['i_prayer_group_privacy'];
				### get groups_ids string by profile id
				
				 $grp_ids_arr =  $this->prayer_group_model->get_grpids_by_logged_in_user($i_profile_id);
				 if(count($grp_ids_arr)){
					$grp_ids_str =  implode(',', $grp_ids_arr);
				 }
				 
				 $arr_pg_user	= $this->prayer_group_model->get_members_ids_by_grpids($grp_ids_str);
				 $pg_user_arr = array();
				  if(count($arr_pg_user)){
					  
					  foreach($arr_pg_user as $val){
						 $pg_user_arr[] = $val['i_user_id'];  
					  }
				  }
				  //pr($pg_user_arr,1);
				
				 if(in_array($logged_user_id,$pg_user_arr))
					$data['postwall_flag']	= true;	
			}
			else if(intval($arr_privacy_for_wall['i_prayer_group_privacy'])==1 && count($arr_user_level_privacy_for_wall_post['Prayer Group']) == 0 ){ 
				
					 if(in_array($logged_user_id, $arr_user_level_privacy_for_wall_post['Prayer Group']))
						$data['postwall_flag']	= true;	
				
			}
			
			### added if no restriction i.e visible to every one
			if($arr_privacy_for_wall['i_friend_privacy']==0 && $arr_privacy_for_wall['i_prayer_group_privacy'] == 0 
					&& $arr_privacy_for_wall['i_ring_privacy'] == 0 &&
					 $arr_privacy_for_wall['i_prayer_partner_privacy'] == 0
					&& $arr_privacy_for_wall['i_netpal_privacy'] == 0){
				$data['postwall_flag']	= true;	
			}
			//echo $data['postwall_flag'].' $$$$ ';
			/*****************************SET UP FLAG FOR POST IN WALL***********************************/
			
			##### message privacy ########
			$data['show_msg'] = getMsgPrivacy($i_profile_id, $logged_user_id,  $this->data['right_panel']['friends_arr'],
									$this->data['right_panel']['netpals_arr'],$this->data['right_panel']['prayer_partner_arr']);
			##### message privacy ########		
			
			if($logged_user_id == $i_profile_id)
			{
				$data['postwall_flag']	= true;	
				$data['show_msg'] = false;
			}
			
			## NEWSFEED ##
			ob_start();
			$this->newsfeed_pagination_show_more($i_profile_id);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['result_newsfeed_content'] = $content_obj->html; 
			ob_end_clean();
			
			
           // $this->data['all_photo_albums'] = $this->photo_albums_model->get_by_user_id($user_id);
			
            
            # view file...
			$VIEW_PG_FILE = "public_profile_new.phtml";
			$VIEW = "{$VIEW_PG_FILE}";
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           show_error($err_obj->getMessage());
        } 

    }
   	###########333 wall  methods #####################3
 
	## SHOWING WALL VIDEOS ##
	
	public function get_video(){
		try
			{
			
				  $i_media_id = intval($this->input->post('media_id'));
				  $width = intval($this->input->post('width'))<=0?'329':intval($this->input->post('width'));
				  $height = intval($this->input->post('height'))<=0?'212':intval($this->input->post('height'));
				  $media_info = $this->data_newsfeed_model->get_by_id( $i_media_id);
			//pr(json_decode($media_info['data']));
			#echo utf8_accents_to_ascii($media_info['s_video_url']);
				  if( !is_array($media_info) || !count($media_info) ) {
					  echo json_encode( array('result'=>'error') );
					  exit;
				  }
				  $video_arr = json_decode($media_info['data']);
				  //$video_arr->video->image_name;
				  //pr($video_arr);
		  /* ******************** Get photo details ************************ */
		  try {
				  $this->load->library('embed_video');
				  $config['zend_library_path'] = APPPATH ."libraries/Zend/";
				  $config['video_url'] =  ($video_arr->video->url);
				  
				  $this->embed_video->initialize($config);
				  $this->embed_video->prepare();
				  $image_source = $this->embed_video->get_player($width,$height);
			  }
			  catch(Exception $e) {
				  //$data['video_exists'] = false;
				  $image_source = 'This video has been deleted.';
			  }
		  $result_arr['result'] = 'success';
		  $result_arr['s_image_source'] = $image_source;		
		  $result_arr['i_media_id'] = $i_media_id;
		  echo json_encode($result_arr );
			  
		} 
		catch(Exception $err_obj)
			{
			} 
			
	 }
	
	##END  SHOWING WALL VIDEOS ##
	public function newsfeed_pagination_show_more($i_profile_id , $page=0)  {
		
        $cur_page = $page + $this->pagination_per_page;
		#echo $page = ( !empty($page) )? ($page + $this->pagination_per_page): $page;
        /*$page = $page + $this->pagination_per_page;
        $cur_page = $page;*/
		
		$data = $this->data;
		
        # echo $page ."--". $this->pagination_per_page ."<br />";
		$result = $this->data_newsfeed_model->get_newsfeeds_by_user_id($i_profile_id, $page, $this->pagination_per_page );
		//echo $this->db->last_query();
		$total_rows = $this->data_newsfeed_model->get_total_newsfeeds_by_user_id($i_profile_id);
		//pr($result,1);
		$data['result_arr'] = $result;
		$data['no_of_result'] = $total_rows;
		//$data['current_page_1'] = $page;
        $data['current_page_1'] = $cur_page;
		$data['profile_id'] = $i_profile_id;
		
		$data['show_comment_like'] = $this->data_newsfeed_model->check_public_newsfeeds_privacy_by_user_id($i_profile_id);
		
		$VIEW_FILE = "newsfeed/public_profile_feeds.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			#$content = '<div class="txt_content01"><p style="margin-left: 330px;">No Posts!</p></div>';
			$content = '';
		}
		
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$content, 'current_page'=>$cur_page) );
	}
	public function fetch_comment_on_post($i_newsfeed_id='')
	{
		try
		  {
			 $data = $this->data;  
			 
			 ob_start();
			 $this->comments_ajax_pagination($i_newsfeed_id);
			 $data['comments_list'] = ob_get_contents();
			 ob_end_clean();
			 
			  $VIEW = "logged/wall/my_wall_view_comments_lightbox.phtml";
              #parent::_render($data, $VIEW); 
			  $html = $this->load->view($VIEW, $data, true);  
			  echo json_encode( array('result'=>success,'html_data'=>$html) );
		   } 
		catch(Exception $err_obj)
			{
			  show_error($err_obj->getMessage());
			} 
			
	} 
	
	  public function comments_ajax_pagination($i_newsfeed_id , $page=0) 
	  {
		 try
		 {
		    $data = $this->data;  
			$result = $this->newsfeed_comments_model->get_by_newsfeed_id($i_newsfeed_id , $page,
																$this->comments_pagination_per_page);
		    $resultCount = count($result);
			 $total_rows = $this->newsfeed_comments_model->get_total_by_newsfeed_id($i_newsfeed_id);
			//pr($result); 		

			$this->load->library('jquery_pagination');
			$config['base_url'] = base_url()."logged/my_wall/comments_ajax_pagination/{$i_newsfeed_id}";
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
			$data['total_pages'] = ceil($total_rows/$this->comments_pagination_per_page);
		  
			 $p = ($page/$this->comments_pagination_per_page);
			 $data['current_loaded_page_no'] =  $p + 1;
			
			# rendering the view file...
			$VIEW_FILE = "logged/wall/my_wall_view_comments_lightbox_ajax.phtml";
			$this->load->view($VIEW_FILE, $data);
			//return $html;
			
		} 
        catch(Exception $err_obj)
        {
            
        } 
	
    }
	
	public function fetch_people_liked_post($i_newsfeed_id='')
	{
		try
		  {
			   $data = $this->data;  
			   
			   ob_start();
			   $this->fetch_people_liked_post_ajax($i_newsfeed_id);
			   $data['people_liked_list'] = ob_get_contents();
			   ob_end_clean();
			  
			  $VIEW = "logged/wall/liked_by_lightbox.phtml";
			  #parent::_render($data, $VIEW); 
			  $html = $this->load->view($VIEW, $data, true);  
			  echo json_encode( array('result'=>success,'html_data'=>$html) );
		   } 
		catch(Exception $err_obj)
			{
			  show_error($err_obj->getMessage());
			} 
			
	} 
	
	  public function fetch_people_liked_post_ajax($i_newsfeed_id , $page=0) 
	  {
		 try
		 {
		    $data = $this->data;  
			$result = $this->newsfeed_comments_model->get_people_liked_by_newsfeed_id($i_newsfeed_id , $page,
																$this->people_liked_pagination_per_page);
		    $resultCount = count($result);
			$total_rows = $this->newsfeed_comments_model->get_total_people_liked_by_newsfeed_id($i_newsfeed_id);
			//pr($result); 		

			$this->load->library('jquery_pagination');
			$config['base_url'] = base_url()."logged/my_wall/fetch_people_liked_post_ajax/{$i_newsfeed_id}";
			$config['total_rows'] = $total_rows;
			$config['per_page'] = $this->people_liked_pagination_per_page;
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
			
			

			$config['div'] = '#view_people_liked'; /* Here #content is the CSS selector for target DIV */
			#$config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
			#$config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */


			$this->jquery_pagination->initialize($config);
			$data['page_links'] = $this->jquery_pagination->create_links();

			
			$data['result_arr'] = $result;
			$data['no_of_result'] = $total_rows;
			$data['current_page'] = $page;
			$data['total_pages'] = ceil($total_rows/$this->people_liked_pagination_per_page);
		  
			 $p = ($page/$this->people_liked_pagination_per_page);
			 $data['current_loaded_page_no'] =  $p + 1;
			
			# rendering the view file...
			$VIEW_FILE = "logged/wall/liked_by_lightbox_ajax.phtml";
			$this->load->view($VIEW_FILE, $data);
			//return $html;
			
		} 
        catch(Exception $err_obj)
        {
            
        } 
	
    }
	
	public function delete_post($i_newsfeed_id='')
	{
	   try
		{
		   $ret_ = $this->data_newsfeed_model->delete_by_id($i_newsfeed_id);
		   
		   echo json_encode( array('result'=>'success','msg'=>'Post has been successfully deleted.') );
		 } 
	   catch(Exception $err_obj)
		{
		  show_error($err_obj->getMessage());
		} 
			
	} 
	
	###########333 wall  methods #####################3
      
  	
	public function post_comment_ajax($feed_id)     // for photo comment post
	{
		$this->load->model('media_comments_model');

		$this->load->model('users_model');
		$user_id = intval(decrypt($this->session->userdata('user_id')));
		$user_details = $this->users_model->fetch_this($user_id);

		$message = nl2br( htmlspecialchars(trim($this->input->post('message')), ENT_QUOTES, 'utf-8') );
        $_html='';
		if($message!='')
		    {
				$arr['i_media_id'] = $feed_id;
				$arr['s_media_type'] = 'photo' ;
				$arr['i_user_id'] = $user_id;
				$arr['s_contents'] = $message;
				$arr['dt_created_on'] = get_db_datetime();
				
				$this->media_comments_model->insert($arr);
				$arr['pseudo'] = $user_details['s_profile_name'];
				$data['comment'] = $arr;
				
				$data['i_media_id'] = $feed_id;
				
				
				## SENDING SYSTEM NOTIFICATION MESSAGE ###
				$media_owner_user_details = $this->user_photos_model->get_user_details_by_media_id($feed_id);
				//pr($media_owner_user_details);
				$info['i_requester_id'] = $user_id;
				$info['i_accepter_id'] = $media_owner_user_details['i_user_id'];
				$message_id = parent::media_notifications_message($info['i_requester_id'], $info['i_accepter_id'], 'photo_comment', $feed_id);
				## check if opted for this notification or not ##
				$notificaion_opt = $this->user_alert_model->check_option_user_id($info['i_accepter_id']);	
			  
				## insert noifications ####
				if($notificaion_opt['e_photo_comments_received'] == 'Y'){
					$notification_arr['i_requester_id'] = $info['i_requester_id'];
					$notification_arr['i_accepter_id'] = $info['i_accepter_id'];
					$notification_arr['s_type'] = 'photo_comment';
					$notification_arr['dt_created_on'] = get_db_datetime();
					
					$ret = $this->user_notifications_model->insert($notification_arr);
				}
				### END ##########
				
				
				## feching comments
			    ob_start();
				$this->new_photo_comments_ajax_pagination($feed_id);
				$comments_contents = json_decode(ob_get_contents()); 
				$comments_list_html = $comments_contents->html;
				$data['no_of_comments'] = $comments_contents->no_of_comments;
				ob_end_clean();
				 
				 
				 
				
				
				echo json_encode( array('success'=>'true', 'msg'=>"Comment posted successfully.",'html'=>$comments_list_html , 'no_of_result'=> $no_of_result , 'cur_page'=>$cur_page,'i_media_id'=> $data['i_media_id']) );
			}
			 else
		   {
			  echo json_encode( array('success'=>'false', 'msg'=>"Please enter some text.", 'html'=>$comments_list_html) );
		   }
		
	}
   
	 public function new_photo_comments_ajax_pagination($i_media_id , $page=0) 
	  {
		 try
		 {
		    $cur_page = $page + $this->comments_pagination_per_page;
			$data = $this->data;  
			$result = $this->media_comments_model->get_by_newsfeed_id($i_media_id , 'photo', null,
																null);
		    $resultCount = count($result);
			$total_rows = $this->media_comments_model->get_total_by_newsfeed_id($i_media_id, 'photo');
			//pr($result); 		
					
			$data['result_comments_arr'] = $result;
			$data['no_of_comments'] = $total_rows;
			$data['current_comments_page'] = $cur_page;
		    $data['i_media_id'] = $i_media_id;
			$VIEW_FILE = "public_profile/public_profile_photo/load_photo_comments_ajax.phtml";
			if( is_array($result) && count($result) ) {
				$content = $this->load->view( $VIEW_FILE , $data, true);
			}
			else {
			   $content = '';
			}
			
			//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
			echo json_encode( array('html'=>$content, 'no_of_comments'=>$data['no_of_comments'], 'current_comments_page'=>$data['current_comments_page']) );
			
		} 
        catch(Exception $err_obj)
        {
            
        } 
	
    }
    
	
	### videos  comments posting   ####################	
	
	public function post_video_comment_ajax($feed_id) 
	{
		$this->load->model('media_comments_model');

		$this->load->model('users_model');
		$user_id = intval(decrypt($this->session->userdata('user_id')));
		$user_details = $this->users_model->fetch_this($user_id);

		$message = nl2br( htmlspecialchars(trim($this->input->post('message')), ENT_QUOTES, 'utf-8') );
        $_html='';
		if($message!='')
		    {
				$arr['i_media_id'] = $feed_id;
				$arr['s_media_type'] = 'video' ;
				$arr['i_user_id'] = $user_id;
				$arr['s_contents'] = $message;
				$arr['dt_created_on'] = get_db_datetime();
				
				$this->media_comments_model->insert($arr);
				$arr['pseudo'] = $user_details['s_profile_name'];
				$data['comment'] = $arr;
				
				$data['i_media_id'] = $feed_id;
				
				
				## SENDING SYSTEM NOTIFICATION MESSAGE ###
				$media_owner_user_details = $this->my_videos_model->get_user_details_by_media_id($feed_id);
				//pr($media_owner_user_details);
				$info['i_requester_id'] = $user_id;
				$info['i_accepter_id'] = $media_owner_user_details['i_user_id'];
				$message_id = parent::media_notifications_message($info['i_requester_id'], $info['i_accepter_id'], 'video_comment', $feed_id);
				## check if opted for this notification or not ##
				$notificaion_opt = $this->user_alert_model->check_option_user_id($info['i_accepter_id']);	
			  
				## insert noifications ####
				if($notificaion_opt['e_video_comments_received'] == 'Y'){
					$notification_arr['i_requester_id'] = $info['i_requester_id'];
					$notification_arr['i_accepter_id'] = $info['i_accepter_id'];
					$notification_arr['s_type'] = 'video_comment';
					$notification_arr['dt_created_on'] = get_db_datetime();
					
					$ret = $this->user_notifications_model->insert($notification_arr);
				}
				### END ##########
				
				
				## feching comments
			    ob_start();
				$this->new_video_comments_ajax_pagination($feed_id);
				$comments_contents = json_decode(ob_get_contents()); 
				$video_comments_list_html = $comments_contents->html;
				$data['no_video_of_comments'] = $comments_contents->no_of_comments;
				ob_end_clean();
				 
				 
				 
				
				
				echo json_encode( array('success'=>'true', 'msg'=>"Comment posted successfully.",'html'=>$video_comments_list_html , 'no_of_result'=> $no_of_result , 'cur_page'=>$cur_page,'i_media_id'=> $data['i_media_id']) );
			}
			 else
		   {
			  echo json_encode( array('success'=>'false', 'msg'=>"Please enter some text.", 'html'=>$video_comments_list_html) );
		   }
		
	}
   
	 public function new_video_comments_ajax_pagination($i_media_id , $page=0) 
	  {
		 try
		 {
		    $cur_page = $page + $this->comments_pagination_per_page;
			$data = $this->data;  
			$result = $this->media_comments_model->get_by_newsfeed_id($i_media_id , 'video', null,
																null);
		    $resultCount = count($result);
			$total_rows = $this->media_comments_model->get_total_by_newsfeed_id($i_media_id, 'video');
			//pr($result); 		
					
			$data['result_video_comments_arr'] = $result;
			$data['no_of_video_comments'] = $total_rows;
			$data['current_video_comments_page'] = $cur_page;
		    $data['i_video_media_id'] = $i_media_id;
			$VIEW_FILE = "public_profile/public_profile_video/load_video_comments_ajax.phtml";
			if( is_array($result) && count($result) ) {
				$content = $this->load->view( $VIEW_FILE , $data, true);
			}
			else {
			   $content = '';
			}
			
			//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
			echo json_encode( array('html'=>$content, 'no_of_comments'=>$data['no_of_video_comments'], 'current_comments_page'=>$data['current_video_comments_page']) );
			
		} 
        catch(Exception $err_obj)
        {
            
        } 
	
    }
	
#### audio comment posting #######################


   public function fetch_audio_details($i_media_id='', $i_user_id)
	{
		try
		  {
			  $data = $this->data;  
			 
			  $s_where = " AND id = {$i_media_id}";
			  $data['arr_audio_detail'] = $this->user_audios_model->get_allaudios_with_comments_by_user_id_($i_user_id, $s_where , 0, 1);
			  
			   ## feching comments
			    ob_start();
				$this->new_audio_comments_ajax_pagination($feed_id);
				$comments_contents = json_decode(ob_get_contents()); 
				$audio_comments_list_html = $comments_contents->html;
				$data['no_audio_of_comments'] = $comments_contents->no_of_comments;
				ob_end_clean();
			  
			 #pr($data['arr_audio_detail'],1);
			  $VIEW = "public_profile/public_profile_audio/audio_details_lightbox.phtml";
              #parent::_render($data, $VIEW); 
			  $html = $this->load->view($VIEW, $data, true);  
			  echo json_encode( array('result'=>success,'html_data'=>$html) );
		   } 
		catch(Exception $err_obj)
			{
			  show_error($err_obj->getMessage());
			} 
			
	 } 


	
	public function post_audio_comment_ajax($feed_id) 
	{
		$this->load->model('media_comments_model');

		$this->load->model('users_model');
		$user_id = intval(decrypt($this->session->userdata('user_id')));
		$user_details = $this->users_model->fetch_this($user_id);

		$message = nl2br( htmlspecialchars(trim($this->input->post('message')), ENT_QUOTES, 'utf-8') );
        $_html='';
		if($message!='')
		    {
				$arr['i_media_id'] = $feed_id;
				$arr['s_media_type'] = 'audio' ;
				$arr['i_user_id'] = $user_id;
				$arr['s_contents'] = $message;
				$arr['dt_created_on'] = get_db_datetime();
				
				$this->media_comments_model->insert($arr);
				$arr['pseudo'] = $user_details['s_profile_name'];
				$data['comment'] = $arr;
				
				$data['i_media_id'] = $feed_id;
				
				
				## SENDING SYSTEM NOTIFICATION MESSAGE ###
				$media_owner_user_details = $this->user_audios_model->get_user_details_by_media_id($feed_id);
				//pr($media_owner_user_details);
				$info['i_requester_id'] = $user_id;
				$info['i_accepter_id'] = $media_owner_user_details['i_user_id'];
				$message_id = parent::media_notifications_message($info['i_requester_id'], $info['i_accepter_id'], 'audio_comment', $feed_id);
				## check if opted for this notification or not ##
				$notificaion_opt = $this->user_alert_model->check_option_user_id($info['i_accepter_id']);	
			  
				## insert noifications ####
				if($notificaion_opt['e_video_comments_received'] == 'Y'){
					$notification_arr['i_requester_id'] = $info['i_requester_id'];
					$notification_arr['i_accepter_id'] = $info['i_accepter_id'];
					$notification_arr['s_type'] = 'audio_comment';
					$notification_arr['dt_created_on'] = get_db_datetime();
					
					$ret = $this->user_notifications_model->insert($notification_arr);
				}
				### END ##########
				
				
				## feching comments
			    ob_start();
				$this->new_audio_comments_ajax_pagination($feed_id);
				$comments_contents = json_decode(ob_get_contents()); 
				$audio_comments_list_html = $comments_contents->html;
				$data['no_audio_of_comments'] = $comments_contents->no_of_comments;
				ob_end_clean();
				 
				 
				 
				
				
				echo json_encode( array('success'=>'true', 'msg'=>"Comment posted successfully.",'html'=>$audio_comments_list_html , 'no_of_result'=> $no_of_result , 'cur_page'=>$cur_page,'i_media_id'=> $data['i_media_id']) );
			}
			 else
		   {
			  echo json_encode( array('success'=>'false', 'msg'=>"Please enter some text.", 'html'=>$video_comments_list_html) );
		   }
		
	}
   
	 public function new_audio_comments_ajax_pagination($i_media_id , $page=0) 
	  {
		 try
		 {
		    $cur_page = $page + $this->comments_pagination_per_page;
			$data = $this->data;  
			$result = $this->media_comments_model->get_by_newsfeed_id($i_media_id , 'audio', null,
																null);
		    $resultCount = count($result);
			$total_rows = $this->media_comments_model->get_total_by_newsfeed_id($i_media_id, 'audio');
			//pr($result); 		
					
			$data['result_audio_comments_arr'] = $result;
			$data['no_of_audio_comments'] = $total_rows;
			$data['current_audio_comments_page'] = $cur_page;
		    $data['i_audio_media_id'] = $i_media_id;
			$VIEW_FILE = "public_profile/public_profile_audio/load_audio_comments_ajax.phtml";
			if( is_array($result) && count($result) ) {
				$content = $this->load->view( $VIEW_FILE , $data, true);
			}
			else {
			   $content = '';
			}
			
			//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
			echo json_encode( array('html'=>$content, 'no_of_comments'=>$data['no_of_audio_comments'], 'current_comments_page'=>$data['current_audio_comments_page']) );
			
		} 
        catch(Exception $err_obj)
        {
            
        } 
	
    }	
	
	public function NEW_fetch_comment_post($i_newsfeed_id='')
	{
		try
		  {
			  $data = $this->data; 
			  $html  = ''; 
			  $result = $this->newsfeed_comments_model->get_by_newsfeed_id($i_newsfeed_id );
			
			//pr($result);
			  if(count($result)){
				  
				  foreach($result as $key=> $val){
					  
					 $profile_image_filename = get_profile_image_of_user('thumb',$val['s_profile_photo'],$val['e_gender']);
			 		 $DESC = html_entity_decode(htmlspecialchars_decode($val['s_contents']),ENT_QUOTES,'utf-8');
					 $profile_link = get_profile_url($val['i_user_id'],$val['s_profile_name']);
					
					 $html .= '<div class="txt_content01 comments-number-content "> 
					 			<a href="'.$profile_link.'"><div style="background:url('.$profile_image_filename.') no-repeat center;width:60px; height:60px;" class="pro_photo" ></div></a>
									<div class="left-nw-wal">
										  <p class="blue_bold12"><a href="javascript:void(0);">'.$val['s_profile_name'].'</a></p>
										  <p>'.nl2br($DESC).'</p>
											 <p class="read-more">Updated on: '.get_time_elapsed($val['dt_created_on']).'</p>
									</div>
									<div class="clr"></div>
							  </div>'; 
							  
						
				  }
			  }
			  else{
				  $html .= '     <div class="txt_content01 comments-number-content" > 
										<div style="text-align:center;"><p>No Comments.</p></div>
										</div>
										';
			  }
			 
			  echo json_encode( array('result'=>'success','html_data'=>$html) );
		   } 
			catch(Exception $err_obj)
			{
			  show_error($err_obj->getMessage());
			} 
			
	} 
	
	public function new_fetch_likes_on_post($i_newsfeed_id='')
	{
		try
		  {
			  $data = $this->data; 
			  $html = ''; 
			 $result = $this->newsfeed_comments_model->get_people_liked_by_newsfeed_id($i_newsfeed_id);
			  
			  //pr($result);
			  
			  if(count($result)){
				  foreach($result as $key=> $val){
					  
						 $name = $val['s_profile_name'];
						 $profile_image = get_profile_image_of_user('thumb',$val['s_profile_photo'],$val['e_gender']);
						 
						 $profile_link = get_profile_url($val['i_user_id'],$val['s_profile_name']);
						
						$html .= '     <div class="user_div dp-list-user"> 
											<a href="'.$profile_link.'">
											<div class="pro_photo3" style="background:url('.$profile_image.') no-repeat center;width:60px; height:60px;"></div></a> 
											<a href="javascript:void(0);" class="blue_link">'.$name.'</a> 
										</div>
										';
				  }
				  $html .= '<br class="clr" />';
			  }
			  else{
				  $html .= '     <div class="user_div" style="width:455px;"> 
										<div style="text-align:center;"><p>No Likes.</p></div>
										</div>
										';
			  }
			  
			 
			  echo json_encode( array('result'=>'success','html_data'=>$html) );
		   } 
			catch(Exception $err_obj)
			{
			  show_error($err_obj->getMessage());
			} 
			
	} 
	
	
	# index function definition...
    public function all_friends($id='') 
    {
        try
        { 
			$posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/lightbox.js',
										'js/tab.js',
										'js/jquery.autofill.js',
										'js/jquery.lightbox.js',*/
										//'js/frontend/public_profile.js',
										'js/frontend/logged/my_friends.js',
										'js/frontend/logged/my_net_pals.js',
										'js/frontend/logged/my_prayer_partner.js',
										'js/frontend/logged/message_box/my_message.js',
										'js/production/tweet_utilities.js',
										//'js/stepcarousel.js'
                                        ));
                                        
       //     parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css') );
										
		 
                                        
                                        
            # adjusting header & footer sections [End]...
			$data['page_view_type'] = 'public_account';
			
			$i_profile_id = intval($id);
			
			$public_arr_profile_info = $this->users_model->fetch_this($i_profile_id);
			$data['public_arr_profile_info'] = $public_arr_profile_info;
			$data['user_education_arr'] = $public_arr_profile_info['education_arr'];
			$data['user_work_arr'] = $public_arr_profile_info['work_arr'];
			$data['user_skill_arr'] = $public_arr_profile_info['skill_arr'];
			
		    //pr($data['arr_profile_info']);
            
            
            $data['i_profile_id'] = $i_profile_id;
			$data['view_name'] = 'Friends';
			$data['friend_pagination_per_page'] = $this->friend_pagination_per_page;
			
			
			## checking friend relationship with logged user ##
			
			$logged_user_id = intval(decrypt($this->session->userdata('user_id')));
			
			if($logged_user_id != $i_profile_id){
				
				# CHECKING IS FRIEND ALREADY #
				$is_friend_arr =$this->users_model->if_already_friend($i_profile_id,$logged_user_id);
				if(count($is_friend_arr) >0 ){
					$data['if_already_friend'] = 'true';
				}
				# checking friend request already sent
				else{
					$is_friend_req_alrdy_snt = $this->users_model->friend_request_already_sent($logged_user_id,$i_profile_id);
					#pr($is_friend_req_alrdy_snt);
					if($is_friend_req_alrdy_snt){
						$data['display_becomefriend']     ='false';
						$data['if_already_friend']     ='';
					}else{
					    $data['if_already_friend']     ='false';
					}
				}
				
				# CHECKING IS Netpals ALREADY #
				$is_netpal_arr =$this->netpals_model->if_already_netpal($i_profile_id,$logged_user_id);
				if(count($is_netpal_arr) >0 ){
					$data['if_already_netpals'] = 'true';
				}
				# checking friend request already sent
				else{
					$is_netpal_req_alrdy_snt = $this->netpals_model->netpals_request_already_sent($logged_user_id,$i_profile_id);
					#pr($is_friend_req_alrdy_snt);
					if($is_netpal_req_alrdy_snt){
						$data['display_becomenetpals']     ='false';
						$data['if_already_netpals']     ='';
					}else{
					    $data['if_already_netpals']     ='false';
					}
				}
				$reffer_name =  basename($_SERVER['HTTP_REFERER']);
				
				$is_hit_frm_pp = strpos($reffer_name ,'prayer-partner');
				
			    ## IF HIT FROM PP PARNER SECTION THEN SHOW PRAYER PARTNER INVITATION SCOPE ##
				if($is_hit_frm_pp){
					$data['is_hit_frm_pp'] = $is_hit_frm_pp;
				}
				
				if($is_hit_frm_pp){
					### CHECK ALREADY PRAYER PARTNERS. ##
			  
					$get_friend_req_sent_status_me_him = $this->my_prayer_partner_model->get_prayer_partner_status_me_him(
																						$logged_user_id , $i_profile_id);
			  
					if(count($get_friend_req_sent_status_me_him) > 0  ) { 
						 $data['prayer_partner']['display_becomeprayer_partner']     ='false';
					 }
							  
					 
					$get_friend_status_me_him = $this->my_prayer_partner_model->get_prayer_partner_accepted_me_him(
														  $logged_user_id , $i_profile_id);
					if(count($get_friend_status_me_him) > 0  ) { 
						   $data['prayer_partner']['display_alreadyprayer_partner']     ='true';
					 }
							  
					$total_PP_arr = $this->my_prayer_partner_model->get_prayerPartnerId_by_user_id($i_profile_id);
					$total_PP = count($total_PP_arr);
					$total_pending_PP_req = $this->my_prayer_partner_model->total_pending_prayer_partner_recieved($i_profile_id);
					## CHECKING TOTAL PRAYER PARTNERS ##
					if($total_PP == 3 || $total_pending_PP_req >= 5){
						   $data['prayer_partner']['is_available']     ='false';		  
					}
					else{
							  $data['prayer_partner']['is_available']     ='true';					 
					 }
			  		 ## CHECKING TOTAL PRAYER PARTNERS ##
				}
				
				## END IF HIT FROM PP PARNER SECTION THEN SHOW PRAYER PARTNER INVITATION SCOPE ##
				
				
				
				$data['own_profile']     ='false';
			}else{
				$data['own_profile']     ='true';
			}
			
			
		
				
				
			##### message privacy ########
			$data['show_msg'] = getMsgPrivacy($i_profile_id, $logged_user_id,  $this->data['right_panel']['friends_arr'],
									$this->data['right_panel']['netpals_arr'],$this->data['right_panel']['prayer_partner_arr']);
			##### message privacy ########	
			if($logged_user_id == $i_profile_id)
			{
				$data['show_msg'] = false;
			}
          	## setting friends ##
		
			ob_start();
			$this->all_friends_ajax_pagination($i_profile_id);
			$content = ob_get_contents();
			$content_obj = json_decode($content); 
			$data['friend_content'] = $content_obj->html; 
			$data['total'] = $content_obj->total;
			ob_end_clean();
			
            # view file...
			$VIEW_PG_FILE = "all-friends.phtml";
			//$VIEW_PG_FILE = "public_profile_new.phtml";
            $VIEW = "{$VIEW_PG_FILE}";
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           show_error($err_obj->getMessage());
        } 

    }
    
	
	 public function all_netpals($id='') 
    {
        try
        { 
			$posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/lightbox.js',
										'js/tab.js',
										'js/jquery.autofill.js',
										'js/jquery.lightbox.js',*/
										//'js/frontend/public_profile.js',
										'js/frontend/logged/my_friends.js',
										'js/frontend/logged/my_net_pals.js',
										'js/frontend/logged/my_prayer_partner.js',
										'js/frontend/logged/message_box/my_message.js',
										'js/production/tweet_utilities.js',
									//	'js/stepcarousel.js'
                                        ));
                                        
        //    parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css') );

		 
                                        
                                        
            # adjusting header & footer sections [End]...
			$data['page_view_type'] = 'public_account';
			
			$i_profile_id = intval($id);
			
			$public_arr_profile_info = $this->users_model->fetch_this($i_profile_id);
			$data['public_arr_profile_info'] = $public_arr_profile_info;
			$data['user_education_arr'] = $public_arr_profile_info['education_arr'];
			$data['user_work_arr'] = $public_arr_profile_info['work_arr'];
			$data['user_skill_arr'] = $public_arr_profile_info['skill_arr'];
			
		    //pr($data['arr_profile_info']);
            
            
            $data['i_profile_id'] = $i_profile_id;
			$data['view_name'] = 'Netpals';
			$data['friend_pagination_per_page'] = $this->friend_pagination_per_page;
			
			## checking friend relationship with logged user ##
			
			$logged_user_id = intval(decrypt($this->session->userdata('user_id')));
			
			if($logged_user_id != $i_profile_id){
				
				# CHECKING IS FRIEND ALREADY #
				$is_friend_arr =$this->users_model->if_already_friend($i_profile_id,$logged_user_id);
				if(count($is_friend_arr) >0 ){
					$data['if_already_friend'] = 'true';
				}
				# checking friend request already sent
				else{
					$is_friend_req_alrdy_snt = $this->users_model->friend_request_already_sent($logged_user_id,$i_profile_id);
					#pr($is_friend_req_alrdy_snt);
					if($is_friend_req_alrdy_snt){
						$data['display_becomefriend']     ='false';
						$data['if_already_friend']     ='';
					}else{
					    $data['if_already_friend']     ='false';
					}
				}
				
				# CHECKING IS Netpals ALREADY #
				$is_netpal_arr =$this->netpals_model->if_already_netpal($i_profile_id,$logged_user_id);
				if(count($is_netpal_arr) >0 ){
					$data['if_already_netpals'] = 'true';
				}
				# checking friend request already sent
				else{
					$is_netpal_req_alrdy_snt = $this->netpals_model->netpals_request_already_sent($logged_user_id,$i_profile_id);
					#pr($is_friend_req_alrdy_snt);
					if($is_netpal_req_alrdy_snt){
						$data['display_becomenetpals']     ='false';
						$data['if_already_netpals']     ='';
					}else{
					    $data['if_already_netpals']     ='false';
					}
				}
				$reffer_name =  basename($_SERVER['HTTP_REFERER']);
				
				$is_hit_frm_pp = strpos($reffer_name ,'prayer-partner');
				
			    ## IF HIT FROM PP PARNER SECTION THEN SHOW PRAYER PARTNER INVITATION SCOPE ##
				if($is_hit_frm_pp){
					$data['is_hit_frm_pp'] = $is_hit_frm_pp;
				}
				
				if($is_hit_frm_pp){
					### CHECK ALREADY PRAYER PARTNERS. ##
			  
					$get_friend_req_sent_status_me_him = $this->my_prayer_partner_model->get_prayer_partner_status_me_him(
																						$logged_user_id , $i_profile_id);
			  
					if(count($get_friend_req_sent_status_me_him) > 0  ) { 
						 $data['prayer_partner']['display_becomeprayer_partner']     ='false';
					 }
							  
					 
					$get_friend_status_me_him = $this->my_prayer_partner_model->get_prayer_partner_accepted_me_him(
														  $logged_user_id , $i_profile_id);
					if(count($get_friend_status_me_him) > 0  ) { 
						   $data['prayer_partner']['display_alreadyprayer_partner']     ='true';
					 }
							  
					$total_PP_arr = $this->my_prayer_partner_model->get_prayerPartnerId_by_user_id($i_profile_id);
					$total_PP = count($total_PP_arr);
					$total_pending_PP_req = $this->my_prayer_partner_model->total_pending_prayer_partner_recieved($i_profile_id);
					## CHECKING TOTAL PRAYER PARTNERS ##
					if($total_PP == 3 || $total_pending_PP_req >= 5){
						   $data['prayer_partner']['is_available']     ='false';		  
					}
					else{
							  $data['prayer_partner']['is_available']     ='true';					 
					 }
			  		 ## CHECKING TOTAL PRAYER PARTNERS ##
				}
				
				## END IF HIT FROM PP PARNER SECTION THEN SHOW PRAYER PARTNER INVITATION SCOPE ##
				
				
				
				$data['own_profile']     ='false';
			}else{
				$data['own_profile']     ='true';
			}
			
			## setting friends ##
			
		    ##### message privacy ########
			$data['show_msg'] = getMsgPrivacy($i_profile_id, $logged_user_id,  $this->data['right_panel']['friends_arr'],
									$this->data['right_panel']['netpals_arr'],$this->data['right_panel']['prayer_partner_arr']);
			##### message privacy ########	
			if($logged_user_id == $i_profile_id)
			{
				$data['show_msg'] = false;
			}
			
		
			ob_start();
			$this->all_netpals_ajax_pagination($i_profile_id);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['friend_content'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
			
            # view file...
			$VIEW_PG_FILE = "all-friends.phtml";
			//$VIEW_PG_FILE = "public_profile_new.phtml";
            $VIEW = "{$VIEW_PG_FILE}";
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           show_error($err_obj->getMessage());
        } 

    }
	
	
	public function all_friends_ajax_pagination($i_user_id, $page=0)
    {
		
		$cur_page = $page + $this->friend_pagination_per_page;
		$data = $this->data;
		$this->load->model('contacts_model');
		
		$WHERE = " WHERE 
						1
						AND c.s_status = 'accepted' 
						AND u.i_status=1 
						AND
						((c.i_requester_id = '".$i_user_id."' AND u.id=c.i_accepter_id ) 
						OR (c.i_accepter_id = '".$i_user_id."' AND u.id=c.i_requester_id ))  GROUP BY u.id "	;	
			  
		$ORDER_BY = "u.id DESC";	
		
		$result = $this->contacts_model->fetch_multi_online_friends($WHERE, intval($page),$this->friend_pagination_per_page,$ORDER_BY);	
			  
       
		$total_rows = $this->contacts_model->gettotal_online_friends($WHERE);
		
		
		$data['CSS_ajax'] = ($page % 6 == 0 && $page != 0 )?'style="clear:both;"':'';
		$data['friends_arr'] = $result;
		$data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
		$data['profile_id'] = $i_user_id;
		
		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->friend_pagination_per_page)
			  $view_more = false;
         //--------- end check
		
		
		$VIEW_FILE = "ajax/all_friends_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
        echo json_encode( array('html'=>$content, 'current_page'=>$cur_page, 'no_of_result'=>$data['no_of_result'],'total'=>$total_rows,'view_more'=>$view_more  ,'cur_page'=>$data['current_page_1']) );
			
	}
	
	public function all_netpals_ajax_pagination($i_user_id, $page=0)
    {
		
		$cur_page = $page + $this->friend_pagination_per_page;
		$data = $this->data;
		$this->load->model('netpals_model');
		
		  $WHERE = " WHERE 
                        1
                        
                        AND u.i_status=1 
                        
                        AND ((c.i_requester_id = ".$i_user_id." AND u.id=c.i_accepter_id) 
                            OR 
                        (c.i_accepter_id=".$i_user_id." AND u.id=c.i_requester_id))
                        AND c.s_status='accepted'  GROUP BY u.id "    ;   
              
         $ORDER_BY = "u.id DESC";   
		
		$result = $this->netpals_model->fetch_multi_online_netpals($WHERE, intval($page),$this->friend_pagination_per_page,$ORDER_BY);		  
       
		$total_rows = $this->netpals_model->gettotal_online_netpals($WHERE);
		
		$data['friends_arr'] = $result;
		$data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
		$data['profile_id'] = $i_user_id;
		
		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->friend_pagination_per_page)
			  $view_more = false;
         //--------- end check
		
		
		$VIEW_FILE = "ajax/all_friends_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
        echo json_encode( array('html'=>$content, 'current_page'=>$cur_page, 'no_of_result'=>$data['no_of_result'],'total'=>$total_rows,'view_more'=>$view_more  ,'cur_page'=>$data['current_page_1']) );
			
	}
	
	
	public function giveProfileRating($i_profile_id, $rate)
	{
	     $info = array();
		 $info['i_user_id'] = $i_profile_id;
		 $info['i_rate'] = $rate ;
		 $info['i_given_by_user_id'] =  intval(decrypt($this->session->userdata('user_id')));
		 $info['dt_created_on'] = get_db_datetime(); ;
		 
		 $this->users_model->insert_rating($info);
		 ### get rating ####
		 $html = show_profile_rating($i_profile_id);
		 
		 echo json_encode( array('result'=>'success','msg'=>'Rating given Successfully.', 'html'=>$html));
		 exit;
	} 
	
	
	
	public function abuseProfile($i_profile_id)
	{
	     
		 $message = nl2br( htmlspecialchars(trim($this->input->post('s_reason')), ENT_QUOTES, 'utf-8') );
		 #### insert abuse report ######
		 $info = array();
		 $info['i_referenced_id'] = $i_profile_id;
		 $info['s_reason'] = $message ;
		 $info['e_type'] = 'user' ;
		 $info['i_given_by_user_id'] =  intval(decrypt($this->session->userdata('user_id')));
		 $info['i_abuser_name'] = $i_profile_id;
		 $info['dt_created_on'] = get_db_datetime(); ;
		 $this->abuse_model->insert($info);
		 #### insert abuse report ######
			
		  $stype = 'user profile'	;
		  $type_name = get_username_by_id($i_profile_id);
		  $sender_name = get_username_by_id($info['i_given_by_user_id']);
		  ### Send Mail ####
		  $this->load->helper('html');
					$this->load->library('email');
					 $email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
				$this->email->initialize($email_setting);
		  $this->load->model('mail_contents_model');
		  $mail_info = $this->mail_contents_model->get_by_name("report_abuse");
                  /*----------------------------*/
                   $against = get_username_by_id($info['i_abuser_name']);
                    $body="<p> Hello admin@cogtime.com<p>,
                            
                            
                            <p>$sender_name report abuse against $against for  profile </p>
                             <br>
                            <p>Thanks</p>
                               <p>$sender_name</p>";
                   parent::admin_send_message( $info['i_given_by_user_id'],'report','',$body);
                  /*-----------------------------*/
                  
		  $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
		  
		  $body = sprintf3( $body, array( 'sender_name'=>$sender_name,
					 						'type'=>$stype,
					 						'type_name' => $type_name,
											'content'=>$message));
					 
		  
		  $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
		  $subject =  sprintf3( $subject, array('sender_name'=>$sender_name,
		  									'type' => $stype));
		  $arr['subject']   =  $subject;
		  
		  $arr['to']         = 'admin@cogtime.com';
		  //$arr['bcc']    = 'aradhana.online19@gmail.com';
		  
		  $arr['from_email'] = get_useremail_by_id($info['i_given_by_user_id']);
		  $arr['from_name'] = $sender_name;
		  $arr['message'] = $body;
		  $this->email->from( $arr['from_email'], $arr['from_name']);
                #dump($arr);
				$this->email->subject($arr['subject']);
						
				$this->email->to($arr['to']);
				$this->email->bcc($arr['bcc']);
				$this->email->message("$body");
                        //send_mail($arr);
				$this->email->send();
		 // send_mail($arr);
		  ### Send Mail ####
		 
		 echo json_encode( array('result'=>'success','msg'=>'Abuse report sent successfully.', 'html'=>$html));
		 exit;
	} 
	
	
	public function abuseMedia($id, $type)
	{
	     //echo $type;exit;
		 $message = nl2br( htmlspecialchars(trim($this->input->post('s_reason')), ENT_QUOTES, 'utf-8') );
		 #### insert abuse report ######
		 $info = array();
		 $info['i_referenced_id'] = $id;
		 $info['s_reason'] = $message ;
		 $info['e_type'] = $type ;
		 $info['i_given_by_user_id'] =  intval(decrypt($this->session->userdata('user_id')));
		 $info['dt_created_on'] = get_db_datetime(); ;
                 
		// $this->abuse_model->insert($info);
		 #### insert abuse report ######
		
		  ### Send Mail ####
  		  $sender_name = get_username_by_id($info['i_given_by_user_id']);
                  
                  
		  
		  if($type == 'user'){
		  	$stype = 'user profile'	;
		  	$type_name = get_username_by_id($id);
			$info['i_abuser_name'] = get_username_by_id($id);
                        /*---------------------------*/
                  $against =  get_username_by_id($id);
                         $body="<p> Hello admin@cogtime.com<p>,
                             <p>$sender_name report abuse against $against for  $stype </p>
                             <br>
                            <p>Thanks</p>
                               <p>$sender_name</p>";
                   parent::admin_send_message( $info['i_given_by_user_id'],'report','',$body); 
                 /*--------------------------------*/
                        
                        
		  }
		  else if($type == 'audio'){
			$stype = 'user audio'	;
		  	$type_name = get_audio_title($id);
			$info['i_abuser_name'] = get_audio_owner_name_by_id($id);
                        /*************************************/
                         $against =  get_audio_owner_name_by_id($id);
                  $body="<p> Hello admin@cogtime.com<p>,
                             <p>$sender_name report abuse against $against for  $type </p>
                             <br>
                            <p>Thanks</p>
                               <p>$sender_name</p>";
                   parent::admin_send_message( $info['i_given_by_user_id'],'report','',$body);
                        /*************************************/
		  }
		  else if($type == 'video'){
			$stype = 'user video'	;
		  	$type_name = get_video_title($id);
			$info['i_abuser_name'] = get_video_owner_name_by_id($id);
                        
                        /*******************************************/
                         $against =  get_video_owner_name_by_id($id);
                          $body="<p> Hello admin@cogtime.com<p>,
                             <p>$sender_name report abuse against $against for  $type </p>
                             <br>
                            <p>Thanks</p>
                               <p>$sender_name</p>";
                   parent::admin_send_message( $info['i_given_by_user_id'],'report','',$body);
                        /********************************************/
		  }
		  else if($type == 'photo'){
			$stype = 'user photo'	;
		  	$type_name = get_photo_title($id);
			$info['i_abuser_name'] = get_photo_owner_name_by_id($id);
                        /***********************************************/
                         $against =  get_photo_owner_name_by_id($id);
                          $body="<p> Hello admin@cogtime.com<p>,
                             <p>$sender_name report abuse against $against for $type </p>
                             <br>
                            <p>Thanks</p>
                               <p>$sender_name</p>";
                   parent::admin_send_message( $info['i_given_by_user_id'],'report','',$body);
                        /***********************************************/
		  }
		  else if($type == 'wall'){
			$stype = 'wall'	;
		  	$type_name = '';
			$info['i_abuser_name'] = get_username_by_id($this->input->post('abuser'));
                        /*********************************************/
                        $against =  get_username_by_id($this->input->post('abuser'));
                         $body="<p> Hello admin@cogtime.com<p>,
                            <p>$sender_name report abuse against $against for  $type </p>
                             <br>
                            <p>Thanks</p>
                               <p>$sender_name</p>";
                   parent::admin_send_message( $info['i_given_by_user_id'],'report','',$body);
                        /*********************************************/
		  }
		  else if($type == 'event'){
			  
			$this->load->model('events_model'); 
			$stype = 'event'	;
		  	$type_name = $this->events_model->get_event_title_id($id);
			$event_info = get_event_detail_by_id($id);								
			$user_name = ($event_info['i_user_type'] == 2)?get_admin_nameby_id($event_info['i_host_id']):get_username_by_id($event_info['i_host_id']);
			$info['i_abuser_name'] = $user_name;
                        /******************************************/
                        $against =  $user_name;
                          $body="<p> Hello admin@cogtime.com<p>,
                             <p>$sender_name report abuse against $against for  $type </p>
                             <br>
                            <p>Thanks</p>
                               <p>$sender_name</p>";
                   parent::admin_send_message( $info['i_given_by_user_id'],'report','',$body);
                        /*****************************************/
		  }
		  else if($type == 'blog'){
			  
			//$this->load->model('events_model'); 
			$stype = 'blog'	;
			//$blog_info = get_blog_name_by_id($id);	
		  	$type_name = get_blog_name_by_id($id);
			//$event_info = get_blog_detail_by_id($id);								
			//$user_name = ($event_info['i_user_type'] == 2)?get_admin_nameby_id($event_info['i_host_id']):get_username_by_id($event_info['i_host_id']);
			$info['i_abuser_name'] = get_username_by_id($this->input->post('abuser'));
                        /*****************************************/
                         $against=get_username_by_id($this->input->post('abuser'));
                          $body="<p> Hello admin@cogtime.com<p>,
                             <p>$sender_name report abuse against $against for  $type </p>
                             <br>
                            <p>Thanks</p>
                               <p>$sender_name</p>";
                   parent::admin_send_message( $info['i_given_by_user_id'],'report','',$body);
                        /*****************************************/
                        
		  }
		  else if($type == 'article'){
			  
			//$this->load->model('events_model'); 
			$stype = 'blog'	;
			$ar_info = get_article_deatil_by_id($id);	
		  	$type_name = $ar_info['s_post_title'];
			//$event_info = get_blog_detail_by_id($id);								
			//$user_name = ($event_info['i_user_type'] == 2)?get_admin_nameby_id($event_info['i_host_id']):get_username_by_id($event_info['i_host_id']);
			$info['i_abuser_name'] = get_username_by_id($this->input->post('abuser'));
                        
                        /****************************************/
                         $against=get_username_by_id($this->input->post('abuser'));
                          $body="<p> Hello admin@cogtime.com<p>,
                            <p>$sender_name report abuse against $against for  $type </p>
                             <br>
                            <p>Thanks</p>
                               <p>$sender_name</p>";
                   parent::admin_send_message( $info['i_given_by_user_id'],'report','',$body);
                        /****************************************/
		  }
		  
		  else if($type == 'ring'){
			  
			//$this->load->model('ring_post_model'); 
			$stype = 'ring'	;
			$ar_info = get_ring_post_deatil_by_id($id);	
		  	$type_name = $ar_info['s_post_title'];						
			//$user_name = ($event_info['i_user_type'] == 2)?get_admin_nameby_id($event_info['i_host_id']):get_username_by_id($event_info['i_host_id']);
			$info['i_abuser_name'] = get_username_by_id($this->input->post('abuser'));
                        /*****************************/
                         $against = get_username_by_id($this->input->post('abuser'));
                          $body="<p> Hello admin@cogtime.com<p>,
                             <p>$sender_name report abuse against $against for  $type </p>
                             <br>
                            <p>Thanks</p>
                               <p>$sender_name</p>";
                   parent::admin_send_message( $info['i_given_by_user_id'],'report','',$body);
                        /*****************************/
		  }
		  
		   else if($type == 'tweet'){
			  
			//$this->load->model('ring_post_model'); 
			$stype = 'ring'	;
			$ar_info = get_tweet_detail_by_id($id);	
			$data_arr=json_decode($ar_info['data']);
		  	$type_name = $data_arr->s_tweet_text ;						
			//$user_name = ($event_info['i_user_type'] == 2)?get_admin_nameby_id($event_info['i_host_id']):get_username_by_id($event_info['i_host_id']);
			$info['i_abuser_name'] = get_username_by_id($this->input->post('abuser'));
                        /********************************************/
                         $against= get_username_by_id($this->input->post('abuser'));
                          $body="<p> Hello admin@cogtime.com<p>,
                           <p>$sender_name report abuse against $against for  $type </p>
                             <br>
                            <p>Thanks</p>
                               <p>$sender_name</p>";
                   parent::admin_send_message( $info['i_given_by_user_id'],'report','',$body);
                        /********************************************/
		  }
		 // pr($info,1);
		  $this->abuse_model->insert($info);
		  $this->load->helper('html');
					$this->load->library('email');
					 $email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
				$this->email->initialize($email_setting);
		  $this->load->model('mail_contents_model');
		  $mail_info = $this->mail_contents_model->get_by_name("report_abuse");
		  $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
		  
		  $body = sprintf3( $body, array( 'sender_name'=>$sender_name,
					 						'type'=>$stype,
					 						'type_name' => $type_name,
											'content'=>$message));
					 
		  
		  $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
		  $subject =  sprintf3( $subject, array('sender_name'=>$sender_name,
		  									'type' => $stype));
		  $arr['subject']   =  $subject;
		  
		  $arr['to']         = 'admin@cogtime.com';
		  
		  //$arr['bcc']    = 'aradhana.online19@gmail.com';
		  
		  $arr['from_email'] = get_useremail_by_id($info['i_given_by_user_id']);
		  $arr['from_name'] = $sender_name;
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
		 // var_dump($arr);
		  ### Send Mail ####
		
		 echo json_encode( array('result'=>'success','msg'=>'Abuse report sent successfully.', 'html'=>$html));
		 exit;
	} 
	
}   // end of controller...

