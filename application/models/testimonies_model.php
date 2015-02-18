<?php
include_once(APPPATH.'models/base_model.php');
class Testimonies_model extends Base_model
{
    
    public function __construct() 
    {
        parent::__construct();
    }
    
    
   
	
    
 
    
   
	
	public function get_all_testimonies($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY id DESC';
       
        
        $sql = "SELECT t.*, CONCAT(u.s_first_name,' ',u.s_last_name) posted_by, u.s_profile_photo 
                FROM {$this->db->BIBLE_TESTIMONIES} t 
                LEFT JOIN {$this->db->BIBLE_PRAYER_REQUEST} r on t.i_prayer_req_id = r.id
                LEFT JOIN {$this->db->USERS} u on u.id=r.i_user_id 
                
                {$where} {$s_order_by} {$limit}";
                /*LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id*/
				
        $res = $this->db->query($sql)->result_array();
        return $res;
    }
    
    public function get_count_all_testimonies($where)
    {
        $sql = "SELECT COUNT(*) testimonies FROM {$this->db->BIBLE_TESTIMONIES} t
                LEFT JOIN {$this->db->BIBLE_PRAYER_REQUEST} r on t.i_prayer_req_id = r.id
                LEFT JOIN {$this->db->USERS} u on u.id=r.i_user_id 
                     {$where}";/*LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id*/
        $res = $this->db->query($sql)->result_array();
        return $res[0]['testimonies'];
    }

    
    public function get_testimony_detail_by_testimony_id($id)
    {
        $sql = "SELECT * FROM {$this->db->BIBLE_TESTIMONIES} WHERE id={$id}";
        $res = $this->db->query($sql)->result_array();
        return $res[0];
    }
    
    public function edit_testimony($info,$id)
    {
        $this->db->update($this->db->BIBLE_TESTIMONIES,$info,array('id'=>$id));
    }
	
    public function delete_by_id($id)
    {
        $sql = "DELETE FROM {$this->db->BIBLE_TESTIMONIES} WHERE id={$id}";
        $this->db->query($sql);
    }
    
    public function get_post_details_by_testimonial_id($id)
    {
        $sql = "SELECT r.* , CONCAT(u.s_first_name,' ',u.s_last_name) posted_by
               FROM {$this->db->BIBLE_TESTIMONIES} t 
                LEFT JOIN {$this->db->BIBLE_PRAYER_REQUEST} r on t.i_prayer_req_id = r.id
                LEFT JOIN {$this->db->USERS} u on u.id=r.i_user_id 
                WHERE t.id = {$id}
                ";
        $res = $this->db->query($sql)->result_array();
        return $res[0];
    }
	
	
    
}// end of model