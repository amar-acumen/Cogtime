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

class Add_new_videos extends Admin_base_Controller
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
           
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    // "index" function definition...
    public function index() 
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
            $data['submenu'] = 3;
            
            
            
            $data['all_cat'] = $this->landing_page_cms_model->fetch_all_categories();
            
            
            # rendering the view file...
            $VIEW_FILE = "admin/media_center/add_new_videos.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }//end of index
    
    
    
    
    
    //------------------------------------ add new video ---------------------------------------------
    function post_add_data()
    {
        
        $error = 0;
        $sys_error=0;
        $info=array();
        $key=array();
        $info['dt_posted_on'] = get_db_datetime();
        
            $info['i_posted_by'] = $this->logged_admin_id;
            
            $info['s_title'] = $this->input->post('txt_title');
            $info['s_desc'] = $this->input->post('txtarea_desc');
            $info['s_url'] = $this->input->post('txt_url');
            
            $info['i_category_id'] = $this->input->post('category');
			$info['s_tags'] = $this->input->post('tags');
			$info['is_featured'] = $this->input->post('is_featured');
                       $info['i_is_feature_home'] =  $this->input->post('is_feature_home');
            $adv_image_featured = $this->input->post('adv_image_featured');
                         $is_feature_home = $this->input->post('is_feature_home');
            
            
            if($info['s_title']=='')
            {
                $arr_messages['title']='* Required field';
                
                $error = 1;
            }
            if($info['s_desc']=='')
            {
                $arr_messages['desc'] = '* Required field';
                $error=1;
            }
            if($info['s_url']=='')
            {
                $arr_messages['url'] = '* Required field';
                $error=1;
            }
//            if($is_feature_home == 1 && $adv_image_featured == ''){
//                 $arr_messages['adv_image_featured'] = '* Required field';
//                $error=1;
//            }
            else
            {
                                try {
                                  
                                      $this->load->library('embed_video');
                                      $config['zend_library_path'] = APPPATH ."libraries/Zend/";
                                      $config['video_url'] = trim($this->input->post('txt_url'));
                                      
                                      $this->embed_video->initialize($config);
                              //echo 'video_url='.$this->embed_video->video_url;
                                      $this->embed_video->prepare();

//echo "duration : ".$this->embed_video->get_video_duration();exit;                                      
                                      
                                      $info['t_duration'] = $this->embed_video->get_video_duration();

                                        
                                        
                                      $this->embed_video->save_thumb($this->upload_path, '-bigthumb', 220, 156);
									  $this->embed_video->save_thumb($this->upload_path, '-midthumb', 164, 114);
									  $this->embed_video->save_thumb($this->upload_path, '-thumb', 104, 104);
                                      $this->video_img_name = $this->embed_video->get_resized_imagename();
                                      
                              
                                      
                                  }
                                  catch(Exception $e) 
                                  {
									  //echo $e.' == ';
                                      $arr_messages['url'] = "* Not valid video URL";
                                      $error_invalid_url = 1;
                                  }
            }
            
            
           
            
            if($error_invalid_url==1)
            {
                $result = 'failure';
                $msg    = 'Please enter a valid URL.';
                echo json_encode(array('result'=>$result,'msg'=>$msg,'arr_messages'=>$arr_messages));
                exit;
            }
            if($error!=1)  
            {
                $info['s_video_thumb'] = $this->video_img_name ;
				if($info['is_featured']==1)
				{
					$info["s_image"] = $this->_upload_profile_image(trim($_FILES['adv_image']['name']) ,'adv_image');
					$info["i_user_type"] = intval(($this->session->userdata('user_type')));
				}
                $res = $this->landing_page_cms_model->add_new_video($info);
                if(!$res)
                {
                    $sys_error=1;
                }
            }
            
            else
            {
                $result = 'failure';
                $msg    = 'Field(s) can not be blank!';
                echo json_encode(array('result'=>$result,'msg'=>$msg,'arr_messages'=>$arr_messages));
                exit;
            }        
            if($sys_error==1)
            {
                $result = 'failure';
                $msg    = 'Error occured! Try again.';
                echo json_encode(array('result'=>$result,'msg'=>$msg));
                exit;
            }    
            
            
            $result = 'success';
            $msg = 'Video added successfully.';
            

            //$html = $this->video_listing_AJAX();

        echo json_encode(array('result'=>$result,'msg'=>$msg));
    }

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
					$config['thumb_marker'] = '-sliderthumb';
					$config['crop'] = false;
					$config['width'] = 127;
					$config['height'] = 100;
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
    
    
}// end of controller