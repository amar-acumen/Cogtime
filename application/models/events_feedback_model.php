<?php
include_once(APPPATH.'models/base_model.php');
class Events_feedback_model extends Base_model
{
	
	public function __construct() 
	{
		parent::__construct();
	}


	public function update($arr=array(), $id) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->update($this->db->EVENTS_FEEDBACK, $arr, array('id'=>$id));
	}
	

	public function delete_by_id($id) {
	     $sql = 'DELETE FROM '.$this->db->EVENTS_FEEDBACK.' WHERE id="'.$id.'"';
		 $this->db->query($sql);
				
	}
	
	public function insert_feedback($arr=array()) {
		if(count($arr)==0) {
			return null;
		}
		$this->db->insert($this->db->EVENTS_FEEDBACK, $arr); //echo $this->db->last_query(); exit;
		return $this->db->insert_id();
	}
	
	public function get_feed_list($i_media_id , $page,$feedbacks_per_page)
	{
	$sql="select * from cg_event_feedback where i_event_id=".$i_media_id." order by id desc limit ".$page.",".$feedbacks_per_page;
	//echo $sql;
	$res=$this->db->query($sql);
	$result=$res->result_array();
	return $result;
	}
	
	public function get_total_by_event_id($i_media_id)
	{
	$sql="select count(*) as count from cg_event_feedback where i_event_id=".$i_media_id." order by id desc ";
	$res=$this->db->query($sql);
	$result=$res->result_array();
	return $result['0']['count'];
	}
}
