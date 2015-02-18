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

class Intercession extends Admin_base_Controller
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
            $this->load->model("user_notifications_model");
            $this->load->model("intercession_model");
            $this->load->model("prayer_wall_photos_model");
			 $this->load->model("users_model");
            
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
            $data['submenu'] = 6;
         
            
            //// Page
            $URI_SEGMENT = $this->uri->segment(3);
            $page = ( is_int($URI_SEGMENT) )? $URI_SEGMENT: 0;
            if( !is_numeric($URI_SEGMENT) ) {
                $this->session->unset_userdata('current_page_intercession');
                $this->session->unset_userdata('search_condition');
                $this->session->unset_userdata('search_key_from');
                $this->session->unset_userdata('search_key_to');
            }
            //// Page
            
            // fetching data
            $WHERE_COND = " WHERE 1 ";
            $data['search_from'] = '';
            $data['search_to'] = '';
            if($this->session->userdata('search_condition')!='')
            {
                $WHERE_COND = $this->session->userdata('search_condition');
                $data['search_from'] = ($this->session->userdata('search_key_from')!='')? $this->session->userdata('search_key_from') : '';
                $data['search_to'] = ($this->session->userdata('search_key_to')!='')? $this->session->userdata('search_key_to') : '';
            }
            $this->session->set_userdata('search_condition',$WHERE_COND);
            //$this->session->set_userdata('search_condition','');
            if($this->session->userdata('current_page_intercession')!='')
                $page=$this->session->userdata('current_page_intercession');
            if($page!=0)
                $this->session->set_userdata('current_page_intercession',$page);
                
            $order_by = "`id` ASC ";
            
            
            ob_start();
            $this->intercession_ajax_pagination($page);
            $data['result_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
            
            $this->session->set_userdata('current_page_intercession','');
                
   //echo "session : ".$this->session->userdata('search_condition');exit;
            # rendering the view file...
            $VIEW_FILE = "admin/holy_place/intercession.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }


    # function to load ajax-pagination [AJAX CALL]...
    public function intercession_ajax_pagination($page=0)
    {
        try
        {
            ## seacrh conditions : filter ############
         $s_where = " WHERE 1 ";
         
         //$s_where = $this->session->userdata('search_condition');
         if($this->input->post('if_post')=='y')
         {
            
             if($this->input->post('date_to1')!='')
             {
                 $start_date=$this->input->post('date_to1');
                 $s_where.=" AND DATE(dt_start_date) >= '".get_db_dateformat($start_date,'/')."'";
                 $this->session->set_userdata('search_key_from',$start_date);
             }
             if($this->input->post('date_to2')!='')
             {
                 $end_date=$this->input->post('date_to2');
                 $s_where.=" AND  DATE(dt_end_date) <= '".get_db_dateformat($end_date,'/')."'";
                 $this->session->set_userdata('search_key_to',$end_date);
             }
             $this->session->set_userdata('search_condition',$s_where);
             
             
             
         }
           

           
            
            $s_where = $this->session->userdata('search_condition');
            
            $order_by = "";
               $result = $this->intercession_model->get_all_intercession($s_where,$page,$this->prayer_wall_pagination_per_page,$order_by);
            $resultCount = count($result);
            
            $total_rows = $this->intercession_model->get_count_all_intercession($s_where);
            
            if(!count($result) && $total_rows)
            {
                $page = $page - $this->prayer_wall_pagination_per_page;
                $result = $this->intercession_model->get_all_intercession($s_where,$page,$this->prayer_wall_pagination_per_page,$order_by);
                
            }
            //echo $total_rows;exit;
            //echo $this->db->last_query(); exit;
            //echo "total : ".$total_rows;exit;
            ## end seacrh conditions : filter ############
            
            //pr($result,1);
            #Jquery Pagination Starts
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/holy_place/intercession/intercession_ajax_pagination";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->prayer_wall_pagination_per_page;
            $config['uri_segment'] = ($this->session->userdata('current_page_intercession')!='')? 3:5;
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
          echo  $this->load->view('admin/holy_place/intercession_ajax.phtml', $data,TRUE);
         }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
        
    }
    
    
    function show_all()
    {

        $this->session->unset_userdata('current_page_intercession');
        $this->session->unset_userdata('search_condition');
        $this->session->unset_userdata('search_key_from');
        $this->session->unset_userdata('search_key_to');
        
        ob_start();
        $this->intercession_ajax_pagination();
        $html = ob_get_contents();
        ob_end_clean();
        
        echo json_encode(array('html'=>$html));
    }
    
    
    function delete_intercession($current_page)
    {
        $id=$this->input->post('id');
        //$current_page = $this->input->post('current_page');
        
        /*if($this->session->userdata('current_page_intercession'))
            echo "set session";
        else
            echo "not set";
        */
        
        //$this->session->set_userdata('current_page_intercession',$current_page);
        
        $this->intercession_model->delete_by_id($id);
        
        ob_start();
        $this->intercession_ajax_pagination($current_page);
        $html = ob_get_contents();
        ob_end_clean();
        
        
        //$this->session->set_userdata('current_page_intercession','');
        
        $result='success';
        echo json_encode(array('result'=>$result,'response'=>$html));
    }
    
    function change_status()
    {
        $id = $this->input->post('id');
        $current_status = $this->input->post('status');
        
        $data['i_is_enable'] = 1 - $current_status;
        $this->intercession_model->change_status_intercession($data,$id);
       
	    ob_start();
		$this->intercession_ajax_pagination($page);
		$result_content = ob_get_contents(); //pr($data['result_content']);
		ob_end_clean(); 
		
		
        echo json_encode(array('status'=>$data['i_is_enable'], 'result_content'=> $result_content));
        
    }
        
        
    function show_testimony()
    {
        $intercession_id = $this->input->post('id') ;
        
        $res = $this->intercession_model->get_testimony_by_intercession_id($intercession_id);
        
		//pr($res);
        if(count($res))
        {
            $mode = "Update";
			$contents = get_unformatted_string_edit(br2nl($res['s_message']));
            echo json_encode(array('html'=>$res,'mode'=>$mode, 's_message'=> $contents));
        }
            
        else
        {
            $mode = "Add";
            echo json_encode(array('mode'=>$mode));
        }
    
    }
    
    function post_testimony()
    {
        $info['i_id_intercession_wall_post '] = $this->input->post('intercession_id');
        //$info['s_message'] = $this->input->post('message');
        $info['s_message'] = nl2br( htmlspecialchars(trim($this->input->post('message')), ENT_QUOTES, 'utf-8') );
        $mode = $this->input->post('mode');
        $testimony_id = $this->input->post('testimony_id');
        if($info['s_message']=='')
        {
            echo json_encode(array('result'=>'failure','msg'=>'Please enter some text.'));
            exit;
        }
        if($mode=='Add')
        {
            $info['dt_insert_time'] = get_db_datetime();
            $l_id = $this->intercession_model->insert_testimony($info);
            if($l_id)
            {
                $msg = "Testimony Successfully inserted.";
                $result = 'success';
            }
            else
            {
                $msg = "Error occured. Try agaiin.";
                $result = 'failure';
            }
            echo json_encode(array(
                                    'result' => $result,
                                    'msg'=>$msg
                                    )
                            );
        }
        else
        {
            $this->intercession_model->update_testimony($info,$testimony_id);
            echo json_encode(array('result'=>'success','msg'=> 'Testimony Successfully updated'));
        }
        
    }
    
    
    
    
    //================================= edit info ====================================
    function edit_intercession($current_page,$id)
    {
        try
        {
            
            //setting current page in session
            $this->session->set_userdata('current_page_intercession',$current_page);
            
            
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
            $data['submenu'] = 6;
         
            $data['mode'] = 'edit';
            $data['current_page'] = $current_page;
            
            $data['posted']= $this->intercession_model->get_info_by_intercession_id($id);
            $data['prayer_photo'] = $this->prayer_wall_photos_model->get_all_prayer_wall_photos();
           
            
           # $posted["dt_start_date"]= getShortDate($info["dt_start_time"],3);
           # $posted["start_time"]= getShortDateWithTime($info["dt_start_time"],8);
            

            $start_date = $data['posted']['dt_start_date'];
            $data['posted']['dt_start_date'] = getShortDate($start_date,3);
            $data['posted']['s_start_time'] = getShortDateWithTime($start_date,8);
            

            
            
            $end_date = $data['posted']['dt_end_date'];
            $data['posted']['dt_end_date'] = getShortDate($end_date,3);
            $data['posted']['s_end_time'] = getShortDateWithTime($end_date,8);
			
			$where	= " i_country_id='".$data['posted']["i_country_id"]."'";
            $data['state'] 	= makeOptionState($where, encrypt($data['posted']["i_state_id"]));
			
			$where1	= " i_state_id='".$data['posted']["i_state_id"]."'";
            $data['city'] 	= makeOptionCity($where1,encrypt($data['posted']["i_city_id"]));
            //pr($data['posted'],1);
            
            

           if($_POST)
            {
                $posted=array();
                $posted["s_subject"]= $this->input->post("s_subject");
                
                
                $posted["s_city"]= intval(decrypt(trim($this->input->post("txt_city"))));
                $posted["s_state"]= intval(decrypt(trim($this->input->post("txt_state"))));
                
                
                $posted["i_country_id"]= intval(decrypt(trim($this->input->post("country"))));
                
                //echo intval(trim($this->input->post("country")));
                $posted["dt_start_date"]= trim($this->input->post("date_to1"));
                $posted["s_start_time"]= trim($this->input->post("txt_start_time"));
                
                $posted["dt_end_date"]= trim($this->input->post("date_to2"));
                $posted["s_end_time"]= trim($this->input->post("txt_end_time"));

                $posted["s_description"]= get_formatted_string(trim($this->input->post("txt_desc")));
                
                $posted['s_image_name'] = $this->input->post('hd_image_name');
				$posted["req_type"]= get_formatted_string(trim($this->input->post("req_type")));
                //$posted["dt_start_date"]= $posted["start_date"];
                //pr($posted);
                
                
                //validation starts here 
                //pr($posted);
                
                

                
                
                    
                $this->form_validation->set_message('required', '* Required Field.');        
                #$this->form_validation->set_rules('txt_title', "* required " ,'trim|required');

                $this->form_validation->set_rules('txt_city', "* required " ,'trim|required');
                $this->form_validation->set_rules('txt_state', "* required " ,'trim|required');
                $this->form_validation->set_rules('date_to1', "* required " ,'trim|required');
                $this->form_validation->set_rules('txt_start_time', "* required " ,'trim|required|callback_check_time');
                $this->form_validation->set_rules('date_to2', "* required " ,'trim|required');
                $this->form_validation->set_rules('txt_end_time', "* required " ,'trim|required|callback_check_time');
                $this->form_validation->set_rules('s_subject', "* required " ,'trim|required');
                
               
                $this->form_validation->set_rules('txt_desc',"* required", 'trim|required');
                
                
                if(trim($this->input->post("country"))  == '-1'){
                    $data['error_country'] = '* Required Field.';
                    
                }
                
				
				if(trim($this->input->post("req_type"))  == '-1'){
                    $data['error_req_type'] = '* Required Field.';
                    
                }
                
            
                //validation ends here
                if ($this->form_validation->run() == FALSE || $data['error_country'] != '' || $data['error_req_type'] != '')
                {
                  
                   ////////Display the add form with posted values within it////
                    $arr = $this->intercession_model->get_info_by_intercession_id($id);
                    $posted['s_image_name'] = $arr['s_image_name'];

                    $data["posted"]=$posted;/*don't change*/
                    $data['posted']['e_request_type'] = $posted["req_type"];
                    
                    
                }
                else
                {
                
                //adding to database
                $info=array();
                #$info["s_title"]= get_formatted_string(trim($posted["s_title"]));
                //$info["i_user_id"] = intval(decrypt($this->session->userdata('user_id')));

                
                $info["s_description"]= get_formatted_string(trim($posted["s_description"]));
                
                if($posted['s_image_name']!='')
                    $info["s_image_name"]= get_formatted_string(trim($posted["s_image_name"]));
                
                
                $info["i_city_id"]= $posted["s_city"];
                $info["i_state_id"]= $posted["s_state"];
				$info["s_subject"]= get_formatted_string($posted["s_subject"]);
				
                
               
                $info["i_country_id"]= intval(trim($posted["i_country_id"]));
                //$info['dt_start_date'] = get_db_dateformat($posted['dt_start_date'],'/');
                $info1['s_start_time'] = $posted['s_start_time'];
                //$info['dt_end_date'] = get_db_dateformat($posted['dt_end_date'],'/');
                $info1['s_end_time'] = $posted['s_end_time'];
                
                $start_time  =  get_db_dateformat($posted["dt_start_date"],'/').' '.$posted["s_start_time"] ; 
                $info["dt_start_date"] = $start_time;
                $end_time  =  get_db_dateformat($posted["dt_end_date"],'/').' '.$posted["s_end_time"] ; 
                $info["dt_end_date"] = $end_time;
				$info["e_request_type"] = get_formatted_string(trim($this->input->post("req_type")));


                //$info['dt_insert_date'] = get_db_datetime();
                
                //pr($info,1);
                $i_newid = $this->intercession_model->edit_intercession($info,$id); //echo $this->db->last_query();
                $re_page = admin_base_url() ."holy-place/".$current_page."/intercession.html";
                header("location:".$re_page);
                exit;
                    
                }
                
            }
          
            # rendering the view file...
            $VIEW_FILE = "admin/holy_place/add_edit_intercession.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
        
    }
    
    
    
    
    //----------------------------------- add denomination ---------------------------------
    function add_intercession()
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
            $data['submenu'] = 6;
         
            $data['mode'] = 'add';
            $data['prayer_photo'] = $this->prayer_wall_photos_model->get_all_prayer_wall_photos();
            
            if($_POST)
            {
                $posted=array();
                #$posted["s_title"]= get_formatted_string(trim($this->input->post("txt_title")));
                
                $posted["s_subject"]= $this->input->post("s_subject");
                $posted["s_city"]= intval(decrypt(trim($this->input->post("txt_city"))));
                $posted["s_state"]= intval(decrypt(trim($this->input->post("txt_state"))));
                
                
                $posted["i_country_id"]= intval(decrypt(trim($this->input->post("country"))));
                
                //echo intval(trim($this->input->post("country")));
                $posted["dt_start_date"]= trim($this->input->post("date_to1"));
                $posted["s_start_time"]= trim($this->input->post("txt_start_time"));
                
                $posted["dt_end_date"]= trim($this->input->post("date_to2"));
                $posted["s_end_time"]= trim($this->input->post("txt_end_time"));

                $posted["s_description"]= get_formatted_string(trim($this->input->post("txt_desc")));
                
                $posted['s_image_name'] = $this->input->post('hd_image_name');
				$posted["req_type"]= get_formatted_string(trim($this->input->post("req_type")));
                //$posted["dt_start_date"]= $posted["start_date"];
                //pr($posted);
                
                
                //validation starts here 
                //pr($posted);
                
                

                
                
                    
                $this->form_validation->set_message('required', '* Required Field.');        
                #$this->form_validation->set_rules('txt_title', "* required " ,'trim|required');

                $this->form_validation->set_rules('txt_city', "* required " ,'trim|required');
                $this->form_validation->set_rules('txt_state', "* required " ,'trim|required');
                $this->form_validation->set_rules('date_to1', "* required " ,'trim|required');
                $this->form_validation->set_rules('txt_start_time', "* required " ,'trim|required|callback_check_time');
                $this->form_validation->set_rules('date_to2', "* required " ,'trim|required');
                $this->form_validation->set_rules('txt_end_time', "* required " ,'trim|required|callback_check_time');
                $this->form_validation->set_rules('s_subject', "* required " ,'trim|required');
                
               
                $this->form_validation->set_rules('txt_desc',"* required", 'trim|required');
                $this->form_validation->set_rules('hd_image_name',"* required", 'trim|required');
                
                
                if(trim($this->input->post("country"))  == '-1'){
                    $data['error_country'] = '* Required Field.';
                    
                }
                
				if(trim($this->input->post("req_type"))  == '-1'){
                    $data['error_req_type'] = '* Required Field.';
                    
                }
            
                //validation ends here
                if ($this->form_validation->run() == FALSE || $data['error_country'] != '' || $data['error_req_type'] != '' )
                {
                  
                   ////////Display the add form with posted values within it////
                    $data["posted"]=$posted;/*don't change*/
                    
                    
                    
                }
                else
                {
                
                //adding to database
                $info=array();
                #$info["s_title"]= get_formatted_string(trim($posted["s_title"]));
                $info["i_user_id"] = intval(decrypt($this->session->userdata('user_id')));
				$info["e_request_type"] = get_formatted_string(trim($this->input->post("req_type")));
                
                $info["s_description"]= get_formatted_string(trim($posted["s_description"]));

                $info["s_image_name"]= get_formatted_string(trim($posted["s_image_name"]));
                
                $info["i_city_id"]= $posted["s_city"];
                $info["i_state_id"]= $posted["s_state"];
                $info["s_subject"]= get_formatted_string($posted["s_subject"]);
               
                $info["i_country_id"]= $posted["i_country_id"];
                //$info['dt_start_date'] = get_db_dateformat($posted['dt_start_date'],'/');
    #$info['s_start_time'] = $posted['s_start_time'];
                //$info['dt_end_date'] = get_db_dateformat($posted['dt_end_date'],'/');
    #$info['s_end_time'] = $posted['s_end_time'];
                
                $start_time  =  get_db_dateformat($posted["dt_start_date"],'/').' '.$posted["s_start_time"] ; 
                $info["dt_start_date"] = $start_time;
                $end_time  =  get_db_dateformat($posted["dt_end_date"],'/').' '.$posted["s_end_time"] ; 
                $info["dt_end_date"] = $end_time;


                $info['dt_insert_date'] = get_db_datetime();
                
                //pr($info,1);
                $i_newid = $this->intercession_model->insert_intercession($info); //echo $this->db->last_query();
				
				if($info['e_request_type']== 'Emergency'){
					$s_user_where = "WHERE i_status = 1  AND i_isdeleted = 1";
					$user_array = $this->users_model->user_list();
					
					if(count($user_array)){
						foreach($user_array as $val){
								 
								$notification_arr  = array(); 
								#add notifications ##
								$notification_arr['i_requester_id'] = 0;
								$notification_arr['i_accepter_id'] = $val['id'];
								$notification_arr['s_type'] = 'emergency_prayer_click';
								$notification_arr['dt_created_on'] = get_db_datetime();
								$ret = $this->user_notifications_model->insert($notification_arr);	
								## send im and add notifications ##
								
						}
					}
				}
				
                $re_page = admin_base_url() ."holy-place/intercession.html";
                header("location:".$re_page);
                exit;
                    
                }
                
            }
           
          
            # rendering the view file...
            $VIEW_FILE = "admin/holy_place/add_edit_intercession.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }   //end of add_intercession
    
    
    

    
    
    
    function check_time($s_time)
    {

        /*if(!preg_match("/^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])(:([0-5]?[0-9]))?$/", $s_time))*/
        if(!preg_match("/^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])?$/", $s_time))
        {
            $this->form_validation->set_message('check_time', '* Please input time in 24 hours format. ');
            return false;
        }

        else
        return true;

    }
    
    
    
    function show_desc()
    {
        $id = $this->input->post('id');
        
        $res = $this->intercession_model->get_info_by_intercession_id($id);

        $res['s_image_name'] = base_url().'uploads/prayer_wall_photos/'.getThumbName($res['s_image_name'],'mid');
        $res['dt_insert_date'] = get_time_elapsed($res['dt_insert_date']);
        $res['dt_start_date'] = getShortDateWithTime($res['dt_start_date'],6);
        $res['dt_end_date'] = getShortDateWithTime($res['dt_end_date'],6);
        
        $res['s_description'] = html_entity_decode(htmlspecialchars_decode($res['s_description']),ENT_QUOTES,'utf-8');

        
        echo json_encode(array( 'res' => $res
                                )
                        );
    }
    
    

    
    
     
    
}   // end of controller...