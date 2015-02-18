<?php
/*********
* Author: 
* Purpose:
*  Controller For "advertisements"
* 
* @package 
* @subpackage 
* 
* @link InfController.php 
* @link Base_Controller.php
* @link model/ring_categories_model.php
* @link views/##
*/

class Edit_audio extends Admin_base_Controller
{
   
   
    
   
    // constructor definition...
    function __construct()
    {
        try
        {
            parent::__construct();
            parent::_check_admin_login();
            
            # configuring paths...
                        
            # loading reqired model & helpers...
            // $this->load->helper('###');
           //$this->load->model("ring_categories_model");
           
           $this->logged_admin_id = $this->session->userdata('loggedin');
            
            
           $this->load->model("landing_page_cms_model");
           $this->load->helper('common_option_helper.php');
           
           
           $this->upload_path = BASEPATH.'../uploads/media_center_videos/';
           $this->upload_path_featured_home = BASEPATH.'../uploads/media_center_listen_featured_home/'; 
			
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    // "index" function definition...
    public function index($current_page,$video_id) 
    {

        try
        {
            
            # adjusting header & footer sections [Start]...
            $data = $this->data;
            parent::_set_title("::: COGTIME Xtian network :::");
            parent::_set_meta_desc("::: COGTIME Xtian network :::");
            parent::_set_meta_keywords("::: COGTIME Xtian network :::");
            parent::_add_js_arr( array( 'js/lightbox.js',
                                        'js/jquery.dd.js',
                                        'js/jquery.form.js',
                                        'js/jquery/JSON/json2.js',
                                        
                                        'tiny_mce/plugins/tinybrowser/tb_tinymce.js.php',
                                        'tiny_mce/tiny_mce.js'
                                        ) );
                                        
             parent::_add_css_arr( array('css/dd.css'
                                        ) );
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 5;
            $data['submenu'] = 4;
            
            $data['current_page'] = $current_page;
            
            $data['all_cat'] = $this->landing_page_cms_model->fetch_all_audio_categories();
            $data['contents'] = $this->landing_page_cms_model->get_contents_by_audio_id($video_id);
            
            $this->session->set_userdata('current_page',$current_page);
            # rendering the view file...
            $VIEW_FILE = "admin/media_center/edit_audio.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }//end of index
    
    
    
    
    
    //------------------------------------ edit video ---------------------------------------------
   function post_add_data()
    {
        
        $error = 0;
        $sys_error=0;
        $info=array();
        $key=array();
        $info['dt_updated_on'] = get_db_datetime();
        $video_id = $this->input->post('i_video_id');
        
            $info['i_posted_by'] = $this->logged_admin_id;
            
           // $info['s_title'] = $this->input->post('txt_title');
           // $info['s_desc'] = $this->input->post('txtarea_desc');
           // $info['s_url'] = 0;
             $info['playlist_name'] = $this->input->post('audio_list');
            $info['track_id'] = $this->input->post('track-name');
            
            $info['i_category_id'] = $this->input->post('category');
            $info['s_tags'] = $this->input->post('tags');
            $info['is_featured'] = $this->input->post('is_featured');
            $current_page = $this->input->post('current_page');
            
			$info['i_is_feature_home'] = $this->input->post('is_feature_home');
                          $query = $this->db->get('cg_site_settings');
foreach ($query->result() as $row)
{
    $client_id = $row->client_id;
    $api_user_id = $row->api_user_id;

  $tracks_json = file_get_contents('http://api.soundcloud.com/tracks/'.$info['track_id'].'.json?client_id='.$client_id.'');
$tracks = json_decode($tracks_json);
$info['s_title'] = $tracks->title;
$info['s_desc'] = $tracks->description;

}
            
//            if($info['s_title']=='')
//            {
//                $arr_messages['title']='* Required field';
//                
//                $error = 1;
//            }
//            if($info['s_desc']=='')
//            {
//                $arr_messages['desc'] = '* Required field';
//                $error=1;
//            }
           if( $info['playlist_name']=='')
            {
                $arr_messages['track'] = '* Required field';
                $error=1;
            }
//             if($info['i_is_feature_home'] == 1 && $_FILES['adv_image_featured']['name']=='' ){
//              $arr_messages['adv_image'] = '* Required field';
//               $error=1;
//            }
            
            
            if($error!=1)  
            {
                $info['s_video_thumb'] = '' ;
				if($info['is_featured']==1)
				{
					if($_FILES['adv_image']['name']!='')
					{
						$info["s_image"] = $this->_upload_profile_image(trim($_FILES['adv_image']['name']) ,'adv_image');
					}
					$info["i_user_type"] = intval(($this->session->userdata('user_type')));
				}
				else
				{
					$news_info = $this->landing_page_cms_model->get_contents_by_audio_id($video_id);
					unlink($this->upload_path. getThumbName($news_info['s_image'], 'thumb'));
					unlink($this->upload_path. getThumbName($news_info['s_image'], 'mid_FO'));
					unlink($this->upload_path. getThumbName($news_info['s_image'], 'mid'));
					unlink($this->upload_path. getThumbName($news_info['s_image'], 'main'));
				}
				
				
				if($_FILES['adv_image_featured']['name']!='')
				{
					$news_info_featured = $this->landing_page_cms_model->get_contents_by_audio_id($video_id);
					unlink($this->upload_path_featured_home. getThumbName($news_info_featured['s_image_featured'], 'thumb'));
					unlink($this->upload_path_featured_home. getThumbName($news_info_featured['s_image_featured'], 'mid_FO'));
					unlink($this->upload_path_featured_home. getThumbName($news_info_featured['s_image_featured'], 'mid'));
					unlink($this->upload_path_featured_home. getThumbName($news_info_featured['s_image_featured'], 'main'));
					$info["s_image_featured"] = $this->_upload_featured_image(trim($_FILES['adv_image_featured']['name']) ,'adv_image_featured');
					$info['i_is_feature_home'] = 1;
				}
				
				$this->landing_page_cms_model->update_audio_all($video_id);
				$res = $this->landing_page_cms_model->update_audio($info,$video_id);
            }
            
            else
            {
                $result = 'failure';
                $msg    = 'Field(s) can not be blank!';
                echo json_encode(array('result'=>$result,'msg'=>$msg,'arr_messages'=>$arr_messages));
                exit;
            }        
            
            
            
            $result = 'success';
            $msg = 'Video updated successfully.';
            
            

            //$html = $this->video_listing_AJAX();

        echo json_encode(array('result'=>$result,'msg'=>$msg));
    }
    
    
    
    
    
    
        //=============================================== play media file ===========================================
        public function get_media()
       {
          try
              {
              
                    $i_media_id = intval($this->input->post('media_id'));
                    $width = intval($this->input->post('width'))<=0?'200':intval($this->input->post('width'));
                    $height = intval($this->input->post('height'))<=0?'150':intval($this->input->post('height'));
                    
                    
                   $media_info = $this->landing_page_cms_model->get_all_videos(" where v.id={$i_media_id}");
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
                    $config['video_url'] =  $media_info[0]['s_url'];
                    
                    $this->embed_video->initialize($config);
                    $this->embed_video->prepare();
    
                    
                    $image_source = $this->embed_video->get_player($width,$height);
                }
                catch(Exception $e) {
                    //$data['video_exists'] = false;
                    $image_source = 'This video has been deleted.';
                }
            


//pr($media_info);


            $result_arr['result'] = 'success';
            $result_arr['s_image_source'] = $image_source;        
                        
            $result_arr['i_media_id'] = $i_media_id;
            
            $result_arr['title'] = $media_info[0]['s_title'];
            $result_arr['desc'] = $media_info[0]['s_desc'];
            $result_arr['posted_on'] = $media_info[0]['dt_posted_on'];
            $result_arr['category'] = $media_info[0]['cat_name'];
            
            
            
            //pr($result_arr);
    
            echo json_encode($result_arr );
                
                
                
              } 
          catch(Exception $err_obj)
              {
                
              } 
        
       }
    //=============================================== end play media file ===========================================
    

    
     public function _upload_profile_image($prev_img = '', $fileElementName)
      {
      
	  // pr($_FILES);
	    //$fileElementName = 'adv_image';	 
        if(!empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] != '') 
		{
				preg_match('/(^.*)\.([^\.]*)$/', $_FILES[$fileElementName]['name'], $matches);
				$ext = "";
				if(count($matches)>0) {
					$ext = strtolower($matches[2]);
					$original_name = $matches[1];
				}
				else
					$original_name = 'image';

			
					 $imagename = createImageName( $original_name ); 

					if(test_file($this->upload_path.$imagename.'-thumb.'.$ext)) {
						for( $i=0; test_file($this->upload_path.$imagename.'-'.$i.'-thumb.'.$ext); $i++ ) {
						}

						$new_imagename = $imagename.'-'.$i;
					}
					else {
						$new_imagename = $imagename;
					}

					$this->imagename = $new_imagename;

					$this->upload_image = $this->upload_path.$new_imagename.'.'.$ext;
					//echo $this->upload_path; exit;
					
					@move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $this->upload_image);
					
					
					$config = array();

					$config['source_image'] = $this->upload_image;
					$config['thumb_marker'] = '-thumb';
					$config['crop'] = false;
					$config['width'] = 98;
					$config['height'] = 75;
					$config1['crop_from'] = 'middle';
                    #$config['within_rectangle'] = true;
                    $config['small_image_resize'] = 'no_resize';
					resize_exact($config);
                    
					unset($config);
					
					
					$config = array();

					$config['source_image'] = $this->upload_image;
					$config['thumb_marker'] = '-mid';
					$config['crop'] = false;
					$config['width'] = 348;
					$config['height'] = 195;
                    $config1['crop_from'] = 'middle';
                    #$config['within_rectangle'] = true;
                    $config['small_image_resize'] = 'inside';
					resize_exact($config);
					
					
					
					
					$config = array();

					$config['source_image'] = $this->upload_image;
					$config['thumb_marker'] = '-mid_FO';
					$config['crop'] = false;
					$config['width'] = 728;
					$config['height'] = 379;
                    $config1['crop_from'] = 'middle';
                    #$config['within_rectangle'] = true;
                    $config['small_image_resize'] = 'inside';
					resize_exact($config);
					
					
					$config = array();

					$config['source_image'] = $this->upload_image;
					$config['thumb_marker'] = '-main';
					$config['crop'] = false;
					$config['width'] = 999;
					$config['height'] = 408;
                    $config1['crop_from'] = 'middle';
                    #$config['within_rectangle'] = true;
                    $config['small_image_resize'] = 'inside';
					resize_exact($config);
					
					$config = array();
					
					$config['source_image'] = $this->upload_image;
					$config['thumb_marker'] = '-big';
					$config['crop'] = false;
					$config['width'] = 800;
					$config['height'] = 536;
                   // $config1['crop_from'] = 'middle';
                    $config['within_rectangle'] = true;
                    $config['small_image_resize'] = 'no_resize';
					resize_exact($config);

                
					
					$this->s_picture_path = $new_imagename.'.'.$ext;
					//echo $this->upload_image; 
					
					@unlink($this->upload_image); //Unlink the original image........
					//@unlink($this->upload_path.getThumbName($prev_img,'thumb')); //Unlink the prevoius image........
					//exit;
					return $this->s_picture_path;

				
			
					
				}
        else
        {
            return $prev_img; // Unchaged previous image
        }
    
   } 
   
   
   public function _upload_featured_image($prev_img = '', $fileElementName)
      {
      
	  // pr($_FILES);
	    //$fileElementName = 'adv_image';	 
        if(!empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] != '') 
		{
				preg_match('/(^.*)\.([^\.]*)$/', $_FILES[$fileElementName]['name'], $matches);
				$ext = "";
				if(count($matches)>0) {
					$ext = strtolower($matches[2]);
					$original_name = $matches[1];
				}
				else
					$original_name = 'image';

			
					 $imagename = 'artical';//createImageName( $original_name ); 

					if(test_file($this->upload_path_featured_home.$imagename.'-thumb.'.$ext)) {
						for( $i=0; test_file($this->upload_path_featured_home.$imagename.'-'.$i.'-thumb.'.$ext); $i++ ) {
						}

						$new_imagename = $imagename;
					}
					else {
						$new_imagename = $imagename;
					}

					$this->imagename = $new_imagename;

					$this->upload_image = $this->upload_path_featured_home.$new_imagename.'.'.$ext;
					//echo $this->upload_path_featured_home; exit;
					
					@move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $this->upload_image);
					
					
					$config = array();

					$config['source_image'] = $this->upload_image;
					$config['thumb_marker'] = '-smallthumb';
					$config['crop'] = false;
					$config['width'] = 75;
					$config['height'] = 53;
					$config1['crop_from'] = 'middle';
                    #$config['within_rectangle'] = true;
                    $config['small_image_resize'] = 'no_resize';
					resize_exact($config);
                    
					unset($config);
					
					
					$config = array();

					$config['source_image'] = $this->upload_image;
					$config['thumb_marker'] = '-thumb';
					$config['crop'] = false;
					$config['width'] = 98;
					$config['height'] = 75;
					$config1['crop_from'] = 'middle';
                    #$config['within_rectangle'] = true;
                    $config['small_image_resize'] = 'no_resize';
					resize_exact($config);
					
					
					$config = array();

					$config['source_image'] = $this->upload_image;
					$config['thumb_marker'] = '-main';
					$config['crop'] = false;
					$config['width'] = 999;
					$config['height'] = 408;
                    $config1['crop_from'] = 'middle';
                    #$config['within_rectangle'] = true;
                    $config['small_image_resize'] = 'inside';
					resize_exact($config);
					
					
					$this->s_picture_path = $new_imagename.'.'.$ext;
					//echo $this->upload_image; 
					
					@unlink($this->upload_image); //Unlink the original image........
					//@unlink($this->upload_path_featured_home.getThumbName($prev_img,'thumb')); //Unlink the prevoius image........
					//exit;
					return $this->s_picture_path;

				}
        else
        {
            return $prev_img; // Unchaged previous image
        }
        
        
    } 
   
    
}// end of controller