<?php
include_once(APPPATH.'models/base_model.php');
class Salvation_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

	public function get() {
		$sql = sprintf('SELECT * FROM '.$this->db->SALVATION_PRAYER.' order by id desc');
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
//pr($result_arr,1);
		return $result_arr;
	}
	
	
	
	public function get_by_id($id, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = sprintf('SELECT * FROM '.$this->db->SALVATION_PRAYER.'  where id = %s',  $id);
		}
		else {
			$sql = sprintf('SELECT * FROM '.$this->db->SALVATION_PRAYER.'  where id = %s limit %s, %s',  $id, $start_limit, $no_of_page);
		}

		$query = $this->db->query($sql); //echo $this->db->last_query(); exit;
		$result_arr = $query->result_array();

		return $result_arr[0];
	}
	
	

	public function insert_photo($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->SALVATION_PHOTO, $arr); #echo $this->db->last_query();
		return $this->db->insert_id();
	}
	
	

	public function update($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->SALVATION_PRAYER, $arr, array('id'=>$id));
		//echo $this->db->last_query(); exit;
	}
	

	public function delete_by_id($id) {
	    $sql = sprintf( 'DELETE FROM '.$this->db->SALVATION_PRAYER.' WHERE id=%s', $id );
		$this->db->query($sql);
	}
	
	public function get_photo_by_salvation_id($id) {
		
		$sql = sprintf('SELECT * FROM '.$this->db->SALVATION_PHOTO.'  where i_salavation_id = %s',  $id);
		
		$query = $this->db->query($sql);//echo $this->db->last_query(); exit;
		$result_arr = $query->result_array();
		return $result_arr;
	}
	
	public function update_photo($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->SALVATION_PHOTO, $arr, array('id'=>$id));
	}
	
	public function delete_photo_by_id($id) {
	    $sql = sprintf( 'DELETE FROM '.$this->db->SALVATION_PHOTO.' WHERE id=%s', $id );
		$this->db->query($sql);
	}
	
	
}
