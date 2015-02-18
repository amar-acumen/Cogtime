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

class User_twitter_profile extends Base_controller
{
    private $pagination_per_page= 10;
	private $trends_pagination_per_page = 40;
    
    public function __construct()
     {
        try
        {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...
            $this->load->model('users_model');
			$this->load->model('contacts_model');
			$this->load->model('my_tweet_model');
			$this->load->model('netpals_model');
			$this->load->model('user_notifications_model');
			$this->load->model('my_tweet_model');
			$this->load->model('tweet_reply_model');
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    
    public function index($i_user_id='') 
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
                                        'js/lightbox.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',
										'js/jquery.autofill.js',
										
										'js/tab.js',
										'js/jquery-ui.triggeredAutocomplete.js',
										'js/frontend/logged/tweets/tweets.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
										'css/jquery.autocomplete.css',
                                          'css/dd.css') );
           
			
			
			$data['public_profile_info'] = $this->users_model->fetch_this($i_user_id);
			$data['profile_id'] = $i_user_id;
			$data['is_follow'] = $this->my_tweet_model->isFollowing($i_user_id);
			$data['total_my_tweets'] = $this->my_tweet_model->get_total_all_tweets_by_user_id($i_user_id);
			$data['total_followers'] = $this->my_tweet_model->get_followers_list_count($i_user_id);
			$data['total_following'] = $this->my_tweet_model->get_following_list_count($i_user_id);
			$data['home_menu'] = '1';
			
			$data['pagination_per_page'] = $this->pagination_per_page;
			$this->session->set_userdata('search_condition' ,'');
			
           	ob_start();
		    $this->all_tweets_ajax_pagination($i_user_id);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['tweets_ajax_content'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
		   
		   	
            # view file...
            $VIEW = "logged/my_tweet/user-twitter-profile.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }   
    
    public function all_tweets_ajax_pagination($i_user_id, $page=0)
    {
		
		//echo $page;
		## seacrh conditions : filter ############
		  $WHERE_COND = '';
		   if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) :
			  
			  $txt_keyword = get_formatted_string(trim($this->input->post('txt_keyword')));
			  $WHERE_COND .= ($txt_keyword=='' || $txt_keyword == 'Keywords')?'':" AND ( t.s_tweet_text LIKE '%".$txt_keyword."%'  
			  											  OR CONCAT(u.s_first_name,' ',u.s_last_name) LIKE  '%".$txt_keyword."%')";
			  $this->session->set_userdata('search_condition',$WHERE_COND);
		  
		  
		 endif;  
		  $s_where = $this->session->userdata('search_condition');
		
		$cur_page = $page + $this->pagination_per_page;
		
		$data = $this->data;
		
       
		$result = $this->my_tweet_model->get_all_tweets_by_user_id($i_user_id,$s_where, intval($page), $this->pagination_per_page);
		//echo $this->db->last_query();
		$total_rows = $this->my_tweet_model->get_total_all_tweets_by_user_id($i_user_id,$s_where);
		#pr($result,1);
		$data['arr_tweets'] = $result;
		$data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
		$data['profile_id'] = $i_user_id;
		
		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->pagination_per_page)
			  $view_more = false;
         //--------- end check
		
		
		$VIEW_FILE = "logged/my_tweet/user_all_tweets_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$content, 'no_of_result'=>$data['no_of_result'],'view_more'=>$view_more, 'cur_page'=>$data['current_page_1']) );
			
	}
	
	public function my_followings($i_user_id) 
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
                                        'js/lightbox.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',
										'js/jquery.autofill.js',
										'js/tab.js',
										'js/jquery-ui.triggeredAutocomplete.js',
										'js/frontend/logged/tweets/tweets.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css') );
										  
										  
           
			
			$data['pagination_per_page'] = $this->pagination_per_page;
			
			$data['public_profile_info'] = $this->users_model->fetch_this($i_user_id);
			$data['profile_id'] = $i_user_id;
			$data['is_follow'] = $this->my_tweet_model->isFollowing($i_user_id);
			$data['total_my_tweets'] = $this->my_tweet_model->get_total_my_tweets($i_user_id);
			$data['total_followers'] = $this->my_tweet_model->get_followers_list_count($i_user_id);
			$data['total_following'] = $this->my_tweet_model->get_following_list_count($i_user_id);
			 
			$this->session->set_userdata('search_condition', '');  
           	ob_start();
			$this->my_followings_ajax_pagination($i_user_id);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['tweets_ajax_content'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
		   
		   	
            # view file...
            $VIEW = "logged/my_tweet/public_followings.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }   

	
	
	
	public function my_followings_ajax_pagination($i_user_id,$page=0)
    {
		
		//echo $page;
	
		$s_where = $this->session->userdata('search_condition');
		$cur_page = $page + $this->pagination_per_page;
		
		$data = $this->data;
		
		$result = $this->my_tweet_model->get_following_list($i_user_id,$s_where, intval($page), $this->pagination_per_page);
		//echo $this->db->last_query();
		$total_rows = $this->my_tweet_model->get_following_list_count($i_user_id,$s_where);
		//pr($result,1);
		$data['arr_followings'] = $result;
		$data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
		$data['profile_id'] = $i_user_id;
		
		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->pagination_per_page)
			  $view_more = false;
         //--------- end check
		
		
		$VIEW_FILE = "logged/my_tweet/public_followings_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$content, 'current_page'=>$cur_page, 'no_of_result'=>$data['no_of_result'],'total'=>$total_rows,'view_more'=>$view_more  ,'cur_page'=>$data['current_page_1']) );
			
	}
	
	
	
	public function my_followers($i_user_id) 
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
                                        'js/lightbox.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',
										'js/jquery.autofill.js',
										'js/tab.js',
										'js/jquery-ui.triggeredAutocomplete.js',
										'js/frontend/logged/tweets/tweets.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css') );
										  
										  
          
			
			$data['pagination_per_page'] = $this->pagination_per_page;
			$data['public_profile_info'] = $this->users_model->fetch_this($i_user_id);
			$data['profile_id'] = $i_user_id;
			$data['is_follow'] = $this->my_tweet_model->isFollowing($i_user_id);
			$data['total_my_tweets'] = $this->my_tweet_model->get_total_my_tweets($i_user_id);
			$data['total_followers'] = $this->my_tweet_model->get_followers_list_count($i_user_id);
			$data['total_following'] = $this->my_tweet_model->get_following_list_count($i_user_id);
			 
			 
			$this->session->set_userdata('search_condition', ''); 
           	ob_start();
			$this->my_followers_ajax_pagination($i_user_id);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['tweets_ajax_content'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
		   
		   	
            # view file...
            $VIEW = "logged/my_tweet/public_followers.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }   

	
	
	
	public function my_followers_ajax_pagination($i_user_id , $page=0)
    {
		
		//echo $page;
		
		$s_where = $this->session->userdata('search_condition');
		$cur_page = $page + $this->pagination_per_page;
		
		$data = $this->data;
		
		$result = $this->my_tweet_model->get_followers_list($i_user_id,$s_where, intval($page), $this->pagination_per_page);
		///echo $this->db->last_query();
		$total_rows = $this->my_tweet_model->get_followers_list_count($i_user_id,$s_where);
		//pr($result,1);
		$data['arr_followers'] = $result;
		$data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
		$data['profile_id'] = $i_user_id;
		
		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->pagination_per_page)
			  $view_more = false;
         //--------- end check
		
		
		$VIEW_FILE = "logged/my_tweet/public_followers_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
        echo json_encode( array('html'=>$content, 'current_page'=>$cur_page, 'no_of_result'=>$data['no_of_result'],'total'=>$total_rows,'view_more'=>$view_more  ,'cur_page'=>$data['current_page_1']) );
			
	}


	 public function follow_user()
	 {
			
			$data = $this->data;
			$page  =  intval($this->input->post('cur_page'));
			$i_status = trim($this->input->post('i_status'));
			$acceptor_id = $this->input->post('user_id');
			$user_id = intval(decrypt($this->session->userdata('user_id')));
			
			
			
			if($this->session->userdata('user_id') !="")
			{
				
				if($i_status=='follow')
				   {
					    $arr = array();
						$arr['i_accepter_id '] = $acceptor_id;
						$arr['i_requester_id'] = $user_id;
						$arr['i_current_status'] = 1;
						$arr['dt_unfollow_on'] = '0000-00-00 00:00:00';
						$arr['dt_created_on'] = get_db_datetime(); 
					 	
						## check if previous follow record exist then update  else  insert new follow
						
						$is_already_followed = $this->my_tweet_model->isUnfollowedEarlier($acceptor_id); 
						
						if($is_already_followed)
							$this->my_tweet_model->update_follow_user($user_id, $acceptor_id);	
						else
							$this->my_tweet_model->insert_follow($arr);	
						
						$action_txt = '<span class="follow-btn"><input name="Unfollow" type="button" value="Unfollow"  onclick="javascript:followUser('.$acceptor_id.',\'Unfollow\')"/></span>';
					
						$SUCCESS_MSG = "Followed successfully.";
						
				   }
				 else if($i_status=='Unfollow')
				   {
					    $this->my_tweet_model->unfollow_user($user_id, $acceptor_id);	
						
						$action_txt = '<span class="follow-btn"><input name="Follow" type="button" value="Follow"  onclick="javascript:followUser('.$acceptor_id.' , \'follow\' )"/></span>';
					   
						$SUCCESS_MSG = "Unfollowed successfully.";
						
				   } 
			}
			else{
			    
				$SUCCESS_MSG = "An error has occured! please try again. ";
				echo json_encode(array('result'=>false,
                					   'u_id'=>$acceptor_id,
									   
									   'action_txt' =>$action_txt,
                					   'msg'=>$SUCCESS_MSG )); exit;
			}
			
			$total_follower = $this->my_tweet_model->get_followers_list_count($acceptor_id);
			
			## showing total following on my fllowing secion 
			$total_following = $this->my_tweet_model->get_following_list_count($user_id);
			
			echo json_encode(array('result'=>true,
								   'u_id'=>$acceptor_id,
								   'action_txt' =>$action_txt,
								   'msg'=>$SUCCESS_MSG ,
								   'total_follower'=>$total_follower,
								   'total_following'=>$total_following));
	 }
}   // end of controller...

