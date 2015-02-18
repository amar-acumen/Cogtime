<?php
/*********
* Author
* 
* Purpose:
*  Controller For Login Page 
* 
* 
*/

include(APPPATH.'controllers/base_controller.php');

class Holy_place extends Base_controller
{
    public function __construct()
     {
        try
        {
            parent::__construct();
           
			# loading reqired model & helpers...
            $this->load->model('cms_model');
            $this->load->model('landing_page_cms_model');
		
			
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
			$data['nav_slider_num'] = 6;       
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
			parent::_add_js_arr( array( 'js/jquery.autofill.js',
										'js/ddsmoothmenu.js',
										'js/ModalDialog.js',
										'js/lightbox.js',
										'js/stepcarousel.js'
										));
			
            //$where = " WHERE s_keyword like 'tc_%'";
            $data['contents'] = $this->landing_page_cms_model->get_contents();
            
            # view file...
            $VIEW = "banner_pages/holy_place_public.phtml";
            parent::_render($data, $VIEW);
			
		 }
        catch(Exception $err_obj)
        {
           
        }
    }
	
	

}   // end of controller...

