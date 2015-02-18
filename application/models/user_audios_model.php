<?php
include_once(APPPATH.'models/base_model.php');
class User_audios_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

	public function get() {
		$sql = sprintf('SELECT * FROM '.$this->db->USER_AUDIO.' order by id desc');
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr;
	}
	
	
	
	public function get_by_id($id, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = sprintf('SELECT * FROM '.$this->db->USER_AUDIO.'  where   id = %s',  $id);
		}
		else {
			
			 $sql = sprintf('SELECT * FROM '.$this->db->USER_AUDIO.'  where    id = %s limit %s, %s',  $id, $start_limit, $no_of_page);
		}

		$query = $this->db->query($sql); #echo $this->db->last_query(); exit;
		$result_arr = $query->result_array();

		return $result_arr[0];
	}
	
	
	function get_audio_title_by_id($db_id)
    {
        $sql = sprintf("SELECT * FROM %s WHERE id= %s",$this->db->USER_AUDIO, $db_id);
        $res = $this->db->query($sql)->result_array();

        return $res[0]['s_title'];
    }
    
    
    

	public function get_by_user_id($user_id, $s_where="", $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = sprintf('SELECT audio.*,gen.genre_name as genre  FROM '.$this->db->USER_AUDIO.' audio
									LEFT JOIN '.$this->db->genre.' gen on gen.id=audio.s_genre_id
								
								 WHERE  audio.i_status =1 AND  audio.i_user_id = %s %s ORDER BY audio.id DESC ',$user_id, $s_where);
		}
	
		else {
			$sql = sprintf('SELECT audio.*,gen.genre_name as genre FROM '.$this->db->USER_AUDIO
								.' audio LEFT JOIN '.$this->db->genre.' gen on gen.id=audio.s_genre_id WHERE 
								audio.i_status =1 AND 
								audio.i_user_id = %s %s ORDER BY audio.id DESC LIMIT %s, %s ', $user_id, $s_where, $start_limit, $no_of_page);
		}


		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
		$this->load->model('media_comments_model');
		if(is_array($result_arr)){
			foreach($result_arr as $key=>$val){
				$result_arr[$key]['total_comments'] = $this->media_comments_model->get_total_by_newsfeed_id($val['id'],'audio');
			}
		}

		return $result_arr;
	}
    
    
    
       /* public function get_allaudios_with_comments_by_user_id_($user_id, $s_where="", $start_limit="", $no_of_page="") {
    
           $logged_user_id = intval(decrypt($this->session->userdata('user_id'))); 
    //echo "user id : ".$user_id." where : ".$s_where." start : ".$start_limit." no of page : ".$no_of_page;
        if("$start_limit" == "") {
            $sql = sprintf("SELECT * FROM {$this->db->audioalbum_privacy} AS ap ,{$this->db->USER_AUDIO}  AS a 
								WHERE ap.i_audio_album_id=a.i_id_audio_album AND ap.i_user_id='".$logged_user_id."' AND
								a.i_status =1 AND  a.i_user_id = %s %s ORDER BY id DESC ",$user_id, $s_where);
        }
        else {
            $sql = sprintf("SELECT * FROM {$this->db->audioalbum_privacy} AS ap ,{$this->db->USER_AUDIO} AS a 
								WHERE ap.i_audio_album_id=a.i_id_audio_album AND ap.i_user_id='".$logged_user_id."' AND
								a.i_status =1 AND a.i_user_id = %s %s ORDER BY id DESC LIMIT %s, %s ", 
								$user_id, $s_where, $start_limit, $no_of_page);
        }

        $query = $this->db->query($sql);
#echo $this->db->last_query();exit;
        $result_arr = $query->result_array();
        $this->load->model('media_comments_model');
        if(is_array($result_arr)){
            foreach($result_arr as $key=>$val){
                $result_arr[$key]['total_comments'] = $this->media_comments_model->get_total_by_newsfeed_id($val['id'],'audio');
                $result_arr[$key]['comments_arr'] =  $this->media_comments_model->get_by_newsfeed_id($val['id'],'audio');
            }
        }
//pr($result_arr);
        return $result_arr;
    }
	*/
	
	

	public function get_total_by_user_id($user_id, $s_where) {
		$sql = sprintf("SELECT count(*) count FROM {$this->db->USER_AUDIO}  AS a 
							WHERE 
								a.i_status =1 AND a.i_user_id = '%s' %s ", $user_id, $s_where);
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}
	
	
	
	

	public function insert($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->USER_AUDIO, $arr);
		//echo $this->db->last_query();
		return $this->db->insert_id();
	}
	
	

	public function update($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->USER_AUDIO, $arr, array('id'=>$id));
		//echo $this->db->last_query();
	}
	
	

	public function delete_by_id($id) {
	    $sql = sprintf( 'DELETE FROM '.$this->db->USER_AUDIO.' WHERE id=%s', $id );

		$this->db->query($sql);
		
		$comments_sql = sprintf( 'DELETE FROM '.$this->db->USER_MEDIA_COMMENTS.' WHERE i_media_id=%s AND s_media_type = "audio" ', $id );

		$this->db->query($comments_sql);
		
		$like_sql = sprintf( "DELETE FROM ".$this->db->USER_MEDIA_LIKE." WHERE i_media_id=%s AND s_media_type = 'audio'" , $id);
		$this->db->query($like_sql); 
		
	}
	
	
	## by mediad id ###
	public function get_user_details_by_media_id($media_id, $s_where="", $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = sprintf('SELECT * FROM '.$this->db->USER_AUDIO.'  WHERE id = %s %s ORDER BY id DESC ',$media_id,$s_where);
		}
		else {
			 $sql = sprintf('SELECT * FROM '.$this->db->USER_AUDIO
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
	
	public function get_allaudios_with_comments_by_user_id_($user_id, $s_where="", $start_limit="", $no_of_page="") {
		
		
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
			
			  $s_qry = " SELECT up.* FROM {$this->db->USER_AUDIO} AS up
								  WHERE  up.i_user_id =".$profile_id."  ORDER BY id DESC 
			   ";
		      $rs=$this->db->query($s_qry); 	//pr($rs->result());
		 
			    $result_arr = $rs->result_array();
				$this->load->model('media_comments_model');
				if(is_array($result_arr)){
					foreach($result_arr as $key=>$val){
						$result_arr[$key]['total_comments'] = $this->media_comments_model->get_total_by_newsfeed_id($val['id'],'photo');
						$result_arr[$key]['comments_arr'] =  $this->media_comments_model->get_by_newsfeed_id($val['id'],'audio');
						
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
							FROM cg_audio_album AS pa 
							LEFT JOIN {$this->db->USER_AUDIO} AS up ON up.i_id_audio_album=pa.id 
							WHERE pa.id not in (SELECT DISTINCT(pp.i_audio_album_id)
														FROM cg_audio_album AS pa1
														JOIN cg_audioalbum_privacy AS pp ON pp.i_audio_album_id = pa1.id  
														AND pa1.i_user_id = '".$profile_id."')
							AND pa.i_user_id = '".$profile_id."'
							AND (SELECT count(up1.id)
													FROM {$this->db->USER_AUDIO} AS up1
													WHERE up1.i_id_audio_album = pa.id
													) > 0 
						   "; 
						  
						 //echo '## '.nl2br($s_qry);
			   $rs=$this->db->query($s_qry); 	//pr($rs->result());
		 
			    $result_arr = $rs->result_array();
				$this->load->model('media_comments_model');
				if(is_array($result_arr)){
					foreach($result_arr as $key=>$val){
						$result_arr[$key]['total_comments'] = $this->media_comments_model->get_total_by_newsfeed_id($val['id'],'photo');
						$result_arr[$key]['comments_arr'] =  $this->media_comments_model->get_by_newsfeed_id($val['id'],'audio');
						
					}
		   		 }
			}
			
		}
		else{	### fetch all albums photos with user level permission	  
			$s_qry = "SELECT pa.id,  up.* 
							FROM cg_audio_album AS pa 
							LEFT JOIN {$this->db->USER_AUDIO} AS up ON up.i_id_audio_album=pa.id 
							WHERE pa.id not in (SELECT DISTINCT(pp.i_audio_album_id)
														FROM cg_audio_album AS pa1
														JOIN cg_audioalbum_privacy AS pp ON pp.i_audio_album_id = pa1.id  
														AND pa1.i_user_id = '".$profile_id."')
							AND pa.i_user_id = '".$profile_id."' 
							AND (SELECT count(up1.id)
													FROM {$this->db->USER_AUDIO} AS up1
													WHERE up1.i_id_audio_album = pa.id
													) > 0 
							
							UNION 
							
							SELECT DISTINCT(pp.i_user_id) AS uid,  up.* FROM 
							   cg_audio_album AS pa LEFT JOIN  
							  {$this->db->USER_AUDIO} AS up ON up.i_id_audio_album=pa.id 
							  LEFT JOIN cg_audioalbum_privacy AS pp ON pp.i_audio_album_id=pa.id 
							  WHERE pp.i_user_id='".$uid."' AND pa.i_user_id = '".$profile_id."'
							  ".$PRIVACY_STR."
							  AND (SELECT count(up1.id)
													FROM {$this->db->USER_AUDIO} AS up1
													WHERE up1.i_id_audio_album = pa.id
													) > 0 
							  
						  "; 
						  
						  //echo nl2br($s_qry);
 		    $rs=$this->db->query($s_qry); #pr($rs->result());
		 
		    $result_arr = $rs->result_array();
			$this->load->model('media_comments_model');
			if(is_array($result_arr)){
				foreach($result_arr as $key=>$val){
					$result_arr[$key]['total_comments'] = $this->media_comments_model->get_total_by_newsfeed_id($val['id'],'photo');
					$result_arr[$key]['comments_arr'] =  $this->media_comments_model->get_by_newsfeed_id($val['id'],'audio');
					
				}
		    }
#pr($result_arr);
				  
		}
		
		
		
		
        return $result_arr;
              
    }
	
}
