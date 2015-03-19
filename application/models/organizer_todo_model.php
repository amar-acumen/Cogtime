<?php
include_once(APPPATH.'models/base_model.php');
class Organizer_todo_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

	
	public function get() {
		$sql = 'SELECT * FROM '.$this->db->ORGANIZER_TO_DO_LIST.' order by id desc';
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr;
	}
	
	
	public function get_by_id($id, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = 'SELECT * FROM '.$this->db->ORGANIZER_TO_DO_LIST.'  where id = "'.$id.'"',  ;
		}
		else {
			$sql = 'SELECT * FROM '.$this->db->ORGANIZER_TO_DO_LIST.'  where id = "'.$id.'" limit '.$start_limit.', '.$no_of_page);
		}

		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
			
		return $result_arr[0];
	}
	


 ### new created

	
	
	public function get_by_user_id($user_id, $s_where, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			  $sql = 'SELECT * FROM '.$this->db->ORGANIZER_TO_DO_LIST.'  WHERE i_user_id = "'.$user_id.'"  {$s_where} ORDER BY id DESC ',, $s_where;
		}
		else {
			  $sql = 'SELECT * FROM '.$this->db->ORGANIZER_TO_DO_LIST.'  WHERE i_user_id = "'.$user_id.'" {$s_where} ORDER BY id DESC LIMIT '.$start_limit.', '.$no_of_page;
		}

		$query = $this->db->query($sql);
		echo $this->db->last_query; 
		$result_arr = $query->result_array();
		
		$new_array = array();
		
		if(count($result_arr) >0){
			foreach($result_arr as $key=> $val){ 
				$result_arr[$key]['event_type'] = ' [ D ] ';
			}
		}
		

		return $result_arr;
	}
	
	
	

	public function get_total_by_user_id($user_id) {
		$sql = "SELECT count(*) count FROM ".$this->db->ORGANIZER_TO_DO_LIST."  where i_user_id = '".$user_id."'";
		$query = $this->db->query($sql); 
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}
	
	

	public function insert($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->ORGANIZER_TO_DO_LIST, $arr); #echo $this->db->last_query(); exit;
		return $this->db->insert_id();
	}
	
	

	public function update($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->ORGANIZER_TO_DO_LIST, $arr, array('id'=>$id));//echo $this->db->last_query();
	}
	
	

	public function delete_by_id($id) {
	
	     $sql = 'DELETE FROM '.$this->db->ORGANIZER_TO_DO_LIST.' WHERE id="'.$id.'"';
		 $this->db->query($sql);
		 
		 ## delete associated photos
		 $photo_sql = 'SELECT s_photo FROM '.$this->db->USER_PHOTOS.' WHERE i_photo_album_id ="'.$id.'" ';
		 $photo_arr = $this->db->query($photo_sql)->result_array();
		 
		 $sql = 'DELETE FROM '.$this->db->USER_PHOTOS.' WHERE i_photo_album_id ="'.$id.'" ';
		 $this->db->query($sql);
		
		/*$sql = sprintf( 'DELETE FROM '.$this->db->MEDIA_MAIN_COMMENTS.' WHERE i_media_id=%s AND s_media_type = \'photo\'', $id );
		$this->db->query($sql);*/
		 return $photo_arr;
	}
	
	
	public function get_reminder_todo_text($id) {
		$sql = 'SELECT s_description FROM '.$this->db->ORGANIZER_TO_DO_LIST.' WHERE id = "'.$id.'"';
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
		return $result_arr[0]['s_description'];
	}
	
	
	public function get_reminder_todo_start_time($id) {
		$sql = 'SELECT t_display_start_time FROM '.$this->db->ORGANIZER_TO_DO_LIST.' WHERE id = "'.$id.'"';
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
		return $result_arr[0]['t_display_start_time'];
	}
	
	
	public function get_reminder_todo_end_time($id) {
		$sql = 'SELECT t_display_end_time FROM '.$this->db->ORGANIZER_TO_DO_LIST.' WHERE id = "'.$id.'"';
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
		return $result_arr[0]['t_display_end_time'];
	}	
	
}
