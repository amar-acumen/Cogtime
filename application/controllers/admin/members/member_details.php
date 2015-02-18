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

class Member_details extends Admin_base_Controller
{
    private $pagination_per_page=10;
	
	 // Error Messages for Required Fields...
    private $required_fields = array('title','day', 'month','year','txt_lname','txt_fname','txt_country','txt_website',
									'txt_profile_url',
	                                );
	private $required_basic_fields = array('txt_lang', 'txt_about',
                                     'txt_cname', 'txt_caddress', 'txt_ccity', 'txt_cstate','txt_ccountry',
                                     'txt_cpostcode', 'txt_cphone', 'txt_denomination'
	                                );
    private $required_accnt_fields    =   array('txt_email'
                                            );

    

    // constructor definition...
    function __construct()
    {
        try
        {
            parent::__construct();
            parent::_check_admin_login();            
            # configuring paths...
            $this->upload_path = BASEPATH.'../uploads/user_profile_image/';
            # loading reqired model & helpers...
            // $this->load->helper('###');
           $this->load->model("users_model");
           $this->load->model("education_model");
           $this->load->model("work_model");    
           $this->load->model("skill_model");    
           $this->load->helper('common_option_helper.php');
           
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    // "index" function definition...
    public function index($id, $page_no='0') 
    {
        
        try
        {
            # adjusting header & footer sections [Start]...
            $data = $this->data;
            parent::_set_title("::: COGTIME Xtian network :::");
            parent::_set_meta_desc("::: COGTIME Xtian network :::");
            parent::_set_meta_keywords("::: COGTIME Xtian network :::");
            parent::_add_js_arr( array( 'js/jquery.dd.js'=>'header',
                                        'js/lightbox.js'=>'header',
                                         'js/ModalDialog.js'=>'header',
                                         'js/jquery-ui-1.8.2.custom.min.js'=>'header',
                                         //'js/tab.js'=>'header',   --commented@13.02.13
										 'js/jquery.form.js'=>'header',
										 'js/jquery/JSON/json2.js'=>'header',
										 'js/backend/members/manage_my_profile.js'=>'header',
										 'js/backend/members/member_details_utility.js'=>'header',
                                         
                                         //// NEW JS CALLS...
                                         'js/backend/members/member_misc_utility.js'=>'header',
                                         'js/base64_decode.js'=>'header'
                                          ) );
                                        
            parent::_add_css_arr( array('css/dd.css') );
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 2;
            $data['submenu'] = 1;
         
            
            // fetching data
            $WHERE_COND = " WHERE 1 AND id={$id}";
            $data['result'] = $this->users_model->fetch_this($id);
			
           	$where	= " i_country_id='".$data['result']["i_country_id"]."'";
            $data['state'] 	= makeOptionState($where, encrypt($data['result']["i_state_id"]));
			$where1	= " i_state_id='".$data['result']["i_state_id"]."'";
            $data['city'] 	= makeOptionCity($where1,encrypt($data['result']["i_city_id"]));
          // pr($data['result']);
            // page no
            $data['page_no']=$page_no;
            
            ///// FEW PREREQUISITES [BEGIN] /////
                $data['selected_user_id'] = $id;
            ///// FEW PREREQUISITES [END] /////
           
            
             #pr($data,1);
            # rendering the view file...
            $VIEW_FILE = "admin/members/member_details.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }
	
	# modify member profile by ajax.
	public function modify_profile_personal_info_ajax($id)
	{
       
        $user_id = intval($id);
        // 1st, adjusting err-messages part accordingly...
        $arr_messages = array();
        $arr_values = $_POST;
		$err_flag = 0;

        // After form submission...
        if( $this->input->post('is_submitted', true)!="" ) {

			
			$posted["title"]=ucfirst(trim($this->input->post("title")));
			$posted["txt_lname"]=ucfirst(trim($this->input->post("txt_lname")));
			$posted["txt_fname"]=ucfirst(trim($this->input->post("txt_fname")));
			
			$posted["day"]=trim($this->input->post("day"));
			$posted["month"]=trim($this->input->post("month"));
			$posted["year"]=trim($this->input->post("year"));
			
			$posted["txt_phone"]		=trim($this->input->post("txt_phone"));
			$posted["time-zone"]		=trim($this->input->post("time-zone"));
			
            $posted["txt_email"]		=trim($this->input->post("txt_email"));
			$posted["txt_city"]		=intval(decrypt(trim($this->input->post("txt_city"))));
			$posted["txt_state"]	=intval(decrypt(trim($this->input->post("txt_state"))));
			$posted["txt_country"]	=intval(decrypt(trim($this->input->post("txt_country"))));
            #$posted["txt_phone"]  = trim($this->input->post("txt_phone"));
			
			$posted["txt_website"]	=prep_url(trim($this->input->post("txt_website")));
			
			$posted["txt_profile_url"]		=(trim($this->input->post("txt_profile_url")));
			$posted["txt_net_pal"]			=(trim($this->input->post("txt_net_pal")));
			$posted["txt_prayer_ptnr"]		=(trim($this->input->post("txt_prayer_ptnr")));
			
			
			
			if( trim($this->input->post('title'))=='-1') 
			{
					$arr_messages['title'] = "* Required Field.";
			}
			if( trim($this->input->post('txt_fname'))=='') 
			{
					$arr_messages['fname'] = "* Required Field.";
			}
			
			if( trim($this->input->post('txt_lname'))=='') 
			{
					$arr_messages['lname'] = "* Required Field.";
			}
			
			
			if(trim($this->input->post('txt_website')) != '' && !isValidURL(trim($this->input->post('txt_website'))))
			{
				$arr_messages['website'] = "* Not a valid url";
			}
			
			if( trim($this->input->post('txt_profile_url'))=='') 
			{
					$arr_messages['profile_url'] = "* Required Field.";
			}
			
			if($posted["txt_country"] =='0') 
			{ 
					$arr_messages['country'] = "* Required Field.";
			}
			
			if($posted["txt_state"] =='0') 
			{ 
					$arr_messages['state'] = "* Required Field.";
			}
			if($posted["txt_city"] =='0') 
			{ 
					$arr_messages['city'] = "* Required Field.";
			}
			
			
			if($posted["txt_email"] =='') 
			{
				 $arr_messages['email'] = "* Required Field.";
			}
			else if($posted["txt_email"] !='' && !preg_match("/^[_a-z0-9-\+]+(\.[_a-z0-9-\+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", $posted["txt_email"]) ) 
			{
					$arr_messages['email'] = "* Invalid Email-Id.";
			}
			
			
			/*if($this->input->post("day") == '-1')
			{
				$arr_messages['day'] = "* Required Field.";
			}
			*/
			if($this->input->post("day") == '-1' || $this->input->post("month") == '-1'  ||$this->input->post("year") == '-1')
			{
				$arr_messages['month'] = "* Required Field";
			}
			
			/*if($this->input->post("year") == '-1')
			{
				$arr_messages['year'] = "* Required Field";
			}*/
			
			if($this->users_model->profile_url_suffix_exists($posted["txt_profile_url"] , $user_id)){
				$arr_messages['profile_url'] = "* Not available, Please try another name.";
			}
			
            

           
			if (count($arr_messages) !=0 ) 
			{
				/*# forming form-error array...
				$err_arr = array();
				foreach($this->required_fields as $key=>$val) {
					$err_arr[$val] = form_error($val);
					
				}
				
					//pr($err_arr);*/
				$data['arr_messages'] = $arr_messages;
				echo json_encode( array('result'=>error,'arr_messages'=>$arr_messages) );
				///echo json_encode(array('result'=>'error','arr_messages'=>$err_arr));
				exit;
			}
			else
			{
				
				$info['e_title'] = get_formatted_string($posted['title']);
				$info['s_first_name'] = get_formatted_string($posted['txt_fname']);
				$info['s_last_name'] = get_formatted_string($posted['txt_lname']);
				//$info['e_gender'] = (ucfirst($posted["txt_gender"]) == 'Female')?'F':'M';
				
				$info['dt_dob'] = date('Y-m-d',mktime(0,0,0,$posted["month"],$posted["day"],$posted["year"]));
				
				$info['s_email'] = get_formatted_string($posted['txt_email']);     
			    $info['s_moblie_no'] = get_formatted_string($posted['txt_phone']);
				$info['s_time'] = get_formatted_string($posted['time-zone']);    
				
				          
				$info['i_city_id'] = $posted['txt_city'];
				$info['i_state_id'] = $posted['txt_state'];
				$info['i_country_id'] = $posted['txt_country'];
				//$info['e_want_net_pal'] = ($posted["txt_net_pal"]== 'Yes')?'Y':'N';  --commented@12.02.13
                $info['e_want_net_pal'] = $posted["txt_net_pal"];
				$info['s_website'] = $posted["txt_website"];
				//$info['e_want_prayer_partner'] = ($posted["txt_prayer_ptnr"] == 'Yes')?'Y':'N';  --commented@12.02.13
                $info['e_want_prayer_partner'] = $posted["txt_prayer_ptnr"];
				$info['s_profile_url_suffix'] = get_formatted_string($posted["txt_profile_url"]);
				//$info['s_profile_photo'] = $this->_upload_profile_image();
				$info['dt_updated_on'] = get_db_datetime();
				
				 #dump($info);

				# modify user-profile info...
				$this->users_model->edit_info($info, $user_id);
                
                
                
                //////////////////////// TO LOAD THE AJAX RESPONSE [START] ////////////////////////
                // fetching data
                $ajax_data['result'] = $this->users_model->fetch_this($user_id);
                ob_start();
                    $AJAX_FIEW_FILE = "admin/members/ajax/member_personal_info_AJAX.phtml";
                    $this->load->view($AJAX_FIEW_FILE, $ajax_data);
                    $AJAX_CONTENT = ob_get_contents();
                ob_end_clean();
                
                // fetching form-content data...
                ob_start();
                    $AJAX_VIEW_FILE = "admin/members/ajax/member_personal_info_form_AJAX.phtml";
                    $this->load->view($AJAX_VIEW_FILE, $ajax_data);
                    $AJAX_FORM_CONTENT = ob_get_contents();
                ob_end_clean();
                
            //////////////////////// TO LOAD THE AJAX RESPONSE [END] ////////////////////////
                 $SUCCESS_MSG = "Account updated successfully.";
                      
                      echo json_encode(array('result'=>'success',
                                             'content'=>base64_encode($AJAX_CONTENT),
                                             'form_content'=>base64_encode($AJAX_FORM_CONTENT),
                                             'redirect'=>$REDIRECT,
                                             'msg'=>$SUCCESS_MSG));
                      exit;
                
                
                
				$REDIRECT = admin_base_url()."members/member-details/index/".$user_id; 
			//echo $this->db->last_query(); exit;
				$SUCCESS_MSG = "Account updated successfully";
				
				//echo json_encode(array('result'=>'success','redirect'=>$REDIRECT,'msg'=>$SUCCESS_MSG));
				 echo json_encode( array('result'=>'success','redirect'=>$REDIRECT ,'arr_messages'=>$arr_messages, 'msg'=>$SUCCESS_MSG) );
				exit;
			}
		
        }   // check if submitted...

        
	} 
	
	
	
	public function modify_my_profile_basic_info_ajax($id)
	{
        # logged user-id...
        $user_id = intval($id);
        // 1st, adjusting err-messages part accordingly...
        $arr_messages = array();
        $arr_values = $_POST;
		$err_flag = 0;
		
        // After form submission...
        if( $this->input->post('is_basic_submitted', true)!="" ) {

			
			$posted["txt_lang"]=trim($this->input->post("txt_lang"));
			$posted["txt_about"]=trim($this->input->post("txt_about"));
			$posted["txt_cname"]=(trim($this->input->post("txt_cname")));
			$posted["txt_caddress"]=(trim($this->input->post("txt_caddress")));
			$posted["txt_ccity"]=(trim($this->input->post("txt_ccity")));
			
			$posted["txt_cstate"]=trim($this->input->post("txt_cstate"));
			$posted["txt_ccountry"]=intval(decrypt(trim($this->input->post("txt_ccountry"))));
			$posted["txt_cpostcode"]=trim($this->input->post("txt_cpostcode"));
			
			$posted["txt_cphone"]=trim($this->input->post("txt_cphone"));
			            
			$posted["txt_denomination"]	 =intval(decrypt(($this->input->post("txt_denomination"))));
			
			
			#pr($posted); 
						
			$this->form_validation->set_message('required', "* Required fields");
			$this->form_validation->set_message('valid_email', "must contain a valid email address.");
			$this->form_validation->set_message('matches', " %s "."field does not match"." %s");
			
			
			/*$this->form_validation->set_rules('txt_lang', " Languages", 'trim|required'); 
				
			$this->form_validation->set_rules('txt_about', "About me", 'trim|required');
			$this->form_validation->set_rules('txt_cname', "Church name", 'trim|required');
			$this->form_validation->set_rules('txt_caddress', "Church address", 'trim|required');
			$this->form_validation->set_rules('txt_ccity', "Church city", 'trim|required');
			
			$this->form_validation->set_rules('txt_cstate', 'Church State', 'trim|required'); 
			$this->form_validation->set_rules('txt_ccountry', 'Church Country', 'required'); 
			$this->form_validation->set_rules('txt_cpostcode', 'Church postcode', 'trim|required'); 
			
			$this->form_validation->set_rules('txt_cphone', 'Church Phone number', 'trim|required'); 
			$this->form_validation->set_rules('txt_denomination', "Church denomination", 'trim|required|callback_valid_denomination');*/
			
			
				
			
			
			
			/*if ($this->form_validation->run() == FALSE ) 
			{
				# forming form-error array...
				$err_arr = array();
				foreach($this->required_basic_fields as $key=>$val) {
					$err_arr[$val] = form_error($val);
				}
					
				
				echo json_encode(array('result'=>'error',
									   'arr_messages'=>$err_arr));
				exit;
			}
			else*/
			{
				
				$info['s_languages'] = get_formatted_string($posted['txt_lang']);
				$info['s_about_me'] = get_formatted_string($posted['txt_about']);
				$info['s_church_name'] = get_formatted_string($posted['txt_cname']);
				$info['s_church_address']     = get_formatted_string($posted["txt_caddress"]);
				
		                
				$info['s_church_city'] = get_formatted_string($posted['txt_ccity']);
				$info['s_church_state'] = get_formatted_string($posted['txt_cstate']);
				$info['i_church_country_id'] = $posted['txt_ccountry'];
				
				$info['s_church_phone'] = $posted["txt_cphone"];
				$info['s_church_postcode'] = get_formatted_string($posted["txt_cpostcode"]);
				$info['i_id_denomination'] = ($posted["txt_denomination"]);
				$info['dt_updated_on'] = get_db_datetime();
				
				 #pr($info);

				# modify user-profile info...
				$this->users_model->edit_info($info, $user_id);
                 //echo $this->db->last_query();
                
                //////////////////////// TO LOAD THE AJAX RESPONSE [START] ////////////////////////
                // fetching data
                $ajax_data['result'] = $this->users_model->fetch_this($user_id);
                ob_start();
                    $AJAX_FIEW_FILE = "admin/members/ajax/member_basic_info_AJAX.phtml";
                    $this->load->view($AJAX_FIEW_FILE, $ajax_data);
                    $AJAX_CONTENT = ob_get_contents();
                ob_end_clean();
                
                // fetching form-content data...
                ob_start();
                    $AJAX_VIEW_FILE = "admin/members/ajax/mamber_basic_info_form_AJAX.phtml";
                    $this->load->view($AJAX_VIEW_FILE, $ajax_data);
                    $AJAX_FORM_CONTENT = ob_get_contents();
                ob_end_clean();
                
            //////////////////////// TO LOAD THE AJAX RESPONSE [END] ////////////////////////
                
                 $SUCCESS_MSG = "Account updated successfully.";
                      
                      echo json_encode(array('result'=>'success',
                                             'content'=>base64_encode($AJAX_CONTENT),
                                             'form_content'=>base64_encode($AJAX_FORM_CONTENT),
                                             'redirect'=>$REDIRECT,
                                             'msg'=>$SUCCESS_MSG));
                      exit;
                      
                      
                      
				
				$REDIRECT = admin_base_url()."members/member-details/index/".$user_id; 
				$SUCCESS_MSG = "Account updated successfully";
				
				echo json_encode(array('result'=>'success',
									   'redirect'=>$REDIRECT,
									   'msg'=>$SUCCESS_MSG));
				exit;
			}
		
        }   // check if submitted...

        
	} 
    
    
//========================================= education section ====================================================       
    public function modify_my_profile_edu_info_ajax($id)
    {
        # logged user-id...
        $user_id = intval( $id ); 
        // 1st, adjusting err-messages part accordingly...
        $arr_messages = array();
        $arr_values = $_POST;
        $err_flag = 0;
        
        // After form submission...
        if( $_POST ) {
            
            $DELETED_IDS_ARR = array();
            $ids_to_be_deleted = $this->input->post("deleted_edu_ids");
            
            if( !empty($ids_to_be_deleted) )
                $DELETED_IDS_ARR = explode('#', $ids_to_be_deleted);
            
            $arr_institute_name     =   array();
            $arr_institute_city     =   array();
            $arr_institute_state    =   array();
            $arr_institute_country  =   array();
            $arr_institute_yrar     =   array();
            $arr_institute_degree   =   array();
            
            $arr_institute_name     = $this->input->post("txt_school_name");
            $arr_institute_city     = $this->input->post("txt_school_city");
            $arr_institute_state    = $this->input->post("txt_school_state");
            $arr_institute_country  = $this->input->post("txt_school_country");
            $arr_institute_year     = $this->input->post("txt_school_year");
            $arr_institute_degree   = $this->input->post("txt_school_degree");
            $arr_db_id              = $this->input->post("db_id");
           
            #$total_divs=$this->input->post('total_edu_divs');
            $total_divs=count( $arr_institute_name );
            $info = array();
            #echo $total_divs;exit;
            
            #dump( $DELETED_IDS_ARR ); exit;
            for($i=0;$i<$total_divs;$i++)
            {
                
                ## CHECKING BLANK ARRAY
                if($arr_institute_name[$i] !='' || $arr_institute_city[$i] != '' || $arr_institute_state[$i] != '' || $arr_institute_country[$i] != ''  || $arr_institute_year[$i] != '' || $arr_institute_degree[$i] !='')
                {
                    
                    if( !empty($DELETED_IDS_ARR) && in_array($arr_db_id[$i], $DELETED_IDS_ARR) ) {
                        #echo "Delete : ". $arr_db_id[$i] ."\r\n";
                        
                        $this->education_model->delete_info_db($arr_db_id[$i]);
                    } else {
                        
                        $info['s_school_name']    =   get_formatted_string($arr_institute_name[$i]);
                        $info['s_school_city']    =   get_formatted_string($arr_institute_city[$i]);
                        $info['s_school_state']   =   get_formatted_string($arr_institute_state[$i]);
                        $info['s_school_country'] =   intval(decrypt(trim($arr_institute_country[$i]))); 
                        $info['i_class_year']     =   get_formatted_string($arr_institute_year[$i]);
                        $info['s_degree']         =   get_formatted_string($arr_institute_degree[$i]);
                        $info['i_user_id']        =   $user_id;
                        
                        
                        //echo $info['s_school_country'];
                        #dump( $info );
                        # modify user-profile info...
                        $result = $this->education_model->edit_edu_info($info, $user_id,$arr_db_id[$i]);
                        #echo $this->db->last_query().' ==  ';  exit;
                        #pr($result,1);
                        
                    }
                    
                }   // end of 'if any field in a div exists
                else
                {
                    $result=$this->education_model->delete_info_db($arr_db_id[$i])  ; 
                }
                    
                
            }
            
            
                      $SUCCESS_MSG = "Account updated successfully.";
                      
                      echo json_encode(array('result'=>'success',
                                             'msg'=>$SUCCESS_MSG));
                      exit;
                 
          
        
        }   // check if submitted...
        
              $REDIRECT = admin_base_url()."members/member-details/index/".$user_id;
              $SUCCESS_MSG = "Sorry! update not successful.";
              
              echo json_encode(array('result'=>'failure',
                                     'redirect'=>$REDIRECT,
                                     'msg'=>$SUCCESS_MSG));
              exit;

    }
      
    
     public function delete_edu_info()
    {
        $db_id=$this->input->post('db_id');
        
        if($db_id)       
        {    
            $result=$this->education_model->delete_info_db($db_id);
        }
        else
        {
            echo json_encode(array(
                                    'result'    =>  'failure',
                                    'msg'       =>  'Removed.'
                            ));
            exit;
        }
            
        
        if($result)
            echo json_encode(array(
                                    'result'=>'success',
                                    'msg'=>'Successfully deleted.'
                            ));
        else
            echo json_encode(array(
                                    'result'=>'failure',
                                    'msg'   =>'Sorry. Can not be deleted.'
                            ));
       
    }
    
    
    
    
    
//========================================= end of education section ==================================================== 

//========================================= work section ====================================================       
    public function modify_my_profile_work_info_ajax($id)
    {
        # logged user-id...
        $user_id = intval( $id ); 
        // 1st, adjusting err-messages part accordingly...
        $arr_messages = array();
        $arr_values = $_POST;
        $err_flag = 0;
        
        // After form submission...
        if( $_POST ) {
            
            # ========= FOR DELETED ARRAY [BEGIN] =========
                $DELETED_IDS_ARR = array();
                $ids_to_be_deleted = $this->input->post("deleted_work_ids");
                
                if( !empty($ids_to_be_deleted) )
                    $DELETED_IDS_ARR = explode('#', $ids_to_be_deleted);
            # ========= FOR DELETED ARRAY [END] =========
            
            
            $arr_employer_name     =   array();
            $arr_employer_city     =   array();
            $arr_employer_state    =   array();
            $arr_employer_country  =   array();
            $arr_employer_position =   array();
            
            $experience_year_from    =   array();
            $experience_month_from   =   array();
            $experience_year_to      =   array();
            $experience_month_to     =   array();
            
            $arr_db_id               =  array();
            
            $total_divs=$this->input->post('total_work_divs'); 
            
            $arr_employer_name        = $this->input->post("txt_employer_name");
            $arr_employer_country     = $this->input->post("txt_employer_country");
            $arr_employer_state       = $this->input->post("txt_employer_state");
            $arr_employer_city        = $this->input->post("txt_employer_city");
            $arr_employer_position    = $this->input->post("txt_employer_position");
            
            $experience_year_from    =   $this->input->post("year_from"); 
            $experience_month_from   =   $this->input->post("mnth_from"); 
            $experience_year_to      =   $this->input->post("year_to"); 
            $experience_month_to     =   $this->input->post("mnth_to"); 
            
            $arr_db_id              = $this->input->post("db_id");
            
             if($this->input->post('is_current_employer'))
                $current_employer_record = $this->input->post('is_current_employer');
            
            for($i=0;$i<$total_divs;$i++)
            {
                if($current_employer_record == ($i+1))
                {
                    //echo "current emp in :".$current_employer_record ;
                    
                    array_splice( $experience_year_to, $i,0, '-1' );
                    array_splice( $experience_month_to, $i,0, '-1' );
                }
                else
                {
                    //echo "not in ".($i+1);
                }
            }
            
            $total_divs = count($arr_employer_name);
            $info = array();
            //echo "total sections = ".$total_divs;
            for($i=0;$i<$total_divs;$i++)
            {
                 //echo "i = ".$i;
                ## CHECKING BLANK ARRAY
                if($arr_employer_name[$i] !='' || $arr_employer_country[$i] != '' || $arr_employer_state[$i] != '' || $arr_employer_city[$i] != ''  || $arr_employer_position[$i] != '')
                {
                    if( !empty($DELETED_IDS_ARR) && in_array($arr_db_id[$i], $DELETED_IDS_ARR) ) {
                        $this->work_model->delete_info_db($arr_db_id[$i]);
                    } else {
                            
                        $info['s_employer_name']       =   get_formatted_string($arr_employer_name[$i]);
                        $info['s_employer_country']    =   intval(decrypt(trim($arr_employer_country[$i]))); //get_formatted_string($arr_employer_country[$i]);
                        $info['s_employer_state']      =   get_formatted_string($arr_employer_state[$i]);
                        $info['s_employer_city']       =   get_formatted_string($arr_employer_city[$i]);
                        $info['s_position']            =   get_formatted_string($arr_employer_position[$i]);
                        
                        
                        $info['s_experience_year_from']      =   get_formatted_string($experience_year_from[$i]);
                        $info['i_experience_month_from']     =   get_formatted_string($experience_month_from[$i]);
                        
                        $info['s_experience_year_to']        =   get_formatted_string($experience_year_to[$i]);
                        $info['i_experience_month_to']       =   get_formatted_string($experience_month_to[$i]);
                         //echo $current_employer_record." ".($i+1);
                        
                        if($current_employer_record == ($i+1))  
                        {
                            $info['e_is_current_employer'] = 'yes';
                        }
                        else
                        {
                            $info['e_is_current_employer'] = 'no';
                        }
                            
                            
                       
                            
                        $info['i_user_id']        =   $user_id;
                        
                        //pr($info);
                        # modify user-profile info...                    
                        
                        $result = $this->work_model->edit_work_info($info, $user_id,$arr_db_id[$i]); 
                       //echo $this->db->last_query().' ------ '; 
                        #pr($result,1);
                    }
                    
                }   // end of 'if any field in a div exists
                else
                {
                    $result=$this->work_model->delete_info_db($arr_db_id[$i])  ;
                }
                
            }
            
            
                      $SUCCESS_MSG = "Account updated successfully.";
                      
                      echo json_encode(array('result'=>'success',
                                             'msg'=>$SUCCESS_MSG));
                      exit;
                 
          
        
        }   // check if submitted...
        
              $REDIRECT = admin_base_url()."members/member-details/index/".$user_id;
              $SUCCESS_MSG = "Sorry! update not successful.";
              
              echo json_encode(array('result'=>'failure',
                                     'redirect'=>$REDIRECT,
                                     'msg'=>$SUCCESS_MSG));
              exit;

    }
    
    public function delete_work_info()
    {
        $db_id=$this->input->post('db_id');
        
        if($db_id)       
        {    
            $result=$this->work_model->delete_info_db($db_id);
        }
        else
        {
            echo json_encode(array(
                                    'result'    =>  'failure',
                                    'msg'       =>  'Removed.'
                            ));
            exit;
        }
            
        
        if($result)
            echo json_encode(array(
                                    'result'=>'success',
                                    'msg'=>'Successfully deleted.'
                            ));
        else
            echo json_encode(array(
                                    'result'=>'failure',
                                    'msg'   =>'Sorry. Can not be deleted.'
                            ));
       
    }
    
//========================================= end of work section ==================================================== 

//=================================== skill section ============================================
    
    public function modify_my_profile_skill_info_ajax($id)
    {
        # logged user-id...
        $user_id = intval( $id );
        // 1st, adjusting err-messages part accordingly...
        $arr_messages = array();
        $arr_values = $_POST;
        $err_flag = 0;
        
        #echo "testing before completing ajax operation"; exit;
        // After form submission...
        if( $_POST ) {
            
            
            # ========= FOR DELETED ARRAY [BEGIN] =========
                $DELETED_IDS_ARR = array();
                $ids_to_be_deleted = $this->input->post("deleted_skill_ids");
                
                if( !empty($ids_to_be_deleted) )
                    $DELETED_IDS_ARR = explode('#', $ids_to_be_deleted);
            # ========= FOR DELETED ARRAY [END] =========
            
            $arr_skill_name     =   array();
            $arr_db_id          =   array();
                        
            $arr_skill_name     = $this->input->post("txt_skill");
            
            $arr_db_id          = $this->input->post("skill_db_id");
          
            
            #$total_divs     =   $this->input->post('total_skill_divs');
            $total_divs     =   count( $arr_skill_name );
            $info = array();
            
            
            for($i=0;$i<$total_divs;$i++)
            {
                
                ## CHECKING BLANK ARRAY
                if($arr_skill_name[$i] !='' ){
                    
                    if( !empty($DELETED_IDS_ARR) && in_array($arr_db_id[$i], $DELETED_IDS_ARR) ) {
                        $this->skill_model->delete_info_db($arr_db_id[$i]);
                    } else {
                                
                        $info['s_name']    =   get_formatted_string($arr_skill_name[$i]);
                        $info['i_user_id']       =   $user_id;
                        $info['id']              =   $arr_db_id[$i];
                        
                        # modify user-profile info...                    
                        $result = $this->skill_model->edit_skill_info($info, $user_id,$arr_db_id[$i]);
                        
                    }
 
                    
                    
                }   // end of 'if any field in a div exists
                else
                {
                    $result=$this->skill_model->delete_info_db($arr_db_id[$i])  ;
                }
                
            }   
            
            
                      $SUCCESS_MSG = "Account updated successfully.";
                      echo json_encode(array('result'=>'success',
                                             'msg'=>$SUCCESS_MSG));
                      exit;
                 
          
        
        }   // check if submitted...
        
              $REDIRECT = admin_base_url()."members/member_details/index/".$user_id;
              $SUCCESS_MSG = "Sorry! update not successful.";
              
              echo json_encode(array('result'=>'failure',
                                     'redirect'=>$REDIRECT,
                                     'msg'=>$SUCCESS_MSG));
              exit;

    }
    
 //------------------------------------------- delete skill --------------------------   
    public function delete_skill_info()
    {
        $db_id=$this->input->post('db_id');
        //echo json_encode($db_id); exit;
        if($db_id)
            $result=$this->skill_model->delete_info_db($db_id);
        else
        {
            echo json_encode(array(
                                    'result'    =>  'failure',
                                    'msg'       =>  'Removed.'
                            ));
            exit;
        }
            
        
        if($result)
            echo json_encode(array(
                                    'result'=>'success',
                                    'msg'=>'Successfully deleted.'
                            ));
        else
            echo json_encode(array(
                                    'result'=>'failure',
                                    'msg'   =>'Sorry. Can not be deleted.'
                            ));
       
    }
    

    
    
//========================================= end of skill section =========================================

     
	
	public function add_photo($id)
	{
		try
		{
			
			$arr_messages = array();
			# error message trapping...
			$user_id = intval($id);
			/*if( $_FILES['s_profile_photo']['name']=='' ) {
				 $arr_messages['photo'] = "* Required Field";
			}*/
			//pr($_FILES['s_profile_photo']['name']);
						
			if( (isset($_FILES['s_profile_photo']['name']) && $_FILES['s_profile_photo']['name']!='')  &&  $user_id !="") {
				preg_match('/(^.*)\.([^\.]*)$/', $_FILES['s_profile_photo']['name'], $matches);
				$ext = "";
				if(count($matches)>0) {
					$ext = $matches[2];
					$original_name = $matches[1];
				}
				else
					$original_name = 'photo';

			
				if ( !in_array($ext , $this->config->item('VALID_IMAGE_EXT'))) 
				{
					 $arr_messages['photo'] ="supported extensions are ".implode(' , ',$this->config->item('VALID_IMAGE_EXT'));
				}
				else if($_FILES['s_profile_photo']['size'] > $this->config->item('MAX_UP_FILE_SIZE')*1024*1024)
				 {
					$arr_messages["photo"] = "Maximum file upload size is ".$this->config->item('MAX_UP_FILE_SIZE')." MB.";
				 }		
			

		   //pr($arr_messages);
				if( count($arr_messages)==0 ) {
						
					$info = array();
					$info['s_profile_photo'] 	= $this->_upload_profile_image($_FILES['s_profile_photo']['name']);	
					$info['dt_updated_on'] = get_db_datetime();
					 #dump($info);
					# modify user-profile info...
					$this->users_model->edit_info($info, $user_id);
					$REDIRECT = admin_base_url()."members/member-details/".$user_id; 
					//echo $this->db->last_query(); exit;
					$SUCCESS_MSG = "Proflie Picture updated successfully.";
					
					echo json_encode(array('result'=>'success',
										   'redirect'=>$REDIRECT,
										   'msg'=>$SUCCESS_MSG));
					exit;
					
						
				}
				else
				{
					echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>t('Error!'),) );
				}
		 }
		else
		{
			$SUCCESS_MSG = "Proflie Picture updated successfully.";
					
			echo json_encode(array('result'=>'success', 'redirect'=>$REDIRECT,'msg'=>$SUCCESS_MSG));
			exit;
		}
		
		}
		catch(Exception $err_obj)
        {
            
        } 
	}
 
    
    

	
	
	
	
		#### UPLOAD PROFILE PIC OF USER ######
	public function _upload_profile_image($prev_img = '',$filefieldname)
	{
	    $fileElementName = 's_profile_photo';	 
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
					#echo $this->upload_path.' === ';  echo $this->upload_image ;

					@move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $this->upload_image);
					
                       					
						
						$config['source_image'] = $this->upload_image;
						$config['thumb_marker'] = '-thumb';
						$config['crop'] = false;
						$config['crop_from'] = 'middle';
						$config['width'] = 60;
						$config['height'] = 60;
						$config['small_image_resize'] = 'bigger';
						
						resize_exact($config);
						
						$config = array();
						$config['source_image'] = $this->upload_image;
						$config['thumb_marker'] = '-main';
						$config['crop'] = false;
						$config['width'] = 144;
						$config['height'] = 144;
						$config1['crop_from'] = 'middle';
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
     
    public function valid_url($url)
    {
       
        if (!isValidURL($url) && $url!='')
        {
            $this->form_validation->set_message('valid_url', "Not a valid url");
		    return FALSE;
        }

        return TRUE;
    } 

   public function modify_my_account_info_ajax($id)
	{
        # logged user-id...
        $user_id = intval($id);
        // 1st, adjusting err-messages part accordingly...
        $arr_messages = array();
        $arr_values = $_POST;
		$err_flag = 0;
		$data['time_err'] ='';
		
        // After form submission...
        if( $this->input->post('is_basic_submitted', true)!="" ) {

			
			$posted["time-zone"]=trim($this->input->post("time-zone"));
			$posted["txt_email"]=trim($this->input->post("txt_email"));
			$posted["txt_phone"]=(trim($this->input->post("txt_phone")));
			
			$this->form_validation->set_message('required', "* Required field.");
			$this->form_validation->set_message('valid_email', "* Incorrect Email ID.");
			$this->form_validation->set_message('matches', " %s "."field does not match"." %s");
			
			
			$this->form_validation->set_rules('txt_email', " email", 'trim|required|valid_email'); 
				
			/*$this->form_validation->set_rules('txt_about', "About me", 'trim|required');
			$this->form_validation->set_rules('txt_cname', "Church name", 'trim|required');
			$this->form_validation->set_rules('txt_caddress', "Church address", 'trim|required');
			$this->form_validation->set_rules('txt_ccity', "Church city", 'trim|required');
			
			$this->form_validation->set_rules('txt_cstate', 'Church State', 'trim|required'); 
			$this->form_validation->set_rules('txt_ccountry', 'Church Country', 'required'); 
			$this->form_validation->set_rules('txt_cpostcode', 'Church postcode', 'trim|required'); 
			
			$this->form_validation->set_rules('txt_cphone', 'Church Phone number', 'trim|required'); 
			$this->form_validation->set_rules('txt_denomination', "Church denomination", 'trim|required|callback_valid_denomination');
			
			if($posted["time-zone"] == '-1'){
				$data['time_err'] = "* Required field.";
			}
				*/
			
			
			
			if ($this->form_validation->run() == FALSE ) 
			{
				# forming form-error array...
				$err_arr = array();
				foreach($this->required_accnt_fields as $key=>$val) {
					$err_arr[$val] = form_error($val);
				}
					
				
				echo json_encode(array('result'=>'error',
									   'arr_messages'=>$err_arr));
				exit;
			}
			else
			{
				
				$info['s_time'] = get_formatted_string($posted['time-zone']);
				$info['s_email'] = get_formatted_string($posted['txt_email']);
				$info['s_mobile'] = get_formatted_string($posted['txt_phone']);
				
				$info['dt_updated_on'] = get_db_datetime();
				
				 #pr($info,1);

				# modify user-profile info...
				$this->users_model->edit_info($info, $user_id);
				
				$REDIRECT = admin_base_url()."members/member-details/index/".$user_id; 
				$SUCCESS_MSG = "Account updated successfully";
				
				echo json_encode(array('result'=>'success',
									   'redirect'=>$REDIRECT,
									   'msg'=>$SUCCESS_MSG));
				exit;
			}
		
        }   // check if submitted...

        
	} 

}   // end of controller
