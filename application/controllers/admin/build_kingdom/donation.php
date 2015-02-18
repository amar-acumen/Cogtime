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

class Donation extends Admin_base_Controller
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
									    'js/backend/build_kingdom/donation.js',
										'js/backend/build_kingdom/charity_project.js') );
                                        
             parent::_add_css_arr( array('css/dd.css',
                                        ) );
            # adjusting header & footer sections [End]...
			$data['top_menu_selected'] = 6;
			$data['submenu'] = 2;
       
			$this->session->set_userdata('search_condition','');
			
			ob_start();
            $this->donation_ajax_pagination('common');
            $data['common_donation_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
			
                
            # rendering the view file...
            $VIEW_FILE = "admin/build_kingdom/donations.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }
	

  
	# function to load ajax-pagination [AJAX CALL]...
    public function donation_ajax_pagination( $tab_name = 'common' ,$page=0)
    {
        try
        {
			## seacrh conditions : filter ############
		 	
			 if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) :
           
				$WHERE_COND = " WHERE 1  ";
				
				$s_name = get_formatted_string(trim($this->input->post('txt_name')));
				$WHERE_COND .= ($s_name=='')?'':" AND (CONCAT(U.s_first_name,' ',U.s_last_name) LIKE '%".$s_name."%')";
				$this->session->set_userdata('search_condition',$WHERE_COND);
			
			
           endif;  
		   	
			$s_where = $this->session->userdata('search_condition');
			
			if($tab_name == 'common'){
		   		$result = $this->projects_model->getCommonDonationlist($s_where,$page,$this->pagination_per_page,$order_by);
				$total_rows = $this->projects_model->getCommonDonationlistCount($s_where);
			}
			else if($tab_name == 'fund'){
				$result = $this->projects_model->getFundDonationlist($s_where,$page,$this->pagination_per_page,$order_by);
				$total_rows = $this->projects_model->getFundDonationlistCount($s_where);
			}
			else if($tab_name == 'skill'){
				$s_where = $this->session->userdata('search_condition');
				$s_where .= " WHERE S_REQ.e_status = 'accepted' ";
				
				$result = $this->projects_model->get_all_skill_req_list($s_where,$page,$this->pagination_per_page,$order_by);
				$total_rows = $this->projects_model->get_all_skill_req_count($s_where);
			}
			else if($tab_name == 'prayer'){
				$result = $this->projects_model->getCommonDonationlist($s_where,$page,$this->pagination_per_page,$order_by);
				$total_rows = $this->projects_model->getCommonDonationlistCount($s_where);
			}
			
			
			## end seacrh conditions : filter ############
			
			//pr($result,1);
			#Jquery Pagination Starts
           	$this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/build_kingdom/donation/donation_ajax_pagination/".$tab_name;
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

            $config['div'] = '#'.$tab_name.'_donation_content'; /* Here #content is the CSS selector for target DIV */
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
			if($tab_name == 'common'){
		   		 echo  $this->load->view('admin/build_kingdom/donation_ajax/common_donation_ajax.phtml', $data,TRUE);
			}
			else if($tab_name == 'fund'){
				 echo  $this->load->view('admin/build_kingdom/donation_ajax/project_donation_ajax.phtml', $data,TRUE);
			}
			else if($tab_name == 'skill'){
				 echo  $this->load->view('admin/build_kingdom/donation_ajax/skill_donation_ajax.phtml', $data,TRUE);
			}
			else if($tab_name == 'prayer'){
				 echo  $this->load->view('admin/build_kingdom/donation_ajax/prayer_donation_ajax.phtml', $data,TRUE);

			}
			
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
	
	

	//// function to Delete donation
    public function delete_donation($id)
    {
		$i_ret=$this->projects_model->deleteDonationRequest($id);
		echo json_encode(array('success'=>true));
		exit;
		
	} // end of Delete banner function...
	 
	
	 
}   // end of controller...