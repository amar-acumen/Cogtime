<?php
include_once(APPPATH.'models/base_model.php');
class Ring_post_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

	
	public function get() {
		$sql = sprintf('SELECT * FROM '.$this->db->USER_RING_POST.' order by id desc');
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr;
	}
	
	
	public function get_by_id($id, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = sprintf('SELECT * FROM '.$this->db->USER_RING_POST.'  where id = %s',  $id);
		}
		else {
			$sql = sprintf('SELECT * FROM '.$this->db->USER_RING_POST.'  where id = %s limit %s, %s',  $id, $start_limit, $no_of_page);
		}

		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
		
		return $result_arr[0];
	}
	



	
	
### GET ALL ring post BY LOGGED USER ID ##########33
public function get_all_ring_post_by_ring_id($i_ring_id, $s_where, $i_start_limit='', $i_no_of_page='') {
		
		if("$i_start_limit" == "") {
			$sql = sprintf("
				(SELECT u.id post_owner_user_id,
							  u.s_email,
							  u.e_gender,
							  CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
							  u.s_profile_photo,
							  (SELECT count(*) FROM cg_user_ring_post_comments RC WHERE RC.i_ring_post_id = t.id) as total_comments,
							  
							  t.*
							  FROM cg_users u, cg_user_ring_post t
							  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND t.i_user_id = u.id and t.i_ring_id = %1\$s and t.i_disable='1'
							  %2\$s)

				ORDER BY t.id DESC
					"
				,  intval($i_ring_id), $s_where
			);
		}
		else {
		
		
		
			 $sql = sprintf("
						(SELECT u.id post_owner_user_id,
									  u.s_email,
									  u.e_gender,
									  CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
									  u.s_profile_photo,
									   (SELECT count(*) FROM cg_user_ring_post_comments RC WHERE RC.i_ring_post_id = t.id) as total_comments,
									  t.*
									  
									  FROM cg_users u, cg_user_ring_post t
									  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND t.i_user_id = u.id and t.i_ring_id = %1\$s and t.i_disable='1'
									  %4\$s)
	
						ORDER BY dt_created_on DESC
						limit %2\$s, %3\$s
					"
				,  intval($i_ring_id), intval($i_start_limit), intval($i_no_of_page),  $s_where
			);
		}

#AND t.i_user_id != '%2\$s'
		$query = $this->db->query($sql); #echo "sql ==>". ($sql);exit; 
		$result_arr = $query->result_array();
        
     //pr($result_arr);
	 	$result_arr = check_friend_netpal_status($result_arr);
     
		return $result_arr;
		
	
	}
	
	public function get_total_all_ring_post_by_ring_id($i_ring_id,  $s_where) {
		

		 $sql = sprintf("
				SELECT COUNT(*) count FROM (
							(SELECT    t.id
									 
									  FROM cg_users u, cg_user_ring_post t
									  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND t.i_user_id = u.id and t.i_ring_id = %1\$s
									  %2\$s)

 				) derived_tbl
					"
				, intval($i_ring_id),$s_where
			);
		
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
		$this->db->insert($this->db->USER_RING_POST, $arr);# echo $this->db->last_query();
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
		
		 $sql = sprintf( 'DELETE FROM '.$this->db->USER_RING_POST_LIKE.' WHERE i_ring_post_id=%s', $id );
		 $this->db->query($sql);
		 
		 $sql = sprintf( 'DELETE FROM '.$this->db->USER_RING_POST_COMMENTS.' WHERE i_ring_post_id=%s', $id );
		 $this->db->query($sql);
		 
	     $sql = sprintf( 'DELETE FROM '.$this->db->USER_RING_POST.' WHERE id=%s', $id );
		 $this->db->query($sql);
		#echo $this->db->last_query(); exit;
	}
	
	public function get_ring_post_by_user_id($i_user_id, $s_where, $i_start_limit='', $i_no_of_page='') {
		
		if("$i_start_limit" == "") {
			$sql = "select * from cg_user_ring_post ".$s_where." and i_user_id=".$i_user_id." order by id desc";
		}
		else {
		
		
		
			 $sql = "select * from cg_user_ring_post ".$s_where." and i_user_id=".$i_user_id." order by id desc
			 limit ".$i_start_limit.",".$i_no_of_page."";
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
	
}
