<?php

/* * *******
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
include(APPPATH . 'controllers/base_controller.php');

class help_center extends Base_controller {

//    private $pagination_per_page = 10;
//    private $home_pagination_per_page = 8;
//    private $commits_pagination_per_page = 5;
//    private $all_commits_pagination_per_page = 10;

    public function __construct() {

        try {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...

            $this->load->model('users_model');
            $this->load->model('help_center_model');
            // $this->load->model('landing_page_cms_model');

            $this->i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function index() {
        //die('d');
        try {

            $posted = array();
            $this->data["posted"] = $posted; /* don't change */
            $data = $this->data;
           // $this->data["MAIN_MENU_SELECTED"] = 20;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');


            parent::_add_js_arr(array('js/ddsmoothmenu.js',
                'js/switch.js', 'js/animate-collapse.js',
                'js/lightbox.js',
                'js/jquery-ui-1.8.2.custom.min.js',
                'js/stepcarousel.js',
                'js/jquery/ui/jquery.ui.core.js',
                'js/jquery.ui.datepicker.js',
                'js/jquery-ui-timepicker-addon.js',
                'js/jquery-ui.triggeredAutocomplete.js',
                'js/jquery-ui-sliderAccess.js',
                'js/tab.js',
                'js/frontend/logged/holy_place/prayer_wall.js',
                'js/frontend/logged/tweets/tweets.js',
                'js/autocomplete/jquery.autocomplete.js'
            ));

            parent::_add_css_arr(array('css/jquery-ui-1.8.13.custom.css',
                'css/jquery-ui-1.8.2.custom.css',
                'css/jquery.autocomplete.css'));

            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
                         
                        $date['cat'] = $this->help_center_model->get_help_center_category(); 
                        $data['help1'] = $this->help_center_model->get_all_help_by_cat(" where c.h_cat=1",0,3,'');  
			$data['help2'] = $this->help_center_model->get_all_help_by_cat(" where c.h_cat=2",0,3,'');  
			$data['help3'] = $this->help_center_model->get_all_help_by_cat(" where c.h_cat=3",0,3,'');  
			$data['help4'] = $this->help_center_model->get_all_help_by_cat(" where c.h_cat=4",0,3,'');  
			$data['help5'] = $this->help_center_model->get_all_help_by_cat(" where c.h_cat=5",0,3,'');  
			$data['help6'] = $this->help_center_model->get_all_help_by_cat(" where c.h_cat=6",0,3,'');
                        $data['help7'] = $this->help_center_model->get_all_help_by_cat(" where c.h_cat=7",0,3,'');
                       //pr($date['categories'],1);
          // pr($date['help1'],1);
//
//            
            $VIEW = "logged/help_center/help_center.phtml";
            parent::_render($data,$VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

  public function help_center_category_details($id)
	{
          // die($id);
		try
        {
			$posted=array();
			$this->data["posted"]=$posted;/*don't change*/    
			$data = $this->data;      
			//$this->data["MAIN_MENU_SELECTED"] = 6;
			parent::_set_title('::: COGTIME Xtian network :::');
			parent::_set_meta_desc('');
			parent::_set_meta_keywords('');
		
			
			parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
										'js/switch.js','js/animate-collapse.js',
										'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
										'js/stepcarousel.js',
										'js/jquery.autofill.js',
										'js/frontend/logged/tweets/tweet_utilities.js',
										'js/tab.js',
										'js/jquery.eislideshow.js',
										'js/jquery.hoverIntent.minified.js',
										'js/jquery.naviDropDown.1.0.js',
										'js/jquery.bxslider.min.js',
                                                                                 'js/jquery.fitvids.js'  
										));
										
			parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
											  'css/dd.css') );
				
				
			# view file...
			/*ob_start();
			$content = $this->generate_christan_project_AJAX();
			$content = ob_get_contents();
			$data['listingContent'] = $content;
			ob_end_clean();*/
			//$data['listingContent'] = $content;
			
			
			
			//pr($data,1);
                        $data['all_cat_name'] = $this->help_center_model->get_all_cat_name();
                        $data['cat_name'] = $this->help_center_model->get_cat_name($id);
			$data['category_details'] = $this->help_center_model->get_cat_details_by_id($id);
			$VIEW = "logged/help_center/help_center_details.phtml"; 
			parent::_render($data, $VIEW);
			
        }
        catch(Exception $err_obj)
        {
           
        } 
	}
         public function help_center_question_details($id)
	{
          //die($id);
		try
        {               //$this->load->model('help_center_model');
			$posted=array();
			$this->data["posted"]=$posted;/*don't change*/    
			$data = $this->data;      
			//$this->data["MAIN_MENU_SELECTED"] = 6;
			parent::_set_title('::: COGTIME Xtian network :::');
			parent::_set_meta_desc('');
			parent::_set_meta_keywords('');
		
			
			parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
										'js/switch.js','js/animate-collapse.js',
										'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
										'js/stepcarousel.js',
										'js/jquery.autofill.js',
										'js/frontend/logged/tweets/tweet_utilities.js',
										'js/tab.js',
										'js/jquery.eislideshow.js',
										'js/jquery.hoverIntent.minified.js',
										'js/jquery.naviDropDown.1.0.js',
										'js/jquery.bxslider.min.js',
                                                                                 'js/jquery.fitvids.js'  
										));
										
			parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
											  'css/dd.css') );
				
				
			# view file...
			/*ob_start();
			$content = $this->generate_christan_project_AJAX();
			$content = ob_get_contents();
			$data['listingContent'] = $content;
			ob_end_clean();*/
			//$data['listingContent'] = $content;
			
			
			
			//pr($data,1);
                        $data['all_cat_name'] = $this->help_center_model->get_all_cat_name();
                        $data['question_details'] = $this->help_center_model->get_all_question($id);
                        //$data['cat_name'] = $this->help_center_model->get_cat_name_by_question_id($id);
			$data['category_details'] = $this->help_center_model->get_cat_name_by_id($id);
			$VIEW = "logged/help_center/help_center_question_details.phtml"; 
			parent::_render($data, $VIEW);
			
        }
        catch(Exception $err_obj)
        {
           
        } 
	}
        
         public function help_center_search()
	{
          //die($id);
             
             //die();
		try
        {               //$this->load->model('help_center_model');
			$posted=array();
			$this->data["posted"]=$posted;/*don't change*/    
			$data = $this->data;      
			//$this->data["MAIN_MENU_SELECTED"] = 6;
			parent::_set_title('::: COGTIME Xtian network :::');
			parent::_set_meta_desc('');
			parent::_set_meta_keywords('');
		
			
			parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
										'js/switch.js','js/animate-collapse.js',
										'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
										'js/stepcarousel.js',
										'js/jquery.autofill.js',
										'js/frontend/logged/tweets/tweet_utilities.js',
										'js/tab.js',
										'js/jquery.eislideshow.js',
										'js/jquery.hoverIntent.minified.js',
										'js/jquery.naviDropDown.1.0.js',
										'js/jquery.bxslider.min.js',
                                                                                 'js/jquery.fitvids.js'  
										));
										
			parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
											  'css/dd.css') );
				
				
			# view file...
			/*ob_start();
			$content = $this->generate_christan_project_AJAX();
			$content = ob_get_contents();
			$data['listingContent'] = $content;
			ob_end_clean();*/
			//$data['listingContent'] = $content;
			 $help_key = trim($this->input->post('help_key'));
			
			
			//pr($data,1);
                        $data['all_cat_name'] = $this->help_center_model->get_all_cat_name();
                        $data['help_tag'] = $help_key;
                        $data['Search_result'] = $this->help_center_model->get_help_search_result($help_key);
                       // $data['question_details'] = $this->help_center_model->get_all_question($id);
                        //$data['cat_name'] = $this->help_center_model->get_cat_name_by_question_id($id);
			//$data['category_details'] = $this->help_center_model->get_cat_name_by_id($id);
			$VIEW = "logged/help_center/help_center_search.phtml"; 
			parent::_render($data, $VIEW);
			
        }
        catch(Exception $err_obj)
        {
           
        } 
	}
        
        
        
}

// end of controller...

