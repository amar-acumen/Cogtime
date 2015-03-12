<?php

/* * *******
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
include(APPPATH . 'controllers/base_controller.php');

class Advertisement_request extends Base_controller {

//    private $pagination_per_page = 10;
//    private $home_pagination_per_page = 8;
//    private $commits_pagination_per_page = 5;
//    private $all_commits_pagination_per_page = 10;

    public function __construct() {

        try {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...

            $this->load->model('help_center_model');
            // $this->load->model('landing_page_cms_model');
            $this->upload_path = BASEPATH.'../uploads/advertisements/';
             $this->upload_path1 = BASEPATH.'../uploads/media_center_advertisement/';
            $this->i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function index() {
       // die('d');
        try {

            $posted = array();
            $this->data["posted"] = $posted; /* don't change */
            $data = $this->data;
           // $this->data["MAIN_MENU_SELECTED"] = 20;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');


            parent::_add_js_arr(array(/*'js/ddsmoothmenu.js',
                'js/switch.js', 'js/animate-collapse.js',
                'js/lightbox.js',
                'js/jquery-ui-1.8.2.custom.min.js',
                'js/stepcarousel.js',
                'js/jquery/ui/jquery.ui.core.js',
                'js/jquery.ui.datepicker.js',*/
                'js/jquery-ui-timepicker-addon.js',
                'js/jquery-ui.triggeredAutocomplete.js',
                'js/jquery-ui-sliderAccess.js',
//                'js/tab.js',
                'js/production/prayer_wall.js',
                'js/production/tweets.js',
                'js/autocomplete/jquery.autocomplete.js'
            ));

            parent::_add_css_arr(array('css/jquery-ui-1.8.13.custom.css',
                'css/jquery-ui-1.8.2.custom.css',
                'css/jquery.autocomplete.css'));

            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
                         
                        $date['cat'] = $this->help_center_model->get_help_center_category(); 
                       
                       //pr($date['categories'],1);
          // pr($date['help1'],1);
//
//            
            $VIEW = "logged/user_advertisement_request/advertisement_request.phtml";
            parent::_render($data,$VIEW);
        } catch (Exception $err_obj) {
            
        }
    }

 function get_cost(){
    $start_date = strtotime($this->input->post('start_date'));
    $end_date = strtotime($this->input->post('end_date'));
    $page_log = trim($this->input->post('page_log'));
     $datediff = $end_date - $start_date;
     $day = floor($datediff/(60*60*24));
      $month = floor($day/30);
     $month_mod = ($day % 30);
      if($month_mod != 0){
      $month = $month+1;    
      }
       if(!empty($page_log)){
      $query = $this->db->get_where('cg_advertisement_cost', array('id' => 1));
     // echo $page_log;
      //pr($query->result());
foreach ($query->result() as $row)
{
    $cost = $row->$page_log;
 
    $total_cost = $month*$cost;
    echo $total_cost;
}
      
       }
       
      
    
    
 }
 function get_media_cost(){
      $start_date = strtotime($this->input->post('start_date'));
    $end_date = strtotime($this->input->post('end_date'));
    $page_log = trim($this->input->post('page_log'));
     $datediff = $end_date - $start_date;
     $day = floor($datediff/(60*60*24));
      $month = floor($day/30);
     $month_mod = ($day % 30);
      if($month_mod != 0){
      $month = $month+1;    
      }
       if(!empty($page_log)){
      $query = $this->db->get_where('cg_advertisement_cost', array('id' => 1));
     // echo $page_log;
      //pr($query->result());
foreach ($query->result() as $row)
{
    $cost = $row->$page_log;
 
    $total_cost = $month*$cost;
    echo $total_cost;
}
      
       }
       
    // die();
     
 }
 function add_cogtime_adv(){
     
     $user = get_username_by_id(intval(decrypt($this->session->userdata('user_id'))));
     $email = get_useremail_by_id(intval(decrypt($this->session->userdata('user_id'))));
     $star_date = $this->input->post('create_time');
      $end_date = $this->input->post('create_time1');
      $p_loc = $this->input->post('cost_cal');
      $url = $this->input->post('addurl');
      $image = $this->input->post('addimage');
      $add_cost = $this->input->post('cogtime_adv_cost');
      if($p_loc == 'home_page_cost'){
          
          $loc = 'my-wall';
      }
      if($p_loc == 'socail_hub_chitter_tweets_page_cost'){
          
          $loc = 'tweets';
      }
      if($p_loc == 'socail_hub_blogs_page_cost'){
          $loc = 'blogs';
      }
       if($p_loc == 'socail_hub_rings_page_cost'){
          $loc = 'ring';
      }
       if($p_loc == 'socail_hub_events_page_cost'){
          $loc = 'events';
      }
      if($p_loc == 'socail_hub_chat_rooms_page_cost'){
          $loc = 'chat-room';
      }
       if($p_loc == 'build_kingdom_prayer_partners_zone_page_cost'){
          $loc = 'prayer-partner';
      }
      if($p_loc == 'build_kingdom_bible_quiz_page_cost'){
          $loc = 'quiz';
      }
       if($p_loc == 'build_kingdom_find_a_church_page_cost'){
          $loc = 'Church';
      }
       if($p_loc == 'my_profile_page_cost'){
          $loc = 'my-profile';
      }
       if($p_loc == 'my_friends_zone_page_costt'){
          $loc = 'my-friends';
      }
      if($p_loc == 'my_netpals_zone_page_cost'){
          $loc = 'my-net-pals';
      }
      if($p_loc == 'my_photos_page_cost'){
          $loc = 'my-photos';
      }
      if($p_loc == 'my_audios_page_cost'){
          $loc = 'my-audios';
      }
      if($p_loc == 'my_videos_page_cost'){
          $loc = 'my-videos';
      }
      if($p_loc == 'my_messages_page_cost'){
          $loc = 'my-msg-inbox';
      }
        if($p_loc == 'my_privacy_settings_page_cost'){
          $loc = 'privacy-settings';
      }
        if($p_loc == 'my_alert_settings_page_cost'){
          $loc = 'user-alert-settings';
      }
      
       if($p_loc == 'my_email_notification_settings_page_cost'){
          $loc = 'user-email-settings';
      }
      
      if($p_loc == 'organizer_page_cost'){
          $loc = 'organizer-day-view';
      }
      
      if($p_loc == 'change_password_page_cost'){
          $loc = 'change-password';
      }
       if($p_loc == 'deactivate_account_page_cost'){
          $loc = 'deactivate-account';
      }
       if($p_loc == 'public_profile_cost'){
          $loc = 'public-profile';
      }
    
             
/*****************paypal********************************************/

     
 //require_once(APPPATH."views/paypal/library.php"); // include the library file
define('EMAIL_ADD', 'arif.zisu@gmail.com'); // define any notification email
define('PAYPAL_EMAIL_ADD', 'pay@cogtime.com'); // facilitator email which will receive payments change this email to a live paypal account id when the site goes live
require_once(APPPATH."views/paypal/paypal_class.php");
$p 				= new paypal_class(); // paypal class
$p->admin_mail 	= EMAIL_ADD; // set notification email
//$action 		= 'process';
#print_r($_GET);
//$action = $this->input->get_post('action');
$action = isset($_REQUEST["action"])?$_REQUEST["action"]:$this->input->get_post('action');

switch($action){
	case "process": // case process insert the form data in DB and process to the paypal
		//mysql_query("INSERT INTO `purchases` (`invoice`, `product_id`, `product_name`, `product_quantity`, `product_amount`, `payer_fname`, `payer_lname`, `payer_address`, `payer_city`, `payer_state`, `payer_zip`, `payer_country`, `payer_email`, `payment_status`, `posted_date`) VALUES ('".$_POST["invoice"]."', '".$_POST["product_id"]."', '".$_POST["product_name"]."', '".$_POST["product_quantity"]."', '".$_POST["product_amount"]."', '".$_POST["payer_fname"]."', '".$_POST["payer_lname"]."', '".$_POST["payer_address"]."', '".$_POST["payer_city"]."', '".$_POST["payer_state"]."', '".$_POST["payer_zip"]."', '".$_POST["payer_country"]."', '".$_POST["payer_email"]."', 'pending', NOW())");
		
                  $info['i_user_id'] =  intval(decrypt($this->session->userdata('user_id')));
                    $info["s_title"]= $loc;
                    $info["dt_start_date"]= $star_date;
                    $info["dt_end_date"]= $end_date;
                    $info["s_url"]= $url;
                    $info['p_loc'] =$loc;
                    $info["s_image"] = $this->_upload_profile_image(trim($image));
                    $info["i_status"] = 2;
                    $info["a_cost"] = $add_cost;
                    $info["user_email"] = $email;
                    $info["s_description"] = ''; 
                    $info['dt_created_on'] = get_db_datetime();
                    $info['dt_updated_on'] = get_db_datetime();
                    $info["a_pay_status"] = 0;
                    $info['u_name'] = $user;
                    $data = $info;
                    $this->db->insert('cg_advertisement', $data);
                    $rsutt = $this->db->insert_id();
                      
            $_SESSION['product_id'] =   $rsutt;
                $this_script = base_url().'advertisement_request';
            //$this_script = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
		$p->add_field('business', PAYPAL_EMAIL_ADD); // Call the facilitator eaccount
		$p->add_field('cmd', '_cart'); // cmd should be _cart for cart checkout
		$p->add_field('upload', '1');
		$p->add_field('return', base_url().'logged/advertisement_request/add_cogtime_adv?action=success'); // return URL after the transaction got over
		$p->add_field('cancel_return', base_url().'logged/advertisement_request/add_cogtime_adv?action=cancel'); // cancel URL if the trasaction was cancelled during half of the transaction
		$p->add_field('notify_url', base_url().'logged/advertisement_request/add_cogtime_adv?action=ipn'); // Notify URL which received IPN (Instant Payment Notification)
//		$p->add_field('return', $this_script.'?action=success'); // return URL after the transaction got over
//		$p->add_field('cancel_return', $this_script.'?action=cancel'); // cancel URL if the trasaction was cancelled during half of the transaction
//		$p->add_field('notify_url', $this_script.'?action=ipn'); 
                $p->add_field('currency_code', 'GBP');
		$p->add_field('invoice', date("His").rand(1234, 9632));
		$p->add_field('item_name_1', $loc);
		$p->add_field('item_number_1', $rsutt);
		$p->add_field('quantity_1', 1);
		$p->add_field('amount_1', $add_cost);
		$p->add_field('first_name',$user);
		$p->add_field('last_name', '');
                $p->add_field('custom', $rsutt);
		$p->add_field('address1', '');
		$p->add_field('city', '');
		$p->add_field('state', '');
		$p->add_field('country', '');
		$p->add_field('zip', '');
		$p->add_field('email', $email);
		$p->submit_paypal_post(); // POST it to paypal
		$p->dump_fields(); // Show the posted values for a reference, comment this line before app goes live
       
                break;
	
	case "success": // success case to show the user payment got success
        $data = array(
                            'a_pay_status' => 1,

                                      );

                            $this->db->where('id', $_SESSION['product_id']);
                            $this->db->update('cg_advertisement', $data); 
		echo '<title>Payment Done Successfully</title>';
		echo '<style>.as_wrapper{
 font-family:Arial;
 color:#096aa7;
 font-size:14px;
 padding:20px;
 border:2px solid #62c3bc;
 border-top:0;
 width:600px;
 margin:0 auto;
 text-align:center;
}
.click-button{ 
 background: #013d62;
    border: 0 none;
    color: #fff;
    cursor: pointer;
 text-align:center;
    font-size: 18px;
    height: 32px;
 line-height:32px;
    margin:0 auto;
 display:block;
    width: 97px;
 text-decoration:none;
 }
 
.click-button:hover{ opacity:0.8;}

</style>

';		echo "<div style='background-color:#013d62; padding:20px 0 20px 0; width:644px; margin:140px auto 0 auto;'><img src='".base_url()."images/logo.png' style='margin:0 auto 0 auto; display:block;' width='230' height='57' alt=''/></div>";
                echo '<div class="as_wrapper">';
		echo "<h1>Payment Transaction Done Successfully</h1>";
		echo "<h2 style='font-weight:normal;'>Please click below link for more "."<br>"." advertisement request</h2>";
                echo "<a class='click-button' href='".base_url()."advertisement_request'>click here</a>";
		echo '</div>';
	break;
	
	case "cancel": // case cancel to show user the transaction was cancelled
            
		echo '<title>Transaction Cancelled Successfully</title>';
		echo '<style>.as_wrapper{
 font-family:Arial;
 color:#096aa7;
 font-size:14px;
 padding:20px;
 border:2px solid #62c3bc;
 border-top:0;
 width:600px;
 margin:0 auto;
 text-align:center;
}
.click-button{ 
 background: #013d62;
    border: 0 none;
    color: #fff;
    cursor: pointer;
 text-align:center;
    font-size: 18px;
    height: 32px;
 line-height:32px;
    margin:0 auto;
 display:block;
    width: 97px;
 text-decoration:none;
 }
 
.click-button:hover{ opacity:0.8;}
</style>

';		echo "<div style='background-color:#013d62; padding:20px 0 20px 0; width:644px; margin:140px auto 0 auto;'><img src='".base_url()."images/logo.png' style='margin:0 auto 0 auto; display:block;' width='230' height='57' alt=''/></div>";
                echo '<div class="as_wrapper">';
		echo "<h1>Transaction Cancelled Successfully</h1>";
		echo "<h2 style='font-weight:normal;'>Please click below link for more "."<br>"." advertisement request</h2>";
                echo "<a class='click-button' href='".base_url()."advertisement_request'>click here</a>";
		echo '</div>';
	break;
	
	case "ipn": // IPN case to receive payment information. this case will not displayed in browser. This is server to server communication. PayPal will send the transactions each and every details to this case in secured POST menthod by server to server. 
            $p->send_report($subject);   
            $trasaction_id  = $_POST["txn_id"];
		$payment_status = strtolower($_POST["payment_status"]);
		$invoice		= $_POST["invoice"];
		$log_array		= print_r($_POST, TRUE);
		$log_query		= "SELECT * FROM `paypal_log` WHERE `txn_id` = '$trasaction_id'";
		$log_check 		= mysql_query($log_query);
		if(mysql_num_rows($log_check) <= 0){
			mysql_query("INSERT INTO `paypal_log` (`txn_id`, `log`, `posted_date`) VALUES ('$trasaction_id', '$log_array', NOW())");
		}else{
			mysql_query("UPDATE `paypal_log` SET `log` = '$log_array' WHERE `txn_id` = '$trasaction_id'");
		} // Save and update the logs array
		$paypal_log_fetch 	= mysql_fetch_array(mysql_query($log_query));
		$paypal_log_id		= $paypal_log_fetch["id"];
		if ($p->validate_ipn()){ // validate the IPN, do the others stuffs here as per your app logic
			mysql_query("UPDATE `purchases` SET `trasaction_id` = '$trasaction_id ', `log_id` = '$paypal_log_id', `payment_status` = '$payment_status' WHERE `invoice` = '$invoice'");
			$subject = 'Instant Payment Notification - Recieved Payment';
			$p->send_report($subject); // Send the notification about the transaction
		}else{
			$subject = 'Instant Payment Notification - Payment Fail';
			$p->send_report($subject); // failed notification
		}
	break;

 }
/**********************************************************/
//$re_page = base_url() ."advertisement_request";
//				header("location:".$re_page);
//				exit;
            
 }
 
 

 public function _upload_profile_image($prev_img = '')
    {
      
	  // pr($_FILES);
	    $fileElementName = 'addimage';	 
        if(!empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] != '') 
		{
				preg_match('/(^.*)\.([^\.]*)$/', $_FILES[$fileElementName]['name'], $matches);
				$ext = "";
				if(count($matches)>0) {
					$ext = strtolower($matches[2]);
					$original_name = $matches[1];
				}
				else
					$original_name = 'image';

			
					 $imagename = createImageName( $original_name ); 

					if(test_file($this->upload_path.$imagename.'-thumb.'.$ext)) {
						for( $i=0; test_file($this->upload_path.$imagename.'-'.$i.'-thumb.'.$ext); $i++ ) {
						}

						$new_imagename = $imagename.'-'.$i;
					}
					else {
						$new_imagename = $imagename;
					}

					$this->imagename = $new_imagename;

					$this->upload_image = $this->upload_path.$new_imagename.'.'.$ext;
					//echo $this->upload_path; exit;
					
					@move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $this->upload_image);
					
					
					$config = array();

					$config['source_image'] = $this->upload_image;
					$config['thumb_marker'] = '-thumb';
					$config['crop'] = false;
					$config['width'] = 60;
					$config['height'] = 60;
					$config1['crop_from'] = 'middle';
                    #$config['within_rectangle'] = true;
                    $config['small_image_resize'] = 'no_resize';
					resize_exact($config);
                    
					unset($config);
					
					$config = array();

					$config['source_image'] = $this->upload_image;
					$config['thumb_marker'] = '-main';
					$config['crop'] = false;
					$config['width'] = 200;
					$config['height'] = 200;
                    $config1['crop_from'] = 'middle';
                    #$config['within_rectangle'] = true;
                    $config['small_image_resize'] = 'inside';
					resize_exact($config);
					
					$config = array();

                
					
					$this->s_picture_path = $new_imagename.'.'.$ext;
					//echo $this->upload_image; 
					
					@unlink($this->upload_image); //Unlink the original image........
					//@unlink($this->upload_path.getThumbName($prev_img,'thumb')); //Unlink the prevoius image........
					//exit;
					return $this->s_picture_path;

				
			
					
				}
        else
        {
            return $prev_img; // Unchaged previous image
        }
        
        
    }
    
    /******************7-10-2014*************************************************/
      function add_media_center_adv(){
          //die('ok');
          $user = get_username_by_id(intval(decrypt($this->session->userdata('user_id'))));
     $email = get_useremail_by_id(intval(decrypt($this->session->userdata('user_id')))); 
      $star_date = $this->input->post('time');
      $end_date = $this->input->post('time2');
      $p_loc = $this->input->post('media_cost');
      $url = $this->input->post('mediaaddurl');
      $image = $this->input->post('mideaaddimage');
      $add_cost = $this->input->post('media_cost_value');
      if($p_loc == 'media_center_banner_page_cost'){
          $loc = 'media-banner';
      }
      if($p_loc == 'read_page_cost'){
          $loc = 'read';
      }
      if($p_loc == 'watch_page_cost'){
          $loc = 'watch';
      }
      if($p_loc == 'listen_page_cost'){
          $loc = 'listen';
      }
      if($p_loc == 'media_center_category_page_cost'){
          $loc = 'category-page';
      }
        if($p_loc == 'media_center_search_page_cost'){
          $loc = 'Search-page';
      }
     
            /*****************paypal********************************************/
 
 require_once(APPPATH."views/paypal/library.php"); // include the library file
define('EMAIL_ADD', 'arif.zisu@gmail.com'); // define any notification email
define('PAYPAL_EMAIL_ADD', 'pay@cogtime.com'); // facilitator email which will receive payments change this email to a live paypal account id when the site goes live
require_once(APPPATH."views/paypal/paypal_class.php");
$p = new paypal_class(); // paypal class
$p->admin_mail 	= EMAIL_ADD; // set notification email
//$action 		= 'process';
$action = $this->input->get_post('action');
switch($action){
	case "process": // case process insert the form data in DB and process to the paypal
		//mysql_query("INSERT INTO `purchases` (`invoice`, `product_id`, `product_name`, `product_quantity`, `product_amount`, `payer_fname`, `payer_lname`, `payer_address`, `payer_city`, `payer_state`, `payer_zip`, `payer_country`, `payer_email`, `payment_status`, `posted_date`) VALUES ('".$_POST["invoice"]."', '".$_POST["product_id"]."', '".$_POST["product_name"]."', '".$_POST["product_quantity"]."', '".$_POST["product_amount"]."', '".$_POST["payer_fname"]."', '".$_POST["payer_lname"]."', '".$_POST["payer_address"]."', '".$_POST["payer_city"]."', '".$_POST["payer_state"]."', '".$_POST["payer_zip"]."', '".$_POST["payer_country"]."', '".$_POST["payer_email"]."', 'pending', NOW())");
        /******************data base adition*******************************************/
                     if($_POST && $p_loc != 'media_center_banner_page_cost')
            {
          //die('s');
          //$posted=array();
          $info['i_user_id'] =  intval(decrypt($this->session->userdata('user_id')));
          $info["s_title"]= $loc;
        $info["dt_start_date"]= $star_date;
        $info["dt_end_date"]= $end_date;
        $info["s_url"]= $url;
        $info['p_loc'] =$loc;
        $info["s_image"] = $this->_upload_profile_image1(trim($image));
        $info["i_status"] = 2;
        $info["a_cost"] = $add_cost;
        $info["user_email"] = $email;
        $info["s_description"] = ''; 
        $info['dt_created_on'] = get_db_datetime();
        $info['dt_updated_on'] = get_db_datetime();
        $info["a_pay_status"] = 0;
        $info['u_name'] = $user;
       

//        $this->db->insert('cg_media_center_advertisement', $data); 
//        $re_page = base_url() ."advertisement_request";
//        header("location:".$re_page);
//        exit;
            }
            if($_POST && $p_loc == 'media_center_banner_page_cost')
            {
         // die('s');
          //$posted=array();
          $info['i_user_id'] =  intval(decrypt($this->session->userdata('user_id')));
          $info["s_title"]= $loc;
        $info["dt_start_date"]= $star_date;
        $info["dt_end_date"]= $end_date;
        $info["s_url"]= $url;
        $info['p_loc'] =$loc;
        $info["s_image"] = $this->_upload_profile_image_media_banner(trim($image));
        $info["i_status"] = 2;
        $info["a_cost"] = $add_cost;
        $info["user_email"] = $email;
        $info["s_description"] = ''; 
        $info['dt_created_on'] = get_db_datetime();
        $info['dt_updated_on'] = get_db_datetime();
        $info["a_pay_status"] = 0;
        $info['u_name'] = $user;
       
            }
             $data = $info;
             //pr($data,1);
            $this->db->insert('cg_media_center_advertisement', $data); 
              $result = $this->db->insert_id();
               $_SESSION['product_id_media'] =   $result;
            $this_script = base_url().'advertisement_request';
            //$this_script = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
		$p->add_field('business', PAYPAL_EMAIL_ADD); // Call the facilitator eaccount
		$p->add_field('cmd', '_cart'); // cmd should be _cart for cart checkout
		$p->add_field('upload', '1');
		$p->add_field('return', base_url().'logged/advertisement_request/add_media_center_adv?action=success'); // return URL after the transaction got over
		$p->add_field('cancel_return', base_url().'logged/advertisement_request/add_media_center_adv?action=cancel'); // cancel URL if the trasaction was cancelled during half of the transaction
		$p->add_field('notify_url', base_url().'logged/advertisement_request/add_media_center_adv?action=ipn'); // Notify URL which received IPN (Instant Payment Notification)
//		$p->add_field('return', $this_script.'?action=success'); // return URL after the transaction got over
//		$p->add_field('cancel_return', $this_script.'?action=cancel'); // cancel URL if the trasaction was cancelled during half of the transaction
//		$p->add_field('notify_url', $this_script.'?action=ipn'); 
                $p->add_field('currency_code', 'GBP');
		$p->add_field('invoice', date("His").rand(1234, 9632));
		$p->add_field('item_name_1', $loc);
		$p->add_field('item_number_1', 1);
		$p->add_field('quantity_1', 1);
		$p->add_field('amount_1', $add_cost);
		$p->add_field('first_name',$user);
		$p->add_field('last_name', '');
		$p->add_field('address1', '');
		$p->add_field('city', '');
		$p->add_field('state', '');
		$p->add_field('country', '');
		$p->add_field('zip', '');
		$p->add_field('email', $email);
		$p->submit_paypal_post(); // POST it to paypal
		$p->dump_fields(); // Show the posted values for a reference, comment this line before app goes live
       
                break;
	
	case "success": // success case to show the user payment got success
         $data = array(
                            'a_pay_status' => 1,

                                      );

                            $this->db->where('id', $_SESSION['product_id_media']);
                            $this->db->update('cg_media_center_advertisement', $data); 
		echo '<title>Payment Done Successfully</title>';
		echo '<style>.as_wrapper{
 font-family:Arial;
 color:#096aa7;
 font-size:14px;
 padding:20px;
 border:2px solid #62c3bc;
 border-top:0;
 width:600px;
 margin:0 auto;
 text-align:center;
}
.click-button{ 
 background: #013d62;
    border: 0 none;
    color: #fff;
    cursor: pointer;
 text-align:center;
    font-size: 18px;
    height: 32px;
 line-height:32px;
    margin:0 auto;
 display:block;
    width: 97px;
 text-decoration:none;
 }
 
.click-button:hover{ opacity:0.8;}
</style>

';		echo "<div style='background-color:#013d62; padding:20px 0 20px 0; width:644px; margin:140px auto 0 auto;'><img src='".base_url()."images/logo.png' style='margin:0 auto 0 auto; display:block;' width='230' height='57' alt=''/></div>";
                echo '<div class="as_wrapper">';
		echo "<h1>Payment Transaction Done Successfully</h1>";
		echo "<h2 style='font-weight:normal;'>Please click below link for more "."<br>"." advertisement request</h2>";
                echo "<a class='click-button' href='".base_url()."advertisement_request'>click here</a>";
		echo '</div>';
	break;
	
	case "cancel": // case cancel to show user the transaction was cancelled
            
		echo '<title>Transaction Cancelled</title>';
		echo '<style>.as_wrapper{
 font-family:Arial;
 color:#096aa7;
 font-size:14px;
 padding:20px;
 border:2px solid #62c3bc;
 border-top:0;
 width:600px;
 margin:0 auto;
 text-align:center;
}
.click-button{ 
 background: #013d62;
    border: 0 none;
    color: #fff;
    cursor: pointer;
 text-align:center;
    font-size: 18px;
    height: 32px;
 line-height:32px;
    margin:0 auto;
 display:block;
    width: 97px;
 text-decoration:none;
 }
 
.click-button:hover{ opacity:0.8;}
</style>

';		echo "<div style='background-color:#013d62; padding:20px 0 20px 0; width:644px; margin:140px auto 0 auto;'><img src='".base_url()."images/logo.png' style='margin:0 auto 0 auto; display:block;' width='230' height='57' alt=''/></div>";
                echo '<div class="as_wrapper">';
		echo "<h1>Transaction Cancelled Successfully</h1>";
		echo "<h2 style='font-weight:normal;'>Please click below link for more "."<br>"." advertisement request</h2>";
                echo "<a class='click-button' href='".base_url()."advertisement_request'>click here</a>";
		echo '</div>';
	break;
	
	case "ipn": // IPN case to receive payment information. this case will not displayed in browser. This is server to server communication. PayPal will send the transactions each and every details to this case in secured POST menthod by server to server. 
//                 $data = array(
//                            'a_pay_status' => 1,
//
//                                      );
//
//                            $this->db->where('id', 31);
//                            $this->db->update('cg_advertisement', $data); 
            
            $trasaction_id  = $_POST["txn_id"];
		$payment_status = strtolower($_POST["payment_status"]);
		$invoice		= $_POST["invoice"];
		$log_array		= print_r($_POST, TRUE);
		//$log_query		= "SELECT * FROM `paypal_log` WHERE `txn_id` = '$trasaction_id'";
		//$log_check 		= mysql_query($log_query);
                 
                            
		
                
//		if ('success' == strtolower($_POST["payment_status"])){ // validate the IPN, do the others stuffs here as per your app logic
//                       
//			//mysql_query("UPDATE `purchases` SET `trasaction_id` = '$trasaction_id ', `log_id` = '$paypal_log_id', `payment_status` = '$payment_status' WHERE `invoice` = '$invoice'");
//			//$subject = 'Instant Payment Notification - Recieved Payment';
//			$p->send_report($subject); // Send the notification about the transaction
//		}else{
//			$subject = 'Instant Payment Notification - Payment Fail';
//			$p->send_report($subject); // failed notification
//		}
	break;
}
 
/**********************************************************/
            
            
//        $re_page = base_url() ."advertisement_request";
//        header("location:".$re_page);
//        exit;
     
      }  
      
        public function _upload_profile_image_media_banner($prev_img = '')
    {
      
	  // pr($_FILES);
	    $fileElementName = 'mideaaddimage';	 
        if(!empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] != '') 
		{
				preg_match('/(^.*)\.([^\.]*)$/', $_FILES[$fileElementName]['name'], $matches);
				$ext = "";
				if(count($matches)>0) {
					$ext = strtolower($matches[2]);
					$original_name = $matches[1];
				}
				else
					$original_name = 'image';

			
					 $imagename = createImageName( $original_name ); 

					if(test_file($this->upload_path1.$imagename.'-thumb.'.$ext)) {
						for( $i=0; test_file($this->upload_path1.$imagename.'-'.$i.'-thumb.'.$ext); $i++ ) {
						}

						$new_imagename = $imagename.'-'.$i;
					}
					else {
						$new_imagename = $imagename;
					}

					$this->imagename = $new_imagename;

					$this->upload_image = $this->upload_path1.$new_imagename.'.'.$ext;
					//echo $this->upload_path; exit;
					
					@move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $this->upload_image);
					
					
					$config = array();

					$config['source_image'] = $this->upload_image;
					$config['thumb_marker'] = '-thumb';
					$config['crop'] = false;
					$config['width'] = 60;
					$config['height'] = 60;
					$config1['crop_from'] = 'middle';
                    #$config['within_rectangle'] = true;
                    $config['small_image_resize'] = 'no_resize';
					resize_exact($config);
                    
					unset($config);
					
					$config = array();

					$config['source_image'] = $this->upload_image;
					$config['thumb_marker'] = '-main';
					$config['crop'] = false;
					$config['width'] = 1000;
					$config['height'] = 300;
                    $config1['crop_from'] = 'middle';
                    #$config['within_rectangle'] = true;
                    $config['small_image_resize'] = 'inside';
					resize_exact($config);
					
					$config = array();

                
					
					$this->s_picture_path = $new_imagename.'.'.$ext;
					//echo $this->upload_image; 
					
					@unlink($this->upload_image); //Unlink the original image........
					//@unlink($this->upload_path.getThumbName($prev_img,'thumb')); //Unlink the prevoius image........
					//exit;
					return $this->s_picture_path;

				
			
					
				}
        else
        {
            return $prev_img; // Unchaged previous image
        }
        
        
    }
     public function _upload_profile_image1($prev_img = '')
    {
      
	  // pr($_FILES);
	    $fileElementName = 'mideaaddimage';	 
        if(!empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] != '') 
		{
				preg_match('/(^.*)\.([^\.]*)$/', $_FILES[$fileElementName]['name'], $matches);
				$ext = "";
				if(count($matches)>0) {
					$ext = strtolower($matches[2]);
					$original_name = $matches[1];
				}
				else
					$original_name = 'image';

			
					 $imagename = createImageName( $original_name ); 

					if(test_file($this->upload_path1.$imagename.'-thumb.'.$ext)) {
						for( $i=0; test_file($this->upload_path1.$imagename.'-'.$i.'-thumb.'.$ext); $i++ ) {
						}

						$new_imagename = $imagename.'-'.$i;
					}
					else {
						$new_imagename = $imagename;
					}

					$this->imagename = $new_imagename;

					$this->upload_image = $this->upload_path1.$new_imagename.'.'.$ext;
					//echo $this->upload_path; exit;
					
					@move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $this->upload_image);
					
					
					$config = array();

					$config['source_image'] = $this->upload_image;
					$config['thumb_marker'] = '-thumb';
					$config['crop'] = false;
					$config['width'] = 60;
					$config['height'] = 60;
					$config1['crop_from'] = 'middle';
                    #$config['within_rectangle'] = true;
                    $config['small_image_resize'] = 'no_resize';
					resize_exact($config);
                    
					unset($config);
					
					$config = array();

					$config['source_image'] = $this->upload_image;
					$config['thumb_marker'] = '-main';
					$config['crop'] = false;
					$config['width'] = 300;
					$config['height'] = 300;
                    $config1['crop_from'] = 'middle';
                    #$config['within_rectangle'] = true;
                    $config['small_image_resize'] = 'inside';
					resize_exact($config);
					
					$config = array();

                
					
					$this->s_picture_path = $new_imagename.'.'.$ext;
					//echo $this->upload_image; 
					
					@unlink($this->upload_image); //Unlink the original image........
					//@unlink($this->upload_path.getThumbName($prev_img,'thumb')); //Unlink the prevoius image........
					//exit;
					return $this->s_picture_path;

				
			
					
				}
        else
        {
            return $prev_img; // Unchaged previous image
        }
        
        
    }
    /******************7-10-2014*************************************************/
        
        
}

// end of controller...

