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

class Public_profile_view_all_photo extends Base_controller
{
    private $pagination_per_page = 10;
    private $album_pagination_per_page = 9;//10
    private $photo_pagination_per_page = 18;//16
    
    
    public function __construct()
     {
        try
        {
            parent::__construct();
			
            $this->upload_tmp_path = BASEPATH.'../uploads/wall_tmp/';
            $this->upload_path  = BASEPATH.'../uploads/wall_photos/';
               $this->upload_photo_path    = BASEPATH.'../uploads/wall_photos/';
            # loading reqired model & helpers...
            //$this->load->helper('wall_helper');
            $this->load->model('users_model');
            //$this->load->model('netpals_model');
            //$this->load->model('my_prayer_partner_model');
            //$this->load->model('data_newsfeed_model');
            //$this->load->model('newsfeed_comments_model');
            $this->load->model('user_photos_model');
            $this->load->model('photo_albums_model');
			//$this->load->model('user_notifications_model');
			//$this->load->model('user_alert_model');
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }
    
    
    
    
        # index function definition...
    public function index($id='') 
    {
        try
        { 
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/lightbox.js',
                                        'js/tab.js',
                                        'js/jquery.autofill.js',
                                        
                                        'js/jquery.lightbox.js',*/
                                        
                                        
                                        'js/production/my_friends.js',
                                        'js/production/my_net_pals.js',
                                        'js/production/my_prayer_partner.js',
                                        'js/production/my_message.js',
										'js/production/tweet_utilities.js',
                                  //      'js/jquery.fancybox.js'
                                        ));
                                        
          //  parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
          //                              'css/jquery.fancybox.css') );
                                        
                                        
            # adjusting header & footer sections [End]...
            $data['page_view_type'] = 'public_account';
            
            $i_profile_id = intval($id);
            
            $public_arr_profile_info = $this->users_model->fetch_this($i_profile_id);
            $data['public_arr_profile_info'] = $public_arr_profile_info;
            $data['profile_id'] = $i_profile_id;
            
            
            ## get user's all photos ##
            //parent::_get_user_all_photos($i_profile_id,'',0,9);
            
            ## get user's all photo albums ##
            //parent::_get_user_all_photo_albums($i_profile_id,0,10);
            
            $data['album_pagination_per_page'] = $this->album_pagination_per_page ;
            $data['photo_pagination_per_page'] = $this->photo_pagination_per_page ;
            
            
			
				
            
            ## albums ##
            ob_start();
            $this->album_pagination_show_more($i_profile_id);
            $content = ob_get_contents();
            $content_obj = json_decode($content);
            $data['result_album_content'] = $content_obj->html; 
            ob_end_clean();
            
            
            ## photos ##
            ob_start();
            $this->photo_pagination_show_more($i_profile_id);
            $content = ob_get_contents();
            $content_obj = json_decode($content);
            $data['result_photo_content'] = $content_obj->html; 
            ob_end_clean();
            
            


          
            # view file...
            $VIEW_PG_FILE = "public_profile/public_profile_view_all_photo/myphoto-William-D.phtml";
            $VIEW = "{$VIEW_PG_FILE}";
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           show_error($err_obj->getMessage());
        }
    }   //index
    
   
    
    
    public function album_pagination_show_more($i_profile_id , $page=0)  
    {
        
        $cur_page = $page + $this->album_pagination_per_page;
        $data = $this->data;
        
        $this->load->model('photo_albums_model');
        $result = $this->photo_albums_model->get_photo_album_with_privacy_settings($i_profile_id, $page, $this->album_pagination_per_page);
       
        //echo $this->db->last_query();
        $total_rows = $this->photo_albums_model->get_total_photo_album_with_privacy_settings($i_profile_id);
        //pr($result,1);
        $data['album_result_arr'] = $result;
        $data['album_no_of_result'] = $total_rows;
        $data['album_current_page'] = $cur_page;
		 $data['profile_id'] = $i_profile_id;
        
        //--- for check whether more videos are there or not
            $view_more = true;
             $rest_counter = $total_rows-$page;
             if($rest_counter<=$this->album_pagination_per_page)
                $view_more = false;
             
        //--------- end check
        
        $VIEW_FILE = "public_profile/public_profile_view_all_photo/photo_albums_ajax.phtml";
        
        if( is_array($result) && count($result) ) {
            $content = $this->load->view( $VIEW_FILE , $data, true);
        }
        else {
            $content = '<div class="shade_box_blue no_comments" style="padding-top:5px; "><div  class="shade_norecords" style="width:375px;"><p class="blue_bold12">No Photo Album.</p></div></div>';
        }
        
        echo json_encode( array('html'=>$content, 'current_page'=>$cur_page,'view_more'=>$view_more) );
    }
    
    
    public function photo_pagination_show_more($i_profile_id , $page=0)  
    {
        
        $cur_page = $page + $this->photo_pagination_per_page;
        
        
        $data = $this->data;
        
        $this->load->model('user_photos_model');
        
        $result = $this->user_photos_model->get_allphotos_with_comments_by_user_id_($i_profile_id,'',$page , $this->photo_pagination_per_page);
        
        //echo $this->db->last_query();
        $total_rows = $this->user_photos_model->get_total_by_user_id($i_profile_id);
        //pr($result,1);
        $data['photo_result_arr'] = $result;
        $data['photo_no_of_result'] = $total_rows;
        $data['photo_current_page'] = $cur_page;
		 $data['profile_id'] = $i_profile_id;
    
        //--- for check whether more videos are there or not
        $view_more = true;
        $rest_counter = $total_rows-$page;
        if($rest_counter<=$this->photo_pagination_per_page)
        $view_more = false;
        //--------- end check
    
    
        
        $VIEW_FILE = "public_profile/public_profile_view_all_photo/photos_ajax.phtml";
        
        if( is_array($result) && count($result) ) {
            $content = $this->load->view( $VIEW_FILE , $data, true);
        }
        else {
            $content = '<div class="shade_box_blue no_comments" style="padding-top:5px; "><div  class="shade_norecords" style="width:360px;"><p class="blue_bold12">No Photo.</p></div></div>';
        }
        
        echo json_encode( array('html'=>$content, 'current_page'=>$cur_page,'view_more'=>$view_more) );
    }
}
?>