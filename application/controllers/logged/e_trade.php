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


class E_trade extends Base_controller
{
    
    private $pagination_per_page =  10 ;
    private $search_pagination_per_page =  2 ;
    private $popular_pagination_per_page =  10 ;
    
    
    
    private $ring_members_pagination_per_page =  10 ;
    
    public function __construct()
     {
	 	
        try
        {
            parent::__construct();
            parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...
			$this->upload_path = BASEPATH.'../uploads/e_trade_product/';
            $this->load->model('users_model');
			//$this->load->model('contacts_model');
			//$this->load->model('netpals_model');
			$this->load->model('e_trade_model');
			$this->load->model('user_notifications_model');
			$this->load->model('user_alert_model');
			
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
			if(count($_POST)==0)
			{
				
				$res = $this->db->query("SELECT * FROM {$this->db->TRADE_CAT} WHERE 1 AND i_parent_category=0 ORDER BY s_category_name ASC LIMIT 0,1");
				$mix_value = $res->result_array();
				$data['category_name']	= '';#$mix_value[0]['s_category_name'];
				
				### fetching parent category:
				$data['parent_category_name']	=$mix_value[0]['s_category_name'];
				### fetching parent category:
				
				$_SESSION['categoryid']	= $mix_value[0]['id'];
				$_SESSION['subcat']		= 0;
			}
			else
			{
				
				$catid	= decrypt($this->input->post('category'));
				$subcat	= $this->input->post('subcat');
				if($subcat==1)
				{
					$res = $this->db->query("SELECT * FROM {$this->db->TRADE_CAT} WHERE 1 AND id='".$catid."'");
					$mix_value = $res->result_array();
					$data['category_name']	= $mix_value[0]['s_category_name'];
					
					
					$_SESSION['categoryid']	= $catid;
					$_SESSION['subcat']		= $subcat;
					$data['category'] = makeOptionEtradeTopCategory('',encrypt($mix_value[0]['i_parent_category']));
					
					
					### fetching parent category:
					$main_cat = $mix_value[0]['i_parent_category'];
					$PCAT = $this->db->query("SELECT * FROM {$this->db->TRADE_CAT} WHERE id = {$main_cat} ");
					$pcat_value = $PCAT->result_array();
					$data['parent_category_name']	= $pcat_value[0]['s_category_name'];
					### fetching parent category:
				}
				else
				{
					$res = $this->db->query("SELECT * FROM {$this->db->TRADE_CAT} WHERE 1 AND id='".$catid."'");
					$mix_value = $res->result_array();
					$data['category_name']	= $mix_value[0]['s_category_name'];
					$_SESSION['categoryid']	= $mix_value[0]['id'];
					$_SESSION['subcat']		= 0;
					$data['prodname']				= $this->input->post('prodname');
					$_SESSION['prodname'] = $data['prodname'];
					$data['category'] = makeOptionEtradeTopCategory('',encrypt($catid));
					
					### fetching parent category:
					if($mix_value[0]['i_parent_category'] != 0){
						$main_cat = $mix_value[0]['i_parent_category'];
						$PCAT = $this->db->query("SELECT * FROM {$this->db->TRADE_CAT} WHERE id = {$main_cat} ");
						$pcat_value = $PCAT->result_array();
						$data['parent_category_name']	= $pcat_value[0]['s_category_name'];
					}
					### fetching parent category:
				}
			}
			
			
			ob_start();
			$content = $this->generate_product_listing_AJAX();
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			#var_dump($content_obj);exit;
			$data['listingContent'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
			
			
			
			$wh	=  " AND i_parent_category='".$mix_value[0]['id']."'";
			$data['subcategory'] = show_cateory_in_li($wh);
			$data['pagination_per_page'] = $this->pagination_per_page;
            $VIEW = "logged/etrade/etrade_product.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 

    }
	
	
	public function generate_product_listing_AJAX($page=0)
    {
		if($_SESSION['subcat']==1)
			$wh	= " AND p.i_category_id='".$_SESSION['categoryid']."' AND p.i_isenabled = 1";
		else
		{
			if($_SESSION['prodname']!='')
				$wh	= " AND p.s_name LIKE '".$_SESSION['prodname']."%' AND c.i_parent_category='".$_SESSION['categoryid']."'
						AND p.i_isenabled = 1 ";
			else
				$wh	= " AND c.i_parent_category='".$_SESSION['categoryid']."' AND p.i_isenabled = 1";
		}
		$data['prod_data']	= $this->e_trade_model->get_etrade_product_list($wh,$page,$this->pagination_per_page,'');
		//pr($data['prod_data']);exit;
		$data['pagination_per_page'] = $this->pagination_per_page;
		//pr($result);
		$resultCount = count($data['prod_data']);
		$total_rows = $this->e_trade_model->get_etrade_product_list_count($wh);
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
        $AJAX_VIEW_FILE = 'logged/etrade/ajax_product/ajax_listing_etrade_product.phtml';
        
        
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
								'no_of_result'=>$data['no_of_prod'],
								'total'=>$total_rows,
								'view_more'=>$view_more ,
								'cur_page'=>$data['current_page_1']) );
    }   
	
    public function manage_my_product()
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
			
			$data['swap_menu'] =2 ;
			ob_start();
			$content = $this->manage_my_product_AJAX();
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			#var_dump($content_obj);exit;
			$data['listingContent'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();							  
			$data['pagination_per_page'] = $this->pagination_per_page;
            $VIEW = "logged/etrade/manage_my_etrade_product.phtml"; 
            parent::_render($data, $VIEW);
	}
	
	public function manage_my_product_AJAX($page=0)
    {
		$wh	= " AND p.i_user_id='".$this->i_profile_id."' AND p.i_isenabled = 1";

		$data['prod_data']	= $this->e_trade_model->get_etrade_product_list($wh,$page,$this->pagination_per_page,'');
		//pr($data['prod_data']);exit;
		$data['pagination_per_page'] = $this->pagination_per_page;
		//pr($result);
		$resultCount = count($data['prod_data']);
		$total_rows = $this->e_trade_model->get_etrade_product_list_count($wh);
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
        $AJAX_VIEW_FILE = 'logged/etrade/ajax_product/ajax_listing_etrade_product.phtml';
        
        
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
								'no_of_result'=>$data['no_of_prod'],
								'total'=>$total_rows,
								'view_more'=>$view_more ,
								'cur_page'=>$data['current_page_1']) );
    }   
	
	public function sendrequest()
	{
		$qty		= $this->input->post('qty');
		$id			= decrypt($this->input->post('id'));
		$email		= $this->input->post('email');
		$phone		= $this->input->post('phone');
		$prod_detail	= $this->e_trade_model->get_by_id($id);
		
		if($qty=='' || $qty==0)
		{
			echo json_encode(array("success"=>false,"msg"=>'Quantity shouldn\'t be blank'));
			exit;
		}
		else if($prod_detail['i_stock']<$qty)
		{
			echo json_encode(array("success"=>false,"msg"=>'Quantity shouldn\'t be greater than stock'));
			exit;
		}
		else if( $email!='' && (!preg_match("/^[_a-z0-9-\+]+(\.[_a-z0-9-\+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", trim($email))) ) 
        {
			echo json_encode(array("success"=>false,"msg"=>'Please check email id'));
			exit;
		}
		else
		{
			$sender		= $this->users_model->fetch_this($this->i_profile_id);
			#pr($sender);
			$receiver	= $this->users_model->fetch_this($prod_detail['i_user_id']);
			
			$arr['i_user_id']			= $this->i_profile_id;
			$arr['i_etrade_prod_id']	= $id;
			$arr['i_qty']				= $qty;
			$arr['s_email']				= ($email!='')?$email:$sender['s_email'];
			$arr['s_phone']				= ($phone!='')?$phone:$sender['s_mobile'];
			$arr['dt_req_date']			= get_db_datetime();
			
			$this->e_trade_model->insert_etrade_request($arr);
			
			
			$notificaion_opt = $this->user_alert_model->check_option_user_id($this->i_profile_id);	
					  
			$notification_arr = array();
		  
			## insert noifications ####
			if($notificaion_opt['e_trade_request_recvd'] == 'Y'){
				
				$notification_arr['i_requester_id'] = $this->i_profile_id;
				$notification_arr['i_accepter_id'] = $receiver['id'];
				$notification_arr['s_type'] = 'etrade_request_recvd';
				$notification_arr['dt_created_on'] = get_db_datetime();
				
				$ret = $this->user_notifications_model->insert($notification_arr);	
				
				$message_id = parent::social_notifications_message($notification_arr['i_requester_id'], $notification_arr['i_accepter_id'], 'etrade_send_request', $id,'etrade');
			}
			
		  ### end  ###
			$detail_arr = $this->e_trade_model->get_by_id($id);
		    $s_url = base_url().'etrade/'.$detail_arr['id'].'/detail.html';
		    $sname = '"<a href="'.$s_url.'">'.$detail_arr['s_name'].'</a>"';
		 
			$this->load->model('mail_contents_model');
			$mail_info = $this->mail_contents_model->get_by_name("etrade_send_request");
			$body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
			
			$body = nl2br(sprintf3( $body, array('usernaname'=>$receiver["s_profile_name"],
										 'sender_name'=>$sender["s_profile_name"],
										 "senderemail"=>$arr['s_email'],
										 "senderphone"=>$arr['s_phone'],
										 "s_url"=>$sname
										 )));
			
										 
			$arr['subject'] 	= htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
			$arr['to']         = $info["s_email"];
			$arr['bcc']        = 'aradhana.online19@gmail.com';
			$arr['from_email'] = 'no-reply@cogtime.com';
			$arr['from_name'] = 'admin@cogtime.com';//$this->site_settings_model->get('s_mail_from_name');
			$arr['message'] = $body;
			#pr($arr); exit;
			
			send_mail($arr);
			
			echo json_encode(array("success"=>true,"msg"=>'Successfully send request'));
			exit;
		
		}
		
	}
	
	public function edit_request()
	{
		$qty		= $this->input->post('qty');
		$reqid		= decrypt($this->input->post('reqid'));
		$id			= decrypt($this->input->post('id'));
		$prod_detail	= $this->e_trade_model->get_by_id($id);
		#pr($prod_detail,1);
		if($qty=='' || $qty==0)
		{
			echo json_encode(array("success"=>false,"msg"=>'Quantity shouldn\'t be blank'));
			exit;
		}
		else if($prod_detail['i_remain_stock']<$qty)
		{
			echo json_encode(array("success"=>false,"msg"=>'Quantity shouldn\'t be greater than stock'));
			exit;
		}
		else
		{
			$sender		= $this->users_model->fetch_this($this->i_profile_id);
			#pr($sender);
			$receiver	= $this->users_model->fetch_this($prod_detail['i_user_id']);
			
			$arr['i_qty']				= $qty;
			$arr['dt_req_date']			= get_db_datetime();
			$wh							= array("id"=>$reqid);
			$this->e_trade_model->update_etrade_request($arr,$wh);
			
			$this->load->model('mail_contents_model');
			$mail_info = $this->mail_contents_model->get_by_name("etrade_send_request");
			$body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
			
			$body = nl2br(sprintf3( $body, array('usernaname'=>$receiver["s_profile_name"],
										 'sender_name'=>$sender["s_profile_name"],
										 "senderemail"=>$arr['s_email'],
										 "senderphone"=>$arr['s_phone']
										 )));
			
										 
			$arr['subject'] 	= htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
			$arr['to']         = $info["s_email"];
			$arr['bcc']        = 'aradhana.online19@gmail.com';
			$arr['from_email'] = 'no-reply@cogtime.com';
			$arr['from_name'] = 'admin@cogtime.com';//$this->site_settings_model->get('s_mail_from_name');
			$arr['message'] = $body;
			#pr($arr); exit;
			
			send_mail($arr);
			
			$where			= " AND r.id='".$reqid."'";
			$data['info']		= $this->e_trade_model->get_etrade_request_product_list($where,'','','');
			$AJAX_VIEW_FILE = 'logged/etrade/ajax_product/ajax_edit_req_rating_div.phtml';
			$listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);
			
			echo json_encode(array("success"=>true,"msg"=>'Successfully send request','id'=>$reqid,'html'=>$listingContent));
			exit;
		
		}
	}
	
    public function add_etrade_product()
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
									'js/stepcarousel.js'
									));
									
		parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css') );
										  
		$wh_cat	= "i_delete=0";
		$data['category']	= makeOptionEtradeCategory($wh_cat);
										  
		$VIEW = "logged/etrade/add_etrade_product.phtml"; 
        parent::_render($data, $VIEW);
	}
	
	public function save_etrade_product()
	{
			
			$id	= $this->input->post('id');
			$arr_messages = array();
			
			### check do the user have enough credits 
			
			$user_exists = $this->e_trade_model->checkUser($this->i_profile_id); 
			$total_credits = $this->e_trade_model->checkTotalCredits($this->i_profile_id);
			
			$ETRADE_LISTING_COST = $this->data['site_settings_arr']['i_etrade_listing_cost'];
			
			if($user_exists && round($total_credits,2) >= $ETRADE_LISTING_COST){
					# error message trapping...
					if( trim($this->input->post('category'))=='') 
					{
						$arr_messages['err_category'] = "* Required Field.";
					}
					if( trim($this->input->post('product_name'))=='') 
					{
						$arr_messages['err_product_name'] = "* Required Field.";
					}
					if( trim($this->input->post('product_brand'))=='') 
					{
						$arr_messages['err_product_brand'] = "* Required Field.";
					}
					if( trim($this->input->post('product_attr1'))=='') 
					{
						$arr_messages['err_product_attr1'] = "* Required Field.";
					}
					if( trim($this->input->post('product_attr2'))=='') 
					{
						$arr_messages['err_product_attr2'] = "* Required Field.";
					}
					if( trim($this->input->post('product_desc'))=='') 
					{
						$arr_messages['err_product_desc'] = "* Required Field.";
					}
					if( trim($this->input->post('stock'))=='') 
					{
						$arr_messages['err_stock'] = "* Required Field.";
					}
					if( trim($this->input->post('condition'))=='') 
					{
							$arr_messages['err_condition'] = "* Required Field.";
					}
					if( trim($this->input->post('product_price'))=='') 
					{
						$arr_messages['err_product_price'] = "* Required Field.";
					}
					if( trim($this->input->post('opensale'))=='') 
					{
						$arr_messages['err_opensale'] = "* Required Field.";
					}
					else if( trim($this->input->post('opensale'))=='1') 
					{
						if(trim($this->input->post('localship'))=='')
						{
							$arr_messages['err_localship'] = "* Required Field.";
						}
					}
					else if( trim($this->input->post('opensale'))=='2') 
					{
						if(trim($this->input->post('international_ship'))=='')
						{
							$arr_messages['err_international_ship'] = "* Required Field.";
						}
					}
					else if( trim($this->input->post('opensale'))=='3') 
					{
						if(trim($this->input->post('localship'))=='')
						{
							$arr_messages['err_localship'] = "* Required Field.";
						}
						if(trim($this->input->post('international_ship'))=='')
						{
							$arr_messages['err_international_ship'] = "* Required Field.";
						}
					}
					
					
					if( isset($_FILES['image']['name']) && $_FILES['image']['name']!='') {
						preg_match('/(^.*)\.([^\.]*)$/', $_FILES['image']['name'], $matches);
						$ext = "";
						if(count($matches)>0) {
							$ext = $matches[2];
							$original_name = $matches[1];
						}
						else
							$original_name = 'photo';
						if ( !in_array($ext , $this->config->item('VALID_IMAGE_EXT'))) 
						{
							 $arr_messages['err_image'] ="supported extensions are ".implode(' , ',$this->config->item('VALID_IMAGE_EXT'));
						}
						else if($_FILES['image']['size'] > $this->config->item('MAX_UP_FILE_SIZE')*1024*1024)
						 {
							$arr_messages["err_image"] = "Maximum file upload size is ".$this->config->item('MAX_UP_FILE_SIZE')." MB.";
						 }
						 
					}
					else
					{
						if($id=='')
							$arr_messages['err_image'] = "* Required Field.";
					}
					
				   //pr($arr_messages);
					if( count($arr_messages)==0 ) {
							
						$info = array();
						
						$info['i_user_id'] 				= intval(decrypt($this->session->userdata('user_id')));	 
						$info['i_category_id'] 			= intval(decrypt($this->input->post('category')));
						$info['s_name'] 				= get_formatted_string($this->input->post('product_name')); 
						$info['s_brand'] 				= get_formatted_string($this->input->post('product_brand')); 
						$info['s_attribute1'] 			= get_formatted_string($this->input->post('product_attr1')); 
						$info['s_attribute2'] 			= get_formatted_string($this->input->post('product_attr2')); 
						$info['s_description'] 			= nl2br($this->input->post('product_desc')); 
						$info['i_stock'] 				= $this->input->post('stock');
						$info['i_remain_stock'] 		= $this->input->post('stock');
						$info['i_condition'] 			= $this->input->post('condition');
						$info['f_unit_price'] 			= $this->input->post('product_price'); 
						$info['i_open_to_offer_for_local'] 	= $this->input->post('opensale'); 
						$info['f_local_shipping_cost'] 	= $this->input->post('localship'); 
						$info['f_international_shipping_cost'] 	= $this->input->post('international_ship'); 
						$info['dt_insert_time'] 	= get_db_datetime();
						if( isset($_FILES['image']['name']) && $_FILES['image']['name']!='') 
							$info['s_image'] 				= $this->_upload_photo('','image');	
							
						if($id!='')
						{
							
							$_ret = $this->e_trade_model->update_etrade_product($info,array('id'=>$id));
							echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'Product Updated Successfully.') );
						}
						else
						{
							$info['product_id'] 	= '#'.time();
							$_ret = $this->e_trade_model->save_etrade_product($info);
							
							### update user credits 
							$pinfo = array();
						    $pinfo['f_amount'] = $this->e_trade_model->checkTotalCredits($this->i_profile_id)- 0.5 ; # 50 cents 
							$this->e_trade_model->update_credits($pinfo,$this->i_profile_id);
							
							echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'Product Added Successfully.') );
						}
						
						
					}
					else
					{
						echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'Error!') );
					}
			}
		 else
		 {
		    echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'You have insufficient balance, Please purchase credits!') );
		 }
	}
	public function edit_product($id)
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
									'js/stepcarousel.js'
									));
									
		parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css') );
										  
		$data['info']		= $this->e_trade_model->get_by_id($id);
		$wh_cat	= "i_delete=0";
		#echo $data['info']['i_category_id'];exit;
		$data['category']	= makeOptionEtradeCategory($wh_cat,encrypt($data['info']['i_category_id']));
										  
		$VIEW = "logged/etrade/edit_etrade_product.phtml"; 
        parent::_render($data, $VIEW);
	}
	
	public function delete_product()
	{
		$id		= $this->input->post('id');
		$wh	= " AND r.i_etrade_prod_id='".$id."'";
		$prod_data	= $this->e_trade_model->get_etrade_request_product_list($wh,'','','');
		
		if(count($prod_data)>0)
		{
			echo json_encode( array('success'=>true,'msg'=>'This product has a request. You can\'t delete this product now'));
			exit;
		}
		else
		{
			$wh		= array('id'=>$id);
			$this->e_trade_model->delete_product($wh);
			ob_start();
			$content = $this->manage_my_product_AJAX();
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			#var_dump($content_obj);exit;
			$listingContent 	= $content_obj->html; 
			ob_end_clean();		
			echo json_encode( array('success'=>true,'msg'=>'Deleted Successfully','html'=>$listingContent));
			exit;
		}
	}
	
	public function _upload_photo($prev_img = '',$fileElementName)
     {
      	parent::check_login(TRUE,'',array('1')); // put this code on those pages which are not accessable by non logged in user
	   #pr($_FILES);
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
					                   
                    
                    # @@@@@@@@@@@@ NEW RESIZING PART [BEGIN] @@@@@@@@@@@
                        $config = array();
                        $config['source_image'] = $this->upload_image;
                        $config['thumb_marker'] = '-thumb';
                        $config['crop'] = false;
                        $config['width'] = 73;
                        $config['height'] = 72;
                        $config1['crop_from'] = 'middle';
                        $config['within_rectangle'] = true;
                        $config['small_image_resize'] = 'no_resize';
                        resize_exact($config);
                        unset($config);
						
						
						$config = array();
                        $config['source_image'] = $this->upload_image;
                        $config['thumb_marker'] = '-big';
                        $config['crop'] = false;
                        $config['width'] = 411;
                        $config['height'] = 366;
                        $config1['crop_from'] = 'middle';
                        $config['within_rectangle'] = true;
                        $config['small_image_resize'] = 'no_resize';
                        resize_exact($config);
                        unset($config);
            
                      					
					$this->s_picture_path = $new_imagename.'.'.$ext;
					
					@unlink($this->upload_image); //Unlink the original image........
					//@unlink($this->upload_path.getThumbName($prev_img,'thumb')); //Unlink the prevoius image........
					
					return $this->s_picture_path;
					
				}
        else
        {
            return $prev_img; // Unchanged previous image
        }
    }
	
	public function manage_buy_request_received()
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
                                        
            parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
                                          'css/dd.css') );
    
            
            # view file...
			$data['category'] = makeOptionEtradeTopCategory();
			$data['prodname'] = '';
			$data['swap_menu'] =3 ;
			if(count($_POST)==0)
			{
				$res = $this->db->query("SELECT * FROM {$this->db->TRADE_CAT} WHERE 1 AND i_parent_category=0 ORDER BY s_category_name ASC LIMIT 0,1");
				$mix_value = $res->result_array();
				$data['category_name']	= $mix_value[0]['s_category_name'];
				$_SESSION['categoryid']	= $mix_value[0]['id'];
				$_SESSION['subcat']		= 0;
			}
			else
			{
				$catid	= decrypt($this->input->post('category'));
				$subcat	= $this->input->post('subcat');
				if($subcat==1)
				{
					$res = $this->db->query("SELECT * FROM {$this->db->TRADE_CAT} WHERE 1 AND id='".$catid."'");
					$mix_value = $res->result_array();
					$data['category_name']	= $mix_value[0]['s_category_name'];
					$_SESSION['categoryid']	= $catid;
					$_SESSION['subcat']		= $subcat;
				}
				else
				{
					$res = $this->db->query("SELECT * FROM {$this->db->TRADE_CAT} WHERE 1 AND id='".$catid."'");
					$mix_value = $res->result_array();
					$data['category_name']	= $mix_value[0]['s_category_name'];
					$_SESSION['categoryid']	= $mix_value[0]['id'];
					$_SESSION['subcat']		= 0;
					$data['prodname']				= $this->input->post('prodname');
					$_SESSION['prodname'] = $data['prodname'];
					$data['category'] = makeOptionEtradeTopCategory('',encrypt($catid));
				}
			}
			
			
			ob_start();
			$content = $this->manage_buy_request_received_AJAX();
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			#var_dump($content_obj);exit;
			$data['listingContent'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
			
			
			
			$wh	=  " AND i_parent_category='".$mix_value[0]['id']."'";
			$data['subcategory'] = show_cateory_in_li($wh);
			$data['pagination_per_page'] = $this->pagination_per_page;
            $VIEW = "logged/etrade/manage_buy_request_received.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 
	}
	
	public function manage_buy_request_received_AJAX($page=0)
	{
		
		$wh	= " AND p.i_user_id='".$this->i_profile_id."' AND r.i_isenabled = 1";

		$data['prod_data']	= $this->e_trade_model->get_etrade_request_product_list($wh,$page,$this->pagination_per_page,'');
		//pr($data['prod_data']);exit;
		$data['pagination_per_page'] = $this->pagination_per_page;
		//pr($result);
		$resultCount = count($data['prod_data']);
		$total_rows = $this->e_trade_model->get_etrade_request_product_list_count($wh);
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
        $AJAX_VIEW_FILE = 'logged/etrade/ajax_product/ajax_manage_buy_request_received.phtml';
        
        
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
								'no_of_result'=>$data['no_of_prod'],
								'total'=>$total_rows,
								'view_more'=>$view_more ,
								'cur_page'=>$data['current_page_1']) );
    
	}
	
	public function accept_deny()
	{
		$id					= $this->input->post('id');
		$type				= $this->input->post('type');
		$prodid				= decrypt($this->input->post('prodid'));
		$prod_detail		= $this->e_trade_model->get_by_id($prodid);
		
		$where			= " AND r.id='".$id."'";
		$req_detail		= $this->e_trade_model->get_etrade_request_product_list($where,'','','');
		
		if($prod_detail['i_remain_stock']<$req_detail[0]['req_qty'])
		{
			echo json_encode( array('success'=>false,'msg'=>'Stock is less than requested Qty.You can\'t accept this request. Please Update your stock immediately.','html'=>''));
			exit;
		}
		$info['i_accept']	= ($type=='accept')?'1':'2';
		$info['dt_accept']	= get_db_datetime();
		$wh					= array("id"=>$id);
		if($this->e_trade_model->update_etrade_request($info,$wh))
		{
			$updatearr['i_remain_stock']	= $prod_detail['i_remain_stock']-$req_detail[0]['req_qty'];
			$wh								= array("id"=>$prodid);
			$this->e_trade_model->update_etrade_product($updatearr,$wh);
			$where			= " AND r.id='".$id."'";
			$data['info']		= get_requests($where);
			$req_detail = $this->e_trade_model->get_request_by_id($id);
			$data['i_user_id'] = $req_detail['i_user_id'];
			
			$AJAX_VIEW_FILE = 'logged/etrade/ajax_product/ajax_rating_div.phtml';
			$listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);
			$msg	= "Successfully ".(($type=='accept')?'accepted':'declined');
			
			$notificaion_opt = $this->user_alert_model->check_option_user_id($req_detail['i_user_id']);	
					  
			$notification_arr = array();
		  
			## insert noifications ####
			if($type=='accept'){
				if($notificaion_opt['e_trade_request_accpt'] == 'Y'){
					
					$notification_arr['i_requester_id'] = $this->i_profile_id;
					$notification_arr['i_accepter_id'] = $req_detail['i_user_id'];
					$notification_arr['s_type'] = 'etrade_request_accpt';
					$notification_arr['dt_created_on'] = get_db_datetime();
					
					$ret = $this->user_notifications_model->insert($notification_arr);	
					
					$message_id = parent::social_notifications_message($notification_arr['i_requester_id'], $notification_arr['i_accepter_id'], 'etrade_accept_join_request', $prodid,'etrade');
				}
			}
			else{
				if($notificaion_opt['e_trade_request_declined'] == 'Y'){
					
					$notification_arr['i_requester_id'] = $this->i_profile_id;
					$notification_arr['i_accepter_id'] = $req_detail['i_user_id'];
					$notification_arr['s_type'] = 'etrade_request_declined';
					$notification_arr['dt_created_on'] = get_db_datetime();
					
					$ret = $this->user_notifications_model->insert($notification_arr);	
					
					$message_id = parent::social_notifications_message($notification_arr['i_requester_id'], $notification_arr['i_accepter_id'], 'etrade_decline_join_request', $prodid,'etrade');
				}
			}
			
		  ### end  ###
			
			echo json_encode( array('success'=>true,'msg'=>$msg,'html'=>$listingContent,"stockid"=>$prodid,"stockval"=>$updatearr['i_remain_stock']));
			exit;
		}
		else
		{
			echo json_encode( array('success'=>false,'msg'=>'Try again','html'=>''));
			exit;
		}
		
	}
	
	public function add_rate_for_buyer()
	{
		$id					= $this->input->post('id');
		$rate				= $this->input->post('rate');
		$info['f_rate_for_buyer']	= $rate;
		$wh					= array("id"=>$id);
		if($this->e_trade_model->update_etrade_request($info,$wh))
		{
			$where			= " AND r.id='".$id."'";
			$data['info']		= get_requests($where);
			
			$AJAX_VIEW_FILE = 'logged/etrade/ajax_product/ajax_rating_div.phtml';
			$listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);
			$msg	= "You have rated this buyer successfully ";
			echo json_encode( array('success'=>true,'msg'=>$msg,'html'=>$listingContent));
			exit;
		}
		else
		{
			echo json_encode( array('success'=>false,'msg'=>'Try agian','html'=>''));
			exit;
		}
	}
	
	public function shipped()
	{
		$id					= $this->input->post('id');
		$info['is_shipped']	= 1;
		$wh					= array("id"=>$id);
		if($this->e_trade_model->update_etrade_request($info,$wh))
		{
			$where			= " AND r.id='".$id."'";
			$data['info']		= get_requests($where);
			
			$req_detail = $this->e_trade_model->get_request_by_id($id);
			$data['i_user_id'] = $req_detail['i_user_id'];
			
			$AJAX_VIEW_FILE = 'logged/etrade/ajax_product/ajax_rating_div.phtml';
			$listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);
			
			$message_id = parent::social_notifications_message($this->i_profile_id, $req_detail['i_user_id'], 'etrade_request_shipped', $req_detail['i_etrade_prod_id'],'etrade');
			
			$msg	= "Status successfully updated!";
			echo json_encode( array('success'=>true,'msg'=>$msg,'html'=>$listingContent));
			exit;
		}
		else
		{
			echo json_encode( array('success'=>false,'msg'=>'Try agian','html'=>''));
			exit;
		}
	}
	
	public function manage_sent_request()
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
			$data['swap_menu'] =1 ;
			if(count($_POST)==0)
			{
				$res = $this->db->query("SELECT * FROM {$this->db->TRADE_CAT} WHERE 1 AND i_parent_category=0 ORDER BY s_category_name ASC LIMIT 0,1");
				$mix_value = $res->result_array();
				$data['category_name']	= $mix_value[0]['s_category_name'];
				$_SESSION['categoryid']	= $mix_value[0]['id'];
				$_SESSION['subcat']		= 0;
			}
			else
			{
				$catid	= decrypt($this->input->post('category'));
				$subcat	= $this->input->post('subcat');
				if($subcat==1)
				{
					$res = $this->db->query("SELECT * FROM {$this->db->TRADE_CAT} WHERE 1 AND id='".$catid."'");
					$mix_value = $res->result_array();
					$data['category_name']	= $mix_value[0]['s_category_name'];
					$_SESSION['categoryid']	= $catid;
					$_SESSION['subcat']		= $subcat;
				}
				else
				{
					$res = $this->db->query("SELECT * FROM {$this->db->TRADE_CAT} WHERE 1 AND id='".$catid."'");
					$mix_value = $res->result_array();
					$data['category_name']	= $mix_value[0]['s_category_name'];
					$_SESSION['categoryid']	= $mix_value[0]['id'];
					$_SESSION['subcat']		= 0;
					$data['prodname']				= $this->input->post('prodname');
					$_SESSION['prodname'] = $data['prodname'];
					$data['category'] = makeOptionEtradeTopCategory('',encrypt($catid));
				}
			}
			
			
			ob_start();
			$content = $this->manage_sent_request_AJAX();
			$content = ob_get_contents();
			$content_obj = json_decode($content);
			#var_dump($content_obj);exit;
			$data['listingContent'] = $content_obj->html; 
			$data['no_of_result'] = $content_obj->no_of_result;
			ob_end_clean();
			
			
			
			$wh	=  " AND i_parent_category='".$mix_value[0]['id']."'";
			$data['subcategory'] = show_cateory_in_li($wh);
			$data['pagination_per_page'] = $this->pagination_per_page;
            $VIEW = "logged/etrade/manage_request_sent.phtml"; 
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
           
        } 
	
	}
	
	public function manage_sent_request_AJAX($page=0)
	{
		$wh	= " AND r.i_user_id='".$this->i_profile_id."' AND p.i_isenabled = 1";
		$data['prod_data']	= $this->e_trade_model->get_etrade_request_sent_product_list($wh,$page,$this->pagination_per_page,'');
		//pr($data['prod_data']);exit;
		$data['pagination_per_page'] = $this->pagination_per_page;
		//pr($result);
		$resultCount = count($data['prod_data']);
		$total_rows = $this->e_trade_model->get_etrade_request_sent_product_list_count($wh);
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
        $AJAX_VIEW_FILE = 'logged/etrade/ajax_product/ajax_manage_buy_request_sent.phtml';
        
        
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
								'no_of_result'=>$data['no_of_prod'],
								'total'=>$total_rows,
								'view_more'=>$view_more ,
								'cur_page'=>$data['current_page_1']) );
    
	}
	
	public function add_rate_for_seller()
	{
		$id						= decrypt($this->input->post('id'));
		$item					= $this->input->post('item');
		$communication			= $this->input->post('communication');
		$dispatch				= $this->input->post('dispatch');
		$info['f_rate_for_seller_item']	= $item;
		$info['f_rate_for_seller_communication']	= $communication;
		$info['f_rate_for_seller_time']	= $dispatch;
		$wh					= array("id"=>$id);
		if($this->e_trade_model->update_etrade_request($info,$wh))
		{
			$where			= " AND r.id='".$id."'";
			$data['info']		= $this->e_trade_model->get_etrade_request_sent_product_list($where,'','','');
			
			$AJAX_VIEW_FILE = 'logged/etrade/ajax_product/ajax_edit_req_rating_div.phtml';
			$listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);
			$msg	= "You have rated this buyer successfully ";
			echo json_encode( array('success'=>true,'msg'=>$msg,'id'=>$id,'html'=>$listingContent));
			exit;
		}
		else
		{
			echo json_encode( array('success'=>false,'msg'=>'Try agian','html'=>''));
			exit;
		}
	}
	
	public function detail($id)
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
//									'js/stepcarousel.js',
//									'js/tab.js'
									));
									
//		parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
//                                          'css/dd.css') );
										  
		$data['info']	= $this->e_trade_model->get_by_id($id);	
							  
		$VIEW = "logged/etrade/detail.phtml"; 
		parent::_render($data, $VIEW);
	}
	
}   // end of controller...

