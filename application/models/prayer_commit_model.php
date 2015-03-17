<?php

class Prayer_commit_model extends CI_Model {
	
	public function __construct() {
		parent::__construct();
	}

	
	 /******
    * This method will fetch all records from the db. 
    * 
    * @param string $s_where, ex- " status=1 AND deleted=0 " 
    * @param int $i_start, starting value for pagination
    * @param int $i_limit, number of records to fetch used for pagination
    * @param string $s_order_by, Column names to be ordered ex- " dt_created_on desc,i_is_deleted asc,id asc "
    * @returns array
    */
    public function fetch_multi($s_where=null,$i_start=null,$i_limit=null,$s_order_by=null)
    {}
    
	/****
    * Fetch Total records
    * @param string $s_where, ex- " status=1 AND deleted=0 " 
    * @returns int on success and FALSE if failed 
    */
    public function gettotal_info($s_where=null)
    {}         
    
	
	
	
	

	public function get_by_request_id($i_prayer_req_id,  $i_start_limit="", $i_no_of_page="") { 
        
		if("$i_start_limit" == "") {
			$sql = "SELECT c.id, 
								   c.i_prayer_req_id,
								   c.i_user_id, 
								   c.s_contents,
								   c.s_weekdays, 
								   c.dt_start_time,
								   c.dt_end_time,
								   c.dt_created_on, 
								   mst_c.s_country,
								   CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
								   u.s_profile_photo,
								   u.e_gender
								   
						FROM cg_bible_prayer_commitments c, cg_users u 
						LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=u.i_country_id
						WHERE c.i_user_id=u.id 
						    AND c.i_prayer_req_id = '".intval($i_prayer_req_id)."' 
						   ORDER BY c.dt_created_on DESC";
		}
		else {
			$sql = "SELECT c.id, 
								   c.i_prayer_req_id,
								   c.i_user_id, 
								   c.s_contents,
								   c.s_weekdays,  
								   c.dt_start_time,
								   c.dt_end_time,
								   c.dt_created_on, 
								   mst_c.s_country,
								   CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
								   u.s_profile_photo
								   
								   
					    FROM cg_bible_prayer_commitments c, cg_users u 
						LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=u.i_country_id
						WHERE c.i_user_id=u.id
						 AND c.i_prayer_req_id = '".intval($i_prayer_req_id)."' 
						 ORDER BY dt_created_on DESC LIMIT ".intval($i_start_limit).", ".intval($i_no_of_page);
		}

        
		$query = $this->db->query($sql); 
    //echo $this->db->last_query();
		$result_arr = $query->result_array();
		
		

		return $result_arr;
	}
	
	

	public function get_total_by_request_id($i_prayer_req_id) {
		$sql = "SELECT count(*) count 
						 
						 FROM cg_bible_prayer_commitments c, cg_users u
						 LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=u.i_country_id
						 WHERE c.i_user_id=u.id AND c.i_prayer_req_id = '".intval($i_prayer_req_id)."' 
						 order by c.dt_created_on";

		$query = $this->db->query($sql); //echo nl2br($sql);
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}



	public function insert($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert('cg_bible_prayer_commitments', $arr);
		//echo $this->db->last_query();
		return $this->db->insert_id();
	}

	public function update($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update('cg_bible_prayer_commitments', $arr, array('id'=>$id));
	}

	public function delete_by_id($id) {
		
		$sql = sprintf( 'DELETE FROM %sbible_prayer_commitments WHERE id =%s  ', $this->db->dbprefix, $id);

		$this->db->query($sql);
		
	}
	
	
	
	### union of e-intercession and prayer wall commitments
	
	public function get_all_commitments_by_user_id($i_user_id,  $i_start_limit="", $i_no_of_page="") { 
        
	
		 	$sql = sprintf(" (SELECT c.id commits_id, 
								   c.i_prayer_req_id as prayer_id,
								   c.i_user_id, 
								   c.s_contents, 
								   c.s_weekdays, 
								   c.s_time, 
								   c.dt_start_time,
								   c.dt_end_time,
								   c.dt_created_on, 
								   p.i_user_id as post_owner_id,
								   mst_c.s_country as s_country_name,
								   
								    '1'  as s_city,
									'1' as  s_state,
								  (SELECT CONCAT(u.s_first_name,' ', u.s_last_name)  FROM {$this->db->USERS} u WHERE  c.i_user_id=u.id) as  s_profile_name,
								  
								   u.s_profile_photo,
								    'prayer_post' s_type,
									p.s_description as prayer_desc,
									p.dt_insert_date as post_created_on,
								  (SELECT CONCAT(u.s_first_name,' ', u.s_last_name)  FROM {$this->db->USERS} u WHERE  p.i_user_id=u.id)
								  		as post_owner_name 	,
								 p.dt_start_date as post_start_date,
								 p.dt_end_date as post_end_date
								 							
								   
								   
					    FROM   %1\$sbible_prayer_request p
						LEFT JOIN  %1\$sbible_prayer_commitments c on p.id = c.i_prayer_req_id
						LEFT JOIN  %1\$susers u on u.id = c.i_user_id 
						LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=u.i_country_id
						
						WHERE  c.i_user_id = %2\$s )
						 
						 UNION 
						 
						 (SELECT et.id commits_id, 
								   et.i_id_intercession_wall_post as prayer_id,
								   et.i_user_id, 
								   et.s_contents, 
								   et.s_weekdays,
								   et.s_time as s_time, 
								   et.dt_start_time,
								   et.dt_end_time,
								   et.dt_created_on, 
								   et.i_user_id as post_owner_id,
								   (SELECT  mst_c.s_country  FROM {$this->db->COUNTRY} mst_c
								   			WHERE  mst_c.id = e.i_country_id) as s_country_name,
											
									e.i_city_id  as s_city,
									e.i_state_id as  s_state,
								   (SELECT CONCAT(u.s_first_name,' ', u.s_last_name)  FROM {$this->db->USERS} u WHERE  et.i_user_id=u.id) as  s_profile_name,
								   u.s_profile_photo,
								   'intercession_post' s_type ,
								  e.s_description as  prayer_desc,
								  e.dt_insert_date as post_created_on,
								  (SELECT a.s_name  FROM {$this->db->ADMIN_USER} a WHERE  e.i_user_id=a.id)
								  		as post_owner_name 	,
								 e.dt_start_date as post_start_date,
								 e.dt_end_date as post_end_date
								
								 							
								  
								   
					    FROM   %1\$sbible_intercession e
						LEFT JOIN  %1\$sbible_intercession_commitments et on e.id= et.i_id_intercession_wall_post
						
						LEFT JOIN  %1\$susers u on u.id = et.i_user_id
						LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=u.i_country_id
						
						WHERE et.i_user_id = %2\$s )
						 
						 
						 ORDER BY dt_created_on DESC LIMIT %3\$s, %4\$s", 
						 $this->db->dbprefix,
						 intval($i_user_id), 
						 intval($i_start_limit), 
						 intval($i_no_of_page));
		
       // echo nl2br($sql); exit;
		$query = $this->db->query($sql); 
    	//echo $this->db->last_query();
#echo nl2br($sql);
		$result_arr = $query->result_array();
		
		//pr($result_arr,1);

		return $result_arr;
	}
	
	

	public function get_all_commitments_total_by_request_id($i_user_id) {
		$sql = sprintf("SELECT count(*) count FROM (
								(SELECT c.id 
								   
												   
										FROM   %1\$sbible_prayer_request p
										LEFT JOIN  %1\$sbible_prayer_commitments c on p.id = c.i_prayer_req_id
										LEFT JOIN  %1\$susers u on u.id = c.i_user_id 
										LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=u.i_country_id
										
										WHERE  c.i_user_id = %2\$s )
						 
						 UNION
						 
						 (SELECT et.id 
								   
								  FROM   %1\$sbible_intercession e
								  LEFT JOIN  %1\$sbible_intercession_commitments et on e.id= et.i_id_intercession_wall_post
								  
								  LEFT JOIN  %1\$susers u on u.id = et.i_user_id
								  LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=u.i_country_id
								  
								  WHERE et.i_user_id = %2\$s )
		
						) derived_tbl
						 
						 ", $this->db->dbprefix, intval($i_user_id));

		$query = $this->db->query($sql); //echo nl2br($sql);
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}
	
	

}
