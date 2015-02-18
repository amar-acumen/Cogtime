<?php
/*********
* Author: 
* Date  : 
* Modified By: 
* Modified Date:
* 
* Purpose:
*  Model For  
* 
* @package 
* @subpackage 
* 
* @link InfModel.php 
* @link Base_model.php
* @link controllers/
* @link views/
*/
require_once(APPPATH.'models/base_model.php');
class E_swap_model extends Base_model 
{
		
        # constructor definition...
	public function __construct() 
	{
		try
        {
          parent::__construct();
          $this->conf =get_config();
		  $this->load->model("users_model");	
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }    
	}

    
    function save_eswap_product($info)
    {
        $res = $this->db->insert($this->db->ESWAP_PROD ,$info);
        //echo $this->db->last_query();exit;
        $lastid = $this->db->insert_id();
        if ($lastid)
		{
			return $lastid;
        }
		else
        {
		    return false;    
        }
    }
	function save_eswap_wantproduct($info)
    {
        $res = $this->db->insert($this->db->ESWAP_WANTPROD ,$info);
        //echo $this->db->last_query();
        $lastid = $this->db->insert_id();
        if ($lastid)
		{
			return $lastid;
        }
		else
        {
		    return false;    
        }
    }
  	public function update_eswap_product($info,$wh)
	{
		/*$this->db->update($this->db->ETRADE_PROD,$info,$wh);
		echo $this->db->last_query();*/
		if($this->db->update($this->db->ESWAP_PROD,$info,$wh))
			return true;
		else
			return false;
	}
	function update_eswap_wantproduct($info,$wh)
    {
        if($this->db->update($this->db->ESWAP_WANTPROD ,$info,$wh))
		{
			return true;
        }
		else
        {
		    return false;    
        }
    }
	public function delete_product($wh)
	{/*$this->db->delete($this->db->ETRADE_PROD,$wh);
		echo $this->db->last_query();*/
		if($this->db->delete($this->db->ESWAP_PROD,$wh))
			return true;
		else
			return false;
	}
	public function get_by_id($id) 
    {
		$sql = "SELECT * FROM {$this->db->ESWAP_PROD}  where id ='".$id."'";
		$query = $this->db->query($sql);# echo $this->db->last_query(); exit;
		$result_arr = $query->result_array();
		#pr($result_arr[0]);
		return $result_arr[0];
	}
	public function get_want_product_by_id($wh) 
    {
		$sql = "SELECT * FROM ".$this->db->ESWAP_WANTPROD."  where 1 $wh";
		$query = $this->db->query($sql); #echo $this->db->last_query(); exit;
		$result_arr = $query->result_array();
		#pr($result_arr[0]);
		return $result_arr;
	}
	
	public function get_eswap_product_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY esp.id DESC';
		
        $sql = "SELECT esp.* FROM {$this->db->ESWAP_PROD} AS esp, 
						{$this->db->ESWAP_WANTPROD} as espw ,
						{$this->db->TRADE_CAT} as c 
						WHERE 1 AND (espw.s_name LIKE CONCAT('%', esp.s_name ,'%')
								AND esp.i_category_id=c.id 
								OR esp.s_name LIKE CONCAT('%', espw.s_name ,'%')
								OR esp.i_category_id=espw.i_category_id 
								OR LOWER(espw.s_brand) = LOWER(esp.s_brand) )
								{$where} GROUP BY esp.id {$s_order_by} {$limit}";
      
        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		if(count($result_arr)){
			foreach($result_arr as $k=>$val){
				$wh = " AND r.i_rcv_prod_id = ".$val['id'];
				$result_arr[$k]['total_request'] =  $this->get_eswap_total_request($wh);
			}
		}
        return $result_arr;
    }
	
    public function get_eswap_product_list_count($where='')
    {
        $sql    = "SELECT COUNT(*) as i_total FROM ( SELECT esp.id
						FROM {$this->db->ESWAP_PROD} AS esp, 
						{$this->db->ESWAP_WANTPROD} as espw ,
						{$this->db->TRADE_CAT} as c 
						WHERE 1 AND (espw.s_name LIKE CONCAT('%', esp.s_name ,'%')
								AND esp.i_category_id=c.id 
								OR esp.i_category_id=espw.i_category_id 
								OR LOWER(espw.s_brand) = LOWER(esp.s_brand) 
								OR espw.s_brand LIKE CONCAT('%',esp.s_brand,'%') ) {$where} GROUP BY esp.id ) as drvd_tbl ";
								
        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0]['i_total'];
    }
	
	public function get_eswap_my_product_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY p.id DESC';
		
        $sql = "SELECT p.*,c.s_category_name FROM {$this->db->ESWAP_PROD} AS p, {$this->db->TRADE_CAT} as c 
				WHERE 1 AND p.i_category_id=c.id {$where} {$s_order_by} {$limit}";
      
        $query     = $this->db->query($sql); //echo $this->db->last_query();exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);
        return $result_arr;
    }
	
    public function get_eswap_my_product_list_count($where='')
    {
        $sql    = "SELECT count(*) as i_total FROM {$this->db->ESWAP_PROD} p, {$this->db->TRADE_CAT} as c  WHERE 1 AND p.i_category_id=c.id {$where} ";
        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0]['i_total'];
    }
	
	public function get_my_product_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY p.id DESC';
		
        $sql = "SELECT p.*,c.s_category_name FROM {$this->db->ESWAP_PROD} AS p, {$this->db->TRADE_CAT} as c 
				WHERE 1 AND p.i_category_id=c.id {$where} {$s_order_by} {$limit}";
      
        $query     = $this->db->query($sql); //echo $this->db->last_query();exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);
        return $result_arr;
    }
	
    public function get_my_product_list_count($where='')
    {
        $sql    = "SELECT count(*) as i_total FROM {$this->db->ESWAP_PROD} p, {$this->db->TRADE_CAT} as c  WHERE 1 AND p.i_category_id=c.id {$where} ";
        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0]['i_total'];
    }
	
	public function insert_eswap_request($info)
	{
		/*$this->db->insert($this->db->ETRADE_REQUEST,$info);
		echo $this->db->last_query();*/
		if($this->db->insert($this->db->ESWAP_REQ,$info))
			return $this->db->insert_id();
		else
			return false;
	}
	
	public function update_eswap_request($info,$wh)
	{
		if($this->db->update($this->db->ESWAP_REQ,$info,$wh))
			return true;
		else
			return false;
	}
	
	public function get_eswap_request_product_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY p.id DESC';
		
        $sql = "SELECT DISTINCT p.*
				FROM {$this->db->ESWAP_PROD} AS p, {$this->db->ESWAP_REQ} as r 
				WHERE 1 AND p.id=r.i_rcv_prod_id 
				{$where} {$s_order_by} {$limit}";
      
        $query     = $this->db->query($sql); //echo $this->db->last_query();exit;
        $result_arr = $query->result_array(); //pr($result_arr);
		
		return $result_arr;
    }
	
		
	public function get_my_eswap_request_product_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY p.id DESC';
		
        $sql = "SELECT DISTINCT p.*
				FROM {$this->db->ESWAP_PROD} AS p, {$this->db->ESWAP_REQ} as r 
				WHERE 1 AND p.id=r.i_req_prod_id 
				{$where} {$s_order_by} {$limit}";
      
        $query     = $this->db->query($sql); #echo $this->db->last_query();exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		return $result_arr;
    }
	
    
	

	
	public function get_eswap_request_sent_product_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY p.id DESC';
		
        $sql = "SELECT 	r.id as request_id,
						p.*,
						r.*,p1.id AS rcv_product_id, p1.s_name AS rcv_product_name
						FROM {$this->db->ESWAP_PROD} AS p LEFT JOIN {$this->db->ESWAP_REQ} as r 
							ON p.id=r.i_req_prod_id , {$this->db->ESWAP_PROD} AS p1,
							 {$this->db->USERS} AS u
						WHERE 1 AND r.i_req_user_id=u.id AND r.i_rcv_prod_id=p1.id
						{$where} {$s_order_by} {$limit}";
      
        $query     = $this->db->query($sql); //echo ' @@@@ '.$this->db->last_query(); exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		return $result_arr;
    }
	
	public function get_eswap_request_sent_product_list_count($where='')
    {
       
        $sql    = "SELECT count(*) as i_total FROM
						( SELECT p.id
						 FROM {$this->db->ESWAP_PROD} AS p LEFT JOIN {$this->db->ESWAP_REQ} as r 
							ON p.id=r.i_req_prod_id, {$this->db->ESWAP_PROD} AS p1,
							 {$this->db->USERS} AS u
						WHERE 1 AND r.i_req_user_id=u.id AND r.i_rcv_prod_id=p1.id {$where} group by p.id) as drvd_tbl";
        $query     = $this->db->query($sql); //echo $this->db->last_query();exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);
        return $result_arr[0]['i_total'];
    }
	
	
	
	
	public function get_eswap_request_recieved_product_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY p.id DESC';
		
        $sql = "SELECT 
						r.id as request_id, 
						r.i_accept,
						p.id as pid,
						p.i_user_id,
						p.i_category_id,
						p.s_name,
						p.s_attribute1,
						p.s_attribute2,
						p.s_brand,
						p.s_description,
						p.s_image,
						p.i_open_to_offer_for_local,
						p.dt_insert_time,
						p.dt_update_time,
						p.i_buy_request,
						p.s_product_age,
						p.product_id,
						p1.id AS rcv_product_id, 
						p1.s_name AS rcv_product_name 
						FROM cg_eswap_product AS p LEFT JOIN cg_eswap_request as r ON p.id=r.i_rcv_prod_id 
						, cg_eswap_product AS p1, cg_users AS u WHERE 1 AND r.i_rcv_user_id=u.id 
						AND r.i_req_prod_id=p1.id {$where} GROUP BY pid {$s_order_by} 
						 {$limit}";
      
        $query     = $this->db->query($sql); //echo ' @@@@ '.$this->db->last_query(); exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);
		if(count($result_arr)){
			foreach($result_arr as $k=> $val){
				
				$wh = " AND p.id = ".$val['pid'];
				$result_arr[$k]['recv_req_arr'] = $this->get_swap_requests($wh);
			}
		}
		return $result_arr;
    }
	
	public function get_eswap_request_recieved_product_list_count($where='')
    {
       
        $sql    = "SELECT count(*) as i_total FROM
						( SELECT 
								p.id
								FROM cg_eswap_product AS p LEFT JOIN cg_eswap_request as r ON p.id=r.i_rcv_prod_id 
								, cg_eswap_product AS p1, cg_users AS u  WHERE 1 AND r.i_rcv_user_id=u.id 
														AND r.i_req_prod_id=p1.id {$where} group by p.id) as drvd_tbl";
        $query     = $this->db->query($sql); #echo $this->db->last_query();exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);
        return $result_arr[0]['i_total'];
    }
	
	
	
	
    public function get_duplicate_req_check($where='')
    {
        $sql    = "SELECT count(*) as i_total FROM {$this->db->ESWAP_REQ} 
						WHERE 1 {$where} ";
        $query     = $this->db->query($sql); #echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0]['i_total'];
    }
	
	function get_swap_requests($where)
	{
		
		$sql = " SELECT p.id AS pid,
						r.s_email AS req_email,
						r.s_phone,r.dt_req_date,
						r.i_accept ,
						r.id AS reqid,
						r.i_req_user_id AS requid,
						r.i_req_prod_id AS reqpid,
						r.dt_accept,
						u.s_email,
						u.s_first_name,
						u.s_last_name,
						u.s_moblie_no
					FROM cg_eswap_product AS p, cg_eswap_request as r ,
						 cg_users AS u
					WHERE 1 AND p.id=r.i_rcv_prod_id AND r.i_req_user_id=u.id
					{$where} ";
		  
			$query     = $this->db->query($sql); //echo $this->db->last_query();exit;
			$result_arr = $query->result_array(); //pr($result_arr,1);
			
			return $result_arr;
	}
	
	public function get_eswap_total_request($where='')
    {
		
        $sql = "SELECT count(*) as total_count FROM (SELECT r.id
										FROM {$this->db->ESWAP_PROD} AS p, {$this->db->ESWAP_REQ} as r 
										WHERE 1 AND p.id=r.i_rcv_prod_id 
										{$where}) as drvd_tbl";
      
        $query     = $this->db->query($sql);// echo $this->db->last_query();exit;
        $result_arr = $query->result_array();
		return $result_arr[0]['total_count'];
    }
	
	
	
	public function get_all_eswap_request_product_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY p.id DESC';
		
        $sql = "SELECT 	r.id as request_id,
						r.s_email AS req_email,
						r.s_phone,r.dt_req_date,
						r.i_accept ,
						r.id AS reqid,
						r.i_req_user_id AS requid,
						r.i_req_prod_id AS reqpid,
						r.i_rcv_user_id AS rcvuid,
						r.i_rcv_prod_id AS rcvpid,
						r.dt_accept,
						r.i_isenabled,
						u.s_email,
						u.s_first_name,
						u.s_last_name,
						u.s_moblie_no,
						p.id as req_product_id,
						p.i_category_id as i_category_id,
						p.s_name AS req_product_name,
						p.s_brand,
						p1.id AS rcv_product_id, 
						p1.s_name AS rcv_product_name
						FROM {$this->db->ESWAP_PROD} AS p LEFT JOIN {$this->db->ESWAP_REQ} as r 
							ON p.id=r.i_req_prod_id , {$this->db->ESWAP_PROD} AS p1,
							 {$this->db->USERS} AS u,
							 {$this->db->TRADE_CAT} as c 
						WHERE 1 AND c.id = p.i_category_id  AND r.i_req_user_id=u.id AND r.i_rcv_prod_id=p1.id
						{$where} {$s_order_by} {$limit}";
      
        $query     = $this->db->query($sql); //echo ' @@@@ '.$this->db->last_query(); exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		return $result_arr;
    }
	
	public function get_all_eswap_request_product_list_count($where='')
    {
       
        $sql    = "SELECT count(*) as i_total FROM
						( SELECT r.id
						 FROM {$this->db->ESWAP_PROD} AS p LEFT JOIN {$this->db->ESWAP_REQ} as r 
							ON p.id=r.i_req_prod_id, {$this->db->ESWAP_PROD} AS p1,
							 {$this->db->USERS} AS u,
							  {$this->db->TRADE_CAT} as c 
						WHERE 1 AND c.id = p.i_category_id AND r.i_req_user_id=u.id AND r.i_rcv_prod_id=p1.id {$where} group by r.id) as drvd_tbl";
        $query     = $this->db->query($sql); //echo $this->db->last_query();exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);
        return $result_arr[0]['i_total'];
    }
	
	
	public function delete_products_and_requests($prod_id){
		
		$SQL = "DELETE FROM {$this->db->ESWAP_REQ} WHERE i_rcv_prod_id = {$prod_id} OR  i_req_prod_id = {$prod_id} ";
		$query     = $this->db->query($SQL);
		
		$SQL = "DELETE FROM {$this->db->ESWAP_PROD} WHERE id = {$prod_id}";
		$query     = $this->db->query($SQL);
		
		return true;
		
	}
	
	public function delete_requests($req_id){
		
		$SQL = "DELETE FROM {$this->db->ESWAP_REQ} WHERE id = {$req_id}";
		$query     = $this->db->query($SQL);
		return true;
	}
	
	
	public function get_all_eswap_product_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY esp.id DESC';
		
        $sql = "SELECT esp.* FROM {$this->db->ESWAP_PROD} AS esp, 
						{$this->db->ESWAP_WANTPROD} as espw ,
						{$this->db->TRADE_CAT} as c 
						WHERE 1 AND c.id = esp.i_category_id 
					   {$where} GROUP BY esp.id {$s_order_by} {$limit}";
      
        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		if(count($result_arr)){
			foreach($result_arr as $k=>$val){
				$wh = " AND r.i_rcv_prod_id = ".$val['id'];
				$result_arr[$k]['total_request'] =  $this->get_eswap_total_request($wh);
			}
		}
        return $result_arr;
    }
	
    public function get_all_eswap_product_list_count($where='')
    {
        $sql    = "SELECT COUNT(*) as i_total FROM ( SELECT esp.id
						FROM {$this->db->ESWAP_PROD} AS esp, 
						{$this->db->ESWAP_WANTPROD} as espw ,
						{$this->db->TRADE_CAT} as c 
						WHERE 1  AND c.id = esp.i_category_id {$where} GROUP BY esp.id ) as drvd_tbl ";
								
        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0]['i_total'];
    }
	
	public function get_my_trade_activities_list($recv_where='', $snt_where ='' ,$i_start=null,$i_limit=null,$s_order_by=''){
		$limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY request_id DESC';
		
		
		###
		# all req = my req + req rec
		###
		
         $sql = " (SELECT 	r.id as request_id,
		 					r.i_req_user_id,
							r.i_rcv_user_id,
							r.i_accept,
							p.id as pid,
							p.i_user_id,
							p.i_category_id,
							p.s_name,
							p.s_attribute1,
							p.s_attribute2,
							p.s_brand,
							p.s_description,
							p.s_image,
							p.i_open_to_offer_for_local,
							p.dt_insert_time,
							p.dt_update_time,
							p.i_buy_request,
							p.s_product_age,
							p.product_id,
							p1.id AS rcv_product_id, 
							p1.s_name AS rcv_product_name,
							'Yes' as 'my_bid'
							FROM {$this->db->ESWAP_PROD} AS p LEFT JOIN {$this->db->ESWAP_REQ} as r 
							ON p.id=r.i_req_prod_id , {$this->db->ESWAP_PROD} AS p1,
							 {$this->db->USERS} AS u
							WHERE 1 AND r.i_req_user_id=u.id AND r.i_rcv_prod_id=p1.id
							{$snt_where} group by request_id)
						 UNION
						 (SELECT 
							r.id as request_id,
							r.i_req_user_id,
							r.i_rcv_user_id, 
							r.i_accept,
							p.id as pid,
							p.i_user_id,
							p.i_category_id,
							p.s_name,
							p.s_attribute1,
							p.s_attribute2,
							p.s_brand,
							p.s_description,
							p.s_image,
							p.i_open_to_offer_for_local,
							p.dt_insert_time,
							p.dt_update_time,
							p.i_buy_request,
							p.s_product_age,
							p.product_id,
							p1.id AS rcv_product_id, 
							p1.s_name AS rcv_product_name,
							'No' as 'my_bid' 
							FROM cg_eswap_product AS p LEFT JOIN cg_eswap_request as r ON p.id=r.i_rcv_prod_id 
							, cg_eswap_product AS p1, cg_users AS u WHERE 1 AND r.i_rcv_user_id=u.id 
							AND r.i_req_prod_id=p1.id {$recv_where} group by request_id)
						{$s_order_by}
						 {$limit}";
						 
						//echo nl2br($sql);
						
        $query     = $this->db->query($sql); //echo ' @@@@ '.$this->db->last_query(); //exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		return $result_arr;
	}
	
	public function get_my_trade_activities_list_count($recv_where='', $snt_where='')
    {
       
    
		###
		# all req = my req + req rec
		###
         $sql = " SELECT count(*) as i_total FROM ((SELECT 	r.id as request_id
									FROM {$this->db->ESWAP_PROD} AS p LEFT JOIN {$this->db->ESWAP_REQ} as r 
							ON p.id=r.i_req_prod_id , {$this->db->ESWAP_PROD} AS p1,
							 {$this->db->USERS} AS u
							WHERE 1 AND r.i_req_user_id=u.id AND r.i_rcv_prod_id=p1.id
							{$snt_where} group by request_id)
						 UNION
						 (SELECT 
							r.id as request_id
							FROM cg_eswap_product AS p LEFT JOIN cg_eswap_request as r ON p.id=r.i_rcv_prod_id 
							, cg_eswap_product AS p1, cg_users AS u WHERE 1 AND r.i_rcv_user_id=u.id 
							AND r.i_req_prod_id=p1.id {$recv_where} group by request_id)
						    )drvd_tbl";
        $query     = $this->db->query($sql); //echo $this->db->last_query();exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);
        return $result_arr[0]['i_total'];
    }
	
	
    public function __destruct()
    {}   




}   // end of class definition...
