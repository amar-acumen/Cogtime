<?php
/*********
* Author: 
* Purpose:
*  Controller For "advertisements"
* 
* @package 
* @subpackage 
* 
* @link InfController.php 
* @link Base_Controller.php
* @link model/admin_groups_model.php
* @link views/##
*/

class Members_post extends Admin_base_Controller
{
    private $pagination_per_page=5;

    

    // constructor definition...
    function __construct()
    {
        try
        {
            parent::__construct();
             parent::_check_admin_login();            
            # configuring paths...
            
            # loading reqired model & helpers...
            // $this->load->helper('###');
           $this->load->model("users_model"); 
           $this->load->model("education_model");
		    $this->load->model("data_newsfeed_model");
			$this->load->model("ring_post_model");
			$this->load->model("my_blog_post_model");
                        $this->load->model("prayer_wall_model");
                        
           $this->load->helper('common_option_helper.php');
		   $this->load->model('my_tweet_model');
            $this->load->library('embed_video');
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    // "index" function definition...
    public function index($user_id,$type,$page_no=0) 
    {
       // echo "in members.php : page_no = ".$page_no;
        try
        {
            # adjusting header & footer sections [Start]...
            $data = $this->data;
            parent::_set_title("::: COGTIME Xtian network :::");
            parent::_set_meta_desc("::: COGTIME Xtian network :::");
            parent::_set_meta_keywords("::: COGTIME Xtian network :::");
            parent::_add_js_arr( array( 'js/lightbox.js',
                                        'js/ModalDialog.js',
                                         'js/jquery.dd.js',
					'js/frontend/logged/my_audio/my_audio.js','js/frontend/logged/my_audio/audio_helper.js','js/jwplayer/jwplayer.js'					// ,'js/backend/members/delete_user.js'
                                         ) );
                                        
            parent::_add_css_arr( array() );
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 2;
            $data['submenu'] = 1;
         
            
            // fetching data
            //$WHERE_COND = " WHERE 1 AND i_isdeleted=1";
            //$this->session->set_userdata('search_condition',$WHERE_COND);
            $page=0;
            //$order_by = " `id` DESC ";
            
            
            //---------------------- for pagination back ---------------------
            if($page_no!=0)
                $page=($page_no-1)*2;
            //---------------------- end pagination back ---------------------
			if($type == '')
			{
			$type ='wall';
			}
            if($type == 'wall')
           { ob_start();
            $this->ajax_pagination($user_id,$type,$page);
            $data['result_content1'] = ob_get_contents(); #pr($data['result_content'],1);
			//$data['type']='wall';
            ob_end_clean();
            }
			else if($type == 'ring')
			{
			ob_start();
            $this->ajax_pagination($user_id,$type,$page);
            $data['result_content2'] = ob_get_contents(); #pr($data['result_content'],1);
			//$data['type']='ring';
            ob_end_clean();
			}
			else if($type == 'blog')
			{
			ob_start();
            $this->ajax_pagination($user_id,$type,$page);
            $data['result_content3'] = ob_get_contents(); #pr($data['result_content'],1);
			//$data['type']='blog';
            ob_end_clean();
			}
			else if($type == 'prayer_group')
			{
			ob_start();
            $this->ajax_pagination($user_id,$type,$page);
            $data['result_content4'] = ob_get_contents(); #pr($data['result_content'],1);
			//$data['type']='blog';
            ob_end_clean();
			}
                        else if($type == 'prayer_wall')
			{
			ob_start();
            $this->ajax_pagination($user_id,$type,$page);
            $data['result_content6'] = ob_get_contents(); #pr($data['result_content'],1);
			//$data['type']='blog';
            ob_end_clean();
			}
			else if($type == 'tweet')
			{
			ob_start();
            $this->ajax_pagination($user_id,$type,$page);
            $data['result_content5'] = ob_get_contents(); #pr($data['result_content'],1);
			//$data['type']='blog';
            ob_end_clean();
			}
                        else if($type == 'photo')
			{
			ob_start();
            $this->ajax_pagination($user_id,$type,$page);
            $data['result_content7'] = ob_get_contents(); #pr($data['result_content'],1);
			//$data['type']='blog';
            ob_end_clean();
			}
                        else if($type == 'videos')
			{
			ob_start();
            $this->ajax_pagination($user_id,$type,$page);
            $data['result_content8'] = ob_get_contents(); #pr($data['result_content'],1);
			//$data['type']='blog';
            ob_end_clean();
			}
                        else if($type == 'audio')
			{
			ob_start();
            $this->ajax_pagination($user_id,$type,$page);
            $data['result_content9'] = ob_get_contents(); #pr($data['result_content'],1);
			//$data['type']='blog';
            ob_end_clean();
			}
             #pr($data,1);
            # rendering the view file...
			$data['i_id']=$user_id;
            $VIEW_FILE = "admin/members/members_post.phtml";
            parent::_render($data, $VIEW_FILE);
        }
		
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }

   
  
     
    
      # function to load ajax-pagination [AJAX CALL]...
    public function ajax_pagination($i_user_id,$type,$page=0)
    {
	$WHERE_COND = " where 1  ";
        try
        {
         if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) :
           
            
			
                                
				if($type == 'wall')
				{
				if($this->input->post('date_to1') != ''){
					 $dt_start_date = get_db_dateformat($this->input->post('date_to1'));
					$WHERE_COND .= ($dt_start_date=='')?'':" AND (DATE(c.dt_created_on) >='".$dt_start_date."' )";
				}
				
				if($this->input->post('date_end1') != ''){
					 $dt_end_date = get_db_dateformat($this->input->post('date_end1'));
					$WHERE_COND .= ($dt_end_date=='')?'':" AND (DATE(c.dt_created_on) <='".$dt_end_date."' )";
				}
                                  if($this->input->post('title') != '' ){ 
                                   //die('dd'); 
                                     $wallpost  = trim($this->input->post('title'));
                                    //$user_id = ".'user_id'.".':'.21;
                                    $WHERE_COND .= ($wallpost=='')?'':" AND  data LIKE '%$wallpost%'";
                                }
				}
				if($type == 'ring')
				{
				if($this->input->post('date_to1') != ''){
					 $dt_start_date = get_db_dateformat($this->input->post('date_to1'));
					$WHERE_COND .= ($dt_start_date=='')?'':" AND (DATE(dt_created_on) >='".$dt_start_date."' )";
				}
				
				if($this->input->post('date_end1') != ''){
					 $dt_end_date = get_db_dateformat($this->input->post('date_end1'));
					$WHERE_COND .= ($dt_end_date=='')?'':" AND (DATE(dt_created_on) <='".$dt_end_date."' )";
				}
                                if($this->input->post('title') != '' ){ 
                                   //die('dd'); 
                                     $ringpost  = trim($this->input->post('title'));
                                    //$user_id = ".'user_id'.".':'.21;
                                    $WHERE_COND .= ($ringpost=='')?'':" AND  s_post_title LIKE '%$ringpost%'";
                                }
				}
                                if($type == 'prayer_group' )
				{
				if($this->input->post('date_to1') != ''){
					 $dt_start_date = get_db_dateformat($this->input->post('date_to1'));
					$WHERE_COND .= ($dt_start_date=='')?'':" AND (DATE(dt_created_on) >='".$dt_start_date."' )";
				}
				
				if($this->input->post('date_end1') != ''){
					 $dt_end_date = get_db_dateformat($this->input->post('date_end1'));
					$WHERE_COND .= ($dt_end_date=='')?'':" AND (DATE(dt_created_on) <='".$dt_end_date."' )";
				}
                                if($this->input->post('title') != '' ){ 
                                   //die('dd'); 
                                     $prayerpost  = trim($this->input->post('title'));
                                    //$user_id = ".'user_id'.".':'.21;
                                    $WHERE_COND .= ($prayerpost=='')?'':" AND  s_post_desc LIKE '%$prayerpost%'";
                                }
				}
				if($type == 'tweet')
				{
				if($this->input->post('date_to1') != ''){
					 $dt_start_date = get_db_dateformat($this->input->post('date_to1'));
					$WHERE_COND .= ($dt_start_date=='')?'':" AND (DATE(T.dt_created_on) >='".$dt_start_date."' )";
				}
				
				if($this->input->post('date_end1') != ''){
					 $dt_end_date = get_db_dateformat($this->input->post('date_end1'));
					$WHERE_COND .= ($dt_end_date=='')?'':" AND (DATE(T.dt_created_on) <='".$dt_end_date."' )";
				}
                                if($this->input->post('title') != '' ){ 
                                   //die('dd'); 
                                    $tweet  = trim($this->input->post('title'));
                                    //$user_id = ".'user_id'.".':'.21;
                                    $WHERE_COND .= ($tweet=='')?'':" AND  data LIKE '%$tweet%'";
                                }
				}
				if($type == 'blog')
				{
				if($this->input->post('date_to1') != ''){
					 $dt_start_date = get_db_dateformat($this->input->post('date_to1'));
					$WHERE_COND .= ($dt_start_date=='')?'':" AND (DATE(dt_created_date) >='".$dt_start_date."' )";
				}
				
				if($this->input->post('date_end1') != ''){
					 $dt_end_date = get_db_dateformat($this->input->post('date_end1'));
					$WHERE_COND .= ($dt_end_date=='')?'':" AND (DATE(dt_created_date) <='".$dt_end_date."' )";
				}
                                 if($this->input->post('title') != '' ){ 
                                   // die('dd');
                                    $s_post_title  = trim($this->input->post('title'));
                                    $WHERE_COND .= ($s_post_title=='')?'':" AND  s_post_title LIKE '%$s_post_title%'";
                                }
				}
                                   if($type == 'prayer_wall'){
				if($this->input->post('date_to1') != ''){
					 $dt_start_date = get_db_dateformat($this->input->post('date_to1'));
					 $WHERE_COND .= ($dt_start_date=='')?'':" AND (DATE(dt_start_time) >='".$dt_start_date."' )";
				}
				
				if($this->input->post('date_end1') != ''){
					 $dt_end_date = get_db_dateformat($this->input->post('date_end1'));
					$WHERE_COND .= ($dt_end_date=='')?'':" AND (DATE(dt_start_time) <='".$dt_end_date."' )";
				}
                                 if($this->input->post('title') != '' ){ 
                                   // die('dd');
                                    $s_contents  = trim($this->input->post('title'));
                                     $s_contents = htmlspecialchars($s_contents);
                                    $WHERE_COND .= ($s_contents=='')?'':" AND  s_contents LIKE '%$s_contents%'";
                                }
				}
                                
                                if($type == 'photo'){
				if($this->input->post('date_to1') != ''){
					 $dt_start_date = get_db_dateformat($this->input->post('date_to1'));
					 $WHERE_COND .= ($dt_start_date=='')?'':" AND (DATE(p.dt_created_on) >='".$dt_start_date."' )";
				}
				
				if($this->input->post('date_end1') != ''){
					 $dt_end_date = get_db_dateformat($this->input->post('date_end1'));
					$WHERE_COND .= ($dt_end_date=='')?'':" AND (DATE(p.dt_created_on) <='".$dt_end_date."' )";
				}
                                 if($this->input->post('title') != '' ){ 
                                   // die('dd');
                                    $s_title  = trim($this->input->post('title'));
                                     $s_title = htmlspecialchars($s_title);
                                    $WHERE_COND .= ($s_title=='')?'':" AND  (p.s_title LIKE '%$s_title%' OR p.s_description '%$s_title%')";
                                }
				}
                                if($type == 'videos'){
				if($this->input->post('date_to1') != ''){
					 $dt_start_date = get_db_dateformat($this->input->post('date_to1'));
					 $WHERE_COND .= ($dt_start_date=='')?'':" AND (DATE(v.dt_created_on) >='".$dt_start_date."' )";
				}
				
				if($this->input->post('date_end1') != ''){
					 $dt_end_date = get_db_dateformat($this->input->post('date_end1'));
					$WHERE_COND .= ($dt_end_date=='')?'':" AND (DATE(v.dt_created_on) <='".$dt_end_date."' )";
				}
                                 if($this->input->post('title') != '' ){ 
                                   // die('dd');
                                    $s_title  = trim($this->input->post('title'));
                                     $s_title = htmlspecialchars($s_title);
                                    $WHERE_COND .= ($s_title=='')?'':" AND  (v.s_title LIKE '%$s_title%' OR v.s_description '%$s_title%')";
                                }
				}
                                 if($type == 'audio'){
				if($this->input->post('date_to1') != ''){
					 $dt_start_date = get_db_dateformat($this->input->post('date_to1'));
					 $WHERE_COND .= ($dt_start_date=='')?'':" AND (DATE(a.dt_created_on) >='".$dt_start_date."' )";
				}
				
				if($this->input->post('date_end1') != ''){
					 $dt_end_date = get_db_dateformat($this->input->post('date_end1'));
					$WHERE_COND .= ($dt_end_date=='')?'':" AND (DATE(a.dt_created_on) <='".$dt_end_date."' )";
				}
                                 if($this->input->post('title') != '' ){ 
                                   // die('dd');
                                    $s_title  = trim($this->input->post('title'));
                                     $s_title = htmlspecialchars($s_title);
                                    $WHERE_COND .= ($s_title=='')?'':" AND  (a.s_title LIKE '%$s_title%' OR a.s_description '%$s_title%')";
                                }
				}
			
				//$this->session->set_userdata('search_condition',$WHERE_COND);
			//echo $WHERE_COND;exit;
			
           endif;  
		   	
			//$s_where = $this->session->userdata('search_condition');

			if($type == 'wall')
			{
			//$s_where ='';
			//$s_where ='where 1 ';
			//echo $WHERE_COND;
			$this->session->set_userdata('search_condition',$WHERE_COND);
			$s_where = $this->session->userdata('search_condition');
            $result = $this->data_newsfeed_model->get_owner_post_by_id($i_user_id,$s_where,$page,$this->pagination_per_page);
			
           $resultCount = count($result);
			//echo $resultCount;
            $total_rows = $this->data_newsfeed_model->get_total_owner_post_by_id($i_user_id,$s_where);
			$data['type']='wall';
            //echo $total_rows;
           if( ( !is_array($result) || !count($result) ) && $total_rows ) {
                $page = $page - $this->pagination_per_page;
                
                $result = $this->data_newsfeed_model->get_owner_post_by_id($i_user_id,$s_where,$page, $this->pagination_per_page);
            }
			}
			
			else if($type == 'ring')
			{
			
			$this->session->set_userdata('search_condition',$WHERE_COND);
			$s_where = $this->session->userdata('search_condition');
			$result = $this->ring_post_model->get_ring_post_by_user_id($i_user_id,$s_where,$page,$this->pagination_per_page);
			
           $resultCount = count($result);
			//echo $resultCount;
            $total_rows = $this->ring_post_model->get_total_ring_post_by_user_id($i_user_id,$s_where);
			$data['type']='ring';
            //echo $total_rows;
           if( ( !is_array($result) || !count($result) ) && $total_rows ) {
                $page = $page - $this->pagination_per_page;
                
                $result = $this->ring_post_model->get_ring_post_by_user_id($i_user_id,$s_where,$page, $this->pagination_per_page);
            }
			}
            
			else if($type == 'blog')
			{
			
			$this->session->set_userdata('search_condition',$WHERE_COND);
			$s_where = $this->session->userdata('search_condition');
			$result = $this->my_blog_post_model->get_blog_post_by_user_id($i_user_id,$s_where,$page,$this->pagination_per_page);
			
           $resultCount = count($result);
			//echo $resultCount;
            $total_rows = $this->my_blog_post_model->get_total_blog_post_by_user_id($i_user_id,$s_where);
			$data['type']='blog';
            //echo $total_rows;
           if( ( !is_array($result) || !count($result) ) && $total_rows ) {
                $page = $page - $this->pagination_per_page;
                
                $result = $this->my_blog_post_model->get_blog_post_by_user_id($i_user_id,$s_where,$page, $this->pagination_per_page);
            }
			}
			
			else if($type == 'tweet')
			{
			$WHERE_COND.=" AND i_owner_id=".$i_user_id;
			$this->session->set_userdata('search_condition',$WHERE_COND);
			$s_where = $this->session->userdata('search_condition');
			
			//echo $s_where;
			$result = $this->my_tweet_model->get_list($s_where,$page,$this->pagination_per_page,$order_by);
			//pr($result);
           $resultCount = count($result);
			//echo $resultCount;
            $total_rows = $this->my_tweet_model->get_list_count($s_where);
			$data['type']='tweet';
            //echo $total_rows;
           if( ( !is_array($result) || !count($result) ) && $total_rows ) {
                $page = $page - $this->pagination_per_page;
                
                $result = $this->my_tweet_model->get_list($s_where,$page,$this->pagination_per_page,$order_by);
            }
			}
			
			else if($type == 'prayer_group')
			{
			$order_by= ' id desc';
			$WHERE_COND.=" AND i_user_id=".$i_user_id;
			$this->session->set_userdata('search_condition',$WHERE_COND);
			$s_where = $this->session->userdata('search_condition');
			$result = $this->prayer_group_model->get_list_by_user($s_where,$page,$this->pagination_per_page,$order_by);
			//pr($result);
           $resultCount = count($result);
			//echo $resultCount;
            $total_rows = $this->prayer_group_model->get_list_count($s_where);
			$data['type']='prayer_group';
            //echo $total_rows;
           if( ( !is_array($result) || !count($result) ) && $total_rows ) {
                $page = $page - $this->pagination_per_page;
                
                $result = $this->prayer_group_model->get_list_by_user($s_where,$page,$this->pagination_per_page,$order_by);
            }
			}
                        if($type == 'prayer_wall')
			{
                           //echo $i_user_id;
                            //die('d');
			//$s_where ='';
			//$s_where ='where 1 ';
			//echo $WHERE_COND;
			$this->session->set_userdata('search_condition',$WHERE_COND);
			$s_where = $this->session->userdata('search_condition');
            $result = $this->prayer_wall_model->get_owner_prayer_wall_by_id($i_user_id,$s_where,$page,$this->pagination_per_page);
			
           $resultCount = count($result);
			//echo $resultCount;
            $total_rows = $this->prayer_wall_model->get_total_owner_prayer_wall_by_id($i_user_id,$s_where);
			$data['type']='prayer_wall';
            //echo $total_rows;
           if( ( !is_array($result) || !count($result) ) && $total_rows ) {
                $page = $page - $this->pagination_per_page;
                
                $result = $this->prayer_wall_model->get_owner_prayer_wall_by_id($i_user_id,$s_where,$page,$this->pagination_per_page);
            }
			}
                        
                        if($type == 'photo')
			{
			
			$this->session->set_userdata('search_condition',$WHERE_COND);
			$s_where = $this->session->userdata('search_condition');
            $result = $this->data_newsfeed_model->get_photo_post_by_id($i_user_id,$s_where,$page,$this->pagination_per_page);
			
           $resultCount = count($result);
			//echo $resultCount;
            $total_rows = $this->data_newsfeed_model->get_total_photo_post_by_id($i_user_id,$s_where);
			$data['type']='photo';
            //echo $total_rows;
//           if( ( !is_array($result) || !count($result) ) && $total_rows ) {
//                $page = ($page - $this->pagination_per_page);
//                
//                $result = $this->data_newsfeed_model->get_photo_post_by_id($i_user_id,$s_where,$page, $this->pagination_per_page);
//            }
			}
                        if($type == 'videos')
			{
			
			$this->session->set_userdata('search_condition',$WHERE_COND);
			$s_where = $this->session->userdata('search_condition');
            $result = $this->data_newsfeed_model->get_video_post_by_id($i_user_id,$s_where,$page,$this->pagination_per_page);
			
           $resultCount = count($result);
			//echo $resultCount;
            $total_rows = $this->data_newsfeed_model->get_total_video_post_by_id($i_user_id,$s_where);
			$data['type']='videos';
            //echo $total_rows;
//           if( ( !is_array($result) || !count($result) ) && $total_rows ) {
//                $page = ($page - $this->pagination_per_page);
//                
//                $result = $this->data_newsfeed_model->get_photo_post_by_id($i_user_id,$s_where,$page, $this->pagination_per_page);
//            }
			}
                         if($type == 'audio')
			{
			
			$this->session->set_userdata('search_condition',$WHERE_COND);
			$s_where = $this->session->userdata('search_condition');
            $result = $this->data_newsfeed_model->get_audio_post_by_id($i_user_id,$s_where,$page,$this->pagination_per_page);
			
           $resultCount = count($result);
			//echo $resultCount;
            $total_rows = $this->data_newsfeed_model->get_total_audio_post_by_id($i_user_id,$s_where);
			$data['type']='audio';
            //echo $total_rows;
//           if( ( !is_array($result) || !count($result) ) && $total_rows ) {
//                $page = ($page - $this->pagination_per_page);
//                
//                $result = $this->data_newsfeed_model->get_photo_post_by_id($i_user_id,$s_where,$page, $this->pagination_per_page);
//            }
			}
            #Jquery Pagination Starts
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/members/members_post/ajax_pagination/".$i_user_id."/".$type;
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->pagination_per_page;
            $config['uri_segment'] = 7;
            $config['num_links'] = 9;
            $config['page_query_string'] = false;
            $config['prev_link'] = 'PREV';
            $config['next_link'] = 'NEXT';

            
            $config['cur_tag_open'] = '<li>';
            $config['cur_tag_close'] = '</li>';

            $config['next_tag_open'] = '<li>';
            $config['next_tag_close'] = '</li>';

            $config['prev_tag_open'] = '<li>';
            $config['prev_tag_close'] = '</li>';

            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';

            $config['div'] = '#'.$type.'_content'; /* Here #content is the CSS selector for target DIV */
            $config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
            $config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */

            $this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();

            // getting   listing...
            $data['info_arr'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $page;
            $data['type1']=$type;
            $data['pagination_per_page']   =    $this->pagination_per_page;
          
            # loading the view-part...
            echo $this->load->view('admin/members/members_post_ajax.phtml', $data,TRUE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
        
    }



// ================================== delete member ==============================================
    public function delete_post($id,$uid,$type)
    {
        //$info['i_isdeleted']=2;
		if($type == 'wall')
		{
        $_ret = $this->data_newsfeed_model->delete_by_id($id);
        $re_page = admin_base_url()."members/members_post/index/".$uid."/".$type."/0";
		}
		
		if($type == 'ring')
		{
		 $_ret = $this->ring_post_model->delete_by_id($id);
        $re_page = admin_base_url()."members/members_post/index/".$uid."/".$type."/0";
		}
		if($type == 'blog')
		{
		 $_ret = $this->my_blog_post_model->delete_by_id($id);
        $re_page = admin_base_url()."members/members_post/index/".$uid."/".$type."/0";
		}
		if($type == 'tweet')
		{
		 $_ret = $this->my_tweet_model->delete_by_id($id);
        $re_page = admin_base_url()."members/members_post/index/".$uid."/".$type."/0";
		}
		if($type == 'prayer_group')
		{
		 $_ret = $this->prayer_group_model->delete_post_by_id($id);
        $re_page = admin_base_url()."members/members_post/index/".$uid."/".$type."/0";
		}
        header("location:".$re_page); 
    }
     function all_comments($page=0){
       
          try
         {
             $i_newsfeed_id= $this->input->post('id');
             $where = "WHERE i_newsfeed_id=".$i_newsfeed_id;
             //$result = $this->my_blog_post_model->show_all_comments($where,$page,$this->comments_pagination_per_page);
             $result = $this->data_newsfeed_model->get_by_comment_id($i_newsfeed_id);
             //pr($res);
        //$total_rows=$this->data_newsfeed_model->get_by_comment_id_count($where);
       // echo $total_rows[0]['num']; 
            #Jquery Pagination Starts
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/members/members_post/all_comments";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->pagination_per_page;
            $config['uri_segment'] = 5;
            $config['num_links'] = 9;
            $config['page_query_string'] = false;
            $config['prev_link'] = 'PREV';
            $config['next_link'] = 'NEXT';

            $config['cur_tag_open'] = '<li> <span><a href="javascript:void(0)" class="select">';
            $config['cur_tag_close'] = '</a></span></li>';

            $config['next_tag_open'] = '<li><a href="javascript:void(0)">';
            $config['next_tag_close'] = '</a></li>';

            $config['prev_tag_open'] = '<li><a href="javascript:void(0)">';
            $config['prev_tag_close'] = '</a></li>';

            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';

            $config['div'] = '#comment_div'; /* Here #content is the CSS selector for target DIV */
            $config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
            $config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */

            $this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();
            $this->jquery_pagination->create_links();

            // getting   listing...
            $data['info_arr'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $page;
          
            # loading the view-part...
            $content = $this->load->view('admin/members/post_comments_ajax.phtml', $data,TRUE);
            
            echo json_encode(array('html'=>$content));
         }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
    }
     function all_comments_ring($page=0){
       
          try
         {
             $i_ring_id= $this->input->post('id');
             $where = "WHERE i_newsfeed_id=".$i_ring_id;
             //$result = $this->my_blog_post_model->show_all_comments($where,$page,$this->comments_pagination_per_page);
             $result = $this->ring_post_model->get_all_ring_comment_ringid($i_ring_id);
             //pr($res);
        //$total_rows=$this->data_newsfeed_model->get_by_comment_id_count($where);
       // echo $total_rows[0]['num']; 
            #Jquery Pagination Starts
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/members/members_post/all_comments_ring";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->pagination_per_page;
            $config['uri_segment'] = 5;
            $config['num_links'] = 9;
            $config['page_query_string'] = false;
            $config['prev_link'] = 'PREV';
            $config['next_link'] = 'NEXT';

            $config['cur_tag_open'] = '<li> <span><a href="javascript:void(0)" class="select">';
            $config['cur_tag_close'] = '</a></span></li>';

            $config['next_tag_open'] = '<li><a href="javascript:void(0)">';
            $config['next_tag_close'] = '</a></li>';

            $config['prev_tag_open'] = '<li><a href="javascript:void(0)">';
            $config['prev_tag_close'] = '</a></li>';

            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';

            $config['div'] = '#comment_div'; /* Here #content is the CSS selector for target DIV */
            $config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
            $config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */

            $this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();
            $this->jquery_pagination->create_links();

            // getting   listing...
            $data['info_arr'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $page;
          
            # loading the view-part...
            $content = $this->load->view('admin/members/ring_comments_ajax.phtml', $data,TRUE);
            
            echo json_encode(array('html'=>$content));
         }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
    }
    
     function show_all_commitment($page=0){
       
          try
         {
             $i_prayer_req_id= $this->input->post('id');
             $where = "WHERE i_newsfeed_id=".$i_prayer_req_id;
             //$result = $this->my_blog_post_model->show_all_comments($where,$page,$this->comments_pagination_per_page);
             $result = $this->prayer_wall_model->get_all_commitmen_by_id($i_prayer_req_id);
             //pr($res);
        //$total_rows=$this->data_newsfeed_model->get_by_comment_id_count($where);
       // echo $total_rows[0]['num']; 
            #Jquery Pagination Starts
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/members/members_post/show_all_commitment";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->pagination_per_page;
            $config['uri_segment'] = 5;
            $config['num_links'] = 9;
            $config['page_query_string'] = false;
            $config['prev_link'] = 'PREV';
            $config['next_link'] = 'NEXT';

            $config['cur_tag_open'] = '<li> <span><a href="javascript:void(0)" class="select">';
            $config['cur_tag_close'] = '</a></span></li>';

            $config['next_tag_open'] = '<li><a href="javascript:void(0)">';
            $config['next_tag_close'] = '</a></li>';

            $config['prev_tag_open'] = '<li><a href="javascript:void(0)">';
            $config['prev_tag_close'] = '</a></li>';

            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';

            $config['div'] = '#comment_div'; /* Here #content is the CSS selector for target DIV */
            $config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
            $config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */

            $this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();
            $this->jquery_pagination->create_links();

            // getting   listing...
            $data['info_arr'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $page;
          
            # loading the view-part...
            $content = $this->load->view('admin/members/praywall_commitment_ajax.phtml', $data,TRUE);
            
            echo json_encode(array('html'=>$content));
         }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
    }
    function all_comments_blog($page=0){
       
          try
         {
             $i_blog_id= $this->input->post('id');
             $where = "WHERE i_newsfeed_id=".$i_blog_id;
             //$result = $this->my_blog_post_model->show_all_comments($where,$page,$this->comments_pagination_per_page);
             $result = $this->my_blog_post_model->get_all_blog_comment_blogid($i_blog_id);
             //pr($res);
        //$total_rows=$this->data_newsfeed_model->get_by_comment_id_count($where);
       // echo $total_rows[0]['num']; 
            #Jquery Pagination Starts
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/members/members_post/all_comments_blog";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->pagination_per_page;
            $config['uri_segment'] = 5;
            $config['num_links'] = 9;
            $config['page_query_string'] = false;
            $config['prev_link'] = 'PREV';
            $config['next_link'] = 'NEXT';

            $config['cur_tag_open'] = '<li> <span><a href="javascript:void(0)" class="select">';
            $config['cur_tag_close'] = '</a></span></li>';

            $config['next_tag_open'] = '<li><a href="javascript:void(0)">';
            $config['next_tag_close'] = '</a></li>';

            $config['prev_tag_open'] = '<li><a href="javascript:void(0)">';
            $config['prev_tag_close'] = '</a></li>';

            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';

            $config['div'] = '#comment_div'; /* Here #content is the CSS selector for target DIV */
            $config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
            $config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */

            $this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();
            $this->jquery_pagination->create_links();

            // getting   listing...
            $data['info_arr'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $page;
          
            # loading the view-part...
            $content = $this->load->view('admin/members/blog_comments_ajax.phtml', $data,TRUE);
            
            echo json_encode(array('html'=>$content));
         }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
    }
    function del_blog(){
        $id = $this->input->post('id');
       $query = $this->db->delete('cg_user_blog_post_comments', array('id' => $id));
       //$result_arr=$query->result_array();
//echo $sql;exit;
//echo $i_start_limit;
//pr($result_arr,1);
//return $result_arr;
    }
    function del_ring_comment(){
         $id = $this->input->post('id');
       $query = $this->db->delete('cg_user_ring_post_comments', array('id' => $id));
    }
    function del_post_com(){
          $id = $this->input->post('id');
       $query = $this->db->delete('cg_user_newsfeed_comments', array('id' => $id));
    }
     function del_commitment(){
         $id = $this->input->post('id');
       $query = $this->db->delete('cg_bible_prayer_commitments', array('id' => $id));
    }
    function del_photo(){
        $id = $this->input->post('id');
        $this->db->delete('cg_user_photos', array('id' => $id)); 
          echo json_encode(array('res'=>'ok'));
    }
    function del_vid(){
        $id = $this->input->post('id');
        $this->db->delete('cg_user_videos', array('id' => $id)); 
          echo json_encode(array('res'=>'ok'));
    }
    function del_audio(){
        $id = $this->input->post('id');
        $this->db->delete('cg_user_audio', array('id' => $id)); 
          echo json_encode(array('res'=>'ok'));
    }

}// end of controller