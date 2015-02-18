<?php

class Events_comments_model extends CI_Model {
	
	public function __construct() {
		parent::__construct();
		$this->load->model('netpals_model');
		$this->load->model('users_model');
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
                    ." FROM ". $this->db->event_comments ." A " 
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
                    ." FROM ". $this->db->event_comments ." A "
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
    
	
	
	
	

	public function get_by_event_id($i_media_id,  $i_start_limit="", $i_no_of_page="") { 
        
		if("$i_start_limit" == "") {
			$sql = sprintf("SELECT c.id, 
								   c.i_event_id,
								   c.i_user_id, 
								   c.s_contents, 
								   c.dt_created_on, 
								   CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
								   u.s_profile_photo, 
								  
								   u.s_first_name as pseudo 
						FROM %1\$sevent_comments c, %1\$susers u 
						WHERE c.i_user_id=u.id 
						    AND c.i_event_id = %2\$s 
							
						   ORDER BY c.dt_created_on DESC", 
						   $this->db->dbprefix, 
						   intval($i_media_id));
		}
		else {
			$sql = sprintf("SELECT c.id, 
								   c.i_event_id,
								   c.i_user_id, 
								   c.s_contents, 
								   c.dt_created_on, 
								   CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
								   u.s_profile_photo, 
								  
								   u.s_first_name as pseudo 
					    FROM %1\$sevent_comments c, %1\$susers u 
						WHERE c.i_user_id=u.id
						 AND c.i_event_id = %2\$s 
						 
						 ORDER BY dt_created_on DESC LIMIT %3\$s, %4\$s", 
						 $this->db->dbprefix,
						 intval($i_media_id), 
						 intval($i_start_limit), 
						 intval($i_no_of_page));
		}

       // echo $sql;
		$query = $this->db->query($sql); 
    //echo $this->db->last_query();
		$result_arr = $query->result_array();
		
		if(is_array($result_arr) && count($result_arr)){
			foreach($result_arr as $key=>$val){
				
				$get_friend_status_me_him = $this->users_model->get_friend_status_me_him(intval(decrypt($this->session->userdata('user_id'))) , $result_arr[$key]['i_user_id']);
                
                $if_friend = $this->users_model->if_already_friend(intval(decrypt($this->session->userdata('user_id'))) , $result_arr[$key]['i_user_id']);
                
		
			 		if(count($get_friend_status_me_him) > 0  ) { 
						 $result_arr[$key]['display_becomefriend']     ='false';
					 }
					
					if(count($if_friend)>0)
                    {
                        $result_arr[$key]['if_already_friend']     ='true';
                    }
                    else
                        $result_arr[$key]['if_already_friend']     ='false';
						
				   $arr_already_netpal=$this->netpals_model->if_already_netpal( intval(decrypt($this->session->userdata('user_id'))) , $result_arr[$key]['i_user_id']);
					  if(count($arr_already_netpal)>0 || (intval(decrypt($this->session->userdata('user_id'))) == $result_arr[$key]['i_user_id']))
						  $result_arr[$key]['already_added_netpal']='true';
					  else
						  $result_arr[$key]['already_added_netpal']='false';
							
					 #unset($get_friend_status_me_him);
			}
		}
		
		

		return $result_arr;
	}
	
	

	public function get_total_by_event_id($i_media_id) {
		$sql = sprintf("SELECT count(*) count FROM %1\$sevent_comments c, %1\$susers u 
						WHERE c.i_user_id=u.id 
						AND c.i_event_id = %2\$s 
						order by c.dt_created_on", $this->db->dbprefix, intval($i_media_id));

		$query = $this->db->query($sql); //echo nl2br($sql);
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}



	public function insert($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert('event_comments', $arr);
		#echo $this->db->last_query();
		return $this->db->insert_id();
	}

	public function update($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update('event_comments', $arr, array('id'=>$id));
	}

	public function delete_by_id($id, $s_media_type) {
		
		$sql = sprintf( 'DELETE FROM %sevent_comments WHERE i_media_id=%s AND s_media_type = "%s" ', $this->db->dbprefix, $id, $s_media_type );

		$this->db->query($sql);
		
		
	}
          public function get_all_comments($s_where,$i_start_limit='', $i_no_of_page=''){
           // die('dd');
            $sql = "select c.* ,p.s_title,p.s_address,u.s_first_name, u.s_last_name from cg_event_comments c , cg_events p ,cg_users u  $s_where and c.i_event_id = p.id  and u.id = c.i_user_id ORDER BY c.id DESC limit ".$i_start_limit.",".$i_no_of_page;
           
            $query=$this->db->query($sql);
$result_arr=$query->result_array();

return $result_arr;
        }
        public function get_all_comments_total($s_where) {
            $sql = "select count(*)as count from cg_event_comments c , cg_users u $s_where and u.id = c.i_user_id ORDER BY c.id DESC ";
             $query = $this->db->query($sql); #echo "sql ==>". ($sql);exit; 
		$result_arr = $query->result_array();
		return $result_arr['0']['count'];
            
        }
	
  
}
