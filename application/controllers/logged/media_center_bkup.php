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


class Media_center extends Base_controller
{
    
    private $pagination_per_page = 6  ;
    private $search_pagination_per_page =  2 ;
    private $popular_pagination_per_page =  10 ;
	private $comment_pagination_per_page =  10 ;
	private $like_pagination_per_page =  10 ;
    
    private $ring_members_pagination_per_page =  10 ;
	private $shouts_pagination_per_page = 10 ;
    
    public function __construct()
     {
	 	
        try
        {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...
            $this->load->model('users_model');
			$this->load->model("gospel_magazine_model");
			$this->load->model("my_blog_post_model");
			
			$this->i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    
	
	public function gospel_magazine($slab='',$id='')
	{
		try
        {
			$posted=array();
			$this->data["posted"]=$posted;/*don't change*/    
			$data = $this->data;      
			$this->data["MAIN_MENU_SELECTED"] = 6;
			parent::_set_title('::: COGTIME Xtian network :::');
			parent::_set_meta_desc('');
			parent::_set_meta_keywords('');
		
			
			parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
										'js/switch.js','js/animate-collapse.js',
										'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
										'js/stepcarousel.js',
										'js/jquery.autofill.js',
										'js/frontend/logged/tweets/tweet_utilities.js',
										'js/tab.js'
										));
										
			parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
											  'css/dd.css') );
				
				
			# view file...
			ob_start();
			$content = $this->generate_gospel_magazine_AJAX();
			$content = ob_get_contents();
			$data['listingContent'] = $content;
			ob_end_clean();
			//$data['listingContent'] = $content;
			$VIEW = "logged/media_center/gospel_magazine.phtml"; 
			parent::_render($data, $VIEW);
			
        }
        catch(Exception $err_obj)
        {
           
        } 
	}
	
	public function generate_gospel_magazine_AJAX($page=0)
    {
		$wh	= "";
		$data['gospel_magazine'] = $this->gospel_magazine_model->get_magazines($s_where ,$page,$this->pagination_per_page,$order_by);
		
		
		$data['pagination_per_page'] = $this->pagination_per_page;
		//pr($result);
		$resultCount = count($data['gospel_magazine']);
		$total_rows = $this->gospel_magazine_model->get_count_magazines($wh);
							
		//$data['versepointer']	= $this->holy_place_model->get_array_of_verse_pointer();					
		$this->load->library('jquery_pagination');
		$config['base_url'] = base_url()."logged/media_center/generate_gospel_magazine_AJAX";
		$config['total_rows'] = $total_rows;
		$config['per_page'] = $this->pagination_per_page;
		$config['uri_segment'] = 4;
		$config['num_links'] = 1;
		$config['page_query_string'] = false;
		$config['prev_link'] = 'PREV PAGE';
		$config['next_link'] = 'NEXT PAGE';
		
		$config['cur_tag_open'] = '<li class="active">';
		$config['cur_tag_close'] = '</li>';
		
		$config['next_tag_open'] = '<li class="next">';
		$config['next_tag_close'] = '</li>';
		
		$config['prev_tag_open'] = '<li class="previous">';
		$config['prev_tag_close'] = '</li>';
		
		$config['num_tag_open'] = '<li class="num_tag_link">';
		$config['num_tag_close'] = '</li>';
		
		$config['first_link'] = '';
		$config['last_link'] = '';
		
	
		
		$config['div'] = '#table_content'; /* Here #content is the CSS selector for target DIV */
		$config['js_bind'] = "showBusyScreen();"; /* if you want to bind extra js code */
		$config['js_rebind'] = "hideBusyScreen();"; /* if you want to rebind extra js code */
		
		$this->jquery_pagination->initialize($config);
		$data['page_links'] = $this->jquery_pagination->create_links();
		
		// getting   listing...
		$data['info_arr'] = $result;
		$data['no_of_result'] = $total_rows;
		$data['current_page'] = $page;
		
		$data['pagination_per_page']   =    $this->pagination_per_page;
		
		# loading the view-part...
		$AJAX_VIEW_FILE = 'logged/media_center/ajax/ajax_listing_gospel_magazine.phtml';
		echo $this->load->view($AJAX_VIEW_FILE, $data,TRUE);
    } 
	
	function gospel_magazine_detail($id)
	{
		$data = $this->data;      
		$this->data["MAIN_MENU_SELECTED"] = 6;
		parent::_set_title('::: COGTIME Xtian network :::');
		parent::_set_meta_desc('');
		parent::_set_meta_keywords('');
	
		
		parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
									'js/switch.js','js/animate-collapse.js',
									'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
									'js/stepcarousel.js',
									'js/jquery.autofill.js',
									'js/frontend/logged/tweets/tweet_utilities.js',
									'js/tab.js'
									));
									
		parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
											  'css/dd.css') );
		
		$wh	= " AND i_magazine_id='".$id."'";
		$data['total_cmt']	= $this->gospel_magazine_model->get_count_gospel_cmnts($wh);
		$data['total_like']	= $this->gospel_magazine_model->get_count_gospel_likes($wh);
		
		$wh	= " AND m.id='".$id."'";
		$data['gospel_magazine'] = $this->gospel_magazine_model->get_magazines($wh ,'','','');
		#pr($data['gospel_magazine'],1);
		$VIEW = 'logged/media_center/gospel_magazine_detail.phtml';
		parent::_render($data, $VIEW);
	}
	
	public function christan_project()
	{
		try
        {
			$posted=array();
			$this->data["posted"]=$posted;/*don't change*/    
			$data = $this->data;      
			$this->data["MAIN_MENU_SELECTED"] = 6;
			parent::_set_title('::: COGTIME Xtian network :::');
			parent::_set_meta_desc('');
			parent::_set_meta_keywords('');
		
			
			parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
										'js/switch.js','js/animate-collapse.js',
										'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
										'js/stepcarousel.js',
										'js/jquery.autofill.js',
										'js/frontend/logged/tweets/tweet_utilities.js',
										'js/tab.js'
										));
										
			parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
											  'css/dd.css') );
				
				
			# view file...
			ob_start();
			$content = $this->generate_christan_project_AJAX();
			$content = ob_get_contents();
			$data['listingContent'] = $content;
			ob_end_clean();
			//$data['listingContent'] = $content;
			$VIEW = "logged/media_center/chirstan_project.phtml"; 
			parent::_render($data, $VIEW);
			
        }
        catch(Exception $err_obj)
        {
           
        } 
	}
	
	public function generate_christan_project_AJAX($page=0)
    {
		
		$wh	= "";
		$data['gospel_magazine'] = $this->gospel_magazine_model->get_ch_project($s_where ,$page,$this->pagination_per_page,$order_by);
		
		
		$data['pagination_per_page'] = $this->pagination_per_page;
		//pr($result);
		$resultCount = count($data['gospel_magazine']);
		$total_rows = $this->gospel_magazine_model->get_count_ch_project($wh);
							
		//$data['versepointer']	= $this->holy_place_model->get_array_of_verse_pointer();					
		$this->load->library('jquery_pagination');
		$config['base_url'] = base_url()."logged/media_center/generate_christan_project_AJAX";
		$config['total_rows'] = $total_rows;
		$config['per_page'] = $this->pagination_per_page;
		$config['uri_segment'] = 4;
		$config['num_links'] = 1;
		$config['page_query_string'] = false;
		$config['prev_link'] = 'PREV PAGE';
		$config['next_link'] = 'NEXT PAGE';
		
		$config['cur_tag_open'] = '<li class="active">';
		$config['cur_tag_close'] = '</li>';
		
		$config['next_tag_open'] = '<li class="next">';
		$config['next_tag_close'] = '</li>';
		
		$config['prev_tag_open'] = '<li class="previous">';
		$config['prev_tag_close'] = '</li>';
		
		$config['num_tag_open'] = '<li class="num_tag_link">';
		$config['num_tag_close'] = '</li>';
		
		$config['first_link'] = '';
		$config['last_link'] = '';
		
	
		
		$config['div'] = '#table_content'; /* Here #content is the CSS selector for target DIV */
		$config['js_bind'] = "showBusyScreen();"; /* if you want to bind extra js code */
		$config['js_rebind'] = "hideBusyScreen();"; /* if you want to rebind extra js code */
		
		$this->jquery_pagination->initialize($config);
		$data['page_links'] = $this->jquery_pagination->create_links();
		
		// getting   listing...
		$data['info_arr'] = $result;
		$data['no_of_result'] = $total_rows;
		$data['current_page'] = $page;
		
		$data['pagination_per_page']   =    $this->pagination_per_page;
		
		# loading the view-part...
		$AJAX_VIEW_FILE = 'logged/media_center/ajax/ajax_listing_chirstan_project.phtml';
		echo $this->load->view($AJAX_VIEW_FILE, $data,TRUE);
    }
	
	function christan_project_detail($id)
	{
		$data = $this->data;      
		$this->data["MAIN_MENU_SELECTED"] = 6;
		parent::_set_title('::: COGTIME Xtian network :::');
		parent::_set_meta_desc('');
		parent::_set_meta_keywords('');
	
		
		parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
									'js/switch.js','js/animate-collapse.js',
									'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
									'js/stepcarousel.js',
									'js/jquery.autofill.js',
									'js/frontend/logged/tweets/tweet_utilities.js',
									'js/tab.js'
									));
									
		parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
											  'css/dd.css') );
		
		$wh	= " AND i_proj_id='".$id."'";
		$data['total_cmt']	= $this->gospel_magazine_model->get_count_project_cmnts($wh);
		$data['total_like']	= $this->gospel_magazine_model->get_count_project_likes($wh);
		
		
		$wh	= " AND m.id='".$id."'";
		$data['gospel_magazine'] = $this->gospel_magazine_model->get_ch_project($wh ,'','','');
		#pr($data['gospel_magazine'],1);
		$VIEW = 'logged/media_center/chirstan_project_detail.phtml';
		parent::_render($data, $VIEW);
	}
	
	/*******************************GOSPEL MAGAZINE COMMENTS AND LIKES**************************************************/
	function add_gospel_magazine_comments()
	{
		$postcmnts_txt	= $this->input->post("postcmnts_txt",true);
        $magazine_id = $this->input->post('magazine_id',true);
		
		$err	= array();
		if($postcmnts_txt=='')
			$err['err_postcmnts_txt']	= "* Required Field.";
		
		if(count($err)>0)
		{
			$return_arr	= array('success'=>0,"msg"=>'Please enter some text.');
			echo json_encode($return_arr);
			exit;
		}
		else
		{
			$cr_date	= get_db_datetime();
		
			$arr	= array("i_magazine_id" =>$magazine_id,
								"i_user_id"=>intval(decrypt($this->session->userdata('user_id'))),
								"s_comments"=>$postcmnts_txt,
								"dt_posted_date"=>$cr_date);
		
			//pr($arr);exit;
			
		    $comment_id = $this->gospel_magazine_model->add_cmnts($arr);
			$wh	= " AND i_magazine_id='".$magazine_id."'";
			$total_cmt	= $this->gospel_magazine_model->get_count_gospel_cmnts($wh);
			$return_arr	= array('success'=>1,"msg"=>"Comment posted successfully","total_comments"=>$total_cmt);
			echo json_encode($return_arr);
			exit;
		}
	}
	
	
	
	function add_gospel_magazine_like()
	{
		$magazine_id = $this->input->post('magazine_id',true);
		$cr_date	= get_db_datetime();
	
		$arr	= array("i_magazine_id" =>$magazine_id,
							"i_user_id"=>intval(decrypt($this->session->userdata('user_id'))),
							"dt_liked_on"=>$cr_date);
	
		//pr($arr);exit;
		
		$i_blog_post_comment_id = $this->gospel_magazine_model->add_likes($arr);
		if($i_blog_post_comment_id)
		{
			$wh	= " AND i_magazine_id='".$magazine_id."'";
			$total_like	= $this->gospel_magazine_model->get_count_gospel_likes($wh);
			$return_arr	= array('success'=>1,"msg"=>"Comment posted successfully","total_like"=>$total_like);
			echo json_encode($return_arr);
			exit;
		}
		else
		{
			$return_arr	= array('success'=>0,"msg"=>"");
			echo json_encode($return_arr);
			exit;
		}
	}
	
	public function fetch_comment_on_gospel_magazine($magazine_id='')
	{
		try
		  {
			 $data = $this->data;  
			 
			 ob_start();
			 $this->magazine_comments_ajax_pagination($magazine_id);
			 $data['comments_list'] = ob_get_contents();
			 ob_end_clean();
			 
			  $VIEW = "logged/media_center/my_christan_project_comments_lightbox.phtml";
              #parent::_render($data, $VIEW); 
			  $html = $this->load->view($VIEW, $data, true);  
			  echo json_encode( array('result'=>'success','html_data'=>$html) );
		   } 
			catch(Exception $err_obj)
			{
			  show_error($err_obj->getMessage());
			} 
			
	} 
	
	
	########### NEW FETCH COMMENTS METHOD ###########
	
	public function NEW_fetch_comment_gospel_magazine($magazine_id='')
	{
		try
		  {
			  $data = $this->data; 
			  $html  = ''; 
			  $wh	= " AND i_magazine_id='".$magazine_id."'";
			  $result = $this->gospel_magazine_model->get_gospel_cmnts($wh ,$page,$this->comments_pagination_per_page);
			
			  if(count($result)){
				  
				  foreach($result as $key=> $val){
					  
					 $profile_image_filename = get_profile_image($val['i_user_id'],'thumb');
			 		 $DESC = html_entity_decode(htmlspecialchars_decode($val['s_comments']),ENT_QUOTES,'utf-8');
					 
					 if($val['if_already_friend'] == 'true' || $val['already_added_netpal'] == 'true')
					  $profile_link = get_profile_url($val['post_owner_user_id'],$val['s_profile_name']);
					 else
					  $profile_link = get_public_profile_url($val['post_owner_user_id'],$val['s_profile_name']);
					 
					 $html .= '<div class="txt_content01 comments-number-content"> 
					 			<a href="'.$profile_link.'"><div style="background:url('.$profile_image_filename.') no-repeat center;width:60px; height:60px;" class="pro_photo3" ></div></a>
									<div>
										  <p class="blue_bold12"><a href="javascript:void(0);">'.$val['s_profile_name'].'</a></p>
										  <p>'.nl2br($DESC).'</p>
											 <p class="read-more">Updated on: '.get_time_elapsed($val['dt_posted_date']).'</p>
									</div>
									<div class="clr"></div>
							  </div>'; 
				  }
			  }
			  
			  $total_rows = $this->gospel_magazine_model->get_count_gospel_cmnts($wh);
			 
			  echo json_encode( array('result'=>'success','html_data'=>$html,'total_rows'=>$total_rows) );
		   } 
			catch(Exception $err_obj)
			{
			  show_error($err_obj->getMessage());
			} 
			
	} 
	
	
	
	public function magazine_comments_ajax_pagination($magazine_id , $page=0) 
	  {
		 try
		 {
		    $data = $this->data;  
			$wh	= " AND i_magazine_id='".$magazine_id."'";
			$result = $this->gospel_magazine_model->get_gospel_cmnts($wh ,$page,$this->comments_pagination_per_page);
			
		    $resultCount = count($result);
			$total_rows = $this->gospel_magazine_model->get_count_gospel_cmnts($wh);
			//pr($result); 		

			$this->load->library('jquery_pagination');
			$config['base_url'] = base_url()."logged/media_center/magazine_comments_ajax_pagination/{$magazine_id}";
			$config['total_rows'] = $total_rows;
			$config['per_page'] = $this->comment_pagination_per_page;
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

			
			$data['result_arr'] = check_friend_netpal_status($result);
			$data['no_of_result'] = $total_rows;
			$data['current_page'] = $page;
			$data['total_pages'] = ceil($total_rows/$this->comment_pagination_per_page);
		  
			 $p = ($page/$this->comment_pagination_per_page);
			 $data['current_loaded_page_no'] =  $p + 1;
			
			# rendering the view file...
			$VIEW_FILE = "logged/media_center/my_christan_project_comments_lightbox_ajax.phtml";
			$this->load->view($VIEW_FILE, $data);
			//return $html;
			
		} 
        catch(Exception $err_obj)
        {
            
        } 
	
    }
	
	##### new method to fetch magazines
	
	public function new_fetch_likes_on_magazine($magazine_id='')
	{
		try
		  {
			  $data = $this->data; 
			  $html = ''; 
			  $wh	= " AND i_magazine_id='".$magazine_id."'";
			  $result = $this->gospel_magazine_model->get_gospel_likes($wh ,$page,$this->comments_pagination_per_page);
			  
			  if(count($result)){
				  foreach($result as $key=> $val){
					  
						 $name = $val['s_profile_name'];
						 $profile_image = get_profile_image($val['i_user_id'],'thumb',$val['s_profile_photo']);
						 
						 if($val['if_already_friend'] == 'true' || $val['already_added_netpal'] == 'true'){
						  $profile_link = get_profile_url($val['post_owner_user_id'],$val['s_profile_name']);
						 }
						 else
						 {
						  $profile_link = get_public_profile_url($val['post_owner_user_id'],$val['s_profile_name']);
						 }  
						 
						$html .= '     <div class="user_div "> 
											<a href="'.$profile_link.'">
											<div class="pro_photo3" style="background:url('.$profile_image.') no-repeat center;width:60px; height:60px;"></div>
											</a> 
											<a href="javascript:void(0);" class="blue_link">'.$name.'</a> 
										</div>
										
										';
										
						if($key % 2 == 0 && $key != 0){
							$html .= '<br class="clr" />';
						}
					
				  }
			  }
			  
			 
			  echo json_encode( array('result'=>'success','html_data'=>$html) );
		   } 
			catch(Exception $err_obj)
			{
			  show_error($err_obj->getMessage());
			} 
			
	} 
	
	public function fetch_likes_on_magazine($magazine_id='')
	{
		try
		  {
			 $data = $this->data;  
			 
			 ob_start();
			 $this->magazine_likes_ajax_pagination($magazine_id);
			 $data['people_liked_list'] = ob_get_contents();
			 ob_end_clean();
			 
			  $VIEW = "logged/media_center/liked_by_lightbox.phtml";
              #parent::_render($data, $VIEW); 
			  $html = $this->load->view($VIEW, $data, true);  
			  echo json_encode( array('result'=>'success','html_data'=>$html) );
		   } 
			catch(Exception $err_obj)
			{
			  show_error($err_obj->getMessage());
			} 
			
	} 
	
	public function magazine_likes_ajax_pagination($magazine_id , $page=0) 
	{
		 try
		 {
		    $data = $this->data;  
			$wh	= " AND i_magazine_id='".$magazine_id."'";
			$result = $this->gospel_magazine_model->get_gospel_likes($wh ,$page,$this->comments_pagination_per_page);
			
		    $resultCount = count($result);
			$total_rows = $this->gospel_magazine_model->get_count_gospel_likes($wh);
			#pr($result);exit; 		

			$this->load->library('jquery_pagination');
			$config['base_url'] = base_url()."logged/media_center/project_likes_ajax_pagination/{$magazine_id}";
			$config['total_rows'] = $total_rows;
			$config['per_page'] = $this->comment_pagination_per_page;
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

			
			$data['result_arr'] = check_friend_netpal_status($result);
			
			$data['no_of_result'] = $total_rows;
			$data['current_page'] = $page;
			$data['total_pages'] = ceil($total_rows/$this->comment_pagination_per_page);
		  
			$p = ($page/$this->comment_pagination_per_page);
			$data['current_loaded_page_no'] =  $p + 1;
			
			# rendering the view file...
			$VIEW_FILE = "logged/media_center/liked_by_lightbox_ajax.phtml";
			$this->load->view($VIEW_FILE, $data);
			//return $html;
			
		} 
        catch(Exception $err_obj)
        {
            
        } 
	
    }
	/*******************************GOSPEL MAGAZINE COMMENTS AND LIKES**************************************************/
	
	
	
	/*******************************CHRISTAN PROJECT MMENTS AND LIKES**************************************************/
	function add_christan_project_comments()
	{
		$postcmnts_txt	= $this->input->post("postcmnts_txt",true);
        $proj_id = $this->input->post('magazine_id',true);
		
		$err	= array();
		if($postcmnts_txt=='')
			$err['err_postcmnts_txt']	= "* Required Field.";
		
		if(count($err)>0)
		{
			$return_arr	= array('success'=>0,"msg"=>'Please enter some text.');
			echo json_encode($return_arr);
			exit;
		}
		else
		{
			$cr_date	= get_db_datetime();
		
			$arr	= array("i_proj_id" =>$proj_id,
								"i_user_id"=>intval(decrypt($this->session->userdata('user_id'))),
								"s_comments"=>$postcmnts_txt,
								"dt_posted_date"=>$cr_date);
		
			//pr($arr);exit;
			
		    $i_blog_post_comment_id = $this->gospel_magazine_model->add_project_cmnts($arr);
			$wh	= " AND i_proj_id='".$proj_id."'";
			$total_cmt	= $this->gospel_magazine_model->get_count_project_cmnts($wh);
			$return_arr	= array('success'=>1,"msg"=>"Comment posted successfully","total_comments"=>$total_cmt);
			echo json_encode($return_arr);
			exit;
		}
	}
	
	
	
	function add_christan_project_like()
	{
		$proj_id = $this->input->post('proj_id',true);
		$cr_date	= get_db_datetime();
	
		$arr	= array("i_proj_id" =>$proj_id,
							"i_user_id"=>intval(decrypt($this->session->userdata('user_id'))),
							"dt_liked_on"=>$cr_date);
	
		//pr($arr);exit;
		
		$i_blog_post_comment_id = $this->gospel_magazine_model->add_project_likes($arr);
		if($i_blog_post_comment_id)
		{
			$wh	= " AND i_proj_id='".$proj_id."'";
			$total_like	= $this->gospel_magazine_model->get_count_project_likes($wh);
			$return_arr	= array('success'=>1,"msg"=>"Comment posted successfully","total_like"=>$total_like);
			echo json_encode($return_arr);
			exit;
		}
		else
		{
			$return_arr	= array('success'=>0,"msg"=>"");
			echo json_encode($return_arr);
			exit;
		}
	}
	public function fetch_comment_on_christan_project($proj_id='')
	{
		try
		  {
			 $data = $this->data;  
			 
			 ob_start();
			 $this->project_comments_ajax_pagination($proj_id);
			 $data['comments_list'] = ob_get_contents();
			 ob_end_clean();
			 
			  $VIEW = "logged/media_center/my_christan_project_comments_lightbox.phtml";
              #parent::_render($data, $VIEW); 
			  $html = $this->load->view($VIEW, $data, true);  
			  echo json_encode( array('result'=>'success','html_data'=>$html) );
		   } 
			catch(Exception $err_obj)
			{
			  show_error($err_obj->getMessage());
			} 
			
	} 
	
	public function project_comments_ajax_pagination($proj_id , $page=0) 
	  {
		 try
		 {
		    $data = $this->data;  
			$wh	= " AND i_proj_id='".$proj_id."'";
			$result = $this->gospel_magazine_model->get_project_cmnts($wh ,$page,$this->comments_pagination_per_page);
			
		    $resultCount = count($result);
			$total_rows = $this->gospel_magazine_model->get_count_project_cmnts($wh);
			#pr($result);exit; 		

			$this->load->library('jquery_pagination');
			$config['base_url'] = base_url()."logged/media_center/project_comments_ajax_pagination/{$proj_id}";
			$config['total_rows'] = $total_rows;
			$config['per_page'] = $this->comment_pagination_per_page;
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

			
			$data['result_arr'] = check_friend_netpal_status($result);
			$data['no_of_result'] = $total_rows;
			$data['current_page'] = $page;
			$data['total_pages'] = ceil($total_rows/$this->comment_pagination_per_page);
		  
			$p = ($page/$this->comment_pagination_per_page);
			$data['current_loaded_page_no'] =  $p + 1;
			
			# rendering the view file...
			$VIEW_FILE = "logged/media_center/my_christan_project_comments_lightbox_ajax.phtml";
			$this->load->view($VIEW_FILE, $data);
			//return $html;
			
		} 
        catch(Exception $err_obj)
        {
            
        } 
	
    }
	
	
	
	public function fetch_likes_on_christan_project($proj_id='')
	{
		try
		  {
			 $data = $this->data;  
			 
			 ob_start();
			 $this->project_likes_ajax_pagination($proj_id);
			 $data['people_liked_list'] = ob_get_contents();
			 ob_end_clean();
			 
			  $VIEW = "logged/media_center/liked_by_lightbox.phtml";
              #parent::_render($data, $VIEW); 
			  $html = $this->load->view($VIEW, $data, true);  
			  echo json_encode( array('result'=>'success','html_data'=>$html) );
		   } 
			catch(Exception $err_obj)
			{
			  show_error($err_obj->getMessage());
			} 
			
	} 
	
	public function project_likes_ajax_pagination($proj_id , $page=0) 
	  {
		 try
		 {
		    $data = $this->data;  
			$wh	= " AND i_proj_id='".$proj_id."'";
			$result = $this->gospel_magazine_model->get_project_likes($wh ,$page,$this->comments_pagination_per_page);
			
		    $resultCount = count($result);
			$total_rows = $this->gospel_magazine_model->get_count_project_likes($wh);
			#pr($result);exit; 		

			$this->load->library('jquery_pagination');
			$config['base_url'] = base_url()."logged/media_center/project_likes_ajax_pagination/{$proj_id}";
			$config['total_rows'] = $total_rows;
			$config['per_page'] = $this->comment_pagination_per_page;
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

			
			$data['result_arr'] = check_friend_netpal_status($result);
			
			$data['no_of_result'] = $total_rows;
			$data['current_page'] = $page;
			$data['total_pages'] = ceil($total_rows/$this->comment_pagination_per_page);
		  
			$p = ($page/$this->comment_pagination_per_page);
			$data['current_loaded_page_no'] =  $p + 1;
			
			# rendering the view file...
			$VIEW_FILE = "logged/media_center/liked_by_lightbox_ajax.phtml";
			$this->load->view($VIEW_FILE, $data);
			//return $html;
			
		} 
        catch(Exception $err_obj)
        {
            
        } 
	
    }
	/*******************************CHRISTAN PROJECT COMMENTS AND LIKES**************************************************/
	
	########### NEW FETCH COMMENTS ON CHRISTIAN PROJECT ###########

	public function new_fetch_likes_on_christian_project($magazine_id='')
	{
		try
		  {
			  $data = $this->data; 
			  $html = ''; 
			  $wh	= " AND i_proj_id='".$magazine_id."'";
			  $result = $this->gospel_magazine_model->get_project_likes($wh ,$page,$this->comments_pagination_per_page);
			  
			  if(count($result)){
				  foreach($result as $key=> $val){
					  
						 $name = $val['s_profile_name'];
						 $profile_image = get_profile_image($val['i_user_id'],'thumb',$val['s_profile_photo']);
						 
						 if($val['if_already_friend'] == 'true' || $val['already_added_netpal'] == 'true'){
						  $profile_link = get_profile_url($val['post_owner_user_id'],$val['s_profile_name']);
						 }
						 else
						 {
						  $profile_link = get_public_profile_url($val['post_owner_user_id'],$val['s_profile_name']);
						 }  
						 
						$html .= '     <div class="user_div"> 
											<a href="'.$profile_link.'">
											<div class="pro_photo3" style="background:url('.$profile_image.') no-repeat center;width:60px; height:60px;"></div>
											</a> 
											<a href="javascript:void(0);" class="blue_link">'.$name.'</a> 
										</div>
										
										';
				  }
				  $html .= '<br class="clr" />';
			  }
			  
			 
			  echo json_encode( array('result'=>'success','html_data'=>$html) );
		   } 
			catch(Exception $err_obj)
			{
			  show_error($err_obj->getMessage());
			} 
			
	} 
	
	########### NEW FETCH COMMENTS ON CHRISTIAN PROJECT METHOD ###########
	
	public function NEW_fetch_comment_christian_method($magazine_id='')
	{
		try
		  {
			  $data = $this->data; 
			  $html  = ''; 
			  $wh	= " AND i_proj_id='".$magazine_id."'";
			  $result = $this->gospel_magazine_model->get_project_cmnts($wh ,$page,$this->comments_pagination_per_page);
			
			  if(count($result)){
				  
				  foreach($result as $key=> $val){
					  
					 $profile_image_filename = get_profile_image($val['i_user_id'],'thumb');
			 		 $DESC = html_entity_decode(htmlspecialchars_decode($val['s_comments']),ENT_QUOTES,'utf-8');
					 
					 if($val['if_already_friend'] == 'true' || $val['already_added_netpal'] == 'true')
					  $profile_link = get_profile_url($val['post_owner_user_id'],$val['s_profile_name']);
					 else
					  $profile_link = get_public_profile_url($val['post_owner_user_id'],$val['s_profile_name']);
					 
					 $html .= '<div class="txt_content01 comments-number-content"> 
					 			<a href="'.$profile_link.'"><div style="background:url('.$profile_image_filename.') no-repeat center;width:60px; height:60px;" class="pro_photo3" ></div></a>
									<div>
										  <p class="blue_bold12"><a href="javascript:void(0);">'.$val['s_profile_name'].'</a></p>
										  <p>'.nl2br($DESC).'</p>
											 <p class="read-more">Updated on: '.get_time_elapsed($val['dt_posted_date']).'</p>
									</div>
									<div class="clr"></div>
							  </div>'; 
				  }
			  }
			 
			  echo json_encode( array('result'=>'success','html_data'=>$html) );
		   } 
			catch(Exception $err_obj)
			{
			  show_error($err_obj->getMessage());
			} 
			
	} 
	
	
	public function minister_shout(){
		
		try
        {
			$posted=array();
			$this->data["posted"]=$posted;/*don't change*/    
			$data = $this->data;      
			$this->data["MAIN_MENU_SELECTED"] = 6;
			parent::_set_title('::: COGTIME Xtian network :::');
			parent::_set_meta_desc('');
			parent::_set_meta_keywords('');
		
			
			parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
										'js/switch.js','js/animate-collapse.js',
										'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
										'js/stepcarousel.js',
										'js/jquery.autofill.js',
										'js/frontend/logged/tweets/tweet_utilities.js',
										'js/tab.js'
										));
										
			parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
											  'css/dd.css') );
				
				
			# view file...
			$data['pagination_per_page']   =    $this->shouts_pagination_per_page;
			
			ob_start();
			$this->shout_list_AJAX();
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['shout_content'] = $content_obj->html;
			$data['no_of_result']  = $content_obj->no_of_result;
			ob_end_clean();
			
			//$data['listingContent'] = $content;
			$VIEW = "logged/media_center/minister-shout.phtml"; 
			parent::_render($data, $VIEW);
			
        }
        catch(Exception $err_obj)
        {
           
        } 
	
	}
	
	public function shout_list_AJAX($page=0)
    {
		
		$cur_page = $page + $this->shouts_pagination_per_page;
		$data = $this->data;
		
		$s_where =  " WHERE 1 AND U.is_minister  = 1 AND BP.i_disable = 1 ";
				
		$order_by = "`id` DESC ";
		
		$result = $this->my_blog_post_model->get_minister_shouts_list($s_where,$page,$this->shouts_pagination_per_page,$order_by);
		$total_rows = $this->my_blog_post_model->get_minister_shouts_count($s_where);
		
		
		$data['result_arr'] = $result;
		$data['no_of_result'] = $total_rows;
		$data['current_page_1'] = $cur_page;
		
		 //--- for check end of he page.
		   $view_more = true;
		   $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->shouts_pagination_per_page)
			  $view_more = false;
		 //--------- end check
		
		
		$VIEW_FILE = "logged/media_center/minister_shout_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
		
        echo json_encode( array('html'=>$content, 'no_of_result'=>$data['no_of_result'],'view_more'=>$view_more, 'cur_page'=>$data['current_page_1']) );
			
	}
	
  public function viewShoutsComment()
  {
	   
	   $article_id = $this->input->post('article_id');
	   $wh = " WHERE c.i_blog_post_id  = {$article_id} ";
	   $comment_arr = $this->my_blog_post_model->getCommentsbyArticleId($wh);
	   
	   $html = '';
	  // pr($tweet_reply_arr,1);
	   if(count($comment_arr))
	   {
		   foreach($comment_arr as $k=>$val){
			   
			   $img = get_profile_image($val['i_user_id'],'thumb');
			   
			   $html .= '<div style="padding:8px 0px;" class="shade_box_white">
						  <div class="txt_content01"> 
						  	<a href="javascript:void(0);"><div style="background:url('.$img.') no-repeat center;width:73px; height:73px;" class="pro_photo3" ></div></a>
								<div>
									  <p class="blue_bold12"><a href="javascript:void(0);">'.$val['s_profile_name'].'</a></p>
									  
									  <p>'.nl2br($val['s_comments']).'</p>
										 <p class="read-more">Date Posted: '.get_time_elapsed_blog($val['dt_created_date']).'</p>
								</div>
								<div class="clr"></div>
						  </div>
					</div>';
		   }
		     
			  
		   echo json_encode(array('des'=>base64_encode($html)));
	   }
	   else
		  echo json_encode(array('des'=>base64_encode('')));
  }
  
   public function show_minister_list($letter){
		
		try
        {
			$posted=array();
			$this->data["posted"]=$posted;/*don't change*/    
			$data = $this->data;      
			$this->data["MAIN_MENU_SELECTED"] = 6;
			parent::_set_title('::: COGTIME Xtian network :::');
			parent::_set_meta_desc('');
			parent::_set_meta_keywords('');
		
			
			parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
										'js/switch.js','js/animate-collapse.js',
										'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
										'js/stepcarousel.js',
										'js/jquery.autofill.js',
										'js/frontend/logged/tweets/tweet_utilities.js',
										'js/tab.js'
										));
										
			parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
											  'css/dd.css') );
				
				
			# view file...
			$data['pagination_per_page']   =    $this->shouts_pagination_per_page;
			$data['letter'] = $letter;
			
			ob_start();
			$this->minister_list_AJAX($letter);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['shout_content'] = $content_obj->html;
			$data['no_of_result']  = $content_obj->no_of_result;
			ob_end_clean();
			
			//$data['listingContent'] = $content;
			$VIEW = "logged/media_center/minister.phtml"; 
			parent::_render($data, $VIEW);
			
        }
        catch(Exception $err_obj)
        {
           
        } 
	
	}
	
	public function minister_list_AJAX($letter = 'all',$page=0)
    {
		
		$cur_page = $page + $this->shouts_pagination_per_page;
		$data = $this->data;
		
		if($letter == 'all'){
			$s_where =  " WHERE 1 AND U.is_minister  = 1  ";
		}
		else{
			$s_where =  " WHERE 1 AND U.is_minister  = 1 AND U.s_first_name like '".$letter."%'";
		}
				
		$order_by = "`id` DESC ";
		
		$result = $this->my_blog_post_model->get_minister_list($s_where,$page,$this->shouts_pagination_per_page,$order_by);
		$total_rows = $this->my_blog_post_model->get_minister_count($s_where);
		
		
		$data['result_arr'] = $result;
		$data['no_of_result'] = $total_rows;
		$data['current_page_1'] = $cur_page;
		
		 //--- for check end of he page.
		   $view_more = true;
		   $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->shouts_pagination_per_page)
			  $view_more = false;
		 //--------- end check
		
		
		$VIEW_FILE = "logged/media_center/minister_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
		
        echo json_encode( array('html'=>$content, 'no_of_result'=>$data['no_of_result'],'view_more'=>$view_more, 'cur_page'=>$data['current_page_1']) );
			
	}
 
 
}   // end of controller...




