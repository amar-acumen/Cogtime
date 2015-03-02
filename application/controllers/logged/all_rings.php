<?php
/*********
* Author: 
* Date  : 
* Modified By: 
* Modified Date:
* 
* Purpose:
* Controller For WALL
* 
* 
*/
include(APPPATH.'controllers/base_controller.php');


class All_rings extends Base_controller
{
    
    private $pagination_per_page =  10 ;
  
    
    
    
    public function __construct()
     {
         
        try
        {
            parent::__construct();
               parent::check_login(TRUE, '', array('1')); // put this code on those pages which are not accessable by guest user
            # loading reqired model & helpers...
            $this->upload_path = BASEPATH.'../uploads/user_ring_logos/';
            $this->load->model('users_model');
            $this->load->model('contacts_model');
            $this->load->model('netpals_model');
            $this->load->model('my_ring_model');
            
            $this->i_profile_id = intval(decrypt($this->session->userdata('user_id')));
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }  

    }
    
    
    public function index()
    {
        $posted=array();
        $this->data["posted"]=$posted;/*don't change*/    
        $data = $this->data;      
        $this->data["MAIN_MENU_SELECTED"] = 1;
        parent::_set_title('::: COGTIME Xtian network :::');
        parent::_set_meta_desc('');
        parent::_set_meta_keywords('');
        
            
        parent::_add_js_arr( array( /*'js/ddsmoothmenu.js',
                                    'js/switch.js',
                                    'js/animate-collapse.js',
                                    'js/lightbox.js',
                                    'js/jquery.dd.js',
                                    'js/jquery-ui-1.8.2.custom.min.js',*/
									'js/frontend/logged/tweets/tweet_utilities.js',
                                    //'js/stepcarousel.js'
                                    ));
                                    
//        parent::_add_css_arr( array('css/jquery-ui-1.8.2.custom.css',
//                                          'css/dd.css') );
                                          
        $this->session->set_userdata('where','');
        
        
        $data['all_category']    = $this->my_ring_model->get_all_category();  
        
        //pr($data['all_category']);
        # view file...
        ob_start();
        $content = $this->generate_all_ring_listing_AJAX();
        $content = ob_get_contents();
        $content_obj = json_decode($content);
        $data['listingContent'] = $content_obj->html; 
        $data['no_of_result'] = $content_obj->no_of_result;
        $data['view_more'] = $content_obj->view_more;
        ob_end_clean();                                  
        $VIEW = "logged/ring/all_rings.phtml"; 
        parent::_render($data, $VIEW);
    }
    
    
    public function generate_all_ring_listing_AJAX($page=0)
    {
        $wh    = "";
       
        
        $data['ringdata']    = $this->my_ring_model->show_all_public_ring($wh,$page,$this->pagination_per_page,'');
        
		$data['ringdata']	= check_friend_netpal_status($data['ringdata']);
        $data['pagination_per_page'] = $this->pagination_per_page;
        //pr($result);
        $data['arr_join_req']    = $this->my_ring_model->get_join_req_arr();
        
        
        $resultCount = count($data['ringdata']);
        $total_rows = $this->my_ring_model->gettotal_of_all_public_ring($wh);
        $cur_page = $page + $this->pagination_per_page;
        
        
        // getting auction-category listing...
        $data['no_of_ring'] = $total_rows;
        $data['no_of_result'] = $total_rows;
        $data['current_page_1'] = $cur_page;

         //--- for check end of he page.
           $view_more = true;
           $rest_counter = $total_rows-$page;
           if($rest_counter<=$this->pagination_per_page)
              $view_more = false;
         //--------- end check
        # loading the view-part...
        $AJAX_VIEW_FILE = 'logged/ring/ajax_ring/all_rings_ajax.phtml';
        
        
        if( $total_rows>0 ) {
            $listingContent = $this->load->view($AJAX_VIEW_FILE, $data, true);
        }
        else {
            $listingContent = '';
        }
        echo json_encode( array('html'=>$listingContent, 
                                'current_page'=>$cur_page, 
                                'no_of_result'=>$data['no_of_result'],
                                'total'=>$total_rows,
                                'view_more'=>$view_more ,
                                'cur_page'=>$data['current_page_1']) );
                                
    }   
    
    
    
    
    
}