<?php

include_once(APPPATH . 'models/base_model.php');

class Prayer_group_model extends Base_model {

    public function __construct() {
        parent::__construct();
    }

    ### get group detail by id

    public function get_group_detail_by_id($id) {

        $sql = 'SELECT * FROM ' . $this->db->PRAYER_GROUP . '  where id = "'.$id.'"  ';

        $query = $this->db->query($sql);
        $result_arr = $query->result_array();

        if (count($result_arr)) {
            foreach ($result_arr as $key => $val) {
                $result_arr[$key]['total_member'] = $this->get_total_members_grp_id($val['id']);
            }
        }
        return $result_arr[0];
    }

    public function get_group_name_by_id($id) {
        $sql = 'SELECT s_group_name FROM ' . $this->db->PRAYER_GROUP . '  where id = "'.$id.'"  ';
        $query = $this->db->query($sql);
        $result_arr = $query->result_array();
        return $result_arr[0]['s_group_name'];
    }

    ## get group list created/ joined by logged user 
    ## MY groups = groups JOINED + CREATED 	

    public function get_my_groups($i_user_id, $s_where, $i_start_limit = '', $i_no_of_page = '', $orderby, $querytype = 'all') {

        if ($i_start_limit == '' && $i_no_of_page == '') {
            $limit = '';
        } else {
            $limit = 'LIMIT ' . $i_start_limit . ' , ' . $i_no_of_page;
        }

        if ($querytype == 'all') {
            $sql = "
					  (SELECT
						u.id i_user_id,
						CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
						u.s_profile_photo,
						pg.*,
						'N' as 'is_owner',
						pg_mem.dt_joined_on as creation_dt
						
						FROM cg_prayer_group pg
						LEFT JOIN cg_prayer_group_members pg_mem ON pg_mem.i_prayer_group_id = pg.id
						LEFT JOIN cg_users u ON u.id = pg.i_owner_id
						WHERE u.i_status='1'
						AND u.i_isdeleted ='1'
						AND pg.i_isenabled = 1
						AND pg.i_owner_id = u.id
						AND
						(
						pg.id in
							(SELECT pg_mem.i_prayer_group_id from cg_prayer_group_members pg_mem, cg_users u where
								pg_mem.i_user_id = '".intval($i_user_id)."' AND pg_mem.s_status = 'accepted'
							)
						
						) {$s_where} GROUP BY pg.id ORDER BY s_group_name ASC)
					 UNION
					 
					 (SELECT 

					  u.id i_user_id,
					  CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
					  u.s_profile_photo,
					  pg.*,
					  'Y' as 'is_owner' ,
					  pg.dt_created_on as creation_dt

					  
					  FROM  cg_prayer_group pg , cg_users u
					  
					  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND pg.i_isenabled = 1 
					  AND pg.i_owner_id = '".intval($i_user_id)."' AND pg.i_owner_id = u.id
					   {$s_where} ORDER BY s_group_name ASC)

				    ORDER BY {$orderby} 
					{$limit}
					";
        } else if ($querytype == 'ownership') {
            $sql = "
								 
					 (SELECT 

					  u.id i_user_id,
					  CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
					  u.s_profile_photo,
					  pg.*,
					  'Y' as 'is_owner' ,
					  pg.dt_created_on as creation_dt

					  
					  FROM  cg_prayer_group pg , cg_users u
					  
					  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND pg.i_isenabled = 1 
					  AND pg.i_owner_id = '".intval($i_user_id)."' AND pg.i_owner_id = u.id
					   {$s_where} ORDER BY s_group_name ASC)

				    ORDER BY {$orderby} 
					{$limit}
					";
        } else if ($querytype == 'members') {
            $sql = "
								 
					 (SELECT
						u.id i_user_id,
						CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
						u.s_profile_photo,
						pg.*,
						'N' as 'is_owner',
						pg_mem.dt_joined_on as creation_dt
						
						FROM cg_prayer_group pg
						LEFT JOIN cg_prayer_group_members pg_mem ON pg_mem.i_prayer_group_id = pg.id
						LEFT JOIN cg_users u ON u.id = pg.i_owner_id
						WHERE u.i_status='1'
						AND u.i_isdeleted ='1'
						AND pg.i_isenabled = 1
						AND pg.i_owner_id = u.id
						AND
						(
						pg.id in
							(SELECT pg_mem.i_prayer_group_id from cg_prayer_group_members pg_mem, cg_users u where
							pg_mem.i_user_id = '".intval($i_user_id)."' AND pg_mem.s_status = 'accepted'
							)
						
						) {$s_where}  GROUP BY pg.id ORDER BY s_group_name ASC)

				    ORDER BY {$orderby} 
					{$limit} 
					";
        }


        $query = $this->db->query($sql); #echo "sql ==>". nl2br($sql) ."<br />"; exit;
        $result_arr = $query->result_array();

        if (count($result_arr)) {
            foreach ($result_arr as $key => $val) {
                $result_arr[$key]['total_member'] = $this->get_total_members_grp_id($val['id']);
            }
        }

        # pr($result_arr);
        return $result_arr;
    }

    public function get_total_my_groups($i_user_id, $s_where, $querytype = 'all') {

        if ($querytype == 'all') {
            $sql = sprintf("
						SELECT COUNT(*) count FROM (
						( SELECT
								pg.id
								FROM cg_prayer_group pg
								LEFT JOIN cg_prayer_group_members pg_mem ON pg_mem.i_prayer_group_id = pg.id
								LEFT JOIN cg_users u ON u.id = pg.i_owner_id
								WHERE u.i_status='1'
								AND u.i_isdeleted ='1'
								AND pg.i_isenabled = 1
								AND pg.i_owner_id = u.id
								AND
								(
								pg.id in
									(SELECT pg_mem.i_prayer_group_id from cg_prayer_group_members pg_mem, cg_users u where
									pg_mem.i_user_id = %2\$s AND pg_mem.s_status = 'accepted'
									)
								
								) %3\$s GROUP BY pg.id 
						
						)
						 UNION
						 
						 (SELECT 
							  pg.id
							  FROM cg_users u, cg_prayer_group pg
							  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND pg.i_isenabled = 1 
							  AND pg.i_owner_id = %2\$s AND pg.i_owner_id = u.id  %3\$s
						   )
						 
		
						) derived_tbl
							"
                    , $this->db->dbprefix, intval($i_user_id), $s_where
            );
        } else if ($querytype == 'ownership') {

            $sql = sprintf("
						SELECT COUNT(*) count FROM (
						 (SELECT 
							  pg.id
							  FROM cg_users u, cg_prayer_group pg
							  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND pg.i_isenabled = 1 
							  AND pg.i_owner_id = %2\$s AND pg.i_owner_id = u.id  %3\$s
						   )
						 
		
						) derived_tbl
							"
                    , $this->db->dbprefix, intval($i_user_id), $s_where
            );
        } else if ($querytype == 'members') {


            $sql = sprintf("
						SELECT COUNT(*) count FROM (
						( SELECT
								pg.id
								FROM cg_prayer_group pg
								LEFT JOIN cg_prayer_group_members pg_mem ON pg_mem.i_prayer_group_id = pg.id
								LEFT JOIN cg_users u ON u.id = pg.i_owner_id
								WHERE u.i_status='1'
								AND u.i_isdeleted ='1'
								AND pg.i_isenabled = 1
								AND pg.i_owner_id = u.id
								AND
								(
								pg.id in
									(SELECT pg_mem.i_prayer_group_id from cg_prayer_group_members pg_mem, cg_users u where
									pg_mem.i_user_id = '".intval($i_user_id)."' AND pg_mem.s_status = 'accepted'
									)
								
								) {$s_where} GROUP BY pg.id 
						
						)
						) derived_tbl
							"
                    , $this->db->dbprefix, intval($i_user_id), $s_where
            );
        }

        $query = $this->db->query($sql);
        $result_arr = $query->result_array();
        return $result_arr[0]['count'];
    }

    ### get all members in a group by group id

    public function get_members_by_grpid($group_id) {

        $sql = "SELECT u.id as post_owner_user_id,
							   CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
					 		   u.s_profile_photo,
							   u.e_gender,
							   u.dt_created_on as member_since,
							   pg.*,
							   pg_mem.*,
							   (SELECT COUNT(*) FROM cg_prayer_group_post gp 
							   		WHERE gp.i_prayer_group_id = {$group_id} AND gp.i_user_id = u.id) as total_post
							   
						
						FROM cg_prayer_group_members pg_mem
						LEFT JOIN  cg_prayer_group pg  ON pg.id = pg_mem.i_prayer_group_id
						LEFT JOIN cg_users u ON u.id= pg_mem.i_user_id
						where pg.i_isenabled= 1 AND pg.id = {$group_id} AND pg_mem.s_status = 'accepted' ";
        #echo $sql; exit;

        $query = $this->db->query($sql);
        $result_arr = $query->result_array();
        $res_arr = check_friend_netpal_status($result_arr);
        return $res_arr;
    }

    ### get total member in a group by id

    public function get_total_members_grp_id($group_id) {

        $SQL = "SELECT COUNT(*) as count FROM cg_prayer_group_members
				WHERE  	i_prayer_group_id = {$group_id} AND s_status = 'accepted' ";

        $query = $this->db->query($SQL);
        $result_arr = $query->result_array();
        return $result_arr[0]['count'];
    }

    ## create prayer group

    public function insert($arr = array()) {
        if (count($arr) == 0) {
            return null;
        }
        $this->db->insert($this->db->PRAYER_GROUP, $arr); //echo $this->db->last_query();
        return $this->db->insert_id();
    }

    ## insert group posts

    public function insert_grp_post($arr = array()) {
        if (count($arr) == 0) {
            return null;
        }
        $this->db->insert($this->db->PRAYER_GROUP_POST, $arr); # echo $this->db->last_query();
        return $this->db->insert_id();
    }

    ## update group posts

    public function update_grp_post($arr = array(), $id) {
        if (count($arr) == 0) {
            return null;
        }
        $this->db->update($this->db->PRAYER_GROUP_POST, $arr, array('id' => $id));
    }

    ## get all posts by group_id 

    public function getAllPosts_grpID($group_id, $s_where, $i_start_limit = '', $i_no_of_page = '') {

        $sql = " SELECT  u.id i_user_id, 
								CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
								p_post.*,
								pg.i_owner_id
								FROM  cg_prayer_group pg 
								LEFT JOIN cg_prayer_group_post  p_post ON pg.id= p_post.i_prayer_group_id
								LEFT JOIN cg_users u ON p_post.i_user_id = u.id
								WHERE u.i_status='1' AND u.i_isdeleted ='1' 
								AND pg.i_isenabled = 1
								AND p_post.i_prayer_group_id = '".intval($group_id)."' 
								
								{$s_where}
								ORDER BY p_post.dt_created_on DESC 
							    limit ".intval($i_start_limit).",".intval($i_no_of_page)
				  ;

        $query = $this->db->query($sql); #echo "sql ==>". ($sql) ."<br />"; 
        $result_arr = $query->result_array();

        return $result_arr;
    }

    public function getTotalPosts_grpID($group_id, $s_where) {


        $sql = "
				SELECT COUNT(*) count FROM (
						 SELECT p_post.*
								FROM  cg_prayer_group pg 
								LEFT JOIN cg_prayer_group_post  p_post ON pg.id= p_post.i_prayer_group_id
								LEFT JOIN cg_users u ON p_post.i_user_id = u.id
								WHERE u.i_status='1' AND u.i_isdeleted ='1' 
								AND pg.i_isenabled = 1
								AND p_post.i_prayer_group_id = '".intval($group_id)."' 
								{$s_where}
								
				) derived_tbl
					";

        $query = $this->db->query($sql); #echo "sql ==>". nl2br($sql) ."<br />";  
        $result_arr = $query->result_array();

        return $result_arr[0]['count'];
    }

    # end Get all posts by group_id 	

    public function get_frnd_list($query_type, $i_start = null, $i_limit = null, $prayer_group_id) {

        if ("$i_start" == '') {
            $limit = '';
        } else {
            $start_limit = (int) $i_start;
            $no_of_page = (int) $i_limit;
            $limit = ' limit ' . $start_limit . ', ' . $no_of_page;
        }

        $SQL = "SELECT derived_tbl.* FROM ( {$query_type}) as  derived_tbl {$limit}";
        //echo nl2br($SQL); exit;
        $query = $this->db->query($SQL);
        $result_arr = $query->result_array();

        //global $CI;
        $this->load->model('contacts_model');
        if (count($result_arr)) {
            foreach ($result_arr as $key => $val) {

                ## check already in prayer group
                if ($prayer_group_id != '') {
                    $result_arr[$key]['member_group_status'] = $this->prayer_group_model->check_member_group_status($val['post_owner_user_id'], $prayer_group_id);
                }

                $result_arr[$key]['total_friend'] = $this->contacts_model->get_total_friends_by_user($val['post_owner_user_id']);
                //echo $this->db->last_query();
            }
        }
        $res_arr = check_friend_netpal_status($result_arr);


        //pr($res_arr,1);
        return $res_arr;
    }

    public function gettotal_frnd_list($query_type) {

        $SQL = "SELECT COUNT(*) count FROM ( 
									{$query_type}) as  derived_tbl {$limit}";

        $query = $this->db->query($SQL);
        $result_arr = $query->result_array();
        return $result_arr[0]['count'];
    }

    ### check if already in group 

    public function check_member_group_status($i_user_id, $i_group_id) {

        try {
            $result_arr = array();
            $s_qry = "SELECT 
									pg.s_status as 's_status'
								FROM {$this->db->PRAYER_GROUP_MEMBERS} pg, {$this->db->USERS} u
								WHERE 
									pg.i_user_id = '".$i_user_id."' and pg.i_prayer_group_id = '".$i_group_id."' 
									AND u.id= pg.i_user_id ";

            $query = $this->db->query($s_qry);
            $result_arr = $query->result_array();

            if ($result_arr[0]['s_status'] == '') {
                return 'not available';
            } else
                return $result_arr[0]['s_status'];
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    ### add invitation to prayer grop member table

    public function insert_group_member($arr = array()) {
        if (count($arr) == 0) {
            return null;
        }
        $this->db->insert($this->db->PRAYER_GROUP_MEMBERS, $arr); //echo $this->db->last_query();
        return $this->db->insert_id();
    }

    ### accept invitation 

    public function accept_invitation($where, $arr, $msg_arr) {
        $this->db->update($this->db->PRAYER_GROUP_MEMBERS, $arr, $where);
        //echo $this->db->last_query();
        $this->db->update('messages', array('i_ended' => '1'), $msg_arr);

        //exit;
    }

    ### decline invitation 

    public function decline_invitation($where, $msg_arr) {
        $this->db->delete($this->db->PRAYER_GROUP_MEMBERS, $where);
        $this->db->update('messages', array('i_ended' => '1'), $msg_arr);
    }

    # function to check if request_already_sent

    function request_already_sent($i_user_id = '', $i_group_id = '') {
        $SQL = "SELECT COUNT(*) AS `check_count` FROM ".$this->db->PRAYER_GROUP_MEMBERS." 
						WHERE `i_user_id`='".$i_user_id."'  AND `i_prayer_group_id` = '".$i_group_id."' 
						AND `s_status` = 'pending' ";
        $ROW = $this->db->query($SQL)->row_array(); //echo $this->db->last_query(); exit;

        if ($ROW['check_count'])
            return 1;
        else
            return 0;
    }

    ### INSERT PRAYER GROUP NOTIFICATIONS BY GRP ID

    public function insert_group_notifications($arr = array()) {
        if (count($arr) == 0) {
            return null;
        }
        $this->db->insert($this->db->PRAYER_GROUP_NOTIFICATIONS, $arr); //echo $this->db->last_query();
        return $this->db->insert_id();
    }

    public function get_my_groups_notificaions($i_user_id, $s_where, $i_start_limit = '', $i_no_of_page = '') {

        if ($i_start_limit == '' && $i_no_of_page == '') {
            $limit = '';
        } else {
            $limit = 'LIMIT ' . $i_start_limit . ' , ' . $i_no_of_page;
        }

        $sql = sprintf("
					(SELECT 
					  pg.s_group_name,
					  n.* 
				  
					 FROM  cg_prayer_group_notifications n
					 LEFT JOIN cg_prayer_group pg ON pg.id = n.i_prayer_group_id
					 LEFT JOIN cg_users u ON pg.i_owner_id = u.id
					 
					  WHERE u.i_status='1'
					  AND u.i_isdeleted ='1' 
					  AND pg.i_isenabled = 1 
					  AND pg.i_owner_id = u.id
					  AND
					  (
						   pg.id in
						  (SELECT pg_mem.i_prayer_group_id from cg_prayer_group_members pg_mem, cg_users u where
							pg_mem.i_user_id = %2\$s AND  pg_mem.s_status = 'accepted'
						  )
						  
					  )  AND n.s_type != 'joining_req' 
					  	 AND n.s_type != 'invited' 
					  %4\$s )
					  
					 UNION
					 
					 (SELECT 

					  pg.s_group_name,
					  n.*
					  
					  FROM  cg_prayer_group_notifications n
					  LEFT JOIN cg_prayer_group pg ON pg.id = n.i_prayer_group_id
					  LEFT JOIN cg_users u ON pg.i_owner_id = u.id
					  
					  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND pg.i_isenabled = 1 
					  AND pg.i_owner_id = %2\$s AND pg.i_owner_id = u.id AND n.s_type != 'invited'
					  
					  
					   %4\$s )
					   
					  UNION
					  
					 (SELECT 

					  pg.s_group_name,
					  n.*
					  
					  FROM  cg_prayer_group_notifications n
					  LEFT JOIN cg_prayer_group pg ON pg.id = n.i_prayer_group_id
					  LEFT JOIN cg_users u ON n.i_user_id = u.id
					  
					  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND pg.i_isenabled = 1 
					  AND n.i_user_id = %2\$s AND n.s_type = 'invited'
					  AND n.s_type != 'joining_req' 
					   %4\$s )

				    ORDER BY `dt_created_on` DESC
					%3\$s
					"
                , $this->db->dbprefix, intval($i_user_id), $limit, $s_where
        );

        $query = $this->db->query($sql); //echo "sql ==>". nl2br($sql) ."<br />"; 
        $result_arr = $query->result_array();
        //pr($result_arr,1);

        $this->load->model('data_messages_model');

        if (count($result_arr)) {

            foreach ($result_arr as $key => $val) {

                if (($val['i_refferd_msg_id'] != '' || $val['i_refferd_msg_id'] != 0 ) && ($val['s_type'] == 'joining_req' || $val['s_type'] == 'invited')) {
                    $result_arr[$key]['message_arr'] = $this->data_messages_model->get_message_by_id($val['i_refferd_msg_id']);
                }
            }
        }

        return $result_arr;
    }

    public function get_total_my_groups_notificaions($i_user_id, $s_where) {


        $sql = sprintf("
				SELECT COUNT(*) count FROM (
				(SELECT 
					  n.id
					  FROM  cg_prayer_group_notifications n
					  LEFT JOIN cg_prayer_group pg ON pg.id = n.i_prayer_group_id
					  LEFT JOIN cg_users u ON pg.i_owner_id = u.id
					  WHERE u.i_status='1' AND u.i_isdeleted ='1' 
					  AND pg.i_isenabled = 1 AND pg.i_owner_id = u.id
					  AND
					  (
					   pg.id in
					  (SELECT pg_mem.i_prayer_group_id from cg_prayer_group_members pg_mem, cg_users u where
					  	pg_mem.i_user_id = %2\$s AND  pg_mem.s_status = 'accepted'
					  )
					  
					  ) AND n.s_type != 'joining_req'  %3\$s)
			     UNION
				 
				 (SELECT 
					  n.id
					  FROM  cg_prayer_group_notifications n
					  LEFT JOIN cg_prayer_group pg ON pg.id = n.i_prayer_group_id
					  LEFT JOIN cg_users u ON pg.i_owner_id = u.id
					  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND pg.i_isenabled = 1 
					  AND pg.i_owner_id = %2\$s AND pg.i_owner_id = u.id AND n.s_type != 'invited'  %3\$s
				   )
				 UNION
					  
					 (SELECT 

					  n.id
					  FROM  cg_prayer_group_notifications n
					  LEFT JOIN cg_prayer_group pg ON pg.id = n.i_prayer_group_id
					  LEFT JOIN cg_users u ON n.i_user_id = u.id
					  
					  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND pg.i_isenabled = 1 
					  AND n.i_user_id = %2\$s AND n.s_type = 'invited'  AND n.s_type != 'joining_req' 
					   %3\$s )
				 

				) derived_tbl
					"
                , $this->db->dbprefix, intval($i_user_id), $s_where
        );


        $query = $this->db->query($sql);
        $result_arr = $query->result_array();
        return $result_arr[0]['count'];
    }

    public function delete_post_by_id($id) {
        $sql = 'DELETE FROM  cg_prayer_group_post WHERE id="'.$id.'"';
        $this->db->query($sql);
    }

    /// group list

    public function group_list($where = '', $i_start = null, $i_limit = null, $s_order_by = '1') {

        $limit = (is_numeric($i_start) && is_numeric($i_limit)) ? " Limit " . intval($i_start) . "," . intval($i_limit) : '';
        $sql = " SELECT  pg.id,
							pg.s_group_name,
							pg.i_owner_id as group_owner,
							pg.dt_created_on,
							CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
							d.s_name,
							u.id as post_owner_user_id,
							u.s_profile_photo,
							u.e_gender,
							(SELECT count(*) FROM cg_prayer_group_members pg_mem 
									LEFT JOIN cg_prayer_group pg1 ON pg1.id = pg_mem.i_prayer_group_id
									WHERE pg_mem.i_prayer_group_id = pg.id
							) as total_member
							FROM cg_prayer_group pg
							LEFT JOIN cg_denomination d ON pg.i_denomination_id = d.id
							LEFT JOIN cg_users u ON pg.i_owner_id = u.id
							WHERE pg.i_isenabled =1 
							{$where} 
							GROUP BY pg.id
							ORDER BY id DESC {$limit}";

        //echo nl2br($sql); exit;

        $query = $this->db->query($sql);
        $result_arr = $query->result_array(); //pr($result_arr,1);

        $logged_user_id = decrypt($this->session->userdata('user_id'));

        if (count($result_arr)) {
            foreach ($result_arr as $key => $val) {

                if ($val['post_owner_user_id'] == $logged_user_id) {
                    $result_arr[$key]['member_group_status'] = 'owner';
                } else
                    $result_arr[$key]['member_group_status'] = $this->prayer_group_model->check_member_group_status($logged_user_id, $val['id']);
            }
        }
        return $result_arr;
    }

    public function group_list_count($where = '') {


        $sql = "SELECT count(*) as i_total 
							FROM (
							SELECT
							pg.id
							FROM cg_prayer_group pg
							LEFT JOIN cg_denomination d ON pg.i_denomination_id = d.id
							LEFT JOIN cg_users u ON pg.i_owner_id = u.id
							WHERE pg.i_isenabled =1 
							{$where} 
							GROUP BY pg.id
							ORDER BY id DESC
								
							) as tbl ";
        $query = $this->db->query($sql);
        $result_arr = $query->result_array();
        return $result_arr[0]['i_total'];
    }

    ######## GET TOTAL JOINING AND INVITATION PENDING REQUEST ######

    public function get_total_pending_groups_requests($i_user_id, $s_where) {


        $sql = sprintf("
				SELECT COUNT(*) count FROM (
				
					 (SELECT 
						  n.id
						  FROM  cg_prayer_group_notifications n
						  LEFT JOIN cg_prayer_group pg ON pg.id = n.i_prayer_group_id
						  LEFT JOIN cg_users u ON pg.i_owner_id = u.id
						  LEFT JOIN cg_prayer_group_members pg_mem ON pg_mem.i_user_id  = n.i_requester_user_id
						   
						  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND pg.i_isenabled = 1 
						  AND pg.i_owner_id = %2\$s AND pg.i_owner_id = u.id AND n.s_type != 'invited' 
						  AND n.s_type = 'joining_req'
						  AND pg_mem.s_status = 'pending' 
					  	  AND pg_mem.i_prayer_group_id = pg.id 
						  %3\$s
					   )
					   
					 UNION
					  
					 (SELECT 

					  n.id
					  FROM  cg_prayer_group_notifications n
					  LEFT JOIN cg_prayer_group pg ON pg.id = n.i_prayer_group_id
					  LEFT JOIN cg_users u ON n.i_user_id = u.id
					  LEFT JOIN cg_prayer_group_members pg_mem ON pg_mem.i_user_id  = u.id
					  
					  
					  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND pg.i_isenabled = 1 
					  AND n.i_user_id = %2\$s AND n.s_type = 'invited'  AND n.s_type != 'joining_req' 
					  AND pg_mem.s_status = 'pending' 
					  AND pg_mem.i_prayer_group_id = pg.id
					   %3\$s )
				 

				) derived_tbl
					"
                , $this->db->dbprefix, intval($i_user_id), $s_where
        );

        //echo nl2br($sql); exit;
        $query = $this->db->query($sql);
        $result_arr = $query->result_array();
        return $result_arr[0]['count'];
    }

    public function checkGroupNameExists($grp_name, $user_id) {

        $SQL = "SELECT COUNT(*) as total 
					FROM cg_prayer_group 
						WHERE i_owner_id  = {$user_id} 
						AND s_group_name = '{$grp_name}'";
        $query = $this->db->query($SQL);
        $result_arr = $query->result_array();

        if ($result_arr[0]['total'] >= 1)
            return true;
        else
            return false;
    }

    public function checkGroupMaxLimit($max_limit, $user_id, $type) {

        if ($type == 'created') {

            $SQL = "SELECT COUNT(*) as total 
					FROM cg_prayer_group 
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
					FROM cg_prayer_group_members 
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

    public function delete_prayer_group($id) {
        $sql = 'DELETE FROM  cg_prayer_group WHERE id="'.$id.'"';
        $this->db->query($sql);

        $sql1 = 'DELETE FROM  cg_prayer_group_members WHERE i_prayer_group_id="'.$id.'"';
        $this->db->query($sql1);

        $sql2 = 'DELETE FROM  cg_prayer_group_notifications WHERE i_prayer_group_id="'.$id.'"';
        $this->db->query($sql2);

        $sql3 = 'DELETE FROM  cg_prayer_group_post WHERE i_prayer_group_id="'.$id.'"';
        $this->db->query($sql3);

        $sql4 = 'DELETE FROM  cg_prayer_grp_chat_room_invitation WHERE i_group_id="'.$id.'"';
        $this->db->query($sql4);
    }

    public function getTotalPrayerRoom($i_user_id) {

        $SQL = "SELECT count(*) as count FROM ".$this->db->CHAT_ROOM_INVITATION." WHERE i_user_id = '".$i_user_id."'  
		  								AND dt_end_time  <= '".get_db_datetime()."' ";

        $query = $this->db->query($SQL); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0]['count'];
    }

    ## get group list created/ joined by logged user 
    ## MY groups = groups JOINED + CREATED 	

    public function get_my_groups_names($i_user_id, $s_where) {

        $sql = sprintf("
					  (SELECT
						pg.*
						
						FROM cg_prayer_group pg
						LEFT JOIN cg_prayer_group_members pg_mem ON pg_mem.i_prayer_group_id = pg.id
						LEFT JOIN cg_users u ON u.id = pg.i_owner_id
						WHERE u.i_status='1'
						AND u.i_isdeleted ='1'
						AND pg.i_isenabled = 1
						AND pg.i_owner_id = u.id
						AND
						(
						pg.id in
							(SELECT pg_mem.i_prayer_group_id from cg_prayer_group_members pg_mem, cg_users u where
								pg_mem.i_user_id = %2\$s AND pg_mem.s_status = 'accepted'
							)
						
						) %3\$s   GROUP BY pg.id ORDER BY s_group_name ASC)
					 UNION
					 
					 (SELECT
					  pg.*
					  
					  FROM  cg_prayer_group pg , cg_users u
					  
					  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND pg.i_isenabled = 1 
					  AND pg.i_owner_id = %2\$s AND pg.i_owner_id = u.id
					  %3\$s ORDER BY s_group_name ASC)
					
					"
                , $this->db->dbprefix, intval($i_user_id), $s_where
        );



        $query = $this->db->query($sql); #echo "sql ==>". nl2br($sql) ."<br />"; exit;
        $result_arr = $query->result_array();

        if (count($result_arr)) {
            foreach ($result_arr as $key => $val) {
                $result_arr[$key]['total_member'] = $this->get_total_members_grp_id($val['id']);
            }
        }

        # pr($result_arr);
        return $result_arr;
    }

    public function get_members_ids_by_grpids($group_ids) {
        if ($group_ids != '') {
            $sql = "SELECT i_user_id
						FROM cg_prayer_group_members
						where i_prayer_group_id IN ({$group_ids}) AND s_status = 'accepted' ";
            #echo $sql; 

            $query = $this->db->query($sql);
            $result_arr = $query->result_array();
            foreach ($result_arr as $val) {
                $arr[] = $val;
            }
        }
        return $arr;
    }

    public function get_grpids_by_logged_in_user($user_id) {

        $sql = "SELECT *
						FROM cg_prayer_group
						where i_owner_id ='" . $user_id . "' AND i_isenabled=1 ";
        #echo $sql; 

        $query = $this->db->query($sql);
        $result_arr = $query->result_array();
        foreach ($result_arr as $val) {
            $arr[] = $val['id'];
        }
        #pr($arr);
        return $arr;
    }

    public function get_grpids_u_id($user_id) {

        $sql = "SELECT *
						FROM cg_prayer_group
						where i_owner_id ='" . $user_id . "' AND i_isenabled=1 ";
        #echo $sql; 

        $query = $this->db->query($sql);
        $result_arr = $query->result_array();
        foreach ($result_arr as $val) {
            $arr[] = $val['id'];
        }
        $sql1 = "SELECT * FROM cg_prayer_group_members members LEFT JOIN cg_prayer_group p_group on p_group.id=members.i_prayer_group_id  WHERE members.i_user_id='" . $user_id . "' AND members.s_status='accepted' AND p_group.i_isenabled=1 GROUP BY members.i_prayer_group_id";
        #echo $sql1;
        $query = $this->db->query($sql1);
        $result_arr1 = $query->result_array();
        foreach ($result_arr1 as $id) {
            $arr1[] = $id['i_prayer_group_id'];
        }
        $arr = array_merge($arr, $arr1);
        #pr($arr);
        return $arr;
    }

    public function get_members($group_id, $i_profile_id) {

        $sql = "SELECT u.id as post_owner_user_id,
							   CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
					 		   u.s_profile_photo,
							   u.e_gender,
							   u.dt_created_on as member_since,
							   pg.*,
							   pg_mem.*,
							   (SELECT COUNT(*) FROM cg_prayer_group_post gp 
							   		WHERE gp.i_prayer_group_id = {$group_id} AND gp.i_user_id = u.id) as total_post
							   
						
						FROM cg_prayer_group_members pg_mem
						LEFT JOIN  cg_prayer_group pg  ON pg.id = pg_mem.i_prayer_group_id
						LEFT JOIN cg_users u ON u.id= pg_mem.i_user_id
						where pg.i_isenabled= 1 AND pg.id = {$group_id} AND pg_mem.s_status = 'accepted' AND pg_mem.i_user_id!='" . $i_profile_id . "'";
        #echo $sql; exit;

        $query = $this->db->query($sql);
        $result_arr = $query->result_array();
        $res_arr = check_friend_netpal_status($result_arr);
        return $res_arr;
    }

    public function get_pending_groups_requests_recieved($i_user_id, $s_where) {


        $sql = sprintf(" 
		  				  SELECT 
						  pg_mem.id as rec_id,
						  pg_mem.i_user_id ,
						  pg_mem.s_status,
						  pg_mem.dt_created_on,
						  pg_mem.dt_joined_on,
						  pg.s_group_name,
						  pg.i_owner_id,
						  pg_mem.i_prayer_group_id,
						  CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
						   u.s_profile_photo,
						   u.e_gender
						  FROM  cg_prayer_group_members pg_mem
						  LEFT JOIN cg_prayer_group pg ON pg.id = pg_mem.i_prayer_group_id
						  LEFT JOIN cg_users u ON pg_mem.i_user_id = u.id
						  
						  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND pg.i_isenabled = 1 
						  AND pg_mem.i_user_id = %2\$s AND pg_mem.i_request = '0' 
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

    public function get_pending_groups_requests_sent($i_user_id, $s_where) {


        $sql = sprintf(" 
		  				  SELECT 
						  pg_mem.id as rec_id,
						  pg_mem.i_user_id ,
						  pg_mem.s_status,
						  pg_mem.dt_created_on,
						  pg_mem.dt_joined_on,
						  pg.s_group_name,
						  pg.i_owner_id,
						  pg_mem.i_prayer_group_id,
						  CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
						   u.s_profile_photo,
						   u.e_gender
						  FROM  cg_prayer_group_members pg_mem
						  LEFT JOIN cg_prayer_group pg ON pg.id = pg_mem.i_prayer_group_id
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

    public function get_my_groups_names_array($i_user_id, $s_where) {

        $sql = sprintf("
					 (SELECT
					  pg.*
					  FROM  cg_prayer_group pg , cg_users u
					  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND pg.i_isenabled = 1 
					  AND pg.i_owner_id = %2\$s AND pg.i_owner_id = u.id
					  %3\$s ORDER BY s_group_name ASC)
					"
                , $this->db->dbprefix, intval($i_user_id), $s_where
        );



        $query = $this->db->query($sql); #echo "sql ==>". nl2br($sql) ."<br />"; exit;
        $result_arr = $query->result_array();

        if (count($result_arr)) {
            foreach ($result_arr as $key => $val) {
                $result_arr[$key]['total_member'] = $this->get_total_members_grp_id($val['id']);
            }
        }

        # pr($result_arr);
        return $result_arr;
    }

    public function get_other_groups_names_arr($i_user_id, $s_where) {

        $sql = sprintf("
					 (SELECT
					  pg.*,u.s_profile_photo,u.e_gender
					  FROM  cg_prayer_group pg,cg_users u 
					  WHERE  pg.i_isenabled = 1 
					  AND pg.i_owner_id=u.id
					  AND pg.i_owner_id != %2\$s 
					  %3\$s ORDER BY pg.s_group_name ASC)
					"
                , $this->db->dbprefix, intval($i_user_id), $s_where
        );



        $query = $this->db->query($sql); #echo "sql ==>". nl2br($sql) ."<br />"; exit;
        $result_arr = $query->result_array();
        # pr($result_arr);
        return $result_arr;
    }

    public function leave_prayer_group($id, $uid) {


        $sql1 = 'DELETE FROM  cg_prayer_group_members WHERE i_prayer_group_id=' . $id . ' AND i_user_id=' . $uid;
        //echo $sql1;
        $this->db->query($sql1);

        /* $sql2 = sprintf( 'DELETE FROM  cg_prayer_group_notifications WHERE i_prayer_group_id=%s', $id );
          $this->db->query($sql2); */

        /* $sql3 = sprintf( 'DELETE FROM  cg_prayer_group_post WHERE i_prayer_group_id=%s', $id );
          $this->db->query($sql3); */

        $sql4 = 'DELETE FROM  cg_prayer_grp_chat_room_invitation WHERE i_group_id=' . $id . ' and i_user_id=' . $uid;
        $this->db->query($sql4);
    }

    public function get_list_by_user($s_where, $start_limit, $limit, $order_by) {
        $sql = "select * from cg_prayer_group_post " . $s_where . " order by " . $order_by . " limit " . $start_limit . "," . $limit;
        //echo $sql;
        $q = $this->db->query($sql);
        $res = $q->result_array();
        return $res;
    }

    public function get_list_count($s_where) {
        $sql = "select count(*) as count from cg_prayer_group_post " . $s_where;

        $q = $this->db->query($sql);
        $res = $q->result_array();
        return $res['0']['count'];
    }
    
    public function prayer_wall_all_comment($s_where,$i_start_limit='', $i_no_of_page=''){
            $sql="select c.*,p.s_subject,p.s_description,CONCAT(u.s_first_name,' ',u.s_last_name) AS s_profile_name from cg_prayer_wall_comments c, cg_bible_prayer_request p,cg_users u $s_where and c.i_prayer_id = p.id and u.id = c.i_user_id ORDER BY c.id DESC limit ".$i_start_limit.",".$i_no_of_page;
        // echo $sql;
            $query=$this->db->query($sql);
$result_arr=$query->result_array();
return $result_arr;
            
        }
         public function prayer_wall_all_comment_total($s_where){
            $sql="select count(*) as count from cg_prayer_wall_comments c,cg_users u $s_where and u.id = c.i_user_id ORDER BY c.id DESC";
            $query=$this->db->query($sql);
$result_arr=$query->result_array();
//pr($result_arr,1);
return $result_arr['0']['count'];
            
            
        }

}
