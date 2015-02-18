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

class Delete_prayer_request extends Base_controller {

	public function __construct() {
		try
		{
			parent::__construct();
			$this->load->model('prayer_wall_model');
		}
		catch(Exception $err_obj)
		{
			show_error($err_obj->getMessage());
		}
	}


    public function index() {
		
		$current_date = (date("Y-m-d H:i:s"));
		$start_date = date("Y-m-d H:i:s", strtotime("$current_date -1 month"));
		$end_date = date("Y-m-d H:i:s", strtotime("$current_date -5 day"));
		
		$prayer_request_deletion = $this->data['site_settings_arr']['i_clear_prayer_request'];
		
		$curr_date = date('Y-m-d');
		
		$where = " WHERE DATE(p.dt_end_date) <= '{$curr_date}' AND TIMESTAMPDIFF(MONTH, DATE(p.dt_end_date), '{$curr_date}') >= {$prayer_request_deletion}";
		
		$prayer_arr  = $this->prayer_wall_model->get_all_prayers($where);
		
		
		if(count($prayer_arr)){
			foreach($prayer_arr as $k=> $val){
					$this->prayer_wall_model->delete_by_id($val['id']);			
			}
		}
		
		
		### clear old verse data per user  too...
		
		$deleteFiveFruits = "DELETE FROM {$this->db->FIVE_FRUITS_PER_USER}
						 	 WHERE dt_created_on  <= '{$curr_date}' ";
						

		$this->db->query($deleteFiveFruits);
		
	}
    
  
}

