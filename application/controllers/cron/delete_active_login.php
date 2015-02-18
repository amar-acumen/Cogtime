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

class Delete_active_login extends Base_controller {

	public function __construct() {
		try
		{
			parent::__construct();
			$this->load->model('users_model');
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
		
		
		$delete_user = "DELETE FROM cg_users_online 
						 	 WHERE unix_timestamp(ts_last_active) 
							 BETWEEN '".strtotime($start_date)."' AND '".strtotime($end_date)."'";
						

		$this->db->query($delete_user);
		
	}
    
  
}

