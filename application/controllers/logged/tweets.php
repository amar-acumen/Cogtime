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

class Tweets extends Base_controller
{
    private $pagination_per_page= 5;
    
    public function __construct()
     {
        try
        {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...
            $this->load->model('users_model');
			$this->load->model('contacts_model');
			$this->load->model('tweet_model');
			$this->load->model('netpals_model');
			$this->load->model('user_notifications_model');
			$this->load->model('my_tweet_model');
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
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',
										'js/jquery.autofill.js',*/
										'js/frontend/logged/tweets/tweets.js',
										//'js/tab.js'
                                        ));
                                        
           // parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
           //                               'css/dd.css') );
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			
			 $this->session->set_userdata('search_condition','');
			 
			$data['pagination_per_page'] = $this->pagination_per_page;
			$data['profile_id'] = $i_user_id;
			
           	ob_start();
		//	$this->all_tweets_ajax_pagination($i_user_id);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['tweets_ajax_content'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
		   
		   	
            # view file...
            $VIEW = "logged/my_tweet/my-twitter-profile.phtml"; 
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
		
       
		$result = $this->tweet_model->get_all_tweets_by_user_id($i_user_id,$s_where, intval($page), $this->pagination_per_page);
		//echo $this->db->last_query();
		$total_rows = $this->tweet_model->get_total_all_tweets_by_user_id($i_user_id,$s_where);
		//pr($result,1);
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
		
		
		$VIEW_FILE = "logged/my_tweet/mix_tweets_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$content, 'no_of_result'=>$data['no_of_result'],'view_more'=>$view_more, 'cur_page'=>$data['current_page_1']) );
			
	}
	
	
	
	public function my_tweets() 
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
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',*/
										'js/frontend/logged/tweets/tweets.js',
										//'js/tab.js'
                                        ));
                                        
//            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
//                                          'css/dd.css') );
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			$this->session->set_userdata('search_condition','');
			$data['pagination_per_page'] = $this->pagination_per_page;
			$data['profile_id'] = $i_user_id;
			 
           	ob_start();
			$this->my_tweets_ajax_pagination($i_user_id);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['tweets_ajax_content'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
		   
		   	
            # view file...
            $VIEW = "logged/my_tweet/my_tweet.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }   

	
	
	
	public function my_tweets_ajax_pagination($i_user_id, $page=0)
    {
		
		
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
		
		$result = $this->tweet_model->get_my_tweets($i_user_id,$s_where, intval($page), $this->pagination_per_page);
		//echo $this->db->last_query();
		$total_rows = $this->tweet_model->get_total_my_tweets($i_user_id,$s_where);
		//pr($result,1);
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
		
		
		$VIEW_FILE = "logged/my_tweet/my_tweets_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
        echo json_encode( array('html'=>$content, 'current_page'=>$cur_page, 'no_of_result'=>$data['no_of_result'],'total'=>$total_rows,'view_more'=>$view_more  ,'cur_page'=>$data['current_page_1']) );
			
	}
	
	
	
	public function my_friends_tweets() 
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
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',*/
										'js/frontend/logged/tweets/tweets.js',
										//'js/tab.js'
                                        ));
                                        
//            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
//                                          'css/dd.css') );
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			$this->session->set_userdata('search_condition','');
			$data['pagination_per_page'] = $this->pagination_per_page;
			$data['profile_id'] = $i_user_id;
			 
           	ob_start();
			$this->my_friends_tweets_ajax_pagination($i_user_id);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['tweets_ajax_content'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
		   
		   	
            # view file...
            $VIEW = "logged/my_tweet/friends_tweet.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }
	
	
	public function my_friends_tweets_ajax_pagination($i_user_id, $page=0)
    {
		
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
		
		$result = $this->tweet_model->get_friends_netpals_tweets_by_user_id($i_user_id,$s_where, intval($page), $this->pagination_per_page);
		$total_rows = $this->tweet_model->get_total_friends_netpals_tweets_by_user_id($i_user_id,$s_where);
		//pr($result,1);
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
		
		
		$VIEW_FILE = "logged/my_tweet/friends_tweets_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
        echo json_encode( array('html'=>$content, 'current_page'=>$cur_page, 'no_of_result'=>$data['no_of_result'],'total'=>$total_rows,'view_more'=>$view_more ,'cur_page'=>$data['current_page_1']) );
			
	}
	
	
	
	### POSTING TWEETS 
	
	public function post_tweet($posted_from ,$page) 
	{
		$user_id = intval(decrypt($this->session->userdata('user_id')));
		$user_details = $this->users_model->fetch_this($user_id);

		$message = nl2br( htmlspecialchars(trim($this->input->post('message')), ENT_QUOTES, 'utf-8') );
		if(trim($this->input->post('message')) == 'Max 140 Characters'){
				$message ='';
		}
        $_html='';
		if($message!='')
		    {
				
				
				
				$arr['i_user_id'] = $user_id;
				$arr['s_tweet_text'] = $message;
				$arr['dt_created_on'] = get_db_datetime();
				$tweet_id = $this->tweet_model->insert($arr);
				
				if($posted_from == 'all_tweet'){
				  ob_start();
				  $this->all_tweets_ajax_pagination($user_id,$page);
				  $content = ob_get_contents();
				  $content_obj = json_decode($content);
				  $_html = $content_obj->html; 
				  $view_more = $content_obj->view_more;
				  $cur_page = $content_obj->cur_page;
				  ob_end_clean();
				}
				else if($posted_from == 'my_tweets' || $posted_from == 'right_bar'){
				  ob_start();
				  $this->my_tweets_ajax_pagination($user_id,$page);
				  $content = ob_get_contents();
				  $content_obj = json_decode($content);
				  $_html = $content_obj->html; 
				  $view_more = $content_obj->view_more;
				  $cur_page = $content_obj->cur_page;
				  ob_end_clean();
				}
				/*else if($posted_from == 'my_friends'){
				  ob_start();
				  $this->my_friends_tweets_ajax_pagination($user_id,$page);
				  $content = ob_get_contents();
				  $content_obj = json_decode($content);
				  $_html = $content_obj->html; 
				  $view_more = $content_obj->view_more;
				  $cur_page = $content_obj->cur_page;
				  ob_end_clean();
				}*/
				$right_tweet_text = my_substr(nl2br($arr['s_tweet_text']),50);
				
				$tweet_owner_info = $this->tweet_model->get_owner_by_tweet_id($tweet_id);
				if($tweet_owner_info['i_owner_id'] != intval(decrypt($this->session->userdata('user_id')))){
					$notification_arr['i_requester_id'] = intval(decrypt($this->session->userdata('user_id')));
					$notification_arr['i_accepter_id'] = $tweet_owner_info['i_owner_id'];
					$notification_arr['s_type'] = 'tweet_comment';
					$notification_arr['dt_created_on'] = get_db_datetime();
					
					$ret = $this->user_notifications_model->insert($notification_arr);
				}
                
                
				echo json_encode( array('success'=>'true', 'msg'=>"Tweet posted successfully.",'html'=>$_html,'view_more'=>$view_more,'cur_page'=>$cur_page, 'right_tweet_text'=>$right_tweet_text ) );
			}
			 else
		   {
			  echo json_encode( array('success'=>'false', 'msg'=>"Please enter some text.", 'html'=>$_html) );
		   }
		
	}
	
	
    public function delete_information ($id)
    {
		$i_ret=$this->tweet_model->delete_by_id($id);
		$re_page =  base_url() ."my-tweets.html";
					header("location:".$re_page);
					exit;
		
	} 
	
	
	### NEW METHOD TO POST TWEETS....
	
	public function post_his_tweets()
	{
		$user_id = intval(decrypt($this->session->userdata('user_id')));
		$user_details = $this->users_model->fetch_this($user_id);

		$message = nl2br( htmlspecialchars(trim($this->input->post('message')), ENT_QUOTES, 'utf-8') );
		if(trim($this->input->post('message')) == 'Max 140 Characters'){
				$message ='';
		}
        $_html='';
		if($message!='')
		    {
				
				$json_data['user_id'] = $user_id;
				$json_data['s_tweet_text'] = $message;
				
				$arr['i_owner_id'] = $user_id;
				if($wall_type == 'public_tweet'){
					$json_data['tweet_owner_id'] = ($this->input->post('public_profile_id')); 
				}
			   
				$arr['data'] = json_encode( $json_data );
				$arr['dt_created_on'] = get_db_datetime();
				$arr['i_owner_id'] = $user_id;
				
				$tweet_id = $this->tweet_model->insert($arr);
				
				if($posted_from == 'all_tweet'){
				  ob_start();
				  $this->all_tweets_ajax_pagination($user_id,$page);
				  $content = ob_get_contents();
				  $content_obj = json_decode($content);
				  $_html = $content_obj->html; 
				  $view_more = $content_obj->view_more;
				  $cur_page = $content_obj->cur_page;
				  ob_end_clean();
				}
				else if($posted_from == 'my_tweets' || $posted_from == 'right_bar'){
				  ob_start();
				  $this->my_tweets_ajax_pagination($user_id,$page);
				  $content = ob_get_contents();
				  $content_obj = json_decode($content);
				  $_html = $content_obj->html; 
				  $view_more = $content_obj->view_more;
				  $cur_page = $content_obj->cur_page;
				  ob_end_clean();
				}
				/*else if($posted_from == 'my_friends'){
				  ob_start();
				  $this->my_friends_tweets_ajax_pagination($user_id,$page);
				  $content = ob_get_contents();
				  $content_obj = json_decode($content);
				  $_html = $content_obj->html; 
				  $view_more = $content_obj->view_more;
				  $cur_page = $content_obj->cur_page;
				  ob_end_clean();
				}*/
				$right_tweet_text = my_substr(nl2br($arr['s_tweet_text']),50);
				
				$tweet_owner_info = $this->tweet_model->get_owner_by_tweet_id($tweet_id);
				if($tweet_owner_info['i_owner_id'] != intval(decrypt($this->session->userdata('user_id')))){
					$notification_arr['i_requester_id'] = intval(decrypt($this->session->userdata('user_id')));
					$notification_arr['i_accepter_id'] = $tweet_owner_info['i_owner_id'];
					$notification_arr['s_type'] = 'tweet_comment';
					$notification_arr['dt_created_on'] = get_db_datetime();
					
					$ret = $this->user_notifications_model->insert($notification_arr);
				}
                
                
				echo json_encode( array('success'=>'true', 'msg'=>"Tweet posted successfully.",'html'=>$_html,'view_more'=>$view_more,'cur_page'=>$cur_page, 'right_tweet_text'=>$right_tweet_text ) );
			}
			 else
		   {
			  echo json_encode( array('success'=>'false', 'msg'=>"Please enter some text.", 'html'=>$_html) );
		   }
		
	}
	
}   // end of controller...

