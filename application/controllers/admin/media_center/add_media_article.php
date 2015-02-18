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

class Add_media_article extends Admin_base_Controller
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
           
           
           $this->upload_path = BASEPATH.'../uploads/media_center_article/';
           $this->upload_path_featured_home = BASEPATH.'../uploads/media_center_article_featured_home/'; 
		   
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
                                        'tiny_mce/tiny_mce.js',
                                        'js/backend/cms/christian_news_tiny_mce.js'
                                        
                                        ) );
                                        
             parent::_add_css_arr( array('css/dd.css'
                                        ) );
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 5;
            $data['submenu'] = 1;
            
            
            $data['categories'] = $this->landing_page_cms_model->get_all_news_cat();
            
            
            $this->session->set_userdata('current_page','');
           
            
            
            # rendering the view file...
            $VIEW_FILE = "admin/media_center/add_christian_news.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }//end of index
    
    
    //------------------------------------ add new news ---------------------------------------------
    function post_add_data()
    {
        $where = " WHERE 1";
        $error = 0;
        $sys_error=0;
        $info=array();
        $key=array();
        $data['i_posted_by'] = $this->logged_admin_id;
		$info['i_posted_by'] = $this->logged_admin_id;
		
		$articleinsert	 = $this->input->post('articleinsert');
		$rssurl			 = $this->input->post('rsslink');
		$info['i_category'] = intval($this->input->post('category'));
		if($articleinsert == 'rss')    
		{
			$return_arr = $this->landing_page_cms_model->get_rss_feed($rssurl);
			foreach($return_arr as $k=>$val)
			{
			$inf[$k] = array_merge($info,$val);
			}
		}
		else
		{
			$info['s_title'] = get_formatted_string($this->input->post('txt_title'));
			$info['s_desc'] = get_formatted_string($this->input->post('txtarea_desc'));
			$info['i_is_top_story'] = $this->input->post('top_story');
			$info['s_tags'] = $this->input->post('tags'); 
			$info['is_rss']		= 0;			
            
			$info['dt_posted_on'] = get_db_datetime();
        	
			$info['i_is_feature_home'] = $this->input->post('is_feature_home');
                          $feature = $this->input->post('top_story');
			
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
			if($info['s_desc']=='')
            {
                $arr_messages['desc'] = '* Required field';
                $error=1;
            }
           	
			if($_FILES['adv_image']['name']=='' && $feature == '1')
            {
                $arr_messages['adv_image'] = '* Required field';
                $error=1;
            }
            if($info['i_is_feature_home'] == '1' && $_FILES['adv_image_featured']['name'] == ''){
                $arr_messages['adv_image_featured'] = '* Required field';
                $error=1;
            }
            
        }   
           
            
           
            if($error!=1)  
            {
//pr($info,1);
				if($_FILES['adv_image']['name']!='')
					$info["s_image"] = $this->_upload_profile_image(trim($_FILES['adv_image']['name']) ,'adv_image');
				
				if($_FILES['adv_image_featured']['name']!='')
					$info["s_image_featured"] = $this->_upload_featured_image(trim($_FILES['adv_image_featured']['name']) ,'adv_image_featured');
				
				if($articleinsert != 'rss')
				{
				//echo $info['is_rss'].'1';exit;
				$res = $this->landing_page_cms_model->add_new_news($info);
				$this->landing_page_cms_model->update_news_all($res);
				}
				else
				{
					//echo $info['is_rss'].'2';exit;
					foreach($inf as $val){
					
				$res = $this->landing_page_cms_model->add_new_news($val);
				$this->landing_page_cms_model->update_news_all($res);
					}
					
				}
				
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
            $msg = 'Article added successfully.';
            

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
                    
					unset($config);
					
					$config = array();

					$config['source_image'] = $this->upload_image;
					$config['thumb_marker'] = '-thumb_1';
					$config['crop'] = false;
					$config['width'] = 200;
					$config['height'] = 138;
					$config1['crop_from'] = 'middle';
                    #$config['within_rectangle'] = true;
                    $config['small_image_resize'] = 'no_resize';
					resize_exact($config);
                    
					unset($config);
					
					$config = array();

					$config['source_image'] = $this->upload_image;
					$config['thumb_marker'] = '-thumb_2';
					$config['crop'] = false;
					$config['width'] = 104;
					$config['height'] = 104;
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
    