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

class Ring_categories extends Admin_base_Controller
{
	private $pagination_per_page= 10;

    

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
            $this->load->model("ring_categories_model");
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
									     'js/backend/manage_ring_categories.js') );
                                        
             parent::_add_css_arr( array('css/dd.css',
                                        ) );
            # adjusting header & footer sections [End]...
			$data['top_menu_selected'] = 3;
			$data['submenu'] = 4;
         
            
			// fetching data
			$WHERE_COND = " WHERE 1 ";
			$this->session->set_userdata('search_condition',$WHERE_COND);
			$page=0;
			$order_by = "`id` ASC ";
			
			ob_start();
            $this->ajax_pagination();
            $data['result_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
			
                
            # rendering the view file...
            $VIEW_FILE = "admin/social_hub/rings/ring_categories.phtml";
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
			## seacrh conditions : filter ############
		 $WHERE_COND = '';
		   if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) :
			  
			  $WHERE_COND = " WHERE 1  ";
			  $txt_tweet = get_formatted_string(trim($this->input->post('txt_tweet')));
			  $WHERE_COND .= ($txt_tweet=='')?'':" AND ( T.s_tweet_text LIKE '%".$txt_tweet."%'  
			  											  OR CONCAT(U.s_first_name,' ',U.s_last_name) LIKE  '%".$txt_tweet."%')";
			  $this->session->set_userdata('search_condition',$WHERE_COND);
		  
		  
		   endif;  
		    $s_where = $this->session->userdata('search_condition');
			$s_where.=' AND C.i_parent_category=0';
			
			$order_by = "`id` DESC ";
		   	$result = $this->ring_categories_model->get_list($s_where,$page,$this->pagination_per_page,$order_by);
            $resultCount = count($result);
			#echo $this->db->last_query(); exit;
			$total_rows = $this->ring_categories_model->get_list_count($s_where);
			
			if( ( !is_array($result) || !count($result) ) && $total_rows ) {
                $page = $page - $this->pagination_per_page;
                
                $result = $this->ring_categories_model->get_list($s_where, $page, $this->pagination_per_page,$ORDER_BY);
            }
			## end seacrh conditions : filter ############
			
			//pr($result,1);
			#Jquery Pagination Starts
           	$this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/social_hub/ring_categories/ajax_pagination";
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
          echo  $this->load->view('admin/social_hub/rings/ring_categories_ajax.phtml', $data,TRUE);
		 }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
    }
	
	
	
	function delete_information()
    {
        $id=$this->input->post('id');
        
        $i_ret=$this->ring_categories_model->delete_by_id($id);
        $result='success';
        echo json_encode(array('result'=>$result));
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
                $this->ring_categories_model->update($info,$id);
                $msg = "Ring category updated successfully.";
                $res = 'success';
              
                echo json_encode(array('result'=>$res,
                                         'msg'=>$msg ,
                                         'id'=>$id ,
                                         'updated_d_name'=> '<a id="product_'.$info['id'].'" href="'.admin_base_url().'social-hub/ring-categories/'.$id.'/'.my_url($info['s_category_name']).'.html">'.$info['s_category_name'].'</a>'
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
           $res = $this->ring_categories_model->get_by_id($id);
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
                
                $res = $this->ring_categories_model->insert($info);
                $response='';
                if($res)
                {
                    $msg = "Ring category added successfully.";
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
                                        ) );
                                        
             parent::_add_css_arr( array('css/dd.css',
                                        ) );
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 3;
			$data['submenu'] = 4;
            
            //empty the session
             $this->session->set_userdata('search_condition','');
            // fetching data
            $WHERE_COND = " WHERE 1 ";
            $WHERE_COND.= "AND i_parent_category={$id}";     // set the parent id
            $this->session->set_userdata('search_condition',$WHERE_COND);
            $page=0;
            $order_by = "`id` ASC ";
            
            $data['product_category_id'] = $id;
            
            $category_detail = $this->ring_categories_model->get_by_id($id,'', '');
            
            $data['category_name'] = $category_detail['s_category_name'];
            
            
            ob_start();
            $this->chat_sub_category_ajax_pagination();
            $data['result_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
            
                
            # rendering the view file...
            $VIEW_FILE = "admin/social_hub/rings/chat_sub_categories.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }
    
    
    public function chat_sub_category_ajax_pagination($page=0)
    {
        try
        {
            ## seacrh conditions : filter ############
         $WHERE_COND = '';
          
           
           
           
            $s_where = $this->session->userdata('search_condition');
            
            $order_by = "s_category_name ASC ";
               $result = $this->ring_categories_model->get_sub_cat_list($s_where,$page,$this->product_sub_category_pagination_per_page,$order_by);
            $resultCount = count($result);
    //echo $this->db->last_query(); exit;
            $total_rows = $this->ring_categories_model->get_sub_cat_list_count($s_where);
            
            
                        
            if( ( !is_array($result) || !count($result) ) && $total_rows ) {
                $page = $page - $this->product_sub_category_pagination_per_page;
                
                $result = $this->ring_categories_model->get_sub_cat_list($s_where, $page, $this->product_sub_category_pagination_per_page,$ORDER_BY);
            }
            ## end seacrh conditions : filter ############
            
            //pr($result,1);
            #Jquery Pagination Starts
            
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/social_hub/ring_categories/chat_sub_category_ajax_pagination";
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
          echo  $this->load->view('admin/social_hub/rings/chat_sub_categories_ajax.phtml', $data,TRUE);
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
        
        $i_ret=$this->ring_categories_model->delete_sub_cat_by_id($id);
        
        ob_start();
        $this->chat_sub_category_ajax_pagination();
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
                $this->ring_categories_model->update($info,$id);
                $msg = "Ring sub category updated successfully.";
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
           $res = $this->ring_categories_model->get_by_id($id);
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
                
                $res = $this->ring_categories_model->insert($info);
                $response='';
                if($res)
                {
                    $msg = "Ring sub category added successfully.";
                    $res = 'success';
                    ob_start();
                    $this->chat_sub_category_ajax_pagination();
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
    function get_cat_rings()
	{
	$cat_id=$this->input->post('del_id');
	$where=" id!=".$cat_id;
	$options="<option value='-1'> --- </option>";
	$options.=makeOptionRingCategory($where);
	echo json_encode(array('result'=>'success','html'=>$options));
	}
	
	function get_subcat_rings()
	{
	$cat_id=decrypt($this->input->post('del_id'));
	$where=" i_parent_category =".$cat_id;
	$options="<option value='-1'> --- </option>";
	$options.=makeOptionRingSubCategory($where);
	echo json_encode(array('result'=>'success','html'=>$options));
	}
	
	function move_rings()
	{
	$del_id=$this->input->post('del_id');
	$cat_id=decrypt($this->input->post('cat_id'));
	$subcat_id=decrypt($this->input->post('subcat_id'));
	if($this->input->post('subcat_id') != '-1')
	{
	$ret=$this->ring_categories_model->move_ring($del_id,$cat_id,$subcat_id);
	echo json_encode(array('result'=>'success'));
	}
	else
	{
	echo json_encode(array('result'=>'fail'));
	}
	}
//======================================= end of category detail =================================================
    
     
    
}   // end of controller...