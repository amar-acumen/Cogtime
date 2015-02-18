<?php
include_once(APPPATH.'models/base_model.php');
class Audio_albums_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

	
	public function get() {
		$sql = sprintf('SELECT * FROM '.$this->db->AUDIO_ALBUM.' order by id desc');
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr;
	}
	
	
	public function get_by_id($id, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = sprintf('SELECT * FROM '.$this->db->AUDIO_ALBUM.'  where id = %s',  $id);
		}
		else {
			$sql = sprintf('SELECT * FROM '.$this->db->AUDIO_ALBUM.'  where id = %s limit %s, %s',  $id, $start_limit, $no_of_page);
		}
		

		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
		
		$new_array = array();
		
		$result_arr[0]['total_photo'] = $this->get_total_by_album_id($id);
		
		return $result_arr[0];
	}
	


 ### new created

	public function get_total_by_album_id($album_id) {
		$sql = sprintf("SELECT count(*) count FROM ".$this->db->USER_AUDIO."  where  i_id_audio_album = '%s'", $album_id);
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}
	
	
	
	public function get_by_user_id($user_id, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = sprintf('SELECT * FROM '.$this->db->AUDIO_ALBUM.'  WHERE i_user_id = %s ORDER BY id DESC ',$user_id);
		}
		else {
			$sql = sprintf('SELECT * FROM '.$this->db->AUDIO_ALBUM.'  WHERE i_user_id = %s ORDER BY id DESC LIMIT %s, %s', $user_id, $start_limit, $no_of_page);
		}

		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
		
		$new_array = array();
		
		if(count($result_arr) >0){
			foreach($result_arr as $key=> $val){ 
				$result_arr[$key]['total_audio'] = $this->get_total_by_album_id($val['id']);
			}
		}
		

		return $result_arr;
	}
	
	
	

	public function get_total_by_user_id($user_id) {
		$sql = sprintf("SELECT count(*) count FROM ".$this->db->AUDIO_ALBUM."  where i_user_id = '%s'", $user_id);
		$query = $this->db->query($sql); 
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}
	
	

	public function insert($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->AUDIO_ALBUM, $arr);//echo $this->db->last_query(); exit;
		return $this->db->insert_id();
	}
	
	

	public function update($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->AUDIO_ALBUM, $arr, array('id'=>$id));
	}
	
	

	public function delete_by_id($id) {
	
	     $sql = sprintf( 'DELETE FROM '.$this->db->AUDIO_ALBUM.' WHERE id=%s', $id );
			 $this->db->query($sql);
		 
		 ## delete associated photos
		 $audio_sql = sprintf( 'SELECT id, s_audio_file_name FROM '.$this->db->USER_AUDIO.' WHERE  i_id_audio_album =%s ', $id );
		 $audio_arr = $this->db->query($audio_sql)->result_array();
		 
		   $albums_comments_sql = sprintf( 'DELETE FROM %sUSER_MEDIA_COMMENTS WHERE i_media_id=%s AND s_media_type = "audio_album" ',
								 $this->db->dbprefix, $id);
		 $this->db->query($albums_comments_sql);
		
		  $like_album_sql = sprintf( "DELETE FROM %suser_media_like WHERE i_media_id=%s AND s_media_type = 'audio_album'" , $this->db->dbprefix, $id);
		  		 $this->db->query($like_album_sql); 
		 
		 ## delete associated comments
		 if(count($audio_arr) && is_array($audio_arr)){
			 foreach($audio_arr as $key=>$val){
				  $comments_sql = sprintf( 'DELETE FROM %sUSER_MEDIA_COMMENTS WHERE i_media_id=%s AND s_media_type = "audio" ',
								 $this->db->dbprefix, $val['id']);
				 $this->db->query($comments_sql);
				 
				# delete from like table #
				  $like_sql = sprintf( "DELETE FROM %suser_media_like WHERE i_media_id=%s AND s_media_type = 'audio'" , $this->db->dbprefix, $val['id']);
		  		 $this->db->query($like_sql); 
				 
				  $audio_sql = sprintf( 'DELETE FROM %sUSER_AUDIO WHERE  	i_id_audio_album=%s  ',
								 $this->db->dbprefix, $id);				 
				 $this->db->query($audio_sql);
				 
				
			 }
		 }
		// pr($audio_arr,1); 
		 
		// $sql = sprintf( 'DELETE FROM '.$this->db->USER_AUDIO.' WHERE i_id_audio_album =%s ', $id );
		// $this->db->query($sql);
		
		#DELETE PRIVACY
		$this->db->query("DELETE FROM {$this->db->audioalbum_privacy} WHERE i_audio_album_id='".$id."'");
		#DELETE PRIVACY
		
		
		return $audio_arr;
	}
	
	
	
	
	
	##### fetching user's pic per album 

	public function get_audios_by_album_id($album_id, $user_id,  $start_limit="", $no_of_page="") 
	{
		global $CI;
				
		if("$start_limit" == "") {
			$sql = 		sprintf("SELECT * FROM ".$this->db->USER_AUDIO
						."  where  i_id_audio_album = '%s' AND i_user_id = '%s' ORDER BY i_order DESC ", $album_id ,$user_id);
		}
		else {
			$sql = 		sprintf("SELECT * FROM ".$this->db->USER_AUDIO
				  		."  where  i_id_audio_album = '%s' AND i_user_id = '%s' ORDER BY i_order DESC LIMIT %s, %s"
				  		, $album_id, $user_id, $start_limit, $no_of_page);
		} 
				
		$query = $this->db->query($sql); 
		//echo $this->db->last_query();  exit;
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
			$CI->load->model('media_comments_model');
			$s_where =  " WHERE i_id_audio_album = {$album_id}";
			foreach($result_arr as $key=>$val){

								
						$result_arr[$key]['image_rank'] = $CI->utility_model->RankingRowCreate($result_arr[$key]['i_order'],
																					   $result_arr[$key]['id'],
																					   $result_arr[$key]['i_id_audio_album'],
																					   $this->db->USER_AUDIO,
																					   $s_where);
			     $result_arr[$key]['total_comments'] = $this->media_comments_model->get_total_by_newsfeed_id($val['id'],'audio');
																					   
					
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
					  .$this->db->USER_AUDIO. " WHERE i_id_audio_album = {$i_album_id}";
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
          $this->db->trans_commit();///new
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
			$sql = sprintf('SELECT * FROM '.$this->db->AUDIO_ALBUM.'  where id = %s %s ' ,  $id , $s_where);
		}
		else {
			$sql = sprintf('SELECT * FROM '.$this->db->AUDIO_ALBUM.'  where id = %s  %s limit %s, %s',  $id, $s_where, $start_limit, $no_of_page);
		}

		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
		
		$new_array = array();
		$total= $this->get_total_audios_by_album_id($id, $s_where);
		if($total > 0){
			$result_arr[0]['total_audio']  = $total;
		}

		
		return $result_arr[0];
	}
	
	
	public function get_total_audios_by_album_id($album_id, $s_where) {
		$sql = sprintf("SELECT count(*) count FROM ".$this->db->USER_AUDIO."  where  i_id_audio_album = '%s' %s ", $album_id, $s_where);
		$query = $this->db->query($sql);
		$result_arr = $query->result_array(); //echo $this->db->last_query(); exit;

		return $result_arr[0]['count'];
	}
	
	
	######## new for organize audio page ################3
	
	public function get_total_audios_albm_id_($album_id, $user_id) {
		$sql = sprintf("SELECT count(*) count FROM ".$this->db->USER_AUDIO."  where  i_id_audio_album = '%s' AND `i_user_id` = %s ", $album_id, $user_id);
		$query = $this->db->query($sql);
		$result_arr = $query->result_array(); //echo $this->db->last_query(); 

		return $result_arr[0]['count'];
	}
	
	public function get_privacy_settings_by_album_id($i_album_id)
	{
		 $s_qry =  "SELECT * FROM "
					  .$this->db->audioalbum_privacy. " WHERE i_audio_album_id = {$i_album_id}";
		  $rs=$this->db->query($s_qry); 
		  #echo $this->db->last_query();
		  foreach($rs->result() as $row)
		  {
			if($row->s_section=='Ring User' || $row->s_section=='Prayer Group')
				$returnarr[$row->s_section][$row->i_section_id][]	= $row->i_user_id;
			else
				$returnarr[$row->s_section][]	= $row->i_user_id;
		  }
          return $returnarr;
	}
	
	
	public function get_audio_album_with_privacy($user_id, $start_limit="", $no_of_page="") {
		$logged_user_id = intval(decrypt($this->session->userdata('user_id')));
		if("$start_limit" == "") {
			$sql = sprintf("SELECT * FROM {$this->db->audioalbum_privacy} AS ap ,{$this->db->AUDIO_ALBUM} AS a 
							 WHERE ap.i_audio_album_id=a.id AND ap.i_user_id='".$logged_user_id."'
								AND a.i_user_id = %s ORDER BY id DESC ",$user_id);
		}
		else {
			$sql = sprintf("SELECT * FROM {$this->db->audioalbum_privacy} AS ap ,{$this->db->AUDIO_ALBUM} AS a 
							 WHERE ap.i_audio_album_id=a.id AND ap.i_user_id='".$logged_user_id."'
							 AND a.i_user_id = %s ORDER BY id DESC LIMIT %s, %s", $user_id, $start_limit, $no_of_page);
		}

		$query = $this->db->query($sql);#echo $this->db->last_query();exit;
		$result_arr = $query->result_array();
		
		$new_array = array();
		
		if(count($result_arr) >0){
			foreach($result_arr as $key=> $val){ 
				$result_arr[$key]['total_audio'] = $this->get_total_by_album_id($val['id']);
			}
		}
		

		return $result_arr;
	}
	
	
	

	public function get_total_audio_album_with_privacy($user_id) {
		$sql = sprintf("SELECT count(*) AS count FROM {$this->db->audioalbum_privacy} AS ap , ".$this->db->AUDIO_ALBUM." AS a 
						where ap.i_audio_album_id=a.id AND ap.i_user_id='".$logged_user_id."'
						AND a.i_user_id = '%s'", $user_id);
		$query = $this->db->query($sql); 
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}
	
}
