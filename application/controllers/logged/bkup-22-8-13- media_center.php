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
    
    public function __construct()
     {
	 	
        try
        {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...
			$this->upload_path = BASEPATH.'../uploads/user_ring_logos/';
            $this->load->model('users_model');
			$this->load->model("gospel_magazine_model");
			
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
		$total_rows = $this->gospel_magazine_model->get_count_magazines($wh);
							
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
        $proj_id = $this->input->post('proj_id',true);
		
		$err	= array();
		if($postcmnts_txt=='')
			$err['err_postcmnts_txt']	= "* Required Field.";
		
		if(count($err)>0)
		{
			$return_arr	= array('success'=>0,"msg"=>$err['err_postcmnts_txt']);
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
			$total_cmt	= $this->gospel_magazine_model->get_count_project_cmnts();
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
			$total_like	= $this->gospel_magazine_model->get_count_project_likes();
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
}   // end of controller...

