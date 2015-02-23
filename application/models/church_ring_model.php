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
class Church_ring_model extends Base_model 
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

 /******
    * This method will fetch all records from the db. 
    * 
    * @param string $s_where, ex- " status=1 AND deleted=0 " 
    * @param int $i_start, starting value for pagination
    * @param int $i_limit, number of records to fetch used for pagination
    * @param string $s_order_by, Column names to be ordered ex- " dt_created_on desc,i_is_deleted asc,id asc "
    * @returns array
    */
    public function fetch_multi($s_where=null,$s_where1=null,$i_start=null,$i_limit=null,$s_order_by=null)
    {
	 
		
	}
    
	
    

    /*******
    * Fetches One record from db for the id value.
    * 
    * @param int $i_id
    * @returns array
    */
    public function fetch_this($i_id)
    {}            
        
    /***
    * Inserts new records into db. As we know the table name 
    * we will not pass it into params.
    * 
    * @param array $info, array of fields(as key) with values,ex-$arr["field_name"]=value
    * @returns $i_new_id  on success and FALSE if failed 
    */
    public function add_info($info)
    {}            

    /***
    * Update records in db. As we know the table name 
    * we will not pass it into params.
    * 
    * @param array $info, array of fields(as key) with values,ex-$arr["field_name"]=value
    * @param int $i_id, id value to be updated used in where clause
    * @returns $i_rows_affected  on success and FALSE if failed 
    */
    public function edit_info($info,$i_id)
    {
        
    }    
	  
    /******
    * Deletes all or single record from db. 
    * For Master entries deletion only change the flag i_is_deleted. 
    *
    * @param int $i_id, id value to be deleted used in where clause 
    * @returns $i_rows_affected  on success and FALSE if failed 
    * 
    */
    public function delete_info($i_id)
    {}      

    
    function add_ring($info,$info_inv)
    {
        $res = $this->db->insert($this->db->CHURCHRING ,$info);
       
        $lastid = $this->db->insert_id();
        if ($lastid)
		{
			foreach($info_inv as $val)
			{
				$add_inv_arr['i_ring_id']		= $lastid;
				$add_inv_arr['i_invited_id']	= $val;
                                $add_inv_arr['dt_request_date'] = get_db_datetime();
				$this->db->insert($this->db->CHURCHRING_INV_USER ,$add_inv_arr);
			}
			
            return $lastid;
        }
		else
        {
		    return false;    
        }
    }
    
    function add_invite_member($add_inv_arr)
    {
        $this->db->insert($this->db->RING_INV_USER ,$add_inv_arr);
        $last_id = $this->db->insert_id();
        return $last_id;
    }
    

    
	

/**********************add 29-01-2015******************************************************/
	
	
//	public function get_by_id($id) 
//    {
//		$sql = sprintf('SELECT * FROM '.$this->db->RING.'  where id = %s',  $id);
//		$query = $this->db->query($sql); #echo $this->db->last_query(); exit;
//		$result_arr = $query->result_array();
//		#pr($result_arr[0]);
//		return $result_arr[0];
//	}
	
public function get_by_id($id) 
    {
		$sql = sprintf('SELECT * FROM cg_church_ring  where id = %s',  $id);
		$query = $this->db->query($sql); //echo $this->db->last_query(); exit;
		$result_arr = $query->result_array();
		#pr($result_arr[0]);
		return $result_arr[0];
	}
	
  /**************************************************************************/  
    

   public function accept_invitation($where,$arr,$is_msg,$msg_arr)
   {
		//$this->db->where($where);
   		$this->db->update('cg_church_ring_invited_user',$arr,$where);
		 $this->db->last_query();
		if($is_msg	== 1)
			$this->db->update('messages',  array('i_ended'=>'1'),$msg_arr );
		#echo $this->db->last_query();
		#exit;
   }
   
	public function decline_invitation($where,$is_msg,$msg_arr)
	{
		$this->db->delete('cg_church_ring_invited_user',$where); 
		if($is_msg	== 1)
			$this->db->update('messages',  array('i_ended'=>'1'),$msg_arr );
	}
    
    /***************************add 29-01-2015********************************************/
//	public function show_ring_by_user($s_where=null,$s_where1=null,$i_start=null,$i_limit=null,$s_order_by=null)
//	{
//	try
//        {
//		  	$ret_=array();
//			
//			$language = get_current_language();
//			
//			$s_qry = "(SELECT r.*, r.id AS ringid, c.s_category_name AS s_category_name, CONCAT(u.s_first_name,' ',u.s_last_name) AS owner_name,
//							(SELECT COUNT(id) FROM {$this->db->USER_RING_POST} WHERE i_ring_id=r.id) AS post,
//							(SELECT COUNT(cm.id) FROM {$this->db->USER_RING_POST} AS po, {$this->db->USER_RING_POST_COMMENTS} AS cm WHERE po.id=cm.i_ring_post_id AND po.i_ring_id=r.id) AS cmt,
//							(SELECT COUNT(lk.id) FROM {$this->db->USER_RING_POST} AS po, {$this->db->USER_RING_POST_LIKE} AS lk WHERE po.id=lk.i_ring_post_id AND po.i_ring_id=r.id) AS lik
//							FROM {$this->db->RING} r LEFT JOIN {$this->db->USERS} AS u 
//								ON r.i_user_id=u.id , {$this->db->RING_CAT} c WHERE r.i_category_id=c.id AND r.i_isenabled=1 "
//						.$s_where.") UNION".
//						"(SELECT r.*, r.id AS ringid, c.s_category_name AS s_category_name, CONCAT(u.s_first_name,' ',u.s_last_name) AS owner_name,
//							(SELECT COUNT(id) FROM {$this->db->USER_RING_POST} WHERE i_ring_id=r.id) AS post,
//							(SELECT COUNT(cm.id) FROM {$this->db->USER_RING_POST} AS po, {$this->db->USER_RING_POST_COMMENTS} AS cm WHERE po.id=cm.i_ring_post_id AND po.i_ring_id=r.id) AS cmt,
//							(SELECT COUNT(lk.id) FROM {$this->db->USER_RING_POST} AS po, {$this->db->USER_RING_POST_LIKE} AS lk WHERE po.id=lk.i_ring_post_id AND po.i_ring_id=r.id) AS lik
//							FROM {$this->db->RING} r LEFT JOIN {$this->db->USERS} AS u 
//								ON r.i_user_id=u.id , {$this->db->RING_CAT} AS c ,{$this->db->RING_INV_USER} AS inv 
//								WHERE r.i_category_id=c.id AND r.i_isenabled=1 AND inv.i_ring_id=r.id AND inv.i_joined = 1 ".$s_where1.")"; 
//                
//          //////////For Pagination///////////*don't change*/
//          //$s_qry=str_replace("'","''",$s_qry);//for string operation in procedure
//          $s_qry= $s_qry.(trim($s_order_by)!=""?" ORDER BY ".$s_order_by."":"ORDER BY ringid DESC")." ".(is_numeric($i_start) && is_numeric($i_limit)?" LIMIT ".intval($i_start).",".intval($i_limit):"");
//		 
//		   #echo ($s_qry);exit;
//          //////////end For Pagination//////////                
//                
//          $this->db->trans_begin();///new                
//          $rs=$this->db->query($s_qry); 
//          $i_cnt=0;
//          if(is_array($rs->result()))
//          {
//              foreach($rs->result() as $row)
//              {
//                        $ret_[$i_cnt]["ringid"]				=	$row->ringid;////always integer
//						$ret_[$i_cnt]["s_ring_name"]		=	$row->s_ring_name;
//                        $ret_[$i_cnt]["s_description"]      =    $row->s_description;
//						$ret_[$i_cnt]["i_user_id"]		    =	$row->i_user_id;
//						$ret_[$i_cnt]["s_logo"]				=	$row->s_logo;
//						
//						$ret_[$i_cnt]["post_owner_user_id"]	=	$row->i_user_id;
//						$ret_[$i_cnt]["owner_name"]			=	$row->owner_name;
//						$ret_[$i_cnt]["i_privacy"]  	   	=	$row->i_privacy; 
//						
//						$ret_[$i_cnt]["i_member"]  	   		=	$row->i_member; 
//						$ret_[$i_cnt]["s_state"]  	   		=	get_unformatted_string($row->s_state); 
//						$ret_[$i_cnt]["dt_created_on"]		=	$row->dt_created_on;
//						$ret_[$i_cnt]["i_country_id"]		=	intval($row->i_country_id);
//						$ret_[$i_cnt]["dt_created_on"]		=	($row->dt_created_on);
//						$ret_[$i_cnt]["s_category_name"]	=	$row->s_category_name;
//						$ret_[$i_cnt]["post"]				=	$row->post;
//						$ret_[$i_cnt]["cmt"]				=	$row->cmt;
//						$ret_[$i_cnt]["lik"]				=	$row->lik;
//						
//						$ret_[$i_cnt]["i_sub_category_id"]		=	$row->i_sub_category_id;
//
//                  $i_cnt++;
//              }    
//              $rs->free_result();          
//          }
//          $this->db->trans_commit();///new
//		  
//		  					
//		  
//          unset($s_qry,$rs,$row,$i_cnt,$s_where,$i_start,$i_limit);
//          return $ret_;
//		    }
//			catch(Exception $err_obj)
//			{
//				show_error($err_obj->getMessage());
//			}           
//    
//    
//    }
//	
//	
//	public function gettotal_ring_by_user($s_where,$s_where1)
//    {
//        try
//        {
//          $ret_=0;
//         
//				
//		  $s_qry = "SELECT COUNT(tab.ringid) AS i_total FROM ((SELECT r.*, r.id AS ringid, c.s_category_name AS s_category_name, CONCAT(u.s_first_name,' ',u.s_last_name) AS owner_name
//							FROM {$this->db->RING} r LEFT JOIN {$this->db->USERS} AS u 
//								ON r.i_user_id=u.id , {$this->db->RING_CAT} c WHERE r.i_category_id=c.id AND r.i_isenabled=1 "
//						.$s_where.") UNION".
//						"(SELECT r.*, r.id AS ringid, c.s_category_name AS s_category_name, CONCAT(u.s_first_name,' ',u.s_last_name) AS owner_name
//							FROM {$this->db->RING} r LEFT JOIN {$this->db->USERS} AS u 
//								ON r.i_user_id=u.id , {$this->db->RING_CAT} AS c ,{$this->db->RING_INV_USER} AS inv 
//								WHERE r.i_category_id=c.id AND r.i_isenabled=1 AND inv.i_ring_id=r.id AND inv.i_joined = 1 ".$s_where1.")) AS tab"; 
//          #echo $s_qry;
//		  $rs=$this->db->query($s_qry);
//          $i_cnt=0;
//          if(is_array($rs->result()))
//          {
//              foreach($rs->result() as $row)
//              {
//                  $ret_=intval($row->i_total); 
//              }    
//              $rs->free_result();          
//          }
//          $this->db->trans_commit();///new
//          unset($s_qry,$rs,$row,$i_cnt,$s_where);
//          return $ret_;
//        }
//        catch(Exception $err_obj)
//        {
//            show_error($err_obj->getMessage());
//        }           
//    }
        
	public function show_ring_by_user($s_where=null,$i_start=null,$i_limit=null,$s_order_by=null)
	{
	try
        {
           // die('dd');
		  	$ret_=array();
			
			//$language = get_current_language();
			
//			$s_qry = "(SELECT r.*, r.id AS ringid, c.s_category_name AS s_category_name, CONCAT(u.s_first_name,' ',u.s_last_name) AS owner_name,
//							(SELECT COUNT(id) FROM {$this->db->USER_RING_POST} WHERE i_ring_id=r.id) AS post,
//							(SELECT COUNT(cm.id) FROM {$this->db->USER_RING_POST} AS po, {$this->db->USER_RING_POST_COMMENTS} AS cm WHERE po.id=cm.i_ring_post_id AND po.i_ring_id=r.id) AS cmt,
//							(SELECT COUNT(lk.id) FROM {$this->db->USER_RING_POST} AS po, {$this->db->USER_RING_POST_LIKE} AS lk WHERE po.id=lk.i_ring_post_id AND po.i_ring_id=r.id) AS lik
//							FROM {$this->db->RING} r LEFT JOIN {$this->db->USERS} AS u 
//								ON r.i_user_id=u.id , {$this->db->RING_CAT} c WHERE r.i_category_id=c.id AND r.i_isenabled=1 "
//						.$s_where.") UNION".
//						"(SELECT r.*, r.id AS ringid, c.s_category_name AS s_category_name, CONCAT(u.s_first_name,' ',u.s_last_name) AS owner_name,
//							(SELECT COUNT(id) FROM {$this->db->USER_RING_POST} WHERE i_ring_id=r.id) AS post,
//							(SELECT COUNT(cm.id) FROM {$this->db->USER_RING_POST} AS po, {$this->db->USER_RING_POST_COMMENTS} AS cm WHERE po.id=cm.i_ring_post_id AND po.i_ring_id=r.id) AS cmt,
//							(SELECT COUNT(lk.id) FROM {$this->db->USER_RING_POST} AS po, {$this->db->USER_RING_POST_LIKE} AS lk WHERE po.id=lk.i_ring_post_id AND po.i_ring_id=r.id) AS lik
//							FROM {$this->db->RING} r LEFT JOIN {$this->db->USERS} AS u 
//								ON r.i_user_id=u.id , {$this->db->RING_CAT} AS c ,{$this->db->RING_INV_USER} AS inv 
//								WHERE r.i_category_id=c.id AND r.i_isenabled=1 AND inv.i_ring_id=r.id AND inv.i_joined = 1 ".$s_where1.")"; 
                
          //////////For Pagination///////////*don't change*/
          //$s_qry=str_replace("'","''",$s_qry);//for string operation in procedure
                       $s_qry = 'SELECT r.*, r.id AS ringid,c.s_category_name AS s_category_name  from cg_church_ring as r , cg_church_ring_category as c where r.i_category_id = c.id '.$s_where.'  ';
          $s_qry= $s_qry.(trim($s_order_by)!=""?" ORDER BY ".$s_order_by."":"ORDER BY ringid DESC")." ".(is_numeric($i_start) && is_numeric($i_limit)?" LIMIT ".intval($i_start).",".intval($i_limit):"");
		 
		  //echo ($s_qry);exit;
          //////////end For Pagination//////////                
                
          $this->db->trans_begin();///new                
          $rs=$this->db->query($s_qry); 
         // ;
          $i_cnt=0;
          if(is_array($rs->result()))
          {
             // pr($rs->result(),1);
              foreach($rs->result() as $row)
              {
                        $ret_[$i_cnt]["ringid"]				=	$row->ringid;////always integer
                      //die('s');
						$ret_[$i_cnt]["s_ring_name"]		=	$row->s_ring_name;
                        $ret_[$i_cnt]["s_description"]      =    $row->s_description;
						$ret_[$i_cnt]["i_user_id"]		    =	$row->i_user_id;
						$ret_[$i_cnt]["s_logo"]				=	$row->s_logo;
						
						$ret_[$i_cnt]["post_owner_user_id"]	=	$row->i_user_id;
						$ret_[$i_cnt]["owner_name"]			=	get_user_name_by_id($row->i_user_id);
//						$ret_[$i_cnt]["i_privacy"]  	   	=	$row->i_privacy; 
//						
//						$ret_[$i_cnt]["i_member"]  	   		=	$row->i_member; 
//						$ret_[$i_cnt]["s_state"]  	   		=	get_unformatted_string($row->s_state); 
//						$ret_[$i_cnt]["dt_created_on"]		=	$row->dt_created_on;
//						$ret_[$i_cnt]["i_country_id"]		=	intval($row->i_country_id);
						$ret_[$i_cnt]["dt_created_on"]		=	($row->dt_created_on);
						$ret_[$i_cnt]["s_category_name"]	=	$row->s_category_name;
//						$ret_[$i_cnt]["post"]				=	$row->post;
//						$ret_[$i_cnt]["cmt"]				=	$row->cmt;
//						$ret_[$i_cnt]["lik"]				=	$row->lik;
//						
						$ret_[$i_cnt]["i_sub_category_id"]		=	$row->i_sub_category_id;

                  $i_cnt++;
              }    
              $rs->free_result();          
          }
          $this->db->trans_commit();///new
		  
		  					
		  
          unset($s_qry,$rs,$row,$i_cnt,$s_where,$i_start,$i_limit);
          return $ret_;
         // pr($res_,1);
		    }
			catch(Exception $err_obj)
			{
				show_error($err_obj->getMessage());
			}           
    
    
    }
	
	
	public function gettotal_ring_by_user($s_where)
    {
        try
        {
          $ret_=0;
         
				
//		  $s_qry = "SELECT COUNT(tab.ringid) AS i_total FROM ((SELECT r.*, r.id AS ringid, c.s_category_name AS s_category_name, CONCAT(u.s_first_name,' ',u.s_last_name) AS owner_name
//							FROM {$this->db->RING} r LEFT JOIN {$this->db->USERS} AS u 
//								ON r.i_user_id=u.id , {$this->db->RING_CAT} c WHERE r.i_category_id=c.id AND r.i_isenabled=1 "
//						.$s_where.") UNION".
//						"(SELECT r.*, r.id AS ringid, c.s_category_name AS s_category_name, CONCAT(u.s_first_name,' ',u.s_last_name) AS owner_name
//							FROM {$this->db->RING} r LEFT JOIN {$this->db->USERS} AS u 
//								ON r.i_user_id=u.id , {$this->db->RING_CAT} AS c ,{$this->db->RING_INV_USER} AS inv 
//								WHERE r.i_category_id=c.id AND r.i_isenabled=1 AND inv.i_ring_id=r.id AND inv.i_joined = 1 ".$s_where1.")) AS tab"; 
          #echo $s_qry;
           $s_qry = 'SELECT count(*) as i_total from cg_church_ring as r , cg_church_ring_category as c where r.i_category_id = c.id '.$s_where.' ';
           //echo ($s_qry);exit;
		  $rs=$this->db->query($s_qry);
          $i_cnt=0;
         // pr($rs->result(),1);
          if(is_array($rs->result()))
          {
              foreach($rs->result() as $row)
              {
                  $ret_=intval($row->i_total); 
              }    
              $rs->free_result();          
          }
          $this->db->trans_commit();///new
          unset($s_qry,$rs,$row,$i_cnt,$s_where);
          return $ret_;
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }           
    }
	
	/*********************************************************************/
	
	public function get_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY R.id DESC';
		
        $sql    = " SELECT R.*,
					(SELECT s_category_name FROM cg_church_ring_category C WHERE C.id = R.i_category_id  ) AS s_category_name,

					(SELECT s_category_name FROM cg_church_ring_category C WHERE C.id = R.i_sub_category_id ) AS s_sub_category_name,
					
					 (SELECT count(*)
								FROM {$this->db->USERS} U, cg_church_ring_invited_user AS RU
								WHERE RU.i_ring_id = R.id  AND RU.i_invited_id = U.id AND RU.i_joined = 1
						)AS i_total_member,
				
					(SELECT CONCAT(s_first_name,' ',s_last_name) FROM {$this->db->USERS} U WHERE R.i_user_id = U.id  ) 
					 AS s_owner_name,
					
					(SELECT count(*) FROM  cg_church_ring_post RP WHERE RP.i_ring_id = R.id  ) AS total_posts,
					
					(SELECT count(*) FROM  cg_church_ring_post RP , cg_church_ring_post_comments RPC 
									   WHERE RPC.i_ring_post_id = RP.id AND RP.i_ring_id = R.id ) AS total_comments
					
					FROM cg_church_ring AS R 
					
					{$where}  {$s_order_by} {$limit}";

        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		
        return $result_arr;
    }
	
	
	    
	
    public function get_list_count($where='')
    {
        
        
        $sql    = "SELECT count(*) as i_total FROM {$this->db->RING} R
					
				  	{$where} ";
        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0]['i_total'];
    }
	
    public function update($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->RING, $arr, array('id'=>$id));
		
	}
	
	/*****************update 29-01-2015**************************************/
//	public function delete_by_id($id) {
//	
//	     $sql = sprintf( 'DELETE FROM '.$this->db->RING.' WHERE id=%s', $id );
//		 $this->db->query($sql);
//		 $sql = sprintf( 'DELETE FROM '.$this->db->RING_INV_USER.' WHERE i_ring_id=%s', $id );
//		 $this->db->query($sql);
//		 $sql = sprintf( 'DELETE FROM '.$this->db->USER_RING_POST.' WHERE i_ring_id=%s', $id );
//		 $this->db->query($sql);
//		 $sql = sprintf( 'DELETE FROM '.$this->db->USER_RING_POST_COMMENTS.' WHERE i_ring_id=%s', $id );
//		 $this->db->query($sql);
//		 $sql = sprintf( 'DELETE FROM '.$this->db->USER_RING_POST_LIKE.' WHERE i_ring_id=%s', $id );
//		 $this->db->query($sql);
//		#echo $this->db->last_query(); exit;
//	}
        
        public function delete_by_id($id) {
	
	     $sql = sprintf( 'DELETE FROM cg_church_ring WHERE id=%s', $id );
		 $this->db->query($sql);
		 $sql = sprintf( 'DELETE FROM cg_church_ring_invited_user WHERE i_ring_id=%s', $id );
		 $this->db->query($sql);
//		 $sql = sprintf( 'DELETE FROM '.$this->db->USER_RING_POST.' WHERE i_ring_id=%s', $id );
//		 $this->db->query($sql);
//		 $sql = sprintf( 'DELETE FROM '.$this->db->USER_RING_POST_COMMENTS.' WHERE i_ring_id=%s', $id );
//		 $this->db->query($sql);
//		 $sql = sprintf( 'DELETE FROM '.$this->db->USER_RING_POST_LIKE.' WHERE i_ring_id=%s', $id );
//		 $this->db->query($sql);
		#echo $this->db->last_query(); exit;
	}
	/**********************************************************/
	public function leave_ring($id) {
	
	     $inv_id	 = intval(decrypt($this->session->userdata('user_id')));
		 $sql = sprintf( 'DELETE FROM cg_church_ring_invited_user WHERE i_ring_id=%s AND i_invited_id=%s', $id,$inv_id );
		 $this->db->query($sql);
		 
		#echo $this->db->last_query(); exit;
	}
	
	
	public function change_status($status ,$id) {
		
	  if($status !='' && $id !=''){	
		  $sql = sprintf( "UPDATE {$this->db->RING} SET `i_isenabled` = '%s'
						   WHERE `id` ='%s'"
					  , $status, $id );
		  $this->db->query($sql);// echo $this->db->last_query();exit;
		  return true;
	  }
	}
	
	
	public function get_ring_users_by_id($ring_id,$where='')
    {
        
       
		
        $sql    = " SELECT R.*,
					CONCAT(U.s_first_name,' ',U.s_last_name) as s_profile_name,
					U.s_profile_photo,
					C.s_country_name,
					U.id AS user_joined_id,
                    RU.id AS table_id
					
					FROM {$this->db->RING} R  , {$this->db->RING_INV_USER} RU
					LEFT JOIN {$this->db->USERS} U ON RU.i_invited_id = U.id
					LEFT JOIN {$this->db->MST_COUNTRY} C ON U.i_country_id = C.id 
					
					WHERE RU.i_ring_id = R.id  
					AND RU.i_invited_id = U.id 
					AND RU.i_joined = 1 
					AND	R.id = {$ring_id}
					{$where}  ";

        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		
        return $result_arr;
    }
    
    //------------------------------------ ring members --------------------------------------------
    public function get_all_ring_members_by_ring_id($ring_id , $start_limit='', $end_limit='',$where='')
    {
        //echo "start : ".$start_limit." -- end : ".$end_limit;
        if("$start_limit"=='')
        {
            $limit='';
        }
        else
        {
            $limit = "LIMIT ".intval($start_limit).' , '.intval($end_limit);
        }
        $sql = "SELECT R.*, CONCAT(U.s_first_name,' ',U.s_last_name) AS profile_name,
                U.s_profile_photo, U.id AS post_owner_user_id, I.id AS table_id, I.dt_joined_date, I.i_invited_id
                
                FROM cg_church_ring AS R 
                LEFT JOIN cg_church_ring_invited_user AS I ON R.id=I.i_ring_id
                LEFT JOIN {$this->db->USERS} U ON U.id=I.i_invited_id
                WHERE R.id={$ring_id}
                AND I.i_joined=1 AND R.i_isenabled=1 AND R.i_delete=0 {$where} {$limit}
                ";
        
        $res = $this->db->query($sql)->result_array();
		    #echo $this->db->last_query();exit;
        $response = check_friend_netpal_status($res);
        return $response;
      
    }
    
    public function get_total_all_ring_members_by_ring_id($ring_id)
    {
        $sql = "SELECT COUNT(*) total FROM cg_church_ring_invited_user WHERE `i_joined`=1 AND i_ring_id={$ring_id}";
        $res = $this->db->query($sql)->row_array();

        return $res['total'];
    }
    
    public function delete_ring_member_by_id($id)
    {
        $sql = "DELETE FROM cg_church_ring_invited_user WHERE id={$id}";
        $this->db->query($sql);
    }
    
    //------------------------------------ /ring members --------------------------------------------
    
    
    //---------------------------------- ring invitation --------------------------------------------
    public function get_all_ring_members_in_table_by_ring_id($ring_id , $start_limit='', $end_limit='',$where='')
    {
        //echo "start : ".$start_limit." -- end : ".$end_limit;
        if("$start_limit"=='')
        {
            $limit='';
        }
        else
        {
            $limit = "LIMIT ".intval($start_limit).' , '.intval($end_limit);
        }
        $sql = "SELECT R.*, CONCAT(U.s_first_name,' ',U.s_last_name) AS profile_name,
                U.s_profile_photo, U.id AS post_owner_user_id, I.id AS table_id, I.dt_joined_date, I.i_invited_id
                
                FROM {$this->db->RING} R 
                LEFT JOIN {$this->db->RING_INV_USER} I ON R.id=I.i_ring_id
                LEFT JOIN {$this->db->USERS} U ON U.id=I.i_invited_id
                WHERE R.id={$ring_id}
                AND R.i_isenabled=1 AND R.i_delete=0 {$where} {$limit}
                ";
                
        $res = $this->db->query($sql)->result_array();
        $response = check_friend_netpal_status($res);
        return $response;
      
    }
    //---------------------------------- /ring invitation --------------------------------------------
    
    
	
	/****************************GET ALL CATEGORY*********************************/
	
	function get_all_category()
	{
		
		$sql = 'SELECT * FROM '.$this->db->RING_CAT.' WHERE i_delete = 0 ORDER BY s_category_name DESC';
		$query = $this->db->query($sql); #echo $this->db->last_query(); exit;
		$result_arr = $query->result_array();
		#pr($result_arr[0]);
		return $result_arr;
	}
	
	/****************************GET ALL CATEGORY*********************************/
	
	/****************************SHOW ALL PUBLIC RING*********************************/
	function show_all_public_ring($s_where,$i_start=null,$i_limit=null,$s_order_by=null)
    {
        try
        {
          $ret_= array();
         
				
		  $s_qry = "SELECT r.*, r.id AS ringid, c.s_category_name AS s_category_name , CONCAT(u.s_first_name,' ',u.s_last_name) AS owner_name,
					(SELECT COUNT(id) FROM {$this->db->USER_RING_POST} WHERE i_ring_id=r.id) AS post,
					(SELECT COUNT(cm.id) FROM {$this->db->USER_RING_POST} AS po, {$this->db->USER_RING_POST_COMMENTS} AS cm WHERE po.id=cm.i_ring_post_id AND po.i_ring_id=r.id) AS cmt,
					(SELECT COUNT(lk.id) FROM {$this->db->USER_RING_POST} AS po, {$this->db->USER_RING_POST_LIKE} AS lk WHERE po.id=lk.i_ring_post_id AND po.i_ring_id=r.id) AS lik
		  					
							FROM {$this->db->RING} AS r, 
							{$this->db->RING_CAT} c , {$this->db->USERS} AS u 
							 
							WHERE r.i_user_id=u.id AND r.i_category_id=c.id AND r.i_isenabled=1 AND r.i_privacy=2 "
						.$s_where."";  #AND i_privacy=2
						
		  $s_qry= $s_qry.(trim($s_order_by)!=""?" ORDER BY ".$s_order_by."":"ORDER BY ringid DESC")." ".(is_numeric($i_start) && is_numeric($i_limit)?" LIMIT ".intval($i_start).",".intval($i_limit):"");
         // echo $s_qry;exit;
		  $this->db->trans_begin();///new                
          $rs=$this->db->query($s_qry); 
		  $i_cnt=0;
		  
          if(is_array($rs->result()))
          {
		  		
              foreach($rs->result() as $row)
              {
                        $ret_[$i_cnt]["ringid"]				=	$row->id;////always integer
						$ret_[$i_cnt]["s_ring_name"]		=	$row->s_ring_name;
                        $ret_[$i_cnt]["s_description"]      =    $row->s_description;
						$ret_[$i_cnt]["s_logo"]				=	$row->s_logo;
						$ret_[$i_cnt]["i_user_id"]			=	$row->i_user_id;
						
						$ret_[$i_cnt]["i_privacy"]  	   	=	$row->i_privacy; 
						
						$ret_[$i_cnt]["i_member"]  	   		=	$row->i_member; 
						$ret_[$i_cnt]["s_state"]  	   		=	get_unformatted_string($row->s_state); 
						$ret_[$i_cnt]["dt_created_on"]		=	$row->dt_created_on;
						$ret_[$i_cnt]["i_country_id"]		=	intval($row->i_country_id);
						$ret_[$i_cnt]["dt_created_on"]		=	($row->dt_created_on);
						$ret_[$i_cnt]["s_category_name"]		=	$row->s_category_name;
						$ret_[$i_cnt]["owner_name"]			=	$row->owner_name;
						$ret_[$i_cnt]["post_owner_user_id"]	=	$row->i_user_id;
						$ret_[$i_cnt]["post"]	=	$row->post;
						$ret_[$i_cnt]["cmt"]	=	$row->cmt;
						$ret_[$i_cnt]["lik"]	=	$row->lik;
						
						

                  $i_cnt++;
              }    
              $rs->free_result();          
          }
          $this->db->trans_commit();///new
		  //pr($ret_);
		  //exit;
          unset($s_qry,$rs,$row,$i_cnt,$s_where,$i_start,$i_limit);
          return $ret_;
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }           
    }
	
	public function gettotal_of_all_public_ring($s_where)
    {
        try
        {
          $ret_=0;
         
				
		 $s_qry = "SELECT COUNT(*) AS i_total
							FROM {$this->db->RING} r  ,
							{$this->db->RING_CAT} c WHERE r.i_category_id=c.id AND r.i_isenabled=1 AND r.i_privacy=2 "
						.$s_where."";
		  #echo 	$s_qry;			
		  $rs=$this->db->query($s_qry);
          $i_cnt=0;
          if(is_array($rs->result()))
          {
              foreach($rs->result() as $row)
              {
                  $ret_=intval($row->i_total); 
              }    
              $rs->free_result();          
          }
          $this->db->trans_commit();///new
          unset($s_qry,$rs,$row,$i_cnt,$s_where);
          return $ret_;
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }           
    }
	
	
	public function get_join_req_arr($where='')
	{
		$arr	= array();
		$uid	= intval(decrypt($this->session->userdata('user_id')));
		$s_qry = "SELECT * FROM cg_church_ring_invited_user AS inv WHERE i_invited_id='{$uid}' {$where}";
		$this->db->trans_begin();///new                
        $rs=$this->db->query($s_qry); 
		$i_cnt=0;
		  
        if(is_array($rs->result()))
        {
            foreach($rs->result() as $row)
            {
				$arr[]	= $row->i_ring_id;
			}
		}
		return $arr;
	}
	/****************************SHOW ALL PUBLIC RING*********************************/
	
	
	
	public function add_join_request($arr)
	{
		if($this->db->insert('cg_church_ring_invited_user' ,$arr))
			return true;
		else	
			return true;
	}
	
	
	public function get_ring_join_req_list($s_where,$i_start=null,$i_limit=null,$s_order_by=null)
    {
        try
        {
          $ret_= array();
		  $s_qry = "SELECT r.*, u.id AS invitedid , CONCAT(u.s_first_name,' ',u.s_last_name) AS s_profile_name ,u.s_profile_photo AS s_profile_photo 
							FROM {$this->db->RING_INV_USER} AS r, 
							{$this->db->USERS} u 
							WHERE r.i_invited_id=u.id AND r.i_request=1 AND r.i_joined=0 "
						.$s_where."";  #AND i_privacy=2
						
		  $s_qry= $s_qry.(trim($s_order_by)!=""?" ORDER BY ".$s_order_by."":" ORDER BY r.i_ring_id DESC")." ".(is_numeric($i_start) && is_numeric($i_limit)?" LIMIT ".intval($i_start).",".intval($i_limit):"");
          //echo $s_qry;exit;
		  $this->db->trans_begin();///new                
          $rs=$this->db->query($s_qry); 
		  $i_cnt=0;
		  
          if(is_array($rs->result()))
          {
		  		
              foreach($rs->result() as $row)
              {
			  		$ret_[$i_cnt]["id"]						=	 $row->id;////always integer
					$ret_[$i_cnt]["ringid"]					=	 $row->i_ring_id;////always integer
					$ret_[$i_cnt]["dt_request_date"]		=	 $row->dt_request_date;
					$ret_[$i_cnt]["s_request_detail"]       =    $row->s_request_detail;
					$ret_[$i_cnt]["s_profile_name"]			=	 $row->s_profile_name;
					$ret_[$i_cnt]["invitedid"]				=	 $row->invitedid;
					$ret_[$i_cnt]["s_profile_photo"]  	   	=	 $row->s_profile_photo; 
					$ret_[$i_cnt]["post_owner_user_id"]		=	 $row->invitedid;
                  	$i_cnt++;
              }    
              $rs->free_result();          
          }
          $this->db->trans_commit();///new
		  
          unset($s_qry,$rs,$row,$i_cnt,$s_where,$i_start,$i_limit);
          return $ret_;
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }           
    }
	
	public function gettotal_ring_join_req($s_where)
    {
        try
        {
          $ret_=0;
         
		$s_qry = "SELECT COUNT(*) AS i_total
							FROM {$this->db->RING_INV_USER} AS r, 
							{$this->db->USERS} u 
							WHERE r.i_invited_id=u.id AND r.i_request=1 AND r.i_joined=0 "
						.$s_where."";		
		 
		  #echo 	$s_qry;			
		  $rs=$this->db->query($s_qry);
          $i_cnt=0;
          if(is_array($rs->result()))
          {
              foreach($rs->result() as $row)
              {
                  $ret_=intval($row->i_total); 
              }    
              $rs->free_result();          
          }
          $this->db->trans_commit();///new
          unset($s_qry,$rs,$row,$i_cnt,$s_where);
          return $ret_;
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }           
    }
	
	
	public function get_ring_inv_list($s_where,$i_start=null,$i_limit=null,$s_order_by=null)
    {
        try
        {
          $ret_= array();
		  $s_qry = "SELECT r.*, u.id AS invitedid , CONCAT(u.s_first_name,' ',u.s_last_name) AS s_profile_name ,u.s_profile_photo AS s_profile_photo 
							FROM cg_church_ring_invited_user AS r, 
							{$this->db->USERS} u 
							WHERE r.i_invited_id=u.id AND r.i_request=0 AND r.i_joined=0 "
						.$s_where."";  #AND i_privacy=2
						
		  $s_qry= $s_qry.(trim($s_order_by)!=""?" ORDER BY ".$s_order_by."":" ORDER BY r.i_ring_id DESC")." ".(is_numeric($i_start) && is_numeric($i_limit)?" LIMIT ".intval($i_start).",".intval($i_limit):"");
          #echo $s_qry;exit;
		  $this->db->trans_begin();///new                
          $rs=$this->db->query($s_qry); 
		  $i_cnt=0;
		  
          if(is_array($rs->result()))
          {
		  		
              foreach($rs->result() as $row)
              {
			  		$ret_[$i_cnt]["id"]						=	 $row->id;////always integer
					$ret_[$i_cnt]["ringid"]					=	 $row->i_ring_id;////always integer
					$ret_[$i_cnt]["dt_request_date"]		=	 $row->dt_request_date;
					$ret_[$i_cnt]["s_request_detail"]       =    $row->s_request_detail;
					$ret_[$i_cnt]["s_profile_name"]			=	 $row->s_profile_name;
					$ret_[$i_cnt]["invitedid"]				=	 $row->invitedid;
					$ret_[$i_cnt]["s_profile_photo"]  	   	=	 $row->s_profile_photo; 
					$ret_[$i_cnt]["post_owner_user_id"]		=	 $row->invitedid;
                  	$i_cnt++;
              }    
              $rs->free_result();          
          }
          $this->db->trans_commit();///new
		  
          unset($s_qry,$rs,$row,$i_cnt,$s_where,$i_start,$i_limit);
          return $ret_;
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }           
    }
	
	public function gettotal_ring_inv($s_where)
    {
        try
        {
          	$ret_=0;
         
			$s_qry = "SELECT COUNT(*) AS i_total
							FROM cg_church_ring_invited_user AS r, 
							{$this->db->USERS} u 
							WHERE r.i_invited_id=u.id AND r.i_request=0 AND r.i_joined=0 "
						.$s_where."";		
		 
		  #echo 	$s_qry;			
		  $rs=$this->db->query($s_qry);
          $i_cnt=0;
          if(is_array($rs->result()))
          {
              foreach($rs->result() as $row)
              {
                  $ret_=intval($row->i_total); 
              }    
              $rs->free_result();          
          }
          $this->db->trans_commit();///new
          unset($s_qry,$rs,$row,$i_cnt,$s_where);
          return $ret_;
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }           
    }
	
	/******************29-01-2015 ******************************/
//	public function update_ring($info,$ring_id)
//	{
//		$query = $this->db->update($this->db->RING,$info,array('id'=>$ring_id));
//        return $query;
//	}
    public function update_ring($info,$ring_id)
	{
		$query = $this->db->update('cg_church_ring',$info,array('id'=>$ring_id));
        return $query;
	}
	/****************************************************/
	public function get_ring_details_by_ring_post_id($ring_post_id , $where='')
    {
             
        $sql = "SELECT 
						R.*,
						RP.s_post_title
                
                FROM {$this->db->RING} R 
                LEFT JOIN  {$this->db->USER_RING_POST} RP ON R.id = RP.i_ring_id
                WHERE RP.id = {$ring_post_id} {$where}
                ";
        
        $result_arr = $this->db->query($sql)->result_array();
		#echo $this->db->last_query();exit;
        return $result_arr[0];
      
    }
	
	
	/****************************NEW Method SHOW ALL PUBLIC RING*********************************/
	function show_all_public_ring_new($s_where,$i_start=null,$i_limit=null,$s_order_by=null, $s_query_type = "both",$wh_ring_post)
    {
           // die('ok');
            
        try
        {
          $ret_= array();
         
		if($s_query_type == 'ring'){		

                   $s_qry =   "SELECT   
		  					   
							   r.id AS ringid, 
							   r.s_ring_name as s_name,
							   r.s_description as s_desc,
							   r.dt_created_on,
							   r.s_logo,
							   r.i_member,
							   r.i_sub_category_id, 
							   
							   'ring' as s_type,
							   r.i_user_id as post_owner_user_id,
							   
							   c.s_category_name AS s_category_name 
							  
							  FROM cg_church_ring AS r, 
							  cg_church_ring_category AS c , {$this->db->USERS} AS u 
							   
							  WHERE r.i_user_id=u.id AND r.i_category_id=c.id AND r.i_isenabled=1  
							  {$s_where} ";  #AND i_privacy=2AND (r.i_privacy=0 || r.i_privacy=2)
                // exit();
               
		}
		else if($s_query_type == 'posts'){
			 
			 $s_qry = "
							 SELECT 
							 	 r.id AS ringid,   
								 rp.id AS ringPostid, 
								 rp.s_post_title as s_name,
								 rp.s_post_description as s_desc,
								 rp.dt_created_on,
								 r.i_sub_category_id, 
								
								 'post' as s_type,
								 c.s_category_name AS s_category_name , 
								 rp.i_user_id as post_owner_user_id, 
								 CONCAT(u.s_first_name,' ',u.s_last_name) AS owner_name,
								 
														 
							   (SELECT COUNT(cm.id) FROM cg_church_ring_post_comments 
												AS cm WHERE cm.i_ring_post_id = rp.id) AS post_cmt,
												
							   (SELECT COUNT(lk.id) FROM cg_church_ring_post_like 
												AS lk WHERE lk.i_ring_post_id = rp.id) AS post_lik
								
								FROM cg_church_ring AS r left join cg_church_ring_post rp ON rp.i_ring_id = r.id , 
								cg_church_ring_category c , cg_users AS u 
								 
								WHERE
								 rp.i_user_id=u.id 
								 AND r.i_category_id=c.id 
								 AND r.i_isenabled=1 
								 AND r.i_privacy=2  
								{$s_where} ";
					
		}
		else if($s_query_type == 'both'){
			
			 $s_qry = " SELECT derived_tbl.* FROM (
							(SELECT 
								   r.id AS ringid, 
								   '0' AS ringPostid,
								   r.s_ring_name as s_name,
								   r.s_description as s_desc,
								   r.dt_created_on,
								   r.s_logo as s_logo,
								   r.i_member AS i_member,
								   'ring' as s_type,
								   r.i_sub_category_id, 
								   r.i_user_id as post_owner_user_id,
								   
								   c.s_category_name AS s_category_name , 
								   CONCAT(u.s_first_name,' ',u.s_last_name) AS owner_name,
								   (SELECT COUNT(id) FROM cg_church_ring_post WHERE i_ring_id=r.id) AS post,
								 
								   (SELECT COUNT(cm.id) FROM cg_church_ring_post AS po, cg_church_ring_post_comments 
													AS cm WHERE po.id=cm.i_ring_post_id AND po.i_ring_id=r.id) AS cmt,
													
								   (SELECT COUNT(lk.id) FROM cg_church_ring_post AS po, cg_church_ring_post_like 
													AS lk WHERE po.id=lk.i_ring_post_id AND po.i_ring_id=r.id) AS lik,
													1 as post_cmt,
													1 as post_lik
													
								  
							  
							  FROM cg_church_ring AS r, 
							  cg_church_ring_category c , {$this->db->USERS} AS u 
							   
							  WHERE r.i_user_id=u.id AND r.i_category_id=c.id AND r.i_isenabled=1 AND r.i_privacy=2
							  {$s_where} 
							  )
                            
                            UNION
                            
                         (
						 
						 
						 	SELECT 
							 	 r.id AS ringid,   
								 rp.id AS ringPostid, 
								 rp.s_post_title as s_name,
								 rp.s_post_description as s_desc,
								 rp.dt_created_on,
								 '0' as s_logo,
								 '0' AS i_member,
								 'post' as s_type,
								   r.i_sub_category_id, 
								 rp.i_user_id as post_owner_user_id,
								 c.s_category_name AS s_category_name , 
								 CONCAT(u.s_first_name,' ',u.s_last_name) AS owner_name,
								 '0' AS post,
								 '0' AS cmt,
								 '0' AS lik,
							
								  (SELECT COUNT(cm.id) FROM cg_church_ring_post_comments 
												AS cm WHERE cm.i_ring_post_id = rp.id) AS post_cmt,
												
							   	(SELECT COUNT(lk.id) FROM cg_church_ring_post_like 
												AS lk WHERE lk.i_ring_post_id = rp.id) AS post_lik
							  
												  
								FROM cg_church_ring AS r left join cg_church_ring_post rp ON rp.i_ring_id = r.id , 
								cg_church_ring_category c , cg_users AS u 
								 
								WHERE
								 rp.i_user_id=u.id 
								 AND r.i_category_id=c.id 
								 AND r.i_isenabled=1 
								 AND r.i_privacy=2  
								{$wh_ring_post} 
						 		)
							 ) as  derived_tbl {$limit} ";
		}
						
		 
		 
		 
		  $s_qry= $s_qry.(trim($s_order_by)!=""?" ORDER BY ".$s_order_by."":"ORDER BY ringid DESC")." ".(is_numeric($i_start) && is_numeric($i_limit)?" LIMIT ".intval($i_start).",".intval($i_limit):"");
//echo nl2br($s_qry);exit;
		  $res = $this->db->query($s_qry)->result_array();
		  return $res;
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }           
    }
	
	public function gettotal_of_all_public_ring_new($s_where , $s_query_type = 'both', $wh_ring_post)
    {
        try
        {
          $ret_=0;
         
				
		/* $s_qry = "SELECT COUNT(*) AS i_total
							FROM {$this->db->RING} r  ,
							{$this->db->RING_CAT} c WHERE r.i_category_id=c.id AND r.i_isenabled=1 AND r.i_privacy=2 "
						.$s_where."";*/
						
						
		  if($s_query_type == 'ring'){	
                      //die('ok');
		  $s_qry =   "SELECT  COUNT(*) AS i_total 
							  FROM cg_church_ring AS r, 
							  cg_church_ring_category c , {$this->db->USERS} AS u 
							   
							  WHERE r.i_user_id=u.id AND r.i_category_id=c.id AND r.i_isenabled=1 
							  {$s_where}";  #AND i_privacy=2AND (r.i_privacy=0 || r.i_privacy=2)
		}
		else if($s_query_type == 'posts'){
			 
			 $s_qry = "  SELECT  COUNT(*) AS i_total
								FROM cg_church_ring AS r left join cg_church_ring_post rp ON rp.i_ring_id = r.id , 
								cg_church_ring_category c , cg_users AS u 
								 
								WHERE
								 rp.i_user_id=u.id 
								 AND r.i_category_id=c.id 
								 AND r.i_isenabled=1 
								 AND r.i_privacy=2  
								{$s_where}";
					
		}
		else if($s_query_type == 'both'){
			
			 $s_qry = " SELECT COUNT(*) AS i_total FROM (
							(SELECT 
								   r.id AS ringid
								  
							  FROM cg_church_ring AS r, 
							  cg_church_ring_category c , {$this->db->USERS} AS u 
							   
							  WHERE r.i_user_id=u.id AND r.i_category_id=c.id AND r.i_isenabled=1 AND r.i_privacy=2
							  {$s_where}
							  )
                            
                            UNION
                            
                         (
						 
						 
						 	SELECT 
							 	 
								 rp.id AS ringPostid
								 												  
								FROM cg_church_ring AS r left join cg_church_ring_post rp ON rp.i_ring_id = r.id , 
								cg_church_ring_category c , cg_users AS u 
								 
								WHERE
								 rp.i_user_id=u.id 
								 AND r.i_category_id=c.id 
								 AND r.i_isenabled=1 
								 AND r.i_privacy=2  
								{$wh_ring_post}
						 		)
							 ) as  derived_tbl {$limit} ";
		}
//echo 	$s_qry;exit();		
		 $res = $this->db->query($s_qry)->result_array();
		//echo  $res[0]['i_total'].' @@';
         return $res[0]['i_total'];
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }           
    }
	
	
	
	public function gettotal_ring_created_by_user($s_where)
    {
        try
        {
          $ret_=0;
         
				
		  $s_qry = "SELECT COUNT(tab.ringid) AS i_total FROM ((SELECT r.*, r.id AS ringid, c.s_category_name AS s_category_name, CONCAT(u.s_first_name,' ',u.s_last_name) AS owner_name
							FROM {$this->db->RING} r LEFT JOIN {$this->db->USERS} AS u 
								ON r.i_user_id=u.id , {$this->db->RING_CAT} c WHERE r.i_category_id=c.id AND r.i_isenabled=1 "
						.$s_where.") ) AS tab"; 
          #echo $s_qry;
		  $rs=$this->db->query($s_qry);
          $i_cnt=0;
          if(is_array($rs->result()))
          {
              foreach($rs->result() as $row)
              {
                  $ret_=intval($row->i_total); 
              }    
              $rs->free_result();          
          }
          $this->db->trans_commit();///new
          unset($s_qry,$rs,$row,$i_cnt,$s_where);
          return $ret_;
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }           
    }
	
	public function get_list_of_ring_created_by_user($s_where)
    {
        try
        {
          $ret_=0;
         
				
		  /*$s_qry = "SELECT r.*, GROUP_CONCAT(CONCAT(u.s_first_name,' ',u.s_last_name)) AS inv_users
							FROM {$this->db->RING} r , {$this->db->RING_INV_USER} AS inv ,{$this->db->USERS} AS u 
							WHERE  r.id=inv.i_ring_id AND inv.i_invited_id=u.id AND inv.i_joined=1 AND r.i_isenabled=1 ".$s_where." GROUP BY r.id"
						; */
						
		$s_qry = "SELECT r.*
						FROM {$this->db->RING} r 
						WHERE  r.i_isenabled=1 ".$s_where." "
					;			
          #echo $s_qry;
		  $rs	= $this->db->query($s_qry);
		  $ret_	= $rs->result();
          return $ret_;
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }           
    }
	
	
	
	
	public function new_get_ring_join_req_list($s_where,$i_start=null,$i_limit=null,$s_order_by=null)
    {
        try
        {
          $ret_= array();
		  $s_qry = "SELECT r.*, u.id AS invitedid , CONCAT(u.s_first_name,' ',u.s_last_name) AS s_profile_name ,u.s_profile_photo AS s_profile_photo ,
		  rg.s_ring_name
							FROM {$this->db->USERS} u , 
							cg_church_ring_invited_user AS r
							LEFT JOIN cg_church_ring rg ON r.i_ring_id = rg.id
							WHERE r.i_invited_id=u.id AND r.i_request=1 AND r.i_joined=0 "
						.$s_where."";  #AND i_privacy=2
						
		  $s_qry= $s_qry.(trim($s_order_by)!=""?" ORDER BY ".$s_order_by."":" ORDER BY r.i_ring_id DESC")." ".(is_numeric($i_start) && is_numeric($i_limit)?" LIMIT ".intval($i_start).",".intval($i_limit):"");
        //echo $s_qry;exit;
		  $this->db->trans_begin();///new                
          $rs=$this->db->query($s_qry); 
		  $i_cnt=0;
		  
          if(is_array($rs->result()))
          {
		  		
              foreach($rs->result() as $row)
              {
			  		$ret_[$i_cnt]["id"]						=	 $row->id;////always integer
					$ret_[$i_cnt]["ringid"]					=	 $row->i_ring_id;////always integer
					$ret_[$i_cnt]["dt_request_date"]		=	 $row->dt_request_date;
					$ret_[$i_cnt]["s_request_detail"]       =    $row->s_request_detail;
					$ret_[$i_cnt]["s_profile_name"]			=	 $row->s_profile_name;
					$ret_[$i_cnt]["invitedid"]				=	 $row->invitedid;
					$ret_[$i_cnt]["s_profile_photo"]  	   	=	 $row->s_profile_photo; 
					$ret_[$i_cnt]["post_owner_user_id"]		=	 $row->invitedid;
					$ret_[$i_cnt]["s_ring_name"]			=	 $row->s_ring_name;
                                        $ret_[$i_cnt]["s_logo"]                         =         $row->s_logo;
                  	$i_cnt++;
              }    
              $rs->free_result();          
          }
          $this->db->trans_commit();///new
		  
          unset($s_qry,$rs,$row,$i_cnt,$s_where,$i_start,$i_limit);
          return $ret_;
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }           
    }
	
	public function new_gettotal_ring_join_req($s_where)
    {
        try
        {
          $ret_=0;
         
		$s_qry = "SELECT COUNT(*) AS i_total
							FROM {$this->db->USERS} u , 
							cg_church_ring_invited_user AS r
							LEFT JOIN cg_church_ring rg ON r.i_ring_id = rg.id 
							WHERE r.i_invited_id=u.id AND r.i_request=1 AND r.i_joined=0 "
						.$s_where."";		
		 
		  #echo 	$s_qry;			
		  $rs=$this->db->query($s_qry);
          $i_cnt=0;
          if(is_array($rs->result()))
          {
              foreach($rs->result() as $row)
              {
                  $ret_=intval($row->i_total); 
              }    
              $rs->free_result();          
          }
          $this->db->trans_commit();///new
          unset($s_qry,$rs,$row,$i_cnt,$s_where);
          return $ret_;
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }           
    }
	
	
	public function get_ring_inv_list_nw($s_where,$i_start=null,$i_limit=null,$s_order_by=null)
    {
        try
        {
          $ret_= array();
		  $s_qry = "SELECT r.*, 
		  				r.i_invited_id AS invitedid , 
						rg.i_user_id as guest_id,  
						CONCAT(u.s_first_name,' ',u.s_last_name) AS s_profile_name ,
						u.s_profile_photo AS s_profile_photo ,  
						rg.s_ring_name
						
							FROM 
							cg_church_ring_invited_user AS r
							LEFT JOIN cg_church_ring rg ON r.i_ring_id = rg.id
							LEFT JOIN {$this->db->USERS}  u  ON  rg.i_user_id=u.id 
							WHERE  r.i_request=0 AND r.i_joined=0 "
						.$s_where."";  #AND i_privacy=2
						
		  $s_qry= $s_qry.(trim($s_order_by)!=""?" ORDER BY ".$s_order_by."":" ORDER BY r.i_ring_id DESC")." ".(is_numeric($i_start) && is_numeric($i_limit)?" LIMIT ".intval($i_start).",".intval($i_limit):"");
         // echo $s_qry;exit;
		  $this->db->trans_begin();///new                
          $rs=$this->db->query($s_qry); 
		  $i_cnt=0;
		  
          if(is_array($rs->result()))
          {
		  		
              foreach($rs->result() as $row)
              {
			  		$ret_[$i_cnt]["id"]						=	 $row->id;////always integer
					$ret_[$i_cnt]["ringid"]					=	 $row->i_ring_id;////always integer
					$ret_[$i_cnt]["dt_request_date"]		=	 $row->dt_request_date;
					$ret_[$i_cnt]["s_request_detail"]       =    $row->s_request_detail;
					$ret_[$i_cnt]["s_profile_name"]			=	 $row->s_profile_name;
					$ret_[$i_cnt]["invitedid"]				=	 $row->invitedid;
					$ret_[$i_cnt]["s_profile_photo"]  	   	=	 $row->s_profile_photo; 
					$ret_[$i_cnt]["post_owner_user_id"]		=	 $row->invitedid;
					$ret_[$i_cnt]["s_ring_name"]			=	 $row->s_ring_name;
					$ret_[$i_cnt]["guest_id"]			=	 $row->guest_id;
					
                  	$i_cnt++;
              }    
              $rs->free_result();          
          }
          $this->db->trans_commit();///new
		  
          unset($s_qry,$rs,$row,$i_cnt,$s_where,$i_start,$i_limit);
          return $ret_;
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }           
    }
	
	public function gettotal_ring_inv_nw($s_where)
    {
            
        try
        {
          	$ret_=0;
         
			$s_qry = "SELECT COUNT(*) AS i_total
							FROM 
							cg_church_ring_invited_user AS r
							LEFT JOIN cg_church_ring rg ON r.i_ring_id = rg.id
							LEFT JOIN {$this->db->USERS}  u  ON  rg.i_user_id=u.id 
							WHERE  r.i_request=0 AND r.i_joined=0"
						.$s_where."";		
		 
		  #echo 	$s_qry;			
		  $rs=$this->db->query($s_qry);
          $i_cnt=0;
          if(is_array($rs->result()))
          {
              foreach($rs->result() as $row)
              {
                  $ret_=intval($row->i_total); 
              }    
              $rs->free_result();          
          }
          $this->db->trans_commit();///new
          unset($s_qry,$rs,$row,$i_cnt,$s_where);
          return $ret_;
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }           
    }
    /*************************19-01-2015*********************************************************/
//	public function get_invitation_by_ring_id($i_ring_id)
//	{
//		 $s_qry =  "SELECT * FROM "
//					  .$this->db->ring_invitation." WHERE i_ring_id =".$i_ring_id;
//		  $rs=$this->db->query($s_qry); 
//		  #echo $this->db->last_query();
//		  $result=$rs->result_array();
//		  foreach($result as $row)
//		  {
//			  
//			  if($row['s_section']=='Ring User' || $row['s_section']=='Prayer Group')
//			  	$returnarr[$row['s_section']][$row['i_section_id']][]	= $row['i_user_id'];
//			  else
//				$returnarr[$row['s_section']][]	= $row['i_user_id'];
//		  	
//				
//		  }
//          return $returnarr;
//	}
    public function get_invitation_by_ring_id($i_ring_id)
	{
		 $s_qry =  "SELECT * FROM cg_church_ring_invited_user
					   WHERE i_ring_id =".$i_ring_id;
		  $rs=$this->db->query($s_qry); 
		  #echo $this->db->last_query();
		  $result=$rs->result_array();
		  foreach($result as $row)
		  {
			  
			  if($row['s_section']=='Ring User' || $row['s_section']=='Prayer Group')
			  	$returnarr[$row['s_section']][$row['i_section_id']][]	= $row['i_user_id'];
			  else
				$returnarr[$row['s_section']][]	= $row['i_user_id'];
		  	
				
		  }
          return $returnarr;
	}
	/************************************************************/
	public function get_all_ring_members_ids_by_ring_ids($ring_ids)
    {
        $sql = "SELECT i_invited_id
                FROM  cg_church_ring_invited_user
                WHERE i_ring_id IN ({$ring_ids})
                AND i_joined=1 {$where} ";
                
        $res = $this->db->query($sql)->result_array();
        foreach($res as $val)
		{
			$arr[]	= $val;
		}
      return $arr;
    }
	
	public function get_all_ring_added_by_loggedin_user($user_id)
	{
		 $sql = "SELECT * FROM {$this->db->RING} R 
                	WHERE i_isenabled=1 AND i_delete=0 AND i_user_id='".$user_id."'";
                
        $res = $this->db->query($sql)->result_array();
        foreach($res as $val)
		{
			$arr[]	= $val['id'];
		}
      return $arr;
	}
	
	public function get_pending_join_req_arr($where='')
	{
		$arr	= array();
		$uid	= intval(decrypt($this->session->userdata('user_id')));
		$s_qry = "SELECT * FROM cg_church_ring_invited_user AS inv WHERE i_invited_id='{$uid}' {$where}";
		$this->db->trans_begin();///new                
        $rs=$this->db->query($s_qry); 
		$i_cnt=0;
		  
        if(is_array($rs->result()))
        {
            foreach($rs->result() as $row)
            {
				$arr[]	= $row->i_ring_id;
			}
		}
		return $arr;
	}
	
	
	public function get_info_list($where='',$i_start=null,$i_limit=null,$s_order_by='1')
    {
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $sql  =  " SELECT PR.* 
						FROM cg_ring_category_request PR
						LEFT JOIN cg_users U ON U.id = PR.i_user_id
						{$where} GROUP BY PR.id 
						ORDER BY id DESC {$limit}";

        $query     = $this->db->query($sql); 
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
        return $result_arr;
    }
	
    public function get_info_list_count($where='')
    {
        $sql    = "     SELECT count(*) as i_total FROM
							(SELECT PR.id
							FROM cg_ring_category_request PR
							LEFT JOIN cg_users U ON U.id = PR.i_user_id
							{$where}
							GROUP BY PR.id  ) as drvd_tbl";
					 
        $query     = $this->db->query($sql);
        $result_arr = $query->result_array();
        return $result_arr[0]['i_total'];
    }
	
	public function get_ringids_by_userID($user_id) {
		
		$sql = "SELECT *
						FROM cg_user_ring
						where i_user_id ='".$user_id."' AND i_isenabled=1 ";
						#echo $sql; 
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
		foreach($result_arr as $val)
		{
			$arr[]	= $val['id'];
		}
		#pr($arr);
      	return $arr;
	}
	
	public function get_members_ids_by_ringids($group_ids) {
	
	 if($group_ids != ''){
		$sql = "SELECT i_invited_id
						FROM cg_ring_invited_user
						where i_ring_id IN ({$group_ids}) AND i_joined = '1' ";
						#echo $sql; 
		
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
		foreach($result_arr as $val)
		{
			$arr[]	= $val;
		}
	 }
      	return $arr;
	}
	
	
    public function __destruct()
    {}   
	
	
	public function getCategory($where='')
    {
        
       $sql = "SELECT *,
						subcat.i_parent_category AS subcat_i_parent_category, 
						(SELECT s_category_name FROM cg_church_ring_category AS pcat WHERE pcat.id=subcat_i_parent_category) AS pcat_name
								FROM cg_church_ring_category AS subcat WHERE subcat.i_parent_category > 0 {$where} ORDER BY pcat_name ASC
				 ";
          
        $res = $this->db->query($sql)->result_array();
        return $res;
    }
    
   public function show_member_ring_by_user($s_where=null,$i_start=null,$i_limit=null,$s_order_by=null)
	{
	try
        {
           // die('dd');
		  	$ret_=array();
                      //  $i_profile_id = intval(decrypt($CI->session->userdata('user_id')));
			
			//$language = get_current_language();
			
//			$s_qry = "(SELECT r.*, r.id AS ringid, c.s_category_name AS s_category_name, CONCAT(u.s_first_name,' ',u.s_last_name) AS owner_name,
//							(SELECT COUNT(id) FROM {$this->db->USER_RING_POST} WHERE i_ring_id=r.id) AS post,
//							(SELECT COUNT(cm.id) FROM {$this->db->USER_RING_POST} AS po, {$this->db->USER_RING_POST_COMMENTS} AS cm WHERE po.id=cm.i_ring_post_id AND po.i_ring_id=r.id) AS cmt,
//							(SELECT COUNT(lk.id) FROM {$this->db->USER_RING_POST} AS po, {$this->db->USER_RING_POST_LIKE} AS lk WHERE po.id=lk.i_ring_post_id AND po.i_ring_id=r.id) AS lik
//							FROM {$this->db->RING} r LEFT JOIN {$this->db->USERS} AS u 
//								ON r.i_user_id=u.id , {$this->db->RING_CAT} c WHERE r.i_category_id=c.id AND r.i_isenabled=1 "
//						.$s_where.") UNION".
//						"(SELECT r.*, r.id AS ringid, c.s_category_name AS s_category_name, CONCAT(u.s_first_name,' ',u.s_last_name) AS owner_name,
//							(SELECT COUNT(id) FROM {$this->db->USER_RING_POST} WHERE i_ring_id=r.id) AS post,
//							(SELECT COUNT(cm.id) FROM {$this->db->USER_RING_POST} AS po, {$this->db->USER_RING_POST_COMMENTS} AS cm WHERE po.id=cm.i_ring_post_id AND po.i_ring_id=r.id) AS cmt,
//							(SELECT COUNT(lk.id) FROM {$this->db->USER_RING_POST} AS po, {$this->db->USER_RING_POST_LIKE} AS lk WHERE po.id=lk.i_ring_post_id AND po.i_ring_id=r.id) AS lik
//							FROM {$this->db->RING} r LEFT JOIN {$this->db->USERS} AS u 
//								ON r.i_user_id=u.id , {$this->db->RING_CAT} AS c ,{$this->db->RING_INV_USER} AS inv 
//								WHERE r.i_category_id=c.id AND r.i_isenabled=1 AND inv.i_ring_id=r.id AND inv.i_joined = 1 ".$s_where1.")"; 
                
          //////////For Pagination///////////*don't change*/
          //$s_qry=str_replace("'","''",$s_qry);//for string operation in procedure
                       $s_qry = 'SELECT r.*,m.i_ring_id as rid, r.id AS ringid,c.s_category_name AS s_category_name  from cg_church_ring as r , cg_church_ring_category as c , cg_church_ring_invited_user as m  where r.i_category_id = c.id AND m.i_ring_id = r.id'.$s_where.'  ';
          $s_qry= $s_qry.(trim($s_order_by)!=""?" ORDER BY ".$s_order_by."":"ORDER BY ringid DESC")." ".(is_numeric($i_start) && is_numeric($i_limit)?" LIMIT ".intval($i_start).",".intval($i_limit):"");
		 
		 // echo ($s_qry);exit;
          //////////end For Pagination//////////                
                
          $this->db->trans_begin();///new                
          $rs=$this->db->query($s_qry); 
         // ;
          $i_cnt=0;
          if(is_array($rs->result()))
          {
             // pr($rs->result(),1);
              foreach($rs->result() as $row)
              {
                        $ret_[$i_cnt]["ringid"]				=	$row->ringid;////always integer
                      //die('s');
						$ret_[$i_cnt]["s_ring_name"]		=	$row->s_ring_name;
                        $ret_[$i_cnt]["s_description"]      =    $row->s_description;
						$ret_[$i_cnt]["i_user_id"]		    =	$row->i_user_id;
						$ret_[$i_cnt]["s_logo"]				=	$row->s_logo;
						
						$ret_[$i_cnt]["post_owner_user_id"]	=	$row->i_user_id;
						$ret_[$i_cnt]["owner_name"]			=	get_user_name_by_id($row->i_user_id);
//						$ret_[$i_cnt]["i_privacy"]  	   	=	$row->i_privacy; 
//						
//						$ret_[$i_cnt]["i_member"]  	   		=	$row->i_member; 
//						$ret_[$i_cnt]["s_state"]  	   		=	get_unformatted_string($row->s_state); 
//						$ret_[$i_cnt]["dt_created_on"]		=	$row->dt_created_on;
//						$ret_[$i_cnt]["i_country_id"]		=	intval($row->i_country_id);
						$ret_[$i_cnt]["dt_created_on"]		=	($row->dt_created_on);
						$ret_[$i_cnt]["s_category_name"]	=	$row->s_category_name;
//						$ret_[$i_cnt]["post"]				=	$row->post;
//						$ret_[$i_cnt]["cmt"]				=	$row->cmt;
//						$ret_[$i_cnt]["lik"]				=	$row->lik;
//						
						$ret_[$i_cnt]["i_sub_category_id"]		=	$row->i_sub_category_id;

                  $i_cnt++;
              }    
              $rs->free_result();          
          }
          $this->db->trans_commit();///new
		  
		  					
		  
          unset($s_qry,$rs,$row,$i_cnt,$s_where,$i_start,$i_limit);
          return $ret_;
         // pr($res_,1);
		    }
			catch(Exception $err_obj)
			{
				show_error($err_obj->getMessage());
			}           
    
    
    }
	
	
	public function gettotal_member_ring_by_user($s_where)
    {
           // die('dd');
        try
        {
          $ret_=0;
         
				
//		  $s_qry = "SELECT COUNT(tab.ringid) AS i_total FROM ((SELECT r.*, r.id AS ringid, c.s_category_name AS s_category_name, CONCAT(u.s_first_name,' ',u.s_last_name) AS owner_name
//							FROM {$this->db->RING} r LEFT JOIN {$this->db->USERS} AS u 
//								ON r.i_user_id=u.id , {$this->db->RING_CAT} c WHERE r.i_category_id=c.id AND r.i_isenabled=1 "
//						.$s_where.") UNION".
//						"(SELECT r.*, r.id AS ringid, c.s_category_name AS s_category_name, CONCAT(u.s_first_name,' ',u.s_last_name) AS owner_name
//							FROM {$this->db->RING} r LEFT JOIN {$this->db->USERS} AS u 
//								ON r.i_user_id=u.id , {$this->db->RING_CAT} AS c ,{$this->db->RING_INV_USER} AS inv 
//								WHERE r.i_category_id=c.id AND r.i_isenabled=1 AND inv.i_ring_id=r.id AND inv.i_joined = 1 ".$s_where1.")) AS tab"; 
          #echo $s_qry;
           $s_qry = 'SELECT count(*) as i_total from cg_church_ring as r , cg_church_ring_category as c,cg_church_ring_invited_user as m  where r.i_category_id = c.id AND  m.i_ring_id = r.id '.$s_where.' ';
          // echo ($s_qry);exit;
		  $rs=$this->db->query($s_qry);
          $i_cnt=0;
         // pr($rs->result(),1);
          if(is_array($rs->result()))
          {
              foreach($rs->result() as $row)
              {
                  $ret_=intval($row->i_total); 
              }    
              $rs->free_result();          
          }
          $this->db->trans_commit();///new
          unset($s_qry,$rs,$row,$i_cnt,$s_where);
          return $ret_;
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }           
    }
	
	


}   // end of class definition...
