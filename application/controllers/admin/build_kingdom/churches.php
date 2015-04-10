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
* @link model/projects_model.php
* @link views/##
*/

class Churches extends Admin_base_Controller
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
           $this->load->model("church_model");
           $this->load->helper('common_option_helper.php');
		   
		   $this->upload_path = BASEPATH.'../uploads/church_csv/';
           
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    // "index" function definition...
    public function index() 
    {

        try
        {
			
            # adjusting header & footer sections [Start]...
            $data = $this->data;
            parent::_set_title("::: COGTIME Xtian network :::");
            parent::_set_meta_desc("::: COGTIME Xtian network :::");
            parent::_set_meta_keywords("::: COGTIME Xtian network :::");
            parent::_add_js_arr( array( 'js/lightbox.js',
										'js/jquery.form.js',
									    'js/jquery/JSON/json2.js') );
                                        
             parent::_add_css_arr( array('css/dd.css',
                                        ) );
            # adjusting header & footer sections [End]...
			$data['top_menu_selected'] = 6;
			$data['submenu'] = 6;
       
			$this->session->set_userdata('search_condition','');
			
			ob_start();
            $this->ajax_pagination();
            $data['result_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
			
                
            # rendering the view file...
            $VIEW_FILE = "admin/build_kingdom/churches.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }
	
	
	# function to load ajax-pagination [AJAX CALL]...
    public function ajax_pagination($page=0)
    {
        try
        {
			//echo $_POST['search_basic']; exit;
			## seacrh conditions : filter ############
		 	
			 if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) :
                         //echo $srch_date =trim($this->input->post('date_filter'));
                            //echo intval(decrypt(trim($this->input->post('date')))); 
                          //die('ok');
				$WHERE_COND = " WHERE 1  ";
				
				$srch_country = intval(decrypt(trim($this->input->post('srch_country')))); 
				$WHERE_COND .= ($srch_country=='0')?'':" AND ( C.i_country_id  =  '".$srch_country."')";
				
				$srch_state = intval(decrypt(trim($this->input->post('srch_state'))));
				$WHERE_COND .= ($srch_state=='0')?'':" AND ( C.i_state_id =  '".$srch_state."')";
				
				$srch_city = intval(decrypt(trim($this->input->post('srch_city'))));
				$WHERE_COND .= ($srch_city=='0')?'':" AND ( C.i_city_id  =  '".$srch_city."')";
                                 
                                if(trim($this->input->post('date_filter'))!= '')
                                {
                                 $srch_date = get_db_dateformat($this->input->post('date_filter'));
                                 $WHERE_COND .= ($srch_date=='0')?'':" AND ( DATE(C.dt_created_on)  =  '".$srch_date."')";  
                                   // die('ok');
                                } 
                                  //  $srch_date = get_db_dateformat(trim($this->input->post('date'))); 
                                  // $WHERE_COND .= ($srch_date=='0')?'':" AND ( C.dt_created_on  =  '".$srch_date."')";  
                                 //}
//                                 $srch_date = get_db_dateformat(trim($this->input->post('date')));
//                                 $WHERE_COND .= ($srch_date=='0')?'':" AND ( C.dt_created_on  =  '".$srch_date."')";
				//echo $WHERE_COND;
                                //echo $this->db->last_query();
				 $this->session->set_userdata('search_condition',$WHERE_COND);
			
            endif;  
		   	
			$s_where = $this->session->userdata('search_condition'); 
$order_by='`dt_created_on` DESC';
		   	$result = $this->church_model->get_list($s_where,$page,$this->pagination_per_page,$order_by);
            $resultCount = count($result);
			$total_rows = $this->church_model->get_list_count($s_where);
			
			
			
			//pr($result,1);
			#Jquery Pagination Starts
           	$this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/build_kingdom/churches/ajax_pagination";
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
			$this->jquery_pagination->create_links();

            // getting   listing...
			$data['info_arr'] = $result;
			$data['no_of_result'] = $total_rows;
			$data['current_page'] = $page;
          
			# loading the view-part...
          echo  $this->load->view('admin/build_kingdom/churches_list_ajax.phtml', $data,TRUE);
		 }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
    }
	
	
	
	//// function to Delete donation
    public function delete_church($id)
    {
		$i_ret=$this->church_model->delete_by_id($id);
		echo json_encode(array('success'=>true));
		exit;
		
	} // end of Delete banner function...
	 
	 
	 public function add_church()
	 {
		try
		{
			
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
				
				$info['dt_created_on'] = get_db_datetime();
				$_ret = $this->church_model->insert($info);
				
				ob_start();
				$this->ajax_pagination();
				$result_content = ob_get_contents(); //pr($data['result_content']);
				ob_end_clean();
					
				echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'Church Registered Successfully.',
						'html'=>$result_content));
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
	
	
	public function edit_church($id)
	 {
		try
		{
			
			$arr_messages = array();
			
			if($_POST){
				
				  $id= $this->input->post('hd_edit_id'); 
				  # error message trapping...
				  if( trim($this->input->post('txt_edit_name'))=='') 
				  {
						  $arr_messages['edit_name'] = "* Required Field.";
				  }
				  
				  if( trim($this->input->post('txt_edit_phone'))=='') 
				  {
						  $arr_messages['edit_phone'] = "* Required Field.";
				  }
				  
				  if( trim($this->input->post('txt_edit_address'))=='') 
				  {
						  $arr_messages['edit_address'] = "* Required Field.";
				  }
				  
				  if( trim($this->input->post('txt_edit_postcode'))=='') 
				  {
						  $arr_messages['edit_postcode'] = "* Required Field.";
				  }
				  
				  if( trim($this->input->post('sel_edit_country'))=='-1') 
				  {
						  $arr_messages['edit_country'] = "* Required Field.";
				  }
				  
				  if( trim($this->input->post('txt_edit_state'))=='-1') 
				  {
						  $arr_messages['edit_state'] = "* Required Field.";
				  }
				  
				  if( trim($this->input->post('txt_edit_city'))=='-1') 
				  {
						  $arr_messages['edit_city'] = "* Required Field.";
				  }
	  
				 //pr($arr_messages);
				  if( count($arr_messages)==0 ) {
						  
					  $info = array();
					  
					  $info['s_name'] = get_formatted_string($this->input->post('txt_edit_name')); 
					  $info['s_address'] = get_formatted_string($this->input->post('txt_edit_address')); 
					  $info['i_city_id'] = intval(decrypt($this->input->post('txt_edit_city'))); 
					  $info['i_state_id'] = intval(decrypt($this->input->post('txt_edit_state'))); 
					  $info['i_country_id'] = intval(decrypt($this->input->post('sel_edit_country'))); 
					  $info['s_phone'] = get_formatted_string($this->input->post('txt_edit_phone'));
					  $info['s_postcode'] = get_formatted_string($this->input->post('txt_edit_postcode'));
					  
					  $info['dt_updated_on'] = get_db_datetime();
					  $_ret = $this->church_model->update($info, $id);
					  
					  ob_start();
					  $this->ajax_pagination();
					  $result_content = ob_get_contents(); //pr($data['result_content']);
					  ob_end_clean();
						  
					  echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'Church updated Successfully.', 'html'=>$result_content));
				  }
				  else
				  {
					  echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'Error!') );
				  }
		
			}
			else
			{
				$church_info  = $this->church_model->get_by_id($id);
				
				$church_info['s_name'] = get_unformatted_string_edit($church_info['s_name']);
				$where	= " i_country_id='".$church_info["i_country_id"]."'";
            	$church_info['state'] 	= makeOptionState($where, encrypt($church_info["i_state_id"]));
			
				$where1	= " i_state_id='".$church_info["i_state_id"]."'";
                $church_info['city'] 	= makeOptionCity($where1,encrypt($church_info["i_city_id"]));
				$church_info['country']	= encrypt($church_info["i_country_id"]);
				
				echo json_encode( array('success'=>true,'church_info'=>$church_info));

			}
		
		}
		catch(Exception $err_obj)
        {
            
        } 
	}
	
	public function change_status()
	 {
			
			$data = $this->data;
			$page  =  intval($this->input->post('cur_page'));
			$i_status = intval($this->input->post('i_status'));
			$cur_status = intval($this->input->post('cur_status'));
			$ID = $this->input->post('record_id');
			/********************GET ADMIN EMAIL***************************************************/
			$query = $this->db->get_where('cg_admin_user', array('id' => 1));
                        
                            foreach ($query->result() as $row)
                                {
                                   $admin_mail = $row->s_email;
                                }
            
			/****************************************************************************/
                         /**********************GET CHURCH EMAIL**************************************/ 
                                $query1 = $this->db->get_where('cg_church', array('id' => $ID));
                                foreach ($query1->result() as $row)
                                {
                                   $church_mail = $row->ch_email;
                                }
                                
                                /*********************************************/
                                
			if($this->session->userdata('user_id') !="")
			{
				$this->church_model->change_status($i_status,$ID);
				if($i_status==1)
				   {
					 
						$action_txt =
							 '<input name="" title="Approve" type="button" class="btn-06" onclick="javascript:changeStatus(\''.$ID.'\',\'2\',\''.$i_status.'\')"  value="Deny"/>';
                                                /************************mail send if approve**********************************/
                                                 $this->load->library('email');
    $this->load->helper('html');
        $email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
$this->email->initialize($email_setting);
//$body = "<p>Dear User, </p><p>your church request is denied</p><p>Thanks</p><p>admin@cogtime.com</p> ";
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
  	<td style="padding:15px;"><p> Dear Church admin,</p>
<p>Your church has been  approved by admin</p> <p>Now you can create church space </p>
<p>Click on below link , go to the church registration  </p>
<p><a href="'.base_url().'register.html" target="_blank">Click here</a> </p>
<p>Thanks</p>
<p>admin@cogtime.com</p>

            
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
$this->email->from($admin_mail, 'From Cogtime Church');
$this->email->to($church_mail);
//->email->bcc("$mailids");
//$this->email->cc('arif.zisu@gmail.com');
//$this->email->bcc('them@their-example.com');

$this->email->subject('Cogtime church approval');
$this->email->message("$body");

 $this->email->send();	
                                                /***************************************************/
					
				   }
				 else if($i_status==2)
				   {
						$action_txt =
							 '<input name="" title="Deny" type="button" class="btn-06" onclick="javascript:changeStatus(\''.$ID.'\',\'1\',\''.$i_status.'\')"  value="Approve"/>';
                                                 /************************mail send if deny**********************************/
                                                 $this->load->library('email');
    $this->load->helper('html');
        $email_setting  = array('mailtype'=>'html','charset'  => 'utf-8',
                  'priority' => '1');
$this->email->initialize($email_setting);
//$body = "<p>Dear User, </p><p>your church request is denied</p><p>Thanks</p><p>admin@cogtime.com</p> ";
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
  	<td style="padding:15px;"><p> Dear Church admin,</p>
<p>Your church has been  denied by admin</p>
<p>Thanks</p>
<p>admin@cogtime.com</p>

            
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
$this->email->from($admin_mail, 'From Cogtime Church');
$this->email->to($church_mail);
//->email->bcc("$mailids");
//$this->email->cc('arif.zisu@gmail.com');
//$this->email->bcc('them@their-example.com');

$this->email->subject('Cogtime church approval');
$this->email->message("$body");

 $this->email->send();	
                                                /***************************************************/
					
				   } 
			}
			else{
			    
				$SUCCESS_MSG = "An error has occured! please try again. ";
				echo json_encode(array('result'=>false,
                					   'u_id'=>$ID,
									   'action_txt' =>$action_txt,
									   'i_status' => $cur_status,
                					   'msg'=>$SUCCESS_MSG , 'redirect'=>true)); exit;
			}
			
			
			$SUCCESS_MSG = "Status changed successfully! ";
	    
			# view part...
			    ob_start();
                $content = '';
                ob_end_clean();
                
                echo json_encode(array('result'=>'success',
                					   'u_id'=>$ID,
									   'action_txt' =>$action_txt,
									   'i_status' => $cur_status,
                					   'msg'=>$SUCCESS_MSG ,'redirect'=>false));
	 }
	 
     public function import_list()
	 {
		 
		$list			= $_FILES['list_file']['name'];
		$list_ext		= getExtension($list);
		if($list=='')
		{
			$err['list_file']	= "* Required field";
		}
		else if($list_ext!='.csv')
		{
			$err['list_file']	= "* Upload .csv files";
		}
		
		if(count($err)>0)
		{
			echo json_encode(array("success"=>false,"msg"=>$err));
			exit;
		}
		else
		{
			
			
				$filepath = $_FILES['list_file']['tmp_name'];
			
				$row = 1;
				
				if (($handle = fopen($filepath, "r")) !== FALSE) {
					$all_values	= '';
					while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
					{
						$num = count($data);
						if($num!=8)
						{
							$err	= "Please upload proper file format";
							echo json_encode(array("success"=>false,"msg"=>$err));
							exit;
						}
						else if($row==1)
						{
							if($data[0]!='Church Name')
							{
								$err	= "Please upload proper file format";
								echo json_encode(array("success"=>false,"msg"=>$err));
								exit;
							}
							else if($data[1]!='Church Address')
							{
								$err	= "Please upload proper file format";
								echo json_encode(array("success"=>false,"msg"=>$err));
								exit;
							}
							else if($data[2]!='City ID')
							{
								$err	= "Please upload proper file format";
								echo json_encode(array("success"=>false,"msg"=>$err));
								exit;
							}
							else if($data[3]!='State ID')
							{
								$err	= "Please upload proper file format";
								echo json_encode(array("success"=>false,"msg"=>$err));
								exit;
							}
							else if($data[4]!='Country ID')
							{
								$err	= "Please upload proper file format";
								echo json_encode(array("success"=>false,"msg"=>$err));
								exit;
							}
							else if($data[5]!='Postcode')
							{
								$err	= "Please upload proper file format";
								echo json_encode(array("success"=>false,"msg"=>$err));
								exit;
							}
							else if($data[6]!='Phone Number')
							{
								$err	= "Please upload proper file format";
								echo json_encode(array("success"=>false,"msg"=>$err));
								exit;
							}
							else if($data[7]!='Url')
							{
								$err	= "Please upload proper file format";
								echo json_encode(array("success"=>false,"msg"=>$err));
								exit;
							}
						}
						//echo "<p> $num fields in line $row: <br /></p>\n";
						//pr($data);
						if($row>1)
						{
							
							if($data[0]!='')
							{
								
								$values	= "(";
								$name	= '';
								for ($c=0; $c < $num; $c++) {
										$name	= $data[$c];
										$values	.= "'".$data[$c]."',";
										
								}
								$values	.= "'".get_db_datetime()."',";
								$all_values	.= $values;
							}
						}
					
						$row++;
					}
				
					fclose($handle);
				}
				//pr($all_values,1);
				$all_values	= substr($all_values,0,strlen($all_values)-1); 
				$all_values .= " )";
				
				$query = "INSERT INTO 	cg_church (s_name,s_address,i_city_id,i_state_id,i_country_id,
				s_postcode,s_phone,s_url, dt_created_on) VALUES ".$all_values;
				$this->db->query($query);
			    
				fclose($handle);
				ob_start();
				$this->ajax_pagination();
				$result_content = ob_get_contents(); //pr($data['result_content']);
				ob_end_clean();
			//echo $this->db->last_query();	
			echo json_encode(array("success"=>true,"msg"=>$err, 'result_content'=>$result_content));
			exit;
		}
	 }
	 
         public function status($id){
           $this->church_model->changestatus($id);  
           $SUCCESS_MSG = "status change succesfully!";
          
           echo json_encode(array('result'=>'success',
                					   'id'=>$id,
                					   'msg'=>$SUCCESS_MSG ));
             
         } 
         public function status_deny($id)
         {
              $SUCCESS_MSG = "status change succesfully!";
             $this->church_model->deny_status($id);  
              echo json_encode(array('result'=>'success',
                					   'id'=>$id,
                					   'msg'=>$SUCCESS_MSG ));
         }
         public function space_view(){
               $data = $this->data;
            parent::_set_title("::: COGTIME Xtian network :::");
            parent::_set_meta_desc("::: COGTIME Xtian network :::");
            parent::_set_meta_keywords("::: COGTIME Xtian network :::");
            parent::_add_js_arr( array( 'js/lightbox.js',
										'js/jquery.form.js',
									    'js/jquery/JSON/json2.js') );
                                        
             parent::_add_css_arr( array('css/dd.css',
                                        ) );
            # adjusting header & footer sections [End]...
			$data['top_menu_selected'] = 6;
			$data['submenu'] = 7;
                        $this->session->set_userdata('search_condition','');
			
			ob_start();
            $this->ajax_church_space_pagination();
            $data['result_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
               $VIEW_FILE = "admin/build_kingdom/churches_space.phtml";
            parent::_render($data, $VIEW_FILE);
                       // $data['subadmin'] = $this->church_model->get_church_subadmin($id);
                        
         }
         public function ajax_church_space_pagination($page=0)
    {
        try
        {
			//echo $_POST['search_basic']; exit;
			## seacrh conditions : filter ############
		 	$s_where = "where C.ch_space_enable = 1 AND C.ch_page_url != ' '    GROUP BY C.id";
			 if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) :
                         
                                 
                                 $srch_name = preg_replace('/[^A-Za-z0-9\-]/', '', trim($this->input->post('church_name'))); 
				$WHERE_COND .= ($srch_name=='0')?'':" AND ( C.s_name  LIKE '%".$srch_name."%')";
//                               
				 $this->session->set_userdata('search_condition',$WHERE_COND);
			
            endif;  
		   	
			$s_where .= $this->session->userdata('search_condition'); 
               
                         $order_by='`dt_created_on` DESC';
		   	$result = $this->church_model->get_space_list($s_where,$page,$this->pagination_per_page,$order_by);
                         $resultCount = count($result);
			$total_rows = $this->church_model->get_space_list_count($s_where);
			
			
			
			//pr($result,1);
			#Jquery Pagination Starts
           $this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/build_kingdom/churches/ajax_church_space_pagination";
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
           
			$this->jquery_pagination->create_links();

            // getting   listing...
			$data['info_arr'] = $result;
			$data['no_of_result'] = $total_rows;
			$data['current_page'] = $page;
          
			# loading the view-part...
          echo  $this->load->view('admin/build_kingdom/churches_space_list_ajax.phtml', $data,TRUE);
		 }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
    }
    function show_member(){
        $id = $this->input->post('id');
        $query = $this->db->query('select u.id,CONCAT(u.s_first_name,u.s_last_name) as user_name , u.s_profile_photo,u.e_gender from cg_users u , cg_church_member cm where cm.church_id='.$id.' AND cm.is_blocked = 1 AND cm.is_leave = 0 AND cm.member_id = u.id AND cm.is_approved = 1');
        $res = $query->result();
        if(count($res) > 0){
        foreach ($res as $val){
            echo '<ul>';
            echo '<li> <img src="'.get_profile_image_of_user('thumb',$val->s_profile_photo,$val->e_gender).'" title='.$val->user_name.' ><span class="mem_nm">'.$val->user_name.'</span><li>';
            echo '</ul>';
        }
        }else{
            echo '<h1 class="no_mem">No Members</h1>';
        }
    }
    function show_subadmin(){
         $id = $this->input->post('id');
        $query = $this->db->query('select u.id,CONCAT(u.s_first_name,u.s_last_name) as user_name , u.s_profile_photo,u.e_gender from cg_users u , cg_church_member cm where cm.church_id='.$id.' AND cm.is_blocked = 1 AND cm.is_leave = 0 AND cm.member_id = u.id AND cm.is_approved = 1 AND role = 2');
        $res = $query->result();
        if(count($res) > 0){
        foreach ($res as $val){
            echo '<ul>';
            echo '<li> <img src="'.get_profile_image_of_user('thumb',$val->s_profile_photo,$val->e_gender).'" title='.$val->user_name.' ><span class="mem_nm">'.$val->user_name.'</span><li>';
            echo '</ul>';
        }
        }else{
            echo '<h1 class="no_mem">No Subadmin</h1>';
        }
    }
    
    
    public function change_status_space()
    {
       $id = $this->input->post('id');
       $inset_val = $this->input->post('insert_val');
      $data = array(
               'ch_space_enable' => $inset_val
               
            );
      $this->db->where('id', $id);
$this->db->update('cg_church', $data); 
if($inset_val == 0){
    $btnval = "Enable";
    $action_txt ='<input name="" title="Enable" type="button" class="btn-06" onclick="javascript:changeStatus_space(\''.$id.'\',\'1\')"  value="Enable"/>';
}
if($inset_val == 1){
  $btnval = "Disable";
    $action_txt ='<input name="" title="Disable" type="button" class="btn-06" onclick="javascript:changeStatus_space(\''.$id.'\',\'0\')"  value="Disable"/>';  
}
//if($inset_val == 0){
//     $btnval = "Enable";
//    $action_txt ='<input name="" title="Enable" type="button" class="btn-06" onclick="javascript:changeStatus_space(\''.$id.'\',\'0\')"  value="Enable"/>';  
//}

echo json_encode(array('msg'=>'Status change successfully' , 'action_txt'=>$action_txt , 'btnval'=>$btnval));
    }
    
    
    
}   // end of controller...