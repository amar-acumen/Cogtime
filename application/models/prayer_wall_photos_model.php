<?php
include_once(APPPATH.'models/base_model.php');
class Prayer_wall_photos_model extends Base_model
{
    
    public function __construct() 
    {
        parent::__construct();
    }
    

    
	public function get_all_prayer_wall_photos($where='',$i_start=null,$i_limit=null,$s_order_by='')
    {
        $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
        $s_order_by = ($s_order_by != '')?'ORDER BY '.$s_order_by :'ORDER BY id DESC';
       
        
        $sql = "SELECT p.* FROM 
                {$this->db->PRAYER_WALL_PHOTOS} p {$where} {$s_order_by} {$limit}";
                /*LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id*/
				
        $res = $this->db->query($sql)->result_array();
        return $res;
    }
    
    public function get_count_all_prayer_wall_photos($where)
    {
        $sql = "SELECT COUNT(*) prayers FROM {$this->db->PRAYER_WALL_PHOTOS} p
                     {$where}";/*LEFT JOIN {$this->db->USERS} u on u.id=p.i_user_id*/
        $res = $this->db->query($sql)->result_array();
        return $res[0]['prayers'];
    }
    
    public function insert_prayer_wall_photos($info)
    {
        $this->db->insert($this->db->PRAYER_WALL_PHOTOS,$info);
        $last_id = $this->db->insert_id();
        return $last_id;
    }
    public function get_info_by_prayer_wall_photos_id($id)
    {
        $sql = "SELECT * FROM {$this->db->PRAYER_WALL_PHOTOS} WHERE id={$id}";
        $res = $this->db->query($sql)->result_array();
        return $res[0];
    }
    public function edit_prayer_wall_photos($info,$id)
    {
        $this->db->update($this->db->PRAYER_WALL_PHOTOS,$info,array('id'=>$id));
        
    }
    public function delete_by_id($id)
    {
        $sql = "DELETE FROM {$this->db->PRAYER_WALL_PHOTOS} WHERE id={$id}";
        $this->db->query($sql);
    }
    
    public function change_status_prayer_wall_photos($data,$id)
    {
        $this->db->update($this->db->PRAYER_WALL_PHOTOS, $data ,array('id'=>$id));
    }
	
	
    
}// end of model