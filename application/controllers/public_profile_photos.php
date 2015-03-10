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

class Public_profile_photos extends Base_controller
{
    private $pagination_per_page = 5;
    private $album_pagination_per_page = 10;
    private $photo_pagination_per_page = 16;
    
    
    public function __construct()
     {
        try
        {
            parent::__construct();
			
            $this->upload_tmp_path = BASEPATH.'../uploads/wall_tmp/';
            $this->upload_path  = BASEPATH.'../uploads/wall_photos/';
               $this->upload_photo_path    = BASEPATH.'../uploads/wall_photos/';
            # loading reqired model & helpers...
            $this->load->helper('wall_helper');
            $this->load->model('users_model');
            $this->load->model('netpals_model');
            $this->load->model('my_prayer_partner_model');
            $this->load->model('data_newsfeed_model');
            $this->load->model('newsfeed_comments_model');
            $this->load->model('user_photos_model');
            $this->load->model('photo_albums_model');
			$this->load->model('user_notifications_model');
			$this->load->model('user_alert_model');
			$this->load->model('media_comments_model');
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }
    
    
    
    
        # index function definition...
    public function index($user_id='', $album_id ='') 
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
                                        'js/production/public_profile.js',
                                        
                                        'js/production/my_friends.js',
                                        'js/production/my_net_pals.js',
                                        'js/production/my_prayer_partner.js',
                                        'js/production/my_message.js',
										'js/production/tweet_utilities.js'
                                        ));
                                        
           // parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css') );
                                        
                                        
            # adjusting header & footer sections [End]...
            $data['page_view_type'] = 'public_account';
            
            $i_profile_id = intval($user_id);
            
            $public_arr_profile_info = $this->users_model->fetch_this($i_profile_id);
            $data['public_arr_profile_info'] = $public_arr_profile_info;
            $data['album_id'] = $album_id;
			
			$data['album_detail'] =  $this->photo_albums_model->get_by_id($album_id);
            
            
            $data['photo_pagination_per_page'] = $this->photo_pagination_per_page;
          
            
            ## photos ##
            ob_start();
            $this->photo_pagination_show_more($i_profile_id , $album_id);
            $content = ob_get_contents();
            $content_obj = json_decode($content);
            $data['result_photo_content'] = $content_obj->html; 
            ob_end_clean();
        
            
            
            # view file...
            $VIEW_PG_FILE = "public_profile/public_profile_view_all_photo/photo-album-details.phtml";
            $VIEW = "{$VIEW_PG_FILE}";
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           show_error($err_obj->getMessage());
        }
    }   //index
    
  
   ########################################################################################################
   
    public function photo_pagination_show_more($i_profile_id, $album_id , $page=0)  
    {
        
        $cur_page = $page + $this->photo_pagination_per_page;
        
        
        $data = $this->data;
        
        $this->load->model('user_photos_model');
        
		$s_where = "AND i_photo_album_id = {$album_id}";
        $result = $this->user_photos_model->get_allphotos_with_comments_by_user_id_($i_profile_id,$s_where,$page , $this->photo_pagination_per_page);
        
        $total_rows = $this->user_photos_model->get_total_allphotos_with_comments_by_user_id_($i_profile_id, $s_where);
		
        $data['photo_result_arr'] = $result;
        $data['photo_no_of_result'] = $total_rows;
        $data['photo_current_page'] = $cur_page;
    	$data['profile_id'] = $i_profile_id;
		$data['album_id'] = $album_id;
    
    
		//--- for check whether more videos are there or not
				$view_more = true;
				 $rest_counter = $total_rows-$page;
				 if($rest_counter<=$this->photo_pagination_per_page)
					$view_more = false;
		 //--------- end check
    
        
        $VIEW_FILE = "public_profile/public_profile_view_all_photo/all_photos_album_wise_ajax.phtml";
        
        if( is_array($result) && count($result) ) {
            $content = $this->load->view( $VIEW_FILE , $data, true);
        }
        else {
            $content = '';
        }
        
        echo json_encode( array('html'=>$content, 'current_page'=>$cur_page,'view_more'=>$view_more) );
    }
	
	public function photo_detail($user_id='', $photo_id ='') 
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
                                        'js/jquery.lightbox.js',
										'js/stepcarousel.js',*/
                                        'js/production/my_friends.js',
                                        'js/production/my_net_pals.js',
                                        'js/production/my_prayer_partner.js',
                                        'js/production/my_message.js',
										'js/production/tweet_utilities.js',
										'js/production/photo_helper.js',
										'js/production/photo_details.js',
                                        ));
                                        
          //  parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css') );
                                        

            # adjusting header & footer sections [End]...
            $data['page_view_type'] = 'public_account';
            
            $i_profile_id = intval($user_id);
            
            $public_arr_profile_info = $this->users_model->fetch_this($i_profile_id);
            $data['public_arr_profile_info'] = $public_arr_profile_info;
			
			$data['photo_detail'] =  $this->user_photos_model->get_by_id($photo_id);
            $data['album_id']     = $data['photo_detail']['i_photo_album_id'];
			$data['album_detail'] =  $this->photo_albums_model->get_by_id($data['album_id']);
			
			$data['pagination_per_page'] = $this->pagination_per_page;
			$data['photo_id'] = $photo_id;
			
			### copy image 
			$i_logged_user_id = intval(decrypt($this->session->userdata('user_id')));
			parent::_set_all_photo_album_data($i_logged_user_id);
			

			ob_start();
			$this->comments_ajax_pagination($photo_id);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['comments_list_html'] = $content_obj->html; 
			$data['no_of_result']  = $content_obj->no_of_result;
			$data['current_page_1'] = $content_obj->current_page;
			ob_end_clean();
            
			//pr($data['photo_detail']);
            # view file...
            $VIEW_PG_FILE = "public_profile/public_profile_view_all_photo/photo-details.phtml";
            $VIEW = "{$VIEW_PG_FILE}";
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           show_error($err_obj->getMessage());
        }
    }   //index
    
	
	
	public function comments_ajax_pagination($i_media_id, $page=0)
    {
		
		
		$cur_page = $page + $this->pagination_per_page;
		$data = $this->data;
		
		$result = $this->media_comments_model->get_by_newsfeed_id($i_media_id , 'photo',$page,
																$this->pagination_per_page);
		$total_rows = $this->media_comments_model->get_total_by_newsfeed_id($i_media_id, 'photo');
		
		$data['result_arr'] = $result;
		$data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
		$data['photo_id'] = $i_media_id;
		
		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->pagination_per_page)
			  $view_more = false;
         //--------- end check
		
		
		$VIEW_FILE = "public_profile/public_profile_view_all_photo/photo_comment_ajax_list.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
        echo json_encode( array('html'=>$content, 'current_page'=>$cur_page, 'no_of_result'=>$data['no_of_result'],'total'=>$total_rows,'view_more'=>$view_more  ,'cur_page'=>$data['current_page_1']) );
			
	}
	
	public function copyImage($id,$current_album_id){
		
		   if($current_album_id == '-1'){
			   
			   echo json_encode( array('success'=>false , 'msg'=>'Please select a album to copy.') );
			   exit;
		   }
		   else
		   {
				$user_id = intval(decrypt($this->session->userdata('user_id')));
				
				$src_path = BASEPATH.'../uploads/user_photos/';
				$upload_path = BASEPATH.'../uploads/user_photos/';
				
				$this->load->model('user_photos_model');
				$photo_details = $this->user_photos_model->get_by_id($id);
				#pr($photo_details ,1);
				
				if( !is_array($photo_details) || !count($photo_details) 
						|| @$photo_details['s_photo']=='' ) {
						
					echo json_encode( array('success'=>false, 'msg'=>'Invalid request') );
					exit;
				}
				
				$ext_arr = get_ext($photo_details['s_photo']);
				$ext = $ext_arr['ext'];
				$filename = $ext_arr['filename'];
	
				if(test_file($upload_path.$filename.'-big.'.$ext)) {
					for( $i=0; test_file($upload_path.$filename.'-'.$i.'-big.'.$ext); $i++ ) {
					}
	
					$new_imagename = $filename.'-'.$i;
				}
				else {
					$new_imagename = $filename;
				}
	
				$this->imagename = $new_imagename;
	
				$this->upload_image = $upload_path.$new_imagename.'.'.$ext;
				//echo $upload_path; exit;
	
				$copied = @copy($src_path.getThumbName($photo_details['s_photo'], 'big'), $this->upload_image);
				
				//echo $src_path.$photo_details['s_photo'].'###'.$this->upload_image;
				
				if(!$copied) {
					echo json_encode( array('success'=>false, 'msg'=>'Some error occurred. Try after sometime.') );
					exit;
				}
				
				# @@@@@@@@@@@@ NEW RESIZING PART [BEGIN] @@@@@@@@@@@
					$config = array();
					$config['source_image'] = $this->upload_image;
					$config['thumb_marker'] = '-thumb';
					$config['crop'] = false;
					$config['width'] = 122;
					$config['height'] = 82;
					$config1['crop_from'] = 'middle';
					$config['within_rectangle'] = true;
					$config['small_image_resize'] = 'no_resize';
					resize_exact($config);
					unset($config);
					
					
					$config = array();
					$config['source_image'] = $this->upload_image;
					$config['thumb_marker'] = '-mini';
					$config['crop'] = false;
					$config['width'] = 73;
					$config['height'] = 73;
					$config1['crop_from'] = 'middle';
					$config['within_rectangle'] = true;
					$config['small_image_resize'] = 'no_resize';
					resize_exact($config);
					unset($config);
					
					$config = array();
					$config['source_image'] = $this->upload_image;
					$config['thumb_marker'] = '-mid';
					#$config['crop'] = false;
					$config['width'] = 472;
					$config['height'] = 378;
					$config['within_rectangle'] =true;
					$config['small_image_resize'] = 'no_resize';
					resize_exact($config);
					unset($config);
					
					$config = array();
					$config['source_image'] = $this->upload_image;
					$config['thumb_marker'] = '-main';
					#$config['crop'] = false;
					$config['width'] = 400;
					$config['height'] = 327;
					$config['within_rectangle'] =true;
					$config['small_image_resize'] = 'no_resize';
					resize_exact($config);
					unset($config);
					
					$config = array();
					$config['source_image'] = $this->upload_image;
					$config['thumb_marker'] = '-big';
					#$config['crop'] = false;
					$config['width'] = 800;
					$config['height'] = 536;
					$config['within_rectangle'] =true;
					$config['small_image_resize'] = 'no_resize';
					resize_exact($config);
					unset($config);
					
				   # @@@@@@@@@@@@ NEW RESIZING PART [END] @@@@@@@@@@@
	  
				 ### insert in user photo db.    
			   
				$info['i_user_id'] = intval(decrypt($this->session->userdata('user_id')));
				$info['s_title'] = $photo_details['s_title']; 
				$info['s_description'] = $photo_details['s_description']; 
				$info['s_photo'] 	= $new_imagename.'.'.$ext;
				$info["i_order"] =  1+$this->photo_albums_model->get_i_order($current_album_id);		
				$info['dt_created_on'] = get_db_datetime();
				$info['i_photo_album_id'] = $current_album_id;
				
				/*$copy_info['user_id'] = $photo_details['i_user_id'];
				$copy_info['user_id'] = $photo_details['i_user_id'];*/
				
				$info['s_copied_photo'] = $photo_details['i_user_id'];
				
				$_ret = $this->user_photos_model->insert($info);
				//echo $this->db->last_query();
				$i_total = $this->user_photos_model->get_total_by_user_id($info['i_user_id']);
				
				echo json_encode( array('success'=>true , 'msg'=>'Photo copied successfully.') );
		   }
	    
	}
}
?>