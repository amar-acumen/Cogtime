<?php
/*********
* Author: 
* Date  : 
* Modified By: 
* Modified Date:
* 
* Purpose:
* Controller For 
* 
* 
*/

include(APPPATH.'controllers/base_controller.php');
include_once APPPATH."libraries/gmapAPI/simpleGMapAPI.php";
include_once APPPATH."libraries/gmapAPI/simpleGMapGeocoder.php";


class Create_church_space extends Base_controller
{
    
    private $pagination_per_page =  10 ;
	private $church_pagination_per_page =  10;
	private $quiz_pagination_per_page =  10;
	

    
    public function __construct()
     {
	 	
        try
        {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...
			$this->upload_path = BASEPATH.'../uploads/project_cv_user/';
            $this->load->model('users_model');
			$this->load->model('landing_page_cms_model');
			$this->load->model('projects_model');
			$this->load->model('church_model');
			$this->load->model('bible_model');
                        $this->load->model('church_new_model');
                        
			
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
										'js/jquery/ui/jquery.ui.core.js',
										'js/jquery.ui.datepicker.js',
									    'js/jquery-ui-timepicker-addon.js',
										'js/jquery-ui-sliderAccess.js',
										'js/frontend/logged/tweets/tweet_utilities.js',
										'js/frontend/logged/build_kingdom/tithe_time.js',
										'js/tab.js',
										'js/ModalDialog.js',
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css') );
          
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
            
			
//			$where = " WHERE s_keyword like 'buildkindom_givin%'";
//            $content_arr = $this->landing_page_cms_model->get_contents($where);
//			$data['s_content'] = $content_arr[0]['s_desc'];
			
			# view file...
            $church_arr = $this->church_new_model->get_user_church_info($i_user_id);
            $data['c_arr'] = $church_arr;
			
            $VIEW = "logged/build_the_kingdom/find-new-church.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }
	
	
	
	
  function search_church_by_email(){
      //die();
      $txt_email = $this->input->post('txt_email');
      $query = $this->db->get_where('cg_church', array('ch_email' => $txt_email ,'i_disabled'=>1 ));
            $result = $query->result();
            $data['result_arr'] = $result;
            $VIEW_FILE = 'church_new_ajax.phtml';
            $html =  $this->load->view($VIEW_FILE, $data,true);
            echo json_encode( array('html'=>$html));
      
  }
  	###
	

  
}   // end of controller...

