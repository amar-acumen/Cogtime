<?php
include_once(APPPATH.'models/base_model.php');
class Prayer_wall_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

	public function get_all_prayers($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY id DESC';
       
        
         $sql = "SELECT p.*, CONCAT(u.s_first_name,' ',u.s_last_name) posted_by,u.s_profile_photo,u.e_gender,mst_c.s_country, 
                u.i_city_id,u.i_state_id
                FROM 
                {$this->db->BIBLE_PRAYER_REQUEST} p 
                LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id 
                LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=u.i_country_id
                {$where} {$s_order_by} {$limit}";
        $res = $this->db->query($sql)->result_array();
        
        foreach($res as $key=>$val)
        {
            $res[$key]['testimony'] = $this->if_testimony_exists_againsr_prayer_wall($val['id']);
            
        }
       
        //pr($res,1);
        return $res;
    }
    
    public function get_count_all_prayers($where)
    {
        $sql = "SELECT COUNT(*) prayers FROM {$this->db->BIBLE_PRAYER_REQUEST} p
                    LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id 
                    LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=u.i_country_id
                    {$where}";
        $res = $this->db->query($sql)->result_array();
        return $res[0]['prayers'];
    }
	
	
	
    
    public function if_testimony_exists_againsr_prayer_wall($id)
    {

        $sql = "SELECT count(*) testimony
                FROM {$this->db->BIBLE_PRAYER_REQUEST_TESTIMONIES} t
                LEFT JOIN {$this->db->BIBLE_PRAYER_REQUEST} r ON r.id=t.i_prayer_req_id
                WHERE r.id={$id}
                "; 
        $res = $this->db->query($sql)->result_array();
        return $res[0]['testimony'];
    }
    
    public function get_testimonial_detail_by_prayer_wall_id($id)
    {
        $sql = "SELECT t.*, CONCAT(u.s_first_name,' ',u.s_last_name) posted_by,u.s_profile_photo,u.e_gender
                FROM {$this->db->BIBLE_PRAYER_REQUEST_TESTIMONIES} t
                LEFT JOIN {$this->db->BIBLE_PRAYER_REQUEST} p ON t.i_prayer_req_id = p.id
                LEFT JOIN {$this->db->USERS} u ON u.id = p.i_user_id
                WHERE p.id={$id}
                ";
        $res = $this->db->query($sql)->result_array();
        return $res[0];
    }
	
    public function insert_testimony($info)
    {
        $this->db->insert($this->db->BIBLE_PRAYER_REQUEST_TESTIMONIES,$info);
        $last_id = $this->db->insert_id();
        return $last_id;
    }
    public function update_testimony($info,$id)
    {
        $this->db->update($this->db->BIBLE_PRAYER_REQUEST_TESTIMONIES,$info,array('id'=>$id));
        //echo $this->db->last_query();
    }
    
    public function edit_prayer_wall($info,$id)
    {
        $this->db->update($this->db->BIBLE_PRAYER_REQUEST,$info,array('id'=>$id));
    }
    
    public function change_status_prayer_wall($info,$id)
    {
        $this->db->update($this->db->BIBLE_PRAYER_REQUEST,$info,array('id'=>$id));
        //echo $this->db->last_query();
    }
	
	
	public function insert($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->BIBLE_PRAYER_REQUEST, $arr); //echo $this->db->last_query();
		return $this->db->insert_id();
	}
	
	
	public function get_list_prayers_request($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY id DESC';
       $curr_date = date('Y-m-d');
        
      $sql = "SELECT p.*, 
				   	   CONCAT(u.s_first_name,' ',u.s_last_name) s_profile_name,
					   u.s_profile_photo,
					   u.e_gender,
					   mst_c.s_country, 
                	   u.i_city_id,
					   u.i_state_id,
					   DATE_FORMAT(p.dt_start_date , '%d/%m/%Y') as start_date,
					   DATE_FORMAT(p.dt_end_date, '%d/%m/%Y') as end_date,
					   DATE_FORMAT(p.dt_start_date , '%H:%i') as start_time,
					   DATE_FORMAT(p.dt_end_date, '%H:%i') as end_time,
					   (SELECT 'Y' FROM {$this->db->BIBLE_PRAYER_REQUEST} p1 WHERE DATE(p1.dt_end_date) < '{$curr_date}'
					    AND p1.id= p.id) as isExpired
                FROM 
                {$this->db->BIBLE_PRAYER_REQUEST} p 
                LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id 
                LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=u.i_country_id
                {$where} {$s_order_by} {$limit}"; //exit;
		//echo nl2br($sql); exit;

        $result_arr = $this->db->query($sql)->result_array(); //
		
        
        foreach($result_arr as $key=>$val)
        {
            $result_arr[$key]['testimony'] = $this->if_testimony_exists_againsr_prayer_wall($val['id']);
			$result_arr[$key]['total_commitments'] = $this->get_total_commitments_prayer_wall($val['id']);
			$result_arr[$key]['total_comments'] = $this->get_total_comments_prayer_wall($val['id']);

			$result_arr[$key]['CommitExists'] = $this->CheckIfCommitexists($val['id'], intval(decrypt($this->session->userdata('user_id'))));
            
        }
       
        return $result_arr;
    }
    
    public function get_count_list_prayers_request($where)
    {
         $sql = "SELECT  COUNT(*) TOTAL_COUNT FROM(
	 					SELECT p.id 
						FROM {$this->db->BIBLE_PRAYER_REQUEST} p
                    	LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id 
                    	LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=u.i_country_id
                    	{$where} ) as dv_tbl";
        $res = $this->db->query($sql)->result_array();
        return $res[0]['TOTAL_COUNT'];
    }
	
	
	
	public function get_total_commitments_prayer_wall($i_prayer_req_id) {
		$sql = sprintf("SELECT count(*) count 
						 
						 FROM %1\$sbible_prayer_commitments c, %1\$susers u
						 LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=u.i_country_id
						 WHERE c.i_user_id=u.id AND c.i_prayer_req_id = %2\$s 
						 order by c.dt_created_on", $this->db->dbprefix, intval($i_prayer_req_id));

		$query = $this->db->query($sql); //echo nl2br($sql);
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}
	
	public function get_by_id($id='')
    {
        
        $sql = "SELECT  p.*,
						mst_c.s_country,
						CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
						u.s_profile_photo,u.e_gender
                FROM 
                {$this->db->BIBLE_PRAYER_REQUEST} p 
				LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id 
                LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=u.i_country_id
                WHERE p.i_isenabled = 1  AND p.id  = {$id} "; //exit;
        $result_arr = $this->db->query($sql)->result_array(); //
		
		#$echo nl2br($sql); exit;
        
        foreach($result_arr as $key=>$val)
        {
            $result_arr[$key]['testimony'] = $this->if_testimony_exists_againsr_prayer_wall($val['id']);
			$result_arr[$key]['total_commitments'] = $this->get_total_commitments_prayer_wall($val['id']);
			$result_arr[$key]['total_comments'] = $this->get_total_comments_prayer_wall($val['id']);

			$result_arr[$key]['CommitExists'] = $this->CheckIfCommitexists($val['id'], intval(decrypt($this->session->userdata('user_id'))));
            
        }
       
        return $result_arr[0];
    }
	
	
	
	
	public function get_testimony_by_prayer_id($i_prayer_req_id) { 
        
		
			$sql = sprintf("SELECT p.*,
								   mst_c.s_country,
								   CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
								   u.s_profile_photo,
								   u.e_gender,
								   pt.s_description,
								   pt.dt_insert_date
								   
					    FROM  cg_bible_prayer_request_testimonies pt , cg_bible_prayer_request p
						LEFT JOIN cg_users u  ON p.i_user_id=u.id
						LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=u.i_country_id
						WHERE p.id = pt.i_prayer_req_id
						    AND pt.i_prayer_req_id = %2\$s 
						 ", 
						 $this->db->dbprefix,
						 intval($i_prayer_req_id));
	
		$query = $this->db->query($sql); 
    //echo $this->db->last_query();
		$result_arr = $query->result_array();

		return $result_arr[0];
	}
    
	
	public function delete_by_id($id) {
		
		## deleting commitments 
		 $sql = sprintf( 'DELETE FROM %sbible_prayer_commitments WHERE i_prayer_req_id=%s  ', $this->db->dbprefix, $id);

		$this->db->query($sql);
		
		#### deleting testimony
		 $sql = sprintf( 'DELETE FROM %sbible_prayer_request_testimonies WHERE i_prayer_req_id=%s  ', $this->db->dbprefix, $id);

		$this->db->query($sql);
		
		## deleting prayer request
		
		 $sql = sprintf( 'DELETE FROM %sbible_prayer_request WHERE id=%s  ', $this->db->dbprefix, $id);

		$this->db->query($sql);
		
	}
	
	public function CheckIfCommitexists($i_prayer_req_id ,$i_user_id)
    {

        $sql = "SELECT count(*) as commit_exists
                FROM cg_bible_prayer_commitments pc
                LEFT JOIN {$this->db->BIBLE_PRAYER_REQUEST} p ON p.id = pc.i_prayer_req_id
                WHERE p.id= {$i_prayer_req_id}
				 	 AND  pc.i_user_id = {$i_user_id}
                "; 
        $result = $this->db->query($sql)->result_array();
        return $result[0]['commit_exists'];
    }
    
    
    //------------- testimony FO start -------------------------
  /*  public function get_own_testimony_details_by_user_id($id ,$where='',$i_start=null,$i_limit=null,$s_order_by='' )
    {
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY testimony_id DESC';
        $sql = "SELECT t.id testimony_id,
                            p.id prayer_wall_req_id,
                            t.s_description testimony_desc,
                            t.dt_insert_date testimony_insert_date,
                            mst_c.s_country_name country,
                            p.s_description prayer_req_desc,
                            p.dt_start_date prayer_req_start_date,
                            p.dt_end_date prayer_req_end_date,
                            p.dt_insert_date prayer_req_insert_date,
                            CONCAT(u.s_first_name,' ',u.s_last_name) posted_by,
                            (SELECT 'Y' FROM {$this->db->BIBLE_PRAYER_REQUEST} p1 WHERE p1.i_user_id = '{$id}' AND p1.id=p.id
                            ) as owner_post
                FROM {$this->db->BIBLE_PRAYER_REQUEST_TESTIMONIES} t
                LEFT JOIN {$this->db->BIBLE_PRAYER_REQUEST} p ON t.i_prayer_req_id = p.id
                LEFT JOIN {$this->db->BIBLE_PRAYER_COMMITMENTS} c ON c.i_prayer_req_id  = p.id
                LEFT JOIN {$this->db->USERS} u ON u.id = p.i_user_id
                LEFT JOIN {$this->db->MST_COUNTRY} mst_c on mst_c.id=u.i_country_id
                WHERE (u.id={$id} OR c.i_user_id = {$id}) AND p.i_isenabled=1  {$where}
                {$s_order_by} {$limit}
                ";
                
        $res = $this->db->query($sql)->result_array();
        return $res;
    }
    public function get_count_all_testimony_detail_by_user_id($id,$where='')
    {
        $sql = "SELECT count(*) total_testimony
                FROM {$this->db->BIBLE_PRAYER_REQUEST_TESTIMONIES} t
                LEFT JOIN {$this->db->BIBLE_PRAYER_REQUEST} p ON t.i_prayer_req_id = p.id
                LEFT JOIN {$this->db->BIBLE_PRAYER_COMMITMENTS} c ON c.i_prayer_req_id  = p.id
                LEFT JOIN {$this->db->USERS} u ON u.id = p.i_user_id
                LEFT JOIN {$this->db->MST_COUNTRY} mst_c on mst_c.id=u.i_country_id
                WHERE (u.id={$id} OR c.i_user_id = {$id}) AND p.i_isenabled=1  {$where}
                ";
        $res = $this->db->query($sql)->result_array();
        return $res[0]['total_testimony'];
    }
  */  
	
	
	function get_my_tesimony_list($s_where=null,$s_like_where, $s_order_by=null ,$start_limit=null,$no_of_page=null, $timeout=''){
		if($timeout=='') {
			$timeout = $this->timeout;
		}

		$timestamp = time() - $timeout;
		
		if("$start_limit" == '') {
			$limit = '';
		}
		else {
			$start_limit = (int) $start_limit;
			$no_of_page = (int) $no_of_page;
			$limit = ' limit '.$start_limit.', '.$no_of_page;
		}
		//echo "where/ ".$s_where."---------------";
		//$ORDERBY = trim($s_order_by)!=""?" ORDER BY ".$s_order_by."":"ORDER BY id asc";
		$user_id = decrypt($this->session->userdata('user_id'));
		$sql = sprintf(" SELECT derived_tbl.* FROM (
							(SELECT 

								  t.id testimony_id,
								  p.id prayer_wall_req_id,
								  t.s_description testimony_desc,
								  t.dt_insert_date testimony_insert_date,
								  mst_c.s_country country,
                                  u.i_state_id state,
								  p.s_description prayer_req_desc,
								  p.dt_start_date prayer_req_start_date,
								  p.dt_end_date prayer_req_end_date,
								  p.dt_insert_date prayer_req_insert_date,
								  CONCAT(u.s_first_name,' ',u.s_last_name) posted_by
								  ,u.s_profile_photo,u.e_gender,
								  (SELECT 'Y' FROM {$this->db->BIBLE_PRAYER_REQUEST} p1 WHERE p1.i_user_id = '%5\$s' AND p1.id=p.id
								  ) as owner_post
								  
								FROM {$this->db->BIBLE_PRAYER_REQUEST_TESTIMONIES} t
								LEFT JOIN {$this->db->BIBLE_PRAYER_REQUEST} p ON t.i_prayer_req_id = p.id
								LEFT JOIN {$this->db->BIBLE_PRAYER_COMMITMENTS} c ON c.i_prayer_req_id  = p.id
								LEFT JOIN {$this->db->USERS} u ON u.id = p.i_user_id
								LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=u.i_country_id
								WHERE   p.i_isenabled=1  
								 
								 %1\$s   
							     )
                            
                            UNION
                            
                         ( SELECT 

								  t.id testimony_id,
								  p.id prayer_wall_req_id,
								  t.s_description testimony_desc,
								  t.dt_insert_date testimony_insert_date,
								  mst_c.s_country country,
                                  u.i_state_id state,
								  p.s_description prayer_req_desc,
								  p.dt_start_date prayer_req_start_date,
								  p.dt_end_date prayer_req_end_date,
								  p.dt_insert_date prayer_req_insert_date,
								  CONCAT(u.s_first_name,' ',u.s_last_name) posted_by,u.s_profile_photo,u.e_gender,
								  (SELECT 'Y' FROM {$this->db->BIBLE_PRAYER_REQUEST} p1 WHERE p1.i_user_id = '%5\$s' AND p1.id=p.id
								  ) as owner_post
								  
								FROM {$this->db->BIBLE_PRAYER_REQUEST_TESTIMONIES} t
								LEFT JOIN {$this->db->BIBLE_PRAYER_REQUEST} p ON t.i_prayer_req_id = p.id
								LEFT JOIN {$this->db->BIBLE_PRAYER_COMMITMENTS} c ON c.i_prayer_req_id  = p.id
								LEFT JOIN {$this->db->USERS} u ON u.id = p.i_user_id
								LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=u.i_country_id
								WHERE  p.i_isenabled=1   
							 %2\$s                                      
                             )) as  derived_tbl ORDER BY testimony_id DESC  %4\$s  "
						, $s_where,$s_like_where, $timestamp, $limit , $user_id);
                        
		//echo nl2br($sql); exit;

		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
		
		//pr($result_arr);
		return $result_arr;
	}
	
	public function gettotal_my_testimony_list($s_where=null,$s_like_where =null) {
		
		$user_id = decrypt($this->session->userdata('user_id'));
		$sql = sprintf(" SELECT count(*) as count FROM (
							(SELECT 
								  
								  t.id testimony_id
								  
								FROM {$this->db->BIBLE_PRAYER_REQUEST_TESTIMONIES} t
								LEFT JOIN {$this->db->BIBLE_PRAYER_REQUEST} p ON t.i_prayer_req_id = p.id
								LEFT JOIN {$this->db->BIBLE_PRAYER_COMMITMENTS} c ON c.i_prayer_req_id  = p.id
								LEFT JOIN {$this->db->USERS} u ON u.id = p.i_user_id
								LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=u.i_country_id
								WHERE  p.i_isenabled=1  
								 
								 %1\$s   
							     ORDER BY testimony_id DESC )
                            
                            UNION
                            
                          ( SELECT 
								  
								  t.id testimony_id
								  
								FROM {$this->db->BIBLE_PRAYER_REQUEST_TESTIMONIES} t
								LEFT JOIN {$this->db->BIBLE_PRAYER_REQUEST} p ON t.i_prayer_req_id = p.id
								LEFT JOIN {$this->db->BIBLE_PRAYER_COMMITMENTS} c ON c.i_prayer_req_id  = p.id
								LEFT JOIN {$this->db->USERS} u ON u.id = p.i_user_id
								LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=u.i_country_id
								WHERE  p.i_isenabled=1    
							 %2\$s                                      
                            ORDER BY testimony_id DESC )) as  derived_tbl  "
						, $s_where,$s_like_where, $timestamp, $user_id);

		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}
    
    
    
    function get_tesimony_list($s_where=null, $s_order_by=null, $start_limit=null,$no_of_page=null, $timeout=''){
        if($timeout=='') {
            $timeout = $this->timeout;
        }

        $timestamp = time() - $timeout;
        
        if("$start_limit" == '') {
            $limit = '';
        }
        else {
            $start_limit = (int) $start_limit;
            $no_of_page = (int) $no_of_page;
            $limit = ' limit '.$start_limit.', '.$no_of_page;
        }
        $ORDERBY = trim($s_order_by)!=""?" ORDER BY ".$s_order_by."":"ORDER BY testimony_id DESC";
        $user_id = decrypt($this->session->userdata('user_id'));
        
        $sql = "SELECT 
                                  t.id testimony_id,
                                  p.id prayer_wall_req_id,
                                  t.s_description testimony_desc,
                                  t.dt_insert_date testimony_insert_date,
                                  mst_c.s_country country,
                                  u.i_state_id state,
								  p.s_subject,
                                  p.s_description prayer_req_desc,
                                  p.dt_start_date prayer_req_start_date,
                                  p.dt_end_date prayer_req_end_date,
                                  p.dt_insert_date prayer_req_insert_date,
                                  CONCAT(u.s_first_name,' ',u.s_last_name) posted_by,u.s_profile_photo,u.e_gender,
                                  (SELECT 'Y' FROM {$this->db->BIBLE_PRAYER_REQUEST} p1 WHERE p1.i_user_id = '{$user_id}' AND p1.id=p.id
                                  ) as owner_post
                                  
                                FROM {$this->db->BIBLE_PRAYER_REQUEST_TESTIMONIES} t
                                LEFT JOIN {$this->db->BIBLE_PRAYER_REQUEST} p ON t.i_prayer_req_id = p.id
                                LEFT JOIN {$this->db->BIBLE_PRAYER_COMMITMENTS} c ON c.i_prayer_req_id  = p.id
                                LEFT JOIN {$this->db->USERS} u ON u.id = p.i_user_id
                                LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=u.i_country_id
                                WHERE   p.i_isenabled=1  
                                 {$s_where} GROUP BY p.id {$ORDERBY}  {$limit}
                                  ";
        $res = $this->db->query($sql)->result_array();
        return $res;
    }
    
    public function gettotal_testimony_list($s_where=null) {
        
        $user_id = decrypt($this->session->userdata('user_id'));
        
        
        $sql = "SELECT COUNT(*) total_testimony
                
                FROM {$this->db->BIBLE_PRAYER_REQUEST_TESTIMONIES} t
                LEFT JOIN {$this->db->BIBLE_PRAYER_REQUEST} p ON t.i_prayer_req_id = p.id
                LEFT JOIN {$this->db->BIBLE_PRAYER_COMMITMENTS} c ON c.i_prayer_req_id  = p.id
                LEFT JOIN {$this->db->USERS} u ON u.id = p.i_user_id
                LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=u.i_country_id
                WHERE   p.i_isenabled=1  
                 {$s_where}
                
                ";
        $res = $this->db->query($sql)->result_array();
        return $res[0]['total_testimony'];
    }
    
    //------------- testimony FO end -------------------------
    
	
	public function update_testimony_by_prayer_id($prayer_id = '',$desc){
		
		  $SQL = "UPDATE {$this->db->BIBLE_PRAYER_REQUEST_TESTIMONIES} SET s_description = '{$desc}' WHERE i_prayer_req_id = {$prayer_id}";
		$this->db->query($SQL); 
		//echo $SQL;
		return 1;
	}


	public function get_all_prayer_srch_result($s_where=null,$s_like_where, $start_limit=null,$no_of_page=null, $s_order_by=null, $like_search = true)
    {
        if("$start_limit" == '') {
            $limit = '';
        }
        else {
            $start_limit = (int) $start_limit;
            $no_of_page = (int) $no_of_page;
            $limit = ' limit '.$start_limit.', '.$no_of_page;
        }
        //echo $like_search;
		$curr_date = date('Y-m-d');
        
        $ORDERBY = trim($s_order_by)!=""?" ORDER BY ".$s_order_by."":"ORDER BY p.id DESC";
		if($like_search == true){
       		 
         $sql = "SELECT derived_tbl.* FROM(
				 (SELECT p.*, 
						   CONCAT(u.s_first_name,' ',u.s_last_name) s_profile_name,
						   u.s_profile_photo,
						   u.e_gender,
						   mst_c.s_country, 
						   u.i_city_id,
						   u.i_state_id,
						   s.s_state,
						   ct.s_city,
						   DATE_FORMAT(p.dt_start_date , '%d/%m/%Y') as start_date,
						   DATE_FORMAT(p.dt_end_date, '%d/%m/%Y') as end_date,
						   DATE_FORMAT(p.dt_start_date , '%H:%i') as start_time,
						   DATE_FORMAT(p.dt_end_date, '%H:%i') as end_time,
						   (SELECT 'Y' FROM {$this->db->BIBLE_PRAYER_REQUEST} p1 WHERE DATE(p1.dt_end_date) < '{$curr_date}'
							AND p1.id= p.id) as isExpired
               	FROM {$this->db->BIBLE_PRAYER_REQUEST} p 
                LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id 
                LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=u.i_country_id
				LEFT JOIN cg_city ct ON  ct.i_country_id = mst_c.id
				LEFT JOIN cg_state s ON  s.id =  ct.i_state_id
				WHERE 1 AND p.i_isenabled  = 1
                {$s_where}
				GROUP BY p.id 
				{$ORDERBY}
				
				
                 
                )
                UNION
                
                (SELECT p.*, 
						   CONCAT(u.s_first_name,' ',u.s_last_name) s_profile_name,
						   u.s_profile_photo,
						   u.e_gender,
						   mst_c.s_country, 
						   u.i_city_id,
						   u.i_state_id,
						   s.s_state,
						   ct.s_city,
						   DATE_FORMAT(p.dt_start_date , '%d/%m/%Y') as start_date,
						   DATE_FORMAT(p.dt_end_date, '%d/%m/%Y') as end_date,
						   DATE_FORMAT(p.dt_start_date , '%H:%i') as start_time,
						   DATE_FORMAT(p.dt_end_date, '%H:%i') as end_time,
						   (SELECT 'Y' FROM {$this->db->BIBLE_PRAYER_REQUEST} p1 WHERE DATE(p1.dt_end_date) < '{$curr_date}'
							AND p1.id= p.id) as isExpired
               	FROM {$this->db->BIBLE_PRAYER_REQUEST} p 
                LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id 
                LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=u.i_country_id
				LEFT JOIN cg_city ct ON  ct.i_country_id = mst_c.id
				LEFT JOIN cg_state s ON  s.id =  ct.i_state_id
				WHERE 1 AND p.i_isenabled  = 1
                {$s_like_where}
				GROUP BY p.id
                {$ORDERBY} )) as derived_tbl  {$limit}
        
        
                ";
		}
		else
		{
			
         $sql = "SELECT derived_tbl.* FROM(
				 (SELECT p.*, 
						   CONCAT(u.s_first_name,' ',u.s_last_name) s_profile_name,
						   u.s_profile_photo,
						   u.e_gender,
						   mst_c.s_country, 
						   u.i_city_id,
						   u.i_state_id,
						   s.s_state,
						   ct.s_city,
						   DATE_FORMAT(p.dt_start_date , '%d/%m/%Y') as start_date,
						   DATE_FORMAT(p.dt_end_date, '%d/%m/%Y') as end_date,
						   DATE_FORMAT(p.dt_start_date , '%H:%i') as start_time,
						   DATE_FORMAT(p.dt_end_date, '%H:%i') as end_time,
						   (SELECT 'Y' FROM {$this->db->BIBLE_PRAYER_REQUEST} p1 WHERE DATE(p1.dt_end_date) < '{$curr_date}'
							AND p1.id= p.id) as isExpired
               	FROM {$this->db->BIBLE_PRAYER_REQUEST} p 
                LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id 
                LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=u.i_country_id
				LEFT JOIN cg_city ct ON  ct.i_country_id = mst_c.id
				LEFT JOIN cg_state s ON  s.id =  ct.i_state_id
				WHERE 1 AND p.i_isenabled  = 1
				AND p.dt_end_date >= '{$curr_date}'
                {$s_where}
				GROUP BY p.id 
				{$ORDERBY}
				
                )) as derived_tbl  {$limit}
        
        
                ";
		}
			//echo nl2br($sql); exit;
        $res = $this->db->query($sql)->result_array();
		
		foreach($res as $key=>$val)
        {
            $res[$key]['testimony'] = $this->if_testimony_exists_againsr_prayer_wall($val['id']);
			$res[$key]['total_commitments'] = $this->get_total_commitments_prayer_wall($val['id']);
			$result_arr[$key]['total_comments'] = $this->get_total_comments_prayer_wall($val['id']);

			$res[$key]['CommitExists'] = $this->CheckIfCommitexists($val['id'], intval(decrypt($this->session->userdata('user_id'))));
            
        }
		
        return $res;
    }
    
    
    
    public function gettotal_prayer_srch_result($s_where=null,$s_like_where =null,$like_search = true)
    {
        if($like_search == true){
			
			$sql = "SELECT count(*) as count FROM(
                    
                  (SELECT p.*
               			FROM {$this->db->BIBLE_PRAYER_REQUEST} p 
               			LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id 
                		LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=u.i_country_id
						LEFT JOIN cg_city ct ON  ct.i_country_id = mst_c.id
						LEFT JOIN cg_state s ON  s.id =  ct.i_state_id
						WHERE 1 AND p.i_isenabled  = 1
                    	{$s_where}
						GROUP BY p.id 
                    )
               UNION
                    (SELECT p.*
               			FROM {$this->db->BIBLE_PRAYER_REQUEST} p 
               			LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id 
                		LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=u.i_country_id
						LEFT JOIN cg_city ct ON  ct.i_country_id = mst_c.id
						LEFT JOIN cg_state s ON  s.id =  ct.i_state_id
						WHERE 1 AND p.i_isenabled  = 1
                    	{$s_like_where}
						GROUP BY p.id 
                    )) as derived_tbl
                
                ";
		}
		else{
			
			$sql = "SELECT count(*) as count FROM(
                    
						  (SELECT p.*
								FROM {$this->db->BIBLE_PRAYER_REQUEST} p 
								LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id 
								LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=u.i_country_id
								LEFT JOIN cg_city ct ON  ct.i_country_id = mst_c.id
								LEFT JOIN cg_state s ON  s.id =  ct.i_state_id
								WHERE 1 AND p.i_isenabled  = 1
								AND p.dt_end_date >= '{$curr_date}'
								{$s_where}
								GROUP BY p.id 
							)) as derived_tbl
					
					";
		}
		
		
				//echo nl2br($sql) ; exit;
        $res = $this->db->query($sql)->result_array();
        return $res[0]['count'];
    }
	
	
	// new functions for comments
	
	public function get_total_comments_prayer_wall($i_prayer_req_id) {
		/*$sql = sprintf("SELECT count(*) count 
						 
						 FROM %1\$prayer_wall_comments c, %1\$susers u
						 LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=u.i_country_id
						 WHERE c.i_user_id=u.id AND c.i_prayer_id = %2\$s 
						 order by c.dt_commented_on", $this->db->dbprefix, intval($i_prayer_req_id));*/
		
		$sql="select count(*) as count from cg_prayer_wall_comments where i_prayer_id=".$i_prayer_req_id;
		$query = $this->db->query($sql); //echo nl2br($sql);exit;
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}

	 
   public function get_comment_by_request_id($prayer_id)
	 {
		$query=$this->db->query("select pc.*,CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,u.s_profile_photo,u.e_gender from cg_prayer_wall_comments pc,cg_users u where pc.i_user_id=u.id and pc.i_prayer_id=".$prayer_id);
		$res=$query->result_array();
		return $res;
	 }  
         
         function get_owner_prayer_wall_by_id($i_user_id,$s_where,$i_start_limit='', $i_no_of_page=''){
   // echo $s_where;
   
    //die('j');
    //{$sql="select distinct c.* ,(SELECT count(*) FROM cg_bible_prayer_commitments RC WHERE RC.i_prayer_req_id = c.id) as total_comments from cg_users u ,cg_bible_prayer_request c ".$s_where." and c.i_user_id='".$i_user_id."'and u.i_isdeleted='1'  limit ".$i_start_limit.",".$i_no_of_page;}
    {$sql="select c.*, p.s_subject,p.s_description,p.e_request_type from cg_bible_prayer_commitments c,cg_bible_prayer_request p ".$s_where." and c.i_user_id = $i_user_id and c.i_prayer_req_id = p.id ORDER BY c.id DESC limit ".$i_start_limit.",".$i_no_of_page;}
//echo $sql;
$query=$this->db->query($sql);
$result_arr=$query->result_array();
//echo $sql;exit;
//echo $i_start_limit;
//pr($result_arr,1);
return $result_arr;
    
}
public function get_total_owner_prayer_wall_by_id($i_user_id,$s_where)
{
//$sql="select count(distinct c.id) as count from cg_users u ,cg_bible_prayer_request c ".$s_where." and c.i_user_id='".$i_user_id."'and u.i_isdeleted='1' ";
$sql= "select count(*) from cg_bible_prayer_commitments ".$s_where." and i_user_id = $i_user_id ORDER BY id DESC";
//echo $sql;
$query=$this->db->query($sql);
$result_arr=$query->result_array();
return $result_arr['0']['count'];
}
function get_all_commitmen_by_id($i_prayer_req_id){
     {$sql="select * from cg_bible_prayer_commitments where i_prayer_req_id=$i_prayer_req_id ";}
#echo $sql;
$query=$this->db->query($sql);
$result_arr=$query->result_array();
//echo $sql;exit;
//echo $i_start_limit;
//pr($result_arr,1);
return $result_arr;
    
}
}