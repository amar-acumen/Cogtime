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
* @link model/chat_rooms_model.php
* @link views/##
*/

class Chat_rooms extends Admin_base_Controller
{
	private $pagination_per_page= 10;
	private $post_pagination_per_page = 20;

    

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
            $this->load->model("chat_rooms_model");
            $this->load->model("chat_categories_model");

            $this->load->helper('common_option_helper.php');
              $this->load->helper('my_utility_helper.php');
           
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
            parent::_add_js_arr( array( 'js/lightbox.js',
										'js/jquery.dd.js',
										'js/jquery.form.js',
                                        'js/jquery/JSON/json2.js',
                                        'js/jquery.ui.datepicker.js',
									    'js/jquery-ui-timepicker-addon.js',
									     'js/backend/manage_chat_rooms.js') );
                                        
             parent::_add_css_arr( array('css/dd.css', 'css/jquery-ui-1.8.2.custom.css'
                                        ) );
            # adjusting header & footer sections [End]...
			$data['top_menu_selected'] = 3;
			$data['submenu'] = 7;
           
            
			// fetching data
			$WHERE_COND = " WHERE 1 ";
			$this->session->set_userdata('search_condition',$WHERE_COND);
			$page=0;
			$order_by = "C.`room_id` ASC ";
			
			ob_start();
            $this->ajax_pagination();
            $data['result_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
			
            
            # rendering the view file...
            $VIEW_FILE = "admin/social_hub/chat/chat_rooms.phtml";
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
		 	$WHERE_COND = '';
		   if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) :
			  
			  $WHERE_COND = " WHERE 1  ";
			 
			  $txt_chat_room_name = get_formatted_string(trim($this->input->post('txt_chat_room_name')));
			  $WHERE_COND .= ($txt_chat_room_name=='')?'':" AND ( C.name LIKE '%".$txt_chat_room_name."%' )";
			  
			  $txt_username = get_formatted_string(trim($this->input->post('txt_username')));
			  $WHERE_COND .= ($txt_username=='')?'':" AND ( concat(u.s_first_name,' ', u.s_last_name) LIKE '%".$txt_username."%'
															OR  u.s_chat_display_name LIKE '%".$txt_username."%')";
				if($this->input->post('create_time') != '')
				{					
			  $create_time = get_db_dateformat($this->input->post('create_time'));
			  $WHERE_COND .= ($create_time=='')?'':" AND  DATE_FORMAT((FROM_UNIXTIME(SUBSTRING(C.create_time,1,10))),'%Y-%m-%d') = '".$create_time."' ";
			  }
			  $room_status = $this->input->post('status');
                          if($room_status == 1){
                          $WHERE_COND .=($room_status=='')?'':"AND (C.user_numbers >= $room_status )";
                          }
                          if($room_status == 0){
                              $WHERE_COND .=($room_status=='')?'':"AND (C.user_numbers <= $room_status )";
                          }
                          if($room_status == ''){
                               $WHERE_COND .=($room_status=='')?'':'';
                          }
			  $room_type = trim($this->input->post('roomtype'));
                          $WHERE_COND .=($room_type=='')?'':"AND (AC.s_type = $room_type)";
			  $this->session->set_userdata('search_condition',$WHERE_COND);
		  
		  
		   endif;  
		    $s_where = $this->session->userdata('search_condition');
			
			$order_by = "C.`room_id` DESC ";
		   	$result = $this->chat_rooms_model->get_list($s_where,$page,$this->pagination_per_page,$order_by);
           
		    // pr($result);
			#echo $this->db->last_query(); exit;
			$total_rows = $this->chat_rooms_model->get_list_count($s_where);
			
			
			## end seacrh conditions : filter ############
			
			//pr($result,1);
			#Jquery Pagination Starts
           	$this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/social_hub/chat_rooms/ajax_pagination";
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

            $config['div'] = '#table_content'; /* Here #content is the CSS selector for target DIV */
            $config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
            $config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */

            $this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();
			$this->jquery_pagination->create_links();

            // getting   listing...
			$data['info_arr'] = $result;
			$data['no_of_result'] = $total_rows;
			$data['current_page'] = $page;
          
			# loading the view-part...
          echo  $this->load->view('admin/social_hub/chat/chat_room_ajax.phtml', $data,TRUE);
		 }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
    }
    
    
	
	function delete_information()
    {
        $id=$this->input->post('id');
        
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
				$this->ajax_pagination();
				$html = ob_get_contents(); //pr($data['result_content']);
				ob_end_clean();
        		$result='success';
        		echo json_encode(array('result'=>$result,'html'=>$html));
			}
		}
		
		
    }
    
	
	
	 public function add_info()
	 {
		try
		  {
			$arr_messages = array();
				  
			  if($_POST){
					# error message trapping...
						
					if(trim($this->input->post('txt_title'))=='') 
					{		
							$arr_messages['title'] = "* Required Field";
					}	
					
					if(trim($this->input->post('txt_desc'))=='') 
					{		
							$arr_messages['desc'] = "* Required Field";
					}
					
					if(trim($this->input->post('txt_max_user'))=='') 
					{		
							$arr_messages['max_user'] = "* Required Field";
					}	
					
					if(trim($this->input->post('sel_cat'))=='-1') 
					{		
							$arr_messages['cat'] = "* Required Field";
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
		
						
				   
					if( count($arr_messages)==0 ) {
						
							  $info = array();
							  $info['name'] = get_formatted_string($this->input->post('txt_title'));	
							  $info['des'] = get_formatted_string($this->input->post('txt_desc'));
							  $info['max_user'] = trim($this->input->post('txt_max_user')) ;
							   
							  $sequence = $this->chat_rooms_model->getMaxSequence();

//							  $end_time = trim($this->input->post('date_end1'));
//							  $end_time_arr = explode(' ',$end_time);
//							  $end_time_str = $end_time_arr[1].':00';
								
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
                                                          // die('ok');

                                                              
							 
                                                          $time_html = '';
								if(count($day_arr)){
									foreach($day_arr as $k=> $val){
                                                                            
                                                                        $dt_start_time = $val.' '.$strt_time_str;
                                                                        $dt_end_time = $val.' '.$end_time_str;
                                                                                     // $star_time_val = strtotime("'".$strt_time_str."'");
                                                                            //$end_time_str =   date("H:i",strtotime('+30 minutes',$star_time_val));
										//$time_html .= '<Time o="special" e="'.$val.' '.$end_time_str.'" s="'.$val.' '.$strt_time_str.'"></Time>';
									}
								}
							   
							  $host = "127.0.0.1";
							  $port = 51127;

							  ## periodic chat room
							  /*$apiCommand = '<?xml version="1.0" encoding="UTF-8"?><Command group="default" api_pwd="3874-3459-9293-2194" type="add_room" name="'.$info['name'].'" desc="'.$info['des'].'" max="'.$info['max_user'].'" audio="false" video="false" passallmessage="false" ><audio enable="0" />
							   <roomOpen>
								'.$time_html.'
								</roomOpen>
							  </Command>'; */

							  # normal chat room
							 $apiCommand = '<?xml version="1.0" encoding="UTF-8"?><Command group="default" api_pwd="3874-3459-9293-2194" type="add_room" name="'.$info['name'].'" desc="'.$info['des'].'" max="'.$info['max_user'].'" audio="false" video="false" passallmessage="false" ><audio enable="0" />
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
									  echo "xml parse error";
								  }
								  else
								  {
									  //print_r($values);
									  xml_parser_free($parser);
									  fclose($fp);
									  
									  ### insert in cat tabl .. roomid and category
									  $cat_arr = array();
									  
									  $cat_arr['i_room_id']  = $values[0]['attributes']['result'];
									  $cat_arr['i_cat_id'] = trim($this->input->post('sel_cat'));
									  $cat_arr['i_parent_cat_id'] = getChatParentCatID(trim($this->input->post('sel_cat')));
									  $this->chat_categories_model->insert_cat($cat_arr);
									  /****************insert cg_public_chat_room_timing************************************/
                                                                            $time_arr = array();
                                                                            $time_arr['i_chat_room_id'] = $values[0]['attributes']['result'];
                                                                            $time_arr['user_id'] = intval(decrypt($this->session->userdata('user_id')));
                                                                            $time_arr['dt_start_time'] = $dt_start_time;
                                                                            $time_arr['dt_end_time'] = $dt_end_time;
                                                                            $time_arr['s_type'] = 'Public chat room';
                                                                            
                                                                            $data = $time_arr;

                                                                           $this->db->insert('cg_public_chat_room_timing', $data);
                                                                           
                                                                           
                                                                           /***********add all room table*************************************/
                                                                            $all_room = array();
                                                                            $all_room['i_chat_room_id'] = $values[0]['attributes']['result'];
                                                                            $all_room['user_id'] = intval(decrypt($this->session->userdata('user_id')));
                                                                            $all_room['dt_start_time'] = $dt_start_time;
                                                                            $all_room['dt_end_time'] =  $dt_end_time;
                                                                            $all_room['s_type'] = 1;
                                                                            $data = $all_room;
                                                                             $this->db->insert('cg_all_chat_room', $data);
                                                                             
                                                                          /***********add all room table*************************************/
                                                                           
                                                                          /*********************insert cg_public_chat_room_timing end*******************************/
									    ob_start();
										$this->ajax_pagination();
										$html = base64_encode(ob_get_contents()); //pr($data['result_content']);
										ob_end_clean();
						  
						 			echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'New Chat room has been added successfully.','html'=>$html) );
								  }
							  }
							  
							   
					}
					else
					{
						echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'Error!') );
					}
		  
			  }
			  
		  }
			catch(Exception $err_obj)
			{
				show_error($err_obj->getMessage());
			}
	  }
	
	
	//================================= edit info ====================================
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
			
			if(trim($this->input->post('txt_edit_desc')) =='')
            {
                $arr_messages['edit_desc'] = "* Required Field.";
               
            }
			
			if(trim($this->input->post('txt_edit_max_user'))=='') 
			{		
					$arr_messages['edit_max_user'] = "* Required Field";
			}
			
			if(trim($this->input->post('edit_sel_cat'))=='-1') 
			{		
					$arr_messages['edit_sel_cat'] = "* Required Field";
			}	
				
            if(count($arr_messages)==0)
            {
               
                $info=array();
                $info['name'] = get_formatted_string(trim($this->input->post('txt_edit_title'))); 
				$info['des'] =  get_formatted_string(trim($this->input->post('txt_edit_desc'))); 
				$info['max_user'] = trim($this->input->post('txt_edit_max_user')) ;
               
				
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
							 '<input name="" title="Restricted" type="button" class="btn-06" onclick="javascript:changeStatus(\''.$ID.'\',\'0\',\''.$i_status.'\')"  value="DISABLE" style="background:#FFC200;"/>';
					
				   }
				 else if($i_status==0)
				   {
						$action_txt =
							 '<input name="" title="Restrict Access" type="button" class="btn-06" onclick="javascript:changeStatus(\''.$ID.'\',\'1\',\''.$i_status.'\')"  value="ENABLE"/>';
					
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
     
	 

    public function delete_expired_rooms(){

    }
    
    

    
}   // end of controller...