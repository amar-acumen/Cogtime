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

class Delete_displayed_notification extends Base_controller {

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
		
		$delete_user = "DELETE FROM cg_notifications 
						 	   WHERE i_notification_shown  = 2 ";
						

		$this->db->query($delete_user);
		
	}
    
  
}

