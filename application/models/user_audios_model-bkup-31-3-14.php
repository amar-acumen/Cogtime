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
    
    
    
        public function get_allaudios_with_comments_by_user_id_($user_id, $s_where="", $start_limit="", $no_of_page="") {
    
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
	
	
	

	public function get_total_by_user_id($user_id, $s_where) {
		$sql = sprintf("SELECT count(*) count FROM {$this->db->audioalbum_privacy} AS ap ,{$this->db->USER_AUDIO}  AS a 
							WHERE ap.i_audio_album_id=a.i_id_audio_album AND ap.i_user_id='".$logged_user_id."' AND
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
	
		
}
