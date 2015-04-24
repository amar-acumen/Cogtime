<?php
include_once(APPPATH.'models/base_model.php');
class Scrolling_headlines_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

	public function get() {
		$sql = 'SELECT * FROM '.$this->db->CHRISTIAN_HEADLINE.' order by id desc';
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
//pr($result_arr,1);
		return $result_arr;
	}
	
	
	
	public function get_by_id($id, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = 'SELECT * FROM '.$this->db->CHRISTIAN_HEADLINE.'  where id = "'.$id.'"';
		}
		else {
			$sql = 'SELECT * FROM '.$this->db->CHRISTIAN_HEADLINE.'  where id = "'.$id.'" limit '.$start_limit.', '.$no_of_page;
		}

		$query = $this->db->query($sql); #echo $this->db->last_query(); exit;
		$result_arr = $query->result_array();

		return $result_arr[0];
	}
	
	

	public function insert($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->CHRISTIAN_HEADLINE, $arr); #echo $this->db->last_query();
		return $this->db->insert_id();
	}
	
	

	public function update($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->CHRISTIAN_HEADLINE, $arr, array('id'=>$id));
	}
	

	public function delete_by_id($id) {
	    $sql = 'DELETE FROM '.$this->db->CHRISTIAN_HEADLINE.' WHERE id="'.$id.'"';
		$this->db->query($sql);
	}
	
	
	public function scrolling_headlines_list($where='',$i_start=null,$i_limit=null,$s_order_by='1')
    {
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $sql    = " SELECT * FROM {$this->db->CHRISTIAN_HEADLINE} {$where} ORDER BY id DESC {$limit}";

        $query     = $this->db->query($sql); 
        $result_arr = $query->result_array(); //pr($result_arr,1);
        return $result_arr;
    }
	
    public function scrolling_headlines_list_count($where='')
    {
        
        
        $sql    = "SELECT count(*) as i_total FROM {$this->db->CHRISTIAN_HEADLINE}  {$where} ";
        $query     = $this->db->query($sql);
        $result_arr = $query->result_array();
        return $result_arr[0]['i_total'];
    }
	
	public function get_scolling_news($where='',$i_start=null,$i_limit=null,$s_order_by='1')
    {
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		if($where != ''){
			$sql    = " SELECT 	id,
								s_title, 
								s_url 
								FROM {$this->db->CHRISTIAN_HEADLINE} {$where} ORDER BY id DESC {$limit}";
		}
		else
		{
			$sql    = " SELECT 	id,
								s_title, 
								s_url  
								FROM {$this->db->CHRISTIAN_HEADLINE} ORDER BY id DESC {$limit}";
		}
		
        $query     = $this->db->query($sql); 
        $result_arr = $query->result_array(); #pr($result_arr,1);
        return $result_arr;
    }
	
	
	public function change_status($status ,$id) {
		
	  if($status !='' && $id !=''){	
		  $sql = "UPDATE {$this->db->CHRISTIAN_HEADLINE}  SET `i_status` = '".$status."'
						   WHERE `id` ='".$id."'";
		  $this->db->query($sql);// echo $this->db->last_query();exit;
		  return true;
	  }
	}
	
	
	 public function get_oldest_id()
    {
        
        
        $sql    = "SELECT id FROM {$this->db->CHRISTIAN_HEADLINE} ORDER BY dt_created_on ASC LIMIT 0 ,1";
        $query     = $this->db->query($sql);
        $result_arr = $query->result_array(); //pr($result_arr,1);
		//echo $this->db->last_query(); 
        return $result_arr[0];
    }
	
	public function get_feed_url_by_id($id)
	{
	echo $sql= "select s_url from {$this->db->CHRISTIAN_HEADLINE} where id=".$id;
	 $query     = $this->db->query($sql);
       $result_arr = $query->result_array();
	return $result_arr[0]['s_url'];
	}
		
}
