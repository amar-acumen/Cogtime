<?php
include_once(APPPATH.'models/base_model.php');
class User_notifications_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

	public function get() {
		$sql = sprintf('SELECT * FROM '.$this->db->NOTIFICATIONS.' order by id desc');
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr;
	}
	
	
	
	public function get_by_id($id, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = sprintf('SELECT * FROM '.$this->db->NOTIFICATIONS.'  where id = %s',  $id);
		}
		else {
			
			 $sql = sprintf('SELECT * FROM '.$this->db->NOTIFICATIONS.'  where id = %s limit %s, %s',  $id, $start_limit, $no_of_page);
		}

		$query = $this->db->query($sql); #echo $this->db->last_query(); exit;
		$result_arr = $query->result_array();

		return $result_arr[0];
	}
	
	
	

	public function get_by_user_id($user_id, $s_where="", $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = sprintf('SELECT * FROM '.$this->db->NOTIFICATIONS.'  WHERE i_accepter_id = %s %s ORDER BY id DESC ',$user_id,  $s_where);
		}
		else {
			 $sql = sprintf('SELECT * FROM '.$this->db->NOTIFICATIONS
								.'  WHERE i_accepter_id = %s %s ORDER BY id DESC LIMIT %s, %s ', $user_id, $s_where, $start_limit, $no_of_page);
		}

		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
		
		return $result_arr;
	}
	
	
	

	public function get_total_by_user_id($user_id, $s_where) {
		$sql = sprintf("SELECT count(*) count FROM ".$this->db->NOTIFICATIONS."  where i_accepter_id = '%s' %s ", $user_id, $s_where);
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}
	
	
	
	

	public function insert($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->NOTIFICATIONS, $arr);
		return $this->db->insert_id();
	}
	
	

	public function update($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->NOTIFICATIONS, $arr, array('id'=>$id));
	}
	
	

	public function delete_by_id($id) {
	     $sql = sprintf( 'DELETE FROM '.$this->db->NOTIFICATIONS.' WHERE id=%s', $id );

		$this->db->query($sql);
		
		/*$sql = sprintf( 'DELETE FROM '.$this->db->MEDIA_MAIN_COMMENTS.' WHERE i_media_id=%s AND s_media_type = \'photo\'', $id );

		$this->db->query($sql);*/
		
	}
	
	
		
}
