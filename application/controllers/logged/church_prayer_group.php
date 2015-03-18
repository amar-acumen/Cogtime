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


class Church_prayer_group extends Base_controller
{
    
    private $pagination_per_page =  10;
	private $post_pagination_per_page = 5;
	private $notification_per_page = 15;

    public function __construct()
     {
	 	
        try
        {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            parent::check_church_id_empty(TRUE, $_SESSION['logged_church_id'], array('1'));
            # loading reqired model & helpers...
			
            //$this->load->model('users_model');
			$this->load->model('user_alert_model');
			$this->load->model('user_notifications_model');
            $this->load->model('prayer_group_model');
			//$this->load->model('contacts_model');
			//$this->load->model('my_prayer_partner_model');

			$this->load->model('organizer_todo_model');
			$this->load->model('church_new_model');
			$this->max_prayer_group = $this->config->item('max_prayer_group');
			
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    
    public function index($c_id) 
    {
        try
        {
		
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
            //$this->data["MAIN_MENU_SELECTED"] = 6;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( 
                                        'js/lightbox.js'
										                                    ));
                                        
            parent::_add_church_css_arr( array('css/church.css','css/church_admin.css') );
			
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			$this->load->model('church_new_model');
			$data['church_arr'] =$this->church_new_model->get_church_info($c_id);
			$data['church_admin'] = $this->church_new_model->get_church_admin_data($c_id);
                        $data['grp_arr'] = $this->church_new_model->get_group_info($c_id);
                        //$data[''] = 
			$this->session->set_userdata('prayer_group_id','');
			$this->session->set_userdata('search_condition','');
			$_SESSION['logged_church_id'] = $c_id;
			$user_id = intval(decrypt($this->session->userdata('user_id')));
           // parent::check_is_church_admin($user_id,$c_id);
		   /*if(subadmin_access('prayer_group'))
			{
				$data['church_list']=$this->church_new_model->get_prayer_group_by_church($c_id);
				$data['church_admin']=true;
			}
			else
			{
				$data['church_list']=$this->church_new_model->get_prayer_group_by_user_id($c_id,$user_id);
				$data['church_admin']=false;
			}*/
		$data['notification_per_page'] = $this->notification_per_page;
			
			#### get group dropdown
			
		//	$data['all_grp_arr'] = $this->prayer_group_model->get_my_groups_names($i_user_id);
			
			
			
			ob_start();
			$this->groups_ajax_listing($page,$c_id);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['group_listing'] = $content_obj->grp_html;
			ob_end_clean();
			
			ob_start();
			$this->notifications_ajax_pagination($page);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['notification_listing'] = $content_obj->html;
			$data['no_of_result']  = $content_obj->no_of_result;
			ob_end_clean();
			
			
			# view file...
			
            $VIEW = "logged/church/church_prayer_group.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }
	
	public function create_prayer_group($c_id)
	{
	 try
        {
			
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
            //$this->data["MAIN_MENU_SELECTED"] = 6;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            /*parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js',
										'js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',
										'js/tab.js',
										'js/frontend/logged/holy_place/prayer_group.js',

                                        ));*/
                                        
            parent::_add_church_css_arr( array('css/church.css','css/church_admin.css','css/jquery.multiselect.css','css/jquery.multiselect.filter.css','css/jquery-ui-1.8.2.custom.css') );
            
             parent::_add_js_arr( array( 
										'js/jquery.multiselect.js',
										'js/jquery.multiselect.filter.js'
                                        ));
                                        
            //parent::_add_church_css_arr( array('css/church.css','css/jquery.multiselect.css','css/jquery.multiselect.filter.css','css/jquery-ui-1.8.2.custom.css') );
			$_SESSION['logged_church_id'] = $c_id;
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			$this->load->model('church_new_model');
			$data['church_arr'] =$this->church_new_model->get_church_info($c_id);
			$data['church_admin'] = $this->church_new_model->get_church_admin_data($c_id);
			$data['active_menu']='create_group';
			$this->session->set_userdata('prayer_group_id','');
			$this->session->set_userdata('search_condition','');
			
			//$data['notification_per_page'] = $this->notification_per_page;
			
			#### get group dropdown
			
		//	$data['all_grp_arr'] = $this->prayer_group_model->get_my_groups_names($i_user_id);
			
			
			
			# view file...
			if($_POST)
			{
				
				$arr['s_group_name']=$this->input->post('txt_group_name');
				$arr['i_owner_id']=$c_id;
				$arr['i_denomination_id']=decrypt($this->input->post('sel_denomination'));
				$arr['dt_created_on']=get_db_datetime();
				$arr['i_isenabled']=1;
				$cid=$this->church_new_model->create_prayer_group($arr);
                                //echo $cid;
                                //die();
                                /********************************************************/
                                
                               // echo $c_id;
                                
                                 $arr_frnd=array();
                             $arr_frnd=$this->input->post('frndinv');
                            // pr($arr_frnd);
  //pr($arr_frnd,1);             
  //pr($complete_arr_frnd,1);
  //insert_invitation($group_id,$_POST,$this->db->prayer_group_invitation,'i_prayer_group_id','prayer_group',$group_id);
  //$invited=get_invited($group_id,$this->db->prayer_group_invitation,'i_prayer_group_id');
                               $this->load->model('church_new_model');
                                foreach($arr_frnd as $k=>$val){
                                  $info = array();

                                  $info['i_prayer_group_id'] = $cid;
                                  $info['i_user_id'] =  $val;
                                  $info['s_status'] = 'pending';
                                  $info['dt_created_on'] = get_db_datetime();

                                  $_ret = $this->church_new_model->insert_group_member($info);

                                //echo $_ret;
                                  /*******************get grp id************************/
                                  $result=$this->db->get_where('cg_church_prayer_group_members',array('id'=>$_ret));
                                 $res =  $result->result();
                                  $grp_id = $res[0]->i_prayer_group_id;
                                // pr($res,1);
                                  /*********************************/
                                /*******************************************************/
                                /************************notification*******************************/
                          $grp_info  = $this->church_new_model->get_prayer_group_details($grp_id);
                         // pr($grp_info,1);
                          $message=get_username_by_id($val).' has invited  to join at '.$grp_info[0]->s_group_name." on ".getShortDateWithTime(get_db_datetime(),2);
                          $grp_notification_arr = array();
   
                            $grp_notification_arr['i_requester_user_id'] = $val;
                            //$grp_notification_arr['i_user_id'] = $owner_id;
                            $grp_notification_arr['i_prayer_group_id'] = $grp_id;
                            $grp_notification_arr['s_type'] = 'invited';
                            $grp_notification_arr['msg'] = $message;
                            $grp_notification_arr['dt_created_on'] = get_db_datetime();
                            $grp_notification_arr['church_id'] = $_SESSION['logged_church_id'];
                            $this->church_new_model->insert_group_notifications($grp_notification_arr);
                                
						$mail_arr['i_requester_id'] =get_username_by_id( decrypt($this->session->userdata('user_id')));
						$mail_arr['i_accepter_id'] =  get_username_by_id($val);
						$mail_arr['s_type'] = 'e_prayer_grp_invitation';
						$mail_arr['group_name']='';
						$mail_id=get_useremail_by_id($val);
						 $this->load->library('email');
						 $this->load->helper('html');
						 //echo $mail_id;exit;
					$email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
					  $body=$this->load->view('logged/my-mail-template.phtml',$mail_arr,TRUE);
						$this->email->initialize($email_setting);
						$this->email->from('admin@cogtime.com', 'Team Cogtime');
					$this->email->to("$mail_id");
						//$this->email->bcc("$mailids");
				//$this->email->cc('arif.zisu@gmail.com');
					//$this->email->bcc('trisha.paul@hotmail.com');

					$this->email->subject($mail_arr["i_requester_id"].' has invited you to his/her Prayer Group.');
					$this->email->message("$body");

					$this->email->send();
			}
                          /********************************************************/
                                /*********************************************************/
                                
                                
                                /*****************************************/
                                
                                
                                
                                
                                
                                
				if($cid != '')
				{
					echo json_encode(array("success"=>true,"msg"=>"Prayer group created successfully" ));
					exit;
				}
				else
				{
					echo json_encode(array("success"=>false,"msg"=>"Eror!"));
					exit;
				}
				
			}
			
				$VIEW = "logged/church/create_church_prayer_group.phtml"; 
				parent::_render($data, $VIEW);
		
           
        }
		catch(Exception $err_obj)
        {
           
        } 

	}
	public function request($c_id) 
    {
        try
        {
			
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
           // $this->data["MAIN_MENU_SELECTED"] = 6;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
              
            parent::_add_church_css_arr( array('css/church.css') );
          
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			$this->session->set_userdata('prayer_group_id','');
			$this->session->set_userdata('search_condition1','');
			$this->session->set_userdata('search_condition2',$whr_cond);
			
			$data['notification_per_page'] = $this->notification_per_page;
			
			#### get group dropdown
			
			//$data['my_grp_arr'] = $this->prayer_group_model->get_my_groups_names_array($i_user_id);
			if(subadmin_access('prayer_group'))
			{
				$data['all_grp_arr'] = $this->church_new_model->get_prayer_group_by_church($_SESSION['logged_church_id'],'id','desc');
			}
			else
			{
				$data['all_grp_arr'] = $this->church_new_model->get_prayer_group_by_user_id($_SESSION['logged_church_id'],$i_user_id,'cp.id Desc');
			}
			ob_start();
			$this->get_pending_groups_requests_sent($page);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['request_sent'] = $content_obj->grp_html;
			ob_end_clean();
			
			ob_start();
			$this->get_pending_groups_requests_recieved($page);
			$content2 = ob_get_contents();
			$content_obj = json_decode($content2); 
			$data['request_recv'] = $content_obj->grp_html;
			ob_end_clean();
			
			
			$this->load->model('church_new_model');
			
			//$data['invitation_list']=$this->church_new_model->get_invitation($c_id,$i_user_id);
			$data['prayer_group_id']=$c_id;
			$data['active_menu']='invite';
			//pr($data['invitation_list'],1);
			
			# view file...
			
            $VIEW = "logged/church/church_prayer_group_invitation_list.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }
	
	
	public function get_pending_groups_requests_sent($page=0, $notif_grp_by = '-1')
	{
		
		$user_id = intval(decrypt($this->session->userdata('user_id')));
		$s_where = '';
		$whr_cond = "";
		
		if($notif_grp_by != '-1'){
			
			$whr_cond = " AND pg.id = '".$notif_grp_by."' ";
			$this->session->set_userdata('search_condition1',$whr_cond);
			$s_where = $this->session->userdata('search_condition1');
		}
		if(subadmin_access('prayer_group'))
		{
		$data['prayer_grp_info'] = $this->church_new_model->get_pending_groups_requests_sent_for_church($_SESSION['logged_church_id'],$s_where); 
		}
		else
		{
			$data['prayer_grp_info'] = $this->church_new_model->get_pending_groups_requests_sent($user_id,$_SESSION['logged_church_id'],$s_where); 
		}
		$data['type'] = 'sent';
		
		//pr($data['prayer_grp_info'],1);
		$VIEW_FILE = "logged/church/church_prayer_group_sent_invitation_list.phtml";
		
		$content = $this->load->view( $VIEW_FILE , $data, true);
		//echo $content;exit;
        echo json_encode( array('grp_html'=>$content) );
	}
	
	### prayer requset recv  ajax
	
	public function get_pending_groups_requests_recieved($page=0, $notif_grp_by = '-1')
	{
		
		$user_id = intval(decrypt($this->session->userdata('user_id')));
		$s_where = '';
		$whr_cond = "";
		
		if($notif_grp_by != '-1'){
			
			$whr_cond = " AND pg.id = '".$notif_grp_by."' ";
			$this->session->set_userdata('search_condition2',$whr_cond);
			$s_where = $this->session->userdata('search_condition2');
		}
		if(subadmin_access('prayer_group'))
		{
			$data['prayer_grp_info_arr'] = $this->church_new_model->get_pending_groups_requests_recieved_for_church($_SESSION['logged_church_id'],$s_where); 
		}
		else
		{
			$data['prayer_grp_info_arr'] = $this->church_new_model->get_pending_groups_requests_recieved($user_id ,$_SESSION['logged_church_id'],$s_where); 
		}
		$data['type'] = 'recv'; //pr($data['prayer_grp_info'],1);
	
		$VIEW_FILE = "logged/church/church_prayer_group_recv_invitation_list.phtml";
		
		$content = $this->load->view( $VIEW_FILE , $data, true);
        echo json_encode( array('grp_html'=>$content) );
	}
	
	
	
	
	
	### prayer group notificaions
	
	public function notifications_ajax_pagination($page=0, $notif_grp_by = '-1')
    {
		
		$cur_page = $page + $this->notification_per_page;
		$data = $this->data;
		$s_where = '';
		$whr_cond = "";
		
		if($notif_grp_by != '-1'){
			
			 $whr_cond = " AND i_prayer_group_id = '".$notif_grp_by."' ";
			$this->session->set_userdata('search_condition',$whr_cond);
			$s_where = $this->session->userdata('search_condition');
		}
		
        
		$user_id = intval(decrypt($this->session->userdata('user_id')));
		
		$result = $this->church_new_model->get_my_groups_notificaions($user_id, $s_where, intval($page), $this->notification_per_page);
		$total_rows = $this->church_new_model->get_total_my_groups_notificaions($user_id, $s_where);

		$data['result_arr'] = $result;
		$data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
		
		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->notification_per_page)
			  $view_more = false;
         //--------- end check
		
		
		$VIEW_FILE = "logged/church/notification_list_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
        echo json_encode( array('html'=>$content, 'no_of_result'=>$data['no_of_result'],'view_more'=>$view_more, 'cur_page'=>$data['current_page_1']) );
			
	}
	
	### prayer groups ajax
	
	public function groups_ajax_listing($page=0,$c_id, $sort_order = 1, $grp_val = 'all')
	{
		
		$user_id = intval(decrypt($this->session->userdata('user_id')));
		
		//$data['prayer_grp_info'] = $this->prayer_group_model->get_my_groups($user_id,'','','',$orderby, $grp_val); 
		
		//$data['i_max_prayer_grp'] = $this->data['site_settings_arr']['i_max_prayer_grp'];
		$user_id = intval(decrypt($this->session->userdata('user_id')));
           // parent::check_is_church_admin($user_id,$c_id);
		   if(subadmin_access('prayer_group'))
			{
				//$orderby = ($sort_order == 1)?'cp.dt_created_on DESC':'cp.s_group_name ASC';
				if($sort_order == 1)
				{
					$order="dt_created_on";
					$direction="DESC";
				}
				else 
				{
					$order="s_group_name";
					$direction="ASC";
				}
				$data['church_list']=$this->church_new_model->get_prayer_group_by_church($c_id,$order,$direction);
				$data['church_admin']=true;
			}
			else
			{
				$orderby = ($sort_order == 1)?'cm.dt_joined_on DESC':'cp.s_group_name ASC';
				$data['church_list']=$this->church_new_model->get_prayer_group_by_user_id($c_id,$user_id,$orderby);
				//pr($data['church_list']);
				$data['church_admin']=false;
			}
			//pr($data);exit;
		$VIEW_FILE = "logged/church/prayer_group_listing_ajax.phtml";
		
		$content = $this->load->view( $VIEW_FILE , $data, true);
        echo json_encode( array('grp_html'=>$content) );
	}
	
	
	
	
	
	
	public function prayer_group_detail($prayer_group_id) 
    {
        try
        {
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');

            parent::_add_church_css_arr( array('css/church.css') );
			$this->load->model('church_new_model');
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			
			$data['prayer_group_id']=$prayer_group_id;
			$data['post_pagination_per_page'] = $this->post_pagination_per_page;
			
			//$data['prayer_detail_arr'] = $this->church_new_model->get_group_detail_by_id($prayer_group_id);
			//$data['prayer_member_arr'] = $this->church_new_model->get_members_by_grpid($prayer_group_id);
			$data['group_posts'] = $this->church_new_model->get_posts_by_grpid($prayer_group_id);
			$data['prayer_group_details'] = $this->church_new_model->get_prayer_group_details($prayer_group_id);
			$data['active_menu']='prayer-groups';
			//$this->grp_posts_ajax_pagination($prayer_group_id,$page);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['postlists'] = $content_obj->html;
			$data['no_of_result']  = $content_obj->no_of_result;
			ob_end_clean();
            # view file...
            
            $VIEW = "logged/church/prayer-group-details.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }
	
	## show suggesstion
	
	public function search_similar_prayer_group()
    {
        
            $arr_messages = array();
            $group_name    = trim($this->input->post("txt_group_name"));   
            # error message trapping...
            if( trim($this->input->post("txt_group_name"))=='') 
            {
                    $arr_messages['group_name'] = "* Required Field.";
            }
              
			if( trim($this->input->post("sel_denomination"))=='') 
            {
                    $arr_messages['denomination'] = "* Required Field.";
            }          
            
			
			$is_exists = $this->prayer_group_model->checkGroupNameExists(mysql_real_escape_string(trim($this->input->post("txt_group_name"))),  intval(decrypt($this->session->userdata('user_id'))));
            
			if($is_exists){
				
				 $arr_messages['grp_name'] = "* You have already created prayer group with this name, Please provide another name!";
			}
           //pr($arr_messages);
            if( count($arr_messages)==0 ) {
                    
                ### get list of similar group name and denomination
				 $d_id = intval(decrypt($this->input->post('sel_denomination'))); 
				 $grp_name = mysql_real_escape_string(trim($this->input->post('txt_group_name')));
				 
				 $where_grp = " AND s_group_name LIKE '%{$grp_name}%' AND i_denomination_id = {$d_id} ";
				
			   	 $data['grp_list']  = $this->prayer_group_model->group_list($where_grp);
				 $total_grp = $this->prayer_group_model->group_list_count($where_grp);
					
			    ### get list of similar group name and denomination
				$VIEW = "logged/holy_place/prayer_group/similar_grp_listing.phtml";
				$html = $this->load->view($VIEW,$data, true);
                                
                echo json_encode( array('success'=>true, 'grp_html'=>$html, 'count'=>$total_grp) );
            }
            else
            {
                echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages) );
            }
        
    }
    
    ## add prayer group
    
    public function add_prayer_group()
    {
        
            $arr_messages = array();
            $group_name    = trim($this->input->post("txt_group_name"));   
            # error message trapping...
            if( trim($this->input->post("txt_group_name"))=='') 
            {
                    $arr_messages['group_name'] = "* Required Field.";
            }
              
			if( trim($this->input->post("sel_denomination"))=='') 
            {
                    $arr_messages['denomination'] = "* Required Field.";
            }          
            
		   //pr($arr_messages);
		   
            if( count($arr_messages)==0 ) {
                    
			    ### check no. of groups already created
				$user_id = intval(decrypt($this->session->userdata('user_id')));
				$isLimitExceed = $this->prayer_group_model->checkGroupMaxLimit($this->max_prayer_group, $user_id, 'created');
				### check no. of groups already created
				if(!$isLimitExceed){		
						$info = array();
						
						$info['i_owner_id']        = intval(decrypt($this->session->userdata('user_id')));     
						$info['s_group_name']      =  get_formatted_string($this->input->post('txt_group_name')); 
						$info['i_denomination_id'] =  intval(decrypt($this->input->post('sel_denomination'))); 
						$info['dt_created_on']     = get_db_datetime();
					   
						$_ret = $this->prayer_group_model->insert($info);
						
						ob_start();
						$this->groups_ajax_listing($page);
						$content = ob_get_contents();
						$content_obj = json_decode($content);
						$grp_html = $content_obj->grp_html;
						ob_end_clean();
										
						echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'Prayer group created Successfully.', 'grp_html'=>$grp_html) );
				}
				else{
					
					echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'You have already created maximum permitted prayer groups.') );
					 exit;
				}
			}
            else
            {
                echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'Error!') );
            }
        
    }
	

   
    ## post a new message
	
	public function post_message($group_id){
		
			$i_user_id = intval(decrypt($this->session->userdata('user_id')));
			$message = nl2br( htmlspecialchars(trim($this->input->post('inv')), ENT_QUOTES, 'utf-8') );
			$_html='';
           	$this->load->model('church_new_model');
			 if($message!='')
		     {
                $ip = getenv("REMOTE_ADDR") ; 
				$info = array();	
				$info['i_user_id'] = $i_user_id;
				$info['i_prayer_group_id'] = $group_id;
				$info['s_post_desc']  = $message;
				$info['dt_created_on'] = get_db_datetime();
				$info['u_ip'] = $ip;
				$prayer_post_id = $this->church_new_model->insert_grp_post($info);
				
				/************************notification*******************************/
                          $grp_info  = $this->church_new_model->get_prayer_group_details($group_id);
                          $message=get_username_by_id($i_user_id).' posted on '.$grp_info[0]->s_group_name." at ".getShortDateWithTime(get_db_datetime(),2);
                          $grp_notification_arr = array();
   
                            $grp_notification_arr['i_requester_user_id'] = $i_user_id;
                            //$grp_notification_arr['i_user_id'] = $owner_id;
                            $grp_notification_arr['i_prayer_group_id'] = $group_id;
                            $grp_notification_arr['s_type'] = 'post';
                            $grp_notification_arr['msg'] = $message;
                            $grp_notification_arr['dt_created_on'] = get_db_datetime();
                             $grp_notification_arr['church_id'] = $_SESSION['logged_church_id'] ;
                            $this->church_new_model->insert_group_notifications($grp_notification_arr);

                          /********************************************************/
				echo json_encode( array('success'=>'true', 'msg'=>"Posted successfully."));
                                
			}
			 else
			 {
				echo json_encode( array('success'=>'false', 'msg'=>"Please enter some text.", 'html'=>$_html) );
			 }
         
	}
	
	public function grp_posts_ajax_pagination($group_id, $page=0)
    {
		
		$cur_page = $page + $this->post_pagination_per_page;
		$data = $this->data;
        $s_where = '';
		
		$result = $this->prayer_group_model->getAllPosts_grpID($group_id, $s_where, intval($page), $this->post_pagination_per_page);
		$total_rows = $this->prayer_group_model->getTotalPosts_grpID($group_id, $s_where);

		$data['result_arr'] = $result;
		$data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
		
		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->post_pagination_per_page)
			  $view_more = false;
         //--------- end check
		
		
		$VIEW_FILE = "logged/holy_place/prayer_group/ajax/grp_post_list_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
        echo json_encode( array('html'=>$content, 'no_of_result'=>$data['no_of_result'],'view_more'=>$view_more, 'cur_page'=>$data['current_page_1']) );
			
	}
   
   ### edit a post
    public function update_post($post_id, $group_id){
		
			$is_grp_owner = trim($this->input->post('is_grp_owner'));
			
			if($is_grp_owner == 'Y')
				$i_user_id = $this->input->post('post_owner_id');
			else
				$i_user_id = intval(decrypt($this->session->userdata('user_id')));
			 
			
			$message = nl2br( htmlspecialchars(trim($this->input->post('message')), ENT_QUOTES, 'utf-8') );
			$_html='';
           	
			 if($message!='')
		     {
				$info = array();	
				$info['i_user_id'] = $i_user_id;
				$info['s_post_desc']  = $message;
				$info['dt_updated_on'] = get_db_datetime();
				
				$prayer_post_id = $this->prayer_group_model->update_grp_post($info, $post_id);
				
				
				if($is_grp_owner == 'Y'){
					
					### send notifications
					    $message_id = parent::social_notifications_message(decrypt($this->session->userdata('user_id')), $this->input->post('post_owner_id'), 'prayer_post_modified', $group_id);
					### end  ###
				}
				
				//$group_id = $this->session->userdata('prayer_group_id');
				ob_start();
				$this->grp_posts_ajax_pagination($group_id,$page);
				$content = ob_get_contents();
				$content_obj = json_decode($content);
				$_html = $content_obj->html; 
				$view_more = $content_obj->view_more;
				$cur_page = $content_obj->cur_page;
				ob_end_clean();
				
				echo json_encode( array('success'=>'true', 'msg'=>"Post updated successfully.",'html'=>$_html,'view_more'=>$view_more,'cur_page'=>$cur_page));
			}
			 else
			 {
				echo json_encode( array('success'=>'false', 'msg'=>"Please enter some text.", 'html'=>$_html) );
			 }
         
	}

   
   ### edit a post
   
    public function search_invite_friends($prayer_group_id) 
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
        
            
            parent::_add_js_arr( array( 
										'js/jquery.multiselect.js',
										'js/jquery.multiselect.filter.js'
                                        ));
                                        
            parent::_add_church_css_arr( array('css/church.css','css/jquery.multiselect.css','css/jquery.multiselect.filter.css','css/jquery-ui-1.8.2.custom.css') );
			$this->load->model('church_new_model');
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
           
			$this->session->set_userdata('s_query_type', '');
			$this->session->set_userdata('is_post', '');
			
			//$data['prayer_detail_arr'] = $this->prayer_group_model->get_group_detail_by_id($prayer_group_id);
			//$data['prayer_member_arr'] = $this->prayer_group_model->get_members_by_grpid($prayer_group_id);
			
			$data['prayer_group_id'] = $prayer_group_id;
			$data['active_menu']='search_invite';
			
			//$data['pagination_per_page'] = $this->pagination_per_page;
            # view file...                              
            
            $VIEW = "logged/church/church_prayer_group_invite.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }
    
     
    
    public function generate_friend_search_listing_AJAX($prayer_group_id, $page=0)
    {
		$query_type = '';
		$exclude_id_csv = '';
		$s_query_type = '';
		$name = '';
		
		$logged_user_id = intval(decrypt($this->session->userdata('user_id')));
			$inv_frnds				= $this->input->post('frnd_type1'); //pr($inv_frnds,1);
			$arr_frnd	            = explode(',',$inv_frnds);
			$inv_netpal				= $this->input->post('frnd_type2');
			$arr_netpal	            = explode(',',$inv_netpal);
			$inv_pp				= $this->input->post('frnd_type3');
			$arr_pp	            = explode(',',$inv_pp);
			$complete_arr_frnd =  array();
			$contact_arr = array_merge($arr_frnd,$arr_netpal);
			$complete_arr_frnd =  array_merge($contact_arr,$arr_pp);
			$complete_arr_frnd = array_unique($complete_arr_frnd);
			$complete_arr_frnd=array_filter($complete_arr_frnd);
			#pr($complete_arr_frnd);
		
       /* $is_friend    = $this->input->post('frnd_type1');
	
        $is_netpal    = $this->input->post('frnd_type2');
        $is_ppartner  = $this->input->post('frnd_type3');*/
		//$s_name       = $this->input->post('txt_name');
		#$s_name = get_formatted_string(trim($this->input->post('txt_from')));
		#$WHERE_COND .= ($s_name=='')?'':" AND CONCAT(u.s_first_name,' ',u.s_last_name) LIKE '%".$s_name."%' ";
        //$prayer_group_id  = $this->session->userdata('prayer_group_id');
    	
		//$exclude_id_csv .= $logged_user_id;
		
		/*if($s_name != ''){
			
			$name = " AND CONCAT(u.s_first_name,' ',u.s_last_name) LIKE '%".$s_name."%' ";
		}
		
		if($is_friend == '' &&  $is_netpal == '' && $is_ppartner == '' && $s_name != ''){
			
			$is_friend    = 1;
        	$is_netpal    = 1;
        	$is_ppartner = 1;
		}
        ### new search
		if($this->input->post('hd_submit') == 'Y'){ 
			if($is_friend == 1){
				
				if( $query_type == ''){
				 $query_type .= "(SELECT 
										
										u.id post_owner_user_id, 
										u.s_email,
										u.i_country_id,
										u.s_last_name,
										u.s_first_name ,
										u.s_profile_photo,
										'Friend'  as 'relationship'
										FROM {$this->db->USER_CONTACTS} c ,{$this->db->USERS} u 
										WHERE u.i_status=1 AND c.s_status='accepted'
										AND ((c.i_requester_id = ".$logged_user_id." AND u.id=c.i_accepter_id) 
											OR (c.i_accepter_id=".$logged_user_id." AND u.id=c.i_requester_id))
										AND u.id NOT IN (".$exclude_id_csv.")
										{$name}
										GROUP BY u.id
										ORDER BY u.s_first_name ASC) ";
				}
				else{
					
					$query_type .= " UNION
									  (SELECT 
										
										u.id post_owner_user_id, 
										u.s_email,
										u.i_country_id,
										u.s_last_name,
										u.s_first_name ,
										u.s_profile_photo,
										'Friend'  as  'relationship'
										FROM {$this->db->USER_CONTACTS} c ,{$this->db->USERS} u 
										WHERE u.i_status=1 AND c.s_status='accepted'
										AND ((c.i_requester_id = ".$logged_user_id." AND u.id=c.i_accepter_id) 
											OR (c.i_accepter_id=".$logged_user_id." AND u.id=c.i_requester_id))
										AND u.id NOT IN (".$exclude_id_csv.")
										{$name}
										GROUP BY u.id
										ORDER BY u.s_first_name ASC) ";
				}
				
				$contacts = $this->contacts_model->fetch_multi_online_friends($WHERE, null, null,$ORDER_BY);
				
				$exclude_id_arr = $this->contacts_model->get_friendsId_by_user_id($logged_user_id);
				
				if(count($exclude_id_arr)){
					$exclude_id_csv .= ', ';
					$exclude_id_csv .= implode(', ',$exclude_id_arr);
				}
				$this->session->set_userdata('is_friend', '1');
			}
			
			
			
			if($is_ppartner == 1){
				 
				 if( $query_type == ''){
					 $query_type .= "(SELECT   
										  
										   u.id post_owner_user_id, 
										   u.s_email,
										   u.i_country_id,
										   u.s_last_name,
										   u.s_first_name ,
										   u.s_profile_photo,
										   'Prayer Partner' as 'relationship' 
										   FROM {$this->db->USER_PRAYER_PARTNER} p ,{$this->db->USERS} u
										   WHERE u.i_status=1 AND p.s_status='accepted'
										   AND ((p.i_requester_id = ".$logged_user_id." AND u.id=p.i_accepter_id) 
												OR (p.i_accepter_id=".$logged_user_id." AND u.id=p.i_requester_id))
										   AND u.id NOT IN (".$exclude_id_csv.")
										   {$name}
										   GROUP BY u.id
										   ORDER BY u.id ASC)";
				 }
				 else{
					 
					 $query_type .= "UNION
										(SELECT   
										   
										   u.id post_owner_user_id, 
										   u.s_email,
										   u.i_country_id,
										   u.s_last_name,
										   u.s_first_name ,
										   u.s_profile_photo,
										  'Prayer Partner' as 'relationship' 
										   FROM {$this->db->USER_PRAYER_PARTNER} p ,{$this->db->USERS} u
										   WHERE u.i_status=1 AND p.s_status='accepted'
										   AND ((p.i_requester_id = ".$logged_user_id." AND u.id=p.i_accepter_id) 
												OR (p.i_accepter_id=".$logged_user_id." AND u.id=p.i_requester_id))
										   AND u.id NOT IN (".$exclude_id_csv.")
										   {$name}
										   GROUP BY u.id
										   ORDER BY u.id ASC)";
				 }
				
				$exclude_id_PP_arr = $this->my_prayer_partner_model->get_prayerPartnerId_by_user_id($logged_user_id);
				if(count($exclude_id_PP_arr)){
						$exclude_id_csv .= ', ';
						$exclude_id_csv .= implode(', ',$exclude_id_PP_arr);
				}
				$this->session->set_userdata('is_ppartner', '1');
			}
			
			if($is_netpal == 1){
				 
				 if( $query_type == ''){
					
					 $query_type .= "(SELECT  
									   
									   u.id post_owner_user_id, 
									   u.i_country_id,
									   u.s_email,
									   u.s_last_name,
									   u.s_first_name ,
									   u.s_profile_photo,
									   'Netpal' as 'relationship' 
									   FROM {$this->db->NETPAL} n ,{$this->db->USERS} u 
									   WHERE u.i_status=1 AND n.s_status='accepted'
									   AND ((n.i_requester_id = ".$logged_user_id." AND u.id=n.i_accepter_id) 
											OR (n.i_accepter_id=".$logged_user_id." AND u.id=n.i_requester_id))
									   AND u.id NOT IN (".$exclude_id_csv.")
									   {$name}
									   GROUP BY u.id
									   ORDER BY u.id ASC )";
				 }
				 else{
					 
					 $query_type .= "UNION
									 (SELECT  
									   
									   u.id post_owner_user_id, 
									   u.s_email,
									   u.i_country_id,
									   u.s_last_name,
									   u.s_first_name ,
									   u.s_profile_photo,
									   'Netpal' as 'relationship'  
									   FROM {$this->db->NETPAL} n ,{$this->db->USERS} u 
									   WHERE u.i_status=1 AND n.s_status='accepted'
									   AND ((n.i_requester_id = ".$logged_user_id." AND u.id=n.i_accepter_id) 
											OR (n.i_accepter_id=".$logged_user_id." AND u.id=n.i_requester_id))
									   AND u.id NOT IN (".$exclude_id_csv.")
									   {$name}
									   GROUP BY u.id
									   ORDER BY u.id ASC )";
				 }
				 $this->session->set_userdata('is_netpal', '1');
			}
		
			$this->session->set_userdata('s_query_type', $query_type);	
			$this->session->set_userdata('is_post', '1');
		}
		
		$s_query_type = $this->session->userdata('s_query_type');
        $data['is_post'] = $this->session->userdata('is_post');
		$this->session->set_userdata('is_post','');
       */ 
	   foreach($complete_arr_frnd as $val)
	   {
	  		$s_query= "SELECT 
										
										u.id post_owner_user_id, 
										u.s_email,
										u.i_country_id,
										u.s_last_name,
										u.s_first_name ,
										u.s_profile_photo,
										 ,{$this->db->USERS} u 
										WHERE u.i_status=1 AND u.id=".$val."
										GROUP BY u.id
										ORDER BY u.s_first_name ASC";
	   }
       if($this->session->userdata('is_friend') == 1 || $this->session->userdata('is_ppartner') == 1 || $this->session->userdata('is_netpal') == 1){
			
			$cur_page = $page + $this->pagination_per_page;
			$data['result_arr'] =  $this->prayer_group_model->get_frnd_list($s_query_type,$page,$this->pagination_per_page,$prayer_group_id );
			
			$total_rows  =   $this->prayer_group_model->gettotal_frnd_list($s_query_type);
			//pr($data['result_arr']);
			$data['pagination_per_page'] = $this->pagination_per_page;
			
			$data['no_of_result'] = $total_rows; 
			$data['current_page_1'] = $cur_page;
			$data['prayer_group_id'] = $prayer_group_id;
        }
         //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
           if($rest_counter<=$this->pagination_per_page)
              $view_more = false;
         //--------- end check
        # loading the view-part...
        $AJAX_VIEW_FILE = 'logged/holy_place/prayer_group/ajax/search_invite_ajax_list.phtml';
        
        if( $total_rows>0 ) {
            $listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true); 
        }
        else {
            $listingContent = '';
        }

        echo json_encode( array('html'=>$listingContent, 
                                'no_of_result'=>$data['no_of_result'],
                                'total'=>$total_rows,
                                'view_more'=>$view_more ,
                                'cur_page'=>$data['current_page_1'],
                                'is_post'=>$data['is_post']) );
    }
	
	
	public function invite_to_group() 
    {
        try
        {
            $user_id = intval($this->input->post('user_id'));   //acceptor ID
			$group_id = $this->input->post('grp_id');
       		
			## check if already sent
			$is_exists = $this->prayer_group_model->request_already_sent($user_id, $group_id);
			if($is_exists){
				$_ret =1;  
			}
			else{
				
				$info = array();
				
				$info['i_prayer_group_id'] = $group_id;
				$info['i_user_id'] =  $user_id;
				$info['s_status'] = 'pending';
				$info['dt_created_on'] = get_db_datetime();
				
				$_ret = $this->prayer_group_model->insert_group_member($info);
			}
			## 
			$grp_info  = $this->prayer_group_model->get_group_detail_by_id($group_id);
			
			## check if opted for this notification or not ##
			  $notificaion_opt = $this->user_alert_model->check_option_user_id($user_id);	
			## insert noifications ####
			if($notificaion_opt['e_prayer_grp_invitation'] == 'Y'){
				$notification_arr['i_requester_id'] = decrypt($this->session->userdata('user_id'));
				$notification_arr['i_accepter_id'] =  $user_id;
				$notification_arr['s_type'] = 'prayer_group_invitation';
				$notification_arr['dt_created_on'] = get_db_datetime();
				$ret = $this->user_notifications_model->insert($notification_arr);	
				
			}
			$message_id = parent::social_notifications_message(decrypt($this->session->userdata('user_id')), $user_id, 'prayer_group_invitation', $group_id);
			### end  ###
			
			### adding to prayer group notifiaction table:
                        
			$grp_notification_arr = array();
			
			$grp_notification_arr['i_requester_user_id'] = decrypt($this->session->userdata('user_id'));
			$grp_notification_arr['i_user_id'] = $user_id;
			$grp_notification_arr['i_prayer_group_id'] = $group_id;
			$grp_notification_arr['s_type'] = 'invited';
			$grp_notification_arr['i_refferd_msg_id'] = $message_id;
			$grp_notification_arr['dt_created_on'] = get_db_datetime();
			$this->prayer_group_model->insert_group_notifications($grp_notification_arr);
			
			
			
			
			$html = '<a href="javascript:void(0);" onclick="invite_prayer_group('.$user_id.')">Re-send Invitation</a>';
			
			
            if($_ret)
            {                     
                echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'Prayer group invitation sent successfully.','html'=>$html,'uid'=>$user_id) );
            }
            else
            {
                echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'Error!') );
            }
    
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }   
    
	
	public function accept_req($grp_id, $uid,$id,$i_user_id)
	{
           // die();
           // echo $i_user_id;
            //die();
		$user_id  = decrypt($this->session->userdata('user_id'));
		$isLimitExceed = $this->church_new_model->checkGroupMaxLimit($this->max_prayer_group, $user_id, 'created');
		
		if(!$isLimitExceed){
				//die('ok1');
			  $where = array('i_prayer_group_id' => $grp_id,
							  'i_user_id' => $i_user_id
							  );
							  
			  $arr['s_status']	= 'accepted';
			  $arr['i_request']	= 1;
			  $arr['dt_joined_on']	= get_db_datetime();
			  $msgarr	= array();
			  $sender_id = intval(decrypt($this->session->userdata('user_id')));
	  
			  $receiver_id = $uid;
			  $msgarr	= array('s_type'=>'prayer_group_invitation', 'i_referred_media_id'=>$grp_id,'i_receiver_id'=>$sender_id);
			  
			  
				  ## check if opted for this notification or not ##
					/*$notificaion_opt = $this->user_alert_model->check_option_user_id(decrypt($this->session->userdata('user_id')));	
				  ## insert noifications ####
				  if($notificaion_opt['e_prayer_grp_request_accepted'] == 'Y'){
					  $notification_arr['i_requester_id'] = decrypt($this->session->userdata('user_id'));
					  $notification_arr['i_accepter_id'] = $uid;jjjj
					  $notification_arr['s_type'] = 'prayer_group_accept_join_request';
					  $notification_arr['dt_created_on'] = get_db_datetime();
					  $ret = $this->user_notifications_model->insert($notification_arr);	
					  
				  }*/
			  
			  $res = $this->church_new_model->accept_invitation($where,$arr,$msgarr);
                          
                          /************************notification*******************************/
                          $grp_info  = $this->church_new_model->get_prayer_group_details($grp_id);
                          $message=get_username_by_id($i_user_id).' joined '.$grp_info[0]->s_group_name." on ".getShortDateWithTime(get_db_datetime(),2);
                          $grp_notification_arr = array();
   
                            $grp_notification_arr['i_requester_user_id'] = $i_user_id;
                            //$grp_notification_arr['i_user_id'] = $owner_id;
                            $grp_notification_arr['i_prayer_group_id'] = $grp_id;
                            $grp_notification_arr['s_type'] = 'join';
                            $grp_notification_arr['msg'] = $message;
                            $grp_notification_arr['dt_created_on'] = get_db_datetime();
                             $grp_notification_arr['church_id'] = $_SESSION['logged_church_id'];

                            $this->church_new_model->insert_group_notifications($grp_notification_arr);

                          /********************************************************/
			  
			  //parent::social_notifications_message($sender_id, $uid, 'prayer_group_accept_join_request', $grp_id) ;
			  
			 
			  ### adding to prayer group notifiaction table:
			  /*$grp_notification_arr = array();
			  
			  $grp_notification_arr['i_user_id'] = decrypt($this->session->userdata('user_id'));
			  $grp_notification_arr['i_prayer_group_id'] = $grp_id;
			  $grp_notification_arr['s_type'] = 'join';
			  $grp_notification_arr['dt_created_on'] = get_db_datetime();
			  
			  $this->prayer_group_model->insert_group_notifications($grp_notification_arr);?*/
			  ### adding to prayer group notifiaction table: 
			  
			  echo json_encode(array('msg'=>'The Prayer group joining request has been accepted successfully.','uid'=>$sender_id));
			  exit;
		}
		else{
                    die('ok2');
			
			$where = array('i_prayer_group_id' => $grp_id,
							  'i_user_id' => decrypt($this->session->userdata('user_id'))
							  );
							  
			$arr['s_status']	= 'accepted';
			$arr['i_request']	= 1;
			$arr['dt_joined_on']	= get_db_datetime();
			$msgarr	= array();
			$sender_id = intval(decrypt($this->session->userdata('user_id')));
	
			$receiver_id = $uid;
			$msgarr	= array('s_type'=>'prayer_group_invitation', 'i_referred_media_id'=>$grp_id,'i_receiver_id'=>$sender_id);
			
			$this->church_new_model->accept_invitation($where,$arr,$msgarr);
			echo json_encode(array('msg'=>'You have already joined maximum permitted numbers of prayer groups.','uid'=>$sender_id));
			exit;
		}
	}
	
	public function decline_req($grp_id, $uid, $id,$i_user_id)
	{

		$where = array('i_prayer_group_id' => $grp_id,
    					'i_user_id' => $i_user_id,
                                          'id'=> $id
						);
		$arr['s_status']	= 'rejected';
		$arr['dt_joined_on']	= get_db_datetime();				
		$msg_arr	= array();
		$msgarr	= array('id'=>$id);
		//$res = $this->church_new_model->decline_invitation($where,$msg_arr);
		$res = $this->church_new_model->decline_invitation($where,$arr,$msg_arr);
		$sender_id = intval(decrypt($this->session->userdata('user_id')));
		/******************************deline**************************************/
                 $grp_info  = $this->church_new_model->get_prayer_group_details($grp_id);
                          $message='Join request declined for '.$grp_info[0]->s_group_name." on ".getShortDateWithTime(get_db_datetime(),2);
                          $grp_notification_arr = array();
   
                            $grp_notification_arr['i_requester_user_id'] = $i_user_id;
                            //$grp_notification_arr['i_user_id'] = $owner_id;
                            $grp_notification_arr['i_prayer_group_id'] = $grp_id;
                            $grp_notification_arr['s_type'] = 'joining_req';
                            $grp_notification_arr['msg'] = $message;
                            $grp_notification_arr['dt_created_on'] = get_db_datetime();
                             $grp_notification_arr['church_id'] = $_SESSION['logged_church_id'];

                            $this->church_new_model->insert_group_notifications($grp_notification_arr);
                /********************************************************************/
		
		    ## check if opted for this notification or not ##
			 /*$notificaion_opt = $this->user_alert_model->check_option_user_id(decrypt($this->session->userdata('user_id')));	
			## insert noifications ####
			if($notificaion_opt['e_prayer_grp_request_declined'] == 'Y'){
				$notification_arr['i_requester_id'] = decrypt($this->session->userdata('user_id'));
				$notification_arr['i_accepter_id'] = $uid;
				$notification_arr['s_type'] = 'prayer_group_deny_join_request';
				$notification_arr['dt_created_on'] = get_db_datetime();
				$ret = $this->user_notifications_model->insert($notification_arr);	
			}
		
		 parent::social_notifications_message($sender_id, $uid, 'prayer_group_deny_join_request', $grp_id) ;*/
		/*$email_opt = $this->user_alert_model->check_option_email_user_id($uid);
						if($email_opt['e_prayer_grp_request_declined'] == 'Y' ){
						
						$mail_arr['i_requester_id'] =get_username_by_id(decrypt($this->session->userdata('user_id')));
						$mail_arr['i_accepter_id'] =  get_username_by_id($uid);
						$mail_arr['s_type'] = 'e_prayer_grp_request_declined';
						$mail_arr['group_name']=get_prayer_name_group_by_id($grp_id);
						$mail_id=get_useremail_by_id($uid);
						 $this->load->library('email');
						 $this->load->helper('html');
						 //echo $mail_id;exit;
					$email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
					  $body=$this->load->view('logged/my-mail-template.phtml',$mail_arr,TRUE);
						$this->email->initialize($email_setting);
						$this->email->from('admin@cogtime.com', 'Team Cogtime');
					$this->email->to("$mail_id");
						//$this->email->bcc("$mailids");
				//$this->email->cc('arif.zisu@gmail.com');
					//$this->email->bcc('trisha.paul@hotmail.com');
					$this->email->subject($mail_arr["i_requester_id"].' has declined your prayer group joining request.');
					$this->email->message("$body");

					$this->email->send();
					}*/
		 echo json_encode(array('msg'=>'The prayer group joining request has been declined successfully.','uid'=>$sender_id));
		 exit;
	}
	
	
	### DELETE PRAYER GROUP 
    public function delete_post($id, $grp_id)
    {
		$i_ret=$this->prayer_group_model->delete_post_by_id($id);
		$url = get_group_url($grp_id);
		$re_page = $url;
					header("location:".$re_page);
					exit;
		
	} 
	
	
	
	public function create_prayer_room($prayer_group_id) 
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
        
            
//            parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
//                                        'js/switch.js','js/animate-collapse.js',
//                                        'js/lightbox.js',
//										'js/jquery-ui-1.8.2.custom.min.js',
//                                        'js/stepcarousel.js',
//										'js/jquery/ui/jquery.ui.core.js',
//										'js/jquery.ui.datepicker.js',
//									    'js/jquery-ui-timepicker-addon.js',
//										'js/tab.js',
//										'js/frontend/logged/holy_place/prayer_group.js'
//                                        ));
//
//            parent::_add_css_arr( array('css/jquery-ui-1.8.13.custom.css','css/jquery-ui-1.8.2.custom.css') );
          
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			
			$orderby = 'dt_created_on DESC';
			$data['prayer_grp_info'] = $this->prayer_group_model->get_my_groups($i_user_id,'','','',$orderby); 
			$data['prayer_member_arr'] = $this->prayer_group_model->get_members_by_grpid($prayer_group_id);
			$data['prayer_detail_arr'] = $this->prayer_group_model->get_group_detail_by_id($prayer_group_id);
			$data['prayer_group_id'] = $prayer_group_id;
			
				
			# view file...
			
            $VIEW = "logged/holy_place/prayer_group/create-prayer-room.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }
	
	public function add_private_chat_room_invite() 
	{

		try
        {
			
	        $arr_messages = array();
					
					# error message trapping...
					if( trim($this->input->post('recipients'))=='') 
					{
							$arr_messages['send_recepients'] = "* You must select a recipient";
					}
										
					if( trim($this->input->post('txt_room_name'))=='') 
					{
							$arr_messages['room_name'] = "* Required Field.";
					}
					
//					if( trim($this->input->post('date_end1'))=='') 
//					{
//							$arr_messages['date_end1'] = "* Required Field.";
//					}
					
					if( trim($this->input->post('date_to1'))=='') 
					{
							$arr_messages['date_to1'] = "* Required Field.";
					}
					$start_date=trim($this->input->post('date_to1'));
					//$end_date=trim($this->input->post('date_end1'));
//					if($start_date > $end_date)
//					{
//					   $arr_messages['date_end1'] = "Please enter a valid end date.";
//					}

					if( trim($this->input->post('todo_strt_frm'))=='-1') 
					{
							$arr_messages['todo_strt_frm'] = "* Required Field.";
					}

//					if( trim($this->input->post('todo_end_frm'))=='-1') 
//					{
//							$arr_messages['todo_end_frm'] = "* Required Field.";
//					}
                                        

					### fixing 30 minutes slot
					 $start = trim($this->input->post('todo_strt_frm'));
                                       
                                        $parts = explode(':', $start);
                                                                  $seconds = ($parts[0] * 60 * 60) + ($parts[1] * 60) + $parts[2];
                                                                  $after_thirty_min = $seconds+1800; // after 30
                                                                 // $before_thirty_min = $seconds-900;//befor 15
                                                                    $time_str_after_30 = gmdate("H:i:s", $after_thirty_min);
                                                                   
                                                                  //echo $time_str_before_15 = gmdate("H:i:s", $before_thirty_min);
                                                                 
                                                                   $user_id = intval(decrypt($this->session->userdata('user_id')));
                                                                    $query = $this->db->get_where('cg_users', array('id' => $user_id));
                                                                    foreach ($query->result() as $row)
                                                                       {
                                                                           $time_zone = $row->s_time;
                                                                       }
                                                                       $nz_time = new DateTime(null, new DateTimezone($time_zone));
                                                                      $current_user_time = $nz_time->format('H:i:s');
                                                                       $current_user_time_stamp = strtotime($current_user_time);
                                                                       if(!($current_user_time_stamp > strtotime($start) && $current_user_time_stamp < strtotime($time_str_after_30))){
                                                                           $arr_messages['todo_strt_frm']  = 'Invalid time';
                                                                           
                                                                       }
                                                                    //  die('no ok');
					//$stop = strtotime(trim($this->input->post('todo_end_frm')));
//					$diff = ($stop - $start)/60;
//                                        //echo $diff;
//                                        //die('ok');
//
//					if($diff  != 30 ) 
//					{
//						$arr_messages['todo_end_frm'] = "* Maximum or Minimum 30 minutes period is allowed.";
//					}
                                         /**********check current time***************************/
//                                        $user_id = intval(decrypt($this->session->userdata('user_id')));
//                                 $query = $this->db->get_where('cg_users', array('id' => $user_id));
//                                 foreach ($query->result() as $row)
//                                    {
//                                        $time_zone = $row->s_time;
//                                    }
//                                    $nz_time = new DateTime(null, new DateTimezone($time_zone));
//                                   $current_user_time = $nz_time->format('H:i:s');
//                                    $current_user_time_stamp = strtotime($current_user_time);
//                                  
//                                    if( strtotime(trim($this->input->post('todo_strt_frm'))) <= $current_user_time_stamp_pls_30 && strtotime(trim($this->input->post('todo_strt_frm'))) >= $current_user_time_stamp_mns_30   ) {
//                                        $arr_messages['todo_strt_frm'] = "Select nearest time to the current time";
//                                    }
                                        
                                        /**************************************/
					
				   //pr($arr_messages);
					if( count($arr_messages)==0 ) 
						{
		 					
								$this->load->model('users_model');
								
								//$end_time = trim($this->input->post('date_end1'));
								#$end_time_arr = explode(' ',$end_time);
								
								#pr($end_time_arr);
								//$end_time_str = trim($this->input->post('todo_end_frm')); #$end_time_arr[1].':00';
								
								$strt_time = trim($this->input->post('date_to1'));
								#$strt_time_arr = explode(' ',$strt_time);
								
								#pr($strt_time_arr);
								$strt_time_str = trim($this->input->post('todo_strt_frm'));
								
								$chat_room_name =  trim($this->input->post('txt_room_name'));
								
								 $parts = explode(':', $strt_time_str);
                                                                  $seconds = ($parts[0] * 60 * 60) + ($parts[1] * 60) + $parts[2];
                                                                  $after_thirty_min = $seconds+1800;
                                                                   $end_time_str = gmdate("H:i:s", $after_thirty_min);
                                                                   
								$day_arr = GetDays($strt_time);  
								//GetDays($strt_tim, $end_time);
								#pr($day_arr);
								$recipients_ids =  $this->input->post('recipients');
								
								## ADD Private Room API
								//$sequence = $this->chat_rooms_model->getMaxSequence(); 
					   
								$host = "127.0.0.1";
								$port = 51127;
								
//								$time_html = '';
//								if(count($day_arr)){
//									foreach($day_arr as $k=> $val){
//										$time_html .= '<Time o="special" e="'.$val.' '.$end_time_str.'" s="'.$val.' '.$strt_time_str.'"></Time>';
//									}
//								}
								
								#<Time o="special" s="2014-08-27 11:45:00" e="2014-08-27 19:00:00"  ></Time>
								
								
								$apiCommand = '<?xml version="1.0" encoding="UTF-8"?><Command group="default" api_pwd="3874-3459-9293-2194" type="add_room" name="'.$chat_room_name.'" passallmessage="false" member = "true" >
								<roomOpen>
								
								</roomOpen>
								</Command>';
								
								$result = "";
								$resultDoc = "";
								$fp = @fsockopen($host, $port, $errno, $errstr, 2);
								if(!$fp)
								{
									echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'Failed to excute api command,maybe host chat server is not started!') );
								}
								else
								{
									fputs($fp,$apiCommand."\0");
									
									while (!feof($fp)) 
									{
											$resultDoc .= fgets($fp, 1024); 
											$resultDoc = rtrim($resultDoc);
									}
									$parser = xml_parser_create("UTF-8");
									xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
									xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
									if (!xml_parse_into_struct($parser, $resultDoc, $values, $tags))
									{
										printf("XML error: %s at line %d while parsing entity n",
											xml_error_string(xml_get_error_code($parser)),
											xml_get_current_line_number($parser));
										//echo "xml parse error";
										echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'xml parse error!') );
									}
								else
									{
										//print_r($values); exit;
										xml_parser_free($parser);
										fclose($fp);
										$new_chat_room_id = $values[0]['attributes']['result'];
										
										 
									}
								}
								
								foreach( explode(',',$recipients_ids) as $recipient_id ) 
								 {
									$recipient_id = decrypt($recipient_id);
									$notification_arr = array();
									## check if opted for this notification or not ##
									$notificaion_opt = $this->user_alert_model->check_option_user_id($recipient_id);	
									//pr($notificaion_opt);
									
									## insert noifications ####
									if($notificaion_opt['e_prayer_grp_chat_invitation'] == 'Y'){
										$notification_arr['i_requester_id'] = decrypt($this->session->userdata('user_id'));
										$notification_arr['i_accepter_id']  = $recipient_id;
										$notification_arr['s_type'] = 'prayer_group_chat_room_invitation';
										$notification_arr['dt_created_on'] = get_db_datetime();
										$ret = $this->user_notifications_model->insert($notification_arr);	
									}
									
									$email_opt = $this->user_alert_model->check_option_email_user_id($recipient_id);
						if($email_opt['e_prayer_grp_chat_invitation'] == 'Y' ){
						
						$mail_arr['i_requester_id'] =get_username_by_id(decrypt($this->session->userdata('user_id')));
						$mail_arr['i_accepter_id'] =  get_username_by_id($recipient_id);
						$mail_arr['s_type'] = 'e_prayer_grp_chat_invitation';
						$mail_arr['group_name']=trim($this->input->post('txt_room_name'));
						$mail_id=get_useremail_by_id($recipient_id);
						 $this->load->library('email');
						 $this->load->helper('html');
						 //echo $mail_id;exit;
					$email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
					  $body=$this->load->view('logged/my-mail-template.phtml',$mail_arr,TRUE);
						$this->email->initialize($email_setting);
						$this->email->from('admin@cogtime.com', 'Team Cogtime');
					$this->email->to("$mail_id");
						//$this->email->bcc("$mailids");
				//$this->email->cc('arif.zisu@gmail.com');
					//$this->email->bcc('trisha.paul@hotmail.com');
					$this->email->subject($mail_arr["i_requester_id"].' has invited you to his/her Prayer Room.');
					$this->email->message("$body");

					$this->email->send();
					}
									### end  ###
									
									$sender_id = decrypt($this->session->userdata('user_id'));
									
									
									
									
									## adding records to CHAT_ROOM_INVITATION table.
									
									$chat_invitation = array();
									$chat_invitation['i_group_id'] = trim($this->input->post('group_id'));
									$chat_invitation['i_chat_room_id'] = $new_chat_room_id;
									$chat_invitation['i_user_id'] = $recipient_id;
									$chat_invitation['dt_start_time'] = $strt_time;
									$chat_invitation['dt_end_time'] = $strt_time;
									$chat_invitation['s_status'] = 'pending';
									$chat_invitation['is_expired'] = 0;
									$chat_invitation['dt_created_on'] = get_db_datetime();
                                                                        /***************today 15-10-2014***************************************/
									$chat_invitation['start_time'] = $strt_time_str;
                                                                        $chat_invitation['end_time'] = $end_time_str;
                                                                        $chat_invitation['s_type'] = 'Prayer chat room';
                                                                        
                                                                        /*****************************************************/
                                                                        
                                                                        /***********add all room table*************************************/
                                                                        $all_room = array();
                                                                        $all_room['i_chat_room_id'] = $new_chat_room_id;
                                                                        $all_room['user_id'] = intval(decrypt($this->session->userdata('user_id')));
                                                                        $all_room['dt_start_time'] = $strt_time.' '.$strt_time_str;
                                                                        $all_room['dt_end_time'] =  $strt_time.' '.$end_time_str;
                                                                        $all_room['s_type'] = 3;
                                                                        $data = $all_room;
                                                                         $this->db->insert('cg_all_chat_room', $data);
                                                                        /***********add all room table*************************************/
                                                                        
									$ret_id = $this->chat_rooms_model->InsertChatInvitation($chat_invitation);
									
									$message_id = parent::social_notifications_message($sender_id,$recipient_id, 'prayer_group_chat_room_invitation', $new_chat_room_id);
									
									
									## add to organizer 
	   								$gp_info =$this->prayer_group_model->get_group_detail_by_id(trim($this->input->post('group_id')));
	   								$group_name = $gp_info['s_group_name'];
									
									
									  $org_info = array();
									  $org_info['i_user_id'] = $recipient_id;
									  $org_info['s_title'] = 'Prayer Meeting in '.$group_name; 
									  $org_info['s_description'] = 'Prayer Meeting in '.$group_name;  
									  $org_info["d_date"] 		 = trim(getShortDate($strt_time,8));

									  $org_info["t_start_time"] = trim(getShortDateWithTime($strt_time,1));
									  
									 
									  $org_info["t_end_time"] = '';
									  $org_info["t_remind_time"] = '';
									  
									  										  
									  $date_a = new DateTime($org_info["d_date"].' '.$org_info["t_start_time"]);
									  $date_b = new DateTime($org_info["d_date"].' '.'00:15:00');

									  $interval = date_diff($date_a,$date_b); 
									   
									  $org_info["t_remind_me_back"] = $interval->format('%h:%i:%s');;
									  $org_info['s_type_to_do'] = 'personal_event';
									  
									  $org_info['dt_created_on'] = get_db_datetime();
									  $_ret = $this->organizer_todo_model->insert($org_info);
									
									
								}
								
								### adding chat room owner organizer info
								
								      $org_info = array();
									  $org_info['i_user_id'] = decrypt($this->session->userdata('user_id'));
									  $org_info['s_title'] = 'Prayer Meeting in '.$group_name; 
									  $org_info['s_description'] = 'Prayer Meeting in '.$group_name; 
									  $org_info["d_date"] 		 = getShortDate($strt_time,8);

									  $org_info["t_start_time"] = getShortDateWithTime($strt_time,1);
									 
									  $date_a = new DateTime($org_info["d_date"].' '.$org_info["t_start_time"]);
									  $date_b = new DateTime($org_info["d_date"].' '.'00:15:00');

									  $interval = date_diff($date_a,$date_b);
									 
									  $org_info["t_end_time"] = '';
									  $org_info["t_remind_time"] = '';
									 
									  $org_info["t_remind_me_back"] =  $interval->format('%h:%i:%s');;
									  $org_info['s_type_to_do'] = 'personal_event';
									  
									  $org_info['dt_created_on'] = get_db_datetime();
									  $_ret = $this->organizer_todo_model->insert($org_info);
								
									  $redirect_loc = get_group_url(trim($this->input->post('group_id')),$group_name);
																  
							echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'New Chat room has been added successfully.','html'=>$html, 'redirect_loc'=>$redirect_loc) );
						}
						else
						{
							echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'Error!') );
						}
			
		}
        catch(Exception $err_obj)
        {
          
        }   
	}
	
	
	public function join_chat_room($room_id, $uid, $id)
	{

		$where = array('i_chat_room_id' => $room_id,
    					'i_user_id' => decrypt($this->session->userdata('user_id'))
						);
						
		$arr['s_status']	= 'accepted';
		$arr['dt_created_on']	= get_db_datetime();
		$msgarr	= array();
		$sender_id = intval(decrypt($this->session->userdata('user_id')));

		$receiver_id = $uid;
		$msgarr	= array('s_type'=>'prayer_group_chat_room_invitation', 'i_referred_media_id'=>$room_id,'i_receiver_id'=>$receiver_id);
		
		$res = $this->chat_rooms_model->join_prayer_meeting($where,$arr,$msgarr);
		echo json_encode(array('msg'=>'Prayer meeting request has been accepted successfully.','uid'=>$sender_id));
		exit;
	}
	
	
	
	### search and join prayer group
	
	public function search_prayer_group($c_id) 
    {
        try
        {
                  
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
            $this->data["MAIN_MENU_SELECTED"] = 6;
			$data["active_menu"] = 'search_join';
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
			
            parent::_add_church_css_arr( array('css/church.css') );
			
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			$_SESSION['logged_church_id'] = $c_id;
			
			
			$data['pagination_per_page'] = $this->pagination_per_page;
			$this->session->set_userdata('search_condition','');
            # view file...
            ob_start();
            $content = $this->generate_prayer_group_listing_AJAX();
            $content = ob_get_contents();
            $content_obj = json_decode($content);
            $data['listingContent'] = $content_obj->html; 
            $data['no_of_result'] =  $content_obj->no_of_result;
            ob_end_clean();                                  
            
            # view file...
            
              # view file...
            ob_start();
            $content = $this->get_all_prayer_group_AJAX();
            $content = ob_get_contents();
            $content_obj = json_decode($content);
            $data['listingContent'] = $content_obj->html; 
            $data['no_of_result'] =  $content_obj->no_of_result;
            ob_end_clean();                                  
            
            # view file...
            
            
            
            $VIEW = "logged/church/search-and-join-church-prayer-group.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }
    
     
    
    public function generate_prayer_group_listing_AJAX($page=0)
    {
		$logged_user_id = intval(decrypt($this->session->userdata('user_id')));
		
		$s_where ='';
		$WHERE_COND = '';
		## seacrh conditions : filter ############
		 	
			 if(isset($_POST['hd_submit']) && $_POST['hd_submit'] == 'Y' ) :
           
				
				$s_name = get_formatted_string(trim($this->input->post('txt_name')));
				$WHERE_COND .= ($s_name=='')?'':" AND s_group_name LIKE '%".$s_name."%' ";
				
				$sel_denomination = intval(decrypt(trim($this->input->post('sel_denomination'))));
				$WHERE_COND .= ($sel_denomination=='-1' || $sel_denomination=='0')?'':" AND ( i_denomination_id  =  '".$sel_denomination."')";
				
				$this->session->set_userdata('search_condition',$WHERE_COND);
			
            endif;  
		   	
			 $s_where = $this->session->userdata('search_condition'); 
			 //pr($_SESSION);
			$data['church_arr'] =$this->church_new_model->get_church_info($_SESSION['logged_church_id']);
			$data['church_admin'] = $this->church_new_model->get_church_admin_data($_SESSION['logged_church_id']);

		$data['church_list'] =  $this->church_new_model->get_prayer_group_search_result_by_church($_SESSION['logged_church_id'],$logged_user_id,$order='dt_created_on',$direction='DESC',$s_where);
		// pr($data['church_list']);
        # loadi ng the view-part...
        $AJAX_VIEW_FILE = 'logged/church/search_prayer_group_listing_ajax.phtml';
        
        //if( $total_rows>0 ) {
            $listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true); 
       // }
       // else {
        //    $listingContent = '';
       // }

        echo json_encode( array('html'=>$listingContent));
    }
	
	/********************************all prayer grp******************************/
    	public function get_all_prayer_group_AJAX($page=0)
    {
		 $wh	= " AND i_owner_id ='". $_SESSION['logged_church_id']."' ";
		//$wh1	= " AND inv.i_invited_id='".$this->i_profile_id."'";
		$data['grpdata']	= $this->church_new_model->show_group_all($wh,$page,$this->pagination_per_page,'');
		//pr($data['grpdata'],1);
		//$data['ringdata']	= check_friend_netpal_status($data['ringdata']);
		
		$data['pagination_per_page'] = $this->pagination_per_page;
		//pr($result);
		$resultCount = count($data['grpdata']);
		 $total_rows = $this->church_new_model->gettotal_group($wh);
                
                
		$cur_page = $page + $this->pagination_per_page;
        
        
		// getting auction-category listing...
		$data['no_of_grp'] = $total_rows;
		$data['no_of_result'] = $total_rows;
		$data['current_page_1'] = $cur_page;

		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->pagination_per_page)
			  $view_more = false;
         //--------- end check
        # loading the view-part...
        $AJAX_VIEW_FILE = 'logged/church/all_prayer_group_listing_ajax.phtml';
        
        
   //pr($result);
		
		if( $total_rows>0 ) {
        	$listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);
		}
		else {
			$listingContent = '';
		}
		//echo 1; exit;
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$listingContent, 
								'current_page'=>$cur_page, 
								'no_of_result'=>$data['no_of_result'],
								'total'=>$total_rows,
								'view_more'=>$view_more ,
								'cur_page'=>$data['current_page_1']) );
    }  
    /*********************************************************************/
	public function join_group() 
    {
        try
        {
            $user_id = decrypt($this->session->userdata('user_id'));   //acceptor ID
			$group_id = $this->input->post('grp_id');
       		//$owner_id = intval($this->input->post('owner_id'));
			## check if already sent
			$is_exists = $this->church_new_model->request_already_sent($user_id, $group_id);
			if($is_exists){
				$_ret =1;
			}
			else{
				
				$info = array();
				
				$info['i_prayer_group_id'] = $group_id;
				$info['i_user_id'] =  $user_id;
				$info['s_status'] = 'pending';
				$info['i_request'] = 1;
				$info['dt_created_on'] = get_db_datetime();
				
				$_ret = $this->church_new_model->insert_group_member($info);
				
			}
			## 
			$grp_info  = $this->church_new_model->get_prayer_group_details($group_id);
			
			## check if opted for this notification or not ##
			  //$notificaion_opt = $this->user_alert_model->check_option_user_id($user_id);	
			  
			 // pr( $notificaion_opt,1);
			## insert noifications ####
			/*if($notificaion_opt['e_prayer_grp_joining_req'] == 'Y'){
				$notification_arr['i_requester_id'] = $user_id;
				$notification_arr['i_accepter_id'] =  $owner_id;
				$notification_arr['s_type'] = 'prayer_group_joining';
				$notification_arr['dt_created_on'] = get_db_datetime();
				$ret = $this->user_notifications_model->insert($notification_arr);	
			}*/
			//$message_id = parent::social_notifications_message($user_id, $owner_id, 'prayer_group_joining', $group_id);
			/*$email_opt = $this->user_alert_model->check_option_email_user_id($user_id);
						if($email_opt['e_prayer_grp_joining_req'] == 'Y' ){
						
						$mail_arr['i_requester_id'] =get_username_by_id( $user_id);
						$mail_arr['i_accepter_id'] =  get_username_by_id($owner_id);
						$mail_arr['s_type'] = 'prayer_group_joining';
						$mail_arr['group_name']=get_prayer_name_group_by_id($group_id);
						$mail_id=get_useremail_by_id($owner_id);
						 $this->load->library('email');
						 $this->load->helper('html');
						 //echo $mail_id;exit;
					$email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
					  $body=$this->load->view('logged/my-mail-template.phtml',$mail_arr,TRUE);
						$this->email->initialize($email_setting);
						$this->email->from('admin@cogtime.com', 'Team Cogtime');
					$this->email->to("$mail_id");
						//$this->email->bcc("$mailids");
				//$this->email->cc('arif.zisu@gmail.com');
					//$this->email->bcc('trisha.paul@hotmail.com');
					$this->email->subject($mail_arr["i_requester_id"].' wanted to join your Prayer Group.');
					$this->email->message("$body");

					$this->email->send();
					}*/
			### adding to prayer group notifiaction table:
			$message=get_username_by_id($user_id).' has sent request to join '.$grp_info[0]->s_group_name." on ".getShortDateWithTime(get_db_datetime(),2);
			$grp_notification_arr = array();
			
			$grp_notification_arr['i_requester_user_id'] = $user_id;
			//$grp_notification_arr['i_user_id'] = $owner_id;
			$grp_notification_arr['i_prayer_group_id'] = $group_id;
			$grp_notification_arr['s_type'] = 'joining_req';
			$grp_notification_arr['msg'] = $message;
			$grp_notification_arr['dt_created_on'] = get_db_datetime();
			$grp_notification_arr['church_id'] = $_SESSION['logged_church_id'];
			$this->church_new_model->insert_group_notifications($grp_notification_arr);
			
			### end  ###
			
			
			$html = '<input type="button"  value="Re-send join request " class="accept"  onclick="sendGrpJoinReq('.$group_id.', '.$user_id.')" />';
			
			
            if($_ret)
            {                     
                echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'Prayer group joining request sent successfully.','html'=>$html) );
            }
            else
            {
                echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'Error!') );
            }
    
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }
	
	
	public function accept_join_req($grp_id, $uid, $id)
	{
      //echo $grp_id.' == '. $uid.' == '. $id; exit;
	 // $isLimitExceed = $this->prayer_group_model->checkGroupMaxLimit($this->max_prayer_group, $uid, 'created');
	  if(!$isLimitExceed){
			  $arr = array();
			  $where = array('i_prayer_group_id' => $grp_id,
							  'i_user_id' => $uid
							  );
							  
			  $arr['s_status']	    = 'accepted';
			  $arr['dt_joined_on']	= get_db_datetime();
			  $msgarr	= array();
			  $sender_id = intval(decrypt($this->session->userdata('user_id')));
	  
			  $receiver_id = $uid;
			  $msgarr	= array('s_type'=>'prayer_group_joining', 'i_referred_media_id'=>$grp_id,'i_receiver_id'=>$sender_id);
			  
			  
				  ## check if opted for this notification or not ##
					$notificaion_opt = $this->user_alert_model->check_option_user_id(decrypt($this->session->userdata('user_id')));	
				  ## insert noifications ####
				  if($notificaion_opt['e_prayer_grp_request_accepted'] == 'Y'){
					  $notification_arr['i_requester_id'] = decrypt($this->session->userdata('user_id'));
					  $notification_arr['i_accepter_id'] = $uid;
					  $notification_arr['s_type'] = 'prayer_group_accept_join_request';
					  $notification_arr['dt_created_on'] = get_db_datetime();
					  $ret = $this->user_notifications_model->insert($notification_arr);	
					  
				  }
			  
			  $res = $this->prayer_group_model->accept_invitation($where,$arr,$msgarr);
			  
			  parent::social_notifications_message($sender_id, $uid, 'prayer_group_join_request_accepted_by_owner', $grp_id) ;
			  $email_opt = $this->user_alert_model->check_option_email_user_id($uid);
						if($email_opt['e_prayer_grp_request_accepted'] == 'Y' ){
						
						$mail_arr['i_requester_id'] =get_username_by_id(decrypt($this->session->userdata('user_id')));
						$mail_arr['i_accepter_id'] =  get_username_by_id($uid);
						$mail_arr['s_type'] = 'e_prayer_grp_request_accepted';
						$mail_arr['group_name']=get_prayer_name_group_by_id($grp_id);
						$mail_id=get_useremail_by_id($uid);
						 $this->load->library('email');
						 $this->load->helper('html');
						 //echo $mail_id;exit;
					$email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
					  $body=$this->load->view('logged/my-mail-template.phtml',$mail_arr,TRUE);
						$this->email->initialize($email_setting);
						$this->email->from('admin@cogtime.com', 'Team Cogtime');
					$this->email->to("$mail_id");
						//$this->email->bcc("$mailids");
				//$this->email->cc('arif.zisu@gmail.com');
					//$this->email->bcc('trisha.paul@hotmail.com');
					$this->email->subject($mail_arr["i_requester_id"].' has accepted your prayer group joining request.');
					$this->email->message("$body");

					$this->email->send();
					}
			  
			  ### adding to prayer group notifiaction table:
			  $grp_notification_arr = array();
			  
			  $grp_notification_arr['i_user_id'] = $uid;
			  $grp_notification_arr['i_prayer_group_id'] = $grp_id;
			  $grp_notification_arr['s_type'] = 'join';
			  $grp_notification_arr['dt_created_on'] = get_db_datetime();
			  
			  $this->prayer_group_model->insert_group_notifications($grp_notification_arr);
			  ### adding to prayer group notifiaction table: 
			  
			  echo json_encode(array('msg'=>'The Prayer group joining request has been accepted successfully.','uid'=>$sender_id));
			  exit;
	   }
		else{
			  $arr = array();
			  $where = array('i_prayer_group_id' => $grp_id,
							  'i_user_id' => $uid
							  );
							  
			  $arr['s_status']	    = 'rejected';
			  $arr['dt_joined_on']	= get_db_datetime();
			  $msgarr	= array();
			  $sender_id = intval(decrypt($this->session->userdata('user_id')));
	  
			  $receiver_id = $uid;
			  $msgarr	= array('s_type'=>'prayer_group_joining', 'i_referred_media_id'=>$grp_id,'i_receiver_id'=>$sender_id);
			  $res = $this->prayer_group_model->accept_invitation($where,$arr,$msgarr);
			echo json_encode(array('msg'=>'User have already joined maximum permitted numbers of prayer groups.','uid'=>$sender_id));
			exit;
		}
	}  
	
	function deletePrayerChatroom(){
		
		$room_id =  trim($this->input->post('id'));
		$this->chat_rooms_model->delete_chat_invitation($room_id);
		###On -fly delete from room table.
					  
		$host = "127.0.0.1";
		$port = 51127;
		$apiCommand = '<?xml version="1.0" encoding="UTF-8"?><Command group="default" api_pwd="3874-3459-9293-2194" type="del_room" room_id="'.$room_id.'" />';
		$result = "";
		$resultDoc = "";
		$fp = @fsockopen($host, $port, $errno, $errstr, 2);
		if(!$fp)
		{
			echo "Failed to excute api command,maybe host chat server is not started";
		}
		else
		{
			fputs($fp,$apiCommand."\0");
			while (!feof($fp))
			{
				$resultDoc .= fgets($fp, 1024);
				$resultDoc = rtrim($resultDoc);
			}
			$parser = xml_parser_create("UTF-8");
			xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
			xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
			if (!xml_parse_into_struct($parser, $resultDoc, $values, $tags))
			{
				printf("XML error: %s at line %d while parsing entity n",
				xml_error_string(xml_get_error_code($parser)),
				xml_get_current_line_number($parser));
			}
			else
			{
				#print_r($values);
				xml_parser_free($parser);
				fclose($fp);
			   
			}
		}
		
		echo json_encode(array('success'=>true));
		exit;
	}
	
	
	function delete_prayer_group() {
        $groupid = $this->input->post('gr_id');
		//echo $_SESSION['logged_church_id'];exit;
       // $member_arr = $this->prayer_group_model->get_members_by_grpid($groupid);
        //pr($member_arr);
        $this->church_new_model->delete_prayer_group($groupid);
        ob_start();
        $this->groups_ajax_listing(0,$_SESSION['logged_church_id']);
        $content = ob_get_contents();
        $content_obj = json_decode($content);
        $group_listing = $content_obj->grp_html;
        ob_end_clean();
        echo json_encode(array('html' => $group_listing, 'msg' => 'Group Deleted Successfully'));
        exit;
    }
	function send_invitation($group_id)
	{			
		$arr_frnd=array();
		$arr_frnd=$this->input->post('inv');
		//pr($arr_frnd,1);
		//pr($complete_arr_frnd,1);
		//insert_invitation($group_id,$_POST,$this->db->prayer_group_invitation,'i_prayer_group_id','prayer_group',$group_id);
		//$invited=get_invited($group_id,$this->db->prayer_group_invitation,'i_prayer_group_id');
		$this->load->model('church_new_model');
		foreach($arr_frnd as $k=>$val){
				$info = array();
				
				$info['i_prayer_group_id'] = $group_id;
				$info['i_user_id'] =  $val;
				$info['s_status'] = 'pending';
				$info['dt_created_on'] = get_db_datetime();
				
				$_ret = $this->church_new_model->insert_group_member($info);
                                
                                
                                /*******************************************************/
                                /************************notification*******************************/
                          $grp_info  = $this->church_new_model->get_prayer_group_details($group_id);
                         // pr($grp_info,1);
                          $message=get_username_by_id($val).' has invited  to join at '.$grp_info[0]->s_group_name." on ".getShortDateWithTime(get_db_datetime(),2);
                          $grp_notification_arr = array();
   
                            $grp_notification_arr['i_requester_user_id'] = $val;
                            //$grp_notification_arr['i_user_id'] = $owner_id;
                            $grp_notification_arr['i_prayer_group_id'] = $group_id;
                            $grp_notification_arr['s_type'] = 'invited';
                            $grp_notification_arr['msg'] = $message;
                            $grp_notification_arr['dt_created_on'] = get_db_datetime();
                            $grp_notification_arr['church_id'] = $_SESSION['logged_church_id'];
                            $this->church_new_model->insert_group_notifications($grp_notification_arr);

                          /********************************************************/
                                /*********************************************************/
				
			
			## 
						$mail_arr['i_requester_id'] =get_username_by_id( decrypt($this->session->userdata('user_id')));
						$mail_arr['i_accepter_id'] =  get_username_by_id($val);
						$mail_arr['s_type'] = 'e_prayer_grp_invitation';
						$mail_arr['group_name']='';
						$mail_id=get_useremail_by_id($val);
						 $this->load->library('email');
						 $this->load->helper('html');
						 //echo $mail_id;exit;
					$email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
					  $body=$this->load->view('logged/my-mail-template.phtml',$mail_arr,TRUE);
						$this->email->initialize($email_setting);
						$this->email->from('admin@cogtime.com', 'Team Cogtime');
					$this->email->to("$mail_id");
						//$this->email->bcc("$mailids");
				//$this->email->cc('arif.zisu@gmail.com');
					//$this->email->bcc('trisha.paul@hotmail.com');

					$this->email->subject($mail_arr["i_requester_id"].' has invited you to his/her Prayer Group.');
					$this->email->message("$body");

					$this->email->send();
			}
		/*	$email_opt = $this->user_alert_model->check_option_email_user_id($user_id);
						if($email_opt['e_prayer_grp_invitation'] == 'Y' ){
						
						$mail_arr['i_requester_id'] =get_username_by_id( decrypt($this->session->userdata('user_id')));
						$mail_arr['i_accepter_id'] =  get_username_by_id($user_id);
						$mail_arr['s_type'] = 'e_prayer_grp_invitation';
						$mail_arr['group_name']=get_prayer_name_group_by_id($group_id);
						$mail_id=get_useremail_by_id($user_id);
						 $this->load->library('email');
						 $this->load->helper('html');
						 //echo $mail_id;exit;
					$email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
					  $body=$this->load->view('logged/my-mail-template.phtml',$mail_arr,TRUE);
						$this->email->initialize($email_setting);
						$this->email->from('admin@cogtime.com', 'Team Cogtime');
					$this->email->to("$mail_id");
						//$this->email->bcc("$mailids");
				//$this->email->cc('arif.zisu@gmail.com');
					//$this->email->bcc('trisha.paul@hotmail.com');

					$this->email->subject($mail_arr["i_requester_id"].' has invited you to his/her Prayer Group.');
					$this->email->message("$body");

					$this->email->send();*/
					echo json_encode(array('message'=>'invitation sent successfully'));
		
	}
	
	
	function delete_invitation_sent()
	{
		$groupid	= $this->input->post('gr_id');
		$sql = 'DELETE FROM  cg_prayer_group_members WHERE id="'.$groupid.'"';
		$this->db->query($sql);
		
		ob_start();
		$this->get_pending_groups_requests_sent($page);
		$content = ob_get_contents();
		$content_obj = json_decode($content);
		$grp_html = $content_obj->grp_html;
		ob_end_clean();
		echo json_encode( array('grp_html'=>$grp_html,'msg'=>'Request deleted successfully') );
		exit;
	}
	function leave_prayer_group()
	{
	
		$groupid	= $this->input->post('gr_id');
	
		$user_id= intval(decrypt($this->session->userdata('user_id')));
		$this->church_new_model->leave_prayer_group($groupid,$user_id);
		
		ob_start();
		$this->groups_ajax_listing(0,$_SESSION['logged_church_id']);
		$content = ob_get_contents();
		$content_obj = json_decode($content);
		$group_listing = $content_obj->grp_html;
		ob_end_clean();
		echo json_encode( array('html'=>$group_listing,'msg'=>'Group Removed Successfully') );
		exit;
	}
	
	
	function prayer_group_members($prayer_group_id)
	{
		try
        {
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');

            parent::_add_church_css_arr( array('css/church.css') );
          
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			$data['prayer_group_id']=$prayer_group_id;
			$data['active_menu']='member';
			$this->load->model('church_new_model');
			//$data['post_pagination_per_page'] = $this->post_pagination_per_page;
			
			//$data['prayer_detail_arr'] = $this->prayer_group_model->get_group_detail_by_id($prayer_group_id);
			$data['prayer_member_arr'] = $this->church_new_model->get_members_by_grpid($prayer_group_id);
			
			
            # view file...
            
            $VIEW = "logged/church/church_prayer_group_member.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

	}

	
	// remove member
	function remove_member()
	{
		$member=$this->input->post('member');
		$prayer=$this->input->post('grp');
		$ret=$this->church_new_model->leave_prayer_group($prayer,$member);
		
		echo json_encode(array('success'=>$ret,'message'=>'Member Removed Successfully.'));
		
	}
         function delete_prayer_post(){
            $p_id = $this->input->post('p_id');
            $i_use_id = $this->input->post('i_use_id');
            $prayer_group_id = $this->input->post('prayer_group_id');
            $this->db->delete('cg_church_prayer_group_post', array('id' => $p_id ));
            
            
            /**********************************************************************/
            $logged_user_id = intval(decrypt($this->session->userdata('user_id')));
            $grp_info  = $this->church_new_model->get_prayer_group_details($prayer_group_id);
                          $message=get_username_by_id($logged_user_id).' deleted a post of '.$grp_info[0]->s_group_name." on ".getShortDateWithTime(get_db_datetime(),2);
                          $grp_notification_arr = array();
   
                            $grp_notification_arr['i_requester_user_id'] = $i_use_id;
                            //$grp_notification_arr['i_user_id'] = $owner_id;
                            $grp_notification_arr['i_prayer_group_id'] = $prayer_group_id;
                            $grp_notification_arr['s_type'] = 'post';
                            $grp_notification_arr['msg'] = $message;
                            $grp_notification_arr['dt_created_on'] = get_db_datetime();
                             $grp_notification_arr['church_id'] = $_SESSION['logged_church_id'];

                            $this->church_new_model->insert_group_notifications($grp_notification_arr);
            /**************************************************************************/
            
            echo json_encode(array('msg'=>'ok'));
            

        }
        function edit_prayer_post(){
            $i_use_id = $this->input->post('i_use_id');
            $p_id = $this->input->post('p_id');
            $wall_msg = trim($this->input->post('wall_msg'));
              $prayer_group_id = $this->input->post('prayer_group_id');
            $data = array(
               's_post_desc' => $wall_msg,
               'dt_updated_on' => get_db_datetime()
               
            );
/***************************************************************/
             /**********************************************************************/
            $logged_user_id = intval(decrypt($this->session->userdata('user_id')));
            $grp_info  = $this->church_new_model->get_prayer_group_details($prayer_group_id);
                          $message=get_username_by_id($logged_user_id).' edited a post of '.$grp_info[0]->s_group_name." on ".getShortDateWithTime(get_db_datetime(),2);
                          $grp_notification_arr = array();
   
                            $grp_notification_arr['i_requester_user_id'] = $i_use_id;
                            //$grp_notification_arr['i_user_id'] = $owner_id;
                            $grp_notification_arr['i_prayer_group_id'] = $prayer_group_id;
                            $grp_notification_arr['s_type'] = 'post';
                            $grp_notification_arr['msg'] = $message;
                            $grp_notification_arr['dt_created_on'] = get_db_datetime();
                             $grp_notification_arr['church_id'] = $_SESSION['logged_church_id'];

                            $this->church_new_model->insert_group_notifications($grp_notification_arr);
            /**************************************************************************/
            /*******************************************************/

$this->db->where('id', $p_id);
$this->db->update('cg_church_prayer_group_post', $data); 
echo json_encode(array('msg'=>'ok'));

        }
}   // end of controller...

