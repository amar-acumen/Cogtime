<?php
include_once(APPPATH.'models/base_model.php');
class Priviledges_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

	public function get() {
		$sql = sprintf('SELECT * FROM '.$this->db->ADMIN_GRP_PRIVILEGE.' order by id desc');
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
//pr($result_arr,1);
		return $result_arr;
	}
	
	
	
	public function get_by_id($id, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = sprintf('SELECT * FROM '.$this->db->ADMIN_GRP_PRIVILEGE.'  where id = %s',  $id);
		}
		else {
			$sql = sprintf('SELECT * FROM '.$this->db->ADMIN_GRP_PRIVILEGE.'  where id = %s limit %s, %s',  $id, $start_limit, $no_of_page);
		}

		$query = $this->db->query($sql); #echo $this->db->last_query(); exit;
		$result_arr = $query->result_array();

		return $result_arr[0];
	}
	
	

	public function insert($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->ADMIN_GRP_PRIVILEGE, $arr); #echo $this->db->last_query();
		return $this->db->insert_id();
	}
	
	

	public function update($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->ADMIN_GRP_PRIVILEGE, $arr, array('id'=>$id));
	}
	

	public function delete_by_id($id) {
	    $sql = sprintf( 'DELETE FROM '.$this->db->ADMIN_GRP_PRIVILEGE.' WHERE i_id_admin_user_grp =%s', $id );
		$this->db->query($sql);
	}
	
	
	public function get_priviledges_id_list($where='',$i_start=null,$i_limit=null,$s_order_by='1')
    {
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $sql    = " SELECT i_privilege_id FROM {$this->db->ADMIN_GRP_PRIVILEGE} {$where} ORDER BY id DESC {$limit}";

        $query     = $this->db->query($sql); 
        $result_arr = $query->result_array(); //pr($result_arr,1);
		if(count($result_arr)){
			$res_arr = array();
			foreach($result_arr as $k=> $val){
				$res_arr[$k] = $val['i_privilege_id'];
			}
		}
        return $res_arr;
    }
	
    
	
	public function change_status($status ,$id) {
		
	  if($status !='' && $id !=''){	
		  $sql = sprintf( "UPDATE {$this->db->ADMIN_GRP_PRIVILEGE}  SET `i_status` = '%s'
						   WHERE `id` ='%s'"
					  ,  $status, $id );
		  $this->db->query($sql);// echo $this->db->last_query();exit;
		  return true;
	  }
	}
	
	
		
}
