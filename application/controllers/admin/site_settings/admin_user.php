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

class Admin_user extends Admin_base_Controller
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
           $this->load->model("admin_groups_model");
		    $this->load->model("admins_user_model");
           $this->load->helper('common_option_helper.php');
           
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    // "index" function definition...
    public function index($grp_id) 
    {

        try
        {
            # adjusting header & footer sections [Start]...
            $data = $this->data;
            parent::_set_title("::: COGTIME Xtian network :::");
            parent::_set_meta_desc("::: COGTIME Xtian network :::");
            parent::_set_meta_keywords("::: COGTIME Xtian network :::");
            parent::_add_js_arr( array( 'js/lightbox.js','js/jquery.form.js',
									       'js/jquery/JSON/json2.js',
									     'js/backend/tab_report_abuse.js',
                                         'js/backend/admin_groups.js') );
                                        
            parent::_add_css_arr( array() );
            # adjusting header & footer sections [End]...
			$data['top_menu_selected'] = 1;
			$data['submenu'] = 6;
			$data['grpid'] =  $grp_id;
			
			$data['grp_arr'] = $this->admin_groups_model->get_by_id($grp_id);
			         
            
			// fetching data
			$WHERE_COND = " ";
			$this->session->set_userdata('search_condition',$WHERE_COND);
			$page=0;
			$order_by = " `id` DESC ";
			
			ob_start();
            $this->ajax_pagination($grp_id);
            $data['result_content'] = ob_get_contents(); //pr($data['result_content'],1);
            ob_end_clean();
			
             #pr($data,1);
            # rendering the view file...
            $VIEW_FILE = "admin/site_settings/admin_user/admin_groups.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }

     
	
	  # function to load ajax-pagination [AJAX CALL]...
    public function ajax_pagination($grp_id, $page=0)
    {
        try
        {
			## seacrh conditions : filter ############
		 	
			 if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) :
				
				$txt_name = get_formatted_string(trim($this->input->post('txt_name')));
				$WHERE_COND .= ($txt_name=='')?'':" AND (U.s_name LIKE '%".$txt_name."%')";
				
				$txt_email = trim($this->input->post('txt_email'));
				$WHERE_COND .= ($txt_email=='')?'':" AND (U.s_email  = '".$txt_email."')";
				
				$category = $this->input->post('category');
				$WHERE_COND .= ($category =='-1')?'':" AND (G.id =".$category." )";
				
				
				
				$this->session->set_userdata('search_condition',$WHERE_COND);
			    $s_where = $this->session->userdata('search_condition');
		   else:
		   		$s_where = " AND U.i_id_admin_user_group = {$grp_id} ";
			     #echo "testing.."; exit;
           endif;  
		   	
			
			$order_by = " `id` DESC ";
		   	$result = $this->admin_groups_model->admin_user_list_by_grpID($s_where,$page,$this->pagination_per_page,$order_by);
			
			
            $resultCount = count($result);
		   #echo $this->db->last_query(); exit;
           #pr($result,1);
		   $total_rows = $this->admin_groups_model->admin_user_count_by_grpID($s_where);
			
		
			## end seacrh conditions : filter ############
			
			//pr($result,1);
			#Jquery Pagination Starts
           	$this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/site_settings/admin_user/ajax_pagination/".$grp_id;
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

            $config['div'] = '#table_content'; /* Here #content is the CSS selector for target DIV */
            $config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
            $config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */

            $this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();

            // getting   listing...
			$data['info_arr'] = $result;
			$data['no_of_result'] = $total_rows;
			$data['current_page'] = $page;
          
			# loading the view-part...
          echo  $this->load->view('admin/site_settings/admin_user/admin_groups_ajax.phtml', $data,TRUE);
		 }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
    }
	
	
	   public function add($grp_id)
	   {
		  try
			{
			  $arr_messages = array();
					
				if($_POST){
					  # error message trapping...

						  
				      if(trim($this->input->post('txt_g_email')==''))
				      {		
                              $arr_messages['g_email'] = "* Required Field";
				      }
					 else if( !preg_match("/^[_a-z0-9-\+]+(\.[_a-z0-9-\+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", trim($this->input->post('txt_g_email'))) ) 
					  {
							   $arr_messages['g_email'] = 'Invalid Email ID';
					  }		
					  else if($this->admins_user_model->email_exists(trim($this->input->post('txt_g_email'))))
					  {
						  $arr_messages['g_email'] = 'Email ID already exists';
					  }
					  
					  
					  
					  if(trim($this->input->post('txt_name')==''))
				      {		
                              $arr_messages['name'] = "* Required Field";
				      }			
					  
					  if(trim($this->input->post('category')=='-1'))
				      {		
                              $arr_messages['category'] = "* Required Field";
				      }	
					  if(trim($this->input->post('txt_g_password')==''))
				      {		
                              $arr_messages['g_password'] = "* Required Field";
				      }	
					  
					  
					  		
				

					  if( count($arr_messages)==0 ) {
						  
						  	
							 $info = array();
							 $info['s_name'] = get_formatted_string($this->input->post('txt_name'));
							 $info['s_email'] = $this->input->post('txt_g_email');	
							 $info['i_id_admin_user_group'] = $this->input->post('category');	
							 $info['s_password'] = get_salted_password($this->input->post('txt_g_password'));	 	 
							 $info['dt_created_on'] = get_db_datetime();
							 $_ret = $this->admins_user_model->insert($info);
							 
							ob_start();
							$this->ajax_pagination($grp_id);
							$html = ob_get_contents(); //pr($data['result_content'],1);
							ob_end_clean();
							
							
							#### send mail to added user
							//EMAIL SENDING CODE.[start]
							$this->load->helper('html');
					$this->load->library('email');
					 $email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
				$this->email->initialize($email_setting);
							  $this->load->model('mail_contents_model');
							  $mail_info = $this->mail_contents_model->get_by_name("subadmin_registration");
							  $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
							  
								$body = sprintf3( $body, array('email'=>$info["s_email"],
										 'password'=>$this->input->post('txt_g_password')) );
							  
							  $arr['subject'] 	= htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
							  $arr['to']         =  $info['s_email'];
							  $arr['bcc']        = 'aradhana.online19@gmail.com';
							  $arr['from_email'] = 'no-reply@cogtime.com';
							  $arr['from_name'] = 'admin@cogtime.com';
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
							   
							 //EMAIL SENDING CODE.[end]
							
							
						  
						   echo json_encode( array('success'=>true,'html'=>$html,'msg'=>'New user added successfully.') );
					  }
					  else
					  {
						  echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'Error!') );
					  }
			
				}
				
			}
			  catch(Exception $err_obj)
			  {
				  show_error($err_obj->getMessage());
			  }
		}
		
		
	   public function edit($id)
	   {
			 
              try
			  {               
                #echo "testing : ".$id; exit;
				$arr_messages = array();
				if($_POST){
				
					  //$userid= intval($this->input->post('i_edit_id'));
				      if(trim($this->input->post('txt_edit_email')==''))
				      {		
                              $arr_messages['edit_email'] = "* Required Field";
				      }
					 else if( !preg_match("/^[_a-z0-9-\+]+(\.[_a-z0-9-\+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", trim($this->input->post('txt_edit_email'))) ) 
					  {
							  $arr_messages['edit_email'] = 'Invalid Email ID';
					  }	
					  else if(($this->admins_user_model->email_exists(trim($this->input->post('txt_edit_email')),$id)))
					  {
						  $arr_messages['edit_email'] = 'Email ID already exists';
					  }
					  
					  
					  
					  	
					  
					  if(trim($this->input->post('txt_edit_name')==''))
				      {		
                              $arr_messages['edit_name'] = "* Required Field";
				      }			
					  
					  if(trim($this->input->post('edit_category')=='-1'))
				      {		
                              $arr_messages['edit_category'] = "* Required Field";
				      }	
					
					  
					  		
				

					  if( count($arr_messages)==0 ) {
						  
						  	
							 $info = array();
							 $info['s_name'] = get_formatted_string($this->input->post('txt_edit_name'));
							 $info['s_email'] = $this->input->post('txt_edit_email');	
							 $info['i_id_admin_user_group'] = $this->input->post('edit_category');	
							 $info['dt_updated_on'] = get_db_datetime();
							 $_ret = $this->admins_user_model->update_user($info, $id);
							 
							$arr = $this->admins_user_model->get_admin_by_id($id) ; //pr($arr,1);
							ob_start();
							$this->ajax_pagination($arr[0]['i_id_admin_user_group']);
							$html = ob_get_contents(); //pr($data['result_content'],1);
							ob_end_clean();
						  
						   echo json_encode( array('success'=>true,'html'=>$html,'msg'=>'User updated successfully.') );
					  }
					  else
					  {
						  echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'Error!') );
					  }
			  
				  }else
				  {
					  
					    $arr = $this->admins_user_model->get_admin_by_id($id) ;
						//pr($arr);
						$html  = ' <div class="lable01">ADMIN ID :</div>
                                  <div class="field01">
                                        <input name="txt_edit_email" type="text" value="'.$arr[0]['s_email'].'"/>
                                  </div>
                                  <div class="error-message" id="err_edit_email"></div>
                                  <div class="clr"></div>
                                  
                                  <div class="lable01">Name :</div>
                                  <div class="field01">
                                        <input name="txt_edit_name" type="text" value="'.$arr[0]['s_name'].'" />
                                  </div>
                                  <div class="error-message" id="err_edit_name"></div>
                                  <div class="clr"></div>
                                  
                                  
                                  <div class="lable01">Select Group  :</div>
                                  <div class="field01" style="margin-bottom:8px;">
                                        <select name="edit_category" id="category2" style="width:348px;">
                                                <option value ="-1">select</option>
                                                '.makeAdminGroup('',$arr[0]['i_id_admin_user_group']).'
                                          </select>
                                       
                                  </div>
                                  <div class="error-message" id="err_edit_category"></div>
                                  <div class="clr"></div>
                                  
                                  <div class="lable01"> </div>
                                  <div class="field01">
                                        <input name="" type="submit" class="btn"  value="Update"/>
                                        <input name="" type="reset" class="btn"  value="Cancel" onclick="clear_box(\'edit\'); hide_dialog();"/>
                                  </div>';
                        #pr($data['posted']);
						echo json_encode( array('result'=>success,'html'=>$html) );	  

				  }
				  
			  }
			  catch(Exception $err_obj)
			  {
				  show_error($err_obj->getMessage());
			  }
		}
        
		
		
	 public function reset_password()
	 {
			
			$data = $this->data;
			$ID = $this->input->post('record_id');
			$RANDOM_PASS = $this->admins_user_model->generatePassword();
			
			## fetchin users detail for mail
			$user_info = $this->admins_user_model->get_admin_by_id($ID);
			#pr($user_info ,1);
			 
			$USERNAME = $user_info['s_name'];
			$EMAIL = $user_info['s_email'];
			
			$NEW_PASSWD = $RANDOM_PASS;
			$replaceArr = array('email' =>  $EMAIL,
			'name' => $USERNAME,
			'password' => $NEW_PASSWD);
			
			## end
			
			## fetch admin details for mail
			$super_admin_details = $this->admins_user_model->get_admin_by_id(1);
			
			$admin_id = $super_admin_details[0]['id'] ;
			$admin_name = $super_admin_details[0]['s_name'] ;
			
			$replaceArr_admin = array('username'=>$USERNAME,'email' =>  $EMAIL,
			'name' => $admin_name,
			'password' => $NEW_PASSWD);
			
			if($this->session->userdata('user_id') !="")
			{
				//echo $NEW_PASSWD;
				 $pass_arr['s_password'] = get_salted_password($NEW_PASSWD); 
                               //  pr($pass_arr,1);
				 $this->admins_user_model->update($pass_arr,$ID);
				echo 'acumen'.$NEW_PASSWD; die('ok');
				## sending mail to the user and super-admin .. key individual_password_reset_user
				
				 ##user ##
					$this->load->helper('html');
					$this->load->library('email');
					 $email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
				$this->email->initialize($email_setting);				 
				  $this->load->model('mail_contents_model');
				  $mail_info = $this->mail_contents_model->get_by_name("individual_password_reset_user");
				  $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
  
				  $body = sprintf3( $body, $replaceArr);
  
				  $arr['subject'] = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
				  $arr['to'] = $MAIL_ID;
				  $arr['from_email'] = 'no-reply@cogtime.com';
				  $arr['from_name'] = 'Team Cogtime';
				  $arr['message'] = $body;
				 //dump($arr);
				$this->email->from( $arr['from_email'], $arr['from_name']);
                #dump($arr);
				$this->email->subject($arr['subject']);
						
				$this->email->to($arr['to']);
				$this->email->bcc($arr['bcc']);
				$this->email->message("$body");
                        //send_mail($arr);
				$this->email->send();
				  //send_mail($arr);
				
				 ## admin mail ##
				 
				  $mail_info = $this->mail_contents_model->get_by_name("individual_password_reset_admin");
				  $body = htmlspecialchars_decode($mail_info['body'], ENT_QUOTES);
  
				  $body = sprintf3( $body, $replaceArr_admin);
  
				  $arr_admin['subject'] = htmlspecialchars_decode($mail_info['subject'], ENT_QUOTES);
				  $arr_admin['to'] = $MAIL_ID;
				  $arr_admin['from_email'] = 'no-reply@cogtime.com';
				  $arr_admin['from_name'] = 'Team Cogtime';
				  $arr_admin['message'] = $body;
				 //dump($arr_admin); exit;
				 $this->email->from( $arr_admin['from_email'], $arr_admin['from_name']);
                #dump($arr);
				$this->email->subject($arr_admin['subject']);
						
				$this->email->to($arr_admin['to']);
				$this->email->bcc($arr_admin['bcc']);
				$this->email->message("$body");
                       
				$this->email->send();
				  //send_mail($arr_admin);
				
			}
			else{
			    
				$SUCCESS_MSG = "An error has occured! please try again. ";
				echo json_encode(array('result'=>false,
                					   'u_id'=>$ID,
                					   'msg'=>$SUCCESS_MSG , 'redirect'=>true)); exit;
			}
			
			
			$SUCCESS_MSG = "Password changed Successfully!";
            echo json_encode(array('result'=>'success',
                					   'u_id'=>$ID,
                					   'msg'=>$SUCCESS_MSG ,'redirect'=>false));
	 } 
	 
	 
	function change_status()
    {
        $id = $this->input->post('id');
        $current_status = $this->input->post('status');
        $data['i_status'] = 3 - $current_status;

        $this->admins_user_model->update_user($data, $id);
        
		$status = ( $data['i_status'] == 2)?'Enable':'Disable';
		$action_html =  '<input name="" title="'.$status.'" type="button" class="btn-03"  value="'.$status.'" status="'.$data['i_status'].'" onclick="return change_status('.$id.','.$data['i_status'].')"/>';
        
		echo json_encode(array('status'=>$data['i_status'], 'action_html'=>$action_html));
        
    }
    
    function delete()
    {
        $id = $this->input->post('id');
       
		
		$arr = $this->admins_user_model->get_admin_by_id($id) ; //pr($arr,1);
		$this->admins_user_model->delete_by_id($id);
		ob_start();
		$this->ajax_pagination($arr[0]['i_id_admin_user_group']);
		$html = ob_get_contents(); //pr($data['result_content'],1);
		ob_end_clean();
        
        echo json_encode(array('html'=>$html));
		exit;
    }
    
}   // end of controller...