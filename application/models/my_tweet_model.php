<?php
include_once(APPPATH.'models/base_model.php');
class My_tweet_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

	
	public function get() {
		$sql = 'SELECT * FROM '.$this->db->TWEETS.' order by id desc';
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr;
	}
	
	
	public function get_by_id($id, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = 'SELECT * FROM '.$this->db->TWEETS.'  where id = "'.$id.'"';
		}
		else {
			$sql = 'SELECT * FROM '.$this->db->TWEETS.'  where id = "'.$id.'" limit {$start_limit}, {$no_of_page}';
		}

		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
		
		return $result_arr[0];
	}
	


	
	
### GET ALL TWEETS BY LOGGED USER ID ##########33
public function get_all_tweets_by_user_id($i_user_id, $s_where, $i_start_limit='', $i_no_of_page='') {
		
		
			 $sql = "
					(SELECT  u.id i_user_id, 
						 u.s_email, 
						 u.e_gender, 
						 CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
						 u.s_profile_photo,
						 u.s_tweet_id,
						 t.id,
						 t.i_owner_id,
						 t.data,
						 t.s_type,
						 t.dt_created_on,
						 (SELECT COUNT(*) as count FROM cg_tweets_fav ft WHERE ft.i_tweet_id = t.id )
						 as fav_tweet,
						 
						  (SELECT COUNT(*) as count FROM cg_tweets_replys tr WHERE tr.i_tweet_id = t.id )
						 as total_reply
						
					FROM cg_users u, cg_tweets t
					
					WHERE u.i_status='1' AND u.i_isdeleted ='1' AND t.i_isenabled =1  AND t.i_owner_id = u.id 
					
					AND
					(
						t.i_owner_id in 
						(SELECT u.id from cg_tweets_followers c,cg_users u where 
						((c.i_requester_id = {$i_user_id} and u.id=c.i_accepter_id AND c.i_current_status = 1) 
							) 
						)  {$s_where}
					
					
					
					) 
				)
				UNION
				 ( SELECT  
				    u.id i_user_id,
					u.s_email,
					u.e_gender,
					CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
					u.s_profile_photo,
					u.s_tweet_id,
					t.id,
					t.i_owner_id,
					t.data,
					t.s_type,
					t.dt_created_on,
					(SELECT COUNT(*) as count FROM cg_tweets_fav ft WHERE ft.i_tweet_id = t.id )
						 as fav_tweet,
					
					(SELECT COUNT(*) as count FROM cg_tweets_replys tr WHERE tr.i_tweet_id = t.id )
						 as total_reply
					
					FROM cg_users u, cg_tweets t
					LEFT JOIN cg_tweets_followers c1 ON c1.i_accepter_id = t.i_owner_id
					
					WHERE u.i_status='1' AND u.i_isdeleted ='1' AND t.i_isenabled =1 AND t.i_owner_id = u.id
					AND
					(
					t.i_owner_id in
							(SELECT c.i_accepter_id from cg_tweets_followers c, cg_tweets t1 where
								(c.i_requester_id = {$i_user_id} and  c.i_current_status = 1 )
							)
					
					) 
					AND DATE_FORMAT(t.dt_created_on , '%Y:%m:%d %H:%i:%s') <= c1.dt_unfollow_on
					{$s_where}
				 )
								
				UNION
				(SELECT u.id i_user_id,
					u.s_email,
					u.e_gender,
					CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
					u.s_profile_photo,
					u.s_tweet_id,
					t.id,
					t.i_owner_id,
					t.data,
					t.s_type,
					t.dt_created_on,
					(SELECT COUNT(*) as count FROM cg_tweets_fav ft WHERE ft.i_tweet_id = t.id)
						 as fav_tweet,
					
					 (SELECT COUNT(*) as count FROM cg_tweets_replys tr WHERE tr.i_tweet_id = t.id )
						 as total_reply
					
					FROM cg_users u, cg_tweets t
					WHERE u.i_status='1' AND u.i_isdeleted ='1' AND t.i_isenabled =1  AND t.i_owner_id = u.id AND t.i_owner_id = {$i_user_id} {$s_where})
					

				    ORDER BY dt_created_on DESC
					limit {$i_start_limit}, {$i_no_of_page}
					"
				;
		
		//echo nl2br($sql); exit;
		$query = $this->db->query($sql); 
		$result_arr = $query->result_array();
        
    # pr($result_arr,1);
     
		return $result_arr;
		
	
	}
	
public function get_total_all_tweets_by_user_id($i_user_id,  $s_where) {
		

		 $sql = "
				SELECT COUNT(*) count FROM (
				(SELECT t.id
						
					FROM cg_users u, cg_tweets t
					WHERE u.i_status='1' AND u.i_isdeleted ='1' AND t.i_isenabled =1  AND t.i_owner_id = u.id AND
					(
						t.i_owner_id in (SELECT u.id from cg_tweets_followers c, cg_users u where
						 ((c.i_requester_id = {$i_user_id} and u.id=c.i_accepter_id) 
						) 
						)  {$s_where}
					
					
					
					) )

				UNION
				(SELECT t.id 
					FROM cg_users u, cg_tweets t
					WHERE u.i_status='1' AND u.i_isdeleted ='1' AND t.i_isenabled =1 
					 AND t.i_owner_id = u.id AND t.i_owner_id = {$i_user_id}  {$s_where})
				UNION
				 ( SELECT  
				   
					t.id
					FROM cg_users u, cg_tweets t
					LEFT JOIN cg_tweets_followers c1 ON c1.i_accepter_id = t.i_owner_id
					
					WHERE u.i_status='1' AND u.i_isdeleted ='1' AND t.i_isenabled =1 AND t.i_owner_id = u.id
					AND
					(
					t.i_owner_id in
							(SELECT c.i_accepter_id from cg_tweets_followers c, cg_tweets t1 where
								(c.i_requester_id = {$i_user_id} and  c.i_current_status = 2 )
							)
					) 
					AND DATE_FORMAT(t.dt_created_on , '%Y:%m:%d %H:%i:%s') <= c1.dt_unfollow_on
					{$s_where}
				 )
		

				
				) derived_tbl
					"
				;
		
#and t.i_user_id != '%2\$s' or (c.i_accepter_id = %2\$s and u.id=c.i_requester_id)
		$query = $this->db->query($sql); //echo "sql ==>". ($sql) ."<br />";  
		$result_arr = $query->result_array();
		#echo $result_arr[0]['count']; exit;
		return $result_arr[0]['count'];
	
	}
	
### GET ALL TWEETS BY LOGGED USER ID ##########



### GET ALL TWEETS of only LOGGED USER ID ##########33
public function get_my_tweets($i_user_id, $s_where, $i_start_limit='', $i_no_of_page='') {
		
		if("$i_start_limit" == "") {
			$sql = "
				  (SELECT  u.id i_user_id, 
						 u.s_email, 
						 u.e_gender, 
						 CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
						 u.s_profile_photo,
						(SELECT COUNT(*) as count FROM cg_tweets_fav ft WHERE ft.i_tweet_id = t.id AND ft.i_user_id = '".intval($i_user_id)."')
						 as fav_tweet, 
						(SELECT COUNT(*) as count FROM cg_tweets_replys tr WHERE tr.i_tweet_id = t.id )
						 as total_reply,
						 u.s_tweet_id,
						 t.id,
						 t.i_owner_id,
						 t.data,
						 t.s_type,
						 t.dt_created_on
						
					FROM cg_users u, cg_tweets t
					
					WHERE u.i_status='1' AND u.i_isdeleted ='1' AND t.i_isenabled =1 
					 AND t.i_owner_id = u.id AND t.i_owner_id = '".intval($i_user_id)."' {$s_where}
					ORDER BY dt_created_on DESC
					";
		}
		else {
		
		
		
			 $sql = "
				(SELECT  u.id i_user_id, 
						 u.s_email, 
						 u.e_gender, 
						 CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
						 u.s_profile_photo, 
						 (SELECT COUNT(*) as count FROM cg_tweets_fav ft WHERE ft.i_tweet_id = t.id AND ft.i_user_id = '".intval($i_user_id)."')
						 as fav_tweet,
						(SELECT COUNT(*) as count FROM cg_tweets_replys tr WHERE tr.i_tweet_id = t.id )
						 as total_reply,
						 u.s_tweet_id,
						 t.id,
						 t.i_owner_id,
						 t.data,
						 t.s_type,
						 t.dt_created_on
						
					FROM cg_users u, cg_tweets t
					
					WHERE u.i_status='1' AND u.i_isdeleted ='1' AND t.i_isenabled =1 
					AND t.i_owner_id = u.id AND t.i_owner_id = '".intval($i_user_id)."' {$s_where}
				)

				    ORDER BY dt_created_on DESC
					limit {$i_start_limit}, {$i_no_of_page}
					";
		}

#AND t.i_user_id != '%2\$s'
		$query = $this->db->query($sql); //echo "sql ==>". ($sql) ."<br />"; 
		$result_arr = $query->result_array();
        
     // pr($result_arr);
     
		return $result_arr;
		
	
	}
	
	public function get_total_my_tweets($i_user_id,  $s_where) {
		

		 $sql = "
				SELECT COUNT(*) count FROM (
					(SELECT t.id 
					  FROM cg_users u, cg_tweets t
					  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND t.i_isenabled =1 
					  AND t.i_owner_id = u.id AND t.i_owner_id = '".intval($i_user_id)."' {$s_where})

				
				) derived_tbl";
		
#and t.i_user_id != '%2\$s'
		$query = $this->db->query($sql); //echo "sql ==>". nl2br($sql) ."<br />";  
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	
	}
	
	
### GET ALL TWEETS of only LOGGED USER ID ##########33




### GET ALL fav TWEETS BY LOGGED USER ID ##########33
public function get_fav_tweets_by_user_id($i_user_id, $s_where, $i_start_limit='', $i_no_of_page='') {
		
		if("$i_start_limit" == "") {
			$sql = "
				  (SELECT  u.id i_user_id, 
						 u.s_email, 
						 u.e_gender, 
						 CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
						 u.s_profile_photo, 
						 u.s_tweet_id,
						
						 t.id,
						 t.i_owner_id,
						 t.data,
						 t.s_type,
						 t.dt_created_on,
						 (SELECT COUNT(*) as count FROM cg_tweets_fav ft WHERE ft.i_tweet_id = t.id AND ft.i_user_id = '".intval($i_user_id)."')
						 as fav_tweet,
						 
						 (SELECT COUNT(*) as count FROM cg_tweets_replys tr WHERE tr.i_tweet_id = t.id )
						 as total_reply
						 
						 
						
					FROM cg_users u, cg_tweets t
					
					WHERE u.i_status='1' AND u.i_isdeleted ='1' AND t.i_isenabled =1  AND t.i_owner_id = u.id 
					AND 
					(
						t.id in 
						(SELECT ft.i_tweet_id from cg_tweets_fav ft  where ft.i_tweet_id = t.id AND ft.i_user_id = '".intval($i_user_id)."' )  
						
					)
					AND
					(
						t.i_owner_id in 
						(SELECT u.id from cg_tweets_followers c, cg_users u where c.s_status = 'accepted'
							and ((c.i_requester_id = '".intval($i_user_id)."' and u.id=c.i_accepter_id) 
							or (c.i_accepter_id = '".intval($i_user_id)."' and u.id=c.i_requester_id)) 
						)  {$s_where}
					
					
					
					) )

				UNION
				(SELECT u.id i_user_id,
					u.s_email,
					u.e_gender,
					CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
					u.s_profile_photo,
					u.s_tweet_id,
					
					t.id,
					t.i_owner_id,
					t.data,
					t.s_type,
					t.dt_created_on,
					(SELECT COUNT(*) as count FROM cg_tweets_fav ft WHERE ft.i_tweet_id = t.id AND ft.i_user_id = '".intval($i_user_id)."')
						 as fav_tweet,
					 (SELECT COUNT(*) as count FROM cg_tweets_replys tr WHERE tr.i_tweet_id = t.id )
						 as total_reply
					
					FROM cg_users u, cg_tweets t
					WHERE u.i_status='1' AND u.i_isdeleted ='1' AND t.i_isenabled =1  
					AND t.i_owner_id = u.id AND t.i_owner_id = '".intval($i_user_id)."'
					AND 
					(
						t.id in 
						(SELECT ft.i_tweet_id from cg_tweets_fav ft  where ft.i_tweet_id = t.id AND ft.i_user_id = '".intval($i_user_id)."' )  
						
					))

				ORDER BY t.id DESC
					"
				, $this->db->dbprefix, intval($i_user_id), $s_where
			);
		}
		else {
		
		
		
			 $sql = "
				(SELECT  u.id i_user_id, 
						 u.s_email, 
						 u.e_gender, 
						 CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
						 u.s_profile_photo, 
						 u.s_tweet_id,
						
						 t.id,
						 t.i_owner_id,
						 t.data,
						 t.s_type,
						 t.dt_created_on,
						 (SELECT COUNT(*) as count FROM cg_tweets_fav ft WHERE ft.i_tweet_id = t.id AND ft.i_user_id = '".intval($i_user_id)."')
						 as fav_tweet,
						 
						  (SELECT COUNT(*) as count FROM cg_tweets_replys tr WHERE tr.i_tweet_id = t.id )
						 as total_reply
						
					FROM cg_users u, cg_tweets t
					
					WHERE u.i_status='1' AND u.i_isdeleted ='1' AND t.i_isenabled =1  AND t.i_owner_id = u.id 
					AND 
					(
						t.id in 
						(SELECT ft.i_tweet_id from cg_tweets_fav ft  where ft.i_tweet_id = t.id AND ft.i_user_id = '".intval($i_user_id)."' )  
						
					)
					
					AND
					(
						t.i_owner_id in 
						(SELECT u.id from cg_tweets_followers c, cg_users u where 
						((c.i_requester_id = '".intval($i_user_id)."' and u.id=c.i_accepter_id) 
							) 
						)  {$s_where}
						
					) 
				)

								
				UNION
				(SELECT u.id i_user_id,
					u.s_email,
					u.e_gender,
					CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
					u.s_profile_photo,
					u.s_tweet_id,
					
					t.id,
					t.i_owner_id,
					t.data,
					t.s_type,
					t.dt_created_on,
					(SELECT COUNT(*) as count FROM cg_tweets_fav ft WHERE ft.i_tweet_id = t.id AND ft.i_user_id = '".intval($i_user_id)."')
						 as fav_tweet,
					
					 (SELECT COUNT(*) as count FROM cg_tweets_replys tr WHERE tr.i_tweet_id = t.id )
						 as total_reply
					
					FROM cg_users u, cg_tweets t
					WHERE u.i_status='1' AND u.i_isdeleted ='1' AND t.i_isenabled =1  
					AND t.i_owner_id = u.id AND t.i_owner_id = '".intval($i_user_id)."'  
					AND 
					(
						t.id in 
						(SELECT ft.i_tweet_id from cg_tweets_fav ft  where ft.i_tweet_id = t.id AND ft.i_user_id = '".intval($i_user_id)."' )  
						
					)
					{$s_where})

				    ORDER BY dt_created_on DESC
					limit {$i_start_limit}, {$i_no_of_page}
					";
		}

#AND t.i_user_id != '%2\$s' or (c.i_accepter_id = %2\$s and u.id=c.i_requester_id)
		$query = $this->db->query($sql); #echo "sql ==>". nl2br($sql) ."<br />"; 
		$result_arr = $query->result_array();
        
    # pr($result_arr,1);
     
		return $result_arr;
		
	
	}
	
public function get_total_fav_tweets_by_user_id($i_user_id,  $s_where) {
		

		 $sql = sprintf("
				SELECT COUNT(*) count FROM (
				(SELECT t.id
						
					FROM %1\$susers u, %1\$stweets t
					WHERE u.i_status='1' AND u.i_isdeleted ='1' AND t.i_isenabled =1  AND t.i_owner_id = u.id
					AND 
					(
						t.id in 
						(SELECT ft.i_tweet_id from %1\$stweets_fav ft  where ft.i_tweet_id = t.id AND ft.i_user_id = %2\$s )  
						
					)
					AND
					(
						t.i_owner_id in (SELECT u.id from %1\$stweets_followers c, %1\$susers u where
						 ((c.i_requester_id = %2\$s and u.id=c.i_accepter_id) 
						) 
						)  %3\$s
					
					
					
					) )

				UNION
				(SELECT t.id 
					FROM cg_users u, cg_tweets t
					WHERE u.i_status='1' AND u.i_isdeleted ='1' AND t.i_isenabled =1 
					 AND t.i_owner_id = u.id AND t.i_owner_id = %2\$s 
					 AND 
					(
						t.id in 
						(SELECT ft.i_tweet_id from %1\$stweets_fav ft  where ft.i_tweet_id = t.id AND ft.i_user_id = %2\$s )  
						
					)
					  %3\$s)

				
				) derived_tbl
					"
				, $this->db->dbprefix, intval($i_user_id),$s_where
			);
		
#and t.i_user_id != '%2\$s' or (c.i_accepter_id = %2\$s and u.id=c.i_requester_id)
		$query = $this->db->query($sql); //echo "sql ==>". ($sql) ."<br />";  
		$result_arr = $query->result_array();
		#echo $result_arr[0]['count']; exit;
		return $result_arr[0]['count'];
	
	}
	
### GET ALL fav TWEETS BY LOGGED USER ID ##########
	
	
	

	public function insert($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->TWEETS, $arr);# echo $this->db->last_query();
		return $this->db->insert_id();
	}
	
	

	public function update($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->TWEETS, $arr, array('id'=>$id));
	}
	
	

	public function delete_by_id($id) {
	
	     $sql = sprintf( 'DELETE FROM '.$this->db->TWEETS.' WHERE id=%s', $id );
		 $this->db->query($sql);
		#echo $this->db->last_query(); exit;
	}
	
	public function get_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY id DESC';
		
        $sql    = " SELECT T.*, 
					CONCAT(U.s_first_name,' ', U.s_last_name) AS s_profile_name,
					U.s_tweet_id
					FROM {$this->db->TWEETS} 
					T LEFT JOIN  {$this->db->USERS} U ON U.id = T.i_owner_id 
					{$where}  {$s_order_by} {$limit}";

        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		
        return $result_arr;
    }
	
    public function get_list_count($where='')
    {
        
        
        $sql    = "SELECT count(*) as i_total FROM {$this->db->TWEETS} T 
				  	LEFT JOIN  {$this->db->USERS} U ON U.id = T.i_owner_id  
				  	{$where} ";
        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0]['i_total'];
    }
	
	public function change_status($status ,$id) {
		
	  if($status !='' && $id !=''){	
		  $sql = sprintf( "UPDATE {$this->db->TWEETS} SET `i_isenabled` = '%s'
						   WHERE `id` ='%s'"
					  , $status, $id );
		  $this->db->query($sql);// echo $this->db->last_query();exit;
		  return true;
	  }
	}
	
	
	public function get_owner_by_tweet_id($id) {
		
		$sql = sprintf('SELECT i_owner_id  FROM '.$this->db->TWEETS.'  WHERE id = %s ',$id);
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
		return $result_arr[0];
	}
	
	
	
	
	#### NEW MWTHODS TO CHECK IF TAG ALREADY EXISTS IN DB  TRENDING
	public function ifTagExists($tag) {
		
		$SQL = "SELECT count(*) as count FROM `cg_tweets_trendings` WHERE 1 AND BINARY `s_tags` = '".$tag."'";
		$query = $this->db->query($SQL);
		$result_arr = $query->result_array();#
		return $result_arr[0]['count'];
	}
	
	### INSERTING TAG IN cg_tweets_trendings
	public function insert_tags($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->TWEETS_TRENDINGS, $arr);
		#echo $this->db->last_query();# 
		return $this->db->insert_id();
	}
	
	## updating tags score
	
	public function update_tags_score($i_score , $tag) {
		$sql = sprintf( "UPDATE {$this->db->TWEETS_TRENDINGS} SET `i_score` = '%s'
						   WHERE BINARY `s_tags` ='%s'"
					  ,$i_score, $tag);
	    $this->db->query($sql);// echo $this->db->last_query();exit;
		return true;
	}
	
	## getting total score by stags in trending
	
	public function getScore($tag) {
		$sql = sprintf( "SELECT i_score FROM {$this->db->TWEETS_TRENDINGS} WHERE BINARY `s_tags` ='%s'", $tag);
	    $query = $this->db->query($sql);
		$result_arr = $query->result_array();
		return $result_arr[0]['i_score'];
	}
	
	
	
	### INSERTING FAVOURITE TWEET IN TWEETS_FAV
	public function insert_fav_tweets($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->TWEETS_FAV, $arr);# echo $this->db->last_query();
		return $this->db->insert_id();
	}
	
	### REMOVING FAVOURITE TWEET IN TWEETS_FAV
	public function remove_from_fav_tweet($id, $user_id) {
	
	     $sql = sprintf( 'DELETE FROM '.$this->db->TWEETS_FAV.' WHERE i_tweet_id=%s AND i_user_id = %s ', $id, $user_id);
		 $this->db->query($sql);
		#echo $this->db->last_query(); exit;
	}
	
	
	
	
	
	
	### GET TRENDS LISTS ####
	
	public function getTrendList($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY i_score DESC';
		
        $sql    = " SELECT T.*
						FROM {$this->db->TWEETS_TRENDINGS} 
						T  {$where}  ORDER BY i_score DESC {$limit}";

        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); #pr($result_arr,1);
		
		
        return $result_arr;
    }
	
	public function getTrendListCount($where='')
    {
        
        
        $sql    = "SELECT count(*) as i_total FROM {$this->db->TWEETS_TRENDINGS} T 
				   {$where} ";
        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0]['i_total'];
    }

	public function get_trend_name_by_id($id){
		
		$sql = sprintf('SELECT * FROM '.$this->db->TWEETS_TRENDINGS.'  where id = %s',  $id);
		$query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0];
		
	}
	
	public function get_trend_id_by_name($name){
		
		$sql = sprintf('SELECT *  FROM '.$this->db->TWEETS_TRENDINGS.'  where s_tags = "%s"',  $name);
		$query     = $this->db->query($sql); #echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0];
		
	}
	
	#### FOLLOW A USER 
	
	### CHECKING LOGGED USER IS FOLLOWING A SPECIFIC USER
	public function isFollowing($user_id) {
		
		$i_logged_user_id = intval(decrypt($this->session->userdata('user_id')));
		
		$sql    = "SELECT count(*) as i_total FROM {$this->db->TWEETS_FOLLOWERS} F 
				   WHERE  F.i_accepter_id = {$user_id} AND  F.i_requester_id = {$i_logged_user_id}
				   AND F.i_current_status = 1
				   AND F.dt_unfollow_on = '0000-00-00 00:00:00'
				   ";
				   
        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr);
		
        if($result_arr[0]['i_total'])
			return true;
		else
			return false;
	}
	
	### INSERTING follow TWEET IN TWEETS_FOLLOWERS
	public function insert_follow($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->TWEETS_FOLLOWERS, $arr);#echo $this->db->last_query();
		return $this->db->insert_id();
	}
	
	### REMOVING follow TWEET IN TWEETS_FOLLOWERS
	public function remove_follow($i_requester_id, $i_accepter_id) {
	
	     $sql = sprintf( 'DELETE FROM '.$this->db->TWEETS_FOLLOWERS.' WHERE i_accepter_id=%s AND i_requester_id = %s ', $i_accepter_id, $i_requester_id);
		 $this->db->query($sql);
		#echo $this->db->last_query(); exit;
	}
	
	
	
	
	### GET FOLLOWINGS BY USER ID 
	
	public function get_following_list($user_id, $where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY TF.id DESC';
		
        $sql    = " SELECT  TF.*, 
							u.id as userid,
							u.s_email,
							u.e_gender,
							CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
							u.s_profile_photo,
							u.s_tweet_id,	
							u.s_about_me,
							u.dt_created_on
					
					FROM {$this->db->TWEETS_FOLLOWERS} TF , {$this->db->USERS} u 
					WHERE u.i_status = 1 AND u.i_isdeleted = 1
					AND TF.i_accepter_id = u.id
					AND TF.i_current_status =  1
					AND TF.dt_unfollow_on = '0000-00-00 00:00:00'
					AND TF.i_requester_id  = {$user_id}
					{$where}  {$s_order_by} {$limit}";

        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		
        return $result_arr;
    }
	
    public function get_following_list_count($user_id,$where='')
    {
        
        
        $sql    = " SELECT count(*) as i_total 
					
					FROM {$this->db->TWEETS_FOLLOWERS} TF , {$this->db->USERS} u 
					WHERE u.i_status = 1 AND u.i_isdeleted = 1
					AND TF.i_requester_id  = {$user_id}
					AND TF.i_accepter_id = u.id
					AND TF.i_current_status =  1
					AND TF.dt_unfollow_on = '0000-00-00 00:00:00'
				  	{$where} ";
        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0]['i_total'];
    }
	
	
	### END OF GETTING FOLLOWINGS 
	
	### GET FOLLOWers (people following me) BY USER ID 
	
	public function get_followers_list($user_id, $where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY TF.id DESC';
		
        $sql    = " SELECT  TF.*, 
							u.id as userid,
							u.s_email,
							u.e_gender,
							CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
							u.s_profile_photo,
							u.s_tweet_id,	
							u.s_about_me,
							u.dt_created_on
					
					FROM {$this->db->TWEETS_FOLLOWERS} TF , {$this->db->USERS} u 
					WHERE u.i_status = 1 AND u.i_isdeleted = 1
					AND TF.i_requester_id = u.id
					AND TF.i_accepter_id  = {$user_id}
					AND TF.i_current_status =  1
					AND TF.dt_unfollow_on = '0000-00-00 00:00:00'
					{$where}  {$s_order_by} {$limit}";

        $query     = $this->db->query($sql); //echo $this->db->last_query(); exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		
        return $result_arr;
    }
	
    public function get_followers_list_count($user_id,$where='')
    {
        
        
        $sql    = " SELECT count(*) as i_total 
					
					FROM {$this->db->TWEETS_FOLLOWERS} TF , {$this->db->USERS} u 
					WHERE u.i_status = 1 AND u.i_isdeleted = 1
					AND TF.i_requester_id = u.id
					AND TF.i_accepter_id  = {$user_id}
					AND TF.i_current_status =  1
					AND TF.dt_unfollow_on = '0000-00-00 00:00:00'
				  	{$where} ";
        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0]['i_total'];
    }
	
	
	### END OF GETTING FOLLOWers (people following me) 
	
	
	
	
	### searching people on twitter in cogtime
	
	
	public function search_all_user($s_where, $i_start_limit='', $i_limit='') {
		
		$limit  = (is_numeric($i_start_limit) && is_numeric($i_limit))?" Limit ".intval($i_start_limit).",".intval($i_limit):'';
		
		
		$SQL = "SELECT 
					
					u.id as userid,
					u.s_email,
					u.e_gender,
					CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
					u.s_profile_photo,
					u.s_tweet_id,	
					u.s_about_me,
					u.dt_created_on
					
					FROM {$this->db->USERS} u
					WHERE  u.i_status = 1
					AND u.i_isdeleted =1
					{$s_where}
					ORDER BY dt_created_on DESC {$limit}
				
				";
		$query     = $this->db->query($SQL); #echo nl2br($this->db->last_query()); exit;
        $result_arr = $query->result_array();
		
		return $result_arr;
		
	}
	
	public function get_total_search_all_user($s_where) {
		
		$SQL = " SELECT count(*) as i_total 
							
					FROM {$this->db->USERS} u
					WHERE  u.i_status = 1
					AND u.i_isdeleted =1
					{$s_where}
				";
		$query     = $this->db->query($SQL); #echo $this->db->last_query();
        $result_arr = $query->result_array();
		return $result_arr[0]['i_total'];
	}
	
	
	
	#### searching trend 
	
	
	public function search_tweets_by_trend($i_user_id, $s_where, $i_start_limit='', $i_no_of_page='') {
		
		if("$i_start_limit" == "") {
			$sql = sprintf("
				  SELECT  u.id i_user_id, 
						 u.s_email, 
						 u.e_gender, 
						 CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
						 u.s_profile_photo, 
						 u.s_tweet_id,
						
						 t.id ,
						 t.i_owner_id,
						 t.data,
						 t.s_type,
						 t.dt_created_on,
						 (SELECT COUNT(*) as count FROM %1\$stweets_fav ft WHERE ft.i_tweet_id = t.id AND ft.i_user_id = %2\$s)
						 as fav_tweet,
						 
						 (SELECT COUNT(*) as count FROM %1\$stweets_replys tr WHERE tr.i_tweet_id = t.id )
						 as total_reply
						 
						 
						
					FROM %1\$susers u, %1\$stweets t
					
					WHERE u.i_status='1' AND u.i_isdeleted ='1' AND t.i_isenabled =1  AND t.i_owner_id = u.id  
					%3\$s
				    ORDER BY t.id DESC
					"
				, $this->db->dbprefix, intval($i_user_id), $s_where
			);
		}
		else {
		
		
		
			 $sql = sprintf("
				SELECT  u.id i_user_id, 
						 u.s_email, 
						 u.e_gender, 
						 CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
						 u.s_profile_photo, 
						 u.s_tweet_id,
						
						 t.id ,
						 t.i_owner_id,
						 t.data,
						 t.s_type,
						 t.dt_created_on,
						 (SELECT COUNT(*) as count FROM %1\$stweets_fav ft WHERE ft.i_tweet_id = t.id AND ft.i_user_id = %2\$s)
						 as fav_tweet,
						 
						  (SELECT COUNT(*) as count FROM %1\$stweets_replys tr WHERE tr.i_tweet_id = t.id )
						 as total_reply
						
					FROM %1\$susers u, %1\$stweets t
					
					WHERE u.i_status='1' AND u.i_isdeleted ='1' AND t.i_isenabled =1  AND t.i_owner_id = u.id 
					%5\$s
					
					
					ORDER BY t.id DESC
					limit %3\$s, %4\$s
					"
				, $this->db->dbprefix, intval($i_user_id), intval($i_start_limit), intval($i_no_of_page),  $s_where
			);
		}

#AND t.i_user_id != '%2\$s' or (c.i_accepter_id = %2\$s and u.id=c.i_requester_id)
		$query = $this->db->query($sql); #echo "sql ==>". nl2br($sql) ."<br />"; 
		$result_arr = $query->result_array();
        
    # pr($result_arr,1);
     
		return $result_arr;
		
	
	}
	
	public function get_total_search_tweets_by_trend($i_user_id, $s_where) {
		

		 $sql = sprintf("
				SELECT COUNT(*) count FROM (
				(SELECT t.id
						
					FROM %1\$susers u, %1\$stweets t
					WHERE u.i_status='1' AND u.i_isdeleted ='1' AND t.i_isenabled =1  AND t.i_owner_id = u.id  %2\$s  
				 )

				) derived_tbl
					"
				, $this->db->dbprefix, $s_where
			);
		
#and t.i_user_id != '%2\$s' or (c.i_accepter_id = %2\$s and u.id=c.i_requester_id)
		$query = $this->db->query($sql); #echo "sql ==>". ($sql) ."<br />";  exit;
		$result_arr = $query->result_array();
		#echo $result_arr[0]['count']; exit;
		return $result_arr[0]['count'];
	
	}
	
	
	### new method to unfollow ####
	
	public function unfollow_user($i_requester_id, $i_accepter_id) {
	
	     $sql = 'UPDATE '.$this->db->TWEETS_FOLLOWERS.' SET dt_unfollow_on = "'.get_db_datetime().'",
		 					 i_current_status = 2 
		 					 WHERE i_accepter_id='.$i_accepter_id.' AND i_requester_id = '.$i_requester_id. '';
		 $this->db->query($sql);
		#echo $this->db->last_query(); exit;
	}
	
	public function isUnfollowedEarlier($user_id) {
		
		$i_logged_user_id = intval(decrypt($this->session->userdata('user_id')));
		
		$sql    = "SELECT count(*) as i_total FROM {$this->db->TWEETS_FOLLOWERS} F 
				   WHERE  F.i_accepter_id = {$user_id} 
				   AND  F.i_requester_id = {$i_logged_user_id}
				   AND F.i_current_status = 2
				   ";
				   
        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr);
		
        if($result_arr[0]['i_total'])
			return true;
		else
			return false;
	}
	
	
	public function update_follow_user($i_requester_id, $i_accepter_id) {
	
	     $sql = 'UPDATE '.$this->db->TWEETS_FOLLOWERS.' SET dt_unfollow_on = "0000-00-00 00:00:00",
		 					 i_current_status = 1  , dt_created_on = "'.get_db_datetime().'"
		 					 WHERE i_accepter_id='.$i_accepter_id.' AND i_requester_id = '.$i_requester_id. '';
		 $this->db->query($sql);
		#echo $this->db->last_query(); exit;
	}
	
	public function get_video_by_id($i_newsfeed_id) {
	
	    $_ret = array();
		if(intval($i_newsfeed_id)>0)
		{
			$sql = sprintf("SELECT * FROM %stweets WHERE id = '%s'", $this->db->dbprefix, intval($i_newsfeed_id));
			
			$query = $this->db->query($sql);
			$result_arr = $query->result_array();
			 $_ret = $result_arr[0];
			 return $_ret;
		}
		}
}
