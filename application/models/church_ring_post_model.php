<?php
include_once(APPPATH.'models/base_model.php');
class Church_ring_post_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

	
	public function get() {
		$sql = 'SELECT * FROM '.$this->db->USER_RING_POST.' order by id desc';
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr;
	}
	
	
	public function get_by_id($id, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = 'SELECT * FROM '.$this->db->USER_RING_POST.'  where id = "'.$id.'"';
		}
		else {
			$sql = 'SELECT * FROM '.$this->db->USER_RING_POST.'  where id = "'.$id.'" limit {$start_limit}, {$no_of_page}';
		}

		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
		
		return $result_arr[0];
	}
	



	
	
### GET ALL ring post BY LOGGED USER ID ##########33
public function get_all_ring_post_by_ring_id($i_ring_id, $s_where, $i_start_limit='', $i_no_of_page='') {
		
		if("$i_start_limit" == "") {
			$sql = "
				(SELECT u.id post_owner_user_id,
							  u.s_email,
							  u.e_gender,
							  CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
							  u.s_profile_photo,
							  (SELECT count(*) FROM cg_church_ring_post_comments RC WHERE RC.i_ring_post_id = t.id) as total_comments,
							  
							  t.*
							  FROM cg_users u, cg_church_ring_post t
							  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND t.i_user_id = u.id and t.i_ring_id = '".intval($i_ring_id)."' and t.i_disable='1'
							  {$s_where})

				ORDER BY t.id DESC";
		}
		else {
		
		
		
			 $sql = "
						(SELECT u.id post_owner_user_id,
									  u.s_email,
									  u.e_gender,
									  CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
									  u.s_profile_photo,
									   (SELECT count(*) FROM cg_church_ring_post_comments RC WHERE RC.i_ring_post_id = t.id) as total_comments,
									  t.*
									  
									  FROM cg_users u, cg_church_ring_post t
									  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND t.i_user_id = u.id and t.i_ring_id = '".intval($i_ring_id)."' and t.i_disable='1'
									  {$s_where})
	
						ORDER BY dt_created_on DESC
						limit {$i_start_limit}, {$i_no_of_page}
					";
		}

#AND t.i_user_id != '%2\$s'
		$query = $this->db->query($sql);//echo "sql ==>". ($sql);exit; 
		$result_arr = $query->result_array();
        
     //pr($result_arr);
	 	$result_arr = check_friend_netpal_status($result_arr);
     
		return $result_arr;
		
	
	}
	
	public function get_total_all_ring_post_by_ring_id($i_ring_id,  $s_where) {
		

		 $sql = "
				SELECT COUNT(*) count FROM (
							(SELECT    t.id
									 
									  FROM cg_users u, cg_church_ring_post t
									  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND t.i_user_id = u.id and t.i_ring_id = '".intval($i_ring_id)."'
									  {$s_where})

 				) derived_tbl
					";
		
#and t.i_user_id != '%2\$s'
		$query = $this->db->query($sql); //echo "sql ==>". ($sql) ."<br />";  
		$result_arr = $query->result_array();
		#echo $result_arr[0]['count']; exit;
		return $result_arr[0]['count'];
	
	}
	
	
### GET ALL TWEETS BY LOGGED USER ID ##########


	
	

	public function insert($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert('cg_church_ring_post', $arr);// echo $this->db->last_query();
		return $this->db->insert_id();
	}
	
	

	public function update($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->USER_RING_POST, $arr, array('id'=>$id));
		#echo $this->db->last_query(); exit;
	}
	
	

	public function delete_by_id($id) {
		
		 $sql = 'DELETE FROM '.$this->db->USER_RING_POST_LIKE.' WHERE i_ring_post_id="'.$id.'"';
		 $this->db->query($sql);
		 
		 $sql = 'DELETE FROM '.$this->db->USER_RING_POST_COMMENTS.' WHERE i_ring_post_id="'.$id.'"';
		 $this->db->query($sql);
		 
	     $sql = 'DELETE FROM '.$this->db->USER_RING_POST.' WHERE id="'.$id.'"';
		 $this->db->query($sql);
		#echo $this->db->last_query(); exit;
	}
	
	public function get_ring_post_by_user_id($i_user_id, $s_where, $i_start_limit='', $i_no_of_page='') {
		//die('d');
		if("$i_start_limit" == "") {
			$sql = "select  *,(SELECT count(*) FROM cg_user_ring_post_comments RC WHERE RC.i_ring_post_id = cg_user_ring_post.id) as total_comments from cg_user_ring_post ".$s_where." and i_user_id=".$i_user_id." order by id desc";
		
                       // echo $sql;exit;
                }
		else {
		
		
		
			 $sql = "select  *,(SELECT count(*) FROM cg_user_ring_post_comments RC WHERE RC.i_ring_post_id = cg_user_ring_post.id) as total_comments from cg_user_ring_post ".$s_where." and i_user_id=".$i_user_id." order by id desc
			 limit ".$i_start_limit.",".$i_no_of_page."";
                           //echo $sql;exit;
		}

#AND t.i_user_id != '%2\$s'
		$query = $this->db->query($sql); #echo "sql ==>". ($sql);exit; 
		$result_arr = $query->result_array();
        
     //pr($result_arr);
	 	//$result_arr = check_friend_netpal_status($result_arr);
     
		return $result_arr;
		
	
	}
	
	public function get_total_ring_post_by_user_id($i_user_id,$s_where)
	{
	$sql="select count(*) as count from cg_user_ring_post ".$s_where."and i_user_id=".$i_user_id;
	$query = $this->db->query($sql); #echo "sql ==>". ($sql);exit; 
		$result_arr = $query->result_array();
		return $result_arr['0']['count'];
	}
         public function get_all_ring_comment_ringid($i_ring_id) {
            {$sql="select distinct c.* ,CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name, u.s_profile_photo from cg_users u ,cg_user_ring_post_comments c where u.id = c.i_user_id AND c.i_ring_post_id='$i_ring_id' ";}
//echo $sql; exit;
$query=$this->db->query($sql);
$result_arr=$query->result_array();
//echo $sql;exit;
//echo $i_start_limit;
//pr($result_arr,1);
return $result_arr;
            
        }
        public function get_all_ring_comments($s_where,$i_start_limit='', $i_no_of_page=''){
            $sql="select c.* ,p.s_post_title,a.s_ring_name, u.s_first_name, u.s_last_name from cg_user_ring_post_comments c ,cg_user_ring_post p,cg_user_ring a,cg_users u  $s_where and c.i_ring_post_id = p.id and p.i_ring_id = a.id and u.id = c.i_user_id ORDER BY c.id DESC limit ".$i_start_limit.",".$i_no_of_page;
           //echo $sql;
            $query=$this->db->query($sql);
$result_arr=$query->result_array();
//echo $sql;exit;
//echo $i_start_limit;
//pr($result_arr,1);
return $result_arr;
        }
        function get_all_ring_comments_total($s_where){
            
            $sql = "select count(*) as count from cg_user_ring_post_comments c ,cg_users u $s_where and c.i_user_id = u.id ORDER BY c.id DESC ";
            $query = $this->db->query($sql); #echo "sql ==>". ($sql);exit; 
		$result_arr = $query->result_array();
		return $result_arr['0']['count'];
            
        }
	
}
