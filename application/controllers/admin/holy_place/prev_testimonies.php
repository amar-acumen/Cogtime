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

class Testimonies extends Admin_base_Controller
{
    private $prayer_wall_pagination_per_page= 2;
   
    

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
            $this->load->model("holy_place_model");
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
                                        'js/jquery.dd.js',
                                        'js/jquery.form.js',
                                        'js/jquery/JSON/json2.js',
                                         'js/backend/manage_product_categories.js') );
                                        
             parent::_add_css_arr( array('css/dd.css'
                                        ) );
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 7;
            $data['submenu'] = 6;
         
            
            // fetching data
            $WHERE_COND = " WHERE 1 ";
            
            $this->session->set_userdata('search_condition',$WHERE_COND);
            $page=0;
            $order_by = "`id` ASC ";
            
            ob_start();
            $this->testimonies_ajax_pagination();
            $data['result_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
            
                
            # rendering the view file...
            $VIEW_FILE = "admin/holy_place/testimonies.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }


    # function to load ajax-pagination [AJAX CALL]...
    public function testimonies_ajax_pagination($page=0)
    {
        try
        {
            ## seacrh conditions : filter ############
         $WHERE_COND = '';
         $s_where = $this->session->userdata('search_condition');
         if($_POST)
         {
             if($this->input->post('date_to1')!='')
             {
                 $start_date=$this->input->post('date_to1');
                 $s_where.=" AND dt_start_date >= '".get_db_dateformat($start_date,'/')."'";
             }
             if($this->input->post('date_to2')!='')
             {
                 $end_date=$this->input->post('date_to2');
                 $s_where.=" AND  dt_end_date <= '".get_db_dateformat($end_date,'/')."'";
             }
         }
            
            $s_where.='';
            
            $order_by = "";
               $result = $this->holy_place_model->get_all_testimonies($s_where,$page,$this->prayer_wall_pagination_per_page,$order_by);
            $resultCount = count($result);
            
            $total_rows = $this->holy_place_model->get_count_all_testimonies($s_where);
            //echo $total_rows;exit;
            //echo $this->db->last_query(); exit;
            //echo "total : ".$total_rows;exit;
            ## end seacrh conditions : filter ############
            
            //pr($result,1);
            #Jquery Pagination Starts
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/holy_place/testimonies/testimonies_ajax_pagination";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->prayer_wall_pagination_per_page;
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
          echo  $this->load->view('admin/holy_place/testimonies_ajax.phtml', $data,TRUE);
         }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
        
    }
    
    
    
    function delete_information()
    {
        $id=$this->input->post('id');
        
        //$i_ret=$this->product_categories_model->delete_by_id($id);
        
        ob_start();
        $this->product_category_ajax_pagination();
        $html = ob_get_contents();
        $response = base64_encode($html);
        ob_end_clean();
        
        
        $result='success';
        echo json_encode(array('result'=>$result,'response'=>$response));
    }
        
    
    
    
    
    //================================= edit info ====================================
    function edit_info($id)
    {
        $arr_messages=array();
        if($_POST)
        {
            $id= intval($this->input->post('i_edit_id'));
            $d_name = trim($this->input->post('txt_edit_cat_name'));
            if($d_name=='')
            {
                $arr_messages['edit_cat_name'] = "* Required Field.";
               
            }
            
            if(count($arr_messages)==0)
            {
               
                $info=array();
                $info['s_category_name'] = get_formatted_string($d_name); 
                //$info['id'] = $id;
                $this->product_categories_model->update($info,$id);
                $msg = "Product category updated successfully.";
                $res = 'success';
              
                echo json_encode(array('result'=>$res,
                                         'msg'=>$msg ,
                                         'id'=>$id ,
                                         'updated_d_name'=> $info['s_category_name']
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
           $res = $this->product_categories_model->get_by_id($id);
            //echo $res['s_name'];exit;
            
            echo json_encode(array('s_category_name'=>$res['s_category_name'])); 
        }
        
        
        
        
        
    }
    
    
    
    
    //----------------------------------- add denomination ---------------------------------
    function add_info()
    {
       
        if($_POST)
        {
            $name = trim($this->input->post('txt_cat_name'));
            
            if($name=='')
            {
                $arr_message['cat_name'] = "* Required Field.";
            }
            
            if(count($arr_message)==0)
            {
                $info=array();
                $info['s_category_name'] = get_formatted_string($name); 
                $info['dt_created_on'] = get_db_datetime();
                
                $res = $this->product_categories_model->insert($info);
                $response='';
                if($res)
                {
                    $msg = "Product category added successfully.";
                    $res = 'success';
                    ob_start();
                    $this->product_category_ajax_pagination();
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
    
    
//======================================= category detail =================================================
    function category_detail($id)
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
                                        
             parent::_add_css_arr( array('css/dd.css',
                                        ) );
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 4;
            $data['submenu'] = 2;
         
            
            //empty the session
             $this->session->set_userdata('search_condition','');
            // fetching data
            $WHERE_COND = " WHERE 1 ";
            $WHERE_COND.= "AND i_parent_category={$id}";     // set the parent id
            $this->session->set_userdata('search_condition',$WHERE_COND);
            $page=0;
            $order_by = "`id` ASC ";
            
            $data['product_category_id'] = $id;
            
            $category_detail = $this->product_categories_model->get_by_id($id);
            
            $data['category_name'] = $category_detail['s_category_name'];
            
            
            ob_start();
            $this->product_sub_category_ajax_pagination();
            $data['result_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
            
                
            # rendering the view file...
            $VIEW_FILE = "admin/trade_center/product_categories/product_sub_categories.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }
    
    
    public function product_sub_category_ajax_pagination($page=0)
    {
        try
        {
            ## seacrh conditions : filter ############
         $WHERE_COND = '';
          
           
           
           
            $s_where = $this->session->userdata('search_condition');
            
            $order_by = "s_category_name ASC ";
               $result = $this->product_categories_model->get_sub_cat_list($s_where,$page,$this->product_sub_category_pagination_per_page,$order_by);
            $resultCount = count($result);
    //echo $this->db->last_query(); exit;
            $total_rows = $this->product_categories_model->get_sub_cat_list_count($s_where);
            
            
                        
            if( ( !is_array($result) || !count($result) ) && $total_rows ) {
                $page = $page - $this->product_sub_category_pagination_per_page;
                
                $result = $this->product_categories_model->get_sub_cat_list($s_where, $page, $this->product_sub_category_pagination_per_page,$ORDER_BY);
            }
            ## end seacrh conditions : filter ############
            
            //pr($result,1);
            #Jquery Pagination Starts
            
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/trade_center/product_categories/product_sub_category_ajax_pagination";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->product_sub_category_pagination_per_page;
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
          echo  $this->load->view('admin/trade_center/product_categories/product_sub_categories_ajax.phtml', $data,TRUE);
         }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
        
    }
    
    
    //--------------------- delete --------------------------------
    function delete_sub_information()
    {
        $id=$this->input->post('id');
        
        $i_ret=$this->product_categories_model->delete_sub_cat_by_id($id);
        
        ob_start();
        $this->product_sub_category_ajax_pagination();
        $html = ob_get_contents();
        $response = base64_encode($html);
        ob_end_clean();
        
        
        $result='success';
        echo json_encode(array('result'=>$result,'response'=>$response));
    }
    //--------------------- end delete --------------------------------
    
    
    //================================= edit info ====================================
    function edit_sub_cat_info($id)
    {
        $arr_messages=array();
        if($_POST)
        {
            $id= intval($this->input->post('i_edit_id'));
            $parent_cat_id = intval($this->input->post('edit_parent_prod_id'));
            $d_name = trim($this->input->post('txt_edit_cat_name'));
            if($d_name=='')
            {
                $arr_messages['edit_cat_name'] = "* Required Field.";
               
            }
            
            if(count($arr_messages)==0)
            {
               
                $info=array();
                $info['s_category_name'] = get_formatted_string($d_name); 
                $info['i_parent_category'] = $parent_cat_id; 
                //$info['id'] = $id;
                $this->product_categories_model->update($info,$id);
                $msg = "Product sub category updated successfully.";
                $res = 'success';
              
                echo json_encode(array('result'=>$res,
                                         'msg'=>$msg ,
                                         'id'=>$id ,
                                         'updated_d_name'=> $info['s_category_name']
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
           $res = $this->product_categories_model->get_by_id($id);
            //echo $res['s_name'];exit;
            
            echo json_encode(array('s_category_name'=>$res['s_category_name'])); 
        }
        
        
        
    }
    
    
    
    
    //----------------------------------- add sub-category ---------------------------------
    function add_sub_cat_info()
    {
       
        if($_POST)
        {
            $name = trim($this->input->post('txt_cat_name'));
            $parent_cat_id = intval($this->input->post('add_parent_prod_id'));
            
            if($name=='')
            {
                $arr_message['cat_name'] = "* Required Field.";
            }
            
            if(count($arr_message)==0)
            {
                $info=array();
                $info['s_category_name'] = get_formatted_string($name); 
                $info['dt_created_on'] = get_db_datetime();
                $info['i_parent_category'] = $parent_cat_id;
                
                $res = $this->product_categories_model->insert($info);
                $response='';
                if($res)
                {
                    $msg = "Product sub category added successfully.";
                    $res = 'success';
                    ob_start();
                    $this->product_sub_category_ajax_pagination();
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
    
//======================================= end of category detail =================================================
    
    
     
    
}   // end of controller...