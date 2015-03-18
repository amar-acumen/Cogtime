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
class My_blog_model extends Base_model 
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
    public function fetch_multi($s_where=null,$i_start=null,$i_limit=null,$s_order_by=null)
    {
	 
		try
        {
		  	$ret_=array();
			
			$language = get_current_language();
			
			$s_qry = "SELECT   c.id AS blogid, 
						   c.s_title, 
						   c.s_description,
                           c.i_user_id,
						   
						   c.i_view_count, 
						   c.dt_created_on, 
						   c.i_isenabled,
						   c.s_publish,
						   u.id user_id, 
						   u.s_email,
						   CONCAT(u.s_first_name,' ',u.s_last_name) AS s_profile_name,
						   u.s_profile_photo,
						   u.e_gender,
						   u.i_status,
						   COUNT(p.id) AS no_of_posts
						   
							FROM 
								{$this->db->USER_BLOGS} c LEFT JOIN {$this->db->USER_BLOG_POST} AS p ON p.i_blog_id=c.id , {$this->db->USERS} u "
						.$s_where.' GROUP BY p.i_blog_id '; 
					
		/*cn.s_country_name {$this->db->MST_COUNTRY} cn*/
                
          //////////For Pagination///////////*don't change*/
          //$s_qry=str_replace("'","''",$s_qry);//for string operation in procedure
          $s_qry= $s_qry.(trim($s_order_by)!=""?" ORDER BY ".$s_order_by."":"ORDER BY blogid asc")." ".(is_numeric($i_start) && is_numeric($i_limit)?" LIMIT ".intval($i_start).",".intval($i_limit):"");
		 
			#echo ($s_qry); exit;
          //////////end For Pagination//////////                
                
             
          $rs=$this->db->query($s_qry); 
          $i_cnt=0;
          if(is_array($rs->result()))
          {
              foreach($rs->result() as $row)
              {
                        $ret_[$i_cnt]["blogid"]				=	$row->blogid;////always integer
						$ret_[$i_cnt]["s_title"]			=	$row->s_title;
                        $ret_[$i_cnt]["s_description"]        =    $row->s_description;
						$ret_[$i_cnt]["i_user_id"]		    =	$row->i_user_id;
						$ret_[$i_cnt]["i_popularity_count"]	=	intval($row->i_popularity_count);
						$ret_[$i_cnt]["i_view_count"]		=	intval($row->i_view_count);
						
						$ret_[$i_cnt]["s_publish"]			=	$row->s_publish;
						
						$ret_[$i_cnt]["s_profile_name"]		=	$row->s_profile_name;
						$ret_[$i_cnt]["s_email"]  	   		=	get_unformatted_string($row->s_email); 
						
						$ret_[$i_cnt]["s_profile_photo"]  	=	get_unformatted_string($row->s_profile_photo); 
						$ret_[$i_cnt]["s_gender"]			=	($row->e_gender == 'M')?'Male':'Female';
						$ret_[$i_cnt]["s_city"]  	   		=	get_unformatted_string($row->s_city); 
						$ret_[$i_cnt]["s_state"]  	   		=	get_unformatted_string($row->s_state); 
						#causing prob 		#$ret_[$i_cnt]["s_country_name"]  	    =	get_unformatted_string($row->s_country_name); 
						$ret_[$i_cnt]["i_user_type"]		=	intval($row->i_user_type);
						$ret_[$i_cnt]["i_country_id"]		=	intval($row->i_country_id);
						$ret_[$i_cnt]["dt_created_on"]		=	($row->dt_created_on);
						$ret_[$i_cnt]["i_isenabled"]		=	intval($row->i_isenabled);
						
						
						$ret_[$i_cnt]["total_articles"]		=    $this->get_total_articles_by_blog_id($row->blogid); 
						$ret_[$i_cnt]["total_comments"]		=    $this->get_total_comments_by_blog_id($row->blogid);
						

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
						{$this->db->USER_BLOGS} c LEFT JOIN {$this->db->USER_BLOG_POST} AS p ON p.i_blog_id=c.id , {$this->db->USERS} u
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


	
	public function add_bog($add_arr,$title)
	{

		if($title=='')
        {

            $return_str    = $this->db->insert($this->db->USER_BLOGS ,$add_arr);
        }
			
		else
		{
			$this->db->where('i_user_id', intval(decrypt($this->session->userdata('user_id'))));
			$return_str	= $this->db->update($this->db->USER_BLOGS ,$add_arr);
		}
		if( $return_str)
			return true;
		else
			return false;
			
	}
    
    function add_blog($info)
    {
        $res = $this->db->insert($this->db->USER_BLOGS ,$info);
        
        $lastid = $this->db->insert_id();
        if ($lastid)
            return true;
        else
            return false;    
        
    }
    
    function edit_blog($info,$blog_id)
    {
       
        $query = $this->db->update($this->db->USER_BLOGS,$info,array('id'=>$blog_id));
        
        return $query;
    }
    
    
	
	
	public function get_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
		$s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY id DESC';
		
        $sql    = " SELECT B.*, 
					CONCAT(U.s_first_name,' ', U.s_last_name) AS s_profile_name
					FROM {$this->db->USER_BLOGS} 
					B LEFT JOIN  {$this->db->USERS} U ON U.id = B.i_user_id 
					{$where}  {$s_order_by} {$limit}";

        $query     = $this->db->query($sql);// echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr,1);
		
		if(count($result_arr)){
			
			foreach($result_arr as $key=> $val){
				$result_arr[$key]["total_articles"]		=    $this->get_total_articles_by_blog_id($val['id']); 
				$result_arr[$key]["total_comments"]		=    $this->get_total_comments_by_blog_id($val['id']);
			}
		}
		
		
        return $result_arr;
    }
	
    public function get_list_count($where='')
    {
        
        
        $sql    = "SELECT count(*) as i_total FROM {$this->db->USER_BLOGS} B 
				  	LEFT JOIN  {$this->db->USERS} U ON U.id = B.i_user_id  
				  	{$where} ";
        $query     = $this->db->query($sql); //echo $this->db->last_query();
        $result_arr = $query->result_array(); //pr($result_arr);
        return $result_arr[0]['i_total'];
    }
	
	
	
    
	public function get_total_articles_by_blog_id($blog_id) {
	
		$sql = "SELECT count(*) count FROM ".$this->db->USER_BLOG_POST."  where i_blog_id = '".$blog_id."'";
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}
	
	public function get_total_comments_by_blog_id($blog_id) {
		
		$sql = "SELECT count(*) count FROM ".$this->db->USER_BLOG_POST_COMMENTS.
					  " where i_blog_id = '".$blog_id."'";
							
		$query = $this->db->query($sql);
 		//echo $this->db->last_query();
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}
	
	public function change_status($status ,$id) {
		
	  if($status !='' && $id !=''){	
		  $sql = "UPDATE {$this->db->USER_BLOGS} SET `i_isenabled` = '".$status."'
						   WHERE `id` ='".$id."'";
		  $this->db->query($sql);// echo $this->db->last_query();exit;
		  return true;
	  }
	}
	
    
    //--------- for admin use ---------------------
	public function delete_by_id($id) {
	
	     $sql = 'DELETE FROM '.$this->db->USER_BLOG_POST_COMMENTS.' WHERE i_blog_post_id="'.$id.'"';
		 $this->db->query($sql);
		 
		 $sql = 'DELETE FROM '.$this->db->USER_BLOG_POST.' WHERE i_blog_id="'.$id.'"';
		 $this->db->query($sql);
		 
		 $sql = 'DELETE FROM '.$this->db->USER_BLOGS.' WHERE id="'.$id.'"';
		 $this->db->query($sql);
		#echo $this->db->last_query(); exit;
	}
	
	
	public function get_by_id($id) {

		$sql = 'SELECT * FROM '.$this->db->USER_BLOGS.'  where id = "'.$id.'"';
		$query = $this->db->query($sql); #echo $this->db->last_query(); exit;
		$result_arr = $query->result_array();
		#pr($result_arr[0]);
		return $result_arr[0];
	}
	
    
    
    //--------------------------------- search blog -------------------------------------
    function get_search_list($where, $i_start=null, $i_limit=null, $s_order_by='')
    {
       
        if("$i_start" == '') {
            $limit = '';
        }
        else {
            $i_start = (int) $i_start;
            $i_limit = (int) $i_limit;
            $limit = ' limit '.$i_start.', '.$i_limit;
        }
        
        $user_id = decrypt($this->session->userdata('user_id'));
        
       $sql    = "SELECT B.id,
	   					 B.s_title,
						 B.s_description,
						 B.dt_created_on, 
						 U.id AS user_id, 
						 U.s_first_name,
						 U.s_last_name  
						 
						 FROM {$this->db->USER_BLOGS} B 
                      LEFT JOIN  {$this->db->USERS} U ON U.id = B.i_user_id  WHERE 1 
                      {$where} {$limit}";
        
        $res = $this->db->query($sql)->result_array();
        

        if(count($res)){
            
            foreach($res as $key=> $val){
                $res[$key]["total_articles"]        =    $this->get_total_articles_by_blog_id($val['id']); 
                $res[$key]["total_comments"]        =    $this->get_total_comments_by_blog_id($val['id']);
            }
        }
        
//pr($res);
        return $res;
        
        
    }
    
    
    function get_total_search_result($where)
    {
         $sql    = "SELECT count(*) count FROM {$this->db->USER_BLOGS} B 
                      LEFT JOIN  {$this->db->USERS} U ON U.id = B.i_user_id  WHERE 1 
                      {$where} ";
         $res = $this->db->query($sql)->result_array();
         return $res[0]['count'];
         
    }
    
    
    //-------------------------------------- / search blog ---------------------------------------
    
    
    
    
    //-------------------------------- popular blog -----------------------------------------
    public function get_search_list_popular_blog($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
      
		if("$i_start" == '') {
			$limit = '';
		}
		else {
			$i_start = (int) $start_limit;
			$i_limit = (int) $i_limit;
			$limit = ' limit '.$i_start.', '.$i_limit;
		}
		$user_id = decrypt($this->session->userdata('user_id'));
		$sql = " SELECT derived_tbl.* FROM (
							(SELECT B.*, CONCAT(U.s_first_name,' ',U.s_last_name) AS user_name,
							
							(SELECT count(*) FROM cg_user_blog_post 
										 WHERE i_blog_id = B.id ) total_articles,
										 
							(SELECT count(*) FROM cg_user_blog_post_comments 
										 WHERE i_blog_id = B.id ) total_comments
							
							FROM cg_user_blogs B
							LEFT JOIN  {$this->db->USERS} U  ON U.id = B.i_user_id 
							WHERE  	i_isenabled = 1 {$where} GROUP BY B.id ORDER BY `i_view_count` DESC, B.`dt_created_on` DESC, 
							`total_articles` DESC, `total_comments`  DESC )
							
  						) as  derived_tbl {$limit}  ";
		//echo $sql;exit;

		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
 //pr($result_arr,1);
		return $result_arr;
	
		
		
    }
    
    
    
    //-------------------------------- end popular blog -----------------------------------------
    
    
    
    //-------------------------------- blog detail ----------------------------------
    
    function update_view_count_by_blog_id($blog_id)
    {
        $query1 = "SELECT `i_view_count` FROM {$this->db->USER_BLOGS} WHERE id={$blog_id}";
        $res = $this->db->query($query1)->row_array();
//pr($res);
//echo "res : ".$res['i_view_count'];
        
        $view_count = $res['i_view_count'];
        $view_count++;
        
        $query = "UPDATE {$this->db->USER_BLOGS} SET `i_view_count`='".$view_count."' WHERE id='".$blog_id."'";
        $this->db->query($query);
    }
    
    //-------------------------------- end blog detail ----------------------------------
    
    
    
    //--------------------------------- all blogs --------------------------------------
    public function get_search_list_all_blogs($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
      
        if("$i_start" == '') {
            $limit = '';
        }
        else {
            $i_start = (int) $i_start;
            $i_limit = (int) $i_limit;
            $limit = ' limit '.$i_start.', '.$i_limit;
        }
        $user_id = decrypt($this->session->userdata('user_id'));
        $sql = " SELECT derived_tbl.* FROM (
                            (SELECT B.*, CONCAT(U.s_first_name,' ',U.s_last_name) AS user_name,
                            
                            (SELECT count(*) FROM cg_user_blog_post 
                                         WHERE i_blog_id = B.id ) total_articles,
                                         
                            (SELECT count(*) FROM cg_user_blog_post_comments 
                                         WHERE i_blog_id = B.id ) total_comments
                            
                            FROM cg_user_blogs B
                            LEFT JOIN  {$this->db->USERS} U  ON U.id = B.i_user_id 
                            WHERE      i_isenabled = 1 {$where} GROUP BY B.id ORDER BY  B.`dt_created_on` DESC)
                            
                          ) as  derived_tbl {$limit}  ";
        //echo $sql;exit;

        $query = $this->db->query($sql);
        $result_arr = $query->result_array();
 //pr($result_arr,1);
        return $result_arr;
    
        
        
    }
    
    public function get_total_all_blogs($where)
    {
        $sql = "SELECT count(*) AS count FROM {$this->db->USER_BLOGS} {$where}";

        $res = $this->db->query($sql)->row_array();
        return $res['count'];
    }
    //--------------------------------- end of all blogs --------------------------------------
    
	
	public function get_blog_details_by_article_id($article_id , $where='')
    {
             
       $sql = "SELECT 
						B.*,
						BP.s_post_title
                
                FROM {$this->db->USER_BLOGS} B 
                LEFT JOIN  {$this->db->USER_BLOG_POST} BP ON B.id = BP.i_blog_id
                WHERE BP.id = {$article_id} {$where}
                ";
        
        $result_arr = $this->db->query($sql)->result_array();
		//echo $this->db->last_query();exit;
        return $result_arr[0];
      
    }
	
	
	### new method for search blog ###
	
	function get_blog_search_result($where, $i_start=null, $i_limit=null,$s_query_type = "both", $WHERE_ARTICLE_COND = '')
    {
       
        if("$i_start" == '') {
            $limit = '';
        }
        else {
            $i_start = (int) $i_start;
            $i_limit = (int) $i_limit;
           $limit = ' limit '.$i_start.', '.$i_limit;
        }
        
        $user_id = decrypt($this->session->userdata('user_id'));
		 if($s_query_type == 'blog'){ 
		   $sql    = "SELECT B.id,
							 B.s_title as title,
							 B.s_description as s_desc,
							 B.dt_created_on as dt_created, 
							 U.id AS user_id, 
							 U.s_first_name,
							 U.s_last_name ,
							 U.s_profile_photo,
							 U.e_gender,
							  'blog' as s_type  ,
							 1 as  comment_count ,
							 B.id as blog_id
							 
							 FROM {$this->db->USER_BLOGS} B 
						  LEFT JOIN  {$this->db->USERS} U ON U.id = B.i_user_id  WHERE 1 
						  AND B.i_isenabled = 1
						  {$where} {$limit}";
						
		 }
		 else  if($s_query_type == 'article'){ 
		  
		   $sql    = "SELECT 
		   					 BP.id,
							 BP.s_post_title as title,
							 BP.s_post_description as s_desc,
							 BP.dt_created_date as dt_created, 
							 U.id AS user_id, 
							 U.s_first_name,
							 U.s_last_name ,
							 U.s_profile_photo,
							 U.e_gender,
							 'article' as s_type   ,
							 (SELECT count(*)  FROM cg_user_blog_post_comments BPC
							          WHERE BPC.i_blog_post_id = BP.id AND BPC.i_blog_id = BP.i_blog_id )as comment_count ,
							  BP.i_blog_id as blog_id
							 
							 FROM {$this->db->USER_BLOG_POST} BP 
						     LEFT JOIN  {$this->db->USERS} U ON U.id = BP.i_user_id  WHERE 1 
							 AND BP.i_disable = 1
						     {$where} {$limit}";
						
		 }
		 else  if($s_query_type == 'both'){ 
		   
		   $sql = " SELECT derived_tbl.* FROM (
							(SELECT B.id,
							 B.s_title as title,
							 B.s_description as s_desc,
							 B.dt_created_on as dt_created, 
							 U.id AS user_id, 
							 U.s_first_name,
							 U.s_last_name ,
							 U.s_profile_photo,
							 U.e_gender,
							 'blog' as s_type  ,
							 1 as  comment_count,
							 B.id as blog_id
							 
							 FROM {$this->db->USER_BLOGS} B 
						  LEFT JOIN  {$this->db->USERS} U ON U.id = B.i_user_id  WHERE 1 
						  AND B.i_isenabled = 1
						  {$where}  ORDER BY B.dt_created_on DESC)
                            
                            UNION
                            
                         (SELECT 
		   					 BP.id,
							 BP.s_post_title as title,
							 BP.s_post_description as s_desc,
							 BP.dt_created_date as dt_created, 
							 U.id AS user_id, 
							 U.s_first_name,
							 U.s_last_name,
							 U.s_profile_photo,
							 U.e_gender,
							 'article' as s_type   ,
							 (SELECT count(*)  FROM cg_user_blog_post_comments BPC
							          WHERE BPC.i_blog_post_id = BP.id AND BPC.i_blog_id = BP.i_blog_id )as comment_count,
							BP.i_blog_id as blog_id
									  
							 FROM {$this->db->USER_BLOG_POST} BP 
						     LEFT JOIN  {$this->db->USERS} U ON U.id = BP.i_user_id  WHERE 1 
							 AND BP.i_disable = 1
						     {$WHERE_ARTICLE_COND} ORDER BY BP.dt_created_date DESC)
							 ) as  derived_tbl {$limit} ";
		   
		  
		 }
	   
   //echo '=='.nl2br($sql); exit;
        $res = $this->db->query($sql)->result_array();
       // pr($res);

        if(count($res)){
            
            foreach($res as $key=> $val){
                $res[$key]["total_articles"]        =    $this->get_total_articles_by_blog_id($val['id']); 
                $res[$key]["total_comments"]        =    $this->get_total_comments_by_blog_id($val['id']);
            }
        }
        

        return $res;
        
        
    }
    
    
    function get_total_blog_search_result($where, $s_query_type = 'both', $WHERE_ARTICLE_COND ='')
    {
        		 
		  if($s_query_type == 'blog'){ 
		   $sql    = "SELECT count(*) count
							 
							 FROM {$this->db->USER_BLOGS} B 
						  LEFT JOIN  {$this->db->USERS} U ON U.id = B.i_user_id  WHERE 1 
						  AND B.i_isenabled = 1
						  {$where} {$limit}";
						
		 }
		 else  if($s_query_type == 'article'){ 
		  
		   $sql    = "SELECT 
		   					 count(*) count
							 
							 FROM {$this->db->USER_BLOG_POST} BP 
						     LEFT JOIN  {$this->db->USERS} U ON U.id = BP.i_user_id  WHERE 1 
							 AND BP.i_disable = 1
						     {$where} {$limit}";
						
		 }
		 else  if($s_query_type == 'both'){ 
		   
		   $sql = " SELECT count(*) count FROM (
							(SELECT B.id,
							 B.s_title as title,
							 B.s_description as s_desc,
							 B.dt_created_on as dt_created, 
							 U.id AS user_id, 
							 U.s_first_name,
							 U.s_last_name ,
							 U.s_profile_photo,
							 U.e_gender,
							 
							 1 
							 
							 FROM {$this->db->USER_BLOGS} B 
						  LEFT JOIN  {$this->db->USERS} U ON U.id = B.i_user_id  WHERE 1 
						  AND B.i_isenabled = 1
						  {$where} {$limit} ORDER BY B.dt_created_on DESC)
                            
                            UNION
                            
                         (SELECT 
		   					 BP.id,
							 BP.s_post_title as title,
							 BP.s_post_description as s_desc,
							 BP.dt_created_date as dt_created, 
							 U.id AS user_id, 
							 U.s_first_name,
							 U.s_last_name ,
							 U.s_profile_photo,
							 U.e_gender,
							 (SELECT count(*)  FROM cg_user_blog_post_comments BPC
							          WHERE BPC.i_blog_post_id = BP.id AND BPC.i_blog_id = BP.i_blog_id )as comment_count
							 
							 FROM {$this->db->USER_BLOG_POST} BP 
						     LEFT JOIN  {$this->db->USERS} U ON U.id = BP.i_user_id  WHERE 1 
							 AND BP.i_disable = 1
						     {$WHERE_ARTICLE_COND} {$limit} ORDER BY BP.dt_created_date DESC)
							 ) as  derived_tbl  ";
		   
		  
		 }
		 //echo $sql;
         $res = $this->db->query($sql)->result_array();
         return $res[0]['count'];
         
    }
     public function get_blog_all_comments($s_where,$i_start_limit='', $i_no_of_page=''){
            $sql = "select c.*,p.s_post_title,p.s_post_description from cg_user_blog_post_comments c,cg_user_blog_post p $s_where and c.i_blog_post_id = p.id limit ".$i_start_limit.",".$i_no_of_page;
            //echo $sql;
            //exit;
            $query=$this->db->query($sql);
$result_arr=$query->result_array();
//echo $sql;exit;
//echo $i_start_limit;
//pr($result_arr,1);
return $result_arr;
        }
        public function get_blog_all_comments_total($s_where){
            $sql = "select count(*) as count from cg_user_blog_post_comments c $s_where";
            //echo $sql;
            //exit;
            $query = $this->db->query($sql); #echo "sql ==>". ($sql);exit; 
		$result_arr = $query->result_array();
		return $result_arr['0']['count'];
        }
    
    public function __destruct()
    {}   




}   // end of class definition...
