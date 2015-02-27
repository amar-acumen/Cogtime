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
					n.s_type = 'friend_with' AND n.i_status='1' AND
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
					AND n.s_type != 'friend_with' AND n.i_status='1' AND
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
					n.s_type = 'friend_with' AND n.i_status='1' AND
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
					AND n.s_type != 'friend_with' AND n.i_status='1' AND
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

		$query = $this->db->query($sql);//echo "sql ==>". nl2br($sql) ."<br />"; 
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
//pr($result_arr,1);
		return $result_arr;
	}

	 /*get total of my($user_id) newsfeeds */
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
	
	
	
	
	###### privacy settings::::
	
	public function get_privacy_settings_wall_by_wall_owner_id($i_owner_id)
	{
		 $s_qry =  "SELECT * FROM cg_wallpost_privacy WHERE i_wall_owner_id = {$i_owner_id}  ";
		  $rs=$this->db->query($s_qry); 
		  #echo $this->db->last_query();
		  foreach($rs->result() as $row)
		  {
			  if($row->s_section=='Ring User' || $row->s_section=='Prayer Group')
			  	$returnarr[$row->s_section][]	= $row->i_user_id;
			else
				$returnarr[$row->s_section][]	= $row->i_user_id;
		  }
		 
          return $returnarr;
	}
	
	public function get_privacy_settings_likeComment_by_wall_owner_id($i_owner_id)
	{
		 $s_qry =  "SELECT * FROM cg_wallcommentlike_privacy WHERE i_wall_owner_id = {$i_owner_id}  ";
		  $rs=$this->db->query($s_qry); 
		  #echo $this->db->last_query();
		  foreach($rs->result() as $row)
		  {
			  if($row->s_section=='Ring User' || $row->s_section=='Prayer Group')
			  	$returnarr[$row->s_section][]	= $row->i_user_id;
			else
				$returnarr[$row->s_section][]	= $row->i_user_id;
		  }
		 
          return $returnarr;
	}
	
	
	########### changed ::: method 
	/* get all my($user_id) newsfeeds */
	public function check_public_newsfeeds_privacy_by_user_id($i_user_id) {
		
		$uid = $logged_user_id = intval(decrypt($this->session->userdata('user_id')));
		$profile_id = $i_user_id;
		
		#### adding for public:::: 
		$mp_sql = "  SELECT  if ((mp.i_friend_privacy = '0' &&  mp.i_netpal_privacy = '0' &&  mp.i_prayer_partner_privacy = '0' 
		 					&& mp.i_ring_privacy = '0' &&  mp.i_prayer_group_privacy = '0'
						    ) , '' ,
						       (concat(IF(mp.i_friend_privacy = '1', '\"Friend\", ', ''),''
		 							, IF(mp.i_netpal_privacy = '1', '\"Netpal\", ',''),'' 
									,IF(mp.i_prayer_partner_privacy = '1', '\"Prayer Partner\", ', '') , ''
									,IF(mp.i_ring_privacy = '1', '\"Ring User\", ', ''),''
									,IF(mp.i_prayer_group_privacy = '1', '\"Prayer Group\", ', '')))) as str
						FROM cg_privacy_settings mp 
						WHERE
						mp.i_user_id = '".$profile_id."' and mp.s_section_name = 'wallCommentLike'" ;
		 
		 $res_data = $this->db->query($mp_sql); 
		 $res_data_arr = $res_data->result_array();
		//pr($res_data_arr);
	   # echo $res_data_arr[0]['str'].' #$#$'; 
	    if($res_data_arr[0]['str'] != '') 
	    	$PRIVACY_STR = " AND pp.s_section IN  (".substr($res_data_arr[0]['str'], 0, -2).")";
		else
			$PRIVACY_STR = '';



		$check_user_level_perm =	"SELECT COUNT(pp.i_user_id) as total FROM cg_wallcommentlike_privacy as pp 
									      WHERE pp.i_wall_owner_id= '".$profile_id."' ";
			 
		$is_user_level = $this->db->query($check_user_level_perm)->result_array();
		
		#echo 'total::: '.($is_user_level[0]['total']);
		
		$show_comment = false;
		#### if public no restriction show all 
		if($res_data_arr[0]['str'] == ''){
					$show_comment = true;
		}
		### if user level permission not given  i.e $is_user_level[0]['total'] =0  
		#then check for group level else check user level
		elseif($is_user_level[0]['total'] == 0){
			
			## check user relationship if logged user has privacy permission of album 	
			$network_arr = CheckUserNetwork($profile_id);
			#pr($network_arr);
			$PRIVACY_ARR = explode(', ',substr($res_data_arr[0]['str'], 0, -2)); 
			#pr($PRIVACY_ARR);
			$privacy_result = array_intersect($network_arr, $PRIVACY_ARR);
			
			if(count($privacy_result)){ 
			### fetch all albums photos without user level permission i.e. only group level permission
			### check 
				$show_comment = true;
			}
			
		}
		else{	### fetch all  with user level permission	  
			 $SQL = "SELECT COUNT(*) as total FROM cg_wallcommentlike_privacy as pp 
									 WHERE pp.i_wall_owner_id= '".$profile_id."'  AND pp.i_user_id = ".$uid;	
			 $res = $this->db->query($SQL); 
		 	 $result = $res->result_array();	 //pr($result);
			 if($result[0]['total'] > 0)
			 	$show_comment = true;
			 else  
			 	$show_comment = false;
		}
		
		#echo $show_comment.'dwedw'; exit;
		
		return $show_comment;
	}
public function get_owner_post_by_id($i_user_id,$s_where,$i_start_limit='', $i_no_of_page='')
{
/*if($i_start_limit == "") {
$sql="select distinct c.* from cg_users u ,cg_user_newsfeeds c where 1 and c.i_owner_id='".$i_user_id."'and u.i_isdeleted='1'";

}
else*/
{$sql="select distinct c.* ,CONCAT(u.s_first_name,' ',u.s_last_name) AS s_profile_name , (SELECT count(*) FROM cg_user_newsfeed_comments RC WHERE RC.i_newsfeed_id = c.id) as total_comments from cg_users u ,cg_user_newsfeeds c ".$s_where." and c.i_owner_id='".$i_user_id."'and u.i_isdeleted='1' ORDER BY c.id DESC  limit ".$i_start_limit.",".$i_no_of_page;}
#echo $sql;
$query=$this->db->query($sql);
$result_arr=$query->result_array();
//echo $sql;exit;
//echo $i_start_limit;
//pr($result_arr,1);
return $result_arr;
}
public function get_total_owner_post_by_id($i_user_id,$s_where)
{
$sql="select count(distinct c.id) as count from cg_users u ,cg_user_newsfeeds c ".$s_where." and c.i_owner_id='".$i_user_id."'and u.i_isdeleted='1' ORDER BY c.id DESC ";
#echo $sql;
$query=$this->db->query($sql);
$result_arr=$query->result_array();
return $result_arr['0']['count'];
}
public function get_by_comment_id($i_newsfeed_id){
   {$sql="select distinct c.* ,CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name, u.s_profile_photo from cg_users u ,cg_user_newsfeed_comments c where u.id = c.i_user_id AND c.i_newsfeed_id='$i_newsfeed_id' ";}
#echo $sql;
$query=$this->db->query($sql);
$result_arr=$query->result_array();
//echo $sql;exit;
//echo $i_start_limit;
//pr($result_arr,1);
return $result_arr;
    
}
public function get_by_comment_id_count($where){
   {$sql="select count(*) as num from cg_user_newsfeed_comments $where";}
#echo $sql;
$query=$this->db->query($sql);
$result_arr=$query->result_array();
//echo $sql;exit;
//echo $i_start_limit;
//pr($result_arr,1);
return $result_arr; 
    
}

public function get_all_comments_wall($s_where,$i_start_limit='', $i_no_of_page=''){
    //die('jj');
    $sql="select c.*,p.i_owner_id,p.data,CONCAT(u.s_first_name,' ',u.s_last_name) AS s_profile_name "
            . "   from cg_user_newsfeed_comments c,cg_user_newsfeeds p,cg_users u "
            . "$s_where and c.i_newsfeed_id = p.id  and u.id = c.i_user_id ORDER BY c.id DESC limit ".$i_start_limit.",".$i_no_of_page;
   // echo $sql;  
    $query=$this->db->query($sql);
$result_arr=$query->result_array();
//echo $sql;
//echo $i_start_limit;
//pr($result_arr);
return $result_arr;
}

    public function get_all_comments_wall_total($s_where){
    
    $sql="select count(*)as count from cg_user_newsfeed_comments c,cg_users u  $s_where and  u.id = c.i_user_id ORDER BY c.id DESC";
    
   //echo $sql;
     // exit; 
    $query=$this->db->query($sql);
     $result_arr=$query->result_array();
    // pr($result_arr);
    //pr($query);
//    if($query->num_rows() > 0){
//        $result_arr=$query->result_array();
//        echo  $result_arr['0']['count'];
//    }

//echo $result_arr['0']['count'];exit;
        return $result_arr;

}
}
