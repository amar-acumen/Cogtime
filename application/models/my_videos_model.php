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
class My_videos_model extends Base_model
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
    }   //  end of __construct()
    
    
    public function create_video_album($arr=array())
    {
        $query = $this->db->insert($this->db->VIDEO_ALBUM, $arr);
        $res = $this->db->insert_id($query);
        return $res;
    }
    
    function get_all_video_album_name($id)
    {
        $query = "SELECT * FROM ".$this->db->VIDEO_ALBUM." WHERE i_user_id = '".$id."'";
        $res = $this->db->query($query)->result_array();
        return $res;
    }
    
    
    //------------------------ edit album ---------------------------
    function edit_video_album($arr=array())
    {
        //pr($arr);
        $res = $this->db->update($this->db->VIDEO_ALBUM,$arr,array('id'=>$arr['id']));
        return $res;
    }
    //------------------------ end edit album ---------------------------
    
    
    // ----------------------- my video index page --------------------------
    function insert_new_video($arr = array())
    {
        $this->db->insert($this->db->USER_VIDEOS, $arr);
        $res = $this->db->insert_id();
        return $res;
        
    }
    
    function create_video_album_at_video_upload($arr=array())
    {
        $this->db->insert($this->db->VIDEO_ALBUM,$arr);
        $res = $this->db->insert_id();
        return $res;
    }
    
    
    
    
    function get_all_video_albums_with_count($id,$start_limit,$end_limit)
    {
        
	   if("$start_limit" == ''){
		    $query = "SELECT * FROM cg_video_album WHERE i_user_id = '".$id."' ORDER BY dt_created_on  ";
	   }
	   else{
           $query = "SELECT * FROM cg_video_album WHERE i_user_id = '".$id."' ORDER BY dt_created_on DESC LIMIT ".$start_limit." ,  ".$end_limit;
	   }

        $res = $this->db->query($query)->result_array();		//echo $this->db->last_query(); 

        if(count($res))
        {
            foreach($res as $key=>$val)
            {
                $res[$key]['total_videos'] = $this->count_per_album($val['id']);
            }
        }
		
        
        return $res;
    }
    function count_per_album($album_id)
    {
        $sql = "SELECT count(*) as count FROM ".$this->db->USER_VIDEOS." WHERE  i_status =1 AND  i_video_album_id='".$album_id."'";
        $res = $this->db->query($sql)->row_array();
        return $res['count'];
    }
    
    function get_total_no_of_albums($id)    // also called from manage video albums
    {
        $sql = "SELECT count(*) as total FROM ".$this->db->VIDEO_ALBUM." WHERE `i_user_id` = '".$id."'";
        $res =$this->db->query($sql)->result_array();
        return $res[0]['total'];
    }

    
    function get_all_videos($id, $start_limit, $end_limit)
    {
        $query = "SELECT * FROM ".$this->db->USER_VIDEOS." WHERE  i_status =1 AND  i_user_id= '".$id."' ORDER BY id DESC LIMIT ".$start_limit." , ".$end_limit;
        $res = $this->db->query($query)->result_array();
        return $res;
    }
    
    function get_total_no_of_videos($id)
    {
        $sql = "SELECT count(*) as count FROM ".$this->db->USER_VIDEOS." WHERE i_status =1 AND `i_user_id`='".$id."'";
        $res = $this->db->query($sql)->result_array();
        return $res[0]['count'];
        
    }
    
    
    function get_total_no_of_videos_of_album_by_album_id($id,$where)
    {
        $sql = "SELECT count(*) as count FROM ".$this->db->USER_VIDEOS." WHERE  i_status =1 AND  `i_user_id`='".$id."' ".$where;
        $res = $this->db->query($sql)->result_array();
        return $res[0]['count'];
        
    }
    
    
        ### all videos with comments ###
    
    /* public function get_allvideos_with_comments_by_user_id_($user_id, $s_where="", $start_limit="", $no_of_page="") {
		$logged_user_id = intval(decrypt($this->session->userdata('user_id')));
        
		
		#### adding for public:::: 
		$mp_sql = "  SELECT  if ((mp.i_friend_privacy = '0' &&  mp.i_netpal_privacy = '0' 
										&&  mp.i_prayer_partner_privacy = '0' && 
							            mp.i_ring_privacy = '0' &&  mp.i_prayer_group_privacy = '0'
										) , 'Y' , 'N') as str
						FROM cg_privacy_settings mp 
						WHERE
						mp.i_user_id = '".$user_id."' and mp.s_section_name = 'video' " ;
		 
		 $res_data = $this->db->query($mp_sql); 
		 $res_data_arr = $res_data->result_array();
		 
		// pr($res_data_arr,1);
		//echo $this->db->last_query();exit;
		if($res_data_arr[0]['str'] == 'Y'){ # public then fetch all
			
			if("$start_limit" == "") {
				$sql = sprintf("SELECT * FROM {$this->db->USER_VIDEOS} AS v 
								WHERE v.i_status =1 AND v.i_user_id = %s %s ORDER BY id DESC ",$user_id,$s_where);
			}
			else {
				 $sql = sprintf("SELECT * FROM {$this->db->USER_VIDEOS} AS v
								  WHERE v.i_status =1 AND  v.i_user_id = %s %s ORDER BY id DESC 
								  LIMIT %s, %s ", $user_id, $s_where, $start_limit, $no_of_page);
			}
		
		}
		else {
			
			if("$start_limit" == "") {
				$sql = sprintf("SELECT * FROM {$this->db->videolbum_privacy}  AS vp, {$this->db->USER_VIDEOS} AS v 
								WHERE vp.i_video_album_id=v.i_video_album_id AND vp.i_user_id='".$logged_user_id."' 
								AND v.i_status =1 AND v.i_user_id = %s %s ORDER BY id DESC ",$user_id,$s_where);
			}
			else {
				 $sql = sprintf("SELECT * FROM {$this->db->videolbum_privacy}  AS vp, {$this->db->USER_VIDEOS} AS v
								  WHERE  vp.i_video_album_id=v.i_video_album_id AND vp.i_user_id='".$logged_user_id."' 
								AND v.i_status =1 AND  v.i_user_id = %s %s ORDER BY id DESC LIMIT %s, %s ", $user_id, $s_where, $start_limit, $no_of_page);
			}
		}
        $query = $this->db->query($sql);
		
        $result_arr = $query->result_array();
        $this->load->model('media_comments_model');
        if(is_array($result_arr)){
            foreach($result_arr as $key=>$val){
                $result_arr[$key]['total_comments'] = $this->media_comments_model->get_total_by_newsfeed_id($val['id'],'video');
                $result_arr[$key]['comments_arr'] =  $this->media_comments_model->get_by_newsfeed_id($val['id'],'video');
                
            }
        }
//pr($result_arr);
        return $result_arr;
    }*/
    
    public function get_count_allvideos_with_comments_by_user_id_($user_id, $s_where="", $start_limit="", $no_of_page="") {
		$logged_user_id = intval(decrypt($this->session->userdata('user_id')));
		
		$sql = "SELECT count(*) AS count FROM {$this->db->videolbum_privacy}  AS vp, {$this->db->USER_VIDEOS} AS v
		WHERE  vp.i_video_album_id=v.i_video_album_id AND vp.i_user_id='".$logged_user_id."' 
		AND v.i_status =1 AND  v.i_user_id = '".$user_id."' ";
		
		
		$res = $this->db->query($sql)->result_array();
		return $res[0]['count'];
    }
    
    
    function get_video_by_id($db_id)
    {
        $sql = "SELECT * FROM ".$this->db->USER_VIDEOS." WHERE id= '".$db_id."'";
        $res = $this->db->query($sql)->result_array();

        return $res[0]['s_video_file_name'];
    }
    
    function get_video_title_by_id($db_id)
    {
        $sql = "SELECT * FROM ".$this->db->USER_VIDEOS." WHERE id= '".$db_id."'";
        $res = $this->db->query($sql)->result_array();
        
        return $res[0]['s_title'];
    }
    
    
    
    //---------------------------- search video ------------------------------
    function search_get_all_videos($id, $where, $start_limit, $end_limit)
    {
        $query = "SELECT * FROM ".$this->db->USER_VIDEOS." WHERE  i_status =1 AND i_user_id= '".$id."' {$where} ORDER BY id DESC LIMIT ".$start_limit." , ".$end_limit;
        $res = $this->db->query($query)->result_array();
        $this->load->model('media_comments_model');
        if(is_array($res)){
            foreach($res as $key=>$val){
                $res[$key]['total_comments'] = $this->media_comments_model->get_total_by_newsfeed_id($val['id'],'video');
            }
        }
        
        return $res;
    }
    
    function search_get_total_no_of_videos($id,$where)
    {
        $sql = "SELECT count(*) as count FROM ".$this->db->USER_VIDEOS." WHERE  i_status =1 AND `i_user_id`='".$id."' ".$where;
        $res = $this->db->query($sql)->result_array();
        return $res[0]['count'];
        
    }
    
   // *** unused
    function rest_total_videos($id, $start_limit, $end_limit)
    {
        $sql = "SELECT count(*) as count FROM ".$this->db->USER_VIDEOS." WHERE  i_status =1 AND `i_user_id`='".$id."' LIMIT ".$start_limit.",".$end_limit;
        $res = $this->db->query($sql)->result_array();
        echo "q :".$this->db->last_query();
        return $res[0]['count'];
    }
    
    //------------------------ delete video(s)----------------------------
    function delete_video($video_id)
    {
        $sql = "DELETE FROM ".$this->db->USER_VIDEOS." WHERE id='".$video_id."'";
        $res = $this->db->query($sql);
        return $res;
    }
    
    function get_video_img_by_id($video_id)
    {
        $sql = "SELECT `s_video_image` FROM ".$this->db->USER_VIDEOS." WHERE id='".$video_id."'";
        $res = $this->db->query($sql)->row_array();
        return $res;
    }
    
    // ----------------------- end of my video index page --------------------------
    
    //----------------------------- delete video album --------------------------------
    function delete_video_album_by_id($album_id)
    {
        
        $aql1 = "DELETE FROM ".$this->db->VIDEO_ALBUM." WHERE id='".$album_id."'";
        $res1 = $this->db->query($aql1);
        
        
        $sql2 = "DELETE FROM ".$this->db->VIDEO_ALBUM." WHERE i_video_album_id='".$album_id."'";
        $res2 = $this->db->query($sql2);
		
		$this->db->query("DELETE FROM {$this->db->videolbum_privacy} WHERE i_video_album_id='".$album_id."'");
        
    }
    
    function get_album_img_by_album_id($video_id)
    {
        $sql = "SELECT `s_video_album_image` FROM ".$this->db->VIDEO_ALBUM." WHERE `id`='".$video_id."'";
        $res = $this->db->query($sql)->row_array();
        return $res;
    }
    //----------------------------- end of delete video album --------------------------------
    //------------------------ manage video albums --------------------------------
    function manage_video_albums_with_count($id,$start_limit,$end_limit)
    {
        /*$query = sprintf("SELECT count(*) AS count,
                        a.id,
                        a.s_name,
                        a.s_video_album_image,
                        a.s_desc,
                        a.dt_created_on 
                    FROM   cg_user_videos as v
                    left join 
                        cg_video_album as a
                    on v.i_video_album_id=a.id
                    WHERE v.`i_user_id`=%s
                    group by a.id LIMIT %s, %s",$id,$start_limit,$end_limit);
        */

         $query = "SELECT * FROM cg_video_album WHERE i_user_id = '".$id."' ORDER BY dt_created_on DESC LIMIT ".$start_limit.", ".$end_limit;
                       
        //$query = sprintf("SELECT * FROM %s WHERE `i_user_id`= %s", $this->db->VIDEO_ALBUM, $id);
        $res = $this->db->query($query)->result_array();
        if(count($res))
        {
            foreach($res as $key=>$val)
            {
                $res[$key]['count'] = $this->count_per_album($val['id']);
            }
        }
     
        

        return $res;
    }
    
    function get_all_videos_of_album($id,$where='',$start_limit,$end_limit)
    {
       // echo "album id in m,o : ".$id;
      
         $sql = "SELECT * FROM ".$this->db->USER_VIDEOS." WHERE `i_video_album_id`= '".$id."''".$this->db->USER_VIDEOS."' {$where} ORDER BY i_order DESC LIMIT ".$start_limit.", ".$end_limit;
        $res = $this->db->query($sql)->result_array();
        
        echo $sql;        exit();
        $this->load->model("utility_model");
        $s_where =  " WHERE i_video_album_id = {$id}";
        foreach($res as $key=>$val){


                
        $res[$key]['image_rank'] = $this->utility_model->RankingRowCreate($res[$key]['i_order'],
                                                                                   $res[$key]['id'],
                                                                                   $res[$key]['i_video_album_id'],
                                                                                   $this->db->USER_VIDEOS,
                                                                                   $s_where);
                                                                                   
                
        }
        
        $this->load->model('media_comments_model');
        if(is_array($res)){
            foreach($res as $key=>$val){
                $res[$key]['total_comments'] = $this->media_comments_model->get_total_by_newsfeed_id($val['id'],'video');
            }
        }
         // pr($res,1); 
        return $res;
    }
    
    
    function get_total_no_of_videos_of_album($id)
    {
        $sql = "SELECT count(*) as count FROM ".$this->db->USER_VIDEOS." WHERE  i_status =1 AND  `i_video_album_id`='".$id."'";
        $res = $this->db->query($sql)->result_array();
        return $res[0]['count'];
    }
    
    function get_album_info($album_id)
    {
        $sql = "SELECT * FROM ".$this->db->VIDEO_ALBUM." WHERE `id`='".$album_id."'";
        $res = $this->db->query($sql)->result_array();
		
		 $res[0]['total_videos'] = $this->get_total_no_of_album_videos($album_id);
        return $res;
    }
    
    function get_total_no_of_album_videos($album_id)
    {
//echo $album_id;
        $sql = sprintf("SELECT count(*) as count FROM %s WHERE  i_status =1 AND `i_video_album_id`=%s",$this->db->USER_VIDEOS,$album_id);
        $res = $this->db->query($sql)->result_array();
//echo $this->db->last_query();
//pr($res);
        return $res[0]['count'];
        
    }
    
    
    public function get_i_order($i_album_id)
    {
        try
        {
          $ret_=0;
          $s_qry =  "SELECT IFNULL(MAX(i_order),1) AS `max_i_order` FROM "
                      .$this->db->USER_VIDEOS. " WHERE  i_status =1 AND i_video_album_id = {$i_album_id}";
          $rs=$this->db->query($s_qry); 
          #echo $this->db->last_query();
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
    
    

    
    //------------------------ end of manage video albums --------------------------------
    
    
    //------------------------------------ organize change video album-------------------------------------
    function change_video_album($video_id,$now_album_id)
    {
        $sql="UPDATE ".$this->db->USER_VIDEOS." SET i_video_album_id='".$now_album_id."' WHERE id='".$video_id."'";
        $this->db->query($sql);
    }
    
    function get_video_info_by_id($video_id)
    {
        $sql = "SELECT * FROM ".$this->db->USER_VIDEOS." WHERE id= '".$video_id."'";
        $res = $this->db->query($sql)->result_array();
        return $res;
    }
    
    function update_video($arr=array())
    {
        $r=$this->db->update($this->db->USER_VIDEOS,$arr,array('id'=>$arr['id']));
        
        return $r;
    }
    
    //------------------------------------ end of organize change video album-------------------------------------
	
	
	
	### to get user detail by media id ###
	
	### by mediad id ###
	public function get_user_details_by_media_id($media_id, $s_where="", $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = 'SELECT * FROM '.$this->db->USER_VIDEOS.'  WHERE id = "'.$media_id.'" '.$s_where.' ORDER BY id DESC ';
		}
		else {
			 $sql = 'SELECT * FROM '.$this->db->USER_VIDEOS
								.'  WHERE id = "'.$media_id.'" {$s_where} ORDER BY id DESC LIMIT '.$start_limit.', '.$no_of_page;
		}

		$query = $this->db->query($sql);
        
		$result_arr = $query->result_array();
		/*$this->load->model('media_comments_model');
		if(is_array($result_arr)){
			foreach($result_arr as $key=>$val){
				$result_arr[$key]['total_comments'] = $this->media_comments_model->get_total_by_newsfeed_id($val['id'],'video');
			}
		}*/

		return $result_arr[0];
	}		
	
	public function get_total_by_user_id($user_id, $s_where) {
		$sql = "SELECT count(*) count FROM ".$this->db->USER_VIDEOS."  where  i_status =1 AND  i_user_id = '".$user_id."' ".$s_where;
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();

		return $result_arr[0]['count'];
	}
	
	public function get_privacy_settings_by_video_album_id($i_album_id)
	{
		 $s_qry =  "SELECT * FROM "
					  .$this->db->videolbum_privacy. " WHERE i_video_album_id = {$i_album_id}";
		  $rs=$this->db->query($s_qry); 
		  #echo $this->db->last_query();
		  foreach($rs->result() as $row)
		  {
			  if($row->s_section=='Ring User' || $row->s_section=='Prayer Group')
			  	$returnarr[$row->s_section][$row->i_section_id][]	= $row->i_user_id;
			else
				$returnarr[$row->s_section][]	= $row->i_user_id;
		  }
          return $returnarr;
	}
	
	/*function get_all_video_albums_with_privacy_and_count($id,$start_limit,$end_limit)
    {
	   $logged_user_id = intval(decrypt($this->session->userdata('user_id')));
	   if("$start_limit" == ''){
		    $query = sprintf("SELECT * FROM {$this->db->videolbum_privacy} AS vp, cg_video_album AS va 
								WHERE vp.i_video_album_id=va.id AND 
								vp.i_user_id='".$logged_user_id."' AND va.i_user_id = %s ORDER BY dt_created_on  ",$id);
	   }
	   else{
           $query = sprintf("SELECT * FROM {$this->db->videolbum_privacy} AS vp, cg_video_album AS va 
								WHERE vp.i_video_album_id=va.id  AND 
								vp.i_user_id='".$logged_user_id."' AND
								va.i_user_id = %s ORDER BY dt_created_on DESC LIMIT %s , %s ",$id,$start_limit,$end_limit);
	   }
        $res = $this->db->query($query)->result_array();		#echo $this->db->last_query(); exit;
        if(count($res))
        {
            foreach($res as $key=>$val)
            {
                $res[$key]['total_videos'] = $this->count_per_album($val['id']);
            }
        }
        return $res;
    }
	
	function get_total_no_of_albums_with_privacy($id)    // also called from manage video albums
    {
        $sql = sprintf("SELECT count(*) as total FROM {$this->db->videolbum_privacy} AS vp, {$this->db->VIDEO_ALBUM} AS va 
						WHERE vp.i_video_album_id=va.id  AND 
								vp.i_user_id='".$logged_user_id."' AND
								va.i_user_id = %s",$id);
        $res =$this->db->query($sql)->result_array();
        return $res[0]['total'];
    }
	*/
	
	
	
	function get_all_video_albums_with_privacy_and_count($id,$start_limit,$end_limit)
    {
	  	$no_of_page = $end_limit;
		$uid = $logged_user_id = intval(decrypt($this->session->userdata('user_id')));
		$profile_id = $id;
		
		#### adding for public:::: 
		 $mp_sql = "  SELECT  if ((mp.i_friend_privacy = '0' &&  mp.i_netpal_privacy = '0' &&  mp.i_prayer_partner_privacy = '0' 
		 					&& mp.i_ring_privacy = '0' &&  mp.i_prayer_group_privacy = '0'
						    ) , '' ,
						       (concat(IF(mp.i_friend_privacy = '1', '\"Friend\", ', ''),''
		 							, IF(mp.i_netpal_privacy = '1', '\"Netpal\", ',''),'' 
									,IF(mp.i_prayer_partner_privacy = '1', '\"Prayer Partner\", ', '') , ''
									,IF(mp.i_ring_privacy = '1', '\"Ring User\", ', ''),''
									,IF(mp.i_prayer_group_privacy = '1', '\"Prayer Group\", ', '')))) as str
						FROM cg_privacy_settings mp 
						WHERE
						mp.i_user_id = '".$profile_id."' and mp.s_section_name = 'video'" ;
		 
		 $res_data = $this->db->query($mp_sql); 
		 $res_data_arr = $res_data->result_array();
		//pr($res_data_arr);
	    //echo $res_data_arr[0]['str'].' #$#$'; 
	    if($res_data_arr[0]['str'] != '') 
	    	$PRIVACY_STR = " AND pp.s_section IN  (".substr($res_data_arr[0]['str'], 0, -2).")";
		else
			$PRIVACY_STR = '';
	 
				  

		 $check_user_level_perm =	"SELECT COUNT(pp.i_video_album_id) as total FROM cg_video_album AS pa 
										LEFT JOIN {$this->db->USER_VIDEOS} AS up ON up.i_video_album_id=pa.id 
										LEFT JOIN {$this->db->videolbum_privacy} AS pp ON pp.i_video_album_id=pa.id 
										WHERE pa.i_user_id= '".$profile_id."' ";
			 
		 $is_user_level = $this->db->query($check_user_level_perm)->result_array();
		
		// echo 'total::: '.($is_user_level[0]['total']);
		 #### if public no restriction show all 
		if($res_data_arr[0]['str'] == ''){
			
			 $s_qry = " SELECT up.* FROM cg_video_album AS up
								  WHERE  up.i_user_id =".$profile_id."  
								   ORDER BY id DESC LIMIT {$start_limit},{$no_of_page}
			   					";
		      $rs=$this->db->query($s_qry); 	//pr($rs->result());
		 
			    $result_arr = $rs->result_array();
				$this->load->model('media_comments_model');
				if(is_array($result_arr)){
					foreach($result_arr as $key=>$val){
						$result_arr[$key]['total_videos'] = $this->count_per_album($val['id']);
						
					}
		   		 }
		
		}
		### if user level permission not given  i.e $is_user_level[0]['total'] =0  
		#then check for group level else check user level
		elseif($is_user_level[0]['total'] == 0){
			
			## check user relationship if logged user has privacy permission of album 	
			$network_arr = CheckUserNetwork($profile_id);
			//pr($network_arr);
			
			$PRIVACY_ARR = explode(', ',substr($res_data_arr[0]['str'], 0, -2)); 
			//pr($PRIVACY_ARR);
			
			$privacy_result = array_intersect($network_arr, $PRIVACY_ARR);
			
			if(count($privacy_result)){ 
			### fetch all albums videos without user level permission i.e. only group level permission
			### check 
			   
			$s_qry = " SELECT pa.*
							FROM cg_video_album AS pa 
							LEFT JOIN {$this->db->USER_VIDEOS} AS up ON up.i_video_album_id=pa.id 
							WHERE pa.id not in (SELECT DISTINCT(pp.i_video_album_id)
														FROM cg_video_album AS pa1
														JOIN {$this->db->videolbum_privacy} AS pp ON pp.i_video_album_id = pa1.id  
														AND pa1.i_user_id = '".$profile_id."')
							AND pa.i_user_id = '".$profile_id."'
							GROUP BY pa.id
							ORDER BY pa.id DESC LIMIT {$start_limit},{$no_of_page}
						   "; 
						  
						 // echo '## '.nl2br($s_qry);
			   $rs=$this->db->query($s_qry); 	//pr($rs->result());
		 
			    $result_arr = $rs->result_array();
				$this->load->model('media_comments_model');
				if(is_array($result_arr)){
					foreach($result_arr as $key=>$val){
						$result_arr[$key]['total_videos'] = $this->count_per_album($val['id']);
					}
		   		 }
			}
			
		}
		else{	### fetch all albums videos with user level permission	  
			$s_qry = "SELECT pa.* 
							FROM cg_video_album AS pa 
							LEFT JOIN {$this->db->USER_VIDEOS} AS up ON up.i_video_album_id=pa.id 
							WHERE pa.id not in (SELECT DISTINCT(pp.i_video_album_id)
														FROM cg_video_album AS pa1
														JOIN {$this->db->videolbum_privacy} AS pp ON pp.i_video_album_id = pa1.id  
														AND pa1.i_user_id = '".$profile_id."')
							AND pa.i_user_id = '".$profile_id."' 
							
							UNION 
							
							SELECT pa.* FROM 
							   cg_video_album AS pa LEFT JOIN  
							  {$this->db->USER_VIDEOS} AS up ON up.i_video_album_id=pa.id 
							  LEFT JOIN ".$this->db->videolbum_privacy." AS pp ON pp.i_video_album_id=pa.id 
							  WHERE pp.i_user_id='".$uid."' AND pa.i_user_id = '".$profile_id."'
							  ".$PRIVACY_STR."
							  GROUP BY pa.id
							  ORDER BY id DESC LIMIT {$start_limit},{$no_of_page}
						  "; 
						  
						  //echo nl2br($s_qry);
 		    $rs=$this->db->query($s_qry); #pr($rs->result());
		 
		    $result_arr = $rs->result_array();
			$this->load->model('media_comments_model');
			if(is_array($result_arr)){
				foreach($result_arr as $key=>$val){
					$result_arr[$key]['total_videos'] = $this->count_per_album($val['id']);
				}
		    }
#pr($result_arr);
				  
		}
		return $result_arr;
    }
	
	function get_total_no_of_albums_with_privacy($id)    // also called from manage video albums
    {
        	
		$uid = $logged_user_id = intval(decrypt($this->session->userdata('user_id')));
		$profile_id = $id;
		
		#### adding for public:::: 
		 $mp_sql = "  SELECT  if ((mp.i_friend_privacy = '0' &&  mp.i_netpal_privacy = '0' &&  mp.i_prayer_partner_privacy = '0' 
		 					&& mp.i_ring_privacy = '0' &&  mp.i_prayer_group_privacy = '0'
						    ) , '' ,
						       (concat(IF(mp.i_friend_privacy = '1', '\"Friend\", ', ''),''
		 							, IF(mp.i_netpal_privacy = '1', '\"Netpal\", ',''),'' 
									,IF(mp.i_prayer_partner_privacy = '1', '\"Prayer Partner\", ', '') , ''
									,IF(mp.i_ring_privacy = '1', '\"Ring User\", ', ''),''
									,IF(mp.i_prayer_group_privacy = '1', '\"Prayer Group\", ', '')))) as str
						FROM cg_privacy_settings mp 
						WHERE
						mp.i_user_id = '".$profile_id."' and mp.s_section_name = 'video'" ;
		 
		 $res_data = $this->db->query($mp_sql); 
		 $res_data_arr = $res_data->result_array();
		//pr($res_data_arr);
	    //echo $res_data_arr[0]['str'].' #$#$'; 
	    if($res_data_arr[0]['str'] != '') 
	    	$PRIVACY_STR = " AND pp.s_section IN  (".substr($res_data_arr[0]['str'], 0, -2).")";
		else
			$PRIVACY_STR = '';
	 
				  

		 $check_user_level_perm =	"SELECT COUNT(pp.i_video_album_id) as total FROM cg_video_album AS pa 
										LEFT JOIN {$this->db->USER_VIDEOS} AS up ON up.i_video_album_id=pa.id 
										LEFT JOIN {$this->db->videolbum_privacy} AS pp ON pp.i_video_album_id=pa.id 
										WHERE pa.i_user_id= '".$profile_id."' ";
			 
		 $is_user_level = $this->db->query($check_user_level_perm)->result_array();
		
		// echo 'total::: '.($is_user_level[0]['total']);
		 #### if public no restriction show all 
		if($res_data_arr[0]['str'] == ''){
			
			  $s_qry = " SELECT count(up.id) as total FROM cg_video_album AS up
								  WHERE  up.i_user_id =".$profile_id."  
								   
			   ";
		      $rs=$this->db->query($s_qry); 	//pr($rs->result());
		 
			    $result_arr = $rs->result_array();
				
		
		}
		### if user level permission not given  i.e $is_user_level[0]['total'] =0  
		#then check for group level else check user level
		elseif($is_user_level[0]['total'] == 0){
			
			## check user relationship if logged user has privacy permission of album 	
			$network_arr = CheckUserNetwork($profile_id);
			//pr($network_arr);
			
			$PRIVACY_ARR = explode(', ',substr($res_data_arr[0]['str'], 0, -2)); 
			//pr($PRIVACY_ARR);
			
			$privacy_result = array_intersect($network_arr, $PRIVACY_ARR);
			
			if(count($privacy_result)){ 
			### fetch all albums videos without user level permission i.e. only group level permission
			### check 
			   
			$s_qry = " SELECT count(up.id) as total
							FROM cg_video_album AS pa 
							LEFT JOIN {$this->db->USER_VIDEOS} AS up ON up.i_video_album_id=pa.id 
							WHERE pa.id not in (SELECT DISTINCT(pp.i_video_album_id)
														FROM cg_video_album AS pa1
														JOIN {$this->db->videolbum_privacy} AS pp ON pp.i_video_album_id = pa1.id  
														AND pa1.i_user_id = '".$profile_id."')
							AND pa.i_user_id = '".$profile_id."'
							GROUP BY pa.id
							
						   "; 
						  
						 // echo '## '.nl2br($s_qry);
			   $rs=$this->db->query($s_qry); 	//pr($rs->result());
		 
			    $result_arr = $rs->result_array();
				
			}
			
		}
		else{	### fetch all albums videos with user level permission	  
			$s_qry = "SELECT COUNT(*) as total FROM(
						SELECT pa.id 
							FROM cg_video_album AS pa 
							LEFT JOIN {$this->db->USER_VIDEOS} AS up ON up.i_video_album_id=pa.id 
							WHERE pa.id not in (SELECT DISTINCT(pp.i_video_album_id)
														FROM cg_video_album AS pa1
														JOIN {$this->db->videolbum_privacy} AS pp ON pp.i_video_album_id = pa1.id  
														AND pa1.i_user_id = '".$profile_id."')
							AND pa.i_user_id = '".$profile_id."' 
							
							UNION 
							
							SELECT pa.id
							 FROM cg_video_album AS pa LEFT JOIN  
							  {$this->db->USER_VIDEOS} AS up ON up.i_video_album_id=pa.id 
							  LEFT JOIN ".$this->db->videolbum_privacy." AS pp ON pp.i_video_album_id=pa.id 
							  WHERE pp.i_user_id='".$uid."' AND pa.i_user_id = '".$profile_id."'
							  ".$PRIVACY_STR."
							  GROUP BY pa.id
							  ) as drvd_tbl
						  "; 
						  
						  //echo nl2br($s_qry);
 		    $rs=$this->db->query($s_qry); #pr($rs->result());
		 
		    $result_arr = $rs->result_array();
				  
		}
        return $result_arr[0]['total'];
    }
	
	
	###### changed
	 public function get_allvideos_with_comments_by_user_id_($user_id, $s_where="", $start_limit="", $no_of_page="") {
		
		$uid = $logged_user_id = intval(decrypt($this->session->userdata('user_id')));
		$profile_id = $user_id;
		
		#### adding for public:::: 
		 $mp_sql = "  SELECT  if ((mp.i_friend_privacy = '0' &&  mp.i_netpal_privacy = '0' &&  mp.i_prayer_partner_privacy = '0' 
		 					&& mp.i_ring_privacy = '0' &&  mp.i_prayer_group_privacy = '0'
						    ) , '' ,
						       (concat(IF(mp.i_friend_privacy = '1', '\"Friend\", ', ''),''
		 							, IF(mp.i_netpal_privacy = '1', '\"Netpal\", ',''),'' 
									,IF(mp.i_prayer_partner_privacy = '1', '\"Prayer Partner\", ', '') , ''
									,IF(mp.i_ring_privacy = '1', '\"Ring User\", ', ''),''
									,IF(mp.i_prayer_group_privacy = '1', '\"Prayer Group\", ', '')))) as str
						FROM cg_privacy_settings mp 
						WHERE
						mp.i_user_id = '".$profile_id."' and mp.s_section_name = 'video'" ;
		 
		 $res_data = $this->db->query($mp_sql); 
		 $res_data_arr = $res_data->result_array();
		//pr($res_data_arr);
	    //echo $res_data_arr[0]['str'].' #$#$'; 
	    if($res_data_arr[0]['str'] != '') 
	    	$PRIVACY_STR = " AND pp.s_section IN  (".substr($res_data_arr[0]['str'], 0, -2).")";
		else
			$PRIVACY_STR = '';
	 
				  

		 $check_user_level_perm =	"SELECT COUNT(pp.i_video_album_id) as total FROM cg_video_album AS pa 
										LEFT JOIN {$this->db->USER_VIDEOS} AS up ON up.i_video_album_id=pa.id 
										LEFT JOIN {$this->db->videolbum_privacy} AS pp ON pp.i_video_album_id=pa.id 
										WHERE pa.i_user_id= '".$profile_id."' ";
			 
		 $is_user_level = $this->db->query($check_user_level_perm)->result_array();
		
		// echo 'total::: '.($is_user_level[0]['total']);
		 #### if public no restriction show all 
		if($res_data_arr[0]['str'] == ''){
			
			  $s_qry = " SELECT up.* FROM {$this->db->USER_VIDEOS} AS up
								  WHERE  up.i_user_id =".$profile_id."  ORDER BY id DESC 
			   ";
		      $rs=$this->db->query($s_qry); 	//pr($rs->result());
		 
			    $result_arr = $rs->result_array();
				$this->load->model('media_comments_model');
				if(is_array($result_arr)){
					foreach($result_arr as $key=>$val){
						$result_arr[$key]['total_comments'] = $this->media_comments_model->get_total_by_newsfeed_id($val['id'],'video');
						$result_arr[$key]['comments_arr'] =  $this->media_comments_model->get_by_newsfeed_id($val['id'],'video');
						
					}
		   		 }
		
		}
		### if user level permission not given  i.e $is_user_level[0]['total'] =0  
		#then check for group level else check user level
		elseif($is_user_level[0]['total'] == 0){
			
			## check user relationship if logged user has privacy permission of album 	
			$network_arr = CheckUserNetwork($profile_id);
			//pr($network_arr);
			
			$PRIVACY_ARR = explode(', ',substr($res_data_arr[0]['str'], 0, -2)); 
			//pr($PRIVACY_ARR);
			
			$privacy_result = array_intersect($network_arr, $PRIVACY_ARR);
			
			if(count($privacy_result)){ 
			### fetch all albums videos without user level permission i.e. only group level permission
			### check 
			   
			$s_qry = " SELECT pa.id, up.*
							FROM cg_video_album AS pa 
							LEFT JOIN {$this->db->USER_VIDEOS} AS up ON up.i_video_album_id=pa.id 
							WHERE pa.id not in (SELECT DISTINCT(pp.i_video_album_id)
														FROM cg_video_album AS pa1
														JOIN {$this->db->videolbum_privacy} AS pp ON pp.i_video_album_id = pa1.id  
														AND pa1.i_user_id = '".$profile_id."')
							AND pa.i_user_id = '".$profile_id."'
							AND (SELECT count(up1.id)
													FROM {$this->db->USER_VIDEOS} AS up1
													WHERE up1.i_video_album_id = pa.id
													) > 0 
						   "; 
						  
						 // echo '## '.nl2br($s_qry);
			   $rs=$this->db->query($s_qry); 	//pr($rs->result());
		 
			    $result_arr = $rs->result_array();
				$this->load->model('media_comments_model');
				if(is_array($result_arr)){
					foreach($result_arr as $key=>$val){
						$result_arr[$key]['total_comments'] = $this->media_comments_model->get_total_by_newsfeed_id($val['id'],'video');
						$result_arr[$key]['comments_arr'] =  $this->media_comments_model->get_by_newsfeed_id($val['id'],'video');
						
					}
		   		 }
			}
			
		}
		else{	### fetch all albums videos with user level permission	  
			$s_qry = "SELECT pa.id,  up.* 
							FROM cg_video_album AS pa 
							LEFT JOIN {$this->db->USER_VIDEOS} AS up ON up.i_video_album_id=pa.id 
							WHERE pa.id not in (SELECT DISTINCT(pp.i_video_album_id)
														FROM cg_video_album AS pa1
														JOIN {$this->db->videolbum_privacy} AS pp ON pp.i_video_album_id = pa1.id  
														AND pa1.i_user_id = '".$profile_id."')
							AND pa.i_user_id = '".$profile_id."' 
							AND (SELECT count(up1.id)
													FROM {$this->db->USER_VIDEOS} AS up1
													WHERE up1.i_video_album_id = pa.id
													) > 0 
							
							UNION 
							
							SELECT DISTINCT(pp.i_user_id) AS uid,  up.* FROM 
							   cg_video_album AS pa LEFT JOIN  
							  {$this->db->USER_VIDEOS} AS up ON up.i_video_album_id=pa.id 
							  LEFT JOIN ".$this->db->videolbum_privacy." AS pp ON pp.i_video_album_id=pa.id 
							  WHERE pp.i_user_id='".$uid."' AND pa.i_user_id = '".$profile_id."'
							  ".$PRIVACY_STR."
							  AND (SELECT count(up1.id)
													FROM {$this->db->USER_VIDEOS} AS up1
													WHERE up1.i_video_album_id = pa.id
													) > 0 
							  
						  "; 
						  
						  //echo nl2br($s_qry);
 		    $rs=$this->db->query($s_qry); #pr($rs->result());
		 
		    $result_arr = $rs->result_array();
			$this->load->model('media_comments_model');
			if(is_array($result_arr)){
				foreach($result_arr as $key=>$val){
					$result_arr[$key]['total_comments'] = $this->media_comments_model->get_total_by_newsfeed_id($val['id'],'video');
					$result_arr[$key]['comments_arr'] =  $this->media_comments_model->get_by_newsfeed_id($val['id'],'video');
					
				}
		    }
#pr($result_arr);
				  
		}
		
		
		
		
        return $result_arr;
    }
    
}// end of model
