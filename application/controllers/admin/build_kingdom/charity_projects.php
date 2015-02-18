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

class Charity_projects extends Admin_base_Controller
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
            $this->load->model("projects_model");
           $this->load->helper('common_option_helper.php');
           
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
									       'js/jquery/JSON/json2.js',
									     'js/backend/build_kingdom/charity_project.js') );
                                        
             parent::_add_css_arr( array('css/dd.css',
                                        ) );
            # adjusting header & footer sections [End]...
			$data['top_menu_selected'] = 6;
			$data['submenu'] = 3;
         
            
			// fetching data
			$WHERE_COND = " WHERE 1 ";
			$this->session->set_userdata('search_condition',$WHERE_COND);
			$page=0;
			$order_by = "`dt_start_time` ASC ";
			
			ob_start();
            $this->ajax_pagination();
            $data['result_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
			
                
            # rendering the view file...
            $VIEW_FILE = "admin/build_kingdom/charity_project/charity_projects.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }
	
	

    
    
    # function to Add new advertisement
    public function add_info()
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
									    'js/jquery/JSON/json2.js',
									  	'js/backend/build_kingdom/charity_project.js'
										 ) );
                                        
              parent::_add_css_arr( array('css/dd.css',
                                        ) );
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 6;
			$data['submenu'] = 3;
			$data['mode']="add";
            $data['error_country'] = '';
			$arr_messages = array();
			//Submitted Form
            if($_POST)
            {
				$posted=array();
                $posted["s_title"]= get_formatted_string(trim($this->input->post("txt_title")));
				$posted["s_description"]= get_formatted_string(trim($this->input->post("ta_description")));
				
				$posted["s_city"] = intval(decrypt(trim($this->input->post("txt_city"))));
				$posted["s_state"]	=intval(decrypt(trim($this->input->post("txt_state"))));
				$posted["i_country_id"]	=intval(decrypt(trim($this->input->post("sel_country"))));
				
				$posted["dt_start_date"]= trim($this->input->post("date_to1"));
				$posted["dt_end_date"]= trim($this->input->post("date_to2"));
				
				$posted["f_project_cost"]= trim($this->input->post("txt_project_cost"));
				
				
				
				$skill_date_from_arr = array();
				$skill_date_to_arr = array();
				$skill_name_arr = array();
				$skill_manpower_arr = array();
				
				$skill_date_from_arr = $this->input->post("skill_date_from");
				$skill_date_to_arr = $this->input->post("skill_date_to");
				$skill_name_arr  = $this->input->post("txt_skill");
				
				$skill_manpower_arr = $this->input->post("txt_manpower");
				
				$total_skills = count($skill_date_from_arr);
				
				
				if(trim($this->input->post('sel_country'))=='-1') 
				{		
						$arr_messages['country'] = "* Required Field";
				}	
				
				if(trim($this->input->post('txt_title'))=='') 
				{		
						$arr_messages['title'] = "* Required Field";
				}	
				
				if(trim($this->input->post('ta_description'))=='') 
				{		
						$arr_messages['description'] = "* Required Field";
				}
				
				/*if(trim($this->input->post('txt_city'))=='') 
				{		
						$arr_messages['city'] = "* Required Field";
				}*/
				
				if(trim($this->input->post('txt_state'))=='') 
				{		
						$arr_messages['state'] = "* Required Field";
				}	
				
				if(trim($this->input->post('date_to1'))=='' ) 
				{		
						$arr_messages['date_to1'] = "* Required Field";
				}	
				
				if(trim($this->input->post('date_to2'))=='' ) 
				{		
						$arr_messages['date_to2'] = "* Required Field";
				}	
				
				
				if(trim($this->input->post("txt_project_cost")) == ''){
					$arr_messages['project_cost'] = "* Required Field";
				}
				else if(!is_numeric(trim($this->input->post("txt_project_cost")))){
					$arr_messages['project_cost'] = "* Numeric value only";
				}
				
				
				
				
				if($skill_date_from_arr[0] == ''){
					$arr_messages['skill_date_from_1'] = '* Required Field.';
				}
				
				
				if($skill_date_to_arr[0] == ''){
					$arr_messages['skill_date_to_1'] = '* Required Field.';
				}
				
				if($skill_name_arr[0] == ''){
					$arr_messages['skill_name_1'] = '* Required Field.';
				}
				
				if($skill_manpower_arr[0] == '' || $skill_manpower_arr[0] == 0){
					$arr_messages['manpower_1'] = "* Required Field";
				}
				else if(!is_numeric($skill_manpower_arr[0])){
					$arr_messages['manpower_1'] = "* Numeric value only";
				}
				
					
				if(count($arr_messages) == 0)
				{
				
					//adding to database
					$info=array();
					$info["s_title"]= $posted["s_title"];
					$info["i_host_id"] = intval(decrypt($this->session->userdata('user_id')));
					
					
					$info["s_description"]= $posted["s_description"];
					
					$info["s_city"]= $posted["s_city"];
					$info["s_state"]= $posted["s_state"];
					
					$info["i_country_id"]= intval(trim($posted["i_country_id"]));
					
					$info["f_project_cost"]= $posted["f_project_cost"];
					
					$info["dt_start_date"]= get_db_dateformat($posted["dt_start_date"] ,'/');
					$info["dt_end_date"]= get_db_dateformat($posted["dt_end_date"] ,'/');
					
					$info['dt_created_on'] = get_db_datetime();
					
					//pr($info);
					$i_newid = $this->projects_model->insert($info); //echo $this->db->last_query(); 
					
					### inserting skills in PROJECT_SKILL_REQUIRED
					
					for($i= 0; $i < $total_skills; $i++){
						
						$skill_arr = array();
						$dayinfo = array();
						
						$skill_arr['i_project_id'] = $i_newid;
						$skill_arr['s_name'] = $skill_name_arr[$i];
						$skill_arr['dt_start_date'] = get_db_dateformat($skill_date_from_arr[$i] , '/');
						$skill_arr['dt_end_date'] = get_db_dateformat($skill_date_to_arr[$i],'/');
						$dayinfo = get_date_diff($skill_arr['dt_start_date'], $skill_arr['dt_end_date']);					                            
						//$skill_arr['i_total_skill_req'] = $dayinfo[0]['difference'];
						$skill_arr["i_total_manpower_req"] = $skill_manpower_arr[$i];

						$skill_arr['dt_created_on'] = get_db_datetime();
						
						$skill_id = $this->projects_model->insert_skill_req($skill_arr);
					}
					
					
					echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'New project has been added successfully.') );
					exit;
					
				}
				else
				{
					 echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'Error!') );
					 exit;
				}
				
			}
		 	// End Submitted Form
			 $VIEW_FILE = "admin/build_kingdom/charity_project/add_edit_project.phtml";
            parent::_render($data, $VIEW_FILE);
			
		}
		
		catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
		
	}   // end of Add new banner function...
    
    
    
    
    //// function to Edit the Existing advertisement
    public function edit_info($id)
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
									    'js/jquery/JSON/json2.js',
										'js/backend/build_kingdom/charity_project.js'
									     ) );
                                        
            parent::_add_css_arr( array('css/dd.css',
                                        ) );
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 6;
			$data['submenu'] = 3;
			# rendering the view file...
			$data['mode']="edit";
			
			$data['project_id'] = $id;
		
			$info=$this->projects_model->get_by_id($id);
				
			$posted=array();
			$posted["id"]= trim($info["id"]);
			$posted["s_title"]= trim($info["s_title"]);
			$posted["s_description"]= trim($info["s_description"]);
			
			$posted["i_host_id"]= trim($info["i_host_id"]);
			$posted["s_city"]= ($info["s_city"]);
			$posted["s_state"]= ($info["s_state"]);
			
			$posted["i_country_id"]= $info["i_country_id"];
			
			$posted["dt_start_date"]= getShortDate($info["dt_start_date"],3);
			$posted["dt_end_date"]= getShortDate($info["dt_end_date"],3);
			
			$posted["f_project_cost"]= $info["f_project_cost"];
			$posted["dt_created_on"]= $info["dt_created_on"];
			
			
			$skill_info =  $this->projects_model->get_all_skill_by_project_id($id);
				
				
			$where	= " i_country_id='".$posted["i_country_id"]."'";
            $data['state'] 	= makeOptionState($where, encrypt($posted["s_state"]));
			
			$where1	= " i_state_id='".$posted["s_state"]."'";
            $data['city'] 	= makeOptionCity($where1,encrypt($posted["s_city"]));
			
			 $data['posted']=$posted;
			 $data['skill_info'] = $skill_info;
			 
			 $VIEW_FILE = "admin/build_kingdom/charity_project/add_edit_project.phtml";
            parent::_render($data, $VIEW_FILE);
			
		}
		catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
		
		
	}	// end of edit banner function...
    
    
    public function update_info_ajax()
	{
		try
		{
			
			$arr_messages = array();
		    if($_POST)
            {
		    	
				$posted=array();
                $posted["s_title"]= get_formatted_string(trim($this->input->post("txt_title")));
				$posted["s_description"]= get_formatted_string(trim($this->input->post("ta_description")));
				
				$posted["s_city"] = intval(decrypt(trim($this->input->post("txt_city"))));
				$posted["s_state"]	=intval(decrypt(trim($this->input->post("txt_state"))));
				$posted["i_country_id"]	=intval(decrypt(trim($this->input->post("sel_country"))));
				
				$posted["dt_start_date"]= trim($this->input->post("date_to1"));
				$posted["dt_end_date"]= trim($this->input->post("date_to2"));
				
				$posted["f_project_cost"]= trim($this->input->post("txt_project_cost"));
				
				$posted['s_name']  = get_formatted_string(trim($this->input->post("txt_skill")));
				$posted["skill_date_from"]= ($this->input->post("skill_date_from"));
				$posted["skill_date_to"]= ($this->input->post("skill_date_to"));
				//pr($posted,1);
				
				
				$skill_date_from_arr = array();
				$skill_date_to_arr = array();
				$skill_name_arr = array();
				$skill_manpower_arr = array();
				
				$skill_date_from_arr = $this->input->post("skill_date_from");
				$skill_date_to_arr = $this->input->post("skill_date_to");
				$skill_name_arr  = $this->input->post("txt_skill");
				
				$skill_dbid  = $this->input->post("dbid");
				$skill_manpower_arr = $this->input->post("txt_manpower");
				
				//pr($skill_manpower_arr,1);
				$total_skills = count($skill_date_from_arr);
				
				
				if(trim($this->input->post('sel_country'))=='-1') 
				{		
						$arr_messages['country'] = "* Required Field";
				}	
				
				if(trim($this->input->post('txt_title'))=='') 
				{		
						$arr_messages['title'] = "* Required Field";
				}	
				
				if(trim($this->input->post('ta_description'))=='') 
				{		
						$arr_messages['description'] = "* Required Field";
				}
				
				/*if(trim($this->input->post('txt_city'))=='') 
				{		
						$arr_messages['city'] = "* Required Field";
				}*/
				
				if(trim($this->input->post('txt_state'))=='') 
				{		
						$arr_messages['state'] = "* Required Field";
				}	
				
				if(trim($this->input->post('date_to1'))=='' ) 
				{		
						$arr_messages['date_to1'] = "* Required Field";
				}	
				
				if(trim($this->input->post('date_to2'))=='' ) 
				{		
						$arr_messages['date_to2'] = "* Required Field";
				}	
				
				if(trim($this->input->post("txt_project_cost")) == ''){
					$arr_messages['project_cost'] = "* Required Field";
				}
				else if(!is_numeric(trim($this->input->post("txt_project_cost")))){
					$arr_messages['project_cost'] = "* Numeric value only";
				}
				
				
				if($skill_date_from_arr[0] == ''){
					$arr_messages['skill_date_from_1'] = '* Required Field.';
				}
				
				
				if($skill_date_to_arr[0] == ''){
					$arr_messages['skill_date_to_1'] = '* Required Field.';
				}
				
				
				if($skill_name_arr[0] == ''){
					$arr_messages['skill_name_1'] = '* Required Field.';
				}
			
				if($skill_manpower_arr[0] == '' || $skill_manpower_arr[0] == 0){
					$arr_messages['manpower_1'] = "* Required Field";
				}
				else if(!is_numeric($skill_manpower_arr[0])){
					$arr_messages['manpower_1'] = "* Numeric value only";
				}
				
			
			}
		   
			if( count($arr_messages)==0 ) {
					
				$info = array();
				
				
				
					//adding to database
					$info=array();
					$info["s_title"]= $posted["s_title"];
					$info["i_host_id"] = intval(decrypt($this->session->userdata('user_id')));
					$info["s_description"]= $posted["s_description"];
					
					$info["s_city"]= $posted["s_city"];
					$info["s_state"]= $posted["s_state"];
					
					$info["i_country_id"]= intval(trim($posted["i_country_id"]));
					
					$info["f_project_cost"]= $posted["f_project_cost"];
					
					$info["dt_start_date"]= get_db_dateformat($posted["dt_start_date"] ,'/');
					$info["dt_end_date"]= get_db_dateformat($posted["dt_end_date"] ,'/');
					
					$info['dt_updated_on'] = get_db_datetime();
					
					$i_newid = $this->projects_model->update($info,$this->input->post('project_id'));
					
					
					### inserting and Updating skills in PROJECT_SKILL_REQUIRED
					for($i=0 ; $i<=count($skill_name_arr) ; $i++){
						if($skill_name_arr[$i] != ''){	
							
							$skill_arr = array();
							
							$skill_arr['i_project_id'] = $this->input->post('project_id');
							$skill_arr['s_name'] = $skill_name_arr[$i] ;
							$skill_arr['dt_start_date'] = get_db_dateformat($skill_date_from_arr[$i] , '/');
							$skill_arr['dt_end_date'] = get_db_dateformat($skill_date_to_arr[$i],'/');
							
							$dayinfo = get_date_diff($skill_arr['dt_start_date'], $skill_arr['dt_end_date']);					                            $skill_arr['i_total_day'] = $dayinfo[0]['difference']; 
							$skill_arr["i_total_manpower_req"] = $skill_manpower_arr[$i];
							$skill_arr['dt_updated_on'] = get_db_datetime();

							
							if($skill_dbid[$i] != ''){
								$this->projects_model->update_skill_req($skill_arr, $skill_dbid[$i]);
							}
							else{
								$skill_id = $this->projects_model->insert_skill_req($skill_arr);
							}
						}
					}
					
					
									
				echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'Project details updated Successfully.') );
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
    
    
    
    //// function to Delete Banner
    public function delete_information ($id)
    {
		$i_ret=$this->projects_model->delete_by_id($id);
		$re_page = admin_base_url() ."charity-projects.html";
					header("location:".$re_page);
					exit;
		
	} // end of Delete banner function...
	
	
	# function to load ajax-pagination [AJAX CALL]...
    public function ajax_pagination($page=0)
    {
        try
        {
			## seacrh conditions : filter ############
		 	
			 if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) :
           
            
			
				$WHERE_COND = " WHERE 1  ";
				
				$s_title = get_formatted_string(trim($this->input->post('txt_title')));
				$WHERE_COND .= ($s_title=='')?'':" AND (P.s_title LIKE '%".$s_title."%')";
				
				$s_skill = get_formatted_string(trim($this->input->post('txt_skill')));
				$WHERE_COND .= ($s_skill=='')?'':" AND (S.s_name LIKE '".$s_skill."%')";
				
				
						
			
				if($this->input->post('pro_date_to1') != ''){
					$p_dt_start_date = get_db_dateformat($this->input->post('pro_date_to1'));
					$WHERE_COND .= ($p_dt_start_date=='')?'':" AND (DATE(P.dt_start_date) ='".$p_dt_start_date."' )";
				}
				
				if(trim($this->input->post('pro_date_to2')) != ''){
					$p_dt_end_date = get_db_dateformat($this->input->post('pro_date_to2'));
					$WHERE_COND .= ($p_dt_end_date=='')?'':" AND (DATE(P.dt_end_date) ='".$p_dt_end_date."' )";
				}
				
				
				if($this->input->post('s_date_to1') != ''){
					$s_dt_start_date = get_db_dateformat($this->input->post('s_date_to1'));
					$WHERE_COND .= ($s_dt_start_date=='')?'':" AND (DATE(S.dt_start_date) ='".$s_dt_start_date."' )";
				}
				
				if($this->input->post('s_date_to2') != ''){
					$s_dt_end_date = get_db_dateformat($this->input->post('s_date_to2'));
					$WHERE_COND .= ($s_dt_end_date=='')?'':" AND (DATE(S.dt_end_date) ='".$s_dt_end_date."' )";
				}
				
				$this->session->set_userdata('search_condition',$WHERE_COND);
			
			
           endif;  
		   	
			$s_where = $this->session->userdata('search_condition');
			$order_by = "`dt_start_time` ASC ";
		   	$result = $this->projects_model->get_list($s_where,$page,$this->pagination_per_page,$order_by);
            $resultCount = count($result);
	//echo $this->db->last_query(); exit;
			$total_rows = $this->projects_model->get_list_count($s_where);
			
			if( ( !is_array($result) || !count($result) ) && $total_rows ) {
                $page = $page - $this->pagination_per_page;
                
                $result = $this->projects_model->get_list($s_where, $page, $this->pagination_per_page,$ORDER_BY);
            }
			## end seacrh conditions : filter ############
			
			//pr($result,1);
			#Jquery Pagination Starts
           	$this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/build_kingdom/charity_projects/ajax_pagination";
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
          echo  $this->load->view('admin/build_kingdom/charity_project/manage_projects_ajax.phtml', $data,TRUE);
		 }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
    }
	
	public function deleteSkill($skill_id){
		$this->projects_model->delete_skill_by_id($skill_id);
		echo json_encode(array('success'=>true));
		exit;
	}
	
	
	public function manage_skill_donations($project_id) 
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
									    'js/jquery/JSON/json2.js',
									    'js/backend/build_kingdom/charity_project.js') );
                                        
             parent::_add_css_arr( array('css/dd.css',
                                        ) );
            # adjusting header & footer sections [End]...
			$data['top_menu_selected'] = 6;
			$data['submenu'] = 3;
         
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
			
			#pr($data['project_info']);
			$this->session->set_userdata('search_condition','');
			
			ob_start();
            $this->skillRequestAjaxPagination($project_id);
            $data['skill_list'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
			
			$data['i_project_id'] = $project_id;
			
                
            # rendering the view file...
            $VIEW_FILE = "admin/build_kingdom/charity_project/manage-skill-donations.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }
	
	# function to load ajax-pagination [AJAX CALL]...
    public function skillRequestAjaxPagination($project_id, $page=0)
    {
        try
        {
			## seacrh conditions : filter ############
		 	
			 if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) :
            
				$WHERE_COND = " WHERE 1  ";
				
				$s_name = get_formatted_string(trim($this->input->post('txt_name')));
				$WHERE_COND .= ($s_name=='')?'':" AND CONCAT(u.s_first_name,' ',u.s_last_name) LIKE '%".$s_name."%' ";
				
				$s_skill = get_formatted_string(trim($this->input->post('txt_skill')));
				$WHERE_COND .= ($s_skill=='-1' || $s_skill=='')?'':" AND (S_REQ.s_skill_name LIKE '".$s_skill."%')";
				
				$this->session->set_userdata('search_condition',$WHERE_COND);
			
           endif;  
		   
		   
		   	
			if($this->session->userdata('search_condition') != ''){
				$s_where = $this->session->userdata('search_condition');	
				$s_where .= " AND S_REQ.i_project_id = {$project_id} ";
			}
			else{
				$s_where = " WHERE S_REQ.i_project_id = {$project_id} ";
			}
			
		   	$result = $this->projects_model->get_all_skill_req_list($s_where,$page,$this->pagination_per_page,$order_by);
            $resultCount = count($result);

			$total_rows = $this->projects_model->get_all_skill_req_count($s_where);
			
			if( ( !is_array($result) || !count($result) ) && $total_rows ) {
                $page = $page - $this->pagination_per_page;
                $result = $this->projects_model->get_all_skill_req_list($s_where, $page, $this->pagination_per_page,$ORDER_BY);
            }
			## end seacrh conditions : filter ############
			
			//pr($result,1);
			#Jquery Pagination Starts
           	$this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/build_kingdom/charity_projects/skillRequestAjaxPagination/".$project_id;
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->pagination_per_page;
            $config['uri_segment'] = 6;
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
          echo  $this->load->view('admin/build_kingdom/charity_project/skill_list_ajax.phtml', $data,TRUE);
		 }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
    }
    
	
	 public function dayWiseSkillRequestAjaxPagination($project_id, $page=0)
    {
        try
        {
			
		   ####  get day wise request #####
		    $_REQUEST['search_daywise'];
		   if(isset($_REQUEST['search_daywise']) && $_REQUEST['search_daywise'] == 'Y' ) :
            
				$WHERE_COND = " WHERE 1  ";
				
				if($_REQUEST['block_dt'] != ''){
					$d_start_date = $_REQUEST['block_dt'];
					$WHERE_COND .= ($d_start_date=='')?'':" AND (S_REQ.d_start_date ='".$d_start_date."' 
									OR S_REQ.d_end_date = '".$d_start_date."' 
									OR  '".$d_start_date."' BETWEEN S_REQ.d_start_date AND S_REQ.d_end_date)";
				}
				
				if($_REQUEST['skill_name'] != ''){
					
					$s_skill = $_REQUEST['skill_name'];
					$WHERE_COND .= ($s_skill=='')?'':" AND (S_REQ.s_skill_name ='".$s_skill."')";
				}
				
				
				
				$this->session->set_userdata('search_condition',$WHERE_COND);
			
           endif;  
		   
		   ##### get day wise request #####
		   
		   
		   	
			if($this->session->userdata('search_condition') != ''){
				$s_where = $this->session->userdata('search_condition');	
				$s_where .= " AND S_REQ.i_project_id = {$project_id} ";
			}
			else{
				$s_where = " WHERE S_REQ.i_project_id = {$project_id} ";
			}
			
		   	$result = $this->projects_model->get_all_skill_req_list($s_where,$page,$this->pagination_per_page,$order_by);
            $resultCount = count($result);

			$total_rows = $this->projects_model->get_all_skill_req_count($s_where);
			
			if( ( !is_array($result) || !count($result) ) && $total_rows ) {
                $page = $page - $this->pagination_per_page;
                $result = $this->projects_model->get_all_skill_req_list($s_where, $page, $this->pagination_per_page,$ORDER_BY);
            }
			## end seacrh conditions : filter ############
			
			//pr($result,1);
			#Jquery Pagination Starts
           	$this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/build_kingdom/charity_projects/dayWiseSkillRequestAjaxPagination/".$project_id;
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->pagination_per_page;
            $config['uri_segment'] = 6;
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
          	 $html = $this->load->view('admin/build_kingdom/charity_project/skill_list_ajax.phtml', $data,TRUE);
			 echo $html;
			 
		 }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
    }
	
	
	
	
	
	public function ajaxSkillChart($project_id)
    {
        try
        {
		    $project_info =  $this->projects_model->get_by_id($project_id);
			$data['project_info'] = $project_info;
			
			
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
			  ### forming manage skill calendar array
			  ###############################################
			
			 # loading the view-part...
          	 $html = $this->load->view('admin/build_kingdom/charity_project/manage_skill_ajax_chart.phtml', $data,TRUE);
			 echo $html;
			 
		 }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
    }
	
	
	 public function change_status()
	 {
			
			$data = $this->data;
			$page  =  intval($this->input->post('cur_page'));
			$i_status = $this->input->post('i_status');
			$cur_status = $this->input->post('cur_status');
			$ID = $this->input->post('record_id');
			$project_id = $this->input->post('project_id');
			
			if($i_status == 'accepted'){
				$css = "blue_btn";
			}
			else if($i_status == 'pending' ){	
				$css = "orange_btn";
			}
			else if($i_status == 'suspended'){
				$css = "red_btn";
			}
			
			if($this->session->userdata('user_id') !="")
			{
				$arr = array();
				$arr['e_status'] = $i_status;
				$this->projects_model->update_skill_request($arr,$ID);
				/*if($i_status=='pending')
				   {
					 
						$action_txt =
							 '<input name="" title="Approved" type="button" class="btn-03 '.$css.'" onclick="javascript:changeStatus(\''.$ID.'\',\'accepted\',\''.$i_status.'\' , \''.$project_id.'\')"  value="Approved"/>';
							 
							 $SUCCESS_MSG = "Skill approved successfully! ";
					
				   }
				 else if($i_status=='accepted')
				   {
						$action_txt =
							 '<input name="" title="Suspended" type="button" class="btn-03 '.$css.'" onclick="javascript:changeStatus(\''.$ID.'\',\'suspended\',\''.$i_status.'\' , \''.$project_id.'\')"  value="Suspended"/>';
							 
							$SUCCESS_MSG = "Skill suspended successfully! ";
					
				   } 
				 else if($i_status=='suspended')
				   {
					 
						$action_txt =
							 '<input name="" title="Approved" type="button" class="btn-03 '.$css.'" onclick="javascript:changeStatus(\''.$ID.'\',\'accepted\',\''.$i_status.'\' , \''.$project_id.'\')"  value="Approved"/>';
							 
							 $SUCCESS_MSG = "Skill approved successfully! ";
					
				   }*/
				   
				    ob_start();
					$this->skillRequestAjaxPagination($project_id);
					$skill_list = ob_get_contents(); //pr($data['result_content']);
					ob_end_clean();
			}
			else{
			    
				$SUCCESS_MSG = "An error has occured! please try again. ";
				echo json_encode(array('result'=>false,
                					   'u_id'=>$ID,
									   'action_txt' =>$action_txt,
									   'i_status' => $cur_status,
                					   'msg'=>$SUCCESS_MSG,
									   'skill_list'=>$skill_list)); exit;
			}
			
			
			
	    
			# view part...
			ob_start();
            $this->ajaxSkillChart($project_id);
            $chart_html = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
                
            echo json_encode(array('result'=>true,
                					   'u_id'=>$ID,
									   'action_txt' =>$action_txt,
									   'i_status' => $cur_status,
                					   'msg'=>$SUCCESS_MSG,
									   'chart_html'=>$chart_html,
									     'skill_list'=>$skill_list));
	 }
	 
	 
	//// function to Delete donation
    public function delete_donation($id)
    {
		$i_ret=$this->projects_model->deleteDonationRequest($id);
		echo json_encode(array('success'=>true));
		exit;
		
	} // end of Delete banner function...
	 
	 
	 public function change_project_status()
	 {
			
			$data = $this->data;
			$i_status = $this->input->post('i_status');
			$cur_status = $this->input->post('cur_status');
			$ID = $this->input->post('record_id');
			
			if($this->session->userdata('user_id') !="")
			{
				$arr = array();
				$arr['i_isopened'] = $i_status;
				$this->projects_model->update($arr,$ID);
				if($i_status=='2')
				   {
					 
						$action_txt =
							 '<input name="" title="Open" type="button" class="btn-03" onclick="javascript:changeProjectStatus(\''.$ID.'\',\'1\',\''.$i_status.'\' )"  value="OPEN"/>';
							 
							 $SUCCESS_MSG = "Project closed successfully! ";
					
				   }
				 else if($i_status=='1')
				   {
						$action_txt =
							 '<input name="" title="Close" type="button" class="btn-03" onclick="javascript:changeProjectStatus(\''.$ID.'\',\'2\',\''.$i_status.'\' )"  value="CLOSE"/>';
							 
							$SUCCESS_MSG = "Project opened successfully! ";
					
				   } 
			}
			else{
			    
				$SUCCESS_MSG = "An error has occured! please try again. ";
				echo json_encode(array('result'=>false,
                					   'u_id'=>$ID,
									   'action_txt' =>$action_txt,
									   'i_status' => $cur_status,
                					   'msg'=>$SUCCESS_MSG)); exit;
			}
	    
			# view part...
			ob_start();
            $this->ajax_pagination();
            $html = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
                
            echo json_encode(array('result'=>true,
                					   'u_id'=>$ID,
									   'action_txt' =>$action_txt,
									   'i_status' => $cur_status,
                					   'msg'=>$SUCCESS_MSG,
									   'html'=>$html));
	 }
	 
   public function getProjectDetail($p_id)
	{
	  try
		{
		   $data = $this->data; 
		   $data['project_detail'] = $this->projects_model->get_by_id($p_id);

		   $VIEW = "admin/build_kingdom/charity_project/project_detail_ajax.phtml";

		   $html = $this->load->view($VIEW, $data, true);  
		   echo json_encode( array('result'=>success,'html'=>$html) );
		 } 
	  catch(Exception $err_obj)
		{
		  show_error($err_obj->getMessage());
		} 
			
	} 
	 
	public function getFundDonor($p_id)
	{
	  try
		{
		   $data = $this->data;
		   $fund_where = "WHERE PF.i_project_id = {$p_id} 
								    AND PF.i_order_status = 1
									AND PF.e_dnt_disclose_name = 'N'"; 
									
		   $data['donor_list'] = $this->projects_model->getFundDonationlist($fund_where);

		   $VIEW = "admin/build_kingdom/charity_project/fund_donor_ajax_list.phtml";

		   $html = $this->load->view($VIEW, $data, true);  
		   echo json_encode( array('result'=>'success','html'=>$html) );
		 } 
	  catch(Exception $err_obj)
		{
		  show_error($err_obj->getMessage());
		} 
			
	} 
	 
	public function getSkillDonor($p_id)
	{
	  try
		{
		   $data = $this->data; 
		   $d_where = " WHERE S_REQ.i_project_id = {$p_id} 
								   AND S_REQ.e_status = 'accepted' ";
		   $data['donor_list'] = $this->projects_model->get_all_skill_req_list($d_where);

		   $VIEW = "admin/build_kingdom/charity_project/skill_donor_ajax_list.phtml";

		   $html = $this->load->view($VIEW, $data, true);  
		   echo json_encode( array('result'=>'success','html'=>$html) );
		 } 
	  catch(Exception $err_obj)
		{
		  show_error($err_obj->getMessage());
		} 
			
	} 
	
	
	public function downloadResume($s_id)
	{
	  try
	  { 
			$request_info = $this->projects_model->get_skill_request_detail_by_id($s_id);
			$filename = $request_info['s_cv_filename'];
			$upload_path = BASEPATH.'../uploads/project_cv_user/';
			$this->load->helper('download_helper');
			$data = file_get_contents($upload_path.$filename);
			force_download($filename, $data);
			exit;
	   } 
	 catch(Exception $err_obj)
	  {
		show_error($err_obj->getMessage());
	  } 
			
	} 
	
	
	
	 // "index" function definition...
    public function requested_informations($project_id) 
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
									       'js/jquery/JSON/json2.js',
									     'js/backend/build_kingdom/charity_project.js') );
                                        
             parent::_add_css_arr( array('css/dd.css',
                                        ) );
            # adjusting header & footer sections [End]...
			$data['top_menu_selected'] = 6;
			$data['submenu'] = 3;
			// fetching data
			$this->session->set_userdata('search_condition','WHERE 1  ');
			$data['project_info'] = $this->projects_model->get_by_id($project_id);
			
			ob_start();
            $this->info_ajax_pagination($project_id);
            $data['result_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
			
                
            # rendering the view file...
            $VIEW_FILE = "admin/build_kingdom/charity_project/info.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }
	
	
	# function to load ajax-pagination [AJAX CALL]...
    public function info_ajax_pagination($project_id, $page=0)
    {
        try
        {
			## seacrh conditions : filter ############
		 	
			 if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) :
				$WHERE_COND = "WHERE 1   ";
				
				$s_title = get_formatted_string(trim($this->input->post('txt_title')));
				$WHERE_COND .= ($s_title=='')?'':" AND (CONCAT(U.s_first_name, U.s_last_name )LIKE '%".$s_title."%')";
				$this->session->set_userdata('search_condition',$WHERE_COND);
            endif;  
		   	
			$s_where = $this->session->userdata('search_condition');
			$s_where .= " AND PR.i_project_id = {$project_id}";
			
			
		   	$result = $this->projects_model->get_info_list($s_where,$page,$this->pagination_per_page,$order_by);
            $resultCount = count($result);
			$total_rows = $this->projects_model->get_info_list_count($s_where);
			## end seacrh conditions : filter ############
			
			//pr($result,1);
			#Jquery Pagination Starts
           	$this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/build_kingdom/charity_projects/info_ajax_pagination/".$project_id;
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->pagination_per_page;
            $config['uri_segment'] = 6;
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
          echo  $this->load->view('admin/build_kingdom/charity_project/info_ajax.phtml', $data,TRUE);
		 }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
    }
}   // end of controller...