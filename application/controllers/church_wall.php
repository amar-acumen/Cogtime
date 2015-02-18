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


class Church_wall extends Base_controller
{
    
//    private $pagination_per_page =  10;
//    private $comments_pagination_per_page =  2 ;
//    private $people_liked_pagination_per_page =  4 ;
//   
    
    public function __construct()
     {
	 	
        try
        {
            parent::__construct();
            parent::_add_church_css_arr (array('css/church.css'));
               parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers... 
//			$user_id = intval(decrypt($this->session->userdata('user_id')));
//                        parent::check_is_church_admin($user_id);
            $this->load->model('users_model');
			
			$this->load->model('user_notifications_model');
            $this->load->model('user_alert_model');
			$this->load->model('landing_page_cms_model');
                        $this->load->model('church_new_model');
			$this->i_profile_id = intval(decrypt($this->session->userdata('user_id')));
                         $this->upload_path = BASEPATH . '../uploads/church_logo_image/';
                         $this->upload_cover_path = BASEPATH.'../uploads/church_cover_image/';
                         $this->load->helper('Imagelibrary_helper');
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    
    public function index($c_id) 
    {
       //echo $c_id;
        //die('comming soon.........');
        try
        {
        $user_id = intval(decrypt($this->session->userdata('user_id')));
                        //parent::check_is_church_admin($user_id,$c_id);
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
//               $this->data["MAIN_MENU_SELECTED"] = 1;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
//          
              $_SESSION['logged_church_id'] = $c_id;
            $data['church_arr'] =     $this->church_new_model->get_church_info($c_id);
                $VIEW = "logged/church/church_wall.phtml";
            

                  parent::_render($data, $VIEW);
 
            
        }
        
        catch(Exception $err_obj)
        {
           
        } 

    } // end of index   
    

    
   
}   // end of controller...

