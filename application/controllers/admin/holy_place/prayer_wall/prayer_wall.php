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
* @link model/ring_categories_model.php
* @link views/##
*/

class Prayer_wall extends Admin_base_Controller
{
    private $prayer_wall_pagination_per_page= 10;
   
    

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
           //$this->load->model("ring_categories_model");
            $this->load->model("prayer_wall_model");
           $this->load->helper('common_option_helper.php');
           
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    // "index" function definition...
    public function index($page=0) 
    {

        try
        {
            
            # adjusting header & footer sections [Start]...
            $data = $this->data;
            parent::_set_title("::: COGTIME Xtian network :::");
            parent::_set_meta_desc("::: COGTIME Xtian network :::");
            parent::_set_meta_keywords("::: COGTIME Xtian network :::");
            parent::_add_js_arr( array( 'js/lightbox.js',
                                        'js/jquery.dd.js',
                                        'js/jquery.form.js',
                                        'js/jquery/JSON/json2.js',
                                         'js/backend/manage_product_categories.js') );
                                        
             parent::_add_css_arr( array('css/dd.css'
                                        ) );
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 7;
            $data['submenu'] = 5;
         
         
            //// Page
            $URI_SEGMENT = $this->uri->segment(3);
            $page = ( is_int($URI_SEGMENT) )? $URI_SEGMENT: 0;
            if( !is_numeric($URI_SEGMENT) ) {
                $this->session->unset_userdata('current_page_prayer_wall');
                $this->session->unset_userdata('search_condition_prayer_wall');
                
                $this->session->unset_userdata('search_key_country');
                $this->session->unset_userdata('search_key_state');
                $this->session->unset_userdata('search_key_city');
                $this->session->unset_userdata('search_key_dt_from');
                $this->session->unset_userdata('search_key_dt_to');
                $this->session->unset_userdata('search_key_posted_by');
            }
            //// Page
         
         
            
            // fetching data
            $WHERE_COND = " WHERE 1 ";
            if($this->session->userdata('search_condition_prayer_wall')!='')
            {
                $WHERE_COND = $this->session->userdata('search_condition_prayer_wall');
                $data['country'] = ($this->session->userdata('search_key_country')!='')? $this->session->userdata('search_key_country') : '';
                $data['state'] = ($this->session->userdata('search_key_state')!='')? $this->session->userdata('search_key_state') : '';
                $data['city'] = ($this->session->userdata('search_key_city')!='')? $this->session->userdata('search_key_city') : '';
                $data['date_from'] = ($this->session->userdata('search_key_dt_from')!='')? $this->session->userdata('search_key_dt_from') : '';
                $data['date_to'] = ($this->session->userdata('search_key_dt_to')!='')? $this->session->userdata('search_key_dt_to') : '';
                $data['posted_by'] = ($this->session->userdata('search_key_posted_by')!='')? $this->session->userdata('search_key_posted_by') : '';
            }   
            $this->session->set_userdata('search_condition_prayer_wall',$WHERE_COND);
           if($this->session->userdata('current_page_prayer_wall')!='')
                $page=$this->session->userdata('current_page_prayer_wall');
            if($page!=0)
                $this->session->set_userdata('current_page_prayer_wall',$page);
                

            $order_by = "`id` ASC ";
            
            ob_start();
            $this->prayer_wall_ajax_pagination($page);
            $data['result_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
            
            $this->session->set_userdata('current_page_prayer_wall','');
            # rendering the view file...
            $VIEW_FILE = "admin/holy_place/prayer_wall/prayer_wall.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }


    # function to load ajax-pagination [AJAX CALL]...
    public function prayer_wall_ajax_pagination($page=0)
    {
        try
        {
            ## seacrh conditions : filter ############
         $WHERE_COND = '';
         $s_where = " WHERE 1";
         if($this->input->post('if_posted'))
         //if($_POST)
         {
             
             if($this->input->post('country')!='-1')
             {
                 $country = decrypt($this->input->post('country'));
                 $s_where.=" AND mst_c.id = {$country}";
                 $this->session->set_userdata('search_key_country',$this->input->post('country'));
             }
             if($this->input->post('txt_state')!='')
             {
                 $state = $this->input->post('txt_state');
                 $s_where.=" AND u.s_state LIKE '{$state}%'";
                 $this->session->set_userdata('search_key_state',$state);
             }
             if($this->input->post('txt_city')!='')
             {
                 $city = $this->input->post('txt_city');
                 $s_where.=" AND u.s_city LIKE '{$city}%'";
                 $this->session->set_userdata('search_key_city',$city);
             }
             if($this->input->post('date_to1')!='')
             {
                 $start_date=$this->input->post('date_to1');
                 $s_where.=" AND DATE(dt_start_date) >= '".get_db_dateformat($start_date,'/')."'";
                 $this->session->set_userdata('search_key_dt_from',$start_date);
             }
             if($this->input->post('date_to2')!='')
             {
                 $end_date=$this->input->post('date_to2');
                 $s_where.=" AND  DATE(dt_end_date) <= '".get_db_dateformat($end_date,'/')."'";
                 $this->session->set_userdata('search_key_dt_to',$end_date);
             }
             if($this->input->post('posted_by')!='')
             {
                 $posted_by = $this->input->post('posted_by');
                 $s_where.=" AND CONCAT(u.s_first_name,' ',u.s_last_name) LIKE '{$posted_by}%'";
                 $this->session->set_userdata('search_key_posted_by',$posted_by);
             }
             $this->session->set_userdata('search_condition_prayer_wall','');
             $this->session->set_userdata('search_condition_prayer_wall',$s_where);
         }
           
           

            
            $s_where=$this->session->userdata('search_condition_prayer_wall');
            
            $order_by = "";
               $result = $this->prayer_wall_model->get_all_prayers($s_where,$page,$this->prayer_wall_pagination_per_page,$order_by);
            $resultCount = count($result);
            
            $total_rows = $this->prayer_wall_model->get_count_all_prayers($s_where);
            //echo $total_rows;exit;
            //echo $this->db->last_query(); exit;
            //echo "total : ".$total_rows;exit;
            ## end seacrh conditions : filter ############
            
            //pr($result,1);
            #Jquery Pagination Starts
               $this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/holy_place/prayer_wall/prayer_wall/prayer_wall_ajax_pagination";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->prayer_wall_pagination_per_page;
            $config['uri_segment'] = ($this->session->userdata('current_page_prayer_wall')!='')? 3:6;
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
          echo  $this->load->view('admin/holy_place/prayer_wall/prayer_wall_ajax.phtml', $data,TRUE);
         }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
        
    }
    
    function show_all()
    {
        $this->session->unset_userdata('current_page_prayer_wall');
        $this->session->unset_userdata('search_condition_prayer_wall');

        $this->session->unset_userdata('search_key_country');
        $this->session->unset_userdata('search_key_state');
        $this->session->unset_userdata('search_key_city');
        $this->session->unset_userdata('search_key_dt_from');
        $this->session->unset_userdata('search_key_dt_to');
        $this->session->unset_userdata('search_key_posted_by');
        ob_start();
        $this->prayer_wall_ajax_pagination();
        $html = ob_get_contents(); //pr($data['result_content']);
        ob_end_clean();
        
        echo json_encode(array('html'=>$html));
    }
    
    function show_desc()
    {
        $id = $this->input->post('id');
        
        $where = " WHERE p.id={$id}";
        $res = $this->prayer_wall_model->get_all_prayers($where);

        $res[0]['s_image_name'] = base_url().'uploads/prayer_wall_photos/'.getThumbName($res[0]['s_image_name'],'mid');
        $res[0]['dt_insert_date'] = get_time_elapsed($res[0]['dt_insert_date']);
        $res[0]['dt_start_date'] = getShortDateWithTime($res[0]['dt_start_date'],6);
        $res[0]['dt_end_date'] = getShortDateWithTime($res[0]['dt_end_date'],6);
        $res[0]['s_description'] = html_entity_decode(htmlspecialchars_decode($res[0]['s_description']),ENT_QUOTES,'utf-8');
        
        //pr($res[0]);
        
        echo json_encode(array( 'res' => $res[0]
                                )
                        );
        
    }
    
    function change_status()
    {
        $id = $this->input->post('id');
        $current_status = $this->input->post('status');
        
        $data['i_isenabled'] = 3 - $current_status;
        $this->prayer_wall_model->change_status_prayer_wall($data,$id);
        
        echo json_encode(array('status'=>$data['i_isenabled']));
        
    }
    
    function delete_prayer_wall($current_page)
    {
        $prayer_req_id = $this->input->post('id');
        $this->prayer_wall_model->delete_by_id($prayer_req_id);
        
        ob_start();
        $this->prayer_wall_ajax_pagination($current_page);
        $html = ob_get_contents(); //pr($data['result_content']);
        ob_end_clean();
        
        echo json_encode(array('html'=>$html));
    }
    
    function show_testimony()
    {
        $prayer_wall_id = $this->input->post('id');
        
        $res = $this->prayer_wall_model->get_testimonial_detail_by_prayer_wall_id($prayer_wall_id);
       // pr($res);
        if(count($res))
        {
            $mode = "Update";
			$contents = get_unformatted_string_edit(br2nl($res['s_description']));
            echo json_encode(array('html'=>$res,'mode'=>$mode, 'contents'=>$contents ));
        }
            
        else
        {
            $mode = "Add";
            echo json_encode(array('mode'=>$mode));
        }
        
        //echo json_encode(array('html'=>$res));
    }
    
    function post_testimony()
    {

        $info['i_prayer_req_id '] = $this->input->post('prayer_wall_id');
        //$info['s_message'] = $this->input->post('message');
        $info['s_description'] = nl2br( htmlspecialchars(trim($this->input->post('message')), ENT_QUOTES, 'utf-8') );
        $mode = $this->input->post('mode');
        $testimony_id = $this->input->post('testimony_id');
        if($info['s_description']=='')
        {
            echo json_encode(array('result'=>'failure','msg'=>'Please enter some text.'));
            exit;
        }
        if($mode=='Add')
        {
            $info['dt_insert_date'] = get_db_datetime();
            $l_id = $this->prayer_wall_model->insert_testimony($info);
            if($l_id)
            {
                $msg = "Testimony Successfully inserted.";
                $result = 'success';
            }
            else
            {
                $msg = "Error occured. Try again.";
                $result = 'failure';
            }
            echo json_encode(array(
                                    'result' => $result,
                                    'msg'=>$msg
                                    )
                            );
            exit;
        }
        else
        {
            $this->prayer_wall_model->update_testimony($info,$testimony_id);
            echo json_encode(array('result'=>'success','msg'=>'Testimony Successfully updated'));
        }
        
    }
    
    
    
    
    
    
    
        
    
    
    
    
    
    
    
     
    
}   // end of controller...
