<?php
include_once(APPPATH.'models/base_model.php');
class Denomination_model extends Base_model
{

    public function __construct() 
    {
        parent::__construct();
    }
    
   
   //----------------------- get all denomination ----------------------
    public function get_all_denomination_list()
    {
        $sql = 'SELECT * FROM '.$this->db->DENOMINATION.' order by s_name asc';
        $query = $this->db->query($sql);
        $result_arr = $query->result_array();
//pr($result_arr,1);
        return $result_arr;
    }
    
    
    //-------------------- get total no of denomination ------------------
    public function denomination_list_count($where='')
    {
        
        
        $sql    = "SELECT count(*) as i_total FROM {$this->db->DENOMINATION}  {$where} ";
        $query     = $this->db->query($sql);
        $result_arr = $query->result_array();
        return $result_arr[0]['i_total'];
    }
    
    
    
    //-------------------------------------- update status ------------------------------------
    function update_status($arr=array(),$id)
    {
        $res = $this->db->update($this->db->DENOMINATION,  $arr, array('id'=>$id) );
       
        return $res;
        
    }
    
    //-------------------------------------- update status ------------------------------------
    function update_info($arr=array(),$id)
    {
        $res = $this->db->update($this->db->DENOMINATION,  $arr, array('id'=>$id) );
        $this->db->last_query();
    //echo "update : ".$res; 
        return $res;
        
    }
    
    
    //-------------------------- delete info -----------------------------
    function delete_info($id)
    {
        $this->db->delete($this->db->DENOMINATION,array('id'=>$id));
        return $this->db->affected_rows();
    }
    
    
    
    //------------------------- get particular denoination ----------------------
    function fetch_this($id)
    {
        $query = sprintf("SELECT * FROM %s WHERE `id`=%s",$this->db->DENOMINATION,$id);
        $res = $this->db->query($query)->result_array();
        //pr($res);
        return $res;
    
    }
    
    
    //--------------------------------- edit info -----------------------------------
    function edit_info()
    {
        
    }
    
    //------------------------------- add info ---------------------------------
    function add_info($info=array())
    {
        //pr($info);
        $res = $this->db->insert($this->db->DENOMINATION,$info);
        //echo $this->db->last_query();
        return $insert_id = $this->db->insert_id();
    }



}