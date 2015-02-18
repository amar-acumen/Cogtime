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

class Edit_prayer_wall extends Admin_base_Controller
{
  
   
    

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
            $this->load->model("prayer_wall_photos_model");
           $this->load->helper('common_option_helper.php');
           
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    // "index" function definition...
    public function index($page=0,$prayer_req_id) 
    {

        try
        {
           $this->session->set_userdata('current_page_prayer_wall',$page);
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
            
            $data['current_page'] = $page;
            
            // fetching data
            $WHERE_COND = " WHERE 1 ";
            
            $this->session->set_userdata('search_condition',$WHERE_COND);

            $order_by = "`id` ASC ";
            
            $data['prayer_photo'] = $this->prayer_wall_photos_model->get_all_prayer_wall_photos();
            $res = $this->prayer_wall_model->get_all_prayers(" WHERE p.id={$prayer_req_id}");
            $data['posted'] = $res[0];
            
            $start_date = $data['posted']['dt_start_date'];
            $data['posted']['dt_start_date'] = getShortDate($start_date,3);
            $data['posted']['s_start_time'] = getShortDateWithTime($start_date,8);
            
            $end_date = $data['posted']['dt_end_date'];
            $data['posted']['dt_end_date'] = getShortDate($end_date,3);
            $data['posted']['s_end_time'] = getShortDateWithTime($end_date,8);
            
            #pr($data['posted']);
            if($_POST)
            {
                $posted=array();
                $posted["req_type"]= get_formatted_string(trim($this->input->post("req_type")));
                
                
               
                $posted["s_subject"]= get_formatted_string($this->input->post('s_subject'));
                //echo intval(trim($this->input->post("country")));
                $posted["dt_start_date"]= trim($this->input->post("date_to1"));
                $posted["s_start_time"]= trim($this->input->post("txt_start_time"));
                
                $posted["dt_end_date"]= trim($this->input->post("date_to2"));
                $posted["s_end_time"]= trim($this->input->post("txt_end_time"));

                $posted["s_description"]= get_formatted_string(trim($this->input->post("txt_desc")));
                
                $posted['s_image_name'] = $this->input->post('hd_image_name');
                //$posted["dt_start_date"]= $posted["start_date"];
                //pr($posted);
                
                
                //validation starts here 
                //pr($posted);
                
                

                
                
                    
                $this->form_validation->set_message('required', '* Required Field.');        
				$this->form_validation->set_rules('s_subject', "* required " ,'trim|required');
                $this->form_validation->set_rules('date_to1', "* required " ,'trim|required');
                $this->form_validation->set_rules('txt_start_time', "* required " ,'trim|required|callback_check_time');
                $this->form_validation->set_rules('date_to2', "* required " ,'trim|required');
                $this->form_validation->set_rules('txt_end_time', "* required " ,'trim|required|callback_check_time');
                
               
               
                $this->form_validation->set_rules('txt_desc',"* required", 'trim|required');
                
                if($posted["req_type"]=='-1')
                {
                    $data['error_req_type'] = "* Required Field.";
                }

                
            
                //validation ends here
                if ($this->form_validation->run() == FALSE || $data['error_req_type']!='' )
                {
                  
                   ////////Display the add form with posted values within it////
                   
                   $arr = $this->prayer_wall_model->get_all_prayers(" WHERE p.id={$prayer_req_id}");
                    $posted['s_image_name'] = $arr[0]['s_image_name'];
                    $data["posted"]=$posted;/*don't change*/
					$data['posted']["e_request_type"] = $posted["req_type"];
                    
					//pr($data["posted"]);
                    
                    
                }
                else
                {
                
                //adding to database
                $info=array();
                

                $info["e_request_type"] = get_formatted_string(trim($this->input->post("req_type")));

                
                $info["s_description"]= get_formatted_string(trim($posted["s_description"]));
                
                if($posted['s_image_name']!='')
                    $info["s_image_name"]= get_formatted_string(trim($posted["s_image_name"]));
                

                
                $start_time  =  get_db_dateformat($posted["dt_start_date"],'/').' '.$posted["s_start_time"] ; 
                $info["dt_start_date"] = $start_time;
                $end_time  =  get_db_dateformat($posted["dt_end_date"],'/').' '.$posted["s_end_time"] ; 
                $info["dt_end_date"] = $end_time;
				$info["s_subject"]= get_formatted_string($this->input->post('s_subject'));

                //$info['dt_insert_date'] = get_db_datetime();
                
                //pr($info,1);
                $i_newid = $this->prayer_wall_model->edit_prayer_wall($info,$prayer_req_id); //echo $this->db->last_query();
                $re_page = admin_base_url() ."holy-place/".$page."/prayer-wall.html";
                header("location:".$re_page);
                exit;
                    
                }
                
            }
            
            
                
            # rendering the view file...
            $VIEW_FILE = "admin/holy_place/prayer_wall/edit_prayer_wall.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }//end of index
    

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
    
    
}   // end of controller