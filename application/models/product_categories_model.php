<?php
include_once(APPPATH.'models/base_model.php');
class Product_categories_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

	
	public function get() {
		$sql = 'SELECT * FROM '.$this->db->TRADE_CAT.' order by id desc';
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr;
	}
	
	
	public function get_by_id($id, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = 'SELECT * FROM '.$this->db->TRADE_CAT.'  where id = "'.$id.'"';
		}
		else {
			$sql = 'SELECT * FROM '.$this->db->TRADE_CAT.'  where id = "'.$id.'" limit '.$start_limit.', '.$no_of_page;
		}

		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
		
		return $result_arr[0];
	}
	


	public function insert($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->TRADE_CAT, $arr); //echo $this->db->last_query();
		return $this->db->insert_id();
	}
	

	public function update($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->TRADE_CAT, $arr, array('id'=>$id));
	}
	

	public function delete_by_id($id) {
	

		## deleting sub category 
		
         $sql = "SELECT * FROM {$this->db->TRADE_CAT} WHERE i_parent_category={$id}";
		 $res = $this->db->query($sql)->result_array();
         if(count($res))
         {
             foreach($res as $r)
             {
                 $sub_id = $r['id'];
                 $this->delete_sub_cat_by_id($sub_id);
             }
         }
         //pr($res,1);
         
		 
		## deleting trade cateory 
	     $sql = 'DELETE FROM '.$this->db->TRADE_CAT.' WHERE id="'.$id.'"';
		 $this->db->query($sql);
		#echo $this->db->last_query(); exit;
	}
    
    public function delete_sub_cat_by_id($id)
    {
        $sql = "DELETE FROM {$this->db->TRADE_CAT} WHERE id= {$id}";
        $this->db->query($sql);
        
    }
	
    
    
    
    
	public function get_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY id DESC';
		
        /*$sql    = " SELECT C.*,
					(SELECT count(*) FROM {$this->db->TRADE_CAT} A, {$this->db->TRADE_CAT} B WHERE B.id=A.i_parent_category
                    AND B.id=C.id) AS total_sub_categories 
                    FROM {$this->db->TRADE_CAT} C {$where}  {$s_order_by} {$limit}";
                    */
        $sql = "SELECT *,p.id AS pid,(SELECT COUNT(*) FROM {$this->db->TRADE_CAT} AS sub WHERE sub.i_parent_category=pid) AS total_sub_categories FROM {$this->db->TRADE_CAT} AS p {$where} {$s_order_by} {$limit}";
       
        
     /*  $sql    = "SELECT C.* FROM {$this->db->TRADE_CAT} AS C 
                    {$where}  {$s_order_by} {$limit}";
 */
        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		
        return $result_arr;
    }
	
    public function get_list_count($where='')
    {
       
        $sql    = "SELECT count(*) as i_total FROM {$this->db->TRADE_CAT} p {$where} ";
        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0]['i_total'];
    }
	
    
    

    
	public function get_sub_cat_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY id DESC';
        
        
        $sql = "SELECT * FROM {$this->db->TRADE_CAT} {$where} {$s_order_by} {$limit}";
          
        $res = $this->db->query($sql)->result_array();
        return $res;
    }
    
    
    public function get_sub_cat_list_count($where='')
    {
        $sql = "SELECT count(*) as i_total from {$this->db->TRADE_CAT} {$where}";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return $result[0]['i_total'];
    }
    
	
}
