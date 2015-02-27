<?php
include_once(APPPATH.'models/base_model.php');
class Photo_albums_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

	
	public function get() {
		$sql = sprintf('SELECT * FROM '.$this->db->PHOTO_ALBUM.' order by id desc');
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr;
	}
	
	
	public function get_by_id($id, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = sprintf('SELECT * FROM '.$this->db->PHOTO_ALBUM.'  where id = %s',  $id);
		}
		else {
			$sql = sprintf('SELECT * FROM '.$this->db->PHOTO_ALBUM.'  where id = %s limit %s, %s',  $id, $start_limit, $no_of_page);
		}

		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
		
		$new_array = array();
		
		$result_arr[0]['total_photo'] = $this->get_total_by_album_id($id);
		
		return $result_arr[0];
	}
	


 ### new created

	public function get_total_by_album_id($album_id) {
		$sql = sprintf("SELECT count(*) count FROM ".$this->db->USER_PHOTOS."  where  i_photo_album_id = '%s'", $album_id);
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}
	
	
	
	public function get_by_user_id($user_id, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = sprintf('SELECT p.*,CONCAT(u.s_first_name," ",u.s_last_name) s_profile_name FROM '.$this->db->PHOTO_ALBUM.' p,cg_users u WHERE p.i_user_id=u.id AND p.i_user_id = %s ORDER BY p.id DESC ',$user_id);
		}
		else {
			$sql = sprintf('SELECT p.*,CONCAT(u.s_first_name," ",u.s_last_name) s_profile_name FROM '.$this->db->PHOTO_ALBUM.' p,cg_users u WHERE p.i_user_id=u.id AND p.i_user_id = %s ORDER BY p.id DESC LIMIT %s, %s', $user_id, $start_limit, $no_of_page);
		}

		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
		
		$new_array = array();
		
		if(count($result_arr) >0){
			foreach($result_arr as $key=> $val){ 
				$result_arr[$key]['total_photo'] = $this->get_total_by_album_id($val['id']);
			}
		}
		

		return $result_arr;
	}
	
	
	

	public function get_total_by_user_id($user_id) {
		$sql = sprintf("SELECT count(*) count FROM ".$this->db->PHOTO_ALBUM."  where i_user_id = '%s'", $user_id);
		$query = $this->db->query($sql); 
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}
	
	

	public function insert($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->PHOTO_ALBUM, $arr);# echo $this->db->last_query();
		return $this->db->insert_id();
	}
	
	

	public function update($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->PHOTO_ALBUM, $arr, array('id'=>$id));
	}
	
	

	public function delete_by_id($id) {
	
	     $sql = sprintf( 'DELETE FROM '.$this->db->PHOTO_ALBUM.' WHERE id=%s', $id );
		 $this->db->query($sql);
		 
		 ## delete associated photos
		 $photo_sql = sprintf( 'SELECT s_photo FROM '.$this->db->USER_PHOTOS.' WHERE i_photo_album_id =%s ', $id );
		 $photo_arr = $this->db->query($photo_sql)->result_array();
		 
		 $sql = sprintf( 'DELETE FROM '.$this->db->USER_PHOTOS.' WHERE i_photo_album_id =%s ', $id );
		 $this->db->query($sql);
		
		#DELETE PRIVACY
		$this->db->query("DELETE FROM {$this->db->photoalbum_privacy} WHERE i_photo_album_id='".$rec_id."'");
		#DELETE PRIVACY
			
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
 //echo $this->db->last_query(); 
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
			//pr($result_arr);
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
			$sql = sprintf('SELECT * FROM '.$this->db->PHOTO_ALBUM.'  where id = %s %s ' ,  $id , $s_where);
		}
		else {
			$sql = sprintf('SELECT * FROM '.$this->db->PHOTO_ALBUM.'  where id = %s  %s limit %s, %s',  $id, $s_where, $start_limit, $no_of_page);
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
	
	public function get_privacy_settings_by_album_id($i_album_id)
	{
		 $s_qry =  "SELECT * FROM "
					  .$this->db->photoalbum_privacy. " WHERE i_photo_album_id = {$i_album_id}";
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
	
	public function get_photo_album_with_privacy_settings($user_id, $start_limit="", $no_of_page="") {
		/*$logged_user_id = intval(decrypt($this->session->userdata('user_id')));
		if("$start_limit" == "") {
			$sql = sprintf("SELECT * FROM ".$this->db->photoalbum_privacy." AS pr ,".$this->db->PHOTO_ALBUM." AS album 
							WHERE album.id=pr.i_photo_album_id AND 
							album.i_user_id = %s AND pr.i_user_id=%s 
							ORDER BY id DESC " ,$user_id,$logged_user_id);
		}
		else {
			$sql = sprintf('SELECT * FROM '.$this->db->photoalbum_privacy.' AS pr ,'.$this->db->PHOTO_ALBUM.' AS album 
							WHERE album.id=pr.i_photo_album_id AND 
							album.i_user_id = %s AND pr.i_user_id=%s 
							ORDER BY id DESC LIMIT %s, %s', $user_id, $logged_user_id, $start_limit, $no_of_page);
		}
		
		$query = $this->db->query($sql);#echo $this->db->last_query();exit;
		$result_arr = $query->result_array();
		
		$new_array = array();
		
		if(count($result_arr) >0){
			foreach($result_arr as $key=> $val){ 
				$result_arr[$key]['total_photo'] = $this->get_total_by_album_id($val['id']);
			}
		}*/
		

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
						mp.i_user_id = '".$profile_id."' and mp.s_section_name = 'photo'" ;
		 
		 $res_data = $this->db->query($mp_sql); 
		 $res_data_arr = $res_data->result_array();
		//pr($res_data_arr);
	    //echo $res_data_arr[0]['str'].' #$#$'; 
	    if($res_data_arr[0]['str'] != '') 
	    	$PRIVACY_STR = " AND pp.s_section IN  (".substr($res_data_arr[0]['str'], 0, -2).")";
		else
			$PRIVACY_STR = '';
	 
				  

		 $check_user_level_perm =	"SELECT COUNT(pp.i_photo_album_id) as total FROM cg_photo_album AS pa 
										LEFT JOIN cg_user_photos AS up ON up.i_photo_album_id=pa.id 
										LEFT JOIN cg_photoalbum_privacy AS pp ON pp.i_photo_album_id=pa.id 
										WHERE pa.i_user_id= '".$profile_id."' ";
			 
		 $is_user_level = $this->db->query($check_user_level_perm)->result_array();
		
		// echo 'total::: '.($is_user_level[0]['total']);
		#### if public no restriction show all 
		if($res_data_arr[0]['str'] == ''){
			
			  $s_qry = " SELECT up.* FROM cg_photo_album AS up
								  WHERE  up.i_user_id =".$profile_id." 
								 
							      ORDER BY id DESC LIMIT {$start_limit},{$no_of_page} 
			   ";
		      $rs=$this->db->query($s_qry); 	//pr($rs->result());
		 
			    $result_arr = $rs->result_array();
				$this->load->model('media_comments_model');
				if(is_array($result_arr)){
					foreach($result_arr as $key=>$val){
						$result_arr[$key]['total_photo'] = $this->get_total_by_album_id($val['id']);
						
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
							FROM cg_photo_album AS pa 
							LEFT JOIN cg_user_photos AS up ON up.i_photo_album_id=pa.id 
							WHERE pa.id not in (SELECT DISTINCT(pp.i_photo_album_id)
														FROM cg_photo_album AS pa1
														JOIN cg_photoalbum_privacy AS pp ON pp.i_photo_album_id = pa1.id  
														AND pa1.i_user_id = '".$profile_id."')
							AND pa.i_user_id = '".$profile_id."'
							GROUP BY pa.id
							ORDER BY id DESC LIMIT {$start_limit},{$no_of_page}
						   "; 
						  
						 // echo '## '.nl2br($s_qry);
			   $rs=$this->db->query($s_qry); 	//pr($rs->result());
		 
			    $result_arr = $rs->result_array();
				$this->load->model('media_comments_model');
				if(is_array($result_arr)){
					foreach($result_arr as $key=>$val){
						$result_arr[$key]['total_photo'] = $this->get_total_by_album_id($val['id']);
					}
		   		 }
			}
			
		}
		else{	### fetch all albums photos with user level permission	  
			$s_qry = "SELECT pa.* 
							FROM cg_photo_album AS pa 
							LEFT JOIN cg_user_photos AS up ON up.i_photo_album_id=pa.id 
							WHERE pa.id not in (SELECT DISTINCT(pp.i_photo_album_id)
														FROM cg_photo_album AS pa1
														JOIN cg_photoalbum_privacy AS pp ON pp.i_photo_album_id = pa1.id  
														AND pa1.i_user_id = '".$profile_id."')
							AND pa.i_user_id = '".$profile_id."' 
							GROUP BY pa.id
							
							
							UNION 
							
							SELECT pa.* FROM 
							  {$this->db->PHOTO_ALBUM} AS pa LEFT JOIN  
							  {$this->db->USER_PHOTOS} AS up ON up.i_photo_album_id=pa.id 
							  LEFT JOIN ".$this->db->photoalbum_privacy." AS pp ON pp.i_photo_album_id=pa.id 
							  WHERE pp.i_user_id='".$uid."'  AND pa.i_user_id = '".$profile_id."'
							  ".$PRIVACY_STR."
							  GROUP BY pa.id
							  ORDER BY id DESC LIMIT {$start_limit},{$no_of_page}
							  
						  "; 
						  
						  //echo nl2br($s_qry);
 		    $rs=$this->db->query($s_qry); 	//pr($rs->result());
		 
		    $result_arr = $rs->result_array();
			$this->load->model('media_comments_model');
			if(is_array($result_arr)){
				foreach($result_arr as $key=>$val){
					$result_arr[$key]['total_photo'] = $this->get_total_by_album_id($val['id']);
				}
		    }
#pr($result_arr);
				  
		}
			
		
		return $result_arr;
	}
	public function get_total_photo_album_with_privacy_settings($user_id) {
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
						mp.i_user_id = '".$profile_id."' and mp.s_section_name = 'photo'" ;
		 
		 $res_data = $this->db->query($mp_sql); 
		 $res_data_arr = $res_data->result_array();
		//pr($res_data_arr);
	    //echo $res_data_arr[0]['str'].' #$#$'; 
	    if($res_data_arr[0]['str'] != '') 
	    	$PRIVACY_STR = " AND pp.s_section IN  (".substr($res_data_arr[0]['str'], 0, -2).")";
		else
			$PRIVACY_STR = '';
	 
				  

		 $check_user_level_perm =	"SELECT COUNT(pp.i_photo_album_id) as total FROM cg_photo_album AS pa 
										LEFT JOIN cg_user_photos AS up ON up.i_photo_album_id=pa.id 
										LEFT JOIN cg_photoalbum_privacy AS pp ON pp.i_photo_album_id=pa.id 
										WHERE pa.i_user_id= '".$profile_id."' ";
			 
		 $is_user_level = $this->db->query($check_user_level_perm)->result_array();
		
		// echo 'total::: '.($is_user_level[0]['total']);
		#### if public no restriction show all 
		if($res_data_arr[0]['str'] == ''){
			
			  $s_qry = " SELECT count(up.id) as count FROM cg_photo_album AS up
								  WHERE  up.i_user_id =".$profile_id." 

			   ";
		      $rs=$this->db->query($s_qry); 	//pr($rs->result());
			  $result_arr = $rs->result_array();
				
		
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
			   
			$s_qry = " SELECT count(pa.id) as count
							FROM cg_photo_album AS pa 
							LEFT JOIN cg_user_photos AS up ON up.i_photo_album_id=pa.id 
							WHERE pa.id not in (SELECT DISTINCT(pp.i_photo_album_id)
														FROM cg_photo_album AS pa1
														JOIN cg_photoalbum_privacy AS pp ON pp.i_photo_album_id = pa1.id  
														AND pa1.i_user_id = '".$profile_id."')
							AND pa.i_user_id = '".$profile_id."'
							GROUP BY pa.id
							
						   "; 
						  
						 // echo '## '.nl2br($s_qry);
			   $rs=$this->db->query($s_qry); 	//pr($rs->result());
			   $result_arr = $rs->result_array();
				
			}
			
		}
		else{	### fetch all albums photos with user level permission	  
			$s_qry = " SELECT COUNT(*) as count FROM ( SELECT pa.id
							FROM cg_photo_album AS pa 
							LEFT JOIN cg_user_photos AS up ON up.i_photo_album_id=pa.id 
							WHERE pa.id not in (SELECT DISTINCT(pp.i_photo_album_id)
														FROM cg_photo_album AS pa1
														JOIN cg_photoalbum_privacy AS pp ON pp.i_photo_album_id = pa1.id  
														AND pa1.i_user_id = '".$profile_id."')
							AND pa.i_user_id = '".$profile_id."' 
							GROUP BY pa.id
							
							
							UNION 
							
							SELECT pa.id FROM 
							  {$this->db->PHOTO_ALBUM} AS pa LEFT JOIN  
							  {$this->db->USER_PHOTOS} AS up ON up.i_photo_album_id=pa.id 
							  LEFT JOIN ".$this->db->photoalbum_privacy." AS pp ON pp.i_photo_album_id=pa.id 
							  WHERE pp.i_user_id='".$uid."'  AND pa.i_user_id = '".$profile_id."'
							  ".$PRIVACY_STR."
							  GROUP BY pa.id
							  ) as drvd_tbl
							  
						  "; 
						  
						  //echo nl2br($s_qry);
 		    $rs=$this->db->query($s_qry); 	//pr($rs->result());
		    $result_arr = $rs->result_array();
			
#pr($result_arr);
				  
		}

		return $result_arr[0]['count'];
	}
}
