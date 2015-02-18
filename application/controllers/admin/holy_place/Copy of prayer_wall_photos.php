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

class Prayer_wall_photos extends Admin_base_Controller
{
    private $prayer_wall_pagination_per_page= 2;
    private $upload_path ;
   
    

    // constructor definition...
    function __construct()
    {
        try
        {
            parent::__construct();
            parent::_check_admin_login();
            
            $this->upload_path = BASEPATH.'../uploads/prayer_wall_photos/';
            
            # configuring paths...
                        
            # loading reqired model & helpers...
            // $this->load->helper('###');
           //$this->load->model("ring_categories_model");
            $this->load->model("prayer_wall_photos_model");
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
            $data['submenu'] = 8;
         
            
            // fetching data
            $WHERE_COND =  " WHERE 1 ";
            $data['search_keyword'] = '';
            
            /*if($this->session->userdata('search_prayer_wall_photos')!='')
            {
                $WHERE_COND = $this->session->userdata('search_prayer_wall_photos');
                $data['search_keyword'] = $this->session->userdata('search_keyword');
            }
            else
            {
                $WHERE_COND = " WHERE 1 ";
            }
            */
            $this->session->set_userdata('search_prayer_wall_photos',$WHERE_COND);
            if($this->session->userdata('current_page_prayer_wall_photos')!='')
                $page=$this->session->userdata('current_page_prayer_wall_photos');
            if($page!=0)
                $this->session->set_userdata('current_page_prayer_wall_photos',$page);
                
            $order_by = "`id` ASC ";
            
            
            ob_start();
            $this->prayer_wall_photos_ajax_pagination($page);
            $data['result_content'] = ob_get_contents(); //pr($data['result_content']);
            ob_end_clean();
            
            $this->session->set_userdata('current_page_prayer_wall_photos','');
            $this->session->set_userdata('search_prayer_wall_photos','');
                
            # rendering the view file...
            $VIEW_FILE = "admin/holy_place/prayer_wall_photos/prayer_wall_photos.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }


    # function to load ajax-pagination [AJAX CALL]...
    public function prayer_wall_photos_ajax_pagination($page=0)
    {
        try
        {
            ## seacrh conditions : filter ############
         $WHERE_COND = '';
         
         if($this->input->post('if_post')=='y')
         {
             $this->session->set_userdata('search_prayer_wall_photos','');
             //echo "post";
             if($this->input->post('txt_title')!='')
             {
                 $title = get_formatted_string($this->input->post('txt_title'));
                 $s_where=" WHERE MATCH (p.s_title) AGAINST('{$title}' IN BOOLEAN MODE) ";
                 $this->session->set_userdata('search_prayer_wall_photos',$s_where);
                 $this->session->set_userdata('search_keyword',$title);
             }
             
         }
           

           
            
            $s_where = $this->session->userdata('search_prayer_wall_photos');
            
            $order_by = "";
               $result = $this->prayer_wall_photos_model->get_all_prayer_wall_photos($s_where,$page,$this->prayer_wall_pagination_per_page,$order_by);
            $resultCount = count($result);
            
            $total_rows = $this->prayer_wall_photos_model->get_count_all_prayer_wall_photos($s_where);
            
            if(!count($result) && $total_rows)
            {
                
                $page=$page-$this->prayer_wall_pagination_per_page;
                $result = $this->prayer_wall_photos_model->get_all_prayer_wall_photos($s_where,$page,$this->prayer_wall_pagination_per_page,$order_by);
                
            }
            //echo $total_rows;exit;
            //echo $this->db->last_query(); exit;
            //echo "total : ".$total_rows;exit;
            ## end seacrh conditions : filter ############
            
            //pr($result,1);
            #Jquery Pagination Starts
            $this->load->library('jquery_pagination');
            $config['base_url'] = base_url()."admin/holy_place/prayer_wall_photos/prayer_wall_photos_ajax_pagination";
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $this->prayer_wall_pagination_per_page;
            $config['uri_segment'] = ($this->session->userdata('current_page_prayer_wall_photos')!='')? 3:5;
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
          echo  $this->load->view('admin/holy_place/prayer_wall_photos/prayer_wall_photos_ajax.phtml', $data,TRUE);
         }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 
        
    }
    
    
    
    function delete_prayer_wall_photos($current_page)
    {
        $id=$this->input->post('id');
        //$current_page = $this->input->post('current_page');
        
        /*if($this->session->userdata('current_page_intercession'))
            echo "set session";
        else
            echo "not set";
        */
        
        //$this->session->set_userdata('current_page_intercession',$current_page);
        
        $info = $this->prayer_wall_photos_model->get_info_by_prayer_wall_photos_id($id);
        $photo = getThumbName($info['s_photo_name'],'main');
        
        $prev_photo_path = $this->upload_path.$photo;
        @unlink($prev_photo_path);
        
        
        
        $this->prayer_wall_photos_model->delete_by_id($id);
        
        ob_start();
        $this->prayer_wall_photos_ajax_pagination($current_page);
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
        $this->prayer_wall_photos_model->change_status_prayer_wall_photos($data,$id);
        
        echo json_encode(array('status'=>$data['i_is_enable']));
        
    }
        
    
    
    
    
    //================================= edit info ====================================
    function edit_prayer_wall_photos($current_page,$id)
    {
        try
        {
            
            //setting current page in session
            $this->session->set_userdata('current_page_prayer_wall_photos',$current_page);
            
            
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
            
            $data['posted']= $this->prayer_wall_photos_model->get_info_by_prayer_wall_photos_id($id);
            
          
            //pr($data['posted'],1);
            
            

           if($_POST)
            {
                $posted=array();
                $posted["s_title"]= get_formatted_string(trim($this->input->post("txt_title")));
                
                
                $posted["s_photo_name"]= $_FILES['file_photo']['name'];
                
                
                //pr($posted,1);
                    
                $this->form_validation->set_message('required', '* Required Field.');        
                $this->form_validation->set_rules('txt_title', "* required " ,'trim|required');

              
             
                
                
               
            
                
                
                
                
                if( isset($_FILES['file_photo']['name']) && $_FILES['file_photo']['name']!='') {
                    preg_match('/(^.*)\.([^\.]*)$/', $_FILES['file_photo']['name'], $matches);
                    $ext = "";
                    if(count($matches)>0) {
                        $ext = $matches[2];
                        $original_name = $matches[1];
                    }
                    else
                        $original_name = 'photo';

                
                    if ( !in_array($ext , $this->config->item('VALID_IMAGE_EXT'))) 
                    {
                         $data['error_file_photo'] ="supported extensions are ".implode(' , ',$this->config->item('VALID_IMAGE_EXT'));
                    }
                    else if($_FILES['file_photo']['size'] > $this->config->item('MAX_UP_FILE_SIZE')*1024*1024)
                     {
                        $data['error_file_photo'] = "Maximum file upload size is ".$this->config->item('MAX_UP_FILE_SIZE')." MB.";
                     }   
                          
                }
                else
                {
                    #$data['error_file_photo'] = "* Required Field.";
                    
                }
                
                
              
            
                //validation ends here
                if ($this->form_validation->run() == FALSE || $data['error_file_photo'] != '' )
                {
                  
                   ////////Display the add form with posted values within it////
                   
                   $arr = $this->prayer_wall_photos_model->get_info_by_prayer_wall_photos_id($id);
                    $posted['s_photo_name'] = $arr['s_photo_name'];
                    $data["posted"]=$posted;/*don't change*/
                    
                    
                    
                }
                else
                {
                
                //adding to database
                $info=array();
                $info["s_title"]= get_formatted_string(trim($posted["s_title"]));
                
                if( isset($_FILES['file_photo']['name']) && $_FILES['file_photo']['name']!='')
                {
                    $info["s_photo_name"] = $this->_upload_photo();
                }

                

                $info['dt_updated_on'] = get_db_datetime();
                
                //pr($info,1);
                
                //--------------- delete the previous one ----------------------------
                if( isset($_FILES['file_photo']['name']) && $_FILES['file_photo']['name']!='')
                {
                    $prev_photo = getThumbName($data['posted']['s_photo_name'],'main');
                    $prev_photo_path = $this->upload_path.$prev_photo;
                    @unlink($prev_photo_path);
                }
                
                
                $this->prayer_wall_photos_model->edit_prayer_wall_photos($info,$id); //echo $this->db->last_query();
                $re_page = admin_base_url() ."holy-place/".$current_page."/prayer-wall-photos.html";
                header("location:".$re_page);
                exit;
                    
                }
                
            }
          
            
           
            
                
            # rendering the view file...
            $VIEW_FILE = "admin/holy_place/prayer_wall_photos/add_edit_prayer_wall_photos.phtml";
            parent::_render($data, $VIEW_FILE);
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
        
    }
    
    
    
    
    //----------------------------------- add denomination ---------------------------------
    function add_prayer_wall_photos()
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
            $data['submenu'] = 8;
         
            $data['mode'] = 'add';
            
            
            if($_POST)
            {
                $posted=array();
                $posted["s_title"]= get_formatted_string(trim($this->input->post("txt_title")));
                
                
                $posted["s_photo_name"]= $_FILES['file_photo']['name'];
                
                
                //pr($posted,1);
                    
                $this->form_validation->set_message('required', '* Required Field.');        
                $this->form_validation->set_rules('txt_title', "* required " ,'trim|required');

              
             
                
                
               
            
                
                
                
                
                if( isset($_FILES['file_photo']['name']) && $_FILES['file_photo']['name']!='') {
                    preg_match('/(^.*)\.([^\.]*)$/', $_FILES['file_photo']['name'], $matches);
                    $ext = "";
                    if(count($matches)>0) {
                        $ext = $matches[2];
                        $original_name = $matches[1];
                    }
                    else
                        $original_name = 'photo';

                
                    if ( !in_array($ext , $this->config->item('VALID_IMAGE_EXT'))) 
                    {
                         $data['error_file_photo'] ="supported extensions are ".implode(' , ',$this->config->item('VALID_IMAGE_EXT'));
                    }
                    else if($_FILES['file_photo']['size'] > $this->config->item('MAX_UP_FILE_SIZE')*1024*1024)
                     {
                        $data['error_file_photo'] = "Maximum file upload size is ".$this->config->item('MAX_UP_FILE_SIZE')." MB.";
                     }   
                          
                }
                else
                {
                    $data['error_file_photo'] = "* Required Field.";
                }
                
                
              
            
                //validation ends here
                if ($this->form_validation->run() == FALSE || $data['error_file_photo'] != '' )
                {
                  
                   ////////Display the add form with posted values within it////
                    $data["posted"]=$posted;/*don't change*/
                    
                    
                    
                }
                else
                {
                
                //adding to database
                $info=array();
                $info["s_title"]= get_formatted_string(trim($posted["s_title"]));
                $info["s_photo_name"] = $this->_upload_photo();

                

                $info['dt_uploaded_on'] = get_db_datetime();
                
                //pr($info,1);
                $i_newid = $this->prayer_wall_photos_model->insert_prayer_wall_photos($info); //echo $this->db->last_query();
                $re_page = admin_base_url() ."holy-place/0/prayer-wall-photos.html";
                header("location:".$re_page);
                exit;
                    
                }
                
            }
           
          
            # rendering the view file...
            $VIEW_FILE = "admin/holy_place/prayer_wall_photos/add_edit_prayer_wall_photos.phtml";
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
    public function _upload_photo($prev_img = '')
     {
          
       #pr($_FILES);
        $fileElementName = 'file_photo';     
        if(!empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] != '') 
        {
                preg_match('/(^.*)\.([^\.]*)$/', $_FILES[$fileElementName]['name'], $matches);
                $ext = "";
                if(count($matches)>0) {
                    $ext = strtolower($matches[2]);
                    $original_name = $matches[1];
                }
                else
                    $original_name = 'image';

            
                    $imagename = createImageName( $original_name );

                    if(test_file($this->upload_path.$imagename.'-thumb.'.$ext)) {
                        for( $i=0; test_file($this->upload_path.$imagename.'-'.$i.'-thumb.'.$ext); $i++ ) {
                        }

                        $new_imagename = $imagename.'-'.$i;
                    }
                    else {
                        $new_imagename = $imagename;
                    }

                    $this->imagename = $new_imagename;

                    $this->upload_image = $this->upload_path.$new_imagename.'.'.$ext;


                    @move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $this->upload_image);
                                       
                    
                    # @@@@@@@@@@@@ NEW RESIZING PART [BEGIN] @@@@@@@@@@@
                        
                        
                        $config = array();
                        $config['source_image'] = $this->upload_image;
                        $config['thumb_marker'] = '-mid';
                        $config['crop'] = false;
                        $config['width'] = 286;
                        $config['height'] = 142;
                        $config1['crop_from'] = 'middle';
                        $config['within_rectangle'] = true;
                        $config['small_image_resize'] = 'no_resize';
                        resize_exact($config);
                        unset($config);
                        
                        $config = array();
                        $config['source_image'] = $this->upload_image;
                        $config['thumb_marker'] = '-thumb';
                        $config['crop'] = false;
                        $config['width'] = 60;
                        $config['height'] = 60;
                        $config1['crop_from'] = 'middle';
                        $config['within_rectangle'] = true;
                        $config['small_image_resize'] = 'no_resize';
                        resize_exact($config);
                        unset($config);
                        
                       
                        
                       # @@@@@@@@@@@@ NEW RESIZING PART [END] @@@@@@@@@@@
                    
                    $this->s_picture_path = $new_imagename.'.'.$ext;
                    
                    @unlink($this->upload_image); //Unlink the original image........
                    //@unlink($this->upload_path.getThumbName($prev_img,'thumb')); //Unlink the prevoius image........
                    
                    return $this->s_picture_path;
                    
                }
        else
        {
            return $prev_img; // Unchanged previous image
        }
        
        
    }
    
     
    
}   // end of controller...