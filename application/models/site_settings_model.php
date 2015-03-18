<?php

class Site_settings_model extends CI_Model{
	
	public function __construct() {
		parent::__construct();
	}

	public function get($name)
	 {
		$result = $this->db->get('site_settings');
		$row = $result->row_array();
		if(isset($row[$name])) {
			return $row[$name];
		}
		else {
			return null;
		}
	}
	
	public function get_all()
	 {
		
		
		$result = $this->db->get('site_settings');
		$row = $result->row_array();
		if(isset($row)) {
			return $row;
		}
		else {
			return null;
		}
	}
	
	
	
	
	public function get_by_id($id, $start_limit="", $no_of_page="") {
		if("$start_limit" == "") {
			$sql = 'SELECT * FROM '.$this->db->site_settings.'  where id = "'.$id.'"';
		}
		else {
			$sql = 'SELECT * FROM '.$this->db->site_settings.'  where id = "'.$id.'" limit '.$start_limit.', '.$no_of_page;
		}
		$query = $this->db->query($sql);
		$result_arr = $query->result_array();
		
		return $result_arr[0];
	}
	
	
	
    public function update($arr=array(), $i_id) 
    {
        if(count($arr)==0) {
            return null;
        }
        $this->db->update('site_settings', $arr, array('id'=>$i_id));
    }
    
    

    # fixing paypal url part...
    public function getPaypalURL($mode='live')
    {
        if( $mode=='test' )
            $URL = 'https://www.sandbox.paypal.com/cgi-bin/webscr';   // live paypal url...
        else
            $URL = 'https://www.paypal.com/cgi-bin/webscr';   // sandbox paypal url...

        return $URL;
    }
    function update_media_center_landing_page_text($info){
        //pr($info);
       // die();
        $data = $info;

$this->db->where('id', 1);
$this->db->update('cg_media_center_landing_page_text', $data); 
    }
    function get_media_text_by_id(){
        $query = $this->db->get('cg_media_center_landing_page_text');
        $result_arr = $query->result_array();
        return $result_arr[0];
        
    }
    
     function get_advertisement_cost($id){
        $sql = 'select * from cg_advertisement_cost where id  = '.$id.' ';
       $query = $this->db->query($sql);
	$result_arr = $query->result_array();
        //pr($result_arr,1);
        return $result_arr[0];
    }
    function update_ad_cost($info,$id){
        $data = $info;

$this->db->where('id', $id);
$this->db->update('cg_advertisement_cost', $data); 
    }
    
    
    
} 
