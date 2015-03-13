<?php
include_once(APPPATH.'models/base_model.php');
class Chat_rooms_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
		$this->chat_db = $this->load->database('flashchat', TRUE);
	}

	
	public function get() {
		$sql = 'SELECT * FROM '.$this->chat_db->CHAT_ROOM.' order by id desc';
		$query = $this->chat_db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr;
	}
	
	
	public function get_by_id($id, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = 'SELECT * FROM '.$this->chat_db->CHAT_ROOM.'  where room_id = "'.$id.'"';
		}
		else {
			$sql = 'SELECT * FROM '.$this->chat_db->CHAT_ROOM.'  where room_id = "'.$id.'" limit {$start_limit}, {$no_of_page}';
		}
		$query = $this->chat_db->query($sql);
		$result_arr = $query->result_array();
		
		return $result_arr[0];
	}
	


	public function insert($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->chat_db->insert($this->chat_db->CHAT_ROOM, $arr); //echo $this->db->last_query();
		return $this->chat_db->insert_id();
	}
	

	public function update($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		
		$SQL = "UPDATE room
			    SET  name = '{$arr['name']}' , des = '{$arr['des']}'".$condition." WHERE room_id = " .$id;
	    $query = $this->chat_db->query($SQL);
		//echo $this->db->last_query(); exit;
	}
	

	public function delete_by_id($id) {
	
		## deleting ring cateory 
	     $sql = 'DELETE FROM '.$this->chat_db->CHAT_ROOM.' WHERE room_id="'.$id.'"';
		 $this->chat_db->query($sql);
		#echo $this->db->last_query(); exit;
	}
	
	/*public function get_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY C.room_id DESC';
		
        $sql    = " SELECT C.*
					FROM {$this->chat_db->CHAT_ROOM} C 
					{$where}  {$s_order_by} {$limit}";

        $query     = $this->chat_db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		
        return $result_arr;
    }
	
    public function get_list_count($where='')
    {
        
        
        $sql    = "SELECT count(*) as i_total FROM {$this->chat_db->CHAT_ROOM} C 
					
				  	{$where} ";
        $query     = $this->chat_db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0]['i_total'];
    }*/
	
	public function get_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY C.room_id DESC';
		
        $sql    = " SELECT C.*,
						  FROM_UNIXTIME(C.create_time) as room_creation_dt, 
						  RC.i_cat_id, 
						  RC.i_parent_cat_id, 
						  CAT.s_category_name 
                                                 
					FROM flashchat.room C 
					LEFT JOIN cogtime.cg_room_cat RC ON C.room_id = RC.i_room_id 
                                         LEFT JOIN cogtime.cg_all_chat_room AC ON  C.room_id =  AC.i_chat_room_id  
					LEFT JOIN cogtime.cg_chat_category CAT ON CAT.id = RC.i_cat_id 
					LEFT JOIN cogtime.cg_user_chat_rooms cr ON cr.i_room_id = C.room_id
					LEFT JOIN cogtime.cg_users u ON u.id = cr.i_user_id
                                       
					{$where}  {$s_order_by} {$limit}";

        $result     = mysql_query($sql); #echo $sql; 
		
       //  $query->result_array(); 
	 
      for ($array = array(); $row = mysql_fetch_assoc($result); 
				isset($row[$key_column]) ? $array[$row[$key_column]] = $row : $array[] = $row);
	
       return $array;
				
    }
	
    public function get_list_count($where='')
    {
        
        		
		 $sql    = " SELECT count(*) as i_total FROM (
		             SELECT C.room_id 
					FROM flashchat.room C 
					LEFT JOIN cogtime.cg_room_cat RC ON C.room_id = RC.i_room_id 
                                         LEFT JOIN cogtime.cg_all_chat_room AC ON C.room_id = AC.i_chat_room_id
					LEFT JOIN cogtime.cg_chat_category CAT ON CAT.id = RC.i_cat_id 
					LEFT JOIN cogtime.cg_user_chat_rooms cr ON cr.i_room_id = C.room_id
					LEFT JOIN cogtime.cg_users u ON u.id = cr.i_user_id
                                       
					{$where}  ) as drvd_tbl";

        $query     = mysql_query($sql);
		$row = mysql_fetch_assoc($query); 
		//echo $row['i_total']; pr($row); exit;
        return $row['i_total'];
    }
	
	public function change_status($status ,$id) {
		
		  $sql = sprintf( "UPDATE {$this->chat_db->CHAT_ROOM} SET `enable` = '%s'
						   WHERE `room_id` ='%s'"
					  , $status, $id );
		  $this->chat_db->query($sql); //echo $this->db->last_query();exit;
		  return true;
	}
	
	
	public function add_room($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		
		$SQL = "INSERT INTO room
				SET name = '{$arr['name']}' 
				, des = '{$arr['des']}'
				, max_user = '{$arr['max_user']}' ";
		$this->chat_db->query($SQL);
		
		
		//$this->db->insert($this->db->CHAT_ROOM, $arr); //echo $this->db->last_query();
		return $this->chat_db->insert_id();
	}
	
	/* function to get maximum [in time of INSERT action] */
	  function getMaxSequence($where="")
	  {
		  $tbl = 'room';
		  
		  $SQL = "SELECT IFNULL(MAX(`sequence`)+1,1) AS `max_sequence` FROM ".$tbl." {$where}";
		  $query = $this->chat_db->query($SQL);
		  $rows = $query->row();

		  return $rows->max_sequence;
	  }
	  
	  ## adding to chat invitaion table
	  
	  public function InsertChatInvitation($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->CHAT_ROOM_INVITATION, $arr); //echo $this->db->last_query();
		return $this->db->insert_id();
	  }
	  
	  public function get_grpid_by_Chatroom_id($room_id){
		
		  $SQL = "SELECT i_group_id FROM ".$this->db->CHAT_ROOM_INVITATION." WHERE i_chat_room_id = '".$room_id."'";
									   
		  $query     = $this->db->query($SQL); //echo $this->db->last_query();
          $result_arr = $query->result_array(); //pr($result_arr);
          return $result_arr[0]['i_group_id'];
		  
	  }
	  
	   public function join_prayer_meeting($where,$arr,$msg_arr)
	   {
			$this->db->update($this->db->CHAT_ROOM_INVITATION,$arr,$where);
			$this->db->update('messages',  array('i_ended'=>'1'),$msg_arr );
			#echo $this->db->last_query(); exit;
			#exit;
	   }
	   
	   ## fetch Chat room details by group_id
	    public function get_Chatroom_grpid_by($grp_id){
		
		 $SQL = "SELECT i_chat_room_id FROM ".$this->db->CHAT_ROOM_INVITATION." WHERE i_group_id = '".$grp_id."'  ";
									   
		  $query     = $this->db->query($SQL); //echo $this->db->last_query(); 
          $result_arr = $query->result_array(); //pr($result_arr);
          return $result_arr[0]['i_chat_room_id'];
	  }
	   
	   
	   
	   public function checkExistenceChatRoom($room_id){
		   
                      /*************15-10-2014**************************************/
                             $user_id = intval(decrypt($this->session->userdata('user_id')));
                                 $query = $this->db->get_where('cg_users', array('id' => $user_id));
                                 foreach ($query->result() as $row)
                                    {
                                        $time_zone = $row->s_time;
                                    }
                                    $nz_time = new DateTime(null, new DateTimezone($time_zone));
                                    $current_user_time = $nz_time->format('H:i:s');
                                    // $current_user_time_stamp =  strtotime($current_user_time);
                                     
                      /***************************************************/
		  $current_date = date('Y-m-d H:i:s');
//		  $sql    = "   SELECT count(*) as count FROM {$this->db->CHAT_ROOM_INVITATION} C
//		  				WHERE C.i_chat_room_id = {$room_id} 
//						AND C.dt_end_time >= '{$current_date}'  
//				      ";
                                                
                           $sql    = "   SELECT count(*) as count FROM {$this->db->CHAT_ROOM_INVITATION} C
		  				WHERE C.i_chat_room_id = {$room_id} 
						AND C.start_time <= '{$current_user_time}' AND  C.end_time >= '{$current_user_time}'
				      ";
                                                
					
		  $query   = $this->db->query($sql); //echo $this->db->last_query();
                 // die('');
		  $result_arr = $query->result_array(); //pr($result_arr);
		  if($result_arr[0]['count'] >= 1){
			  return 'true';
		  }
		  else {
			  return 'false';
		  }
	   }
	   
	   
	   
	   
	   ## invited members can only see the chat room
	   public function membersJoining_PrayerMeet($room_id, $grp_id){
		   
		  $sql  = " SELECT C.i_user_id FROM {$this->db->CHAT_ROOM_INVITATION} C
		  				 WHERE C.i_chat_room_id = {$room_id} 
						 AND C.i_group_id = '{$grp_id}'  
				     ";
					
		  $query   = $this->db->query($sql); #echo $this->db->last_query();
		  $result_arr = $query->result_array(); //pr($result_arr);
		 
		  
		  $ret_arr = array();
		  if(count($result_arr)){
			  foreach($result_arr as $key=> $val){
				  $ret_arr[$key]= $val['i_user_id'];
			  }
		  }
		  // pr($ret_arr,1);
		  return $ret_arr;
	   }
	   
	   
	   
	 public function delete_chat_invitation($id) {
	
		## deleting ring cateory 
	     $sql = 'DELETE FROM '.$this->db->CHAT_ROOM_INVITATION.' WHERE i_chat_room_id="'.$id.'"';
		 $this->db->query($sql);
		#echo $this->db->last_query(); exit;
	}
	
	 public function checkRoomID_InvitationTbl($room_id){
		   
		  $sql    = "   SELECT count(*) as count FROM {$this->db->CHAT_ROOM_INVITATION} C
		  				WHERE C.i_chat_room_id = {$room_id} 
				      ";
					
		  $query   = $this->db->query($sql); //echo $this->db->last_query();
		  $result_arr = $query->result_array(); //pr($result_arr);
		  if($result_arr[0]['count'] >= 1){
			  return 'true';
		  }
		  else {
			  return 'false';
		  }
	   }
	   
	public function insertUsersChatRooms($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->user_chat_rooms, $arr); //echo $this->db->last_query();
		return $this->db->insert_id();
	}
	
	public function InsertUsersChatRoomsInvitation($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->users_chat_room_invitation, $arr); //echo $this->db->last_query();
		return $this->db->insert_id();
	}
	
	
	public function my_chat_room_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
		
		$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY C.room_id DESC';
		
        $sql    = "	(SELECT 
						C.*, CR.i_user_id as owner_id,
						'Y' is_owner
						FROM flashchat.room C 
						LEFT JOIN cogtime.cg_user_chat_rooms CR ON C.room_id = CR.i_room_id 
						WHERE C.enable = 1 AND C.member_only = 1 AND C.show_type = 0 
						AND CR.i_user_id = {$i_profile_id}
						{$where}
						{$s_order_by}
				    )
					UNION 
					(
							SELECT 
							C.*, 
							 CR.i_user_id as owner_id,
							  'N' is_owner
							FROM flashchat.room C 
							LEFT JOIN cogtime.cg_users_chat_room_invitation CR ON C.room_id = CR.i_room_id 
							WHERE  C.enable = 1 AND C.member_only = 1 AND C.show_type = 0 
							AND CR.i_user_id in
							(     SELECT CI.i_user_id from flashchat.room C1 , cogtime.cg_users_chat_room_invitation CI 
									where CI.i_room_id = C1.room_id AND CI.i_user_id = {$i_profile_id}
							) {$where} 
							{$s_order_by}				
											
					)   {$limit}";

        $query     = $this->db->query($sql);// echo nl2br($this->db->last_query());  exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
				
        return $result_arr;
    }
	
    public function my_chat_room_list_count($where='')
    {
        $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        
        $sql    = "SELECT count(*) as i_total FROM 
		            ( (SELECT 
						C.room_id
						FROM flashchat.room C 
						LEFT JOIN cogtime.cg_user_chat_rooms CR ON C.room_id = CR.i_room_id 
						WHERE C.enable = 1 AND C.member_only = 1 AND C.show_type = 0 
						AND CR.i_user_id = {$i_profile_id}
						{$where}
				    )
					UNION 
					(
							SELECT 
							C.room_id
							FROM flashchat.room C 
							LEFT JOIN cogtime.cg_users_chat_room_invitation CR ON C.room_id = CR.i_room_id 
							WHERE  C.enable = 1 AND C.member_only = 1 AND C.show_type = 0 
							AND CR.i_user_id in
							(     SELECT CI.i_user_id from flashchat.room C1 , cogtime.cg_users_chat_room_invitation CI 
									where CI.i_room_id = C1.room_id AND CI.i_user_id = {$i_profile_id}
							) {$where} 
					)
					
					) as drvd_tbl";
        $query     = $this->db->query($sql);// echo nl2br($this->db->last_query()); exit;
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0]['i_total'];
    }  
	
	
	#### ALL private chat rooms :: prayer room + ring + my rooms +invited rooms
	
	public function my_private_room_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
		
		$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY C.room_id DESC';
		
        $sql    = "	(SELECT 
						C.*, 
						 CR.i_user_id as owner_id,
						 'Y' is_owner
						FROM flashchat.room C 
						LEFT JOIN cogtime.cg_user_chat_rooms CR ON C.room_id = CR.i_room_id 
						WHERE C.enable = 1 AND C.member_only = 1 AND C.show_type = 0 
						AND CR.i_user_id = {$i_profile_id}
						{$where}
						{$s_order_by}
				    )
					UNION 
					(
							SELECT 
							C.*,
							CR.i_user_id as owner_id,
							'N' is_owner
							FROM flashchat.room C 
							LEFT JOIN cogtime.cg_users_chat_room_invitation CR ON C.room_id = CR.i_room_id 
							WHERE  C.enable = 1 AND C.member_only = 1 AND C.show_type = 0 
							AND CR.i_user_id in
							(     SELECT CI.i_user_id from flashchat.room C1 , cogtime.cg_users_chat_room_invitation CI 
									where CI.i_room_id = C1.room_id AND CI.i_user_id = {$i_profile_id}
							) {$where} 
							{$s_order_by}				
											
					)
					UNION
					( SELECT 
						C.*, 
						PR.i_user_id as owner_id,
						'Y' is_owner
						FROM flashchat.room C 
						LEFT JOIN cogtime.cg_prayer_grp_chat_room_invitation PR ON C.room_id = PR.i_chat_room_id 
						LEFT JOIN cogtime.cg_prayer_group PG ON PG.id = PR.i_group_id
						WHERE C.enable = 1 AND C.member_only = 1 AND C.show_type = 0 
						AND PG.i_owner_id = {$i_profile_id}
						{$where}
						Group by PG.id
						{$s_order_by}
					)
					UNION 
					(
							SELECT 
							C.*,
							PR.i_user_id as owner_id,
							'N' is_owner
							FROM flashchat.room C 
							LEFT JOIN cogtime.cg_prayer_grp_chat_room_invitation PR ON C.room_id = PR.i_chat_room_id 
							WHERE  C.enable = 1 AND C.member_only = 1 AND C.show_type = 0 
							AND PR.i_user_id in
							(     SELECT PR1.i_user_id from flashchat.room C1 , cogtime.cg_prayer_grp_chat_room_invitation PR1 
									where PR1.i_chat_room_id = C1.room_id AND PR1.i_user_id = {$i_profile_id}
							) {$where} 
							{$s_order_by}				
											
					)
					
					UNION
					( 
						SELECT 
						C.*, 
						RC.i_user_id as owner_id,
						'N' is_owner
						FROM flashchat.room C 
						LEFT JOIN cogtime.cg_ring_grp_chat_room_invitation RC ON C.room_id = RC.i_chat_room_id 
						WHERE C.enable = 1 AND C.member_only = 1 AND C.show_type = 0 
						AND RC.i_user_id = {$i_profile_id}
						{$where}
						{$s_order_by}
					)
					UNION 
					(
							SELECT 
							C.*,
							RC.i_user_id as owner_id,
							'N' is_owner
							FROM flashchat.room C 
							LEFT JOIN cogtime.cg_ring_grp_chat_room_invitation RC ON C.room_id = RC.i_chat_room_id 
							WHERE  C.enable = 1 AND C.member_only = 1 AND C.show_type = 0 
							AND RC.i_user_id in
							(     SELECT RC1.i_user_id from flashchat.room C1 , cogtime.cg_ring_grp_chat_room_invitation RC1 
									where RC1.i_chat_room_id = C1.room_id AND RC1.i_user_id = {$i_profile_id}
							) {$where} 
							{$s_order_by}				
											
					)
					 {$limit}";

        $query     = $this->db->query($sql); #echo nl2br($this->db->last_query());  exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
				
        return $result_arr;
    }
	
    public function my_private_room_list_count($where='')
    {
        $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        
        $sql    = "SELECT count(*) as i_total FROM 
		            (  
					(SELECT 
						C.*, 
						 CR.i_user_id as owner_id,
						 'Y' is_owner
						FROM flashchat.room C 
						LEFT JOIN cogtime.cg_user_chat_rooms CR ON C.room_id = CR.i_room_id 
						WHERE C.enable = 1 AND C.member_only = 1 AND C.show_type = 0 
						AND CR.i_user_id = {$i_profile_id}
						{$where}
				    )
					UNION 
					(
							SELECT 
							C.*,
							CR.i_user_id as owner_id,
							'N' is_owner
							FROM flashchat.room C 
							LEFT JOIN cogtime.cg_users_chat_room_invitation CR ON C.room_id = CR.i_room_id 
							WHERE  C.enable = 1 AND C.member_only = 1 AND C.show_type = 0 
							AND CR.i_user_id in
							(     SELECT CI.i_user_id from flashchat.room C1 , cogtime.cg_users_chat_room_invitation CI 
									where CI.i_room_id = C1.room_id AND CI.i_user_id = {$i_profile_id}
							) {$where} 
					)
					UNION
					( 
						SELECT 
						C.*, 
						PR.i_user_id as owner_id,
						'N' is_owner
						FROM flashchat.room C 
						LEFT JOIN cogtime.cg_prayer_grp_chat_room_invitation PR ON C.room_id = PR.i_chat_room_id 
						WHERE C.enable = 1 AND C.member_only = 1 AND C.show_type = 0 
						AND PR.i_user_id = {$i_profile_id}
						{$where}
					)
					UNION 
					(
							SELECT 
							C.*,
							PR.i_user_id as owner_id,
							'N' is_owner
							FROM flashchat.room C 
							LEFT JOIN cogtime.cg_prayer_grp_chat_room_invitation PR ON C.room_id = PR.i_chat_room_id 
							WHERE  C.enable = 1 AND C.member_only = 1 AND C.show_type = 0 
							AND PR.i_user_id in
							(     SELECT PR1.i_user_id from flashchat.room C1 , cogtime.cg_prayer_grp_chat_room_invitation PR1 
									where PR1.i_chat_room_id = C1.room_id AND PR1.i_user_id = {$i_profile_id}
							) {$where} 
					)
					
					UNION
					( 
						SELECT 
						C.*, 
						RC.i_user_id as owner_id,
						'N' is_owner
						FROM flashchat.room C 
						LEFT JOIN cogtime.cg_ring_grp_chat_room_invitation RC ON C.room_id = RC.i_chat_room_id 
						WHERE C.enable = 1 AND C.member_only = 1 AND C.show_type = 0 
						AND RC.i_user_id = {$i_profile_id}
						{$where}
					)
					UNION 
					(
							SELECT 
							C.*,
							RC.i_user_id as owner_id,
							'N' is_owner
							FROM flashchat.room C 
							LEFT JOIN cogtime.cg_ring_grp_chat_room_invitation RC ON C.room_id = RC.i_chat_room_id 
							WHERE  C.enable = 1 AND C.member_only = 1 AND C.show_type = 0 
							AND RC.i_user_id in
							(     SELECT RC1.i_user_id from flashchat.room C1 , cogtime.cg_ring_grp_chat_room_invitation RC1 
									where RC1.i_chat_room_id = C1.room_id AND RC1.i_user_id = {$i_profile_id}
							) {$where} 
											
					)
					 ) as drvd_tbl";
        $query     = $this->db->query($sql);// echo nl2br($this->db->last_query()); exit;
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0]['i_total'];
    } 
	
	
	#### ALL private chat rooms :: prayer room + ring + my rooms +invited rooms
	
	public function my_prayer_room_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
		
		$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY C.room_id DESC';
		
        $sql    = "	( SELECT 
						C.*, 
						PR.i_user_id as owner_id,
						'Y' is_owner,
						PR.dt_start_time,
						PR.dt_end_time,
						PG.s_group_name
						FROM flashchat.room C 
						LEFT JOIN cogtime.cg_prayer_grp_chat_room_invitation PR ON C.room_id = PR.i_chat_room_id 
						LEFT JOIN cogtime.cg_prayer_group PG ON PG.id = PR.i_group_id
						WHERE C.enable = 1 AND C.member_only = 1 AND C.show_type = 0 
						AND PG.i_owner_id = {$i_profile_id}
						{$where}
						Group by PG.id
						{$s_order_by}
					)
					UNION 
					(
							SELECT 
							C.*,
							PR.i_user_id as owner_id,
							'N' is_owner,
							PR.dt_start_time,
							PR.dt_end_time,
							PG.s_group_name
							FROM flashchat.room C 
							LEFT JOIN cogtime.cg_prayer_grp_chat_room_invitation PR ON C.room_id = PR.i_chat_room_id 
							LEFT JOIN cogtime.cg_prayer_group PG ON PG.id = PR.i_group_id
							WHERE  C.enable = 1 AND C.member_only = 1 AND C.show_type = 0 
							AND PR.i_user_id in
							(     SELECT PR1.i_user_id from flashchat.room C1 , cogtime.cg_prayer_grp_chat_room_invitation PR1 
									where PR1.i_chat_room_id = C1.room_id AND PR1.i_user_id = {$i_profile_id}
							) {$where} 
							{$s_order_by}				
											

					)
					
					
					 {$limit}";

        $query     = $this->db->query($sql);//echo nl2br($this->db->last_query());  exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
				
        return $result_arr;
    }
	
    public function my_prayer_room_list_count($where='')
    {
        $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        
        $sql    = "SELECT count(*) as i_total FROM 
		           (  
						( SELECT 
						C.*, 
						PR.i_user_id as owner_id,
						'Y' is_owner
						FROM flashchat.room C 
						LEFT JOIN cogtime.cg_prayer_grp_chat_room_invitation PR ON C.room_id = PR.i_chat_room_id 
						LEFT JOIN cogtime.cg_prayer_group PG ON PG.id = PR.i_group_id
						WHERE C.enable = 1 AND C.member_only = 1 AND C.show_type = 0 
						AND PG.i_owner_id = {$i_profile_id}
						{$where}
						Group by PG.id
						{$s_order_by}
					)
					UNION 
					(
							SELECT 
							C.*,
							PR.i_user_id as owner_id,
							'N' is_owner
							FROM flashchat.room C 
							LEFT JOIN cogtime.cg_prayer_grp_chat_room_invitation PR ON C.room_id = PR.i_chat_room_id 
							LEFT JOIN cogtime.cg_prayer_group PG ON PG.id = PR.i_group_id
							WHERE  C.enable = 1 AND C.member_only = 1 AND C.show_type = 0 
							AND PR.i_user_id in
							(     SELECT PR1.i_user_id from flashchat.room C1 , cogtime.cg_prayer_grp_chat_room_invitation PR1 
									where PR1.i_chat_room_id = C1.room_id AND PR1.i_user_id = {$i_profile_id}
							) {$where} 
							{$s_order_by}				
											
					)
					
				   ) as drvd_tbl";
        $query     = $this->db->query($sql);// echo nl2br($this->db->last_query()); exit;
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0]['i_total'];
    } 
	
	
	#### ALL private chat rooms :: prayer room + ring + my rooms +invited rooms
	
	public function my_ring_room_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
		
		$i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY C.room_id DESC';
		
        $sql    = "	( SELECT 
						C.*, 
						UR.i_user_id as owner_id,
						'Y' is_owner,
						UR.s_ring_name,
						CONCAT(u.s_first_name,' ', u.s_last_name) as s_profile_name
						FROM flashchat.room C 
						LEFT JOIN cogtime.cg_user_ring UR ON C.room_id = UR.i_room_id 
						LEFT JOIN cogtime.cg_ring_invited_user RC ON UR.id = RC.i_ring_id
						LEFT JOIN cogtime.cg_users u ON UR.i_user_id = u.id
						WHERE C.enable = 1 AND C.member_only = 1 AND C.show_type = 0 
						AND UR.i_user_id = {$i_profile_id}
						{$where}
					)
					UNION 
					(
							SELECT 
							C.*,
							UR.i_user_id as owner_id,
							'N' is_owner,
							UR.s_ring_name,
							CONCAT(u.s_first_name,' ', u.s_last_name) as s_profile_name
							FROM flashchat.room C 
							LEFT JOIN cogtime.cg_user_ring UR ON C.room_id = UR.i_room_id 
							LEFT JOIN cogtime.cg_ring_invited_user RC ON UR.id = RC.i_ring_id
							LEFT JOIN cogtime.cg_users u ON UR.i_user_id = u.id
							WHERE  C.enable = 1 AND C.member_only = 1 AND C.show_type = 0 
							AND RC.i_invited_id in
							(     SELECT  RC1.i_invited_id from flashchat.room C1 
										  LEFT JOIN cogtime.cg_user_ring UR1 ON C1.room_id = UR1.i_room_id 
										  LEFT JOIN cogtime.cg_ring_invited_user RC1 ON UR1.id = RC1.i_ring_id
									   where UR1.i_room_id = C1.room_id AND RC1.i_invited_id = {$i_profile_id}
							) {$where} 
											
					)
					 {$limit}";

        $query     = $this->db->query($sql);//echo nl2br($this->db->last_query());  exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
				
        return $result_arr;
    }
	
    public function my_ring_room_list_count($where='')
    {
        $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        
        $sql    = "SELECT count(*) as i_total FROM 
		           (  
				   	( SELECT 
						C.*, 
						UR.i_user_id as owner_id,
						'Y' is_owner,
						UR.s_ring_name
						FROM flashchat.room C 
						LEFT JOIN cogtime.cg_user_ring UR ON C.room_id = UR.i_room_id 
						LEFT JOIN cogtime.cg_ring_invited_user RC ON UR.id = RC.i_ring_id
						
						WHERE C.enable = 1 AND C.member_only = 1 AND C.show_type = 0 
						AND UR.i_user_id = {$i_profile_id}
						{$where}
					)
					UNION 
					(
							SELECT 
							C.*,
							UR.i_user_id as owner_id,
							'N' is_owner,
							UR.s_ring_name
							FROM flashchat.room C 
							LEFT JOIN cogtime.cg_user_ring UR ON C.room_id = UR.i_room_id 
							LEFT JOIN cogtime.cg_ring_invited_user RC ON UR.id = RC.i_ring_id
							
							WHERE  C.enable = 1 AND C.member_only = 1 AND C.show_type = 0 
							AND RC.i_invited_id in
							(     SELECT  RC1.i_invited_id from flashchat.room C1 
										  LEFT JOIN cogtime.cg_user_ring UR1 ON C1.room_id = UR1.i_room_id 
										  LEFT JOIN cogtime.cg_ring_invited_user RC1 ON UR1.id = RC1.i_ring_id
									   where UR1.i_room_id = C1.room_id AND RC1.i_invited_id = {$i_profile_id}
							) {$where} 
											
					)
				   ) as drvd_tbl";
        $query     = $this->db->query($sql);// echo nl2br($this->db->last_query()); exit;
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0]['i_total'];
    } 
	
	function getAllUserId_private_chat_room($room_id){
		
		$SQL = "SELECT CI.i_user_id 
					FROM flashchat.room C1 , cogtime.cg_users_chat_room_invitation CI 
					WHERE C1.room_id = CI.i_room_id
					";
		$query     = $this->db->query($SQL);// echo nl2br($this->db->last_query()); exit;
        $result_arr = $query->result_array(); //pr($result_arr);
		
		if(count($result_arr)){
			$res = array();
			foreach($result_arr as $k=> $val){
				$res[$k] = $val['i_user_id'];
			}
		}
		
        return $res;
		
	}
	
	
	 	function get_room_by_id($id)
		 {
			 $sql="SELECT i_room_id as id FROM cg_user_chat_rooms WHERE id=".$id;
			 $query =$this->db->query($sql);
			 $result_arr = $query->result_array();
			 #pr($result_arr);
			 return $result_arr;
		 }
	
	 function get_chat_room_invited($id)
	 {
		 	  $s_qry =  "SELECT * FROM cg_chat_invitation WHERE i_chat_id =".$id;
			  $rs=$this->db->query($s_qry); 
			  //echo $this->db->last_query();
			  foreach($rs->result() as $row)
			  {
				  
					$returnarr[$row->s_section][]	= $row->i_user_id;
			  }
			  return $returnarr;
	 }
	
}
