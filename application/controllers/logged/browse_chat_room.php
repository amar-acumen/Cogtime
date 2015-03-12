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

class Browse_chat_room extends Base_controller
{
    private $pagination_per_page= 10;
    
    public function __construct()
     {
        try
        {
            parent::__construct();
            parent::check_login(TRUE, '', array('1'));
            # loading reqired model & helpers...
           
			$this->load->model('chat_rooms_model');
			$this->load->model('contacts_model');
			$this->load->helper('my_utility_helper');

			
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
            $this->data["MAIN_MENU_SELECTED"] = 1;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',*/
										'js/production/tweet_utilities.js',
										'js/production/events_helper.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css') );
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			
			$this->session->set_userdata('search_condition','');
			 
			$data['pagination_per_page'] = $this->pagination_per_page;
			$data['profile_id'] = $i_user_id;
			
          	ob_start();
			$this->all_chat_rooms_ajax_pagination();
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['room_ajax_content'] = $content_obj->html;
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
		   
		   	
            # view file...
            $VIEW = "logged/chat/chat_rooms.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }   
    
	 public function all_chat_rooms_ajax_pagination($page=0)
    {
		
		## show_type: video rooms
		## memberonly : private rooms = 1
		
		$s_where = 'WHERE C.enable = 1 AND C.member_only = 0 AND C.show_type = 0 ';
		## seacrh conditions : filter ############
		 $WHERE_COND = '';
		   if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) :
			 
			  $room_parent_cat = trim($this->input->post('cat'));
			  $WHERE_COND .= ($room_parent_cat =='-1')?'':" AND ( RC.i_parent_cat_id = ".$room_parent_cat." )";
			  
			  $room_sub_cat = trim($this->input->post('sub_cat'));
			  $WHERE_COND .= ($room_sub_cat =='-1')?'':" AND ( RC.i_cat_id = ".$room_sub_cat." )"; 
			  
			  
			  $this->session->set_userdata('search_condition',$WHERE_COND);
		  
		endif;  
		## seacrh conditions : filter ############
		if($this->session->userdata('search_condition') != '')
			$s_where .= $this->session->userdata('search_condition');
		
		
				
		$cur_page = $page + $this->pagination_per_page;
		$data = $this->data;
		
		$order_by = "C.`room_id` DESC ";
		$result = $this->chat_rooms_model->get_list($s_where,$page,$this->pagination_per_page,$order_by);
		
		//echo $this->db->last_query(); 
		$total_rows = $this->chat_rooms_model->get_list_count($s_where);
		//pr($result,1);
		//pr($result,1);
		$data['arr_chat_rooms'] = $result;
		$data['no_of_result'] = $total_rows; 
        $data['current_page_1'] = $cur_page;
		$data['profile_id'] = $i_user_id;
		
		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->pagination_per_page)
			  $view_more = false;
         //--------- end check
		
		
		$VIEW_FILE = "logged/chat/chat_room_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$content, 'current_page'=>$cur_page, 'no_of_result'=>$data['no_of_result'],'total'=>$total_rows,'view_more'=>$view_more ,'cur_page'=>$data['current_page_1']) );
			
	}
	
 public function generateSubCat($id)
 {
	 $this->load->model('chat_categories_model');
	 $wh = " AND subcat.i_parent_category = ".$id;
	 $cat_arr = $this->chat_categories_model->getCategory( $wh);
	// pr($cat_arr,1);
	 
	 $html = '<div style="width:120px;text-align:left;" class="lable02"> Select Sub Category :  </div>      
				   <div class="">
						<select style="width: 215px;" id="sub_cat" name="sub_cat">
						<option value="-1">Select Sub Category</option>
						   ';
	 if(count($cat_arr)){
		 foreach($cat_arr as $k=> $val){
			  $html .= '<option value="'.$val['id'].'">'.$val['s_category_name'].'</option>';
		 }
	 }
	  $html .= '</select></div><div class="clr"></div>';
 	 echo json_encode(array('room_arr'=>$room_arr, 'html'=>$html)); 
	 exit;
 }
	
	
    public function my_chat_room() 
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
                                        'js/lightbox.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',*/
										'js/production/tweet_utilities.js',
										'js/production/events_helper.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css') );
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			
			$this->session->set_userdata('search_condition','');
			 
			$data['pagination_per_page'] = $this->pagination_per_page;
			$data['profile_id'] = $i_user_id;
			
          	ob_start();
			$this->my_chat_rooms_ajax_pagination();
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['room_ajax_content'] = $content_obj->html;
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
		   
		   	
            # view file...
            $VIEW = "logged/chat/my_chat_rooms.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }   
    
	 public function my_chat_rooms_ajax_pagination($page=0)
    {
		
		## show_type: video rooms
		## memberonly : private rooms = 1
		$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
		
		$s_where = ' AND 1= 1 ';
		## seacrh conditions : filter ############
		 $WHERE_COND = '';
		   if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) :
			 
			  $room_parent_cat = trim($this->input->post('cat'));
			  $WHERE_COND .= ($room_parent_cat =='-1')?'':" AND ( RC.i_parent_cat_id = ".$room_parent_cat." )";
			  
			  $room_sub_cat = trim($this->input->post('sub_cat'));
			  $WHERE_COND .= ($room_sub_cat =='-1')?'':" AND ( RC.i_cat_id = ".$room_sub_cat." )"; 
			  
			  
			  $this->session->set_userdata('search_condition',$WHERE_COND);
		  
		endif;  
		## seacrh conditions : filter ############
		if($this->session->userdata('search_condition') != '')
			$s_where .= $this->session->userdata('search_condition');
		
		
				
		$cur_page = $page + $this->pagination_per_page;
		$data = $this->data;
		
		$order_by = "C.`room_id` DESC ";
		$result = $this->chat_rooms_model->my_chat_room_list($s_where,$page,$this->pagination_per_page,$order_by);
		
		//echo $this->db->last_query(); 
		$total_rows = $this->chat_rooms_model->my_chat_room_list_count($s_where);
		//pr($result,1);
		//pr($result,1);
		$data['arr_chat_rooms'] = $result;
		$data['no_of_result'] = $total_rows; 
        $data['current_page_1'] = $cur_page;
		$data['profile_id'] = $i_user_id;
		
		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->pagination_per_page)
			  $view_more = false;
         //--------- end check
		
		
		$VIEW_FILE = "logged/chat/my_chat_room_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$content, 'current_page'=>$cur_page, 'no_of_result'=>$data['no_of_result'],'total'=>$total_rows,'view_more'=>$view_more ,'cur_page'=>$data['current_page_1']) );
			
	}
	

   public function create_chat_room() 
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
                                        'js/lightbox.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',*/
										'js/production/tweet_utilities.js',
										'js/production/events_helper.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css') );
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			
			$this->session->set_userdata('search_condition','');
			 
			$data['pagination_per_page'] = $this->pagination_per_page;
			$data['profile_id'] = $i_user_id;
			
			
			$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
			
			## FETCHING FRIENDS ###
			
			$WHERE = " WHERE 
						1
						AND c.s_status = 'accepted' 
						AND u.i_status=1 
						AND
						((c.i_requester_id = '".$i_profile_id."' AND u.id=c.i_accepter_id ) 
						OR (c.i_accepter_id = '".$i_profile_id."' AND u.id=c.i_requester_id ))  GROUP BY u.id "	;	
			  
			$ORDER_BY = "u.s_first_name ASC";
			$contacts = $this->contacts_model->fetch_multi_online_friends($WHERE, null, null,$ORDER_BY);	
			//pr($contacts); 
			
			#echo $this->db->last_query();
			$exclude_id_csv = '';
			$exclude_id_csv .= $i_profile_id.', ';
			$exclude_id_arr = $this->contacts_model->get_friendsId_by_user_id($i_profile_id);
                       // pr($exclude_id_arr);
			if(count($exclude_id_arr)){
					
				$exclude_id_csv .= implode(', ',$exclude_id_arr);
			}
			
			## FETCHING PRAYER PARTNERS ###
			$PRAYERPARTNER_WHERE = " WHERE 
										1
										AND c.s_status = 'accepted' 
										AND u.i_status=1 
										AND
										((c.i_requester_id = '".$i_profile_id."' AND u.id=c.i_accepter_id ) 
										OR (c.i_accepter_id = '".$i_profile_id."' AND u.id=c.i_requester_id ))
										AND u.id NOT IN (".$exclude_id_csv.") 
										GROUP BY u.id "	;	
			  
			$ORDER_BY = "u.s_first_name ASC";	
				  
			$prayer_partners = $this->my_prayer_partner_model->fetch_multi_online_friends($PRAYERPARTNER_WHERE,null,null,$ORDER_BY);
			//pr($prayer_partners);
			#echo $this->db->last_query();
			
			$exclude_id_PP_arr = $this->my_prayer_partner_model->get_prayerPartnerId_by_user_id($i_profile_id);
			if(count($exclude_id_PP_arr)){
					$exclude_id_csv .= ', ';
					$exclude_id_csv .= implode(', ',$exclude_id_PP_arr);
			}
			
			//echo $exclude_id_csv;
			
			## FETCHING NETPALS ###
			$total_where =  " WHERE 1
                                    AND u.i_status=1 
                                    AND ((c.i_requester_id = ".$i_profile_id." AND u.id=c.i_accepter_id) 
                                        OR 
                                    (c.i_accepter_id=".$i_profile_id." AND u.id=c.i_requester_id))
                                    AND c.s_status='accepted' 
									AND u.id NOT IN (".$exclude_id_csv.")
									GROUP BY u.id " ;
			
			$ORDER_BY = "u.s_first_name ASC";
			
             
            $netpals = $this->netpals_model->fetch_multi_online_netpals($total_where, null,null,$ORDER_BY);	
			
			$total_contact_arr = array();
			
			
			$contact_arr = array_merge($contacts,$prayer_partners);
			$total_contact_arr =  array_merge($contact_arr,$netpals);
			array_sort_by_column($total_contact_arr, 's_displayname');

			$data['contacts'] = $total_contact_arr;//$contacts;
			
		   	
            # view file...
            $VIEW = "logged/chat/create_chat_room.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    } 
	
	public function add_room()
	{
        $this->load->model('user_notifications_model');
			$arr_messages = array();
				
			# error message trapping...
			if( trim($this->input->post('room_name'))=='') 
			{
					$arr_messages['room_name'] = "* Required Field.";
			}
                        if( trim($this->input->post('date_to1'))=='') 
			{
					$arr_messages['date_to1'] = "* Required Field.";
			}
			/*if( trim($this->input->post('frnds'))=='' || trim($this->input->post('netpal'))=='' ||  trim($this->input->post('pp'))=='' ) 
			{
					$arr_messages['invitation'] = "* Required Field.";
			}*/
		
			/*if( trim($this->input->post('sel_cat'))=='-1') 
			{
					$arr_messages['sel_cat'] = "* Required Field.";
			}
			*/
			
			//$inv_frnds				=  //pr($inv_frnds,1);
			$arr_frnd	            = $this->input->post('frnds');
			//$inv_netpal				= ;
			$arr_netpal	            = $this->input->post('netpal');
			//$inv_pp				= $this->input->post('pp');
			$arr_pp	            	= $this->input->post('pp');

			if(empty($arr_frnd)  && empty($arr_netpal) && empty($arr_pp)){
				$arr_messages['invitation'] = "* Required Field.";
			}


			if(empty($arr_frnd))
			{
				$arr_frnd=array(0=>'0');
				#pr($arr_frnd);
			}
		
			//$arr_frnd=explode(',',$inv_frnds);
			//$arr_netpal=$this->input->post('frnd_type2');
			if(empty($arr_netpal))
			{
				$arr_netpal=array(0=>'0');
				
			}
			//$arr_netpal=explode(',',$inv_netpal);
			//$arr_pp=$this->input->post('frnd_type3');
			if(empty($arr_pp))
			{
				$arr_pp=array(0=>'0');
				
			}
			
			
			
			$complete_arr_frnd =  array();
			
			$contact_arr = array_merge($arr_frnd,$arr_netpal);
			$complete_arr_frnd =  array_merge($contact_arr,$arr_pp);
			$complete_arr_frnd = array_unique($complete_arr_frnd);
			$complete_arr_frnd=array_filter($complete_arr_frnd);
			#pr($complete_arr_frnd);
		   ## adding restriction
		   //pr($arr_messages);
			if( count($arr_messages)==0 ) {
				
				
				### add  chat room

				$host = "127.0.0.1";
				$port = 51127;
				$apiCommand = '<?xml version="1.0" encoding="UTF-8"?><Command group="default" api_pwd="3874-3459-9293-2194" type="add_room" name="'.trim($this->input->post('room_name')).'" passallmessage="false" member = "true" ><audio enable="0" /></Command>';
				
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
                                /***********add timing************************************/
                                $strt_time = trim($this->input->post('date_to1'));
				 $strt_time_arr = explode(' ',$strt_time);
				 $strt_time_str = $strt_time_arr[1].':00'; 
				 $day_arr = GetDays($strt_time);

                                $timestr = $strt_time_str;

                                $parts = explode(':', $timestr);
                               // pr($parts,1);

                                $seconds = ($parts[0] * 60 * 60) + ($parts[1] * 60) + $parts[2];
                                $after_thirty_min = $seconds+1800;
                                $end_time_str = gmdate("H:i:s", $after_thirty_min);
                                if(count($day_arr)){
                                  foreach($day_arr as $k=> $val1){
                                      
                                      $dt_start_time = $val1.' '.$strt_time_str;
                                      $dt_end_time = $val1.' '.$end_time_str;
                                           // $star_time_val = strtotime("'".$strt_time_str."'");
                                            //$end_time_str =   date("H:i",strtotime('+30 minutes',$star_time_val));
                                               //$time_html .= '<Time o="special" e="'.$val1.' '.$end_time_str.'" s="'.$val1.' '.$strt_time_str.'"></Time>';
                                        }
                                }
                                $time_arr = array();
                                $time_arr['i_chat_room_id'] = $new_chat_room_id;
                                $time_arr['user_id'] = intval(decrypt($this->session->userdata('user_id')));
                                $time_arr['dt_start_time'] = $dt_start_time;
                                $time_arr['dt_end_time'] = $dt_end_time;
                                $time_arr['s_type'] = 'Private chat room';
                                $data = $time_arr;

                               $this->db->insert('cg_privet_chat_room_timing', $data);
                                /**********************************************/
				/***********add all room table*************************************/
                               $all_room = array();
                               $all_room['i_chat_room_id'] = $new_chat_room_id;
                               $all_room['user_id'] = intval(decrypt($this->session->userdata('user_id')));
                               $all_room['dt_start_time'] = $dt_start_time;
                               $all_room['dt_end_time'] =  $dt_end_time;
                               $all_room['s_type'] = 2;
                               $data = $all_room;
                                $this->db->insert('cg_all_chat_room', $data);
                               /***********add all room table*************************************/
				### adding chat room :::: insertUsersChatRooms
					
				$info = array();
				
				$info['i_user_id'] 	= intval(decrypt($this->session->userdata('user_id')));	 
				$info['i_room_id'] 	= $new_chat_room_id;
				//$info['i_room_id'] 	=  intval(decrypt($this->session->userdata('user_id')))+1;
				$info['dt_created_on'] 	= get_db_datetime();
				$_ret = $this->chat_rooms_model->insertUsersChatRooms($info);
				#pr($_ret);
				//echo '=========================='.$this->db->chat_invitation;
				$room_id=$this->chat_rooms_model->get_room_by_id($_ret);
				//pr($room_id);
				insert_invitation($room_id['0']['id'],$_POST,'cg_chat_invitation','i_chat_id','chat');
				
				//pr($arr_frnd,1);
				## adding to invitation records
				#pr($complete_arr_frnd);
				if(count($complete_arr_frnd)){
					foreach($complete_arr_frnd as $recipient_id ) 
					{					
						$chat_invitation = array();
						$chat_invitation['i_owner_user_id'] = intval(decrypt($this->session->userdata('user_id')));
						$chat_invitation['i_room_id'] =  $new_chat_room_id;
						//$chat_invitation['i_room_id'] =  $room_id['0']['id'];
						$chat_invitation['i_user_id'] = $recipient_id;
						$chat_invitation['dt_created_on'] = get_db_datetime();
						$ret_id = $this->chat_rooms_model->InsertUsersChatRoomsInvitation($chat_invitation);
										
							$this->social_notifications_message( intval(decrypt($this->session->userdata('user_id'))), decrypt($val), 'user_chat_room_invitation', $new_chat_room_id) ;
							
							$notification_arr = array();
							$notification_arr['i_requester_id'] = decrypt($this->session->userdata('user_id'));
							$notification_arr['i_accepter_id'] =  $recipient_id;
							$notification_arr['s_type'] = 'user_chat_room_invitation';
							$notification_arr['dt_created_on'] = get_db_datetime();
							$ret = $this->user_notifications_model->insert($notification_arr);
					
					}
				}
				

				### add ring chat room
				
				### send mail  ###
				//$this->load->model('user_notifications_model');
				/*if($_ret)
				{	
					if(count($complete_arr_frnd)){
						foreach($complete_arr_frnd as $val)
						{				
							$this->social_notifications_message( intval(decrypt($this->session->userdata('user_id'))), decrypt($val), 'user_chat_room_invitation', $new_chat_room_id) ;
							
							$notification_arr = array();
							$notification_arr['i_requester_id'] = decrypt($this->session->userdata('user_id'));
							$notification_arr['i_accepter_id'] =  decrypt($val);
							$notification_arr['s_type'] = 'user_chat_room_invitation';
							$notification_arr['dt_created_on'] = get_db_datetime();
							$ret = $this->user_notifications_model->insert($notification_arr);
						}
					}
				}
				  */
				echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'Chat room created and invitation sent Successfully.') );
			}
			
			else
			{
				echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'error!') );
			}
		
		
		
	}
	
	
	function delete_information( $id)
    {
        //$id=$this->input->post('id');
        
       // $i_ret=$this->chat_rooms_model->delete_by_id($id);
	    $host = "127.0.0.1";
		$port = 51127;
		$apiCommand = '<?xml version="1.0" encoding="UTF-8"?><Command group="default" api_pwd="3874-3459-9293-2194" type="del_room" room_id="'.$id.'" />';
		$result = "";
		$resultDoc = "";
		$fp = @fsockopen($host, $port, $errno, $errstr, 2);
		if(!$fp)
		{
			#echo "Failed to excute api command,maybe host chat server is not started";
			echo json_encode(array('result'=>$result,'html'=>$html,'msg'=>'Failed to excute api command,maybe host chat server is not started'));
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
				#echo "xml parse error";
				echo json_encode(array('result'=>$result,'html'=>$html,'msg'=>'xml parse error'));
			}
			else
			{
				#print_r($values);
				xml_parser_free($parser);
				fclose($fp);
				ob_start();
				$this->my_chat_rooms_ajax_pagination();
				$html = ob_get_contents(); //pr($data['result_content']);
				ob_end_clean();
        		$result='success';
        		echo json_encode(array('result'=>$result,'html'=>$html));
			}
		}
		
		
    }
	
	
	public function my_private_room() 
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
                                        'js/lightbox.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',*/
										'js/production/tweet_utilities.js',
										'js/production/events_helper.js'
                                        ));
                                        
//            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
//                                          'css/dd.css') );
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			
			$this->session->set_userdata('search_condition','');
			 
			$data['pagination_per_page'] = $this->pagination_per_page;
			$data['profile_id'] = $i_user_id;
			
			$data['heading'] = "Private Rooms";
			
          	ob_start();
			$this->my_private_rooms_ajax_pagination();
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['room_ajax_content'] = $content_obj->html;
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
		   
		   	
            # view file...
            $VIEW = "logged/chat/my_chat_rooms.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }   
    
	 public function my_private_rooms_ajax_pagination($page=0)
    {
		
		## show_type: video rooms
		## memberonly : private rooms = 1
		$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
		
		$s_where = ' AND 1= 1 ';
		## seacrh conditions : filter ############
		 $WHERE_COND = '';
		   if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) :
			 
			  $room_parent_cat = trim($this->input->post('cat'));
			  $WHERE_COND .= ($room_parent_cat =='-1')?'':" AND ( RC.i_parent_cat_id = ".$room_parent_cat." )";
			  
			  $room_sub_cat = trim($this->input->post('sub_cat'));
			  $WHERE_COND .= ($room_sub_cat =='-1')?'':" AND ( RC.i_cat_id = ".$room_sub_cat." )"; 
			  
			  
			  $this->session->set_userdata('search_condition',$WHERE_COND);
		  
		endif;  
		## seacrh conditions : filter ############
		if($this->session->userdata('search_condition') != '')
			$s_where .= $this->session->userdata('search_condition');
		
		
				
		$cur_page = $page + $this->pagination_per_page;
		$data = $this->data;
		
		$order_by = "C.`room_id` DESC ";
		$result = $this->chat_rooms_model->my_private_room_list($s_where,$page,$this->pagination_per_page,$order_by);
		
		//echo $this->db->last_query(); 
		$total_rows = $this->chat_rooms_model->my_private_room_list_count($s_where);
		//pr($result,1);
		//pr($result,1);
		$data['arr_chat_rooms'] = $result;
		$data['no_of_result'] = $total_rows; 
        $data['current_page_1'] = $cur_page;
		$data['profile_id'] = $i_user_id;
		
		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->pagination_per_page)
			  $view_more = false;
         //--------- end check
		
		
		$VIEW_FILE = "logged/chat/my_chat_room_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$content, 'current_page'=>$cur_page, 'no_of_result'=>$data['no_of_result'],'total'=>$total_rows,'view_more'=>$view_more ,'cur_page'=>$data['current_page_1']) );
			
	}
	
	
	
	public function my_prayer_room() 
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
                                        'js/lightbox.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',*/
										'js/production/tweet_utilities.js',
										'js/production/events_helper.js'
                                        ));
                                        
//            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
//                                          'css/dd.css') );
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			
			$this->session->set_userdata('search_condition','');
			 
			$data['pagination_per_page'] = $this->pagination_per_page;
			$data['profile_id'] = $i_user_id;
			
          	ob_start();
			$this->my_prayer_rooms_ajax_pagination();
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['room_ajax_content'] = $content_obj->html;
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
		   
		   	
            # view file...
            $VIEW = "logged/chat/prayer_chat_rooms.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }   
    
	 public function my_prayer_rooms_ajax_pagination($page=0)
    {
		
		## show_type: video rooms
		## memberonly : private rooms = 1
		$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
		
		$s_where = ' AND 1= 1 ';
		## seacrh conditions : filter ############
		 $WHERE_COND = '';
		   if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) :
			 
			  $room_parent_cat = trim($this->input->post('cat'));
			  $WHERE_COND .= ($room_parent_cat =='-1')?'':" AND ( RC.i_parent_cat_id = ".$room_parent_cat." )";
			  
			  $room_sub_cat = trim($this->input->post('sub_cat'));
			  $WHERE_COND .= ($room_sub_cat =='-1')?'':" AND ( RC.i_cat_id = ".$room_sub_cat." )"; 
			  
			  
			  $this->session->set_userdata('search_condition',$WHERE_COND);
		  
		endif;  
		## seacrh conditions : filter ############
		if($this->session->userdata('search_condition') != '')
			$s_where .= $this->session->userdata('search_condition');
		
		
				
		$cur_page = $page + $this->pagination_per_page;
		$data = $this->data;
		
		$order_by = "C.`room_id` DESC ";
		$result = $this->chat_rooms_model->my_prayer_room_list($s_where,$page,$this->pagination_per_page,$order_by);
		
		//echo $this->db->last_query(); 
		$total_rows = $this->chat_rooms_model->my_prayer_room_list_count($s_where);
		//pr($result,1);
		//pr($result,1);
		$data['arr_chat_rooms'] = $result;
		$data['no_of_result'] = $total_rows; 
        $data['current_page_1'] = $cur_page;
		$data['profile_id'] = $i_user_id;
		
		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->pagination_per_page)
			  $view_more = false;
         //--------- end check
		
		
		$VIEW_FILE = "logged/chat/prayer_chat_room_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$content, 'current_page'=>$cur_page, 'no_of_result'=>$data['no_of_result'],'total'=>$total_rows,'view_more'=>$view_more ,'cur_page'=>$data['current_page_1']) );
			
	}
	
	
	
	public function my_ring_room() 
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
                                        'js/lightbox.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',*/
										'js/production/tweet_utilities.js',
										'js/production/events_helper.js'
                                        ));
                                        
//            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
//                                          'css/dd.css') );
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			
			$this->session->set_userdata('search_condition','');
			 
			$data['pagination_per_page'] = $this->pagination_per_page;
			$data['profile_id'] = $i_user_id;
			$data['heading'] = "Ring Rooms";
			
			
          	ob_start();
			$this->my_ring_rooms_ajax_pagination();
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['room_ajax_content'] = $content_obj->html;
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
		   
		   	
            # view file...
            $VIEW = "logged/chat/my_chat_rooms.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }   
    
	 public function my_ring_rooms_ajax_pagination($page=0)
    {
		
		## show_type: video rooms
		## memberonly : private rooms = 1
		$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
		
		$s_where = ' AND 1= 1 ';
		## seacrh conditions : filter ############
		 $WHERE_COND = '';
		   if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) :
			 
			  $room_parent_cat = trim($this->input->post('cat'));
			  $WHERE_COND .= ($room_parent_cat =='-1')?'':" AND ( RC.i_parent_cat_id = ".$room_parent_cat." )";
			  
			  $room_sub_cat = trim($this->input->post('sub_cat'));
			  $WHERE_COND .= ($room_sub_cat =='-1')?'':" AND ( RC.i_cat_id = ".$room_sub_cat." )"; 
			  
			  
			  $this->session->set_userdata('search_condition',$WHERE_COND);
		  
		endif;  
		## seacrh conditions : filter ############
		if($this->session->userdata('search_condition') != '')
			$s_where .= $this->session->userdata('search_condition');
		
		
				
		$cur_page = $page + $this->pagination_per_page;
		$data = $this->data;
		
		$order_by = "C.`room_id` DESC ";
		$result = $this->chat_rooms_model->my_ring_room_list($s_where,$page,$this->pagination_per_page,$order_by);
		
		//echo $this->db->last_query(); 
		$total_rows = $this->chat_rooms_model->my_ring_room_list_count($s_where);
		//pr($result,1);
		//pr($result,1);
		$data['arr_chat_rooms'] = $result;
		$data['no_of_result'] = $total_rows; 
        $data['current_page_1'] = $cur_page;
		$data['profile_id'] = $i_user_id;
		
		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->pagination_per_page)
			  $view_more = false;
         //--------- end check
		
		
		$VIEW_FILE = "logged/chat/ring_chat_room_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$content, 'current_page'=>$cur_page, 'no_of_result'=>$data['no_of_result'],'total'=>$total_rows,'view_more'=>$view_more ,'cur_page'=>$data['current_page_1']) );
			
	}
	
	
	function edit_info($id)
    {
        $arr_messages=array();
        if($_POST)
        {
            $id= intval($this->input->post('i_edit_id'));
            
            if(trim($this->input->post('txt_edit_title')) =='')
            {
                $arr_messages['edit_title'] = "* Required Field.";
               
            }
		
				
            if(count($arr_messages)==0)
            {
               
                $info=array();
                $info['name'] = get_formatted_string(trim($this->input->post('txt_edit_title'))); 
				
						  $host = "127.0.0.1";
						  $port = 51127;
						  $apiCommand = '<?xml version="1.0" encoding="UTF-8"?><Command group="default" api_pwd="3874-3459-9293-2194" type="edit_room"  roomid="'.$id.'" name="'.$info['name'].'" owner="flashchat" desc="'.$info['des'].'" max="'.$info['max_user'].'" sequence = "'.$sequence.'" passallmessage="false"><audio enable="0" /></Command>';
						  $result = "";
						  $resultDoc = "";
						  $fp = @fsockopen($host, $port, $errno, $errstr, 2);
						  if(!$fp)
						  {
						 	
						  	echo json_encode(array('result'=>'failure',
				 						'arr_messages'=>$arr_messages,'msg'=>'Failed to excute api command,maybe host chat server is not started'));
										exit;
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
							  	 echo "xml parse error";
							  }
							  else
							  {
								 // print_r($values);
								  xml_parser_free($parser);
								  fclose($fp);
								  
								  ### update in cat tabl .. roomid and category
								  $cat_arr = array();
								  
								  $cat_arr['i_cat_id'] = trim($this->input->post('edit_sel_cat'));
								  $cat_arr['i_parent_cat_id'] = getChatParentCatID(trim($this->input->post('edit_sel_cat')));
								  $this->chat_categories_model->update_cat($cat_arr, $id);
								  
								  
								  ob_start();
								  $this->ajax_pagination();
								  $html = ob_get_contents(); //pr($data['result_content']);
								  ob_end_clean();
								  echo json_encode(array('result'=>'success',
				 						'msg'=>'Chat room updated successfully!','html'=> $html));
								  
								  
							  }
						  }
						 
						  
		
            }
            else
            {
                 echo json_encode(array('result'=>'failure',
				 						'arr_messages'=>$arr_messages));
            }
        }
        else
        {
            $room_arr = $this->chat_rooms_model->get_by_id($id);
			
			$wh = " WHERE  cat.i_room_id =  ".$id;
			$cat_id = $this->chat_categories_model->get_category_id($wh); 
			
			$cat_html = '<select style="width:350px;" name="edit_sel_cat" id="edit_sel_cat">
                                  <option value="-1"></option>'.
                               makeOptionChatCategory('', $cat_id).'
                         </select>';
			echo json_encode(array('room_arr'=>$room_arr, 'cat_html'=>$cat_html)); 
        }
        
    }
    
    
    
	public function change_status()
	 {
			
			$data = $this->data;
			$page  =  intval($this->input->post('cur_page'));
			$i_status = intval($this->input->post('i_status'));
			$cur_status = intval($this->input->post('cur_status'));
			$ID = $this->input->post('record_id');
			
			
			
			if($this->session->userdata('user_id') !="")
			{
				$this->chat_rooms_model->change_status($i_status,$ID);
				if($i_status==1)
				   {
					 
						$action_txt =
							 '<input name="" title="ENABLE" type="button" class="btn-06" onclick="javascript:changeStatus(\''.$ID.'\',\'0\',\''.$i_status.'\')"  value="DISABLE"/>';
					
				   }
				 else if($i_status==0)
				   {
						$action_txt =
							 '<input name="" title="DISABLE" type="button" class="btn-06" onclick="javascript:changeStatus(\''.$ID.'\',\'1\',\''.$i_status.'\')"  value="ENABLE"/>';
					
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
                
                echo json_encode(array('result'=>'success',
                					   'u_id'=>$ID,
									   'action_txt' =>$action_txt,
									   'i_status' => $cur_status,
                					   'msg'=>$SUCCESS_MSG ,'redirect'=>false));
	 }
     
	 
	 public function edit_chat_room($room_id) 
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
                                        'js/lightbox.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',*/
										'js/production/tweet_utilities.js',
										'js/production/events_helper.js'
                                        ));
                                        
//            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
//                                          'css/dd.css') );
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			
			$this->session->set_userdata('search_condition','');
			 
			$data['pagination_per_page'] = $this->pagination_per_page;
			$data['profile_id'] = $i_user_id;
			$data['mode'] = 'Edit';
			
			$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
			
			### get invited user list ###
			
			$invited_users = $this->chat_rooms_model->getAllUserId_private_chat_room($room_id);
			//pr($invited_users,1);
			
			$INV_STR = implode(',',$invited_users);
			
			## FETCHING FRIENDS ###
			$exclude_id_csv =  $i_user_id ;

			if(count($exclude_id_arr)){
				$exclude_id_csv .= ','.$INV_STR;
			}
			
			$WHERE = " WHERE 
						1
						AND c.s_status = 'accepted' 
						AND u.i_status=1 
						AND
						((c.i_requester_id = '".$i_profile_id."' AND u.id=c.i_accepter_id ) 
						OR (c.i_accepter_id = '".$i_profile_id."' AND u.id=c.i_requester_id ))  
						AND u.id NOT IN (".$exclude_id_csv.") 
						GROUP BY u.id "	;	
			  
			$ORDER_BY = "u.s_first_name ASC";
			$contacts = $this->contacts_model->fetch_multi_online_friends($WHERE, null, null,$ORDER_BY);	
			//pr($contacts); 
			
			#echo $this->db->last_query();
			$exclude_id_csv .= $i_profile_id;			

			$exclude_id_arr = $this->contacts_model->get_friendsId_by_user_id($i_profile_id);
			if(count($exclude_id_arr)){
				$exclude_id_csv	.= ', ';
				$exclude_id_csv .= implode(', ',$exclude_id_arr);
			}
			
			## FETCHING PRAYER PARTNERS ###
			$PRAYERPARTNER_WHERE = " WHERE 
										1
										AND c.s_status = 'accepted' 
										AND u.i_status=1 
										AND
										((c.i_requester_id = '".$i_profile_id."' AND u.id=c.i_accepter_id ) 
										OR (c.i_accepter_id = '".$i_profile_id."' AND u.id=c.i_requester_id ))
										AND u.id NOT IN (".$exclude_id_csv.") 
										GROUP BY u.id "	;	
			  
			$ORDER_BY = "u.s_first_name ASC";	
				  
			$prayer_partners = $this->my_prayer_partner_model->fetch_multi_online_friends($PRAYERPARTNER_WHERE,null,null,$ORDER_BY);
			//pr($prayer_partners);
			#echo $this->db->last_query();
			
			$exclude_id_PP_arr = $this->my_prayer_partner_model->get_prayerPartnerId_by_user_id($i_profile_id);
			if(count($exclude_id_PP_arr)){
					$exclude_id_csv .= ', ';
					$exclude_id_csv .= implode(', ',$exclude_id_PP_arr);
			}
			
			//echo $exclude_id_csv;
			
			## FETCHING NETPALS ###
			$total_where =  " WHERE 1
                                    AND u.i_status=1 
                                    AND ((c.i_requester_id = ".$i_profile_id." AND u.id=c.i_accepter_id) 
                                        OR 
                                    (c.i_accepter_id=".$i_profile_id." AND u.id=c.i_requester_id))
                                    AND c.s_status='accepted' 
									AND u.id NOT IN (".$exclude_id_csv.")
									GROUP BY u.id " ;
			
			$ORDER_BY = "u.s_first_name ASC";
			
             
            $netpals = $this->netpals_model->fetch_multi_online_netpals($total_where, null,null,$ORDER_BY);	
			
			$total_contact_arr = array();
			
			
			$contact_arr = array_merge($contacts,$prayer_partners);
			$total_contact_arr =  array_merge($contact_arr,$netpals);
			array_sort_by_column($total_contact_arr, 's_displayname');



			$data['chat_invited']=$this->chat_rooms_model->get_chat_room_invited($room_id);
			$data['r']=array_filter($chat_invited);
			
			$data['contacts'] = $total_contact_arr;//$contacts;
			
			
			$data['chat_details'] = $this->chat_rooms_model->get_by_id($room_id);
			
			//pr($data['chat_details']);
		   	
            # view file...
            $VIEW = "logged/chat/create_chat_room.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    } 
	
	public function edit_room($id)
	{
        	$this->load->model('user_notifications_model');
			$arr_messages = array();
				
			# error message trapping...
			if( trim($this->input->post('room_name'))=='') 
			{
					$arr_messages['room_name'] = "* Required Field.";
			}
			//$inv_frnds				= $this->input->post('frnds'); //pr($inv_frnds,1);
			$arr_frnd	            = $this->input->post('frnds');
			//$inv_netpal				= ;
			$arr_netpal	            = $this->input->post('netpal');
			//$inv_pp				= $this->input->post('pp');
			$arr_pp	            = $this->input->post('pp');

			if(empty($arr_frnd)  && empty($arr_netpal) && empty($arr_pp)){
				$arr_messages['invitation'] = "* Required Field.";
			}

			if(empty($arr_frnd))
			{
				$arr_frnd=array(0=>'0');
				#pr($arr_frnd);
			}
			
			//$arr_frnd=explode(',',$inv_frnds);
			//$arr_netpal=$this->input->post('frnd_type2');
			if(empty($arr_netpal))
			{
				$arr_netpal=array(0=>'0');
				
			}
			//$arr_netpal=explode(',',$inv_netpal);
			//$arr_pp=$this->input->post('frnd_type3');
			if(empty($arr_pp))
			{
				$arr_pp=array(0=>'0');
				
			}
			$complete_arr_frnd =  array();
			
			$contact_arr = array_merge($arr_frnd,$arr_netpal);
			$complete_arr_frnd =  array_merge($contact_arr,$arr_pp);
			$complete_arr_frnd = array_unique($complete_arr_frnd);
			$complete_arr_frnd= array_filter($complete_arr_frnd);
			$invited_people=get_invited($id,'cg_chat_invitation','i_chat_id');
			## adding restriction
		   //pr($arr_messages);
			if( count($arr_messages)==0 ) {
				
				
				### add  chat room
			$host = "127.0.0.1";
				$port = 51127;
				$apiCommand = '<?xml version="1.0" encoding="UTF-8"?><Command group="default" api_pwd="3874-3459-9293-2194" type="edit_room"  roomid="'.$id.'" name="'.trim($this->input->post('room_name')).'"   passallmessage="false" member = "true"></Command>';
				
				
				$result = "";
				$resultDoc = "";
				$fp = @fsockopen($host, $port, $errno, $errstr, 2);
				if(!$fp)
				{
				  
				  echo json_encode(array('result'=>'failure',
							  'arr_messages'=>$arr_messages,'msg'=>'Failed to excute api command,maybe host chat server is not started'));
							  exit;
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
					   echo "xml parse error";
					}
					else
					{
					   // print_r($values);
						xml_parser_free($parser);
						fclose($fp);
						
						### update in cat tabl .. roomid and category
						### adding chat room :::: insertUsersChatRooms
					
				
						## adding to invitation records
						insert_invitation($id,$_POST,'cg_chat_invitation','i_chat_id','chat');
						$invited=get_invited($id,$this->db->chat_invitation,'i_chat_id');
				//pr($arr_frnd,1);
				## adding to invitation records
				#pr($complete_arr_frnd);
				$i=0;
				$inv_user=array();
				foreach($invited as $inv)
				{
					$inv_user[$i]=$inv['user_id'];
					$i++;
				}
				#pr($inv_user);
				if(count($complete_arr_frnd))
				{
					
					foreach($complete_arr_frnd as $recipient_id ) 
					{	
							
							if(!in_array($recipient_id,$inv_user))
							{		
								$chat_invitation = array();
								$chat_invitation['i_owner_user_id'] = intval(decrypt($this->session->userdata('user_id')));
								//$chat_invitation['i_room_id'] =  $new_chat_room_id;
								$chat_invitation['i_room_id'] =  $id;
								$chat_invitation['i_user_id'] = $recipient_id;
								$chat_invitation['dt_created_on'] = get_db_datetime();
								$ret_id = $this->chat_rooms_model->InsertUsersChatRoomsInvitation($chat_invitation);
								### add ring chat room
								
								### send mail  ###
								$this->social_notifications_message( intval(decrypt($this->session->userdata('user_id'))), $recipient_id, 'user_chat_room_invitation', $id) ;
											
											$notification_arr = array();
											$notification_arr['i_requester_id'] = decrypt($this->session->userdata('user_id'));
											/*$notification_arr['i_accepter_id'] =  decrypt($val)*/
											$notification_arr['i_accepter_id'] =  $recipient_id;
											$notification_arr['s_type'] = 'user_chat_room_invitation';
											$notification_arr['dt_created_on'] = get_db_datetime();
											$ret = $this->user_notifications_model->insert($notification_arr);
							}
						
					}
					
				}
				

						
		
						### add ring chat room
						
						### send mail  ###
						/*$this->load->model('user_notifications_model');
						if($_ret)
						{	
							if(count($complete_arr_frnd)){
								
								foreach($complete_arr_frnd as $val)
								{				
									$this->social_notifications_message( intval(decrypt($this->session->userdata('user_id'))), decrypt($val), 'user_chat_room_invitation', $new_chat_room_id) ;
									
									$notification_arr = array();
									$notification_arr['i_requester_id'] = decrypt($this->session->userdata('user_id'));
									$notification_arr['i_accepter_id'] =  decrypt($val);
									$notification_arr['s_type'] = 'user_chat_room_invitation';
									$notification_arr['dt_created_on'] = get_db_datetime();
									$ret = $this->user_notifications_model->insert($notification_arr);
								}
							}
						}*/
						  
						echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'Chat room updated and invitation sent Successfully.') );
						
						
					}
				
				
				}
			}
			else
			{
				echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'error!') );
			}
		
		
		
	}
    

}   // end of controller...

