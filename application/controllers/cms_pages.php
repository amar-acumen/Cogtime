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

class Cms_pages extends Base_controller
{
    public function __construct()
     {
        try
        {
            parent::__construct();
           
			# loading reqired model & helpers...
            $this->load->model('cms_model');
		
			
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    
    
    
    public function index($id = '') 
    {
        try
        {      
			     
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data; 
			     
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
			parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
										'js/jquery.autofill.js',
										'js/stepcarousel.js'
										));
										
    		$cms_id = intval($id);    
			$data['info_arr'] = $this->cms_model->get_by_id($cms_id);
			//pr($data['info_arr']);
            # view file...
            $VIEW = "cms/cms.phtml";
            parent::_render($data, $VIEW);
			
		 }
        catch(Exception $err_obj)
        {
           
        }
    }
	
	

}   // end of controller...

