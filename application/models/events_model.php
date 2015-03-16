<?php

include_once(APPPATH . 'models/base_model.php');

class Events_model extends Base_model {

    public function __construct() {
        parent::__construct();
        $this->load->model('netpals_model');
        $this->load->model('users_model');
    }

    public function get() {
        $sql = 'SELECT * FROM ' . $this->db->EVENTS . ' order by id desc';
        $query = $this->db->query($sql);
        $result_arr = $query->result_array();
//pr($result_arr,1);
        return $result_arr;
    }

    public function get_by_id($id, $where = "", $start_limit = "", $no_of_page = "") {

        $where_cond = ($where != '') ? " AND {$where} " : " ";
        if ("$start_limit" == "") {
            $sql = "SELECT E.*, 
								   C.s_country_name, 
								   U.s_name, 
								   U.s_last_name ,
                                                                   CONCAT(u.s_first_name,' ',u.s_last_name) AS s_profile_name
								   
								   
							FROM {$this->db->EVENTS} E 
							LEFT JOIN  {$this->db->ADMIN_USER} U 
							ON U.id = E.i_host_id 
							LEFT JOIN  {$this->db->MST_COUNTRY} C ON E.i_country_id = C.id  LEFT JOIN cg_users u ON u.id = E.i_host_id
							where E.id = '".$id."'  {$where_cond} ";
        } else {
            $sql = "SELECT E.*, 
							C.s_country_name,
							U.s_name, 
							U.s_last_name 
							
							FROM {$this->db->EVENTS}
							E LEFT JOIN  {$this->db->ADMIN_USER} U ON U.id = E.i_host_id 
						    LEFT JOIN  {$this->db->MST_COUNTRY} C ON E.i_country_id = C.id 
							where E.id = '".$id."'   {$where_cond}   limit ".$start_limit.", ".$no_of_page."";
        }

        $query = $this->db->query($sql); //echo $this->db->last_query(); //exit;
        $result_arr = $query->result_array();

        return $result_arr[0];
    }

    public function insert($arr = array()) {
        if (count($arr) == 0) {
            return null;
        }
        $this->db->insert($this->db->EVENTS, $arr); #echo $this->db->last_query();
        return $this->db->insert_id();
    }

    public function update($arr = array(), $id) {
        if (count($arr) == 0) {
            return null;
        }
        $this->db->update($this->db->EVENTS, $arr, array('id' => $id));
        # echo $this->db->last_query();
    }

    public function delete_by_id($id) {

        ## deleting evens comments : cg_event_comments
        //$sql = sprintf('DELETE FROM cg_event_comments WHERE i_event_id =%s', $id);
		$sql = "DELETE FROM cg_event_comments WHERE i_event_id ='".$id."'";
        $this->db->query($sql);

        ## deleting events feedback : cg_event_feedback 

        //$sql = sprintf('DELETE FROM cg_event_feedback WHERE i_event_id =%s', $id);
		$sql = "DELETE FROM cg_event_feedback WHERE i_event_id ='".$id."'";
        $this->db->query($sql);

        ## deleting events rsvp : cg_event_rsvp

        //$sql = sprintf('DELETE FROM cg_event_rsvp WHERE i_event_id =%s', $id);
		$sql = "DELETE FROM cg_event_rsvp WHERE i_event_id ='".$id."'";
        $this->db->query($sql);

        ## deleting user email and user id : cg_event_email_invited cg_event_user_invited

        //$sql = sprintf('DELETE FROM cg_event_email_invited WHERE i_event_id =%s', $id);
		$sql = "DELETE FROM cg_event_email_invited WHERE i_event_id ='".$id."'";
        $this->db->query($sql);

        //$sql = sprintf('DELETE FROM cg_event_user_invited WHERE i_event_id =%s', $id);
		$sql = "DELETE FROM cg_event_user_invited WHERE i_event_id ='".$id."'";
        $this->db->query($sql);

        ## deleting event
        //$sql = sprintf('DELETE FROM ' . $this->db->EVENTS . ' WHERE id=%s', $id);
		$sql = "DELETE FROM ". $this->db->EVENTS ." WHERE id ='".$id."'";
        $this->db->query($sql);

        #DELETE PRIVACY
        $this->db->query("DELETE FROM {$this->db->event_privacy} WHERE i_event_id='" . $id . "'");
        #DELETE PRIVACY	
    }

    public function get_list($where = '', $i_start = null, $i_limit = null, $s_order_by = '') {

        $limit = (is_numeric($i_start) && is_numeric($i_limit)) ? " Limit " . intval($i_start) . "," . intval($i_limit) : '';
        $s_order_by = ($s_order_by != '') ? 'ORDER BY ' . $s_order_by : 'ORDER BY id DESC';

        $sql = " SELECT E.*, 
							U.s_name,
                                                        CONCAT(u.s_first_name,' ',u.s_last_name) AS s_profile_name,
							U.s_last_name, 
							C.s_country_name 
							FROM {$this->db->EVENTS} 
							E LEFT JOIN  {$this->db->ADMIN_USER} U ON U.id = E.i_host_id 
							LEFT JOIN  {$this->db->MST_COUNTRY} C ON E.i_country_id = C.id  LEFT JOIN  cg_users u  ON u.id = E.i_host_id   {$where}  {$s_order_by} {$limit}";

        $query = $this->db->query($sql); //echo $this->db->last_query(); //exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);


        return $result_arr;
    }

    public function get_list_count($where = '') {


        $sql = "SELECT count(*) as i_total FROM {$this->db->EVENTS} E LEFT JOIN  
				  {$this->db->ADMIN_USER} U ON U.id = E.i_host_id  
				  LEFT JOIN  {$this->db->MST_COUNTRY} C ON E.i_country_id = C.id  {$where} ";
        $query = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0]['i_total'];
    }

    public function change_status($status, $id) {

        if ($status != '' && $id != '') {
            $sql = "UPDATE {$this->db->EVENTS} SET `i_status` = '".$status."'
						   WHERE `id` ='".$id."'";
            $this->db->query($sql); // echo $this->db->last_query();exit;
            return true;
        }
    }

## MY EVENTS = EVENTS JOINED + CREATED 	

    /* 	public function get_my_events($i_user_id, $s_where, $i_start_limit='', $i_no_of_page='') {

      //$curr_date = date('Y-m-d');

      if("$i_start_limit" == "") {
      $sql = sprintf("
      (SELECT
      u.id i_user_id,
      u.s_email,
      u.e_gender,
      CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
      u.s_profile_photo,

      e.id,
      e.i_host_id as event_owner_id,
      e.i_user_type,
      e.dt_start_time,
      e.dt_end_time,
      e.dt_created_on,
      e.s_title,
      e.s_address,
      e.s_postcode,
      e.s_desc,
      e.i_country_id,
      e.s_city,
      e.s_state,
      e.s_image_1,
      e.s_image_2,
      e.s_image_3,
      e.s_image_4,
      e.s_image_5,
      e.s_timezone,
      (SELECT count(*) FROM cg_event_comments c WHERE c.i_event_id = e.id ) AS total_comments,
      'followed' as s_type,
      ' [ E ] ' as event_type

      FROM cg_users u, cg_events e

      WHERE u.i_status='1' AND u.i_isdeleted ='1' AND e.i_status = 1 AND e.i_host_id = u.id



      AND
      (
      e.id in
      (SELECT r.i_event_id from cg_event_rsvp r, cg_users u where
      r.i_user_id = %2\$s
      )

      ) %3\$s )

      UNION

      (SELECT

      u.id i_user_id,
      u.s_email,
      u.e_gender,
      CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
      u.s_profile_photo,

      e.id,
      e.i_host_id as event_owner_id,
      e.i_user_type,
      e.dt_start_time,
      e.dt_end_time,
      e.dt_created_on,
      e.s_title,
      e.s_address,
      e.s_postcode,
      e.s_desc,
      e.i_country_id,
      e.s_city,
      e.s_state,
      e.s_image_1,
      e.s_image_2,
      e.s_image_3,
      e.s_image_4,
      e.s_image_5,
      e.s_timezone,
      (SELECT count(*) FROM cg_event_comments c WHERE c.i_event_id = e.id ) AS total_comments,
      'hosted' as s_type,
      ' [ E ] ' as event_type

      FROM cg_users u, cg_events e

      WHERE u.i_status='1' AND u.i_isdeleted ='1' AND e.i_status = 1 AND

      e.i_host_id = u.id AND e.i_host_id = %2\$s
      %3\$s )

      ORDER BY `dt_start_time` ASC
      "
      , $this->db->dbprefix, intval($i_user_id), $s_where
      );
      }
      else {



      $sql = sprintf("
      (SELECT

      u.id i_user_id,
      u.s_email,
      u.e_gender,
      CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
      u.s_profile_photo,

      e.id,
      e.i_host_id as event_owner_id,
      e.i_user_type,
      e.dt_start_time,
      e.dt_end_time,
      e.dt_created_on,
      e.s_title,
      e.s_address,
      e.s_postcode,
      e.s_desc,
      e.i_country_id,
      e.s_city,
      e.s_state,
      e.s_image_1,
      e.s_image_2,
      e.s_image_3,
      e.s_image_4,
      e.s_image_5,
      e.s_timezone,

      (SELECT count(*) FROM cg_event_comments c WHERE c.i_event_id = e.id ) AS total_comments,
      'followed' as s_type,
      ' [ E ] ' as event_type

      FROM cg_users u, cg_events e

      WHERE u.i_status='1' AND u.i_isdeleted ='1' AND e.i_status = 1 AND e.i_host_id = u.id

      AND
      (
      e.id in
      (SELECT r.i_event_id from cg_event_rsvp r, cg_users u where
      r.i_user_id = %2\$s
      )

      ) %5\$s )

      UNION

      (SELECT

      u.id i_user_id,
      u.s_email,
      u.e_gender,
      CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
      u.s_profile_photo,

      e.id,
      e.i_host_id as event_owner_id,
      e.i_user_type,
      e.dt_start_time,
      e.dt_end_time,
      e.dt_created_on,
      e.s_title,
      e.s_address,
      e.s_postcode,
      e.s_desc,
      e.i_country_id,
      e.s_city,
      e.s_state,
      e.s_image_1,
      e.s_image_2,
      e.s_image_3,
      e.s_image_4,
      e.s_image_5,
      e.s_timezone,
      (SELECT count(*) FROM cg_event_comments c WHERE c.i_event_id = e.id ) AS total_comments,
      'hosted' as s_type,
      ' [ E ] ' as event_type

      FROM cg_users u, cg_events e

      WHERE u.i_status='1' AND u.i_isdeleted ='1'

      AND e.i_status = 1 AND e.i_host_id = %2\$s AND e.i_host_id = u.id
      %5\$s )

      ORDER BY `dt_start_time` ASC
      limit %3\$s, %4\$s
      "
      , $this->db->dbprefix, intval($i_user_id), intval($i_start_limit), intval($i_no_of_page),  $s_where
      );
      }

      $query = $this->db->query($sql); //echo "sql ==>". nl2br($sql) ."<br />";
      $result_arr = $query->result_array();

      if(is_array($result_arr) && count($result_arr)){
      foreach($result_arr as $key=>$val){

      $get_friend_status_me_him = $this->users_model->get_friend_status_me_him(intval(decrypt($this->session->userdata('user_id'))) , $result_arr[$key]['event_owner_id']);

      $if_friend = $this->users_model->if_already_friend(intval(decrypt($this->session->userdata('user_id'))) , $result_arr[$key]['event_owner_id']);


      if(count($get_friend_status_me_him) > 0  ) {
      $result_arr[$key]['display_becomefriend']     ='false';
      }

      if(count($if_friend)>0)
      {
      $result_arr[$key]['if_already_friend']     ='true';
      }
      else
      $result_arr[$key]['if_already_friend']     ='false';

      $arr_already_netpal=$this->netpals_model->if_already_netpal( intval(decrypt($this->session->userdata('user_id'))) , $result_arr[$key]['event_owner_id']);
      if(count($arr_already_netpal)>0 || (intval(decrypt($this->session->userdata('user_id'))) == $result_arr[$key]['event_owner_id']))
      $result_arr[$key]['already_added_netpal']='true';
      else
      $result_arr[$key]['already_added_netpal']='false';

      #unset($get_friend_status_me_him);
      }
      }

      # pr($result_arr);
      return $result_arr;


      } */

    //withot timezone

    public function get_my_events($i_user_id, $s_where, $i_start_limit = '', $i_no_of_page = '') {

        //$curr_date = date('Y-m-d');

        if ("$i_start_limit" == "") {
            $sql = sprintf("
				  (SELECT 
					u.id i_user_id,
					u.s_email,
					u.e_gender,
					CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
					u.s_profile_photo,
					
					e.id,
					e.i_host_id as event_owner_id,
					e.i_user_type,
					e.dt_start_time,
					e.dt_end_time,
					e.dt_created_on,
					e.s_title,
					e.s_address,
					e.s_postcode,
					e.s_desc,
					e.i_country_id,
					e.s_city,
					e.s_state,
					e.s_image_1,
					e.s_image_2,
					e.s_image_3,
					e.s_image_4,
					e.s_image_5,
					
					(SELECT count(*) FROM cg_event_comments c WHERE c.i_event_id = e.id ) AS total_comments,
					  'followed' as s_type,
					  ' [ E ] ' as event_type
					
					FROM cg_users u, cg_events e
					
					WHERE u.i_status='1' AND u.i_isdeleted ='1' AND e.i_status = 1 AND e.i_host_id = u.id  AND e.dt_end_time >  NOW()
					
					
					
					AND
					(
					e.id in
					(SELECT r.i_event_id from cg_event_rsvp r, cg_users u where
					  r.i_user_id = %2\$s
					)
					
					) %3\$s )
					
					 UNION
					 
					 (SELECT 

					  u.id i_user_id,
					  u.s_email,
					  u.e_gender,
					  CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
					  u.s_profile_photo,
					  
					  e.id,
					  e.i_host_id as event_owner_id,
					  e.i_user_type,
					  e.dt_start_time,
					  e.dt_end_time,
					  e.dt_created_on,
					  e.s_title,
					  e.s_address,
					  e.s_postcode,
					  e.s_desc,
					  e.i_country_id,
					  e.s_city,
					  e.s_state,
					  e.s_image_1,
					  e.s_image_2,
					  e.s_image_3,
					  e.s_image_4,
					  e.s_image_5,
					  
					  (SELECT count(*) FROM cg_event_comments c WHERE c.i_event_id = e.id ) AS total_comments,
					  'hosted' as s_type,
					  ' [ E ] ' as event_type
					  
					  FROM cg_users u, cg_events e
					  
					  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND e.i_status = 1 AND  e.dt_end_time >  NOW() AND
					 
					   e.i_host_id = u.id AND e.i_host_id = %2\$s
					   %3\$s  )

				ORDER BY `dt_created_on` DESC
					"
                    , $this->db->dbprefix, intval($i_user_id), $s_where
            );
        } else {

			//echo 'ELSLESLELS';

            $sql = sprintf("
					(SELECT 

					  u.id i_user_id,
					  u.s_email,
					  u.e_gender,
					  CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
					  u.s_profile_photo,
					  
					  e.id,
					  e.i_host_id as event_owner_id,
					  e.i_user_type,
					  e.dt_start_time,
					  e.dt_end_time,
					  e.dt_created_on,
					  e.s_title,
					  e.s_address,
					  e.s_postcode,
					  e.s_desc,
					  e.i_country_id,
					  e.s_city,
					  e.s_state,
					  e.s_image_1,
					  e.s_image_2,
					  e.s_image_3,
					  e.s_image_4,
					  e.s_image_5,
				
					  
					  (SELECT count(*) FROM cg_event_comments c WHERE c.i_event_id = e.id ) AS total_comments,
					  'followed' as s_type,
					  ' [ E ] ' as event_type
					  
					  FROM cg_users u, cg_events e
					  
					  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND e.i_status = 1 AND e.i_host_id = u.id  AND e.dt_end_time >  NOW()
					 
					  AND
					  (
					  e.id in
					  (SELECT r.i_event_id from cg_event_rsvp r, cg_users u where
					  r.i_user_id = %2\$s
					  )
					  
					  ) %5\$s )
					  
					 UNION
					 
					 (SELECT 

					  u.id i_user_id,
					  u.s_email,
					  u.e_gender,
					  CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
					  u.s_profile_photo,
					  
					  e.id,
					  e.i_host_id as event_owner_id,
					  e.i_user_type,
					  e.dt_start_time,
					  e.dt_end_time,
					  e.dt_created_on,
					  e.s_title,
					  e.s_address,
					  e.s_postcode,
					  e.s_desc,
					  e.i_country_id,
					  e.s_city,
					  e.s_state,
					  e.s_image_1,
					  e.s_image_2,
					  e.s_image_3,
					  e.s_image_4,
					  e.s_image_5,
				
					  (SELECT count(*) FROM cg_event_comments c WHERE c.i_event_id = e.id ) AS total_comments,
					  'hosted' as s_type,
					  ' [ E ] ' as event_type
					  
					  FROM cg_users u, cg_events e
					  
					  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND e.dt_end_time >  NOW()
					 
					  AND e.i_status = 1 AND e.i_host_id = %2\$s AND e.i_host_id = u.id
					   %5\$s  )

				    ORDER BY `dt_created_on` DESC
					limit %3\$s, %4\$s
					"
                    , $this->db->dbprefix, intval($i_user_id), intval($i_start_limit), intval($i_no_of_page), $s_where
            );
        }

        $query = $this->db->query($sql); //echo "sql ==>". nl2br($sql) ."<br />"; 
        $result_arr = $query->result_array();

        if (is_array($result_arr) && count($result_arr)) {
            foreach ($result_arr as $key => $val) {

                $get_friend_status_me_him = $this->users_model->get_friend_status_me_him(intval(decrypt($this->session->userdata('user_id'))), $result_arr[$key]['event_owner_id']);

                $if_friend = $this->users_model->if_already_friend(intval(decrypt($this->session->userdata('user_id'))), $result_arr[$key]['event_owner_id']);


                if (count($get_friend_status_me_him) > 0) {
                    $result_arr[$key]['display_becomefriend'] = 'false';
                }

                if (count($if_friend) > 0) {
                    $result_arr[$key]['if_already_friend'] = 'true';
                } else
                    $result_arr[$key]['if_already_friend'] = 'false';

                $arr_already_netpal = $this->netpals_model->if_already_netpal(intval(decrypt($this->session->userdata('user_id'))), $result_arr[$key]['event_owner_id']);
                if (count($arr_already_netpal) > 0 || (intval(decrypt($this->session->userdata('user_id'))) == $result_arr[$key]['event_owner_id']))
                    $result_arr[$key]['already_added_netpal'] = 'true';
                else
                    $result_arr[$key]['already_added_netpal'] = 'false';

                #unset($get_friend_status_me_him);
            }
        }

        # pr($result_arr);
        return $result_arr;
    }

    public function get_total_my_events($i_user_id, $s_where) {


        $sql = "
				SELECT COUNT(*) count FROM (
				(SELECT 
					  e.id
					  FROM cg_users u, cg_events e
					  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND e.i_status = 1 AND e.i_host_id = u.id
					  AND
					  (
					  e.id in
					  (SELECT r.i_event_id from cg_event_rsvp r, cg_users u where
					  r.i_user_id = '".intval($i_user_id)."' 
					  )
					  
					  ) {$s_where})
			     UNION
				 
				 (SELECT 
					  e.id
					  FROM cg_users u, cg_events e
					  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND e.i_status = 1
					   AND e.i_host_id = u.id 
					  AND e.i_host_id = '".intval($i_user_id)."' {$s_where}
				   )
				 

				) derived_tbl
					";

#and t.i_user_id != '%2\$s'
        $query = $this->db->query($sql);
        $result_arr = $query->result_array();
#echo $result_arr[0]['count']." === sql ==>". nl2br($sql) ."<br />";   exit;
        return $result_arr[0]['count'];
    }

### LISTING ALL EVENTS ADMIN AS WELL AS FO USER

    public function get_all_events($s_where, $i_start_limit = '', $i_no_of_page = '') {

        if ("$i_start_limit" == "") {
            $sql = sprintf("
				  (SELECT 
					a.id i_user_id,
					a.s_email,
					
					s_name AS s_profile_name,
					1 as s_profile_photo,
					
					e.id,
					e.i_host_id as event_owner_id,
					e.i_user_type,
					e.dt_start_time,
					e.dt_end_time,
					e.dt_created_on,
					e.s_title,
					e.s_address,
					e.s_postcode,
					e.s_desc,
					e.i_country_id,
					e.s_city,
					e.s_state,
					e.s_image_1,
					e.s_image_2,
					e.s_image_3,
					e.s_image_4,
					e.s_image_5,
					
					(SELECT count(*) FROM cg_event_comments c WHERE c.i_event_id = e.id ) AS total_comments
					
					FROM cg_admin_user a, cg_events e
					
					WHERE a.i_status='1' AND a.e_disabled ='no' AND e.i_status = 1 AND e.i_host_id = a.id AND e.dt_end_time >  NOW()
					AND e.i_user_type = 2
					 {$s_where} )
					
					 UNION
					 
					 (SELECT 

					  u.id i_user_id,
					  u.s_email,
					  
					  CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
					  u.s_profile_photo as s_profile_photo,
					  
					  e.id,
					  e.i_host_id as event_owner_id,
					  e.i_user_type,
					  e.dt_start_time,
					  e.dt_end_time,
					  e.dt_created_on,
					  e.s_title,
					  e.s_address,
					  e.s_postcode,
					  e.s_desc,
					  e.i_country_id,
					  e.s_city,
					  e.s_state,
					  e.s_image_1,
					  e.s_image_2,
					  e.s_image_3,
					  e.s_image_4,
					  e.s_image_5,
					  
					  (SELECT count(*) FROM cg_event_comments c WHERE c.i_event_id = e.id ) AS total_comments
					  
					  FROM cg_users u, cg_events e
					  
					  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND e.i_status = 1 AND e.i_host_id = u.id AND e.dt_end_time >  NOW() AND e.i_user_type = 1
					   {$s_where} )

				ORDER BY  dt_created_on DESC
					"
                    , $this->db->dbprefix, $s_where
            );
        } else {



            $sql = sprintf("
					(SELECT 
					a.id i_user_id,
					a.s_email,
					
					s_name AS s_profile_name,
					1 as s_profile_photo,
					
					e.id,
					e.i_host_id as event_owner_id,
					e.i_user_type,
					e.dt_start_time,
					e.dt_end_time,
					e.dt_created_on,
					e.s_title,
					e.s_address,
					e.s_postcode,
					e.s_desc,
					e.i_country_id,
					e.s_city,
					e.s_state,
					e.s_image_1,
					e.s_image_2,
					e.s_image_3,
					e.s_image_4,
					e.s_image_5,
				
					(SELECT count(*) FROM cg_event_comments c WHERE c.i_event_id = e.id ) AS total_comments
					
					FROM cg_admin_user a, cg_events e
					
					WHERE a.i_status='1' AND a.e_disabled ='no' AND e.i_status = 1 AND e.i_host_id = a.id  AND e.dt_end_time >  NOW() AND e.i_user_type = 2
					 %4\$s )
					  
					 UNION
					 
					 (SELECT 

					  u.id i_user_id,
					  u.s_email,
					  
					  CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
					  u.s_profile_photo as s_profile_photo,
					  
					  e.id,
					  e.i_host_id as event_owner_id,
					  e.i_user_type,
					  e.dt_start_time,
					  e.dt_end_time,
					  e.dt_created_on,
					  e.s_title,
					  e.s_address,
					  e.s_postcode,
					  e.s_desc,
					  e.i_country_id,
					  e.s_city,
					  e.s_state,
					  e.s_image_1,
					  e.s_image_2,
					  e.s_image_3,
					  e.s_image_4,
					  e.s_image_5,
					  
					  (SELECT count(*) FROM cg_event_comments c WHERE c.i_event_id = e.id ) AS total_comments
					  
					  FROM cg_users u, cg_events e
					  
					  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND e.i_status = 1 AND e.i_host_id = u.id AND e.dt_end_time >  NOW() AND  e.i_user_type = 1
					   %4\$s )

				    ORDER BY  dt_created_on DESC 
					limit %2\$s, %3\$s
					"
                    , $this->db->dbprefix, intval($i_start_limit), intval($i_no_of_page), $s_where
            );
        }

        $query = $this->db->query($sql); echo "sql ==>". nl2br($sql) ."<br />";  exit;
        $result_arr = $query->result_array();

        # pr($result_arr);
        return $result_arr;
    }

    #without timezone
    /* 	public function get_all_events($s_where, $i_start_limit='', $i_no_of_page='') {

      if("$i_start_limit" == "") {
      $sql = sprintf("
      (SELECT
      a.id i_user_id,
      a.s_email,

      s_name AS s_profile_name,
      1 as s_profile_photo,

      e.id,
      e.i_host_id as event_owner_id,
      e.i_user_type,
      e.dt_start_time,
      e.dt_end_time,
      e.dt_created_on,
      e.s_title,
      e.s_address,
      e.s_postcode,
      e.s_desc,
      e.i_country_id,
      e.s_city,
      e.s_state,
      e.s_image_1,
      e.s_image_2,
      e.s_image_3,
      e.s_image_4,
      e.s_image_5,
      e.e_privacy,
      (SELECT count(*) FROM cg_event_comments c WHERE c.i_event_id = e.id ) AS total_comments

      FROM cg_admin_user a, cg_events e

      WHERE a.i_status='1' AND a.e_disabled ='no' AND e.i_status = 1 AND e.i_host_id = a.id
      AND e.i_user_type = 2  AND e.dt_end_time >  NOW()
      %2\$s )

      UNION

      (SELECT

      u.id i_user_id,
      u.s_email,

      CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
      u.s_profile_photo as s_profile_photo,

      e.id,
      e.i_host_id as event_owner_id,
      e.i_user_type,
      e.dt_start_time,
      e.dt_end_time,
      e.dt_created_on,
      e.s_title,
      e.s_address,
      e.s_postcode,
      e.s_desc,
      e.i_country_id,
      e.s_city,
      e.s_state,
      e.s_image_1,
      e.s_image_2,
      e.s_image_3,
      e.s_image_4,
      e.s_image_5,
      e.e_privacy,
      (SELECT count(*) FROM cg_event_comments c WHERE c.i_event_id = e.id ) AS total_comments

      FROM cg_users u, cg_events e

      WHERE u.i_status='1' AND u.i_isdeleted ='1' AND e.i_status = 1 AND e.i_host_id = u.id AND e.i_user_type = 1  AND e.dt_end_time >  NOW()
      %2\$s )

      ORDER BY `dt_start_time` ASC
      "
      , $this->db->dbprefix, $s_where
      );
      }
      else {



      $sql = sprintf("
      (SELECT
      a.id i_user_id,
      a.s_email,

      s_name AS s_profile_name,
      1 as s_profile_photo,

      e.id,
      e.i_host_id as event_owner_id,
      e.i_user_type,
      e.dt_start_time,
      e.dt_end_time,
      e.dt_created_on,
      e.s_title,
      e.s_address,
      e.s_postcode,
      e.s_desc,
      e.i_country_id,
      e.s_city,
      e.s_state,
      e.s_image_1,
      e.s_image_2,
      e.s_image_3,
      e.s_image_4,
      e.s_image_5,
      e.e_privacy,
      (SELECT count(*) FROM cg_event_comments c WHERE c.i_event_id = e.id ) AS total_comments

      FROM cg_admin_user a, cg_events e

      WHERE a.i_status='1' AND a.e_disabled ='no' AND e.i_status = 1 AND e.i_host_id = a.id AND e.i_user_type = 2  AND e.dt_end_time >  NOW()
      %4\$s )

      UNION

      (SELECT

      u.id i_user_id,
      u.s_email,

      CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
      u.s_profile_photo as s_profile_photo,

      e.id,
      e.i_host_id as event_owner_id,
      e.i_user_type,
      e.dt_start_time,
      e.dt_end_time,
      e.dt_created_on,
      e.s_title,
      e.s_address,
      e.s_postcode,
      e.s_desc,
      e.i_country_id,
      e.s_city,
      e.s_state,
      e.s_image_1,
      e.s_image_2,
      e.s_image_3,
      e.s_image_4,
      e.s_image_5,
      e.e_privacy,

      (SELECT count(*) FROM cg_event_comments c WHERE c.i_event_id = e.id ) AS total_comments

      FROM cg_users u, cg_events e

      WHERE u.i_status='1' AND u.i_isdeleted ='1' AND e.i_status = 1 AND e.i_host_id = u.id AND e.i_user_type = 1  AND e.dt_end_time >  NOW()
      %4\$s )

      ORDER BY `dt_start_time` ASC
      limit %2\$s, %3\$s
      "
      , $this->db->dbprefix, intval($i_start_limit), intval($i_no_of_page),  $s_where
      );
      }

      $query = $this->db->query($sql); //echo "sql ==>". nl2br($sql) ."<br />";  exit;
      $result_arr = $query->result_array();

      // pr($result_arr,1);
      $result=array();
      $userid=intval(decrypt($this->session->userdata('user_id')));
      //echo $userid;exit;
      foreach($result_arr as $val)
      {
      if($val['event_owner_id'] != $userid)
      {
      $ev_p=get_privacy_setting_by_user_id($val['event_owner_id'],'event');
      if(($ev_p['i_friend_privacy'] !=0) || ($ev_p['i_netpal_privacy'] !=0) || ($ev_p['i_prayer_partner_privacy'] !=0 || $ev_p['i_ring_privacy'] !=0 || $ev_p['i_prayer_group_privacy'] !=0) )
      {
      $pr=check_event_privacy($val[id],$userid);
      if($pr!=0)
      {
      $result[]=$val;
      }
      }
      else
      {
      $result[]=$val;
      }
      }
      else
      {
      $result[]=$val;
      }

      }
      #pr($result,1);
      return $result;


      } */

    public function get_total_all_events($s_where) {


        $sql = "SELECT COUNT(*) count FROM (
				(SELECT 
					a.id
					FROM cg_admin_user a, cg_events e
					
					WHERE a.i_status='1' AND a.e_disabled ='no' AND e.i_status = 1 AND e.i_host_id = a.id AND e.i_user_type = 2 AND e.dt_end_time >  NOW()
					 ".mysql_real_escape_string($s_where)." )
			     UNION
				 
				 (SELECT 
					  e.id
					  FROM cg_users u, cg_events e
					  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND e.i_status = 1 AND e.i_host_id = u.id AND e.i_user_type = 1 AND e.dt_end_time >  NOW()
					  ".mysql_real_escape_string($s_where)."
				   )
				 

				) derived_tbl";

#and t.i_user_id != '%2\$s'
        $query = $this->db->query($sql); #echo $result_arr[0]['count']." === sql ==>". nl2br($sql) ."<br />";   exit;
        $this->db->last_query();
        $result_arr = $query->result_array();

        return $result_arr[0]['count'];
    }

    /* 	
      public function get_total_all_events($s_where) {


      $sql = sprintf("
      (SELECT
      a.id i_user_id,
      a.s_email,

      s_name AS s_profile_name,
      1 as s_profile_photo,

      e.id,
      e.i_host_id as event_owner_id,
      e.i_user_type,
      e.dt_start_time,
      e.dt_end_time,
      e.dt_created_on,
      e.s_title,
      e.s_address,
      e.s_postcode,
      e.s_desc,
      e.i_country_id,
      e.s_city,
      e.s_state,
      e.s_image_1,
      e.s_image_2,
      e.s_image_3,
      e.s_image_4,
      e.s_image_5,
      e.e_privacy,
      (SELECT count(*) FROM cg_event_comments c WHERE c.i_event_id = e.id ) AS total_comments

      FROM cg_admin_user a, cg_events e

      WHERE a.i_status='1' AND a.e_disabled ='no' AND e.i_status = 1 AND e.i_host_id = a.id
      AND e.i_user_type = 2
      %2\$s )

      UNION

      (SELECT

      u.id i_user_id,
      u.s_email,

      CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
      u.s_profile_photo as s_profile_photo,

      e.id,
      e.i_host_id as event_owner_id,
      e.i_user_type,
      e.dt_start_time,
      e.dt_end_time,
      e.dt_created_on,
      e.s_title,
      e.s_address,
      e.s_postcode,
      e.s_desc,
      e.i_country_id,
      e.s_city,
      e.s_state,
      e.s_image_1,
      e.s_image_2,
      e.s_image_3,
      e.s_image_4,
      e.s_image_5,
      e.e_privacy,
      (SELECT count(*) FROM cg_event_comments c WHERE c.i_event_id = e.id ) AS total_comments

      FROM cg_users u, cg_events e

      WHERE u.i_status='1' AND u.i_isdeleted ='1' AND e.i_status = 1 AND e.i_host_id = u.id AND e.i_user_type = 1
      %2\$s )

      ORDER BY `dt_start_time` ASC
      "
      , $this->db->dbprefix, $s_where
      );

      #and t.i_user_id != '%2\$s'
      $query = $this->db->query($sql); //echo $result_arr[0]['count']." === sql ==>". nl2br($sql) ."<br />";   exit;
      $result_arr = $query->result_array();
      #pr($result_arr,1);
      $result=array();
      $userid=intval(decrypt($this->session->userdata('user_id')));
      foreach($result_arr as $val)
      {
      if($val['event_owner_id'] != $userid)
      {
      $ev_p=get_privacy_setting_by_user_id($val['event_owner_id'],'event');
      if(($ev_p['i_friend_privacy'] !=0) || ($ev_p['i_netpal_privacy'] !=0) || ($ev_p['i_prayer_partner_privacy'] !=0) || ($ev_p['i_ring_privacy'] !=0) || ($ev_p['i_prayer_group_privacy'] !=0) )
      {
      $pr=check_event_privacy($val[id],$userid);
      if($pr!=0)
      {
      $result[]=$val;
      }
      }
      else
      {
      $result[]=$val;
      }
      }
      else
      {
      $result[]=$val;
      }

      }
      #pr($result,1);
      //exit;
      return count($result);

      } */

    public function get_owner_by_event_id($event_id) {

        //$sql = sprintf('SELECT i_host_id as i_user_id, i_user_type FROM ' . $this->db->EVENTS . '  WHERE id = %s ', $event_id);
		$sql = "SELECT i_host_id as i_user_id, i_user_type FROM " . $this->db->EVENTS . "  WHERE id = '".$event_id."'";
        $query = $this->db->query($sql);
        $result_arr = $query->result_array();
        return $result_arr[0];
    }

    public function get_by_user_id($user_id, $s_where, $start_limit = "", $no_of_page = "") {
        if ("$start_limit" == "") {
            //$sql = sprintf('SELECT * FROM ' . $this->db->EVENTS . '  WHERE i_host_id = %s AND i_status = 1 AND i_user_type = 1  %s ORDER BY id DESC ', $user_id, $s_where);
			$sql = "SELECT * FROM " . $this->db->EVENTS . "  WHERE i_host_id = '".$user_id."' 
			  					AND i_status = 1 
								AND i_user_type = 1  ".mysql_real_escape_string($s_where)." ORDER BY id DESC";					
        } else {
            //$sql = sprintf('SELECT * FROM ' . $this->db->EVENTS . '  WHERE i_host_id = %s AND i_status = 1 AND i_user_type = 1 %s ORDER BY id DESC LIMIT %s, %s', $user_id, $s_where, $start_limit, $no_of_page);
			$sql = "SELECT * FROM " . $this->db->EVENTS . "  WHERE i_host_id = '".$user_id."'
			 				    AND i_status = 1 
								AND i_user_type = 1
							 ".mysql_real_escape_string($s_where)." ORDER BY id DESC LIMIT ".mysql_real_escape_string($start_limit).", ".mysql_real_escape_string($no_of_page)."";				 
        }
		//echo nl2br($sql);exit;

        $query = $this->db->query($sql);
        $result_arr = $query->result_array();

        $new_array = array();

        if (count($result_arr) > 0) {
            foreach ($result_arr as $key => $val) {
                $result_arr[$key]['event_type'] = ' [ E ] ';
            }
        }


        return $result_arr;
    }

    public function get_all_event_members_by_event_id($event_id, $start_limit = '', $end_limit = '', $where = '') {
        //echo "start : ".$start_limit." -- end : ".$end_limit;
        if ("$start_limit" == '') {
            $limit = '';
        } else {
            $limit = "LIMIT " . intval($start_limit) . ' , ' . intval($end_limit);
        }
        $sql = "SELECT E.*, CONCAT(U.s_first_name,' ',U.s_last_name) AS profile_name,
					U.s_profile_photo, U.id AS post_owner_user_id, I.id AS rsvp_id, I.dt_created_on
					
					FROM {$this->db->EVENTS} E 
					LEFT JOIN {$this->db->EVENTS_USER_INVITED} I ON E.id = I.i_event_id
					LEFT JOIN {$this->db->EVENT_RSVP} ER ON I.i_event_id = ER.i_event_id
					LEFT JOIN {$this->db->USERS} U ON U.id = I.i_user_id
					WHERE E.id={$event_id}
					AND E.i_status=1 AND ER.i_user_id = I.i_user_id {$where} {$limit}
                ";
        ///echo nl2br($sql);exit;
        $res = $this->db->query($sql)->result_array();

        $response = check_friend_netpal_status($res);
        return $response;
    }

    public function get_reminder_todo_text($id) {
        //$sql = sprintf('SELECT s_title FROM ' . $this->db->EVENTS . ' WHERE id = %s', $id);
		$sql = "SELECT s_title FROM " . $this->db->EVENTS . " WHERE id = '".$id."'";
        $query = $this->db->query($sql);
        $result_arr = $query->result_array();
        return get_unformatted_string_edit($result_arr[0]['s_title']);
    }

    public function get_reminder_todo_start_time($id) {
        //$sql = sprintf('SELECT t_start_time FROM ' . $this->db->EVENTS . ' WHERE id = %s', $id);
		$sql = "SELECT t_start_time FROM " . $this->db->EVENTS . " WHERE id = '".$id."'";
        $query = $this->db->query($sql);
        $result_arr = $query->result_array();
        return $result_arr[0]['t_start_time'];
    }

    public function get_reminder_todo_end_time($id) {
        //$sql = sprintf('SELECT t_end_time FROM ' . $this->db->EVENTS . ' WHERE id = %s', $id);
		$sql = "SELECT t_end_time FROM " . $this->db->EVENTS . " WHERE id = '".$id."'";
        $query = $this->db->query($sql);
        $result_arr = $query->result_array();
        return $result_arr[0]['t_end_time'];
    }

    ####get only admin active events 

    public function get_admin_events($s_where, $i_start_limit = '', $i_no_of_page = '') {


        $sql = "
						(SELECT 
						a.id i_user_id,
						a.s_email,
						
						s_name AS s_profile_name,
						1 as s_profile_photo,
						
						e.id,
						e.i_host_id as event_owner_id,
						e.i_user_type,
						e.dt_start_time,
						e.dt_end_time,
						e.dt_created_on,
						e.s_title,
						e.s_address,
						e.s_postcode,
						e.s_desc,
						e.i_country_id,
						e.s_city,
						e.s_state,
						e.s_image_1,
						e.s_image_2,
						e.s_image_3,
						e.s_image_4,
						e.s_image_5,
						(SELECT count(*) FROM cg_event_comments c WHERE c.i_event_id = e.id ) AS total_comments
						
						FROM cg_admin_user a, cg_events e
						
						WHERE a.i_status='1' AND a.e_disabled ='no' AND e.i_status = 1 AND e.i_host_id = a.id AND e.i_user_type = 2
						 {$s_where} )
						ORDER BY `dt_start_time` ASC
						limit {$i_start_limit}, {$i_no_of_page}
						";

        $query = $this->db->query($sql); //echo "sql ==>". nl2br($sql) ."<br />";  exit;
        $result_arr = $query->result_array();

        # pr($result_arr);
        return $result_arr;
    }

    public function get_total_admin_events($s_where) {


        $sql = "SELECT COUNT(*) count FROM (
					(SELECT 
						a.id
						FROM cg_admin_user a, cg_events e
						
						WHERE a.i_status='1' AND a.e_disabled ='no' AND e.i_status = 1 AND e.i_host_id = a.id AND e.i_user_type = 2
						 {$s_where} )
					) derived_tbl
						";

        #and t.i_user_id != '%2\$s'
        $query = $this->db->query($sql); //echo $result_arr[0]['count']." === sql ==>". nl2br($sql) ."<br />";   exit;
        $result_arr = $query->result_array();

        return $result_arr[0]['count'];
    }

    public function get_event_membersID_by_event_id($event_id) {

        $sql = "SELECT U.id AS UID
					FROM {$this->db->EVENTS_USER_INVITED} I
					LEFT JOIN {$this->db->USERS} U ON U.id = I.i_user_id
					WHERE I.i_event_id={$event_id}
                ";
        //echo nl2br($sql);exit;
        $query = $this->db->query($sql); //echo $result_arr[0]['count']." === sql ==>". nl2br($sql) ."<br />";   exit;
        $result_arr = $query->result_array();

        if (count($result_arr)) {
            $res = array();
            foreach ($result_arr as $k => $val) {
                $res[$k] = $val['UID'];
            }
        }
        return $res;
    }

    public function get_event_title_id($id) {

        $sql = "SELECT E.s_title FROM {$this->db->EVENTS} E 
							   where E.id = '".$id."'";

        $query = $this->db->query($sql); //echo $this->db->last_query(); //exit;
        $result_arr = $query->result_array();

        return $result_arr[0]['s_title'];
    }

    public function get_privacy_settings_by_event_id($i_event_id) {
        $s_qry = "SELECT * FROM "
                . $this->db->event_privacy . " WHERE i_event_id = '" . $i_event_id . "'";
        $rs = $this->db->query($s_qry);
        #echo $this->db->last_query();
        foreach ($rs->result() as $row) {
            if ($row->s_section == 'Ring User' || $row->s_section == 'Prayer Group')
                $returnarr[$row->s_section][$row->i_section_id][] = $row->i_user_id;
            else
                $returnarr[$row->s_section][] = $row->i_user_id;
        } //pr($returnarr,1);
        return $returnarr;
    }

    public function get_invited_by_id($id) {
        //echo $id;exit;
        $s_qry = "SELECT * FROM "
                . $this->db->event_invitation . " WHERE i_event_id =" . $id;
        $rs = $this->db->query($s_qry);
        //echo $this->db->last_query();
        foreach ($rs->result() as $row) {
            $returnarr[$row->s_section][] = $row->i_user_id;
        }
        #pr($returnarr);
        return $returnarr;
    }

    ## MY EVENTS = EVENTS JOINED + CREATED 	 FOR ORGANIZER::::::

    public function get_my_events_new($i_user_id, $s_where, $i_start_limit = '', $i_no_of_page = '') {

        $sql = sprintf("
					(SELECT 

					  u.id i_user_id,
					  u.s_email,
					  u.e_gender,
					  CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
					  u.s_profile_photo,
					  
					  e.id,
					  e.i_host_id as event_owner_id,
					  e.i_user_type,
					  e.dt_start_time,
					  e.dt_end_time,
					  e.dt_created_on,
					  e.s_title,
					  e.s_address,
					  e.s_postcode,
					  e.s_desc,
					  e.i_country_id,
					  e.s_city,
					  e.s_state,
					  e.s_image_1,
					  e.s_image_2,
					  e.s_image_3,
					  e.s_image_4,
					  e.s_image_5,
					 
					  
					  (SELECT count(*) FROM cg_event_comments c WHERE c.i_event_id = e.id ) AS total_comments,
					  'followed' as s_type,
					  ' [ E ] ' as event_type
					  
					  FROM cg_users u, cg_events e
					  
					  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND e.i_status = 1 AND e.i_host_id = u.id
					 
					  AND
					  (
					  e.id in
					  (SELECT r.i_event_id from cg_event_rsvp r, cg_users u where
					  r.i_user_id = %2\$s
					  )
					  
					  ) %5\$s )
					  
					 UNION
					 
					 (SELECT 

					  u.id i_user_id,
					  u.s_email,
					  u.e_gender,
					  CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
					  u.s_profile_photo,
					  
					  e.id,
					  e.i_host_id as event_owner_id,
					  e.i_user_type,
					  e.dt_start_time,
					  e.dt_end_time,
					  e.dt_created_on,
					  e.s_title,
					  e.s_address,
					  e.s_postcode,
					  e.s_desc,
					  e.i_country_id,
					  e.s_city,
					  e.s_state,
					  e.s_image_1,
					  e.s_image_2,
					  e.s_image_3,
					  e.s_image_4,
					  e.s_image_5,
					 
					  (SELECT count(*) FROM cg_event_comments c WHERE c.i_event_id = e.id ) AS total_comments,
					  'hosted' as s_type,
					  ' [ E ] ' as event_type
					  
					  FROM cg_users u, cg_events e
					  
					  WHERE u.i_status='1' AND u.i_isdeleted ='1' 
					 
					  AND e.i_status = 1 AND e.i_host_id = %2\$s AND e.i_host_id = u.id
					   %5\$s )

				    ORDER BY `dt_start_time` ASC
					limit %3\$s, %4\$s
					"
                , $this->db->dbprefix, intval($i_user_id), intval($i_start_limit), intval($i_no_of_page), $s_where
        );

        $query = $this->db->query($sql); //echo "sql ==>". nl2br($sql) ."<br />"; 
        $result_arr = $query->result_array();

        if (is_array($result_arr) && count($result_arr)) {
            foreach ($result_arr as $key => $val) {

                $get_friend_status_me_him = $this->users_model->get_friend_status_me_him(intval(decrypt($this->session->userdata('user_id'))), $result_arr[$key]['event_owner_id']);

                $if_friend = $this->users_model->if_already_friend(intval(decrypt($this->session->userdata('user_id'))), $result_arr[$key]['event_owner_id']);


                if (count($get_friend_status_me_him) > 0) {
                    $result_arr[$key]['display_becomefriend'] = 'false';
                }

                if (count($if_friend) > 0) {
                    $result_arr[$key]['if_already_friend'] = 'true';
                } else
                    $result_arr[$key]['if_already_friend'] = 'false';

                $arr_already_netpal = $this->netpals_model->if_already_netpal(intval(decrypt($this->session->userdata('user_id'))), $result_arr[$key]['event_owner_id']);
                if (count($arr_already_netpal) > 0 || (intval(decrypt($this->session->userdata('user_id'))) == $result_arr[$key]['event_owner_id']))
                    $result_arr[$key]['already_added_netpal'] = 'true';
                else
                    $result_arr[$key]['already_added_netpal'] = 'false';

                #unset($get_friend_status_me_him);
            }
        }

        # pr($result_arr);
        return $result_arr;
    }

    public function get_total_my_events_new($i_user_id, $s_where) {


        $sql = "
				SELECT COUNT(*) count FROM (
				(SELECT 
					  e.id
					  FROM cg_users u, cg_events e
					  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND e.i_status = 1 AND e.i_host_id = u.id
					  AND
					  (
					  e.id in
					  (SELECT r.i_event_id from cg_event_rsvp r, cg_users u where
					  r.i_user_id = '".intval($i_user_id)."' 
					  )
					  
					  ) {$s_where})
			     UNION
				 
				 (SELECT 
					  e.id
					  FROM cg_users u, cg_events e
					  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND e.i_status = 1
					   AND e.i_host_id = u.id 
					  AND e.i_host_id = '".intval($i_user_id)."' {$s_where}
				   )
				 

				) derived_tbl
					";

#and t.i_user_id != '%2\$s'
        $query = $this->db->query($sql);
        $result_arr = $query->result_array();
#echo $result_arr[0]['count']." === sql ==>". nl2br($sql) ."<br />";   exit;
        return $result_arr[0]['count'];
    }

    public function get_latest_admin_events() {
        $sql = $this->db->query('select s_title,s_desc,s_city,s_state,i_country_id,dt_start_time,dt_end_time from cg_events where i_user_type = "2" and i_status="1" order by dt_created_on DESC limit 0,2 ');
        $res = $sql->result_array();
        return $res;
    }

    public function get_event_by_user_id($i_user_id) {
        $sql = $this->db->query('select * from cg_events where i_user_type="1" and i_status="1" and i_host_id=' . $i_user_id);
        $res = $sql->result_array();
        return $res;
    }

    public function get_event_inv($id, $page = '', $limit = '') {
        if ($page != '' || $limit != '') {
            $sql = $this->db->query("select c.*,CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,u.s_profile_photo,u.e_gender from cg_event_user_invited c left join cg_users u on c.i_user_id=u.id  where c.i_event_id =" . $id . " group by c.i_user_id limit " . $page . "," . $limit);
        } else {
            $sql = $this->db->query("select c.*,CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,u.s_profile_photo,u.e_gender from cg_event_user_invited c left join cg_users u on c.i_user_id=u.id where c.i_event_id =" . $id . " group by c.i_user_id ");
        }
        //$this->db->last_query();exit;
        $result = $sql->result_array();
        return $result;
    }

    public function gettotal_event_inv($id) {
        $sql = $this->db->query("select count(*) as count from cg_event_invitation where i_event_id =" . $id . "");
        $result = $sql->result_array();
        return $result['0']['count'];
    }

}
