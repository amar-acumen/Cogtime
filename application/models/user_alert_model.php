<?php
include_once(APPPATH.'models/base_model.php');
class User_alert_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

	public function get() {
		$sql = 'SELECT * FROM '.$this->db->USER_ALERTS.' order by id desc';
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
//pr($result_arr,1);
		return $result_arr;
	}
	
	
	
	public function get_by_user_id($id, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = 'SELECT * FROM '.$this->db->USER_ALERTS.'  where i_user_id = "'.$id.'"';
		}
		else {
			$sql = sprintf('SELECT * FROM '.$this->db->USER_ALERTS.'  where i_user_id = %s limit %s, %s',  $id, $start_limit, $no_of_page);
		}

		$query = $this->db->query($sql); #echo $this->db->last_query(); exit;
		$result_arr = $query->result_array();

		return $result_arr[0];
	}
	
	

	public function insert($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->USER_ALERTS, $arr); //echo $this->db->last_query();exit;
		return $this->db->insert_id();
	}
	
	

	public function update($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->USER_ALERTS, $arr, array('i_user_id'=>$id)); //echo $this->db->last_query();
	}
	

		
	public function change_status($status ,$id) {
		
	  if($status !='' && $id !=''){	
		  $sql = sprintf( "UPDATE {$this->db->USER_ALERTS}  SET `i_status` = '%s'
						   WHERE `id` ='%s'"
					  ,  $status, $id );
		  $this->db->query($sql);// echo $this->db->last_query();exit;
		  return true;
	  }
	}
	
	
	public function check_option_user_id($id) {
		
		$sql = sprintf('SELECT * FROM '.$this->db->USER_ALERTS.'  where i_user_id = %s ',  $id);

		$query = $this->db->query($sql); //echo $this->db->last_query(); 
		$result_arr = $query->result_array();

		return $result_arr[0];
	}
	
	
	public function get_privacy_settings($id)
	{
		$sql = "SELECT * FROM ".$this->db->PRIVACY_SETTINGS."  
					where i_user_id = '".$id."'"  ;

		$query = $this->db->query($sql); //echo $this->db->last_query(); 
		$result_arr = $query->result_array();
		foreach($result_arr as $val)
		{
			$returnarr[$val['s_section_name']]	= array('friend_privacy'=>$val['i_friend_privacy'],
														'netpal_privacy'=>$val['i_netpal_privacy'],
														'prayer_partner_privacy'=>$val['i_prayer_partner_privacy'],
														'ring_privacy'=>$val['i_ring_privacy'],
														'prayer_group_privacy'=>$val['i_prayer_group_privacy']);
		}
		return $returnarr;
	}
	public function privacy_setting($arr,$id,$section)
	{
		$sql = "SELECT COUNT(*) AS rowexist FROM ".$this->db->PRIVACY_SETTINGS."  
					where i_user_id = '".$id."' AND s_section_name='".$section."'"  ;

		$query = $this->db->query($sql); //echo $this->db->last_query(); 
		$result_arr = $query->result_array();
		
		if(count($arr)==0) {
			return null;
		}
		
		//echo $result_arr[0]['rowexist'].'#$#$'; exit;
		
		if($result_arr[0]['rowexist']>0)
		{	
			$this->db->update($this->db->PRIVACY_SETTINGS, $arr, array('i_user_id'=>$id,'s_section_name'=>$section)); 
			
		}
		else
			$this->db->insert($this->db->PRIVACY_SETTINGS, $arr); 
			//echo $this->db->last_query();
			//echo $this->db->last_query();
	
	}
	
	public function public_privacy_settings($id,$section)
	{
		#delete from privacy settings table
		
		$sql = "SELECT COUNT(*) AS rowexist FROM ".$this->db->PRIVACY_SETTINGS."  
					where i_user_id = '".$id."' AND s_section_name='".$section."'"  ;

		$query = $this->db->query($sql); //echo $this->db->last_query(); 
		$result_arr = $query->result_array();
		
		
		
		//echo $result_arr[0]['rowexist'].'#$#$'; exit;
		
		if($result_arr[0]['rowexist']<= 0){
			## insert 
			$arr= array();
			$arr['i_friend_privacy'] =0; 
			$arr['i_netpal_privacy'] =0;
			$arr['i_prayer_partner_privacy'] =0;
			$arr['i_ring_privacy'] = 0;
			$arr['i_prayer_group_privacy'] = 0;
			$arr['i_user_id'] = $id ;
			$arr['s_section_name'] = $section;
			$this->db->insert($this->db->PRIVACY_SETTINGS, $arr); 
		}
		else{
			$sql1 = "UPDATE ".$this->db->PRIVACY_SETTINGS." SET 
						i_friend_privacy=0,  
						i_netpal_privacy=0,
						i_prayer_partner_privacy=0,
						i_ring_privacy=0,
						i_prayer_group_privacy=0
						where i_user_id = '".$id."' AND s_section_name='".$section."'"  ;
			$this->db->query($sql1);
		}
		
		if($section=='photo')
		{
			$sql2 = "SELECT GROUP_CONCAT(id) AS albums  FROM ".$this->db->PHOTO_ALBUM."  
						where i_user_id = '".$id."'"  ;
			$query2	= $this->db->query($sql2);
			$result_arr = $query2->result_array();
			if($result_arr[0]['albums'])
			{
				$sql3 = "DELETE FROM ".$this->db->photoalbum_privacy."  
						where i_photo_album_id IN(".$result_arr[0]['albums'].")"  ;
				$this->db->query($sql3);
			}			
			
		}
		if($section=='audio')
		{
			$sql2 = "SELECT GROUP_CONCAT(id) AS albums  FROM ".$this->db->AUDIO_ALBUM."  
						where i_user_id = '".$id."'"  ;
			$query2	= $this->db->query($sql2);
			$result_arr = $query2->result_array();

			if($result_arr[0]['albums'])
			{
				$sql3 = "DELETE FROM ".$this->db->audioalbum_privacy."  
						where i_audio_album_id IN(".$result_arr[0]['albums'].")"  ;
				$this->db->query($sql3);
			}			
			
		}
		if($section=='video')
		{
			$sql2 = "SELECT GROUP_CONCAT(id) AS albums  FROM ".$this->db->VIDEO_ALBUM."  
						where i_user_id = '".$id."'"  ;
			$query2	= $this->db->query($sql2);
			$result_arr = $query2->result_array();

			if($result_arr[0]['albums'])
			{
				$sql3 = "DELETE FROM ".$this->db->videolbum_privacy."  
						where i_video_album_id IN(".$result_arr[0]['albums'].")"  ;
				$this->db->query($sql3);
			}			
			
		}
		
		if($section=='event')
		{
			$sql2 = "SELECT GROUP_CONCAT(id) AS albums  FROM ".$this->db->EVENTS."  
						where i_host_id = '".$id."'"  ;
			$query2	= $this->db->query($sql2);
			$result_arr = $query2->result_array();
			
			if($result_arr[0]['albums'])
			{
				$sql3 = "DELETE FROM ".$this->db->event_privacy."  
						where i_event_id IN(".$result_arr[0]['albums'].")"  ;
				$this->db->query($sql3);
			}			
			
		}

		
	}
	
	public function get_email_by_user_id($id) {
	
			$sql = sprintf('SELECT count(*) as count FROM '.$this->db->USER_EMAIL_ALERTS.'  where i_user_id = %s',  $id);
		

		$query = $this->db->query($sql); #echo $this->db->last_query(); exit;
		$result_arr = $query->result_array();
		if($result_arr['0']['count'] == '0')
		{
			$arr['i_user_id']=$id;
			$this->db->insert($this->db->USER_EMAIL_ALERTS,$arr);
		}
		
		$sql1 = sprintf('SELECT * FROM '.$this->db->USER_EMAIL_ALERTS.'  where i_user_id = %s',  $id);

		
		$query1 = $this->db->query($sql1); #echo $this->db->last_query(); exit;
		$result_arr1 = $query1->result_array();
		return $result_arr1[0];
	}
	
	public function insert_email($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->USER_EMAIL_ALERTS, $arr); //echo $this->db->last_query();exit;
		return $this->db->insert_id();
	}
	
	

	public function update_email($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->USER_EMAIL_ALERTS, $arr, array('i_user_id'=>$id)); //echo $this->db->last_query();
	}
	public function check_option_email_user_id($id) {
		$sql = sprintf('SELECT count(*) as count FROM '.$this->db->USER_EMAIL_ALERTS.'  where i_user_id = %s',  $id);
		

		$query = $this->db->query($sql); #echo $this->db->last_query(); exit;
		$result_arr = $query->result_array();
		if($result_arr['0']['count'] == '0')
		{
			$arr['i_user_id']=$id;
			$this->db->insert($this->db->USER_EMAIL_ALERTS,$arr);
		}
		$sql1 = sprintf('SELECT * FROM '.$this->db->USER_EMAIL_ALERTS.'  where i_user_id = %s ',  $id);

		$query1 = $this->db->query($sql1); //echo $this->db->last_query(); 
		$result_arr1 = $query1->result_array();

		return $result_arr1[0];
	}
}
