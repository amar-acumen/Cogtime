<?php
/*********
* Author:
* Date  : 
* Modified By: 
* Modified Date:
* 
* Purpose:
* 
* 
*/

include(APPPATH.'controllers/base_controller.php');

class GetVersesOfDay extends Base_controller {

	public function __construct() {
		try
		{
			parent::__construct();
			$this->load->model('holy_place_model');
		}
		catch(Exception $err_obj)
		{
			show_error($err_obj->getMessage());
		}
	}


    public function index() {
		
	 	//clear cg_five_fruits_per_user table
		$SQL = "TRUNCATE {$this->db->day_verse}";
		$this->db->query($SQL);
		## verse for a day for all user will remain same/ changed.
		$rand_verse_id = rand(1, 31102);
		$s_verse_where = " AND v.id =  {$rand_verse_id}"; 
		
        $sql = "SELECT *,v.id AS verseid FROM 
                {$this->db->BIBLE_BOOK} AS b, 
				{$this->db->BIBLE_CHAPTER} AS c ,
				{$this->db->BIBLE_VERSES} AS v 
				WHERE b.id=c.i_book_id AND c.id=v.i_chapter_id {$s_verse_where} ";
		
        $result_arr = $this->db->query($sql)->result_array(); 
		
		if(count($result_arr)){
			foreach($result_arr as $key=>$val){
		     	
				$arr = array();
				
				$arr['s_verse']   = $val['s_text'];
				$arr['s_book_name'] = $val['s_book_name'];
				$arr['bible_chapter_no'] = $val['s_chapter'];
				$arr['bible_verse_no'] = $val['i_verses'];
				$arr['dt_created_on'] = get_db_datetime();
				
				$this->db->insert($this->db->day_verse, $arr); 
			}
		}
		
		
				
				
	}
    
  
}

