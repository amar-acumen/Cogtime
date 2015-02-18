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
* @link model/my_blog_model.php
* @link views/##
*/

class Blog_detail extends Admin_base_Controller
{
   
	private $pagination_per_page=10;
    private $comments_pagination_per_page = 2;

    

    // constructor definition...
    function __construct()
    {
        try
        {
            parent::__construct();
            parent::_check_admin_login();
            
            # loading reqired model & helpers...
            // $this->load->helper('###');
            $this->load->model("my_blog_model");
            $this->load->model('my_blog_post_model');
           $this->load->helper('common_option_helper.php');
           
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }

    // "index" function definition...
    public function index($i_blog_id='') 
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
									     'js/backend/manage_blogs.js') );
                                        
             parent::_add_css_arr( array('css/dd.css',
                                        ) );
            # adjusting header & footer sections [End]...
			$data['top_menu_selected'] = 3;
			$data['submenu'] = 3;
         
            
			// fetching data
			$WHERE_COND = " WHERE 1 ";
			$this->session->set_userdata('search_condition',$WHERE_COND);
			$page=0;
			$order_by = "`id` DESC ";
            
            $this->session->set_userdata('blog_id',$i_blog_id);
            
            
            $wh    = " WHERE c.id='".$i_blog_id."'  AND c.i_user_id=u.id ";
            $data['blogdata']    = $this->my_blog_model->fetch_multi($wh,'','','');
			
			ob_start();
            $this->ajax_pagination();
            $data['result_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
			
                
            # rendering the view file...
            $VIEW_FILE = "admin/social_hub/blogs/blog-detail.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }


	 public function edit_info($id='')
	 {
		  try
		  {
			$arr_messages = array();
			if($_POST){
					$id= intval($this->input->post('i_edit_id'));
				  # error message trapping...
					  
				  if(trim($this->input->post('txt_title'))=='') 
				  {		
						  $arr_messages['title'] = "* Required Field";
				  }	
				  if(trim($this->input->post('limitedtextarea'))=='') 
                  {        
                          $arr_messages['limitedtextarea'] = "* Required Field";
                  }    
									
				  if( count($arr_messages)==0 ) {
					  
						 $info = array();
						 $info['s_post_title'] = get_formatted_string($this->input->post('txt_title'));	
                         $info['s_post_description'] = get_formatted_string($this->input->post('limitedtextarea'));    
						 $info['dt_updated_date'] = get_db_datetime();
						
						 $_ret = $this->my_blog_post_model->edit_info($info, $id);
						 
						  ob_start();
						  $this->ajax_pagination();
						  $html = ob_get_contents(); //pr($data['result_content']);
						  ob_end_clean();
			
							  
					   echo json_encode( array('success'=>true,'arr_messages'=>$arr_messages,'msg'=>'The Blog has been updated successfully.', 'html'=>$html) );
				  }
				  else
				  {
					  echo json_encode( array('success'=>false,'arr_messages'=>$arr_messages,'msg'=>'Error!') );
				  }
		
			
			  }else
			  {
				  
					$data['posted'] = $this->my_blog_post_model->get_by_id($id) ;
					echo json_encode( array('result'=>success,'s_title'=>$data['posted']['s_post_title'],'s_description'=>$data['posted']['s_post_description']) );	  

			  }
			  
		  }
		  catch(Exception $err_obj)
		  {
			  show_error($err_obj->getMessage());
		  }
    }

    //// function to Delete Banner
    public function delete_information ()
    {
        $id = $this->input->post('id');
		$i_ret=$this->my_blog_post_model->delete_by_id($id);
		$re_page = admin_base_url() ."blogs.html";
					//header("location:".$re_page);
					exit;
		
	} // end of Delete banner function...
	
	
	  # function to load ajax-pagination [AJAX CALL]...
    public function ajax_pagination($page=0)
    {
        try
        {
			## seacrh conditions : filter ############
		 	$WHERE_COND = '';
			 if(isset($_POST['search_basic']) && $_POST['search_basic'] == 'Y' ) :
				
				$WHERE_COND = " WHERE 1 ";
				
				$txt_blog_title = get_formatted_string(trim($this->input->post('txt_article_title')));
                //$WHERE_COND .= ($txt_blog_title=='')?'':" AND ( B.s_title LIKE '%".$txt_blog_title."%')";
				$WHERE_COND .= ($txt_blog_title=='')?'':" AND ( p.s_post_title LIKE '%".$txt_blog_title."%')";
			    
				$txt_username = get_formatted_string(trim($this->input->post('txt_username')));
				$WHERE_COND .= ($txt_username=='')?'':" AND (  CONCAT(U.s_first_name,' ',U.s_last_name) LIKE  '%".$txt_username."%')";
															
															
				$this->session->set_userdata('search_condition',$WHERE_COND);
			
			
			 endif;  
		   	$i_blog_id = $this->session->userdata('blog_id');
			$s_where = $this->session->userdata('search_condition')." AND p.i_blog_id={$i_blog_id}";
			$order_by = "`id` DESC ";
		   	//$result = $this->my_blog_model->get_list($s_where,$page,$this->pagination_per_page,$order_by);
            $result = $this->my_blog_post_model->fetch_multi($s_where, $page, $this->pagination_per_page,'');
            $resultCount = count($result);
	
			$total_rows = $this->my_blog_post_model->gettotal_info_admin($s_where);
			
			if( ( !is_array($result) || !count($result) ) && $total_rows ) {
                $page = $page - $this->pagination_per_page;
                
                $result = $this->my_blog_model->get_list($s_where, $page, $this->pagination_per_page,$ORDER_BY);
            }
			## end seacrh conditions : filter ############
			
			//pr($result,1);
			#Jquery Pagination Starts
           	$this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/social_hub/blog_detail/ajax_pagination";
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
          echo  $this->load->view('admin/social_hub/blogs/list_articles_ajax.phtml', $data,TRUE);
		 }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
		
    }
	
	
	 public function change_status()
	 {
			
			$data = $this->data;
			//$page  =  intval($this->input->post('cur_page'));
			$i_status = intval($this->input->post('i_status'));
			//$cur_status = intval($this->input->post('cur_status'));
			$ID = $this->input->post('id');

			$now_status = 3-$i_status;

			
			
			if($this->session->userdata('user_id') !="")
			{
				$this->my_blog_post_model->change_status($now_status,$ID);
				if($now_status==1)
				   {
					 
						$action_txt =
							 '<input name="" title="ENABLE" type="button" class="btn-06" onclick="return change_status(\''.$now_status.'\',\''.$ID.'\')"  value="DISABLE"/>';
					
				   }
				 else if($now_status==2)
				   {
						$action_txt =
							 '<input name="" title="DISABLE" type="button" class="btn-06" onclick="return change_status(\''.$now_status.'\',\''.$ID.'\')"  value="ENABLE"/>';
					
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
	    

                
                echo json_encode(array('result'=>true,
                					   'u_id'=>$ID,
									   'action_txt' =>$action_txt,
									   'i_status' => $cur_status,
                					   'msg'=>$SUCCESS_MSG ,'redirect'=>false));
	 }
     
     
     public function article_detail()
     {
         $article_id = $this->input->post('id');
         $user_id = $this->input->post('user_id');
         $res = $this->my_blog_post_model->get_detail_by_id($article_id);
         //pr($res);
         if(count($res)!=0)
         {
             $success = true;
             $title = $res['s_post_title'];
             $desc = nl2br($res['s_post_description']);
             $created_dt = getShortDate($res['dt_created_date'],5);
             $img = get_profile_image($user_id,'thumb');
             $user_name = $res['user_name'];
             
             
             echo json_encode(array('success'=>$success,
                                    'title'=>$title,
                                    'desc'=>$desc,
                                    'created_dt'=>$created_dt,
                                    'img'=>$img,
                                    'user_name'=>$user_name));
         }
         else
         {
             $success = false;
             echo json_encode(array('success'=>$success));
         }
     }
     
     
     
     
     public function all_comments_ajax_pagination($page=0)
     {
         try
         {
             $article_id = $this->input->post('id');
             
             $where = "WHERE c.i_blog_post_id=".$article_id;
             //$result = $this->my_blog_post_model->show_all_comments($where,$page,$this->comments_pagination_per_page);
             $result = $this->my_blog_post_model->show_all_comments($where);
             //pr($res);
         
            #Jquery Pagination Starts
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/social_hub/blog_detail/all_comments_ajax_pagination";
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
            $content = $this->load->view('admin/social_hub/blogs/article_comments_ajax.phtml', $data,TRUE);
            
            echo json_encode(array('html'=>$content));
         }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
         
     }
	
	
    
}   // end of controller...