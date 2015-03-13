<?php
include_once(APPPATH.'models/base_model.php');
class Audio_albums_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

	
	public function get() {
		$sql = 'SELECT * FROM '.$this->db->AUDIO_ALBUM.' order by id desc';
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr;
	}
	
	
	public function get_by_id($id, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = 'SELECT * FROM '.$this->db->AUDIO_ALBUM.'  where id = "'.$id.'"';
		}
		else {
			$sql = 'SELECT * FROM '.$this->db->AUDIO_ALBUM.'  where id = "'.$id.'" limit {$start_limit}, {$no_of_page}';
		}
		

		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
		
		$new_array = array();
		
		$result_arr[0]['total_photo'] = $this->get_total_by_album_id($id);
		
		return $result_arr[0];
	}
	


 ### new created

	public function get_total_by_album_id($album_id) {
		$sql = "SELECT count(*) count FROM ".$this->db->USER_AUDIO."  where  i_id_audio_album = '".$album_id."'";
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}
	
	
	
	public function get_by_user_id($user_id, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = 'SELECT * FROM '.$this->db->AUDIO_ALBUM.'  WHERE i_user_id = "'.$user_id.'" ORDER BY id DESC ';
		}
		else {
			$sql = 'SELECT * FROM '.$this->db->AUDIO_ALBUM.'  WHERE i_user_id = "'.$user_id.'" ORDER BY id DESC LIMIT '.$start_limit.', '.$no_of_page.'';
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
<<<<<<< HEAD
		$sql = "SELECT count(*) count FROM ".$this->db->AUDIO_ALBUM."  where i_user_id = '".$user_id."'", ;
=======
		$sql = "SELECT count(*) count FROM ".$this->db->AUDIO_ALBUM."  where i_user_id = '".$user_id."'";
>>>>>>> 919edf40b7b491c6b74f33e8bdbf9bb4db6fa402
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
            
              //echo $id;
	        
            $logged_user_id = intval(decrypt($this->session->userdata('user_id')));
            //echo $logged_user_id; 
            // die('ok');
<<<<<<< HEAD
	     $sql = 'DELETE FROM '.$this->db->AUDIO_ALBUM.' WHERE id="'.$id.'"';
			 $this->db->query($sql);
		 
		 ## delete associated photos
		 $audio_sql = 'SELECT id, s_audio_file_name FROM '.$this->db->USER_AUDIO.' WHERE  i_id_audio_album ="'.$id.'" ';
=======
	     	$sql =  'DELETE FROM '.$this->db->AUDIO_ALBUM.' WHERE id="'.$id.'"';
			$this->db->query($sql);
		 
		 ## delete associated photos
		 $audio_sql = 'SELECT id, s_audio_file_name FROM '.$this->db->USER_AUDIO.' WHERE  i_id_audio_album = "'.$id.'"';
>>>>>>> 919edf40b7b491c6b74f33e8bdbf9bb4db6fa402
		 $audio_arr = $this->db->query($audio_sql)->result_array();
		 
		   $albums_comments_sql = 'DELETE FROM cg_user_media_comments WHERE i_media_id="'.$id.'" AND s_media_type = "audio_album" ';
		 $this->db->query($albums_comments_sql);
		
<<<<<<< HEAD
		  $like_album_sql = "DELETE FROM cg_user_media_like WHERE i_media_id='".$id."' AND s_media_type = 'audio_album'" ;
=======
		  $like_album_sql = "DELETE FROM cg_user_media_like WHERE i_media_id='".$id."' AND s_media_type = 'audio_album'";
>>>>>>> 919edf40b7b491c6b74f33e8bdbf9bb4db6fa402
		  		 $this->db->query($like_album_sql); 
		 
		 ## delete associated comments
		 if(count($audio_arr) && is_array($audio_arr)){
			 foreach($audio_arr as $key=>$val){
				  $comments_sql = 'DELETE FROM cg_user_media_comments WHERE i_media_id="'.$val['id'].'" AND s_media_type = "audio" ';
				 $this->db->query($comments_sql);
				 
				# delete from like table #
				  $like_sql = "DELETE FROM cg_user_media_like WHERE i_media_id='".$val['id']."' AND s_media_type = 'audio'";
		  		 $this->db->query($like_sql); 
				 
				  $audio_sql = 'DELETE FROM cg_user_audio WHERE i_id_audio_album="'.$id.'"';				 
				 $this->db->query($audio_sql);
				 
				
			 }
		 }
		
		
		#DELETE PRIVACY
		$this->db->query("DELETE FROM {$this->db->audioalbum_privacy} WHERE i_audio_album_id='".$id."'");
		#DELETE PRIVACY
		/****************delete audio************************/
                $this->db->delete('cg_user_audio', array('i_id_audio_album' => $id , 'i_user_id' => $logged_user_id));

                /******************************************/
		
		return $audio_arr;
	}
	
	
	
	##### fetching user's pic per album 

	public function get_audios_by_album_id($album_id, $user_id,  $start_limit="", $no_of_page="") 
	{
		global $CI;
				
		if("$start_limit" == "") {
			$sql = 		"SELECT * FROM ".$this->db->USER_AUDIO
						."  where  i_id_audio_album = '".$album_id."' AND i_user_id = '".$user_id."' ORDER BY i_order DESC ";
		}
		else {
			$sql = 		"SELECT * FROM ".$this->db->USER_AUDIO."  where  i_id_audio_album = '".$album_id."' AND i_user_id = '".$user_id."' ORDER BY i_order DESC LIMIT {$start_limit}, {$no_of_page}";
		} 
				
		$query = $this->db->query($sql); 
		//echo $this->db->last_query();  exit;
		$result_arr = $query->result_array();
		
		if(count($result_arr) > 0 ){
			
			$user_sql = "SELECT s_profile_photo FROM ".$this->db->USERS."  where  id = '".$user_id."'";
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
<<<<<<< HEAD
			$sql = 'SELECT * FROM '.$this->db->AUDIO_ALBUM.'  where id = "'.$id.'" {$s_where} ';
		}
		else {
			$sql = 'SELECT * FROM '.$this->db->AUDIO_ALBUM.'  where id = "'.$id.'"  {$s_where} limit {$start_limit}, {$no_of_page}';
=======
			$sql = 'SELECT * FROM '.$this->db->AUDIO_ALBUM.'  where id =  %s '.$s_where.'';
		}
		else {
			$sql = 'SELECT * FROM '.$this->db->AUDIO_ALBUM.'  where id ="'.$id.'" '.$s_where.' limit {$start_limit}, {$no_of_page}';
>>>>>>> 919edf40b7b491c6b74f33e8bdbf9bb4db6fa402
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
		$sql = "SELECT count(*) count FROM ".$this->db->USER_AUDIO."  where  i_id_audio_album = '".$album_id."' {$s_where} ";
		$query = $this->db->query($sql);
		$result_arr = $query->result_array(); //echo $this->db->last_query(); exit;

		return $result_arr[0]['count'];
	}
	
	
	######## new for organize audio page ################3
	
	public function get_total_audios_albm_id_($album_id, $user_id) {
		$sql = "SELECT count(*) count FROM ".$this->db->USER_AUDIO."  where  i_id_audio_album = '".$album_id."' AND `i_user_id` = '".$user_id."' ";
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
		
		
		
		$uid = $logged_user_id = intval(decrypt($this->session->userdata('user_id')));
		$profile_id = $user_id;
		
		#### adding for public:::: 
		 $mp_sql = "  SELECT  if ((mp.i_friend_privacy = '0' &&  mp.i_netpal_privacy = '0' &&  mp.i_prayer_partner_privacy = '0' 
		 					&& mp.i_ring_privacy = '0' &&  mp.i_prayer_group_privacy = '0'
						    ) , '' ,
						       (concat(IF(mp.i_friend_privacy = '1', '\"Friend\", ', ''),''
		 							, IF(mp.i_netpal_privacy = '1', '\"Netpal\", ',''),'' 
									,IF(mp.i_prayer_partner_privacy = '1', '\"Prayer Partner\", ', '') , ''
									,IF(mp.i_ring_privacy = '1', '\"Ring User\", ', ''),''
									,IF(mp.i_prayer_group_privacy = '1', '\"Prayer Group\", ', '')))) as str
						FROM cg_privacy_settings mp 
						WHERE
						mp.i_user_id = '".$profile_id."' and mp.s_section_name = 'audio'" ;
		 
		 $res_data = $this->db->query($mp_sql); 
		 $res_data_arr = $res_data->result_array();
		//pr($res_data_arr);
	    //echo $res_data_arr[0]['str'].' #$#$'; 
	    if($res_data_arr[0]['str'] != '') 
	    	$PRIVACY_STR = " AND pp.s_section IN  (".substr($res_data_arr[0]['str'], 0, -2).")";
		else
			$PRIVACY_STR = '';
	 
				  

		 $check_user_level_perm =	"SELECT COUNT(pp.i_audio_album_id) as total FROM cg_audio_album AS pa 
										LEFT JOIN {$this->db->USER_AUDIO} AS up ON up.i_id_audio_album=pa.id 
										LEFT JOIN cg_audioalbum_privacy AS pp ON pp.i_audio_album_id=pa.id 
										WHERE pa.i_user_id= '".$profile_id."' ";
			 
		 $is_user_level = $this->db->query($check_user_level_perm)->result_array();
		
		// echo 'total::: '.($is_user_level[0]['total']);
		 #### if public no restriction show all 
		if($res_data_arr[0]['str'] == ''){
			
			  $s_qry = " SELECT pa.* FROM cg_audio_album AS pa
								  WHERE  pa.i_user_id =".$profile_id."  
								   ORDER BY id DESC LIMIT {$start_limit},{$no_of_page}
			   ";
		      $rs=$this->db->query($s_qry); 	//pr($rs->result());
		 
			    $result_arr = $rs->result_array();
				$this->load->model('media_comments_model');
				if(is_array($result_arr)){
					foreach($result_arr as $key=>$val){
						$result_arr[$key]['total_audio'] = $this->get_total_by_album_id($val['id']);
						
					}
		   		 }
		
		}
		### if user level permission not given  i.e $is_user_level[0]['total'] =0  
		#then check for group level else check user level
		elseif($is_user_level[0]['total'] == 0){
			
			## check user relationship if logged user has privacy permission of album 	
			$network_arr = CheckUserNetwork($profile_id);
			//pr($network_arr);
			
			$PRIVACY_ARR = explode(', ',substr($res_data_arr[0]['str'], 0, -2)); 
			//pr($PRIVACY_ARR);
			
			$privacy_result = array_intersect($network_arr, $PRIVACY_ARR);
			
			if(count($privacy_result)){ 
			### fetch all albums photos without user level permission i.e. only group level permission
			### check 
			   
			$s_qry = " SELECT pa.*
							FROM cg_audio_album AS pa 
							LEFT JOIN {$this->db->USER_AUDIO} AS up ON up.i_id_audio_album=pa.id 
							WHERE pa.id not in (SELECT DISTINCT(pp.i_audio_album_id)
														FROM cg_audio_album AS pa1
														JOIN cg_audioalbum_privacy AS pp ON pp.i_audio_album_id = pa1.id  
														AND pa1.i_user_id = '".$profile_id."')
							AND pa.i_user_id = '".$profile_id."'
							GROUP BY pa.id
							ORDER BY id DESC LIMIT {$start_limit},{$no_of_page}
							
						   "; 
						  
						 //echo '## '.nl2br($s_qry);
			   $rs=$this->db->query($s_qry); 	//pr($rs->result());
		 
			    $result_arr = $rs->result_array();
				$this->load->model('media_comments_model');
				if(is_array($result_arr)){
					foreach($result_arr as $key=>$val){
						$result_arr[$key]['total_audio'] = $this->get_total_by_album_id($val['id']);	
					}
		   		 }
			}
			
		}
		else{	### fetch all albums photos with user level permission	  
			$s_qry = "SELECT pa.*
							FROM cg_audio_album AS pa 
							LEFT JOIN {$this->db->USER_AUDIO} AS up ON up.i_id_audio_album=pa.id 
							WHERE pa.id not in (SELECT DISTINCT(pp.i_audio_album_id)
														FROM cg_audio_album AS pa1
														JOIN cg_audioalbum_privacy AS pp ON pp.i_audio_album_id = pa1.id  
														AND pa1.i_user_id = '".$profile_id."')
							AND pa.i_user_id = '".$profile_id."' 
							GROUP BY pa.id
							
							UNION 
							
							SELECT pa.* FROM 
							   cg_audio_album AS pa LEFT JOIN  
							  {$this->db->USER_AUDIO} AS up ON up.i_id_audio_album=pa.id 
							  LEFT JOIN cg_audioalbum_privacy AS pp ON pp.i_audio_album_id=pa.id 
							  WHERE pp.i_user_id='".$uid."' AND pa.i_user_id = '".$profile_id."'
							  ".$PRIVACY_STR."
							  GROUP BY pa.id
							  
							  ORDER BY id DESC LIMIT {$start_limit},{$no_of_page}
							  
						  "; 
						  
						  
 		    $rs=$this->db->query($s_qry); #pr($rs->result());
		 
		    $result_arr = $rs->result_array();
			$this->load->model('media_comments_model');
			if(is_array($result_arr)){
				foreach($result_arr as $key=>$val){
					$result_arr[$key]['total_audio'] = $this->get_total_by_album_id($val['id']);
					
				}
		    }
				  
		}
		
		//echo nl2br($s_qry);
		
	//pr($result_arr,1);
	
        return $result_arr;
		
		
		
		
	}
	
	
	

	public function get_total_audio_album_with_privacy($user_id) {
		
		
		
		
		$uid = $logged_user_id = intval(decrypt($this->session->userdata('user_id')));
		$profile_id = $user_id;
		
		#### adding for public:::: 
		 $mp_sql = "  SELECT  if ((mp.i_friend_privacy = '0' &&  mp.i_netpal_privacy = '0' &&  mp.i_prayer_partner_privacy = '0' 
		 					&& mp.i_ring_privacy = '0' &&  mp.i_prayer_group_privacy = '0'
						    ) , '' ,
						       (concat(IF(mp.i_friend_privacy = '1', '\"Friend\", ', ''),''
		 							, IF(mp.i_netpal_privacy = '1', '\"Netpal\", ',''),'' 
									,IF(mp.i_prayer_partner_privacy = '1', '\"Prayer Partner\", ', '') , ''
									,IF(mp.i_ring_privacy = '1', '\"Ring User\", ', ''),''
									,IF(mp.i_prayer_group_privacy = '1', '\"Prayer Group\", ', '')))) as str
						FROM cg_privacy_settings mp 
						WHERE
						mp.i_user_id = '".$profile_id."' and mp.s_section_name = 'audio'" ;
		 
		 $res_data = $this->db->query($mp_sql); 
		 $res_data_arr = $res_data->result_array();
		//pr($res_data_arr);
	    //echo $res_data_arr[0]['str'].' #$#$'; 
	    if($res_data_arr[0]['str'] != '') 
	    	$PRIVACY_STR = " AND pp.s_section IN  (".substr($res_data_arr[0]['str'], 0, -2).")";
		else
			$PRIVACY_STR = '';
	 
				  

		 $check_user_level_perm =	"SELECT COUNT(pp.i_audio_album_id) as total FROM cg_audio_album AS pa 
										LEFT JOIN {$this->db->USER_AUDIO} AS up ON up.i_id_audio_album=pa.id 
										LEFT JOIN cg_audioalbum_privacy AS pp ON pp.i_audio_album_id=pa.id 
										WHERE pa.i_user_id= '".$profile_id."' ";
			 
		 $is_user_level = $this->db->query($check_user_level_perm)->result_array();
		
		// echo 'total::: '.($is_user_level[0]['total']);
		 #### if public no restriction show all 
		if($res_data_arr[0]['str'] == ''){
			
			  $s_qry = " SELECT COUNT(pa.id) as count  FROM cg_audio_album AS pa
								  WHERE  pa.i_user_id =".$profile_id."  ORDER BY id DESC 
			   ";
		      $rs=$this->db->query($s_qry); 	//pr($rs->result());
		 
			    $result_arr = $rs->result_array();
				$this->load->model('media_comments_model');
				if(is_array($result_arr)){
					foreach($result_arr as $key=>$val){
						$result_arr[$key]['total_audio'] = $this->get_total_by_album_id($val['id']);
						
					}
		   		 }
		
		}
		### if user level permission not given  i.e $is_user_level[0]['total'] =0  
		#then check for group level else check user level
		elseif($is_user_level[0]['total'] == 0){
			
			## check user relationship if logged user has privacy permission of album 	
			$network_arr = CheckUserNetwork($profile_id);
			//pr($network_arr);
			
			$PRIVACY_ARR = explode(', ',substr($res_data_arr[0]['str'], 0, -2)); 
			//pr($PRIVACY_ARR);
			
			$privacy_result = array_intersect($network_arr, $PRIVACY_ARR);
			
			if(count($privacy_result)){ 
			### fetch all albums photos without user level permission i.e. only group level permission
			### check 
			   
			$s_qry = " SELECT COUNT(pa.id) as count 
							FROM cg_audio_album AS pa 
							LEFT JOIN {$this->db->USER_AUDIO} AS up ON up.i_id_audio_album=pa.id 
							WHERE pa.id not in (SELECT DISTINCT(pp.i_audio_album_id)
														FROM cg_audio_album AS pa1
														JOIN cg_audioalbum_privacy AS pp ON pp.i_audio_album_id = pa1.id  
														AND pa1.i_user_id = '".$profile_id."')
							AND pa.i_user_id = '".$profile_id."'
							GROUP BY pa.id
							
						   "; 
						  
						 //echo '## '.nl2br($s_qry);
			   $rs=$this->db->query($s_qry); 	//pr($rs->result());
		 
			    $result_arr = $rs->result_array();
				$this->load->model('media_comments_model');
				if(is_array($result_arr)){
					foreach($result_arr as $key=>$val){
						$result_arr[$key]['total_audio'] = $this->get_total_by_album_id($val['id']);	
					}
		   		 }
			}
			
		}
		else{	### fetch all albums photos with user level permission	  
			$s_qry = " SELECT COUNT(*) as count FROM (SELECT pa.id
							FROM cg_audio_album AS pa 
							LEFT JOIN {$this->db->USER_AUDIO} AS up ON up.i_id_audio_album=pa.id 
							WHERE pa.id not in (SELECT DISTINCT(pp.i_audio_album_id)
														FROM cg_audio_album AS pa1
														JOIN cg_audioalbum_privacy AS pp ON pp.i_audio_album_id = pa1.id  
														AND pa1.i_user_id = '".$profile_id."')
							AND pa.i_user_id = '".$profile_id."' 
							GROUP BY pa.id
							
							UNION 
							
							SELECT pa.id FROM 
							   cg_audio_album AS pa LEFT JOIN  
							  {$this->db->USER_AUDIO} AS up ON up.i_id_audio_album=pa.id 
							  LEFT JOIN cg_audioalbum_privacy AS pp ON pp.i_audio_album_id=pa.id 
							  WHERE pp.i_user_id='".$uid."' AND pa.i_user_id = '".$profile_id."'
							  ".$PRIVACY_STR."
							  GROUP BY pa.id
							  ) as drvd_tbl
							  
							  
						  "; 
						  
						  
 		    $rs=$this->db->query($s_qry); #pr($rs->result());
		 
		    $result_arr = $rs->result_array();
				  
		}
	
		return $result_arr[0]['count'];
	}
	
}
