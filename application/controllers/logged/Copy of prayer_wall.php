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


class Build_the_kingdom extends Base_controller
{
    
    private $pagination_per_page =  10 ;
	private $home_pagination_per_page = 8;
	private $commits_pagination_per_page = 5;
	private $all_commits_pagination_per_page = 10;
    
    public function __construct()
     {
	 	
        try
        {
            parent::__construct();
               parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...

            $this->load->model('users_model');
			$this->load->model('holy_place_model');
			$this->load->model('bible_fruits_model');
			$this->load->model('prayer_wall_photos_model');
			$this->load->model('prayer_wall_model');
			$this->load->model('prayer_commit_model');
			$this->load->model('user_alert_model');
			$this->load->model('user_notifications_model');
			
			$this->i_profile_id = intval(decrypt($this->session->userdata('user_id')));
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
            $this->data["MAIN_MENU_SELECTED"] = 6;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js',
										'js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',
										'js/tab.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.13.custom.css') );
          
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			
			
			# view file...
			
            $VIEW = "logged/build_the_kingdom/giving.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }
	
}   // end of controller...

