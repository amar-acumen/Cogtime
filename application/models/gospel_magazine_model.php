<?php
include_once(APPPATH.'models/base_model.php');
class Gospel_magazine_model extends Base_model
{
    
    public function __construct() 
    {
        parent::__construct();
    }
    
	
    
    //----- admin perpose --------------
    public function get_magazines($s_where=null, $start_limit=null,$no_of_page=null, $timeout='',$s_order_by=null )
    {
        if("$start_limit" == '') {
            $limit = '';
        }
        else {
            $start_limit = (int) $start_limit;
            $no_of_page = (int) $no_of_page;
            $limit = ' limit '.$start_limit.', '.$no_of_page;
        }
        
        
        $ORDERBY = trim($s_order_by)!=""?" ORDER BY ".$s_order_by."":"ORDER BY m.id DESC";
        
        $sql = "SELECT m.*  ,
				(SELECT COUNT(*) FROM {$this->db->GOSPEL_MAGAZINE_CMT} WHERE i_magazine_id=m.id) AS total_comments,
				(SELECT COUNT(*) FROM {$this->db->GOSPEL_MAGAZINE_LIKE} WHERE i_magazine_id=m.id) AS total_likes
                FROM {$this->db->GOSPEL_MAGAZINE} m
                LEFT JOIN {$this->db->ADMIN_USER} a ON a.id=m.i_posted_by
                WHERE 1 {$s_where} {$ORDERBY} {$limit}
                ";
        $res = $this->db->query($sql)->result_array();
        return $res;
    }
    public function get_count_magazines($s_where)
    {
        $sql = "SELECT COUNT(*) magazines
                FROM {$this->db->GOSPEL_MAGAZINE} m
                WHERE 1 {$s_where}
                ";
        $res = $this->db->query($sql)->result_array();
        return $res[0]['magazines'];
    }
	    
    
    //----------------- add new article ----------------------
    public function add_new_article($info)
    {
        $this->db->insert($this->db->GOSPEL_MAGAZINE,$info);
        $res = $this->db->insert_id();
        return $res;
    }
    //----------------- end add new article ----------------------
    
    //------------------ edit article ------------------
    public function fetch_article_info_by_article_id($id)
    {
        $sql = "SELECT m.*
                FROM {$this->db->GOSPEL_MAGAZINE} m
                WHERE id = {$id}
                ";
        $res = $this->db->query($sql)->result_array();
        return $res[0];
    }
    
    public function edit_article($info,$id)
    {
        $this->db->update($this->db->GOSPEL_MAGAZINE,$info,array('id'=>$id));
    }
    //------------------ end edit article ------------------
    
    //-------------------- delete article --------------
    public function delete_article_by_article_id($id)
    {
        $sql = "DELETE FROM {$this->db->GOSPEL_MAGAZINE} WHERE id={$id}";
        $this->db->query($sql);
    }
	public function delete_likes_by_project_id($id)
    {
        $sql = "DELETE FROM {$this->db->GOSPEL_MAGAZINE_LIKE} WHERE i_magazine_id={$id}";
        $this->db->query($sql);
    }
	public function delete_comments_by_project_id($id)
    {
        $sql = "DELETE FROM {$this->db->GOSPEL_MAGAZINE_CMT} WHERE i_magazine_id={$id}";
        $this->db->query($sql);
    }
    //-------------------- end delete article --------------
    
	 //----------------- add new project ----------------------
    public function add_new_project($info)
    {
        $this->db->insert($this->db->CHRISTAN_PROJECT,$info);
        
        $res = $this->db->insert_id();
        return $res;
    }
    //----------------- end add new project ----------------------
	//----------------- edit new project ----------------------
	public function edit_project($info,$id)
    {
        $this->db->update($this->db->CHRISTAN_PROJECT,$info,array('id'=>$id));
		
    }
	//----------------- edit new project ----------------------
    //----- end admin perpose --------------
    
    
    
    public function get_intercession_testimony($s_where=null, $start_limit=null,$no_of_page=null, $timeout='',$s_order_by=null )
    {
        if("$start_limit" == '') {
            $limit = '';
        }
        else {
            $start_limit = (int) $start_limit;
            $no_of_page = (int) $no_of_page;
            $limit = ' limit '.$start_limit.', '.$no_of_page;
        }
        
        
        $ORDERBY = trim($s_order_by)!=""?" ORDER BY ".$s_order_by."":"ORDER BY testimony_id DESC";
        
        $sql = "SELECT t.id testimony_id,
                            i.id prayer_wall_req_id,
                            t.s_message testimony_desc,
                            t.dt_insert_time testimony_insert_date,
                            mst_c.s_country_name country,
                            i.s_state state,
                            i.s_description intercession_desc,
                            i.dt_start_date intercession_start_date,
                            i.dt_end_date intercession_end_date,
                            i.dt_insert_date intercession_insert_date,
                            CONCAT(a.s_name,' ',a.s_last_name) posted_by
                FROM {$this->db->BIBLE_INTERCESSION_WALL_POST_TESTIMONY} t
                LEFT JOIN {$this->db->BIBLE_INTERCESSION} i ON t.i_id_intercession_wall_post = i.id
                LEFT JOIN {$this->db->BIBLE_INTERCESSION_COMMITMENTS} c on c.i_id_intercession_wall_post=i.id
                LEFT JOIN {$this->db->ADMIN_USER} a ON a.id = i.i_user_id
                LEFT JOIN {$this->db->MST_COUNTRY} mst_c on mst_c.id=i.i_country_id
                WHERE i.i_is_enable=1
                {$s_where} GROUP BY testimony_id {$ORDERBY} {$limit}
                 ";
                
        $res = $this->db->query($sql)->result_array();
        return $res;
        
    }
    public function get_total_intercession_testimony($s_where=null)
    {
         $sql = "SELECT count(*) as count FROM( SELECT t.id
                    
                    FROM {$this->db->BIBLE_INTERCESSION_WALL_POST_TESTIMONY} t
                    LEFT JOIN {$this->db->BIBLE_INTERCESSION} i ON t.i_id_intercession_wall_post = i.id
                    LEFT JOIN {$this->db->BIBLE_INTERCESSION_COMMITMENTS} c on c.i_id_intercession_wall_post=i.id
                    LEFT JOIN {$this->db->ADMIN_USER} a ON a.id = i.i_user_id
                    LEFT JOIN {$this->db->MST_COUNTRY} mst_c on mst_c.id=i.i_country_id
                    WHERE i.i_is_enable=1
                    {$s_where} GROUP BY t.id) as derived_tbl ";
        $res = $this->db->query($sql)->result_array();
        return $res[0]['count'];
    }
        
    //------------- testimony FO end -------------------------
	
	
	
	 public function get_ch_project($s_where=null, $start_limit=null,$no_of_page=null, $timeout='',$s_order_by=null )
    {
        if("$start_limit" == '') {
            $limit = '';
        }
        else {
            $start_limit = (int) $start_limit;
            $no_of_page = (int) $no_of_page;
            $limit = ' limit '.$start_limit.', '.$no_of_page;
        }
        
        
        $ORDERBY = trim($s_order_by)!=""?" ORDER BY ".$s_order_by."":"ORDER BY m.id DESC";
        
        $sql = "SELECT m.*,
				(SELECT COUNT(*) FROM {$this->db->CHRISTAN_PROJECT_CMT} WHERE i_proj_id=m.id) AS total_comments,
				(SELECT COUNT(*) FROM {$this->db->CHRISTAN_PROJECT_LIKE} WHERE i_proj_id=m.id) AS total_likes
                FROM {$this->db->CHRISTAN_PROJECT} m 
                WHERE 1 {$s_where} {$ORDERBY} {$limit}
                ";
        $res = $this->db->query($sql)->result_array();
        return $res;
    }
    public function get_count_ch_project($s_where)
    {
        $sql = "SELECT COUNT(*) magazines
                FROM {$this->db->CHRISTAN_PROJECT} m
                WHERE 1 {$s_where}
                ";
        $res = $this->db->query($sql)->result_array();
        return $res[0]['magazines'];
    }
	
	/*************magazine comments****************/
	public function get_gospel_cmnts($s_where)
    {
        $sql = "SELECT m.* , CONCAT(u.s_first_name,' ',u.s_last_name) s_profile_name , u.id AS post_owner_user_id
                FROM {$this->db->GOSPEL_MAGAZINE_CMT} m , {$this->db->USERS} u
                WHERE 1 AND u.id=m.i_user_id {$s_where}
                ";
        $res = $this->db->query($sql)->result_array();
        return $res;
    }
	public function add_cmnts($info)
    {
        $this->db->insert($this->db->GOSPEL_MAGAZINE_CMT,$info);
        
        $res = $this->db->insert_id();
        return $res;
    }
	public function get_count_gospel_cmnts($s_where)
    {
		$sql = "SELECT COUNT(*) cmts
                FROM {$this->db->GOSPEL_MAGAZINE_CMT} m
                WHERE 1 {$s_where}
                ";
        $res = $this->db->query($sql)->result_array();
        return $res[0]['cmts'];
    }
	/*************magazine comments****************/
	
	/*************magazine like****************/
	public function add_likes($info)
    {
		$s_where	= " AND i_user_id='".intval(decrypt($this->session->userdata('user_id')))."' AND i_magazine_id='".$info['i_magazine_id']."'";
		$count	= $this->get_count_gospel_likes($s_where);
		if($count>0)
			return false;
		else
        {
			$this->db->insert($this->db->GOSPEL_MAGAZINE_LIKE,$info);
       		$res = $this->db->insert_id();
        	return $res;
		}
    }
	public function get_count_gospel_likes($s_where)
    {
		$sql = "SELECT COUNT(*) AS lik
                FROM {$this->db->GOSPEL_MAGAZINE_LIKE} m
                WHERE 1 {$s_where}";
				
        $res = $this->db->query($sql)->result_array();
        return $res[0]['lik'];
    }
	public function get_gospel_likes($s_where)
    {
		$sql = "SELECT m.* , CONCAT(u.s_first_name,' ',u.s_last_name) s_profile_name , u.id AS post_owner_user_id, u.s_profile_photo
                FROM {$this->db->GOSPEL_MAGAZINE_LIKE} m , {$this->db->USERS} u
                WHERE 1 AND u.id=m.i_user_id {$s_where}";
				
        $res = $this->db->query($sql)->result_array();
        return $res;
    }
	/*************magazine like****************/
	
	
	/*************project comments****************/
	public function get_project_cmnts($s_where)
    {
        $sql = "SELECT m.* , CONCAT(u.s_first_name,' ',u.s_last_name) s_profile_name , u.id AS post_owner_user_id , u.s_profile_photo
                FROM {$this->db->CHRISTAN_PROJECT_CMT} m , {$this->db->USERS} u
                WHERE 1 AND u.id=m.i_user_id {$s_where}
                ";
        $res = $this->db->query($sql)->result_array();
        return $res;
    }
	public function add_project_cmnts($info)
    {
        $this->db->insert($this->db->CHRISTAN_PROJECT_CMT,$info);
        
        $res = $this->db->insert_id();
        return $res;
    }
	public function get_count_project_cmnts($s_where)
    {
		 $sql = "SELECT COUNT(*) cmts
                FROM {$this->db->CHRISTAN_PROJECT_CMT} m
                WHERE 1 {$s_where}
                ";
        $res = $this->db->query($sql)->result_array();
        return $res[0]['cmts'];
    }
	/*************project comments****************/
	
	/*************project like****************/
	public function add_project_likes($info)
    {
		$s_where	= " AND i_user_id='".intval(decrypt($this->session->userdata('user_id')))."' AND i_proj_id='".$info['i_proj_id']."'";
		$count	= $this->get_count_project_likes($s_where);
		if($count>0)
			return false;
		else
        {
			$this->db->insert($this->db->CHRISTAN_PROJECT_LIKE,$info);
       		$res = $this->db->insert_id();
        	return $res;
		}
    }
	public function get_count_project_likes($s_where)
    {
		$sql = "SELECT COUNT(*) AS lik
                FROM {$this->db->CHRISTAN_PROJECT_LIKE} m
                WHERE 1 {$s_where}";
				
        $res = $this->db->query($sql)->result_array();
        return $res[0]['lik'];
    }
	
	public function get_project_likes($s_where)
    {
		$sql = "SELECT m.* , CONCAT(u.s_first_name,' ',u.s_last_name) s_profile_name , u.id AS post_owner_user_id, u.s_profile_photo
                FROM {$this->db->CHRISTAN_PROJECT_LIKE} m , {$this->db->USERS} u
                WHERE 1 AND u.id=m.i_user_id {$s_where}";
				
        $res = $this->db->query($sql)->result_array();
        return $res;
    }
	/*************project like****************/
	
    
}// end of model