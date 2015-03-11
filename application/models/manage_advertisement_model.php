<?php
/*********
* Author: 
* Purpose:
*  Model For managing advertisement
* 
* @package 
* @subpackage 
* 
* @link InfModel.php 
* @link Base_model.php
* @link controllers/.php
* @link views/admin/site_settings/
*/
require_once(APPPATH.'models/base_model.php');
class Manage_advertisement_model extends Base_model implements InfModel
{
	private $tbl;///used for this class

     # constructor definition...
     public function __construct() 
     {
        try
        {
          parent::__construct();
		  $this->tbl = $this->db->ADVERTISEMENT;
          $this->conf = get_config();
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
    public function fetch_multi($s_where=null,$i_start=null,$i_limit=null,$s_order_by='i_order')
    {
		$s_qry=  "SELECT A.*, U.s_name, U.s_last_name FROM ".$this->tbl." AS A " 
				." LEFT JOIN  ".$this->db->ADMIN_USER." AS U ON  A.i_user_id = U.id";
        $s_qry.=  ($s_where!=""?$s_where:"" )
				  ." ORDER BY {$s_order_by} ".(is_numeric($i_start) && is_numeric($i_limit)?" Limit "
				  .intval($i_start).",".intval($i_limit):"" );
	#echo $s_qry;
		
		$rs=$this->db->query($s_qry);
		$i_cnt=0;
		
       	 
		if(is_array($rs->result()))
		{
			$ret_ = $rs->result_array();
			$rs->free_result();          
			
		}
		
		unset($s_qry,$rs,$s_where,$i_start,$i_limit); //pr($ret_,1);
		return $ret_;

	}
    
    /****
    * Fetch Total records
    * @param string $s_where, ex- " status=1 AND deleted=0 " 
    * @returns int on success and FALSE if failed 
    */
    public function gettotal_info($s_where=null)
    {
        try
        {
          $ret_=0;
            //$s_where = ( empty($s_where) )? " WHERE `i_parent_id` = 0 AND `i_is_active`=1 ": $s_where;
          $s_qry="SELECT count(*) as i_total FROM ".$this->tbl." AS A " 
				." LEFT JOIN  ".$this->db->ADMIN_USER." AS U ON  A.i_user_id = U.id "
                .$s_where;
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
          
          unset($s_qry,$rs,$row,$i_cnt,$s_where);
          return $ret_;
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
        
     }        
    

    /*******
    * Fetches One record from db for the id value.
    * 
    * @param int $i_id
    * @returns array
    */
    public function fetch_this($id)
    {
		$s_qry="SELECT * FROM ".$this->tbl
                    ." Where id=?";
                
                 $rs=$this->db->query($s_qry,array(intval($id)));
				 
        if($rs->num_rows()>0)
        {
         	foreach($rs->result() as $row)
            {
             	$ret_["id"]=$row->id;////always integer
				$ret_["s_title"]=get_unformatted_string($row->s_title);
				$ret_["s_desc"]=html_entity_decode(get_unformatted_string($row->s_desc),ENT_QUOTES,'utf-8');
                $ret_["s_url"]=stripslashes(htmlspecialchars_decode($row->s_url));
                $ret_["s_image"]=stripslashes(htmlspecialchars_decode($row->s_image));
				$ret_["dt_start_date"]=($row->dt_start_date);
				$ret_["dt_end_date"]=($row->dt_end_date);
				$ret_["i_status"]=intval($row->i_status);
                                $ret_["p_loc"]=$row->p_loc;
              }    
              $rs->free_result();          
          }
		  
		  return $ret_;
				 
	}            
        
    /***
    * Inserts new records into db. As we know the table name 
    * we will not pass it into params.
    * 
    * @param array $info, array of fields(as key) with values,ex-$arr["field_name"]=value
    * @returns $i_new_id  on success and FALSE if failed 
    */
    public function add_info($info)
    {
        try
        {
            $i_ret_=0; ////Returns false
            if(!empty($info))
            {
                $this->db->trans_begin();///new   
                $this->db->insert($this->tbl, $info);
                $i_ret_=$this->db->insert_id();     
                if($i_ret_)
                {
                    $this->db->trans_commit();///new   
                }
                else
                {
                    $this->db->trans_rollback();///new
                }
            }
			
           return $i_ret_;
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }          
    
    }            

    /***
    * Update records in db. As we know the table name 
    * we will not pass it into params.
    * 
    * @param array $info, array of fields(as key) with values,ex-$arr["field_name"]=value
    * @param int $i_id, id value to be updated used in where clause
    * @returns $i_rows_affected  on success and FALSE if failed 
    */
    public function edit_info($info,$id)
    {
        try
        {
			$i_ret_=0;////Returns false
            
            if(!empty($info))
            {
                $this->db->update($this->tbl, $info, array('id'=>$id));
                $i_ret_=$this->db->affected_rows();   
                if($i_ret_)
                {
                    $this->db->trans_commit();///new   
                }
                else
                {
                    $this->db->trans_rollback();///new
                }                                            
            }
            unset($s_qry);
            return $i_ret_;
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
              
    }    
      
    /******
    * Deletes all or single record from db. 
    * For Master entries deletion only change the flag i_is_deleted. 
    *
    * @param int $i_id, id value to be deleted used in where clause 
    * @returns $i_rows_affected  on success and FALSE if failed 
    * 
    */
    public function delete_info($id)
    {
		try
        {
				$s_qry="DELETE FROM ".$this->tbl." ";
                $s_qry.=" Where id=? ";
                $this->db->query($s_qry, array(intval($id)) );
                $i_ret_=$this->db->affected_rows(); 
				return $i_ret_;
		}
		catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
	}      

	 /****
    * Getting The Max Order For 
    * 
    * 
    */
	public function get_i_order()
	{
		try
		{
			$ret_=0;
			$s_qry="SELECT IFNULL(MAX(i_order),1) AS `max_i_order` FROM ".$this->tbl;
			 $rs=$this->db->query($s_qry);
			 //echo $this->db->last_query();
          $i_cnt=0;
          if(is_array($rs->result()))
          {
              foreach($rs->result() as $row)
              {
                  $ret_=intval($row->max_i_order); 
              }
              $rs->free_result();          
          }
          
          unset($s_qry,$rs,$row,$i_cnt,$s_where);
          return $ret_;
			
			
		}
		catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  
		
	}
    /****
    * Register a log for add,edit and delete operation
    * 
    * @param mixed $attr
    * @returns TRUE on success and FALSE if failed 
    */
    public function log_info($attr)
    {
        try
        {
            return $this->write_log($attr["msg"],decrypt($this->session->userdata("i_user_id")),($attr["sql"]?$attr["sql"]:""));
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }           
    } 
	
	
	public function change_adv_status($status ,$id) {
		
	  if($status !='' && $id !=''){	
		  $sql = sprintf( "UPDATE cg_advertisement SET `i_status` = '%s'
						   WHERE `id` ='%s'"
					  , $status, $id );
		  $this->db->query($sql); //echo $this->db->last_query();exit;
		  return true;
	  }
	}
	 
        
        /**********************today****************************************/	 
    function add_media_center_info($info){
         try
        {
            $i_ret_=0; ////Returns false
            //pr($info);
            //die();
            if(!empty($info))
            {
                $this->db->trans_begin();///new   
                $this->db->insert('cg_media_center_advertisement', $info);
                $i_ret_=$this->db->insert_id();     
                if($i_ret_)
                {
                    $this->db->trans_commit();///new   
                }
                else
                {
                    $this->db->trans_rollback();///new
                }
            }
			
           return $i_ret_;
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }          
    
    }         
    
    
    public function fetch_multi_media_center($s_where=null,$i_start=null,$i_limit=null,$s_order_by='i_order')
    {
		$s_qry=  "SELECT A.*, U.s_name, U.s_last_name FROM cg_media_center_advertisement AS A " 
				." LEFT JOIN  ".$this->db->ADMIN_USER." AS U ON  A.i_user_id = U.id";
        $s_qry.=  ($s_where!=""?$s_where:"" )
				  ." ORDER BY {$s_order_by} ".(is_numeric($i_start) && is_numeric($i_limit)?" Limit "
				  .intval($i_start).",".intval($i_limit):"" );
	//echo $s_qry;
		
		$rs=$this->db->query($s_qry);
		$i_cnt=0;
		
       	 
		if(is_array($rs->result()))
		{
			$ret_ = $rs->result_array();
			$rs->free_result();          
			
		}
		
		unset($s_qry,$rs,$s_where,$i_start,$i_limit); //pr($ret_,1);
		return $ret_;

	}
         public function gettotal_info_media_center($s_where=null)
    {
        try
        {
          $ret_=0;
            //$s_where = ( empty($s_where) )? " WHERE `i_parent_id` = 0 AND `i_is_active`=1 ": $s_where;
          $s_qry="SELECT count(*) as i_total FROM cg_media_center_advertisement AS A " 
				." LEFT JOIN  ".$this->db->ADMIN_USER." AS U ON  A.i_user_id = U.id "
                .$s_where;
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
         
          unset($s_qry,$rs,$row,$i_cnt,$s_where);
          return $ret_;
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
        
     } 
      public function fetch_this_media_center($id)
    {
		$s_qry="SELECT * FROM cg_media_center_advertisement
                     Where id=?";
                
                 $rs=$this->db->query($s_qry,array(intval($id)));
				 
        if($rs->num_rows()>0)
        {
         	foreach($rs->result() as $row)
            {
             	$ret_["id"]=$row->id;////always integer
				$ret_["s_title"]=get_unformatted_string($row->s_title);
				$ret_["s_desc"]=html_entity_decode(get_unformatted_string($row->s_desc),ENT_QUOTES,'utf-8');
                $ret_["s_url"]=stripslashes(htmlspecialchars_decode($row->s_url));
                $ret_["s_image"]=stripslashes(htmlspecialchars_decode($row->s_image));
				$ret_["dt_start_date"]=($row->dt_start_date);
				$ret_["dt_end_date"]=($row->dt_end_date);
				$ret_["i_status"]=intval($row->i_status);
                                $ret_["p_loc"]=$row->p_loc;
                                
                  
              }    
              $rs->free_result();          
          }
		  
		  return $ret_;
				 
	}  
        
         public function edit_info_media_center($info,$id)
    {
        try
        {
			$i_ret_=0;////Returns false
            
            if(!empty($info))
            {
                $this->db->update('cg_media_center_advertisement', $info, array('id'=>$id));
                $i_ret_=$this->db->affected_rows();   
                if($i_ret_)
                {
                    $this->db->trans_commit();///new   
                }
                else
                {
                    $this->db->trans_rollback();///new
                }                                            
            }
            unset($s_qry);
            return $i_ret_;
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
              
    } 
    
    
    public function change_adv_status_media($status ,$id) {
		
	  if($status !='' && $id !=''){	
		  $sql = sprintf( "UPDATE cg_media_center_advertisement SET `i_status` = '%s'
						   WHERE `id` ='%s'"
					  , $status, $id );
		  $this->db->query($sql); //echo $this->db->last_query();exit;
		  return true;
	  }
	}
//     public function delete_info_media($id)
//    {
//	
//	} 
    
    /*************************************************/
    
    public function __destruct()
    {}   

    
        
}   // end of class definition.....
