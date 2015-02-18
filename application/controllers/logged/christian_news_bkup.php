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


class Christian_news extends Base_controller
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

    
    public function index($cat_id='t',$cat_name='top-stories') 
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
										'js/tab.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css') );
            
           
		  
		   
           
           $data['pagination_per_page'] = $this->pagination_per_page;
           $data['all_categories'] = $this->landing_page_cms_model->get_all_news_cat();
	      
   # pr($data['blogdata']);
    
            $this->session->set_userdata('where','');
            
            if( $is_ajax=='N')
                $selected_cat = $this->session->userdata('category');
            
            $this->session->set_userdata('category','');
    
            if($cat_id=='t')
                $category_name = "Top Stories";
            else if($cat_id=='v')
                $category_name = "Most Viewed";
            else
                $category_name = $this->landing_page_cms_model->get_category_name_by_category_id($cat_id);
            
            $data['cat_name'] = $category_name;
            
            # view file...
			ob_start();
            $init_page = 0;
			$this->news_ajax($init_page, $cat_id);
            $content_obj = json_decode(ob_get_contents());
            $data['listingContent'] = $content_obj->html;
            $data['current_page'] = $content_obj->current_page;
            $data['view_more'] = $content_obj->view_more;
            
			ob_end_clean();
            
			//$data['listingContent'] = $content;
            $VIEW = "logged/media_center/christian_news/christian_news.phtml";
            

                parent::_render($data, $VIEW);
            
        }
        
        catch(Exception $err_obj)
        {
           
        } 

    } // end of index   
    
  

	


    
    public function news_ajax($page=0,$cat='t')
    {
        
        //echo "page : ".$page;exit;
		//echo "cat : ".$cat;exit;
        $where=' WHERE 1';
        $order_by = '';
        if($this->input->post('is_posted')=='y')
        {
             $cat = $this->input->post('cat');
        }     
        
         if($cat=='t')
         {
            $where .= " AND n.i_is_top_story = 1 ";
         }
         else if($cat=='v')
         {
             $order_by = "i_view_count DESC";
         }
         else
         {
             $where .= " AND n.i_category = '{$cat}'";
         }
           
        
        
        
            
        //$WHERE_COND.=$this->session->userdata('where',$where);
		$result = $this->landing_page_cms_model->get_all_news($where, $page, $this->pagination_per_page,$order_by);
//pr($result);
		$resultCount = count($result);
		$total_rows = $this->landing_page_cms_model->get_total_news($where);
		$cur_page = $page + $this->pagination_per_page;
        
        
        
         //--- for check whether more videos are there or not
            $view_more = true;
             $rest_counter = $total_rows-$page;
            if($rest_counter<=$this->pagination_per_page)
                $view_more = false;
             
             
             //--------- end check
        
        
        
		// getting auction-category listing...
		$data['news_arr'] = $result;
		$data['no_of_news'] = $total_rows;
		$data['no_of_result'] = $total_rows;
		$data['current_page'] = $cur_page;
        $data['view_more'] = $view_more;
        
        $data['category'] = $cat;
        
       
        # loading the view-part...
        $AJAX_VIEW_FILE = 'logged/media_center/christian_news/christian_news_ajax.phtml';
        $listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);
        
       
			 if( is_array($result) && count($result) ) 
			 	$Content	= $listingContent;
			 else
			 	$Content	= '<div class="content-box"><h3 style="margin-left:195px; margin-top:50px;">No News.</h3></div>';
			 echo json_encode( array('html'=>$Content, 'current_page'=>$cur_page, 'view_more'=>$view_more) );
		
    }
    
    
    
    
    
    
    
    public function christian_news_details($id,$cat)
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
           $data['all_categories'] = $this->landing_page_cms_model->get_all_news_cat();
           $data['detail_content'] = $this->landing_page_cms_model->get_news_info_by_news_id($id);
           
           $data['news_id'] = $id;
           
           $data['category'] = $cat;
           
           $this->session->set_userdata('category','');
           $this->session->set_userdata('category',$cat);
           
           if($cat=='t')
                $cat_name = 'Top Stories';
           else if($cat=='v')
                $cat_name = 'Most Viewed';
           else
           {
               $cat_name = $this->landing_page_cms_model->get_category_name_by_category_id($cat);
           }
           $data['category_name'] = $cat_name;
          
          
          $data['prev_url'] = base_url()."christian-news/".$cat."/".my_url($cat_name).".html";
  
           $VIEW = "logged/media_center/christian_news/christian_news_details.phtml"; 
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
            
        
        
        echo json_encode(array('result'=>$success,
                                'msg'=>$msg,
                                'comments'=>$no_of_comments
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
			  $result = $this->landing_page_cms_model->get_people_liked_by_news_id($news_id);
			 
			  if(count($result)){
				  foreach($result as $key=> $val){
					  
						 $name = $val['s_profile_name'];
						 $profile_image = get_profile_image($val['i_liked_user_id'],'thumb',$val['s_profile_photo']);
						 
					
						 
																
						$html .= '  <div class="user_div dp-list-user" style="width:212px !important;"> 
										<a href="javascript:void(0);">
										<div class="pro_photo3" style="background:url('.$profile_image.') no-repeat center;width:60px; height:60px;"></div>
										</a> 
										<a class="blue_link" href="javascript:void(0);">'.$name.'</a> 
									</div>';
									
						
							
						
					
				  }
				  $html .= '<br class="clr" />';
			  }
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
			  $result = $this->landing_page_cms_model->get_comment_by_news_id($news_id);
			
			// pr($result);
			  if(count($result)){
				  
				  foreach($result as $key=> $val){
					  
					 $profile_image_filename = get_profile_image($val['i_posted_user_id'],'thumb');
			 		 $DESC = html_entity_decode(htmlspecialchars_decode($val['s_contents']),ENT_QUOTES,'utf-8');
					 
					 
					 $html .= '<div class="txt_content01 comments-number-content"> 
					 			<a href="javascript:void(0);"><div style="background:url('.$profile_image_filename.') no-repeat center;width:60px; height:60px;" class="pro_photo3" ></div></a>
									<div>
										  <p class="blue_bold12"><a href="javascript:void(0);">'.$val['s_profile_name'].'</a></p>
										  <p>'.nl2br($DESC).'</p>
											 <p class="read-more txt">Updated on: '.get_time_elapsed($val['dt_commented_on']).'</p>
									</div>
									<div class="clr"></div>
							  </div>'; 
				  }
			  }
			 
			  echo json_encode( array('result'=>'success','html_data'=>$html) );
		   } 
			catch(Exception $err_obj)
			{
			  show_error($err_obj->getMessage());
			} 
			
	} 

    
    
}   // end of controller...

