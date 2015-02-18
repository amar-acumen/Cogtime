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


class Media_center extends Base_controller
{
    
    private $pagination_per_page = 6  ;
    private $search_pagination_per_page =  2 ;
    private $popular_pagination_per_page =  10 ;
	private $comment_pagination_per_page =  10 ;
	private $like_pagination_per_page =  10 ;
    
    private $ring_members_pagination_per_page =  10 ;
	private $shouts_pagination_per_page = 10 ;
    
    public function __construct()
     {
	 	
        try
        {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...
            $this->load->model('users_model');
			$this->load->model("gospel_magazine_model");
			$this->load->model("my_blog_post_model");
			$this->load->model("landing_page_cms_model");
			
			$this->i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

	
	public function christan_project()
	{
           // die('ok');
		try
        {
			$posted=array();
			$this->data["posted"]=$posted;/*don't change*/    
			$data = $this->data;      
			$this->data["MAIN_MENU_SELECTED"] = 6;
			parent::_set_title('::: COGTIME Xtian network :::');
			parent::_set_meta_desc('');
			parent::_set_meta_keywords('');
		
			
			parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
										'js/switch.js','js/animate-collapse.js',
										'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
										'js/stepcarousel.js',
										'js/jquery.autofill.js',
										'js/frontend/logged/tweets/tweet_utilities.js',
										'js/tab.js',
										'js/jquery.eislideshow.js',
										'js/jquery.hoverIntent.minified.js',
										'js/jquery.naviDropDown.1.0.js',
										'js/jquery.bxslider.min.js',
                                                                                 'js/jquery.fitvids.js'  
										));
										
			parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
											  'css/dd.css') );
				
				
			# view file...
			/*ob_start();
			$content = $this->generate_christan_project_AJAX();
			$content = ob_get_contents();
			$data['listingContent'] = $content;
			ob_end_clean();*/
			//$data['listingContent'] = $content;
			
			
			$res = $this->landing_page_cms_model->get_all_video_cat();
			foreach($res as $v)
			{
				$data['cat_name'][$v[id]]	= $v['s_name'];
			}
			
			
			
			$res = $this->landing_page_cms_model->get_all_news_cat();
			$data['artical_cat_name']= $res;
			
			$res_watch = $this->landing_page_cms_model->get_all_video_cat();
			$data['watch_cat_name']	= $res_watch;
			
			$res_listen = $this->landing_page_cms_model->get_all_audio_cat();
			$data['listen_cat_name'] = $res_listen; 
			
			$rst = $data['video_featured'] = $this->landing_page_cms_model->get_all_videos(" where is_featured=1");
                        
                                //$vidurl = $val['s_url'];
                              // $finalurl = end(explode('https:',$vidurl));
                        /**************************************************************/
                                $this->load->library('embed_video');
                                $config['zend_library_path'] = APPPATH ."libraries/Zend/";
                                $config['video_url'] =  $rst[0]['s_url'];
                                $this->embed_video->initialize($config);
                         /******************************************************************/       
                                
                                
			$this->embed_video->prepare();
			$data['image_source'] = $this->embed_video->get_player('728','546');
			$data['video1'] = $this->landing_page_cms_model->get_all_videos(" where i_category_id=1",0,6,'');  
			$data['video2'] = $this->landing_page_cms_model->get_all_videos(" where i_category_id=2");  
			$data['video3'] = $this->landing_page_cms_model->get_all_videos(" where i_category_id=3");  
			$data['video4'] = $this->landing_page_cms_model->get_all_videos(" where i_category_id=4");  
			$data['video5'] = $this->landing_page_cms_model->get_all_videos(" where i_category_id=5");  
			$data['video6'] = $this->landing_page_cms_model->get_all_videos(" where i_category_id=6",0,7,'');  
			//pr($data,1);
			
			$VIEW = "logged/media_center/video.phtml"; 
			parent::_render($data, $VIEW);
			
        }
        catch(Exception $err_obj)
        {
           
        } 
	}
	
	public function category($id) 
    {
        try
        {
        
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
               $this->data["MAIN_MENU_SELECTED"] = 1;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',
										'js/frontend/logged/tweets/tweet_utilities.js',
                                        'js/frontend/logged/christian_news_js.js',
										'js/tab.js',
										'js/jquery.flexslider.js'
										,'js/jquery.eislideshow.js',
										'js/jquery.hoverIntent.minified.js',
										'js/jquery.naviDropDown.1.0.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css',
										  'css/flexslider.css') );
            
           
		  
		   
           
           $data['pagination_per_page'] = $this->pagination_per_page;
          
	        
			
			$res = $this->landing_page_cms_model->get_all_news_cat();
			$data['artical_cat_name']= $res;
			
			$res_watch = $this->landing_page_cms_model->get_all_video_cat();
			$data['watch_cat_name']	= $res_watch;
			
			$res_listen = $this->landing_page_cms_model->get_all_audio_cat();
			$data['listen_cat_name'] = $res_listen; 
			
			
			
            
            # view file...
			
			$data['article']	=	$this->landing_page_cms_model->get_all_videos(" where v.i_category_id='".$id."'" ,'','','');
			$data['right_content']  = $this->landing_page_cms_model->get_all_videos(" where v.i_category_id='".$id."'",0,7,'');
			
			$data['is_video'] = 'y';
			$VIEW = "logged/media_center/read_category.phtml";
            

                parent::_render($data, $VIEW);
            
        }
        
        catch(Exception $err_obj)
        {
           
        } 

    } // end of index  
    
 
	public function watch_detail($id,$cat)
    {
        try
        {

            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
               $this->data["MAIN_MENU_SELECTED"] = 1;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',
                                        'js/frontend/logged/christian_news_js.js',
										'js/tab.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css') );
            
			
			
			parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
										'js/switch.js','js/animate-collapse.js',
										'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',
										'js/stepcarousel.js',
										'js/jquery.autofill.js',
										'js/frontend/logged/tweets/tweet_utilities.js',
										'js/tab.js',
										'js/jquery.eislideshow.js',
										'js/jquery.hoverIntent.minified.js',
										'js/jquery.naviDropDown.1.0.js',
										'js/jquery.bxslider.min.js'
										));
										
			parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
											  'css/dd.css') );
			
           
           //####################### update view count ######################
           $this->landing_page_cms_model->update_view_count_by_news_id($id);
           //####################### end update view count ######################
           
           
			$data['pagination_per_page'] = $this->pagination_per_page;
			$data['all_categories'] = $this->landing_page_cms_model->get_all_news_cat();
			$rst = $this->landing_page_cms_model->get_all_videos(" where v.id='".$id."' ");
			$this->load->library('embed_video');
			$config['zend_library_path'] = APPPATH ."libraries/Zend/";
			$config['video_url'] =  $rst[0]['s_url'];
			
			$this->embed_video->initialize($config);
			$this->embed_video->prepare();
			$data['image_source'] = $this->embed_video->get_player('728','546');
			
			$data['detail_content'] = $rst[0];
			
			$data['news_id'] = $id;
			
			$data['comments']  = $this->fetch_comment($id);
			
			$res = $this->landing_page_cms_model->get_all_news_cat();
			$data['artical_cat_name']= $res;
			
			$res_watch = $this->landing_page_cms_model->get_all_video_cat();
			$data['watch_cat_name']	= $res_watch;
			
			$res_listen = $this->landing_page_cms_model->get_all_audio_cat();
			$data['listen_cat_name'] = $res_listen; 
		    $data['right_content']=$this->landing_page_cms_model->get_suggestion_video($id);
		   
           $VIEW = "logged/media_center/watch_details.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 
    }   // end of christian_news_details
	
	
	public function comment_videos()
    {
        
        $info['i_news_id'] = $this->input->post('news_id');
        $info['i_posted_user_id'] = $this->i_profile_id;
        $info['s_contents'] = get_formatted_string($this->input->post('comment'));
        $info['dt_commented_on'] = get_db_datetime();
        
        if($info['s_contents']!='')
        {
            $res = $this->landing_page_cms_model->comment_videos($info);
            if($res)
            {
                $success = true;
                $msg = "Successfully updated!";
            }
            else
            {
                $success = false;
                $msg = "Sorry. Try again.";
            }
        }
            
        else
        {
            $success = false;
            $msg = "Comment content should not be blank.";
        }
        $no_of_comments = $this->landing_page_cms_model->get_total_comment_by_video_id($info['i_news_id']);
        $html  = $this->fetch_comment($info['i_news_id']);      
        
        
        echo json_encode(array('result'=>$success,
                                'msg'=>$msg,
                                'comments'=>$no_of_comments,
								'html'=>$html
                                )
                        );
     
    }
	
	public function fetch_comment($id='')
	{
		try
		  {
			  $data = $this->data; 
			  $html  = ''; 
			  $data['news_id']	= $news_id;
			  $data['result'] = $this->landing_page_cms_model->get_comment_by_video_id($id);
			
			  $VIEW_FILE = 'logged/media_center/news_comment.phtml';
			  $html = $this->load->view($VIEW_FILE, $data,true);
			  return $html;
		   } 
			catch(Exception $err_obj)
			{
			  show_error($err_obj->getMessage());
			} 
			
	} 
 
}   // end of controller...




