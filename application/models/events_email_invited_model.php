<?php
include_once(APPPATH.'models/base_model.php');
class Events_email_invited_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

	public function get() {
		$sql = 'SELECT * FROM '.$this->db->EVENTS_EMAIL_INVITED.' order by id desc';
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
//pr($result_arr,1);
		return $result_arr;
	}
	
	
	
	public function get_by_id($id, $where = "", $start_limit="", $no_of_page="") {
		
		$where_cond = ($where !='')?" AND {$where} ":" ";
		if("$start_limit" == "") {
			$sql = "SELECT E.*, C.s_country_name , U.s_name, U.s_last_name 
							FROM {$this->db->EVENTS_EMAIL_INVITED} E LEFT JOIN  {$this->db->ADMIN_USER} U 
							ON U.id = E.i_host_id 
							LEFT JOIN  {$this->db->MST_COUNTRY} C ON E.i_country_id = C.id 
							where E.id = '".$id."'  {$where_cond}";
		}
		else {
			$sql = "SELECT E.*, C.s_country_name, U.s_name, U.s_last_name FROM {$this->db->EVENTS_EMAIL_INVITED}
							 E LEFT JOIN  {$this->db->ADMIN_USER} U ON U.id = E.i_host_id 
							 LEFT JOIN  {$this->db->MST_COUNTRY} C ON E.i_country_id = C.id 
							  where E.id = '".$id."'   {$where_cond}   limit {$start_limit}, {$no_of_page}";
		}

		$query = $this->db->query($sql); //echo $this->db->last_query(); //exit;
		$result_arr = $query->result_array();

		return $result_arr[0];
	}
	
	

	public function insert($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->EVENTS_EMAIL_INVITED, $arr); #echo $this->db->last_query();
		return $this->db->insert_id();
	}
	
	

	public function update($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->EVENTS_EMAIL_INVITED, $arr, array('id'=>$id));
	}
	

	public function delete_by_id($id) {
	     $sql = 'DELETE FROM '.$this->db->EVENTS_EMAIL_INVITED.' WHERE id="'.$id.'"' ;

		$this->db->query($sql);
				
	}
	
	
	public function get_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY id DESC';
		
        $sql    = " SELECT E.*, U.s_name, U.s_last_name, C.s_country_name FROM {$this->db->EVENTS_EMAIL_INVITED} 
					E LEFT JOIN  {$this->db->ADMIN_USER} U ON U.id = E.i_host_id 
					LEFT JOIN  {$this->db->MST_COUNTRY} C ON E.i_country_id = C.id {$where}  {$s_order_by} {$limit}";

        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		
        return $result_arr;
    }
	
    public function get_list_count($where='')
    {
        
        
        $sql    = "SELECT count(*) as i_total FROM {$this->db->EVENTS_EMAIL_INVITED} E LEFT JOIN  
				  {$this->db->ADMIN_USER} U ON U.id = E.i_host_id  
				  LEFT JOIN  {$this->db->MST_COUNTRY} C ON E.i_country_id = C.id  {$where} ";
        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0]['i_total'];
    }
	
	
	
	
	public function insert_user_invited_emails($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->EVENTS_EMAIL_INVITED, $arr); //echo $this->db->last_query(); exit;
		return $this->db->insert_id();
	}
		
}
