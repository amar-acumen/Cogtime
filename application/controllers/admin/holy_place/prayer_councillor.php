<?php

/* * *******
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

class Prayer_councillor extends Admin_base_Controller {

    private $pagination_per_page = 25;

    // constructor definition...
    function __construct() {
        try {
            parent::__construct();
            parent::_check_admin_login();
            # configuring paths...
            # loading reqired model & helpers...
            // $this->load->helper('###');
            $this->load->model("users_model");
            $this->load->model("education_model");
            $this->load->helper('common_option_helper.php');
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    // "index" function definition...
    public function index($page_no = 0) {
        //echo "in members.php : page_no = ".$page_no;
        try {
            # adjusting header & footer sections [Start]...
            $data = $this->data;
            parent::_set_title("::: COGTIME Xtian network :::");
            parent::_set_meta_desc("::: COGTIME Xtian network :::");
            parent::_set_meta_keywords("::: COGTIME Xtian network :::");
            parent::_add_js_arr(array('js/lightbox.js',
                'js/ModalDialog.js',
                'js/jquery.dd.js',
                'js/backend/members/delete_user.js'
            ));

            parent::_add_css_arr(array());
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 7;
            $data['submenu'] = 10;


            // fetching data
            $WHERE_COND = " WHERE 1 AND i_isdeleted=1 AND is_councillor=1";
            $this->session->set_userdata('search_condition', $WHERE_COND);
            $page = 0;
            $order_by = " `id` DESC ";


            //---------------------- for pagination back ---------------------
            if ($page_no != 0)
                $page = ($page_no - 1) * 2;
            //---------------------- end pagination back ---------------------

            ob_start();
            $this->ajax_pagination($page);
            $data['result_content'] = ob_get_contents(); //pr($data['result_content'],1);
            ob_end_clean();

            #pr($data,1);
            # rendering the view file...
            $VIEW_FILE = "admin/holy_place/prayer_councillor.phtml";
            parent::_render($data, $VIEW_FILE);
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

    public function get_member_list($page) {

        ob_start();
        $this->ajax_pagination($page);
        $data['result_content'] = ob_get_contents();
        ob_end_clean();
        //echo json_encode( array('success'=>1, 'msg'=>$success_msg) );

        $VIEW_FILE = "admin/holy_place/prayer_councillor.phtml";
        parent::_render($data, $VIEW_FILE);
    }

    # function to load ajax-pagination [AJAX CALL]...

    public function ajax_pagination($page = 0) {
        try {
            $s_where = $this->session->userdata('search_condition');
            $order_by = " `id` DESC ";
            $result = $this->users_model->user_list($s_where, $page, $this->pagination_per_page, $order_by);
            $resultCount = count($result);
            // echo $this->db->last_query(); 
            #pr($result,1);
            $total_rows = $this->users_model->user_list_count($s_where);

            if ((!is_array($result) || !count($result) ) && $total_rows) {
                $page = $page - $this->pagination_per_page;

                $result = $this->users_model->user_list($s_where, $page, $this->pagination_per_page, $order_by);
            }
            ## end seacrh conditions : filter ############
            //pr($result,1);
            #Jquery Pagination Starts
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url() . "admin/holy_place/prayer_councillor/ajax_pagination";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->pagination_per_page;
            $config['uri_segment'] = 5;
            $config['num_links'] = 9;
            $config['page_query_string'] = false;
            $config['prev_link'] = 'PREV';
            $config['next_link'] = 'NEXT';

            /* $config['cur_tag_open'] = '<li> <span><a href="javascript:void(0)" class="select">';
              $config['cur_tag_close'] = '</a></span></li>';

              $config['next_tag_open'] = '<li><a href="javascript:void(0)">';
              $config['next_tag_close'] = '</a></li>';

              $config['prev_tag_open'] = '<li><a href="javascript:void(0)">';
              $config['prev_tag_close'] = '</a></li>'; --commented@12.02.13 */

            $config['cur_tag_open'] = '<li>';
            $config['cur_tag_close'] = '</li>';

            $config['next_tag_open'] = '<li>';
            $config['next_tag_close'] = '</li>';

            $config['prev_tag_open'] = '<li>';
            $config['prev_tag_close'] = '</li>';

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

            $data['pagination_per_page'] = $this->pagination_per_page;

            # loading the view-part...
            echo $this->load->view('admin/holy_place/admin_prayer_councillor_ajax.phtml', $data, TRUE);
        } catch (Exception $err_obj) {
            show_error($err_obj->getMessage());
        }
    }

	public function add_prayer_councillor () { 
		//$c_id = $_SESSION['logged_church_id'];
        //$user_id = intval(decrypt($this->session->userdata('user_id')));
		$this->session->set_userdata('search_condition', '');
        $posted=array();
        $this->data["posted"]=$posted;/*don't change*/    
        $data = $this->data;
		parent::_set_title("::: COGTIME Xtian network :::");
		parent::_set_meta_desc("::: COGTIME Xtian network :::");
		parent::_set_meta_keywords("::: COGTIME Xtian network :::");
           
		
		if(count($_POST)>0)
        {
            $data['searchemail'] = $_POST['searchemail'];
            if($_POST['subadminaccess']==1)
            {
                $data = array(
                           'is_councillor' => 1
                           );
               
                $this->db->where('id', $_POST['assign']);
                $res = $this->db->update('cg_users', $data);
                header('location:'.base_url().'admin/holy-place/prayer_councillor'); 
            }
            else
            {
                $s_where = " WHERE 1 AND i_isdeleted=1 AND s_email='".$_POST['searchemail']."'";
                $sql = 'select s_email from cg_users '.$s_where.'';
                        //echo $sql;
                $query = $this->db->query($sql);
                $result = $query->result();
				
				if(count($result) == 0)
                {
                    $_SESSION['addsubadminmsg'] = 'Wrong mail id';
                }
                else
                {
					$s_where = " WHERE s_email='".$_POST['searchemail']."'";
					$data['member_arr'] =   $this->users_model->user_list($s_where, '', '', ' `id` DESC  ');
                   //pr($data['member_arr']);
                    if(count($data['member_arr']) == 0)
                    {
                        $_SESSION['addsubadminmsg'] = 'Mail id does not exists in member list';
                    } 
                    else if($data['member_arr'][0]['is_councillor'] == 1)
                    {
                        $_SESSION['addsubadminmsg'] = 'Already a prayer councillor';
                    }  
                } 
            }
            
            
        }
        
        $VIEW = "admin/holy_place/add_prayercouncillor.phtml";
        

        parent::_render($data, $VIEW);
              
		}

	function delete_prayer_councillor() {
		parent::_add_js_arr( array( 
                                        'js/lightbox.js','js/ModalDialog.js'
										                                    ));
                                        
        parent::_add_church_css_arr( array('css/church.css','css/church_admin.css') );
		
        $groupid = $this->input->post('gr_id');
		//echo $_SESSION['logged_church_id'];exit;
       // $member_arr = $this->prayer_group_model->get_members_by_grpid($groupid);
        //pr($member_arr);
        $data = array(
                           'is_councillor' => 0
                           );
               
		$this->db->where('id', $groupid);
		$res = $this->db->update('cg_users', $data);
		//echo $this->db->last_query();
		
        ob_start();
        $this->ajax_pagination(0);
        $content = ob_get_contents();
        $content_obj = json_decode($content);
        $data['result_content'] = $content_obj->grp_html;
        ob_end_clean();
        echo json_encode(array('html' => $data['result_content'], 'msg' => 'Prayer Councillor Deleted Successfully'));
        exit;
    }
	
}

// end of controller