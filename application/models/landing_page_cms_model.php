<?php
include_once(APPPATH.'models/base_model.php');
class Landing_page_cms_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}

	public function get_contents($where='')
    {
        $sql = "SELECT * FROM {$this->db->LANDING_PAGE_CONTENT} {$where}";
        $result = $this->db->query($sql)->result_array();
        return $result;
    }
    
    public function insert_content($info)
    {
        $sql = $this->db->insert($this->db->LANDING_PAGE_CONTENT,$info);
    }
    
    public function update_content($info,$key)
    {

        $sql = $this->db->update($this->db->LANDING_PAGE_CONTENT,$info,$key);
        
    }
    
    
    
    
    //================================== media center ==============================================
    public function get_word_for_today_list($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY id DESC';
        
        $sql = "SELECT w.*, concat(a.s_name,' ',a.s_last_name) admin_name
                FROM {$this->db->WORD_FOR_TODAY} w
                    LEFT JOIN {$this->db->ADMIN_USER} a on w.i_posted_by=a.id {$where} {$s_order_by} {$limit}";
                    
                    
        $res = $this->db->query($sql)->result_array();
        return $res;
    }
    
    public function get_total_word($where='')
    {
        $sql = "SELECT COUNT(*) total_words FROM {$this->db->WORD_FOR_TODAY} w {$where}";
        $res = $this->db->query($sql)->result_array();
        return $res[0]['total_words'];
    }
    
    //--------------------------- insert word --------------------------------
    public function add_new_word($info)
    {
        $res = $this->db->insert($this->db->WORD_FOR_TODAY,$info);
        $last_id = $this->db->insert_id();
        return $last_id;
    }
    
    
    //--------------------------- fetch word info for edit--------------------------------
    public function get_word_info_by_word_id($id)
    {
        $sql = "SELECT * FROM {$this->db->WORD_FOR_TODAY} WHERE id={$id}";
        $res = $this->db->query($sql)->result_array();
        return $res[0];
    }
    //---------------------------- edit word -----------------------------
    public function update_word_info($info,$id)
    {
        $res = $this->db->update($this->db->WORD_FOR_TODAY,$info,array('id'=>$id));
    }
    
    
    
    public function delete_word_by_word_id($id)
    {
        $sql = "DELETE FROM {$this->db->WORD_FOR_TODAY} WHERE id={$id}";
        $this->db->query($sql);
    }
    
    //------------------------word like unlike------------
	
	
	public function postLikeUnlike($like_unlike_information_array,$like_or_unlike="like")
  {
        
         $response['value'] = false ;
         $response['message'] = "Success" ;
         $response['last_inserted_id'] = '';
		 
		# pr($like_unlike_information_array);
		// echo $like_or_unlike.' == '.$like_unlike_information_array['i_'.$like_or_unlike.'d_user_id'];

            //$like_or_unlike =strtolower($like_or_unlike);

      	  $sql_present="SELECT COUNT(*) as total FROM {$this->db->dbprefix}user_word_{$like_or_unlike} WHERE i_word_id='{$like_unlike_information_array['i_word_id']}' 
			 AND i_{$like_or_unlike}d_user_id='{$like_unlike_information_array["i_{$like_or_unlike}d_user_id"]}'
			"; 
            $res = $this->db->query($sql_present);
            $result = $res->row_array();
            $is_present = $result['total'];


       if($like_or_unlike=="like"){

         
      
           if( $is_present == 0){
			//$this->db->insert($this->db->dbprefix."user_media_like",$like_unlike_information_array);
			//echo $this->db->last_query();exit;   
			  
			   
            if($this->db->insert($this->db->dbprefix."user_word_like",$like_unlike_information_array))
            {
				
               $sql_del ="DELETE FROM {$this->db->dbprefix}user_word_unlike WHERE i_word_id='{$like_unlike_information_array['i_word_id']}' AND i_unliked_user_id='{$like_unlike_information_array['i_liked_user_id']}'";
                $this->db->query($sql_del);
                $response['last_inserted_id'] = $this->db->insert_id();
                $response['value'] = true ;
                $response['message'] = "Succes" ;
            }
           }
     
        
       }
	   else if($like_or_unlike=="unlike")
        {

        if($is_present==0)
            {
            if($this->db->insert($this->db->dbprefix."user_word_unlike",$like_unlike_information_array))
                {

                    $sql_del ="DELETE FROM {$this->db->dbprefix}user_word_like WHERE i_word_id='{$like_unlike_information_array['i_word_id']}' AND i_liked_user_id='{$like_unlike_information_array['i_unliked_user_id']}'";
                    $this->db->query($sql_del);
                    $response['last_inserted_id'] = $this->db->insert_id();
                    $response['value'] = true ;
                    $response['message'] = "Success" ;
                }
            }

        }
//pr($response);
        return $response;
  }
  //---------------------------------word comment
  public function insert($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert('user_word_comments', $arr);
		//echo $this->db->last_query();
		return $this->db->insert_id();
	}
	
//-----------------------------word comment fetch
public function get_by_newsfeed_id($i_word_id,  $i_start_limit="", $i_no_of_page="") { 
		
        #echo $i_word_id;
		if("$i_start_limit" == "") {
			$sql = sprintf("SELECT c.id, 
								   c.i_word_id,
								   c.i_user_id, 
								   c.s_contents, 
								   c.dt_created_on, 
								   CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
								   u.s_profile_photo, 
								   u.s_first_name as pseudo ,
                                   u.e_gender
						FROM %1\$suser_word_comments c, %1\$susers u 
						WHERE c.i_user_id=u.id 
						    AND c.i_word_id =  %2\$s 
							
						   ORDER BY c.dt_created_on DESC", 
						   $this->db->dbprefix, 
						   intval($i_word_id));
		}
		else {
			$sql = sprintf("SELECT c.id, 
								   c.i_word_id,
								   c.i_user_id, 
								   c.s_contents, 
								   c.dt_created_on, 
								   CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
								   u.s_profile_photo, 
								  
								   u.s_first_name as pseudo 
					    FROM %1\$suser_word_comments c, %1\$susers u 
						WHERE c.i_user_id=u.id
						 AND c.i_word_id = %2\$s 
						
						 ORDER BY dt_created_on DESC LIMIT %3\$s, %4\$s", 
						 $this->db->dbprefix,
						 intval($i_word_id), 
						 intval($i_start_limit), 
						 intval($i_no_of_page));
		}

        #echo $sql;
		$query = $this->db->query($sql); 
    //echo $this->db->last_query();
		$result_arr = $query->result_array();

		return $result_arr;
	}
    //------------------------------ front end ----------------------------
    public function latest_word_for_today()
    {
        $sql = "SELECT * FROM {$this->db->WORD_FOR_TODAY} ORDER BY id DESC";
        $res = $this->db->query($sql)->result_array();
        return $res[0];
    }
    
    
    
    
    
    public function get_news_info_by_news_id($id)
    {
        $sql = "SELECT * FROM {$this->db->CHRISTIAN_NEWS} WHERE id = {$id}";
        $res = $this->db->query($sql)->result_array();
        
        foreach($res as $key=>$val)
        {
            $res[$key]['total_comments'] = $this->get_total_comment_by_news_id($id);
            $res[$key]['total_likes'] = $this->get_total_people_liked_by_news_id($id);
        }
        
        return $res[0];
    }
    
    public function get_category_name_by_category_id($id)
    {
        $sql = "SELECT * FROM {$this->db->CHRISTIAN_NEWS_CAT} WHERE id={$id}";
        $res = $this->db->query($sql)->result_array();
        return $res[0]['s_cat_name'];
    }
    
    public function get_category_id_by_category_name($cat_name)
    {
        $sql = "SELECT * FROM {$this->db->CHRISTIAN_NEWS_CAT} WHERE `s_cat_name`={$cat_name}";
        $res = $this->db->query($sql)->result_array();
        return $res[0]['id'];
    }
    public function update_view_count_by_news_id($id)
    {
        $sql = "UPDATE {$this->db->CHRISTIAN_NEWS} SET `i_view_count`=`i_view_count`+1 WHERE id={$id}";
        $this->db->query($sql);
        
        /*$sql = "SELECT `i_view_count` FROM {$this->db->CHRISTIAN_NEWS} WHERE id = {$id}";
        $res = $this->db->query($sql)->result_array();
        $prev_count=$res[0]['i_view_count'];
        $now_count = $prev_count+1;
        $this->db->update($this->db->CHRISTIAN_NEWS,array('i_view_count'=>$now_count),array('id'=>$id));
        */
    }
    
    //-------------- posting ------------------------
    public function whether_prev_liked($news_id,$user_id)
    {
        $sql="SELECT * FROM {$this->db->CHRISTIAN_NEWS_LIKE} WHERE i_news_id={$news_id} AND i_liked_user_id={$user_id}";
        $res = $this->db->query($sql)->result_array();
        return $res;
    }
    
    public function like_news($info)
    {
        $this->db->insert($this->db->CHRISTIAN_NEWS_LIKE,$info);
        $last_id = $this->db->insert_id();
        return $last_id;
    }
    public function comment_news($info)
    {
        $this->db->insert($this->db->CHRISTIAN_NEWS_COMMENTS,$info);
        $last_id = $this->db->insert_id();
        return $last_id;
    }
    //-------------------- / posting ------------------------
    
    public function get_comment_by_news_id($news_id, $start_limit, $end_limit)
    {
        $limit  = (is_numeric($start_limit) && is_numeric($end_limit))?" Limit ".intval($start_limit).",".intval($end_limit):'';
                $sql = ("SELECT c.id, 
                                  
                                   c.i_posted_user_id, 
                                   c.s_contents, 
                                   c.dt_commented_on, 
                                   CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
                                   u.s_profile_photo, 
                                  
                                   u.s_first_name as pseudo 
                        FROM {$this->db->CHRISTIAN_NEWS_COMMENTS} c, {$this->db->USERS} u 
                        WHERE c.i_posted_user_id=u.id
                         AND c.i_news_id = {$news_id} 
                        
                         ORDER BY dt_commented_on DESC {$limit}");
        $res = $this->db->query($sql)->result_array();
        return $res;
        
    }
    
    public function get_total_comment_by_news_id($news_id)
    {
        $sql = "SELECT COUNT(*) total FROM {$this->db->CHRISTIAN_NEWS_COMMENTS} WHERE i_news_id={$news_id}"; 
        $res = $this->db->query($sql)->result_array();
        return $res[0]['total'];
    }
    
    
    
    public function get_people_liked_by_news_id($news_id,$start_limit, $end_limit)
    {
        $limit  = (is_numeric($start_limit) && is_numeric($end_limit))?" Limit ".intval($start_limit).",".intval($end_limit):'';
        $sql = ("SELECT c.id, 
                        c.i_liked_user_id, 
                        c.dt_liked_on, 
                        CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
                        u.s_profile_photo,u.e_gender, 
                        u.s_first_name as pseudo,
                        mst_c.s_country_name
                        
                        FROM {$this->db->CHRISTIAN_NEWS_LIKE} c, {$this->db->USERS} u , {$this->db->MST_COUNTRY} mst_c
                        WHERE c.i_liked_user_id=u.id
                        AND u.i_country_id=mst_c.id
                        AND c.i_news_id={$news_id}
                       
                         ORDER BY dt_liked_on DESC {$limit}");
        $res = $this->db->query($sql)->result_array();

        return $res;
        
    }
    
    public function get_total_people_liked_by_news_id($news_id)
    {
        $sql = ("SELECT count(*) total FROM {$this->db->CHRISTIAN_NEWS_LIKE} c, {$this->db->USERS} u WHERE c.i_liked_user_id=u.id AND c.i_news_id = {$news_id} order by c.dt_liked_on DESC");
        $res = $this->db->query($sql)->result_array();
        return $res[0]['total'];
    }
    
    
    
    //------------------------------ end front end ----------------------------
    
    
    
    
    //------------------------------------- videos -------------------------------
	
	public function get_all_video_cat()
    {
        //$sql = "SELECT * FROM {$this->db->MC_VIDEO_CAT}";
		
		$sql = "SELECT TAB1.*, (SELECT COUNT(*) FROM {$this->db->MC_VIDEOS} WHERE i_category_id = TAB1.id) AS CAT_COUNT
					FROM {$this->db->MC_VIDEO_CAT} AS TAB1";
		
        $res = $this->db->query($sql)->result_array();
        return $res;
    }
	
	public function get_total_video_cat()
    {
        $sql = "SELECT COUNT(*) total_videos FROM {$this->db->MC_VIDEO_CAT} v ";
        $res = $this->db->query($sql)->result_array();
        
        return $res[0]['total_videos'];
    }
	
	
    public function get_all_videos($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY id DESC';
        
        $sql = "SELECT v.*, c.s_name as cat_name, concat(a.s_name,' ',a.s_last_name) admin_name
                FROM {$this->db->MC_VIDEOS} v 
                LEFT JOIN {$this->db->MC_VIDEO_CAT} c on v.i_category_id=c.id 
                LEFT JOIN {$this->db->ADMIN_USER} a on v.i_posted_by=a.id {$where} {$s_order_by} {$limit}
                ";
        $res = $this->db->query($sql)->result_array();
		foreach($res as $key=>$val)
        {
            $res[$key]['total_comments'] = $this->get_total_comment_by_video_id($val['id']);
            $res[$key]['total_likes'] = $this->get_total_people_liked_by_video_id($val['id']);
        }
        return $res;
        
    }


    
    public function get_total_videos($where='')
    {
        $sql = "SELECT COUNT(*) total_videos FROM {$this->db->MC_VIDEOS} v
                LEFT JOIN {$this->db->ADMIN_USER} a on v.i_posted_by=a.id {$where}  ";
        $res = $this->db->query($sql)->result_array();
        
        return $res[0]['total_videos'];
    }
    
    
    public function delete_video_by_id($id)
    {
        $sql = "DELETE FROM {$this->db->MC_VIDEOS} WHERE id={$id}";
        $this->db->query($sql);
    }
    
    //----------------------------------- add video -------------------------------
    public function fetch_all_categories()
    {
        $sql = "SELECT * FROM {$this->db->MC_VIDEO_CAT}";
        $res = $this->db->query($sql)->result_array();
        return $res;
    }
    
     public function add_new_video($info)
    {
        $res = $this->db->insert($this->db->MC_VIDEOS,$info);
        
        $last_id = $this->db->insert_id();
        return $last_id;
    }
    
    //----------------------------------- end add video -------------------------------
    
    //------------------------------------ edit video ------------------------------
    public function get_contents_by_video_id($id)
    {
        $sql = "SELECT * FROM {$this->db->MC_VIDEOS} WHERE id={$id}";
        $res = $this->db->query($sql)->result_array();
        return $res[0];
    }
    
    public function get_video_thumb_by_id($id)
    {
        $sql = "SELECT s_video_thumb FROM {$this->db->MC_VIDEOS} WHERE id={$id}";
        $res = $this->db->query($sql)->result_array();
        return $res[0]['s_video_thumb'];
    }
    public function update_video($info,$id)
    {
        $res = $this->db->update($this->db->MC_VIDEOS,$info,array('id'=>$id));
    }
	
	public function update_video_all($id)
    {
        $sql = "UPDATE {$this->db->MC_VIDEOS} SET `i_is_feature_home`=0 WHERE id!={$id}";
        $this->db->query($sql);
    }
	
    //------------------------------------ end edit video ------------------------------
	
	
	//-------------- posting ------------------------
    public function whether_video_prev_liked($news_id,$user_id)
    {
        $sql="SELECT * FROM {$this->db->mc_video_like} WHERE i_news_id={$news_id} AND i_liked_user_id={$user_id}";
        $res = $this->db->query($sql)->result_array();
        return $res;
    }
    
    public function like_videos($info)
    {
        $this->db->insert($this->db->mc_video_like,$info);
        $last_id = $this->db->insert_id();
        return $last_id;
    }
    public function comment_videos($info)
    {
        $this->db->insert($this->db->mc_video_comments,$info);
        $last_id = $this->db->insert_id();
        return $last_id;
    }
    //-------------------- / posting ------------------------
    
    public function get_comment_by_video_id($news_id, $start_limit, $end_limit)
    {
        $limit  = (is_numeric($start_limit) && is_numeric($end_limit))?" Limit ".intval($start_limit).",".intval($end_limit):'';
                $sql = ("SELECT c.id, 
                                  
                                   c.i_posted_user_id, 
                                   c.s_contents, 
                                   c.dt_commented_on, 
                                   CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
                                   u.s_profile_photo, 
                                  
                                   u.s_first_name as pseudo 
                        FROM {$this->db->mc_video_comments} c, {$this->db->USERS} u 
                        WHERE c.i_posted_user_id=u.id
                         AND c.i_news_id = {$news_id} 
                        
                         ORDER BY dt_commented_on DESC {$limit}");
        $res = $this->db->query($sql)->result_array();
        return $res;
        
    }
    
    public function get_total_comment_by_video_id($news_id)
    {
        $sql = "SELECT COUNT(*) total FROM {$this->db->mc_video_comments} WHERE i_news_id={$news_id}"; 
        $res = $this->db->query($sql)->result_array();
        return $res[0]['total'];
    }
    
	public function get_total_comment_by_audio_id($news_id)
    {
        $sql = "SELECT COUNT(*) total FROM {$this->db->mc_audio_comments} WHERE i_news_id={$news_id}"; 
        $res = $this->db->query($sql)->result_array();
        return $res[0]['total'];
    }
    
    
    public function get_people_liked_by_video_id($news_id,$start_limit, $end_limit)
    {
        $limit  = (is_numeric($start_limit) && is_numeric($end_limit))?" Limit ".intval($start_limit).",".intval($end_limit):'';
        $sql = ("SELECT c.id, 
                        c.i_liked_user_id, 
                        c.dt_liked_on, 
                        CONCAT(u.s_first_name,' ', u.s_last_name) s_profile_name,
                        u.s_profile_photo, 
                        u.s_first_name as pseudo,
                        mst_c.s_country_name
                        
                        FROM {$this->db->mc_video_like} c, {$this->db->USERS} u , {$this->db->MST_COUNTRY} mst_c
                        WHERE c.i_liked_user_id=u.id
                        AND u.i_country_id=mst_c.id
                        AND c.i_news_id={$news_id}
                       
                         ORDER BY dt_liked_on DESC {$limit}");
        $res = $this->db->query($sql)->result_array();

        return $res;
        
    }
    
    public function get_total_people_liked_by_video_id($news_id)
    {
        $sql = ("SELECT count(*) total FROM {$this->db->mc_video_like} c, {$this->db->USERS} u WHERE c.i_liked_user_id=u.id AND c.i_news_id = {$news_id} order by c.dt_liked_on DESC");
        $res = $this->db->query($sql)->result_array();
        return $res[0]['total'];
    }
    
    
    
    
    //------------------------------------- end videos -------------------------------
    
    

    //------------------------------------audio----------------------------------------

    public function get_all_audio_cat()
    {
        //$sql = "SELECT * FROM {$this->db->MC_AUDIO_CAT}";
		
		$sql = "SELECT TAB1.*, (SELECT COUNT(*) FROM {$this->db->MC_AUDIO} WHERE i_category_id = TAB1.id) AS CAT_COUNT
					FROM {$this->db->MC_AUDIO_CAT} AS TAB1";
		
        $res = $this->db->query($sql)->result_array();
        return $res;
    }
    
    
    public function get_all_audios($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY id DESC';
        
         $sql = "SELECT v.*, c.s_name as cat_name, concat(a.s_name,' ',a.s_last_name) admin_name
                FROM {$this->db->MC_AUDIO} v 
                LEFT JOIN {$this->db->MC_AUDIO_CAT} c on v.i_category_id=c.id 
                LEFT JOIN {$this->db->ADMIN_USER} a on v.i_posted_by=a.id {$where} {$s_order_by} {$limit}
                ";
        $res = $this->db->query($sql)->result_array();
        foreach($res as $key=>$val)
        {
            $res[$key]['total_comments'] = $this->get_total_comment_by_video_id($val['id']);
            $res[$key]['total_likes'] = $this->get_total_people_liked_by_video_id($val['id']);
        }
        return $res;
        
    }




    //----------------------------------- add audio -------------------------------
    public function fetch_all_audio_categories()
    {
        $sql = "SELECT * FROM {$this->db->MC_AUDIO_CAT}";
        $res = $this->db->query($sql)->result_array();
        return $res;
    }
	
	public function get_total_audio_cat()
    {
        $sql = "SELECT COUNT(*) total_videos FROM {$this->db->MC_AUDIO_CAT} v ";
        $res = $this->db->query($sql)->result_array();
        
        return $res[0]['total_videos'];
    }
    
     public function add_new_audio($info)
    {
        $res = $this->db->insert($this->db->MC_AUDIO,$info);
        
        $last_id = $this->db->insert_id();
        return $last_id;
    }
    
    //----------------------------------- end add video -------------------------------


//------------------------------------ edit audio ------------------------------
    public function get_contents_by_audio_id($id)
    {
        $sql = "SELECT * FROM {$this->db->MC_AUDIO} WHERE id={$id}";
        $res = $this->db->query($sql)->result_array();
        return $res[0];
    }
    
    public function get_audio_thumb_by_id($id)
    {
        $sql = "SELECT s_video_thumb FROM {$this->db->MC_AUDIO} WHERE id={$id}";
        $res = $this->db->query($sql)->result_array();
        return $res[0]['s_video_thumb'];
    }
    public function update_audio($info,$id)
    {
        $res = $this->db->update($this->db->MC_AUDIO,$info,array('id'=>$id));
    }
    //------------------------------------ end edit audio ------------------------------

    
	public function update_audio_all($id)
    {
        $sql = "UPDATE {$this->db->MC_AUDIO} SET `i_is_feature_home`=0 WHERE id!={$id}";
        $this->db->query($sql);
    }
	
    
    public function get_total_audios($where='')
    {
        $sql = "SELECT COUNT(*) total_videos FROM {$this->db->MC_AUDIO} v
                LEFT JOIN {$this->db->ADMIN_USER} a on v.i_posted_by=a.id {$where}  ";
        $res = $this->db->query($sql)->result_array();
        
        return $res[0]['total_videos'];
    }
    
    
    public function delete_audio_by_id($id)
    {
        $sql = "DELETE FROM {$this->db->MC_AUDIO} WHERE id={$id}";
        $this->db->query($sql);
    }
    


    
    
    //------------------------------------- christian news ------------------------------------------------
    public function get_all_news_cat()
    {
        $sql = "SELECT TAB1.*, (SELECT COUNT(*) FROM {$this->db->CHRISTIAN_NEWS} WHERE i_category = TAB1.id) AS CAT_COUNT
					FROM {$this->db->CHRISTIAN_NEWS_CAT} AS TAB1 
					ORDER BY order_by";
					
        $res = $this->db->query($sql)->result_array();
        return $res;
    }
	
	public function get_total_news_cat()
    {
        $sql = "SELECT COUNT(*) total_news FROM {$this->db->CHRISTIAN_NEWS_CAT} n ";
        $res = $this->db->query($sql)->result_array();
        return $res[0]['total_news'];
    }
    
    public function get_all_news($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by.',n.id DESC' :'ORDER BY n.id DESC';
        
        $sql = "SELECT n.*, concat(a.s_name,' ',a.s_last_name) admin_name, c.s_cat_name as cat_name
                FROM {$this->db->CHRISTIAN_NEWS} n
                LEFT JOIN {$this->db->CHRISTIAN_NEWS_CAT} c on c.id=n.i_category
                LEFT JOIN {$this->db->ADMIN_USER} a on a.id=n.i_posted_by
                {$where} {$s_order_by} {$limit}";
        $res = $this->db->query($sql)->result_array();
        
        foreach($res as $key=>$val)
        {
            $res[$key]['total_comments'] = $this->get_total_comment_by_news_id($val['id']);
            $res[$key]['total_likes'] = $this->get_total_people_liked_by_news_id($val['id']);
        }
       
        
        return $res;
    }
	
	
	
	 
	public function get_search_result_from_media_center($where_news='',$where_audio='',$where_video='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by.',n.id DESC' :'ORDER BY n.id DESC';
        
        /*$sql = "SELECT n.*, concat(a.s_name,' ',a.s_last_name) admin_name, c.s_cat_name as cat_name
                FROM {$this->db->CHRISTIAN_NEWS} n
                LEFT JOIN {$this->db->CHRISTIAN_NEWS_CAT} c on c.id=n.i_category
                LEFT JOIN {$this->db->ADMIN_USER} a on a.id=n.i_posted_by
                {$where} {$s_order_by} {$limit}";*/
				
		$sql = "
					SELECT TAB1.* FROM (
		(SELECT n.id,n.`s_title`,n.`s_desc`,nc.`s_cat_name`,n.`i_view_count`,n.`i_posted_by`,n.`dt_posted_on`,1 as type FROM cg_christian_news as n left join cg_christian_news_cat as nc ON nc.id=n.i_category
				{$where_news}) 
				UNION
				(SELECT a.id,a.`s_title`,a.`s_desc`,ac.`s_name`,a.`i_view_count`,a.`i_posted_by`,a.`dt_posted_on`,2 as type FROM cg_mc_audio as a left join cg_mc_audio_cat as ac ON ac.id=a.i_category_id
				{$where_audio}) 
				UNION
				(SELECT v.id,v.`s_title`,v.`s_desc`,vc.`s_name`,v.`i_view_count`,v.`i_posted_by`,v.`dt_posted_on`,3 as type FROM cg_mc_videos as v left join cg_mc_video_cat as vc ON vc.id=v.i_category_id
				{$where_video}) 
				) AS TAB1 ORDER BY `TAB1`.`id`  DESC
				";
		
				
        $res = $this->db->query($sql)->result_array();
        
		//print_r($res);
		
        foreach($res as $key=>$val)
        {
			if ($val['type'] == 1)
			{
				$res[$key]['total_comments'] = $this->get_total_comment_by_news_id($val['id']);
				$res[$key]['total_likes'] = $this->get_total_people_liked_by_news_id($val['id']);
			}
			else if ($val['type'] == 2)
			{
				$res[$key]['total_comments'] = $this->get_total_comment_by_audio_id($val['id']);
				//$res[$key]['total_likes'] = $this->get_total_people_liked_by_audio_id($val['id']);
			}
			else if ($val['type'] == 3)
			{
				$res[$key]['total_comments'] = $this->get_total_comment_by_video_id($val['id']);
				//$res[$key]['total_likes'] = $this->get_total_people_liked_by_video_id($val['id']);
			}
			
        }
       
        
        return $res;
    }
	
	
    
    public function get_total_news($where)
    {
		$sql="SELECT COUNT(*) total_news FROM {$this->db->CHRISTIAN_NEWS} n
                LEFT JOIN {$this->db->ADMIN_USER} a on n.i_posted_by=a.id {$where} ";
        //$sql = "SELECT COUNT(*) total_news FROM {$this->db->CHRISTIAN_NEWS} n {$where}";
        $res = $this->db->query($sql)->result_array();
        return $res[0]['total_news'];
    }
    
    
    public function add_new_news($info)
    {
        //pr($info,1);
        $res = $this->db->insert($this->db->CHRISTIAN_NEWS,$info);
		$this->db->last_query();
        $last_id = $this->db->insert_id();
        return $last_id;
    }
    
    
    
    
    public function delete_news_by_news_id($id)
    {
        $sql = "DELETE FROM {$this->db->CHRISTIAN_NEWS} WHERE id={$id}";
        $this->db->query($sql);
    }
    
    
    public function remove_from_news_like_list($id)
    {
        $sql = "DELETE FROM {$this->db->CHRISTIAN_NEWS_LIKE} WHERE id={$id}";
        $this->db->query($sql);
    }
    
    public function remove_from_news_comment_list($id)
    {
        $sql = "DELETE FROM {$this->db->CHRISTIAN_NEWS_COMMENTS} WHERE id={$id}";
        $this->db->query($sql);
    }
    
    
    //----------------------- edit news -----------------
    public function fetch_news_by_id($id)
    {
        $sql = "SELECT * FROM {$this->db->CHRISTIAN_NEWS} WHERE id={$id}";
        $res = $this->db->query($sql)->result_array();
        return $res[0];
    }
    public function update_news_by_id($info,$id)
    {
        $this->db->update($this->db->CHRISTIAN_NEWS,$info,array('id'=>$id));
        
    }
	
	public function fetch_news_cat_by_id($id)
    {
        $sql = "SELECT * FROM {$this->db->CHRISTIAN_NEWS_CAT} WHERE id={$id}";
        $res = $this->db->query($sql)->result_array();
        return $res[0];
    }
	public function update_news_cat_by_id($info,$id)
    {
        $this->db->update($this->db->CHRISTIAN_NEWS_CAT,$info,array('id'=>$id));
	}
	
	public function fetch_video_cat_by_id($id)
    {
        $sql = "SELECT * FROM {$this->db->MC_VIDEO_CAT} WHERE id={$id}";
        $res = $this->db->query($sql)->result_array();
        return $res[0];
    }
	public function update_video_cat_by_id($info,$id)
    {
        $this->db->update($this->db->MC_VIDEO_CAT,$info,array('id'=>$id));
	}
	
	public function fetch_audio_cat_by_id($id)
    {
        $sql = "SELECT * FROM {$this->db->MC_AUDIO_CAT} WHERE id={$id}";
        $res = $this->db->query($sql)->result_array();
        return $res[0];
    }
	public function update_audio_cat_by_id($info,$id)
    {
        $this->db->update($this->db->MC_AUDIO_CAT,$info,array('id'=>$id));
	}
    
    //----------------------- end edit news -----------------
    
    
	//----------------------- edit all news -----------------
	public function update_news_all($id)
    {
        $sql = "UPDATE {$this->db->CHRISTIAN_NEWS} SET `i_is_feature_home`=0 WHERE id!={$id}";
        $this->db->query($sql);
    }
	
	//----------------------- end edit all news -----------------
	
    
    //------------------------------------- end christian news ------------------------------------------------
    
   
    function get_rss_feed($url)
	{
		try{
				$this->load->library('rssparser');
				
				
				$rss=array();
				$this->rssparser->set_feed_url($url);  // get feed
				$this->rssparser->set_cache_life(30);                       // Set cache life time in minutes
				$rss = $this->rssparser->getFeed(15);
				
				$feeds=array();
				foreach($rss as $key=>$val)
				{
					$feeds[$key]['s_title'] 		= $val['title'];
					$feeds[$key]['s_desc'] 		= $val['description'];
					$feeds[$key]['s_image']		= ($val['image'])?$val['image']['url']:'';
					$feeds[$key]['dt_posted_on']	= get_db_datetime();
					$feeds[$key]['is_rss']		= 1;
					$feeds[$key]['feed_news_link']		= $val['link'];
					$feeds[$key]['s_feed_url']=$url;
				}
				return $feeds;
			}
			catch (Exception $err_obj) {
				
			}
		}
    
    function get_suggestion_video($id)
	{
	try
	{
		$sql=$this->db->query("select c.*,cats.s_name as cat_name from cg_mc_videos c left JOIN cg_mc_video_like l on c.id=l.i_news_id left JOIN cg_mc_video_cat cats on c.i_category_id=cats.id where c.id!=".$id." order by rand() limit 7");
		//echo $this->db->last_query();
		$res=$sql->result_array();
		foreach($res as $key=>$val)
		{
			$sql1=$this->db->query("select count(id) as total_likes from cg_mc_video_like where i_news_id=".$val["id"]);
			$res1=$sql1->result_array();
			$res[$key]["total_likes"]=$res1[0]["total_likes"];
		}
		return $res;
	}
	catch (Exception $err_obj)
	{
		
	}
	}
	
	function get_suggestion_news($id)
	{
	try
	{
		$sql=$this->db->query("select c.*,cats.s_cat_name as cat_name from cg_christian_news c left JOIN cg_christian_news_cat cats on c.i_category=cats.id where c.i_category!=".$id." order by rand() limit 7");
		//echo $this->db->last_query();
		$res=$sql->result_array();
		foreach($res as $key=>$val)
		{
			$sql1=$this->db->query("select count(id) as total_likes from cg_christian_news_like where i_news_id=".$val["id"]);
			$res1=$sql1->result_array();
			$res[$key]["total_likes"]=$res1[0]["total_likes"];
		}
		return $res;
	}
	catch (Exception $err_obj)
	{
		
	}
	}
    //================================== end media center ==============================================
	
}
