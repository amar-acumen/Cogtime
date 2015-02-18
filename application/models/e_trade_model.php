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
class E_trade_model extends Base_model 
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

    
    function save_etrade_product($info)
    {
        $res = $this->db->insert($this->db->ETRADE_PROD ,$info);
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
  	public function update_etrade_product($info,$wh)
	{
		/*$this->db->update($this->db->ETRADE_PROD,$info,$wh);
		echo $this->db->last_query();*/
		if($this->db->update($this->db->ETRADE_PROD,$info,$wh))
			return true;
		else
			return false;
	}
	
	public function delete_product($wh)
	{/*$this->db->delete($this->db->ETRADE_PROD,$wh);
		echo $this->db->last_query();*/
		if($this->db->delete($this->db->ETRADE_PROD,$wh))
			return true;
		else
			return false;
	}
	public function get_by_id($id) 
    {
		$sql = sprintf('SELECT * FROM '.$this->db->ETRADE_PROD.'  where id = %s',  $id);
		$query = $this->db->query($sql); #echo $this->db->last_query(); exit;
		$result_arr = $query->result_array();
		#pr($result_arr[0]);
		return $result_arr[0];
	}
	
	public function get_request_by_id($id) 
    {
		$sql = sprintf('SELECT * FROM '.$this->db->ETRADE_REQUEST.'  where id = %s',  $id);
		$query = $this->db->query($sql); #echo $this->db->last_query(); exit;
		$result_arr = $query->result_array();
		#pr($result_arr[0]);
		return $result_arr[0];
	}
	
	
	public function get_etrade_product_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY p.id DESC';
		
        $sql = "SELECT p.*,c.s_category_name FROM {$this->db->ETRADE_PROD} AS p, {$this->db->TRADE_CAT} as c 
				WHERE 1 AND p.i_category_id=c.id {$where} {$s_order_by} {$limit}";
      
        $query     = $this->db->query($sql); 
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		
        return $result_arr;
    }
	
    public function get_etrade_product_list_count($where='')
    {
       
        $sql    = "SELECT count(*) as i_total FROM {$this->db->ETRADE_PROD} p, {$this->db->TRADE_CAT} as c  WHERE 1 AND p.i_category_id=c.id {$where} ";
        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0]['i_total'];
    }
	
	public function insert_etrade_request($info)
	{
		/*$this->db->insert($this->db->ETRADE_REQUEST,$info);
		echo $this->db->last_query();*/
		if($this->db->insert($this->db->ETRADE_REQUEST,$info))
			return $this->db->insert_id();
		else
			return false;
	}
	
	public function update_etrade_request($info,$wh)
	{
		/*$this->db->update($this->db->ETRADE_REQUEST,$info,$wh);
		echo $this->db->last_query();*/
		if($this->db->update($this->db->ETRADE_REQUEST,$info,$wh))
			return true;
		else
			return false;
	}
	
	public function get_etrade_request_product_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY p.id DESC';
		
        $sql = "SELECT DISTINCT p.*
				FROM {$this->db->ETRADE_PROD} AS p, {$this->db->ETRADE_REQUEST} as r 
				WHERE 1 AND p.id=r.i_etrade_prod_id 
				{$where} {$s_order_by} {$limit}";
      
        $query     = $this->db->query($sql); //echo $this->db->last_query();exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		return $result_arr;
    }
	
    public function get_etrade_request_product_list_count($where='')
    {
       
        $sql    = "SELECT count(DISTINCT p.id) as i_total FROM {$this->db->ETRADE_PROD} AS p, {$this->db->ETRADE_REQUEST} as r 
				WHERE 1 AND p.id=r.i_etrade_prod_id {$where} ";
        $query     = $this->db->query($sql); #echo $this->db->last_query();exit;
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0]['i_total'];
    }
	

	
	public function get_etrade_request_sent_product_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY p.id DESC';
		
        $sql = "SELECT p.*,r.i_qty AS req_qty,r.s_email AS req_email,r.s_phone,r.dt_req_date,
						r.i_accept ,r.id AS reqid,r.i_user_id AS requid,r.f_rate_for_buyer AS f_rate_for_buyer,
						r.f_rate_for_seller_item AS f_rate_for_seller_item, 
						r.f_rate_for_seller_communication AS f_rate_for_seller_communication, 
						r.f_rate_for_seller_time AS f_rate_for_seller_time,
						u.s_email,u.s_first_name,u.s_last_name,u.s_moblie_no
						FROM {$this->db->ETRADE_PROD} AS p, {$this->db->ETRADE_REQUEST} as r ,
							 {$this->db->USERS} AS u
						WHERE 1 AND p.id=r.i_etrade_prod_id  AND r.i_user_id=u.id
						{$where} {$s_order_by} {$limit}";
      
        $query     = $this->db->query($sql); //echo $this->db->last_query();exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		return $result_arr;
    }
	
    public function get_etrade_request_sent_product_list_count($where='')
    {
       
        $sql    = "SELECT count(*) as i_total FROM {$this->db->ETRADE_PROD} AS p, {$this->db->ETRADE_REQUEST} as r ,
							 {$this->db->USERS} AS u
						WHERE 1 AND p.id=r.i_etrade_prod_id  AND r.i_user_id=u.id
						{$where} ";
        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0]['i_total'];
    }
	
	public function delete_products_and_requests($prod_id){
		
		$SQL = "DELETE FROM {$this->db->ETRADE_REQUEST} WHERE i_etrade_prod_id = {$prod_id}";
		$query     = $this->db->query($SQL);
		
		$SQL = "DELETE FROM {$this->db->ETRADE_PROD} WHERE id = {$prod_id}";
		$query     = $this->db->query($SQL);
		
		return true;
		
	}
	
	public function delete_requests($req_id){
		
		$SQL = "DELETE FROM {$this->db->ETRADE_REQUEST} WHERE id = {$req_id}";
		$query     = $this->db->query($SQL);
		return true;
	}
	
	
	public function get_etradeProductReq_list($where='',$i_start=null,$i_limit=null,$s_order_by=''){
     $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
	 $s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY r.id DESC';
		
       $sql = "SELECT p.*,
					r.i_etrade_prod_id,
					r.s_phone,
					r.s_email,
					r.i_user_id as req_userid ,
					r.dt_req_date,
					r.i_accept,
					r.f_rate_for_buyer,
					r.f_rate_for_seller_item,
					r.f_rate_for_seller_communication,
					r.f_rate_for_seller_time,
					r.is_shipped,
					r.i_isenabled as r_i_isenabled,
					r.i_qty,
					r.id as request_id
				FROM {$this->db->ETRADE_PROD} AS p, {$this->db->ETRADE_REQUEST} as r 
				, {$this->db->TRADE_CAT} as c 
				WHERE 1 AND p.i_category_id=c.id  AND p.id=r.i_etrade_prod_id 
				{$where} GROUP BY r.id {$s_order_by} {$limit}";
      
        $query     = $this->db->query($sql); #echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		return $result_arr;
	}
	
	
	public function get_etradeProductReq_list_count($where){
       $sql = "SELECT COUNT(*) FROM(
	   				SELECT r.id 
				FROM {$this->db->ETRADE_PROD} AS p, {$this->db->ETRADE_REQUEST} as r , {$this->db->TRADE_CAT} as c 
				WHERE 1 AND p.i_category_id=c.id  AND p.id=r.i_etrade_prod_id 
				{$where} GROUP BY r.id ) as drvd_tbl";
      
        $query     = $this->db->query($sql); //echo $this->db->last_query();exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		return $result_arr[0]['count'];
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
						r.i_qty,
						r.dt_req_date,
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
						p.product_id,
						p.f_local_shipping_cost,
						p.f_international_shipping_cost,
					    'No' as 'my_bid'
						
						FROM {$this->db->ETRADE_PROD} AS p LEFT JOIN {$this->db->ETRADE_REQUEST} as r 
							 ON p.id=r.i_etrade_prod_id ,
							 {$this->db->USERS} AS u
						WHERE 1 AND r.i_user_id=u.id AND r.i_etrade_prod_id=p.id
						{$recv_where} )
						 UNION
					 (SELECT
						r.id as request_id, 
						r.i_accept,
						r.i_qty,
						r.dt_req_date,
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
						p.product_id,
						p.f_local_shipping_cost,
						p.f_international_shipping_cost,
						'Yes' as 'my_bid'
						FROM {$this->db->ETRADE_PROD} AS p LEFT JOIN {$this->db->ETRADE_REQUEST} as r 
							 ON p.id=r.i_etrade_prod_id ,
							 {$this->db->USERS} AS u
						WHERE 1 AND r.i_user_id=u.id AND r.i_etrade_prod_id=p.id
						{$snt_where} 
						
						 )
						{$s_order_by}
						 {$limit}";
						 
						// echo nl2br($sql); exit;
						
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
						FROM {$this->db->ETRADE_PROD} AS p LEFT JOIN {$this->db->ETRADE_REQUEST} as r 
							 ON p.id=r.i_etrade_prod_id ,
							 {$this->db->USERS} AS u
						WHERE 1 AND r.i_user_id=u.id AND r.i_etrade_prod_id=p.id
						{$recv_where} group by r.id )
						 UNION
					 (SELECT
						r.id 
						FROM {$this->db->ETRADE_PROD} AS p LEFT JOIN {$this->db->ETRADE_REQUEST} as r 
							 ON p.id=r.i_etrade_prod_id ,
							 {$this->db->USERS} AS u
						WHERE 1 AND r.i_user_id=u.id AND r.i_etrade_prod_id=p.id
						{$snt_where} group by r.id
						 ) )drvd_tbl";
        $query     = $this->db->query($sql); //echo $this->db->last_query();exit;
        $result_arr = $query->result_array(); //pr($result_arr,1);
        return $result_arr[0]['i_total'];
    }
	
	
	
	
	
	############# purchase product credits ####################
	
	public function insert_purchase_credits_details($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->purchase_credit_history, $arr); //echo $this->db->last_query();
		return $this->db->insert_id();
	}
	
	
	public function update_purchase_credits_details($arr=array(),$id) {
		  if(count($arr)==0) {
			  return null;
		  }
		  $this->db->update($this->db->purchase_credit_history, $arr, array('id'=>$id)); //echo $this->db->last_query();
     }
	
	
	public function insert_credits($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->user_credits, $arr); //echo $this->db->last_query();
		return $this->db->insert_id();
	}
	
	
	public function update_credits($arr=array(),$id) {
		  if(count($arr)==0) {
			  return null;
		  }
		  $this->db->update($this->db->user_credits, $arr, array('i_user_id'=>$id)); //echo $this->db->last_query();
    }
	
	public function checkTotalCredits($user_id){
		
		$sql  = "SELECT sum(f_amount) as total_amount FROM cg_user_credits where i_user_id = {$user_id} ";
		$query     = $this->db->query($sql); 
        $result_arr = $query->result_array();
		return $result_arr[0]['total_amount'];
	}
	
	public function checkUser($user_id){
		
		$sql  = "SELECT count(*) as tcount FROM cg_user_credits where i_user_id = {$user_id} ";
		$query     = $this->db->query($sql); 
        $result_arr = $query->result_array();
		return $result_arr[0]['tcount'];
	}
	
	
	############# purchase product credits ####################
	##############################################
	##############################################
	
	public function getCreditPurchaselist($where='',$i_start=null,$i_limit=null,$s_order_by='1')
    {
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		
        $sql  = " SELECT  P.*,
						  CONCAT(U.s_first_name,' ',U.s_last_name) as s_profile_name
						  FROM {$this->db->purchase_credit_history} P
						  LEFT JOIN {$this->db->USERS} U ON P.i_user_id = U.id
						  WHERE 1 
						  {$where} 
						  ORDER BY P.id DESC {$limit}";

        $query     = $this->db->query($sql); 
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		
		
        return $result_arr;
    }
	
	
	
	public function getCreditPurchaseCount($where='')
    {
        $sql    = "     SELECT count(*) as i_total FROM
							( SELECT  P.*,
							  CONCAT(U.s_first_name,' ',U.s_last_name) as s_profile_name
							  FROM {$this->db->purchase_credit_history} P
							  LEFT JOIN {$this->db->USERS} U ON P.i_user_id = U.id
					    	  WHERE 1	{$where}
								
							) as tbl  ";
					 
        $query     = $this->db->query($sql);
        $result_arr = $query->result_array();
        return $result_arr[0]['i_total'];
    }
	
	
	public function deletePurchaseCredit($id){
		
		$SQL = "DELETE FROM {$this->db->purchase_credit_history} WHERE id = {$id}";
		$query     = $this->db->query($SQL);
		return true;
	}
	
	
    public function __destruct()
    {}   




}   // end of class definition...
