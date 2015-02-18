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
class E_freebie_model extends Base_model 
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

    
    function save_efreebie_product($info)
    {
        $res = $this->db->insert($this->db->EFREE_PROD ,$info);
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
	
  	public function update_efreebie_product($info,$wh)
	{
		/*$this->db->update($this->db->ETRADE_PROD,$info,$wh);
		echo $this->db->last_query();*/
		if($this->db->update($this->db->EFREE_PROD,$info,$wh))
			return true;
		else
			return false;
	}
	
	public function delete_product($wh)
	{/*$this->db->delete($this->db->ETRADE_PROD,$wh);
		echo $this->db->last_query();*/
		if($this->db->delete($this->db->EFREE_PROD,$wh))
			return true;
		else
			return false;
	}
	public function get_by_id($id) 
    {
		$sql = "SELECT * FROM {$this->db->EFREE_PROD}  where id ='".$id."'";
		$query = $this->db->query($sql);# echo $this->db->last_query(); exit;
		$result_arr = $query->result_array();
		#pr($result_arr[0]);
		return $result_arr[0];
	}
	
	
	public function get_efreebie_product_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY esp.id DESC';
		
        $sql = "SELECT esp.* FROM {$this->db->EFREE_PROD} AS esp, 
						          {$this->db->TRADE_CAT} as c 
						        WHERE 1
								AND esp.i_category_id = c.id
								{$where} GROUP BY esp.id {$s_order_by} {$limit}";
      
        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		if(count($result_arr)){
			foreach($result_arr as $k=>$val){
				$wh = " AND r.i_freebie_prod_id = ".$val['id'];
				$result_arr[$k]['total_request'] =  $this->get_efreebie_total_request($wh);
			}
		}
        return $result_arr;
    }
	
    public function get_efreebie_product_list_count($where='')
    {
        $sql    = "SELECT COUNT(*) as i_total FROM ( SELECT esp.id
	   									 FROM
										{$this->db->EFREE_PROD} AS esp, 
										{$this->db->TRADE_CAT} as c 
										WHERE 1 
										AND esp.i_category_id = c.id
										{$where} GROUP BY esp.id  ) as drvd_tbl "; 
								
        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0]['i_total'];
    }
	
	public function get_efreebie_my_product_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY p.id DESC';
		
        $sql = "SELECT p.*,c.s_category_name FROM {$this->db->EFREE_PROD} AS p, {$this->db->TRADE_CAT} as c 
				WHERE 1 AND p.i_category_id=c.id {$where} {$s_order_by} {$limit}";
      
        $query     = $this->db->query($sql); //echo $this->db->last_query();exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);
        return $result_arr;
    }
	
    public function get_efreebie_my_product_list_count($where='')
    {
        $sql    = "SELECT count(*) as i_total FROM {$this->db->EFREE_PROD} p, {$this->db->TRADE_CAT} as c  
					WHERE 1 AND p.i_category_id=c.id {$where} ";
        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0]['i_total'];
    }
	
	public function get_my_product_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY p.id DESC';
		
        $sql = "SELECT p.*,c.s_category_name FROM {$this->db->EFREE_PROD} AS p, {$this->db->TRADE_CAT} as c 
				WHERE 1 AND p.i_category_id=c.id {$where} {$s_order_by} {$limit}";
      
        $query     = $this->db->query($sql); //echo $this->db->last_query();exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);
        return $result_arr;
    }
	
    public function get_my_product_list_count($where='')
    {
        $sql    = "SELECT count(*) as i_total FROM {$this->db->EFREE_PROD} p, {$this->db->TRADE_CAT} as c  WHERE 1 AND p.i_category_id=c.id {$where} ";
        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0]['i_total'];
    }
	
	public function insert_efreebie_request($info)
	{
		/*$this->db->insert($this->db->ETRADE_REQUEST,$info);
		echo $this->db->last_query();*/
		if($this->db->insert($this->db->EFREE_REQ,$info))
			return $this->db->insert_id();
		else
			return false;
	}
	
	public function update_efreebie_request($info,$wh)
	{
		if($this->db->update($this->db->EFREE_REQ,$info,$wh))
			return true;
		else
			return false;
	}
	
	public function get_efreebie_request_product_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY p.id DESC';
		
        $sql = "SELECT DISTINCT p.*
				FROM {$this->db->EFREE_PROD} AS p, {$this->db->EFREE_REQ} as r 
				WHERE 1 AND p.id=r.i_freebie_prod_id 
				{$where} {$s_order_by} {$limit}";
      
        $query     = $this->db->query($sql); //echo $this->db->last_query();exit;
        $result_arr = $query->result_array(); //pr($result_arr);
		
		return $result_arr;
    }
	
		
	public function get_my_efreebie_request_product_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY p.id DESC';
		
        $sql = "SELECT DISTINCT p.*
				FROM {$this->db->EFREE_PROD} AS p, {$this->db->EFREE_REQ} as r 
				WHERE 1 AND p.id=r.i_req_prod_id 
				{$where} {$s_order_by} {$limit}";
      
        $query     = $this->db->query($sql); #echo $this->db->last_query();exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		return $result_arr;
    }
	
    
	

	
	public function get_efreebie_request_sent_product_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
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
						p.s_product_age,
						p.product_id,
						p.f_local_shipping_cost,
						p.f_international_shipping_cost
						
						FROM {$this->db->EFREE_PROD} AS p LEFT JOIN {$this->db->EFREE_REQ} as r 
							 ON p.id=r.i_freebie_prod_id ,
							 {$this->db->USERS} AS u
						WHERE 1 AND r.i_user_id=u.id AND r.i_freebie_prod_id=p.id
						{$where} {$s_order_by} {$limit}";
						
						//echo nl2br($sql); exit;
      
        $query     = $this->db->query($sql);
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		
		return $result_arr;
    }
	
	public function get_efreebie_request_sent_product_list_count($where='')
    {
       
        $sql    = "SELECT count(*) as i_total FROM
						( SELECT r.id
							FROM {$this->db->EFREE_PROD} AS p LEFT JOIN {$this->db->EFREE_REQ} as r 
								 ON p.id=r.i_freebie_prod_id ,
								 {$this->db->USERS} AS u
							WHERE 1 AND r.i_user_id=u.id AND r.i_freebie_prod_id=p.id
						{$where} group by p.id) as drvd_tbl";
        $query     = $this->db->query($sql); //echo $this->db->last_query();exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);
        return $result_arr[0]['i_total'];
    }
	
	
	
	
	public function get_efreebie_request_recieved_product_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
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
						p.s_product_age,
						p.product_id,
						p.f_local_shipping_cost,
						p.f_international_shipping_cost
						FROM cg_efreebie_product AS p LEFT JOIN cg_efreebie_request as r ON p.id=r.i_freebie_prod_id 
						, cg_users AS u WHERE 1 AND r.i_user_id=u.id 
						{$where} group by p.id {$s_order_by} 
						 {$limit}";
      
        $query     = $this->db->query($sql); //echo ' @@@@ '.$this->db->last_query(); exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);
		if(count($result_arr)){
			foreach($result_arr as $k=> $val){
				
				$wh = " AND p.id = ".$val['pid'];
				$result_arr[$k]['recv_req_arr'] = $this->get_freebie_requests($wh);
			}
		}
		return $result_arr;
    }
	
	public function get_efreebie_request_recieved_product_list_count($where='')
    {
       
        $sql    = "SELECT count(*) as i_total FROM
						( SELECT 
								p.id
								FROM cg_efreebie_product AS p LEFT JOIN cg_efreebie_request as r ON p.id=r.i_freebie_prod_id
								, cg_users AS u  WHERE 1 AND r.i_user_id=u.id 
														 {$where} group by p.id) as drvd_tbl";
        $query     = $this->db->query($sql); #echo $this->db->last_query();exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);
        return $result_arr[0]['i_total'];
    }
	
	
	
	
    public function get_duplicate_req_check($where='')
    {
        $sql    = "SELECT count(*) as i_total FROM {$this->db->EFREE_REQ} 
						WHERE 1 {$where} ";
        $query     = $this->db->query($sql); #echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0]['i_total'];
    }
	
	function get_freebie_requests($where)
	{
		
		$sql = " SELECT p.id AS pid,
						r.s_email AS req_email,
						r.s_phone,r.dt_req_date,
						r.i_accept ,
						r.id AS reqid,
						r.i_user_id AS requid,
						r.i_freebie_prod_id AS reqpid,
						r.dt_accept,
						u.s_email,
						u.s_first_name,
						u.s_last_name,
						u.s_moblie_no
					FROM cg_efreebie_product AS p, cg_efreebie_request as r ,
						 cg_users AS u
					WHERE 1 AND p.id=r.i_freebie_prod_id AND r.i_user_id=u.id
					{$where} ";
		  
			$query     = $this->db->query($sql); //echo $this->db->last_query();exit;
			$result_arr = $query->result_array(); //pr($result_arr,1);
			
			return $result_arr;
	}
	
	public function get_efreebie_total_request($where='')
    {
		
       $sql = "SELECT count(*) as total_count FROM (SELECT r.id
										FROM {$this->db->EFREE_PROD} AS p, 
										{$this->db->EFREE_REQ} as r 
										WHERE 1 AND p.id=r.i_freebie_prod_id 
										{$where}) as drvd_tbl";
      
        $query     = $this->db->query($sql);// echo $this->db->last_query();exit;
        $result_arr = $query->result_array();
		return $result_arr[0]['total_count'];
    }
	
	
	
	public function get_all_efreebie_request_product_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY p.id DESC';
		
        $sql = "SELECT 	r.id as request_id,
						r.s_email AS req_email,
						r.s_phone,r.dt_req_date,
						r.i_accept ,
						r.id AS reqid,
						r.i_user_id AS requid,
						r.i_freebie_prod_id AS reqpid,
						p.i_user_id AS rcvuid,
						p.id AS rcvpid,
						r.dt_accept,
						r.i_isenabled,
						u.s_email,
						u.s_first_name,
						u.s_last_name,
						u.s_moblie_no,
						p.id as req_product_id,
						p.i_category_id as i_category_id,
						p.s_name AS req_product_name,
						p.s_brand
						FROM {$this->db->EFREE_PROD} AS p LEFT JOIN {$this->db->EFREE_REQ} as r 
							ON p.id=r.i_freebie_prod_id , 
							 {$this->db->USERS} AS u,
							 {$this->db->TRADE_CAT} as c 
						WHERE 1 AND c.id = p.i_category_id  AND r.i_user_id=u.id AND r.i_freebie_prod_id = p.id
						{$where} {$s_order_by} {$limit}";
      
        $query     = $this->db->query($sql); //echo ' @@@@ '.$this->db->last_query(); //exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		return $result_arr;
    }
	
	public function get_all_efreebie_request_product_list_count($where='')
    {
       
        $sql    = "SELECT count(*) as i_total FROM
						( SELECT r.id
						   FROM {$this->db->EFREE_PROD} AS p LEFT JOIN {$this->db->EFREE_REQ} as r 
							ON p.id=r.i_freebie_prod_id , 
							 {$this->db->USERS} AS u,
							{$this->db->TRADE_CAT} as c 
						  WHERE 1 AND c.id = p.i_category_id  AND r.i_user_id=u.id AND r.i_freebie_prod_id = p.id
						 {$where} group by r.id) as drvd_tbl";
        $query     = $this->db->query($sql); //echo $this->db->last_query();exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);
        return $result_arr[0]['i_total'];
    }
	
	
	public function delete_products_and_requests($prod_id){
		
		$SQL = "DELETE FROM {$this->db->EFREE_REQ} WHERE i_freebie_prod_id = {$prod_id} ";
		$query     = $this->db->query($SQL);
		
		$SQL = "DELETE FROM {$this->db->EFREE_PROD} WHERE id = {$prod_id}";
		$query     = $this->db->query($SQL);
		
		return true;
		
	}
	
	public function delete_requests($req_id){
		
		$SQL = "DELETE FROM {$this->db->EFREE_REQ} WHERE id = {$req_id}";
		$query     = $this->db->query($SQL);
		return true;
	}
	
	
	
	
	public function get_my_trade_activities_list($recv_where='', $snt_where ='' ,$i_start=null,$i_limit=null,$s_order_by=''){
		$limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY request_id DESC';
		
		
		###
		# all req = my req + req rec
		###
         $sql = " (SELECT
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
						p.s_product_age,
						p.product_id,
						p.f_local_shipping_cost,
						p.f_international_shipping_cost,
						'No' as 'my_bid'
						
						FROM {$this->db->EFREE_PROD} AS p LEFT JOIN {$this->db->EFREE_REQ} as r 
							 ON p.id=r.i_freebie_prod_id ,
							 {$this->db->USERS} AS u
						WHERE 1 AND r.i_user_id=u.id AND r.i_freebie_prod_id=p.id
						{$recv_where} )
						 UNION
					 (SELECT
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
						p.s_product_age,
						p.product_id,
						p.f_local_shipping_cost,
						p.f_international_shipping_cost,
						'Yes' as 'my_bid'
						
						FROM {$this->db->EFREE_PROD} AS p LEFT JOIN {$this->db->EFREE_REQ} as r 
							 ON p.id=r.i_freebie_prod_id ,
							 {$this->db->USERS} AS u
						WHERE 1 AND r.i_user_id=u.id AND r.i_freebie_prod_id=p.id
						{$snt_where} 
						
						 )
						{$s_order_by}
						 {$limit}";
						 
						// echo nl2br($sql);
						
        $query     = $this->db->query($sql); //echo ' @@@@ '.$this->db->last_query(); //exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		return $result_arr;
	}
	
	public function get_my_trade_activities_list_count($recv_where='', $snt_where='')
    {
       
    
		###
		# all req = my req + req rec
		###
         $sql = " SELECT count(*) as i_total FROM ( (SELECT
						r.id 
						FROM {$this->db->EFREE_PROD} AS p LEFT JOIN {$this->db->EFREE_REQ} as r 
							 ON p.id=r.i_freebie_prod_id ,
							 {$this->db->USERS} AS u
						WHERE 1 AND r.i_user_id=u.id AND r.i_freebie_prod_id=p.id
						{$recv_where} group by r.id )
						 UNION
					 (SELECT
						r.id 
						FROM {$this->db->EFREE_PROD} AS p LEFT JOIN {$this->db->EFREE_REQ} as r 
							 ON p.id=r.i_freebie_prod_id ,
							 {$this->db->USERS} AS u
						WHERE 1 AND r.i_user_id=u.id AND r.i_freebie_prod_id=p.id
						{$snt_where} group by r.id
						 ) )drvd_tbl";
        $query     = $this->db->query($sql); //echo $this->db->last_query();exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);
        return $result_arr[0]['i_total'];
    }
	
	
	
	
	
	
    public function __destruct()
    {}   




}   // end of class definition...
