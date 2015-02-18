<?php
include_once(APPPATH.'models/base_model.php');
class Intercession_model extends Base_model
{
    
    public function __construct() 
    {
        parent::__construct();
    }
    

    
	public function get_all_intercession($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY id DESC';
  		
		$curr_date = date('Y-m-d');
		 
        $sql = "SELECT i.*, CONCAT(a.s_name,' ',a.s_last_name) posted_by_admin, 
						mst_c.s_country,
						ct.s_city,
						s.s_state,
						(SELECT 'Y' FROM {$this->db->BIBLE_INTERCESSION} p1 WHERE DATE(p1.dt_end_date) < '{$curr_date}'
					    AND p1.id= i.id) as isExpired
				
                FROM {$this->db->BIBLE_INTERCESSION} i
                LEFT JOIN {$this->db->ADMIN_USER} a on i.i_user_id=a.id
				LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=i.i_country_id
				LEFT JOIN cg_city ct ON  ct.i_country_id = mst_c.id
				LEFT JOIN cg_state s ON  s.id =  ct.i_state_id
                {$where} GROUP BY i.id {$s_order_by} {$limit}";
               
			  //echo nl2br($sql) ;
        $res = $this->db->query($sql)->result_array();
  // pr($res,1);
	   if(count($res)){
			foreach($res as $key=>$val)
			{
				$res[$key]['testimony'] = $this->if_testimony_exists_against_intercession($val['id']);
				$res[$key]['commit_exists'] = $this->CheckIfCommitexists($val['id'] , intval(decrypt($this->session->userdata('user_id'))));
				$res[$key]['total_commitments'] = $this->get_total_by_request_id($val['id']);
				
			}
	   }
        //pr($res,1);
        return $res;
    }
    
    public function if_testimony_exists_against_intercession($id)
    {
        
        $sql = "SELECT count(*) testimony
				FROM {$this->db->BIBLE_INTERCESSION_WALL_POST_TESTIMONY} t
				LEFT JOIN {$this->db->BIBLE_INTERCESSION} i ON i.id = t.i_id_intercession_wall_post
				WHERE i.id={$id}
				";
        $res = $this->db->query($sql)->result_array();
        
        //echo "id : ".$id." - ".$res[0]['testimony'];
        return $res[0]['testimony'];
        
    }
    
    public function get_count_all_intercession($where)
    {
        $sql = "SELECT COUNT(*) prayers FROM(
	   				SELECT i.id
	   				 FROM {$this->db->BIBLE_INTERCESSION} i
					LEFT JOIN {$this->db->ADMIN_USER} a on i.i_user_id=a.id
					LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=i.i_country_id
					LEFT JOIN cg_city ct ON  ct.i_country_id = mst_c.id
					LEFT JOIN cg_state s ON  s.id =  ct.i_state_id
				
                {$where}
				GROUP BY i.id) as tbl
				";/*LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id*/
        $res = $this->db->query($sql)->result_array(); 
        return $res[0]['prayers'];
    }
    
    public function insert_intercession($info)
    {
        $this->db->insert($this->db->BIBLE_INTERCESSION,$info); //echo $this->db->last_query(); exit;
        $last_id = $this->db->insert_id();
        return $last_id;
    }
    public function get_info_by_intercession_id($id)
    {
        $sql = "SELECT i.*,CONCAT(a.s_name,' ',a.s_last_name) posted_by FROM {$this->db->BIBLE_INTERCESSION} i
                LEFT JOIN {$this->db->ADMIN_USER} a ON a.id=i.i_user_id WHERE i.id={$id}";
        $res = $this->db->query($sql)->result_array();
        return $res[0];
    }
    public function edit_intercession($info,$id)
    {
        $this->db->update($this->db->BIBLE_INTERCESSION,$info,array('id'=>$id));
        //echo "last q: ".$this->db->last_query();
        
    }
    public function delete_by_id($id)
    {
        $sql = "DELETE FROM {$this->db->BIBLE_INTERCESSION} WHERE id={$id}";
        $this->db->query($sql);
        
        $sql1= "DELETE FROM {$this->db->BIBLE_INTERCESSION_WALL_POST_TESTIMONY} WHERE i_id_intercession_wall_post={$id}";
        $this->db->query($sql1);
    }
    
    public function change_status_intercession($data,$id)
    {
        $this->db->update($this->db->BIBLE_INTERCESSION, $data ,array('id'=>$id));
    }
	
    
    public function get_testimony_by_intercession_id($id)
    {
        $sql = "SELECT t.*, CONCAT(a.s_name,' ',a.s_last_name) posted_by_admin
                FROM {$this->db->BIBLE_INTERCESSION_WALL_POST_TESTIMONY} t
                LEFT JOIN {$this->db->BIBLE_INTERCESSION} i ON i.id = t.i_id_intercession_wall_post
                LEFT JOIN {$this->db->ADMIN_USER} a on i.i_user_id=a.id
                WHERE i.id = {$id}
                ";
        $res = $this->db->query($sql)->result_array();
        return $res[0];
    }
    
    
    public function insert_testimony($info)
    {
        $this->db->insert($this->db->BIBLE_INTERCESSION_WALL_POST_TESTIMONY,$info);
        $last_id = $this->db->insert_id();
        return $last_id;
    }
    
    public function update_testimony($info,$id)
    {
        $this->db->update($this->db->BIBLE_INTERCESSION_WALL_POST_TESTIMONY,$info,array('id'=>$id));
    }
	
	
	
	
	### intercession commitments
	
	
	public function get_by_request_id($i_prayer_req_id,  $i_start_limit="", $i_no_of_page="") { 
        
		if("$i_start_limit" == "") {
			$sql = sprintf("SELECT c.id, 
								   c.i_id_intercession_wall_post,
								   c.i_user_id, 
								   c.s_contents, 
								   c.dt_start_time,
								   c.dt_end_time,
								   c.dt_created_on, 
								   mst_c.s_country,
								   CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
								   u.s_profile_photo
								   
						FROM %1\$sbible_intercession_commitments c, %1\$susers u 
						LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=u.i_country_id
						
						WHERE c.i_user_id=u.id 
						    AND c.i_id_intercession_wall_post = %2\$s 
						   ORDER BY c.dt_created_on DESC", 
						   $this->db->dbprefix, 
						   intval($i_prayer_req_id));
		}
		else {
			$sql = sprintf("SELECT c.id, 
								   c.i_id_intercession_wall_post,
								   c.i_user_id, 
								   c.s_contents, 
								   c.dt_start_time,
								   c.dt_end_time,
								   c.dt_created_on, 
								   mst_c.s_country,
								   CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
								   u.s_profile_photo
								   
								   
					    FROM %1\$sbible_intercession_commitments c, %1\$susers u 
						LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=u.i_country_id
						
						WHERE c.i_user_id=u.id
						 AND c.i_id_intercession_wall_post = %2\$s 
						 ORDER BY dt_created_on DESC LIMIT %3\$s, %4\$s", 
						 $this->db->dbprefix,
						 intval($i_prayer_req_id), 
						 intval($i_start_limit), 
						 intval($i_no_of_page));
		}

        //echo $sql; exit;
		$query = $this->db->query($sql); 
    //echo $this->db->last_query();
		$result_arr = $query->result_array();
		
		

		return $result_arr;
	}
	
	

	public function get_total_by_request_id($i_prayer_req_id) {
		$sql = sprintf("SELECT count(*) count 
						 
						 FROM %1\$sbible_intercession_commitments c, %1\$susers u
						 LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=u.i_country_id
						 WHERE c.i_user_id=u.id AND c.i_id_intercession_wall_post = %2\$s 
						 order by c.dt_created_on", $this->db->dbprefix, intval($i_prayer_req_id));

		$query = $this->db->query($sql); //echo nl2br($sql);
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}
	
	
	public function insert_commitments($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert('cg_bible_intercession_commitments', $arr);
		//echo $this->db->last_query();
		return $this->db->insert_id();
	}

	
	
	public function update_commitments($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update('cg_bible_intercession_commitments', $arr, array('id'=>$id));
	}
	
	public function delete_commitments_by_id($id) {
		
		$sql = sprintf( 'DELETE FROM %sbible_intercession_commitments WHERE id =%s  ', $this->db->dbprefix, $id);

		$this->db->query($sql);
		
	}
	
	public function CheckIfCommitexists($i_id_intercession_wall_post  ,$i_user_id)
    {

        $sql = "SELECT count(*) as commit_exists
                FROM cg_bible_intercession_commitments pc
                LEFT JOIN {$this->db->BIBLE_INTERCESSION} p ON p.id = pc.i_id_intercession_wall_post 
                WHERE p.id= {$i_id_intercession_wall_post }
				 	 AND  pc.i_user_id = {$i_user_id}
                "; 
        $result = $this->db->query($sql)->result_array();
        return $result[0]['commit_exists'];
    }
    
    //------------- testimony FO start -------------------------
    public function get_testimony_details($i_start=null,$i_limit=null,$s_order_by='' )
    {
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY testimony_id DESC';
        $sql = "SELECT t.id testimony_id,
                            i.id prayer_wall_req_id,
                            t.s_message testimony_desc,
                            t.dt_insert_time testimony_insert_date,
                            mst_c.s_country country,
                            i.s_description intercession_desc,
                            i.dt_start_date intercession_start_date,
                            i.dt_end_date intercession_end_date,
                            i.dt_insert_date intercession_insert_date,
                            CONCAT(a.s_name,' ',a.s_last_name) posted_by
                FROM {$this->db->BIBLE_INTERCESSION_WALL_POST_TESTIMONY} t
                LEFT JOIN {$this->db->BIBLE_INTERCESSION} i ON t.i_id_intercession_wall_post = i.id
                LEFT JOIN {$this->db->ADMIN_USER} a ON a.id = i.i_user_id
                LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=i.i_country_id
				
                WHERE i.i_is_enable=1
                {$s_order_by} {$limit}
                ";
        
        $res = $this->db->query($sql)->result_array();
        return $res;
    }
    public function get_count_all_testimony()
    {
        $sql = "SELECT count(*) total_testimony
                FROM {$this->db->BIBLE_INTERCESSION_WALL_POST_TESTIMONY} t
                LEFT JOIN {$this->db->BIBLE_INTERCESSION} i ON t.i_id_intercession_wall_post = i.id
                LEFT JOIN {$this->db->ADMIN_USER} a ON a.id = i.i_user_id
                LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=i.i_country_id
                WHERE i.i_is_enable=1
                ";
        $res = $this->db->query($sql)->result_array();
        return $res[0]['total_testimony'];
    }
    
    
    
    
    
    public function get_all_intercession_testimony($s_where=null,$s_like_where, $start_limit=null,$no_of_page=null, $timeout='',$s_order_by=null )
    {
        if("$start_limit" == '') {
            $limit = '';
        }
        else {
            $start_limit = (int) $start_limit;
            $no_of_page = (int) $no_of_page;
            $limit = ' limit '.$start_limit.', '.$no_of_page;
        }
        
        
        $ORDERBY = trim($s_order_by)!=""?" ORDER BY ".$s_order_by."":"ORDER BY testimony_id DESC";
        
         $sql = "SELECT derived_tbl.* FROM(
                (SELECT t.id testimony_id,
                            i.id prayer_wall_req_id,
                            t.s_message testimony_desc,
                            t.dt_insert_time testimony_insert_date,
                            mst_c.s_country country,
							ct.s_city,
							s.s_state,
                            i.s_description intercession_desc,
                            i.dt_start_date intercession_start_date,
                            i.dt_end_date intercession_end_date,
                            i.dt_insert_date intercession_insert_date,
                            CONCAT(a.s_name,' ',a.s_last_name) posted_by
                FROM {$this->db->BIBLE_INTERCESSION_WALL_POST_TESTIMONY} t
                LEFT JOIN {$this->db->BIBLE_INTERCESSION} i ON t.i_id_intercession_wall_post = i.id
                LEFT JOIN {$this->db->ADMIN_USER} a ON a.id = i.i_user_id
                LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=i.i_country_id
				LEFT JOIN cg_city ct ON  ct.i_country_id = mst_c.id
				LEFT JOIN cg_state s ON  s.id =  ct.i_state_id
                WHERE i.i_is_enable=1
                {$s_where}
				GROUP BY t.id
                 
                )
                UNION
                
                (SELECT t.id testimony_id,
                            i.id prayer_wall_req_id,
                            t.s_message testimony_desc,
                            t.dt_insert_time testimony_insert_date,
                            mst_c.s_country country,
							ct.s_city,
							s.s_state,
                            i.s_description intercession_desc,
                            i.dt_start_date intercession_start_date,
                            i.dt_end_date intercession_end_date,
                            i.dt_insert_date intercession_insert_date,
                            CONCAT(a.s_name,' ',a.s_last_name) posted_by
                FROM {$this->db->BIBLE_INTERCESSION_WALL_POST_TESTIMONY} t
                LEFT JOIN {$this->db->BIBLE_INTERCESSION} i ON t.i_id_intercession_wall_post = i.id
                LEFT JOIN {$this->db->ADMIN_USER} a ON a.id = i.i_user_id
                LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=i.i_country_id
				LEFT JOIN cg_city ct ON  ct.i_country_id = mst_c.id
				LEFT JOIN cg_state s ON  s.id =  ct.i_state_id
                WHERE i.i_is_enable=1
                {$s_like_where}
				GROUP BY t.id
                 )) as derived_tbl {$ORDERBY} {$limit}
        
        
                ";
        $res = $this->db->query($sql)->result_array();
        return $res;
    }
    
    
    
    public function gettotal_intercession_testimony($s_where=null,$s_like_where =null)
    {
        $sql = "SELECT count(*) as count FROM(
                    
                    (SELECT t.id testimony_id
                    FROM {$this->db->BIBLE_INTERCESSION_WALL_POST_TESTIMONY} t
                    LEFT JOIN {$this->db->BIBLE_INTERCESSION} i ON t.i_id_intercession_wall_post = i.id
                    LEFT JOIN {$this->db->ADMIN_USER} a ON a.id = i.i_user_id
                    LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=i.i_country_id
					LEFT JOIN cg_city ct ON  ct.i_country_id = mst_c.id
					LEFT JOIN cg_state s ON  s.id =  ct.i_state_id
                    WHERE i.i_is_enable=1
                    {$s_where}
					GROUP BY t.id
                    )
               UNION
                    (SELECT t.id testimony_id
                    FROM {$this->db->BIBLE_INTERCESSION_WALL_POST_TESTIMONY} t
                    LEFT JOIN {$this->db->BIBLE_INTERCESSION} i ON t.i_id_intercession_wall_post = i.id
                    LEFT JOIN {$this->db->ADMIN_USER} a ON a.id = i.i_user_id
                    LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=i.i_country_id
					LEFT JOIN cg_city ct ON  ct.i_country_id = mst_c.id
					LEFT JOIN cg_state s ON  s.id =  ct.i_state_id
                    WHERE i.i_is_enable=1
                    {$s_like_where}
					GROUP BY t.id
                    )) as derived_tbl
                
                ";
        $res = $this->db->query($sql)->result_array();
        return $res[0]['count'];
    }
    
    
    public function get_intercession_testimony($s_where=null, $start_limit=null,$no_of_page=null, $timeout='',$s_order_by=null )
    {
        if("$start_limit" == '') {
            $limit = '';
        }
        else {
            $start_limit = (int) $start_limit;
            $no_of_page = (int) $no_of_page;
            $limit = ' limit '.$start_limit.', '.$no_of_page;
        }
        
        
        $ORDERBY = trim($s_order_by)!=""?" ORDER BY ".$s_order_by."":"ORDER BY testimony_id DESC";
        
        $sql = "SELECT t.id testimony_id,
                            i.id prayer_wall_req_id,
                            t.s_message testimony_desc,
                            t.dt_insert_time testimony_insert_date,
                            mst_c.s_country country,
                            i.i_state_id state,
							i.s_subject s_subject,
                            i.s_description intercession_desc,
                            i.dt_start_date intercession_start_date,
                            i.dt_end_date intercession_end_date,
                            i.dt_insert_date intercession_insert_date,
                            CONCAT(a.s_name,' ',a.s_last_name) posted_by,
							ct.s_city,
							s.s_state
                FROM {$this->db->BIBLE_INTERCESSION_WALL_POST_TESTIMONY} t
                LEFT JOIN {$this->db->BIBLE_INTERCESSION} i ON t.i_id_intercession_wall_post = i.id
                LEFT JOIN {$this->db->BIBLE_INTERCESSION_COMMITMENTS} c on c.i_id_intercession_wall_post=i.id
                LEFT JOIN {$this->db->ADMIN_USER} a ON a.id = i.i_user_id
                LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=i.i_country_id
				LEFT JOIN cg_city ct ON  ct.i_country_id = mst_c.id
				LEFT JOIN cg_state s ON  s.id =  ct.i_state_id
                WHERE i.i_is_enable=1
                {$s_where} GROUP BY testimony_id {$ORDERBY} {$limit}
                 ";
                
        $res = $this->db->query($sql)->result_array();
        return $res;
        
    }
    public function get_total_intercession_testimony($s_where=null)
    {
         $sql = "SELECT count(*) as count FROM( SELECT t.id
                    
                    FROM {$this->db->BIBLE_INTERCESSION_WALL_POST_TESTIMONY} t
                    LEFT JOIN {$this->db->BIBLE_INTERCESSION} i ON t.i_id_intercession_wall_post = i.id
                    LEFT JOIN {$this->db->BIBLE_INTERCESSION_COMMITMENTS} c on c.i_id_intercession_wall_post=i.id
                    LEFT JOIN {$this->db->ADMIN_USER} a ON a.id = i.i_user_id
                    LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=i.i_country_id
                    WHERE i.i_is_enable=1
                    {$s_where} GROUP BY t.id) as derived_tbl ";
        $res = $this->db->query($sql)->result_array();
        return $res[0]['count'];
    }
        
    //------------- testimony FO end -------------------------
	
	
	 public function get_total_intercession($where)
    {
       $sql = "SELECT COUNT(*) prayers FROM {$this->db->BIBLE_INTERCESSION} i
                {$where}
				
				";/*LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id*/
        $res = $this->db->query($sql)->result_array();
        return $res[0]['prayers'];
    }
	
	
	
	
	#### new added for e-intercession
	
	/*public function get_all_intercession_srch_result($s_where=null,$s_like_where, $start_limit=null,$no_of_page=null, $s_order_by=null )
    {
        if("$start_limit" == '') {
            $limit = '';
        }
        else {
            $start_limit = (int) $start_limit;
            $no_of_page = (int) $no_of_page;
            $limit = ' limit '.$start_limit.', '.$no_of_page;
        }
        
        
        $ORDERBY = trim($s_order_by)!=""?" ORDER BY ".$s_order_by."":"ORDER BY i.id DESC";
        
         $sql = "SELECT derived_tbl.* FROM(
				 (SELECT   i.*, 
						   CONCAT(a.s_first_name,' ',a.s_last_name) s_profile_name,
						   mst_c.s_country, 
						   s.s_state,
						   ct.s_city,
						   DATE_FORMAT(i.dt_start_date , '%d/%m/%Y') as start_date,
						   DATE_FORMAT(i.dt_end_date, '%d/%m/%Y') as end_date,
						   DATE_FORMAT(i.dt_start_date , '%H:%i') as start_time,
						   DATE_FORMAT(i.dt_end_date, '%H:%i') as end_time,
						   (SELECT 'Y' FROM {$this->db->BIBLE_INTERCESSION} i1  WHERE DATE(i1.dt_end_date) < '{$curr_date}'
							AND i1.id= i.id) as isExpired
						  
						  FROM {$this->db->BIBLE_INTERCESSION} i
						  LEFT JOIN {$this->db->ADMIN_USER} a on i.i_user_id=a.id
						  LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=i.i_country_id
						  LEFT JOIN cg_city ct ON  ct.i_country_id = mst_c.id
						  LEFT JOIN cg_state s ON  s.id =  ct.i_state_id			
						  WHERE 1 AND i.i_is_enable  = 1
						  {$s_where}
						  GROUP BY i.id 
						  {$ORDERBY}
				
                )
				          
       
                UNION
                
                (SELECT  i.*, 
						   CONCAT(a.s_first_name,' ',a.s_last_name) s_profile_name,
						   mst_c.s_country, 
						   s.s_state,
						   ct.s_city,
						   DATE_FORMAT(i.dt_start_date , '%d/%m/%Y') as start_date,
						   DATE_FORMAT(i.dt_end_date, '%d/%m/%Y') as end_date,
						   DATE_FORMAT(i.dt_start_date , '%H:%i') as start_time,
						   DATE_FORMAT(i.dt_end_date, '%H:%i') as end_time,
						   (SELECT 'Y' FROM {$this->db->BIBLE_INTERCESSION} i1  WHERE DATE(i1.dt_end_date) < '{$curr_date}'
							AND i1.id= i.id) as isExpired
						  
						  FROM {$this->db->BIBLE_INTERCESSION} i
						  LEFT JOIN {$this->db->ADMIN_USER} a on i.i_user_id=a.id
						  LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=i.i_country_id
						  LEFT JOIN cg_city ct ON  ct.i_country_id = mst_c.id
						  LEFT JOIN cg_state s ON  s.id =  ct.i_state_id			
						  WHERE 1 AND i.i_is_enable  = 1
                		  {$s_like_where}
						  GROUP BY i.id
                		 {$ORDERBY} )
					) as derived_tbl  {$limit}
        
                ";
				//echo nl2br($sql); exit;
        $res = $this->db->query($sql)->result_array();
		
		foreach($res as $key=>$val)
        {
            $res[$key]['testimony'] = $this->if_testimony_exists_against_intercession($val['id']);
			$res[$key]['commit_exists'] = $this->CheckIfCommitexists($val['id'] 
																, intval(decrypt($this->session->userdata('user_id'))));
			$res[$key]['total_commitments'] = $this->get_total_by_request_id($val['id']);
            
        }
		
        return $res;
    }
    
    
    
    public function gettotal_intercession_srch_result($s_where=null,$s_like_where =null)
    {
        $sql = "SELECT count(*) as count FROM(
                    
                  (SELECT i.*
               			  FROM {$this->db->BIBLE_INTERCESSION} i
						  LEFT JOIN {$this->db->ADMIN_USER} a on i.i_user_id=a.id
						  LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=i.i_country_id
						  LEFT JOIN cg_city ct ON  ct.i_country_id = mst_c.id
						  LEFT JOIN cg_state s ON  s.id =  ct.i_state_id			
						  WHERE 1 AND i.i_is_enable  = 1
                    	  {$s_where}
                    )
                UNION
                    (SELECT i.*
               			FROM {$this->db->BIBLE_INTERCESSION} i
						  LEFT JOIN {$this->db->ADMIN_USER} a on i.i_user_id=a.id
						  LEFT JOIN {$this->db->COUNTRY} mst_c on mst_c.id=i.i_country_id
						  LEFT JOIN cg_city ct ON  ct.i_country_id = mst_c.id
						  LEFT JOIN cg_state s ON  s.id =  ct.i_state_id			
						  WHERE 1 AND i.i_is_enable  = 1
                    	  {$s_like_where}
                    )) as derived_tbl
                
                ";
				//echo nl2br($sql) ; exit;
        $res = $this->db->query($sql)->result_array();
        return $res[0]['count'];
    }
	*/

    
	public function getSkippedPrayerClick_IDs($s_where=''){
		
		 $user_id =  intval(decrypt($this->session->userdata('user_id')));
		 $sql = "SELECT i_request_id
               			  FROM cg_skip_prayer_click	
						  WHERE 1 AND i_user_id = {$user_id} {$s_where}";
		 $res = $this->db->query($sql)->result_array();
		 
		 if(count($res)){
			 $result_arr = array();
			 foreach($res as $k=> $val){
				 $result_arr[] = $val['i_request_id'];
			 }
		 }
		 
		 return  $result_arr;
	}
	public function get_commitments_by_id($id)
	{
	$sql=$this->db->query("select * from cg_bible_intercession_commitments where i_id_intercession_wall_post=".$id);
	$res=$sql->result_array();
	return $res;
	}
	
	public function delete_commitment($id)
	{
	$sql=$this->db->query('delete from cg_bible_intercession_commitments where id='.$id);
	}
}// end of model