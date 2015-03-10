<?php
/*********
* Author: 
* Date  : 
* Modified By: 
* Modified Date:
* 
* Purpose:
* Controller For 
* 
* 
*/

include(APPPATH.'controllers/base_controller.php');
include_once APPPATH."libraries/gmapAPI/simpleGMapAPI.php";
include_once APPPATH."libraries/gmapAPI/simpleGMapGeocoder.php";


class Build_the_kingdom extends Base_controller
{
    
    private $pagination_per_page =  10 ;
	private $church_pagination_per_page =  10;
	private $quiz_pagination_per_page =  10;
	

    
    public function __construct()
     {
	 	
        try
        {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...
			$this->upload_path = BASEPATH.'../uploads/project_cv_user/';
            $this->load->model('users_model');
			$this->load->model('landing_page_cms_model');
			$this->load->model('projects_model');
			$this->load->model('church_model');
			$this->load->model('bible_model');
			
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
            $this->data["MAIN_MENU_SELECTED"] = 6;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js',
										'js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',
										'js/jquery/ui/jquery.ui.core.js',
										'js/jquery.ui.datepicker.js',*/
									    'js/jquery-ui-timepicker-addon.js',
										'js/jquery-ui-sliderAccess.js',
										'js/production/tweet_utilities.js',
										'js/production/tithe_time.js',
										//'js/tab.js',
										//'js/ModalDialog.js',
                                        ));
                                        
//            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css') );
          
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			
			$where = " WHERE s_keyword like 'buildkindom_givin%'";
            $content_arr = $this->landing_page_cms_model->get_contents($where);
			$data['s_content'] = $content_arr[0]['s_desc'];
			
			# view file...
			
            $VIEW = "logged/build_the_kingdom/giving.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }
	
	public function charity_project_home($post_type = 'suggest-projects') 
    {
        try
        {
                  
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
            $this->data["MAIN_MENU_SELECTED"] = 6;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js',
										'js/animate-collapse.js',
                                        'js/lightbox.js',
										'js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',
										'js/jquery.ui.datepicker.js',*/
										'js/production/tweet_utilities.js',
										'js/production/charity_project.js',
										//'js/tab.js'
                                        ));
                                        
//            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css') );
          
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			
			$data['post_type'] =  ($post_type == 'suggest-projects')?'1':'2';//$this->input->post('post_type');
			$data['pagination_per_page'] = $this->pagination_per_page;
			$data['no_of_result'] = 0;
			$this->session->set_userdata('search_condition','');
			
			
			 switch($post_type) {
				case 'suggest-projects': $data['post_type'] = 1;
					 					 break;
				case 'search-project': $data['post_type'] = 2;
					 				     break;
				case 'suggest-tithe-time-projects': $data['post_type'] = 3;
					 					  			break;
										  
				case 'search-tithe-time-project': $data['post_type'] = 4;
					 					  			break;
			 }
			
			
			if($data['post_type'] == 1){
				ob_start();
				$this->projects_ajax_pagination(1, $page);
				$content = ob_get_contents();
				$content_obj = json_decode($content);
				$data['charity_list_content'] = $content_obj->html;
				$data['no_of_result']  = $content_obj->no_of_result;
				ob_end_clean();
			}
			else if($data['post_type'] == 2){
				
				
				ob_start();
				$this->projects_ajax_pagination(2, $page);
				$content = ob_get_contents();
				$content_obj = json_decode($content);
				$data['charity_list_content'] = $content_obj->html;
				$data['no_of_result']  = $content_obj->no_of_result;
				ob_end_clean();
			}
			else if($data['post_type'] == 3){
				
				
				ob_start();
				$this->projects_ajax_pagination(3, $page);
				$content = ob_get_contents();
				$content_obj = json_decode($content);
				$data['charity_list_content'] = $content_obj->html;
				$data['no_of_result']  = $content_obj->no_of_result;
				ob_end_clean();
			}
			
			else if($data['post_type'] == 4){
				
				
				ob_start();
				$this->projects_ajax_pagination(4, $page);
				$content = ob_get_contents();
				$content_obj = json_decode($content);
				$data['charity_list_content'] = $content_obj->html;
				$data['no_of_result']  = $content_obj->no_of_result;
				ob_end_clean();
			}
			
			
			
			# view file...
			
            $VIEW = "logged/build_the_kingdom/charity-project-home.phtml";
            parent::_render($data, $VIEW);
        } 
        catch(Exception $err_obj)
        {
           
        } 

    }
	
	
	### prayer group notificaions
	
	public function projects_ajax_pagination($post_type, $page=0)
    {
		
		$cur_page = $page + $this->pagination_per_page;
		$data = $this->data;
		$s_where = '';
		################## search filter ############
		if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) :
            
				$WHERE_COND = " AND 1 ";
				
				$project_name = get_formatted_string(trim($this->input->post('txt_project_name')));
				$WHERE_COND .= ($project_name=='')?'':" AND P.s_title LIKE '%".$project_name."%' ";
				
				$skill_name = get_formatted_string(trim($this->input->post('txt_skill')));
				$WHERE_COND .= ($skill_name=='')?'':" AND S.s_name LIKE '".$skill_name."%' ";
				
			   /* if($this->input->post('date_to') != ''){
					  $project_start_date = get_db_dateformat($this->input->post('date_to'));
					  $WHERE_COND .= ($project_start_date=='')?'':" AND (P.dt_start_date  ='".$project_start_date."' )";
				 }
			  
				if($this->input->post('date_to2') != ''){
					   $project_end_date = get_db_dateformat($this->input->post('date_to2'));
					  $WHERE_COND .= ($project_end_date=='')?'':" AND (P.dt_end_date ='".$project_end_date."' )";
				}
				*/
				
				if($this->input->post('date_to') != '' && $this->input->post('date_to2') != ''){
					
					 $project_start_date = get_db_dateformat($this->input->post('date_to'));
					 $project_end_date = get_db_dateformat($this->input->post('date_to2'));
					 $WHERE_COND .= ($project_start_date=='')?'':" AND (
					 											P.dt_start_date  BETWEEN '".$project_start_date."' AND '".$project_end_date."'
																OR P.dt_end_date BETWEEN '".$project_start_date."' AND '".$project_end_date."')";
				}
				else if($this->input->post('date_to') != '' && $this->input->post('date_to2') == ''){
					
					 $project_start_date = get_db_dateformat($this->input->post('date_to'));
					 $WHERE_COND .= ($project_start_date=='')?'':" AND (
					 											P.dt_start_date  = '".$project_start_date."' )";
				}
				else if($this->input->post('date_to') == '' && $this->input->post('date_to2') != ''){
					 $project_end_date = get_db_dateformat($this->input->post('date_to2'));
					 $WHERE_COND .= ($project_end_date=='')?'':" AND (P.dt_end_date ='".$project_end_date."' )";
				}
				
				
				
				if($this->input->post('date_to3') != '' && $this->input->post('date_to4') != ''){
					
					 $skill_start_date = get_db_dateformat($this->input->post('date_to3'));
					 $skill_end_date = get_db_dateformat($this->input->post('date_to4'));
					 $WHERE_COND .= ($skill_end_date=='' && $skill_start_date == '')?'':" AND (
					 											S.dt_start_date  BETWEEN '".$skill_start_date."' AND '".$skill_end_date."'
																OR S.dt_end_date BETWEEN '".$skill_start_date."' AND '".$skill_end_date."')";
				}
							
				else if($this->input->post('date_to3') != '' && $this->input->post('date_to4') == ''){
					  $skill_start_date = get_db_dateformat($this->input->post('date_to3'));
					  $WHERE_COND .= ($skill_start_date=='')?'':" AND (S.dt_start_date  ='".$skill_start_date."' )";
				}
			    else if($this->input->post('date_to4') != '' && $this->input->post('date_to3') == ''){
					  $skill_end_date = get_db_dateformat($this->input->post('date_to4'));
					  $WHERE_COND .= ($skill_end_date=='')?'':" AND (S.dt_end_date ='".$skill_end_date."' )";
				}
				
				//echo $WHERE_COND.' ##'; exit;
				if($WHERE_COND != " AND 1 "){
					$this->session->set_userdata('search_condition',$WHERE_COND);
				}
			
           endif;  
		   
		   
		   	
			if($this->session->userdata('search_condition') != ''){
				$s_where = $this->session->userdata('search_condition');	
			}
			
			
		################ search filter ##################
		
		if($post_type == 1 || $post_type == 3){
				$result = $this->projects_model->get_project_details_list($s_where, intval($page), $this->pagination_per_page);
				
				$total_rows = $this->projects_model->get_project_details_list_count($s_where); 
		}
		else if(($post_type == 2 && $s_where != '') || ($post_type == 4 && $s_where != '')){
			$result = $this->projects_model->get_project_details_list($s_where, intval($page), $this->pagination_per_page);
			$total_rows = $this->projects_model->get_project_details_list_count($s_where); 
		}
		
		
		
		
		$data['result_arr'] = $result;
		$data['no_of_result'] = $total_rows;
		$data['current_page_1'] = $cur_page;
		
		 //--- for check end of he page.
		   $view_more = true;
		   $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->pagination_per_page)
			  $view_more = false;
		 //--------- end check
		
		if($post_type == 1 || $post_type == 2){
			$VIEW_FILE = "logged/build_the_kingdom/ajax/charity_project_list_ajax.phtml";
		}
		else if($post_type == 3 || $post_type == 4){
			
			$VIEW_FILE = "logged/build_the_kingdom/ajax/charity_project_tithe_list_ajax.phtml";
		}
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
		
        echo json_encode( array('html'=>$content, 'no_of_result'=>$data['no_of_result'],'view_more'=>$view_more, 'cur_page'=>$data['current_page_1']) );
			
	}
	
	
	
  public function CommonDonation(){
		
		$card_info = array();

		################################
		#### DIRECT CARD PROCESSING 
		################################
		  
		    $card_info['txt_amount'] = $this->input->post('txt_amount');
			$card_info['txt_card_holder'] = trim($this->input->post('txt_card_holder'));
			$card_info['mnth'] = trim($this->input->post('mnth'));
			$card_info['yr']  = trim($this->input->post('yr'));
			$card_info['txt_cvv'] = $this->input->post('txt_cvv');
			$card_info['txt_card_number'] = $this->input->post('txt_card_number');
			$card_info['txt_card_typ'] = trim($this->input->post('txt_card_typ'));
			
			
			$arr_messages = array();
			
			if($card_info['txt_amount'] == ''){
				$arr_messages['amount'] = '* Required Field.';
			}
			else if(!is_numeric($card_info['txt_amount'])){
				$arr_messages['amount'] = '* Please provide numeric input.';
			}
			
			if($card_info['txt_card_holder'] == ''){
				$arr_messages['card_holder'] = '* Required Field.';
			}
			
			if($card_info['mnth'] == '-1' || $card_info['yr'] == '-1' ){
				$arr_messages['mnth'] = '* Invalid expiry date.';
			}
			
			/*if($card_info['yr'] == '-1' ){
				$arr_messages['yr'] = '* Required Field.';
			}*/
			
			if($card_info['txt_cvv'] == '' ){
				$arr_messages['cvv'] = '* Required Field.';
			}
			else if(!is_numeric($card_info['txt_cvv'])){
				$arr_messages['cvv'] = '* Invalid cvv.';
			}
			
			
			if($card_info['txt_card_number'] == ''){
				$arr_messages['card_number'] = '* Required Field.';
			}
			else if(!is_numeric($card_info['txt_card_number'])){
				$arr_messages['card_number'] = '* Invalid card number';
			}
			
			if($card_info['txt_card_typ'] == '-1'){
				$arr_messages['card_typ'] = '* Required Field.';
			}
			
			
		   if(count($arr_messages) == 0){ 
			 
					
					$i_user_id = intval(decrypt($this->session->userdata('user_id')));
					$profile_info = $this->users_model->fetch_this($i_user_id);
				  	
					 $db_info = array();
					
					 $db_info['i_user_id'] = $i_user_id;
					 $db_info['f_amount'] = $this->input->post('txt_amount');
					 $db_info['s_card_holder_name'] = trim($this->input->post('txt_card_holder'));
					 
					 $db_info['dt_created_on'] = get_db_datetime();
					
					 $donation_id = $this->projects_model->insert_common_donation_details($db_info); 
					#pr($card_info);
				  
					$paymentType = urlencode('Authorization');                // or 'Sale'
					$firstName = urlencode($profile_info['s_first_name']); //urlencode($_SESSION["fname"]);
					$lastName = urlencode($profile_info['s_last_name']); //urlencode($_SESSION["lname"]);
					$creditCardType = urlencode($card_info['txt_card_typ']);//urlencode($_SESSION["card_type"]);
					$creditCardNumber = urlencode($card_info['txt_card_number']);//urlencode($cardnum);
					
					if($card_info['mnth']<10)
						$card_info['mnth']="0".$card_info['mnth'];
						$expDateMonth = $card_info['mnth'];
					// Month must be padded with leading zero
					$padDateMonth = urlencode(str_pad($expDateMonth, 2, '0', STR_PAD_LEFT));
				
					$expDateYear = urlencode($card_info['yr']);
					$cvv2Number = urlencode($card_info['txt_cvv']);
					$address1 = '';
					$address2 = '';
					$city = '';
					$state = '';
					$zip = '';
					$country = '';                // US or other valid country code
					$amount = $card_info['txt_amount'] ;
					//$amount = 0.01;
					//$currencyID = urlencode('GBP'); 
					$currencyID = urlencode('USD'); 
				  
				  
				   $nvpStr =    "&PAYMENTACTION=$paymentType&AMT=$amount&CREDITCARDTYPE=$creditCardType&ACCT=$creditCardNumber".
						"&EXPDATE=$padDateMonth$expDateYear&CVV2=$cvv2Number&FIRSTNAME=$firstName&LASTNAME=$lastName".
						"&STREET=$address1&CITY=$city&STATE=$state&ZIP=$zip&COUNTRYCODE=$country&CURRENCYCODE=$currencyID";
		
					// Execute the API operation; see the PPHttpPost function above.
					$httpParsedResponseAr = PPHttpPost('DoDirectPayment', $nvpStr);
						  
				  //pr($httpParsedResponseAr,1);
				  
				 if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) 
				  {
					 //exit('Direct Payment Completed Successfully: '.print_r($httpParsedResponseAr, true));
					  
					  ## update donation recording 
					   $db_info['i_order_status'] = 1;
					   $this->projects_model->update_common_donation_details($db_info,$donation_id); 
					 
					  echo json_encode(array('msg'=>'Fund donated successfully.', 'success'=>true));
					  exit;
				  } 
				  else  
				  {
					// exit('DoDirectPayment failed: ' . print_r($httpParsedResponseAr, true));
					  
					  echo json_encode(array('msg'=>rawurldecode($httpParsedResponseAr['L_LONGMESSAGE0']),'success'=>false));
					  exit;
				  }
				  
		  }
		  else{
			  
			  echo json_encode(array('arr_messages'=>$arr_messages,'success'=>false));
			  exit;
		  }

	}
  
  
  public function donateFundToProject(){
		
		$card_info = array();

		################################
		#### DIRECT CARD PROCESSING 
		################################
		  
		    $card_info['txt_amount'] = $this->input->post('txt_amount');
			$card_info['txt_card_holder'] = trim($this->input->post('txt_card_holder'));
			$card_info['mnth'] = trim($this->input->post('mnth'));
			$card_info['yr']  = trim($this->input->post('yr'));
			$card_info['txt_cvv'] = $this->input->post('txt_cvv');
			$card_info['txt_card_number'] = $this->input->post('txt_card_number');
			$card_info['txt_card_typ'] = trim($this->input->post('txt_card_typ'));
			
			$project_id = $this->input->post('hd_project_id');
			
			$arr_messages = array();
			
			if($card_info['txt_amount'] == ''){
				$arr_messages['amount_'.$project_id] = '* Required Field.';
			}
			else if(!is_numeric($card_info['txt_amount'])){
				$arr_messages['amount_'.$project_id] = '* Please provide numeric input.';
			}
			
			if($card_info['txt_card_holder'] == ''){
				$arr_messages['card_holder_'.$project_id] = '* Required Field.';
			}
			
			if($card_info['mnth'] == '-1' || $card_info['yr'] == '-1' ){
				$arr_messages['mnth_'.$project_id] = '* Invalid expiry date.';
			}
			
			if($card_info['txt_cvv'] == '' ){
				$arr_messages['cvv_'.$project_id] = '* Required Field.';
			}
			else if(!is_numeric($card_info['txt_cvv'])){
				$arr_messages['cvv_'.$project_id] = '* Invalid cvv.';
			}
			
			if($card_info['txt_card_number'] == ''){
				$arr_messages['card_number_'.$project_id] = '* Required Field.';
			}
			else if(!is_numeric($card_info['txt_card_number'])){
				$arr_messages['card_number_'.$project_id] = '* Invalid card number';
			}
			
			if($card_info['txt_card_typ'] == '-1'){
				$arr_messages['card_typ_'.$project_id] = '* Required Field.';
			}
			
			
		   if(count($arr_messages) == 0){ 
			 
					
					 $i_user_id = intval(decrypt($this->session->userdata('user_id')));
					 $profile_info = $this->users_model->fetch_this($i_user_id);
				  	
					 $db_info = array();
					
					 $db_info['i_user_id'] = $i_user_id;
					 $db_info['i_project_id'] = $this->input->post('hd_project_id');
					 $db_info['f_amount'] = $this->input->post('txt_amount');
					 $db_info['s_card_holder_name'] = trim($this->input->post('txt_card_holder'));
					 $db_info['e_dnt_disclose_name'] = ($this->input->post('chk_dntDisclosed') == '1')?'Y':'N';
					 $db_info['e_gift_aid_my_donation'] = ($this->input->post('chk_aid') == '1')?'Y':'N';
					 $db_info['dt_created_on'] = get_db_datetime();
					
					$donation_id = $this->projects_model->insert_project_fund_donation_details($db_info); 
					#pr($card_info);
				  
					$paymentType = urlencode('Authorization');                // or 'Sale'
					$firstName = urlencode($profile_info['s_first_name']); //urlencode($_SESSION["fname"]);
					$lastName = urlencode($profile_info['s_last_name']); //urlencode($_SESSION["lname"]);
					$creditCardType = urlencode($card_info['txt_card_typ']);//urlencode($_SESSION["card_type"]);
					$creditCardNumber = urlencode($card_info['txt_card_number']);//urlencode($cardnum);
					
					if($card_info['mnth']<10)
						$card_info['mnth']="0".$card_info['mnth'];
					$expDateMonth = $card_info['mnth'];
					// Month must be padded with leading zero
					$padDateMonth = urlencode(str_pad($expDateMonth, 2, '0', STR_PAD_LEFT));
				
					$expDateYear = urlencode($card_info['yr']);
					$cvv2Number = urlencode($card_info['txt_cvv']);
					$address1 = '';
					$address2 = '';
					$city = '';
					$state = '';
					$zip = '';
					$country = '';                // US or other valid country code
					$amount = $card_info['txt_amount'] ;
					//$amount = 0.01;
					$currencyID = urlencode('USD'); 
				  
				  
				   $nvpStr =    "&PAYMENTACTION=$paymentType&AMT=$amount&CREDITCARDTYPE=$creditCardType&ACCT=$creditCardNumber".
						"&EXPDATE=$padDateMonth$expDateYear&CVV2=$cvv2Number&FIRSTNAME=$firstName&LASTNAME=$lastName".
						"&STREET=$address1&CITY=$city&STATE=$state&ZIP=$zip&COUNTRYCODE=$country&CURRENCYCODE=$currencyID";
		
					// Execute the API operation; see the PPHttpPost function above.
					$httpParsedResponseAr = PPHttpPost('DoDirectPayment', $nvpStr);
						  
				 // pr($httpParsedResponseAr,1);
				  
				 if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) 
				  {
					 //exit('Direct Payment Completed Successfully: '.print_r($httpParsedResponseAr, true));
					  
					  ## update donation recording 
					   $db_info['i_order_status'] = 1;
					   $this->projects_model->update_project_fund_donation_details($db_info,$donation_id); 
					   
					  $total_donation = $this->projects_model->get_total_fund_donated_by_project_id($project_id);
					 
					  echo json_encode(array('msg'=>'Fund donated successfully.', 'success'=>true,'total_donation'=>round($total_donation,2)));
					  exit;
				  } 
				  else  
				  {
					// exit('DoDirectPayment failed: ' . print_r($httpParsedResponseAr, true));
					  
					  echo json_encode(array('msg'=>rawurldecode($httpParsedResponseAr['L_LONGMESSAGE0']),'success'=>false));
					  exit;
				  }
				  
		  }
		  else{
			  
			  echo json_encode(array('arr_messages'=>$arr_messages,'success'=>false));
			  exit;
		  }
	
	}
  
  public function sendProjectQuery(){
	 
	  $i_user_id = intval(decrypt($this->session->userdata('user_id'))); 
	  $profile_info = $this->users_model->fetch_this($i_user_id);
	 
	  $s_content = nl2br( htmlspecialchars(trim($this->input->post('s_content')), ENT_QUOTES, 'utf-8') );
	  $project_name = trim($this->input->post('project_name'));
	  
	  $project_id = trim($this->input->post('project_id'));
	  
	  if($s_content != ''){
		  
		$info =  array();
		
		$info['i_user_id'] = $i_user_id;
		$info['i_project_id'] = $project_id;
		$info['s_information'] = $s_content;
		$info['s_project_name'] = $project_name;
		$info['dt_created_on'] = get_db_datetime();
		$id = $this->projects_model->insert_request_info($info);
		 parent::admin_send_message($logged_id,'project',$id);
		  
	   ### SENDING MAIL ###
	   	$this->load->helper('html');
					$this->load->library('email');
					 $email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
				$this->email->initialize($email_setting);
		$this->load->model('mail_contents_model');
		$mail_info = $this->mail_contents_model->get_by_name("charity_project_query");
	   
		$subject = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
		$subject = sprintf3( $subject, array('sender_name'=> $profile_info["s_first_name"],
										  'project_name'=> $project_name
									   ));
		
		$body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
		$body = sprintf3( $body, array('sender_name'=> $profile_info["s_first_name"],
									   		'project_name'=> '" '.$project_name.' "',
									  		'content' => $s_content
									   ) );
	   
									   
			$arr['subject'] 	 = $subject;
			$arr['to']         = 'admin@cogtime.com';
			$arr['bcc']        = 'aradhana.online19@gmail.com';
			$arr['from_email'] = $profile_info["s_email"];
			$arr['from_name']  = $profile_info["s_first_name"].' '.$profile_info["s_last_name"];
			$arr['message']    = $body;
			$this->email->from( $arr['from_email'], $arr['from_name']);
                #dump($arr);
				$this->email->subject($arr['subject']);
						
				$this->email->to($arr['to']);
				$this->email->bcc($arr['bcc']);
				$this->email->message("$body");
                        //send_mail($arr);
				$this->email->send();
		// send_mail($arr);
		 ### SENDING MAIL ###
		 
	     echo json_encode(array('msg'=>'Query sent successfully.', 'success'=>true));
		 exit;
	   
	   
	  }
	  else{
		  
		    echo json_encode(array('msg'=>'Please input some text.', 'success'=>false));
			exit;
	  }
	  
  }
  
  
    public function view_my_projects() 
    {
        try
        {
                  
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
            $this->data["MAIN_MENU_SELECTED"] = 6;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js',
										'js/animate-collapse.js',
                                        'js/lightbox.js',
										'js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',
										'js/jquery.ui.datepicker.js',*/
										'js/production/tweet_utilities.js',
										'js/production/charity_project.js',
										//'js/tab.js'
                                        ));
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css') );
          
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			
			$data['pagination_per_page'] = $this->pagination_per_page;
			
			$this->session->set_userdata('search_condition','');
			
			ob_start();
			$this->my_donated_projects_ajax_pagination($page);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['charity_list_content'] = $content_obj->html;
			$data['no_of_result']  = $content_obj->no_of_result;
			ob_end_clean();
			
			
			
			# view file...
			
            $VIEW = "logged/build_the_kingdom/view-projects.phtml";
            parent::_render($data, $VIEW);
        } 
        catch(Exception $err_obj)
        {
           
        } 
		
		#my_donated_project_list_ajax.phtml

    }
	
	public function my_donated_projects_ajax_pagination($page=0)
    {
		
		$cur_page = $page + $this->pagination_per_page;
		$data = $this->data;
		$s_where = '';
		
		$result = $this->projects_model->get_my_project_details_list($s_where, intval($page), $this->pagination_per_page);
		$total_rows = $this->projects_model->get_project_details_list_count($s_where);  		
		$data['result_arr'] = $result;
		$data['no_of_result'] = $total_rows;
		$data['current_page_1'] = $cur_page;
		
		 //--- for check end of he page.
		   $view_more = true;
		   $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->pagination_per_page)
			  $view_more = false;
		 //--------- end check
		
		
		$VIEW_FILE = "logged/build_the_kingdom/ajax/my_donated_project_list_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
		
        echo json_encode( array('html'=>$content, 'no_of_result'=>$data['no_of_result'],'view_more'=>$view_more, 'cur_page'=>$data['current_page_1']) );
			
	}
  
  	
	public function donateSkillRequest($p_id)
	{
	  try
		{
		   $data = $this->data;
		   $skill_arr =  $this->projects_model->get_all_skill_id_by_project_id($p_id);
		   
		   $arr_messages = array();
		   if($_POST)
           {
			   $checked_skill = array();
			   foreach($skill_arr as $val){
				   	$checked_skill[$val['id']] = ($this->input->post("chk_skill".$val['id']) == '1')?'yes':'no';
			   }
			  
			  // pr($checked_skill,1);
			   
			   ## error trapping.......
			   
				   if(count($checked_skill)){
					   
					   foreach($checked_skill as $key=>$s_val){
						   
						   if($s_val == 'yes'){
							   
								/*if(trim($this->input->post('ta_desc'.$key)) == ''){
									$arr_messages['ta_desc'.$key] = '* Required Field.';
								}*/
								
								if(trim($this->input->post('date_to_tim'.$key)) == ''){
									$arr_messages['date_to_tim'.$key] = '* Required Field.';
								}
								
								if(trim($this->input->post('date_from_tim'.$key)) == ''){
									$arr_messages['date_from_tim'.$key] = '* Required Field.';
								}
								
						   }
					   }
				   }
				   
				   ### check file 
					
					if( $_FILES['file_cv'.$p_id]['name']=='' ) {
						 $arr_messages['file_cv'.$p_id] = "* Required Field.";
					}
					else if( isset($_FILES['file_cv'.$p_id]['name']) && $_FILES['file_cv'.$p_id]['name']!='') 
					{
						preg_match('/(^.*)\.([^\.]*)$/', $_FILES['file_cv'.$p_id]['name'], $matches);
						$ext = "";
						if(count($matches)>0) {
							$ext = $matches[2];
							$original_name = $matches[1];
						}
						else
							$original_name = 'cv';
		
						$ext_arr = array('pdf','doc','docx');
						
						if ( !in_array($ext , $ext_arr)) 
						{
							 $arr_messages['file_cv'.$p_id] = "supported extensions are ".implode(' , ',$ext_arr);
						}
						/*else if($_FILES['file_cv'.$key]['size'] > $this->config->item('MAX_UP_FILE_SIZE')*1024*1024)
						 {
							 $arr_messages['file_cv'.$key] ="Maximum file upload size is ".$this->config->item('MAX_UP_FILE_SIZE')." MB.";
						 }	*/	
					}
				   
			   
			 }
			 if(count($arr_messages) == 0){ 
			   	
				 $target_path = $this->upload_path;
				 $file_name_arr = explode('.',$_FILES['file_cv'.$p_id]['name']);
				 $file_extn = end(explode('.',$_FILES['file_cv'.$p_id]['name']));
				 $file_name =  setCvFilename($target_path ,$file_name_arr[0] ,$file_extn); 
								  
				 $target_path = $target_path .$file_name; 
				  
				 move_uploaded_file($_FILES['file_cv'.$p_id]['tmp_name'], $target_path);
			   	
			   
				 if(count($checked_skill)){
				   
				   foreach($checked_skill as $key=>$s_val){
						if($s_val == 'yes'){   
						
							 
							 $info = array();
						   
							 $info["i_user_id"]     = intval(decrypt($this->session->userdata('user_id')));
							 $info["i_project_id"]  = $p_id;
							 $info["s_skill_name"]  = get_formatted_string(trim($this->input->post("txt_skill".$key)));
							 $info["s_description"] = get_formatted_string(trim($this->input->post("ta_desc".$key)));
							 $info["s_cv_filename"] = $file_name;
							 $info["d_start_date"]  = get_db_dateformat($this->input->post("date_to_tim".$key) , '/');
							 $info["d_end_date"]    = get_db_dateformat($this->input->post("date_from_tim".$key) , '/');
							 $info["dt_created_on"] = get_db_datetime();
							 
							 ### check if skill already donated for this date 
							 $err_msg = '';
							 $is_exists = $this->projects_model->checkSkillRequest_RequestedDate($info["s_skill_name"], $p_id, $info["d_start_date"],$info["d_end_date"] );
							 
								 if(!$is_exists){
								   	$ret_id = $this->projects_model->insert_skill_request($info);
								 }
								 
						  }
					   }
				   }
				   
				   if(!$is_exists){
		   	       	echo json_encode( array('result'=>true, 'p_id'=>$p_id, 'arr_messages'=>$arr_messages,
				   							'msg'=>'Skill donated successfully.'));
				   }
				   else{
						   $err_msg = 'You have already contributed your skill at this time.';
						   echo json_encode( array('result'=>false, 'p_id'=>$p_id,
							  'msg'=>$err_msg));
					   }
			 }
			 else
			 {
	 		   	 echo json_encode( array('result'=>false, 'p_id'=>$p_id, 'arr_messages'=>$arr_messages,
				 					'msg'=>$err_msg));
			 }
		 } 
	  catch(Exception $err_obj)
		{
		  show_error($err_obj->getMessage());
		} 
			
	} 
  
  
  	###
	
	public function search_prayer_request_eintercession() 
    {
        try
        {
                  
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js',
										'js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',
										'js/jquery/ui/jquery.ui.core.js',
										'js/jquery.ui.datepicker.js',*/
									    'js/jquery-ui-timepicker-addon.js',
										'js/jquery-ui-sliderAccess.js',
										'js/production/tweet_utilities.js',
										'js/production/tithe_time.js',
									//	'js/tab.js'
                                        ));
                                        
//            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css') );
          	
			$i_user_id = intval(decrypt($this->session->userdata('user_id')));
            
			$data['pagination_per_page'] = $this->pagination_per_page;
			
			$this->session->set_userdata('search_condition','');
			$WHERE_COND = '';
			$current_date = (date("Y-m-d"));
		    $curr_date =  date("Y-m-d", strtotime("$current_date -1 month"));
			
		   	if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) {
				
				
			 $search_typ = $this->input->post('search_typ'); 
			 $data['search_typ'] = $search_typ;
			 #### setting session as  per search type AND i.dt_end_date >= '{$curr_date}'
				if($search_typ == 'prayer_wall')
					 $alias  = 'p';
				else
					 $alias   = 'i';
					 
				    if($search_typ == 'eintercession_wall'){
							$WHERE_COND .= " WHERE i.i_is_enable = 1 AND i.e_request_type = 'On Going'
											   ";
					}
					
				   	if($this->input->post('start_prayer_dt1') != '' && $this->input->post('end_prayer_dt2') != ''){
						  
						  $dt_start_date = get_db_dateformat($this->input->post('start_prayer_dt1'));
						  $dt_end_date = get_db_dateformat($this->input->post('end_prayer_dt2'));
						  
						  if($WHERE_COND != ''){
							  $WHERE_COND .= ($dt_start_date =='' && $dt_end_date == '')?''
							  				 :" AND  ( ".$alias.".dt_start_date BETWEEN 
											 			'{$dt_start_date}' AND '{$dt_end_date}' 
														OR 
														".$alias.".dt_end_date BETWEEN 
											 			'{$dt_start_date}' AND '{$dt_end_date}' 
														
														OR '{$dt_start_date}' BETWEEN 
											 			 ".$alias.".dt_start_date AND  ".$alias.".dt_end_date
														OR 
														'{$dt_start_date}' BETWEEN 
											 			".$alias.".dt_start_date AND  ".$alias.".dt_end_date
														
														)";
						  }
						  else
						  {
							  $WHERE_COND .= ($dt_start_date =='' && $dt_end_date == '')?''
							  				 :"   (  ".$alias.".dt_start_date BETWEEN 
											 			'{$dt_start_date}' AND '{$dt_end_date}' 
														OR 
														".$alias.".dt_end_date BETWEEN 
											 			'{$dt_start_date}' AND '{$dt_end_date}' 
														
														OR '{$dt_start_date}' BETWEEN 
											 			 ".$alias.".dt_start_date AND  ".$alias.".dt_end_date
														OR 
														'{$dt_end_date}' BETWEEN 
											 			".$alias.".dt_start_date AND  ".$alias.".dt_end_date
														)";
						  }
					
				 	}
					
					
					## day
					if($this->input->post('chk_day') != ''){
						  
						  $day_arr  = array();
						  $day_arr = $this->input->post('chk_day');
						  
						  $day_str = implode(', ', $day_arr);
						  
						  //pr($day_arr);
						  if($WHERE_COND != ''){
							  $WHERE_COND .= ($day_str=='')?''
							  				  :" AND  ( DAYNAME(".$alias.".dt_start_date) in (".$day_str.")
											  			 OR 
														 DAYNAME(".$alias.".dt_end_date) in (".$day_str.")
											  		)";
						  }
						  else
						  {
							  $WHERE_COND .= ($day_str=='')?''
							  				  :" ( DAYNAME(".$alias.".dt_start_date) in (".$day_str.")
											  	   OR 
												   DAYNAME(".$alias.".dt_end_date) in (".$day_str.")
												  )";
						  }
					
						  
					}
			 		
					if($this->input->post('chk_time') != ''){
						  
						  $tim_arr  = array();
						  $tim_arr = $this->input->post('chk_time');
						  
						  $tim_str = '';
						  foreach($tim_arr as $k => $val){
							 
							  switch ($val) {
									case 0:
										$tim_str .= ($tim_str != '')?',0, 1, 2':'0, 1, 2';
										break;
									case 3:
										$tim_str .= ($tim_str != '')?', 3, 4, 5':' 3, 4, 5';
										break;
									case 6:
										$tim_str .= ($tim_str != '')?', 6, 7, 8':' 6, 7, 8';
										break;
									case 9:
										$tim_str .= ($tim_str != '')?', 9, 10, 11':'9, 10, 11';
										break;
									case 12:
										$tim_str .= ($tim_str != '')?', 12, 13, 14': '12, 13, 14';
										break;
									case 15:
										$tim_str .= ($tim_str != '')?', 15, 16, 17':'15, 16, 17';
										break;
									case 18:
										$tim_str .= ($tim_str != '')?', 18, 19, 20':'18, 19, 20';
										break;
									case 21:
										$tim_str .= ($tim_str != '')?', 21, 22, 23':'21, 22, 23';
										break;
									
							  }
						  }
						  
						  if($WHERE_COND != ''){
							  $WHERE_COND .= ($tim_str=='')?''
							  				  :" AND ( HOUR(".$alias.".dt_start_date) in (".$tim_str.")
											  		  OR HOUR(".$alias.".dt_end_date) in (".$tim_str.")
											  )";
						  }
						  else
						  {
							  $WHERE_COND .= ($tim_str=='')?''
							  				  :" ( HOUR(".$alias.".dt_start_date) in (".$tim_str.") 
											  		OR HOUR(".$alias.".dt_end_date) in (".$tim_str.") 
												 )";
						  }
						  
					}
			 
		  
		  		 $this->session->set_userdata('search_condition',$WHERE_COND);
		   }
		   
			//echo $this->session->userdata('search_condition'); 
			ob_start();
		    $this->search_all_prayer_request_ajax_pagination($search_typ, 0);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['search_content'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
			
			# view file...
			
            $VIEW = "logged/build_the_kingdom/tithe-time.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }
	
	public function search_all_prayer_request_ajax_pagination($search_typ, $page=0)
    {
		
		$this->load->model('prayer_wall_model');
		$this->load->model('intercession_model');
		## seacrh conditions : filter ############
		 
		
		if($this->session->userdata('search_condition') != ''){
			
			if($search_typ == 'eintercession_wall'){
				$s_where = $this->session->userdata('search_condition');
			}
			else
				$s_where = ' AND( '.$this->session->userdata('search_condition'). ') ';
		}
		
		//echo $s_where;
		$cur_page = $page + $this->pagination_per_page;
		
		$data = $this->data;
		
		 if($search_typ == 'prayer_wall'){ 
			$result = $this->prayer_wall_model->get_all_prayer_srch_result($s_where, $s_non_exact_where, intval($page), $this->pagination_per_page, '',false); 
	    	$total_rows = $this->prayer_wall_model->gettotal_prayer_srch_result($s_where, $s_non_exact_where,false);
		 }
		 else
		 {
			 $result = $this->intercession_model->get_all_intercession($s_where, intval($page), $this->pagination_per_page);
			 $total_rows = $this->intercession_model->get_count_all_intercession($s_where);
		 }
		
		//pr($result,1);
		$data['arr_request'] = $result;
		$data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
		$data['profile_id'] = $i_user_id;
		
		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->pagination_per_page)
			  $view_more = false;
         //--------- end check
		
		
		$VIEW_FILE = "logged/build_the_kingdom/ajax/prayer_request_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$content, 'no_of_result'=>$data['no_of_result'],'view_more'=>$view_more, 'cur_page'=>$data['current_page_1']) );
			
	}
  
	
	 public function registerChurch(){
		try
        {
                  
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
            $this->data["MAIN_MENU_SELECTED"] = 6;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js',
										'js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',
										'js/jquery/ui/jquery.ui.core.js',
										'js/jquery.ui.datepicker.js',*/
										'js/production/tweet_utilities.js',
										'js/production/church.js',
										//'js/tab.js'
                                        ));
                                        
//            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css') );
          
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			
			
			# view file...
			
            $VIEW = "logged/build_the_kingdom/register-church.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 
	 }
	 
	 public function add_church()
	 {
		try
		{
			
			parent::check_login(TRUE,'',array('1')); // put this code on those pages which are not accessable by non logged in user
			$arr_messages = array();
			# error message trapping...
			if( trim($this->input->post('txt_name'))=='') 
			{
					$arr_messages['name'] = "* Required Field.";
			}
			
			if( trim($this->input->post('txt_phone'))=='') 
			{
					$arr_messages['phone'] = "* Required Field.";
			}
			
			if( trim($this->input->post('txt_address'))=='') 
			{
					$arr_messages['address'] = "* Required Field.";
			}
			
			if( trim($this->input->post('txt_postcode'))=='') 
			{
					$arr_messages['postcode'] = "* Required Field.";
			}
			
			if( trim($this->input->post('sel_country'))=='-1') 
			{
					$arr_messages['country'] = "* Required Field.";
			}
			
			if( trim($this->input->post('txt_state'))=='-1') 
			{
					$arr_messages['state'] = "* Required Field.";
			}
			
			if( trim($this->input->post('txt_city'))=='-1') 
			{
					$arr_messages['city'] = "* Required Field.";
			}
                      if( trim($this->input->post('txt_email'))=='') 
			{
					$arr_messages['email'] = "* Required Field.";
			}
                        if(!filter_var(trim($this->input->post('txt_email')), FILTER_VALIDATE_EMAIL))
                          {
                $arr_messages['email'] = "Invalid email id.";
                          }
		   //pr($arr_messages);
			if( count($arr_messages)==0 ) {
					
				$info = array();
				
				$info['s_name'] = get_formatted_string($this->input->post('txt_name')); 
				$info['s_address'] = get_formatted_string($this->input->post('txt_address')); 
				$info['i_city_id'] = intval(decrypt($this->input->post('txt_city'))); 
				$info['i_state_id'] = intval(decrypt($this->input->post('txt_state'))); 
				$info['i_country_id'] = intval(decrypt($this->input->post('sel_country'))); 
				$info['s_phone'] = get_formatted_string($this->input->post('txt_phone'));
				$info['s_postcode'] = get_formatted_string($this->input->post('txt_postcode'));
				$info['ch_email'] = trim($this->input->post('txt_email'));
				$info['dt_created_on'] = get_db_datetime();
				$_ret = $this->church_model->insert($info);
				//$query = $this->db->query('SELECT name, title, email FROM my_table');
				$logged_id=intval(decrypt($_SESSION['user_id']));
                                $newArr =  get_primary_user_info($logged_id);
                                $content= " Hello admin@cogtime.com,
                            <p>A member has requested for church.</p>
                             <br>
                            <p>Thanks</p>
                               <p>".$newArr["s_email"]."</p>";
                                //pr($newinfo);
                                $time= get_db_datetime();
                                $email = $newArr["s_email"];
                                $_newr = $this->church_model->insert_information($logged_id,$_ret,$time,$content,$email);
                                parent::admin_send_message($logged_id,'church',$_ret);
				### send mail to admin
				$i_user_id = intval(decrypt($this->session->userdata('user_id')));
				
				$info = get_primary_user_info($i_user_id);
					$this->load->helper('html');
					$this->load->library('email');
					 $email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
				$this->email->initialize($email_setting);
				$this->load->model('mail_contents_model');
				$mail_info = $this->mail_contents_model->get_by_name("add_church_request");
				$body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
				
			
				$body = sprintf3( $body, array(
						   'email'=>$info["s_email"],
						   'member_name'=>$info["s_profile_name"],
						   'admin' =>'admin@cogtime.com') );
								   
				$arr['subject'] 	= htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
				$arr['to']         = 'admin@cogtime.com';
				
				$arr['from_email'] = $info["s_email"];;
				$arr['from_name'] = $info["s_email"];
				$arr['message'] = $body;
				$this->email->from( $arr['from_email'], $arr['from_name']);
                #dump($arr);
				$this->email->subject($arr['subject']);
						
				$this->email->to($arr['to']);
				$this->email->bcc($arr['bcc']);
				$this->email->message("$body");
                        //send_mail($arr);
				$this->email->send();
				//send_mail($arr);
				
				
					
				echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'Church Registered Successfully.'));
			}
			else
			{
				echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'Error!') );
			}
		
		
		
		}
		catch(Exception $err_obj)
        {
            
        } 
	}
	 
	  public function findChurch(){
		try
        {
                  
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
            $this->data["MAIN_MENU_SELECTED"] = 6;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js',
										'js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',
										'js/jquery/ui/jquery.ui.core.js',
										'js/jquery.ui.datepicker.js',*/
										'js/production/tweet_utilities.js',
										//'js/tab.js',
										'js/autocomplete/jquery.autocomplete.js'
                                        ));
                                        
            parent::_add_css_arr( array(/*'css/jquery-ui-1.8.2.custom.css',
											'css/dd.css',*/
									'css/jquery.autocomplete.css'
										) );
          
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			$this->session->set_userdata('search_condition','');
			$data['pagination_per_page'] = $this->church_pagination_per_page;;
		
			# view file...
			
            $VIEW = "logged/build_the_kingdom/find-church.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 
	 }
	 
	public function search_church_ajax_pagination($page=0)
    {
		
		
		$cur_page = $page + $this->church_pagination_per_page;
		$data = $this->data;
		## seacrh conditions : filter ############
		 	
			 if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) :
           
				$WHERE_COND = " WHERE 1 AND i_disabled = 1  ";
				
				$s_name = get_formatted_string(trim($this->input->post('txt_name')));
				$WHERE_COND .= ($s_name=='')?'':' AND C.s_name LIKE "%'.$s_name.'%" ';
                                
                                $s_email = get_formatted_string(trim($this->input->post('txt_email')));
				$WHERE_COND .= ($s_email=='')?'':' AND C.ch_email LIKE "%'.$s_email.'%" ';
                                
				
				$s_phone = get_formatted_string(trim($this->input->post('txt_phone')));
				$WHERE_COND .= ($s_phone=='')?'':' AND C.s_phone LIKE "'.$s_phone.'%"' ;
				
				$s_address = get_formatted_string(trim($this->input->post('txt_address')));
				$WHERE_COND .= ($s_address=='')?'':' AND C.s_address LIKE "'.$s_address.'%"' ;
				
				$s_postcode = get_formatted_string(trim($this->input->post('txt_postcode')));
				$WHERE_COND .= ($s_postcode=='')?'':' AND C.s_postcode LIKE "'.$s_postcode.'%"';
				
				
				
				/*$srch_country = intval(decrypt(trim($this->input->post('srch_country')))); 
				$WHERE_COND .= ($srch_country=='0')?'':" AND ( C.i_country_id  =  ".$srch_country." )";
				
				$srch_state = intval(decrypt(trim($this->input->post('srch_state'))));
				$WHERE_COND .= ($srch_state=='0')?'':" AND ( C.i_state_id =  ".$srch_state.")";
				
				$srch_city = intval(decrypt(trim($this->input->post('srch_city'))));
				$WHERE_COND .= ($srch_city=='0')?'':" AND ( C.i_city_id  =  ".$srch_city.")";
				*/
				
				  $location = get_formatted_string(trim($this->input->post('txt_location'))); 
				  if($location != '')
				  {
				   $location_arr = explode(', ',$location);
				  }
				  $total_locations = count($location_arr);
				  
					 if($total_locations)
					 {
						 for($i=0;$i<$total_locations;$i++)
						 {
						  
						  if($i== 0){
							$WHERE_COND .= " AND (mst_c.s_country like '".trim($location_arr[$i])."%' OR  mst_s.s_state like '".$location_arr[$i]."%' OR mst_city.s_city like '".$location_arr[$i]."%')";
						  }
						  else{
						   
						   $WHERE_COND .= "  AND (mst_c.s_country like '{$location_arr[$i]}%' OR  mst_s.s_state like '{$location_arr[$i]}%' OR mst_city.s_city like '{$location_arr[$i]}%')";
						  }
					  
						   
					 }
					} 
       
				
				$this->session->set_userdata('search_condition',$WHERE_COND);
			
            endif;  
		   	
			$s_where = $this->session->userdata('search_condition'); 

		   	$result = $this->church_model->get_list($s_where,$page,$this->church_pagination_per_page,$order_by);
            $resultCount = count($result);
			$total_rows = $this->church_model->get_list_count($s_where);
		
		
		//pr($result,1);
		$data['arr_request'] = $result;
		$data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
		$data['profile_id'] = $i_user_id;
		
		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->church_pagination_per_page)
			  $view_more = false;
         //--------- end check
		
		
		$VIEW_FILE = "logged/build_the_kingdom/ajax/church_list_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$content, 'no_of_result'=>$data['no_of_result'],'view_more'=>$view_more, 'cur_page'=>$data['current_page_1']) );
			
	}
  
	
	 public function bible_quiz(){
		try
        {
                  
            $posted=array();
            $this->data["posted"]=$posted;/*don't change*/    
            $data = $this->data;      
            $this->data["MAIN_MENU_SELECTED"] = 6;
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js',
										'js/jquery-ui-1.8.2.custom.min.js',
                                        'js/stepcarousel.js',
										'js/jquery/ui/jquery.ui.core.js',
										'js/jquery.ui.datepicker.js',*/
										'js/frontend/logged/tweets/tweet_utilities.js',
									//	'js/tab.js'
                                        ));
                                        
//            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css') );
          
            $i_user_id = intval(decrypt($this->session->userdata('user_id')));
			$this->session->set_userdata('search_condition','');
			$data['pagination_per_page'] = $this->quiz_pagination_per_page;;
		
			ob_start();
			$this->quiz_ajax_pagination(0);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			$data['quiz_list_content'] = $content_obj->html;
			$data['no_of_result']  = $content_obj->no_of_result;
			ob_end_clean();
			# view file...
			
            $VIEW = "logged/build_the_kingdom/bible-quiz.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 
	 }
	 
	public function quiz_ajax_pagination($page=0)
    {
		
		//echo $page;
		$cur_page = $page + $this->quiz_pagination_per_page;
		$data = $this->data;
	
		$result = $this->bible_model->get_list($s_where,$page,$this->quiz_pagination_per_page,$order_by);
		$resultCount = count($result);
		$total_rows = $this->bible_model->get_list_count($s_where);
		
		
		//pr($result,1);
		$data['arr_request'] = $result;
		$data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;
		$data['profile_id'] = $i_user_id;
		
		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->quiz_pagination_per_page)
			  $view_more = false;
         //--------- end check
		
		
		$VIEW_FILE = "logged/build_the_kingdom/ajax/quiz_list_ajax.phtml";
		
		if( is_array($result) && count($result) ) {
			$content = $this->load->view( $VIEW_FILE , $data, true);
		}
		else {
			$content = '';
		}
		
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$content, 'no_of_result'=>$data['no_of_result'],'view_more'=>$view_more, 'cur_page'=>$data['current_page_1']) );
			
	} 
	
	
	public function get_skill_chart($project_id){
		
		$project_info =  $this->projects_model->get_by_id($project_id);
		$data['project_info'] = $project_info;
		
		$skill_arr = $this->projects_model->get_all_skill_by_project_id($project_id);
		
		$skill = array();
		
		if(count($skill_arr)){
			foreach($skill_arr as $s_val){
				array_push($skill, $s_val['s_name']);
			}
		}
		
		$data['skill_arr'] = $skill;
		//$data['suffice_skill_arr'] = $this->projects_model->getSkillSufficency($skill_arr);
		//pr($skill_arr);
		
		###############################################
		### forming manage skill calendar array
		###############################################
		
		
		$no_days = get_date_diff($project_info['dt_start_date'],$project_info['dt_end_date']);
		$total_days = $no_days[0]['difference'];
		
		$projectStartDay =  getDesiredDate($project_info['dt_start_date'],'d');
		$projectStartMnth =  getDesiredDate($project_info['dt_start_date'],'m');
		$projectStartYear =  getDesiredDate($project_info['dt_start_date'],'y');
		
		$calendar_arr = array();
		
		if($total_days != 0){
			
			$inc_end_date ='';
			$i = 0;
			while($inc_end_date < $project_info['dt_end_date']){
				
				$calendar_arr[$i]['day'] = date('d',mktime(0, 0, 0,  $projectStartMnth  ,$projectStartDay, $projectStartYear));
				$calendar_arr[$i]['month'] = date('M',mktime(0, 0, 0, $projectStartMnth  ,$projectStartDay, $projectStartYear));
				
				$calendar_arr[$i]['year'] = date('Y',mktime(0, 0, 0,  $projectStartMnth  ,$projectStartDay, $projectStartYear));
				$calendar_arr[$i]['day_name'] = getShortDay(date('l',mktime(0, 0, 0,  $projectStartMnth  ,$projectStartDay, $projectStartYear)));
				
				
				$projectStartDay = $projectStartDay+1;
				$inc_end_date = $calendar_arr[$i]['year'].'-'.date('m',mktime(0, 0, 0, $projectStartMnth  ,$projectStartDay, $projectStartYear)).'-'.$calendar_arr[$i]['day'];
				$calendar_arr[$i]['dt'] = $inc_end_date;
				$i++;
			}
		}
		
		$data['calendar_arr'] = $calendar_arr;
		
		###############################################
		### end forming manage skill calendar array
		###############################################
		
		$VIEW_FILE = "logged/build_the_kingdom/manage_skill_ajax_chart.phtml";
		$content = $this->load->view( $VIEW_FILE , $data, true);
		echo json_encode( array('html'=>$content));
	}
	
	
	############view map#####################
	
	public function view_map($church_id)
	{
		
		 $map = new simpleGMapAPI();
         $geo = new simpleGMapGeocoder();
              
         $map->setWidth(475);
         $map->setHeight(180);
		 $map->setZoomLevel(13);
		 
		 $church_detail = $this->church_model->get_by_id($church_id);
		 //pr($church_detail);
		
		 $full_address	= $church_detail['s_address'].' '.$church_detail['s_postcode'].' '.get_cityname($church_detail['i_city_id']).' '.get_state_name_by_id($church_detail['i_state_id']).' '.get_country_name_by_id($church_detail['i_country_id']);
				//echo $full_address;
		  $coords = $geo->getGeoCoords( $full_address );
		  
		  $map->addMarker($coords['lat'], $coords['lng'], 'Map Location', '', '', true);
		  $data['mapshow'] = $map;
		  $data['geodata'] = $coords;
		  $data['id'] = $church_id;
		// pr($map);
		//pr($this->data['geodata']);
		 
		  $html = $this->load->view('logged/build_the_kingdom/church_lightbox.phtml' , $data, true);
		
		  if(strtolower($coords['status']) == 'ok')
		  {
			  echo json_encode(array('success'=>true,'html'=>$html));
		  }
		  else
		  {
			  echo json_encode(array('success'=>false));
		  }
	}
	
  
}   // end of controller...

