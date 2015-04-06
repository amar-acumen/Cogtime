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


class Church extends Base_controller
{
    
    private $pagination_per_page =  5;
//    private $comments_pagination_per_page =  2 ;
//    private $people_liked_pagination_per_page =  4 ;
//   
    
    public function __construct()
     {
	 	
        try
        {
            parent::__construct();
            $this->css_files = array('css/church_admin.css','css/church.css');
               parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers... 
           


            $this->load->model('church_new_model');
			$this->i_profile_id = intval(decrypt($this->session->userdata('user_id')));
            $this->upload_path = BASEPATH . '../uploads/church_logo_image/';
            $this->upload_cover_path = BASEPATH.'../uploads/church_cover_image/';
            $this->upload_membercsv_path = BASEPATH . '../uploads/church_member_csv/';
            
            
             /************upload church landing page image***************************************/
            $this->upload_landing_page_image1 = BASEPATH . '../uploads/church_landing_page_image1/';
            $this->upload_landing_page_image2 = BASEPATH . '../uploads/church_landing_page_image2/';
            $this->upload_landing_page_image3 = BASEPATH . '../uploads/church_landing_page_image3/';
            $this->upload_landing_page_image4 = BASEPATH . '../uploads/church_landing_page_image4/';
            /*************************************************/
            
            //$this->load->helper('Imagelibrary_helper');
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    
    public function index($c_name,$c_id) 
    {
      // echo $c_id;
        //die('comming soon.........');
        try
        {
            $user_id = intval(decrypt($this->session->userdata('user_id')));
            get_all_church_session($c_id);
            parent::check_is_church_admin($user_id,$c_id);
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
            //               $this->data["MAIN_MENU_SELECTED"] = 1;
            parent::check_church_id_empty(TRUE, $_SESSION['logged_church_id'], array('1'));
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');


            //            

            $data['church_arr'] =     $this->church_new_model->get_church_info($c_id);
            $VIEW = "logged/church/church_admin.phtml";


            parent::_render($data, $VIEW);

            
        }
        
        catch(Exception $err_obj)
        {
           
        } 

    } // end of index   
    
 function church_member($page_no = 0){
        
        $c_id = $_SESSION['logged_church_id'];
       //die();
        $user_id = intval(decrypt($this->session->userdata('user_id')));
                        parent::check_is_church_admin($user_id,$c_id);
        $posted=array();
        $this->data["posted"]=$posted;/*don't change*/    
        $data = $this->data;      
//               $this->data["MAIN_MENU_SELECTED"] = 1;
        parent::_set_title('::: COGTIME Xtian network :::');
        parent::_set_meta_desc('');
        parent::_set_meta_keywords('');
		parent::_add_js_arr( array( 
                                        'js/lightbox.js'
										                                    ));
                                        
        parent::_add_church_css_arr( array('css/church.css','css/church_admin.css') );
		
        parent::check_church_id_empty(TRUE, $_SESSION['logged_church_id'], array('1'));
        
       
        $where = '';
        $page = 0;
        //---------------------- for pagination back ---------------------
        if ($page_no != 0)
            $page = ($page_no - 1) * 2;
        //---------------------- end pagination back ---------------------

        ob_start();
        $this->ajax_church_member_pagination($page);
        $data['result_content'] = ob_get_contents(); //pr($data['result_content'],1);
        ob_end_clean();

        $VIEW = "logged/church/church_member.phtml";
        

        parent::_render($data, $VIEW);
              
 }
 public function ajax_church_member_pagination($page = 0) {
        try {
            ## seacrh conditions : filter ############

            $c_id = $_SESSION['logged_church_id'];
            $where = '';
            if($_SESSION['memberstatus']!='')
                $where = ' AND cm.is_approved='.$_SESSION['memberstatus'];
            $order_by = " cm.id DESC ";
            $result = $this->church_new_model->get_churchmembers($c_id,$where,$page, $order_by, $this->pagination_per_page);
            $resultCount = count($result);
            // echo $this->db->last_query(); 
            
            $total_rows = $this->church_new_model->get_churchmembers_count($c_id,$where);

            if ((!is_array($result) || !count($result) ) && $total_rows) {
                $page = $page - $this->pagination_per_page;
                $result = $this->church_new_model->get_churchmembers($c_id,$where,$page, $this->pagination_per_page, $order_by);
            }
            ## end seacrh conditions : filter ############
            //pr($result,1);
            #Jquery Pagination Starts
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url() . "logged/church/ajax_church_member_pagination";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->pagination_per_page;
            $config['uri_segment'] = 4;
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

            $config['div'] = '#table_content'; /* Here #content is the CSS selector for target DIV */
            $config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
            $config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */

            $this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();

            // getting   listing...
            $data['info_arr'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $page;

            $data['pagination_per_page'] = $this->pagination_per_page;

            # loading the view-part...
            echo $this->load->view('logged/church/ajax_member/church_member_ajax.phtml', $data, TRUE);
            
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }
	
	function church_user($page_no = 0){
        $c_id = $_SESSION['logged_church_id'];
       //die();
        $user_id = intval(decrypt($this->session->userdata('user_id')));
                        //parent::check_is_church_admin($user_id,$c_id);
        $posted=array();
        $this->data["posted"]=$posted;/*don't change*/    
        $data = $this->data;      
//               $this->data["MAIN_MENU_SELECTED"] = 1;
        parent::_set_title('::: COGTIME Xtian network :::');
        parent::_set_meta_desc('');
        parent::_set_meta_keywords('');
		parent::_add_js_arr( array( 
                                        'js/lightbox.js'
										                                    ));
                                        
        parent::_add_church_css_arr( array('css/church.css','css/church_admin.css') );
		
        parent::check_church_id_empty(TRUE, $_SESSION['logged_church_id'], array('1'));
        
       
        $where = '';
        $page = 0;
        //---------------------- for pagination back ---------------------
        if ($page_no != 0)
            $page = ($page_no - 1) * 2;
        //---------------------- end pagination back ---------------------

        ob_start();
        $this->ajax_church_user_pagination($page);
        $data['result_content'] = ob_get_contents(); //pr($data['result_content'],1);
        ob_end_clean();

        $VIEW = "logged/church/church_user.phtml";
        

        parent::_render($data, $VIEW);
              
	}
	
	public function ajax_church_user_pagination($page = 0) {
        try {
            ## seacrh conditions : filter ############

            $c_id = $_SESSION['logged_church_id'];
            $order_by = " u.id DESC ";
            $result = $this->church_new_model->get_churchusers($c_id,$page, $order_by, $this->pagination_per_page);
			
            $resultCount = count($result);
            // echo $this->db->last_query(); 
            
            $total_rows = $this->church_new_model->get_churchusers_count($c_id);

            if ((!is_array($result) || !count($result) ) && $total_rows) {
                $page = $page - $this->pagination_per_page;
                $result = $this->church_new_model->get_churchusers($c_id,$page, $this->pagination_per_page, $order_by);
            }
            ## end seacrh conditions : filter ############
            //pr($result,1);
            #Jquery Pagination Starts
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url() . "logged/church/ajax_church_user_pagination";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->pagination_per_page;
            $config['uri_segment'] = 4;
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

            $config['div'] = '#table_content'; /* Here #content is the CSS selector for target DIV */
            $config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
            $config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */

            $this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();

            // getting   listing...
            $data['info_arr'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $page;

            $data['pagination_per_page'] = $this->pagination_per_page;

            # loading the view-part...
            echo $this->load->view('logged/church/ajax_member/church_user_ajax.phtml', $data, TRUE);
            
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }
	
	
    function search_church_member_pagination()
    {
        if(count($_POST)>0)
        {
            $_SESSION['memberstatus'] = $_POST['memberstatus'];
        }
        $page = 0;
        ob_start();
        $this->ajax_church_member_pagination($page);
        $result_content = ob_get_contents(); //pr($data['result_content'],1);
        ob_end_clean();
        echo  json_encode(array('html'=>$result_content,'success' => true));
        exit;
    }
 function approve_member()
 {
    $mid = $_POST['mid'];
    $status = $_POST['status'];
    $memberid = $_POST['memberid'];



    $data = array(
              'is_approved'=>$status
            );
    $this->db->where('id',$mid);
    $this->db->update('cg_church_member',$data); 
    $s_qry = "SELECT s_email   "
                    . " FROM " . $this->db->USERS ." where id='".$memberid."'";
    $rs = $this->db->query($s_qry);
    $i_cnt = 0;
    $userarr = $rs->result();
       


    $this->load->model('mail_contents_model');
    $mail_info = $this->mail_contents_model->get_by_name("church_community_approve");

    $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
    $c_id = $_SESSION['logged_church_id'];
    $church_arr =     $this->church_new_model->get_church_info($c_id);
    $reqstatus = ($status==1)?'Approved':'Declined';

    $subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
    $subject = sprintf3( $subject, array('requeststatus'=>$reqstatus)
           ) ;
    $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
    $body = sprintf3( $body, array('churchname'=>$church_arr[0]->s_name ,'requeststatus'=>$reqstatus)
           ) ;

   
    
    
        $to      = $userarr[0]->s_email;
        $subject = $subject;
        $message = $body;
        $headers = 'From: admin@cogtime.com' . "\r\n" .
            'Reply-To: admin@cogtime.com' . "\r\n" .
            'X-Mailer: PHP/' . phpversion() . "\r\n";
        $headers  .= 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        mail($to, $subject, $message, $headers);
    
   
    echo json_encode(array('success'=>true));
    exit;
 }

  function delete_member () {
        $groupid = $this->input->post('gr_id');
		//echo $_SESSION['logged_church_id'];exit;
       // $member_arr = $this->prayer_group_model->get_members_by_grpid($groupid);
        //pr($member_arr);
        $data = array(
					   'is_deleted' => 1
                       );
            
            $this->db->where('id', $groupid);
            $res = $this->db->update('cg_church_member', $data);
			//echo $this->db->last_query();
        
		ob_start();
        $this->ajax_church_member_pagination($page);
        $data['result_content'] = ob_get_contents(); //pr($data['result_content'],1);
        ob_end_clean();
        echo json_encode(array('html' => $data['result_content'], 'msg' => 'Member Deleted Successfully'));
        exit;
    
	}
	
	function block_member () {
        $groupid = $this->input->post('gr_id');
		//echo $_SESSION['logged_church_id'];exit;
       // $member_arr = $this->prayer_group_model->get_members_by_grpid($groupid);
        //pr($member_arr);
        $data = array(
					   'is_blocked' => 0
                       );
            
            $this->db->where('id', $groupid);
            $res = $this->db->update('cg_church_member', $data);
			//echo $this->db->last_query();
        
		ob_start();
        $this->ajax_church_member_pagination($page);
        $data['result_content'] = ob_get_contents(); //pr($data['result_content'],1);
        ob_end_clean();
        echo json_encode(array('html' => $data['result_content'], 'msg' => 'Member Blocked Successfully'));
        exit;
    
	}
function unblock_member () {
        $groupid = $this->input->post('gr_id');
		//echo $_SESSION['logged_church_id'];exit;
       // $member_arr = $this->prayer_group_model->get_members_by_grpid($groupid);
        //pr($member_arr);
        $data = array(
					   'is_blocked' => 1
                       );
            
            $this->db->where('id', $groupid);
            $res = $this->db->update('cg_church_member', $data);
			//echo $this->db->last_query();
        
		ob_start();
        $this->ajax_church_member_pagination($page);
        $data['result_content'] = ob_get_contents(); //pr($data['result_content'],1);
        ob_end_clean();
        echo json_encode(array('html' => $data['result_content'], 'msg' => 'Member Unblocked Successfully'));
        exit;
    
	}	
 
 function church_invited_member($page_no = 0){
        
        $c_id = $_SESSION['logged_church_id'];
       //die();
        $user_id = intval(decrypt($this->session->userdata('user_id')));
        parent::check_is_church_admin($user_id,$c_id);
        $posted=array();
        $this->data["posted"]=$posted;/*don't change*/    
        $data = $this->data;      
//               $this->data["MAIN_MENU_SELECTED"] = 1;
        parent::_set_title('::: COGTIME Xtian network :::');
        parent::_set_meta_desc('');
        parent::_set_meta_keywords('');
		parent::_add_js_arr( array( 
                                        'js/lightbox.js'
										                                    ));
                                        
        parent::_add_church_css_arr( array('css/church.css','css/church_admin.css') );
		
        parent::check_church_id_empty(TRUE, $_SESSION['logged_church_id'], array('1'));
        
       
        $where = '';
        $page = 0;
        //---------------------- for pagination back ---------------------
        if ($page_no != 0)
            $page = ($page_no - 1) * 2;
        //---------------------- end pagination back ---------------------

        ob_start();
        $this->ajax_church_invited_member_pagination($page);
        $data['result_content'] = ob_get_contents(); //pr($data['result_content'],1);
        ob_end_clean();

        $VIEW = "logged/church/church_invited_member.phtml";
        

        parent::_render($data, $VIEW);
              
 }
 public function ajax_church_invited_member_pagination($page = 0) {
        try {
            ## seacrh conditions : filter ############

            $c_id = $_SESSION['logged_church_id'];
            //$where = '';
            $order_by = " id DESC ";
            $result = $this->church_new_model->get_church_invited_members($c_id,$page, $order_by, $this->pagination_per_page);
            $resultCount = count($result);
             //echo $this->db->last_query(); 
            
            $total_rows = $this->church_new_model->get_church_invited_members_count($c_id);

            if ((!is_array($result) || !count($result) ) && $total_rows) {
                $page = $page - $this->pagination_per_page;
                $result = $this->church_new_model->get_church_invited_members($c_id,$where,$page, $this->pagination_per_page, $order_by);
            }
            ## end seacrh conditions : filter ############
            //pr($result);
            #Jquery Pagination Starts
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url() . "logged/church/ajax_church_invited_member_pagination";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->pagination_per_page;
            $config['uri_segment'] = 4;
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

            $config['div'] = '#table_content'; /* Here #content is the CSS selector for target DIV */
            $config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
            $config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */

            $this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();

            // getting   listing...
            $data['info_arr'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $page;

            $data['pagination_per_page'] = $this->pagination_per_page;

            # loading the view-part...
            echo $this->load->view('logged/church/ajax_member/church_invited_member_ajax.phtml', $data, TRUE);
            
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }
 
 function church_setting(){
    // die('ok');
    $c_id = $_SESSION['logged_church_id'];
      $user_id = intval(decrypt($this->session->userdata('user_id')));
                        parent::check_is_church_admin($user_id,$c_id);
      $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;   
             parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
             parent::check_church_id_empty(TRUE, $_SESSION['logged_church_id'], array('1'));
           
              $data['church_arr'] =     $this->church_new_model->get_church_info($c_id);
              $data['service_arr'] = $this->church_new_model->get_service_info($c_id);  
                $VIEW = "logged/church/church_setting.phtml";
               parent::_render($data, $VIEW);
 }
 function add_church_data(){
     // pr($_POST,1);
	 $church_open_arr = array();
	 $church_open_arr['sunday']=$this->input->post('sun_ch_open');
	 $church_open_arr['monday']=$this->input->post('mon_ch_open');
	 $church_open_arr['tuesday']=$this->input->post('tues_ch_open');
	 $church_open_arr['wednesday']=$this->input->post('wed_ch_open');
	 $church_open_arr['thursday']=$this->input->post('thurs_ch_open');
	 $church_open_arr['friday']=$this->input->post('fri_ch_open');
	 $church_open_arr['saturday']=$this->input->post('sat_ch_open');
	 
	 $church_close_arr = array();
	 $church_close_arr['sunday']=$this->input->post('sun_ch_close');
	 $church_close_arr['monday']=$this->input->post('mon_ch_close');
	 $church_close_arr['tuesday']=$this->input->post('tues_ch_close');
	 $church_close_arr['wednesday']=$this->input->post('wed_ch_close');
	 $church_close_arr['thursday']=$this->input->post('thurs_ch_close');
	 $church_close_arr['friday']=$this->input->post('fri_ch_close');
	 $church_close_arr['saturday']=$this->input->post('sat_ch_close');
	 
	 $church_special_note_arr = array();
	 $church_special_note_arr['sunday']=$this->input->post('sun_special_note');
	 $church_special_note_arr['monday']=$this->input->post('mon_special_note');
	 $church_special_note_arr['tuesday']=$this->input->post('tues_special_note');
	 $church_special_note_arr['wednesday']=$this->input->post('wed_special_note');
	 $church_special_note_arr['thursday']=$this->input->post('thurs_special_note');
	 $church_special_note_arr['friday']=$this->input->post('fri_special_note');
	 $church_special_note_arr['saturday']=$this->input->post('sat_special_note');
	 
//	 pr($church_open_arr);
//         pr($church_close_arr,1);
    $info['ch_open_time'] = json_encode($church_open_arr);
    $info['ch_close_time'] = json_encode($church_close_arr);
	$info['ch_special_note'] = json_encode($church_special_note_arr);
    $info['ch_banner_heading'] = $this->input->post('banner_heading');
    $info['ch_details'] = $this->input->post('church_des');

    list($width_ch_logo, $height_ch_logo, $type, $attr) =  getimagesize($_FILES['ch_logo']['tmp_name']);
    list($width_ch_cover, $height_ch_cover, $type, $attr) =  getimagesize($_FILES['ch_cover']['tmp_name']);

    list($width_ch_profile_img, $height_ch_profile_img, $type, $attr) =  getimagesize($_FILES['ch_profile_img']['tmp_name']);

    $flag = 0;
    if($_FILES['ch_logo']['name']!='')
    {
        if($width_ch_logo < 260 || $height_ch_logo < 40)
        {
            $flag = 1;
            $_SESSION['ch_logo_msg'] = "Upload image size must be greater than 260x40 ";
            header('location:church_setting');
            exit;
        }
        
    }
    
    if($_FILES['ch_cover']['name']!='')
    {
        if($width_ch_cover < 1300 || $height_ch_cover < 400 )
        {
            $flag = 1;
            $_SESSION['ch_cover_msg'] = "Upload image size must be greater than 1300x400 ";
            header('location:church_setting');
            exit;
        }
    }
    
    if( $_FILES['ch_profile_img']['name']!='' )
    {
        if($width_ch_profile_img < 136 && $height_ch_profile_img < 136)
        {
            $flag = 1;
            $_SESSION['ch_profile_img_msg'] = "Upload image size must be greater than 136x136 ";
            header('location:church_setting');
            exit;
        }
    }   
    
    if($flag==0)         
    {
        if (isset($_FILES['ch_logo']['name']) && $_FILES['ch_logo']['name'] != '') {
            $info['ch_logo'] = $this->_upload_profile_image();
        }
        if (isset($_FILES['ch_cover']['name']) && $_FILES['ch_cover']['name'] != '') {
            $info['ch_cover'] = $this->_upload_cover_image();
        }
        
        if (isset($_FILES['ch_profile_img']['name']) && $_FILES['ch_profile_img']['name'] != '') {
            $info['ch_profile_img'] = $this->_upload_profile_photo();
        }
        $c_id = $this->input->post('c_id');
   
         $data = $info;
		 //pr($data);
         
        $this->db->where('id', $c_id);
        $rs = $this->db->update('cg_church', $data);
		//echo $this->db->last_query();
        if($rs){
            $url = base_url().'church_setting?sta=1';
            header('location:'.$url.'');
        }
    }
    
                
     //pr($info,1);
     


 
}
 

function add_service(){
    //pr($_POST,1);
    $open_time = $this->input->post('ch_open');
    $close_time = $this->input->post('ch_close');
	$week_day = $this->input->post('ch_service_week_day');
    $des = $this->input->post('des');
    $ch_id = $this->input->post('ch_id');
    for($j = 0 ; $j < count($week_day) ; $j++){
    for($i = 0; $i < count($open_time); $i++){
        $data = array(
   'c_id' => $ch_id ,
   'c_start_time' => $open_time[$i] ,
   'c_end_time' => $close_time[$i],
   'c_des' => $des[$i], 
   'ch_service_week_day' => $week_day[$j],	 
   'c_update' => get_db_datetime()         
);

$rs = $this->db->insert('cg_church_schedul', $data); 
    }
    }
    if($rs){
    $url = base_url().'church_setting?ser=1';
    header('location:'.$url.'');
}
   // echo $ch_id;
   
    //pr($_POST,1);
}

function general_setting(){
  // pr($_POST,1);
    $wel_hed = trim($this->input->post('wel_hed'));
    $wel_des = trim($this->input->post('wel_des'));
    $ch_id = $this->input->post('ch_id');
    $data = array(
               'w_heading' => $wel_hed,
               'w_des' => $wel_des
               );
    //pr($data,1);
     $this->db->where('id', $ch_id);
     $res = $this->db->update('cg_church', $data); 
//var_dump($res);
     if($res){
    $url = base_url().'church_setting?genS=1';
    header('location:'.$url.'');
}
    
}

    // pr($_POST,1);
     
    public function import_member_csv()
    {
        parent::check_login(TRUE, '', array('1')); 

        if ( isset($_FILES["csv"])) 
        {
            if(getExtension($_FILES["csv"]["name"])!='.csv')
            {
                $arr_messages['err_csv'] = "Please upload csv";
                echo json_encode(array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'please select CSV files to import!'));
                exit;
            }
            $file_name  = $_FILES["csv"]["name"];
            $destfile =  $this->upload_membercsv_path.$file_name;
            //$_FILES["file"]["type"] 
            move_uploaded_file($_FILES["csv"]["tmp_name"],$destfile);

            $row = 1;

           
            $this->load->model('mail_contents_model');
            $mail_info = $this->mail_contents_model->get_by_name("church_community_invitation_mail");

            $subject = 'test';
            /*$subject = sprintf3( $subject, array('sender_name'=> $profile_info["s_first_name"],
                              'project_name'=> $project_name
                           ));*/
            //$body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
			//$body = sprintf3( $body, array('churchurl'=> base_url().'church_registration_by_email/'.$_SESSION['logged_church_id'].'/1') );
			
            if (($handle = fopen($destfile, "r")) !== FALSE) {
               
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    
                    $num = count($data);
                  
                    $row++;
                 
					if($row>2)
					{
                                            
						$invite_mem_info = array(
							'name' => $data[0],
							'email' => $data[1],
							'church_id' => $_SESSION['logged_church_id'],
							'invitation_sent_date' => get_db_datetime()
							);
                                               // pr($invite_mem_info);
                                                /****************already member***********************/
                                                /**********if already invited member**********************************/
                                    $query1 = $this->db->get_where('cg_church_member_invitation', array('email' => $invite_mem_info['email'] , 'church_id'=>$_SESSION['logged_church_id'] ));
                                    $result = $query1->result();
                                    
                                     $query2 = $this->db->get_where('cg_church', array('ch_admin_id' => get_user_id_byemail($invite_mem_info['email']) , 'id' =>$_SESSION['logged_church_id']));
                                      $result1 = $query2->result();
                                    
                                    if(count($result) > 0 || count($result1) > 0){
                                         continue;
                                    }
                                                /*******************************************/
                                                
                                                
						$this->db->insert('cg_church_member_invitation', $invite_mem_info);
                                                $add_mem_id = $this->db->insert_id();
						
                                              // $invite_mem_info['email'].'===';
                                                                     /*************check already cogtime user*******************/
                                                $query = $this->db->get_where('cg_users', array('s_email' => $invite_mem_info['email'] ,'i_status' => 1));
                                                $result = $query->result();
                                                
                                                 $this->load->library('email');
                                                $this->load->helper('html');
                                              $email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                                                                    'priority' => '1');
                                                      $this->email->initialize($email_setting);
                                                if($result[0]->id == null){
                                                    $location =  base_url().'church_registration_by_email/'.$_SESSION['logged_church_id'].'/1/'.$add_mem_id;
                                                   // echo 'new';
                                                    $logo="http://cogtime.com/images/logo.png";
    $body = '<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#e9f3f5" style="font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:19px;">
  <tr>
    <td align="left" style="background:#013D62; border-bottom:5px solid #62C3BC; padding:15px 0 15px 20px;"><img src="'.$logo.'" alt= ""></td>
  </tr>
  <tr style="border-top:1px solid #ffffff;">
    <td style="padding-top:10px; padding-bottom:10px;">&nbsp;</td>
  </tr>
  <tr>
  	
  </tr>
  <tr>
  	<td style="padding:15px;"><p>Hello,</p>
<p>You are invited to join church community. Please click on link and register to join the church community. </p>

<p><a href='.$location.'>Click</a></p>
            <p>Team Cogtime</p>
	</td>
</tr>
  <tr>
    <td align="center" valign="middle" style="background:#A8A7A7; padding:15px;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle" style="color:#d3edfd; font-family:Arial, Helvetica, sans-serif; font-size:12px;"> <a href="http://acumencs.com/drandpt-arabic/contact-us/" style="color:#d3edfd; text-decoration:none;"></a></td>
        
        <td align="right" style="color:#013d62; font-family:Arial, Helvetica, sans-serif; font-size:12px; text-align="center" ">© All Rights Reserved<span style="color:#525252;"><strong> COGTIME 2014  </strong></span></td>
      </tr>
    </table></td>
  </tr>
</table>'; 
$this->email->from('admin@cogtime.com', 'From Cogtime');
$this->email->to($invite_mem_info['email']);
//->email->bcc("$mailids");
//$this->email->cc('arif.zisu@gmail.com');
//$this->email->bcc('them@their-example.com');

$this->email->subject('Churh Member Request');
$this->email->message("$body");

 $this->email->send();	
                                                    
                                                    
                                                    
                                                    
                                                    
                                                    
                                                }else{
                                                    //echo 'old';
                                                   $location =  base_url().'already_user/'.$_SESSION['logged_church_id'].'/1/'.$result[0]->id.'/'.$add_mem_id;
                                                    $logo="http://cogtime.com/images/logo.png";
    $body = '<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#e9f3f5" style="font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:19px;">
  <tr>
    <td align="left" style="background:#013D62; border-bottom:5px solid #62C3BC; padding:15px 0 15px 20px;"><img src="'.$logo.'" alt= ""></td>
  </tr>
  <tr style="border-top:1px solid #ffffff;">
    <td style="padding-top:10px; padding-bottom:10px;">&nbsp;</td>
  </tr>
  <tr>
  	
  </tr>
  <tr>
  	<td style="padding:15px;"><p>Hello,</p>
<p>You are invited to join church community. Please click on link and register to join the church community. </p>

<p><a href='.$location.'>Click</a></p>
      <p>Team Cogtime</p>      
	</td>
</tr>
  <tr>
    <td align="center" valign="middle" style="background:#A8A7A7; padding:15px;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="middle" style="color:#d3edfd; font-family:Arial, Helvetica, sans-serif; font-size:12px;"> <a href="http://acumencs.com/drandpt-arabic/contact-us/" style="color:#d3edfd; text-decoration:none;"></a></td>
        
        <td align="right" style="color:#013d62; font-family:Arial, Helvetica, sans-serif; font-size:12px; text-align="center" ">© All Rights Reserved<span style="color:#525252;"><strong> COGTIME 2014  </strong></span></td>
      </tr>
    </table></td>
  </tr>
</table>'; 
$this->email->from('admin@cogtime.com', 'From Cogtime');
$this->email->to($invite_mem_info['email']);
//->email->bcc("$mailids");
//$this->email->cc('arif.zisu@gmail.com');
//$this->email->bcc('them@their-example.com');

$this->email->subject('Churh Member Request');
$this->email->message("$body");

 $this->email->send();	
                                                  
                                                }   
                                            //pr($result);
                                                 //echo count($result);
                                                        
//                                                  if(!empty($result) && count($result) == 1){
//                                                       $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
//                                                      foreach ($query->result() as $row)
//                                                        {
//                                                          $user_id =  $row->id;
//                                                        } 
//                                                   $location =  base_url().'already_user/'.$_SESSION['logged_church_id'].'/1/'.$user_id.'/'.$add_mem_id;
//                                                  $body = sprintf3( $body, array('churchurl'=> $location) );
//                                                    
//                                                }else if(empty($result) && count($result) == 0) {
//                                                     $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
//                                                   
//                                                   $location =  base_url().'church_registration_by_email/'.$_SESSION['logged_church_id'].'/1/'.$add_mem_id;
//                                                   $body = sprintf3( $body, array('churchurl'=> $location) );
//                                                }
//                                               $to      =  $invite_mem_info['email'];
//                                                $subject = $subject;
//                                           $message = $body;
//                                            $headers = 'From: admin@cogtime.com' . "\r\n" .
//                                                'Reply-To: admin@cogtime.com' . "\r\n" .
//                                                'X-Mailer: PHP/' . phpversion() . "\r\n";
//                                            $headers  .= 'MIME-Version: 1.0' . "\r\n";
//                                            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
//                                            mail($to, $subject, $message, $headers);
                                               
					}
//                    for ($c=1; $c < 2; $c++) {
                     
//                    }
					
		
                }
               
                fclose($handle);
            }

        }
        else {
            $arr_messages['err_csv'] = "* Required Field.";
            echo json_encode(array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'please select CSV files to import!'));
            exit;
        }
        echo json_encode(array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'Mail sent successfully'));
        exit;
    }

	public function _upload_profile_photo($prev_img = '', $filefieldname) {

        parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by non logged user and admin
        #pr($_FILES);
        $fileElementName = 'ch_profile_img';
        if (!empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] != '') {
            preg_match('/(^.*)\.([^\.]*)$/', $_FILES[$fileElementName]['name'], $matches);
            $ext = "";
            if (count($matches) > 0) {
                $ext = strtolower($matches[2]);
                $original_name = $matches[1];
            } else
                $original_name = 'image';
            $imagename = createImageName($original_name);
            if (test_file($this->upload_path . $imagename . '-profile.' . $ext)) {
                for ($i = 0; test_file($this->upload_path . $imagename . '-' . $i . '-thumb.' . $ext); $i++) {
                    
                }
                $new_imagename = $imagename . '-' . $i;
            } else {
                $new_imagename = $imagename;
            }

            $this->imagename = $new_imagename;

            $this->upload_image = $this->upload_path . $new_imagename . '.' . $ext;
            @move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $this->upload_image);

            $config['source_image'] = $this->upload_image;
            $config['thumb_marker'] = '-profile';
            $config['crop'] = true;
            $config['crop_from'] = 'middle';
            $config['width'] = 136;
            $config['height'] = 136;
            $config['small_image_resize'] = 'bigger';

            resize_exact($config);

            unset($config);

            $this->s_picture_path = $new_imagename . '.' . $ext;

            @unlink($this->upload_image); //Unlink the original image........
            //@unlink($this->upload_path.getThumbName($prev_img,'thumb')); //Unlink the prevoius image........

            return $this->s_picture_path;
        } else {
            return $prev_img; // Unchanged previous image
        }
    }
	
	


    function church_plugin(){
    // die('ok');
    $c_id = $_SESSION['logged_church_id'];
    $user_id = intval(decrypt($this->session->userdata('user_id')));
    parent::check_is_church_admin($user_id,$c_id);
      $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;   
             parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
             parent::check_church_id_empty(TRUE, $_SESSION['logged_church_id'], array('1'));
           
              $data['church_arr'] =     $this->church_new_model->get_church_info($c_id);
              
              $data['service_arr'] = $this->church_new_model->get_service_info($c_id);  
                $VIEW = "logged/church/church_plugin.phtml";
               parent::_render($data, $VIEW);
 }

 function get_church_code($size)
 {
    $c_id = $_SESSION['logged_church_id'];
    $ch_arr = $this->church_new_model->get_church_info($c_id);

    $church_plugin  = '<a herf="'.base_url().$ch_arr[0]->ch_page_url.'"><img src="'.base_url().'uploads/church_plugin_image/'.$size.'.jpg" /></a>';

    $data = array(
               'church_plugin' => htmlspecialchars($church_plugin)
               );
    //pr($data,1);
    $this->db->where('id', $c_id);
    $res = $this->db->update('cg_church', $data); 

    echo json_encode(array('churl' => $church_plugin,'result'=>'success' ));
    exit;
 }
	
    public function _upload_profile_image($prev_img = '', $filefieldname) {

        parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by non logged user and admin
        #pr($_FILES);
        $fileElementName = 'ch_logo';
        if (!empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] != '') {
            preg_match('/(^.*)\.([^\.]*)$/', $_FILES[$fileElementName]['name'], $matches);
            $ext = "";
            if (count($matches) > 0) {
                $ext = strtolower($matches[2]);
                $original_name = $matches[1];
            } else
                $original_name = 'image';


            $imagename = createImageName($original_name);

            if (test_file($this->upload_path . $imagename . '-thumb.' . $ext)) {
                for ($i = 0; test_file($this->upload_path . $imagename . '-' . $i . '-thumb.' . $ext); $i++) {
                    
                }

                $new_imagename = $imagename . '-' . $i;
            } else {
                $new_imagename = $imagename;
            }

            $this->imagename = $new_imagename;

            $this->upload_image = $this->upload_path . $new_imagename . '.' . $ext;
            #echo $this->upload_path.' === ';  echo $this->upload_image ;

            @move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $this->upload_image);



            $config['source_image'] = $this->upload_image;
            $config['thumb_marker'] = '-thumb';
            $config['crop'] = true;
            $config['crop_from'] = 'middle';
            $config['width'] = 200;
            $config['height'] = 25;
            $config['small_image_resize'] = 'bigger';

            resize_exact($config);

            $config = array();
            $config['source_image'] = $this->upload_image;
            $config['thumb_marker'] = '-main';
            $config['crop'] = true;
            $config['width'] = 300;
            $config['height'] = 40;
            $config['crop_from'] = 'middle';
            $config['small_image_resize'] = 'bigger';
            resize_exact($config);

            
            unset($config);

            $this->s_picture_path = $new_imagename . '.' . $ext;

            @unlink($this->upload_image); //Unlink the original image........
            //@unlink($this->upload_path.getThumbName($prev_img,'thumb')); //Unlink the prevoius image........

            return $this->s_picture_path;
        } else {
            return $prev_img; // Unchanged previous image
        }
    }
    public function _upload_cover_image($prev_img = '', $filefieldname) {

        parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by non logged user and admin
        #pr($_FILES);
        $fileElementName = 'ch_cover';
        if (!empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] != '') {
            preg_match('/(^.*)\.([^\.]*)$/', $_FILES[$fileElementName]['name'], $matches);
            $ext = "";
            if (count($matches) > 0) {
                $ext = strtolower($matches[2]);
                $original_name = $matches[1];
            } else
                $original_name = 'image';


            $imagename = createImageName($original_name);

            if (test_file($this->upload_cover_path . $imagename . '-thumb.' . $ext)) {
                for ($i = 0; test_file($this->upload_cover_path . $imagename . '-' . $i . '-thumb.' . $ext); $i++) {
                    
                }

                $new_imagename = $imagename . '-' . $i;
            } else {
                $new_imagename = $imagename;
            }

            $this->imagename = $new_imagename;

            $this->upload_image = $this->upload_cover_path . $new_imagename . '.' . $ext;
            #echo $this->upload_path.' === ';  echo $this->upload_image ;

            @move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $this->upload_image);



            $config['source_image'] = $this->upload_image;
            $config['thumb_marker'] = '-thumb';
            $config['crop'] = true;
            $config['crop_from'] = 'middle';
            $config['width'] = 300;
            $config['height'] = 150;
            $config['small_image_resize'] = 'bigger';

            resize_exact($config);

            $config = array();
            $config['source_image'] = $this->upload_image;
            $config['thumb_marker'] = '-main';
            $config['crop'] = true;
            $config['width'] = 1300;
            $config['height'] = 400;
			$config['crop_from'] = 'middle';
            $config['small_image_resize'] = 'bigger';
            resize_exact($config);
             /******************04-12-2014****************************/
//            $config = array();
//            $config['source_image'] = $this->upload_image;
//            $config['thumb_marker'] = '-large';
//            $config['crop'] = false;
//            $config['width'] = 400;
//            $config['height'] = 400;
//            $config1['crop_from'] = 'middle';
//            $config['small_image_resize'] = 'no_resize';
//            resize_exact($config);
            /*********************************************/
            
            unset($config);

            $this->s_picture_path = $new_imagename . '.' . $ext;

            @unlink($this->upload_image); //Unlink the original image........
            //@unlink($this->upload_path.getThumbName($prev_img,'thumb')); //Unlink the prevoius image........

            return $this->s_picture_path;
        } else {
            return $prev_img; // Unchanged previous image
        }
    }
    function church_subadmin(){

        $c_id = $_SESSION['logged_church_id'];
        $user_id = intval(decrypt($this->session->userdata('user_id')));
                        parent::check_is_church_admin($user_id,$c_id);
        $posted=array();
        $this->data["posted"]=$posted;/*don't change*/    
        $data = $this->data;      
//               $this->data["MAIN_MENU_SELECTED"] = 1;
        parent::_set_title('::: COGTIME Xtian network :::');
        parent::_set_meta_desc('');
        parent::_set_meta_keywords('');
		
		parent::_add_js_arr( array( 
                                        'js/lightbox.js'
										                                    ));
                                        
        parent::_add_church_css_arr( array('css/church.css','css/church_admin.css') );
		
         parent::check_church_id_empty(TRUE, $_SESSION['logged_church_id'], array('1'));
       
        //$data['member_arr'] =     $this->church_new_model->get_churchsubadmin($c_id);
		
		$where = '';
        $page = 0;
        //---------------------- for pagination back ---------------------
        if ($page_no != 0)
            $page = ($page_no - 1) * 2;
        //---------------------- end pagination back ---------------------

        ob_start();
        $this->ajax_church_subadmin_pagination($page);
        $data['result_content'] = ob_get_contents(); //pr($data['result_content'],1);
        ob_end_clean();
        
        $VIEW = "logged/church/church_subadmin.phtml";
        

        parent::_render($data, $VIEW);
              
    }

	public function ajax_church_subadmin_pagination($page = 0) {
        try {
            ## seacrh conditions : filter ############

            $c_id = $_SESSION['logged_church_id'];
            $result = $this->church_new_model->get_churchsubadmin($c_id,$page,$this->pagination_per_page);
            $resultCount = count($result);
            // echo $this->db->last_query(); 
            
            $total_rows = $this->church_new_model->get_churchsubadmin_count($c_id);
			
            if ((!is_array($result) || !count($result) ) && $total_rows) {
                $page = $page - $this->pagination_per_page;
                $result = $this->church_new_model->get_churchsubadmin($c_id,$page,$this->pagination_per_page);
            }
            ## end seacrh conditions : filter ############
            //pr($result);
            #Jquery Pagination Starts
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url() . "logged/church/ajax_church_subadmin_pagination";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->pagination_per_page;
            $config['uri_segment'] = 4;
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

            $config['div'] = '#table_content'; /* Here #content is the CSS selector for target DIV */
            $config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
            $config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */

            $this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();

            // getting   listing...
            $data['info_arr'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $page;

            $data['pagination_per_page'] = $this->pagination_per_page;

            # loading the view-part...
            echo $this->load->view('logged/church/ajax_member/church_subadmin_ajax.phtml', $data, TRUE);
            
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    function church_addsubadmin(){
//pr($_POST,1);
        $c_id = $_SESSION['logged_church_id'];
        $user_id = intval(decrypt($this->session->userdata('user_id')));
                        parent::check_is_church_admin($user_id,$c_id);
        $posted=array();
        $this->data["posted"]=$posted;/*don't change*/    
        $data = $this->data;      
//               $this->data["MAIN_MENU_SELECTED"] = 1;
        parent::_set_title('::: COGTIME Xtian network :::');
        parent::_set_meta_desc('');
        parent::_set_meta_keywords('');
         parent::check_church_id_empty(TRUE, $_SESSION['logged_church_id'], array('1'));
       
        if(count($_POST)>0)
        {
            $data['searchemail'] = $_POST['searchemail'];
            if($_POST['subadminaccess']==1)
            {
                $accessstring = '';
                foreach ($_POST['assign'] as $value) {
                    $accessstring .= $value.',';
                }
                $accessstring = substr($accessstring,0,-1);
                $data = array(
                           'role' => 2,
                           'access' => $accessstring
                           );
              // pr($_POST,1);
               $this->db->update('cg_church_member', $data, array('member_id' => $_POST['memberid'] , 'church_id'=>$c_id ));
              //  echo $this->db->last_query();  die('ok');            
                header('location:'.base_url().'church-subadmin'); 
            }
            else
            {

                $wh = " AND u.s_email='".$_POST['searchemail']."'";
                $sql = 'select u.s_email from cg_users AS u WHERE u.s_email="'.$_POST['searchemail'].'"';
                        //echo $sql;
                $query = $this->db->query($sql);
                $result = $query->result();
                
                if(count($result) == 0)
                {
                    $_SESSION['addsubadminmsg'] = 'Wrong mail id';
                }
                else
                {
                    $data['member_arr'] =     $this->church_new_model->get_churchmembers($c_id,$wh,'',' cm.id desc ','');
                    //pr($data['member_arr'],1);
                    if(count($data['member_arr']) == 0)
                    {
                        $_SESSION['addsubadminmsg'] = 'Mail id does not exists in member list';
                    } 
                    else if($data['member_arr'][0]->role == 2)
                    {
                        $_SESSION['addsubadminmsg'] = 'Already a subadmin';
                    }  
                }    
                
            }
            
            
        }
        
        
        $VIEW = "logged/church/church_addsubadmin.phtml";
        

        parent::_render($data, $VIEW);
              
    }


    function church_editsubadmin($id){
         
        $c_id = $_SESSION['logged_church_id'];
        $user_id = intval(decrypt($this->session->userdata('user_id')));
        parent::check_is_church_admin($user_id,$c_id);
        $posted=array();
        $this->data["posted"]=$posted;/*don't change*/    
        $data = $this->data;      
//               $this->data["MAIN_MENU_SELECTED"] = 1;
        parent::_set_title('::: COGTIME Xtian network :::');
        parent::_set_meta_desc('');
        parent::_set_meta_keywords('');
       
        $wh = " AND cm.id='".$id."'";
       
        $data['member_arr'] =  $this->church_new_model->get_churchmembers($c_id,$wh,'',' cm.id desc','');
        
        if($_POST['subadminaccess']==1)
        {
            $accessstring = '';
            //pr($_POST['assign'],1);
            foreach ($_POST['assign'] as $value) {
                $accessstring .= $value.',';
            }
            $accessstring = substr($accessstring,0,-1);
            $data = array(
                       'access' => $accessstring
                       );
            
            $this->db->where('id', $_POST['memberid']);
            $res = $this->db->update('cg_church_member', $data);
            header('location:'.base_url().'church-subadmin'); 
        }
        
        $VIEW = "logged/church/church_editsubadmin.phtml";
        

        parent::_render($data, $VIEW);
              
    }
    
    function delete_subadmin () {
        $groupid = $this->input->post('gr_id');
		//echo $_SESSION['logged_church_id'];exit;
       // $member_arr = $this->prayer_group_model->get_members_by_grpid($groupid);
        //pr($member_arr);
        $data = array(
                       'access' => NULL,
					   'role' => 1
                       );
            
            $this->db->where('id', $groupid);
            $res = $this->db->update('cg_church_member', $data);
			//echo $this->db->last_query();
        
		ob_start();
        $this->ajax_church_subadmin_pagination($page);
        $data['result_content'] = ob_get_contents(); //pr($data['result_content'],1);
        ob_end_clean();
        echo json_encode(array('html' => $data['result_content'], 'msg' => 'Subadmin Deleted Successfully'));
        exit;
    
	}
	
   /***************************for upload landing page image***********************************************/
     function add_landing_page_img(){
       //  pr($_POST,1);
          $info['img1_des'] = trim($this->input->post('ch_img1_txt'));
         $info['img2_des'] = $this->input->post('ch_img2_txt');
         $info['img3_des'] = $this->input->post('ch_img3_txt');
         $info['img4_des'] = $this->input->post('ch_img4_txt');
         $ch_id = $this->input->post('ch_id');
         

          if (isset($_FILES['ch_img1']['name']) && $_FILES['ch_img1']['name'] != '') {
                    $info['img1'] = $this->_upload_landing_page_image_one();
                }
                if (isset($_FILES['ch_img2']['name']) && $_FILES['ch_img2']['name'] != '') {
                    $info['img2'] = $this->_upload_landing_page_image_two();
                }
                if (isset($_FILES['ch_img3']['name']) && $_FILES['ch_img3']['name'] != '') {
                    $info['img3'] = $this->_upload_landing_page_image_three();
                }
                if (isset($_FILES['ch_img4']['name']) && $_FILES['ch_img4']['name'] != '') {
                    $info['img4'] = $this->_upload_landing_page_image_four();
                }
               $data = $info;

$this->db->where('id', $ch_id);
$res = $this->db->update('cg_church', $data); 

             if($res){
    $url = base_url().'church_setting?img=1';
    header('location:'.$url.'');
}    
                
       // pr($_POST,1);
    }
      public function _upload_landing_page_image_one($prev_img = '', $filefieldname) {

        parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by non logged user and admin
        #pr($_FILES);
        $fileElementName = 'ch_img1';
        if (!empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] != '') {
            preg_match('/(^.*)\.([^\.]*)$/', $_FILES[$fileElementName]['name'], $matches);
            $ext = "";
            if (count($matches) > 0) {
                $ext = strtolower($matches[2]);
                $original_name = $matches[1];
            } else
                $original_name = 'image';


            $imagename = createImageName($original_name);

            if (test_file($this->upload_landing_page_image1 . $imagename . '-thumb.' . $ext)) {
                for ($i = 0; test_file($this->upload_landing_page_image1 . $imagename . '-' . $i . '-thumb.' . $ext); $i++) {
                    
                }

                $new_imagename = $imagename . '-' . $i;
            } else {
                $new_imagename = $imagename;
            }

            $this->imagename = $new_imagename;

            $this->upload_image = $this->upload_landing_page_image1 . $new_imagename . '.' . $ext;
            #echo $this->upload_path.' === ';  echo $this->upload_image ;

            @move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $this->upload_image);



            $config['source_image'] = $this->upload_image;
            $config['thumb_marker'] = '-thumb';
            $config['crop'] = true;
            $config['crop_from'] = 'middle';
            $config['width'] = 330;
            $config['height'] = 220;
            $config['small_image_resize'] = 'bigger';

            resize_exact($config);

            $config = array();
            $config['source_image'] = $this->upload_image;
            $config['thumb_marker'] = '-main';
            $config['crop'] = true;
            $config['width'] = 350;
            $config['height'] = 250;
            $config1['crop_from'] = 'middle';
            $config['small_image_resize'] = 'no_resize';
            resize_exact($config);
             /******************04-12-2014****************************/
//            $config = array();
//            $config['source_image'] = $this->upload_image;
//            $config['thumb_marker'] = '-large';
//            $config['crop'] = false;
//            $config['width'] = 400;
//            $config['height'] = 400;
//            $config1['crop_from'] = 'middle';
//            $config['small_image_resize'] = 'no_resize';
//            resize_exact($config);
            /*********************************************/
            
            unset($config);

            $this->s_picture_path = $new_imagename . '.' . $ext;

            @unlink($this->upload_image); //Unlink the original image........
            //@unlink($this->upload_path.getThumbName($prev_img,'thumb')); //Unlink the prevoius image........

            return $this->s_picture_path;
        } else {
            return $prev_img; // Unchanged previous image
        }
    }
     public function _upload_landing_page_image_two($prev_img = '', $filefieldname) {

        parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by non logged user and admin
        #pr($_FILES);
        $fileElementName = 'ch_img2';
        if (!empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] != '') {
            preg_match('/(^.*)\.([^\.]*)$/', $_FILES[$fileElementName]['name'], $matches);
            $ext = "";
            if (count($matches) > 0) {
                $ext = strtolower($matches[2]);
                $original_name = $matches[1];
            } else
                $original_name = 'image';


            $imagename = createImageName($original_name);

            if (test_file($this->upload_landing_page_image2 . $imagename . '-thumb.' . $ext)) {
                for ($i = 0; test_file($this->upload_landing_page_image2 . $imagename . '-' . $i . '-thumb.' . $ext); $i++) {
                    
                }

                $new_imagename = $imagename . '-' . $i;
            } else {
                $new_imagename = $imagename;
            }

            $this->imagename = $new_imagename;

            $this->upload_image = $this->upload_landing_page_image2 . $new_imagename . '.' . $ext;
            #echo $this->upload_path.' === ';  echo $this->upload_image ;

            @move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $this->upload_image);



            $config['source_image'] = $this->upload_image;
            $config['thumb_marker'] = '-thumb';
            $config['crop'] = true;
            $config['crop_from'] = 'middle';
            $config['width'] = 330;
            $config['height'] = 220;
            $config['small_image_resize'] = 'bigger';

            resize_exact($config);

            $config = array();
            $config['source_image'] = $this->upload_image;
            $config['thumb_marker'] = '-main';
            $config['crop'] = true;
            $config['width'] = 350;
            $config['height'] = 250;
            $config1['crop_from'] = 'middle';
            $config['small_image_resize'] = 'no_resize';
            resize_exact($config);
             /******************04-12-2014****************************/
//            $config = array();
//            $config['source_image'] = $this->upload_image;
//            $config['thumb_marker'] = '-large';
//            $config['crop'] = false;
//            $config['width'] = 400;
//            $config['height'] = 400;
//            $config1['crop_from'] = 'middle';
//            $config['small_image_resize'] = 'no_resize';
//            resize_exact($config);
            /*********************************************/
            
            unset($config);

            $this->s_picture_path = $new_imagename . '.' . $ext;

            @unlink($this->upload_image); //Unlink the original image........
            //@unlink($this->upload_path.getThumbName($prev_img,'thumb')); //Unlink the prevoius image........

            return $this->s_picture_path;
        } else {
            return $prev_img; // Unchanged previous image
        }
    }
     public function _upload_landing_page_image_three($prev_img = '', $filefieldname) {

        parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by non logged user and admin
        #pr($_FILES);
        $fileElementName = 'ch_img3';
        if (!empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] != '') {
            preg_match('/(^.*)\.([^\.]*)$/', $_FILES[$fileElementName]['name'], $matches);
            $ext = "";
            if (count($matches) > 0) {
                $ext = strtolower($matches[2]);
                $original_name = $matches[1];
            } else
                $original_name = 'image';


            $imagename = createImageName($original_name);

            if (test_file($this->upload_landing_page_image3 . $imagename . '-thumb.' . $ext)) {
                for ($i = 0; test_file($this->upload_landing_page_image3 . $imagename . '-' . $i . '-thumb.' . $ext); $i++) {
                    
                }

                $new_imagename = $imagename . '-' . $i;
            } else {
                $new_imagename = $imagename;
            }

            $this->imagename = $new_imagename;

            $this->upload_image = $this->upload_landing_page_image3 . $new_imagename . '.' . $ext;
            #echo $this->upload_path.' === ';  echo $this->upload_image ;

            @move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $this->upload_image);



            $config['source_image'] = $this->upload_image;
            $config['thumb_marker'] = '-thumb';
            $config['crop'] = true;
            $config['crop_from'] = 'middle';
            $config['width'] = 330;
            $config['height'] = 220;
            $config['small_image_resize'] = 'bigger';

            resize_exact($config);

            $config = array();
            $config['source_image'] = $this->upload_image;
            $config['thumb_marker'] = '-main';
            $config['crop'] = true;
            $config['width'] = 350;
            $config['height'] = 250;
            $config1['crop_from'] = 'middle';
            $config['small_image_resize'] = 'no_resize';
            resize_exact($config);
             /******************04-12-2014****************************/
//            $config = array();
//            $config['source_image'] = $this->upload_image;
//            $config['thumb_marker'] = '-large';
//            $config['crop'] = false;
//            $config['width'] = 400;
//            $config['height'] = 400;
//            $config1['crop_from'] = 'middle';
//            $config['small_image_resize'] = 'no_resize';
//            resize_exact($config);
            /*********************************************/
            
            unset($config);

            $this->s_picture_path = $new_imagename . '.' . $ext;

            @unlink($this->upload_image); //Unlink the original image........
            //@unlink($this->upload_path.getThumbName($prev_img,'thumb')); //Unlink the prevoius image........

            return $this->s_picture_path;
        } else {
            return $prev_img; // Unchanged previous image
        }
    }
     public function _upload_landing_page_image_four($prev_img = '', $filefieldname) {

        parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by non logged user and admin
        #pr($_FILES);
        $fileElementName = 'ch_img4';
        if (!empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] != '') {
            preg_match('/(^.*)\.([^\.]*)$/', $_FILES[$fileElementName]['name'], $matches);
            $ext = "";
            if (count($matches) > 0) {
                $ext = strtolower($matches[2]);
                $original_name = $matches[1];
            } else
                $original_name = 'image';


            $imagename = createImageName($original_name);

            if (test_file($this->upload_landing_page_image4 . $imagename . '-thumb.' . $ext)) {
                for ($i = 0; test_file($this->upload_landing_page_image4 . $imagename . '-' . $i . '-thumb.' . $ext); $i++) {
                    
                }

                $new_imagename = $imagename . '-' . $i;
            } else {
                $new_imagename = $imagename;
            }

            $this->imagename = $new_imagename;

            $this->upload_image = $this->upload_landing_page_image4 . $new_imagename . '.' . $ext;
            #echo $this->upload_path.' === ';  echo $this->upload_image ;

            @move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $this->upload_image);



            $config['source_image'] = $this->upload_image;
            $config['thumb_marker'] = '-thumb';
            $config['crop'] = true;
            $config['crop_from'] = 'middle';
            $config['width'] = 330;
            $config['height'] = 220;
            $config['small_image_resize'] = 'bigger';

            resize_exact($config);

            $config = array();
            $config['source_image'] = $this->upload_image;
            $config['thumb_marker'] = '-main';
            $config['crop'] = true;
            $config['width'] = 350;
            $config['height'] = 250;
            $config1['crop_from'] = 'middle';
            $config['small_image_resize'] = 'no_resize';
            resize_exact($config);
             /******************04-12-2014****************************/
//            $config = array();
//            $config['source_image'] = $this->upload_image;
//            $config['thumb_marker'] = '-large';
//            $config['crop'] = false;
//            $config['width'] = 400;
//            $config['height'] = 400;
//            $config1['crop_from'] = 'middle';
//            $config['small_image_resize'] = 'no_resize';
//            resize_exact($config);
            /*********************************************/
            
            unset($config);

            $this->s_picture_path = $new_imagename . '.' . $ext;

            @unlink($this->upload_image); //Unlink the original image........
            //@unlink($this->upload_path.getThumbName($prev_img,'thumb')); //Unlink the prevoius image........

            return $this->s_picture_path;
        } else {
            return $prev_img; // Unchanged previous image
        }
    }
    
    function edit_service(){
        $ser_id = $this->input->post('ser_id');
        $edit_ch_open = $this->input->post('edit_ch_open');
        $edit_ch_close = $this->input->post('edit_ch_close');
        $edit_des = $this->input->post('edit_des');
        $ch_id = $this->input->post('ch_id');
        $data = array(
               'c_des' => $edit_des,
               'c_start_time' => $edit_ch_open,
               'c_end_time' => $edit_ch_close,
               'c_update' => get_db_datetime()
                
            );
//pr($data,1);
$this->db->where('id', $ser_id);
$this->db->update('cg_church_schedul', $data); 
 echo json_encode( array('success'=>'true'));
    }
    function del_service(){
        $id = $this->input->post('id');
        $this->db->delete('cg_church_schedul', array('id' => $id)); 
        echo json_encode( array('success'=>'true'));
    }
    function download_sample_csv(){
        $this->load->helper('download');
       $path = BASEPATH . '../uploads/sample_csv/member_mailid.csv';
       
        $data = file_get_contents($path); // Read the file's contents
$name = 'member_mailid.csv';

force_download($name, $data); 
echo json_encode( array('success'=>'true'));
    }
	
	function church_add_member_form() {
	 try
        {
			if($_POST)
			{
				if(!empty($_POST['val_arr'])) {
				foreach( $_POST['val_arr'] As $key => $val) {
					$invite_val[] = explode('_',$val);
				
				}
				//echo '<pre>';
				
				$invite_val_count = count($invite_val);
				for($i=0; $i<$invite_val_count; $i++){
                                    
                                    
                                    /**********if already invited member**********************************/
                                    $query1 = $this->db->get_where('cg_church_member_invitation', array('email' => $invite_val[$i][1] ,'church_id'=>$_SESSION['logged_church_id'] ));
                                    $result = $query1->result();
                                     $query2 = $this->db->get_where('cg_church', array('ch_admin_id' => get_user_id_byemail($invite_val[$i][1]) , 'id' =>$_SESSION['logged_church_id']));
                                      $result1 = $query2->result();
//pr($result,1);
                                     
                                    if(count($result) > 0 || count($result1) > 0){
                                         echo json_encode(array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>''.$invite_val[$i][1].' already exist'));
                                         continue;
                                        
                                    }
                                    /********************************************/
					$invited_member = array(
					'name' => $invite_val[$i][0],
					'email' => $invite_val[$i][1],
					'church_id' => $_SESSION['logged_church_id'],
					'invitation_sent_date' => get_db_datetime()
				);
				$this->db->insert('cg_church_member_invitation', $invited_member);
				$invte_id = $this->db->insert_id(); 
                                       // echo $invite_val[$i][1].'======';
				/********************************************************************/
                                $query = $this->db->get_where('cg_users', array('s_email' => $invite_val[$i][1] ,'i_status' => 1));
                                                $result = $query->result();
                             //  pr($result);
                                if($result[0]->id == null){
                                    $this->load->model('mail_contents_model');
				$mail_info = $this->mail_contents_model->get_by_name("church_community_invitation_mail");

				$subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
				$body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
				$body = sprintf3( $body, array('churchurl'=> base_url().'church_registration_by_email/'.$_SESSION['logged_church_id'].'/1/'.$invte_id) );
						   
				$to      = $invite_val[$i][1];
				$subject = $subject;
				$message = $body;
				$headers = 'From: admin@cogtime.com' . "\r\n" .
					'Reply-To: admin@cogtime.com' . "\r\n" .
					'X-Mailer: PHP/' . phpversion() . "\r\n";
				$headers  .= 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				//echo $body;exit;
				mail($to, $subject, $message, $headers);
                                    
                                    
                                    
                                }else{
                                    $this->load->model('mail_contents_model');
				$mail_info = $this->mail_contents_model->get_by_name("church_community_invitation_mail");

				$subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
				$body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
				$body = sprintf3( $body, array('churchurl'=> base_url().'already_user/'.$_SESSION['logged_church_id'].'/1/'.$result[0]->id.'/'.$invte_id) );
						   
				$to      = $invite_val[$i][1];
				$subject = $subject;
				$message = $body;
				$headers = 'From: admin@cogtime.com' . "\r\n" .
					'Reply-To: admin@cogtime.com' . "\r\n" .
					'X-Mailer: PHP/' . phpversion() . "\r\n";
				$headers  .= 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				//echo $body;exit;
				mail($to, $subject, $message, $headers);
                                    
                                }
				
                                /*********************************************************/
				//echo json_encode(array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'Mail sent successfully'));
				}
			  }
			}
			else
			{
				$user_id = intval(decrypt($this->session->userdata('user_id')));
				get_all_church_session($_SESSION['logged_church_id']);
				//parent::check_is_church_admin($user_id,$c_id);
				$posted=array();
				$this->data["posted"]=$posted;/*don't change*/    
				$data = $this->data;      
				//               $this->data["MAIN_MENU_SELECTED"] = 1;
				parent::check_church_id_empty(TRUE, $_SESSION['logged_church_id'], array('1'));
				parent::_set_title('::: COGTIME Xtian network :::');
				parent::_set_meta_desc('');
				parent::_set_meta_keywords('');

				//$data['church_arr'] =     $this->church_new_model->get_church_info($c_id);
				$VIEW = "logged/church/church_add_member_form.phtml";
				parent::_render($data, $VIEW);
			}
        }
        catch(Exception $err_obj)
        {
           
        } 
	}
    /********************************************************************/
}   // end of controller...

