<?php

class Tweet_reply_model extends CI_Model {
	
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
                    ." FROM ". $this->db->tweets_replys ." A " 
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
                    ." FROM ". $this->db->tweets_replys ." A "
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
          $this->db->trans_commit();///new
          unset($s_qry,$rs,$row,$i_cnt,$s_where);
          return $ret_;
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }           
    }         
    
	
	
	
	

	public function get_by_tweet_id($i_tweet_id,  $i_start_limit="", $i_no_of_page="") { 
        
		if("$i_start_limit" == "") {
			$sql = "SELECT c.id, 
								   c.i_tweet_id,
								   c.i_user_id, 
								   c.s_contents, 
								   c.dt_created_on, 
								   CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
								   u.s_profile_photo, 
								   u.s_tweet_id,
								  
								   u.s_first_name as pseudo 
						FROM cg_tweets_replys c, cg_users u 
						WHERE c.i_user_id=u.id 
						    AND c.i_tweet_id = '".intval($i_tweet_id)."' 
						   ORDER BY c.dt_created_on DESC";
		}
		else {
			$sql = "SELECT c.id, 
								   c.i_tweet_id,
								   c.i_user_id, 
								   c.s_contents, 
								   c.dt_created_on, 
								   CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
								   u.s_profile_photo, 
								   u.s_tweet_id, 
								  
								   u.s_first_name as pseudo 
					    FROM cg_tweets_replys c, cg_users u 
						WHERE c.i_user_id=u.id
						 AND c.i_tweet_id = '".intval($i_tweet_id)."' 
						 ORDER BY dt_created_on DESC LIMIT {$i_start_limit}, {$i_no_of_page}";
		}

        
		$query = $this->db->query($sql); 
    //echo $this->db->last_query();
		$result_arr = $query->result_array();

		return $result_arr;
	}
	
	

	public function get_total_by_tweet_id($i_tweet_id) {
		$sql = "SELECT count(*) count FROM cg_tweets_replys c, %1\$susers u
						 WHERE c.i_user_id=u.id AND c.i_tweet_id = '".intval($i_tweet_id)."' 
						 order by c.dt_created_on";

		$query = $this->db->query($sql); //echo nl2br($sql);
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}



	public function insert($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert('tweets_replys', $arr);
		//echo $this->db->last_query();
		return $this->db->insert_id();
	}

	public function update($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update('tweets_replys', $arr, array('id'=>$id));
	}

	public function delete_by_id($id) {
		
		$sql = 'DELETE FROM %stweets_replys WHERE i_tweet_id=%s AND s_media_type = "'.$id.'" ';

		$this->db->query($sql);
		
		
	}
	
   
}
