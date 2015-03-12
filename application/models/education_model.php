<?php
/*********
* Author: 
* Date  : 
* Modified By: 
* Modified Date:
* 
* Purpose:
*  Model For ## Management
* 
* @package 
* @subpackage 
* 
* @link InfModel.php 
* @link Base_model.php
* @link controllers/
* @link views/
*
*/

include_once(APPPATH.'models/base_model.php');

class Education_model extends Base_model
{

    private $tbl_name;
    private $user_status;
    private $user_type;
    
    public function __construct() 
    {
        try
        {
          parent::__construct();
          $this->conf = get_config();
          $this->tbl_name = $this->db->USERS;
          
			$this->timeout = $this->conf['online_user_timeout'];
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
             
             
             if($timeout=='') {
                $timeout = $this->timeout;
             }

             $timestamp = time() - $timeout;
    
                  
                  $s_qry ="SELECT   A.* ,
				  					fn_total_user_photo(A.id) i_total_user_photo ,
									fn_total_user_video(A.id) i_total_user_video ,
									fn_total_user_fan(A.id) i_total_user_fan ,
				  					 B.s_country_name_{$curLanguage} as s_country_name ,
									 C.s_genre_name_{$curLanguage} as s_genre_name "
                         ." FROM ".$this->db->USERS." AS A "
						 ." LEFT JOIN  ".$this->db->MST_COUNTRY." AS B ON A.i_country_id = B.id "
						  ." LEFT JOIN  ".$this->db->MST_GENRE." AS C ON A.i_genre_id = C.id "
                         .$s_where; 
                
          //////////For Pagination///////////*don't change*/
          //$s_qry=str_replace("'","''",$s_qry);//for string operation in procedure
          $s_qry= $s_qry.(trim($s_order_by)!=""?" ORDER BY ".$s_order_by."":"ORDER BY id asc")." ".(is_numeric($i_start) && is_numeric($i_limit)?" LIMIT ".intval($i_start).",".intval($i_limit):"");
          //////////end For Pagination//////////                
                
            
          $rs=$this->db->query($s_qry); 
      // echo $this->db->last_query() ."<br /><br />"; 
          $i_cnt=0;
          if(is_array($rs->result()))
          {
              foreach($rs->result() as $row)
              {
                      $ret_[$i_cnt]["id"]                 		  =    $row->id;////always integer
                      $ret_[$i_cnt]["s_language"]                 =    $row->s_language; 
                      $ret_[$i_cnt]["s_profile_name"]             =    get_unformatted_string($row->s_profile_name); 
                      $ret_[$i_cnt]["s_name"]      		          =    get_unformatted_string($row->s_name); 
					  $ret_[$i_cnt]["s_about_me"]      		      =    get_unformatted_string($row->s_about_me);
					  $ret_[$i_cnt]["s_display_name"]      		  =    get_unformatted_string($row->s_name); 
					  $artist_name 								  =    get_unformatted_string($row->s_name); 
					  
					  if(strlen($artist_name)>9)
				  	   $s_name[$i_cnt] = substr($artist_name ,0,8);
                  	  $ret_[$i_cnt]["s_artist_name"]      	      =	  $s_name[$i_cnt];  // added to show in similar artist section
					  
					  $ret_[$i_cnt]["s_picture"]             =    ($row->s_picture); 
					  $ret_[$i_cnt]["s_status_text"]      		          =    get_unformatted_string($row->s_status_text); 
					  $ret_[$i_cnt]["dt_status_updated_on"]              =    $row->dt_status_updated_on; 
					  $ret_[$i_cnt]["s_banner"]             =    ($row->s_banner); 
					  $ret_[$i_cnt]["s_email"]         	  		  =    $row->s_email; 
					  $ret_[$i_cnt]["s_gender"]         	  	  =    $row->s_gender;
					  $ret_[$i_cnt]["s_city"]      		  =    get_unformatted_string($row->s_city); 
					  $ret_[$i_cnt]["s_state"]      		  =    get_unformatted_string($row->s_state); 
					  
                      $ret_[$i_cnt]["i_country_id"]                        =    intval($row->i_country_id);
					  $ret_[$i_cnt]["s_country_name"]      		  =    get_unformatted_string($row->s_country_name);
					  $ret_[$i_cnt]["i_genre_id"]      		  =    intval($row->i_genre_id);  // added 
					  
					  $ret_[$i_cnt]["s_genre_name"]      		  =    get_unformatted_string($row->s_genre_name); 
					  
					  $s_genre_name 			      		  =    get_unformatted_string($row->s_genre_name); 
					  
					 /* if(strlen($s_genre_name)>13)
				  	 echo  $s_genre_name[$i_cnt] = substr($s_genre_name ,0,10);
                  	  $ret_[$i_cnt]["s_genre_name_artist"]      	      =	  $s_genre_name[$i_cnt];  // added to show in similar artist section*/
					  
					  
					  $ret_[$i_cnt]["s_additional_genre1"]    	  =    get_unformatted_string($row->s_additional_genre1);
					  $ret_[$i_cnt]["s_additional_genre2"]    	  =    get_unformatted_string($row->s_additional_genre2);  
					  $ret_[$i_cnt]["i_user_type"]        		  =    intval($row->i_user_type); 
					  $ret_[$i_cnt]["i_signed"]        		  	  =    intval($row->i_signed);
					  $ret_[$i_cnt]["s_signed"]        		  	  =    (intval($row->i_signed) == 1 )?'Signed artist':'Independant';
					   $ret_[$i_cnt]["i_total_user_photo"]        		  	  =    intval($row->i_total_user_photo);
					   $ret_[$i_cnt]["i_total_user_video"]        		  	  =    intval($row->i_total_user_video);
					   $ret_[$i_cnt]["i_total_user_fan"]        		  	  =    intval($row->i_total_user_fan);
					  $ret_[$i_cnt]["i_status"]        		  	  =    intval($row->i_status);
                      
					  $ret_[$i_cnt]["s_website"]                        =    $row->s_website;
					  
					  
					  $ret_[$i_cnt]["s_paypal_id"]                =    $row->s_paypal_id; 
					  $ret_[$i_cnt]["dt_created_on"]              =    $row->dt_created_on; 
                      $i_cnt++;
              }    
              $rs->free_result();          
          }
          
          
           //pr($ret_);                   
          
          unset($s_qry,$rs,$row,$i_cnt,$s_where,$i_start,$i_limit,$artist_name,$s_name);
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
          $s_qry = "SELECT   COUNT(*) AS i_total "
                    ." FROM ".$this->db->USERS." AS A "
					." LEFT JOIN  ".$this->db->MST_COUNTRY." AS B ON A.i_country_id = B.id "
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
    
	
	
	 /****
    * Fetch Total records for similar artist
    * @param string $s_where, ex- " status=1 AND deleted=0 " 
    * @returns int on success and FALSE if failed 
    */
    public function gettotal_fetch_multi_info($s_where=null)
    {
        try
        {
          $ret_=0;
          $s_qry = "SELECT   COUNT(*) AS i_total "
                    ." FROM ".$this->db->USERS." AS A "
					." LEFT JOIN  ".$this->db->MST_COUNTRY." AS B ON A.i_country_id = B.id "
					  ." LEFT JOIN  ".$this->db->MST_GENRE." AS C ON A.i_genre_id = C.id "
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
    {
        try
        {
		  $curLanguage = get_current_language();
          $ret_=array();
          if(intval($i_id)>0)
          {
              ////Using Prepared Statement///
                   
      
             
              $s_qry ="SELECT   A.* , B.s_name as s_name, C.s_status as s_status ,
			  				D.s_school_name as s_school_name, 
							D.s_school_city as s_school_city, 
							D.s_school_state as s_school_state, 
							D.s_school_country as s_school_country, 
							D.i_class_year as i_class_year, 
							D.s_degree as s_degree  
																"
                         . " FROM ".$this->db->USERS." AS A "
						 . " LEFT JOIN  ".$this->db->DENOMINATION." AS B ON A.i_id_denomination = B.id "
						 . " LEFT JOIN  ".$this->db->USERS_ONLINE." AS C ON A.id = C.i_user_id "
						 . " LEFT JOIN  ".$this->db->USER_EDUCATION." AS D ON A.id = D.i_user_id"
                      . " WHERE A.id=?";     
              
                  
                    
                                  
              $rs=$this->db->query($s_qry,array(intval($i_id)));
              # echo $this->db->last_query() ."<br />";
              
              
              if(is_array($rs->result()))
              {
                  
				  foreach($rs->result() as $row)
                  {
                      $ret_["id"]                 		  =    $row->id;////always integer
                      $ret_["s_profile_name"]             =    get_unformatted_string($row->s_first_name).' '
					  										  .get_unformatted_string($row->s_last_name); 
					
					  $ret_["e_title"]					  =	   get_unformatted_string($row->e_title);		  
                      $ret_["s_title"]      		      =    (($row->e_title == 'Mr')?'Mr.':(($row->e_title == 'Mrs')?'Mrs.':'Ms.')); 
					  $ret_["s_first_name"]      		  =    get_unformatted_string($row->s_first_name);
					  $ret_["s_last_name"]      		  =    get_unformatted_string($row->s_last_name);
					  
					  $ret_["s_email"]         	  		  =    $row->s_email;
					  $ret_["e_gender"]         	  	  =    $row->e_gender;
					  $ret_["s_gender"]         	  	  =    ($row->e_gender == 'M')?'Male':'Female'; 
					  $ret_["dt_dob"]         	  	  	  =    $row->dt_dob; 
					  $ret_["s_city"]      		  		  =    get_unformatted_string($row->s_city); 
					  $ret_["s_state"]      		      =    get_unformatted_string($row->s_state);
					  $ret_["s_country"]      		      =    $row->s_country; 
					  $ret_["s_mobile"]                     =    get_unformatted_string($row->s_mobile);
 
					  
					  $ret_["s_about_me"]      		      =    get_unformatted_string($row->s_about_me);
					  $ret_["s_profile_photo"]            =    ($row->s_profile_photo); 
					  $ret_["s_status_message"]           =    get_unformatted_string($row->s_status_message); 
					  $ret_["e_want_net_pal"]       	  =    get_unformatted_string($row->e_want_net_pal); 
					  $ret_["e_want_prayer_partner"]      =    get_unformatted_string($row->e_want_prayer_partner);
					  $ret_["s_want_net_pal"]       	  =    ($row->e_want_net_pal== 'Y')?'Yes':'No'; 
					  $ret_["s_want_prayer_partner"]      =    ($row->e_want_prayer_partner == 'Y')?'Yes':'No';
					   
					  $ret_["s_website"]     			  =    get_unformatted_string($row->s_website);
					  
					  $ret_["s_church_name"]      	 =    get_unformatted_string($row->s_church_name);
					  $ret_["s_church_address"]      =    get_unformatted_string($row->s_church_address);
					  $ret_["s_church_city"]         =    get_unformatted_string($row->s_church_city);
					  $ret_["s_church_state"]        =    get_unformatted_string($row->s_church_state);
					  $ret_["s_church_country"]      =    get_unformatted_string($row->s_church_country);
					  $ret_["s_church_phone"]        =    get_unformatted_string($row->s_church_phone);
					  $ret_["s_church_postcode"]        =    get_unformatted_string($row->s_church_postcode);
					  $ret_["i_id_denomination"]        =    get_unformatted_string($row->i_id_denomination);
					 
					  $ret_["s_church_location"] 	 =     $ret_["s_church_address"].' '.$ret_["s_church_postcode"].' '
					  									   .$ret_["s_church_city"].' '
					 									   . $ret_["s_church_state"] .' '.$ret_["s_church_country"];					  
					  $ret_["i_user_type"]        		  =    intval($row->i_user_type);
					  $ret_["i_status"]        		  	  =    intval($row->i_status);
					  
					  $ret_["e_online_status"]       =    get_unformatted_string($row->e_online_status);
					  $ret_["e_is_minister"]     	 =    get_unformatted_string($row->e_is_minister);
					  $ret_["e_member_of_month"]     =    get_unformatted_string($row->e_member_of_month);
					  $ret_["e_disabled"]      		 =    get_unformatted_string($row->e_disabled);
					  $ret_["e_email_verified"]      =    get_unformatted_string($row->e_email_verified);
					  $ret_["dt_update_time"]        =    $row->dt_update_time;
					  $ret_["dt_created_on"]         =    $row->dt_created_on;
					  
					   
					  $ret_["s_name"]     	 =    get_unformatted_string($row->s_name);
					  $ret_["s_profile_url_suffix"] = get_unformatted_string($row->s_profile_url_suffix);
					  $ret_["s_languages"]     	 =    get_unformatted_string($row->s_languages);
					  $ret_["i_profile_visits"]            =    $row->i_profile_visits;  //added
					  					  
					  
					   $ret_["i_total_user_photo"]        		  	  =    intval($row->i_total_user_photo);
					   $ret_["i_total_user_video"]        		  	  =    intval($row->i_total_user_video);
                      
					   //// new fields...
                       $ret_["s_password"]                =    get_unformatted_string($row->s_password); 
                       $ret_["i_total_user_albums"]       =    intval($row->i_total_user_albums);
                       
                       $ret_["i_is_admin"]                =    $row->i_is_admin; 
					   
					   
					   $ret_["s_status"]     	 =    get_unformatted_string($row->s_status);
					   
					   ### education
					   
					  $ret_["s_school_name"]      	 =    get_unformatted_string($row->s_school_name);
					  $ret_["s_school_city"]      =    get_unformatted_string($row->s_school_city);
					  $ret_["s_school_state"]         =    get_unformatted_string($row->s_school_state);
					  $ret_["s_school_country"]        =    get_unformatted_string($row->s_school_country);
					  
					  $ret_["s_school_loc"]      =   $ret_["s_school_city"].' '.
					  								 $ret_["s_school_state"] .' '.$ret_["s_school_country"]; 
					  $ret_["i_class_year"]     	 =    intval($row->i_class_year);
					  $ret_["s_degree"]       		 =    get_unformatted_string($row->s_degree);
					  
              
                  }    
                  $rs->free_result();          
              }
              
              unset($s_qry,$rs,$row,$i_id);
              
                
          }
          return $ret_;
          
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }            
        
         
  public function total_feedback($i_id)
  {
        try
        {
          $ret_=0;
          $s_qry="SELECT COUNT(*) AS i_total "
                ."FROM ".$this->db->VW_AUCTION ." A WHERE A.i_user_id = '{$i_id}' AND A.i_feedback_status = 1 "
                ;
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
  public function avg_rating($i_id)
  {
        try
        {
          $ret_=0;
          $s_qry="SELECT AVG(i_rating) AS i_rate "
                ."FROM ".$this->db->VW_AUCTION ." A WHERE A.i_user_id = '{$i_id}' AND A.i_feedback_status = 1 "
                ;
          $rs=$this->db->query($s_qry);
          $i_cnt=0;
          if(is_array($rs->result()))
          {
              foreach($rs->result() as $row)
              {
                  $ret_=round($row->i_rate,2); 
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
    
    
    public function edit_info($info, $user_id)
    {
        try
        {
            $i_ret_=0;////Returns false
            
            #pr($info,1);exit;
            if(!empty($info))
            {
                $c_ins=count($info);
                // check if user's data already exists
                $arr=$this->check_edu_info($user_id);
                //pr($arr);exit;
                #pr($info);
                if(empty($arr))
                {
                    $i_ret_ =  $this->insert_edu($info);
                                    
				}
				else
				{
					
                        $this->db->update($this->db->USER_EDUCATION, $info);
                        
                        $i_ret_=$this->db->affected_rows();   
                        #$this->db->last_query();  exit;
                        if($i_ret_)
                        {
                          $this->db->trans_commit();///new   
                        }
                        else
                        {
                          $this->db->trans_rollback();///new
                        }  
                    
					
					  
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
   
    public function edit_edu_info($info, $user_id, $record_id=null)
    {
        
        try
        {
            $i_ret_=0;////Returns false
            if(!empty($info))
            {
                $c_ins=count($info);
                // check if user's data already exists
                #if( !empty($record_id) ) :
                    $is_exists = $this->check_edu_info($record_id);
                     #echo "is exists in model : ".$is_exists;
                    if($is_exists < 1)
                    {
                        $i_ret_ =  $this->insert_edu($info);
                        //echo $this->db->last_query();
                    }
                    else
                    {
                       	    $this->db->where('id', $record_id);
						    $this->db->update($this->db->USER_EDUCATION, $info);
                            #echo $this->db->last_query();
                            $i_ret_=$this->db->affected_rows();   
                            //echo $this->db->last_query();  exit;
                            if($i_ret_)
                            {
                              $this->db->trans_commit();///new   
                            }
                            else
                            {
                              $this->db->trans_rollback();///new
                            }  
                     }
                #endif;
            }
            unset($s_qry);
            return $i_ret_;
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }
    // check education data exists or not
    public function check_edu_info($record_id)
    {
		$sql = sprintf("SELECT count(*) as total_count from cg_user_education where id  = '%s'"
							, $record_id);
        $query=$this->db->query($sql);
	    $result_arr = $query->result_array();
		//pr($result_arr[0]['total_count'] ,1);
        return $result_arr[0]['total_count'];
    }
      
   
     public function insert_edu($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		//pr($arr);
		$this->db->insert($this->db->USER_EDUCATION, $arr);
		 //echo $this->db->last_query();  exit;
		return $this->db->insert_id();
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
    {
        try
        {
            $i_ret_=0;////Returns false
    
            if(intval($i_id)>0)
            {
                $s_qry="DELETE FROM ".$this->db->USERS." ";
                $s_qry.=" WHERE id=? ";
                
                $this->db->trans_begin();///new  
                $this->db->query($s_qry, array(intval($i_id)) );
                $i_ret_=$this->db->affected_rows();        
                if($i_ret_)
                {
                   /* $logi["msg"]="Deleting ".$this->db->USERS." ";
                    $logi["sql"]= serialize(array($s_qry, array(intval($i_id))) ) ;
                    $this->log_info($logi); 
                    unset($logi);*/
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
    public function delete_info_db($db_id)
    {
        try
        {
            $i_ret_=0;////Returns false
            if(intval($db_id)>0)
            {
                
                $q=sprintf("DELETE FROM %s WHERE id=%s",$this->db->USER_EDUCATION,$db_id);
                
                $this->db->trans_begin();///new  
                $this->db->query($q);
                //echo $this->db->last_query();
                $i_ret_=$this->db->affected_rows();  
                //echo "result_model : ".$i_ret_;      
                if($i_ret_)
                {

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
        catch(Excaption $err_obj)
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
    

       
}

