<?php
/*********
* Author: 
* Date  : 
* Modified By: 
* Modified Date:
* 
* Purpose:
*  Controller For ## Management
* 
* @package 
* @subpackage 
* 
* @link InfController.php 
* @link Base_Controller.php
* @link model/##.php
* @link views/##
*/

class Index extends Admin_base_Controller
{

	function __construct()
	 {
		try
		{
		    parent::__construct();
			parent::_non_accessible_by_admin_logged(); //not accessable by logged in admin
            # loading reqired model & helpers...
		  	$this->load->model('admins_user_model');
			$this->load->model('admins_user_model');
		}
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

	}

	function index() 
	{

        try
		 {
            $data = $this->data;
			$data['err'] = '';
            
            # adjusting header & footer sections [Start]...
                parent::_set_title('::: COGTIME Xtian network :::');
                parent::_set_meta_desc("::: COGTIME Xtian network :::");
                parent::_set_meta_keywords("::: COGTIME Xtian network :::");
                
                parent::_add_js_arr( array('js/lightbox.js',
											
											'js/backend/forgot_pwd.js') );										
                parent::_add_css_arr( array('') );
		
			# adjusting header & footer sections [End]...
			if($this->input->post('is_submitted') == 'Y' ) {
				
				$posted["txt_email"]=trim($this->input->post("txt_email",true));
				$posted["txt_password"]=trim($this->input->post("txt_password",true));
                $chkRem = ($this->input->post('chkRem')== '1')? 'checked' :'';
				$this->form_validation->set_message('required', "* Required Field");
				$this->form_validation->set_message('matches', " %s "."field does not match"." %s");
				
				
				$this->form_validation->set_rules('txt_email', 'Email', 'trim|required'); 
				$this->form_validation->set_rules('txt_password', "Password", 'trim|required');
				
				
				if( !preg_match("/^[_a-z0-9-\+]+(\.[_a-z0-9-\+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", trim($this->input->post('txt_email'))) && $posted["txt_email"] !='' &&  $posted["txt_password"] != '') 
                    {
                             $data['err'] = '* Error in connections...Please check your login email/ password & try again!';
                    }
				
				
				if ($this->form_validation->run() == FALSE || $data['err'] != '')
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
						
						$default_redirect_url = base_url().'admin/dashboard.html';						
						if( $this->input->post('login_referrer')!='' && url_exists(base64_decode($this->input->post('login_referrer'))) ) {
							$redirect_url =  base64_decode($this->input->post('login_referrer')).$hash ;
						}
						
						$pattern = '/[\s]*\'[\s]*(.*)[\s]*\'[\s]*/';
						$login_data['s_email'] = preg_replace($pattern, "$1",$this->db->escape($posted["txt_email"]));
						$login_data['s_password'] = preg_replace($pattern, "$1",$this->db->escape($posted["txt_password"]));
						
						
						$loggedin = $this->admins_user_model->authenticate($login_data);
					#echo $this->db->last_query(); exit;
						if(!empty($loggedin)) 
						{
						   /* start of Remeber me */
						   if($this->input->post('chkRem')=='1' ) 
						   {
							  setcookie("CG[email]", $this->input->post( 'txt_email', true ), time()+2592000, "/");  
							  setcookie("CG[password]", $this->input->post('txt_password', true), time()+2592000, "/"); 
							  setcookie("CG[remember]", 'checked', time()+2592000, "/"); 
							  #var_dump($_COOKIE);exit;
						   }
							else
							{
								setcookie("CG[email]","",time() - 3600, "/");
								setcookie("CG[password]","",time() - 3600, "/");
								setcookie("CG[remember]","",time() - 3600, "/");
							}
							
							/* end of Remenber me */
								$REDIRECT_URL = base_url().'admin/dashboard.html';
								 header("location:".$REDIRECT_URL);               
									  exit;
							
						}
						else 
						{
							////////Display the add form with posted values within it////
							 $this->data["posted"]=$posted;/*don't change*/
							$data['err'] = '* Error in connections...Please check your login email/ password & try again!';
						}
                 
				}
			
			}
             				
			# rendering the view file...
            $view_file = "admin/index.phtml";
            parent::_render($data, $view_file);
		}
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 

	}   

	    # forgot password part...
    public  function forgot_password_ajax()
    {                
        try
        {         
            $arr_messages = array();
            $arr_values = $_POST;

            $MAIL_ID= trim($this->input->post('txt_forgot_email'));

            if( $MAIL_ID=='' ) {
                $err_msg = "* Required Field.";
            }
            else if( !preg_match("/^[_a-z0-9-\+]+(\.[_a-z0-9-\+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", $MAIL_ID) ) {
                $err_msg = '* Invalid Email ID.';
            }
            else
            {
                # check for email-id existence...
                $Query = sprintf("SELECT * FROM cg_admin_user WHERE binary `s_email` = '%s' ", $MAIL_ID);
                $query = $this->db->query($Query);

                if( $query->num_rows() )
                {
                    $row = $query->result_array();
                   // pr($row,1);
                    # retrieving replacable values...
                    $USER_ID = $row[0]['id'];
					$USERNAME = $row[0]['s_name'];
					$EMAIL = $row[0]['s_email'];

                    # new random passowrd...
                  
                    $RANDOM_PASS = $this->admins_user_model->generatePassword();
                    $NEW_PASSWD = $RANDOM_PASS;
                    $replaceArr = array('email' =>  $EMAIL,
                    'name' => $USERNAME,
                    'password' => $NEW_PASSWD);
                }
                else
                {
                    $err_msg = "* Invalid Email ID.";
                }
            }

            if( empty($err_msg) ) 
            {
					$this->load->helper('html');
					$this->load->library('email');
					 $email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
				$this->email->initialize($email_setting);
                # sending mail to the user with the reset password [Start]...
                $this->load->model('mail_contents_model');
                $mail_info = $this->mail_contents_model->get_by_name("forgot_password_en");
                $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);

                $body = sprintf3( $body, $replaceArr);

                $arr['subject'] = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
                $arr['to'] = $MAIL_ID;
                $arr['bcc'] = 'aradhana.online19@gmail.com';
                $arr['from_email'] = 'no-reply@cogttime.coms';
                $arr['from_name'] = 'Team Cogtime';
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
			   
			    # sending mail to the user with the reset password [End]...
                # and finally, updating user-info...
                $pass_arr['s_password'] = get_salted_password($NEW_PASSWD);
                $this->admins_user_model->update($pass_arr,$USER_ID);

                $success_msg = "Password has been sent to your email.";
                echo json_encode( array('result'=>'success', 'msg'=>$success_msg) ); exit;
            }
            else 
            {
                echo json_encode( array('result'=>'failure', 'msg'=>$err_msg) ); exit;
            }
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }                    

    }   //// end of forgot-password function

		
}   // end of controller...