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

class Get_server_time extends Base_controller {

	public function __construct() {
		try
		{
			parent::__construct();
			$this->load->model('users_model');
			$this->load->model('organizer_note_model');
			$this->load->model('organizer_todo_model');
			$this->load->model('system_reminder_model');
		}
		catch(Exception $err_obj)
		{
			show_error($err_obj->getMessage());
		}
	}


    public function index() {
        
		##### fetch the todolist of expire after two days ########
									   
					
		# todays date +2  and is active.
		# send mail , im , show popup method in base controller.
		# check if reminder expired.
		$popup_array = array();
		$user_array= array();
		
		$s_user_where = "WHERE i_status = 1 
						 AND i_isdeleted = 1";
		$user_array = $this->users_model->user_list();
		echo date('H:i:s',time());
		echo '<br/>.'. date('Y-m-d');
		
		$today    =  strtotime(date("Y-m-d"));
		$tommorow =  strtotime('+2 days');
		
		echo date('Y-m-d', $today).'" AND "'.date('Y-m-d', $tommorow);
		
		
		$tzone = date_default_timezone_get();
		echo "$tzone";  
		$timezone = new DateTimeZone("CET");
		print_r(reset($timezone->getTransitions()));
		//pr($popup_array);	
    }
    
  
}

