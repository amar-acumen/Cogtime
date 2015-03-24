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


class My_ring extends Base_controller
{
    
    private $pagination_per_page =  6 ;
    private $search_pagination_per_page =  20 ;
    private $popular_pagination_per_page =  10 ;
    
    
    
    private $ring_members_pagination_per_page =  10 ;
    
    public function __construct()
     {
	 	
        try
        {
            parent::__construct();
               parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...
			$this->upload_path = BASEPATH.'../uploads/user_ring_logos/';
            //$this->load->model('users_model');
			$this->load->model('contacts_model');
			$this->load->model('netpals_model');
			$this->load->model('my_ring_model');
			
			$this->i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    
    public function index($s_member_type = '') 
    {
        try
        {
                  
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
            $this->data["MAIN_MENU_SELECTED"] = 1;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',*/
										'js/production/tweet_utilities.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css') );
            
           
		   
    //pr($data['blogdata']);
    
            $this->session->set_userdata('where','');
			
			
            
            # view file...
			ob_start();
			$content = $this->generate_ring_listing_AJAX();
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['listingContent'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
			//$data['listingContent'] = $content;
            $VIEW = "logged/ring/my_ring.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }
	
	
	public function generate_ring_listing_AJAX($page=0)
    {
		$wh	= " AND r.i_user_id='".$this->i_profile_id."'";
		$wh1	= " AND inv.i_invited_id='".$this->i_profile_id."'";
		$data['ringdata']	= $this->my_ring_model->show_ring_by_user($wh,$wh1,$page,$this->pagination_per_page,'');
		
		$data['ringdata']	= check_friend_netpal_status($data['ringdata']);
		
		$data['pagination_per_page'] = $this->pagination_per_page;
		//pr($result);
		$resultCount = count($data['ringdata']);
		$total_rows = $this->my_ring_model->gettotal_ring_by_user($wh,$wh1);
		$cur_page = $page + $this->pagination_per_page;
        
        
		// getting auction-category listing...
		$data['no_of_ring'] = $total_rows;
		$data['no_of_result'] = $total_rows;
		$data['current_page_1'] = $cur_page;

		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->pagination_per_page)
			  $view_more = false;
         //--------- end check
        # loading the view-part...
        $AJAX_VIEW_FILE = 'logged/ring/ajax_ring/ajax_listing_ring.phtml';
        
        
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
    
    
    
    
    function create_my_ring()
    {
       
        try
        {
                  
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
            $this->data["MAIN_MENU_SELECTED"] = 1;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',*/
										'js/production/my_events.js',
										'js/production/tweet_utilities.js',
										'js/jquery.textCounter-min.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css') );
            
			$wh_cat	= "i_delete=0";
			$data['category']	= makeOptionRingCategory($wh_cat);
			
			$i_user_id = intval(decrypt($this->session->userdata('user_id')));
			
			## FETCHING FRIENDS ###
			
			$WHERE = " WHERE 
						1
						AND c.s_status = 'accepted' 
						AND u.i_status=1 
						AND
						((c.i_requester_id = '".$i_user_id."' AND u.id=c.i_accepter_id ) 
						OR (c.i_accepter_id = '".$i_user_id."' AND u.id=c.i_requester_id ))  GROUP BY u.id "	;	
			  
			$ORDER_BY = "u.s_first_name ASC";
			$contacts = $this->contacts_model->fetch_multi_online_friends($WHERE, null, null,$ORDER_BY);	
			
			$exclude_id_csv = '';
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
			
			$data['contact_arr'] = $contact_arr;
			
			$data['MAX_GRP'] =  $this->data['site_settings_arr']['i_max_ring'];
			
            $VIEW = "logged/ring/create_my_ring.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

   
    }
    
	public function add_ring()
	{
        
			$arr_messages = array();
				
			# error message trapping...
			if( trim($this->input->post('ring_name'))=='') 
			{
					$arr_messages['name'] = "* Required Field.";
			}
			
			if( trim($this->input->post('category'))=='') 
			{
					$arr_messages['category'] = "* Required Field.";
			}
			
			if( trim($this->input->post('sub_cat'))=='-1') 
			{
					$arr_messages['sub_cat'] = "* Required Field.";
			}
			
			if( isset($_FILES['ring_logo']['name']) && $_FILES['ring_logo']['name']!='') {
				preg_match('/(^.*)\.([^\.]*)$/', $_FILES['ring_logo']['name'], $matches);
				$ext = "";
				if(count($matches)>0) {
					$ext = $matches[2];
					$original_name = $matches[1];
				}
				else
					$original_name = 'photo';
				if ( !in_array($ext , $this->config->item('VALID_IMAGE_EXT'))) 
				{
					 $arr_messages['logo'] ="supported extensions are ".implode(' , ',$this->config->item('VALID_IMAGE_EXT'));
				}
				else if($_FILES['s_photo']['size'] > $this->config->item('MAX_UP_FILE_SIZE')*1024*1024)
				 {
					$arr_messages["logo"] = "Maximum file upload size is ".$this->config->item('MAX_UP_FILE_SIZE')." MB.";
				 }		
			}
			else
			{
				$arr_messages['logo'] = "* Required Field.";
			}
				$arr_frnd=array();
				$arr_netpal=array();
				$arr_pp=array();
				$arr_pg=array();
				if($this->input->post('frndinv')=='')
				{
					$arr_frnd['0']='0';
					//echo '0';
				}
				else
				{
					$arr_frnd=$this->input->post('frndinv');
			//echo '1';
				}
				if($this->input->post('netpalinv') == '')
				{
					$arr_netpal['0']='0';
				}
				else
				{
					$arr_netpal=$this->input->post('netpalinv');
				}
				if($this->input->post('ppinv') == '')
				{
					$arr_pp['0']='0';
				}
				else
				{
					$arr_pp=$this->input->post('ppinv');
				}
					$arr_group=$this->input->post('pginv');
					#pr($arr_group);
					foreach($arr_group as $val)
					{	$arr1=explode('_',$val);
						$arr_pg[]=$arr1['0'];
					}
				$complete_arr_frnd =  array();
				$contact_arr = array();
			
				$contact_arr = array_merge($arr_frnd,$arr_netpal);
				$complete_arr_frnd =  array_merge($contact_arr,$arr_pp);
				$complete_arr_frnd =  array_merge($complete_arr_frnd,$arr_pg);
				$complete_arr_frnd = array_unique($complete_arr_frnd);
				$complete_arr_frnd = array_filter($complete_arr_frnd);
				#pr($complete_arr_frnd);
			$MAX_RING_MEMBER  =  $this->data['site_settings_arr']['i_max_ring_member'];
			if(count($arr_frnd) > $MAX_RING_MEMBER && $MAX_RING_MEMBER != 0)
			{
				$arr_messages['invite_frnd'] = "* You can not add more than ".$MAX_RING_MEMBER." member";
			}
			
		   
		   ## adding restriction
		   $wh	= " AND r.i_user_id='".$this->i_profile_id."'"; 
		   $total_ring_created = $this->my_ring_model->gettotal_ring_created_by_user($wh);
		   $MAX_GRP =  $this->data['site_settings_arr']['i_max_ring'];
		
		   //pr($arr_messages);
			if( count($arr_messages)==0 && ($total_ring_created < $MAX_GRP || $MAX_GRP == 0)) {
					
				$info = array();
				$ip = getenv("REMOTE_ADDR") ;
                                $info['u_ip'] = $ip;
				$info['i_user_id'] 		= intval(decrypt($this->session->userdata('user_id')));	 
				$info['i_category_id'] 	= intval(decrypt($this->input->post('category')));
				$info['i_sub_category_id'] 	= intval(decrypt($this->input->post('sub_cat')));
				
				$info['s_ring_name'] 	= get_formatted_string($this->input->post('ring_name'));
				if(get_formatted_string($this->input->post('ring_desc')) != 'Max 500 Char allowed')
				{ 
					$info['s_description'] 	= get_formatted_string($this->input->post('ring_desc')); 
				}
				else
				{
					$info['s_description'] 	= '';
				}
				$info['i_privacy'] 		= $this->input->post('privacy_settings'); 
				$info['i_member'] 		= $this->config->item('ring_member');
				$info['s_logo'] 		= $this->_upload_photo('','ring_logo');
				$info['dt_created_on'] 	= get_db_datetime();
				
				//pr($info,1);
			
				$_ret = $this->my_ring_model->add_ring($info,$complete_arr_frnd);
				#pr($_ret);
				
				
				insert_invitation($_ret,$_POST,$this->db->ring_invitation,'i_ring_id');
				### add ring chat room

//				$host = "127.0.0.1";
//				$port = 51127;
				/*$apiCommand = '<?xml version="1.0" encoding="UTF-8"?><Command group="default" api_pwd="3874-3459-9293-2194" type="add_room" name="'.$info['s_ring_name'].'" passallmessage="false" member = "true" ></Command>';/*
				
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
				
				### adding chat room id in ring table: 
				$arr = array();
				$arr['i_room_id'] = $new_chat_room_id;//$new_chat_room_id;
				$this->my_ring_model->update($arr,$_ret);
				/***********add all room table*************************************/
                                
                                 $user_id = intval(decrypt($this->session->userdata('user_id')));
                                $query = $this->db->get_where('cg_users', array('id' => $user_id));
                                foreach ($query->result() as $row)
                                   {
                                       $time_zone = $row->s_time;
                                   }
                                   $nz_time = new DateTime(null, new DateTimezone($time_zone));
                                  $current_user_time = $nz_time->format('Y-m-d H:i:s');
                                
                                
                               $all_room = array();
                               $all_room['i_chat_room_id'] = $new_chat_room_id;
                               $all_room['user_id'] = intval(decrypt($this->session->userdata('user_id')));
                               $all_room['dt_start_time'] = $current_user_time;
                               $all_room['dt_end_time'] =  '';
                               $all_room['s_type'] = 4;
                               $data = $all_room;
                                $this->db->insert('cg_all_chat_room', $data);
                               /***********add all room table*************************************/
				
				## adding to ring chat tbl 
				/*if(count($arr_frnd)){
					foreach($arr_frnd as $recipient_id ) 
					{					
						$chat_invitation = array();
						$chat_invitation['i_ring_id'] = $_ret;
						$chat_invitation['i_chat_room_id'] = $new_chat_room_id;
						$chat_invitation['i_user_id'] = $recipient_id;
						$chat_invitation['s_status'] = 'pending';
						$chat_invitation['is_expired'] = 0;
						$chat_invitation['dt_created_on'] = get_db_datetime();
						
						$this->load->model('chat_rooms_model');
						$ret_id = $this->chat_rooms_model->InsertRingChatInvitation($chat_invitation);
						
					}
				}*/

				### add ring chat room
				
				
				
				$this->i_profile_id = intval(decrypt($this->session->userdata('user_id')));
				
				### send mail  ###
				if($_ret)
				{	
					
					if(count($complete_arr_frnd))
					{
						#pr($complete_arr_frnd,1);
						foreach($complete_arr_frnd as $val)
						
						{		
								$add_inv_arr = array();
								$add_inv_arr['i_ring_id']		= $_ret;
								$add_inv_arr['i_invited_id']	= $val;
								
								// $this->db->insert($this->db->RING_INV_USER ,$add_inv_arr);		
								//$this->social_notifications_message($this->i_profile_id, $val, 'ring_join_request', $_ret) ;
                                                                
                                                                
                                                                  /*******send mail*********/
                                         $this->load->model('users_model');
                $this->load->model('my_ring_model');
                #echo $i_sender_id.'####'.$i_receiver_id;
                $user_sender = $this->users_model->get_user_email_by_id($this->i_profile_id);
                $user_receiver = $this->users_model->get_user_email_by_id($val);
               // pr($user_receiver,1);
                /********************GET ADMIN EMAIL***************************************************/
			$query = $this->db->get_where('cg_admin_user', array('id' => 1));
                        
                            foreach ($query->result() as $row)
                                {
                                   $admin_mail = $row->s_email;
                                }
            
			/****************************************************************************/
                   $this->load->library('email');
    $this->load->helper('html');
        $email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
$this->email->initialize($email_setting);
                #pr($user_sender);pr($user_receiver);
                $body = ' ';
                $subject = ' ';
$url = '<a href="' . base_url() . 'accept_invitation/' . encrypt($val) . '" target="_blank" style=" width:108px; height:28px; background-color:#62C3BC; color:#fff; display:block; text-align:center; text-decoration:none; padding-top:8px;">Join</a>';
               $logo="http://cogtime.com/images/logo.png";
    $body = '<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#e9f3f5" style="font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:19px;">
  <tr>
    <td align="left" style="background:#013D62; border-bottom:5px solid #62C3BC; padding:15px 0 15px 20px;"><img src="'.$logo.'" alt= ""></td>
  </tr>
  <tr style="border-top:1px solid #ffffff;">
    <td style="padding-top:10px; padding-bottom:10px;">&nbsp;</td>
  </tr>
  <tr>
  	
  </tr>
  <tr>
  	<td style="padding:15px;"><p> Dear '.$user_receiver['s_profile_name'].',</p>
<p>You have received ring request from '.$user_sender['s_profile_name'].' in COGTIME.</p>
    <p>
    To view the message, please log in to '.base_url().'
    </p>

<p>Best Regards,</p>
<p>COGTIME Team</p>

            
	</td>
</tr>
  <tr>
    <td align="center" valign="middle" style="background:#A8A7A7; padding:15px;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle" style="color:#d3edfd; font-family:Arial, Helvetica, sans-serif; font-size:12px;"> <a href="http://acumencs.com/drandpt-arabic/contact-us/" style="color:#d3edfd; text-decoration:none;"></a></td>
        
        <td align="right" style="color:#013d62; font-family:Arial, Helvetica, sans-serif; font-size:12px; text-align="center" ">© All Rights Reserved<span style="color:#525252;"><strong> COGTIME 2014  </strong></span></td>
      </tr>
    </table></td>
  </tr>
</table>'; 
    $this->email->from($admin_mail, 'From Cogtime ');
$this->email->to($user_receiver['s_email']);
//->email->bcc("$mailids");
//$this->email->cc('arif.zisu@gmail.com');
//$this->email->bcc('them@their-example.com');

$this->email->subject('You have received a new message!');
$this->email->message("$body");

 $this->email->send();	
                                        /**********************/
                                                                
                                                                
                                                                
						}
						//pr($add_inv_arr);
					}
				}
				  
				echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'Ring created Successfully.') );
			}
			else if(count($arr_messages)==0 && $total_ring_created == $MAX_GRP)
			{
				echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'Maximum ring creation limit reached!') );
			}
			else
			{
				echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'error!') );
			}
		
		
		
	}
    
    //================================= delete Ring ===============================
    function deletering()
    {
        $ring_id = decrypt($this->input->post('i_del_id'));
		
		$res = $this->my_ring_model->get_by_id($ring_id);
		//pr($res,1);
		
		###On -fly delete from room table.
			/********for client server********/		  
		/*$host = "127.0.0.1";
		$port = 51127;
		$apiCommand = '<?xml version="1.0" encoding="UTF-8"?><Command group="default" api_pwd="3874-3459-9293-2194" type="del_room" room_id="'.$res['i_room_id'].'" />';
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
		}*/
		
		
		@unlink($this->upload_path.getThumbName($res['s_logo'],'thumb'));
		
		### send im to other ring users
		$member_arr = $this->my_ring_model->get_all_ring_members_ids_by_ring_ids($ring_id);
		
		if(count($member_arr)){	
		  foreach($member_arr as $val){
			 $this->social_notifications_message($this->i_profile_id, $val['i_invited_id'], 'ring_delete', $ring_id) ; 
		  }
		}
		### send im to other ring users
		//pr($member_arr);
		
        $res = $this->my_ring_model->delete_by_id($ring_id);
        
       
        
        $blogid = $data['blogdata'][0]['blogid'];
        ob_start();
        $this->generate_ring_listing_AJAX($blogid,1);
        $content = ob_get_contents();
        $content_obj = json_decode($content);
        $html = $content_obj->html;
        $view_more = $content_obj->view_more;
        $current_page = $content_obj->current_page;
        ob_end_clean();
        
        # success message...
        $SUCCESS_MSG = "Ring deleted successfully";

        echo json_encode(array('result'=>'success',
                               'html'=>$html,
                               'msg'=>$SUCCESS_MSG,
                               'view_more'=>$view_more,
                               'current_page'=>$current_page));
        
		exit;

    }
	
	function leavering()
    {
        $ring_id = decrypt($this->input->post('i_leave_id'));
        $res = $this->my_ring_model->leave_ring($ring_id);
		
        $ringdata = $this->my_ring_model->get_by_id($ring_id);
		$this->social_notifications_message($this->i_profile_id, $ringdata['i_user_id'], 'ring_leave', $ring_id) ; 
        
        $blogid = $data['blogdata'][0]['blogid'];
        ob_start();
        $this->generate_ring_listing_AJAX($blogid,1);
        $content = ob_get_contents();
        $content_obj = json_decode($content);
        $html = $content_obj->html;
        $view_more = $content_obj->view_more;
        $current_page = $content_obj->current_page;
        ob_end_clean();
        
        # success message...
        $SUCCESS_MSG = "You have left this ring sucessfully.";

        echo json_encode(array('result'=>'success',
                               'html'=>$html,
                               'msg'=>$SUCCESS_MSG,
                               'view_more'=>$view_more,
                               'current_page'=>$current_page));
        
		exit;

    }
    
	public function search_ring()
	{
		$posted=array();
		$this->data["posted"]=$posted;/*don't change*/    
		$data = $this->data;      
		$this->data["MAIN_MENU_SELECTED"] = 1;
		parent::_set_title('::: COGTIME Xtian network :::');
		parent::_set_meta_desc('');
		parent::_set_meta_keywords('');
        
            
		parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
									'js/switch.js','js/animate-collapse.js',
									'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
									'js/stepcarousel.js',*/'js/production/tweet_utilities.js'
									));
									
//		parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
//                                          'css/dd.css') );
										  
		$this->session->set_userdata('where','');
		
		$data['searchtitle']	= "All Rings";
        //$data['all_category']	= $this->my_ring_model->get_all_category(); 
		$data['pagination_per_page'] = $this->pagination_per_page; 
		$wh_cat	= "i_delete=0";
		$data['category']	= makeOptionRingCategory($wh_cat);
		
	    $this->session->set_userdata('where','');
		$this->session->set_userdata('is_post','');
		$this->session->set_userdata('WHERE_POST_COND','');
		$this->session->set_userdata('s_query_type','');
		//pr($data['all_category']);
		# view file...
		ob_start();
 		$content = $this->generate_ring_search_listing_AJAX();
		$content = ob_get_contents();
		$content_obj = json_decode($content);
		$data['listingContent'] = $content_obj->html; 
		$data['no_of_result'] =  $content_obj->no_of_result;
		$data['formpost'] = $content_obj->formpost;
		ob_end_clean();								  
		$VIEW = "logged/ring/search_ring.phtml"; 
        parent::_render($data, $VIEW);
	}
	
	
	public function generate_ring_search_listing_AJAX($page=0)
    {
		$wh	= "";
		$wh_ring_post = "";
		$is_post = 0;
		
		$ring_name	= $this->input->post('searchtxt');
		$cat_id	= decrypt($this->input->post('category'));
		$sub_cat	= decrypt($this->input->post('sub_cat'));
		/*if($ring_name!='')
		{
			$wh	.= " AND s_ring_name LIKE '%".$ring_name."%'";
		}
		if($cat_id!='')
		{
			$wh	.= " AND r.i_category_id ='".$cat_id."'";
		}*/
		$rd_type = trim($this->input->post('rd_type'));
		
		
		### new search 
		if($rd_type != ''){
			if($rd_type == 1 ){
				  
				  $s_query_type = 'both';
				  if($ring_name!='')
				  {
					  $wh	.= " AND (r.s_ring_name LIKE '%".$ring_name."%' OR r.s_description LIKE '%".$ring_name."%')";
				  }
				  if($cat_id!='')
				  {
					  $wh	.= " AND r.i_category_id ='".$cat_id."'";
				  }
				  if($sub_cat!='')
				  {
					  $wh	.= " AND r.i_sub_category_id ='".$sub_cat."'";
				  }
				  
				  if($ring_name!='')
				  {
					  $wh_ring_post	.= " AND (rp.s_post_title  LIKE '%".$ring_name."%' OR rp.s_post_description LIKE '%".$ring_name."%')";
				  }
				  if($cat_id!='')
				  {
					  $wh_ring_post	.= " AND r.i_category_id ='".$cat_id."'";
				  }
				  
				  
			}
			else if($rd_type == 2){
				 
				  $s_query_type = 'posts';
				  if($ring_name!='')
				  {
					  $wh	.= " AND (rp.s_post_title LIKE '%".$ring_name."%' OR rp.s_post_description LIKE '%".$ring_name."%')";
				  }
				  if($cat_id!='')
				  {
					  $wh	.= " AND r.i_category_id ='".$cat_id."'";
				  }
				  
				  if($sub_cat!='')
				  {
					  $wh	.= " AND r.i_sub_category_id ='".$sub_cat."'";
				  }
				  
			}
			
			else if($rd_type == 3){
				
				  $s_query_type = 'ring';
				  if($ring_name!='')
				  {
					  $wh	.= " AND (r.s_ring_name LIKE '%".$ring_name."%' OR r.s_description LIKE '%".$ring_name."%')";
				  }
				  if($cat_id!='')
				  {
					  $wh	.= " AND r.i_category_id ='".$cat_id."'";
				  }
				  
				  if($sub_cat!='')
				  {
					  $wh	.= " AND r.i_sub_category_id ='".$sub_cat."'";
				  }
			}
			
				  $this->session->set_userdata('where',$wh);
				  $this->session->set_userdata('WHERE_POST_COND',$wh_ring_post);
				  $this->session->set_userdata('s_query_type',$s_query_type);
				  //$is_post = 1;
				  $this->session->set_userdata('is_post',$is_post);
			
		}
		//echo $wh.' @@';
		/*if($ring_name=='' && $cat_id=='' && $rd_type == '')
		{
			echo json_encode( array('html'=>'', 
								'current_page'=>0, 
								'no_of_result'=>0,
								'total'=>0,
								'view_more'=>'' ,
								'cur_page'=>0,
								'formpost'=>'') );
			exit;
		}*/
		$wh = $this->session->userdata('where');
		$wh_ring_post = $this->session->userdata('WHERE_POST_COND');
		
		$s_query_type =  $this->session->userdata('s_query_type');
		
        $is_post = $this->session->userdata('is_post');
		
		if($wh != ''){
		$data['ringdata']	= $this->my_ring_model->show_all_public_ring_new($wh,$page,$this->pagination_per_page,'',$s_query_type, $wh_ring_post);
		
		//pr($data['ringdata']);		exit;
		$data['pagination_per_page'] = $this->pagination_per_page;
		//pr($result);
		
		$data['arr_join_req']	= $this->my_ring_model->get_join_req_arr();
		
		$snt_whr = ' AND i_request = 0 ';
		$data['arr_join_req_sent']	=  $this->my_ring_model->get_join_req_arr($snt_whr);
		
		$recv_whr = ' AND i_request = 1';
		$data['arr_join_req_recv']	=  $this->my_ring_model->get_join_req_arr($recv_whr);
		
		
		$resultCount = count($data['ringdata']);
		$total_rows = $this->my_ring_model->gettotal_of_all_public_ring_new($wh,$s_query_type, $wh_ring_post);
		$cur_page = $page + $this->pagination_per_page;
        
        
		// getting auction-category listing...
		$data['no_of_ring'] = $total_rows; 
		$data['no_of_result'] = $total_rows; 
		$data['current_page_1'] = $cur_page;
		}
		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->pagination_per_page)
			  $view_more = false;
         //--------- end check
        # loading the view-part...
        $AJAX_VIEW_FILE = 'logged/ring/ajax_ring/ajax_listing_search_ring.phtml';
        
        
   //pr($result);
		
		if( $total_rows>0 ) {
        	$listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true); 
		}
		else {
			$listingContent = '';
		}
		//echo 1; exit;
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>($listingContent), 
								'current_page'=>$cur_page, 
								'no_of_result'=>$data['no_of_result'],
								'total'=>$total_rows,
								'view_more'=>$view_more ,
								'cur_page'=>$data['current_page_1'],
								'formpost'=>1) );
    }   
    
	
	
	
	
 	public function add_join_request()
	{
		$err	= array();
		$ringid	 = decrypt($this->input->post('ringid'));
                 $MAX_RING_MEMBER  =  $this->data['site_settings_arr']['i_max_ring_member'];
                   $member = $this->input->post('member');
		$ringdata	= $this->my_ring_model->get_by_id($ringid);
		$arr['i_ring_id']		= $ringid;
		$arr['i_invited_id']	= $this->i_profile_id;
		$arr['i_request']		= 1;
		$arr['dt_request_date']	= get_db_datetime(); 
                if($member >= $MAX_RING_MEMBER && $MAX_RING_MEMBER != 0)
                         {
                    //die('ss');  
                    echo json_encode( array("msg"=>"error","err"=>$err,"removeid"=>$ringid));
			exit;
                    
                         }
		else if($this->my_ring_model->add_join_request($arr))
		{
		
			$this->social_notifications_message($this->i_profile_id, $ringdata['i_user_id'], 'ring_join_request_from_normal_user', $ringid) ;
			
			echo json_encode( array("msg"=>"success","err"=>$err,"removeid"=>$ringid));
			exit;
		}
		else
		{
			echo json_encode( array("msg"=>"error","err"=>$err,"removeid"=>$ringid));
			exit;
		}
	}
    
	
	
	
	
	
    public function _upload_photo($prev_img = '',$fileElementName)
     {
      	parent::check_login(TRUE,'',array('1')); // put this code on those pages which are not accessable by non logged in user
	   #pr($_FILES);
	    #$fileElementName = 's_photo';	 
        if(!empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] != '') 
		{
				preg_match('/(^.*)\.([^\.]*)$/', $_FILES[$fileElementName]['name'], $matches);
				$ext = "";
				if(count($matches)>0) {
					$ext = strtolower($matches[2]);
					$original_name = $matches[1];
				}
				else
					$original_name = 'image';

			
					$imagename = createImageName( $original_name );

					if(test_file($this->upload_path.$imagename.'-thumb.'.$ext)) {
						for( $i=0; test_file($this->upload_path.$imagename.'-'.$i.'-thumb.'.$ext); $i++ ) {
						}

						$new_imagename = $imagename.'-'.$i;
					}
					else {
						$new_imagename = $imagename;
					}

					$this->imagename = $new_imagename;

					$this->upload_image = $this->upload_path.$new_imagename.'.'.$ext;
					//echo $this->upload_path; exit;

					@move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $this->upload_image);
					                   
                    
                    # @@@@@@@@@@@@ NEW RESIZING PART [BEGIN] @@@@@@@@@@@
                        $config = array();
                        $config['source_image'] = $this->upload_image;
                        $config['thumb_marker'] = '-thumb';
                        $config['crop'] = false;
                        $config['width'] = 73;
                        $config['height'] = 72;
                        $config1['crop_from'] = 'middle';
                        $config['within_rectangle'] = true;
                        $config['small_image_resize'] = 'no_resize';
                        resize_exact($config);
                        unset($config);
            
                      					
					$this->s_picture_path = $new_imagename.'.'.$ext;
					
					@unlink($this->upload_image); //Unlink the original image........
					//@unlink($this->upload_path.getThumbName($prev_img,'thumb')); //Unlink the prevoius image........
					
					return $this->s_picture_path;
					
				}
        else
        {
            return $prev_img; // Unchanged previous image
        }
        
        
    }
	
	public function edit($id)
	{
		$posted=array();
		$this->data["posted"]=$posted;/*don't change*/    
		$data = $this->data;      
		$this->data["MAIN_MENU_SELECTED"] = 1;
		parent::_set_title('::: COGTIME Xtian network :::');
		parent::_set_meta_desc('');
		parent::_set_meta_keywords('');
	
		
		parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
									'js/switch.js','js/animate-collapse.js',
									'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
									'js/stepcarousel.js',*/
									'js/production/my_events.js',
									'js/production/tweet_utilities.js',
									'js/jquery.textCounter-min.js'
									));
									
//		parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
//									  'css/dd.css') );
		
		$data['ringdetail']	= $this->my_ring_model->get_by_id($id);
		$data['ringinvted']=$this->my_ring_model->get_invitation_by_ring_id($id);
		
		$wh_cat	= "i_delete=0";
		$data['category']	= makeOptionRingCategory($wh_cat,encrypt($data['ringdetail']['i_category_id']));
		
		
		 $this->load->model('ring_categories_model');
		 $id = decrypt($id);
		 
		 
		 $wh = " AND subcat.i_parent_category = ".$data['ringdetail']['i_category_id'];
		 $cat_arr = $this->ring_categories_model->getCategory( $wh);
		// pr($data['ringdetail']	,1);
		 
		 $html = '<div class="lable01" style="width:100px;">Sub Category:</div>  
							<div class="field03" style="width: 350px;">
							<select style="width:265px;"  id="sub_cat" name="sub_cat">
							<option value="-1">Select Sub Category</option>';
							
		 if(count($cat_arr)){
			 foreach($cat_arr as $k=> $val){
				 if($val['id'] == $data['ringdetail']['i_sub_category_id'])
						$sel = ' selected="selected"';
				 else
				 	$sel = '';	
					
				  $html .= '<option value="'.encrypt($val['id']).'" '.$sel.'>'.$val['s_category_name'].'</option>';
			 }
		  }
		  $html .= '</select><span id="err_sub_cat" class="error-message"></span></div>  <div class="clr"></div>';
		
			$data['sub_cat_html'] = $html;
		$VIEW = "logged/ring/edit_my_ring.phtml"; 
        parent::_render($data, $VIEW);	
		
	}
	
	
	public function update_ring($id)
	{
		
        
			$arr_messages = array();
				
			# error message trapping...
			if( trim($this->input->post('ring_name'))=='') 
			{
					$arr_messages['name'] = "* Required Field.";
			}
			/*if( trim($this->input->post('ring_desc'))=='') 
			{
					$arr_messages['desc'] = "* Required Field.";
			}*/
			if( trim($this->input->post('category'))=='') 
			{
					$arr_messages['category'] = "* Required Field.";
			}
			
			if( isset($_FILES['ring_logo']['name']) && $_FILES['ring_logo']['name']!='') {
				preg_match('/(^.*)\.([^\.]*)$/', $_FILES['ring_logo']['name'], $matches);
				$ext = "";
				if(count($matches)>0) {
					$ext = $matches[2];
					$original_name = $matches[1];
				}
				else
					$original_name = 'photo';
				if ( !in_array($ext , $this->config->item('VALID_IMAGE_EXT'))) 
				{
					 $arr_messages['logo'] ="supported extensions are ".implode(' , ',$this->config->item('VALID_IMAGE_EXT'));
				}
				else if($_FILES['s_photo']['size'] > $this->config->item('MAX_UP_FILE_SIZE')*1024*1024)
				 {
					$arr_messages["logo"] = "Maximum file upload size is ".$this->config->item('MAX_UP_FILE_SIZE')." MB.";
				 }		
			}
				$arr_frnd=array();
				$arr_netpal=array();
				$arr_pp=array();
				$arr_pg=array();
				if($this->input->post('frndinv')=='')
				{
					$arr_frnd['0']='0';
					//echo '0';
				}
				else
				{
					$arr_frnd=$this->input->post('frndinv');
			//echo '1';
				}
				if($this->input->post('netpalinv') == '')
				{
					$arr_netpal['0']='0';
				}
				else
				{
					$arr_netpal=$this->input->post('netpalinv');
				}
				if($this->input->post('ppinv') == '')
				{
					$arr_pp['0']='0';
				}
				else
				{
					$arr_pp=$this->input->post('ppinv');
				}
					$arr_group=$this->input->post('pginv');
					#pr($arr_group);
					foreach($arr_group as $val)
					{	$arr1=explode('_',$val);
						$arr_pg[]=$arr1['0'];
					}
				$complete_arr_frnd =  array();
				$contact_arr = array();
			
				$contact_arr = array_merge($arr_frnd,$arr_netpal);
				$complete_arr_frnd =  array_merge($contact_arr,$arr_pp);
				$complete_arr_frnd =  array_merge($complete_arr_frnd,$arr_pg);
				$complete_arr_frnd = array_unique($complete_arr_frnd);
				$complete_arr_frnd = array_filter($complete_arr_frnd);
				#pr($complete_arr_frnd,1);
			if( count($arr_messages)==0 ) {
					
				$info = array();
				
				$info['i_user_id'] 		= intval(decrypt($this->session->userdata('user_id')));	 
				$info['i_category_id'] 	= intval(decrypt($this->input->post('category')));
				$info['i_sub_category_id'] 	= intval(decrypt($this->input->post('sub_cat')));
				$info['s_ring_name'] 	= get_formatted_string($this->input->post('ring_name')); 
				if(get_formatted_string($this->input->post('ring_desc')) != 'Max 500 Char allowed')
				{ 
					$info['s_description'] 	= get_formatted_string($this->input->post('ring_desc')); 
				}
				else
				{
					$info['s_description'] 	= '';
				} 
				$info['i_privacy'] 		= $this->input->post('privacy_settings'); 
				$info['i_member'] 		= $this->config->item('ring_member');
				if($_FILES['ring_logo']['name']!='')
				{
					$ringdetail	= $this->my_ring_model->get_by_id($id);
					@unlink($this->upload_path.getThumbName($ringdetail['s_logo'],'thumb'));
					$info['s_logo'] 		= $this->_upload_photo('','ring_logo');
				}
				
				$info['dt_created_on'] 	= get_db_datetime();
				$_ret = $this->my_ring_model->update_ring($info,$id);
				$invitation_sent=get_invited($id,$this->db->ring_invitation,'i_ring_id');
				#pr($invitation_sent);
				$i=0;
				$inv_u=array();
				foreach($invitation_sent as $inv)
				{
					$inv_u[$i]=$inv['user_id'];
					$i++;
				}
				if (count($complete_arr_frnd))
				{
					foreach($complete_arr_frnd as $val)
					{  
							if(!in_array($val,$inv_u))
							{
								$add_inv_arr = array();
								$add_inv_arr['i_ring_id']		= $id;
								$add_inv_arr['i_invited_id']	= $val;
								$this->db->insert($this->db->RING_INV_USER ,$add_inv_arr);
							}
						
					}
				}
				
				
				insert_invitation($id,$_POST,$this->db->ring_invitation,'i_ring_id');
				
				### updating room information
//				$host = "127.0.0.1";
//				$port = 51127;
				/*$apiCommand = '<?xml version="1.0" encoding="UTF-8"?><Command group="default" api_pwd="3874-3459-9293-2194" type="edit_room" name="'.$info['s_ring_name'].'" roomid="'.$id.'"  passallmessage="false" member = "true" ></Command>';
				
				
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
						 
					}
				}
				/*$arr_frnd=$this->input->post('frnds');
				$arr_netpal=$this->input->post('netpal');
				$arr_pp=$this->input->post('pp');
				$complete_arr_frnd =  array();
				$contact_arr = array();
				$contact_arr = array_merge($arr_frnd,$arr_netpal);
				$complete_arr_frnd =  array_merge($contact_arr,$arr_pp);
				//$complete_arr_frnd = array_unique($complete_arr_frnd);
				//$complete_arr_frnd = array_filter($complete_arr_frnd);
				pr($contact_arr);*/
				$this->i_profile_id = intval(decrypt($this->session->userdata('user_id')));
				
				if(count($complete_arr_frnd) != '0')
				{	
				//pr($complete_arr_frnd);
					
					foreach($complete_arr_frnd as $val)
					{	
						if(!in_array($val,$inv_u))
							{			
								//echo $val.'===========';
							$this->social_notifications_message($this->i_profile_id, $val,'ring_join_request',$id) ;
							}
						
					}
				}
				
				echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'Ring updated Successfully.') );
			}
			else
			{
				echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>t('Error!')) );
			}
		
		
		
	
	}
	
	
 public function generateSubCat($id)
 {
	 $this->load->model('ring_categories_model');
	 $id = decrypt($id);
	 if(intval($id) != 0){
	 $wh = " AND subcat.i_parent_category = ".$id;
	 $cat_arr = $this->ring_categories_model->getCategory( $wh);
	 }
	// pr($cat_arr,1);
	 
	 $html = '<div class="lable05" style="width:95px;">Sub Category:</div>
                       <div class="field01">
						<select style="width:265px;"  id="sub_cat" name="sub_cat">
						<option value="-1">Select Sub Category</option>';
	 if(count($cat_arr)){
		 foreach($cat_arr as $k=> $val){
			  $html .= '<option value="'.encrypt($val['id']).'">'.$val['s_category_name'].'</option>';
		 }
	  }
	  $html .= '</select></div><div class="clr"></div>';
 	 echo json_encode(array('room_arr'=>$room_arr, 'html'=>$html)); 
	 exit;
 }
	
	public function send_cat_req()
	{
		$category	= $this->input->post('category');
		$i_user_id 	= intval(decrypt($this->session->userdata('user_id')));
		
		$info = get_primary_user_info($i_user_id);
		$this->load->helper('html');
					$this->load->library('email');
					 $email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
				$this->email->initialize($email_setting);
		$this->load->model('mail_contents_model');
		$mail_info = $this->mail_contents_model->get_by_name("send_ring_cate_request");
		$body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
		
		
		$body = sprintf3( $body, array(
				   'email'=>$info["s_email"],
				   'category_name'=>$category,
				   'admin' =>'admin@cogtime.com') );
				   
				   
						   
		$arr['subject'] 	= htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
		$arr['to']         = 'admin@cogtime.com';
		
		$arr['from_email'] 	= $info["s_email"];;
		$arr['from_name'] 	= $info["s_email"];
		$arr['message'] 	= $body;
		$this->email->from( $arr['from_email'], $arr['from_name']);
                #dump($arr);
				$this->email->subject($arr['subject']);
						
				$this->email->to($arr['to']);
				$this->email->bcc($arr['bcc']);
				$this->email->message("$body");
                        //send_mail($arr);
				$this->email->send();
		//send_mail($arr);
		
		$request_arr = array();
		$request_arr['i_user_id'] = $i_user_id;
		$request_arr['s_suggested_category_name'] = get_formatted_string($category);
		$request_arr['s_information'] = $body;
		$request_arr['dt_created_on'] = get_db_datetime();
		
		### insert  request
		$id = $this->db->insert('cg_ring_category_request' ,$request_arr);
                 $body_req = "<p> Hello admin@cogtime.com<p>,
                            <p>A member has requested for ring category.</p>
                             <br>
                            <p>Thanks</p>
                               <p>".$info["s_email"]."</p>";
                  parent::admin_send_message($i_user_id,'ring',$id,$body_req);
		### insert  request
		
		
		
			
		echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'New Category Request Sent Successfully.'));
	}
    
	
 public function generateSubCat_II($id)
 {
	 $this->load->model('ring_categories_model');
	 $id = decrypt($id);
	 
	 
	 $wh = " AND subcat.i_parent_category = ".$id;
	 $cat_arr = $this->ring_categories_model->getCategory( $wh);
	// pr($cat_arr,1);
	 
	 $html = '<div class="lable01" style="width:100px;">Sub Category:</div>  
                        <div class="field03" style="width: 350px;">
						<select style="width:265px;"  id="sub_cat" name="sub_cat">
						<option value="-1">Select Sub Category</option>';
	 if(count($cat_arr)){
		 foreach($cat_arr as $k=> $val){
			  $html .= '<option value="'.encrypt($val['id']).'">'.$val['s_category_name'].'</option>';
		 }
	  }
	  $html .= '</select><span id="err_sub_cat" class="error-message"></span></div>  <div class="clr"></div>';
 	 echo json_encode(array('room_arr'=>$room_arr, 'html'=>base64_encode($html))); 
	 exit;
 }
 
 
}   // end of controller...

