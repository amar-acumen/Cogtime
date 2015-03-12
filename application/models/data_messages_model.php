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
class Data_messages_model extends Base_model
{

        # constructor definition...
	 public function __construct() 
	{
		try
        {
          parent::__construct();
          $this->conf =get_config();
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
    {}
    
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
          $s_qry="SELECT COUNT(*) AS i_total "
                ."FROM ".$this->db->MESSAGES." u "
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
			$info['i_referred_media_id']= ($info['i_referred_media_id'] == '')?'0':$info['i_referred_media_id'];
            if(!empty($info))
            {
			
			
                $s_qry="INSERT INTO ".$this->db->MESSAGES." SET ";
				$s_qry.="  i_sender_id=? ";
				$s_qry.=", i_receiver_id=? ";
                $s_qry.=", s_subject=? ";
				$s_qry.=", s_message=? ";
				$s_qry.=", i_is_unread=? ";
                $s_qry.=", s_type=? ";
				$s_qry.=", dt_created_on=? ";
				$s_qry.=", i_is_deleted=? ";
				$s_qry.=", i_referred_media_id=? ";
                
                $this->db->trans_begin();///new   
				$this->db->query($s_qry,array(
				 							  intval($info["i_sender_id"]),
											  intval($info["i_receiver_id"]),
											  ($info["s_subject"]),
											  ($info["s_message"]),
											  1,
											  ($info["s_type"]),
											  get_db_datetime(),
											  0,
											  ($info['i_referred_media_id'])
											 ));
                $i_ret_=$this->db->insert_id();   
				#echo nl2br($this->db->last_query()) ;exit;  
                if($i_ret_)
                {
					$logi["msg"]="Inserting into ".$this->db->MESSAGES." ";
					$logi["sql"]= serialize(array($s_qry,array(
				 							  intval($info["i_sender_id"]),
											  intval($info["i_receiver_id"]),
											  ($info["s_subject"]),
											  ($info["s_message"]),
											  1,
											  ($info["s_type"]),
											  get_db_datetime(),
											  0,
											  ($info['i_referred_media_id'])
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
    {}      

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
	
	 public function __destruct()
    {}   

     
	 
	 public function get_by_receiver($i_user_id, $i_start_limit="", $i_no_of_page="", $s_where = '') 
	 {
		
		try
        {
			
		  	$ret_=array();
			if("$i_start_limit" == "") 
			  {
				 $s_qry = sprintf('SELECT 
										m.id, 
										m.i_sender_id, 
										m.i_receiver_id, 
										m.s_message, 
										m.i_referred_media_id,
										m.s_subject, 
										m.i_is_unread, 
										m.s_type, 
										m.i_ended,
										m.dt_created_on, 
										u.s_first_name, 
										u.s_last_name,
										u.s_profile_photo,
										u.e_gender
								FROM %smessages m, %susers u 
								WHERE u.id = m.i_sender_id AND m.i_receiver_id = %s AND m.i_is_deleted_by_receiver = 0 
								%s ORDER BY m.dt_created_on DESC', 
								$this->db->dbprefix, $this->db->dbprefix, $i_user_id, $s_where);
			}
			else 
			{
				  $s_qry = sprintf('SELECT m.id, 
										m.i_sender_id, 
										m.i_receiver_id, 
										m.s_message, 
										m.i_referred_media_id,
										m.s_subject, 
										m.i_is_unread, 
										m.s_type, 
										m.i_ended,
										m.dt_created_on, 
										u.s_first_name, 
										u.s_last_name,
										u.s_profile_photo,
										u.e_gender
								 FROM %smessages m, %susers u 
								 WHERE u.id = m.i_sender_id AND m.i_receiver_id = %s AND m.i_is_deleted_by_receiver = 0 %s
								 ORDER BY m.dt_created_on DESC limit %s, %s', $this->db->dbprefix, $this->db->dbprefix, $i_user_id,$s_where, $i_start_limit, $i_no_of_page); 
								 
								
			}
			
	//echo nl2br($s_qry);
		//query = $this->db->query($sql);
		//$result_arr = $query->result_array();

		//return $result_arr;
		
		
		          
          $rs=$this->db->query($s_qry);
          $i_cnt=0;
          if(is_array($rs->result()))
          {
              foreach($rs->result() as $row)
              {
					$ret_[$i_cnt]["id"]					=	$row->id;////always integer
					$ret_[$i_cnt]["s_first_name"]		=	get_unformatted_string($row->s_first_name); 
					$ret_[$i_cnt]["s_last_name"]			=	get_unformatted_string($row->s_last_name); 
					$ret_[$i_cnt]["s_displayname"]			=	$ret_[$i_cnt]["s_first_name"].' '.$ret_[$i_cnt]["s_last_name"]; 
					
					$ret_[$i_cnt]["i_sender_id"]		=	intval($row->i_sender_id); 
					$ret_[$i_cnt]["i_receiver_id"]		=	intval($row->i_receiver_id); 
					$ret_[$i_cnt]["i_referred_media_id"]	=	intval($row->i_referred_media_id); 
					
					$ret_[$i_cnt]["s_message"]			=	get_unformatted_string($row->s_message); 
					$ret_[$i_cnt]["s_subject"]			=	get_unformatted_string($row->s_subject); 
					$ret_[$i_cnt]["s_type"]				=	$row->s_type;
					$ret_[$i_cnt]["ended"]				=	$row->i_ended;
					
					$ret_[$i_cnt]["dt_created_on"]		=   $row->dt_created_on;
					$ret_[$i_cnt]["i_is_unread"]		=	intval($row->i_is_unread);  
					$ret_[$i_cnt]["s_profile_photo"]	=   $row->s_profile_photo;
					$ret_[$i_cnt]["e_gender"]		    =   $row->e_gender;
                  $i_cnt++;
              }    
              $rs->free_result();          
          }
          				
		  
          unset($s_qry,$rs,$row,$i_cnt,$s_where,$i_start_limit,$i_no_of_page);
          return $ret_;
		}
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }    
		
		
	}


	public function get_total_by_receiver($i_user_id , $s_where) 
	{
		$sql = sprintf("SELECT count(*) count FROM %smessages m, %susers u WHERE u.id = m.i_sender_id AND m.i_receiver_id = %s AND m.i_is_deleted_by_receiver = 0  %s ", $this->db->dbprefix, $this->db->dbprefix, $i_user_id,$s_where);		

		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}
	
	
	
	 public function get_by_sender($i_user_id, $i_start_limit="", $i_no_of_page="", $s_where ='') 
	 {
		
		try
        {
		  	$ret_=array();
			if("$i_start_limit" == "") 
			  {
				$s_qry = sprintf('SELECT 
										m.id, 
										m.i_sender_id, 
										m.i_receiver_id, 
										m.i_referred_media_id,
										m.s_message, 
										m.s_subject, 
										m.i_is_unread, 
										m.s_type, 
										m.dt_created_on, 
										m.i_ended, 
										u.s_first_name, 
										u.s_last_name 
								FROM %smessages m, %susers u 
								WHERE u.id = m.i_receiver_id AND m.i_sender_id = %s  AND m.s_type=\'normal\' AND m.i_is_deleted_by_sender = 0 %s ORDER BY m.dt_created_on DESC', 
								$this->db->dbprefix, $this->db->dbprefix, $i_user_id , $s_where);
			}
			else 
			{
				 $s_qry = sprintf('SELECT m.id, 
										m.i_sender_id, 
										m.i_receiver_id, 
										m.i_referred_media_id,
										m.s_message, 
										m.s_subject, 
										m.i_is_unread, 
										m.s_type, 
										m.dt_created_on, 
										m.i_ended, 
										u.s_first_name, 
										u.s_last_name 
								 FROM %smessages m, %susers u 
								 WHERE u.id = m.i_receiver_id AND m.i_sender_id = %s AND m.s_type=\'normal\'  AND m.i_is_deleted_by_sender = 0 %s ORDER BY m.dt_created_on DESC limit %s, %s', $this->db->dbprefix, $this->db->dbprefix, $i_user_id, $s_where, $i_start_limit, $i_no_of_page);
			}
	
		//query = $this->db->query($sql); AND m.s_type=\'normal\'
		//$result_arr = $query->result_array();

		//return $result_arr;
		//echo nl2br($s_qry);
		
		       
          $rs=$this->db->query($s_qry);
          $i_cnt=0;
          if(is_array($rs->result()))
          {
              foreach($rs->result() as $row)
              {
					
					$ret_[$i_cnt]["id"]					=	$row->id;////always integer
					$ret_[$i_cnt]["s_first_name"]		=	get_unformatted_string($row->s_first_name); 
					$ret_[$i_cnt]["s_last_name"]			=	get_unformatted_string($row->s_last_name); 
					$ret_[$i_cnt]["s_displayname"]			=	$ret_[$i_cnt]["s_first_name"].' '.$ret_[$i_cnt]["s_last_name"]; 
					
					$ret_[$i_cnt]["i_sender_id"]		=	intval($row->i_sender_id); 
					$ret_[$i_cnt]["i_receiver_id"]		=	intval($row->i_receiver_id); 
					$ret_[$i_cnt]["i_referred_media_id"]	=	intval($row->i_referred_media_id); 
					$ret_[$i_cnt]["s_message"]			=	get_unformatted_string($row->s_message); 
					$ret_[$i_cnt]["s_subject"]			=	get_unformatted_string($row->s_subject); 
					$ret_[$i_cnt]["s_type"]				=	$row->s_type;
					$ret_[$i_cnt]["ended"]				=	$row->i_ended;
					
					$ret_[$i_cnt]["dt_created_on"]		=   $row->dt_created_on;
					$ret_[$i_cnt]["i_is_unread"]		=	intval($row->i_is_unread);  
				
                  $i_cnt++;
              }    
              $rs->free_result();          
          }
          					
		  
          unset($s_qry,$rs,$row,$i_cnt,$s_where,$i_start_limit,$i_no_of_page);
          return $ret_;
		}
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }    
		
		
	}


	public function get_total_by_sender($i_user_id , $s_where ='') 
	{
		 $sql = sprintf("SELECT count(*) count FROM %smessages m, %susers u WHERE u.id = m.i_receiver_id AND m.i_sender_id = %s AND m.i_is_deleted_by_sender = 0  AND m.s_type= 'normal'  %s ", $this->db->dbprefix, $this->db->dbprefix, $i_user_id , $s_where );		

		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}
	

	public function get_total_unread_by_user_id($i_user_id) 
	{
		$sql = sprintf("SELECT count(distinct id) count FROM %smessages AS m WHERE m.i_receiver_id = %s AND m.i_is_unread = 1 AND m.i_is_deleted_by_receiver = 0 ", $this->db->dbprefix, $i_user_id);
	
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}
	
	/* for extra validation that user is the owner */
	public function update_by_id_receiver($arr=array(), $i_id, $i_user_id) 
	{
		if(count($arr)==0) {
			return null;
		}
		$this->db->update('messages', $arr, array('id'=>$i_id, 'i_receiver_id'=>$i_user_id));
	}
	
	
	public function update_by_id_sender($arr=array(), $i_id, $i_user_id) 
	{
		if(count($arr)==0) {
			return null;
		}
		$this->db->update('messages', $arr, array('id'=>$i_id, 'i_sender_id'=>$i_user_id));
	}
	
		/* for extra validation that user is the owner */
	public function get_by_id_receiver($id, $i_user_id) 
	{
		
		$sql = sprintf('SELECT m.id, 
								m.i_sender_id, 
								m.i_receiver_id, 
								m.i_referred_media_id,
								m.s_message, 
								m.s_subject, 
								m.i_is_unread, 
								m.s_type, 
								m.dt_created_on, 
								m.i_ended, 
								u.s_first_name, 
								u.s_last_name,
								u.s_profile_photo,
								u.i_user_type 
		 			FROM %smessages m, %susers u 
					WHERE u.id = m.i_sender_id and m.id = %s and m.i_receiver_id = %s order by m.dt_created_on DESC', $this->db->dbprefix, $this->db->dbprefix, $id, $i_user_id);
		

		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		if( isset($result_arr[0]) ) {
			return $result_arr[0];
		}
		else {
			array();
		}
	}
	
	public function get_by_id_sender($id, $i_user_id) 
	{
		
		$sql = sprintf('SELECT m.id, 
								m.i_sender_id, 
								m.i_receiver_id, 
								m.i_referred_media_id,
								m.s_message, 
								m.s_subject, 
								m.i_is_unread, 
								m.s_type, 
								m.dt_created_on,
								m.i_ended, 
								u.s_first_name, 
								u.s_last_name,
								u.s_profile_photo,
								u.i_user_type 
		 			FROM %smessages m, %susers u 
					WHERE u.id = m.i_receiver_id and m.id = %s and m.i_sender_id = %s order by m.dt_created_on DESC', $this->db->dbprefix, $this->db->dbprefix, $id, $i_user_id);
		

		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		if( isset($result_arr[0]) ) {
			return $result_arr[0];
		}
		else {
			array();
		}
	}
	
	
	
		/* for extra validation that user is the owner */
	function delete_by_id_receiver($id, $user_id) {
		$sql = sprintf( 'UPDATE  %smessages SET i_is_deleted_by_receiver = 1 WHERE id=%s and i_receiver_id = %s', $this->db->dbprefix, $id, $user_id );

		$this->db->query($sql);
	}
	
	
		/* for extra validation that user is the owner */
	function delete_by_id_sender($id, $user_id) {
		$sql = sprintf( 'UPDATE  %smessages SET i_is_deleted_by_sender = 1 WHERE id=%s and i_sender_id = %s', $this->db->dbprefix, $id, $user_id );

		$this->db->query($sql);
	}
	
	public function get_message_by_id($id) 
	{
		$sql = sprintf("SELECT * FROM %smessages m WHERE m.id = %s  "
						, $this->db->dbprefix, $id);		

		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr[0];
	}
	
	 public function get_trash_msg_list($i_user_id, $i_start_limit="", $i_no_of_page="", $s_where ='') 
	 {
		
		try
        {
		  	$ret_=array();
			
			$s_qry = sprintf("(SELECT  m.id, 
										m.i_sender_id, 
										m.i_receiver_id, 
										m.s_message, 
										m.i_referred_media_id,
										m.s_subject, 
										m.i_is_unread, 
										m.s_type, 
										m.i_ended,
										m.dt_created_on, 
										u.s_first_name, 
										u.s_last_name,
										'i_is_deleted_by_sender' as type,
										u.s_profile_photo,
										u.e_gender,
										m.dt_created_on as  msg_dt
								 FROM cg_messages m, cg_users u 
								 WHERE u.id = m.i_receiver_id AND m.i_sender_id = %1\$s  AND m.i_is_deleted_by_sender = 1
								  %2\$s
								 GROUP BY m.id
								 )
								 
						     UNION 
							 (SELECT m.id, 
										m.i_sender_id, 
										m.i_receiver_id, 
										m.s_message, 
										m.i_referred_media_id,
										m.s_subject, 
										m.i_is_unread, 
										m.s_type, 
										m.i_ended,
										m.dt_created_on, 
										u.s_first_name, 
										u.s_last_name,
										'i_is_deleted_by_receiver' as type,
										u.s_profile_photo ,
										u.e_gender,
										m.dt_created_on as  msg_dt
								 FROM cg_messages m, cg_users u 
								 WHERE u.id = m.i_sender_id AND m.i_receiver_id = %1\$s AND m.i_is_deleted_by_receiver = 1 %2\$s
								 GROUP BY m.id
								 )
								  ORDER BY msg_dt DESC limit %3\$s, %4\$s",  $i_user_id, $s_where, $i_start_limit, $i_no_of_page);

	//	echo nl2br($s_qry); 
		      
          $rs=$this->db->query($s_qry);
          $i_cnt=0;
          if(is_array($rs->result()))
          {
              foreach($rs->result() as $row)
              {
					
					$ret_[$i_cnt]["id"]					=	$row->id;////always integer
					$ret_[$i_cnt]["s_first_name"]		=	get_unformatted_string($row->s_first_name); 
					$ret_[$i_cnt]["s_last_name"]			=	get_unformatted_string($row->s_last_name); 
					$ret_[$i_cnt]["s_displayname"]			=	$ret_[$i_cnt]["s_first_name"].' '.$ret_[$i_cnt]["s_last_name"]; 
					
					$ret_[$i_cnt]["i_sender_id"]		=	intval($row->i_sender_id); 
					$ret_[$i_cnt]["i_receiver_id"]		=	intval($row->i_receiver_id); 
					$ret_[$i_cnt]["i_referred_media_id"]	=	intval($row->i_referred_media_id); 
					$ret_[$i_cnt]["s_message"]			=	get_unformatted_string($row->s_message); 
					$ret_[$i_cnt]["s_subject"]			=	get_unformatted_string($row->s_subject); 
					$ret_[$i_cnt]["s_type"]				=	$row->s_type;
					$ret_[$i_cnt]["type"]				=	$row->type;
					$ret_[$i_cnt]["ended"]				=	$row->i_ended;
					
					$ret_[$i_cnt]["dt_created_on"]		=   $row->dt_created_on;
					$ret_[$i_cnt]["i_is_unread"]		=	intval($row->i_is_unread);  
				
                  $i_cnt++;
              }    
              $rs->free_result();          
          }
          
		  
		  					
		  
          unset($s_qry,$rs,$row,$i_cnt,$s_where,$i_start_limit,$i_no_of_page);
          return $ret_;
		}
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }    
		
		
	}


	public function get_trash_msg_list_count($i_user_id , $s_where ='') 
	{
		 $sql = sprintf("SELECT count(*) count FROM ((SELECT m.id
								 FROM cg_messages m, cg_users u 
								 WHERE u.id = m.i_receiver_id AND m.i_sender_id = %1\$s  AND m.i_is_deleted_by_sender = 1 %2\$s
								 GROUP BY m.id
								 )
								 
						     UNION 
							 (SELECT m.id
								 FROM cg_messages m, cg_users u 
								 WHERE u.id = m.i_sender_id AND m.i_receiver_id = %1\$s AND m.i_is_deleted_by_receiver = 1 %2\$s
								 GROUP BY m.id
								 )) as drvd_tbl",  $i_user_id , $s_where );		

		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}
	
	function delete_from_trash($id, $fld_type ) {
		$sql = sprintf( 'UPDATE  %smessages SET %s = 2 WHERE id=%s ', $this->db->dbprefix, $fld_type, $id );
		$this->db->query($sql);
	}
	
	
	function moved_from_trash($id, $fld_type ) {
		$sql = sprintf( 'UPDATE  %smessages SET %s = 0 WHERE id=%s ', $this->db->dbprefix, $fld_type, $id );
		$this->db->query($sql);
	}
        
        
        
        
        
        function church_req($i_sender_id, $s_type,$s_subject,$s_message){
            $data = array(
   'i_sender_id' => $i_sender_id ,
   's_subject' => $s_subject ,
   's_message' => $s_message,
    'i_is_unread' => 1,
     's_type' => $s_type,
      '	dt_created_on' => get_db_datetime(), 
     
                
);
   // pr($data,1);
$this->db->insert('cg_admin_messages', $data);

        
        }
        
        
        function admin_add_info($info)
        {
          if(count($info))
          {
              $this->db->insert('cg_admin_messages', $info);
              
          }
          
        }
        
        
        function message_list_admin($s_where,$i_start_limit,$i_limit,$order_by)
        {
            $sql="select * from cg_admin_messages ".$s_where." order by ".$order_by." limit ".$i_start_limit.",".$i_limit;
            $q=$this->db->query($sql);
           //echo $sql;exit;
            $result_arr=$q->result_array();
            return $result_arr;
        }
        
        function message_list_admin_count($s_where)
        {
             $sql="select count(*) as count from cg_admin_messages ".$s_where;
             $q=$this->db->query($sql);
           //echo $sql;exit;
            $result_arr=$q->result_array();
            return $result_arr['0']['count'];
        }
     function    change_admin_message_status($id)
            {
            $data = array(
                      'i_status' => 2

                   );

       $this->db->where('id', $id);
       $this->db->update('cg_admin_messages', $data);
       
       $query = $this->db->get_where('cg_admin_messages', array('id' => $id));
       foreach ($query->result() as $row)
       {
           $s_type = $row->s_type;
      $this->load->helper('html');
    $logo="http://cogtime.com/images/logo.png";
    $body = '<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#e9f3f5" style="font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:19px;">
  <tr>
    <td align="left" style="background:#013D62; border-bottom:5px solid #62C3BC; padding:15px 0;"><img src="'.$logo.'" alt= ""></td>
  </tr>
  <tr style="border-top:1px solid #ffffff;">
    <td style="padding-top:10px; padding-bottom:10px;">&nbsp;</td>
  </tr>
  <tr>
  	<td style="background:#62C3BC; color:#fff; font-family:Arial, Helvetica, sans-serif; font-size:22px; font-weight:bold; width:300px; margin:0px auto; display:block; padding:15px; text-align:center;">
    Denied your request
    </td>
  </tr>
  <tr>
  	<td style="padding:15px;"><p>Dear User, </p><p>your  request is denied</p><p>Thanks</p><p>admin@cogtime.com</p></tr>
  <tr>
    <td align="center" valign="middle" style="background:#A8A7A7; padding:15px;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle" style="color:#d3edfd; font-family:Arial, Helvetica, sans-serif; font-size:12px;"> <a href="http://acumencs.com/drandpt-arabic/contact-us/" style="color:#d3edfd; text-decoration:none;"></a></td>
        
        <td align="right" style="color:#525252; font-family:Arial, Helvetica, sans-serif; font-size:12px; direction:rtl;">© All Rights Reserved<span style="color:#525252;"><strong> COGTIME 2014  </strong></span></td>
      </tr>
    </table></td>
  </tr>
</table>'; 
   // $body = " ";
    
	$this->load->library('email');
       $email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
$this->email->initialize($email_setting);

$this->email->from('admin@cogtime.com', 'Team Cogtime');
$this->email->to("$row->user_email");
$this->email->cc('arif.zisu@gmail.com');
//$this->email->bcc('them@their-example.com');

$this->email->subject('New Chuch accept message from Cogtime');
$this->email->message("$body");

$this->email->send();	
     // echo $this->email->print_debugger();   
       }
       

            }
            function change_admin_message_status_accept($id)
            {
              $data = array(
                      'i_status' => 1

                   );

       $this->db->where('id', $id);
       $this->db->update('cg_admin_messages', $data); 
       
       $query = $this->db->get_where('cg_admin_messages', array('id' => $id));
       foreach ($query->result() as $row)
        {
            //echo 
            $type = $row->s_type;
            $church_id = $row->object_id;
            if($type == 'church')
            {
             $data = array(
                      'i_disabled' => 1

                   );

       $this->db->where('id', $church_id);
       $this->db->update('cg_church', $data); 
       
        $data1 = array(
                      's_status' => 1

                   );

       $this->db->where('id', $church_id);
       $this->db->update('cg_church_request', $data1); 
       
      $this->load->helper('html');
    $logo="http://cogtime.com/images/logo.png";
    $body = '<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#e9f3f5" style="font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:19px;">
  <tr>
    <td align="left" style="background:#013D62; border-bottom:5px solid #62C3BC; padding:15px 0;"><img src="'.$logo.'" alt= ""></td>
  </tr>
  <tr style="border-top:1px solid #ffffff;">
    <td style="padding-top:10px; padding-bottom:10px;">&nbsp;</td>
  </tr>
  <tr>
  	<td style="background:#62C3BC; color:#fff; font-family:Arial, Helvetica, sans-serif; font-size:22px; font-weight:bold; width:300px; margin:0px auto; display:block; padding:15px; text-align:center;">
    Church Request
    </td>
  </tr>
  <tr>
  	<td style="padding:15px;"><p>Dear User, </p><p>your church request is accepted</p><p>Thanks</p><p>admin@cogtime.com</p></tr>
  <tr>
    <td align="center" valign="middle" style="background:#A8A7A7; padding:15px;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle" style="color:#d3edfd; font-family:Arial, Helvetica, sans-serif; font-size:12px;"> <a href="http://acumencs.com/drandpt-arabic/contact-us/" style="color:#d3edfd; text-decoration:none;"></a></td>
        
        <td align="right" style="color:#525252; font-family:Arial, Helvetica, sans-serif; font-size:12px; direction:rtl;">© All Rights Reserved<span style="color:#525252;"><strong> COGTIME 2014  </strong></span></td>
      </tr>
    </table></td>
  </tr>
</table>'; 
   // $body = " ";
    
	$this->load->library('email');
       $email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
$this->email->initialize($email_setting);

$this->email->from('admin@cogtime.com', 'Team Cogtime');
$this->email->to("$row->user_email");
$this->email->cc('arif.zisu@gmail.com');
//$this->email->bcc('them@their-example.com');

$this->email->subject('New Chuch accept message from Cogtime');
$this->email->message("$body");

$this->email->send();	
       
       
            }
            
            else if($type == 'project Information Request')
            {
              
                $this->load->helper('html');
    $logo="http://cogtime.com/images/logo.png";
    $body = '<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#e9f3f5" style="font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:19px;">
  <tr>
    <td align="left" style="background:#013D62; border-bottom:5px solid #62C3BC; padding:15px 0;"><img src="'.$logo.'" alt= ""></td>
  </tr>
  <tr style="border-top:1px solid #ffffff;">
    <td style="padding-top:10px; padding-bottom:10px;">&nbsp;</td>
  </tr>
  <tr>
  	<td style="background:#62C3BC; color:#fff; font-family:Arial, Helvetica, sans-serif; font-size:22px; font-weight:bold; width:300px; margin:0px auto; display:block; padding:15px; text-align:center;">
   project Information Request
    </td>
  </tr>
  <tr>
  	<td style="padding:15px;"><p>Dear User, </p><p>project Information send you as soon as possible</p><p>Thanks</p><p>admin@cogtime.com</p></tr>
  <tr>
    <td align="center" valign="middle" style="background:#A8A7A7; padding:15px;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle" style="color:#d3edfd; font-family:Arial, Helvetica, sans-serif; font-size:12px;"> <a href="http://acumencs.com/drandpt-arabic/contact-us/" style="color:#d3edfd; text-decoration:none;"></a></td>
        
        <td align="right" style="color:#525252; font-family:Arial, Helvetica, sans-serif; font-size:12px; direction:rtl;">© All Rights Reserved<span style="color:#525252;"><strong> COGTIME 2014  </strong></span></td>
      </tr>
    </table></td>
  </tr>
</table>'; 
   // $body = " ";
    
	$this->load->library('email');
       $email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
$this->email->initialize($email_setting);

$this->email->from('admin@cogtime.com', 'Team Cogtime');
$this->email->to("$row->user_email");
$this->email->cc('arif.zisu@gmail.com');
//$this->email->bcc('them@their-example.com');

$this->email->subject('New  project Information Request of Cogtime');
$this->email->message("$body");

$this->email->send();	
                
                
            }
            else if($type == 'Ring Category Request')
            {
                              $this->load->helper('html');
    $logo="http://cogtime.com/images/logo.png";
    $body = '<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#e9f3f5" style="font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:19px;">
  <tr>
    <td align="left" style="background:#013D62; border-bottom:5px solid #62C3BC; padding:15px 0;"><img src="'.$logo.'" alt= ""></td>
  </tr>
  <tr style="border-top:1px solid #ffffff;">
    <td style="padding-top:10px; padding-bottom:10px;">&nbsp;</td>
  </tr>
  <tr>
  	<td style="background:#62C3BC; color:#fff; font-family:Arial, Helvetica, sans-serif; font-size:22px; font-weight:bold; width:300px; margin:0px auto; display:block; padding:15px; text-align:center;">
   Ring Category Request
    </td>
  </tr>
  <tr>
  	<td style="padding:15px;"><p>Dear User, </p><p>Ring Category Request approve  as soon as possible</p><p>Thanks</p><p>admin@cogtime.com</p></tr>
  <tr>
    <td align="center" valign="middle" style="background:#A8A7A7; padding:15px;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle" style="color:#d3edfd; font-family:Arial, Helvetica, sans-serif; font-size:12px;"> <a href="http://acumencs.com/drandpt-arabic/contact-us/" style="color:#d3edfd; text-decoration:none;"></a></td>
        
        <td align="right" style="color:#525252; font-family:Arial, Helvetica, sans-serif; font-size:12px; direction:rtl;">© All Rights Reserved<span style="color:#525252;"><strong> COGTIME 2014  </strong></span></td>
      </tr>
    </table></td>
  </tr>
</table>'; 
   // $body = " ";
    
	$this->load->library('email');
       $email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
$this->email->initialize($email_setting);

$this->email->from('admin@cogtime.com', 'Team Cogtime');
$this->email->to("$row->user_email");
$this->email->cc('arif.zisu@gmail.com');
//$this->email->bcc('them@their-example.com');

$this->email->subject('New  project Information Request of Cogtime');
$this->email->message("$body");

$this->email->send(); 
            }
            
            
        }
            }
            function change_admin_message_status_admin_del($id)
            {
                $data = array(
                      'i_is_deleted' => 1

                   );

       $this->db->where('id', $id);
       $this->db->update('cg_admin_messages', $data);  
            }
}   // end of class definition...
