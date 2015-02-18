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
* @link model/my_ring_model.php
* @link views/##
*/

class Rings extends Admin_base_Controller
{
	private $pagination_per_page= 10;
	private $post_pagination_per_page = 20;

    

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
            $this->load->model("my_ring_model");
			$this->load->model("ring_post_model");
			$this->load->model("ring_post_comments_model");
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
									     'js/backend/manage_rings.js') );
                                        
             parent::_add_css_arr( array('css/dd.css',
                                        ) );
            # adjusting header & footer sections [End]...
			$data['top_menu_selected'] = 3;
			$data['submenu'] = 5;
         
            
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
            $VIEW_FILE = "admin/social_hub/rings/rings.phtml";
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
			  
			  $category = decrypt(trim($this->input->post('category')));
			  $WHERE_COND .= ($category=='-1' || $category=='')?'':" AND ( R.i_category_id = '".$category."' )";
			  
			 
			  $txt_ring_name = get_formatted_string(trim($this->input->post('txt_ring_name')));
			  $WHERE_COND .= ($txt_ring_name=='')?'':" AND ( R.s_ring_name LIKE '%".$txt_ring_name."%' )";
			  
			  
			  $this->session->set_userdata('search_condition',$WHERE_COND);
		  
		  
		   endif;  
		    $s_where = $this->session->userdata('search_condition');
			
			$order_by = "R.`id` DESC ";
		   	$result = $this->my_ring_model->get_list($s_where,$page,$this->pagination_per_page,$order_by);
           
		    // pr($result);
			#echo $this->db->last_query(); exit;
			$total_rows = $this->my_ring_model->get_list_count($s_where);
			
			
			## end seacrh conditions : filter ############
			
			//pr($result,1);
			#Jquery Pagination Starts
           	$this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/social_hub/rings/ajax_pagination";
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
          echo  $this->load->view('admin/social_hub/rings/rings_ajax.phtml', $data,TRUE);
		 }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
    }
    
    
	
	function delete_information()
    {
        $id=$this->input->post('id');
        
        $i_ret=$this->my_ring_model->delete_by_id($id);
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
            $max_member = trim($this->input->post('txt_max_member'));
            if($max_member=='')
            {
                $arr_messages['max_member'] = "* Required Field.";
               
            }
			
			if(!(preg_match('/^\d+$/',$max_member)) &&  $max_member!=''){
				
				 $arr_messages['max_member'] = "* Only Integer Value.";
			}
            
            if(count($arr_messages)==0)
            {
               
                $info=array();
                $info['i_member'] = intval($max_member); 
               
                $this->my_ring_model->update($info,$id);
                $msg = "Max Members updated successfully.";
                $res = 'success';
              
                echo json_encode(array('result'=>$res,
                                         'msg'=>$msg ,
                                         'id'=>$id ,
                                         'updated_max_members'=> $info['i_member']
                                            )
                                    );
            }
            else
            {
                 echo json_encode(array('result'=>'failure',
				 						'arr_messages'=>$arr_messages,
                                        'msg'=>'error' )
                                    );
            }
        }
        else
        {
            $res = $this->my_ring_model->get_by_id($id);
			//echo $this->db->last_query();
            //echo $res['s_name'];exit;
            echo json_encode(array('i_member'=>$res['i_member'])); 
        }
        
        
        
        
        
    }
    
    
    
    
   
    
	public function change_status()
	 {
			
			$data = $this->data;
			$page  =  intval($this->input->post('cur_page'));
			$i_status = intval($this->input->post('i_status'));
			$cur_status = intval($this->input->post('cur_status'));
			$ID = $this->input->post('record_id');
			
			
			
			if($this->session->userdata('user_id') !="")
			{
				$this->my_ring_model->change_status($i_status,$ID);
				if($i_status==1)
				   {
					 
						$action_txt =
							 '<input name="" title="ENABLE" type="button" class="btn-06" onclick="javascript:changeStatus(\''.$ID.'\',\'2\',\''.$i_status.'\')"  value="DISABLE"/>';
					
				   }
				 else if($i_status==2)
				   {
						$action_txt =
							 '<input name="" title="DISABLE" type="button" class="btn-06" onclick="javascript:changeStatus(\''.$ID.'\',\'1\',\''.$i_status.'\')"  value="ENABLE"/>';
					
				   } 
			}
			else{
			    
				$SUCCESS_MSG = "An error has occured! please try again. ";
				echo json_encode(array('result'=>false,
                					   'u_id'=>$ID,
									   'action_txt' =>$action_txt,
									   'i_status' => $cur_status,
                					   'msg'=>$SUCCESS_MSG , 'redirect'=>true)); exit;
			}
			
			
			$SUCCESS_MSG = "Status changed successfully! ";
	    
			# view part...
			    ob_start();
                $content = '';
                ob_end_clean();
                
                echo json_encode(array('result'=>'success',
                					   'u_id'=>$ID,
									   'action_txt' =>$action_txt,
									   'i_status' => $cur_status,
                					   'msg'=>$SUCCESS_MSG ,'redirect'=>false));
	 }
     
	 
	public function ring_details($id) 
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
									     'js/backend/manage_rings.js') );
                                        
             parent::_add_css_arr( array('css/dd.css',
                                        ) );
            # adjusting header & footer sections [End]...
			$data['top_menu_selected'] = 3;
			$data['submenu'] = 5;
         
            
			// fetching data
			$WHERE_COND = " WHERE 1 ";
			$this->session->set_userdata('search_condition',$WHERE_COND);
			$page=0;
			$order_by = "`id` ASC ";
			
			ob_start();
            $this->ring_post_ajax_pagination($id);
            $data['result_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
			
			$this->session->set_userdata('ring_id',$id);
			## fetching ring details #
			$where = " WHERE R.id  = {$id} "; 
			$data['ring_detail_arr'] = $this->my_ring_model->get_list($where);
			
			$where  = " AND i_invited =  1";
			$data['ring_user_arr'] = $this->my_ring_model->get_ring_users_by_id($id);
			
			#pr($data['ring_user_arr']);
            
           
                
            # rendering the view file...
            $VIEW_FILE = "admin/social_hub/rings/ring-details.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }
    
    
    public function ajax_show_all_members()
    {
        $ring_id = $this->input->post('ring_id');
        
        $where  = " AND i_invited =  1";
        $data['ring_user_arr'] = $this->my_ring_model->get_ring_users_by_id($ring_id);
        
        $view_file = $this->load->view("admin/social_hub/rings/ring_details_all_members_ajax.phtml",$data,true);
        
        echo json_encode(array('response'=>$view_file));
        
    }
    
    public function ajax_show_eight_members()
    {
        $ring_id = $this->input->post('ring_id');
        
        $where  = " AND i_invited =  1";
        $data['ring_user_arr'] = $this->my_ring_model->get_ring_users_by_id($ring_id);
        
        $view_file = $this->load->view("admin/social_hub/rings/ring_details_eight_members_ajax.phtml",$data,true);
        
        echo json_encode(array('response'=>$view_file));
        
    }
    
    
    

	    # function to load ajax-pagination [AJAX CALL]...
    public function ring_post_ajax_pagination($i_ring_id , $page=0)
    {
        try
        {
				
			$order_by = "R.`id` DESC ";
		   	$result = $this->ring_post_model->get_all_ring_post_by_ring_id($i_ring_id,$s_where, intval($page), $this->post_pagination_per_page);
			//echo $this->db->last_query();
			$total_rows = $this->ring_post_model->get_total_all_ring_post_by_ring_id($i_ring_id,$s_where);
			
			## end seacrh conditions : filter ############
			
			#pr($result);
			#Jquery Pagination Starts
           	$this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/social_hub/rings/ring_post_ajax_pagination/".$i_ring_id;
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->post_pagination_per_page;
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
          echo  $this->load->view('admin/social_hub/rings/ring_details_ajax.phtml', $data,TRUE);
		 }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
    }
	
	 ### function to zoom photo
   
	public function show_post_detail($id)
	{ 
		$data = $this->data;
		$data['post_info'] = $this->ring_post_model->get_by_id($id);
		//pr($data['post_info']);
		$html = $this->load->view('admin/social_hub/rings/post_detail_popup.phtml', $data, true);
		
		echo json_encode(array('html'=>$html));
	}
	
	
	function edit_post_info($id)
    {
        $arr_messages=array();
        if($_POST)
        {
            $id= intval($this->input->post('i_edit_id'));
            
            if(trim($this->input->post('txt_edit_post'))=='')
            {
                $arr_messages['edit_post'] = "* Required Field.";
               
            }
			
			if(trim($this->input->post('txt_edit_title'))=='')
            {
                $arr_messages['edit_title'] = "* Required Field.";
               
            }
			
			
            if(count($arr_messages)==0)
            {
               
                $info=array();
                $info['s_post_title'] = get_formatted_string(trim($this->input->post('txt_edit_title'))); 
				$info['s_post_description'] = get_formatted_string(trim($this->input->post('txt_edit_post')));
				$info['dt_updated_on'] = get_db_datetime(); 
               
                $this->ring_post_model->update($info,$id);
                $msg = "Post updated successfully.";
                $res = 'success';
				
				$i_ring_id = $this->session->userdata('ring_id');
				ob_start();
            	$this->ring_post_ajax_pagination($i_ring_id);
            	$html  = ob_get_contents(); //pr($data['result_content']);
            	ob_end_clean();
              
                echo json_encode(array('success'=>'true',
                                         'msg'=>$msg ,
										 'html'=> $html
                                            )
                                    );
            }
            else
            {
                 echo json_encode(array('success'=>'false',
				 						'arr_messages'=>$arr_messages,
                                        'msg'=>'error' )
                                    );
            }
        }
        else
        {
            $post_info = $this->ring_post_model->get_by_id($id);
			
            echo json_encode(array('post_info_desc'=>get_unformatted_string_edit(br2nl($post_info['s_post_description'])),'post_info_title'=>$post_info['s_post_title'])); 
        }
        
        
    }
    
    
	function delete_ring_post()
    {
        $id=$this->input->post('id');
        $i_ret=$this->ring_post_model->delete_by_id($id);
        $result='success';
		
		$i_ring_id = $this->session->userdata('ring_id');
		ob_start();
		$this->ring_post_ajax_pagination($i_ring_id);
		$html  = ob_get_contents(); //pr($data['result_content']);
		ob_end_clean();
		
		
        echo json_encode(array('result'=>$result, 'html'=> $html));
    }
    
    
    function delete_member()
    {
        $table_id = $this->input->post('table_id');
        $this->my_ring_model->delete_ring_member_by_id($table_id);
        /*$sql = "update {$this->db->RING_INV_USER} set i_joined=0 where id={$table_id}";
        $this->db->query($sql);
        */
        
        echo json_encode(array("response"=>true, "msg"=>"The member successfully removed."));
    }
	
    public function all_comments_ajax_pagination($page=0)
     {
         try
         {
             $article_id = $this->input->post('id');
             $where = "WHERE A.i_ring_post_id=".$article_id;
             //$result = $this->my_blog_post_model->show_all_comments($where,$page,$this->comments_pagination_per_page);
             $result = $this->ring_post_comments_model->get_by_ring_post_id($article_id);
             //pr($res);
        // $total_rows=$this->ring_post_comments_model->get_by_ring_post_id($article_id);
            #Jquery Pagination Starts
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/social_hub/rings/all_comments_ajax_pagination";
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

            $config['div'] = '#comment_div'; /* Here #content is the CSS selector for target DIV */
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
            $content = $this->load->view('admin/social_hub/rings/ring_comments_ajax.phtml', $data,TRUE);
            
            echo json_encode(array('html'=>$content));
         }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
         
     }
	
    
}   // end of controller...