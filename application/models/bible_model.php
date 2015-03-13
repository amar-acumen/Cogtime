<?php
include_once(APPPATH.'models/base_model.php');
class Bible_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

	
	public function get_by_id($id, $start_limit="", $no_of_page="") {
		
		$sql = 'SELECT * FROM '.$this->db->BIBLE_QUIZ.'  where id = "'.$id.'"';
		
		$query = $this->db->query($sql); #echo $this->db->last_query(); exit;
		$result_arr = $query->result_array();
		return $result_arr[0];
	}
	
	

	public function insert($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->BIBLE_QUIZ, $arr); #echo $this->db->last_query();
		return $this->db->insert_id();
	}
	
	

	public function update($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->BIBLE_QUIZ, $arr, array('id'=>$id));
		#echo $this->db->last_query();
	}
	

	public function delete_by_id($id) {
	    $sql = 'DELETE FROM '.$this->db->BIBLE_QUIZ.' WHERE id="'.$id.'"';
		$this->db->query($sql);
		
	}
	
	
	public function get_list($where='',$i_start=null,$i_limit=null,$s_order_by='1')
    {
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $sql  = " SELECT C.* 
				  		FROM {$this->db->BIBLE_QUIZ} C
						
						{$where} GROUP BY C.id 
						ORDER BY id DESC {$limit}";

        $query     = $this->db->query($sql); 
        $result_arr = $query->result_array(); //pr($result_arr,1);
		//echo $this->db->last_query(); exit;
        return $result_arr;
    }
	
    public function get_list_count($where='')
    {
        $sql    = "     SELECT count(*) as i_total FROM (
									SELECT C.id
									FROM {$this->db->BIBLE_QUIZ} C
									
									{$where}
									GROUP BY C.id 
									) AS TBL ";
					 
        $query     = $this->db->query($sql);
        $result_arr = $query->result_array();
        return $result_arr[0]['i_total'];
    }

}
