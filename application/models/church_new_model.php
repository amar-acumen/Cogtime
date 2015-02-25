<?php
include_once(APPPATH.'models/base_model.php');
class Church_new_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

        function get_church_info($c_id){
             $query = $this->db->get_where('cg_church', array('id' => $c_id));
             $result = $query->result();
			 
             return $result;
            
        }
        function get_service_info($c_id){
           
              $query = $this->db->query('select * from cg_church_schedul where c_id = '.$c_id.' ORDER BY c_update ASC LIMIT 0, 5');
             $result = $query->result();
             return $result;
            
        }
		
		function get_church_admin_data($c_id)
		{
			$query = $this->db->get_where('cg_church_admin',array('ch_id'=>$c_id));
			$result = $query->result();
			
			return $result[0];
		}
    	function get_churchmembers($c_id,$where='',$i_start=0,$order_by=' cm.id DESC',$i_limit=0){
    		$limit = (is_numeric($i_start) && is_numeric($i_limit)) ? " Limit " . intval($i_start) . "," . intval($i_limit) : '';
        	$order_by =  " ORDER BY {$order_by}" ;
            $sql = 'select *,u.id AS mid, CONCAT(u.s_first_name, " ", u.s_last_name) AS member_name,cm.id AS cmid from cg_church_member AS cm 
            LEFT JOIN cg_users AS u ON cm.member_id=u.id WHERE cm.church_id = '.$c_id.' AND cm.is_deleted=0 '.$where.' '.$order_by.' '.$limit.'';
				//echo $sql;
            $query = $this->db->query($sql);
            $result = $query->result();
            return $result;
            
        }
        function get_churchmembers_count($c_id,$where=''){
        	
            $sql = 'select COUNT(*) AS totrow from cg_church_member AS cm LEFT JOIN cg_users AS u ON cm.member_id=u.id 
            WHERE cm.church_id = '.$c_id.' AND cm.is_deleted=0 '.$where;
				//echo $sql;
            $query = $this->db->query($sql);
            $result = $query->result();
            return $result[0]->totrow;
        }

        function get_churchsubadmin($c_id,$i_start=0,$i_limit=0){
			$limit = (is_numeric($i_start) && is_numeric($i_limit)) ? " Limit " . intval($i_start) . "," . intval($i_limit) : '';
            $sql = 'select *,cm.id AS mid from cg_church_member AS cm,cg_users AS u 
                where cm.member_id=u.id AND cm.church_id = '.$c_id.' AND is_deleted=0 AND role=2 ORDER BY cm.id DESC'.$limit.'';
            $query = $this->db->query($sql);
            $result = $query->result();
            return $result;
            
        }
		
		function get_churchsubadmin_count($c_id){
        	
            $sql = 'select COUNT(*) AS totrow from cg_church_member AS cm,cg_users AS u 
                where cm.member_id=u.id AND cm.church_id = '.$c_id.' AND is_deleted=0 AND role=2';
				//echo $sql;exit;
            $query = $this->db->query($sql);
            $result = $query->result();
            return $result[0]->totrow;
        }
		
		function get_churchmembers_search_result($c_id,$where=''){
            $sql = 'select *,cm.id AS mid, CONCAT(user.s_first_name, " ", user.s_last_name) AS member_name from cg_church_member AS cm LEFT JOIN cg_users AS user ON cm.member_id=user.id WHERE cm.church_id = '.$c_id.' AND is_approved=1 AND cm.is_deleted=0 '.$where.' ORDER BY cm.id DESC';
			//echo $sql;
            $query = $this->db->query($sql);
            $result = $query->result();
            return $result;
            
        }
		/** prayer_group**/
		function create_prayer_group($arr)
		{
			$this->db->insert('cg_church_prayer_group',$arr);
			return $this->db->insert_id();
		}
		
		function get_prayer_group_by_church($church_id,$order,$direction)
		{
			$res=$this->db->order_by($order,$direction)->get_where('cg_church_prayer_group',array('i_owner_id'=>$church_id,'i_isenabled'=>1));
		
			//echo $this->db->last_query();
			$result=$res->result();
			foreach($result as $key=>$res)
			{
				$sql="select cm.id as c from cg_church_prayer_group cp left JOIN cg_church_prayer_group_members cm  on cp.id=cm.i_prayer_group_id where cm.i_prayer_group_id=".$res->id." and cm.s_status='accepted'";
				$s=$this->db->query($sql);
				//echo $sql;
				$mem=$s->result();
				$result[$key]->members=count($mem);
				//pr($result,1);
			}
			//pr($result,1);
			return $result;
		}
		function get_prayer_group_by_user_id($church_id,$user_id,$orderby)
		{
			$sql="select * from cg_church_prayer_group cp left JOIN cg_church_prayer_group_members cm  on cp.id=cm.i_prayer_group_id where cp.i_owner_id=".$church_id." and cm.i_user_id=".$user_id." and cm.s_status='accepted' and cp.i_isenabled=1 order by ".$orderby."";
			//echo $sql;
			$s=$this->db->query($sql);
			$result=$s->result();
			//pr($result);
			foreach($result as $key=>$res)
			{
				$sql="select cm.id as c, cm.* from cg_church_prayer_group_members cm left JOIN cg_church_prayer_group cp  on cp.id=cm.i_prayer_group_id where cm.i_prayer_group_id=".$res->id." and i_request=1";
				$s=$this->db->query($sql);
				//echo $sql;
				$mem=$s->result();
				//pr($mem);
				$result[$key]->members=count($mem);
				//pr($result);exit;
			}
			return $result;
		}
		
		function get_members_by_grpid($gid)
		{
			$res=$this->db->get_where('cg_church_prayer_group_members',array('i_prayer_group_id'=>$gid,'s_status'=>'accepted'));
			//echo $this->db->last_query();exit;
			$result=$res->result();
			return $result;
		}
		
		function insert_group_member($arr)
		{
			$result=$this->db->get_where('cg_church_prayer_group_members',array('s_status'=>'rejected','i_prayer_group_id'=>$arr['i_prayer_group_id'],'i_user_id'=>$arr['i_user_id']));
			//echo $this->db->last_query();
			if($result == null)
			{
				$res=array();
			}
			else
			{
				$res=$result->result();
			}
			if(count($res) != 0)
			{
				$this->db->where('id',$res[0]->id);
				$this->db->update('cg_church_prayer_group_members',$arr);
				return $res[0]->id;
			}
			else
			{
				$this->db->insert('cg_church_prayer_group_members',$arr);
				return $this->db->insert_id();
			}
			
		}
		
		function get_invitation($cid,$user)
		{
			
			$sql="select cp.i_owner_id,cm.*,cp.s_group_name from cg_church_prayer_group cp left JOIN cg_church_prayer_group_members cm on cp.id=cm.i_prayer_group_id where cp.i_owner_id=".$cid." and cm.i_user_id=".$user." and cm.s_status='pending'";
			//echo $sql;
			$res=$this->db->query($sql);
			$result=$res->result();
			return $result;
		}
		
		
		function insert_grp_post($arr)
		{
			$this->db->insert('cg_church_prayer_group_post',$arr);
			return $this->db->insert_id;
		}
		
		function get_posts_by_grpid($grp_id)
		{
			//$res=$this->db->get_where('cg_church_prayer_group_post',array('i_prayer_group_id'=>$grp_id));
                    $res = $this->db->query('select * from cg_church_prayer_group_post where i_prayer_group_id = "'.$grp_id.'" ORDER BY dt_created_on DESC');
			$result=$res->result();
			return $result;
		}
		
	/*-- prayer group--*/
	function get_church_activity_feed_comments_count($c_id,$s_where=''){
            $sql = '(select cncm.id,cncm.i_feed_id,cncm.s_contents, CONCAT(user.s_first_name, " ", user.s_last_name) AS member_name, cnf.data AS feedtitle , "wallpost" AS logo,"1" AS post_type, 
                      cncm.dt_created_on AS createdon
                      from cg_church_newsfeed_comments AS cncm LEFT JOIN cg_users AS user ON cncm.i_user_id = user.id 
                      LEFT JOIN cg_church_newsfeed AS cnf ON cncm.i_feed_id = cnf.id where cncm.church_id = "'.$c_id.'"'.$s_where.')
                      UNION
                      (select cncm.id,cncm.i_ring_id as i_feed_id ,cncm.s_contents, CONCAT(user.s_first_name, " ", user.s_last_name) AS member_name, cnf.s_ring_name AS feedtitle, s_logo AS logo,"2" AS post_type, 
                        cncm.dt_created_on AS createdon  
                      from cg_church_ring_post_comments AS cncm LEFT JOIN cg_users AS user ON cncm.i_user_id = user.id 
                      LEFT JOIN cg_church_ring AS cnf ON cncm.i_ring_id = cnf.id where cncm.church_id = "'.$c_id.'"'.$s_where.')';
			//echo $sql;exit;
            $query = $this->db->query($sql);
            $result = $query->result();
			//pr($result);
            return count($result);
            
        }
		
		function get_church_activity_feed_comments($c_id,$s_where='',$page,$offset){
            $sql = '(select cncm.id,cncm.i_feed_id,cncm.s_contents, CONCAT(user.s_first_name, " ", user.s_last_name) AS member_name, cnf.data AS feedtitle , "wallpost" AS logo,"1" AS post_type, 
                      cncm.dt_created_on AS createdon
                      from cg_church_newsfeed_comments AS cncm LEFT JOIN cg_users AS user ON cncm.i_user_id = user.id 
                      LEFT JOIN cg_church_newsfeed AS cnf ON cncm.i_feed_id = cnf.id where cncm.church_id = "'.$c_id.'" '.$s_where.' )
                      UNION
                      (select cncm.id,cncm.i_ring_id as i_feed_id ,cncm.s_contents, CONCAT(user.s_first_name, " ", user.s_last_name) AS member_name, cnf.s_ring_name AS feedtitle, s_logo AS logo,"2" AS post_type, 
                        cncm.dt_created_on AS createdon  
                      from cg_church_ring_post_comments AS cncm LEFT JOIN cg_users AS user ON cncm.i_user_id = user.id 
                      LEFT JOIN cg_church_ring AS cnf ON cncm.i_ring_id = cnf.id where cncm.church_id ="'.$c_id.'" '.$s_where.') limit '.$page.','.$offset.'';
			//echo $sql;exit;
            $query = $this->db->query($sql);
            $result = $query->result();
			//pr($result,1);
            return $result;
            
        }
	public function update_church_about_info($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update('cg_church', $arr, array('id'=>$id));
		//echo $this->db->last_query();
		return true;
	}
	
	public function delete_prayer_group($id) {
        $sql = sprintf('DELETE FROM  cg_church_prayer_group WHERE id=%s', $id);
        $this->db->query($sql);

        $sql1 = sprintf('DELETE FROM  cg_church_prayer_group_members WHERE i_prayer_group_id=%s', $id);
        $this->db->query($sql1);
        $sql2 = sprintf('DELETE FROM  cg_church_prayer_group_notifications WHERE i_prayer_group_id=%s', $id);
        $this->db->query($sql2);

        /*$sql2 = sprintf('DELETE FROM  cg_church_prayer_group_post WHERE i_prayer_group_id=%s', $id);
        $this->db->query($sql2);*/
		//echo $this->db->last_query();
		return true;
    }
	
	 public function leave_prayer_group($id, $uid) {
        $sql1 = 'DELETE FROM  cg_church_prayer_group_members WHERE i_prayer_group_id=' . $id . ' AND i_user_id=' . $uid;
		//echo $sql1;
        $this->db->query($sql1);
        $sql2 = 'DELETE FROM  cg_church_prayer_group_notifications WHERE i_prayer_group_id= "'.$id.'" AND i_requester_user_id = "'.$uid.'" ';
        $this->db->query($sql2);
		return true;
    }
	
	public function checkGroupMaxLimit($max_limit, $user_id, $type) {

        if ($type == 'created') {

            $SQL = "SELECT COUNT(*) as total 
					FROM cg_church_prayer_group 
						WHERE i_owner_id  = {$user_id} 
						";
            $query = $this->db->query($SQL);
            $result_arr = $query->result_array();

            if ($result_arr[0]['total'] < $max_limit) {
                return false;
            } else if ($result_arr[0]['total'] >= $max_limit) {
                return true;
            }
        } else if ($type == 'member') {

            $SQL = "SELECT COUNT(*) as total 
					FROM c 
						WHERE i_user_id  = {$user_id} 
						AND s_status = 'accepted' ";
            $query = $this->db->query($SQL);
            $result_arr = $query->result_array();

            if ($result_arr[0]['total'] < $max_limit) {
                return false;
            } else if ($result_arr[0]['total'] >= $max_limit) {
                return true;
            }
        }
    }
	
	 ### accept invitation 

    public function accept_invitation($where, $arr, $msg_arr) {
        $this->db->update('cg_church_prayer_group_members', $arr, $where);
        //echo $this->db->last_query();exit;
        $this->db->update('messages', array('i_ended' => '1'), $msg_arr);

        //exit;
    }

    ### decline invitation 

    public function decline_invitation($where, $arr, $msg_arr) {
        //$this->db->delete('cg_church_prayer_group_members', $where);
		$this->db->update('cg_church_prayer_group_members', $arr, $where);
		//echo $this->db->last_query();exit;
        $this->db->update('messages', array('i_ended' => '1'), $msg_arr);
    }
	
	function get_prayer_group_details($prayer_group_id){
           
              $query = $this->db->query('select * from cg_church_prayer_group where id = '.$prayer_group_id.' ORDER BY dt_created_on DESC');
             $result = $query->result();
             return $result;
            
        }
		
		function get_prayer_group_search_result_by_church($church_id,$user_id,$order='dt_created_on',$direction='DESC',$where='')
		{
		$sql="select * from cg_church_prayer_group where i_owner_id=".$church_id." and i_isenabled=1 ".$where."order by ".$order." ". $direction;
		//echo $sql;
		$res=$this->db->query($sql);
		
		$result=$res->result();
			foreach($result as $key=>$res)
			{
				$sql="select cm.id as c from cg_church_prayer_group cp left JOIN cg_church_prayer_group_members cm  on cp.id=cm.i_prayer_group_id where cm.i_prayer_group_id=".$res->id." and cm.s_status='accepted'";
				$s=$this->db->query($sql);
				//echo $sql;
				$mem=$s->result();
				$result[$key]->members=count($mem);
				//pr($result);
				$sql1="select count(cm.id) as c from cg_church_prayer_group_members cm left JOIN cg_church_prayer_group cp  on cp.id=cm.i_prayer_group_id where cm.i_prayer_group_id=".$res->id."  and cm.i_user_id=".$user_id." and cm.s_status='accepted'";
				$s1=$this->db->query($sql1);
				//echo $sql1;exit;
				$mem1=$s1->result();
				$result[$key]->is_member=$mem1[0]->c;
				if($mem1[0]->c == 0)
				{
					$sql1="select count(cm.id) as c from cg_church_prayer_group_members cm left JOIN cg_church_prayer_group cp  on cp.id=cm.i_prayer_group_id where cm.i_prayer_group_id=".$res->id."  and cm.i_user_id=".$user_id." and cm.s_status='pending'";
					$s1=$this->db->query($sql1);
					//echo $sql1;exit;
					$mems=$s1->result();
					if($mems[0]->c != 0)
					{
						$result[$key]->is_member='pending';
					}
				}
			}
			//pr($result,1);
			return $result;
		}
		
		public function get_pending_groups_requests_sent_for_church($i_user_id, $s_where) {


        $sql = sprintf(" 
		  				  SELECT 
						  pg_mem.id as rec_id,
						  pg_mem.i_user_id ,
						  pg_mem.s_status,
						  pg_mem.dt_created_on,
						  pg_mem.dt_joined_on,
						  pg.s_group_name,
						  pg.i_owner_id,
						  pg_mem.i_prayer_group_id
						  FROM  cg_church_prayer_group_members pg_mem
						  LEFT JOIN cg_church_prayer_group pg ON pg.id = pg_mem.i_prayer_group_id
						  LEFT JOIN cg_users u ON pg_mem.i_user_id = u.id
						  
						  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND pg.i_isenabled = 1 
						  AND pg.i_owner_id = %2\$s AND pg_mem.i_request = '0' 
						  AND pg_mem.s_status = 'pending' 
						   %3\$s
						   group by pg_mem.id 
					  
					"
                , $this->db->dbprefix, intval($i_user_id), $s_where
        );
        //echo nl2br($sql); exit;
        $query = $this->db->query($sql);
        $result_arr = $query->result_array(); //pr($result_arr,1);
        return $result_arr;
    }
	
	public function get_pending_groups_requests_recieved_for_church($i_user_id, $s_where) {


        $sql = sprintf(" 
		  				  SELECT 
						  pg_mem.id as rec_id,
						  pg_mem.i_user_id ,
						  pg_mem.s_status,
						  pg_mem.dt_created_on,
						  pg_mem.dt_joined_on,
						  pg.s_group_name,
						  pg.i_owner_id,
						  pg_mem.i_prayer_group_id
						  FROM  cg_church_prayer_group_members pg_mem
						  LEFT JOIN cg_church_prayer_group pg ON pg.id = pg_mem.i_prayer_group_id
						  LEFT JOIN cg_users u ON pg_mem.i_user_id = u.id
						  
						  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND pg.i_isenabled = 1 
						  AND pg.i_owner_id = %2\$s AND pg_mem.i_request = '1' 
						  AND pg_mem.s_status = 'pending' 
						   %3\$s
						   group by pg_mem.id 
					  
					"
                , $this->db->dbprefix, intval($i_user_id), $s_where
        );
       // echo nl2br($sql); exit;
        $query = $this->db->query($sql);
        $result_arr = $query->result_array(); //echo '1';pr($result_arr,1);
        return $result_arr;
    }
	
	
		
	function insert_group_notifications($arr)
	{
		$this->db->insert('cg_church_prayer_group_notifications',$arr);
		return $this->db->insert_id();
		
	}
	
	# function to check if request_already_sent

    function request_already_sent($i_user_id = '', $i_group_id = '') {
        $SQL = sprintf("SELECT COUNT(*) AS `check_count` FROM %s 
						WHERE `i_user_id`='%s'  AND `i_prayer_group_id` = '%s' 
						AND `s_status` = 'pending' ", $this->db->CHURCH_PRAYER_GROUP_MEMBERS, $i_user_id, $i_group_id);
        $ROW = $this->db->query($SQL)->row_array(); //echo $this->db->last_query(); exit;

        if ($ROW['check_count'])
            return 1;
        else
            return 0;
    }
	
	public function get_pending_groups_requests_recieved($i_user_id,$c_id, $s_where) {


        $sql = sprintf(" 
		  				  SELECT 
						  pg_mem.id as rec_id,
						  pg_mem.i_user_id ,
						  pg_mem.s_status,
						  pg_mem.dt_created_on,
						  pg_mem.dt_joined_on,
						  pg.s_group_name,
						  pg.i_owner_id,
						  pg_mem.i_prayer_group_id
						  FROM  cg_church_prayer_group_members pg_mem
						  LEFT JOIN cg_church_prayer_group pg ON pg.id = pg_mem.i_prayer_group_id
						  LEFT JOIN cg_users u ON pg_mem.i_user_id = u.id
						  
						  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND pg.i_isenabled = 1 
						  AND pg_mem.i_user_id = %2\$s AND pg_mem.i_request = '0' 
						  AND pg_mem.s_status = 'pending' AND pg.i_owner_id= %3\$s 
						   %4\$s
						   group by pg_mem.id 
					  
					"
                , $this->db->dbprefix, intval($i_user_id),$c_id, $s_where
        );
        //echo nl2br($sql); exit;
        $query = $this->db->query($sql);
        $result_arr = $query->result_array(); //pr($result_arr,1);
        return $result_arr;
    }

    public function get_pending_groups_requests_sent($i_user_id,$c_id,$s_where) {

	//echo $c_id;exit;
        $sql = sprintf(" 
		  				  SELECT 
						  pg_mem.id as rec_id,
						  pg_mem.i_user_id ,
						  pg_mem.s_status,
						  pg_mem.dt_created_on,
						  pg_mem.dt_joined_on,
						  pg.s_group_name,
						  pg.i_owner_id,
						  pg_mem.i_prayer_group_id
						  FROM  cg_church_prayer_group_members pg_mem
						  LEFT JOIN cg_church_prayer_group pg ON pg.id = pg_mem.i_prayer_group_id
						  LEFT JOIN cg_users u ON pg_mem.i_user_id = u.id
						  
						  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND pg.i_isenabled = 1 
						  AND pg.i_owner_id = %2\$s AND pg_mem.i_request = '1' 
						  AND pg_mem.s_status = 'pending' AND pg_mem.i_user_id=%3\$s 
						   %4\$s
						   group by pg_mem.id 
					  
					"
                , $this->db->dbprefix, $c_id,intval($i_user_id), $s_where
        );
        //echo nl2br($sql); exit;
        $query = $this->db->query($sql);
        $result_arr = $query->result_array(); //pr($result_arr,1);
        return $result_arr;
    }
                 function get_user_church_info($i_user_id){
                    
                     $sql_churchmember = "SELECT *,ch.id AS chid FROM cg_church AS ch,cg_church_member AS chm WHERE ch.id=chm.church_id AND chm.member_id='".$i_user_id."'";

              $query_churchmember = $this->db->query($sql_churchmember);
			  $numrowmember = $query_churchmember->num_rows();
              $result_churchmember = $query_churchmember->result();
                $query = $this->db->get_where('cg_church_admin', array('ch_admin_id' => $user_id));
              $result = $query->result();
              return $result;
                    
                }
                
                 /**********************************************************/
                
                
                
                
                    public function get_my_groups_notificaions($i_user_id, $s_where, $i_start_limit = '', $i_no_of_page = '') {

        if ($i_start_limit == '' && $i_no_of_page == '') {
            $limit = '';
        } else {
            $limit = 'LIMIT ' . $i_start_limit . ' , ' . $i_no_of_page;
        }
        
        if($_SESSION['charch_super_admin'] =='yes'){

        $sql = 'select * from cg_church_prayer_group_notifications where church_id = '.$_SESSION["logged_church_id"].' '.$s_where.' order by id DESC  '.$limit.' ';
        }else{
            $user_id = intval(decrypt($this->session->userdata('user_id')));
            $S_new_where = ' AND i_requester_user_id = '.$user_id.'';
            $sql = 'select * from cg_church_prayer_group_notifications where church_id = '.$_SESSION["logged_church_id"].' '.$S_new_where.' '.$s_where.' order by id DESC  '.$limit.' ';
        }			
                 
       

        $query = $this->db->query($sql);// echo "sql ==>". nl2br($sql) ."<br />"; 
        $result_arr = $query->result_array();
       // pr($result_arr,1);

        

        return $result_arr;
    }

    public function get_total_my_groups_notificaions($i_user_id, $s_where) {

            if($_SESSION['charch_super_admin'] =='yes'){
         $sql = 'select * from cg_church_prayer_group_notifications where church_id = '.$_SESSION["logged_church_id"].' '.$s_where.'  '.$limit.'';
            }else{
               $user_id = intval(decrypt($this->session->userdata('user_id')));
            $S_new_where = ' AND i_requester_user_id = '.$user_id.''; 
            $sql = 'select * from cg_church_prayer_group_notifications where church_id = '.$_SESSION["logged_church_id"].' '.$S_new_where.' '.$s_where.'  '.$limit.'';
            }

        $query = $this->db->query($sql);
        $result_arr = $query->result_array();
        return $result_arr[0]['count'];
    }
    
    public function get_group_info($c_id) {
         if($_SESSION['charch_super_admin'] =='yes'){
        $query = $this->db->get_where('cg_church_prayer_group', array('i_owner_id' => $c_id));
                 $result = $query->result();
         }else{
              $user_id = intval(decrypt($this->session->userdata('user_id')));
             $query = $this->db->query("Select pg.* from cg_church_prayer_group as pg , cg_church_prayer_group_members as pm where pg.i_owner_id ='".$c_id."' AND pg.id = pm.i_prayer_group_id AND pm.i_user_id ='".$user_id."' AND pm.s_status LIKE 'accepted' ");
              $result = $query->result();
         }
                 return $result;
        
    }
    
    
    /*******************************************************get all  group*****************************************/
    public function show_group_all($s_where=null,$i_start=null,$i_limit=null,$s_order_by=null)
	{
	try
        {
           // die('dd');
		  	$ret_=array();
			
			
                       $s_qry = 'select * from cg_church_prayer_group where 1 '.$s_where.'  ';
          $s_qry= $s_qry.(trim($s_order_by)!=""?" ORDER BY ".$s_order_by."":"ORDER BY id DESC")." ".(is_numeric($i_start) && is_numeric($i_limit)?" LIMIT ".intval($i_start).",".intval($i_limit):"");
		 
		            
          $rs=$this->db->query($s_qry); 
         
         $result=$rs->result();
         $user_id = intval(decrypt($this->session->userdata('user_id')));
			foreach($result as $key=>$res)
			{
				$sql="select cm.id as c from cg_church_prayer_group cp left JOIN cg_church_prayer_group_members cm  on cp.id=cm.i_prayer_group_id where cm.i_prayer_group_id=".$res->id." and cm.s_status='accepted'";
				$s=$this->db->query($sql);
				//echo $sql;
				$mem=$s->result();
				$result[$key]->members=count($mem);
				//pr($result);
				$sql1="select count(cm.id) as c from cg_church_prayer_group_members cm left JOIN cg_church_prayer_group cp  on cp.id=cm.i_prayer_group_id where cm.i_prayer_group_id=".$res->id."  and cm.i_user_id=".$user_id." and cm.s_status='accepted'";
				$s1=$this->db->query($sql1);
				//echo $sql1;exit;
				$mem1=$s1->result();
				$result[$key]->is_member=$mem1[0]->c;
				if($mem1[0]->c == 0)
				{
					$sql1="select count(cm.id) as c from cg_church_prayer_group_members cm left JOIN cg_church_prayer_group cp  on cp.id=cm.i_prayer_group_id where cm.i_prayer_group_id=".$res->id."  and cm.i_user_id=".$user_id." and cm.s_status='pending'";
					$s1=$this->db->query($sql1);
					//echo $sql1;exit;
					$mems=$s1->result();
					if($mems[0]->c != 0)
					{
						$result[$key]->is_member='pending';
					}
				}
			}
			//pr($result,1);
			return $result;
         // pr($res_,1);
		    }
			catch(Exception $err_obj)
			{
				show_error($err_obj->getMessage());
			}           
    
    
    }
	
	
	public function gettotal_group($s_where)
    {
        try
        {
          $ret_=0;
         
				
//		  $s_qry = "SELECT COUNT(tab.ringid) AS i_total FROM ((SELECT r.*, r.id AS ringid, c.s_category_name AS s_category_name, CONCAT(u.s_first_name,' ',u.s_last_name) AS owner_name
//							FROM {$this->db->RING} r LEFT JOIN {$this->db->USERS} AS u 
//								ON r.i_user_id=u.id , {$this->db->RING_CAT} c WHERE r.i_category_id=c.id AND r.i_isenabled=1 "
//						.$s_where.") UNION".
//						"(SELECT r.*, r.id AS ringid, c.s_category_name AS s_category_name, CONCAT(u.s_first_name,' ',u.s_last_name) AS owner_name
//							FROM {$this->db->RING} r LEFT JOIN {$this->db->USERS} AS u 
//								ON r.i_user_id=u.id , {$this->db->RING_CAT} AS c ,{$this->db->RING_INV_USER} AS inv 
//								WHERE r.i_category_id=c.id AND r.i_isenabled=1 AND inv.i_ring_id=r.id AND inv.i_joined = 1 ".$s_where1.")) AS tab"; 
          #echo $s_qry;
           $s_qry = 'select count(*)as total from cg_church_prayer_group where 1 '.$s_where.'   ';
           //echo ($s_qry);exit;
		  $rs=$this->db->query($s_qry);
                  $result = $rs->result_array();
                 // pr($result);
                 // echo $result[0]['total'];
                  return $result[0]['total'];
          //$i_cnt=0;
         // pr($rs->result(),1);
//          if(is_array($rs->result()))
//          {
//              foreach($rs->result() as $row)
//              {
//                  $ret_=intval($row->i_total); 
//              }    
//              $rs->free_result();          
//          }
//          $this->db->trans_commit();///new
//          unset($s_qry,$rs,$row,$i_cnt,$s_where);
//          return $ret_;
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }           
    }
    /*****************************************************************************************************/
				
				
}
