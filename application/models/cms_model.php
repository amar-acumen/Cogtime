<?php
include_once(APPPATH.'models/base_model.php');
class Cms_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

	public function get() {
		$sql = 'SELECT * FROM '.$this->db->CMS_PAGE.' order by id desc';
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
//pr($result_arr,1);
		return $result_arr;
	}
	
	
	
	public function get_by_id($id, $start_limit="", $no_of_page="") {
		
		if("$start_limit" == "") {
			$sql = 'SELECT * FROM '.$this->db->CMS_PAGE.'  where id = "'.$id .'"';
		}
		else {
			$sql = 'SELECT * FROM '.$this->db->CMS_PAGE.'  where id = "'.$id.'" limit {$start_limit}, {$no_of_page}';
		}

		$query = $this->db->query($sql); //echo $this->db->last_query(); //exit;
		$result_arr = $query->result_array();

		return $result_arr[0];
	}
	
	

	public function insert($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->CMS_PAGE, $arr); #echo $this->db->last_query();
		return $this->db->insert_id();
	}
	
	

	public function update($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->CMS_PAGE, $arr, array('id'=>$id));
	}
	

	public function delete_by_id($id) {
		
	     $sql = 'DELETE FROM '.$this->db->CMS_PAGE.' WHERE id="'.$id.'"';

		$this->db->query($sql);
				
	}
	
	
	public function cms_list($where='',$i_start=null,$i_limit=null,$s_order_by='i_order ASC')
    {
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $sql    = " SELECT * FROM {$this->db->CMS_PAGE} {$where} ORDER BY {$s_order_by} {$limit}";

        $query     = $this->db->query($sql); 
        $result_arr = $query->result_array(); 
		$this->load->model("admin_utility_model");
		if(count($result_arr)){
		 foreach($result_arr as $key=>$val)
		  {
			 $result_arr[$key]['image_rank'] = $this->admin_utility_model->RankingRowCreate($val['i_order'],
																			 $val['id'],
																			 $this->db->CMS_PAGE,
																			 $s_where); 
		  }
		}//pr($result_arr,1);
        return $result_arr;
    }
	
    public function cms_list_count($where='')
    {
        
        
        $sql    = "SELECT count(*) as i_total FROM {$this->db->CMS_PAGE}  {$where} ";
        $query     = $this->db->query($sql);
        $result_arr = $query->result_array();
        return $result_arr[0]['i_total'];
    }
	
	public function cms_menu($where='',$i_start=null,$i_limit=null,$s_order_by='1')
    {
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		if($where != ''){
			$sql    = " SELECT 	id, 
								s_title, 
								e_show_in_non_logged_in_page, 
								e_show_in_logged_in_page, 
								e_both 
								FROM {$this->db->CMS_PAGE} {$where} ORDER BY i_order desc {$limit}";
		}
		else
		{
			$sql    = " SELECT 	id, 
								s_title, 
								e_show_in_non_logged_in_page, 
								e_show_in_logged_in_page, 
								e_both 
								FROM {$this->db->CMS_PAGE} ORDER BY i_order desc {$limit}";
		}
		
        $query     = $this->db->query($sql); 
        $result_arr = $query->result_array(); #pr($result_arr,1);
        return $result_arr;
    }
	
	public function change_status($status ,$id) {
		
	  if($status !='' && $id !=''){	
		  $sql = sprintf( "UPDATE {$this->db->CMS_PAGE}  SET `i_status` = '%s'
						   WHERE `id` ='%s'"
					  ,  $status, $id );
		  $this->db->query($sql);// echo $this->db->last_query();exit;
		  return true;
	  }
	}	
}
