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
class Contacts_model extends Base_model implements InfModel
{

        # constructor definition...
	 public function __construct() 
	{
		try
        {
          parent::__construct();
          $this->conf =get_config();
		  $this->load->model('users_model');
		  $this->load->model('netpals_model');
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
    public function fetch_multi($s_where=null,$i_start=null,$i_limit=null,$s_order_by=null)
    {
	 
		try
        {
		  	$ret_=array();
			
			$language = get_current_language();
			
			$s_qry = "SELECT   c.id, 
						   c.i_requester_id, 
						   c.i_accepter_id,
						   c.i_deleted_by,
						   c.dt_created_on, 
						   c.dt_accepted_on, 
						   u.id user_id, 
						   u.s_email,
						  
						   u.s_last_name,
						   u.s_first_name ,
						  
						   u.s_profile_photo,
						   u.e_gender,
						   u.i_country_id, 
						   u.i_user_type,
						   u.s_city,
						   u.s_state,
						   u.i_status,
						   u.dt_created_on
						   
						   
					FROM 
						{$this->db->USER_CONTACTS} c ,{$this->db->USERS} u "
						.$s_where; 
					
		/*cn.s_country_name {$this->db->MST_COUNTRY} cn*/
                
          //////////For Pagination///////////*don't change*/
          //$s_qry=str_replace("'","''",$s_qry);//for string operation in procedure
          $s_qry= $s_qry.(trim($s_order_by)!=""?" ORDER BY ".$s_order_by."":"ORDER BY id asc")." ".(is_numeric($i_start) && is_numeric($i_limit)?" LIMIT ".intval($i_start).",".intval($i_limit):"");
		 
	#echo ($s_qry);
          //////////end For Pagination//////////                
                
          $this->db->trans_begin();///new                
          $rs=$this->db->query($s_qry); 
          $i_cnt=0;
          if(is_array($rs->result()))
          {
              foreach($rs->result() as $row)
              {
                        $ret_[$i_cnt]["id"]				=	$row->id;////always integer
						$ret_[$i_cnt]["i_requester_id"]		=	intval($row->i_requester_id);
						$ret_[$i_cnt]["i_accepter_id"]		=	intval($row->i_accepter_id);
						$ret_[$i_cnt]["user_id"]		=	intval($row->user_id);
						$ret_[$i_cnt]["s_first_name"]		=	get_unformatted_string($row->s_first_name);
						$ret_[$i_cnt]["s_last_name"]				=	get_unformatted_string($row->s_last_name); 
 
						$ret_[$i_cnt]["s_displayname"]		=	$ret_[$i_cnt]["s_first_name"].' '.$ret_[$i_cnt]["s_last_name"]	; 
						$ret_[$i_cnt]["s_email"]  	   			 =	get_unformatted_string($row->s_email); 
						
						$ret_[$i_cnt]["s_profile_photo"]  	    =	get_unformatted_string($row->s_profile_photo); 
						$ret_[$i_cnt]["s_gender"]				=	($row->e_gender == 'M')?'Male':'Female';
						$ret_[$i_cnt]["s_city"]  	   			 =	get_unformatted_string($row->s_city); 
						$ret_[$i_cnt]["s_state"]  	   			 =	get_unformatted_string($row->s_state); 
				#causing prob 		#$ret_[$i_cnt]["s_country_name"]  	    =	get_unformatted_string($row->s_country_name); 
						$ret_[$i_cnt]["i_user_type"]			=	intval($row->i_user_type);
						$ret_[$i_cnt]["i_country_id"]			=	intval($row->i_country_id);
						$ret_[$i_cnt]["dt_created_on"]			=	$row->dt_created_on;
						
						
					    $if_friend = $this->users_model->if_already_friend(intval(decrypt($this->session->userdata('user_id'))) , $ret_[$i_cnt]['user_id']);
						if(count($if_friend)>0)
						{
							$ret_[$i_cnt]['if_already_friend']     ='true';
						}
						else{
							$ret_[$i_cnt]['if_already_friend']     ='false';
						}
				   $arr_already_netpal=$this->netpals_model->if_already_netpal( intval(decrypt($this->session->userdata('user_id'))) , $ret_[$i_cnt]['user_id']);
					  if(count($arr_already_netpal)>0 )
						  $ret_[$i_cnt]['already_added_netpal']='true';
					  else
						  $ret_[$i_cnt]['already_added_netpal']='false';
						
						
				 
                 
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
         
				
		  $s_qry = "SELECT COUNT(*) AS i_total 
					FROM 
						{$this->db->USER_CONTACTS} c, {$this->db->USERS} u
					".$s_where;
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
    {
        try
        {
            $i_ret_=0; ////Returns false
            if(!empty($info))
            {
			
				
                $s_qry="INSERT INTO ".$this->db->USER_CONTACTS." SET ";
                $s_qry.=" i_requester_id=? ";
				$s_qry.=", i_accepter_id=? ";
				$s_qry.=", s_status=? ";
                $s_qry.=", dt_created_on=? ";
                
                $this->db->trans_begin();///new   
				$this->db->query($s_qry,array(
											  intval($info["i_requester_id"]),
											  intval($info["i_accepter_id"]),
											  ($info["s_status"]),
											  get_db_datetime()
											 ));
                $i_ret_=$this->db->insert_id();     
                if($i_ret_)
                {
					$logi["msg"]="Inserting into ".$this->db->USER_CONTACTS." ";
					$logi["sql"]= serialize(array($s_qry,array(
											  intval($info["i_requester_id"]),
											  intval($info["i_accepter_id"]),
											  ($info["s_status"]),
											  get_db_datetime()
											 )) ) ;
                    $this->log_info($logi); 
                    unset($logi);
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

    /***
    * Update records in db. As we know the table name 
    * we will not pass it into params.
    * 
    * @param array $info, array of fields(as key) with values,ex-$arr["field_name"]=value
    * @param int $i_id, id value to be updated used in where clause
    * @returns $i_rows_affected  on success and FALSE if failed 
    */
    public function edit_info($info,$i_id)
    {}    
	  
    /******
    * Deletes all or single record from db. 
    * For Master entries deletion only change the flag i_is_deleted. 
    *
    * @param int $i_id, id value to be deleted used in where clause 
    * @returns $i_rows_affected  on success and FALSE if failed 
    * 
    */
    public function delete_info($i_id)
    {
        try
        {
            $i_ret_=0;////Returns false
    
            if(intval($i_id)>0)
            {
				$s_qry="UPDATE  ".$this->db->USER_CONTACTS." ";
                $s_qry.="SET s_status='deleted' WHERE id=? ";
                
                $this->db->trans_begin();///new  
                $this->db->query($s_qry, array(intval($i_id)) );
                $i_ret_=$this->db->affected_rows();        
                if($i_ret_)
                {
                    $logi["msg"]="UPDATE ".$this->db->USER_CONTACTS." ";
                    $logi["sql"]= serialize(array($s_qry, array(intval($i_id))) ) ;
                    $this->log_info($logi); 
                    unset($logi);
                    $this->db->trans_commit();///new   
                }
                else
                {
                    $this->db->trans_rollback();///new
                }                                      
            }
            elseif(intval($i_id)==-1)////Deleting All
            {
				$s_qry="DELETE FROM ".$this->db->USERS." ";
                $this->db->trans_begin();///new
                $this->db->query($s_qry);
                $i_ret_=$this->db->affected_rows();        
                if($i_ret_)
                {
                    $logi["msg"]="Deleting all information from ".$this->db->USERS." ";
                    $logi["sql"]= serialize(array($s_qry) ) ;
                    $this->log_info($logi); 
                    unset($logi);
                    $this->db->trans_commit();///new   
                }
                else
                {
                    $this->db->trans_rollback();///new
                }            
            }
            unset($s_qry, $i_id);
            return $i_ret_;
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
	
  
  public function get_by_anyuser($i_user_id,$i_start="",$i_limit="") 
	{
		
       try
        {
		  $language = get_current_language();
		  $ret_=array();
		  
		  /*$sql    =   "SELECT * FROM(
                        (SELECT a.frnd_id f_id FROM {$this->db->dbprefix}frnd a WHERE a.user_id ='{$userid}')
                        UNION
                        (SELECT b.user_id f_id FROM {$this->db->dbprefix}frnd b WHERE b.frnd_id ='{$userid}')
                    ) tab INNER JOIN {$this->db->dbprefix}users ms ON ms.user_id=tab.f_id WHERE ms.status=1
                        AND f_id<>{$userid}  ";*/
		 	$s_qry = sprintf("( SELECT 1, 
						 	c.id, 
						   c.i_requester_id, 
						   c.i_accepter_id,
						   c.i_deleted_by,
						   c.dt_created_on, 
						   c.dt_accepted_on, 
						   u.id user_id, 
						   u.s_email,
						  
						   u.s_last_name,
						   u.s_first_name ,
						  
						   u.s_profile_photo,
						   u.e_gender,
						   u.i_country_id, 
						   u.i_user_type,
						   u.s_city,
						   u.s_state,
						   u.i_status,
						   u.dt_created_on
					FROM 
						{$this->db->USER_CONTACTS} c, {$this->db->USERS} u 
					WHERE 
						1
						AND c.s_status = 'accepted' 
						AND u.i_status=1 
						AND
						((c.i_requester_id = %s AND u.id=c.i_accepter_id ) 
						OR (c.i_accepter_id = %s AND u.id=c.i_requester_id )) )
				
				ORDER BY 1, dt_accepted_on DESC", 
						/*intval($i_user_id), 
						intval($i_user_id), , dt_created_on DESC*/
						intval($i_user_id), 
						intval($i_user_id));
						
				
						
			
		  if($i_limit>0)
		  
		   	 $s_qry .=" limit {$i_start}, {$i_limit}"; 
		        # echo nl2br($s_qry);		
          $this->db->trans_begin();///new                
          $rs=$this->db->query($s_qry);
          $i_cnt=0;
          if(is_array($rs->result()))
          {
              foreach($rs->result() as $row)
              {
                       
						$ret_[$i_cnt]["id"]				=	$row->id;////always integer
						$ret_[$i_cnt]["i_requester_id"]		=	intval($row->i_requester_id);
						$ret_[$i_cnt]["i_accepter_id"]		=	intval($row->i_accepter_id);
						$ret_[$i_cnt]["user_id"]		=	intval($row->user_id);
						$ret_[$i_cnt]["s_first_name"]		=	get_unformatted_string($row->s_first_name);
						$ret_[$i_cnt]["s_last_name"]				=	get_unformatted_string($row->s_last_name); 
 
						$ret_[$i_cnt]["s_displayname"]		=	$ret_[$i_cnt]["s_first_name"].' '.$ret_[$i_cnt]["s_last_name"]	; 
						$ret_[$i_cnt]["s_email"]  	   			 =	get_unformatted_string($row->s_email); 
						
						$ret_[$i_cnt]["s_profile_photo"]  	    =	get_unformatted_string($row->s_profile_photo); 
						$ret_[$i_cnt]["s_gender"]				=	($row->e_gender == 'M')?'Male':'Female';
						$ret_[$i_cnt]["s_city"]  	   			 =	get_unformatted_string($row->s_city); 
						$ret_[$i_cnt]["s_state"]  	   			 =	get_unformatted_string($row->s_state); 
				#causing prob 		#$ret_[$i_cnt]["s_country_name"]  	    =	get_unformatted_string($row->s_country_name); 
						$ret_[$i_cnt]["i_user_type"]			=	intval($row->i_user_type);
						$ret_[$i_cnt]["i_country_id"]			=	intval($row->i_country_id);
						$ret_[$i_cnt]["dt_created_on"]			=	($row->dt_created_on);
						
				 
				 
                  
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
	
	
	public function get_total_by_anyuser($i_user_id) {
	
	  $ret_=array();
	  
		$s_qry = sprintf("SELECT count(*) count
					
						  FROM 
						  {$this->db->USER_CONTACTS} c, {$this->db->USERS} u
						  WHERE 
						  1
						  AND c.s_status = 'accepted' 
						  AND u.i_status=1 
						  AND
						  ((c.i_requester_id = %s AND u.id=c.i_accepter_id ) 
						  OR (c.i_accepter_id = %s AND u.id=c.i_requester_id ))
						  ", 
						  /*intval($i_user_id), 
						  intval($i_user_id), */
						  intval($i_user_id), 
						  intval($i_user_id));
					
	  
	  $rs=$this->db->query($s_qry)->result_array();
	  
	  return $rs[0]['count'];
	}
  
  
  
  
  
   	public function update_by_requester_accepter($arr=array(), $i_requester_id, $i_accepter_id)
	{
		if(count($arr)==0) {
			return null;
		}
		$arr['dt_accepted_on'] = get_db_datetime();
		$_ret = $this->db->update('user_contacts', $arr, array('i_requester_id'=>$i_requester_id, 'i_accepter_id'=>$i_accepter_id));
        $q_count="(SELECT count(*) AS count FROM cg_user_contacts WHERE `i_requester_id`=".$i_accepter_id." AND `i_accepter_id`=".$i_requester_id." AND `i_deleted_by`=1)";
        $res=$this->db->query($q_count)->row_array();;
        if($res['count'])
         {
            
             $_ret = $this->db->update('user_contacts', $arr, array('i_requester_id'=>$i_accepter_id, 'i_accepter_id'=>$i_requester_id));
         }   
       
       
        //$_ret = $this->db->update('user_contacts', $arr, array('i_requester_id'=>$i_accepter_id, 'i_accepter_id'=>$i_requester_id));
		return $_ret;
	}
  
  
  
   /* add info for sending invitation mail */
    public function add_invitation_info($info)
    {
        try
        {
            $i_ret_=0; ////Returns false
            if(!empty($info))
            {
			
				
                $s_qry="INSERT INTO ".$this->db->INVITATIONS." SET ";
                $s_qry.="  i_user_id=? ";
				$s_qry.=", s_firstname=? ";
				$s_qry.=", s_lastname=? ";
				$s_qry.=", s_email=? ";
				$s_qry.=", i_country_id=? ";
				$s_qry.=", i_entity_id=? ";
				$s_qry.=", s_message=? ";
				$s_qry.=", dt_created_on=? ";
                
                $this->db->trans_begin();///new   
				$this->db->query($s_qry,array(
											  intval($info["i_user_id"]),
											  get_formatted_string($info["s_firstname"]),
											  get_formatted_string($info["s_lastname"]),
											  get_formatted_string($info["s_email"]),
											  intval($info["i_country_id"]),
											  intval($info["i_entity_id"]),
											  get_formatted_string($info["s_message"]),
											  get_db_datetime()
											 ));
                $i_ret_=$this->db->insert_id();     
                if($i_ret_)
                {
					$logi["msg"]="Inserting into ".$this->db->INVITATIONS." ";
					$logi["sql"]= serialize(array($s_qry,array(
											  intval($info["i_user_id"]),
											  get_formatted_string($info["s_firstname"]),
											  get_formatted_string($info["s_lastname"]),
											  get_formatted_string($info["s_email"]),
											  intval($info["i_country_id"]),
											  intval($info["i_entity_id"]),
											  get_formatted_string($info["s_message"]),
											  get_db_datetime()
											 )) ) ;
                    $this->log_info($logi); 
                    unset($logi);
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
	
	
	
	
	
	
	public function cancel_friend_request_sent($arr=array())
	{
		if(count($arr)==0) {
			return null;
		}
		#$arr['dt_accepted_on'] = get_db_datetime();
		$_ret = $this->db->update('user_contacts', array('i_deleted_by'=>2,'s_status'=>'deleted'),$arr);
		
		#echo $this->db->last_query();
		return $_ret;
	}  
	
	
	 public function decline_friend_request_recieved($arr=array())
	 {
		if(count($arr)==0) {
			return null;
		}
		#$arr['dt_accepted_on'] = get_db_datetime();
		$_ret = $this->db->update('user_contacts', array('i_deleted_by'=>2 ,'s_status'=>'rejected'),$arr);
		
		#echo $this->db->last_query();
		return $_ret;
	}  
	
	public function get_friendsId_by_user_id($i_user_id)
	{
		
		try
        {
		  	$ret_=array();
			
			$language = get_current_language();
			$s_where = "WHERE 
						1
						AND c.s_status = 'accepted' 
						AND u.i_status=1 
						AND i_deleted_by = 1
						AND
						((c.i_requester_id = '".$i_user_id."' AND u.id=c.i_accepter_id ) 
						OR (c.i_accepter_id = '".$i_user_id."' AND u.id=c.i_requester_id ))";
			
		   $s_qry = "SELECT   c.id, c.i_requester_id, c.i_accepter_id
					 FROM 
						{$this->db->USER_CONTACTS} c ,{$this->db->USERS} u "
						.$s_where; 
        
		  #echo nl2br($s_qry);
                
          $this->db->trans_begin();///new                
          $rs=$this->db->query($s_qry); 
          $i_cnt=0;
          if(is_array($rs->result()))
          {
              foreach($rs->result() as $row)
              {
				  	if($i_user_id == $row->i_requester_id){
                        $ret_[$i_cnt]			=	$row->i_accepter_id;////always integer
						                 
					}else if($i_user_id == $row->i_accepter_id){
						$ret_[$i_cnt]			=	$row->i_requester_id;
					}
                  $i_cnt++;
              }    
              $rs->free_result();          
          }
          $this->db->trans_commit();///new
		  
		  #pr(array_unique($ret_));				
		  
          unset($s_qry,$rs,$row,$i_cnt,$s_where,$i_start,$i_limit);
          return array_unique($ret_);
		    }
		catch(Exception $err_obj)
		{
			show_error($err_obj->getMessage());
		}           
		
	}
	
	
	public function fetch_multi_online_friends($s_where=null,$i_start=null,$i_limit=null,$s_order_by=null)
    {
	 
		try
        {
		  	$ret_=array();
			$s_qry = "SELECT   c.id, 
						   c.i_requester_id, 
						   c.i_accepter_id,
						   c.i_deleted_by,
						   c.dt_created_on, 
						   c.dt_accepted_on, 
						   u.id user_id, 
						   u.s_email,
						  
						   u.s_last_name,
						   u.s_first_name ,
						  
						   u.s_profile_photo,
						   u.e_gender,
						   u.i_country_id, 
						   u.i_user_type,
						   u.s_city,
						   u.s_state,
						   u.i_status,
						   u.dt_created_on,
						   uon.s_status 
						   
					FROM 
						{$this->db->USER_CONTACTS} c ,{$this->db->USERS} u 
						LEFT JOIN cg_users_online AS uon ON u.id = uon.i_user_id "
						.$s_where; 
					
		  /*cn.s_country_name {$this->db->MST_COUNTRY} cn*/
                
          //////////For Pagination///////////*don't change*/
          //$s_qry=str_replace("'","''",$s_qry);//for string operation in procedure
          $s_qry= $s_qry.(trim($s_order_by)!=""?" ORDER BY ".$s_order_by."":"ORDER BY id asc")." ".(is_numeric($i_start) && is_numeric($i_limit)?" LIMIT ".intval($i_start).",".intval($i_limit):"");
		 
	
          //////////end For Pagination//////////                
                
          $this->db->trans_begin();///new                
          $rs=$this->db->query($s_qry); 
          $i_cnt=0;
		  $this->load->model('users_model');
          if(is_array($rs->result()))
          {
              foreach($rs->result() as $row)
              {
                        $ret_[$i_cnt]["id"]				=	$row->id;////always integer
						$ret_[$i_cnt]["i_requester_id"]		=	intval($row->i_requester_id);
						$ret_[$i_cnt]["i_accepter_id"]		=	intval($row->i_accepter_id);
						$ret_[$i_cnt]["user_id"]		=	intval($row->user_id);
						$ret_[$i_cnt]["s_first_name"]		=	get_unformatted_string($row->s_first_name);
						$ret_[$i_cnt]["s_last_name"]				=	get_unformatted_string($row->s_last_name); 
 
						$ret_[$i_cnt]["s_displayname"]		=	$ret_[$i_cnt]["s_first_name"].' '.$ret_[$i_cnt]["s_last_name"]	; 
						$ret_[$i_cnt]["s_email"]  	   			 =	get_unformatted_string($row->s_email); 
						
						$ret_[$i_cnt]["s_profile_photo"]  	    =	get_unformatted_string($row->s_profile_photo); 
						$ret_[$i_cnt]["s_gender"]				=	($row->e_gender == 'M')?'Male':'Female';
						$ret_[$i_cnt]["s_city"]  	   			 =	get_unformatted_string($row->s_city); 
						$ret_[$i_cnt]["s_state"]  	   			 =	get_unformatted_string($row->s_state); 
				#causing prob 		#$ret_[$i_cnt]["s_country_name"]  	    =	get_unformatted_string($row->s_country_name); 
						$ret_[$i_cnt]["i_user_type"]			=	intval($row->i_user_type);
						$ret_[$i_cnt]["i_country_id"]			=	intval($row->i_country_id);
						$ret_[$i_cnt]["dt_created_on"]			=	($row->dt_created_on);
						
						$if_friend = $this->users_model->if_already_friend(intval(decrypt($this->session->userdata('user_id'))) , $ret_[$i_cnt]['user_id']);
						if(count($if_friend)>0 || (intval(decrypt($this->session->userdata('user_id'))) == $ret_[$i_cnt]['user_id']))
						{
							$ret_[$i_cnt]['if_already_friend']     ='true';
						}
						else
							$ret_[$i_cnt]['if_already_friend']     ='false';
							
					   $arr_already_netpal=$this->netpals_model->if_already_netpal( intval(decrypt($this->session->userdata('user_id'))) , $ret_[$i_cnt]['user_id']);
                        if(count($arr_already_netpal)>0 || (intval(decrypt($this->session->userdata('user_id'))) == $ret_[$i_cnt]['user_id']))
                            $ret_[$i_cnt]['already_added_netpal']='true';
                        else
                            $ret_[$i_cnt]['already_added_netpal']='false';
							
				 
                 
                  $i_cnt++;
              }    
              $rs->free_result();          
          }
          $this->db->trans_commit();///new
		  
		  					
		  #pr($ret_);
          unset($s_qry,$rs,$row,$i_cnt,$s_where,$i_start,$i_limit);
          return $ret_;
		    }
			catch(Exception $err_obj)
			{
				show_error($err_obj->getMessage());
			}           
    
	}
    
	
	
	/****
    * Fetch Total records
    * @param string $s_where, ex- " status=1 AND deleted=0 " 
    * @returns int on success and FALSE if failed 
    */
    public function gettotal_online_friends($s_where=null)
    {
        try
        {
          $ret_=0;
         
				
		  $s_qry = "SELECT COUNT(*) AS i_total 
					     FROM(SELECT COUNT(*)  
					     FROM 
						{$this->db->USER_CONTACTS} c, {$this->db->USERS} u
						LEFT JOIN cg_users_online AS uon ON u.id = uon.i_user_id 
					".$s_where.") AS derived_tbl";
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
	
	
	# function to check if friend_request_already_sent
    function friend_request_already_sent($i_requester_id='', $i_accepter_id = '')
    {
      	$SQL = sprintf("SELECT COUNT(*) AS `check_count` FROM %s WHERE `i_requester_id`='%s'  AND `i_accepter_id` = '%s' AND `s_status` = 'pending' ",
                        $this->db->USER_CONTACTS, $i_requester_id, $i_accepter_id);
        $ROW = $this->db->query($SQL)->row_array(); #echo $this->db->last_query(); exit;
        
        if( $ROW['check_count'] )
            return 1 ;
       else 
        return 0;
    } 
	
	# delete friend .
	 public function delete_friend($arr=array())
	 {
		if(count($arr)==0) {
			return null;
		}
		#$arr['dt_accepted_on'] = get_db_datetime();
		$SQL = sprintf("DELETE FROM %s WHERE (`i_requester_id`='{$arr['i_requester_id']}'  AND `i_accepter_id` = '{$arr['i_accepter_id']}' ) 
						OR (`i_requester_id`='{$arr['i_accepter_id']}'  AND `i_accepter_id` = '{$arr['i_requester_id']}' AND `s_status`='{$arr['s_status']}' ) ",
                        $this->db->USER_CONTACTS);
		
		$this->db->query($SQL);
		$ret_ =  $this->db->affected_rows(); 
		#echo $this->db->last_query(); 
		return $ret_;
	} 
	
	# function to check total pending prayer_partner sent
    function total_pending_friend_sent($i_requester_id='')
    {
      	$SQL = sprintf("SELECT COUNT(*) AS `check_count` FROM %s WHERE (`i_requester_id`='%s' ) AND `s_status` = 'pending' ",
                        $this->db->USER_CONTACTS, $i_requester_id);
        $ROW = $this->db->query($SQL)->row_array(); #echo $this->db->last_query(); exit;
        
        if( $ROW['check_count'] )
            return $ROW['check_count'] ;
       else 
        return 0;
    } 
  	
	# function to check total pending prayer_partner recieved
    function total_pending_friend_recieved($i_requester_id='')
    {
      	$SQL = sprintf("SELECT COUNT(*) AS `check_count` FROM %s WHERE (`i_accepter_id`='%s' ) AND `s_status` = 'pending' ",
                        $this->db->USER_CONTACTS, $i_requester_id);
        $ROW = $this->db->query($SQL)->row_array(); //echo $this->db->last_query(); 
        //echo  $ROW['check_count'];
        if( $ROW['check_count'] )
            return $ROW['check_count'] ;
       else 
        return 0;
    } 
	
   public function get_pending_friend_recieved_notification($s_where=null,$i_start=null,$i_limit=null,$s_order_by=null)
    {
	 
		try
        {
		  	$ret_=array();

			  $s_qry = "SELECT   c.id, 
						   c.i_requester_id, 
						   c.i_accepter_id,
						   c.i_deleted_by,
						   c.s_status,
						   c.dt_created_on, 
						   c.dt_accepted_on,
						   c.i_notification_shown
						  					   
						   
					FROM 
						{$this->db->USER_CONTACTS} c 
						"
						.$s_where; 
					
		/*cn.s_country_name {$this->db->MST_COUNTRY} cn*/
                
          //////////For Pagination///////////*don't change*/
          //$s_qry=str_replace("'","''",$s_qry);//for string operation in procedure
          $s_qry= $s_qry.(trim($s_order_by)!=""?" ORDER BY ".$s_order_by."":"ORDER BY id asc")." ".(is_numeric($i_start) && is_numeric($i_limit)?" LIMIT ".intval($i_start).",".intval($i_limit):"");
		 
	#echo nl2br($s_qry);
          //////////end For Pagination//////////                
                
          $this->db->trans_begin();///new                
          $rs=$this->db->query($s_qry); 
          $i_cnt=0;
          if(is_array($rs->result()))
          {
              foreach($rs->result() as $row)
              {
                        $ret_[$i_cnt]["id"]				=	$row->id;////always integer
						$ret_[$i_cnt]["i_requester_id"]		=	intval($row->i_requester_id);
						$ret_[$i_cnt]["i_accepter_id"]		=	intval($row->i_accepter_id);
						$ret_[$i_cnt]["s_status"] = $row->s_status;
						$ret_[$i_cnt]["dt_created_on"]			=	($row->dt_created_on);
						$ret_[$i_cnt]["i_notification_shown"]			=	($row->i_notification_shown);
						
						
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


	public function get_total_friends_by_user($i_user_id) {
	
	  $ret_=array();
	  
	  $s_qry = " SELECT COUNT(*) count FROM ( 
							  
							  SELECT u.id
							  FROM 
							  {$this->db->USER_CONTACTS} c, {$this->db->USERS} u
							  WHERE 
							  1
							  AND c.s_status = 'accepted' 
							  AND u.i_status=1 
							  AND
							  ((c.i_requester_id = {$i_user_id} AND u.id=c.i_accepter_id ) 
							  OR (c.i_accepter_id = {$i_user_id} AND u.id=c.i_requester_id ))
							  GROUP BY u.id
							 ) as derived_tbl  ";
	  
	  		$rs=$this->db->query($s_qry)->result_array();
	  		
	  		return $rs[0]['count'];
	}
   public function fetch_contacts($uid)
   {
	   $s_qry = "SELECT * FROM {$this->db->contacts} 
						WHERE i_user_id='".$uid."'"; 
	   $rs=$this->db->query($s_qry)->result_array();
	   return $rs;
   }
   
   public function get_mutual_friends_by_user($i_user_id) {
	
	  $ret_=array();
	  ### get all friends
	$s_qry =  " SELECT  group_concat(DISTINCT u.id separator ',') as user_id
							  FROM 
							  {$this->db->USER_CONTACTS} c, {$this->db->USERS} u
							  WHERE 
							  1
							  AND c.s_status = 'accepted' 
							  AND u.i_status=1 
							  AND
							  ((c.i_requester_id = {$i_user_id} AND u.id=c.i_accepter_id ) 
							  OR (c.i_accepter_id = {$i_user_id} AND u.id=c.i_requester_id ))
							  
	   ";
	  $result=$this->db->query($s_qry)->result_array();
	  
	  $friends_id_str = $result[0]['user_id'].', '.$i_user_id;
	  
	  
	  ### fetching rest all users 
	   $rest_users =  " SELECT  u.id as user_id
	  						 FROM
	  						 {$this->db->USERS} u
							  WHERE 
							  1
							  AND u.i_status=1 
							  AND u.id NOT IN ({$friends_id_str})
							  
	   ";
	  // echo $rest_users;
	  $rest_user_arr = $this->db->query($rest_users)->result_array();
	 // pr($rest_user_arr,1);
	  
	  if(count($rest_user_arr)){
		  $rest_user_ID_arr = array();
		  foreach($rest_user_arr as $k=> $val){
			  $rest_user_ID_arr[] = $val['user_id'];
		  }
	  }
	  //pr($rest_user_ID_arr,1);
	  
	  if(count($rest_user_ID_arr)){
		  $mut=array();
		  $com=array();
		  $common=array();
		  foreach($rest_user_ID_arr as $k=> $val){
			  
			 $mutual_ids =  " SELECT  CONCAT_WS(',',u.id,u.s_first_name,u.s_last_name,u.e_gender,u.s_profile_photo) as user_id
								FROM 
								{$this->db->USER_CONTACTS} c, {$this->db->USERS} u
								WHERE 
								1
								AND c.s_status = 'accepted' 
								AND u.i_status=1 
								AND
								((c.i_requester_id = {$val} AND u.id=c.i_requester_id  AND  c.i_accepter_id in ({$result[0]['user_id']})) 
								OR (c.i_accepter_id = {$val} AND u.id=c.i_accepter_id  AND  c.i_requester_id in ({$result[0]['user_id']}))) Having (count(*)>0)
			   			  ";
						   
						  
			  $mutual_ids_arr = $this->db->query($mutual_ids)->result_array();
			 // $mut[]=$mutual_ids_arr['0'];
			$common_ids =  "SELECT  group_concat(DISTINCT u.id separator ',') as cuser_id
								FROM 
								{$this->db->USER_CONTACTS} c, {$this->db->USERS} u
								WHERE 
								1
								AND c.s_status = 'accepted' 
								AND u.i_status=1 
								AND
								((c.i_requester_id = {$val} AND u.id=c.i_accepter_id  AND  c.i_accepter_id in ({$result[0]['user_id']})) 
								OR (c.i_accepter_id = {$val} AND u.id=c.i_requester_id  AND  c.i_requester_id in ({$result[0]['user_id']}))) Having (count(*)>0)
			   			  ";
			  $common_ids_arr=$this->db->query($common_ids)->result_array();
			 // $com[]=$common_ids_arr['0'];
			  if($common_ids_arr['0']['cuser_id'] != '' && $mutual_ids_arr['0']['user_id'])
			  {
				$common[$mutual_ids_arr['0']['user_id']]=$common_ids_arr['0']['cuser_id'];
			  }
		  }
	  }
	 // pr($common);  
  return $common;
  
	}


  public function get_mutual_friends_by_user_for_wall($i_user_id) {
      $s_qry = "select group_concat( tab1.user_id separator ',') as frnd_id from 
                  (
                      (select DISTINCT i_accepter_id as user_id
                                             from cg_user_contacts where (i_requester_id ='" . $i_user_id . "') 
                                             AND s_status='accepted' ORDER BY RAND())
                      UNION
                      (select DISTINCT i_requester_id as user_id
                                             from cg_user_contacts where (i_accepter_id='" . $i_user_id . "') 
                                             AND s_status='accepted' ORDER BY RAND())
                  ) as tab1";


      

      $result=$this->db->query($s_qry)->result_array();
      $frnds = explode(',', $result[0]['frnd_id']); 
      $frndcount = count($frnds);
      $j = 0;
      for($i=0;$i<3;$i++)
      {
          $s_qry1 = "select u.id user_id, 
                         u.s_email,
                         CONCAT(u.s_first_name ,' ', u.s_last_name) AS name,
                         u.s_profile_photo,
                         u.e_gender,
                         u.i_country_id, 
                         u.i_user_type,
                         u.s_city,
                         u.s_state,
                         u.i_status,
                         u.dt_created_on from 
                          (
                              (select DISTINCT i_accepter_id as user_id
                                                     from cg_user_contacts AS c  where (c.i_requester_id ='" . $frnds[$i] . "') 
                                                     AND s_status='accepted' AND i_accepter_id NOT IN('".$result[0]['frnd_id']."','".$i_user_id."')  ORDER BY RAND() LIMIT 0,1)
                              UNION
                              (select DISTINCT i_requester_id as user_id
                                                     from cg_user_contacts AS c where (c.i_accepter_id='" . $frnds[$i] . "') 
                                                     AND s_status='accepted' AND i_requester_id NOT IN('".$result[0]['frnd_id']."','".$i_user_id."') ORDER BY RAND() LIMIT 0,1)
                          ) as tab1, cg_users AS u WHERE u.id=tab1.user_id"; 

          $result1  = $this->db->query($s_qry1)->result_array();

          if(count($result1[0])>0)
          {
            $result1[0]['mutualfrnd'] = $this->get_number_of_mutual_friends($frnds[$i],$i_user_id);
            $ret[$j]      = $result1[0];
            $j++;
          }
          if(count($result1[1])>0)
          {
            $result1[1]['mutualfrnd'] = $this->get_number_of_mutual_friends($frnds[$i],$i_user_id);
            $ret[$j]      = $result1[1]; 
            $j++;
          }
          
      }
      return $ret;
  }

  public function get_number_of_mutual_friends($uid1,$uid2)
  {
      $s_qry1 = "select group_concat( tab1.user_id separator ',') as frnd_id from 
                  (
                      (select DISTINCT i_accepter_id as user_id
                                             from cg_user_contacts where (i_requester_id ='" . $uid1 . "') 
                                             AND s_status='accepted')
                      UNION
                      (select DISTINCT i_requester_id as user_id
                                             from cg_user_contacts where (i_accepter_id='" . $uid1 . "') 
                                             AND s_status='accepted')
                  ) as tab1";


      

      $result1=$this->db->query($s_qry1)->result_array();

      $s_qry2 = "select tab1.user_id AS frnd_id from 
                  (
                      (select GROUP_CONCAT(DISTINCT i_accepter_id) as user_id
                                             from cg_user_contacts where (i_requester_id ='" . $uid2 . "') 
                                             AND s_status='accepted' AND i_accepter_id IN (".$result1[0]['frnd_id']."))
                      UNION
                      (select GROUP_CONCAT(DISTINCT i_requester_id) as user_id
                                             from cg_user_contacts where (i_accepter_id='" . $uid2 . "') 
                                             AND s_status='accepted' AND i_requester_id IN (".$result1[0]['frnd_id']."))
                  ) as tab1";


      
      
      $result2=$this->db->query($s_qry2)->result_array();
      $mutualfrnd = count(explode(',', $result2[0]['frnd_id'])) + count(explode(',', $result2[1]['frnd_id']));
      
      return $mutualfrnd;
  }

   public function __destruct()
    {}   




}   // end of class definition...
