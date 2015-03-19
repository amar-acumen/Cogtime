<?php
include_once(APPPATH.'models/base_model.php');
class System_reminder_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

	
	
	
	
	public function get_by_type_id($reminder_id,  $s_where, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			  $sql = 'SELECT * FROM '.$this->db->SYSTEM_REMINDER.'  WHERE i_reminder_id = "'.$reminder_id.'" 
			  				 
							 {$s_where} ORDER BY id DESC ';
		}
		else {
			 $sql = 'SELECT * FROM '.$this->db->SYSTEM_REMINDER.'  WHERE i_reminder_id = "'.$reminder_id.'" 
			 				
							{$s_where} ORDER BY id DESC LIMIT '.$start_limit.', '.$no_of_page;
		}

		$query = $this->db->query($sql); //echo $this->db->last_query(); exit;
		$result_arr = $query->result_array();
		
		return $result_arr;
	}
	
	
	

	public function get_by_user_id($user_id,  $s_where, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			  $sql = 'SELECT * FROM '.$this->db->SYSTEM_REMINDER.'  WHERE i_user_id = "'.$user_id.'" 
			  				 
							 {$s_where} ORDER BY id DESC ';
		}
		else {
			 $sql = 'SELECT * FROM '.$this->db->SYSTEM_REMINDER.'  WHERE i_user_id = "'.$user_id.'" 
			 				
							{$s_where} ORDER BY id DESC LIMIT '.$start_limit.', '.$no_of_page;
		}

		$query = $this->db->query($sql); #echo $this->db->last_query(); exit;
		$result_arr = $query->result_array();
		
		return $result_arr;
	}
	

	public function insert($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->SYSTEM_REMINDER, $arr);# echo $this->db->last_query();
		return $this->db->insert_id();
	}
	
	

	public function update($arr=array(), $id, $reminder_type) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->SYSTEM_REMINDER, $arr, array('id'=>$id));
	}
	
	public function update_by_reminder_id($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->SYSTEM_REMINDER, $arr, array('i_reminder_id'=>$id));
	}
	
    public function get_last_count($id) {
		$sql = 'SELECT i_sent_mail_count FROM '.$this->db->SYSTEM_REMINDER.' WHERE id = "'.$id.'"';
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
		return $result_arr[0]['i_sent_mail_count'];
	}
	
	  public function get_last_mail_sent_time($id) {
		$sql = 'SELECT t_time_last_mail_sent FROM '.$this->db->SYSTEM_REMINDER.' WHERE id = "'.$id.'"';
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
		return $result_arr[0]['t_time_last_mail_sent'];
	}
	
	

}
