<?php
include_once(APPPATH.'models/base_model.php');
class Organizer_note_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

	
	public function get() {
		$sql = 'SELECT * FROM '.$this->db->ORGANIZER_NOTE.' order by id desc';
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr;
	}
	
	
	public function get_by_id($id, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = 'SELECT * FROM '.$this->db->ORGANIZER_NOTE.'  where id = "'.$id.'"',  ;
		}
		else {
			$sql = 'SELECT * FROM '.$this->db->ORGANIZER_NOTE.'  where id = "'.$id.'" limit '.$start_limit.', '.$no_of_page;
		}

		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
		
		return $result_arr[0];
	}
	


 ### new created

	
	
	
	public function get_by_user_id($user_id, $s_where, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			  $sql = 'SELECT * FROM '.$this->db->ORGANIZER_NOTE.'  WHERE i_user_id = "'.$user_id.'"  {$s_where} ORDER BY id DESC ';
		}
		else {
			  $sql = 'SELECT * FROM '.$this->db->ORGANIZER_NOTE.'  WHERE i_user_id = "'.$user_id.'" '.$s_where.' ORDER BY id DESC LIMIT '.$start_limit.', '.$no_of_page;
		}
		//echo $sql;exit;

		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
		$new_array = array();
		
		if(count($result_arr) >0){
			foreach($result_arr as $key=> $val){ 
				$result_arr[$key]['event_type'] = ' [ N ] ';
			}
		}
		

		return $result_arr;
	}
	
	
	

	public function get_total_by_user_id($user_id) {
		$sql = "SELECT count(*) count FROM ".$this->db->ORGANIZER_NOTE."  where i_user_id = '".$user_id."'";
		$query = $this->db->query($sql); 
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}
	
	

	public function insert($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->ORGANIZER_NOTE, $arr);# echo $this->db->last_query();
		return $this->db->insert_id();
	}
	
	

	public function update($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->ORGANIZER_NOTE, $arr, array('id'=>$id));
		//echo $this->db->last_query();
	}
	
	

	public function delete_by_id($id) {
	
	     $sql = 'DELETE FROM '.$this->db->ORGANIZER_NOTE.' WHERE id="'.$id.'"';
		 $this->db->query($sql);
		 
	}
	
	public function get_allNotes_by_user_id($user_id, $s_where, $s_bible_where, $start_limit="", $no_of_page="") {
	
	$sql = " (SELECT id , 
					   i_user_id, 
					   s_note AS s_description,
					   dt_created_date as dt_added,
					   'bible' as s_type
					   FROM cg_bible_note
					   WHERE i_user_id = {$user_id}
					   {$s_bible_where}
					   
				 )
				 UNION 
				 
				 (SELECT  id ,
				 		  i_user_id,
						   s_description AS s_description,
						   d_date as dt_added,
						   'pnote' as s_type
						  FROM cg_organizer_note
						  WHERE i_user_id = {$user_id}
						  {$s_where}
				) ORDER BY dt_added
				 
				"; //echo nl2br($sql);

		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
		$new_array = array();
		
		if(count($result_arr) >0){
			foreach($result_arr as $key=> $val){ 
				$result_arr[$key]['event_type'] = ' [ N ] ';
			}
		}
		
		//pr($result_arr,1);

		return $result_arr;
	}
	
	
	
}
