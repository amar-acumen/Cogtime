<?php
include_once(APPPATH.'models/base_model.php');
class Events_user_invited_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
		$this->load->model('netpals_model');
		$this->load->model('users_model');
	}

	
	
	

	public function update($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->EVENTS_USER_INVITED, $arr, array('id'=>$id));
	}
	

	public function delete_by_id($id) {
	     $sql = sprintf( 'DELETE FROM '.$this->db->EVENTS_USER_INVITED.' WHERE id=%s', $id );
		 $this->db->query($sql);
				
	}
	
	public function insert_user_invited_from_contacts($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->EVENTS_USER_INVITED, $arr); //echo $this->db->last_query(); exit;
		return $this->db->insert_id();
	}
	
	
	
	public function get_events_invitation_recived($i_user_id, $s_where, $i_start_limit='', $i_no_of_page='') {
		
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
					(SELECT count(*) FROM cg_event_comments c WHERE c.i_event_id = e.id ) AS total_comments
					
					FROM cg_users u, cg_events e
					
					WHERE u.i_status='1' AND u.i_isdeleted ='1' AND e.i_status = 1 AND e.i_host_id = u.id
					
					AND
					(
					e.id in
					(SELECT ui.i_event_id from cg_event_user_invited ui, cg_users u where
					ui.i_user_id = %2\$s
					)
					
					) %3\$s )

				ORDER BY `dt_created_on` ASC
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
					  (SELECT count(*) FROM cg_event_comments c WHERE c.i_event_id = e.id ) AS total_comments
					  
					  FROM cg_users u, cg_events e
					  
					  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND e.i_status = 1 AND e.i_host_id = u.id
					  
					  AND
					  (
					  e.id in
					  (SELECT ui.i_event_id from cg_event_user_invited ui, cg_users u where
					  ui.i_user_id = %2\$s
					  )
					  
					  ) %5\$s )

				    ORDER BY `dt_created_on` ASC
					limit %3\$s, %4\$s
					"
				, $this->db->dbprefix, intval($i_user_id), intval($i_start_limit), intval($i_no_of_page),  $s_where
			);
		}

#AND t.i_user_id != '%2\$s'
		$query = $this->db->query($sql); #echo "sql ==>". nl2br($sql) ."<br />"; 
		$result_arr = $query->result_array();
		
     	if(is_array($result_arr) && count($result_arr)){
			foreach($result_arr as $key=>$val){
				
				$get_friend_status_me_him = $this->users_model->get_friend_status_me_him(intval(decrypt($this->session->userdata('user_id'))) , $result_arr[$key]['event_owner_id']);
                
                $if_friend = $this->users_model->if_already_friend(intval(decrypt($this->session->userdata('user_id'))) , $result_arr[$key]['event_owner_id']);
                
		#pr($get_friend_status_me_him);
				#echo count($get_friend_status_me_him);
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
     
		return $result_arr;
		
	
	}
	
	public function get_total_events_invitation_recived($i_user_id,  $s_where) {
		

		 $sql = sprintf("
				SELECT COUNT(*) count FROM (
				(SELECT 
					  e.id
					  FROM cg_users u, cg_events e
					  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND e.i_status = 1 AND e.i_host_id = u.id
					  AND
					  (
					  e.id in
					  (SELECT ui.i_event_id from cg_event_user_invited ui, cg_users u where
					  ui.i_user_id = %2\$s 
					  )
					  
					  )%3\$s )

				) derived_tbl
					"
				, $this->db->dbprefix, intval($i_user_id),$s_where
			);
		
#and t.i_user_id != '%2\$s'
		$query = $this->db->query($sql); 
		$result_arr = $query->result_array();
//echo $result_arr[0]['count']." === sql ==>". nl2br($sql) ."<br />";   exit;
		return $result_arr[0]['count'];
	
	}
	
	
	## send rsvp insert 
	
	public function insert_user_rsvp($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->EVENT_RSVP, $arr); #echo $this->db->last_query(); exit;
		return $this->db->insert_id();
	}
	
	
   #### rsvps recieved 
   
   public function get_events_rsvps_recived($i_user_id, $s_where, $i_start_limit='', $i_no_of_page='') {
		
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
		
		$query = $this->db->query($sql); //echo "sql ==>". nl2br($sql) ."<br />"; 
		# AND e.id in (SELECT r.i_event_id from cg_event_rsvp r)
		$result_arr = $query->result_array();
		
		if(count($result_arr)){
			foreach($result_arr as $k=> $val){
				$result_arr[$k]['rsvp_list'] = $this->getEventsRSVPS($val['id']);
				$result_arr[$k]['total_rsvp'] = count($result_arr[$k]['rsvp_list']);
			}
		}
		
		return $result_arr;
   	
   }

	
	public function get_total_events_rsvps_recived($i_user_id,  $s_where) {
		
		 $sql = sprintf("
				SELECT COUNT(*) count FROM (
				 (SELECT 
					  e.id
					  FROM cg_users u, cg_events e
					  WHERE u.i_status='1' AND u.i_isdeleted ='1' AND e.i_status = 1
					   AND e.i_host_id = u.id 
					  AND e.i_host_id = %2\$s
					  
					  %3\$s
				   )
				) derived_tbl
					"
				, $this->db->dbprefix, intval($i_user_id),$s_where
			);
		#AND e.id in (SELECT r.i_event_id from cg_event_rsvp r)
		$query = $this->db->query($sql); 
		$result_arr = $query->result_array();
		return $result_arr[0]['count'];
	
  }
  
  public function getEventsRSVPS($event_id){
	  
	  $SQL = " SELECT u.id ,
	  				  u.s_email,
					  u.e_gender,
					  CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
					  u.s_profile_photo,
					  ER.dt_created_on as rsvp_dt
	  					  FROM
	  					  cg_events E 
						  LEFT JOIN {$this->db->EVENT_RSVP} ER  ON  ER.i_event_id = E.id 
						  LEFT JOIN cg_users u ON u.id = ER.i_user_id
						  WHERE ER.i_event_id = {$event_id}
						  ";
						  
	  $query = $this->db->query($SQL); 
	  $result_arr = $query->result_array();
	  return $result_arr;
  }
	
	
	
	
}
