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
*/

include(APPPATH.'controllers/base_controller.php');

class Set_expired_room extends Base_controller {

	public function __construct() {
		try
		{
			parent::__construct();
			$this->load->model('chat_rooms_model');
			$this->load->model('prayer_group_model');
		}
		catch(Exception $err_obj)
		{
			show_error($err_obj->getMessage());
		}
	}


    public function index() {
		
	
	## fetch all chat rooms
		//$where = " WHERE C.member_only =1 ";
		//$chat_room_list = $this->chat_rooms_model->get_list($where);
		//mail('aradhana.online19@gmail.com', 'My Subject', 'wdkjwqkejkjed');
		
	    $sql    = " SELECT C.* 
					FROM flashchat.room C , cogtime.cg_prayer_grp_chat_room_invitation PR , cogtime.cg_prayer_group PG 
					WHERE C.enable = 1 AND C.member_only = 1 AND C.show_type = 0 AND C.room_id = PR.i_chat_room_id 
					AND PG.id = PR.i_group_id GROUP BY C.room_id";

        $query     = $this->db->query($sql);//echo nl2br($this->db->last_query());  exit;
        $chat_room_list = $query->result_array(); 
		//pr($chat_room_list);
		
		//exit;
		
		
		
		//pr($chat_room_list);
		if(count($chat_room_list )){
			foreach($chat_room_list  as $key=>$c_val){
				
				 	$chat_room_added = 	$this->chat_rooms_model->checkRoomID_InvitationTbl($c_val['room_id']);
				 if($chat_room_added == 'true'){
				  	$data['is_exists'] = $this->chat_rooms_model->checkExistenceChatRoom($c_val['room_id']);
                                       // pr($data['is_exists'],1);
				 }
				  if($data['is_exists']  == 'false'){	
					 
					
					  ##delete from cg_prayer_grp_chat_room_invitation
					$this->chat_rooms_model->delete_chat_invitation($c_val['room_id']);
					  
					  ###On -fly delete from room table.
					  
					  $host = "127.0.0.1";
					  $port = 51127;
					  $apiCommand = '<?xml version="1.0" encoding="UTF-8"?><Command group="default" api_pwd="3874-3459-9293-2194" type="del_room" room_id="'.$c_val['room_id'].'" />';
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
									
					}
				}
		}
		
			
		
 }
    
  
}

