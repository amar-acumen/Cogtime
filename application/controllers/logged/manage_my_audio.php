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

class Manage_my_audio extends Base_controller
{
    private $pagination_per_page =  5 ;
	private $upload_path ;
    
    public function __construct()
     {
        try
        {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
           
		    $this->upload_path_music_full = BASEPATH.'../uploads/user_audio_files/';
			//$this->load->model('user_audios_model');
			$this->load->model('audio_albums_model');
		    # loading reqired model & helpers...
            $this->load->model('users_model');
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
            $data = $this->data;      
            $this->data["MAIN_MENU_SELECTED"] = 1;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',*/
										'js/production/tweet_utilities.js',
										'js/production/manage_audio_album.js'
                                        ));
                                        
//            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
//                                          'css/dd.css') );
										  
			############################################################
			$i_user_id = intval(decrypt($this->session->userdata('user_id')));
			$data['page_view_type'] = 'myaccount'; 
			$arr_profile_info = $this->users_model->fetch_this($i_user_id);
			
			parent::_set_all_audio_album_data($i_user_id);
			
			#### FETCHING PHOTOS PER USER 
			if(is_array($arr_profile_info) && !empty($arr_profile_info)){
				$data['arr_profile_info'] = $arr_profile_info;
				#$data['arr_photos'] = $this->user_photos_model->get_by_user_id($i_user_id, 0, $this->pagination_per_page);
				$data['arr_albums'] = $this->audio_albums_model->get_by_user_id($i_user_id, 0 ,$this->pagination_per_page);
				
								
				###fetching all photo to show in slideshow
				
				//$data['arr_allphotos'] = $this->user_photos_model->get_by_user_id($i_user_id);
				
				/*ob_start();
				$this->albums_ajax_pagination($i_user_id,$data['page_view_type']);
				$data['result_album_content'] = ob_get_contents();
				ob_end_clean(); 
                */
                
                $data['profile_id'] = $i_user_id;
                $data['pagination_per_page'] = $this->pagination_per_page;
                
                ob_start();
                $this->albums_ajax_pagination($i_user_id,$data['page_view_type']);
                $content = ob_get_contents();
                $content_obj = json_decode($content);
                $data['result_album_content'] = $content_obj->html; 
                ob_end_clean();
				
			}
            
            # view file...
            $VIEW = "logged/audios/manage_my_audios.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }   
	
	
      public function albums_ajax_pagination($i_user_id,$page_view_type='public',$page=0) 
	  {

		 try
		 {
            $current_page = $page+$this->pagination_per_page;
		   	$data = $this->data;    
			$i_user_id = intval($i_user_id)<=0?intval(decrypt($this->session->userdata('user_id'))):intval($i_user_id);
			$data['page_view_type'] = $page_view_type;	
			
				 
		    $result = $this->audio_albums_model->get_by_user_id($i_user_id, intval($page), $this->pagination_per_page);
			$total_rows = $this->audio_albums_model->get_total_by_user_id($i_user_id);
			
		/*	$this->load->library('jquery_pagination');
			$config['base_url'] = base_url().'logged/manage_my_audio/albums_ajax_pagination/'.$i_user_id."/".$page_view_type."/";
			$config['total_rows'] = $total_rows;
			$config['per_page'] = $this->pagination_per_page;
			$config['uri_segment'] = 6;
			$config['num_links'] = 9;
			$config['page_query_string'] = false;
            
            $config['prev_link'] = '&laquo; Previous';
            $config['next_link'] = 'Next &raquo;';

            $config['cur_tag_open'] = '<li> <span><a href="javascript:void(0)" class="select">';
			$config['cur_tag_close'] = '</a></span></li>';

			$config['next_tag_open'] = '<li><a href="javascript:void(0)">';
			$config['next_tag_close'] = '</a></li>';

			$config['prev_tag_open'] = '<li><a href="javascript:void(0)">';
			$config['prev_tag_close'] = '</a></li>';

			$config['num_tag_open'] = '<li>';
			$config['num_tag_close'] = '</li>';

            $config['div'] = '#album_div'; /* Here #content is the CSS selector for target DIV 
			$config['js_bind'] = "showLoading();"; /* if you want to bind extra js code 
			$config['js_rebind'] = "hideLoading();";
	
	
			$this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();
*/
            $data['arr_albums'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $current_page;
			
			$data['total_pages'] = ceil($total_rows/$this->pagination_per_page);
		  
			$p = ($page/$this->pagination_per_page);
			$data['current_loaded_page_no'] =  $p + 1;
			
            $VIEW_FILE = "logged/audios/blocks/load_my_audios_albums_listing_ajax.phtml";
			//$this->load->view( $VIEW_FILE , $data);
            
            if( is_array($result) && count($result) ) {
            $content = $this->load->view( $VIEW_FILE , $data, true);
            }
            else {
                #$content = '<div class="txt_content01"><p style="margin-left: 330px;">No Posts!</p></div>';
                $content = '';
            }
            echo json_encode( array('html'=>$content, 'current_page'=>$current_page) );
		} 
        catch(Exception $err_obj)
        {
            
        } 
	
    } 
	
	public function add_photo()
	{
		try
		{
			
			parent::check_login(TRUE,'',array('1'));//code for those pages which are not accessable by non logged in user
			$arr_messages = array();
				
			# error message trapping...
			if( trim($this->input->post('txt_title'))=='') 
			{
					$arr_messages['title'] = "* Required Field.";
			}
			
			if( $_FILES['s_photo']['name']=='' ) {
				 $arr_messages['photo'] = "* Required Field.";
			}
			
				if( isset($_FILES['s_photo']['name']) && $_FILES['s_photo']['name']!='') {
					preg_match('/(^.*)\.([^\.]*)$/', $_FILES['s_photo']['name'], $matches);
					$ext = "";
					if(count($matches)>0) {
						$ext = $matches[2];
						$original_name = $matches[1];
					}
					else
						$original_name = 'photo';
	
				
					if ( !in_array($ext , $this->config->item('VALID_IMAGE_EXT'))) 
					{
						 $arr_messages['photo'] =t("supported extensions are ".implode(' , ',$this->config->item('VALID_IMAGE_EXT')) );
					}
					else if($_FILES['s_photo']['size'] > $this->config->item('MAX_UP_FILE_SIZE')*1024*1024)
					 {
						$data["photo"] ="Maximum file upload size is ".$this->config->item('MAX_UP_FILE_SIZE')." MB.";
					 }		
				}

		   //pr($arr_messages);
			if( count($arr_messages)==0 ) {
					
				$info = array();
				
				$info['i_user_id'] = intval(decrypt($this->session->userdata('user_id')));	 
				$info['s_title'] = get_formatted_string($this->input->post('s_title')); 
				$info['s_description'] = get_formatted_string($this->input->post('s_description')); 
				$info['s_photo'] 	= $this->_upload_photo();			
				$info['dt_created_on'] = get_db_datetime();
				$_ret = $this->user_photos_model->insert($info);
				$i_total = $this->user_photos_model->get_total_by_user_id($info['i_user_id']);
				
									
				echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'i_last_inserted_id'=>$_ret,'msg'=>t('Message sent'), 'mini_img'=>getThumbName($info['s_photo'], 'mini'), 'bigthumb'=>getThumbName($info['s_photo'], 'bigthumb'),'i_total'=>$i_total) );
			}
			else
			{
				echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'Error!') );
			}
		
		
		
		}
		catch(Exception $err_obj)
        {
            
        } 
	}
    
	  public function delete($rec_id){
			$data = $this->data;
			
			$album_info = $this->audio_albums_model->get_by_id($rec_id);
			$audio_arr = $this->audio_albums_model->delete_by_id($rec_id);
		// pr($audio_arr,1);
			foreach($audio_arr as $key=>$val){
			  @unlink($this->upload_path_music_full . $audio_arr['s_audio_file_name']);
			  
			}
			@unlink(BASEPATH.'../uploads/user_audio_album_photos/'. getThumbName($album_info['s_image_name'], 'thumb'));
			
			$re_page = base_url() ."manage-my-audio.html";
					header("location:".$re_page);
					exit;
		}
	
}   // end of controller...

