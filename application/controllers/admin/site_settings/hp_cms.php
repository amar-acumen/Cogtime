<?php
/*********
* Author: 
* Date  : 
* Modified By: 
* Modified Date:
* 
* Purpose:
*  Controller For ## Management
* 
* @package 
* @subpackage 
* 
* @link InfController.php 
* @link Base_Controller.php
* @link model/##.php
* @link views/##
*/

class Hp_cms extends Admin_base_Controller
{

	function __construct()
	 {
		try
		{
		    parent::__construct();
			 parent::_check_admin_login();
			 
			$this->upload_path = BASEPATH.'../uploads/hp_cms_video/';
            # loading reqired model & helpers...
			$this->load->model('hp_cms_model');
		}
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

	}

	function index() 
	{
		try
		 {
			$data = $this->data;
			$data['top_menu_selected'] = 1;
			$data['submenu'] =2;
            
            # adjusting header & footer sections [Start]...
                parent::_set_title('::: COGTIME Xtian network :::');
                parent::_set_meta_desc("::: COGTIME Xtian network :::");
                parent::_set_meta_keywords("::: COGTIME Xtian network :::");
                
                parent::_add_js_arr( array('js/jquery.form.js'=>'header','js/jquery/JSON/json2.js'=>'header','js/backend/cms/hp_cms.js'=>'header') );										
                parent::_add_css_arr( array('css/admin.css') );
		
			# adjusting header & footer sections [End]...
			
			# fetch and show hp cms content
			
			 $data['hp_cms_content'] = $this->hp_cms_model->get_by_id(1);
             				
			# rendering the view file...
            $view_file = "admin/site_settings/hp_cms.phtml";
            parent::_render($data, $view_file);
		}
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
	}  
	
	public function edit_hp_cms($id=1)
	{
		try
		{
		  $arr_messages = array();
				
			if($_POST){
				  # error message trapping...
				  if( trim($this->input->post('txt_content1'))=='') 
				  {
						  $arr_messages['content1'] = "* Required Field";
				  }
				  if( trim($this->input->post('txt_content2'))=='') 
				  {
						  $arr_messages['content2'] = "* Required Field";
				  }
				  if( trim($this->input->post('txt_video_url'))=='') 
				  {
						  $arr_messages['video_url'] = "* Required Field";
				  }
				   if( trim($this->input->post('txt_video_url_2'))=='') 
				  {
						  $arr_messages['video_url_2'] = "* Required Field";
				  }
				  
				  
				  if($this->input->post('txt_video_url')!='') 
						  {
								  
							  try {
								  
									  $this->load->library('embed_video');
									  $config['zend_library_path'] = APPPATH ."libraries/Zend/";
									  $config['video_url'] = trim($this->input->post('txt_video_url'));
									  
									  $this->embed_video->initialize($config);
									  //echo 'video_url='.$this->embed_video->video_url;
									  $this->embed_video->prepare();
									  
									  $this->embed_video->save_thumb($this->upload_path, '-bigthumb', 320, 190);
									  $this->video_img_name = $this->embed_video->get_resized_imagename();
									  
							  
									  
								  }
								  catch(Exception $e) 
								  {
									  $arr_messages['video_url'] = "* Not valid video URL";
								  }
					  }
				
				if($this->input->post('txt_video_url_2')!='') 
						  {
								  
							  try {
								  
									  $this->load->library('embed_video');
									  $config['zend_library_path'] = APPPATH ."libraries/Zend/";
									  $config['video_url'] = trim($this->input->post('txt_video_url_2'));
									  
									  $this->embed_video->initialize($config);
									  //echo 'video_url='.$this->embed_video->video_url;
									  $this->embed_video->prepare();
									  
									  $this->embed_video->save_thumb($this->upload_path, '-bigthumb', 300, 190);
									  $this->video_img_name_2 = $this->embed_video->get_resized_imagename();
									  
							  
									  
								  }
								  catch(Exception $e) 
								  {
									  $arr_messages['video_url_2'] = "* Not valid video URL";
								  }
					  }
				  
				  if( count($arr_messages)==0 ) {
						  
					  $info = array();
					  
					  $info['s_content_1'] = get_formatted_string($this->input->post('txt_content1'));	 
					  $info['s_content_2'] = get_formatted_string($this->input->post('txt_content2')); 
					  $info['s_video_url'] = trim($this->input->post('txt_video_url')); 
					  $info['s_video_url_2'] = trim($this->input->post('txt_video_url_2')); 
	  				  $info['s_video_image'] 	= $this->video_img_name;
					  $info['s_video_image_1'] 	= $this->video_img_name_2;
					  $info['dt_created_on'] = get_db_datetime();
					  
					  $_ret = $this->hp_cms_model->update($info ,1);
					  
										  
					  echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'Homepage Content updated Successfully.') );
				  }
				  else
				  {
					  echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>t('Error!')) );
				  }
		
			}
			
		}
		catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
	}

}   // end of controller...