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


class Listen extends Base_controller
{
    
    private $pagination_per_page =  10;
    private $comments_pagination_per_page =  2 ;
    private $people_liked_pagination_per_page =  4 ;
   
    
    public function __construct()
     {
	 	
        try
        {
            parent::__construct();
               parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...
			
            $this->load->model('users_model');
			
			$this->load->model('user_notifications_model');
            $this->load->model('user_alert_model');
			$this->load->model('landing_page_cms_model');
			$this->i_profile_id = intval(decrypt($this->session->userdata('user_id')));
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
          
	      
   # pr($data['blogdata']);
    
            $this->session->set_userdata('where','');
            
            if( $is_ajax=='N')
                $selected_cat = $this->session->userdata('category');
            
            $this->session->set_userdata('category','');
    
         
            
            # view file...
			
			$res = $this->landing_page_cms_model->get_all_audio_cat();
			foreach($res as $v)
			{
				$data['cat_name'][$v[id]]	= $v['s_cat_name'];
			}
			$data['cat1_article']	=	$this->landing_page_cms_model->get_all_audios(" where v.i_category_id=1" ,0,4,'');
			//pr($data['cat1_article'],1);
			$data['cat2_article']	=	$this->landing_page_cms_model->get_all_audios(" where v.i_category_id=2 ",0,4,'');
			$data['cat3_article']	=	$this->landing_page_cms_model->get_all_audios(" where v.i_category_id=3 ",0,4,'');
			$data['cat4_article']	=	$this->landing_page_cms_model->get_all_audios(" where v.i_category_id=4 ",0,4,'');
			$data['cat5_article']	=	$this->landing_page_cms_model->get_all_audios(" where v.i_category_id=5 ",0,4,'');
			$data['cat6_article']	=	$this->landing_page_cms_model->get_all_audios(" where v.i_category_id=6 ",0,4,'');
			
			$data['featured']		=	$this->landing_page_cms_model->get_all_audios(" where v.is_featured=1 ");
			
			
			//$data['listingContent'] = $content;
            $VIEW = "logged/media_center/listen.phtml";
            

                parent::_render($data, $VIEW);
            
        }
        
        catch(Exception $err_obj)
        {
           
        } 

    } // end of index   
    
 
    
    
    
    
    
    
    public function details($id,$cat)
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
            
           
           //####################### update view count ######################
           $this->landing_page_cms_model->update_view_count_by_news_id($id);
           //####################### end update view count ######################
           
           
           $data['pagination_per_page'] = $this->pagination_per_page;
           $rst = $this->landing_page_cms_model->get_all_audios(" where .id='".$id."'");
           $data['detail_content'] = $rst[0];
		   echo $data['ogimage'] = base_url() . 'uploads/media_center_article/' . getThumbName($data['detail_content']['s_image'], 'main');
		   $data['right_content']  = $this->landing_page_cms_model->get_all_audios(" where i_category='".$data['detail_content']['i_category_id']."'",0,7,'');
           
           $data['news_id'] = $id;
           
           $data['category'] = $cat;
           
           $this->session->set_userdata('category','');
           $this->session->set_userdata('category',$cat);
           
           
           //$cat_name = $this->landing_page_cms_model->get_category_name_by_category_id($cat);
           
           //$data['category_name'] = $cat_name;
			$data['category_name'] = $cat;
          
           $data['prev_url'] = base_url()."christian-news/".$cat."/".my_url($cat_name).".html";
			$data['comments']  = $this->NEW_fetch_comment_christian_news($id);
           $VIEW = "logged/media_center/audio.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 
    }   // end of christian_news_details
    
    
    
    
//================================= like n comments ====================================   
    public function like_news()
    {
        $info['i_news_id'] = $this->input->post('news_id');
        $info['i_liked_user_id'] = $this->i_profile_id;
        $info['dt_liked_on'] = get_db_datetime();
        
        $response = $this->landing_page_cms_model->whether_prev_liked($info['i_news_id'],$this->i_profile_id);
        
        if(count($response)==0)
        {
            $res = $this->landing_page_cms_model->like_news($info);
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
            
        }
            
        
        
        $no_of_likes = $this->landing_page_cms_model->get_total_people_liked_by_news_id($info['i_news_id']);
        echo json_encode(array('result'=>$success,
                                'msg'=>$msg,
                                'likes'=>$no_of_likes
                                )
                        );
    }                   // end like
    
    
    public function comment_news()
    {
        
        $info['i_news_id'] = $this->input->post('news_id');
        $info['i_posted_user_id'] = $this->i_profile_id;
        $info['s_contents'] = get_formatted_string($this->input->post('comment'));
        $info['dt_commented_on'] = get_db_datetime();
        
        if($info['s_contents']!='')
        {
            $res = $this->landing_page_cms_model->comment_news($info);
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
        $no_of_comments = $this->landing_page_cms_model->get_total_comment_by_news_id($info['i_news_id']);
        $html  = $this->NEW_fetch_comment_christian_news($info['i_news_id']);  
        
        
        echo json_encode(array('result'=>$success,
                                'msg'=>$msg,
                                'comments'=>$no_of_comments,
								'html'=>$html
                                )
                        );
     
    }
    
    
    
    
    //----------------------- fetch -----------------------------
    public function fetch_comment_on_news()
    {
        try
          {

             $data = $this->data;  
             $news_id = $this->input->post('news_id');
             
                 ob_start();
                 $this->comments_ajax_pagination($news_id);
                 $content = ob_get_contents();
                 $content_obj = json_decode($content);
                 $data['comments_list'] = $content_obj->html;
                 $data['view_more'] = $content_obj->view_more;
                 ob_end_clean();
             
              $VIEW = "logged/media_center/christian_news/comments/news_view_comments_lightbox.phtml";
              #parent::_render($data, $VIEW); 
              $html = $this->load->view($VIEW, $data, true);  
              echo json_encode( array('result'=>success,'html_data'=>$html) );
           } 
        catch(Exception $err_obj)
            {
              show_error($err_obj->getMessage());
            } 
            
    } 
    
      public function comments_ajax_pagination($news_id , $page=0) 
      {
         try
         {
         
            $data = $this->data;  
            $result = $this->landing_page_cms_model->get_comment_by_news_id($news_id , $page,
                                                                $this->comments_pagination_per_page);
            $resultCount = count($result);
            $total_rows = $this->landing_page_cms_model->get_total_comment_by_news_id($news_id);
     
        
            $current_page = $page+$this->comments_pagination_per_page;
            
            //--- for check whether more are there or not
            $view_more = true;
             $rest_counter = $total_rows-$page;
             if($rest_counter<=$this->comments_pagination_per_page)
                $view_more = false;
             
             //--------- end check
        

            
            $data['result_arr'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $current_page;
            $data['total_pages'] = ceil($total_rows/$this->comments_pagination_per_page);
            $data['view_more']=$view_more;
          
             $p = ($page/$this->comments_pagination_per_page);
             $data['current_loaded_page_no'] =  $p + 1;
            
            # rendering the view file...
            $VIEW_FILE = "logged/media_center/christian_news/comments/news_view_comments_lightbox_ajax.phtml";
            $html = $this->load->view($VIEW_FILE, $data,true);
            //return $html;
            
            echo json_encode(array(
                                    'html'=>$html,
                                    'current_page'=>$current_page,
                                    'view_more'=>$view_more
                                    )
                            );
            
        } 
        catch(Exception $err_obj)
        {
            
        } 
    
    }
    
    public function fetch_people_liked_post()
    {
        try
          {
               $data = $this->data;  
               $news_id = $this->input->post('news_id');
           
               ob_start();
                 $this->fetch_people_liked_post_ajax($news_id);
                 $content = ob_get_contents();
                 $content_obj = json_decode($content);
                 $data['people_liked_list'] = $content_obj->html;
                 $data['view_more'] = $content_obj->view_more;
                 ob_end_clean();
              
              $VIEW = "logged/media_center/christian_news/comments/liked_by_lightbox.phtml";
              #parent::_render($data, $VIEW); 
              $html = $this->load->view($VIEW, $data, true);  
              echo json_encode( array('result'=>success,'html_data'=>$html) );
           } 
        catch(Exception $err_obj)
            {
              show_error($err_obj->getMessage());
            } 
            
    } 
    
      public function fetch_people_liked_post_ajax($news_id , $page=0) 
      {
         try
         {
            $data = $this->data;  
            $result = $this->landing_page_cms_model->get_people_liked_by_news_id($news_id , $page,
                                                                $this->people_liked_pagination_per_page);
            $resultCount = count($result);
            $total_rows = $this->landing_page_cms_model->get_total_people_liked_by_news_id($news_id);
         
            $current_page = $page+$this->people_liked_pagination_per_page;
            
            //--- for check whether more are there or not
            $view_more = true;
             $rest_counter = $total_rows - $page;
             if($rest_counter <= $this->people_liked_pagination_per_page)
                $view_more = false;

             //--------- end check
         
         
            
            $data['result_arr'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $page;
            $data['total_pages'] = ceil($total_rows/$this->people_liked_pagination_per_page);
            
            $data['view_more'] = $view_more;
            $data['current_page'] = $current_page;
          
             $p = ($page/$this->people_liked_pagination_per_page);
             $data['current_loaded_page_no'] =  $p + 1;
            
            # rendering the view file...
            $VIEW_FILE = "logged/media_center/christian_news/comments/liked_by_lightbox_ajax.phtml";
            $html = $this->load->view($VIEW_FILE, $data,true);
            //return $html;
            echo json_encode(array(
                                    'html'=>$html,
                                    'current_page'=>$current_page,
                                    'view_more'=>$view_more
                                    )
                            );
            
        } 
        catch(Exception $err_obj)
        {
            
        } 
    
    }
//================================= end like n comments ====================================   
	
	public function new_fetch_likes_on_christian_news($news_id='')
	{
		try
		  {
			  $data = $this->data; 
			  $html = ''; 
			  $data['result'] = $this->landing_page_cms_model->get_people_liked_by_news_id($news_id);
			 
			  echo json_encode( array('result'=>'success','html_data'=>$html) );
		   } 
			catch(Exception $err_obj)
			{
			  show_error($err_obj->getMessage());
			} 
			
	} 
	
	########### NEW FETCH COMMENTS ON CHRISTIAN PROJECT METHOD ###########
	
	public function NEW_fetch_comment_christian_news($news_id='')
	{
		try
		  {
			  $data = $this->data; 
			  $html  = ''; 
			  $data['news_id']	= $news_id;
			  $data['result'] = $this->landing_page_cms_model->get_comment_by_news_id($news_id);
			
			  $VIEW_FILE = 'logged/media_center/news_comment.phtml';
			  $html = $this->load->view($VIEW_FILE, $data,true);
			  return $html;
		   } 
			catch(Exception $err_obj)
			{
			  show_error($err_obj->getMessage());
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
          
	      
   # pr($data['blogdata']);
    
            $this->session->set_userdata('cat',$id);
            
           
    
          
            
            # view file...
			
			$res = $this->landing_page_cms_model->get_all_news_cat();
			foreach($res as $v)
			{
				$cat_name[$v[id]]	= $v['s_cat_name'];
			}
			$data['cat_name'] = $cat_name[$id];
			$data['article']	=	$this->landing_page_cms_model->get_all_news(" where v.i_category_id='".$id."'" ,'','','');
			
			
			$data['is_video'] = 'n';
			//$data['listingContent'] = $content;
            $VIEW = "logged/media_center/read_category.phtml";
            

                parent::_render($data, $VIEW);
            
        }
        
        catch(Exception $err_obj)
        {
           
        } 

    } // end of index  
    
}   // end of controller...

