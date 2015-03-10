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


class Trade_activities extends Base_controller
{
    
    private $pagination_per_page =  20;
    
    public function __construct()
     {
	 	
        try
        {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...
			$this->upload_path = BASEPATH.'../uploads/e_trade_product/';
            $this->load->model('users_model');
			$this->load->model('contacts_model');
			$this->load->model('netpals_model');
			$this->load->model('e_freebie_model');
			$this->load->model('e_trade_model');
			$this->load->model('e_swap_model');
			
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
        
            
            parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                        'js/switch.js','js/animate-collapse.js',
                                        'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',*/
										'js/production/tweet_utilities.js',
//                                        'js/stepcarousel.js',
//                                        'js/tab.js'
                                        ));
                                        
//            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
//                                          'css/dd.css') );
    
            
            # view file...
			$data['category'] = makeOptionEtradeTopCategory();
			$data['prodname'] = '';
			$_SESSION['prodname'] = '';
			
			if(count($_POST)>0)
			{
				$data['prodname']	  = $this->input->post('prodname');
				$_SESSION['prodname'] = $data['prodname'];
			}
			
			
			ob_start();
			$content = $this->generate_product_listing_AJAX();
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			#var_dump($content_obj);exit;
			$data['listingContent'] = $content_obj->html; 
			$data['trade_no_of_result'] = $content_obj->trade_no_of_result;
			$data['current_page_1'] = $content_obj->cur_page;
			ob_end_clean();
			
			
			ob_start();
			$content = $this->generate_product_listing_AJAX(1);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			#var_dump($content_obj);exit;
			$data['swap_listingContent'] = $content_obj->html; 
			$data['swap_no_of_result'] = $content_obj->swap_no_of_result;
			$data['current_page_1'] = $content_obj->cur_page;
			ob_end_clean();
			
			ob_start();
			$content = $this->generate_product_listing_AJAX(2);
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			#var_dump($content_obj);exit;
			$data['free_listingContent'] = $content_obj->html; 
			$data['free_no_of_result'] = $content_obj->free_no_of_result;
			$data['current_page_1'] = $content_obj->cur_page;
			ob_end_clean();
						
			
			$wh	=  " AND i_parent_category='".$mix_value[0]['id']."'";
			$data['subcategory'] = show_cateory_in_li($wh);
			$data['pagination_per_page'] = $this->pagination_per_page;
            $VIEW = "logged/efreebie/trade-center-activities.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }
	
	
	public function generate_product_listing_AJAX($type=0, $page=0)
    {
		$rcv_wh ='';
		$snt_wh = '';
		
		if($type == 0){
			$data['s_type'] = 'etrade';
			if($_SESSION['prodname'] != ''){
				
				$rcv_wh	= " AND  p.s_name like '%".$_SESSION['prodname']."%' 
							AND p.i_isenabled =1  AND p.i_user_id  = ".$this->i_profile_id;
				$snt_wh	= " AND p.s_name like '%".$_SESSION['prodname']."%' AND p.i_isenabled =1  
							AND r.i_user_id  = ".$this->i_profile_id;
			}
			else
			{
				$rcv_wh	= " AND p.i_isenabled =1  AND p.i_user_id  = ".$this->i_profile_id;
				$snt_wh	= " AND p.i_isenabled =1  AND r.i_user_id  = ".$this->i_profile_id;
			}
			
			$data['prod_data']	= $this->e_trade_model->get_my_trade_activities_list($rcv_wh,$snt_wh,$page,$this->pagination_per_page,'');
			$total_rows = $this->e_trade_model->get_my_trade_activities_list_count($rcv_wh,$snt_wh);
			$data['trade_no_of_result'] = $total_rows;
						
		}
		else if($type == 1){
			$data['s_type'] = 'eswap';
			if($_SESSION['prodname'] != ''){
				
				$rcv_wh	= " AND  p.s_name like '%".$_SESSION['prodname']."%'
							AND p.i_isenabled =1  AND p.i_user_id= ".$this->i_profile_id;
				$snt_wh	= " AND  p.s_name like '%".$_SESSION['prodname']."%' AND p.i_isenabled =1  
							AND p.i_user_id= ".$this->i_profile_id;
			}
			else
			{
				$rcv_wh	= " AND p.i_isenabled =1  AND  p.i_user_id= ".$this->i_profile_id;
				$snt_wh	= " AND p.i_isenabled =1  AND  p.i_user_id= ".$this->i_profile_id;
			}
			
			$data['prod_data']	= $this->e_swap_model->get_my_trade_activities_list($rcv_wh,$snt_wh,$page,$this->pagination_per_page,'');
			$total_rows = $this->e_swap_model->get_my_trade_activities_list_count($rcv_wh,$snt_wh);
			$data['swap_no_of_result'] = $total_rows;
						
		}
		else if($type == 2){
			$data['s_type'] = 'efreebie';
			if($_SESSION['prodname'] != ''){
				
				$rcv_wh	= " AND  p.s_name like '%".$_SESSION['prodname']."%'
							AND p.i_isenabled =1  AND p.i_user_id  = ".$this->i_profile_id;
				$snt_wh	= " AND  p.s_name like '%".$_SESSION['prodname']."%' AND p.i_isenabled =1  
							AND r.i_user_id  = ".$this->i_profile_id;
			}
			else
			{
				$rcv_wh	= " AND p.i_isenabled =1  AND p.i_user_id  = ".$this->i_profile_id;
				$snt_wh	= " AND p.i_isenabled =1  AND r.i_user_id  = ".$this->i_profile_id;
			}
			
			$data['prod_data']	= $this->e_freebie_model->get_my_trade_activities_list($rcv_wh,$snt_wh,$page,$this->pagination_per_page,'');
			$total_rows = $this->e_freebie_model->get_my_trade_activities_list_count($rcv_wh,$snt_wh);
			$data['free_no_of_result'] = $total_rows;		
				
		}
		
			
		
		//pr($data['prod_data']);exit;
		$data['pagination_per_page'] = $this->pagination_per_page;
		//pr($result);
		$resultCount = count($data['prod_data']);
		$optionwh	= '';
		$cur_page = $page + $this->pagination_per_page;
        
        
		// getting auction-category listing...
		$data['no_of_prod'] = $total_rows;
		$data['no_of_result'] = $total_rows;
		$data['current_page_1'] = $cur_page;

		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->pagination_per_page)
			  $view_more = false;
         //--------- end check
        # loading the view-part...
        $AJAX_VIEW_FILE = 'logged/efreebie/ajax_efreebie_activities.phtml';
        
        
   //pr($result);
		
		if( $total_rows>0 ) {
        	$listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);
		}
		else {
			$listingContent = '';
		}
		//echo 1; exit;
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$listingContent, 
								'current_page'=>$cur_page, 
								'trade_no_of_result'=>$data['trade_no_of_result'],
								'swap_no_of_result'=>$data['swap_no_of_result'],
								'free_no_of_result'=>$data['free_no_of_result'],
								'total'=>$total_rows,
								'view_more'=>$view_more ,
								'cur_page'=>$data['current_page_1']) );
    } 
	
	public function purchaseCredit()
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
                                        'js/lightbox.js','js/jquery.dd.js','js/jquery-ui-1.8.2.custom.min.js',*/
										'js/production/tweet_utilities.js',
//                                        'js/stepcarousel.js',
//                                        'js/tab.js'
                                        ));
                                        
//            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
//                                          'css/dd.css') );
    
            
            # view file...
			
			
			
			ob_start();
			$content = $this->generate_credits_listing_AJAX();
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			#var_dump($content_obj);exit;
			$data['listingContent'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			$data['current_page_1'] = $content_obj->cur_page;
			ob_end_clean();
			
			$data['pagination_per_page'] = $this->pagination_per_page;
			
			$data['total_amount'] = round($this->e_trade_model->checkTotalCredits($this->i_profile_id),2);
			
            $VIEW = "logged/efreebie/buy-product-listing-credit.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

	}
	
	public function purchaseProductCredit(){
		
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
				$arr_messages['err_amount'] = '* Required Field.';
			}
			else if(!is_numeric($card_info['txt_amount'])){
				$arr_messages['err_amount'] = '* Please provide numeric input.';
			}
			
			if($card_info['txt_card_holder'] == ''){
				$arr_messages['err_card_holder'] = '* Required Field.';
			}
			
			if($card_info['mnth'] == '-1' || $card_info['yr'] == '-1' ){
				$arr_messages['err_mnth'] = '* Invalid expiry date.';
			}
			
			if($card_info['txt_cvv'] == '' ){
				$arr_messages['err_cvv'] = '* Required Field.';
			}
			else if(!is_numeric($card_info['txt_cvv'])){
				$arr_messages['err_cvv'] = '* Invalid cvv.';
			}
			
			if($card_info['txt_card_number'] == ''){
				$arr_messages['err_card_number'] = '* Required Field.';
			}
			else if(!is_numeric($card_info['txt_card_number'])){
				$arr_messages['err_card_number'] = '* Invalid card number';
			}
			
			if($card_info['txt_card_typ'] == '-1'){
				$arr_messages['err_card_typ'] = '* Required Field.';
			}
			
			
		   if(count($arr_messages) == 0){ 
			 
					
					 $i_user_id = intval(decrypt($this->session->userdata('user_id')));
					 $profile_info = $this->users_model->fetch_this($i_user_id);
					 $db_info = array();
					
					 $db_info['i_user_id'] = $i_user_id;
					 $db_info['f_amount'] = $this->input->post('txt_amount');
					 $db_info['s_card_holder_name'] = trim($this->input->post('txt_card_holder'));
					 $db_info['dt_created_on'] = get_db_datetime();
					
					$donation_id = $this->e_trade_model->insert_purchase_credits_details($db_info); 
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
					    $this->e_trade_model->update_purchase_credits_details($db_info,$donation_id); 
					   
					    ob_start();
						$content = $this->generate_credits_listing_AJAX();
						$content = ob_get_contents();
						$content_obj = json_decode($content);
						#var_dump($content_obj);exit;
						$listingContent = $content_obj->html; 
						$no_of_result = $content_obj->trade_no_of_result;
						$current_page_1 = $content_obj->cur_page;					
						ob_end_clean();
						
						### update use credits table 
						$user_exists = $this->e_trade_model->checkUser($i_user_id); 
						if($user_exists){
							$info = array();
							$info['f_amount'] = $this->e_trade_model->checkTotalCredits($i_user_id)+ $db_info['f_amount']; 
							$total_amt = round($info['f_amount'],2); 
							$this->e_trade_model->update_credits($info,$i_user_id);
						}
						else
						{
							 $info = array();
							 $info['i_user_id'] = $i_user_id;
							 $info['f_amount'] = $this->input->post('txt_amount');
							 $info['dt_created_on'] = get_db_datetime();
							 $this->e_trade_model->insert_credits($info);
						}
						
						
					  echo json_encode(array('msg'=>'Credit purchased successfully.', 'success'=>true, 'listingContent'=>$listingContent, 'no_of_result'=>$no_of_result, 'current_page_1'=>$current_page_1 , 'total_amount'=>$total_amt));
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
	
	
	public function generate_credits_listing_AJAX($page=0)
    {
		
		$wh = " AND P.i_user_id= ".$this->i_profile_id;;	
		$data['prod_data']	= $this->e_trade_model->getCreditPurchaselist($wh,$page,$this->pagination_per_page,'');
		$total_rows = $this->e_trade_model->getCreditPurchaseCount($wh);
		$data['no_of_result'] = $total_rows;		
		$data['pagination_per_page'] = $this->pagination_per_page;
		//pr($result);
		$resultCount = count($data['prod_data']);
		$optionwh	= '';
		$cur_page = $page + $this->pagination_per_page;
        
        
		// getting auction-category listing...
		$data['no_of_result'] = $total_rows;
		$data['current_page_1'] = $cur_page;
		$data['page'] = $page;

		 //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
		   if($rest_counter<=$this->pagination_per_page)
			  $view_more = false;
         //--------- end check
        # loading the view-part...
        $AJAX_VIEW_FILE = 'logged/efreebie/credit_list_ajax.phtml';
        
        
   //pr($result);
		
		if( $total_rows>0 ) {
        	$listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);
		}
		else {
			$listingContent = '';
		}
		//echo 1; exit;
		//echo json_encode( array('html'=>$content, 'current_page'=>$page) );
        echo json_encode( array('html'=>$listingContent, 
								'current_page'=>$cur_page, 
								'no_of_result'=>$data['no_of_result'],
								'total'=>$total_rows,
								'view_more'=>$view_more ,
								'cur_page'=>$data['current_page_1']) );
    } 
   
}   // end of controller...

