<?php
include_once(APPPATH.'models/base_model.php');
class Admin_groups_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

	public function get() {
		$sql = 'SELECT * FROM '.$this->db->ADMIN_USER_GRP.' order by id desc';
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
//pr($result_arr,1);
		return $result_arr;
	}
	
	
	
	public function get_by_id($id, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = sprintf('SELECT * FROM '.$this->db->ADMIN_USER_GRP.'  where id = %s',  $id);
		}
		else {
			$sql = sprintf('SELECT * FROM '.$this->db->ADMIN_USER_GRP.'  where id = %s limit %s, %s',  $id, $start_limit, $no_of_page);
		}

		$query = $this->db->query($sql); #echo $this->db->last_query(); exit;
		$result_arr = $query->result_array();
        
		return $result_arr[0];
	}
	
	

	public function insert($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->ADMIN_USER_GRP, $arr); 
        #echo $this->db->last_query();
		return $this->db->insert_id();
	}
	
	

	public function update($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->ADMIN_USER_GRP, $arr, array('id'=>$id));
	}
	

	public function delete_by_id($id) {
	     $sql = sprintf( 'DELETE FROM '.$this->db->ADMIN_USER_GRP.' WHERE id=%s', $id );

		$this->db->query($sql);
				
	}
	
	
	public function admin_groups_list($where='',$i_start=null,$i_limit=null,$s_order_by='1')
    {
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $sql    = " SELECT * FROM {$this->db->ADMIN_USER_GRP} {$where} ORDER BY id DESC {$limit}";

        $query     = $this->db->query($sql); 
        $result_arr = $query->result_array();// pr($result_arr);
		
		if(count($result_arr)){
			foreach($result_arr as $k=>$val){
				  $whr = " AND U.i_id_admin_user_group = ".$val['id'];
				 $result_arr[$k]['total_member'] = $this->admin_user_count_by_grpID( $whr);
			}
		}
        return $result_arr;
    }
	
    public function admin_groups_list_count($where='')
    {
        
        
        $sql    = "SELECT count(*) as i_total FROM {$this->db->ADMIN_USER_GRP}  {$where} ";
        $query     = $this->db->query($sql);
        $result_arr = $query->result_array();
        return $result_arr['i_total'];
    }
    
    public function update_status($arr=array(), $id) {
        if(count($arr)==0|| intval($id)==0) {
			
            return false;
        }
        else{
            $this->db->update($this->db->ADMIN_USER_GRP, $arr, array('id'=>$id)); //echo $this->db->last_query();
            return true;    
        }
        
        
    }
	
	
	public function admin_user_list_by_grpID($where='',$i_start=null,$i_limit=null,$s_order_by='1')
    {
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
         $sql    = " SELECT G.id as grp_id,
		 					G.s_name as grp_name,
							U.id as id,
							U.s_email,
							U.s_name,
							U.i_id_admin_user_group,
							U.i_status 
							 FROM
							 {$this->db->ADMIN_USER_GRP} G, {$this->db->ADMIN_USER} U
							 WHERE G.id = U.i_id_admin_user_group
							 {$where} ORDER BY U.id DESC {$limit}";

        $query     = $this->db->query($sql); 
        $result_arr = $query->result_array(); //pr($result_arr,1);
        return $result_arr;
    }
	
    public function admin_user_count_by_grpID($where='')
    {
        
        
        $sql    = " SELECT COUNT(*) as count FROM (SELECT U.id FROM
							 {$this->db->ADMIN_USER_GRP} G, {$this->db->ADMIN_USER} U
							 WHERE G.id = U.i_id_admin_user_group
							 {$where} ) as drvd_tbl";
        $query     = $this->db->query($sql);
        $result_arr = $query->result_array(); //pr($result_arr,1);
        return $result_arr[0]['count'];
    }
	
	
	
	public function getPriviledges_by_Groupid($grp_id, $where='')
    {
        
        $sql    = " SELECT       M.s_keyword
								 FROM
							     cg_admin_grp_privilege G
							     LEFT JOIN cg_grp_privilege P ON P.id = G.i_privilege_id
								 LEFT JOIN cg_bo_menu M ON M.i_prviledge_id = P.id
								 WHERE  G.i_id_admin_user_grp = ".$grp_id."
								 AND P.i_status = 1
								{$where} ORDER BY P.id DESC {$limit}";
								
								

        $query     = $this->db->query($sql); 
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		if(count($result_arr)){
			foreach($result_arr as $k=> $val){
				$result[$k]  = $val['s_keyword'];
			}
		}
        return $result;
    }
	
	public function getAllPriviledgesGroupid($grp_id, $where='')
    {
        
        $sql   = " SELECT   P.id
								 FROM
							     cg_admin_grp_privilege G
							     LEFT JOIN cg_grp_privilege P ON P.id = G.i_privilege_id
								 LEFT JOIN cg_bo_menu M ON M.i_prviledge_id = P.id
								 WHERE  G.i_id_admin_user_grp = ".$grp_id."
								 AND P.i_status = 1
								{$where} ORDER BY P.id DESC {$limit}";
								
								

        $query     = $this->db->query($sql); 
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		if(count($result_arr)){
			$res_arr = array();
			foreach($result_arr as $k=> $val){
				$res_arr[$k] = $val['id'];
			}
		}
		
        return $res_arr;
    }
	
	
	public function getMenuDetail( $where='')
    {
        
        $sql   = " SELECT   P.id as priv_id, 
							M.s_keyword,
							M.i_parent_menu_id,
							M.i_sub_menu_id,
							M.i_sec_sub_menu_id,
							M.s_parent_menu_name,
							M.s_sub_menu_name,
							M.s_sec_sub_menu_name
							
								 FROM
							     cg_grp_privilege P 
								 LEFT JOIN cg_bo_menu M ON M.i_prviledge_id = P.id
								 WHERE P.i_status = 1
								{$where} ORDER BY M.i_parent_menu_id ASC {$limit}";
        $query     = $this->db->query($sql); 
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		if(count($result_arr)){
			$res_arr = array();
			foreach($result_arr as $k=> $val){
				
				$res_arr[$val['i_parent_menu_id']][$k] = $val;
				$res_arr[$val['i_parent_menu_id']]['s_parent_menu_name'] = $val['s_parent_menu_name'];
				//
			}
		}
		//pr($res_arr,1);
		
		//pr($res_arr);
        return $res_arr;
    }
	
	public function getAllPriviledges($where='')
    {
        
        $sql    = " SELECT       M.s_keyword
								 FROM
							     cg_grp_privilege P 
								 LEFT JOIN cg_bo_menu M ON M.i_prviledge_id = P.id
								 WHERE  P.i_status = 1
								{$where} ORDER BY P.id DESC {$limit}";
								
        $query     = $this->db->query($sql); 
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		if(count($result_arr)){
			foreach($result_arr as $k=> $val){
				$result[$k]  = $val['s_keyword'];
			}
		}
        return $result;
    }
	
		
}
