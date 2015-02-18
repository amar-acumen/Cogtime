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
* @link model/scrolling_headlines_model.php
* @link views/##
*/

class Denomination extends Admin_base_Controller
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
           $this->load->model("denomination_model");
          // $this->load->model("scrolling_headlines_model");
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
                                        'js/backend/manage_denomination.js') );
                                        
            parent::_add_css_arr( array() );
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 1;
            $data['submenu'] = 11;
         
            
            // fetching data
            $WHERE_COND = " WHERE 1 ";
            $this->session->set_userdata('search_condition',$WHERE_COND);
            $page=0;
            $order_by = " `id` DESC ";

            ob_start();
            $this->ajax_pagination();
            $data['result_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
            
                
            # rendering the view file...
            $VIEW_FILE = "admin/site_settings/denomination/denomination.phtml";
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
               
            $s_where = $this->session->userdata('search_condition');
            $order_by = " `id` DESC ";
               $result = $this->denomination_model->get_all_denomination_list($s_where,$page,$this->pagination_per_page,$order_by);
            $resultCount = count($result);
    #pr($result);
    #echo "total : ".$resultCount." end";
    #echo $this->db->last_query(); exit;
            $total_rows = $this->denomination_model->denomination_list_count($s_where);
            
            if( ( !is_array($result) || !count($result) ) && $total_rows ) {
                $page = $page - $this->pagination_per_page;
                
                $result = $this->denomination_model->get_all_denomination_list($s_where, $page, $this->pagination_per_page,$ORDER_BY);
            }
            ## end seacrh conditions : filter ############
            
            //pr($result,1);
            #Jquery Pagination Starts
               $this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/site_settings/denomination/ajax_pagination";
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
          echo  $this->load->view('admin/site_settings/denomination/denomination_ajax.phtml', $data,TRUE);
         }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
        
    }
    
    
    
    
    
    //====================================== status change ====================================
    function change_status()
    {
       
        $record_id = $this->input->post('record_id');
        $info['i_status'] = $this->input->post('i_status');
        $cur_status = $this->input->post('cur_status');
        
        $res = $this->denomination_model->update_status($info, $record_id);
        if($res)
        {
            $success = true;
            $msg = "Status updated successfully.";
            $now_status = ($cur_status==1)? 'SHOW' : 'HIDE';
            
        }
        else
        {
            $success = false;
            $msg = "Error. Please try again.";
            $now_status ='';
        }
        
        echo json_encode(array("result"=> $success,
                                "msg" => $msg,
                                "now_status" =>$now_status
                                )
                        );
    }
    
    
    
    
    //==================================== delete info ======================================
    function delete_information()
    {
        $id=$this->input->post('id');
        
        $res = $this->denomination_model->delete_info($id);
        
        if($res)
            $result='success';
        else
            $result='failure';
       
       echo json_encode(array('result'=>$result));
    }
    
    
    //================================= edit info ====================================
    function edit_info($id)
    {
        $arr_messages=array();
        if($_POST)
        {
            $id= intval($this->input->post('i_edit_id'));
            $d_name = trim($this->input->post('txt_edit_denomination'));
            if($d_name=='')
            {
                $arr_messages['denomination_edit'] = "* Required Field";
               
            }
            
            if(count($arr_messages)==0)
            {
               
                $info=array();
                $info['s_name'] = get_formatted_string($d_name); 
                //$info['id'] = $id;
                $res = $this->denomination_model->update_info($info,$id);
                if($res)
                {
                    $msg = "Denomination updated successfully..";
                    $res = 'success';
                }
                else
                {
                    
                    $msg = "Error. Sorry. Try again.";
                    $res = 'failure';
                }
                echo json_encode(array('result'=>$res,
                                         'msg'=>$msg ,
                                         'id'=>$id ,
                                         'updated_d_name'=> $info['s_name']
                                            )
                                    );
            }
            else
            {
                 echo json_encode(array('result'=>'failure',
				 						'arr_messages'=>$arr_messages,
                                        'msg'=>'error'
                                       
                                            )
                                    );
            }
        }
        else
        {
           $res = $this->denomination_model->fetch_this($id);
            //echo $res['s_name'];exit;
            echo json_encode(array('s_denomination'=>$res[0]['s_name'])); 
        }
        
        
        
        
        
    }
    
    
    
    
    //----------------------------------- add denomination ---------------------------------
    function add_info()
    {
       
        if($_POST)
        {
            $name = trim($this->input->post('txt_add_denomination'));
            
            if($name=='')
            {
                $arr_message['denomination'] = "* Required Field";
            }
            
            if(count($arr_message)==0)
            {
                $info=array();
                $info['s_name'] = get_formatted_string($name); 
                $info['dt_created_on'] = get_db_datetime();
                
                $res = $this->denomination_model->add_info($info);
                $response='';
                if($res)
                {
                    $msg = "Denomination inserted successfully..";
                    $res = 'success';
                    ob_start();
                    $this->ajax_pagination();
					$html = ob_get_contents();
                    $response = base64_encode($html);
                    ob_end_clean();
                    
                }
                else
                {
                    
                    $msg = "Error. Sorry. Try again.";
                    $res = 'failure';
                }
                echo json_encode(array('result'=>$res,
                                         'msg'=>$msg ,
                                         'response'=>$response
                                         )
                                    );
            }
            else
            {
                 echo json_encode(array('result'=>'failure',
                                         'arr_messages'=>$arr_message,
                                        'msg'=>'error'
                                       
                                            )
                                    );
            }
        }
    }
    
    
    
    
    
    
    
    
} // end of class