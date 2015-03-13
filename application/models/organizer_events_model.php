<?php
include_once(APPPATH.'models/base_model.php');
class Organizer_events_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

	
	public function get() {
		$sql = 'SELECT * FROM '.$this->db->ORGANIZER_EVENT.' order by id desc';
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr;
	}
	
	
	public function get_by_id($id, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = sprintf('SELECT * FROM '.$this->db->ORGANIZER_EVENT.'  where id = %s',  $id);
		}
		else {
			$sql = sprintf('SELECT * FROM '.$this->db->ORGANIZER_EVENT.'  where id = %s limit %s, %s',  $id, $start_limit, $no_of_page);
		}

		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
		
		$new_array = array();
		
		$result_arr[0]['total_photo'] = $this->get_total_by_album_id($id);
		
		return $result_arr[0];
	}
	


 ### new created

	public function get_total_by_album_id($album_id) {
		$sql = sprintf("SELECT count(*) count FROM ".$this->db->USER_PHOTOS."  where  i_ORGANIZER_EVENT_id = '%s'", $album_id);
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}
	
	
	
	public function get_by_user_id($user_id, $s_where, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			  $sql = sprintf('SELECT * FROM '.$this->db->ORGANIZER_EVENT.'  WHERE i_user_id = %s  %s ORDER BY id DESC ',$user_id, $s_where);
		}
		else {
			 $sql = sprintf('SELECT * FROM '.$this->db->ORGANIZER_EVENT.'  WHERE i_user_id = %s %s ORDER BY id DESC LIMIT %s, %s', $user_id, $s_where,  $start_limit, $no_of_page);
		}

		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
		
		$new_array = array();
		
		if(count($result_arr) >0){
			foreach($result_arr as $key=> $val){ 
				$result_arr[$key]['event_type'] = ' [ E ] ';
			}
		}
		

		return $result_arr;
	}
	
	
	

	public function get_total_by_user_id($user_id) {
		$sql = sprintf("SELECT count(*) count FROM ".$this->db->ORGANIZER_EVENT."  where i_user_id = '%s'", $user_id);
		$query = $this->db->query($sql); 
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}
	
	

	public function insert($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->ORGANIZER_EVENT, $arr);# echo $this->db->last_query();
		return $this->db->insert_id();
	}
	
	

	public function update($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->ORGANIZER_EVENT, $arr, array('id'=>$id));
	}
	
	

	public function delete_by_id($id) {
	
	     $sql = sprintf( 'DELETE FROM '.$this->db->ORGANIZER_EVENT.' WHERE id=%s', $id );
		 $this->db->query($sql);
		 
		 ## delete associated photos
		 $photo_sql = sprintf( 'SELECT s_photo FROM '.$this->db->USER_PHOTOS.' WHERE i_photo_album_id =%s ', $id );
		 $photo_arr = $this->db->query($photo_sql)->result_array();
		 
		 $sql = sprintf( 'DELETE FROM '.$this->db->USER_PHOTOS.' WHERE i_photo_album_id =%s ', $id );
		 $this->db->query($sql);
		
		/*$sql = sprintf( 'DELETE FROM '.$this->db->MEDIA_MAIN_COMMENTS.' WHERE i_media_id=%s AND s_media_type = \'photo\'', $id );
		$this->db->query($sql);*/
		 return $photo_arr;
	}
	
	
	
	
	
	##### fetching user's pic per album 

	public function get_photos_by_album_id($album_id, $user_id,  $start_limit="", $no_of_page="") 
	{
		global $CI;
				
		if("$start_limit" == "") {
			$sql = 		sprintf("SELECT * FROM ".$this->db->USER_PHOTOS
						."  where  i_photo_album_id = '%s' AND i_user_id = '%s' ORDER BY i_order DESC ", $album_id ,$user_id);
		}
		else {
			$sql = 		sprintf("SELECT * FROM ".$this->db->USER_PHOTOS
				  		."  where  i_photo_album_id = '%s' AND i_user_id = '%s' ORDER BY i_order DESC LIMIT %s, %s"
				  		, $album_id, $user_id, $start_limit, $no_of_page);
		}
				
		$query = $this->db->query($sql); 
			#echo $this->db->last_query(); 
		$result_arr = $query->result_array();
		
		if(count($result_arr) > 0 ){
			
			$user_sql = sprintf("SELECT s_profile_photo FROM ".$this->db->USERS."  where  id = '%s'", $user_id);
			$query = $this->db->query($user_sql); 
			
			  foreach ($query->result() as $row)
			  {
				 $user_pic = $row->s_profile_photo;
			  }
			$query->free_result(); 
			
			$CI->load->model("utility_model");
			$s_where =  " WHERE i_photo_album_id = {$album_id}";
			foreach($result_arr as $key=>$val){

				#### Checking whether a profile photo is selected from user's photo album
						  if($val['s_photo'] == $user_pic ){
							  $result_arr[$key]['is_profile_pic'] = 'true';
						  }
						  else{
							  $result_arr[$key]['is_profile_pic'] = 'false';
						  }
				#### End of Checking whether a profile photo is selected from user's photo album
					
						$result_arr[$key]['image_rank'] = $CI->utility_model->RankingRowCreate($result_arr[$key]['i_order'],
																					   $result_arr[$key]['id'],
																					   $result_arr[$key]['i_photo_album_id'],
																					   $this->db->USER_PHOTOS,
																					   $s_where);
																					   
					
			}
			
		} 
      // pr($result_arr,1);
		return $result_arr;
	}
	
	#Getting The Max Order 
	public function get_i_order($i_album_id)
	{
		try
		{
		  $ret_=0;
		  $s_qry =  "SELECT IFNULL(MAX(i_order),1) AS `max_i_order` FROM "
					  .$this->db->USER_PHOTOS. " WHERE i_photo_album_id = {$i_album_id}";
		  $rs=$this->db->query($s_qry); 
		  #echo $this->db->last_query();
          $i_cnt=0;
          if(is_array($rs->result()))
          {
              foreach($rs->result() as $row)
              {
                  $ret_=intval($row->max_i_order); 
              }
              $rs->free_result();          
          }
          
          unset($s_qry,$rs,$row,$i_cnt,$s_where);
          return $ret_;
			
			
		}
		catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  
		
	}
	
	public function get_by_album_details_id($id, $s_where,  $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = sprintf('SELECT * FROM '.$this->db->ORGANIZER_EVENT.'  where id = %s %s ' ,  $id , $s_where);
		}
		else {
			$sql = sprintf('SELECT * FROM '.$this->db->ORGANIZER_EVENT.'  where id = %s  %s limit %s, %s',  $id, $s_where, $start_limit, $no_of_page);
		}

		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
		
		$new_array = array();
		$total= $this->get_total_photos_by_album_id($id, $s_where);
		if($total > 0){
			$result_arr[0]['total_photo']  = $total;
		}

		
		return $result_arr[0];
	}
	
	
	public function get_total_photos_by_album_id($album_id, $s_where) {
		$sql = sprintf("SELECT count(*) count FROM ".$this->db->USER_PHOTOS."  where  i_photo_album_id = '%s' %s ", $album_id, $s_where);
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}
	
}
