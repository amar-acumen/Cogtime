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


class My_daily_bible_reading_plan extends Base_controller
{
    
    private $pagination_per_page =  25 ;
    private $search_pagination_per_page =  2 ;
    private $popular_pagination_per_page =  10 ;
    
    
    
    private $ring_members_pagination_per_page =  10 ;
    
    public function __construct()
     {
	 	
        try
        {
            parent::__construct();
               parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...
			$this->upload_path = BASEPATH.'../uploads/user_ring_logos/';
            //$this->load->model('users_model');
			//$this->load->model('holy_place_model');
			//$this->load->model('bible_fruits_model');
			
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
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',*/
										'js/production/tweet_utilities.js',
//                                        'js/stepcarousel.js'
                                        ));
                                        
//            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
//                                          'css/dd.css') );
          
            
			//$data[] = ;
			# view file...
			
            $VIEW = "logged/holy_place/my_daily_reading_plan.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }
	
	public function generateNextDaySlot($start_day, $limit)
	{
		try
		{
				$sel_html = makeDaysList($start_day, $limit);				
				echo json_encode( array('success'=>true,'sel_html'=>$sel_html));
		}
		catch(Exception $err_obj)
        {
            
        } 
	}
	
	
	public function generateReadingPlan()
	{
		try
		{
				$plan_type = trim($this->input->post('plan_type'));
				$per_day_reading_old_testament = '';
				$per_day_reading_new_testament = '';
				
				 if(intval($plan_type) == 1){
						
						$date_to = get_db_dateformat(trim($this->input->post('date_to')),'/');
						$date_end = get_db_dateformat(trim($this->input->post('date_end')),'/');	
						
						$d1= strtotime($date_to);
						$d2= strtotime($date_end);
						$all = round(($d2 - $d1) / 60);
						$d = floor ($all / 1440);
						
						
						echo "Days difference = ".$d ; 
						## reading from  testament
						 $per_day_reading_old_testament =  get_bible_reading_plan($d , 21345);
						 
						 pr($per_day_reading_old_testament);
						 
						 $per_day_reading_new_testament =  get_bible_reading_plan($d , 7958);
						 
						 pr($per_day_reading_new_testament); exit;
						 
						 
				 }
				 elseif(intval($plan_type) == 2) {
					  $selected_day = trim($this->input->post('selected_day'));
					   
					   $per_day_reading_old_testament =  get_bible_reading_plan($selected_day , 21345);
					   
					   $old_verse_info_first_arr = explode('###',$per_day_reading_old_testament[0]);
					   $old_verse_total_verse_for_first_few_day = $old_verse_info_first_arr[0];
					   $old_verse_total_first_few_day = $old_verse_info_first_arr[1];
					   
					   $old_verse_info_last_arr = explode('###',$per_day_reading_old_testament[1]);
					   $old_verse_total_verse_for_last_few_day = $old_verse_info_last_arr[0];
					   $old_verse_total_last_few_day = $old_verse_info_last_arr[1];
						 
						pr($per_day_reading_old_testament);
						 
						 $per_day_reading_new_testament =  get_bible_reading_plan($selected_day , 7958);
						 
						 
						$new_verse_info_first_arr = explode('###',$per_day_reading_new_testament[0]);
					   	$new_verse_total_verse_for_first_few_day = $new_verse_info_first_arr[0];
					   	$new_verse_total_first_few_day = $new_verse_info_first_arr[1];
					   
					  	$new_verse_info_last_arr = explode('###',$per_day_reading_new_testament[1]);
					   	$new_verse_total_verse_for_last_few_day = $new_verse_info_last_arr[0];
					   	$new_verse_total_last_few_day = $new_verse_info_last_arr[1];
						
						
						pr($per_day_reading_new_testament); exit;
						
						 
				 }
				
				echo json_encode( array('success'=>true,'sel_html'=>$sel_html));
		}
		catch(Exception $err_obj)
        {
            
        } 
	}
}   // end of controller...

