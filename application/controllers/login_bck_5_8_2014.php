<?php
/*********
* Author: 
* Date  : 
* Modified By: 
* Modified Date:
* 
* Purpose:
*  Controller For Login Page 
* 
* 
*/
require_once(APPPATH.'libraries/recaptchaLib/recaptchalib.php');
include(APPPATH.'controllers/base_controller.php');

class Login extends Base_controller
{
   
    public function __construct()
     {
        try
        {
            parent::__construct();
            parent::_non_accessible_by_logged(); // put this code on those pages which are not accessable by logged in user
			# loading reqired model & helpers...
            $this->load->model('users_model');
		
			
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
            #pr($data);
          
            parent::_set_title('::: COGTIME Xtian network :::');
            parent::_set_meta_desc('');
            parent::_set_meta_keywords('');
		
			
            parent::_add_js_arr( array('js/login.js'=>'header'));
            
            /////////////////////////////////////////////
			
			if($this->input->post('is_submitted') == 'Y' ) {
			    #pr($_POST);
				
				
				$posted["txt_email"]=trim($this->input->post("txt_email",true));
				$posted["txt_password"]=trim($this->input->post("txt_password",true));
               // $chkRem = ($this->input->post('chkRem')== '1')? 'checked' :'';
				$this->form_validation->set_message('required', "* Required Field.");
				$this->form_validation->set_message('valid_email', "* Invalid Email ID.");
				$this->form_validation->set_message('matches', "field does not match");
				
				
				$this->form_validation->set_rules('txt_email', 'Email', 'trim|required|valid_email'); 
				$this->form_validation->set_rules('txt_password', "Password", 'trim|required');
				
				
				if ($this->form_validation->run() == FALSE)
				{
				    ////////Display the add form with posted values within it////
                    $this->data["posted"]=$posted;/*don't change*/
				}
				else
				{
				
						$hash = '';
						if( $this->input->post('hash')!='' ) {
							$hash = $this->input->post('hash');
						}
						
						
						$default_redirect_url = base_url()."index.html";
						
						if( $this->input->post('login_referrer')!='' && url_exists(base64_decode($this->input->post('login_referrer'))) ) {
							$redirect_url =  base64_decode($this->input->post('login_referrer')).$hash ;
						}
						
						
						$pattern = '/[\s]*\'[\s]*(.*)[\s]*\'[\s]*/';
	
						$login_data['s_email'] = preg_replace($pattern, "$1",$this->db->escape($posted["txt_email"]));
						$login_data['s_password'] = preg_replace($pattern, "$1",$this->db->escape($posted["txt_password"]));
						
						
						$loggedin = $this->users_model->authenticate($login_data);
					
						if(!empty($loggedin)) 
						{
						   						
							
							if( $loggedin['i_is_admin']==1) {
								$REDIRECT_URL = base_url();
								 header("location:".$REDIRECT_URL);               
									  exit;
							}
							else {
								
										$redirect_url = base_url().'my-wall.html';  
										 header("location:".$redirect_url);               
										 exit;
							
								 
							 }
						}
						else 
						{
							////////Display the add form with posted values within it////
							 $this->data["posted"]=$posted;/*don't change*/
							$data['err'] = 'Error in connection!';
						}
                 
				}
			}
			
			#pr($data);
			//////////////////////////////////////////
            # view file...
            $VIEW = "index.phtml";
            parent::_render($data, $VIEW);
			
		 }
        catch(Exception $err_obj)
        {
           
        }
    }
	
	
	
	public  function forgot_password()
	{
        try
        {         
            $posted=array();
			$this->data["posted"]=$posted;/*don't change*/    
			$data = $this->data;      
			#pr($data);
		  
			parent::_set_title('::: COGTIME Xtian network :::');
			parent::_set_meta_desc('');
			parent::_set_meta_keywords('');
			parent::_add_js_arr( array(  'js/jquery.autofill.js',
									  ));
			
			
		    $err_msg = ''; 
		    $err_flag = 0;
			$captcha_err ='';
			$data['success_msg'] = '';			
			if($_POST) 
			{
			    #pr($_POST);
				$posted["txt_email"]=trim($this->input->post("txt_email",true));
				$MAIL_ID = $posted["txt_email"];
                $posted["s_ssc"]=trim(decrypt($this->input->post("ssc")));
                $posted["s_security_code"]=trim($this->input->post("security_code"));
			  
				$this->form_validation->set_message('required', "* Required Field.");
				$this->form_validation->set_message('valid_email', "* Invalid Email ID.");
				//$this->form_validation->set_rules('txt_email', 'Email', 'trim|required|valid_email|callback_check_email'); 
				 if($posted["s_security_code"] == ''){
					 $captcha_err = "* Required Field.";
				 }
				else if($posted["s_ssc"]!=$posted["s_security_code"])
                {
                    $captcha_err = "* Verification Code field does not match the ssc field";
					//$this->form_validation->set_rules('security_code', 'Verification Code','trim|required|matches[ssc]');
                }
				
				 if( $MAIL_ID=='' ) {
					  $err_msg = "* Required Field.";
				  }
				  else if( !preg_match("/^[_a-z0-9-\+]+(\.[_a-z0-9-\+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", $MAIL_ID) ) {
					  $err_msg = '* Invalid Email ID.';
				  }
				  else{
					  # check for email-id existence...
					  $Query = sprintf("SELECT * FROM cg_users WHERE binary `s_email` = '%s' ", $MAIL_ID);
					  $query = $this->db->query($Query);
						//echo $this->db->last_query();
					  if( $query->num_rows() )
					  {
						  $row = $query->result_array();
						  //pr($row,1);
						  # retrieving replacable values...
						   $USER_ID = $row[0]['id'];
						  $USERNAME = $row[0]['s_first_name'].' '.$row[0]['s_last_name'];
						  $EMAIL = $row[0]['s_email'];
	  
						  # new random passowrd...
						
						  $RANDOM_PASS = $this->users_model->generatePassword();
						  $NEW_PASSWD = $RANDOM_PASS;
						  $replaceArr = array('email' =>  $EMAIL,
						  'name' => $USERNAME,
						  'password' => $NEW_PASSWD);
					  }
					  else
					  {
						  $err_msg = "* Email ID does not exists, Please try again.";
						  $err_flag = 1;
					  }
			  }
			  #########################################
					  
					  
					   
				
				
				if ($captcha_err !='' || $err_flag == 1 || $err_msg != '')
				{
				    ////////Display the add form with posted values within it////
                    $this->data["posted"]=$posted;/*don't change*/
					$this->data['email_err_msg'] = $err_msg; 
					$this->data['captcha_err'] = $captcha_err; 
								
					// 'here'; exit;
				}
				else
				{
					  
					  
					
					  
					  # sending mail to the user with the reset password [Start]...
					  $this->load->model('mail_contents_model');
					  $mail_info = $this->mail_contents_model->get_by_name("forgot_password_en");
					  $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
	  
					  $body = sprintf3( $body, $replaceArr);
	  
					  $arr['subject'] = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
					  $arr['to'] = $MAIL_ID;
					  $arr['bcc'] = 'aradhana.online19@gmail.com';
					  $arr['from_email'] = 'no-reply@cogtime.com';
					  $arr['from_name'] = 'Team Cogtime';
					  $arr['message'] = $body;
					  #dump($arr);
					  send_mail($arr);
					 
					  # sending mail to the user with the reset password [End]...
					  # and finally, updating user-info...
					  $pass_arr['s_password'] = get_salted_password($NEW_PASSWD);
					  $this->users_model->update($pass_arr,$USER_ID);
	  
					  $data['success_msg'] = "Your password has been reset and sent to your e-mail. Please check your e-mail <br> for the new password.";
					  //set_success_msg('You have Successfully Registered.');
					 /* $redirect_url = base_url().'index.html';  
										 header("location:".$redirect_url);               
										 exit;	*/				  
					
				}
			}
			
			 /////captcha///
        
            $this->load->library('antispam');
            $configs = array(
                    'img_path' => './captcha/',
                    'img_url' => base_url() . 'captcha/',
                    'font_path'     =>  './fonts/',
                    'font_name'        => 'VeraBd.ttf',
                    'img_height' => '35',
                    
                    'font_size'        =>     15
                    
                );            
            $data["captcha"] = $this->antispam->get_antispam_image($configs);
			

			  /* ******************** Generating recaptcha html ********************* */
				  $data['recaptcha_html'] = recaptcha_get_html($this->config->item('recaptcha_public_key'));
			  /* ********************************************************************* */
			
			#pr($data);
			//////////////////////////////////////////
            # view file...
            $VIEW = "forgot_password.phtml";
            parent::_render($data, $VIEW);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }                    

    
	}
	
	
}   // end of controller...

