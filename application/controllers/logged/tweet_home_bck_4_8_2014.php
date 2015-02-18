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

class Tweet_home extends Base_controller
{
    private $pagination_per_page= 10;
	private $trends_pagination_per_page = 40;
	private $trends_search_pagination_per_page = 5;
    
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
			$this->load->model('user_alert_model');
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
                                        'js/lightbox.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',
										'js/jquery.autofill.js',
										
										'js/tab.js',
										
										'js/jquery-ui.triggeredAutocomplete.js',
										'js/frontend/logged/tweets/tweets.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.13.custom.css',
										'css/jquery.autocomplete.css',
                                          'css/dd.css') );
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			
			 $this->session->set_userdata('search_condition','');
			 
			$data['pagination_per_page'] = $this->pagination_per_page;
			$data['profile_id'] = $i_user_id;
			
			$data['total_my_tweets'] = $this->my_tweet_model->get_total_my_tweets($i_user_id);
			
			$data['total_followers'] = $this->my_tweet_model->get_followers_list_count($i_user_id);
			$data['total_following'] = $this->my_tweet_model->get_following_list_count($i_user_id);
			
			
           	ob_start();
		    $this->all_tweets_ajax_pagination($i_user_id);
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
		
       
		$result = $this->my_tweet_model->get_all_tweets_by_user_id($i_user_id,$s_where, intval($page), $this->pagination_per_page);
		//echo $this->db->last_query();
		$total_rows = $this->my_tweet_model->get_total_all_tweets_by_user_id($i_user_id,$s_where);
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
		
		
		$VIEW_FILE = "logged/my_tweet/home_all_tweets_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
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
        
            
            parent::_add_js_arr( array( 'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',
										'js/jquery.autofill.js',
										
										'js/tab.js',
										
										'js/jquery-ui.triggeredAutocomplete.js',
										'js/frontend/logged/tweets/tweets.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.13.custom.css',
                                          'css/dd.css') );
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			$this->session->set_userdata('search_condition','');
			$data['pagination_per_page'] = $this->pagination_per_page;
			$data['profile_id'] = $i_user_id;
			
			$data['total_my_tweets'] = $this->my_tweet_model->get_total_my_tweets($i_user_id);
			$data['total_followers'] = $this->my_tweet_model->get_followers_list_count($i_user_id);
			$data['total_following'] = $this->my_tweet_model->get_following_list_count($i_user_id);
			 
           	ob_start();
			$this->my_tweets_ajax_pagination($i_user_id);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['tweets_ajax_content'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
		   
		   	
            # view file...
            $VIEW = "logged/my_tweet/my_tweets.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }   

	
	
	
	public function my_tweets_ajax_pagination($i_user_id, $page=0)
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
		
		$result = $this->my_tweet_model->get_my_tweets($i_user_id,$s_where, intval($page), $this->pagination_per_page);
		//echo $this->db->last_query();
		$total_rows = $this->my_tweet_model->get_total_my_tweets($i_user_id,$s_where);
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
		
		
		$VIEW_FILE = "logged/my_tweet/my_all_tweets_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
        echo json_encode( array('html'=>$content, 'current_page'=>$cur_page, 'no_of_result'=>$data['no_of_result'],'total'=>$total_rows,'view_more'=>$view_more  ,'cur_page'=>$data['current_page_1']) );
			
	}
	
	
	
	### POSTING TWEETS 

    public function delete_information ($id)
    {
		$i_ret=$this->my_tweet_model->delete_by_id($id);
		$re_page =  base_url() ."my-tweets.html";
					header("location:".$re_page);
					exit;
		
	} 
	
	
	### NEW METHOD TO POST TWEETS....
	
	public function post_his_tweets($posted_from ,$page)
	{
		$user_id = intval(decrypt($this->session->userdata('user_id')));
		$user_details = $this->users_model->fetch_this($user_id);

		 #$message = nl2br( htmlspecialchars(trim($this->input->post('message')), ENT_QUOTES, 'utf-8') ); 
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
				
				$tweet_id = $this->my_tweet_model->insert($arr);
				
				
				$tweet_trends = get_twitter_tags($message); 
				
				## INSERTING TRENDS 
				if(count($tweet_trends)){
					foreach($tweet_trends as $tags){
						
						## check if already exists in db 
						$isExists = $this->my_tweet_model->ifTagExists($tags);
						
						if($isExists ==  0){
							$trend = array();
							$trend['s_tags'] = $tags;
						  	$trend['dt_created_on'] = get_db_datetime();
						  	$i_trend_id = $this->my_tweet_model->insert_tags($trend);
						}
						else{
							$i_score = 1+$this->my_tweet_model->getScore($tags);
							$this->my_tweet_model->update_tags_score($i_score , $tags);
						}
					}
				}
				
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
				else if($posted_from == 'my_tweets' ){
				  ob_start();
				  $this->my_tweets_ajax_pagination($user_id,$page);
				  $content = ob_get_contents();
				  $content_obj = json_decode($content);
				  $_html = $content_obj->html; 
				  $view_more = $content_obj->view_more;
				  $cur_page = $content_obj->cur_page;
				  ob_end_clean();
				}
				else if($posted_from == 'right_bar'){
		   
				  ob_start();
				  $this->right_tweets_ajax_pagination($user_id, $page);
				  $content = ob_get_contents();
				  $content_obj = json_decode($content);
				  $_html = $content_obj->html; 
				  $view_more = $content_obj->view_more;
				  $cur_page = $content_obj->cur_page;
				  ob_end_clean();
				}
				$right_tweet_text = my_substr(nl2br($json_data['s_tweet_text']),50);
				$right_tweet_date  = get_time_elapsed($arr['dt_created_on']);
				
				$tweet_owner_info = $this->my_tweet_model->get_owner_by_tweet_id($tweet_id);
				if($tweet_owner_info['i_owner_id'] != intval(decrypt($this->session->userdata('user_id')))){
					$notification_arr['i_requester_id'] = intval(decrypt($this->session->userdata('user_id')));
					$notification_arr['i_accepter_id'] = $tweet_owner_info['i_owner_id'];
					$notification_arr['s_type'] = 'tweet_comment';
					$notification_arr['dt_created_on'] = get_db_datetime();
					
					$ret = $this->user_notifications_model->insert($notification_arr);
				}
                
                $total_my_tweets = $this->my_tweet_model->get_total_my_tweets($user_id);
				
				echo json_encode( array('success'=>'true', 'msg'=>"Tweet posted successfully.",'html'=>$_html,'view_more'=>$view_more,'cur_page'=>$cur_page,'total_my_tweets'=>$total_my_tweets) );
			}
			 else
		   {
			  echo json_encode( array('success'=>'false', 'msg'=>"Please enter some text.", 'html'=>$_html) );
		   }
		
	}
	
	
	### post reply ona tweet 
	
	
	public function post_reply($tweet_id)
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
				$arr = array();		
				$arr['i_user_id'] = $user_id;
				$arr['s_contents'] = $message;
				
				$arr['dt_created_on'] = get_db_datetime();
				$arr['i_tweet_id'] = $tweet_id;
				
				$tweet_reply_id = $this->tweet_reply_model->insert($arr);
				$tweet_trends = get_twitter_tags($message); 
				
				## INSERTING TRENDS 
				if(count($tweet_trends)){
					foreach($tweet_trends as $tags){
						
						## check if already exists in db 
						$isExists = $this->my_tweet_model->ifTagExists($tags);
						
						if($isExists ==  0){
							$trend = array();
							$trend['s_tags'] = $tags;
						  	$trend['dt_created_on'] = get_db_datetime();
						  	$i_trend_id = $this->my_tweet_model->insert_tags($trend);
						}
						else{
							$i_score = 1+$this->my_tweet_model->getScore($tags);
							$this->my_tweet_model->update_tags_score($i_score , $tags);
						}
					}
				}
				
				## fetch tweeter owner id
				$tweet_info =  $this->my_tweet_model->get_by_id($tweet_id);
				
				$notificaion_opt = $this->user_alert_model->check_option_user_id($tweet_info['i_owner_id']);	
				$notification_arr = array();
		
				## insert noifications ####
					if($notificaion_opt['e_tweet_comments_received'] == 'Y' ){
						
						$notification_arr['i_requester_id'] = $user_id;
						$notification_arr['i_accepter_id'] = $tweet_info['i_owner_id'];
						$notification_arr['s_type'] = 'tweet_comment';
						$notification_arr['dt_created_on'] = get_db_datetime();
						
						
						$ret = $this->user_notifications_model->insert($notification_arr);	
						$message_id = parent::social_notifications_message($notification_arr['i_requester_id'], $notification_arr['i_accepter_id'], 'tweet_comment', $tweet_id);
					}
								
								
				 ### end  ###
                
				echo json_encode( array('success'=>'true', 'msg'=>"Reply posted successfully.",'html'=>$_html,'view_more'=>$view_more,'cur_page'=>$cur_page) );
			}
			 else
		   {
			  echo json_encode( array('success'=>'false', 'msg'=>"Please enter some text.", 'html'=>$_html) );
		   }
		
	}
	
	
	
	function viewTweetReply()
    {
         
         $tweet_id = $this->input->post('tweet_id');
         $tweet_reply_arr = $this->tweet_reply_model->get_by_tweet_id($tweet_id);
        // pr($tweet_reply_arr,1);
         if(count($tweet_reply_arr))
         {
             $des="";
             $counter=0;
             $count=count($tweet_reply_arr);
             foreach($tweet_reply_arr as $val)
             {
				 
				 $des .= '<div class="tweet-reply">
                          <p>'.nl2br($val['s_contents']).'</p>
                          <h2><a href="javascript:void(0);"><span>'.$val['s_profile_name'].'</span> '.$val['s_tweet_id'].'</a></h2>
                          <p class="date-time">'.get_time_elapsed($val['dt_created_on']).'</p>
                          </div>';
             }
			 
             echo json_encode(array('des'=>base64_encode($des)));
         }
         else
            echo json_encode(array('des'=>base64_encode('')));
    }
	
	
	public function show_trends($letter)
	{	$search_arr = array();
		$search_string = '';
		
		//echo $_REQUEST['term'].'@@';
		$letter = $_REQUEST['term'];//$_REQUEST['q'];
			
		$search_arr = get_twitter_uid($letter);
		//pr($search_arr);
		
		if(count($search_arr)){
			$where =' WHERE  ';
			
			foreach($search_arr as $key=>$tag_val){
				
				if($key ==  0){
					$where .= " T.s_tags like '{$tag_val}%' ";
				}else{
					$where .= "OR T.s_tags like '{$tag_val}%' ";
				}
				
			}
			//$search_string = implode(',',$search_arr);
			
		    $arr_trend = $this->my_tweet_model->getTrendList($where);
			//$this->users_model->getTweetConnectionName($where);
		
		#pr($arr_trend);
		  asort($arr_trend,SORT_REGULAR);
		  $sugg = array();
			if(count($arr_trend)>0){
				foreach($arr_trend as $key=> $val)
				{
					//echo  $val['s_tags']."\n";
					$sugg[$key]['value'] = $val['id'];
					$sugg[$key]['label'] = $val['s_tags'];
					#array_push($sugg['value'] ,$val['id']);
					#array_push($sugg['label'] ,$val['s_tags']);
					
				}
				 echo json_encode($sugg);
				exit;
			}
			else
				//echo 'no';
				echo json_encode(array('sugg_html'=>"No Data.",'success'=>false));
			exit;
		}
		else
			//echo 'XX'; 
			echo json_encode(array('sugg_html'=>"XX",'success'=>false));
			exit;
	}
	
	
	
	
	public function show_connections($letter)
	{	$search_arr = array();
		$search_string = '';
		
		//echo $_GET['term'].'@@';
	     $letter = $_GET['term'];//$_REQUEST['q'];
			
		$search_arr = get_twitter_uid($letter);
		//pr($search_arr,1);
		
		if(count($search_arr)){
			$where =' WHERE  ';
			
			foreach($search_arr as $key=>$tag_val){
				
				if($key ==  0){
					$where .= " s_tweet_id like '{$tag_val}%' ";
				}else{
					$where .= "OR s_tweet_id like '{$tag_val}%' ";
				}
				
			}
			//$search_string = implode(',',$search_arr);
			
			//$arr_trend = $this->my_tweet_model->getTrendList($where);
			$arr_trend = $this->users_model->getTweetConnectionName($where);
		
		#pr($arr_trend);
		  asort($arr_trend,SORT_REGULAR);
		  $sugg = array();
			if(count($arr_trend)>0){
				foreach($arr_trend as $key=> $val)
				{
					//echo  $val['s_tags']."\n";
					$sugg[$key]['value'] = $val['id'];
					$sugg[$key]['label'] = $val['s_tweet_id'];
					#array_push($sugg['value'] ,$val['id']);
					#array_push($sugg['label'] ,$val['s_tags']);
					
				}
				 echo json_encode($sugg);
				exit;
			}
			else
				//echo 'no';
				echo json_encode(array('sugg_html'=>"No Data.",'success'=>false));
			exit;
		}
		else
			//echo 'XX'; 
			echo json_encode(array('sugg_html'=>"XX",'success'=>false));
			exit;
	}
	
	
	### SEARCH PEOPLE ON COGTIME TWITTER
	
	
	
	public function search_people() 
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
										//'js/jquery-sew-master/jquery.caretposition.js',
										//'js/jquery-sew-master/jquery.sew.js',
										'js/jquery-ui.triggeredAutocomplete.js',
										'js/frontend/logged/tweets/tweets.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.13.custom.css',
                                          'css/dd.css') );
            
			
			$i_user_id = intval(decrypt($this->session->userdata('user_id')));
			
			$data['profile_id'] = $i_user_id;
			
			$data['total_my_tweets'] = $this->my_tweet_model->get_total_my_tweets($i_user_id);
			$data['total_followers'] = $this->my_tweet_model->get_followers_list_count($i_user_id);
			$data['total_following'] = $this->my_tweet_model->get_following_list_count($i_user_id);
			
			$this->session->set_userdata('search_condition','');
			$this->session->set_userdata('search_name' ,'');	
			
			$WHERE_COND = '';
			
			if($_POST['hd_search'] != '' && $_POST['hd_search'] == 'Y'){
				
				$txt_keyword = get_formatted_string(trim($this->input->post('searchTweeter')));
				$WHERE_COND .= ($txt_keyword=='' || $txt_keyword == 'Type Keyword')?'':
										" AND (CONCAT(u.s_first_name,' ',u.s_last_name) LIKE  '%".$txt_keyword."%'
												OR u.s_tweet_id  LIKE  '%".$txt_keyword."%')";
				
				$this->session->set_userdata('search_condition',$WHERE_COND);
				if($txt_keyword != 'Type Keyword'){
					$this->session->set_userdata('search_name' ,$txt_keyword);
				}
			}
			
			
		  
			
			$data['pagination_per_page'] = $this->pagination_per_page;
				 
		 
           	ob_start();
			$this->search_people_ajax_pagination(0);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['tweets_ajax_content'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
		   
		   	
            # view file...
            $VIEW = "logged/my_tweet/search_list.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }   

	
	
	
	public function search_people_ajax_pagination($page=0)
    {
		
		//echo $page;
		$i_user_id = intval(decrypt($this->session->userdata('user_id')));
		$s_where = " AND u.id != $i_user_id ";
		$s_where .= $this->session->userdata('search_condition');
		
		$cur_page = $page + $this->pagination_per_page;
		
		$data = $this->data;
		
        # echo $page ."--". $this->pagination_per_page ."<br />";
		if($this->session->userdata('search_condition') != ''){
			$result = $this->my_tweet_model->search_all_user($s_where, intval($page), $this->pagination_per_page);
			//echo $this->db->last_query();
			$total_rows = $this->my_tweet_model->get_total_search_all_user($s_where);
		}
		//pr($result,1);
		$data['srch_result'] = $result;
		$data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
		$data['profile_id'] = $i_user_id;
		
		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->pagination_per_page)
			  $view_more = false;
         //--------- end check
		
		
		$VIEW_FILE = "logged/my_tweet/search_list_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$content, 'current_page'=>$cur_page, 'no_of_result'=>$data['no_of_result'],'total'=>$total_rows,'view_more'=>$view_more  ,'cur_page'=>$data['current_page_1']) );
			
	}
	
	 public function markFavTweet()
	 {
			
			$data = $this->data;
			$page  =  intval($this->input->post('cur_page'));
			$i_status = intval($this->input->post('i_status'));
			$cur_status = intval($this->input->post('cur_status'));
			$ID = $this->input->post('record_id');
			$user_id = intval(decrypt($this->session->userdata('user_id')));
			
			
			
			if($this->session->userdata('user_id') !="")
			{
				
				if($i_status==1)
				   {
					    $arr = array();
						$arr['i_user_id'] = $user_id;
						$arr['i_tweet_id'] = $ID;
						$arr['dt_created_on'] = get_db_datetime(); 
						
					 	$this->my_tweet_model->insert_fav_tweets($arr);	
									 
						$action_txt =	 '<span id="'.$ID.'_status"><input name="favorite" type="button" value="Unmark" class="small-blue-btn" onclick="javascript:mark_fav_tweet('.$ID.',0,1)"/></span>';
					
						$SUCCESS_MSG = "Marked as favourite! ";
						$MARK_UNMARK_TXT = 'Unmark from your favourite tweet?';
				   }
				 else if($i_status==0)
				   {
					    $this->my_tweet_model->remove_from_fav_tweet($ID,$user_id);	
						
						$action_txt =	 '<span id="'.$ID.'_status"><input name="favorite" type="button" value="Mark" class="small-blue-btn" onclick="javascript:mark_fav_tweet('.$ID.',1,0)"/></span>';
					   
					    $MARK_UNMARK_TXT = 'Mark this as your favourite tweet?';
						$SUCCESS_MSG = "Unmarked from favourite! ";
				   } 
			}
			else{
			    
				$SUCCESS_MSG = "An error has occured! please try again. ";
				echo json_encode(array('result'=>false,
                					   'u_id'=>$ID,
									   
									   'action_txt' =>$action_txt,
									   'i_status' => $cur_status,
                					   'msg'=>$SUCCESS_MSG , 'redirect'=>true)); exit;
			}
			
			
			echo json_encode(array('result'=>'success',
								   'u_id'=>$ID,
								   'action_txt' =>$action_txt,
								   'MARK_UNMARK_TXT'=> $MARK_UNMARK_TXT,
								   'i_status' => $cur_status,
								   'msg'=>$SUCCESS_MSG ,'redirect'=>false));
	 }
	
	
	
	
	### RETWEETING
	public function retweet($posted_from ,$tweet_id)
	{
		$user_id = intval(decrypt($this->session->userdata('user_id')));
		$user_details = $this->users_model->fetch_this($user_id);

		### get tweet by id  : 
		$tweet_info = $this->my_tweet_model->get_by_id($tweet_id);
		
	//pr($tweet_info, 1);
		
		$tweet_data = json_decode($tweet_info['data']);
								
		$json_data['user_id'] = $user_id;
		$json_data['s_tweet_text'] = $tweet_data->s_tweet_text;
		
		if($tweet_info['s_type'] == 'retweeted'){
			$json_data['tweet_owner_id'] = $tweet_data->tweet_owner_id; 
		}
		else{
			$json_data['tweet_owner_id'] = $tweet_data->user_id;
		}
		$arr['i_owner_id'] = $user_id;
		
	   
		$arr['data'] = json_encode( $json_data );
		$arr['dt_created_on'] = get_db_datetime();
		$arr['i_owner_id'] = $user_id;
		$arr['s_type'] = 'retweeted';
		
		$tweet_id = $this->my_tweet_model->insert($arr);
		
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
		else if($posted_from == 'my_all_tweet'){
		  ob_start();
		  $this->my_tweets_ajax_pagination($user_id,$page);
		  $content = ob_get_contents();
		  $content_obj = json_decode($content);
		  $_html = $content_obj->html; 
		  $view_more = $content_obj->view_more;
		  $cur_page = $content_obj->cur_page;
		  ob_end_clean();
		}
		else if($posted_from == 'trend_search_tweet'){
		   
		  ob_start();
		  $this->search_trendings_ajax_pagination($page);
		  $content = ob_get_contents();
		  $content_obj = json_decode($content);
		  $_html = $content_obj->html; 
		  $view_more = $content_obj->view_more;
		  $cur_page = $content_obj->cur_page;
		  ob_end_clean();
		}
		else if($posted_from == 'fav_tweet'){
		   
		  ob_start();
		  $this->my_fav_tweets_ajax_pagination($user_id, $page);
		  $content = ob_get_contents();
		  $content_obj = json_decode($content);
		  $_html = $content_obj->html; 
		  $view_more = $content_obj->view_more;
		  $cur_page = $content_obj->cur_page;
		  ob_end_clean();
		}
		else if($posted_from == 'right_bar'){
		   
		  ob_start();
		  $this->right_tweets_ajax_pagination($user_id, $page);
		  $content = ob_get_contents();
		  $content_obj = json_decode($content);
		  $_html = $content_obj->html; 
		  $view_more = $content_obj->view_more;
		  $cur_page = $content_obj->cur_page;
		  ob_end_clean();
		}
		
		
		
		$tweet_owner_info = $this->my_tweet_model->get_owner_by_tweet_id($tweet_id);
		
		
		$notificaion_opt = $this->user_alert_model->check_option_user_id($json_data['tweet_owner_id']);	
		$notification_arr = array();
		
		## insert noifications ####
			if($notificaion_opt['e_retweet'] == 'Y' ){
				
				$notification_arr['i_requester_id'] = $user_id;
				$notification_arr['i_accepter_id'] = $json_data['tweet_owner_id'];
				$notification_arr['s_type'] = 'retweet';
				$notification_arr['dt_created_on'] = get_db_datetime();
				
				
				$ret = $this->user_notifications_model->insert($notification_arr);	
				$message_id = parent::social_notifications_message($notification_arr['i_requester_id'], $notification_arr['i_accepter_id'], 'retweet', $tweet_id);
			}
						
						
		 ### end  ###
		
		
		$total_my_tweets = $this->my_tweet_model->get_total_my_tweets($user_id);
		
		echo json_encode( array('success'=>'true', 'msg'=>"Tweet posted successfully.",'html'=>$_html,'view_more'=>$view_more,'cur_page'=>$cur_page, 'right_tweet_text'=>$right_tweet_text ,'total_my_tweets'=>$total_my_tweets, 'right_tweet_date'=>$right_tweet_date) );
		exit;
		
		
	}
	
	
	
	public function my_fav_tweets() 
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
										//'js/jquery-sew-master/jquery.caretposition.js',
										//'js/jquery-sew-master/jquery.sew.js',
										'js/jquery-ui.triggeredAutocomplete.js',
										'js/frontend/logged/tweets/tweets.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.13.custom.css',
                                          'css/dd.css') );
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			$this->session->set_userdata('search_condition','');
			$data['pagination_per_page'] = $this->pagination_per_page;
			$data['profile_id'] = $i_user_id;
			
			$data['total_my_tweets'] = $this->my_tweet_model->get_total_my_tweets($i_user_id);
			$data['total_followers'] = $this->my_tweet_model->get_followers_list_count($i_user_id);
			$data['total_following'] = $this->my_tweet_model->get_following_list_count($i_user_id);
			
           	ob_start();
			$this->my_fav_tweets_ajax_pagination($i_user_id);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['tweets_ajax_content'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
		   
		   	
            # view file...
            $VIEW = "logged/my_tweet/my_fav_tweets.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }   

	
	
	
	public function my_fav_tweets_ajax_pagination($i_user_id, $page=0)
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
		
        # echo $page ."--". $this->pagination_per_page ."<br />";
		
		$result = $this->my_tweet_model->get_fav_tweets_by_user_id($i_user_id,$s_where, intval($page), $this->pagination_per_page);
		//echo $this->db->last_query();
		$total_rows = $this->my_tweet_model->get_total_fav_tweets_by_user_id($i_user_id,$s_where);
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
		
		
		$VIEW_FILE = "logged/my_tweet/my_fav_tweets_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$content, 'current_page'=>$cur_page, 'no_of_result'=>$data['no_of_result'],'total'=>$total_rows,'view_more'=>$view_more  ,'cur_page'=>$data['current_page_1']) );
			
	}
	
	
	public function trends() 
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
										//'js/jquery-sew-master/jquery.caretposition.js',
										//'js/jquery-sew-master/jquery.sew.js',
										'js/jquery-ui.triggeredAutocomplete.js',
										'js/frontend/logged/tweets/tweets.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.13.custom.css',
                                          'css/dd.css') );
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			$this->session->set_userdata('search_condition','');
			$data['pagination_per_page'] = $this->trends_pagination_per_page;
			$data['profile_id'] = $i_user_id;
			
			$data['total_my_tweets'] = $this->my_tweet_model->get_total_my_tweets($i_user_id);
			$data['total_followers'] = $this->my_tweet_model->get_followers_list_count($i_user_id);
			$data['total_following'] = $this->my_tweet_model->get_following_list_count($i_user_id);
			 
           	ob_start();
			$this->trendings_ajax_pagination($page);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['trends_content'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
		   
		   	
            # view file...
            $VIEW = "logged/my_tweet/my_trends.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }   

	
	
	
	public function trendings_ajax_pagination($page=0)
    {
		
		//echo $page;
		$s_where = $this->session->userdata('search_condition');
		
		$cur_page = $page + $this->trends_pagination_per_page;
		
		$data = $this->data;
		
		$result = $this->my_tweet_model->getTrendList($s_where, intval($page), $this->trends_pagination_per_page);
		//echo $this->db->last_query();
		$total_rows = $this->my_tweet_model->getTrendListCount($s_where);
		//pr($result,1);
		$data['arr_trends'] = $result;
		$data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
		$data['profile_id'] = $i_user_id;
		
		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->trends_pagination_per_page)
			  $view_more = false;
         //--------- end check
		
		
		$VIEW_FILE = "logged/my_tweet/trends_list_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
        echo json_encode( array('html'=>$content, 'current_page'=>$cur_page, 'no_of_result'=>$data['no_of_result'],'total'=>$total_rows,'view_more'=>$view_more  ,'cur_page'=>$data['current_page_1']) );
			
	}
	
	
	public function my_followings() 
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
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.13.custom.css',
                                          'css/dd.css') );
										  
										  
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			
			$data['pagination_per_page'] = $this->pagination_per_page;
			$data['profile_id'] = $i_user_id;
			
			$data['total_my_tweets'] = $this->my_tweet_model->get_total_my_tweets($i_user_id);
			$data['total_followers'] = $this->my_tweet_model->get_followers_list_count($i_user_id);
			$data['total_following'] = $this->my_tweet_model->get_following_list_count($i_user_id);
			 
           	ob_start();
			$this->my_followings_ajax_pagination();
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['tweets_ajax_content'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
		   
		   	
            # view file...
            $VIEW = "logged/my_tweet/my_followings.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }   

	
	
	
	public function my_followings_ajax_pagination($page=0)
    {
		
		//echo $page;
		$i_user_id = intval(decrypt($this->session->userdata('user_id')));
		
		$s_where = $this->session->userdata('search_condition');
		$cur_page = $page + $this->pagination_per_page;
		
		$data = $this->data;
		
		$result = $this->my_tweet_model->get_following_list($i_user_id,$s_where, intval($page), $this->pagination_per_page);
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
		
		
		$VIEW_FILE = "logged/my_tweet/my_followings_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$content, 'current_page'=>$cur_page, 'no_of_result'=>$data['no_of_result'],'total'=>$total_rows,'view_more'=>$view_more  ,'cur_page'=>$data['current_page_1']) );
			
	}
	
	
	
	public function my_followers() 
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
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.13.custom.css',
                                          'css/dd.css') );
										  
										  
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			
			$data['pagination_per_page'] = $this->pagination_per_page;
			$data['profile_id'] = $i_user_id;
			
			$data['total_my_tweets'] = $this->my_tweet_model->get_total_my_tweets($i_user_id);
			$data['total_followers'] = $this->my_tweet_model->get_followers_list_count($i_user_id);
			$data['total_following'] = $this->my_tweet_model->get_following_list_count($i_user_id);
			 
           	ob_start();
			$this->my_followers_ajax_pagination();
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['tweets_ajax_content'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
		   
		   	
            # view file...
            $VIEW = "logged/my_tweet/my_followers.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }   

	
	
	
	public function my_followers_ajax_pagination($page=0)
    {
		
		//echo $page;
		$i_user_id = intval(decrypt($this->session->userdata('user_id')));
		
		$s_where = $this->session->userdata('search_condition');
		$cur_page = $page + $this->pagination_per_page;
		
		$data = $this->data;
		
		$result = $this->my_tweet_model->get_followers_list($i_user_id,$s_where, intval($page), $this->pagination_per_page);
		//echo $this->db->last_query();
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
		
		
		$VIEW_FILE = "logged/my_tweet/my_followers_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$content, 'current_page'=>$cur_page, 'no_of_result'=>$data['no_of_result'],'total'=>$total_rows,'view_more'=>$view_more  ,'cur_page'=>$data['current_page_1']) );
			
	}
	
	
	
	public function search_trends($trend_id) 
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
										//'js/jquery-sew-master/jquery.caretposition.js',
										//'js/jquery-sew-master/jquery.sew.js',
										'js/jquery-ui.triggeredAutocomplete.js',
										'js/frontend/logged/tweets/tweets.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.13.custom.css',
                                          'css/dd.css') );
          
		  
		    $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			
			$data['pagination_per_page'] = $this->trends_search_pagination_per_page;
			$data['profile_id'] = $i_user_id;
			
			$data['total_my_tweets'] = $this->my_tweet_model->get_total_my_tweets($i_user_id);
			$data['total_followers'] = $this->my_tweet_model->get_followers_list_count($i_user_id);
			$data['total_following'] = $this->my_tweet_model->get_following_list_count($i_user_id);
			
			$data['menu_name'] = 'search_trend';
			
			$s_where = '';
			$this->session->set_userdata('trend_search_condition','');

			
			
			### get trend name
			 $trend_arr = $this->my_tweet_model->get_trend_name_by_id($trend_id);
			 //pr($trend_arr);
			#trending
			
			$data['s_tags']  = $trend_arr['s_tags'];
			$s_where = " AND t.data  LIKE '%{$trend_arr['s_tags']}%' ";
			$this->session->set_userdata('trend_search_condition',$s_where);
			 
           	ob_start();
			$this->search_trendings_ajax_pagination($page);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['trends_content'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
		   
		   	
            # view file...
            $VIEW = "logged/my_tweet/trend_search_list.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }   

	
	
	
	public function search_trendings_ajax_pagination($page=0)
    {
		
		//echo $page;
		$i_user_id = intval(decrypt($this->session->userdata('user_id')));
		$s_where = $this->session->userdata('trend_search_condition');
		
		$cur_page = $page + $this->trends_search_pagination_per_page;
		
		$data = $this->data;
		
		
		$result = $this->my_tweet_model->search_tweets_by_trend($i_user_id, $s_where, intval($page), $this->trends_search_pagination_per_page);
		
		//echo $this->db->last_query();
		 $total_rows = $this->my_tweet_model->get_total_search_tweets_by_trend($i_user_id , $s_where);
		//pr($result,1);
		$data['arr_trends'] = $result;
		$data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
		$data['profile_id'] = $i_user_id;
		
		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->trends_search_pagination_per_page)
			  $view_more = false;
         //--------- end check
		
		
		 $VIEW_FILE = "logged/my_tweet/trend_search_list_ajax.phtml"; 
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
        echo json_encode( array('html'=>$content, 'current_page'=>$cur_page, 'no_of_result'=>$data['no_of_result'],'total'=>$total_rows,'view_more'=>$view_more  ,'cur_page'=>$data['current_page_1']) );
			
	}
		
	 public function right_tweets_ajax_pagination($i_user_id, $page=0)
    {
		
		//echo $page;
		$s_where = '';
		$data = $this->data;
       
		$result = $this->my_tweet_model->get_all_tweets_by_user_id($i_user_id,$s_where, 0,3);
		$total_rows = $this->my_tweet_model->get_total_all_tweets_by_user_id($i_user_id,$s_where);
		#pr($result,1);
		$data['right_bar']['tweet_arr'] = $result;
		$data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
		$data['profile_id'] = $i_user_id;
		
		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=3)
			  $view_more = false;
         //--------- end check
		
		
		$VIEW_FILE = "logged/my_tweet/right_all_tweets_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$content, 'no_of_result'=>$data['no_of_result'],'view_more'=>$view_more, 'cur_page'=>$data['current_page_1']) );
			
	}
	
	
	### new method to tweet prayer request
	
	public function post_prayer_req_tweets($posted_from ,$page)
	{
		$user_id = intval(decrypt($this->session->userdata('user_id')));
		$user_details = $this->users_model->fetch_this($user_id);

		 #$message = nl2br( htmlspecialchars(trim($this->input->post('message')), ENT_QUOTES, 'utf-8') ); 
		 $message = nl2br( htmlspecialchars(($this->input->post('message')), ENT_QUOTES, 'utf-8') );
		//echo ' =-=--- '.html_entity_decode(htmlspecialchars_decode($message),ENT_QUOTES,'utf-8');
		//exit;
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
				
				$tweet_id = $this->my_tweet_model->insert($arr);
				
				
				$tweet_trends = get_twitter_tags($message); 
				
				## INSERTING TRENDS 
				if(count($tweet_trends)){
					foreach($tweet_trends as $tags){
						
						## check if already exists in db 
						$isExists = $this->my_tweet_model->ifTagExists($tags);
						
						if($isExists ==  0){
							$trend = array();
							$trend['s_tags'] = $tags;
						  	$trend['dt_created_on'] = get_db_datetime();
						  	$i_trend_id = $this->my_tweet_model->insert_tags($trend);
						}
						else{
							$i_score = 1+$this->my_tweet_model->getScore($tags);
							$this->my_tweet_model->update_tags_score($i_score , $tags);
						}
					}
				}
				
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
				else if($posted_from == 'my_tweets' ){
				  ob_start();
				  $this->my_tweets_ajax_pagination($user_id,$page);
				  $content = ob_get_contents();
				  $content_obj = json_decode($content);
				  $_html = $content_obj->html; 
				  $view_more = $content_obj->view_more;
				  $cur_page = $content_obj->cur_page;
				  ob_end_clean();
				}
				else if($posted_from == 'right_bar'){
		   
				  ob_start();
				  $this->right_tweets_ajax_pagination($user_id, $page);
				  $content = ob_get_contents();
				  $content_obj = json_decode($content);
				  $_html = $content_obj->html; 
				  $view_more = $content_obj->view_more;
				  $cur_page = $content_obj->cur_page;
				  ob_end_clean();
				}
				$right_tweet_text = my_substr(nl2br($json_data['s_tweet_text']),50);
				$right_tweet_date  = get_time_elapsed($arr['dt_created_on']);
				
				$tweet_owner_info = $this->my_tweet_model->get_owner_by_tweet_id($tweet_id);
				if($tweet_owner_info['i_owner_id'] != intval(decrypt($this->session->userdata('user_id')))){
					$notification_arr['i_requester_id'] = intval(decrypt($this->session->userdata('user_id')));
					$notification_arr['i_accepter_id'] = $tweet_owner_info['i_owner_id'];
					$notification_arr['s_type'] = 'tweet_comment';
					$notification_arr['dt_created_on'] = get_db_datetime();
					
					$ret = $this->user_notifications_model->insert($notification_arr);
				}
                
                $total_my_tweets = $this->my_tweet_model->get_total_my_tweets($user_id);
				
				echo json_encode( array('success'=>'true', 'msg'=>"Tweet posted successfully.",'html'=>$_html,'view_more'=>$view_more,'cur_page'=>$cur_page,'total_my_tweets'=>$total_my_tweets) );
			}
			 else
		   {
			  echo json_encode( array('success'=>'false', 'msg'=>"Please enter some text.", 'html'=>$_html) );
		   }
		
	}
	
	
}   // end of controller...

