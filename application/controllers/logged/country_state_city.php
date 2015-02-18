<?php
/*********
* Author: 
* Date  : 
* Modified By: 
* Modified Date:
* 
* Purpose:
* Controller For WALL
* 
* 
*/
include(APPPATH.'controllers/base_controller.php');

class Country_state_city extends Base_controller
{

    public function __construct()
     {
        try
        {
            parent::__construct();
            #parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...
            $this->load->model('users_model');

        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    
    public function getstate($countryid) 
    {
        try
        {
			$where	= " i_country_id='".decrypt($countryid)."'";
            $return 	= makeOptionState($where,'');
			echo $return;
			exit;
        }
        catch(Exception $err_obj)
        {
           
        } 

    }   
	
	public function getcity($stateid) 
    {
        try
        {
            $where	= " i_state_id='".decrypt($stateid)."'";
            $return 	= makeOptionCity($where,'');
			echo $return;
			exit;    
            
        }
        catch(Exception $err_obj)
        {
           
        } 

    }
    
   
}   // end of controller...

