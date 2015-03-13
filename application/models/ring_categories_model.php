<?php
include_once(APPPATH.'models/base_model.php');
class Ring_categories_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

	
	public function get() {
		$sql = 'SELECT * FROM '.$this->db->RING_CAT.' order by id desc';
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr;
	}
	
	
	public function get_by_id($id, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = sprintf('SELECT * FROM '.$this->db->RING_CAT.'  where id = %s',  $id);
		}
		else {
			$sql = sprintf('SELECT * FROM '.$this->db->RING_CAT.'  where id = %s limit %s, %s',  $id, $start_limit, $no_of_page);
		}

		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
		
		return $result_arr[0];
	}
	


	public function insert($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->RING_CAT, $arr); //echo $this->db->last_query();
		return $this->db->insert_id();
	}
	

	public function update($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->RING_CAT, $arr, array('id'=>$id));
	}
	

	public function delete_by_id($id) {
	
	
		## deleting rings 
		 $sql = sprintf( 'DELETE FROM '.$this->db->RING.' WHERE i_category_id=%s', $id );
		 $this->db->query($sql);
		 
		## deleting ring cateory 
	     $sql = sprintf( 'DELETE FROM '.$this->db->RING_CAT.' WHERE id=%s', $id );
		 $this->db->query($sql);
		#echo $this->db->last_query(); exit;
	}
	
	public function get_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY id DESC';
		
        $sql    = " SELECT C.*,
					(SELECT count(*) FROM {$this->db->RING} R WHERE C.id = R.i_category_id  ) AS total_rings
					FROM {$this->db->RING_CAT} C 
					
					{$where}  {$s_order_by} {$limit}";

        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		
        return $result_arr;
    }
	
    public function get_list_count($where='')
    {
        
        
        $sql    = "SELECT count(*) as i_total FROM {$this->db->RING_CAT} C 
					
				  	{$where} ";
        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0]['i_total'];
    }
	
	public function delete_sub_cat_by_id($id)
    {
        $sql = "DELETE FROM {$this->db->RING_CAT} WHERE id= {$id}";
        $this->db->query($sql);
        
    }
	
	public function get_sub_cat_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY id DESC';
        
        
        $sql = "SELECT * FROM {$this->db->RING_CAT} {$where} {$s_order_by} {$limit}";
          
        $res = $this->db->query($sql)->result_array();
		
	
		//pr($res,1);
        return $res;
    }
    
    
    public function get_sub_cat_list_count($where='')
    {
        $sql = "SELECT count(*) as i_total from {$this->db->RING_CAT} {$where}";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return $result[0]['i_total'];
    }
	
	
	
	public function insert_cat($arr=array()) {
		 
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->RING_CAT, $arr); //echo $this->db->last_query();
		return $this->db->insert_id();
	}
    
	public function update_cat($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->RING_CAT, $arr, array('id'=>$id));
	}
	
	public function getCategory($where='')
    {
        
       $sql = "SELECT *,
						subcat.i_parent_category AS subcat_i_parent_category, 
						(SELECT s_category_name FROM {$this->db->RING_CAT} AS pcat WHERE pcat.id=subcat_i_parent_category) AS pcat_name
								FROM {$this->db->RING_CAT} AS subcat WHERE subcat.i_parent_category > 0 {$where} ORDER BY pcat_name ASC
				 ";
          
        $res = $this->db->query($sql)->result_array();
        return $res;
    }
    
	
	public function move_ring($del_id,$cat_id,$subcat_id)
	{
		$sql=$this->db->query("select GROUP_CONCAT(id) as ring_id from cg_user_ring where i_category_id =".$del_id);
		//echo $this->db->last_query();
		$res=$sql->result_array();
		$sql1=$this->db->query("update cg_user_ring set i_category_id=".$cat_id." where id IN (".$res['0']['ring_id'].")");
		//echo $this->db->last_query();
		$sql2=$this->db->query("update cg_user_ring set i_sub_category_id=".$subcat_id." where id IN (".$res['0']['ring_id'].")");
		//	echo $this->db->last_query();
		return true;
	}
        
        	public function getChurchCategory($where='')
    {
        
       $sql = "SELECT *,
						subcat.i_parent_category AS subcat_i_parent_category, 
						(SELECT s_category_name FROM cg_church_ring_category AS pcat WHERE pcat.id=subcat_i_parent_category) AS pcat_name
								FROM cg_church_ring_category AS subcat WHERE subcat.i_parent_category > 0 AND subcat.church_id = '".$_SESSION['logged_church_id']."'  {$where} ORDER BY pcat_name ASC
				 "; 
          
        $res = $this->db->query($sql)->result_array();
        return $res;
    }
}
