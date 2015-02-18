<?php
include_once(APPPATH.'models/base_model.php');
class User_photos_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

	public function get() {
		$sql = sprintf('SELECT * FROM '.$this->db->USER_PHOTOS.' order by id desc');
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr;
	}
	
	
	
	public function get_by_id($id, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = sprintf('SELECT * FROM '.$this->db->USER_PHOTOS.'  where  id = %s',  $id);
		}
		else {
			
			 $sql = sprintf('SELECT * FROM '.$this->db->USER_PHOTOS.'  where  id = %s limit %s, %s',  $id, $start_limit, $no_of_page);
		}

		$query = $this->db->query($sql); #echo $this->db->last_query(); exit;
		$result_arr = $query->result_array();

		return $result_arr[0];
	}
	
	
	

	public function get_by_user_id($user_id, $s_where="", $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = sprintf('SELECT * FROM '.$this->db->USER_PHOTOS.'  WHERE i_status =1 AND i_user_id = %s %s ORDER BY id DESC ',$user_id,$s_where);
		}
		else {
			 $sql = sprintf('SELECT * FROM '.$this->db->USER_PHOTOS
								.'  WHERE i_status =1 AND i_user_id = %s %s ORDER BY id DESC LIMIT %s, %s ', $user_id, $s_where, $start_limit, $no_of_page);
		}

		$query = $this->db->query($sql);
        
		$result_arr = $query->result_array();
		$this->load->model('media_comments_model');
		if(is_array($result_arr)){
			foreach($result_arr as $key=>$val){
				$result_arr[$key]['total_comments'] = $this->media_comments_model->get_total_by_newsfeed_id($val['id'],'photo');
			}
		}

		return $result_arr;
	}
	
	
	

	public function get_total_by_user_id($user_id, $s_where) {
		$sql = sprintf("SELECT count(*) count FROM ".$this->db->USER_PHOTOS."  where i_status =1 AND i_user_id = '%s' %s ", $user_id, $s_where);
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}
	
	
	
	

	public function insert($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->USER_PHOTOS, $arr);
		return $this->db->insert_id();
	}
	
	

	public function update($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->USER_PHOTOS, $arr, array('id'=>$id));
	}
	
	

	public function delete_by_id($id) {
	     $sql = sprintf( 'DELETE FROM '.$this->db->USER_PHOTOS.' WHERE id=%s', $id );

		$this->db->query($sql);
		
		/*$sql = sprintf( 'DELETE FROM '.$this->db->MEDIA_MAIN_COMMENTS.' WHERE i_media_id=%s AND s_media_type = \'photo\'', $id );

		$this->db->query($sql);*/
		
	}
	
	
	public function get_by_media_id($id, $s_where="", $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = sprintf('SELECT * FROM '.$this->db->USER_PHOTOS.'  WHERE i_status =1 AND  id = %s %s ORDER BY id DESC ',$id,$s_where);
		}
		else {
			 $sql = sprintf('SELECT * FROM '.$this->db->USER_PHOTOS
								.'  WHERE i_status =1 AND id = %s %s ORDER BY id DESC LIMIT %s, %s ', $id, $s_where, $start_limit, $no_of_page);
		}

		$query = $this->db->query($sql);
        
		$result_arr = $query->result_array();
		$this->load->model('media_comments_model');
		if(is_array($result_arr)){
			foreach($result_arr as $key=>$val){
				$result_arr[$key]['total_comments'] = $this->media_comments_model->get_total_by_newsfeed_id($val['id'],'photo');
			}
		}

		return $result_arr;
	}
	
	
	

	public function get_total_by_media_id($id, $s_where) {
		$sql = sprintf("SELECT count(*) count FROM ".$this->db->USER_PHOTOS."  where i_status =1 AND id = '%s' %s ", $id, $s_where);
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}
	
	
	### all photos with comments ###
	
	 public function get_allphotos_with_comments_by_user_id_($user_id, $s_where="", $start_limit="", $no_of_page="") {
		$logged_user_id = intval(decrypt($this->session->userdata('user_id')));
		if("$start_limit" == "") {
			$sql = sprintf("SELECT * FROM ".$this->db->USER_PHOTOS." AS up, {$this->db->photoalbum_privacy} AS pr 
							WHERE pr.i_photo_album_id=up.i_photo_album_id 
							AND pr.i_user_id='".$logged_user_id."' AND
							up.i_status =1 AND  up.i_user_id = %s %s ORDER BY id DESC ",$user_id,$s_where);
		}
		else {
			 $sql = sprintf("SELECT * FROM ".$this->db->USER_PHOTOS." AS up, {$this->db->photoalbum_privacy} AS pr 
								  WHERE pr.i_photo_album_id=up.i_photo_album_id 
									AND pr.i_user_id='".$logged_user_id."' AND
									up.i_status =1 AND up.i_user_id = %s %s ORDER BY id DESC LIMIT %s, %s ", $user_id, $s_where, $start_limit, $no_of_page);
		}

		$query = $this->db->query($sql); #echo $this->db->last_query();
        
		$result_arr = $query->result_array();
		$this->load->model('media_comments_model');
		if(is_array($result_arr)){
			foreach($result_arr as $key=>$val){
				$result_arr[$key]['total_comments'] = $this->media_comments_model->get_total_by_newsfeed_id($val['id'],'photo');
				$result_arr[$key]['comments_arr'] =  $this->media_comments_model->get_by_newsfeed_id($val['id'],'photo');
				
			}
		}
#pr($result_arr);
		return $result_arr;
	}
		
	public function get_total_allphotos_with_comments_by_user_id_($user_id, $s_where) {
		$sql = sprintf("SELECT count(*) count FROM ".$this->db->USER_PHOTOS." AS up, {$this->db->photoalbum_privacy} AS pr  
						where pr.i_photo_album_id=up.i_photo_album_id 
									AND pr.i_user_id='".$logged_user_id."' AND 
									up.i_status =1 AND  up.i_user_id = '%s' %s ", $user_id, $s_where);
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}
	
	
	
	
	
	### by mediad id ###
	public function get_user_details_by_media_id($media_id, $s_where="", $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = sprintf('SELECT * FROM '.$this->db->USER_PHOTOS.'  WHERE id = %s %s ORDER BY id DESC ',$media_id,$s_where);
		}
		else {
			 $sql = sprintf('SELECT * FROM '.$this->db->USER_PHOTOS
								.'  WHERE id = %s %s ORDER BY id DESC LIMIT %s, %s ', $media_id, $s_where, $start_limit, $no_of_page);
		}

		$query = $this->db->query($sql);
        
		$result_arr = $query->result_array();
		/*$this->load->model('media_comments_model');
		if(is_array($result_arr)){
			foreach($result_arr as $key=>$val){
				$result_arr[$key]['total_comments'] = $this->media_comments_model->get_total_by_newsfeed_id($val['id'],'photo');
			}
		}*/

		return $result_arr[0];
	}		
		

}
