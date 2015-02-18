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

class Media_center_landing_videos extends Admin_base_Controller
{
   
   private $video_pagination_per_page =  20 ;
    
   
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
           
           $this->logged_admin_id = $this->session->userdata('loggedin');
            
            
           $this->load->model("landing_page_cms_model");
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
                                        'js/jquery/JSON/json2.js'
                                        ) );
                                        
             parent::_add_css_arr( array('css/dd.css'
                                        ) );
            # adjusting header & footer sections [End]...
            $data['top_menu_selected'] = 5;
            $data['submenu'] = 3;
            
            
            $current_page = ($this->session->userdata('current_page')!='')? $this->session->userdata('current_page'):$page;

            
            ### ajax call ###
            ob_start();
            $this->video_listing_AJAX($current_page);
            $data['content'] = ob_get_contents();
            ob_end_clean();
            ### end ajax call ###
            

            $this->session->set_userdata('current_page','');
            
            # rendering the view file...
            $VIEW_FILE = "admin/media_center/media_center_landing_videos.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }//end of index
    
    
    public function video_listing_AJAX($page=0) 
    {
         try
         {
            $data = $this->data; 
            $where    = " WHERE 1" ;
            $this->session->set_userdata('where','');
			$hd=$this->input->post('hd_val');
             if($_POST && $hd==1)
            {
                $title = $this->input->post('title');
                $posted_by = $this->input->post('uploaded_by');
               // $posted_on = $this->input->post('create_time');
                if($title!='')
                {
                    $where.= " AND v.s_title LIKE '{$title}%'";
                    //$where = "WHERE MATCH (v.s_title) AGAINST ('{$title}')";
                }
                if($posted_by!='')
                {
                    $where.= " AND concat(a.s_name,' ',a.s_last_name) LIKE '{$posted_by}%'";
                }
				if($this->input->post('create_time') != ''){
					 $dt_start_date = get_db_dateformat($this->input->post('create_time'));
					$where .= ($dt_start_date=='')?'':" AND (DATE(v.dt_posted_on) ='".$dt_start_date."' )";
				}
				
				if($this->input->post('category')!='-1')
				{
					$category=intval(decrypt($this->input->post('category')));
					$where.= " AND v.i_category_id=".$category;
				}
				if($this->input->post('featured')!='-1')
				{
					$featured=$this->input->post('featured');
					$where.= " AND v.is_featured=".$featured;
				}
				
                $this->session->set_userdata('where',$where);
            }
           // echo "where : ".$where;
            $s_where = $this->session->userdata('where');
            
            $result = $this->landing_page_cms_model->get_all_videos($s_where ,$page,
                                                                $this->video_pagination_per_page);
            $resultCount = count($result);
            $total_rows = $this->landing_page_cms_model->get_total_videos($s_where);
    
            if(count($result)==0 && $total_rows)
            {
                $page = $page - $this->video_pagination_per_page;
                $result = $this->landing_page_cms_model->get_all_videos($s_where ,$page,
                                                                $this->video_pagination_per_page);
                
            }
    //pr($result);         
    //echo "total : ".$total_rows;exit;

    
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/media_center/media_center_landing_videos/video_listing_AJAX";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->video_pagination_per_page;
            $config['uri_segment'] = ($this->session->userdata('current_page')!='')? 2:5;
            $config['num_links'] = 9;
            $config['page_query_string'] = false;
            
            $config['prev_link'] = '&laquo; Previous';
            $config['next_link'] = 'Next &raquo;';

            $config['cur_tag_open'] = '<li> <span><a href="javascript:void(0)" class="select">';
            $config['cur_tag_close'] = '</a></span></li>';

            $config['next_tag_open'] = '<li><a href="javascript:void(0)">';
            $config['next_tag_close'] = '</a></li>';

            $config['prev_tag_open'] = '<li><a href="javascript:void(0)">';
            $config['prev_tag_close'] = '</a></li>';

            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';
            
            

            $config['div'] = '#table_contents'; /* Here #content is the CSS selector for target DIV */
            #$config['js_bind'] = "showBusyScreen(); "; /* if you want to bind extra js code */
            #$config['js_rebind'] = "hideBusyScreen(); "; /* if you want to rebind extra js code */


            $this->jquery_pagination->initialize($config);
            $data['page_links'] = $this->jquery_pagination->create_links();

            
            $data['result_arr'] = $result;
            $data['no_of_result'] = $total_rows;
            $data['current_page'] = $page;
            $data['total_pages'] = ceil($total_rows/$this->video_pagination_per_page);
          
             $p = ($page/$this->video_pagination_per_page);
             $data['current_loaded_page_no'] =  $p + 1;
            
            # rendering the view file...
            $VIEW_FILE = "admin/media_center/media_center_landing_videos_ajax.phtml";
            echo $this->load->view($VIEW_FILE, $data,true);
            //return $html;
            
        } 
        catch(Exception $err_obj)
        {
            
        } 
    
    }
    
    

    
    
    //--------------------------------------- delete video ----------------------------------------------
    function delete_video($current_page)
    {
        $id = $this->input->post('id');
        
        
        $prev_video_img = $this->landing_page_cms_model->get_video_thumb_by_id($id);
        $img_path = BASEPATH."../uploads/media_center_videos/".getThumbName($prev_video_img,'bigthumb');
        @unlink($img_path);
        
        $this->landing_page_cms_model->delete_video_by_id($id);
        
        ### ajax call ###
        ob_start();
        $this->video_listing_AJAX($current_page);
        $html = ob_get_contents();
        ob_end_clean();
        ### end ajax call ###
        
        
        echo json_encode(array('success'=>true,
                                'msg'=>'Video deleted successfully.',
                                'html'=>$html
                                ));
    }
    //--------------------------------------- end of delete word ----------------------------------------------
    
    
}// end of controller