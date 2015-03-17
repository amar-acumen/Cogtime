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
class Netpals_model extends Base_model 
{

        # constructor definition...
	 public function __construct() 
	{
		try
        {
          parent::__construct();
          $this->conf =get_config();
		  $this->load->model('users_model');
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
    
    
    
    
    //=============================== show all nel pals connected ======================================
    public function fetch_multi_online_netpals($s_where=null,$i_start=null,$i_limit=null,$s_order_by=null)
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
                           u.s_about_me,
                           u.i_status,
                           u.dt_created_on,
                           uon.s_status    
                           
                    FROM 
                        {$this->db->NETPAL} c ,{$this->db->USERS} u 
                        LEFT JOIN cg_users_online AS uon ON u.id = uon.i_user_id "
                        .$s_where; 
                    
        /*cn.s_country_name {$this->db->MST_COUNTRY} cn*/
                
          //////////For Pagination///////////*don't change*/
          //$s_qry=str_replace("'","''",$s_qry);//for string operation in procedure
          $s_qry= $s_qry.(trim($s_order_by)!=""?" ORDER BY ".$s_order_by."":"ORDER BY id asc")." ".(is_numeric($i_start) && is_numeric($i_limit)?" LIMIT ".intval($i_start).",".intval($i_limit):"");
         
    //echo nl2br($s_qry);
          //////////end For Pagination//////////                
                
                        
          $rs=$this->db->query($s_qry); 
          $i_cnt=0;
		  $this->load->model('users_model');
          if(is_array($rs->result()))
          {
              foreach($rs->result() as $row)
              {
                        $ret_[$i_cnt]["id"]                =    $row->id;////always integer
                        $ret_[$i_cnt]["i_requester_id"]        =    intval($row->i_requester_id);
                        $ret_[$i_cnt]["i_accepter_id"]        =    intval($row->i_accepter_id);
                        $ret_[$i_cnt]["user_id"]        =    intval($row->user_id);
                        $ret_[$i_cnt]["s_first_name"]        =    get_unformatted_string($row->s_first_name);
                        $ret_[$i_cnt]["s_last_name"]                =    get_unformatted_string($row->s_last_name); 
 
                        $ret_[$i_cnt]["s_displayname"]        =    $ret_[$i_cnt]["s_first_name"].' '.$ret_[$i_cnt]["s_last_name"]    ; 
                        $ret_[$i_cnt]["s_email"]                      =    get_unformatted_string($row->s_email); 
                        
                        $ret_[$i_cnt]["s_profile_photo"]          =    get_unformatted_string($row->s_profile_photo); 
                        $ret_[$i_cnt]["s_gender"]                =    ($row->e_gender == 'M')?'Male':'Female';
                        $ret_[$i_cnt]["s_city"]                      =    get_unformatted_string($row->s_city); 
                        $ret_[$i_cnt]["s_state"]                      =    get_unformatted_string($row->s_state); 
                #causing prob         #$ret_[$i_cnt]["s_country_name"]          =    get_unformatted_string($row->s_country_name); 
                        $ret_[$i_cnt]["i_user_type"]            =    intval($row->i_user_type);
                        $ret_[$i_cnt]["i_country_id"]            =    intval($row->i_country_id);
                        $ret_[$i_cnt]["dt_created_on"]            =    intval($row->dt_created_on);
                        $ret_[$i_cnt]["s_about_me"]                      =    get_unformatted_string($row->s_about_me); 
                        
                        $arr_already_netpal=$this->if_already_netpal( intval(decrypt($this->session->userdata('user_id'))) , $ret_[$i_cnt]['user_id']);
                        if(count($arr_already_netpal)>0 || (intval(decrypt($this->session->userdata('user_id'))) == $ret_[$i_cnt]['user_id']))
                            $ret_[$i_cnt]['already_added_netpal']='true';
                        else
                            $ret_[$i_cnt]['already_added_netpal']='false';
							
							
						 $if_friend = $this->users_model->if_already_friend(intval(decrypt($this->session->userdata('user_id'))) , $ret_[$i_cnt]['user_id']);
						
						if(count($if_friend)>0)
                    	{
                          $ret_[$i_cnt]['if_already_friend']     ='true';
                        }
                       else{
                          $ret_[$i_cnt]['if_already_friend']     ='false';
					   }
                 
                 
                  $i_cnt++;
              }    
              $rs->free_result();          
          }
          
          
                              
          
          unset($s_qry,$rs,$row,$i_cnt,$s_where,$i_start,$i_limit);
          return $ret_;
            }
            catch(Exception $err_obj)
            {
                show_error($err_obj->getMessage());
            }           
    
    }
    
    
    public function gettotal_online_netpals($s_where=null)
    {
        try
        {
          $ret_=0;
         
                
          $s_qry = "SELECT COUNT(*) AS i_total  FROM(SELECT COUNT(*)  
                         FROM 
                        {$this->db->NETPAL} c, {$this->db->USERS} u
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
          
          unset($s_qry,$rs,$row,$i_cnt,$s_where);
          return $ret_;
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }           
    }     
    
    //========================================= end of show all netpals =============================================
    
    
    //========================================= net pals request sent ===============================================
    public function fetch_multi($s_where=null,$i_start=null,$i_limit=null,$s_order_by=null)
    {
	 
		try
        {
		  	$ret_=array();
			
			$language = get_current_language();
			
			$s_qry = "SELECT   n.id netpal_id, 
						   n.i_requester_id, 
						   n.i_accepter_id,
                           
						   n.i_deleted_by,
						   n.dt_created_on, 
						   n.dt_accepted_on, 
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
						{$this->db->USERS} u, {$this->db->NETPAL} n "
						.$s_where; 
					
		/*cn.s_country_name {$this->db->MST_COUNTRY} cn*/
                
          //////////For Pagination///////////*don't change*/
          //$s_qry=str_replace("'","''",$s_qry);//for string operation in procedure
          $s_qry= $s_qry.(trim($s_order_by)!=""?" ORDER BY ".$s_order_by."":"ORDER BY id asc")." ".(is_numeric($i_start) && is_numeric($i_limit)?" LIMIT ".intval($i_start).",".intval($i_limit):"");
		 
	//echo nl2br($s_qry);
          //////////end For Pagination//////////                
                
              
          $rs=$this->db->query($s_qry); 
    //echo nl2br($this->db->last_query());
          $i_cnt=0;
          if(is_array($rs->result()))
          {
              foreach($rs->result() as $row)
              {
                        //$ret_[$i_cnt]["id"]				=	$row->id;////always integer
                        $ret_[$i_cnt]["netpal_id"]            =    ($row->netpal_id);
						$ret_[$i_cnt]["i_requester_id"]		=	intval($row->i_requester_id);
						$ret_[$i_cnt]["i_accepter_id"]		=	intval($row->i_accepter_id);
						$ret_[$i_cnt]["user_id"]			=	intval($row->user_id);
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
						$ret_[$i_cnt]["dt_created_on"]            =    ($row->dt_created_on);
                        
                        $ret_[$i_cnt]["dt_accepted_on"]			=	($row->dt_accepted_on);
						
						
					$arr_already_netpal=$this->if_already_netpal( intval(decrypt($this->session->userdata('user_id'))) , $ret_[$i_cnt]['user_id']);
                        if(count($arr_already_netpal)>0)
                            $ret_[$i_cnt]['already_added_netpal']='true';
                        else
                            $ret_[$i_cnt]['already_added_netpal']='false';
							
							
					    $if_friend = $this->users_model->if_already_friend(intval(decrypt($this->session->userdata('user_id'))) , $ret_[$i_cnt]['user_id']);
						//echo $this->db->last_query(); exit;
						
						if(count($if_friend)>0)
                    	{
                          $ret_[$i_cnt]['if_already_friend']     ='true';
                        }
                       else{
                          $ret_[$i_cnt]['if_already_friend']     ='false';
					   }
                        
						
						        
				 
                 
                  $i_cnt++;
              }    
              $rs->free_result();          
          }
          
		  
		  					
		  
          unset($s_qry,$rs,$row,$i_cnt,$s_where,$i_start,$i_limit);
          return $ret_;
		    }
			catch(Exception $err_obj)
			{
				show_error($err_obj->getMessage());
			}           
    
	}
    
    
 //============================================= search netpal suggestion ========================================
    public function get_netpal_suggestion($s_where=null,$i_start=null,$i_limit=null,$s_order_by=null)
    {

        try
        {
              $ret_=array();
                                             /*c.id, 
                                   c.i_requester_id, 
                                   c.i_accepter_id,
                                 c.i_deleted_by,
                                    c.dt_created_on, 
                                 c.dt_accepted_on,    */
              $s_qry = "SELECT   
                                  
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
                                  
                                  
                                  u.e_want_net_pal,
                                  d.s_name ,
                                  uc.s_country_name
                           
                    FROM 
                        {$this->db->USERS} u  
                         LEFT JOIN {$this->db->MST_COUNTRY} AS uc  ON uc.id = u.i_country_id
                        LEFT JOIN {$this->db->DENOMINATION} AS d ON u.i_id_denomination = d.id "
                        .$s_where; 
                    
        /*cn.s_country_name {$this->db->MST_COUNTRY} cn*/
                
          //////////For Pagination///////////*don't change*/
          //$s_qry=str_replace("'","''",$s_qry);//for string operation in procedure
          $s_qry= $s_qry.(trim($s_order_by)!=""?" ORDER BY ".$s_order_by."":"ORDER BY id asc")." ".(is_numeric($i_start) && is_numeric($i_limit)?" LIMIT ".intval($i_start).",".intval($i_limit):"");
         
    //echo nl2br($s_qry); 
          //////////end For Pagination//////////                
                
                   
          $rs=$this->db->query($s_qry); 
    //pr($rs->result(),1);
          $i_cnt=0;
		  $this->load->model('users_model');
          if(is_array($rs->result()))
          {
              foreach($rs->result() as $row)
              {
                       /* $ret_[$i_cnt]["id"]                    =    $row->id;////always integer
                        $ret_[$i_cnt]["i_requester_id"]        =    intval($row->i_requester_id);
                        $ret_[$i_cnt]["i_accepter_id"]        =    intval($row->i_accepter_id);
                        */$ret_[$i_cnt]["user_id"]            =    intval($row->user_id);
                        $ret_[$i_cnt]["s_first_name"]        =    get_unformatted_string($row->s_first_name);
                        $ret_[$i_cnt]["s_last_name"]        =    get_unformatted_string($row->s_last_name); 
 
                        $ret_[$i_cnt]["s_displayname"]        =    $ret_[$i_cnt]["s_first_name"].' '.$ret_[$i_cnt]["s_last_name"]    ; 
                        $ret_[$i_cnt]["s_email"]                 =    get_unformatted_string($row->s_email); 
                        
                        $ret_[$i_cnt]["s_profile_photo"]      =    get_unformatted_string($row->s_profile_photo); 
                        $ret_[$i_cnt]["s_gender"]            =    ($row->e_gender == 'M')?'Male':'Female';
                        $ret_[$i_cnt]["s_city"]                 =    get_unformatted_string($row->s_city); 
                        $ret_[$i_cnt]["s_state"]                 =    get_unformatted_string($row->s_state); 
                        $ret_[$i_cnt]["s_country_name"]      =    get_unformatted_string($row->s_country_name); 
                        $ret_[$i_cnt]["s_denomination"]     =    get_unformatted_string($row->s_name);
                        $ret_[$i_cnt]["i_user_type"]        =    intval($row->i_user_type);
                        $ret_[$i_cnt]["i_country_id"]        =    intval($row->i_country_id);
                        $ret_[$i_cnt]["dt_created_on"]        =    intval($row->dt_created_on);
                        
                        $arr_=$this->get_status_me_him( intval(decrypt($this->session->userdata('user_id'))) , $ret_[$i_cnt]['user_id']);
                        if(count($arr_)>0)
                            $ret_[$i_cnt]['netpals_request_already_sent']      =   'true';
                        else
                            $ret_[$i_cnt]['netpals_request_already_sent']      =   'false';
                        
                        
                        $arr_already_netpal=$this->if_already_netpal( intval(decrypt($this->session->userdata('user_id'))) , $ret_[$i_cnt]['user_id']);
                        if(count($arr_already_netpal)>0)
                            $ret_[$i_cnt]['already_added_netpal']='true';
                        else
                            $ret_[$i_cnt]['already_added_netpal']='false';
							
							
					    $if_friend = $this->users_model->if_already_friend(intval(decrypt($this->session->userdata('user_id'))) , $ret_[$i_cnt]['user_id']);
						//echo $this->db->last_query(); exit;
						
						if(count($if_friend)>0)
                    	{
                          $ret_[$i_cnt]['if_already_friend']     ='true';
                        }
                       else{
                          $ret_[$i_cnt]['if_already_friend']     ='false';
					   }
                        
                 
 //pr($ret_);
                  $i_cnt++;
              }    
              $rs->free_result();          
          }
         
          
                              
          
          unset($s_qry,$rs,$row,$i_cnt,$s_where,$i_start,$i_limit);
          return $ret_;
            }
            catch(Exception $err_obj)
            {
                show_error($err_obj->getMessage());
            }           
    
    }
    
    
    
    public function gettotal_netpal_suggestion($s_where=null)
    {
        try
        {
          $ret_=0;
         
                
          $s_qry = "SELECT COUNT(*) AS i_total  FROM(SELECT COUNT(*) 
                        FROM 
                        {$this->db->USERS} u  
                         LEFT JOIN {$this->db->MST_COUNTRY} AS uc  ON uc.id = u.i_country_id
                        LEFT JOIN {$this->db->DENOMINATION} AS d ON u.i_id_denomination = d.id ".$s_where.") AS derived_tbl";
          $rs=$this->db->query($s_qry);
          
    //echo nl2br($s_qry); exit;
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
         
				
		  $s_qry = "SELECT COUNT(*) AS i_total  FROM(SELECT COUNT(*) AS i_total 
					FROM 
						{$this->db->NETPAL} n, {$this->db->USERS} u
					".$s_where.") AS netpal_total";
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
			
				
                $s_qry="INSERT INTO ".$this->db->NETPAL." SET ";
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
					$logi["msg"]="Inserting into ".$this->db->NETPAL." ";
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
    
    
    function netpal_request_already_sent($i_requester_id='', $i_accepter_id = '')
    {
          $SQL = "SELECT COUNT(*) AS `check_count` FROM ".$this->db->NETPAL." WHERE `i_requester_id`='".$i_requester_id."'  AND `i_accepter_id` = '".$i_accepter_id."' AND `s_status` = 'pending'  ";
        $ROW = $this->db->query($SQL)->row_array(); #echo $this->db->last_query(); exit;
        
        if( $ROW['check_count'] )
            return 1 ;
       else 
        return 0;
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
	
  
  
   ############# start ##########
	public function get_online_user($s_where=null,$i_start=null,$i_limit=null,$s_order_by=null){
		$s_qry = "SELECT   c.id, 
						   c.i_requester_id, 
						   c.i_accepter_id,
						   c.dt_created_on, 
						   c.dt_accepted_on, 
						   u.id user_id, 
						   u.s_email,
						   u.s_name,
						 
						   u.s_first_name ,
						   u.s_profile_photo,
						   u.s_gender, 
						   u.i_user_type,
						   u.s_city,
						   u.i_status, 
						   cn.s_country_name,
						   uon.s_ip 
					FROM 
						{$this->db->USER_CONTACTS} c, {$this->db->MST_COUNTRY} cn, {$this->db->USERS} u 
						LEFT JOIN zl_users_online AS uon ON u.id = uon.i_user_id ".$s_where;
						
          $s_qry= $s_qry.(trim($s_order_by)!=""?" ORDER BY ".$s_order_by."":"ORDER BY id asc")." ".(is_numeric($i_start) && is_numeric($i_limit)?" LIMIT ".intval($i_start).",".intval($i_limit):"");
						
		    
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
						$ret_[$i_cnt]["s_name"]		=	get_unformatted_string($row->s_name); 
						$ret_[$i_cnt]["s_first_name"]		=	get_unformatted_string($row->s_first_name); 
						$ret_[$i_cnt]["s_displayname"]		=	$ret_[$i_cnt]["s_name"]	; 
						$ret_[$i_cnt]["s_email"]  	   			 =	get_unformatted_string($row->s_email); 
						
						$ret_[$i_cnt]["s_profile_photo"]  	    =	get_unformatted_string($row->s_profile_photo); 
						$ret_[$i_cnt]["s_gender"]				=	$row->s_gender;
						$ret_[$i_cnt]["s_city"]  	   			 =	get_unformatted_string($row->s_city); 
						$ret_[$i_cnt]["s_country_name"]  	    =	get_unformatted_string($row->s_country_name); 
						$ret_[$i_cnt]["i_user_type"]			=	intval($row->i_user_type);
						
				 
                 
                  $i_cnt++;
              }    
              $rs->free_result();          
          }
          
		  
          unset($s_qry,$rs,$row,$i_cnt,$s_where,$i_start,$i_limit);
          return $ret_;
	}
	
	public function get_online_user_count($s_where){
		$ret_=0;
         
				
		  $s_qry = "SELECT COUNT(*) AS i_total 
					FROM 
						{$this->db->USER_CONTACTS} c, {$this->db->MST_COUNTRY} cn, {$this->db->USERS} u 
						LEFT JOIN zl_users_online AS uon ON u.id = uon.i_user_id 
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
          
          unset($s_qry,$rs,$row,$i_cnt,$s_where);
          return $ret_;
	}
	############# end ############
  
  
  
  
  	public function get_by_anyuser($i_user_id,$i_start="",$i_limit="") 
	{
		
       try
        {
		  $language = get_current_language();
		  $ret_=array();
		  
		 
		 	$s_qry = "( SELECT 1, 
						  c.id, 
						c.i_requester_id, 
						c.i_accepter_id,
						c.s_status,
						c.dt_created_on, 
						c.dt_accepted_on, 
						u.id user_id, 
						u.s_email,
						u.s_first_name, 
						u.s_last_name,
						u.s_profile_photo, 
						u.i_user_type,
						u.i_status, 
						cn.s_country_name 
					FROM 
						{$this->db->USER_CONTACTS} c, {$this->db->USERS} u, {$this->db->MST_COUNTRY} cn 
					WHERE 
						1
						AND c.s_status = 'accepted' 
						AND u.i_status=1 
						AND
						((c.i_requester_id = '".intval($i_user_id)."' AND u.id=c.i_accepter_id AND u.i_country_id=cn.id) 
						OR (c.i_accepter_id = '".intval($i_user_id)."' AND u.id=c.i_requester_id AND u.i_country_id=cn.id)) )
				
				ORDER BY 1, dt_accepted_on DESC, dt_created_on DESC";
						
						
						#u.s_first_name,
			
		  if($i_limit>0)
		  
		   	 $s_qry .=" limit {$i_start}, {$i_limit}"; 
		         
                   
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
						$ret_[$i_cnt]["s_last_name"]		=	get_unformatted_string($row->s_last_name); 
						$ret_[$i_cnt]["s_displayname"]		=	$ret_[$i_cnt]["s_first_name"] .' '.$ret_[$i_cnt]["s_last_name"]	; 
						$ret_[$i_cnt]["s_email"]  	   			 =	get_unformatted_string($row->s_email); 
						$ret_[$i_cnt]["s_profile_photo"]  	    =	get_unformatted_string($row->s_profile_photo); 
						$ret_["s_gender"]				=	($row->e_gender == 'M')?'Male':'Female';
						$ret_[$i_cnt]["s_country_name"]  	    =	get_unformatted_string($row->s_country_name); 
						$ret_[$i_cnt]["i_user_type"]			=	intval($row->i_user_type);
						
				 
				 
                  
                  $i_cnt++;
              }    
              $rs->free_result();          
          }
         
		  
		  					
		  
          unset($s_qry,$rs,$row,$i_cnt,$s_where,$i_start,$i_limit);
          return $ret_;
		    }
			catch(Exception $err_obj)
			{
				show_error($err_obj->getMessage());
			} 
	}
	
	
	public function get_total_by_anyuser($i_user_id) {
	
	  $language = get_current_language();
	  $ret_=array();
	  
		$s_qry = "SELECT count(*) count
				FROM 
					{$this->db->USER_CONTACTS} c, {$this->db->USERS} u, {$this->db->MST_COUNTRY} cn 
				WHERE 
					1
					AND c.s_status = 'accepted' 
					AND u.i_status=1 
					AND
					((c.i_requester_id = '".intval($i_user_id)."' AND u.id=c.i_accepter_id AND u.i_country_id=cn.id) 
					OR (c.i_accepter_id = '".intval($i_user_id)."' AND u.id=c.i_requester_id AND u.i_country_id=cn.id))
					";
					
	  
	  $rs=$this->db->query($s_qry)->result_array();
	  
	  return $rs[0]['count'];
	}
	
	
	public function get_status_me_him($i_me, $i_him) 
	{
			
			try
        	{
				$ret_=array();
				$language = get_current_language();
				if(intval($i_me) > 0 && intval($i_him) > 0 &&  intval($i_me)!=intval($i_him))
				{
					$s_qry = "SELECT 
									c.id, 
									c.i_requester_id, 
									c.i_accepter_id,
									c.s_status,
									c.dt_created_on, 
									c.dt_accepted_on, 
									u.id user_id, 
									u.s_email,

									u.s_first_name, 
									u.s_profile_photo, 
									u.i_user_type,
									u.i_status
									
					FROM 
							{$this->db->NETPAL} c, {$this->db->USERS} u 
				   WHERE 
					( (c.i_requester_id = '".$i_me."' and c.i_accepter_id = '".$i_him."')  OR (c.i_accepter_id = '".$i_me."' and c.i_requester_id = '".$i_him."') )
					AND u.id=c.i_requester_id
                    AND c.s_status='pending'
                     
					
			";
	
			
			
			                 
			  $rs=$this->db->query($s_qry);
            
                 
              
			  if(is_array($rs->result()))
			  {
				  foreach($rs->result() as $row)
				  {
					 
					    $ret_["id"]				=	$row->id;////always integer
						$ret_["i_requester_id"]		=	intval($row->i_requester_id);
						$ret_["i_accepter_id"]		=	intval($row->i_accepter_id);
						$ret_["user_id"]		=	intval($row->user_id);
						$ret_["s_status"]				=	$row->s_status;
						//$ret_["s_name"]		=	get_unformatted_string($row->s_name); 
						$ret_["s_email"]  	   			 =	get_unformatted_string($row->s_email); 
						$ret_["s_profile_photo"]  	    =	get_unformatted_string($row->s_profile_photo); 
						//$ret_["s_gender"]				=	$row->s_gender;
						//$ret_["s_country_name"]  	    =	get_unformatted_string($row->s_country_name); 
						$ret_["i_user_type"]			=	intval($row->i_user_type);
					
						
						// $ret_[$i_cnt]["dt_created_on"]	=   getShortDate($row->dt_created_on); 
			        
			  
				  }    
                
				  $rs->free_result();          
			  }
			 
			  unset($s_qry,$rs,$row,$i_me,$i_him);
			  #pr($ret_);
				
		  }
          return $ret_;
			
			
		
		  }
			catch(Exception $err_obj)
			{
				show_error($err_obj->getMessage());
			} 
		
	}
    
    
    //================================ if already added in net pal ===================================
    public function if_already_netpal($i_me, $i_him) 
    {
            
            try
            {
                $ret_=array();
                $language = get_current_language();
                if(intval($i_me) > 0 && intval($i_him) > 0 &&  intval($i_me)!=intval($i_him))
                {
                    $s_qry = "SELECT 
                                    c.id, 
                                    c.i_requester_id, 
                                    c.i_accepter_id,
                                    c.s_status,
                                    c.dt_created_on, 
                                    c.dt_accepted_on, 
                                    u.id user_id, 
                                    u.s_email,

                                    u.s_first_name, 
                                    u.s_profile_photo, 
                                    u.i_user_type,
                                    u.i_status
                                    
                    FROM 
                            {$this->db->NETPAL} c, {$this->db->USERS} u 
                   WHERE 
                    ( (c.i_requester_id = '".$i_me."' and c.i_accepter_id = '".$i_him."')  OR (c.i_accepter_id = '".$i_me."' and c.i_requester_id = '".$i_him."') )
                    AND u.id=c.i_requester_id
                    AND c.s_status='accepted'
                     
                    
            ";
    
            
            
                               
              $rs=$this->db->query($s_qry);
            
                 
              
              if(is_array($rs->result()))
              {
                  foreach($rs->result() as $row)
                  {
                     
                        $ret_["id"]                =    $row->id;////always integer
                        $ret_["i_requester_id"]        =    intval($row->i_requester_id);
                        $ret_["i_accepter_id"]        =    intval($row->i_accepter_id);
                        $ret_["user_id"]        =    intval($row->user_id);
                        $ret_["s_status"]                =    $row->s_status;
                        //$ret_["s_name"]        =    get_unformatted_string($row->s_name); 
                        $ret_["s_email"]                      =    get_unformatted_string($row->s_email); 
                        $ret_["s_profile_photo"]          =    get_unformatted_string($row->s_profile_photo); 
                        //$ret_["s_gender"]                =    $row->s_gender;
                        //$ret_["s_country_name"]          =    get_unformatted_string($row->s_country_name); 
                        $ret_["i_user_type"]            =    intval($row->i_user_type);
                    
                        
                        // $ret_[$i_cnt]["dt_created_on"]    =   getShortDate($row->dt_created_on); 
                    
              
                  }    
                
                  $rs->free_result();          
              }
              
              unset($s_qry,$rs,$row,$i_me,$i_him);
              #pr($ret_);
                
          }
          return $ret_;
            
            
        
          }
            catch(Exception $err_obj)
            {
                show_error($err_obj->getMessage());
            } 
        
    }

  
  
  
  //========================================= for accepting request from msg inbox ===============================================
   	public function update_by_requester_accepter($arr=array(), $i_requester_id, $i_accepter_id)
	{
		if(count($arr)==0) {
			return null;
		}
		$arr['dt_accepted_on'] = get_db_datetime();
		$_ret = $this->db->update('users_net_pal_contacts', $arr, array('i_requester_id'=>$i_requester_id, 'i_accepter_id'=>$i_accepter_id));
    //echo $this->db->last_query();
        
        $q_count="(SELECT count(*) AS count FROM cg_users_net_pal_contacts WHERE `i_requester_id`=".$i_accepter_id." AND `i_accepter_id`=".$i_requester_id." AND `i_deleted_by`=1)";
        $res=$this->db->query($q_count)->row_array();
        
        if($res['count'])
         {
            
             $_ret = $this->db->update('users_net_pal_contacts', $arr, array('i_requester_id'=>$i_accepter_id, 'i_accepter_id'=>$i_requester_id));
         }   
    
    
    
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
	
	
	
	public function remove_netpal_request_sent($arr=array())
    {
        if(count($arr)==0)
            return null;
       
       $SQL = "DELETE FROM ". $this->db->NETPAL." WHERE (`i_requester_id`='{$arr['i_requester_id']}'  AND `i_accepter_id` = '{$arr['i_accepter_id']}' ) 
                        OR (`i_requester_id`='{$arr['i_accepter_id']}'  AND `i_accepter_id` = '{$arr['i_requester_id']}' AND `s_status`='{$arr['s_status']}' ) ";
       
        
        $this->db->query($SQL);
        $ret_ =  $this->db->affected_rows(); 

    #echo $this->db->last_query();
        return $ret_;
    }
    
    
	
	
	public function cancel_netpal_request_sent($arr=array())
	{
		if(count($arr)==0) {
			return null;
		}

		#$arr['dt_accepted_on'] = get_db_datetime();
		$_ret = $this->db->update('users_net_pal_contacts', array('i_deleted_by'=>2,'s_status'=>'deleted'),$arr);
		
	#echo $this->db->last_query();
		return $_ret;
	}  
    
    function total_pending_netpal_sent($i_requester_id='')
    {
          $SQL = "SELECT COUNT(*) AS `check_count` FROM ".$this->db->NETPAL." WHERE (`i_requester_id`='".$i_requester_id."' ) AND `s_status` = 'pending' ";
        $ROW = $this->db->query($SQL)->row_array(); 
    //echo $this->db->last_query(); exit;
        
        if( $ROW['check_count'] )
            return $ROW['check_count'] ;
       else 
        return 0;
    } 
    
    function total_pending_netpal_received($i_accepter_id='')
    {
          $SQL = "SELECT COUNT(*) AS `check_count` FROM ".$this->db->NETPAL." WHERE (`i_accepter_id`='".$i_accepter_id."' ) AND `s_status` = 'pending' ";
        $ROW = $this->db->query($SQL)->row_array(); 
    //echo $this->db->last_query(); 
	//echo $ROW['check_count'];
        
        if( $ROW['check_count'] )
            return $ROW['check_count'] ;
       else 
        return 0;
    }
	
    
    
    
//=================================== decline netpal request ======================================
	 public function decline_netpal_request_recieved($arr=array())
	 {
		if(count($arr)==0) {
			return null;
		}

		#$arr['dt_accepted_on'] = get_db_datetime();
		$_ret = $this->db->update('users_net_pal_contacts', array('i_deleted_by'=>2 ,'s_status'=>'rejected'),$arr);
		
		#echo $this->db->last_query();
		return $_ret;
	}  
	
	public function get_netpalsId_by_user_id($i_user_id)
	{
		
		try
        {
		  	$ret_=array();
			
			$language = get_current_language();
			$s_where = "WHERE 
						1
						AND n.s_status = 'accepted' 
						AND u.i_status=1 
						AND i_deleted_by = 1
						AND
						((n.i_requester_id = '".$i_user_id."' AND u.id=n.i_accepter_id ) 
						OR (n.i_accepter_id = '".$i_user_id."' AND u.id=n.i_requester_id ))";
			
		   $s_qry = "SELECT   n.id, n.i_requester_id, n.i_accepter_id
					 FROM 
						{$this->db->NETPAL} n ,{$this->db->USERS} u "
						.$s_where; 
        
	//echo nl2br($s_qry);
                
            
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
         
		  
		  #pr(array_unique($ret_));				
		  
          unset($s_qry,$rs,$row,$i_cnt,$s_where,$i_start,$i_limit);
          return array_unique($ret_);
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
   
	
	
	# function to check if friend_request_already_sent
    function netpals_request_already_sent($i_requester_id='', $i_accepter_id = '')
    {
        
      	$SQL = "SELECT COUNT(*) AS `check_count` FROM ".$this->db->NETPAL." WHERE `i_requester_id`='".$i_requester_id."'  AND `i_accepter_id` = '".$i_accepter_id."' ";
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
		$SQL = "DELETE FROM ".$this->db->USER_CONTACTS." WHERE (`i_requester_id`='{$arr['i_requester_id']}'  AND `i_accepter_id` = '{$arr['i_accepter_id']}' ) 
						OR (`i_requester_id`='{$arr['i_accepter_id']}'  AND `i_accepter_id` = '{$arr['i_requester_id']}' AND `s_status`='{$arr['s_status']}' ) ";
		
		$this->db->query($SQL);
		$ret_ =  $this->db->affected_rows(); 
		#echo $this->db->last_query(); 
		return $ret_;
	} 
	
	# function to check total pending prayer_partner recieved
    function total_pending_netpal_recieved($i_requester_id='')
    {
      	$SQL = "SELECT COUNT(*) AS `check_count` FROM ".$this->db->NETPAL." WHERE (`i_accepter_id`='".$i_requester_id."' ) AND `s_status` = 'pending' ";
        $ROW = $this->db->query($SQL)->row_array(); #echo $this->db->last_query(); exit;
        
        if( $ROW['check_count'] )
            return $ROW['check_count'] ;
       else 
        return 0;
    }    
    
	
	public function get_pending_netpal_recieved_notification($s_where=null,$i_start=null,$i_limit=null,$s_order_by=null)
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
						{$this->db->NETPAL} c 
						"
						.$s_where; 
					
		/*cn.s_country_name {$this->db->MST_COUNTRY} cn*/
                
          //////////For Pagination///////////*don't change*/
          //$s_qry=str_replace("'","''",$s_qry);//for string operation in procedure
          $s_qry= $s_qry.(trim($s_order_by)!=""?" ORDER BY ".$s_order_by."":"ORDER BY id asc")." ".(is_numeric($i_start) && is_numeric($i_limit)?" LIMIT ".intval($i_start).",".intval($i_limit):"");
		 
	#echo nl2br($s_qry);
          //////////end For Pagination//////////                
                
             
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
          
		
		
		  
	     unset($s_qry,$rs,$row,$i_cnt,$s_where,$i_start,$i_limit);
	     return $ret_;
	  }
		catch(Exception $err_obj)
		{
			show_error($err_obj->getMessage());
		}           
	  
	}
  
   public function __destruct()
    {}   




}   // end of class definition...
