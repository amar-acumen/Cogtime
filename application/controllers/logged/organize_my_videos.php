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

class Organize_my_videos extends Base_controller
{
    private $pagination_per_page= 10;

    
    public function __construct()
     {
        try
        {
            parent::__construct();
               parent::check_login(TRUE, '', array('1','2', '3')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...
            $this->load->model('users_model');
            $this->load->model('my_videos_model');
            
            
            #defining path
            
            $this->upload_path = BASEPATH.'../uploads/user_videos/';
            $this->upload_path_album_photo = BASEPATH.'../uploads/user_videos_album/';
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }

    } // end of constructor
    
    
    
    
    
    public function index($album_id) 
    {
        try
        {
            
            $i_profile_id = intval( decrypt($this->session->userdata('user_id')) );
            $this->session->set_userdata('album_id',$album_id);
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
               $this->data["MAIN_MENU_SELECTED"] = 1;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js',
                                        'js/animate-collapse.js',
                                        'js/lightbox.js',
                                        'js/jquery.dd.js',
                                        'js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',
                                        'js/jquery.form.js',
                                        'js/jquery/JSON/json2.js',*/
										'js/production/tweet_utilities.js',
                                        'js/production/my_videos.js',
                                        'js/production/organize_my_videos.js'
                                        ));
                                        
//            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
//                                        'css/dd.css') );
            # adjusting header & footer sections [End]...

            $data['page_view_type'] = 'myaccount';
            $arr_profile_info = $this->users_model->fetch_this($i_profile_id);
            $data['arr_profile_info'] = $arr_profile_info;
            
            // to show all album names in select box while uploading a video
            $data['album_names'] = $this->upload_video_select_album_name();     
            
            // get full album info
            $data['album_info'] = $this->my_videos_model->get_album_info($album_id);
            $data['this_album_id'] = $album_id;
            
            
            $data['pagination_per_page'] = $this->pagination_per_page;
            
            ### for all videos  ###
           
            ob_start();
            $this->my_videos_ajax_pagination();
            $content = ob_get_contents();
            $content_obj = json_decode($content);
            $data['result_arr'] = $content_obj->html; 
            $data['total'] = $content_obj->total;
            
            ob_end_clean();
            
            
            ### end all albums  ###
    
            
            # view file...
            $VIEW = "logged/videos/organize/organize_my_videos.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    } // end of index
    
    public function my_videos_ajax_pagination($page=0)
    {
        try
        {     
            $current_page = $page + $this->pagination_per_page;
         $album_id = $this->session->userdata('album_id');

                $i_profile_id = intval(decrypt($this->session->userdata('user_id')));
            
                $result = $this->my_videos_model->get_all_videos_of_album($album_id,'',$page,$this->pagination_per_page);
                
                $total_rows = $this->my_videos_model->get_total_no_of_videos_of_album($album_id);

   

            //--- for check whether more videos are there or not
            $view_more = true;
             $rest_counter = $total_rows-$page;
             if($rest_counter<=$this->pagination_per_page)
                $view_more = false;
             
             
             //--------- end check


              // getting   listing...
              $data['my_videos_album_content'] = $result;
              $data['no_of_result'] = $total_rows;
              $data['current_page'] = $current_page;
              $data['total_pages'] = ceil($total_rows/$this->pagination_per_page);
              
              
              // to show all album names in select box while uploading a video
              $data['album_names'] = $this->upload_video_select_album_name(); 
              
               $p = ($page/$this->pagination_per_page);
               $data['current_loaded_page_no'] =  $p + 1;
               $data['is_post_'] = $this->session->userdata('is_post_') ;
                # loading the view-part...
                $VIEW_FILE = 'logged/videos/organize/organize_my_videos_ajax.phtml';
            if( is_array($result) && count($result) ) {
            $content = $this->load->view( $VIEW_FILE , $data, true);
            }
            else {
                #$content = '<div class="txt_content01"><p style="margin-left: 330px;">No Posts!</p></div>';
                $content = '';
            }
            echo json_encode( array('html'=>$content, 'current_page'=>$current_page,'total'=>$total_rows, 'view_more'=>$view_more) );
                  
                  
                  
         }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
        
    }
    
    
    
    //------------------------------------------------ fetch album name -----------------------------------------------
    function upload_video_select_album_name()       //called from index
    {
        $user_id = intval( decrypt($this->session->userdata('user_id')) );
        $names = $this->my_videos_model->get_all_video_album_name($user_id);
       
        return $names;
        
    }
    
    
    
    //----------------------------------------------#### ordering in ajax ####------------------------------
   function maintain_displayorder_ajax($page=0)
    {
        //sleep(2);
        $data = $this->data;
        $actionID = $this->input->post('rid'); 
        $i_album_id = $this->input->post('i_album_id');
        $status = $this->input->post('status');
        $i_user_id = intval($i_user_id)<=0?intval(decrypt($this->session->userdata('user_id'))):intval($i_user_id);
           $data['page_view_type'] = $page_view_type;
                
        
        # retrieving  info...
    
        $this->load->model("utility_model");
        $tbl=$this->db->USER_VIDEOS;
    
         
        $WHERE_COND_BEGIN = " AND i_video_album_id = {$i_album_id}";    
        $this->utility_model->Ranking($status, $actionID, $WHERE_COND_BEGIN,$tbl);
       
         
                $result = $this->my_videos_model->get_all_videos_of_album($i_album_id,'',$page,$this->pagination_per_page);
                
                $total_rows = $this->my_videos_model->get_total_no_of_videos_of_album($i_album_id);

         ob_start();
		  $this->my_videos_ajax_pagination();
		  $content = ob_get_contents();
		  $content_obj = json_decode($content);
		  $html = $content_obj->html; 
		  $total_rows = $content_obj->total;
		  $current_page = $content_obj->current_page;
		  $view_more = $content_obj->view_more;
		 ob_end_clean();
		 
		 echo json_encode( array('html'=>$html, 'current_page'=>$current_page,'total'=>$total_rows, 'view_more'=>$view_more) );
    }
    
//----------------------------------------------#### end ordering in ajax ####------------------------------   
    
    
    //--------------------------------- change album -----------------------------------------
    function change_album()
    {
        $video_id = $this->input->post('video_id');
        $album_id = $this->input->post('album_id');
        $now_album_id = $this->input->post('now_album_id');
        
        $this->my_videos_model->change_video_album($video_id,$now_album_id);
        
         ### for all videos  ###
          ob_start();
		  $this->my_videos_ajax_pagination();
		  $content = ob_get_contents();
		  $content_obj = json_decode($content);
		  $html = $content_obj->html; 
		  $total_rows = $content_obj->total;
		  $current_page = $content_obj->current_page;
		  $view_more = $content_obj->view_more;
		 ob_end_clean();
		 
		 echo json_encode( array('html'=>$html, 'current_page'=>$current_page,'total'=>$total_rows, 'view_more'=>$view_more) );
        
    }
    
    //--------------------------------- end change album -----------------------------------------
    
    
    //-------------------------------------------- upload individual video --------------------------------------------
    function upload_individual_video()
    {
        
    try
        {
            $arr_messages = array();
                
            if($_POST){
                  # error message trapping...
                  if( trim($this->input->post('txt_title'))=='') 
                  {
                          $arr_messages['txt_title'] = "* Required Field";
                  }
         
                  if( trim($this->input->post('txt_video_track_album'))=='') 
                  {
                          $arr_messages['txt_video_track_album'] = "* Required Field";
                  }
                  if( trim($this->input->post('txtarea_desc_video'))=='') 
                  {
                          $arr_messages['txtarea_desc_video'] = "* Required Field";
                  }
                  
                  if( trim($this->input->post('select_album1'))=='-1') 
                  {
                          $arr_messages['select_album1'] = "* Required Field";
                  }
                  elseif(trim($this->input->post('select_album1'))=='0')
                  {
                      $new_album = false;
                        if( trim($this->input->post('txt_nw_album'))=='') 
                        {
                              $arr_messages['txt_nw_album'] = "* Required Field";
                        }
                        
                        else
                        {
                            $info_nw_album['s_name'] = get_formatted_string($this->input->post('txt_nw_album'));
                            $info_nw_album['i_user_id'] = intval( decrypt($this->session->userdata('user_id')) );
                            $info_nw_album['e_privacy'] = 'everyone';
                            $info_nw_album['dt_created_on'] = get_db_datetime();
                            
                            $new_album = true;
                            
                        }
                  }
                  else
                  {
                      //$info['i_video_album_id'] = trim($this->input->post('select_album1'));
                  }
                  
                  
                  
           
                
                if($this->input->post('txt_video_track_album')!='') 
                          {
                                  
                              try {
                                  
                                      $this->load->library('embed_video');
                                      $config['zend_library_path'] = APPPATH ."libraries/Zend/";
                                      $config['video_url'] = trim($this->input->post('txt_video_track_album'));
                                      
                                      $this->embed_video->initialize($config);
                              
                                      $this->embed_video->prepare();
                                      
                                      $this->embed_video->save_thumb($this->upload_path, '-bigthumb', 122, 82);
                                      $this->video_img_name = $this->embed_video->get_resized_imagename();
                                      
                              
                                      
                                  }
                                  catch(Exception $e) 
                                  {
                                      $arr_messages['txt_video_track_album'] = "* Not valid video URL";
                                  }
                      }
           
                  $MAX_VIDEO =  $this->data['site_settings_arr']['i_max_video_upload'];
				  $TOTAL_VIDEO = $this->my_videos_model->get_total_by_user_id(intval( decrypt($this->session->userdata('user_id')) ));
		
                  if( count($arr_messages)==0 && ($TOTAL_VIDEO < $MAX_VIDEO || $MAX_VIDEO == 0 )) {
                          
                      $info = array();
                      $info['i_user_id'] = intval( decrypt($this->session->userdata('user_id')) );
                      
                      if($new_album)  
                      {
                          $nw_album_id = $this->my_videos_model->create_video_album_at_video_upload($info_nw_album);  
                          $info['i_video_album_id'] = $nw_album_id;   // setting new video album id
                      }
                          
                      else
                            $info['i_video_album_id'] = trim($this->input->post('select_album1')); 
                            
                      $info['s_video_file_name'] = trim($this->input->post('txt_video_track_album')); 
                     
                      $info['s_video_image']     = $this->video_img_name;
                      $info['s_title'] = get_formatted_string($this->input->post('txt_title'));     
                      $info['s_artist'] = get_formatted_string($this->input->post('txt_artist')); 
                      $info['s_genre'] = trim($this->input->post('txt_genre')); 

                      $info['s_description'] = trim($this->input->post('txtarea_desc_video')); 
                      $info["i_order"] =  1+$this->my_videos_model->get_i_order($info['i_video_album_id']);
                      

                      $info['dt_created_on'] = get_db_datetime();
                      
                      $_ret = $this->my_videos_model->insert_new_video($info);
                      
                        ### for all videos  ###
                   
                        
                        ob_start();
                        $this->my_videos_ajax_pagination();
                        $content = ob_get_contents();
                        $content_obj = json_decode($content);
                        $data['result_arr'] = $content_obj->html; 
                        ob_end_clean();
                        ### end all albums  ###
                      
                      
                      
                                          
                      echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'Video File Uploaded Successfully.','html'=>$data['result_arr']) );
                  }
				  
				  else if(count($arr_messages)==0 && $TOTAL_VIDEO  == $MAX_VIDEO ){	

						echo json_encode( array('success'=>false,'msg'=>'Video cannot be uploaded as maximum video upload limit is reached!','maxlimit'=>true) );
						exit;
				  }
                  else
                  {
                      echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'Error!'));
                  }
        
            }   //$_POST
            
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }
//----------------------------------------------- end of upload video -----------------------------------------



//------------------------------------------------- edit video ------------------------------------------------
function edit_video_fetch_data()
{
   
    $video_id = $this->input->post('video_id');
    $res = $this->my_videos_model->get_video_info_by_id($video_id);
    
    $r['id'] = $res[0]['id'];
    $r['s_video_image'] = $res[0]['s_video_image'];
    
    $r['s_title'] = $res[0]['s_title'];
    $r['s_artist'] = $res[0]['s_artist'];
    $r['s_genre'] = $res[0]['s_genre'];
    $r['s_video_file_name'] = $res[0]['s_video_file_name'];
    $r['s_description'] = $res[0]['s_description'];
    $r['i_video_album_id'] = $res[0]['i_video_album_id'];
    
   
    echo json_encode(array('response'=>true,
                            'fetched_data'=>$r
                            ));
    //pr($res);
}

    function edit_individual_video()
    {
        
    try
        {
            $arr_messages = array();
                
            if($_POST){
                
                  # error message trapping...
                  if( trim($this->input->post('txt_title_edit'))=='') 
                  {
                          $arr_messages['txt_title_edit'] = "* Required Field";
                  }
       
                  if( trim($this->input->post('txt_video_track_album_edit'))=='') 
                  {
                          $arr_messages['txt_video_track_album_edit'] = "* Required Field";
                  }
                  if( trim($this->input->post('txtarea_desc_video_edit'))=='') 
                  {
                          $arr_messages['txtarea_desc_video_edit'] = "* Required Field";
                  }
                  
                  if( trim($this->input->post('select_album1_edit'))=='-1') 
                  {
                          $arr_messages['select_album1_edit'] = "* Required Field";
                  }
                  elseif(trim($this->input->post('select_album1_edit'))=='0')
                  {
                      $new_album = false;
                        if( trim($this->input->post('txt_nw_album_edit'))=='') 
                        {
                              $arr_messages['txt_nw_album_edit'] = "* Required Field";
                        }
                        
                        else
                        {
                            $info_nw_album['s_name'] = get_formatted_string($this->input->post('txt_nw_album'));
                            $info_nw_album['i_user_id'] = intval( decrypt($this->session->userdata('user_id')) );
                            $info_nw_album['e_privacy'] = 'everyone';
                            $info_nw_album['dt_created_on'] = get_db_datetime();
                            
                            $new_album = true;
                            
                        }
                  }
                  else
                  {
                      //$info['i_video_album_id'] = trim($this->input->post('select_album1'));
                  }
                  
                  
                  
           
                
                 if($this->input->post('txt_video_track_album_edit')!='') 
                          {
                                  
                             try {
                                  
                                      $this->load->library('embed_video');
                                      $config['zend_library_path'] = APPPATH ."libraries/Zend/";
                                      $config['video_url'] = trim($this->input->post('txt_video_track_album_edit'));
                                      
                                      $this->embed_video->initialize($config);
                              
                                      $this->embed_video->prepare();
                                      
                                      $this->embed_video->save_thumb($this->upload_path, '-bigthumb', 122, 82);
                                      $this->video_img_name = $this->embed_video->get_resized_imagename();
                                      
                              
                                      
                                  }
                                  catch(Exception $e) 
                                  {
                                      $arr_messages['txt_video_track_album_edit'] = "* Not valid video URL";
                                  }
                      }
                      
        
        
                  if( count($arr_messages)==0 ) {
                          
                      $info = array();
                      $info['id']=$this->input->post('id');
                      $img = $this->input->post('s_video_image') ;
                      $img_path = BASEPATH."../uploads/user_videos/".getThumbName($img,'bigthumb');
                      @unlink($img_path);
                      
                      $info['i_user_id'] = intval( decrypt($this->session->userdata('user_id')) );
                      
                      if($new_album)  
                      {
                          $nw_album_id = $this->my_videos_model->create_video_album_at_video_upload($info_nw_album);  
                          $info['i_video_album_id'] = $nw_album_id;   // setting new video album id
                      }
                          
                      else
                            $info['i_video_album_id'] = trim($this->input->post('select_album1_edit')); 
                            
                      $info['s_video_file_name'] = trim($this->input->post('txt_video_track_album_edit')); 
                      
                      $info['s_video_image']     = $this->video_img_name;
                     
                      
                      $info['s_title'] = get_formatted_string($this->input->post('txt_title_edit'));     
                      $info['s_artist'] = get_formatted_string($this->input->post('txt_artist_edit')); 
                      $info['s_genre'] = trim($this->input->post('txt_genre_edit')); 

                      $info['s_description'] = trim($this->input->post('txtarea_desc_video_edit')); 
        
                      $info["i_order"] =  1+$this->my_videos_model->get_i_order($info['i_video_album_id']);
                      

                      $info['dt_updated_on'] = get_db_datetime();
                      
                      
        //pr($arr_messages);
        //pr($info,1);
        
                      $_ret = $this->my_videos_model->update_video($info);
                      
                      
                        
                        ob_start();
                        $this->my_videos_ajax_pagination();
                        $content = ob_get_contents();
                        $content_obj = json_decode($content);
                        $data['result_arr'] = $content_obj->html; 
                        $data['total'] = $content_obj->total;
                        
                        ob_end_clean();          
                        ### end all albums  ###
                      
                      
                      
                                          
                      echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'Video File Uploaded Successfully.','html'=>$data['result_arr']) );
                  }
                  else
                  {
                      echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>t('Error!')) );
                  }
        
            }   //$_POST
            
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }




//------------------------------------------------- end of edit video ------------------------------------------------


//------------------------------------------- delete video ----------------------------------------
function delete_video()
{
    $video_id = $this->input->post('video_id');
    $img_res = $this->my_videos_model->get_video_img_by_id($video_id);
    $img = BASEPATH."../uploads/user_videos/".getThumbName($img_res['s_video_image'],'bigthumb');
   
    $res = $this->my_videos_model->delete_video($video_id);
    
    $this->load->model('media_comments_model');
    $this->media_comments_model->delete_by_id($video_id,'video');
    
    if($res)
    {
         @unlink($img);
         $msg = 'Video File Deleted Successfully.';
         $success = true;
    }
    else
    {
         $msg = 'Error.';
         $success = false;
    }
    ### for all videos  ###
   
    
    ob_start();
    $this->my_videos_ajax_pagination();
    $content = ob_get_contents();
    $content_obj = json_decode($content);
    $data['result_arr'] = $content_obj->html; 
    $data['total'] = $content_obj->total;
    
    ob_end_clean();
    ### end all albums  ###
                      
                      
                      
                                          
    echo json_encode( array('success'=>$success,'msg'=>$msg,'html'=>$data['result_arr']) );
    
}
    
    
    
    
    
    
    
    
    
    
    
    
    
    
        //=============================================== play media file ===========================================
    public function get_media()
       {
          try
              {
              
                    $i_media_id = intval($this->input->post('media_id'));
                    //echo $i_media_id = $this->session->userdata('album_id');
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
    
    
    
    
} // end of controller