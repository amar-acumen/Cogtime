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

class Public_profile_view_all_video extends Base_controller
{
    private $pagination_per_page = 10;
    private $album_pagination_per_page = 10;
    private $video_pagination_per_page = 10;
    //private $pagination_per_page_all_videos=10;
    
    
    public function __construct()
     {
        try
        {
            parent::__construct();
            # loading reqired model & helpers...
            $this->load->helper('wall_helper');
            $this->load->model('users_model');
            $this->load->model('netpals_model');
            $this->load->model('my_prayer_partner_model');
            $this->load->model('data_newsfeed_model');
            $this->load->model('newsfeed_comments_model');
            $this->load->model('my_videos_model');
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
                                        'js/jquery.bxSlider.js',
                                        'js/jquery.lightbox.js',*/
                                        'js/production/public_profile.js',
                                        
                                        'js/production/my_friends.js',
                                        'js/production/my_net_pals.js',
                                        'js/production/my_prayer_partner.js',
                                        'js/production/my_message.js',
										'js/production/tweet_utilities.js',
                                        'js/jquery.fancybox.js'
                                        ));
                                        
          //  parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
           //                             'css/jquery.fancybox.css') );
                                        
                                        
            # adjusting header & footer sections [End]...
            $data['page_view_type'] = 'public_account';
            
            $i_profile_id = intval($id);
            $data['profile_id']=$i_profile_id;
            
            $public_arr_profile_info = $this->users_model->fetch_this($i_profile_id);
            $data['public_arr_profile_info'] = $public_arr_profile_info;
         	
			
			
            
            ## albums ##
            ob_start();
            $this->album_pagination_show_more($i_profile_id);
            $content = ob_get_contents();
            $content_obj = json_decode($content);
            $data['result_album_content'] = $content_obj->html; 
            ob_end_clean();
            
            
            ## photos ##
            ob_start();
            $this->video_pagination_show_more($i_profile_id);
            $content = ob_get_contents();
            $content_obj = json_decode($content);
            $data['result_video_content'] = $content_obj->html; 
            ob_end_clean();
           
            
            # view file...
            $VIEW_PG_FILE = "public_profile/public_profile_view_all_video/all-videos.phtml";
            $VIEW = "{$VIEW_PG_FILE}";
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           show_error($err_obj->getMessage());
        }
    }   //index
    
    
    public function newsfeed_pagination_show_more($i_profile_id , $page=0)  
    {
        
        $cur_page = $page + $this->pagination_per_page;
        $data = $this->data;
        
        # echo $page ."--". $this->pagination_per_page ."<br />";
        $result = $this->data_newsfeed_model->get_newsfeeds_by_user_id($i_profile_id, $page, $this->pagination_per_page );
        //echo $this->db->last_query();
        $total_rows = $this->data_newsfeed_model->get_total_newsfeeds_by_user_id($i_profile_id);
        //pr($result,1);
        $data['result_arr'] = $result;
        $data['no_of_result'] = $total_rows;
        //$data['current_page_1'] = $page;
        $data['current_page_1'] = $cur_page;
        $data['profile_id'] = $i_profile_id;
        
        $VIEW_FILE = "newsfeed/public_profile_feeds.phtml";
        
        if( is_array($result) && count($result) ) {
            $content = $this->load->view( $VIEW_FILE , $data, true);
        }
        else {
            #$content = '<div class="txt_content01"><p style="margin-left: 330px;">No Posts!</p></div>';
            $content = '';
        }
        
        //echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$content, 'current_page'=>$cur_page) );
    }
    
    
    
    
    public function album_pagination_show_more($i_profile_id , $page=0)  
    {
        
        $cur_page = $page + $this->album_pagination_per_page;

        
        $data = $this->data;
        
        $this->load->model('my_videos_model');
        $result = $this->my_videos_model->get_all_video_albums_with_privacy_and_count($i_profile_id,$page,$this->album_pagination_per_page);
                
        $total_rows = $this->my_videos_model->get_total_no_of_albums_with_privacy($i_profile_id);
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
        
        $VIEW_FILE = "public_profile/public_profile_view_all_video/video_albums_ajax.phtml";
        
        if( is_array($result) && count($result) ) {
            $content = $this->load->view( $VIEW_FILE , $data, true);
        }
        else {
            $content = '<div class="shade_box_blue no_comments" style="padding-top:5px; "><div  class="shade_norecords" style="width:375px;"><p class="blue_bold12">No Video Album.</p></div></div>';
        }
        
        echo json_encode( array('html'=>$content, 'current_page'=>$cur_page,'view_more'=>$view_more) );
    }
    
    
    public function video_pagination_show_more($i_profile_id , $page=0)  
    {
        
        $cur_page = $page + $this->video_pagination_per_page;
        
        
        $data = $this->data;
        
        $this->load->model('my_videos_model');
        $result = $this->my_videos_model->get_allvideos_with_comments_by_user_id_($i_profile_id,'', $page , $this->video_pagination_per_page);
                
        $total_rows = $this->my_videos_model->get_count_allvideos_with_comments_by_user_id_($i_profile_id,'');
        //pr($result,1);
        $data['video_result_arr'] = $result;
        $data['video_no_of_result'] = $total_rows;
        $data['video_current_page'] = $cur_page;
		$data['profile_id']=$i_profile_id;    
    
     
		//--- for check whether more videos are there or not
				$view_more = true;
				 $rest_counter = $total_rows-$page;
				 if($rest_counter<=$this->video_pagination_per_page)
					$view_more = false;
		//--------- end check
    
        
        $VIEW_FILE = "public_profile/public_profile_view_all_video/videos_ajax.phtml";
        
        if( is_array($result) && count($result) ) {
            $content = $this->load->view( $VIEW_FILE , $data, true);
        }
        else {
            $content = '<div class="shade_box_blue no_comments" style="padding-top:5px; "><div  class="shade_norecords" style="width:360px;"><p class="blue_bold12">No Video.</p></div></div>';
        }
        
        echo json_encode( array('html'=>$content, 'current_page'=>$cur_page,'view_more'=>$view_more) );
    }
    
    
    
    
    
    
    
    
    
    
    
    
        //=============================================== play media file ===========================================
    public function get_media()
       {
          try
              {
              
                    $i_media_id = intval($this->input->post('media_id'));
                    $width = intval($this->input->post('width'))<=0?'122':intval($this->input->post('width'));
                    $height = intval($this->input->post('height'))<=0?'82':intval($this->input->post('height'));
                    
                    
                   $media_info = $this->my_videos_model->get_video_by_id($i_media_id);
              #echo utf8_accents_to_ascii($media_info['s_video_url']);
    
                    if($media_info == '') {
                        echo json_encode( array('result'=>'error') );
                        exit;
                    }
            
                    //$this->data['current_media_id'] = $i_media_id;
    
            /* ******************** Get photo details ************************ */
            
        
            
            try {
                    $this->load->library('embed_video');
                    $config['zend_library_path'] = APPPATH ."libraries/Zend/";
                    $config['video_url'] =  $media_info;
                    
                    $this->embed_video->initialize($config);
                    $this->embed_video->prepare();
    
                    
                    $image_source = $this->embed_video->get_player($width,$height);
                }
                catch(Exception $e) {
                    //$data['video_exists'] = false;
                    $image_source = 'This video has been deleted.';
                }
            
    
            $result_arr['result'] = 'success';
            $result_arr['s_image_source'] = $image_source;        
                        
            $result_arr['i_media_id'] = $i_media_id;
            //pr($result_arr);
    
            echo json_encode($result_arr );
                
                
                
              } 
          catch(Exception $err_obj)
              {
                
              } 
        
       }
    //=============================================== end play media file ===========================================
}
?>
