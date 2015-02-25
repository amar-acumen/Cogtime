<?php 
//ini_set('memory_limit', '2048M');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*********
* Author:
* 
* Purpose:
*  Controller For HOME Page 
* 
* @package 
* @subpackage 
* 
* @link InfController.php 
* @link Base_Controller.php
*/

include(APPPATH.'controllers/base_controller.php');



include_once APPPATH . "libraries/gmapAPI/simpleGMapAPI.php";
include_once APPPATH . "libraries/gmapAPI/simpleGMapGeocoder.php";

class Index extends Base_controller
{
	
	
    public function __construct()
     {
        try
        {
            parent::__construct();
          	parent::_non_accessible_by_logged(); //not accessable by logged in user
			
			# loading reqired model & helpers...
            $this->load->model('users_model');
			$this->upload_path = BASEPATH.'../uploads/hp_cms_video/';
			$this->load->model('hp_cms_model');
			$this->load->model('manage_hp_banner_model');
			$this->load->model('events_model');
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
            $data = $this->data;
            
            /* start seo tags */
		
			parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
			/* end seo tags */
			
            parent::_add_js_arr( array( //'js/jquery-1.7.2.js',
										/*'js/jquery.js', // causing conflict with block ui*/
									   'js/frontend/header_slider.js',
									   'js/contentslider.js',
									   'js/jquery.autofill.js',
									   
									   
									  ));
			parent::_add_css_arr( array('css/big-slider.css') );
       
            # HOMEPAGE CMS part
			
			$time_zone_arr = array();
			$time_zone_arr = $this->config->item('time_zone_arr');
			
			
			
			
			//exit;		
			
			foreach($time_zone_arr as $key=> $val){
				$timezone = new DateTimeZone($val);
				$date = new DateTime();
				$date->setTimezone($timezone);
				$data['time_val'][$key] = $date->format( 'H:i' );
			}
			//echo date('Y-m-d H:i:s');
			//pr($data['time_val'],1);
			//$timezone = new DateTimeZone($val);
			
            $data['hp_cms_content']=$this->hp_cms_model->get_by_id(1);
			$s_where=" where id<>3 and id<>4";
			$data['hp_banner']=$this->manage_hp_banner_model->fetch_multi($s_where);
			
			$cur_time = date('Y-m-d');
			$where = "WHERE 1 and E.i_status =1 AND E.dt_end_time >= '".$cur_time."' AND E.i_user_type = 2";
			$order_by = "`dt_start_time` ASC ";
			$data['hp_events']=$this->events_model->get_list($where,0,4,$order_by);
            //pr($data['hp_events']);
            
            # view file...
            $VIEW = "index.phtml";
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           show_error($err_obj->getMessage());
        }
    }
  
  	public function get_banner_clock($i_album_id)
	{
			
			$time_zone_arr = array();
			$time_zone_arr = $this->config->item('time_zone_arr');
			
			#pr($this->config->item('time_zone_arr'),1);			
			
			foreach($time_zone_arr as $key=> $val){
				$timezone = new DateTimeZone($val);
				$date = new DateTime();
				$date->setTimezone($timezone);
				$data['time_val'][$key] = $date->format( 'H:i' );
			}
			$VIEWS = "layouts/banner_clock.phtml";
			$html = $this->load->view($VIEWS, $data, true); 
			
			echo json_encode(array('result'=>'success','time_val'=>$data['time_val'],'html'=>$html));
			exit;
			
	}
	
	 public function get_media()
	   {
			  try
				  {
				  
						$i_media_id = intval($this->input->post('media_id'));
						$width = intval($this->input->post('width'))<=0?'329':intval($this->input->post('width'));
						$height = intval($this->input->post('height'))<=0?'190':intval($this->input->post('height'));
						
						
						$media_info = $this->hp_cms_model->get_by_id($i_media_id);
		          #echo utf8_accents_to_ascii($media_info['s_video_url']);
		
						if( !is_array($media_info) || !count($media_info) ) {
							echo json_encode( array('result'=>'error') );
							exit;
						}
				
					    //$this->data['current_media_id'] = $i_media_id;
		
				/* ******************** Get photo details ************************ */
				
			
				
				try {
						$this->load->library('embed_video');
						$config['zend_library_path'] = APPPATH ."libraries/Zend/";
						$config['video_url'] =  ($media_info['s_video_url']);
						
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
		
				echo json_encode($result_arr );
					
					
					
				  } 
			  catch(Exception $err_obj)
				  {
					
				  } 
			
		   }
		   
		   
	 public function know_about_cogtime_video(){
		try
			{
			
				  $i_media_id = intval($this->input->post('media_id'));
				  $width = intval($this->input->post('width'))<=0?'300':intval($this->input->post('width'));
				  $height = intval($this->input->post('height'))<=0?'190':intval($this->input->post('height'));
				  
				  
				  $media_info = $this->hp_cms_model->get_by_id($i_media_id);
			#echo utf8_accents_to_ascii($media_info['s_video_url']);
  
				  if( !is_array($media_info) || !count($media_info) ) {
					  echo json_encode( array('result'=>'error') );
					  exit;
				  }
		  
				  //$this->data['current_media_id'] = $i_media_id;
  
		  /* ******************** Get photo details ************************ */
		  
	  
		  
		  try {
				  $this->load->library('embed_video');
				  $config['zend_library_path'] = APPPATH ."libraries/Zend/";
				  $config['video_url'] =  ($media_info['s_video_url_2']);
				  
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
  
		  echo json_encode($result_arr );
			  
			  
			  
			} 
		catch(Exception $err_obj)
			{
			  
			} 
			
	 }
         function event_details(){
             
			parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
			/* end seo tags */
			
            parent::_add_js_arr( array( //'js/jquery-1.7.2.js',
										/*'js/jquery.js', // causing conflict with block ui*/
									   'js/production/frontend/header_slider.js',
									   'js/production/contentslider.js',
									   'js/production/jquery.autofill.js',
									   
									   
									  ));
			parent::_add_css_arr( array('css/big-slider.css') );
             $cur_time = date('Y-m-d');
			$where = "WHERE 1 and E.i_status =1 AND E.dt_end_time >= '".$cur_time."' AND E.i_user_type = 2";
			$order_by = "`dt_start_time` ASC ";
			$data['hp_events']=$this->events_model->get_list($where,0,4,$order_by);
                        $map = new simpleGMapAPI();
            $geo = new simpleGMapGeocoder();

            $map->setWidth(475);
            $map->setHeight(180);
            $map->setZoomLevel(13); 
            
                        
               $VIEW = "banner_pages/event_details.phtml";
            parent::_render($data, $VIEW);
         }

}   // end of controller...

