<?php
/*********
* Author
* 
* Purpose:
*  Controller For Login Page 
* 
* 
*/

include(APPPATH.'controllers/base_controller.php');

class Media_center extends Base_controller
{
    private $word_for_today_pagination_per_page =  5 ;
    public function __construct()
     {
        try
        {
            parent::__construct();
           
			# loading reqired model & helpers...
			$this->load->helper('wall_helper');
			$this->load->model('users_model');
            $this->load->model('cms_model');
            $this->load->model('landing_page_cms_model');
		$this->load->library('embed_video');
			
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
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data; 
			$data['nav_slider_num'] = 4;       
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
			parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
										'js/ModalDialog.js',
										'js/lightbox.js',
										'js/jquery.ui.widget.js',
										'js/jquery.ui.rcarousel.js',
										'js/jquery.autofill.js',
										'js/stepcarousel.js',
										'js/jquery.flexslider.js',
										'js/jquery.eislideshow.js'
										));
			
			parent::_add_css_arr( array('css/read-slider.css') );
			
			$data['is_feature_home_news']		=	$this->landing_page_cms_model->get_all_news(" where n.i_is_feature_home=1 ",0,1,'');
			$data['is_feature_home_listen']		=	$this->landing_page_cms_model->get_all_audios(" where v.i_is_feature_home=1 ",0,1,'');
			$data['is_feature_home_video'] 		= $this->landing_page_cms_model->get_all_videos(" where i_is_feature_home=1 ",0,1,'');  
			//------------------- for top most main section ----------------------
            $VIEW = "logged/media_center/main_page.phtml";
            parent::_render($data, $VIEW);
			
		 }
        catch(Exception $err_obj)
        {
           
        }
    }
    
    
    
    
    //=============================================== play media file ===========================================
    public function get_media()
       {
          try
              {
              
                    $i_media_id = intval($this->input->post('media_id'));
                    $width = intval($this->input->post('width'))<=0?'560':intval($this->input->post('width'));
                    $height = intval($this->input->post('height'))<=0?'345':intval($this->input->post('height'));
                    
                    
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
    
    
    
    
    
    
    
    public function word_for_today_private()
    {
        try
        {      
                 
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data; 
            $data['nav_slider_num'] = 4;       
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
            parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
                                        'js/ModalDialog.js',
                                        'js/lightbox.js',
                                        /*'js/jquery.ui.widget.js',
                                        'js/jquery.ui.rcarousel.js',*/
                                        'js/jquery.autofill.js',
										'js/stepcarousel.js'
                                        ));
            
            //-------------------------------- video section -------------------------------------
            $data['videos'] = $this->landing_page_cms_model->get_all_videos();
            
            ### ajax call ###
            ob_start();
            $this->word_for_today_listing_AJAX();
            $content = ob_get_contents();
            $content_obj = json_decode($content);
            $data['content'] = $content_obj->html;
            $data['current_page'] = $content_obj->current_page;
            $data['view_more'] = $content_obj->view_more;
            
            ob_end_clean();
            ### end ajax call ###
            
            
            
            
            # view file...
            $VIEW = "banner_pages/word_for_today.phtml";
            parent::_render($data, $VIEW);
            
         }
        catch(Exception $err_obj)
        {
           
        }
    }
    
    
    public function word_for_today_listing_AJAX($page=0) 
    {
         try
         {
            $data = $this->data; 
            $where    = "" ;
            

            
            $result = $this->landing_page_cms_model->get_word_for_today_list($where ,$page,
                                                                $this->word_for_today_pagination_per_page);
            $resultCount = count($result);
            $total_rows = $this->landing_page_cms_model->get_total_word($where);
    
    //pr($result);         
    //echo "total : ".$total_rows;exit;

    
      /*      $this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."banner_pages/media_center/word_for_today_listing_AJAX";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->word_for_today_pagination_per_page;
            $config['uri_segment'] = 5;
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
            
            

            $config['div'] = '#result'; /* Here #content is the CSS selector for target DIV 
            #$config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code 
            #$config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code 


            $this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();

 */     
 
            
            $cur_page = $page + $this->word_for_today_pagination_per_page;
            
         //--- for check whether more videos are there or not
            $view_more = true;
             $rest_counter = $total_rows-$page;
             if($rest_counter<=$this->word_for_today_pagination_per_page)
                $view_more = false;
             
             
             //--------- end check
            
                  
            $data['result_arr'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $page;
            $data['total_pages'] = ceil($total_rows/$this->word_for_today_pagination_per_page);
          
             $p = ($page/$this->word_for_today_pagination_per_page);
             $data['current_loaded_page_no'] =  $p + 1;
            
            # rendering the view file...
            $VIEW_FILE = "banner_pages/word_for_today_ajax.phtml";
            $content = $this->load->view($VIEW_FILE, $data,true);
            //return $html;
            
            echo json_encode(array('html'=>$content,'current_page'=>$cur_page,'view_more'=>$view_more));
            
        } 
        catch(Exception $err_obj)
        {
            
        } 
    
    }
    
	//-------------------------------like word post ------------------------------------------------//
	
	
	public function like_word()
	{
		$window_id=$this->input->post('window_id');
		$like_or_unlike=$this->input->post('like_val');
		$liked_user_id = intval(decrypt($this->session->userdata('user_id')));
		$log_time    = get_db_datetime();
		$ip_address  = $this->input->server('REMOTE_ADDR');
		if($like_or_unlike =="Like"){
               $like_unlike_information_array = array( "i_word_id"=>$window_id,
                                                        "i_liked_user_id"=>$liked_user_id,
                                                        "dt_liked_on"=>$log_time,
                                                       );
           }
              else if($like_or_unlike =="Unlike")
                {
                    $like_unlike_information_array = array( "i_word_id"=>$window_id,
															"i_liked_user_id"=>$liked_user_id,
															"dt_liked_on"=>$log_time,
														);
                 }

                 $status = 0;
                 $response = $this->landing_page_cms_model->postLikeUnlike($like_unlike_information_array,strtolower($like_or_unlike));
				 
				
				 $_html ='';
                 if($response['value'])
                  {

                $last_id = $response['last_inserted_id'];
                $response_message=  "<span class='success_message'>".$response['message']."</span>";
                $status =1;
                $like_val = like_display($window_id);
                $display_style = $like_val[1];
                $all_user_liked = $like_val[0];

                $dislike_val = dislike_display($window_id);
                $display_style_un = $dislike_val[1];
                $all_user_unliked = $dislike_val[0];

/*
$_html ='<div class="comment_box" style="display:'.$display_style.'" id="window_like_block'.$window_id.'">
&nbsp;<img align="absmiddle" src="images/wall_like.png">&nbsp;<span id="window_like_msg'.$window_id.'">'.$all_user_liked.'</span>
</div>
<div class="comment_box" style="display:'.$display_style_un.'" id="window_unlike_block'.$window_id.'">
&nbsp;<img align="absmiddle" src="images/wall_unlike.png">&nbsp;<span id="window_unlike_msg'.$window_id.'">'.$all_user_unliked.'</span>
</div>
';*/

$_html = ''."Liked by "." (".count_word_like_link($window_id).")";


                  }
                 else
                      $response_message =  "<span class='error_message'>".$response['message']."</span>";


		   $json_data = array ('status'=>$status,'response_message'=>$response_message,'response_html'=>$_html);
           echo json_encode($json_data);

	}
	
//-----------------------------------word comment

public function post_comment($feed_id) 
	{
		$this->load->model('landing_page_cms_model');

		$this->load->model('users_model');
		$user_id = intval(decrypt($this->session->userdata('user_id')));
		$user_details = $this->users_model->fetch_this($user_id);

		$message = nl2br( htmlspecialchars(trim($this->input->post('message')), ENT_QUOTES, 'utf-8') );
        $_html='';
		if($message!='')
		    {
				$arr['i_word_id'] = $feed_id;
				//$arr['s_media_type'] = 'photo' ;
				$arr['i_user_id'] = $user_id;
				$arr['s_contents'] = $message;
				$arr['dt_created_on'] = get_db_datetime();
				
				$this->landing_page_cms_model->insert($arr);
				$arr['pseudo'] = $user_details['s_profile_name'];
				$data['comment'] = $arr;
				//$data['total_comments'] = $this->media_comments_model->get_total_by_newsfeed_id($feed_id);
				//$comment = $this->load->view('newsfeed/my_profile_single_feed_comment.phtml', $data, true);
                
                $_html = ''."Comments "." (".count_word_comment_link($feed_id).")";
				echo json_encode( array('success'=>'true', 'msg'=>"Comment posted successfully.",'html'=>$_html ) );
			}
			 else
		   {
			  echo json_encode( array('success'=>'false', 'msg'=>"Please enter some text.", 'html'=>$_html) );
		   }
		
	}
	
		public function NEW_fetch_comment_word($i_word_id='')
	{
		try
		  {
			  $data = $this->data; 
			  $html  = ''; 
			  $result = $this->landing_page_cms_model->get_by_newsfeed_id($i_word_id);
			
			//pr($result);
			  if(count($result)){
				  
				  foreach($result as $key=> $val){
					  
					 $profile_image_filename = get_profile_image_of_user('thumb',$val['s_profile_photo'],$val['e_gender']);
			 		 $DESC = html_entity_decode(htmlspecialchars_decode($val['s_contents']),ENT_QUOTES,'utf-8');
					 $profile_link = get_profile_url($val['i_user_id'],$val['s_profile_name']);
					
					 $html .= '<div class="txt_content01 comments-number-content" style="margin-bottom:3px !important"> 
					 			<a href="'.$profile_link.'"><div style="background:url('.$profile_image_filename.') no-repeat center;width:60px; height:60px;" class="pro_photo" ></div></a>
									<div class="left-nw-wal" style="padding-top:0px">
										  <p class="blue_bold12" style="margin-bottom:3px"><a href="javascript:void(0);">'.$val['s_profile_name'].'</a></p>
										  <p style="margin-bottom:3px">'.nl2br($DESC).'</p>
											 <p class="read-more" style="padding-top:2px">Updated on: '.get_time_elapsed($val['dt_created_on']).'</p>
									</div>
									<div class="clr"></div>
							  </div>'; 
							  
						
				  }
			  }
			  else{
				  $html .= '     <div class="txt_content01 comments-number-content" style="width:905px !important;"> 
										<div style="text-align:center;"><p>No Comments.</p></div>
										</div>
										';
			  }
			 
			  echo json_encode( array('result'=>'success','html_data'=>$html) );
		   } 
			catch(Exception $err_obj)
			{
			  show_error($err_obj->getMessage());
			} 
			
	} 
}   // end of controller...

