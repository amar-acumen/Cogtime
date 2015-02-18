<?php

class Language_model extends CI_Model{
	
	public function __construct() {
		parent::__construct();
	}

	public function get($name)
	 {
		$result = $this->db->get('language');
		$row = $result->row_array();
		if(isset($row[$name])) {
			return $row[$name];
		}
		else {
			return null;
		}
	}
	
	public function get_all()
	 {
		
		
		$result = $this->db->get('language');
		$row = $result->row_array();
		if(isset($row)) {
			return $row;
		}
		else {
			return null;
		}
	}
	public function insert($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->LANGUAGE, $arr); #echo $this->db->last_query();
		return $this->db->insert_id();
	}
	
	
	public function get_by_id($id, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = sprintf('SELECT * FROM '.$this->db->LANGUAGE.'  where id = %s',  $id);
		}
		else {
			$sql = sprintf('SELECT * FROM '.$this->db->LANGUAGE.'  where id = %s limit %s, %s',  $id, $start_limit, $no_of_page);
		}
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
		
		return $result_arr[0];
	}
	
	
	public function lang_list($where='',$i_start=null,$i_limit=null,$s_order_by='id ASC')
    {
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $sql    = " SELECT * FROM {$this->db->LANGUAGE} {$where} ORDER BY {$s_order_by} {$limit}";

        $query     = $this->db->query($sql); 
        $result_arr = $query->result_array(); 
		/*$this->load->model("admin_utility_model");
		if(count($result_arr)){
		 foreach($result_arr as $key=>$val)
		  {
			 $result_arr[$key]['image_rank'] = $this->admin_utility_model->RankingRowCreate($val['i_order'],
																			 $val['id'],
																			 $this->db->CMS_PAGE,
																			 $s_where); 
		  }
		}//pr($result_arr,1);*/
        return $result_arr;
    }
	public function lang_list_count($where='')
	{
		$sql=" SELECT * FROM {$this->db->LANGUAGE}";
		$q=$this->db->query($sql);
		$result=$q->num_rows();
		return $result;
	}
    public function update($arr=array(), $i_id) 
    {
        if(count($arr)==0) {
            return null;
        }
      $this->db->update('language', $arr, array('id'=>$i_id));
	 // echo 1;exit;
    }
    
   Public function delete_by_id($id)
	{
		$sql="delete from ".$this->db->LANGUAGE." where id=".$id;
		//echo $sql;exit;
		$this->db->query($sql);
	}   
    
	public function change_status($status,$id)
	{
	$sql= "update ".$this->db->LANGUAGE." set is_enabled=".$status." where id=".$id;
	//echo $sql;exit;
		$this->db->query($sql);
		return true;
	}
} 
