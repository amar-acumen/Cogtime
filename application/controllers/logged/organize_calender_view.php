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

class Organize_calender_view extends Base_controller
{
    
    
    public function __construct()
     {
        try
        {
            parent::__construct();
               parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...
            $this->load->model('users_model');
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    
    public function index() 
    {
        try
        {
                  
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
               $this->data["MAIN_MENU_SELECTED"] = 1;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js',
                                        'js/stepcarousel.js',
										'js/tab.js',*/
										'js/production/tweet_utilities.js',
										'js/jquery.jcarousel.min.js',
//										'js/jquery-ui-1.8.2.custom.min.js',
										'js/jquery.mCustomScrollbar.min.js',
										'js/production/organizer.js'
										
                                        ));
                                        
            parent::_add_css_arr( array(/*'css/date/jquery-ui-1.8.2.custom.css',*/
                                          'css/jquery.mCustomScrollbar.css') );
										  
            $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
         	$data['right_panel']['calendar_data'] = parent::show_event_calendar($i_profile_id, date('Y'), date('m'), '', true);
			$data['right_panel']['uid'] = $i_profile_id; 
            # view file...
            $VIEW = "logged/organize/new-organize-calender-view.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }   
    
    
}   // end of controller...

