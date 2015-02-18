<?php
/* 
Note: 
1. When I comment on someone's photo, or post on my wall these are posted on my wall as
owner: my user id
ownership: 'ownerpost'
these are news feeds for my friends.

2. When I post on Mr. X's wall these will not be shown in anyone's wall except Mr. X
owner: Mr X's id
ownership: 'otherpost'

3. My Newsfeeds = My Wall Posts + Friend's Wall posts provided post made by that friend (ownership:'ownerpost')
*/
class Data_newsfeed_model extends CI_Model 
{
	
	public function __construct() {
		parent::__construct();
	}

	public function get() {
		$sql = sprintf("SELECT * FROM %s ORDER BY dt_created_on DESC", $this->db->user_newsfeeds);
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr;
	}

	
	public function get_by_id($i_newsfeed_id) {
	
	    $_ret = array();
		if(intval($i_newsfeed_id)>0)
		{
			$sql = sprintf("SELECT * FROM %suser_newsfeeds WHERE id = '%s'", $this->db->dbprefix, intval($i_newsfeed_id));
			$query = $this->db->query($sql);
			$result_arr = $query->result_array();
			 $_ret = $result_arr[0];
		}

		return  $_ret;
	}

	/* get users wall posts */
	public function get_by_owner($i_owner_id) {
		$sql = sprintf("SELECT * FROM %suser_newsfeeds where i_owner_id = '%s' order by dt_created_on desc", $this->db->dbprefix, intval($i_owner_id));
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr;
	}

	/* get all my($user_id) newsfeeds */
	public function get_newsfeeds_by_user_id($i_user_id, $i_start_limit='', $i_no_of_page='') {
		if("$i_start_limit" == "") {
			$sql = sprintf("
				(SELECT  u.id i_user_id, 
						 u.s_email, 
						
						u.e_gender, 
						CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
						u.s_profile_photo, 
						u.i_user_type,
						n.id,
						n.i_owner_id,
						n.s_type,
						n.s_ownership,
						n.data,
						n.dt_created_on,
						n.i_referrence_id
					FROM %1\$susers u, %1\$suser_newsfeeds n 
					WHERE u.i_status='1' AND u.i_isdeleted ='1' AND n.i_owner_id = u.id AND
					n.s_type = 'friend_with' AND
					(
						n.i_owner_id in (SELECT u.id from %1\$suser_contacts c, %1\$susers u where c.s_status = 'accepted'
						and ((c.i_requester_id = %2\$s and u.id=c.i_accepter_id) 
						or (c.i_accepter_id = %2\$s and u.id=c.i_requester_id)) 
							and n.data not regexp '\"user_id1\"[[.:.]]\"%2\$s\"'  
							and n.data not regexp '\"user_id2\"[[.:.]]\"%2\$s\"' 
						) and i_owner_id != '%2\$s' and n.s_ownership = 'ownerpost'
					
					
					
					) )

				UNION ALL

				 (SELECT  u.id i_user_id, 
						u.s_email, 
						
						u.e_gender, 
						CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
						u.s_profile_photo, 
						u.i_user_type,
						n.id,
						n.i_owner_id,
						n.s_type,
						n.s_ownership,
						n.data,
						n.dt_created_on,
						n.i_referrence_id
					FROM %1\$susers u, %1\$suser_newsfeeds n 
					WHERE u.i_status='1' AND u.i_isdeleted ='1'  AND n.i_owner_id = u.id 
					AND n.s_type != 'friend_with' AND
					(
						n.i_owner_id in (SELECT u.id from %1\$suser_contacts c, %1\$susers u where c.s_status = 'accepted'
						AND ((c.i_requester_id = %2\$s AND u.id=c.i_accepter_id) 
						OR (c.i_accepter_id = %2\$s AND u.id=c.i_requester_id))) AND n.s_ownership = 'ownerpost'
						
						
						OR 
						n.i_owner_id = '%2\$s'
					) )

				ORDER BY dt_created_on DESC
					"
				, $this->db->dbprefix, intval($i_user_id)
			);
		}
		else {
		
		
		
			 $sql = sprintf("
				(SELECT  u.id i_user_id, 
						u.s_email, 
						
						u.e_gender, 
						CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
						u.s_profile_photo, 
						u.i_user_type,
						n.*
					FROM %1\$susers u, %1\$suser_newsfeeds n 
					WHERE u.i_status='1' AND u.i_isdeleted ='1' AND  n.i_owner_id = u.id AND
					n.s_type = 'friend_with' AND
					(
						n.i_owner_id in (SELECT u.id from %1\$suser_contacts c, %1\$susers u where c.s_status = 'accepted'
						and ((c.i_requester_id = %2\$s and u.id=c.i_accepter_id) 
						or (c.i_accepter_id = %2\$s and u.id=c.i_requester_id)) 
							and n.data not regexp '\"user_id1\"[[.:.]]\"%2\$s\"'  
							and n.data not regexp '\"user_id2\"[[.:.]]\"%2\$s\"' 
						) and i_owner_id != '%2\$s' and n.s_ownership = 'ownerpost'
					
					
					
					) )

				UNION ALL

				 (SELECT u.id i_user_id, 
						u.s_email, 
						
						u.e_gender, 
						CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
						u.s_profile_photo, 
						u.i_user_type,
						n.*
					FROM %1\$susers u, %1\$suser_newsfeeds n 
					WHERE u.i_status='1' AND u.i_isdeleted ='1'  AND n.i_owner_id = u.id 
					AND n.s_type != 'friend_with' AND
					(
						n.i_owner_id in (SELECT u.id from %1\$suser_contacts c, %1\$susers u where c.s_status = 'accepted'
						AND ((c.i_requester_id = %2\$s AND u.id=c.i_accepter_id) 
						OR (c.i_accepter_id = %2\$s AND u.id=c.i_requester_id))) AND n.s_ownership = 'ownerpost'
						OR
						n.i_owner_id = '%2\$s'
					) )

				ORDER BY dt_created_on DESC
					limit %3\$s, %4\$s
					"
				, $this->db->dbprefix, intval($i_user_id), intval($i_start_limit), intval($i_no_of_page)
			);
		}

		$query = $this->db->query($sql); #echo "sql ==>". nl2br($sql) ."<br />"; 
		$result_arr = $query->result_array();
        
     // pr($result_arr);
        
		$ci = get_instance();
		$ci->load->model('newsfeed_comments_model');

		if( is_array($result_arr) && count($result_arr) ) {
			foreach($result_arr as $key=>$item) {
				$result_arr[$key]['comments'] = $ci->newsfeed_comments_model->get_by_newsfeed_id($item['id']);
				$result_arr[$key]['reference_comment'] = $this->get_by_id(intval($item['i_referrence_id']));
				$result_arr[$key]['total_comments'] = $ci->newsfeed_comments_model->get_total_by_newsfeed_id($item['id']);
			}
		}
//pr($result_arr);
		return $result_arr;
	}

	/* get total of my($user_id) newsfeeds */
	public function get_total_newsfeeds_by_user_id($i_user_id) {

		$sql = sprintf("
				SELECT COUNT(*) count FROM (
				(SELECT u.id
						
					FROM %1\$susers u, %1\$suser_newsfeeds n 
					WHERE u.i_status='1' AND u.i_isdeleted ='1'  AND n.i_owner_id = u.id AND
					n.s_type = 'friend_with' AND
					(
						n.i_owner_id in (SELECT u.id from %1\$suser_contacts c, %1\$susers u where c.s_status = 'accepted'
						and ((c.i_requester_id = %2\$s and u.id=c.i_accepter_id) 
						or (c.i_accepter_id = %2\$s and u.id=c.i_requester_id)) 
							and n.data not regexp '\"user_id1\"[[.:.]]\"%2\$s\"'  
							and n.data not regexp '\"user_id2\"[[.:.]]\"%2\$s\"' 
						) and i_owner_id != '%2\$s' and n.s_ownership = 'ownerpost'
					
					
					
					) )

				UNION ALL

				(SELECT u.id
					FROM %1\$susers u, %1\$suser_newsfeeds n 
					WHERE u.i_status='1' AND u.i_isdeleted ='1' AND n.i_owner_id = u.id 
					AND n.s_type != 'friend_with' AND
					(
						n.i_owner_id in (SELECT u.id from %1\$suser_contacts c, %1\$susers u where c.s_status = 'accepted'
						AND ((c.i_requester_id = %2\$s AND u.id=c.i_accepter_id) 
						OR (c.i_accepter_id = %2\$s AND u.id=c.i_requester_id))) AND n.s_ownership = 'ownerpost'
						OR
						n.i_owner_id = '%2\$s'
					) )

				
				) t
					"
				, $this->db->dbprefix, intval($i_user_id)
			);
		

		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}

	
	public function insert($arr) {
		#dump($arr);
		$this->db->insert('user_newsfeeds', $arr);
		return $this->db->insert_id();
	}

	public function update_by_id($id, $arr) {
		$this->db->update('user_newsfeeds', $arr, array('id' => $id));
	}


	public function delete_by_id($id) {
		$sql = sprintf( "DELETE FROM %suser_newsfeeds WHERE id=%s", $this->db->dbprefix, $id );

		$this->db->query($sql);
		
		$sql = sprintf( "DELETE FROM %suser_newsfeed_comments WHERE i_newsfeed_id=%s", $this->db->dbprefix, $id );

		$this->db->query($sql);
		
		# delete from like table #
		$sql = sprintf( "DELETE FROM %suser_newsfeed_like WHERE i_newsfeed_id=%s", $this->db->dbprefix, $id );

		$this->db->query($sql);
	}
	
	

	
}
