<?php
include_once(APPPATH.'models/base_model.php');
class Reporting_abuse_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

	public function get() {
		$sql = 'SELECT * FROM '.$this->db->ABUSE_DICTIONARY.' order by id desc';
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
//pr($result_arr,1);
		return $result_arr;
	}
	
	
	
	public function get_by_id($id, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = sprintf('SELECT * FROM '.$this->db->ABUSE_DICTIONARY.'  where id = %s',  $id);
		}
		else {
			$sql = sprintf('SELECT * FROM '.$this->db->ABUSE_DICTIONARY.'  where id = %s limit %s, %s',  $id, $start_limit, $no_of_page);
		}

		$query = $this->db->query($sql); #echo $this->db->last_query(); exit;
		$result_arr = $query->result_array();

		return $result_arr[0];
	}
	
	

	public function insert($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->ABUSE_DICTIONARY, $arr); #echo $this->db->last_query();
		return $this->db->insert_id();
	}
	
	

	public function update($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->ABUSE_DICTIONARY, $arr, array('id'=>$id));
	}
	

	public function delete_by_id($id) {
	     $sql = sprintf( 'DELETE FROM '.$this->db->ABUSE_DICTIONARY.' WHERE id=%s', $id );

		$this->db->query($sql);
				
	}
	
	
	public function abuse_word_list($where='',$i_start=null,$i_limit=null,$s_order_by='1')
    {
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $sql    = " SELECT * FROM {$this->db->ABUSE_DICTIONARY} {$where} ORDER BY id DESC {$limit}";

        $query     = $this->db->query($sql); 
        $result_arr = $query->result_array(); #pr($result_arr,1);
        return $result_arr;
    }
	
    public function abuse_word_list_count($where='')
    {
        
        
        $sql    = "SELECT count(*) as i_total FROM {$this->db->ABUSE_DICTIONARY}  {$where} ";
        $query     = $this->db->query($sql);
        $result_arr = $query->result_array();
        return $result_arr['i_total'];
    }
	
	
		
}
