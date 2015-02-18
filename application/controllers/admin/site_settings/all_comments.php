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

class All_comments extends Admin_base_Controller
{
    private $pagination_per_page=10;

    

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
           //$this->load->model("users_model"); 
          // $this->load->model("education_model");
            $this->load->model("users_model"); 
           $this->load->model("education_model");
		    $this->load->model("data_newsfeed_model");
			$this->load->model("ring_post_model");
			$this->load->model("my_blog_post_model");
                        $this->load->model("media_comments_model");
                       // prayer_group_model
                         $this->load->model("prayer_group_model");
                        $this->load->model("media_comments_model");
                        $this->load->model("events_comments_model");
                        
           $this->load->helper('common_option_helper.php');
		   $this->load->model('my_tweet_model');
            
           //data_messages_model
          // $this->load->helper('common_option_helper.php');
           $this->load->helper('text');
           
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    // "index" function definition...
    public function index($type,$page_no=0) 
    {
        //echo "in members.php : page_no = ".$page_no;
        try
        {
            # adjusting header & footer sections [Start]...
            $data = $this->data;
            parent::_set_title("::: COGTIME Xtian network :::");
            parent::_set_meta_desc("::: COGTIME Xtian network :::");
            parent::_set_meta_keywords("::: COGTIME Xtian network :::");
            parent::_add_js_arr( array( 'js/lightbox.js',
                                        'js/ModalDialog.js',
                                         'js/jquery.dd.js'
										// ,'js/backend/members/delete_user.js'
                                         ) );
                                        
            parent::_add_css_arr( array() );
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 1;
            $data['submenu'] = 16;
         
            
            // fetching data
            //$WHERE_COND = " WHERE 1 AND i_isdeleted=1";
            //$this->session->set_userdata('search_condition',$WHERE_COND);
            $page=0;
            //$order_by = " `id` DESC ";
            
            
            //---------------------- for pagination back ---------------------
            if($page_no!=0)
                $page=($page_no-1)*2;
            //---------------------- end pagination back ---------------------
		//echo $type;die('k');
            if($type == ''){ $type = 'wall';}
            if($type == 'wall')
           { ob_start();
           
            $this->ajax_pagination($type,$page);
            $data['result_content1'] = ob_get_contents(); //pr($data['result_content'],1);
			//$data['type']='wall';
            ob_end_clean();
            }
			else if($type == 'ring')
			{
			ob_start();
            $this->ajax_pagination($type,$page);
            $data['result_content2'] = ob_get_contents(); #pr($data['result_content'],1);
			//$data['type']='ring';
            ob_end_clean();
			}
			else if($type == 'blog')
			{
			ob_start();
            $this->ajax_pagination($type,$page);
            $data['result_content3'] = ob_get_contents(); #pr($data['result_content'],1);
			//$data['type']='blog';
            ob_end_clean();
			}
			else if($type == 'event')
			{
			ob_start();
            $this->ajax_pagination($type,$page);
            $data['result_content4'] = ob_get_contents(); #pr($data['result_content'],1);
			//$data['type']='blog';
            ob_end_clean();
			}
                        else if($type == 'prayer_wall')
			{
			ob_start();
            $this->ajax_pagination($type,$page);
            $data['result_content6'] = ob_get_contents(); #pr($data['result_content'],1);
			//$data['type']='blog';
            ob_end_clean();
			}
			else if($type == 'media')
			{
			ob_start();
            $this->ajax_pagination($type,$page);
            $data['result_content5'] = ob_get_contents(); #pr($data['result_content'],1);
			//$data['type']='blog';
            ob_end_clean();
			}
             #pr($data,1);
            # rendering the view file...
			$data['i_id']=$user_id;
            $VIEW_FILE = "admin/site_settings/all_comments/comments.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }

   
   
    
      # function to load ajax-pagination [AJAX CALL]...
    public function ajax_pagination($type,$page=0)
    {
        
	$WHERE_COND = " where 1  ";
       
        try
        {
         if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y') :
           
        
				
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
                                    $WHERE_COND .= ($wallpost=='')?'':" AND  c.s_contents LIKE '%$wallpost%'";
                                }
                                if($this->input->post('cmntby') != '' ){ 
                                   //die('dd'); 
                                     $wallpostcmt  = get_formatted_string(trim($this->input->post('cmntby')));
                                    //$wallpostcmt= preg_replace('/\s+/', '', $wallpostcmt);
                                    //$user_id = ".'user_id'.".':'.21;
                                    $WHERE_COND .= ($wallpostcmt=='')?'':" AND (  CONCAT(u.s_first_name,' ',u.s_last_name) LIKE  '%".$wallpostcmt."%')";
                                }
                                
				}
				if($type == 'ring')
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
                                     $ringcomnt  = trim($this->input->post('title'));
                                    //$user_id = ".'user_id'.".':'.21;
                                    $WHERE_COND .= ($ringcomnt=='')?'':" AND  c.s_contents LIKE '%$ringcomnt%'";
                                }
                                if($this->input->post('cmntby') != '' ){ 
                                   //die('dd'); 
                                     $ringposter  = get_formatted_string(trim($this->input->post('cmntby')));
                                    //$wallpostcmt= preg_replace('/\s+/', '', $wallpostcmt);
                                    //$user_id = ".'user_id'.".':'.21;
                                    $WHERE_COND .= ($ringposter=='')?'':" AND (  CONCAT(u.s_first_name,' ',u.s_last_name) LIKE  '%".$ringposter."%')";
                                }
                                
				}
				if($type == 'media')
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
                                    $media_comment  = trim($this->input->post('title'));
                                    //$user_id = ".'user_id'.".':'.21;
                                    $WHERE_COND .= ($media_comment=='')?'':" AND  c.s_contents LIKE '%$media_comment%'";
                                }
                                if($this->input->post('cmntby') != '' ){ 
                                   //die('dd'); 
                                     $mediapostcmnt  = get_formatted_string(trim($this->input->post('cmntby')));
                                    //$wallpostcmt= preg_replace('/\s+/', '', $wallpostcmt);
                                    //$user_id = ".'user_id'.".':'.21;
                                    $WHERE_COND .= ($mediapostcmnt=='')?'':" AND (  CONCAT(u.s_first_name,' ',u.s_last_name) LIKE  '%".$mediapostcmnt."%')";
                                }
                                
				}
				if($type == 'blog')
				{
				if($this->input->post('date_to1') != ''){
					 $dt_start_date = get_db_dateformat($this->input->post('date_to1'));
					$WHERE_COND .= ($dt_start_date=='')?'':" AND (DATE(c.dt_posted_date) >='".$dt_start_date."' )";
				}
				
				if($this->input->post('date_end1') != ''){
					 $dt_end_date = get_db_dateformat($this->input->post('date_end1'));
					$WHERE_COND .= ($dt_end_date=='')?'':" AND (DATE(c.dt_posted_date) <='".$dt_end_date."' )";
				}
                                if($this->input->post('title') != '' ){ 
                                   // die('dd');
                                    $s_post_title  = trim($this->input->post('title'));
                                    $WHERE_COND .= ($s_post_title=='')?'':" AND  c.s_comments LIKE '%$s_post_title%'";
                                }
                                if($this->input->post('cmntby') != '' ){ 
                                   //die('dd'); 
                                     $blogpostcmnt  = get_formatted_string(trim($this->input->post('cmntby')));
                                    //$wallpostcmt= preg_replace('/\s+/', '', $wallpostcmt);
                                    //$user_id = ".'user_id'.".':'.21;
                                    $WHERE_COND .= ($blogpostcmnt=='')?'':" AND (  CONCAT(u.s_first_name,' ',u.s_last_name) LIKE  '%".$blogpostcmnt."%')";
                                }
                                
				}
                                if($type == 'prayer_wall'){
				if($this->input->post('date_to1') != ''){
					 $dt_start_date = get_db_dateformat($this->input->post('date_to1'));
					 $WHERE_COND .= ($dt_start_date=='')?'':" AND (DATE(c.dt_created_on) >='".$dt_start_date."' )";
				}
				
				if($this->input->post('date_end1') != ''){
					 $dt_end_date = get_db_dateformat($this->input->post('date_end1'));
					$WHERE_COND .= ($dt_end_date=='')?'':" AND (DATE(c.dt_created_on) <='".$dt_end_date."' )";
				}
                                if($this->input->post('title') != '' ){ 
                                   // die('dd');
                                    $s_contents  = trim($this->input->post('title'));
                                     $s_contents = htmlspecialchars($s_contents);
                                    $WHERE_COND .= ($s_contents=='')?'':" AND  c.s_contents LIKE '%$s_contents%'";
                                }
                                 if($this->input->post('cmntby') != '' ){ 
                                   //die('dd'); 
                                     $prayerpostcmntmnt  = get_formatted_string(trim($this->input->post('cmntby')));
                                    //$wallpostcmt= preg_replace('/\s+/', '', $wallpostcmt);
                                    //$user_id = ".'user_id'.".':'.21;
                                    $WHERE_COND .= ($prayerpostcmntmnt=='')?'':" AND (  CONCAT(u.s_first_name,' ',u.s_last_name) LIKE  '%".$prayerpostcmntmnt."%')";
                                }
				}
                                   if($type == 'event'){
				if($this->input->post('date_to1') != ''){
					 $dt_start_date = get_db_dateformat($this->input->post('date_to1'));
					 $WHERE_COND .= ($dt_start_date=='')?'':" AND (DATE(c.dt_created_on) >='".$dt_start_date."' )";
				}
				
				if($this->input->post('date_end1') != ''){
					 $dt_end_date = get_db_dateformat($this->input->post('date_end1'));
					$WHERE_COND .= ($dt_end_date=='')?'':" AND (DATE(c.dt_created_on) <='".$dt_end_date."' )";
				}
                                if($this->input->post('title') != '' ){ 
                                   // die('dd');
                                    $s_contents  = trim($this->input->post('title'));
                                     $s_contents = htmlspecialchars($s_contents);
                                    $WHERE_COND .= ($s_contents=='')?'':" AND  c.s_contents LIKE '%$s_contents%'";
                                }
                                if($this->input->post('cmntby') != '' ){ 
                                   //die('dd'); 
                                     $eventpostcmnt  = get_formatted_string(trim($this->input->post('cmntby')));
                                    //$wallpostcmt= preg_replace('/\s+/', '', $wallpostcmt);
                                    //$user_id = ".'user_id'.".':'.21;
                                    $WHERE_COND .= ($eventpostcmnt=='')?'':" AND (  CONCAT(u.s_first_name,' ',u.s_last_name) LIKE  '%".$eventpostcmnt."%')";
                                }
				}
				//$this->session->set_userdata('search_condition',$WHERE_COND);
			//echo $WHERE_COND;exit;
			
           endif;  
		   	
			//$s_where = $this->session->userdata('search_condition');

			if($type == 'wall')
			{
                           // die('kk');
			//$s_where ='';
			//$s_where ='where 1 ';
			//echo $WHERE_COND; die();
			$this->session->set_userdata('search_condition',$WHERE_COND);
			$s_where = $this->session->userdata('search_condition');
            $result = $this->data_newsfeed_model->get_all_comments_wall($s_where,$page,$this->pagination_per_page);
			
           $resultCount = count($result);
			//echo $resultCount; die();
            $total_rows = $this->data_newsfeed_model->get_all_comments_wall_total($s_where);
          // echo $total_rows = $total_rows->count;
           $total_rows=$total_rows[0]['count'];
			$data['type']='wall';
            //echo $total_rows; die();
           if( ( !is_array($result) || !count($result) ) && $total_rows ) {
                $page = $page - $this->pagination_per_page;
               //die();
                
                 $result = $this->data_newsfeed_model->get_all_comments_wall($s_where,$page,$this->pagination_per_page);
            }
			}
                        
			
			else if($type == 'ring')
			{
			//die('d');
			$this->session->set_userdata('search_condition',$WHERE_COND);
			$s_where = $this->session->userdata('search_condition');
			$result = $this->ring_post_model->get_all_ring_comments($s_where,$page,$this->pagination_per_page);
			
           $resultCount = count($result);
			//echo $resultCount;
            $total_rows = $this->ring_post_model->get_all_ring_comments_total($s_where);
             //$total_rows=$total_rows[0]['count'];
			$data['type']='ring';
            //echo $total_rows;
           if( ( !is_array($result) || !count($result) ) && $total_rows ) {
                $page = $page - $this->pagination_per_page;
                
                $result = $this->ring_post_model->get_all_ring_comments($s_where,$page,$this->pagination_per_page);
            }
			}
            
			else if($type == 'blog')
			{
			
			$this->session->set_userdata('search_condition',$WHERE_COND);
			$s_where = $this->session->userdata('search_condition');
			$result = $this->my_blog_post_model->get_blog_all_comments($s_where,$page,$this->pagination_per_page);
			
           $resultCount = count($result);
			//echo $resultCount;
            $total_rows = $this->my_blog_post_model->get_blog_all_comments_total($s_where);
			$data['type']='blog';
            //echo $total_rows;
           if( ( !is_array($result) || !count($result) ) && $total_rows ) {
                $page = $page - $this->pagination_per_page;
                
                $result = $this->my_blog_post_model->get_blog_all_comments($s_where,$page,$this->pagination_per_page);
            }
			}
                        
                        
			
			else if($type == 'media')
			{
			//$WHERE_COND.=" AND i_owner_id=".$i_user_id;
			$this->session->set_userdata('search_condition',$WHERE_COND);
			$s_where = $this->session->userdata('search_condition');
			
			//echo $s_where;
			$result = $this->media_comments_model->all_media_comments($s_where,$page,$this->pagination_per_page,$order_by);
			//pr($result);
           $resultCount = count($result);
			//echo $resultCount;
            $total_rows = $this->media_comments_model->all_media_comments_total($s_where);
			$data['type']='media';
            //echo $total_rows;
           if( ( !is_array($result) || !count($result) ) && $total_rows ) {
                $page = $page - $this->pagination_per_page;
                
                $result = $this->media_comments_model->all_media_comments($s_where,$page,$this->pagination_per_page,$order_by);
            }
			}
			
			else if($type == 'event')
			{
			//$order_by= ' id desc';
			//$WHERE_COND.=" AND i_user_id=".$i_user_id;
			$this->session->set_userdata('search_condition',$WHERE_COND);
			$s_where = $this->session->userdata('search_condition');
			$result = $this->events_comments_model->get_all_comments($s_where,$page,$this->pagination_per_page,$order_by);
			//pr($result);
           $resultCount = count($result);
			//echo $resultCount;
            $total_rows = $this->events_comments_model->get_all_comments_total($s_where);
			$data['type']='event';
            //echo $total_rows;
           if( ( !is_array($result) || !count($result) ) && $total_rows ) {
                $page = $page - $this->pagination_per_page;
                
                $result = $this->events_comments_model->get_all_comments($s_where,$page,$this->pagination_per_page,$order_by);
            }
			}
                        if($type == 'prayer_wall')
			{
			$this->session->set_userdata('search_condition',$WHERE_COND);
		 $s_where = $this->session->userdata('search_condition');
            $result = $this->prayer_group_model->prayer_wall_all_comment($s_where,$page,$this->pagination_per_page);
			
           $resultCount = count($result);
			//echo $resultCount;
            $total_rows = $this->prayer_group_model->prayer_wall_all_comment_total($s_where);
			$data['type']='prayer_wall';
            //echo $total_rows;
           if( ( !is_array($result) || !count($result) ) && $total_rows ) {
                $page = $page - $this->pagination_per_page;
                
                $result = $this->prayer_group_model->prayer_wall_all_comment($s_where,$page,$this->pagination_per_page);
            }
			}
                        
            #Jquery Pagination Starts
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/site_settings/all_comments/ajax_pagination/".$type;
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->pagination_per_page;
            $config['uri_segment'] = 6;
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
              // die();
            # loading the view-part...
            echo $this->load->view('admin/site_settings/all_comments/comments_ajax.phtml', $data,TRUE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
        
    }



// ================================== delete com ==============================================
   function del_com($id,$type){
       $type = $this->input->post('type');
        $id = $this->input->post('id');
        if($type =='wall' ){ 
             $query = $this->db->delete('cg_user_newsfeed_comments', array('id' => $id));
        }
        else if($type =='ring' ){
             $query = $this->db->delete('cg_user_ring_post_comments', array('id' => $id));
        }
        else if($type =='blog' ){
            $query = $this->db->delete('cg_user_blog_post_comments', array('id' => $id));
        }
        else if($type =='event' ){
            $query = $this->db->delete('cg_event_comments', array('id' => $id));
        }
         else if($type =='prayer_wall' ){
            $query = $this->db->delete('cg_prayer_wall_comments', array('id' => $id));
        }
         else if($type =='media' ){
            $query = $this->db->delete('cg_user_media_comments', array('id' => $id));
        }
        //echo $id;
       //echo $type;
      //die('s');
   }
// ================================= show_media======================================
   function show_media($id,$type) {
      // echo 'sssss';
      // die('d');
       $id = $this->input->post('id');
       $type = $this->input->post('type');
       
       if($type == 'photo'){
           //die('s');
         $result =  $this->media_comments_model->media_photo($id);
           $data['info_arr'] = $result;
            $content = $this->load->view('admin/site_settings/all_comments/photo.phtml', $data,TRUE);
            
            echo json_encode(array('html'=>$content));
       }
       if($type == 'audio'){
           $result=$this->media_comments_model->media_audio($id);
          // $result= $this->media_comments_model->media_vedio($id);
            $data['info_arr'] = $result;
            $content = $this->load->view('admin/site_settings/all_comments/audio.phtml', $data,TRUE);
            
            echo json_encode(array('html'=>$content));
       }
       if($type == 'video'){
          $result= $this->media_comments_model->media_vedio($id);
            $data['info_arr'] = $result;
            $content = $this->load->view('admin/site_settings/all_comments/video.phtml', $data,TRUE);
            
            echo json_encode(array('html'=>$content));
       }
       //die('');
   }
   
// ================================= end of change status ======================================     
// ================================= change is minister ======================================
   
   /*********************add ip block unblock on 18-12-2014*******************************************************/
    function ip_block($id,$ip){
       $id = $this->input->post('id');
       $ip = $this->input->post('ip');
       $data = array(
               'is_status' => 1,
               
            );

$this->db->update('cg_user_ip', $data, array('u_id' => $id,'u_ip' =>$ip));
      echo json_encode(array('status'=>true)); 
       
   }
   
   function ip_unblock($id,$ip){
       $id = $this->input->post('id');
       $ip = $this->input->post('ip');
       $data = array(
               'is_status' => 0,
               
            );

$this->db->update('cg_user_ip', $data, array('u_id' => $id,'u_ip' =>$ip));
     echo json_encode(array('status'=>true));    
   }
   /**********************************************************************************/
}