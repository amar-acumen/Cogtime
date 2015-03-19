<?php
include_once(APPPATH.'models/base_model.php');
class User_photos_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

	public function get() {
		$sql = 'SELECT * FROM '.$this->db->USER_PHOTOS.' order by id desc';
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr;
	}
	
	
	
	public function get_by_id($id, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = 'SELECT * FROM '.$this->db->USER_PHOTOS.'  where  id = "'.$id.'"';
		}
		else {
			
			 $sql = 'SELECT * FROM '.$this->db->USER_PHOTOS.'  where  id = "'.$id.'" limit '.$start_limit.', '.$no_of_page;
		}

		$query = $this->db->query($sql); #echo $this->db->last_query(); exit;
		$result_arr = $query->result_array();

		return $result_arr[0];
	}
	
	
	

	public function get_by_user_id($user_id, $s_where="", $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = 'SELECT * FROM '.$this->db->USER_PHOTOS.'  WHERE i_status =1 AND i_user_id = "'.$user_id.'" {$s_where} ORDER BY id DESC ';
		}
		else {
			 $sql = 'SELECT * FROM '.$this->db->USER_PHOTOS
								.'  WHERE i_status =1 AND i_user_id = "'.$user_id.'" {$s_where} ORDER BY id DESC LIMIT {$start_limit},  {$no_of_page}';
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
		$sql = "SELECT count(*) count FROM ".$this->db->USER_PHOTOS."  where i_status =1 AND i_user_id = '".$user_id."' ".$s_where;
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
	     $sql = 'DELETE FROM '.$this->db->USER_PHOTOS.' WHERE id="'.$id.'"';

		$this->db->query($sql);
		
		/*$sql = sprintf( 'DELETE FROM '.$this->db->MEDIA_MAIN_COMMENTS.' WHERE i_media_id=%s AND s_media_type = \'photo\'', $id );

		$this->db->query($sql);*/
		
	}
	
	
	public function get_by_media_id($id, $s_where="", $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = 'SELECT * FROM '.$this->db->USER_PHOTOS.'  WHERE i_status =1 AND  id = "'.$id.'" {$s_where} ORDER BY id DESC ';
		}
		else {
			 $sql = 'SELECT * FROM '.$this->db->USER_PHOTOS
								.'  WHERE i_status =1 AND id = "'.$id.'" {$s_where} ORDER BY id DESC LIMIT {$start_limit}, {$no_of_page} ';
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
		$sql = "SELECT count(*) count FROM ".$this->db->USER_PHOTOS."  where i_status =1 AND id = '".$id."' ".$s_where;
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}
	
	
	### all photos with comments ###
	
	/* public function get_allphotos_with_comments_by_user_id_($user_id, $s_where="", $start_limit="", $no_of_page="") {
		$logged_user_id = intval(decrypt($this->session->userdata('user_id')));
		
		#### adding for public:::: 
		$mp_sql = "  SELECT  if ((mp.i_friend_privacy = '0' &&  mp.i_netpal_privacy = '0' 
										&&  mp.i_prayer_partner_privacy = '0' && 
							            mp.i_ring_privacy = '0' &&  mp.i_prayer_group_privacy = '0'
										) , 'Y' , 'N') as str
						FROM cg_privacy_settings mp 
						WHERE
						mp.i_user_id = '".$user_id."' and mp.s_section_name = 'photo' " ;
		 
		 $res_data = $this->db->query($mp_sql); 
		 $res_data_arr = $res_data->result_array();
		 
		//pr($res_data_arr,1);
		//echo $this->db->last_query();exit;
		if($res_data_arr[0]['str'] == 'Y'){ # public then fetch all
			
			if("$start_limit" == "") {
				$sql = sprintf("SELECT * FROM {$this->db->USER_PHOTOS} AS up 
								WHERE up.i_status =1 AND  up.i_user_id = %s %s ORDER BY id DESC ",$user_id,$s_where);
			}
			else {
				 $sql = sprintf("SELECT * FROM {$this->db->USER_PHOTOS} AS up
								  WHERE up.i_status =1 AND up.i_user_id = %s %s ORDER BY id DESC 
								  LIMIT %s, %s ", $user_id, $s_where, $start_limit, $no_of_page);
			}
		
		}
		else {  ### private permission
			
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
		}

		$query = $this->db->query($sql); //echo $this->db->last_query();
        
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
	}*/
		
	public function get_total_allphotos_with_comments_by_user_id_($user_id, $s_where) {
		$sql = "SELECT count(*) count FROM ".$this->db->USER_PHOTOS." AS up, {$this->db->photoalbum_privacy} AS pr  
						where pr.i_photo_album_id=up.i_photo_album_id 
									AND pr.i_user_id='".$logged_user_id."' AND 
									up.i_status =1 AND  up.i_user_id = '".$user_id."' ".$s_where;
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}
	
	
	
	
	
	### by mediad id ###
	public function get_user_details_by_media_id($media_id, $s_where="", $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = 'SELECT * FROM '.$this->db->USER_PHOTOS.'  WHERE id = "'.$media_id.'" {$s_where} ORDER BY id DESC ';
		}
		else {
			 $sql = 'SELECT * FROM '.$this->db->USER_PHOTOS
								.'  WHERE id = "'.$media_id.'" {$s_where} ORDER BY id DESC LIMIT '.$start_limit.', '.$no_of_page, $media_id, $s_where, , ;
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
	
	
	
	##### changed method
	 public function get_allphotos_with_comments_by_user_id_($user_id, $s_where="", $start_limit="", $no_of_page="") {
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
			
			  $s_qry = " SELECT up.* FROM {$this->db->USER_PHOTOS} AS up
								  WHERE  up.i_user_id =".$profile_id."  ORDER BY id DESC 
			   ";
		      $rs=$this->db->query($s_qry); 	//pr($rs->result());
		 
			    $result_arr = $rs->result_array();
				$this->load->model('media_comments_model');
				if(is_array($result_arr)){
					foreach($result_arr as $key=>$val){
						$result_arr[$key]['total_comments'] = $this->media_comments_model->get_total_by_newsfeed_id($val['id'],'photo');
						$result_arr[$key]['comments_arr'] =  $this->media_comments_model->get_by_newsfeed_id($val['id'],'photo');
						
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
			   
			$s_qry = " SELECT pa.id, up.*
							FROM cg_photo_album AS pa 
							LEFT JOIN cg_user_photos AS up ON up.i_photo_album_id=pa.id 
							WHERE pa.id not in (SELECT DISTINCT(pp.i_photo_album_id)
														FROM cg_photo_album AS pa1
														JOIN cg_photoalbum_privacy AS pp ON pp.i_photo_album_id = pa1.id  
														AND pa1.i_user_id = '".$profile_id."')
							AND pa.i_user_id = '".$profile_id."'
							AND (SELECT count(up1.id)
													FROM cg_user_photos AS up1
													WHERE up1.i_photo_album_id = pa.id
													) > 0 
						   "; 
						  
						 // echo '## '.nl2br($s_qry);
			   $rs=$this->db->query($s_qry); 	//pr($rs->result());
		 
			    $result_arr = $rs->result_array();
				$this->load->model('media_comments_model');
				if(is_array($result_arr)){
					foreach($result_arr as $key=>$val){
						$result_arr[$key]['total_comments'] = $this->media_comments_model->get_total_by_newsfeed_id($val['id'],'photo');
						$result_arr[$key]['comments_arr'] =  $this->media_comments_model->get_by_newsfeed_id($val['id'],'photo');
						
					}
		   		 }
			}
			
		}
		else{	### fetch all albums photos with user level permission	  
			$s_qry = "SELECT pa.id,  up.* 
							FROM cg_photo_album AS pa 
							LEFT JOIN cg_user_photos AS up ON up.i_photo_album_id=pa.id 
							WHERE pa.id not in (SELECT DISTINCT(pp.i_photo_album_id)
														FROM cg_photo_album AS pa1
														JOIN cg_photoalbum_privacy AS pp ON pp.i_photo_album_id = pa1.id  
														AND pa1.i_user_id = '".$profile_id."')
							AND pa.i_user_id = '".$profile_id."' 
							AND (SELECT count(up1.id)
													FROM cg_user_photos AS up1
													WHERE up1.i_photo_album_id = pa.id
													) > 0 
							
							UNION 
							
							SELECT DISTINCT(pp.i_user_id) AS uid,  up.* FROM 
							  {$this->db->PHOTO_ALBUM} AS pa LEFT JOIN  
							  {$this->db->USER_PHOTOS} AS up ON up.i_photo_album_id=pa.id 
							  LEFT JOIN ".$this->db->photoalbum_privacy." AS pp ON pp.i_photo_album_id=pa.id 
							  WHERE pp.i_user_id='".$uid."'  AND pa.i_user_id = '".$profile_id."'
							  ".$PRIVACY_STR."
							  AND (SELECT count(up1.id)
													FROM cg_user_photos AS up1
													WHERE up1.i_photo_album_id = pa.id
													) > 0 
							  
						  "; 
						  
						  //echo nl2br($s_qry);
 		    $rs=$this->db->query($s_qry); 	//pr($rs->result());
		 
		    $result_arr = $rs->result_array();
			$this->load->model('media_comments_model');
			if(is_array($result_arr)){
				foreach($result_arr as $key=>$val){
					$result_arr[$key]['total_comments'] = $this->media_comments_model->get_total_by_newsfeed_id($val['id'],'photo');
					$result_arr[$key]['comments_arr'] =  $this->media_comments_model->get_by_newsfeed_id($val['id'],'photo');
					
				}
		    }
#pr($result_arr);
				  
		}
			
		
		return $result_arr;
	}	
		

}
