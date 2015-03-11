<?php

include_once(APPPATH . 'models/base_model.php');

class Help_center_model extends Base_model {

    public function __construct() {
        parent::__construct();
        //$this->load->model('netpals_model');
        //$this->load->model('users_model');
    }
function get_help_center_category(){
    $sql = "select * from cg_help_center_category";
    $query = $this->db->query($sql); #echo $this->db->last_query(); exit;
		$result_arr = $query->result_array();
               // pr($result_arr,1);
                return $result_arr;
}
function fetch_help_cat_by_id($category_id){
    $sql = "select * from cg_help_center_category where id = $category_id";
    $query = $this->db->query($sql); #echo $this->db->last_query(); exit;
		$result_arr = $query->result_array();
               // pr($result_arr,1);
                return $result_arr;
    
}
function get_all_help( $where ,$i_start=null,$i_limit=null, $order_by)
                {
    if($where == ''){ $where = ' WHERE 1';}
     $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
     $order_by = 'order by c.id ASC';
 $sql = "select c.* , ct.cat_name from cg_help_center c,cg_help_center_category ct, cg_admin_user as u $where and c.h_cat = ct.id AND u.id = c.i_posted_by  $order_by $limit";
   $query = $this->db->query($sql); #echo $this->db->last_query(); exit;
   //die();		
    $result_arr = $query->result_array();
               // pr($result_arr,1);
                return $result_arr;
}
function get_total_help($s_where){
    
    $sql = "select count(*) as total_help from cg_help_center c $s_where";
    $query = $this->db->query($sql); #echo $this->db->last_query(); exit;
		$result_arr = $query->result_array();
               // pr($result_arr,1);
                return $result_arr[0]['total_help'];
}
function add_help_article($info){
    //pr($info);
    //die('dd');
    $data = $info;

$this->db->insert('cg_help_center', $data); 
}
function fetch_help_center_by_id($id){
   $sq1= 'select c.*,ct.cat_name from cg_help_center c,cg_help_center_category ct where c.h_cat = ct.id and c.id = '.$id.' ';
   $query = $this->db->query($sq1); #echo $this->db->last_query(); exit;
   //die();		
    $result_arr = $query->result_array();
               //pr($result_arr,1);
                return $result_arr;
}
function update_help_center_by_id($info,$row_id){
    $data = $info;

$this->db->where('id', $row_id);
$this->db->update('cg_help_center', $data); 
}
/******************front end*****************************************/
function get_all_help_by_cat( $where ,$i_start=null,$i_limit=null, $order_by){
     $limit  = (is_numeric($i_start) && is_numeric($i_limit))?" Limit ".intval($i_start).",".intval($i_limit):'';
      $order_by = 'order by c.id ASC';
 $sql = "select c.* , ct.cat_name from cg_help_center c,cg_help_center_category ct $where and c.h_cat = ct.id  $order_by $limit";
   $query = $this->db->query($sql); #echo $this->db->last_query(); exit;
   //die();		
    $result_arr = $query->result_array();
               // pr($result_arr,1);
                return $result_arr;
}
function get_cat_details_by_id($id){
    $sql = 'select * from cg_help_center where h_cat = '.$id.' order by id ASC';
     $query = $this->db->query($sql); #echo $this->db->last_query(); exit;
   //die();		
    $result_arr = $query->result_array();
               // pr($result_arr,1);
                return $result_arr;
}
function  get_cat_name($id){
    $sql = 'select cat_name from cg_help_center_category where id = '.$id.'';
    $query = $this->db->query($sql); #echo $this->db->last_query(); exit;
   //die();		
    $result_arr = $query->result_array();
               // pr($result_arr,1);
                return $result_arr;
}
function get_all_cat_name(){
     $sql = 'select * from cg_help_center_category';
    $query = $this->db->query($sql); #echo $this->db->last_query(); exit;
   //die();		
    $result_arr = $query->result_array();
               // pr($result_arr,1);
                return $result_arr;
}
function get_all_question($id){
     $sql = 'select * from cg_help_center where id = '.$id.'';
     $query = $this->db->query($sql); #echo $this->db->last_query(); exit;
   //die();		
    $result_arr = $query->result_array();
               // pr($result_arr,1);
                return $result_arr;
     
}
function get_cat_name_by_id($id){
     $sql = 'select c.*,ct.cat_name from cg_help_center c , cg_help_center_category ct where c.id = '.$id.' and c.h_cat = ct.id';
     $query = $this->db->query($sql); #echo $this->db->last_query(); exit;
   //die();		
    $result_arr = $query->result_array();
               // pr($result_arr,1);
                return $result_arr;
}
function  get_help_search_result($help_key){
   
    if($help_key == NULL){
        $where = 'where 1';
    }
    if($help_key != NULL){
        //echo $help_key;
        $where = 'where h_title LIKE "%'.$help_key.'%" OR h_des LIKE  "%'.$help_key.'%"';
       
        //die('dd');
    }
   $sql = 'Select * from cg_help_center '.$where.' order by id ASC';
    $query = $this->db->query($sql); #echo $this->db->last_query(); exit;
   //die();		
    $result_arr = $query->result_array();
               // pr($result_arr,1);
                return $result_arr;

}




}
