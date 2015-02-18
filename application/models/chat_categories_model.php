<?php
include_once(APPPATH.'models/base_model.php');
class Chat_categories_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

	
	public function get() {
		$sql = sprintf('SELECT * FROM '.$this->db->chat_category.' order by id desc');
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr;
	}
	
	
	public function get_by_id($id, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = sprintf('SELECT * FROM '.$this->db->chat_category.'  where id = %s',  $id);
		}
		else {
			$sql = sprintf('SELECT * FROM '.$this->db->chat_category.'  where id = %s limit %s, %s',  $id, $start_limit, $no_of_page);
		}

		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
		
		return $result_arr[0];
	}
	


	public function insert($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->chat_category, $arr); //echo $this->db->last_query();
		return $this->db->insert_id();
	}
	

	public function update($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->chat_category, $arr, array('id'=>$id));
	}
	

	public function delete_by_id($id) {
	

		## deleting sub category 
		
         $sql = "SELECT * FROM {$this->db->chat_category} WHERE i_parent_category={$id}";
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
	     $sql = sprintf( 'DELETE FROM '.$this->db->chat_category.' WHERE id=%s', $id );
		 $this->db->query($sql);
		#echo $this->db->last_query(); exit;
	}
    
    public function delete_sub_cat_by_id($id)
    {
        $sql = "DELETE FROM {$this->db->chat_category} WHERE id= {$id}";
        $this->db->query($sql);
        
    }
	
    
    
    
    
	public function get_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY id DESC';
		
        /*$sql    = " SELECT C.*,
					(SELECT count(*) FROM {$this->db->chat_category} A, {$this->db->chat_category} B WHERE B.id=A.i_parent_category
                    AND B.id=C.id) AS total_sub_categories 
                    FROM {$this->db->chat_category} C {$where}  {$s_order_by} {$limit}";
                    */
        $sql = "SELECT *,p.id AS pid,(SELECT COUNT(*) FROM {$this->db->chat_category} AS sub WHERE sub.i_parent_category=pid) AS total_sub_categories FROM {$this->db->chat_category} AS p {$where} {$s_order_by} {$limit}";
       
        
     /*  $sql    = "SELECT C.* FROM {$this->db->chat_category} AS C 
                    {$where}  {$s_order_by} {$limit}";
 */
        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		
        return $result_arr;
    }
	
    public function get_list_count($where='')
    {
       
        $sql    = "SELECT count(*) as i_total FROM {$this->db->chat_category} p {$where} ";
        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0]['i_total'];
    }
	
    
    

    
	public function get_sub_cat_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY id DESC';
        
        
        $sql = "SELECT * FROM {$this->db->chat_category} {$where} {$s_order_by} {$limit}";
          
        $res = $this->db->query($sql)->result_array();
		
		if(count($res)){
			foreach($res as $k=> $val){
				$whr = " WHERE cat.i_parent_cat_id =  ".$val['i_parent_category'];
				$res[$k]['total_room'] = $this->get_total_chat_rooms($whr);
			}
		}
		//pr($res,1);
        return $res;
    }
    
    
    public function get_sub_cat_list_count($where='')
    {
        $sql = "SELECT count(*) as i_total from {$this->db->chat_category} {$where}";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return $result[0]['i_total'];
    }
	
	public function get_total_chat_rooms($where='')
    {
        
        $sql = " SELECT COUNT(*) as count FROM( select cat.id
					from cg_chat_category C
					LEFT join cg_room_cat cat ON C.id = cat.i_cat_id
					left join room r ON r.room_id = cat.i_room_id
					{$where} ) as drvd_tbl";
          
        $res = $this->db->query($sql)->result_array();
        return $res[0]['count'];
    }
	
	public function insert_cat($arr=array()) {
		 
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->room_cat, $arr); 
		return $this->db->insert_id();
	}
    
	public function update_cat($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->room_cat, $arr, array('i_room_id'=>$id));
	}
	
	public function get_category_id($where='')
    {
        
        $sql = "select C.id
					from cg_chat_category C
					LEFT join cg_room_cat cat ON C.id = cat.i_cat_id
					
					{$where} ";
          #left join room r ON r.room_id = cat.i_room_id
        $res = $this->db->query($sql)->result_array();
        return $res[0]['id'];
    }
	
	public function get_category_name($where='')
    {
        
        $sql = "select *
					from cg_chat_category C
					LEFT join cg_room_cat cat ON C.id = cat.i_cat_id
					
					{$where} ";
          #left join room r ON r.room_id = cat.i_room_id
        $res = $this->db->query($sql)->result_array(); //pr($res,1);
        return $res[0]['s_category_name'];
    }
	
	
	public function getCategory($where='')
    {
        
       $sql = "SELECT *,
						subcat.i_parent_category AS subcat_i_parent_category, 
						(SELECT s_category_name FROM cg_chat_category AS pcat WHERE pcat.id=subcat_i_parent_category) AS pcat_name
								FROM cg_chat_category AS subcat WHERE subcat.i_parent_category > 0 {$where} ORDER BY pcat_name ASC
				 ";
          
        $res = $this->db->query($sql)->result_array();
        return $res;
    }
	
}
