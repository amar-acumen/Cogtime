<?php

class Church_ring_post_comments_model extends CI_Model {
	
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
    {
      //NOT EDITED
      try
      {
           $ret_= array();
           $s_where = ( empty($s_where) )? " WHERE 1=1 ": $s_where;
           $s_qry = "SELECT  A.*  "
                    ." FROM ". $this->db->USER_RING_POST_COMMENTS ." A " 
                    .$s_where; 

          //////////For Pagination///////////*don't change*/
          //$s_qry=str_replace("'","''",$s_qry);//for string operation in procedure
          $s_qry= $s_qry.(trim($s_order_by)!=""? " ORDER BY ".$s_order_by."": " ORDER BY id asc");
          $s_qry= $s_qry.(is_numeric($i_start) && is_numeric($i_limit)?" LIMIT ".intval($i_start).",".intval($i_limit):"");
          #echo $s_qry;
          //////////end For Pagination//////////                
                
          $rs=$this->db->query($s_qry);
          $i_cnt=0;
          if(is_array($rs->result()))
          {
              $ret_ = $rs->result_array();
              $rs->free_result();          
          }
          unset($s_qry,$rs,$row,$i_cnt,$s_where,$i_start,$i_limit);
          return $ret_;
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
                   
    }
    
	/****
    * Fetch Total records
    * @param string $s_where, ex- " status=1 AND deleted=0 " 
    * @returns int on success and FALSE if failed 
    */
    public function gettotal_info($s_where=null)
    {
        try
        {
          $ret_=0;
         
		 $s_qry = "SELECT COUNT(*) AS i_total "
                    ." FROM ". $this->db->USER_RING_POST_COMMENTS ." A "
					.$s_where;		
          $rs=$this->db->query($s_qry);
          $i_cnt=0;
          if(is_array($rs->result()))
          {
              foreach($rs->result() as $row)
              {
                  $ret_=intval($row->i_total); 
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
    
	
	
	
	

	public function get_by_ring_post_id($i_media_id,$i_start_limit="", $i_no_of_page="") { 
        
		if("$i_start_limit" == "") {
			$sql = "SELECT c.id, 
								  
								   c.i_ring_post_id,
								   c.i_user_id, 
								   c.s_contents, 
								   c.dt_created_on, 
								   CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
								   u.s_profile_photo, 
								   u.s_first_name as pseudo, 
								   u.e_gender
								   
						FROM cg_church_ring_post_comments  c, cg_users u 
						WHERE c.i_user_id=u.id 
						    AND c.i_ring_post_id = '".intval($i_media_id)."' 
							
						   ORDER BY c.dt_created_on DESC";
		}
		else {
			$sql = "SELECT c.id, 
								   c.i_ring_post_id,
								   c.i_user_id, 
								   c.s_contents, 
								   c.dt_created_on, 
								   CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
								   u.s_profile_photo, 
								  
								   u.s_first_name as pseudo 
					    FROM cg_church_ring_post_comments c, cg_users u 
						WHERE c.i_user_id=u.id
						 AND c.i_ring_post_id = '".intval($i_media_id)."' 
						 
						 ORDER BY dt_created_on DESC LIMIT {intval($i_start_limit)}, {intval($i_no_of_page)}";
		}

        
		$query = $this->db->query($sql); 
   echo $this->db->last_query();exit;
		$result_arr = $query->result_array();

		return $result_arr;
	}
	
	

	public function get_total_by_ring_post_id($i_media_id) {
		//$sql = sprintf("SELECT count(*) count FROM %1\$schurch_ring_post_comments c, %1\$susers u WHERE c.i_user_id=u.id AND c.i_ring_post_id = %2\$s  order by c.dt_created_on", $this->db->dbprefix, intval($i_media_id) );
		$sql = "SELECT count(*) count FROM cg_church_ring_post_comments c, cg_users u WHERE c.i_user_id=u.id AND c.i_ring_post_id = '".intval($i_media_id)."'  order by c.dt_created_on";

		$query = $this->db->query($sql); //echo nl2br($sql);
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}



	public function get_total_comments_by_ring_id($ring_id) {
		$sql = " SELECT count(*) as total_comment 
						 FROM cg_church_ring R , cg_church_ring_post RP , cg_church_ring_post_comments RPC
					     WHERE RPC.i_ring_post_id = RP.id 
						 AND RP.i_ring_id = R.id 
						 AND R.id = {$ring_id}  
						 ";
						
						
		$query = $this->db->query($sql); #echo nl2br($sql);
		$result_arr = $query->result_array();

		return $result_arr[0]['total_comment'];
	}


	public function insert($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
               // pr($arr,1);
		$this->db->insert('cg_church_ring_post_comments', $arr);
		#echo $this->db->last_query();
		return $this->db->insert_id();
	}

	public function update($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update('cg_user_ring_post_comments', $arr, array('id'=>$id));
	}

	public function delete_by_id($id, $s_media_type) {
		
		$sql = 'DELETE FROM cg_user_ring_post_comments WHERE i_ring_post_id ="'.$id.'" AND s_media_type = "'.$s_media_type.'" ';

		$this->db->query($sql);
		
		# delete from like table #
		$sql = "DELETE FROM cg_suser_ring_post_like WHERE i_ring_post_id ='".$id."' AND s_media_type = '".$s_media_type."'";

		$this->db->query($sql); 
	}
	
  public function postLikeUnlike($like_unlike_information_array,$like_or_unlike="like")
  {
        
         $response['value'] = false ;
         $response['message'] = "Success" ;
         $response['last_inserted_id'] = '';
		 
		  $sql_present="SELECT COUNT(*) as total FROM {$this->db->dbprefix}church_ring_post_{$like_or_unlike} 
		  					WHERE i_ring_post_id='{$like_unlike_information_array['i_ring_post_id']}' 
			 				AND i_{$like_or_unlike}d_user_id='{$like_unlike_information_array["i_{$like_or_unlike}d_user_id"]}'
			 			"; 
            $res = $this->db->query($sql_present);
            $result = $res->row_array();
            $is_present = $result['total'];


       if($like_or_unlike=="like"){

         
      
           if( $is_present == 0){
			//$this->db->insert($this->db->dbprefix."user_ring_post_like",$like_unlike_information_array);
			//echo $this->db->last_query();exit;   
			  
			   
            if($this->db->insert($this->db->dbprefix."church_ring_post_like",$like_unlike_information_array))
            {
				
               $sql_del ="DELETE FROM {$this->db->dbprefix}church_ring_post_unlike WHERE i_ring_post_id='{$like_unlike_information_array['i_ring_post_id']}' AND i_unliked_user_id='{$like_unlike_information_array['i_liked_user_id']}'";
                $this->db->query($sql_del);
                $response['last_inserted_id'] = $this->db->insert_id();
                $response['value'] = true ;
                $response['message'] = "Succes" ;
            }
           }
     
        
       }
	   else if($like_or_unlike=="unlike")
        {

        if($is_present==0)
            {
            if($this->db->insert($this->db->dbprefix."church_ring_post_unlike",$like_unlike_information_array))
                {

                    $sql_del ="DELETE FROM {$this->db->dbprefix}church_ring_post_like WHERE i_ring_post_id='{$like_unlike_information_array['i_ring_post_id']}' AND i_liked_user_id='{$like_unlike_information_array['i_unliked_user_id']}'";
                    $this->db->query($sql_del);
                    $response['last_inserted_id'] = $this->db->insert_id();
                    $response['value'] = true ;
                    $response['message'] = "Success" ;
                }
            }

        }
//pr($response);
        return $response;
  }
  
  
  
  public function get_people_liked_by_ring_post_id($i_media_id,$i_start_limit="", $i_no_of_page="") {
		if("$i_start_limit" == "") {
			$sql = sprintf("SELECT c.id, 
								   c.i_ring_post_id,
								   c.i_liked_user_id, 
								   
								   c.dt_liked_on, 
								   CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
								   u.s_profile_photo, 
								  
								   u.s_first_name as pseudo 
						FROM %1\$schurch_ring_post_like c, %1\$susers u 
						WHERE c.i_liked_user_id=u.id 
						    AND c.i_ring_post_id = %2\$s 
							
						   ORDER BY c.dt_liked_on DESC", 
						   $this->db->dbprefix, 
						   intval($i_media_id) );
		}
		else {
			 $sql = sprintf("SELECT c.id, 
								   c.i_ring_post_id,
								   c.i_liked_user_id, 
								   
								   c.dt_liked_on, 
								   CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
								   u.s_profile_photo, 
								  
								   u.s_first_name as pseudo 
					    FROM %1\$schurch_ring_post_like c, %1\$susers u 
						WHERE c.i_liked_user_id=u.id
						 AND c.i_ring_post_id = %2\$s 
						 
						 ORDER BY dt_liked_on DESC LIMIT %3\$s, %4\$s", 
						 $this->db->dbprefix,
						 intval($i_media_id), 
						 intval($i_start_limit), 
						 intval($i_no_of_page),   $s_media_type);
		}
//echo ($sql);

		$query = $this->db->query($sql); 
		$result_arr = $query->result_array();

		return $result_arr;
	}
	
	public function get_total_people_liked_by_ring_post_id($i_media_id ) {
			$sql = sprintf("SELECT count(*) count FROM %1\$schurch_ring_post_like c, %1\$susers u 
							WHERE c.i_liked_user_id=u.id 
							AND c.i_ring_post_id = %2\$s 
							
							order by c.dt_liked_on", $this->db->dbprefix, intval($i_media_id));

		$query = $this->db->query($sql); //echo nl2br($sql);
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}
	
	
	public function get_owner_by_ring_post_id($post_id) {
		
		$sql = sprintf('SELECT R.i_user_id as i_owner_id FROM cg_church_ring_post R, cg_church_ring_post_comments RC WHERE RC.i_ring_post_id = %s ',$post_id);
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
		return $result_arr[0];
	}

  
}
