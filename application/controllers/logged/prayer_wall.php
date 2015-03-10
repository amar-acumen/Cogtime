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


class Prayer_wall extends Base_controller
{
    
    private $pagination_per_page =  10 ;
	private $home_pagination_per_page = 8;
	private $commits_pagination_per_page = 5;
	private $all_commits_pagination_per_page = 10;
    
    public function __construct()
     {
	 	
        try
        {
            parent::__construct();
               parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...

            $this->load->model('users_model');
			$this->load->model('holy_place_model');
			$this->load->model('bible_fruits_model');
			$this->load->model('prayer_wall_photos_model');
			$this->load->model('prayer_wall_model');
			$this->load->model('prayer_commit_model');
			$this->load->model('user_alert_model');
			$this->load->model('user_notifications_model');
			
			$this->i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    
    public function index() 
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
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js',
										'js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',
										'js/jquery/ui/jquery.ui.core.js',
										'js/jquery.ui.datepicker.js',*/
									    'js/jquery-ui-timepicker-addon.js',
										'js/jquery-ui.triggeredAutocomplete.js',
										'js/jquery-ui-sliderAccess.js',
//										'js/tab.js',
										'js/production/prayer_wall.js',
										'js/production/tweets.js',
										'js/autocomplete/jquery.autocomplete.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.13.custom.css',
//										'css/jquery-ui-1.8.2.custom.css',
										'css/jquery.autocomplete.css') );
          
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			$data['prayer_photo'] = $this->prayer_wall_photos_model->get_all_prayer_wall_photos();
			$data['pagination_per_page'] =  $this->home_pagination_per_page;
			
			$data['page_refresh_time'] = ($data['site_settings_arr']['i_prayer_wall_refresh_frequency'] -1) * 1000;
			
			$this->session->set_userdata('search_condition' ,'');
			$this->session->set_userdata('location','');
		    $this->session->set_userdata('txt_srch_from_time','');
		    $this->session->set_userdata('txt_srch_to_time','');
		    $this->session->set_userdata('dt_start_date','');
		    $this->session->set_userdata('dt_end_date','');
		    $this->session->set_userdata('srch_request_type','');
			
			$today_date = date('Y-m-d');
			$s_where = "WHERE 1 AND p.dt_end_date >= '{$today_date}'";
			$data['total_records'] = $this->prayer_wall_model->get_count_list_prayers_request($s_where);
			
			ob_start();
		    $this->home_prayer_request_ajax_pagination(0);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['prayer_req_ajax_content'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
			
			# view file...
			
            $VIEW = "logged/holy_place/prayer_wall/prayer-wall-home.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }
	
	
	public function home_prayer_request_ajax_pagination($page=0 , $s_type= '')
    {
		$today_date = date('Y-m-d');
		$s_where = "WHERE 1 AND p.dt_end_date >= '{$today_date}'";
		$total_records = $this->prayer_wall_model->get_count_list_prayers_request($s_where);
		
		
		
				if($page == 0){
				   $prev_page_1 = floor(($total_records/$this->home_pagination_per_page)) * $this->home_pagination_per_page;
				   $next_page_1 = $page + $this->home_pagination_per_page;
				}
				else if($page == floor(($total_records/$this->home_pagination_per_page)) * $this->home_pagination_per_page){
				   		
						$next_page_1 = 0;
						$prev_page_1 = $page - $this->home_pagination_per_page;
				}
				else{
					$prev_page_1 = $page - $this->home_pagination_per_page;
					$next_page_1 = $page + $this->home_pagination_per_page;
				}
		
				$cur_page = $page + $this->home_pagination_per_page;
				
				
	
		
		$data = $this->data;
		$result = $this->prayer_wall_model->get_list_prayers_request($s_where, intval($page), $this->home_pagination_per_page);
		#echo $this->db->last_query();
		 $total_rows = $this->prayer_wall_model->get_count_list_prayers_request($s_where);
	//pr($result,1);
		
		$data['arr_prayer_request'] = $result;
		$data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
		$data['prev_page_1'] = $prev_page_1;
		$data['next_page_1'] = $next_page_1;
		
		$data['profile_id'] = $i_user_id;
		
		
		
		
		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->home_pagination_per_page)
			  $view_more = false;
         //--------- end check
		
		
		$VIEW_FILE = "logged/holy_place/prayer_wall/ajax_view/home_prayer_list_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$content, 'no_of_result'=>$data['no_of_result'],'view_more'=>$view_more, 'cur_page'=>$data['current_page_1'],'prev_page_1'=>$prev_page_1, 'next_page_1'=>$next_page_1) );
			
	}
	
	
	public function view_all_prayer_request() 
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
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js', 'js/stepcarousel.js',
										'js/jquery-ui-1.8.2.custom.min.js',
										'js/jquery/ui/jquery.ui.core.js',
										'js/jquery.ui.datepicker.js',*/
									    'js/jquery-ui-timepicker-addon.js',
										'js/jquery-ui-sliderAccess.js',
										'js/autocomplete/jquery.autocomplete.js',
                                       
										
//										'js/tab.js',
										'js/production/prayer_wall.js',
										
                                        ));
                                        
            parent::_add_css_arr( array(/*'css/jquery-ui-1.8.2.custom.css',*/'css/jquery.autocomplete.css') );
          	
			$i_user_id = intval(decrypt($this->session->userdata('user_id')));
            
			$data['prayer_photo'] = $this->prayer_wall_photos_model->get_all_prayer_wall_photos();
			$data['pagination_per_page'] = $this->pagination_per_page;
			
			$this->session->set_userdata('search_condition','');
			
			$this->session->set_userdata('location','');
		    $this->session->set_userdata('txt_srch_from_time','');
		    $this->session->set_userdata('txt_srch_to_time','');
		    $this->session->set_userdata('dt_start_date','');
		    $this->session->set_userdata('dt_end_date','');
		    $this->session->set_userdata('srch_request_type','');
			
			ob_start();
		    $this->all_prayer_request_ajax_pagination(0);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['prayer_req_ajax_content'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
			
			# view file...
			
            $VIEW = "logged/holy_place/prayer_wall/view-all-prayer-request.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }
	
	
	public function all_prayer_request_ajax_pagination($page=0)
    {
		
		//echo $page;
		## seacrh conditions : filter ############
		  $WHERE_COND = 'WHERE 1 AND p.i_isenabled  = 1';
		  
		   if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) {
			  
			  $txt_srch_country = intval(decrypt($this->input->post('txt_srch_country')));
			  $WHERE_COND .= ($txt_srch_country=='' || $txt_srch_country == '-1')?'':" AND 
			  				(u.i_country_id =  '{$txt_srch_country}')";
							
			  
			  $txt_srch_state = get_formatted_string(trim($this->input->post('txt_srch_state')));
			  $WHERE_COND .= ($txt_srch_state=='')?'':" AND (u.s_state  LIKE  '%".$txt_srch_state."%')";
			  
			  $txt_srch_city = get_formatted_string(trim($this->input->post('txt_srch_city')));
			  $WHERE_COND .= ($txt_srch_city=='')?'':" AND (u.s_city  LIKE  '%".$txt_srch_city."%')";
			  
			  
			  $txt_srch_from_time = (trim($this->input->post('txt_srch_from_time')));
			  $WHERE_COND .= ($txt_srch_from_time=='')?'':" AND (DATE_FORMAT(p.dt_start_date , '%H:%i')  = '".$txt_srch_from_time."')";
			  
			  
			  $txt_srch_to_time = (trim($this->input->post('txt_srch_to_time')));
			  $WHERE_COND .= ($txt_srch_to_time=='')?'':" AND ( DATE_FORMAT(p.dt_end_date ,'%H:%i') = '".$txt_srch_to_time."')";
			  
			
			  
			  if($this->input->post('srch_date_to') != ''){
					$dt_start_date = get_db_dateformat($this->input->post('srch_date_to'));
					$WHERE_COND .= ($dt_start_date=='')?'':" AND (DATE(p.dt_start_date) ='".$dt_start_date."' )";
			  }
			  
			  if($this->input->post('srch_date_end') != ''){
					 $dt_end_date = get_db_dateformat($this->input->post('srch_date_end'));
					$WHERE_COND .= ($dt_end_date=='')?'':" AND (DATE(p.dt_end_date) ='".$dt_end_date."' )";
			  }
			  
			  $srch_request_type = (trim($this->input->post('srch_request_type')));
			  
			  if($srch_request_type == '1'){
			 		 $WHERE_COND .= " AND (p.e_request_type  =  'Emergency')";
			  }else if($srch_request_type == '2') {
				     $WHERE_COND .= " AND (p.e_request_type  =  'On Going')";
			  }
		  
		  		 $this->session->set_userdata('search_condition',$WHERE_COND);
		   }
		
		$s_where = $this->session->userdata('search_condition');
		//exit;
		$cur_page = $page + $this->pagination_per_page;
		
		$data = $this->data;
		
       //	$s_order_by = "p.dt_start_date DESC";
		$result = $this->prayer_wall_model->get_list_prayers_request($s_where, intval($page), $this->pagination_per_page);
		//echo $this->db->last_query();
		 $total_rows = $this->prayer_wall_model->get_count_list_prayers_request($s_where);
		//pr($result,1);
		$data['arr_prayer_request'] = $result;
		$data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
		$data['profile_id'] = $i_user_id;
		
		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->pagination_per_page)
			  $view_more = false;
         //--------- end check
		
		
		$VIEW_FILE = "logged/holy_place/prayer_wall/ajax_view/all_prayer_request_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		    
			
		
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$content, 'no_of_result'=>$data['no_of_result'],'view_more'=>$view_more, 'cur_page'=>$data['current_page_1'], 'home_html'=>$content_home) );
			
	}
	
	
	public function add_prayer_request_ajax($id)
	{
		try
		{
			
			parent::check_login(TRUE, '', array('1'));
			$arr_messages = array();
			# error message trapping...
			
			
			if( trim($this->input->post('request_type'))=='-1') 
			{
					$arr_messages['request_type'] = "* Required Field.";
			}
			
			
			
			if(trim($this->input->post('ta_desc'))=='' || trim($this->input->post('ta_desc')) == 'Max 500 Char allowed') 
			{
					$arr_messages['desc'] = "* Required Field.";
			}
		/*	
			if( trim($this->input->post('txt_from_time'))=='') 
			{
					$arr_messages['from_time'] = "* Required Field.";
			}
			elseif(!preg_match("/^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])?$/", trim($this->input->post('txt_from_time')))){
				  $arr_messages['from_time'] = '* Please input time in 24 hours format.';
			}
			
			if( trim($this->input->post('txt_to_time'))=='') 
			{
					$arr_messages['to_time'] = "* Required Field.";
			}
			elseif(!preg_match("/^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])?$/", trim($this->input->post('txt_to_time')))){
				  $arr_messages['to_time'] = '* Please input time in 24 hours format.';
			}*/
			
			if( trim($this->input->post('date_end1'))=='') 
			{
					$arr_messages['date_end1'] = "* Required Field.";
			}
			
			if( trim($this->input->post('date_to1'))=='') 
			{
					$arr_messages['date_to1'] = "* Required Field.";
			}
			
			if($this->input->post('hd_image_name') == ''){
				$arr_messages['image_msg'] = "* Required Field.";
			}
			
			if($this->input->post('s_subject') == ''){
				$arr_messages['s_subject'] = "* Required Field.";
			}
		
		   //pr($arr_messages);
			if( count($arr_messages)==0 ) {
					
				$info = array();
					
				$info["i_user_id"] = intval(decrypt($this->session->userdata('user_id')));
				
				$info["s_description"]= get_formatted_string($this->input->post('ta_desc'));
				$info["s_subject"]= get_formatted_string($this->input->post('s_subject'));
				
				$info['s_image_name'] = trim($this->input->post('hd_image_name'));
				$info["e_request_type"]= ($this->input->post('request_type') == '2')?'On Going':'Emergency';
				
				//$start_time  =  get_db_dateformat($this->input->post('date_to1'),'/').' '.$this->input->post('txt_from_time') ; 
				$info["dt_start_date"] = trim($this->input->post('date_to1')).':'.'00' ;//$start_time;
				
				//$end_time  =  get_db_dateformat($this->input->post('date_end1'),'/').' '.$this->input->post('txt_to_time') ; 
				$info["dt_end_date"] = trim($this->input->post('date_end1')).':'.'00' ; //$end_time;
				
				$info['dt_insert_date'] = get_db_datetime();
				$i_prayer_request_id = $this->prayer_wall_model->insert($info); #echo $this->db->last_query();exit;
				
			
				if($this->input->post('created_from') == 'my_prayer_request'){
					
					ob_start();
					$this->my_all_prayer_request_ajax_pagination($info['i_user_id'],0);
					$content = ob_get_contents();
					$content_obj = json_decode($content);
					$html = base64_encode($content_obj->html); 
					$no_of_result = $content_obj->no_of_result;
					ob_end_clean();
				}
				else{
					ob_start();
					$this->all_prayer_request_ajax_pagination(0);
					$content = ob_get_contents();
					$content_obj = json_decode($content);
					$html = base64_encode($content_obj->html); 
					$no_of_result = $content_obj->no_of_result;
					ob_end_clean();
				}
				
				ob_start();
				$this->home_prayer_request_ajax_pagination(0);
				$content = ob_get_contents();
				$content_obj = json_decode($content);
				$home_html = base64_encode($content_obj->html); 
				
				ob_end_clean();

				echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'Prayer request created Successfully.','html'=>$html, 'home_html'=>$home_html));
			}
			else
			{
				echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'Error!'));
			}
		
		
		
		}
		catch(Exception $err_obj)
        {
            
        } 
	}
	
	
	
	
	public function edit_prayer_request_ajax($id)
	{
		try
		{
			
			parent::check_login(TRUE, '', array('1'));
			$arr_messages = array();
			# error message trapping...
			
			
			if( trim($this->input->post('request_type'))=='-1') 
			{
					$arr_messages['request_type'.$id] = "* Required Field.";
			}
			
			
			
			if(trim($this->input->post('message'))=='' || trim($this->input->post('message')) == 'Max 300 Characters') 
			{
					$arr_messages['edit_desc'.$id] = "* Required Field.";
			}
			/*
			if( trim($this->input->post('start_time'))=='') 
			{
					$arr_messages['from_time'.$id] = "* Required Field.";
			}
			elseif(!preg_match("/^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])?$/", trim($this->input->post('start_time')))){
				  $arr_messages['from_time'.$id] = '* Please input time in 24 hours format.';
			}
			
			if( trim($this->input->post('end_time'))=='') 
			{
					$arr_messages['to_time'.$id] = "* Required Field.";
			}
			elseif(!preg_match("/^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])?$/", trim($this->input->post('end_time')))){
				  $arr_messages['to_time'.$id] = '* Please input time in 24 hours format.';
			}*/
			
			if( trim($this->input->post('end_date'))=='') 
			{
					$arr_messages['date_end1'.$id] = "* Required Field.";
			}
			
			if( trim($this->input->post('start_date'))=='') 
			{
					$arr_messages['date_to1'.$id] = "* Required Field.";
			}
			
			if($this->input->post('image_name') == ''){
				$arr_messages['image_msg'.$id] = "* Required Field.";
			}
			if($this->input->post('s_subject') == ''){
				$arr_messages['s_subject'.$id] = "* Required Field.";
			}
	//	echo ' == '.$this->input->post('image_name');
		   //pr($arr_messages);
			if( count($arr_messages)==0 ) {
					
				$info = array();
					
				$info["i_user_id"] = intval(decrypt($this->session->userdata('user_id')));
				
				$info["s_description"]= get_formatted_string($this->input->post('message'));
			    $info["s_subject"]= get_formatted_string($this->input->post('s_subject'));
				
				$info['s_image_name'] = trim($this->input->post('image_name'));
			
				$info["e_request_type"]= ($this->input->post('request_type') == '2')?'On Going':'Emergency';
				
				$start_time  =  get_db_dateformat($this->input->post('start_date'),'/').' '.$this->input->post('start_time') ; 
				$info["dt_start_date"] = trim($this->input->post('start_date')).':'.'00';//$start_time;
				
				$end_time  =  get_db_dateformat($this->input->post('end_date'),'/').' '.$this->input->post('end_time') ; 
				$info["dt_end_date"] = trim($this->input->post('end_date')).':'.'00';
				$info["s_subject"]= get_formatted_string($this->input->post('s_subject'));
				
				$info['dt_update_date'] = get_db_datetime();
				$i_prayer_request_id = $this->prayer_wall_model->edit_prayer_wall($info, $id);
				
				$i_user_id = intval(decrypt($this->session->userdata('user_id')));
				ob_start();
				$this->my_all_prayer_request_ajax_pagination($i_user_id,0);
				$content = ob_get_contents();
				$content_obj = json_decode($content);
				$html = $content_obj->html; 
				$no_of_result = $content_obj->no_of_result;
				ob_end_clean();
				
				
				

				echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'Prayer request updated Successfully.', 'html'=>$html,'no_of_result'=>$no_of_result));
			}
			else
			{
				echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'Error!'));
			}
		
		
		
		}
		catch(Exception $err_obj)
        {
            
        } 
	}
	
	
	public function manage_my_prayer_request() 
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
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js',
										'js/jquery-ui-1.8.2.custom.min.js',
										'js/jquery/ui/jquery.ui.core.js',
										'js/jquery.ui.datepicker.js',*/
									    'js/jquery-ui-timepicker-addon.js',
										'js/jquery-ui-sliderAccess.js',
//                                        'js/stepcarousel.js',
										'js/autocomplete/jquery.autocomplete.js',
										
//										'js/tab.js',
										'js/production/prayer_wall.js'
                                        ));
                                        
            parent::_add_css_arr( array(/*'css/jquery-ui-1.8.2.custom.css',*/
                'css/jquery.autocomplete.css') );
          	
			$i_user_id = intval(decrypt($this->session->userdata('user_id')));
            
			$data['prayer_photo'] = $this->prayer_wall_photos_model->get_all_prayer_wall_photos();
			$data['pagination_per_page'] = $this->pagination_per_page;
			
			$this->session->set_userdata('search_condition','');
			$this->session->set_userdata('location','');
		    $this->session->set_userdata('txt_srch_from_time','');
		    $this->session->set_userdata('txt_srch_to_time','');
		    $this->session->set_userdata('dt_start_date','');
		    $this->session->set_userdata('dt_end_date','');
		    $this->session->set_userdata('srch_request_type','');
			
			ob_start();
		    $this->my_all_prayer_request_ajax_pagination($i_user_id,0);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['prayer_req_ajax_content'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
			
			# view file...
			
            $VIEW = "logged/holy_place/prayer_wall/manage-my-prayer-request.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }
	
	
	public function my_all_prayer_request_ajax_pagination($i_user_id ,$page=0)
	{
		
		## seacrh conditions : filter ############
		$WHERE_COND = "WHERE 1 AND p.i_isenabled  = 1 AND p.i_user_id = {$i_user_id}";
		  
		$s_where = $WHERE_COND;
		//exit;
		$cur_page = $page + $this->pagination_per_page;
		
		$data = $this->data;
		
	   
		$result = $this->prayer_wall_model->get_list_prayers_request($s_where, intval($page), $this->pagination_per_page);
		//echo $this->db->last_query(); exit;
		 $total_rows = $this->prayer_wall_model->get_count_list_prayers_request($s_where);
		//pr($result,1);
		$data['arr_prayer_request'] = $result;
		$data['no_of_result'] = $total_rows;
		$data['current_page_1'] = $cur_page;
		$data['profile_id'] = $i_user_id;
		
		 //--- for check end of he page.
		   $view_more = true;
		   $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->pagination_per_page)
			  $view_more = false;
		 //--------- end check
		
		
		$VIEW_FILE = "logged/holy_place/prayer_wall/ajax_view/my_all_prayer_request_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
		echo json_encode( array('html'=>$content, 'no_of_result'=>$data['no_of_result'],'view_more'=>$view_more, 'cur_page'=>$data['current_page_1']) );
			
	}
	
	public function manage_my_commitments() 
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
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery.dd.js',
                                        'js/stepcarousel.js',
										'js/jquery-ui-1.8.2.custom.min.js',
										'js/jquery/ui/jquery.ui.core.js',
										'js/jquery.ui.datepicker.js',*/
									    'js/jquery-ui-timepicker-addon.js',
										'js/jquery-ui-sliderAccess.js',
//										'js/tab.js',
										'js/autocomplete/jquery.autocomplete.js',
										'js/production/prayer_wall.js'
                                        ));
                                        
            parent::_add_css_arr( array(/*'css/jquery-ui-1.8.2.custom.css',*/
										'css/jquery.autocomplete.css') );
          	
			$i_user_id = intval(decrypt($this->session->userdata('user_id')));
            
			$data['prayer_photo'] = $this->prayer_wall_photos_model->get_all_prayer_wall_photos();
			$data['pagination_per_page'] = $this->all_commits_pagination_per_page;
			
			$this->session->set_userdata('search_condition','');
			$this->session->set_userdata('location','');
		    $this->session->set_userdata('txt_srch_from_time','');
		    $this->session->set_userdata('txt_srch_to_time','');
		    $this->session->set_userdata('dt_start_date','');
		    $this->session->set_userdata('dt_end_date','');
		    $this->session->set_userdata('srch_request_type','');
			
			ob_start();
		    $this->my_all_commitments_ajax_pagination();
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['commitments_ajax_content'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
			
			# view file...
			
            $VIEW = "logged/holy_place/prayer_wall/manage-my-commitments.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }
	
	
	
	public function my_all_commitments_ajax_pagination($page=0)
    {
		
		$i_user_id = intval(decrypt($this->session->userdata('user_id')));
		$cur_page = $page + $this->all_commits_pagination_per_page;
		
		$data = $this->data;
		$result = $this->prayer_commit_model->get_all_commitments_by_user_id($i_user_id, intval($page),
																				 $this->all_commits_pagination_per_page);
		//echo $this->db->last_query();
		$total_rows = $this->prayer_commit_model->get_all_commitments_total_by_request_id($i_user_id);
		//pr($result,1);
		$data['all_commits_info'] = $result;
		$data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
		$data['profile_id'] = $i_user_id;
		
		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->all_commits_pagination_per_page)
			  $view_more = false;
         //--------- end check
		
		
		$VIEW_FILE = "logged/holy_place/prayer_wall/ajax_view/manage_commitments_list_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$content, 'no_of_result'=>$data['no_of_result'],'view_more'=>$view_more, 'cur_page'=>$data['current_page_1']) );
			
	}
	
	
	### post commitments ###
	
  public function post_commitments($p_request_id)
  {
	  $user_id = intval(decrypt($this->session->userdata('user_id')));
	  $user_details = $this->users_model->fetch_this($user_id);
	  $arr_messages = array();
  
  
	  $message = nl2br( htmlspecialchars(trim($this->input->post('message')), ENT_QUOTES, 'utf-8') );
	  if(trim($this->input->post('message')) == 'Max 140 Characters'){
			  $arr_messages['message'.$p_request_id] ='* Required Field.';
	  }
	    
	  if( trim($this->input->post('start_date'))=='') 
	  {
			  $arr_messages['start_date'.$p_request_id] = "* Required Field.";
	  }
	  $start_date= trim($this->input->post('start_date'));
	  $end_date=trim($this->input->post('end_date'));
	  if($start_date > $end_date)
	  {
	   $arr_messages['end_date'.$p_request_id] = "Please enter a valid end date.";
	  }
	  if( trim($this->input->post('end_date'))=='') 
	  {
			  $arr_messages['end_date'.$p_request_id] = "* Required Field.";
	  }
	  
	  //echo substr($this->input->post('chk_day'),0, -1);
	  if($this->input->post('chk_day') == '') 
	  {
			  $arr_messages['chk_day'.$p_request_id] = "* Required Field.";
	  } 
	  
	  if($this->input->post('chk_time') == '') 
	  {
			  $arr_messages['chk_time'.$p_request_id] = "* Required Field.";
	  } 
          /********************************time blocked************************************************/
          
          /*         * ************************************************************ */
        $day_arr = GetDays(trim($this->input->post('start_date')), trim($this->input->post('end_date')));
        $time_arr = array();
        $time_arr = explode(', ', substr($this->input->post('chk_time'), 0, -2));
        if (count($day_arr)) {
            foreach ($day_arr as $val) {

                ### time slots
                if (count($time_arr)) {

                    foreach ($time_arr as $t_val) {

                        $t_arr = explode('-', $t_val);
                        $strt_time = explode(':', $t_arr[0]);
                        $end_time = explode(':', $t_arr[1]);

                        $user_id = intval(decrypt($this->session->userdata('user_id')));
                        $query = $this->db->get_where('cg_organizer_to_do_list', array('i_user_id' => $user_id));
                        $res = $query->result();
                       // pr($res,1);
                        /****************************************/
                        
                        foreach ($query->result() as $row) {
                            $date = $row->d_date;
                            $time = $row->t_start_time;
                           
                            $i_active = $row->i_active;
                            //echo $val.'/'.$date;
                            //echo $time.'/'.$t_arr[0];
                            
                                if($val == $date && $time >= $t_arr[0] && $time <= $t_arr[1] && $i_active == 1 ){
                                  //  echo 'kk';
                                  $arr_messages['block_time' . $p_request_id] = "This time zone already blocked";  
                                }
                                
                          
                                
                                
                            
                           
                           
                        }
                     
                        /**************/
                       
                    }
                }
            }
        } //die();
        /*         * **************************************************************** */

          
          
          
          /****************************time blocker end*****************************************************/
          
	  
	  $_html='';
	  
	  $TOTAL_COMMITS = $this->prayer_wall_model->get_total_commitments_prayer_wall($p_request_id);
	  $MAX_COMMITS = $this->data['site_settings_arr']['i_max_commitement'];
	  
	  if(count($arr_messages) == 0 && ($TOTAL_COMMITS < $MAX_COMMITS || $MAX_COMMITS == 0))
	   {
			  $prayer_arr = $this->prayer_wall_model->get_by_id($p_request_id);
			 
			 // pr($time_arr);
			  //pr($_POST,1);
			  $ip = getenv("REMOTE_ADDR") ; 
			  $arr = array();		
			  $arr['i_user_id'] = $user_id;
			  $arr['s_contents'] = $message;
			  $arr['dt_start_time'] = trim($this->input->post('start_date')).':00';
			  //get_db_dateformat($this->input->post('start_date'),'/').' '.$this->input->post('start_time') ; 
			  $arr['dt_end_time'] = trim($this->input->post('end_date')).':00';
			  // get_db_dateformat($this->input->post('end_date'),'/').' '.$this->input->post('end_time') ;
			  $arr['dt_created_on'] = get_db_datetime();
			  $arr['i_prayer_req_id'] = $p_request_id;
			  
			  $arr['s_weekdays'] = substr($this->input->post('chk_day'),0, -2);
			  $arr['s_time'] = substr($this->input->post('chk_time'),0, -2);
			   $arr['u_ip'] = $ip;
			  
			  $commit_id = $this->prayer_commit_model->insert($arr);
			  
			  
			  ### add to do list
			  $this->load->model('organizer_todo_model');
			  
			  ### getting days
			   
			  $day_arr = GetDays(trim($this->input->post('start_date')), trim($this->input->post('end_date')));  
			  //pr($day_arr);
			  
			  $time_arr = array();
			  $time_arr = explode(', ',substr($this->input->post('chk_time'),0, -2));
			  
			  //pr($time_arr,1);
			  
			  if(count($day_arr)){ 
			  	foreach($day_arr as $val){ 
					  
					### time slots
					if(count($time_arr)){  
					  
					  foreach($time_arr as $t_val){
						  
						  $t_arr = explode('-',$t_val);
						  //$strt_time = $t_arr[0];
						  $strt_time = explode(':',$t_arr[0]);
                                                 $end_time = explode(':',$t_arr[1]);
						  for($i = $strt_time[0] ; $i <= $end_time[0] ; $i++){
						  $info = array();
						  $info['i_user_id'] = intval(decrypt($this->session->userdata('user_id')));
						  //$info['s_description'] = " ".get_formatted_string($prayer_arr['s_subject']).".";
                                                  $info['s_description'] = $message;
						  $info["d_date"] = $val;
//						  $info["t_start_time"] = $t_arr[0];
//						  $info["t_end_time"] = $t_arr[1];
                                                  $info["t_start_time"] = $i.':00:00';
						  $info["t_end_time"] = $i.':00:00';
						  $info["t_remind_time"] = '00:15:00';
						  $info['i_request_id'] = $p_request_id;
                                                  $info['t_display_start_time'] = $i.':00:00';
                                                  $info['t_display_end_time'] = $end_time[0].':00:00';
                                                  $info['t_display_remind_me_back'] = '00:15:00';  
                                                  $info['t_display_remind_time'] = '00:15:00';
                                                   $info['d_display_date'] = $val;
						 	//pr($info,1);				  
						  $date_a = new DateTime($info["d_date"].' '.$info["t_start_time"]);
						  $date_b = new DateTime($info["d_date"].' '.trim($this->input->post('todo_rem_time')));
			
						  $interval = date_diff($date_a,$date_b);
			
						  $info["t_remind_me_back"] = $interval->format('%h:%i:%s');
						  
						  
						  $info['dt_created_on'] = get_db_datetime();
                                                //  pr($info,1);
						  $_ret = $this->organizer_todo_model->insert($info);
                                                  }
					  }
					}
				 }
			  }
			  
			  ### add to do list
			  
			  ## fetch prayer owner id
			  $request_info =  $this->prayer_wall_model->get_by_id($p_request_id);
			  
			  //pr( $request_info,1);
			  $total_commits = $this->prayer_wall_model->get_total_commitments_prayer_wall($p_request_id);
			  
			  $notificaion_opt = $this->user_alert_model->check_option_user_id($request_info['i_user_id']);	
			  $notification_arr = array();
	  
			  ## insert noifications ####
				  if($notificaion_opt['e_prayer_commit_received'] == 'Y' ){
					  
					  $notification_arr['i_requester_id'] = $user_id;
					  $notification_arr['i_accepter_id'] = $request_info['i_user_id'];
					  $notification_arr['s_type'] = 'prayer_r_commit';
					  $notification_arr['dt_created_on'] = get_db_datetime();
					  
					  
					  $ret = $this->user_notifications_model->insert($notification_arr);	
					  $message_id = parent::social_notifications_message($notification_arr['i_requester_id'], $notification_arr['i_accepter_id'], 'prayer_r_commit', $p_request_id);
				  }
					$email_opt = $this->user_alert_model->check_option_email_user_id($request_info['i_user_id']);
						if($email_opt['e_prayer_commit_received'] == 'Y' ){
						
						$mail_arr['i_requester_id'] =get_username_by_id( $user_id);
						$mail_arr['i_accepter_id'] =  get_username_by_id($request_info['i_user_id']);
						$mail_arr['s_type'] = 'e_prayer_commit_received';
						$mail_arr['prayer_name']=$request_info['s_subject'];
						$mail_id=get_useremail_by_id($request_info['i_user_id']);
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

					$this->email->subject($mail_arr["i_requester_id"].' has Commited to your prayer request');
					$this->email->message("$body");

					$this->email->send();
					}		  
			   ### end  ###
			   
			   	ob_start();
		    	$this->commitments_ajax_pagination($p_request_id);
				$content = ob_get_contents();
				$content_obj = json_decode($content);
				$_html = $content_obj->html; 
				$no_of_result = $content_obj->no_of_result;
				ob_end_clean();
			  
			  echo json_encode( array('success'=>'true', 'msg'=>"Commited successfully.",'html'=>$_html,'view_more'=>$view_more,'cur_page'=>$cur_page, 'total_commits'=>$total_commits) );
		  }
		 else if(count($arr_messages) == 0 && $TOTAL_COMMITS == $MAX_COMMITS)
		 {
			echo json_encode( array('success'=>'false', 'msg'=>"Maximum commitment limit reached! ",'html'=>$_html) );
		 }
		 else
		 {
			echo json_encode( array('success'=>'false', 'arr_messages'=>$arr_messages, 'html'=>$_html) );
		 }
		 exit;
	  
  }
	
 public function viewCommits($type = 'long')
  {
	   
	   $i_prayer_id = $this->input->post('p_request_id');
	   $commit_arr = $this->prayer_commit_model->get_by_request_id($i_prayer_id);
	  // pr($tweet_reply_arr,1);
	   if(count($commit_arr))
	   {
		      if($type == 'short'){
			  	$VIEW_FILE = "logged/holy_place/prayer_wall/ajax_view/short_single_commitment_ajax.phtml";
			  }
			  else{
			  	$VIEW_FILE = "logged/holy_place/prayer_wall/ajax_view/single_commitment_ajax.phtml";
			  }
	  
			  $data['commit_arr'] = $commit_arr;
			  $content = $this->load->view( $VIEW_FILE , $data, true);
			  
		   echo json_encode(array('des'=>base64_encode($content)));
	   }
	   else
		  echo json_encode(array('des'=>base64_encode('')));
  }

    public function post_testimony($p_request_id)
	{
		$user_id = intval(decrypt($this->session->userdata('user_id')));
		$user_details = $this->users_model->fetch_this($user_id);
		$arr_messages = array();


		$message = nl2br( htmlspecialchars(trim($this->input->post('message')), ENT_QUOTES, 'utf-8') );
		if(trim($this->input->post('message')) == 'Max 500 Characters'){
				$message = '';
		}
		
        $_html='';
		if($message != '')
		 {
				$arr = array();		
				$arr['s_description'] = $message;
				$arr['i_prayer_req_id'] = $p_request_id;
				$arr['dt_insert_date'] = get_db_datetime();

				
				$commit_id = $this->prayer_wall_model->insert_testimony($arr);
				
				## fetch prayer owner id
				//$request_info =  $this->prayer_wall_model->get_by_id($p_request_id);
				//$total_commits = $this->prayer_wall_model->get_total_commitments_prayer_wall($p_request_id);
				
				/*$notificaion_opt = $this->user_alert_model->check_option_user_id($tweet_info['i_owner_id']);	
				$notification_arr = array();
		
				## insert noifications ####
					if($notificaion_opt['e_tweet_comments_received'] == 'Y' ){
						
						$notification_arr['i_requester_id'] = $user_id;
						$notification_arr['i_accepter_id'] = $tweet_info['i_owner_id'];
						$notification_arr['s_type'] = 'tweet_comment';
						$notification_arr['dt_created_on'] = get_db_datetime();
						
						
						$ret = $this->user_notifications_model->insert($notification_arr);	
						$message_id = parent::social_notifications_message($notification_arr['i_requester_id'], $notification_arr['i_accepter_id'], 'tweet_comment', $tweet_id);
					}
								
								
				 ### end  ##*/#
                
				echo json_encode( array('success'=>'true', 'msg'=>"Testimony added successfully.",'html'=>$_html,'view_more'=>$view_more,'cur_page'=>$cur_page) );
			}
			 else
		   {
			  echo json_encode( array('success'=>'false', 'msg'=>"Please enter some text!", 'html'=>$_html) );
		   }
		
	}
	
	
  public function viewTestimony()
  {
	   
	   $i_prayer_id = $this->input->post('p_request_id');
	   $commit_arr = $this->prayer_wall_model->get_testimony_by_prayer_id($i_prayer_id);
	  //pr($commit_arr,1);
	  $content  = '';
	   if(count($commit_arr))
	   {
		     $profile_image_filename = get_profile_image_of_user('thumb',$commit_arr['s_profile_photo'],$commit_arr['e_gender']);
			 $DESC = html_entity_decode(htmlspecialchars_decode($commit_arr['s_description']),ENT_QUOTES,'utf-8');
			 
			   $content ='<div class="commitment-box" id="view_commit_'.$commit_arr['id'].'"> 
                    <a href="javascript:void(0);" class="profile-image"><div style="background:url('.$profile_image_filename.') no-repeat center;width:60px; height:60px;" ></div></a>
                    <div class="right-panel">
                        <p class="quoted-text">
                          '.$DESC.'
                            <span>Updated on: '.get_time_elapsed($commit_arr['dt_created_on']).'</span>
                        </p>
                        <h2 class="name">'.$commit_arr['s_profile_name'].'</h2>
                        <p class="place">'.$commit_arr['s_country'].'</p>
                        <a class="edit-testimony-btn" href="javascript:void(0);" onclick="show_edit_testimony('.$commit_arr['id'].')">Edit Testimony</a>
                    </div>
                    <div class="clr"></div>
              </div>
			  <div class="commit-form edit-testimony" id="edit-testimony'.$commit_arr['id'].'" style="display: none;">
				  <!--minimize link start -->
				  <div title="Minimize" class="minimize" onclick="minimize_edit_testimony('.$commit_arr['id'].')">&nbsp;</div>
				  <!--minimize link end -->
					  <h2>Edit Testimony</h2>
					  <form enctype="multipart/form-data" method="post" action="">
						  <label class="normal">Comment:</label>
						  <textarea class="full-textarea" name="ta_edit_testimony"  onfocus="if(this.value==\'Max 500 Characters\')this.value=\'\';" onblur="if(this.value==\'\')this.value=\'Max 500 Characters\';" id="ta_edit_testimony'.$commit_arr['id'].'" onKeyDown="limitText(this.form.ta_edit_testimony,this.form.countdown,500);" 
onKeyUp="limitText(this.form.ta_edit_testimony,this.form.countdown,500);" >'.$DESC.'</textarea>
						  <div class="clr"></div>
						  <input type="button" class="post-btn post-btn2" value="Submit" name="post" onclick="edit_testimony('.$commit_arr['id'].')">
					  </form>
					  <div class="clr"></div>
			 </div>';
			  
		   echo json_encode(array('des'=>base64_encode($content)));
	   }
	   else
		  echo json_encode(array('des'=>base64_encode('')));
  }
  
   public function delete_information ($id)
    {
		$i_ret=$this->prayer_wall_model->delete_by_id($id);
		$re_page =  base_url() ."manage-my-prayer-request.html";
					header("location:".$re_page);
					exit;
		
	} 
	
	
	public function prayer_wall_request_detail($id) 
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
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js',
										'js/jquery-ui-1.8.2.custom.min.js',
										'js/jquery/ui/jquery.ui.core.js',
										'js/jquery.ui.datepicker.js',*/
									    'js/jquery-ui-timepicker-addon.js',
//                                        'js/stepcarousel.js',
										'js/jquery-ui.triggeredAutocomplete.js',
										'js/jquery-ui-sliderAccess.js',
										'js/autocomplete/jquery.autocomplete.js',
//										'js/tab.js',
										'js/production/prayer_wall.js',
										'js/production/tweet-prayerwall.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.13.custom.css',
//										'css/jquery-ui-1.8.2.custom.css',
										'css/jquery.autocomplete.css') );
          
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			$data['prayer_info'] = $this->prayer_wall_model->get_by_id($id);			
			$data['prayer_commits_info'] = $this->prayer_commit_model->get_by_request_id($id);
			$data['prayer_photo'] = $this->prayer_wall_photos_model->get_all_prayer_wall_photos();
			#pr($data['prayer_info']);
			
			$data['pagination_per_page'] = $this->commits_pagination_per_page;
			
			ob_start();
		    $this->commitments_ajax_pagination($id);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['prayer_commit_content'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
			
			
            $VIEW = "logged/holy_place/prayer_wall/prayer-wall-request-detail.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }
	
	public function commitments_ajax_pagination($prayer_id , $page=0)
    {
		
		$cur_page = $page + $this->commits_pagination_per_page;
		
		$data = $this->data;
		$result = $this->prayer_commit_model->get_by_request_id($prayer_id, intval($page), $this->commits_pagination_per_page);
		//echo $this->db->last_query();
		$total_rows = $this->prayer_commit_model->get_total_by_request_id($prayer_id);
		//pr($result,1);
		$data['prayer_commits_info'] = $result;
		$data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
		$data['profile_id'] = $i_user_id;
		
		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->commits_pagination_per_page)
			  $view_more = false;
         //--------- end check
		
		
		$VIEW_FILE = "logged/holy_place/prayer_wall/ajax_view/ajax_commits_list.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$content, 'no_of_result'=>$data['no_of_result'],'view_more'=>$view_more, 'cur_page'=>$data['current_page_1']) );
			
	}
	
	
	
	### edit commitments
	
  public function edit_commitments($commit_id , $s_type)
  {
	  $user_id = intval(decrypt($this->session->userdata('user_id')));
	  $user_details = $this->users_model->fetch_this($user_id);
	  $arr_messages = array();
  
  
	  $message = nl2br( htmlspecialchars(trim($this->input->post('message')), ENT_QUOTES, 'utf-8') );
	  if(trim($this->input->post('message')) == 'Max 140 Characters'){
			  $arr_messages['message_'.$s_type.'_'.$commit_id] ='* Required Field.';
	  }
	  
	  /*if( trim($this->input->post('start_time'))=='') 
	  {
			 $arr_messages['start_time_'.$s_type.'_'.$commit_id] = "* Required Field.";
	  }
	  elseif(!preg_match("/^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])?$/", trim($this->input->post('start_time')))){
			$arr_messages['start_time'.$p_request_id] = '* Please input time in 24 hours format.';
	  }
	  
	  if( trim($this->input->post('end_time'))=='') 
	  {
			  $arr_messages['end_time_'.$s_type.'_'.$commit_id] = "* Required Field.";
	  }
	  elseif(!preg_match("/^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])?$/", trim($this->input->post('end_time')))){
			$arr_messages['end_time_'.$s_type.'_'.$commit_id] = '* Please input time in 24 hours format.';
	  }*/
	  
	  if( trim($this->input->post('start_date'))=='') 
	  {
			  $arr_messages['start_date_'.$s_type.'_'.$commit_id] = "* Required Field.";
	  }
	  
	  if( trim($this->input->post('end_date'))=='') 
	  {
			  $arr_messages['end_date_'.$s_type.'_'.$commit_id] = "* Required Field.";
	  }
	  
	  if( trim($this->input->post('chk_day'))=='') 
	  {
			  $arr_messages['chk_day'.$s_type.'_'.$commit_id] = "* Required Field.";
	  }
	  
	  if(trim($this->input->post('chk_time')) == '') 
	  {
			  $arr_messages['chk_time'.$s_type.'_'.$commit_id] = "* Required Field.";
	  } 
	  
	  $_html='';
	  if(count($arr_messages) == 0)
	   {
			 
			 if($s_type == 'prayer_post'){
			  $arr = array();		
			  $arr['i_user_id'] = $user_id;
			  $arr['s_contents'] = $message;
			  $arr['dt_start_time'] = trim($this->input->post('start_date')).':00';
			  //get_db_dateformat($this->input->post('start_date'),'/').' '.$this->input->post('start_time') ; 
			  $arr['dt_end_time'] = trim($this->input->post('end_date')).':00';
			  //get_db_dateformat($this->input->post('end_date'),'/').' '.$this->input->post('end_time') ;
			  $arr['dt_updated_on'] = get_db_datetime();
			  $arr['i_prayer_req_id'] =trim($this->input->post('prayer_id'));
			  $arr['s_weekdays'] = substr($this->input->post('chk_day'),0, -2);
			  $arr['s_time'] =   substr($this->input->post('chk_time'),0, -2);
			   
			  $this->prayer_commit_model->update($arr, $commit_id);
			  
			 
			  
			  
			  ### add to do list 
			  $request_id = trim($this->input->post('prayer_id'));
			  $prayer_arr = $this->prayer_wall_model->get_by_id($request_id); 
			  $this->load->model('organizer_todo_model');
			  
			  ### getting days
			  $day_arr = GetDays(trim($this->input->post('start_date')), trim($this->input->post('end_date')));  
			 
			  
			  $time_arr = array();
			  $time_arr = explode(', ',substr($this->input->post('chk_time'),0, -2));
			  
			 // pr($time_arr);
			  // pr($day_arr,1);
			  if(count($day_arr)){ 
			  
			   ### delete all to-do enter new ###
			    $sql = " DELETE FROM cg_organizer_to_do_list WHERE i_request_id = {$request_id} ";
			    $this->db->query($sql); 
				
			  	foreach($day_arr as $val){ 
					  
					### time slots
					if(count($time_arr)){  
					  
					  foreach($time_arr as $t_val){
						  
						  $t_arr = explode('-',$t_val);
						  $strt_time = $t_arr[0];
						  
						  $info = array();
						  $info['i_user_id'] = intval(decrypt($this->session->userdata('user_id')));
						  $info['s_description'] = " ".get_formatted_string($prayer_arr['s_subject']).".";
						  $info["d_date"] = $val;
						  $info["t_start_time"] = $t_arr[0];
						  $info["t_end_time"] = $t_arr[1];
						  $info["t_remind_time"] = '00:15:00';
						  
						 	//pr($info,1);				  
						  $date_a = new DateTime($info["d_date"].' '.$info["t_start_time"]);
						  $date_b = new DateTime($info["d_date"].' '.trim($this->input->post('todo_rem_time')));
			
						  $interval = date_diff($date_a,$date_b);
			
						  $info["t_remind_me_back"] = $interval->format('%h:%i:%s');
						  $info["i_request_id"] = $request_id;
						  
						  $info['dt_created_on'] = get_db_datetime();
						  $_ret = $this->organizer_todo_model->insert($info);
					  }
					}
				 }
			  }
			  
			  ### add to do list
			  
			 }
			 else if($s_type == 'intercession_post'){
				 
				 $this->load->model('intercession_model');
				 $arr = array();		
			  	 $arr['i_user_id'] = $user_id;
			  	 $arr['s_contents'] = $message;
			     $arr['dt_start_time'] = trim($this->input->post('start_date')).':00';//
				 // get_db_dateformat($this->input->post('start_date'),'/').' '.$this->input->post('start_time') ; 
			     $arr['dt_end_time'] = trim($this->input->post('end_date')).':00';
				 //get_db_dateformat($this->input->post('end_date'),'/').' '.$this->input->post('end_time') ;
			  	 $arr['dt_updated_on'] = get_db_datetime();
			  	 $arr['i_id_intercession_wall_post'] =trim($this->input->post('prayer_id'));
				 $arr['s_weekdays'] = substr($this->input->post('chk_day'),0, -2);
				 $arr['s_time'] =   substr($this->input->post('chk_time'),0, -2);
				
			    // pr($arr,1);
			  	 $commit_id = $this->intercession_model->update_commitments($arr , $commit_id);
				 
				  ### add to do list 
					$request_id = trim($this->input->post('prayer_id'));
					$prayer_arr = $this->intercession_model->get_info_by_intercession_id($request_id); 
					
					$request_id =  'inter#'.$request_id;
					$this->load->model('organizer_todo_model');
					
					$strt = explode(' ',trim($this->input->post('start_date')));
					$end = explode(' ',trim($this->input->post('end_date')));
					//$day_arr  = GetDays('2014-05-30','2014-05-31'); pr($day_arr,1);
					### getting days
					$day_arr = GetDays($strt[0], $end[0]);  
				  
					
					$time_arr = array();
					$time_arr = explode(', ',substr($this->input->post('chk_time'),0, -2));
					
				    //pr($time_arr);
					 
					if(count($day_arr)){ 
					
					 ### delete all to-do enter new ###
					  $sql = " DELETE FROM cg_organizer_to_do_list WHERE i_request_id = '{$request_id}'";
					  $this->db->query($sql); 
					  
					  foreach($day_arr as $val){ 
							
						  ### time slots
						  if(count($time_arr)){  
							
							foreach($time_arr as $t_val){
								
								$t_arr = explode('-',$t_val);
								$strt_time = $t_arr[0];
								
								$info = array();
								$info['i_user_id'] = intval(decrypt($this->session->userdata('user_id')));
								$info['s_description'] = "Pray for: ".get_formatted_string($prayer_arr['s_subject']).".";
								$info["d_date"] = $val;
								$info["t_start_time"] = $t_arr[0];
								$info["t_end_time"] = $t_arr[1];
								$info["t_remind_time"] = '00:15:00';
								
								  //pr($info,1);				  
								$date_a = new DateTime($info["d_date"].' '.$info["t_start_time"]);
								$date_b = new DateTime($info["d_date"].' '.trim($this->input->post('todo_rem_time')));
				  
								$interval = date_diff($date_a,$date_b);
				  
								$info["t_remind_me_back"] = $interval->format('%h:%i:%s');
								$info["i_request_id"] = $request_id;
								
								$info['dt_created_on'] = get_db_datetime();
								$_ret = $this->organizer_todo_model->insert($info);
							}
						  }
					   }
					}
					
					### add to do list
			 }
			 
			 
			ob_start();
		    $this->my_all_commitments_ajax_pagination();
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$html = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
			 
			 
			 
			 
			  /*$notificaion_opt = $this->user_alert_model->check_option_user_id($tweet_info['i_owner_id']);	
			  $notification_arr = array();
	  
			  ## insert noifications ####
				  if($notificaion_opt['e_tweet_comments_received'] == 'Y' ){
					  
					  $notification_arr['i_requester_id'] = $user_id;
					  $notification_arr['i_accepter_id'] = $tweet_info['i_owner_id'];
					  $notification_arr['s_type'] = 'tweet_comment';
					  $notification_arr['dt_created_on'] = get_db_datetime();
					  
					  
					  $ret = $this->user_notifications_model->insert($notification_arr);	
					  $message_id = parent::social_notifications_message($notification_arr['i_requester_id'], $notification_arr['i_accepter_id'], 'tweet_comment', $tweet_id);
				  }
							  
							  
			   ### end  ##*/#
			  
			  echo json_encode( array('success'=>'true', 'msg'=>"Commitment updated successfully.",'html'=>$html,'view_more'=>$view_more,'cur_page'=>$cur_page, 'total_commits'=>$total_commits) );
		  }
		   else
		 {
			echo json_encode( array('success'=>'false', 'arr_messages'=>$arr_messages, 'html'=>$_html) );
		 }
	  
  }
  
  
    public function delete_commitments($id, $s_type)
    {
		if($s_type == 'prayer_post'){
			
			$i_ret=$this->prayer_commit_model->delete_by_id($id);
		}
		else{
			
			 $this->load->model('intercession_model');
			$i_ret=$this->intercession_model->delete_commitments_by_id($id);
		}
		$re_page =  base_url() ."manage-my-commitments.html";
					header("location:".$re_page);
					exit;
		
	}
	
	public function search_prayer_request() 
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
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js',
                                        'js/stepcarousel.js',
										'js/jquery-ui-1.8.2.custom.min.js',
										'js/jquery/ui/jquery.ui.core.js',
										'js/jquery.ui.datepicker.js',*/
									    'js/jquery-ui-timepicker-addon.js',
										'js/jquery-ui-sliderAccess.js',
										'js/autocomplete/jquery.autocomplete.js',
										//'js/tab.js',
										'js/production/prayer_wall.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
										'css/jquery.autocomplete.css') );
          	
			$i_user_id = intval(decrypt($this->session->userdata('user_id')));
            
			$data['prayer_photo'] = $this->prayer_wall_photos_model->get_all_prayer_wall_photos();
			$data['pagination_per_page'] = $this->pagination_per_page;
			
			$this->session->set_userdata('search_condition','');
			$this->session->set_userdata('search_non_exact','');
			
			$WHERE_COND = '';
			$NON_EXACT_WHERE = '';
        	$S_NON_EXACT_WHERE = "";
		  
		   	if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) {
				
				
			 $location = get_formatted_string(trim($this->input->post('txt_location'))); 
			  if($location != '')
			  {
				  $location_arr = explode(',',$location);
			  }
			  $total_locations = count($location_arr);
				
			   if($total_locations)
				{
					
					
					
					for($i=0;$i<$total_locations;$i++)
					{
						
						if($i== 0){
							 $WHERE_COND .= "  (mst_c.s_country like '".trim($location_arr[$i])."%' OR  s.s_state like '".$location_arr[$i]."%' OR ct.s_city like '".$location_arr[$i]."%')";
							 
							 $NON_EXACT_WHERE .= " (mst_c.s_country like '".trim($location_arr[$i])."%' OR s.s_state like '".trim($location_arr[$i])."%' OR ct.s_city like '".trim($location_arr[$i])."%')";
						}
						else{
							
							$WHERE_COND .= "  AND (mst_c.s_country like '{$location_arr[$i]}%' OR  s.s_state like '{$location_arr[$i]}%' OR ct.s_city like '{$location_arr[$i]}%')";
							
							$NON_EXACT_WHERE .= " OR (mst_c.s_country like '".trim($location_arr[$i])."%' OR s.s_state like '".trim($location_arr[$i])."%' OR ct.s_city like '".trim($location_arr[$i])."%')";
						}
						
											
					}
					
				}	
			  
			 if($this->input->post('srch_date_to') != '' && $this->input->post('srch_date_end') != ''){ 
			  
			  	$dt_start_date = $this->input->post('srch_date_to');
				$dt_end_date = $this->input->post('srch_date_end');
			 	
				  if($WHERE_COND != ''){
					  $WHERE_COND .= ($dt_start_date=='' && $dt_end_date == '')?''
					  				:" AND  ( DATE_FORMAT(p.dt_start_date , '%Y-%m-%d %H:%i') BETWEEN
										'".$dt_start_date."' AND '".$dt_end_date."'
										AND 
										    DATE_FORMAT(p.dt_end_date , '%Y-%m-%d %H:%i') BETWEEN
										'".$dt_start_date."' AND '".$dt_end_date."' )";
				  }
				  else
				  {
					  $WHERE_COND .= ($dt_start_date=='' && $dt_end_date == '')?'':" ( DATE_FORMAT(p.dt_start_date , '%Y-%m-%d %H:%i') BETWEEN
										'".$dt_start_date."' AND '".$dt_end_date."'
										AND 
										  DATE_FORMAT(p.dt_end_date , '%Y-%m-%d %H:%i') BETWEEN
										'".$dt_start_date."' AND '".$dt_end_date."' )";
				  }
				  
				  if($NON_EXACT_WHERE != ''){
					  $NON_EXACT_WHERE .=    ($dt_start_date=='' && $dt_end_date == '')?'':" OR ( DATE_FORMAT(p.dt_start_date , '%Y-%m-%d %H:%i') BETWEEN
										'".$dt_start_date."' AND '".$dt_end_date."'
										OR 
										  DATE_FORMAT(p.dt_end_date , '%Y-%m-%d %H:%i') BETWEEN
										'".$dt_start_date."' AND '".$dt_end_date."' ) ";
				  }
				  else{
					  $NON_EXACT_WHERE .=    ($dt_start_date=='' && $dt_end_date == '')?'':"  ( DATE_FORMAT(p.dt_start_date , '%Y-%m-%d %H:%i') BETWEEN
										'".$dt_start_date."' AND '".$dt_end_date."'
										OR 
										 DATE_FORMAT(p.dt_end_date , '%Y-%m-%d %H:%i') BETWEEN
										'".$dt_start_date."' AND '".$dt_end_date."' )";
				  }
				
			 }
			  
			 else if($this->input->post('srch_date_to') != ''){
					$dt_start_date = $this->input->post('srch_date_to');
					
					if($WHERE_COND != ''){
						$WHERE_COND .= ($dt_start_date=='')?'':" AND  ( DATE_FORMAT(p.dt_start_date , '%Y-%m-%d %H:%i')='".$dt_start_date."' )";
					}
					else
					{
						$WHERE_COND .= ($dt_start_date=='')?'':" ( DATE_FORMAT(p.dt_start_date , '%Y-%m-%d %H:%i')='".$dt_start_date."' )";
					}
					
					if($NON_EXACT_WHERE != ''){
						$NON_EXACT_WHERE .=    ($dt_start_date=='')?'':" OR  ( DATE_FORMAT(p.dt_start_date , '%Y-%m-%d %H:%i')='".$dt_start_date."' )";
					}
					else{
						$NON_EXACT_WHERE .=    ($dt_start_date=='')?'':"  ( DATE_FORMAT(p.dt_start_date , '%Y-%m-%d %H:%i')='".$dt_start_date."' )";
					}
			  }
			  
			 else if($this->input->post('srch_date_end') != ''){
					 $dt_end_date = $this->input->post('srch_date_end');
					 
					 if($WHERE_COND != ''){
					 	 $WHERE_COND .= ($dt_end_date=='')?'':" AND ( DATE_FORMAT(p.dt_end_date , '%Y-%m-%d %H:%i') ='".$dt_end_date."' )";
					 }
					 else{
						 $WHERE_COND .= ($dt_end_date=='')?'':"  ( DATE_FORMAT(p.dt_end_date , '%Y-%m-%d %H:%i') ='".$dt_end_date."' )";
					 }
					 
					if($NON_EXACT_WHERE != ''){ 
					 	$NON_EXACT_WHERE .=  ($dt_end_date=='')?'':" OR ( DATE_FORMAT(p.dt_end_date , '%Y-%m-%d %H:%i') ='".$dt_end_date."' )";
					}
					else{
						$NON_EXACT_WHERE .=  ($dt_end_date=='')?'':"  ( DATE_FORMAT(p.dt_end_date , '%Y-%m-%d %H:%i') ='".$dt_end_date."' )";
					}
			  }
			  
			  $srch_request_type = (trim($this->input->post('srch_request_type')));
			  
			  if($srch_request_type == '1'){
				  
				  if($WHERE_COND != ''){
			 		 $WHERE_COND .= " AND (p.e_request_type  =  'Emergency')";
				  }
				  else{
					  $WHERE_COND .= "  (p.e_request_type  =  'Emergency')";
				  }
					
				  if($NON_EXACT_WHERE != ''){
					 $NON_EXACT_WHERE .=  " OR (p.e_request_type  =  'Emergency')";
				  }
				  else{
					  $NON_EXACT_WHERE .=  " (p.e_request_type  =  'Emergency')";
				  }
					 
			  }else if($srch_request_type == '2') {
				  
				  if($WHERE_COND != ''){
				     $WHERE_COND .= " AND (p.e_request_type  =  'On Going')";
				  }
				  else{
					  $WHERE_COND .= "  (p.e_request_type  =  'On Going')";
				  }
				  
				  if($NON_EXACT_WHERE != ''){
					$NON_EXACT_WHERE .=  " OR (p.e_request_type  =  'On Going')";
				  }
				  else{
					 $NON_EXACT_WHERE .=  "  (p.e_request_type  =  'On Going')"; 
				  }
			  }
		  
		  		 $this->session->set_userdata('search_condition',$WHERE_COND);
				 $this->session->set_userdata('search_non_exact',$NON_EXACT_WHERE);
				 
				 $this->session->set_userdata('location',$location);
				 $this->session->set_userdata('txt_srch_from_time',$txt_srch_from_time);
				 $this->session->set_userdata('txt_srch_to_time',$txt_srch_to_time);
				 $this->session->set_userdata('dt_start_date',$this->input->post('srch_date_to'));
				 $this->session->set_userdata('dt_end_date',$this->input->post('srch_date_end'));
				 $this->session->set_userdata('srch_request_type',$srch_request_type);
		   }
		   else{
			     $this->session->set_userdata('location','');
				 $this->session->set_userdata('txt_srch_from_time','');
				 $this->session->set_userdata('txt_srch_to_time','');
				 $this->session->set_userdata('dt_start_date','');
				 $this->session->set_userdata('dt_end_date','');
				 $this->session->set_userdata('srch_request_type','');
		   }
			
			//echo $this->session->userdata('search_condition'); 
			ob_start();
		    $this->search_all_prayer_request_ajax_pagination(0);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['prayer_req_ajax_content'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
			
			# view file...
			
            $VIEW = "logged/holy_place/prayer_wall/search-all-prayer-request.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }
	
	
	public function search_all_prayer_request_ajax_pagination($page=0)
    {
		
		//echo $page;
		## seacrh conditions : filter ############
		 
		if($this->session->userdata('search_condition') != ''){
			$s_where = ' AND( '.$this->session->userdata('search_condition'). ') ';
		}
		if($this->session->userdata('search_non_exact') != ''){
			$s_non_exact_where = ' AND( '.$this->session->userdata('search_non_exact'). ') ';
		}
		//exit;
		$cur_page = $page + $this->pagination_per_page;
		
		$data = $this->data;
		
		  
		$result = $this->prayer_wall_model->get_all_prayer_srch_result($s_where, $s_non_exact_where, intval($page), $this->pagination_per_page); 
	    $total_rows = $this->prayer_wall_model->gettotal_prayer_srch_result($s_where, $s_non_exact_where);
		
		//pr($result,1);
		$data['arr_prayer_request'] = $result;
		$data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
		$data['profile_id'] = $i_user_id;
		
		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->pagination_per_page)
			  $view_more = false;
         //--------- end check
		
		
		$VIEW_FILE = "logged/holy_place/prayer_wall/ajax_view/search_all_prayer_request_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$content, 'no_of_result'=>$data['no_of_result'],'view_more'=>$view_more, 'cur_page'=>$data['current_page_1']) );
			
	}
	
	
	### new added
	public function update_testimony($p_request_id)
	{
		$user_id = intval(decrypt($this->session->userdata('user_id')));
		$user_details = $this->users_model->fetch_this($user_id);
		$arr_messages = array();


		$message = nl2br( htmlspecialchars(trim($this->input->post('message')), ENT_QUOTES, 'utf-8') );
		if(trim($this->input->post('message')) == 'Max 500 Characters'){
				$message = '';
		}
		
        $_html='';
		if($message != '')
		 {
				$arr = array();		
				$arr['s_description'] = $message;
								
				$commit_id = $this->prayer_wall_model->update_testimony_by_prayer_id($p_request_id, $arr['s_description']);
			
								
				 ### end  ##*/#
                
				echo json_encode( array('success'=>'true', 'msg'=>"Testimony updated successfully.",'html'=>$_html,'view_more'=>$view_more,'cur_page'=>$cur_page) );
			}
			 else
		   {
			  echo json_encode( array('success'=>'false', 'msg'=>"Please enter some text!", 'html'=>$_html) );
		   }
		
	}
	
	
	
	### search city state country for auto suggest #####
	
	public function send_cities()
	{
		$letter = $_GET['q']; 
		$this->load->model('location_model');
		
		$arr_category = $this->location_model->get_search_detail($letter); 
		
		//pr($arr_category);
		
		if(count($arr_category)>0)
			foreach($arr_category as $val)
			{
				if($val['result_type'] == 'country_res'){
					echo  $val['s_country']."\n";
				}
				else if($val['result_type'] == 'state_res'){
					echo  $val['s_state'].', '.$val['s_country']."\n";
				}
				else {
					echo  $val['s_city'].', '.$val['s_state'].', '.$val['s_country']."\n";
				}
				
			}
		else
			echo "No Result.";
		exit;
	}
	
	
	public function getCarriageCount(){
		
		$inputString = ($this->input->post('str'));
		$enter = 0;
		$data = strtolower ($inputString);
		foreach (count_chars ($data, 1) as $i => $val)
			{
				if ($enter == 1)
				{
					$enter = 0;
					continue;
				}
				if (chr ($i) == "\n")
				{
					//echo "There are $val instance(s) of \" Enter \" in the string.\n";
					$enter = 1;
					$count = $val;
				}
				
			}
		/*
		$count= substr_count($str, "\r\n"); //preg_match_all ('/\r\n/',$str);
		pr($count);*/
		echo json_encode( array('success'=>'true', 'count'=>$count) );
	}
	
	//new functions for comments
	public function post_comment($p_request_id)
	{
		
		$posted['message']=$this->input->post('message');
		
		if($posted['message'] == '' || $posted['message'] =='Max 500 Characters')
		{
			echo json_encode(array('success'=>false,'msg'=>'Please add your comment.'));
			exit;
		}
		else{
                     $ip = getenv("REMOTE_ADDR") ; 
		$info['i_user_id']=intval(decrypt($_SESSION['user_id']));
		$info['i_prayer_id']=$p_request_id;
		$info['s_contents']=trim($posted['message']);
		$info['dt_created_on']=get_db_datetime();
                $info['u_ip'] = $ip;
		$this->db->insert('cg_prayer_wall_comments',$info);
		
		$total_comments=$this->prayer_wall_model->get_total_comments_prayer_wall($p_request_id);
		echo json_encode(array('success'=>true,'msg'=>'comment posted successfully.','comm_count'=>$total_comments));
		exit;
		}
	}
	
	
	 public function viewComments()
  {
	   
	   $i_prayer_id = $this->input->post('p_request_id');
	   $comment_arr = $this->prayer_wall_model->get_comment_by_request_id($i_prayer_id);
	 
	  // pr($total_comments,1);
	   if(count($comment_arr))
	   {
		      
			  	$VIEW_FILE = "logged/holy_place/prayer_wall/ajax_view/single_comment_ajax.phtml";
			  
	  
			  $data['comment_arr'] = $comment_arr;
			  
			  $content = $this->load->view( $VIEW_FILE , $data, true);
			  
		   echo json_encode(array('des'=>base64_encode($content)));
	   }
	   else
		  echo json_encode(array('des'=>base64_encode('')));
  }
	
	
	
	
}   // end of controller...

