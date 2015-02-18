<?php
/*********
* Author: 
* Date  : 
* Modified By: 
* Modified Date:
* 
* Purpose:
* 
* 
* 
*/
include(APPPATH.'controllers/base_controller.php');

class My_photo_album_details extends Base_controller
{
    private $pagination_per_page =  9 ;
	private $album_pagination_per_page =  3 ;   //unused
	private $comments_pagination_per_page = 2;
	private $people_liked_pagination_per_page = 10;
	private $upload_path ;
	private $upload_tmp_path;
    
    public function __construct()
     {
        try
        {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
           
		    $this->upload_path = BASEPATH.'../uploads/user_photos/';
			$this->upload_tmp_path = BASEPATH.'../uploads/_tmp/';
			
			$this->load->helper('wall_helper');
            $this->load->model('users_model');
			$this->load->model('user_photos_model');
			$this->load->model('photo_albums_model');
			$this->load->model('media_comments_model');
			
		    # loading reqired model & helpers...
            $this->load->model('users_model');
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    
    public function index($i_album_id= '') 
    {
        try
        {
                  
            $posted=array();
            $data = $this->data;      
            $this->data["MAIN_MENU_SELECTED"] = 1;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( 
										'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',
										//'js/uploadify/jquery.uploadify.min.js'
										//'js/uploadify/jquery.uploadify.js'
                                       'uploadify/swfobject.js',
                                        'uploadify/jquery.uploadify.js',
										'js/frontend/logged/my_photo/my_photo.js',
										'js/frontend/logged/my_photo/photo_helper.js',
										'js/frontend/logged/tweets/tweet_utilities.js',
										'js/frontend/logged/my_photo/photo_details.js',
										'js/pop-up-lightbox.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css',
										  'uploadify/uploadify.css') );
										  
			############################################################
			$i_user_id = intval(decrypt($this->session->userdata('user_id')));
			$data['page_view_type'] = 'myaccount'; 
			$arr_profile_info = $this->users_model->fetch_this($i_user_id);
			parent::_set_all_photo_album_data($i_user_id);
			
			#### FETCHING PHOTOS PER USER 
			if(is_array($arr_profile_info) && !empty($arr_profile_info)){
				$data['arr_profile_info'] = $arr_profile_info;
				$s_where = " AND `i_user_id` = {$i_user_id}";
				$data['arr_albums'] = $this->photo_albums_model->get_by_album_details_id($i_album_id, $s_where, 0,1);

				$data['arr_photos'] = $this->user_photos_model->get_by_user_id($i_user_id, null , 0, $this->pagination_per_page);
				$data['current_album_id'] = $i_album_id;				
				
				
				$this->session->set_userdata('search_condition','');
				
                $data['profile_id'] = $i_user_id;
                $data['album_id'] = $i_album_id;
                $data['pagination_per_page'] = $this->pagination_per_page;
                
                
				/*ob_start();
				$this->photos_ajax_pagination($i_user_id, $i_album_id);
				$data['photo_content'] = ob_get_contents();
				ob_end_clean();
               */ 
                
                ob_start();
                $this->photos_ajax_pagination($i_user_id, $i_album_id);
                $content = ob_get_contents();
                $content_obj = json_decode($content);
                $data['photo_content'] = $content_obj->html; 
                $data['total'] = $content_obj->total;
                ob_end_clean();
				
			/*	ob_start();
				$this->photos_ajax_pagination($i_user_id,$data['page_view_type']);
				$data['result_content'] = ob_get_contents();
				ob_end_clean(); */
				
			} 
			#pr($data['arr_photos']);
          #pr($data['arr_albums']); exit;
            # view file...
            $VIEW = "logged/photos/my-photo-album-details.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }   
	
	
	
	
	public function photos_ajax_pagination($i_user_id,$i_album_id, $page=0) 
	  {

		 try
		 {
             $current_page = $page + $this->pagination_per_page;
			 //pr($this->uri->segments);
		   ## seacrh conditions : filter ############
			$WHERE_COND = '';
			 if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) :
				$s_photo = get_formatted_string(trim($this->input->post('txt_title')));
				$WHERE_COND .= ($s_photo=='')?'':" AND s_title LIKE '%".$s_photo."%' ";
				
			  $this->session->set_userdata('search_condition',$WHERE_COND);
			
			
			 endif; 
			$s_where = " AND i_photo_album_id = {$i_album_id}"; 
		 	if($this->session->userdata('search_condition') != ''){	  
		   		$s_where .= $this->session->userdata('search_condition');
			}
		   $data = $this->data;
			
		   $result = $this->user_photos_model->get_by_user_id($i_user_id, $s_where, intval($page), $this->pagination_per_page);
		   //echo $this->db->last_query();
		   $total_rows = $this->user_photos_model->get_total_by_user_id($i_user_id, $s_where);
		  //pr($result);
/*			$this->load->library('jquery_pagination');
			$config['base_url'] = base_url().'logged/my_photo_album_details/photos_ajax_pagination/'.$i_user_id.'/'.$i_album_id;
			$config['total_rows'] = $total_rows;
			$config['per_page'] = $this->pagination_per_page;
			$config['uri_segment'] = 6;
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

            $config['div'] = '#result_photo_section'; /* Here #content is the CSS selector for target DIV 
			$config['js_bind'] = "showLoading();"; /* if you want to bind extra js code  
			$config['js_rebind'] = "hideLoading();";
	
	
			$this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();
*/            


              //--- for check whether more videos are there or not
            $view_more = true;
             $rest_counter = $total_rows-$page;
             if($rest_counter<=$this->pagination_per_page)
                $view_more = false;
             
             
             //--------- end check



            $data['arr_photos'] = $result;

            $data['total_no_of_photos'] = $total_rows;
            $data['current_page'] = $current_page;
			
			$data['total_pages'] = ceil($total_rows/$this->pagination_per_page);
		  
			$p = ($page/$this->pagination_per_page);
			$data['current_loaded_page_no'] =  $p + 1;
			
			
            $VIEW_FILE = "logged/photos/load_album_photo_thumbnails.phtml";
			//$this->load->view( $VIEW_FILE , $data);
            
            
             if( is_array($result) && count($result) ) {
            $content = $this->load->view( $VIEW_FILE , $data, true);
            }
            else {
                #$content = '<div class="txt_content01"><p style="margin-left: 330px;">No Posts!</p></div>';
                $content = '';
            }
            echo json_encode( array('html'=>$content, 'current_page'=>$current_page, 'total'=>$total_rows,'view_more'=>$view_more) );
            
		} 
        catch(Exception $err_obj)
        {
            
        } 
	
    } 
	
	
	public function fetch_photo_details($i_media_id='')
	{
		try
		  {
			  $data = $this->data;  
			  $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			  
			  $s_where = " AND id = {$i_media_id}";
			  $data['arr_photo_detail'] = $this->user_photos_model->get_by_user_id($i_user_id, $s_where , 0, 1);
			  
			  
			  ## feching comments
			  	 ob_start();
				 $this->comments_ajax_pagination($i_media_id);
				 $data['comments_list'] = ob_get_contents();
				 ob_end_clean();
				 
			 //pr($data['comments_list']);
			  
			  // pr($data['arr_photo_detail']);
			  $VIEW = "logged/photos/photo-details/load_photo_details.phtml";
              #parent::_render($data, $VIEW); 
			  $html = $this->load->view($VIEW, $data, true);  
			  echo json_encode( array('result'=>success,'html_data'=>$html) );
		   } 
		catch(Exception $err_obj)
			{
			  show_error($err_obj->getMessage());
			} 
			
	 } 
	
	
	  public function comments_ajax_pagination($i_media_id , $page=0) 
	  {
		 try
		 {
		    $cur_page = $page + $this->comments_pagination_per_page;
			$data = $this->data;  
			$result = $this->media_comments_model->get_by_newsfeed_id($i_media_id , 'photo',$page,
																$this->comments_pagination_per_page);
		    $resultCount = count($result);
			$total_rows = $this->media_comments_model->get_total_by_newsfeed_id($i_media_id, 'photo');
			//pr($result); 		
					
			$data['result_arr'] = $result;
			$data['no_of_result'] = $total_rows;
			$data['current_page_1'] = $cur_page;
		    $data['i_media_id'] = $i_media_id;
			$VIEW_FILE = "logged/photos/photo-details/comments/photo_detail_comments_ajax.phtml";
		
			if( is_array($result) && count($result) ) {
				$content = $this->load->view( $VIEW_FILE , $data, true);
			}
			else {
				$content = '';
			}
			
			//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
			echo json_encode( array('html'=>$content, 'no_of_result'=>$data['no_of_result'], 'current_page'=>$data['current_page_1']) );
			
		} 
        catch(Exception $err_obj)
        {
            
        } 
	
    }
		
	
	## post comments  ##
	
	public function post_comment_ajax($feed_id) 
	{
		$this->load->model('media_comments_model');
		$this->load->model('user_alert_model');
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
				//$data['total_comments'] = $this->media_comments_model->get_total_by_newsfeed_id($feed_id);
				//$comment = $this->load->view('newsfeed/my_profile_single_feed_comment.phtml', $data, true);
                
               // $_html = ''."Comments "." (".count_photo_comment_link($feed_id, 'photo').")";
				$data['i_media_id'] = $feed_id;
				$owner_id=get_photo_ownerID_by_id($feed_id);
				if($owner_id != $user_id)
				{
				$email_opt = $this->user_alert_model->check_option_email_user_id($user_id);
						if($email_opt['e_photo_comments_received'] == 'Y'){
						
						$mail_arr['i_requester_id'] =get_username_by_id( $user_id);
						$mail_arr['i_accepter_id'] =  get_username_by_id($owner_id);
						$mail_arr['s_type'] = 'e_photo_comments_received';
						$mail_arr['s_title']=get_photo_title($feed_id);
						$mail_arr['s_url']=get_photo_detail_url($feed_id);
						$mail_id=get_useremail_by_id($owner_id);
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

					$this->email->subject($mail_arr["i_requester_id"].' commented on your photo');
					$this->email->message("$body");

					$this->email->send();
					}
				}
				  ## feching comments
			  	ob_start();
				$this->comments_ajax_pagination($feed_id);
				$content = ob_get_contents();
				$content_obj = json_decode($content); //pr($content_obj);
				$comments_list_html = $content_obj->html; 
				$no_of_result  = $content_obj->no_of_result;
				$cur_page = $content_obj->current_page;
				ob_end_clean();
				
				
				echo json_encode( array('success'=>'true', 'msg'=>"Comment posted successfully.",'html'=>$comments_list_html , 'no_of_result'=> $no_of_result , 'cur_page'=>$cur_page,'i_media_id'=> $data['i_media_id']) );
			}
			 else
		   {
			  echo json_encode( array('success'=>'false', 'msg'=>"Please enter some text.", 'html'=>$comments_list_html) );
		   }
		
	}
	
	## end post comments ##
	
	public function photo_detail($id= '') 
    {
        try
        {
                  
            $posted=array();
            $data = $this->data;      
            $this->data["MAIN_MENU_SELECTED"] = 1;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( 
										'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',
										'js/thickbox.js',
										//'js/uploadify/jquery.uploadify.min.js'
										//'js/uploadify/jquery.uploadify.js'
                                         'uploadify/swfobject.js',
                                        'uploadify/jquery.uploadify.js',
										'js/frontend/logged/my_photo/my_photo.js',
										'js/frontend/logged/my_photo/photo_helper.js',
										'js/frontend/logged/my_photo/photo_details.js',
										'js/frontend/logged/tweets/tweet_utilities.js',
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css',
										  'uploadify/uploadify.css','css/thickbox.css') );
										  
			############################################################
			$i_user_id = intval(decrypt($this->session->userdata('user_id')));
			$data['page_view_type'] = 'myaccount'; 
			$arr_profile_info = $this->users_model->fetch_this($i_user_id);
			parent::_set_all_photo_album_data($i_user_id);
			
			#### FETCHING PHOTOS PER USER 
			if(is_array($arr_profile_info) && !empty($arr_profile_info)){
				$data['arr_profile_info'] = $arr_profile_info;
				$i_user_id = intval(decrypt($this->session->userdata('user_id')));
			  
			  	$s_where = " AND id = {$id}";
			  	$arr_photo_detail = $this->user_photos_model->get_by_user_id($i_user_id, $s_where , 0, 1);
				$data['arr_photo_detail'] = $arr_photo_detail[0];
				
				$data['current_album_id'] = $i_album_id;				
				
				
				$this->session->set_userdata('search_condition','');
			  
			 	ob_start();
				$this->comments_ajax_pagination($id);
				$content = ob_get_contents();
				$content_obj = json_decode($content);
				$data['comments_list_html'] = $content_obj->html; 
				$data['no_of_result']  = $content_obj->no_of_result;
				
				$data['current_page_1'] = $content_obj->current_page;
				ob_end_clean();
				
			} 
			#pr($data['arr_photos']);
          #pr($data['arr_albums']); exit;
            # view file...
            $VIEW = "logged/photos/my-photo-details.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }   
	
	 ### function to zoom photo
   
	public function zoom_large_photo($id)
	{ 
		$data = $this->data;
		$data['photo_info'] = $this->user_photos_model->get_by_id($id);
		$this->load->view('logged/photos/photo-details/large_zoom_photo_popup.phtml', $data);
	}
	
	
	
}   // end of controller...

