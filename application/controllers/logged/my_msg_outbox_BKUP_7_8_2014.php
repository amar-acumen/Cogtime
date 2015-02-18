<?php
/*********
* Author: 
* Date  : 
* Modified By: 
* Modified Date:
* 
*/
include(APPPATH.'controllers/base_controller.php');

class My_msg_outbox extends Base_controller 
{
    
	private $pagination_per_page =  20 ;
	
	public function __construct()
     {
       try
         {
         
            parent::__construct();
			
			parent::check_login(TRUE,'',array('1')); // put this code on those pages which are not accessable by non logged in user

            $this->load->model('users_model');
			   $this->load->model('data_messages_model');
         }        
        catch(Exception $err_obj)
         {
            
         }        
    }
    
    public function index() 
    {
    	try
         {
		        
			$data = $this->data;      
           	parent::_set_title('::: COGTIME Xtian network :::');
			parent::_set_meta_desc('');
			parent::_set_meta_keywords('');
		
			
			parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
										'js/switch.js','js/animate-collapse.js',
										'js/lightbox.js','js/jquery.dd.js',
										'js/stepcarousel.js',
										'js/jquery.ui.datepicker.js',
										'js/frontend/logged/message_box/my_outbox_message.js',
										'js/frontend/logged/tweets/tweet_utilities.js',
										));
										
			parent::_add_css_arr( array(
										'css/jquery-ui-1.8.13.custom.css',
										'css/jquery-ui-1.8.2.custom.css',
										'css/dd.css') );
			
            
            /////////////////////////////////////////////
             
			$i_user_id = intval(decrypt($this->session->userdata('user_id')));	
			$i_profile_id = $i_user_id;
			
			$this->load->model('users_model');
			$arr_profile_info = $this->users_model->fetch_this($i_user_id);
            #dump( $arr_profile_info );
			$data['arr_profile_info'] = $arr_profile_info; 
			
			$this->load->model('contacts_model');
			$contacts = $this->contacts_model->get_by_anyuser($i_user_id);
			$data['contacts'] = $contacts;
			
			## filter  ###
			
			$this->session->set_userdata('search_condition','');
			
			   
            ob_start();
            $this->outbox_ajax_pagination();
            $data['result_content'] = ob_get_contents();
            ob_end_clean(); 
			#pr($data);
			//////////////////////////////////////////
			
			$data['page_view_type'] = 'myaccount'; 
			$VIEW = "logged/message_box/my-msg-outbox.phtml";
			parent::_render($data, $VIEW);
		 
		 }        
        catch(Exception $err_obj)
         {
            
         }    
    }


      public function outbox_ajax_pagination($page=0) 
	  {

		 try
		 {
		   
			## seacrh conditions : filter ############
		 	$WHERE_COND = '';
			 if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) :
				$s_name = get_formatted_string(trim($this->input->post('txt_from')));
				$WHERE_COND .= ($s_name=='')?'':" AND CONCAT(u.s_first_name,' ',u.s_last_name) LIKE '%".$s_name."%' ";
				
				if($this->input->post('dt_sent') != ''){
					
					 $dt_start_date = $this->input->post('dt_sent');
					$WHERE_COND .= ($dt_start_date=='')?'':" AND DATE_FORMAT(m.dt_created_on,'%Y-%d-%m') ='".$dt_start_date."' ";
				}
				
				$this->session->set_userdata('search_condition',$WHERE_COND);
			
			
           endif;  
		   	
			$s_where = $this->session->userdata('search_condition');
			$i_user_id = intval(decrypt($this->session->userdata('user_id')));
			
		    $result = $this->data_messages_model->get_by_sender($i_user_id, intval($page), $this->pagination_per_page , $s_where);
			//echo $this->db->last_query();  exit;
	       $total_rows = $this->data_messages_model->get_total_by_sender($i_user_id , $s_where);
			//pr($result);
			
			$this->load->library('jquery_pagination');
			$config['base_url'] = base_url().'logged/my_msg_outbox/outbox_ajax_pagination/';
			$config['total_rows'] = $total_rows;
			$config['per_page'] = $this->pagination_per_page;
			$config['uri_segment'] = 4;
			$config['num_links'] = 9;
			$config['page_query_string'] = false;
            
            $config['prev_link'] = '&laquo; Previous';
            $config['next_link'] = 'Next &raquo;';

            $config['cur_tag_open'] = '<li><a class="select" href="javascript:void(0);">';
            $config['cur_tag_close'] = '</a></li>';

            $config['next_tag_open'] = '<li>';
            $config['next_tag_close'] = '</li>';

            $config['prev_tag_open'] = '<li><a href="javascript:void(0)">';
            $config['prev_tag_close'] = '</a></li>';

            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';
            
            $config['first_link'] = '';
            $config['last_link'] = '';

            $config['div'] = '#result_div'; /* Here #content is the CSS selector for target DIV */
			$config['js_bind'] = "showLoading();"; /* if you want to bind extra js code */
			$config['js_rebind'] = "hideLoading();";
	
			$this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();
            $data['result_arr'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $page;
			$data['total_pages'] = ceil($total_rows/$this->pagination_per_page);
			$p = ($page/$this->pagination_per_page);
			$data['current_loaded_page_no'] =  $p + 1;
		
			
            $VIEW_FILE = "logged/message_box/msg_outbox_ajax.phtml";
			$this->load->view( $VIEW_FILE , $data);
		} 
        catch(Exception $err_obj)
        {
            
        } 
	
    }
	
	
	
	
	public function get_message_body() {
		
		//sleep(3);
		
		$data = $this->data;
		$i_user_id = intval(decrypt($this->session->userdata('user_id')));
		$i_message_id = $this->input->post('message_id');
		//echo 'message_id='.$message_id;
		$arr['i_is_unread'] = 2;
		//$this->data_messages_model->update_by_id_receiver($arr, $i_message_id,$i_user_id );

		$this->load->model('data_messages_model');
		$message = $this->data_messages_model->get_by_id_sender($i_message_id, $i_user_id);

		/*$unread_mail = $this->data_messages_model->get_total_unread_by_user_id($i_user_id);
		$unread_mail_count = intval($unread_mail);
		if($unread_mail==0) {
			$unread_mail = '';
		}
		else {
			$unread_mail = "($unread_mail)";
		}*/

		$data['message'] = $message;
		$data['sent_msgs'] = 'true';

		#pr($data['message']);

		ob_start();
		$this->load->view('logged/message_box/mailbox_mail_detail.phtml', $data);
		$content = ob_get_contents();
		ob_end_clean();

		echo json_encode( array('error'=>'', 'debug'=>'', 'content'=>$content, 'unread_mail'=>$unread_mail,'unread_mail_count'=>$unread_mail_count) );
	}

	
	public function send_message() 
	{

		try
        {
			
	        $arr_messages = array();
					
					# error message trapping...
					if( trim($this->input->post('recipients'))=='') 
					{
							$arr_messages['send_recepients'] = "* You must select a recipient";
					}
					
					
					if( trim($this->input->post('txt_send_message'))=='') 
					{
							$arr_messages['send_message'] = "* Required Field.";
					}
					
					
		
				   //pr($arr_messages);
					if( count($arr_messages)==0 ) 
						{
		 					
								$this->load->model('users_model');
								
						
								$recipients_ids =  $this->input->post('recipients');
								
								foreach( explode(',',$recipients_ids) as $recipient_id ) 
								 {
									$recipient_id = decrypt($recipient_id);
									parent::send_message(decrypt($this->session->userdata('user_id')), $recipient_id, 'normal', $this->input->post('txt_send_subject'), $this->input->post('txt_send_message'));
									
								}
								echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>t('Message sent')) );
						}
						else
						{
							echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>t('Error!'),) );
						}
			
		}
        catch(Exception $err_obj)
        {
          
        }   
	}
     
	public function delete_messages() 
	{
		//$messages = $this->input->post('csv_mail_ids');

		$this->load->model('data_messages_model');
		
		$current_page = $this->input->post('current_page'); 

		foreach( explode(',',$this->input->post('csv_mail_ids')) as $message_id ) 
		{
			 $this->data_messages_model->delete_by_id_sender($message_id, intval(decrypt($this->session->userdata('user_id'))));
		}
			
			$content = '';
			ob_start();
			$this->outbox_ajax_pagination($current_page);
			$content =  ob_get_contents();
			ob_end_clean();
			
		echo json_encode( array('sucess'=>TRUE, 'content' =>$content , 'msg'=>'Selected messages successfully deleted') );
	}
   
    public function update_msg_status($i_message_id){
		
		$data = $this->data;
		$i_user_id = intval(decrypt($this->session->userdata('user_id')));
		$arr['i_is_unread'] = 2;
		$this->data_messages_model->update_by_id_sender($arr, $i_message_id,$i_user_id );
	}
	
	
	##################3 trashbox  ##################3333
	public function msg_trash() 
    {
    	try
         {
		        
			$data = $this->data;      
           	parent::_set_title('::: COGTIME Xtian network :::');
			parent::_set_meta_desc('');
			parent::_set_meta_keywords('');
		
			
			parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
										'js/switch.js','js/animate-collapse.js',
										'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
										'js/stepcarousel.js',
										'js/jquery.ui.datepicker.js',
										'js/frontend/logged/message_box/my_trash_message.js',
										'js/frontend/logged/tweets/tweet_utilities.js',
										));
										
			parent::_add_css_arr( array(
			'css/jquery-ui-1.8.13.custom.css','css/jquery-ui-1.8.2.custom.css',
										'css/dd.css') );
			
            
            /////////////////////////////////////////////
             
			$i_user_id = intval(decrypt($this->session->userdata('user_id')));	
			$i_profile_id = $i_user_id;
			
			$this->load->model('users_model');
			$arr_profile_info = $this->users_model->fetch_this($i_user_id);
            #dump( $arr_profile_info );
			$data['arr_profile_info'] = $arr_profile_info; 
			
			$this->load->model('contacts_model');
			$contacts = $this->contacts_model->get_by_anyuser($i_user_id);
			$data['contacts'] = $contacts;
			
			## filter  ###
			
			$this->session->set_userdata('search_condition','');
			
			   
            ob_start();
            $this->trash_ajax_pagination();
            $data['result_content'] = ob_get_contents();
            ob_end_clean(); 
			#pr($data);
			//////////////////////////////////////////
			
			$data['page_view_type'] = 'myaccount'; 
			$VIEW = "logged/message_box/my-msg-trash.phtml";
			parent::_render($data, $VIEW);
		 
		 }        
        catch(Exception $err_obj)
         {
            
         }    
    }


      public function trash_ajax_pagination($page=0) 
	  {

		 try
		 {
		   
			## seacrh conditions : filter ############
		 	$WHERE_COND = '';
			 if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) :
				$s_name = get_formatted_string(trim($this->input->post('txt_from')));
				$WHERE_COND .= ($s_name=='')?'':" AND CONCAT(u.s_first_name,' ',u.s_last_name) LIKE '%".$s_name."%' ";
				$type= $this->input->post('sort_trash');
				
				
				if($type == '-1')
				{
					$WHERE_COND .='';
				}
				else if($type == 'normal')
				{
					$WHERE_COND .=" AND m.s_type='normal'";
				}
				else if($type == 'user')
				{
					$WHERE_COND .=" AND m.s_type LIKE '".$type."%'";
				}
				
				else if($type == 'etrade' || $type == 'esw' || $type == 'efr')
				{
					$WHERE_COND .=" AND m.s_type LIKE '".$type."%' ";
				}
				else
				{
					$WHERE_COND .=" AND m.s_type LIKE '%".$type."%' ";
				}
				if($this->input->post('dt_sent') != ''){
					 $dt_start_date = $this->input->post('dt_sent');
					$WHERE_COND .= ($dt_start_date=='')?'':" AND DATE_FORMAT(m.dt_created_on,'%Y-%d-%m') ='".$dt_start_date."' ";
				}
				
				$this->session->set_userdata('search_condition',$WHERE_COND);
			
			
           endif;  
		   	
			$s_where = $this->session->userdata('search_condition');
			$i_user_id = intval(decrypt($this->session->userdata('user_id')));
			
		    $result = $this->data_messages_model->get_trash_msg_list($i_user_id, intval($page), $this->pagination_per_page , $s_where);
			//echo $this->db->last_query();  exit;
	       $total_rows = $this->data_messages_model->get_trash_msg_list_count($i_user_id , $s_where);
			//pr($result);
			
			$this->load->library('jquery_pagination');
			$config['base_url'] = base_url().'logged/my_msg_outbox/trash_ajax_pagination/';
			$config['total_rows'] = $total_rows;
			$config['per_page'] = $this->pagination_per_page;
			$config['uri_segment'] = 4;
			$config['num_links'] = 9;
			$config['page_query_string'] = false;
            
            $config['prev_link'] = '&laquo; Previous';
            $config['next_link'] = 'Next &raquo;';

            $config['cur_tag_open'] = '<li><a class="select" href="javascript:void(0);">';
            $config['cur_tag_close'] = '</a></li>';

            $config['next_tag_open'] = '<li>';
            $config['next_tag_close'] = '</li>';

            $config['prev_tag_open'] = '<li><a href="javascript:void(0)">';
            $config['prev_tag_close'] = '</a></li>';

            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';
            
            $config['first_link'] = '';
            $config['last_link'] = '';

            $config['div'] = '#result_div'; /* Here #content is the CSS selector for target DIV */
			$config['js_bind'] = "showLoading();"; /* if you want to bind extra js code */
			$config['js_rebind'] = "hideLoading();";
	
	
			$this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();
            $data['result_arr'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $page;
			$data['total_pages'] = ceil($total_rows/$this->pagination_per_page);
			$p = ($page/$this->pagination_per_page);
			$data['current_loaded_page_no'] =  $p + 1;
			
            $VIEW_FILE = "logged/message_box/trash_msg_ajax.phtml";
			$this->load->view( $VIEW_FILE , $data);
		} 
        catch(Exception $err_obj)
        {
            
        } 
	
    }
	
   
   public function delete_trash_messages() 
	{
		//$messages = $this->input->post('csv_mail_ids');

		$this->load->model('data_messages_model');
		
		$current_page = $this->input->post('current_page'); 
		$type_arr = explode(',',$this->input->post('type_vals'));

		foreach( explode(',',$this->input->post('csv_mail_ids')) as $k=>$message_id ) 
		{
			 $this->data_messages_model->delete_from_trash($message_id, $type_arr[$k]);
		}
			
			$content = '';
			ob_start();
			$this->trash_ajax_pagination($current_page);
			$content =  ob_get_contents();
			ob_end_clean();
			
		echo json_encode( array('sucess'=>TRUE, 'content' =>$content , 'msg'=>'Selected messages successfully deleted') );
	}
	
	
	public function move_messages() 
	{
		//$messages = $this->input->post('csv_mail_ids');

		$this->load->model('data_messages_model');
		
		$current_page = $this->input->post('current_page'); 
		$type_arr = explode(',',$this->input->post('type_vals'));
		
		foreach( explode(',',$this->input->post('csv_mail_ids')) as $k=>$message_id ) 
		{
			 $this->data_messages_model->moved_from_trash($message_id,$type_arr[$k]);
		}
			
			$content = '';
			ob_start();
			$this->trash_ajax_pagination($current_page);
			$content =  ob_get_contents();
			ob_end_clean();
			
		echo json_encode( array('sucess'=>TRUE, 'content' =>$content , 'msg'=>'Selected messages successfully deleted') );
	}
   
}

